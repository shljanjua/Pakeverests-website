<?php
/** Agreement generator: delivery, distributor and dispenser agreements. */
declare(strict_types=1);

$types = [
    'delivery_agreement'   => 'Water Delivery Agreement',
    'distributor_agreement'=> 'Distributor Agreement',
    'dispenser_agreement'  => 'Dispenser Rental Agreement',
    'custom'               => 'Custom Agreement',
];

/** Build the default body text for an agreement type. */
function agreement_template(string $type, array $d): string
{
    $company   = site_name();
    $address   = (string) setting('address_full');
    $email     = contact_email();
    $phone     = primary_whatsapp();
    $party     = e($d['party_name'] ?: '________________');
    $company_p = e($d['party_company'] ?: '________________');
    $cnic      = e($d['party_cnic'] ?: '________________');
    $partyAddr = e($d['party_address'] ?: '________________');
    $territory = e($d['territory'] ?: '________________');
    $start     = $d['start_date'] ? pretty_date($d['start_date']) : '________________';
    $end       = $d['end_date'] ? pretty_date($d['end_date']) : '________________';
    $security  = $d['security_amount'] ? money($d['security_amount']) : '________________';
    $target    = e($d['monthly_target'] ?: '________________');
    $rates     = nl2br(e($d['rate_summary'] ?: ''));

    $header = "<h2>Parties</h2>
<p>This agreement is made on <strong>" . date('d F Y') . "</strong> between:</p>
<p><strong>1. {$company}</strong> (the Company), a Punjab Food Authority approved bottled drinking water establishment
of {$address}, telephone {$phone}, email {$email};</p>
<p><strong>2. {$party}</strong>" . ($d['party_company'] ? " of <strong>{$company_p}</strong>" : '') . ", CNIC {$cnic},
of {$partyAddr} (the Customer).</p>";

    switch ($type) {
        case 'distributor_agreement':
            return $header . "
<h2>1. Appointment and territory</h2>
<p>The Company appoints the Distributor as its authorised distributor for the territory of <strong>{$territory}</strong>.
Within that territory the Distributor is the sole appointed distributor for the listed product range for the term of
this agreement, provided the agreed targets are met. The Distributor shall not sell, supply or solicit customers
inside the territory of any other appointed distributor.</p>
<p>The Company reserves the right to serve national corporate accounts, government institutions and pre-existing
direct customers within the territory.</p>

<h2>2. Term</h2>
<p>This agreement commences on <strong>{$start}</strong> and continues until <strong>{$end}</strong>, renewable by
written agreement subject to a performance review. Either party may terminate with thirty (30) days written notice.
The Company may terminate immediately for a material breach, including territory encroachment, non-payment,
adulteration of product, or any act damaging to the brand.</p>

<h2>3. Security deposit</h2>
<p>The Distributor shall deposit <strong>{$security}</strong> as a refundable security. It secures stock supplied on
credit, returnable bottles, crates and any equipment issued. It is refunded within thirty (30) days of termination
after reconciliation of all accounts, stock, bottles and assets.</p>

<h2>4. Pricing, targets and payment</h2>
<p>Distributor pricing is set out in the confidential price schedule attached to this agreement. The Distributor
undertakes to observe the maximum retail price published by the Company. The agreed monthly volume target is
<strong>{$target}</strong>.</p>
<div>{$rates}</div>
<p>Opening orders are supplied against advance payment. A credit limit may be extended after a satisfactory trading
history of not less than three months. Supply is suspended on any account exceeding its limit or overdue period.</p>

<h2>5. Storage, handling and food safety</h2>
<p>The Distributor undertakes to store stock in a clean, dry, covered warehouse away from direct sunlight, heat and
any chemical, fuel, paint or pesticide; to observe first in first out stock rotation; to transport product in clean,
covered vehicles used only for food grade goods; and never to open, decant, refill, relabel, dilute or tamper with any
product. Tampering is an immediate termination event and will be reported to the Punjab Food Authority.</p>
<p>The Distributor permits inspection of storage premises and vehicles by the Company or by the Punjab Food Authority
on reasonable notice.</p>

<h2>6. Company property</h2>
<p>Returnable bottles, crates, pallets, racks, coolers, signage and any vehicle issued remain the property of the
Company. Loss and damage are governed by the Company damage policy and are recoverable against the security deposit.</p>

<h2>7. Branding</h2>
<p>The Distributor may use the Company name and logo only for distributing the product and only in the form supplied.
Marketing material, signage and vehicle branding require written approval. The Distributor shall not register any
domain name, social media handle or trademark containing the Company name.</p>

<h2>8. Confidentiality</h2>
<p>Pricing schedules, customer lists and margin structures are confidential and remain so for two years after
termination.</p>

<h2>9. Termination settlement</h2>
<p>On termination all outstanding invoices become immediately payable; unsold stock in saleable condition and within
shelf life may be returned for credit at the price paid, subject to inspection; all bottles, crates, equipment and
branded material are returned; use of the Company name ceases immediately; and the security deposit is reconciled and
the balance refunded within thirty (30) days.</p>

<h2>10. Dispute resolution and governing law</h2>
<p>Disputes follow the four stage process published by the Company: direct resolution, management review, independent
mediation and, failing settlement, arbitration by a sole arbitrator under the Arbitration Act 1940. The seat of
arbitration is Gujar Khan or Rawalpindi, District Rawalpindi, Punjab. This agreement is governed by the laws of the
Islamic Republic of Pakistan and the courts at Gujar Khan and Rawalpindi have exclusive jurisdiction.</p>";

        case 'dispenser_agreement':
            return $header . "
<h2>1. Equipment supplied</h2>
<p>The Company supplies one (1) hot and cold water dispenser to the Customer at the address stated above, on rental
terms, commencing <strong>{$start}</strong>.</p>

<h2>2. Rental and deposit</h2>
<p>Monthly rental is payable in advance. A refundable security deposit of <strong>{$security}</strong> is collected at
installation. Rental includes routine servicing and quarterly internal sanitisation by the Company.</p>
<div>{$rates}</div>

<h2>3. Ownership</h2>
<p>The dispenser remains the property of the Company at all times. It may not be sold, pledged, sub-let, modified or
moved to another address without the written consent of the Company.</p>

<h2>4. Customer responsibilities</h2>
<p>The Customer shall: use a voltage stabiliser; never run the hot tank dry; not open, modify or have the unit repaired
by any third party; keep the unit out of direct sunlight and on a level floor; use only water supplied by the Company
in the unit; and allow Company staff access for scheduled sanitisation.</p>

<h2>5. Damage and loss</h2>
<p>Normal wear and any fault arising in the cooling or heating system during normal use is repaired or replaced by the
Company at no cost. Damage from power surge without a stabiliser, unauthorised repair, running dry, physical impact,
fire, flood or customer transport is chargeable at repair cost. If the unit is lost, stolen, sold or not returned, the
full replacement value is payable. The published damage policy of the Company applies in full.</p>

<h2>6. Termination</h2>
<p>Either party may terminate with fifteen (15) days written notice. The unit is collected by the Company in working
condition and the security deposit is refunded within seven (7) working days, less any deduction assessed under the
damage policy. Rental is not refundable for a part month.</p>

<h2>7. Governing law</h2>
<p>This agreement is governed by the laws of the Islamic Republic of Pakistan. The courts at Gujar Khan and Rawalpindi
have exclusive jurisdiction.</p>";

        case 'delivery_agreement':
        default:
            return $header . "
<h2>1. Scope of supply</h2>
<p>The Company agrees to supply bottled drinking water to the Customer at the address stated above, on a regular
schedule agreed between the parties, commencing <strong>{$start}</strong>" . ($d['end_date'] ? " and continuing until <strong>{$end}</strong>" : '') . ".</p>

<h2>2. Products and rates</h2>
<div>{$rates}</div>
<p>Rates are inclusive of delivery within the published coverage area of the Company. The Company may revise rates with
fifteen (15) days written notice. Rates confirmed for a specific order are not changed after acceptance.</p>

<h2>3. Bottle security deposit</h2>
<p>Returnable 19 litre bottles are supplied against a refundable security deposit of <strong>{$security}</strong>
recorded against the Customer. The deposit secures the bottle only and is not an advance against the price of water.
Bottles remain the property of the Company at all times. The deposit is refunded in full when bottles are returned in
a usable condition, subject to the published damage policy.</p>

<h2>4. Delivery</h2>
<p>Deliveries are made between 8:00 AM and 9:00 PM, Monday to Sunday. Orders placed before 4:00 PM in daily route areas
are normally delivered the same day. Delivery times given are estimates. Where nobody is available to receive a
delivery at the confirmed address, the first and second attempts are rescheduled at no cost; after a third failed
attempt the order may be cancelled.</p>

<h2>5. Payment</h2>
<p>Payment is due on delivery unless a credit account has been agreed in writing. The Company accepts cash, EasyPaisa,
JazzCash and bank transfer to the Company account only. Supply may be suspended on any overdue account.</p>

<h2>6. Quality and storage</h2>
<p>The Company warrants that the water supplied is produced under Punjab Food Authority approved conditions and meets
applicable PSQCA drinking water requirements at the moment it leaves the plant. This warranty is conditional on
correct storage by the Customer, away from direct sunlight, heat and any chemical, fuel or paint.</p>
<p>Quality concerns must be reported within twenty four (24) hours of delivery with the batch code and filling date
printed on the bottle. Product found defective is replaced free of charge.</p>

<h2>7. Term and termination</h2>
<p>Either party may terminate this arrangement with seven (7) days written notice. On termination all outstanding
invoices become payable, bottles are returned and the security deposit is reconciled and refunded.</p>

<h2>8. Dispute resolution and governing law</h2>
<p>Disputes follow the published four stage process of the Company: direct resolution, management review, mediation and
arbitration under the Arbitration Act 1940. This agreement is governed by the laws of the Islamic Republic of Pakistan
and the courts at Gujar Khan and Rawalpindi have exclusive jurisdiction.</p>";
    }
}

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'save':
            $data = [
                'doc_type'        => array_key_exists(post('doc_type'), $types) ? post('doc_type') : 'delivery_agreement',
                'party_name'      => post('party_name'),
                'party_company'   => post('party_company'),
                'party_cnic'      => post('party_cnic'),
                'party_phone'     => post('party_phone'),
                'party_email'     => post('party_email'),
                'party_address'   => post('party_address'),
                'territory'       => post('territory'),
                'start_date'      => post('start_date') ?: null,
                'end_date'        => post('end_date') ?: null,
                'security_amount' => post('security_amount') !== '' ? (float) post('security_amount') : null,
                'monthly_target'  => post('monthly_target'),
                'rate_summary'    => post('rate_summary'),
                'body'            => post('body'),
                'status'          => in_array(post('status'), ['draft', 'sent', 'signed', 'expired', 'cancelled'], true) ? post('status') : 'draft',
                'updated_at'      => date('Y-m-d H:i:s'),
            ];
            if ($data['body'] === '') {
                $data['body'] = agreement_template($data['doc_type'], $data);
            }
            if ($id > 0) {
                db_update('agreements', $data, $id);
                flash('success', 'Agreement saved.');
            } else {
                $data['doc_ref']    = generate_ref('AGR');
                $data['created_by'] = (int) (admin_user()['id'] ?? 0);
                $data['created_at'] = date('Y-m-d H:i:s');
                $id = db_insert('agreements', $data);
                flash('success', 'Agreement created.');
            }
            admin_log('Saved agreement', 'agreements', $id);
            redirect('admin/agreements?edit=' . $id);
            break;

        case 'regenerate':
            $a = fetch_one('SELECT * FROM agreements WHERE id = ?', [$id]);
            if ($a) {
                db_update('agreements', ['body' => agreement_template($a['doc_type'], $a), 'updated_at' => date('Y-m-d H:i:s')], $id);
                flash('success', 'Agreement text regenerated from the template. Any manual edits were replaced.');
            }
            redirect('admin/agreements?edit=' . $id);
            break;

        case 'send':
            $a = fetch_one('SELECT * FROM agreements WHERE id = ?', [$id]);
            if ($a && filter_var($a['party_email'], FILTER_VALIDATE_EMAIL)) {
                $html = '<p>Dear ' . e($a['party_name']) . ',</p>'
                    . '<p>Please find your ' . e($types[$a['doc_type']] ?? 'agreement') . ' below, reference <strong>' . e($a['doc_ref']) . '</strong>.</p>'
                    . '<hr>' . rich_text($a['body'])
                    . '<hr><p>Please review, sign and return a copy. If anything needs to change, reply to this email or message us on WhatsApp at ' . e(primary_whatsapp()) . '.</p>';
                $ok = send_mail($a['party_email'], $types[$a['doc_type']] . ' — ' . $a['doc_ref'] . ' — ' . site_name(), $html, contact_email(), 'agreement');
                if ($ok) {
                    db_update('agreements', ['status' => 'sent', 'sent_to' => $a['party_email'], 'sent_at' => date('Y-m-d H:i:s')], $id);
                    admin_log('Sent agreement ' . $a['doc_ref'], 'agreements', $id);
                    flash('success', 'Agreement emailed to ' . $a['party_email'] . '.');
                } else {
                    flash('error', 'The email could not be sent. Check the SMTP settings, or use the print view and send it manually.');
                }
            } else {
                flash('error', 'This agreement has no valid email address.');
            }
            redirect('admin/agreements?edit=' . $id);
            break;

        case 'delete':
            db_delete('agreements', $id);
            flash('success', 'Agreement deleted.');
            redirect('admin/agreements');
            break;
    }
}

