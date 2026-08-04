<?php
/** Page hero band. Expects $heroTitle, optional $heroSubtitle. */
declare(strict_types=1);
?>
<section class="page-hero">
  <div class="container page-hero-inner">
    <?= breadcrumbs_html() ?>
    <h1><?= e($heroTitle ?? seo_get('title')) ?></h1>
    <?php if (!empty($heroSubtitle)): ?><p><?= e($heroSubtitle) ?></p><?php endif; ?>
  </div>
</section>
