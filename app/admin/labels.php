<?php
/** Custom label requests. */
declare(strict_types=1);

$statuses = ['new', 'quoted', 'in_design', 'approved', 'printing', 'delivered', 'cancelled'];
$viewId   = (int) get('view', 0);

if (is_post()) {
    $id = (int) post('id');
    if (post('action') === 'status' && $id > 0 && in_array(post('status'), $statuses, true)) {
        db_update('label_requests', [
            'status'        => post('status'),
            'quoted_amount' => post('quoted_amount') !== '' ? (float) post('quoted_amount') : null,
            'admin_note'    => post('admin_note'),
        ], $id);
        admin_log('Updated label request', 'label_requests', $id);
        flash('success', 'Request updated.');
        redirect('admin/labels?view=' . $id);
    }
    if (post('action') === 'delete') {
        db_delete('label_requests', $id);
        admin_log('Deleted label request', 'label_requests', $id);
        flash('success', 'Request deleted.');
        redirect('admin/labels');
    }
}

if (get('export') === 'csv') {
    admin_export_csv('pakeverests-label-requests', fetch_all('SELECT * FROM label_requests ORDER BY created_at DESC'));
}

if ($viewId > 0) {
    $r = fetch_one('SELECT * FROM label_requests WHERE id = ?', [$viewId]);
    if (!$r) { flash('error', 'Request not found.'); redirect('admin/labels'); }

    admin_header('Label request ' . $r['ref'], $r['contact_name'] . ($r['company_name'] ? ' — ' . $r['company_name'] : ''), [
        ['label' => '← All requests', 'href' => admin_url('labels'), 'class' => 'btn-ghost'],
    ]);
    ?>
    <div class="a-grid" style="grid-template-columns:minmax(0,2fr) minmax(280px,1fr);gap:20px;align-items:start;">
      <div class="a-card">
        <div class="a-card-head"><h2>Brief</h2><?= status_pill($r['status']) ?></div>
        <dl class="detail-list">
          <div class="detail-row"><dt>Contact</dt><dd><?= e($r['contact_name']) ?> <?= $r['designation'] ? '(' . e($r['designation']) . ')' : '' ?></dd></div>
          <div class="detail-row"><dt>Company</dt><dd><?= e((string) $r['company_name']) ?: '—' ?></dd></div>
          <div class="detail-row"><dt>Phone</dt><dd><a href="tel:<?= e($r['phone']) ?>"><?= e($r['phone']) ?></a></dd></div>
          <div class="detail-row"><dt>WhatsApp</dt><dd><a href="<?= e(wa_link((string) $r['whatsapp'])) ?>" target="_blank" rel="noopener"><?= e((string) $r['whatsapp']) ?></a></dd></div>
          <div class="detail-row"><dt>Email</dt><dd><?= $r['email'] ? '<a href="mailto:' . e($r['email']) . '">' . e($r['email']) . '</a>' : '—' ?></dd></div>
          <div class="detail-row"><dt>City</dt><dd><?= e((string) $r['city']) ?: '—' ?></dd></div>
          <div class="detail-row"><dt>Address</dt><dd><?= nl2br(e((string) $r['address'])) ?: '—' ?></dd></div>
          <div class="detail-row"><dt>Bottle size</dt><dd><strong><?= e((string) $r['bottle_size']) ?></strong></dd></div>
          <div class="detail-row"><dt>Quantity</dt><dd><strong><?= e((string) $r['quantity']) ?></strong></dd></div>
          <div class="detail-row"><dt>Occasion</dt><dd><?= e((string) $r['occasion']) ?></dd></div>
          <div class="detail-row"><dt>Required by</dt><dd><?= $r['required_date'] ? pretty_date($r['required_date']) : '—' ?></dd></div>
          <div class="detail-row"><dt>Has artwork</dt><dd><?= e((string) $r['has_artwork']) ?></dd></div>
          <div class="detail-row"><dt>Brand colours</dt><dd><?= e((string) $r['brand_colors']) ?: '—' ?></dd></div>
          <div class="detail-row"><dt>Design help wanted</dt><dd><?= e((string) $r['design_help']) ?></dd></div>
          <div class="detail-row"><dt>Label text</dt><dd><?= nl2br(e((string) $r['label_text'])) ?: '—' ?></dd></div>
          <div class="detail-row"><dt>Message</dt><dd><?= nl2br(e((string) $r['message'])) ?: '—' ?></dd></div>
          <div class="detail-row"><dt>Submitted</dt><dd><?= pretty_date($r['created_at'], 'd M Y, g:i A') ?></dd></div>
        </dl>

        <?php if ($r['artwork_path'] || $r['logo_path']): ?>
        <h3 style="margin-top:20px;">Uploaded files</h3>
        <div class="media-grid">
          <?php foreach (array_filter([$r['artwork_path'], $r['logo_path']]) as $file): ?>
          <div class="media-tile">
            <?php if (in_array(strtolower(pathinfo((string) $file, PATHINFO_EXTENSION)), PE_IMAGE_TYPES, true)): ?>
              <img src="/<?= e(ltrim((string) $file, '/')) ?>" alt="Uploaded artwork">
            <?php endif; ?>
            <div class="media-tile-body">
              <a href="/<?= e(ltrim((string) $file, '/')) ?>" target="_blank" rel="noopener">Open file</a>
              <span class="media-path"><?= e((string) $file) ?></span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <aside>
        <div class="a-card">
          <div class="a-card-head"><h2>Progress</h2></div>
          <form method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="status">
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" name="status">
                <?php foreach ($statuses as $s): ?>
                <option value="<?= e($s) ?>" <?= $r['status'] === $s ? 'selected' : '' ?>><?= e(ucwords(str_replace('_', ' ', $s))) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="quoted_amount">Quoted amount (PKR)</label>
              <input type="number" step="0.01" id="quoted_amount" name="quoted_amount" value="<?= e((string) $r['quoted_amount']) ?>">
            </div>
            <div class="form-group">
              <label for="admin_note">Internal note</label>
              <textarea id="admin_note" name="admin_note"><?= e((string) $r['admin_note']) ?></textarea>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Save</button>
          </form>
        </div>

        <div class="a-card">
          <div class="a-card-head"><h2>Actions</h2></div>
          <a class="btn btn-primary btn-block" target="_blank" rel="noopener"
             href="<?= e(wa_link((string) ($r['whatsapp'] ?: $r['phone']), 'Hello ' . $r['contact_name'] . ', regarding your Pak-Everests custom label request ' . $r['ref'] . '.')) ?>">Message on WhatsApp</a>
          <a class="btn btn-ghost btn-block" style="margin-top:9px;"
             href="<?= e(admin_url('quotations?edit=new&name=' . rawurlencode($r['contact_name']) . '&company=' . rawurlencode((string) $r['company_name']) . '&phone=' . rawurlencode($r['phone']) . '&email=' . rawurlencode((string) $r['email']))) ?>">Create a quotation</a>
        </div>

        <div class="a-card">
          <form method="post" data-confirm="Delete this label request permanently?">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <button class="btn btn-danger btn-block" type="submit">Delete request</button>
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
$list = admin_list('label_requests', ['where' => implode(' AND ', $where), 'params' => $params, 'order' => 'created_at DESC', 'perPage' => 30]);

admin_header('Custom Label Requests', 'Private label briefs submitted through the customisation form', [
    ['label' => 'Export CSV', 'href' => admin_url('labels?export=csv'), 'class' => 'btn-ghost'],
]);
?>
<div class="a-card">
  <div class="tabs">
    <a class="tab<?= $filterStatus === '' ? ' is-active' : '' ?>" href="<?= e(admin_url('labels')) ?>">All</a>
    <?php foreach ($statuses as $s): ?>
    <a class="tab<?= $filterStatus === $s ? ' is-active' : '' ?>" href="<?= e(admin_url('labels?status=' . $s)) ?>"><?= e(ucwords(str_replace('_', ' ', $s))) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (!$list['rows']): ?>
    <div class="empty">No label requests found.</div>
  <?php else: ?>
  <div class="a-table-wrap">
    <table class="a-table">
      <thead><tr><th>Reference</th><th>Contact</th><th>Company</th><th>Bottle</th><th>Quantity</th><th>Needed by</th><th>Status</th><th>Received</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($list['rows'] as $r): ?>
        <tr<?= $r['status'] === 'new' ? ' style="font-weight:600;"' : '' ?>>
          <td><a href="<?= e(admin_url('labels?view=' . (int) $r['id'])) ?>"><?= e($r['ref']) ?></a></td>
          <td><?= e($r['contact_name']) ?><br><small style="color:var(--a-muted);"><?= e($r['phone']) ?></small></td>
          <td><?= e((string) $r['company_name']) ?></td>
          <td><small><?= e((string) $r['bottle_size']) ?></small></td>
          <td><small><?= e((string) $r['quantity']) ?></small></td>
          <td><small><?= $r['required_date'] ? pretty_date($r['required_date']) : '—' ?></small></td>
          <td><?= status_pill($r['status']) ?></td>
          <td><small><?= time_ago($r['created_at']) ?></small></td>
          <td class="actions"><a class="btn btn-ghost btn-sm" href="<?= e(admin_url('labels?view=' . (int) $r['id'])) ?>">Open</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?= pagination_links($list['pagination'], admin_url('labels?status=' . rawurlencode($filterStatus))) ?>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
