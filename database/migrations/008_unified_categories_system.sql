-- ==================================================
-- A Casa do Gi - Unified Categories System
-- Migration 008: Create unified categories for both Activities and Products
-- ==================================================

-- Create unified categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type ENUM('activity', 'product') NOT NULL DEFAULT 'product',
    slug VARCHAR(100) NOT NULL,
    icon VARCHAR(50) DEFAULT NULL COMMENT 'Icon identifier for activities',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_slug_type (slug, type),
    INDEX idx_type (type),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create category translations table
CREATE TABLE IF NOT EXISTS category_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE,
    UNIQUE KEY unique_category_language (category_id, language_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrate existing product categories to new unified system
INSERT INTO categories (id, type, slug, sort_order, is_active, created_at)
SELECT pc.id, 'product', pc.slug, pc.sort_order, pc.is_active, pc.created_at
FROM product_categories pc
ON DUPLICATE KEY UPDATE categories.id=categories.id;

-- Migrate product category translations
INSERT INTO category_translations (category_id, language_id, name, description)
SELECT pct.category_id, pct.language_id, pct.name, pct.description
FROM product_category_translations pct
ON DUPLICATE KEY UPDATE category_translations.category_id=category_translations.category_id;

-- Insert default activity categories (migrating from hardcoded values)
INSERT INTO categories (type, slug, icon, sort_order, is_active) VALUES
('activity', 'nature', 'tree', 1, 1),
('activity', 'culture', 'building', 2, 1),
('activity', 'gastronomy', 'utensils', 3, 1),
('activity', 'restaurants', 'utensils', 4, 1),
('activity', 'cafes', 'coffee', 5, 1),
('activity', 'architecture', 'building', 6, 1),
('activity', 'adventure', 'mountain', 7, 1),
('activity', 'events', 'calendar', 8, 1),
('activity', 'wellness', 'spa', 9, 1),
('activity', 'rural_tourism', 'tractor', 10, 1),
('activity', 'leisure', 'gamepad', 11, 1)
ON DUPLICATE KEY UPDATE slug=slug;

-- Get language IDs
SET @pt_lang_id = (SELECT id FROM languages WHERE code = 'pt' LIMIT 1);
SET @en_lang_id = (SELECT id FROM languages WHERE code = 'en' LIMIT 1);

-- Insert activity category translations (Portuguese)
INSERT INTO category_translations (category_id, language_id, name, description)
SELECT c.id, @pt_lang_id,
    CASE c.slug
        WHEN 'nature' THEN 'Natureza'
        WHEN 'culture' THEN 'Cultura'
        WHEN 'gastronomy' THEN 'Gastronomia'
        WHEN 'restaurants' THEN 'Restaurantes'
        WHEN 'cafes' THEN 'Cafés'
        WHEN 'architecture' THEN 'Arquitetura'
        WHEN 'adventure' THEN 'Aventura'
        WHEN 'events' THEN 'Eventos'
        WHEN 'wellness' THEN 'Bem-estar'
        WHEN 'rural_tourism' THEN 'Turismo Rural'
        WHEN 'leisure' THEN 'Lazer'
    END,
    NULL
FROM categories c
WHERE c.type = 'activity'
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Insert activity category translations (English)
INSERT INTO category_translations (category_id, language_id, name, description)
SELECT c.id, @en_lang_id,
    CASE c.slug
        WHEN 'nature' THEN 'Nature'
        WHEN 'culture' THEN 'Culture'
        WHEN 'gastronomy' THEN 'Gastronomy'
        WHEN 'restaurants' THEN 'Restaurants'
        WHEN 'cafes' THEN 'Cafés'
        WHEN 'architecture' THEN 'Architecture'
        WHEN 'adventure' THEN 'Adventure'
        WHEN 'events' THEN 'Events'
        WHEN 'wellness' THEN 'Wellness'
        WHEN 'rural_tourism' THEN 'Rural Tourism'
        WHEN 'leisure' THEN 'Leisure'
    END,
    NULL
FROM categories c
WHERE c.type = 'activity'
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Add category_id to activities table (replacing old category varchar field)
ALTER TABLE activities
ADD COLUMN IF NOT EXISTS category_id INT UNSIGNED NULL AFTER id;

-- Add index if not exists
SET @exist := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE table_schema = 'casadogi' AND table_name = 'activities' AND index_name = 'idx_category');
SET @sqlstmt := IF(@exist = 0, 'ALTER TABLE activities ADD INDEX idx_category (category_id)', 'SELECT ''Index already exists''');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;

-- Migrate existing activity categories from string to category_id
UPDATE activities a
INNER JOIN categories c ON c.slug = a.category AND c.type = 'activity'
SET a.category_id = c.id;

-- After migration, make category_id required and remove old category field
-- ALTER TABLE activities MODIFY category_id INT UNSIGNED NOT NULL;
-- ALTER TABLE activities DROP COLUMN category;

-- Note: Keep old category column for now as fallback, will be removed later after verification

-- Create view for easier querying
CREATE OR REPLACE VIEW v_categories_with_translations AS
SELECT
    c.id,
    c.type,
    c.slug,
    c.icon,
    c.sort_order,
    c.is_active,
    ct.language_id,
    l.code as language_code,
    ct.name,
    ct.description
FROM categories c
LEFT JOIN category_translations ct ON c.id = ct.category_id
LEFT JOIN languages l ON ct.language_id = l.id
WHERE c.is_active = 1
ORDER BY c.type, c.sort_order, ct.language_id;

-- ==================================================
-- End of Migration 008
-- ==================================================
