<?php
/** News ticker: items plus full styling control. */
declare(strict_types=1);

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'save_item':
            $data = [
                'text'       => post('text'),
                'link'       => post('link'),
                'highlight'  => post('highlight') ? 1 : 0,
                'is_active'  => post('is_active') ? 1 : 0,
                'starts_at'  => post('starts_at') ? date('Y-m-d H:i:s', strtotime(post('starts_at'))) : null,
                'ends_at'    => post('ends_at') ? date('Y-m-d H:i:s', strtotime(post('ends_at'))) : null,
                'sort_order' => (int) post('sort_order', 0),
            ];
            if ($id > 0) { db_update('news_ticker', $data, $id); flash('success', 'Ticker item updated.'); }
            else { $data['created_at'] = date('Y-m-d H:i:s'); $id = db_insert('news_ticker', $data); flash('success', 'Ticker item added.'); }
            admin_log('Saved ticker item', 'news_ticker', $id);
            redirect('admin/ticker');
            break;

        case 'save_style':
            settings_save([
                'ticker_enabled'         => post('ticker_enabled') ? '1' : '0',
                'ticker_label'           => post('ticker_label'),
                'ticker_speed'           => (int) post('ticker_speed', 45),
                'ticker_font_size'       => (int) post('ticker_font_size', 14),
                'ticker_font_weight'     => (int) post('ticker_font_weight', 600),
                'ticker_font_family'     => post('ticker_font_family'),
                'ticker_bg'              => post('ticker_bg'),
                'ticker_color'           => post('ticker_color'),
                'ticker_highlight_color' => post('ticker_highlight_color'),
                'ticker_pause_on_hover'  => post('ticker_pause_on_hover') ? '1' : '0',
            ]);
            admin_log('Updated ticker styling');
            flash('success', 'Ticker appearance saved.');
            redirect('admin/ticker');
            break;

        case 'toggle':
            q('UPDATE news_ticker SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?', [$id]);
            redirect('admin/ticker');
            break;

        case 'delete':
            db_delete('news_ticker', $id);
            flash('success', 'Ticker item deleted.');
            redirect('admin/ticker');
            break;
    }
}

$editId = (int) get('edit', 0);
$item = $editId > 0 ? fetch_one('SELECT * FROM news_ticker WHERE id = ?', [$editId]) : [
    'id' => 0, 'text' => '', 'link' => '', 'highlight' => 0, 'is_active' => 1,
    'starts_at' => '', 'ends_at' => '', 'sort_order' => 0,
];
$items = fetch_all('SELECT * FROM news_ticker ORDER BY sort_order ASC, id DESC');

admin_header('News Ticker', 'The scrolling announcement bar under the header');
?>

<div class="a-card">
  <div class="a-card-head"><h2>Live preview</h2></div>
  <div id="tickerPreview" class="news-ticker"
       style="--ticker-bg:<?= e((string) setting('ticker_bg', '#0b6fa4')) ?>;--ticker-color:<?= e((string) setting('ticker_color', '#ffffff')) ?>;--ticker-highlight:<?= e((string) setting('ticker_highlight_color', '#7fe3ff')) ?>;--ticker-size:<?= (int) setting('ticker_font_size', '14') ?>px;--ticker-weight:<?= (int) setting('ticker_font_weight', '600') ?>;--ticker-family:<?= e((string) setting('ticker_font_family', 'inherit')) ?>;--ticker-duration:<?= (int) setting('ticker_speed', '45') ?>s;display:flex;overflow:hidden;border-radius:10px;background:var(--ticker-bg);color:var(--ticker-color);font-size:var(--ticker-size);font-weight:var(--ticker-weight);font-family:var(--ticker-family);">
    <div style="padding:10px 18px;background:rgba(0,0,0,.26);letter-spacing:.12em;font-size:.78em;font-weight:800;flex:none;">
      <?= e((string) setting('ticker_label', 'LATEST')) ?>
    </div>
    <div style="flex:1;overflow:hidden;display:flex;align-items:center;">
      <div style="display:flex;white-space:nowrap;padding-block:10px;animation:ticker-scroll var(--ticker-duration) linear infinite;">
        <?php for ($pass = 0; $pass < 2; $pass++): ?>
          <?php foreach (ticker_items() ?: [['text' => 'Add ticker items below to see them here', 'highlight' => 0, 'link' => '']] as $t): ?>
          <span style="padding-inline:22px;<?= (int) ($t['highlight'] ?? 0) === 1 ? 'color:var(--ticker-highlight);' : '' ?>">&bull; <?= e($t['text']) ?></span>
          <?php endforeach; ?>
        <?php endfor; ?>
      </div>
    </div>
  </div>
  <style>@keyframes ticker-scroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}</style>
  <p class="form-hint" style="margin-top:10px;">Colour and speed changes below update this preview instantly. Save to apply them to the website.</p>
