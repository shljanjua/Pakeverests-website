<?php
/** Reviews and star ratings: approve, reply, edit, add. */
declare(strict_types=1);

$statuses = ['pending', 'approved', 'rejected'];
$editId   = get('edit');

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'save':
            $data = [
                'product_id'    => (int) post('product_id') ?: null,
                'reviewer_name' => post('reviewer_name'),
                'reviewer_role' => post('reviewer_role'),
                'location'      => post('location'),
                'rating'        => max(1, min(5, (int) post('rating', 5))),
                'title'         => post('title'),
                'body'          => post('body'),
                'is_featured'   => post('is_featured') ? 1 : 0,
                'is_verified'   => post('is_verified') ? 1 : 0,
                'status'        => in_array(post('status'), $statuses, true) ? post('status') : 'pending',
                'admin_reply'   => post('admin_reply'),
            ];
            if ($id > 0) {
                db_update('reviews', $data, $id);
                admin_log('Updated review', 'reviews', $id);
                flash('success', 'Review saved.');
            } else {
                $data['created_at'] = post('created_at') ? date('Y-m-d H:i:s', strtotime(post('created_at'))) : date('Y-m-d H:i:s');
                $id = db_insert('reviews', $data);
                admin_log('Added review', 'reviews', $id);
                flash('success', 'Review added.');
            }
            recalculate_product_ratings();
            redirect('admin/reviews');
            break;

        case 'quick_status':
            if ($id > 0 && in_array(post('status'), $statuses, true)) {
                db_update('reviews', ['status' => post('status')], $id);
                admin_log('Set review status to ' . post('status'), 'reviews', $id);
                recalculate_product_ratings();
                flash('success', 'Review status updated.');
            }
            redirect('admin/reviews' . (get('status') ? '?status=' . rawurlencode(get('status')) : ''));
            break;

        case 'toggle':
            $col = post('column');
            if ($id > 0 && in_array($col, ['is_featured', 'is_verified'], true)) {
                q("UPDATE reviews SET `$col` = IF(`$col` = 1, 0, 1) WHERE id = ?", [$id]);
            }
            redirect('admin/reviews');
            break;

        case 'delete':
            db_delete('reviews', $id);
            admin_log('Deleted review', 'reviews', $id);
            recalculate_product_ratings();
            flash('success', 'Review deleted.');
            redirect('admin/reviews');
            break;
    }
}

