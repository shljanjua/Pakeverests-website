<?php
/** FAQ manager. */
declare(strict_types=1);

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'save':
            $data = [
                'question'     => post('question'),
                'answer'       => post('answer'),
                'category'     => post('category') ?: 'General',
                'show_on_home' => post('show_on_home') ? 1 : 0,
                'sort_order'   => (int) post('sort_order', 0),
                'is_active'    => post('is_active') ? 1 : 0,
            ];
            if ($id > 0) {
                db_update('faqs', $data, $id);
                flash('success', 'FAQ updated.');
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $id = db_insert('faqs', $data);
                flash('success', 'FAQ added.');
            }
            admin_log('Saved FAQ', 'faqs', $id);
            redirect('admin/faqs');
            break;
        case 'toggle':
            $col = post('column');
            if (in_array($col, ['is_active', 'show_on_home'], true)) {
                q("UPDATE faqs SET `$col` = IF(`$col` = 1, 0, 1) WHERE id = ?", [$id]);
            }
            redirect('admin/faqs');
            break;
        case 'delete':
            db_delete('faqs', $id);
            admin_log('Deleted FAQ', 'faqs', $id);
            flash('success', 'FAQ deleted.');
            redirect('admin/faqs');
            break;
    }
}

$editId = (int) get('edit', 0);
$faq = $editId > 0 ? fetch_one('SELECT * FROM faqs WHERE id = ?', [$editId]) : [
    'id' => 0, 'question' => '', 'answer' => '', 'category' => 'General', 'show_on_home' => 0, 'sort_order' => 0, 'is_active' => 1,
];
$faqs = fetch_all('SELECT * FROM faqs ORDER BY sort_order ASC, id ASC');
$homeCount = (int) fetch_val('SELECT COUNT(*) FROM faqs WHERE show_on_home = 1 AND is_active = 1', [], 0);

admin_header('FAQs', 'Questions and answers, with automatic FAQ schema for Google');
?>
<div class="a-grid" style="grid-template-columns:minmax(0,1fr) minmax(300px,400px);gap:20px;align-items:start;">
  <div class="a-card">
    <div class="a-card-head"><h2>All questions (<?= count($faqs) ?>)</h2></div>
    <?php if ($homeCount !== 5): ?>
      <div class="alert alert-info">
        <?= $homeCount ?> question(s) are currently set to appear in the home page FAQ block. Five is the recommended number.
      </div>
    <?php endif; ?>
    <?php if (!$faqs): ?>
      <div class="empty">No FAQs yet.</div>
    <?php else: ?>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead><tr><th>Question</th><th>Category</th><th>Home</th><th>Active</th><th>Order</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($faqs as $f): ?>
          <tr>
            <td><strong><?= e($f['question']) ?></strong><br><small style="color:var(--a-muted);"><?= e(excerpt($f['answer'], 80)) ?></small></td>
            <td><small><?= e($f['category']) ?></small></td>
            <td><?= toggle_button((int) $f['id'], 'show_on_home', (int) $f['show_on_home'] === 1, 'Show on home') ?></td>
            <td><?= toggle_button((int) $f['id'], 'is_active', (int) $f['is_active'] === 1, 'Active') ?></td>
            <td><?= (int) $f['sort_order'] ?></td>
            <td class="actions">
              <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('faqs?edit=' . (int) $f['id'])) ?>">Edit</a>
              <?= delete_button((int) $f['id'], 'Delete this FAQ?') ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <aside class="a-card">
    <div class="a-card-head"><h2><?= $editId ? 'Edit question' : 'Add a question' ?></h2></div>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int) $faq['id'] ?>">
      <div class="form-group">
        <label for="question">Question <span class="req">*</span></label>
        <textarea id="question" name="question" required style="min-height:70px;"><?= e((string) $faq['question']) ?></textarea>
      </div>
      <div class="form-group">
        <label for="answer">Answer <span class="req">*</span></label>
        <textarea id="answer" name="answer" required class="code" style="min-height:180px;"><?= e((string) $faq['answer']) ?></textarea>
        <span class="form-hint">HTML allowed. Use &lt;p&gt;, &lt;strong&gt; and &lt;a href="/page"&gt; links.</span>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="category">Category</label>
          <input type="text" id="category" name="category" list="faqCats" value="<?= e((string) $faq['category']) ?>">
          <datalist id="faqCats">
            <option value="General"><option value="Pricing"><option value="Delivery"><option value="Quality">
            <option value="Products"><option value="Orders"><option value="Payment"><option value="Custom Labels">
            <option value="Distribution"><option value="Policies">
          </datalist>
        </div>
        <div class="form-group">
          <label for="sort_order">Order</label>
          <input type="number" id="sort_order" name="sort_order" value="<?= (int) $faq['sort_order'] ?>">
        </div>
      </div>
      <label class="check"><input type="checkbox" name="show_on_home" value="1" <?= (int) $faq['show_on_home'] === 1 ? 'checked' : '' ?>> Show in the home page FAQ block</label>
      <label class="check"><input type="checkbox" name="is_active" value="1" <?= (int) $faq['is_active'] === 1 ? 'checked' : '' ?>> Active</label>
      <button class="btn btn-primary btn-block" type="submit"><?= $editId ? 'Save changes' : 'Add question' ?></button>
      <?php if ($editId): ?><a class="btn btn-ghost btn-block" style="margin-top:8px;" href="<?= e(admin_url('faqs')) ?>">Cancel</a><?php endif; ?>
    </form>
  </aside>
</div>
<?php admin_footer(); ?>
