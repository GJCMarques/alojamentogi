-- =====================================================
-- Migration: 005_activities_complete_system.sql
-- Description: Complete Activities System with Slugs,
--              Multiple Images, and External Links
-- Date: 2026-02-04
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- =====================================================
-- 1. EXPAND ACTIVITIES TABLE
-- =====================================================

-- Add new columns to activities table
ALTER TABLE `activities`
ADD COLUMN IF NOT EXISTS `cover_image` VARCHAR(255) DEFAULT NULL COMMENT 'Main cover image for the activity' AFTER `image`,
ADD COLUMN IF NOT EXISTS `address` VARCHAR(500) DEFAULT NULL COMMENT 'Full address of the location' AFTER `external_url`,
ADD COLUMN IF NOT EXISTS `phone` VARCHAR(50) DEFAULT NULL COMMENT 'Contact phone number' AFTER `address`,
ADD COLUMN IF NOT EXISTS `website` VARCHAR(500) DEFAULT NULL COMMENT 'Official website URL' AFTER `phone`,
ADD COLUMN IF NOT EXISTS `email` VARCHAR(255) DEFAULT NULL COMMENT 'Contact email' AFTER `website`,
ADD COLUMN IF NOT EXISTS `opening_hours` TEXT DEFAULT NULL COMMENT 'Opening hours JSON or text' AFTER `email`,
ADD COLUMN IF NOT EXISTS `price_range` ENUM('free', 'budget', 'moderate', 'expensive') DEFAULT NULL COMMENT 'Price range indicator' AFTER `opening_hours`,
ADD COLUMN IF NOT EXISTS `google_maps_embed` TEXT DEFAULT NULL COMMENT 'Google Maps iframe embed code' AFTER `price_range`,
ADD COLUMN IF NOT EXISTS `meta_title` VARCHAR(255) DEFAULT NULL COMMENT 'SEO meta title' AFTER `google_maps_embed`,
ADD COLUMN IF NOT EXISTS `meta_description` TEXT DEFAULT NULL COMMENT 'SEO meta description' AFTER `meta_title`,
ADD COLUMN IF NOT EXISTS `views_count` INT(10) UNSIGNED DEFAULT 0 COMMENT 'Number of page views' AFTER `sort_order`;

-- Update category ENUM to include more options
ALTER TABLE `activities`
MODIFY COLUMN `category` ENUM('nature', 'culture', 'gastronomy', 'adventure', 'wellness', 'events', 'restaurants', 'cafes', 'accommodation', 'architecture', 'rural_tourism', 'leisure') DEFAULT 'culture';

-- =====================================================
-- 2. EXPAND ACTIVITY TRANSLATIONS TABLE
-- =====================================================

-- Add full_description and other fields to translations
ALTER TABLE `activity_translations`
ADD COLUMN IF NOT EXISTS `address_description` VARCHAR(500) DEFAULT NULL COMMENT 'Localized address/directions description' AFTER `full_description`,
ADD COLUMN IF NOT EXISTS `opening_hours_text` TEXT DEFAULT NULL COMMENT 'Localized opening hours text' AFTER `address_description`,
ADD COLUMN IF NOT EXISTS `tips` TEXT DEFAULT NULL COMMENT 'Local tips and recommendations' AFTER `opening_hours_text`,
ADD COLUMN IF NOT EXISTS `meta_title` VARCHAR(255) DEFAULT NULL COMMENT 'Localized SEO title' AFTER `tips`,
ADD COLUMN IF NOT EXISTS `meta_description` TEXT DEFAULT NULL COMMENT 'Localized SEO description' AFTER `meta_title`;

-- =====================================================
-- 3. CREATE ACTIVITY IMAGES TABLE (Multiple images)
-- =====================================================

