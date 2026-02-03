-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 02-Fev-2026 às 22:29
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
  `airbnb_url` varchar(500) DEFAULT NULL COMMENT 'Airbnb URL'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `accommodation`
--

INSERT INTO `accommodation` (`id`, `slug`, `max_guests`, `bedrooms`, `bathrooms`, `area_sqm`, `floor_number`, `has_elevator`, `check_in_time`, `check_out_time`, `latitude`, `longitude`, `license_number`, `created_at`, `updated_at`, `is_active`, `rating`, `reviews_count`, `city`, `region`, `country`, `host_type`, `checkin_type`, `checkin_instructions`, `towels_linens_included`, `min_nights`, `instant_booking`, `accommodation_number`, `guestready_url`, `booking_url`, `airbnb_url`) VALUES
(1, 'casa-do-gi-1', 6, 3, 2, 100.00, 1, 0, '16:00:00', '11:00:00', 41.34217000, -6.71347000, '146729/AL', '2026-01-19 12:51:19', '2026-01-30 02:30:59', 1, 4.8, 127, 'Mogadouro', 'Trás-os-Montes', 'Portugal', 'superhost', 'self_checkin', NULL, 1, 2, 1, 1, 'https://book.guestready.com/pt/properties/mogadouro/fuga-ecletica-em-mogadouro/72622?adults=1&amp;children=0&amp;infants=0&amp;checkin=&amp;checkout=', '', ''),
(2, 'casa-do-gi-2', 6, 3, 2, 100.00, 1, 0, '16:00:00', '11:00:00', 41.34217000, -6.71347000, '146729/AL', '2026-01-30 02:22:49', '2026-01-30 02:22:49', 1, 4.8, 127, 'Mogadouro', 'Trás-os-Montes', 'Portugal', 'superhost', 'self_checkin', NULL, 1, 2, 1, 2, NULL, NULL, NULL);

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
(1, 1, 1, 1),
(1, 2, 1, 2),
(1, 3, 1, 3),
(1, 4, 1, 4),
(1, 5, 1, 23),
(1, 6, 1, 24),
(1, 7, 1, 25),
(1, 8, 1, 26),
(1, 9, 0, 5),
(1, 10, 0, 7),
(1, 11, 0, 19),
(1, 12, 0, 6),
(1, 13, 0, 8),
(1, 14, 0, 9),
(1, 15, 0, 10),
(1, 16, 0, 11),
(1, 17, 0, 12),
(1, 18, 0, 13),
(1, 19, 0, 14),
(1, 20, 0, 15),
(1, 21, 0, 16),
(1, 22, 0, 17),
(1, 23, 0, 18),
(1, 24, 0, 20),
(1, 25, 0, 21),
(1, 26, 0, 22),
(1, 27, 0, 31),
(1, 28, 0, 32),
(1, 29, 0, 33),
(1, 30, 0, 34),
(1, 31, 0, 35),
(1, 32, 0, 36),
(1, 33, 0, 37),
(1, 34, 0, 38),
(1, 35, 0, 27),
(1, 36, 0, 28),
(1, 37, 0, 29),
(1, 38, 0, 30),
(1, 39, 0, 39),
(1, 40, 0, 40),
(2, 1, 1, 1),
(2, 2, 1, 2),
(2, 3, 1, 3),
(2, 4, 1, 4),
(2, 5, 1, 5),
(2, 6, 1, 6),
(2, 7, 1, 7),
(2, 8, 1, 8),
(2, 9, 0, 0),
(2, 10, 0, 0),
(2, 11, 0, 0),
(2, 12, 0, 0),
(2, 13, 0, 0),
(2, 14, 0, 0),
(2, 15, 0, 0),
(2, 16, 0, 0),
(2, 17, 0, 0),
(2, 18, 0, 0),
(2, 19, 0, 0),
(2, 20, 0, 0),
(2, 21, 0, 0),
(2, 22, 0, 0),
(2, 23, 0, 0),
(2, 24, 0, 0),
(2, 25, 0, 0),
(2, 26, 0, 0),
(2, 27, 0, 0),
(2, 28, 0, 0),
(2, 29, 0, 0),
(2, 30, 0, 0),
(2, 31, 0, 0),
(2, 32, 0, 0),
(2, 33, 0, 0),
(2, 34, 0, 0),
(2, 35, 0, 0),
(2, 36, 0, 0),
(2, 37, 0, 0),
(2, 38, 0, 0),
(2, 39, 0, 0),
(2, 40, 0, 0);

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
(1, 1, 1, 'A Casa do Gi', 'Casa de ferias de 100m2, andar nr 1, sem elevador', 'A Casa do Gi e sinonimo de simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor! Construida nos anos 80, altura em que os artistas da construcao e os materiais eram escassos por Terras de Mogadouro.', NULL, 'A Casa do Gi', '', '', 'Localizado no coração de Mogadouro, a poucos passos do centro histórico. A zona é tranquila e segura, ideal para famílias. Perto de restaurantes, supermercados e dos principais pontos turísticos da região de Trás-os-Montes.', 'Cancelamento gratuito até 7 dias antes do check-in. Após essa data, será cobrado o valor da primeira noite. Não comparência resulta em cobrança total.', 'Self check-in com cofre de chaves. Instruções enviadas 24h antes da chegada.', 'Somos uma família local apaixonada por Mogadouro e Trás-os-Montes. Adoramos partilhar a nossa terra com visitantes de todo o mundo.', 'Cancelamento gratuito até 30 dias antes do check-in. Cancelamentos após este período sujeitos a taxas de acordo com a plataforma de reserva.', 'Mogadouro &amp; Envolvência', 'Mogadouro é uma vila histórica no coração do Planalto Mirandês, onde a tradição se funde com a natureza. A partir da Casa do Gi, poderá explorar o Castelo de Mogadouro, percorrer trilhos no Parque Natural do Douro Internacional e saborear a gastronomia local única.'),
(2, 1, 2, 'A Casa do Gi', 'Holiday home of 100m2, 1st floor, no elevator', 'A Casa do Gi is synonymous with simplicity, welcoming, remarkable moments of conviviality, warmth of family, joy, fun, laughter and a lot of love! Built in the 80s, when construction artists and materials were scarce in the lands of Mogadouro.', NULL, '', 'Um espaço pensado para proporcionar momentos de tranquilidade e bem-estar no coração de Trás-os-Montes.', '', 'Located in the heart of Mogadouro, just steps from the historic center. The area is quiet and safe, ideal for families. Close to restaurants, supermarkets and the main tourist attractions of the Trás-os-Montes region.', 'Free cancellation up to 7 days before check-in. After that date, the first night will be charged. No-show results in full charge.', 'Self check-in with lockbox. Instructions sent 24h before arrival.', 'We are a local family passionate about Mogadouro and Trás-os-Montes. We love sharing our land with visitors from all over the world.', 'Free cancellation up to 30 days before check-in. Cancellations after this period subject to fees according to the booking platform.', 'Mogadouro &amp; Surroundings', 'Mogadouro is a historic town in the heart of the Mirandês Plateau, where tradition merges with nature. From Casa do Gi, you can explore Mogadouro Castle, walk trails in the Douro International Natural Park and savor the unique local gastronomy.'),
(3, 2, 1, '', NULL, NULL, NULL, 'A Casa do Gi 2', '', '', 'Localizado no coração de Mogadouro, a poucos passos do centro histórico. A zona é tranquila e segura, ideal para famílias. Perto de restaurantes, supermercados e dos principais pontos turísticos da região de Trás-os-Montes.', 'Cancelamento gratuito até 7 dias antes do check-in. Após essa data, será cobrado o valor da primeira noite. Não comparência resulta em cobrança total.', 'Self check-in com cofre de chaves. Instruções enviadas 24h antes da chegada.', 'Somos uma família local apaixonada por Mogadouro e Trás-os-Montes. Adoramos partilhar a nossa terra com visitantes de todo o mundo.', 'Cancelamento gratuito até 30 dias antes do check-in. Cancelamentos após este período sujeitos a taxas de acordo com a plataforma de reserva.', 'Mogadouro & Envolvência', 'Mogadouro é uma vila histórica no coração do Planalto Mirandês, onde a tradição se funde com a natureza. A partir da Casa do Gi, poderá explorar o Castelo de Mogadouro, percorrer trilhos no Parque Natural do Douro Internacional e saborear a gastronomia local única.'),
(4, 2, 2, '', NULL, NULL, NULL, ' 2', 'Um espaço pensado para proporcionar momentos de tranquilidade e bem-estar no coração de Trás-os-Montes.', '', 'Located in the heart of Mogadouro, just steps from the historic center. The area is quiet and safe, ideal for families. Close to restaurants, supermarkets and the main tourist attractions of the Trás-os-Montes region.', 'Free cancellation up to 7 days before check-in. After that date, the first night will be charged. No-show results in full charge.', 'Self check-in with lockbox. Instructions sent 24h before arrival.', 'We are a local family passionate about Mogadouro and Trás-os-Montes. We love sharing our land with visitors from all over the world.', 'Free cancellation up to 30 days before check-in. Cancellations after this period subject to fees according to the booking platform.', 'Mogadouro & Surroundings', 'Mogadouro is a historic town in the heart of the Mirandês Plateau, where tradition merges with nature. From Casa do Gi, you can explore Mogadouro Castle, walk trails in the Douro International Natural Park and savor the unique local gastronomy.');

