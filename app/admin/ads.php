<?php
/** Monetisation: AdSense, Adsterra, Monetag codes and ads.txt. */
declare(strict_types=1);

$placements = [
    'header'         => 'Header banner (below the navigation)',
    'content_top'    => 'In-content, top of the page body',
    'content_middle' => 'In-content, middle of the page body',
    'sidebar'        => 'Sidebar (blog and product pages)',
    'footer'         => 'Footer banner (above the newsletter)',
    'social_bar'     => 'Adsterra social bar (floating)',
    'popunder'       => 'Adsterra popunder',
    'push'           => 'Monetag push notification',
];

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'save_slot':
            $data = [
                'name'       => post('name'),
                'network'    => post('network'),
                'placement'  => array_key_exists(post('placement'), $placements) ? post('placement') : 'header',
                'code'       => (string) ($_POST['code'] ?? ''),
                'is_active'  => post('is_active') ? 1 : 0,
                'sort_order' => (int) post('sort_order', 0),
            ];
            if ($id > 0) { db_update('ad_slots', $data, $id); flash('success', 'Ad slot saved.'); }
            else { $data['created_at'] = date('Y-m-d H:i:s'); $id = db_insert('ad_slots', $data); flash('success', 'Ad slot created.'); }
            admin_log('Saved ad slot', 'ad_slots', $id);
            redirect('admin/ads');
            break;

        case 'toggle':
            q('UPDATE ad_slots SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?', [$id]);
            redirect('admin/ads');
            break;

        case 'delete':
            db_delete('ad_slots', $id);
            flash('success', 'Ad slot deleted.');
            redirect('admin/ads');
            break;

        case 'save_settings':
            settings_save([
                'ads_enabled'          => post('ads_enabled') ? '1' : '0',
                'adsense_publisher_id' => post('adsense_publisher_id'),
                'adsense_auto_ads'     => post('adsense_auto_ads') ? '1' : '0',
                'ads_txt'              => (string) ($_POST['ads_txt'] ?? ''),
            ]);
            admin_log('Updated monetisation settings');
            flash('success', 'Monetisation settings saved.');
            redirect('admin/ads');
            break;
    }
}

$editId = (int) get('edit', 0);
$slot = $editId > 0 ? fetch_one('SELECT * FROM ad_slots WHERE id = ?', [$editId]) : [
    'id' => 0, 'name' => '', 'network' => 'adsense', 'placement' => 'header', 'code' => '', 'is_active' => 0, 'sort_order' => 0,
];
$slots = fetch_all('SELECT * FROM ad_slots ORDER BY sort_order ASC, id ASC');

admin_header('Ads and Monetisation', 'AdSense, Adsterra, Monetag and ads.txt');
?>

<?php if (!setting_bool('ads_enabled')): ?>
<div class="alert alert-info">
  Monetisation is currently <strong>switched off</strong>, so no ad code is output on the website even where slots are
  marked active. Turn it on below once your ad accounts are approved.
</div>
<?php endif; ?>

<div class="a-card">
  <div class="a-card-head"><h2>Global settings</h2></div>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save_settings">
    <label class="check"><input type="checkbox" name="ads_enabled" value="1" <?= setting_bool('ads_enabled') ? 'checked' : '' ?>> Enable advertising across the website</label>
    <label class="check"><input type="checkbox" name="adsense_auto_ads" value="1" <?= setting_bool('adsense_auto_ads') ? 'checked' : '' ?>> Use Google AdSense auto ads (loads the AdSense script site-wide)</label>

    <div class="form-group">
      <label for="adsense_publisher_id">AdSense publisher ID</label>
      <input type="text" id="adsense_publisher_id" name="adsense_publisher_id"
             value="<?= e((string) setting('adsense_publisher_id')) ?>" placeholder="ca-pub-0000000000000000">
      <span class="form-hint">Found in your AdSense account under Account &rarr; Settings &rarr; Account information.</span>
    </div>

    <div class="form-group">
      <label for="ads_txt">ads.txt content</label>
      <textarea id="ads_txt" name="ads_txt" class="code" style="min-height:160px;" placeholder="google.com, pub-0000000000000000, DIRECT, f08c47fec0942fa0"><?= e((string) setting('ads_txt')) ?></textarea>
      <span class="form-hint">
        Published at <a href="<?= e(url('ads.txt')) ?>" target="_blank" rel="noopener"><?= e(SITE_URL) ?>/ads.txt</a>.
        Paste the lines each network gives you, one per line. AdSense, Adsterra and Monetag each provide their own line.
      </span>
    </div>

    <button class="btn btn-primary" type="submit">Save settings</button>
  </form>
