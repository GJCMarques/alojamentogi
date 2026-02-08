-- =============================================
-- A Casa do Gi - Shop Page Heroes (Product, Cart, Checkout)
-- Run this in phpMyAdmin SQL tab
-- =============================================

-- Add heroes for Product Detail, Cart, and Checkout pages
INSERT IGNORE INTO page_heroes (page_key, page_name_pt, page_name_en, hero_overlay_opacity, is_active, sort_order) VALUES
('product_detail', 'Produto (Detalhe)', 'Product (Detail)', 0.40, 1, 7);

INSERT IGNORE INTO page_heroes (page_key, page_name_pt, page_name_en, hero_overlay_opacity, is_active, sort_order) VALUES
('cart', 'Carrinho de Compras', 'Shopping Cart', 0.40, 1, 8);

INSERT IGNORE INTO page_heroes (page_key, page_name_pt, page_name_en, hero_overlay_opacity, is_active, sort_order) VALUES
('checkout', 'Finalizar Compra', 'Checkout', 0.40, 1, 9);

-- Done! Now refresh the Heroes admin page.
