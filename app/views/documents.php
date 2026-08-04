<?php
/** Licences and documents page. */
declare(strict_types=1);

$docs = fetch_all('SELECT * FROM documents WHERE is_public = 1 ORDER BY sort_order ASC, id ASC');
$cats = array_values(array_unique(array_map(fn($d) => $d['category'], $docs)));

seo_set([
    'title'       => 'Licences, Certificates &amp; Documents',
    'description' => 'Punjab Food Authority licence, water quality test reports, and the sample agreement and quotation formats issued by Pak-Everests.',
    'keywords'    => 'water plant licence, Punjab Food Authority licence, water test report, sample agreement, water quotation format',
    'breadcrumbs' => ['Documents' => '/documents'],
]);

require PE_ROOT . '/app/partials/header.php';
$heroTitle = 'Licences, Certificates and Documents';
$heroSubtitle = 'Our regulatory approvals, laboratory reports and the standard agreement and quotation formats we issue.';
require PE_ROOT . '/app/partials/page-hero.php';
?>

<section class="section">
  <div class="container">
    <?php if (!$docs): ?>
      <div class="empty-state">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm0 7V3.5L19.5 9H14z"/></svg>
        <p>Documents are being uploaded. Please contact us if you need a copy in the meantime.</p>
      </div>
    <?php else: ?>

      <?php if (count($cats) > 1): ?>
      <div class="filter-bar">
        <button type="button" class="filter-chip is-active" data-filter="all" data-target="#docGrid">All</button>
        <?php foreach ($cats as $c): ?>
        <button type="button" class="filter-chip" data-filter="<?= e($c) ?>" data-target="#docGrid"><?= e($c) ?></button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="grid grid-4" id="docGrid">
        <?php foreach ($docs as $d):
          $hasFile = trim((string) $d['file_path']) !== '' && is_file(PE_ROOT . '/' . ltrim($d['file_path'], '/'));
          $isPdf   = $d['doc_type'] === 'pdf'; ?>
        <article class="card doc-card reveal" data-category="<?= e($d['category']) ?>">
          <?php if ($isPdf): ?>
            <div class="doc-thumb">
              <div class="doc-pdf">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm0 7V3.5L19.5 9H14z"/></svg>
                <span>PDF Document</span>
              </div>
            </div>
          <?php else: ?>
            <a class="doc-thumb" href="<?= e(media_url($d['file_path'], 'doc')) ?>"
               data-lightbox="<?= e(media_url($d['file_path'], 'doc')) ?>" data-caption="<?= e($d['title']) ?>">
              <img src="<?= e(media_url($d['file_path'], 'doc')) ?>" alt="<?= e($d['title']) ?>" loading="lazy" width="300" height="400">
            </a>
          <?php endif; ?>

          <div>
            <span class="badge"><?= e($d['category']) ?></span>
            <h2 style="font-size:1.05rem;margin:10px 0 6px;"><?= e($d['title']) ?></h2>
            <p style="font-size:.88rem;color:var(--text-soft);"><?= e($d['description']) ?></p>
            <ul style="list-style:none;padding:0;font-size:.8rem;color:var(--text-muted);">
              <?php if ($d['issued_by']): ?><li><strong>Issued by:</strong> <?= e($d['issued_by']) ?></li><?php endif; ?>
              <?php if ($d['reference_no']): ?><li><strong>Reference:</strong> <?= e($d['reference_no']) ?></li><?php endif; ?>
              <?php if ($d['issue_date']): ?><li><strong>Issued:</strong> <?= pretty_date($d['issue_date']) ?></li><?php endif; ?>
              <?php if ($d['expiry_date']): ?><li><strong>Valid until:</strong> <?= pretty_date($d['expiry_date']) ?></li><?php endif; ?>
            </ul>
          </div>

          <?php if ($hasFile): ?>
            <a class="btn btn-outline btn-sm" href="<?= e('/' . ltrim($d['file_path'], '/')) ?>" target="_blank" rel="noopener">
              <?= $isPdf ? 'Open PDF' : 'View Full Size' ?>
            </a>
          <?php else: ?>
            <span class="badge badge-warning">Upload pending</span>
          <?php endif; ?>
        </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="section section-soft">
  <div class="container container-narrow">
    <div class="prose">
      <h2>Requesting a document</h2>
      <p>
        If you need a document that is not published here, such as a current laboratory report for a tender, a
        signed quotation on letterhead, or a copy of our licence for your own records, contact us and we will
        provide it. Corporate and institutional buyers can also request a formal quotation and a water delivery
        agreement drawn up for their specific requirement.
      </p>
      <h2>Sample agreements and quotations</h2>
      <p>
        The sample agreement and quotation formats published here are the standard versions we issue. Actual documents
        are generated for each customer with their own names, rates, territory and dates, and are sent by email or
        WhatsApp for signature.
      </p>
      <p>
        See also our <a href="<?= e(url('terms-and-conditions')) ?>">terms and conditions</a>,
        <a href="<?= e(url('distributor-terms')) ?>">distributor terms</a> and
        <a href="<?= e(url('quality-assurance-policy')) ?>">quality assurance policy</a>.
      </p>
    </div>
    <div class="cta-actions">
      <a class="btn btn-primary btn-lg" href="<?= e(url('contact?subject=' . rawurlencode('Bulk or corporate quotation'))) ?>">Request a Document</a>
      <a class="btn btn-outline btn-lg" href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests, I need a copy of a document.')) ?>">WhatsApp Us</a>
    </div>
  </div>
</section>

<?php require PE_ROOT . '/app/partials/footer.php'; ?>
