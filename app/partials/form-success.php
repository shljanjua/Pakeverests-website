<?php
/**
 * Success panel shown after a public form submits.
 * Expects: $result (with ref / wa_link), $successTitle, $successBody.
 */
declare(strict_types=1);
?>
<div class="success-panel">
  <div class="success-icon">
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg>
  </div>
  <h2><?= e($successTitle ?? 'Thank you, we have received it') ?></h2>
  <p><?= $successBody ?? 'Our team will get back to you shortly.' ?></p>
  <?php if (!empty($result['ref'])): ?>
    <p>Your reference number: <span class="ref-code"><?= e($result['ref']) ?></span></p>
    <p class="form-hint">Please quote this reference in any follow up message.</p>
  <?php endif; ?>
  <div class="cta-actions">
    <?php if (!empty($result['wa_link'])): ?>
      <a class="btn btn-lg btn-whatsapp" href="<?= e($result['wa_link']) ?>" target="_blank" rel="noopener">
        Send the details on WhatsApp
      </a>
    <?php endif; ?>
    <a class="btn btn-lg btn-outline" href="<?= e(url('/')) ?>">Back to Home</a>
  </div>
  <?php if (!empty($result['wa_link'])): ?>
  <p class="form-hint" style="margin-top:18px;">
    Tapping the WhatsApp button opens a message that is already filled in with your details. Sending it lets our team
    confirm your request instantly, but it is optional. We have already received everything in our system.
  </p>
  <?php endif; ?>
</div>
