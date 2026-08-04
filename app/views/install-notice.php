<?php
/** Shown when the database tables have not been imported yet. */
declare(strict_types=1);
http_response_code(503);
?><!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pak-Everests — Setup Required</title>
<meta name="robots" content="noindex, nofollow">
<style>
  body{margin:0;font-family:"Segoe UI",system-ui,Arial,sans-serif;background:linear-gradient(150deg,#06283a,#0b6490);color:#eaf6fb;
       display:grid;place-items:center;min-height:100vh;padding:24px;line-height:1.7;}
  .box{max-width:720px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.2);border-radius:22px;padding:38px;backdrop-filter:blur(10px);}
  h1{margin:0 0 6px;font-size:1.7rem;} h2{font-size:1.1rem;margin:26px 0 8px;color:#8ef0c9;}
  code,pre{background:rgba(0,0,0,.32);border-radius:8px;padding:2px 7px;font-family:ui-monospace,"Courier New",monospace;font-size:.9rem;}
  pre{padding:14px 16px;overflow-x:auto;}
  ol{padding-left:1.2em;} li{margin-bottom:.55em;}
  .muted{color:#a9cfe2;font-size:.92rem;}
</style></head><body>
<div class="box">
  <h1>Setup required</h1>
  <p class="muted">The Pak-Everests website files are deployed, but the database has not been imported yet.</p>

  <h2>1. Import the database</h2>
  <p>In Hostinger hPanel open <strong>Databases &rarr; phpMyAdmin</strong> for
     <code>u237845628_Pakeverests</code> and import these files, in this order:</p>
  <pre>sql/01-schema.sql
sql/02-seed-data.sql
sql/03-pages.sql
sql/04-blog.sql</pre>

  <h2>2. Add your database password</h2>
  <p>Copy <code>app/config.sample.php</code> to <code>app/config.local.php</code> and set
     <code>db_pass</code> to your MySQL password.</p>

  <h2>3. Log in to the admin panel</h2>
  <p>Open <code>/admin</code> and sign in with username <code>admin</code> and the password from
     <code>sql/02-seed-data.sql</code>. Change it immediately.</p>

  <p class="muted" style="margin-top:26px;">Full instructions are in <code>README.md</code> and <code>DEPLOYMENT.md</code>.</p>
</div>
</body></html>
