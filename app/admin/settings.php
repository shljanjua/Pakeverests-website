<?php
/** Website settings: general, SEO, maps, payments, SMTP, analytics. */
declare(strict_types=1);

$tab  = get('tab', 'general');
$tabs = [
    'general'   => 'General & Branding',
    'contact'   => 'Contact & Social',
    'seo'       => 'SEO & Search Console',
    'maps'      => 'Google Maps',
    'payment'   => 'Payment Details',
    'smtp'      => 'SMTP Email',
    'analytics' => 'Analytics & Tracking',
    'website'   => 'Website Behaviour',
];

if (is_post()) {
    switch (post('action')) {
        case 'save_general':
            $pairs = [
                'site_name' => post('site_name'), 'brand_suffix' => post('brand_suffix'),
                'site_tagline' => post('site_tagline'), 'footer_about' => post('footer_about'),
                'company_founded' => post('company_founded'), 'legal_name' => post('legal_name'),
                'license_authority' => post('license_authority'), 'license_number' => post('license_number'),
                'psqca_number' => post('psqca_number'), 'per_litre_rate' => post('per_litre_rate'),
                'hours_display' => post('hours_display'), 'hours_open' => post('hours_open'), 'hours_close' => post('hours_close'),
            ];
            foreach (['logo_path', 'logo_dark_path', 'favicon_path', 'og_image', 'hero_image', 'about_image', 'custom_label_image', 'distribution_image'] as $img) {
                $pairs[$img] = admin_upload_field($img, 'branding', (string) setting($img, ''));
            }
            settings_save($pairs);
            admin_log('Updated general settings');
            flash('success', 'General settings saved.');
            redirect('admin/settings?tab=general');
            break;

        case 'save_contact':
            settings_save([
                'contact_email' => post('contact_email'), 'sales_email' => post('sales_email'),
                'phone_display' => post('phone_display'), 'address_street' => post('address_street'),
                'address_city' => post('address_city'), 'address_region' => post('address_region'),
                'address_postal' => post('address_postal'), 'address_full' => post('address_full'),
                'google_business_url' => post('google_business_url'),
                'social_facebook' => post('social_facebook'), 'social_instagram' => post('social_instagram'),
                'social_youtube' => post('social_youtube'), 'social_tiktok' => post('social_tiktok'),
                'social_linkedin' => post('social_linkedin'), 'social_twitter' => post('social_twitter'),
                'social_whatsapp_channel' => post('social_whatsapp_channel'),
            ]);
            admin_log('Updated contact settings');
            flash('success', 'Contact and social settings saved.');
            redirect('admin/settings?tab=contact');
            break;

        case 'save_seo':
            settings_save([
                'meta_title' => post('meta_title'), 'meta_description' => post('meta_description'),
                'meta_keywords' => post('meta_keywords'),
                'google_site_verification' => post('google_site_verification'),
                'bing_site_verification' => post('bing_site_verification'),
                'yandex_verification' => post('yandex_verification'),
                'pinterest_verification' => post('pinterest_verification'),
                'robots_txt' => (string) ($_POST['robots_txt'] ?? ''),
                'custom_head_code' => (string) ($_POST['custom_head_code'] ?? ''),
                'custom_body_code' => (string) ($_POST['custom_body_code'] ?? ''),
                'sitemap_enabled' => post('sitemap_enabled') ? '1' : '0',
            ]);
            admin_log('Updated SEO settings');
            flash('success', 'SEO settings saved.');
            redirect('admin/settings?tab=seo');
            break;

        case 'indexnow_ping_all':
            $sent = indexnow_submit_all();
            admin_log('Submitted all URLs to IndexNow (' . $sent . ')');
            flash('success', 'Submitted ' . $sent . ' pages to IndexNow (Bing, Yandex and others). Google does not accept instant pings — use URL Inspection in Search Console for Google.');
            redirect('admin/settings?tab=seo');
            break;

        case 'save_maps':
            settings_save([
                'map_lat' => post('map_lat'), 'map_lng' => post('map_lng'), 'map_zoom' => post('map_zoom'),
                'map_embed_code' => (string) ($_POST['map_embed_code'] ?? ''),
                'map_directions_url' => post('map_directions_url'),
            ]);
            admin_log('Updated map settings');
            flash('success', 'Map settings saved.');
            redirect('admin/settings?tab=maps');
            break;

        case 'save_payment':
            settings_save([
                'payment_cod_enabled' => post('payment_cod_enabled') ? '1' : '0',
                'payment_note' => post('payment_note'),
                'easypaisa_enabled' => post('easypaisa_enabled') ? '1' : '0',
                'easypaisa_title' => post('easypaisa_title'), 'easypaisa_number' => post('easypaisa_number'),
                'jazzcash_enabled' => post('jazzcash_enabled') ? '1' : '0',
                'jazzcash_title' => post('jazzcash_title'), 'jazzcash_number' => post('jazzcash_number'),
                'bank_enabled' => post('bank_enabled') ? '1' : '0',
                'bank_name' => post('bank_name'), 'bank_branch' => post('bank_branch'),
                'bank_account_title' => post('bank_account_title'), 'bank_account_number' => post('bank_account_number'),
                'bank_iban' => post('bank_iban'), 'bank_swift' => post('bank_swift'),
            ]);
            admin_log('Updated payment settings');
            flash('success', 'Payment details saved. They are shown on the contact page and on quotations.');
            redirect('admin/settings?tab=payment');
            break;

        case 'save_smtp':
            $pairs = [
                'smtp_enabled' => post('smtp_enabled') ? '1' : '0',
                'smtp_host' => post('smtp_host'), 'smtp_port' => post('smtp_port'),
                'smtp_encryption' => post('smtp_encryption'), 'smtp_username' => post('smtp_username'),
                'smtp_from_email' => post('smtp_from_email'), 'smtp_from_name' => post('smtp_from_name'),
            ];
            if (post('smtp_password') !== '') {
                $pairs['smtp_password'] = post('smtp_password');
            }
            settings_save($pairs);
            admin_log('Updated SMTP settings');
            flash('success', 'SMTP settings saved.');
            redirect('admin/settings?tab=smtp');
            break;

        case 'test_smtp':
            $to = post('test_email') ?: contact_email();
            $ok = send_mail($to, 'Pak-Everests SMTP test', '<p>This is a test email from the Pak-Everests admin panel.</p>'
                . '<p>If you are reading this, SMTP is configured correctly and order notifications will reach you.</p>', '', 'smtp_test');
            if ($ok) {
                flash('success', 'Test email sent to ' . $to . '. Check the inbox and the spam folder.');
            } else {
                $last = fetch_one('SELECT * FROM email_log ORDER BY id DESC LIMIT 1');
                flash('error', 'The test email failed. ' . e(substr((string) ($last['error'] ?? ''), 0, 300)));
            }
            redirect('admin/settings?tab=smtp');
            break;

        case 'save_analytics':
            settings_save([
                'google_analytics_id' => post('google_analytics_id'),
                'google_tag_manager_id' => post('google_tag_manager_id'),
                'meta_pixel_id' => post('meta_pixel_id'),
                'tiktok_pixel_id' => post('tiktok_pixel_id'),
                'analytics_tracking_enabled' => post('analytics_tracking_enabled') ? '1' : '0',
                'analytics_track_bots' => post('analytics_track_bots') ? '1' : '0',
                'geo_lookup_enabled' => post('geo_lookup_enabled') ? '1' : '0',
            ]);
            admin_log('Updated analytics settings');
            flash('success', 'Analytics settings saved.');
            redirect('admin/settings?tab=analytics');
            break;

        case 'save_website':
            settings_save([
                'orders_enabled' => post('orders_enabled') ? '1' : '0',
                'reviews_open' => post('reviews_open') ? '1' : '0',
                'whatsapp_float_enabled' => post('whatsapp_float_enabled') ? '1' : '0',
                'default_theme' => post('default_theme') === 'dark' ? 'dark' : 'light',
                'announcement_bar' => post('announcement_bar'),
                'maintenance_mode' => post('maintenance_mode') ? '1' : '0',
                'free_delivery_note' => post('free_delivery_note'),
            ]);
            admin_log('Updated website behaviour settings');
            flash('success', 'Website settings saved.');
            redirect('admin/settings?tab=website');
            break;
    }
}

