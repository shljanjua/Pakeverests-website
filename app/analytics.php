<?php
/**
 * First-party analytics: page views, visitor device/browser/OS parsing and
 * optional IP → country/region resolution (cached, so one lookup per IP).
 */

declare(strict_types=1);

function ua_parse(string $ua): array
{
    $ua = $ua ?: '';
    $browser = 'Other';
    $os      = 'Other';
    $device  = 'Desktop';

    $browsers = [
        'Edge'      => '/Edg[eA]?\//i',
        'Opera'     => '/OPR\/|Opera/i',
        'Samsung'   => '/SamsungBrowser/i',
        'Chrome'    => '/Chrome\/|CriOS/i',
        'Firefox'   => '/Firefox\/|FxiOS/i',
        'Safari'    => '/Safari\//i',
        'Instagram' => '/Instagram/i',
        'Facebook'  => '/FBAN|FBAV/i',
    ];
    foreach ($browsers as $name => $re) {
        if (preg_match($re, $ua)) { $browser = $name; break; }
    }

    $systems = [
        'Android' => '/Android/i',
        'iOS'     => '/iPhone|iPad|iPod/i',
        'Windows' => '/Windows NT/i',
        'macOS'   => '/Macintosh|Mac OS X/i',
        'Linux'   => '/Linux/i',
    ];
    foreach ($systems as $name => $re) {
        if (preg_match($re, $ua)) { $os = $name; break; }
    }

    if (preg_match('/iPad|Tablet/i', $ua)) {
        $device = 'Tablet';
    } elseif (preg_match('/Mobi|Android|iPhone/i', $ua)) {
        $device = 'Mobile';
    }
    if (preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|lighthouse|pagespeed/i', $ua)) {
        $device = 'Bot';
    }

    return ['browser' => $browser, 'os' => $os, 'device' => $device];
}

/** Resolve an IP to country/region/city, cached in the geo_cache table. */
function geo_lookup(string $ip): array
{
    $unknown = ['country' => 'Unknown', 'country_code' => '', 'region' => '', 'city' => ''];

    if ($ip === '0.0.0.0' || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        return ['country' => 'Local', 'country_code' => '', 'region' => '', 'city' => ''];
    }

    // Cloudflare / proxy supplied country wins — free and instant.
    $cf = $_SERVER['HTTP_CF_IPCOUNTRY'] ?? '';

    try {
        $cached = fetch_one('SELECT * FROM geo_cache WHERE ip_address = ?', [$ip]);
        if ($cached) {
            return [
                'country'      => $cached['country'],
                'country_code' => $cached['country_code'],
                'region'       => $cached['region'],
                'city'         => $cached['city'],
            ];
        }
    } catch (Throwable $e) {
        return $unknown;
    }

    $geo = $unknown;
    if (setting_bool('geo_lookup_enabled', true) && function_exists('curl_init')) {
        $ch = curl_init('http://ip-api.com/json/' . $ip . '?fields=status,country,countryCode,regionName,city');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 2,
            CURLOPT_CONNECTTIMEOUT => 2,
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);
        $data = json_decode((string) $raw, true);
        if (is_array($data) && ($data['status'] ?? '') === 'success') {
            $geo = [
                'country'      => $data['country'] ?: 'Unknown',
                'country_code' => $data['countryCode'] ?: '',
                'region'       => $data['regionName'] ?: '',
                'city'         => $data['city'] ?: '',
            ];
        }
    }
    if ($geo['country_code'] === '' && $cf !== '' && $cf !== 'XX') {
        $geo['country_code'] = $cf;
        $geo['country']      = $cf;
    }

    try {
        db_insert('geo_cache', [
            'ip_address'   => $ip,
            'country'      => $geo['country'],
            'country_code' => $geo['country_code'],
            'region'       => $geo['region'],
            'city'         => $geo['city'],
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
    } catch (Throwable $e) {
        // Duplicate/race — ignore.
    }

    return $geo;
}

/** Record a page view. Called once per public request from index.php. */
function track_pageview(string $pageTitle = ''): void
{
    if (!setting_bool('analytics_tracking_enabled', true)) {
        return;
    }

    $ua   = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $meta = ua_parse($ua);

    if ($meta['device'] === 'Bot' && !setting_bool('analytics_track_bots', false)) {
        return;
    }

    if (empty($_SESSION['pe_visitor'])) {
        $_SESSION['pe_visitor'] = bin2hex(random_bytes(12));
        $_SESSION['pe_started'] = time();
    }

    $ip  = client_ip();
    $geo = geo_lookup($ip);

    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    $source   = 'Direct';
    if ($referrer !== '') {
        $host = parse_url($referrer, PHP_URL_HOST) ?: '';
        if ($host && !str_contains($host, parse_url(SITE_URL, PHP_URL_HOST) ?: 'pakeverests.site')) {
            if (preg_match('/google\./i', $host))          $source = 'Google';
            elseif (preg_match('/bing\./i', $host))        $source = 'Bing';
            elseif (preg_match('/facebook|fb\./i', $host)) $source = 'Facebook';
            elseif (preg_match('/instagram/i', $host))     $source = 'Instagram';
            elseif (preg_match('/whatsapp/i', $host))      $source = 'WhatsApp';
            elseif (preg_match('/youtube/i', $host))       $source = 'YouTube';
            elseif (preg_match('/tiktok/i', $host))        $source = 'TikTok';
            else                                           $source = $host;
        } else {
            $source = 'Internal';
        }
    }
    if (!empty($_GET['utm_source'])) {
        $source = substr(preg_replace('/[^\w\-. ]/', '', (string) $_GET['utm_source']), 0, 60);
    }

    try {
        db_insert('page_views', [
            'session_id'   => $_SESSION['pe_visitor'],
            'page_url'     => substr(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', 0, 255),
            'page_title'   => substr($pageTitle, 0, 190),
            'referrer'     => substr($referrer, 0, 255),
            'source'       => substr($source, 0, 80),
            'ip_address'   => $ip,
            'country'      => $geo['country'],
            'country_code' => $geo['country_code'],
            'region'       => $geo['region'],
            'city'         => $geo['city'],
            'browser'      => $meta['browser'],
            'os'           => $meta['os'],
            'device'       => $meta['device'],
            'user_agent'   => substr($ua, 0, 255),
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
    } catch (Throwable $e) {
        // Never let analytics break a page render.
    }
}

/** Bump a counter used by the dashboard (orders today, etc.). */
function analytics_range_where(string $range): array
{
    switch ($range) {
        case 'today': return ['created_at >= CURDATE()', []];
        case '7d':    return ['created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)', []];
        case '90d':   return ['created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)', []];
        case 'all':   return ['1=1', []];
        case '30d':
        default:      return ['created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)', []];
    }
}
