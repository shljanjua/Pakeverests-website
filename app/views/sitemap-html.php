<?php
/**
 * Human and crawler friendly HTML sitemap. Every published URL is linked here
 * from a single page that itself sits in the footer of the already-indexed
 * home page, so Googlebot can reach every page in one hop. This is the main
 * on-site lever for pages stuck in "Discovered - currently not indexed".
 */
declare(strict_types=1);

seo_set([
    'title'       => 'Sitemap',
    'description' => 'A complete list of every page on the Pak-Everests website: products and prices, guides, blog articles, coverage areas and company information.',
    'keywords'    => 'Pak-Everests sitemap, all pages, water plant Gujar Khan',
    'breadcrumbs' => ['Sitemap' => '/sitemap'],
]);

$products   = fetch_all('SELECT slug, name FROM products WHERE status = "published" ORDER BY sort_order ASC');
$posts      = fetch_all('SELECT slug, title FROM blog_posts WHERE status = "published" ORDER BY COALESCE(published_at, created_at) DESC');
$legalPages = nav_pages('legal');

/* Core, company and service pages, grouped for readers and crawlers alike. */
$mainPages = [
    'Home'                        => '/',
    'All Products & Price List'   => '/products',
    'Order Water Online'          => '/order',
    'Delivery Coverage Areas'     => '/coverage-areas',
    'Bulk Water Calculator'       => '/bulk-water-calculator',
];
$companyPages = [
    'About Pak-Everests'          => '/about',
    '8-Stage Purification Process'=> '/purification-process',
    'Minerals & Health Benefits'  => '/minerals-and-benefits',
    'Photo Gallery'               => '/gallery',
    'Certificates & Documents'    => '/documents',
    'Careers'                     => '/careers',
    'Customer Reviews'            => '/reviews',
    'Contact Us'                  => '/contact',
    'FAQs'                        => '/faqs',
];
$servicePages = [
    'Distribution & Dealership'   => '/distribution',
    'Distributor Application'     => '/distributor-application',
    'Custom Label Bottles'        => '/custom-label-bottles',
    'Custom Label Request'        => '/custom-label-request',
];

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Sitemap';
$heroSubtitle = 'Every page on the Pak-Everests website in one place.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container">
    <div class="grid grid-2" style="gap:32px;align-items:start;">

      <div class="card">
        <h2>Main pages</h2>
        <ul class="link-list">
          <?php foreach ($mainPages as $label => $href): ?>
          <li><a href="<?= e(url($href)) ?>"><?= e($label) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="card">
        <h2>Products</h2>
        <ul class="link-list">
          <?php foreach ($products as $p): ?>
          <li><a href="<?= e(url('product/' . $p['slug'])) ?>"><?= e($p['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="card">
        <h2>Company</h2>
        <ul class="link-list">
          <?php foreach ($companyPages as $label => $href): ?>
          <li><a href="<?= e(url($href)) ?>"><?= e($label) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="card">
        <h2>Distribution &amp; custom labels</h2>
        <ul class="link-list">
          <?php foreach ($servicePages as $label => $href): ?>
          <li><a href="<?= e(url($href)) ?>"><?= e($label) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <?php if ($posts): ?>
      <div class="card">
        <h2>Blog &amp; guides</h2>
        <ul class="link-list">
          <?php foreach ($posts as $post): ?>
          <li><a href="<?= e(url('blog/' . $post['slug'])) ?>"><?= e($post['title']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>

      <?php if ($legalPages): ?>
      <div class="card">
        <h2>Legal &amp; policies</h2>
        <ul class="link-list">
          <?php foreach ($legalPages as $lp): ?>
          <li><a href="<?= e(url($lp['slug'])) ?>"><?= e($lp['title']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>

    </div>

    <p class="form-hint" style="margin-top:26px;">
      Looking for the machine-readable version? The <a href="<?= e(url('sitemap.xml')) ?>">XML sitemap</a> is here.
    </p>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
