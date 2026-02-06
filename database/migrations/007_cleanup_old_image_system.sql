-- =====================================================
-- Migration: 007_cleanup_old_image_system.sql
-- Description: Clean up old image tables and columns
--              Remove activity_images table and legacy columns
-- Date: 2026-02-06
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- =====================================================
-- 1. DROP OLD ACTIVITY_IMAGES TABLE
-- =====================================================

DROP TABLE IF EXISTS `activity_images`;

-- =====================================================
-- 2. REMOVE LEGACY IMAGE COLUMNS FROM ACTIVITIES
-- =====================================================

-- Remove old cover_image column (now in media table)
ALTER TABLE `activities`
DROP COLUMN IF EXISTS `cover_image`,
DROP COLUMN IF EXISTS `image`;

-- =====================================================
-- 3. REMOVE LEGACY HERO_IMAGE COLUMN FROM PAGE_HEROES
-- =====================================================

-- Remove old hero_image column (now in media table)
ALTER TABLE `page_heroes`
DROP COLUMN IF EXISTS `hero_image`;

-- =====================================================
-- 4. VERIFY CLEANUP
-- =====================================================

-- Check that media table has all the data
SELECT
    entity_type,
    COUNT(*) as total_images
FROM media
GROUP BY entity_type;

COMMIT;

-- =====================================================
-- CLEANUP COMPLETE!
-- =====================================================
-- The following have been removed:
-- - activity_images table (replaced by media)
-- - activities.cover_image column (replaced by media with is_cover=1)
-- - activities.image column (legacy, unused)
-- - page_heroes.hero_image column (replaced by media)
--
-- All images are now centralized in the media table!
-- =====================================================
