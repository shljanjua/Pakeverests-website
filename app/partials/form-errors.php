<?php
/** Renders $result['errors'] from a form handler. */
declare(strict_types=1);
if (!empty($result['errors'])): ?>
<div class="alert alert-error" role="alert">
  <strong>Please check the following:</strong>
  <ul>
    <?php foreach ($result['errors'] as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
  </ul>
</div>
<?php endif;
