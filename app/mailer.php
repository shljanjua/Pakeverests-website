<?php
/**
 * Dependency-free SMTP mailer (Hostinger SMTP friendly) with a mail() fallback.
 * Credentials come from the admin panel → Settings → SMTP.
 */

declare(strict_types=1);

class PeSmtp
{
    private $socket;
    private array $cfg;
    public string $log = '';

    public function __construct(array $cfg)
    {
        $this->cfg = $cfg;
    }

    private function say(string $cmd, array $expect): bool
    {
        if ($cmd !== '') {
            fwrite($this->socket, $cmd . "\r\n");
            $this->log .= '> ' . (stripos($cmd, 'AUTH') === 0 ? 'AUTH ***' : $cmd) . "\n";
        }
        $response = '';
        while ($line = fgets($this->socket, 515)) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        $this->log .= '< ' . trim($response) . "\n";
        return in_array((int) substr(trim($response), 0, 3), $expect, true);
    }

    public function send(string $to, string $subject, string $html, string $replyTo = '', array $extra = []): bool
    {
        $host    = $this->cfg['host'];
        $port    = (int) $this->cfg['port'];
        $enc     = strtolower($this->cfg['encryption'] ?? 'tls');
        $timeout = 20;

        $remote = ($enc === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;
        $ctx    = stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true]]);

        $this->socket = @stream_socket_client($remote, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $ctx);
        if (!$this->socket) {
            $this->log .= "Connection failed: $errstr ($errno)\n";
            return false;
        }
        stream_set_timeout($this->socket, $timeout);

        if (!$this->say('', [220])) return $this->bail();

        $ehlo = 'EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'pakeverests.site');
        if (!$this->say($ehlo, [250])) return $this->bail();

        if ($enc === 'tls') {
            if (!$this->say('STARTTLS', [220])) return $this->bail();
            if (!stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                $this->log .= "TLS negotiation failed\n";
                return $this->bail();
            }
            if (!$this->say($ehlo, [250])) return $this->bail();
        }

        if (!empty($this->cfg['username'])) {
            if (!$this->say('AUTH LOGIN', [334])) return $this->bail();
            if (!$this->say(base64_encode($this->cfg['username']), [334])) return $this->bail();
            if (!$this->say(base64_encode($this->cfg['password']), [235])) return $this->bail();
        }

        $from = $this->cfg['from_email'];
        if (!$this->say('MAIL FROM:<' . $from . '>', [250])) return $this->bail();

        $recipients = array_filter(array_map('trim', explode(',', $to)));
        foreach ($recipients as $rcpt) {
            if (!$this->say('RCPT TO:<' . $rcpt . '>', [250, 251])) return $this->bail();
        }
        foreach (($extra['bcc'] ?? []) as $bcc) {
            $this->say('RCPT TO:<' . $bcc . '>', [250, 251]);
        }

        if (!$this->say('DATA', [354])) return $this->bail();

        $boundary = '=_PE_' . bin2hex(random_bytes(8));
        $headers  = [
            'Date: ' . date('r'),
            'From: ' . $this->encodeHeader($this->cfg['from_name']) . ' <' . $from . '>',
            'To: ' . implode(', ', $recipients),
            'Subject: ' . $this->encodeHeader($subject),
            'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . ($this->cfg['domain'] ?? 'pakeverests.site') . '>',
            'MIME-Version: 1.0',
            'X-Mailer: Pak-Everests Website',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        ];
        if ($replyTo !== '') {
            $headers[] = 'Reply-To: ' . $replyTo;
        }

        $plain = trim(html_entity_decode(strip_tags(preg_replace('#<br\s*/?>|</p>|</tr>#i', "\n", $html)), ENT_QUOTES, 'UTF-8'));

        $body  = implode("\r\n", $headers) . "\r\n\r\n";
        $body .= '--' . $boundary . "\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($plain)) . "\r\n";
        $body .= '--' . $boundary . "\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($html)) . "\r\n";
        $body .= '--' . $boundary . "--\r\n.";

        if (!$this->say($body, [250])) return $this->bail();

        $this->say('QUIT', [221]);
        fclose($this->socket);
        return true;
    }

    private function encodeHeader(string $text): string
    {
        return preg_match('/[\x80-\xFF]/', $text) ? '=?UTF-8?B?' . base64_encode($text) . '?=' : $text;
    }

    private function bail(): bool
    {
        if (is_resource($this->socket)) {
            @fwrite($this->socket, "QUIT\r\n");
            @fclose($this->socket);
        }
        return false;
    }
}

