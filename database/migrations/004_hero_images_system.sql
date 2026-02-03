-- =============================================
-- A Casa do Gi - Hero Images Management System
-- Migration 004: Add hero and cover images for accommodations
--                 + Page heroes management table
-- =============================================

USE casadogi;

-- =============================================
-- STEP 1: Add hero_image and cover_image to accommodation table
-- These are for Casa 1 and Casa 2 specific images
-- =============================================

-- Check and add hero_image column
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'casadogi' AND TABLE_NAME = 'accommodation' AND COLUMN_NAME = 'hero_image');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE accommodation ADD COLUMN hero_image VARCHAR(500) DEFAULT NULL COMMENT ''Hero image for this accommodation''',
    'SELECT ''Column hero_image already exists''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add cover_image column
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'casadogi' AND TABLE_NAME = 'accommodation' AND COLUMN_NAME = 'cover_image');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE accommodation ADD COLUMN cover_image VARCHAR(500) DEFAULT NULL COMMENT ''Cover image for selection cards on main page''',
    'SELECT ''Column cover_image already exists''');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =============================================
-- STEP 2: Create page_heroes table
-- For managing hero images of all site pages
-- =============================================

CREATE TABLE IF NOT EXISTS page_heroes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_key VARCHAR(50) NOT NULL UNIQUE COMMENT 'Unique page identifier',
    page_name_pt VARCHAR(100) NOT NULL COMMENT 'Page name in Portuguese',
    page_name_en VARCHAR(100) NOT NULL COMMENT 'Page name in English',
    hero_image VARCHAR(500) DEFAULT NULL COMMENT 'Path to hero image',
    hero_overlay_opacity DECIMAL(3,2) DEFAULT 0.40 COMMENT 'Overlay darkness (0-1)',
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_page_key (page_key),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- =============================================
-- STEP 3: Insert default page hero entries
-- =============================================

INSERT INTO page_heroes (page_key, page_name_pt, page_name_en, hero_image, sort_order) VALUES
('home', 'Página Inicial', 'Homepage', 'images/MogadouroAtividades.jpg', 1),
('accommodation_main', 'Alojamento (Página Principal)', 'Accommodation (Main Page)', 'images/MogadouroAlojamento.jpg', 2),
('activities', 'Atividades', 'Activities', 'images/MogadouroAtividades.jpg', 3),
('about', 'Sobre Nós', 'About Us', 'images/MogadouroAlojamento.jpg', 4),
('contact', 'Contactos', 'Contact', 'images/MogadouroAlojamento.jpg', 5),
('shop', 'Loja', 'Shop', 'images/MogadouroNeve.jpeg', 6)
ON DUPLICATE KEY UPDATE
    page_name_pt = VALUES(page_name_pt),
    page_name_en = VALUES(page_name_en);

-- =============================================
-- STEP 4: Set default images for existing accommodations
-- =============================================

UPDATE accommodation SET
    hero_image = 'images/MogadouroAlojamento.jpg',
    cover_image = 'images/IgrejaMatriz.jpg'
WHERE accommodation_number = 1 AND hero_image IS NULL;

UPDATE accommodation SET
    hero_image = 'images/MogadouroAlojamento.jpg',
    cover_image = 'images/Castelo.jpg'
WHERE accommodation_number = 2 AND hero_image IS NULL;

-- =============================================
-- Migration complete!
-- Run this in phpMyAdmin to apply changes
-- =============================================
