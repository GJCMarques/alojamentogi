-- =============================================
-- A Casa do Gi - Dual Accommodation System
-- Support for Casa 1 and Casa 2 with full text control
-- COMPATIBLE VERSION for MySQL 5.7+
-- =============================================

USE casadogi;

-- =============================================
-- STEP 1: Update accommodation table with new fields
-- =============================================
ALTER TABLE accommodation
    ADD COLUMN accommodation_number INT UNSIGNED DEFAULT 1 COMMENT 'Casa 1 or Casa 2',
    ADD COLUMN slug VARCHAR(100) COMMENT 'URL-friendly identifier';

-- Update existing record to be Casa 1
UPDATE accommodation SET accommodation_number = 1, slug = 'casa-do-gi-1' WHERE id = 1;

-- Create Casa 2
INSERT INTO accommodation (accommodation_number, slug, max_guests, bedrooms, bathrooms, area_sqm, floor_number, has_elevator,
                          check_in_time, check_out_time, latitude, longitude, license_number,
                          rating, reviews_count, city, region, country, host_type, checkin_type,
                          towels_linens_included, min_nights, instant_booking, is_active)
SELECT 2, 'casa-do-gi-2', max_guests, bedrooms, bathrooms, area_sqm, floor_number, has_elevator,
       check_in_time, check_out_time, latitude, longitude, license_number,
       rating, reviews_count, city, region, country, host_type, checkin_type,
       towels_linens_included, min_nights, instant_booking, 1
FROM accommodation WHERE id = 1
ON DUPLICATE KEY UPDATE accommodation_number = VALUES(accommodation_number);

-- Get Casa 2 ID
SET @casa2_id = (SELECT id FROM accommodation WHERE accommodation_number = 2 LIMIT 1);

-- Create translations for Casa 2
INSERT INTO accommodation_translations (accommodation_id, language_id, name, tagline, description, location_description, refund_policy, checkin_description, host_description)
SELECT @casa2_id, language_id,
       CONCAT(name, ' 2'),
       tagline,
       description,
       location_description,
       refund_policy,
       checkin_description,
       host_description
FROM accommodation_translations WHERE accommodation_id = 1
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- =============================================
-- STEP 2: Add booking platform URLs to accommodation
-- =============================================
ALTER TABLE accommodation
    ADD COLUMN guestready_url VARCHAR(500) DEFAULT NULL COMMENT 'GuestReady booking URL',
    ADD COLUMN booking_url VARCHAR(500) DEFAULT NULL COMMENT 'Booking.com URL',
    ADD COLUMN airbnb_url VARCHAR(500) DEFAULT NULL COMMENT 'Airbnb URL';