/* ---- Print view ---------------------------------------------------------- */
$printId = (int) get('print', 0);
if ($printId > 0) {
    $a = fetch_one('SELECT * FROM agreements WHERE id = ?', [$printId]);
    if (!$a) { flash('error', 'Agreement not found.'); redirect('admin/agreements'); }
    admin_header($types[$a['doc_type']] . ' — ' . $a['doc_ref'], 'Print view', [
        ['label' => '← Back', 'href' => admin_url('agreements?edit=' . $printId), 'class' => 'btn-ghost'],
    ]);
    ?>
    <div class="no-print" style="margin-bottom:16px;">
      <button class="btn btn-primary" type="button" onclick="window.print()">Print or save as PDF</button>
    </div>
    <div class="doc-preview">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:20px;border-bottom:2px solid #0b6fa4;padding-bottom:16px;margin-bottom:22px;">
        <div>
          <img src="<?= e(media_url((string) setting('logo_path'), 'logo')) ?>" alt="" style="height:52px;">
          <p style="margin:8px 0 0;font-size:.85rem;color:#55707f;">
            <?= e((string) setting('address_full')) ?><br>
            <?= e(contact_email()) ?> &middot; <?= e(primary_whatsapp()) ?>
          </p>
        </div>
        <div style="text-align:right;">
          <h1 style="margin:0;font-size:1.3rem;"><?= e($types[$a['doc_type']]) ?></h1>
          <p style="margin:6px 0 0;font-size:.85rem;">
            Reference: <strong><?= e($a['doc_ref']) ?></strong><br>
            Date: <?= pretty_date($a['created_at']) ?>
          </p>
        </div>
      </div>

      <?= rich_text($a['body']) ?>

      <div style="margin-top:56px;display:flex;gap:40px;justify-content:space-between;">
        <div style="flex:1;">
          <div style="border-top:1px solid #12303f;padding-top:8px;font-size:.86rem;">
            For and on behalf of<br><strong><?= e(site_name()) ?></strong><br>
            Name, signature and stamp
          </div>
        </div>
        <div style="flex:1;">
          <div style="border-top:1px solid #12303f;padding-top:8px;font-size:.86rem;">
            <strong><?= e($a['party_name']) ?></strong><?= $a['party_company'] ? '<br>' . e($a['party_company']) : '' ?><br>
            CNIC: <?= e((string) $a['party_cnic']) ?><br>
            Signature and date
          </div>
        </div>
      </div>

      <p style="margin-top:34px;font-size:.76rem;color:#86a2b1;text-align:center;">
        This document is issued by <?= e(site_name()) ?>, a <?= e((string) setting('license_authority')) ?> approved
        bottled drinking water establishment. Licence <?= e((string) setting('license_number')) ?>.
      </p>
    </div>
    <?php
    admin_footer();
    return;
}

