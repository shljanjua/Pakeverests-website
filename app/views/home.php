<?php
/** Home page. */
declare(strict_types=1);

$featured = fetch_all('SELECT * FROM products WHERE status = "published" AND is_featured = 1 ORDER BY sort_order ASC LIMIT 6');
if (!$featured) {
    $featured = fetch_all('SELECT * FROM products WHERE status = "published" ORDER BY sort_order ASC LIMIT 6');
}
$stages    = fetch_all('SELECT * FROM process_stages WHERE is_active = 1 ORDER BY stage_no ASC');
$minerals  = fetch_all('SELECT * FROM minerals WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 4');
$reviews   = fetch_all('SELECT * FROM reviews WHERE status = "approved" AND is_featured = 1 ORDER BY created_at DESC LIMIT 6');
$faqs      = fetch_all('SELECT * FROM faqs WHERE is_active = 1 AND show_on_home = 1 ORDER BY sort_order ASC LIMIT 5');
$posts     = fetch_all('SELECT * FROM blog_posts WHERE status = "published" ORDER BY published_at DESC LIMIT 3');
$areas     = coverage_areas();
$galleryPreview = fetch_all('SELECT * FROM gallery WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 6');

$avgRating = (float) fetch_val('SELECT AVG(rating) FROM reviews WHERE status = "approved"', [], 4.9);
$totalRev  = (int) fetch_val('SELECT COUNT(*) FROM reviews WHERE status = "approved"', [], 0);

seo_set([
    'title'       => (string) setting('meta_title', 'Mineral Water Plant in Gujar Khan | 19 Litre Water Bottle Delivery'),
    'description' => (string) setting('meta_description'),
    'keywords'    => (string) setting('meta_keywords'),
]);

if ($faqs) {
    seo_add_schema(schema_faq($faqs));
}
seo_add_schema([
    '@context'        => 'https://schema.org',
    '@type'           => 'ItemList',
    'name'            => 'Pak-Everests Water Products',
    'itemListElement' => array_map(fn($p, $i) => [
        '@type'    => 'ListItem',
        'position' => $i + 1,
        'name'     => $p['name'],
        'url'      => SITE_URL . '/product/' . $p['slug'],
    ], $featured, array_keys($featured)),
]);

require PE_ROOT . '/app/partials/header.php';
?>