admin_header('Settings', 'Everything that configures the website');
?>
<div class="tabs">
  <?php foreach ($tabs as $k => $label): ?>
    <a class="tab<?= $tab === $k ? ' is-active' : '' ?>" href="<?= e(admin_url('settings?tab=' . $k)) ?>"><?= e($label) ?></a>
  <?php endforeach; ?>
</div>

<?php if ($tab === 'general'): ?>
<form method="post" enctype="multipart/form-data" class="a-card">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="save_general">
  <div class="a-card-head"><h2>Brand identity</h2></div>
  <div class="form-row">
    <div class="form-group">
      <label for="site_name">Website name</label>
      <input type="text" id="site_name" name="site_name" value="<?= e(site_name()) ?>">
    </div>
    <div class="form-group">
      <label for="brand_suffix">Title suffix (appended to page titles)</label>
      <input type="text" id="brand_suffix" name="brand_suffix" value="<?= e((string) setting('brand_suffix')) ?>">
    </div>
    <div class="form-group">
      <label for="site_tagline">Tagline</label>
      <input type="text" id="site_tagline" name="site_tagline" value="<?= e(site_tagline()) ?>">
    </div>
    <div class="form-group">
      <label for="company_founded">Year founded</label>
      <input type="text" id="company_founded" name="company_founded" value="<?= e((string) setting('company_founded')) ?>">
    </div>
  </div>
  <div class="form-group">
    <label for="legal_name">Registered legal name</label>
    <input type="text" id="legal_name" name="legal_name" value="<?= e((string) setting('legal_name')) ?>">
    <p class="form-hint">The exact business name registered with Google Business Profile and the Punjab Food Authority. Used in the LocalBusiness schema as an authority signal.</p>
  </div>
  <div class="form-group">
    <label for="footer_about">Short company description (footer)</label>
    <textarea id="footer_about" name="footer_about" style="min-height:100px;"><?= e((string) setting('footer_about')) ?></textarea>
  </div>

  <div class="a-card-head" style="margin-top:22px;"><h2>Images</h2></div>
  <p class="form-hint" style="margin-bottom:14px;">
    Upload your logo here and it appears in the header, footer, emails, agreements and quotations. WebP or PNG with a
    transparent background works best.
  </p>
  <div class="form-row">
    <div><?php image_field('logo_path', (string) setting('logo_path'), 'Main logo', 'Around 400x120 pixels.'); ?></div>
    <div><?php image_field('logo_dark_path', (string) setting('logo_dark_path'), 'Dark mode logo (optional)'); ?></div>
    <div><?php image_field('favicon_path', (string) setting('favicon_path'), 'Favicon', '512x512 PNG.'); ?></div>
    <div><?php image_field('og_image', (string) setting('og_image'), 'Social sharing image', '1200x630 JPG or PNG.'); ?></div>
    <div><?php image_field('hero_image', (string) setting('hero_image'), 'Home page hero image', 'Your bottle photo, transparent background ideally.'); ?></div>
    <div><?php image_field('about_image', (string) setting('about_image'), 'About page image'); ?></div>
    <div><?php image_field('custom_label_image', (string) setting('custom_label_image'), 'Custom label page image'); ?></div>
    <div><?php image_field('distribution_image', (string) setting('distribution_image'), 'Distribution page image'); ?></div>
  </div>

  <div class="a-card-head" style="margin-top:22px;"><h2>Licensing and operations</h2></div>
  <div class="form-row">
    <div class="form-group">
      <label for="license_authority">Licensing authority</label>
      <input type="text" id="license_authority" name="license_authority" value="<?= e((string) setting('license_authority')) ?>">
    </div>
    <div class="form-group">
      <label for="license_number">Licence number</label>
      <input type="text" id="license_number" name="license_number" value="<?= e((string) setting('license_number')) ?>">
    </div>
    <div class="form-group">
      <label for="psqca_number">PSQCA reference</label>
      <input type="text" id="psqca_number" name="psqca_number" value="<?= e((string) setting('psqca_number')) ?>">
    </div>
    <div class="form-group">
      <label for="per_litre_rate">Bulk filling rate per litre (PKR)</label>
      <input type="number" step="0.01" id="per_litre_rate" name="per_litre_rate" value="<?= e((string) setting('per_litre_rate')) ?>">
      <span class="form-hint">Drives the bulk water calculator.</span>
    </div>
    <div class="form-group">
      <label for="hours_display">Opening hours (display text)</label>
      <input type="text" id="hours_display" name="hours_display" value="<?= e((string) setting('hours_display')) ?>">
    </div>
    <div class="form-group">
      <label for="hours_open">Opens (24h, for schema)</label>
      <input type="text" id="hours_open" name="hours_open" value="<?= e((string) setting('hours_open')) ?>" placeholder="08:00">
    </div>
    <div class="form-group">
      <label for="hours_close">Closes (24h, for schema)</label>
      <input type="text" id="hours_close" name="hours_close" value="<?= e((string) setting('hours_close')) ?>" placeholder="21:00">
    </div>
  </div>
  <button class="btn btn-primary" type="submit">Save general settings</button>
