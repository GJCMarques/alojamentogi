-- ============================================================
-- Migration 017 — Reservas + Contacto
-- Idempotente. Telefone real do cliente + remoção do Booking.com dos dados.
-- ============================================================

SET NAMES utf8mb4;

-- Telefone real do cliente
UPDATE settings SET setting_value = '+351 966 691 902' WHERE setting_key = 'contact_phone';

-- Remover Booking.com (manter apenas GuestReady e Airbnb)
UPDATE accommodation SET booking_url = NULL;
UPDATE settings SET setting_value = '' WHERE setting_key = 'booking_url';
