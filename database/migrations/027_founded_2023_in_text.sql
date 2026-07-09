-- Migration 027 — Integrar "Fundada em 2023 em Mogadouro" no início do texto
-- descritivo de cada página (Início, Sobre Nós, Casa do Gi 1 e 2), em vez de um
-- badge separado no hero. Idempotente: só antepõe se ainda não estiver presente.

-- Início (secção "Sobre Nós" da homepage) — PT
UPDATE content_blocks
    SET content = CONCAT('Fundada em 2023 em Mogadouro. ', content)
    WHERE block_key = 'home_about_text1' AND language_id = 1 AND content NOT LIKE 'Fundada em 2023%';
-- Início — EN
UPDATE content_blocks
    SET content = CONCAT('Founded in 2023 in Mogadouro. ', content)
    WHERE block_key = 'home_about_text1' AND language_id = 2 AND content NOT LIKE 'Founded in 2023%';

-- Sobre Nós (história de origem) — PT
UPDATE content_blocks
    SET content = CONCAT('Fundada em 2023 em Mogadouro. ', content)
    WHERE block_key = 'about_origin_text1' AND language_id = 1 AND content NOT LIKE 'Fundada em 2023%';
-- Sobre Nós — EN
UPDATE content_blocks
    SET content = CONCAT('Founded in 2023 in Mogadouro. ', content)
    WHERE block_key = 'about_origin_text1' AND language_id = 2 AND content NOT LIKE 'Founded in 2023%';

-- Casa do Gi 1 e 2 (descrição do alojamento) — PT
UPDATE accommodation_translations
    SET description = CONCAT('Fundada em 2023 em Mogadouro. ', description)
    WHERE language_id = 1 AND description NOT LIKE 'Fundada em 2023%';
-- Casa do Gi 1 e 2 — EN
UPDATE accommodation_translations
    SET description = CONCAT('Founded in 2023 in Mogadouro. ', description)
    WHERE language_id = 2 AND description NOT LIKE 'Founded in 2023%';
