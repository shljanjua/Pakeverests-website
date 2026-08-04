<?php
/** WhatsApp numbers used across the website and for order routing. */
declare(strict_types=1);

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'save':
            $data = [
                'label'           => post('label'),
                'number'          => post('number'),
                'department'      => post('department'),
                'receives_orders' => post('receives_orders') ? 1 : 0,
                'is_primary'      => post('is_primary') ? 1 : 0,
                'is_active'       => post('is_active') ? 1 : 0,
                'sort_order'      => (int) post('sort_order', 0),
            ];
            if ($data['is_primary'] === 1) {
                q('UPDATE whatsapp_numbers SET is_primary = 0');
            }
            if ($id > 0) { db_update('whatsapp_numbers', $data, $id); flash('success', 'Number updated.'); }
            else { $data['created_at'] = date('Y-m-d H:i:s'); $id = db_insert('whatsapp_numbers', $data); flash('success', 'Number added.'); }
            admin_log('Saved WhatsApp number', 'whatsapp_numbers', $id);
            redirect('admin/whatsapp');
            break;
        case 'toggle':
            $col = post('column');
            if (in_array($col, ['is_active', 'receives_orders'], true)) {
                q("UPDATE whatsapp_numbers SET `$col` = IF(`$col` = 1, 0, 1) WHERE id = ?", [$id]);
            }
            redirect('admin/whatsapp');
            break;
        case 'make_primary':
            q('UPDATE whatsapp_numbers SET is_primary = 0');
            q('UPDATE whatsapp_numbers SET is_primary = 1 WHERE id = ?', [$id]);
            flash('success', 'Primary number updated.');
            redirect('admin/whatsapp');
            break;
        case 'delete':
            db_delete('whatsapp_numbers', $id);
            flash('success', 'Number removed.');
            redirect('admin/whatsapp');
            break;
        case 'save_settings':
            settings_save([
                'whatsapp_float_enabled' => post('whatsapp_float_enabled') ? '1' : '0',
                'phone_display'          => post('phone_display'),
                'order_notify_email'     => post('order_notify_email'),
            ]);
            flash('success', 'Settings saved.');
            redirect('admin/whatsapp');
            break;
    }
}

$editId = (int) get('edit', 0);
$num = $editId > 0 ? fetch_one('SELECT * FROM whatsapp_numbers WHERE id = ?', [$editId]) : [
    'id' => 0, 'label' => '', 'number' => '', 'department' => '', 'receives_orders' => 1,
    'is_primary' => 0, 'is_active' => 1, 'sort_order' => 0,
];
$numbers = fetch_all('SELECT * FROM whatsapp_numbers ORDER BY is_primary DESC, sort_order ASC, id ASC');

