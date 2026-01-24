-- =============================================
-- A Casa do Gi - Database Schema
-- Character Set: utf8mb4 (full Unicode support)
-- Collation: utf8mb4_unicode_ci
-- =============================================

CREATE DATABASE IF NOT EXISTS casadogi
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE casadogi;

-- =============================================
-- TABLE: languages
-- Supported languages for the CMS
-- =============================================
CREATE TABLE IF NOT EXISTS languages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(5) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL,
    locale VARCHAR(10) NOT NULL,
    flag_icon VARCHAR(10) DEFAULT NULL,
    is_default TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

INSERT INTO languages (code, name, locale, flag_icon, is_default, is_active) VALUES
('pt', 'Portugues', 'pt_PT', 'pt', 1, 1),
('en', 'English', 'en_GB', 'gb', 0, 1)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- =============================================
-- TABLE: admins
-- Back office administrator accounts
-- =============================================
CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin', 'editor') DEFAULT 'editor',
    avatar VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME DEFAULT NULL,
    login_attempts INT UNSIGNED DEFAULT 0,
    locked_until DATETIME DEFAULT NULL,
    password_reset_token VARCHAR(255) DEFAULT NULL,
    password_reset_expires DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- Default admin user (password: admin123 - CHANGE IN PRODUCTION!)
INSERT INTO admins (username, email, password_hash, full_name, role) VALUES
('admin', 'admin@acasadogi.pt', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4.n2vJh9PqGvYGKy', 'Administrador', 'super_admin')
ON DUPLICATE KEY UPDATE full_name=VALUES(full_name);

-- =============================================
-- TABLE: settings
-- Global site configuration (key-value)
-- =============================================
CREATE TABLE IF NOT EXISTS settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('text', 'textarea', 'boolean', 'number', 'json', 'email', 'url') DEFAULT 'text',
    setting_group VARCHAR(50) DEFAULT 'general',
    description VARCHAR(255) DEFAULT NULL,
    is_public TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key),
    INDEX idx_group (setting_group)
) ENGINE=InnoDB;

-- Default settings
INSERT INTO settings (setting_key, setting_value, setting_type, setting_group, description, is_public) VALUES
('site_name', 'A Casa do Gi', 'text', 'general', 'Nome do site', 1),
('site_tagline_pt', 'Alojamento Local em Mogadouro', 'text', 'general', 'Tagline PT', 1),
('site_tagline_en', 'Local Accommodation in Mogadouro', 'text', 'general', 'Tagline EN', 1),
('contact_email', 'xana.pires73@gmail.com', 'email', 'contact', 'Email principal', 1),
('contact_phone', '+351 479 117 027', 'text', 'contact', 'Telefone', 1),
('contact_address', 'Av. N. Sr. do Caminho 52, 5200-207 Mogadouro', 'textarea', 'contact', 'Morada', 1),
('contact_form_enabled', '1', 'boolean', 'contact', 'Formulario ativo', 0),
('facebook_url', '', 'url', 'social', 'URL Facebook', 1),
('instagram_url', '', 'url', 'social', 'URL Instagram', 1),
('booking_url', '', 'url', 'booking', 'URL Booking.com', 1),
('airbnb_url', '', 'url', 'booking', 'URL Airbnb', 1),
('guestready_url', 'https://book.guestready.com/pt/properties/mogadouro/fuga-ecletica-em-mogadouro/72622', 'url', 'booking', 'URL GuestReady', 1),
('shop_enabled', '1', 'boolean', 'shop', 'Loja ativa', 0),
('shop_shipping_fee', '5.00', 'number', 'shop', 'Taxa de envio', 0),
('shop_free_shipping_above', '50.00', 'number', 'shop', 'Portes gratis acima de', 0),
('maintenance_mode', '0', 'boolean', 'general', 'Modo manutencao', 0)
ON DUPLICATE KEY UPDATE description=VALUES(description);

-- =============================================
-- TABLE: content_blocks
-- CMS content with multi-language support
-- =============================================
CREATE TABLE IF NOT EXISTS content_blocks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    block_key VARCHAR(100) NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    content_type ENUM('text', 'textarea', 'html', 'json') DEFAULT 'text',
    content TEXT,
    page VARCHAR(50) DEFAULT NULL,
    section VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_block_lang (block_key, language_id),
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE,
    INDEX idx_block_key (block_key),
    INDEX idx_page (page),
    INDEX idx_section (section)
) ENGINE=InnoDB;

