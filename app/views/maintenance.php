<?php
/** Maintenance mode screen. */
declare(strict_types=1);
?><!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Back shortly — <?= e(site_name()) ?></title>
<meta name="robots" content="noindex, nofollow">
<style>
  body{margin:0;font-family:"Segoe UI",system-ui,Arial,sans-serif;background:linear-gradient(150deg,#06283a,#199bd2);color:#fff;
       display:grid;place-items:center;min-height:100vh;padding:24px;text-align:center;line-height:1.7;}
  .box{max-width:560px;} h1{font-size:2rem;margin:0 0 10px;}
  a{color:#8ef0c9;} .btn{display:inline-block;margin-top:20px;background:#25d366;color:#06331a;font-weight:700;
     padding:13px 26px;border-radius:999px;text-decoration:none;}
</style></head><body>
<div class="box">
  <h1><?= e(site_name()) ?></h1>
  <p>Our website is briefly down for maintenance. Deliveries are running as normal.</p>
  <p>To place an order right now, message us on WhatsApp or call <?= e(primary_whatsapp()) ?>.</p>
  <a class="btn" href="<?= e(wa_link(primary_whatsapp(), 'Hello Pak-Everests, I would like to order water.')) ?>">Order on WhatsApp</a>
  <p style="margin-top:24px;font-size:.9rem;opacity:.8;"><?= e(contact_email()) ?></p>
</div>
</body></html>
