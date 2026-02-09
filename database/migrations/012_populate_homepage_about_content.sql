-- Migration 012: Populate content_blocks for Homepage and About Us pages
-- Adds all hardcoded text as editable content blocks (PT=1, EN=2)
-- Safe to re-run: uses INSERT ... ON DUPLICATE KEY UPDATE

-- Clean up old homepage blocks that no longer match the new structure
DELETE FROM content_blocks WHERE block_key IN (
    'home_hero_title', 'home_intro_title', 'home_intro_text',
    'home_accommodation_title', 'home_accommodation_text',
    'home_shop_title', 'home_shop_text',
    'about_title', 'about_intro', 'about_story_title',
    'about_story_text', 'about_mission_title', 'about_mission_text'
);

-- =============================================
-- HOMEPAGE BLOCKS
-- =============================================

-- Hero subtitle
INSERT INTO content_blocks (block_key, language_id, content_type, content, page, section) VALUES
('home_hero_subtitle', 1, 'text', 'Onde a tradição transmontana encontra o conforto moderno', 'home', 'hero'),
('home_hero_subtitle', 2, 'text', 'Where Transmontana tradition meets modern comfort', 'home', 'hero')
ON DUPLICATE KEY UPDATE content_type=VALUES(content_type), page=VALUES(page), section=VALUES(section);

-- Split Hero - Left panel
INSERT INTO content_blocks (block_key, language_id, content_type, content, page, section) VALUES
('home_split_left_label', 1, 'text', 'Bem-vindo ao', 'home', 'split_hero'),
('home_split_left_label', 2, 'text', 'Welcome to the', 'home', 'split_hero'),
('home_split_left_title', 1, 'text', 'Refúgio', 'home', 'split_hero'),
('home_split_left_title', 2, 'text', 'Refuge', 'home', 'split_hero')
ON DUPLICATE KEY UPDATE content_type=VALUES(content_type), page=VALUES(page), section=VALUES(section);

-- Split Hero - Right panel
INSERT INTO content_blocks (block_key, language_id, content_type, content, page, section) VALUES
('home_split_right_label', 1, 'text', 'Descubra a', 'home', 'split_hero'),
('home_split_right_label', 2, 'text', 'Discover the', 'home', 'split_hero'),
('home_split_right_title', 1, 'text', 'Tradição', 'home', 'split_hero'),
('home_split_right_title', 2, 'text', 'Tradition', 'home', 'split_hero')
ON DUPLICATE KEY UPDATE content_type=VALUES(content_type), page=VALUES(page), section=VALUES(section);

-- Explore section title
INSERT INTO content_blocks (block_key, language_id, content_type, content, page, section) VALUES
('home_explore_title', 1, 'text', 'Explore o Nosso Mundo', 'home', 'explore'),
('home_explore_title', 2, 'text', 'Explore Our World', 'home', 'explore')
ON DUPLICATE KEY UPDATE content_type=VALUES(content_type), page=VALUES(page), section=VALUES(section);

-- Card 1: Accommodation
INSERT INTO content_blocks (block_key, language_id, content_type, content, page, section) VALUES
('home_card1_label', 1, 'text', 'Dormir', 'home', 'explore'),
('home_card1_label', 2, 'text', 'Sleep', 'home', 'explore'),
('home_card1_title', 1, 'text', 'Alojamento', 'home', 'explore'),
('home_card1_title', 2, 'text', 'Accommodation', 'home', 'explore'),
('home_card1_text', 1, 'text', 'Sinta o conforto das nossas casas rústicas.', 'home', 'explore'),
('home_card1_text', 2, 'text', 'Experience the comfort of our rustic houses.', 'home', 'explore'),
('home_card1_cta', 1, 'text', 'Ver Casas', 'home', 'explore'),
('home_card1_cta', 2, 'text', 'View Rooms', 'home', 'explore')
ON DUPLICATE KEY UPDATE content_type=VALUES(content_type), page=VALUES(page), section=VALUES(section);

