<?php
/** Realtime and historical visitor analytics. */
declare(strict_types=1);

$range = get('range', '30d');
[$rangeSql] = analytics_range_where($range);

$ranges = ['today' => 'Today', '7d' => 'Last 7 days', '30d' => 'Last 30 days', '90d' => 'Last 90 days', 'all' => 'All time'];

/* Purge old data on request. */
if (is_post() && post('action') === 'purge') {
    $days = max(30, (int) post('days', 180));
    q('DELETE FROM page_views WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)', [$days]);
    admin_log('Purged analytics older than ' . $days . ' days');
    flash('success', 'Old analytics data removed.');
    redirect('admin/analytics');
}

$agg = function (string $column, int $limit = 12) use ($rangeSql) {
    return fetch_all(
        "SELECT `$column` AS label, COUNT(*) AS c, COUNT(DISTINCT session_id) AS visitors
         FROM page_views WHERE $rangeSql AND `$column` <> '' AND `$column` IS NOT NULL
         GROUP BY `$column` ORDER BY c DESC LIMIT $limit"
    );
};

$totalViews    = (int) fetch_val("SELECT COUNT(*) FROM page_views WHERE $rangeSql", [], 0);
$totalVisitors = (int) fetch_val("SELECT COUNT(DISTINCT session_id) FROM page_views WHERE $rangeSql", [], 0);
$countries     = $agg('country');
$regions       = $agg('region');
$cities        = $agg('city');
$pages         = $agg('page_url', 15);
$browsers      = $agg('browser');
$systems       = $agg('os');
$devices       = $agg('device');
$sources       = $agg('source');

$byHour = fetch_all(
    "SELECT HOUR(created_at) AS h, COUNT(*) AS c FROM page_views WHERE $rangeSql GROUP BY h ORDER BY h ASC"
);
$hourMap = array_fill(0, 24, 0);
foreach ($byHour as $r) { $hourMap[(int) $r['h']] = (int) $r['c']; }
$maxHour = max(1, max($hourMap));

$byDay = fetch_all(
    "SELECT DATE(created_at) AS d, COUNT(*) AS c, COUNT(DISTINCT session_id) AS v
     FROM page_views WHERE $rangeSql GROUP BY d ORDER BY d DESC LIMIT 30"
);
$byDay   = array_reverse($byDay);
$maxDay  = max(1, (int) max(array_column($byDay ?: [['c' => 1]], 'c')));

$live = fetch_all(
    'SELECT * FROM page_views WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 MINUTE) ORDER BY created_at DESC LIMIT 25'
);
$liveCount = (int) fetch_val('SELECT COUNT(DISTINCT session_id) FROM page_views WHERE created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)', [], 0);

$renderBars = function (array $rows, string $emptyMsg) {
    if (!$rows) {
        echo '<div class="empty">' . e($emptyMsg) . '</div>';
        return;
    }
    $max = max(1, (int) $rows[0]['c']);
    echo '<div class="bar-list">';
    foreach ($rows as $row) {
        printf(
            '<div class="bar-row"><span class="label" title="%1$s">%1$s</span>'
            . '<span class="bar-track"><span class="bar-fill" style="width:%2$d%%"></span></span>'
            . '<span class="value">%3$s</span></div>',
            e((string) $row['label']),
            (int) round((int) $row['c'] / $max * 100),
            number_format((int) $row['c'])
        );
    }
    echo '</div>';
};

admin_header('Analytics', 'Traffic by region, page, browser and device');
?>

<div class="tabs">
  <?php foreach ($ranges as $key => $label): ?>
    <a class="tab<?= $range === $key ? ' is-active' : '' ?>" href="<?= e(admin_url('analytics?range=' . $key)) ?>"><?= e($label) ?></a>
  <?php endforeach; ?>
</div>

<div class="a-grid a-grid-4" style="margin-bottom:20px;">
  <div class="stat">
    <span class="stat-icon"><svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.7 7.6 1 12c1.7 4.4 6 7.5 11 7.5s9.3-3.1 11-7.5c-1.7-4.4-6-7.5-11-7.5zm0 12.5a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/></svg></span>
    <div><div class="stat-value"><?= number_format($totalViews) ?></div><div class="stat-label">Page views</div></div>
  </div>
  <div class="stat">
    <span class="stat-icon ok"><svg viewBox="0 0 24 24"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0 2c-4 0-9 2-9 5v3h18v-3c0-3-5-5-9-5z"/></svg></span>
    <div><div class="stat-value"><?= number_format($totalVisitors) ?></div><div class="stat-label">Unique visitors</div></div>
  </div>
  <div class="stat">
    <span class="stat-icon warn"><svg viewBox="0 0 24 24"><path d="M13 2 4.09 12.11a1 1 0 0 0 .76 1.64H11l-1 8.25 8.91-10.11a1 1 0 0 0-.76-1.64H13z"/></svg></span>
    <div><div class="stat-value"><?= $liveCount ?></div><div class="stat-label">Active in last 5 minutes</div></div>
  </div>
  <div class="stat">
    <span class="stat-icon"><svg viewBox="0 0 24 24"><path d="M5 9.2h3V19H5zM10.6 5h2.8v14h-2.8zm5.6 8H19v6h-2.8z"/></svg></span>
    <div>
      <div class="stat-value"><?= $totalVisitors > 0 ? number_format($totalViews / $totalVisitors, 1) : '0' ?></div>
      <div class="stat-label">Pages per visitor</div>
    </div>
  </div>
