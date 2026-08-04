<?php
/** Distributor applications. */
declare(strict_types=1);

$statuses = ['new', 'reviewing', 'approved', 'rejected', 'on_hold'];
$viewId   = (int) get('view', 0);

if (is_post()) {
    $id = (int) post('id');
    if (post('action') === 'status' && $id > 0 && in_array(post('status'), $statuses, true)) {
        db_update('distributor_applications', ['status' => post('status'), 'admin_note' => post('admin_note')], $id);
        admin_log('Updated distributor application', 'distributor_applications', $id);
        flash('success', 'Application updated.');
        redirect('admin/distributors?view=' . $id);
    }
    if (post('action') === 'delete') {
        db_delete('distributor_applications', $id);
        admin_log('Deleted distributor application', 'distributor_applications', $id);
        flash('success', 'Application deleted.');
        redirect('admin/distributors');
    }
}

if (get('export') === 'csv') {
    admin_export_csv('pakeverests-distributors', fetch_all('SELECT * FROM distributor_applications ORDER BY created_at DESC'));
}

if ($viewId > 0) {
    $d = fetch_one('SELECT * FROM distributor_applications WHERE id = ?', [$viewId]);
    if (!$d) { flash('error', 'Application not found.'); redirect('admin/distributors'); }

    admin_header('Application ' . $d['ref'], $d['applicant_name'] . ' — ' . $d['area_requested'], [
        ['label' => '← All applications', 'href' => admin_url('distributors'), 'class' => 'btn-ghost'],
    ]);
    ?>
    <div class="a-grid" style="grid-template-columns:minmax(0,2fr) minmax(280px,1fr);gap:20px;align-items:start;">
      <div class="a-card">
        <div class="a-card-head"><h2>Application details</h2><?= status_pill($d['status']) ?></div>
        <dl class="detail-list">
          <div class="detail-row"><dt>Applicant</dt><dd><?= e($d['applicant_name']) ?></dd></div>
          <div class="detail-row"><dt>Business name</dt><dd><?= e((string) $d['business_name']) ?: '—' ?></dd></div>
          <div class="detail-row"><dt>CNIC</dt><dd><?= e((string) $d['cnic']) ?: '—' ?></dd></div>
          <div class="detail-row"><dt>Phone</dt><dd><a href="tel:<?= e($d['phone']) ?>"><?= e($d['phone']) ?></a></dd></div>
          <div class="detail-row"><dt>WhatsApp</dt><dd><a href="<?= e(wa_link((string) $d['whatsapp'])) ?>" target="_blank" rel="noopener"><?= e((string) $d['whatsapp']) ?></a></dd></div>
          <div class="detail-row"><dt>Email</dt><dd><?= $d['email'] ? '<a href="mailto:' . e($d['email']) . '">' . e($d['email']) . '</a>' : '—' ?></dd></div>
          <div class="detail-row"><dt>City</dt><dd><?= e((string) $d['city']) ?: '—' ?></dd></div>
          <div class="detail-row"><dt>Territory requested</dt><dd><strong><?= e($d['area_requested']) ?></strong></dd></div>
          <div class="detail-row"><dt>Address</dt><dd><?= nl2br(e((string) $d['address'])) ?: '—' ?></dd></div>
          <div class="detail-row"><dt>Business type</dt><dd><?= e((string) $d['business_type']) ?></dd></div>
          <div class="detail-row"><dt>Experience</dt><dd><?= e((string) $d['experience_years']) ?></dd></div>
          <div class="detail-row"><dt>Vehicle</dt><dd><?= e((string) $d['has_vehicle']) ?> <?= e((string) $d['vehicle_details']) ?></dd></div>
          <div class="detail-row"><dt>Storage</dt><dd><?= e((string) $d['has_storage']) ?> <?= e((string) $d['storage_details']) ?></dd></div>
          <div class="detail-row"><dt>Investment capacity</dt><dd><?= e((string) $d['investment_range']) ?></dd></div>
          <div class="detail-row"><dt>Monthly target</dt><dd><?= e((string) $d['monthly_target']) ?></dd></div>
          <div class="detail-row"><dt>Message</dt><dd><?= nl2br(e((string) $d['message'])) ?: '—' ?></dd></div>
          <div class="detail-row"><dt>Attached document</dt>
            <dd><?= $d['document_path'] ? '<a href="/' . e(ltrim($d['document_path'], '/')) . '" target="_blank" rel="noopener">Open document</a>' : '—' ?></dd></div>
          <div class="detail-row"><dt>Submitted</dt><dd><?= pretty_date($d['created_at'], 'd M Y, g:i A') ?></dd></div>
        </dl>
      </div>

      <aside>
        <div class="a-card">
          <div class="a-card-head"><h2>Decision</h2></div>
          <form method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="status">
            <input type="hidden" name="id" value="<?= (int) $d['id'] ?>">
            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" name="status">
                <?php foreach ($statuses as $s): ?>
                <option value="<?= e($s) ?>" <?= $d['status'] === $s ? 'selected' : '' ?>><?= e(ucwords(str_replace('_', ' ', $s))) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="admin_note">Internal note</label>
              <textarea id="admin_note" name="admin_note"><?= e((string) $d['admin_note']) ?></textarea>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Save</button>
          </form>
        </div>

        <div class="a-card">
          <div class="a-card-head"><h2>Next steps</h2></div>
          <a class="btn btn-primary btn-block" target="_blank" rel="noopener"
             href="<?= e(wa_link((string) ($d['whatsapp'] ?: $d['phone']), 'Hello ' . $d['applicant_name'] . ', thank you for your Pak-Everests distributor application (' . $d['ref'] . ').')) ?>">
            Message the applicant
          </a>
          <a class="btn btn-ghost btn-block" style="margin-top:9px;"
             href="<?= e(admin_url('agreements?edit=new&type=distributor_agreement&name=' . rawurlencode($d['applicant_name']) . '&phone=' . rawurlencode($d['phone']) . '&territory=' . rawurlencode($d['area_requested']) . '&email=' . rawurlencode((string) $d['email']))) ?>">
            Generate distributor agreement
          </a>
        </div>

        <div class="a-card">
          <form method="post" data-confirm="Delete this application permanently?">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $d['id'] ?>">
            <button class="btn btn-danger btn-block" type="submit">Delete application</button>
          </form>
        </div>
      </aside>
    </div>
    <?php
    admin_footer();
    return;
}

