<?php
/** Careers: job openings and applications. */
declare(strict_types=1);

$tab = get('tab', 'applications');

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'save_job':
            $slug = slugify(post('slug') ?: post('title'));
            if (fetch_val('SELECT id FROM job_openings WHERE slug = ? AND id <> ?', [$slug, $id])) {
                $slug .= '-' . substr((string) time(), -4);
            }
            $data = [
                'slug' => $slug, 'title' => post('title'), 'department' => post('department'),
                'location' => post('location'), 'job_type' => post('job_type'), 'salary_range' => post('salary_range'),
                'experience' => post('experience'), 'description' => post('description'), 'requirements' => post('requirements'),
                'positions' => max(1, (int) post('positions', 1)),
                'closing_date' => post('closing_date') ?: null,
                'is_active' => post('is_active') ? 1 : 0,
                'sort_order' => (int) post('sort_order', 0),
            ];
            if ($id > 0) { db_update('job_openings', $data, $id); flash('success', 'Vacancy updated.'); }
            else { $data['created_at'] = date('Y-m-d H:i:s'); $id = db_insert('job_openings', $data); flash('success', 'Vacancy created.'); }
            admin_log('Saved job opening', 'job_openings', $id);
            redirect('admin/careers?tab=jobs');
            break;

        case 'delete_job':
            db_delete('job_openings', $id);
            flash('success', 'Vacancy deleted.');
            redirect('admin/careers?tab=jobs');
            break;

        case 'toggle_job':
            q('UPDATE job_openings SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?', [$id]);
            redirect('admin/careers?tab=jobs');
            break;

        case 'app_status':
            if (in_array(post('status'), ['new', 'shortlisted', 'interviewed', 'hired', 'rejected'], true)) {
                db_update('job_applications', ['status' => post('status'), 'admin_note' => post('admin_note')], $id);
                admin_log('Updated job application', 'job_applications', $id);
                flash('success', 'Application updated.');
            }
            redirect('admin/careers');
            break;

        case 'delete_app':
            db_delete('job_applications', $id);
            flash('success', 'Application deleted.');
            redirect('admin/careers');
            break;
    }
}

if (get('export') === 'csv') {
    admin_export_csv('pakeverests-applications', fetch_all('SELECT * FROM job_applications ORDER BY created_at DESC'));
}

$editJob = get('edit');

