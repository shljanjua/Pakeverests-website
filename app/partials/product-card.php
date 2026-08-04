<?php
/** Product card. Expects $p (a products row). */
declare(strict_types=1);

$comingSoon = (int) $p['is_coming_soon'] === 1;
$productUrl = url('product/' . $p['slug']);
?>
<article class="product-card reveal">
  <div class="product-flags">
    <?php if ($comingSoon): ?><span class="badge badge-soon">Coming Soon</span><?php endif; ?>
    <?php if ((int) $p['is_bestseller'] === 1 && !$comingSoon): ?><span class="badge badge-hot">Best Seller</span><?php endif; ?>
    <?php if ((int) $p['free_delivery'] === 1 && !$comingSoon): ?><span class="badge badge-success">Free Delivery</span><?php endif; ?>
  </div>

  <a class="product-media" href="<?= e($productUrl) ?>" aria-label="<?= e($p['name']) ?>">
    <img src="<?= e(media_url($p['main_image'], 'product')) ?>" alt="<?= e($p['name']) ?> - Pak-Everests mineral water" loading="lazy" width="400" height="300">
  </a>

  <div class="product-body">
    <h3><a href="<?= e($productUrl) ?>"><?= e($p['name']) ?></a></h3>

    <div class="product-meta">
      <?php if ((int) $p['rating_count'] > 0): ?>
        <?= stars((float) $p['rating']) ?>
        <span><?= (int) $p['rating_count'] ?> review<?= (int) $p['rating_count'] === 1 ? '' : 's' ?></span>
      <?php else: ?>
        <span>No reviews yet</span>
      <?php endif; ?>
      <?php if ($p['volume_label']): ?><span>&middot; <?= e($p['volume_label']) ?></span><?php endif; ?>
    </div>

    <p class="product-desc"><?= e(excerpt($p['short_description'], 118)) ?></p>

    <div class="product-price">
      <?php if ($comingSoon || (float) $p['price'] <= 0): ?>
        <span class="amount" style="font-size:1.15rem;">Price on announcement</span>
      <?php else: ?>
        <span class="amount"><?= money($p['price']) ?></span>
        <span class="unit"><?= e($p['price_unit']) ?></span>
      <?php endif; ?>
    </div>

    <?php if ((float) $p['security_deposit'] > 0): ?>
      <p class="deposit-note">+ <?= money($p['security_deposit']) ?> refundable bottle deposit, one time only</p>
    <?php elseif ($p['rent_price'] !== null && (float) $p['rent_price'] > 0): ?>
      <p class="deposit-note">Or <?= money($p['rent_price']) ?> <?= e((string) $p['rent_unit']) ?></p>
    <?php endif; ?>

    <div class="product-actions">
      <a class="btn btn-outline btn-sm" href="<?= e($productUrl) ?>">Details</a>
      <?php if ($comingSoon): ?>
        <a class="btn btn-ghost btn-sm" href="<?= e(url('contact')) ?>">Notify Me</a>
      <?php else: ?>
        <a class="btn btn-primary btn-sm" href="<?= e(url('order?product=' . $p['slug'])) ?>">Order</a>
      <?php endif; ?>
    </div>
  </div>
</article>
