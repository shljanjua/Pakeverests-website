<?php
/** Careers page with application form. */
declare(strict_types=1);

$result = [];
if (is_post() && post('form_type') === 'career') {
    $result = handle_job_form();
}

$jobs = fetch_all('SELECT * FROM job_openings WHERE is_active = 1 ORDER BY sort_order ASC, id ASC');

seo_set([
    'title'       => 'Careers at Pak-Everests',
    'description' => 'Job openings at Pak-Everests in Gujar Khan and Rawalpindi: delivery riders, plant operators, sales officers and support staff. Apply online.',
    'keywords'    => 'jobs in Gujar Khan, water plant jobs, delivery rider jobs Rawalpindi, careers Pak-Everests',
    'breadcrumbs' => ['Careers' => '/careers'],
]);
if ($jobs) {
    foreach ($jobs as $j) {
        seo_add_schema([
            '@context'    => 'https://schema.org',
            '@type'       => 'JobPosting',
            'title'       => $j['title'],
            'description' => strip_tags((string) $j['description']) . ' Requirements: ' . str_replace("\n", '; ', (string) $j['requirements']),
            'datePosted'  => date('Y-m-d', strtotime((string) $j['created_at'])),
            'employmentType' => strtoupper(str_replace(' ', '_', (string) $j['job_type'])),
            'hiringOrganization' => ['@id' => SITE_URL . '/#organization'],
            'jobLocation' => [
                '@type'   => 'Place',
                'address' => [
                    '@type'           => 'PostalAddress',
                    'addressLocality' => $j['location'],
                    'addressRegion'   => 'Punjab',
                    'addressCountry'  => 'PK',
                ],
            ],
            'validThrough' => $j['closing_date'] ? date('c', strtotime((string) $j['closing_date'])) : date('c', strtotime('+90 days')),
        ]);
    }
}

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Careers at Pak-Everests';
$heroSubtitle = 'We hire locally, train properly and pay on time. If that sounds like the kind of employer you want, look at what is open.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container">
    <div class="grid grid-4" style="margin-bottom:44px;">
      <?php
      $perks = [
        ['Pay on time, every month', 'Salaries are paid on a fixed date. No delays, no excuses, no partial payments.'],
        ['Local jobs for local people', 'Our riders, operators and support staff come from Gujar Khan and the surrounding towns.'],
        ['Training that is real', 'Product, hygiene, handling and customer service training, not a one hour induction.'],
        ['Room to move up', 'Riders become route supervisors, operators become shift leads. We promote from inside first.'],
      ];
      foreach ($perks as [$t, $b]): ?>
      <article class="card reveal">
        <h2 style="font-size:1.02rem;"><?= e($t) ?></h2>
        <p style="font-size:.9rem;"><?= e($b) ?></p>
      </article>
      <?php endforeach; ?>
    </div>

    <div class="section-head">
      <span class="eyebrow">Open Positions</span>
      <h2>Current Vacancies</h2>
    </div>

    <?php if (!$jobs): ?>
      <div class="empty-state">
        <p>There are no open positions at the moment. You are welcome to send a speculative application using the form below and we will keep it on file.</p>
      </div>
    <?php else: ?>
      <div class="accordion" data-single="false">
        <?php foreach ($jobs as $j): ?>
        <div class="accordion-item">
          <button class="accordion-trigger" type="button">
            <span>
              <?= e($j['title']) ?>
              <small style="display:block;font-weight:500;color:var(--text-muted);font-size:.82rem;margin-top:3px;">
                <?= e($j['department']) ?> &middot; <?= e($j['location']) ?> &middot; <?= e($j['job_type']) ?>
                <?php if ($j['salary_range']): ?> &middot; <?= e($j['salary_range']) ?><?php endif; ?>
              </small>
            </span>
            <span class="accordion-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 10l5 5 5-5z"/></svg></span>
          </button>
          <div class="accordion-panel">
            <div class="accordion-body">
              <div class="prose" style="font-size:.95rem;"><?= rich_text($j['description']) ?></div>
              <?php if ($j['requirements']): ?>
                <h3 style="font-size:1rem;margin-top:16px;">Requirements</h3>
                <ul>
                  <?php foreach (array_filter(array_map('trim', explode("\n", (string) $j['requirements']))) as $req): ?>
                    <li><?= e($req) ?></li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
              <p class="form-hint">
                Positions available: <?= (int) $j['positions'] ?>
                <?php if ($j['experience']): ?> &middot; Experience: <?= e($j['experience']) ?><?php endif; ?>
                <?php if ($j['closing_date']): ?> &middot; Closing: <?= pretty_date($j['closing_date']) ?><?php endif; ?>
              </p>
              <a class="btn btn-primary btn-sm" href="#apply" onclick="document.getElementById('job_title').value=<?= ejs($j['title']) ?>;document.getElementById('job_id').value=<?= ejs((string) $j['id']) ?>;">Apply for this role</a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="section section-soft" id="apply">
  <div class="container container-narrow">
    <div class="section-head">
      <span class="eyebrow">Apply</span>
      <h2>Send Your Application</h2>
    </div>

    <?php if (!empty($result['success'])):
      $successTitle = 'Application received';
      $successBody  = 'Thank you for your interest in Pak-Everests. Our team reviews every application and will contact shortlisted candidates directly.';
      require PE_ROOT . '/app/partials/form-success.php';
    else: ?>
      <?php require PE_ROOT . '/app/partials/form-errors.php'; ?>
      <form class="form-card" method="post" action="<?= e(url('careers')) ?>#apply" enctype="multipart/form-data" data-guard="true">
        <?= csrf_field() ?>
        <input type="hidden" name="form_type" value="career">
        <input type="hidden" name="job_id" id="job_id" value="<?= e(post('job_id')) ?>">
        <div class="hp-field" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

        <div class="form-grid">
          <div class="form-group is-full">
            <label for="job_title">Position applying for <span class="req">*</span></label>
            <input list="jobList" type="text" id="job_title" name="job_title" required value="<?= e(post('job_title')) ?>" placeholder="Select or type a position">
            <datalist id="jobList">
              <?php foreach ($jobs as $j): ?><option value="<?= e($j['title']) ?>"><?php endforeach; ?>
              <option value="Speculative application">
            </datalist>
          </div>
          <div class="form-group">
            <label for="name">Full name <span class="req">*</span></label>
            <input type="text" id="name" name="name" required value="<?= e(post('name')) ?>" autocomplete="name">
          </div>
          <div class="form-group">
            <label for="phone">Mobile number <span class="req">*</span></label>
            <input type="tel" id="phone" name="phone" required value="<?= e(post('phone')) ?>" placeholder="03XX XXXXXXX">
          </div>
          <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" value="<?= e(post('email')) ?>">
          </div>
          <div class="form-group">
            <label for="city">City / area</label>
            <input type="text" id="city" name="city" value="<?= e(post('city')) ?>">
          </div>
          <div class="form-group">
            <label for="experience">Total experience</label>
            <select id="experience" name="experience">
              <?php foreach (['Fresh candidate', 'Less than 1 year', '1 - 3 years', '3 - 5 years', 'More than 5 years'] as $ex): ?>
              <option value="<?= e($ex) ?>" <?= post('experience') === $ex ? 'selected' : '' ?>><?= e($ex) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="education">Education</label>
            <input type="text" id="education" name="education" value="<?= e(post('education')) ?>" placeholder="e.g. Intermediate, DAE, BSc">
          </div>
        </div>

        <div class="form-group">
          <label for="cover_note">Why you are a good fit</label>
          <textarea id="cover_note" name="cover_note" placeholder="A few lines about your experience and why you want this role"><?= e(post('cover_note')) ?></textarea>
        </div>

        <div class="form-group">
          <label for="cv">Attach your CV</label>
          <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp">
          <span class="form-hint">PDF or Word preferred. A clear photograph of a printed CV is also accepted. Maximum 12 MB.</span>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">Submit Application</button>
      </form>
    <?php endif; ?>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
