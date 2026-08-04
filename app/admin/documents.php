<?php
/** Documents: licences, certificates, sample agreements and quotations. */
declare(strict_types=1);

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'save':
            $data = [
                'title'        => post('title'),
                'description'  => post('description'),
                'category'     => post('category') ?: 'Licences',
                'issued_by'    => post('issued_by'),
                'reference_no' => post('reference_no'),
                'issue_date'   => post('issue_date') ?: null,
                'expiry_date'  => post('expiry_date') ?: null,
                'is_public'    => post('is_public') ? 1 : 0,
                'sort_order'   => (int) post('sort_order', 0),
            ];
            $current = $id > 0 ? fetch_one('SELECT file_path, doc_type FROM documents WHERE id = ?', [$id]) : [];
            $allowed = array_merge(PE_IMAGE_TYPES, PE_DOC_TYPES);
            $path    = admin_upload_field('file_path', 'documents', (string) ($current['file_path'] ?? ''), $allowed);
            $data['file_path'] = $path;
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $data['doc_type'] = $ext === 'pdf' ? 'pdf' : 'image';

            if ($id > 0) {
                db_update('documents', $data, $id);
                flash('success', 'Document updated.');
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $id = db_insert('documents', $data);
                flash('success', 'Document added.');
            }
            admin_log('Saved document', 'documents', $id);
            redirect('admin/documents');
            break;

        case 'toggle':
            q('UPDATE documents SET is_public = IF(is_public = 1, 0, 1) WHERE id = ?', [$id]);
            redirect('admin/documents');
            break;

        case 'delete':
            db_delete('documents', $id);
            admin_log('Deleted document', 'documents', $id);
            flash('success', 'Document deleted.');
            redirect('admin/documents');
            break;
    }
}

$editId = (int) get('edit', 0);
$doc = $editId > 0 ? fetch_one('SELECT * FROM documents WHERE id = ?', [$editId]) : [
    'id' => 0, 'title' => '', 'description' => '', 'category' => 'Licences', 'doc_type' => 'image',
    'file_path' => '', 'issued_by' => '', 'reference_no' => '', 'issue_date' => '', 'expiry_date' => '',
    'is_public' => 1, 'sort_order' => 0,
];

$docs = fetch_all('SELECT * FROM documents ORDER BY category ASC, sort_order ASC');
$cats = ['Licences', 'Certificates', 'Test Reports', 'Sample Agreements', 'Quotations', 'Registrations', 'Other'];

admin_header('Documents', 'Licences, test reports and sample agreements shown on the public documents page');
?>
<div class="a-grid" style="grid-template-columns:minmax(0,1fr) minmax(300px,380px);gap:20px;align-items:start;">
  <div class="a-card">
    <div class="a-card-head"><h2>All documents (<?= count($docs) ?>)</h2></div>
    <?php if (!$docs): ?>
      <div class="empty">No documents yet.</div>
    <?php else: ?>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead><tr><th>File</th><th>Title</th><th>Category</th><th>Reference</th><th>Public</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($docs as $d): ?>
          <tr>
            <td>
              <?php if ($d['doc_type'] === 'pdf'): ?>
                <span class="pill pill-bad">PDF</span>
              <?php else: ?>
                <img class="thumb" src="<?= e(media_url($d['file_path'], 'doc')) ?>" alt="">
              <?php endif; ?>
            </td>
            <td><strong><?= e($d['title']) ?></strong><br><small style="color:var(--a-muted);"><?= e(excerpt($d['description'], 60)) ?></small></td>
            <td><small><?= e($d['category']) ?></small></td>
            <td><small><?= e((string) $d['reference_no']) ?></small></td>
            <td><?= toggle_button((int) $d['id'], 'is_public', (int) $d['is_public'] === 1, 'Public') ?></td>
            <td class="actions">
              <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('documents?edit=' . (int) $d['id'])) ?>">Edit</a>
              <?= delete_button((int) $d['id'], 'Delete this document?') ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <aside class="a-card">
    <div class="a-card-head"><h2><?= $editId ? 'Edit document' : 'Add a document' ?></h2></div>
    <form method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int) $doc['id'] ?>">

      <div class="form-group">
        <label for="file_path">File (image or PDF)</label>
        <input type="file" id="file_path" name="file_path" accept=".pdf,.jpg,.jpeg,.png,.webp">
        <input type="text" name="file_path_path" value="<?= e((string) $doc['file_path']) ?>" placeholder="or an existing path" style="margin-top:8px;">
        <span class="form-hint">Scanned licences work best as WebP or JPG. Reports and agreements as PDF.</span>
      </div>
      <div class="form-group">
        <label for="title">Title <span class="req">*</span></label>
        <input type="text" id="title" name="title" required value="<?= e((string) $doc['title']) ?>">
      </div>
      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" style="min-height:80px;"><?= e((string) $doc['description']) ?></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="category">Category</label>
          <input type="text" id="category" name="category" list="docCats" value="<?= e((string) $doc['category']) ?>">
          <datalist id="docCats"><?php foreach ($cats as $c): ?><option value="<?= e($c) ?>"><?php endforeach; ?></datalist>
        </div>
        <div class="form-group">
          <label for="sort_order">Order</label>
          <input type="number" id="sort_order" name="sort_order" value="<?= (int) $doc['sort_order'] ?>">
        </div>
        <div class="form-group">
          <label for="issued_by">Issued by</label>
          <input type="text" id="issued_by" name="issued_by" value="<?= e((string) $doc['issued_by']) ?>">
        </div>
        <div class="form-group">
          <label for="reference_no">Reference number</label>
          <input type="text" id="reference_no" name="reference_no" value="<?= e((string) $doc['reference_no']) ?>">
        </div>
        <div class="form-group">
          <label for="issue_date">Issue date</label>
          <input type="date" id="issue_date" name="issue_date" value="<?= e((string) $doc['issue_date']) ?>">
        </div>
        <div class="form-group">
          <label for="expiry_date">Valid until</label>
          <input type="date" id="expiry_date" name="expiry_date" value="<?= e((string) $doc['expiry_date']) ?>">
        </div>
      </div>
      <label class="check"><input type="checkbox" name="is_public" value="1" <?= (int) $doc['is_public'] === 1 ? 'checked' : '' ?>> Show on the public documents page</label>
      <button class="btn btn-primary btn-block" type="submit"><?= $editId ? 'Save changes' : 'Add document' ?></button>
      <?php if ($editId): ?><a class="btn btn-ghost btn-block" style="margin-top:8px;" href="<?= e(admin_url('documents')) ?>">Cancel</a><?php endif; ?>
    </form>
  </aside>
</div>
<?php admin_footer(); ?>
