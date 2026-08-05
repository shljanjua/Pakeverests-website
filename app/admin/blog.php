<?php
/** Blog manager: create and edit posts with cover, in-content and bottom images. */
declare(strict_types=1);

$editId = get('edit');

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'save':
            $slug = slugify(post('slug') ?: post('title'));
            if (fetch_val('SELECT id FROM blog_posts WHERE slug = ? AND id <> ?', [$slug, $id])) {
                $slug .= '-' . substr((string) time(), -4);
            }
            $content = post('content');
            $data = [
                'slug'             => $slug,
                'title'            => post('title'),
                'excerpt'          => post('excerpt'),
                'content'          => $content,
                'category'         => post('category') ?: 'Water & Health',
                'tags'             => post('tags'),
                'author'           => post('author') ?: 'Pak-Everests Team',
                'reading_minutes'  => max(1, (int) ceil(str_word_count(strip_tags($content)) / 200)),
                'meta_title'       => post('meta_title'),
                'meta_description' => post('meta_description'),
                'meta_keywords'    => post('meta_keywords'),
                'is_featured'      => post('is_featured') ? 1 : 0,
                'status'           => post('status') === 'draft' ? 'draft' : 'published',
                'published_at'     => post('published_at') ? date('Y-m-d H:i:s', strtotime(post('published_at'))) : date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ];

            $current = $id > 0 ? fetch_one('SELECT * FROM blog_posts WHERE id = ?', [$id]) : [];
            foreach (['cover_image', 'inline_image_1', 'inline_image_2', 'bottom_image'] as $field) {
                $data[$field] = admin_upload_field($field, 'blog', (string) ($current[$field] ?? ''));
            }

            if ($id > 0) {
                db_update('blog_posts', $data, $id);
                admin_log('Updated blog post ' . $data['title'], 'blog_posts', $id);
                flash('success', 'Post saved.');
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $id = db_insert('blog_posts', $data);
                admin_log('Created blog post ' . $data['title'], 'blog_posts', $id);
                flash('success', 'Post created.');
            }
            if ($data['status'] === 'published') {
                notify_search_engines('blog/' . $data['slug']);
            }
            redirect('admin/blog?edit=' . $id);
            break;

        case 'delete':
            db_delete('blog_posts', $id);
            admin_log('Deleted blog post', 'blog_posts', $id);
            flash('success', 'Post deleted.');
            redirect('admin/blog');
            break;

        case 'toggle':
            if (post('column') === 'is_featured') {
                q('UPDATE blog_posts SET is_featured = IF(is_featured = 1, 0, 1) WHERE id = ?', [$id]);
            }
            redirect('admin/blog');
            break;
    }
}

