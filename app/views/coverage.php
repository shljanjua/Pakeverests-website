<?php
/** Delivery coverage areas page. */
declare(strict_types=1);

$areas = coverage_areas();

seo_set([
    'title'       => 'Delivery Coverage Areas',
    'description' => 'Free water delivery across Gujar Khan, Mandra, Daultala, Bewal, Kallar Syedan, Rawat, Adiala Road, Bahria Town and DHA Islamabad.',
    'keywords'    => 'water delivery Gujar Khan, water delivery Rawalpindi, water delivery Islamabad, water supplier Bahria Town, water delivery DHA Islamabad, water plant near me',
    'breadcrumbs' => ['Coverage Areas' => '/coverage-areas'],
]);

/* Service schema with an explicit areaServed list — a strong local-SEO signal
   that names every town and neighbourhood we deliver to, provided by the
   licensed business entity. */
$areaList = $areas ?: [
    ['area_name' => 'Gujar Khan'], ['area_name' => 'Mandra'], ['area_name' => 'Daultala'],
    ['area_name' => 'Bewal'], ['area_name' => 'Kallar Syedan'], ['area_name' => 'Rawat'],
    ['area_name' => 'Adiala Road'], ['area_name' => 'Bahria Town Rawalpindi'],
    ['area_name' => 'DHA Islamabad'], ['area_name' => 'Rawalpindi'], ['area_name' => 'Islamabad'],
];
seo_add_schema([
    '@context'    => 'https://schema.org',
    '@type'       => 'Service',
    '@id'         => SITE_URL . '/coverage-areas#service',
    'serviceType' => 'Bottled drinking water delivery',
    'name'        => 'Free mineral water delivery',
    'description' => 'Free home and office delivery of Pak-Everests mineral water across the Potohar belt, from Gujar Khan to Rawalpindi and Islamabad.',
    'provider'    => ['@id' => SITE_URL . '/#organization'],
    'areaServed'  => array_map(
        fn($a) => ['@type' => 'City', 'name' => $a['area_name']],
        $areaList
    ),
    'offers'      => [
        '@type'         => 'Offer',
        'price'         => '0',
        'priceCurrency' => 'PKR',
        'description'   => 'No delivery charge and no minimum order within the coverage area.',
    ],
]);

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Where We Deliver';
$heroSubtitle = 'Free delivery, no minimum order, across the whole Potohar belt from Gujar Khan to DHA Islamabad.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container">
    <div class="table-wrap">
      <table>
        <caption class="sr-only">Pak-Everests delivery coverage areas</caption>
        <thead>
          <tr><th scope="col">Area</th><th scope="col">District</th><th scope="col">Delivery schedule</th><th scope="col">Minimum order</th><th scope="col">Delivery charge</th></tr>
        </thead>
        <tbody>
          <?php foreach ($areas as $a): ?>
          <tr>
            <th scope="row"><?= e($a['area_name']) ?></th>
            <td><?= e($a['district']) ?></td>
            <td><?= e($a['delivery_days']) ?></td>
            <td><?= e($a['min_order']) ?></td>
            <td><?= (int) $a['is_free_delivery'] === 1 ? '<span class="badge badge-success">Free</span>' : 'On request' ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Area by Area</span>
      <h2>What We Cover in Each Area</h2>
    </div>
    <div class="grid grid-2">
      <?php foreach ($areas as $a): ?>
      <article class="card card-hover reveal">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
          <span class="area-pin"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg></span>
          <h3 style="margin:0;font-size:1.12rem;"><?= e($a['area_name']) ?></h3>
        </div>
        <p><?= e($a['description']) ?></p>
        <p class="form-hint"><strong>Schedule:</strong> <?= e($a['delivery_days']) ?> &middot; <strong>Minimum:</strong> <?= e($a['min_order']) ?> &middot; <strong>Delivery:</strong> Free</p>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid-2" style="gap:34px;align-items:start;">
      <div class="prose">
        <h2>How our routes work</h2>
        <h3>Same day cut off</h3>
        <p>Orders placed before <strong>4:00 PM</strong> for an area on that day route are normally delivered the same day. Orders after the cut off move to the next scheduled run for that area.</p>
        <h3>Delivery hours</h3>
        <p>Deliveries run from <strong>8:00 AM to 9:00 PM, Monday to Sunday</strong>, including public holidays except where announced on the website news ticker.</p>
        <h3>Standing orders get priority</h3>
        <p>Offices, schools, hospitals and shops on a fixed weekly or fortnightly schedule are assigned a set day and approximate time, and are routed before one off orders.</p>
        <h3>If your address is just outside</h3>
        <p>Ask us anyway. Our routes extend regularly and we will tell you honestly whether we can serve you reliably rather than promising and failing. Send your address on WhatsApp and we will confirm.</p>
        <p><a href="<?= e(url('delivery-policy')) ?>">Read the full delivery policy</a>, including failed deliveries and force majeure.</p>
      </div>
      <div class="map-embed">
        <?php require PE_ROOT . '/app/partials/map.php'; ?>
      </div>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="cta-band">
      <h2>Not sure if we reach you?</h2>
      <p>Send us your address on WhatsApp. We will confirm coverage and the delivery schedule for your street within minutes.</p>
      <div class="cta-actions">
        <a class="btn btn-lg btn-whatsapp" href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests, do you deliver to this address: ')) ?>" target="_blank" rel="noopener">Check My Address</a>
        <a class="btn btn-lg btn-light" href="<?= e(url('order')) ?>">Place an Order</a>
      </div>
    </div>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
