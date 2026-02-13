-- Performance optimization indexes
-- Run this on the production database

-- Composite index on media for hero image lookups (covers entity_type + entity_id + is_cover in one index)
ALTER TABLE `media` ADD KEY `idx_media_entity_cover` (`entity_type`, `entity_id`, `is_cover`);

-- Composite index on page_heroes for active page lookups
ALTER TABLE `page_heroes` ADD KEY `idx_page_key_active` (`page_key`, `is_active`);

-- Index on settings for key lookups (if not already indexed)
ALTER TABLE `settings` ADD KEY `idx_settings_key` (`setting_key`);