admin_header('WhatsApp Numbers', 'Numbers shown on the website and used to route orders');
?>
<div class="a-grid" style="grid-template-columns:minmax(0,1fr) minmax(300px,380px);gap:20px;align-items:start;">
  <div>
    <div class="a-card">
      <div class="a-card-head"><h2>Numbers (<?= count($numbers) ?>)</h2></div>
      <p class="form-hint" style="margin-bottom:14px;">
        The <strong>primary</strong> number appears in the floating WhatsApp button, the hero buttons and every
        "Send on WhatsApp" link. Numbers marked <strong>receives orders</strong> are used for order routing.
      </p>
      <?php if (!$numbers): ?>
        <div class="empty">No numbers configured. The website falls back to 0333 5592206 until you add one.</div>
      <?php else: ?>
      <div class="a-table-wrap">
        <table class="a-table">
          <thead><tr><th>Label</th><th>Number</th><th>Department</th><th>Orders</th><th>Active</th><th>Primary</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($numbers as $n): ?>
            <tr>
              <td><strong><?= e($n['label']) ?></strong></td>
              <td>
                <?= e($n['number']) ?><br>
                <small class="media-path">wa.me/<?= e(wa_number($n['number'])) ?></small>
              </td>
              <td><small><?= e((string) $n['department']) ?></small></td>
              <td><?= toggle_button((int) $n['id'], 'receives_orders', (int) $n['receives_orders'] === 1, 'Receives orders') ?></td>
              <td><?= toggle_button((int) $n['id'], 'is_active', (int) $n['is_active'] === 1, 'Active') ?></td>
              <td>
                <?php if ((int) $n['is_primary'] === 1): ?>
                  <span class="pill pill-ok">Primary</span>
                <?php else: ?>
                <form method="post" class="inline-form">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="make_primary">
                  <input type="hidden" name="id" value="<?= (int) $n['id'] ?>">
                  <button class="btn btn-ghost btn-sm" type="submit">Make primary</button>
                </form>
                <?php endif; ?>
              </td>
              <td class="actions">
                <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('whatsapp?edit=' . (int) $n['id'])) ?>">Edit</a>
                <a class="btn-icon" title="Test" href="<?= e(wa_link($n['number'], 'Test message from the Pak-Everests admin panel')) ?>" target="_blank" rel="noopener">
                  <svg viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
                </a>
                <?= delete_button((int) $n['id'], 'Remove this number?') ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

    <div class="a-card">
      <div class="a-card-head"><h2>Contact display and order routing</h2></div>
      <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_settings">
        <label class="check"><input type="checkbox" name="whatsapp_float_enabled" value="1" <?= setting_bool('whatsapp_float_enabled', true) ? 'checked' : '' ?>> Show the floating WhatsApp button on every page</label>
        <div class="form-row">
          <div class="form-group">
            <label for="phone_display">Phone number shown in text</label>
            <input type="text" id="phone_display" name="phone_display" value="<?= e((string) setting('phone_display')) ?>">
          </div>
          <div class="form-group">
            <label for="order_notify_email">Email address that receives order notifications</label>
            <input type="email" id="order_notify_email" name="order_notify_email" value="<?= e((string) setting('order_notify_email')) ?>">
            <span class="form-hint">Separate multiple addresses with commas.</span>
          </div>
        </div>
        <button class="btn btn-primary" type="submit">Save</button>
      </form>
    </div>
  </div>

  <aside class="a-card">
    <div class="a-card-head"><h2><?= $editId ? 'Edit number' : 'Add a number' ?></h2></div>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int) $num['id'] ?>">
      <div class="form-group">
        <label for="label">Label <span class="req">*</span></label>
        <input type="text" id="label" name="label" required value="<?= e((string) $num['label']) ?>" placeholder="Orders and Delivery">
      </div>
      <div class="form-group">
        <label for="number">WhatsApp number <span class="req">*</span></label>
        <input type="text" id="number" name="number" required value="<?= e((string) $num['number']) ?>" placeholder="0333 5592206">
        <span class="form-hint">Local format is fine. It is converted to the international wa.me format automatically.</span>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="department">Department</label>
          <input type="text" id="department" name="department" value="<?= e((string) $num['department']) ?>" placeholder="Sales, Support, Distribution">
        </div>
        <div class="form-group">
          <label for="sort_order">Order</label>
          <input type="number" id="sort_order" name="sort_order" value="<?= (int) $num['sort_order'] ?>">
        </div>
      </div>
      <label class="check"><input type="checkbox" name="receives_orders" value="1" <?= (int) $num['receives_orders'] === 1 ? 'checked' : '' ?>> Receives website orders</label>
      <label class="check"><input type="checkbox" name="is_primary" value="1" <?= (int) $num['is_primary'] === 1 ? 'checked' : '' ?>> Make this the primary number</label>
      <label class="check"><input type="checkbox" name="is_active" value="1" <?= (int) $num['is_active'] === 1 ? 'checked' : '' ?>> Active</label>
      <button class="btn btn-primary btn-block" type="submit"><?= $editId ? 'Save changes' : 'Add number' ?></button>
      <?php if ($editId): ?><a class="btn btn-ghost btn-block" style="margin-top:8px;" href="<?= e(admin_url('whatsapp')) ?>">Cancel</a><?php endif; ?>
    </form>
  </aside>
</div>
<?php admin_footer(); ?>
