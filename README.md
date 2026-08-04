# Pak-Everests Bottled Drinking Water — pakeverests.site

A complete, self-contained website and admin panel for a Punjab Food Authority approved
mineral water plant in Gujar Khan, delivering across Mandra, Gujar Khan, Rawalpindi,
Islamabad and the wider Potohar region.

Built in plain PHP 8 and MySQL with no framework and no Composer dependencies, so it
runs on Hostinger shared hosting exactly as it is.

**→ For installation, read [DEPLOYMENT.md](DEPLOYMENT.md).**

---

## What is included

### Public website (32 pages)

| Section | Pages |
|---------|-------|
| Home | Animated water hero, products, process, minerals, sectors, coverage, reviews, FAQ, blog, map |
| Products | Price list plus a detailed page per product (12 products) |
| Company | About, 8 stage purification process, minerals and benefits, gallery, documents, careers |
| Ordering | Order form with live totals, bulk water calculator, coverage areas |
| Distribution | Distributor overview, application form, published commercial terms |
| Custom labels | Service page and a full artwork brief form |
| Content | Blog with 6 long-form articles, FAQs, customer reviews with submission form |
| Legal | Privacy, terms, refund, delivery, damage, dispute resolution, distributor terms, cookies, disclaimer, quality policy |

### Products and pricing (all editable in the admin panel)

| Product | Price | Notes |
|---------|-------|-------|
| 19 litre refill | Rs 250 | + Rs 1,500 refundable bottle deposit, free delivery |
| 12 litre bottle | Rs 230 | Free delivery |
| 6 litre bottle | Rs 130 | Free delivery |
| 1.5 litre Pure, pack of 6 | Rs 400 | Premium PET |
| 1.5 litre Mix, pack of 6 | Rs 350 | Standard PET |
| 500 ml Pure, pack of 12 | Rs 400 | Most popular for custom labels |
| 500 ml Mix, pack of 12 | Rs 350 | Value pack |
| 350 ml, pack of 24 | Coming soon | |
| Glass bottles | Coming soon | Returnable crate system |
| Water pouches | Coming soon | |
| Water dispenser | Rs 42,000 | Or Rs 2,000 per month on rent |
| Bulk facility filling | Rs 6 per litre | Collection at the plant |

### Delivery coverage

Gujar Khan · Mandra · Daultala · Bewal · Habib Chowk · Kallar Syedan · Rawat ·
DHA Islamabad · Bahria Town Rawalpindi · Adiala Road · Rawalpindi city and Cantt

---

## Admin panel

Sign in at `/admin`. Twenty-six modules, grouped in the sidebar:

**Overview** — Dashboard, Analytics
**Enquiries** — Orders, Messages, Distributors, Label requests, Reviews, Careers, Subscribers
**Content** — Products, Blog, Pages and Legal, Gallery, Documents, Media library, FAQs,
8 Stage Process, Minerals, Delivery areas, News ticker
**Documents** — Agreement generator, Quotation generator
**Configuration** — WhatsApp numbers, Ads and monetisation, Settings, Admin users

### What each part does

**Dashboard** — orders today and this month, order value, page views, unique visitors,
a 14 day traffic chart, top pages, best selling products, recent orders and messages,
and a setup checklist that tells you what still needs configuring.

**Analytics** — first-party visitor tracking with no third-party dependency. Views and
unique visitors by day and by hour, and breakdowns by country, region, city, page,
browser, operating system, device type and traffic source. Includes a live view of the
last 30 minutes and a data purge tool.

**Orders** — every order with its full item list, deposits, delivery note and customer
details. Status workflow, internal notes, bulk status changes, CSV export, and one-click
WhatsApp confirmation messages that are pre-filled with the order.

**Products** — full editor: pricing, deposit, rental rate, descriptions, features,
specifications, main and additional photos, availability, badges and per-product SEO.

**Blog** — write posts with a cover image, two in-content images and a bottom image.
The first in-content image is placed automatically about a third of the way down.
Reading time is calculated on save.

**Pages and Legal** — edit every policy page and create entirely new pages. A new page is
live at `/your-slug`, appears in the chosen footer column, and enters the sitemap
immediately.

**Reviews** — approve, reject, reply publicly, edit or add reviews. Product star ratings
and Google review schema recalculate automatically from approved reviews.

**News ticker** — add, edit, schedule and reorder messages, with full control of colour,
font family, size, weight, scroll speed and hover-pause, and a live preview.

**Agreement generator** — pick a type (water delivery, distributor, dispenser rental or
custom), fill in the party details, and the complete legal text is generated from the
published Pak-Everests terms. Edit any clause, email it to the customer, or print to PDF
with a signature block.

