<?php
/** Public site footer: newsletter, link columns, social, contact, scripts. */
declare(strict_types=1);

$footerProducts = fetch_all('SELECT slug, short_name, name FROM products WHERE status = "published" ORDER BY sort_order ASC LIMIT 10');
$footerLegal    = nav_pages('legal');
$footerCompany  = nav_pages('company');
$areas          = coverage_areas();
?>
</main>

<?= ad_slot('footer') ?>

<!-- ================= Newsletter ================= -->
<section class="newsletter" id="subscribe" aria-labelledby="subscribe-heading">
  <div class="container newsletter-inner">
    <div class="newsletter-copy">
      <h2 id="subscribe-heading">Stay updated with Pak-Everests</h2>
      <p>Get new product launches, seasonal offers and water care tips delivered to your inbox. No spam, and you can unsubscribe any time.</p>
    </div>
    <form class="newsletter-form" method="post" action="<?= e(url('')) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="form_type" value="subscribe">
      <input type="hidden" name="subscribe_source" value="footer">
      <div class="hp-field" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
      <label class="sr-only" for="subscribeEmail">Email address</label>
      <input type="email" id="subscribeEmail" name="subscribe_email" placeholder="Enter your email address" required>
      <button type="submit" class="btn btn-primary">Subscribe</button>
    </form>
  </div>
</section>

