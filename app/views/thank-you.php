<?php
/** Generic thank you page. */
declare(strict_types=1);

seo_set([
    'title'       => 'Thank You',
    'description' => 'Thank you for contacting Pak-Everests Bottled Drinking Water.',
    'robots'      => 'noindex, follow',
]);

require PE_ROOT . '/app/partials/header.php';
?>
<section class="section">
  <div class="container container-narrow">
    <div class="success-panel">
      <div class="success-icon">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg>
      </div>
      <h1>Thank you</h1>
      <p>We have received your submission and our team will be in touch shortly.</p>
      <div class="cta-actions">
        <a class="btn btn-primary btn-lg" href="<?= e(url('/')) ?>">Back to Home</a>
        <a class="btn btn-whatsapp btn-lg" href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests')) ?>" target="_blank" rel="noopener">WhatsApp Us</a>
      </div>
    </div>
  </div>
</section>
<?php require PE_ROOT . '/app/partials/footer.php'; ?>
