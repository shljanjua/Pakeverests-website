<?php
/** Blog listing and single post. $param holds an optional slug. */
declare(strict_types=1);

$slug = (string) ($param ?? '');

/* ------------------------------------------------------------------ single */
if ($slug !== '') {
    $post = fetch_one('SELECT * FROM blog_posts WHERE slug = ? AND status = "published"', [$slug]);
    if (!$post) {
        http_response_code(404);
        require PE_ROOT . '/app/views/404.php';
        return;
    }

    q('UPDATE blog_posts SET views = views + 1 WHERE id = ?', [(int) $post['id']]);

    $related = fetch_all(
        'SELECT * FROM blog_posts WHERE status = "published" AND id <> ? AND category = ? ORDER BY published_at DESC LIMIT 3',
        [(int) $post['id'], $post['category']]
    );
    if (count($related) < 3) {
        $related = fetch_all('SELECT * FROM blog_posts WHERE status = "published" AND id <> ? ORDER BY published_at DESC LIMIT 3', [(int) $post['id']]);
    }

    seo_set([
        'title'        => (string) ($post['meta_title'] ?: $post['title']),
        'description'  => (string) ($post['meta_description'] ?: excerpt($post['excerpt'] ?: $post['content'], 165)),
        'keywords'     => (string) $post['meta_keywords'],
        'og_type'      => 'article',
        'image'        => media_url($post['cover_image'], 'photo'),
        'published_at' => $post['published_at'],
        'modified_at'  => $post['updated_at'] ?: $post['published_at'],
        'breadcrumbs'  => ['Blog' => '/blog', $post['title'] => '/blog/' . $post['slug']],
    ]);
    seo_add_schema([
        '@context'         => 'https://schema.org',
        '@type'            => 'BlogPosting',
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => SITE_URL . '/blog/' . $post['slug']],
        'headline'         => $post['title'],
        'description'      => excerpt($post['excerpt'] ?: $post['content'], 200),
        'image'            => [abs_url(media_url($post['cover_image'], 'photo'))],
        'datePublished'    => date('c', strtotime((string) ($post['published_at'] ?: $post['created_at']))),
        'dateModified'     => date('c', strtotime((string) ($post['updated_at'] ?: $post['published_at'] ?: $post['created_at']))),
        'author'           => ['@type' => 'Organization', 'name' => $post['author'], 'url' => SITE_URL],
        'publisher'        => ['@id' => SITE_URL . '/#organization'],
        'articleSection'   => $post['category'],
        'keywords'         => $post['tags'],
        'wordCount'        => str_word_count(strip_tags((string) $post['content'])),
        'inLanguage'       => 'en-PK',
    ]);

    require PE_ROOT . '/app/partials/header.php';
    ?>
    <section class="page-hero">
      <div class="container page-hero-inner">
        <?= breadcrumbs_html() ?>
        <span class="badge" style="background:rgba(255,255,255,.16);color:#fff;"><?= e($post['category']) ?></span>
        <h1 style="margin-top:14px;"><?= e($post['title']) ?></h1>
        <p style="display:flex;flex-wrap:wrap;gap:18px;font-size:.9rem;">
          <span>By <?= e($post['author']) ?></span>
          <span><?= pretty_date($post['published_at'] ?: $post['created_at']) ?></span>
          <span><?= (int) $post['reading_minutes'] ?> min read</span>
          <span><?= (int) $post['views'] ?> views</span>
        </p>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <div class="grid" style="grid-template-columns:minmax(0,2fr) minmax(260px,1fr);gap:36px;align-items:start;">
          <article>
            <img src="<?= e(media_url($post['cover_image'], 'photo')) ?>" alt="<?= e($post['title']) ?>"
                 style="width:100%;border-radius:var(--r-lg);box-shadow:var(--shadow-sm);margin-bottom:28px;" width="900" height="506">

            <?= ad_slot('content_top') ?>

            <div class="prose">
              <?php if ($post['excerpt']): ?>
                <p style="font-size:1.12rem;font-weight:600;color:var(--text);"><?= e($post['excerpt']) ?></p>
              <?php endif; ?>

              <?php
              $body = rich_text($post['content']);
              /* Drop the first in-content image roughly a third of the way through. */
              if ($post['inline_image_1']) {
                  $chunks = explode('</p>', $body);
                  $at = max(1, (int) floor(count($chunks) / 3));
                  $img = '<figure><img src="' . e(media_url($post['inline_image_1'], 'photo')) . '" alt="' . e($post['title']) . '" loading="lazy"></figure>';
                  array_splice($chunks, $at, 0, [$img]);
                  $body = implode('</p>', $chunks);
              }
              echo $body;
              ?>

              <?php if ($post['inline_image_2']): ?>
                <figure><img src="<?= e(media_url($post['inline_image_2'], 'photo')) ?>" alt="<?= e($post['title']) ?>" loading="lazy"></figure>
              <?php endif; ?>
            </div>

            <?= ad_slot('content_middle') ?>

            <?php if ($post['bottom_image']): ?>
              <img src="<?= e(media_url($post['bottom_image'], 'photo')) ?>" alt="<?= e($post['title']) ?>"
                   style="width:100%;border-radius:var(--r-lg);margin-top:26px;" loading="lazy">
            <?php endif; ?>

            <?php if ($post['tags']): ?>
            <div style="margin-top:28px;display:flex;flex-wrap:wrap;gap:8px;">
              <?php foreach (array_filter(array_map('trim', explode(',', (string) $post['tags']))) as $tag): ?>
                <span class="badge"><?= e($tag) ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="cta-band" style="margin-top:36px;">
              <h2>Order Pak-Everests water today</h2>
              <p>Free delivery across Gujar Khan, Rawalpindi and Islamabad, with no minimum order.</p>
              <div class="cta-actions">
                <a class="btn btn-lg btn-light" href="<?= e(url('order')) ?>">Place an Order</a>
                <a class="btn btn-lg btn-whatsapp" href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests')) ?>" target="_blank" rel="noopener">WhatsApp Us</a>
              </div>
            </div>
          </article>

          <aside>
            <div class="card" style="margin-bottom:22px;">
              <h2 style="font-size:1.1rem;">Quick links</h2>
              <ul style="list-style:none;padding:0;font-size:.92rem;">
                <li><a href="<?= e(url('products')) ?>">Products and price list</a></li>
                <li><a href="<?= e(url('purification-process')) ?>">The 8 stage process</a></li>
                <li><a href="<?= e(url('minerals-and-benefits')) ?>">Minerals and benefits</a></li>
                <li><a href="<?= e(url('coverage-areas')) ?>">Delivery coverage</a></li>
                <li><a href="<?= e(url('bulk-water-calculator')) ?>">Bulk water calculator</a></li>
                <li><a href="<?= e(url('faqs')) ?>">Frequently asked questions</a></li>
              </ul>
            </div>
            <?= ad_slot('sidebar') ?>
            <div class="card">
              <h2 style="font-size:1.1rem;">Need water now?</h2>
              <p style="font-size:.92rem;">Same day delivery on orders placed before 4:00 PM in daily route areas.</p>
              <a class="btn btn-primary btn-block" href="<?= e(url('order')) ?>">Order Online</a>
            </div>
          </aside>
        </div>
      </div>
    </section>

    <?php if ($related): ?>
    <section class="section section-soft">
      <div class="container">
        <div class="section-head"><h2>Related articles</h2></div>
        <div class="grid grid-3">
          <?php foreach ($related as $post): require PE_ROOT . '/app/partials/post-card.php'; endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <?php
    require PE_ROOT . '/app/partials/footer.php';
    return;
}

