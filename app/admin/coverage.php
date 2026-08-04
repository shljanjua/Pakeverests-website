<?php
/** Delivery coverage areas. */
declare(strict_types=1);

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'save':
            $data = [
                'area_name'        => post('area_name'),
                'district'         => post('district'),
                'description'      => post('description'),
                'delivery_days'    => post('delivery_days'),
                'min_order'        => post('min_order'),
                'is_free_delivery' => post('is_free_delivery') ? 1 : 0,
                'is_active'        => post('is_active') ? 1 : 0,
                'sort_order'       => (int) post('sort_order', 0),
            ];
            if ($id > 0) { db_update('coverage_areas', $data, $id); flash('success', 'Area updated.'); }
            else { $id = db_insert('coverage_areas', $data); flash('success', 'Area added.'); }
            admin_log('Saved coverage area', 'coverage_areas', $id);
            redirect('admin/coverage');
            break;
        case 'toggle':
            $col = post('column');
            if (in_array($col, ['is_active', 'is_free_delivery'], true)) {
                q("UPDATE coverage_areas SET `$col` = IF(`$col` = 1, 0, 1) WHERE id = ?", [$id]);
            }
            redirect('admin/coverage');
            break;
        case 'delete':
            db_delete('coverage_areas', $id);
            flash('success', 'Area deleted.');
            redirect('admin/coverage');
            break;
    }
}

$editId = (int) get('edit', 0);
$area = $editId > 0 ? fetch_one('SELECT * FROM coverage_areas WHERE id = ?', [$editId]) : [
    'id' => 0, 'area_name' => '', 'district' => 'Rawalpindi', 'description' => '', 'delivery_days' => 'Daily',
    'min_order' => '1 bottle', 'is_free_delivery' => 1, 'is_active' => 1, 'sort_order' => 0,
];
$areas = fetch_all('SELECT * FROM coverage_areas ORDER BY sort_order ASC, area_name ASC');

admin_header('Delivery Areas', 'Areas shown on the coverage page, the order form and in the footer');
?>
<div class="a-grid" style="grid-template-columns:minmax(0,1fr) minmax(300px,380px);gap:20px;align-items:start;">
  <div class="a-card">
    <div class="a-card-head"><h2>Coverage areas (<?= count($areas) ?>)</h2></div>
    <?php if (!$areas): ?>
      <div class="empty">No areas defined yet.</div>
    <?php else: ?>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead><tr><th>Area</th><th>District</th><th>Schedule</th><th>Free</th><th>Active</th><th>Order</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($areas as $a): ?>
          <tr>
            <td><strong><?= e($a['area_name']) ?></strong><br><small style="color:var(--a-muted);"><?= e(excerpt($a['description'], 60)) ?></small></td>
            <td><small><?= e((string) $a['district']) ?></small></td>
            <td><small><?= e((string) $a['delivery_days']) ?></small></td>
            <td><?= toggle_button((int) $a['id'], 'is_free_delivery', (int) $a['is_free_delivery'] === 1, 'Free delivery') ?></td>
            <td><?= toggle_button((int) $a['id'], 'is_active', (int) $a['is_active'] === 1, 'Active') ?></td>
            <td><?= (int) $a['sort_order'] ?></td>
            <td class="actions">
              <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('coverage?edit=' . (int) $a['id'])) ?>">Edit</a>
              <?= delete_button((int) $a['id'], 'Delete this delivery area?') ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <aside class="a-card">
    <div class="a-card-head"><h2><?= $editId ? 'Edit area' : 'Add an area' ?></h2></div>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int) $area['id'] ?>">
      <div class="form-group">
        <label for="area_name">Area name <span class="req">*</span></label>
        <input type="text" id="area_name" name="area_name" required value="<?= e((string) $area['area_name']) ?>">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="district">District</label>
          <input type="text" id="district" name="district" value="<?= e((string) $area['district']) ?>">
        </div>
        <div class="form-group">
          <label for="delivery_days">Delivery schedule</label>
          <input type="text" id="delivery_days" name="delivery_days" list="schedList" value="<?= e((string) $area['delivery_days']) ?>">
          <datalist id="schedList"><option value="Daily"><option value="Alternate days"><option value="Twice a week"><option value="Weekly"><option value="On request"></datalist>
        </div>
        <div class="form-group">
          <label for="min_order">Minimum order</label>
          <input type="text" id="min_order" name="min_order" value="<?= e((string) $area['min_order']) ?>">
        </div>
        <div class="form-group">
          <label for="sort_order">Order</label>
          <input type="number" id="sort_order" name="sort_order" value="<?= (int) $area['sort_order'] ?>">
        </div>
      </div>
      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" style="min-height:90px;"><?= e((string) $area['description']) ?></textarea>
      </div>
      <label class="check"><input type="checkbox" name="is_free_delivery" value="1" <?= (int) $area['is_free_delivery'] === 1 ? 'checked' : '' ?>> Free delivery</label>
      <label class="check"><input type="checkbox" name="is_active" value="1" <?= (int) $area['is_active'] === 1 ? 'checked' : '' ?>> Active</label>
      <button class="btn btn-primary btn-block" type="submit"><?= $editId ? 'Save changes' : 'Add area' ?></button>
      <?php if ($editId): ?><a class="btn btn-ghost btn-block" style="margin-top:8px;" href="<?= e(admin_url('coverage')) ?>">Cancel</a><?php endif; ?>
    </form>
  </aside>
</div>
<?php admin_footer(); ?>
