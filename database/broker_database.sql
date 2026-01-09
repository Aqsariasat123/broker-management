Warning: A partial dump from a server that has GTIDs will by default include the GTIDs of all transactions, even those that changed suppressed parts of the database. If you don't want to restore GTIDs, pass --set-gtid-purged=OFF. To make a complete dump, pass --all-databases --triggers --routines --events. 
Warning: A dump from a server that has GTIDs enabled will by default include the GTIDs of all transactions, even those that were executed during its extraction and might not be represented in the dumped data. This might result in an inconsistent data dump. 
In order to ensure a consistent backup of the database, pass --single-transaction or --lock-all-tables or --source-data. 
-- MySQL dump 10.13  Distrib 9.5.0, for macos14.8 (x86_64)
--
-- Host: localhost    Database: broker
-- ------------------------------------------------------
-- Server version	9.5.0

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
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '50e828de-cc88-11f0-861c-63dd2c41c108:1-1815';

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `audit_logs_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `audit_logs_action_index` (`action`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,NULL,'login_failed',NULL,NULL,'Failed login attempt for: admin',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','http://127.0.0.1:8000/login','POST','2026-01-09 01:59:53','2026-01-09 01:59:53'),(2,NULL,'login_failed',NULL,NULL,'Failed login attempt for: admin',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','http://127.0.0.1:8000/login','POST','2026-01-09 02:01:19','2026-01-09 02:01:19'),(3,NULL,'login_failed',NULL,NULL,'Failed login attempt for: webadmin',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','http://127.0.0.1:8000/login','POST','2026-01-09 02:04:08','2026-01-09 02:04:08'),(4,1,'login',NULL,NULL,'User logged in',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','http://127.0.0.1:8000/login','POST','2026-01-09 02:04:40','2026-01-09 02:04:40'),(5,1,'login',NULL,NULL,'User logged in',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','http://127.0.0.1:8000/login','POST','2026-01-09 02:07:58','2026-01-09 02:07:58'),(6,1,'login',NULL,NULL,'User logged in',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','http://127.0.0.1:8001/login','POST','2026-01-09 06:33:11','2026-01-09 06:33:11');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `beneficial_owners`
--

DROP TABLE IF EXISTS `beneficial_owners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `beneficial_owners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `owner_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `full_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `nin_passport_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shares` decimal(5,2) DEFAULT NULL,
  `pep` tinyint(1) NOT NULL DEFAULT '0',
  `pep_details` text COLLATE utf8mb4_unicode_ci,
  `date_added` date DEFAULT NULL,
  `removed` tinyint(1) NOT NULL DEFAULT '0',
  `relationship` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ownership_percentage` decimal(5,2) DEFAULT NULL,
  `id_document_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poa_document_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `beneficial_owners_owner_code_unique` (`owner_code`),
  KEY `beneficial_owners_client_id_foreign` (`client_id`),
  CONSTRAINT `beneficial_owners_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `beneficial_owners`
--