-- Card 2: Activities
INSERT INTO content_blocks (block_key, language_id, content_type, content, page, section) VALUES
('home_card2_label', 1, 'text', 'Experienciar', 'home', 'explore'),
('home_card2_label', 2, 'text', 'Experience', 'home', 'explore'),
('home_card2_title', 1, 'text', 'Atividades', 'home', 'explore'),
('home_card2_title', 2, 'text', 'Activities', 'home', 'explore'),
('home_card2_text', 1, 'text', 'Descubra a natureza e história de Mogadouro.', 'home', 'explore'),
('home_card2_text', 2, 'text', 'Discover the nature and history of Mogadouro.', 'home', 'explore'),
('home_card2_cta', 1, 'text', 'Explorar', 'home', 'explore'),
('home_card2_cta', 2, 'text', 'Explore', 'home', 'explore')
ON DUPLICATE KEY UPDATE content_type=VALUES(content_type), page=VALUES(page), section=VALUES(section);

-- Card 3: Shop
INSERT INTO content_blocks (block_key, language_id, content_type, content, page, section) VALUES
('home_card3_label', 1, 'text', 'Saborear', 'home', 'explore'),
('home_card3_label', 2, 'text', 'Taste', 'home', 'explore'),
('home_card3_title', 1, 'text', 'Loja Regional', 'home', 'explore'),
('home_card3_title', 2, 'text', 'Regional Shop', 'home', 'explore'),
('home_card3_text', 1, 'text', 'Sabores autênticos de Trás-os-Montes.', 'home', 'explore'),
('home_card3_text', 2, 'text', 'Authentic flavors from Tras-os-Montes.', 'home', 'explore'),
('home_card3_cta', 1, 'text', 'Comprar', 'home', 'explore'),
('home_card3_cta', 2, 'text', 'Shop Now', 'home', 'explore')
ON DUPLICATE KEY UPDATE content_type=VALUES(content_type), page=VALUES(page), section=VALUES(section);

-- Card 4: Contact
INSERT INTO content_blocks (block_key, language_id, content_type, content, page, section) VALUES
('home_card4_label', 1, 'text', 'Conectar', 'home', 'explore'),
('home_card4_label', 2, 'text', 'Connect', 'home', 'explore'),
('home_card4_title', 1, 'text', 'Contactos', 'home', 'explore'),
('home_card4_title', 2, 'text', 'Contact Us', 'home', 'explore'),
('home_card4_text', 1, 'text', 'Fale connosco e planeie a sua visita.', 'home', 'explore'),
('home_card4_text', 2, 'text', 'Get in touch and plan your visit.', 'home', 'explore'),
('home_card4_cta', 1, 'text', 'Contactar', 'home', 'explore'),
('home_card4_cta', 2, 'text', 'Get in Touch', 'home', 'explore')
ON DUPLICATE KEY UPDATE content_type=VALUES(content_type), page=VALUES(page), section=VALUES(section);

-- About teaser on homepage
INSERT INTO content_blocks (block_key, language_id, content_type, content, page, section) VALUES
('home_about_label', 1, 'text', 'A Nossa História', 'home', 'about_teaser'),
('home_about_label', 2, 'text', 'Our Story', 'home', 'about_teaser'),
('home_about_title', 1, 'html', 'Mais que uma casa,<br>um <span class="italic text-accent">legado</span>.', 'home', 'about_teaser'),
('home_about_title', 2, 'html', 'More than a house,<br>a <span class="italic text-accent">legacy</span>.', 'home', 'about_teaser'),
('home_about_text1', 1, 'textarea', 'A Casa do Gi nasceu da vontade de preservar as raízes transmontanas. O que outrora foi uma casa de família, é hoje um refúgio para quem procura a autenticidade do campo.', 'home', 'about_teaser'),
('home_about_text1', 2, 'textarea', 'A Casa do Gi was born from the will to preserve the roots of Tras-os-Montes. What was once a family home is now a refuge for those seeking the authenticity of the countryside.', 'home', 'about_teaser'),
('home_about_text2', 1, 'textarea', 'Aqui, o tempo abranda. Convidamo-lo a descobrir as tradições, os sabores e as gentes que fazem de Mogadouro um lugar único no mundo.', 'home', 'about_teaser'),
('home_about_text2', 2, 'textarea', 'Here, time slows down. We invite you to discover the traditions, the flavors, and the people that make Mogadouro a unique place in the world.', 'home', 'about_teaser'),
('home_about_cta', 1, 'text', 'Ler História Completa', 'home', 'about_teaser'),
('home_about_cta', 2, 'text', 'Read Full Story', 'home', 'about_teaser')
ON DUPLICATE KEY UPDATE content_type=VALUES(content_type), page=VALUES(page), section=VALUES(section);

