-- ============================================================
-- Migration 018 — Conteúdo legal (Política de Privacidade + Termos e Condições)
-- Corrige acentos nos heros e popula legal_sections (estava vazia).
-- Idempotente: substitui as secções legais em cada execução.
-- ============================================================

SET NAMES utf8mb4;

-- ---------- Corrigir acentos nos blocos hero (PT) ----------
UPDATE content_blocks SET content = 'Política de Privacidade' WHERE block_key = 'privacy_hero_title' AND language_id = 1;
UPDATE content_blocks SET content = 'Informação Legal' WHERE block_key = 'privacy_hero_tagline' AND language_id = 1;
UPDATE content_blocks SET content = 'A sua privacidade é importante para nós. Saiba como tratamos os seus dados.' WHERE block_key = 'privacy_hero_subtitle' AND language_id = 1;
UPDATE content_blocks SET content = 'Atualizado em: julho de 2026' WHERE block_key = 'privacy_date' AND language_id = 1;
UPDATE content_blocks SET content = 'Updated: July 2026' WHERE block_key = 'privacy_date' AND language_id = 2;
UPDATE content_blocks SET content = 'Termos e Condições' WHERE block_key = 'terms_hero_title' AND language_id = 1;
UPDATE content_blocks SET content = 'Informação Legal' WHERE block_key = 'terms_hero_tagline' AND language_id = 1;
UPDATE content_blocks SET content = 'Por favor, leia atentamente os termos e condições de utilização do nosso serviço.' WHERE block_key = 'terms_hero_subtitle' AND language_id = 1;
UPDATE content_blocks SET content = 'Atualizado em: julho de 2026' WHERE block_key = 'terms_date' AND language_id = 1;
UPDATE content_blocks SET content = 'Updated: July 2026' WHERE block_key = 'terms_date' AND language_id = 2;

-- ---------- Repor secções legais ----------
DELETE FROM legal_sections WHERE page IN ('privacy', 'terms'); -- cascata apaga traduções

-- =========================== PRIVACIDADE ===========================
INSERT INTO legal_sections (id, page, sort_order, is_active) VALUES
 (1, 'privacy', 1, 1),
 (2, 'privacy', 2, 1),
 (3, 'privacy', 3, 1),
 (4, 'privacy', 4, 1),
 (5, 'privacy', 5, 1),
 (6, 'privacy', 6, 1),
 (7, 'privacy', 7, 1),
 (8, 'privacy', 8, 1),
 (9, 'privacy', 9, 1);

INSERT INTO legal_section_translations (section_id, language_id, title, content) VALUES
(1, 1, 'Introdução e Responsável pelo Tratamento', '<p>A presente Política de Privacidade descreve como a <strong>A Casa do Gi</strong>, alojamento local situado em Mogadouro, recolhe, utiliza e protege os dados pessoais dos visitantes e hóspedes deste website, em conformidade com o Regulamento Geral sobre a Proteção de Dados (RGPD - Regulamento UE 2016/679) e a legislação portuguesa aplicável.</p><p>Para qualquer questão relativa aos seus dados pessoais, pode contactar-nos através do email <a href="mailto:geral@acasadogi.pt">geral@acasadogi.pt</a>.</p>'),
(2, 1, 'Dados que Recolhemos', '<p>Recolhemos apenas os dados necessários para responder aos seus pedidos:</p><ul><li><strong>Formulário de contacto:</strong> nome, email, telefone (opcional), assunto e mensagem;</li><li><strong>Dados técnicos:</strong> endereço IP e informação do navegador, recolhidos automaticamente por motivos de segurança e para prevenir abuso;</li><li><strong>Cookies:</strong> pequenos ficheiros utilizados para o funcionamento do website (ver secção Cookies).</li></ul><p>Não recolhemos dados de pagamento neste website. As reservas e pagamentos são efetuados através de plataformas externas (GuestReady e Airbnb), com as suas próprias políticas de privacidade.</p>'),
(3, 1, 'Como Utilizamos os Seus Dados', '<p>Utilizamos os seus dados pessoais para:</p><ul><li>Responder às mensagens enviadas através do formulário de contacto;</li><li>Prestar informações sobre o alojamento e as reservas;</li><li>Garantir a segurança do website e prevenir spam ou utilização indevida;</li><li>Cumprir obrigações legais aplicáveis.</li></ul><p>A base legal para o tratamento é o seu consentimento e o nosso interesse legítimo em responder aos contactos e proteger o website.</p>'),
(4, 1, 'Cookies', '<p>Este website utiliza cookies essenciais para o seu correto funcionamento e para memorizar preferências (como o idioma). Não utilizamos cookies de publicidade. Pode configurar o seu navegador para bloquear ou eliminar cookies, embora algumas funcionalidades possam ficar limitadas.</p>'),
(5, 1, 'Partilha de Dados com Terceiros', '<p>Não vendemos nem cedemos os seus dados pessoais. Os seus dados poderão ser partilhados apenas com:</p><ul><li>Plataformas de reserva (GuestReady, Airbnb), quando efetua uma reserva através das mesmas;</li><li>Prestadores de serviços técnicos (alojamento do website), estritamente para operar o serviço;</li><li>Autoridades competentes, quando exigido por lei.</li></ul>'),
(6, 1, 'Conservação dos Dados', '<p>Conservamos os dados do formulário de contacto apenas durante o tempo necessário para responder ao seu pedido e cumprir eventuais obrigações legais. Os registos técnicos de segurança são eliminados periodicamente.</p>'),
(7, 1, 'Os Seus Direitos', '<p>Nos termos do RGPD, tem o direito de aceder, retificar, apagar, limitar ou opor-se ao tratamento dos seus dados, bem como o direito à portabilidade. Para exercer estes direitos, contacte-nos através de <a href="mailto:geral@acasadogi.pt">geral@acasadogi.pt</a>. Tem ainda o direito de apresentar reclamação junto da Comissão Nacional de Proteção de Dados (CNPD).</p>'),
(8, 1, 'Segurança', '<p>Adotamos medidas técnicas e organizativas adequadas para proteger os seus dados contra acesso, alteração ou divulgação não autorizados, incluindo ligação cifrada (HTTPS) e controlo de acessos.</p>'),
(9, 1, 'Alterações a esta Política', '<p>Esta Política de Privacidade pode ser atualizada periodicamente. A data da última atualização é indicada no topo desta página. Recomendamos a sua consulta regular.</p>');

