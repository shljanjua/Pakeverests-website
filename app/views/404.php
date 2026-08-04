<?php
/** 404 page. */
declare(strict_types=1);

http_response_code(404);
seo_set([
    'title'       => 'Page Not Found',
    'description' => 'The page you were looking for could not be found on pakeverests.site.',
    'robots'      => 'noindex, follow',
]);

require PE_ROOT . '/app/partials/header.php';
?>
<section class="section">
  <div class="container container-narrow" style="text-align:center;">
    <div class="icon-box" style="margin:0 auto 22px;width:80px;height:80px;border-radius:26px;">
      <svg viewBox="0 0 24 24" aria-hidden="true" style="width:40px;height:40px;"><path d="M12 2c-3.2 4.4-6 7.6-6 11a6 6 0 0 0 12 0c0-3.4-2.8-6.6-6-11z"/></svg>
    </div>
    <h1>This page has run dry</h1>
    <p style="font-size:1.05rem;color:var(--text-soft);">
      The page you were looking for does not exist, or it has been moved. Here are the places most people are heading.
    </p>

    <div class="grid grid-3" style="margin-top:36px;text-align:left;">
      <a class="card card-hover" href="<?= e(url('products')) ?>" style="text-decoration:none;">
        <h2 style="font-size:1.05rem;">Products &amp; Prices</h2>
        <p style="font-size:.9rem;">The full range with transparent pricing and deposit terms.</p>
      </a>
      <a class="card card-hover" href="<?= e(url('order')) ?>" style="text-decoration:none;">
        <h2 style="font-size:1.05rem;">Place an Order</h2>
        <p style="font-size:.9rem;">Free delivery across the whole coverage area, no minimum order.</p>
      </a>
      <a class="card card-hover" href="<?= e(url('coverage-areas')) ?>" style="text-decoration:none;">
        <h2 style="font-size:1.05rem;">Delivery Areas</h2>
        <p style="font-size:.9rem;">Check whether we reach your street and how often.</p>
      </a>
      <a class="card card-hover" href="<?= e(url('purification-process')) ?>" style="text-decoration:none;">
        <h2 style="font-size:1.05rem;">8 Stage Process</h2>
        <p style="font-size:.9rem;">How raw groundwater becomes sealed mineral water.</p>
      </a>
      <a class="card card-hover" href="<?= e(url('faqs')) ?>" style="text-decoration:none;">
        <h2 style="font-size:1.05rem;">FAQs</h2>
        <p style="font-size:.9rem;">Prices, deposits, delivery, quality and dispensers.</p>
      </a>
      <a class="card card-hover" href="<?= e(url('contact')) ?>" style="text-decoration:none;">
        <h2 style="font-size:1.05rem;">Contact Us</h2>
        <p style="font-size:.9rem;">WhatsApp, email, plant address and payment details.</p>
      </a>
    </div>

    <div class="cta-actions" style="margin-top:34px;">
      <a class="btn btn-primary btn-lg" href="<?= e(url('/')) ?>">Back to Home</a>
      <a class="btn btn-whatsapp btn-lg" href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests')) ?>" target="_blank" rel="noopener">WhatsApp Us</a>
    </div>
  </div>
</section>
<?php require PE_ROOT . '/app/partials/footer.php'; ?>
