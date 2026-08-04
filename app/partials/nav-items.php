<?php
/**
 * Shared primary navigation items. Included by both the desktop nav bar and the
 * mobile drawer so the two never drift apart. Expects $route and $navProducts
 * to be in scope (set in header.php).
 */
declare(strict_types=1);
?>
<li><a href="<?= e(url('/')) ?>" <?= $route === '' ? 'aria-current="page"' : '' ?>>Home</a></li>

<li class="has-dropdown">
  <a href="<?= e(url('about')) ?>" aria-haspopup="true">About <svg class="caret" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 10l5 5 5-5z"/></svg></a>
  <ul class="dropdown">
    <li><a href="<?= e(url('about')) ?>">About Pak-Everests</a></li>
    <li><a href="<?= e(url('purification-process')) ?>">8 Stage Purification Process</a></li>
    <li><a href="<?= e(url('minerals-and-benefits')) ?>">Minerals &amp; Health Benefits</a></li>
    <li><a href="<?= e(url('quality-assurance-policy')) ?>">Quality Assurance Policy</a></li>
    <li><a href="<?= e(url('gallery')) ?>">Photo Gallery</a></li>
    <li><a href="<?= e(url('documents')) ?>">Licences &amp; Documents</a></li>
    <li><a href="<?= e(url('careers')) ?>">Careers</a></li>
  </ul>
</li>

<li class="has-dropdown">
  <a href="<?= e(url('products')) ?>" aria-haspopup="true">Products <svg class="caret" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 10l5 5 5-5z"/></svg></a>
  <ul class="dropdown dropdown-wide">
    <li><a href="<?= e(url('products')) ?>"><strong>All Products &amp; Prices</strong></a></li>
    <?php foreach ($navProducts as $np): ?>
    <li><a href="<?= e(url('product/' . $np['slug'])) ?>"><?= e($np['short_name'] ?: $np['name']) ?></a></li>
    <?php endforeach; ?>
    <li><a href="<?= e(url('custom-label-bottles')) ?>">Custom Label Bottles</a></li>
    <li><a href="<?= e(url('bulk-water-calculator')) ?>">Bulk Water Calculator</a></li>
  </ul>
</li>

<li class="has-dropdown">
  <a href="<?= e(url('distribution')) ?>" aria-haspopup="true">Distribution <svg class="caret" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 10l5 5 5-5z"/></svg></a>
  <ul class="dropdown">
    <li><a href="<?= e(url('distribution')) ?>">Become a Distributor</a></li>
    <li><a href="<?= e(url('distributor-application')) ?>">Distributor Application Form</a></li>
    <li><a href="<?= e(url('distributor-terms')) ?>">Distributor Terms</a></li>
    <li><a href="<?= e(url('coverage-areas')) ?>">Delivery Coverage Areas</a></li>
  </ul>
</li>

<li><a href="<?= e(url('coverage-areas')) ?>">Coverage</a></li>
<li><a href="<?= e(url('blog')) ?>">Blog</a></li>
<li><a href="<?= e(url('faqs')) ?>">FAQs</a></li>
<li><a href="<?= e(url('contact')) ?>">Contact</a></li>
