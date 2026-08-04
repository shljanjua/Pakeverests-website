<?php
/**
 * Pak-Everests — admin panel front controller.
 * Every /admin/* URL is routed through here.
 */

declare(strict_types=1);

define('PE_ROOT', dirname(__DIR__));
define('PE_ADMIN', true);

require_once PE_ROOT . '/app/config.php';
require_once PE_ROOT . '/app/admin/auth.php';
require_once PE_ROOT . '/app/admin/helpers.php';

if (!db_table_exists('admin_users')) {
    require PE_ROOT . '/app/views/install-notice.php';
    exit;
}

/* ---- Resolve the module -------------------------------------------------- */
$path     = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/');
$segments = explode('/', $path);
array_shift($segments);                       // drop "admin"
$module   = $segments[0] ?? 'dashboard';
$action   = $segments[1] ?? '';
$module   = preg_replace('/[^a-z0-9\-]/', '', strtolower($module)) ?: 'dashboard';

/* ---- Public admin routes ------------------------------------------------- */
if ($module === 'logout') {
    admin_logout();
    redirect('admin/login');
}

if ($module === 'login') {
    if (admin_user()) {
        redirect('admin');
    }
    require PE_ROOT . '/app/admin/login.php';
    exit;
}

/* ---- Everything else requires a session ---------------------------------- */
admin_require_login();

/* CSRF on every admin POST. */
if (is_post() && !csrf_verify()) {
    http_response_code(419);
    die('Security token expired. Please go back, refresh the page and try again.');
}

$modules = [
    'dashboard'    => 'dashboard',
    ''             => 'dashboard',
    'analytics'    => 'analytics',
    'orders'       => 'orders',
    'messages'     => 'messages',
    'distributors' => 'distributors',
    'labels'       => 'labels',
    'reviews'      => 'reviews',
    'products'     => 'products',
    'gallery'      => 'gallery',
    'media'        => 'media',
    'documents'    => 'documents',
    'blog'         => 'blog',
    'pages'        => 'pages',
    'faqs'         => 'faqs',
    'coverage'     => 'coverage',
    'careers'      => 'careers',
    'ticker'       => 'ticker',
    'whatsapp'     => 'whatsapp',
    'subscribers'  => 'subscribers',
    'agreements'   => 'agreements',
    'quotations'   => 'quotations',
    'process'      => 'process',
    'minerals'     => 'minerals',
    'ads'          => 'ads',
    'settings'     => 'settings',
    'users'        => 'users',
];

$file = PE_ROOT . '/app/admin/' . ($modules[$module] ?? 'dashboard') . '.php';
if (!is_file($file)) {
    $file = PE_ROOT . '/app/admin/dashboard.php';
}

$GLOBALS['adminModule'] = $module === '' ? 'dashboard' : $module;
$GLOBALS['adminAction'] = $action;

require $file;
