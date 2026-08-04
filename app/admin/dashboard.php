<?php
/** Admin dashboard: headline numbers, recent activity, quick links. */
declare(strict_types=1);

$safeVal = function (string $sql, array $p = []) {
    try { return fetch_val($sql, $p, 0); } catch (Throwable $e) { return 0; }
};
$safeAll = function (string $sql, array $p = []) {
    try { return fetch_all($sql, $p); } catch (Throwable $e) { return []; }
};

$stats = [
    'orders_today'   => (int) $safeVal('SELECT COUNT(*) FROM orders WHERE created_at >= CURDATE()'),
    'orders_month'   => (int) $safeVal('SELECT COUNT(*) FROM orders WHERE created_at >= DATE_FORMAT(NOW(), "%Y-%m-01")'),
    'orders_new'     => (int) $safeVal('SELECT COUNT(*) FROM orders WHERE status = "new"'),
    'revenue_month'  => (float) $safeVal('SELECT COALESCE(SUM(total),0) FROM orders WHERE status <> "cancelled" AND created_at >= DATE_FORMAT(NOW(), "%Y-%m-01")'),
    'views_today'    => (int) $safeVal('SELECT COUNT(*) FROM page_views WHERE created_at >= CURDATE()'),
    'visitors_today' => (int) $safeVal('SELECT COUNT(DISTINCT session_id) FROM page_views WHERE created_at >= CURDATE()'),
    'views_month'    => (int) $safeVal('SELECT COUNT(*) FROM page_views WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)'),
    'messages_new'   => (int) $safeVal('SELECT COUNT(*) FROM contact_messages WHERE status = "new"'),
    'dist_new'       => (int) $safeVal('SELECT COUNT(*) FROM distributor_applications WHERE status = "new"'),
    'labels_new'     => (int) $safeVal('SELECT COUNT(*) FROM label_requests WHERE status = "new"'),
    'reviews_pending'=> (int) $safeVal('SELECT COUNT(*) FROM reviews WHERE status = "pending"'),
    'subscribers'    => (int) $safeVal('SELECT COUNT(*) FROM subscribers WHERE status = "active"'),
    'products'       => (int) $safeVal('SELECT COUNT(*) FROM products WHERE status = "published"'),
    'posts'          => (int) $safeVal('SELECT COUNT(*) FROM blog_posts WHERE status = "published"'),
];

$viewsByDay = $safeAll(
    'SELECT DATE(created_at) AS d, COUNT(*) AS c FROM page_views
     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 13 DAY) GROUP BY d ORDER BY d ASC'
);
$maxViews = max(1, (int) max(array_column($viewsByDay ?: [['c' => 1]], 'c')));

$ordersByDay = $safeAll(
    'SELECT DATE(created_at) AS d, COUNT(*) AS c, COALESCE(SUM(total),0) AS revenue FROM orders
     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 13 DAY) GROUP BY d ORDER BY d ASC'
);

$recentOrders   = $safeAll('SELECT * FROM orders ORDER BY created_at DESC LIMIT 8');
$recentMessages = $safeAll('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 6');
$topPages       = $safeAll('SELECT page_url, COUNT(*) AS c FROM page_views WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY page_url ORDER BY c DESC LIMIT 8');
$topProducts    = $safeAll('SELECT product_name, SUM(quantity) AS qty, SUM(line_total) AS revenue FROM order_items GROUP BY product_name ORDER BY qty DESC LIMIT 6');
$activity       = $safeAll('SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 8');
$maxPage        = max(1, (int) ($topPages[0]['c'] ?? 1));
$maxProduct     = max(1, (int) ($topProducts[0]['qty'] ?? 1));

/* Setup warnings that help the owner finish configuration. */
$warnings = [];
if (PE_CONFIG['db_pass'] === '') {
    $warnings[] = 'The database password in app/config.local.php is empty. If the site is working this is fine, but confirm the file exists on the server.';
}
if (!setting('google_site_verification')) {
    $warnings[] = 'Google Search Console verification code has not been added yet. Add it under Settings &rarr; SEO.';
}
if (!setting_bool('smtp_enabled')) {
    $warnings[] = 'SMTP is disabled, so notification emails fall back to the PHP mail() function. Configure it under Settings &rarr; SMTP for reliable delivery.';
}
if (!fetch_val('SELECT COUNT(*) FROM gallery WHERE image_path <> ""', [], 0)) {
    $warnings[] = 'No gallery photographs have been uploaded yet. Add them under Photo Gallery.';
}
if (!fetch_val('SELECT COUNT(*) FROM products WHERE main_image <> ""', [], 0)) {
    $warnings[] = 'No product photographs have been uploaded yet. Add them under Products.';
}

