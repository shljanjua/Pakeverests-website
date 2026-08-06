<?php
/** Single product page. $param holds the slug. */
declare(strict_types=1);

$slug    = (string) ($param ?? '');
$product = $slug !== '' ? fetch_one('SELECT * FROM products WHERE slug = ? AND status = "published"', [$slug]) : null;

if (!$product) {
    http_response_code(404);
    require PE_ROOT . '/app/views/404.php';
    return;
}

$images   = fetch_all('SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC', [(int) $product['id']]);
$reviews  = fetch_all('SELECT * FROM reviews WHERE product_id = ? AND status = "approved" ORDER BY created_at DESC LIMIT 8', [(int) $product['id']]);
$related  = fetch_all('SELECT * FROM products WHERE status = "published" AND id <> ? ORDER BY RAND() LIMIT 3', [(int) $product['id']]);
$features = array_values(array_filter(array_map('trim', explode("\n", (string) $product['features']))));
$specs    = array_values(array_filter(array_map('trim', explode("\n", (string) $product['specifications']))));
$soon     = (int) $product['is_coming_soon'] === 1;

$reviewAvg   = (float) ($product['rating'] ?: 5);
$reviewCount = max((int) $product['rating_count'], count($reviews));

seo_set([
    'title'       => (string) ($product['meta_title'] ?: $product['name']),
    'description' => (string) ($product['meta_description'] ?: excerpt($product['short_description'], 165)),
    'keywords'    => (string) $product['meta_keywords'],
    'og_type'     => 'product',
    'image'       => media_url($product['main_image'], 'product'),
    'breadcrumbs' => ['Products' => '/products', $product['name'] => '/product/' . $product['slug']],
]);

$offer = [
    '@type'         => 'Offer',
    'url'           => SITE_URL . '/product/' . $product['slug'],
    'priceCurrency' => 'PKR',
    'price'         => (float) $product['price'],
    'availability'  => $soon ? 'https://schema.org/PreOrder' : 'https://schema.org/InStock',
    'itemCondition' => 'https://schema.org/NewCondition',
    'seller'        => ['@id' => SITE_URL . '/#organization'],
    'priceValidUntil' => date('Y-12-31'),
    // Free delivery across the coverage area — always declared so Merchant
    // listings has the shipping information it expects.
    'shippingDetails' => [
        '@type' => 'OfferShippingDetails',
        'shippingRate' => ['@type' => 'MonetaryAmount', 'value' => 0, 'currency' => 'PKR'],
        'shippingDestination' => ['@type' => 'DefinedRegion', 'addressCountry' => 'PK'],
        'deliveryTime' => [
            '@type' => 'ShippingDeliveryTime',
            'handlingTime' => ['@type' => 'QuantitativeValue', 'minValue' => 0, 'maxValue' => 1, 'unitCode' => 'DAY'],
            'transitTime'  => ['@type' => 'QuantitativeValue', 'minValue' => 0, 'maxValue' => 1, 'unitCode' => 'DAY'],
        ],
    ],
    // Bottled drinking water is a consumable, so purchase returns are not
    // offered (the 19L bottle deposit is refunded separately on bottle return).
    'hasMerchantReturnPolicy' => [
        '@type' => 'MerchantReturnPolicy',
        'applicableCountry' => 'PK',
        'returnPolicyCategory' => 'https://schema.org/MerchantReturnNotPermitted',
    ],
];

$productSchema = [
    '@context'    => 'https://schema.org',
    '@type'       => 'Product',
    'name'        => $product['name'],
    'image'       => [schema_image_url($product['main_image'])],
    'description' => excerpt($product['short_description'], 300),
    'sku'         => $product['sku'],
    'brand'       => ['@type' => 'Brand', 'name' => 'Pak-Everests'],
    'offers'      => $offer,
];
if ($reviewCount > 0) {
    $productSchema['aggregateRating'] = [
        '@type'       => 'AggregateRating',
        'ratingValue' => number_format($reviewAvg, 1),
        'reviewCount' => $reviewCount,
        'bestRating'  => 5,
        'worstRating' => 1,
    ];
}
if ($reviews) {
    $productSchema['review'] = array_map(fn($r) => [
        '@type'         => 'Review',
        'author'        => ['@type' => 'Person', 'name' => $r['reviewer_name']],
        'datePublished' => date('Y-m-d', strtotime($r['created_at'])),
        'reviewBody'    => $r['body'],
        'name'          => $r['title'],
        'reviewRating'  => ['@type' => 'Rating', 'ratingValue' => (int) $r['rating'], 'bestRating' => 5, 'worstRating' => 1],
    ], array_slice($reviews, 0, 5));
}
seo_add_schema($productSchema);

