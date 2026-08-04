<?php
/** 8 stage purification process page. */
declare(strict_types=1);

$stages = fetch_all('SELECT * FROM process_stages WHERE is_active = 1 ORDER BY stage_no ASC');

seo_set([
    'title'       => '8 Stage Water Purification Process',
    'description' => 'Inside our Gujar Khan plant: sediment, carbon, softening, micron, reverse osmosis, re-mineralisation, ultraviolet and ozonated sealed filling.',
    'keywords'    => 'water purification process, 8 stage purification, reverse osmosis plant, mineral water plant Gujar Khan, RO UV ozone water treatment',
    'breadcrumbs' => ['8 Stage Purification Process' => '/purification-process'],
]);
seo_add_schema([
    '@context' => 'https://schema.org',
    '@type'    => 'HowTo',
    'name'     => 'The Pak-Everests 8 Stage Water Purification Process',
    'description' => 'How raw groundwater becomes sealed, mineral balanced Pak-Everests drinking water.',
    'totalTime'   => 'PT4H',
    'step' => array_map(fn($s) => [
        '@type' => 'HowToStep',
        'position' => (int) $s['stage_no'],
        'name'  => $s['title'],
        'text'  => strip_tags((string) $s['summary']),
        'url'   => SITE_URL . '/purification-process#stage-' . (int) $s['stage_no'],
    ], $stages),
]);

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'The 8 Stage Purification Process';
$heroSubtitle = 'Every drop passes through all eight stages, in this exact order. Each stage exists because it removes something the stage before it cannot.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container container-narrow">
    <div class="prose">
      <p style="font-size:1.1rem;">
        Purification is not a single machine. It is a sequence, and the order matters as much as the equipment.
        Put reverse osmosis first and the membranes are destroyed by chlorine and hardness within weeks. Skip
        re-mineralisation and the water is safe but flat and empty. What follows is exactly what happens inside the
        Pak-Everests plant at Gujar Khan, stage by stage, with the parameter we measure at each one.
      </p>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="process-timeline">
      <?php foreach ($stages as $s): ?>
      <article class="process-step reveal" id="stage-<?= (int) $s['stage_no'] ?>">
        <div class="process-number"><?= (int) $s['stage_no'] ?></div>
        <div class="process-content">
          <h2 style="font-size:1.32rem;"><?= e($s['title']) ?></h2>
          <?php if ($s['subtitle']): ?><p class="process-subtitle"><?= e($s['subtitle']) ?></p><?php endif; ?>
          <div class="prose" style="font-size:.98rem;"><?= rich_text($s['details'] ?: '<p>' . e((string) $s['summary']) . '</p>') ?></div>
          <?php if ($s['what_it_removes']): ?>
          <div class="process-removes">
            <strong>What this stage handles</strong>
            <?= e($s['what_it_removes']) ?>
          </div>
          <?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">After the Eighth Stage</span>
      <h2>Testing, Coding and Release</h2>
      <p>Purification is only half of quality control. Nothing leaves the plant until the batch record is complete.</p>
    </div>
    <div class="grid grid-3">
      <article class="card card-hover reveal">
        <div class="icon-box"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 2v6.6l5.4 9.4A2 2 0 0 1 16.7 21H7.3a2 2 0 0 1-1.7-3L11 8.6V2h2z"/></svg></div>
        <h3>Laboratory clearance</h3>
        <p>Every batch is tested in house for pH, total dissolved solids, turbidity, taste and odour, plus microbiological checks for total plate count, coliforms, E. coli and Pseudomonas. Product is held until it clears.</p>
      </article>
      <article class="card card-hover reveal">
        <div class="icon-box is-mint"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h4v4H4V4zm6 0h2v4h-2V4zm4 0h2v4h-2V4zm4 0h2v16h-2V4zM4 10h4v4H4v-4zm6 0h2v4h-2v-4zm4 0h2v4h-2v-4zM4 16h4v4H4v-4zm6 0h2v4h-2v-4zm4 0h2v4h-2v-4z"/></svg></div>
        <h3>Batch coding</h3>
        <p>Every bottle carries a batch number and filling date. From that code we can pull the production shift, every process parameter logged that day, the laboratory result and the delivery route it went out on.</p>
      </article>
      <article class="card card-hover reveal">
        <div class="icon-box is-sun"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1 3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 15-4-4 1.41-1.41L11 13.17l5.59-5.59L18 9l-7 7z"/></svg></div>
        <h3>Traceability in practice</h3>
        <p>This is why we ask for a photograph of the code when a customer reports a concern. It turns an investigation from approximate into exact, and it is how a quality problem gets fixed instead of repeated.</p>
      </article>
    </div>
  </div>
</section>

<section class="section section-dark">
  <div class="container container-narrow">
    <div class="section-head">
      <span class="eyebrow">Maintenance Discipline</span>
      <h2>Why the Process Keeps Working</h2>
    </div>
    <div class="prose" style="color:rgba(255,255,255,.86);">
      <p>A purification line that is installed correctly and then neglected produces good water for about six months and mediocre water thereafter. Consumables are replaced on scheduled service life at Pak-Everests, not on visible failure:</p>
      <ul>
        <li>Media beds are back washed on a fixed schedule and the media itself is replaced by service life</li>
        <li>Carbon is replaced before exhaustion, tested by free chlorine breakthrough rather than by guesswork</li>
        <li>Softener resin is regenerated on schedule with brine, with hardness checked after every regeneration</li>
        <li>Micron cartridges are replaced on pressure differential, never cleaned and reused</li>
        <li>Membranes are cleaned in place and monitored on permeate flow, reject flow and salt rejection</li>
        <li>Ultraviolet lamps are replaced on lamp hours, because output falls long before the lamp stops glowing</li>
        <li>Tanks, lines and the filling room are cleaned and sanitised on a documented schedule with sign off</li>
      </ul>
      <p>Any reading outside its control limit stops the line. Production does not resume until the cause is corrected and the parameter is back in range.</p>
    </div>
    <div class="cta-actions">
      <a class="btn btn-light btn-lg" href="<?= e(url('quality-assurance-policy')) ?>">Read the Quality Policy</a>
      <a class="btn btn-outline btn-lg" style="border-color:rgba(255,255,255,.4);color:#fff;" href="<?= e(url('minerals-and-benefits')) ?>">See the Mineral Profile</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="cta-band">
      <h2>Taste the difference eight stages make</h2>
      <p>Order a 19 litre refill with free delivery, or visit the plant in Gujar Khan and see the line for yourself. We are happy to show it.</p>
      <div class="cta-actions">
        <a class="btn btn-lg btn-light" href="<?= e(url('order')) ?>">Order Now</a>
        <a class="btn btn-lg btn-whatsapp" href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests, I would like to visit the plant.')) ?>" target="_blank" rel="noopener">Request a Plant Visit</a>
      </div>
    </div>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
