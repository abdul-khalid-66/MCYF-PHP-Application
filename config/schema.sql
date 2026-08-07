-- =============================================================================
--  MCYF — Masood Community Youth Forum
--  Full Database Schema  (MySQL 8+ / MariaDB 10.5+)
--  Run this once to create all tables.
--  Then run seed.sql (or the seeder script) to insert demo data.
-- =============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ── Settings (platform config editable by super-admin) ────────────────────────
CREATE TABLE IF NOT EXISTS `settings` (
  `key`        VARCHAR(100) NOT NULL,
  `value`      TEXT         NOT NULL DEFAULT '',
  `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Members / Users ───────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `members` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(150)    NOT NULL,
  `father_name`  VARCHAR(150)    NOT NULL DEFAULT '',
  `photo`        VARCHAR(300)    NOT NULL DEFAULT '',
  `cnic`         VARCHAR(20)     NOT NULL DEFAULT '',
  `mobile`       VARCHAR(20)     NOT NULL DEFAULT '',
  `email`        VARCHAR(150)    NOT NULL,
  `password`     VARCHAR(255)    NOT NULL,        -- bcrypt hash
  `role`         ENUM('super_admin','admin','committee_head','member','pending')
                                 NOT NULL DEFAULT 'pending',
  `address`      VARCHAR(300)    NOT NULL DEFAULT '',
  `district`     VARCHAR(100)    NOT NULL DEFAULT '',
  `tehsil`       VARCHAR(100)    NOT NULL DEFAULT '',
  `village`      VARCHAR(100)    NOT NULL DEFAULT '',
  `education`    VARCHAR(150)    NOT NULL DEFAULT '',
  `occupation`   VARCHAR(150)    NOT NULL DEFAULT '',
  `blood_group`  VARCHAR(10)     NOT NULL DEFAULT '',
  `position`     VARCHAR(100)    NOT NULL DEFAULT '',
  `status`       ENUM('active','inactive','pending')
                                 NOT NULL DEFAULT 'pending',
  `joined_at`    DATE            NULL,
  `created_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Role Permissions (editable by admin/super-admin from UI) ──────────────────
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role`       VARCHAR(50)  NOT NULL,
  `permission` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_role_perm` (`role`, `permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Announcements ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `announcements` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(300) NOT NULL,
  `description` TEXT         NOT NULL,
  `priority`    ENUM('urgent','important','general') NOT NULL DEFAULT 'general',
  `posted_at`   DATE         NULL,
  `created_by`  INT UNSIGNED NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`created_by`) REFERENCES `members`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Notifications ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `notifications` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(300)  NOT NULL,
  `message`     TEXT          NOT NULL,
  `type`        VARCHAR(100)  NOT NULL DEFAULT '',
  `is_read`     TINYINT(1)    NOT NULL DEFAULT 0,
  `target_role` VARCHAR(50)   NOT NULL DEFAULT 'all',   -- 'all' or a specific role
  `posted_at`   DATE          NULL,
  `created_by`  INT UNSIGNED  NULL,
  `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`created_by`) REFERENCES `members`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Events ───────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `events` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(300) NOT NULL,
  `event_date`  DATE         NULL,
  `venue`       VARCHAR(300) NOT NULL DEFAULT '',
  `organizer`   VARCHAR(150) NOT NULL DEFAULT '',
  `description` TEXT         NOT NULL DEFAULT '',
  `created_by`  INT UNSIGNED NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`created_by`) REFERENCES `members`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Event Gallery (photos per event) ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `event_gallery` (
  `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` INT UNSIGNED NOT NULL,
  `image`    VARCHAR(300) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Committees ────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `committees` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(300) NOT NULL,
  `description` TEXT         NOT NULL DEFAULT '',
  `chairman_id` INT UNSIGNED NULL,   -- FK to members
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`chairman_id`) REFERENCES `members`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Committee Members (pivot) ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `committee_members` (
  `committee_id` INT UNSIGNED NOT NULL,
  `member_id`    INT UNSIGNED NOT NULL,
  PRIMARY KEY (`committee_id`, `member_id`),
  FOREIGN KEY (`committee_id`) REFERENCES `committees`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`member_id`)    REFERENCES `members`(`id`)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Gallery Images ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `gallery_images` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `url`        VARCHAR(300) NOT NULL,
  `category`   VARCHAR(100) NOT NULL DEFAULT '',
  `caption`    VARCHAR(300) NOT NULL DEFAULT '',
  `created_by` INT UNSIGNED NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`created_by`) REFERENCES `members`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Gallery Videos ───────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `gallery_videos` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type`       ENUM('youtube','upload') NOT NULL DEFAULT 'youtube',
  `youtube_id` VARCHAR(50)  NOT NULL DEFAULT '',
  `video_path` VARCHAR(300) NOT NULL DEFAULT '',
  `category`   VARCHAR(100) NOT NULL DEFAULT '',
  `caption`    VARCHAR(300) NOT NULL DEFAULT '',
  `created_by` INT UNSIGNED NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`created_by`) REFERENCES `members`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Emergency Services ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `emergency_services` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(300) NOT NULL,
  `icon`        VARCHAR(100) NOT NULL DEFAULT 'bi-heart-pulse',
  `description` TEXT         NOT NULL DEFAULT '',
  `sort_order`  INT          NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Contact Info ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `contact_info` (
  `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type`      ENUM('phone','email','address','map') NOT NULL,
  `value`     TEXT         NOT NULL,
  `sort_order`INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── About Content (vision, mission, objectives, charter, constitution) ────────
CREATE TABLE IF NOT EXISTS `about_content` (
  `key`        VARCHAR(100) NOT NULL,
  `value`      LONGTEXT     NOT NULL,
  `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Contact Messages (submissions from the public contact form) ──────────────
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(150) NOT NULL,
  `contact_info`VARCHAR(200) NOT NULL DEFAULT '',
  `subject`     VARCHAR(300) NOT NULL DEFAULT '',
  `message`     TEXT         NOT NULL,
  `is_read`     TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