</form>

<?php elseif ($tab === 'contact'): ?>
<form method="post" class="a-card">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="save_contact">
  <div class="a-card-head"><h2>Contact details</h2></div>
  <div class="form-row">
    <div class="form-group">
      <label for="contact_email">Main email address</label>
      <input type="email" id="contact_email" name="contact_email" value="<?= e(contact_email()) ?>">
    </div>
    <div class="form-group">
      <label for="sales_email">Sales email address</label>
      <input type="email" id="sales_email" name="sales_email" value="<?= e((string) setting('sales_email')) ?>">
    </div>
    <div class="form-group">
      <label for="phone_display">Phone number (display)</label>
      <input type="text" id="phone_display" name="phone_display" value="<?= e((string) setting('phone_display')) ?>">
      <span class="form-hint">WhatsApp numbers are managed separately under WhatsApp Numbers.</span>
    </div>
    <div class="form-group">
      <label for="address_street">Street address</label>
      <input type="text" id="address_street" name="address_street" value="<?= e((string) setting('address_street')) ?>">
    </div>
    <div class="form-group">
      <label for="address_city">City</label>
      <input type="text" id="address_city" name="address_city" value="<?= e((string) setting('address_city')) ?>">
    </div>
    <div class="form-group">
      <label for="address_region">Province</label>
      <input type="text" id="address_region" name="address_region" value="<?= e((string) setting('address_region')) ?>">
    </div>
    <div class="form-group">
      <label for="address_postal">Postal code</label>
      <input type="text" id="address_postal" name="address_postal" value="<?= e((string) setting('address_postal')) ?>">
    </div>
  </div>
  <div class="form-group">
    <label for="address_full">Full address (shown in the footer and on documents)</label>
    <textarea id="address_full" name="address_full" style="min-height:70px;"><?= e((string) setting('address_full')) ?></textarea>
  </div>

  <div class="a-card-head" style="margin-top:22px;"><h2>Google Business Profile</h2></div>
  <p class="form-hint" style="margin-bottom:12px;">Paste the public link to your verified <strong>Pak-Everests</strong> Google Business Profile / Maps listing. This is the single strongest local-SEO authority signal — it ties the website to your verified business in Google's Knowledge Graph, drives the LocalBusiness <code>sameAs</code> and <code>hasMap</code> schema, and is referenced in llms.txt for AI answer engines.</p>
  <div class="form-group">
    <label for="google_business_url">Google Business Profile / Maps URL</label>
    <input type="url" id="google_business_url" name="google_business_url" value="<?= e((string) setting('google_business_url')) ?>" placeholder="https://maps.google.com/... or https://g.page/...">
    <p class="form-hint">Open Google Maps, find your business, click Share, and copy the link. A short <code>g.page</code>, <code>maps.app.goo.gl</code> or full Maps URL all work.</p>
  </div>

  <div class="a-card-head" style="margin-top:22px;"><h2>Social profiles</h2></div>
  <p class="form-hint" style="margin-bottom:12px;">Leave a field blank to hide that icon in the footer. These URLs also feed the sameAs property in your business schema.</p>
  <div class="form-row">
    <?php foreach ([
      'social_facebook' => 'Facebook page URL', 'social_instagram' => 'Instagram profile URL',
      'social_youtube' => 'YouTube channel URL', 'social_tiktok' => 'TikTok profile URL',
      'social_linkedin' => 'LinkedIn page URL', 'social_twitter' => 'X (Twitter) profile URL',
      'social_whatsapp_channel' => 'WhatsApp channel URL',
    ] as $key => $label): ?>
    <div class="form-group">
      <label for="<?= e($key) ?>"><?= e($label) ?></label>
      <input type="url" id="<?= e($key) ?>" name="<?= e($key) ?>" value="<?= e((string) setting($key)) ?>" placeholder="https://">
    </div>
    <?php endforeach; ?>
  </div>
  <button class="btn btn-primary" type="submit">Save contact settings</button>