/* -------------------------------------------------------------------- list */
$search   = get('q');
$category = get('category');
$page     = max(1, (int) get('page', 1));
$perPage  = 9;

$where  = ['status = "published"'];
$params = [];
if ($search !== '') {
    $where[]  = '(title LIKE ? OR excerpt LIKE ? OR content LIKE ?)';
    $like     = '%' . $search . '%';
    $params[] = $like; $params[] = $like; $params[] = $like;
}
if ($category !== '') {
    $where[]  = 'category = ?';
    $params[] = $category;
}
$whereSql = implode(' AND ', $where);

$total = (int) fetch_val("SELECT COUNT(*) FROM blog_posts WHERE $whereSql", $params, 0);
$p     = paginate($total, $perPage, $page);
$posts = fetch_all(
    "SELECT * FROM blog_posts WHERE $whereSql ORDER BY is_featured DESC, published_at DESC LIMIT $perPage OFFSET {$p['offset']}",
    $params
);
$categories = fetch_all('SELECT category, COUNT(*) AS total FROM blog_posts WHERE status = "published" GROUP BY category ORDER BY total DESC');

seo_set([
    'title'       => 'Blog &amp; Water Knowledge',
    'description' => 'Practical guides on choosing a water plant, mineral water versus RO, hydration in the Potohar summer, custom labels and water distribution.',
    'keywords'    => 'water blog Pakistan, mineral water articles, hydration guide, water business blog',
    'breadcrumbs' => ['Blog' => '/blog'],
]);
seo_add_schema([
    '@context' => 'https://schema.org',
    '@type'    => 'Blog',
    'name'     => 'Pak-Everests Blog',
    'url'      => SITE_URL . '/blog',
    'publisher' => ['@id' => SITE_URL . '/#organization'],
]);

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Blog and Water Knowledge';
$heroSubtitle = 'Practical, honest writing about water quality, hydration, branding and the business of supplying water in the Potohar region.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container">
    <form method="get" action="<?= e(url('blog')) ?>" style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center;margin-bottom:30px;">
      <label class="sr-only" for="q">Search articles</label>
      <input type="search" id="q" name="q" value="<?= e($search) ?>" placeholder="Search articles" style="max-width:320px;">
      <button type="submit" class="btn btn-primary">Search</button>
      <?php if ($search !== '' || $category !== ''): ?>
        <a class="btn btn-ghost" href="<?= e(url('blog')) ?>">Clear</a>
      <?php endif; ?>
    </form>

    <?php if ($categories): ?>
    <div class="filter-bar">
      <a class="filter-chip<?= $category === '' ? ' is-active' : '' ?>" href="<?= e(url('blog')) ?>">All articles</a>
      <?php foreach ($categories as $c): ?>
      <a class="filter-chip<?= $category === $c['category'] ? ' is-active' : '' ?>" href="<?= e(url('blog?category=' . rawurlencode($c['category']))) ?>">
        <?= e($c['category']) ?> (<?= (int) $c['total'] ?>)
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!$posts): ?>
      <div class="empty-state">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-2 12H7v-2h10v2zm0-4H7V9h10v2zm-3-4H7V5h7v2z"/></svg>
        <p>No articles found<?= $search !== '' ? ' for "' . e($search) . '"' : '' ?>. Try a different search.</p>
      </div>
    <?php else: ?>
      <div class="grid grid-3">
        <?php foreach ($posts as $post): require PE_ROOT . '/app/partials/post-card.php'; endforeach; ?>
      </div>
      <?= pagination_links($p, url('blog' . ($category !== '' ? '?category=' . rawurlencode($category) : ''))) ?>
    <?php endif; ?>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
