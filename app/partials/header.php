<?php
/**
 * Public site header: <head>, top bar, navigation, news ticker.
 * Views call seo_set() before including this file.
 */

declare(strict_types=1);

$seoTitle   = seo_title();
$seoDesc    = (string) seo_get('description');
$canonical  = (string) seo_get('canonical', current_canonical());
$ogImage    = abs_url((string) seo_get('image', '/assets/img/og-default.jpg'));
$crumbs     = seo_get('breadcrumbs', []);

/* Base schema on every page, plus whatever the view added. */
$schema = seo_get('schema', []);
array_unshift($schema, schema_organization(), schema_website());
if ($crumbs) {
    $schema[] = schema_breadcrumbs($crumbs);
}
$GLOBALS['seo']['schema'] = $schema;

track_pageview((string) seo_get('title', ''));

$navProducts = fetch_all('SELECT slug, short_name, name FROM products WHERE status = "published" ORDER BY sort_order ASC');
$legalPages  = nav_pages('legal');
$theme       = (string) setting('default_theme', 'light');
$logo        = media_url((string) setting('logo_path'), 'logo');
$logoDarkRaw = trim((string) setting('logo_dark_path'));
$logoDark    = ($logoDarkRaw !== '' && is_file(PE_ROOT . '/' . ltrim($logoDarkRaw, '/'))) ? media_url($logoDarkRaw, 'logo') : '';
?>
<!DOCTYPE html>
<html lang="en-PK" data-theme="<?= e($theme) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#0b6fa4" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#06283a" media="(prefers-color-scheme: dark)">

<title><?= e($seoTitle) ?></title>
<meta name="description" content="<?= e($seoDesc) ?>">
<meta name="keywords" content="<?= e((string) seo_get('keywords')) ?>">
<meta name="robots" content="<?= e((string) seo_get('robots')) ?>">
<meta name="googlebot" content="<?= e((string) seo_get('robots')) ?>">
<meta name="author" content="<?= e(site_name()) ?>">
<meta name="publisher" content="<?= e(site_name()) ?>">
<meta name="geo.region" content="PK-PB">
<meta name="geo.placename" content="<?= e((string) setting('address_city', 'Gujar Khan')) ?>">
<meta name="geo.position" content="<?= e((string) setting('map_lat', '33.2544')) ?>;<?= e((string) setting('map_lng', '73.3047')) ?>">
<meta name="ICBM" content="<?= e((string) setting('map_lat', '33.2544')) ?>, <?= e((string) setting('map_lng', '73.3047')) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">

<!-- Open Graph -->
<meta property="og:site_name" content="<?= e(site_name()) ?>">
<meta property="og:type" content="<?= e((string) seo_get('og_type', 'website')) ?>">
<meta property="og:locale" content="en_PK">
<meta property="og:title" content="<?= e($seoTitle) ?>">
<meta property="og:description" content="<?= e($seoDesc) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="<?= e(site_name()) ?>">
<?php if (seo_get('published_at')): ?>
<meta property="article:published_time" content="<?= e(date('c', strtotime((string) seo_get('published_at')))) ?>">
<?php endif; ?>
<?php if (seo_get('modified_at')): ?>
<meta property="article:modified_time" content="<?= e(date('c', strtotime((string) seo_get('modified_at')))) ?>">
<?php endif; ?>

<!-- Twitter card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($seoTitle) ?>">
<meta name="twitter:description" content="<?= e($seoDesc) ?>">
<meta name="twitter:image" content="<?= e($ogImage) ?>">

<?php if ($v = setting('google_site_verification')): ?>
<meta name="google-site-verification" content="<?= e($v) ?>">
<?php endif; ?>
<?php if ($v = setting('bing_site_verification')): ?>
<meta name="msvalidate.01" content="<?= e($v) ?>">
<?php endif; ?>
<?php if ($v = setting('yandex_verification')): ?>
<meta name="yandex-verification" content="<?= e($v) ?>">
<?php endif; ?>
<?php if ($v = setting('pinterest_verification')): ?>
<meta name="p:domain_verify" content="<?= e($v) ?>">
<?php endif; ?>

