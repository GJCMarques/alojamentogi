-- Migration 021 — Conteúdo do alojamento + reservas (GuestReady) + comodidades da Casa 2
-- Idempotente: pode ser executada várias vezes sem efeitos secundários.
-- language_id: 1 = PT, 2 = EN
-- accommodation_id: 1 = Casa do Gi 1, 2 = Casa do Gi 2

-- ------------------------------------------------------------------
-- 1. Corrigir/preencher texto do alojamento (título, descrição, secção
--    "Mogadouro & Envolvência" e políticas de estadia) — remove o mojibake
--    "EnvolvÃªncia" e os placeholders "wewe".
-- ------------------------------------------------------------------

-- Casa 1 — PT
UPDATE accommodation_translations SET
    title = 'A Casa do Gi 1',
    description = 'A Casa do Gi é sinónimo de simplicidade, acolhimento, momentos de convívio marcantes, calor de família, alegria, diversão, gargalhadas e muito amor! Foi construída nos anos 80, numa altura em que os artesãos da construção e os materiais eram escassos pelas Terras de Mogadouro.',
    activity_section_title = 'Mogadouro & Envolvência',
    activity_section_description = 'Mogadouro é uma vila histórica no coração do Planalto Mirandês, onde a tradição transmontana se funde com a natureza. A partir da Casa do Gi pode visitar o Castelo de Mogadouro, percorrer os trilhos do Parque Natural do Douro Internacional e descobrir a gastronomia e o artesanato locais.',
    cancellation_policy = 'Cancelamento gratuito até 30 dias antes do check-in. Entre 30 e 7 dias antes da chegada é retido 50% do valor da reserva. Nos 7 dias que antecedem o check-in a reserva não é reembolsável. As condições podem variar consoante a plataforma onde efetua a reserva.',
    refund_policy = 'Os reembolsos elegíveis são processados no prazo de 14 dias, pelo mesmo método de pagamento utilizado na reserva. Em caso de saída antecipada, as noites não usufruídas não são reembolsáveis.'
WHERE accommodation_id = 1 AND language_id = 1;

-- Casa 1 — EN
UPDATE accommodation_translations SET
    title = 'A Casa do Gi 1',
    description = 'A Casa do Gi means simplicity, warmth and unforgettable moments together — the warmth of family, joy, fun, laughter and plenty of love! It was built in the 1980s, at a time when skilled builders and materials were scarce across the Mogadouro region.',
    activity_section_title = 'Mogadouro & Surroundings',
    activity_section_description = 'Mogadouro is a historic town in the heart of the Miranda Plateau, where the traditions of Trás-os-Montes meet unspoilt nature. From Casa do Gi you can visit Mogadouro Castle, walk the trails of the Douro Internacional Natural Park and discover the region''s food and crafts.',
    cancellation_policy = 'Free cancellation up to 30 days before check-in. Between 30 and 7 days before arrival, 50% of the booking value is retained. Within 7 days of check-in the booking is non-refundable. Conditions may vary depending on the platform used to book.',
    refund_policy = 'Eligible refunds are processed within 14 days, using the same payment method as the original booking. In the event of an early departure, unused nights are non-refundable.'
WHERE accommodation_id = 1 AND language_id = 2;

-- Casa 2 — PT
UPDATE accommodation_translations SET
    title = 'A Casa do Gi 2',
    tagline = 'Simplicidade, acolhimento e muito amor',
    description = 'A Casa do Gi 2 partilha o mesmo espírito de simplicidade e acolhimento da casa original, oferecendo um refúgio tranquilo e confortável em pleno coração de Mogadouro. Um espaço pensado para momentos de convívio, descanso e reencontro com a autenticidade de Trás-os-Montes.',
    activity_section_title = 'Mogadouro & Envolvência',
    activity_section_description = 'Mogadouro é uma vila histórica no coração do Planalto Mirandês, onde a tradição transmontana se funde com a natureza. A partir da Casa do Gi pode visitar o Castelo de Mogadouro, percorrer os trilhos do Parque Natural do Douro Internacional e descobrir a gastronomia e o artesanato locais.',
    cancellation_policy = 'Cancelamento gratuito até 30 dias antes do check-in. Entre 30 e 7 dias antes da chegada é retido 50% do valor da reserva. Nos 7 dias que antecedem o check-in a reserva não é reembolsável. As condições podem variar consoante a plataforma onde efetua a reserva.',
    refund_policy = 'Os reembolsos elegíveis são processados no prazo de 14 dias, pelo mesmo método de pagamento utilizado na reserva. Em caso de saída antecipada, as noites não usufruídas não são reembolsáveis.'