$filterStatus = get('status');
$where = ['1=1']; $params = [];
if ($filterStatus !== '' && in_array($filterStatus, $statuses, true)) { $where[] = 'status = ?'; $params[] = $filterStatus; }

$list = admin_list('distributor_applications', ['where' => implode(' AND ', $where), 'params' => $params, 'order' => 'created_at DESC', 'perPage' => 30]);

admin_header('Distributor Applications', 'Territory applications submitted through the website', [
    ['label' => 'Export CSV', 'href' => admin_url('distributors?export=csv'), 'class' => 'btn-ghost'],
]);
?>
<div class="a-card">
  <div class="tabs">
    <a class="tab<?= $filterStatus === '' ? ' is-active' : '' ?>" href="<?= e(admin_url('distributors')) ?>">All</a>
    <?php foreach ($statuses as $s): ?>
    <a class="tab<?= $filterStatus === $s ? ' is-active' : '' ?>" href="<?= e(admin_url('distributors?status=' . $s)) ?>"><?= e(ucwords(str_replace('_', ' ', $s))) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (!$list['rows']): ?>
    <div class="empty">No applications found.</div>
  <?php else: ?>
  <div class="a-table-wrap">
    <table class="a-table">
      <thead><tr><th>Reference</th><th>Applicant</th><th>Territory</th><th>Vehicle</th><th>Storage</th><th>Target</th><th>Status</th><th>Received</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($list['rows'] as $d): ?>
        <tr<?= $d['status'] === 'new' ? ' style="font-weight:600;"' : '' ?>>
          <td><a href="<?= e(admin_url('distributors?view=' . (int) $d['id'])) ?>"><?= e($d['ref']) ?></a></td>
          <td><?= e($d['applicant_name']) ?><br><small style="color:var(--a-muted);"><?= e($d['phone']) ?></small></td>
          <td><?= e($d['area_requested']) ?></td>
          <td><small><?= e((string) $d['has_vehicle']) ?></small></td>
          <td><small><?= e((string) $d['has_storage']) ?></small></td>
          <td><small><?= e((string) $d['monthly_target']) ?></small></td>
          <td><?= status_pill($d['status']) ?></td>
          <td><small><?= time_ago($d['created_at']) ?></small></td>
          <td class="actions"><a class="btn btn-ghost btn-sm" href="<?= e(admin_url('distributors?view=' . (int) $d['id'])) ?>">Open</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?= pagination_links($list['pagination'], admin_url('distributors?status=' . rawurlencode($filterStatus))) ?>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
