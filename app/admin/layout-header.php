<?php
/** Admin layout: <head>, sidebar, topbar. */
declare(strict_types=1);

$u      = admin_user();
$badges = admin_badges();
$title  = $GLOBALS['adminPageTitle'] ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= e($_COOKIE['pe_admin_theme'] ?? 'light') ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($title) ?> — Pak-Everests Admin</title>
<link rel="icon" href="<?= e(media_url((string) setting('favicon_path'), 'logo')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
<script>
(function(){try{var t=localStorage.getItem('pe_admin_theme');if(t)document.documentElement.setAttribute('data-theme',t);}catch(e){}})();
</script>
</head>
<body class="admin-body">

<a class="skip-link" href="#adminMain">Skip to content</a>

<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-brand">
    <img src="<?= e(media_url((string) setting('logo_path'), 'logo')) ?>" alt="Pak-Everests">
    <span>Admin Panel</span>
  </div>

  <nav class="sidebar-nav" aria-label="Admin navigation">
    <p class="nav-section">Overview</p>
    <a class="nav-item<?= admin_active('dashboard') ?>" href="<?= e(admin_url('dashboard')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg> Dashboard
    </a>
    <a class="nav-item<?= admin_active('analytics') ?>" href="<?= e(admin_url('analytics')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 9.2h3V19H5zM10.6 5h2.8v14h-2.8zm5.6 8H19v6h-2.8z"/></svg> Analytics
    </a>

    <p class="nav-section">Enquiries</p>
    <a class="nav-item<?= admin_active('orders') ?>" href="<?= e(admin_url('orders')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm10 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7.2 14h9.5c.8 0 1.4-.5 1.6-1.2l2.6-8H6.2l-.5-2H2v2h2.4l3.6 9.4-1.3 2.4c-.5 1 .2 2.4 1.4 2.4H20v-2H8.5l.7-1.4z"/></svg>
      Orders <?php if ($badges['orders']): ?><em class="nav-badge"><?= $badges['orders'] ?></em><?php endif; ?>
    </a>
    <a class="nav-item<?= admin_active('messages') ?>" href="<?= e(admin_url('messages')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5z"/></svg>
      Messages <?php if ($badges['messages']): ?><em class="nav-badge"><?= $badges['messages'] ?></em><?php endif; ?>
    </a>
    <a class="nav-item<?= admin_active('distributors') ?>" href="<?= e(admin_url('distributors')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm-8 0a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0 2c-2.7 0-8 1.3-8 4v3h10v-3c0-1 .4-2.7 2.5-3.7C11.3 13.1 9.6 13 8 13zm8 0c-.3 0-.7 0-1.1.1 1.3 1 2.1 2.3 2.1 3.9v3h7v-3c0-2.7-5.3-4-8-4z"/></svg>
      Distributors <?php if ($badges['distributors']): ?><em class="nav-badge"><?= $badges['distributors'] ?></em><?php endif; ?>
    </a>
    <a class="nav-item<?= admin_active('labels') ?>" href="<?= e(admin_url('labels')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21.4 11.6 12.4 2.6A2 2 0 0 0 11 2H4a2 2 0 0 0-2 2v7c0 .5.2 1 .6 1.4l9 9a2 2 0 0 0 2.8 0l7-7a2 2 0 0 0 0-2.8zM6.5 8A1.5 1.5 0 1 1 6.5 5a1.5 1.5 0 0 1 0 3z"/></svg>
      Label Requests <?php if ($badges['labels']): ?><em class="nav-badge"><?= $badges['labels'] ?></em><?php endif; ?>
    </a>
    <a class="nav-item<?= admin_active('reviews') ?>" href="<?= e(admin_url('reviews')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/></svg>
      Reviews <?php if ($badges['reviews']): ?><em class="nav-badge"><?= $badges['reviews'] ?></em><?php endif; ?>
    </a>
    <a class="nav-item<?= admin_active('careers') ?>" href="<?= e(admin_url('careers')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6h-4V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2H4a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2zM10 4h4v2h-4V4z"/></svg>
      Careers <?php if ($badges['careers']): ?><em class="nav-badge"><?= $badges['careers'] ?></em><?php endif; ?>
    </a>
    <a class="nav-item<?= admin_active('subscribers') ?>" href="<?= e(admin_url('subscribers')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0 2c-4 0-9 2-9 5v3h18v-3c0-3-5-5-9-5z"/></svg> Subscribers
    </a>

    <p class="nav-section">Content</p>
    <a class="nav-item<?= admin_active('products') ?>" href="<?= e(admin_url('products')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2c-3.2 4.4-6 7.6-6 11a6 6 0 0 0 12 0c0-3.4-2.8-6.6-6-11z"/></svg> Products
    </a>
    <a class="nav-item<?= admin_active('blog') ?>" href="<?= e(admin_url('blog')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-2 12H7v-2h10v2zm0-4H7V9h10v2zm-3-4H7V5h7v2z"/></svg> Blog
    </a>
    <a class="nav-item<?= admin_active('pages') ?>" href="<?= e(admin_url('pages')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm0 7V3.5L19.5 9H14z"/></svg> Pages &amp; Legal
    </a>
    <a class="nav-item<?= admin_active('gallery') ?>" href="<?= e(admin_url('gallery')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2zM8.5 13.5l2.5 3 3.5-4.5 4.5 6H5l3.5-4.5z"/></svg> Photo Gallery
    </a>
    <a class="nav-item<?= admin_active('documents') ?>" href="<?= e(admin_url('documents')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1 3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg> Documents
    </a>
    <a class="nav-item<?= admin_active('media') ?>" href="<?= e(admin_url('media')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6h-8l-2-2H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2z"/></svg> Media Library
    </a>
    <a class="nav-item<?= admin_active('faqs') ?>" href="<?= e(admin_url('faqs')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 17h-2v-2h2v2zm2.1-7.7-.9.9c-.7.7-1.2 1.3-1.2 2.8h-2v-.5c0-1.1.5-2.1 1.2-2.8l1.2-1.3c.4-.3.6-.8.6-1.4a2 2 0 1 0-4 0H8a4 4 0 1 1 8 0c0 .9-.4 1.7-.9 2.3z"/></svg> FAQs
    </a>
    <a class="nav-item<?= admin_active('process') ?>" href="<?= e(admin_url('process')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h6v6H4zm10 0h6v6h-6zM4 14h6v6H4zm10 0h6v6h-6z"/></svg> 8 Stage Process
    </a>
    <a class="nav-item<?= admin_active('minerals') ?>" href="<?= e(admin_url('minerals')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3a9 9 0 1 0 9 9 9 9 0 0 0-9-9zm0 4a5 5 0 1 1-5 5 5 5 0 0 1 5-5z"/></svg> Minerals
    </a>
    <a class="nav-item<?= admin_active('coverage') ?>" href="<?= e(admin_url('coverage')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg> Delivery Areas
    </a>
    <a class="nav-item<?= admin_active('ticker') ?>" href="<?= e(admin_url('ticker')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 7h20v3H2zm0 5h14v3H2zm0 5h18v3H2z"/></svg> News Ticker
    </a>

    <p class="nav-section">Documents</p>
    <a class="nav-item<?= admin_active('agreements') ?>" href="<?= e(admin_url('agreements')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17 3H7a2 2 0 0 0-2 2v16l7-3 7 3V5a2 2 0 0 0-2-2z"/></svg> Agreements
    </a>
    <a class="nav-item<?= admin_active('quotations') ?>" href="<?= e(admin_url('quotations')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 2H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zm-9 5h7v2h-7V7zm0 4h7v2h-7v-2zm0 4h5v2h-5v-2z"/></svg> Quotations
    </a>

    <p class="nav-section">Configuration</p>
    <a class="nav-item<?= admin_active('whatsapp') ?>" href="<?= e(admin_url('whatsapp')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg> WhatsApp Numbers
    </a>
    <a class="nav-item<?= admin_active('ads') ?>" href="<?= e(admin_url('ads')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h18v4H3zm0 6h11v8H3zm13 0h5v8h-5z"/></svg> Ads &amp; Monetisation
    </a>
    <a class="nav-item<?= admin_active('settings') ?>" href="<?= e(admin_url('settings')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19.4 13a7.8 7.8 0 0 0 0-2l2-1.6-2-3.4-2.4 1a7.6 7.6 0 0 0-1.7-1L15 3H9l-.3 2.9c-.6.3-1.2.6-1.7 1l-2.4-1-2 3.4L4.6 11a7.8 7.8 0 0 0 0 2l-2 1.6 2 3.4 2.4-1c.5.4 1.1.8 1.7 1L9 21h6l.3-2.9c.6-.3 1.2-.6 1.7-1l2.4 1 2-3.4-2-1.7zM12 15.5a3.5 3.5 0 1 1 0-7 3.5 3.5 0 0 1 0 7z"/></svg> Settings
    </a>
    <?php if (admin_can_manage_users()): ?>
    <a class="nav-item<?= admin_active('users') ?>" href="<?= e(admin_url('users')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0 2c-4 0-9 2-9 5v3h18v-3c0-3-5-5-9-5z"/></svg> Admin Users
    </a>
    <?php endif; ?>
  </nav>

  <div class="sidebar-footer">
    <a class="nav-item" href="<?= e(url('/')) ?>" target="_blank" rel="noopener">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 19H5V5h7V3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7h-2v7zM14 3v2h3.6l-9.8 9.8 1.4 1.4L19 6.4V10h2V3h-7z"/></svg> View website
    </a>
    <a class="nav-item" href="<?= e(admin_url('logout')) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17 7l-1.4 1.4L18.2 11H8v2h10.2l-2.6 2.6L17 17l5-5-5-5zM4 5h8V3H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8v-2H4V5z"/></svg> Sign out
    </a>
  </div>