INSERT INTO legal_section_translations (section_id, language_id, title, content) VALUES
(1, 2, 'Introduction and Data Controller', '<p>This Privacy Policy describes how <strong>A Casa do Gi</strong>, a local accommodation located in Mogadouro, Portugal, collects, uses and protects the personal data of visitors and guests of this website, in accordance with the General Data Protection Regulation (GDPR - Regulation EU 2016/679) and applicable Portuguese law.</p><p>For any questions regarding your personal data, you may contact us at <a href="mailto:geral@acasadogi.pt">geral@acasadogi.pt</a>.</p>'),
(2, 2, 'Data We Collect', '<p>We only collect the data necessary to respond to your requests:</p><ul><li><strong>Contact form:</strong> name, email, phone (optional), subject and message;</li><li><strong>Technical data:</strong> IP address and browser information, collected automatically for security and abuse prevention;</li><li><strong>Cookies:</strong> small files used for the operation of the website (see Cookies section).</li></ul><p>We do not collect payment data on this website. Bookings and payments are made through external platforms (GuestReady and Airbnb), which have their own privacy policies.</p>'),
(3, 2, 'How We Use Your Data', '<p>We use your personal data to:</p><ul><li>Respond to messages sent through the contact form;</li><li>Provide information about the accommodation and bookings;</li><li>Ensure website security and prevent spam or misuse;</li><li>Comply with applicable legal obligations.</li></ul><p>The legal basis for processing is your consent and our legitimate interest in responding to contacts and protecting the website.</p>'),
(4, 2, 'Cookies', '<p>This website uses essential cookies for its correct operation and to remember preferences (such as language). We do not use advertising cookies. You can configure your browser to block or delete cookies, although some features may be limited.</p>'),
(5, 2, 'Sharing Data with Third Parties', '<p>We do not sell or transfer your personal data. Your data may only be shared with:</p><ul><li>Booking platforms (GuestReady, Airbnb) when you make a booking through them;</li><li>Technical service providers (website hosting), strictly to operate the service;</li><li>Competent authorities, when required by law.</li></ul>'),
(6, 2, 'Data Retention', '<p>We keep contact form data only for as long as necessary to respond to your request and comply with any legal obligations. Technical security logs are deleted periodically.</p>'),
(7, 2, 'Your Rights', '<p>Under the GDPR, you have the right to access, rectify, erase, restrict or object to the processing of your data, as well as the right to data portability. To exercise these rights, contact us at <a href="mailto:geral@acasadogi.pt">geral@acasadogi.pt</a>. You also have the right to lodge a complaint with the Portuguese Data Protection Authority (CNPD).</p>'),
(8, 2, 'Security', '<p>We adopt appropriate technical and organisational measures to protect your data against unauthorised access, alteration or disclosure, including encrypted connection (HTTPS) and access control.</p>'),
(9, 2, 'Changes to this Policy', '<p>This Privacy Policy may be updated periodically. The date of the last update is shown at the top of this page. We recommend that you review it regularly.</p>');