require PE_ROOT . '/app/partials/header.php';
?>

<section class="page-hero">
  <div class="container page-hero-inner">
    <?= breadcrumbs_html() ?>
    <h1><?= e($product['name']) ?></h1>
    <?php if ($product['tagline']): ?><p><?= e($product['tagline']) ?></p><?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid-2" style="gap:40px;align-items:start;">

      <div>
        <div class="product-media" style="border-radius:var(--r-lg);border:1px solid var(--border);aspect-ratio:1;">
          <img src="<?= e(media_url($product['main_image'], 'product')) ?>" alt="<?= e($product['name']) ?>" width="600" height="600" fetchpriority="high" decoding="async">
        </div>
        <?php if ($images): ?>
        <div class="gallery-grid" style="grid-template-columns:repeat(4,1fr);margin-top:14px;">
          <?php foreach ($images as $img): ?>
          <a class="gallery-item" style="aspect-ratio:1;" href="<?= e(media_url($img['image_path'], 'product')) ?>"
             data-lightbox="<?= e(media_url($img['image_path'], 'product')) ?>" data-caption="<?= e($img['alt_text'] ?: $product['name']) ?>">
            <img src="<?= e(media_url($img['image_path'], 'product')) ?>" alt="<?= e($img['alt_text'] ?: $product['name']) ?>" loading="lazy">
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <div>
        <div class="product-meta" style="margin-bottom:14px;">
          <?php if ($reviewCount > 0): ?>
            <?= stars($reviewAvg, true) ?>
            <span>(<?= (int) $reviewCount ?> review<?= $reviewCount === 1 ? '' : 's' ?>)</span>
          <?php else: ?>
            <a href="<?= e(url('reviews?product=' . (int) $product['id'])) ?>">Be the first to review this product</a>
          <?php endif; ?>
          <?php if ($soon): ?><span class="badge badge-soon">Coming soon</span>
          <?php elseif ($product['stock_status'] === 'on_demand'): ?><span class="badge badge-warning">On demand</span>
          <?php else: ?><span class="badge badge-success">In stock</span><?php endif; ?>
        </div>

        <div class="card" style="margin-bottom:22px;">
          <?php if ($soon || (float) $product['price'] <= 0): ?>
            <h2 style="margin:0;font-size:1.4rem;">Pricing to be announced</h2>
            <p style="margin-top:8px;">Register your interest and we will notify you with launch pricing the day this product goes on sale.</p>
          <?php else: ?>
            <div class="product-price">
              <span class="amount" style="font-size:2.4rem;"><?= money($product['price']) ?></span>
              <span class="unit"><?= e($product['price_unit']) ?></span>
            </div>
            <?php if ((float) $product['security_deposit'] > 0): ?>
              <p class="deposit-note" style="margin-top:12px;">
                Plus a one time <strong><?= money($product['security_deposit']) ?></strong> refundable security deposit per bottle.
                Fully refunded when the bottle is returned in a usable condition.
              </p>
            <?php endif; ?>
            <?php if ($product['rent_price'] !== null && (float) $product['rent_price'] > 0): ?>
              <p class="deposit-note" style="margin-top:12px;">
                Rental option: <strong><?= money($product['rent_price']) ?></strong> <?= e((string) $product['rent_unit']) ?>, servicing included.
              </p>
            <?php endif; ?>
            <?php if ((int) $product['free_delivery'] === 1): ?>
              <p style="margin-top:12px;color:var(--success);font-weight:700;">Free delivery across the full coverage area</p>
            <?php endif; ?>
          <?php endif; ?>
        </div>

        <p style="font-size:1.05rem;color:var(--text-soft);"><?= rich_text($product['short_description']) ?></p>

        <?php if ($features): ?>
        <h2 style="font-size:1.2rem;margin-top:26px;">Key features</h2>
        <ul>
          <?php foreach ($features as $f): ?><li><?= e($f) ?></li><?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <div class="hero-actions" style="margin-top:26px;">
          <?php if ($soon): ?>
            <a class="btn btn-primary btn-lg" href="<?= e(url('contact?subject=' . rawurlencode('Notify me: ' . $product['name']))) ?>">Register Interest</a>
          <?php else: ?>
            <a class="btn btn-primary btn-lg" href="<?= e(url('order?product=' . $product['slug'])) ?>">Order This Product</a>
          <?php endif; ?>
          <a class="btn btn-whatsapp btn-lg" target="_blank" rel="noopener"
             href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests, I am interested in: ' . $product['name'] . ' (' . SITE_URL . '/product/' . $product['slug'] . ')')) ?>">
            Ask on WhatsApp
          </a>
        </div>

        <?php if ($product['best_for']): ?>
        <p class="form-hint" style="margin-top:18px;"><strong>Best for:</strong> <?= e($product['best_for']) ?></p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?= ad_slot('content_top') ?>

