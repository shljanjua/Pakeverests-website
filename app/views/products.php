<?php
/** Products listing page. */
declare(strict_types=1);

$products = fetch_all('SELECT * FROM products WHERE status = "published" ORDER BY sort_order ASC');
$perLitre = (string) setting('per_litre_rate', '6');

seo_set([
    'title'       => 'Water Products &amp; Price List',
    'description' => 'Pak-Everests price list: 19L refill Rs 250, 12L Rs 230, 6L Rs 130, 1.5L and 500ml packs, dispensers, plus bulk filling at Rs ' . $perLitre . ' per litre.',
    'keywords'    => 'mineral water price list Pakistan, 19 liters water bottle price, water bottle rates Rawalpindi, water plant near me',
    'breadcrumbs' => ['Products' => '/products'],
]);
seo_add_schema([
    '@context' => 'https://schema.org',
    '@type'    => 'ItemList',
    'name'     => 'Pak-Everests water products and prices',
    'itemListElement' => array_map(fn($p, $i) => [
        '@type'    => 'ListItem',
        'position' => $i + 1,
        'item'     => [
            '@type'       => 'Product',
            'name'        => $p['name'],
            'url'         => SITE_URL . '/product/' . $p['slug'],
            'image'       => schema_image_url($p['main_image']),
            'description' => excerpt($p['short_description'], 180),
            'brand'       => ['@type' => 'Brand', 'name' => 'Pak-Everests'],
            'offers'      => [
                '@type'         => 'Offer',
                'price'         => (float) $p['price'],
                'priceCurrency' => 'PKR',
                'availability'  => (int) $p['is_coming_soon'] === 1 ? 'https://schema.org/PreOrder' : 'https://schema.org/InStock',
                'url'           => SITE_URL . '/product/' . $p['slug'],
            ],
        ],
    ], $products, array_keys($products)),
]);

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Water Products and Price List';
$heroSubtitle = 'Every pack we produce, with transparent pricing, deposit terms and delivery information. Free delivery applies across the whole coverage area.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container">
    <div class="table-wrap" style="margin-bottom:44px;">
      <table>
        <caption class="sr-only">Pak-Everests price list</caption>
        <thead>
          <tr><th scope="col">Product</th><th scope="col">Pack</th><th scope="col">Price</th><th scope="col">Security deposit</th><th scope="col">Delivery</th><th scope="col">Status</th></tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): ?>
          <tr>
            <th scope="row"><a href="<?= e(url('product/' . $p['slug'])) ?>"><?= e($p['name']) ?></a></th>
            <td><?= e($p['pack_size'] ?: '-') ?></td>
            <td><?= (int) $p['is_coming_soon'] === 1 || (float) $p['price'] <= 0 ? 'To be announced' : '<strong>' . money($p['price']) . '</strong> <small>' . e($p['price_unit']) . '</small>' ?>
                <?php if ($p['rent_price'] !== null && (float) $p['rent_price'] > 0): ?><br><small>or <?= money($p['rent_price']) ?> <?= e((string) $p['rent_unit']) ?></small><?php endif; ?></td>
            <td><?= (float) $p['security_deposit'] > 0 ? money($p['security_deposit']) . ' <small>refundable</small>' : '-' ?></td>
            <td><?= (int) $p['free_delivery'] === 1 ? '<span class="badge badge-success">Free</span>' : 'Collection' ?></td>
            <td>
              <?php if ((int) $p['is_coming_soon'] === 1): ?><span class="badge badge-soon">Coming soon</span>
              <?php elseif ($p['stock_status'] === 'on_demand'): ?><span class="badge badge-warning">On demand</span>
              <?php else: ?><span class="badge badge-success">Available</span><?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="grid grid-3">
      <?php foreach ($products as $p): require PE_ROOT . '/app/partials/product-card.php'; endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container container-narrow">
    <div class="section-head">
      <span class="eyebrow">Good to Know</span>
      <h2>How Our Pricing Works</h2>
    </div>
    <div class="grid grid-2">
      <article class="card">
        <h3>Free delivery, no minimum order</h3>
        <p>Every price shown includes delivery to your door anywhere in our published coverage area. There is no fuel surcharge, no small order fee and no minimum bottle count. The only exception is bulk filling at the plant, which is a collection service by design.</p>
      </article>
      <article class="card">
        <h3>The refundable bottle deposit</h3>
        <p>Returnable 19 litre bottles carry a one time refundable deposit of <?= money((float) fetch_val('SELECT security_deposit FROM products WHERE slug = "19-litre-refill-bottle"', [], 1500)) ?> per bottle. It secures the bottle, never the water, and it comes back to you in full when the bottle is returned in a usable condition.</p>
      </article>
      <article class="card">
        <h3>Pure and Mix packs</h3>
        <p>The water inside both is identical. Pure packs use a heavier premium PET preform with higher clarity for boardrooms, hotels and branded labels. Mix packs use our standard commercial preform, which brings the price down for households, canteens and shops.</p>
      </article>
      <article class="card">
        <h3>Bulk filling at Rs <?= e($perLitre) ?> per litre</h3>
        <p>Bring a clean food grade container to our Gujar Khan plant and fill at a flat Rs <?= e($perLitre) ?> per litre, metered, with no minimum and no maximum. Use the <a href="<?= e(url('bulk-water-calculator')) ?>">bulk water calculator</a> to work out your monthly cost.</p>
      </article>
    </div>
    <div class="cta-actions" style="margin-top:34px;">
      <a class="btn btn-primary btn-lg" href="<?= e(url('order')) ?>">Place an Order</a>
      <a class="btn btn-outline btn-lg" href="<?= e(url('contact')) ?>">Ask for a Quotation</a>
    </div>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