<!-- =================== HERO =================== -->
<section class="hero">
  <div class="hero-bubbles" aria-hidden="true"></div>
  <div class="container hero-inner">
    <div class="hero-copy">
      <span class="hero-eyebrow">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1 3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 15-4-4 1.41-1.41L11 13.17l5.59-5.59L18 9l-7 7z"/></svg>
        <?= e((string) setting('license_authority', 'Punjab Food Authority')) ?> Approved &middot; Potohar, Punjab
      </span>

      <h1>Pure Mineral Water from the <span class="accent">Heart of Potohar</span></h1>

      <p class="hero-lead">
        Pak-Everests runs a modern mineral water plant in Gujar Khan with a full eight stage purification
        process and controlled re-mineralisation. Order 19 litre refills at
        <strong>Rs <?= (int) fetch_val('SELECT price FROM products WHERE slug = "19-litre-refill-bottle"', [], 250) ?></strong>
        with <strong>free delivery</strong> across Gujar Khan, Rawalpindi, Islamabad and the wider Potohar region.
      </p>

      <div class="hero-actions">
        <a class="btn btn-lg btn-light" href="<?= e(url('order')) ?>">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 18a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm10 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7.2 14h9.5c.8 0 1.4-.5 1.6-1.2l2.6-8H6.2l-.5-2H2v2h2.4l3.6 9.4-1.3 2.4c-.5 1 .2 2.4 1.4 2.4H20v-2H8.5l.7-1.4z"/></svg>
          Order Water Now
        </a>
        <a class="btn btn-lg btn-whatsapp" href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests, I would like to order water. My area is: ')) ?>" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm5.8 14.13c-.24.68-1.4 1.3-1.94 1.34-.5.05-.98.23-3.3-.69-2.78-1.1-4.55-3.94-4.69-4.12-.14-.18-1.12-1.49-1.12-2.85s.71-2.02.97-2.3c.25-.27.55-.34.73-.34h.53c.17.01.4-.06.62.48.24.57.8 1.96.87 2.1.07.14.12.3.02.48-.09.18-.14.3-.28.46-.14.16-.3.36-.42.48-.14.14-.29.29-.12.57.16.27.73 1.2 1.56 1.95 1.07.95 1.97 1.25 2.25 1.39.27.14.43.12.59-.07.16-.18.68-.79.86-1.06.18-.27.36-.23.61-.14.24.09 1.55.73 1.82.86.27.14.45.2.51.32.07.11.07.64-.17 1.32z"/></svg>
          WhatsApp Order
        </a>
      </div>

      <div class="hero-stats">
        <div class="hero-stat"><strong data-count="8">8</strong><span>Purification stages</span></div>
        <div class="hero-stat"><strong data-count="<?= count($areas) ?>"><?= count($areas) ?></strong><span>Delivery areas</span></div>
        <div class="hero-stat"><strong data-count="<?= $totalRev ?>" data-suffix="+"><?= $totalRev ?>+</strong><span>Customer reviews</span></div>
        <div class="hero-stat"><strong>Rs 0</strong><span>Delivery charges</span></div>
      </div>
    </div>

    <div class="hero-visual">
      <div class="hero-ring" aria-hidden="true"></div>
      <img class="hero-bottle" src="<?= e(media_url((string) setting('hero_image'), 'hero')) ?>"
           alt="Pak-Everests 19 litre mineral water bottle" width="400" height="500" fetchpriority="high">
      <div class="hero-price-card">
        <small>19L Refill</small>
        <strong>Rs <?= (int) fetch_val('SELECT price FROM products WHERE slug = "19-litre-refill-bottle"', [], 250) ?></strong>
        <small>Free delivery</small>
      </div>
    </div>
  </div>

  <div class="hero-wave" aria-hidden="true">
    <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
      <path class="wave-back"  d="M0,64 C240,110 480,10 720,42 C960,74 1200,110 1440,58 L1440,120 L0,120 Z"/>
      <path class="wave-mid"   d="M0,80 C240,40 480,110 720,74 C960,38 1200,60 1440,86 L1440,120 L0,120 Z"/>
      <path class="wave-front" d="M0,96 C240,120 480,74 720,90 C960,106 1200,120 1440,100 L1440,120 L0,120 Z"/>
    </svg>
  </div>
</section>

<!-- =================== TRUST =================== -->
<section class="section" aria-labelledby="why-choose">
  <div class="container">
    <h2 id="why-choose" class="sr-only">Why choose Pak-Everests</h2>
    <div class="grid grid-4">
      <?php
      $trust = [
        ['Free Home Delivery', 'No delivery charge and no minimum order anywhere in our published coverage area, from Gujar Khan to DHA Islamabad.', 'M20 8h-3V4H3a2 2 0 0 0-2 2v11h2a3 3 0 0 0 6 0h6a3 3 0 0 0 6 0h2v-5l-3-4zM6 18.5A1.5 1.5 0 1 1 6 15.5a1.5 1.5 0 0 1 0 3zm12 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm1.5-9 1.96 2.5H17V9.5h2.5z', ''],
        ['8 Stage Purification', 'Sand, carbon, softening, micron, reverse osmosis, re-mineralisation, ultraviolet and ozone, in that order, every batch.', 'M12 2 4 6v6c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V6l-8-4z', 'is-mint'],
        ['Balanced Minerals', 'Calcium, magnesium, potassium and sodium restored under metered control so every bottle tastes the same all year.', 'M12 3a9 9 0 1 0 9 9 9 9 0 0 0-9-9zm0 4a5 5 0 1 1-5 5 5 5 0 0 1 5-5z', 'is-sun'],
        ['Food Authority Approved', 'A licensed bottled drinking water establishment with batch coding and full traceability on every bottle.', 'M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z', ''],
      ];
      foreach ($trust as [$title, $body, $icon, $mod]): ?>
      <article class="card card-hover reveal">
        <div class="icon-box <?= e($mod) ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="<?= $icon ?>"/></svg></div>
        <h3><?= e($title) ?></h3>
        <p><?= e($body) ?></p>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?= ad_slot('content_top') ?>