-- =============================================
-- ABOUT US PAGE BLOCKS
-- =============================================

-- Hero section
INSERT INTO content_blocks (block_key, language_id, content_type, content, page, section) VALUES
('about_hero_label', 1, 'text', 'A Nossa História', 'about', 'hero'),
('about_hero_label', 2, 'text', 'Our Story', 'about', 'hero'),
('about_hero_subtitle', 1, 'textarea', 'Simplicidade, acolhimento, momentos de convívio marcantes, calor da família, alegria, diversão, gargalhadas e muito amor!', 'about', 'hero'),
('about_hero_subtitle', 2, 'textarea', 'Simplicity, warmth, memorable moments together, family warmth, joy, fun, laughter and lots of love!', 'about', 'hero')
ON DUPLICATE KEY UPDATE content_type=VALUES(content_type), page=VALUES(page), section=VALUES(section);

-- Origin section
INSERT INTO content_blocks (block_key, language_id, content_type, content, page, section) VALUES
('about_origin_label', 1, 'text', 'A Nossa Origem', 'about', 'origin'),
('about_origin_label', 2, 'text', 'Our Origins', 'about', 'origin'),
('about_origin_title', 1, 'html', 'Uma casa construída com <span class="italic text-secondary">amor</span> e <span class="italic text-secondary">saudade</span>.', 'about', 'origin'),
('about_origin_title', 2, 'html', 'A house built with <span class="italic text-secondary">love</span> and <span class="italic text-secondary">longing</span>.', 'about', 'origin'),
('about_origin_text1', 1, 'textarea', 'Erguida nos anos 80, a <strong>Casa do Gi</strong> conta a história intemporal de quem partiu para longe mas nunca esqueceu as suas raízes. Construída tijolo a tijolo, representa o sonho concretizado de regressar a casa.', 'about', 'origin'),
('about_origin_text1', 2, 'textarea', 'Built in the 80s, <strong>Casa do Gi</strong> tells the timeless story of those who left for distant lands but never forgot their roots. Constructed brick by brick, it represents the fulfilled dream of returning home.', 'about', 'origin'),
('about_origin_text2', 1, 'textarea', 'O que começou como um projeto de vida familiar transformou-se num refúgio para quem procura a paz do interior. Aqui, o tempo abranda e os dias são medidos pela luz do sol e pelas conversas à beira da lareira.', 'about', 'origin'),
('about_origin_text2', 2, 'textarea', 'What began as a family life project transformed into a refuge for those seeking the peace of the countryside. Here, time slows down and days are measured by sunlight and conversations by the fireplace.', 'about', 'origin'),
('about_origin_caption', 1, 'text', '1980 • O Início', 'about', 'origin'),
('about_origin_caption', 2, 'text', '1980 • The Beginning', 'about', 'origin'),
('about_origin_signature', 1, 'text', 'Família Gi', 'about', 'origin'),
('about_origin_signature', 2, 'text', 'Gi Family', 'about', 'origin')
ON DUPLICATE KEY UPDATE content_type=VALUES(content_type), page=VALUES(page), section=VALUES(section);

-- Values section
INSERT INTO content_blocks (block_key, language_id, content_type, content, page, section) VALUES
('about_values_label', 1, 'text', 'Valores', 'about', 'values'),
('about_values_label', 2, 'text', 'Values', 'about', 'values'),
('about_values_title', 1, 'html', 'A arte de bem receber,<br>à moda antiga.', 'about', 'values'),
('about_values_title', 2, 'html', 'The art of welcoming,<br>the old-fashioned way.', 'about', 'values'),
('about_values_intro', 1, 'textarea', 'Não somos um hotel. Somos uma casa de família que decidiu abrir as portas ao mundo. Aqui, a hospitalidade não é um serviço, é a nossa natureza.', 'about', 'values'),
('about_values_intro', 2, 'textarea', 'We are not a hotel. We are a family home that decided to open its doors to the world. Here, hospitality is not a service, it''s our nature.', 'about', 'values')
ON DUPLICATE KEY UPDATE content_type=VALUES(content_type), page=VALUES(page), section=VALUES(section);

