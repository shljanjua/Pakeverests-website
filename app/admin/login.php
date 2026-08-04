<?php
/** Admin login screen. */
declare(strict_types=1);

$error = '';
if (is_post()) {
    if (!csrf_verify()) {
        $error = 'Your session expired. Please try again.';
    } elseif (rate_limited('admin_login', 10, 900)) {
        $error = 'Too many login attempts from this device. Please wait a few minutes.';
    } else {
        $res = admin_attempt_login(post('username'), (string) ($_POST['password'] ?? ''));
        if ($res['ok']) {
            $to = $_SESSION['admin_redirect'] ?? 'admin';
            unset($_SESSION['admin_redirect']);
            redirect($to);
        }
        $error = $res['error'] ?? 'Login failed.';
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Admin Sign In — Pak-Everests</title>
<link rel="icon" href="<?= e(media_url((string) setting('favicon_path'), 'logo')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body class="admin-body">
<div class="login-wrap">
  <div class="login-card">
    <img src="<?= e(media_url((string) setting('logo_path'), 'logo')) ?>" alt="Pak-Everests">
    <h1>Admin Panel</h1>
    <p class="sub">Sign in to manage orders, content and settings</p>

    <?php if ($error): ?>
      <div class="alert alert-error" role="alert"><?= e($error) ?></div>
    <?php endif; ?>
    <?= flash_render() ?>

    <form method="post" action="<?= e(admin_url('login')) ?>">
      <?= csrf_field() ?>
      <div class="form-group">
        <label for="username">Username or email</label>
        <input type="text" id="username" name="username" required autofocus autocomplete="username" value="<?= e(post('username')) ?>">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn btn-primary btn-block">Sign In</button>
    </form>

    <p class="login-foot">
      <a href="<?= e(url('/')) ?>">&larr; Back to website</a>
    </p>
    <p class="login-foot" style="font-size:.74rem;">
      Accounts lock for 15 minutes after 5 failed attempts.
    </p>
  </div>
</div>
</body>
</html>
