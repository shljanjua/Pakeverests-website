<?php
/** Minerals and health benefits page. */
declare(strict_types=1);

$minerals = fetch_all('SELECT * FROM minerals WHERE is_active = 1 ORDER BY sort_order ASC');

seo_set([
    'title'       => 'Minerals in Our Water &amp; Their Health Benefits',
    'description' => 'Calcium, magnesium, sodium, potassium, TDS and pH in Pak-Everests water, with typical values, WHO and PSQCA limits and what each one does.',
    'keywords'    => 'minerals in drinking water, calcium magnesium water benefits, TDS in water, pH of drinking water, mineral water benefits',
    'breadcrumbs' => ['Minerals &amp; Benefits' => '/minerals-and-benefits'],
]);

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Minerals and Health Benefits';
$heroSubtitle = 'What is actually dissolved in a Pak-Everests bottle, how much of it there is, and why each one matters.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container container-narrow">
    <div class="prose">
      <p style="font-size:1.1rem;">
        Reverse osmosis removes almost everything from water, both the harmful and the useful. That is the point of
        it. What separates a properly run plant from the rest is what happens next: putting back calcium, magnesium,
        potassium and sodium under metered control, so every bottle carries the same profile in January as it does
        in July, regardless of what the aquifer is doing.
      </p>
      <p>
        The values below are our target ranges, verified on every batch in our in-house laboratory and periodically
        by an independent accredited laboratory. They are compared against the World Health Organization guideline
        values and the PSQCA drinking water requirements.
      </p>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="table-wrap" style="margin-bottom:44px;">
      <table>
        <caption class="sr-only">Pak-Everests typical mineral profile</caption>
        <thead>
          <tr>
            <th scope="col">Parameter</th><th scope="col">Symbol</th><th scope="col">Pak-Everests typical</th>
            <th scope="col">Unit</th><th scope="col">WHO guideline</th><th scope="col">PSQCA limit</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($minerals as $m): ?>
          <tr>
            <th scope="row"><?= e($m['name']) ?></th>
            <td><?= e($m['symbol']) ?></td>
            <td><strong><?= e($m['typical_value']) ?></strong></td>
            <td><?= e($m['unit']) ?></td>
            <td><?= e($m['who_limit']) ?></td>
            <td><?= e($m['psqca_limit']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="grid grid-2">
      <?php foreach ($minerals as $m): ?>
      <article class="card mineral-card card-hover reveal" style="--mineral-color:<?= e($m['color'] ?: '#0b7cb2') ?>;">
        <div class="mineral-symbol"><?= e($m['symbol']) ?></div>
        <h2 style="font-size:1.28rem;"><?= e($m['name']) ?></h2>
        <div class="mineral-value"><?= e($m['typical_value']) ?> <small><?= e($m['unit']) ?></small></div>
        <div class="mineral-limits">
          <span>WHO: <?= e($m['who_limit']) ?></span>
          <span>PSQCA: <?= e($m['psqca_limit']) ?></span>
        </div>
        <p style="margin-top:14px;font-weight:600;color:var(--text);"><?= e($m['benefits']) ?></p>
        <div class="prose" style="font-size:.94rem;margin-top:10px;"><?= rich_text($m['details']) ?></div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Practical Effect</span>
      <h2>What Balanced Minerals Actually Change</h2>
    </div>
    <div class="grid grid-3">
      <?php
      $effects = [
        ['Better taste', 'Minerals give water body on the palate. Bicarbonate in particular makes it taste soft and slightly sweet. Tasting panels consistently rate the 100 to 300 mg per litre TDS band highest, which is where we sit.'],
        ['Better hydration in heat', 'Sweat carries sodium, potassium, magnesium and chloride. Replacing heavy sweat loss with pure water alone dilutes what remains, which is why people who drink plenty still get cramps and headaches in a Potohar summer.'],
        ['A daily nutritional contribution', 'Nobody meets their mineral requirement from water alone. But minerals dissolved in water are in ionic form and absorb efficiently, so a steady daily contribution genuinely counts, particularly for growing children and older adults.'],
        ['Stable, comfortable pH', 'Unmineralised reverse osmosis water drifts slightly acidic because it has no buffering capacity. Bicarbonate alkalinity holds our finished water at 7.2 to 7.8, which is gentler on the stomach and tastes cleanest.'],
        ['Consistency all year', 'Groundwater mineral content changes with the season and the water table. Because we strip to purity and rebuild under metered control, every bottle is identical by design rather than by luck.'],
        ['Low sodium by choice', 'We hold sodium at 8 to 20 mg per litre, far below the 200 mg per litre ceiling. Enough for electrolyte balance and clean taste, low enough to suit people managing blood pressure.'],
      ];
      foreach ($effects as [$title, $body]): ?>
      <article class="card card-hover reveal">
        <h3><?= e($title) ?></h3>
        <p><?= e($body) ?></p>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container container-narrow">
    <div class="alert alert-info" role="note">
      <strong>A note on health claims.</strong> The information on this page is general education, not medical advice.
      Mineral requirements vary between individuals. Anyone with a kidney condition, a heart condition, high blood
      pressure or a sodium or potassium restricted diet should follow the guidance of a qualified physician rather
      than anything written here. See our <a href="<?= e(url('disclaimer')) ?>">disclaimer</a> for the full statement.
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="cta-band">
      <h2>Mineral balanced water, delivered free</h2>
      <p>Order a 19 litre refill, a PET pack or bulk filling at Rs <?= e((string) setting('per_litre_rate', '6')) ?> per litre. The same mineral profile in every format.</p>
      <div class="cta-actions">
        <a class="btn btn-lg btn-light" href="<?= e(url('order')) ?>">Order Now</a>
        <a class="btn btn-lg btn-outline" style="border-color:rgba(255,255,255,.4);color:#fff;" href="<?= e(url('purification-process')) ?>">See How We Make It</a>
      </div>
    </div>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
