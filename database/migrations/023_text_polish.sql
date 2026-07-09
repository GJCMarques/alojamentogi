-- Migration 023 — Polimento de texto (PT + EN): restaurar acento em "Trás-os-Montes".
-- REPLACE ao nível dos bytes só substitui as ocorrências realmente sem acento
-- ("Tras-os-Montes"); as já corretas ("Trás-os-Montes") não são afetadas. Idempotente.

UPDATE content_blocks
    SET content = REPLACE(content, 'Tras-os-Montes', 'Trás-os-Montes')
    WHERE content LIKE '%Tras-os-Montes%';

UPDATE accommodation_translations
    SET description = REPLACE(description, 'Tras-os-Montes', 'Trás-os-Montes')
    WHERE description LIKE '%Tras-os-Montes%';

UPDATE accommodation_translations
    SET activity_section_description = REPLACE(activity_section_description, 'Tras-os-Montes', 'Trás-os-Montes')
    WHERE activity_section_description LIKE '%Tras-os-Montes%';

UPDATE accommodation
    SET region = 'Trás-os-Montes'
    WHERE region = 'Tras-os-Montes';