<!-- ================= Footer ================= -->
<footer class="site-footer" role="contentinfo">
  <div class="footer-wave" aria-hidden="true">
    <svg viewBox="0 0 1440 80" preserveAspectRatio="none"><path d="M0,32 C240,80 480,0 720,24 C960,48 1200,80 1440,40 L1440,80 L0,80 Z"/></svg>
  </div>

  <div class="container footer-grid">

    <div class="footer-col footer-about">
      <a class="footer-brand" href="<?= e(url('/')) ?>">
        <img src="<?= e(media_url((string) setting('logo_path'), 'logo')) ?>" alt="<?= e(site_name()) ?> logo" width="190" height="58" loading="lazy" decoding="async">
      </a>
      <p class="footer-text"><?= e((string) setting('footer_about')) ?></p>

      <ul class="footer-badges">
        <li><?= e((string) setting('license_authority', 'Punjab Food Authority')) ?> Approved</li>
        <li><?= e((string) setting('psqca_number', 'PSQCA compliant')) ?></li>
        <li>8 Stage Purification</li>
      </ul>

      <div class="footer-social">
        <?php
        $socials = [
            'facebook'  => ['Facebook',  'M13.5 9H16V6h-2.5C11.6 6 10 7.6 10 9.5V11H8v3h2v7h3v-7h2.3l.7-3H13V9.8c0-.5.3-.8.5-.8z'],
            'instagram' => ['Instagram', 'M12 7.4a4.6 4.6 0 1 0 0 9.2 4.6 4.6 0 0 0 0-9.2zm0 7.6a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm5.9-7.8a1.1 1.1 0 1 1-2.2 0 1.1 1.1 0 0 1 2.2 0zM21 7.9c0-1.6-.4-3-1.5-4.1C18.4 2.7 17 2.3 15.4 2.2 13.8 2.1 10.2 2.1 8.6 2.2 7 2.3 5.6 2.7 4.5 3.8 3.4 4.9 3 6.3 2.9 7.9c-.1 1.6-.1 5.2 0 6.8.1 1.6.5 3 1.6 4.1 1.1 1.1 2.5 1.5 4.1 1.6 1.6.1 5.2.1 6.8 0 1.6-.1 3-.5 4.1-1.6 1.1-1.1 1.5-2.5 1.6-4.1.1-1.6.1-5.2 0-6.8zm-1.9 8.5c-.3.8-1 1.5-1.9 1.9-1.3.5-4.4.4-5.9.4s-4.6.1-5.9-.4c-.8-.3-1.5-1-1.9-1.9-.5-1.3-.4-4.4-.4-5.9s-.1-4.6.4-5.9c.3-.8 1-1.5 1.9-1.9 1.3-.5 4.4-.4 5.9-.4s4.6-.1 5.9.4c.8.3 1.5 1 1.9 1.9.5 1.3.4 4.4.4 5.9s.1 4.6-.4 5.9z'],
            'youtube'   => ['YouTube',   'M21.6 7.2s-.2-1.4-.8-2c-.8-.8-1.6-.8-2-.9C15.9 4 12 4 12 4h0s-3.9 0-6.8.3c-.4 0-1.2.1-2 .9-.6.6-.8 2-.8 2S2.2 8.8 2.2 10.4v1.5c0 1.6.2 3.2.2 3.2s.2 1.4.8 2c.8.8 1.8.8 2.2.9 1.6.1 6.6.3 6.6.3s3.9 0 6.8-.3c.4 0 1.2-.1 2-.9.6-.6.8-2 .8-2s.2-1.6.2-3.2v-1.5c0-1.6-.2-3.2-.2-3.2zM9.9 14.1V8.6l5.2 2.8-5.2 2.7z'],
            'tiktok'    => ['TikTok',    'M16.5 2h-3v13.2a2.6 2.6 0 1 1-2.2-2.6v-3a5.6 5.6 0 1 0 5.2 5.6V9.4a6.7 6.7 0 0 0 3.9 1.2v-3a3.8 3.8 0 0 1-3.9-3.7V2z'],
            'linkedin'  => ['LinkedIn',  'M6.9 8.5H4V20h2.9V8.5zM5.4 4a1.7 1.7 0 1 0 0 3.4 1.7 1.7 0 0 0 0-3.4zM20 13.6c0-3-1.6-4.4-3.7-4.4-1.7 0-2.5.9-2.9 1.6V8.5H10.5V20h2.9v-6.3c0-1.3.6-2 1.7-2 1 0 1.6.6 1.6 2V20H20v-6.4z'],
            'twitter'   => ['X',         'M17.5 3h3l-6.6 7.5L21.7 21h-6l-4.7-6.1L5.6 21h-3l7-8-7.3-10h6.1l4.3 5.6L17.5 3zm-1 16h1.7L7.6 4.8H5.8L16.5 19z'],
        ];
        foreach ($socials as $key => [$label, $pathData]):
            $link = setting('social_' . $key);
            if (!$link) { continue; }
        ?>
        <a href="<?= e($link) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($label) ?>" title="<?= e($label) ?>">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="<?= $pathData ?>"/></svg>
        </a>
        <?php endforeach; ?>
        <a href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests')) ?>" target="_blank" rel="noopener" aria-label="WhatsApp" title="WhatsApp">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm5.8 14.13c-.24.68-1.4 1.3-1.94 1.34-.5.05-.98.23-3.3-.69-2.78-1.1-4.55-3.94-4.69-4.12-.14-.18-1.12-1.49-1.12-2.85s.71-2.02.97-2.3c.25-.27.55-.34.73-.34h.53c.17.01.4-.06.62.48.24.57.8 1.96.87 2.1.07.14.12.3.02.48-.09.18-.14.3-.28.46-.14.16-.3.36-.42.48-.14.14-.29.29-.12.57.16.27.73 1.2 1.56 1.95 1.07.95 1.97 1.25 2.25 1.39.27.14.43.12.59-.07.16-.18.68-.79.86-1.06.18-.27.36-.23.61-.14.24.09 1.55.73 1.82.86.27.14.45.2.51.32.07.11.07.64-.17 1.32z"/></svg>
        </a>
      </div>
    </div>

    <div class="footer-col">
      <h3 class="footer-heading">Company</h3>
      <ul class="footer-links">
        <li><a href="<?= e(url('about')) ?>">About Us</a></li>
        <li><a href="<?= e(url('purification-process')) ?>">8 Stage Process</a></li>
        <li><a href="<?= e(url('minerals-and-benefits')) ?>">Minerals &amp; Benefits</a></li>
        <li><a href="<?= e(url('quality-assurance-policy')) ?>">Quality Policy</a></li>
        <li><a href="<?= e(url('gallery')) ?>">Photo Gallery</a></li>
        <li><a href="<?= e(url('documents')) ?>">Licences &amp; Documents</a></li>
        <li><a href="<?= e(url('careers')) ?>">Careers</a></li>
        <li><a href="<?= e(url('blog')) ?>">Blog &amp; News</a></li>
        <?php foreach ($footerCompany as $p): ?>
        <li><a href="<?= e(url($p['slug'])) ?>"><?= e($p['title']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="footer-col">
      <h3 class="footer-heading">Products</h3>
      <ul class="footer-links">
        <?php foreach ($footerProducts as $p): ?>
        <li><a href="<?= e(url('product/' . $p['slug'])) ?>"><?= e($p['short_name'] ?: $p['name']) ?></a></li>
        <?php endforeach; ?>
        <li><a href="<?= e(url('custom-label-bottles')) ?>">Custom Label Bottles</a></li>
        <li><a href="<?= e(url('bulk-water-calculator')) ?>">Bulk Water Calculator</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h3 class="footer-heading">Order &amp; Support</h3>
      <ul class="footer-links">
        <li><a href="<?= e(url('order')) ?>">Place an Order</a></li>
        <li><a href="<?= e(url('coverage-areas')) ?>">Delivery Areas</a></li>
        <li><a href="<?= e(url('distribution')) ?>">Become a Distributor</a></li>
        <li><a href="<?= e(url('distributor-application')) ?>">Distributor Form</a></li>
        <li><a href="<?= e(url('custom-label-request')) ?>">Label Customisation Form</a></li>
        <li><a href="<?= e(url('reviews')) ?>">Customer Reviews</a></li>
        <li><a href="<?= e(url('faqs')) ?>">FAQs</a></li>
        <li><a href="<?= e(url('contact')) ?>">Contact Us</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h3 class="footer-heading">Legal</h3>
      <ul class="footer-links">
        <?php foreach ($footerLegal as $p): ?>
        <li><a href="<?= e(url($p['slug'])) ?>"><?= e($p['title']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="footer-col footer-contact">
      <h3 class="footer-heading">Get in Touch</h3>
      <ul class="footer-contact-list">
        <li>
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>
          <span><?= e((string) setting('address_full')) ?></span>
        </li>
        <?php foreach (whatsapp_numbers() as $wn): ?>
        <li>
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.6.1.3 0 .7-.2 1l-2.3 2.2z"/></svg>
          <a href="<?= e(wa_link($wn['number'])) ?>" target="_blank" rel="noopener"><?= e($wn['number']) ?> <small>(<?= e($wn['label']) ?>)</small></a>
        </li>
        <?php endforeach; ?>
        <li>
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5z"/></svg>
          <a href="mailto:<?= e(contact_email()) ?>"><?= e(contact_email()) ?></a>
        </li>
        <li>
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 10.6V6h-2v7.4l5.2 3.1 1-1.7-4.2-2.2z"/></svg>
          <span><?= e((string) setting('hours_display')) ?></span>
        </li>
      </ul>
      <a class="btn btn-outline btn-block" href="<?= e(url('contact')) ?>">Contact &amp; Map</a>
    </div>
  </div>

  <div class="footer-areas">
    <div class="container">
      <h3 class="footer-heading">Delivery Coverage</h3>
      <p class="footer-area-list">
        <?php foreach ($areas as $i => $a): ?>
          <a href="<?= e(url('coverage-areas')) ?>"><?= e($a['area_name']) ?></a><?= $i < count($areas) - 1 ? ' <span aria-hidden="true">•</span> ' : '' ?>
        <?php endforeach; ?>
      </p>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="container footer-bottom-inner">
      <p>&copy; <?= date('Y') ?> <?= e(site_name()) ?>. All rights reserved.</p>
      <p class="footer-bottom-links">
        <a href="<?= e(url('privacy-policy')) ?>">Privacy</a>
        <a href="<?= e(url('terms-and-conditions')) ?>">Terms</a>
        <a href="<?= e(url('refund-policy')) ?>">Refunds</a>
        <a href="<?= e(url('sitemap.xml')) ?>">Sitemap</a>
      </p>
    </div>
  </div>
</footer>

<?php if (setting_bool('whatsapp_float_enabled', true)): ?>
<a class="whatsapp-float" href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests, I would like to order water. My area is: ')) ?>" target="_blank" rel="noopener" aria-label="Order on WhatsApp">
  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm5.8 14.13c-.24.68-1.4 1.3-1.94 1.34-.5.05-.98.23-3.3-.69-2.78-1.1-4.55-3.94-4.69-4.12-.14-.18-1.12-1.49-1.12-2.85s.71-2.02.97-2.3c.25-.27.55-.34.73-.34h.53c.17.01.4-.06.62.48.24.57.8 1.96.87 2.1.07.14.12.3.02.48-.09.18-.14.3-.28.46-.14.16-.3.36-.42.48-.14.14-.29.29-.12.57.16.27.73 1.2 1.56 1.95 1.07.95 1.97 1.25 2.25 1.39.27.14.43.12.59-.07.16-.18.68-.79.86-1.06.18-.27.36-.23.61-.14.24.09 1.55.73 1.82.86.27.14.45.2.51.32.07.11.07.64-.17 1.32z"/></svg>
  <span class="whatsapp-float-text">Order on WhatsApp</span>
</a>
<?php endif; ?>

<button class="back-to-top" id="backToTop" type="button" aria-label="Back to top">
  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4l8 8h-5v8h-6v-8H4z"/></svg>
</button>

<?= ad_slot('social_bar') ?>
<?= ad_slot('popunder') ?>
<?= ad_slot('push') ?>

</div><!-- /.pe-page -->

<!-- ================= Mobile navigation drawer =================
     Kept at the top level of the DOM (outside .pe-page and the sticky header)
     so it is never trapped inside another stacking context. This is what makes
     the panel render solid and above the dimmed backdrop. -->
<div class="mobile-nav" id="mobileNav" aria-hidden="true">
  <div class="mobile-nav-overlay" data-close></div>
  <aside class="mobile-nav-panel" role="dialog" aria-modal="true" aria-label="Menu">
    <div class="mobile-nav-head">
      <a class="mobile-nav-brand" href="<?= e(url('/')) ?>" data-close>
        <img src="<?= e(media_url((string) setting('logo_path'), 'logo')) ?>" alt="<?= e(site_name()) ?>" width="180" height="52">
      </a>
      <button class="mobile-nav-close" type="button" data-close aria-label="Close menu">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.3 5.7 12 12l6.3 6.3-1.4 1.4L10.6 13.4 4.3 19.7 2.9 18.3 9.2 12 2.9 5.7 4.3 4.3l6.3 6.3 6.3-6.3z"/></svg>
      </button>
    </div>

    <nav class="mobile-nav-list" aria-label="Mobile navigation">
      <ul class="nav-list">
        <?php require PE_ROOT . '/app/partials/nav-items.php'; ?>
      </ul>
    </nav>

    <div class="mobile-nav-cta">
      <a class="btn btn-primary btn-block" href="<?= e(url('order')) ?>" data-close>Order Water Now</a>
      <a class="btn btn-whatsapp btn-block" href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests, I would like to order water.')) ?>" target="_blank" rel="noopener" data-close>WhatsApp Us</a>
    </div>

    <div class="mobile-nav-contact">
      <a href="tel:<?= e(wa_number(primary_whatsapp())) ?>"><?= e(primary_whatsapp()) ?></a>
      <a href="mailto:<?= e(contact_email()) ?>"><?= e(contact_email()) ?></a>
    </div>
  </aside>
</div>

<script src="<?= e(asset('js/main.js')) ?>" defer></script>
</body>
</html>