-- =============================================
-- STEP 3: House Rules table
-- =============================================
CREATE TABLE IF NOT EXISTS house_rules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    accommodation_id INT UNSIGNED NOT NULL,
    is_highlighted TINYINT(1) DEFAULT 0 COMMENT 'Show in main section (not just modal)',
    sort_order INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (accommodation_id) REFERENCES accommodation(id) ON DELETE CASCADE,
    INDEX idx_accommodation (accommodation_id),
    INDEX idx_highlighted (is_highlighted)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS house_rule_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rule_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    rule_text TEXT NOT NULL,
    UNIQUE KEY unique_rule_lang (rule_id, language_id),
    FOREIGN KEY (rule_id) REFERENCES house_rules(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert default house rules for Casa 1
INSERT INTO house_rules (accommodation_id, is_highlighted, sort_order) VALUES
(1, 1, 1), (1, 1, 2), (1, 1, 3), (1, 0, 4), (1, 0, 5);

-- Translations for Casa 1 rules
INSERT INTO house_rule_translations (rule_id, language_id, rule_text) VALUES
(1, 1, 'Não são permitidas festas ou eventos.'),
(1, 2, 'No parties or events allowed.'),
(2, 1, 'Horário de silêncio: 22h00 - 08h00.'),
(2, 2, 'Quiet hours: 22:00 - 08:00.'),
(3, 1, 'Proibido fumar no interior.'),
(3, 2, 'No smoking inside.'),
(4, 1, 'Animais de estimação não são permitidos.'),
(4, 2, 'Pets are not allowed.'),
(5, 1, 'Respeite os vizinhos e a propriedade.'),
(5, 2, 'Respect neighbors and property.');

-- Copy rules to Casa 2
INSERT INTO house_rules (accommodation_id, is_highlighted, sort_order)
SELECT @casa2_id, is_highlighted, sort_order FROM house_rules WHERE accommodation_id = 1;

SET @rule_offset = (SELECT MAX(id) FROM house_rules WHERE accommodation_id = 1);

INSERT INTO house_rule_translations (rule_id, language_id, rule_text)
SELECT hr.id, hrt.language_id, hrt.rule_text
FROM house_rules hr
JOIN house_rules hr_orig ON hr.sort_order = hr_orig.sort_order AND hr_orig.accommodation_id = 1
JOIN house_rule_translations hrt ON hrt.rule_id = hr_orig.id
WHERE hr.accommodation_id = @casa2_id;

-- =============================================
-- STEP 4: Highlighted amenities system
-- =============================================
ALTER TABLE accommodation_amenities
    ADD COLUMN is_highlighted TINYINT(1) DEFAULT 0 COMMENT 'Show in main section (top 8)',
    ADD COLUMN sort_order INT UNSIGNED DEFAULT 0 COMMENT 'Display order';

-- Set first 8 amenities as highlighted for Casa 1
UPDATE accommodation_amenities SET is_highlighted = 1, sort_order = (
    SELECT COUNT(*) FROM (SELECT * FROM accommodation_amenities) AS aa2
    WHERE aa2.accommodation_id = accommodation_amenities.accommodation_id
    AND aa2.amenity_id <= accommodation_amenities.amenity_id
) WHERE accommodation_id = 1 LIMIT 8;

-- =============================================
-- STEP 5: Update bedroom and bathroom translations
-- =============================================
-- Add title fields to bedroom translations
ALTER TABLE bedroom_translations
    ADD COLUMN title VARCHAR(50) DEFAULT NULL COMMENT 'Section title like "Dormidas"';

UPDATE bedroom_translations SET title = 'Dormidas' WHERE language_id = 1 AND title IS NULL;
UPDATE bedroom_translations SET title = 'Sleeping Arrangements' WHERE language_id = 2 AND title IS NULL;

-- Add title fields to bathroom translations
ALTER TABLE bathroom_translations
    ADD COLUMN title VARCHAR(50) DEFAULT NULL COMMENT 'Section title like "Higiene"';

UPDATE bathroom_translations SET title = 'Higiene' WHERE language_id = 1 AND title IS NULL;
UPDATE bathroom_translations SET title = 'Bathrooms' WHERE language_id = 2 AND title IS NULL;

-- =============================================
-- STEP 6: Copy bedrooms and bathrooms to Casa 2
-- =============================================
-- Copy bedrooms
INSERT INTO bedrooms (accommodation_id, bedroom_number, image)
SELECT @casa2_id, bedroom_number, image FROM bedrooms WHERE accommodation_id = 1;

-- Copy bedroom translations
INSERT INTO bedroom_translations (bedroom_id, language_id, name, beds_description, title)
SELECT b2.id, bt.language_id, bt.name, bt.beds_description, bt.title
FROM bedrooms b1
JOIN bedrooms b2 ON b1.bedroom_number = b2.bedroom_number AND b1.accommodation_id = 1 AND b2.accommodation_id = @casa2_id
JOIN bedroom_translations bt ON bt.bedroom_id = b1.id;

-- Copy bathrooms
INSERT INTO bathrooms (accommodation_id, bathroom_number, is_ensuite, has_shower, has_bathtub, has_bidet, image)
SELECT @casa2_id, bathroom_number, is_ensuite, has_shower, has_bathtub, has_bidet, image
FROM bathrooms WHERE accommodation_id = 1;

-- Copy bathroom translations
INSERT INTO bathroom_translations (bathroom_id, language_id, name, description, title)
SELECT b2.id, bt.language_id, bt.name, bt.description, bt.title
FROM bathrooms b1
JOIN bathrooms b2 ON b1.bathroom_number = b2.bathroom_number AND b1.accommodation_id = 1 AND b2.accommodation_id = @casa2_id
JOIN bathroom_translations bt ON bt.bathroom_id = b1.id;

-- =============================================
-- STEP 7: Copy amenities to Casa 2
-- =============================================
INSERT INTO accommodation_amenities (accommodation_id, amenity_id, is_highlighted, sort_order)
SELECT @casa2_id, amenity_id, is_highlighted, sort_order
FROM accommodation_amenities WHERE accommodation_id = 1;

-- =============================================
-- STEP 8: Separate gallery images by accommodation
-- =============================================
ALTER TABLE media
    ADD COLUMN accommodation_id INT UNSIGNED DEFAULT NULL COMMENT 'Link to specific accommodation';

-- Link existing gallery images to Casa 1
UPDATE media SET accommodation_id = 1 WHERE category = 'gallery' AND accommodation_id IS NULL;

-- =============================================
-- STEP 9: Cancellation policy text field
-- =============================================
ALTER TABLE accommodation_translations
    ADD COLUMN cancellation_policy TEXT DEFAULT NULL COMMENT 'Cancellation policy text';

UPDATE accommodation_translations SET
    cancellation_policy = 'Cancelamento gratuito até 30 dias antes do check-in. Cancelamentos após este período sujeitos a taxas de acordo com a plataforma de reserva.'
WHERE language_id = 1 AND cancellation_policy IS NULL;

UPDATE accommodation_translations SET
    cancellation_policy = 'Free cancellation up to 30 days before check-in. Cancellations after this period subject to fees according to the booking platform.'
WHERE language_id = 2 AND cancellation_policy IS NULL;

-- =============================================
-- STEP 10: Add unique index for accommodation_number
-- =============================================
ALTER TABLE accommodation
    ADD UNIQUE KEY unique_accommodation_number (accommodation_number);

-- =============================================
-- STEP 11: Activity section title/description
-- =============================================
ALTER TABLE accommodation_translations
    ADD COLUMN activity_section_title VARCHAR(255) DEFAULT NULL COMMENT 'Title for activities section',
    ADD COLUMN activity_section_description TEXT DEFAULT NULL COMMENT 'Description for activities section';

UPDATE accommodation_translations SET
    activity_section_title = 'Mogadouro & Envolvência',
    activity_section_description = 'Mogadouro é uma vila histórica no coração do Planalto Mirandês, onde a tradição se funde com a natureza. A partir da Casa do Gi, poderá explorar o Castelo de Mogadouro, percorrer trilhos no Parque Natural do Douro Internacional e saborear a gastronomia local única.'
WHERE language_id = 1;

UPDATE accommodation_translations SET
    activity_section_title = 'Mogadouro & Surroundings',
    activity_section_description = 'Mogadouro is a historic town in the heart of the Mirandês Plateau, where tradition merges with nature. From Casa do Gi, you can explore Mogadouro Castle, walk trails in the Douro International Natural Park and savor the unique local gastronomy.'
WHERE language_id = 2;