</div>

<div class="a-grid" style="grid-template-columns:minmax(0,1fr) minmax(320px,440px);gap:20px;align-items:start;">
  <div class="a-card">
    <div class="a-card-head"><h2>Ad slots (<?= count($slots) ?>)</h2></div>
    <?php if (!$slots): ?>
      <div class="empty">No ad slots configured.</div>
    <?php else: ?>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead><tr><th>Name</th><th>Network</th><th>Placement</th><th>Code</th><th>Active</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($slots as $s): ?>
          <tr>
            <td><strong><?= e($s['name']) ?></strong></td>
            <td><small><?= e(ucfirst($s['network'])) ?></small></td>
            <td><small><?= e($placements[$s['placement']] ?? $s['placement']) ?></small></td>
            <td><?= trim((string) $s['code']) !== '' ? '<span class="pill pill-ok">Set</span>' : '<span class="pill pill-muted">Empty</span>' ?></td>
            <td><?= toggle_button((int) $s['id'], 'is_active', (int) $s['is_active'] === 1, 'Active') ?></td>
            <td class="actions">
              <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('ads?edit=' . (int) $s['id'])) ?>">Edit</a>
              <?= delete_button((int) $s['id'], 'Delete this ad slot?') ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>

    <h3 style="margin-top:22px;">Where each placement appears</h3>
    <dl class="detail-list">
      <?php foreach ($placements as $key => $desc): ?>
      <div class="detail-row"><dt><code><?= e($key) ?></code></dt><dd><?= e($desc) ?></dd></div>
      <?php endforeach; ?>
    </dl>
  </div>

  <aside class="a-card">
    <div class="a-card-head"><h2><?= $editId ? 'Edit ad slot' : 'Add an ad slot' ?></h2></div>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save_slot">
      <input type="hidden" name="id" value="<?= (int) $slot['id'] ?>">
      <div class="form-group">
        <label for="name">Slot name <span class="req">*</span></label>
        <input type="text" id="name" name="name" required value="<?= e((string) $slot['name']) ?>">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="network">Network</label>
          <select id="network" name="network">
            <?php foreach (['adsense' => 'Google AdSense', 'adsterra' => 'Adsterra', 'monetag' => 'Monetag', 'propeller' => 'PropellerAds', 'other' => 'Other / custom'] as $k => $v): ?>
            <option value="<?= e($k) ?>" <?= $slot['network'] === $k ? 'selected' : '' ?>><?= e($v) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="sort_order">Order</label>
          <input type="number" id="sort_order" name="sort_order" value="<?= (int) $slot['sort_order'] ?>">
        </div>
      </div>
      <div class="form-group">
        <label for="placement">Placement</label>
        <select id="placement" name="placement">
          <?php foreach ($placements as $k => $v): ?>
          <option value="<?= e($k) ?>" <?= $slot['placement'] === $k ? 'selected' : '' ?>><?= e($v) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="code">Ad code</label>
        <textarea id="code" name="code" class="code" style="min-height:200px;" placeholder="Paste the exact script or HTML your ad network gives you"><?= e((string) $slot['code']) ?></textarea>
        <span class="form-hint">
          Pasted exactly as given, without modification. Only paste code from networks you trust, because it runs on
          every page where the placement appears.
        </span>
      </div>
      <label class="check"><input type="checkbox" name="is_active" value="1" <?= (int) $slot['is_active'] === 1 ? 'checked' : '' ?>> Active</label>
      <button class="btn btn-primary btn-block" type="submit"><?= $editId ? 'Save slot' : 'Add slot' ?></button>
      <?php if ($editId): ?><a class="btn btn-ghost btn-block" style="margin-top:8px;" href="<?= e(admin_url('ads')) ?>">Cancel</a><?php endif; ?>
    </form>
  </aside>
</div>
<?php admin_footer(); ?>
