<?php
/** FAQ page. */
declare(strict_types=1);

$faqs = fetch_all('SELECT * FROM faqs WHERE is_active = 1 ORDER BY sort_order ASC, id ASC');
$grouped = [];
foreach ($faqs as $f) {
    $grouped[$f['category']][] = $f;
}

seo_set([
    'title'       => 'Frequently Asked Questions',
    'description' => 'Answers on water prices, the Rs 1,500 bottle deposit, delivery areas and timing, quality approvals, custom labels, dispensers and payment.',
    'keywords'    => 'water delivery FAQ, 19 litre bottle price, water bottle deposit, mineral water questions Pakistan',
    'breadcrumbs' => ['FAQs' => '/faqs'],
]);
if ($faqs) {
    seo_add_schema(schema_faq($faqs));
}

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Frequently Asked Questions';
$heroSubtitle = 'Prices, deposits, delivery, quality, custom labels, dispensers and distribution, answered in full.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container container-narrow">
    <?php if (!$faqs): ?>
      <div class="empty-state"><p>No questions have been published yet.</p></div>
    <?php else: ?>
      <?php foreach ($grouped as $cat => $items): ?>
        <h2 style="margin-top:2rem;"><?= e($cat) ?></h2>
        <div class="accordion" data-single="false" style="margin-bottom:28px;">
          <?php foreach ($items as $f): ?>
          <div class="accordion-item">
            <button class="accordion-trigger" type="button">
              <span><?= e($f['question']) ?></span>
              <span class="accordion-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 10l5 5 5-5z"/></svg></span>
            </button>
            <div class="accordion-panel"><div class="accordion-body"><?= rich_text($f['answer']) ?></div></div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="cta-band">
      <h2>Question not answered here?</h2>
      <p>Send it to us on WhatsApp or through the contact form. We answer every message, and useful questions get added to this page.</p>
      <div class="cta-actions">
        <a class="btn btn-lg btn-whatsapp" href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests, I have a question: ')) ?>" target="_blank" rel="noopener">Ask on WhatsApp</a>
        <a class="btn btn-lg btn-light" href="<?= e(url('contact')) ?>">Contact Form</a>
      </div>
    </div>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
