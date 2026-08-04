<?php
/** Quotation generator with line items, totals, print view and email sending. */
declare(strict_types=1);

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'save':
            $items = [];
            $descs = (array) ($_POST['item_desc'] ?? []);
            foreach ($descs as $i => $desc) {
                $desc = trim((string) $desc);
                if ($desc === '') { continue; }
                $qty  = (float) ($_POST['item_qty'][$i] ?? 0);
                $rate = (float) ($_POST['item_rate'][$i] ?? 0);
                $items[] = [
                    'description' => $desc,
                    'unit'        => trim((string) ($_POST['item_unit'][$i] ?? '')),
                    'qty'         => $qty,
                    'rate'        => $rate,
                    'total'       => round($qty * $rate, 2),
                ];
            }
            $subtotal = array_sum(array_column($items, 'total'));
            $discount = (float) post('discount', 0);
            $delivery = (float) post('delivery_charges', 0);

            $data = [
                'client_name'      => post('client_name'),
                'client_company'   => post('client_company'),
                'client_phone'     => post('client_phone'),
                'client_email'     => post('client_email'),
                'client_address'   => post('client_address'),
                'items_json'       => json_encode($items, JSON_UNESCAPED_UNICODE),
                'subtotal'         => $subtotal,
                'discount'         => $discount,
                'delivery_charges' => $delivery,
                'total'            => $subtotal - $discount + $delivery,
                'valid_until'      => post('valid_until') ?: null,
                'notes'            => post('notes'),
                'terms'            => post('terms'),
                'status'           => in_array(post('status'), ['draft', 'sent', 'accepted', 'declined', 'expired'], true) ? post('status') : 'draft',
                'updated_at'       => date('Y-m-d H:i:s'),
            ];

            if ($id > 0) {
                db_update('quotations', $data, $id);
                flash('success', 'Quotation saved.');
            } else {
                $data['quote_ref']  = generate_ref('QTN');
                $data['created_by'] = (int) (admin_user()['id'] ?? 0);
                $data['created_at'] = date('Y-m-d H:i:s');
                $id = db_insert('quotations', $data);
                flash('success', 'Quotation created.');
            }
            admin_log('Saved quotation', 'quotations', $id);
            redirect('admin/quotations?edit=' . $id);
            break;

        case 'send':
            $qt = fetch_one('SELECT * FROM quotations WHERE id = ?', [$id]);
            if ($qt && filter_var($qt['client_email'], FILTER_VALIDATE_EMAIL)) {
                $items = json_decode((string) $qt['items_json'], true) ?: [];
                $rows  = '<table role="presentation" cellpadding="8" cellspacing="0" width="100%" style="border-collapse:collapse;font-size:14px;">'
                    . '<tr style="background:#eaf5fb;"><th align="left">Item</th><th align="right">Qty</th><th align="right">Rate</th><th align="right">Total</th></tr>';
                foreach ($items as $it) {
                    $rows .= '<tr><td style="border-bottom:1px solid #e4eef4;">' . e($it['description']) . '</td>'
                        . '<td align="right" style="border-bottom:1px solid #e4eef4;">' . e((string) $it['qty']) . ' ' . e((string) $it['unit']) . '</td>'
                        . '<td align="right" style="border-bottom:1px solid #e4eef4;">' . money($it['rate']) . '</td>'
                        . '<td align="right" style="border-bottom:1px solid #e4eef4;">' . money($it['total']) . '</td></tr>';
                }
                $rows .= '<tr><td colspan="3" align="right"><strong>Subtotal</strong></td><td align="right">' . money($qt['subtotal']) . '</td></tr>';
                if ((float) $qt['discount'] > 0) {
                    $rows .= '<tr><td colspan="3" align="right">Discount</td><td align="right">- ' . money($qt['discount']) . '</td></tr>';
                }
                if ((float) $qt['delivery_charges'] > 0) {
                    $rows .= '<tr><td colspan="3" align="right">Delivery</td><td align="right">' . money($qt['delivery_charges']) . '</td></tr>';
                }
                $rows .= '<tr style="background:#eaf5fb;"><td colspan="3" align="right"><strong>Total</strong></td><td align="right"><strong>'
                    . money($qt['total']) . '</strong></td></tr></table>';

                $html = '<p>Dear ' . e($qt['client_name']) . ',</p>'
                    . '<p>Thank you for your enquiry. Please find our quotation <strong>' . e($qt['quote_ref']) . '</strong> below.</p>'
                    . $rows
                    . ($qt['valid_until'] ? '<p><strong>Valid until:</strong> ' . pretty_date($qt['valid_until']) . '</p>' : '')
                    . ($qt['notes'] ? '<p>' . nl2br(e((string) $qt['notes'])) . '</p>' : '')
                    . ($qt['terms'] ? '<hr><p style="font-size:12px;color:#5c7a8a;">' . nl2br(e((string) $qt['terms'])) . '</p>' : '')
                    . '<p>To accept this quotation, reply to this email or message us on WhatsApp at ' . e(primary_whatsapp()) . '.</p>';

                $ok = send_mail($qt['client_email'], 'Quotation ' . $qt['quote_ref'] . ' from ' . site_name(), $html, contact_email(), 'quotation');
                if ($ok) {
                    db_update('quotations', ['status' => 'sent', 'sent_to' => $qt['client_email'], 'sent_at' => date('Y-m-d H:i:s')], $id);
                    admin_log('Sent quotation ' . $qt['quote_ref'], 'quotations', $id);
                    flash('success', 'Quotation emailed to ' . $qt['client_email'] . '.');
                } else {
                    flash('error', 'The email could not be sent. Check the SMTP settings, or use the print view.');
                }
            } else {
                flash('error', 'This quotation has no valid email address.');
            }
            redirect('admin/quotations?edit=' . $id);
            break;

        case 'delete':
            db_delete('quotations', $id);
            flash('success', 'Quotation deleted.');
            redirect('admin/quotations');
            break;
    }
}

