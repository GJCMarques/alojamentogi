-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: acasadogi
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accommodation`
--

DROP TABLE IF EXISTS `accommodation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accommodation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL DEFAULT 'casa-do-gi',
  `max_guests` int(10) unsigned DEFAULT 6,
  `bedrooms` int(10) unsigned DEFAULT 3,
  `bathrooms` int(10) unsigned DEFAULT 2,
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
  `reviews_count` int(10) unsigned DEFAULT 0 COMMENT 'Total number of reviews',
  `city` varchar(100) DEFAULT 'Mogadouro' COMMENT 'City name',
  `region` varchar(100) DEFAULT 'Trás-os-Montes' COMMENT 'Region name',
  `country` varchar(100) DEFAULT 'Portugal' COMMENT 'Country name',
  `host_type` enum('professional','superhost','standard') DEFAULT 'standard' COMMENT 'Host type badge',
  `checkin_type` enum('self_checkin','meet_host','key_lockbox','smart_lock') DEFAULT 'self_checkin' COMMENT 'Check-in method',
  `checkin_instructions` text DEFAULT NULL COMMENT 'Check-in instructions (internal)',
  `towels_linens_included` tinyint(1) DEFAULT 1 COMMENT 'Towels and linens provided',
  `min_nights` int(10) unsigned DEFAULT 1 COMMENT 'Minimum nights stay',
  `instant_booking` tinyint(1) DEFAULT 0 COMMENT 'Instant booking available',
  `accommodation_number` int(10) unsigned DEFAULT 1 COMMENT 'Casa 1 or Casa 2',
  `guestready_url` varchar(500) DEFAULT NULL COMMENT 'GuestReady booking URL',
  `booking_url` varchar(500) DEFAULT NULL COMMENT 'Booking.com URL',
  `airbnb_url` varchar(500) DEFAULT NULL COMMENT 'Airbnb URL',
  `hero_image` varchar(500) DEFAULT NULL COMMENT 'Hero image for this accommodation',
  `cover_image` varchar(500) DEFAULT NULL COMMENT 'Cover image for selection cards on main page',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_accommodation_number` (`accommodation_number`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accommodation`
--

