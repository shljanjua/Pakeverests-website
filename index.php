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
 |  Automated content API — create a blog post as a DRAFT for owner approval.
 |  POST /api/publish-blog with header  X-Content-Key: <content_api_key()>
 |  and a JSON body { title, content, excerpt, category, tags, meta_* }.
 * ------------------------------------------------------------------------ */
if ($route === 'api' && $param === 'publish-blog') {
    header('Content-Type: application/json; charset=utf-8');
    $fail = function (int $code, string $msg) {
        http_response_code($code);
        echo json_encode(['ok' => false, 'error' => $msg]);
        exit;
    };
    if (!is_post()) {
        $fail(405, 'POST required');
    }
    $given = (string) ($_SERVER['HTTP_X_CONTENT_KEY'] ?? '');
    if ($given === '' || !hash_equals(content_api_key(), $given)) {
        $fail(401, 'Unauthorized');
    }
    if (rate_limited('content_api', 12, 3600)) {
        $fail(429, 'Rate limit exceeded');
    }
    $in = json_decode((string) file_get_contents('php://input'), true);
    if (!is_array($in)) {
        $fail(400, 'Invalid JSON body');
    }
    $title   = trim((string) ($in['title'] ?? ''));
    $content = trim((string) ($in['content'] ?? ''));
    if ($title === '' || mb_strlen($title) > 190) {
        $fail(422, 'A title (max 190 chars) is required');
    }
    if (str_word_count(strip_tags($content)) < 300) {
        $fail(422, 'Content must be a substantial article (300+ words)');
    }
    $slug = slugify((string) ($in['slug'] ?? $title));
    if (fetch_val('SELECT id FROM blog_posts WHERE slug = ?', [$slug])) {
        $slug .= '-' . substr((string) time(), -4);
    }
    $now  = date('Y-m-d H:i:s');
    $data = [
        'slug'             => $slug,
        'title'            => $title,
        'excerpt'          => trim((string) ($in['excerpt'] ?? '')),
        'content'          => $content,
        'category'         => trim((string) ($in['category'] ?? '')) ?: 'Water & Health',
        'tags'             => trim((string) ($in['tags'] ?? '')),
        'author'           => trim((string) ($in['author'] ?? '')) ?: 'Pak-Everests Team',
        'reading_minutes'  => max(1, (int) ceil(str_word_count(strip_tags($content)) / 200)),
        'meta_title'       => trim((string) ($in['meta_title'] ?? '')),
        'meta_description' => trim((string) ($in['meta_description'] ?? '')),
        'meta_keywords'    => trim((string) ($in['meta_keywords'] ?? '')),
        'is_featured'      => 0,
        'status'           => 'draft',   // always a draft — the owner approves before it goes live
        'published_at'     => $now,
        'created_at'       => $now,
        'updated_at'       => $now,
    ];
    $id = db_insert('blog_posts', $data);
    echo json_encode([
        'ok'       => true,
        'id'       => (int) $id,
        'status'   => 'draft',
        'slug'     => $slug,
        'edit_url' => SITE_URL . '/admin/blog?edit=' . (int) $id,
    ]);
    exit;
}

/* Legacy / short-form URL redirects to the canonical page (301). */
$aliasRedirects = [
    'coverage'      => 'coverage-areas',
    'coverage-area' => 'coverage-areas',
    'distributor'   => 'distribution',
    'faq'           => 'faqs',
    'review'        => 'reviews',
];
if ($param === '' && isset($aliasRedirects[$route])) {
    redirect($aliasRedirects[$route], 301);
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
