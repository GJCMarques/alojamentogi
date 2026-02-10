INSERT INTO content_blocks (block_key, language_id, content_type, content) VALUES
-- Footer Description
('footer_description', 1, 'textarea', 'Simplicidade, acolhimento e muito amor em Mogadouro, Portugal.'),
('footer_description', 2, 'textarea', 'Simplicity, warmth and love in Mogadouro, Portugal.'),

-- Footer Quick Links Title
('footer_quicklinks_title', 1, 'text', 'Links Rápidos'),
('footer_quicklinks_title', 2, 'text', 'Quick Links'),

-- Footer Contact Title
('footer_contact_title', 1, 'text', 'Contacto'),
('footer_contact_title', 2, 'text', 'Contact'),

-- Footer Address
('footer_address', 1, 'text', '52 Avenida Nossa Senhora do Caminho, Mogadouro'),
('footer_address', 2, 'text', '52 Avenida Nossa Senhora do Caminho, Mogadouro'),

-- Footer Book Now Title
('footer_book_title', 1, 'text', 'Reserve Já'),
('footer_book_title', 2, 'text', 'Book Now'),

-- Footer Rights
('footer_rights_text', 1, 'text', 'Todos os direitos reservados.'),
('footer_rights_text', 2, 'text', 'All rights reserved.'),

-- Cookie Banner Text
('cookie_banner_text', 1, 'wysiwyg', 'Utilizamos cookies para melhorar a sua experiência no nosso website. Ao continuar a navegar, concorda com a utilização de cookies. Saiba mais nos nossos <a href="/alojamentogi/termos-condicoes/" class="text-secondary hover:underline">termos e condições</a> e <a href="/alojamentogi/politica-privacidade/" class="text-secondary hover:underline">política de privacidade</a>.'),
('cookie_banner_text', 2, 'wysiwyg', 'We use cookies to improve your experience on our website. By continuing to browse, you agree to our use of cookies. Learn more in our <a href="/alojamentogi/en/termos-condicoes/" class="text-secondary hover:underline">terms and conditions</a> and <a href="/alojamentogi/en/politica-privacidade/" class="text-secondary hover:underline">privacy policy</a>.'),

-- Cookie Banner Buttons
('cookie_banner_accept', 1, 'text', 'Aceitar'),
('cookie_banner_accept', 2, 'text', 'Accept'),
('cookie_banner_details', 1, 'text', 'Ver Detalhes'),
('cookie_banner_details', 2, 'text', 'Details')

ON DUPLICATE KEY UPDATE content = VALUES(content), content_type = VALUES(content_type);
