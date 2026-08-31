-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 31-Ago-2026 às 16:19
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `acasadogi`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `accommodation`
--

CREATE TABLE `accommodation` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(100) NOT NULL DEFAULT 'casa-do-gi',
  `max_guests` int(10) UNSIGNED DEFAULT 6,
  `bedrooms` int(10) UNSIGNED DEFAULT 3,
  `bathrooms` int(10) UNSIGNED DEFAULT 2,
  `area_sqm` decimal(6,2) DEFAULT 100.00,
  `floor_number` int(11) DEFAULT 1,
  `has_elevator` tinyint(1) DEFAULT 0,
  `check_in_time` time DEFAULT '16:00:00',
  `check_out_time` time DEFAULT '11:00:00',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT '146729/AL',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `rating` decimal(2,1) DEFAULT NULL COMMENT 'Average rating (e.g., 4.3)',
  `reviews_count` int(10) UNSIGNED DEFAULT 0 COMMENT 'Total number of reviews',
  `city` varchar(100) DEFAULT 'Mogadouro' COMMENT 'City name',
  `region` varchar(100) DEFAULT 'Trás-os-Montes' COMMENT 'Region name',
  `country` varchar(100) DEFAULT 'Portugal' COMMENT 'Country name',
  `host_type` enum('professional','superhost','standard') DEFAULT 'standard' COMMENT 'Host type badge',
  `checkin_type` enum('self_checkin','meet_host','key_lockbox','smart_lock') DEFAULT 'self_checkin' COMMENT 'Check-in method',
  `checkin_instructions` text DEFAULT NULL COMMENT 'Check-in instructions (internal)',
  `towels_linens_included` tinyint(1) DEFAULT 1 COMMENT 'Towels and linens provided',
  `min_nights` int(10) UNSIGNED DEFAULT 1 COMMENT 'Minimum nights stay',
  `instant_booking` tinyint(1) DEFAULT 0 COMMENT 'Instant booking available',
  `accommodation_number` int(10) UNSIGNED DEFAULT 1 COMMENT 'Casa 1 or Casa 2',
  `guestready_url` varchar(500) DEFAULT NULL COMMENT 'GuestReady booking URL',
  `booking_url` varchar(500) DEFAULT NULL COMMENT 'Booking.com URL',
  `airbnb_url` varchar(500) DEFAULT NULL COMMENT 'Airbnb URL'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `accommodation`
--