/* ---- Print view ---------------------------------------------------------- */
$printId = (int) get('print', 0);
if ($printId > 0) {
    $qt = fetch_one('SELECT * FROM quotations WHERE id = ?', [$printId]);
    if (!$qt) { flash('error', 'Quotation not found.'); redirect('admin/quotations'); }
    $items = json_decode((string) $qt['items_json'], true) ?: [];

    admin_header('Quotation ' . $qt['quote_ref'], 'Print view', [
        ['label' => '← Back', 'href' => admin_url('quotations?edit=' . $printId), 'class' => 'btn-ghost'],
    ]);
    ?>
    <div class="no-print" style="margin-bottom:16px;">
      <button class="btn btn-primary" type="button" onclick="window.print()">Print or save as PDF</button>
    </div>
    <div class="doc-preview">
      <div style="display:flex;justify-content:space-between;gap:20px;border-bottom:2px solid #0b6fa4;padding-bottom:16px;margin-bottom:22px;">
        <div>
          <img src="<?= e(media_url((string) setting('logo_path'), 'logo')) ?>" alt="" style="height:52px;">
          <p style="margin:8px 0 0;font-size:.85rem;color:#55707f;">
            <?= e((string) setting('address_full')) ?><br>
            <?= e(contact_email()) ?> &middot; <?= e(primary_whatsapp()) ?><br>
            <?= e((string) setting('license_authority')) ?> Licence <?= e((string) setting('license_number')) ?>
          </p>
        </div>
        <div style="text-align:right;">
          <h1 style="margin:0;font-size:1.5rem;">QUOTATION</h1>
          <p style="margin:6px 0 0;font-size:.86rem;">
            Reference: <strong><?= e($qt['quote_ref']) ?></strong><br>
            Date: <?= pretty_date($qt['created_at']) ?><br>
            <?= $qt['valid_until'] ? 'Valid until: <strong>' . pretty_date($qt['valid_until']) . '</strong>' : '' ?>
          </p>
        </div>
      </div>

      <h2 style="font-size:1rem;">Quotation for</h2>
      <p>
        <strong><?= e($qt['client_name']) ?></strong><?= $qt['client_company'] ? '<br>' . e($qt['client_company']) : '' ?>
        <?= $qt['client_address'] ? '<br>' . nl2br(e($qt['client_address'])) : '' ?>
        <?= $qt['client_phone'] ? '<br>' . e($qt['client_phone']) : '' ?>
        <?= $qt['client_email'] ? '<br>' . e($qt['client_email']) : '' ?>
      </p>

      <table>
        <thead>
          <tr><th style="width:44px;">#</th><th>Description</th><th style="width:110px;">Quantity</th><th style="width:110px;">Rate</th><th style="width:120px;">Amount</th></tr>
        </thead>
        <tbody>
          <?php foreach ($items as $i => $it): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= e($it['description']) ?></td>
            <td><?= e((string) $it['qty']) ?> <?= e((string) $it['unit']) ?></td>
            <td><?= money($it['rate']) ?></td>
            <td><?= money($it['total']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr><td colspan="4" style="text-align:right;">Subtotal</td><td><?= money($qt['subtotal']) ?></td></tr>
          <?php if ((float) $qt['discount'] > 0): ?>
          <tr><td colspan="4" style="text-align:right;">Discount</td><td>- <?= money($qt['discount']) ?></td></tr>
          <?php endif; ?>
          <tr><td colspan="4" style="text-align:right;">Delivery</td><td><?= (float) $qt['delivery_charges'] > 0 ? money($qt['delivery_charges']) : 'Free' ?></td></tr>
          <tr><td colspan="4" style="text-align:right;"><strong>Total payable</strong></td><td><strong><?= money($qt['total']) ?></strong></td></tr>
        </tfoot>
      </table>

      <?php if ($qt['notes']): ?>
        <h2 style="font-size:1rem;">Notes</h2>
        <p><?= nl2br(e((string) $qt['notes'])) ?></p>
      <?php endif; ?>

      <?php if ($qt['terms']): ?>
        <h2 style="font-size:1rem;">Terms and conditions</h2>
        <p style="font-size:.85rem;color:#55707f;"><?= nl2br(e((string) $qt['terms'])) ?></p>
      <?php endif; ?>

      <h2 style="font-size:1rem;">Payment details</h2>
      <p style="font-size:.88rem;">
        <?php if (setting_bool('easypaisa_enabled', true)): ?>EasyPaisa: <?= e((string) setting('easypaisa_title')) ?> — <?= e((string) setting('easypaisa_number')) ?><br><?php endif; ?>
        <?php if (setting_bool('jazzcash_enabled', true)): ?>JazzCash: <?= e((string) setting('jazzcash_title')) ?> — <?= e((string) setting('jazzcash_number')) ?><br><?php endif; ?>
        <?php if (setting_bool('bank_enabled', true)): ?>
          <?= e((string) setting('bank_name')) ?>, <?= e((string) setting('bank_branch')) ?><br>
          Title: <?= e((string) setting('bank_account_title')) ?><br>
          Account: <?= e((string) setting('bank_account_number')) ?><br>
          IBAN: <?= e((string) setting('bank_iban')) ?>
        <?php endif; ?>
      </p>

      <div style="margin-top:48px;border-top:1px solid #12303f;padding-top:8px;width:260px;font-size:.86rem;">
        For <?= e(site_name()) ?><br>Authorised signature and stamp
      </div>
    </div>
    <?php
    admin_footer();
    return;
}

/* ---- Editor -------------------------------------------------------------- */
$editId = get('edit');
if ($editId !== '') {
    $isNew = $editId === 'new';

    $defaultTerms = "Prices are in Pakistan Rupees and inclusive of delivery within the published coverage area.\n"
        . "A refundable security deposit applies to returnable 19 litre bottles and is not part of the price of water.\n"
        . "This quotation is valid for the period stated above and is subject to stock availability.\n"
        . "Payment terms: cash on delivery unless a credit account has been agreed in writing.\n"
        . "All supply is governed by the published terms and conditions of Pak-Everests Bottled Drinking Water.";

    $qt = $isNew ? [
        'id' => 0, 'quote_ref' => 'Generated on save', 'client_name' => get('name'),
        'client_company' => get('company'), 'client_phone' => get('phone'), 'client_email' => get('email'),
        'client_address' => '', 'items_json' => '', 'subtotal' => 0, 'discount' => 0, 'delivery_charges' => 0,
        'total' => 0, 'valid_until' => date('Y-m-d', strtotime('+15 days')), 'notes' => '',
        'terms' => $defaultTerms, 'status' => 'draft', 'sent_to' => '', 'sent_at' => '',
    ] : fetch_one('SELECT * FROM quotations WHERE id = ?', [(int) $editId]);
    if (!$qt) { flash('error', 'Quotation not found.'); redirect('admin/quotations'); }

    $items = json_decode((string) $qt['items_json'], true) ?: [];

    /* Prefill from an order when requested. */
    $fromOrder = (int) get('from_order', 0);
    if ($isNew && $fromOrder > 0) {
        $order = fetch_one('SELECT * FROM orders WHERE id = ?', [$fromOrder]);
        if ($order) {
            $qt['client_name']    = $order['customer_name'];
            $qt['client_phone']   = $order['phone'];
            $qt['client_email']   = (string) $order['email'];
            $qt['client_address'] = $order['address'];
            foreach (fetch_all('SELECT * FROM order_items WHERE order_id = ?', [$fromOrder]) as $oi) {
                $items[] = [
                    'description' => $oi['product_name'],
                    'unit'        => 'pcs',
                    'qty'         => (float) $oi['quantity'],
                    'rate'        => (float) $oi['unit_price'],
                    'total'       => (float) $oi['line_total'],
                ];
            }
        }
    }

    if (!$items) {
        $items = [['description' => '', 'unit' => 'pcs', 'qty' => 1, 'rate' => 0, 'total' => 0]];
    }

    $actions = [['label' => '← All quotations', 'href' => admin_url('quotations'), 'class' => 'btn-ghost']];
    if (!$isNew) {
        $actions[] = ['label' => 'Print view', 'href' => admin_url('quotations?print=' . (int) $qt['id']), 'class' => 'btn-ghost'];
    }
    admin_header($isNew ? 'Create a quotation' : 'Quotation ' . $qt['quote_ref'], 'Add line items and the totals calculate automatically', $actions);
    ?>
    <form method="post" id="quotationForm" data-dirty-guard>
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int) $qt['id'] ?>">

      <div class="a-grid" style="grid-template-columns:minmax(0,2fr) minmax(280px,1fr);gap:20px;align-items:start;">
        <div>
          <div class="a-card">
            <div class="a-card-head"><h2>Client</h2></div>
            <div class="form-row">
              <div class="form-group">
                <label for="client_name">Client name <span class="req">*</span></label>
                <input type="text" id="client_name" name="client_name" required value="<?= e((string) $qt['client_name']) ?>">
              </div>
              <div class="form-group">
                <label for="client_company">Company / organisation</label>
                <input type="text" id="client_company" name="client_company" value="<?= e((string) $qt['client_company']) ?>">
              </div>
              <div class="form-group">
                <label for="client_phone">Phone</label>
                <input type="text" id="client_phone" name="client_phone" value="<?= e((string) $qt['client_phone']) ?>">
              </div>
              <div class="form-group">
                <label for="client_email">Email</label>
                <input type="email" id="client_email" name="client_email" value="<?= e((string) $qt['client_email']) ?>">
              </div>
            </div>
            <div class="form-group">
              <label for="client_address">Address</label>
              <textarea id="client_address" name="client_address" style="min-height:70px;"><?= e((string) $qt['client_address']) ?></textarea>
            </div>
          </div>

          <div class="a-card">
            <div class="a-card-head">
              <h2>Line items</h2>
              <button class="btn btn-ghost btn-sm" type="button" id="addQuoteLine">+ Add line</button>
            </div>
            <div id="quoteLines">
              <?php foreach ($items as $it): ?>
              <div class="quote-line form-row" style="align-items:flex-end;border-bottom:1px dashed var(--a-border);padding-bottom:12px;margin-bottom:12px;">
                <div class="form-group" style="flex:3;min-width:200px;">
                  <label>Description</label>
                  <input type="text" name="item_desc[]" value="<?= e((string) $it['description']) ?>" placeholder="19 Litre refill bottle">
                </div>
                <div class="form-group" style="max-width:110px;">
                  <label>Quantity</label>
                  <input type="number" step="0.01" class="q-qty" name="item_qty[]" value="<?= e((string) $it['qty']) ?>">
                </div>
                <div class="form-group" style="max-width:100px;">
                  <label>Unit</label>
                  <input type="text" name="item_unit[]" value="<?= e((string) $it['unit']) ?>" placeholder="pcs">
                </div>
                <div class="form-group" style="max-width:120px;">
                  <label>Rate (PKR)</label>
                  <input type="number" step="0.01" class="q-rate" name="item_rate[]" value="<?= e((string) $it['rate']) ?>">
                </div>
                <div class="form-group" style="max-width:130px;">
                  <label>Amount</label>
                  <div class="q-total" style="padding:10px 0;font-weight:700;">Rs 0</div>
                  <input type="hidden" class="q-total-input" value="0">
                </div>
                <div class="form-group" style="max-width:44px;">
                  <button class="btn-icon btn-icon-danger remove-line" type="button" title="Remove line">
                    <svg viewBox="0 0 24 24"><path d="M19 13H5v-2h14v2z"/></svg>
                  </button>
                </div>
              </div>
              <?php endforeach; ?>
            </div>

            <div class="form-row" style="margin-top:16px;">
              <div class="form-group">
                <label for="quoteDiscount">Discount (PKR)</label>
                <input type="number" step="0.01" id="quoteDiscount" name="discount" value="<?= e((string) $qt['discount']) ?>">
              </div>
              <div class="form-group">
                <label for="quoteDelivery">Delivery charges (PKR)</label>
                <input type="number" step="0.01" id="quoteDelivery" name="delivery_charges" value="<?= e((string) $qt['delivery_charges']) ?>">
                <span class="form-hint">Leave at 0 for free delivery.</span>
              </div>
            </div>

            <div style="border-top:2px solid var(--a-border);padding-top:14px;margin-top:6px;display:grid;gap:8px;">
              <div style="display:flex;justify-content:space-between;"><span>Subtotal</span><strong id="quoteSubtotal">Rs 0</strong></div>
              <div style="display:flex;justify-content:space-between;font-size:1.15rem;"><span>Total</span><strong id="quoteTotal" style="color:var(--a-brand);">Rs 0</strong></div>
              <input type="hidden" id="quoteSubtotalInput" name="subtotal_display" value="0">
              <input type="hidden" id="quoteTotalInput" name="total_display" value="0">
            </div>
          </div>

          <div class="a-card">
            <div class="a-card-head"><h2>Notes and terms</h2></div>
            <div class="form-group">
              <label for="notes">Notes for the client</label>
              <textarea id="notes" name="notes" style="min-height:90px;"><?= e((string) $qt['notes']) ?></textarea>
            </div>
            <div class="form-group">
              <label for="terms">Terms and conditions</label>
              <textarea id="terms" name="terms" style="min-height:140px;"><?= e((string) $qt['terms']) ?></textarea>
            </div>
          </div>
        </div>

        <aside>
          <div class="a-card">
            <div class="a-card-head"><h2>Details</h2></div>
            <div class="form-group">
              <label for="valid_until">Valid until</label>
              <input type="date" id="valid_until" name="valid_until" value="<?= e((string) $qt['valid_until']) ?>">
            </div>
            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" name="status">
                <?php foreach (['draft', 'sent', 'accepted', 'declined', 'expired'] as $s): ?>
                <option value="<?= e($s) ?>" <?= $qt['status'] === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <?php if (!$isNew): ?>
              <p class="form-hint">
                Reference <strong><?= e($qt['quote_ref']) ?></strong><br>
                <?= $qt['sent_at'] ? 'Sent to ' . e((string) $qt['sent_to']) . ' on ' . pretty_date($qt['sent_at']) : 'Not sent yet' ?>
              </p>
            <?php endif; ?>
            <button class="btn btn-primary btn-block" type="submit">Save quotation</button>
          </div>

          <?php if (!$isNew): ?>
          <div class="a-card">
            <div class="a-card-head"><h2>Send</h2></div>
            <button class="btn btn-ok btn-block" type="submit" name="action" value="send"
                    onclick="return confirm('Email this quotation to <?= e((string) $qt['client_email']) ?>?')">Email the quotation</button>
            <a class="btn btn-ghost btn-block" style="margin-top:9px;" href="<?= e(admin_url('quotations?print=' . (int) $qt['id'])) ?>">Print view</a>
            <?php if ($qt['client_phone']): ?>
            <a class="btn btn-ghost btn-block" style="margin-top:9px;" target="_blank" rel="noopener"
               href="<?= e(wa_link((string) $qt['client_phone'], 'Hello ' . $qt['client_name'] . ', your quotation ' . $qt['quote_ref'] . ' from Pak-Everests is ready. Total: ' . money($qt['total']) . '.')) ?>">
              Notify on WhatsApp
            </a>
            <?php endif; ?>
          </div>

          <div class="a-card">
            <button class="btn btn-danger btn-block" type="submit" name="action" value="delete"
                    onclick="return confirm('Delete this quotation permanently?')">Delete quotation</button>
          </div>
          <?php endif; ?>
        </aside>
      </div>
    </form>
    <?php
    admin_footer();
    return;
}

