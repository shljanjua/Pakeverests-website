-- ==========================================================================
--  Pak-Everests Bottled Drinking Water — Database Schema
--  Target: Hostinger MySQL / MariaDB 10.x  (database: u237845628_Pakeverests)
--
--  Import order:
--    1) sql/01-schema.sql   (this file — tables)
--    2) sql/02-seed-data.sql (content, products, settings, admin user)
--
--  Import via hPanel → Databases → phpMyAdmin → Import.
-- ==========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_ENGINE_SUBSTITUTION';

-- --------------------------------------------------------------------------
--  1. Settings  (every admin-editable option: SEO, ads, SMTP, payments, maps)
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key`   VARCHAR(120) NOT NULL,
  `setting_value` LONGTEXT NULL,
  `setting_group` VARCHAR(60) NOT NULL DEFAULT 'general',
  `updated_at`    DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_setting_key` (`setting_key`),
  KEY `idx_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
--  2. Admin users
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(120) NOT NULL,
  `email`         VARCHAR(190) NOT NULL,
  `username`      VARCHAR(60)  NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role`          ENUM('super_admin','admin','editor') NOT NULL DEFAULT 'admin',
  `is_active`     TINYINT(1) NOT NULL DEFAULT 1,
  `last_login_at` DATETIME NULL,
  `last_login_ip` VARCHAR(45) NULL,
  `login_attempts` INT NOT NULL DEFAULT 0,
  `locked_until`  DATETIME NULL,
  `created_at`    DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