-- --------------------------------------------------------

--
-- Estrutura da tabela `activities`
--

CREATE TABLE `activities` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` enum('nature','culture','gastronomy','adventure','wellness','events') DEFAULT 'culture',
  `external_url` varchar(500) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `distance_km` decimal(5,2) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `activities`
--

INSERT INTO `activities` (`id`, `slug`, `image`, `category`, `external_url`, `latitude`, `longitude`, `distance_km`, `is_featured`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'castelo-mogadouro', NULL, 'culture', NULL, NULL, NULL, 0.50, 1, 1, 1, '2026-01-19 12:51:20', '2026-01-19 12:51:20'),
(2, 'miradouro-serpente-medal', NULL, 'nature', NULL, NULL, NULL, 15.00, 1, 1, 2, '2026-01-19 12:51:20', '2026-01-19 12:51:20'),
(3, 'parque-natural-douro', NULL, 'nature', NULL, NULL, NULL, 20.00, 1, 1, 3, '2026-01-19 12:51:20', '2026-01-19 12:51:20'),
(4, 'museu-mogadouro', NULL, 'culture', NULL, NULL, NULL, 0.30, 0, 1, 4, '2026-01-19 12:51:20', '2026-01-19 12:51:20'),
(5, 'igreja-matriz', NULL, 'culture', NULL, NULL, NULL, 0.20, 0, 1, 5, '2026-01-19 12:51:20', '2026-01-19 12:51:20');

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
  `full_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `activity_translations`
