<?php
/** Distributor application form page. */
declare(strict_types=1);

$result = [];
if (is_post() && post('form_type') === 'distributor') {
    $result = handle_distributor_form();
}
$areas = coverage_areas();

seo_set([
    'title'       => 'Distributor Application Form',
    'description' => 'Apply online for a Pak-Everests water distributorship. Tell us your territory, storage capacity, delivery vehicle and target volume and our team will review your application.',
    'keywords'    => 'distributor application, water dealership form, become a distributor Pakistan',
    'breadcrumbs' => ['Distribution' => '/distribution', 'Application Form' => '/distributor-application'],
]);

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Distributor Application';
$heroSubtitle = 'Every application is reviewed by our team. Shortlisted applicants are invited to the plant at Gujar Khan.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container">
  <?php if (!empty($result['success'])):
      $successTitle = 'Application received';
      $successBody  = 'Our distribution team reviews every application and will contact you within three to five working days. Please keep your reference number for follow up.';
      require PE_ROOT . '/app/partials/form-success.php';
  else: ?>

    <div class="grid" style="grid-template-columns:minmax(0,1.6fr) minmax(270px,1fr);gap:32px;align-items:start;">
      <div>
        <?php require PE_ROOT . '/app/partials/form-errors.php'; ?>

        <form class="form-card" method="post" action="<?= e(url('distributor-application')) ?>" enctype="multipart/form-data" data-guard="true">
          <?= csrf_field() ?>
          <input type="hidden" name="form_type" value="distributor">
          <div class="hp-field" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

          <h2 style="font-size:1.2rem;">Applicant details</h2>
          <div class="form-grid">
            <div class="form-group">
              <label for="applicant_name">Full name <span class="req">*</span></label>
              <input type="text" id="applicant_name" name="applicant_name" required value="<?= e(post('applicant_name')) ?>" autocomplete="name">
            </div>
            <div class="form-group">
              <label for="business_name">Business / firm name</label>
              <input type="text" id="business_name" name="business_name" value="<?= e(post('business_name')) ?>">
            </div>
            <div class="form-group">
              <label for="cnic">CNIC number</label>
              <input type="text" id="cnic" name="cnic" value="<?= e(post('cnic')) ?>" placeholder="00000-0000000-0">
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
          </div>

          <hr>
          <h2 style="font-size:1.2rem;">Territory</h2>
          <div class="form-grid">
            <div class="form-group">
              <label for="city">City</label>
              <input type="text" id="city" name="city" value="<?= e(post('city')) ?>" placeholder="e.g. Rawalpindi">
            </div>
            <div class="form-group">
              <label for="area_requested">Territory you want to cover <span class="req">*</span></label>
              <input type="text" id="area_requested" name="area_requested" required value="<?= e(post('area_requested')) ?>"
                     list="areaList" placeholder="e.g. Kallar Syedan and surrounding villages">
              <datalist id="areaList">
                <?php foreach ($areas as $a): ?><option value="<?= e($a['area_name']) ?>"><?php endforeach; ?>
              </datalist>
            </div>
          </div>
          <div class="form-group">
            <label for="address">Business address</label>
            <textarea id="address" name="address" style="min-height:90px;" placeholder="Shop or warehouse address"><?= e(post('address')) ?></textarea>
          </div>

          <hr>
          <h2 style="font-size:1.2rem;">Business capacity</h2>
          <div class="form-grid">
            <div class="form-group">
              <label for="business_type">Current business type</label>
              <select id="business_type" name="business_type">
                <?php foreach (['General store / retail', 'Wholesale / distribution', 'FMCG distribution', 'Water supply', 'Transport', 'New to business', 'Other'] as $bt): ?>
                <option value="<?= e($bt) ?>" <?= post('business_type') === $bt ? 'selected' : '' ?>><?= e($bt) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="experience_years">Years of experience</label>
              <select id="experience_years" name="experience_years">
                <?php foreach (['No experience', 'Less than 1 year', '1 - 3 years', '3 - 5 years', 'More than 5 years'] as $ex): ?>
                <option value="<?= e($ex) ?>" <?= post('experience_years') === $ex ? 'selected' : '' ?>><?= e($ex) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="has_vehicle">Do you have a delivery vehicle?</label>
              <select id="has_vehicle" name="has_vehicle">
                <option value="Yes" <?= post('has_vehicle') === 'Yes' ? 'selected' : '' ?>>Yes</option>
                <option value="No" <?= post('has_vehicle') === 'No' ? 'selected' : '' ?>>No, but I will arrange one</option>
              </select>
            </div>
            <div class="form-group">
              <label for="vehicle_details">Vehicle details</label>
              <input type="text" id="vehicle_details" name="vehicle_details" value="<?= e(post('vehicle_details')) ?>" placeholder="e.g. Suzuki pickup, loader rickshaw">
            </div>
            <div class="form-group">
              <label for="has_storage">Do you have covered storage?</label>
              <select id="has_storage" name="has_storage">
                <option value="Yes" <?= post('has_storage') === 'Yes' ? 'selected' : '' ?>>Yes</option>
                <option value="No" <?= post('has_storage') === 'No' ? 'selected' : '' ?>>No, but I will arrange it</option>
              </select>
            </div>
            <div class="form-group">
              <label for="storage_details">Storage size and location</label>
              <input type="text" id="storage_details" name="storage_details" value="<?= e(post('storage_details')) ?>" placeholder="e.g. 400 sq ft covered godown">
            </div>
            <div class="form-group">
              <label for="investment_range">Investment capacity</label>
              <select id="investment_range" name="investment_range">
                <?php foreach (['Under Rs 100,000', 'Rs 100,000 - Rs 300,000', 'Rs 300,000 - Rs 500,000', 'Rs 500,000 - Rs 1,000,000', 'Above Rs 1,000,000'] as $inv): ?>
                <option value="<?= e($inv) ?>" <?= post('investment_range') === $inv ? 'selected' : '' ?>><?= e($inv) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="monthly_target">Monthly volume you can target</label>
              <select id="monthly_target" name="monthly_target">
                <?php foreach (['Under 500 bottles', '500 - 1,000 bottles', '1,000 - 3,000 bottles', '3,000 - 5,000 bottles', 'Above 5,000 bottles'] as $tg): ?>
                <option value="<?= e($tg) ?>" <?= post('monthly_target') === $tg ? 'selected' : '' ?>><?= e($tg) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="message">Tell us about your plan</label>
            <textarea id="message" name="message" placeholder="Which outlets, offices or institutions you already deal with, how you plan to build the route, and anything else we should know"><?= e(post('message')) ?></textarea>
          </div>

          <div class="form-group">
            <label for="document">Attach a document (optional)</label>
            <input type="file" id="document" name="document" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx">
            <span class="form-hint">CNIC copy, trade licence, warehouse photograph or a short business profile. Maximum 12 MB.</span>
          </div>

          <div class="check-row" style="margin-bottom:20px;">
            <input type="checkbox" id="agreeTerms" required>
            <label for="agreeTerms">I have read and accept the <a href="<?= e(url('distributor-terms')) ?>" target="_blank">distributor terms and conditions</a>.</label>
          </div>

          <button type="submit" class="btn btn-primary btn-lg">Submit Application</button>
        </form>
      </div>

      <aside>
        <div class="card" style="margin-bottom:20px;">
          <h2 style="font-size:1.1rem;">What happens next</h2>
          <ol style="font-size:.92rem;color:var(--text-soft);">
            <li>We acknowledge your application and give you a reference number.</li>
            <li>Our team reviews the territory against existing coverage.</li>
            <li>Shortlisted applicants are invited to the plant at Gujar Khan.</li>
            <li>A formal agreement sets out territory, pricing, security and targets.</li>
            <li>Opening stock, branding material and team training follow.</li>
          </ol>
        </div>
        <div class="card">
          <h2 style="font-size:1.1rem;">Prefer to talk first?</h2>
          <p style="font-size:.92rem;">Our distribution line is open <?= e((string) setting('hours_display')) ?>.</p>
          <a class="btn btn-whatsapp btn-block" href="<?= e(wa_link((string) (whatsapp_numbers()[1]['number'] ?? primary_whatsapp()), 'Hello, I am interested in a Pak-Everests distributorship.')) ?>" target="_blank" rel="noopener">WhatsApp Distribution</a>
          <a class="btn btn-outline btn-block" style="margin-top:10px;" href="<?= e(url('distribution')) ?>">Read the Overview</a>
        </div>
      </aside>
    </div>
  <?php endif; ?>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
