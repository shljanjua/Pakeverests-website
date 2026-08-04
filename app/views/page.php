<?php
/** Renders any admin-managed CMS page (legal pages and custom pages). */
declare(strict_types=1);

$cms = $GLOBALS['cmsPage'] ?? null;
if (!$cms) {
    http_response_code(404);
    require PE_ROOT . '/app/views/404.php';
    return;
}

seo_set([
    'title'       => (string) ($cms['meta_title'] ?: $cms['title']),
    'description' => (string) ($cms['meta_description'] ?: excerpt($cms['content'], 165)),
    'keywords'    => (string) $cms['meta_keywords'],
    'robots'      => (int) $cms['noindex'] === 1 ? 'noindex, follow' : 'index, follow, max-image-preview:large, max-snippet:-1',
    'modified_at' => $cms['updated_at'],
    'breadcrumbs' => [$cms['title'] => '/' . $cms['slug']],
]);
seo_add_schema([
    '@context'     => 'https://schema.org',
    '@type'        => 'WebPage',
    'name'         => $cms['title'],
    'url'          => SITE_URL . '/' . $cms['slug'],
    'description'  => excerpt($cms['content'], 200),
    'dateModified' => date('c', strtotime((string) ($cms['updated_at'] ?: $cms['created_at']))),
    'isPartOf'     => ['@id' => SITE_URL . '/#website'],
    'publisher'    => ['@id' => SITE_URL . '/#organization'],
]);

require PE_ROOT . '/app/partials/header.php';
$heroTitle = $cms['title'];
$heroSubtitle = $cms['subtitle'] ?: '';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container container-narrow">
    <div class="prose"><?= rich_text($cms['content']) ?></div>

    <p class="form-hint" style="margin-top:36px;padding-top:18px;border-top:1px solid var(--border);">
      Last updated: <?= pretty_date($cms['updated_at'] ?: $cms['created_at']) ?>.
      Questions about this page? Email <a href="mailto:<?= e(contact_email()) ?>"><?= e(contact_email()) ?></a>
      or WhatsApp <?= e(primary_whatsapp()) ?>.
    </p>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="section-head"><h2>Related policies</h2></div>
    <div class="grid grid-4">
      <?php foreach (nav_pages('legal') as $lp):
        if ($lp['slug'] === $cms['slug']) { continue; } ?>
      <a class="card card-hover" href="<?= e(url($lp['slug'])) ?>" style="text-decoration:none;">
        <h3 style="font-size:1rem;margin:0;"><?= e($lp['title']) ?></h3>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
