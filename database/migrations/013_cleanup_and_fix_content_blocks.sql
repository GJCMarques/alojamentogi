-- Migration 013: Cleanup and fix content_blocks
-- Removes orphaned/legacy blocks and fixes NULL page/section values
-- Safe to re-run (idempotent)

-- =============================================
-- 1. REMOVE OLD/ORPHANED BLOCKS
-- These were from the old structure before the dynamic content refactor
-- =============================================

DELETE FROM content_blocks WHERE block_key IN (
    'hero_title',
    'hero_subtitle',
    'hero_cta',
    'about_text'
);

-- =============================================
-- 2. FIX NULL page/section VALUES
-- These blocks exist but have NULL page/section, which breaks admin filtering
-- =============================================

UPDATE content_blocks SET page = 'accommodation', section = 'main'
WHERE block_key = 'accommodation_cta_text' AND (page IS NULL OR section IS NULL);

UPDATE content_blocks SET page = 'accommodation', section = 'main'
WHERE block_key = 'accommodation_cta_title' AND (page IS NULL OR section IS NULL);

UPDATE content_blocks SET page = 'contact', section = 'main'
WHERE block_key = 'contact_success_message' AND (page IS NULL OR section IS NULL);

UPDATE content_blocks SET page = 'shop', section = 'main'
WHERE block_key = 'shop_empty_message' AND (page IS NULL OR section IS NULL);

-- =============================================
-- 3. ADD MISSING BLOCKS
-- footer_tagline is defined in admin but missing from DB
-- =============================================

INSERT INTO content_blocks (block_key, language_id, content_type, content, page, section) VALUES
('footer_tagline', 1, 'text', 'Simplicidade, acolhimento e muito amor em Mogadouro', 'footer', 'main'),
('footer_tagline', 2, 'text', 'Simplicity, warmth and love in Mogadouro', 'footer', 'main')
ON DUPLICATE KEY UPDATE page=VALUES(page), section=VALUES(section);

-- =============================================
-- 4. FIX content_type MISMATCHES
-- Ensure DB content_type matches what admin expects (text vs textarea vs html)
-- =============================================

-- accommodation_cta_text should be textarea (admin defines it as textarea)
UPDATE content_blocks SET content_type = 'textarea'
WHERE block_key = 'accommodation_cta_text' AND content_type != 'textarea';

-- contact_success_message should be textarea (admin defines it as textarea)
UPDATE content_blocks SET content_type = 'textarea'
WHERE block_key = 'contact_success_message' AND content_type != 'textarea';
