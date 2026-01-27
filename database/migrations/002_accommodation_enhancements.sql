-- =============================================
-- A Casa do Gi - Accommodation Enhancements Migration
-- Adds: rating, reviews, location details, host type,
--       check-in info, bathrooms, refund policy, etc.
-- =============================================

USE casadogi;

-- =============================================
-- STEP 1: Update accommodation table with new fields
-- =============================================
ALTER TABLE accommodation
    ADD COLUMN IF NOT EXISTS rating DECIMAL(2,1) DEFAULT NULL COMMENT 'Average rating (e.g., 4.3)',
    ADD COLUMN IF NOT EXISTS reviews_count INT UNSIGNED DEFAULT 0 COMMENT 'Total number of reviews',
    ADD COLUMN IF NOT EXISTS city VARCHAR(100) DEFAULT 'Mogadouro' COMMENT 'City name',
    ADD COLUMN IF NOT EXISTS region VARCHAR(100) DEFAULT 'Trás-os-Montes' COMMENT 'Region name',
    ADD COLUMN IF NOT EXISTS country VARCHAR(100) DEFAULT 'Portugal' COMMENT 'Country name',
    ADD COLUMN IF NOT EXISTS host_type ENUM('professional', 'superhost', 'standard') DEFAULT 'standard' COMMENT 'Host type badge',
    ADD COLUMN IF NOT EXISTS checkin_type ENUM('self_checkin', 'meet_host', 'key_lockbox', 'smart_lock') DEFAULT 'self_checkin' COMMENT 'Check-in method',
    ADD COLUMN IF NOT EXISTS checkin_instructions TEXT DEFAULT NULL COMMENT 'Check-in instructions (internal)',
    ADD COLUMN IF NOT EXISTS towels_linens_included TINYINT(1) DEFAULT 1 COMMENT 'Towels and linens provided',
    ADD COLUMN IF NOT EXISTS min_nights INT UNSIGNED DEFAULT 1 COMMENT 'Minimum nights stay',
    ADD COLUMN IF NOT EXISTS instant_booking TINYINT(1) DEFAULT 0 COMMENT 'Instant booking available';

-- Update default accommodation record with sample values
UPDATE accommodation SET
    rating = 4.8,
    reviews_count = 127,
    city = 'Mogadouro',
    region = 'Trás-os-Montes',
    country = 'Portugal',
    host_type = 'superhost',
    checkin_type = 'self_checkin',
    towels_linens_included = 1,
    min_nights = 2,
    instant_booking = 1
WHERE id = 1;

-- =============================================
-- STEP 2: Update accommodation_translations with new fields
-- =============================================
ALTER TABLE accommodation_translations
    ADD COLUMN IF NOT EXISTS location_description TEXT DEFAULT NULL COMMENT 'Description of the location/neighborhood',
    ADD COLUMN IF NOT EXISTS refund_policy TEXT DEFAULT NULL COMMENT 'Refund/cancellation policy text',
    ADD COLUMN IF NOT EXISTS checkin_description VARCHAR(255) DEFAULT NULL COMMENT 'Check-in description for guests',
    ADD COLUMN IF NOT EXISTS host_description TEXT DEFAULT NULL COMMENT 'About the host';

-- Update Portuguese translations
UPDATE accommodation_translations SET
    location_description = 'Localizado no coração de Mogadouro, a poucos passos do centro histórico. A zona é tranquila e segura, ideal para famílias. Perto de restaurantes, supermercados e dos principais pontos turísticos da região de Trás-os-Montes.',
    refund_policy = 'Cancelamento gratuito até 7 dias antes do check-in. Após essa data, será cobrado o valor da primeira noite. Não comparência resulta em cobrança total.',
    checkin_description = 'Self check-in com cofre de chaves. Instruções enviadas 24h antes da chegada.',
    host_description = 'Somos uma família local apaixonada por Mogadouro e Trás-os-Montes. Adoramos partilhar a nossa terra com visitantes de todo o mundo.'
WHERE accommodation_id = 1 AND language_id = 1;

-- Update English translations
UPDATE accommodation_translations SET
    location_description = 'Located in the heart of Mogadouro, just steps from the historic center. The area is quiet and safe, ideal for families. Close to restaurants, supermarkets and the main tourist attractions of the Trás-os-Montes region.',
    refund_policy = 'Free cancellation up to 7 days before check-in. After that date, the first night will be charged. No-show results in full charge.',
    checkin_description = 'Self check-in with lockbox. Instructions sent 24h before arrival.',
    host_description = 'We are a local family passionate about Mogadouro and Trás-os-Montes. We love sharing our land with visitors from all over the world.'