/* ---- Editor -------------------------------------------------------------- */
if ($editId !== '') {
    $isNew = $editId === 'new';
    $post_ = $isNew ? [
        'id' => 0, 'slug' => '', 'title' => '', 'excerpt' => '', 'content' => '', 'cover_image' => '',
        'inline_image_1' => '', 'inline_image_2' => '', 'bottom_image' => '', 'category' => 'Water & Health',
        'tags' => '', 'author' => 'Pak-Everests Team', 'meta_title' => '', 'meta_description' => '',
        'meta_keywords' => '', 'is_featured' => 0, 'status' => 'published', 'published_at' => date('Y-m-d H:i:s'),
        'views' => 0,
    ] : fetch_one('SELECT * FROM blog_posts WHERE id = ?', [(int) $editId]);

    if (!$post_) { flash('error', 'Post not found.'); redirect('admin/blog'); }

    $actions = [['label' => '← All posts', 'href' => admin_url('blog'), 'class' => 'btn-ghost']];
    if (!$isNew) {
        $actions[] = ['label' => 'View on site', 'href' => url('blog/' . $post_['slug']), 'class' => 'btn-ghost'];
    }
    admin_header($isNew ? 'Write a new post' : 'Edit post', 'Images can be placed at the top, inside the content and at the bottom', $actions);
    ?>
    <form method="post" enctype="multipart/form-data" data-dirty-guard>
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int) $post_['id'] ?>">

      <div class="a-grid" style="grid-template-columns:minmax(0,2fr) minmax(280px,1fr);gap:20px;align-items:start;">
        <div>
          <div class="a-card">
            <div class="form-group">
              <label for="title">Post title <span class="req">*</span></label>
              <input type="text" id="title" name="title" required value="<?= e((string) $post_['title']) ?>" data-slug-source="slug" style="font-size:1.05rem;">
            </div>
            <div class="form-group">
              <label for="slug">URL slug</label>
              <input type="text" id="slug" name="slug" value="<?= e((string) $post_['slug']) ?>">
              <span class="form-hint">Final URL: <?= e(SITE_URL) ?>/blog/<span id="slugPreview"><?= e((string) $post_['slug']) ?></span></span>
            </div>
            <div class="form-group">
              <label for="excerpt">Excerpt (shown on cards and as the meta fallback)</label>
              <textarea id="excerpt" name="excerpt" style="min-height:80px;"><?= e((string) $post_['excerpt']) ?></textarea>
            </div>
          </div>

          <div class="a-card">
            <div class="a-card-head"><h2>Content</h2></div>
            <p class="form-hint" style="margin-bottom:8px;">
              HTML is allowed. Use &lt;h2&gt;, &lt;h3&gt; and &lt;h4&gt; for headings — the post title is already the page H1.
              The first in-content image is inserted automatically about a third of the way down.
            </p>
            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px;">
              <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('content','&lt;h2&gt;','&lt;/h2&gt;')">H2</button>
              <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('content','&lt;h3&gt;','&lt;/h3&gt;')">H3</button>
              <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('content','&lt;p&gt;','&lt;/p&gt;')">Paragraph</button>
              <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('content','&lt;strong&gt;','&lt;/strong&gt;')">Bold</button>
              <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('content','&lt;em&gt;','&lt;/em&gt;')">Italic</button>
              <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('content','&lt;ul&gt;&lt;li&gt;','&lt;/li&gt;&lt;/ul&gt;')">List</button>
              <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('content','&lt;a href=&quot;&quot;&gt;','&lt;/a&gt;')">Link</button>
              <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('content','&lt;blockquote&gt;','&lt;/blockquote&gt;')">Quote</button>
            </div>
            <textarea id="content" name="content" class="code tall"><?= e((string) $post_['content']) ?></textarea>
          </div>

          <div class="a-card">
            <div class="a-card-head"><h2>Search engine optimisation</h2></div>
            <div class="form-group">
              <label for="meta_title">Meta title</label>
              <input type="text" id="meta_title" name="meta_title" value="<?= e((string) $post_['meta_title']) ?>" data-counter="60">
            </div>
            <div class="form-group">
              <label for="meta_description">Meta description</label>
              <textarea id="meta_description" name="meta_description" data-counter="160" style="min-height:80px;"><?= e((string) $post_['meta_description']) ?></textarea>
            </div>
            <div class="form-group">
              <label for="meta_keywords">Meta keywords</label>
              <input type="text" id="meta_keywords" name="meta_keywords" value="<?= e((string) $post_['meta_keywords']) ?>">
            </div>
          </div>
        </div>

        <aside>
          <div class="a-card">
            <div class="a-card-head"><h2>Publish</h2></div>
            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" name="status">
                <option value="published" <?= $post_['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="draft" <?= $post_['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
              </select>
            </div>
            <div class="form-group">
              <label for="published_at">Publish date</label>
              <input type="datetime-local" id="published_at" name="published_at"
                     value="<?= e(date('Y-m-d\TH:i', strtotime((string) ($post_['published_at'] ?: 'now')))) ?>">
            </div>
            <div class="form-group">
              <label for="category">Category</label>
              <input type="text" id="category" name="category" list="catList" value="<?= e((string) $post_['category']) ?>">
              <datalist id="catList">
                <?php foreach (fetch_all('SELECT DISTINCT category FROM blog_posts') as $c): ?>
                <option value="<?= e($c['category']) ?>">
                <?php endforeach; ?>
                <option value="Water &amp; Health"><option value="Buying Guide"><option value="Products"><option value="Business"><option value="Custom Labels"><option value="Company News">
              </datalist>
            </div>
            <div class="form-group">
              <label for="tags">Tags (comma separated)</label>
              <input type="text" id="tags" name="tags" value="<?= e((string) $post_['tags']) ?>">
            </div>
            <div class="form-group">
              <label for="author">Author</label>
              <input type="text" id="author" name="author" value="<?= e((string) $post_['author']) ?>">
            </div>
            <label class="check"><input type="checkbox" name="is_featured" value="1" <?= (int) $post_['is_featured'] === 1 ? 'checked' : '' ?>> Feature this post</label>
            <?php if (!$isNew): ?>
              <p class="form-hint"><?= (int) $post_['views'] ?> views so far.</p>
            <?php endif; ?>
            <button class="btn btn-primary btn-block" type="submit">Save post</button>
          </div>

          <div class="a-card">
            <div class="a-card-head"><h2>Images</h2></div>
            <?php
            image_field('cover_image', (string) $post_['cover_image'], 'Cover image (top of the post)', '16:9, around 1200x675 pixels.');
            image_field('inline_image_1', (string) $post_['inline_image_1'], 'In-content image 1', 'Inserted automatically about a third of the way through.');
            image_field('inline_image_2', (string) $post_['inline_image_2'], 'In-content image 2', 'Placed at the end of the content body.');
            image_field('bottom_image', (string) $post_['bottom_image'], 'Bottom image', 'Shown after the article body.');
            ?>
          </div>
        </aside>
      </div>
    </form>
    <?php
    admin_footer();
    return;
}

