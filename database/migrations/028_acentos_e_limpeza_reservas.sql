-- Migration 028 — Restaurar acentos em todo o texto PT (language_id = 1) e
-- limpar as definições de reserva Booking/Airbnb (removidas do site). Idempotente.

-- ------------------------------------------------------------------
-- content_blocks (PT)
-- ------------------------------------------------------------------
UPDATE content_blocks SET content = 'Simplicidade, acolhimento, momentos de convívio marcantes, calor da família, alegria, diversão, gargalhadas e muito amor!' WHERE block_key = 'about_hero_subtitle' AND language_id = 1;
UPDATE content_blocks SET content = 'A Nossa História' WHERE block_key = 'about_hero_tagline' AND language_id = 1;
UPDATE content_blocks SET content = 'Wi-Fi Grátis' WHERE block_key = 'accommodation_feature_1' AND language_id = 1;
UPDATE content_blocks SET content = 'Check-in Autónomo' WHERE block_key = 'accommodation_feature_2' AND language_id = 1;
UPDATE content_blocks SET content = 'Localização Central' WHERE block_key = 'accommodation_feature_4' AND language_id = 1;
UPDATE content_blocks SET content = 'Acolhimento transmontano, momentos em família e memórias para sempre.' WHERE block_key = 'accommodation_hero_subtitle' AND language_id = 1;
UPDATE content_blocks SET content = 'Ambas as casas oferecem o mesmo conforto e hospitalidade transmontana. Escolha a que melhor se adapta à sua estadia.' WHERE block_key = 'accommodation_intro' AND language_id = 1;
UPDATE content_blocks SET content = 'Duas Casas, Uma Experiência' WHERE block_key = 'accommodation_section_subtitle' AND language_id = 1;
UPDATE content_blocks SET content = 'Escolha o Seu Refúgio' WHERE block_key = 'accommodation_section_title' AND language_id = 1;
UPDATE content_blocks SET content = 'De paisagens deslumbrantes a sabores únicos, o nordeste transmontano tem muito para oferecer.' WHERE block_key = 'activities_hero_subtitle' AND language_id = 1;
UPDATE content_blocks SET content = 'Tem alguma questão? Entre em contacto connosco' WHERE block_key = 'contact_hero_subtitle' AND language_id = 1;
UPDATE content_blocks SET content = 'Tem alguma questão? Entre em contacto connosco' WHERE block_key = 'contact_intro' AND language_id = 1;
UPDATE content_blocks SET content = 'Obrigado pelo seu contacto. Iremos responder o mais brevemente possível.' WHERE block_key = 'contact_success_message' AND language_id = 1;
UPDATE content_blocks SET content = 'Esta categoria ainda não tem produtos disponíveis.' WHERE block_key = 'shop_empty_message' AND language_id = 1;
UPDATE content_blocks SET content = 'Sabores autênticos de Trás-os-Montes, selecionados com carinho para a sua mesa.' WHERE block_key = 'shop_intro' AND language_id = 1;

-- ------------------------------------------------------------------
-- amenity_translations (PT)
-- ------------------------------------------------------------------
UPDATE amenity_translations SET name = 'Estacionamento incluído'          WHERE amenity_id = 4  AND language_id = 1;
UPDATE amenity_translations SET name = 'Terraço'                          WHERE amenity_id = 8  AND language_id = 1;
UPDATE amenity_translations SET name = 'Máquina de lavar'                 WHERE amenity_id = 9  AND language_id = 1;
UPDATE amenity_translations SET name = 'Lava-loiça'                       WHERE amenity_id = 10 AND language_id = 1;
UPDATE amenity_translations SET name = 'Área de trabalho para portátil'  WHERE amenity_id = 12 AND language_id = 1;

-- ------------------------------------------------------------------
-- bedroom_translations (PT)
-- ------------------------------------------------------------------
UPDATE bedroom_translations SET name = 'Quarto de Hóspedes' WHERE language_id = 1 AND name = 'Quarto de Hospedes';
UPDATE bedroom_translations SET beds_description = 'Sofá-cama de solteiro, Cama de casal' WHERE language_id = 1 AND beds_description = 'Sofa-cama de solteiro, Cama de casal';

