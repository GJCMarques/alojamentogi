-- =====================================================
-- Migration: 006_unified_media_system.sql
-- Description: Unified Media System - Move all images to media table
--              Supports: Activities, Heroes, Accommodation
-- Date: 2026-02-06
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- =====================================================
-- 1. EXTEND MEDIA TABLE
-- =====================================================

-- Add new columns to support different entity types
ALTER TABLE `media`
ADD COLUMN IF NOT EXISTS `entity_type` ENUM('activity', 'hero', 'accommodation', 'product', 'standalone', 'other') DEFAULT 'standalone' COMMENT 'Type of entity this media belongs to' AFTER `category`,
ADD COLUMN IF NOT EXISTS `entity_id` INT(10) UNSIGNED DEFAULT NULL COMMENT 'ID of the related entity (activity_id, hero_id, etc)' AFTER `entity_type`,
ADD COLUMN IF NOT EXISTS `is_cover` TINYINT(1) DEFAULT 0 COMMENT 'Is this the cover/main image for the entity' AFTER `entity_id`,
ADD COLUMN IF NOT EXISTS `caption_pt` VARCHAR(500) DEFAULT NULL COMMENT 'Portuguese caption' AFTER `alt_text_en`,
ADD COLUMN IF NOT EXISTS `caption_en` VARCHAR(500) DEFAULT NULL COMMENT 'English caption' AFTER `caption_pt`;

-- Add indexes for performance
CREATE INDEX IF NOT EXISTS `idx_media_entity` ON `media` (`entity_type`, `entity_id`);
CREATE INDEX IF NOT EXISTS `idx_media_cover` ON `media` (`is_cover`);

-- =====================================================
-- 2. MIGRATE ACTIVITY IMAGES TO MEDIA
-- =====================================================

-- Insert activity images into media table
INSERT INTO `media` (
    `filename`,
    `original_name`,
    `file_path`,
    `file_type`,
    `file_size`,
    `alt_text_pt`,
    `alt_text_en`,
    `caption_pt`,
    `caption_en`,
    `category`,
    `entity_type`,
    `entity_id`,
    `is_cover`,
    `sort_order`,
    `created_at`
)
SELECT
    SUBSTRING_INDEX(`image_path`, '/', -1) as `filename`,
    SUBSTRING_INDEX(`image_path`, '/', -1) as `original_name`,
    CONCAT('/', `image_path`) as `file_path`,
    'image/jpeg' as `file_type`,
    0 as `file_size`,
    `alt_text_pt`,
    `alt_text_en`,
    `caption_pt`,
    `caption_en`,
    'activities' as `category`,
    'activity' as `entity_type`,
    `activity_id` as `entity_id`,
    `is_cover`,
    `sort_order`,
    `created_at`
FROM `activity_images`
WHERE NOT EXISTS (
    SELECT 1 FROM `media` m
    WHERE m.entity_type = 'activity'
    AND m.entity_id = `activity_images`.`activity_id`
    AND m.file_path = CONCAT('/', `activity_images`.`image_path`)
);

-- =====================================================
-- 3. UPDATE ACTIVITIES COVER IMAGES
-- =====================================================

-- For each activity, if cover_image is set, make sure it exists in media
INSERT INTO `media` (
    `filename`,
    `original_name`,
    `file_path`,
    `file_type`,
    `file_size`,
    `category`,
    `entity_type`,
    `entity_id`,
    `is_cover`,
    `sort_order`,
    `created_at`
)
SELECT
    SUBSTRING_INDEX(a.`cover_image`, '/', -1) as `filename`,
    SUBSTRING_INDEX(a.`cover_image`, '/', -1) as `original_name`,
    CONCAT('/', a.`cover_image`) as `file_path`,
    'image/jpeg' as `file_type`,
    0 as `file_size`,
    'activities' as `category`,
    'activity' as `entity_type`,
    a.`id` as `entity_id`,
    1 as `is_cover`,
    0 as `sort_order`,
    NOW() as `created_at`
FROM `activities` a
WHERE a.`cover_image` IS NOT NULL
AND a.`cover_image` != ''
AND NOT EXISTS (
    SELECT 1 FROM `media` m
    WHERE m.entity_type = 'activity'
    AND m.entity_id = a.`id`
    AND m.is_cover = 1
);

-- =====================================================
-- 4. MIGRATE HERO IMAGES TO MEDIA
-- =====================================================

-- Insert hero images into media table
INSERT INTO `media` (
    `filename`,
    `original_name`,
    `file_path`,
    `file_type`,
    `file_size`,
    `category`,
    `entity_type`,
    `entity_id`,
    `is_cover`,
    `sort_order`,
    `created_at`
)
SELECT
    SUBSTRING_INDEX(ph.`hero_image`, '/', -1) as `filename`,
    SUBSTRING_INDEX(ph.`hero_image`, '/', -1) as `original_name`,
    CONCAT('/', ph.`hero_image`) as `file_path`,
    'image/jpeg' as `file_type`,
    0 as `file_size`,
    'content' as `category`,
    'hero' as `entity_type`,
    ph.`id` as `entity_id`,
    1 as `is_cover`,
    0 as `sort_order`,
    ph.`created_at`
FROM `page_heroes` ph
WHERE ph.`hero_image` IS NOT NULL
AND ph.`hero_image` != ''
AND NOT EXISTS (
    SELECT 1 FROM `media` m
    WHERE m.entity_type = 'hero'
    AND m.entity_id = ph.`id`
    AND m.file_path = CONCAT('/', ph.`hero_image`)
);

-- =====================================================
-- 5. ADD HELPER VIEWS (Optional - for easy querying)
-- =====================================================

-- View for activity media
CREATE OR REPLACE VIEW `v_activity_media` AS
SELECT
    m.*,
    a.slug as activity_slug,
    at.title as activity_title
FROM `media` m
INNER JOIN `activities` a ON m.entity_id = a.id
LEFT JOIN `activity_translations` at ON a.id = at.activity_id AND at.language_id = 1
WHERE m.entity_type = 'activity';

-- View for hero media
CREATE OR REPLACE VIEW `v_hero_media` AS
SELECT
    m.*,
    ph.page_key,
    ph.is_active
FROM `media` m
INNER JOIN `page_heroes` ph ON m.entity_id = ph.id
WHERE m.entity_type = 'hero';

-- =====================================================
-- 6. NOTES FOR FUTURE CLEANUP
-- =====================================================

-- After verifying the migration worked correctly, you can:
-- 1. Drop the activity_images table: DROP TABLE IF EXISTS `activity_images`;
-- 2. Remove cover_image column from activities: ALTER TABLE `activities` DROP COLUMN `cover_image`;
-- 3. Remove hero_image column from page_heroes: ALTER TABLE `page_heroes` DROP COLUMN `hero_image`;

-- DO NOT RUN THESE YET - Keep for manual execution after testing!

COMMIT;

-- =====================================================
-- VERIFICATION QUERIES (Run these to check migration)
-- =====================================================

-- Check activity images migrated
-- SELECT COUNT(*) as activity_images_in_media FROM media WHERE entity_type = 'activity';

-- Check hero images migrated
-- SELECT COUNT(*) as hero_images_in_media FROM media WHERE entity_type = 'hero';

-- List all media by type
-- SELECT entity_type, COUNT(*) as count FROM media GROUP BY entity_type;
