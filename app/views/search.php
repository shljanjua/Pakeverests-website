<?php
/** Simple site search across products, pages and blog posts. */
declare(strict_types=1);

$term = get('q');
$products = $pages = $posts = [];

if ($term !== '') {
    $like = '%' . $term . '%';
    $products = fetch_all('SELECT * FROM products WHERE status = "published" AND (name LIKE ? OR short_description LIKE ? OR meta_keywords LIKE ?) ORDER BY sort_order ASC LIMIT 8', [$like, $like, $like]);
    $posts    = fetch_all('SELECT * FROM blog_posts WHERE status = "published" AND (title LIKE ? OR excerpt LIKE ? OR content LIKE ?) ORDER BY published_at DESC LIMIT 8', [$like, $like, $like]);
    $pages    = fetch_all('SELECT title, slug, content FROM pages WHERE status = "published" AND (title LIKE ? OR content LIKE ?) ORDER BY title ASC LIMIT 8', [$like, $like]);
}

seo_set([
    'title'       => $term !== '' ? 'Search results for ' . $term : 'Search',
    'description' => 'Search Pak-Everests products, policies and articles.',
    'robots'      => 'noindex, follow',
    'breadcrumbs' => ['Search' => '/search'],
]);

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Search';
$heroSubtitle = 'Find a product, a policy or an article.';
require PE_ROOT . '/app/partials/page-hero.php';
?>
<section class="section">
  <div class="container">
    <form method="get" action="<?= e(url('search')) ?>" style="display:flex;gap:10px;flex-wrap:wrap;justify-content:center;margin-bottom:34px;">
      <label class="sr-only" for="q">Search</label>
      <input type="search" id="q" name="q" value="<?= e($term) ?>" placeholder="Search products, policies and articles" style="max-width:420px;" autofocus>
      <button class="btn btn-primary" type="submit">Search</button>
    </form>

    <?php if ($term === ''): ?>
      <p style="text-align:center;color:var(--text-muted);">Enter a search term above.</p>
    <?php elseif (!$products && !$posts && !$pages): ?>
      <div class="empty-state"><p>Nothing found for "<?= e($term) ?>". Try a shorter or different term.</p></div>
    <?php else: ?>

      <?php if ($products): ?>
      <h2>Products</h2>
      <div class="grid grid-3" style="margin-bottom:38px;">
        <?php foreach ($products as $p): require PE_ROOT . '/app/partials/product-card.php'; endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if ($posts): ?>
      <h2>Articles</h2>
      <div class="grid grid-3" style="margin-bottom:38px;">
        <?php foreach ($posts as $post): require PE_ROOT . '/app/partials/post-card.php'; endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if ($pages): ?>
      <h2>Pages and policies</h2>
      <div class="grid grid-2">
        <?php foreach ($pages as $pg): ?>
        <a class="card card-hover" href="<?= e(url($pg['slug'])) ?>" style="text-decoration:none;">
          <h3 style="font-size:1.05rem;"><?= e($pg['title']) ?></h3>
          <p style="font-size:.9rem;"><?= e(excerpt($pg['content'], 140)) ?></p>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
<?php require PE_ROOT . '/app/partials/footer.php'; ?>
