<?php
/** Become a distributor page. */
declare(strict_types=1);

$termsPage = fetch_one('SELECT * FROM pages WHERE slug = "distributor-terms" AND status = "published"');

seo_set([
    'title'       => 'Become a Water Distributor',
    'description' => 'Apply for a Pak-Everests water distributorship in Gujar Khan, Rawalpindi or Islamabad. Protected territory, trade pricing and reliable supply.',
    'keywords'    => 'water distribution business, become a water distributor, mineral water distributorship Pakistan, water dealership Rawalpindi',
    'breadcrumbs' => ['Distribution' => '/distribution'],
]);

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Become a Pak-Everests Distributor';
$heroSubtitle = 'Protected territory, confidential pricing, reliable supply and a partner who publishes the terms before you sign.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container">
    <div class="grid grid-2" style="gap:40px;align-items:center;">
      <div class="prose">
        <h2>Why distribute Pak-Everests</h2>
        <p>
          Bottled water is a repeat purchase category with genuine customer stickiness. A household that is happy with
          its supplier rarely changes. That cuts both ways: accounts take effort to win and then stay won.
        </p>
        <p>
          Demand across the Potohar belt is real and growing. Housing schemes along Adiala Road, Bahria Town and the
          Rawat corridor keep expanding. Offices, schools, clinics and marts now treat bottled water as a standing
          requirement rather than an occasional purchase.
        </p>
        <p>
          What we offer a distributor is not a slogan. It is a defined territory, a confidential price schedule,
          supply priority over one off customers, and commercial terms published in full on this website before you
          commit to anything.
        </p>
      </div>
      <div>
        <img src="<?= e(media_url((string) setting('distribution_image'), 'photo')) ?>"
             alt="Pak-Everests water distribution fleet"
             style="border-radius:var(--r-lg);box-shadow:var(--shadow);width:100%;" loading="lazy" width="640" height="480">
      </div>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">What You Get</span>
      <h2>Distributor Support Package</h2>
    </div>
    <div class="grid grid-3">
      <?php
      $support = [
        ['Protected territory', 'A defined geographical area where you are our sole appointed distributor for the listed product range, for as long as targets are met. Territory encroachment by another distributor is treated as a material breach.'],
        ['Confidential pricing', 'A distributor price schedule with volume rebates and seasonal incentives, revised only with fifteen days written notice. Stock already purchased is never repriced.'],
        ['Supply priority', 'Confirmed distributor orders are loaded before one off customer orders, including through the summer peak when demand doubles.'],
        ['A named account manager', 'One person who knows your territory and answers your call, rather than a general number.'],
        ['Marketing support', 'Point of sale material, banners, shop signage and vehicle branding, supplied and approved by us.'],
        ['Training', 'Product, hygiene and handling training for your delivery team, plus guidance on bottle tracking and route building.'],
      ];
      foreach ($support as [$title, $body]): ?>
      <article class="card card-hover reveal">
        <h3><?= e($title) ?></h3>
        <p><?= e($body) ?></p>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">What We Look For</span>
      <h2>Distributor Requirements</h2>
      <p>We would rather be honest about this up front than appoint someone who cannot make the economics work.</p>
    </div>
    <div class="grid grid-2">
      <article class="card reveal">
        <h3>A delivery vehicle</h3>
        <p>A Suzuki pickup or loader rickshaw is the standard starting point. Capacity matters more than speed, because route density is what makes the economics work.</p>
      </article>
      <article class="card reveal">
        <h3>Covered, clean storage</h3>
        <p>Dry, covered, secure space away from direct sunlight, heat and any chemical, fuel or paint. This is a food product and storage discipline is not optional. We inspect it.</p>
      </article>
      <article class="card reveal">
        <h3>Working capital</h3>
        <p>Opening stock, the security deposit, fuel, and enough cash to carry credit customers for a month. Underestimating the last of these is the most common reason new distributors fail.</p>
      </article>
      <article class="card reveal">
        <h3>Local knowledge and staff</h3>
        <p>Familiarity with your territory and at least one reliable rider. Delivery staff are the entire brand as far as most customers are concerned.</p>
      </article>
    </div>
  </div>
</section>

<section class="section section-dark">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">The Process</span>
      <h2>From Application to First Delivery</h2>
    </div>
    <div class="grid grid-4">
      <?php
      $steps = [
        ['1', 'Apply online', 'Complete the distributor application form with your area, storage, vehicle and target volume.'],
        ['2', 'Review and meeting', 'We review every application and invite shortlisted applicants to the plant at Gujar Khan.'],
        ['3', 'Agreement and security', 'A formal distributor agreement setting out territory, pricing, targets and the refundable security deposit.'],
        ['4', 'Launch', 'Opening stock, branding material, team training and your first route plan.'],
      ];
      foreach ($steps as [$n, $title, $body]): ?>
      <article class="card reveal" style="background:rgba(255,255,255,.07);border-color:rgba(255,255,255,.16);">
        <div class="icon-box"><strong style="font-size:1.3rem;"><?= $n ?></strong></div>
        <h3 style="color:#fff;font-size:1.05rem;"><?= e($title) ?></h3>
        <p style="color:rgba(255,255,255,.82);font-size:.9rem;"><?= e($body) ?></p>
      </article>
      <?php endforeach; ?>
    </div>
    <div class="cta-actions">
      <a class="btn btn-lg btn-light" href="<?= e(url('distributor-application')) ?>">Apply for a Territory</a>
      <a class="btn btn-lg btn-outline" style="border-color:rgba(255,255,255,.4);color:#fff;" href="<?= e(url('distributor-terms')) ?>">Read the Full Terms</a>
    </div>
  </div>
</section>

<?php if ($termsPage): ?>
<section class="section">
  <div class="container container-narrow">
    <div class="section-head is-left">
      <span class="eyebrow">Commercial Terms</span>
      <h2>Distributor Terms Summary</h2>
      <p>The full terms are published openly. Below is the complete text so you can read it before applying.</p>
    </div>
    <div class="prose"><?= rich_text($termsPage['content']) ?></div>
  </div>
</section>
<?php endif; ?>

<section class="section section-soft">
  <div class="container">
    <div class="cta-band">
      <h2>Territories are open across the Potohar region</h2>
      <p>Gujar Khan, Mandra, Daultala, Bewal, Kallar Syedan, Rawat, Adiala Road and the wider Rawalpindi and Islamabad market.</p>
      <div class="cta-actions">
        <a class="btn btn-lg btn-light" href="<?= e(url('distributor-application')) ?>">Submit Your Application</a>
        <a class="btn btn-lg btn-whatsapp" href="<?= e(wa_link((string) (whatsapp_numbers()[1]['number'] ?? primary_whatsapp()), 'Hello Pak-Everests, I am interested in a distributorship.')) ?>" target="_blank" rel="noopener">Ask a Question First</a>
      </div>
    </div>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