</form>

<?php elseif ($tab === 'seo'): ?>
<form method="post" class="a-card">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="save_seo">
  <div class="a-card-head"><h2>Home page and default meta</h2></div>
  <div class="form-group">
    <label for="meta_title">Home page title</label>
    <input type="text" id="meta_title" name="meta_title" value="<?= e((string) setting('meta_title')) ?>" data-counter="60">
  </div>
  <div class="form-group">
    <label for="meta_description">Default meta description</label>
    <textarea id="meta_description" name="meta_description" data-counter="160" style="min-height:90px;"><?= e((string) setting('meta_description')) ?></textarea>
  </div>
  <div class="form-group">
    <label for="meta_keywords">Target keywords</label>
    <textarea id="meta_keywords" name="meta_keywords" style="min-height:80px;"><?= e((string) setting('meta_keywords')) ?></textarea>
    <span class="form-hint">Comma separated. Used as the fallback keywords meta tag on pages without their own.</span>
  </div>

  <div class="a-card-head" style="margin-top:22px;"><h2>Search engine verification</h2></div>
  <div class="alert alert-info">
    <strong>Google Search Console setup:</strong> choose the <em>HTML tag</em> verification method, copy only the
    <code>content="..."</code> value and paste it below. Then submit
    <a href="<?= e(url('sitemap.xml')) ?>" target="_blank" rel="noopener"><?= e(SITE_URL) ?>/sitemap.xml</a> as your sitemap.
  </div>
  <div class="form-row">
    <div class="form-group">
      <label for="google_site_verification">Google Search Console verification code</label>
      <input type="text" id="google_site_verification" name="google_site_verification" value="<?= e((string) setting('google_site_verification')) ?>">
    </div>
    <div class="form-group">
      <label for="bing_site_verification">Bing Webmaster verification code</label>
      <input type="text" id="bing_site_verification" name="bing_site_verification" value="<?= e((string) setting('bing_site_verification')) ?>">
    </div>
    <div class="form-group">
      <label for="yandex_verification">Yandex verification code</label>
      <input type="text" id="yandex_verification" name="yandex_verification" value="<?= e((string) setting('yandex_verification')) ?>">
    </div>
    <div class="form-group">
      <label for="pinterest_verification">Pinterest verification code</label>
      <input type="text" id="pinterest_verification" name="pinterest_verification" value="<?= e((string) setting('pinterest_verification')) ?>">
    </div>
  </div>

  <div class="a-card-head" style="margin-top:22px;"><h2>robots.txt</h2></div>
  <div class="form-group">
    <label for="robots_txt">Custom robots.txt (leave blank to use the sensible default)</label>
    <textarea id="robots_txt" name="robots_txt" class="code" style="min-height:170px;"><?= e((string) setting('robots_txt')) ?></textarea>
    <span class="form-hint">Live at <a href="<?= e(url('robots.txt')) ?>" target="_blank" rel="noopener"><?= e(SITE_URL) ?>/robots.txt</a></span>
  </div>
  <label class="check"><input type="checkbox" name="sitemap_enabled" value="1" <?= setting_bool('sitemap_enabled', true) ? 'checked' : '' ?>> Keep the XML sitemap enabled</label>

  <div class="a-card-head" style="margin-top:22px;"><h2>Custom code injection</h2></div>
  <div class="form-group">
    <label for="custom_head_code">Code inserted before &lt;/head&gt;</label>
    <textarea id="custom_head_code" name="custom_head_code" class="code" style="min-height:130px;"><?= e((string) setting('custom_head_code')) ?></textarea>
    <span class="form-hint">Verification tags, fonts or any third party script. Only paste code you trust.</span>
  </div>
  <div class="form-group">
    <label for="custom_body_code">Code inserted after &lt;body&gt;</label>
    <textarea id="custom_body_code" name="custom_body_code" class="code" style="min-height:130px;"><?= e((string) setting('custom_body_code')) ?></textarea>
  </div>
  <button class="btn btn-primary" type="submit">Save SEO settings</button>
