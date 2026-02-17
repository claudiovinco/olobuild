-- Mosaic Builder Database Schema
-- These tables are created via dbDelta() in class-database.php

CREATE TABLE IF NOT EXISTS `wp_mosaic_templates` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL DEFAULT '',
    `type` VARCHAR(50) NOT NULL DEFAULT 'page',
    `content` LONGTEXT NOT NULL,
    `settings` LONGTEXT NOT NULL,
    `thumbnail` VARCHAR(500) DEFAULT '',
    `status` VARCHAR(20) NOT NULL DEFAULT 'draft',
    `author_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_status` (`status`),
    KEY `idx_type` (`type`),
    KEY `idx_author_id` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `wp_mosaic_revisions` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `template_id` BIGINT(20) UNSIGNED NOT NULL,
    `content` LONGTEXT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_template_id` (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
