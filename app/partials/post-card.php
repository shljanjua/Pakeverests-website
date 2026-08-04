<?php
/** Blog post card. Expects $post (a blog_posts row). */
declare(strict_types=1);
$postUrl = url('blog/' . $post['slug']);
?>
<article class="card post-card card-hover reveal" style="padding:0;">
  <a class="post-media" href="<?= e($postUrl) ?>" aria-label="<?= e($post['title']) ?>">
    <img src="<?= e(media_url($post['cover_image'], 'photo')) ?>" alt="<?= e($post['title']) ?>" loading="lazy" width="480" height="270">
  </a>
  <div class="post-body">
    <div class="post-meta">
      <span class="badge"><?= e($post['category']) ?></span>
      <span><?= pretty_date($post['published_at'] ?: $post['created_at']) ?></span>
      <span><?= (int) $post['reading_minutes'] ?> min read</span>
    </div>
    <h3><a href="<?= e($postUrl) ?>"><?= e($post['title']) ?></a></h3>
    <p class="product-desc"><?= e(excerpt($post['excerpt'] ?: $post['content'], 130)) ?></p>
    <a class="btn btn-ghost btn-sm" href="<?= e($postUrl) ?>" style="align-self:flex-start;">Read Article</a>
  </div>
</article>