<link rel="icon" href="<?= e(media_url((string) setting('favicon_path'), 'logo')) ?>">
<link rel="apple-touch-icon" href="<?= e(media_url((string) setting('favicon_path'), 'logo')) ?>">
<?php /* Warm up connections only to third parties actually loaded on this page. */ ?>
<?php if (setting('google_analytics_id') || setting('google_tag_manager_id') || setting('meta_pixel_id')): ?>
<link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>
<?php endif; ?>
<?php if (setting_bool('ads_enabled') && setting('adsense_publisher_id')): ?>
<link rel="preconnect" href="https://pagead2.googlesyndication.com" crossorigin>
<?php endif; ?>
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">

<script>
/* Apply the saved theme before first paint so there is no flash. */
(function () {
  document.documentElement.className += ' js';
  try {
    var saved = localStorage.getItem('pe_theme');
    if (saved) { document.documentElement.setAttribute('data-theme', saved); }
  } catch (e) {}
})();
</script>

<?= seo_render_schema() ?>

<?php if ($gaId = setting('google_analytics_id')): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($gaId) ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', <?= ejs($gaId) ?>);
</script>
<?php endif; ?>

<?php if ($gtm = setting('google_tag_manager_id')): ?>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});
var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;
j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer',<?= ejs($gtm) ?>);</script>
<?php endif; ?>

<?php if ($pixel = setting('meta_pixel_id')): ?>
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', <?= ejs($pixel) ?>); fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none" alt="" src="https://www.facebook.com/tr?id=<?= e($pixel) ?>&ev=PageView&noscript=1"></noscript>
<?php endif; ?>

<?php if (setting_bool('ads_enabled') && ($pub = setting('adsense_publisher_id'))): ?>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?= e($pub) ?>" crossorigin="anonymous"></script>
<?php endif; ?>

<?= setting('custom_head_code', '') ?>
</head>
<body class="<?= e(seo_get('body_class', '')) ?>">
<?= setting('custom_body_code', '') ?>

<a class="skip-link" href="#main">Skip to main content</a>

<div class="pe-page">

<?php if ($bar = setting('announcement_bar')): ?>
<div class="announcement-bar"><div class="container"><?= e($bar) ?></div></div>
<?php endif; ?>

<!-- ================= Top bar ================= -->
<div class="topbar">
  <div class="container topbar-inner">
    <div class="topbar-contact">
      <a href="mailto:<?= e(contact_email()) ?>" class="topbar-link">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v16H4z" fill="none"/><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5z"/></svg>
        <span><?= e(contact_email()) ?></span>
      </a>
      <?php foreach (whatsapp_numbers() as $wn): ?>
      <a href="<?= e(wa_link($wn['number'], 'Hello Pak-Everests, I would like to order water.')) ?>" class="topbar-link" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm5.8 14.13c-.24.68-1.4 1.3-1.94 1.34-.5.05-.98.23-3.3-.69-2.78-1.1-4.55-3.94-4.69-4.12-.14-.18-1.12-1.49-1.12-2.85s.71-2.02.97-2.3c.25-.27.55-.34.73-.34.18 0 .37 0 .53.01.17.01.4-.06.62.48.24.57.8 1.96.87 2.1.07.14.12.3.02.48-.09.18-.14.3-.28.46-.14.16-.3.36-.42.48-.14.14-.29.29-.12.57.16.27.73 1.2 1.56 1.95 1.07.95 1.97 1.25 2.25 1.39.27.14.43.12.59-.07.16-.18.68-.79.86-1.06.18-.27.36-.23.61-.14.24.09 1.55.73 1.82.86.27.14.45.2.51.32.07.11.07.64-.17 1.32z"/></svg>
        <span><?= e($wn['number']) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
    <div class="topbar-meta">
      <span class="topbar-badge">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1 3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 15-4-4 1.41-1.41L11 13.17l5.59-5.59L18 9l-7 7z"/></svg>
        <?= e((string) setting('license_authority', 'Punjab Food Authority')) ?> Approved
      </span>
      <span class="topbar-hours"><?= e((string) setting('hours_display', 'Open 8:00 AM - 9:00 PM')) ?></span>
    </div>
  </div>
