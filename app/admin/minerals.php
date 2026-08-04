<?php
/** Mineral profile editor. */
declare(strict_types=1);

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'save':
            $data = [
                'name'          => post('name'),
                'symbol'        => post('symbol'),
                'typical_value' => post('typical_value'),
                'unit'          => post('unit') ?: 'mg/L',
                'who_limit'     => post('who_limit'),
                'psqca_limit'   => post('psqca_limit'),
                'benefits'      => post('benefits'),
                'details'       => post('details'),
                'color'         => post('color'),
                'sort_order'    => (int) post('sort_order', 0),
                'is_active'     => post('is_active') ? 1 : 0,
            ];
            if ($id > 0) { db_update('minerals', $data, $id); flash('success', 'Mineral updated.'); }
            else { $id = db_insert('minerals', $data); flash('success', 'Mineral added.'); }
            admin_log('Saved mineral', 'minerals', $id);
            redirect('admin/minerals');
            break;
        case 'delete':
            db_delete('minerals', $id);
            flash('success', 'Mineral deleted.');
            redirect('admin/minerals');
            break;
    }
}

$editId = (int) get('edit', 0);
$m = $editId > 0 ? fetch_one('SELECT * FROM minerals WHERE id = ?', [$editId]) : [
    'id' => 0, 'name' => '', 'symbol' => '', 'typical_value' => '', 'unit' => 'mg/L', 'who_limit' => '',
    'psqca_limit' => '', 'benefits' => '', 'details' => '', 'color' => '#0b7cb2', 'sort_order' => 0, 'is_active' => 1,
];
$minerals = fetch_all('SELECT * FROM minerals ORDER BY sort_order ASC');

admin_header('Mineral Profile', 'Typical values and health benefits shown on the minerals page', [
    ['label' => 'View page', 'href' => url('minerals-and-benefits'), 'class' => 'btn-ghost'],
]);
?>
<div class="a-grid" style="grid-template-columns:minmax(0,1fr) minmax(320px,440px);gap:20px;align-items:start;">
  <div class="a-card">
    <div class="a-card-head"><h2>Minerals and parameters (<?= count($minerals) ?>)</h2></div>
    <?php if (!$minerals): ?>
      <div class="empty">No minerals defined.</div>
    <?php else: ?>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead><tr><th>Colour</th><th>Name</th><th>Typical</th><th>WHO</th><th>PSQCA</th><th>Active</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($minerals as $row): ?>
          <tr>
            <td><span style="display:inline-block;width:24px;height:24px;border-radius:7px;background:<?= e((string) $row['color']) ?>;"></span></td>
            <td><strong><?= e($row['name']) ?></strong> <small style="color:var(--a-muted);">(<?= e((string) $row['symbol']) ?>)</small></td>
            <td><?= e((string) $row['typical_value']) ?> <small><?= e($row['unit']) ?></small></td>
            <td><small><?= e((string) $row['who_limit']) ?></small></td>
            <td><small><?= e((string) $row['psqca_limit']) ?></small></td>
            <td><?= (int) $row['is_active'] === 1 ? '<span class="pill pill-ok">Active</span>' : '<span class="pill pill-muted">Hidden</span>' ?></td>
            <td class="actions">
              <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('minerals?edit=' . (int) $row['id'])) ?>">Edit</a>
              <?= delete_button((int) $row['id'], 'Delete this mineral?') ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <aside class="a-card">
    <div class="a-card-head"><h2><?= $editId ? 'Edit mineral' : 'Add a mineral' ?></h2></div>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
      <div class="form-row">
        <div class="form-group">
          <label for="name">Name <span class="req">*</span></label>
          <input type="text" id="name" name="name" required value="<?= e((string) $m['name']) ?>">
        </div>
        <div class="form-group">
          <label for="symbol">Symbol</label>
          <input type="text" id="symbol" name="symbol" value="<?= e((string) $m['symbol']) ?>" placeholder="Ca">
        </div>
        <div class="form-group">
          <label for="typical_value">Typical value</label>
          <input type="text" id="typical_value" name="typical_value" value="<?= e((string) $m['typical_value']) ?>" placeholder="40 - 60">
        </div>
        <div class="form-group">
          <label for="unit">Unit</label>
          <input type="text" id="unit" name="unit" value="<?= e((string) $m['unit']) ?>">
        </div>
        <div class="form-group">
          <label for="who_limit">WHO guideline</label>
          <input type="text" id="who_limit" name="who_limit" value="<?= e((string) $m['who_limit']) ?>">
        </div>
        <div class="form-group">
          <label for="psqca_limit">PSQCA limit</label>
          <input type="text" id="psqca_limit" name="psqca_limit" value="<?= e((string) $m['psqca_limit']) ?>">
        </div>
        <div class="form-group">
          <label for="color">Accent colour</label>
          <input type="color" id="color" name="color" value="<?= e((string) ($m['color'] ?: '#0b7cb2')) ?>">
        </div>
        <div class="form-group">
          <label for="sort_order">Order</label>
          <input type="number" id="sort_order" name="sort_order" value="<?= (int) $m['sort_order'] ?>">
        </div>
      </div>
      <div class="form-group">
        <label for="benefits">One line benefit summary</label>
        <textarea id="benefits" name="benefits" style="min-height:70px;"><?= e((string) $m['benefits']) ?></textarea>
      </div>
      <div class="form-group">
        <label for="details">Full explanation (HTML allowed)</label>
        <textarea id="details" name="details" class="code" style="min-height:200px;"><?= e((string) $m['details']) ?></textarea>
      </div>
      <label class="check"><input type="checkbox" name="is_active" value="1" <?= (int) $m['is_active'] === 1 ? 'checked' : '' ?>> Active</label>
      <button class="btn btn-primary btn-block" type="submit"><?= $editId ? 'Save mineral' : 'Add mineral' ?></button>
      <?php if ($editId): ?><a class="btn btn-ghost btn-block" style="margin-top:8px;" href="<?= e(admin_url('minerals')) ?>">Cancel</a><?php endif; ?>
    </form>
  </aside>
</div>
<?php admin_footer(); ?>
