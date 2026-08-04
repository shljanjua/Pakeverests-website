<?php
/** Bulk water filling calculator / facility filling page. */
declare(strict_types=1);

$rate    = (float) setting('per_litre_rate', '6');
$product = fetch_one('SELECT * FROM products WHERE slug = "bulk-water-filling"');

seo_set([
    'title'       => 'Bulk Water Calculator &amp; Facility Filling',
    'description' => 'Fill your own container at our Gujar Khan plant at Rs ' . $rate . ' per litre. Work out your daily, monthly and yearly water cost instantly.',
    'keywords'    => 'water per litre rate, bulk water filling, water filling station near me, water plant in Gujar Khan, cheap drinking water Rawalpindi',
    'breadcrumbs' => ['Bulk Water Calculator' => '/bulk-water-calculator'],
]);

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Facility Filling and Bulk Water Calculator';
$heroSubtitle = 'Bring your own food grade container and fill at a flat Rs ' . $rate . ' per litre, metered, with no minimum and no maximum.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container">
    <div id="bulkCalculator" class="calc-panel" data-rate="<?= e((string) $rate) ?>">
      <div class="form-card">
        <h2 style="font-size:1.3rem;">Work out your cost</h2>
        <p class="form-hint" style="margin-bottom:20px;">Enter the volume you take at a time and how often you take it. The figures update as you type.</p>

        <div class="form-group">
          <label for="calcLitres">Litres per fill</label>
          <input type="number" id="calcLitres" value="200" min="1" max="100000" step="1" inputmode="numeric">
          <span class="form-hint">For reference: a 19 litre bottle is 19 litres, a typical water tanker is 1,000 to 5,000 litres.</span>
        </div>

        <div class="form-group">
          <label for="calcFrequency">How often</label>
          <select id="calcFrequency">
            <option value="1">Once a day</option>
            <option value="2">Twice a day</option>
            <option value="0.5">Every 2 days</option>
            <option value="0.142857">Once a week</option>
            <option value="0.0333">Once a month</option>
          </select>
        </div>

        <div class="alert alert-info" style="margin-top:18px;">
          <strong>Rate:</strong> Rs <?= e((string) $rate) ?> per litre, flat. No minimum volume, no maximum, and no
          hidden charges. Filling is metered so you pay for exactly what you take.
        </div>
      </div>

      <div class="calc-result">
        <h3 style="font-size:1.05rem;opacity:.86;">Cost per fill of <span id="calcLitresOut">200 litres</span></h3>
        <div class="calc-figure" id="calcPerFill">Rs 1,200</div>
        <div style="margin-top:22px;">
          <div class="calc-line"><span>Per day</span><strong id="calcDaily">Rs 1,200</strong></div>
          <div class="calc-line"><span>Per month (30 days)</span><strong id="calcMonthly">Rs 36,000</strong></div>
          <div class="calc-line"><span>Per year</span><strong id="calcYearly">Rs 438,000</strong></div>
          <div class="calc-line"><span>Rate applied</span><strong>Rs <?= e((string) $rate) ?> / litre</strong></div>
        </div>
        <a class="btn btn-light btn-block btn-lg" style="margin-top:24px;" href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests, I want to discuss bulk water filling.')) ?>" target="_blank" rel="noopener">
          Discuss Bulk Supply on WhatsApp
        </a>
      </div>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">At the Counter</span>
      <h2>How Facility Filling Works</h2>
    </div>
    <div class="grid grid-3">
      <article class="card reveal">
        <div class="icon-box"><strong style="font-size:1.3rem;">1</strong></div>
        <h3>Bring a clean container</h3>
        <p>Containers must be food grade, sound, and free of any previous chemical, fuel or paint contents. Our staff inspect every container and will politely refuse anything unsuitable for drinking water.</p>
      </article>
      <article class="card reveal">
        <div class="icon-box is-mint"><strong style="font-size:1.3rem;">2</strong></div>
        <h3>We rinse and inspect</h3>
        <p>Containers are rinsed with treated water at the counter before filling. Ask if you would prefer a full sanitisation rather than a rinse and we will arrange it.</p>
      </article>
      <article class="card reveal">
        <div class="icon-box is-sun"><strong style="font-size:1.3rem;">3</strong></div>
        <h3>Fill and pay by volume</h3>
        <p>Filling is metered. You pay exactly Rs <?= e((string) $rate) ?> for every litre dispensed, with no minimum and no maximum, at the plant counter.</p>
      </article>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid-2" style="gap:34px;">
      <div class="prose">
        <h2>Who uses bulk filling</h2>
        <ul>
          <li><strong>Caterers and marquee operators</strong> supplying large functions where per litre cost decides the margin</li>
          <li><strong>School, college and factory canteens</strong> serving hundreds of people daily</li>
          <li><strong>Construction site managers</strong> keeping site coolers filled</li>
          <li><strong>Tanker operators</strong> serving housing schemes and residential blocks</li>
          <li><strong>Households in Gujar Khan</strong> who prefer to collect rather than take delivery</li>
        </ul>
        <h2>Important notes</h2>
        <p>Facility filling is a <strong>collection service at the plant</strong>. It is not delivered, and our free delivery offer does not apply to it.</p>
        <p>Water dispensed into a customer container leaves our chain of custody at the moment of filling. Storage hygiene afterwards is the customer responsibility. Keep containers covered, out of sunlight and away from any chemical or fuel.</p>
        <p>Timings: <?= e((string) setting('hours_display')) ?>. Address: <?= e((string) setting('address_full')) ?>.</p>
      </div>
      <div class="map-embed">
        <?php require PE_ROOT . '/app/partials/map.php'; ?>
      </div>
    </div>
  </div>
</section>

<?php if ($product): ?>
<section class="section section-soft">
  <div class="container">
    <div class="section-head"><h2>Prefer bottles delivered instead?</h2></div>
    <div class="grid grid-3">
      <?php foreach (fetch_all('SELECT * FROM products WHERE status = "published" AND is_featured = 1 AND slug <> "bulk-water-filling" ORDER BY sort_order ASC LIMIT 3') as $p):
        require PE_ROOT . '/app/partials/product-card.php';
      endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
