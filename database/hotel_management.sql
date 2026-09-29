-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: hotel_management
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
-- Table structure for table `booking_service`
--

DROP TABLE IF EXISTS `booking_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking_service` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint(20) unsigned NOT NULL,
  `service_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(12,0) NOT NULL,
  `total_price` decimal(12,0) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_service_booking_id_foreign` (`booking_id`),
  KEY `booking_service_service_id_foreign` (`service_id`),
  CONSTRAINT `booking_service_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `booking_service_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_service`
--

LOCK TABLES `booking_service` WRITE;
/*!40000 ALTER TABLE `booking_service` DISABLE KEYS */;
INSERT INTO `booking_service` VALUES (1,1,1,3,150000,450000,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(2,1,2,2,80000,160000,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(3,3,1,2,150000,300000,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(4,3,4,4,15000,60000,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(5,5,7,1,500000,500000,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(6,5,3,2,350000,700000,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(7,5,6,3,35000,105000,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(8,4,1,2,150000,300000,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(9,4,10,1,50000,50000,'2026-09-29 07:28:27','2026-09-29 07:28:27');
/*!40000 ALTER TABLE `booking_service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `room_id` bigint(20) unsigned NOT NULL,
  `guest_name` varchar(255) NOT NULL,
  `guest_phone` varchar(20) NOT NULL,
  `guest_email` varchar(255) DEFAULT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `guests_count` int(11) NOT NULL DEFAULT 1,
  `status` enum('pending','confirmed','checked_in','checked_out','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
  `total_price` decimal(12,0) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bookings_user_id_foreign` (`user_id`),
  KEY `bookings_room_id_foreign` (`room_id`),
  CONSTRAINT `bookings_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,3,1,'Lê Văn Hùng','0987654321','hung.le@gmail.com','2026-09-19','2026-09-22',2,'checked_out','paid',1350000,'Khách yêu cầu phòng yên tĩnh.','2026-09-29 07:28:27','2026-09-29 07:28:27'),(2,4,9,'Phạm Thị Mai','0976543210','mai.pham@gmail.com','2026-09-21','2026-09-24',1,'checked_out','paid',3600000,NULL,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(3,5,2,'Hoàng Đức Anh','0965432109','anh.hoang@gmail.com','2026-09-27','2026-09-30',2,'checked_in','unpaid',1350000,'Khách đi công tác, cần hoá đơn VAT.','2026-09-29 07:28:27','2026-09-29 07:28:27'),(4,6,9,'Ngô Thanh Tùng','0954321098','tung.ngo@gmail.com','2026-09-28','2026-10-02',2,'checked_in','partial',4800000,'Đặt cọc 2.000.000đ.','2026-09-29 07:28:27','2026-09-29 07:28:27'),(5,7,14,'Vũ Minh Châu','0943210987','chau.vu@gmail.com','2026-09-28','2026-10-01',3,'checked_in','unpaid',7500000,'Khách VIP, yêu cầu hoa tươi trong phòng.','2026-09-29 07:28:27','2026-09-29 07:28:27'),(6,8,6,'Đặng Quốc Bảo','0932109876','bao.dang@gmail.com','2026-10-01','2026-10-04',2,'confirmed','paid',2250000,'Đã thanh toán online qua VNPAY.','2026-09-29 07:28:27','2026-09-29 07:28:27'),(7,9,12,'Bùi Thị Hương','0921098765','huong.bui@gmail.com','2026-10-04','2026-10-06',2,'confirmed','unpaid',2400000,'Kỷ niệm ngày cưới, yêu cầu trang trí phòng.','2026-09-29 07:28:27','2026-09-29 07:28:27'),(8,10,7,'Trịnh Văn Nam','0910987654','nam.trinh@gmail.com','2026-10-06','2026-10-09',1,'pending','unpaid',2250000,NULL,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(9,3,13,'Lê Văn Hùng','0987654321','hung.le@gmail.com','2026-09-26','2026-09-28',4,'cancelled','unpaid',5000000,'Khách huỷ do thay đổi lịch trình.','2026-09-29 07:28:27','2026-09-29 07:28:27'),(10,NULL,3,'David Johnson','0899887766','david.j@yahoo.com','2026-09-30','2026-10-03',1,'confirmed','unpaid',1350000,'Khách nước ngoài, walk-in tại quầy lễ tân.','2026-09-29 07:28:27','2026-09-29 07:28:27');
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `unit_price` decimal(12,0) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `line_total` decimal(12,0) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_items_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
INSERT INTO `invoice_items` VALUES (1,1,'Phòng 101 (Standard) × 3 đêm',450000,3,1350000,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(2,1,'Bữa sáng buffet × 3',150000,3,450000,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(3,1,'Giặt ủi quần áo × 2',80000,2,160000,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(4,2,'Phòng 301 (Deluxe) × 3 đêm',1200000,3,3600000,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(5,3,'Phòng 202 (Superior) × 3 đêm',750000,3,2250000,'2026-09-29 07:28:27','2026-09-29 07:28:27');
/*!40000 ALTER TABLE `invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` bigint(20) unsigned NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `issued_at` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `subtotal` decimal(12,0) NOT NULL,
  `tax` decimal(12,0) NOT NULL DEFAULT 0,
  `total` decimal(12,0) NOT NULL,
  `status` enum('draft','paid','cancelled') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_booking_id_foreign` (`booking_id`),
  CONSTRAINT `invoices_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,1,'INV-2024-0001','2026-09-22','2026-09-22',1960000,196000,2156000,'paid','2026-09-29 07:28:27','2026-09-29 07:28:27'),(2,2,'INV-2024-0002','2026-09-24','2026-09-24',3600000,360000,3960000,'paid','2026-09-29 07:28:27','2026-09-29 07:28:27'),(3,6,'INV-2024-0003','2026-09-29','2026-10-01',2250000,225000,2475000,'paid','2026-09-29 07:28:27','2026-09-29 07:28:27');
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_01_01_000000_create_roles_table',1),(5,'2024_01_01_000001_add_fields_to_users_table',1),(6,'2024_01_01_000010_create_room_types_table',1),(7,'2024_01_01_000011_create_rooms_table',1),(8,'2024_01_01_000020_create_bookings_table',1),(9,'2024_01_01_000021_create_services_table',1),(10,'2024_01_01_000022_create_booking_service_table',1),(11,'2024_01_01_000030_create_invoices_table',1),(12,'2024_01_01_000031_create_invoice_items_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','Quản trị viên','2026-09-29 07:28:23','2026-09-29 07:28:23'),(2,'receptionist','Lễ tân','2026-09-29 07:28:23','2026-09-29 07:28:23'),(3,'customer','Khách hàng','2026-09-29 07:28:23','2026-09-29 07:28:23');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_types`
--

DROP TABLE IF EXISTS `room_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `base_price` decimal(12,0) NOT NULL,
  `capacity` int(11) NOT NULL,
  `area` double DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_types`
--

LOCK TABLES `room_types` WRITE;
/*!40000 ALTER TABLE `room_types` DISABLE KEYS */;
INSERT INTO `room_types` VALUES (1,'Standard','Phòng tiêu chuẩn với đầy đủ tiện nghi cơ bản. Bao gồm giường đôi hoặc 2 giường đơn, điều hoà, TV màn hình phẳng 32 inch, minibar, Wi-Fi miễn phí, phòng tắm riêng với vòi sen.',450000,2,22,'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800','2026-09-29 07:28:27','2026-09-29 07:28:27'),(2,'Superior','Phòng hạng ưu đãi rộng rãi hơn với ban công riêng. Bao gồm giường King-size, điều hoà, TV 43 inch, minibar, Wi-Fi miễn phí, bàn làm việc, phòng tắm với bồn tắm đứng và đồ dùng vệ sinh cao cấp.',750000,2,30,'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800','2026-09-29 07:28:27','2026-09-29 07:28:27'),(3,'Deluxe','Phòng sang trọng với tầm nhìn thành phố tuyệt đẹp. Bao gồm giường King-size, ghế sofa, điều hoà 2 chiều, TV 55 inch, minibar đầy đủ, Wi-Fi tốc độ cao, bàn làm việc, tủ quần áo, phòng tắm rộng với bồn tắm và vòi sen riêng biệt, áo choàng tắm và dép.',1200000,3,40,'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800','2026-09-29 07:28:27','2026-09-29 07:28:27'),(4,'Suite','Phòng Suite cao cấp nhất với phòng khách và phòng ngủ riêng biệt. Bao gồm giường King-size, sofa phòng khách, bàn ăn 4 người, điều hoà 2 chiều, TV 65 inch, hệ thống âm thanh, minibar cao cấp, Wi-Fi tốc độ cao, ban công rộng view toàn cảnh, phòng tắm đá cẩm thạch với bồn tắm jacuzzi, đồ dùng vệ sinh hạng sang, máy pha cà phê Nespresso.',2500000,4,65,'https://images.unsplash.com/photo-1591088398332-8a7791972843?w=800','2026-09-29 07:28:27','2026-09-29 07:28:27');
/*!40000 ALTER TABLE `room_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rooms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `room_number` varchar(255) NOT NULL,
  `room_type_id` bigint(20) unsigned NOT NULL,
  `floor` int(11) NOT NULL,
  `status` enum('available','booked','occupied','maintenance','cleaning') NOT NULL DEFAULT 'available',
  `price_override` decimal(12,0) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rooms_room_number_unique` (`room_number`),
  KEY `rooms_room_type_id_foreign` (`room_type_id`),
  CONSTRAINT `rooms_room_type_id_foreign` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (1,'101',1,1,'available',NULL,'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600','2026-09-29 07:28:27','2026-09-29 07:28:27'),(2,'102',1,1,'occupied',NULL,'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=600','2026-09-29 07:28:27','2026-09-29 07:28:27'),(3,'103',1,1,'available',NULL,'https://images.unsplash.com/photo-1631049552057-403cdb8f0658?w=600','2026-09-29 07:28:27','2026-09-29 07:28:27'),(4,'104',1,1,'cleaning',NULL,'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?w=600','2026-09-29 07:28:27','2026-09-29 07:28:27'),(5,'201',2,2,'available',NULL,'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=600','2026-09-29 07:28:27','2026-09-29 07:28:27'),(6,'202',2,2,'booked',NULL,'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=600','2026-09-29 07:28:27','2026-09-29 07:28:27'),(7,'203',2,2,'available',NULL,'https://images.unsplash.com/photo-1586105251261-72a756497a11?w=600','2026-09-29 07:28:27','2026-09-29 07:28:27'),(8,'204',2,2,'maintenance',NULL,'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?w=600','2026-09-29 07:28:27','2026-09-29 07:28:27'),(9,'301',3,3,'occupied',NULL,'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600','2026-09-29 07:28:27','2026-09-29 07:28:27'),(10,'302',3,3,'available',NULL,'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=600','2026-09-29 07:28:27','2026-09-29 07:28:27'),(11,'303',3,3,'available',NULL,'https://images.unsplash.com/photo-1590073242678-70ee3fc28e8e?w=600','2026-09-29 07:28:27','2026-09-29 07:28:27'),(12,'304',3,3,'booked',NULL,'https://images.unsplash.com/photo-1564078516393-cf04bd96897b?w=600','2026-09-29 07:28:27','2026-09-29 07:28:27'),(13,'401',4,4,'available',NULL,'https://images.unsplash.com/photo-1591088398332-8a7791972843?w=600','2026-09-29 07:28:27','2026-09-29 07:28:27'),(14,'402',4,4,'occupied',NULL,'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=600','2026-09-29 07:28:27','2026-09-29 07:28:27'),(15,'403',4,4,'available',NULL,'https://images.unsplash.com/photo-1631049421450-348ccd7f8949?w=600','2026-09-29 07:28:27','2026-09-29 07:28:27'),(16,'404',4,4,'available',NULL,'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=600','2026-09-29 07:28:27','2026-09-29 07:28:27');
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,0) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'Bữa sáng buffet','Buffet sáng tại nhà hàng tầng 1 (6h - 10h)',150000,1,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(2,'Giặt ủi quần áo','Giặt và ủi phẳng, trả trong ngày',80000,1,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(3,'Đưa đón sân bay','Xe sedan đưa đón sân bay Tân Sơn Nhất (1 chiều)',350000,1,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(4,'Nước suối đóng chai','Nước khoáng Lavie 500ml',15000,1,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(5,'Minibar – Nước ngọt','Coca-Cola / Pepsi / 7Up lon 330ml',25000,1,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(6,'Minibar – Bia','Bia Tiger / Heineken lon 330ml',35000,1,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(7,'Spa & Massage','Massage toàn thân 60 phút tại Spa tầng 5',500000,1,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(8,'Thuê xe máy','Thuê xe máy tay ga Honda Air Blade (1 ngày)',120000,1,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(9,'Phụ thu giường phụ','Thêm 1 giường đơn phụ trong phòng',200000,1,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(10,'Dịch vụ phòng (Room service)','Gọi đồ ăn / thức uống về phòng',50000,1,'2026-09-29 07:28:27','2026-09-29 07:28:27');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Nguyễn Văn Admin','admin@hotel.com',NULL,'$2y$12$ih98.T/E/Z9Xt7KKIV5GTO1AlOo8sqrwLQOul0CI543NQvNlPeZqm',1,'0901234567',NULL,'2026-09-29 07:28:24','2026-09-29 07:28:24'),(2,'Trần Thị Lễ Tân','staff@hotel.com',NULL,'$2y$12$hn1KiMad4CHtDNc5t0K.s.vgo.fQLPs7tdHTA1whqw5jNV6VHDv.y',2,'0912345678',NULL,'2026-09-29 07:28:24','2026-09-29 07:28:24'),(3,'Lê Văn Hùng','hung.le@gmail.com',NULL,'$2y$12$c8lBuO.axeNJl7FVnlyj1eEOnaGCA7k9kd7L1tr7cuvc9ceTtRPrm',3,'0987654321',NULL,'2026-09-29 07:28:25','2026-09-29 07:28:25'),(4,'Phạm Thị Mai','mai.pham@gmail.com',NULL,'$2y$12$pxlc3gkrck1aCcG1J8BcPetjr3xqFAhXx5eemrYURfoOuhSYEazMS',3,'0976543210',NULL,'2026-09-29 07:28:25','2026-09-29 07:28:25'),(5,'Hoàng Đức Anh','anh.hoang@gmail.com',NULL,'$2y$12$z3YAlYu20DJutze2CPqPcepMuZCrouKUvCSfvApUsu0cMjIdjKuWa',3,'0965432109',NULL,'2026-09-29 07:28:25','2026-09-29 07:28:25'),(6,'Ngô Thanh Tùng','tung.ngo@gmail.com',NULL,'$2y$12$WdF2lOc0St7SoBH3jdHhveFtAREoC1mbPphDly2MWgTuiUGtRSJFq',3,'0954321098',NULL,'2026-09-29 07:28:26','2026-09-29 07:28:26'),(7,'Vũ Minh Châu','chau.vu@gmail.com',NULL,'$2y$12$a15SPZrQG2GO6HNSlKCCp.s3mnVcfF42k7myMOEGF5h6fWaAxID66',3,'0943210987',NULL,'2026-09-29 07:28:26','2026-09-29 07:28:26'),(8,'Đặng Quốc Bảo','bao.dang@gmail.com',NULL,'$2y$12$4yQ2BX9GrUeQM.1afJIbuOfU1qUInHjNQNCRvPJ2hNKkX7bw/ln/u',3,'0932109876',NULL,'2026-09-29 07:28:26','2026-09-29 07:28:26'),(9,'Bùi Thị Hương','huong.bui@gmail.com',NULL,'$2y$12$w0IJT1TlN3Rmf3NcLPgl5O8x/jFyPIEbsiYYnlSl7Rd7.IR8Nq9Ly',3,'0921098765',NULL,'2026-09-29 07:28:27','2026-09-29 07:28:27'),(10,'Trịnh Văn Nam','nam.trinh@gmail.com',NULL,'$2y$12$K0/FMolLxH.B75AJNqSoJOsl6MeoiENomH///GxHwwQv7zpBK1Z6m',3,'0910987654',NULL,'2026-09-29 07:28:27','2026-09-29 07:28:27');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-29 21:30:21