-- ------------------------------------------------------------------
-- bathroom_translations (PT) — corrigir acentos e REMOVER o bidé (o WC não tem).
-- ------------------------------------------------------------------
UPDATE bathroom_translations SET name = 'Casa de Banho Secundária' WHERE language_id = 1 AND name = 'Casa de Banho Secundaria';
UPDATE bathroom_translations SET description = 'Banheira, chuveiro, secador de cabelo' WHERE language_id = 1 AND description = 'Banheira, chuveiro, bide, secador de cabelo';
UPDATE bathroom_translations SET description = 'Chuveiro, lavatório' WHERE language_id = 1 AND description = 'Chuveiro, lavatorio';

-- ------------------------------------------------------------------
-- settings — remover chaves de reserva Booking/Airbnb (já não são usadas).
-- ------------------------------------------------------------------
DELETE FROM settings WHERE setting_key IN ('booking_url', 'airbnb_url');

-- ------------------------------------------------------------------
-- legal_section_translations — remover Airbnb dos textos legais (só GuestReady).
-- ------------------------------------------------------------------
UPDATE legal_section_translations SET content = '<p>Recolhemos apenas os dados necessários para responder aos seus pedidos:</p><ul><li><strong>Formulário de contacto:</strong> nome, email, telefone (opcional), assunto e mensagem;</li><li><strong>Dados técnicos:</strong> endereço IP e informação do navegador, recolhidos automaticamente por motivos de segurança e para prevenir abuso;</li><li><strong>Cookies:</strong> pequenos ficheiros utilizados para o funcionamento do website (ver secção Cookies).</li></ul><p>Não recolhemos dados de pagamento neste website. As reservas e pagamentos são efetuados através da plataforma externa GuestReady, com a sua própria política de privacidade.</p>' WHERE section_id = 2 AND language_id = 1;
UPDATE legal_section_translations SET content = '<p>We only collect the data necessary to respond to your requests:</p><ul><li><strong>Contact form:</strong> name, email, phone (optional), subject and message;</li><li><strong>Technical data:</strong> IP address and browser information, collected automatically for security and abuse prevention;</li><li><strong>Cookies:</strong> small files used for the operation of the website (see Cookies section).</li></ul><p>We do not collect payment data on this website. Bookings and payments are made through the external platform GuestReady, which has its own privacy policy.</p>' WHERE section_id = 2 AND language_id = 2;

UPDATE legal_section_translations SET content = '<p>Não vendemos nem cedemos os seus dados pessoais. Os seus dados poderão ser partilhados apenas com:</p><ul><li>A plataforma de reserva GuestReady, quando efetua uma reserva através da mesma;</li><li>Prestadores de serviços técnicos (alojamento do website), estritamente para operar o serviço;</li><li>Autoridades competentes, quando exigido por lei.</li></ul>' WHERE section_id = 5 AND language_id = 1;
UPDATE legal_section_translations SET content = '<p>We do not sell or transfer your personal data. Your data may only be shared with:</p><ul><li>The booking platform GuestReady, when you make a booking through it;</li><li>Technical service providers (website hosting), strictly to operate the service;</li><li>Competent authorities, when required by law.</li></ul>' WHERE section_id = 5 AND language_id = 2;

UPDATE legal_section_translations SET content = '<p>As reservas e respetivos pagamentos são processados exclusivamente através da plataforma <strong>GuestReady</strong>. Aplicam-se os termos, condições e políticas da plataforma. A Casa do Gi não processa pagamentos diretamente neste website.</p>' WHERE section_id = 13 AND language_id = 1;
UPDATE legal_section_translations SET content = '<p>Bookings and their payments are processed exclusively through the platform <strong>GuestReady</strong>. The terms, conditions and policies of the platform apply. A Casa do Gi does not process payments directly on this website.</p>' WHERE section_id = 13 AND language_id = 2;

UPDATE legal_section_translations SET content = '<p>As condições de cancelamento e reembolso são as definidas pela plataforma através da qual a reserva foi efetuada (GuestReady). Recomendamos a leitura atenta dessas condições no momento da reserva.</p>' WHERE section_id = 14 AND language_id = 1;
UPDATE legal_section_translations SET content = '<p>Cancellation and refund conditions are those defined by the platform through which the booking was made (GuestReady). We recommend reading these conditions carefully at the time of booking.</p>' WHERE section_id = 14 AND language_id = 2;