</form>

<form method="post" class="a-card">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="indexnow_ping_all">
  <div class="a-card-head"><h2>Submit all pages to search engines</h2></div>
  <?php $inKey = indexnow_key(); $inKeyUrl = SITE_URL . '/' . $inKey . '.txt'; ?>
  <div class="alert alert-success" style="margin-bottom:14px;">
    <strong>✓ IndexNow is active.</strong> New and updated pages are submitted to search engines automatically.
  </div>
  <div class="form-group">
    <label for="indexnow_key_display">Your IndexNow key</label>
    <input type="text" id="indexnow_key_display" value="<?= e($inKey) ?>" readonly onclick="this.select();" style="font-family:monospace;">
    <span class="form-hint">
      Generated automatically and stored for this site. Confirm it is live by opening the key file — it must show exactly this key:
      <a href="<?= e($inKeyUrl) ?>" target="_blank" rel="noopener"><?= e($inKeyUrl) ?></a>
    </span>
  </div>
  <p class="form-hint" style="margin-bottom:12px;">
    The button below instantly notifies <strong>Bing, Yandex</strong> and other IndexNow search engines about every published page
    (<?= (int) count(all_indexable_urls()) ?> URLs) — use it to push the whole site at once, for example after a big content update.
  </p>
  <div class="alert alert-info" style="margin-bottom:14px;">
    <strong>For Google:</strong> Google does not accept instant pings. Open
    <a href="https://search.google.com/search-console" target="_blank" rel="noopener">Search Console</a>,
    use <em>URL Inspection → Request Indexing</em> for your key pages (about 10–12 per day), and make sure
    <a href="<?= e(url('sitemap.xml')) ?>" target="_blank" rel="noopener">/sitemap.xml</a> is submitted.
  </div>
  <button class="btn btn-primary" type="submit">Submit all pages to IndexNow now</button>
</form>

