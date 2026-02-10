-- Remove obsolete accommodation blocks
DELETE FROM content_blocks WHERE block_key IN ('accommodation_cta_title', 'accommodation_cta_text');

-- Insert new Accommodation blocks
INSERT INTO content_blocks (block_key, language_id, content) VALUES
('accommodation_hero_tagline', 1, 'Alojamento Local'),
('accommodation_hero_tagline', 2, 'Local Accommodation'),
('accommodation_hero_title', 1, 'A Casa do Gi'),
('accommodation_hero_title', 2, 'A Casa do Gi'),
('accommodation_hero_subtitle', 1, 'Acolhimento transmontano, momentos em familia e memorias para sempre.'),
('accommodation_hero_subtitle', 2, 'Transmontano hospitality, family moments and memories forever.'),

('accommodation_section_subtitle', 1, 'Duas Casas, Uma Experiencia'),
('accommodation_section_subtitle', 2, 'Two Houses, One Experience'),
('accommodation_section_title', 1, 'Escolha o Seu Refugio'),
('accommodation_section_title', 2, 'Choose Your Refuge'),

('accommodation_features_title', 1, 'O Que Ambas as Casas Oferecem'),
('accommodation_features_title', 2, 'What Both Houses Offer'),
('accommodation_feature_1', 1, 'Wi-Fi Gratis'),
('accommodation_feature_1', 2, 'Free Wi-Fi'),
('accommodation_feature_2', 1, 'Check-in Autonomo'),
('accommodation_feature_2', 2, 'Self Check-in'),
('accommodation_feature_3', 1, 'Roupa de Cama'),
('accommodation_feature_3', 2, 'Bed Linen'),
('accommodation_feature_4', 1, 'Localizacao Central'),
('accommodation_feature_4', 2, 'Central Location'),


-- Insert Activities blocks
('activities_hero_tagline', 1, 'Descubra Mogadouro'),
('activities_hero_tagline', 2, 'Discover Mogadouro'),
('activities_hero_title', 1, 'O Que Fazer'),
('activities_hero_title', 2, 'What to Do'),
('activities_hero_subtitle', 1, 'De paisagens deslumbrantes a sabores unicos, o nordeste transmontano tem muito para oferecer.'),
('activities_hero_subtitle', 2, 'From stunning landscapes to unique flavors, the northeast of Tras-os-Montes has much to offer.')

ON DUPLICATE KEY UPDATE content = VALUES(content);
