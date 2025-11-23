-- FILE: /database.sql
-- SplashReels - AI Video Creator SaaS
-- Database Schema with Seed Data

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ====================================================================
-- CORE TENANT & USER TABLES
-- ====================================================================

CREATE TABLE `tenants` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `domain` VARCHAR(255) NULL,
  `status` ENUM('active', 'suspended', 'canceled') NOT NULL DEFAULT 'active',
  `settings_json` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NULL,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `role` ENUM('platform_admin', 'tenant_admin', 'editor', 'viewer') NOT NULL DEFAULT 'editor',
  `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
  `last_login_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_email` (`email`),
  INDEX `idx_tenant_id` (`tenant_id`),
  INDEX `idx_role` (`role`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- SUBSCRIPTION & QUOTA TABLES
-- ====================================================================

CREATE TABLE `plans` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `price_monthly` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `price_yearly` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `max_projects` INT NOT NULL DEFAULT 5,
  `max_minutes_input_per_month` INT NOT NULL DEFAULT 60,
  `max_exports_per_month` INT NOT NULL DEFAULT 20,
  `max_users` INT NOT NULL DEFAULT 3,
  `storage_limit_mb` INT NOT NULL DEFAULT 5000,
  `features_json` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tenant_subscriptions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NOT NULL,
  `plan_id` INT UNSIGNED NOT NULL,
  `status` ENUM('trialing', 'active', 'past_due', 'canceled') NOT NULL DEFAULT 'trialing',
  `start_date` DATE NOT NULL,
  `end_date` DATE NULL,
  `renewal_date` DATE NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_tenant_id` (`tenant_id`),
  INDEX `idx_plan_id` (`plan_id`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`plan_id`) REFERENCES `plans`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tenant_usage` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NOT NULL,
  `month` VARCHAR(7) NOT NULL,
  `total_input_minutes` INT NOT NULL DEFAULT 0,
  `total_exported_clips` INT NOT NULL DEFAULT 0,
  `total_storage_mb` INT NOT NULL DEFAULT 0,
  `api_calls_count` INT NOT NULL DEFAULT 0,
  `projects_count` INT NOT NULL DEFAULT 0,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_tenant_month` (`tenant_id`, `month`),
  INDEX `idx_month` (`month`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- PROJECT & MEDIA TABLES
-- ====================================================================

CREATE TABLE `projects` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `status` ENUM('active', 'archived') NOT NULL DEFAULT 'active',
  `created_by_user_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_tenant_id` (`tenant_id`),
  INDEX `idx_slug` (`slug`),
  INDEX `idx_status` (`status`),
  INDEX `idx_created_by` (`created_by_user_id`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by_user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `media_files` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NOT NULL,
  `project_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `source_type` ENUM('upload', 'youtube_url', 'other') NOT NULL DEFAULT 'upload',
  `source_url` VARCHAR(500) NULL,
  `file_path` VARCHAR(500) NULL,
  `duration_seconds` INT NULL,
  `resolution` VARCHAR(20) NULL,
  `aspect_ratio` VARCHAR(10) NULL,
  `file_size_mb` DECIMAL(10,2) NULL,
  `status` ENUM('uploaded', 'analyzing', 'ready', 'failed') NOT NULL DEFAULT 'uploaded',
  `created_by_user_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_tenant_id` (`tenant_id`),
  INDEX `idx_project_id` (`project_id`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by_user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- AI PIPELINE TABLES
-- ====================================================================

CREATE TABLE `transcripts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NOT NULL,
  `media_file_id` INT UNSIGNED NOT NULL,
  `transcript_text` LONGTEXT NOT NULL,
  `language` VARCHAR(10) NOT NULL DEFAULT 'en',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_tenant_id` (`tenant_id`),
  INDEX `idx_media_file_id` (`media_file_id`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`media_file_id`) REFERENCES `media_files`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `clip_suggestions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NOT NULL,
  `media_file_id` INT UNSIGNED NOT NULL,
  `start_seconds` INT NOT NULL,
  `end_seconds` INT NOT NULL,
  `suggested_title` VARCHAR(255) NOT NULL,
  `suggested_caption_text` TEXT NULL,
  `confidence_score` DECIMAL(3,2) NOT NULL DEFAULT 0.75,
  `status` ENUM('suggested', 'accepted', 'rejected') NOT NULL DEFAULT 'suggested',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_tenant_id` (`tenant_id`),
  INDEX `idx_media_file_id` (`media_file_id`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`media_file_id`) REFERENCES `media_files`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- TEMPLATES & BRAND KITS
-- ====================================================================

CREATE TABLE `brand_kits` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `primary_color` VARCHAR(7) NOT NULL DEFAULT '#000000',
  `secondary_color` VARCHAR(7) NOT NULL DEFAULT '#FFFFFF',
  `accent_color` VARCHAR(7) NOT NULL DEFAULT '#FF0000',
  `font_family` VARCHAR(100) NOT NULL DEFAULT 'Arial',
  `logo_path` VARCHAR(500) NULL,
  `outro_template_json` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_tenant_id` (`tenant_id`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `clip_templates` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `aspect_ratio` VARCHAR(10) NOT NULL DEFAULT '9:16',
  `safe_zone_json` TEXT NULL,
  `font_family` VARCHAR(100) NOT NULL DEFAULT 'Arial',
  `font_color` VARCHAR(7) NOT NULL DEFAULT '#FFFFFF',
  `background_color` VARCHAR(7) NOT NULL DEFAULT '#000000',
  `overlays_json` TEXT NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_tenant_id` (`tenant_id`),
  INDEX `idx_is_default` (`is_default`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- CLIPS & CAPTIONS
-- ====================================================================

CREATE TABLE `scripts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NOT NULL,
  `project_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `script_text` LONGTEXT NOT NULL,
  `language` VARCHAR(10) NOT NULL DEFAULT 'en',
  `target_duration_seconds` INT NULL,
  `created_by_user_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_tenant_id` (`tenant_id`),
  INDEX `idx_project_id` (`project_id`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by_user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `clips` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NOT NULL,
  `project_id` INT UNSIGNED NOT NULL,
  `media_file_id` INT UNSIGNED NULL,
  `script_source_id` INT UNSIGNED NULL,
  `clip_template_id` INT UNSIGNED NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `start_seconds` INT NULL,
  `end_seconds` INT NULL,
  `duration_seconds` INT NULL,
  `status` ENUM('draft', 'processing', 'ready', 'failed') NOT NULL DEFAULT 'draft',
  `preview_thumbnail_path` VARCHAR(500) NULL,
  `output_file_path` VARCHAR(500) NULL,
  `platform_hint` ENUM('tiktok', 'reels', 'shorts', 'generic') NOT NULL DEFAULT 'generic',
  `created_by_user_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_tenant_id` (`tenant_id`),
  INDEX `idx_project_id` (`project_id`),
  INDEX `idx_media_file_id` (`media_file_id`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`media_file_id`) REFERENCES `media_files`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`script_source_id`) REFERENCES `scripts`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`clip_template_id`) REFERENCES `clip_templates`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by_user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `captions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NOT NULL,
  `clip_id` INT UNSIGNED NOT NULL,
  `caption_json` TEXT NOT NULL,
  `style_json` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_tenant_id` (`tenant_id`),
  INDEX `idx_clip_id` (`clip_id`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`clip_id`) REFERENCES `clips`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- EXPORTS
-- ====================================================================

CREATE TABLE `exports` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NOT NULL,
  `clip_id` INT UNSIGNED NOT NULL,
  `format` VARCHAR(10) NOT NULL DEFAULT 'mp4',
  `resolution` VARCHAR(20) NOT NULL DEFAULT '1080x1920',
  `status` ENUM('queued', 'processing', 'ready', 'failed') NOT NULL DEFAULT 'queued',
  `download_url` VARCHAR(500) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_tenant_id` (`tenant_id`),
  INDEX `idx_clip_id` (`clip_id`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`clip_id`) REFERENCES `clips`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- SOCIAL ACCOUNTS & SCHEDULING (STUB)
-- ====================================================================

CREATE TABLE `social_accounts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NOT NULL,
  `platform` ENUM('tiktok', 'instagram', 'youtube', 'twitter', 'linkedin', 'other') NOT NULL,
  `handle` VARCHAR(255) NOT NULL,
  `access_token` TEXT NULL,
  `status` ENUM('connected', 'disconnected', 'expired') NOT NULL DEFAULT 'disconnected',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_tenant_id` (`tenant_id`),
  INDEX `idx_platform` (`platform`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `scheduled_posts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NOT NULL,
  `clip_id` INT UNSIGNED NOT NULL,
  `platform` VARCHAR(50) NOT NULL,
  `social_account_id` INT UNSIGNED NULL,
  `scheduled_at` TIMESTAMP NOT NULL,
  `status` ENUM('scheduled', 'posted', 'failed', 'canceled') NOT NULL DEFAULT 'scheduled',
  `posted_at` TIMESTAMP NULL,
  `error_message` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_tenant_id` (`tenant_id`),
  INDEX `idx_clip_id` (`clip_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_scheduled_at` (`scheduled_at`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`clip_id`) REFERENCES `clips`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`social_account_id`) REFERENCES `social_accounts`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- API KEYS
-- ====================================================================

CREATE TABLE `tenant_api_keys` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NOT NULL,
  `api_key` VARCHAR(64) NOT NULL UNIQUE,
  `label` VARCHAR(255) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `rate_limit_per_minute` INT NOT NULL DEFAULT 60,
  `last_used_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_tenant_id` (`tenant_id`),
  INDEX `idx_api_key` (`api_key`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- ACTIVITY LOGS
-- ====================================================================

CREATE TABLE `activity_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NULL,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NULL,
  `entity_id` INT UNSIGNED NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(500) NULL,
  `metadata_json` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_tenant_id` (`tenant_id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_action` (`action`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- LOGIN ATTEMPTS (for brute force protection)
-- ====================================================================

CREATE TABLE `login_attempts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_ip_address` (`ip_address`),
  INDEX `idx_attempted_at` (`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- SEED DATA
-- ====================================================================

-- Insert Plans
INSERT INTO `plans` (`name`, `slug`, `price_monthly`, `price_yearly`, `max_projects`, `max_minutes_input_per_month`, `max_exports_per_month`, `max_users`, `storage_limit_mb`, `features_json`) VALUES
('Free', 'free', 0.00, 0.00, 2, 30, 10, 1, 1000, '{"auto_captions": false, "brand_templates": false, "api_access": false}'),
('Pro', 'pro', 29.00, 290.00, 10, 300, 100, 5, 50000, '{"auto_captions": true, "brand_templates": true, "api_access": true}'),
('Agency', 'agency', 99.00, 990.00, 50, 1500, 500, 20, 200000, '{"auto_captions": true, "brand_templates": true, "api_access": true, "white_label": true}');

-- Insert Demo Tenant
INSERT INTO `tenants` (`name`, `slug`, `domain`, `status`) VALUES
('Demo Creative Agency', 'demo-agency', NULL, 'active');

-- Insert Platform Admin User
INSERT INTO `users` (`tenant_id`, `email`, `password_hash`, `first_name`, `last_name`, `role`, `status`) VALUES
(NULL, 'admin@splashreels.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Platform', 'Admin', 'platform_admin', 'active');
-- Password: password

-- Insert Demo Tenant Users
INSERT INTO `users` (`tenant_id`, `email`, `password_hash`, `first_name`, `last_name`, `role`, `status`) VALUES
(1, 'admin@demo-agency.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Smith', 'tenant_admin', 'active'),
(1, 'editor@demo-agency.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Doe', 'editor', 'active'),
(1, 'viewer@demo-agency.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Johnson', 'viewer', 'active');
-- All passwords: password

-- Insert Active Subscription for Demo Tenant
INSERT INTO `tenant_subscriptions` (`tenant_id`, `plan_id`, `status`, `start_date`, `end_date`, `renewal_date`) VALUES
(1, 2, 'active', '2025-01-01', NULL, '2025-12-01');

-- Insert Current Month Usage for Demo Tenant
INSERT INTO `tenant_usage` (`tenant_id`, `month`, `total_input_minutes`, `total_exported_clips`, `total_storage_mb`, `api_calls_count`, `projects_count`) VALUES
(1, '2025-11', 45, 12, 2340, 67, 3);

-- Insert Demo Project
INSERT INTO `projects` (`tenant_id`, `name`, `slug`, `description`, `status`, `created_by_user_id`) VALUES
(1, 'Product Launch Campaign', 'product-launch-campaign', 'Social media content for our new product launch', 'active', 2);

-- Insert Demo Media File
INSERT INTO `media_files` (`tenant_id`, `project_id`, `title`, `description`, `source_type`, `file_path`, `duration_seconds`, `resolution`, `aspect_ratio`, `file_size_mb`, `status`, `created_by_user_id`) VALUES
(1, 1, 'Product Demo Full Video', 'Complete product demonstration recorded for the launch', 'upload', '/storage/uploads/raw/1/demo_video_001.mp4', 1820, '1920x1080', '16:9', 487.50, 'ready', 3);

-- Insert Demo Transcript
INSERT INTO `transcripts` (`tenant_id`, `media_file_id`, `transcript_text`, `language`) VALUES
(1, 1, 'Welcome to our amazing new product demonstration. Today we are going to show you the incredible features that make this product stand out from the competition. [0:05] First, let me introduce the sleek design and user-friendly interface. [0:15] The main dashboard gives you complete control over all your settings in one place. [0:25] Now let''s dive into the advanced features that power users will love. [0:40] Performance metrics show real-time analytics and insights. [1:00] Integration with your favorite tools is seamless and secure. [1:30] And finally, our support team is always ready to help you succeed.', 'en');

-- Insert Demo Clip Suggestions
INSERT INTO `clip_suggestions` (`tenant_id`, `media_file_id`, `start_seconds`, `end_seconds`, `suggested_title`, `suggested_caption_text`, `confidence_score`, `status`) VALUES
(1, 1, 5, 25, 'Sleek Design Showcase', 'Check out this beautiful interface! 😍 #ProductDesign', 0.89, 'suggested'),
(1, 1, 25, 50, 'Advanced Features Revealed', 'Power users, this one is for you! 🚀 #AdvancedFeatures', 0.92, 'suggested'),
(1, 1, 60, 95, 'Real-Time Analytics Demo', 'See your data come to life 📊 #Analytics #DataDriven', 0.85, 'accepted'),
(1, 1, 100, 140, 'Seamless Integrations', 'Connect with all your favorite tools 🔗 #Integrations', 0.78, 'suggested');

-- Insert Demo Brand Kit
INSERT INTO `brand_kits` (`tenant_id`, `name`, `primary_color`, `secondary_color`, `accent_color`, `font_family`, `logo_path`) VALUES
(1, 'Demo Agency Brand Kit', '#2563eb', '#1e293b', '#f59e0b', 'Inter', '/storage/uploads/logos/1/demo_logo.png');

-- Insert Global Clip Templates
INSERT INTO `clip_templates` (`tenant_id`, `name`, `description`, `aspect_ratio`, `font_family`, `font_color`, `background_color`, `overlays_json`, `is_default`) VALUES
(NULL, 'TikTok Vertical', 'Optimized for TikTok and vertical platforms', '9:16', 'Poppins', '#FFFFFF', '#000000', '{"progress_bar": true, "captions_position": "bottom"}', 1),
(NULL, 'Instagram Reels', 'Perfect for Instagram Reels', '9:16', 'Montserrat', '#FFFFFF', '#1a1a1a', '{"progress_bar": false, "captions_position": "center"}', 0),
(NULL, 'YouTube Shorts', 'Tailored for YouTube Shorts', '9:16', 'Roboto', '#FFFFFF', '#0f0f0f', '{"progress_bar": true, "captions_position": "bottom"}', 0);

-- Insert Tenant-Specific Template
INSERT INTO `clip_templates` (`tenant_id`, `name`, `description`, `aspect_ratio`, `font_family`, `font_color`, `background_color`, `overlays_json`, `is_default`) VALUES
(1, 'Demo Agency Custom', 'Custom branded template for Demo Agency', '9:16', 'Inter', '#FFFFFF', '#2563eb', '{"progress_bar": true, "captions_position": "bottom", "logo": true}', 0);

-- Insert Demo Clip (ready)
INSERT INTO `clips` (`tenant_id`, `project_id`, `media_file_id`, `clip_template_id`, `title`, `description`, `start_seconds`, `end_seconds`, `duration_seconds`, `status`, `preview_thumbnail_path`, `output_file_path`, `platform_hint`, `created_by_user_id`) VALUES
(1, 1, 1, 1, 'Real-Time Analytics Demo', 'Showcasing our powerful analytics dashboard', 60, 95, 35, 'ready', '/storage/uploads/thumbnails/1/clip_001_thumb.jpg', '/storage/uploads/clips/1/clip_001.mp4', 'tiktok', 3);

-- Insert Demo Caption
INSERT INTO `captions` (`tenant_id`, `clip_id`, `caption_json`, `style_json`) VALUES
(1, 1, '[{"start": 0, "end": 3, "text": "See your data"}, {"start": 3, "end": 6, "text": "come to life"}]', '{"position": "bottom", "font_size": 24, "background": true}');

-- Insert Demo Export
INSERT INTO `exports` (`tenant_id`, `clip_id`, `format`, `resolution`, `status`, `download_url`) VALUES
(1, 1, 'mp4', '1080x1920', 'ready', '/storage/uploads/clips/1/clip_001.mp4');

-- Insert Demo API Key
INSERT INTO `tenant_api_keys` (`tenant_id`, `api_key`, `label`, `is_active`, `rate_limit_per_minute`) VALUES
(1, 'sk_live_demo_1234567890abcdef1234567890abcdef12345678', 'Demo API Key', 1, 100);

-- Insert Some Activity Logs
INSERT INTO `activity_logs` (`tenant_id`, `user_id`, `action`, `entity_type`, `entity_id`, `ip_address`) VALUES
(1, 2, 'login', 'user', 2, '127.0.0.1'),
(1, 3, 'create_project', 'project', 1, '127.0.0.1'),
(1, 3, 'upload_media', 'media_file', 1, '127.0.0.1'),
(1, 3, 'generate_clips', 'media_file', 1, '127.0.0.1'),
(1, 3, 'create_clip', 'clip', 1, '127.0.0.1');