/* ---- Job editor ---------------------------------------------------------- */
if ($editJob !== '') {
    $isNew = $editJob === 'new';
    $job = $isNew ? [
        'id' => 0, 'slug' => '', 'title' => '', 'department' => '', 'location' => 'Gujar Khan',
        'job_type' => 'Full Time', 'salary_range' => '', 'experience' => '', 'description' => '',
        'requirements' => '', 'positions' => 1, 'closing_date' => '', 'is_active' => 1, 'sort_order' => 0,
    ] : fetch_one('SELECT * FROM job_openings WHERE id = ?', [(int) $editJob]);
    if (!$job) { flash('error', 'Vacancy not found.'); redirect('admin/careers?tab=jobs'); }

    admin_header($isNew ? 'Post a vacancy' : 'Edit vacancy', 'Vacancies appear on the careers page with JobPosting schema', [
        ['label' => '← Careers', 'href' => admin_url('careers?tab=jobs'), 'class' => 'btn-ghost'],
    ]);
    ?>
    <form method="post" class="a-card" data-dirty-guard>
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save_job">
      <input type="hidden" name="id" value="<?= (int) $job['id'] ?>">
      <div class="form-row">
        <div class="form-group">
          <label for="title">Job title <span class="req">*</span></label>
          <input type="text" id="title" name="title" required value="<?= e((string) $job['title']) ?>" data-slug-source="slug">
        </div>
        <div class="form-group">
          <label for="slug">URL slug</label>
          <input type="text" id="slug" name="slug" value="<?= e((string) $job['slug']) ?>">
        </div>
        <div class="form-group">
          <label for="department">Department</label>
          <input type="text" id="department" name="department" value="<?= e((string) $job['department']) ?>">
        </div>
        <div class="form-group">
          <label for="location">Location</label>
          <input type="text" id="location" name="location" value="<?= e((string) $job['location']) ?>">
        </div>
        <div class="form-group">
          <label for="job_type">Employment type</label>
          <select id="job_type" name="job_type">
            <?php foreach (['Full Time', 'Part Time', 'Contract', 'Temporary', 'Internship'] as $t): ?>
            <option value="<?= e($t) ?>" <?= $job['job_type'] === $t ? 'selected' : '' ?>><?= e($t) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="salary_range">Salary range</label>
          <input type="text" id="salary_range" name="salary_range" value="<?= e((string) $job['salary_range']) ?>">
        </div>
        <div class="form-group">
          <label for="experience">Experience required</label>
          <input type="text" id="experience" name="experience" value="<?= e((string) $job['experience']) ?>">
        </div>
        <div class="form-group">
          <label for="positions">Number of positions</label>
          <input type="number" id="positions" name="positions" min="1" value="<?= (int) $job['positions'] ?>">
        </div>
        <div class="form-group">
          <label for="closing_date">Closing date</label>
          <input type="date" id="closing_date" name="closing_date" value="<?= e((string) $job['closing_date']) ?>">
        </div>
        <div class="form-group">
          <label for="sort_order">Display order</label>
          <input type="number" id="sort_order" name="sort_order" value="<?= (int) $job['sort_order'] ?>">
        </div>
      </div>
      <div class="form-group">
        <label for="description">Role description (HTML allowed)</label>
        <textarea id="description" name="description" class="code" style="min-height:170px;"><?= e((string) $job['description']) ?></textarea>
      </div>
      <div class="form-group">
        <label for="requirements">Requirements (one per line)</label>
        <textarea id="requirements" name="requirements" style="min-height:140px;"><?= e((string) $job['requirements']) ?></textarea>
      </div>
      <label class="check"><input type="checkbox" name="is_active" value="1" <?= (int) $job['is_active'] === 1 ? 'checked' : '' ?>> Publish this vacancy</label>
      <button class="btn btn-primary" type="submit">Save vacancy</button>
      <a class="btn btn-ghost" href="<?= e(admin_url('careers?tab=jobs')) ?>">Cancel</a>
    </form>
    <?php
    admin_footer();
    return;
}

admin_header('Careers', 'Vacancies and job applications', [
    ['label' => '+ Post a vacancy', 'href' => admin_url('careers?edit=new')],
    ['label' => 'Export applications', 'href' => admin_url('careers?export=csv'), 'class' => 'btn-ghost'],
]);
?>
<div class="tabs">
  <a class="tab<?= $tab === 'applications' ? ' is-active' : '' ?>" href="<?= e(admin_url('careers?tab=applications')) ?>">
    Applications (<?= (int) fetch_val('SELECT COUNT(*) FROM job_applications', [], 0) ?>)
  </a>
  <a class="tab<?= $tab === 'jobs' ? ' is-active' : '' ?>" href="<?= e(admin_url('careers?tab=jobs')) ?>">
    Vacancies (<?= (int) fetch_val('SELECT COUNT(*) FROM job_openings', [], 0) ?>)
  </a>
</div>

<?php if ($tab === 'jobs'):
  $jobs = fetch_all('SELECT * FROM job_openings ORDER BY sort_order ASC, id DESC'); ?>