/* ---- List ---------------------------------------------------------------- */
$list = admin_list('blog_posts', ['order' => 'published_at DESC', 'perPage' => 25]);

admin_header('Blog', 'Write and manage articles', [
    ['label' => '+ Write a post', 'href' => admin_url('blog?edit=new')],
]);
?>
<div class="a-card">
  <?php if (!$list['rows']): ?>
    <div class="empty">No posts yet. Write your first article.</div>
  <?php else: ?>
  <div class="a-table-wrap">
    <table class="a-table">
      <thead><tr><th>Cover</th><th>Title</th><th>Category</th><th>Views</th><th>Featured</th><th>Status</th><th>Published</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($list['rows'] as $b): ?>
        <tr>
          <td><img class="thumb" src="<?= e(media_url($b['cover_image'], 'photo')) ?>" alt=""></td>
          <td><strong><?= e($b['title']) ?></strong><br><small style="color:var(--a-muted);">/blog/<?= e($b['slug']) ?></small></td>
          <td><small><?= e($b['category']) ?></small></td>
          <td><?= (int) $b['views'] ?></td>
          <td><?= toggle_button((int) $b['id'], 'is_featured', (int) $b['is_featured'] === 1, 'Featured') ?></td>
          <td><?= status_pill($b['status']) ?></td>
          <td><small><?= pretty_date($b['published_at']) ?></small></td>
          <td class="actions">
            <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('blog?edit=' . (int) $b['id'])) ?>">Edit</a>
            <a class="btn-icon" title="View" href="<?= e(url('blog/' . $b['slug'])) ?>" target="_blank" rel="noopener">
              <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.7 7.6 1 12c1.7 4.4 6 7.5 11 7.5s9.3-3.1 11-7.5c-1.7-4.4-6-7.5-11-7.5zm0 12.5a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/></svg>
            </a>
            <?= delete_button((int) $b['id'], 'Delete this post permanently?') ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?= pagination_links($list['pagination'], admin_url('blog')) ?>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
