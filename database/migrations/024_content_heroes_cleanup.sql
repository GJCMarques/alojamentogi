-- Migration 024 — Corrigir mojibake restante (content_blocks + page_heroes) e
-- remover as entradas de "hero" relativas à loja. Idempotente.

-- content_blocks (PT) com mojibake box-drawing não apanhado antes.
UPDATE content_blocks SET content = 'Refúgio'                                  WHERE block_key = 'home_split_left_title' AND language_id = 1;
UPDATE content_blocks SET content = 'Sinta o conforto das nossas casas rústicas.' WHERE block_key = 'home_card1_text'      AND language_id = 1;
UPDATE content_blocks SET content = 'Descubra a natureza e história de Mogadouro.' WHERE block_key = 'home_card2_text'      AND language_id = 1;
UPDATE content_blocks SET content = 'A Nossa História'                          WHERE block_key = 'home_about_label'       AND language_id = 1;
UPDATE content_blocks SET content = 'Ler História Completa'                     WHERE block_key = 'home_about_cta'         AND language_id = 1;
UPDATE content_blocks SET content = 'A Nossa História'                          WHERE block_key = 'about_hero_label'       AND language_id = 1;

-- page_heroes: nomes PT com mojibake (Latin1-as-UTF8).
UPDATE page_heroes SET page_name_pt = 'Página Inicial'          WHERE page_key = 'home';
UPDATE page_heroes SET page_name_pt = 'Sobre Nós'               WHERE page_key = 'about';
UPDATE page_heroes SET page_name_pt = 'Política de Privacidade' WHERE page_key = 'privacy_policy';
UPDATE page_heroes SET page_name_pt = 'Termos e Condições'      WHERE page_key = 'terms_conditions';

-- Remover heroes de páginas da loja que já não existem (a página /loja informativa
-- usa a imagem por defeito). Mantém-se tudo o resto.
DELETE FROM page_heroes WHERE page_key IN ('shop', 'product_detail', 'cart', 'checkout');
