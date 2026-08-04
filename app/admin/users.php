<?php
/** Admin users: add, edit, change password, deactivate. */
declare(strict_types=1);

$me = admin_user();

if (!admin_can_manage_users()) {
    /* Editors and admins can still change their own password. */
    if (is_post() && post('action') === 'change_own_password') {
        $current = (string) ($_POST['current_password'] ?? '');
        $new     = (string) ($_POST['new_password'] ?? '');
        if (!password_verify($current, $me['password_hash'])) {
            flash('error', 'Your current password is incorrect.');
        } elseif (strlen($new) < 10) {
            flash('error', 'The new password must be at least 10 characters long.');
        } else {
            q('UPDATE admin_users SET password_hash = ? WHERE id = ?', [password_hash($new, PASSWORD_BCRYPT), (int) $me['id']]);
            admin_log('Changed own password', 'admin_users', (int) $me['id']);
            flash('success', 'Your password has been changed.');
        }
        redirect('admin/users');
    }

    admin_header('My Account', 'Change your own password');
    ?>
    <div class="a-card" style="max-width:520px;">
      <div class="a-card-head"><h2>Change your password</h2></div>
      <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="change_own_password">
        <div class="form-group">
          <label for="current_password">Current password</label>
          <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
        </div>
        <div class="form-group">
          <label for="new_password">New password</label>
          <input type="password" id="new_password" name="new_password" required minlength="10" autocomplete="new-password">
          <span class="form-hint">At least 10 characters. Use a passphrase you do not use anywhere else.</span>
        </div>
        <button class="btn btn-primary" type="submit">Change password</button>
      </form>
    </div>
    <?php
    admin_footer();
    return;
}

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'save':
            $username = strtolower(preg_replace('/[^a-z0-9._-]/i', '', post('username')));
            $email    = post('email');
            if ($username === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                flash('error', 'Please provide a valid username and email address.');
                redirect('admin/users');
            }
            if (fetch_val('SELECT id FROM admin_users WHERE (username = ? OR email = ?) AND id <> ?', [$username, $email, $id])) {
                flash('error', 'That username or email address is already in use.');
                redirect('admin/users');
            }

            $data = [
                'name'      => post('name'),
                'email'     => $email,
                'username'  => $username,
                'role'      => in_array(post('role'), ['super_admin', 'admin', 'editor'], true) ? post('role') : 'editor',
                'is_active' => post('is_active') ? 1 : 0,
            ];

            $password = (string) ($_POST['password'] ?? '');
            if ($password !== '') {
                if (strlen($password) < 10) {
                    flash('error', 'The password must be at least 10 characters long.');
                    redirect('admin/users');
                }
                $data['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
                $data['login_attempts'] = 0;
                $data['locked_until']   = null;
            }

            if ($id > 0) {
                if ($id === (int) $me['id'] && $data['is_active'] === 0) {
                    flash('error', 'You cannot deactivate your own account.');
                    redirect('admin/users');
                }
                db_update('admin_users', $data, $id);
                admin_log('Updated admin user ' . $username, 'admin_users', $id);
                flash('success', 'User updated.');
            } else {
                if ($password === '') {
                    flash('error', 'A password is required when creating a user.');
                    redirect('admin/users');
                }
                $data['created_at'] = date('Y-m-d H:i:s');
                $id = db_insert('admin_users', $data);
                admin_log('Created admin user ' . $username, 'admin_users', $id);
                flash('success', 'User created.');
            }
            redirect('admin/users');
            break;

        case 'unlock':
            q('UPDATE admin_users SET login_attempts = 0, locked_until = NULL WHERE id = ?', [$id]);
            flash('success', 'Account unlocked.');
            redirect('admin/users');
            break;

        case 'delete':
            if ($id === (int) $me['id']) {
                flash('error', 'You cannot delete your own account.');
            } elseif ((int) fetch_val('SELECT COUNT(*) FROM admin_users WHERE role = "super_admin" AND is_active = 1', [], 0) <= 1
                      && fetch_val('SELECT role FROM admin_users WHERE id = ?', [$id]) === 'super_admin') {
                flash('error', 'You cannot delete the last super administrator.');
            } else {
                db_delete('admin_users', $id);
                admin_log('Deleted admin user', 'admin_users', $id);
                flash('success', 'User deleted.');
            }
            redirect('admin/users');
            break;
    }
}

$editId = (int) get('edit', 0);
$user = $editId > 0 ? fetch_one('SELECT * FROM admin_users WHERE id = ?', [$editId]) : [
    'id' => 0, 'name' => '', 'email' => '', 'username' => '', 'role' => 'editor', 'is_active' => 1,
    'last_login_at' => null, 'locked_until' => null,
];
$users = fetch_all('SELECT * FROM admin_users ORDER BY id ASC');

admin_header('Admin Users', 'Who can sign in to this panel and what they can do');
?>

<?php
$defaultStillSet = fetch_one('SELECT * FROM admin_users WHERE username = "admin"');
if ($defaultStillSet && password_verify('PakEverests@2026', (string) $defaultStillSet['password_hash'])): ?>
<div class="alert alert-error">
  <strong>Security warning:</strong> the default admin password from the installation file is still in use.
  Change it now using the form on the right.
