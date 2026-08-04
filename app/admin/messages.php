<?php
/** Contact messages inbox. */
declare(strict_types=1);

$statuses = ['new', 'read', 'replied', 'archived'];
$viewId   = (int) get('view', 0);

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'status':
            if ($id > 0 && in_array(post('status'), $statuses, true)) {
                db_update('contact_messages', ['status' => post('status'), 'admin_note' => post('admin_note')], $id);
                admin_log('Updated message status', 'contact_messages', $id);
                flash('success', 'Message updated.');
            }
            redirect('admin/messages' . ($id ? '?view=' . $id : ''));
            break;
        case 'delete':
            db_delete('contact_messages', $id);
            admin_log('Deleted message', 'contact_messages', $id);
            flash('success', 'Message deleted.');
            redirect('admin/messages');
            break;
    }
}

if (get('export') === 'csv') {
    admin_export_csv('pakeverests-messages', fetch_all('SELECT ref, name, email, phone, area, subject, message, form_type, status, created_at FROM contact_messages ORDER BY created_at DESC'));
}

if ($viewId > 0) {
    $m = fetch_one('SELECT * FROM contact_messages WHERE id = ?', [$viewId]);
    if (!$m) { flash('error', 'Message not found.'); redirect('admin/messages'); }
    if ($m['status'] === 'new') {
        q('UPDATE contact_messages SET status = "read" WHERE id = ?', [$viewId]);
        $m['status'] = 'read';
    }

    admin_header('Message ' . $m['ref'], 'Received ' . pretty_date($m['created_at'], 'd M Y, g:i A'), [
        ['label' => '← Inbox', 'href' => admin_url('messages'), 'class' => 'btn-ghost'],
    ]);
    ?>
    <div class="a-grid" style="grid-template-columns:minmax(0,2fr) minmax(280px,1fr);gap:20px;align-items:start;">
      <div class="a-card">
        <div class="a-card-head"><h2><?= e((string) $m['subject']) ?></h2><?= status_pill($m['status']) ?></div>
        <dl class="detail-list">
          <div class="detail-row"><dt>From</dt><dd><?= e($m['name']) ?></dd></div>
          <div class="detail-row"><dt>Email</dt><dd><?= $m['email'] ? '<a href="mailto:' . e($m['email']) . '">' . e($m['email']) . '</a>' : '—' ?></dd></div>
          <div class="detail-row"><dt>Phone</dt><dd><?= $m['phone'] ? '<a href="tel:' . e($m['phone']) . '">' . e($m['phone']) . '</a>' : '—' ?></dd></div>
          <div class="detail-row"><dt>Area</dt><dd><?= e((string) $m['area']) ?: '—' ?></dd></div>
          <div class="detail-row"><dt>Form</dt><dd><?= e($m['form_type']) ?></dd></div>
          <div class="detail-row"><dt>IP address</dt><dd><?= e((string) $m['ip_address']) ?></dd></div>
        </dl>
        <hr style="border:0;border-top:1px solid var(--a-border);margin:18px 0;">
        <p style="white-space:pre-wrap;"><?= e($m['message']) ?></p>
      </div>

      <aside>
        <div class="a-card">
          <div class="a-card-head"><h2>Reply</h2></div>
          <?php if ($m['phone']): ?>
          <a class="btn btn-primary btn-block" target="_blank" rel="noopener"
             href="<?= e(wa_link((string) $m['phone'], 'Hello ' . $m['name'] . ', thank you for contacting Pak-Everests regarding: ' . $m['subject'])) ?>">Reply on WhatsApp</a>
          <?php endif; ?>
          <?php if ($m['email']): ?>
          <a class="btn btn-ghost btn-block" style="margin-top:9px;"
             href="mailto:<?= e($m['email']) ?>?subject=<?= rawurlencode('Re: ' . $m['subject'] . ' (' . $m['ref'] . ')') ?>">Reply by email</a>
          <?php endif; ?>
        </div>

        <div class="a-card">
          <div class="a-card-head"><h2>Status</h2></div>
          <form method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="status">
            <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" name="status">
                <?php foreach ($statuses as $s): ?>
                <option value="<?= e($s) ?>" <?= $m['status'] === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="admin_note">Internal note</label>
              <textarea id="admin_note" name="admin_note"><?= e((string) $m['admin_note']) ?></textarea>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Save</button>
          </form>
        </div>

        <div class="a-card">
          <form method="post" data-confirm="Delete this message permanently?">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
            <button class="btn btn-danger btn-block" type="submit">Delete message</button>
          </form>
        </div>
      </aside>
    </div>
    <?php
    admin_footer();
    return;
}

$filterStatus = get('status');
$search = get('q');
$where = ['1=1']; $params = [];
if ($filterStatus !== '' && in_array($filterStatus, $statuses, true)) { $where[] = 'status = ?'; $params[] = $filterStatus; }
if ($search !== '') {
    $where[] = '(ref LIKE ? OR name LIKE ? OR email LIKE ? OR phone LIKE ? OR message LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like, $like, $like, $like);
}

$list = admin_list('contact_messages', ['where' => implode(' AND ', $where), 'params' => $params, 'order' => 'created_at DESC', 'perPage' => 30]);

admin_header('Messages', 'Enquiries submitted through the contact form', [
    ['label' => 'Export CSV', 'href' => admin_url('messages?export=csv'), 'class' => 'btn-ghost'],
]);
?>
<div class="a-card">
  <form class="filter-form" method="get" action="<?= e(admin_url('messages')) ?>">
    <div class="form-group">
      <label for="q">Search</label>
      <input type="search" id="q" name="q" value="<?= e($search) ?>" placeholder="Name, email, phone, text">
    </div>
    <div class="form-group">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="">All</option>
        <?php foreach ($statuses as $s): ?>
        <option value="<?= e($s) ?>" <?= $filterStatus === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button class="btn btn-primary" type="submit">Filter</button>
  </form>

  <?php if (!$list['rows']): ?>
    <div class="empty">No messages found.</div>
  <?php else: ?>
  <div class="a-table-wrap">
    <table class="a-table">
      <thead><tr><th>Reference</th><th>From</th><th>Subject</th><th>Message</th><th>Status</th><th>Received</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($list['rows'] as $m): ?>
        <tr<?= $m['status'] === 'new' ? ' style="font-weight:600;"' : '' ?>>
          <td><a href="<?= e(admin_url('messages?view=' . (int) $m['id'])) ?>"><?= e($m['ref']) ?></a></td>
          <td><?= e($m['name']) ?><br><small style="color:var(--a-muted);"><?= e((string) ($m['phone'] ?: $m['email'])) ?></small></td>
          <td><?= e((string) $m['subject']) ?></td>
          <td><small><?= e(excerpt($m['message'], 70)) ?></small></td>
          <td><?= status_pill($m['status']) ?></td>
          <td><small><?= time_ago($m['created_at']) ?></small></td>
          <td class="actions">
            <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('messages?view=' . (int) $m['id'])) ?>">Open</a>
            <?= delete_button((int) $m['id'], 'Delete this message?') ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?= pagination_links($list['pagination'], admin_url('messages?status=' . rawurlencode($filterStatus) . '&q=' . rawurlencode($search))) ?>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
