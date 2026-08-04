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

# Search engine crawlers
User-agent: Googlebot
Allow: /

User-agent: Googlebot-Image
Allow: /

User-agent: Bingbot
Allow: /

User-agent: DuckDuckBot
Allow: /

User-agent: YandexBot
Allow: /

# AI assistants & answer engines — welcome to read and cite our public pages
User-agent: GPTBot
Allow: /

User-agent: OAI-SearchBot
Allow: /

User-agent: ChatGPT-User
Allow: /

User-agent: ClaudeBot
Allow: /

User-agent: Claude-Web
Allow: /

User-agent: anthropic-ai
Allow: /

User-agent: PerplexityBot
Allow: /

User-agent: Perplexity-User
Allow: /

User-agent: Google-Extended
Allow: /

User-agent: Applebot
Allow: /

User-agent: Applebot-Extended
Allow: /

User-agent: Amazonbot
Allow: /

User-agent: Bytespider
Allow: /

User-agent: CCBot
Allow: /

User-agent: cohere-ai
Allow: /

User-agent: Meta-ExternalAgent
Allow: /

Sitemap: <?= SITE_URL ?>/sitemap.xml