CREATE TABLE IF NOT EXISTS `activity_images` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `activity_id` INT(10) UNSIGNED NOT NULL,
    `image_path` VARCHAR(255) NOT NULL COMMENT 'Path to image file',
    `alt_text_pt` VARCHAR(255) DEFAULT NULL COMMENT 'Portuguese alt text',
    `alt_text_en` VARCHAR(255) DEFAULT NULL COMMENT 'English alt text',
    `caption_pt` VARCHAR(500) DEFAULT NULL COMMENT 'Portuguese caption',
    `caption_en` VARCHAR(500) DEFAULT NULL COMMENT 'English caption',
    `is_cover` TINYINT(1) DEFAULT 0 COMMENT 'Is this the cover/main image',
    `sort_order` INT(10) UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_activity_images_activity` (`activity_id`),
    KEY `idx_activity_images_cover` (`is_cover`),
    KEY `idx_activity_images_order` (`sort_order`),
    CONSTRAINT `fk_activity_images_activity` FOREIGN KEY (`activity_id`)
        REFERENCES `activities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. CREATE EXTERNAL LINKS TABLE (Links about Mogadouro)
-- =====================================================

CREATE TABLE IF NOT EXISTS `external_links` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `url` VARCHAR(500) NOT NULL COMMENT 'External website URL',
    `icon` VARCHAR(100) DEFAULT NULL COMMENT 'Icon class or path',
    `icon_image` VARCHAR(255) DEFAULT NULL COMMENT 'Custom icon image path',
    `category` ENUM('tourism', 'government', 'news', 'gastronomy', 'culture', 'nature', 'events', 'accommodation', 'other') DEFAULT 'tourism',
    `is_featured` TINYINT(1) DEFAULT 0 COMMENT 'Show in featured section',
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT(10) UNSIGNED DEFAULT 0,
    `clicks_count` INT(10) UNSIGNED DEFAULT 0 COMMENT 'Track clicks',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_external_links_active` (`is_active`),
    KEY `idx_external_links_featured` (`is_featured`),
    KEY `idx_external_links_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. CREATE EXTERNAL LINK TRANSLATIONS TABLE
-- =====================================================

CREATE TABLE IF NOT EXISTS `external_link_translations` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `link_id` INT(10) UNSIGNED NOT NULL,
    `language_id` INT(10) UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL COMMENT 'Link title',
    `description` TEXT DEFAULT NULL COMMENT 'Link description',
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_link_language` (`link_id`, `language_id`),
    KEY `idx_external_link_trans_link` (`link_id`),
    KEY `idx_external_link_trans_lang` (`language_id`),
    CONSTRAINT `fk_external_link_trans_link` FOREIGN KEY (`link_id`)
        REFERENCES `external_links` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_external_link_trans_lang` FOREIGN KEY (`language_id`)
        REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. INSERT DEFAULT EXTERNAL LINKS
-- =====================================================

INSERT INTO `external_links` (`url`, `icon`, `category`, `is_featured`, `is_active`, `sort_order`) VALUES
('https://www.cm-mogadouro.pt/', 'building', 'government', 1, 1, 1),
('https://natural.pt/protected-areas/parque-natural-do-douro-internacional', 'tree', 'nature', 1, 1, 2),
('https://www.visitportugal.com/pt-pt/destinos/porto-e-norte/tras-os-montes', 'map', 'tourism', 1, 1, 3),
('https://www.centerofportugal.com/pt/regiao/tras-os-montes/', 'compass', 'tourism', 0, 1, 4),
('https://www.tripadvisor.pt/Tourism-g1169689-Mogadouro_Braganca_District_Northern_Portugal-Vacations.html', 'star', 'tourism', 0, 1, 5);