</div>

<div class="a-card">
  <div class="a-card-head"><h2>Traffic over time</h2></div>
  <?php if ($byDay): ?>
    <div class="spark" style="height:120px;">
      <?php foreach ($byDay as $row): ?>
        <span style="height:<?= max(4, (int) round((int) $row['c'] / $maxDay * 100)) ?>%"
              title="<?= e($row['d']) ?>: <?= (int) $row['c'] ?> views, <?= (int) $row['v'] ?> visitors"></span>
      <?php endforeach; ?>
    </div>
    <p class="form-hint" style="margin-top:10px;">Hover a bar to see the exact figures for that day.</p>
  <?php else: ?>
    <div class="empty">No traffic in this period.</div>
  <?php endif; ?>
</div>

<div class="a-card">
  <div class="a-card-head"><h2>Busiest hours of the day</h2></div>
  <div class="spark" style="height:90px;">
    <?php for ($h = 0; $h < 24; $h++): ?>
      <span style="height:<?= max(3, (int) round($hourMap[$h] / $maxHour * 100)) ?>%"
            title="<?= sprintf('%02d:00', $h) ?> — <?= number_format($hourMap[$h]) ?> views"></span>
    <?php endfor; ?>
  </div>
  <p class="form-hint" style="margin-top:10px;">Useful for timing WhatsApp broadcasts and social posts. Times are <?= e(PE_CONFIG['timezone']) ?>.</p>
</div>

<div class="a-grid a-grid-2">
  <div class="a-card">
    <div class="a-card-head"><h2>Countries</h2></div>
    <?php $renderBars($countries, 'No country data yet.'); ?>
  </div>
  <div class="a-card">
    <div class="a-card-head"><h2>Regions and provinces</h2></div>
    <?php $renderBars($regions, 'No region data yet. Enable IP lookup under Settings &rarr; Analytics.'); ?>
  </div>
  <div class="a-card">
    <div class="a-card-head"><h2>Cities</h2></div>
    <?php $renderBars($cities, 'No city data yet.'); ?>
  </div>
  <div class="a-card">
    <div class="a-card-head"><h2>Top pages</h2></div>
    <?php $renderBars($pages, 'No page data yet.'); ?>
  </div>
  <div class="a-card">
    <div class="a-card-head"><h2>Browsers</h2></div>
    <?php $renderBars($browsers, 'No browser data yet.'); ?>
  </div>
  <div class="a-card">
    <div class="a-card-head"><h2>Operating systems</h2></div>
    <?php $renderBars($systems, 'No system data yet.'); ?>
  </div>
  <div class="a-card">
    <div class="a-card-head"><h2>Device types</h2></div>
    <?php $renderBars($devices, 'No device data yet.'); ?>
  </div>
  <div class="a-card">
    <div class="a-card-head"><h2>Traffic sources</h2></div>
    <?php $renderBars($sources, 'No referrer data yet.'); ?>
  </div>
</div>

<div class="a-card">
  <div class="a-card-head">
    <h2>Live visitors, last 30 minutes</h2>
    <button class="btn btn-ghost btn-sm" type="button" onclick="location.reload()">Refresh</button>
  </div>
  <?php if ($live): ?>
  <div class="a-table-wrap">
    <table class="a-table">
      <thead><tr><th>Time</th><th>Page</th><th>Location</th><th>Device</th><th>Browser</th><th>Source</th></tr></thead>
      <tbody>
        <?php foreach ($live as $v): ?>
        <tr>
          <td><?= date('H:i:s', strtotime($v['created_at'])) ?></td>
          <td><?= e($v['page_url']) ?></td>
          <td><?= e(trim(($v['city'] ? $v['city'] . ', ' : '') . ($v['region'] ? $v['region'] . ', ' : '') . $v['country'], ', ')) ?: 'Unknown' ?></td>
          <td><?= e($v['device']) ?> / <?= e($v['os']) ?></td>
          <td><?= e($v['browser']) ?></td>
          <td><?= e($v['source']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <div class="empty">No visitors in the last 30 minutes.</div>
  <?php endif; ?>
</div>

<div class="a-card">
  <div class="a-card-head"><h2>Data housekeeping</h2></div>
  <p class="form-hint">Analytics rows accumulate over time. Removing old data keeps the database small and the admin panel fast.</p>
  <form method="post" data-confirm="Delete analytics data older than the selected period? This cannot be undone.">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="purge">
    <div class="filter-form">
      <div class="form-group">
        <label for="days">Keep the last</label>
        <select id="days" name="days">
          <option value="90">90 days</option>
          <option value="180" selected>180 days</option>
          <option value="365">365 days</option>
          <option value="730">730 days</option>
        </select>
      </div>
      <button class="btn btn-danger" type="submit">Delete older data</button>
    </div>
  </form>
</div>

<?php admin_footer(); ?>
