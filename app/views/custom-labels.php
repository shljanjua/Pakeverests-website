<?php
/** Custom label bottles marketing page. */
declare(strict_types=1);

$labelProducts = fetch_all('SELECT * FROM products WHERE status = "published" AND category = "pet" ORDER BY sort_order ASC');

seo_set([
    'title'       => 'Custom Label Water Bottles',
    'description' => 'Personalised water bottles with your printed label for weddings, offices and events in Rawalpindi and Islamabad. Free design support included.',
    'keywords'    => 'custom label water bottles, personalised water bottles Pakistan, wedding water bottles, branded water bottles, promotional water bottles Rawalpindi',
    'breadcrumbs' => ['Custom Label Bottles' => '/custom-label-bottles'],
]);
seo_add_schema([
    '@context'    => 'https://schema.org',
    '@type'       => 'Service',
    'serviceType' => 'Custom label water bottle printing',
    'provider'    => ['@id' => SITE_URL . '/#organization'],
    'areaServed'  => 'Rawalpindi, Islamabad and the Potohar region',
    'description' => 'Custom printed label water bottles in 500 ml and 1.5 litre formats for weddings, corporate branding and events.',
]);

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Custom Label Water Bottles';
$heroSubtitle = 'The only item at your event that every single guest picks up, holds and reads. Put your name on it.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container">
    <div class="grid grid-2" style="gap:40px;align-items:center;">
      <div class="prose">
        <h2>Your brand, professionally printed</h2>
        <p>
          We print custom labels on <strong>500 ml</strong> and <strong>1.5 litre</strong> bottles today, and the
          350 ml bottle joins the range at launch. The 500 ml is by far the most popular for events: it is the size
          a guest actually finishes, it sits neatly on a table setting, and it carries a full label area at eye level.
        </p>
        <p>
          The water inside is the same eight stage purified, mineral balanced Pak-Everests water we supply everywhere
          else. Only the label changes.
        </p>
        <div class="hero-actions" style="margin-top:22px;">
          <a class="btn btn-primary btn-lg" href="<?= e(url('custom-label-request')) ?>">Start Your Label Brief</a>
          <a class="btn btn-whatsapp btn-lg" href="<?= e(wa_link(primary_whatsapp(), 'Hello, I want custom printed label water bottles.')) ?>" target="_blank" rel="noopener">Ask on WhatsApp</a>
        </div>
      </div>
      <div>
        <img src="<?= e(media_url((string) setting('custom_label_image'), 'product')) ?>"
             alt="Custom printed label water bottles by Pak-Everests"
             style="border-radius:var(--r-lg);box-shadow:var(--shadow);width:100%;" loading="lazy" width="640" height="640">
      </div>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Where It Is Used</span>
      <h2>Popular Applications</h2>
    </div>
    <div class="grid grid-3">
      <?php
      $uses = [
        ['Weddings and family events', 'Couple names and the wedding date, mehndi and barat themed designs, aqiqah and walima bottles. Guests genuinely take these home.'],
        ['Corporate offices', 'Reception areas, meeting rooms and client visits. A branded bottle in front of a client says something before anyone speaks.'],
        ['Conferences and seminars', 'Event branding, sponsor logos, dates and venue. Exhibition stands use them as the give away that costs least and gets used most.'],
        ['Hotels and restaurants', 'House branded table water at a fraction of the cost of an imported brand.'],
        ['Hospitals and clinics', 'Clinic branded sealed bottles for patient rooms, waiting areas and pharmacy counters.'],
        ['Campaigns and welfare', 'Campaign branding and welfare organisation identity on relief distribution.'],
      ];
      foreach ($uses as [$title, $body]): ?>
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
    <div class="grid grid-2" style="gap:40px;align-items:start;">
      <div class="prose">
        <h2>Artwork requirements</h2>
        <p>This is where most delays happen, so it is worth reading carefully.</p>
        <ul>
          <li><strong>Format:</strong> AI, PSD, PDF, EPS or a high resolution PNG</li>
          <li><strong>Resolution:</strong> 300 DPI at final print size. A logo pulled from a website at 72 DPI will print soft and there is no way around that</li>
          <li><strong>Colour mode:</strong> CMYK. Convert before sending, because RGB colours shift on press</li>
          <li><strong>Bleed:</strong> 3 mm on every side</li>
          <li><strong>Text safety:</strong> keep important text at least 5 mm inside the trim, because the label wraps around a curve</li>
          <li><strong>Fonts:</strong> outlined, or supplied with the file</li>
        </ul>
        <p>
          No artwork ready? Our design team will create it. Send your logo, brand colours, the text you want and a
          reference you like, and we produce concepts for you at no extra charge on qualifying quantities.
        </p>
        <h2>What must stay on the label</h2>
        <p>
          Every bottle sold in Punjab must carry the statutory information required by the Punjab Food Authority:
          plant name and licence details, batch code, filling date and volume. This occupies a defined strip and
          cannot be removed or covered by customer artwork. Our design team lays it out so it never interferes with
          your branding.
        </p>
      </div>

      <div>
        <div class="card" style="margin-bottom:22px;">
          <h2 style="font-size:1.15rem;">Production timeline</h2>
          <div class="table-wrap" style="border:0;">
            <table>
              <tbody>
                <tr><th scope="row">Brief received, quotation issued</th><td>Same day</td></tr>
                <tr><th scope="row">Design concepts (if we create artwork)</th><td>2 - 3 working days</td></tr>
                <tr><th scope="row">Revisions and proof approval</th><td>1 - 2 working days</td></tr>
                <tr><th scope="row">Label printing</th><td>3 - 5 working days</td></tr>
                <tr><th scope="row">Filling, labelling and delivery</th><td>2 - 3 working days</td></tr>
              </tbody>
            </table>
          </div>
          <p class="form-hint" style="margin-top:12px;">
            Plan for seven to twelve working days end to end. For a wedding, start three weeks out. Rush production is
            sometimes possible at a surcharge, but proof approval cannot be compressed.
          </p>
        </div>

        <div class="card">
          <h2 style="font-size:1.15rem;">Five mistakes to avoid</h2>
          <ol style="font-size:.93rem;color:var(--text-soft);">
            <li><strong>Low resolution logo.</strong> The most common problem by a distance.</li>
            <li><strong>Text too close to the edge.</strong> The label wraps, so margin text disappears around the back.</li>
            <li><strong>Very fine text.</strong> Anything under 6 point is unreadable on a curved surface.</li>
            <li><strong>Dark heavy backgrounds.</strong> They show every handling mark and condensation drop.</li>
            <li><strong>Approving the proof without reading it.</strong> Check every name and date. Twice.</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if ($labelProducts): ?>
<section class="section section-soft">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Available Formats</span>
      <h2>Bottles You Can Brand</h2>
    </div>
    <div class="grid grid-4">
      <?php foreach ($labelProducts as $p): require PE_ROOT . '/app/partials/product-card.php'; endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section">
  <div class="container">
    <div class="cta-band">
      <h2>Send us your logo and we will do the rest</h2>
      <p>Tell us the bottle size, quantity, occasion and the date you need it. We will issue a written quotation the same day.</p>
      <div class="cta-actions">
        <a class="btn btn-lg btn-light" href="<?= e(url('custom-label-request')) ?>">Submit Your Label Brief</a>
        <a class="btn btn-lg btn-whatsapp" href="<?= e(wa_link(primary_whatsapp(), 'Hello, I want a quotation for custom label bottles.')) ?>" target="_blank" rel="noopener">Get a Quotation</a>
      </div>
    </div>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