<section class="section section-soft">
  <div class="container">
    <div class="grid" style="grid-template-columns:minmax(0,2fr) minmax(260px,1fr);gap:34px;align-items:start;">
      <div class="prose">
        <?= rich_text($product['long_description']) ?>
      </div>

      <aside>
        <?php if ($specs): ?>
        <div class="card" style="margin-bottom:22px;">
          <h2 style="font-size:1.12rem;">Specifications</h2>
          <table>
            <tbody>
            <?php foreach ($specs as $row):
              $parts = explode(':', $row, 2); ?>
              <tr>
                <th scope="row" style="width:48%;"><?= e(trim($parts[0])) ?></th>
                <td><?= e(trim($parts[1] ?? '')) ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>

        <div class="card">
          <h2 style="font-size:1.12rem;">Order or ask a question</h2>
          <p style="font-size:.92rem;">Our team confirms every order by phone or WhatsApp before dispatch.</p>
          <div style="display:grid;gap:10px;">
            <a class="btn btn-primary btn-block" href="<?= e(url('order?product=' . $product['slug'])) ?>">Order Online</a>
            <a class="btn btn-whatsapp btn-block" href="<?= e(wa_link(primary_whatsapp(), 'Hello, I want to order: ' . $product['name'])) ?>" target="_blank" rel="noopener">WhatsApp Us</a>
            <a class="btn btn-outline btn-block" href="<?= e(url('coverage-areas')) ?>">Check Delivery Area</a>
          </div>
        </div>
      </aside>
    </div>
  </div>
</section>

<?php if ($reviews): ?>
<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Verified Feedback</span>
      <h2>Reviews for <?= e($product['short_name'] ?: $product['name']) ?></h2>
      <p><?= stars($reviewAvg, true) ?> from <?= (int) $reviewCount ?> customers.</p>
    </div>
    <div class="grid grid-3">
      <?php foreach ($reviews as $r): ?>
      <article class="card review-card reveal">
        <div class="review-head">
          <div class="review-avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($r['reviewer_name'], 0, 1))) ?></div>
          <div>
            <div class="review-name"><?= e($r['reviewer_name']) ?></div>
            <div class="review-role"><?= e($r['reviewer_role']) ?><?= $r['location'] ? ' &middot; ' . e($r['location']) : '' ?></div>
          </div>
        </div>
        <?= stars((float) $r['rating']) ?>
        <?php if ($r['title']): ?><h3 style="font-size:1rem;margin:0;"><?= e($r['title']) ?></h3><?php endif; ?>
        <p class="review-body"><?= e($r['body']) ?></p>
      </article>
      <?php endforeach; ?>
    </div>
    <p style="text-align:center;margin-top:30px;">
      <a class="btn btn-outline" href="<?= e(url('reviews?product=' . $product['id'])) ?>">Write a Review</a>
    </p>
  </div>
</section>
<?php endif; ?>

<?php if ($related): ?>
<section class="section section-soft">
  <div class="container">
    <div class="section-head"><h2>You may also need</h2></div>
    <div class="grid grid-3">
      <?php foreach ($related as $p): require PE_ROOT . '/app/partials/product-card.php'; endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
