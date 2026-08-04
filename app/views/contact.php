<?php
/** Contact page with form, payment details and map. */
declare(strict_types=1);

$result = [];
if (is_post() && post('form_type') === 'contact') {
    $result = handle_contact_form('contact');
}
$areas = coverage_areas();

seo_set([
    'title'       => 'Contact Us',
    'description' => 'Contact Pak-Everests in Gujar Khan. WhatsApp 0333 5592206 or 0332 2901309, email info@pakeverests.site. Open 8 AM to 9 PM, seven days a week.',
    'keywords'    => 'contact water plant Gujar Khan, water supplier phone number, Pak-Everests contact, water delivery contact Rawalpindi',
    'breadcrumbs' => ['Contact Us' => '/contact'],
]);
seo_add_schema([
    '@context' => 'https://schema.org',
    '@type'    => 'ContactPage',
    'url'      => SITE_URL . '/contact',
    'mainEntity' => ['@id' => SITE_URL . '/#organization'],
]);

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Contact Pak-Everests';
$heroSubtitle = 'Orders, quotations, complaints, distribution enquiries and plant visits. We answer every message.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container">
    <div class="grid grid-4" style="margin-bottom:44px;">
      <?php foreach (whatsapp_numbers() as $wn): ?>
      <article class="card card-hover reveal" style="text-align:center;">
        <div class="icon-box is-mint" style="margin-inline:auto;">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
        </div>
        <h2 style="font-size:1.02rem;"><?= e($wn['label']) ?></h2>
        <p style="font-size:1.05rem;font-weight:700;color:var(--brand);"><?= e($wn['number']) ?></p>
        <a class="btn btn-whatsapp btn-sm" href="<?= e(wa_link($wn['number'], 'Hello Pak-Everests')) ?>" target="_blank" rel="noopener">WhatsApp</a>
      </article>
      <?php endforeach; ?>

      <article class="card card-hover reveal" style="text-align:center;">
        <div class="icon-box" style="margin-inline:auto;">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5z"/></svg>
        </div>
        <h2 style="font-size:1.02rem;">Email</h2>
        <p style="font-size:.95rem;font-weight:700;color:var(--brand);word-break:break-all;"><?= e(contact_email()) ?></p>
        <a class="btn btn-outline btn-sm" href="mailto:<?= e(contact_email()) ?>">Send Email</a>
      </article>

      <article class="card card-hover reveal" style="text-align:center;">
        <div class="icon-box is-sun" style="margin-inline:auto;">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>
        </div>
        <h2 style="font-size:1.02rem;">Plant address</h2>
        <p style="font-size:.88rem;"><?= e((string) setting('address_full')) ?></p>
        <a class="btn btn-outline btn-sm" href="<?= e((string) setting('map_directions_url', '#')) ?>" target="_blank" rel="noopener">Directions</a>
      </article>
    </div>

    <div class="grid" style="grid-template-columns:minmax(0,1.4fr) minmax(280px,1fr);gap:32px;align-items:start;">
      <div>
        <?php if (!empty($result['success'])):
            $successTitle = 'Message received. Thank you.';
            $successBody  = 'Our team replies to every message within one working day, and usually much faster on WhatsApp.';
            require PE_ROOT . '/app/partials/form-success.php';
        else: ?>
        <div class="form-card">
          <h2>Send us a message</h2>
          <p class="form-hint" style="margin-bottom:20px;">Everything submitted here reaches our admin panel and our email inbox immediately.</p>

          <?php require PE_ROOT . '/app/partials/form-errors.php'; ?>

          <form method="post" action="<?= e(url('contact')) ?>" data-guard="true">
            <?= csrf_field() ?>
            <input type="hidden" name="form_type" value="contact">
            <div class="hp-field" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

            <div class="form-grid">
              <div class="form-group">
                <label for="name">Your name <span class="req">*</span></label>
                <input type="text" id="name" name="name" required value="<?= e(post('name')) ?>" autocomplete="name">
              </div>
              <div class="form-group">
                <label for="phone">Phone / WhatsApp</label>
                <input type="tel" id="phone" name="phone" value="<?= e(post('phone')) ?>" autocomplete="tel" placeholder="03XX XXXXXXX">
              </div>
              <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" value="<?= e(post('email')) ?>" autocomplete="email">
              </div>
              <div class="form-group">
                <label for="area">Your area</label>
                <select id="area" name="area">
                  <option value="">Select area</option>
                  <?php foreach ($areas as $a): ?>
                  <option value="<?= e($a['area_name']) ?>" <?= post('area') === $a['area_name'] ? 'selected' : '' ?>><?= e($a['area_name']) ?></option>
                  <?php endforeach; ?>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="subject">Subject</label>
              <select id="subject" name="subject">
                <?php
                $subjects = ['New order enquiry', 'Existing order or delivery', 'Bulk or corporate quotation', 'Custom label printing',
                             'Water dispenser', 'Distribution enquiry', 'Bottle deposit or refund', 'Complaint', 'Plant visit request', 'Other'];
                $chosen = get('subject') ?: post('subject');
                foreach ($subjects as $s): ?>
                <option value="<?= e($s) ?>" <?= $chosen === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                <?php endforeach; ?>
                <?php if ($chosen && !in_array($chosen, $subjects, true)): ?>
                <option value="<?= e($chosen) ?>" selected><?= e($chosen) ?></option>
                <?php endif; ?>
              </select>
            </div>

            <div class="form-group">
              <label for="message">Your message <span class="req">*</span></label>
              <textarea id="message" name="message" required placeholder="Tell us what you need. If it relates to a specific delivery, please include the date and the batch code printed on the bottle."><?= e(post('message')) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
          </form>
        </div>
        <?php endif; ?>
      </div>

      <aside>
        <div class="card" style="margin-bottom:22px;">
          <h2 style="font-size:1.1rem;">Opening hours</h2>
          <p><?= e((string) setting('hours_display')) ?></p>
          <p class="form-hint">Deliveries run seven days a week including public holidays, unless announced otherwise on the news ticker.</p>
        </div>

        <div class="card">
          <h2 style="font-size:1.1rem;">Payment details</h2>
          <p class="form-hint" style="margin-bottom:14px;"><?= e((string) setting('payment_note')) ?></p>

          <?php if (setting_bool('easypaisa_enabled', true)): ?>
          <div style="padding:12px 0;border-bottom:1px dashed var(--border);">
            <strong>EasyPaisa</strong><br>
            <small><?= e((string) setting('easypaisa_title')) ?></small><br>
            <span style="font-weight:700;color:var(--brand);"><?= e((string) setting('easypaisa_number')) ?></span>
            <button type="button" class="btn btn-ghost btn-sm" data-copy="<?= e((string) setting('easypaisa_number')) ?>">Copy</button>
          </div>
          <?php endif; ?>

          <?php if (setting_bool('jazzcash_enabled', true)): ?>
          <div style="padding:12px 0;border-bottom:1px dashed var(--border);">
            <strong>JazzCash</strong><br>
            <small><?= e((string) setting('jazzcash_title')) ?></small><br>
            <span style="font-weight:700;color:var(--brand);"><?= e((string) setting('jazzcash_number')) ?></span>
            <button type="button" class="btn btn-ghost btn-sm" data-copy="<?= e((string) setting('jazzcash_number')) ?>">Copy</button>
          </div>
          <?php endif; ?>

          <?php if (setting_bool('bank_enabled', true)): ?>
          <div style="padding:12px 0;">
            <strong><?= e((string) setting('bank_name')) ?></strong><br>
            <small><?= e((string) setting('bank_branch')) ?></small>
            <table style="margin-top:8px;">
              <tbody>
                <tr><th scope="row" style="width:40%;">Title</th><td><?= e((string) setting('bank_account_title')) ?></td></tr>
                <tr><th scope="row">Account</th><td><?= e((string) setting('bank_account_number')) ?>
                  <button type="button" class="btn btn-ghost btn-sm" data-copy="<?= e((string) setting('bank_account_number')) ?>">Copy</button></td></tr>
                <tr><th scope="row">IBAN</th><td style="word-break:break-all;"><?= e((string) setting('bank_iban')) ?></td></tr>
                <?php if (setting('bank_swift')): ?>
                <tr><th scope="row">SWIFT</th><td><?= e((string) setting('bank_swift')) ?></td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>

          <p class="form-hint" style="margin-top:12px;">
            After any online payment please send the transaction screenshot to our WhatsApp so it can be matched to
            your order immediately.
          </p>
        </div>
      </aside>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Find Us</span>
      <h2>Our Location in Gujar Khan</h2>
      <p>Visit the plant to see the purification line, or use the bulk filling counter at Rs <?= e((string) setting('per_litre_rate', '6')) ?> per litre.</p>
    </div>
    <div class="map-embed">
      <?php require PE_ROOT . '/app/partials/map.php'; ?>
    </div>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
