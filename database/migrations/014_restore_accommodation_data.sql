-- =============================================
-- Migration 014: Restore accommodation data
-- Fixes accommodation settings that were zeroed out
-- and ensures all bedrooms, bathrooms, and house rules
-- exist with proper PT/EN translations for both casas.
-- Safe to re-run (uses ON DUPLICATE KEY UPDATE).
-- =============================================

-- =============================================
-- STEP 1: Restore accommodation basic settings
-- =============================================
UPDATE accommodation SET
    max_guests = 6,
    bedrooms = 3,
    bathrooms = 2,
    area_sqm = 100.00,
    floor_number = 1,
    has_elevator = 0,
    check_in_time = '16:00:00',
    check_out_time = '11:00:00',
    latitude = 41.34217000,
    longitude = -6.71347000,
    license_number = '146729/AL',
    city = 'Mogadouro',
    region = 'Tras-os-Montes',
    country = 'Portugal',
    host_type = 'standard',
    checkin_type = 'self_checkin',
    towels_linens_included = 1,
    min_nights = 1,
    instant_booking = 0,
    is_active = 1
WHERE accommodation_number = 1;

UPDATE accommodation SET
    max_guests = 6,
    bedrooms = 3,
    bathrooms = 2,
    area_sqm = 100.00,
    floor_number = 1,
    has_elevator = 0,
    check_in_time = '16:00:00',
    check_out_time = '11:00:00',
    latitude = 41.34217000,
    longitude = -6.71347000,
    license_number = '146729/AL',
    city = 'Mogadouro',
    region = 'Tras-os-Montes',
    country = 'Portugal',
    host_type = 'standard',
    checkin_type = 'self_checkin',
    towels_linens_included = 1,
    min_nights = 1,
    instant_booking = 0,
    is_active = 1
WHERE accommodation_number = 2;

-- =============================================
-- STEP 2: Restore accommodation translations
-- =============================================
UPDATE accommodation_translations SET
    name = 'A Casa do Gi',
    tagline = 'Simplicidade, acolhimento e muito amor',
    description = 'A Casa do Gi e sinonimo de simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor! Construida nos anos 80, altura em que os artistas da construcao e os materiais eram escassos por Terras de Mogadouro.'
WHERE accommodation_id = (SELECT id FROM accommodation WHERE accommodation_number = 1)
  AND language_id = (SELECT id FROM languages WHERE code = 'pt');

UPDATE accommodation_translations SET
    name = 'A Casa do Gi',
    tagline = 'Simplicity, warmth and love',
    description = 'A Casa do Gi is synonymous with simplicity, welcoming, remarkable moments of conviviality, warmth of family, joy, fun, laughter and a lot of love! Built in the 80s, when construction artists and materials were scarce in the lands of Mogadouro.'
WHERE accommodation_id = (SELECT id FROM accommodation WHERE accommodation_number = 1)
  AND language_id = (SELECT id FROM languages WHERE code = 'en');

UPDATE accommodation_translations SET
    name = 'A Casa do Gi 2',
    tagline = 'Simplicidade, acolhimento e muito amor',
    description = 'A Casa do Gi e sinonimo de simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor! Construida nos anos 80, altura em que os artistas da construcao e os materiais eram escassos por Terras de Mogadouro.'
WHERE accommodation_id = (SELECT id FROM accommodation WHERE accommodation_number = 2)
  AND language_id = (SELECT id FROM languages WHERE code = 'pt');

UPDATE accommodation_translations SET
    name = 'A Casa do Gi 2',
    tagline = 'Simplicity, warmth and love',
    description = 'A Casa do Gi is synonymous with simplicity, welcoming, remarkable moments of conviviality, warmth of family, joy, fun, laughter and a lot of love! Built in the 80s, when construction artists and materials were scarce in the lands of Mogadouro.'
WHERE accommodation_id = (SELECT id FROM accommodation WHERE accommodation_number = 2)
  AND language_id = (SELECT id FROM languages WHERE code = 'en');

-- =============================================
-- STEP 3: Clean and re-insert bedrooms for Casa 1
-- =============================================
SET @casa1_id = (SELECT id FROM accommodation WHERE accommodation_number = 1);
SET @casa2_id = (SELECT id FROM accommodation WHERE accommodation_number = 2);
SET @pt_id = (SELECT id FROM languages WHERE code = 'pt');
SET @en_id = (SELECT id FROM languages WHERE code = 'en');

-- Remove existing bedrooms for both casas (cascades to translations)
DELETE FROM bedrooms WHERE accommodation_id = @casa1_id;
DELETE FROM bedrooms WHERE accommodation_id = @casa2_id;