LOCK TABLES `accommodation` WRITE;
/*!40000 ALTER TABLE `accommodation` DISABLE KEYS */;
INSERT INTO `accommodation` VALUES (1,'casa-do-gi-1',6,3,2,100.00,1,0,'16:00:00','11:00:00',41.34217000,-6.71347000,'146729/AL','2026-01-19 12:51:19','2026-02-10 18:18:43',1,NULL,0,'Mogadouro','Tras-os-Montes','Portugal','standard','self_checkin',NULL,1,1,0,1,'','','','images/MogadouroAlojamento.jpg','images/IgrejaMatriz.jpg'),(2,'casa-do-gi-2',6,3,2,100.00,1,0,'16:00:00','11:00:00',41.34217000,-6.71347000,'146729/AL','2026-01-30 02:22:49','2026-02-10 18:18:43',1,NULL,0,'Mogadouro','Tras-os-Montes','Portugal','standard','self_checkin',NULL,1,1,0,2,'','','','uploads/accommodation/hero_casa2_1770084834.jpeg','images/Castelo.jpg');
/*!40000 ALTER TABLE `accommodation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `accommodation_amenities`
--

DROP TABLE IF EXISTS `accommodation_amenities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accommodation_amenities` (
  `accommodation_id` int(10) unsigned NOT NULL,
  `amenity_id` int(10) unsigned NOT NULL,
  `is_highlighted` tinyint(1) DEFAULT 0 COMMENT 'Show in main section (top 8)',
  `sort_order` int(10) unsigned DEFAULT 0 COMMENT 'Display order',
  PRIMARY KEY (`accommodation_id`,`amenity_id`),
  KEY `amenity_id` (`amenity_id`),
  CONSTRAINT `accommodation_amenities_ibfk_1` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodation` (`id`) ON DELETE CASCADE,
  CONSTRAINT `accommodation_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accommodation_amenities`
--

LOCK TABLES `accommodation_amenities` WRITE;
/*!40000 ALTER TABLE `accommodation_amenities` DISABLE KEYS */;
INSERT INTO `accommodation_amenities` VALUES (1,1,0,1),(1,2,0,2),(1,3,0,3),(1,4,0,4),(1,10,0,6),(1,11,0,9),(1,12,0,5),(1,13,0,7),(1,23,0,8),(1,26,0,10),(1,27,0,15),(1,28,0,16),(1,29,0,17),(1,30,0,18),(1,35,0,11),(1,36,0,12),(1,37,0,13),(1,38,0,14);
/*!40000 ALTER TABLE `accommodation_amenities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `accommodation_translations`
--

DROP TABLE IF EXISTS `accommodation_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accommodation_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accommodation_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
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
  `activity_section_description` text DEFAULT NULL COMMENT 'Description for activities section',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_acc_lang` (`accommodation_id`,`language_id`),
  KEY `language_id` (`language_id`),
  CONSTRAINT `accommodation_translations_ibfk_1` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodation` (`id`) ON DELETE CASCADE,
  CONSTRAINT `accommodation_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accommodation_translations`
--

LOCK TABLES `accommodation_translations` WRITE;
/*!40000 ALTER TABLE `accommodation_translations` DISABLE KEYS */;
INSERT INTO `accommodation_translations` VALUES (1,1,1,'A Casa do Gi','Casa de ferias de 100m2, andar nr 1, sem elevador','A Casa do Gi e sinonimo de simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor! Construida nos anos 80, altura em que os artistas da construcao e os materiais eram escassos por Terras de Mogadouro.',NULL,'A Casa do Gi','Simplicidade, acolhimento e muito amor','A Casa do Gi e sinonimo de simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor! Construida nos anos 80, altura em que os artistas da construcao e os materiais eram escassos por Terras de Mogadouro.','','wewewee','','','wewew','Mogadouro & Envolvência',''),(2,1,2,'A Casa do Gi','Holiday home of 100m2, 1st floor, no elevator','A Casa do Gi is synonymous with simplicity, welcoming, remarkable moments of conviviality, warmth of family, joy, fun, laughter and a lot of love! Built in the 80s, when construction artists and materials were scarce in the lands of Mogadouro.',NULL,'A Casa do Gi','Simplicity, warmth and love','A Casa do Gi is synonymous with simplicity, welcoming, remarkable moments of conviviality, warmth of family, joy, fun, laughter and a lot of love! Built in the 80s, when construction artists and materials were scarce in the lands of Mogadouro.','','wewewe','','','2wwewe','',''),(3,2,1,'',NULL,NULL,NULL,'A Casa do Gi 2','Simplicidade, acolhimento e muito amor','A Casa do Gi e sinonimo de simplicidade, acolhimento, momentos de convivio marcantes, calor da familia, alegria, diversao, gargalhadas e muito amor! Construida nos anos 80, altura em que os artistas da construcao e os materiais eram escassos por Terras de Mogadouro.','','','','','','',''),(4,2,2,'',NULL,NULL,NULL,'A Casa do Gi 2','Simplicity, warmth and love','A Casa do Gi is synonymous with simplicity, welcoming, remarkable moments of conviviality, warmth of family, joy, fun, laughter and a lot of love! Built in the 80s, when construction artists and materials were scarce in the lands of Mogadouro.','','','','','','','');
/*!40000 ALTER TABLE `accommodation_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_links`
--

DROP TABLE IF EXISTS `activity_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(50) NOT NULL DEFAULT 'official',
  `tag_pt` varchar(100) DEFAULT NULL,
  `tag_en` varchar(100) DEFAULT NULL,
  `title_pt` varchar(255) NOT NULL,
  `title_en` varchar(255) DEFAULT NULL,
  `desc_pt` text DEFAULT NULL,
  `desc_en` text DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_links`
--

LOCK TABLES `activity_links` WRITE;
/*!40000 ALTER TABLE `activity_links` DISABLE KEYS */;
INSERT INTO `activity_links` VALUES (1,'official','SITE OFICIAL','OFFICIAL SITE','Câmara Municipal de Mogadouro','Mogadouro City Hall','Informação oficial do concelho: o que visitar, património, eventos e contactos.','Official county info: what to visit, heritage, events and contacts.','https://www.mogadouro.pt/',1,1),(2,'official','TURISMO','TOURISM','Posto de Turismo de Mogadouro','Mogadouro Tourism Office','Loja Interativa de Turismo - pontos de interesse, percursos e apoio ao visitante.','Interactive Tourism Shop ù points of interest, routes and visitor support.','https://www.mogadouro.pt/pages/17',2,1),(3,'guide','','','Roteiro por Mogadouro - Vagamundos','Mogadouro Guide ù Vagamundos','','','https://www.vagamundos.pt/visitar-mogadouro-roteiro/',3,1),(4,'guide','','','Atrações em torno de Mogadouro - Komoot','Attractions around Mogadouro ù Komoot','','','https://www.komoot.com/pt-pt/guide/900754/atracoes-em-torno-de-mogadouro',4,1),(5,'guide','','','Mogadouro - Tripadvisor','Mogadouro ù Tripadvisor','','','https://www.tripadvisor.pt/Attractions-g1458520-Activities-Mogadouro_Braganca_District_Northern_Portugal.html',5,1);
/*!40000 ALTER TABLE `activity_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('super_admin','admin','editor') DEFAULT 'editor',
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `login_attempts` int(10) unsigned DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `password_reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_username` (`username`),
  KEY `idx_email` (`email`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin','admin@acasadogi.pt','$2y$12$h1hHCeq1svPUpfFc82tKYuHPtR3j8bReNo1nSeCcR7ZK3M3YKld.G','Administrador','super_admin',NULL,1,'2026-08-15 01:03:59',0,NULL,NULL,NULL,'2026-01-19 12:51:19','2026-08-15 00:03:59');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `amenities`
--

DROP TABLE IF EXISTS `amenities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amenities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `icon` varchar(50) NOT NULL,
  `category` enum('general','kitchen','bedroom','bathroom','outdoor','entertainment','safety','children','sports','services') DEFAULT 'general',
  `sort_order` int(10) unsigned DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `amenities`
--

LOCK TABLES `amenities` WRITE;
/*!40000 ALTER TABLE `amenities` DISABLE KEYS */;
INSERT INTO `amenities` VALUES (1,'wifi','general',1,1,'2026-01-19 12:51:19'),(2,'ac','general',2,1,'2026-01-19 12:51:19'),(3,'heater','general',3,1,'2026-01-19 12:51:19'),(4,'parking','general',4,1,'2026-01-19 12:51:19'),(5,'pool-private','outdoor',5,1,'2026-01-19 12:51:19'),(6,'pool-shared','outdoor',6,1,'2026-01-19 12:51:19'),(7,'garden','outdoor',7,1,'2026-01-19 12:51:19'),(8,'terrace','outdoor',8,1,'2026-01-19 12:51:19'),(9,'washing-machine','general',9,1,'2026-01-19 12:51:19'),(10,'dishwasher','kitchen',10,1,'2026-01-19 12:51:19'),(11,'hairdryer','bathroom',11,1,'2026-01-19 12:51:19'),(12,'workspace','general',12,1,'2026-01-19 12:51:19'),(13,'oven','kitchen',20,1,'2026-01-26 22:31:24'),(14,'microwave','kitchen',21,1,'2026-01-26 22:31:24'),(15,'fridge','kitchen',22,1,'2026-01-26 22:31:24'),(16,'coffee-maker','kitchen',23,1,'2026-01-26 22:31:24'),(17,'toaster','kitchen',24,1,'2026-01-26 22:31:24'),(18,'kettle','kitchen',25,1,'2026-01-26 22:31:24'),(19,'cookware','kitchen',26,1,'2026-01-26 22:31:24'),(20,'bed-linens','bedroom',30,1,'2026-01-26 22:31:24'),(21,'extra-pillows','bedroom',31,1,'2026-01-26 22:31:24'),(22,'blackout-curtains','bedroom',32,1,'2026-01-26 22:31:24'),(23,'hangers','bedroom',33,1,'2026-01-26 22:31:24'),(24,'hot-water','bathroom',40,1,'2026-01-26 22:31:24'),(25,'towels','bathroom',41,1,'2026-01-26 22:31:24'),(26,'toiletries','bathroom',42,1,'2026-01-26 22:31:24'),(27,'smoke-detector','safety',50,1,'2026-01-26 22:31:24'),(28,'fire-extinguisher','safety',51,1,'2026-01-26 22:31:24'),(29,'first-aid','safety',52,1,'2026-01-26 22:31:24'),(30,'carbon-monoxide','safety',53,1,'2026-01-26 22:31:24'),(31,'high-chair','children',60,1,'2026-01-26 22:31:24'),(32,'crib','children',61,1,'2026-01-26 22:31:24'),(33,'baby-bath','children',62,1,'2026-01-26 22:31:24'),(34,'child-safety','children',63,1,'2026-01-26 22:31:24'),(35,'smart-tv','entertainment',70,1,'2026-01-26 22:31:24'),(36,'streaming','entertainment',71,1,'2026-01-26 22:31:24'),(37,'books','entertainment',72,1,'2026-01-26 22:31:24'),(38,'board-games','entertainment',73,1,'2026-01-26 22:31:24'),(39,'cleaning','services',80,1,'2026-01-26 22:31:24'),(40,'luggage-storage','services',81,1,'2026-01-26 22:31:24');
/*!40000 ALTER TABLE `amenities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `amenity_translations`
--

DROP TABLE IF EXISTS `amenity_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amenity_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `amenity_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_amenity_lang` (`amenity_id`,`language_id`),
  KEY `language_id` (`language_id`),
  CONSTRAINT `amenity_translations_ibfk_1` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `amenity_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `amenity_translations`
--

LOCK TABLES `amenity_translations` WRITE;
/*!40000 ALTER TABLE `amenity_translations` DISABLE KEYS */;
INSERT INTO `amenity_translations` VALUES (1,1,1,'Internet Wifi'),(2,1,2,'Wifi Internet'),(3,2,1,'Ar condicionado'),(4,2,2,'Air conditioning'),(5,3,1,'Aquecedores'),(6,3,2,'Heaters'),(7,4,1,'Estacionamento incluido'),(8,4,2,'Parking included'),(9,5,1,'Piscina privada'),(10,5,2,'Private pool'),(11,6,1,'Piscina partilhada'),(12,6,2,'Shared pool'),(13,7,1,'Jardim'),(14,7,2,'Garden'),(15,8,1,'Terraco'),(16,8,2,'Terrace'),(17,9,1,'Maquina de lavar'),(18,9,2,'Washing machine'),(19,10,1,'Lava-louca'),(20,10,2,'Dishwasher'),(21,11,1,'Secador de cabelo'),(22,11,2,'Hair dryer'),(23,12,1,'Area de trabalho para portatil'),(24,12,2,'Laptop workspace'),(25,13,1,'Forno'),(26,14,1,'Micro-ondas'),(27,15,1,'Frigorífico'),(28,16,1,'Máquina de café'),(29,17,1,'Torradeira'),(30,18,1,'Chaleira'),(31,19,1,'Utensílios de cozinha'),(32,13,2,'Oven'),(33,14,2,'Microwave'),(34,15,2,'Refrigerator'),(35,16,2,'Coffee maker'),(36,17,2,'Toaster'),(37,18,2,'Electric kettle'),(38,19,2,'Cookware'),(39,20,1,'Roupa de cama'),(40,21,1,'Almofadas extra'),(41,22,1,'Cortinas blackout'),(42,23,1,'Cabides'),(46,20,2,'Bed linens'),(47,21,2,'Extra pillows'),(48,22,2,'Blackout curtains'),(49,23,2,'Hangers'),(53,24,1,'Água quente'),(54,25,1,'Toalhas'),(55,26,1,'Artigos de higiene'),(56,24,2,'Hot water'),(57,25,2,'Towels'),(58,26,2,'Toiletries'),(59,27,1,'Detetor de fumo'),(60,28,1,'Extintor'),(61,29,1,'Kit primeiros socorros'),(62,30,1,'Detetor de monóxido'),(66,27,2,'Smoke detector'),(67,28,2,'Fire extinguisher'),(68,29,2,'First aid kit'),(69,30,2,'Carbon monoxide detector'),(73,31,1,'Cadeira alta'),(74,32,1,'Berço'),(75,33,1,'Banheira bebé'),(76,34,1,'Proteções para crianças'),(80,31,2,'High chair'),(81,32,2,'Crib'),(82,33,2,'Baby bath'),(83,34,2,'Child safety gates'),(87,35,1,'Smart TV'),(88,36,1,'Streaming (Netflix)'),(89,37,1,'Livros'),(90,38,1,'Jogos de tabuleiro'),(94,35,2,'Smart TV'),(95,36,2,'Streaming (Netflix)'),(96,37,2,'Books'),(97,38,2,'Board games'),(101,39,1,'Limpeza incluída'),(102,40,1,'Guarda bagagem'),(104,39,2,'Cleaning included'),(105,40,2,'Luggage storage');
/*!40000 ALTER TABLE `amenity_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bathroom_translations`
--

DROP TABLE IF EXISTS `bathroom_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bathroom_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bathroom_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `name` varchar(100) DEFAULT NULL COMMENT 'Bathroom name',
  `description` varchar(255) NOT NULL COMMENT 'Bathroom description',
  `title` varchar(50) DEFAULT NULL COMMENT 'Section title like "Higiene"',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_bathroom_lang` (`bathroom_id`,`language_id`),
  KEY `language_id` (`language_id`),
  CONSTRAINT `bathroom_translations_ibfk_1` FOREIGN KEY (`bathroom_id`) REFERENCES `bathrooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bathroom_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bathroom_translations`
--

LOCK TABLES `bathroom_translations` WRITE;
/*!40000 ALTER TABLE `bathroom_translations` DISABLE KEYS */;
INSERT INTO `bathroom_translations` VALUES (17,9,1,'Casa de Banho Principal','Banheira, chuveiro, bide, secador de cabelo',NULL),(18,9,2,'Main Bathroom','Bathtub, shower, bidet, hair dryer',NULL),(19,10,1,'Casa de Banho Secundaria','Chuveiro, lavatorio',NULL),(20,10,2,'Secondary Bathroom','Shower, sink',NULL),(21,11,1,'Casa de Banho Principal','Banheira, chuveiro, bide, secador de cabelo',NULL),(22,11,2,'Main Bathroom','Bathtub, shower, bidet, hair dryer',NULL),(23,12,1,'Casa de Banho Secundaria','Chuveiro, lavatorio',NULL),(24,12,2,'Secondary Bathroom','Shower, sink',NULL);
/*!40000 ALTER TABLE `bathroom_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bathrooms`
--

DROP TABLE IF EXISTS `bathrooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bathrooms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accommodation_id` int(10) unsigned NOT NULL,
  `bathroom_number` int(10) unsigned NOT NULL,
  `is_ensuite` tinyint(1) DEFAULT 0 COMMENT 'Is this an ensuite bathroom',
  `has_shower` tinyint(1) DEFAULT 1,
  `has_bathtub` tinyint(1) DEFAULT 0,
  `has_bidet` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL COMMENT 'Bathroom photo path',
  PRIMARY KEY (`id`),
  KEY `accommodation_id` (`accommodation_id`),
  CONSTRAINT `bathrooms_ibfk_1` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodation` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bathrooms`
--

LOCK TABLES `bathrooms` WRITE;
/*!40000 ALTER TABLE `bathrooms` DISABLE KEYS */;
INSERT INTO `bathrooms` VALUES (9,1,1,0,1,1,1,'2026-02-10 18:23:27',NULL),(10,1,2,0,1,0,0,'2026-02-10 18:23:27',NULL),(11,2,1,0,1,1,1,'2026-02-10 18:23:27',NULL),(12,2,2,0,1,0,0,'2026-02-10 18:23:27',NULL);
/*!40000 ALTER TABLE `bathrooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bedroom_translations`
--

DROP TABLE IF EXISTS `bedroom_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bedroom_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bedroom_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `beds_description` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL COMMENT 'Bedroom name (e.g., Master Suite)',
  `title` varchar(50) DEFAULT NULL COMMENT 'Section title like "Dormidas"',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_bedroom_lang` (`bedroom_id`,`language_id`),
  KEY `language_id` (`language_id`),
  CONSTRAINT `bedroom_translations_ibfk_1` FOREIGN KEY (`bedroom_id`) REFERENCES `bedrooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bedroom_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bedroom_translations`
--

LOCK TABLES `bedroom_translations` WRITE;
/*!40000 ALTER TABLE `bedroom_translations` DISABLE KEYS */;
INSERT INTO `bedroom_translations` VALUES (25,13,1,'2 camas de solteiro','Quarto Principal',NULL),(26,13,2,'2 single beds','Master Bedroom',NULL),(27,14,1,'Sofa-cama de solteiro, Cama de casal','Quarto Duplo',NULL),(28,14,2,'Single sofa bed, Double bed','Twin Room',NULL),(29,15,1,'Cama de casal','Quarto de Hospedes',NULL),(30,15,2,'Double bed','Guest Room',NULL),(31,16,1,'2 camas de solteiro','Quarto Principal',NULL),(32,16,2,'2 single beds','Master Bedroom',NULL),(33,17,1,'Sofa-cama de solteiro, Cama de casal','Quarto Duplo',NULL),(34,17,2,'Single sofa bed, Double bed','Twin Room',NULL),(35,18,1,'Cama de casal','Quarto de Hospedes',NULL),(36,18,2,'Double bed','Guest Room',NULL);
/*!40000 ALTER TABLE `bedroom_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bedrooms`
--

DROP TABLE IF EXISTS `bedrooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bedrooms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accommodation_id` int(10) unsigned NOT NULL,
  `bedroom_number` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL COMMENT 'Bedroom photo path',
  PRIMARY KEY (`id`),
  KEY `accommodation_id` (`accommodation_id`),
  CONSTRAINT `bedrooms_ibfk_1` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodation` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bedrooms`
--

LOCK TABLES `bedrooms` WRITE;
/*!40000 ALTER TABLE `bedrooms` DISABLE KEYS */;
INSERT INTO `bedrooms` VALUES (13,1,1,'2026-02-10 18:23:26',NULL),(14,1,2,'2026-02-10 18:23:26',NULL),(15,1,3,'2026-02-10 18:23:26',NULL),(16,2,1,'2026-02-10 18:23:27',NULL),(17,2,2,'2026-02-10 18:23:27',NULL),(18,2,3,'2026-02-10 18:23:27',NULL);
/*!40000 ALTER TABLE `bedrooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_submissions`
--

DROP TABLE IF EXISTS `contact_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_submissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_read` (`is_read`),
  KEY `idx_created` (`created_at`),
  KEY `idx_ignored` (`is_ignored`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_submissions`
--

LOCK TABLES `contact_submissions` WRITE;
/*!40000 ALTER TABLE `contact_submissions` DISABLE KEYS */;
INSERT INTO `contact_submissions` VALUES (2,'Guilherme Marques','guilherme.jcmarques@gmail.com','999323876','Testar sistema','Teste do Sistema de Formulário de Mensagens.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','pt',1,0,0,'2026-02-07 01:34:24'),(7,'Teste','guilherme.jcmarques@gmail.com','999000222','teste','wewewewewew','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','pt',1,0,0,'2026-08-14 15:03:58'),(8,'teste2','guilherme.jcmarques@gmail.com','999888777','testye2','sdsfdsvdvsdvdvdfdfcxc','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','en',1,0,0,'2026-08-14 15:04:25'),(9,'teste3','teste@teste.com','999888222','wewewewew','wraeaefsefrsreg','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','pt',1,0,0,'2026-08-14 15:30:21'),(10,'etet','teste@teste.com','222333111','arwqrwra','jhdttjgjhgrfhggfgf','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','pt',0,0,0,'2026-08-14 16:03:55');
/*!40000 ALTER TABLE `contact_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content_blocks`
--

DROP TABLE IF EXISTS `content_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_blocks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `block_key` varchar(100) NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `content_type` enum('text','textarea','html','json') DEFAULT 'text',
  `content` text DEFAULT NULL,
  `page` varchar(50) DEFAULT NULL,
  `section` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_block_lang` (`block_key`,`language_id`),
  KEY `language_id` (`language_id`),
  KEY `idx_block_key` (`block_key`),
  KEY `idx_page` (`page`),
  KEY `idx_section` (`section`),
  CONSTRAINT `content_blocks_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=469 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_blocks`
--

LOCK TABLES `content_blocks` WRITE;
/*!40000 ALTER TABLE `content_blocks` DISABLE KEYS */;
INSERT INTO `content_blocks` VALUES (6,'accommodation_title',1,'text','O Alojamento','accommodation','main','2026-01-19 12:51:19','2026-01-19 12:51:19'),(7,'accommodation_intro',1,'textarea','Ambas as casas oferecem o mesmo conforto e hospitalidade transmontana. Escolha a que melhor se adapta à sua estadia.','accommodation','main','2026-01-19 12:51:19','2026-08-14 17:12:28'),(8,'shop_title',1,'text','Produtos Regionais','shop','main','2026-01-19 12:51:19','2026-01-19 12:51:19'),(9,'shop_intro',1,'textarea','Sabores autênticos de Trás-os-Montes, selecionados com carinho para a sua mesa.','shop','main','2026-01-19 12:51:19','2026-08-14 17:12:28'),(10,'activities_title',1,'text','O Que Fazer','activities','main','2026-01-19 12:51:19','2026-01-19 12:51:19'),(11,'activities_intro',1,'textarea','Descubra as maravilhas de Mogadouro e arredores','activities','main','2026-01-19 12:51:19','2026-01-19 12:51:19'),(12,'contact_title',1,'text','Contacte-nos','contact','main','2026-01-19 12:51:19','2026-01-19 12:51:19'),(13,'contact_intro',1,'textarea','Tem alguma questão? Entre em contacto connosco','contact','main','2026-01-19 12:51:19','2026-08-14 17:12:28'),(19,'accommodation_title',2,'text','The Accommodation','accommodation','main','2026-01-19 12:51:19','2026-01-19 12:51:19'),(20,'accommodation_intro',2,'textarea','Both houses offer the same comfort and Transmontana hospitality. Choose the one that best suits your stay.','accommodation','main','2026-01-19 12:51:19','2026-02-09 20:00:28'),(21,'shop_title',2,'text','Regional Products','shop','main','2026-01-19 12:51:19','2026-01-19 12:51:19'),(22,'shop_intro',2,'textarea','Authentic flavors from Tras-os-Montes, selected with care for your table.','shop','main','2026-01-19 12:51:19','2026-02-09 19:52:45'),(23,'activities_title',2,'text','Things To Do','activities','main','2026-01-19 12:51:19','2026-01-19 12:51:19'),(24,'activities_intro',2,'textarea','Discover the wonders of Mogadouro and surroundings','activities','main','2026-01-19 12:51:19','2026-01-19 12:51:19'),(25,'contact_title',2,'text','Contact Us','contact','main','2026-01-19 12:51:19','2026-01-19 12:51:19'),(26,'contact_intro',2,'textarea','Do you have any questions? Get in touch with us','contact','main','2026-01-19 12:51:19','2026-02-09 19:27:19'),(27,'home_hero_subtitle',1,'text','Onde a tradição transmontana encontra o conforto moderno','home','hero','2026-02-09 18:34:33','2026-08-14 20:52:51'),(28,'home_hero_subtitle',2,'text','Where Transmontana tradition meets modern comfort','home','hero','2026-02-09 18:34:33','2026-08-14 20:52:51'),(29,'home_split_left_label',1,'text','Bem-vindo ao','home','split_hero','2026-02-09 18:34:33','2026-08-14 20:52:51'),(30,'home_split_left_label',2,'text','Welcome to the','home','split_hero','2026-02-09 18:34:33','2026-08-14 20:52:51'),(31,'home_split_left_title',1,'text','Refúgio','home','split_hero','2026-02-09 18:34:33','2026-08-14 20:52:51'),(32,'home_split_left_title',2,'text','Refuge','home','split_hero','2026-02-09 18:34:33','2026-08-14 20:52:51'),(33,'home_split_right_label',1,'text','Descubra a','home','split_hero','2026-02-09 18:34:33','2026-08-14 20:52:51'),(34,'home_split_right_label',2,'text','Discover the','home','split_hero','2026-02-09 18:34:33','2026-08-14 20:52:51'),(35,'home_split_right_title',1,'text','Tradição','home','split_hero','2026-02-09 18:34:33','2026-08-14 20:52:51'),(36,'home_split_right_title',2,'text','Tradition','home','split_hero','2026-02-09 18:34:33','2026-08-14 20:52:51'),(37,'home_explore_title',1,'text','Explore o Nosso Mundo','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(38,'home_explore_title',2,'text','Explore Our World','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(41,'home_card1_title',1,'text','Alojamento','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(42,'home_card1_title',2,'text','Accommodation','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(43,'home_card1_text',1,'text','Sinta o conforto das nossas casas rústicas.','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(44,'home_card1_text',2,'text','Experience the comfort of our rustic houses.','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(45,'home_card1_cta',1,'text','Ver Casas','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(46,'home_card1_cta',2,'text','View Rooms','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(49,'home_card2_title',1,'text','Atividades','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(50,'home_card2_title',2,'text','Activities','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(51,'home_card2_text',1,'text','Descubra a natureza e história de Mogadouro.','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(52,'home_card2_text',2,'text','Discover the nature and history of Mogadouro.','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(53,'home_card2_cta',1,'text','Explorar','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(54,'home_card2_cta',2,'text','Explore','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(57,'home_card3_title',1,'text','Loja Regional','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(58,'home_card3_title',2,'text','Regional Shop','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(59,'home_card3_text',1,'text','Sabores autênticos de Trás-os-Montes.','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(60,'home_card3_text',2,'text','Authentic flavors from Tras-os-Montes.','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(61,'home_card3_cta',1,'text','Comprar','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(62,'home_card3_cta',2,'text','Shop Now','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(65,'home_card4_title',1,'text','Contactos','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(66,'home_card4_title',2,'text','Contact Us','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(67,'home_card4_text',1,'text','Entre em contacto connosco e planeie a sua visita.','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(68,'home_card4_text',2,'text','Get in touch and plan your visit.','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(69,'home_card4_cta',1,'text','Contactar','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(70,'home_card4_cta',2,'text','Get in Touch','home','explore','2026-02-09 18:34:33','2026-08-14 20:52:51'),(71,'home_about_label',1,'text','A Nossa História','home','about_teaser','2026-02-09 18:34:33','2026-08-14 20:52:51'),(72,'home_about_label',2,'text','Our Story','home','about_teaser','2026-02-09 18:34:33','2026-08-14 20:52:51'),(73,'home_about_title',1,'html','Mais do que uma casa, um <span class=\"italic text-accent\">Legado</span>.','home','about_teaser','2026-02-09 18:34:33','2026-08-14 20:52:51'),(74,'home_about_title',2,'html','More than a house,<br>a <span class=\"italic text-accent\">legacy</span>.','home','about_teaser','2026-02-09 18:34:33','2026-08-14 20:52:51'),(75,'home_about_text1',1,'textarea','A Casa do Gi nasceu da vontade de preservar as raízes transmontanas. O que outrora foi uma casa de família, é hoje um refúgio para quem procura a autenticidade do campo.','home','about_teaser','2026-02-09 18:34:33','2026-08-14 20:52:51'),(76,'home_about_text1',2,'textarea','A Casa do Gi was born from the will to preserve the roots of Tras-os-Montes. What was once a family home is now a refuge for those seeking the authenticity of the countryside.','home','about_teaser','2026-02-09 18:34:33','2026-08-14 20:52:51'),(77,'home_about_text2',1,'textarea','Aqui, o tempo abranda. Convidamo-lo a descobrir as tradições, os sabores e as gentes que fazem de Mogadouro um lugar único no mundo.','home','about_teaser','2026-02-09 18:34:33','2026-08-14 20:52:51'),(78,'home_about_text2',2,'textarea','Here, time slows down. We invite you to discover the traditions, the flavors, and the people that make Mogadouro a unique place in the world.','home','about_teaser','2026-02-09 18:34:33','2026-08-14 20:52:51'),(79,'home_about_cta',1,'text','Ler História Completa','home','about_teaser','2026-02-09 18:34:33','2026-08-14 20:52:51'),(80,'home_about_cta',2,'text','Read Full Story','home','about_teaser','2026-02-09 18:34:33','2026-08-14 20:52:51'),(81,'about_hero_label',1,'text','A Nossa Hist├│ria','about','hero','2026-02-09 18:34:33','2026-02-09 18:34:33'),(82,'about_hero_label',2,'text','Our Story','about','hero','2026-02-09 18:34:33','2026-02-09 18:34:33'),(83,'about_hero_subtitle',1,'textarea','Simplicidade, acolhimento, momentos de convívio marcantes, calor da família, alegria, diversão, gargalhadas e muito amor!','about','hero','2026-02-09 18:34:33','2026-08-14 17:12:28'),(84,'about_hero_subtitle',2,'textarea','Simplicity, warmth, remarkable moments of conviviality, family warmth, joy, fun, laughter and lots of love!','about','hero','2026-02-09 18:34:33','2026-02-09 20:08:48'),(85,'about_origin_label',1,'text','A Nossa Origem','about','origin','2026-02-09 18:34:33','2026-02-09 18:34:33'),(86,'about_origin_label',2,'text','Our Origins','about','origin','2026-02-09 18:34:33','2026-02-09 18:34:33'),(87,'about_origin_title',1,'html','Uma casa construída com <span class=\"italic text-secondary\">amor</span> e <span class=\"italic text-secondary\">saudade</span>.','about','origin','2026-02-09 18:34:33','2026-08-14 17:51:14'),(88,'about_origin_title',2,'html','A house built with <span class=\"italic text-secondary\">love</span> and <span class=\"italic text-secondary\">longing</span>.','about','origin','2026-02-09 18:34:33','2026-02-09 18:34:33'),(89,'about_origin_text1',1,'textarea','Erguida nos anos 80, a <strong>Casa do Gi</strong> conta a história intemporal de quem partiu para longe mas nunca esqueceu as suas raízes. Construída tijolo a tijolo, representa o sonho concretizado de regressar a casa.','about','origin','2026-02-09 18:34:33','2026-08-14 17:51:14'),(90,'about_origin_text1',2,'textarea','Built in the 80s, <strong>Casa do Gi</strong> tells the timeless story of those who left for distant lands but never forgot their roots. Constructed brick by brick, it represents the fulfilled dream of returning home.','about','origin','2026-02-09 18:34:33','2026-02-09 18:34:33'),(91,'about_origin_text2',1,'textarea','O que começou como um projeto de vida familiar transformou-se num refúgio para quem procura a paz do interior. Aqui, o tempo abranda e os dias são medidos pela luz do sol e pelas conversas à beira da lareira.','about','origin','2026-02-09 18:34:33','2026-08-14 17:51:14'),(92,'about_origin_text2',2,'textarea','What began as a family life project transformed into a refuge for those seeking the peace of the countryside. Here, time slows down and days are measured by sunlight and conversations by the fireplace.','about','origin','2026-02-09 18:34:33','2026-02-09 18:34:33'),(93,'about_origin_caption',1,'text','1980 • O Início','about','origin','2026-02-09 18:34:33','2026-08-14 17:51:14'),(94,'about_origin_caption',2,'text','1980 ÔÇó The Beginning','about','origin','2026-02-09 18:34:33','2026-02-09 18:34:33'),(95,'about_origin_signature',1,'text','Família Gi','about','origin','2026-02-09 18:34:33','2026-08-14 17:51:14'),(96,'about_origin_signature',2,'text','Gi Family','about','origin','2026-02-09 18:34:33','2026-02-09 18:34:33'),(97,'about_values_label',1,'text','Valores','about','values','2026-02-09 18:34:33','2026-02-09 18:34:33'),(98,'about_values_label',2,'text','Values','about','values','2026-02-09 18:34:33','2026-02-09 18:34:33'),(99,'about_values_title',1,'html','A arte de bem receber,<br>à moda antiga.','about','values','2026-02-09 18:34:33','2026-08-14 17:51:14'),(100,'about_values_title',2,'html','The art of welcoming,<br>the old-fashioned way.','about','values','2026-02-09 18:34:33','2026-02-09 18:34:33'),(101,'about_values_intro',1,'textarea','Não somos um hotel. Somos uma casa de família que decidiu abrir as portas ao mundo. Aqui, a hospitalidade não é um serviço, é a nossa natureza.','about','values','2026-02-09 18:34:33','2026-08-14 17:51:14'),(102,'about_values_intro',2,'textarea','We are not a hotel. We are a family home that decided to open its doors to the world. Here, hospitality is not a service, it\'s our nature.','about','values','2026-02-09 18:34:33','2026-02-09 18:34:33'),(103,'about_value1_title',1,'text','Acolhimento Genuíno','about','values','2026-02-09 18:34:33','2026-08-14 17:51:14'),(104,'about_value1_title',2,'text','Genuine Hospitality','about','values','2026-02-09 18:34:33','2026-02-09 18:34:33'),(105,'about_value1_text',1,'textarea','Recebemos cada hóspede como um velho amigo. Sem formalismos rígidos, com o calor de um abraço e a sinceridade de um sorriso transmontano.','about','values','2026-02-09 18:34:33','2026-08-14 17:51:14'),(106,'about_value1_text',2,'textarea','We welcome each guest as an old friend. Without rigid formalities, with the warmth of a hug and the sincerity of a Transmontano smile.','about','values','2026-02-09 18:34:33','2026-02-09 18:34:33'),(107,'about_value2_title',1,'text','Paz Absoluta','about','values','2026-02-09 18:34:33','2026-02-09 18:34:33'),(108,'about_value2_title',2,'text','Absolute Peace','about','values','2026-02-09 18:34:33','2026-02-09 18:34:33'),(109,'about_value2_text',1,'textarea','O luxo do silêncio. Longe da confusão, onde o único ruído é o vento nas árvores e o cantar dos pássaros. O refúgio perfeito para recarregar energias.','about','values','2026-02-09 18:34:33','2026-08-14 17:51:14'),(110,'about_value2_text',2,'textarea','The luxury of silence. Far from the hustle, where the only sound is the wind in the trees and the singing of birds. The perfect refuge to recharge energies.','about','values','2026-02-09 18:34:33','2026-02-09 18:34:33'),(111,'about_value3_title',1,'text','Espírito de Partilha','about','values','2026-02-09 18:34:33','2026-08-14 17:51:14'),(112,'about_value3_title',2,'text','Spirit of Sharing','about','values','2026-02-09 18:34:33','2026-02-09 18:34:33'),(113,'about_value3_text',1,'textarea','Acreditamos que as melhores memórias são construídas à mesa. Partilhamos histórias, sabores e experiências que ficam para sempre.','about','values','2026-02-09 18:34:33','2026-08-14 17:51:14'),(114,'about_value3_text',2,'textarea','We believe the best memories are built at the table. We share stories, flavors and experiences that last forever.','about','values','2026-02-09 18:34:33','2026-02-09 18:34:33'),(115,'about_value4_title',1,'text','Atenção ao Detalhe','about','values','2026-02-09 18:34:33','2026-08-14 17:51:14'),(116,'about_value4_title',2,'text','Attention to Detail','about','values','2026-02-09 18:34:33','2026-02-09 18:34:33'),(117,'about_value4_text',1,'textarea','Nada é deixado ao acaso. Do pequeno-almoço caseiro à decoração cuidada, tudo é pensado para o seu conforto e bem-estar.','about','values','2026-02-09 18:34:33','2026-08-14 17:51:14'),(118,'about_value4_text',2,'textarea','Nothing is left to chance. From homemade breakfast to thoughtful decoration, everything is designed for your comfort and wellbeing.','about','values','2026-02-09 18:34:33','2026-02-09 18:34:33'),(119,'about_region_label',1,'text','O Nosso Berço','about','region','2026-02-09 18:34:33','2026-08-14 17:51:14'),(120,'about_region_label',2,'text','Our Home','about','region','2026-02-09 18:34:33','2026-02-09 18:34:33'),(121,'about_region_text',1,'textarea','Onde o tempo pára e a alma respira. Uma terra de horizontes infinitos, guardiã de tradições milenares e de uma beleza natural bruta e intocada.','about','region','2026-02-09 18:34:33','2026-08-14 17:51:14'),(122,'about_region_text',2,'textarea','Where time stops and the soul breathes. A land of infinite horizons, guardian of ancient traditions and raw, untouched natural beauty.','about','region','2026-02-09 18:34:33','2026-02-09 18:34:33'),(123,'about_region_cta1',1,'text','Planear Visita','about','region','2026-02-09 18:34:33','2026-02-09 18:34:33'),(124,'about_region_cta1',2,'text','Plan Visit','about','region','2026-02-09 18:34:33','2026-02-09 18:34:33'),(125,'about_region_cta2',1,'text','O que fazer','about','region','2026-02-09 18:34:33','2026-02-09 18:34:33'),(126,'about_region_cta2',2,'text','Things to do','about','region','2026-02-09 18:34:33','2026-02-09 18:34:33'),(333,'shop_empty_message',1,'text','Esta categoria ainda não tem produtos disponíveis.','shop','main','2026-02-09 19:27:19','2026-08-14 17:12:28'),(334,'shop_empty_message',2,'text','This category does not have products available yet.','shop','main','2026-02-09 19:27:19','2026-02-09 19:52:45'),(335,'contact_success_message',1,'textarea','Obrigado pelo seu contacto. Iremos responder o mais brevemente possível.','contact','main','2026-02-09 19:27:19','2026-08-14 17:12:28'),(336,'contact_success_message',2,'textarea','Thank you for your contact. We will reply as soon as possible.','contact','main','2026-02-09 19:27:19','2026-02-09 19:47:40'),(343,'footer_tagline',1,'text','Simplicidade, acolhimento e muito amor em Mogadouro','footer','main','2026-02-09 19:47:40','2026-02-09 19:50:59'),(344,'footer_tagline',2,'text','Simplicity, warmth and love in Mogadouro','footer','main','2026-02-09 19:47:40','2026-02-09 19:50:59'),(361,'accommodation_hero_tagline',1,'text','Alojamento Local',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:00:28'),(362,'accommodation_hero_tagline',2,'text','Local Accommodation',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:00:28'),(363,'accommodation_hero_title',1,'text','A Casa do Gi',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:00:28'),(364,'accommodation_hero_title',2,'text','A Casa do Gi',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:00:28'),(365,'accommodation_hero_subtitle',1,'text','Acolhimento transmontano, momentos em família e memórias para sempre.',NULL,NULL,'2026-02-09 19:57:15','2026-08-14 17:12:28'),(366,'accommodation_hero_subtitle',2,'text','Transmontano hospitality, family moments and memories forever.',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:00:28'),(367,'accommodation_section_subtitle',1,'text','Duas Casas, Uma Experiência',NULL,NULL,'2026-02-09 19:57:15','2026-08-14 17:12:28'),(368,'accommodation_section_subtitle',2,'text','Two Houses, One Experience',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:00:28'),(369,'accommodation_section_title',1,'text','Escolha o Seu Refúgio',NULL,NULL,'2026-02-09 19:57:15','2026-08-14 17:12:28'),(370,'accommodation_section_title',2,'text','Choose Your Refuge',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:00:28'),(371,'accommodation_features_title',1,'text','O Que Ambas as Casas Oferecem',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:00:28'),(372,'accommodation_features_title',2,'text','What Both Houses Offer',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:00:28'),(373,'accommodation_feature_1',1,'text','Wi-Fi Grátis',NULL,NULL,'2026-02-09 19:57:15','2026-08-14 17:12:28'),(374,'accommodation_feature_1',2,'text','Free Wi-Fi',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:00:28'),(375,'accommodation_feature_2',1,'text','Check-in Autónomo',NULL,NULL,'2026-02-09 19:57:15','2026-08-14 17:12:28'),(376,'accommodation_feature_2',2,'text','Self Check-in',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:00:28'),(377,'accommodation_feature_3',1,'text','Roupa de Cama',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:00:28'),(378,'accommodation_feature_3',2,'text','Bed Linen',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:00:28'),(379,'accommodation_feature_4',1,'text','Localização Central',NULL,NULL,'2026-02-09 19:57:15','2026-08-14 17:12:28'),(380,'accommodation_feature_4',2,'text','Central Location',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:00:28'),(381,'activities_hero_tagline',1,'text','Descubra Mogadouro',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:01:29'),(382,'activities_hero_tagline',2,'text','Discover Mogadouro',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:01:29'),(383,'activities_hero_title',1,'text','O Que Fazer',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:01:29'),(384,'activities_hero_title',2,'text','What to Do',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:01:29'),(385,'activities_hero_subtitle',1,'text','De paisagens deslumbrantes a sabores únicos, o nordeste transmontano tem muito para oferecer.',NULL,NULL,'2026-02-09 19:57:15','2026-08-14 17:12:28'),(386,'activities_hero_subtitle',2,'text','From stunning landscapes to unique flavors, the northeast of Tras-os-Montes has much to offer.',NULL,NULL,'2026-02-09 19:57:15','2026-02-09 20:01:29'),(387,'contact_hero_tagline',1,'text','Fale Connosco',NULL,NULL,'2026-02-09 20:08:12','2026-02-09 20:08:12'),(388,'contact_hero_tagline',2,'text','Talk to Us',NULL,NULL,'2026-02-09 20:08:12','2026-02-09 20:08:12'),(389,'contact_hero_title',1,'text','Contacte-nos',NULL,NULL,'2026-02-09 20:08:12','2026-02-09 20:08:12'),(390,'contact_hero_title',2,'text','Contact Us',NULL,NULL,'2026-02-09 20:08:12','2026-02-09 20:08:12'),(391,'contact_hero_subtitle',1,'text','Tem alguma questão? Entre em contacto connosco',NULL,NULL,'2026-02-09 20:08:12','2026-08-14 17:12:28'),(392,'contact_hero_subtitle',2,'text','Have any questions? Get in touch with us',NULL,NULL,'2026-02-09 20:08:12','2026-02-09 20:08:12'),(399,'about_hero_tagline',1,'text','A Nossa História',NULL,NULL,'2026-02-09 20:08:48','2026-08-14 17:12:28'),(400,'about_hero_tagline',2,'text','Our Story',NULL,NULL,'2026-02-09 20:08:48','2026-02-09 20:08:48'),(401,'about_hero_title',1,'text','A Casa do Gi',NULL,NULL,'2026-02-09 20:08:48','2026-02-09 20:08:48'),(402,'about_hero_title',2,'text','A Casa do Gi',NULL,NULL,'2026-02-09 20:08:48','2026-02-09 20:08:48'),(403,'privacy_hero_tagline',1,'text','Informação Legal',NULL,NULL,'2026-02-09 20:08:48','2026-08-15 00:04:18'),(404,'privacy_hero_tagline',2,'text','Legal Information',NULL,NULL,'2026-02-09 20:08:48','2026-08-15 00:04:18'),(405,'privacy_hero_title',1,'text','Política de Privacidade',NULL,NULL,'2026-02-09 20:08:48','2026-08-15 00:04:18'),(406,'privacy_hero_title',2,'text','Privacy Policy',NULL,NULL,'2026-02-09 20:08:48','2026-08-15 00:04:18'),(407,'privacy_hero_subtitle',1,'textarea','A sua privacidade é importante para nós. Saiba como tratamos os seus dados.',NULL,NULL,'2026-02-09 20:08:48','2026-08-15 00:04:18'),(408,'privacy_hero_subtitle',2,'textarea','Your privacy is important to us. Learn how we handle your data.',NULL,NULL,'2026-02-09 20:08:48','2026-08-15 00:04:18'),(409,'privacy_date',1,'text','Atualizado em: 14 de Agosto de 2026',NULL,NULL,'2026-02-09 20:08:48','2026-08-15 00:04:18'),(410,'privacy_date',2,'text','Updated on: February 9, 2025',NULL,NULL,'2026-02-09 20:08:48','2026-08-15 00:04:18'),(413,'terms_hero_tagline',1,'text','Informação Legal',NULL,NULL,'2026-02-09 20:08:48','2026-08-14 22:58:33'),(414,'terms_hero_tagline',2,'text','Legal Information',NULL,NULL,'2026-02-09 20:08:48','2026-08-14 22:58:33'),(415,'terms_hero_title',1,'text','Termos e Condições',NULL,NULL,'2026-02-09 20:08:48','2026-08-14 22:58:33'),(416,'terms_hero_title',2,'text','Terms and Conditions',NULL,NULL,'2026-02-09 20:08:48','2026-08-14 22:58:33'),(417,'terms_hero_subtitle',1,'textarea','Por favor, leia atentamente os termos e condições de utilização do nosso serviço.',NULL,NULL,'2026-02-09 20:08:48','2026-08-14 22:58:33'),(418,'terms_hero_subtitle',2,'textarea','Please read carefully the terms and conditions of use of our service.',NULL,NULL,'2026-02-09 20:08:48','2026-08-14 22:58:33'),(419,'terms_date',1,'text','Atualizado em: 14 de Agosto de 2026',NULL,NULL,'2026-02-09 20:08:48','2026-08-14 22:58:33'),(420,'terms_date',2,'text','Updated on: February 9, 2025',NULL,NULL,'2026-02-09 20:08:48','2026-08-14 22:58:33'),(431,'footer_description',1,'textarea','Simplicidade, acolhimento e muito amor em Mogadouro, Portugal.',NULL,NULL,'2026-02-09 20:31:09','2026-02-09 20:34:54'),(432,'footer_description',2,'textarea','Simplicity, warmth and love in Mogadouro, Portugal.',NULL,NULL,'2026-02-09 20:31:09','2026-02-09 20:34:54'),(433,'footer_quicklinks_title',1,'text','Links Rápidos',NULL,NULL,'2026-02-09 20:31:09','2026-08-14 17:12:28'),(434,'footer_quicklinks_title',2,'text','Quick Links',NULL,NULL,'2026-02-09 20:31:09','2026-02-09 20:34:54'),(435,'footer_contact_title',1,'text','Contacto',NULL,NULL,'2026-02-09 20:31:09','2026-02-09 20:34:54'),(436,'footer_contact_title',2,'text','Contact',NULL,NULL,'2026-02-09 20:31:09','2026-02-09 20:34:54'),(437,'footer_address',1,'text','52 Avenida Nossa Senhora do Caminho, Mogadouro',NULL,NULL,'2026-02-09 20:31:09','2026-02-09 20:34:54'),(438,'footer_address',2,'text','52 Avenida Nossa Senhora do Caminho, Mogadouro',NULL,NULL,'2026-02-09 20:31:09','2026-02-09 20:34:54'),(439,'footer_book_title',1,'text','Reserve Já',NULL,NULL,'2026-02-09 20:31:09','2026-08-14 17:12:28'),(440,'footer_book_title',2,'text','Book Now',NULL,NULL,'2026-02-09 20:31:09','2026-02-09 20:34:54'),(441,'footer_rights_text',1,'text','Todos os direitos reservados.',NULL,NULL,'2026-02-09 20:31:09','2026-02-09 20:34:54'),(442,'footer_rights_text',2,'text','All rights reserved.',NULL,NULL,'2026-02-09 20:31:09','2026-02-09 20:34:54'),(443,'cookie_banner_text',1,'','Utilizamos cookies para melhorar a sua experiência no nosso website. Ao continuar a navegar, concorda com a utilização de cookies. Saiba mais nos nossos <a href=\"/acasadogi/termos-condicoes/\" class=\"text-secondary hover:underline\">termos e condições</a> e <a href=\"/acasadogi/politica-privacidade/\" class=\"text-secondary hover:underline\">política de privacidade</a>.',NULL,NULL,'2026-02-09 20:31:09','2026-08-14 22:56:00'),(444,'cookie_banner_text',2,'','We use cookies to improve your experience on our website. By continuing to browse, you agree to our use of cookies. Learn more in our <a href=\"/acasadogi/en/termos-condicoes/\" class=\"text-secondary hover:underline\">terms and conditions</a> and <a href=\"/acasadogi/en/politica-privacidade/\" class=\"text-secondary hover:underline\">privacy policy</a>.',NULL,NULL,'2026-02-09 20:31:09','2026-08-14 22:56:00'),(445,'cookie_banner_accept',1,'text','Aceitar',NULL,NULL,'2026-02-09 20:31:09','2026-02-09 20:34:54'),(446,'cookie_banner_accept',2,'text','Accept',NULL,NULL,'2026-02-09 20:31:09','2026-02-09 20:34:54'),(447,'cookie_banner_details',1,'text','Ver Detalhes',NULL,NULL,'2026-02-09 20:31:09','2026-02-09 20:34:54'),(448,'cookie_banner_details',2,'text','Details',NULL,NULL,'2026-02-09 20:31:09','2026-02-09 20:34:54'),(467,'home_image_split_left',1,'text','/uploads/content/home_image_split_left_1770815718.jpg',NULL,NULL,'2026-02-10 02:19:22','2026-02-11 13:15:18'),(468,'home_image_split_left',2,'text','/uploads/content/home_image_split_left_1770815718.jpg',NULL,NULL,'2026-02-10 02:19:22','2026-02-11 13:15:18');
/*!40000 ALTER TABLE `content_blocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `house_rule_translations`
--

DROP TABLE IF EXISTS `house_rule_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `house_rule_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `rule_text` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_rule_lang` (`rule_id`,`language_id`),
  KEY `language_id` (`language_id`),
  CONSTRAINT `house_rule_translations_ibfk_1` FOREIGN KEY (`rule_id`) REFERENCES `house_rules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `house_rule_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `house_rule_translations`
--

LOCK TABLES `house_rule_translations` WRITE;
/*!40000 ALTER TABLE `house_rule_translations` DISABLE KEYS */;
INSERT INTO `house_rule_translations` VALUES (41,21,1,'Nao sao permitidas festas ou eventos.wewew'),(42,21,2,'No parties or events allowed.'),(43,22,1,'Horario de silencio: 22h00 - 08h00.'),(44,22,2,'Quiet hours: 22:00 - 08:00.'),(45,23,1,'Proibido fumar no interior.'),(46,23,2,'No smoking inside.'),(47,24,1,'Animais de estimacao nao sao permitidos.'),(48,24,2,'Pets are not allowed.'),(49,25,1,'Respeite os vizinhos e a propriedade.'),(50,25,2,'Respect neighbors and property.'),(51,26,1,'Nao sao permitidas festas ou eventos.'),(52,26,2,'No parties or events allowed.'),(53,27,1,'Horario de silencio: 22h00 - 08h00.'),(54,27,2,'Quiet hours: 22:00 - 08:00.'),(55,28,1,'Proibido fumar no interior.'),(56,28,2,'No smoking inside.'),(57,29,1,'Animais de estimacao nao sao permitidos.'),(58,29,2,'Pets are not allowed.'),(59,30,1,'Respeite os vizinhos e a propriedade.'),(60,30,2,'Respect neighbors and property.');
/*!40000 ALTER TABLE `house_rule_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `house_rules`
--

DROP TABLE IF EXISTS `house_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `house_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accommodation_id` int(10) unsigned NOT NULL,
  `is_highlighted` tinyint(1) DEFAULT 0 COMMENT 'Show in main section (not just modal)',
  `sort_order` int(10) unsigned DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_accommodation` (`accommodation_id`),
  KEY `idx_highlighted` (`is_highlighted`),
  CONSTRAINT `house_rules_ibfk_1` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodation` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `house_rules`
--

LOCK TABLES `house_rules` WRITE;
/*!40000 ALTER TABLE `house_rules` DISABLE KEYS */;
INSERT INTO `house_rules` VALUES (21,1,1,1,'2026-02-10 18:23:27'),(22,1,1,2,'2026-02-10 18:23:27'),(23,1,1,3,'2026-02-10 18:23:27'),(24,1,0,4,'2026-02-10 18:23:27'),(25,1,0,5,'2026-02-10 18:23:27'),(26,2,1,1,'2026-02-10 18:23:27'),(27,2,1,2,'2026-02-10 18:23:27'),(28,2,1,3,'2026-02-10 18:23:27'),(29,2,0,4,'2026-02-10 18:23:27'),(30,2,0,5,'2026-02-10 18:23:27');
/*!40000 ALTER TABLE `house_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `locale` varchar(10) NOT NULL,
  `flag_icon` varchar(10) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_code` (`code`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'pt','Português','pt_PT','pt',1,1,'2026-01-19 12:51:19'),(2,'en','English','en_GB','gb',0,1,'2026-01-19 12:51:19');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `legal_section_translations`
--

DROP TABLE IF EXISTS `legal_section_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `legal_section_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_legal_lang` (`section_id`,`language_id`),
  KEY `language_id` (`language_id`),
  CONSTRAINT `legal_section_translations_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `legal_sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `legal_section_translations_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legal_section_translations`
--

LOCK TABLES `legal_section_translations` WRITE;
/*!40000 ALTER TABLE `legal_section_translations` DISABLE KEYS */;
INSERT INTO `legal_section_translations` VALUES (1,1,1,'1. Identificação do Responsável pelo Tratamento','A \"A Casa do Gi\" (doravante \"nós\" ou \"nosso\") é a entidade responsável pela recolha e tratamento dos dados pessoais submetidos através do website www.acasadogi.com. Comprometemo-nos a respeitar a sua privacidade e a proteger os seus dados pessoais em conformidade com o Regulamento Geral sobre a Proteção de Dados (RGPD) e a legislação portuguesa aplicável (Lei n.º 58/2019).'),(2,1,2,'1. Identification of the Data Controller','\"A Casa do Gi\" (hereinafter \"we\" or \"our\") is the entity responsible for the collection and processing of personal data submitted through the website www.acasadogi.com. We are committed to respecting your privacy and protecting your personal data in accordance with the General Data Protection Regulation (GDPR) and applicable Portuguese legislation (Law no. 58/2019).'),(3,2,1,'2. Que dados recolhemos e para que fim?','Recolhemos apenas os dados estritamente necessários para a interação consigo. Através do nosso formulário de contacto, podemos recolher o seu Nome, Endereço de Email, Número de Telefone e a Mensagem que nos envia.\nEstes dados são utilizados **exclusivamente** com a finalidade de responder às suas questões, pedidos de informação ou esclarecimento de dúvidas (com base no consentimento e diligências pré-contratuais). Não utilizamos os seus dados para envio de marketing não solicitado nem os vendemos a terceiros.'),(4,2,2,'2. What data do we collect and for what purpose?','We only collect the data strictly necessary for interaction with you. Through our contact form, we may collect your Name, Email Address, Phone Number, and the Message you send us.\nThese data are used **exclusively** for the purpose of answering your questions, requests for information, or clarification of doubts (based on consent and pre-contractual steps). We do not use your data for sending unsolicited marketing nor do we sell it to third parties.'),(5,3,1,'3. Plataformas de Terceiros e Reservas (GuestReady)','O nosso website não processa reservas diretas, nem recolhe dados de pagamento ou de cartão de crédito. Todo o processo de reserva de alojamento é feito através da plataforma externa gerida pela **GuestReady**. Ao clicar nos botões de \"Reservar\", será redirecionado para os servidores da GuestReady. Recomendamos a leitura da Política de Privacidade da referida plataforma, uma vez que o tratamento de dados inerentes à estadia e faturação é da sua exclusiva responsabilidade.'),(6,3,2,'3. Third-party Platforms and Bookings (GuestReady)','Our website does not process direct bookings, nor does it collect payment or credit card data. The entire accommodation booking process is done through an external platform managed by **GuestReady**. By clicking on the \"Book\" buttons, you will be redirected to GuestReady\'s servers. We recommend reading the Privacy Policy of that platform, as the data processing inherent to the stay and billing is their exclusive responsibility.'),(7,4,1,'4. Prazo de Conservação dos Dados','Os dados pessoais recolhidos atravÚs do formulßrio de contacto serÒo mantidos apenas pelo tempo estritamente necessßrio para responder ao seu pedido, ou por um perÝodo mßximo de 30 dias. Findo esse prazo de 30 dias, todos os dados submetidos serÒo apagados permanentemente, exceto se houver uma obrigaþÒo legal que exija a sua conservaþÒo.'),(8,4,2,'4. Data Retention Period','Personal data collected through the contact form will be kept only for the time strictly necessary to respond to your request, or for a maximum period of 30 days. At the end of this 30-day period, all submitted data will be permanently deleted, unless there is a legal obligation that requires its retention.'),(9,5,1,'5. Os Seus Direitos','Nos termos do RGPD, o utilizador tem o direito de solicitar o acesso, retificação, apagamento (direito ao esquecimento), limitação do tratamento e a portabilidade dos seus dados pessoais. Pode exercer estes direitos a qualquer momento entrando em contacto connosco através do email ou telefone disponibilizados na página de Contactos deste website.'),(10,5,2,'5. Your Rights','Under the GDPR, the user has the right to request access, rectification, erasure (right to be forgotten), restriction of processing, and portability of their personal data. You can exercise these rights at any time by contacting us through the email or phone number provided on the Contact page of this website.'),(23,12,1,'1. Condições Gerais de Utilização','Os presentes Termos e Condições regulam a utilização do website www.acasadogi.com. Ao aceder e navegar neste website, o utilizador aceita estes Termos e Condições na íntegra. Caso não concorde com os mesmos, deverá cessar imediatamente a utilização deste website.'),(24,12,2,'1. General Conditions of Use','These Terms and Conditions govern the use of the website www.acasadogi.com. By accessing and browsing this website, the user accepts these Terms and Conditions in full. If you do not agree with them, you must immediately cease using this website.'),(25,13,1,'2. Reservas, Pagamentos e Estadias','O website \"A Casa do Gi\" tem um caráter meramente informativo e de apresentação das nossas propriedades. Não realizamos contratos de alojamento, não cobramos taxas nem processamos pagamentos de forma direta através do nosso website. Toda a gestão de reservas, verificação de disponibilidade, preços, pagamentos e políticas de cancelamento é operada de forma independente pela plataforma **GuestReady**. Quaisquer dúvidas, alterações, pagamentos ou litígios relacionados com a reserva da estadia e alojamento devem ser tratados exclusivamente junto da GuestReady, aplicando-se os termos e condições da referida plataforma.'),(26,13,2,'2. Bookings, Payments, and Stays','The website \"A Casa do Gi\" has a purely informative character and presents our properties. We do not enter into accommodation contracts, charge fees, or process payments directly through our website. All management of bookings, availability verification, prices, payments, and cancellation policies is operated independently by the **GuestReady** platform. Any doubts, changes, payments, or disputes related to the booking of the stay and accommodation must be handled exclusively with GuestReady, applying the terms and conditions of that platform.'),(27,14,1,'3. Atividades Locais e Links de Terceiros','As informações disponibilizadas na secção \"O Que Fazer\" (Atividades) consistem em sugestões de roteiros e locais com caráter estritamente informativo. O nosso website contém ligações (links) diretas para websites de terceiros, como a Câmara Municipal de Mogadouro ou plataformas de rotas (ex: Komoot). Não exercemos qualquer controlo sobre o conteúdo, segurança, políticas de privacidade ou práticas de websites de terceiros. Como tal, isentamo-nos de qualquer responsabilidade por eventuais incorreções de informação, alterações de horários, acidentes ou problemas na prestação de serviços por essas entidades terceiras. O acesso a essas hiperligações é da inteira responsabilidade e risco do utilizador.'),(28,14,2,'3. Local Activities and Third-Party Links','The information provided in the \"Things To Do\" (Activities) section consists of suggestions for itineraries and locations of a strictly informative nature. Our website contains direct links to third-party websites, such as the Mogadouro City Hall or route platforms (e.g., Komoot). We exercise no control over the content, security, privacy policies, or practices of third-party websites. As such, we exempt ourselves from any responsibility for possible inaccuracies of information, schedule changes, accidents, or problems in the provision of services by these third entities. Access to these links is at the user\'s entire responsibility and risk.'),(29,15,1,'4. Loja e Produtos Regionais (Em Construção)','A secção \"Loja\" ou \"Produtos Regionais\" encontra-se, à presente data, em fase de desenvolvimento e construção. \"A Casa do Gi\" não efetua, através do seu website, qualquer apresentação ativa de catálogo, venda online ou transação financeira de produtos físicos. Qualquer menção a esta área tem um caráter meramente prospetivo e ilustrativo para projetos futuros.'),(30,15,2,'4. Shop and Regional Products (Under Construction)','The \"Shop\" or \"Regional Products\" section is currently in the development and construction phase. \"A Casa do Gi\" does not carry out, through its website, any active catalog presentation, online sale, or financial transaction of physical products. Any mention of this area has a purely prospective and illustrative character for future projects.'),(31,16,1,'5. Propriedade Intelectual','Todo o conteúdo presente neste website, incluindo (mas não limitado a) textos, logótipos, fotografias, imagens, design gráfico e código fonte, são propriedade exclusiva de \"A Casa do Gi\" ou de entidades que expressamente nos autorizaram a sua utilização, estando protegidos pela legislação nacional e internacional de Direitos de Autor e Propriedade Intelectual. É estritamente proibida a reprodução, cópia, distribuição ou modificação de qualquer conteúdo sem a nossa autorização prévia por escrito.'),(32,16,2,'5. Intellectual Property','All content present on this website, including (but not limited to) texts, logos, photographs, images, graphic design, and source code, is the exclusive property of \"A Casa do Gi\" or entities that expressly authorized us to use them, and are protected by national and international Copyright and Intellectual Property legislation. It is strictly prohibited to reproduce, copy, distribute, or modify any content without our prior written authorization.'),(33,17,1,'6. Alterações e Disponibilidade do Website','Reservamo-nos o direito de, a qualquer momento e sem aviso prévio, alterar, suspender ou descontinuar qualquer aspeto do website, bem como atualizar estes Termos e Condições. Recomendamos ao utilizador a consulta regular desta página para se manter informado. Não garantimos que o acesso ao website seja ininterrupto ou livre de falhas técnicas.'),(34,17,2,'6. Changes and Website Availability','We reserve the right, at any time and without prior notice, to change, suspend, or discontinue any aspect of the website, as well as to update these Terms and Conditions. We recommend the user to regularly consult this page to stay informed. We do not guarantee that access to the website will be uninterrupted or free of technical faults.'),(35,18,1,'7. Lei Aplicável e Foro Competente','Aos presentes Termos e Condições aplica-se a Lei Portuguesa. Para a resolução de qualquer litígio emergente da interpretação, aplicação ou execução dos presentes Termos, que não envolva responsabilidades afetas às plataformas parceiras de terceiros, é estipulado como exclusivamente competente o foro da Comarca de Trás-os-Montes/Bragança, com expressa renúncia a qualquer outro.'),(36,18,2,'7. Applicable Law and Competent Jurisdiction','These Terms and Conditions are governed by Portuguese Law. For the resolution of any dispute arising from the interpretation, application, or execution of these Terms, which does not involve responsibilities assigned to third-party partner platforms, the competent jurisdiction is exclusively stipulated as the Court of the District of Trás-os-Montes/Bragança, with express waiver of any other.');
/*!40000 ALTER TABLE `legal_section_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `legal_sections`
--

DROP TABLE IF EXISTS `legal_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `legal_sections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page` enum('terms','privacy') NOT NULL,
  `sort_order` int(10) unsigned DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_page` (`page`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `legal_sections`
--

LOCK TABLES `legal_sections` WRITE;
/*!40000 ALTER TABLE `legal_sections` DISABLE KEYS */;
INSERT INTO `legal_sections` VALUES (1,'privacy',1,1,'2026-08-14 22:51:07','2026-08-14 22:51:07'),(2,'privacy',2,1,'2026-08-14 22:51:07','2026-08-14 22:51:07'),(3,'privacy',3,1,'2026-08-14 22:51:07','2026-08-14 22:51:07'),(4,'privacy',4,1,'2026-08-14 22:51:07','2026-08-14 22:51:07'),(5,'privacy',5,1,'2026-08-14 22:51:07','2026-08-14 22:51:07'),(12,'terms',1,1,'2026-08-14 23:54:12','2026-08-14 23:54:12'),(13,'terms',2,1,'2026-08-14 23:54:12','2026-08-14 23:54:12'),(14,'terms',3,1,'2026-08-14 23:54:12','2026-08-14 23:54:12'),(15,'terms',4,1,'2026-08-14 23:54:12','2026-08-14 23:54:12'),(16,'terms',5,1,'2026-08-14 23:54:12','2026-08-14 23:54:12'),(17,'terms',6,1,'2026-08-14 23:54:12','2026-08-14 23:54:12'),(18,'terms',7,1,'2026-08-14 23:54:12','2026-08-14 23:54:12');
/*!40000 ALTER TABLE `legal_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(10) unsigned NOT NULL,
  `alt_text_pt` varchar(255) DEFAULT NULL,
  `alt_text_en` varchar(255) DEFAULT NULL,
  `caption_pt` varchar(500) DEFAULT NULL COMMENT 'Portuguese caption',
  `caption_en` varchar(500) DEFAULT NULL COMMENT 'English caption',
  `category` enum('gallery','content','other') DEFAULT 'other',
  `entity_type` enum('hero','accommodation','standalone','other') DEFAULT 'standalone',
  `entity_id` int(10) unsigned DEFAULT NULL COMMENT 'ID of the related entity (activity_id, hero_id, etc)',
  `is_cover` tinyint(1) DEFAULT 0 COMMENT 'Is this the cover/main image for the entity',
  `sort_order` int(10) unsigned DEFAULT 0,
  `uploaded_by` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `accommodation_id` int(10) unsigned DEFAULT NULL COMMENT 'Link to specific accommodation',
  PRIMARY KEY (`id`),
  KEY `uploaded_by` (`uploaded_by`),
  KEY `idx_category` (`category`),
  KEY `idx_sort` (`sort_order`),
  KEY `idx_media_entity` (`entity_type`,`entity_id`),
  KEY `idx_media_cover` (`is_cover`),
  CONSTRAINT `media_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` VALUES (8,'6976756e94c17_1769370990.jpg','AlojamentoQuarto8.jpg','/uploads/media/6976756e94c17_1769370990.jpg','image/jpeg',77675,'','',NULL,NULL,'gallery','standalone',NULL,0,0,1,'2026-01-25 19:56:30',1),(9,'6976756e992d1_1769370990.jpg','AlojamentoQuarto7.jpg','/uploads/media/6976756e992d1_1769370990.jpg','image/jpeg',78878,'','',NULL,NULL,'gallery','standalone',NULL,0,0,1,'2026-01-25 19:56:30',1),(10,'6976756e9adb4_1769370990.jpg','AlojamentoQuarto6.jpg','/uploads/media/6976756e9adb4_1769370990.jpg','image/jpeg',86814,'','',NULL,NULL,'gallery','standalone',NULL,0,0,1,'2026-01-25 19:56:30',1),(11,'6976756e9c308_1769370990.jpg','AlojamentoQuarto5.jpg','/uploads/media/6976756e9c308_1769370990.jpg','image/jpeg',82471,'','',NULL,NULL,'gallery','standalone',NULL,0,0,1,'2026-01-25 19:56:30',1),(12,'6976756e9d47d_1769370990.jpg','AlojamentoQuarto4.jpg','/uploads/media/6976756e9d47d_1769370990.jpg','image/jpeg',100462,'','',NULL,NULL,'gallery','standalone',NULL,0,0,1,'2026-01-25 19:56:30',1),(13,'6976756e9e5a6_1769370990.jpg','AlojamentoQuarto3.jpg','/uploads/media/6976756e9e5a6_1769370990.jpg','image/jpeg',93303,'','',NULL,NULL,'gallery','standalone',NULL,0,0,1,'2026-01-25 19:56:30',1),(14,'6976756e9f8b2_1769370990.jpg','AlojamentoQuarto2.jpg','/uploads/media/6976756e9f8b2_1769370990.jpg','image/jpeg',75318,'','',NULL,NULL,'gallery','standalone',NULL,0,0,1,'2026-01-25 19:56:30',1),(15,'6976756ea0995_1769370990.jpg','AlojamentoQuarto1.jpg','/uploads/media/6976756ea0995_1769370990.jpg','image/jpeg',97800,'Quarto Cama de Casal','Double Bed Room',NULL,NULL,'gallery','standalone',NULL,0,0,1,'2026-01-25 19:56:30',1),(19,'6976986c696fb_1769379948.jpg','AlojamentoQuarto49.jpg','/uploads/media/6976986c696fb_1769379948.jpg','image/jpeg',57394,NULL,NULL,NULL,NULL,'other','standalone',NULL,0,0,1,'2026-01-25 22:25:48',NULL),(20,'6976986c6ad28_1769379948.jpg','AlojamentoQuarto48.jpg','/uploads/media/6976986c6ad28_1769379948.jpg','image/jpeg',163886,NULL,NULL,NULL,NULL,'other','standalone',NULL,0,0,1,'2026-01-25 22:25:48',NULL),(21,'6976986c6c3a6_1769379948.jpg','AlojamentoQuarto47.jpg','/uploads/media/6976986c6c3a6_1769379948.jpg','image/jpeg',129213,NULL,NULL,NULL,NULL,'other','standalone',NULL,0,0,1,'2026-01-25 22:25:48',NULL),(22,'6976986c6e346_1769379948.jpg','AlojamentoQuarto46.jpg','/uploads/media/6976986c6e346_1769379948.jpg','image/jpeg',98581,NULL,NULL,NULL,NULL,'other','standalone',NULL,0,0,1,'2026-01-25 22:25:48',NULL),(23,'6976986c7080e_1769379948.jpg','AlojamentoQuarto45.jpg','/uploads/media/6976986c7080e_1769379948.jpg','image/jpeg',67252,'','',NULL,NULL,'gallery','standalone',NULL,0,0,1,'2026-01-25 22:25:48',1),(24,'6976986c72fde_1769379948.jpg','AlojamentoQuarto44.jpg','/uploads/media/6976986c72fde_1769379948.jpg','image/jpeg',63988,NULL,NULL,NULL,NULL,'other','standalone',NULL,0,0,1,'2026-01-25 22:25:48',NULL),(25,'accommodation_697698bc063af.jpg','AlojamentoQuarto26.jpg','/uploads/accommodation/accommodation_697698bc063af.jpg','image/jpeg',69718,'','',NULL,NULL,'gallery','standalone',NULL,0,1,NULL,'2026-01-25 22:27:08',1),(37,'hero_about_1770084603.png','hero_about_1770084603.png','/uploads/heroes/hero_about_1770084603.png','image/jpeg',0,NULL,NULL,NULL,NULL,'content','hero',4,1,0,NULL,'2026-02-03 00:18:35',NULL),(38,'hero_contact_1770084865.jpg','hero_contact_1770084865.jpg','/uploads/heroes/hero_contact_1770084865.jpg','image/jpeg',0,NULL,NULL,NULL,NULL,'content','hero',5,1,0,NULL,'2026-02-03 00:18:35',NULL),(41,'hero_accommodation_main_1770400137.jpg','MogadouroAlojamento.jpg','/uploads/heroes/hero_accommodation_main_1770400137.jpg','image/jpeg',267538,NULL,NULL,NULL,NULL,'content','hero',2,1,0,1,'2026-02-06 17:48:57',NULL),(42,'hero_shop_1770400179.png','MogadouroLogin2.png','/uploads/heroes/hero_shop_1770400179.png','image/png',48057,NULL,NULL,NULL,NULL,'content','hero',6,1,0,1,'2026-02-06 17:49:39',NULL),(43,'hero_activities_1770400187.jpg','MogadouroAtividades.jpg','/uploads/heroes/hero_activities_1770400187.jpg','image/jpeg',618067,NULL,NULL,NULL,NULL,'content','hero',3,1,0,1,'2026-02-06 17:49:47',NULL),(44,'hero_home_1770400193.jpg','MogadouroAtividades.jpg','/uploads/heroes/hero_home_1770400193.jpg','image/jpeg',618067,NULL,NULL,NULL,NULL,'content','hero',1,1,0,1,'2026-02-06 17:49:53',NULL),(57,'home_image_split_left_1770689962.png','Atelier Logo.png','/uploads/content/home_image_split_left_1770689962.png','image/png',1084608,NULL,NULL,NULL,NULL,'content','standalone',NULL,0,0,1,'2026-02-10 02:19:22',NULL),(59,'home_image_split_left_1770815718.jpg','IgrejaMatriz.jpg','/uploads/content/home_image_split_left_1770815718.jpg','image/jpeg',123974,NULL,NULL,NULL,NULL,'content','standalone',NULL,0,0,1,'2026-02-11 13:15:18',NULL);
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_heroes`
--

DROP TABLE IF EXISTS `page_heroes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page_heroes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_key` varchar(50) NOT NULL COMMENT 'Unique page identifier',
  `page_name_pt` varchar(100) NOT NULL COMMENT 'Page name in Portuguese',
  `page_name_en` varchar(100) NOT NULL COMMENT 'Page name in English',
  `hero_overlay_opacity` decimal(3,2) DEFAULT 0.40 COMMENT 'Overlay darkness (0-1)',
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(10) unsigned DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_key` (`page_key`),
  KEY `idx_page_key` (`page_key`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_heroes`
--

LOCK TABLES `page_heroes` WRITE;
/*!40000 ALTER TABLE `page_heroes` DISABLE KEYS */;
INSERT INTO `page_heroes` VALUES (1,'home','Página Inicial','Homepage',0.40,1,1,'2026-02-03 00:18:35','2026-02-03 00:18:35'),(2,'accommodation_main','Alojamento','Accommodation (Main Page)',0.40,1,2,'2026-02-03 00:18:35','2026-02-06 18:11:40'),(3,'activities','Atividades','Activities',0.40,1,3,'2026-02-03 00:18:35','2026-02-03 00:18:35'),(4,'about','Sobre Nós','About Us',0.40,1,4,'2026-02-03 00:18:35','2026-02-03 02:10:03'),(5,'contact','Contactos','Contact',0.40,1,5,'2026-02-03 00:18:35','2026-02-03 02:14:25'),(6,'shop','Loja','Shop',0.40,1,6,'2026-02-03 00:18:35','2026-02-03 00:18:35'),(8,'product_detail','Produto (Detalhe)','Product (Detail)',0.40,1,7,'2026-02-08 18:45:41','2026-02-08 18:45:41'),(9,'cart','Carrinho de Compras','Shopping Cart',0.40,1,8,'2026-02-08 18:45:42','2026-02-08 18:45:42'),(10,'checkout','Finalizar Compra','Checkout',0.40,1,9,'2026-02-08 18:45:42','2026-02-08 18:45:42'),(11,'privacy_policy','Política de Privacidade','Privacy Policy',0.40,1,10,'2026-02-09 20:08:12','2026-02-10 21:37:27'),(12,'terms_conditions','Termos e Condições','Terms and Conditions',0.40,1,11,'2026-02-09 20:08:12','2026-02-10 21:38:00');
/*!40000 ALTER TABLE `page_heroes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','textarea','boolean','number','json','email','url') DEFAULT 'text',
  `setting_group` varchar(50) DEFAULT 'general',
  `description` varchar(255) DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `idx_key` (`setting_key`),
  KEY `idx_group` (`setting_group`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'site_name','A Casa do Gi','text','general','Nome do site',1,'2026-01-19 12:51:19','2026-01-19 12:51:19'),(2,'site_tagline_pt','Simplicidade, acolhimento e muito amor','text','general','Tagline PT',1,'2026-01-19 12:51:19','2026-01-20 16:22:55'),(3,'site_tagline_en','Simplicity, warmth and love','text','general','Tagline EN',1,'2026-01-19 12:51:19','2026-01-20 16:22:55'),(4,'contact_email','geral@acasadogi.pt','email','contact','Email principal',1,'2026-01-19 12:51:19','2026-01-20 16:22:55'),(5,'contact_phone','+351 912 345 678','text','contact','Telefone',1,'2026-01-19 12:51:19','2026-01-20 16:22:55'),(6,'contact_address','Rua Principal, 123\n5200 Mogadouro\nPortugal','textarea','contact','Morada',1,'2026-01-19 12:51:19','2026-01-20 16:22:55'),(7,'contact_form_enabled','1','boolean','contact','Formulario ativo',0,'2026-01-19 12:51:19','2026-01-19 12:51:19'),(8,'facebook_url','https://facebook.com/acasadogi','url','social','URL Facebook',1,'2026-01-19 12:51:19','2026-01-20 16:22:55'),(9,'instagram_url','https://instagram.com/acasadogi','url','social','URL Instagram',1,'2026-01-19 12:51:19','2026-01-20 16:22:55'),(10,'booking_url','https://www.booking.com/','url','booking','URL Booking.com',1,'2026-01-19 12:51:19','2026-01-20 16:22:55'),(11,'airbnb_url','https://www.airbnb.com/','url','booking','URL Airbnb',1,'2026-01-19 12:51:19','2026-01-20 16:22:55'),(13,'shop_enabled','1','boolean','shop','Loja ativa',0,'2026-01-19 12:51:19','2026-01-19 12:51:19'),(14,'shop_shipping_fee','5.00','number','shop','Taxa de envio',0,'2026-01-19 12:51:19','2026-01-19 12:51:19'),(15,'shop_free_shipping_above','50.00','number','shop','Portes gratis acima de',0,'2026-01-19 12:51:19','2026-01-19 12:51:19'),(16,'maintenance_mode','0','boolean','general','Modo manutencao',0,'2026-01-19 12:51:19','2026-02-11 21:33:53'),(17,'free_shipping_threshold','50','number','shop',NULL,0,'2026-01-20 16:22:55','2026-01-20 16:22:55'),(18,'shipping_cost','5','number','shop',NULL,0,'2026-01-20 16:22:55','2026-01-20 16:22:55'),(82,'shop_mode','manual','text','shop','Modo da loja: active, manual, closed',0,'2026-02-07 20:01:43','2026-02-09 00:34:35'),(83,'site_description','','text','general','Descrição (SEO)',0,'2026-02-11 02:38:34','2026-02-11 02:38:34'),(84,'guestready_url_casa1','https://book.guestready.com/pt/properties/mogadouro/fuga-ecletica-em-mogadouro/72622','url','reservations','Link GuestReady - Casa do Gi 1',0,'2026-08-14 21:48:24','2026-08-14 22:40:27'),(85,'guestready_url_casa2','https://book.guestready.com/pt/properties/mogadouro/refugio-acolhedor-ecletico-em-mogadouro/72624','url','reservations','Link GuestReady - Casa do Gi 2',0,'2026-08-14 21:48:24','2026-08-14 22:40:49');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spam_emails`
--

DROP TABLE IF EXISTS `spam_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spam_emails` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `reason` varchar(500) DEFAULT NULL COMMENT 'Why this email was marked as spam',
  `blocked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spam_emails`
--

LOCK TABLES `spam_emails` WRITE;
/*!40000 ALTER TABLE `spam_emails` DISABLE KEYS */;
/*!40000 ALTER TABLE `spam_emails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `v_hero_media`
--

DROP TABLE IF EXISTS `v_hero_media`;
/*!50001 DROP VIEW IF EXISTS `v_hero_media`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_hero_media` AS SELECT
 1 AS `id`,
  1 AS `filename`,
  1 AS `original_name`,
  1 AS `file_path`,
  1 AS `file_type`,
  1 AS `file_size`,
  1 AS `alt_text_pt`,
  1 AS `alt_text_en`,
  1 AS `caption_pt`,
  1 AS `caption_en`,
  1 AS `category`,
  1 AS `entity_type`,
  1 AS `entity_id`,
  1 AS `is_cover`,
  1 AS `sort_order`,
  1 AS `uploaded_by`,
  1 AS `created_at`,
  1 AS `accommodation_id`,
  1 AS `page_key`,
  1 AS `is_active` */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v_hero_media`
--

/*!50001 DROP VIEW IF EXISTS `v_hero_media`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_hero_media` AS select `m`.`id` AS `id`,`m`.`filename` AS `filename`,`m`.`original_name` AS `original_name`,`m`.`file_path` AS `file_path`,`m`.`file_type` AS `file_type`,`m`.`file_size` AS `file_size`,`m`.`alt_text_pt` AS `alt_text_pt`,`m`.`alt_text_en` AS `alt_text_en`,`m`.`caption_pt` AS `caption_pt`,`m`.`caption_en` AS `caption_en`,`m`.`category` AS `category`,`m`.`entity_type` AS `entity_type`,`m`.`entity_id` AS `entity_id`,`m`.`is_cover` AS `is_cover`,`m`.`sort_order` AS `sort_order`,`m`.`uploaded_by` AS `uploaded_by`,`m`.`created_at` AS `created_at`,`m`.`accommodation_id` AS `accommodation_id`,`ph`.`page_key` AS `page_key`,`ph`.`is_active` AS `is_active` from (`media` `m` join `page_heroes` `ph` on(`m`.`entity_id` = `ph`.`id`)) where `m`.`entity_type` = 'hero' */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-15  1:15:06