--

INSERT INTO `activity_translations` (`id`, `activity_id`, `language_id`, `title`, `short_description`, `full_description`) VALUES
(1, 1, 1, 'Castelo de Mogadouro', 'Castelo do seculo XIII com vista panoramica da regiao', NULL),
(2, 1, 2, 'Mogadouro Castle', '13th century castle with panoramic views of the region', NULL),
(3, 2, 1, 'Miradouro Serpente do Medal', 'Vista panoramica sobre o rio Douro nas Arribas', NULL),
(4, 2, 2, 'Serpente do Medal Viewpoint', 'Panoramic view over the Douro river in the Arribas', NULL),
(5, 3, 1, 'Parque Natural do Douro Internacional', 'Area protegida com aguias e abutres', NULL),
(6, 3, 2, 'Douro International Natural Park', 'Protected area with eagles and vultures', NULL),
(7, 4, 1, 'Museu de Mogadouro', 'Historia e tradicoes da regiao', NULL),
(8, 4, 2, 'Mogadouro Museum', 'History and traditions of the region', NULL),
(9, 5, 1, 'Igreja Matriz de Mogadouro', 'Igreja de origem romanica no centro historico', NULL),
(10, 5, 2, 'Mogadouro Main Church', 'Romanesque origin church in the historic center', NULL);

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
(1, 'admin', 'admin@acasadogi.pt', '$2y$12$95JaIzBEov7tZz0SfnUwPOIecK1ujIWqumI74Ndw.e2RHwm/FpVqy', 'Administrador', 'super_admin', NULL, 1, '2026-02-02 21:17:58', 0, NULL, NULL, NULL, '2026-01-19 12:51:19', '2026-02-02 21:17:58');

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
(22, 1, 'login', 'admin', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 21:17:58');

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
(1, 1, 1, 0, 1, 1, 1, '2026-01-26 22:31:24', NULL),
(2, 1, 2, 0, 1, 0, 0, '2026-01-26 22:31:24', NULL),
(3, 2, 1, 0, 1, 1, 1, '2026-01-30 02:22:50', NULL),
(4, 2, 2, 0, 1, 0, 0, '2026-01-30 02:22:50', NULL);

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
(1, 1, 1, 'Casa de Banho Principal', 'Banheira, chuveiro, bidé, secador de cabelo', 'Higiene'),
(2, 1, 2, 'Main Bathroom', 'Bathtub, shower, bidet, hair dryer', 'Bathrooms'),
(3, 2, 1, 'Casa de Banho Secundária', 'Chuveiro, lavatório', 'Higiene'),
(4, 2, 2, 'Secondary Bathroom', 'Shower, sink', 'Bathrooms'),
(5, 3, 1, 'Casa de Banho Principal', 'Banheira, chuveiro, bidé, secador de cabelo', 'Higiene'),
(6, 3, 2, 'Main Bathroom', 'Bathtub, shower, bidet, hair dryer', 'Bathrooms'),
(7, 4, 1, 'Casa de Banho Secundária', 'Chuveiro, lavatório', 'Higiene'),
(8, 4, 2, 'Secondary Bathroom', 'Shower, sink', 'Bathrooms');

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
(1, 1, 1, '2026-01-19 12:51:19', NULL),
(2, 1, 2, '2026-01-19 12:51:19', NULL),
(3, 1, 3, '2026-01-19 12:51:19', NULL),
(4, 2, 1, '2026-01-30 02:22:50', NULL),
(5, 2, 2, '2026-01-30 02:22:50', NULL),
(6, 2, 3, '2026-01-30 02:22:50', NULL);

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
(1, 1, 1, '2 camas de solteiro', 'Quarto Principal', 'Dormidas'),
(2, 1, 2, '2 single beds', 'Master Bedroom', 'Sleeping Arrangements'),
(3, 2, 1, 'Sofa-cama de solteiro, Cama de casal', 'Quarto Duplo', 'Dormidas'),
(4, 2, 2, 'Single sofa bed, Double bed', 'Twin Room', 'Sleeping Arrangements'),
(5, 3, 1, 'Cama de casal', 'Quarto de Hóspedes', 'Dormidas'),
(6, 3, 2, 'Double bed', 'Guest Room', 'Sleeping Arrangements'),
(7, 4, 1, '2 camas de solteiro', 'Quarto Principal', 'Dormidas'),
(8, 4, 2, '2 single beds', 'Master Bedroom', 'Sleeping Arrangements'),
(9, 5, 1, 'Sofa-cama de solteiro, Cama de casal', 'Quarto Duplo', 'Dormidas'),
(10, 5, 2, 'Single sofa bed, Double bed', 'Twin Room', 'Sleeping Arrangements'),
(11, 6, 1, 'Cama de casal', 'Quarto de Hóspedes', 'Dormidas'),
(12, 6, 2, 'Double bed', 'Guest Room', 'Sleeping Arrangements');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'hero_title', 1, 'text', 'A Casa do Gi', 'home', 'hero', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(2, 'hero_subtitle', 1, 'textarea', 'Simplicidade, acolhimento e muito amor em Mogadouro', 'home', 'hero', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(3, 'hero_cta', 1, 'text', 'Descobrir', 'home', 'hero', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(4, 'about_title', 1, 'text', 'A Nossa Historia', 'home', 'about', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(5, 'about_text', 1, 'html', '<p>Construida nos anos 80, altura em que os \"artistas da construcao\" e os \"materiais\" eram escassos por Terras de Mogadouro, este edificio foi mandado construir desde terras de Santa Cruz, por carta, e com os recursos de quem saiu da terra em busca de uma melhor oportunidade!</p><p>A Casa do Gi... e sinonimo de simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor!</p>', 'home', 'about', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(6, 'accommodation_title', 1, 'text', 'O Alojamento', 'accommodation', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(7, 'accommodation_intro', 1, 'textarea', 'Fuga ecletica em Mogadouro - Casa de ferias de 100m2, perfeita para 6 hospedes', 'accommodation', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(8, 'shop_title', 1, 'text', 'Produtos Regionais', 'shop', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(9, 'shop_intro', 1, 'textarea', 'Descubra os sabores autenticos de Mogadouro e Tras-os-Montes', 'shop', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(10, 'activities_title', 1, 'text', 'O Que Fazer', 'activities', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(11, 'activities_intro', 1, 'textarea', 'Descubra as maravilhas de Mogadouro e arredores', 'activities', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(12, 'contact_title', 1, 'text', 'Contacte-nos', 'contact', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(13, 'contact_intro', 1, 'textarea', 'Tem alguma questao? Entre em contacto connosco', 'contact', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(14, 'hero_title', 2, 'text', 'A Casa do Gi', 'home', 'hero', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(15, 'hero_subtitle', 2, 'textarea', 'Simplicity, warmth and love in Mogadouro', 'home', 'hero', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(16, 'hero_cta', 2, 'text', 'Discover', 'home', 'hero', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(17, 'about_title', 2, 'text', 'Our Story', 'home', 'about', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(18, 'about_text', 2, 'html', '<p>Built in the 80s, when \"construction artists\" and \"materials\" were scarce in the lands of Mogadouro, this building was commissioned from the lands of Santa Cruz, by letter, and with the resources of those who left the land in search of a better opportunity!</p><p>A Casa do Gi... is synonymous with simplicity, welcoming, remarkable moments of conviviality, warmth of family, joy, fun, laughter and a lot of love!</p>', 'home', 'about', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(19, 'accommodation_title', 2, 'text', 'The Accommodation', 'accommodation', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(20, 'accommodation_intro', 2, 'textarea', 'Eclectic getaway in Mogadouro - 100m2 holiday home, perfect for 6 guests', 'accommodation', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(21, 'shop_title', 2, 'text', 'Regional Products', 'shop', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(22, 'shop_intro', 2, 'textarea', 'Discover the authentic flavors of Mogadouro and Tras-os-Montes', 'shop', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(23, 'activities_title', 2, 'text', 'Things To Do', 'activities', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(24, 'activities_intro', 2, 'textarea', 'Discover the wonders of Mogadouro and surroundings', 'activities', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(25, 'contact_title', 2, 'text', 'Contact Us', 'contact', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(26, 'contact_intro', 2, 'textarea', 'Have a question? Get in touch with us', 'contact', 'main', '2026-01-19 12:51:19', '2026-01-19 12:51:19');

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
(1, 1, 1, 1, '2026-01-30 02:22:49'),
(2, 1, 1, 2, '2026-01-30 02:22:49'),
(3, 1, 1, 3, '2026-01-30 02:22:49'),
(4, 1, 0, 4, '2026-01-30 02:22:49'),
(5, 1, 0, 5, '2026-01-30 02:22:49'),
(6, 2, 1, 1, '2026-01-30 02:22:50'),
(7, 2, 1, 2, '2026-01-30 02:22:50'),
(8, 2, 1, 3, '2026-01-30 02:22:50'),
(9, 2, 0, 4, '2026-01-30 02:22:50'),
(10, 2, 0, 5, '2026-01-30 02:22:50');

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
(1, 1, 1, 'Não são permitidas festas ou eventos.'),
(2, 1, 2, 'No parties or events allowed.'),
(3, 2, 1, 'Horário de silêncio: 22h00 - 08h00.'),
(4, 2, 2, 'Quiet hours: 22:00 - 08:00.'),
(5, 3, 1, 'Proibido fumar no interior.'),
(6, 3, 2, 'No smoking inside.'),
(7, 4, 1, 'Animais de estimação não são permitidos.'),
(8, 4, 2, 'Pets are not allowed.'),
(9, 5, 1, 'Respeite os vizinhos e a propriedade.'),
(10, 5, 2, 'Respect neighbors and property.'),
(11, 6, 1, 'Não são permitidas festas ou eventos.'),
(12, 6, 2, 'No parties or events allowed.'),
(13, 7, 1, 'Horário de silêncio: 22h00 - 08h00.'),
(14, 7, 2, 'Quiet hours: 22:00 - 08:00.'),
(15, 8, 1, 'Proibido fumar no interior.'),
(16, 8, 2, 'No smoking inside.'),
(17, 9, 1, 'Animais de estimação não são permitidos.'),
(18, 9, 2, 'Pets are not allowed.'),
(19, 10, 1, 'Respeite os vizinhos e a propriedade.'),
(20, 10, 2, 'Respect neighbors and property.');

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
  `category` enum('gallery','products','activities','content','other') DEFAULT 'other',
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `uploaded_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `accommodation_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Link to specific accommodation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `media`
--

INSERT INTO `media` (`id`, `filename`, `original_name`, `file_path`, `file_type`, `file_size`, `alt_text_pt`, `alt_text_en`, `category`, `sort_order`, `uploaded_by`, `created_at`, `accommodation_id`) VALUES
(8, '6976756e94c17_1769370990.jpg', 'AlojamentoQuarto8.jpg', '/uploads/media/6976756e94c17_1769370990.jpg', 'image/jpeg', 77675, '', '', 'gallery', 0, 1, '2026-01-25 19:56:30', 1),
(9, '6976756e992d1_1769370990.jpg', 'AlojamentoQuarto7.jpg', '/uploads/media/6976756e992d1_1769370990.jpg', 'image/jpeg', 78878, '', '', 'gallery', 0, 1, '2026-01-25 19:56:30', 1),
(10, '6976756e9adb4_1769370990.jpg', 'AlojamentoQuarto6.jpg', '/uploads/media/6976756e9adb4_1769370990.jpg', 'image/jpeg', 86814, '', '', 'gallery', 0, 1, '2026-01-25 19:56:30', 1),
(11, '6976756e9c308_1769370990.jpg', 'AlojamentoQuarto5.jpg', '/uploads/media/6976756e9c308_1769370990.jpg', 'image/jpeg', 82471, '', '', 'gallery', 0, 1, '2026-01-25 19:56:30', 1),
(12, '6976756e9d47d_1769370990.jpg', 'AlojamentoQuarto4.jpg', '/uploads/media/6976756e9d47d_1769370990.jpg', 'image/jpeg', 100462, '', '', 'gallery', 0, 1, '2026-01-25 19:56:30', 1),
(13, '6976756e9e5a6_1769370990.jpg', 'AlojamentoQuarto3.jpg', '/uploads/media/6976756e9e5a6_1769370990.jpg', 'image/jpeg', 93303, '', '', 'gallery', 0, 1, '2026-01-25 19:56:30', 1),
(14, '6976756e9f8b2_1769370990.jpg', 'AlojamentoQuarto2.jpg', '/uploads/media/6976756e9f8b2_1769370990.jpg', 'image/jpeg', 75318, '', '', 'gallery', 0, 1, '2026-01-25 19:56:30', 1),
(15, '6976756ea0995_1769370990.jpg', 'AlojamentoQuarto1.jpg', '/uploads/media/6976756ea0995_1769370990.jpg', 'image/jpeg', 97800, 'Quarto Cama de Casal', 'Double Bed Room', 'gallery', 0, 1, '2026-01-25 19:56:30', 1),
(18, '6976986c675eb_1769379948.jpg', 'AlojamentoQuarto50.jpg', '/uploads/media/6976986c675eb_1769379948.jpg', 'image/jpeg', 54673, NULL, NULL, 'other', 0, 1, '2026-01-25 22:25:48', NULL),
(19, '6976986c696fb_1769379948.jpg', 'AlojamentoQuarto49.jpg', '/uploads/media/6976986c696fb_1769379948.jpg', 'image/jpeg', 57394, NULL, NULL, 'other', 0, 1, '2026-01-25 22:25:48', NULL),
(20, '6976986c6ad28_1769379948.jpg', 'AlojamentoQuarto48.jpg', '/uploads/media/6976986c6ad28_1769379948.jpg', 'image/jpeg', 163886, NULL, NULL, 'other', 0, 1, '2026-01-25 22:25:48', NULL),
(21, '6976986c6c3a6_1769379948.jpg', 'AlojamentoQuarto47.jpg', '/uploads/media/6976986c6c3a6_1769379948.jpg', 'image/jpeg', 129213, NULL, NULL, 'other', 0, 1, '2026-01-25 22:25:48', NULL),
(22, '6976986c6e346_1769379948.jpg', 'AlojamentoQuarto46.jpg', '/uploads/media/6976986c6e346_1769379948.jpg', 'image/jpeg', 98581, NULL, NULL, 'other', 0, 1, '2026-01-25 22:25:48', NULL),
(23, '6976986c7080e_1769379948.jpg', 'AlojamentoQuarto45.jpg', '/uploads/media/6976986c7080e_1769379948.jpg', 'image/jpeg', 67252, '', '', 'gallery', 0, 1, '2026-01-25 22:25:48', 1),
(24, '6976986c72fde_1769379948.jpg', 'AlojamentoQuarto44.jpg', '/uploads/media/6976986c72fde_1769379948.jpg', 'image/jpeg', 63988, NULL, NULL, 'other', 0, 1, '2026-01-25 22:25:48', NULL),
(25, 'accommodation_697698bc063af.jpg', 'AlojamentoQuarto26.jpg', '/uploads/accommodation/accommodation_697698bc063af.jpg', 'image/jpeg', 69718, '', '', 'gallery', 1, NULL, '2026-01-25 22:27:08', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
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
  `notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `language` varchar(2) DEFAULT 'pt',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `weight_grams` int(10) UNSIGNED DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(16, 'maintenance_mode', '0', 'boolean', 'general', 'Modo manutencao', 0, '2026-01-19 12:51:19', '2026-01-19 12:51:19'),
(17, 'free_shipping_threshold', '50', 'number', 'shop', NULL, 0, '2026-01-20 16:22:55', '2026-01-20 16:22:55'),
(18, 'shipping_cost', '5', 'number', 'shop', NULL, 0, '2026-01-20 16:22:55', '2026-01-20 16:22:55');

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
  ADD KEY `idx_active` (`is_active`);

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
  ADD KEY `idx_created` (`created_at`);

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
-- Índices para tabela `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_sort` (`sort_order`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `activity_translations`
--
ALTER TABLE `activity_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `amenities`
--
ALTER TABLE `amenities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de tabela `amenity_translations`
--
ALTER TABLE `amenity_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT de tabela `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `bathrooms`
--
ALTER TABLE `bathrooms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `bathroom_translations`
--
ALTER TABLE `bathroom_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `bedrooms`
--
ALTER TABLE `bedrooms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `bedroom_translations`
--
ALTER TABLE `bedroom_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `content_blocks`
--
ALTER TABLE `content_blocks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `house_rules`
--
ALTER TABLE `house_rules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `house_rule_translations`
--
ALTER TABLE `house_rule_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `media`
--
ALTER TABLE `media`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `product_translations`
--
ALTER TABLE `product_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

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
