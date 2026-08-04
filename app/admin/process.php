<?php
/** Eight stage purification process editor. */
declare(strict_types=1);

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'save':
            $data = [
                'stage_no'        => (int) post('stage_no', 1),
                'title'           => post('title'),
                'subtitle'        => post('subtitle'),
                'summary'         => post('summary'),
                'details'         => post('details'),
                'what_it_removes' => post('what_it_removes'),
                'is_active'       => post('is_active') ? 1 : 0,
            ];
            $current = $id > 0 ? fetch_one('SELECT image_path FROM process_stages WHERE id = ?', [$id]) : [];
            $data['image_path'] = admin_upload_field('image_path', 'process', (string) ($current['image_path'] ?? ''));

            if ($id > 0) { db_update('process_stages', $data, $id); flash('success', 'Stage updated.'); }
            else { $id = db_insert('process_stages', $data); flash('success', 'Stage added.'); }
            admin_log('Saved process stage', 'process_stages', $id);
            redirect('admin/process');
            break;
        case 'delete':
            db_delete('process_stages', $id);
            flash('success', 'Stage deleted.');
            redirect('admin/process');
            break;
    }
}

$editId = (int) get('edit', 0);
$nextNo = (int) fetch_val('SELECT COALESCE(MAX(stage_no),0)+1 FROM process_stages', [], 1);
$stage = $editId > 0 ? fetch_one('SELECT * FROM process_stages WHERE id = ?', [$editId]) : [
    'id' => 0, 'stage_no' => $nextNo, 'title' => '', 'subtitle' => '', 'summary' => '',
    'details' => '', 'what_it_removes' => '', 'image_path' => '', 'is_active' => 1,
];
$stages = fetch_all('SELECT * FROM process_stages ORDER BY stage_no ASC');

admin_header('Purification Process', 'The eight stages shown on the process page and the home page', [
    ['label' => 'View page', 'href' => url('purification-process'), 'class' => 'btn-ghost'],
]);
?>
<div class="a-grid" style="grid-template-columns:minmax(0,1fr) minmax(320px,440px);gap:20px;align-items:start;">
  <div class="a-card">
    <div class="a-card-head"><h2>Stages (<?= count($stages) ?>)</h2></div>
    <?php if (!$stages): ?>
      <div class="empty">No stages defined.</div>
    <?php else: ?>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead><tr><th>#</th><th>Stage</th><th>Summary</th><th>Active</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($stages as $s): ?>
          <tr>
            <td><strong style="font-size:1.1rem;color:var(--a-brand);"><?= (int) $s['stage_no'] ?></strong></td>
            <td><strong><?= e($s['title']) ?></strong><br><small style="color:var(--a-muted);"><?= e((string) $s['subtitle']) ?></small></td>
            <td><small><?= e(excerpt($s['summary'], 80)) ?></small></td>
            <td><?= (int) $s['is_active'] === 1 ? '<span class="pill pill-ok">Active</span>' : '<span class="pill pill-muted">Hidden</span>' ?></td>
            <td class="actions">
              <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('process?edit=' . (int) $s['id'])) ?>">Edit</a>
              <?= delete_button((int) $s['id'], 'Delete this stage?') ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <aside class="a-card">
    <div class="a-card-head"><h2><?= $editId ? 'Edit stage ' . (int) $stage['stage_no'] : 'Add a stage' ?></h2></div>
    <form method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int) $stage['id'] ?>">
      <div class="form-row">
        <div class="form-group">
          <label for="stage_no">Stage number</label>
          <input type="number" id="stage_no" name="stage_no" min="1" max="20" value="<?= (int) $stage['stage_no'] ?>">
        </div>
        <div class="form-group">
          <label for="title">Title <span class="req">*</span></label>
          <input type="text" id="title" name="title" required value="<?= e((string) $stage['title']) ?>">
        </div>
      </div>
      <div class="form-group">
        <label for="subtitle">Subtitle</label>
        <input type="text" id="subtitle" name="subtitle" value="<?= e((string) $stage['subtitle']) ?>">
      </div>
      <div class="form-group">
        <label for="summary">Short summary (shown on the home page)</label>
        <textarea id="summary" name="summary" style="min-height:80px;"><?= e((string) $stage['summary']) ?></textarea>
      </div>
      <div class="form-group">
        <label for="details">Full explanation (HTML allowed)</label>
        <textarea id="details" name="details" class="code" style="min-height:220px;"><?= e((string) $stage['details']) ?></textarea>
        <span class="form-hint">Use &lt;p&gt; and &lt;h3&gt;. The stage title is already an H2 on the page.</span>
      </div>
      <div class="form-group">
        <label for="what_it_removes">What this stage handles</label>
        <textarea id="what_it_removes" name="what_it_removes" style="min-height:70px;"><?= e((string) $stage['what_it_removes']) ?></textarea>
      </div>
      <?php image_field('image_path', (string) $stage['image_path'], 'Stage photograph (optional)'); ?>
      <label class="check"><input type="checkbox" name="is_active" value="1" <?= (int) $stage['is_active'] === 1 ? 'checked' : '' ?>> Active</label>
      <button class="btn btn-primary btn-block" type="submit"><?= $editId ? 'Save stage' : 'Add stage' ?></button>
      <?php if ($editId): ?><a class="btn btn-ghost btn-block" style="margin-top:8px;" href="<?= e(admin_url('process')) ?>">Cancel</a><?php endif; ?>
    </form>
  </aside>
</div>
<?php admin_footer(); ?>
