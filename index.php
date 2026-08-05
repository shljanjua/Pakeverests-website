<?php
/**
 * Pak-Everests — public front controller.
 * Every public URL is routed through here (see .htaccess).
 */

declare(strict_types=1);

define('PE_ROOT', __DIR__);
require_once __DIR__ . '/app/config.php';

/* ---------------------------------------------------------------------------
 |  Resolve the request path
 * ------------------------------------------------------------------------ */
$path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/');
$segments = $path === '' ? [] : explode('/', $path);
$route    = $segments[0] ?? '';
$param    = $segments[1] ?? '';

/* ---------------------------------------------------------------------------
 |  Installation guard — friendly message before the SQL files are imported.
 |  Runs before anything touches the database.
 * ------------------------------------------------------------------------ */
if (!db_table_exists('settings')) {
    require PE_ROOT . '/app/views/install-notice.php';
    exit;
}

$GLOBALS['seo'] = seo_defaults();

/* ---------------------------------------------------------------------------
 |  Maintenance mode
 * ------------------------------------------------------------------------ */
if (setting_bool('maintenance_mode', false) && empty($_SESSION['admin_user_id'])) {
    http_response_code(503);
    header('Retry-After: 3600');
    require PE_ROOT . '/app/views/maintenance.php';
    exit;
}

require_once PE_ROOT . '/app/forms.php';

/* Newsletter subscribe is available from every page footer. */
if (is_post() && post('form_type') === 'subscribe') {
    handle_subscribe_form();
}

/* ---------------------------------------------------------------------------
 |  Non-HTML endpoints
 * ------------------------------------------------------------------------ */
switch ($route) {
    case 'sitemap.xml':
        require PE_ROOT . '/app/views/sitemap.php';
        exit;
    case 'robots.txt':
        require PE_ROOT . '/app/views/robots.php';
        exit;
    case 'llms.txt':
        require PE_ROOT . '/app/views/llms.php';
        exit;
    case 'ads.txt':
        header('Content-Type: text/plain; charset=utf-8');
        echo trim((string) setting('ads_txt', '')) . "\n";
        exit;
}

/* IndexNow key verification file, served at /{key}.txt */
if (preg_match('/^[a-f0-9]{16,}\.txt$/', $route) && $route === indexnow_key() . '.txt') {
    header('Content-Type: text/plain; charset=utf-8');
    echo indexnow_key();
    exit;
}

/* ---------------------------------------------------------------------------
 |  Route table  ->  view file in app/views/
 * ------------------------------------------------------------------------ */
$routes = [
    ''                      => 'home',
    'products'              => 'products',
    'product'               => 'product',
    'purification-process'  => 'process',
    'minerals-and-benefits' => 'minerals',
    'about'                 => 'about',
    'contact'               => 'contact',
    'coverage-areas'        => 'coverage',
    'distribution'          => 'distribution',
    'distributor-application' => 'distributor-form',
    'custom-label-bottles'  => 'custom-labels',
    'custom-label-request'  => 'custom-label-form',
    'order'                 => 'order',
    'gallery'               => 'gallery',
    'documents'             => 'documents',
    'blog'                  => 'blog',
    'faqs'                  => 'faqs',
    'reviews'               => 'reviews',
    'careers'               => 'careers',
    'bulk-water-calculator' => 'calculator',
    'facility-filling'      => 'calculator',
    'thank-you'             => 'thank-you',
    'search'                => 'search',
    'sitemap'               => 'sitemap-html',
];

$viewFile = null;

if (isset($routes[$route])) {
    $viewFile = PE_ROOT . '/app/views/' . $routes[$route] . '.php';
} else {
    // Fall back to an admin-managed CMS page.
    $cmsPage = fetch_one('SELECT * FROM pages WHERE slug = ? AND status = "published"', [$route]);
    if ($cmsPage) {
        $GLOBALS['cmsPage'] = $cmsPage;
        $viewFile = PE_ROOT . '/app/views/page.php';
    }
}

if ($viewFile === null || !is_file($viewFile)) {
    http_response_code(404);
    $viewFile = PE_ROOT . '/app/views/404.php';
}

require $viewFile;
