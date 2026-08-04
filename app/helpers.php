<?php
/**
 * Shared helpers: escaping, URLs, formatting, CSRF, uploads, flash messages.
 */

declare(strict_types=1);

/* ---------------------------------------------------------------------------
 |  Output
 * ------------------------------------------------------------------------ */

/** HTML-escape. */
function e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Escape for use inside a JS string / JSON attribute. */
function ejs($value): string
{
    return json_encode((string) $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

/**
 * Render admin-authored rich text. Content is written by the site owner in the
 * admin panel, so a permissive but tag-limited filter is used: script/iframe
 * style vectors are stripped, layout markup is kept.
 */
function rich_text(?string $html): string
{
    if ($html === null || trim($html) === '') {
        return '';
    }
    $allowed = '<p><br><strong><b><em><i><u><ul><ol><li><h2><h3><h4><h5><blockquote>'
             . '<a><img><table><thead><tbody><tr><th><td><hr><span><div><figure><figcaption><small><sup><sub><code><pre>';
    $clean = strip_tags($html, $allowed);
    // Neutralise inline event handlers and javascript: URLs.
    $clean = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean);
    $clean = preg_replace('/javascript\s*:/i', '', $clean);
    return $clean;
}

/** Plain-text excerpt of any HTML. */
function excerpt(?string $html, int $chars = 160): string
{
    $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $html)));
    if (mb_strlen($text) <= $chars) {
        return $text;
    }
    return rtrim(mb_substr($text, 0, $chars), " ,.;:-") . '…';
}

/* ---------------------------------------------------------------------------
 |  URLs
 * ------------------------------------------------------------------------ */

function url(string $path = ''): string
{
    return SITE_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    $rel  = '/assets/' . ltrim($path, '/');
    $file = PE_ROOT . $rel;
    $ver  = is_file($file) ? substr((string) filemtime($file), -6) : '1';
    return $rel . '?v=' . $ver;
}

/**
 * URL for an uploaded/managed image with a graceful fallback.
 * Missing images resolve to the branded SVG placeholder so the layout never
 * breaks before the owner uploads the real WebP assets.
 */
function media_url(?string $path, string $fallback = 'placeholder'): string
{
    $path = trim((string) $path);
    if ($path !== '') {
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        $rel = '/' . ltrim($path, '/');
        if (is_file(PE_ROOT . $rel)) {
            return $rel;
        }
    }
    return '/assets/img/placeholder-' . $fallback . '.svg';
}

function current_url(): string
{
    return SITE_URL . ($_SERVER['REQUEST_URI'] ?? '/');
}

function redirect(string $path, int $code = 302): void
{
    $target = preg_match('#^https?://#i', $path) ? $path : url($path);
    header('Location: ' . $target, true, $code);
    exit;
}

function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = strtolower(trim((string) $text, '-'));
    $text = preg_replace('~-+~', '-', $text);
    return $text !== '' ? $text : 'item';
}

/* ---------------------------------------------------------------------------
 |  Formatting
 * ------------------------------------------------------------------------ */

function money($amount, bool $withCode = true): string
{
    $n = number_format((float) $amount, ((float) $amount == floor((float) $amount)) ? 0 : 2);
    return $withCode ? 'Rs ' . $n : $n;
}

function pretty_date(?string $date, string $format = 'd M Y'): string
{
    if (!$date || $date === '0000-00-00 00:00:00') {
        return '—';
    }
    return date($format, strtotime($date));
}

function time_ago(?string $date): string
{
    if (!$date) {
        return '—';
    }
    $diff = time() - strtotime($date);
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return floor($diff / 60) . ' min ago';
    if ($diff < 86400)  return floor($diff / 3600) . ' hr ago';
    if ($diff < 604800) return floor($diff / 86400) . ' d ago';
    return pretty_date($date);
}

/** Normalise a Pakistani mobile number to wa.me format (923xxxxxxxxx). */
function wa_number(string $number): string
{
    $digits = preg_replace('/\D+/', '', $number);
    if (str_starts_with($digits, '92')) {
        return $digits;
    }
    if (str_starts_with($digits, '0')) {
        return '92' . substr($digits, 1);
    }
    if (str_starts_with($digits, '3')) {
        return '92' . $digits;
    }
    return $digits;
}

function wa_link(string $number, string $message = ''): string
{
    $url = 'https://wa.me/' . wa_number($number);
    if ($message !== '') {
        $url .= '?text=' . rawurlencode($message);
    }
    return $url;
}

/** Render a 5-star rating block. */
function stars(float $rating, bool $showValue = false): string
{
    $rating = max(0, min(5, $rating));
    $out    = '<span class="stars" role="img" aria-label="' . number_format($rating, 1) . ' out of 5 stars">';
    for ($i = 1; $i <= 5; $i++) {
        $class = $rating >= $i ? 'is-full' : ($rating >= $i - 0.5 ? 'is-half' : 'is-empty');
        $out  .= '<i class="star ' . $class . '" aria-hidden="true"></i>';
    }
    $out .= '</span>';
    if ($showValue) {
        $out .= '<span class="stars-value">' . number_format($rating, 1) . '</span>';
    }
    return $out;
}

/* ---------------------------------------------------------------------------
 |  CSRF
 * ------------------------------------------------------------------------ */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify(?string $token = null): bool
{
    $token = $token ?? ($_POST['csrf_token'] ?? '');
    return is_string($token) && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function csrf_guard(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
        http_response_code(419);
        die('Security token expired. Please go back, refresh the page and submit the form again.');
    }
}