admin_header('Dashboard', 'Live overview of orders, traffic and enquiries');
?>

<?php if ($warnings): ?>
<div class="alert alert-warning">
  <strong>Setup checklist</strong>
  <ul><?php foreach ($warnings as $w): ?><li><?= $w ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="a-grid a-grid-4" style="margin-bottom:20px;">
  <div class="stat">
    <span class="stat-icon"><svg viewBox="0 0 24 24"><path d="M7 18a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm10 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7.2 14h9.5c.8 0 1.4-.5 1.6-1.2l2.6-8H6.2l-.5-2H2v2h2.4l3.6 9.4-1.3 2.4c-.5 1 .2 2.4 1.4 2.4H20v-2H8.5l.7-1.4z"/></svg></span>
    <div>
      <div class="stat-value"><?= $stats['orders_today'] ?></div>
      <div class="stat-label">Orders today</div>
      <div class="stat-sub"><?= $stats['orders_month'] ?> this month</div>
    </div>
  </div>
  <div class="stat">
    <span class="stat-icon ok"><svg viewBox="0 0 24 24"><path d="M11 17h2v-1h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2h-4V7h6V5h-3V4h-2v1H8a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h4v2H6v2h5v1z"/></svg></span>
    <div>
      <div class="stat-value"><?= money($stats['revenue_month']) ?></div>
      <div class="stat-label">Order value this month</div>
      <div class="stat-sub">Includes refundable deposits</div>
    </div>
  </div>
  <div class="stat">
    <span class="stat-icon"><svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.7 7.6 1 12c1.7 4.4 6 7.5 11 7.5s9.3-3.1 11-7.5c-1.7-4.4-6-7.5-11-7.5zm0 12.5a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg></span>
    <div>
      <div class="stat-value"><?= number_format($stats['views_today']) ?></div>
      <div class="stat-label">Page views today</div>
      <div class="stat-sub"><?= number_format($stats['visitors_today']) ?> unique visitors</div>
    </div>
  </div>
  <div class="stat">
    <span class="stat-icon <?= $stats['orders_new'] + $stats['messages_new'] > 0 ? 'warn' : '' ?>">
      <svg viewBox="0 0 24 24"><path d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2zm6-6v-5a6 6 0 0 0-5-5.9V4a1 1 0 1 0-2 0v1.1A6 6 0 0 0 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
    </span>
    <div>
      <div class="stat-value"><?= $stats['orders_new'] + $stats['messages_new'] + $stats['dist_new'] + $stats['labels_new'] + $stats['reviews_pending'] ?></div>
      <div class="stat-label">Items needing attention</div>
      <div class="stat-sub"><?= $stats['orders_new'] ?> orders, <?= $stats['messages_new'] ?> messages, <?= $stats['reviews_pending'] ?> reviews</div>
    </div>
  </div>
</div>

<div class="a-grid a-grid-2">
  <div class="a-card">
    <div class="a-card-head">
      <h2>Page views, last 14 days</h2>
      <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('analytics')) ?>">Full analytics</a>
    </div>
    <?php if ($viewsByDay): ?>
      <div class="spark">
        <?php foreach ($viewsByDay as $row): ?>
          <span style="height:<?= max(4, (int) round((int) $row['c'] / $maxViews * 100)) ?>%"
                title="<?= e($row['d']) ?>: <?= (int) $row['c'] ?> views"></span>
        <?php endforeach; ?>
      </div>
      <p class="form-hint" style="margin-top:10px;">
        <?= number_format($stats['views_month']) ?> views in the last 30 days. Peak day in this period:
        <?= number_format($maxViews) ?> views.
      </p>
    <?php else: ?>
      <div class="empty">No traffic recorded yet. Data appears as soon as visitors browse the website.</div>
    <?php endif; ?>
  </div>

  <div class="a-card">
    <div class="a-card-head"><h2>Most visited pages (30 days)</h2></div>
    <?php if ($topPages): ?>
      <div class="bar-list">
        <?php foreach ($topPages as $row): ?>
        <div class="bar-row">
          <span class="label" title="<?= e($row['page_url']) ?>"><?= e($row['page_url']) ?></span>
          <span class="bar-track"><span class="bar-fill" style="width:<?= round((int) $row['c'] / $maxPage * 100) ?>%"></span></span>
          <span class="value"><?= number_format((int) $row['c']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty">No page data yet.</div>
    <?php endif; ?>
  </div>
</div>

<div class="a-card">
  <div class="a-card-head">
    <h2>Recent orders</h2>
    <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('orders')) ?>">All orders</a>
  </div>
  <?php if ($recentOrders): ?>
  <div class="a-table-wrap">
    <table class="a-table">
      <thead><tr><th>Reference</th><th>Customer</th><th>Area</th><th>Total</th><th>Status</th><th>Received</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($recentOrders as $o): ?>
        <tr>
          <td><strong><?= e($o['order_ref']) ?></strong></td>
          <td><?= e($o['customer_name']) ?><br><small style="color:var(--a-muted);"><?= e($o['phone']) ?></small></td>
          <td><?= e($o['area']) ?></td>
          <td><strong><?= money($o['total']) ?></strong></td>
          <td><?= status_pill($o['status']) ?></td>
          <td><?= time_ago($o['created_at']) ?></td>
          <td class="actions"><a class="btn btn-ghost btn-sm" href="<?= e(admin_url('orders?view=' . (int) $o['id'])) ?>">Open</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <div class="empty">No orders received yet. Orders placed on the website appear here immediately.</div>
  <?php endif; ?>
