<?php
/** Products: full editor with images, pricing, specs and SEO. */
declare(strict_types=1);

$editId = get('edit');

if (is_post()) {
    $id = (int) post('id');
    switch (post('action')) {
        case 'save':
            $slug = slugify(post('slug') ?: post('name'));
            $exists = fetch_val('SELECT id FROM products WHERE slug = ? AND id <> ?', [$slug, $id]);
            if ($exists) { $slug .= '-' . substr((string) time(), -4); }

            $data = [
                'slug'             => $slug,
                'name'             => post('name'),
                'short_name'       => post('short_name'),
                'category'         => post('category'),
                'pack_size'        => post('pack_size'),
                'volume_label'     => post('volume_label'),
                'price'            => (float) post('price', 0),
                'price_unit'       => post('price_unit'),
                'security_deposit' => (float) post('security_deposit', 0),
                'rent_price'       => post('rent_price') !== '' ? (float) post('rent_price') : null,
                'rent_unit'        => post('rent_unit'),
                'sku'              => post('sku'),
                'tagline'          => post('tagline'),
                'short_description'=> post('short_description'),
                'long_description' => post('long_description'),
                'features'         => post('features'),
                'specifications'   => post('specifications'),
                'best_for'         => post('best_for'),
                'free_delivery'    => post('free_delivery') ? 1 : 0,
                'is_coming_soon'   => post('is_coming_soon') ? 1 : 0,
                'is_featured'      => post('is_featured') ? 1 : 0,
                'is_bestseller'    => post('is_bestseller') ? 1 : 0,
                'stock_status'     => in_array(post('stock_status'), ['in_stock', 'on_demand', 'coming_soon', 'out_of_stock'], true) ? post('stock_status') : 'in_stock',
                'meta_title'       => post('meta_title'),
                'meta_description' => post('meta_description'),
                'meta_keywords'    => post('meta_keywords'),
                'sort_order'       => (int) post('sort_order', 0),
                'status'           => post('status') === 'draft' ? 'draft' : 'published',
                'updated_at'       => date('Y-m-d H:i:s'),
            ];

            if ($id > 0) {
                $current = fetch_one('SELECT main_image FROM products WHERE id = ?', [$id]);
                $data['main_image'] = admin_upload_field('main_image', 'products', (string) ($current['main_image'] ?? ''));
                db_update('products', $data, $id);
                admin_log('Updated product ' . $data['name'], 'products', $id);
                flash('success', 'Product saved.');
            } else {
                $data['main_image'] = admin_upload_field('main_image', 'products', '');
                $data['created_at'] = date('Y-m-d H:i:s');
                $id = db_insert('products', $data);
                admin_log('Created product ' . $data['name'], 'products', $id);
                flash('success', 'Product created.');
            }

            /* Extra gallery images */
            if (!empty($_FILES['gallery_images']['name'][0])) {
                foreach ($_FILES['gallery_images']['name'] as $i => $fname) {
                    if ($fname === '') { continue; }
                    $file = [
                        'name'     => $_FILES['gallery_images']['name'][$i],
                        'type'     => $_FILES['gallery_images']['type'][$i],
                        'tmp_name' => $_FILES['gallery_images']['tmp_name'][$i],
                        'error'    => $_FILES['gallery_images']['error'][$i],
                        'size'     => $_FILES['gallery_images']['size'][$i],
                    ];
                    $up = handle_upload($file, 'products');
                    if ($up['ok']) {
                        media_record($up, $data['name'], 'products');
                        db_insert('product_images', [
                            'product_id' => $id,
                            'image_path' => $up['path'],
                            'alt_text'   => $data['name'],
                            'sort_order' => 0,
                        ]);
                    }
                }
            }
            redirect('admin/products?edit=' . $id);
            break;

        case 'delete':
            q('DELETE FROM product_images WHERE product_id = ?', [$id]);
            db_delete('products', $id);
            admin_log('Deleted product', 'products', $id);
            flash('success', 'Product deleted.');
            redirect('admin/products');
            break;

        case 'delete_image':
            db_delete('product_images', $id);
            flash('success', 'Image removed.');
            redirect('admin/products?edit=' . (int) post('product_id'));
            break;

        case 'toggle':
            $col = post('column');
            if (in_array($col, ['is_featured', 'is_bestseller', 'is_coming_soon', 'free_delivery'], true)) {
                q("UPDATE products SET `$col` = IF(`$col` = 1, 0, 1) WHERE id = ?", [$id]);
            }
            redirect('admin/products');
            break;
    }
}