-- Value cards
INSERT INTO content_blocks (block_key, language_id, content_type, content, page, section) VALUES
('about_value1_title', 1, 'text', 'Acolhimento Genuíno', 'about', 'values'),
('about_value1_title', 2, 'text', 'Genuine Hospitality', 'about', 'values'),
('about_value1_text', 1, 'textarea', 'Recebemos cada hóspede como um velho amigo. Sem formalismos rígidos, com o calor de um abraço e a sinceridade de um sorriso transmontano.', 'about', 'values'),
('about_value1_text', 2, 'textarea', 'We welcome each guest as an old friend. Without rigid formalities, with the warmth of a hug and the sincerity of a Transmontano smile.', 'about', 'values'),
('about_value2_title', 1, 'text', 'Paz Absoluta', 'about', 'values'),
('about_value2_title', 2, 'text', 'Absolute Peace', 'about', 'values'),
('about_value2_text', 1, 'textarea', 'O luxo do silêncio. Longe da confusão, onde o único ruído é o vento nas árvores e o cantar dos pássaros. O refúgio perfeito para recarregar energias.', 'about', 'values'),
('about_value2_text', 2, 'textarea', 'The luxury of silence. Far from the hustle, where the only sound is the wind in the trees and the singing of birds. The perfect refuge to recharge energies.', 'about', 'values'),
('about_value3_title', 1, 'text', 'Espírito de Partilha', 'about', 'values'),
('about_value3_title', 2, 'text', 'Spirit of Sharing', 'about', 'values'),
('about_value3_text', 1, 'textarea', 'Acreditamos que as melhores memórias são construídas à mesa. Partilhamos histórias, sabores e experiências que ficam para sempre.', 'about', 'values'),
('about_value3_text', 2, 'textarea', 'We believe the best memories are built at the table. We share stories, flavors and experiences that last forever.', 'about', 'values'),
('about_value4_title', 1, 'text', 'Atenção ao Detalhe', 'about', 'values'),
('about_value4_title', 2, 'text', 'Attention to Detail', 'about', 'values'),
('about_value4_text', 1, 'textarea', 'Nada é deixado ao acaso. Do pequeno-almoço caseiro à decoração cuidada, tudo é pensado para o seu conforto e bem-estar.', 'about', 'values'),
('about_value4_text', 2, 'textarea', 'Nothing is left to chance. From homemade breakfast to thoughtful decoration, everything is designed for your comfort and wellbeing.', 'about', 'values')
ON DUPLICATE KEY UPDATE content_type=VALUES(content_type), page=VALUES(page), section=VALUES(section);

-- Region section
INSERT INTO content_blocks (block_key, language_id, content_type, content, page, section) VALUES
('about_region_label', 1, 'text', 'O Nosso Berço', 'about', 'region'),
('about_region_label', 2, 'text', 'Our Home', 'about', 'region'),
('about_region_text', 1, 'textarea', 'Onde o tempo pára e a alma respira. Uma terra de horizontes infinitos, guardiã de tradições milenares e de uma beleza natural bruta e intocada.', 'about', 'region'),
('about_region_text', 2, 'textarea', 'Where time stops and the soul breathes. A land of infinite horizons, guardian of ancient traditions and raw, untouched natural beauty.', 'about', 'region'),
('about_region_cta1', 1, 'text', 'Planear Visita', 'about', 'region'),
('about_region_cta1', 2, 'text', 'Plan Visit', 'about', 'region'),
('about_region_cta2', 1, 'text', 'O que fazer', 'about', 'region'),
('about_region_cta2', 2, 'text', 'Things to do', 'about', 'region')
ON DUPLICATE KEY UPDATE content_type=VALUES(content_type), page=VALUES(page), section=VALUES(section);