<div class="a-card">
  <?php if (!$jobs): ?>
    <div class="empty">No vacancies posted. The careers page invites speculative applications when this list is empty.</div>
  <?php else: ?>
  <div class="a-table-wrap">
    <table class="a-table">
      <thead><tr><th>Title</th><th>Department</th><th>Location</th><th>Type</th><th>Positions</th><th>Applications</th><th>Active</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($jobs as $j): ?>
        <tr>
          <td><strong><?= e($j['title']) ?></strong><br><small style="color:var(--a-muted);"><?= e((string) $j['salary_range']) ?></small></td>
          <td><small><?= e((string) $j['department']) ?></small></td>
          <td><small><?= e((string) $j['location']) ?></small></td>
          <td><small><?= e($j['job_type']) ?></small></td>
          <td><?= (int) $j['positions'] ?></td>
          <td><?= (int) fetch_val('SELECT COUNT(*) FROM job_applications WHERE job_id = ?', [(int) $j['id']], 0) ?></td>
          <td>
            <form method="post" class="inline-form">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="toggle_job">
              <input type="hidden" name="id" value="<?= (int) $j['id'] ?>">
              <button type="submit" class="switch<?= (int) $j['is_active'] === 1 ? ' is-on' : '' ?>" aria-label="Toggle"><span></span></button>
            </form>
          </td>
          <td class="actions">
            <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('careers?edit=' . (int) $j['id'])) ?>">Edit</a>
            <form method="post" class="inline-form" data-confirm="Delete this vacancy?">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete_job">
              <input type="hidden" name="id" value="<?= (int) $j['id'] ?>">
              <button class="btn-icon btn-icon-danger" type="submit" title="Delete">
                <svg viewBox="0 0 24 24"><path d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php else:
  $list = admin_list('job_applications', ['order' => 'created_at DESC', 'perPage' => 30]); ?>
<div class="a-card">
  <?php if (!$list['rows']): ?>
    <div class="empty">No applications received yet.</div>
  <?php else: ?>
  <div class="a-table-wrap">
    <table class="a-table">
      <thead><tr><th>Reference</th><th>Applicant</th><th>Position</th><th>Experience</th><th>CV</th><th>Status</th><th>Received</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($list['rows'] as $a): ?>
        <tr<?= $a['status'] === 'new' ? ' style="font-weight:600;"' : '' ?>>
          <td><?= e($a['ref']) ?></td>
          <td><?= e($a['name']) ?><br><small style="color:var(--a-muted);"><?= e($a['phone']) ?><?= $a['city'] ? ' &middot; ' . e($a['city']) : '' ?></small></td>
          <td><small><?= e((string) $a['job_title']) ?></small></td>
          <td><small><?= e((string) $a['experience']) ?></small></td>
          <td>
            <?php if ($a['cv_path']): ?>
              <a class="btn btn-ghost btn-sm" href="/<?= e(ltrim($a['cv_path'], '/')) ?>" target="_blank" rel="noopener">Open CV</a>
            <?php else: ?><small>—</small><?php endif; ?>
          </td>
          <td>
            <form method="post" class="inline-form" style="display:flex;gap:6px;">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="app_status">
              <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
              <select name="status" onchange="this.form.submit()" style="padding:5px 8px;font-size:.78rem;">
                <?php foreach (['new', 'shortlisted', 'interviewed', 'hired', 'rejected'] as $s): ?>
                <option value="<?= e($s) ?>" <?= $a['status'] === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                <?php endforeach; ?>
              </select>
            </form>
          </td>
          <td><small><?= time_ago($a['created_at']) ?></small></td>
          <td class="actions">
            <a class="btn-icon" title="Call" href="tel:<?= e($a['phone']) ?>">
              <svg viewBox="0 0 24 24"><path d="M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.6.1.3 0 .7-.2 1l-2.3 2.2z"/></svg>
            </a>
            <form method="post" class="inline-form" data-confirm="Delete this application?">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete_app">
              <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
              <button class="btn-icon btn-icon-danger" type="submit" title="Delete">
                <svg viewBox="0 0 24 24"><path d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?= pagination_links($list['pagination'], admin_url('careers?tab=applications')) ?>
  <?php endif; ?>
</div>
<?php endif; ?>
<?php admin_footer(); ?>