/* ---- Editor -------------------------------------------------------------- */
$editId = get('edit');
if ($editId !== '') {
    $isNew = $editId === 'new';
    $a = $isNew ? [
        'id' => 0, 'doc_ref' => 'Generated on save', 'doc_type' => get('type', 'delivery_agreement'),
        'party_name' => get('name'), 'party_company' => '', 'party_cnic' => '', 'party_phone' => get('phone'),
        'party_email' => get('email'), 'party_address' => '', 'territory' => get('territory'),
        'start_date' => date('Y-m-d'), 'end_date' => '', 'security_amount' => '', 'monthly_target' => '',
        'rate_summary' => "19 Litre refill: Rs 250 per refill\nSecurity deposit: Rs 1,500 per bottle (refundable)\n12 Litre bottle: Rs 230\n6 Litre bottle: Rs 130\nDelivery: Free within the coverage area",
        'body' => '', 'status' => 'draft', 'sent_to' => '', 'sent_at' => '',
    ] : fetch_one('SELECT * FROM agreements WHERE id = ?', [(int) $editId]);
    if (!$a) { flash('error', 'Agreement not found.'); redirect('admin/agreements'); }

    $actions = [['label' => '← All agreements', 'href' => admin_url('agreements'), 'class' => 'btn-ghost']];
    if (!$isNew) {
        $actions[] = ['label' => 'Print view', 'href' => admin_url('agreements?print=' . (int) $a['id']), 'class' => 'btn-ghost'];
    }
    admin_header($isNew ? 'Generate an agreement' : 'Agreement ' . $a['doc_ref'], 'Fill in the details and the text is generated for you', $actions);
    ?>
    <form method="post" data-dirty-guard>
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">

      <div class="a-grid" style="grid-template-columns:minmax(0,2fr) minmax(280px,1fr);gap:20px;align-items:start;">
        <div>
          <div class="a-card">
            <div class="a-card-head"><h2>Party details</h2></div>
            <div class="form-row">
              <div class="form-group">
                <label for="doc_type">Agreement type</label>
                <select id="doc_type" name="doc_type">
                  <?php foreach ($types as $k => $label): ?>
                  <option value="<?= e($k) ?>" <?= $a['doc_type'] === $k ? 'selected' : '' ?>><?= e($label) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label for="party_name">Name <span class="req">*</span></label>
                <input type="text" id="party_name" name="party_name" required value="<?= e((string) $a['party_name']) ?>">
              </div>
              <div class="form-group">
                <label for="party_company">Company / firm</label>
                <input type="text" id="party_company" name="party_company" value="<?= e((string) $a['party_company']) ?>">
              </div>
              <div class="form-group">
                <label for="party_cnic">CNIC</label>
                <input type="text" id="party_cnic" name="party_cnic" value="<?= e((string) $a['party_cnic']) ?>">
              </div>
              <div class="form-group">
                <label for="party_phone">Phone</label>
                <input type="text" id="party_phone" name="party_phone" value="<?= e((string) $a['party_phone']) ?>">
              </div>
              <div class="form-group">
                <label for="party_email">Email</label>
                <input type="email" id="party_email" name="party_email" value="<?= e((string) $a['party_email']) ?>">
              </div>
            </div>
            <div class="form-group">
              <label for="party_address">Address</label>
              <textarea id="party_address" name="party_address" style="min-height:70px;"><?= e((string) $a['party_address']) ?></textarea>
            </div>
          </div>

          <div class="a-card">
            <div class="a-card-head"><h2>Commercial terms</h2></div>
            <div class="form-row">
              <div class="form-group">
                <label for="territory">Territory / delivery location</label>
                <input type="text" id="territory" name="territory" value="<?= e((string) $a['territory']) ?>">
              </div>
              <div class="form-group">
                <label for="security_amount">Security amount (PKR)</label>
                <input type="number" step="0.01" id="security_amount" name="security_amount" value="<?= e((string) $a['security_amount']) ?>">
              </div>
              <div class="form-group">
                <label for="monthly_target">Monthly target</label>
                <input type="text" id="monthly_target" name="monthly_target" value="<?= e((string) $a['monthly_target']) ?>">
              </div>
              <div class="form-group">
                <label for="start_date">Start date</label>
                <input type="date" id="start_date" name="start_date" value="<?= e((string) $a['start_date']) ?>">
              </div>
              <div class="form-group">
                <label for="end_date">End date</label>
                <input type="date" id="end_date" name="end_date" value="<?= e((string) $a['end_date']) ?>">
              </div>
            </div>
            <div class="form-group">
              <label for="rate_summary">Rates and pricing (one per line)</label>
              <textarea id="rate_summary" name="rate_summary" style="min-height:130px;"><?= e((string) $a['rate_summary']) ?></textarea>
            </div>
          </div>

          <div class="a-card">
            <div class="a-card-head">
              <h2>Agreement text</h2>
              <?php if (!$isNew): ?>
              <button class="btn btn-ghost btn-sm" type="submit" name="action" value="regenerate"
                      onclick="return confirm('Regenerate the text from the template? Any manual edits will be lost.')">
                Regenerate from template
              </button>
              <?php endif; ?>
            </div>
            <p class="form-hint" style="margin-bottom:8px;">
              Leave this blank when creating a new agreement and the standard text for the selected type is generated
              automatically. You can then edit any clause.
            </p>
            <textarea id="body" name="body" class="code tall" style="min-height:420px;"><?= e((string) $a['body']) ?></textarea>
          </div>
        </div>

        <aside>
          <div class="a-card">
            <div class="a-card-head"><h2>Status</h2></div>
            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" name="status">
                <?php foreach (['draft', 'sent', 'signed', 'expired', 'cancelled'] as $s): ?>
                <option value="<?= e($s) ?>" <?= $a['status'] === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <?php if (!$isNew): ?>
              <p class="form-hint">
                Reference <strong><?= e($a['doc_ref']) ?></strong><br>
                <?= $a['sent_at'] ? 'Sent to ' . e((string) $a['sent_to']) . ' on ' . pretty_date($a['sent_at']) : 'Not sent yet' ?>
              </p>
            <?php endif; ?>
            <button class="btn btn-primary btn-block" type="submit">Save agreement</button>
          </div>

          <?php if (!$isNew): ?>
          <div class="a-card">
            <div class="a-card-head"><h2>Send to the customer</h2></div>
            <button class="btn btn-ok btn-block" type="submit" name="action" value="send"
                    onclick="return confirm('Email this agreement to <?= e((string) $a['party_email']) ?>?')">
              Email the agreement
            </button>
            <a class="btn btn-ghost btn-block" style="margin-top:9px;" href="<?= e(admin_url('agreements?print=' . (int) $a['id'])) ?>">Open print view</a>
            <?php if ($a['party_phone']): ?>
            <a class="btn btn-ghost btn-block" style="margin-top:9px;" target="_blank" rel="noopener"
               href="<?= e(wa_link((string) $a['party_phone'], 'Hello ' . $a['party_name'] . ', your ' . $types[$a['doc_type']] . ' (' . $a['doc_ref'] . ') from Pak-Everests is ready. We are sending it to you now.')) ?>">
              Notify on WhatsApp
            </a>
            <?php endif; ?>
            <p class="form-hint" style="margin-top:10px;">
              Emailing requires SMTP to be configured under Settings &rarr; SMTP. The print view always works and can be
              saved as a PDF from the browser print dialogue.
            </p>
          </div>

          <div class="a-card">
            <button class="btn btn-danger btn-block" type="submit" name="action" value="delete"
                    onclick="return confirm('Delete this agreement permanently?')">Delete agreement</button>
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
$list = admin_list('agreements', ['order' => 'created_at DESC', 'perPage' => 25]);

admin_header('Agreements', 'Generate delivery, distributor and dispenser agreements', [
    ['label' => '+ New agreement', 'href' => admin_url('agreements?edit=new')],
]);
?>
<div class="a-card">
  <p class="form-hint" style="margin-bottom:14px;">
    Choose an agreement type, fill in the party details and the full legal text is generated automatically from the
    published Pak-Everests terms. You can then edit any clause, email it to the customer, or print it as a PDF.
  </p>
  <?php if (!$list['rows']): ?>
    <div class="empty">No agreements yet. Create your first one.</div>
  <?php else: ?>
  <div class="a-table-wrap">
    <table class="a-table">
      <thead><tr><th>Reference</th><th>Type</th><th>Party</th><th>Territory</th><th>Status</th><th>Created</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($list['rows'] as $a): ?>
        <tr>
          <td><strong><?= e($a['doc_ref']) ?></strong></td>
          <td><small><?= e($types[$a['doc_type']] ?? $a['doc_type']) ?></small></td>
          <td><?= e($a['party_name']) ?><br><small style="color:var(--a-muted);"><?= e((string) $a['party_company']) ?></small></td>
          <td><small><?= e((string) $a['territory']) ?></small></td>
          <td><?= status_pill($a['status']) ?></td>
          <td><small><?= pretty_date($a['created_at']) ?></small></td>
          <td class="actions">
            <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('agreements?edit=' . (int) $a['id'])) ?>">Edit</a>
            <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('agreements?print=' . (int) $a['id'])) ?>">Print</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?= pagination_links($list['pagination'], admin_url('agreements')) ?>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
