<?php
/** Reviews listing plus submission form. */
declare(strict_types=1);

$result = [];
if (is_post() && post('form_type') === 'review') {
    $result = handle_review_form();
}

$page    = max(1, (int) get('page', 1));
$perPage = 12;
$filterProduct = (int) get('product', 0);

$where  = ['status = "approved"'];
$params = [];
if ($filterProduct > 0) {
    $where[]  = 'product_id = ?';
    $params[] = $filterProduct;
}
$whereSql = implode(' AND ', $where);

$total   = (int) fetch_val("SELECT COUNT(*) FROM reviews WHERE $whereSql", $params, 0);
$p       = paginate($total, $perPage, $page);
$reviews = fetch_all("SELECT * FROM reviews WHERE $whereSql ORDER BY is_featured DESC, created_at DESC LIMIT $perPage OFFSET {$p['offset']}", $params);

$avg = (float) fetch_val('SELECT AVG(rating) FROM reviews WHERE status = "approved"', [], 5);
$breakdown = [];
foreach ([5, 4, 3, 2, 1] as $starVal) {
    $breakdown[$starVal] = (int) fetch_val('SELECT COUNT(*) FROM reviews WHERE status = "approved" AND rating = ?', [$starVal], 0);
}
$grandTotal = array_sum($breakdown) ?: 1;
$products = fetch_all('SELECT id, name FROM products WHERE status = "published" ORDER BY sort_order ASC');

seo_set([
    'title'       => 'Customer Reviews &amp; Ratings',
    'description' => 'Verified customer reviews for Pak-Everests mineral water from homes, offices, schools, clinics and shops across Gujar Khan, Rawalpindi and Islamabad.',
    'keywords'    => 'Pak-Everests reviews, water supplier reviews Rawalpindi, mineral water ratings Pakistan',
    'breadcrumbs' => ['Reviews' => '/reviews'],
]);

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Customer Reviews and Ratings';
$heroSubtitle = 'Unedited feedback from customers across our delivery area. Every review is moderated for spam only, never for tone.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container">
    <div class="rating-summary reveal" style="margin-bottom:38px;">
      <div class="rating-big">
        <strong><?= number_format($avg, 1) ?></strong>
        <?= stars($avg) ?>
        <p class="form-hint" style="margin-top:6px;"><?= (int) $total ?> reviews</p>
      </div>
      <div class="rating-bars">
        <?php foreach ($breakdown as $starVal => $count):
          $pct = round($count / $grandTotal * 100); ?>
        <div class="rating-bar">
          <span><?= $starVal ?> star</span>
          <span class="rating-bar-track"><span class="rating-bar-fill" style="width:<?= $pct ?>%;"></span></span>
          <span><?= $count ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <?php if (!$reviews): ?>
      <div class="empty-state"><p>No reviews published yet. Be the first to leave one below.</p></div>
    <?php else: ?>
      <div class="grid grid-3">
        <?php foreach ($reviews as $r): ?>
        <article class="card review-card reveal">
          <div class="review-head">
            <div class="review-avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($r['reviewer_name'], 0, 1))) ?></div>
            <div>
              <div class="review-name"><?= e($r['reviewer_name']) ?></div>
              <div class="review-role"><?= e($r['reviewer_role']) ?><?= $r['location'] ? ' &middot; ' . e($r['location']) : '' ?></div>
            </div>
          </div>
          <?= stars((float) $r['rating']) ?>
          <?php if ($r['title']): ?><h2 style="font-size:1rem;margin:0;"><?= e($r['title']) ?></h2><?php endif; ?>
          <p class="review-body"><?= e($r['body']) ?></p>
          <?php if ($r['admin_reply']): ?>
            <div style="background:var(--surface-2);border-left:3px solid var(--brand);padding:10px 14px;border-radius:0 var(--r-sm) var(--r-sm) 0;font-size:.88rem;">
              <strong>Pak-Everests reply:</strong> <?= e($r['admin_reply']) ?>
            </div>
          <?php endif; ?>
          <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
            <?php if ((int) $r['is_verified'] === 1): ?>
            <span class="review-verified">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg> Verified
            </span>
            <?php else: ?><span></span><?php endif; ?>
            <small style="color:var(--text-muted);"><?= pretty_date($r['created_at']) ?></small>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <?= pagination_links($p, url('reviews')) ?>
    <?php endif; ?>
  </div>