--  3. Products
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`             VARCHAR(160) NOT NULL,
  `name`             VARCHAR(190) NOT NULL,
  `short_name`       VARCHAR(90)  NULL,
  `category`         VARCHAR(80)  NOT NULL DEFAULT 'bottles',
  `pack_size`        VARCHAR(90)  NULL,
  `volume_label`     VARCHAR(60)  NULL,
  `price`            DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `price_unit`       VARCHAR(60)  NOT NULL DEFAULT 'per bottle',
  `security_deposit` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `rent_price`       DECIMAL(10,2) NULL,
  `rent_unit`        VARCHAR(60)  NULL,
  `compare_price`    DECIMAL(10,2) NULL,
  `sku`              VARCHAR(60)  NULL,
  `tagline`          VARCHAR(255) NULL,
  `short_description` TEXT NULL,
  `long_description` LONGTEXT NULL,
  `features`         TEXT NULL,           -- one per line
  `specifications`   TEXT NULL,           -- "Label: Value" one per line
  `best_for`         VARCHAR(255) NULL,
  `main_image`       VARCHAR(255) NULL,
  `free_delivery`    TINYINT(1) NOT NULL DEFAULT 1,
  `is_coming_soon`   TINYINT(1) NOT NULL DEFAULT 0,
  `is_featured`      TINYINT(1) NOT NULL DEFAULT 0,
  `is_bestseller`    TINYINT(1) NOT NULL DEFAULT 0,
  `stock_status`     ENUM('in_stock','on_demand','coming_soon','out_of_stock') NOT NULL DEFAULT 'in_stock',
  `rating`           DECIMAL(3,2) NOT NULL DEFAULT 5.00,
  `rating_count`     INT NOT NULL DEFAULT 0,
  `meta_title`       VARCHAR(190) NULL,
  `meta_description` VARCHAR(320) NULL,
  `meta_keywords`    VARCHAR(320) NULL,
  `sort_order`       INT NOT NULL DEFAULT 0,
  `status`           ENUM('published','draft') NOT NULL DEFAULT 'published',
  `created_at`       DATETIME NOT NULL,
  `updated_at`       DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_product_slug` (`slug`),
  KEY `idx_status_sort` (`status`,`sort_order`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `alt_text`   VARCHAR(190) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
--  4. Orders
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_ref`      VARCHAR(40) NOT NULL,
  `customer_name`  VARCHAR(150) NOT NULL,
  `phone`          VARCHAR(40)  NOT NULL,
  `whatsapp`       VARCHAR(40)  NULL,
  `email`          VARCHAR(190) NULL,
  `address`        TEXT NOT NULL,
  `area`           VARCHAR(120) NULL,
  `city`           VARCHAR(120) NULL,
  `customer_type`  VARCHAR(80)  NULL,   -- home / office / school / hospital ...
  `order_type`     VARCHAR(60)  NOT NULL DEFAULT 'one_time',
  `preferred_time` VARCHAR(90)  NULL,
  `payment_method` VARCHAR(60)  NULL,
  `message`        TEXT NULL,
  `delivery_note`  TEXT NULL,
  `subtotal`       DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `deposit_total`  DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `total`          DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `status`         ENUM('new','confirmed','out_for_delivery','delivered','cancelled') NOT NULL DEFAULT 'new',
  `admin_note`     TEXT NULL,
  `source`         VARCHAR(60) NOT NULL DEFAULT 'website',
  `ip_address`     VARCHAR(45) NULL,
  `is_read`        TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`     DATETIME NOT NULL,
  `updated_at`     DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_ref` (`order_ref`),
  KEY `idx_status_created` (`status`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`     INT UNSIGNED NOT NULL,
  `product_id`   INT UNSIGNED NULL,
  `product_name` VARCHAR(190) NOT NULL,
  `quantity`     INT NOT NULL DEFAULT 1,
  `unit_price`   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `deposit`      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `line_total`   DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
--  5. Contact messages
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref`        VARCHAR(40) NOT NULL,
  `name`       VARCHAR(150) NOT NULL,
  `email`      VARCHAR(190) NULL,
  `phone`      VARCHAR(40)  NULL,
  `subject`    VARCHAR(190) NULL,
  `message`    TEXT NOT NULL,
  `area`       VARCHAR(120) NULL,
  `form_type`  VARCHAR(60) NOT NULL DEFAULT 'contact',
  `status`     ENUM('new','read','replied','archived') NOT NULL DEFAULT 'new',
  `admin_note` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
--  6. Distributor applications
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `distributor_applications`;
CREATE TABLE `distributor_applications` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref`              VARCHAR(40) NOT NULL,
  `applicant_name`   VARCHAR(150) NOT NULL,
  `business_name`    VARCHAR(190) NULL,
  `cnic`             VARCHAR(30)  NULL,
  `phone`            VARCHAR(40)  NOT NULL,
  `whatsapp`         VARCHAR(40)  NULL,
  `email`            VARCHAR(190) NULL,
  `city`             VARCHAR(120) NULL,
  `area_requested`   VARCHAR(190) NULL,
  `address`          TEXT NULL,
  `business_type`    VARCHAR(120) NULL,
  `experience_years` VARCHAR(60)  NULL,
  `has_vehicle`      VARCHAR(20)  NULL,
  `vehicle_details`  VARCHAR(190) NULL,
  `has_storage`      VARCHAR(20)  NULL,
  `storage_details`  VARCHAR(190) NULL,
  `investment_range` VARCHAR(90)  NULL,
  `monthly_target`   VARCHAR(90)  NULL,
  `message`          TEXT NULL,
  `document_path`    VARCHAR(255) NULL,
  `status`           ENUM('new','reviewing','approved','rejected','on_hold') NOT NULL DEFAULT 'new',
  `admin_note`       TEXT NULL,
  `ip_address`       VARCHAR(45) NULL,
  `created_at`       DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
--  7. Custom label / private label requests
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `label_requests`;
CREATE TABLE `label_requests` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref`             VARCHAR(40) NOT NULL,
  `contact_name`    VARCHAR(150) NOT NULL,
  `company_name`    VARCHAR(190) NULL,
  `designation`     VARCHAR(120) NULL,
  `phone`           VARCHAR(40)  NOT NULL,
  `whatsapp`        VARCHAR(40)  NULL,
  `email`           VARCHAR(190) NULL,
  `city`            VARCHAR(120) NULL,
  `address`         TEXT NULL,
  `bottle_size`     VARCHAR(90)  NULL,
  `quantity`        VARCHAR(90)  NULL,
  `occasion`        VARCHAR(120) NULL,
  `required_date`   DATE NULL,
  `has_artwork`     VARCHAR(30)  NULL,
  `artwork_path`    VARCHAR(255) NULL,
  `logo_path`       VARCHAR(255) NULL,
  `brand_colors`    VARCHAR(190) NULL,
  `label_text`      TEXT NULL,
  `design_help`     VARCHAR(30)  NULL,
  `message`         TEXT NULL,
  `status`          ENUM('new','quoted','in_design','approved','printing','delivered','cancelled') NOT NULL DEFAULT 'new',
  `quoted_amount`   DECIMAL(12,2) NULL,
  `admin_note`      TEXT NULL,
  `ip_address`      VARCHAR(45) NULL,
  `created_at`      DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
--  8. Reviews & testimonials
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id`     INT UNSIGNED NULL,
  `reviewer_name`  VARCHAR(150) NOT NULL,
  `reviewer_email` VARCHAR(190) NULL,
  `reviewer_role`  VARCHAR(150) NULL,
  `location`       VARCHAR(120) NULL,
  `rating`         TINYINT NOT NULL DEFAULT 5,
  `title`          VARCHAR(190) NULL,
  `body`           TEXT NOT NULL,
  `avatar_path`    VARCHAR(255) NULL,
  `is_featured`    TINYINT(1) NOT NULL DEFAULT 0,
  `is_verified`    TINYINT(1) NOT NULL DEFAULT 0,
  `status`         ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_reply`    TEXT NULL,
  `ip_address`     VARCHAR(45) NULL,
  `created_at`     DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_product_status` (`product_id`,`status`),
  KEY `idx_status_created` (`status`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
--  9. Blog
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `blog_posts`;
CREATE TABLE `blog_posts` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`             VARCHAR(190) NOT NULL,
  `title`            VARCHAR(190) NOT NULL,
  `excerpt`          TEXT NULL,
  `content`          LONGTEXT NULL,
  `cover_image`      VARCHAR(255) NULL,
  `inline_image_1`   VARCHAR(255) NULL,
  `inline_image_2`   VARCHAR(255) NULL,
  `bottom_image`     VARCHAR(255) NULL,
  `category`         VARCHAR(90) NOT NULL DEFAULT 'Water & Health',
  `tags`             VARCHAR(255) NULL,
  `author`           VARCHAR(120) NOT NULL DEFAULT 'Pak-Everests Team',
  `reading_minutes`  INT NOT NULL DEFAULT 4,
  `views`            INT NOT NULL DEFAULT 0,
  `meta_title`       VARCHAR(190) NULL,
  `meta_description` VARCHAR(320) NULL,
  `meta_keywords`    VARCHAR(320) NULL,
  `is_featured`      TINYINT(1) NOT NULL DEFAULT 0,
  `status`           ENUM('published','draft') NOT NULL DEFAULT 'published',
  `published_at`     DATETIME NULL,
  `created_at`       DATETIME NOT NULL,
  `updated_at`       DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_post_slug` (`slug`),
  KEY `idx_status_pub` (`status`,`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 10. CMS pages (legal + custom pages, fully editable from admin)
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`             VARCHAR(190) NOT NULL,
  `title`            VARCHAR(190) NOT NULL,
  `subtitle`         VARCHAR(255) NULL,
  `content`          LONGTEXT NULL,
  `hero_image`       VARCHAR(255) NULL,
  `nav_group`        ENUM('none','legal','company','support','main') NOT NULL DEFAULT 'legal',
  `is_system`        TINYINT(1) NOT NULL DEFAULT 0,
  `meta_title`       VARCHAR(190) NULL,
  `meta_description` VARCHAR(320) NULL,
  `meta_keywords`    VARCHAR(320) NULL,
  `noindex`          TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order`       INT NOT NULL DEFAULT 0,
  `status`           ENUM('published','draft') NOT NULL DEFAULT 'published',
  `created_at`       DATETIME NOT NULL,
  `updated_at`       DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_page_slug` (`slug`),
  KEY `idx_nav_group` (`nav_group`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 11. Media library
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_path`  VARCHAR(255) NOT NULL,
  `file_name`  VARCHAR(255) NULL,
  `file_type`  ENUM('image','document') NOT NULL DEFAULT 'image',
  `extension`  VARCHAR(12) NULL,
  `file_size`  INT UNSIGNED NOT NULL DEFAULT 0,
  `alt_text`   VARCHAR(190) NULL,
  `folder`     VARCHAR(90) NOT NULL DEFAULT 'general',
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_folder` (`folder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 12. Photo gallery
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(190) NOT NULL,
  `caption`    TEXT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `alt_text`   VARCHAR(190) NULL,
  `category`   VARCHAR(90) NOT NULL DEFAULT 'Plant',
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cat` (`category`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 13. Documents (licences, agreements, quotations, certificates)
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(190) NOT NULL,
  `description` TEXT NULL,
  `category`    VARCHAR(90) NOT NULL DEFAULT 'Licences',
  `doc_type`    ENUM('image','pdf') NOT NULL DEFAULT 'image',
  `file_path`   VARCHAR(255) NOT NULL,
  `thumb_path`  VARCHAR(255) NULL,
  `issued_by`   VARCHAR(190) NULL,
  `issue_date`  DATE NULL,
  `expiry_date` DATE NULL,
  `reference_no` VARCHAR(90) NULL,
  `is_public`   TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order`  INT NOT NULL DEFAULT 0,
  `created_at`  DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cat` (`category`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 14. News ticker
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `news_ticker`;
CREATE TABLE `news_ticker` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `text`       VARCHAR(400) NOT NULL,
  `link`       VARCHAR(255) NULL,
  `icon`       VARCHAR(20) NULL,
  `highlight`  TINYINT(1) NOT NULL DEFAULT 0,
  `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
  `starts_at`  DATETIME NULL,
  `ends_at`    DATETIME NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 15. WhatsApp numbers
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `whatsapp_numbers`;
CREATE TABLE `whatsapp_numbers` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `label`      VARCHAR(90) NOT NULL,
  `number`     VARCHAR(40) NOT NULL,
  `department` VARCHAR(90) NULL,
  `receives_orders` TINYINT(1) NOT NULL DEFAULT 1,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 16. Analytics — page views + geo cache
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `page_views`;
CREATE TABLE `page_views` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id`   VARCHAR(40) NOT NULL,
  `page_url`     VARCHAR(255) NOT NULL,
  `page_title`   VARCHAR(190) NULL,
  `referrer`     VARCHAR(255) NULL,
  `source`       VARCHAR(80) NULL,
  `ip_address`   VARCHAR(45) NULL,
  `country`      VARCHAR(90) NULL,
  `country_code` VARCHAR(4)  NULL,
  `region`       VARCHAR(90) NULL,
  `city`         VARCHAR(90) NULL,
  `browser`      VARCHAR(40) NULL,
  `os`           VARCHAR(40) NULL,
  `device`       VARCHAR(20) NULL,
  `user_agent`   VARCHAR(255) NULL,
  `created_at`   DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_created` (`created_at`),
  KEY `idx_page` (`page_url`(120)),
  KEY `idx_country` (`country`),
  KEY `idx_session` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `geo_cache`;
CREATE TABLE `geo_cache` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_address`   VARCHAR(45) NOT NULL,
  `country`      VARCHAR(90) NULL,
  `country_code` VARCHAR(4) NULL,
  `region`       VARCHAR(90) NULL,
  `city`         VARCHAR(90) NULL,
  `created_at`   DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ip` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 17. Newsletter subscribers
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `subscribers`;
CREATE TABLE `subscribers` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`      VARCHAR(190) NOT NULL,
  `name`       VARCHAR(150) NULL,
  `source`     VARCHAR(60) NOT NULL DEFAULT 'footer',
  `status`     ENUM('active','unsubscribed') NOT NULL DEFAULT 'active',
  `ip_address` VARCHAR(45) NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 18. Agreements & quotations generator
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `agreements`;
CREATE TABLE `agreements` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `doc_ref`        VARCHAR(40) NOT NULL,
  `doc_type`       ENUM('delivery_agreement','distributor_agreement','dispenser_agreement','custom') NOT NULL DEFAULT 'delivery_agreement',
  `party_name`     VARCHAR(190) NOT NULL,
  `party_company`  VARCHAR(190) NULL,
  `party_cnic`     VARCHAR(30) NULL,
  `party_phone`    VARCHAR(40) NULL,
  `party_email`    VARCHAR(190) NULL,
  `party_address`  TEXT NULL,
  `territory`      VARCHAR(190) NULL,
  `start_date`     DATE NULL,
  `end_date`       DATE NULL,
  `security_amount` DECIMAL(12,2) NULL,
  `monthly_target` VARCHAR(90) NULL,
  `rate_summary`   TEXT NULL,
  `body`           LONGTEXT NULL,
  `status`         ENUM('draft','sent','signed','expired','cancelled') NOT NULL DEFAULT 'draft',
  `sent_to`        VARCHAR(190) NULL,
  `sent_at`        DATETIME NULL,
  `created_by`     INT UNSIGNED NULL,
  `created_at`     DATETIME NOT NULL,
  `updated_at`     DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_doc_ref` (`doc_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `quotations`;
CREATE TABLE `quotations` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote_ref`     VARCHAR(40) NOT NULL,
  `client_name`   VARCHAR(190) NOT NULL,
  `client_company` VARCHAR(190) NULL,
  `client_phone`  VARCHAR(40) NULL,
  `client_email`  VARCHAR(190) NULL,
  `client_address` TEXT NULL,
  `items_json`    LONGTEXT NULL,
  `subtotal`      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `discount`      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `delivery_charges` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `total`         DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `valid_until`   DATE NULL,
  `notes`         TEXT NULL,
  `terms`         LONGTEXT NULL,
  `status`        ENUM('draft','sent','accepted','declined','expired') NOT NULL DEFAULT 'draft',
  `sent_to`       VARCHAR(190) NULL,
  `sent_at`       DATETIME NULL,
  `created_by`    INT UNSIGNED NULL,
  `created_at`    DATETIME NOT NULL,
  `updated_at`    DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_quote_ref` (`quote_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 19. FAQs
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `faqs`;
CREATE TABLE `faqs` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `question`   VARCHAR(320) NOT NULL,
  `answer`     TEXT NOT NULL,
  `category`   VARCHAR(90) NOT NULL DEFAULT 'General',
  `show_on_home` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 20. Coverage / delivery areas
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `coverage_areas`;
CREATE TABLE `coverage_areas` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `area_name`     VARCHAR(120) NOT NULL,
  `district`      VARCHAR(120) NULL,
  `description`   TEXT NULL,
  `delivery_days` VARCHAR(190) NULL,
  `min_order`     VARCHAR(90) NULL,
  `is_free_delivery` TINYINT(1) NOT NULL DEFAULT 1,
  `is_active`     TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order`    INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 21. Purification process stages
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `process_stages`;
CREATE TABLE `process_stages` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `stage_no`    INT NOT NULL,
  `title`       VARCHAR(190) NOT NULL,
  `subtitle`    VARCHAR(255) NULL,
  `icon`        VARCHAR(40) NULL,
  `summary`     TEXT NULL,
  `details`     LONGTEXT NULL,
  `what_it_removes` TEXT NULL,
  `image_path`  VARCHAR(255) NULL,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_stage_no` (`stage_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 22. Minerals
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `minerals`;
CREATE TABLE `minerals` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(90) NOT NULL,
  `symbol`       VARCHAR(10) NULL,
  `typical_value` VARCHAR(60) NULL,
  `unit`         VARCHAR(20) NOT NULL DEFAULT 'mg/L',
  `who_limit`    VARCHAR(60) NULL,
  `psqca_limit`  VARCHAR(60) NULL,
  `benefits`     TEXT NULL,
  `details`      LONGTEXT NULL,
  `color`        VARCHAR(20) NULL,
  `sort_order`   INT NOT NULL DEFAULT 0,
  `is_active`    TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 23. Careers
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `job_openings`;
CREATE TABLE `job_openings` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`         VARCHAR(160) NOT NULL,
  `title`        VARCHAR(190) NOT NULL,
  `department`   VARCHAR(120) NULL,
  `location`     VARCHAR(120) NULL,
  `job_type`     VARCHAR(60) NOT NULL DEFAULT 'Full Time',
  `salary_range` VARCHAR(90) NULL,
  `experience`   VARCHAR(90) NULL,
  `description`  LONGTEXT NULL,
  `requirements` TEXT NULL,
  `positions`    INT NOT NULL DEFAULT 1,
  `closing_date` DATE NULL,
  `is_active`    TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order`   INT NOT NULL DEFAULT 0,
  `created_at`   DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_job_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `job_applications`;
CREATE TABLE `job_applications` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref`        VARCHAR(40) NOT NULL,
  `job_id`     INT UNSIGNED NULL,
  `job_title`  VARCHAR(190) NULL,
  `name`       VARCHAR(150) NOT NULL,
  `email`      VARCHAR(190) NULL,
  `phone`      VARCHAR(40) NOT NULL,
  `city`       VARCHAR(120) NULL,
  `experience` VARCHAR(120) NULL,
  `education`  VARCHAR(190) NULL,
  `cover_note` TEXT NULL,
  `cv_path`    VARCHAR(255) NULL,
  `status`     ENUM('new','shortlisted','interviewed','hired','rejected') NOT NULL DEFAULT 'new',
  `admin_note` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 24. Ad slots (AdSense / Adsterra / Monetag)
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `ad_slots`;
CREATE TABLE `ad_slots` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120) NOT NULL,
  `network`    VARCHAR(60) NOT NULL DEFAULT 'adsense',
  `placement`  VARCHAR(60) NOT NULL,
  `code`       LONGTEXT NULL,
  `is_active`  TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_placement` (`placement`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 25. Email log
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `email_log`;
CREATE TABLE `email_log` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipient`  VARCHAR(255) NOT NULL,
  `subject`    VARCHAR(255) NULL,
  `context`    VARCHAR(60) NULL,
  `status`     ENUM('sent','failed') NOT NULL DEFAULT 'sent',
  `error`      TEXT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 26. Admin activity log
-- --------------------------------------------------------------------------
DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE `activity_log` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NULL,
  `user_name`  VARCHAR(120) NULL,
  `action`     VARCHAR(190) NOT NULL,
  `entity`     VARCHAR(90) NULL,
  `entity_id`  INT UNSIGNED NULL,
  `ip_address` VARCHAR(45) NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