-- Insert translations for default links
INSERT INTO `external_link_translations` (`link_id`, `language_id`, `title`, `description`) VALUES
(1, 1, 'Câmara Municipal de Mogadouro', 'Site oficial da Câmara Municipal com informações sobre serviços, eventos e notícias locais.'),
(1, 2, 'Mogadouro City Hall', 'Official City Hall website with information about services, events and local news.'),
(2, 1, 'Parque Natural do Douro Internacional', 'Descubra a fauna e flora únicas das Arribas do Douro, um dos últimos refúgios de aves de rapina na Europa.'),
(2, 2, 'Douro International Natural Park', 'Discover the unique fauna and flora of the Douro Cliffs, one of the last refuges for birds of prey in Europe.'),
(3, 1, 'Visit Portugal - Trás-os-Montes', 'Portal oficial de turismo de Portugal com guias e sugestões para explorar a região transmontana.'),
(3, 2, 'Visit Portugal - Trás-os-Montes', 'Official Portugal tourism portal with guides and suggestions to explore the Transmontana region.'),
(4, 1, 'Centro de Portugal - Trás-os-Montes', 'Informações turísticas detalhadas sobre a região, incluindo roteiros e pontos de interesse.'),
(4, 2, 'Center of Portugal - Trás-os-Montes', 'Detailed tourist information about the region, including itineraries and points of interest.'),
(5, 1, 'TripAdvisor - Mogadouro', 'Avaliações e sugestões de viajantes sobre o que fazer e onde comer em Mogadouro.'),
(5, 2, 'TripAdvisor - Mogadouro', 'Reviews and suggestions from travelers about what to do and where to eat in Mogadouro.');

-- =====================================================
-- 7. UPDATE EXISTING ACTIVITIES WITH MORE DATA
-- =====================================================

-- Update Castelo de Mogadouro
UPDATE `activities` SET
    `address` = 'Largo do Castelo, 5200-251 Mogadouro',
    `google_maps_embed` = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3010.1234567890123!2d-6.7134700!3d41.3421700!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDHCsDIwJzMxLjgiTiA2wrA0MicxMC4yIlc!5e0!3m2!1spt-PT!2spt!4v1234567890123!5m2!1spt-PT!2spt" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
    `latitude` = 41.34217,
    `longitude` = -6.71347,
    `price_range` = 'free'
WHERE `slug` = 'castelo-mogadouro';

-- Update activity_translations with full descriptions
UPDATE `activity_translations` SET
    `full_description` = '<p>O Castelo de Mogadouro, também conhecido como Torre de Menagem, é um dos monumentos mais emblemáticos do concelho. Construído no século XIII pelos Templários, esta torre medieval ergue-se majestosamente sobre a vila, oferecendo vistas panorâmicas deslumbrantes sobre a paisagem transmontana.</p><p>A torre, de planta quadrada, é o único elemento que resta do antigo castelo medieval que protegia a povoação. As suas paredes robustas contam histórias de batalhas e conquistas que moldaram a história desta região fronteiriça.</p><p>Visitar o Castelo de Mogadouro é fazer uma viagem no tempo, descobrindo os segredos da Ordem dos Templários e a importância estratégica que esta fortaleza teve na defesa do território português.</p>',
    `tips` = 'Visite ao final da tarde para apreciar o pôr do sol sobre as montanhas. A entrada é gratuita e o local é acessível durante todo o ano.'
WHERE `activity_id` = 1 AND `language_id` = 1;

UPDATE `activity_translations` SET
    `full_description` = '<p>Mogadouro Castle, also known as the Keep Tower, is one of the most emblematic monuments in the municipality. Built in the 13th century by the Templars, this medieval tower rises majestically over the village, offering stunning panoramic views over the Transmontana landscape.</p><p>The square-plan tower is the only remaining element of the old medieval castle that protected the settlement. Its robust walls tell stories of battles and conquests that shaped the history of this border region.</p><p>Visiting Mogadouro Castle is a journey through time, discovering the secrets of the Order of the Templars and the strategic importance this fortress had in the defense of Portuguese territory.</p>',
    `tips` = 'Visit in the late afternoon to enjoy the sunset over the mountains. Admission is free and the site is accessible year-round.'
WHERE `activity_id` = 1 AND `language_id` = 2;