<!-- =================== PRODUCTS =================== -->
<section class="section section-soft" id="products">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Our Range</span>
      <h2>Water Products &amp; Prices</h2>
      <p>From the 19 litre dispenser refill to single serve packs, custom printed labels and bulk facility filling at Rs <?= e((string) setting('per_litre_rate', '6')) ?> per litre.</p>
    </div>

    <div class="grid grid-3">
      <?php foreach ($featured as $p): require PE_ROOT . '/app/partials/product-card.php'; endforeach; ?>
    </div>

    <p style="text-align:center;margin-top:34px;">
      <a class="btn btn-primary btn-lg" href="<?= e(url('products')) ?>">View All Products &amp; Prices</a>
    </p>
  </div>
</section>

<!-- =================== PROCESS =================== -->
<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">How We Purify</span>
      <h2>The 8 Stage Purification Process</h2>
      <p>Every drop of Pak-Everests water passes through all eight stages. Each one exists because it removes something the stage before it cannot.</p>
    </div>

    <div class="grid grid-4">
      <?php foreach ($stages as $s): ?>
      <article class="card card-hover reveal" style="text-align:center;">
        <div class="icon-box <?= (int) $s['stage_no'] % 2 === 0 ? 'is-mint' : '' ?>" style="margin-inline:auto;">
          <strong style="font-size:1.3rem;"><?= (int) $s['stage_no'] ?></strong>
        </div>
        <h3 style="font-size:1.02rem;"><?= e($s['title']) ?></h3>
        <p style="font-size:.88rem;"><?= e(excerpt($s['summary'], 110)) ?></p>
      </article>
      <?php endforeach; ?>
    </div>

    <p style="text-align:center;margin-top:34px;">
      <a class="btn btn-outline btn-lg" href="<?= e(url('purification-process')) ?>">Read the Full Process in Detail</a>
    </p>
  </div>
</section>

<!-- =================== MINERALS =================== -->
<section class="section section-dark">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">What Is Inside</span>
      <h2>Minerals That Do Real Work</h2>
      <p>Reverse osmosis strips everything. We put back exactly what the body uses, in measured quantities, so the water is consistent in January and in July.</p>
    </div>

    <div class="grid grid-4">
      <?php foreach ($minerals as $m): ?>
      <article class="card reveal" style="background:rgba(255,255,255,.07);border-color:rgba(255,255,255,.16);">
        <div class="mineral-symbol" style="background:<?= e($m['color'] ?: '#0b7cb2') ?>;"><?= e($m['symbol']) ?></div>
        <h3 style="color:#fff;"><?= e($m['name']) ?></h3>
        <div class="mineral-value" style="color:#fff;"><?= e($m['typical_value']) ?> <small><?= e($m['unit']) ?></small></div>
        <p style="color:rgba(255,255,255,.8);font-size:.89rem;margin-top:10px;"><?= e(excerpt($m['benefits'], 120)) ?></p>
      </article>
      <?php endforeach; ?>
    </div>

    <p style="text-align:center;margin-top:34px;">
      <a class="btn btn-light btn-lg" href="<?= e(url('minerals-and-benefits')) ?>">See the Full Mineral Profile</a>
    </p>
  </div>
</section>