WHERE accommodation_id = 1 AND language_id = 2;

-- =============================================
-- STEP 3: Update bedroom_translations with name field
-- =============================================
ALTER TABLE bedroom_translations
    ADD COLUMN IF NOT EXISTS name VARCHAR(100) DEFAULT NULL COMMENT 'Bedroom name (e.g., Master Suite)';

-- Update bedroom names
UPDATE bedroom_translations SET name = 'Quarto Principal' WHERE bedroom_id = 1 AND language_id = 1;
UPDATE bedroom_translations SET name = 'Master Bedroom' WHERE bedroom_id = 1 AND language_id = 2;
UPDATE bedroom_translations SET name = 'Quarto Duplo' WHERE bedroom_id = 2 AND language_id = 1;
UPDATE bedroom_translations SET name = 'Twin Room' WHERE bedroom_id = 2 AND language_id = 2;
UPDATE bedroom_translations SET name = 'Quarto de Hóspedes' WHERE bedroom_id = 3 AND language_id = 1;
UPDATE bedroom_translations SET name = 'Guest Room' WHERE bedroom_id = 3 AND language_id = 2;

-- =============================================
-- STEP 4: Create bathrooms table
-- =============================================
CREATE TABLE IF NOT EXISTS bathrooms (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    accommodation_id INT UNSIGNED NOT NULL,
    bathroom_number INT UNSIGNED NOT NULL,
    is_ensuite TINYINT(1) DEFAULT 0 COMMENT 'Is this an ensuite bathroom',
    has_shower TINYINT(1) DEFAULT 1,
    has_bathtub TINYINT(1) DEFAULT 0,
    has_bidet TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (accommodation_id) REFERENCES accommodation(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bathroom_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bathroom_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) DEFAULT NULL COMMENT 'Bathroom name',
    description VARCHAR(255) NOT NULL COMMENT 'Bathroom description',
    UNIQUE KEY unique_bathroom_lang (bathroom_id, language_id),
    FOREIGN KEY (bathroom_id) REFERENCES bathrooms(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert default bathrooms
INSERT INTO bathrooms (accommodation_id, bathroom_number, is_ensuite, has_shower, has_bathtub, has_bidet) VALUES
(1, 1, 0, 1, 1, 1),
(1, 2, 0, 1, 0, 0)
ON DUPLICATE KEY UPDATE bathroom_number = VALUES(bathroom_number);

-- Insert bathroom translations
INSERT INTO bathroom_translations (bathroom_id, language_id, name, description) VALUES
(1, 1, 'Casa de Banho Principal', 'Banheira, chuveiro, bidé, secador de cabelo'),
(1, 2, 'Main Bathroom', 'Bathtub, shower, bidet, hair dryer'),
(2, 1, 'Casa de Banho Secundária', 'Chuveiro, lavatório'),
(2, 2, 'Secondary Bathroom', 'Shower, sink')
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- =============================================
-- STEP 5: Update amenities categories and add more amenities
-- =============================================

-- First, update the category enum to include more options
ALTER TABLE amenities MODIFY COLUMN category
    ENUM('general', 'kitchen', 'bedroom', 'bathroom', 'outdoor', 'entertainment', 'safety', 'children', 'sports', 'services')
    DEFAULT 'general';

-- Insert additional amenities
INSERT INTO amenities (icon, category, sort_order, is_active) VALUES
-- Kitchen
('oven', 'kitchen', 20, 1),
('microwave', 'kitchen', 21, 1),
('fridge', 'kitchen', 22, 1),
('coffee-maker', 'kitchen', 23, 1),
('toaster', 'kitchen', 24, 1),
('kettle', 'kitchen', 25, 1),
('cookware', 'kitchen', 26, 1),
-- Bedroom
('bed-linens', 'bedroom', 30, 1),
('extra-pillows', 'bedroom', 31, 1),
('blackout-curtains', 'bedroom', 32, 1),
('hangers', 'bedroom', 33, 1),
-- Bathroom
('hot-water', 'bathroom', 40, 1),
('towels', 'bathroom', 41, 1),
('toiletries', 'bathroom', 42, 1),
-- Safety
('smoke-detector', 'safety', 50, 1),
('fire-extinguisher', 'safety', 51, 1),
('first-aid', 'safety', 52, 1),
('carbon-monoxide', 'safety', 53, 1),
-- Children
('high-chair', 'children', 60, 1),
('crib', 'children', 61, 1),
('baby-bath', 'children', 62, 1),
('child-safety', 'children', 63, 1),
-- Entertainment
('smart-tv', 'entertainment', 70, 1),
('streaming', 'entertainment', 71, 1),
('books', 'entertainment', 72, 1),
('board-games', 'entertainment', 73, 1),
-- Services
('cleaning', 'services', 80, 1),
('luggage-storage', 'services', 81, 1)
ON DUPLICATE KEY UPDATE category = VALUES(category);

-- Get the IDs of newly inserted amenities and add translations
-- Note: We need to handle the auto-increment IDs properly

-- Insert translations for kitchen amenities
INSERT INTO amenity_translations (amenity_id, language_id, name)
SELECT a.id, 1,
    CASE a.icon
        WHEN 'oven' THEN 'Forno'
        WHEN 'microwave' THEN 'Micro-ondas'
        WHEN 'fridge' THEN 'Frigorífico'
        WHEN 'coffee-maker' THEN 'Máquina de café'
        WHEN 'toaster' THEN 'Torradeira'
        WHEN 'kettle' THEN 'Chaleira'
        WHEN 'cookware' THEN 'Utensílios de cozinha'
    END
FROM amenities a WHERE a.icon IN ('oven', 'microwave', 'fridge', 'coffee-maker', 'toaster', 'kettle', 'cookware')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO amenity_translations (amenity_id, language_id, name)
SELECT a.id, 2,
    CASE a.icon
        WHEN 'oven' THEN 'Oven'
        WHEN 'microwave' THEN 'Microwave'
        WHEN 'fridge' THEN 'Refrigerator'
        WHEN 'coffee-maker' THEN 'Coffee maker'
        WHEN 'toaster' THEN 'Toaster'
        WHEN 'kettle' THEN 'Electric kettle'
        WHEN 'cookware' THEN 'Cookware'
    END
FROM amenities a WHERE a.icon IN ('oven', 'microwave', 'fridge', 'coffee-maker', 'toaster', 'kettle', 'cookware')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Bedroom amenity translations
INSERT INTO amenity_translations (amenity_id, language_id, name)
SELECT a.id, 1,
    CASE a.icon
        WHEN 'bed-linens' THEN 'Roupa de cama'
        WHEN 'extra-pillows' THEN 'Almofadas extra'
        WHEN 'blackout-curtains' THEN 'Cortinas blackout'
        WHEN 'hangers' THEN 'Cabides'
    END
FROM amenities a WHERE a.icon IN ('bed-linens', 'extra-pillows', 'blackout-curtains', 'hangers')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO amenity_translations (amenity_id, language_id, name)
SELECT a.id, 2,
    CASE a.icon
        WHEN 'bed-linens' THEN 'Bed linens'
        WHEN 'extra-pillows' THEN 'Extra pillows'
        WHEN 'blackout-curtains' THEN 'Blackout curtains'
        WHEN 'hangers' THEN 'Hangers'
    END
FROM amenities a WHERE a.icon IN ('bed-linens', 'extra-pillows', 'blackout-curtains', 'hangers')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Bathroom amenity translations
INSERT INTO amenity_translations (amenity_id, language_id, name)
SELECT a.id, 1,
    CASE a.icon
        WHEN 'hot-water' THEN 'Água quente'
        WHEN 'towels' THEN 'Toalhas'
        WHEN 'toiletries' THEN 'Artigos de higiene'
    END
FROM amenities a WHERE a.icon IN ('hot-water', 'towels', 'toiletries')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO amenity_translations (amenity_id, language_id, name)
SELECT a.id, 2,
    CASE a.icon
        WHEN 'hot-water' THEN 'Hot water'
        WHEN 'towels' THEN 'Towels'
        WHEN 'toiletries' THEN 'Toiletries'
    END
FROM amenities a WHERE a.icon IN ('hot-water', 'towels', 'toiletries')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Safety amenity translations
INSERT INTO amenity_translations (amenity_id, language_id, name)
SELECT a.id, 1,
    CASE a.icon
        WHEN 'smoke-detector' THEN 'Detetor de fumo'
        WHEN 'fire-extinguisher' THEN 'Extintor'
        WHEN 'first-aid' THEN 'Kit primeiros socorros'
        WHEN 'carbon-monoxide' THEN 'Detetor de monóxido'
    END
FROM amenities a WHERE a.icon IN ('smoke-detector', 'fire-extinguisher', 'first-aid', 'carbon-monoxide')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO amenity_translations (amenity_id, language_id, name)
SELECT a.id, 2,
    CASE a.icon
        WHEN 'smoke-detector' THEN 'Smoke detector'
        WHEN 'fire-extinguisher' THEN 'Fire extinguisher'
        WHEN 'first-aid' THEN 'First aid kit'
        WHEN 'carbon-monoxide' THEN 'Carbon monoxide detector'
    END
FROM amenities a WHERE a.icon IN ('smoke-detector', 'fire-extinguisher', 'first-aid', 'carbon-monoxide')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Children amenity translations
INSERT INTO amenity_translations (amenity_id, language_id, name)
SELECT a.id, 1,
    CASE a.icon
        WHEN 'high-chair' THEN 'Cadeira alta'
        WHEN 'crib' THEN 'Berço'
        WHEN 'baby-bath' THEN 'Banheira bebé'
        WHEN 'child-safety' THEN 'Proteções para crianças'
    END
FROM amenities a WHERE a.icon IN ('high-chair', 'crib', 'baby-bath', 'child-safety')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO amenity_translations (amenity_id, language_id, name)
SELECT a.id, 2,
    CASE a.icon
        WHEN 'high-chair' THEN 'High chair'
        WHEN 'crib' THEN 'Crib'
        WHEN 'baby-bath' THEN 'Baby bath'
        WHEN 'child-safety' THEN 'Child safety gates'
    END
FROM amenities a WHERE a.icon IN ('high-chair', 'crib', 'baby-bath', 'child-safety')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Entertainment amenity translations
INSERT INTO amenity_translations (amenity_id, language_id, name)
SELECT a.id, 1,
    CASE a.icon
        WHEN 'smart-tv' THEN 'Smart TV'
        WHEN 'streaming' THEN 'Streaming (Netflix)'
        WHEN 'books' THEN 'Livros'
        WHEN 'board-games' THEN 'Jogos de tabuleiro'
    END
FROM amenities a WHERE a.icon IN ('smart-tv', 'streaming', 'books', 'board-games')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO amenity_translations (amenity_id, language_id, name)
SELECT a.id, 2,
    CASE a.icon
        WHEN 'smart-tv' THEN 'Smart TV'
        WHEN 'streaming' THEN 'Streaming (Netflix)'
        WHEN 'books' THEN 'Books'
        WHEN 'board-games' THEN 'Board games'
    END
FROM amenities a WHERE a.icon IN ('smart-tv', 'streaming', 'books', 'board-games')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Services amenity translations
INSERT INTO amenity_translations (amenity_id, language_id, name)
SELECT a.id, 1,
    CASE a.icon
        WHEN 'cleaning' THEN 'Limpeza incluída'
        WHEN 'luggage-storage' THEN 'Guarda bagagem'
    END
FROM amenities a WHERE a.icon IN ('cleaning', 'luggage-storage')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO amenity_translations (amenity_id, language_id, name)
SELECT a.id, 2,
    CASE a.icon
        WHEN 'cleaning' THEN 'Cleaning included'
        WHEN 'luggage-storage' THEN 'Luggage storage'
    END
FROM amenities a WHERE a.icon IN ('cleaning', 'luggage-storage')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Link new amenities to accommodation
INSERT INTO accommodation_amenities (accommodation_id, amenity_id)
SELECT 1, a.id FROM amenities a
WHERE a.id NOT IN (SELECT amenity_id FROM accommodation_amenities WHERE accommodation_id = 1)
ON DUPLICATE KEY UPDATE accommodation_id = VALUES(accommodation_id);

-- =============================================
-- STEP 6: Add image field to bedrooms for bedroom photos
-- =============================================
ALTER TABLE bedrooms
    ADD COLUMN IF NOT EXISTS image VARCHAR(255) DEFAULT NULL COMMENT 'Bedroom photo path';

-- =============================================
-- STEP 7: Add image field to bathrooms for bathroom photos
-- =============================================
ALTER TABLE bathrooms
    ADD COLUMN IF NOT EXISTS image VARCHAR(255) DEFAULT NULL COMMENT 'Bathroom photo path';