/**
 * Send an email using the configured SMTP account, falling back to PHP mail().
 * Every attempt is written to the email_log table so the admin can audit it.
 */
function send_mail(string $to, string $subject, string $html, string $replyTo = '', string $context = 'general'): bool
{
    $to = trim($to);
    if ($to === '' || !filter_var(explode(',', $to)[0], FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $useSmtp = setting_bool('smtp_enabled', false);
    $ok      = false;
    $error   = '';

    $wrapped = email_wrap($subject, $html);

    if ($useSmtp && setting('smtp_host')) {
        $smtp = new PeSmtp([
            'host'       => (string) setting('smtp_host'),
            'port'       => (int) setting('smtp_port', '587'),
            'encryption' => (string) setting('smtp_encryption', 'tls'),
            'username'   => (string) setting('smtp_username'),
            'password'   => (string) setting('smtp_password'),
            'from_email' => (string) setting('smtp_from_email', contact_email()),
            'from_name'  => (string) setting('smtp_from_name', site_name()),
            'domain'     => parse_url(SITE_URL, PHP_URL_HOST) ?: 'pakeverests.site',
        ]);
        $ok    = $smtp->send($to, $subject, $wrapped, $replyTo);
        $error = $ok ? '' : substr($smtp->log, -900);
    }

    if (!$ok) {
        $from    = (string) setting('smtp_from_email', contact_email());
        $headers = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n"
                 . 'From: ' . site_name() . ' <' . $from . ">\r\n";
        if ($replyTo !== '') {
            $headers .= 'Reply-To: ' . $replyTo . "\r\n";
        }
        $ok = @mail($to, $subject, $wrapped, $headers);
        if (!$ok && $error === '') {
            $error = 'PHP mail() returned false';
        }
    }

    try {
        db_insert('email_log', [
            'recipient'  => $to,
            'subject'    => $subject,
            'context'    => $context,
            'status'     => $ok ? 'sent' : 'failed',
            'error'      => $error,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    } catch (Throwable $e) {
        // Logging must never break the request.
    }

    return $ok;
}

/** Wrap message HTML in the branded email shell. */
function email_wrap(string $title, string $bodyHtml): string
{
    $logo  = abs_url((string) setting('logo_path', '/assets/img/logo.webp'));
    $brand = e(site_name());
    $year  = date('Y');
    $site  = SITE_URL;

    return <<<HTML
<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{$title}</title></head>
<body style="margin:0;padding:0;background:#eef6fb;font-family:Segoe UI,Helvetica,Arial,sans-serif;color:#12303f;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eef6fb;padding:24px 12px;">
<tr><td align="center">
  <table role="presentation" width="620" cellpadding="0" cellspacing="0" style="max-width:620px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 6px 24px rgba(12,74,110,.12);">
    <tr><td style="background:linear-gradient(135deg,#0b6fa4,#12a5c9);padding:22px 28px;">
      <img src="{$logo}" alt="{$brand}" height="46" style="height:46px;display:block;border:0;">
    </td></tr>
    <tr><td style="padding:28px;">
      <h1 style="margin:0 0 16px;font-size:20px;color:#0b6fa4;">{$title}</h1>
      {$bodyHtml}
    </td></tr>
    <tr><td style="background:#f5fafd;padding:18px 28px;font-size:12px;color:#5c7a8a;line-height:1.6;">
      {$brand} — Punjab Food Authority approved mineral water, Potohar region.<br>
      <a href="{$site}" style="color:#0b6fa4;">{$site}</a> &middot; &copy; {$year} All rights reserved.
    </td></tr>
  </table>
</td></tr></table>
</body></html>
HTML;
}

/** Format an associative array as an email detail table. */
function email_table(array $rows): string
{
    $html = '<table role="presentation" cellpadding="8" cellspacing="0" width="100%" style="border-collapse:collapse;font-size:14px;">';
    foreach ($rows as $label => $value) {
        if ($value === '' || $value === null) {
            continue;
        }
        $html .= '<tr>'
            . '<td style="border-bottom:1px solid #e4eef4;color:#5c7a8a;width:38%;vertical-align:top;"><strong>' . e($label) . '</strong></td>'
            . '<td style="border-bottom:1px solid #e4eef4;color:#12303f;">' . nl2br(e((string) $value)) . '</td>'
            . '</tr>';
    }
    return $html . '</table>';
}