<?php elseif ($tab === 'maps'): ?>
<form method="post" class="a-card">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="save_maps">
  <div class="a-card-head"><h2>Google Maps</h2></div>
  <div class="alert alert-info">
    <strong>How to get your embed code:</strong> open Google Maps, search for your plant location, click
    <em>Share</em> &rarr; <em>Embed a map</em>, copy the whole <code>&lt;iframe&gt;</code> and paste it below. The map
    then appears on the home page, the contact page and the coverage page.
  </div>
  <div class="form-group">
    <label for="map_embed_code">Map embed code</label>
    <textarea id="map_embed_code" name="map_embed_code" class="code" style="min-height:150px;"><?= e((string) setting('map_embed_code')) ?></textarea>
  </div>
  <div class="form-row">
    <div class="form-group">
      <label for="map_lat">Latitude</label>
      <input type="text" id="map_lat" name="map_lat" value="<?= e((string) setting('map_lat')) ?>">
      <span class="form-hint">Used in your LocalBusiness schema for local search.</span>
    </div>
    <div class="form-group">
      <label for="map_lng">Longitude</label>
      <input type="text" id="map_lng" name="map_lng" value="<?= e((string) setting('map_lng')) ?>">
    </div>
    <div class="form-group">
      <label for="map_zoom">Default zoom</label>
      <input type="number" id="map_zoom" name="map_zoom" value="<?= e((string) setting('map_zoom')) ?>">
    </div>
    <div class="form-group">
      <label for="map_directions_url">Get directions URL</label>
      <input type="url" id="map_directions_url" name="map_directions_url" value="<?= e((string) setting('map_directions_url')) ?>">
    </div>
  </div>
  <button class="btn btn-primary" type="submit">Save map settings</button>
</form>

<div class="a-card">
  <div class="a-card-head"><h2>Current map preview</h2></div>
  <div class="map-embed" style="border-radius:12px;overflow:hidden;">
    <?php require PE_ROOT . '/app/partials/map.php'; ?>
  </div>
</div>

<?php elseif ($tab === 'payment'): ?>
<form method="post" class="a-card">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="save_payment">
  <p class="form-hint" style="margin-bottom:16px;">
    These details are shown to customers on the contact page, on the order form and on every quotation.
  </p>

  <div class="a-card-head"><h2>Cash on delivery</h2></div>
  <label class="check"><input type="checkbox" name="payment_cod_enabled" value="1" <?= setting_bool('payment_cod_enabled', true) ? 'checked' : '' ?>> Offer cash on delivery</label>
  <div class="form-group">
    <label for="payment_note">Payment note shown to customers</label>
    <textarea id="payment_note" name="payment_note" style="min-height:80px;"><?= e((string) setting('payment_note')) ?></textarea>
  </div>

  <div class="a-card-head" style="margin-top:22px;"><h2>EasyPaisa</h2></div>
  <label class="check"><input type="checkbox" name="easypaisa_enabled" value="1" <?= setting_bool('easypaisa_enabled', true) ? 'checked' : '' ?>> Show EasyPaisa details</label>
  <div class="form-row">
    <div class="form-group">
      <label for="easypaisa_title">Account title</label>
      <input type="text" id="easypaisa_title" name="easypaisa_title" value="<?= e((string) setting('easypaisa_title')) ?>">
    </div>
    <div class="form-group">
      <label for="easypaisa_number">Account number</label>
      <input type="text" id="easypaisa_number" name="easypaisa_number" value="<?= e((string) setting('easypaisa_number')) ?>">
    </div>
  </div>

  <div class="a-card-head" style="margin-top:22px;"><h2>JazzCash</h2></div>
  <label class="check"><input type="checkbox" name="jazzcash_enabled" value="1" <?= setting_bool('jazzcash_enabled', true) ? 'checked' : '' ?>> Show JazzCash details</label>
  <div class="form-row">
    <div class="form-group">
      <label for="jazzcash_title">Account title</label>
      <input type="text" id="jazzcash_title" name="jazzcash_title" value="<?= e((string) setting('jazzcash_title')) ?>">
    </div>
    <div class="form-group">
      <label for="jazzcash_number">Account number</label>
      <input type="text" id="jazzcash_number" name="jazzcash_number" value="<?= e((string) setting('jazzcash_number')) ?>">
    </div>
  </div>

  <div class="a-card-head" style="margin-top:22px;"><h2>Bank account</h2></div>
  <label class="check"><input type="checkbox" name="bank_enabled" value="1" <?= setting_bool('bank_enabled', true) ? 'checked' : '' ?>> Show bank transfer details</label>
  <div class="form-row">
    <div class="form-group">
      <label for="bank_name">Bank name</label>
      <input type="text" id="bank_name" name="bank_name" value="<?= e((string) setting('bank_name')) ?>">
    </div>
    <div class="form-group">
      <label for="bank_branch">Branch</label>
      <input type="text" id="bank_branch" name="bank_branch" value="<?= e((string) setting('bank_branch')) ?>">
    </div>
    <div class="form-group">
      <label for="bank_account_title">Account title</label>
      <input type="text" id="bank_account_title" name="bank_account_title" value="<?= e((string) setting('bank_account_title')) ?>">
    </div>
    <div class="form-group">
      <label for="bank_account_number">Account number</label>
      <input type="text" id="bank_account_number" name="bank_account_number" value="<?= e((string) setting('bank_account_number')) ?>">
    </div>
    <div class="form-group">
      <label for="bank_iban">IBAN</label>
      <input type="text" id="bank_iban" name="bank_iban" value="<?= e((string) setting('bank_iban')) ?>">
    </div>
    <div class="form-group">
      <label for="bank_swift">SWIFT code</label>
      <input type="text" id="bank_swift" name="bank_swift" value="<?= e((string) setting('bank_swift')) ?>">
    </div>
  </div>
  <button class="btn btn-primary" type="submit">Save payment details</button>