/** Keep the star rating shown on each product page in sync with approved reviews. */
function recalculate_product_ratings(): void
{
    $rows = fetch_all('SELECT product_id, AVG(rating) AS avg_rating, COUNT(*) AS total
                       FROM reviews WHERE status = "approved" AND product_id IS NOT NULL GROUP BY product_id');
    foreach ($rows as $r) {
        q('UPDATE products SET rating = ?, rating_count = ? WHERE id = ?',
          [round((float) $r['avg_rating'], 2), (int) $r['total'], (int) $r['product_id']]);
    }
}

$products = fetch_all('SELECT id, name FROM products ORDER BY sort_order ASC');

/* ---- Editor -------------------------------------------------------------- */
if ($editId !== '') {
    $isNew  = $editId === 'new';
    $review = $isNew ? [
        'id' => 0, 'product_id' => 0, 'reviewer_name' => '', 'reviewer_role' => 'Home Customer',
        'location' => '', 'rating' => 5, 'title' => '', 'body' => '', 'is_featured' => 0,
        'is_verified' => 1, 'status' => 'approved', 'admin_reply' => '', 'created_at' => date('Y-m-d H:i:s'),
    ] : fetch_one('SELECT * FROM reviews WHERE id = ?', [(int) $editId]);

    if (!$review) { flash('error', 'Review not found.'); redirect('admin/reviews'); }

    admin_header($isNew ? 'Add a review' : 'Edit review', 'Reviews power the star ratings and review schema on product pages', [
        ['label' => '← All reviews', 'href' => admin_url('reviews'), 'class' => 'btn-ghost'],
    ]);
    ?>
    <form method="post" class="a-card" data-dirty-guard>
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int) $review['id'] ?>">

      <div class="form-row">
        <div class="form-group">
          <label for="reviewer_name">Reviewer name <span class="req">*</span></label>
          <input type="text" id="reviewer_name" name="reviewer_name" required value="<?= e((string) $review['reviewer_name']) ?>">
        </div>
        <div class="form-group">
          <label for="reviewer_role">Reviewer type</label>
          <input type="text" id="reviewer_role" name="reviewer_role" value="<?= e((string) $review['reviewer_role']) ?>" placeholder="Home Customer, Office, School…">
        </div>
        <div class="form-group">
          <label for="location">Location</label>
          <input type="text" id="location" name="location" value="<?= e((string) $review['location']) ?>" placeholder="Gujar Khan">
        </div>
        <div class="form-group">
          <label for="rating">Star rating</label>
          <select id="rating" name="rating">
            <?php foreach ([5, 4, 3, 2, 1] as $r): ?>
            <option value="<?= $r ?>" <?= (int) $review['rating'] === $r ? 'selected' : '' ?>><?= $r ?> star<?= $r > 1 ? 's' : '' ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="product_id">Product</label>
          <select id="product_id" name="product_id">
            <option value="0">General service review</option>
            <?php foreach ($products as $p): ?>
            <option value="<?= (int) $p['id'] ?>" <?= (int) $review['product_id'] === (int) $p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="status">Status</label>
          <select id="status" name="status">
            <?php foreach ($statuses as $s): ?>
            <option value="<?= e($s) ?>" <?= $review['status'] === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="title">Review title</label>
        <input type="text" id="title" name="title" value="<?= e((string) $review['title']) ?>">
      </div>
      <div class="form-group">
        <label for="body">Review text <span class="req">*</span></label>
        <textarea id="body" name="body" required><?= e((string) $review['body']) ?></textarea>
      </div>
      <div class="form-group">
        <label for="admin_reply">Public reply from Pak-Everests</label>
        <textarea id="admin_reply" name="admin_reply" placeholder="Optional. Shown below the review on the website."><?= e((string) $review['admin_reply']) ?></textarea>
      </div>

      <?php if ($isNew): ?>
      <div class="form-group">
        <label for="created_at">Review date</label>
        <input type="datetime-local" id="created_at" name="created_at" value="<?= date('Y-m-d\TH:i') ?>">
      </div>
      <?php endif; ?>

      <label class="check"><input type="checkbox" name="is_featured" value="1" <?= (int) $review['is_featured'] === 1 ? 'checked' : '' ?>> Feature on the home page</label>
      <label class="check"><input type="checkbox" name="is_verified" value="1" <?= (int) $review['is_verified'] === 1 ? 'checked' : '' ?>> Mark as a verified customer</label>

      <button class="btn btn-primary" type="submit">Save review</button>
      <a class="btn btn-ghost" href="<?= e(admin_url('reviews')) ?>">Cancel</a>
    </form>
    <?php
    admin_footer();
    return;
}

/* ---- List ---------------------------------------------------------------- */
$filterStatus = get('status');
$where = ['1=1']; $params = [];
if ($filterStatus !== '' && in_array($filterStatus, $statuses, true)) { $where[] = 'status = ?'; $params[] = $filterStatus; }
$list = admin_list('reviews', ['where' => implode(' AND ', $where), 'params' => $params, 'order' => 'created_at DESC', 'perPage' => 30]);

$avg = (float) fetch_val('SELECT AVG(rating) FROM reviews WHERE status = "approved"', [], 0);

