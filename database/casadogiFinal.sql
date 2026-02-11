-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 11-Fev-2026 às 04:11
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
-- Banco de dados: `casadogi`
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
  `airbnb_url` varchar(500) DEFAULT NULL COMMENT 'Airbnb URL',
  `hero_image` varchar(500) DEFAULT NULL COMMENT 'Hero image for this accommodation',
  `cover_image` varchar(500) DEFAULT NULL COMMENT 'Cover image for selection cards on main page'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `accommodation`
--

INSERT INTO `accommodation` (`id`, `slug`, `max_guests`, `bedrooms`, `bathrooms`, `area_sqm`, `floor_number`, `has_elevator`, `check_in_time`, `check_out_time`, `latitude`, `longitude`, `license_number`, `created_at`, `updated_at`, `is_active`, `rating`, `reviews_count`, `city`, `region`, `country`, `host_type`, `checkin_type`, `checkin_instructions`, `towels_linens_included`, `min_nights`, `instant_booking`, `accommodation_number`, `guestready_url`, `booking_url`, `airbnb_url`, `hero_image`, `cover_image`) VALUES
(1, 'casa-do-gi-1', 6, 3, 2, 100.00, 1, 0, '16:00:00', '11:00:00', 41.34217000, -6.71347000, '146729/AL', '2026-01-19 12:51:19', '2026-02-10 18:18:43', 1, NULL, 0, 'Mogadouro', 'Tras-os-Montes', 'Portugal', 'standard', 'self_checkin', NULL, 1, 1, 0, 1, '', '', '', 'images/MogadouroAlojamento.jpg', 'images/IgrejaMatriz.jpg'),
(2, 'casa-do-gi-2', 6, 3, 2, 100.00, 1, 0, '16:00:00', '11:00:00', 41.34217000, -6.71347000, '146729/AL', '2026-01-30 02:22:49', '2026-02-10 18:18:43', 1, NULL, 0, 'Mogadouro', 'Tras-os-Montes', 'Portugal', 'standard', 'self_checkin', NULL, 1, 1, 0, 2, '', '', '', 'uploads/accommodation/hero_casa2_1770084834.jpeg', 'images/Castelo.jpg');

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
(1, 10, 0, 6),
(1, 11, 0, 9),
(1, 12, 0, 5),
(1, 13, 0, 7),
(1, 23, 0, 8),
(1, 26, 0, 10),
(1, 27, 0, 15),
(1, 28, 0, 16),
(1, 29, 0, 17),
(1, 30, 0, 18),
(1, 35, 0, 11),
(1, 36, 0, 12),
(1, 37, 0, 13),
(1, 38, 0, 14);

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
(1, 1, 1, 'A Casa do Gi', 'Casa de ferias de 100m2, andar nr 1, sem elevador', 'A Casa do Gi e sinonimo de simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor! Construida nos anos 80, altura em que os artistas da construcao e os materiais eram escassos por Terras de Mogadouro.', NULL, 'A Casa do Gi', 'Simplicidade, acolhimento e muito amor', 'A Casa do Gi e sinonimo de simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor! Construida nos anos 80, altura em que os artistas da construcao e os materiais eram escassos por Terras de Mogadouro.', '', 'wewewee', '', '', 'wewew', 'Mogadouro & Envolvência', ''),
(2, 1, 2, 'A Casa do Gi', 'Holiday home of 100m2, 1st floor, no elevator', 'A Casa do Gi is synonymous with simplicity, welcoming, remarkable moments of conviviality, warmth of family, joy, fun, laughter and a lot of love! Built in the 80s, when construction artists and materials were scarce in the lands of Mogadouro.', NULL, 'A Casa do Gi', 'Simplicity, warmth and love', 'A Casa do Gi is synonymous with simplicity, welcoming, remarkable moments of conviviality, warmth of family, joy, fun, laughter and a lot of love! Built in the 80s, when construction artists and materials were scarce in the lands of Mogadouro.', '', 'wewewe', '', '', '2wwewe', '', ''),
(3, 2, 1, '', NULL, NULL, NULL, 'A Casa do Gi 2', 'Simplicidade, acolhimento e muito amor', 'A Casa do Gi e sinonimo de simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor! Construida nos anos 80, altura em que os artistas da construcao e os materiais eram escassos por Terras de Mogadouro.', '', '', '', '', '', '', ''),
(4, 2, 2, '', NULL, NULL, NULL, 'A Casa do Gi 2', 'Simplicity, warmth and love', 'A Casa do Gi is synonymous with simplicity, welcoming, remarkable moments of conviviality, warmth of family, joy, fun, laughter and a lot of love! Built in the 80s, when construction artists and materials were scarce in the lands of Mogadouro.', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Estrutura da tabela `activities`
--

CREATE TABLE `activities` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `category` enum('nature','culture','gastronomy','adventure','wellness','events','restaurants','cafes','accommodation','architecture','rural_tourism','leisure') DEFAULT 'culture',
  `external_url` varchar(500) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL COMMENT 'Full address of the location',
  `phone` varchar(50) DEFAULT NULL COMMENT 'Contact phone number',
  `website` varchar(500) DEFAULT NULL COMMENT 'Official website URL',
  `email` varchar(255) DEFAULT NULL COMMENT 'Contact email',
  `opening_hours` text DEFAULT NULL COMMENT 'Opening hours JSON or text',
  `price_range` enum('free','budget','moderate','expensive') DEFAULT NULL COMMENT 'Price range indicator',
  `google_maps_embed` text DEFAULT NULL COMMENT 'Google Maps iframe embed code',
  `meta_title` varchar(255) DEFAULT NULL COMMENT 'SEO meta title',
  `meta_description` text DEFAULT NULL COMMENT 'SEO meta description',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `distance_km` decimal(5,2) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `views_count` int(10) UNSIGNED DEFAULT 0 COMMENT 'Number of page views',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `activities`
--

INSERT INTO `activities` (`id`, `category_id`, `slug`, `category`, `external_url`, `address`, `phone`, `website`, `email`, `opening_hours`, `price_range`, `google_maps_embed`, `meta_title`, `meta_description`, `latitude`, `longitude`, `distance_km`, `is_featured`, `is_active`, `sort_order`, `views_count`, `created_at`, `updated_at`) VALUES
(1, 8, 'castelo-mogadouro', 'culture', '', 'Largo do Castelo, 5200-251 Mogadouro', '', '', '', NULL, 'free', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3010.1234567890123!2d-6.7134700!3d41.3421700!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDHCsDIwJzMxLjgiTiA2wrA0MicxMC4yIlc!5e0!3m2!1spt-PT!2spt!4v1234567890123!5m2!1spt-PT!2spt\" width=\"100%\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\"></iframe>', NULL, NULL, 41.34217000, -6.71347000, 0.50, 1, 1, 1, 45, '2026-01-19 12:51:20', '2026-02-11 02:31:41'),
(2, 7, 'miradouro-serpente-medal', 'nature', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15.00, 1, 1, 2, 3, '2026-01-19 12:51:20', '2026-02-09 13:59:44'),
(4, 8, 'museu-mogadouro', 'culture', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.30, 0, 1, 4, 0, '2026-01-19 12:51:20', '2026-02-06 22:04:33'),
(5, 8, 'igreja-matriz', 'culture', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.20, 0, 1, 5, 0, '2026-01-19 12:51:20', '2026-02-06 22:04:33'),
(6, 10, 'restaurante-a-lareira', 'restaurants', '', 'Av. Nossa Senhora do Caminho, 5200-207 Mogadouro', '', '', '', NULL, 'moderate', '', NULL, NULL, NULL, NULL, 0.10, 0, 1, 10, 3, '2026-02-06 14:42:31', '2026-02-07 01:29:09'),
(8, 14, 'feira-medieval-mogadouro', 'events', '', 'Centro Histórico, 5200-207 Mogadouro', '', '', '', NULL, 'free', '', NULL, NULL, NULL, NULL, 0.00, 0, 1, 12, 0, '2026-02-06 14:42:31', '2026-02-06 22:04:33'),
(9, 12, 'convento-sao-francisco', 'architecture', '', 'Largo de São Francisco, 5200-207 Mogadouro', '', '', '', NULL, 'free', '', NULL, NULL, NULL, NULL, 0.30, 0, 1, 13, 1, '2026-02-06 14:42:31', '2026-02-06 22:04:33'),
(10, 8, 'praca-do-municipio', 'culture', NULL, 'Praça do Município, 5200-207 Mogadouro', NULL, NULL, NULL, NULL, 'free', NULL, NULL, NULL, NULL, NULL, 0.00, 0, 1, 14, 6, '2026-02-06 14:42:31', '2026-02-07 01:48:03'),
(13, 7, 'testar', 'nature', '', '', '', '', '', NULL, 'free', '', NULL, NULL, NULL, NULL, NULL, 0, 1, 0, 2, '2026-02-06 18:32:57', '2026-02-07 01:23:26');

-- --------------------------------------------------------

--
-- Estrutura da tabela `activity_translations`
--

CREATE TABLE `activity_translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `activity_id` int(10) UNSIGNED NOT NULL,
  `language_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `short_description` text DEFAULT NULL,
  `full_description` text DEFAULT NULL,
  `address_description` varchar(500) DEFAULT NULL COMMENT 'Localized address/directions description',
  `opening_hours_text` text DEFAULT NULL COMMENT 'Localized opening hours text',
  `tips` text DEFAULT NULL COMMENT 'Local tips and recommendations',
  `meta_title` varchar(255) DEFAULT NULL COMMENT 'Localized SEO title',
  `meta_description` text DEFAULT NULL COMMENT 'Localized SEO description'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `activity_translations`
--

INSERT INTO `activity_translations` (`id`, `activity_id`, `language_id`, `title`, `short_description`, `full_description`, `address_description`, `opening_hours_text`, `tips`, `meta_title`, `meta_description`) VALUES
(1, 1, 1, 'Castelo de Mogadouro', 'Castelo do seculo XIII com vista panoramica da regiao', '<p>O Castelo de Mogadouro, também conhecido como Torre de Menagem, é um dos monumentos mais emblemáticos do concelho. Construído no século XIII pelos Templários, esta torre medieval ergue-se majestosamente sobre a vila, oferecendo vistas panorâmicas deslumbrantes sobre a paisagem transmontana.</p><p>A torre, de planta quadrada, é o único elemento que resta do antigo castelo medieval que protegia a povoação. As suas paredes robustas contam histórias de batalhas e conquistas que moldaram a história desta região fronteiriça.</p><p>Visitar o Castelo de Mogadouro é fazer uma viagem no tempo, descobrindo os segredos da Ordem dos Templários e a importância estratégica que esta fortaleza teve na defesa do território português.</p>', NULL, NULL, 'Visite ao final da tarde para apreciar o pôr do sol sobre as montanhas. A entrada é gratuita e o local é acessível durante todo o ano.', NULL, NULL),
(2, 1, 2, 'Mogadouro Castle', '13th century castle with panoramic views of the region', '<p>Mogadouro Castle, also known as the Keep Tower, is one of the most emblematic monuments in the municipality. Built in the 13th century by the Templars, this medieval tower rises majestically over the village, offering stunning panoramic views over the Transmontana landscape.</p><p>The square-plan tower is the only remaining element of the old medieval castle that protected the settlement. Its robust walls tell stories of battles and conquests that shaped the history of this border region.</p><p>Visiting Mogadouro Castle is a journey through time, discovering the secrets of the Order of the Templars and the strategic importance this fortress had in the defense of Portuguese territory.</p>', NULL, NULL, 'Visit in the late afternoon to enjoy the sunset over the mountains. Admission is free and the site is accessible year-round.', NULL, NULL),
(3, 2, 1, 'Miradouro Serpente do Medal', 'Vista panoramica sobre o rio Douro nas Arribas', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 2, 2, 'Serpente do Medal Viewpoint', 'Panoramic view over the Douro river in the Arribas', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 4, 1, 'Museu de Mogadouro', 'Historia e tradicoes da regiao', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 4, 2, 'Mogadouro Museum', 'History and traditions of the region', NULL, NULL, NULL, NULL, NULL, NULL),
(9, 5, 1, 'Igreja Matriz de Mogadouro', 'Igreja de origem romanica no centro historico', NULL, NULL, NULL, NULL, NULL, NULL),
(10, 5, 2, 'Mogadouro Main Church', 'Romanesque origin church in the historic center', NULL, NULL, NULL, NULL, NULL, NULL),
(11, 6, 1, 'Restaurante A Lareira', 'Cozinha tradicional transmontana com pratos típicos da região.', '<p>O Restaurante A Lareira é uma referência da gastronomia transmontana em Mogadouro. Com um ambiente acolhedor e rústico, oferece os melhores pratos da região, preparados com ingredientes locais de qualidade.</p><p>Especialidades da casa incluem a famosa posta mirandesa, cabrito assado, enchidos tradicionais e o delicioso folar de carne. A carta de vinhos apresenta uma seleção cuidada de vinhos regionais do Douro.</p>', NULL, NULL, '', NULL, NULL),
(13, 8, 1, 'Feira Medieval de Mogadouro', 'Evento anual que recria a época medieval com mercado, espetáculos e gastronomia.', '<p>A Feira Medieval de Mogadouro é um dos eventos mais aguardados do ano. Durante três dias, o centro histórico transforma-se num autêntico mercado medieval, com artesãos, músicos, malabaristas e espetáculos de falcoaria.</p><p>Prove as iguarias medievais, assista a torneios de cavaleiros e mergulhe na atmosfera única desta festa que celebra a rica história templária de Mogadouro.</p>', NULL, NULL, '', NULL, NULL),
(14, 9, 1, 'Convento de São Francisco', 'Antigo convento franciscano do século XIII com arquitetura gótica notável.', '<p>O Convento de São Francisco, fundado no século XIII, é um dos mais importantes monumentos religiosos de Mogadouro. A sua igreja preserva elementos arquitectónicos góticos e manuelinos de grande valor histórico.</p><p>Destaque para os azulejos do século XVIII que decoram o interior e para o claustro sereno que convida à contemplação.</p>', NULL, NULL, '', NULL, NULL),
(15, 10, 1, 'Praça do Município', 'Centro nevrálgico de Mogadouro com esplanadas e comércio tradicional.', '<p>A Praça do Município é o coração de Mogadouro. Rodeada de edifícios históricos, é o local ideal para sentir o pulso da vila, tomar um café numa esplanada ou simplesmente observar o quotidiano transmontano.</p>', NULL, NULL, NULL, NULL, NULL),
(18, 6, 2, 'A Lareira Restaurant', 'Traditional Transmontana cuisine with typical regional dishes.', '<p>A Lareira Restaurant is a reference for Transmontana gastronomy in Mogadouro. With a cozy and rustic atmosphere, it offers the best dishes from the region, prepared with quality local ingredients.</p><p>House specialties include the famous Mirandesa steak, roasted kid, traditional sausages and the delicious meat folar. The wine list features a careful selection of regional Douro wines.</p>', NULL, NULL, '', NULL, NULL),
(20, 8, 2, 'Mogadouro Medieval Fair', 'Annual event recreating the medieval era with market, shows and gastronomy.', '<p>The Mogadouro Medieval Fair is one of the most anticipated events of the year. For three days, the historic center transforms into an authentic medieval market, with artisans, musicians, jugglers and falconry shows.</p><p>Taste medieval delicacies, watch knight tournaments and immerse yourself in the unique atmosphere of this festival that celebrates Mogadouro\'s rich Templar history.</p>', NULL, NULL, '', NULL, NULL),
(21, 9, 2, 'São Francisco Convent', 'Former 13th century Franciscan convent with notable Gothic architecture.', '<p>The São Francisco Convent, founded in the 13th century, is one of Mogadouro\'s most important religious monuments. Its church preserves Gothic and Manueline architectural elements of great historical value.</p><p>Highlights include the 18th century tiles that decorate the interior and the serene cloister that invites contemplation.</p>', NULL, NULL, '', NULL, NULL),
(22, 10, 2, 'Municipality Square', 'Mogadouro\'s nerve center with terraces and traditional commerce.', '<p>The Municipality Square is the heart of Mogadouro. Surrounded by historic buildings, it\'s the ideal place to feel the pulse of the village, have a coffee on a terrace or simply observe the Transmontana daily life.</p>', NULL, NULL, NULL, NULL, NULL),
(25, 13, 1, 'Testar esta cena', 'Nada', 'Nada completo', NULL, NULL, 'Nada para te dizer', NULL, NULL),
(26, 13, 2, 'Testing this thing', 'Nothing', 'Nothing complete', NULL, NULL, 'Nothing to say to you', NULL, NULL);

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
(1, 'admin', 'admin@acasadogi.pt', '$2y$12$95JaIzBEov7tZz0SfnUwPOIecK1ujIWqumI74Ndw.e2RHwm/FpVqy', 'Administrador', 'super_admin', NULL, 1, '2026-02-11 02:57:46', 0, NULL, NULL, NULL, '2026-01-19 12:51:19', '2026-02-11 02:57:46');

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
-- Estrutura da tabela `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` int(10) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `audit_log`
--

INSERT INTO `audit_log` (`id`, `admin_id`, `action`, `entity_type`, `entity_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-20 16:48:10'),
(2, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-22 21:34:11'),
(3, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-22 21:34:57'),
(4, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-23 21:38:13'),
(5, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-24 14:01:53'),
(6, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-24 19:03:04'),
(7, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-24 19:13:07'),
(8, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-25 16:54:40'),
(9, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-25 19:54:01'),
(10, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-25 19:54:52'),
(11, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-25 22:16:08'),
(12, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-25 22:22:27'),
(13, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 19:33:49'),
(14, 1, 'logout', 'admin', 1, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 22:30:44'),
(15, 1, 'logout', 'admin', 1, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 22:30:59'),
(16, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-26 22:31:08'),
(17, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 13:26:18'),
(18, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 18:50:15'),
(19, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 02:23:24'),
(20, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 19:01:50'),
(21, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 17:40:12'),
(22, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 21:17:58'),
(23, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 15:14:01'),
(24, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-06 18:29:25'),
(25, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:03:54'),
(26, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 20:51:43'),
(27, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 15:54:22'),
(28, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 15:55:27'),
(29, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 15:56:10'),
(30, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 16:01:55'),
(31, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:03:53'),
(32, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:03:53'),
(33, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:03:59'),
(34, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:03:59'),
(35, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:04:05'),
(36, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:04:06'),
(37, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:04:13'),
(38, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:04:13'),
(39, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:05:19'),
(40, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:05:19'),
(41, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:05:32'),
(42, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:05:32'),
(43, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:13:05'),
(44, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:14:39'),
(45, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:33:41'),
(46, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:35:44'),
(47, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:50:02'),
(48, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:50:46'),
(49, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:50:53'),
(50, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:56:21'),
(51, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 19:59:45'),
(52, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 20:04:20'),
(53, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 20:29:02'),
(54, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 20:30:24'),
(55, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 20:34:12'),
(56, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 20:38:10'),
(57, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 20:45:38'),
(58, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 20:46:55'),
(59, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 21:36:42'),
(60, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 21:39:40'),
(61, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 21:45:05'),
(62, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 21:47:47'),
(63, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 21:51:51'),
(64, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 21:53:04'),
(65, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 21:54:44'),
(66, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 21:56:29'),
(67, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 21:56:48'),
(68, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 21:58:49'),
(69, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:00:20'),
(70, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:01:42'),
(71, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 23:41:57'),
(72, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 23:43:05'),
(73, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 00:08:21'),
(74, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 00:15:47'),
(75, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 01:09:36'),
(76, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 01:25:47'),
(77, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 01:27:26'),
(78, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 01:28:35'),
(79, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 02:03:40'),
(80, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 02:20:50'),
(81, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 02:21:22'),
(82, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 15:32:52'),
(83, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 15:34:26'),
(84, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 16:11:15'),
(85, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 16:15:39'),
(86, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 16:17:18'),
(87, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 16:18:26'),
(88, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 16:18:26'),
(89, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 16:24:31'),
(90, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 16:27:55'),
(91, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 16:32:50'),
(92, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 16:37:51'),
(93, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 16:46:45'),
(94, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 16:49:35'),
(95, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 16:52:03'),
(96, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 16:54:59'),
(97, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 16:56:33'),
(98, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 17:00:07'),
(99, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 17:00:33'),
(100, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 17:04:29'),
(101, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 17:06:15'),
(102, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 17:07:29'),
(103, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 17:07:46'),
(104, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 17:11:38'),
(105, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 17:12:47'),
(106, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 17:14:08'),
(107, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 17:32:30'),
(108, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 17:38:07'),
(109, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 17:38:26'),
(110, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 17:39:55'),
(111, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 17:47:17'),
(112, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 17:51:53'),
(113, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 17:55:06'),
(114, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 17:58:25'),
(115, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 18:05:42'),
(116, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 18:07:49'),
(117, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 18:10:43'),
(118, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 18:13:18'),
(119, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 18:13:50'),
(120, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 18:15:06'),
(121, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 18:23:35'),
(122, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 18:28:15'),
(123, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 18:30:38'),
(124, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 18:32:30'),
(125, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 18:56:59'),
(126, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 19:00:26'),
(127, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 19:00:57'),
(128, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 19:11:52'),
(129, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 19:29:07'),
(130, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 19:31:03'),
(131, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 19:31:15'),
(132, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 19:32:56'),
(133, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 19:36:25'),
(134, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 19:39:22'),
(135, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 20:58:07'),
(136, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 20:59:21'),
(137, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 21:01:49'),
(138, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 21:03:50'),
(139, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 21:09:23'),
(140, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 21:09:26'),
(141, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 21:11:20'),
(142, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 21:12:22'),
(143, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 21:27:24'),
(144, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 21:30:27'),
(145, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 21:35:24'),
(146, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 21:36:47'),
(147, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 21:38:09'),
(148, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 21:42:29'),
(149, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Linux; Android 13; SM-G981B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-11 00:46:22'),
(150, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 00:47:52'),
(151, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 02:38:29'),
(152, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 02:40:19'),
(153, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 02:44:35'),
(154, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 02:45:44'),
(155, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 02:57:46'),
(156, 1, 'logout', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 02:59:04');

-- --------------------------------------------------------

--
-- Estrutura da tabela `barcode_batches`
--

CREATE TABLE `barcode_batches` (
  `id` int(10) UNSIGNED NOT NULL,
  `batch_number` int(10) UNSIGNED NOT NULL,
  `codes_used` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `max_codes` int(10) UNSIGNED NOT NULL DEFAULT 999999999,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `barcode_batches`
--

INSERT INTO `barcode_batches` (`id`, `batch_number`, `codes_used`, `max_codes`, `is_active`, `created_at`) VALUES
(1, 1, 0, 999999999, 1, '2026-02-07 20:01:43');

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
(9, 1, 1, 0, 1, 1, 1, '2026-02-10 18:23:27', NULL),
(10, 1, 2, 0, 1, 0, 0, '2026-02-10 18:23:27', NULL),
(11, 2, 1, 0, 1, 1, 1, '2026-02-10 18:23:27', NULL),
(12, 2, 2, 0, 1, 0, 0, '2026-02-10 18:23:27', NULL);

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
(17, 9, 1, 'Casa de Banho Principal', 'Banheira, chuveiro, bide, secador de cabelo', NULL),
(18, 9, 2, 'Main Bathroom', 'Bathtub, shower, bidet, hair dryer', NULL),
(19, 10, 1, 'Casa de Banho Secundaria', 'Chuveiro, lavatorio', NULL),
(20, 10, 2, 'Secondary Bathroom', 'Shower, sink', NULL),
(21, 11, 1, 'Casa de Banho Principal', 'Banheira, chuveiro, bide, secador de cabelo', NULL),
(22, 11, 2, 'Main Bathroom', 'Bathtub, shower, bidet, hair dryer', NULL),
(23, 12, 1, 'Casa de Banho Secundaria', 'Chuveiro, lavatorio', NULL),
(24, 12, 2, 'Secondary Bathroom', 'Shower, sink', NULL);

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
(13, 1, 1, '2026-02-10 18:23:26', NULL),
(14, 1, 2, '2026-02-10 18:23:26', NULL),
(15, 1, 3, '2026-02-10 18:23:26', NULL),
(16, 2, 1, '2026-02-10 18:23:27', NULL),
(17, 2, 2, '2026-02-10 18:23:27', NULL),
(18, 2, 3, '2026-02-10 18:23:27', NULL);

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
(25, 13, 1, '2 camas de solteiro', 'Quarto Principal', NULL),
(26, 13, 2, '2 single beds', 'Master Bedroom', NULL),
(27, 14, 1, 'Sofa-cama de solteiro, Cama de casal', 'Quarto Duplo', NULL),
(28, 14, 2, 'Single sofa bed, Double bed', 'Twin Room', NULL),
(29, 15, 1, 'Cama de casal', 'Quarto de Hospedes', NULL),
(30, 15, 2, 'Double bed', 'Guest Room', NULL),
(31, 16, 1, '2 camas de solteiro', 'Quarto Principal', NULL),
(32, 16, 2, '2 single beds', 'Master Bedroom', NULL),
(33, 17, 1, 'Sofa-cama de solteiro, Cama de casal', 'Quarto Duplo', NULL),
(34, 17, 2, 'Single sofa bed, Double bed', 'Twin Room', NULL),
(35, 18, 1, 'Cama de casal', 'Quarto de Hospedes', NULL),
(36, 18, 2, 'Double bed', 'Guest Room', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` enum('activity','product') NOT NULL DEFAULT 'product',
  `slug` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL COMMENT 'Icon identifier for activities',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `categories`
--

INSERT INTO `categories` (`id`, `type`, `slug`, `icon`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'product', 'mel', NULL, 1, 1, '2026-01-19 12:51:19', '2026-02-06 22:01:32'),
(2, 'product', 'azeite', NULL, 2, 1, '2026-01-19 12:51:19', '2026-02-06 22:01:32'),
(3, 'product', 'vinho', NULL, 3, 1, '2026-01-19 12:51:19', '2026-02-06 22:01:32'),
(4, 'product', 'enchidos', NULL, 4, 1, '2026-01-19 12:51:19', '2026-02-06 22:01:32'),
(5, 'product', 'queijos', NULL, 5, 1, '2026-01-19 12:51:19', '2026-02-06 22:01:32'),
(6, 'product', 'doces', NULL, 6, 1, '2026-01-19 12:51:19', '2026-02-06 22:01:32'),
(7, 'activity', 'nature', 'tree', 1, 1, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(8, 'activity', 'culture', 'building', 2, 1, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(9, 'activity', 'gastronomy', 'utensils', 3, 1, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(10, 'activity', 'restaurants', 'utensils', 4, 1, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(12, 'activity', 'architecture', 'building', 6, 1, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(13, 'activity', 'adventure', 'mountain', 7, 1, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(14, 'activity', 'events', 'calendar', 8, 1, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(15, 'activity', 'wellness', 'spa', 9, 1, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(16, 'activity', 'rural_tourism', 'tractor', 10, 1, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(17, 'activity', 'leisure', 'gamepad', 11, 1, '2026-02-06 22:03:51', '2026-02-06 22:03:51');

-- --------------------------------------------------------

--
-- Estrutura da tabela `category_translations`
--

CREATE TABLE `category_translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `language_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `category_translations`
--

INSERT INTO `category_translations` (`id`, `category_id`, `language_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Mel', 'Mel da regiao de Tras-os-Montes', '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(2, 1, 2, 'Honey', 'Honey from Tras-os-Montes region', '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(3, 2, 1, 'Azeite', 'Azeite do vale do Sabor', '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(4, 2, 2, 'Olive Oil', 'Olive oil from Sabor valley', '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(5, 3, 1, 'Vinho', 'Vinhos da regiao do Douro', '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(6, 3, 2, 'Wine', 'Wines from Douro region', '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(7, 4, 1, 'Enchidos', 'Enchidos tradicionais transmontanos', '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(8, 4, 2, 'Cured Meats', 'Traditional Transmontano cured meats', '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(9, 5, 1, 'Queijos', 'Queijos de ovelha e cabra', '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(10, 5, 2, 'Cheeses', 'Sheep and goat cheeses', '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(11, 6, 1, 'Doces', 'Doces e bolos tradicionais', '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(12, 6, 2, 'Sweets', 'Traditional sweets and cakes', '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(16, 7, 1, 'Natureza', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(17, 8, 1, 'Cultura', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(18, 9, 1, 'Gastronomia', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(19, 10, 1, 'Restaurantes', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(21, 12, 1, 'Arquitetura', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(22, 13, 1, 'Aventura', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(23, 14, 1, 'Eventos', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(24, 15, 1, 'Bem-estar', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(25, 16, 1, 'Turismo Rural', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(26, 17, 1, 'Lazer', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(31, 7, 2, 'Nature', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(32, 8, 2, 'Culture', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(33, 9, 2, 'Gastronomy', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(34, 10, 2, 'Restaurants', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(36, 12, 2, 'Architecture', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(37, 13, 2, 'Adventure', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(38, 14, 2, 'Events', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(39, 15, 2, 'Wellness', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(40, 16, 2, 'Rural Tourism', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51'),
(41, 17, 2, 'Leisure', NULL, '2026-02-06 22:03:51', '2026-02-06 22:03:51');

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
(2, 'Guilherme Marques', 'guilherme.jcmarques@gmail.com', '999323876', 'Testar sistema', 'Teste do Sistema de Formulário de Mensagens.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'pt', 1, 0, 0, '2026-02-07 01:34:24');

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
(7, 'accommodation_intro', 1, 'textarea', 'Ambas as casas oferecem o mesmo conforto e hospitalidade transmontana. Escolha a que melhor se adapta a sua estadia.', 'accommodation', 'main', '2026-01-19 12:51:19', '2026-02-09 20:00:28'),
(8, 'shop_title', 1, 'text', 'Produtos Regionais', 'shop', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(9, 'shop_intro', 1, 'textarea', 'Sabores autenticos de Tras-os-Montes, selecionados com carinho para a sua mesa.', 'shop', 'main', '2026-01-19 12:51:19', '2026-02-09 19:52:45'),
(10, 'activities_title', 1, 'text', 'O Que Fazer', 'activities', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(11, 'activities_intro', 1, 'textarea', 'Descubra as maravilhas de Mogadouro e arredores', 'activities', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(12, 'contact_title', 1, 'text', 'Contacte-nos', 'contact', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(13, 'contact_intro', 1, 'textarea', 'Tem alguma questao? Entre em contacto connosco', 'contact', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(19, 'accommodation_title', 2, 'text', 'The Accommodation', 'accommodation', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(20, 'accommodation_intro', 2, 'textarea', 'Both houses offer the same comfort and Transmontana hospitality. Choose the one that best suits your stay.', 'accommodation', 'main', '2026-01-19 12:51:19', '2026-02-09 20:00:28'),
(21, 'shop_title', 2, 'text', 'Regional Products', 'shop', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(22, 'shop_intro', 2, 'textarea', 'Authentic flavors from Tras-os-Montes, selected with care for your table.', 'shop', 'main', '2026-01-19 12:51:19', '2026-02-09 19:52:45'),
(23, 'activities_title', 2, 'text', 'Things To Do', 'activities', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(24, 'activities_intro', 2, 'textarea', 'Discover the wonders of Mogadouro and surroundings', 'activities', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(25, 'contact_title', 2, 'text', 'Contact Us', 'contact', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(26, 'contact_intro', 2, 'textarea', 'Do you have any questions? Get in touch with us', 'contact', 'main', '2026-01-19 12:51:19', '2026-02-09 19:27:19'),
(27, 'home_hero_subtitle', 1, 'text', 'Onde a tradi├º├úo transmontana encontra o conforto moderno', 'home', 'hero', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(28, 'home_hero_subtitle', 2, 'text', 'Where Transmontana tradition meets modern comfort', 'home', 'hero', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(29, 'home_split_left_label', 1, 'text', 'Bem-vindo ao', 'home', 'split_hero', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(30, 'home_split_left_label', 2, 'text', 'Welcome to the', 'home', 'split_hero', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(31, 'home_split_left_title', 1, 'text', 'Ref├║gio', 'home', 'split_hero', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(32, 'home_split_left_title', 2, 'text', 'Refuge', 'home', 'split_hero', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(33, 'home_split_right_label', 1, 'text', 'Descubra a', 'home', 'split_hero', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(34, 'home_split_right_label', 2, 'text', 'Discover the', 'home', 'split_hero', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(35, 'home_split_right_title', 1, 'text', 'Tradi├º├úo', 'home', 'split_hero', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(36, 'home_split_right_title', 2, 'text', 'Tradition', 'home', 'split_hero', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(37, 'home_explore_title', 1, 'text', 'Explore o Nosso Mundo', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(38, 'home_explore_title', 2, 'text', 'Explore Our World', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(39, 'home_card1_label', 1, 'text', 'Dormir', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(40, 'home_card1_label', 2, 'text', 'Sleep', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(41, 'home_card1_title', 1, 'text', 'Alojamento', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(42, 'home_card1_title', 2, 'text', 'Accommodation', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(43, 'home_card1_text', 1, 'text', 'Sinta o conforto das nossas casas r├║sticas.', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(44, 'home_card1_text', 2, 'text', 'Experience the comfort of our rustic houses.', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(45, 'home_card1_cta', 1, 'text', 'Ver Casas', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(46, 'home_card1_cta', 2, 'text', 'View Rooms', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(47, 'home_card2_label', 1, 'text', 'Experienciar', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(48, 'home_card2_label', 2, 'text', 'Experience', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(49, 'home_card2_title', 1, 'text', 'Atividades', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(50, 'home_card2_title', 2, 'text', 'Activities', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(51, 'home_card2_text', 1, 'text', 'Descubra a natureza e hist├│ria de Mogadouro.', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(52, 'home_card2_text', 2, 'text', 'Discover the nature and history of Mogadouro.', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(53, 'home_card2_cta', 1, 'text', 'Explorar', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(54, 'home_card2_cta', 2, 'text', 'Explore', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(55, 'home_card3_label', 1, 'text', 'Saborear', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(56, 'home_card3_label', 2, 'text', 'Taste', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(57, 'home_card3_title', 1, 'text', 'Loja Regional', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(58, 'home_card3_title', 2, 'text', 'Regional Shop', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(59, 'home_card3_text', 1, 'text', 'Sabores aut├¬nticos de Tr├ís-os-Montes.', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(60, 'home_card3_text', 2, 'text', 'Authentic flavors from Tras-os-Montes.', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(61, 'home_card3_cta', 1, 'text', 'Comprar', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(62, 'home_card3_cta', 2, 'text', 'Shop Now', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(63, 'home_card4_label', 1, 'text', 'Conectar', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(64, 'home_card4_label', 2, 'text', 'Connect', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(65, 'home_card4_title', 1, 'text', 'Contactos', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(66, 'home_card4_title', 2, 'text', 'Contact Us', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(67, 'home_card4_text', 1, 'text', 'Fale connosco e planeie a sua visita.', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(68, 'home_card4_text', 2, 'text', 'Get in touch and plan your visit.', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(69, 'home_card4_cta', 1, 'text', 'Contactar', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(70, 'home_card4_cta', 2, 'text', 'Get in Touch', 'home', 'explore', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(71, 'home_about_label', 1, 'text', 'A Nossa Hist├│ria', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(72, 'home_about_label', 2, 'text', 'Our Story', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(73, 'home_about_title', 1, 'html', 'Mais que uma casa,<br>um <span class=\"italic text-accent\">legado</span>.', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(74, 'home_about_title', 2, 'html', 'More than a house,<br>a <span class=\"italic text-accent\">legacy</span>.', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(75, 'home_about_text1', 1, 'textarea', 'A Casa do Gi nasceu da vontade de preservar as ra├¡zes transmontanas. O que outrora foi uma casa de fam├¡lia, ├® hoje um ref├║gio para quem procura a autenticidade do campo.', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(76, 'home_about_text1', 2, 'textarea', 'A Casa do Gi was born from the will to preserve the roots of Tras-os-Montes. What was once a family home is now a refuge for those seeking the authenticity of the countryside.', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(77, 'home_about_text2', 1, 'textarea', 'Aqui, o tempo abranda. Convidamo-lo a descobrir as tradi├º├Áes, os sabores e as gentes que fazem de Mogadouro um lugar ├║nico no mundo.', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(78, 'home_about_text2', 2, 'textarea', 'Here, time slows down. We invite you to discover the traditions, the flavors, and the people that make Mogadouro a unique place in the world.', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(79, 'home_about_cta', 1, 'text', 'Ler Hist├│ria Completa', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(80, 'home_about_cta', 2, 'text', 'Read Full Story', 'home', 'about_teaser', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(81, 'about_hero_label', 1, 'text', 'A Nossa Hist├│ria', 'about', 'hero', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(82, 'about_hero_label', 2, 'text', 'Our Story', 'about', 'hero', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(83, 'about_hero_subtitle', 1, 'textarea', 'Simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor!', 'about', 'hero', '2026-02-09 18:34:33', '2026-02-09 20:08:48'),
(84, 'about_hero_subtitle', 2, 'textarea', 'Simplicity, warmth, remarkable moments of conviviality, family warmth, joy, fun, laughter and lots of love!', 'about', 'hero', '2026-02-09 18:34:33', '2026-02-09 20:08:48'),
(85, 'about_origin_label', 1, 'text', 'A Nossa Origem', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(86, 'about_origin_label', 2, 'text', 'Our Origins', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(87, 'about_origin_title', 1, 'html', 'Uma casa constru├¡da com <span class=\"italic text-secondary\">amor</span> e <span class=\"italic text-secondary\">saudade</span>.', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(88, 'about_origin_title', 2, 'html', 'A house built with <span class=\"italic text-secondary\">love</span> and <span class=\"italic text-secondary\">longing</span>.', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(89, 'about_origin_text1', 1, 'textarea', 'Erguida nos anos 80, a <strong>Casa do Gi</strong> conta a hist├│ria intemporal de quem partiu para longe mas nunca esqueceu as suas ra├¡zes. Constru├¡da tijolo a tijolo, representa o sonho concretizado de regressar a casa.', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(90, 'about_origin_text1', 2, 'textarea', 'Built in the 80s, <strong>Casa do Gi</strong> tells the timeless story of those who left for distant lands but never forgot their roots. Constructed brick by brick, it represents the fulfilled dream of returning home.', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(91, 'about_origin_text2', 1, 'textarea', 'O que come├ºou como um projeto de vida familiar transformou-se num ref├║gio para quem procura a paz do interior. Aqui, o tempo abranda e os dias s├úo medidos pela luz do sol e pelas conversas ├á beira da lareira.', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(92, 'about_origin_text2', 2, 'textarea', 'What began as a family life project transformed into a refuge for those seeking the peace of the countryside. Here, time slows down and days are measured by sunlight and conversations by the fireplace.', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(93, 'about_origin_caption', 1, 'text', '1980 ÔÇó O In├¡cio', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(94, 'about_origin_caption', 2, 'text', '1980 ÔÇó The Beginning', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(95, 'about_origin_signature', 1, 'text', 'Fam├¡lia Gi', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(96, 'about_origin_signature', 2, 'text', 'Gi Family', 'about', 'origin', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(97, 'about_values_label', 1, 'text', 'Valores', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(98, 'about_values_label', 2, 'text', 'Values', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(99, 'about_values_title', 1, 'html', 'A arte de bem receber,<br>├á moda antiga.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(100, 'about_values_title', 2, 'html', 'The art of welcoming,<br>the old-fashioned way.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(101, 'about_values_intro', 1, 'textarea', 'N├úo somos um hotel. Somos uma casa de fam├¡lia que decidiu abrir as portas ao mundo. Aqui, a hospitalidade n├úo ├® um servi├ºo, ├® a nossa natureza.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(102, 'about_values_intro', 2, 'textarea', 'We are not a hotel. We are a family home that decided to open its doors to the world. Here, hospitality is not a service, it\'s our nature.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(103, 'about_value1_title', 1, 'text', 'Acolhimento Genu├¡no', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(104, 'about_value1_title', 2, 'text', 'Genuine Hospitality', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(105, 'about_value1_text', 1, 'textarea', 'Recebemos cada h├│spede como um velho amigo. Sem formalismos r├¡gidos, com o calor de um abra├ºo e a sinceridade de um sorriso transmontano.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(106, 'about_value1_text', 2, 'textarea', 'We welcome each guest as an old friend. Without rigid formalities, with the warmth of a hug and the sincerity of a Transmontano smile.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(107, 'about_value2_title', 1, 'text', 'Paz Absoluta', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(108, 'about_value2_title', 2, 'text', 'Absolute Peace', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(109, 'about_value2_text', 1, 'textarea', 'O luxo do sil├¬ncio. Longe da confus├úo, onde o ├║nico ru├¡do ├® o vento nas ├írvores e o cantar dos p├íssaros. O ref├║gio perfeito para recarregar energias.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(110, 'about_value2_text', 2, 'textarea', 'The luxury of silence. Far from the hustle, where the only sound is the wind in the trees and the singing of birds. The perfect refuge to recharge energies.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(111, 'about_value3_title', 1, 'text', 'Esp├¡rito de Partilha', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(112, 'about_value3_title', 2, 'text', 'Spirit of Sharing', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(113, 'about_value3_text', 1, 'textarea', 'Acreditamos que as melhores mem├│rias s├úo constru├¡das ├á mesa. Partilhamos hist├│rias, sabores e experi├¬ncias que ficam para sempre.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(114, 'about_value3_text', 2, 'textarea', 'We believe the best memories are built at the table. We share stories, flavors and experiences that last forever.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(115, 'about_value4_title', 1, 'text', 'Aten├º├úo ao Detalhe', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(116, 'about_value4_title', 2, 'text', 'Attention to Detail', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(117, 'about_value4_text', 1, 'textarea', 'Nada ├® deixado ao acaso. Do pequeno-almo├ºo caseiro ├á decora├º├úo cuidada, tudo ├® pensado para o seu conforto e bem-estar.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(118, 'about_value4_text', 2, 'textarea', 'Nothing is left to chance. From homemade breakfast to thoughtful decoration, everything is designed for your comfort and wellbeing.', 'about', 'values', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(119, 'about_region_label', 1, 'text', 'O Nosso Ber├ºo', 'about', 'region', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(120, 'about_region_label', 2, 'text', 'Our Home', 'about', 'region', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(121, 'about_region_text', 1, 'textarea', 'Onde o tempo p├íra e a alma respira. Uma terra de horizontes infinitos, guardi├ú de tradi├º├Áes milenares e de uma beleza natural bruta e intocada.', 'about', 'region', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(122, 'about_region_text', 2, 'textarea', 'Where time stops and the soul breathes. A land of infinite horizons, guardian of ancient traditions and raw, untouched natural beauty.', 'about', 'region', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(123, 'about_region_cta1', 1, 'text', 'Planear Visita', 'about', 'region', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(124, 'about_region_cta1', 2, 'text', 'Plan Visit', 'about', 'region', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(125, 'about_region_cta2', 1, 'text', 'O que fazer', 'about', 'region', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(126, 'about_region_cta2', 2, 'text', 'Things to do', 'about', 'region', '2026-02-09 18:34:33', '2026-02-09 18:34:33'),
(333, 'shop_empty_message', 1, 'text', 'Esta categoria ainda nao tem produtos disponiveis.', 'shop', 'main', '2026-02-09 19:27:19', '2026-02-09 19:52:45'),
(334, 'shop_empty_message', 2, 'text', 'This category does not have products available yet.', 'shop', 'main', '2026-02-09 19:27:19', '2026-02-09 19:52:45'),
(335, 'contact_success_message', 1, 'textarea', 'Obrigado pelo seu contacto. Iremos responder o mais brevemente possivel.', 'contact', 'main', '2026-02-09 19:27:19', '2026-02-09 19:47:40'),
(336, 'contact_success_message', 2, 'textarea', 'Thank you for your contact. We will reply as soon as possible.', 'contact', 'main', '2026-02-09 19:27:19', '2026-02-09 19:47:40'),
(343, 'footer_tagline', 1, 'text', 'Simplicidade, acolhimento e muito amor em Mogadouro', 'footer', 'main', '2026-02-09 19:47:40', '2026-02-09 19:50:59'),
(344, 'footer_tagline', 2, 'text', 'Simplicity, warmth and love in Mogadouro', 'footer', 'main', '2026-02-09 19:47:40', '2026-02-09 19:50:59'),
(361, 'accommodation_hero_tagline', 1, 'text', 'Alojamento Local', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(362, 'accommodation_hero_tagline', 2, 'text', 'Local Accommodation', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(363, 'accommodation_hero_title', 1, 'text', 'A Casa do Gi', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(364, 'accommodation_hero_title', 2, 'text', 'A Casa do Gi', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(365, 'accommodation_hero_subtitle', 1, 'text', 'Acolhimento transmontano, momentos em familia e memorias para sempre.', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(366, 'accommodation_hero_subtitle', 2, 'text', 'Transmontano hospitality, family moments and memories forever.', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(367, 'accommodation_section_subtitle', 1, 'text', 'Duas Casas, Uma Experiencia', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(368, 'accommodation_section_subtitle', 2, 'text', 'Two Houses, One Experience', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(369, 'accommodation_section_title', 1, 'text', 'Escolha o Seu Refugio', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(370, 'accommodation_section_title', 2, 'text', 'Choose Your Refuge', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(371, 'accommodation_features_title', 1, 'text', 'O Que Ambas as Casas Oferecem', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(372, 'accommodation_features_title', 2, 'text', 'What Both Houses Offer', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(373, 'accommodation_feature_1', 1, 'text', 'Wi-Fi Gratis', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(374, 'accommodation_feature_1', 2, 'text', 'Free Wi-Fi', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(375, 'accommodation_feature_2', 1, 'text', 'Check-in Autonomo', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(376, 'accommodation_feature_2', 2, 'text', 'Self Check-in', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(377, 'accommodation_feature_3', 1, 'text', 'Roupa de Cama', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(378, 'accommodation_feature_3', 2, 'text', 'Bed Linen', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(379, 'accommodation_feature_4', 1, 'text', 'Localizacao Central', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(380, 'accommodation_feature_4', 2, 'text', 'Central Location', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:00:28'),
(381, 'activities_hero_tagline', 1, 'text', 'Descubra Mogadouro', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:01:29'),
(382, 'activities_hero_tagline', 2, 'text', 'Discover Mogadouro', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:01:29'),
(383, 'activities_hero_title', 1, 'text', 'O Que Fazer', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:01:29'),
(384, 'activities_hero_title', 2, 'text', 'What to Do', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:01:29'),
(385, 'activities_hero_subtitle', 1, 'text', 'De paisagens deslumbrantes a sabores unicos, o nordeste transmontano tem muito para oferecer.', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:01:29'),
(386, 'activities_hero_subtitle', 2, 'text', 'From stunning landscapes to unique flavors, the northeast of Tras-os-Montes has much to offer.', NULL, NULL, '2026-02-09 19:57:15', '2026-02-09 20:01:29'),
(387, 'contact_hero_tagline', 1, 'text', 'Fale Connosco', NULL, NULL, '2026-02-09 20:08:12', '2026-02-09 20:08:12'),
(388, 'contact_hero_tagline', 2, 'text', 'Talk to Us', NULL, NULL, '2026-02-09 20:08:12', '2026-02-09 20:08:12'),
(389, 'contact_hero_title', 1, 'text', 'Contacte-nos', NULL, NULL, '2026-02-09 20:08:12', '2026-02-09 20:08:12'),
(390, 'contact_hero_title', 2, 'text', 'Contact Us', NULL, NULL, '2026-02-09 20:08:12', '2026-02-09 20:08:12'),
(391, 'contact_hero_subtitle', 1, 'text', 'Tem alguma questao? Entre em contacto connosco', NULL, NULL, '2026-02-09 20:08:12', '2026-02-09 20:08:12'),
(392, 'contact_hero_subtitle', 2, 'text', 'Have any questions? Get in touch with us', NULL, NULL, '2026-02-09 20:08:12', '2026-02-09 20:08:12'),
(399, 'about_hero_tagline', 1, 'text', 'A Nossa Historia', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(400, 'about_hero_tagline', 2, 'text', 'Our Story', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(401, 'about_hero_title', 1, 'text', 'A Casa do Gi', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(402, 'about_hero_title', 2, 'text', 'A Casa do Gi', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(403, 'privacy_hero_tagline', 1, 'text', 'Informacao Legal', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(404, 'privacy_hero_tagline', 2, 'text', 'Legal Information', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(405, 'privacy_hero_title', 1, 'text', 'Politica de Privacidade', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(406, 'privacy_hero_title', 2, 'text', 'Privacy Policy', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(407, 'privacy_hero_subtitle', 1, 'textarea', 'A sua privacidade e importante para nos. Saiba como tratamos os seus dados.', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(408, 'privacy_hero_subtitle', 2, 'textarea', 'Your privacy is important to us. Learn how we handle your data.', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(409, 'privacy_date', 1, 'text', 'Atualizado em: 09 de Fevereiro de 2025', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(410, 'privacy_date', 2, 'text', 'Updated on: February 9, 2025', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(413, 'terms_hero_tagline', 1, 'text', 'Informacao Legal', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(414, 'terms_hero_tagline', 2, 'text', 'Legal Information', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(415, 'terms_hero_title', 1, 'text', 'Termos e Condicoes', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(416, 'terms_hero_title', 2, 'text', 'Terms and Conditions', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(417, 'terms_hero_subtitle', 1, 'textarea', 'Por favor, leia atentamente os termos e condicoes de utilizacao do nosso servico.', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(418, 'terms_hero_subtitle', 2, 'textarea', 'Please read carefully the terms and conditions of use of our service.', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(419, 'terms_date', 1, 'text', 'Atualizado em: 09 de Fevereiro de 2025', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(420, 'terms_date', 2, 'text', 'Updated on: February 9, 2025', NULL, NULL, '2026-02-09 20:08:48', '2026-02-09 20:08:48'),
(431, 'footer_description', 1, 'textarea', 'Simplicidade, acolhimento e muito amor em Mogadouro, Portugal.', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(432, 'footer_description', 2, 'textarea', 'Simplicity, warmth and love in Mogadouro, Portugal.', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(433, 'footer_quicklinks_title', 1, 'text', 'Links R??', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(434, 'footer_quicklinks_title', 2, 'text', 'Quick Links', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(435, 'footer_contact_title', 1, 'text', 'Contacto', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(436, 'footer_contact_title', 2, 'text', 'Contact', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(437, 'footer_address', 1, 'text', '52 Avenida Nossa Senhora do Caminho, Mogadouro', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(438, 'footer_address', 2, 'text', '52 Avenida Nossa Senhora do Caminho, Mogadouro', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(439, 'footer_book_title', 1, 'text', 'Reserve J??', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(440, 'footer_book_title', 2, 'text', 'Book Now', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(441, 'footer_rights_text', 1, 'text', 'Todos os direitos reservados.', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(442, 'footer_rights_text', 2, 'text', 'All rights reserved.', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(443, 'cookie_banner_text', 1, '', 'Utilizamos cookies para melhorar a sua experi??ncia no nosso website. Ao continuar a navegar, concorda com a utiliza????o de cookies. Saiba mais nos nossos <a href=\"/alojamentogi/termos-condicoes/\" class=\"text-secondary hover:underline\">termos e condi????es</a> e <a href=\"/alojamentogi/politica-privacidade/\" class=\"text-secondary hover:underline\">pol??tica de privacidade</a>.', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(444, 'cookie_banner_text', 2, '', 'We use cookies to improve your experience on our website. By continuing to browse, you agree to our use of cookies. Learn more in our <a href=\"/alojamentogi/en/termos-condicoes/\" class=\"text-secondary hover:underline\">terms and conditions</a> and <a href=\"/alojamentogi/en/politica-privacidade/\" class=\"text-secondary hover:underline\">privacy policy</a>.', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(445, 'cookie_banner_accept', 1, 'text', 'Aceitar', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(446, 'cookie_banner_accept', 2, 'text', 'Accept', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(447, 'cookie_banner_details', 1, 'text', 'Ver Detalhes', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(448, 'cookie_banner_details', 2, 'text', 'Details', NULL, NULL, '2026-02-09 20:31:09', '2026-02-09 20:34:54'),
(467, 'home_image_split_left', 1, 'text', '/uploads/content/home_image_split_left_1770690443.jpg', NULL, NULL, '2026-02-10 02:19:22', '2026-02-10 02:27:23'),
(468, 'home_image_split_left', 2, 'text', '/uploads/content/home_image_split_left_1770690443.jpg', NULL, NULL, '2026-02-10 02:19:22', '2026-02-10 02:27:23');

-- --------------------------------------------------------

--
-- Estrutura da tabela `external_links`
--

CREATE TABLE `external_links` (
  `id` int(10) UNSIGNED NOT NULL,
  `url` varchar(500) NOT NULL COMMENT 'External website URL',
  `icon` varchar(100) DEFAULT NULL COMMENT 'Icon class or path',
  `icon_image` varchar(255) DEFAULT NULL COMMENT 'Custom icon image path',
  `category` enum('tourism','government','news','gastronomy','culture','nature','events','accommodation','other') DEFAULT 'tourism',
  `is_featured` tinyint(1) DEFAULT 0 COMMENT 'Show in featured section',
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `clicks_count` int(10) UNSIGNED DEFAULT 0 COMMENT 'Track clicks',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `external_links`
--

INSERT INTO `external_links` (`id`, `url`, `icon`, `icon_image`, `category`, `is_featured`, `is_active`, `sort_order`, `clicks_count`, `created_at`, `updated_at`) VALUES
(1, 'https://www.cm-mogadouro.pt/', 'building', NULL, 'government', 0, 1, 1, 0, '2026-02-06 14:42:31', '2026-02-07 01:51:28'),
(2, 'https://natural.pt/protected-areas/parque-natural-do-douro-internacional', 'tree', NULL, 'nature', 1, 1, 2, 0, '2026-02-06 14:42:31', '2026-02-06 14:42:31'),
(3, 'https://www.visitportugal.com/pt-pt/destinos/porto-e-norte/tras-os-montes', 'map', NULL, 'tourism', 1, 1, 3, 0, '2026-02-06 14:42:31', '2026-02-06 14:42:31'),
(4, 'https://www.centerofportugal.com/pt/regiao/tras-os-montes/', 'compass', NULL, 'tourism', 0, 1, 4, 0, '2026-02-06 14:42:31', '2026-02-06 14:42:31'),
(6, 'https://www.youtube.com/watch?v=wh-07BzfgYY', 'globe', NULL, 'events', 1, 1, 0, 0, '2026-02-06 18:34:43', '2026-02-07 01:51:36');

-- --------------------------------------------------------

--
-- Estrutura da tabela `external_link_translations`
--

CREATE TABLE `external_link_translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `link_id` int(10) UNSIGNED NOT NULL,
  `language_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL COMMENT 'Link title',
  `description` text DEFAULT NULL COMMENT 'Link description'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `external_link_translations`
--

INSERT INTO `external_link_translations` (`id`, `link_id`, `language_id`, `title`, `description`) VALUES
(1, 1, 1, 'Câmara Municipal de Mogadouro', 'Site oficial da Câmara Municipal com informações sobre serviços, eventos e notícias locais.'),
(2, 1, 2, 'Mogadouro City Hall', 'Official City Hall website with information about services, events and local news.'),
(3, 2, 1, 'Parque Natural do Douro Internacional', 'Descubra a fauna e flora únicas das Arribas do Douro, um dos últimos refúgios de aves de rapina na Europa.'),
(4, 2, 2, 'Douro International Natural Park', 'Discover the unique fauna and flora of the Douro Cliffs, one of the last refuges for birds of prey in Europe.'),
(5, 3, 1, 'Visit Portugal - Trás-os-Montes', 'Portal oficial de turismo de Portugal com guias e sugestões para explorar a região transmontana.'),
(6, 3, 2, 'Visit Portugal - Trás-os-Montes', 'Official Portugal tourism portal with guides and suggestions to explore the Transmontana region.'),
(7, 4, 1, 'Centro de Portugal - Trás-os-Montes nao', 'Informações turísticas detalhadas sobre a região, incluindo roteiros e pontos de interesse.'),
(8, 4, 2, 'Centro de Portugal - Trás-os-Montes nao', 'Informações turísticas detalhadas sobre a região, incluindo roteiros e pontos de interesse.'),
(11, 6, 1, 'Link testar', 'Nada'),
(12, 6, 2, 'Link testar', 'Nada');

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
(21, 1, 1, 1, '2026-02-10 18:23:27'),
(22, 1, 1, 2, '2026-02-10 18:23:27'),
(23, 1, 1, 3, '2026-02-10 18:23:27'),
(24, 1, 0, 4, '2026-02-10 18:23:27'),
(25, 1, 0, 5, '2026-02-10 18:23:27'),
(26, 2, 1, 1, '2026-02-10 18:23:27'),
(27, 2, 1, 2, '2026-02-10 18:23:27'),
(28, 2, 1, 3, '2026-02-10 18:23:27'),
(29, 2, 0, 4, '2026-02-10 18:23:27'),
(30, 2, 0, 5, '2026-02-10 18:23:27');

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
(41, 21, 1, 'Nao sao permitidas festas ou eventos.wewew'),
(42, 21, 2, 'No parties or events allowed.'),
(43, 22, 1, 'Horario de silencio: 22h00 - 08h00.'),
(44, 22, 2, 'Quiet hours: 22:00 - 08:00.'),
(45, 23, 1, 'Proibido fumar no interior.'),
(46, 23, 2, 'No smoking inside.'),
(47, 24, 1, 'Animais de estimacao nao sao permitidos.'),
(48, 24, 2, 'Pets are not allowed.'),
(49, 25, 1, 'Respeite os vizinhos e a propriedade.'),
(50, 25, 2, 'Respect neighbors and property.'),
(51, 26, 1, 'Nao sao permitidas festas ou eventos.'),
(52, 26, 2, 'No parties or events allowed.'),
(53, 27, 1, 'Horario de silencio: 22h00 - 08h00.'),
(54, 27, 2, 'Quiet hours: 22:00 - 08:00.'),
(55, 28, 1, 'Proibido fumar no interior.'),
(56, 28, 2, 'No smoking inside.'),
(57, 29, 1, 'Animais de estimacao nao sao permitidos.'),
(58, 29, 2, 'Pets are not allowed.'),
(59, 30, 1, 'Respeite os vizinhos e a propriedade.'),
(60, 30, 2, 'Respect neighbors and property.');

-- --------------------------------------------------------

--
-- Estrutura da tabela `invoices`
--

CREATE TABLE `invoices` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `invoice_uuid` char(36) NOT NULL COMMENT 'UUID v4 unique identifier',
  `barcode` char(9) NOT NULL COMMENT '9-digit barcode',
  `barcode_batch` int(10) UNSIGNED DEFAULT 1 COMMENT 'Batch number for code recycling',
  `integrity_hash` char(64) NOT NULL COMMENT 'SHA-256 hash for tamper detection',
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_nif` varchar(20) DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `billing_postal_code` varchar(10) DEFAULT NULL,
  `billing_city` varchar(100) DEFAULT NULL,
  `items_json` text NOT NULL COMMENT 'JSON snapshot of order items at invoice time',
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `paid_at` datetime DEFAULT NULL,
  `emailed_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Estrutura da tabela `manual_orders`
--

CREATE TABLE `manual_orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `shipping_address` text DEFAULT NULL,
  `shipping_postal_code` varchar(10) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `items_json` text NOT NULL COMMENT 'JSON snapshot of cart items',
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `status` enum('new','contacted','converted','cancelled') NOT NULL DEFAULT 'new',
  `admin_notes` text DEFAULT NULL,
  `converted_order_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'If converted to real order',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `notes` text DEFAULT NULL COMMENT 'Customer notes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `contacted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `manual_orders`
--

INSERT INTO `manual_orders` (`id`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `shipping_postal_code`, `shipping_city`, `items_json`, `subtotal`, `shipping_fee`, `total`, `status`, `admin_notes`, `converted_order_id`, `ip_address`, `user_agent`, `notes`, `created_at`, `updated_at`, `contacted_at`) VALUES
(1, 'Guilherme Jose Costa Marques', 'guilherme.jcmarques@gmail.com', '965079823', 'R. Pádua Correia 166', '4430-999', 'Vila Nova de Gaia', '[{\"product_id\":2,\"product_name\":\"Banana das Américas Pack 5kg\",\"product_sku\":\"969\",\"quantity\":1,\"unit_price\":8,\"subtotal\":8}]', 8.00, 5.00, 13.00, 'converted', NULL, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Nada.', '2026-02-09 00:13:43', '2026-02-10 17:33:14', NULL),
(2, 'James', 'james@test.com', '998323447', NULL, NULL, NULL, '[{\"product_id\":2,\"product_name\":\"American Bananas\",\"product_sku\":\"969\",\"quantity\":1,\"unit_price\":8,\"subtotal\":8}]', 8.00, 5.00, 13.00, 'converted', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', NULL, '2026-02-09 23:08:20', '2026-02-10 17:08:04', '2026-02-10 16:52:30');

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
  `category` enum('gallery','products','activities','content','other') DEFAULT 'other',
  `entity_type` enum('activity','hero','accommodation','product','standalone','other') DEFAULT 'standalone' COMMENT 'Type of entity this media belongs to',
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
(8, '6976756e94c17_1769370990.jpg', 'AlojamentoQuarto8.jpg', '/uploads/media/6976756e94c17_1769370990.jpg', 'image/jpeg', 77675, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 0, 1, '2026-01-25 19:56:30', 1),
(9, '6976756e992d1_1769370990.jpg', 'AlojamentoQuarto7.jpg', '/uploads/media/6976756e992d1_1769370990.jpg', 'image/jpeg', 78878, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 0, 1, '2026-01-25 19:56:30', 1),
(10, '6976756e9adb4_1769370990.jpg', 'AlojamentoQuarto6.jpg', '/uploads/media/6976756e9adb4_1769370990.jpg', 'image/jpeg', 86814, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 0, 1, '2026-01-25 19:56:30', 1),
(11, '6976756e9c308_1769370990.jpg', 'AlojamentoQuarto5.jpg', '/uploads/media/6976756e9c308_1769370990.jpg', 'image/jpeg', 82471, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 0, 1, '2026-01-25 19:56:30', 1),
(12, '6976756e9d47d_1769370990.jpg', 'AlojamentoQuarto4.jpg', '/uploads/media/6976756e9d47d_1769370990.jpg', 'image/jpeg', 100462, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 0, 1, '2026-01-25 19:56:30', 1),
(13, '6976756e9e5a6_1769370990.jpg', 'AlojamentoQuarto3.jpg', '/uploads/media/6976756e9e5a6_1769370990.jpg', 'image/jpeg', 93303, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 0, 1, '2026-01-25 19:56:30', 1),
(14, '6976756e9f8b2_1769370990.jpg', 'AlojamentoQuarto2.jpg', '/uploads/media/6976756e9f8b2_1769370990.jpg', 'image/jpeg', 75318, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 0, 1, '2026-01-25 19:56:30', 1),
(15, '6976756ea0995_1769370990.jpg', 'AlojamentoQuarto1.jpg', '/uploads/media/6976756ea0995_1769370990.jpg', 'image/jpeg', 97800, 'Quarto Cama de Casal', 'Double Bed Room', NULL, NULL, 'gallery', 'standalone', NULL, 0, 0, 1, '2026-01-25 19:56:30', 1),
(19, '6976986c696fb_1769379948.jpg', 'AlojamentoQuarto49.jpg', '/uploads/media/6976986c696fb_1769379948.jpg', 'image/jpeg', 57394, NULL, NULL, NULL, NULL, 'other', 'standalone', NULL, 0, 0, 1, '2026-01-25 22:25:48', NULL),
(20, '6976986c6ad28_1769379948.jpg', 'AlojamentoQuarto48.jpg', '/uploads/media/6976986c6ad28_1769379948.jpg', 'image/jpeg', 163886, NULL, NULL, NULL, NULL, 'other', 'standalone', NULL, 0, 0, 1, '2026-01-25 22:25:48', NULL),
(21, '6976986c6c3a6_1769379948.jpg', 'AlojamentoQuarto47.jpg', '/uploads/media/6976986c6c3a6_1769379948.jpg', 'image/jpeg', 129213, NULL, NULL, NULL, NULL, 'other', 'standalone', NULL, 0, 0, 1, '2026-01-25 22:25:48', NULL),
(22, '6976986c6e346_1769379948.jpg', 'AlojamentoQuarto46.jpg', '/uploads/media/6976986c6e346_1769379948.jpg', 'image/jpeg', 98581, NULL, NULL, NULL, NULL, 'other', 'standalone', NULL, 0, 0, 1, '2026-01-25 22:25:48', NULL),
(23, '6976986c7080e_1769379948.jpg', 'AlojamentoQuarto45.jpg', '/uploads/media/6976986c7080e_1769379948.jpg', 'image/jpeg', 67252, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 0, 1, '2026-01-25 22:25:48', 1),
(24, '6976986c72fde_1769379948.jpg', 'AlojamentoQuarto44.jpg', '/uploads/media/6976986c72fde_1769379948.jpg', 'image/jpeg', 63988, NULL, NULL, NULL, NULL, 'other', 'standalone', NULL, 0, 0, 1, '2026-01-25 22:25:48', NULL),
(25, 'accommodation_697698bc063af.jpg', 'AlojamentoQuarto26.jpg', '/uploads/accommodation/accommodation_697698bc063af.jpg', 'image/jpeg', 69718, '', '', NULL, NULL, 'gallery', 'standalone', NULL, 0, 1, NULL, '2026-01-25 22:27:08', 1),
(26, 'activity_gallery_1_698618f7cee51.jpg', 'activity_gallery_1_698618f7cee51.jpg', '/uploads/activities/activity_gallery_1_698618f7cee51.jpg', 'image/jpeg', 0, NULL, NULL, NULL, NULL, 'activities', 'activity', 1, 0, 1, NULL, '2026-02-06 16:38:15', NULL),
(27, 'activity_gallery_1_698618f7d243f.jpg', 'activity_gallery_1_698618f7d243f.jpg', '/uploads/activities/activity_gallery_1_698618f7d243f.jpg', 'image/jpeg', 0, NULL, NULL, NULL, NULL, 'activities', 'activity', 1, 0, 2, NULL, '2026-02-06 16:38:15', NULL),
(28, 'activity_gallery_1_698618f7d3769.jpg', 'activity_gallery_1_698618f7d3769.jpg', '/uploads/activities/activity_gallery_1_698618f7d3769.jpg', 'image/jpeg', 0, NULL, NULL, NULL, NULL, 'activities', 'activity', 1, 0, 3, NULL, '2026-02-06 16:38:15', NULL),
(29, 'activity_gallery_1_698618f7d4806.jpg', 'activity_gallery_1_698618f7d4806.jpg', '/uploads/activities/activity_gallery_1_698618f7d4806.jpg', 'image/jpeg', 0, NULL, NULL, NULL, NULL, 'activities', 'activity', 1, 0, 4, NULL, '2026-02-06 16:38:15', NULL),
(30, 'activity_gallery_1_698618f7d5980.jpg', 'activity_gallery_1_698618f7d5980.jpg', '/uploads/activities/activity_gallery_1_698618f7d5980.jpg', 'image/jpeg', 0, NULL, NULL, NULL, NULL, 'activities', 'activity', 1, 0, 5, NULL, '2026-02-06 16:38:15', NULL),
(33, 'activity_69860c41e0ec9.jpg', 'activity_69860c41e0ec9.jpg', '/uploads/activities/activity_69860c41e0ec9.jpg', 'image/jpeg', 0, NULL, NULL, NULL, NULL, 'activities', 'activity', 1, 1, 0, NULL, '2026-02-06 17:11:45', NULL),
(37, 'hero_about_1770084603.png', 'hero_about_1770084603.png', '/uploads/heroes/hero_about_1770084603.png', 'image/jpeg', 0, NULL, NULL, NULL, NULL, 'content', 'hero', 4, 1, 0, NULL, '2026-02-03 00:18:35', NULL),
(38, 'hero_contact_1770084865.jpg', 'hero_contact_1770084865.jpg', '/uploads/heroes/hero_contact_1770084865.jpg', 'image/jpeg', 0, NULL, NULL, NULL, NULL, 'content', 'hero', 5, 1, 0, NULL, '2026-02-03 00:18:35', NULL),
(41, 'hero_accommodation_main_1770400137.jpg', 'MogadouroAlojamento.jpg', '/uploads/heroes/hero_accommodation_main_1770400137.jpg', 'image/jpeg', 267538, NULL, NULL, NULL, NULL, 'content', 'hero', 2, 1, 0, 1, '2026-02-06 17:48:57', NULL),
(42, 'hero_shop_1770400179.png', 'MogadouroLogin2.png', '/uploads/heroes/hero_shop_1770400179.png', 'image/png', 48057, NULL, NULL, NULL, NULL, 'content', 'hero', 6, 1, 0, 1, '2026-02-06 17:49:39', NULL),
(43, 'hero_activities_1770400187.jpg', 'MogadouroAtividades.jpg', '/uploads/heroes/hero_activities_1770400187.jpg', 'image/jpeg', 618067, NULL, NULL, NULL, NULL, 'content', 'hero', 3, 1, 0, 1, '2026-02-06 17:49:47', NULL),
(44, 'hero_home_1770400193.jpg', 'MogadouroAtividades.jpg', '/uploads/heroes/hero_home_1770400193.jpg', 'image/jpeg', 618067, NULL, NULL, NULL, NULL, 'content', 'hero', 1, 1, 0, 1, '2026-02-06 17:49:53', NULL),
(45, 'activity_cover_13_698633d9d42ad.png', 'FotoGi.png', '/uploads/activities/activity_cover_13_698633d9d42ad.png', 'image/png', 573857, NULL, NULL, NULL, NULL, 'activities', 'activity', 13, 1, 0, 1, '2026-02-06 18:32:57', NULL),
(46, 'activity_gallery_13_698633d9d6dd0.jpg', 'MogadouroAtividades.jpg', '/uploads/activities/activity_gallery_13_698633d9d6dd0.jpg', 'image/jpeg', 618067, NULL, NULL, NULL, NULL, 'activities', 'activity', 13, 0, 1, 1, '2026-02-06 18:32:57', NULL),
(47, 'activity_gallery_13_698633d9d80d0.jpg', 'MogadouroContacto.jpg', '/uploads/activities/activity_gallery_13_698633d9d80d0.jpg', 'image/jpeg', 262185, NULL, NULL, NULL, NULL, 'activities', 'activity', 13, 0, 2, 1, '2026-02-06 18:32:57', NULL),
(48, 'activity_gallery_13_698633d9d939e.jpg', 'MogadouroContacto2.jpg', '/uploads/activities/activity_gallery_13_698633d9d939e.jpg', 'image/jpeg', 164906, NULL, NULL, NULL, NULL, 'activities', 'activity', 13, 0, 3, 1, '2026-02-06 18:32:57', NULL),
(49, 'activity_gallery_13_698633d9daadc.png', 'MogadouroLogin.png', '/uploads/activities/activity_gallery_13_698633d9daadc.png', 'image/png', 70079, NULL, NULL, NULL, NULL, 'activities', 'activity', 13, 0, 4, 1, '2026-02-06 18:32:57', NULL),
(50, 'activity_gallery_13_698633d9dc7ec.png', 'MogadouroLogin2.png', '/uploads/activities/activity_gallery_13_698633d9dc7ec.png', 'image/png', 48057, NULL, NULL, NULL, NULL, 'activities', 'activity', 13, 0, 5, 1, '2026-02-06 18:32:57', NULL),
(51, 'activity_gallery_13_698633d9dda8c.jpeg', 'MogadouroNeve.jpeg', '/uploads/activities/activity_gallery_13_698633d9dda8c.jpeg', 'image/jpeg', 263714, NULL, NULL, NULL, NULL, 'activities', 'activity', 13, 0, 6, 1, '2026-02-06 18:32:57', NULL),
(52, 'activity_gallery_13_698633d9deee7.jpeg', 'MogadouroNeve2.jpeg', '/uploads/activities/activity_gallery_13_698633d9deee7.jpeg', 'image/jpeg', 147542, NULL, NULL, NULL, NULL, 'activities', 'activity', 13, 0, 7, 1, '2026-02-06 18:32:57', NULL),
(53, 'activity_gallery_13_698633d9dfdfc.png', 'MogadouroSobre.png', '/uploads/activities/activity_gallery_13_698633d9dfdfc.png', 'image/png', 206730, NULL, NULL, NULL, NULL, 'activities', 'activity', 13, 0, 8, 1, '2026-02-06 18:32:57', NULL),
(54, 'banana-americas-6987cc4c52fff.jpg', 'banan2.jpg', '/uploads/products/banana-americas-6987cc4c52fff.jpg', 'image/jpeg', 248844, '', '', NULL, NULL, 'products', 'standalone', NULL, 0, 0, NULL, '2026-02-07 23:35:40', NULL),
(55, 'banana-americas-6987cc4c566d0.jpg', 'bananacapa.jpg', '/uploads/products/banana-americas-6987cc4c566d0.jpg', 'image/jpeg', 524349, NULL, NULL, NULL, NULL, 'products', 'standalone', NULL, 0, 0, NULL, '2026-02-07 23:35:40', NULL),
(57, 'home_image_split_left_1770689962.png', 'Atelier Logo.png', '/uploads/content/home_image_split_left_1770689962.png', 'image/png', 1084608, NULL, NULL, NULL, NULL, 'content', 'standalone', NULL, 0, 0, 1, '2026-02-10 02:19:22', NULL),
(58, 'home_image_split_left_1770690443.jpg', 'ExpoLiveCapa.jpg', '/uploads/content/home_image_split_left_1770690443.jpg', 'image/jpeg', 237277, NULL, NULL, NULL, NULL, 'content', 'standalone', NULL, 0, 0, 1, '2026-02-10 02:27:23', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `invoice_id` int(10) UNSIGNED DEFAULT NULL,
  `order_number` varchar(20) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_nif` varchar(20) DEFAULT NULL,
  `billing_address` text NOT NULL,
  `billing_postal_code` varchar(10) NOT NULL,
  `billing_city` varchar(100) NOT NULL,
  `billing_country` varchar(2) DEFAULT 'PT',
  `shipping_same_as_billing` tinyint(1) DEFAULT 1,
  `shipping_address` text DEFAULT NULL,
  `shipping_postal_code` varchar(10) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_country` varchar(2) DEFAULT 'PT',
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_fee` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `payment_method` enum('mbway','card','multibanco','transfer') NOT NULL,
  `payment_status` enum('pending','processing','paid','failed','refunded') DEFAULT 'pending',
  `payment_reference` varchar(100) DEFAULT NULL,
  `payment_entity` varchar(10) DEFAULT NULL,
  `payment_transaction_id` varchar(255) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `tracking_code` varchar(100) DEFAULT NULL,
  `shipped_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `language` varchar(2) DEFAULT 'pt',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `orders`
--

INSERT INTO `orders` (`id`, `invoice_id`, `order_number`, `customer_name`, `customer_email`, `customer_phone`, `customer_nif`, `billing_address`, `billing_postal_code`, `billing_city`, `billing_country`, `shipping_same_as_billing`, `shipping_address`, `shipping_postal_code`, `shipping_city`, `shipping_country`, `subtotal`, `shipping_fee`, `discount_amount`, `total`, `payment_method`, `payment_status`, `payment_reference`, `payment_entity`, `payment_transaction_id`, `paid_at`, `status`, `tracking_code`, `shipped_at`, `delivered_at`, `notes`, `admin_notes`, `ip_address`, `user_agent`, `language`, `created_at`, `updated_at`) VALUES
(1, NULL, 'GI-2026-13AEC8', 'Guilherme Jose Costa Marques', 'guilherme.jcmarques@gmail.com', '965079823', NULL, 'R. Pádua Correia 166', '4430-999', 'Vila Nova de Gaia', 'PT', 1, 'R. Pádua Correia 166', '4430-999', 'Vila Nova de Gaia', 'PT', 8.00, 5.00, 0.00, 13.00, 'transfer', 'paid', NULL, NULL, NULL, '2026-02-10 17:33:14', 'delivered', NULL, NULL, '2026-02-10 17:35:46', 'Nada.', 'Convertido do pedido manual #1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'pt', '2026-02-10 17:33:14', '2026-02-10 17:35:46');

-- --------------------------------------------------------

--
-- Estrutura da tabela `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_sku` varchar(50) DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_sku`, `quantity`, `unit_price`, `total_price`) VALUES
(1, 1, 2, 'Banana das Américas Pack 5kg', '969', 1, 8.00, 8.00);

-- --------------------------------------------------------

--
-- Estrutura da tabela `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `status` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `changed_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `order_status_history`
--

INSERT INTO `order_status_history` (`id`, `order_id`, `status`, `notes`, `changed_by`, `created_at`) VALUES
(1, 1, 'confirmed', '', 1, '2026-02-10 17:35:23'),
(2, 1, 'delivered', '', 1, '2026-02-10 17:35:46');

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
(2, 'accommodation_main', 'Alojamento', 'Accommodation (Main Page)', 0.40, 1, 2, '2026-02-03 00:18:35', '2026-02-06 18:11:40'),
(3, 'activities', 'Atividades', 'Activities', 0.40, 1, 3, '2026-02-03 00:18:35', '2026-02-03 00:18:35'),
(4, 'about', 'Sobre Nós', 'About Us', 0.40, 1, 4, '2026-02-03 00:18:35', '2026-02-03 02:10:03'),
(5, 'contact', 'Contactos', 'Contact', 0.40, 1, 5, '2026-02-03 00:18:35', '2026-02-03 02:14:25'),
(6, 'shop', 'Loja', 'Shop', 0.40, 1, 6, '2026-02-03 00:18:35', '2026-02-03 00:18:35'),
(8, 'product_detail', 'Produto (Detalhe)', 'Product (Detail)', 0.40, 1, 7, '2026-02-08 18:45:41', '2026-02-08 18:45:41'),
(9, 'cart', 'Carrinho de Compras', 'Shopping Cart', 0.40, 1, 8, '2026-02-08 18:45:42', '2026-02-08 18:45:42'),
(10, 'checkout', 'Finalizar Compra', 'Checkout', 0.40, 1, 9, '2026-02-08 18:45:42', '2026-02-08 18:45:42'),
(11, 'privacy_policy', 'Política de Privacidade', 'Privacy Policy', 0.40, 1, 10, '2026-02-09 20:08:12', '2026-02-10 21:37:27'),
(12, 'terms_conditions', 'Termos e Condições', 'Terms and Conditions', 0.40, 1, 11, '2026-02-09 20:08:12', '2026-02-10 21:38:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT -1,
  `track_inventory` tinyint(1) DEFAULT 1,
  `weight` decimal(8,3) DEFAULT NULL,
  `weight_grams` int(10) UNSIGNED DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `products`
--

INSERT INTO `products` (`id`, `sku`, `slug`, `category_id`, `price`, `sale_price`, `stock_quantity`, `track_inventory`, `weight`, `weight_grams`, `is_featured`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(2, '969', 'banana-americas', 6, 8.00, NULL, 10, 1, 5.000, NULL, 1, 1, 0, '2026-02-07 23:35:40', '2026-02-10 16:59:02');

-- --------------------------------------------------------

--
-- Estrutura da tabela `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `product_categories`
--

INSERT INTO `product_categories` (`id`, `slug`, `image`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'mel', NULL, 1, 1, '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(2, 'azeite', NULL, 2, 1, '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(3, 'vinho', NULL, 3, 1, '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(4, 'enchidos', NULL, 4, 1, '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(5, 'queijos', NULL, 5, 1, '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(6, 'doces', NULL, 6, 1, '2026-01-19 12:51:19', '2026-01-19 12:51:19');

-- --------------------------------------------------------

--
-- Estrutura da tabela `product_category_translations`
--

CREATE TABLE `product_category_translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `language_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `product_category_translations`
--

INSERT INTO `product_category_translations` (`id`, `category_id`, `language_id`, `name`, `description`) VALUES
(1, 1, 1, 'Mel', 'Mel da regiao de Tras-os-Montes'),
(2, 1, 2, 'Honey', 'Honey from Tras-os-Montes region'),
(3, 2, 1, 'Azeite', 'Azeite do vale do Sabor'),
(4, 2, 2, 'Olive Oil', 'Olive oil from Sabor valley'),
(5, 3, 1, 'Vinho', 'Vinhos da regiao do Douro'),
(6, 3, 2, 'Wine', 'Wines from Douro region'),
(7, 4, 1, 'Enchidos', 'Enchidos tradicionais transmontanos'),
(8, 4, 2, 'Cured Meats', 'Traditional Transmontano cured meats'),
(9, 5, 1, 'Queijos', 'Queijos de ovelha e cabra'),
(10, 5, 2, 'Cheeses', 'Sheep and goat cheeses'),
(11, 6, 1, 'Doces', 'Doces e bolos tradicionais'),
(12, 6, 2, 'Sweets', 'Traditional sweets and cakes');

-- --------------------------------------------------------

--
-- Estrutura da tabela `product_images`
--

CREATE TABLE `product_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `media_id` int(10) UNSIGNED NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `sort_order` int(10) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `media_id`, `is_primary`, `sort_order`) VALUES
(1, 2, 54, 1, 0),
(2, 2, 55, 0, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `product_translations`
--

CREATE TABLE `product_translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `language_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `short_description` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `full_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `product_translations`
--

INSERT INTO `product_translations` (`id`, `product_id`, `language_id`, `name`, `short_description`, `description`, `full_description`) VALUES
(1, 2, 1, 'Banana das Américas Pack 5kg', 'Boa qualidade da Quinta do Zé', 'Apanhadas por crianças de 12 anos pagas 8 Reais por hora a trabalharem 12 horas por dia para chegarem para a sua mesa.', NULL),
(2, 2, 2, 'American Bananas', 'Boa qualidade da Quinta do Zé', 'Apanhadas por crianças de 12 anos pagas 8 Reais por hora a trabalharem 12 horas por dia para chegarem para a sua mesa.', NULL);

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
(6, 'contact_address', 'Rua Principal, 123\n5200 Mogadouro\nPortugal', 'textarea', 'contact', 'Morada', 1, '2026-01-19 12:51:19', '2026-01-20 16:22:55'),
(7, 'contact_form_enabled', '1', 'boolean', 'contact', 'Formulario ativo', 0, '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(8, 'facebook_url', 'https://facebook.com/acasadogi', 'url', 'social', 'URL Facebook', 1, '2026-01-19 12:51:19', '2026-01-20 16:22:55'),
(9, 'instagram_url', 'https://instagram.com/acasadogi', 'url', 'social', 'URL Instagram', 1, '2026-01-19 12:51:19', '2026-01-20 16:22:55'),
(10, 'booking_url', 'https://www.booking.com/', 'url', 'booking', 'URL Booking.com', 1, '2026-01-19 12:51:19', '2026-01-20 16:22:55'),
(11, 'airbnb_url', 'https://www.airbnb.com/', 'url', 'booking', 'URL Airbnb', 1, '2026-01-19 12:51:19', '2026-01-20 16:22:55'),
(12, 'guestready_url', 'https://www.guestready.com/en-pt/rentals/quinta-de-mouraes-tbd/', 'url', 'booking', 'URL GuestReady', 1, '2026-01-19 12:51:19', '2026-01-20 16:22:55'),
(13, 'shop_enabled', '1', 'boolean', 'shop', 'Loja ativa', 0, '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(14, 'shop_shipping_fee', '5.00', 'number', 'shop', 'Taxa de envio', 0, '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(15, 'shop_free_shipping_above', '50.00', 'number', 'shop', 'Portes gratis acima de', 0, '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(16, 'maintenance_mode', '0', 'boolean', 'general', 'Modo manutencao', 0, '2026-01-19 12:51:19', '2026-02-11 02:57:51'),
(17, 'free_shipping_threshold', '50', 'number', 'shop', NULL, 0, '2026-01-20 16:22:55', '2026-01-20 16:22:55'),
(18, 'shipping_cost', '5', 'number', 'shop', NULL, 0, '2026-01-20 16:22:55', '2026-01-20 16:22:55'),
(82, 'shop_mode', 'manual', 'text', 'shop', 'Modo da loja: active, manual, closed', 0, '2026-02-07 20:01:43', '2026-02-09 00:34:35'),
(83, 'site_description', '', 'text', 'general', 'Descrição (SEO)', 0, '2026-02-11 02:38:34', '2026-02-11 02:38:34');

-- --------------------------------------------------------

--
-- Estrutura da tabela `spam_emails`
--

CREATE TABLE `spam_emails` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `reason` varchar(500) DEFAULT NULL COMMENT 'Why this email was marked as spam',
  `blocked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura stand-in para vista `v_activity_media`
-- (Veja abaixo para a view atual)
--
CREATE TABLE `v_activity_media` (
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
,`category` enum('gallery','products','activities','content','other')
,`entity_type` enum('activity','hero','accommodation','product','standalone','other')
,`entity_id` int(10) unsigned
,`is_cover` tinyint(1)
,`sort_order` int(10) unsigned
,`uploaded_by` int(10) unsigned
,`created_at` timestamp
,`accommodation_id` int(10) unsigned
,`activity_slug` varchar(255)
,`activity_title` varchar(255)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para vista `v_categories_with_translations`
-- (Veja abaixo para a view atual)
--
CREATE TABLE `v_categories_with_translations` (
`id` int(10) unsigned
,`type` enum('activity','product')
,`slug` varchar(100)
,`icon` varchar(50)
,`sort_order` int(11)
,`is_active` tinyint(1)
,`language_id` int(10) unsigned
,`language_code` varchar(5)
,`name` varchar(100)
,`description` text
);

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
,`category` enum('gallery','products','activities','content','other')
,`entity_type` enum('activity','hero','accommodation','product','standalone','other')
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
-- Estrutura para vista `v_activity_media`
--
DROP TABLE IF EXISTS `v_activity_media`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_activity_media`  AS SELECT `m`.`id` AS `id`, `m`.`filename` AS `filename`, `m`.`original_name` AS `original_name`, `m`.`file_path` AS `file_path`, `m`.`file_type` AS `file_type`, `m`.`file_size` AS `file_size`, `m`.`alt_text_pt` AS `alt_text_pt`, `m`.`alt_text_en` AS `alt_text_en`, `m`.`caption_pt` AS `caption_pt`, `m`.`caption_en` AS `caption_en`, `m`.`category` AS `category`, `m`.`entity_type` AS `entity_type`, `m`.`entity_id` AS `entity_id`, `m`.`is_cover` AS `is_cover`, `m`.`sort_order` AS `sort_order`, `m`.`uploaded_by` AS `uploaded_by`, `m`.`created_at` AS `created_at`, `m`.`accommodation_id` AS `accommodation_id`, `a`.`slug` AS `activity_slug`, `at`.`title` AS `activity_title` FROM ((`media` `m` join `activities` `a` on(`m`.`entity_id` = `a`.`id`)) left join `activity_translations` `at` on(`a`.`id` = `at`.`activity_id` and `at`.`language_id` = 1)) WHERE `m`.`entity_type` = 'activity' ;

-- --------------------------------------------------------

--
-- Estrutura para vista `v_categories_with_translations`
--
DROP TABLE IF EXISTS `v_categories_with_translations`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_categories_with_translations`  AS SELECT `c`.`id` AS `id`, `c`.`type` AS `type`, `c`.`slug` AS `slug`, `c`.`icon` AS `icon`, `c`.`sort_order` AS `sort_order`, `c`.`is_active` AS `is_active`, `ct`.`language_id` AS `language_id`, `l`.`code` AS `language_code`, `ct`.`name` AS `name`, `ct`.`description` AS `description` FROM ((`categories` `c` left join `category_translations` `ct` on(`c`.`id` = `ct`.`category_id`)) left join `languages` `l` on(`ct`.`language_id` = `l`.`id`)) WHERE `c`.`is_active` = 1 ORDER BY `c`.`type` ASC, `c`.`sort_order` ASC, `ct`.`language_id` ASC ;

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
-- Índices para tabela `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_activities_slug` (`slug`),
  ADD KEY `idx_activities_category` (`category`),
  ADD KEY `idx_activities_featured_active` (`is_featured`,`is_active`);

--
-- Índices para tabela `activity_translations`
--
ALTER TABLE `activity_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_act_lang` (`activity_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

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
-- Índices para tabela `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin` (`admin_id`),
  ADD KEY `idx_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created` (`created_at`);

--
-- Índices para tabela `barcode_batches`
--
ALTER TABLE `barcode_batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `batch_number` (`batch_number`),
  ADD KEY `idx_batch_active` (`is_active`);

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
-- Índices para tabela `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_slug_type` (`slug`,`type`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_active` (`is_active`);

--
-- Índices para tabela `category_translations`
--
ALTER TABLE `category_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_category_language` (`category_id`,`language_id`),
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
-- Índices para tabela `external_links`
--
ALTER TABLE `external_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_external_links_active` (`is_active`),
  ADD KEY `idx_external_links_featured` (`is_featured`),
  ADD KEY `idx_external_links_order` (`sort_order`);

--
-- Índices para tabela `external_link_translations`
--
ALTER TABLE `external_link_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_link_language` (`link_id`,`language_id`),
  ADD KEY `idx_external_link_trans_link` (`link_id`),
  ADD KEY `idx_external_link_trans_lang` (`language_id`);

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
-- Índices para tabela `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_uuid` (`invoice_uuid`),
  ADD UNIQUE KEY `barcode` (`barcode`),
  ADD KEY `idx_invoice_barcode` (`barcode`),
  ADD KEY `idx_invoice_uuid` (`invoice_uuid`),
  ADD KEY `idx_invoice_order` (`order_id`),
  ADD KEY `idx_invoice_email` (`customer_email`),
  ADD KEY `idx_invoice_status` (`payment_status`),
  ADD KEY `idx_invoice_date` (`issued_at`);

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
-- Índices para tabela `manual_orders`
--
ALTER TABLE `manual_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_manual_status` (`status`),
  ADD KEY `idx_manual_date` (`created_at`),
  ADD KEY `idx_manual_email` (`customer_email`);

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
-- Índices para tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `idx_order_number` (`order_number`),
  ADD KEY `idx_customer_email` (`customer_email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Índices para tabela `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_order` (`order_id`);

--
-- Índices para tabela `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `changed_by` (`changed_by`),
  ADD KEY `idx_order` (`order_id`);

--
-- Índices para tabela `page_heroes`
--
ALTER TABLE `page_heroes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_key` (`page_key`),
  ADD KEY `idx_page_key` (`page_key`),
  ADD KEY `idx_active` (`is_active`);

--
-- Índices para tabela `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_featured` (`is_featured`);

--
-- Índices para tabela `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_active` (`is_active`);

--
-- Índices para tabela `product_category_translations`
--
ALTER TABLE `product_category_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cat_lang` (`category_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Índices para tabela `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_id` (`media_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_primary` (`is_primary`);

--
-- Índices para tabela `product_translations`
--
ALTER TABLE `product_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_prod_lang` (`product_id`,`language_id`),
  ADD KEY `language_id` (`language_id`);

--
-- Índices para tabela `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_key` (`setting_key`),
  ADD KEY `idx_group` (`setting_group`);

--
-- Índices para tabela `spam_emails`
--
ALTER TABLE `spam_emails`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

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
-- AUTO_INCREMENT de tabela `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `activity_translations`
--
ALTER TABLE `activity_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

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
-- AUTO_INCREMENT de tabela `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT de tabela `barcode_batches`
--
ALTER TABLE `barcode_batches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `bathrooms`
--
ALTER TABLE `bathrooms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `bathroom_translations`
--
ALTER TABLE `bathroom_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `bedrooms`
--
ALTER TABLE `bedrooms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `bedroom_translations`
--
ALTER TABLE `bedroom_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de tabela `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de tabela `category_translations`
--
ALTER TABLE `category_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de tabela `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `content_blocks`
--
ALTER TABLE `content_blocks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=469;

--
-- AUTO_INCREMENT de tabela `external_links`
--
ALTER TABLE `external_links`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `external_link_translations`
--
ALTER TABLE `external_link_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `house_rules`
--
ALTER TABLE `house_rules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de tabela `house_rule_translations`
--
ALTER TABLE `house_rule_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de tabela `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `legal_sections`
--
ALTER TABLE `legal_sections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `legal_section_translations`
--
ALTER TABLE `legal_section_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `manual_orders`
--
ALTER TABLE `manual_orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `media`
--
ALTER TABLE `media`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `page_heroes`
--
ALTER TABLE `page_heroes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `product_category_translations`
--
ALTER TABLE `product_category_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `product_translations`
--
ALTER TABLE `product_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT de tabela `spam_emails`
--
ALTER TABLE `spam_emails`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Limitadores para a tabela `activity_translations`
--
ALTER TABLE `activity_translations`
  ADD CONSTRAINT `activity_translations_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `amenity_translations`
--
ALTER TABLE `amenity_translations`
  ADD CONSTRAINT `amenity_translations_ibfk_1` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `amenity_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

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
-- Limitadores para a tabela `category_translations`
--
ALTER TABLE `category_translations`
  ADD CONSTRAINT `category_translations_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `content_blocks`
--
ALTER TABLE `content_blocks`
  ADD CONSTRAINT `content_blocks_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `external_link_translations`
--
ALTER TABLE `external_link_translations`
  ADD CONSTRAINT `fk_external_link_trans_lang` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_external_link_trans_link` FOREIGN KEY (`link_id`) REFERENCES `external_links` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Limitadores para a tabela `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_invoice_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

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

--
-- Limitadores para a tabela `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `order_status_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_status_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `product_category_translations`
--
ALTER TABLE `product_category_translations`
  ADD CONSTRAINT `product_category_translations_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_category_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_images_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `product_translations`
--
ALTER TABLE `product_translations`
  ADD CONSTRAINT `product_translations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