-- Update Parque Natural do Douro
UPDATE `activities` SET
    `address` = 'Parque Natural do Douro Internacional, Miranda do Douro / Mogadouro',
    `website` = 'https://natural.pt/protected-areas/parque-natural-do-douro-internacional',
    `google_maps_embed` = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d95000!2d-6.8!3d41.5!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd3a1f7c5b4a3d2e1%3A0x1234567890abcdef!2sParque+Natural+do+Douro+Internacional!5e0!3m2!1spt-PT!2spt!4v1234567890123!5m2!1spt-PT!2spt" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
    `price_range` = 'free'
WHERE `slug` = 'parque-natural-douro';

UPDATE `activity_translations` SET
    `full_description` = '<p>O Parque Natural do Douro Internacional é uma das áreas protegidas mais impressionantes de Portugal. Estendendo-se ao longo de cerca de 120 km na fronteira com Espanha, este parque preserva uma paisagem única de escarpas profundas talhadas pelo rio Douro.</p><p>As arribas do Douro, com mais de 200 metros de altura em alguns pontos, são o habitat de uma biodiversidade extraordinária. Aqui encontra-se uma das maiores colónias de aves de rapina da Europa, incluindo o abutre-do-egipto, a águia-real, o grifo e a cegonha-preta.</p><p>Os diversos miradouros espalhados pelo parque oferecem vistas de cortar a respiração sobre o canyon do Douro, tornando este local imperdível para amantes da natureza e fotografia.</p>',
    `tips` = 'Traga binóculos para observação de aves. Os melhores horários para avistar as aves de rapina são ao início da manhã e ao final da tarde.'
WHERE `activity_id` = 3 AND `language_id` = 1;

UPDATE `activity_translations` SET
    `full_description` = '<p>The Douro International Natural Park is one of the most impressive protected areas in Portugal. Stretching along about 120 km on the border with Spain, this park preserves a unique landscape of deep cliffs carved by the Douro River.</p><p>The Douro cliffs, over 200 meters high in some points, are home to an extraordinary biodiversity. Here you will find one of the largest colonies of birds of prey in Europe, including the Egyptian vulture, golden eagle, griffon vulture and black stork.</p><p>The various viewpoints scattered throughout the park offer breathtaking views over the Douro canyon, making this place a must-visit for nature and photography lovers.</p>',
    `tips` = 'Bring binoculars for bird watching. The best times to spot birds of prey are early morning and late afternoon.'
WHERE `activity_id` = 3 AND `language_id` = 2;

-- =====================================================
-- 8. ADD MORE ACTIVITIES
-- =====================================================

-- Insert new activities
INSERT INTO `activities` (`slug`, `category`, `address`, `distance_km`, `is_featured`, `is_active`, `sort_order`, `price_range`) VALUES
('restaurante-a-lareira', 'restaurants', 'Av. Nossa Senhora do Caminho, 5200-207 Mogadouro', 0.10, 1, 1, 10, 'moderate'),
('cafe-central-mogadouro', 'cafes', 'Praça do Município, 5200-207 Mogadouro', 0.05, 0, 1, 11, 'budget'),
('feira-medieval-mogadouro', 'events', 'Centro Histórico, 5200-207 Mogadouro', 0.00, 1, 1, 12, 'free'),
('convento-sao-francisco', 'architecture', 'Largo de São Francisco, 5200-207 Mogadouro', 0.30, 1, 1, 13, 'free'),
('praca-do-municipio', 'culture', 'Praça do Município, 5200-207 Mogadouro', 0.00, 0, 1, 14, 'free'),
('rio-sabor', 'nature', 'Vale do Sabor, Mogadouro', 5.00, 1, 1, 15, 'free'),
('gastronomia-transmontana', 'gastronomy', 'Mogadouro e região', 0.00, 1, 1, 16, 'moderate');

-- Get the IDs of newly inserted activities (assuming they start at 6)
SET @rest_id = (SELECT id FROM activities WHERE slug = 'restaurante-a-lareira');
SET @cafe_id = (SELECT id FROM activities WHERE slug = 'cafe-central-mogadouro');
SET @feira_id = (SELECT id FROM activities WHERE slug = 'feira-medieval-mogadouro');
SET @convento_id = (SELECT id FROM activities WHERE slug = 'convento-sao-francisco');
SET @praca_id = (SELECT id FROM activities WHERE slug = 'praca-do-municipio');
SET @sabor_id = (SELECT id FROM activities WHERE slug = 'rio-sabor');
SET @gastro_id = (SELECT id FROM activities WHERE slug = 'gastronomia-transmontana');

