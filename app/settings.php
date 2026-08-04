<?php
/**
 * Key/value settings store — everything the admin panel can change lives here.
 */

declare(strict_types=1);

function settings_all(bool $refresh = false): array
{
    static $cache = null;
    if ($cache !== null && !$refresh) {
        return $cache;
    }
    $cache = [];
    try {
        foreach (fetch_all('SELECT setting_key, setting_value FROM settings') as $row) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
    } catch (Throwable $e) {
        $cache = [];
    }
    return $cache;
}

/** Read a setting with a sensible fallback. */
function setting(string $key, $default = '')
{
    $all = settings_all();
    $val = $all[$key] ?? null;
    if ($val === null || $val === '') {
        return $default;
    }
    return $val;
}

function setting_bool(string $key, bool $default = false): bool
{
    $val = setting($key, $default ? '1' : '0');
    return in_array((string) $val, ['1', 'yes', 'true', 'on'], true);
}

function setting_set(string $key, $value): void
{
    q(
        'INSERT INTO settings (setting_key, setting_value, updated_at) VALUES (?, ?, NOW())
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()',
        [$key, (string) $value]
    );
    settings_all(true);
}

function settings_save(array $pairs): void
{
    foreach ($pairs as $k => $v) {
        if (is_array($v)) {
            $v = implode(',', $v);
        }
        setting_set((string) $k, (string) $v);
    }
}

/* ---------------------------------------------------------------------------
 |  Convenience accessors used throughout the site
 * ------------------------------------------------------------------------ */

function site_name(): string
{
    return (string) setting('site_name', 'Pak-Everests Bottled Drinking Water');
}

function site_tagline(): string
{
    return (string) setting('site_tagline', 'Pure Mineral Water from the Heart of Potohar');
}

function contact_email(): string
{
    return (string) setting('contact_email', 'info@pakeverests.site');
}

/** Active WhatsApp numbers, primary first. */
function whatsapp_numbers(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    try {
        $cache = fetch_all(
            'SELECT * FROM whatsapp_numbers WHERE is_active = 1 ORDER BY is_primary DESC, sort_order ASC, id ASC'
        );
    } catch (Throwable $e) {
        $cache = [];
    }
    if (!$cache) {
        $cache = [
            ['id' => 0, 'label' => 'Orders', 'number' => '0333 5592206', 'is_primary' => 1],
            ['id' => 0, 'label' => 'Support', 'number' => '0332 2901309', 'is_primary' => 0],
        ];
    }
    return $cache;
}

function primary_whatsapp(): string
{
    $numbers = whatsapp_numbers();
    return (string) ($numbers[0]['number'] ?? '0333 5592206');
}

/** Active news-ticker items. */
function ticker_items(): array
{
    try {
        return fetch_all(
            'SELECT * FROM news_ticker WHERE is_active = 1
             AND (starts_at IS NULL OR starts_at <= NOW())
             AND (ends_at   IS NULL OR ends_at   >= NOW())
             ORDER BY sort_order ASC, id DESC'
        );
    } catch (Throwable $e) {
        return [];
    }
}

/** Footer/legal pages managed from the admin panel. */
function nav_pages(string $group = 'legal'): array
{
    try {
        return fetch_all(
            'SELECT title, slug FROM pages WHERE status = "published" AND nav_group = ? ORDER BY sort_order ASC, title ASC',
            [$group]
        );
    } catch (Throwable $e) {
        return [];
    }
}

/** Ad code for a placement, when monetisation is enabled. */
function ad_slot(string $placement): string
{
    if (!setting_bool('ads_enabled', false)) {
        return '';
    }
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            foreach (fetch_all('SELECT placement, code FROM ad_slots WHERE is_active = 1') as $row) {
                $cache[$row['placement']][] = $row['code'];
            }
        } catch (Throwable $e) {
            $cache = [];
        }
    }
    if (empty($cache[$placement])) {
        return '';
    }
    return '<div class="ad-slot ad-slot--' . e($placement) . '">' . implode("\n", $cache[$placement]) . '</div>';
}

/** Delivery areas (used by nav, coverage page and order form). */
function coverage_areas(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    try {
        $cache = fetch_all('SELECT * FROM coverage_areas WHERE is_active = 1 ORDER BY sort_order ASC, area_name ASC');
    } catch (Throwable $e) {
        $cache = [];
    }
    return $cache;
}
