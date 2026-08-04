<?php
/** Orders: list, detail view, status updates, CSV export. */
declare(strict_types=1);

$statuses = ['new', 'confirmed', 'out_for_delivery', 'delivered', 'cancelled'];
$viewId   = (int) get('view', 0);

/* ---- Actions ------------------------------------------------------------- */
if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'status':
            if ($id > 0 && in_array(post('status'), $statuses, true)) {
                db_update('orders', [
                    'status'     => post('status'),
                    'admin_note' => post('admin_note'),
                    'is_read'    => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                ], $id);
                admin_log('Updated order status to ' . post('status'), 'orders', $id);
                flash('success', 'Order updated.');
            }
            redirect('admin/orders' . ($id ? '?view=' . $id : ''));
            break;

        case 'delete':
            if ($id > 0) {
                q('DELETE FROM order_items WHERE order_id = ?', [$id]);
                db_delete('orders', $id);
                admin_log('Deleted order', 'orders', $id);
                flash('success', 'Order deleted.');
            }
            redirect('admin/orders');
            break;

        case 'bulk_status':
            $ids = array_map('intval', (array) ($_POST['ids'] ?? []));
            $st  = post('status');
            if ($ids && in_array($st, $statuses, true)) {
                $in = implode(',', array_fill(0, count($ids), '?'));
                q("UPDATE orders SET status = ?, is_read = 1, updated_at = NOW() WHERE id IN ($in)", array_merge([$st], $ids));
                admin_log('Bulk updated ' . count($ids) . ' orders to ' . $st, 'orders');
                flash('success', count($ids) . ' order(s) updated.');
            }
            redirect('admin/orders');
            break;
    }
}