</div>

<div class="a-grid" style="grid-template-columns:minmax(0,1fr) minmax(300px,380px);gap:20px;align-items:start;">
  <div>
    <div class="a-card">
      <div class="a-card-head"><h2>Ticker items (<?= count($items) ?>)</h2></div>
      <?php if (!$items): ?>
        <div class="empty">No ticker items yet.</div>
      <?php else: ?>
      <div class="a-table-wrap">
        <table class="a-table">
          <thead><tr><th>Message</th><th>Link</th><th>Highlight</th><th>Schedule</th><th>Active</th><th>Order</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($items as $t): ?>
            <tr>
              <td><?= e($t['text']) ?></td>
              <td><small class="media-path"><?= e((string) $t['link']) ?: '—' ?></small></td>
              <td><?= (int) $t['highlight'] === 1 ? '<span class="pill pill-info">Yes</span>' : '—' ?></td>
              <td><small>
                <?= $t['starts_at'] ? 'From ' . pretty_date($t['starts_at']) : '' ?>
                <?= $t['ends_at'] ? ' until ' . pretty_date($t['ends_at']) : '' ?>
                <?= (!$t['starts_at'] && !$t['ends_at']) ? 'Always' : '' ?>
              </small></td>
              <td><?= toggle_button((int) $t['id'], 'is_active', (int) $t['is_active'] === 1, 'Active') ?></td>
              <td><?= (int) $t['sort_order'] ?></td>
              <td class="actions">
                <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('ticker?edit=' . (int) $t['id'])) ?>">Edit</a>
                <?= delete_button((int) $t['id'], 'Delete this ticker item?') ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

    <div class="a-card">
      <div class="a-card-head"><h2>Appearance and behaviour</h2></div>
      <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save_style">
        <label class="check"><input type="checkbox" name="ticker_enabled" value="1" <?= setting_bool('ticker_enabled', true) ? 'checked' : '' ?>> Show the news ticker on the website</label>
        <label class="check"><input type="checkbox" name="ticker_pause_on_hover" value="1" <?= setting_bool('ticker_pause_on_hover', true) ? 'checked' : '' ?>> Pause scrolling when the visitor hovers over it</label>

        <div class="form-row">
          <div class="form-group">
            <label for="ticker_label">Label text</label>
            <input type="text" id="ticker_label" name="ticker_label" value="<?= e((string) setting('ticker_label', 'LATEST')) ?>">
          </div>
          <div class="form-group">
            <label for="tickerSpeed">Scroll duration (seconds, higher is slower)</label>
            <input type="number" id="tickerSpeed" name="ticker_speed" min="10" max="200" value="<?= (int) setting('ticker_speed', '45') ?>">
          </div>
          <div class="form-group">
            <label for="tickerSize">Font size (px)</label>
            <input type="number" id="tickerSize" name="ticker_font_size" min="10" max="26" value="<?= (int) setting('ticker_font_size', '14') ?>">
          </div>
          <div class="form-group">
            <label for="tickerWeight">Font weight</label>
            <select id="tickerWeight" name="ticker_font_weight">
              <?php foreach ([400, 500, 600, 700, 800] as $w): ?>
              <option value="<?= $w ?>" <?= (int) setting('ticker_font_weight', '600') === $w ? 'selected' : '' ?>><?= $w ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="tickerFamily">Font family</label>
            <select id="tickerFamily" name="ticker_font_family">
              <?php
              $fonts = ['inherit' => 'Same as website', 'Georgia, serif' => 'Georgia (serif)',
                        '"Times New Roman", serif' => 'Times New Roman', 'Verdana, sans-serif' => 'Verdana',
                        '"Trebuchet MS", sans-serif' => 'Trebuchet MS', '"Courier New", monospace' => 'Courier New',
                        'Tahoma, sans-serif' => 'Tahoma'];
              $currentFont = (string) setting('ticker_font_family', 'inherit');
              foreach ($fonts as $val => $label): ?>
              <option value="<?= e($val) ?>" <?= $currentFont === $val ? 'selected' : '' ?>><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="tickerBg">Background colour</label>
            <input type="color" id="tickerBg" name="ticker_bg" value="<?= e((string) setting('ticker_bg', '#0b6fa4')) ?>">
          </div>
          <div class="form-group">
            <label for="tickerColor">Text colour</label>
            <input type="color" id="tickerColor" name="ticker_color" value="<?= e((string) setting('ticker_color', '#ffffff')) ?>">
          </div>
          <div class="form-group">
            <label for="tickerHighlight">Highlight colour</label>
            <input type="color" id="tickerHighlight" name="ticker_highlight_color" value="<?= e((string) setting('ticker_highlight_color', '#7fe3ff')) ?>">
          </div>
        </div>
        <button class="btn btn-primary" type="submit">Save appearance</button>
      </form>
    </div>
  </div>

  <aside class="a-card">
    <div class="a-card-head"><h2><?= $editId ? 'Edit item' : 'Add a ticker item' ?></h2></div>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save_item">
      <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
      <div class="form-group">
        <label for="text">Message <span class="req">*</span></label>
        <textarea id="text" name="text" required maxlength="400" style="min-height:80px;"><?= e((string) $item['text']) ?></textarea>
      </div>
      <div class="form-group">
        <label for="link">Link (optional)</label>
        <input type="text" id="link" name="link" value="<?= e((string) $item['link']) ?>" placeholder="/products or https://…">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="starts_at">Show from</label>
          <input type="datetime-local" id="starts_at" name="starts_at" value="<?= $item['starts_at'] ? date('Y-m-d\TH:i', strtotime((string) $item['starts_at'])) : '' ?>">
        </div>
        <div class="form-group">
          <label for="ends_at">Show until</label>
          <input type="datetime-local" id="ends_at" name="ends_at" value="<?= $item['ends_at'] ? date('Y-m-d\TH:i', strtotime((string) $item['ends_at'])) : '' ?>">
        </div>
        <div class="form-group">
          <label for="sort_order">Order</label>
          <input type="number" id="sort_order" name="sort_order" value="<?= (int) $item['sort_order'] ?>">
        </div>
      </div>
      <label class="check"><input type="checkbox" name="highlight" value="1" <?= (int) $item['highlight'] === 1 ? 'checked' : '' ?>> Show in the highlight colour</label>
      <label class="check"><input type="checkbox" name="is_active" value="1" <?= (int) $item['is_active'] === 1 ? 'checked' : '' ?>> Active</label>
      <button class="btn btn-primary btn-block" type="submit"><?= $editId ? 'Save item' : 'Add item' ?></button>
      <?php if ($editId): ?><a class="btn btn-ghost btn-block" style="margin-top:8px;" href="<?= e(admin_url('ticker')) ?>">Cancel</a><?php endif; ?>
    </form>
  </aside>
</div>
<?php admin_footer(); ?>
