<?php
/** Photo gallery page. */
declare(strict_types=1);

$items = fetch_all('SELECT * FROM gallery WHERE is_active = 1 ORDER BY sort_order ASC, id DESC');
$cats  = array_values(array_unique(array_map(fn($g) => $g['category'], $items)));

seo_set([
    'title'       => 'Photo Gallery',
    'description' => 'Photographs from inside the Pak-Everests water plant at Gujar Khan: the RO bank, bottle washing, filling line, laboratory and delivery fleet.',
    'keywords'    => 'water plant photos, mineral water plant gallery, Pak-Everests gallery, water bottling plant Pakistan',
    'breadcrumbs' => ['Photo Gallery' => '/gallery'],
]);
if ($items) {
    seo_add_schema([
        '@context' => 'https://schema.org',
        '@type'    => 'ImageGallery',
        'name'     => 'Pak-Everests Photo Gallery',
        'url'      => SITE_URL . '/gallery',
        'image'    => array_map(fn($g) => abs_url(media_url($g['image_path'], 'photo')), array_slice($items, 0, 12)),
    ]);
}

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Photo Gallery';
$heroSubtitle = 'Inside the plant, on the road, and at our customers premises.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container">
    <?php if (!$items): ?>
      <div class="empty-state">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2zM8.5 13.5l2.5 3 3.5-4.5 4.5 6H5l3.5-4.5z"/></svg>
        <p>Gallery photographs are being uploaded. Please check back shortly.</p>
      </div>
    <?php else: ?>
      <?php if (count($cats) > 1): ?>
      <div class="filter-bar">
        <button type="button" class="filter-chip is-active" data-filter="all" data-target=".gallery-grid">All</button>
        <?php foreach ($cats as $c): ?>
        <button type="button" class="filter-chip" data-filter="<?= e($c) ?>" data-target=".gallery-grid"><?= e($c) ?></button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="gallery-grid">
        <?php foreach ($items as $g): ?>
        <a class="gallery-item reveal" data-category="<?= e($g['category']) ?>"
           href="<?= e(media_url($g['image_path'], 'photo')) ?>"
           data-lightbox="<?= e(media_url($g['image_path'], 'photo')) ?>"
           data-caption="<?= e($g['title'] . ($g['caption'] ? ' — ' . $g['caption'] : '')) ?>">
          <img src="<?= e(media_url($g['image_path'], 'photo')) ?>" alt="<?= e($g['alt_text'] ?: $g['title']) ?>" loading="lazy" width="400" height="300">
          <span class="gallery-caption"><?= e($g['title']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="cta-band">
      <h2>Prefer to see it in person?</h2>
      <p>We are happy to show the purification line, bottle washing station and laboratory to any customer who asks.</p>
      <div class="cta-actions">
        <a class="btn btn-lg btn-whatsapp" href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests, I would like to visit the plant.')) ?>" target="_blank" rel="noopener">Request a Plant Visit</a>
        <a class="btn btn-lg btn-light" href="<?= e(url('purification-process')) ?>">Read the 8 Stage Process</a>
      </div>
    </div>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