</form>

<?php elseif ($tab === 'smtp'): ?>
<form method="post" class="a-card">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="save_smtp">
  <div class="a-card-head"><h2>Outgoing email (SMTP)</h2></div>
  <div class="alert alert-info">
    <strong>Hostinger email:</strong> create the mailbox <code>info@pakeverests.site</code> in hPanel under
    Emails, then use host <code>smtp.hostinger.com</code>, port <code>465</code> with SSL, and the mailbox address and
    password as the username and password. Without SMTP the site falls back to the PHP mail() function, which is far
    more likely to land in spam.
  </div>
  <label class="check"><input type="checkbox" name="smtp_enabled" value="1" <?= setting_bool('smtp_enabled') ? 'checked' : '' ?>> Send email through SMTP</label>
  <div class="form-row">
    <div class="form-group">
      <label for="smtp_host">SMTP host</label>
      <input type="text" id="smtp_host" name="smtp_host" value="<?= e((string) setting('smtp_host')) ?>">
    </div>
    <div class="form-group">
      <label for="smtp_port">Port</label>
      <input type="number" id="smtp_port" name="smtp_port" value="<?= e((string) setting('smtp_port')) ?>">
    </div>
    <div class="form-group">
      <label for="smtp_encryption">Encryption</label>
      <select id="smtp_encryption" name="smtp_encryption">
        <?php foreach (['ssl' => 'SSL (port 465)', 'tls' => 'STARTTLS (port 587)', 'none' => 'None'] as $k => $v): ?>
        <option value="<?= e($k) ?>" <?= setting('smtp_encryption') === $k ? 'selected' : '' ?>><?= e($v) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label for="smtp_username">Username</label>
      <input type="text" id="smtp_username" name="smtp_username" value="<?= e((string) setting('smtp_username')) ?>">
    </div>
    <div class="form-group">
      <label for="smtp_password">Password</label>
      <input type="password" id="smtp_password" name="smtp_password" placeholder="<?= setting('smtp_password') ? 'Saved — leave blank to keep it' : 'Enter the mailbox password' ?>" autocomplete="new-password">
    </div>
    <div class="form-group">
      <label for="smtp_from_email">From address</label>
      <input type="email" id="smtp_from_email" name="smtp_from_email" value="<?= e((string) setting('smtp_from_email')) ?>">
    </div>
    <div class="form-group">
      <label for="smtp_from_name">From name</label>
      <input type="text" id="smtp_from_name" name="smtp_from_name" value="<?= e((string) setting('smtp_from_name')) ?>">
    </div>
  </div>
  <button class="btn btn-primary" type="submit">Save SMTP settings</button>
</form>

<div class="a-card">
  <div class="a-card-head"><h2>Send a test email</h2></div>
  <form method="post" class="filter-form">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="test_smtp">
    <div class="form-group" style="flex:1;min-width:240px;">
      <label for="test_email">Send a test message to</label>
      <input type="email" id="test_email" name="test_email" value="<?= e(contact_email()) ?>">
    </div>
    <button class="btn btn-ok" type="submit">Send test email</button>
  </form>
</div>

<div class="a-card">
  <div class="a-card-head"><h2>Recent email log</h2></div>
  <?php $log = fetch_all('SELECT * FROM email_log ORDER BY id DESC LIMIT 15'); ?>
  <?php if (!$log): ?>
    <div class="empty">No emails sent yet.</div>
  <?php else: ?>
  <div class="a-table-wrap">
    <table class="a-table">
      <thead><tr><th>Time</th><th>Recipient</th><th>Subject</th><th>Type</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($log as $l): ?>
        <tr>
          <td><small><?= pretty_date($l['created_at'], 'd M, H:i') ?></small></td>
          <td><small><?= e($l['recipient']) ?></small></td>
          <td><small><?= e((string) $l['subject']) ?></small></td>
          <td><small><?= e((string) $l['context']) ?></small></td>
          <td><?= status_pill($l['status']) ?>
            <?php if ($l['status'] === 'failed' && $l['error']): ?>
              <br><small style="color:var(--a-bad);"><?= e(substr((string) $l['error'], 0, 120)) ?></small>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php elseif ($tab === 'analytics'): ?>
