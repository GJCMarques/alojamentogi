-- Migration 010: Invoices System + Shop Modes + Manual Orders
-- A Casa do Gi - Complete Shop System

-- ============================================================
-- 1. INVOICES TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS invoices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    invoice_uuid CHAR(36) NOT NULL UNIQUE COMMENT 'UUID v4 unique identifier',
    barcode CHAR(9) NOT NULL UNIQUE COMMENT '9-digit barcode',
    barcode_batch INT UNSIGNED DEFAULT 1 COMMENT 'Batch number for code recycling',
    integrity_hash CHAR(64) NOT NULL COMMENT 'SHA-256 hash for tamper detection',

    -- Customer snapshot (frozen at invoice time)
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) DEFAULT NULL,
    customer_nif VARCHAR(20) DEFAULT NULL,

    -- Billing address snapshot
    billing_address TEXT DEFAULT NULL,
    billing_postal_code VARCHAR(10) DEFAULT NULL,
    billing_city VARCHAR(100) DEFAULT NULL,

    -- Items snapshot (JSON)
    items_json TEXT NOT NULL COMMENT 'JSON snapshot of order items at invoice time',

    -- Financials
    subtotal DECIMAL(10,2) NOT NULL,
    shipping_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,

    -- Payment info
    payment_method VARCHAR(20) DEFAULT NULL,
    payment_status ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',

    -- Timestamps
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    paid_at DATETIME DEFAULT NULL,
    emailed_at DATETIME DEFAULT NULL,

    -- Notes
    notes TEXT DEFAULT NULL,

    -- Indexes
    INDEX idx_invoice_barcode (barcode),
    INDEX idx_invoice_uuid (invoice_uuid),
    INDEX idx_invoice_order (order_id),
    INDEX idx_invoice_email (customer_email),
    INDEX idx_invoice_status (payment_status),
    INDEX idx_invoice_date (issued_at),

    CONSTRAINT fk_invoice_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 2. BARCODE BATCHES TABLE (for code recycling)
-- ============================================================
CREATE TABLE IF NOT EXISTS barcode_batches (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_number INT UNSIGNED NOT NULL UNIQUE,
    codes_used INT UNSIGNED NOT NULL DEFAULT 0,
    max_codes INT UNSIGNED NOT NULL DEFAULT 999999999,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_batch_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert first batch
INSERT INTO barcode_batches (batch_number, codes_used, is_active) VALUES (1, 0, 1)
ON DUPLICATE KEY UPDATE batch_number = batch_number;


-- ============================================================
-- 3. MANUAL ORDERS TABLE (for manual/phone payment mode)
-- ============================================================
CREATE TABLE IF NOT EXISTS manual_orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- Customer info
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,

    -- Shipping
    shipping_address TEXT DEFAULT NULL,
    shipping_postal_code VARCHAR(10) DEFAULT NULL,
    shipping_city VARCHAR(100) DEFAULT NULL,

    -- Cart snapshot
    items_json TEXT NOT NULL COMMENT 'JSON snapshot of cart items',
    subtotal DECIMAL(10,2) NOT NULL,
    shipping_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,

    -- Status
    status ENUM('new','contacted','converted','cancelled') NOT NULL DEFAULT 'new',
    admin_notes TEXT DEFAULT NULL,
    converted_order_id INT UNSIGNED DEFAULT NULL COMMENT 'If converted to real order',

    -- Meta
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(500) DEFAULT NULL,
    notes TEXT DEFAULT NULL COMMENT 'Customer notes',

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    contacted_at DATETIME DEFAULT NULL,

    INDEX idx_manual_status (status),
    INDEX idx_manual_date (created_at),
    INDEX idx_manual_email (customer_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 4. ADD COLUMNS TO EXISTING TABLES
-- ============================================================

-- Add invoice_id to orders table
ALTER TABLE orders
    ADD COLUMN IF NOT EXISTS invoice_id INT UNSIGNED DEFAULT NULL AFTER id,
    ADD COLUMN IF NOT EXISTS tracking_code VARCHAR(100) DEFAULT NULL AFTER status,
    ADD COLUMN IF NOT EXISTS shipped_at DATETIME DEFAULT NULL AFTER tracking_code,
    ADD COLUMN IF NOT EXISTS delivered_at DATETIME DEFAULT NULL AFTER shipped_at;

-- Add shop_mode setting if not exists
INSERT INTO settings (setting_key, setting_value, setting_type, setting_group, description, is_public)
VALUES ('shop_mode', 'active', 'text', 'shop', 'Modo da loja: active, manual, closed', 0)
ON DUPLICATE KEY UPDATE setting_key = setting_key;
