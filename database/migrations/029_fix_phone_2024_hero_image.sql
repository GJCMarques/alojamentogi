-- Migration 029 — Corrigir telefone, atualizar "Fundada em 2023" para reforçar 2024/Casa 1 e 2, e corrigir imagem partida do hero "Refúgio"
-- Pedido do cliente (Casa do Gi) via email, 2026-07-23

-- 1) Telefone de contacto estava com dois dígitos trocados (691 -> 911)
UPDATE settings
SET setting_value = '+351 966 911 902'
WHERE setting_key = 'contact_phone';

-- 2) Texto "Fundada em 2023 em Mogadouro." -> reforçar Casa do Gi 1 e 2, desde 2024, em Mogadouro
--    (mantém "Erguida/Construída nos anos 80" no resto do texto — 2024 refere-se a quando passou a receber hóspedes, não à construção)
UPDATE content_blocks
SET content = REPLACE(content, 'Fundada em 2023 em Mogadouro. ', 'A Casa do Gi 1 e a Casa do Gi 2 recebem hóspedes desde 2024, em Mogadouro. ')
WHERE language_id = 1
  AND content LIKE 'Fundada em 2023 em Mogadouro.%';

UPDATE accommodation_translations
SET description = REPLACE(description, 'Fundada em 2023 em Mogadouro. ', 'A receber hóspedes desde 2024, em Mogadouro. ')
WHERE language_id = 1
  AND description LIKE 'Fundada em 2023 em Mogadouro.%';

-- 3) Imagem do hero "Refúgio" (home_image_split_left) aponta para um upload perdido (404).
--    Repor a imagem estática do repositório (assets/images/IgrejaMatriz.webp), que é sempre publicada com o deploy.
UPDATE content_blocks
SET content = 'images/IgrejaMatriz.webp'
WHERE block_key = 'home_image_split_left';
