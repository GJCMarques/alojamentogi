-- Migration 022 — Remover completamente a loja da base de dados.
-- A loja passa a ser gerida num serviço externo (shopk.it); todo o
-- código, gestão e dados da loja foram removidos do website.
-- Idempotente: usa DROP TABLE IF EXISTS.

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS order_status_history;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS invoices;
DROP TABLE IF EXISTS manual_orders;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS product_translations;
DROP TABLE IF EXISTS product_category_translations;
DROP TABLE IF EXISTS product_categories;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS barcode_batches;

SET FOREIGN_KEY_CHECKS = 1;

-- Remover definições da loja/pagamentos das settings.
DELETE FROM settings WHERE setting_key IN (
    'shop_mode', 'shop_shipping_fee', 'shop_free_shipping_above',
    'shipping_cost', 'free_shipping_threshold',
    'ifthenpay_enabled', 'ifthenpay_entity', 'ifthenpay_subentity',
    'ifthenpay_mbway_key', 'ifthenpay_card_key', 'ifthenpay_anti_phishing_key',
    'ifthenpay_callback_url'
);

-- Manter shop_enabled = 0 e shop_external_url (usados pela página informativa /loja).
UPDATE settings SET setting_value = '0' WHERE setting_key = 'shop_enabled';
