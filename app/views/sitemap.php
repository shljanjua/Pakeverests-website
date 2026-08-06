<?php
/** Dynamic XML sitemap. */
declare(strict_types=1);

header('Content-Type: application/xml; charset=utf-8');

$urls = [];
$add = function (string $loc, string $lastmod = '', string $freq = 'weekly', string $priority = '0.7') use (&$urls) {
    $urls[] = [
        'loc'      => SITE_URL . '/' . ltrim($loc, '/'),
        'lastmod'  => $lastmod ? date('Y-m-d', strtotime($lastmod)) : date('Y-m-d'),
        'freq'     => $freq,
        'priority' => $priority,
    ];
};

/* Static routes */
$add('', '', 'daily', '1.0');
$add('products', '', 'weekly', '0.9');
$add('order', '', 'monthly', '0.9');
$add('purification-process', '', 'monthly', '0.8');
$add('minerals-and-benefits', '', 'monthly', '0.8');
$add('about', '', 'monthly', '0.7');
$add('contact', '', 'monthly', '0.8');
$add('coverage-areas', '', 'weekly', '0.8');
$add('distribution', '', 'monthly', '0.8');
$add('distributor-application', '', 'monthly', '0.7');
$add('custom-label-bottles', '', 'monthly', '0.8');
$add('custom-label-request', '', 'monthly', '0.7');
$add('gallery', '', 'weekly', '0.6');
$add('documents', '', 'monthly', '0.6');
$add('blog', '', 'daily', '0.8');
$add('faqs', '', 'monthly', '0.7');
$add('reviews', '', 'weekly', '0.7');
$add('careers', '', 'weekly', '0.6');
$add('bulk-water-calculator', '', 'monthly', '0.7');
$add('sitemap', '', 'weekly', '0.5');

/* Products */
foreach (fetch_all('SELECT slug, updated_at, created_at FROM products WHERE status = "published"') as $row) {
    $add('product/' . $row['slug'], (string) ($row['updated_at'] ?: $row['created_at']), 'weekly', '0.9');
}

/* Blog posts */
foreach (fetch_all('SELECT slug, updated_at, published_at, created_at FROM blog_posts WHERE status = "published"') as $row) {
    $add('blog/' . $row['slug'], (string) ($row['updated_at'] ?: $row['published_at'] ?: $row['created_at']), 'monthly', '0.7');
}

/* CMS pages — excluding thin boilerplate pages we deliberately keep out of the index */
foreach (fetch_all('SELECT slug, updated_at, created_at FROM pages WHERE status = "published" AND noindex = 0') as $row) {
    if (in_array($row['slug'], default_noindex_slugs(), true)) {
        continue;
    }
    $add($row['slug'], (string) ($row['updated_at'] ?: $row['created_at']), 'yearly', '0.4');
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $u): ?>
  <url>
    <loc><?= htmlspecialchars($u['loc'], ENT_XML1) ?></loc>
    <lastmod><?= $u['lastmod'] ?></lastmod>
    <changefreq><?= $u['freq'] ?></changefreq>
    <priority><?= $u['priority'] ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