admin_header('Reviews and Ratings', 'Approve, reply to and manage customer reviews', [
    ['label' => '+ Add review', 'href' => admin_url('reviews?edit=new')],
]);
?>
<div class="a-grid a-grid-4" style="margin-bottom:20px;">
  <div class="stat"><span class="stat-icon ok"><svg viewBox="0 0 24 24"><path d="M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/></svg></span>
    <div><div class="stat-value"><?= number_format($avg, 1) ?></div><div class="stat-label">Average rating</div></div></div>
  <div class="stat"><span class="stat-icon"><svg viewBox="0 0 24 24"><path d="M20 2H4a2 2 0 0 0-2 2v18l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/></svg></span>
    <div><div class="stat-value"><?= (int) fetch_val('SELECT COUNT(*) FROM reviews WHERE status = "approved"', [], 0) ?></div><div class="stat-label">Published</div></div></div>
  <div class="stat"><span class="stat-icon warn"><svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 15h-2v-2h2zm0-4h-2V7h2z"/></svg></span>
    <div><div class="stat-value"><?= (int) fetch_val('SELECT COUNT(*) FROM reviews WHERE status = "pending"', [], 0) ?></div><div class="stat-label">Awaiting approval</div></div></div>
  <div class="stat"><span class="stat-icon"><svg viewBox="0 0 24 24"><path d="M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/></svg></span>
    <div><div class="stat-value"><?= (int) fetch_val('SELECT COUNT(*) FROM reviews WHERE is_featured = 1 AND status = "approved"', [], 0) ?></div><div class="stat-label">Featured on home</div></div></div>
</div>

<div class="a-card">
  <div class="tabs">
    <a class="tab<?= $filterStatus === '' ? ' is-active' : '' ?>" href="<?= e(admin_url('reviews')) ?>">All</a>
    <?php foreach ($statuses as $s): ?>
    <a class="tab<?= $filterStatus === $s ? ' is-active' : '' ?>" href="<?= e(admin_url('reviews?status=' . $s)) ?>"><?= e(ucfirst($s)) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (!$list['rows']): ?>
    <div class="empty">No reviews found.</div>
  <?php else: ?>
  <div class="a-table-wrap">
    <table class="a-table">
      <thead><tr><th>Reviewer</th><th>Rating</th><th>Review</th><th>Product</th><th>Featured</th><th>Verified</th><th>Status</th><th>Date</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($list['rows'] as $r):
          $prodName = $r['product_id'] ? fetch_val('SELECT short_name FROM products WHERE id = ?', [(int) $r['product_id']], '') : ''; ?>
        <tr>
          <td><strong><?= e($r['reviewer_name']) ?></strong><br><small style="color:var(--a-muted);"><?= e((string) $r['location']) ?></small></td>
          <td><?= str_repeat('★', (int) $r['rating']) ?><span style="color:var(--a-border);"><?= str_repeat('★', 5 - (int) $r['rating']) ?></span></td>
          <td><small><?= e(excerpt($r['body'], 80)) ?></small></td>
          <td><small><?= e((string) $prodName) ?: 'General' ?></small></td>
          <td><?= toggle_button((int) $r['id'], 'is_featured', (int) $r['is_featured'] === 1, 'Featured') ?></td>
          <td><?= toggle_button((int) $r['id'], 'is_verified', (int) $r['is_verified'] === 1, 'Verified') ?></td>
          <td><?= status_pill($r['status']) ?></td>
          <td><small><?= pretty_date($r['created_at']) ?></small></td>
          <td class="actions">
            <?php if ($r['status'] !== 'approved'): ?>
            <form method="post" class="inline-form">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="quick_status">
              <input type="hidden" name="status" value="approved">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <button class="btn btn-ok btn-sm" type="submit">Approve</button>
            </form>
            <?php endif; ?>
            <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('reviews?edit=' . (int) $r['id'])) ?>">Edit</a>
            <?= delete_button((int) $r['id'], 'Delete this review?') ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?= pagination_links($list['pagination'], admin_url('reviews?status=' . rawurlencode($filterStatus))) ?>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