<!-- =================== SECTORS =================== -->
<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Who We Supply</span>
      <h2>Trusted Across Every Sector</h2>
      <p>From a single household bottle to a standing weekly order for a four hundred student school, the same water and the same reliability.</p>
    </div>

    <div class="grid grid-4">
      <?php
      $sectors = [
        ['Corporate Offices', 'Dispensers, standing weekly orders and custom branded bottles for reception and boardrooms.'],
        ['Schools &amp; Colleges', 'Sealed single serve packs for students and 19 litre dispensers for staff rooms and canteens.'],
        ['Hospitals &amp; Clinics', 'Hygienic sealed bottles for patient rooms, waiting areas and consulting rooms.'],
        ['Shops, Marts &amp; Malls', 'Fast moving retail packs with healthy margins and reliable restocking.'],
        ['Mosques &amp; Gatherings', 'Bulk supply for Friday prayers, Ramadan and community events.'],
        ['Homes &amp; Residences', 'The 19 litre refill at Rs 250 with free delivery and a refundable bottle deposit.'],
        ['Hotels &amp; Restaurants', 'Table water, custom labels and the glass bottle range launching soon.'],
        ['Construction &amp; Industry', 'Site cartons, bulk filling at Rs ' . e((string) setting('per_litre_rate', '6')) . ' per litre and scheduled delivery.'],
      ];
      foreach ($sectors as [$title, $desc]): ?>
      <article class="card card-hover reveal">
        <h3 style="font-size:1.02rem;"><?= $title ?></h3>
        <p style="font-size:.89rem;"><?= $desc ?></p>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- =================== COVERAGE =================== -->
<section class="section section-soft">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Delivery Coverage</span>
      <h2>Where We Deliver, Free of Charge</h2>
      <p>Daily and alternate day routes across the Potohar belt. Orders placed before 4:00 PM on a daily route area are normally delivered the same day.</p>
    </div>

    <div class="area-grid">
      <?php foreach ($areas as $a): ?>
      <div class="area-card reveal">
        <span class="area-pin"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg></span>
        <div>
          <strong><?= e($a['area_name']) ?></strong>
          <span><?= e($a['delivery_days']) ?> &middot; Free delivery</span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <p style="text-align:center;margin-top:30px;">
      <a class="btn btn-outline" href="<?= e(url('coverage-areas')) ?>">Full Coverage Details</a>
    </p>
  </div>
</section>

<!-- =================== CUSTOM LABELS =================== -->
<section class="section">
  <div class="container">
    <div class="grid grid-2" style="align-items:center;gap:40px;">
      <div class="reveal">
        <span class="eyebrow">Private Label</span>
        <h2>Your Brand on Every Bottle</h2>
        <p>A branded bottle is the one item at an event that every guest picks up, holds and reads. We print custom labels on 500 ml and 1.5 litre bottles for weddings, corporate offices, conferences, hotels and welfare campaigns.</p>
        <ul>
          <li>Free design support if you do not have artwork ready</li>
          <li>Digital proof shared for your approval before printing</li>
          <li>Seven to twelve working days from brief to delivery</li>
          <li>Statutory food authority information handled by our design team</li>
        </ul>
        <div class="hero-actions" style="margin-top:22px;">
          <a class="btn btn-primary" href="<?= e(url('custom-label-request')) ?>">Start Your Label Brief</a>
          <a class="btn btn-outline" href="<?= e(url('custom-label-bottles')) ?>">See Examples</a>
        </div>
      </div>
      <div class="reveal">
        <img src="<?= e(media_url((string) setting('custom_label_image'), 'product')) ?>"
             alt="Custom printed label water bottles by Pak-Everests"
             style="border-radius:var(--r-lg);box-shadow:var(--shadow);width:100%;" loading="lazy" width="600" height="600">
      </div>
    </div>
  </div>
</section>

<?= ad_slot('content_middle') ?>

<!-- =================== REVIEWS =================== -->
<section class="section section-soft" id="reviews">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Customer Reviews</span>
      <h2>What Our Customers Say</h2>
      <p><?= stars($avgRating, true) ?> average from <?= (int) $totalRev ?> verified customer reviews across Gujar Khan, Rawalpindi and Islamabad.</p>
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
        <?php if ((int) $r['is_verified'] === 1): ?>
        <span class="review-verified">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg> Verified customer
        </span>
        <?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>

    <p style="text-align:center;margin-top:34px;">
      <a class="btn btn-outline" href="<?= e(url('reviews')) ?>">Read All Reviews &amp; Write Yours</a>
    </p>
  </div>
</section>

