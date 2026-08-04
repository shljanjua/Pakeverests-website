# Deployment Guide — pakeverests.site

Deploying from GitHub to Hostinger. **No FTP is used at any point.**

Total time for a first deployment: about 20 minutes.

---

## Step 1 — Create the database (5 minutes)

1. Sign in to **hPanel** → **Databases** → **Management**.
2. Your database already exists:
   - Database name: `u237845628_Pakeverests`
   - Username: `u237845628_Pakeverests`
3. If you do not remember the password, click **Change password** and set a new one.
   **Copy it somewhere safe — you need it in Step 3.**

### Import the SQL files

1. Click **Enter phpMyAdmin** next to the database.
2. Select the `u237845628_Pakeverests` database in the left sidebar.
3. Open the **Import** tab and import these four files **in this exact order**, one at a time:

   | Order | File | What it creates |
   |-------|------|-----------------|
   | 1 | `sql/01-schema.sql` | All 26 tables |
   | 2 | `sql/02-seed-data.sql` | Settings, products, prices, areas, FAQs, reviews, ticker, admin login |
   | 3 | `sql/03-pages.sql` | All 10 legal and policy pages |
   | 4 | `sql/04-blog.sql` | 6 full blog articles |

4. After each import you should see a green success message. If a file fails, stop and
   fix it before importing the next one — the order matters.

---

## Step 2 — Connect GitHub to Hostinger (5 minutes)

1. In hPanel go to **Websites** → select `pakeverests.site` → **Advanced** → **GIT**.
2. Under **Create a New Repository** enter:
   - **Repository address:** `https://github.com/shljanjua/pakeverests-website.git`
   - **Branch:** `main` (or whichever branch you merged this into)
   - **Directory:** leave **blank** so it deploys into `public_html` itself
3. Click **Create**. Hostinger clones the repository.
4. Click **Deploy** to pull the latest commit.

### Automatic deployment on every push (optional but recommended)

1. On the same GIT page, click **Auto Deployment** and copy the webhook URL.
2. In GitHub go to your repository → **Settings** → **Webhooks** → **Add webhook**.
3. Paste the URL into **Payload URL**, set **Content type** to `application/json`,
   leave the secret blank, choose **Just the push event**, and click **Add webhook**.

From then on, every push to `main` deploys to the live site automatically.

---

## Step 3 — Add your database password (2 minutes)

The database password is **never** committed to GitHub. It lives in one file on the
server only.

1. In hPanel go to **Files** → **File Manager** → `public_html/app/`.
2. Find `config.sample.php`, right-click → **Copy**, and name the copy
   `config.local.php` in the same folder.
3. Right-click `config.local.php` → **Edit** and set your real values:

```php
<?php
return [
    'db_host'  => 'localhost',
    'db_name'  => 'u237845628_Pakeverests',
    'db_user'  => 'u237845628_Pakeverests',
    'db_pass'  => 'YOUR-REAL-DATABASE-PASSWORD',
    'site_url' => 'https://pakeverests.site',
    'debug'    => false,
];
```

4. Save.

> `config.local.php` is listed in `.gitignore`, so it stays on the server and is never
> overwritten by a deployment and never pushed to GitHub.

---

## Step 4 — Set folder permissions (1 minute)

In File Manager, right-click the `uploads` folder → **Permissions** → set to **755**
and tick **Apply to subdirectories**. This is what lets the admin panel save images.

---

## Step 5 — Enable HTTPS (2 minutes)

1. hPanel → **Security** → **SSL** → install the free SSL certificate for
   `pakeverests.site` if it is not already active.
2. The included `.htaccess` then forces every visitor onto HTTPS and strips `www.`
   automatically. Nothing else to do.

---

## Step 6 — First login and lockdown (3 minutes)

1. Open **https://pakeverests.site/admin**
2. Sign in with:
   - Username: `admin`
   - Password: `PakEverests@2026`
3. **Immediately** go to **Admin Users** → edit your account → set a new password of at
   least 10 characters, and update the name and email address.

Accounts lock automatically for 15 minutes after 5 failed attempts.

---

## Step 7 — Set up email (5 minutes)

Order notifications reach you far more reliably through SMTP than through PHP mail.

1. hPanel → **Emails** → **Email Accounts** → create `info@pakeverests.site` and set a
   password.