</section>

<section class="section section-soft" id="write">
  <div class="container container-narrow">
    <div class="section-head">
      <span class="eyebrow">Your Turn</span>
      <h2>Write a Review</h2>
      <p>Reviews are published after a quick moderation check for spam. We do not edit the substance of genuine feedback.</p>
    </div>

    <?php if (!empty($result['success'])): ?>
      <div class="success-panel">
        <div class="success-icon">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg>
        </div>
        <h3>Thank you for your review</h3>
        <p>It has been submitted for moderation and will appear on this page shortly.</p>
        <a class="btn btn-outline" href="<?= e(url('reviews')) ?>">Back to Reviews</a>
      </div>
    <?php elseif (!setting_bool('reviews_open', true)): ?>
      <div class="alert alert-info">Review submissions are temporarily closed. Please send your feedback to <?= e(contact_email()) ?>.</div>
    <?php else: ?>
      <?php require PE_ROOT . '/app/partials/form-errors.php'; ?>
      <form class="form-card" method="post" action="<?= e(url('reviews')) ?>#write" data-guard="true">
        <?= csrf_field() ?>
        <input type="hidden" name="form_type" value="review">
        <div class="hp-field" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

        <div class="form-group" id="ratingInput">
          <label>Your rating <span class="req">*</span></label>
          <div class="radio-cards" style="grid-template-columns:repeat(5,1fr);">
            <?php foreach ([1, 2, 3, 4, 5] as $starVal): ?>
            <label class="radio-card">
              <input type="radio" name="rating" value="<?= $starVal ?>" <?= (int) post('rating', 5) === $starVal ? 'checked' : '' ?> required>
              <span><?= $starVal ?> &#9733;</span>
            </label>
            <?php endforeach; ?>
          </div>
          <span class="form-hint" id="ratingLabel"></span>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label for="reviewer_name">Your name <span class="req">*</span></label>
            <input type="text" id="reviewer_name" name="reviewer_name" required value="<?= e(post('reviewer_name')) ?>">
          </div>
          <div class="form-group">
            <label for="location">Your area</label>
            <input type="text" id="location" name="location" value="<?= e(post('location')) ?>" placeholder="e.g. Gujar Khan">
          </div>
          <div class="form-group">
            <label for="reviewer_role">You are a</label>
            <select id="reviewer_role" name="reviewer_role">
              <?php foreach (['Home Customer', 'Office / Corporate', 'School / College', 'Hospital / Clinic', 'Shop Owner', 'Distributor', 'Event Customer', 'Other'] as $role): ?>
              <option value="<?= e($role) ?>" <?= post('reviewer_role') === $role ? 'selected' : '' ?>><?= e($role) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="product_id">Which product</label>
            <select id="product_id" name="product_id">
              <option value="0">General service review</option>
              <?php foreach ($products as $pr): ?>
              <option value="<?= (int) $pr['id'] ?>" <?= (int) post('product_id', $filterProduct) === (int) $pr['id'] ? 'selected' : '' ?>><?= e($pr['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="reviewer_email">Email (not published)</label>
            <input type="email" id="reviewer_email" name="reviewer_email" value="<?= e(post('reviewer_email')) ?>">
          </div>
          <div class="form-group">
            <label for="title">Review title</label>
            <input type="text" id="title" name="title" value="<?= e(post('title')) ?>" placeholder="Sum it up in a few words">
          </div>
        </div>

        <div class="form-group">
          <label for="body">Your review <span class="req">*</span></label>
          <textarea id="body" name="body" required placeholder="What has your experience been? Delivery, taste, staff, pricing, anything that would help another customer decide."><?= e(post('body')) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">Submit Review</button>
      </form>
    <?php endif; ?>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
