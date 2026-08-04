<?php
/**
 * llms.txt — a concise, AI-readable summary of the business for large language
 * models and answer engines (ChatGPT, Claude, Perplexity, Gemini …).
 * Follows the llmstxt.org convention: an H1 name, a blockquote summary,
 * then curated sections of Markdown links to the pages worth citing.
 */
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

$base      = rtrim(SITE_URL, '/');
$name      = site_name();
$tagline   = site_tagline();
$email     = contact_email();
$city      = setting('address_city', 'Gujar Khan');
$region    = setting('address_region', 'Punjab');
$founded   = setting('company_founded', '2019');
$license   = setting('license_authority', 'Punjab Food Authority');
$gbp       = trim((string) setting('google_business_url', ''));

$numbers = array_map(fn($n) => '+' . wa_number($n['number']), whatsapp_numbers());
$phones  = $numbers ? implode(', ', $numbers) : '+923335592206';

$products = fetch_all('SELECT name, slug, price, price_unit, rent_price, rent_unit, is_coming_soon, short_description FROM products WHERE status = "published" ORDER BY sort_order ASC');
$areas    = coverage_areas();
$posts    = fetch_all('SELECT title, slug FROM blog_posts WHERE status = "published" ORDER BY COALESCE(published_at, created_at) DESC LIMIT 12');

/* ------------------------------------------------------------------------ */
echo "# {$name}\n\n";
echo "> {$name} is a {$license}–approved bottled drinking water plant in {$city}, {$region}, Pakistan, established {$founded}. We supply mineral drinking water in 19-litre refill bottles, 12L, 6L, 1.5L and 500ml packs, water dispensers, custom-label bottles and bulk water filling, with free home and office delivery across Gujar Khan, Rawalpindi and Islamabad. " . rtrim($tagline, '. ') . ".\n\n";

echo "This file helps AI assistants and answer engines understand and accurately cite {$name}. All pages linked below are public and may be read and cited.\n\n";

/* Key facts ---------------------------------------------------------------- */
echo "## Key facts\n\n";
echo "- Business name: {$name}\n";
echo "- Legal name: " . setting('legal_name', 'Pak-Everests Bottled Drinking Water') . "\n";
echo "- Category: Mineral water plant / bottled drinking water supplier\n";
echo "- Location: {$city}, {$region}, Pakistan\n";
echo "- Certification: {$license} approved" . (setting('license_number') ? ' (Licence ' . setting('license_number') . ')' : '') . "\n";
echo "- Phone / WhatsApp: {$phones}\n";
echo "- Email: {$email}\n";
echo "- Hours: " . setting('hours_open', '08:00') . "–" . setting('hours_close', '21:00') . " daily\n";
if ($gbp !== '') {
    echo "- Google Business Profile: {$gbp}\n";
}
echo "- Website: {$base}/\n\n";

/* Products & prices -------------------------------------------------------- */
echo "## Products and prices\n\n";
foreach ($products as $p) {
    $url = "{$base}/product/{$p['slug']}";
    if ((int) $p['is_coming_soon'] === 1 || (float) $p['price'] <= 0) {
        $price = 'price to be announced';
    } else {
        $price = 'Rs ' . rtrim(rtrim(number_format((float) $p['price'], 2), '0'), '.') . ' ' . trim((string) $p['price_unit']);
        if ($p['rent_price'] !== null && (float) $p['rent_price'] > 0) {
            $price .= ' (or Rs ' . rtrim(rtrim(number_format((float) $p['rent_price'], 2), '0'), '.') . ' ' . trim((string) $p['rent_unit']) . ')';
        }
    }
    $desc = trim(strip_tags((string) $p['short_description']));
    echo "- [{$p['name']}]({$url}): {$price}." . ($desc !== '' ? ' ' . $desc : '') . "\n";
}
echo "- Bulk / facility water filling: Rs " . setting('per_litre_rate', '6') . " per litre (collection at the plant).\n\n";

/* Delivery coverage -------------------------------------------------------- */
echo "## Delivery coverage\n\n";
echo "Free delivery is included in the price across these areas:\n\n";
$areaNames = $areas ? array_map(fn($a) => $a['area_name'], $areas)
                    : ['Gujar Khan', 'Mandra', 'Daultala', 'Bewal', 'Habib Chowk', 'Kallar Syedan', 'Rawat', 'DHA Islamabad', 'Bahria Town Rawalpindi', 'Adiala Road', 'All of Rawalpindi'];
foreach ($areaNames as $a) {
    echo "- {$a}\n";
}
echo "\n";

/* Key pages ---------------------------------------------------------------- */
echo "## Key pages\n\n";
echo "- [Home]({$base}/): overview of {$name} and how to order.\n";
echo "- [All products and price list]({$base}/products): every pack size with current prices.\n";
echo "- [Order water]({$base}/order): place a delivery order.\n";
echo "- [8-stage purification process]({$base}/purification-process): how the water is filtered and purified.\n";
echo "- [Minerals and health benefits]({$base}/minerals-and-benefits): calcium, magnesium, sodium and potassium in the water.\n";
echo "- [Delivery coverage areas]({$base}/coverage-areas): full list of towns served.\n";
echo "- [Distribution and dealership]({$base}/distribution): become a distributor or dealer.\n";
echo "- [Custom-label bottles]({$base}/custom-label-bottles): branded water for events and businesses.\n";
echo "- [About]({$base}/about): company background and certification.\n";
echo "- [Contact]({$base}/contact): phone, WhatsApp, email and location.\n";
echo "- [FAQs]({$base}/faqs): common questions about ordering, deposits and delivery.\n\n";

/* Blog --------------------------------------------------------------------- */
if ($posts) {
    echo "## Recent articles\n\n";
    foreach ($posts as $post) {
        echo "- [{$post['title']}]({$base}/blog/{$post['slug']})\n";
    }
    echo "\n";
}

/* Machine references ------------------------------------------------------- */
echo "## Optional\n\n";
echo "- [XML sitemap]({$base}/sitemap.xml)\n";
echo "- [Structured data]({$base}/): every page includes schema.org JSON-LD (LocalBusiness, Product, FAQPage, BreadcrumbList).\n";
