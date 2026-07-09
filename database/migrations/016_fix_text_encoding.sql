-- ============================================================
-- Migration 016 — Corrigir codificação de texto (mojibake) + limpeza de conteúdo
-- Idempotente: pode ser re-executada com segurança (UPDATE por chave).
-- Corrige 3 tipos de corrupção detetados: box-drawing, Latin1 duplo e literais "??".
-- Também remove a referência a "pequeno-almoço" (cliente não serve pequeno-almoço).
-- Aplicar com: mysql --default-character-set=utf8mb4 <db> < 016_fix_text_encoding.sql
-- ============================================================

SET NAMES utf8mb4;

-- ---------- content_blocks (PT, language_id = 1) ----------
UPDATE content_blocks SET content = '1980 – O Início' WHERE block_key = 'about_origin_caption' AND language_id = 1;
UPDATE content_blocks SET content = 'Família Gi' WHERE block_key = 'about_origin_signature' AND language_id = 1;
UPDATE content_blocks SET content = 'Erguida nos anos 80, a <strong>Casa do Gi</strong> conta a história intemporal de quem partiu para longe mas nunca esqueceu as suas raízes. Construída tijolo a tijolo, representa o sonho concretizado de regressar a casa.' WHERE block_key = 'about_origin_text1' AND language_id = 1;
UPDATE content_blocks SET content = 'O que começou como um projeto de vida familiar transformou-se num refúgio para quem procura a paz do interior. Aqui, o tempo abranda e os dias são medidos pela luz do sol e pelas conversas à beira da lareira.' WHERE block_key = 'about_origin_text2' AND language_id = 1;
UPDATE content_blocks SET content = 'Uma casa construída com <span class="italic text-secondary">amor</span> e <span class="italic text-secondary">saudade</span>.' WHERE block_key = 'about_origin_title' AND language_id = 1;
UPDATE content_blocks SET content = 'O Nosso Berço' WHERE block_key = 'about_region_label' AND language_id = 1;
UPDATE content_blocks SET content = 'Onde o tempo pára e a alma respira. Uma terra de horizontes infinitos, guardiã de tradições milenares e de uma beleza natural bruta e intocada.' WHERE block_key = 'about_region_text' AND language_id = 1;
UPDATE content_blocks SET content = 'Recebemos cada hóspede como um velho amigo. Sem formalismos rígidos, com o calor de um abraço e a sinceridade de um sorriso transmontano.' WHERE block_key = 'about_value1_text' AND language_id = 1;
UPDATE content_blocks SET content = 'Acolhimento Genuíno' WHERE block_key = 'about_value1_title' AND language_id = 1;
UPDATE content_blocks SET content = 'O luxo do silêncio. Longe da confusão, onde o único ruído é o vento nas árvores e o cantar dos pássaros. O refúgio perfeito para recarregar energias.' WHERE block_key = 'about_value2_text' AND language_id = 1;
UPDATE content_blocks SET content = 'Acreditamos que as melhores memórias são construídas à mesa. Partilhamos histórias, sabores e experiências que ficam para sempre.' WHERE block_key = 'about_value3_text' AND language_id = 1;
UPDATE content_blocks SET content = 'Espírito de Partilha' WHERE block_key = 'about_value3_title' AND language_id = 1;
-- about_value4_text: SEM referência a pequeno-almoço (cliente não serve)
UPDATE content_blocks SET content = 'Nada é deixado ao acaso. Da decoração cuidada aos pequenos detalhes, tudo é pensado para o seu conforto e bem-estar.' WHERE block_key = 'about_value4_text' AND language_id = 1;
UPDATE content_blocks SET content = 'Atenção ao Detalhe' WHERE block_key = 'about_value4_title' AND language_id = 1;
UPDATE content_blocks SET content = 'Não somos um hotel. Somos uma casa de família que decidiu abrir as portas ao mundo. Aqui, a hospitalidade não é um serviço, é a nossa natureza.' WHERE block_key = 'about_values_intro' AND language_id = 1;
UPDATE content_blocks SET content = 'A arte de bem receber,<br>à moda antiga.' WHERE block_key = 'about_values_title' AND language_id = 1;
UPDATE content_blocks SET content = 'Utilizamos cookies para melhorar a sua experiência no nosso website. Ao continuar a navegar, concorda com a utilização de cookies. Saiba mais nos nossos <a href="/termos-condicoes/" class="text-secondary hover:underline">termos e condições</a> e <a href="/politica-privacidade/" class="text-secondary hover:underline">política de privacidade</a>.' WHERE block_key = 'cookie_banner_text' AND language_id = 1;
UPDATE content_blocks SET content = 'Reserve Já' WHERE block_key = 'footer_book_title' AND language_id = 1;
UPDATE content_blocks SET content = 'Links Rápidos' WHERE block_key = 'footer_quicklinks_title' AND language_id = 1;
UPDATE content_blocks SET content = 'A Casa do Gi nasceu da vontade de preservar as raízes transmontanas. O que outrora foi uma casa de família, é hoje um refúgio para quem procura a autenticidade do campo.' WHERE block_key = 'home_about_text1' AND language_id = 1;
UPDATE content_blocks SET content = 'Aqui, o tempo abranda. Convidamo-lo a descobrir as tradições, os sabores e as gentes que fazem de Mogadouro um lugar único no mundo.' WHERE block_key = 'home_about_text2' AND language_id = 1;
UPDATE content_blocks SET content = 'Sabores autênticos de Trás-os-Montes.' WHERE block_key = 'home_card3_text' AND language_id = 1;
UPDATE content_blocks SET content = 'Onde a tradição transmontana encontra o conforto moderno' WHERE block_key = 'home_hero_subtitle' AND language_id = 1;
UPDATE content_blocks SET content = 'Tradição' WHERE block_key = 'home_split_right_title' AND language_id = 1;

-- ---------- content_blocks (EN, language_id = 2) ----------
UPDATE content_blocks SET content = '1980 – The Beginning' WHERE block_key = 'about_origin_caption' AND language_id = 2;
-- about_value4_text EN: remove breakfast reference (client does not serve breakfast)
UPDATE content_blocks SET content = 'Nothing is left to chance. From the careful decoration to the small details, everything is designed for your comfort and wellbeing.' WHERE block_key = 'about_value4_text' AND language_id = 2;

-- ---------- amenity_translations (PT, language_id = 1) ----------
UPDATE amenity_translations SET name = 'Frigorífico' WHERE amenity_id = 15 AND language_id = 1;
UPDATE amenity_translations SET name = 'Máquina de café' WHERE amenity_id = 16 AND language_id = 1;
UPDATE amenity_translations SET name = 'Utensílios de cozinha' WHERE amenity_id = 19 AND language_id = 1;
UPDATE amenity_translations SET name = 'Água quente' WHERE amenity_id = 24 AND language_id = 1;
UPDATE amenity_translations SET name = 'Detetor de monóxido de carbono' WHERE amenity_id = 30 AND language_id = 1;
UPDATE amenity_translations SET name = 'Berço' WHERE amenity_id = 32 AND language_id = 1;
UPDATE amenity_translations SET name = 'Banheira de bebé' WHERE amenity_id = 33 AND language_id = 1;
UPDATE amenity_translations SET name = 'Proteções de segurança para crianças' WHERE amenity_id = 34 AND language_id = 1;
UPDATE amenity_translations SET name = 'Limpeza incluída' WHERE amenity_id = 39 AND language_id = 1;

-- ---------- settings ----------
UPDATE settings SET description = 'Descrição (SEO)' WHERE setting_key = 'site_description';
