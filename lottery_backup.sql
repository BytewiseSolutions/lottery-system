-- MySQL dump 10.13  Distrib 9.5.0, for macos26.1 (arm64)
--
-- Host: localhost    Database: lottery_db
-- ------------------------------------------------------
-- Server version	9.4.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `entries`
--

DROP TABLE IF EXISTS `entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `entries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `lottery` varchar(50) NOT NULL,
  `numbers` json NOT NULL,
  `bonus_numbers` json NOT NULL,
  `draw_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lottery_draw` (`lottery`,`draw_date`),
  KEY `idx_user_date` (`user_id`,`created_at`),
  CONSTRAINT `entries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entries`
--

LOCK TABLES `entries` WRITE;
/*!40000 ALTER TABLE `entries` DISABLE KEYS */;
INSERT INTO `entries` VALUES (1,1,'Monday Lotto','[17, 19, 56, 60, 61]','[6, 70]','2026-01-19','2026-01-17 12:02:26'),(2,1,'Monday Lotto','[2, 31, 38, 44, 68]','[23, 55]','2026-01-19','2026-01-17 12:08:38'),(3,1,'Monday Lotto','[3, 6, 56, 70, 75]','[38, 69]','2026-01-19','2026-01-17 12:18:15');
/*!40000 ALTER TABLE `entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otp_verifications`
--

DROP TABLE IF EXISTS `otp_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `otp_verifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `otp_type` enum('email','phone') NOT NULL,
  `expires_at` timestamp NOT NULL,
  `is_used` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `otp_verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otp_verifications`
--

LOCK TABLES `otp_verifications` WRITE;
/*!40000 ALTER TABLE `otp_verifications` DISABLE KEYS */;
INSERT INTO `otp_verifications` VALUES (1,1,'716559','email','2026-01-10 19:13:32',0,'2026-01-10 21:03:32'),(2,1,'420224','phone','2026-01-10 19:13:32',0,'2026-01-10 21:03:32'),(3,1,'815763','email','2026-01-10 19:15:13',0,'2026-01-10 21:05:13'),(4,2,'961988','email','2026-01-10 19:41:09',0,'2026-01-10 21:31:09'),(5,2,'891679','phone','2026-01-10 19:41:09',0,'2026-01-10 21:31:09');
/*!40000 ALTER TABLE `otp_verifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `past_draws`
--

DROP TABLE IF EXISTS `past_draws`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `past_draws` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lottery` varchar(50) NOT NULL,
  `draw_date` date NOT NULL,
  `winning_numbers` json NOT NULL,
  `bonus_numbers` json NOT NULL,
  `jackpot` decimal(10,2) NOT NULL,
  `winners` int DEFAULT '0',
  `status` varchar(20) DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `past_draws`
--

LOCK TABLES `past_draws` WRITE;
/*!40000 ALTER TABLE `past_draws` DISABLE KEYS */;
INSERT INTO `past_draws` VALUES (1,'Monday Lotto','2026-01-19','[]','[]',10.03,0,'completed','2026-01-20 08:39:10');
/*!40000 ALTER TABLE `past_draws` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rate_limits`
--

DROP TABLE IF EXISTS `rate_limits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rate_limits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `endpoint` varchar(50) NOT NULL,
  `attempts` int DEFAULT '1',
  `last_attempt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ip_action` (`ip_address`,`action_type`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rate_limits`
--

LOCK TABLES `rate_limits` WRITE;
/*!40000 ALTER TABLE `rate_limits` DISABLE KEYS */;
INSERT INTO `rate_limits` VALUES (1,'::1','register','register',1,'2026-01-10 21:03:32','2026-01-10 21:03:32'),(2,'1','play','play',1,'2026-01-10 21:09:15','2026-01-10 21:09:15'),(3,'1','play','play',1,'2026-01-10 21:10:59','2026-01-10 21:10:59'),(4,'1','play','play',1,'2026-01-10 21:12:13','2026-01-10 21:12:13'),(5,'2','play','play',1,'2026-01-10 21:32:50','2026-01-10 21:32:50');
/*!40000 ALTER TABLE `rate_limits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `results`
--

DROP TABLE IF EXISTS `results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `results` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lottery` varchar(50) NOT NULL,
  `winning_numbers` json NOT NULL,
  `bonus_numbers` json NOT NULL,
  `draw_date` date NOT NULL,
  `jackpot` varchar(20) NOT NULL,
  `winners` int DEFAULT '0',
  `status` varchar(20) DEFAULT 'published',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `results`
--

LOCK TABLES `results` WRITE;
/*!40000 ALTER TABLE `results` DISABLE KEYS */;
INSERT INTO `results` VALUES (9,'Monday Lotto','[1, 34, 56, 45, 12]','[75, 70]','2026-01-11','15',0,'published','','2026-01-11 20:07:55'),(11,'Monday Lotto','[12, 13, 14, 15, 16]','[10, 20]','2026-01-19','810.03',0,'published','','2026-01-17 12:34:49');
/*!40000 ALTER TABLE `results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `upcoming_draws`
--

DROP TABLE IF EXISTS `upcoming_draws`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `upcoming_draws` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lottery` varchar(50) NOT NULL,
  `draw_date` datetime NOT NULL,
  `jackpot` decimal(10,2) DEFAULT '10.00',
  `status` varchar(20) DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_lottery_date` (`lottery`,`draw_date`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `upcoming_draws`
--

LOCK TABLES `upcoming_draws` WRITE;
/*!40000 ALTER TABLE `upcoming_draws` DISABLE KEYS */;
INSERT INTO `upcoming_draws` VALUES (1,'Monday Lotto','2026-01-26 19:00:00',10.00,'scheduled','2026-01-17 11:55:19'),(2,'Wednesday Lotto','2026-01-21 19:00:00',10.00,'scheduled','2026-01-17 11:55:19'),(3,'Friday Lotto','2026-01-23 19:00:00',10.00,'scheduled','2026-01-17 11:55:19');
/*!40000 ALTER TABLE `upcoming_draws` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'user',
  `email_verified` tinyint(1) DEFAULT '0',
  `phone_verified` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`),
  KEY `idx_email` (`email`),
  KEY `idx_phone` (`phone`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `check_email_or_phone` CHECK (((`email` is not null) or (`phone` is not null)))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'LEBOHANG MONAMANE','monamane.lebohang45@gmail.com','59181664','$2y$12$vQ3ezOpvbGlJmZQlRsY8DuGe/47THucuu3bS38IRH62AEKYppH5p.','user',1,0,1,'2026-01-10 21:03:32'),(2,'Qenehelo Khophoche','qenehelokhophoche@gmail.com','57510582','$2y$12$T/pKLeZnMPqzNB/DG6zmnOtQPYybGo/cQtX1GS/KHswQ.1saF.Cfm','user',1,0,1,'2026-01-10 21:31:09'),(3,'Free Lotto','lebomona78@gmail.com',NULL,'$2y$12$un0rlEjJVbeQyc2T8ob17uwPZAFy1IYTZ25t.XLIJ2p1Qb1p4KiF.','admin',1,0,1,'2026-01-11 07:07:37');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `winners`
--

DROP TABLE IF EXISTS `winners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `winners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `lottery` varchar(50) NOT NULL,
  `prize_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `winners_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `winners`
--

LOCK TABLES `winners` WRITE;
/*!40000 ALTER TABLE `winners` DISABLE KEYS */;
/*!40000 ALTER TABLE `winners` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-20 11:12:52
