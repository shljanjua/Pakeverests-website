<?php
/** Custom label request form. */
declare(strict_types=1);

$result = [];
if (is_post() && post('form_type') === 'label') {
    $result = handle_label_form();
}

seo_set([
    'title'       => 'Label Customisation Request Form',
    'description' => 'Submit your custom water bottle label brief: bottle size, quantity, occasion, artwork and brand colours. Pak-Everests issues a written quotation the same day.',
    'keywords'    => 'custom label form, personalised water bottle order, branded bottle quotation',
    'breadcrumbs' => ['Custom Label Bottles' => '/custom-label-bottles', 'Request Form' => '/custom-label-request'],
]);

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Label Customisation Request';
$heroSubtitle = 'Tell us what you need and attach whatever artwork you have. If you have none, our design team will create it.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container">
  <?php if (!empty($result['success'])):
      $successTitle = 'Label brief received';
      $successBody  = 'Our design team will review your brief and send a written quotation, normally the same working day. Nothing goes to print until you approve a digital proof.';
      require PE_ROOT . '/app/partials/form-success.php';
  else: ?>

    <div class="grid" style="grid-template-columns:minmax(0,1.6fr) minmax(270px,1fr);gap:32px;align-items:start;">
      <div>
        <?php require PE_ROOT . '/app/partials/form-errors.php'; ?>

        <form class="form-card" method="post" action="<?= e(url('custom-label-request')) ?>" enctype="multipart/form-data" data-guard="true">
          <?= csrf_field() ?>
          <input type="hidden" name="form_type" value="label">
          <div class="hp-field" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

          <h2 style="font-size:1.2rem;">Your details</h2>
          <div class="form-grid">
            <div class="form-group">
              <label for="contact_name">Contact name <span class="req">*</span></label>
              <input type="text" id="contact_name" name="contact_name" required value="<?= e(post('contact_name')) ?>" autocomplete="name">
            </div>
            <div class="form-group">
              <label for="company_name">Company / family name</label>
              <input type="text" id="company_name" name="company_name" value="<?= e(post('company_name')) ?>" placeholder="Appears on the label">
            </div>
            <div class="form-group">
              <label for="designation">Designation</label>
              <input type="text" id="designation" name="designation" value="<?= e(post('designation')) ?>">
            </div>
            <div class="form-group">
              <label for="phone">Mobile number <span class="req">*</span></label>
              <input type="tel" id="phone" name="phone" required value="<?= e(post('phone')) ?>" placeholder="03XX XXXXXXX">
            </div>
            <div class="form-group">
              <label for="whatsapp">WhatsApp number</label>
              <input type="tel" id="whatsapp" name="whatsapp" value="<?= e(post('whatsapp')) ?>">
            </div>
            <div class="form-group">
              <label for="email">Email address</label>
              <input type="email" id="email" name="email" value="<?= e(post('email')) ?>">
            </div>
            <div class="form-group">
              <label for="city">City</label>
              <input type="text" id="city" name="city" value="<?= e(post('city')) ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="address">Delivery address</label>
            <textarea id="address" name="address" style="min-height:80px;"><?= e(post('address')) ?></textarea>
          </div>

          <hr>
          <h2 style="font-size:1.2rem;">Your requirement</h2>
          <div class="form-group">
            <label>Bottle size <span class="req">*</span></label>
            <div class="radio-cards">
              <?php foreach (['500 ml (Pack of 12)', '1.5 Litre (Pack of 6)', '350 ml (at launch)', 'Mixed sizes'] as $bs):
                $checked = post('bottle_size') === $bs ? 'checked' : ''; ?>
              <label class="radio-card">
                <input type="radio" name="bottle_size" value="<?= e($bs) ?>" <?= $checked ?> required>
                <span><?= e($bs) ?></span>
              </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label for="quantity">Approximate quantity <span class="req">*</span></label>
              <select id="quantity" name="quantity" required>
                <option value="">Select quantity</option>
                <?php foreach (['Under 500 bottles', '500 - 1,000 bottles', '1,000 - 3,000 bottles', '3,000 - 5,000 bottles', '5,000 - 10,000 bottles', 'Above 10,000 bottles'] as $q): ?>
                <option value="<?= e($q) ?>" <?= post('quantity') === $q ? 'selected' : '' ?>><?= e($q) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="occasion">Occasion / purpose</label>
              <select id="occasion" name="occasion">
                <?php foreach (['Wedding / Barat / Walima', 'Mehndi', 'Aqiqah', 'Corporate office use', 'Conference / Seminar', 'Exhibition / Stand', 'Hotel / Restaurant', 'Clinic / Hospital', 'Campaign / Welfare', 'Other'] as $oc): ?>
                <option value="<?= e($oc) ?>" <?= post('occasion') === $oc ? 'selected' : '' ?>><?= e($oc) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="required_date">Date you need them by</label>
              <input type="date" id="required_date" name="required_date" value="<?= e(post('required_date')) ?>" min="<?= date('Y-m-d') ?>">
              <span class="form-hint">Allow 7 to 12 working days from proof approval.</span>
            </div>
            <div class="form-group">
              <label for="brand_colors">Brand colours</label>
              <input type="text" id="brand_colors" name="brand_colors" value="<?= e(post('brand_colors')) ?>" placeholder="e.g. navy blue and gold, or #0b6fa4">
            </div>
          </div>

          <hr>
          <h2 style="font-size:1.2rem;">Artwork</h2>
          <div class="form-group">
            <label>Do you have print ready artwork?</label>
            <div class="radio-cards">
              <?php foreach (['Yes, attached', 'Only a logo', 'Nothing yet'] as $ha):
                $checked = post('has_artwork') === $ha ? 'checked' : ''; ?>
              <label class="radio-card">
                <input type="radio" name="has_artwork" value="<?= e($ha) ?>" <?= $checked ?>>
                <span><?= e($ha) ?></span>
              </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label for="artwork">Upload artwork</label>
              <input type="file" id="artwork" name="artwork" accept=".pdf,.jpg,.jpeg,.png,.webp,.svg">
              <span class="form-hint">AI, PSD, PDF, EPS or 300 DPI PNG. Maximum 12 MB.</span>
            </div>
            <div class="form-group">
              <label for="logo">Upload logo</label>
              <input type="file" id="logo" name="logo" accept=".pdf,.jpg,.jpeg,.png,.webp,.svg">
              <span class="form-hint">The highest resolution version you have.</span>
            </div>
          </div>

          <div class="form-group">
            <label for="label_text">Exact text for the label</label>
            <textarea id="label_text" name="label_text" placeholder="Names, dates, taglines, contact details. Please write them exactly as they should be printed, spelling included."><?= e(post('label_text')) ?></textarea>
            <span class="form-hint">Check spellings carefully. Once a proof is approved and printing begins, the order cannot be changed.</span>
          </div>

          <div class="form-group">
            <label>Do you want our design team to create the artwork?</label>
            <div class="radio-cards">
              <?php foreach (['Yes please', 'No, mine is ready', 'Show me options'] as $dh):
                $checked = post('design_help') === $dh ? 'checked' : ''; ?>
              <label class="radio-card">
                <input type="radio" name="design_help" value="<?= e($dh) ?>" <?= $checked ?>>
                <span><?= e($dh) ?></span>
              </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="form-group">
            <label for="message">Anything else</label>
            <textarea id="message" name="message" style="min-height:90px;" placeholder="Reference designs you like, delivery instructions, budget guidance"><?= e(post('message')) ?></textarea>
          </div>

          <div class="check-row" style="margin-bottom:20px;">
            <input type="checkbox" id="agreeArt" required>
            <label for="agreeArt">
              I confirm I own or hold the rights to use the artwork and logo I am submitting, and I accept the
              <a href="<?= e(url('terms-and-conditions')) ?>" target="_blank">terms and conditions</a> for custom label orders.
            </label>
          </div>

          <button type="submit" class="btn btn-primary btn-lg">Send My Label Brief</button>
        </form>
      </div>

      <aside>
        <div class="card" style="margin-bottom:20px;">
          <h2 style="font-size:1.1rem;">How it works</h2>
          <ol style="font-size:.92rem;color:var(--text-soft);">
            <li>You send this brief.</li>
            <li>We issue a written quotation, normally the same day.</li>
            <li>Our design team prepares a digital proof.</li>
            <li>You approve the proof in writing or on WhatsApp.</li>
            <li>Labels print, bottles fill, delivery follows.</li>
          </ol>
          <p class="form-hint">Nothing is printed and nothing is charged until you approve the proof.</p>
        </div>
        <div class="card">
          <h2 style="font-size:1.1rem;">Need it urgently?</h2>
          <p style="font-size:.92rem;">Send the brief here first, then message us so we can flag it for rush handling.</p>
          <a class="btn btn-whatsapp btn-block" href="<?= e(wa_link(primary_whatsapp(), 'Hello, I have submitted a custom label brief and need it urgently.')) ?>" target="_blank" rel="noopener">WhatsApp Our Team</a>
        </div>
      </aside>
    </div>
  <?php endif; ?>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
