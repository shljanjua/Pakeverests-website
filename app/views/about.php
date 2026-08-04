<?php
/** About us page. */
declare(strict_types=1);

$gallery = fetch_all('SELECT * FROM gallery WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 6');
$areas   = coverage_areas();

seo_set([
    'title'       => 'About Pak-Everests Mineral Water',
    'description' => 'A Punjab Food Authority approved mineral water plant in Gujar Khan supplying homes, offices, schools and hospitals across the Potohar region.',
    'keywords'    => 'about Pak-Everests, mineral water company Pakistan, water plant Gujar Khan, Potohar water supplier',
    'breadcrumbs' => ['About Us' => '/about'],
]);
seo_add_schema([
    '@context' => 'https://schema.org',
    '@type'    => 'AboutPage',
    'name'     => 'About Pak-Everests Bottled Drinking Water',
    'url'      => SITE_URL . '/about',
    'mainEntity' => ['@id' => SITE_URL . '/#organization'],
]);

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'About Pak-Everests';
$heroSubtitle = 'A water plant built in Gujar Khan, for the Potohar region, by people who live here.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container">
    <div class="grid grid-2" style="gap:44px;align-items:center;">
      <div class="prose reveal">
        <h2>Why we started</h2>
        <p>
          Groundwater quality across the Potohar belt is variable, and everyone who lives here knows it. In some
          villages the water is hard enough to leave scale in a kettle within a week. In others the taste changes
          with the season. Families who could afford it were buying bottled water from suppliers based two hours
          away, paying delivery charges, and still receiving bottles that had sat in the sun on a truck all afternoon.
        </p>
        <p>
          Pak-Everests was established to fix that locally. We built a full eight stage purification plant on the
          G.T. Road at Gujar Khan, close enough to our customers that a bottle filled in the morning is at the door
          the same day, and priced so that clean drinking water is an ordinary household purchase rather than a luxury.
        </p>
        <h2>What we do differently</h2>
        <p>
          Anyone can install a filter. What takes discipline is running a documented process, measuring a parameter
          at every stage, coding every batch so it can be traced afterwards, and retiring a bottle permanently the
          moment it fails inspection rather than quietly refilling it.
        </p>
        <p>
          We also publish things most suppliers keep vague. Our <a href="<?= e(url('damage-policy')) ?>">bottle damage
          schedule</a>, our <a href="<?= e(url('refund-policy')) ?>">deposit refund process</a> and our
          <a href="<?= e(url('distributor-terms')) ?>">distributor terms</a> are all on this website in full, because a
          customer who knows the terms in advance never has to argue about them later.
        </p>
      </div>
      <div class="reveal">
        <img src="<?= e(media_url((string) setting('about_image'), 'photo')) ?>"
             alt="The Pak-Everests water plant at Gujar Khan"
             style="border-radius:var(--r-lg);box-shadow:var(--shadow);width:100%;" loading="lazy" width="640" height="480">
      </div>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="grid grid-4">
      <article class="card reveal" style="text-align:center;">
        <div class="icon-box" style="margin-inline:auto;"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1 3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg></div>
        <h3 style="font-size:1rem;">Licensed</h3>
        <p style="font-size:.9rem;"><?= e((string) setting('license_authority')) ?> approved bottled drinking water establishment.</p>
      </article>
      <article class="card reveal" style="text-align:center;">
        <div class="icon-box is-mint" style="margin-inline:auto;"><strong style="font-size:1.3rem;">8</strong></div>
        <h3 style="font-size:1rem;">Stage process</h3>
        <p style="font-size:.9rem;">Sand, carbon, softening, micron, RO, re-mineralisation, UV and ozone.</p>
      </article>
      <article class="card reveal" style="text-align:center;">
        <div class="icon-box is-sun" style="margin-inline:auto;"><strong style="font-size:1.15rem;"><?= count($areas) ?></strong></div>
        <h3 style="font-size:1rem;">Delivery areas</h3>
        <p style="font-size:.9rem;">Gujar Khan to DHA Islamabad, all with free delivery and no minimum order.</p>
      </article>
      <article class="card reveal" style="text-align:center;">
        <div class="icon-box" style="margin-inline:auto;"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2 3 7v10l9 5 9-5V7l-9-5z"/></svg></div>
        <h3 style="font-size:1rem;">Since <?= e((string) setting('company_founded', '2019')) ?></h3>
        <p style="font-size:.9rem;">Serving homes, offices, schools, hospitals, mosques and businesses.</p>
      </article>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">What We Stand For</span>
      <h2>Our Values</h2>
    </div>
    <div class="grid grid-3">
      <?php
      $values = [
        ['Safety is not negotiable', 'A batch that fails a laboratory check is held, investigated and destroyed if necessary. It never goes out because a route is waiting. Nothing about a delivery schedule is more important than what is inside the bottle.'],
        ['Say the price, keep the price', 'Free delivery means free delivery, with no fuel surcharge and no small order fee added at the door. The refundable deposit is refundable, on a published schedule, assessed in front of you.'],
        ['Reliability in June, not just March', 'Anyone can deliver in mild weather. We size our fleet and our stock for the third week of June, when demand doubles, because that is when customers actually find out who their supplier is.'],
        ['Publish the terms', 'Deposit deductions, delivery failures, damage assessments and dispute escalation are all written down and public. Nobody should have to discover the rules during a disagreement.'],
        ['Respect at the door', 'Our delivery team carries bottles inside, waits while you inspect the seal, and issues a receipt. They are the entire brand as far as most customers are concerned, and they are trained accordingly.'],
        ['Local employment', 'Our riders, plant operators and support staff are hired from Gujar Khan and the surrounding towns. The business exists here, so the jobs should too.'],
      ];
      foreach ($values as [$title, $body]): ?>
      <article class="card card-hover reveal">
        <h3><?= e($title) ?></h3>
        <p><?= e($body) ?></p>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-dark">
  <div class="container">
    <div class="grid grid-2" style="gap:40px;">
      <div>
        <span class="eyebrow">Our Mission</span>
        <h2>Clean drinking water as an ordinary purchase</h2>
        <p>
          To make laboratory verified, mineral balanced drinking water available to every household, office, school,
          clinic and mosque in the Potohar region at a price that does not require a second thought, delivered free,
          on a schedule people can plan around.
        </p>
      </div>
      <div>
        <span class="eyebrow">Our Vision</span>
        <h2>The supplier the region trusts by default</h2>
        <p>
          To be the water brand that families in Gujar Khan, Rawalpindi and Islamabad recommend without being asked,
          because the bottle arrives when it should, tastes the same every time, and the company behind it does what
          it said it would do.
        </p>
      </div>
    </div>
  </div>
