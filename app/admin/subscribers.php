<?php
/** Newsletter subscribers. */
declare(strict_types=1);

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'delete':
            db_delete('subscribers', $id);
            flash('success', 'Subscriber removed.');
            redirect('admin/subscribers');
            break;
        case 'add':
            $email = post('email');
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                q('INSERT INTO subscribers (email, name, source, status, created_at) VALUES (?, ?, "admin", "active", NOW())
                   ON DUPLICATE KEY UPDATE status = "active"', [$email, post('name')]);
                flash('success', 'Subscriber added.');
            } else {
                flash('error', 'Please enter a valid email address.');
            }
            redirect('admin/subscribers');
            break;
    }
}

if (get('export') === 'csv') {
    admin_export_csv('pakeverests-subscribers', fetch_all('SELECT email, name, source, status, created_at FROM subscribers ORDER BY created_at DESC'));
}

$list = admin_list('subscribers', ['order' => 'created_at DESC', 'perPage' => 50]);
$active = (int) fetch_val('SELECT COUNT(*) FROM subscribers WHERE status = "active"', [], 0);
$emails = implode(', ', array_column(fetch_all('SELECT email FROM subscribers WHERE status = "active"'), 'email'));

admin_header('Subscribers', 'Email addresses collected through the footer subscription form', [
    ['label' => 'Export CSV', 'href' => admin_url('subscribers?export=csv'), 'class' => 'btn-ghost'],
]);
?>
<div class="a-grid a-grid-3" style="margin-bottom:20px;">
  <div class="stat"><span class="stat-icon ok"><svg viewBox="0 0 24 24"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5z"/></svg></span>
    <div><div class="stat-value"><?= number_format($active) ?></div><div class="stat-label">Active subscribers</div></div></div>
  <div class="stat"><span class="stat-icon"><svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/></svg></span>
    <div><div class="stat-value"><?= (int) fetch_val('SELECT COUNT(*) FROM subscribers WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)', [], 0) ?></div>
    <div class="stat-label">New in last 30 days</div></div></div>
  <div class="stat"><span class="stat-icon warn"><svg viewBox="0 0 24 24"><path d="M19 13H5v-2h14v2z"/></svg></span>
    <div><div class="stat-value"><?= (int) fetch_val('SELECT COUNT(*) FROM subscribers WHERE status = "unsubscribed"', [], 0) ?></div>
    <div class="stat-label">Unsubscribed</div></div></div>
</div>

<div class="a-card">
  <div class="a-card-head"><h2>Send a campaign</h2></div>
  <p class="form-hint">Copy the address list below into the BCC field of your email client or newsletter tool. Always use BCC so subscriber addresses stay private.</p>
  <textarea readonly style="min-height:90px;" onclick="this.select()"><?= e($emails) ?></textarea>
  <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap;">
    <button class="btn btn-ghost btn-sm" type="button" data-copy="<?= e($emails) ?>">Copy all addresses</button>
    <a class="btn btn-ghost btn-sm" href="mailto:?bcc=<?= rawurlencode($emails) ?>&subject=<?= rawurlencode('News from Pak-Everests') ?>">Open in email client</a>
  </div>
</div>

<div class="a-grid" style="grid-template-columns:minmax(0,1fr) minmax(260px,320px);gap:20px;align-items:start;">
  <div class="a-card">
    <div class="a-card-head"><h2>All subscribers</h2></div>
    <?php if (!$list['rows']): ?>
      <div class="empty">No subscribers yet.</div>
    <?php else: ?>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead><tr><th>Email</th><th>Name</th><th>Source</th><th>Status</th><th>Subscribed</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($list['rows'] as $s): ?>
          <tr>
            <td><a href="mailto:<?= e($s['email']) ?>"><?= e($s['email']) ?></a></td>
            <td><?= e((string) $s['name']) ?: '—' ?></td>
            <td><small><?= e($s['source']) ?></small></td>
            <td><?= status_pill($s['status']) ?></td>
            <td><small><?= pretty_date($s['created_at']) ?></small></td>
            <td class="actions"><?= delete_button((int) $s['id'], 'Remove this subscriber?') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?= pagination_links($list['pagination'], admin_url('subscribers')) ?>
    <?php endif; ?>
  </div>

  <aside class="a-card">
    <div class="a-card-head"><h2>Add manually</h2></div>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="add">
      <div class="form-group">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="name">Name (optional)</label>
        <input type="text" id="name" name="name">
      </div>
      <button class="btn btn-primary btn-block" type="submit">Add subscriber</button>
    </form>
  </aside>
</div>
<?php admin_footer(); ?>