-- Insert translations for new activities (Portuguese)
INSERT INTO `activity_translations` (`activity_id`, `language_id`, `title`, `short_description`, `full_description`) VALUES
(@rest_id, 1, 'Restaurante A Lareira', 'Cozinha tradicional transmontana com pratos típicos da região.', '<p>O Restaurante A Lareira é uma referência da gastronomia transmontana em Mogadouro. Com um ambiente acolhedor e rústico, oferece os melhores pratos da região, preparados com ingredientes locais de qualidade.</p><p>Especialidades da casa incluem a famosa posta mirandesa, cabrito assado, enchidos tradicionais e o delicioso folar de carne. A carta de vinhos apresenta uma seleção cuidada de vinhos regionais do Douro.</p>'),
(@cafe_id, 1, 'Café Central', 'Café tradicional no coração de Mogadouro, ideal para um café e pastel.', '<p>Localizado na praça principal de Mogadouro, o Café Central é o ponto de encontro favorito dos locais. Desfrute de um café e de doces tradicionais enquanto observa o dia-a-dia da vila.</p>'),
(@feira_id, 1, 'Feira Medieval de Mogadouro', 'Evento anual que recria a época medieval com mercado, espetáculos e gastronomia.', '<p>A Feira Medieval de Mogadouro é um dos eventos mais aguardados do ano. Durante três dias, o centro histórico transforma-se num autêntico mercado medieval, com artesãos, músicos, malabaristas e espetáculos de falcoaria.</p><p>Prove as iguarias medievais, assista a torneios de cavaleiros e mergulhe na atmosfera única desta festa que celebra a rica história templária de Mogadouro.</p>'),
(@convento_id, 1, 'Convento de São Francisco', 'Antigo convento franciscano do século XIII com arquitetura gótica notável.', '<p>O Convento de São Francisco, fundado no século XIII, é um dos mais importantes monumentos religiosos de Mogadouro. A sua igreja preserva elementos arquitectónicos góticos e manuelinos de grande valor histórico.</p><p>Destaque para os azulejos do século XVIII que decoram o interior e para o claustro sereno que convida à contemplação.</p>'),
(@praca_id, 1, 'Praça do Município', 'Centro nevrálgico de Mogadouro com esplanadas e comércio tradicional.', '<p>A Praça do Município é o coração de Mogadouro. Rodeada de edifícios históricos, é o local ideal para sentir o pulso da vila, tomar um café numa esplanada ou simplesmente observar o quotidiano transmontano.</p>'),
(@sabor_id, 1, 'Rio Sabor', 'Rio de águas cristalinas ideal para passeios, piqueniques e observação da natureza.', '<p>O Rio Sabor serpenteia pela paisagem transmontana oferecendo cenários de beleza natural incomparável. As suas margens são perfeitas para piqueniques, caminhadas ou simplesmente para desfrutar da tranquilidade.</p><p>Em alguns troços, é possível praticar canoagem e outras atividades aquáticas, sempre em harmonia com a natureza preservada.</p>'),
(@gastro_id, 1, 'Gastronomia Transmontana', 'Descubra os sabores únicos da cozinha tradicional de Trás-os-Montes.', '<p>A gastronomia transmontana é uma das mais ricas e autênticas de Portugal. Em Mogadouro, pode saborear pratos que são verdadeiras obras de arte culinária, transmitidos de geração em geração.</p><p>Não deixe de provar a posta mirandesa (carne de raça autóctone), os enchidos (alheira, butelo, salpicão), o folar de carne, a sopa de castanhas e os doces conventuais. O azeite DOP de Trás-os-Montes é considerado um dos melhores do mundo.</p>');