</section>

<?php if ($gallery): ?>
<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Inside the Plant</span>
      <h2>See Where Your Water Comes From</h2>
      <p>We are happy to show the plant to any customer who asks. In the meantime, here is a look inside.</p>
    </div>
    <div class="gallery-grid">
      <?php foreach ($gallery as $g): ?>
      <a class="gallery-item reveal" href="<?= e(media_url($g['image_path'], 'photo')) ?>"
         data-lightbox="<?= e(media_url($g['image_path'], 'photo')) ?>" data-caption="<?= e($g['title']) ?>">
        <img src="<?= e(media_url($g['image_path'], 'photo')) ?>" alt="<?= e($g['alt_text'] ?: $g['title']) ?>" loading="lazy" width="400" height="300">
        <span class="gallery-caption"><?= e($g['title']) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
    <p style="text-align:center;margin-top:30px;">
      <a class="btn btn-outline" href="<?= e(url('gallery')) ?>">Full Gallery</a>
      <a class="btn btn-outline" href="<?= e(url('documents')) ?>">Licences &amp; Documents</a>
    </p>
  </div>
</section>
<?php endif; ?>

<section class="section section-soft">
  <div class="container">
    <div class="cta-band">
      <h2>Come and see the plant, or just try a bottle</h2>
      <p>Both offers are genuine. Book a visit, or place a first order and judge the water yourself.</p>
      <div class="cta-actions">
        <a class="btn btn-lg btn-light" href="<?= e(url('order')) ?>">Place an Order</a>
        <a class="btn btn-lg btn-whatsapp" href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests, I would like to visit the plant.')) ?>" target="_blank" rel="noopener">Request a Visit</a>
      </div>
    </div>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
