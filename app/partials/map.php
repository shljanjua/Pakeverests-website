<?php
/** Google Maps embed, or a branded placeholder until the code is pasted in admin. */
declare(strict_types=1);
$embed = trim((string) setting('map_embed_code', ''));
if ($embed !== '' && stripos($embed, '<iframe') !== false) {
    echo $embed;
} else {
    ?>
    <div class="map-placeholder">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>
      <div>
        <strong><?= e((string) setting('address_full', 'Gujar Khan, Punjab, Pakistan')) ?></strong>
        <p class="form-hint" style="margin-top:8px;">Paste your Google Maps embed code in Admin &rarr; Settings &rarr; Maps to display the interactive map here.</p>
      </div>
      <a class="btn btn-outline btn-sm" href="<?= e((string) setting('map_directions_url', 'https://www.google.com/maps')) ?>" target="_blank" rel="noopener">Open in Google Maps</a>
    </div>
    <?php
}