-- =========================== TERMOS ===========================
INSERT INTO legal_sections (id, page, sort_order, is_active) VALUES
 (11, 'terms', 1, 1),
 (12, 'terms', 2, 1),
 (13, 'terms', 3, 1),
 (14, 'terms', 4, 1),
 (15, 'terms', 5, 1),
 (16, 'terms', 6, 1),
 (17, 'terms', 7, 1),
 (18, 'terms', 8, 1);

INSERT INTO legal_section_translations (section_id, language_id, title, content) VALUES
(11, 1, 'Aceitação dos Termos', '<p>Ao aceder e utilizar o website da <strong>A Casa do Gi</strong>, o utilizador aceita os presentes Termos e Condições. Caso não concorde com os mesmos, deverá abster-se de utilizar o website.</p>'),
(12, 1, 'Objeto', '<p>Este website tem caráter informativo e destina-se a divulgar o alojamento local A Casa do Gi, situado em Mogadouro, bem como atividades e pontos de interesse da região. As reservas são efetuadas através de plataformas externas.</p>'),
(13, 1, 'Reservas e Pagamentos', '<p>As reservas e respetivos pagamentos são processados exclusivamente através das plataformas parceiras <strong>GuestReady</strong> e <strong>Airbnb</strong>. Aplicam-se os termos, condições e políticas de cada plataforma. A Casa do Gi não processa pagamentos diretamente neste website.</p>'),
(14, 1, 'Cancelamentos', '<p>As condições de cancelamento e reembolso são as definidas pela plataforma através da qual a reserva foi efetuada (GuestReady ou Airbnb). Recomendamos a leitura atenta dessas condições no momento da reserva.</p>'),
(15, 1, 'Regras do Alojamento', '<p>Os hóspedes comprometem-se a utilizar o alojamento de forma responsável, respeitando as regras da casa, o descanso da vizinhança e o património. Não é permitido exceder o número de hóspedes indicado na reserva sem autorização prévia.</p>'),
(16, 1, 'Responsabilidades e Limitações', '<p>Envidamos os melhores esforços para manter a informação do website atualizada e correta, não nos responsabilizando por eventuais imprecisões ou pela indisponibilidade temporária do serviço. A Casa do Gi não é responsável pelos conteúdos ou serviços de websites de terceiros para os quais existam ligações.</p>'),
(17, 1, 'Propriedade Intelectual', '<p>Todos os conteúdos deste website (textos, imagens, marca e design) são propriedade da A Casa do Gi ou utilizados com autorização, estando protegidos por direitos de autor. É proibida a sua reprodução sem autorização.</p>'),
(18, 1, 'Lei Aplicável e Foro', '<p>Os presentes Termos regem-se pela lei portuguesa. Para a resolução de quaisquer litígios é competente o foro da comarca de Bragança, com renúncia a qualquer outro. O consumidor pode ainda recorrer a entidades de resolução alternativa de litígios de consumo.</p>');

INSERT INTO legal_section_translations (section_id, language_id, title, content) VALUES
(11, 2, 'Acceptance of Terms', '<p>By accessing and using the <strong>A Casa do Gi</strong> website, the user accepts these Terms and Conditions. If you do not agree with them, you should refrain from using the website.</p>'),
(12, 2, 'Purpose', '<p>This website is informative in nature and is intended to present the A Casa do Gi local accommodation, located in Mogadouro, Portugal, as well as activities and points of interest in the region. Bookings are made through external platforms.</p>'),
(13, 2, 'Bookings and Payments', '<p>Bookings and their payments are processed exclusively through the partner platforms <strong>GuestReady</strong> and <strong>Airbnb</strong>. The terms, conditions and policies of each platform apply. A Casa do Gi does not process payments directly on this website.</p>'),
(14, 2, 'Cancellations', '<p>Cancellation and refund conditions are those defined by the platform through which the booking was made (GuestReady or Airbnb). We recommend reading these conditions carefully at the time of booking.</p>'),
(15, 2, 'Accommodation Rules', '<p>Guests agree to use the accommodation responsibly, respecting the house rules, the neighbours'' rest and the property. Exceeding the number of guests stated in the booking without prior authorisation is not permitted.</p>'),
(16, 2, 'Liability and Limitations', '<p>We make our best efforts to keep the website information up to date and accurate, and are not liable for any inaccuracies or for temporary unavailability of the service. A Casa do Gi is not responsible for the content or services of third-party websites to which links may exist.</p>'),
(17, 2, 'Intellectual Property', '<p>All content on this website (texts, images, brand and design) is the property of A Casa do Gi or used with permission, and is protected by copyright. Reproduction without authorisation is prohibited.</p>'),
(18, 2, 'Applicable Law and Jurisdiction', '<p>These Terms are governed by Portuguese law. Any disputes shall be subject to the jurisdiction of the district of Bragança, Portugal. Consumers may also resort to alternative consumer dispute resolution bodies.</p>');