</div>

<!-- ================= Header ================= -->
<header class="site-header" id="siteHeader">
  <div class="container header-inner">
    <a class="brand" href="<?= e(url('/')) ?>" aria-label="<?= e(site_name()) ?> home">
      <img src="<?= e($logo) ?>" alt="<?= e(site_name()) ?> logo" class="brand-logo<?= $logoDark ? ' logo-light-only' : '' ?>" width="200" height="60">
      <?php if ($logoDark): ?>
      <img src="<?= e($logoDark) ?>" alt="" class="brand-logo logo-dark-only" width="200" height="60" aria-hidden="true">
      <?php endif; ?>
      <span class="sr-only"><?= e(site_name()) ?> — <?= e(site_tagline()) ?></span>
    </a>

    <nav class="main-nav" aria-label="Main navigation">
      <ul class="nav-list">
        <?php require PE_ROOT . '/app/partials/nav-items.php'; ?>
      </ul>
    </nav>

    <div class="header-actions">
      <button class="theme-toggle" id="themeToggle" type="button" aria-label="Switch between light and dark mode" title="Toggle light and dark mode">
        <svg class="icon-sun" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0-5v3m0 14v3M2 12h3m14 0h3M4.2 4.2l2.1 2.1m11.4 11.4 2.1 2.1M19.8 4.2l-2.1 2.1M6.3 17.7l-2.1 2.1"/></svg>
        <svg class="icon-moon" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
      </button>
      <a class="btn btn-primary btn-order" href="<?= e(url('order')) ?>">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2c-3.2 4.4-6 7.6-6 11a6 6 0 0 0 12 0c0-3.4-2.8-6.6-6-11z"/></svg>
        Order Now
      </a>
      <button class="nav-toggle" id="navToggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobileNav">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<?php if (setting_bool('ticker_enabled', true) && ($tickerItems = ticker_items())): ?>
<div class="news-ticker" style="--ticker-bg:<?= e((string) setting('ticker_bg', '#0b6fa4')) ?>;--ticker-color:<?= e((string) setting('ticker_color', '#ffffff')) ?>;--ticker-highlight:<?= e((string) setting('ticker_highlight_color', '#7fe3ff')) ?>;--ticker-size:<?= (int) setting('ticker_font_size', '14') ?>px;--ticker-weight:<?= (int) setting('ticker_font_weight', '600') ?>;--ticker-family:<?= e((string) setting('ticker_font_family', 'inherit')) ?>;--ticker-duration:<?= (int) setting('ticker_speed', '45') ?>s;">
  <div class="ticker-label"><?= e((string) setting('ticker_label', 'LATEST')) ?></div>
  <div class="ticker-viewport<?= setting_bool('ticker_pause_on_hover', true) ? ' pause-on-hover' : '' ?>">
    <div class="ticker-track">
      <?php for ($pass = 0; $pass < 2; $pass++): ?>
        <?php foreach ($tickerItems as $item): ?>
        <span class="ticker-item<?= (int) $item['highlight'] === 1 ? ' is-highlight' : '' ?>"<?= $pass === 1 ? ' aria-hidden="true"' : '' ?>>
          <span class="ticker-dot" aria-hidden="true"></span>
          <?php if (!empty($item['link'])): ?>
            <a href="<?= e($item['link']) ?>"><?= e($item['text']) ?></a>
          <?php else: ?>
            <?= e($item['text']) ?>
          <?php endif; ?>
        </span>
        <?php endforeach; ?>
      <?php endfor; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<?= ad_slot('header') ?>

<main id="main">
<?= flash_render() ?>
