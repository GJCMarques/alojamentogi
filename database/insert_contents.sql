INSERT INTO content_blocks (block_key, language_id, content) VALUES
-- Accommodation Data
('accommodation_intro', 1, 'Ambas as casas oferecem o mesmo conforto e hospitalidade transmontana. Escolha a que melhor se adapta a sua estadia.'),
('accommodation_intro', 2, 'Both houses offer the same comfort and Transmontana hospitality. Choose the one that best suits your stay.'),
('accommodation_cta_title', 1, 'Reserve Ja'),
('accommodation_cta_title', 2, 'Book Now'),
('accommodation_cta_text', 1, 'Escolha a sua plataforma preferida'),
('accommodation_cta_text', 2, 'Choose your preferred platform'),

-- Shop Data
('shop_intro', 1, 'Sabores autenticos de Tras-os-Montes, selecionados com carinho para a sua mesa.'),
('shop_intro', 2, 'Authentic flavors from Tras-os-Montes, selected with care for your table.'),
('shop_empty_message', 1, 'Esta categoria ainda nao tem produtos disponiveis.'),
('shop_empty_message', 2, 'This category does not have products available yet.'),

-- Contact Data
('contact_intro', 1, 'Tem alguma questao? Entre em contacto connosco'),
('contact_intro', 2, 'Do you have any questions? Get in touch with us'),
('contact_success_message', 1, 'Obrigado pelo seu contacto. Iremos responder o mais brevemente possivel.'),
('contact_success_message', 2, 'Thank you for your contact. We will reply as soon as possible.')

ON DUPLICATE KEY UPDATE content = VALUES(content);