-- Default content blocks (Portuguese)
INSERT INTO content_blocks (block_key, language_id, content_type, content, page, section) VALUES
('hero_title', 1, 'text', 'A Casa do Gi', 'home', 'hero'),
('hero_subtitle', 1, 'textarea', 'Simplicidade, acolhimento e muito amor em Mogadouro', 'home', 'hero'),
('hero_cta', 1, 'text', 'Descobrir', 'home', 'hero'),
('about_title', 1, 'text', 'A Nossa Historia', 'home', 'about'),
('about_text', 1, 'html', '<p>Construida nos anos 80, altura em que os "artistas da construcao" e os "materiais" eram escassos por Terras de Mogadouro, este edificio foi mandado construir desde terras de Santa Cruz, por carta, e com os recursos de quem saiu da terra em busca de uma melhor oportunidade!</p><p>A Casa do Gi... e sinonimo de simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor!</p>', 'home', 'about'),
('accommodation_title', 1, 'text', 'O Alojamento', 'accommodation', 'main'),
('accommodation_intro', 1, 'textarea', 'Fuga ecletica em Mogadouro - Casa de ferias de 100m2, perfeita para 6 hospedes', 'accommodation', 'main'),
('shop_title', 1, 'text', 'Produtos Regionais', 'shop', 'main'),
('shop_intro', 1, 'textarea', 'Descubra os sabores autenticos de Mogadouro e Tras-os-Montes', 'shop', 'main'),
('activities_title', 1, 'text', 'O Que Fazer', 'activities', 'main'),
('activities_intro', 1, 'textarea', 'Descubra as maravilhas de Mogadouro e arredores', 'activities', 'main'),
('contact_title', 1, 'text', 'Contacte-nos', 'contact', 'main'),
('contact_intro', 1, 'textarea', 'Tem alguma questao? Entre em contacto connosco', 'contact', 'main')
ON DUPLICATE KEY UPDATE content=VALUES(content);

-- Default content blocks (English)
INSERT INTO content_blocks (block_key, language_id, content_type, content, page, section) VALUES
('hero_title', 2, 'text', 'A Casa do Gi', 'home', 'hero'),
('hero_subtitle', 2, 'textarea', 'Simplicity, warmth and love in Mogadouro', 'home', 'hero'),
('hero_cta', 2, 'text', 'Discover', 'home', 'hero'),
('about_title', 2, 'text', 'Our Story', 'home', 'about'),
('about_text', 2, 'html', '<p>Built in the 80s, when "construction artists" and "materials" were scarce in the lands of Mogadouro, this building was commissioned from the lands of Santa Cruz, by letter, and with the resources of those who left the land in search of a better opportunity!</p><p>A Casa do Gi... is synonymous with simplicity, welcoming, remarkable moments of conviviality, warmth of family, joy, fun, laughter and a lot of love!</p>', 'home', 'about'),
('accommodation_title', 2, 'text', 'The Accommodation', 'accommodation', 'main'),
('accommodation_intro', 2, 'textarea', 'Eclectic getaway in Mogadouro - 100m2 holiday home, perfect for 6 guests', 'accommodation', 'main'),
('shop_title', 2, 'text', 'Regional Products', 'shop', 'main'),
('shop_intro', 2, 'textarea', 'Discover the authentic flavors of Mogadouro and Tras-os-Montes', 'shop', 'main'),
('activities_title', 2, 'text', 'Things To Do', 'activities', 'main'),
('activities_intro', 2, 'textarea', 'Discover the wonders of Mogadouro and surroundings', 'activities', 'main'),
('contact_title', 2, 'text', 'Contact Us', 'contact', 'main'),
('contact_intro', 2, 'textarea', 'Have a question? Get in touch with us', 'contact', 'main')
ON DUPLICATE KEY UPDATE content=VALUES(content);

-- =============================================
-- TABLE: media
-- Media library for uploaded files
-- =============================================
CREATE TABLE IF NOT EXISTS media (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    alt_text_pt VARCHAR(255) DEFAULT NULL,
    alt_text_en VARCHAR(255) DEFAULT NULL,
    category ENUM('gallery', 'products', 'activities', 'content', 'other') DEFAULT 'other',
    sort_order INT UNSIGNED DEFAULT 0,
    uploaded_by INT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_category (category),
    INDEX idx_sort (sort_order)
) ENGINE=InnoDB;

