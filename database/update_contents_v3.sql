-- Insert new Page Heroes
INSERT INTO page_heroes (page_key, page_name_pt, page_name_en, sort_order, is_active) VALUES
('privacy_policy', 'Politica de Privacidade', 'Privacy Policy', 10, 1),
('terms_conditions', 'Termos e Condicoes', 'Terms and Conditions', 11, 1)
ON DUPLICATE KEY UPDATE page_name_pt = VALUES(page_name_pt);

-- Insert Content Blocks
INSERT INTO content_blocks (block_key, language_id, content_type, content) VALUES
-- Contact Hero
('contact_hero_tagline', 1, 'text', 'Fale Connosco'),
('contact_hero_tagline', 2, 'text', 'Talk to Us'),
('contact_hero_title', 1, 'text', 'Contacte-nos'),
('contact_hero_title', 2, 'text', 'Contact Us'),
('contact_hero_subtitle', 1, 'textarea', 'Tem alguma questao? Entre em contacto connosco'),
('contact_hero_subtitle', 2, 'textarea', 'Have any questions? Get in touch with us'),

-- About Hero
('about_hero_tagline', 1, 'text', 'A Nossa Historia'),
('about_hero_tagline', 2, 'text', 'Our Story'),
('about_hero_title', 1, 'text', 'A Casa do Gi'),
('about_hero_title', 2, 'text', 'A Casa do Gi'),
('about_hero_subtitle', 1, 'textarea', 'Simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor!'),
('about_hero_subtitle', 2, 'textarea', 'Simplicity, warmth, remarkable moments of conviviality, family warmth, joy, fun, laughter and lots of love!'),

-- Privacy Policy
('privacy_hero_tagline', 1, 'text', 'Informacao Legal'),
('privacy_hero_tagline', 2, 'text', 'Legal Information'),
('privacy_hero_title', 1, 'text', 'Politica de Privacidade'),
('privacy_hero_title', 2, 'text', 'Privacy Policy'),
('privacy_hero_subtitle', 1, 'textarea', 'A sua privacidade e importante para nos. Saiba como tratamos os seus dados.'),
('privacy_hero_subtitle', 2, 'textarea', 'Your privacy is important to us. Learn how we handle your data.'),
('privacy_date', 1, 'text', 'Atualizado em: 09 de Fevereiro de 2025'),
('privacy_date', 2, 'text', 'Updated on: February 9, 2025'),
('privacy_content', 1, 'html', '<p>A sua privacidade e importante para nos. E politica da Casa do Gi respeitar a sua privacidade em relacao a qualquer informacao sua que possamos recolher no site A Casa do Gi, e outros sites que possuimos e operamos.</p><h3>1. Informacoes que recolhemos</h3><p>Solicitamos informacoes pessoais apenas quando realmente precisamos delas para lhe fornecer um servico. Fazemo-lo por meios justos e legais, com o seu conhecimento e consentimento. Tambem informamos por que estamos a recolher e como sera usado.</p>'),
('privacy_content', 2, 'html', '<p>Your privacy is important to us. It is A Casa do Gi policy to respect your privacy regarding any information we may collect from you across our website, A Casa do Gi, and other sites we own and operate.</p><h3>1. Information we collect</h3><p>We only ask for personal information when we truly need it to provide a service to you. We collect it by fair and lawful means, with your knowledge and consent. We also let you know why we are collecting it and how it will be used.</p>'),

-- Terms and Conditions
('terms_hero_tagline', 1, 'text', 'Informacao Legal'),
('terms_hero_tagline', 2, 'text', 'Legal Information'),
('terms_hero_title', 1, 'text', 'Termos e Condicoes'),
('terms_hero_title', 2, 'text', 'Terms and Conditions'),
('terms_hero_subtitle', 1, 'textarea', 'Por favor, leia atentamente os termos e condicoes de utilizacao do nosso servico.'),
('terms_hero_subtitle', 2, 'textarea', 'Please read carefully the terms and conditions of use of our service.'),
('terms_date', 1, 'text', 'Atualizado em: 09 de Fevereiro de 2025'),
('terms_date', 2, 'text', 'Updated on: February 9, 2025'),
('terms_content', 1, 'html', '<p>Ao aceder ao site A Casa do Gi, concorda em cumprir estes termos de servico, todas as leis e regulamentos aplicaveis e concorda que e responsavel pelo cumprimento de todas as leis locais aplicaveis. Se nao concordar com algum destes termos, esta proibido de usar ou aceder a este site.</p><h3>1. Uso de Licenca</h3><p>E concedida permissao para baixar temporariamente uma copia dos materiais (informacoes ou software) no site A Casa do Gi , apenas para visualizacao transitoria pessoal e nao comercial.</p>'),
('terms_content', 2, 'html', '<p>By accessing the website A Casa do Gi, you agree to be bound by these terms of service, all applicable laws and regulations, and agree that you are responsible for compliance with any applicable local laws. If you do not agree with any of these terms, you are prohibited from using or accessing this site.</p><h3>1. License Use</h3><p>Permission is granted to temporarily download one copy of the materials (information or software) on A Casa do Gi website for personal, non-commercial transitory viewing only.</p>')

ON DUPLICATE KEY UPDATE content = VALUES(content);