/* ---- Editor -------------------------------------------------------------- */
if ($editId !== '') {
    $isNew = $editId === 'new';
    $p = $isNew ? [
        'id' => 0, 'slug' => '', 'name' => '', 'short_name' => '', 'category' => 'bottles',
        'pack_size' => '', 'volume_label' => '', 'price' => '0', 'price_unit' => 'per bottle',
        'security_deposit' => '0', 'rent_price' => '', 'rent_unit' => '', 'sku' => '', 'tagline' => '',
        'short_description' => '', 'long_description' => '', 'features' => '', 'specifications' => '',
        'best_for' => '', 'main_image' => '', 'free_delivery' => 1, 'is_coming_soon' => 0,
        'is_featured' => 0, 'is_bestseller' => 0, 'stock_status' => 'in_stock',
        'meta_title' => '', 'meta_description' => '', 'meta_keywords' => '', 'sort_order' => 0, 'status' => 'published',
    ] : fetch_one('SELECT * FROM products WHERE id = ?', [(int) $editId]);

    if (!$p) { flash('error', 'Product not found.'); redirect('admin/products'); }
    $images = $isNew ? [] : fetch_all('SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC', [(int) $p['id']]);

    admin_header($isNew ? 'Add a product' : 'Edit: ' . $p['name'], 'Everything on the product page is controlled here', [
        ['label' => '← All products', 'href' => admin_url('products'), 'class' => 'btn-ghost'],
    ] + ($isNew ? [] : [1 => ['label' => 'View on site', 'href' => url('product/' . $p['slug']), 'class' => 'btn-ghost']]));
    ?>
    <form method="post" enctype="multipart/form-data" data-dirty-guard>
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">

      <div class="a-grid" style="grid-template-columns:minmax(0,2fr) minmax(280px,1fr);gap:20px;align-items:start;">
        <div>
          <div class="a-card">
            <div class="a-card-head"><h2>Basics</h2></div>
            <div class="form-row">
              <div class="form-group">
                <label for="name">Product name <span class="req">*</span></label>
                <input type="text" id="name" name="name" required value="<?= e((string) $p['name']) ?>" data-slug-source="slug">
              </div>
              <div class="form-group">
                <label for="slug">URL slug</label>
                <input type="text" id="slug" name="slug" value="<?= e((string) $p['slug']) ?>" placeholder="19-litre-refill-bottle">
                <span class="form-hint">Leave blank to generate from the name. Changing it changes the page URL.</span>
              </div>
              <div class="form-group">
                <label for="short_name">Short name</label>
                <input type="text" id="short_name" name="short_name" value="<?= e((string) $p['short_name']) ?>" placeholder="19L Refill">
              </div>
              <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category">
                  <?php foreach (['refill', 'bottles', 'pet', 'glass', 'pouch', 'equipment', 'bulk'] as $c): ?>
                  <option value="<?= e($c) ?>" <?= $p['category'] === $c ? 'selected' : '' ?>><?= e(ucfirst($c)) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label for="pack_size">Pack size</label>
                <input type="text" id="pack_size" name="pack_size" value="<?= e((string) $p['pack_size']) ?>" placeholder="Pack of 12 bottles">
              </div>
              <div class="form-group">
                <label for="volume_label">Volume label</label>
                <input type="text" id="volume_label" name="volume_label" value="<?= e((string) $p['volume_label']) ?>" placeholder="500 ml">
              </div>
            </div>
            <div class="form-group">
              <label for="tagline">Tagline (shown under the page title)</label>
              <input type="text" id="tagline" name="tagline" value="<?= e((string) $p['tagline']) ?>">
            </div>
          </div>

          <div class="a-card">
            <div class="a-card-head"><h2>Pricing</h2></div>
            <div class="form-row">
              <div class="form-group">
                <label for="price">Price (PKR)</label>
                <input type="number" step="0.01" id="price" name="price" value="<?= e((string) $p['price']) ?>">
              </div>
              <div class="form-group">
                <label for="price_unit">Price unit</label>
                <input type="text" id="price_unit" name="price_unit" value="<?= e((string) $p['price_unit']) ?>" placeholder="per refill, per pack of 12, per litre">
              </div>
              <div class="form-group">
                <label for="security_deposit">Refundable security deposit (PKR)</label>
                <input type="number" step="0.01" id="security_deposit" name="security_deposit" value="<?= e((string) $p['security_deposit']) ?>">
                <span class="form-hint">Set to 0 for products with no deposit.</span>
              </div>
              <div class="form-group">
                <label for="sku">SKU</label>
                <input type="text" id="sku" name="sku" value="<?= e((string) $p['sku']) ?>">
              </div>
              <div class="form-group">
                <label for="rent_price">Rental price (PKR)</label>
                <input type="number" step="0.01" id="rent_price" name="rent_price" value="<?= e((string) $p['rent_price']) ?>" placeholder="Dispensers only">
              </div>
              <div class="form-group">
                <label for="rent_unit">Rental unit</label>
                <input type="text" id="rent_unit" name="rent_unit" value="<?= e((string) $p['rent_unit']) ?>" placeholder="per month on rent">
              </div>
            </div>
          </div>

          <div class="a-card">
            <div class="a-card-head"><h2>Descriptions</h2></div>
            <div class="form-group">
              <label for="short_description">Short description (product cards and meta fallback)</label>
              <textarea id="short_description" name="short_description"><?= e((string) $p['short_description']) ?></textarea>
            </div>
            <div class="form-group">
              <label for="long_description">Full description (HTML allowed)</label>
              <p class="form-hint" style="margin-bottom:6px;">
                Use &lt;h2&gt; and &lt;h3&gt; headings, &lt;p&gt; paragraphs and &lt;ul&gt; lists. The page H1 is the product name, so never use &lt;h1&gt; here.
              </p>
              <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px;">
                <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('long_description','&lt;h2&gt;','&lt;/h2&gt;')">H2</button>
                <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('long_description','&lt;h3&gt;','&lt;/h3&gt;')">H3</button>
                <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('long_description','&lt;p&gt;','&lt;/p&gt;')">Paragraph</button>
                <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('long_description','&lt;strong&gt;','&lt;/strong&gt;')">Bold</button>
                <button class="btn btn-ghost btn-sm" type="button" onclick="peWrap('long_description','&lt;ul&gt;&lt;li&gt;','&lt;/li&gt;&lt;/ul&gt;')">List</button>
              </div>
              <textarea id="long_description" name="long_description" class="code tall"><?= e((string) $p['long_description']) ?></textarea>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="features">Key features (one per line)</label>
                <textarea id="features" name="features" placeholder="Free delivery&#10;Food grade BPA free bottle"><?= e((string) $p['features']) ?></textarea>
              </div>
              <div class="form-group">
                <label for="specifications">Specifications (one per line, "Label: Value")</label>
                <textarea id="specifications" name="specifications" placeholder="Volume: 19 Litres&#10;Refill price: Rs 250"><?= e((string) $p['specifications']) ?></textarea>
              </div>
            </div>
            <div class="form-group">
              <label for="best_for">Best for</label>
              <input type="text" id="best_for" name="best_for" value="<?= e((string) $p['best_for']) ?>" placeholder="Homes, offices, schools, hospitals">
            </div>
          </div>

          <div class="a-card">
            <div class="a-card-head"><h2>Search engine optimisation</h2></div>
            <div class="form-group">
              <label for="meta_title">Meta title</label>
              <input type="text" id="meta_title" name="meta_title" value="<?= e((string) $p['meta_title']) ?>" data-counter="60">
            </div>
            <div class="form-group">
              <label for="meta_description">Meta description</label>
              <textarea id="meta_description" name="meta_description" data-counter="160" style="min-height:80px;"><?= e((string) $p['meta_description']) ?></textarea>
            </div>
            <div class="form-group">
              <label for="meta_keywords">Meta keywords</label>
              <input type="text" id="meta_keywords" name="meta_keywords" value="<?= e((string) $p['meta_keywords']) ?>">
            </div>
          </div>
        </div>

        <aside>
          <div class="a-card">
            <div class="a-card-head"><h2>Publish</h2></div>
            <div class="form-group">
              <label for="status">Status</label>
              <select id="status" name="status">
                <option value="published" <?= $p['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="draft" <?= $p['status'] === 'draft' ? 'selected' : '' ?>>Draft (hidden)</option>
              </select>
            </div>
            <div class="form-group">
              <label for="stock_status">Availability</label>
              <select id="stock_status" name="stock_status">
                <?php foreach (['in_stock' => 'In stock', 'on_demand' => 'On demand', 'coming_soon' => 'Coming soon', 'out_of_stock' => 'Out of stock'] as $k => $v): ?>
                <option value="<?= e($k) ?>" <?= $p['stock_status'] === $k ? 'selected' : '' ?>><?= e($v) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="sort_order">Display order</label>
              <input type="number" id="sort_order" name="sort_order" value="<?= (int) $p['sort_order'] ?>">
            </div>
            <label class="check"><input type="checkbox" name="free_delivery" value="1" <?= (int) $p['free_delivery'] === 1 ? 'checked' : '' ?>> Free delivery</label>
            <label class="check"><input type="checkbox" name="is_featured" value="1" <?= (int) $p['is_featured'] === 1 ? 'checked' : '' ?>> Show on the home page</label>
            <label class="check"><input type="checkbox" name="is_bestseller" value="1" <?= (int) $p['is_bestseller'] === 1 ? 'checked' : '' ?>> Best seller badge</label>
            <label class="check"><input type="checkbox" name="is_coming_soon" value="1" <?= (int) $p['is_coming_soon'] === 1 ? 'checked' : '' ?>> Coming soon (hides the order button)</label>
            <button class="btn btn-primary btn-block" type="submit">Save product</button>
          </div>

          <div class="a-card">
            <div class="a-card-head"><h2>Main image</h2></div>
            <?php image_field('main_image', (string) $p['main_image'], 'Product photo', 'WebP recommended, square, around 800x800 pixels.'); ?>
          </div>

          <div class="a-card">
            <div class="a-card-head"><h2>Additional images</h2></div>
            <div class="form-group">
              <label for="gallery_images">Upload more photos</label>
              <input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
            </div>
            <?php if ($images): ?>
            <div class="media-grid">
              <?php foreach ($images as $img): ?>
              <div class="media-tile">
                <img src="<?= e(media_url($img['image_path'], 'product')) ?>" alt="">
                <div class="media-tile-body">
                  <span class="media-path"><?= e(basename($img['image_path'])) ?></span>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <p class="form-hint">Remove images from the list below the form after saving.</p>
            <?php endif; ?>
          </div>
        </aside>
      </div>
    </form>

    <?php if ($images): ?>
    <div class="a-card">
      <div class="a-card-head"><h2>Manage additional images</h2></div>
      <div class="a-table-wrap">
        <table class="a-table">
          <thead><tr><th>Image</th><th>Path</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($images as $img): ?>
            <tr>
              <td><img class="thumb" src="<?= e(media_url($img['image_path'], 'product')) ?>" alt=""></td>
              <td><small class="media-path"><?= e($img['image_path']) ?></small></td>
              <td class="actions">
                <form method="post" class="inline-form" data-confirm="Remove this image?">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="delete_image">
                  <input type="hidden" name="id" value="<?= (int) $img['id'] ?>">
                  <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                  <button class="btn btn-danger btn-sm" type="submit">Remove</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

    <?php
    admin_footer();
    return;
}

/* ---- List ---------------------------------------------------------------- */
$products = fetch_all('SELECT * FROM products ORDER BY sort_order ASC, id ASC');

admin_header('Products', 'Prices, descriptions, photos and availability', [
    ['label' => '+ Add product', 'href' => admin_url('products?edit=new')],
]);
?>
<div class="a-card">
  <?php if (!$products): ?>
    <div class="empty">No products yet. Add your first product to get started.</div>
  <?php else: ?>
  <div class="a-table-wrap">
    <table class="a-table">
      <thead><tr><th>Photo</th><th>Product</th><th>Price</th><th>Deposit</th><th>Featured</th><th>Best seller</th><th>Coming soon</th><th>Status</th><th>Order</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td><img class="thumb" src="<?= e(media_url($p['main_image'], 'product')) ?>" alt=""></td>
          <td>
            <strong><?= e($p['name']) ?></strong><br>
            <small style="color:var(--a-muted);">/product/<?= e($p['slug']) ?></small>
          </td>
          <td><?= (float) $p['price'] > 0 ? money($p['price']) : '—' ?><br><small style="color:var(--a-muted);"><?= e($p['price_unit']) ?></small></td>
          <td><?= (float) $p['security_deposit'] > 0 ? money($p['security_deposit']) : '—' ?></td>
          <td><?= toggle_button((int) $p['id'], 'is_featured', (int) $p['is_featured'] === 1, 'Featured') ?></td>
          <td><?= toggle_button((int) $p['id'], 'is_bestseller', (int) $p['is_bestseller'] === 1, 'Best seller') ?></td>
          <td><?= toggle_button((int) $p['id'], 'is_coming_soon', (int) $p['is_coming_soon'] === 1, 'Coming soon') ?></td>
          <td><?= status_pill($p['status']) ?></td>
          <td><?= (int) $p['sort_order'] ?></td>
          <td class="actions">
            <a class="btn btn-ghost btn-sm" href="<?= e(admin_url('products?edit=' . (int) $p['id'])) ?>">Edit</a>
            <a class="btn-icon" title="View on site" href="<?= e(url('product/' . $p['slug'])) ?>" target="_blank" rel="noopener">
              <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.7 7.6 1 12c1.7 4.4 6 7.5 11 7.5s9.3-3.1 11-7.5c-1.7-4.4-6-7.5-11-7.5zm0 12.5a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/></svg>
            </a>
            <?= delete_button((int) $p['id'], 'Delete this product and all its images?') ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php admin_footer(); ?>