/* ---------------------------------------------------------------------------
 |  Input
 * ------------------------------------------------------------------------ */

function post(string $key, $default = '')
{
    $val = $_POST[$key] ?? $default;
    return is_string($val) ? trim($val) : $val;
}

function get(string $key, $default = '')
{
    $val = $_GET[$key] ?? $default;
    return is_string($val) ? trim($val) : $val;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function client_ip(): string
{
    foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = trim(explode(',', $_SERVER[$key])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return '0.0.0.0';
}

/* ---------------------------------------------------------------------------
 |  Flash messages
 * ------------------------------------------------------------------------ */

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function flash_pull(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

function flash_render(): string
{
    $out = '';
    foreach (flash_pull() as $f) {
        $out .= '<div class="alert alert-' . e($f['type']) . '">' . e($f['message']) . '</div>';
    }
    return $out;
}

/* ---------------------------------------------------------------------------
 |  Uploads
 * ------------------------------------------------------------------------ */

const PE_IMAGE_TYPES = ['webp', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'avif'];
const PE_DOC_TYPES   = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];

/**
 * Handle a single file upload.
 *
 * @return array{ok:bool,path?:string,name?:string,size?:int,ext?:string,error?:string}
 */
function handle_upload(array $file, string $folder = 'general', array $allowed = PE_IMAGE_TYPES): array
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['ok' => false, 'error' => 'No file selected.'];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'Upload failed (code ' . $file['error'] . '). The file may be larger than the server limit.'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        return ['ok' => false, 'error' => 'File type .' . $ext . ' is not allowed here. Allowed: ' . implode(', ', $allowed) . '.'];
    }

    $maxBytes = 12 * 1024 * 1024; // 12 MB
    if ($file['size'] > $maxBytes) {
        return ['ok' => false, 'error' => 'File is larger than 12 MB.'];
    }

    $folder = preg_replace('/[^a-z0-9\-_]/i', '', $folder) ?: 'general';
    $sub    = $folder . '/' . date('Y/m');
    $dir    = UPLOADS_DIR . '/' . $sub;
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        return ['ok' => false, 'error' => 'Could not create the upload folder. Check permissions on /uploads.'];
    }

    $base     = slugify(pathinfo($file['name'], PATHINFO_FILENAME));
    $filename = $base . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    $target   = $dir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return ['ok' => false, 'error' => 'Could not save the uploaded file.'];
    }
    @chmod($target, 0644);

    $relative = ltrim(UPLOADS_URL, '/') . '/' . $sub . '/' . $filename;

    return [
        'ok'   => true,
        'path' => $relative,
        'name' => $file['name'],
        'size' => (int) $file['size'],
        'ext'  => $ext,
    ];
}

/** Record an uploaded file in the media library. */
function media_record(array $upload, string $alt = '', string $folder = 'general'): int
{
    return db_insert('media', [
        'file_path'  => $upload['path'],
        'file_name'  => $upload['name'],
        'file_type'  => in_array($upload['ext'], PE_IMAGE_TYPES, true) ? 'image' : 'document',
        'extension'  => $upload['ext'],
        'file_size'  => $upload['size'],
        'alt_text'   => $alt,
        'folder'     => $folder,
        'created_at' => date('Y-m-d H:i:s'),
    ]);
}

/* ---------------------------------------------------------------------------
 |  Misc
 * ------------------------------------------------------------------------ */

function generate_ref(string $prefix): string
{
    return strtoupper($prefix) . '-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
}

function paginate(int $total, int $perPage, int $page): array
{
    $pages = max(1, (int) ceil($total / max(1, $perPage)));
    $page  = max(1, min($page, $pages));
    return [
        'total'   => $total,
        'pages'   => $pages,
        'page'    => $page,
        'offset'  => ($page - 1) * $perPage,
        'perPage' => $perPage,
    ];
}

function pagination_links(array $p, string $baseUrl): string
{
    if ($p['pages'] < 2) {
        return '';
    }
    $sep  = str_contains($baseUrl, '?') ? '&' : '?';
    $out  = '<nav class="pagination" aria-label="Pagination">';
    if ($p['page'] > 1) {
        $out .= '<a class="page-link" href="' . e($baseUrl . $sep . 'page=' . ($p['page'] - 1)) . '" rel="prev">← Prev</a>';
    }
    $start = max(1, $p['page'] - 2);
    $end   = min($p['pages'], $start + 4);
    for ($i = $start; $i <= $end; $i++) {
        $cls  = $i === $p['page'] ? 'page-link is-active' : 'page-link';
        $out .= '<a class="' . $cls . '" href="' . e($baseUrl . $sep . 'page=' . $i) . '">' . $i . '</a>';
    }
    if ($p['page'] < $p['pages']) {
        $out .= '<a class="page-link" href="' . e($baseUrl . $sep . 'page=' . ($p['page'] + 1)) . '" rel="next">Next →</a>';
    }
    return $out . '</nav>';
}

/** Simple per-IP rate limiter for public forms. */
function rate_limited(string $bucket, int $maxHits = 5, int $windowSeconds = 600): bool
{
    $key = 'rl_' . $bucket;
    $now = time();
    $log = array_values(array_filter($_SESSION[$key] ?? [], fn($t) => $t > $now - $windowSeconds));
    if (count($log) >= $maxHits) {
        $_SESSION[$key] = $log;
        return true;
    }
    $log[]          = $now;
    $_SESSION[$key] = $log;
    return false;
}

/** Honeypot check — bots fill hidden fields. */
function is_spam_submission(): bool
{
    return trim((string) ($_POST['website'] ?? '')) !== '';
}