</aside>

<div class="admin-shell">
  <header class="admin-topbar">
    <button class="icon-btn menu-btn" id="sidebarToggle" type="button" aria-label="Toggle menu">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18v2H3zm0 5h18v2H3zm0 5h18v2H3z"/></svg>
    </button>

    <div class="topbar-title">
      <h1><?= e($title) ?></h1>
      <?php if (!empty($GLOBALS['adminPageSubtitle'])): ?>
        <p><?= e((string) $GLOBALS['adminPageSubtitle']) ?></p>
      <?php endif; ?>
    </div>

    <div class="topbar-actions">
      <?php foreach (($GLOBALS['adminPageActions'] ?? []) as $act): ?>
        <a class="btn <?= e($act['class'] ?? 'btn-primary') ?>" href="<?= e($act['href']) ?>"><?= e($act['label']) ?></a>
      <?php endforeach; ?>

      <button class="icon-btn" id="adminThemeToggle" type="button" aria-label="Toggle dark mode" title="Toggle dark mode">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
      </button>

      <div class="topbar-user">
        <span class="avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr((string) $u['name'], 0, 1))) ?></span>
        <span class="topbar-user-meta">
          <strong><?= e((string) $u['name']) ?></strong>
          <small><?= e(ucwords(str_replace('_', ' ', (string) $u['role']))) ?></small>
        </span>
      </div>
    </div>
  </header>

  <main class="admin-main" id="adminMain">
    <?= flash_render() ?>