-- =============================================
-- TABLE: accommodation
-- Accommodation details and configuration
-- =============================================
CREATE TABLE IF NOT EXISTS accommodation (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL DEFAULT 'casa-do-gi',
    max_guests INT UNSIGNED DEFAULT 6,
    bedrooms INT UNSIGNED DEFAULT 3,
    bathrooms INT UNSIGNED DEFAULT 2,
    area_sqm DECIMAL(6,2) DEFAULT 100.00,
    floor_number INT DEFAULT 1,
    has_elevator TINYINT(1) DEFAULT 0,
    check_in_time TIME DEFAULT '16:00:00',
    check_out_time TIME DEFAULT '11:00:00',
    latitude DECIMAL(10,8) DEFAULT NULL,
    longitude DECIMAL(11,8) DEFAULT NULL,
    license_number VARCHAR(50) DEFAULT '146729/AL',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB;

INSERT INTO accommodation (slug, max_guests, bedrooms, bathrooms, area_sqm, floor_number, license_number) VALUES
('casa-do-gi', 6, 3, 2, 100.00, 1, '146729/AL')
ON DUPLICATE KEY UPDATE max_guests=VALUES(max_guests);

-- =============================================
-- TABLE: accommodation_translations
-- Multi-language accommodation text content
-- =============================================
CREATE TABLE IF NOT EXISTS accommodation_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    accommodation_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    tagline VARCHAR(255) DEFAULT NULL,
    description TEXT,
    house_rules TEXT,
    UNIQUE KEY unique_acc_lang (accommodation_id, language_id),
    FOREIGN KEY (accommodation_id) REFERENCES accommodation(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO accommodation_translations (accommodation_id, language_id, name, tagline, description) VALUES
(1, 1, 'A Casa do Gi', 'Simplicidade, acolhimento e muito amor', 'A Casa do Gi e sinonimo de simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor! Construida nos anos 80, altura em que os artistas da construcao e os materiais eram escassos por Terras de Mogadouro.'),
(1, 2, 'A Casa do Gi', 'Simplicity, warmth and love', 'A Casa do Gi is synonymous with simplicity, welcoming, remarkable moments of conviviality, warmth of family, joy, fun, laughter and a lot of love! Built in the 80s, when construction artists and materials were scarce in the lands of Mogadouro.')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- =============================================
-- TABLE: amenities
-- Accommodation amenities/features
-- =============================================
CREATE TABLE IF NOT EXISTS amenities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50) NOT NULL,
    category ENUM('general', 'kitchen', 'bedroom', 'bathroom', 'outdoor', 'entertainment', 'safety') DEFAULT 'general',
    sort_order INT UNSIGNED DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- TABLE: amenity_translations
-- Multi-language amenity names
-- =============================================
CREATE TABLE IF NOT EXISTS amenity_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    amenity_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    UNIQUE KEY unique_amenity_lang (amenity_id, language_id),
    FOREIGN KEY (amenity_id) REFERENCES amenities(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert default amenities
INSERT INTO amenities (icon, category, sort_order) VALUES
('wifi', 'general', 1),
('ac', 'general', 2),
('heater', 'general', 3),
('parking', 'general', 4),
('pool-private', 'outdoor', 5),
('pool-shared', 'outdoor', 6),
('garden', 'outdoor', 7),
('terrace', 'outdoor', 8),
('washing-machine', 'general', 9),
('dishwasher', 'kitchen', 10),
('hairdryer', 'bathroom', 11),
('workspace', 'general', 12);

INSERT INTO amenity_translations (amenity_id, language_id, name) VALUES
(1, 1, 'Internet Wifi'), (1, 2, 'Wifi Internet'),
(2, 1, 'Ar condicionado'), (2, 2, 'Air conditioning'),
(3, 1, 'Aquecedores'), (3, 2, 'Heaters'),
(4, 1, 'Estacionamento incluido'), (4, 2, 'Parking included'),
(5, 1, 'Piscina privada'), (5, 2, 'Private pool'),
(6, 1, 'Piscina partilhada'), (6, 2, 'Shared pool'),
(7, 1, 'Jardim'), (7, 2, 'Garden'),
(8, 1, 'Terraco'), (8, 2, 'Terrace'),
(9, 1, 'Maquina de lavar'), (9, 2, 'Washing machine'),
(10, 1, 'Lava-louca'), (10, 2, 'Dishwasher'),
(11, 1, 'Secador de cabelo'), (11, 2, 'Hair dryer'),
(12, 1, 'Area de trabalho para portatil'), (12, 2, 'Laptop workspace');

-- =============================================
-- TABLE: accommodation_amenities (junction table)
-- =============================================
CREATE TABLE IF NOT EXISTS accommodation_amenities (
    accommodation_id INT UNSIGNED NOT NULL,
    amenity_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (accommodation_id, amenity_id),
    FOREIGN KEY (accommodation_id) REFERENCES accommodation(id) ON DELETE CASCADE,
    FOREIGN KEY (amenity_id) REFERENCES amenities(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Link all amenities to accommodation
INSERT INTO accommodation_amenities (accommodation_id, amenity_id)
SELECT 1, id FROM amenities;

-- =============================================
-- TABLE: bedrooms
-- Bedroom configuration
-- =============================================
CREATE TABLE IF NOT EXISTS bedrooms (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    accommodation_id INT UNSIGNED NOT NULL,
    bedroom_number INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (accommodation_id) REFERENCES accommodation(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bedroom_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bedroom_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    beds_description VARCHAR(255) NOT NULL,
    UNIQUE KEY unique_bedroom_lang (bedroom_id, language_id),
    FOREIGN KEY (bedroom_id) REFERENCES bedrooms(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO bedrooms (accommodation_id, bedroom_number) VALUES (1, 1), (1, 2), (1, 3);

INSERT INTO bedroom_translations (bedroom_id, language_id, beds_description) VALUES
(1, 1, '2 camas de solteiro'), (1, 2, '2 single beds'),
(2, 1, 'Sofa-cama de solteiro, Cama de casal'), (2, 2, 'Single sofa bed, Double bed'),
(3, 1, 'Cama de casal'), (3, 2, 'Double bed');

-- =============================================
-- TABLE: product_categories
-- Shop product categories
-- =============================================
CREATE TABLE IF NOT EXISTS product_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    image VARCHAR(255) DEFAULT NULL,
    sort_order INT UNSIGNED DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS product_category_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    UNIQUE KEY unique_cat_lang (category_id, language_id),
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Default product categories
INSERT INTO product_categories (slug, sort_order) VALUES
('mel', 1), ('azeite', 2), ('vinho', 3), ('enchidos', 4), ('queijos', 5), ('doces', 6);

INSERT INTO product_category_translations (category_id, language_id, name, description) VALUES
(1, 1, 'Mel', 'Mel da regiao de Tras-os-Montes'), (1, 2, 'Honey', 'Honey from Tras-os-Montes region'),
(2, 1, 'Azeite', 'Azeite do vale do Sabor'), (2, 2, 'Olive Oil', 'Olive oil from Sabor valley'),
(3, 1, 'Vinho', 'Vinhos da regiao do Douro'), (3, 2, 'Wine', 'Wines from Douro region'),
(4, 1, 'Enchidos', 'Enchidos tradicionais transmontanos'), (4, 2, 'Cured Meats', 'Traditional Transmontano cured meats'),
(5, 1, 'Queijos', 'Queijos de ovelha e cabra'), (5, 2, 'Cheeses', 'Sheep and goat cheeses'),
(6, 1, 'Doces', 'Doces e bolos tradicionais'), (6, 2, 'Sweets', 'Traditional sweets and cakes');

-- =============================================
-- TABLE: products
-- Shop products
-- =============================================
CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) UNIQUE,
    slug VARCHAR(255) NOT NULL UNIQUE,
    category_id INT UNSIGNED DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2) DEFAULT NULL,
    stock_quantity INT DEFAULT 0,
    track_inventory TINYINT(1) DEFAULT 1,
    weight DECIMAL(8,3) DEFAULT NULL,
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_category (category_id),
    INDEX idx_active (is_active),
    INDEX idx_featured (is_featured)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS product_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    short_description TEXT,
    description TEXT,
    UNIQUE KEY unique_prod_lang (product_id, language_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS product_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255) DEFAULT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    sort_order INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id),
    INDEX idx_primary (is_primary)
) ENGINE=InnoDB;

-- =============================================
-- TABLE: orders
-- Customer orders
-- =============================================
CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) NOT NULL UNIQUE,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_nif VARCHAR(20) DEFAULT NULL,
    billing_address TEXT NOT NULL,
    billing_postal_code VARCHAR(10) NOT NULL,
    billing_city VARCHAR(100) NOT NULL,
    billing_country VARCHAR(2) DEFAULT 'PT',
    shipping_same_as_billing TINYINT(1) DEFAULT 1,
    shipping_address TEXT,
    shipping_postal_code VARCHAR(10),
    shipping_city VARCHAR(100),
    shipping_country VARCHAR(2) DEFAULT 'PT',
    subtotal DECIMAL(10,2) NOT NULL,
    shipping_fee DECIMAL(10,2) DEFAULT 0.00,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    payment_method ENUM('mbway', 'card', 'multibanco', 'transfer') NOT NULL,
    payment_status ENUM('pending', 'processing', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_reference VARCHAR(100) DEFAULT NULL,
    payment_entity VARCHAR(10) DEFAULT NULL,
    payment_transaction_id VARCHAR(255) DEFAULT NULL,
    paid_at DATETIME DEFAULT NULL,
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    tracking_code VARCHAR(100) DEFAULT NULL,
    notes TEXT,
    admin_notes TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    language VARCHAR(2) DEFAULT 'pt',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order_number (order_number),
    INDEX idx_customer_email (customer_email),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED DEFAULT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_sku VARCHAR(50) DEFAULT NULL,
    quantity INT UNSIGNED NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    INDEX idx_order (order_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_status_history (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    status VARCHAR(50) NOT NULL,
    notes TEXT,
    changed_by INT UNSIGNED DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_order (order_id)
) ENGINE=InnoDB;

-- =============================================
-- TABLE: activities
-- Tourist activities/attractions
-- =============================================
CREATE TABLE IF NOT EXISTS activities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(255) NOT NULL UNIQUE,
    image VARCHAR(255) DEFAULT NULL,
    category ENUM('nature', 'culture', 'gastronomy', 'adventure', 'wellness', 'events') DEFAULT 'culture',
    external_url VARCHAR(500) DEFAULT NULL,
    latitude DECIMAL(10,8) DEFAULT NULL,
    longitude DECIMAL(11,8) DEFAULT NULL,
    distance_km DECIMAL(5,2) DEFAULT NULL,
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_category (category),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS activity_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    activity_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    short_description TEXT,
    full_description TEXT,
    UNIQUE KEY unique_act_lang (activity_id, language_id),
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Default activities
INSERT INTO activities (slug, category, distance_km, is_featured, sort_order) VALUES
('castelo-mogadouro', 'culture', 0.5, 1, 1),
('miradouro-serpente-medal', 'nature', 15, 1, 2),
('parque-natural-douro', 'nature', 20, 1, 3),
('museu-mogadouro', 'culture', 0.3, 0, 4),
('igreja-matriz', 'culture', 0.2, 0, 5);

INSERT INTO activity_translations (activity_id, language_id, title, short_description) VALUES
(1, 1, 'Castelo de Mogadouro', 'Castelo do seculo XIII com vista panoramica da regiao'),
(1, 2, 'Mogadouro Castle', '13th century castle with panoramic views of the region'),
(2, 1, 'Miradouro Serpente do Medal', 'Vista panoramica sobre o rio Douro nas Arribas'),
(2, 2, 'Serpente do Medal Viewpoint', 'Panoramic view over the Douro river in the Arribas'),
(3, 1, 'Parque Natural do Douro Internacional', 'Area protegida com aguias e abutres'),
(3, 2, 'Douro International Natural Park', 'Protected area with eagles and vultures'),
(4, 1, 'Museu de Mogadouro', 'Historia e tradicoes da regiao'),
(4, 2, 'Mogadouro Museum', 'History and traditions of the region'),
(5, 1, 'Igreja Matriz de Mogadouro', 'Igreja de origem romanica no centro historico'),
(5, 2, 'Mogadouro Main Church', 'Romanesque origin church in the historic center');

-- =============================================
-- TABLE: contact_submissions
-- Contact form submissions log
-- =============================================
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    subject VARCHAR(255) DEFAULT NULL,
    message TEXT NOT NULL,
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    language VARCHAR(2) DEFAULT 'pt',
    is_read TINYINT(1) DEFAULT 0,
    is_spam TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_read (is_read),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- =============================================
-- TABLE: audit_log
-- Track admin actions for security
-- =============================================
CREATE TABLE IF NOT EXISTS audit_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id INT UNSIGNED DEFAULT NULL,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id INT UNSIGNED DEFAULT NULL,
    old_values JSON DEFAULT NULL,
    new_values JSON DEFAULT NULL,
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_admin (admin_id),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;