</div>

<div class="a-grid a-grid-2">
  <div class="a-card">
    <div class="a-card-head">
      <h2>Best selling products</h2>
    </div>
    <?php if ($topProducts): ?>
      <div class="bar-list">
        <?php foreach ($topProducts as $row): ?>
        <div class="bar-row">
          <span class="label" title="<?= e($row['product_name']) ?>"><?= e($row['product_name']) ?></span>
          <span class="bar-track"><span class="bar-fill" style="width:<?= round((int) $row['qty'] / $maxProduct * 100) ?>%"></span></span>
          <span class="value"><?= number_format((int) $row['qty']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty">No product sales recorded yet.</div>
    <?php endif; ?>
  </div>

  <div class="a-card">
    <div class="a-card-head">
      <h2>Latest messages</h2>
      <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('messages')) ?>">All messages</a>
    </div>
    <?php if ($recentMessages): ?>
      <div class="detail-list">
        <?php foreach ($recentMessages as $m): ?>
        <div class="detail-row">
          <dt><?= e($m['name']) ?><br><small><?= time_ago($m['created_at']) ?></small></dt>
          <dd>
            <?= e(excerpt($m['message'], 110)) ?><br>
            <?= status_pill($m['status']) ?>
            <a href="<?= e(admin_url('messages?view=' . (int) $m['id'])) ?>">Open</a>
          </dd>
        </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty">No messages yet.</div>
    <?php endif; ?>
  </div>
</div>

<div class="a-grid a-grid-2">
  <div class="a-card">
    <div class="a-card-head"><h2>Quick actions</h2></div>
    <div class="a-grid a-grid-3">
      <a class="btn btn-ghost" href="<?= e(admin_url('blog?edit=new')) ?>">Write a blog post</a>
      <a class="btn btn-ghost" href="<?= e(admin_url('products')) ?>">Manage products</a>
      <a class="btn btn-ghost" href="<?= e(admin_url('gallery')) ?>">Upload photos</a>
      <a class="btn btn-ghost" href="<?= e(admin_url('ticker')) ?>">Edit news ticker</a>
      <a class="btn btn-ghost" href="<?= e(admin_url('quotations?edit=new')) ?>">Create a quotation</a>
      <a class="btn btn-ghost" href="<?= e(admin_url('agreements?edit=new')) ?>">Generate an agreement</a>
      <a class="btn btn-ghost" href="<?= e(admin_url('pages')) ?>">Edit legal pages</a>
      <a class="btn btn-ghost" href="<?= e(admin_url('settings')) ?>">Website settings</a>
      <a class="btn btn-ghost" href="<?= e(admin_url('ads')) ?>">Ad codes</a>
    </div>
  </div>

  <div class="a-card">
    <div class="a-card-head"><h2>Recent admin activity</h2></div>
    <?php if ($activity): ?>
      <div class="detail-list">
        <?php foreach ($activity as $a): ?>
        <div class="detail-row">
          <dt><?= time_ago($a['created_at']) ?></dt>
          <dd><strong><?= e((string) $a['user_name']) ?></strong> — <?= e($a['action']) ?>
            <?php if ($a['entity']): ?><small style="color:var(--a-muted);">(<?= e($a['entity']) ?><?= $a['entity_id'] ? ' #' . (int) $a['entity_id'] : '' ?>)</small><?php endif; ?>
          </dd>
        </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty">No activity logged yet.</div>
    <?php endif; ?>
  </div>
</div>

<?php admin_footer(); ?>
