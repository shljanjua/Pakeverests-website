<?php
/** Photo gallery manager. */
declare(strict_types=1);

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'save':
            $data = [
                'title'      => post('title'),
                'caption'    => post('caption'),
                'alt_text'   => post('alt_text') ?: post('title'),
                'category'   => post('category') ?: 'Plant',
                'sort_order' => (int) post('sort_order', 0),
                'is_active'  => post('is_active') ? 1 : 0,
            ];
            $current = $id > 0 ? fetch_one('SELECT image_path FROM gallery WHERE id = ?', [$id]) : [];
            $data['image_path'] = admin_upload_field('image_path', 'gallery', (string) ($current['image_path'] ?? ''));

            if ($id > 0) {
                db_update('gallery', $data, $id);
                flash('success', 'Photo updated.');
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $id = db_insert('gallery', $data);
                flash('success', 'Photo added.');
            }
            admin_log('Saved gallery photo', 'gallery', $id);
            redirect('admin/gallery');
            break;

        case 'bulk_upload':
            $added = 0;
            if (!empty($_FILES['photos']['name'][0])) {
                foreach ($_FILES['photos']['name'] as $i => $fname) {
                    if ($fname === '') { continue; }
                    $file = [
                        'name' => $_FILES['photos']['name'][$i], 'type' => $_FILES['photos']['type'][$i],
                        'tmp_name' => $_FILES['photos']['tmp_name'][$i], 'error' => $_FILES['photos']['error'][$i],
                        'size' => $_FILES['photos']['size'][$i],
                    ];
                    $up = handle_upload($file, 'gallery');
                    if ($up['ok']) {
                        media_record($up, '', 'gallery');
                        $title = ucwords(str_replace('-', ' ', pathinfo($up['name'], PATHINFO_FILENAME)));
                        db_insert('gallery', [
                            'title'      => $title,
                            'caption'    => '',
                            'image_path' => $up['path'],
                            'alt_text'   => $title,
                            'category'   => post('bulk_category') ?: 'Plant',
                            'sort_order' => 0,
                            'is_active'  => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                        $added++;
                    } else {
                        flash('error', $up['error']);
                    }
                }
            }
            admin_log('Bulk uploaded ' . $added . ' gallery photos', 'gallery');
            flash('success', $added . ' photo(s) uploaded. Edit each one to add a title and caption.');
            redirect('admin/gallery');
            break;

        case 'toggle':
            q('UPDATE gallery SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?', [$id]);
            redirect('admin/gallery');
            break;

        case 'delete':
            db_delete('gallery', $id);
            admin_log('Deleted gallery photo', 'gallery', $id);
            flash('success', 'Photo deleted.');
            redirect('admin/gallery');
            break;
    }
}

$editId = (int) get('edit', 0);
$item = $editId > 0
    ? fetch_one('SELECT * FROM gallery WHERE id = ?', [$editId])
    : ['id' => 0, 'title' => '', 'caption' => '', 'image_path' => '', 'alt_text' => '', 'category' => 'Plant', 'sort_order' => 0, 'is_active' => 1];

$items = fetch_all('SELECT * FROM gallery ORDER BY sort_order ASC, id DESC');
$cats  = ['Plant', 'Quality', 'Delivery', 'Custom Labels', 'Customers', 'Events', 'Team'];

admin_header('Photo Gallery', 'Upload and organise the photographs shown on the gallery page');
?>

<div class="a-grid" style="grid-template-columns:minmax(0,1fr) minmax(300px,380px);gap:20px;align-items:start;">
  <div class="a-card">
    <div class="a-card-head"><h2>Gallery photos (<?= count($items) ?>)</h2></div>
    <?php if (!$items): ?>
      <div class="empty">No photos yet. Use the upload panel to add your first images.</div>
    <?php else: ?>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead><tr><th>Photo</th><th>Title</th><th>Category</th><th>Order</th><th>Visible</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($items as $g): ?>
          <tr>
            <td><img class="thumb" src="<?= e(media_url($g['image_path'], 'photo')) ?>" alt=""></td>
            <td><strong><?= e($g['title']) ?></strong><br><small style="color:var(--a-muted);"><?= e(excerpt($g['caption'], 60)) ?></small></td>
            <td><small><?= e($g['category']) ?></small></td>
            <td><?= (int) $g['sort_order'] ?></td>
            <td><?= toggle_button((int) $g['id'], 'is_active', (int) $g['is_active'] === 1, 'Visible') ?></td>
            <td class="actions">
              <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('gallery?edit=' . (int) $g['id'])) ?>">Edit</a>
              <?= delete_button((int) $g['id'], 'Delete this photo from the gallery?') ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <aside>
    <div class="a-card">
      <div class="a-card-head"><h2>Bulk upload</h2></div>
      <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="bulk_upload">
        <div class="form-group">
          <label for="photos">Select photos (multiple allowed)</label>
          <input type="file" id="photos" name="photos[]" accept="image/*" multiple required>
          <span class="form-hint">WebP recommended. Titles are generated from filenames and can be edited afterwards.</span>
        </div>
        <div class="form-group">
          <label for="bulk_category">Category</label>
          <select id="bulk_category" name="bulk_category">
            <?php foreach ($cats as $c): ?><option value="<?= e($c) ?>"><?= e($c) ?></option><?php endforeach; ?>
          </select>
        </div>
        <button class="btn btn-primary btn-block" type="submit">Upload photos</button>
      </form>
    </div>

    <div class="a-card">
      <div class="a-card-head"><h2><?= $editId ? 'Edit photo' : 'Add a single photo' ?></h2></div>
      <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
        <?php image_field('image_path', (string) $item['image_path'], 'Photograph'); ?>
        <div class="form-group">
          <label for="title">Title <span class="req">*</span></label>
          <input type="text" id="title" name="title" required value="<?= e((string) $item['title']) ?>">
        </div>
        <div class="form-group">
          <label for="caption">Caption</label>
          <textarea id="caption" name="caption" style="min-height:70px;"><?= e((string) $item['caption']) ?></textarea>
        </div>
        <div class="form-group">
          <label for="alt_text">Alt text (for accessibility and SEO)</label>
          <input type="text" id="alt_text" name="alt_text" value="<?= e((string) $item['alt_text']) ?>">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="category">Category</label>
            <input type="text" id="category" name="category" list="galCats" value="<?= e((string) $item['category']) ?>">
            <datalist id="galCats"><?php foreach ($cats as $c): ?><option value="<?= e($c) ?>"><?php endforeach; ?></datalist>
          </div>
          <div class="form-group">
            <label for="sort_order">Order</label>
            <input type="number" id="sort_order" name="sort_order" value="<?= (int) $item['sort_order'] ?>">
          </div>
        </div>
        <label class="check"><input type="checkbox" name="is_active" value="1" <?= (int) $item['is_active'] === 1 ? 'checked' : '' ?>> Visible on the website</label>
        <button class="btn btn-primary btn-block" type="submit"><?= $editId ? 'Save changes' : 'Add photo' ?></button>
        <?php if ($editId): ?><a class="btn btn-ghost btn-block" style="margin-top:8px;" href="<?= e(admin_url('gallery')) ?>">Cancel editing</a><?php endif; ?>
      </form>
    </div>
  </aside>
</div>
<?php admin_footer(); ?>