LOCK TABLES `beneficial_owners` WRITE;
/*!40000 ALTER TABLE `beneficial_owners` DISABLE KEYS */;
/*!40000 ALTER TABLE `beneficial_owners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
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
-- Table structure for table `claims`
--

DROP TABLE IF EXISTS `claims`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `claims` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `claim_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `vehicle_id` bigint unsigned DEFAULT NULL,
  `loss_date` date DEFAULT NULL,
  `claim_date` date DEFAULT NULL,
  `claim_amount` decimal(15,2) DEFAULT NULL,
  `claim_summary` text COLLATE utf8mb4_unicode_ci,
  `claim_stage` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `close_date` date DEFAULT NULL,
  `paid_amount` decimal(15,2) DEFAULT NULL,
  `settlment_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `claims_claim_id_unique` (`claim_id`),
  KEY `claims_policy_id_foreign` (`policy_id`),
  KEY `claims_client_id_foreign` (`client_id`),
  KEY `claims_vehicle_id_foreign` (`vehicle_id`),
  CONSTRAINT `claims_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `claims_policy_id_foreign` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `claims_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `claims`
--

LOCK TABLES `claims` WRITE;
/*!40000 ALTER TABLE `claims` DISABLE KEYS */;
/*!40000 ALTER TABLE `claims` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nin_bcrn` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob_dor` date DEFAULT NULL,
  `mobile_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wa` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `has_vehicle` tinyint(1) NOT NULL DEFAULT '0',
  `has_house` tinyint(1) NOT NULL DEFAULT '0',
  `has_business` tinyint(1) NOT NULL DEFAULT '0',
  `has_boat` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signed_up` date NOT NULL,
  `agency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employer` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `income_source` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monthly_income` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `married` tinyint(1) NOT NULL DEFAULT '0',
  `spouses_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alternate_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` text COLLATE utf8mb4_unicode_ci,
  `island` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `po_box_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pep` tinyint(1) NOT NULL DEFAULT '0',
  `pep_comment` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salutation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `other_names` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `surname` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `passport_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_expiry_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_document_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  `poa_document_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `clients_clid_unique` (`clid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,'Jean Grey','Individual',NULL,NULL,'00000000',NULL,NULL,NULL,'Direct',NULL,0,0,0,0,NULL,'Active','2026-01-09',NULL,NULL,NULL,'CLI000001',NULL,NULL,NULL,0,NULL,NULL,'jean.grey@example.com',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'Jean',NULL,'Grey',NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22',0,0),(2,'Barbara Walton','Individual',NULL,NULL,'00000000',NULL,NULL,NULL,'Direct',NULL,0,0,0,0,NULL,'Active','2026-01-09',NULL,NULL,NULL,'CLI000002',NULL,NULL,NULL,0,NULL,NULL,'barbara.walton@example.com',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'Barbara',NULL,'Walton',NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22',0,0),(3,'Cornerstone (Pty) Ltd','Individual',NULL,NULL,'00000000',NULL,NULL,NULL,'Direct',NULL,0,0,0,0,NULL,'Active','2026-01-09',NULL,NULL,NULL,'CLI000003',NULL,NULL,NULL,0,NULL,NULL,'cornerstone.(pty).ltd@example.com',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'Cornerstone',NULL,'(Pty) Ltd',NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22',0,0),(4,'Anna\'s Spa','Individual',NULL,NULL,'00000000',NULL,NULL,NULL,'Direct',NULL,0,0,0,0,NULL,'Active','2026-01-09',NULL,NULL,NULL,'CLI000004',NULL,NULL,NULL,0,NULL,NULL,'annas.spa@example.com',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'Anna\'s',NULL,'Spa',NULL,NULL,'2026-01-09 02:03:23','2026-01-09 02:03:23',0,0),(5,'Brian Trapper','Individual',NULL,NULL,'00000000',NULL,NULL,NULL,'Direct',NULL,0,0,0,0,NULL,'Active','2026-01-09',NULL,NULL,NULL,'CLI000005',NULL,NULL,NULL,0,NULL,NULL,'brian.trapper@example.com',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'Brian',NULL,'Trapper',NULL,NULL,'2026-01-09 02:03:23','2026-01-09 02:03:23',0,0),(6,'Adbul Juma','Individual',NULL,NULL,'00000000',NULL,NULL,NULL,'Direct',NULL,0,0,0,0,NULL,'Active','2026-01-09',NULL,NULL,NULL,'CLI000006',NULL,NULL,NULL,0,NULL,NULL,'adbul.juma@example.com',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'Adbul',NULL,'Juma',NULL,NULL,'2026-01-09 02:03:23','2026-01-09 02:03:23',0,0),(7,'Beta Center','Individual',NULL,NULL,'00000000',NULL,NULL,NULL,'Direct',NULL,0,0,0,0,NULL,'Active','2026-01-09',NULL,NULL,NULL,'CLI000007',NULL,NULL,NULL,0,NULL,NULL,'beta.center@example.com',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'Beta',NULL,'Center',NULL,NULL,'2026-01-09 02:03:23','2026-01-09 02:03:23',0,0),(8,'Steven Drax','Individual',NULL,NULL,'00000000',NULL,NULL,NULL,'Direct',NULL,0,0,0,0,NULL,'Active','2026-01-09',NULL,NULL,NULL,'CLI000008',NULL,NULL,NULL,0,NULL,NULL,'steven.drax@example.com',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'Steven',NULL,'Drax',NULL,NULL,'2026-01-09 02:03:23','2026-01-09 02:03:23',0,0);
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commission_notes`
--

DROP TABLE IF EXISTS `commission_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commission_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `schedule_id` bigint unsigned NOT NULL,
  `com_note_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `issued_on` date DEFAULT NULL,
  `total_premium` decimal(15,2) DEFAULT NULL,
  `expected_commission` decimal(15,2) DEFAULT NULL,
  `attachment_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commission_notes_com_note_id_unique` (`com_note_id`),
  KEY `commission_notes_schedule_id_foreign` (`schedule_id`),
  CONSTRAINT `commission_notes_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commission_notes`
--

LOCK TABLES `commission_notes` WRITE;
/*!40000 ALTER TABLE `commission_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `commission_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commission_statements`
--

DROP TABLE IF EXISTS `commission_statements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commission_statements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `commission_note_id` bigint unsigned DEFAULT NULL,
  `com_stat_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_start` date DEFAULT NULL,
  `period_end` date DEFAULT NULL,
  `net_commission` decimal(15,2) DEFAULT NULL,
  `tax_withheld` decimal(15,2) DEFAULT NULL,
  `attachment_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commission_statements_com_stat_id_unique` (`com_stat_id`),
  KEY `commission_statements_commission_note_id_foreign` (`commission_note_id`),
  CONSTRAINT `commission_statements_commission_note_id_foreign` FOREIGN KEY (`commission_note_id`) REFERENCES `commission_notes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commission_statements`
--

LOCK TABLES `commission_statements` WRITE;
/*!40000 ALTER TABLE `commission_statements` DISABLE KEYS */;
/*!40000 ALTER TABLE `commission_statements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commissions`
--

DROP TABLE IF EXISTS `commissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `commission_note_id` bigint unsigned NOT NULL,
  `commission_statement_id` bigint unsigned DEFAULT NULL,
  `grouping` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `basic_premium` decimal(15,2) DEFAULT NULL,
  `rate` decimal(8,2) DEFAULT NULL,
  `amount_due` decimal(15,2) DEFAULT NULL,
  `payment_status_id` bigint unsigned DEFAULT NULL,
  `amount_received` decimal(15,2) DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `mode_of_payment_id` bigint unsigned DEFAULT NULL,
  `variance` decimal(15,2) DEFAULT NULL,
  `variance_reason` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_due` date DEFAULT NULL,
  `commission_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commissions_commission_code_unique` (`commission_code`),
  KEY `commissions_commission_note_id_foreign` (`commission_note_id`),
  KEY `commissions_commission_statement_id_foreign` (`commission_statement_id`),
  KEY `commissions_payment_status_id_foreign` (`payment_status_id`),
  KEY `commissions_mode_of_payment_id_foreign` (`mode_of_payment_id`),
  CONSTRAINT `commissions_commission_note_id_foreign` FOREIGN KEY (`commission_note_id`) REFERENCES `commission_notes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commissions_commission_statement_id_foreign` FOREIGN KEY (`commission_statement_id`) REFERENCES `commission_statements` (`id`) ON DELETE SET NULL,
  CONSTRAINT `commissions_mode_of_payment_id_foreign` FOREIGN KEY (`mode_of_payment_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `commissions_payment_status_id_foreign` FOREIGN KEY (`payment_status_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commissions`
--

LOCK TABLES `commissions` WRITE;
/*!40000 ALTER TABLE `commissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `commissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `contact_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wa` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `occupation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employer` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acquired` date DEFAULT NULL,
  `source` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rank` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_contact` date DEFAULT NULL,
  `next_follow_up` date DEFAULT NULL,
  `coid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `salutation` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `savings_budget` decimal(10,2) DEFAULT NULL,
  `married` tinyint(1) NOT NULL DEFAULT '0',
  `children` int NOT NULL DEFAULT '0',
  `children_details` text COLLATE utf8mb4_unicode_ci,
  `vehicle` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `house` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contacts_contact_id_unique` (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `debit_notes`
--

DROP TABLE IF EXISTS `debit_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `debit_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payment_plan_id` bigint unsigned NOT NULL,
  `debit_note_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `issued_on` date DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `document_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `debit_notes_debit_note_no_unique` (`debit_note_no`),
  KEY `debit_notes_payment_plan_id_foreign` (`payment_plan_id`),
  CONSTRAINT `debit_notes_payment_plan_id_foreign` FOREIGN KEY (`payment_plan_id`) REFERENCES `payment_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `debit_notes`
--

LOCK TABLES `debit_notes` WRITE;
/*!40000 ALTER TABLE `debit_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `debit_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `doc_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tied_to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `format` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_added` date DEFAULT NULL,
  `year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `file_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `documents_doc_id_unique` (`doc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `endorsements`
--

DROP TABLE IF EXISTS `endorsements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `endorsements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `policy_id` bigint unsigned NOT NULL,
  `endorsement_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `description` text COLLATE utf8mb4_unicode_ci,
  `endorsement_notes` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `endorsements_endorsement_no_unique` (`endorsement_no`),
  KEY `endorsements_policy_id_foreign` (`policy_id`),
  CONSTRAINT `endorsements_policy_id_foreign` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `endorsements`
--

LOCK TABLES `endorsements` WRITE;
/*!40000 ALTER TABLE `endorsements` DISABLE KEYS */;
/*!40000 ALTER TABLE `endorsements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `expense_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expense_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payee` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_paid` date DEFAULT NULL,
  `amount_paid` decimal(15,2) DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `mode_of_payment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mode_of_payment_id` bigint unsigned DEFAULT NULL,
  `attachment_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expense_notes` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expenses_expense_code_unique` (`expense_code`),
  UNIQUE KEY `expenses_expense_id_unique` (`expense_id`),
  KEY `expenses_category_id_foreign` (`category_id`),
  KEY `expenses_mode_of_payment_id_foreign` (`mode_of_payment_id`),
  CONSTRAINT `expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_mode_of_payment_id_foreign` FOREIGN KEY (`mode_of_payment_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
-- Table structure for table `followups`
--

DROP TABLE IF EXISTS `followups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `followups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `follow_up_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `life_proposal_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `channel` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `summary` text COLLATE utf8mb4_unicode_ci,
  `next_action` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `followups_follow_up_code_unique` (`follow_up_code`),
  KEY `followups_contact_id_foreign` (`contact_id`),
  KEY `followups_client_id_foreign` (`client_id`),
  KEY `followups_life_proposal_id_foreign` (`life_proposal_id`),
  KEY `followups_user_id_foreign` (`user_id`),
  CONSTRAINT `followups_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `followups_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `followups_life_proposal_id_foreign` FOREIGN KEY (`life_proposal_id`) REFERENCES `life_proposals` (`id`) ON DELETE SET NULL,
  CONSTRAINT `followups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `followups`
--

LOCK TABLES `followups` WRITE;
/*!40000 ALTER TABLE `followups` DISABLE KEYS */;
/*!40000 ALTER TABLE `followups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incomes`
--

DROP TABLE IF EXISTS `incomes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `incomes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `income_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commission_statement_id` bigint unsigned DEFAULT NULL,
  `income_source_id` bigint unsigned DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `amount_received` decimal(15,2) DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `mode_of_payment_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `incomes_income_code_unique` (`income_code`),
  KEY `incomes_commission_statement_id_foreign` (`commission_statement_id`),
  KEY `incomes_income_source_id_foreign` (`income_source_id`),
  KEY `incomes_category_id_foreign` (`category_id`),
  KEY `incomes_mode_of_payment_id_foreign` (`mode_of_payment_id`),
  CONSTRAINT `incomes_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `incomes_commission_statement_id_foreign` FOREIGN KEY (`commission_statement_id`) REFERENCES `commission_statements` (`id`) ON DELETE SET NULL,
  CONSTRAINT `incomes_income_source_id_foreign` FOREIGN KEY (`income_source_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `incomes_mode_of_payment_id_foreign` FOREIGN KEY (`mode_of_payment_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incomes`
--

LOCK TABLES `incomes` WRITE;
/*!40000 ALTER TABLE `incomes` DISABLE KEYS */;
/*!40000 ALTER TABLE `incomes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
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
-- Table structure for table `life_proposals`
--

DROP TABLE IF EXISTS `life_proposals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `life_proposals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `proposers_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_id` bigint unsigned NOT NULL,
  `insurer_id` bigint unsigned DEFAULT NULL,
  `policy_plan_id` bigint unsigned DEFAULT NULL,
  `salutation_id` bigint unsigned DEFAULT NULL,
  `sum_assured` decimal(15,2) DEFAULT NULL,
  `term` int DEFAULT NULL,
  `add_ons` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `offer_date` date DEFAULT NULL,
  `premium` decimal(15,2) DEFAULT NULL,
  `frequency_id` bigint unsigned DEFAULT NULL,
  `proposal_stage_id` bigint unsigned DEFAULT NULL,
  `age` int DEFAULT NULL,
  `status_id` bigint unsigned DEFAULT NULL,
  `source_of_payment_id` bigint unsigned DEFAULT NULL,
  `mcr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_id` bigint unsigned DEFAULT NULL,
  `is_submitted` tinyint(1) NOT NULL DEFAULT '0',
  `sex` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anb` int DEFAULT NULL,
  `riders` json DEFAULT NULL,
  `rider_premiums` json DEFAULT NULL,
  `annual_premium` decimal(15,2) DEFAULT NULL,
  `base_premium` decimal(15,2) DEFAULT NULL,
  `admin_fee` decimal(15,2) DEFAULT NULL,
  `total_premium` decimal(15,2) DEFAULT NULL,
  `medical_examination_required` tinyint(1) NOT NULL DEFAULT '0',
  `policy_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loading_premium` decimal(15,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `maturity_date` date DEFAULT NULL,
  `method_of_payment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `life_proposals_prid_unique` (`prid`),
  KEY `life_proposals_contact_id_foreign` (`contact_id`),
  KEY `life_proposals_insurer_id_foreign` (`insurer_id`),
  KEY `life_proposals_policy_plan_id_foreign` (`policy_plan_id`),
  KEY `life_proposals_salutation_id_foreign` (`salutation_id`),
  KEY `life_proposals_frequency_id_foreign` (`frequency_id`),
  KEY `life_proposals_proposal_stage_id_foreign` (`proposal_stage_id`),
  KEY `life_proposals_status_id_foreign` (`status_id`),
  KEY `life_proposals_source_of_payment_id_foreign` (`source_of_payment_id`),
  KEY `life_proposals_class_id_foreign` (`class_id`),
  CONSTRAINT `life_proposals_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `life_proposals_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `life_proposals_frequency_id_foreign` FOREIGN KEY (`frequency_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `life_proposals_insurer_id_foreign` FOREIGN KEY (`insurer_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `life_proposals_policy_plan_id_foreign` FOREIGN KEY (`policy_plan_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `life_proposals_proposal_stage_id_foreign` FOREIGN KEY (`proposal_stage_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `life_proposals_salutation_id_foreign` FOREIGN KEY (`salutation_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `life_proposals_source_of_payment_id_foreign` FOREIGN KEY (`source_of_payment_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `life_proposals_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `life_proposals`
--

LOCK TABLES `life_proposals` WRITE;
/*!40000 ALTER TABLE `life_proposals` DISABLE KEYS */;
/*!40000 ALTER TABLE `life_proposals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lookup_categories`
--

DROP TABLE IF EXISTS `lookup_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lookup_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lookup_categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lookup_categories`
--

LOCK TABLES `lookup_categories` WRITE;
/*!40000 ALTER TABLE `lookup_categories` DISABLE KEYS */;
INSERT INTO `lookup_categories` VALUES (1,'Contact Type',1,'2026-01-09 02:03:20','2026-01-09 02:03:20'),(2,'Claim Stage',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(3,'Vehicle Make',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(4,'Client Type',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(5,'Insurer',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(6,'Frequency',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(7,'Payment Plan',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(8,'Contact Stage',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(9,'Source',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(10,'Contact Status',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(11,'Policy Status',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(12,'APL Agency',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(13,'Channel',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(14,'Payment Status',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(15,'Agent',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(16,'Ranking',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(17,'Rank',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(18,'Client Status',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(19,'Issuing Country',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(20,'Source Of Payment',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(21,'ID Type',1,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(22,'Class',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(23,'Island',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(24,'Mode Of Payment (Life)',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(25,'Claim Status',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(26,'Salutation',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(27,'Mode Of Payment (General)',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(28,'Useage',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(29,'Expense Category',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(30,'Vehicle Type',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(31,'Income Category',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(32,'Business Type',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(33,'Income Source',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(34,'Proposal Stage',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(35,'Proposal Status',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(36,'PaymentType',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(37,'Term',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(38,'Engine Type',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(39,'ENDORSEMENT',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(40,'District',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(41,'Occupation',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(42,'Term Units',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(43,'Document Type',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(44,'Task Category',1,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(45,'Insurers',1,NULL,NULL),(46,'Policy Classes',1,NULL,NULL),(47,'Policy Plans',1,NULL,NULL),(48,'Policy Statuses',1,NULL,NULL),(49,'Business Types',1,NULL,NULL);
/*!40000 ALTER TABLE `lookup_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lookup_values`
--

DROP TABLE IF EXISTS `lookup_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lookup_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lookup_category_id` bigint unsigned NOT NULL,
  `seq` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lookup_values_lookup_category_id_seq_unique` (`lookup_category_id`,`seq`),
  CONSTRAINT `lookup_values_lookup_category_id_foreign` FOREIGN KEY (`lookup_category_id`) REFERENCES `lookup_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=290 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lookup_values`
--

LOCK TABLES `lookup_values` WRITE;
/*!40000 ALTER TABLE `lookup_values` DISABLE KEYS */;
INSERT INTO `lookup_values` VALUES (1,1,1,'Lead',1,NULL,NULL,NULL,'2026-01-09 02:03:20','2026-01-09 02:03:20'),(2,1,2,'Prospect',1,NULL,NULL,NULL,'2026-01-09 02:03:20','2026-01-09 02:03:20'),(3,1,3,'Contact',1,NULL,NULL,NULL,'2026-01-09 02:03:20','2026-01-09 02:03:20'),(4,1,4,'SO Bank Officer',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(5,1,5,'Payroll Officer',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(6,2,1,'Awaiting Documents',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(7,2,2,'Awaiting QS Report',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(8,3,1,'Hyundai',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(9,3,2,'Kia',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(10,3,3,'Suzuki',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(11,3,4,'Toyota',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(12,3,5,'Ford',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(13,3,6,'MG',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(14,3,7,'Nissan',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(15,3,8,'Mazda',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(16,3,9,'BMW',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(17,3,10,'Mercedes',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(18,3,11,'Lexus',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(19,3,12,'Haval',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(20,3,13,'Honda',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(21,3,14,'Tata',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(22,3,15,'Isuzu',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(23,4,1,'Individual',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(24,4,2,'Business',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(25,4,3,'Company',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(26,4,4,'Organization',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(27,5,1,'SACOS',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(28,5,2,'HSavy',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(29,5,3,'Alliance',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(30,5,4,'MUA',0,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(31,6,1,'Year',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(32,6,2,'Days',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(33,6,3,'Weeks',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(34,7,1,'Single',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(35,7,2,'Instalments',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(36,7,3,'Regular (Life)',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(37,8,1,'Open',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(38,8,2,'Qualified',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(39,8,3,'KIV',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(40,8,4,'Closed',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(41,9,1,'Direct',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(42,9,2,'Online',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(43,9,3,'Bank ABSA',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(44,9,4,'MCB',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(45,9,5,'NOU',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(46,9,6,'BAR',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(47,9,7,'BOC',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(48,9,8,'SCB',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(49,9,9,'SCU',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(50,9,10,'AIRTEL',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(51,9,11,'Cable & Wireless',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(52,9,12,'Intelvision',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(53,9,13,'PUC',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(54,9,14,'SFA',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(55,9,15,'STC',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(56,9,16,'FSA',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(57,9,17,'Mins Of Education',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(58,9,18,'Mins Of Health',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(59,9,19,'SFRSA',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(60,9,20,'Seychelles Police',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(61,9,21,'Treasury',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(62,9,22,'Judiciary',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(63,9,23,'Pilgrims',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(64,9,24,'SPTC',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(65,10,1,'Not Contacted',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(66,10,2,'Qualified',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(67,10,3,'Converted to Client',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(68,10,4,'Keep In View',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(69,10,5,'Archived',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(70,11,1,'In Force',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(71,11,2,'Expired',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(72,11,3,'Cancelled',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(73,11,4,'Lapsed',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(74,11,5,'Matured',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(75,11,6,'Surrenders',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(76,11,7,'Payout D',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(77,11,8,'Payout TPD',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(78,11,9,'Null & Void',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(79,12,1,'Keystone',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(80,12,2,'LIS',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(81,13,1,'Direct',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(82,13,2,'Online',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(83,13,3,'Agent',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(84,13,4,'Broker',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(85,13,5,'Referral',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(86,14,1,'Paid',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(87,14,2,'Partly Paid',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(88,14,3,'Unpaid',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(89,15,1,'Mandy',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(90,15,2,'Simon',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(91,16,1,'VIP',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(92,16,2,'High',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(93,16,3,'Medium',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(94,16,4,'Low',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(95,17,1,'VIP',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(96,17,2,'High',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(97,17,3,'Medium',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(98,17,4,'Low',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(99,17,5,'Warm',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(100,18,1,'Active',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(101,18,2,'Inactive',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(102,18,3,'Suspended',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(103,18,4,'Pending',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(104,18,5,'Dormant',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(105,19,1,'Seychelles',1,NULL,NULL,'SEY','2026-01-09 02:03:21','2026-01-09 02:03:21'),(106,19,2,'Great Britain',1,NULL,NULL,'GBR','2026-01-09 02:03:21','2026-01-09 02:03:21'),(107,19,3,'Botswana',1,NULL,NULL,'BOT','2026-01-09 02:03:21','2026-01-09 02:03:21'),(108,19,4,'Sri Lanka',1,NULL,NULL,'SRI','2026-01-09 02:03:21','2026-01-09 02:03:21'),(109,19,5,'India',1,NULL,NULL,'IND','2026-01-09 02:03:21','2026-01-09 02:03:21'),(110,19,6,'Nepal',1,NULL,NULL,'NEP','2026-01-09 02:03:21','2026-01-09 02:03:21'),(111,19,7,'Bangladesh',1,NULL,NULL,'BAN','2026-01-09 02:03:21','2026-01-09 02:03:21'),(112,19,8,'Russia',1,NULL,NULL,'RUS','2026-01-09 02:03:21','2026-01-09 02:03:21'),(113,19,9,'Ukraine',1,NULL,NULL,'UKR','2026-01-09 02:03:21','2026-01-09 02:03:21'),(114,19,10,'Kenya',1,NULL,NULL,'KEN','2026-01-09 02:03:21','2026-01-09 02:03:21'),(115,20,1,'Commission',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(116,20,2,'Bonus',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(117,20,3,'Prize',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(118,20,4,'Other',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(119,21,1,'ID Card',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(120,21,2,'Driving License',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(121,21,3,'Passport',1,NULL,NULL,NULL,'2026-01-09 02:03:21','2026-01-09 02:03:21'),(122,22,1,'Motor',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(123,22,2,'General',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(124,22,3,'Life',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(125,22,4,'Bonds',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(126,22,5,'Travel',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(127,22,6,'Marine',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(128,22,7,'Health',0,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(129,23,1,'Mahe',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(130,23,2,'Praslin',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(131,23,3,'La Digue',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(132,23,4,'Perseverance',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(133,23,5,'Cerf',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(134,23,6,'Eden',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(135,23,7,'Silhouette',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(136,24,1,'Transfer',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(137,24,2,'Cheque',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(138,24,3,'Cash',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(139,24,4,'Online',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(140,24,5,'Standing Order',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(141,24,6,'Salary Deduction',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(142,24,7,'Direect',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(143,25,1,'Processing',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(144,25,2,'Settled',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(145,25,3,'Declined',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(146,26,1,'Mr',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(147,26,2,'Ms',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(148,26,3,'Mrs',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(149,26,4,'Miss',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(150,26,5,'Dr',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(151,26,6,'Mr & Mrs',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(152,27,1,'Cash',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(153,27,2,'Card',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(154,27,3,'Transfer',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(155,27,4,'Cheque',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(156,28,1,'Private',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(157,28,2,'Commercial',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(158,28,3,'For Hire',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(159,28,4,'Carriage Of Goods',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(160,28,5,'Commuter',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(161,29,1,'License',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(162,29,2,'Insurance',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(163,29,3,'Office supplies',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(164,29,4,'Telephone & Internet',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(165,29,5,'Marketting',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(166,29,6,'Travel',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(167,29,7,'Referals',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(168,29,8,'Rentals',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(169,29,9,'Vehicle',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(170,29,10,'Fuel',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(171,29,11,'Bank Fees',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(172,29,12,'Charges',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(173,29,13,'Misc',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(174,29,14,'Asset Purchase',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(175,30,1,'SUV',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(176,30,2,'Hatchback',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(177,30,3,'Sedan',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(178,30,4,'Twin Cab',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(179,30,5,'Pick Up',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(180,30,6,'Scooter',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(181,30,7,'Motor Cycle',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(182,30,8,'Taxi',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(183,30,9,'Van',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(184,31,1,'General',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(185,31,2,'Commission',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(186,31,3,'Bonus',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(187,31,4,'Salary',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(188,31,5,'Investment',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(189,31,6,'Rentals',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(190,31,7,'Other',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(191,32,1,'Direct',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(192,32,2,'Transfer',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(193,32,3,'Renewal',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(194,33,1,'Employment',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(195,33,2,'Self Employed',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(196,33,3,'Business',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(197,33,4,'Investment',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(198,33,5,'Rentals',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(199,33,6,'Retirement',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(200,33,7,'Allowance',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(201,33,8,'Other',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(202,34,1,'Not Contacted',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(203,34,2,'RNR',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(204,34,3,'In Discussion',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(205,34,4,'Offer Made',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(206,34,5,'Proposal Filled',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(207,35,1,'Awaiting Medical',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(208,35,2,'Awaiting Policy',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(209,35,3,'Approved',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(210,35,4,'Declined',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(211,35,5,'Withdrawn',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(212,36,1,'Full',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(213,36,2,'Instalment',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(214,36,3,'Adjustment',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(215,37,1,'Annual',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(216,37,2,'Single',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(217,37,3,'Monthly',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(218,37,4,'Quarterly',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(219,37,5,'Bi-Annual',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(220,38,1,'Hybrid',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(221,38,2,'Petrol',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(222,38,3,'Diesel',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(223,38,4,'Electric',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(224,39,1,'Renewal',1,'Policy Renewed',NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(225,39,2,'Cancelation',1,'Policy Cancelled',NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(226,39,3,'Amendment',1,'Sum Insured Reduced',NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(227,39,4,'Amendment',1,'Sum Insured Increased',NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(228,39,5,'Amendment',1,'Plan Cover Changed',NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(229,39,6,'Amendment',1,'Beneficary change',NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(230,39,7,'Amendment',1,'Pay Plan Changed',NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(231,39,8,'Amendment',1,'Vehicle changed',NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(232,40,1,'Victoria',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(233,40,2,'Beau Vallon',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(234,40,3,'Mont Fleuri',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(235,40,4,'Cascade',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(236,40,5,'Providence',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(237,40,6,'Grand Anse',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(238,40,7,'Anse Aux Pins',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(239,41,1,'Accountant',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(240,41,2,'Driver',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(241,41,3,'Customer Service Officer',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(242,41,4,'Real Estate Agent',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(243,41,5,'Rock Breaker',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(244,41,6,'Payroll Officer',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(245,41,7,'Boat Charter',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(246,41,8,'Contractor',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(247,41,9,'Technician',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(248,41,10,'Paymaster',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(249,41,11,'Human Resources Manager',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(250,42,1,'Year',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(251,42,2,'Month',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(252,42,3,'Days',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(253,43,1,'Policy Document',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(254,43,2,'Certificate',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(255,43,3,'Claim Document',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(256,43,4,'Other Document',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(257,44,1,'Payment',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(258,44,2,'Report',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(259,44,3,'Follow-up',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(260,44,4,'Meeting',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(261,44,5,'Call',1,NULL,NULL,NULL,'2026-01-09 02:03:22','2026-01-09 02:03:22'),(262,45,1,'SACOS',1,NULL,NULL,NULL,NULL,NULL),(263,45,2,'Alliance',1,NULL,NULL,NULL,NULL,NULL),(264,45,3,'Hsavy',1,NULL,NULL,NULL,NULL,NULL),(265,45,4,'AON',1,NULL,NULL,NULL,NULL,NULL),(266,45,5,'Marsh',1,NULL,NULL,NULL,NULL,NULL),(267,46,1,'Motor',1,NULL,NULL,NULL,NULL,NULL),(268,46,2,'General',1,NULL,NULL,NULL,NULL,NULL),(269,46,3,'Travel',1,NULL,NULL,NULL,NULL,NULL),(270,46,4,'Marine',1,NULL,NULL,NULL,NULL,NULL),(271,46,5,'Health',1,NULL,NULL,NULL,NULL,NULL),(272,46,6,'Life',1,NULL,NULL,NULL,NULL,NULL),(273,47,1,'Comprehensive',1,NULL,NULL,NULL,NULL,NULL),(274,47,2,'Third Party',1,NULL,NULL,NULL,NULL,NULL),(275,47,3,'Householder\'s',1,NULL,NULL,NULL,NULL,NULL),(276,47,4,'Public Liability',1,NULL,NULL,NULL,NULL,NULL),(277,47,5,'Employer\'s Liability',1,NULL,NULL,NULL,NULL,NULL),(278,47,6,'Fire & Special Perils',1,NULL,NULL,NULL,NULL,NULL),(279,47,7,'House Insurance',1,NULL,NULL,NULL,NULL,NULL),(280,47,8,'Fire Industrial',1,NULL,NULL,NULL,NULL,NULL),(281,47,9,'World Wide Basic',1,NULL,NULL,NULL,NULL,NULL),(282,47,10,'Marine Hull',1,NULL,NULL,NULL,NULL,NULL),(283,48,1,'In Force',1,NULL,NULL,NULL,NULL,NULL),(284,48,2,'DFR',1,NULL,NULL,NULL,NULL,NULL),(285,48,3,'Expired',1,NULL,NULL,NULL,NULL,NULL),(286,48,4,'Cancelled',1,NULL,NULL,NULL,NULL,NULL),(287,49,1,'Direct',1,NULL,NULL,NULL,NULL,NULL),(288,49,2,'Transfer',1,NULL,NULL,NULL,NULL,NULL),(289,49,3,'Renewal',1,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `lookup_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medicals`
--

DROP TABLE IF EXISTS `medicals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `medicals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `life_proposal_id` bigint unsigned NOT NULL,
  `medical_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `medical_type_id` bigint unsigned DEFAULT NULL,
  `clinic` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordered_on` date DEFAULT NULL,
  `completed_on` date DEFAULT NULL,
  `status_id` bigint unsigned DEFAULT NULL,
  `results_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `medicals_medical_code_unique` (`medical_code`),
  KEY `medicals_medical_type_id_foreign` (`medical_type_id`),
  KEY `medicals_status_id_foreign` (`status_id`),
  KEY `medicals_life_proposal_id_status_id_index` (`life_proposal_id`,`status_id`),
  CONSTRAINT `medicals_life_proposal_id_foreign` FOREIGN KEY (`life_proposal_id`) REFERENCES `life_proposals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `medicals_medical_type_id_foreign` FOREIGN KEY (`medical_type_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `medicals_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medicals`
--

LOCK TABLES `medicals` WRITE;
/*!40000 ALTER TABLE `medicals` DISABLE KEYS */;
/*!40000 ALTER TABLE `medicals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_10_05_171758_create_tasks_table',1),(5,'2025_10_11_084446_create_lookup_tables',1),(6,'2025_10_14_153256_create_clients_table',1),(7,'2025_10_15_101405_create_policies_table',1),(8,'2025_10_19_112711_create_contacts_table',1),(9,'2025_10_19_184103_create_life_proposals_table',1),(10,'2025_10_19_200529_create_expenses_table',1),(11,'2025_11_09_125623_create_documents_table',1),(12,'2025_11_09_141513_create_vehicles_table',1),(13,'2025_11_09_145357_create_claims_table',1),(14,'2025_11_09_152400_create_schedules_table',1),(15,'2025_11_09_152500_create_commission_notes_table',1),(16,'2025_11_09_152600_create_commission_statements_table',1),(17,'2025_11_09_152705_create_incomes_table',1),(18,'2025_11_09_163024_create_commissions_table',1),(19,'2025_11_09_180836_create_statements_table',1),(20,'2025_11_18_112128_create_beneficial_owners_table',1),(21,'2025_11_18_112148_create_nominees_table',1),(22,'2025_11_18_112215_create_renewal_notices_table',1),(23,'2025_11_18_112248_create_payment_plans_table',1),(24,'2025_11_18_112305_create_debit_notes_table',1),(25,'2025_11_18_112314_create_payments_table',1),(26,'2025_11_18_112323_create_endorsements_table',1),(27,'2025_11_18_112341_create_followups_table',1),(28,'2025_11_18_112352_create_medicals_table',1),(29,'2025_11_18_112435_create_tax_returns_table',1),(30,'2025_11_19_100000_add_roles_to_users_table',1),(31,'2025_11_19_100100_create_permissions_table',1),(32,'2025_11_19_100200_create_audit_logs_table',1),(33,'2025_11_19_100300_create_roles_table',1),(34,'2025_11_27_101333_update_role_permissions_table_to_use_role_id',1),(35,'2025_11_27_110318_add_encryption_flags_to_tables',1),(36,'2025_11_27_111755_add_client_id_to_policies_table',1),(37,'2025_12_02_090031_add_additional_fields_to_clients_table',1),(38,'2025_12_09_093600_add_policy_status_id_to_policies_table',1),(39,'2025_12_10_120000_remove_redundant_fields_from_policies_table',1),(40,'2025_12_11_095000_add_nin_passport_no_to_nominees_table',1),(41,'2025_12_11_100000_add_date_removed_to_nominees_table',1),(42,'2025_12_11_101000_make_policy_id_nullable_in_nominees_table',1),(43,'2025_12_13_012248_add_item_to_tasks_table',1),(44,'2025_12_13_015306_add_wa_to_contacts_table',1),(45,'2025_12_15_063841_update_claims_table_add_policy_id_foreign_key',1),(46,'2025_12_15_064602_add_claim_stage_to_claims_table',1),(47,'2025_12_15_065910_remove_client_name_from_claims_table',1),(48,'2025_12_15_071004_add_receipt_no_to_expenses_table',1),(49,'2025_12_15_072345_remove_receipt_path_from_expenses_table',1),(50,'2025_12_15_072351_remove_document_path_from_incomes_table',1),(51,'2025_12_15_073025_add_category_id_to_incomes_table',1),(52,'2025_12_15_105446_add_missing_columns_to_expenses_table',1),(53,'2025_12_20_055847_add_columns_to_beneficial_owners_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nominees`
--

DROP TABLE IF EXISTS `nominees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nominees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nominee_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `full_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `relationship` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `share_percentage` decimal(5,2) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `date_removed` date DEFAULT NULL,
  `nin_passport_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_document_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nominees_nominee_code_unique` (`nominee_code`),
  KEY `nominees_policy_id_foreign` (`policy_id`),
  KEY `nominees_client_id_foreign` (`client_id`),
  CONSTRAINT `nominees_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `nominees_policy_id_foreign` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nominees`
--

LOCK TABLES `nominees` WRITE;
/*!40000 ALTER TABLE `nominees` DISABLE KEYS */;
/*!40000 ALTER TABLE `nominees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `payment_plans`
--

DROP TABLE IF EXISTS `payment_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `schedule_id` bigint unsigned NOT NULL,
  `installment_label` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `frequency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_plans_schedule_id_foreign` (`schedule_id`),
  CONSTRAINT `payment_plans_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_plans`
--

LOCK TABLES `payment_plans` WRITE;
/*!40000 ALTER TABLE `payment_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `debit_note_id` bigint unsigned NOT NULL,
  `payment_reference` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_on` date DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `mode_of_payment_id` bigint unsigned DEFAULT NULL,
  `receipt_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_payment_reference_unique` (`payment_reference`),
  KEY `payments_debit_note_id_foreign` (`debit_note_id`),
  KEY `payments_mode_of_payment_id_foreign` (`mode_of_payment_id`),
  CONSTRAINT `payments_debit_note_id_foreign` FOREIGN KEY (`debit_note_id`) REFERENCES `debit_notes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_mode_of_payment_id_foreign` FOREIGN KEY (`mode_of_payment_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`),
  UNIQUE KEY `permissions_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `policies`
--

DROP TABLE IF EXISTS `policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `policies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL,
  `policy_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `insurer_id` bigint unsigned DEFAULT NULL,
  `policy_class_id` bigint unsigned DEFAULT NULL,
  `policy_plan_id` bigint unsigned DEFAULT NULL,
  `sum_insured` decimal(15,2) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `insured` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insured_item` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `policy_status_id` bigint unsigned DEFAULT NULL,
  `date_registered` date NOT NULL,
  `renewable` tinyint(1) NOT NULL DEFAULT '1',
  `business_type_id` bigint unsigned DEFAULT NULL,
  `term` int DEFAULT NULL,
  `term_unit` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `base_premium` decimal(15,2) DEFAULT NULL,
  `premium` decimal(15,2) DEFAULT NULL,
  `frequency_id` bigint unsigned DEFAULT NULL,
  `pay_plan_lookup_id` bigint unsigned DEFAULT NULL,
  `agency_id` bigint unsigned DEFAULT NULL,
  `agent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `channel_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `wsc` decimal(15,2) DEFAULT NULL,
  `lou` decimal(15,2) DEFAULT NULL,
  `pa` decimal(15,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `policies_policy_no_unique` (`policy_no`),
  UNIQUE KEY `policies_policy_code_unique` (`policy_code`),
  KEY `policies_client_id_foreign` (`client_id`),
  KEY `policies_insurer_id_foreign` (`insurer_id`),
  KEY `policies_policy_class_id_foreign` (`policy_class_id`),
  KEY `policies_policy_plan_id_foreign` (`policy_plan_id`),
  KEY `policies_policy_status_id_foreign` (`policy_status_id`),
  KEY `policies_business_type_id_foreign` (`business_type_id`),
  KEY `policies_frequency_id_foreign` (`frequency_id`),
  KEY `policies_pay_plan_lookup_id_foreign` (`pay_plan_lookup_id`),
  KEY `policies_agency_id_foreign` (`agency_id`),
  KEY `policies_channel_id_foreign` (`channel_id`),
  CONSTRAINT `policies_agency_id_foreign` FOREIGN KEY (`agency_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `policies_business_type_id_foreign` FOREIGN KEY (`business_type_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `policies_channel_id_foreign` FOREIGN KEY (`channel_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `policies_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `policies_frequency_id_foreign` FOREIGN KEY (`frequency_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `policies_insurer_id_foreign` FOREIGN KEY (`insurer_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `policies_pay_plan_lookup_id_foreign` FOREIGN KEY (`pay_plan_lookup_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `policies_policy_class_id_foreign` FOREIGN KEY (`policy_class_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `policies_policy_plan_id_foreign` FOREIGN KEY (`policy_plan_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `policies_policy_status_id_foreign` FOREIGN KEY (`policy_status_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `policies`
--

LOCK TABLES `policies` WRITE;
/*!40000 ALTER TABLE `policies` DISABLE KEYS */;
INSERT INTO `policies` VALUES (1,1,'MPV-23-HEA-P0002132','MPV-23-HEA-P0002132',NULL,NULL,NULL,390000.00,'2023-10-16','2024-10-15','S44444','Suzuki Fronx',NULL,'2024-10-16',1,NULL,1,'Year',9875.77,11455.89,NULL,NULL,NULL,NULL,NULL,'New vehicle policy','2026-01-09 02:03:22','2026-01-09 02:03:22',NULL,NULL,NULL),(2,2,'FSP-21-P00012999','FSP-21-P00012999',NULL,NULL,NULL,NULL,'2020-04-18','2025-04-17',NULL,'Residence at Anse Royal',NULL,'2020-04-18',1,NULL,1,'Year',7650.00,35467.00,NULL,NULL,NULL,NULL,NULL,'Home insurance policy','2026-01-09 02:03:22','2026-01-09 02:03:22',NULL,NULL,NULL),(3,3,'PL-22-ALP-000033','PL-22-ALP-000033',NULL,NULL,NULL,NULL,'2022-11-30','2023-11-29',NULL,NULL,NULL,'2022-11-30',1,NULL,1,'Year',5000.00,5800.00,NULL,NULL,NULL,NULL,NULL,'Business liability insurance','2026-01-09 02:03:22','2026-01-09 02:03:22',NULL,NULL,NULL),(4,3,'HS1-23-P00023132','HS1-23-P00023132',NULL,NULL,NULL,NULL,'2022-11-12','2023-11-11',NULL,NULL,NULL,'2022-11-12',1,NULL,1,'Year',2500.00,2900.00,NULL,NULL,NULL,NULL,NULL,'Employee coverage','2026-01-09 02:03:22','2026-01-09 02:03:22',NULL,NULL,NULL),(5,4,'FSP-19-P00024','FSP-19-P00024',NULL,NULL,NULL,NULL,'2023-10-06','2024-10-05',NULL,'SPA at English River',NULL,'2022-10-05',1,NULL,1,'Year',3750.00,4350.00,NULL,NULL,NULL,NULL,NULL,'Spa business insurance','2026-01-09 02:03:23','2026-01-09 02:03:23',NULL,NULL,NULL),(6,5,'MVC-18-000331','MVC-18-000331',NULL,NULL,NULL,285000.00,'2022-11-15','2023-11-14','S260','Toyota Hyrider',NULL,'2022-11-15',1,NULL,1,'Year',6652.00,7716.32,NULL,NULL,NULL,NULL,NULL,'SUV insurance','2026-01-09 02:03:23','2026-01-09 02:03:23',NULL,NULL,NULL),(7,6,'MTC-22-000012','MTC-22-000012',NULL,NULL,NULL,0.00,'2022-09-11','2023-09-10','S32453','Hyundai Creta',NULL,'2022-09-11',1,NULL,1,'Year',1500.00,1827.00,NULL,NULL,NULL,NULL,NULL,'Third party only','2026-01-09 02:03:23','2026-01-09 02:03:23',NULL,NULL,NULL),(8,7,'MVT-21-000324','MVT-21-000324',NULL,NULL,NULL,NULL,'2022-12-03','2023-12-02',NULL,'Shop Office Complex Providence',NULL,'2022-12-03',1,NULL,1,'Year',14377.00,16677.32,NULL,NULL,NULL,NULL,NULL,'Commercial property','2026-01-09 02:03:23','2026-01-09 02:03:23',NULL,NULL,NULL);
/*!40000 ALTER TABLE `policies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `renewal_notices`
--

DROP TABLE IF EXISTS `renewal_notices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `renewal_notices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `policy_id` bigint unsigned NOT NULL,
  `rnid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notice_date` date DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `delivery_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `renewal_notices_rnid_unique` (`rnid`),
  KEY `renewal_notices_policy_id_foreign` (`policy_id`),
  CONSTRAINT `renewal_notices_policy_id_foreign` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `renewal_notices`
--

LOCK TABLES `renewal_notices` WRITE;
/*!40000 ALTER TABLE `renewal_notices` DISABLE KEYS */;
/*!40000 ALTER TABLE `renewal_notices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permission_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permissions_role_id_permission_id_unique` (`role_id`,`permission_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`),
  UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','admin','Full system access',1,'2026-01-09 01:58:52','2026-01-09 01:58:52'),(2,'Support','support','Support staff access',0,'2026-01-09 01:58:52','2026-01-09 01:58:52');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedules`
--

DROP TABLE IF EXISTS `schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `policy_id` bigint unsigned NOT NULL,
  `schedule_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `issued_on` date DEFAULT NULL,
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `debit_note_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `policy_schedule_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `renewal_notice_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_agreement_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `schedules_schedule_no_unique` (`schedule_no`),
  KEY `schedules_policy_id_foreign` (`policy_id`),
  CONSTRAINT `schedules_policy_id_foreign` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedules`
--

LOCK TABLES `schedules` WRITE;
/*!40000 ALTER TABLE `schedules` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
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
INSERT INTO `sessions` VALUES ('61xBAeKnAuimVZGLxrBKxgj9oNmU7Ax1TKJm9ePo',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoieTNFWG92aG1ndXlxOHZLd3ZvOVJVZ004U3RHS2lXMjl6bzJDODJzdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1767970779),('AgaeLPnBNafgVyNoER0v6yHcoWx9iKiN3zkglP6U',NULL,'127.0.0.1','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRmR1N2pDQnZ6U2ppbjY2ZHdHUkFIaTVqVm1OOE0xUDF1RkROWXk3VyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1767955744),('eXO9PMnE1MPwNntmPxIxom6P7DxJYYW6AXeEMFGK',NULL,'127.0.0.1','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTlhFRnJWUnRQdUQzMjJrQ3NYV2hiTktrSXNPR1ZpYzVOTUZmVVdoYyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1767968357),('IWXszvSQ3cGsmkqAExX8tbVaka92WmER3ZaHSxev',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOGdXNXNqU0ZJOEhMd2JmZFdRWDJhcWpHcFBxRDh5OUF5bFZ2VWFnMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1767955767),('uTpN7LmroxC8QycJGAFwd6yzmgXOtZ59XN9jSQqo',NULL,'127.0.0.1','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiM3JmQU5GM1RVSmVEVDVmZzN1T0lLN0VxNGNIc3pzWWJ4a2I3WUdXaiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1767968345),('w4Ih3f0G7RS1l4THLZoHREzCCHAZCVc85E8FMN5I',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiRlFPaUhHdGFtQ0NWZTllT1JVcVpJa0Nkd1FxbjFRaFhLMUVyR2Q2MSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQ4OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZGFzaGJvYXJkP2RhdGVfcmFuZ2U9bW9udGgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1767953990),('zmMfUa8Au58E3hAyA1G1CcrYAEvewId9SmTQ0W9P',NULL,'127.0.0.1','curl/8.7.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVmxCOEs0WGRRNWtrR2RQZTJIbHppdkV5djJsT0pCeElMVE1Rb0ZUUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1767952747);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statements`
--

DROP TABLE IF EXISTS `statements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `statements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `statement_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurer_id` bigint unsigned DEFAULT NULL,
  `business_category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `amount_received` decimal(15,2) DEFAULT NULL,
  `mode_of_payment_id` bigint unsigned DEFAULT NULL,
  `remarks` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `statements_statement_no_unique` (`statement_no`),
  KEY `statements_insurer_id_foreign` (`insurer_id`),
  KEY `statements_mode_of_payment_id_foreign` (`mode_of_payment_id`),
  CONSTRAINT `statements_insurer_id_foreign` FOREIGN KEY (`insurer_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  CONSTRAINT `statements_mode_of_payment_id_foreign` FOREIGN KEY (`mode_of_payment_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statements`
--

LOCK TABLES `statements` WRITE;
/*!40000 ALTER TABLE `statements` DISABLE KEYS */;
/*!40000 ALTER TABLE `statements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `due_date` date NOT NULL,
  `due_time` time DEFAULT NULL,
  `date_in` date DEFAULT NULL,
  `assignee` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `task_status` enum('Not Done','In Progress','Completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Not Done',
  `date_done` date DEFAULT NULL,
  `repeat` tinyint(1) NOT NULL DEFAULT '0',
  `frequency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rpt_date` date DEFAULT NULL,
  `rpt_stop_date` date DEFAULT NULL,
  `task_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tasks_task_id_unique` (`task_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
INSERT INTO `tasks` VALUES (1,'TK24043','Payment',NULL,'P.O. Box Rental','Seychelles Postal Services','2765937','2025-10-18',NULL,NULL,'Mandy','Not Done',NULL,1,'Annually','2025-01-01','2027-12-31',NULL,'2026-01-09 02:03:23','2026-01-09 02:03:23'),(2,'TK24044','Report',NULL,'Beneficial Owner Report','FIU','4282828','2025-10-17',NULL,NULL,'Mandy','Not Done',NULL,1,'Bi-Annually','2025-01-15','2026-12-31',NULL,'2026-01-09 02:03:23','2026-01-09 02:03:23'),(3,'TK24045','257','1','1','6','00000000','2026-01-10','22:34:00',NULL,'1','In Progress','2026-01-10',0,'31','2026-01-06','2026-01-08',NULL,'2026-01-09 06:33:50','2026-01-09 06:33:50');
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_returns`
--

DROP TABLE IF EXISTS `tax_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tax_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `commission_statement_id` bigint unsigned NOT NULL,
  `tax_ref_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filing_period` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filed_on` date DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `amount_due` decimal(15,2) DEFAULT NULL,
  `amount_paid` decimal(15,2) DEFAULT NULL,
  `supporting_doc_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tax_returns_tax_ref_id_unique` (`tax_ref_id`),
  KEY `tax_returns_commission_statement_id_foreign` (`commission_statement_id`),
  CONSTRAINT `tax_returns_commission_statement_id_foreign` FOREIGN KEY (`commission_statement_id`) REFERENCES `commission_statements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tax_returns`
--

LOCK TABLES `tax_returns` WRITE;
/*!40000 ALTER TABLE `tax_returns` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_returns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint unsigned DEFAULT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'support',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin@gmail.com',1,'support',1,'2026-01-09 06:33:11','127.0.0.1','2026-01-09 02:03:11','$2y$12$hyKwrpMvQkRYXpBoj30HnOtErCAlesmVgLY3wglkGEU.LCyR4RjQ2',NULL,'2026-01-09 02:03:11','2026-01-09 06:33:11');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_id` bigint unsigned NOT NULL,
  `regn_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `make` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usage` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manufacture_year` year DEFAULT NULL,
  `value` decimal(15,2) DEFAULT NULL,
  `engine` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine_capacity` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chassis_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_from` date DEFAULT NULL,
  `cover_to` date DEFAULT NULL,
  `slta_certificate_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proof_of_purchase_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value_certificate_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_seats` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicles_vehicle_code_unique` (`vehicle_code`),
  KEY `vehicles_policy_id_foreign` (`policy_id`),
  CONSTRAINT `vehicles_policy_id_foreign` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-09 23:03:11