-- Casa 1 bedrooms
INSERT INTO bedrooms (accommodation_id, bedroom_number) VALUES
(@casa1_id, 1),
(@casa1_id, 2),
(@casa1_id, 3);

SET @bed1_id = (SELECT id FROM bedrooms WHERE accommodation_id = @casa1_id AND bedroom_number = 1);
SET @bed2_id = (SELECT id FROM bedrooms WHERE accommodation_id = @casa1_id AND bedroom_number = 2);
SET @bed3_id = (SELECT id FROM bedrooms WHERE accommodation_id = @casa1_id AND bedroom_number = 3);

INSERT INTO bedroom_translations (bedroom_id, language_id, name, beds_description) VALUES
(@bed1_id, @pt_id, 'Quarto Principal', '2 camas de solteiro'),
(@bed1_id, @en_id, 'Master Bedroom', '2 single beds'),
(@bed2_id, @pt_id, 'Quarto Duplo', 'Sofa-cama de solteiro, Cama de casal'),
(@bed2_id, @en_id, 'Twin Room', 'Single sofa bed, Double bed'),
(@bed3_id, @pt_id, 'Quarto de Hospedes', 'Cama de casal'),
(@bed3_id, @en_id, 'Guest Room', 'Double bed');

-- Casa 2 bedrooms (same layout)
INSERT INTO bedrooms (accommodation_id, bedroom_number) VALUES
(@casa2_id, 1),
(@casa2_id, 2),
(@casa2_id, 3);

SET @bed4_id = (SELECT id FROM bedrooms WHERE accommodation_id = @casa2_id AND bedroom_number = 1);
SET @bed5_id = (SELECT id FROM bedrooms WHERE accommodation_id = @casa2_id AND bedroom_number = 2);
SET @bed6_id = (SELECT id FROM bedrooms WHERE accommodation_id = @casa2_id AND bedroom_number = 3);

INSERT INTO bedroom_translations (bedroom_id, language_id, name, beds_description) VALUES
(@bed4_id, @pt_id, 'Quarto Principal', '2 camas de solteiro'),
(@bed4_id, @en_id, 'Master Bedroom', '2 single beds'),
(@bed5_id, @pt_id, 'Quarto Duplo', 'Sofa-cama de solteiro, Cama de casal'),
(@bed5_id, @en_id, 'Twin Room', 'Single sofa bed, Double bed'),
(@bed6_id, @pt_id, 'Quarto de Hospedes', 'Cama de casal'),
(@bed6_id, @en_id, 'Guest Room', 'Double bed');

-- =============================================
-- STEP 4: Clean and re-insert bathrooms for both casas
-- =============================================

-- Remove existing bathrooms (cascades to translations)
DELETE FROM bathrooms WHERE accommodation_id = @casa1_id;
DELETE FROM bathrooms WHERE accommodation_id = @casa2_id;

-- Casa 1 bathrooms
INSERT INTO bathrooms (accommodation_id, bathroom_number, is_ensuite, has_shower, has_bathtub, has_bidet) VALUES
(@casa1_id, 1, 0, 1, 1, 1),
(@casa1_id, 2, 0, 1, 0, 0);

SET @bath1_id = (SELECT id FROM bathrooms WHERE accommodation_id = @casa1_id AND bathroom_number = 1);
SET @bath2_id = (SELECT id FROM bathrooms WHERE accommodation_id = @casa1_id AND bathroom_number = 2);

INSERT INTO bathroom_translations (bathroom_id, language_id, name, description) VALUES
(@bath1_id, @pt_id, 'Casa de Banho Principal', 'Banheira, chuveiro, bide, secador de cabelo'),
(@bath1_id, @en_id, 'Main Bathroom', 'Bathtub, shower, bidet, hair dryer'),
(@bath2_id, @pt_id, 'Casa de Banho Secundaria', 'Chuveiro, lavatorio'),
(@bath2_id, @en_id, 'Secondary Bathroom', 'Shower, sink');

-- Casa 2 bathrooms (same layout)
INSERT INTO bathrooms (accommodation_id, bathroom_number, is_ensuite, has_shower, has_bathtub, has_bidet) VALUES
(@casa2_id, 1, 0, 1, 1, 1),
(@casa2_id, 2, 0, 1, 0, 0);

SET @bath3_id = (SELECT id FROM bathrooms WHERE accommodation_id = @casa2_id AND bathroom_number = 1);
SET @bath4_id = (SELECT id FROM bathrooms WHERE accommodation_id = @casa2_id AND bathroom_number = 2);

