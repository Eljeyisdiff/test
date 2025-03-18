-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: localhost    Database: nu_queuest
-- ------------------------------------------------------
-- Server version	8.0.33

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'Jemimah','jemimah@email.com','password123');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `employee_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `office_id` int DEFAULT NULL,
  `window` int DEFAULT NULL,
  `logged_In` tinyint DEFAULT NULL,
  PRIMARY KEY (`employee_id`),
  KEY `office_id1_idx` (`office_id`),
  KEY `user_id1_idx` (`user_id`),
  CONSTRAINT `office_id1` FOREIGN KEY (`office_id`) REFERENCES `offices` (`office_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_id1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,2,1,NULL,1);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `office_purpose`
--

DROP TABLE IF EXISTS `office_purpose`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `office_purpose` (
  `purpose_id` int NOT NULL AUTO_INCREMENT,
  `office_id` int DEFAULT NULL,
  `purpose` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`purpose_id`),
  KEY `office_id_fk_idx` (`office_id`),
  CONSTRAINT `office_id_fk1` FOREIGN KEY (`office_id`) REFERENCES `offices` (`office_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `office_purpose`
--

LOCK TABLES `office_purpose` WRITE;
/*!40000 ALTER TABLE `office_purpose` DISABLE KEYS */;
INSERT INTO `office_purpose` VALUES (1,1,'To submit your admission application'),(2,1,'To ask questions about the admission process'),(3,2,'For processing your tuition and fee payments');
/*!40000 ALTER TABLE `office_purpose` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `office_windows`
--

DROP TABLE IF EXISTS `office_windows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `office_windows` (
  `window_id` int NOT NULL AUTO_INCREMENT,
  `office_id` int DEFAULT NULL,
  `employee_id` int DEFAULT NULL,
  `window_number` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `window_status` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_ticket` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`window_id`),
  KEY `ticketid_idx` (`window_number`),
  KEY `office_id_idx` (`office_id`),
  KEY `ticket_id_idx` (`window_number`),
  KEY `employee_id_fk_idx` (`employee_id`),
  CONSTRAINT `employee_id_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `office_id_fk` FOREIGN KEY (`office_id`) REFERENCES `offices` (`office_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `office_windows`
--

LOCK TABLES `office_windows` WRITE;
/*!40000 ALTER TABLE `office_windows` DISABLE KEYS */;
INSERT INTO `office_windows` VALUES (1,1,1,'2','open','ADM1165');
/*!40000 ALTER TABLE `office_windows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offices`
--

DROP TABLE IF EXISTS `offices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offices` (
  `office_id` int NOT NULL AUTO_INCREMENT,
  `office_name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prefix` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office_description` text COLLATE utf8mb4_unicode_ci,
  `on_break` tinyint DEFAULT NULL,
  `estimated_time` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `windows_num` int DEFAULT NULL,
  `status` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_queue_number` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`office_id`),
  UNIQUE KEY `office_name_UNIQUE` (`office_name`),
  UNIQUE KEY `prefix_UNIQUE` (`prefix`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offices`
--

LOCK TABLES `offices` WRITE;
/*!40000 ALTER TABLE `offices` DISABLE KEYS */;
INSERT INTO `offices` VALUES (1,'Admissions Office','ADM','The Admissions Office is responsible for managing the student enrollment process, from initial inquiries to final acceptance.',NULL,NULL,NULL,'open','1166'),(2,'Accounting Office','ACC','The Accounting Office oversees the financial operations of the university and ensures accurate processing of payments, fees, and financial records.',NULL,NULL,NULL,'closed',NULL);
/*!40000 ALTER TABLE `offices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `temp_queue`
--

DROP TABLE IF EXISTS `temp_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `temp_queue` (
  `queue_id` int NOT NULL AUTO_INCREMENT,
  `office_id` int DEFAULT NULL,
  `ticket_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `queue_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position_number` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`queue_id`),
  KEY `officeid_idx` (`office_id`),
  KEY `tikcetid_idx` (`ticket_id`),
  KEY `userid_idx` (`user_id`),
  CONSTRAINT `officeid` FOREIGN KEY (`office_id`) REFERENCES `offices` (`office_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tikcetid` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `userid` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temp_queue`
--

LOCK TABLES `temp_queue` WRITE;
/*!40000 ALTER TABLE `temp_queue` DISABLE KEYS */;
INSERT INTO `temp_queue` VALUES (56,1,56,6,'ADM1166',1,'2024-11-05 14:16:19','2024-11-05 14:17:03');
/*!40000 ALTER TABLE `temp_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `ticket_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `office_id` int DEFAULT NULL,
  `queue_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_details` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ticket_status` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `attended_at` timestamp NULL DEFAULT NULL,
  `sevice_ended_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ticket_id`),
  KEY `user_id_idx` (`user_id`),
  KEY `office_idfk_1_idx` (`office_id`),
  CONSTRAINT `office_idfk_1` FOREIGN KEY (`office_id`) REFERENCES `offices` (`office_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (1,1,1,'ADM1111','Enrollment for Transferees','Completed','2024-10-31 08:19:56','2024-11-03 16:59:28',NULL,NULL),(2,3,1,'ADM1112','Enrollment for Transferees','waiting','2024-10-31 09:49:50','2024-10-31 09:49:50',NULL,NULL),(3,4,1,'ADM1113','Enrollment for Transferees','Completed','2024-11-03 16:44:42','2024-11-03 16:59:51',NULL,NULL),(4,4,1,'ADM1114','Enrollment for Transferees','Completed','2024-11-03 17:00:02','2024-11-03 17:01:57',NULL,NULL),(5,4,1,'ADM1115','Enrollment for Transferees','Completed','2024-11-03 17:05:58','2024-11-04 11:02:17',NULL,NULL),(6,5,1,'ADM1116','Enrollment for Transferees','Completed','2024-11-03 17:10:40','2024-11-04 11:14:52',NULL,NULL),(7,6,1,'ADM1117','','Completed','2024-11-04 11:16:04','2024-11-04 11:16:12',NULL,NULL),(8,6,1,'ADM1118','Secret','Completed','2024-11-04 11:17:32','2024-11-04 11:17:39',NULL,NULL),(9,6,1,'ADM1119','Enrollment for Transferees','Completed','2024-11-04 11:18:06','2024-11-04 11:19:29',NULL,NULL),(10,6,1,'ADM1120','Enrollment for Transferees','Completed','2024-11-04 11:21:20','2024-11-04 11:21:28',NULL,NULL),(11,6,1,'ADM1121','Enrollment for Transferees','Completed','2024-11-04 11:22:34','2024-11-04 11:22:45',NULL,NULL),(12,6,1,'ADM1122','Enrollment for Transferees','Completed','2024-11-04 11:23:03','2024-11-04 11:23:09',NULL,NULL),(13,6,1,'ADM1123','Enrollment for Transferees','Completed','2024-11-04 11:26:24','2024-11-04 11:26:29',NULL,NULL),(14,6,1,'ADM1124','Enrollment for Transferees','Completed','2024-11-04 11:27:18','2024-11-04 11:27:27',NULL,NULL),(15,6,1,'ADM1125','Enrollment for Transferees','Completed','2024-11-04 11:27:40','2024-11-04 11:27:50',NULL,NULL),(16,6,1,'ADM1126','Enrollment for Transferees','Cancelled','2024-11-04 11:32:18','2024-11-04 11:33:18',NULL,NULL),(17,6,1,'ADM1127','Enrollment for Transferees','Cancelled','2024-11-04 11:33:36','2024-11-04 11:37:37',NULL,NULL),(18,6,1,'ADM1128','Enrollment for Transferees','Cancelled','2024-11-04 11:37:46','2024-11-04 11:41:35',NULL,NULL),(19,6,1,'ADM1129','Enrollment for Transferees','Cancelled','2024-11-04 11:41:38','2024-11-04 11:41:50',NULL,NULL),(20,6,1,'ADM1130','Enrollment for Transferees','Cancelled','2024-11-04 11:51:51','2024-11-04 11:51:59',NULL,NULL),(21,6,1,'ADM1131','Enrollment for Transferees','Cancelled','2024-11-04 11:52:02','2024-11-04 11:53:38',NULL,NULL),(22,6,1,'ADM1132','Enrollment for Transferees','Completed','2024-11-04 13:07:08','2024-11-04 14:00:29',NULL,NULL),(23,2,1,'ADM1133','Enrollment for Transferees','Cancelled','2024-11-04 13:39:38','2024-11-04 14:41:03',NULL,NULL),(24,2,1,'ADM1134','Enrollment for Transferees','Completed','2024-11-04 14:20:00','2024-11-04 14:38:44',NULL,NULL),(25,2,1,'ADM1135','Enrollment for Transferees','Completed','2024-11-04 14:40:03','2024-11-04 14:40:29',NULL,NULL),(26,2,1,'ADM1136','Enrollment for Transferees','Completed','2024-11-04 14:40:55','2024-11-04 14:41:12',NULL,NULL),(27,2,1,'ADM1137','Enrollment for Transferees','Completed','2024-11-04 14:57:39','2024-11-04 14:59:34',NULL,NULL),(28,6,1,'ADM1138','Enrollment for Transferees','Completed','2024-11-04 14:58:09','2024-11-04 14:59:59',NULL,NULL),(29,2,1,'ADM1139','Enrollment for Transferees','Completed','2024-11-04 14:59:52','2024-11-04 15:43:44',NULL,NULL),(30,2,1,'ADM1140','Enrollment for Transferees','Completed','2024-11-04 15:44:19','2024-11-04 15:45:06',NULL,NULL),(31,2,1,'ADM1141','Enrollment for Transferees','Completed','2024-11-04 15:46:11','2024-11-04 15:46:17',NULL,NULL),(32,2,1,'ADM1142','Enrollment for Transferees','Completed','2024-11-04 15:48:39','2024-11-04 15:50:03',NULL,NULL),(33,2,1,'ADM1143','Enrollment for Transferees','Completed','2024-11-04 15:54:38','2024-11-04 16:07:15',NULL,NULL),(34,2,1,'ADM1144','Enrollment for Transferees','Completed','2024-11-04 16:13:54','2024-11-04 16:14:06',NULL,NULL),(35,2,1,'ADM1145','Enrollment for Transferees','Completed','2024-11-04 16:20:22','2024-11-04 16:20:30',NULL,NULL),(36,2,1,'ADM1146','Enrollment for Transferees','Completed','2024-11-04 16:23:34','2024-11-04 16:24:38',NULL,NULL),(37,2,1,'ADM1147','Enrollment for Transferees','Cancelled','2024-11-04 16:23:44','2024-11-04 16:25:07',NULL,NULL),(38,2,1,'ADM1148','Enrollment for Transferees','Cancelled','2024-11-04 16:28:06','2024-11-04 16:28:53',NULL,NULL),(39,2,1,'ADM1149','Enrollment for Transferees','Cancelled','2024-11-04 16:33:04','2024-11-04 16:33:10',NULL,NULL),(40,2,1,'ADM1150','Enrollment for Transferees','Cancelled','2024-11-04 16:47:50','2024-11-04 16:51:25',NULL,NULL),(41,2,1,'ADM1151','Enrollment for Transferees','Completed','2024-11-04 16:51:37','2024-11-04 16:51:51',NULL,NULL),(42,2,1,'ADM1152',NULL,'Cancelled','2024-11-04 17:45:21','2024-11-04 17:45:28',NULL,NULL),(43,2,1,'ADM1153',NULL,'Cancelled','2024-11-04 17:50:41','2024-11-04 17:51:17',NULL,NULL),(44,2,1,'ADM1154',NULL,'Cancelled','2024-11-04 17:57:18','2024-11-04 17:57:25',NULL,NULL),(45,2,1,'ADM1155','','Completed','2024-11-04 17:58:02','2024-11-05 05:53:48',NULL,NULL),(46,4,1,'ADM1156','Enrollment for Transferees','Completed','2024-11-05 05:54:13','2024-11-05 05:54:54',NULL,NULL),(47,4,1,'ADM1157','Enrollment for Transferees','Cancelled','2024-11-05 05:55:06','2024-11-05 05:55:39',NULL,NULL),(48,4,1,'ADM1158','Enrollment for Transferees','Completed','2024-11-05 06:00:39','2024-11-05 08:14:27',NULL,NULL),(49,4,1,'ADM1159','Enrollment for Transferees','Completed','2024-11-05 08:14:42','2024-11-05 08:29:53',NULL,NULL),(50,2,1,'ADM1160','Enrollment for Transferees','Completed','2024-11-05 08:15:14','2024-11-05 08:34:02',NULL,NULL),(51,7,1,'ADM1161','Enrollment for Transferees','Completed','2024-11-05 08:29:39','2024-11-05 13:05:29',NULL,NULL),(52,4,1,'ADM1162',NULL,'waiting','2024-11-05 08:31:02','2024-11-05 08:31:02',NULL,NULL),(53,5,1,'ADM1163','Enrollment for Transferees','Completed','2024-11-05 13:33:16','2024-11-05 13:33:25',NULL,NULL),(54,5,1,'ADM1164',NULL,'waiting','2024-11-05 13:39:30','2024-11-05 13:39:30',NULL,NULL),(55,4,1,'ADM1165','Enrollment for Transferees','waiting','2024-11-05 13:39:55','2024-11-05 13:39:55',NULL,NULL),(56,6,1,'ADM1166','Enrollment for Transferees','waiting','2024-11-05 14:16:19','2024-11-05 14:16:19',NULL,NULL);
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `account_type` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Student','','yabojo@students.nu-laguna.edu.ph',NULL),(2,'Employee','Jemimah','jemimah1@email.com','$2y$10$t8fs3sRRdYxBUnv4WRjRH.KupvHQlSuNcY.4hEHzlyliHwAP5.nB.'),(3,'Student','Jemimah','jemimah@students.nu-laguna.edu.ph',NULL),(4,'Guest','Amy Santiago','amy@email.com',NULL),(5,'Guest','Jemimah','billie@email.com',NULL),(6,'Guest','Jemimah','jemi@email.com',NULL),(7,'Guest','Jemimah Yabo','jemimahyabo1@gmail.com',NULL);
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

-- Dump completed on 2024-11-05 22:19:28