<form method="post" class="a-card">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="save_analytics">
  <div class="a-card-head"><h2>Third party tracking</h2></div>
  <div class="form-row">
    <div class="form-group">
      <label for="google_analytics_id">Google Analytics 4 measurement ID</label>
      <input type="text" id="google_analytics_id" name="google_analytics_id" value="<?= e((string) setting('google_analytics_id')) ?>" placeholder="G-XXXXXXXXXX">
    </div>
    <div class="form-group">
      <label for="google_tag_manager_id">Google Tag Manager container ID</label>
      <input type="text" id="google_tag_manager_id" name="google_tag_manager_id" value="<?= e((string) setting('google_tag_manager_id')) ?>" placeholder="GTM-XXXXXXX">
    </div>
    <div class="form-group">
      <label for="meta_pixel_id">Meta (Facebook) Pixel ID</label>
      <input type="text" id="meta_pixel_id" name="meta_pixel_id" value="<?= e((string) setting('meta_pixel_id')) ?>">
    </div>
    <div class="form-group">
      <label for="tiktok_pixel_id">TikTok Pixel ID</label>
      <input type="text" id="tiktok_pixel_id" name="tiktok_pixel_id" value="<?= e((string) setting('tiktok_pixel_id')) ?>">
    </div>
  </div>

  <div class="a-card-head" style="margin-top:22px;"><h2>Built-in analytics</h2></div>
  <label class="check"><input type="checkbox" name="analytics_tracking_enabled" value="1" <?= setting_bool('analytics_tracking_enabled', true) ? 'checked' : '' ?>> Record page views in the admin analytics dashboard</label>
  <label class="check"><input type="checkbox" name="geo_lookup_enabled" value="1" <?= setting_bool('geo_lookup_enabled', true) ? 'checked' : '' ?>> Resolve visitor country, region and city from the IP address</label>
  <label class="check"><input type="checkbox" name="analytics_track_bots" value="1" <?= setting_bool('analytics_track_bots') ? 'checked' : '' ?>> Also record search engine crawlers (not recommended, it inflates the numbers)</label>
  <button class="btn btn-primary" type="submit">Save analytics settings</button>
</form>

<?php else: ?>
<form method="post" class="a-card">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="save_website">
  <div class="a-card-head"><h2>Website behaviour</h2></div>
  <label class="check"><input type="checkbox" name="orders_enabled" value="1" <?= setting_bool('orders_enabled', true) ? 'checked' : '' ?>> Accept orders through the online order form</label>
  <label class="check"><input type="checkbox" name="reviews_open" value="1" <?= setting_bool('reviews_open', true) ? 'checked' : '' ?>> Allow customers to submit reviews</label>
  <label class="check"><input type="checkbox" name="whatsapp_float_enabled" value="1" <?= setting_bool('whatsapp_float_enabled', true) ? 'checked' : '' ?>> Show the floating WhatsApp button</label>

  <div class="form-row" style="margin-top:14px;">
    <div class="form-group">
      <label for="default_theme">Default colour theme for new visitors</label>
      <select id="default_theme" name="default_theme">
        <option value="light" <?= setting('default_theme') === 'light' ? 'selected' : '' ?>>Light</option>
        <option value="dark" <?= setting('default_theme') === 'dark' ? 'selected' : '' ?>>Dark</option>
      </select>
      <span class="form-hint">Visitors can always switch using the toggle in the header.</span>
    </div>
    <div class="form-group">
      <label for="free_delivery_note">Free delivery note</label>
      <input type="text" id="free_delivery_note" name="free_delivery_note" value="<?= e((string) setting('free_delivery_note')) ?>">
    </div>
  </div>

  <div class="form-group">
    <label for="announcement_bar">Announcement bar (leave blank to hide)</label>
    <input type="text" id="announcement_bar" name="announcement_bar" value="<?= e((string) setting('announcement_bar')) ?>"
           placeholder="e.g. Eid holidays: deliveries resume on 12 April">
    <span class="form-hint">Appears as a coloured strip above the top bar on every page.</span>
  </div>

  <div class="a-card-head" style="margin-top:22px;"><h2>Maintenance mode</h2></div>
  <div class="alert alert-warning">
    Maintenance mode hides the whole public website behind a holding page with your WhatsApp number. You stay signed in
    to the admin panel and can still see the site. Use it only while making major changes.
  </div>
  <label class="check"><input type="checkbox" name="maintenance_mode" value="1" <?= setting_bool('maintenance_mode') ? 'checked' : '' ?>> Put the website into maintenance mode</label>

  <button class="btn btn-primary" type="submit">Save website settings</button>
</form>
<?php endif; ?>

<?php admin_footer(); ?>
