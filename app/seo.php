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

/**
 * A guaranteed raster (non-SVG) absolute image URL for structured data.
 * Google Merchant listings / Product rich results reject SVG images, and the
 * site's default placeholders are SVG, so fall back to a real raster image
 * (the configured OG image, else the bundled JPG) whenever the given path is
 * missing, not a real file, or an SVG.
 */
function schema_image_url(?string $path): string
{
    $candidates = [
        trim((string) $path),
        trim((string) setting('og_image', '')),
        '/assets/img/og-default.jpg',
    ];
    foreach ($candidates as $c) {
        if ($c === '' || preg_match('#\.svg(\?|$)#i', $c)) {
            continue;
        }
        if (preg_match('#^https?://#i', $c)) {
            return $c;
        }
        $rel = '/' . ltrim($c, '/');
        if (is_file(PE_ROOT . $rel)) {
            return abs_url($rel);
        }
    }
    return abs_url('/assets/img/og-default.jpg');
}

/* ---------------------------------------------------------------------------
 |  Organisation / LocalBusiness — the base entity for the whole site
 * ------------------------------------------------------------------------ */
function schema_organization(): array
{
    /* sameAs is the strongest authority signal — it ties the website to the
       verified Google Business Profile and every official social account. */
    $sameAs = array_values(array_filter([
        setting('google_business_url'),   // Google Business Profile / Maps listing
        setting('social_facebook'),
        setting('social_instagram'),
        setting('social_youtube'),
        setting('social_tiktok'),
        setting('social_linkedin'),
        setting('social_twitter'),
    ]));

    $numbers = array_map(fn($n) => '+' . wa_number($n['number']), whatsapp_numbers());

    $org = [
        '@context'    => 'https://schema.org',
        '@type'       => ['LocalBusiness', 'FoodEstablishment', 'Store'],
        '@id'         => SITE_URL . '/#organization',
        'name'        => site_name(),
        'legalName'   => setting('legal_name', 'Pak-Everests Bottled Drinking Water'),
        'alternateName' => ['Pak-Everests Water', 'Pak Everests', 'PakEverests'],
        'url'         => SITE_URL . '/',
        'logo'        => [
            '@type'  => 'ImageObject',
            'url'    => abs_url(setting('logo_path', '/assets/img/logo.webp')),
            'width'  => 400,
            'height' => 120,
        ],
        'image'       => abs_url(setting('og_image', '/assets/img/og-default.jpg')),
        'description' => setting('meta_description', 'Punjab Food Authority approved mineral water plant in Gujar Khan, Potohar, Punjab.'),
        'slogan'      => site_tagline(),
        'email'       => contact_email(),
        'telephone'   => $numbers[0] ?? '+923335592206',
        'foundingDate'=> setting('company_founded', '2019'),
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
        'knowsAbout'  => [
            'Mineral water', 'Bottled drinking water', '19 litre water bottle refill',
            'Reverse osmosis water purification', 'Water dispenser supply', 'Custom label water bottles',
            'Water delivery in Gujar Khan', 'Water delivery in Rawalpindi', 'Water delivery in Islamabad',
        ],
        'sameAs'      => $sameAs,
        'hasCredential' => [
            '@type' => 'EducationalOccupationalCredential',
            'credentialCategory' => 'license',
            'name'  => setting('license_authority', 'Punjab Food Authority') . ' Licence',
            'identifier' => setting('license_number', ''),
        ],
        'award' => setting('license_authority', 'Punjab Food Authority') . ' Approved Bottled Drinking Water Establishment',
    ];

    /* Link the entity to the verified Google Business Profile / Maps listing. */
    $map = setting('google_business_url') ?: setting('map_directions_url', '');
    if ($map !== '') {
        $org['hasMap'] = $map;
    }

    /* Aggregate rating from genuinely approved reviews — a real trust signal
       that can surface star ratings in local and organic results. */
    try {
        $agg = fetch_one('SELECT AVG(rating) AS avg_rating, COUNT(*) AS total FROM reviews WHERE status = "approved"');
        if ($agg && (int) $agg['total'] > 0) {
            $org['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => number_format((float) $agg['avg_rating'], 1),
                'reviewCount' => (int) $agg['total'],
                'bestRating'  => 5,
                'worstRating' => 1,
            ];
        }
    } catch (Throwable $e) {
        // Ratings are optional; never break the page.
    }

    return $org;
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

/* ---------------------------------------------------------------------------
 |  IndexNow — instantly notify search engines when a page is published or
 |  updated, so new content is discovered without waiting for a crawl.
 |  Supported by Bing, Yandex, Seznam, Naver and others from a single ping.
 * ------------------------------------------------------------------------ */

/** The IndexNow key: generated once, stored, and served as a text file at the root. */
function indexnow_key(): string
{
    $key = trim((string) setting('indexnow_key', ''));
    if (!preg_match('/^[a-f0-9]{16,}$/', $key)) {
        $key = bin2hex(random_bytes(16)); // 32 hex characters
        settings_save(['indexnow_key' => $key]);
    }
    return $key;
}

/**
 * Secret key that authenticates the automated content-publishing endpoint
 * (POST /api/publish-blog). Generated once and stored; shown in the admin so
 * the owner can copy it into the scheduled automation. An env override lets a
 * host rotate it without touching the database.
 */
function content_api_key(): string
{
    $env = getenv('CONTENT_API_KEY');
    if (is_string($env) && preg_match('/^[a-f0-9]{24,}$/', $env)) {
        return $env;
    }
    $key = trim((string) setting('content_api_key', ''));
    if (!preg_match('/^[a-f0-9]{24,}$/', $key)) {
        $key = bin2hex(random_bytes(20)); // 40 hex characters
        settings_save(['content_api_key' => $key]);
    }
    return $key;
}

/**
 * Submit absolute URLs to IndexNow. Best-effort and non-blocking: any failure
 * here is swallowed so it can never affect saving content in the admin panel.
 */
function indexnow_submit(array $urls): void
{
    $urls = array_values(array_filter(array_unique($urls)));
    if (!$urls || !function_exists('curl_init')) {
        return;
    }
    // Only ping for a real public domain — never for localhost during development.
    $host = parse_url(SITE_URL, PHP_URL_HOST) ?: '';
    if ($host === '' || $host === 'localhost' || str_starts_with($host, '127.') || !str_contains($host, '.')) {
        return;
    }
    $key = indexnow_key();
    $payload = json_encode([
        'host'        => $host,
        'key'         => $key,
        'keyLocation' => SITE_URL . '/' . $key . '.txt',
        'urlList'     => array_slice($urls, 0, 100),
    ], JSON_UNESCAPED_SLASHES);

    try {
        $ch = curl_init('https://api.indexnow.org/indexnow');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json; charset=utf-8'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 4,
            CURLOPT_CONNECTTIMEOUT => 3,
        ]);
        curl_exec($ch);
        curl_close($ch);
    } catch (Throwable $e) {
        // Notification is optional; never surface a failure to the user.
    }
}