if (get('export') === 'csv') {
    $rows = fetch_all('SELECT order_ref, customer_name, phone, whatsapp, email, address, area, city, customer_type,
                              order_type, payment_method, subtotal, deposit_total, total, status, delivery_note, message, created_at
                       FROM orders ORDER BY created_at DESC');
    admin_log('Exported orders CSV');
    admin_export_csv('pakeverests-orders', $rows);
}

/* ---- Detail view --------------------------------------------------------- */
if ($viewId > 0) {
    $order = fetch_one('SELECT * FROM orders WHERE id = ?', [$viewId]);
    if (!$order) {
        flash('error', 'Order not found.');
        redirect('admin/orders');
    }
    if ((int) $order['is_read'] === 0) {
        q('UPDATE orders SET is_read = 1 WHERE id = ?', [$viewId]);
    }
    $items = fetch_all('SELECT * FROM order_items WHERE order_id = ?', [$viewId]);

    $waLines = [];
    foreach ($items as $it) {
        $waLines[] = $it['product_name'] . ' x ' . (int) $it['quantity'] . ' = ' . money($it['line_total']);
    }
    $waText = "Hello " . $order['customer_name'] . ",\n\nYour Pak-Everests order " . $order['order_ref'] . " is confirmed.\n\n"
        . implode("\n", $waLines) . "\n\nTotal: " . money($order['total'])
        . "\nAddress: " . $order['address'] . "\n\nThank you for choosing Pak-Everests.";

    admin_header('Order ' . $order['order_ref'], 'Received ' . pretty_date($order['created_at'], 'd M Y, g:i A'), [
        ['label' => '← All orders', 'href' => admin_url('orders'), 'class' => 'btn-ghost'],
    ]);
    ?>
    <div class="a-grid" style="grid-template-columns:minmax(0,2fr) minmax(280px,1fr);gap:20px;align-items:start;">
      <div>
        <div class="a-card">
          <div class="a-card-head">
            <h2>Order items</h2>
            <?= status_pill($order['status']) ?>
          </div>
          <div class="a-table-wrap">
            <table class="a-table">
              <thead><tr><th>Product</th><th>Qty</th><th>Unit price</th><th>Deposit</th><th>Line total</th></tr></thead>
              <tbody>
                <?php foreach ($items as $it): ?>
                <tr>
                  <td><?= e($it['product_name']) ?></td>
                  <td><?= (int) $it['quantity'] ?></td>
                  <td><?= money($it['unit_price']) ?></td>
                  <td><?= (float) $it['deposit'] > 0 ? money($it['deposit']) : '—' ?></td>
                  <td><strong><?= money($it['line_total']) ?></strong></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr><th colspan="4" style="text-align:right;">Water total</th><td><?= money($order['subtotal']) ?></td></tr>
                <tr><th colspan="4" style="text-align:right;">Refundable deposit</th><td><?= money($order['deposit_total']) ?></td></tr>
                <tr><th colspan="4" style="text-align:right;">Grand total</th><td><strong><?= money($order['total']) ?></strong></td></tr>
              </tfoot>
            </table>
          </div>
        </div>

        <div class="a-card">
          <div class="a-card-head"><h2>Customer and delivery</h2></div>
          <dl class="detail-list">
            <div class="detail-row"><dt>Name</dt><dd><?= e($order['customer_name']) ?></dd></div>
            <div class="detail-row"><dt>Phone</dt><dd><a href="tel:<?= e($order['phone']) ?>"><?= e($order['phone']) ?></a></dd></div>
            <div class="detail-row"><dt>WhatsApp</dt><dd><a href="<?= e(wa_link((string) $order['whatsapp'])) ?>" target="_blank" rel="noopener"><?= e($order['whatsapp']) ?></a></dd></div>
            <div class="detail-row"><dt>Email</dt><dd><?= $order['email'] ? '<a href="mailto:' . e($order['email']) . '">' . e($order['email']) . '</a>' : '—' ?></dd></div>
            <div class="detail-row"><dt>Address</dt><dd><?= nl2br(e($order['address'])) ?></dd></div>
            <div class="detail-row"><dt>Area / City</dt><dd><?= e($order['area']) ?><?= $order['city'] ? ' — ' . e($order['city']) : '' ?></dd></div>
            <div class="detail-row"><dt>Location type</dt><dd><?= e($order['customer_type']) ?></dd></div>
            <div class="detail-row"><dt>Order frequency</dt><dd><?= e(ucwords(str_replace('_', ' ', (string) $order['order_type']))) ?></dd></div>
            <div class="detail-row"><dt>Preferred time</dt><dd><?= e($order['preferred_time']) ?></dd></div>
            <div class="detail-row"><dt>Payment method</dt><dd><?= e($order['payment_method']) ?></dd></div>
            <div class="detail-row"><dt>Delivery note</dt><dd><?= $order['delivery_note'] ? nl2br(e($order['delivery_note'])) : '—' ?></dd></div>
            <div class="detail-row"><dt>Customer message</dt><dd><?= $order['message'] ? nl2br(e($order['message'])) : '—' ?></dd></div>
            <div class="detail-row"><dt>Source / IP</dt><dd><?= e($order['source']) ?> — <?= e((string) $order['ip_address']) ?></dd></div>
          </dl>
        </div>
      </div>

      <aside>
        <div class="a-card">
          <div class="a-card-head"><h2>Update status</h2></div>
          <form method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="status">
            <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" name="status">
                <?php foreach ($statuses as $s): ?>
                <option value="<?= e($s) ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= e(ucwords(str_replace('_', ' ', $s))) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="admin_note">Internal note</label>
              <textarea id="admin_note" name="admin_note" placeholder="Rider assigned, payment received, follow up date…"><?= e((string) $order['admin_note']) ?></textarea>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Save</button>
          </form>
        </div>

        <div class="a-card">
          <div class="a-card-head"><h2>Contact the customer</h2></div>
          <a class="btn btn-primary btn-block" href="<?= e(wa_link((string) $order['whatsapp'], $waText)) ?>" target="_blank" rel="noopener">
            Send confirmation on WhatsApp
          </a>
          <a class="btn btn-ghost btn-block" style="margin-top:9px;" href="tel:<?= e($order['phone']) ?>">Call <?= e($order['phone']) ?></a>
          <?php if ($order['email']): ?>
          <a class="btn btn-ghost btn-block" style="margin-top:9px;" href="mailto:<?= e($order['email']) ?>?subject=<?= rawurlencode('Your Pak-Everests order ' . $order['order_ref']) ?>">Send an email</a>
          <?php endif; ?>
          <a class="btn btn-ghost btn-block" style="margin-top:9px;" href="<?= e(admin_url('quotations?edit=new&from_order=' . (int) $order['id'])) ?>">Create a quotation from this order</a>
        </div>

        <div class="a-card">
          <div class="a-card-head"><h2>Danger zone</h2></div>
          <form method="post" data-confirm="Delete this order permanently? This cannot be undone.">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
            <button class="btn btn-danger btn-block" type="submit">Delete order</button>
          </form>
        </div>
      </aside>
    </div>
    <?php
    admin_footer();
    return;
}

/* ---- List ---------------------------------------------------------------- */
$filterStatus = get('status');
$search       = get('q');
$where  = ['1=1'];
$params = [];
if ($filterStatus !== '' && in_array($filterStatus, $statuses, true)) {
    $where[] = 'status = ?'; $params[] = $filterStatus;
}
if ($search !== '') {
    $where[] = '(order_ref LIKE ? OR customer_name LIKE ? OR phone LIKE ? OR address LIKE ? OR area LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like, $like, $like, $like);
}

$list = admin_list('orders', [
    'where'  => implode(' AND ', $where),
    'params' => $params,
    'order'  => 'created_at DESC',
    'perPage'=> 30,
]);

admin_header('Orders', 'Every order placed through the website order form', [
    ['label' => 'Export CSV', 'href' => admin_url('orders?export=csv'), 'class' => 'btn-ghost'],
]);
?>

<div class="a-card">
  <form class="filter-form" method="get" action="<?= e(admin_url('orders')) ?>">
    <div class="form-group">
      <label for="q">Search</label>
      <input type="search" id="q" name="q" value="<?= e($search) ?>" placeholder="Reference, name, phone, address">
    </div>
    <div class="form-group">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="">All statuses</option>
        <?php foreach ($statuses as $s): ?>
        <option value="<?= e($s) ?>" <?= $filterStatus === $s ? 'selected' : '' ?>><?= e(ucwords(str_replace('_', ' ', $s))) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button class="btn btn-primary" type="submit">Filter</button>
    <?php if ($search !== '' || $filterStatus !== ''): ?>
      <a class="btn btn-ghost" href="<?= e(admin_url('orders')) ?>">Reset</a>
    <?php endif; ?>
  </form>

  <?php if (!$list['rows']): ?>
    <div class="empty">No orders found.</div>
  <?php else: ?>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="bulk_status">
    <div class="a-table-wrap">
      <table class="a-table">
        <thead>
          <tr>
            <th><input type="checkbox" data-check-all aria-label="Select all"></th>
            <th>Reference</th><th>Customer</th><th>Area</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th><th>Received</th><th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($list['rows'] as $o):
            $itemCount = (int) fetch_val('SELECT COALESCE(SUM(quantity),0) FROM order_items WHERE order_id = ?', [(int) $o['id']], 0); ?>
          <tr<?= (int) $o['is_read'] === 0 ? ' style="font-weight:600;"' : '' ?>>
            <td><input type="checkbox" name="ids[]" value="<?= (int) $o['id'] ?>" aria-label="Select order <?= e($o['order_ref']) ?>"></td>
            <td><a href="<?= e(admin_url('orders?view=' . (int) $o['id'])) ?>"><?= e($o['order_ref']) ?></a></td>
            <td><?= e($o['customer_name']) ?><br><small style="color:var(--a-muted);"><?= e($o['phone']) ?></small></td>
            <td><?= e($o['area']) ?></td>
            <td><?= $itemCount ?></td>
            <td><strong><?= money($o['total']) ?></strong></td>
            <td><small><?= e($o['payment_method']) ?></small></td>
            <td><?= status_pill($o['status']) ?></td>
            <td><small><?= time_ago($o['created_at']) ?></small></td>
            <td class="actions">
              <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('orders?view=' . (int) $o['id'])) ?>">Open</a>
              <a class="btn-icon" title="WhatsApp" href="<?= e(wa_link((string) $o['whatsapp'], 'Hello ' . $o['customer_name'] . ', regarding your Pak-Everests order ' . $o['order_ref'])) ?>" target="_blank" rel="noopener">
                <svg viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2z"/></svg>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="filter-form" style="margin-top:16px;">
      <div class="form-group">
        <label for="bulkStatus">With selected, set status to</label>
        <select id="bulkStatus" name="status">
          <?php foreach ($statuses as $s): ?>
          <option value="<?= e($s) ?>"><?= e(ucwords(str_replace('_', ' ', $s))) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button class="btn btn-primary" type="submit">Apply</button>
    </div>
  </form>

  <?= pagination_links($list['pagination'], admin_url('orders?status=' . rawurlencode($filterStatus) . '&q=' . rawurlencode($search))) ?>
  <?php endif; ?>
</div>

<?php admin_footer(); ?>
