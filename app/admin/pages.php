<?php
/** CMS pages: edit legal pages and create new pages. */
declare(strict_types=1);

$editId = get('edit');

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'save':
            $slug = slugify(post('slug') ?: post('title'));
            if (fetch_val('SELECT id FROM pages WHERE slug = ? AND id <> ?', [$slug, $id])) {
                $slug .= '-' . substr((string) time(), -4);
            }
            $reserved = ['products', 'product', 'order', 'contact', 'about', 'blog', 'gallery', 'documents',
                         'careers', 'faqs', 'reviews', 'distribution', 'coverage-areas', 'admin', 'search'];
            if (in_array($slug, $reserved, true)) {
                flash('error', 'The slug "' . $slug . '" is reserved by a built-in page. Please choose another.');
                redirect('admin/pages' . ($id ? '?edit=' . $id : '?edit=new'));
            }

            $data = [
                'slug'             => $slug,
                'title'            => post('title'),
                'subtitle'         => post('subtitle'),
                'content'          => post('content'),
                'nav_group'        => in_array(post('nav_group'), ['none', 'legal', 'company', 'support', 'main'], true) ? post('nav_group') : 'legal',
                'meta_title'       => post('meta_title'),
                'meta_description' => post('meta_description'),
                'meta_keywords'    => post('meta_keywords'),
                'noindex'          => post('noindex') ? 1 : 0,
                'sort_order'       => (int) post('sort_order', 0),
                'status'           => post('status') === 'draft' ? 'draft' : 'published',
                'updated_at'       => date('Y-m-d H:i:s'),
            ];
            $current = $id > 0 ? fetch_one('SELECT hero_image FROM pages WHERE id = ?', [$id]) : [];
            $data['hero_image'] = admin_upload_field('hero_image', 'pages', (string) ($current['hero_image'] ?? ''));

            if ($id > 0) {
                db_update('pages', $data, $id);
                admin_log('Updated page ' . $data['title'], 'pages', $id);
                flash('success', 'Page saved.');
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $id = db_insert('pages', $data);
                admin_log('Created page ' . $data['title'], 'pages', $id);
                flash('success', 'Page created.');
            }
            redirect('admin/pages?edit=' . $id);
            break;

        case 'delete':
            $page = fetch_one('SELECT * FROM pages WHERE id = ?', [$id]);
            if ($page && (int) $page['is_system'] === 1) {
                flash('error', 'This is a system page and cannot be deleted. You can set it to draft instead.');
            } else {
                db_delete('pages', $id);
                admin_log('Deleted page', 'pages', $id);
                flash('success', 'Page deleted.');
            }
            redirect('admin/pages');
            break;
    }
}

