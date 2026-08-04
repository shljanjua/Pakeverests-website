<?php
/** robots.txt — admin editable, with a sensible default. */
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

$custom = trim((string) setting('robots_txt', ''));
if ($custom !== '') {
    echo $custom . "\n";
    return;
}
?>
User-agent: *
Allow: /

Disallow: /admin
Disallow: /app/
Disallow: /sql/
Disallow: /uploads/careers/
Disallow: /uploads/distributors/
Disallow: /search
Disallow: /thank-you

User-agent: Googlebot
Allow: /

User-agent: Bingbot
Allow: /

Sitemap: <?= SITE_URL ?>/sitemap.xml
