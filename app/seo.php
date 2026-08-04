<?php
/**
 * SEO layer — titles, meta, canonicals, Open Graph, Twitter cards,
 * JSON-LD schema and breadcrumbs.
 *
 * Every view sets $GLOBALS['seo'] via seo_set() before including the header.
 */

declare(strict_types=1);

function seo_defaults(): array
{
    return [
        'title'        => site_name(),
        'description'  => setting('meta_description', 'Punjab Food Authority approved mineral water in 19L refills, 12L, 6L, 1.5L and 500ml packs with free delivery across Rawalpindi and Islamabad.'),
        'keywords'     => setting('meta_keywords', 'water plant near me, mineral water, 19 liters water bottle, mineral water plant, water plant in Gujar Khan, best water plant in Gujar Khan'),
        'canonical'    => current_canonical(),
        'robots'       => 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
        'og_type'      => 'website',
        'image'        => setting('og_image', '/assets/img/og-default.jpg'),
        'breadcrumbs'  => [],
        'schema'       => [],
        'published_at' => null,
        'modified_at'  => null,
    ];
}

/** Canonical URL for the current request (query string stripped by default). */
function current_canonical(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $path = rtrim($path, '/');
    // Keep pagination in the canonical — it is a distinct page.
    $page = (int) ($_GET['page'] ?? 0);
    $qs   = $page > 1 ? '?page=' . $page : '';
    return SITE_URL . ($path === '' ? '/' : $path) . $qs;
}

function seo_set(array $data): void
{
    $GLOBALS['seo'] = array_merge($GLOBALS['seo'] ?? seo_defaults(), $data);
}

function seo_get(string $key, $default = null)
{
    $seo = $GLOBALS['seo'] ?? seo_defaults();
    return $seo[$key] ?? $default;
}

function seo_add_schema(array $schema): void
{
    $GLOBALS['seo']['schema'][] = $schema;
}

/** Absolute URL for an image path. */
function abs_url(string $path): string
{
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    return SITE_URL . '/' . ltrim($path, '/');
}

/* ---------------------------------------------------------------------------
 |  Organisation / LocalBusiness — the base entity for the whole site
 * ------------------------------------------------------------------------ */
function schema_organization(): array
{
    $sameAs = array_values(array_filter([
        setting('social_facebook'),
        setting('social_instagram'),
        setting('social_youtube'),
        setting('social_tiktok'),
        setting('social_linkedin'),
        setting('social_twitter'),
    ]));

    $numbers = array_map(fn($n) => '+' . wa_number($n['number']), whatsapp_numbers());

    return [
        '@context'    => 'https://schema.org',
        '@type'       => ['LocalBusiness', 'FoodEstablishment'],
        '@id'         => SITE_URL . '/#organization',
        'name'        => site_name(),
        'alternateName' => 'Pak-Everests Water',
        'url'         => SITE_URL . '/',
        'logo'        => abs_url(setting('logo_path', '/assets/img/logo.webp')),
        'image'       => abs_url(setting('og_image', '/assets/img/og-default.jpg')),
        'description' => setting('meta_description', 'Punjab Food Authority approved mineral water plant in Gujar Khan, Potohar, Punjab.'),
        'email'       => contact_email(),
        'telephone'   => $numbers[0] ?? '+923335592206',
        'priceRange'  => 'Rs 130 – Rs 42,000',
        'currenciesAccepted' => 'PKR',
        'paymentAccepted'    => 'Cash on Delivery, EasyPaisa, JazzCash, Bank Transfer',
        'address'     => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => setting('address_street', 'Main G.T. Road, Gujar Khan'),
            'addressLocality' => setting('address_city', 'Gujar Khan'),
            'addressRegion'   => setting('address_region', 'Punjab'),
            'postalCode'      => setting('address_postal', '47850'),
            'addressCountry'  => 'PK',
        ],
        'geo' => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => (float) setting('map_lat', '33.2544'),
            'longitude' => (float) setting('map_lng', '73.3047'),
        ],
        'openingHoursSpecification' => [[
            '@type'     => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            'opens'     => setting('hours_open', '08:00'),
            'closes'    => setting('hours_close', '21:00'),
        ]],
        'areaServed' => array_map(
            fn($a) => ['@type' => 'City', 'name' => $a['area_name']],
            coverage_areas() ?: [['area_name' => 'Gujar Khan'], ['area_name' => 'Rawalpindi'], ['area_name' => 'Islamabad']]
        ),
        'sameAs'    => $sameAs,
        'hasCredential' => 'Punjab Food Authority Licensed',
    ];
}

function schema_website(): array
{
    return [
        '@context'        => 'https://schema.org',
        '@type'           => 'WebSite',
        '@id'             => SITE_URL . '/#website',
        'url'             => SITE_URL . '/',
        'name'            => site_name(),
        'publisher'       => ['@id' => SITE_URL . '/#organization'],
        'inLanguage'      => 'en-PK',
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => ['@type' => 'EntryPoint', 'urlTemplate' => SITE_URL . '/blog?q={search_term_string}'],
            'query-input' => 'required name=search_term_string',
        ],
    ];
}

function schema_breadcrumbs(array $crumbs): array
{
    $items = [];
    $pos   = 1;
    $items[] = ['@type' => 'ListItem', 'position' => $pos++, 'name' => 'Home', 'item' => SITE_URL . '/'];
    foreach ($crumbs as $label => $href) {
        $item = ['@type' => 'ListItem', 'position' => $pos++, 'name' => $label];
        if ($href) {
            $item['item'] = abs_url($href);
        }
        $items[] = $item;
    }
    return [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ];
}

function schema_faq(array $faqs): array
{
    return [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array_map(fn($f) => [
            '@type'          => 'Question',
            'name'           => $f['question'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($f['answer'])],
        ], $faqs),
    ];
}

/** Print every JSON-LD block collected for this request. */
function seo_render_schema(): string
{
    $blocks = seo_get('schema', []);
    $out    = '';
    foreach ($blocks as $block) {
        $json = json_encode($block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $out .= '<script type="application/ld+json">' . $json . '</script>' . "\n";
    }
    return $out;
}

/** Visible breadcrumb trail for the page hero. */
function breadcrumbs_html(): string
{
    $crumbs = seo_get('breadcrumbs', []);
    if (!$crumbs) {
        return '';
    }
    $out = '<ol class="breadcrumbs"><li><a href="' . e(url('/')) . '">Home</a></li>';
    foreach ($crumbs as $label => $href) {
        $out .= '<li>' . ($href ? '<a href="' . e(abs_url($href)) . '">' . e($label) . '</a>' : e($label)) . '</li>';
    }
    return $out . '</ol>';
}

/** Full <title> including brand suffix. */
function seo_title(): string
{
    $title = (string) seo_get('title', site_name());
    $brand = setting('brand_suffix', 'Pak-Everests');
    if (stripos($title, $brand) !== false) {
        return $title;
    }
    return $title . ' | ' . $brand;
}