<!-- =================== GALLERY PREVIEW =================== -->
<?php if ($galleryPreview): ?>
<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Inside the Plant</span>
      <h2>Photo Gallery</h2>
      <p>Our filling line, bottle washing station, laboratory and delivery fleet in Gujar Khan.</p>
    </div>
    <div class="gallery-grid">
      <?php foreach ($galleryPreview as $g): ?>
      <a class="gallery-item reveal" href="<?= e(media_url($g['image_path'], 'photo')) ?>"
         data-lightbox="<?= e(media_url($g['image_path'], 'photo')) ?>" data-caption="<?= e($g['title']) ?>">
        <img src="<?= e(media_url($g['image_path'], 'photo')) ?>" alt="<?= e($g['alt_text'] ?: $g['title']) ?>" loading="lazy" width="400" height="300">
        <span class="gallery-caption"><?= e($g['title']) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
    <p style="text-align:center;margin-top:30px;">
      <a class="btn btn-outline" href="<?= e(url('gallery')) ?>">View the Full Gallery</a>
    </p>
  </div>
</section>
<?php endif; ?>

<!-- =================== FAQ =================== -->
<?php if ($faqs): ?>
<section class="section section-soft">
  <div class="container container-narrow">
    <div class="section-head">
      <span class="eyebrow">Questions</span>
      <h2>Frequently Asked Questions</h2>
      <p>The five things customers ask most before their first order.</p>
    </div>

    <div class="accordion" data-single="true" data-open-first="true">
      <?php foreach ($faqs as $f): ?>
      <div class="accordion-item">
        <button class="accordion-trigger" type="button">
          <span><?= e($f['question']) ?></span>
          <span class="accordion-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 10l5 5 5-5z"/></svg></span>
        </button>
        <div class="accordion-panel"><div class="accordion-body"><?= rich_text($f['answer']) ?></div></div>
      </div>
      <?php endforeach; ?>
    </div>

    <p style="text-align:center;margin-top:28px;">
      <a class="btn btn-outline" href="<?= e(url('faqs')) ?>">See All FAQs</a>
    </p>
  </div>
</section>
<?php endif; ?>

<!-- =================== BLOG =================== -->
<?php if ($posts): ?>
<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Blog &amp; News</span>
      <h2>Water Knowledge from Our Team</h2>
      <p>Practical guides on water quality, hydration, custom labels and the distribution business.</p>
    </div>

    <div class="grid grid-3">
      <?php foreach ($posts as $post): require PE_ROOT . '/app/partials/post-card.php'; endforeach; ?>
    </div>

    <p style="text-align:center;margin-top:34px;">
      <a class="btn btn-outline" href="<?= e(url('blog')) ?>">Read the Blog</a>
    </p>
  </div>
</section>
<?php endif; ?>

<!-- =================== MAP + CTA =================== -->
<section class="section section-soft">
  <div class="container">
    <div class="grid grid-2" style="gap:34px;align-items:stretch;">
      <div>
        <span class="eyebrow">Find Us</span>
        <h2>Our Water Plant in Gujar Khan</h2>
        <p><?= e((string) setting('address_full')) ?></p>
        <p><strong>Timings:</strong> <?= e((string) setting('hours_display')) ?></p>
        <p><strong>Bulk filling counter:</strong> Rs <?= e((string) setting('per_litre_rate', '6')) ?> per litre, bring your own food grade container.</p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="<?= e((string) setting('map_directions_url', '#')) ?>" target="_blank" rel="noopener">Get Directions</a>
          <a class="btn btn-outline" href="<?= e(url('contact')) ?>">Contact Us</a>
        </div>
      </div>
      <div class="map-embed">
        <?php require PE_ROOT . '/app/partials/map.php'; ?>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="cta-band reveal">
      <h2>Ready for cleaner, better tasting water?</h2>
      <p>Place your order online or send us a WhatsApp message. Same day delivery on orders placed before 4:00 PM in daily route areas, and no delivery charge anywhere we serve.</p>
      <div class="cta-actions">
        <a class="btn btn-lg btn-light" href="<?= e(url('order')) ?>">Place an Order</a>
        <a class="btn btn-lg btn-whatsapp" href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests, I would like to order water.')) ?>" target="_blank" rel="noopener">WhatsApp <?= e(primary_whatsapp()) ?></a>
      </div>
    </div>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