INSERT INTO `accommodation` (`id`, `slug`, `max_guests`, `bedrooms`, `bathrooms`, `area_sqm`, `floor_number`, `has_elevator`, `check_in_time`, `check_out_time`, `latitude`, `longitude`, `license_number`, `created_at`, `updated_at`, `is_active`, `rating`, `reviews_count`, `city`, `region`, `country`, `host_type`, `checkin_type`, `checkin_instructions`, `towels_linens_included`, `min_nights`, `instant_booking`, `accommodation_number`, `guestready_url`, `booking_url`, `airbnb_url`) VALUES
(1, 'casa-do-gi-1', 6, 3, 2, 100.00, 1, 0, '16:00:00', '11:00:00', 41.34217000, -6.71347000, '146729/AL', '2026-01-19 12:51:19', '2026-08-28 20:11:38', 1, 4.3, 116, 'Mogadouro', 'Tras-os-Montes', 'Portugal', 'standard', 'self_checkin', NULL, 1, 1, 0, 1, 'https://book.guestready.com/pt/properties/mogadouro/fuga-ecletica-em-mogadouro/72622', NULL, NULL),
(2, 'casa-do-gi-2', 6, 3, 2, 100.00, 2, 0, '16:00:00', '11:00:00', 41.34217000, -6.71347000, '146731/AL', '2026-01-30 02:22:49', '2026-08-30 18:58:46', 1, 4.3, 111, 'Mogadouro', 'Tras-os-Montes', 'Portugal', 'standard', 'self_checkin', NULL, 1, 1, 0, 2, 'https://book.guestready.com/pt/properties/mogadouro/fuga-ecletica-em-mogadouro/72622', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `accommodation_amenities`
--

CREATE TABLE `accommodation_amenities` (
  `accommodation_id` int(10) UNSIGNED NOT NULL,
  `amenity_id` int(10) UNSIGNED NOT NULL,
  `is_highlighted` tinyint(1) DEFAULT 0 COMMENT 'Show in main section (top 8)',
  `sort_order` int(10) UNSIGNED DEFAULT 0 COMMENT 'Display order'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `accommodation_amenities`
--

INSERT INTO `accommodation_amenities` (`accommodation_id`, `amenity_id`, `is_highlighted`, `sort_order`) VALUES
(1, 1, 0, 1),
(1, 2, 0, 2),
(1, 3, 0, 3),
(1, 4, 0, 4),
(1, 8, 0, 19),
(1, 10, 0, 6),
(1, 11, 0, 15),
(1, 12, 0, 5),
(1, 13, 0, 7),
(1, 14, 0, 8),
(1, 15, 0, 9),
(1, 16, 0, 10),
(1, 17, 0, 11),
(1, 19, 0, 12),
(1, 21, 0, 13),
(1, 23, 0, 14),
(1, 24, 0, 16),
(1, 25, 0, 17),
(1, 26, 0, 18),
(1, 27, 0, 22),
(1, 28, 0, 23),
(1, 29, 0, 24),
(1, 30, 0, 25),
(1, 31, 0, 26),
(1, 32, 0, 27),
(1, 34, 0, 28),
(1, 35, 0, 20),
(1, 36, 0, 21),
(1, 39, 0, 29),
(1, 40, 0, 30),
(2, 1, 0, 1),
(2, 2, 0, 2),
(2, 3, 0, 3),
(2, 4, 0, 4),
(2, 8, 0, 19),
(2, 10, 0, 6),
(2, 11, 0, 15),
(2, 12, 0, 5),
(2, 13, 0, 7),
(2, 14, 0, 8),
(2, 15, 0, 9),
(2, 16, 0, 10),
(2, 17, 0, 11),
(2, 19, 0, 12),
(2, 21, 0, 13),
(2, 23, 0, 14),
(2, 24, 0, 16),
(2, 25, 0, 17),
(2, 26, 0, 18),
(2, 27, 0, 22),
(2, 28, 0, 23),
(2, 29, 0, 24),
(2, 30, 0, 25),
(2, 31, 0, 26),
(2, 32, 0, 27),
(2, 34, 0, 28),
(2, 35, 0, 20),
(2, 36, 0, 21),
(2, 39, 0, 29),
(2, 40, 0, 30);

-- --------------------------------------------------------

--
-- Estrutura da tabela `accommodation_translations`
--

CREATE TABLE `accommodation_translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `accommodation_id` int(10) UNSIGNED NOT NULL,
  `language_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `short_description` text DEFAULT NULL,
  `full_description` text DEFAULT NULL,
  `house_rules` text DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT 'A Casa do Gi',
  `tagline` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location_description` text DEFAULT NULL COMMENT 'Description of the location/neighborhood',
  `refund_policy` text DEFAULT NULL COMMENT 'Refund/cancellation policy text',
  `checkin_description` varchar(255) DEFAULT NULL COMMENT 'Check-in description for guests',
  `host_description` text DEFAULT NULL COMMENT 'About the host',
  `cancellation_policy` text DEFAULT NULL COMMENT 'Cancellation policy text',
  `activity_section_title` varchar(255) DEFAULT NULL COMMENT 'Title for activities section',
  `activity_section_description` text DEFAULT NULL COMMENT 'Description for activities section'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `accommodation_translations`
--

INSERT INTO `accommodation_translations` (`id`, `accommodation_id`, `language_id`, `title`, `short_description`, `full_description`, `house_rules`, `name`, `tagline`, `description`, `location_description`, `refund_policy`, `checkin_description`, `host_description`, `cancellation_policy`, `activity_section_title`, `activity_section_description`) VALUES
(1, 1, 1, 'A Casa do Gi', 'Esta encantadora casa com 3 quartos em Mogadouro é perfeita para famílias e amigos ficarem enquanto visitam esta cidade. Tem tudo o que é necessário para uma estadia agradável. A propriedade fica perto de várias atracções (por exemplo, Estátua de Trindade Coelho, Baloiço de São Cristóvão), bons restaurantes e lojas.', 'Bem vindo!\n\nTradicionalmente mobilada e decorada com bom gosto, esta linda casa (100 m2) fica perto de várias atracções de Mogadouro, como a Estátua de Trindade Coelho e o Baloiço de São Cristóvão!\n\nA sala de estar tem uma área de estar confortável, uma televisão para ver todos os seus programas favoritos e uma mesa de jantar. É também generosamente iluminada, ideal para partilhar momentos com o seu grupo. \n\nA cozinha inclui electrodomésticos topo de gama, utensílios de cozinha essenciais e talheres que satisfarão as suas necessidades culinárias. \n\nO primeiro quarto tem uma cama de casal, o segundo tem duas camas individuais e o terceiro tem um sofá-cama de solteiro e uma cama de casal. Todas as camas estão equipadas com roupa de cama de qualidade de hotel para garantir uma boa noite de sono.\n\nAs casas de banho têm todas as comodidades de que necessita para se refrescar, incluindo toalhas limpas e produtos de higiene pessoal para sua conveniência.\n\nA casa é sempre limpa profissionalmente para seu conforto.\n\nDesfrute da sua estadia!', 'Adequado para crianças e bebés\nNão são permitidos animais de estimação\nNão são permitidas festas\nNão é permitido fumar\n\nOutras informações:\nExiste uma política de tolerância zero para fumar na propriedade. Se a nossa equipa descobrir qualquer prova de que esta regra foi violada, reservamo-nos o direito de cobrar uma taxa no valor de 200 EUR, no mínimo.\n\nPor favor, note que para estadias superiores a 30 noites, existe uma política de uso justo dos utilitários que será aplicada com um limite de 80 € incluído.\n\nPara os primeiros dias, fornecemos as seguintes comodidades básicas: pequenos recipientes de gel de banho, shampoo e sabonete, papel higiénico, rolo de cozinha, esponja, detergente da loiça e saco do lixo.\n\nChaves extra: 20€ (conjunto extra quando possível, perda de chaves ou serviço de abertura de porta).\nLimpeza extra: preço de uma taxa de limpeza.\nRoupa Extra: 30€ (toalhas e lençóis para 2 pessoas, ou seja, quando o sofá-cama não está incluído).', 'A Casa do Gi', 'Simplicidade, acolhimento e muito amor', '<div class=\"space-y-4 text-charcoal/80 leading-relaxed\">\r\n    <p><strong>Bem-vindo à Casa do Gi!</strong></p>\r\n    <p>Esta encantadora e acolhedora casa de 100m² com 3 quartos em Mogadouro é perfeita para famílias e grupos de amigos. Tradicionalmente mobilada e decorada com muito bom gosto, dispõe de tudo o que necessita para uma estadia memorável e fica a escassos passos das principais atrações da vila (como a Estátua de Trindade Coelho e o famoso Baloiço de São Cristóvão), bem como de excelentes restaurantes e comércio local.</p>\r\n    \r\n    <p>A casa é sempre limpa profissionalmente para seu máximo conforto. Desfrute da sua estadia!</p>\r\n</div>', '', '• Tolerância zero ao fumo no interior (multa a partir de 200€).\r\n• Estadias longas (>30 noites) têm limite de 80€ incluído para utilitários.\r\n• Incluímos comodidades básicas (gel de banho, champô, sabonete, etc.) para os primeiros dias.\r\n• Taxas adicionais: 20€ para chaves extra/abertura de porta; 30€ para roupa de cama extra (sofá-cama). Limpeza extra também disponível sob taxa.', '', '', 'Reembolso total até 5 dias antes da chegada. Processo gerido através da GuestReady.', 'Mogadouro & Envolvência', '<div class=\"space-y-4 text-charcoal/80 leading-relaxed\">\r\n    <p>Mogadouro, uma vila encantadora, oferece um conjunto de atrações que mostram a sua rica história e património cultural, proporcionando uma experiência memorável.</p>\r\n    \r\n    <ul class=\"list-none space-y-3 mt-4\">\r\n        <li class=\"flex items-start gap-2\">\r\n            <span class=\"text-secondary mt-1\">✦</span>\r\n            <span><strong>Património e Cultura:</strong> Visite a majestosa Igreja Matriz e o Museu de Arqueologia, que traçam as origens da vila. Não perca a estátua em homenagem ao ilustre escritor transmontano Trindade Coelho.</span>\r\n        </li>\r\n        <li class=\"flex items-start gap-2\">\r\n            <span class=\"text-secondary mt-1\">✦</span>\r\n            <span><strong>Natureza e Aventura:</strong> Desfrute das vistas panorâmicas emocionantes no Baloiço de São Cristóvão e explore o Parque Natural do Douro Internacional, um extenso santuário que alberga águias e abutres protegidos.</span>\r\n        </li>\r\n    </ul>\r\n</div>'),
(2, 1, 2, 'A Casa do Gi', 'This charming 3-bedroom house in Mogadouro is perfect for families and friends to stay while visiting this town. It has everything you need for a pleasant stay. The property is close to several attractions (e.g., Trindade Coelho Statue, São Cristóvão Swing), good restaurants, and shops.', 'Welcome!\n\nTraditionally furnished and tastefully decorated, this lovely house (100 m2) is close to several attractions in Mogadouro, such as the Trindade Coelho Statue and the São Cristóvão Swing!\n\nThe living room has a comfortable seating area, a television to watch all your favorite shows, and a dining table. It is also generously illuminated, ideal for sharing moments with your group.\n\nThe kitchen includes top-of-the-line appliances, essential cooking utensils, and cutlery that will satisfy your culinary needs.\n\nThe first bedroom has a double bed, the second has two single beds, and the third has a single sofa bed and a double bed. All beds are equipped with hotel-quality linens to ensure a good night\'s sleep.\n\nThe bathrooms have all the amenities you need to freshen up, including clean towels and complimentary toiletries for your convenience.\n\nThe house is always professionally cleaned for your comfort.\n\nEnjoy your stay!', 'Suitable for children and babies\nPets are not allowed\nParties are not allowed\nSmoking is not allowed\n\nOther information:\nThere is a zero-tolerance policy for smoking on the property. If our team discovers any evidence that this rule was violated, we reserve the right to charge a minimum fee of 200 EUR.\n\nPlease note that for stays longer than 30 nights, a fair use policy for utilities will be applied with a limit of 80 € included.\n\nFor the first few days, we provide the following basic amenities: small containers of shower gel, shampoo and soap, toilet paper, kitchen roll, sponge, dishwashing detergent, and garbage bag.\n\nExtra keys: 20€ (extra set when possible, lost keys, or door opening service).\nExtra cleaning: price of a cleaning fee.\nExtra linen: 30€ (towels and sheets for 2 people, i.e., when the sofa bed is not included).', 'A Casa do Gi', 'Simplicity, warmth and love', '<div class=\"space-y-4 text-charcoal/80 leading-relaxed\">\r\n    <p><strong>Welcome to Casa do Gi!</strong></p>\r\n    <p>This charming and cozy 100sqm house with 3 bedrooms in Mogadouro is perfect for families and groups of friends. Traditionally furnished and tastefully decorated, it features everything you need for a memorable stay and is just a few steps away from the town\'s main attractions (like the Trindade Coelho Statue and the famous São Cristóvão Swing), as well as excellent restaurants and local shops.</p>\r\n    \r\n    <p>The house is always professionally cleaned for your maximum comfort. Enjoy your stay!</p>\r\n</div>', '', '• Zero tolerance for smoking inside (fines start at €200).\r\n• Long stays (>30 nights) have an €80 cap on utilities included.\r\n• We provide basic amenities (body wash, shampoo, soap, etc.) for the first days.\r\n• Additional fees: €20 for extra keys/door opening; €30 for extra bed linen (sofa bed). Extra cleaning also available for a fee.', '', '', 'Full refund up to 5 days before arrival. Process managed through GuestReady.', 'Mogadouro & Surroundings', '<div class=\"space-y-4 text-charcoal/80 leading-relaxed\">\r\n    <p>Mogadouro, a charming town, offers a set of attractions that showcase its rich history and cultural heritage, providing a memorable experience.</p>\r\n    \r\n    <ul class=\"list-none space-y-3 mt-4\">\r\n        <li class=\"flex items-start gap-2\">\r\n            <span class=\"text-secondary mt-1\">✦</span>\r\n            <span><strong>Heritage and Culture:</strong> Visit the majestic Main Church and the Archeology Museum, which trace the town\'s origins. Don\'t miss the statue honoring the illustrious local writer Trindade Coelho.</span>\r\n        </li>\r\n        <li class=\"flex items-start gap-2\">\r\n            <span class=\"text-secondary mt-1\">✦</span>\r\n            <span><strong>Nature and Adventure:</strong> Enjoy the thrilling panoramic views at the São Cristóvão Swing and explore the Douro International Natural Park, an extensive sanctuary that is home to protected eagles and vultures.</span>\r\n        </li>\r\n    </ul>\r\n</div>'),
(3, 2, 1, '', NULL, NULL, NULL, 'A Casa do Gi 2', 'Simplicidade, acolhimento e muito amor', '<div class=\"space-y-4 text-charcoal/80 leading-relaxed\">\r\n    <p><strong>Bem-vindo à Casa do Gi!</strong></p>\r\n    <p>Esta encantadora e acolhedora casa de 100m² com 3 quartos em Mogadouro é perfeita para famílias e grupos de amigos. Tradicionalmente mobilada e decorada com muito bom gosto, dispõe de tudo o que necessita para uma estadia memorável e fica a escassos passos das principais atrações da vila (como a Estátua de Trindade Coelho e o famoso Baloiço de São Cristóvão), bem como de excelentes restaurantes e comércio local.</p>\r\n    \r\n    <p>A casa é sempre limpa profissionalmente para seu máximo conforto. Desfrute da sua estadia!</p>\r\n</div>', '', '• Tolerância zero ao fumo no interior (multa a partir de 200€).\r\n• Estadias longas (>30 noites) têm limite de 80€ incluído para utilitários.\r\n• Incluímos comodidades básicas (gel de banho, champô, sabonete, etc.) para os primeiros dias.\r\n• Taxas adicionais: 20€ para chaves extra/abertura de porta; 30€ para roupa de cama extra (sofá-cama). Limpeza extra também disponível sob taxa.', '', '', 'Reembolso total até 5 dias antes da chegada. Processo gerido através da GuestReady.', 'Mogadouro & Envolvência', '<div class=\"space-y-4 text-charcoal/80 leading-relaxed\">\r\n    <p>Mogadouro, uma vila encantadora, oferece um conjunto de atrações que mostram a sua rica história e património cultural, proporcionando uma experiência memorável.</p>\r\n    \r\n    <ul class=\"list-none space-y-3 mt-4\">\r\n        <li class=\"flex items-start gap-2\">\r\n            <span class=\"text-secondary mt-1\">✦</span>\r\n            <span><strong>Património e Cultura:</strong> Visite a majestosa Igreja Matriz e o Museu de Arqueologia, que traçam as origens da vila. Não perca a estátua em homenagem ao ilustre escritor transmontano Trindade Coelho.</span>\r\n        </li>\r\n        <li class=\"flex items-start gap-2\">\r\n            <span class=\"text-secondary mt-1\">✦</span>\r\n            <span><strong>Natureza e Aventura:</strong> Desfrute das vistas panorâmicas emocionantes no Baloiço de São Cristóvão e explore o Parque Natural do Douro Internacional, um extenso santuário que alberga águias e abutres protegidos.</span>\r\n        </li>\r\n    </ul>\r\n</div>'),
(4, 2, 2, '', NULL, NULL, NULL, 'A Casa do Gi', 'Simplicity, warmth and love', '<div class=\"space-y-4 text-charcoal/80 leading-relaxed\">\r\n    <p><strong>Welcome to Casa do Gi!</strong></p>\r\n    <p>This charming and cozy 100sqm house with 3 bedrooms in Mogadouro is perfect for families and groups of friends. Traditionally furnished and tastefully decorated, it features everything you need for a memorable stay and is just a few steps away from the town\'s main attractions (like the Trindade Coelho Statue and the famous São Cristóvão Swing), as well as excellent restaurants and local shops.</p>\r\n    \r\n    <p>The house is always professionally cleaned for your maximum comfort. Enjoy your stay!</p>\r\n</div>', '', '• Zero tolerance for smoking inside (fines start at €200).\r\n• Long stays (>30 nights) have an €80 cap on utilities included.\r\n• We provide basic amenities (body wash, shampoo, soap, etc.) for the first days.\r\n• Additional fees: €20 for extra keys/door opening; €30 for extra bed linen (sofa bed). Extra cleaning also available for a fee.', '', '', 'Full refund up to 5 days before arrival. Process managed through GuestReady.', 'Mogadouro & Surroundings', '<div class=\"space-y-4 text-charcoal/80 leading-relaxed\">\r\n    <p>Mogadouro, a charming town, offers a set of attractions that showcase its rich history and cultural heritage, providing a memorable experience.</p>\r\n    \r\n    <ul class=\"list-none space-y-3 mt-4\">\r\n        <li class=\"flex items-start gap-2\">\r\n            <span class=\"text-secondary mt-1\">✦</span>\r\n            <span><strong>Heritage and Culture:</strong> Visit the majestic Main Church and the Archeology Museum, which trace the town\'s origins. Don\'t miss the statue honoring the illustrious local writer Trindade Coelho.</span>\r\n        </li>\r\n        <li class=\"flex items-start gap-2\">\r\n            <span class=\"text-secondary mt-1\">✦</span>\r\n            <span><strong>Nature and Adventure:</strong> Enjoy the thrilling panoramic views at the São Cristóvão Swing and explore the Douro International Natural Park, an extensive sanctuary that is home to protected eagles and vultures.</span>\r\n        </li>\r\n    </ul>\r\n</div>');

-- --------------------------------------------------------

--
-- Estrutura da tabela `activity_links`
--

CREATE TABLE `activity_links` (
  `id` int(11) NOT NULL,
  `section` varchar(50) NOT NULL DEFAULT 'official',
  `tag_pt` varchar(100) DEFAULT NULL,
  `tag_en` varchar(100) DEFAULT NULL,
  `title_pt` varchar(255) NOT NULL,
  `title_en` varchar(255) DEFAULT NULL,
  `desc_pt` text DEFAULT NULL,
  `desc_en` text DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `activity_links`
--

INSERT INTO `activity_links` (`id`, `section`, `tag_pt`, `tag_en`, `title_pt`, `title_en`, `desc_pt`, `desc_en`, `url`, `sort_order`, `is_active`) VALUES
(1, 'official', 'SITE OFICIAL', 'OFFICIAL SITE', 'Câmara Municipal de Mogadouro', 'Mogadouro City Hall', 'Informação oficial do concelho: o que visitar, património, eventos e contactos.', 'Official county info: what to visit, heritage, events and contacts.', 'https://www.mogadouro.pt/', 1, 1),
(2, 'official', 'TURISMO', 'TOURISM', 'Posto de Turismo de Mogadouro', 'Mogadouro Tourism Office', 'Loja Interativa de Turismo - pontos de interesse, percursos e apoio ao visitante.', 'Interactive Tourism Shop - points of interest, routes and visitor support.', 'https://www.mogadouro.pt/pages/17', 2, 1),
(3, 'guide', '', '', 'Roteiro por Mogadouro - Vagamundos', 'Mogadouro Guide - Vagamundos', '', '', 'https://www.vagamundos.pt/visitar-mogadouro-roteiro/', 3, 1),
(4, 'guide', '', '', 'Atrações em torno de Mogadouro - Komoot', 'Attractions around Mogadouro - Komoot', '', '', 'https://www.komoot.com/pt-pt/guide/900754/atracoes-em-torno-de-mogadouro', 4, 1),
(5, 'guide', '', '', 'Mogadouro - Tripadvisor', 'Mogadouro - Tripadvisor', '', '', 'https://www.tripadvisor.pt/Attractions-g1458520-Activities-Mogadouro_Braganca_District_Northern_Portugal.html', 5, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('super_admin','admin','editor') DEFAULT 'editor',
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `login_attempts` int(10) UNSIGNED DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `password_reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password_hash`, `full_name`, `role`, `avatar`, `is_active`, `last_login`, `login_attempts`, `locked_until`, `password_reset_token`, `password_reset_expires`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@acasadogi.pt', '$2y$12$h1hHCeq1svPUpfFc82tKYuHPtR3j8bReNo1nSeCcR7ZK3M3YKld.G', 'Administrador', 'super_admin', NULL, 1, '2026-08-31 00:22:19', 0, NULL, NULL, NULL, '2026-01-19 12:51:19', '2026-08-30 23:22:19');

-- --------------------------------------------------------

--
-- Estrutura da tabela `amenities`
--

CREATE TABLE `amenities` (
  `id` int(10) UNSIGNED NOT NULL,
  `icon` varchar(50) NOT NULL,
  `category` enum('general','kitchen','bedroom','bathroom','outdoor','entertainment','safety','children','sports','services') DEFAULT 'general',
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `amenities`
--

INSERT INTO `amenities` (`id`, `icon`, `category`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'wifi', 'general', 1, 1, '2026-01-19 12:51:19'),
(2, 'ac', 'general', 2, 1, '2026-01-19 12:51:19'),
(3, 'heater', 'general', 3, 1, '2026-01-19 12:51:19'),
(4, 'parking', 'general', 4, 1, '2026-01-19 12:51:19'),
(5, 'pool-private', 'outdoor', 5, 1, '2026-01-19 12:51:19'),
(6, 'pool-shared', 'outdoor', 6, 1, '2026-01-19 12:51:19'),
(7, 'garden', 'outdoor', 7, 1, '2026-01-19 12:51:19'),
(8, 'terrace', 'outdoor', 8, 1, '2026-01-19 12:51:19'),
(9, 'washing-machine', 'general', 9, 1, '2026-01-19 12:51:19'),
(10, 'dishwasher', 'kitchen', 10, 1, '2026-01-19 12:51:19'),
(11, 'hairdryer', 'bathroom', 11, 1, '2026-01-19 12:51:19'),
(12, 'workspace', 'general', 12, 1, '2026-01-19 12:51:19'),
(13, 'oven', 'kitchen', 20, 1, '2026-01-26 22:31:24'),
(14, 'microwave', 'kitchen', 21, 1, '2026-01-26 22:31:24'),
(15, 'fridge', 'kitchen', 22, 1, '2026-01-26 22:31:24'),
(16, 'coffee-maker', 'kitchen', 23, 1, '2026-01-26 22:31:24'),
(17, 'toaster', 'kitchen', 24, 1, '2026-01-26 22:31:24'),
(18, 'kettle', 'kitchen', 25, 1, '2026-01-26 22:31:24'),
(19, 'cookware', 'kitchen', 26, 1, '2026-01-26 22:31:24'),
(20, 'bed-linens', 'bedroom', 30, 1, '2026-01-26 22:31:24'),
(21, 'extra-pillows', 'bedroom', 31, 1, '2026-01-26 22:31:24'),
(22, 'blackout-curtains', 'bedroom', 32, 1, '2026-01-26 22:31:24'),
(23, 'hangers', 'bedroom', 33, 1, '2026-01-26 22:31:24'),
(24, 'hot-water', 'bathroom', 40, 1, '2026-01-26 22:31:24'),
(25, 'towels', 'bathroom', 41, 1, '2026-01-26 22:31:24'),
(26, 'toiletries', 'bathroom', 42, 1, '2026-01-26 22:31:24'),
(27, 'smoke-detector', 'safety', 50, 1, '2026-01-26 22:31:24'),
(28, 'fire-extinguisher', 'safety', 51, 1, '2026-01-26 22:31:24'),
(29, 'first-aid', 'safety', 52, 1, '2026-01-26 22:31:24'),
(30, 'carbon-monoxide', 'safety', 53, 1, '2026-01-26 22:31:24'),
(31, 'high-chair', 'children', 60, 1, '2026-01-26 22:31:24'),
(32, 'crib', 'children', 61, 1, '2026-01-26 22:31:24'),
(33, 'baby-bath', 'children', 62, 1, '2026-01-26 22:31:24'),
(34, 'child-safety', 'children', 63, 1, '2026-01-26 22:31:24'),
(35, 'smart-tv', 'entertainment', 70, 1, '2026-01-26 22:31:24'),
(36, 'streaming', 'entertainment', 71, 1, '2026-01-26 22:31:24'),
(37, 'books', 'entertainment', 72, 1, '2026-01-26 22:31:24'),
(38, 'board-games', 'entertainment', 73, 1, '2026-01-26 22:31:24'),
(39, 'cleaning', 'services', 80, 1, '2026-01-26 22:31:24'),
(40, 'luggage-storage', 'services', 81, 1, '2026-01-26 22:31:24');

-- --------------------------------------------------------

--
-- Estrutura da tabela `amenity_translations`
--

CREATE TABLE `amenity_translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `amenity_id` int(10) UNSIGNED NOT NULL,
  `language_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `amenity_translations`
--

INSERT INTO `amenity_translations` (`id`, `amenity_id`, `language_id`, `name`) VALUES
(1, 1, 1, 'Internet Wifi'),
(2, 1, 2, 'Wifi Internet'),
(3, 2, 1, 'Ar condicionado'),
(4, 2, 2, 'Air conditioning'),
(5, 3, 1, 'Aquecedores'),
(6, 3, 2, 'Heaters'),
(7, 4, 1, 'Estacionamento incluido'),
(8, 4, 2, 'Parking included'),
(9, 5, 1, 'Piscina privada'),
(10, 5, 2, 'Private pool'),
(11, 6, 1, 'Piscina partilhada'),
(12, 6, 2, 'Shared pool'),
(13, 7, 1, 'Jardim'),
(14, 7, 2, 'Garden'),
(15, 8, 1, 'Terraco'),
(16, 8, 2, 'Terrace'),
(17, 9, 1, 'Maquina de lavar'),
(18, 9, 2, 'Washing machine'),
(19, 10, 1, 'Lava-louca'),
(20, 10, 2, 'Dishwasher'),
(21, 11, 1, 'Secador de cabelo'),
(22, 11, 2, 'Hair dryer'),
(23, 12, 1, 'Area de trabalho para portatil'),
(24, 12, 2, 'Laptop workspace'),
(25, 13, 1, 'Forno'),
(26, 14, 1, 'Micro-ondas'),
(27, 15, 1, 'Frigorífico'),
(28, 16, 1, 'Máquina de café'),
(29, 17, 1, 'Torradeira'),
(30, 18, 1, 'Chaleira'),
(31, 19, 1, 'Utensílios de cozinha'),
(32, 13, 2, 'Oven'),
(33, 14, 2, 'Microwave'),
(34, 15, 2, 'Refrigerator'),
(35, 16, 2, 'Coffee maker'),
(36, 17, 2, 'Toaster'),
(37, 18, 2, 'Electric kettle'),
(38, 19, 2, 'Cookware'),
(39, 20, 1, 'Roupa de cama'),
(40, 21, 1, 'Almofadas extra'),
(41, 22, 1, 'Cortinas blackout'),
(42, 23, 1, 'Cabides'),
(46, 20, 2, 'Bed linens'),
(47, 21, 2, 'Extra pillows'),
(48, 22, 2, 'Blackout curtains'),
(49, 23, 2, 'Hangers'),
(53, 24, 1, 'Água quente'),
(54, 25, 1, 'Toalhas'),
(55, 26, 1, 'Artigos de higiene'),
(56, 24, 2, 'Hot water'),
(57, 25, 2, 'Towels'),
(58, 26, 2, 'Toiletries'),
(59, 27, 1, 'Detetor de fumo'),
(60, 28, 1, 'Extintor'),
(61, 29, 1, 'Kit primeiros socorros'),
(62, 30, 1, 'Detetor de monóxido'),
(66, 27, 2, 'Smoke detector'),
(67, 28, 2, 'Fire extinguisher'),
(68, 29, 2, 'First aid kit'),
(69, 30, 2, 'Carbon monoxide detector'),
(73, 31, 1, 'Cadeira alta'),
(74, 32, 1, 'Berço'),
(75, 33, 1, 'Banheira bebé'),
(76, 34, 1, 'Proteções para crianças'),
(80, 31, 2, 'High chair'),
(81, 32, 2, 'Crib'),
(82, 33, 2, 'Baby bath'),
(83, 34, 2, 'Child safety gates'),
(87, 35, 1, 'Smart TV'),
(88, 36, 1, 'Streaming (Netflix)'),
(89, 37, 1, 'Livros'),
(90, 38, 1, 'Jogos de tabuleiro'),
(94, 35, 2, 'Smart TV'),
(95, 36, 2, 'Streaming (Netflix)'),
(96, 37, 2, 'Books'),
(97, 38, 2, 'Board games'),
(101, 39, 1, 'Limpeza incluída'),
(102, 40, 1, 'Guarda bagagem'),
(104, 39, 2, 'Cleaning included'),
(105, 40, 2, 'Luggage storage');

-- --------------------------------------------------------

--
-- Estrutura da tabela `bathrooms`
--

CREATE TABLE `bathrooms` (
  `id` int(10) UNSIGNED NOT NULL,
  `accommodation_id` int(10) UNSIGNED NOT NULL,
  `bathroom_number` int(10) UNSIGNED NOT NULL,
  `is_ensuite` tinyint(1) DEFAULT 0 COMMENT 'Is this an ensuite bathroom',
  `has_shower` tinyint(1) DEFAULT 1,
  `has_bathtub` tinyint(1) DEFAULT 0,
  `has_bidet` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL COMMENT 'Bathroom photo path'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `bathrooms`
--

INSERT INTO `bathrooms` (`id`, `accommodation_id`, `bathroom_number`, `is_ensuite`, `has_shower`, `has_bathtub`, `has_bidet`, `created_at`, `image`) VALUES
(11, 2, 1, 0, 1, 0, 0, '2026-02-10 18:23:27', NULL),
(12, 2, 2, 0, 1, 0, 0, '2026-02-10 18:23:27', NULL),
(13, 1, 1, 0, 1, 0, 0, '2026-08-28 23:52:43', NULL),
(14, 1, 2, 0, 1, 0, 0, '2026-08-28 23:52:43', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `bathroom_translations`
--

CREATE TABLE `bathroom_translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `bathroom_id` int(10) UNSIGNED NOT NULL,
  `language_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL COMMENT 'Bathroom name',
  `description` varchar(255) NOT NULL COMMENT 'Bathroom description',
  `title` varchar(50) DEFAULT NULL COMMENT 'Section title like "Higiene"'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `bathroom_translations`
--

INSERT INTO `bathroom_translations` (`id`, `bathroom_id`, `language_id`, `name`, `description`, `title`) VALUES
(21, 11, 1, 'Casa de Banho Nº 1', 'Sanita, Chuveiro', NULL),
(22, 11, 2, 'Bathroom Nº 1', 'Toilet, Shower', NULL),
(23, 12, 1, 'Casa de Banho Nº 2', 'Sanita, Chuveiro', NULL),
(24, 12, 2, 'Bathroom Nº 2', 'Toilet, Shower', NULL),
(25, 13, 1, 'Casa de Banho Nº 1', 'Sanita, Chuveiro', 'Casa de Banho 1'),
(26, 13, 2, 'Bathroom Nº 1', 'Toilet, Shower', 'Bathroom 1'),
(27, 14, 1, 'Casa de Banho Nº 2', 'Sanita, Chuveiro', 'Casa de Banho 2'),
(28, 14, 2, 'Bathroom Nº 2', 'Toilet, Shower', 'Bathroom 2');

-- --------------------------------------------------------

--
-- Estrutura da tabela `bedrooms`
--

CREATE TABLE `bedrooms` (
  `id` int(10) UNSIGNED NOT NULL,
  `accommodation_id` int(10) UNSIGNED NOT NULL,
  `bedroom_number` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL COMMENT 'Bedroom photo path'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `bedrooms`
--

INSERT INTO `bedrooms` (`id`, `accommodation_id`, `bedroom_number`, `created_at`, `image`) VALUES
(16, 2, 1, '2026-02-10 18:23:27', NULL),
(17, 2, 2, '2026-02-10 18:23:27', NULL),
(18, 2, 3, '2026-02-10 18:23:27', NULL),
(19, 1, 1, '2026-08-28 23:49:17', NULL),
(20, 1, 2, '2026-08-28 23:49:17', NULL),
(21, 1, 3, '2026-08-28 23:49:17', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `bedroom_translations`
--

CREATE TABLE `bedroom_translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `bedroom_id` int(10) UNSIGNED NOT NULL,
  `language_id` int(10) UNSIGNED NOT NULL,
  `beds_description` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL COMMENT 'Bedroom name (e.g., Master Suite)',
  `title` varchar(50) DEFAULT NULL COMMENT 'Section title like "Dormidas"'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `bedroom_translations`
--

INSERT INTO `bedroom_translations` (`id`, `bedroom_id`, `language_id`, `beds_description`, `name`, `title`) VALUES
(31, 16, 1, 'Cama Queen', 'Quarto Nº 1', NULL),
(32, 16, 2, 'Queen Size Bed', 'Bedroom Nº 1', NULL),
(33, 17, 1, '2 Camas de Casal', 'Quarto Nº 2', NULL),
(34, 17, 2, '2 Double Beds', 'Bedroom Nº 2', NULL),
(35, 18, 1, 'Cama de Casal', 'Quarto Nº 3', NULL),
(36, 18, 2, 'Double Bed', 'Bedroom Nº 3', NULL),
(37, 19, 1, 'Sofá-Cama de Solteiro, Cama de Casal', 'Quarto Nº 1', 'Quarto 1'),
(38, 19, 2, 'Single Sofa Bed, Double Bed', 'Bedroom Nº 1', 'Bedroom 1'),
(39, 20, 1, '2 Camas de Solteiro', 'Quarto Nº 2', 'Quarto 2'),
(40, 20, 2, '2 Single Beds', 'Bedroom Nº 2', 'Bedroom 2'),
(41, 21, 1, 'Cama de Casal', 'Quarto Nº 3', 'Quarto 3'),
(42, 21, 2, 'Double Bed', 'Bedroom Nº 3', 'Bedroom 3');

-- --------------------------------------------------------

--
-- Estrutura da tabela `contact_submissions`
--

CREATE TABLE `contact_submissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `language` varchar(2) DEFAULT 'pt',
  `is_read` tinyint(1) DEFAULT 0,
  `is_spam` tinyint(1) DEFAULT 0,
  `is_ignored` tinyint(1) DEFAULT 0 COMMENT 'Ignored messages',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `contact_submissions`
--

INSERT INTO `contact_submissions` (`id`, `name`, `email`, `phone`, `subject`, `message`, `ip_address`, `user_agent`, `language`, `is_read`, `is_spam`, `is_ignored`, `created_at`) VALUES
(1, 'Test User', 'test@example.com', NULL, 'Test Subject', 'Test Message', '::1', '', 'pt', 0, 0, 0, '2026-08-27 11:45:21'),
(2, 'Josefino', 'teste@teset.com', '999888111', 'Algo super bom', 'Oportunidade de ir ao continente.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'pt', 0, 0, 0, '2026-08-27 11:49:58'),
(3, 'Medonca', 'ola@olagelados.com', '888999888', 'Gelados', 'Nem sei o que dizer mais.', '0:0:2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'pt', 0, 0, 0, '2026-08-27 12:12:24');

-- --------------------------------------------------------

--
-- Estrutura da tabela `content_blocks`
--

CREATE TABLE `content_blocks` (
  `id` int(10) UNSIGNED NOT NULL,
  `block_key` varchar(100) NOT NULL,
  `language_id` int(10) UNSIGNED NOT NULL,
  `content_type` enum('text','textarea','html','json') DEFAULT 'text',
  `content` text DEFAULT NULL,
  `page` varchar(50) DEFAULT NULL,
  `section` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `content_blocks`
--

INSERT INTO `content_blocks` (`id`, `block_key`, `language_id`, `content_type`, `content`, `page`, `section`, `created_at`, `updated_at`) VALUES
(6, 'accommodation_title', 1, 'text', 'O Alojamento', 'accommodation', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(7, 'accommodation_intro', 1, 'textarea', 'Ambas as casas oferecem o mesmo conforto e hospitalidade transmontana. Escolha a que melhor se adapta à sua estadia.', 'accommodation', 'main', '2026-01-19 12:51:19', '2026-08-23 22:50:25'),
(8, 'shop_title', 1, 'text', 'Produtos Regionais', 'shop', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(9, 'shop_intro', 1, 'textarea', 'Sabores autênticos de Trás-os-Montes, selecionados com carinho para a sua mesa.', 'shop', 'main', '2026-01-19 12:51:19', '2026-08-14 17:12:28'),
(10, 'activities_title', 1, 'text', 'O Que Fazer', 'activities', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(11, 'activities_intro', 1, 'textarea', 'Descubra as maravilhas de Mogadouro e arredores', 'activities', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(12, 'contact_title', 1, 'text', 'Contacte-nos', 'contact', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(13, 'contact_intro', 1, 'textarea', 'Tem alguma questão? Entre em contacto connosco', 'contact', 'main', '2026-01-19 12:51:19', '2026-08-14 17:12:28'),
(19, 'accommodation_title', 2, 'text', 'The Accommodation', 'accommodation', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(20, 'accommodation_intro', 2, 'textarea', 'Both houses offer the same comfort and Transmontana hospitality. Choose the one that best suits your stay.', 'accommodation', 'main', '2026-01-19 12:51:19', '2026-08-23 22:50:25'),
(21, 'shop_title', 2, 'text', 'Regional Products', 'shop', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(22, 'shop_intro', 2, 'textarea', 'Authentic flavors from Tras-os-Montes, selected with care for your table.', 'shop', 'main', '2026-01-19 12:51:19', '2026-02-09 19:52:45'),
(23, 'activities_title', 2, 'text', 'Things To Do', 'activities', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(24, 'activities_intro', 2, 'textarea', 'Discover the wonders of Mogadouro and surroundings', 'activities', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(25, 'contact_title', 2, 'text', 'Contact Us', 'contact', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(26, 'contact_intro', 2, 'textarea', 'Do you have any questions? Get in touch with us', 'contact', 'main', '2026-01-19 12:51:19', '2026-02-09 19:27:19'),
(27, 'home_hero_subtitle', 1, 'text', 'Onde a tradição transmontana encontra o conforto moderno', 'home', 'hero', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(28, 'home_hero_subtitle', 2, 'text', 'Where Transmontana tradition meets modern comfort', 'home', 'hero', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(29, 'home_split_left_label', 1, 'text', 'Bem-vindo ao', 'home', 'split_hero', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(30, 'home_split_left_label', 2, 'text', 'Welcome to the', 'home', 'split_hero', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(31, 'home_split_left_title', 1, 'text', 'Refúgio', 'home', 'split_hero', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(32, 'home_split_left_title', 2, 'text', 'Refuge', 'home', 'split_hero', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(33, 'home_split_right_label', 1, 'text', 'Descubra a', 'home', 'split_hero', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(34, 'home_split_right_label', 2, 'text', 'Discover the', 'home', 'split_hero', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(35, 'home_split_right_title', 1, 'text', 'Tradição', 'home', 'split_hero', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(36, 'home_split_right_title', 2, 'text', 'Tradition', 'home', 'split_hero', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(37, 'home_explore_title', 1, 'text', 'Explore o Nosso Mundo', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(38, 'home_explore_title', 2, 'text', 'Explore Our World', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(41, 'home_card1_title', 1, 'text', 'Alojamento', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(42, 'home_card1_title', 2, 'text', 'Accommodation', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(43, 'home_card1_text', 1, 'text', 'Sinta o conforto das nossas casas rústicas.', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(44, 'home_card1_text', 2, 'text', 'Experience the comfort of our rustic houses.', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(45, 'home_card1_cta', 1, 'text', 'Ver Casas', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(46, 'home_card1_cta', 2, 'text', 'View Rooms', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(49, 'home_card2_title', 1, 'text', 'Atividades', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(50, 'home_card2_title', 2, 'text', 'Activities', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(51, 'home_card2_text', 1, 'text', 'Descubra a natureza e história de Mogadouro.', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(52, 'home_card2_text', 2, 'text', 'Discover the nature and history of Mogadouro.', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(53, 'home_card2_cta', 1, 'text', 'Explorar', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(54, 'home_card2_cta', 2, 'text', 'Explore', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(57, 'home_card3_title', 1, 'text', 'Loja Regional', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(58, 'home_card3_title', 2, 'text', 'Regional Shop', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(59, 'home_card3_text', 1, 'text', 'Sabores autênticos de Trás-os-Montes.', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(60, 'home_card3_text', 2, 'text', 'Authentic flavors from Tras-os-Montes.', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(61, 'home_card3_cta', 1, 'text', 'Comprar', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(62, 'home_card3_cta', 2, 'text', 'Shop Now', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(65, 'home_card4_title', 1, 'text', 'Contactos', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(66, 'home_card4_title', 2, 'text', 'Contact Us', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(67, 'home_card4_text', 1, 'text', 'Entre em contacto connosco e planeie a sua visita.', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(68, 'home_card4_text', 2, 'text', 'Get in touch and plan your visit.', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(69, 'home_card4_cta', 1, 'text', 'Contactar', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(70, 'home_card4_cta', 2, 'text', 'Get in Touch', 'home', 'explore', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(71, 'home_about_label', 1, 'text', 'A Nossa História', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(72, 'home_about_label', 2, 'text', 'Our Story', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(73, 'home_about_title', 1, 'html', 'Mais do que uma casa, um <span class=\"italic text-accent\">Legado</span>.', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(74, 'home_about_title', 2, 'html', 'More than a house,<br>a <span class=\"italic text-accent\">legacy</span>.', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(75, 'home_about_text1', 1, 'textarea', 'A Casa do Gi nasceu da vontade de preservar as raízes transmontanas. O que outrora foi uma casa de família, é hoje um refúgio para quem procura a autenticidade do campo.', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(76, 'home_about_text1', 2, 'textarea', 'A Casa do Gi was born from the will to preserve the roots of Tras-os-Montes. What was once a family home is now a refuge for those seeking the authenticity of the countryside.', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(77, 'home_about_text2', 1, 'textarea', 'Aqui, o tempo abranda. Convidamo-lo a descobrir as tradições, os sabores e as gentes que fazem de Mogadouro um lugar único no mundo.', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(78, 'home_about_text2', 2, 'textarea', 'Here, time slows down. We invite you to discover the traditions, the flavors, and the people that make Mogadouro a unique place in the world.', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(79, 'home_about_cta', 1, 'text', 'Ler História Completa', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(80, 'home_about_cta', 2, 'text', 'Read Full Story', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-08-14 20:52:51'),
(81, 'about_hero_label', 1, 'text', 'A Nossa Hist├│ria', 'about', 'hero', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(82, 'about_hero_label', 2, 'text', 'Our Story', 'about', 'hero', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(83, 'about_hero_subtitle', 1, 'textarea', 'Simplicidade, acolhimento, momentos de convívio marcantes, calor da família, alegria, diversão, gargalhadas e muito amor!', 'about', 'hero', '2026-02-09 18:34:33', '2026-08-14 17:12:28'),
(84, 'about_hero_subtitle', 2, 'textarea', 'Simplicity, warmth, remarkable moments of conviviality, family warmth, joy, fun, laughter and lots of love!', 'about', 'hero', '2026-02-09 18:34:33', '2026-02-09 20:08:48'),
(85, 'about_origin_label', 1, 'text', 'A Nossa Origem', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(86, 'about_origin_label', 2, 'text', 'Our Origins', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(87, 'about_origin_title', 1, 'html', 'Uma casa construída com <span class=\"italic text-secondary\">amor</span> e <span class=\"italic text-secondary\">saudade</span>.', 'about', 'origin', '2026-02-09 18:34:33', '2026-08-14 17:51:14'),
(88, 'about_origin_title', 2, 'html', 'A house built with <span class=\"italic text-secondary\">love</span> and <span class=\"italic text-secondary\">longing</span>.', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(89, 'about_origin_text1', 1, 'textarea', 'Erguida nos anos 80, a <strong>Casa do Gi</strong> conta a história intemporal de quem partiu para longe mas nunca esqueceu as suas raízes. Construída tijolo a tijolo, representa o sonho concretizado de regressar a casa.', 'about', 'origin', '2026-02-09 18:34:33', '2026-08-14 17:51:14'),
(90, 'about_origin_text1', 2, 'textarea', 'Built in the 80s, <strong>Casa do Gi</strong> tells the timeless story of those who left for distant lands but never forgot their roots. Constructed brick by brick, it represents the fulfilled dream of returning home.', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(91, 'about_origin_text2', 1, 'textarea', 'O que começou como um projeto de vida familiar transformou-se num refúgio para quem procura a paz do interior. Aqui, o tempo abranda e os dias são medidos pela luz do sol e pelas conversas à beira da lareira.', 'about', 'origin', '2026-02-09 18:34:33', '2026-08-14 17:51:14'),
(92, 'about_origin_text2', 2, 'textarea', 'What began as a family life project transformed into a refuge for those seeking the peace of the countryside. Here, time slows down and days are measured by sunlight and conversations by the fireplace.', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(93, 'about_origin_caption', 1, 'text', '1980 • O Início', 'about', 'origin', '2026-02-09 18:34:33', '2026-08-14 17:51:14'),
(94, 'about_origin_caption', 2, 'text', '1980 ÔÇó The Beginning', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(95, 'about_origin_signature', 1, 'text', 'Família Gi', 'about', 'origin', '2026-02-09 18:34:33', '2026-08-14 17:51:14'),
(96, 'about_origin_signature', 2, 'text', 'Gi Family', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(97, 'about_values_label', 1, 'text', 'Valores', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(98, 'about_values_label', 2, 'text', 'Values', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(99, 'about_values_title', 1, 'html', 'A arte de bem receber,<br>à moda antiga.', 'about', 'values', '2026-02-09 18:34:33', '2026-08-14 17:51:14'),
(100, 'about_values_title', 2, 'html', 'The art of welcoming,<br>the old-fashioned way.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(101, 'about_values_intro', 1, 'textarea', 'Não somos um hotel. Somos uma casa de família que decidiu abrir as portas ao mundo. Aqui, a hospitalidade não é um serviço, é a nossa natureza.', 'about', 'values', '2026-02-09 18:34:33', '2026-08-14 17:51:14'),
(102, 'about_values_intro', 2, 'textarea', 'We are not a hotel. We are a family home that decided to open its doors to the world. Here, hospitality is not a service, it\'s our nature.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(103, 'about_value1_title', 1, 'text', 'Acolhimento Genuíno', 'about', 'values', '2026-02-09 18:34:33', '2026-08-14 17:51:14'),
(104, 'about_value1_title', 2, 'text', 'Genuine Hospitality', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(105, 'about_value1_text', 1, 'textarea', 'Recebemos cada hóspede como um velho amigo. Sem formalismos rígidos, com o calor de um abraço e a sinceridade de um sorriso transmontano.', 'about', 'values', '2026-02-09 18:34:33', '2026-08-14 17:51:14'),
(106, 'about_value1_text', 2, 'textarea', 'We welcome each guest as an old friend. Without rigid formalities, with the warmth of a hug and the sincerity of a Transmontano smile.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(107, 'about_value2_title', 1, 'text', 'Paz Absoluta', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(108, 'about_value2_title', 2, 'text', 'Absolute Peace', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(109, 'about_value2_text', 1, 'textarea', 'O luxo do silêncio. Longe da confusão, onde o único ruído é o vento nas árvores e o cantar dos pássaros. O refúgio perfeito para recarregar energias.', 'about', 'values', '2026-02-09 18:34:33', '2026-08-14 17:51:14'),
(110, 'about_value2_text', 2, 'textarea', 'The luxury of silence. Far from the hustle, where the only sound is the wind in the trees and the singing of birds. The perfect refuge to recharge energies.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(111, 'about_value3_title', 1, 'text', 'Espírito de Partilha', 'about', 'values', '2026-02-09 18:34:33', '2026-08-14 17:51:14'),
(112, 'about_value3_title', 2, 'text', 'Spirit of Sharing', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(113, 'about_value3_text', 1, 'textarea', 'Acreditamos que as melhores memórias são construídas à mesa. Partilhamos histórias, sabores e experiências que ficam para sempre.', 'about', 'values', '2026-02-09 18:34:33', '2026-08-14 17:51:14'),
(114, 'about_value3_text', 2, 'textarea', 'We believe the best memories are built at the table. We share stories, flavors and experiences that last forever.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(115, 'about_value4_title', 1, 'text', 'Atenção ao Detalhe', 'about', 'values', '2026-02-09 18:34:33', '2026-08-14 17:51:14'),
(116, 'about_value4_title', 2, 'text', 'Attention to Detail', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(117, 'about_value4_text', 1, 'textarea', 'Nada é deixado ao acaso. Do pequeno-almoço caseiro à decoração cuidada, tudo é pensado para o seu conforto e bem-estar.', 'about', 'values', '2026-02-09 18:34:33', '2026-08-14 17:51:14'),
(118, 'about_value4_text', 2, 'textarea', 'Nothing is left to chance. From homemade breakfast to thoughtful decoration, everything is designed for your comfort and wellbeing.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(119, 'about_region_label', 1, 'text', 'O Nosso Berço', 'about', 'region', '2026-02-09 18:34:33', '2026-08-14 17:51:14'),
(120, 'about_region_label', 2, 'text', 'Our Home', 'about', 'region', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(121, 'about_region_text', 1, 'textarea', 'Onde o tempo pára e a alma respira. Uma terra de horizontes infinitos, guardiã de tradições milenares e de uma beleza natural bruta e intocada.', 'about', 'region', '2026-02-09 18:34:33', '2026-08-14 17:51:14'),
(122, 'about_region_text', 2, 'textarea', 'Where time stops and the soul breathes. A land of infinite horizons, guardian of ancient traditions and raw, untouched natural beauty.', 'about', 'region', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(123, 'about_region_cta1', 1, 'text', 'Planear Visita', 'about', 'region', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(124, 'about_region_cta1', 2, 'text', 'Plan Visit', 'about', 'region', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(125, 'about_region_cta2', 1, 'text', 'O que fazer', 'about', 'region', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(126, 'about_region_cta2', 2, 'text', 'Things to do', 'about', 'region', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(333, 'shop_empty_message', 1, 'text', 'Esta categoria ainda não tem produtos disponíveis.', 'shop', 'main', '2026-02-09 19:27:19', '2026-08-14 17:12:28'),
(334, 'shop_empty_message', 2, 'text', 'This category does not have products available yet.', 'shop', 'main', '2026-02-09 19:27:19', '2026-02-09 19:52:45'),
(335, 'contact_success_message', 1, 'textarea', 'Obrigado pelo seu contacto. Iremos responder o mais brevemente possível.', 'contact', 'main', '2026-02-09 19:27:19', '2026-08-14 17:12:28'),
(336, 'contact_success_message', 2, 'textarea', 'Thank you for your contact. We will reply as soon as possible.', 'contact', 'main', '2026-02-09 19:27:19', '2026-02-09 19:47:40'),
(343, 'footer_tagline', 1, 'text', 'Simplicidade, acolhimento e muito amor em Mogadouro', 'footer', 'main', '2026-02-09 19:47:40', '2026-02-09 19:50:59'),
(344, 'footer_tagline', 2, 'text', 'Simplicity, warmth and love in Mogadouro', 'footer', 'main', '2026-02-09 19:47:40', '2026-02-09 19:50:59'),
(361, 'accommodation_hero_tagline', 1, 'text', 'Alojamento Local', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(362, 'accommodation_hero_tagline', 2, 'text', 'Local Accommodation', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(363, 'accommodation_hero_title', 1, 'text', 'A Casa do Gi', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(364, 'accommodation_hero_title', 2, 'text', 'A Casa do Gi', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(365, 'accommodation_hero_subtitle', 1, 'text', 'Acolhimento transmontano, momentos em família e memórias para sempre.', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(366, 'accommodation_hero_subtitle', 2, 'text', 'Transmontano hospitality, family moments and memories forever.', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(367, 'accommodation_section_subtitle', 1, 'text', 'Duas Casas, Uma Experiência', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(368, 'accommodation_section_subtitle', 2, 'text', 'Two Houses, One Experience', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(369, 'accommodation_section_title', 1, 'text', 'Escolha o Seu Refúgio', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(370, 'accommodation_section_title', 2, 'text', 'Choose Your Refuge', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(371, 'accommodation_features_title', 1, 'text', 'O Que Ambas as Casas Oferecem', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(372, 'accommodation_features_title', 2, 'text', 'What Both Houses Offer', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(373, 'accommodation_feature_1', 1, 'text', 'Wi-Fi Grátis', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(374, 'accommodation_feature_1', 2, 'text', 'Free Wi-Fi', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(375, 'accommodation_feature_2', 1, 'text', 'Check-in Autónomo', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(376, 'accommodation_feature_2', 2, 'text', 'Self Check-in', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(377, 'accommodation_feature_3', 1, 'text', 'Toalhas e lençóis', NULL, NULL, '2026-02-09 19:57:15', '2026-08-28 20:16:26'),
(378, 'accommodation_feature_3', 2, 'text', 'Towels and sheets', NULL, NULL, '2026-02-09 19:57:15', '2026-08-28 20:16:26'),
(379, 'accommodation_feature_4', 1, 'text', 'Localização Central', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(380, 'accommodation_feature_4', 2, 'text', 'Central Location', NULL, NULL, '2026-02-09 19:57:15', '2026-08-23 22:50:25'),
(381, 'activities_hero_tagline', 1, 'text', 'Descubra Mogadouro', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:01:29'),
(382, 'activities_hero_tagline', 2, 'text', 'Discover Mogadouro', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:01:29'),
(383, 'activities_hero_title', 1, 'text', 'O Que Fazer', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:01:29'),
(384, 'activities_hero_title', 2, 'text', 'What to Do', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:01:29'),
(385, 'activities_hero_subtitle', 1, 'text', 'De paisagens deslumbrantes a sabores únicos, o nordeste transmontano tem muito para oferecer.', NULL, NULL, '2026-02-09 19:57:15', '2026-08-14 17:12:28'),
(386, 'activities_hero_subtitle', 2, 'text', 'From stunning landscapes to unique flavors, the northeast of Tras-os-Montes has much to offer.', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:01:29'),
(387, 'contact_hero_tagline', 1, 'text', 'Fale Connosco', NULL, NULL, '2026-02-09 20:08:12', '2026-02-09 20:08:12'),
(388, 'contact_hero_tagline', 2, 'text', 'Talk to Us', NULL, NULL, '2026-02-09 20:08:12', '2026-02-09 20:08:12'),
(389, 'contact_hero_title', 1, 'text', 'Contacte-nos', NULL, NULL, '2026-02-09 20:08:12', '2026-02-09 20:08:12'),
(390, 'contact_hero_title', 2, 'text', 'Contact Us', NULL, NULL, '2026-02-09 20:08:12', '2026-02-09 20:08:12'),
(391, 'contact_hero_subtitle', 1, 'text', 'Tem alguma questão? Entre em contacto connosco', NULL, NULL, '2026-02-09 20:08:12', '2026-08-14 17:12:28'),
(392, 'contact_hero_subtitle', 2, 'text', 'Have any questions? Get in touch with us', NULL, NULL, '2026-02-09 20:08:12', '2026-02-09 20:08:12'),
(399, 'about_hero_tagline', 1, 'text', 'A Nossa História', NULL, NULL, '2026-02-09 20:08:48', '2026-08-14 17:12:28'),
(400, 'about_hero_tagline', 2, 'text', 'Our Story', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(401, 'about_hero_title', 1, 'text', 'A Casa do Gi', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(402, 'about_hero_title', 2, 'text', 'A Casa do Gi', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(403, 'privacy_hero_tagline', 1, 'text', 'Informação Legal', NULL, NULL, '2026-02-09 20:08:48', '2026-08-15 00:04:18'),
(404, 'privacy_hero_tagline', 2, 'text', 'Legal Information', NULL, NULL, '2026-02-09 20:08:48', '2026-08-15 00:04:18'),
(405, 'privacy_hero_title', 1, 'text', 'Política de Privacidade', NULL, NULL, '2026-02-09 20:08:48', '2026-08-15 00:04:18'),
(406, 'privacy_hero_title', 2, 'text', 'Privacy Policy', NULL, NULL, '2026-02-09 20:08:48', '2026-08-15 00:04:18'),
(407, 'privacy_hero_subtitle', 1, 'textarea', 'A sua privacidade é importante para nós. Saiba como tratamos os seus dados.', NULL, NULL, '2026-02-09 20:08:48', '2026-08-15 00:04:18'),
(408, 'privacy_hero_subtitle', 2, 'textarea', 'Your privacy is important to us. Learn how we handle your data.', NULL, NULL, '2026-02-09 20:08:48', '2026-08-15 00:04:18'),
(409, 'privacy_date', 1, 'text', 'Atualizado em: 15 de Agosto de 2026', NULL, NULL, '2026-02-09 20:08:48', '2026-08-15 00:36:02'),
(410, 'privacy_date', 2, 'text', 'Updated on: August 15, 2026', NULL, NULL, '2026-02-09 20:08:48', '2026-08-15 00:36:02'),
(413, 'terms_hero_tagline', 1, 'text', 'Informação Legal', NULL, NULL, '2026-02-09 20:08:48', '2026-08-14 22:58:33'),
(414, 'terms_hero_tagline', 2, 'text', 'Legal Information', NULL, NULL, '2026-02-09 20:08:48', '2026-08-14 22:58:33'),
(415, 'terms_hero_title', 1, 'text', 'Termos e Condições', NULL, NULL, '2026-02-09 20:08:48', '2026-08-14 22:58:33'),
(416, 'terms_hero_title', 2, 'text', 'Terms and Conditions', NULL, NULL, '2026-02-09 20:08:48', '2026-08-14 22:58:33'),
(417, 'terms_hero_subtitle', 1, 'textarea', 'Por favor, leia atentamente os termos e condições de utilização do nosso serviço.', NULL, NULL, '2026-02-09 20:08:48', '2026-08-14 22:58:33'),
(418, 'terms_hero_subtitle', 2, 'textarea', 'Please read carefully the terms and conditions of use of our service.', NULL, NULL, '2026-02-09 20:08:48', '2026-08-14 22:58:33'),
(419, 'terms_date', 1, 'text', 'Atualizado em: 15 de Agosto de 2026', NULL, NULL, '2026-02-09 20:08:48', '2026-08-15 00:36:02'),
(420, 'terms_date', 2, 'text', 'Updated on: August 15, 2026', NULL, NULL, '2026-02-09 20:08:48', '2026-08-15 00:36:02'),
(431, 'footer_description', 1, 'textarea', 'Simplicidade, acolhimento e muito amor em Mogadouro, Portugal.', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(432, 'footer_description', 2, 'textarea', 'Simplicity, warmth and love in Mogadouro, Portugal.', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(433, 'footer_quicklinks_title', 1, 'text', 'Links Rápidos', NULL, NULL, '2026-02-09 20:31:09', '2026-08-14 17:12:28'),
(434, 'footer_quicklinks_title', 2, 'text', 'Quick Links', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(435, 'footer_contact_title', 1, 'text', 'Contacto', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(436, 'footer_contact_title', 2, 'text', 'Contact', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(437, 'footer_address', 1, 'text', '52 Avenida Nossa Senhora do Caminho, Mogadouro', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(438, 'footer_address', 2, 'text', '52 Avenida Nossa Senhora do Caminho, Mogadouro', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(439, 'footer_book_title', 1, 'text', 'Reserve Já', NULL, NULL, '2026-02-09 20:31:09', '2026-08-14 17:12:28'),
(440, 'footer_book_title', 2, 'text', 'Book Now', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(441, 'footer_rights_text', 1, 'text', 'Todos os direitos reservados.', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(442, 'footer_rights_text', 2, 'text', 'All rights reserved.', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(443, 'cookie_banner_text', 1, '', 'Utilizamos cookies para melhorar a sua experiência no nosso website. Ao continuar a navegar, concorda com a utilização de cookies. Saiba mais nos nossos <a href=\"/acasadogi/termos-condicoes/\" class=\"text-secondary hover:underline\">termos e condições</a> e <a href=\"/acasadogi/politica-privacidade/\" class=\"text-secondary hover:underline\">política de privacidade</a>.', NULL, NULL, '2026-02-09 20:31:09', '2026-08-14 22:56:00'),
(444, 'cookie_banner_text', 2, '', 'We use cookies to improve your experience on our website. By continuing to browse, you agree to our use of cookies. Learn more in our <a href=\"/acasadogi/en/termos-condicoes/\" class=\"text-secondary hover:underline\">terms and conditions</a> and <a href=\"/acasadogi/en/politica-privacidade/\" class=\"text-secondary hover:underline\">privacy policy</a>.', NULL, NULL, '2026-02-09 20:31:09', '2026-08-14 22:56:00'),
(445, 'cookie_banner_accept', 1, 'text', 'Aceitar', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(446, 'cookie_banner_accept', 2, 'text', 'Accept', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(447, 'cookie_banner_details', 1, 'text', 'Ver Detalhes', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(448, 'cookie_banner_details', 2, 'text', 'Details', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(467, 'home_image_split_left', 1, 'text', '/uploads/content/home_image_split_left_1787876228.webp', NULL, NULL, '2026-02-10 02:19:22', '2026-08-28 00:17:08'),
(468, 'home_image_split_left', 2, 'text', '/uploads/content/home_image_split_left_1787876228.webp', NULL, NULL, '2026-02-10 02:19:22', '2026-08-28 00:17:08'),
(469, 'accommodation_desc_1', 1, 'text', 'Descubra o conforto e a tradição transmontana nesta casa acolhedora, perfeita para famílias e grupos de amigos.', 'accommodations', NULL, '2026-08-23 22:42:30', '2026-08-23 22:50:25'),
(470, 'accommodation_desc_1', 2, 'text', 'Discover the comfort and Transmontana tradition in this cozy house, perfect for families and groups of friends.', 'accommodations', NULL, '2026-08-23 22:42:30', '2026-08-23 22:50:25'),
(471, 'accommodation_desc_2', 1, 'text', 'Um espaço único com vista para as paisagens transmontanas, ideal para momentos de descanso e conexão com a natureza.', 'accommodations', NULL, '2026-08-23 22:42:30', '2026-08-23 22:50:25'),
(472, 'accommodation_desc_2', 2, 'text', 'A unique space with views of the Transmontana landscapes, ideal for moments of rest and connection with nature.', 'accommodations', NULL, '2026-08-23 22:42:30', '2026-08-23 22:50:25'),
(473, 'accommodation_guests', 1, 'text', 'Hóspedes', 'accommodations', NULL, '2026-08-23 22:42:30', '2026-08-23 22:50:25'),
(474, 'accommodation_guests', 2, 'text', 'Guests', 'accommodations', NULL, '2026-08-23 22:42:30', '2026-08-23 22:50:25'),
(475, 'accommodation_bedrooms', 1, 'text', 'Quartos', 'accommodations', NULL, '2026-08-23 22:42:30', '2026-08-23 22:50:25'),
(476, 'accommodation_bedrooms', 2, 'text', 'Bedrooms', 'accommodations', NULL, '2026-08-23 22:42:30', '2026-08-23 22:50:25'),
(477, 'accommodation_view_details', 1, 'text', 'Ver Detalhes', 'accommodations', NULL, '2026-08-23 22:42:30', '2026-08-23 22:50:25'),
(478, 'accommodation_view_details', 2, 'text', 'View Details', 'accommodations', NULL, '2026-08-23 22:42:30', '2026-08-23 22:50:25'),
(489, 'about_image_intro', 1, 'text', '/uploads/content/about_image_intro_1787876104.webp', NULL, NULL, '2026-08-28 00:15:05', '2026-08-28 00:15:05'),
(490, 'about_image_intro', 2, 'text', '/uploads/content/about_image_intro_1787876104.webp', NULL, NULL, '2026-08-28 00:15:05', '2026-08-28 00:15:05'),
(491, 'about_image_region', 1, 'text', '/uploads/content/about_image_region_1787876163.webp', NULL, NULL, '2026-08-28 00:16:04', '2026-08-28 00:16:04'),
(492, 'about_image_region', 2, 'text', '/uploads/content/about_image_region_1787876163.webp', NULL, NULL, '2026-08-28 00:16:04', '2026-08-28 00:16:04'),
(493, 'home_image_split_right', 1, 'text', '/uploads/content/home_image_split_right_1787876221.webp', NULL, NULL, '2026-08-28 00:17:02', '2026-08-28 00:17:02'),
(494, 'home_image_split_right', 2, 'text', '/uploads/content/home_image_split_right_1787876221.webp', NULL, NULL, '2026-08-28 00:17:02', '2026-08-28 00:17:02'),
(495, 'home_image_about', 1, 'text', '/uploads/content/home_image_about_1787876288.webp', NULL, NULL, '2026-08-28 00:18:08', '2026-08-28 00:18:08'),
(496, 'home_image_about', 2, 'text', '/uploads/content/home_image_about_1787876288.webp', NULL, NULL, '2026-08-28 00:18:08', '2026-08-28 00:18:08');

-- --------------------------------------------------------

--
-- Estrutura da tabela `house_rules`
--

CREATE TABLE `house_rules` (
  `id` int(10) UNSIGNED NOT NULL,
  `accommodation_id` int(10) UNSIGNED NOT NULL,
  `is_highlighted` tinyint(1) DEFAULT 0 COMMENT 'Show in main section (not just modal)',
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `house_rules`
--

INSERT INTO `house_rules` (`id`, `accommodation_id`, `is_highlighted`, `sort_order`, `created_at`) VALUES
(26, 2, 1, 1, '2026-02-10 18:23:27'),
(27, 2, 1, 2, '2026-02-10 18:23:27'),
(28, 2, 1, 3, '2026-02-10 18:23:27'),
(29, 2, 1, 4, '2026-02-10 18:23:27'),
(30, 2, 1, 5, '2026-02-10 18:23:27'),
(31, 1, 1, 0, '2026-08-28 23:17:18'),
(32, 1, 1, 1, '2026-08-28 23:17:18'),
(33, 1, 1, 2, '2026-08-28 23:17:18'),
(34, 1, 1, 3, '2026-08-28 23:17:18'),
(35, 1, 1, 4, '2026-08-28 23:17:18');

-- --------------------------------------------------------

--
-- Estrutura da tabela `house_rule_translations`
--

CREATE TABLE `house_rule_translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `rule_id` int(10) UNSIGNED NOT NULL,
  `language_id` int(10) UNSIGNED NOT NULL,
  `rule_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `house_rule_translations`
--

INSERT INTO `house_rule_translations` (`id`, `rule_id`, `language_id`, `rule_text`) VALUES
(51, 26, 1, 'Adequado para crianças'),
(52, 26, 2, 'Suitable for children'),
(53, 27, 1, 'Adequado para bebés'),
(54, 27, 2, 'Suitable for infants'),
(55, 28, 1, 'Não são permitidos animais de estimação'),
(56, 28, 2, 'Pets are not allowed'),
(57, 29, 1, 'Não são permitidas festas'),
(58, 29, 2, 'Parties are not allowed'),
(59, 30, 1, 'Não é permitido fumar'),
(60, 30, 2, 'Smoking is not allowed'),
(61, 31, 1, 'Adequado para crianças'),
(62, 32, 1, 'Adequado para bebés'),
(63, 33, 1, 'Não são permitidos animais de estimação'),
(64, 34, 1, 'Não são permitidas festas'),
(65, 35, 1, 'Não é permitido fumar'),
(66, 31, 2, 'Suitable for children'),
(67, 32, 2, 'Suitable for infants'),
(68, 33, 2, 'Pets are not allowed'),
(69, 34, 2, 'Parties are not allowed'),
(70, 35, 2, 'Smoking is not allowed');

-- --------------------------------------------------------

--
-- Estrutura da tabela `languages`
--

CREATE TABLE `languages` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `locale` varchar(10) NOT NULL,
  `flag_icon` varchar(10) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `languages`
--

INSERT INTO `languages` (`id`, `code`, `name`, `locale`, `flag_icon`, `is_default`, `is_active`, `created_at`) VALUES
(1, 'pt', 'Português', 'pt_PT', 'pt', 1, 1, '2026-01-19 12:51:19'),
(2, 'en', 'English', 'en_GB', 'gb', 0, 1, '2026-01-19 12:51:19');

-- --------------------------------------------------------

--
-- Estrutura da tabela `legal_sections`
--

CREATE TABLE `legal_sections` (
  `id` int(10) UNSIGNED NOT NULL,
  `page` enum('terms','privacy') NOT NULL,
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `legal_sections`
--

INSERT INTO `legal_sections` (`id`, `page`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(19, 'privacy', 1, 1, '2026-08-15 00:19:04', '2026-08-15 00:19:04'),
(20, 'privacy', 2, 1, '2026-08-15 00:19:04', '2026-08-15 00:19:04'),
(21, 'privacy', 3, 1, '2026-08-15 00:19:04', '2026-08-15 00:19:04'),
(22, 'privacy', 4, 1, '2026-08-15 00:19:04', '2026-08-15 00:19:04'),
(23, 'privacy', 5, 1, '2026-08-15 00:19:04', '2026-08-15 00:19:04'),
(24, 'terms', 1, 1, '2026-08-15 00:19:04', '2026-08-15 00:19:04'),
(25, 'terms', 2, 1, '2026-08-15 00:19:04', '2026-08-15 00:19:04'),
(26, 'terms', 3, 1, '2026-08-15 00:19:04', '2026-08-15 00:19:04'),
(27, 'terms', 4, 1, '2026-08-15 00:19:04', '2026-08-15 00:19:04'),
(28, 'terms', 5, 1, '2026-08-15 00:19:04', '2026-08-15 00:19:04'),
(29, 'terms', 6, 1, '2026-08-15 00:19:04', '2026-08-15 00:19:04'),
(30, 'terms', 7, 1, '2026-08-15 00:19:04', '2026-08-15 00:47:57');

-- --------------------------------------------------------

--
-- Estrutura da tabela `legal_section_translations`
--

CREATE TABLE `legal_section_translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `section_id` int(10) UNSIGNED NOT NULL,
  `language_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `legal_section_translations`
--

INSERT INTO `legal_section_translations` (`id`, `section_id`, `language_id`, `title`, `content`) VALUES
(37, 19, 1, '1. Identificação do Responsável pelo Tratamento', 'A \"A Casa do Gi\" (doravante \"nós\" ou \"nosso\") é a entidade responsável pela recolha e tratamento dos dados pessoais submetidos através do website www.acasadogi.com. Comprometemo-nos a respeitar a sua privacidade e a proteger os seus dados pessoais em conformidade com o Regulamento Geral sobre a Proteção de Dados (RGPD) e a legislação portuguesa aplicável (Lei n.º 58/2019).'),
(38, 19, 2, '1. Identification of the Data Controller', '\"A Casa do Gi\" (hereinafter \"we\" or \"our\") is the entity responsible for the collection and processing of personal data submitted through the website www.acasadogi.com. We are committed to respecting your privacy and protecting your personal data in accordance with the General Data Protection Regulation (GDPR) and applicable Portuguese legislation (Law no. 58/2019).'),
(39, 20, 1, '2. Que dados recolhemos e para que fim?', 'Recolhemos apenas os dados estritamente necessários para a interação consigo. Através do nosso formulário de contacto, podemos recolher o seu Nome, Endereço de Email, Número de Telefone e a Mensagem que nos envia.\nPara a apresentação de mapas no website utilizamos a biblioteca Leaflet.\n\nEstes dados são utilizados **exclusivamente** com a finalidade de responder às suas questões, pedidos de informação ou esclarecimento de dúvidas (com base no consentimento e diligências pré-contratuais). Não utilizamos os seus dados para envio de marketing não solicitado nem os vendemos a terceiros.'),
(40, 20, 2, '2. What data do we collect and for what purpose?', 'We only collect the data strictly necessary for interaction with you. Through our contact form, we may collect your Name, Email Address, Phone Number, and the Message you send us.\nFor the display of maps on the website, we use the Leaflet library.\n\nThese data are used **exclusively** for the purpose of answering your questions, requests for information, or clarification of doubts (based on consent and pre-contractual steps). We do not use your data for sending unsolicited marketing nor do we sell it to third parties.'),
(41, 21, 1, '3. Plataformas de Terceiros e Reservas (GuestReady)', 'O nosso website não processa reservas diretas, nem recolhe dados de pagamento ou de cartão de crédito. Todo o processo de reserva de alojamento é feito através da plataforma externa gerida pela **GuestReady**. Ao clicar nos botões de \"Reservar\", será redirecionado para os servidores da GuestReady. Recomendamos a leitura da Política de Privacidade da referida plataforma, uma vez que o tratamento de dados inerentes à estadia e faturação é da sua exclusiva responsabilidade.'),
(42, 21, 2, '3. Third-party Platforms and Bookings (GuestReady)', 'Our website does not process direct bookings, nor does it collect payment or credit card data. The entire accommodation booking process is done through an external platform managed by **GuestReady**. By clicking on the \"Book\" buttons, you will be redirected to GuestReady\'s servers. We recommend reading the Privacy Policy of that platform, as the data processing inherent to the stay and billing is their exclusive responsibility.'),
(43, 22, 1, '4. Prazo de Conservação dos Dados', 'Os dados pessoais recolhidos através do formulário de contacto serão mantidos apenas pelo tempo estritamente necessário para responder ao seu pedido, ou por um período máximo de 30 dias. Findo esse prazo de 30 dias, todos os dados submetidos serão apagados permanentemente, exceto se houver uma obrigação legal que exija a sua conservação.'),
(44, 22, 2, '4. Data Retention Period', 'Personal data collected through the contact form will be kept only for the time strictly necessary to respond to your request, or for a maximum period of 30 days. At the end of this 30-day period, all submitted data will be permanently deleted, unless there is a legal obligation that requires its retention.'),
(45, 23, 1, '5. Os Seus Direitos', 'Nos termos do RGPD, o utilizador tem o direito de solicitar o acesso, retificação, apagamento (direito ao esquecimento), limitação do tratamento e a portabilidade dos seus dados pessoais. Pode exercer estes direitos a qualquer momento entrando em contacto connosco através do email ou telefone disponibilizados na página de Contactos deste website.'),
(46, 23, 2, '5. Your Rights', 'Under the GDPR, the user has the right to request access, rectification, erasure (right to be forgotten), restriction of processing, and portability of their personal data. You can exercise these rights at any time by contacting us through the email or phone number provided on the Contact page of this website.'),
(47, 24, 1, '1. Condições Gerais de Utilização', 'Os presentes Termos e Condições regulam a utilização do website www.acasadogi.com. Ao aceder e navegar neste website, o utilizador aceita estes Termos e Condições na íntegra. Caso não concorde com os mesmos, deverá cessar imediatamente a utilização deste website.'),
(48, 24, 2, '1. General Conditions of Use', 'These Terms and Conditions govern the use of the website www.acasadogi.com. By accessing and browsing this website, the user accepts these Terms and Conditions in full. If you do not agree with them, you must immediately cease using this website.'),
(49, 25, 1, '2. Reservas, Pagamentos e Estadias', 'O website \"A Casa do Gi\" tem um caráter meramente informativo e de apresentação das nossas propriedades. Não realizamos contratos de alojamento, não cobramos taxas nem processamos pagamentos de forma direta através do nosso website. Toda a gestão de reservas, verificação de disponibilidade, preços, pagamentos e políticas de cancelamento é operada de forma independente pela plataforma **GuestReady**. Quaisquer dúvidas, alterações, pagamentos ou litígios relacionados com a reserva da estadia e alojamento devem ser tratados exclusivamente junto da GuestReady, aplicando-se os termos e condições da referida plataforma.\n\nAlertamos que, por este website não conter todas as informações exaustivas, é de leitura obrigatória a descrição completa, as regras da casa e os valores adicionais estipulados na página da GuestReady antes de finalizar a reserva. Não nos responsabilizamos por queixas ou críticas resultantes da falta de leitura das condições que se encontram explicitamente detalhadas na plataforma de reservas.'),
(50, 25, 2, '2. Bookings, Payments, and Stays', 'The website \"A Casa do Gi\" has a purely informative character and presents our properties. We do not enter into accommodation contracts, charge fees, or process payments directly through our website. All management of bookings, availability verification, prices, payments, and cancellation policies is operated independently by the **GuestReady** platform. Any doubts, changes, payments, or disputes related to the booking of the stay and accommodation must be handled exclusively with GuestReady, applying the terms and conditions of that platform.\n\nWe warn that, as this website does not contain exhaustive information, reading the complete description, house rules, and additional fees stipulated on the GuestReady page is mandatory before finalizing the booking. We are not responsible for complaints or negative reviews resulting from the failure to read the conditions that are explicitly detailed on the booking platform.'),
(51, 26, 1, '3. Atividades Locais e Links de Terceiros', 'As informações disponibilizadas na secção \"O Que Fazer\" (Atividades) consistem em sugestões de roteiros e locais com caráter estritamente informativo. O nosso website contém ligações (links) diretas para websites de terceiros, como a Câmara Municipal de Mogadouro ou plataformas de rotas (ex: Komoot). Não exercemos qualquer controlo sobre o conteúdo, segurança, políticas de privacidade ou práticas de websites de terceiros. Como tal, isentamo-nos de qualquer responsabilidade por eventuais incorreções de informação, alterações de horários, acidentes ou problemas na prestação de serviços por essas entidades terceiras. O acesso a essas hiperligações é da inteira responsabilidade e risco do utilizador.'),
(52, 26, 2, '3. Local Activities and Third-Party Links', 'The information provided in the \"Things To Do\" (Activities) section consists of suggestions for itineraries and locations of a strictly informative nature. Our website contains direct links to third-party websites, such as the Mogadouro City Hall or route platforms (e.g., Komoot). We exercise no control over the content, security, privacy policies, or practices of third-party websites. As such, we exempt ourselves from any responsibility for possible inaccuracies of information, schedule changes, accidents, or problems in the provision of services by these third entities. Access to these links is at the user\'s entire responsibility and risk.'),
(53, 27, 1, '4. Loja e Produtos Regionais (Em Construção)', 'A secção \"Loja\" ou \"Produtos Regionais\" encontra-se, à presente data, em fase de desenvolvimento e construção. \"A Casa do Gi\" não efetua, através do seu website, qualquer apresentação ativa de catálogo, venda online ou transação financeira de produtos físicos. Qualquer menção a esta área tem um caráter meramente prospetivo e ilustrativo para projetos futuros.'),
(54, 27, 2, '4. Shop and Regional Products (Under Construction)', 'The \"Shop\" or \"Regional Products\" section is currently in the development and construction phase. \"A Casa do Gi\" does not carry out, through its website, any active catalog presentation, online sale, or financial transaction of physical products. Any mention of this area has a purely prospective and illustrative character for future projects.'),
(55, 28, 1, '5. Propriedade Intelectual', 'Todo o conteúdo presente neste website, incluindo (mas não limitado a) textos, logótipos, fotografias, imagens, design gráfico e código fonte, são propriedade exclusiva de \"A Casa do Gi\" ou de entidades que expressamente nos autorizaram a sua utilização, estando protegidos pela legislação nacional e internacional de Direitos de Autor e Propriedade Intelectual. É estritamente proibida a reprodução, cópia, distribuição ou modificação de qualquer conteúdo sem a nossa autorização prévia por escrito.'),
(56, 28, 2, '5. Intellectual Property', 'All content present on this website, including (but not limited to) texts, logos, photographs, images, graphic design, and source code, is the exclusive property of \"A Casa do Gi\" or entities that expressly authorized us to use them, and are protected by national and international Copyright and Intellectual Property legislation. It is strictly prohibited to reproduce, copy, distribute, or modify any content without our prior written authorization.'),
(57, 29, 1, '6. Alterações e Disponibilidade do Website', 'Reservamo-nos o direito de, a qualquer momento e sem aviso prévio, alterar, suspender ou descontinuar qualquer aspeto do website, bem como atualizar estes Termos e Condições. Recomendamos ao utilizador a consulta regular desta página para se manter informado. Não garantimos que o acesso ao website seja ininterrupto ou livre de falhas técnicas.'),
(58, 29, 2, '6. Changes and Website Availability', 'We reserve the right, at any time and without prior notice, to change, suspend, or discontinue any aspect of the website, as well as to update these Terms and Conditions. We recommend the user to regularly consult this page to stay informed. We do not guarantee that access to the website will be uninterrupted or free of technical faults.'),
(59, 30, 1, '7. Lei Aplicável e Foro Competente', 'Aos presentes Termos e Condições aplica-se a Lei Portuguesa. Para a resolução de qualquer litígio emergente da interpretação, aplicação ou execução dos presentes Termos, que não envolva responsabilidades afetas às plataformas parceiras de terceiros, é estipulado como exclusivamente competente o foro da Comarca de Trás-os-Montes/Bragança, com expressa renúncia a qualquer outro.'),
(60, 30, 2, '7. Applicable Law and Competent Jurisdiction', 'These Terms and Conditions are governed by Portuguese Law. For the resolution of any dispute arising from the interpretation, application, or execution of these Terms, which does not involve responsibilities assigned to third-party partner platforms, the competent jurisdiction is exclusively stipulated as the Court of the District of Trás-os-Montes/Bragança, with express waiver of any other.');

-- --------------------------------------------------------

--
-- Estrutura da tabela `media`
--

CREATE TABLE `media` (
  `id` int(10) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(10) UNSIGNED NOT NULL,
  `alt_text_pt` varchar(255) DEFAULT NULL,
  `alt_text_en` varchar(255) DEFAULT NULL,
  `caption_pt` varchar(500) DEFAULT NULL COMMENT 'Portuguese caption',
  `caption_en` varchar(500) DEFAULT NULL COMMENT 'English caption',
  `category` enum('gallery','content','cover','hero','other') DEFAULT 'other',
  `entity_type` enum('hero','accommodation','standalone','other') DEFAULT 'standalone',
  `entity_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'ID of the related entity (activity_id, hero_id, etc)',
  `is_cover` tinyint(1) DEFAULT 0 COMMENT 'Is this the cover/main image for the entity',
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `uploaded_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `accommodation_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Link to specific accommodation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `media`
--

INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `alt_text_pt`, `alt_text_en`, `caption_pt`, `caption_en`, `category`, `entity_type`, `entity_id`, `is_cover`, `sort_order`, `uploaded_by`, `created_at`, `accommodation_id`) VALUES
(61, 'hero_home_1787852609.webp', 'MogadouroAtividades.jpg', '/uploads/heroes/hero_home_1787852609.webp', 'image/webp', 386898, NULL, NULL, NULL, NULL, 'content', 'hero', 1, 1, 0, 1, '2026-08-27 17:43:31', NULL),
(64, 'hero_about_1787852647.webp', 'MogadouroSobre.png', '/uploads/heroes/hero_about_1787852647.webp', 'image/webp', 187776, NULL, NULL, NULL, NULL, 'content', 'hero', 4, 1, 0, 1, '2026-08-27 17:44:07', NULL),
(67, 'hero_shop_1787853412.webp', 'mogadouroloja.jpg', '/uploads/heroes/hero_shop_1787853412.webp', 'image/webp', 190586, NULL, NULL, NULL, NULL, 'content', 'hero', 6, 1, 0, 1, '2026-08-27 17:56:53', NULL),
(68, 'hero_activities_1787854068.webp', 'atividadesmogadouro.jpg', '/uploads/heroes/hero_activities_1787854068.webp', 'image/webp', 370858, NULL, NULL, NULL, NULL, 'content', 'hero', 3, 1, 0, 1, '2026-08-27 18:07:50', NULL),
(70, 'hero_privacy_policy_1787854841.webp', 'privamogadouro.webp', '/uploads/heroes/hero_privacy_policy_1787854841.webp', 'image/webp', 231742, NULL, NULL, NULL, NULL, 'content', 'hero', 11, 1, 0, 1, '2026-08-27 18:20:43', NULL),
(73, 'hero_terms_conditions_1787868865.webp', 'mogadourocastle.webp', '/uploads/heroes/hero_terms_conditions_1787868865.webp', 'image/webp', 243802, NULL, NULL, NULL, NULL, 'content', 'hero', 12, 1, 0, 1, '2026-08-27 22:14:27', NULL),
(74, 'hero_accommodation_main_1787868952.webp', 'mogadourocentro.webp', '/uploads/heroes/hero_accommodation_main_1787868952.webp', 'image/webp', 109840, NULL, NULL, NULL, NULL, 'content', 'hero', 2, 1, 0, 1, '2026-08-27 22:15:53', NULL),
(75, 'hero_contact_1787869225.webp', 'mogadouroigreja.jpg', '/uploads/heroes/hero_contact_1787869225.webp', 'image/webp', 121302, NULL, NULL, NULL, NULL, 'content', 'hero', 5, 1, 0, 1, '2026-08-27 22:20:28', NULL),
(76, 'about_image_intro_1787876104.webp', 'FotoGi.png', '/uploads/content/about_image_intro_1787876104.webp', 'image/webp', 35846, NULL, NULL, NULL, NULL, 'content', 'standalone', NULL, 0, 0, 1, '2026-08-28 00:15:05', NULL),
(77, 'about_image_region_1787876163.webp', 'Castelo.jpg', '/uploads/content/about_image_region_1787876163.webp', 'image/webp', 126584, NULL, NULL, NULL, NULL, 'content', 'standalone', NULL, 0, 0, 1, '2026-08-28 00:16:04', NULL),
(78, 'home_image_split_right_1787876221.webp', 'Castelo.jpg', '/uploads/content/home_image_split_right_1787876221.webp', 'image/webp', 126584, NULL, NULL, NULL, NULL, 'content', 'standalone', NULL, 0, 0, 1, '2026-08-28 00:17:02', NULL),
(79, 'home_image_split_left_1787876228.webp', 'IgrejaMatriz.jpg', '/uploads/content/home_image_split_left_1787876228.webp', 'image/webp', 102258, NULL, NULL, NULL, NULL, 'content', 'standalone', NULL, 0, 0, 1, '2026-08-28 00:17:08', NULL),
(80, 'home_image_about_1787876288.webp', 'MogadouroSobre.png', '/uploads/content/home_image_about_1787876288.webp', 'image/webp', 187776, NULL, NULL, NULL, NULL, 'content', 'standalone', NULL, 0, 0, 1, '2026-08-28 00:18:08', NULL),
(83, 'cover_casa1_1787968348.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 36.jpg', '/uploads/accommodation/cover_casa1_1787968348.webp', 'image/webp', 210700, NULL, NULL, NULL, NULL, 'cover', 'accommodation', NULL, 1, 0, NULL, '2026-08-29 01:52:29', 1),
(84, 'hero_casa1_1787969371.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 39.jpg', '/uploads/accommodation/hero_casa1_1787969371.webp', 'image/webp', 94758, NULL, NULL, NULL, NULL, 'hero', 'accommodation', NULL, 0, 0, NULL, '2026-08-29 02:09:32', 1),
(85, 'accommodation_1_6a92d8131ba41.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 35.jpg', '/uploads/accommodation/accommodation_1_6a92d8131ba41.webp', 'image/webp', 198502, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 1, NULL, '2026-08-29 13:01:08', 1),
(86, 'accommodation_1_6a92d81402486.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 36.jpg', '/uploads/accommodation/accommodation_1_6a92d81402486.webp', 'image/webp', 210700, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 2, NULL, '2026-08-29 13:01:08', 1),
(87, 'accommodation_1_6a92d814d3ba4.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 37.jpg', '/uploads/accommodation/accommodation_1_6a92d814d3ba4.webp', 'image/webp', 184252, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 3, NULL, '2026-08-29 13:01:09', 1),
(88, 'accommodation_1_6a92d815b01af.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 38.jpg', '/uploads/accommodation/accommodation_1_6a92d815b01af.webp', 'image/webp', 169330, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 4, NULL, '2026-08-29 13:01:10', 1),
(89, 'accommodation_1_6a92d816856aa.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 39.jpg', '/uploads/accommodation/accommodation_1_6a92d816856aa.webp', 'image/webp', 94758, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 5, NULL, '2026-08-29 13:01:11', 1),
(90, 'accommodation_1_6a92d842096c7.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 42.jpg', '/uploads/accommodation/accommodation_1_6a92d842096c7.webp', 'image/webp', 70852, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 6, NULL, '2026-08-29 13:01:54', 1),
(91, 'accommodation_1_6a92d842cc14f.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 43.jpg', '/uploads/accommodation/accommodation_1_6a92d842cc14f.webp', 'image/webp', 72520, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 7, NULL, '2026-08-29 13:01:55', 1),
(92, 'accommodation_1_6a92d85dc083c.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 18.jpg', '/uploads/accommodation/accommodation_1_6a92d85dc083c.webp', 'image/webp', 177140, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 8, NULL, '2026-08-29 13:02:22', 1),
(93, 'accommodation_1_6a92d85e9dcb5.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 19.jpg', '/uploads/accommodation/accommodation_1_6a92d85e9dcb5.webp', 'image/webp', 163952, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 9, NULL, '2026-08-29 13:02:23', 1),
(94, 'accommodation_1_6a92d86aec7a5.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 17.jpg', '/uploads/accommodation/accommodation_1_6a92d86aec7a5.webp', 'image/webp', 139742, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 10, NULL, '2026-08-29 13:02:35', 1),
(95, 'accommodation_1_6a92d88637a7a.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 20.jpg', '/uploads/accommodation/accommodation_1_6a92d88637a7a.webp', 'image/webp', 102340, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 11, NULL, '2026-08-29 13:03:03', 1),
(96, 'accommodation_1_6a92d88708951.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 21.jpg', '/uploads/accommodation/accommodation_1_6a92d88708951.webp', 'image/webp', 237778, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 12, NULL, '2026-08-29 13:03:03', 1),
(97, 'accommodation_1_6a92d887ed308.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 22.jpg', '/uploads/accommodation/accommodation_1_6a92d887ed308.webp', 'image/webp', 33892, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 13, NULL, '2026-08-29 13:03:04', 1),
(98, 'accommodation_1_6a92d888afc7c.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 23.jpg', '/uploads/accommodation/accommodation_1_6a92d888afc7c.webp', 'image/webp', 51434, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 14, NULL, '2026-08-29 13:03:05', 1),
(99, 'accommodation_1_6a92d8a4526ac.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 28.jpg', '/uploads/accommodation/accommodation_1_6a92d8a4526ac.webp', 'image/webp', 158124, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 15, NULL, '2026-08-29 13:03:33', 1),
(100, 'accommodation_1_6a92d8a531e47.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 29.jpg', '/uploads/accommodation/accommodation_1_6a92d8a531e47.webp', 'image/webp', 142708, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 16, NULL, '2026-08-29 13:03:34', 1),
(101, 'accommodation_1_6a92d8a60a79f.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 30.jpg', '/uploads/accommodation/accommodation_1_6a92d8a60a79f.webp', 'image/webp', 181312, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 17, NULL, '2026-08-29 13:03:34', 1),
(102, 'accommodation_1_6a92d8a6d8e34.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 31.jpg', '/uploads/accommodation/accommodation_1_6a92d8a6d8e34.webp', 'image/webp', 111676, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 18, NULL, '2026-08-29 13:03:35', 1),
(103, 'accommodation_1_6a92d8a7a739a.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 32.jpg', '/uploads/accommodation/accommodation_1_6a92d8a7a739a.webp', 'image/webp', 94334, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 19, NULL, '2026-08-29 13:03:36', 1),
(104, 'accommodation_1_6a92d8a876425.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 33.jpg', '/uploads/accommodation/accommodation_1_6a92d8a876425.webp', 'image/webp', 50894, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 20, NULL, '2026-08-29 13:03:37', 1),
(105, 'accommodation_1_6a92d8a93f6f7.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 34.jpg', '/uploads/accommodation/accommodation_1_6a92d8a93f6f7.webp', 'image/webp', 60752, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 21, NULL, '2026-08-29 13:03:38', 1),
(106, 'accommodation_1_6a92d8aa0bde8.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 40.jpg', '/uploads/accommodation/accommodation_1_6a92d8aa0bde8.webp', 'image/webp', 115546, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 22, NULL, '2026-08-29 13:03:38', 1),
(107, 'accommodation_1_6a92d8aad7339.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 41.jpg', '/uploads/accommodation/accommodation_1_6a92d8aad7339.webp', 'image/webp', 328164, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 23, NULL, '2026-08-29 13:03:39', 1),
(108, 'accommodation_1_6a92d8cd7265a.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 24.jpg', '/uploads/accommodation/accommodation_1_6a92d8cd7265a.webp', 'image/webp', 192708, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 24, NULL, '2026-08-29 13:04:14', 1),
(109, 'accommodation_1_6a92d8ce5b012.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 25.jpg', '/uploads/accommodation/accommodation_1_6a92d8ce5b012.webp', 'image/webp', 192194, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 25, NULL, '2026-08-29 13:04:15', 1),
(110, 'accommodation_1_6a92d8cf3f8d0.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 26.jpg', '/uploads/accommodation/accommodation_1_6a92d8cf3f8d0.webp', 'image/webp', 130706, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 26, NULL, '2026-08-29 13:04:16', 1),
(111, 'accommodation_1_6a92d8d0127ac.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 27.jpg', '/uploads/accommodation/accommodation_1_6a92d8d0127ac.webp', 'image/webp', 93898, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 27, NULL, '2026-08-29 13:04:16', 1),
(112, 'accommodation_1_6a92d8d0d14a3.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 44.jpg', '/uploads/accommodation/accommodation_1_6a92d8d0d14a3.webp', 'image/webp', 260184, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 28, NULL, '2026-08-29 13:04:17', 1),
(113, 'accommodation_1_6a92d8d1b9aae.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 45.jpg', '/uploads/accommodation/accommodation_1_6a92d8d1b9aae.webp', 'image/webp', 345978, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 29, NULL, '2026-08-29 13:04:18', 1),
(114, 'accommodation_1_6a92d8d2b4b49.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 46.jpg', '/uploads/accommodation/accommodation_1_6a92d8d2b4b49.webp', 'image/webp', 375814, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 30, NULL, '2026-08-29 13:04:19', 1),
(115, 'accommodation_1_6a92d8d3baf9f.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 47.jpg', '/uploads/accommodation/accommodation_1_6a92d8d3baf9f.webp', 'image/webp', 73854, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 31, NULL, '2026-08-29 13:04:20', 1),
(116, 'accommodation_1_6a92d8d4843b9.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 48.jpg', '/uploads/accommodation/accommodation_1_6a92d8d4843b9.webp', 'image/webp', 141832, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 32, NULL, '2026-08-29 13:04:21', 1),
(117, 'accommodation_1_6a92d8d5591d3.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 49.jpg', '/uploads/accommodation/accommodation_1_6a92d8d5591d3.webp', 'image/webp', 89178, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 33, NULL, '2026-08-29 13:04:22', 1),
(118, 'accommodation_1_6a92d8d627b8f.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 50.jpg', '/uploads/accommodation/accommodation_1_6a92d8d627b8f.webp', 'image/webp', 94892, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 34, NULL, '2026-08-29 13:04:22', 1),
(119, 'accommodation_1_6a92d8d6e97f6.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 51.jpg', '/uploads/accommodation/accommodation_1_6a92d8d6e97f6.webp', 'image/webp', 70778, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 35, NULL, '2026-08-29 13:04:23', 1),
(120, 'accommodation_1_6a92d90743184.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 07.jpg', '/uploads/accommodation/accommodation_1_6a92d90743184.webp', 'image/webp', 60694, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 36, NULL, '2026-08-29 13:05:12', 1),
(121, 'accommodation_1_6a92d9080927e.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 08.jpg', '/uploads/accommodation/accommodation_1_6a92d9080927e.webp', 'image/webp', 188328, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 37, NULL, '2026-08-29 13:05:12', 1),
(122, 'accommodation_1_6a92d908e32ec.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 02.jpg', '/uploads/accommodation/accommodation_1_6a92d908e32ec.webp', 'image/webp', 134558, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 38, NULL, '2026-08-29 13:05:13', 1),
(123, 'accommodation_1_6a92d909bdbdb.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 03.jpg', '/uploads/accommodation/accommodation_1_6a92d909bdbdb.webp', 'image/webp', 124184, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 39, NULL, '2026-08-29 13:05:14', 1),
(124, 'accommodation_1_6a92d91fec78f.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 01.jpg', '/uploads/accommodation/accommodation_1_6a92d91fec78f.webp', 'image/webp', 160732, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 40, NULL, '2026-08-29 13:05:36', 1),
(125, 'accommodation_1_6a92d920d65fd.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 04.jpg', '/uploads/accommodation/accommodation_1_6a92d920d65fd.webp', 'image/webp', 114522, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 41, NULL, '2026-08-29 13:05:37', 1),
(126, 'accommodation_1_6a92d9219eec9.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 05.jpg', '/uploads/accommodation/accommodation_1_6a92d9219eec9.webp', 'image/webp', 122680, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 42, NULL, '2026-08-29 13:05:38', 1),
(127, 'accommodation_1_6a92d9226ef7c.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 06.jpg', '/uploads/accommodation/accommodation_1_6a92d9226ef7c.webp', 'image/webp', 122122, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 43, NULL, '2026-08-29 13:05:39', 1),
(128, 'accommodation_1_6a92d9233c777.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 09.jpg', '/uploads/accommodation/accommodation_1_6a92d9233c777.webp', 'image/webp', 106332, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 44, NULL, '2026-08-29 13:05:40', 1),
(129, 'accommodation_1_6a92d92f8e819.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 10.jpg', '/uploads/accommodation/accommodation_1_6a92d92f8e819.webp', 'image/webp', 111274, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 45, NULL, '2026-08-29 13:05:52', 1),
(130, 'accommodation_1_6a92d9305c97e.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 11.jpg', '/uploads/accommodation/accommodation_1_6a92d9305c97e.webp', 'image/webp', 347650, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 46, NULL, '2026-08-29 13:05:53', 1),
(131, 'accommodation_1_6a92d931572c2.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 12.jpg', '/uploads/accommodation/accommodation_1_6a92d931572c2.webp', 'image/webp', 277902, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 47, NULL, '2026-08-29 13:05:54', 1),
(132, 'accommodation_1_6a92d93244d34.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 13.jpg', '/uploads/accommodation/accommodation_1_6a92d93244d34.webp', 'image/webp', 277236, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 48, NULL, '2026-08-29 13:05:55', 1),
(133, 'accommodation_1_6a92d9333010e.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 14.jpg', '/uploads/accommodation/accommodation_1_6a92d9333010e.webp', 'image/webp', 305276, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 49, NULL, '2026-08-29 13:05:56', 1),
(134, 'accommodation_1_6a92d93423d24.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 15.jpg', '/uploads/accommodation/accommodation_1_6a92d93423d24.webp', 'image/webp', 98064, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 50, NULL, '2026-08-29 13:05:56', 1),
(135, 'accommodation_1_6a92d934e7ecb.webp', 'GuestReady (airbnb res) -  A Casa do Gi1 72622 - 16.jpg', '/uploads/accommodation/accommodation_1_6a92d934e7ecb.webp', 'image/webp', 125348, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 51, NULL, '2026-08-29 13:05:57', 1),
(137, 'cover_casa2_1788124212.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 16.jpg', '/uploads/accommodation/cover_casa2_1788124212.webp', 'image/webp', 156612, NULL, NULL, NULL, NULL, 'cover', 'accommodation', NULL, 1, 0, NULL, '2026-08-30 21:10:13', 2),
(138, 'hero_casa2_1788124256.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 21.jpg', '/uploads/accommodation/hero_casa2_1788124256.webp', 'image/webp', 181194, NULL, NULL, NULL, NULL, 'hero', 'accommodation', NULL, 0, 0, NULL, '2026-08-30 21:10:57', 2),
(139, 'accommodation_2_6a94b2dd31f3a.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 16.jpg', '/uploads/accommodation/accommodation_2_6a94b2dd31f3a.webp', 'image/webp', 156612, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 1, NULL, '2026-08-30 22:46:54', 2),
(140, 'accommodation_2_6a94b2de10a46.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 17.jpg', '/uploads/accommodation/accommodation_2_6a94b2de10a46.webp', 'image/webp', 160862, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 2, NULL, '2026-08-30 22:46:54', 2),
(141, 'accommodation_2_6a94b2dedf199.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 18.jpg', '/uploads/accommodation/accommodation_2_6a94b2dedf199.webp', 'image/webp', 183662, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 3, NULL, '2026-08-30 22:46:55', 2),
(142, 'accommodation_2_6a94b2dfc69d8.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 19.jpg', '/uploads/accommodation/accommodation_2_6a94b2dfc69d8.webp', 'image/webp', 194134, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 4, NULL, '2026-08-30 22:46:56', 2),
(143, 'accommodation_2_6a94b2e0a4997.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 20.jpg', '/uploads/accommodation/accommodation_2_6a94b2e0a4997.webp', 'image/webp', 48380, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 5, NULL, '2026-08-30 22:46:57', 2),
(144, 'accommodation_2_6a94b2e16b041.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 21.jpg', '/uploads/accommodation/accommodation_2_6a94b2e16b041.webp', 'image/webp', 181194, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 6, NULL, '2026-08-30 22:46:58', 2),
(145, 'accommodation_2_6a94b2e255a6c.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 22.jpg', '/uploads/accommodation/accommodation_2_6a94b2e255a6c.webp', 'image/webp', 41376, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 7, NULL, '2026-08-30 22:46:59', 2),
(146, 'accommodation_2_6a94b354e969b.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 36.jpg', '/uploads/accommodation/accommodation_2_6a94b354e969b.webp', 'image/webp', 182512, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 8, NULL, '2026-08-30 22:48:53', 2),
(147, 'accommodation_2_6a94b361547cd.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 37.jpg', '/uploads/accommodation/accommodation_2_6a94b361547cd.webp', 'image/webp', 163332, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 9, NULL, '2026-08-30 22:49:06', 2),
(148, 'accommodation_2_6a94b36d3a480.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 35.jpg', '/uploads/accommodation/accommodation_2_6a94b36d3a480.webp', 'image/webp', 182562, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 10, NULL, '2026-08-30 22:49:18', 2),
(149, 'accommodation_2_6a94b37cec99c.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 38.jpg', '/uploads/accommodation/accommodation_2_6a94b37cec99c.webp', 'image/webp', 127820, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 11, NULL, '2026-08-30 22:49:33', 2),
(150, 'accommodation_2_6a94b37dd06d1.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 39.jpg', '/uploads/accommodation/accommodation_2_6a94b37dd06d1.webp', 'image/webp', 316658, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 12, NULL, '2026-08-30 22:49:34', 2),
(151, 'accommodation_2_6a94b37ec9c85.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 42.jpg', '/uploads/accommodation/accommodation_2_6a94b37ec9c85.webp', 'image/webp', 81802, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 13, NULL, '2026-08-30 22:49:35', 2),
(152, 'accommodation_2_6a94b38d423a4.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 40.jpg', '/uploads/accommodation/accommodation_2_6a94b38d423a4.webp', 'image/webp', 36914, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 14, NULL, '2026-08-30 22:49:50', 2),
(153, 'accommodation_2_6a94b38e0dd0d.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 41.jpg', '/uploads/accommodation/accommodation_2_6a94b38e0dd0d.webp', 'image/webp', 35092, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 15, NULL, '2026-08-30 22:49:50', 2),
(154, 'accommodation_2_6a94b3ae8a52d.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 08.jpg', '/uploads/accommodation/accommodation_2_6a94b3ae8a52d.webp', 'image/webp', 176412, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 16, NULL, '2026-08-30 22:50:23', 2),
(155, 'accommodation_2_6a94b3af731b9.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 09.jpg', '/uploads/accommodation/accommodation_2_6a94b3af731b9.webp', 'image/webp', 206840, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 17, NULL, '2026-08-30 22:50:24', 2),
(156, 'accommodation_2_6a94b3b05ddad.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 10.jpg', '/uploads/accommodation/accommodation_2_6a94b3b05ddad.webp', 'image/webp', 151064, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 18, NULL, '2026-08-30 22:50:25', 2),
(157, 'accommodation_2_6a94b3b13b8b5.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 11.jpg', '/uploads/accommodation/accommodation_2_6a94b3b13b8b5.webp', 'image/webp', 67406, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 19, NULL, '2026-08-30 22:50:26', 2),
(158, 'accommodation_2_6a94b3b20ed14.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 12.jpg', '/uploads/accommodation/accommodation_2_6a94b3b20ed14.webp', 'image/webp', 349766, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 20, NULL, '2026-08-30 22:50:27', 2),
(159, 'accommodation_2_6a94b3b311932.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 13.jpg', '/uploads/accommodation/accommodation_2_6a94b3b311932.webp', 'image/webp', 113812, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 21, NULL, '2026-08-30 22:50:27', 2),
(160, 'accommodation_2_6a94b3b3e70f2.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 14.jpg', '/uploads/accommodation/accommodation_2_6a94b3b3e70f2.webp', 'image/webp', 58390, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 22, NULL, '2026-08-30 22:50:28', 2),
(161, 'accommodation_2_6a94b3b4b8d43.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 15.jpg', '/uploads/accommodation/accommodation_2_6a94b3b4b8d43.webp', 'image/webp', 89942, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 23, NULL, '2026-08-30 22:50:29', 2),
(162, 'accommodation_2_6a94b3c7dc213.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 23.jpg', '/uploads/accommodation/accommodation_2_6a94b3c7dc213.webp', 'image/webp', 263934, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 24, NULL, '2026-08-30 22:50:48', 2),
(163, 'accommodation_2_6a94b3c8d2075.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 24.jpg', '/uploads/accommodation/accommodation_2_6a94b3c8d2075.webp', 'image/webp', 343080, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 25, NULL, '2026-08-30 22:50:49', 2),
(164, 'accommodation_2_6a94b3c9da77c.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 25.jpg', '/uploads/accommodation/accommodation_2_6a94b3c9da77c.webp', 'image/webp', 84488, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 26, NULL, '2026-08-30 22:50:50', 2),
(165, 'accommodation_2_6a94b3cab121c.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 26.jpg', '/uploads/accommodation/accommodation_2_6a94b3cab121c.webp', 'image/webp', 91626, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 27, NULL, '2026-08-30 22:50:51', 2),
(166, 'accommodation_2_6a94b3e84c2b6.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 04.jpg', '/uploads/accommodation/accommodation_2_6a94b3e84c2b6.webp', 'image/webp', 129046, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 28, NULL, '2026-08-30 22:51:21', 2),
(167, 'accommodation_2_6a94b3e929917.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 05.jpg', '/uploads/accommodation/accommodation_2_6a94b3e929917.webp', 'image/webp', 181554, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 29, NULL, '2026-08-30 22:51:22', 2),
(168, 'accommodation_2_6a94b3ea1ab49.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 06.jpg', '/uploads/accommodation/accommodation_2_6a94b3ea1ab49.webp', 'image/webp', 51354, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 30, NULL, '2026-08-30 22:51:22', 2),
(169, 'accommodation_2_6a94b3eadc9d6.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 07.jpg', '/uploads/accommodation/accommodation_2_6a94b3eadc9d6.webp', 'image/webp', 87258, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 31, NULL, '2026-08-30 22:51:23', 2),
(170, 'accommodation_2_6a94ba16430d4.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 27.jpg', '/uploads/accommodation/accommodation_2_6a94ba16430d4.webp', 'image/webp', 251888, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 32, 1, '2026-08-30 23:17:43', 2),
(171, 'accommodation_2_6a94ba1740292.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 28.jpg', '/uploads/accommodation/accommodation_2_6a94ba1740292.webp', 'image/webp', 265472, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 33, 1, '2026-08-30 23:17:44', 2),
(172, 'accommodation_2_6a94ba183a1f1.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 29.jpg', '/uploads/accommodation/accommodation_2_6a94ba183a1f1.webp', 'image/webp', 319428, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 34, 1, '2026-08-30 23:17:45', 2),
(173, 'accommodation_2_6a94ba193b416.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 30.jpg', '/uploads/accommodation/accommodation_2_6a94ba193b416.webp', 'image/webp', 321336, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 35, 1, '2026-08-30 23:17:46', 2),
(174, 'accommodation_2_6a94ba1a3a920.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 31.jpg', '/uploads/accommodation/accommodation_2_6a94ba1a3a920.webp', 'image/webp', 78916, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 36, 1, '2026-08-30 23:17:46', 2),
(175, 'accommodation_2_6a94ba1b07e1f.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 32.jpg', '/uploads/accommodation/accommodation_2_6a94ba1b07e1f.webp', 'image/webp', 205826, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 37, 1, '2026-08-30 23:17:47', 2),
(176, 'accommodation_2_6a94ba1bef13e.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 33.jpg', '/uploads/accommodation/accommodation_2_6a94ba1bef13e.webp', 'image/webp', 191694, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 38, 1, '2026-08-30 23:17:48', 2),
(177, 'accommodation_2_6a94ba1cdbf5e.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 34.jpg', '/uploads/accommodation/accommodation_2_6a94ba1cdbf5e.webp', 'image/webp', 79876, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 39, 1, '2026-08-30 23:17:49', 2),
(178, 'accommodation_2_6a94ba1dbfa94.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 43.jpg', '/uploads/accommodation/accommodation_2_6a94ba1dbfa94.webp', 'image/webp', 162082, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 40, 1, '2026-08-30 23:17:50', 2),
(179, 'accommodation_2_6a94ba1ea7e22.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 44.jpg', '/uploads/accommodation/accommodation_2_6a94ba1ea7e22.webp', 'image/webp', 104562, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 41, 1, '2026-08-30 23:17:51', 2),
(180, 'accommodation_2_6a94ba1f79b4e.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 45.jpg', '/uploads/accommodation/accommodation_2_6a94ba1f79b4e.webp', 'image/webp', 168550, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 42, 1, '2026-08-30 23:17:52', 2),
(181, 'accommodation_2_6a94ba2068946.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 46.jpg', '/uploads/accommodation/accommodation_2_6a94ba2068946.webp', 'image/webp', 202332, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 43, 1, '2026-08-30 23:17:53', 2),
(182, 'accommodation_2_6a94ba214f7b9.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 47.jpg', '/uploads/accommodation/accommodation_2_6a94ba214f7b9.webp', 'image/webp', 146578, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 44, 1, '2026-08-30 23:17:54', 2),
(183, 'accommodation_2_6a94ba222780f.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 48.jpg', '/uploads/accommodation/accommodation_2_6a94ba222780f.webp', 'image/webp', 124988, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 45, 1, '2026-08-30 23:17:54', 2),
(184, 'accommodation_2_6a94ba2306380.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 49.jpg', '/uploads/accommodation/accommodation_2_6a94ba2306380.webp', 'image/webp', 114030, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 46, 1, '2026-08-30 23:17:55', 2),
(185, 'accommodation_2_6a94ba23e25d2.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 50.jpg', '/uploads/accommodation/accommodation_2_6a94ba23e25d2.webp', 'image/webp', 114866, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 47, 1, '2026-08-30 23:17:56', 2),
(186, 'accommodation_2_6a94ba24bd879.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 51.jpg', '/uploads/accommodation/accommodation_2_6a94ba24bd879.webp', 'image/webp', 110596, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 48, 1, '2026-08-30 23:17:57', 2),
(187, 'accommodation_2_6a94ba2596d91.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 52.jpg', '/uploads/accommodation/accommodation_2_6a94ba2596d91.webp', 'image/webp', 63698, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 49, 1, '2026-08-30 23:17:58', 2),
(188, 'accommodation_2_6a94ba2663d44.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 01.jpg', '/uploads/accommodation/accommodation_2_6a94ba2663d44.webp', 'image/webp', 137166, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 50, 1, '2026-08-30 23:17:59', 2),
(189, 'accommodation_2_6a94ba27351e6.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 02.jpg', '/uploads/accommodation/accommodation_2_6a94ba27351e6.webp', 'image/webp', 122312, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 51, 1, '2026-08-30 23:18:00', 2),
(190, 'accommodation_2_6a94ba281ce25.webp', 'GuestReady (airbnb res) -  A Casa do Gi2 72624 - 03.jpg', '/uploads/accommodation/accommodation_2_6a94ba281ce25.webp', 'image/webp', 42418, '', '', NULL, NULL, 'gallery', 'accommodation', NULL, 0, 52, 1, '2026-08-30 23:18:00', 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `page_heroes`
--

CREATE TABLE `page_heroes` (
  `id` int(10) UNSIGNED NOT NULL,
  `page_key` varchar(50) NOT NULL COMMENT 'Unique page identifier',
  `page_name_pt` varchar(100) NOT NULL COMMENT 'Page name in Portuguese',
  `page_name_en` varchar(100) NOT NULL COMMENT 'Page name in English',
  `hero_overlay_opacity` decimal(3,2) DEFAULT 0.40 COMMENT 'Overlay darkness (0-1)',
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `page_heroes`
--

INSERT INTO `page_heroes` (`id`, `page_key`, `page_name_pt`, `page_name_en`, `hero_overlay_opacity`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'home', 'Página Inicial', 'Homepage', 0.40, 1, 1, '2026-02-03 00:18:35', '2026-02-03 00:18:35'),
(2, 'accommodation_main', 'Alojamento', 'Accommodation (Main Page)', 0.10, 1, 2, '2026-02-03 00:18:35', '2026-08-27 22:16:32'),
(3, 'activities', 'Atividades', 'Activities', 0.30, 1, 3, '2026-02-03 00:18:35', '2026-08-27 22:20:12'),
(4, 'about', 'Sobre Nós', 'About Us', 0.40, 1, 4, '2026-02-03 00:18:35', '2026-02-03 02:10:03'),
(5, 'contact', 'Contactos', 'Contact', 0.30, 1, 5, '2026-02-03 00:18:35', '2026-08-28 00:12:20'),
(6, 'shop', 'Loja', 'Shop', 0.40, 1, 6, '2026-02-03 00:18:35', '2026-02-03 00:18:35'),
(11, 'privacy_policy', 'Política de Privacidade', 'Privacy Policy', 0.40, 1, 10, '2026-02-09 20:08:12', '2026-02-10 21:37:27'),
(12, 'terms_conditions', 'Termos e Condições', 'Terms and Conditions', 0.40, 1, 11, '2026-02-09 20:08:12', '2026-02-10 21:38:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','textarea','boolean','number','json','email','url') DEFAULT 'text',
  `setting_group` varchar(50) DEFAULT 'general',
  `description` varchar(255) DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `setting_group`, `description`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'A Casa do Gi', 'text', 'general', 'Nome do site', 1, '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(2, 'site_tagline_pt', 'Simplicidade, acolhimento e muito amor', 'text', 'general', 'Tagline PT', 1, '2026-01-19 12:51:19', '2026-01-20 16:22:55'),
(3, 'site_tagline_en', 'Simplicity, warmth and love', 'text', 'general', 'Tagline EN', 1, '2026-01-19 12:51:19', '2026-01-20 16:22:55'),
(4, 'contact_email', 'geral@acasadogi.pt', 'email', 'contact', 'Email principal', 1, '2026-01-19 12:51:19', '2026-01-20 16:22:55'),
(5, 'contact_phone', '+351 912 345 678', 'text', 'contact', 'Telefone', 1, '2026-01-19 12:51:19', '2026-01-20 16:22:55'),
(6, 'contact_address', 'Rua Principal, 123\r\n5200 Mogadouro\r\nPortugal', 'textarea', 'contact', 'Morada', 1, '2026-01-19 12:51:19', '2026-08-28 00:30:24'),
(7, 'contact_form_enabled', '1', 'boolean', 'contact', 'Formulario ativo', 0, '2026-01-19 12:51:19', '2026-08-28 00:30:39'),
(8, 'facebook_url', 'https://facebook.com/acasadogi', 'url', 'social', 'URL Facebook', 1, '2026-01-19 12:51:19', '2026-01-20 16:22:55'),
(9, 'instagram_url', 'https://instagram.com/acasadogi', 'url', 'social', 'URL Instagram', 1, '2026-01-19 12:51:19', '2026-01-20 16:22:55'),
(10, 'booking_url', 'https://www.booking.com/', 'url', 'booking', 'URL Booking.com', 1, '2026-01-19 12:51:19', '2026-01-20 16:22:55'),
(11, 'airbnb_url', 'https://www.airbnb.com/', 'url', 'booking', 'URL Airbnb', 1, '2026-01-19 12:51:19', '2026-01-20 16:22:55'),
(13, 'shop_enabled', '1', 'boolean', 'shop', 'Loja ativa', 0, '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(14, 'shop_shipping_fee', '5.00', 'number', 'shop', 'Taxa de envio', 0, '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(15, 'shop_free_shipping_above', '50.00', 'number', 'shop', 'Portes gratis acima de', 0, '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(16, 'maintenance_mode', '0', 'boolean', 'general', 'Modo manutencao', 0, '2026-01-19 12:51:19', '2026-08-28 00:30:16'),
(17, 'free_shipping_threshold', '50', 'number', 'shop', NULL, 0, '2026-01-20 16:22:55', '2026-01-20 16:22:55'),
(18, 'shipping_cost', '5', 'number', 'shop', NULL, 0, '2026-01-20 16:22:55', '2026-01-20 16:22:55'),
(82, 'shop_mode', 'manual', 'text', 'shop', 'Modo da loja: active, manual, closed', 0, '2026-02-07 20:01:43', '2026-02-09 00:34:35'),
(84, 'guestready_url_casa1', 'https://book.guestready.com/pt/properties/mogadouro/fuga-ecletica-em-mogadouro/72622', 'url', 'reservations', 'Link GuestReady - Casa do Gi 1', 0, '2026-08-14 21:48:24', '2026-08-28 17:47:25'),
(85, 'guestready_url_casa2', 'https://book.guestready.com/pt/properties/mogadouro/refugio-acolhedor-ecletico-em-mogadouro/72624', 'url', 'reservations', 'Link GuestReady - Casa do Gi 2', 0, '2026-08-14 21:48:24', '2026-08-28 17:47:43'),
(86, 'google_maps_url', '', 'text', 'contact', 'Google Maps URL', 0, '2026-08-28 00:30:24', '2026-08-28 00:30:24'),
(87, 'site_description_pt', 'A Casa do Gi: O seu refúgio de Alojamento Local em Mogadouro. Reserve uma casa de férias exclusiva com lareira, conforto e paz no coração de Trás-os-Montes.', 'textarea', 'general', 'Descrição SEO (PT)', 0, '2026-08-28 17:09:08', '2026-08-28 17:24:22'),
(88, 'site_description_en', 'A Casa do Gi: Your perfect Local Accommodation in Mogadouro. Book an exclusive holiday home with fireplace, comfort and peace in the heart of Trás-os-Montes.', 'textarea', 'general', 'Descrição SEO (EN)', 0, '2026-08-28 17:09:08', '2026-08-28 17:24:22');

-- --------------------------------------------------------

--
-- Estrutura stand-in para vista `v_hero_media`
-- (Veja abaixo para a view atual)
--
CREATE TABLE `v_hero_media` (
`id` int(10) unsigned
,`filename` varchar(255)
,`original_name` varchar(255)
,`file_path` varchar(500)
,`file_type` varchar(50)
,`file_size` int(10) unsigned
,`alt_text_pt` varchar(255)
,`alt_text_en` varchar(255)
,`caption_pt` varchar(500)
,`caption_en` varchar(500)
,`category` enum('gallery','content','cover','hero','other')
,`entity_type` enum('hero','accommodation','standalone','other')
,`entity_id` int(10) unsigned
,`is_cover` tinyint(1)
,`sort_order` int(10) unsigned
,`uploaded_by` int(10) unsigned
,`created_at` timestamp
,`accommodation_id` int(10) unsigned
,`page_key` varchar(50)
,`is_active` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estrutura para vista `v_hero_media`
--
DROP TABLE IF EXISTS `v_hero_media`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_hero_media`  AS SELECT `m`.`id` AS `id`, `m`.`filename` AS `filename`, `m`.`original_name` AS `original_name`, `m`.`file_path` AS `file_path`, `m`.`file_type` AS `file_type`, `m`.`file_size` AS `file_size`, `m`.`alt_text_pt` AS `alt_text_pt`, `m`.`alt_text_en` AS `alt_text_en`, `m`.`caption_pt` AS `caption_pt`, `m`.`caption_en` AS `caption_en`, `m`.`category` AS `category`, `m`.`entity_type` AS `entity_type`, `m`.`entity_id` AS `entity_id`, `m`.`is_cover` AS `is_cover`, `m`.`sort_order` AS `sort_order`, `m`.`uploaded_by` AS `uploaded_by`, `m`.`created_at` AS `created_at`, `m`.`accommodation_id` AS `accommodation_id`, `ph`.`page_key` AS `page_key`, `ph`.`is_active` AS `is_active` FROM (`media` `m` join `page_heroes` `ph` on(`m`.`entity_id` = `ph`.`id`)) WHERE `m`.`entity_type` = 'hero' ;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `accommodation`
--
ALTER TABLE `accommodation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_accommodation_number` (`accommodation_number`);

--
-- Índices para tabela `accommodation_amenities`
--
ALTER TABLE `accommodation_amenities`
  ADD PRIMARY KEY (`accommodation_id`,`amenity_id`),
  ADD KEY `amenity_id` (`amenity_id`);

--
-- Índices para tabela `accommodation_translations`
--
ALTER TABLE `accommodation_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_acc_lang` (`accommodation_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Índices para tabela `activity_links`
--
ALTER TABLE `activity_links`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_active` (`is_active`);

--
-- Índices para tabela `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `amenity_translations`
--
ALTER TABLE `amenity_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_amenity_lang` (`amenity_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Índices para tabela `bathrooms`
--
ALTER TABLE `bathrooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accommodation_id` (`accommodation_id`);

--
-- Índices para tabela `bathroom_translations`
--
ALTER TABLE `bathroom_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_bathroom_lang` (`bathroom_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Índices para tabela `bedrooms`
--
ALTER TABLE `bedrooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accommodation_id` (`accommodation_id`);

--
-- Índices para tabela `bedroom_translations`
--
ALTER TABLE `bedroom_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_bedroom_lang` (`bedroom_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Índices para tabela `contact_submissions`
--
ALTER TABLE `contact_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_read` (`is_read`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_ignored` (`is_ignored`);

--
-- Índices para tabela `content_blocks`
--
ALTER TABLE `content_blocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_block_lang` (`block_key`,`language_id`),
  ADD KEY `language_id` (`language_id`),
  ADD KEY `idx_block_key` (`block_key`),
  ADD KEY `idx_page` (`page`),
  ADD KEY `idx_section` (`section`);

--
-- Índices para tabela `house_rules`
--
ALTER TABLE `house_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_accommodation` (`accommodation_id`),
  ADD KEY `idx_highlighted` (`is_highlighted`);

--
-- Índices para tabela `house_rule_translations`
--
ALTER TABLE `house_rule_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rule_lang` (`rule_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Índices para tabela `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_active` (`is_active`);

--
-- Índices para tabela `legal_sections`
--
ALTER TABLE `legal_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_page` (`page`),
  ADD KEY `idx_active` (`is_active`);

--
-- Índices para tabela `legal_section_translations`
--
ALTER TABLE `legal_section_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_legal_lang` (`section_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Índices para tabela `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_sort` (`sort_order`),
  ADD KEY `idx_media_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_media_cover` (`is_cover`);

--
-- Índices para tabela `page_heroes`
--
ALTER TABLE `page_heroes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_key` (`page_key`),
  ADD KEY `idx_page_key` (`page_key`),
  ADD KEY `idx_active` (`is_active`);

--
-- Índices para tabela `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_key` (`setting_key`),
  ADD KEY `idx_group` (`setting_group`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `accommodation`
--
ALTER TABLE `accommodation`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `accommodation_translations`
--
ALTER TABLE `accommodation_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `activity_links`
--
ALTER TABLE `activity_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `amenities`
--
ALTER TABLE `amenities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de tabela `amenity_translations`
--
ALTER TABLE `amenity_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT de tabela `bathrooms`
--
ALTER TABLE `bathrooms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `bathroom_translations`
--
ALTER TABLE `bathroom_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de tabela `bedrooms`
--
ALTER TABLE `bedrooms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `bedroom_translations`
--
ALTER TABLE `bedroom_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de tabela `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `content_blocks`
--
ALTER TABLE `content_blocks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=497;

--
-- AUTO_INCREMENT de tabela `house_rules`
--
ALTER TABLE `house_rules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de tabela `house_rule_translations`
--
ALTER TABLE `house_rule_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de tabela `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `legal_sections`
--
ALTER TABLE `legal_sections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de tabela `legal_section_translations`
--
ALTER TABLE `legal_section_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de tabela `media`
--
ALTER TABLE `media`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;

--
-- AUTO_INCREMENT de tabela `page_heroes`
--
ALTER TABLE `page_heroes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `accommodation_amenities`
--
ALTER TABLE `accommodation_amenities`
  ADD CONSTRAINT `accommodation_amenities_ibfk_1` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accommodation_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `accommodation_translations`
--
ALTER TABLE `accommodation_translations`
  ADD CONSTRAINT `accommodation_translations_ibfk_1` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accommodation_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `amenity_translations`
--
ALTER TABLE `amenity_translations`
  ADD CONSTRAINT `amenity_translations_ibfk_1` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `amenity_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `bathrooms`
--
ALTER TABLE `bathrooms`
  ADD CONSTRAINT `bathrooms_ibfk_1` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodation` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `bathroom_translations`
--
ALTER TABLE `bathroom_translations`
  ADD CONSTRAINT `bathroom_translations_ibfk_1` FOREIGN KEY (`bathroom_id`) REFERENCES `bathrooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bathroom_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `bedrooms`
--
ALTER TABLE `bedrooms`
  ADD CONSTRAINT `bedrooms_ibfk_1` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodation` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `bedroom_translations`
--
ALTER TABLE `bedroom_translations`
  ADD CONSTRAINT `bedroom_translations_ibfk_1` FOREIGN KEY (`bedroom_id`) REFERENCES `bedrooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bedroom_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `content_blocks`
--
ALTER TABLE `content_blocks`
  ADD CONSTRAINT `content_blocks_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `house_rules`
--
ALTER TABLE `house_rules`
  ADD CONSTRAINT `house_rules_ibfk_1` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodation` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `house_rule_translations`
--
ALTER TABLE `house_rule_translations`
  ADD CONSTRAINT `house_rule_translations_ibfk_1` FOREIGN KEY (`rule_id`) REFERENCES `house_rules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `house_rule_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `legal_section_translations`
--
ALTER TABLE `legal_section_translations`
  ADD CONSTRAINT `legal_section_translations_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `legal_sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `legal_section_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
