-- =============================================
-- A Casa do Gi - Hero Images System (Simplified)
-- Run this in phpMyAdmin SQL tab
-- =============================================

-- Step 1: Add columns to accommodation table (ignore errors if already exist)
ALTER TABLE accommodation ADD COLUMN hero_image VARCHAR(500) DEFAULT NULL;
ALTER TABLE accommodation ADD COLUMN cover_image VARCHAR(500) DEFAULT NULL;

-- Step 2: Create page_heroes table
CREATE TABLE IF NOT EXISTS page_heroes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_key VARCHAR(50) NOT NULL UNIQUE,
    page_name_pt VARCHAR(100) NOT NULL,
    page_name_en VARCHAR(100) NOT NULL,
    hero_image VARCHAR(500) DEFAULT NULL,
    hero_overlay_opacity DECIMAL(3,2) DEFAULT 0.40,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Step 3: Insert page heroes (will update if already exist)
INSERT INTO page_heroes (page_key, page_name_pt, page_name_en, hero_image, sort_order) VALUES
('home', 'Página Inicial', 'Homepage', 'images/MogadouroAtividades.jpg', 1)
ON DUPLICATE KEY UPDATE page_name_pt = VALUES(page_name_pt);

INSERT INTO page_heroes (page_key, page_name_pt, page_name_en, hero_image, sort_order) VALUES
('accommodation_main', 'Alojamento (Página Principal)', 'Accommodation (Main Page)', 'images/MogadouroAlojamento.jpg', 2)
ON DUPLICATE KEY UPDATE page_name_pt = VALUES(page_name_pt);

INSERT INTO page_heroes (page_key, page_name_pt, page_name_en, hero_image, sort_order) VALUES
('activities', 'Atividades', 'Activities', 'images/MogadouroAtividades.jpg', 3)
ON DUPLICATE KEY UPDATE page_name_pt = VALUES(page_name_pt);

INSERT INTO page_heroes (page_key, page_name_pt, page_name_en, hero_image, sort_order) VALUES
('about', 'Sobre Nós', 'About Us', 'images/MogadouroAlojamento.jpg', 4)
ON DUPLICATE KEY UPDATE page_name_pt = VALUES(page_name_pt);

INSERT INTO page_heroes (page_key, page_name_pt, page_name_en, hero_image, sort_order) VALUES
('contact', 'Contactos', 'Contact', 'images/MogadouroAlojamento.jpg', 5)
ON DUPLICATE KEY UPDATE page_name_pt = VALUES(page_name_pt);

INSERT INTO page_heroes (page_key, page_name_pt, page_name_en, hero_image, sort_order) VALUES
('shop', 'Loja', 'Shop', 'images/MogadouroNeve.jpeg', 6)
ON DUPLICATE KEY UPDATE page_name_pt = VALUES(page_name_pt);

-- Step 4: Set default images for accommodations
UPDATE accommodation SET hero_image = 'images/MogadouroAlojamento.jpg' WHERE accommodation_number = 1 AND hero_image IS NULL;
UPDATE accommodation SET cover_image = 'images/IgrejaMatriz.jpg' WHERE accommodation_number = 1 AND cover_image IS NULL;
UPDATE accommodation SET hero_image = 'images/MogadouroAlojamento.jpg' WHERE accommodation_number = 2 AND hero_image IS NULL;
UPDATE accommodation SET cover_image = 'images/Castelo.jpg' WHERE accommodation_number = 2 AND cover_image IS NULL;

-- Done! Now refresh the Heroes admin page.
