<?php
/** Media library: upload images once and reuse the paths anywhere. */
declare(strict_types=1);

if (is_post()) {
    switch (post('action')) {
        case 'upload':
            $added = 0;
            if (!empty($_FILES['files']['name'][0])) {
                foreach ($_FILES['files']['name'] as $i => $fname) {
                    if ($fname === '') { continue; }
                    $file = [
                        'name' => $_FILES['files']['name'][$i], 'type' => $_FILES['files']['type'][$i],
                        'tmp_name' => $_FILES['files']['tmp_name'][$i], 'error' => $_FILES['files']['error'][$i],
                        'size' => $_FILES['files']['size'][$i],
                    ];
                    $up = handle_upload($file, post('folder') ?: 'general', array_merge(PE_IMAGE_TYPES, PE_DOC_TYPES));
                    if ($up['ok']) { media_record($up, post('alt_text'), post('folder') ?: 'general'); $added++; }
                    else { flash('error', $fname . ': ' . $up['error']); }
                }
            }
            admin_log('Uploaded ' . $added . ' media files');
            flash('success', $added . ' file(s) uploaded.');
            redirect('admin/media');
            break;

        case 'delete':
            $id  = (int) post('id');
            $row = fetch_one('SELECT * FROM media WHERE id = ?', [$id]);
            if ($row) {
                $abs = PE_ROOT . '/' . ltrim($row['file_path'], '/');
                if (is_file($abs)) { @unlink($abs); }
                db_delete('media', $id);
                admin_log('Deleted media file', 'media', $id);
                flash('success', 'File deleted from the server.');
            }
            redirect('admin/media');
            break;
    }
}

$folder = get('folder');
$where  = $folder !== '' ? 'folder = ?' : '1=1';
$params = $folder !== '' ? [$folder] : [];
$list   = admin_list('media', ['where' => $where, 'params' => $params, 'order' => 'id DESC', 'perPage' => 48]);
$folders = fetch_all('SELECT folder, COUNT(*) AS total FROM media GROUP BY folder ORDER BY folder ASC');
$totalSize = (int) fetch_val('SELECT COALESCE(SUM(file_size),0) FROM media', [], 0);

admin_header('Media Library', 'Every uploaded image and document in one place');
?>
<div class="a-card">
  <div class="a-card-head"><h2>Upload files</h2></div>
  <form method="post" enctype="multipart/form-data" class="filter-form">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="upload">
    <div class="form-group" style="flex:2;min-width:250px;">
      <label for="files">Choose files</label>
      <input type="file" id="files" name="files[]" multiple required accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
    </div>
    <div class="form-group">
      <label for="folder">Folder</label>
      <input type="text" id="folder" name="folder" list="folderList" value="general">
      <datalist id="folderList">
        <option value="general"><option value="products"><option value="gallery"><option value="blog">
        <option value="documents"><option value="labels"><option value="branding">
      </datalist>
    </div>
    <div class="form-group">
      <label for="alt_text">Alt text</label>
      <input type="text" id="alt_text" name="alt_text" placeholder="Optional description">
    </div>
    <button class="btn btn-primary" type="submit">Upload</button>
  </form>
  <p class="form-hint">Total library size: <?= number_format($totalSize / 1048576, 1) ?> MB across <?= (int) fetch_val('SELECT COUNT(*) FROM media', [], 0) ?> files.</p>
</div>

<div class="a-card">
  <div class="tabs">
    <a class="tab<?= $folder === '' ? ' is-active' : '' ?>" href="<?= e(admin_url('media')) ?>">All files</a>
    <?php foreach ($folders as $f): ?>
    <a class="tab<?= $folder === $f['folder'] ? ' is-active' : '' ?>" href="<?= e(admin_url('media?folder=' . rawurlencode($f['folder']))) ?>">
      <?= e($f['folder']) ?> (<?= (int) $f['total'] ?>)
    </a>
    <?php endforeach; ?>
  </div>

  <?php if (!$list['rows']): ?>
    <div class="empty">No files uploaded yet.</div>
  <?php else: ?>
  <div class="media-grid">
    <?php foreach ($list['rows'] as $m): ?>
    <div class="media-tile">
      <?php if ($m['file_type'] === 'image'): ?>
        <a href="/<?= e(ltrim($m['file_path'], '/')) ?>" target="_blank" rel="noopener">
          <img src="/<?= e(ltrim($m['file_path'], '/')) ?>" alt="<?= e((string) $m['alt_text']) ?>" loading="lazy">
        </a>
      <?php else: ?>
        <a href="/<?= e(ltrim($m['file_path'], '/')) ?>" target="_blank" rel="noopener"
           style="aspect-ratio:4/3;display:grid;place-items:center;background:var(--a-surface-2);color:var(--a-bad);font-weight:700;">
          <?= e(strtoupper((string) $m['extension'])) ?>
        </a>
      <?php endif; ?>
      <div class="media-tile-body">
        <span class="media-path"><?= e($m['file_path']) ?></span>
        <span><?= number_format((int) $m['file_size'] / 1024, 0) ?> KB &middot; <?= pretty_date($m['created_at']) ?></span>
        <div style="display:flex;gap:6px;">
          <button class="btn btn-ghost btn-sm" type="button" data-copy="<?= e($m['file_path']) ?>">Copy path</button>
          <form method="post" class="inline-form" data-confirm="Delete this file from the server permanently?">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
            <button class="btn btn-danger btn-sm" type="submit">Delete</button>
          </form>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?= pagination_links($list['pagination'], admin_url('media?folder=' . rawurlencode($folder))) ?>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
