<?php
/** Admin authentication: login, lockout, session, activity log. */

declare(strict_types=1);

function admin_user(): ?array
{
    static $user = null;
    if ($user !== null) {
        return $user ?: null;
    }
    $id = (int) ($_SESSION['admin_user_id'] ?? 0);
    if ($id <= 0) {
        $user = false;
        return null;
    }
    $row = fetch_one('SELECT * FROM admin_users WHERE id = ? AND is_active = 1', [$id]);
    if (!$row) {
        unset($_SESSION['admin_user_id']);
        $user = false;
        return null;
    }
    $user = $row;
    return $row;
}

function admin_require_login(): void
{
    if (!admin_user()) {
        $_SESSION['admin_redirect'] = $_SERVER['REQUEST_URI'] ?? '/admin';
        redirect('admin/login');
    }
}

function admin_is(string $role): bool
{
    $u = admin_user();
    return $u && $u['role'] === $role;
}

function admin_can_manage_users(): bool
{
    return admin_is('super_admin');
}

/**
 * Attempt a login.
 * @return array{ok:bool,error?:string}
 */
function admin_attempt_login(string $username, string $password): array
{
    $user = fetch_one('SELECT * FROM admin_users WHERE (username = ? OR email = ?) LIMIT 1', [$username, $username]);

    if (!$user) {
        usleep(400000);
        return ['ok' => false, 'error' => 'Incorrect username or password.'];
    }

    if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
        $mins = max(1, (int) ceil((strtotime($user['locked_until']) - time()) / 60));
        return ['ok' => false, 'error' => 'This account is temporarily locked after too many failed attempts. Try again in ' . $mins . ' minute(s).'];
    }

    if ((int) $user['is_active'] !== 1) {
        return ['ok' => false, 'error' => 'This account has been disabled. Contact the site administrator.'];
    }

    if (!password_verify($password, $user['password_hash'])) {
        $attempts = (int) $user['login_attempts'] + 1;
        $lock     = $attempts >= 5 ? date('Y-m-d H:i:s', time() + 900) : null;
        q('UPDATE admin_users SET login_attempts = ?, locked_until = ? WHERE id = ?', [$attempts, $lock, $user['id']]);
        usleep(400000);
        return ['ok' => false, 'error' => $lock
            ? 'Too many failed attempts. This account is locked for 15 minutes.'
            : 'Incorrect username or password. ' . (5 - $attempts) . ' attempt(s) remaining.'];
    }

    /* Success */
    session_regenerate_id(true);
    $_SESSION['admin_user_id'] = (int) $user['id'];
    q(
        'UPDATE admin_users SET login_attempts = 0, locked_until = NULL, last_login_at = NOW(), last_login_ip = ? WHERE id = ?',
        [client_ip(), $user['id']]
    );
    admin_log('Signed in', 'admin_users', (int) $user['id']);

    return ['ok' => true];
}

function admin_logout(): void
{
    if (admin_user()) {
        admin_log('Signed out');
    }
    unset($_SESSION['admin_user_id']);
    session_regenerate_id(true);
}

/** Write an entry to the activity log. */
function admin_log(string $action, string $entity = '', ?int $entityId = null): void
{
    $u = $_SESSION['admin_user_id'] ?? null;
    try {
        $name = null;
        if ($u) {
            $name = fetch_val('SELECT name FROM admin_users WHERE id = ?', [$u]);
        }
        db_insert('activity_log', [
            'user_id'    => $u,
            'user_name'  => $name,
            'action'     => $action,
            'entity'     => $entity,
            'entity_id'  => $entityId,
            'ip_address' => client_ip(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    } catch (Throwable $e) {
        // Never break a request because of logging.
    }
}