</div>
<?php endif; ?>

<div class="a-grid" style="grid-template-columns:minmax(0,1fr) minmax(300px,400px);gap:20px;align-items:start;">
  <div class="a-card">
    <div class="a-card-head"><h2>Users (<?= count($users) ?>)</h2></div>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Last sign in</th><th>Status</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr>
            <td><strong><?= e($u['name']) ?></strong><br><small style="color:var(--a-muted);"><?= e($u['email']) ?></small></td>
            <td><code><?= e($u['username']) ?></code></td>
            <td><small><?= e(ucwords(str_replace('_', ' ', $u['role']))) ?></small></td>
            <td><small>
              <?= $u['last_login_at'] ? pretty_date($u['last_login_at'], 'd M Y, H:i') : 'Never' ?>
              <?php if ($u['last_login_ip']): ?><br><span style="color:var(--a-muted);"><?= e($u['last_login_ip']) ?></span><?php endif; ?>
            </small></td>
            <td>
              <?php if ((int) $u['is_active'] !== 1): ?><span class="pill pill-muted">Disabled</span>
              <?php elseif ($u['locked_until'] && strtotime((string) $u['locked_until']) > time()): ?><span class="pill pill-bad">Locked</span>
              <?php else: ?><span class="pill pill-ok">Active</span><?php endif; ?>
            </td>
            <td class="actions">
              <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('users?edit=' . (int) $u['id'])) ?>">Edit</a>
              <?php if ($u['locked_until'] && strtotime((string) $u['locked_until']) > time()): ?>
              <form method="post" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="unlock">
                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                <button class="btn btn-ok btn-sm" type="submit">Unlock</button>
              </form>
              <?php endif; ?>
              <?php if ((int) $u['id'] !== (int) $me['id']): ?>
                <?= delete_button((int) $u['id'], 'Delete this admin user?') ?>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <h3 style="margin-top:22px;">What each role can do</h3>
    <dl class="detail-list">
      <div class="detail-row"><dt>Super admin</dt><dd>Everything, including managing other admin users.</dd></div>
      <div class="detail-row"><dt>Admin</dt><dd>Everything except managing admin users.</dd></div>
      <div class="detail-row"><dt>Editor</dt><dd>Everything except managing admin users. Intended for staff who handle content and orders.</dd></div>
    </dl>
  </div>

  <aside class="a-card">
    <div class="a-card-head"><h2><?= $editId ? 'Edit user' : 'Add a user' ?></h2></div>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
      <div class="form-group">
        <label for="name">Full name <span class="req">*</span></label>
        <input type="text" id="name" name="name" required value="<?= e((string) $user['name']) ?>">
      </div>
      <div class="form-group">
        <label for="username">Username <span class="req">*</span></label>
        <input type="text" id="username" name="username" required value="<?= e((string) $user['username']) ?>" autocomplete="off">
      </div>
      <div class="form-group">
        <label for="email">Email address <span class="req">*</span></label>
        <input type="email" id="email" name="email" required value="<?= e((string) $user['email']) ?>">
      </div>
      <div class="form-group">
        <label for="password"><?= $editId ? 'New password (leave blank to keep the current one)' : 'Password' ?></label>
        <input type="password" id="password" name="password" <?= $editId ? '' : 'required' ?> minlength="10" autocomplete="new-password">
        <span class="form-hint">At least 10 characters. A passphrase of three unrelated words works well.</span>
      </div>
      <div class="form-group">
        <label for="role">Role</label>
        <select id="role" name="role">
          <?php foreach (['super_admin' => 'Super admin', 'admin' => 'Admin', 'editor' => 'Editor'] as $k => $v): ?>
          <option value="<?= e($k) ?>" <?= $user['role'] === $k ? 'selected' : '' ?>><?= e($v) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <label class="check"><input type="checkbox" name="is_active" value="1" <?= (int) $user['is_active'] === 1 ? 'checked' : '' ?>> Account is active</label>
      <button class="btn btn-primary btn-block" type="submit"><?= $editId ? 'Save user' : 'Create user' ?></button>
      <?php if ($editId): ?><a class="btn btn-ghost btn-block" style="margin-top:8px;" href="<?= e(admin_url('users')) ?>">Cancel</a><?php endif; ?>
    </form>
  </aside>
</div>

<div class="a-card">
  <div class="a-card-head"><h2>Recent activity log</h2></div>
  <?php $log = fetch_all('SELECT * FROM activity_log ORDER BY id DESC LIMIT 40'); ?>
  <?php if (!$log): ?>
    <div class="empty">No activity recorded yet.</div>
  <?php else: ?>
  <div class="a-table-wrap">
    <table class="a-table">
      <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Item</th><th>IP</th></tr></thead>
      <tbody>
        <?php foreach ($log as $l): ?>
        <tr>
          <td><small><?= pretty_date($l['created_at'], 'd M, H:i') ?></small></td>
          <td><small><?= e((string) $l['user_name']) ?></small></td>
          <td><?= e($l['action']) ?></td>
          <td><small><?= e((string) $l['entity']) ?><?= $l['entity_id'] ? ' #' . (int) $l['entity_id'] : '' ?></small></td>
          <td><small><?= e((string) $l['ip_address']) ?></small></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
