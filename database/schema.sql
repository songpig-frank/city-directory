-- CityDirectory — Database Schema
-- Run this once per city deployment to create all tables.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ── Categories ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `type` ENUM('business','tourism','creator') NOT NULL DEFAULT 'business',
    `icon` VARCHAR(10) DEFAULT NULL COMMENT 'Emoji icon',
    `description` VARCHAR(255) DEFAULT NULL,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    INDEX `idx_type` (`type`),
    INDEX `idx_active` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Users ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('admin','manager','owner','contributor') NOT NULL DEFAULT 'owner',
    `avatar` VARCHAR(255) DEFAULT NULL,
    `bio` TEXT DEFAULT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `social_links` JSON DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `last_login` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_role` (`role`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Listings ───────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `listings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT UNSIGNED NOT NULL,
    `owner_id` INT UNSIGNED DEFAULT NULL,
    `type` ENUM('business','tourism','creator') NOT NULL DEFAULT 'business',
    `name` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(220) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,

    -- Location
    `address` VARCHAR(255) DEFAULT NULL,
    `barangay` VARCHAR(100) DEFAULT NULL,
    `city` VARCHAR(100) DEFAULT NULL,
    `province` VARCHAR(100) DEFAULT NULL,
    `lat` DECIMAL(10,7) DEFAULT NULL,
    `lng` DECIMAL(10,7) DEFAULT NULL,

    -- Contact
    `phone` VARCHAR(30) DEFAULT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `website` VARCHAR(255) DEFAULT NULL,
    `facebook` VARCHAR(255) DEFAULT NULL,
    `instagram` VARCHAR(255) DEFAULT NULL,
    `youtube` VARCHAR(255) DEFAULT NULL,
    `tiktok` VARCHAR(255) DEFAULT NULL,

    -- Hours: {"mon":{"open":"08:00","close":"17:00"},"tue":{...},...}
    `hours` JSON DEFAULT NULL,

    -- External links
    `shopee_link` VARCHAR(500) DEFAULT NULL,
    `lazada_link` VARCHAR(500) DEFAULT NULL,
    `amazon_link` VARCHAR(500) DEFAULT NULL,
    `food_ordering_link` VARCHAR(500) DEFAULT NULL,
    `affiliate_links` JSON DEFAULT NULL,

    -- Real estate specific (nullable)
    `property_type` ENUM('lot','house_lot','farm','commercial','apartment') DEFAULT NULL,
    `property_sqm` DECIMAL(10,2) DEFAULT NULL,
    `property_price` DECIMAL(14,2) DEFAULT NULL,
    `property_terms` VARCHAR(100) DEFAULT NULL,
    `broker_license` VARCHAR(100) DEFAULT NULL,

    -- Prominence
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `featured_until` DATE DEFAULT NULL,
    `is_spotlight` TINYINT(1) NOT NULL DEFAULT 0,
    `spotlight_until` DATE DEFAULT NULL,

    -- Status & Expiry
    `status` ENUM('pending','active','expired','rejected','draft') NOT NULL DEFAULT 'pending',
    `expires_at` DATE DEFAULT NULL,
    `renewal_token` VARCHAR(64) DEFAULT NULL,
    `rejection_reason` VARCHAR(500) DEFAULT NULL,

    -- Meta
    `views` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`owner_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_status` (`status`),
    INDEX `idx_type_status` (`type`, `status`),
    INDEX `idx_category` (`category_id`, `status`),
    INDEX `idx_featured` (`is_featured`, `featured_until`),
    INDEX `idx_expires` (`expires_at`, `status`),
    INDEX `idx_slug` (`slug`),
    FULLTEXT `ft_search` (`name`, `description`, `address`, `barangay`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Listing Images ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `listing_images` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `listing_id` INT UNSIGNED NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `alt_text` VARCHAR(255) DEFAULT NULL,
    `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`listing_id`) REFERENCES `listings`(`id`) ON DELETE CASCADE,
    INDEX `idx_listing` (`listing_id`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Blog Posts ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `posts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `author_id` INT UNSIGNED DEFAULT NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `excerpt` VARCHAR(500) DEFAULT NULL,
    `body` MEDIUMTEXT NOT NULL,
    `featured_image` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('draft','pending','published') NOT NULL DEFAULT 'draft',
    `published_at` TIMESTAMP NULL DEFAULT NULL,
    `views` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_status` (`status`, `published_at`),
    INDEX `idx_slug` (`slug`),
    FULLTEXT `ft_search` (`title`, `body`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Promotions (paid prominence log) ───────────────────────────────
CREATE TABLE IF NOT EXISTS `promotions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `listing_id` INT UNSIGNED NOT NULL,
    `type` ENUM('featured','spotlight','top_category') NOT NULL,
    `starts_at` DATE NOT NULL,
    `ends_at` DATE NOT NULL,
    `amount_paid` DECIMAL(10,2) DEFAULT NULL,
    `payment_method` VARCHAR(50) DEFAULT NULL,
    `payment_reference` VARCHAR(100) DEFAULT NULL,
    `status` ENUM('active','expired','cancelled') NOT NULL DEFAULT 'active',
    `notes` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`listing_id`) REFERENCES `listings`(`id`) ON DELETE CASCADE,
    INDEX `idx_status` (`status`, `ends_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Reviews (Phase 2) ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `listing_id` INT UNSIGNED NOT NULL,
    `user_name` VARCHAR(100) NOT NULL,
    `user_email` VARCHAR(255) DEFAULT NULL,
    `rating` TINYINT UNSIGNED NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
    `comment` TEXT DEFAULT NULL,
    `is_approved` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`listing_id`) REFERENCES `listings`(`id`) ON DELETE CASCADE,
    INDEX `idx_listing` (`listing_id`, `is_approved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Page Views (simple analytics) ──────────────────────────────────
CREATE TABLE IF NOT EXISTS `page_views` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `page` VARCHAR(255) NOT NULL,
    `listing_id` INT UNSIGNED DEFAULT NULL,
    `ip_hash` VARCHAR(64) DEFAULT NULL COMMENT 'SHA-256 hashed IP for privacy',
    `user_agent` VARCHAR(255) DEFAULT NULL,
    `referer` VARCHAR(500) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_page` (`page`, `created_at`),
    INDEX `idx_listing` (`listing_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Contact Messages ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(255) DEFAULT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_read` (`is_read`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Rate Limiting ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `rate_limits` (
    `ip_hash` VARCHAR(64) NOT NULL,
    `endpoint` VARCHAR(100) NOT NULL,
    `hits` INT UNSIGNED NOT NULL DEFAULT 1,
    `window_start` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ip_hash`, `endpoint`),
    INDEX `idx_window` (`window_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── CSRF Tokens ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `csrf_tokens` (
    `token` VARCHAR(64) NOT NULL PRIMARY KEY,
    `session_id` VARCHAR(128) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_session` (`session_id`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