/** Notify search engines that a site-relative path was published or updated. */
function notify_search_engines(string $path): void
{
    indexnow_submit([SITE_URL . '/' . ltrim($path, '/')]);
}

/**
 * Thin, boilerplate CMS pages that are deliberately kept out of the index
 * (noindex, follow) so a new domain's limited crawl budget concentrates on the
 * pages that can actually rank. They stay live and crawlable; only search
 * indexing is suppressed. Privacy, Terms, Refund and the quality policy are
 * intentionally NOT here — those are worth indexing.
 */
function default_noindex_slugs(): array
{
    return ['delivery-policy', 'damage-policy', 'dispute-resolution', 'distributor-terms', 'cookie-policy', 'disclaimer'];
}

/** Whether a CMS page should be indexed, honouring both its DB flag and the default list. */
function page_is_indexable(string $slug, int $dbNoindex): bool
{
    return $dbNoindex !== 1 && !in_array($slug, default_noindex_slugs(), true);
}

/**
 * Every public, indexable URL on the site (the same set the XML sitemap
 * publishes: static routes, products, blog posts and indexable CMS pages),
 * as absolute URLs.
 */
function all_indexable_urls(): array
{
    $paths = [
        '', 'products', 'order', 'purification-process', 'minerals-and-benefits',
        'about', 'contact', 'coverage-areas', 'distribution', 'distributor-application',
        'custom-label-bottles', 'custom-label-request', 'gallery', 'documents', 'blog',
        'faqs', 'reviews', 'careers', 'bulk-water-calculator', 'sitemap',
    ];
    foreach (fetch_all('SELECT slug FROM products WHERE status = "published"') as $r) {
        $paths[] = 'product/' . $r['slug'];
    }
    foreach (fetch_all('SELECT slug FROM blog_posts WHERE status = "published"') as $r) {
        $paths[] = 'blog/' . $r['slug'];
    }
    foreach (fetch_all('SELECT slug FROM pages WHERE status = "published" AND noindex = 0') as $r) {
        if (!in_array($r['slug'], default_noindex_slugs(), true)) {
            $paths[] = $r['slug'];
        }
    }
    return array_values(array_unique(array_map(
        fn($p) => SITE_URL . '/' . ltrim($p, '/'),
        $paths
    )));
}

/** Submit every indexable URL to IndexNow at once. Returns the number sent. */
function indexnow_submit_all(): int
{
    $urls = all_indexable_urls();
    foreach (array_chunk($urls, 100) as $chunk) {
        indexnow_submit($chunk);
    }
    return count($urls);
}
