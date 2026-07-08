-- ============================================================
-- Migration 019 — Loja em manutenção (migração para shopk.it)
-- Idempotente. Desativa a loja interna e regista o URL do serviço externo.
-- ============================================================

SET NAMES utf8mb4;

-- Desativar a loja interna (esconde carrinho no site)
UPDATE settings SET setting_value = '0' WHERE setting_key = 'shop_enabled';

-- URL da nova loja externa (shopk.it) — usado pela página informativa /loja
INSERT INTO settings (setting_key, setting_value, setting_type, setting_group, description, is_public)
VALUES ('shop_external_url', 'https://shopk.it/', 'url', 'shop', 'URL da loja externa (shopk.it)', 1)
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), setting_type = VALUES(setting_type), setting_group = VALUES(setting_group), description = VALUES(description), is_public = VALUES(is_public);