INSERT INTO bathroom_translations (bathroom_id, language_id, name, description) VALUES
(@bath3_id, @pt_id, 'Casa de Banho Principal', 'Banheira, chuveiro, bide, secador de cabelo'),
(@bath3_id, @en_id, 'Main Bathroom', 'Bathtub, shower, bidet, hair dryer'),
(@bath4_id, @pt_id, 'Casa de Banho Secundaria', 'Chuveiro, lavatorio'),
(@bath4_id, @en_id, 'Secondary Bathroom', 'Shower, sink');

-- =============================================
-- STEP 5: Clean and re-insert house rules for both casas
-- =============================================

-- Remove existing rules (cascades to translations)
DELETE FROM house_rules WHERE accommodation_id = @casa1_id;
DELETE FROM house_rules WHERE accommodation_id = @casa2_id;

-- Casa 1 rules
INSERT INTO house_rules (accommodation_id, is_highlighted, sort_order) VALUES
(@casa1_id, 1, 1),
(@casa1_id, 1, 2),
(@casa1_id, 1, 3),
(@casa1_id, 0, 4),
(@casa1_id, 0, 5);

SET @rule1_id = (SELECT id FROM house_rules WHERE accommodation_id = @casa1_id AND sort_order = 1);
SET @rule2_id = (SELECT id FROM house_rules WHERE accommodation_id = @casa1_id AND sort_order = 2);
SET @rule3_id = (SELECT id FROM house_rules WHERE accommodation_id = @casa1_id AND sort_order = 3);
SET @rule4_id = (SELECT id FROM house_rules WHERE accommodation_id = @casa1_id AND sort_order = 4);
SET @rule5_id = (SELECT id FROM house_rules WHERE accommodation_id = @casa1_id AND sort_order = 5);

INSERT INTO house_rule_translations (rule_id, language_id, rule_text) VALUES
(@rule1_id, @pt_id, 'Nao sao permitidas festas ou eventos.'),
(@rule1_id, @en_id, 'No parties or events allowed.'),
(@rule2_id, @pt_id, 'Horario de silencio: 22h00 - 08h00.'),
(@rule2_id, @en_id, 'Quiet hours: 22:00 - 08:00.'),
(@rule3_id, @pt_id, 'Proibido fumar no interior.'),
(@rule3_id, @en_id, 'No smoking inside.'),
(@rule4_id, @pt_id, 'Animais de estimacao nao sao permitidos.'),
(@rule4_id, @en_id, 'Pets are not allowed.'),
(@rule5_id, @pt_id, 'Respeite os vizinhos e a propriedade.'),
(@rule5_id, @en_id, 'Respect neighbors and property.');

-- Casa 2 rules (same)
INSERT INTO house_rules (accommodation_id, is_highlighted, sort_order) VALUES
(@casa2_id, 1, 1),
(@casa2_id, 1, 2),
(@casa2_id, 1, 3),
(@casa2_id, 0, 4),
(@casa2_id, 0, 5);

SET @rule6_id = (SELECT id FROM house_rules WHERE accommodation_id = @casa2_id AND sort_order = 1);
SET @rule7_id = (SELECT id FROM house_rules WHERE accommodation_id = @casa2_id AND sort_order = 2);
SET @rule8_id = (SELECT id FROM house_rules WHERE accommodation_id = @casa2_id AND sort_order = 3);
SET @rule9_id = (SELECT id FROM house_rules WHERE accommodation_id = @casa2_id AND sort_order = 4);
SET @rule10_id = (SELECT id FROM house_rules WHERE accommodation_id = @casa2_id AND sort_order = 5);

INSERT INTO house_rule_translations (rule_id, language_id, rule_text) VALUES
(@rule6_id, @pt_id, 'Nao sao permitidas festas ou eventos.'),
(@rule6_id, @en_id, 'No parties or events allowed.'),
(@rule7_id, @pt_id, 'Horario de silencio: 22h00 - 08h00.'),
(@rule7_id, @en_id, 'Quiet hours: 22:00 - 08:00.'),
(@rule8_id, @pt_id, 'Proibido fumar no interior.'),
(@rule8_id, @en_id, 'No smoking inside.'),
(@rule9_id, @pt_id, 'Animais de estimacao nao sao permitidos.'),
(@rule9_id, @en_id, 'Pets are not allowed.'),
(@rule10_id, @pt_id, 'Respeite os vizinhos e a propriedade.'),
(@rule10_id, @en_id, 'Respect neighbors and property.');