-- Insert translations for new activities (English)
INSERT INTO `activity_translations` (`activity_id`, `language_id`, `title`, `short_description`, `full_description`) VALUES
(@rest_id, 2, 'A Lareira Restaurant', 'Traditional Transmontana cuisine with typical regional dishes.', '<p>A Lareira Restaurant is a reference for Transmontana gastronomy in Mogadouro. With a cozy and rustic atmosphere, it offers the best dishes from the region, prepared with quality local ingredients.</p><p>House specialties include the famous Mirandesa steak, roasted kid, traditional sausages and the delicious meat folar. The wine list features a careful selection of regional Douro wines.</p>'),
(@cafe_id, 2, 'Central Café', 'Traditional café in the heart of Mogadouro, perfect for coffee and pastries.', '<p>Located in Mogadouro''s main square, Central Café is the favorite meeting point for locals. Enjoy a coffee and traditional sweets while watching the village''s daily life.</p>'),
(@feira_id, 2, 'Mogadouro Medieval Fair', 'Annual event recreating the medieval era with market, shows and gastronomy.', '<p>The Mogadouro Medieval Fair is one of the most anticipated events of the year. For three days, the historic center transforms into an authentic medieval market, with artisans, musicians, jugglers and falconry shows.</p><p>Taste medieval delicacies, watch knight tournaments and immerse yourself in the unique atmosphere of this festival that celebrates Mogadouro''s rich Templar history.</p>'),
(@convento_id, 2, 'São Francisco Convent', 'Former 13th century Franciscan convent with notable Gothic architecture.', '<p>The São Francisco Convent, founded in the 13th century, is one of Mogadouro''s most important religious monuments. Its church preserves Gothic and Manueline architectural elements of great historical value.</p><p>Highlights include the 18th century tiles that decorate the interior and the serene cloister that invites contemplation.</p>'),
(@praca_id, 2, 'Municipality Square', 'Mogadouro''s nerve center with terraces and traditional commerce.', '<p>The Municipality Square is the heart of Mogadouro. Surrounded by historic buildings, it''s the ideal place to feel the pulse of the village, have a coffee on a terrace or simply observe the Transmontana daily life.</p>'),
(@sabor_id, 2, 'Sabor River', 'Crystal clear river ideal for walks, picnics and nature observation.', '<p>The Sabor River winds through the Transmontana landscape offering scenes of incomparable natural beauty. Its banks are perfect for picnics, walks or simply enjoying the preserved tranquility.</p><p>In some stretches, it''s possible to practice canoeing and other water activities, always in harmony with preserved nature.</p>'),
(@gastro_id, 2, 'Transmontana Gastronomy', 'Discover the unique flavors of traditional Trás-os-Montes cuisine.', '<p>Transmontana gastronomy is one of the richest and most authentic in Portugal. In Mogadouro, you can taste dishes that are true culinary works of art, passed down from generation to generation.</p><p>Don''t miss the Mirandesa steak (native breed meat), sausages (alheira, butelo, salpicão), meat folar, chestnut soup and convent sweets. The PDO olive oil from Trás-os-Montes is considered one of the best in the world.</p>');

-- =====================================================
-- 9. ADD PAGE HERO FOR ACTIVITIES (if not exists)
-- =====================================================

INSERT INTO `page_heroes` (`page_key`, `hero_image`, `hero_overlay_opacity`, `is_active`)
SELECT 'activities', 'images/MogadouroAtividades.jpg', 0.40, 1
WHERE NOT EXISTS (SELECT 1 FROM `page_heroes` WHERE `page_key` = 'activities');

-- =====================================================
-- 10. CREATE INDEXES FOR PERFORMANCE
-- =====================================================

-- Add index for slug lookups
CREATE INDEX IF NOT EXISTS `idx_activities_slug` ON `activities` (`slug`);
CREATE INDEX IF NOT EXISTS `idx_activities_category` ON `activities` (`category`);
CREATE INDEX IF NOT EXISTS `idx_activities_featured_active` ON `activities` (`is_featured`, `is_active`);

COMMIT;