/* ---- Editor -------------------------------------------------------------- */
if ($editId !== '') {
    $isNew = $editId === 'new';
    $pg = $isNew ? [
        'id' => 0, 'slug' => '', 'title' => '', 'subtitle' => '', 'content' => '', 'hero_image' => '',
        'nav_group' => 'legal', 'is_system' => 0, 'meta_title' => '', 'meta_description' => '',
        'meta_keywords' => '', 'noindex' => 0, 'sort_order' => 0, 'status' => 'published',
    ] : fetch_one('SELECT * FROM pages WHERE id = ?', [(int) $editId]);

    if (!$pg) { flash('error', 'Page not found.'); redirect('admin/pages'); }

    $actions = [['label' => '← All pages', 'href' => admin_url('pages'), 'class' => 'btn-ghost']];
    if (!$isNew) {
        $actions[] = ['label' => 'View on site', 'href' => url($pg['slug']), 'class' => 'btn-ghost'];
    }
    admin_header($isNew ? 'Create a page' : 'Edit: ' . $pg['title'], 'Legal pages, policies and any custom page you need', $actions);
    ?>
    <form method="post" enctype="multipart/form-data" data-dirty-guard>
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int) $pg['id'] ?>">

      <div class="a-grid" style="grid-template-columns:minmax(0,2fr) minmax(280px,1fr);gap:20px;align-items:start;">
        <div>
          <div class="a-card">
            <div class="form-group">
              <label for="title">Page title <span class="req">*</span></label>
              <input type="text" id="title" name="title" required value="<?= e((string) $pg['title']) ?>" data-slug-source="slug" style="font-size:1.05rem;">
            </div>
            <div class="form-group">
              <label for="slug">URL slug</label>
              <input type="text" id="slug" name="slug" value="<?= e((string) $pg['slug']) ?>">
              <span class="form-hint">Page address: <?= e(SITE_URL) ?>/<?= e((string) $pg['slug']) ?></span>
            </div>
            <div class="form-group">
              <label for="subtitle">Subtitle (shown under the heading)</label>
              <input type="text" id="subtitle" name="subtitle" value="<?= e((string) $pg['subtitle']) ?>">
            </div>
          </div>

          <div class="a-card">
            <div class="a-card-head"><h2>Page content</h2></div>
            <p class="form-hint" style="margin-bottom:8px;">
              HTML is allowed. The page title is the H1, so start your content at &lt;h2&gt;. Tables, lists and links all work.
            </p>
            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px;">
              <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('content','&lt;h2&gt;','&lt;/h2&gt;')">H2</button>
              <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('content','&lt;h3&gt;','&lt;/h3&gt;')">H3</button>
              <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('content','&lt;p&gt;','&lt;/p&gt;')">Paragraph</button>
              <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('content','&lt;strong&gt;','&lt;/strong&gt;')">Bold</button>
              <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('content','&lt;ul&gt;&lt;li&gt;','&lt;/li&gt;&lt;/ul&gt;')">List</button>
              <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('content','&lt;ol&gt;&lt;li&gt;','&lt;/li&gt;&lt;/ol&gt;')">Numbered</button>
              <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('content','&lt;a href=&quot;&quot;&gt;','&lt;/a&gt;')">Link</button>
              <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('content','&lt;table&gt;&lt;thead&gt;&lt;tr&gt;&lt;th&gt;','&lt;/th&gt;&lt;/tr&gt;&lt;/thead&gt;&lt;tbody&gt;&lt;tr&gt;&lt;td&gt;&lt;/td&gt;&lt;/tr&gt;&lt;/tbody&gt;&lt;/table&gt;')">Table</button>
            </div>
            <textarea id="content" name="content" class="code tall" style="min-height:480px;"><?= e((string) $pg['content']) ?></textarea>
          </div>

          <div class="a-card">
            <div class="a-card-head"><h2>Search engine optimisation</h2></div>
            <div class="form-group">
              <label for="meta_title">Meta title</label>
              <input type="text" id="meta_title" name="meta_title" value="<?= e((string) $pg['meta_title']) ?>" data-counter="60">
            </div>
            <div class="form-group">
              <label for="meta_description">Meta description</label>
              <textarea id="meta_description" name="meta_description" data-counter="160" style="min-height:80px;"><?= e((string) $pg['meta_description']) ?></textarea>
            </div>
            <div class="form-group">
              <label for="meta_keywords">Meta keywords</label>
              <input type="text" id="meta_keywords" name="meta_keywords" value="<?= e((string) $pg['meta_keywords']) ?>">
            </div>
            <label class="check"><input type="checkbox" name="noindex" value="1" <?= (int) $pg['noindex'] === 1 ? 'checked' : '' ?>> Hide this page from search engines (noindex)</label>
          </div>
        </div>

        <aside>
          <div class="a-card">
            <div class="a-card-head"><h2>Publish</h2></div>
            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" name="status">
                <option value="published" <?= $pg['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="draft" <?= $pg['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
              </select>
            </div>
            <div class="form-group">
              <label for="nav_group">Footer menu section</label>
              <select id="nav_group" name="nav_group">
                <option value="legal" <?= $pg['nav_group'] === 'legal' ? 'selected' : '' ?>>Legal column</option>
                <option value="company" <?= $pg['nav_group'] === 'company' ? 'selected' : '' ?>>Company column</option>
                <option value="support" <?= $pg['nav_group'] === 'support' ? 'selected' : '' ?>>Support column</option>
                <option value="none" <?= $pg['nav_group'] === 'none' ? 'selected' : '' ?>>Do not show in menus</option>
              </select>
            </div>
            <div class="form-group">
              <label for="sort_order">Menu order</label>
              <input type="number" id="sort_order" name="sort_order" value="<?= (int) $pg['sort_order'] ?>">
            </div>
            <?php if ((int) $pg['is_system'] === 1): ?>
              <p class="form-hint">This is a system page. It can be edited freely but not deleted.</p>
            <?php endif; ?>
            <button class="btn btn-primary btn-block" type="submit">Save page</button>
          </div>

          <div class="a-card">
            <div class="a-card-head"><h2>Hero image</h2></div>
            <?php image_field('hero_image', (string) $pg['hero_image'], 'Optional hero image'); ?>
          </div>
        </aside>
      </div>
    </form>
    <?php
    admin_footer();
    return;
}

/* ---- List ---------------------------------------------------------------- */
$pages = fetch_all('SELECT * FROM pages ORDER BY nav_group ASC, sort_order ASC, title ASC');

admin_header('Pages and Legal', 'Edit every policy page and create new pages', [
    ['label' => '+ Create page', 'href' => admin_url('pages?edit=new')],
]);
?>
<div class="a-card">
  <p class="form-hint" style="margin-bottom:14px;">
    Legal pages appear in the footer Legal column automatically. Any new page you create becomes available at
    <?= e(SITE_URL) ?>/your-slug immediately and is added to the sitemap.
  </p>
  <?php if (!$pages): ?>
    <div class="empty">No pages yet.</div>
  <?php else: ?>
  <div class="a-table-wrap">
    <table class="a-table">
      <thead><tr><th>Title</th><th>URL</th><th>Menu section</th><th>Order</th><th>Status</th><th>Updated</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($pages as $pg): ?>
        <tr>
          <td><strong><?= e($pg['title']) ?></strong>
            <?php if ((int) $pg['is_system'] === 1): ?><span class="pill pill-muted">System</span><?php endif; ?>
            <?php if ((int) $pg['noindex'] === 1): ?><span class="pill pill-warn">noindex</span><?php endif; ?>
          </td>
          <td><small class="media-path">/<?= e($pg['slug']) ?></small></td>
          <td><small><?= e(ucfirst($pg['nav_group'])) ?></small></td>
          <td><?= (int) $pg['sort_order'] ?></td>
          <td><?= status_pill($pg['status']) ?></td>
          <td><small><?= pretty_date($pg['updated_at'] ?: $pg['created_at']) ?></small></td>
          <td class="actions">
            <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('pages?edit=' . (int) $pg['id'])) ?>">Edit</a>
            <a class="btn-icon" title="View" href="<?= e(url($pg['slug'])) ?>" target="_blank" rel="noopener">
              <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.7 7.6 1 12c1.7 4.4 6 7.5 11 7.5s9.3-3.1 11-7.5c-1.7-4.4-6-7.5-11-7.5zm0 12.5a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/></svg>
            </a>
            <?php if ((int) $pg['is_system'] !== 1): ?>
              <?= delete_button((int) $pg['id'], 'Delete this page permanently?') ?>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