/* ---- List ---------------------------------------------------------------- */
$list = admin_list('quotations', ['order' => 'created_at DESC', 'perPage' => 25]);

admin_header('Quotations', 'Create and send quotations to corporate and bulk buyers', [
    ['label' => '+ New quotation', 'href' => admin_url('quotations?edit=new')],
]);
?>
<div class="a-card">
  <?php if (!$list['rows']): ?>
    <div class="empty">No quotations yet. Create your first one.</div>
  <?php else: ?>
  <div class="a-table-wrap">
    <table class="a-table">
      <thead><tr><th>Reference</th><th>Client</th><th>Total</th><th>Valid until</th><th>Status</th><th>Created</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($list['rows'] as $qt): ?>
        <tr>
          <td><strong><?= e($qt['quote_ref']) ?></strong></td>
          <td><?= e($qt['client_name']) ?><br><small style="color:var(--a-muted);"><?= e((string) $qt['client_company']) ?></small></td>
          <td><strong><?= money($qt['total']) ?></strong></td>
          <td><small><?= $qt['valid_until'] ? pretty_date($qt['valid_until']) : '—' ?></small></td>
          <td><?= status_pill($qt['status']) ?></td>
          <td><small><?= pretty_date($qt['created_at']) ?></small></td>
          <td class="actions">
            <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('quotations?edit=' . (int) $qt['id'])) ?>">Edit</a>
            <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('quotations?print=' . (int) $qt['id'])) ?>">Print</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?= pagination_links($list['pagination'], admin_url('quotations')) ?>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