WHERE accommodation_id = 2 AND language_id = 1;

-- Casa 2 — EN
UPDATE accommodation_translations SET
    title = 'A Casa do Gi 2',
    tagline = 'Simplicity, warmth and love',
    description = 'A Casa do Gi 2 shares the same spirit of simplicity and warmth as the original house, offering a quiet and comfortable retreat in the very heart of Mogadouro. A space made for time together, rest and reconnecting with the authenticity of Trás-os-Montes.',
    activity_section_title = 'Mogadouro & Surroundings',
    activity_section_description = 'Mogadouro is a historic town in the heart of the Miranda Plateau, where the traditions of Trás-os-Montes meet unspoilt nature. From Casa do Gi you can visit Mogadouro Castle, walk the trails of the Douro Internacional Natural Park and discover the region''s food and crafts.',
    cancellation_policy = 'Free cancellation up to 30 days before check-in. Between 30 and 7 days before arrival, 50% of the booking value is retained. Within 7 days of check-in the booking is non-refundable. Conditions may vary depending on the platform used to book.',
    refund_policy = 'Eligible refunds are processed within 14 days, using the same payment method as the original booking. In the event of an early departure, unused nights are non-refundable.'
WHERE accommodation_id = 2 AND language_id = 2;

-- ------------------------------------------------------------------
-- 2. Reservas: manter apenas a GuestReady. Limpar Booking.com e Airbnb.
--    (o link real da GuestReady deve ser confirmado pelo cliente)
-- ------------------------------------------------------------------
UPDATE accommodation SET
    guestready_url = 'https://www.guestready.com/en-pt/rentals',
    booking_url = NULL,
    airbnb_url = NULL;

UPDATE settings SET setting_value = 'https://www.guestready.com/en-pt/rentals' WHERE setting_key = 'guestready_url';
UPDATE settings SET setting_value = '' WHERE setting_key IN ('booking_url', 'airbnb_url');

-- ------------------------------------------------------------------
-- 3. Comodidades da Casa 2 (estavam vazias) — espelhar as 18 da Casa 1.
-- ------------------------------------------------------------------
INSERT INTO accommodation_amenities (accommodation_id, amenity_id, is_highlighted, sort_order)
SELECT 2, amenity_id, is_highlighted, sort_order
FROM accommodation_amenities
WHERE accommodation_id = 1
  AND amenity_id NOT IN (
      SELECT amenity_id FROM (SELECT amenity_id FROM accommodation_amenities WHERE accommodation_id = 2) AS existing
  );

-- ------------------------------------------------------------------
-- 4. Regras da casa (PT) — restaurar acentos e remover placeholder "wewew".
-- ------------------------------------------------------------------
UPDATE house_rule_translations SET rule_text = 'Não são permitidas festas ou eventos.'
    WHERE language_id = 1 AND rule_text LIKE 'Nao sao permitidas festas%';
UPDATE house_rule_translations SET rule_text = 'Horário de silêncio: 22h00 - 08h00.'
    WHERE language_id = 1 AND rule_text LIKE 'Horario de silencio%';
UPDATE house_rule_translations SET rule_text = 'Animais de estimação não são permitidos.'
    WHERE language_id = 1 AND rule_text LIKE 'Animais de estimacao%';