**Quotation generator** — line items with automatic totals, discount and delivery,
validity date, terms, and your payment details. Email it or print it as a branded PDF.

**Settings** — eight tabs covering branding and images, contact and social, SEO and
Search Console verification, Google Maps, payment accounts (EasyPaisa, JazzCash, bank),
SMTP with a test-send button and an email log, analytics and pixels, and website
behaviour including maintenance mode.

**Ads and monetisation** — AdSense, Adsterra, Monetag and custom code across eight
placements (header, in-content top and middle, sidebar, footer, social bar, popunder,
push), plus an editable `ads.txt` served at `/ads.txt`.

---

## SEO

- One `<h1>` per page; all other headings are `h2`, `h3`, `h4`
- Unique title, meta description (all under 165 characters), keywords and canonical URL
  on every page
- Open Graph and Twitter card tags with a generated default share image
- JSON-LD schema on every page: `LocalBusiness` + `FoodEstablishment` with geo
  coordinates, opening hours and areas served; `WebSite` with search action;
  `BreadcrumbList`; `Product` with offers and reviews; `FAQPage`; `BlogPosting`;
  `HowTo` for the purification process; `JobPosting`; `ImageGallery`; `ContactPage`
- `aggregateRating` is emitted **only** where approved reviews actually exist
- Live XML sitemap at `/sitemap.xml`, editable `robots.txt` at `/robots.txt`
- Verification fields for Google, Bing, Yandex and Pinterest
- HTTPS forced, `www.` stripped, trailing slashes removed, 301 redirects in `.htaccess`
- Geo meta tags for Gujar Khan, plus `areaServed` covering every delivery town

Target keywords: *water plant near me, mineral water, 19 liters water bottle, mineral
water plant, water plant in Gujar Khan, best water plant in Gujar Khan, water delivery
Rawalpindi, drinking water Islamabad, custom label water bottles*.

---

## How orders reach you

Every order and enquiry goes to **three** places at once:

1. **The admin panel** — stored in the database with a reference number
2. **Your email inbox** — a formatted notification to `info@pakeverests.site`
3. **WhatsApp** — the confirmation screen shows a button that opens a message already
   filled in with the full order, addressed to your primary WhatsApp number

Customers who give an email address also receive their own confirmation copy.

---

## Security

- All queries use prepared statements
- CSRF token on every form, public and admin
- bcrypt password hashing, with accounts locking for 15 minutes after 5 failed attempts
- Honeypot fields and per-IP rate limiting on public forms
- Upload restrictions by type and size; PHP execution disabled inside `/uploads`
- `/app` and `/sql` blocked from direct HTTP access
- Security headers: `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`,
  `Permissions-Policy`, HSTS
- Database credentials live only in `app/config.local.php`, which is git-ignored
- Admin activity log recording who changed what, when, from which IP

---

## Technical notes

**Requirements:** PHP 8.0+ with PDO MySQL, GD and cURL; MySQL 5.7+ or MariaDB 10.3+;
Apache or LiteSpeed with mod_rewrite. All standard on Hostinger.

**No build step.** No npm, no Composer, no bundler. Push the files and it runs.

**Front end:** hand-written CSS with custom properties and a full dark theme; vanilla
JavaScript with no libraries. Respects `prefers-reduced-motion`, and includes skip
links, ARIA labelling and keyboard-accessible navigation.

### Project structure

```
├── index.php               Public front controller
├── admin/index.php         Admin front controller
├── app/
│   ├── config.php          Configuration loader
│   ├── config.sample.php   Copy to config.local.php on the server
│   ├── db.php              PDO wrapper
│   ├── helpers.php         Escaping, URLs, uploads, CSRF, formatting
│   ├── settings.php        Key/value settings store
│   ├── seo.php             Meta tags, canonicals, JSON-LD schema
│   ├── mailer.php          Dependency-free SMTP client
│   ├── analytics.php       Page-view tracking and geo lookup
│   ├── forms.php           Public form handlers
│   ├── admin/              26 admin modules
│   ├── partials/           Header, footer, cards, map
│   └── views/              Public page views
├── assets/css|js|img       Stylesheets, scripts, placeholder graphics
├── sql/                    Four import files, in order
└── uploads/                Admin-managed media (git-ignored)
```

---

## Default login

```
URL:      https://pakeverests.site/admin
Username: admin
Password: PakEverests@2026
```

**Change this password the first time you sign in.** The admin panel shows a red warning
banner until you do.

---

## Contact

**Pak-Everests Bottled Drinking Water**
Main G.T. Road, Gujar Khan, District Rawalpindi, Punjab, Pakistan
WhatsApp: 0333 5592206 · 0332 2901309
Email: info@pakeverests.site
