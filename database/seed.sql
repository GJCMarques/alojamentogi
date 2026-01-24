-- A Casa do Gi - Sample Data Seeder
-- Run this after schema.sql to populate with initial data

-- Languages
INSERT INTO languages (code, name, is_default, is_active) VALUES
('pt', 'Português', 1, 1),
('en', 'English', 0, 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Default Admin User (password: admin123)
INSERT INTO admins (username, email, password_hash, full_name, role, is_active) VALUES
('admin', 'admin@acasadogi.pt', '$2y$12$95JaIzBEov7tZz0SfnUwPOIecK1ujIWqumI74Ndw.e2RHwm/FpVqy', 'Administrador', 'super_admin', 1)
ON DUPLICATE KEY UPDATE email = VALUES(email), password_hash = VALUES(password_hash), role = VALUES(role);

-- Site Settings
INSERT INTO settings (setting_key, setting_value, setting_type, setting_group) VALUES
('site_name', 'A Casa do Gi', 'text', 'general'),
('site_tagline_pt', 'Simplicidade, acolhimento e muito amor', 'text', 'general'),
('site_tagline_en', 'Simplicity, warmth and love', 'text', 'general'),
('contact_email', 'geral@acasadogi.pt', 'text', 'contact'),
('contact_phone', '+351 912 345 678', 'text', 'contact'),
('contact_address', 'Rua Principal, 123\n5200 Mogadouro\nPortugal', 'text', 'contact'),
('facebook_url', 'https://facebook.com/acasadogi', 'text', 'social'),
('instagram_url', 'https://instagram.com/acasadogi', 'text', 'social'),
('guestready_url', 'https://www.guestready.com/en-pt/rentals/quinta-de-mouraes-tbd/', 'text', 'booking'),
('booking_url', 'https://www.booking.com/', 'text', 'booking'),
('airbnb_url', 'https://www.airbnb.com/', 'text', 'booking'),
('shop_enabled', '1', 'boolean', 'shop'),
('free_shipping_threshold', '50', 'number', 'shop'),
('shipping_cost', '5', 'number', 'shop'),
('maintenance_mode', '0', 'boolean', 'general'),
('contact_form_enabled', '1', 'boolean', 'contact')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Product Categories
INSERT INTO product_categories (id, slug, sort_order, is_active) VALUES
(1, 'azeite', 1, 1),
(2, 'mel', 2, 1),
(3, 'enchidos', 3, 1),
(4, 'amendoas', 4, 1),
(5, 'compotas', 5, 1),
(6, 'artesanato', 6, 1)
ON DUPLICATE KEY UPDATE slug = VALUES(slug);

-- Product Category Translations (Portuguese)
INSERT INTO product_category_translations (category_id, language_id, name, description) VALUES
(1, 1, 'Azeite', 'Azeite virgem extra DOP de Trás-os-Montes'),
(2, 1, 'Mel', 'Mel artesanal da região transmontana'),
(3, 1, 'Enchidos', 'Enchidos tradicionais fumados a lenha'),
(4, 1, 'Amêndoas', 'Amêndoas e produtos derivados'),
(5, 1, 'Compotas', 'Compotas e doces caseiros'),
(6, 1, 'Artesanato', 'Artesanato tradicional da região');

-- Product Category Translations (English)
INSERT INTO product_category_translations (category_id, language_id, name, description) VALUES
(1, 2, 'Olive Oil', 'Extra virgin olive oil PDO from Trás-os-Montes'),
(2, 2, 'Honey', 'Artisan honey from the Transmontana region'),
(3, 2, 'Cured Meats', 'Traditional wood-smoked cured meats'),
(4, 2, 'Almonds', 'Almonds and derived products'),
(5, 2, 'Jams', 'Homemade jams and preserves'),
(6, 2, 'Handicrafts', 'Traditional regional handicrafts');

-- Sample Products
INSERT INTO products (category_id, slug, sku, price, sale_price, stock_quantity, track_inventory, weight, is_featured, is_active) VALUES
(1, 'azeite-dop-500ml', 'AZ-001', 12.50, NULL, 50, 1, 0.6, 1, 1),
(1, 'azeite-dop-1l', 'AZ-002', 22.00, NULL, 30, 1, 1.1, 0, 1),
(2, 'mel-rosmaninho-500g', 'ML-001', 8.50, NULL, 40, 1, 0.5, 1, 1),
(2, 'mel-castanheiro-500g', 'ML-002', 9.00, NULL, 25, 1, 0.5, 0, 1),
(3, 'chourico-carne-tradicional', 'EN-001', 6.50, NULL, 60, 1, 0.25, 1, 1),
(3, 'alheira-tradicional', 'EN-002', 4.50, NULL, 45, 1, 0.2, 0, 1),
(3, 'salpicao-transmontano', 'EN-003', 8.00, 7.00, 35, 1, 0.3, 1, 1),
(4, 'amendoa-miolo-250g', 'AM-001', 5.50, NULL, 40, 1, 0.25, 0, 1),
(5, 'compota-figo-250g', 'CP-001', 4.50, NULL, 30, 1, 0.3, 0, 1),
(5, 'doce-abobora-amendoa-250g', 'CP-002', 5.00, NULL, 25, 1, 0.3, 1, 1);

-- Product Translations (Portuguese)
INSERT INTO product_translations (product_id, language_id, name, short_description, description) VALUES
(1, 1, 'Azeite DOP 500ml', 'Azeite virgem extra de Trás-os-Montes', 'Azeite virgem extra de produção artesanal, prensado a frio. Denominação de Origem Protegida de Trás-os-Montes. Ideal para temperar saladas, peixe grelhado e pratos tradicionais portugueses.'),
(2, 1, 'Azeite DOP 1 Litro', 'Azeite virgem extra formato familiar', 'O mesmo azeite de qualidade superior em formato económico de 1 litro. Perfeito para uso diário na cozinha.'),
(3, 1, 'Mel de Rosmaninho 500g', 'Mel artesanal de rosmaninho silvestre', 'Mel puro de rosmaninho colhido nas serras transmontanas. Aroma floral delicado e sabor suave. Ideal para adoçar infusões ou consumir ao natural.'),
(4, 1, 'Mel de Castanheiro 500g', 'Mel escuro intenso de castanheiro', 'Mel de castanheiro com sabor intenso e característico. Rico em minerais e antioxidantes. Perfeito para acompanhar queijos curados.'),
(5, 1, 'Chouriço de Carne Tradicional', 'Chouriço fumado a lenha de carvalho', 'Chouriço de carne de porco tradicional, temperado com pimentão e alho, fumado lentamente com lenha de carvalho. Receita transmitida há gerações.'),
(6, 1, 'Alheira Tradicional', 'Alheira artesanal fumada', 'Alheira tradicional transmontana feita com carne de aves e pão de trigo. Fumada de forma artesanal. Assar no forno ou fritar em azeite.'),
(7, 1, 'Salpicão Transmontano', 'Enchido nobre de lombo de porco', 'Salpicão feito com lombo de porco selecionado, temperado com vinho, alho e pimentão. Fumado a lenha durante várias semanas.'),
(8, 1, 'Miolo de Amêndoa 250g', 'Amêndoas inteiras descascadas', 'Amêndoas de Trás-os-Montes, descascadas e selecionadas à mão. Ideais para consumo direto ou para confeitaria.'),
(9, 1, 'Compota de Figo 250g', 'Compota artesanal de figos maduros', 'Compota de figo feita de forma tradicional, com figos colhidos no ponto ideal de maturação. Adoçada apenas com açúcar.'),
(10, 1, 'Doce de Abóbora com Amêndoa', 'Doce tradicional de abóbora', 'Doce regional feito com abóbora menina e amêndoa. Receita conventual transmontana. Ideal para acompanhar queijo fresco.');

-- Product Translations (English)
INSERT INTO product_translations (product_id, language_id, name, short_description, description) VALUES
(1, 2, 'PDO Olive Oil 500ml', 'Extra virgin olive oil from Trás-os-Montes', 'Artisanal extra virgin olive oil, cold pressed. Protected Designation of Origin from Trás-os-Montes. Perfect for seasoning salads, grilled fish and traditional Portuguese dishes.'),
(2, 2, 'PDO Olive Oil 1 Liter', 'Family size extra virgin olive oil', 'The same superior quality olive oil in an economical 1 liter format. Perfect for daily kitchen use.'),
(3, 2, 'Rosemary Honey 500g', 'Artisan wild rosemary honey', 'Pure rosemary honey harvested in the Transmontana mountains. Delicate floral aroma and mild flavor. Ideal for sweetening infusions or eating plain.'),
(4, 2, 'Chestnut Honey 500g', 'Intense dark chestnut honey', 'Chestnut honey with an intense and characteristic flavor. Rich in minerals and antioxidants. Perfect to accompany aged cheeses.'),
(5, 2, 'Traditional Chouriço Sausage', 'Oak-smoked pork sausage', 'Traditional pork chouriço sausage, seasoned with paprika and garlic, slowly smoked with oak wood. Recipe passed down through generations.'),
(6, 2, 'Traditional Alheira', 'Artisan smoked alheira', 'Traditional Transmontana alheira made with poultry meat and wheat bread. Artisanally smoked. Roast in the oven or fry in olive oil.'),
(7, 2, 'Transmontana Salpicão', 'Premium pork loin sausage', 'Salpicão made with selected pork loin, seasoned with wine, garlic and paprika. Wood-smoked for several weeks.'),
(8, 2, 'Almond Kernels 250g', 'Whole shelled almonds', 'Almonds from Trás-os-Montes, shelled and hand-selected. Ideal for direct consumption or pastry making.'),
(9, 2, 'Fig Jam 250g', 'Artisan ripe fig jam', 'Fig jam made in a traditional way, with figs harvested at the ideal point of ripeness. Sweetened only with sugar.'),
(10, 2, 'Pumpkin and Almond Sweet', 'Traditional pumpkin sweet', 'Regional sweet made with butternut squash and almond. Transmontana convent recipe. Ideal to accompany fresh cheese.');

-- Accommodation Data
INSERT INTO accommodation (slug, max_guests, bedrooms, bathrooms, is_active) VALUES
('casa-do-gi', 6, 3, 2, 1)
ON DUPLICATE KEY UPDATE max_guests = VALUES(max_guests);

-- Accommodation Translations
INSERT INTO accommodation_translations (accommodation_id, language_id, name, tagline, description) VALUES
(1, 1, 'A Casa do Gi', 'Simplicidade, acolhimento e muito amor', 'Situada na encantadora vila de Mogadouro, no coração de Trás-os-Montes, a Casa do Gi é um refúgio acolhedor que combina o charme rústico transmontano com todo o conforto moderno. Construída nos anos 80 por emigrantes com muito carinho, esta casa foi renovada preservando a sua essência original.\n\nCom capacidade para 6 hóspedes, a casa dispõe de 3 quartos confortáveis, 2 casas de banho completas, uma cozinha totalmente equipada e uma sala de estar acolhedora com lareira. O jardim privado é perfeito para relaxar e desfrutar da tranquilidade da região.'),
(1, 2, 'A Casa do Gi', 'Simplicity, warmth and love', 'Located in the charming village of Mogadouro, in the heart of Trás-os-Montes, Casa do Gi is a cozy retreat that combines rustic Transmontano charm with all modern comforts. Built in the 80s by emigrants with great care, this house has been renovated while preserving its original essence.\n\nWith capacity for 6 guests, the house has 3 comfortable bedrooms, 2 full bathrooms, a fully equipped kitchen and a cozy living room with fireplace. The private garden is perfect for relaxing and enjoying the tranquility of the region.')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Amenities
INSERT INTO amenities (icon, sort_order, is_active) VALUES
('wifi', 1, 1),
('kitchen', 2, 1),
('fireplace', 3, 1),
('garden', 4, 1),
('parking', 5, 1),
('heating', 6, 1),
('washing-machine', 7, 1),
('tv', 8, 1);

-- Amenity Translations
INSERT INTO amenity_translations (amenity_id, language_id, name) VALUES
(1, 1, 'Wi-Fi Gratuito'),
(1, 2, 'Free Wi-Fi'),
(2, 1, 'Cozinha Equipada'),
(2, 2, 'Equipped Kitchen'),
(3, 1, 'Lareira'),
(3, 2, 'Fireplace'),
(4, 1, 'Jardim Privado'),
(4, 2, 'Private Garden'),
(5, 1, 'Estacionamento'),
(5, 2, 'Free Parking'),
(6, 1, 'Aquecimento Central'),
(6, 2, 'Central Heating'),
(7, 1, 'Máquina de Lavar'),
(7, 2, 'Washing Machine'),
(8, 1, 'Televisão'),
(8, 2, 'Television');

-- Link amenities to accommodation
INSERT INTO accommodation_amenities (accommodation_id, amenity_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8);

SELECT 'Sample data inserted successfully!' as status;