2. In the admin panel go to **Settings → SMTP Email** and enter:

   | Field | Value |
   |-------|-------|
   | Enable SMTP | ticked |
   | Host | `smtp.hostinger.com` |
   | Port | `465` |
   | Encryption | SSL |
   | Username | `info@pakeverests.site` |
   | Password | the mailbox password |
   | From address | `info@pakeverests.site` |
   | From name | Pak-Everests Water |

3. Click **Save**, then use **Send a test email** on the same page and confirm it arrives.
   Failures are shown with the exact SMTP error, and every attempt is written to the
   email log below the form.

---

## Step 8 — Google Search Console (5 minutes)

1. Go to <https://search.google.com/search-console> and add `pakeverests.site` as a
   **URL prefix** property.
2. Choose the **HTML tag** verification method. It shows something like:
   `<meta name="google-site-verification" content="AbC123..." />`
3. Copy **only** the value inside `content="..."`.
4. In the admin panel go to **Settings → SEO & Search Console**, paste it into
   **Google Search Console verification code**, and save.
5. Back in Search Console, click **Verify**.
6. Go to **Sitemaps** and submit: `sitemap.xml`

The sitemap is generated live from your database, so new products, pages and blog posts
appear in it the moment you publish them.

### Also worth doing

- **Google Analytics 4:** paste your `G-XXXXXXXXXX` measurement ID under
  **Settings → Analytics & Tracking**.
- **Google Business Profile:** create a free listing for the plant. For the query
  "water plant near me" this matters more than anything on the website itself.
- **Google Maps:** open Google Maps → find your plant → **Share** → **Embed a map** →
  copy the whole `<iframe>` → paste it under **Settings → Google Maps**.

---

## Step 9 — Upload your own images

Everything works with placeholder graphics out of the box, so nothing is broken while
you gather photographs. Replace them whenever you are ready:

| What | Where in the admin panel | Recommended size |
|------|--------------------------|------------------|
| Logo | Settings → General & Branding | 400 × 120 px WebP or PNG |
| Favicon | Settings → General & Branding | 512 × 512 px PNG |
| Social sharing image | Settings → General & Branding | 1200 × 630 px JPG |
| Hero bottle photo | Settings → General & Branding | Transparent PNG or WebP |
| Product photos | Products → edit each product | 800 × 800 px WebP |
| Gallery photos | Photo Gallery → bulk upload | 1200 × 900 px WebP |
| Licence and certificates | Documents | WebP, JPG or PDF |
| Blog images | Blog → edit post | 1200 × 675 px WebP |

WebP is the best format for this site: much smaller files at the same quality, which
directly improves your Google page speed score.

---

## Step 10 — Go through the settings

Work through **Settings** tab by tab and replace the placeholder values:

- **General & Branding** — licence number, opening hours, per-litre rate
- **Contact & Social** — email addresses, full address, social profile URLs
- **Payment Details** — **your real EasyPaisa, JazzCash and bank account numbers**
  (the seeded bank account number is a dummy `0000-0000000000`)
- **WhatsApp Numbers** — confirm both numbers and which one is primary
- **Google Maps** — paste the embed code for your exact plant location

---

## Updating the site later

```bash
git add .
git commit -m "Describe your change"
git push origin main
```

If you set up the webhook in Step 2, the live site updates within seconds. Otherwise go
to hPanel → GIT → **Deploy**.

Database content — products, prices, pages, blog posts, settings — is edited in the
admin panel and is **not** affected by deployments.

---

## Troubleshooting

**"Setup required" page appears**
The database is not reachable. Check that `app/config.local.php` exists and the password
is correct, and that all four SQL files imported successfully.

**500 error after deployment**
Set `'debug' => true` in `app/config.local.php`, reload the page to read the actual
error, fix it, then set it back to `false`. Never leave debug on for a live site.

**Images upload but do not appear**
Check that `uploads/` is set to permission 755.

**Emails are not arriving**
Check **Settings → SMTP Email** → the email log at the bottom of the page shows the
exact error for every failed send.

**Admin panel returns 404**
Confirm that `.htaccess` deployed to `public_html` (File Manager hides dotfiles by
default — enable "Show hidden files") and that mod_rewrite is enabled, which it is on
all Hostinger shared plans.

---

## Security checklist

- [ ] Default admin password changed
- [ ] `app/config.local.php` created on the server, never committed
- [ ] SSL active and forced
- [ ] `uploads/` set to 755, not 777
- [ ] `debug` set to `false`
- [ ] Real payment details entered, dummy account numbers removed
- [ ] SMTP configured and tested
