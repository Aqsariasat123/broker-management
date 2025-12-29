-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 29, 2025 at 08:53 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `broker`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` bigint UNSIGNED DEFAULT NULL,
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
  KEY `audit_logs_action_index` (`action`)
) ENGINE=MyISAM AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `description`, `old_values`, `new_values`, `ip_address`, `user_agent`, `url`, `method`, `created_at`, `updated_at`) VALUES
(1, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 OPR/123.0.0.0', 'http://127.0.0.1:8000/logout', 'GET', '2025-11-27 04:38:59', '2025-11-27 04:38:59'),
(2, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 OPR/123.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-11-27 04:39:07', '2025-11-27 04:39:07'),
(3, 1, 'create', 'App\\Models\\Role', 3, 'Role created: Manager', NULL, '{\"id\": 3, \"name\": \"Manager\", \"slug\": \"manager\", \"created_at\": \"2025-11-27 10:12:16\", \"updated_at\": \"2025-11-27 10:12:16\", \"description\": null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 OPR/123.0.0.0', 'http://127.0.0.1:8000/roles', 'POST', '2025-11-27 05:12:16', '2025-11-27 05:12:16'),
(4, 1, 'update', 'App\\Models\\Role', 1, 'Role permissions updated: Admin', NULL, '{\"permissions\": [\"10\", \"12\", \"11\", \"9\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 OPR/123.0.0.0', 'http://127.0.0.1:8000/roles/1/permissions', 'PUT', '2025-11-27 05:14:15', '2025-11-27 05:14:15'),
(5, 1, 'create', 'App\\Models\\Schedule', 1, 'Schedule created: 1', NULL, '{\"id\": 1, \"notes\": null, \"status\": \"active\", \"issued_on\": \"2025-11-29 00:00:00\", \"policy_id\": \"5\", \"created_at\": \"2025-11-27 10:40:04\", \"updated_at\": \"2025-11-27 10:40:04\", \"schedule_no\": \"1\", \"effective_to\": \"2026-12-28 00:00:00\", \"effective_from\": \"2025-11-29 00:00:00\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 OPR/123.0.0.0', 'http://127.0.0.1:8000/schedules', 'POST', '2025-11-27 05:40:04', '2025-11-27 05:40:04'),
(6, 1, 'create', 'App\\Models\\PaymentPlan', 1, 'Payment plan created: 1', NULL, '{\"id\": 1, \"amount\": \"100\", \"status\": \"active\", \"due_date\": \"2025-12-06 00:00:00\", \"frequency\": \"Year\", \"created_at\": \"2025-11-27 10:40:21\", \"updated_at\": \"2025-11-27 10:40:21\", \"schedule_id\": \"1\", \"installment_label\": \"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 OPR/123.0.0.0', 'http://127.0.0.1:8000/payment-plans', 'POST', '2025-11-27 05:40:21', '2025-11-27 05:40:21'),
(7, 1, 'create', 'App\\Models\\DebitNote', 1, 'Debit note created: 12', NULL, '{\"id\": 1, \"amount\": \"10\", \"status\": \"issued\", \"issued_on\": \"2025-11-27 00:00:00\", \"created_at\": \"2025-11-27 10:42:26\", \"updated_at\": \"2025-11-27 10:42:26\", \"debit_note_no\": \"12\", \"document_path\": \"sad\", \"payment_plan_id\": \"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 OPR/123.0.0.0', 'http://127.0.0.1:8000/debit-notes', 'POST', '2025-11-27 05:42:26', '2025-11-27 05:42:26'),
(8, 1, 'create', 'App\\Models\\Payment', 1, 'Payment recorded: 12312', NULL, '{\"id\": 1, \"notes\": null, \"amount\": \"100\", \"paid_on\": \"2025-11-27 00:00:00\", \"created_at\": \"2025-11-27 10:43:05\", \"updated_at\": \"2025-11-27 10:43:05\", \"receipt_path\": \"kljk\", \"debit_note_id\": \"1\", \"payment_reference\": \"12312\", \"mode_of_payment_id\": \"162\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 OPR/123.0.0.0', 'http://127.0.0.1:8000/payments', 'POST', '2025-11-27 05:43:05', '2025-11-27 05:43:05'),
(9, 1, 'update', 'App\\Models\\Payment', 1, 'Payment updated: 12312', '{\"id\": 1, \"notes\": null, \"amount\": \"100.00\", \"paid_on\": \"2025-11-27\", \"created_at\": \"2025-11-27 10:43:05\", \"updated_at\": \"2025-11-27 10:43:05\", \"receipt_path\": \"kljk\", \"debit_note_id\": 1, \"payment_reference\": \"12312\", \"mode_of_payment_id\": 162}', '{\"updated_at\": \"2025-11-27 10:48:03\", \"receipt_path\": \"receipts/receipt_69282c6335f63_1764240483.jpg\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 OPR/123.0.0.0', 'http://127.0.0.1:8000/payments/1', 'PUT', '2025-11-27 05:48:03', '2025-11-27 05:48:03'),
(10, 1, 'update', 'App\\Models\\DebitNote', 1, 'Debit note updated: 12', '{\"id\": 1, \"amount\": \"10.00\", \"status\": \"paid\", \"issued_on\": \"2025-11-27\", \"created_at\": \"2025-11-27 10:42:26\", \"updated_at\": \"2025-11-27 10:43:05\", \"debit_note_no\": \"12\", \"document_path\": \"sad\", \"payment_plan_id\": 1}', '{\"updated_at\": \"2025-11-27 10:50:25\", \"document_path\": \"debit-notes/debit_note_69282cf154f70_1764240625.jpg\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 OPR/123.0.0.0', 'http://127.0.0.1:8000/debit-notes/1', 'PUT', '2025-11-27 05:50:25', '2025-11-27 05:50:25'),
(11, 1, 'delete', 'App\\Models\\PaymentPlan', 3, 'Payment plan deleted: Instalment 2 of 2', '{\"id\": 3, \"amount\": \"50.00\", \"status\": \"pending\", \"due_date\": \"2025-12-29\", \"frequency\": \"Year\", \"created_at\": \"2025-11-27 10:40:46\", \"updated_at\": \"2025-11-27 10:40:46\", \"schedule_id\": 1, \"installment_label\": \"Instalment 2 of 2\"}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 OPR/123.0.0.0', 'http://127.0.0.1:8000/payment-plans/3', 'DELETE', '2025-11-27 05:56:21', '2025-11-27 05:56:21'),
(12, NULL, 'create', 'App\\Models\\DebitNote', 2, 'Debit note auto-generated for payment plan: Instalment 1 of 2', NULL, '{\"id\": 2, \"amount\": \"50.00\", \"status\": \"issued\", \"issued_on\": \"2025-11-27 10:58:30\", \"created_at\": \"2025-11-27 10:58:30\", \"updated_at\": \"2025-11-27 10:58:30\", \"debit_note_no\": \"DN000013\", \"payment_plan_id\": 2}', '127.0.0.1', 'Symfony', 'http://localhost', 'GET', '2025-11-27 05:58:30', '2025-11-27 05:58:30'),
(13, 1, 'update', 'App\\Models\\DebitNote', 2, 'Debit note updated: DN000013', '{\"id\": 2, \"amount\": \"50.00\", \"status\": \"issued\", \"issued_on\": \"2025-11-27\", \"created_at\": \"2025-11-27 10:58:30\", \"updated_at\": \"2025-11-27 10:58:30\", \"is_encrypted\": 0, \"debit_note_no\": \"DN000013\", \"document_path\": null, \"payment_plan_id\": 2}', '{\"updated_at\": \"2025-11-27 11:09:38\", \"is_encrypted\": true, \"document_path\": \"debit-notes/48e9e8cd87355bd6e256fed9193ab661b8beb51a215cad00edf2d7d03e99f54d.enc\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36 OPR/123.0.0.0', 'http://127.0.0.1:8000/debit-notes/2', 'PUT', '2025-11-27 06:09:38', '2025-11-27 06:09:38'),
(14, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-11-27 23:35:22', '2025-11-27 23:35:22'),
(15, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-01 02:35:37', '2025-12-01 02:35:37'),
(16, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/logout', 'GET', '2025-12-01 03:27:13', '2025-12-01 03:27:13'),
(17, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-01 03:27:17', '2025-12-01 03:27:17'),
(18, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-02 02:24:37', '2025-12-02 02:24:37'),
(19, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/logout', 'GET', '2025-12-02 02:40:55', '2025-12-02 02:40:55'),
(20, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-02 02:41:00', '2025-12-02 02:41:00'),
(21, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-03 10:34:23', '2025-12-03 10:34:23'),
(22, 1, 'update', 'App\\Models\\Role', 1, 'Role permissions updated: Admin', NULL, '{\"permissions\": [\"2\", \"4\", \"3\", \"10\", \"12\", \"11\", \"9\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/roles/1/permissions', 'PUT', '2025-12-03 11:47:14', '2025-12-03 11:47:14'),
(23, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-03 21:45:41', '2025-12-03 21:45:41'),
(24, 1, 'update', 'App\\Models\\Role', 1, 'Role permissions updated: Admin', NULL, '{\"permissions\": [\"2\", \"4\", \"3\", \"14\", \"13\", \"10\", \"12\", \"11\", \"9\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/roles/1/permissions', 'PUT', '2025-12-03 21:48:14', '2025-12-03 21:48:14'),
(25, 1, 'update', 'App\\Models\\Role', 1, 'Role permissions updated: Admin', NULL, '{\"permissions\": [\"2\", \"4\", \"3\", \"1\", \"14\", \"13\", \"10\", \"12\", \"11\", \"9\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/roles/1/permissions', 'PUT', '2025-12-03 23:34:07', '2025-12-03 23:34:07'),
(26, 1, 'create', 'App\\Models\\Role', 4, 'Role created: super_admin', NULL, '{\"id\": 4, \"name\": \"super_admin\", \"slug\": \"superadmin\", \"created_at\": \"2025-12-04 04:42:32\", \"updated_at\": \"2025-12-04 04:42:32\", \"description\": \"super_admin\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/roles', 'POST', '2025-12-03 23:42:32', '2025-12-03 23:42:32'),
(27, 1, 'create', 'App\\Models\\User', 2, 'User created: touqeer', NULL, '{\"id\": 2, \"name\": \"touqeer\", \"email\": \"atouqeer745@gmail.com\", \"role_id\": \"4\", \"password\": \"$2y$12$4pk43T73lWf455rUvh453.khepp7l5M6D8in6Qv55uKSY6dVOznKy\", \"is_active\": true, \"created_at\": \"2025-12-04 04:43:38\", \"updated_at\": \"2025-12-04 04:43:38\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/users', 'POST', '2025-12-03 23:43:38', '2025-12-03 23:43:38'),
(28, 1, 'create', 'App\\Models\\Permission', 16, 'Permission created: contacts Create', NULL, '{\"id\": 16, \"name\": \"contacts Create\", \"slug\": \"contacts.create\", \"module\": \"contacts\", \"created_at\": \"2025-12-04 04:46:36\", \"updated_at\": \"2025-12-04 04:46:36\", \"description\": \"contacts\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/permissions', 'POST', '2025-12-03 23:46:36', '2025-12-03 23:46:36'),
(29, 1, 'create', 'App\\Models\\Role', 5, 'Role created: cacher', NULL, '{\"id\": 5, \"name\": \"cacher\", \"slug\": \"cacher\", \"created_at\": \"2025-12-04 04:47:43\", \"updated_at\": \"2025-12-04 04:47:43\", \"description\": null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/roles', 'POST', '2025-12-03 23:47:43', '2025-12-03 23:47:43'),
(30, 1, 'create', 'App\\Models\\Role', 6, 'Role created: recovery', NULL, '{\"id\": 6, \"name\": \"recovery\", \"slug\": \"recovery\", \"created_at\": \"2025-12-04 04:47:54\", \"updated_at\": \"2025-12-04 04:47:54\", \"description\": null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/roles', 'POST', '2025-12-03 23:47:54', '2025-12-03 23:47:54'),
(31, 1, 'create', 'App\\Models\\Role', 7, 'Role created: marketing', NULL, '{\"id\": 7, \"name\": \"marketing\", \"slug\": \"marketing\", \"created_at\": \"2025-12-04 04:48:30\", \"updated_at\": \"2025-12-04 04:48:30\", \"description\": null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/roles', 'POST', '2025-12-03 23:48:30', '2025-12-03 23:48:30'),
(32, 1, 'create', 'App\\Models\\Role', 8, 'Role created: sales', NULL, '{\"id\": 8, \"name\": \"sales\", \"slug\": \"sales\", \"created_at\": \"2025-12-04 04:48:38\", \"updated_at\": \"2025-12-04 04:48:38\", \"description\": null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/roles', 'POST', '2025-12-03 23:48:38', '2025-12-03 23:48:38'),
(33, 1, 'update', 'App\\Models\\Role', 1, 'Role permissions updated: Admin', NULL, '{\"permissions\": [\"2\", \"4\", \"3\", \"1\", \"14\", \"13\", \"10\", \"12\", \"11\", \"9\"]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/roles/1/permissions', 'PUT', '2025-12-03 23:48:52', '2025-12-03 23:48:52'),
(34, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-04 23:01:37', '2025-12-04 23:01:37'),
(35, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/logout', 'GET', '2025-12-05 00:08:35', '2025-12-05 00:08:35'),
(36, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-05 00:08:40', '2025-12-05 00:08:40'),
(37, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-05 05:33:28', '2025-12-05 05:33:28'),
(38, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-06 04:18:52', '2025-12-06 04:18:52'),
(39, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-08 02:49:50', '2025-12-08 02:49:50'),
(40, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-08 23:51:55', '2025-12-08 23:51:55'),
(41, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-09 04:34:24', '2025-12-09 04:34:24'),
(42, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-09 23:05:46', '2025-12-09 23:05:46'),
(43, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-10 06:07:54', '2025-12-10 06:07:54'),
(44, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-10 06:08:03', '2025-12-10 06:08:03'),
(45, 1, 'create', 'App\\Models\\Schedule', 5, 'Renewal schedule created: SCH000005', NULL, '{\"id\": 5, \"notes\": \"Renewal Schedule Details:\\nYear: 2026\\nPolicy Plan: Comprehensive\\nSum Insured: 1,000,000.00\\nTerm: 1 Year\\nAdd Ons: 123\\nBase Premium: 1,000.00\\nFull Premium: 1,000,000.00\\nPay Plan Type: Single\\nNOP: 1\\nFrequency: year\\nNote: 12312\", \"status\": \"active\", \"issued_on\": \"2025-12-10 13:02:39\", \"policy_id\": 27, \"created_at\": \"2025-12-10 13:02:39\", \"updated_at\": \"2025-12-10 13:02:39\", \"schedule_no\": \"SCH000005\", \"effective_to\": \"2026-12-18 00:00:00\", \"effective_from\": \"2026-12-04 00:00:00\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/policies/27/renewal-schedule', 'POST', '2025-12-10 08:02:39', '2025-12-10 08:02:39'),
(46, 1, 'create', 'App\\Models\\Schedule', 7, 'Renewal schedule created: SCH000007', NULL, '{\"id\": 7, \"notes\": \"Renewal Schedule Details:\\nYear: 2026\\nPolicy Plan: Comprehensive\\nSum Insured: 1,000,000.00\\nTerm: 1 Year\\nAdd Ons: bnw e\\nBase Premium: 1,000.00\\nFull Premium: 1,000,000.00\\nPay Plan Type: Single\\nNOP: 4\\nFrequency: year\\nNote: sad\", \"status\": \"active\", \"issued_on\": \"2025-12-10 13:19:15\", \"policy_id\": 27, \"created_at\": \"2025-12-10 13:19:15\", \"updated_at\": \"2025-12-10 13:19:15\", \"schedule_no\": \"SCH000007\", \"effective_to\": \"2026-12-18 00:00:00\", \"effective_from\": \"2026-12-04 00:00:00\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/policies/27/renewal-schedule', 'POST', '2025-12-10 08:19:15', '2025-12-10 08:19:15'),
(47, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-11 04:36:01', '2025-12-11 04:36:01'),
(48, NULL, 'login_failed', NULL, NULL, 'Failed login attempt for: admin', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-12 22:58:21', '2025-12-12 22:58:21'),
(49, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-12 22:58:24', '2025-12-12 22:58:24'),
(50, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-15 00:25:52', '2025-12-15 00:25:52'),
(51, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-15 01:40:16', '2025-12-15 01:40:16'),
(52, 1, 'update', 'App\\Models\\PaymentPlan', 7, 'Payment plan updated: Instalment 1 of 2', '{\"id\": 7, \"amount\": \"500000.00\", \"status\": \"pending\", \"due_date\": \"2025-12-04\", \"frequency\": \"year\", \"created_at\": \"2025-12-10 13:01:28\", \"updated_at\": \"2025-12-10 13:01:28\", \"schedule_id\": 4, \"installment_label\": \"Instalment 1 of 2\"}', '[]', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/payment-plans/7', 'PUT', '2025-12-15 06:10:34', '2025-12-15 06:10:34'),
(53, 1, 'update', 'App\\Models\\DebitNote', 1, 'Debit note updated: 12', '{\"id\": 1, \"amount\": \"10.00\", \"status\": \"paid\", \"issued_on\": \"2025-11-27\", \"created_at\": \"2025-11-27 10:42:26\", \"updated_at\": \"2025-11-27 10:50:25\", \"is_encrypted\": 0, \"debit_note_no\": \"12\", \"document_path\": \"debit-notes/debit_note_69282cf154f70_1764240625.jpg\", \"payment_plan_id\": 1}', '{\"updated_at\": \"2025-12-15 11:14:05\", \"is_encrypted\": true, \"document_path\": \"debit-notes/ffc91554b91e2ed1b4d13260f731d1de242da7d7408e226e9027aa02876d6c03.enc\", \"payment_plan_id\": \"16\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/debit-notes/1', 'PUT', '2025-12-15 06:14:05', '2025-12-15 06:14:05'),
(54, 1, 'update', 'App\\Models\\DebitNote', 2, 'Debit note updated: DN000013', '{\"id\": 2, \"amount\": \"50.00\", \"status\": \"issued\", \"issued_on\": \"2025-11-27\", \"created_at\": \"2025-11-27 10:58:30\", \"updated_at\": \"2025-11-27 11:09:38\", \"is_encrypted\": 0, \"debit_note_no\": \"DN000013\", \"document_path\": \"debit-notes/48e9e8cd87355bd6e256fed9193ab661b8beb51a215cad00edf2d7d03e99f54d.enc\", \"payment_plan_id\": 2}', '{\"updated_at\": \"2025-12-15 11:25:03\", \"payment_plan_id\": \"16\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/debit-notes/2', 'PUT', '2025-12-15 06:25:03', '2025-12-15 06:25:03'),
(55, 1, 'update', 'App\\Models\\DebitNote', 2, 'Debit note updated: DN000013', '{\"id\": 2, \"amount\": \"50.00\", \"status\": \"issued\", \"issued_on\": \"2025-11-27\", \"created_at\": \"2025-11-27 10:58:30\", \"updated_at\": \"2025-12-15 11:25:03\", \"is_encrypted\": 0, \"debit_note_no\": \"DN000013\", \"document_path\": \"debit-notes/48e9e8cd87355bd6e256fed9193ab661b8beb51a215cad00edf2d7d03e99f54d.enc\", \"payment_plan_id\": 16}', '[]', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/debit-notes/2', 'PUT', '2025-12-15 06:25:13', '2025-12-15 06:25:13'),
(56, 1, 'update', 'App\\Models\\PaymentPlan', 7, 'Payment plan updated: Instalment 1 of 2', '{\"id\": 7, \"amount\": \"500000.00\", \"status\": \"pending\", \"due_date\": \"2025-12-04\", \"frequency\": \"year\", \"created_at\": \"2025-12-10 13:01:28\", \"updated_at\": \"2025-12-10 13:01:28\", \"schedule_id\": 4, \"installment_label\": \"Instalment 1 of 2\"}', '{\"frequency\": \"Year\", \"updated_at\": \"2025-12-15 11:33:02\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/payment-plans/7', 'PUT', '2025-12-15 06:33:02', '2025-12-15 06:33:02'),
(57, 1, 'update', 'App\\Models\\PaymentPlan', 7, 'Payment plan updated: Instalment 1 of 2', '{\"id\": 7, \"amount\": \"500000.00\", \"status\": \"pending\", \"due_date\": \"2025-12-04\", \"frequency\": \"Year\", \"created_at\": \"2025-12-10 13:01:28\", \"updated_at\": \"2025-12-15 11:33:02\", \"schedule_id\": 4, \"installment_label\": \"Instalment 1 of 2\"}', '[]', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/payment-plans/7', 'PUT', '2025-12-15 06:33:06', '2025-12-15 06:33:06'),
(58, 1, 'create', 'App\\Models\\Payment', 2, 'Payment recorded: sdas', NULL, '{\"id\": 2, \"notes\": \"hjkh\", \"amount\": \"10000\", \"paid_on\": \"2025-12-01 00:00:00\", \"created_at\": \"2025-12-15 11:33:46\", \"updated_at\": \"2025-12-15 11:33:46\", \"is_encrypted\": true, \"receipt_path\": \"receipts/e01a0dd88f35e3e3fb88ca4eab8ce59137d14697ba8ee55692ddd7943c622781.enc\", \"debit_note_id\": \"2\", \"payment_reference\": \"sdas\", \"mode_of_payment_id\": \"162\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/payments', 'POST', '2025-12-15 06:33:46', '2025-12-15 06:33:46'),
(59, 1, 'create', 'App\\Models\\Payment', 3, 'Payment recorded: 12312', NULL, '{\"id\": 3, \"notes\": \"asd\", \"amount\": \"1000\", \"paid_on\": \"2025-12-01 00:00:00\", \"created_at\": \"2025-12-15 11:35:08\", \"updated_at\": \"2025-12-15 11:35:08\", \"is_encrypted\": true, \"receipt_path\": \"receipts/6c2f5646ff8a7989dedd6c07630ef5d9d7628373edbdc7045d758bb51106a644.enc\", \"debit_note_id\": \"2\", \"payment_reference\": \"12312\", \"mode_of_payment_id\": \"162\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/payments', 'POST', '2025-12-15 06:35:08', '2025-12-15 06:35:08'),
(60, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-15 23:02:21', '2025-12-15 23:02:21'),
(61, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-18 04:09:14', '2025-12-18 04:09:14'),
(62, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-18 23:03:16', '2025-12-18 23:03:16'),
(63, NULL, 'login_failed', NULL, NULL, 'Failed login attempt for: admin', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-19 00:10:29', '2025-12-19 00:10:29'),
(64, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-19 00:10:40', '2025-12-19 00:10:40'),
(65, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-22 02:21:00', '2025-12-22 02:21:00'),
(66, NULL, 'login_failed', NULL, NULL, 'Failed login attempt for: admin', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-22 23:02:48', '2025-12-22 23:02:48'),
(67, NULL, 'login_failed', NULL, NULL, 'Failed login attempt for: admin', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-22 23:02:50', '2025-12-22 23:02:50'),
(68, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-22 23:02:52', '2025-12-22 23:02:52'),
(69, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-24 05:49:13', '2025-12-24 05:49:13'),
(70, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-27 00:59:53', '2025-12-27 00:59:53'),
(71, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-28 23:00:54', '2025-12-28 23:00:54'),
(72, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0', 'http://127.0.0.1:8000/logout', 'GET', '2025-12-29 00:26:25', '2025-12-29 00:26:25'),
(73, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0', 'http://127.0.0.1:8000/login', 'POST', '2025-12-29 00:26:30', '2025-12-29 00:26:30'),
(74, 1, 'create', 'App\\Models\\Payment', 4, 'Payment recorded: 123', NULL, '{\"id\": 4, \"notes\": null, \"amount\": \"10000\", \"paid_on\": \"2025-12-10 00:00:00\", \"created_at\": \"2025-12-29 07:14:03\", \"updated_at\": \"2025-12-29 07:14:03\", \"is_encrypted\": true, \"receipt_path\": \"receipts/b4fec59699f1f34e635f99cdb282589f8dc92223179183709411c14c9116f331.enc\", \"debit_note_id\": \"10\", \"payment_reference\": \"123\", \"mode_of_payment_id\": \"162\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0', 'http://127.0.0.1:8000/payments', 'POST', '2025-12-29 02:14:03', '2025-12-29 02:14:03');

-- --------------------------------------------------------

--
-- Table structure for table `beneficial_owners`
--

DROP TABLE IF EXISTS `beneficial_owners`;
CREATE TABLE IF NOT EXISTS `beneficial_owners` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `owner_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` bigint UNSIGNED NOT NULL,
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
  KEY `beneficial_owners_client_id_foreign` (`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

DROP TABLE IF EXISTS `claims`;
CREATE TABLE IF NOT EXISTS `claims` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `policy_id` bigint UNSIGNED DEFAULT NULL,
  `client_id` bigint UNSIGNED DEFAULT NULL,
  `claim_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loss_date` date DEFAULT NULL,
  `claim_date` date DEFAULT NULL,
  `claim_amount` decimal(15,2) DEFAULT NULL,
  `claim_summary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `claim_stage` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `close_date` date DEFAULT NULL,
  `paid_amount` decimal(15,2) DEFAULT NULL,
  `settlment_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `claims_claim_id_unique` (`claim_id`),
  KEY `claims_policy_id_foreign` (`policy_id`),
  KEY `claims_client_id_foreign` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `claims`
--

INSERT INTO `claims` (`id`, `policy_id`, `client_id`, `claim_id`, `policy_no`, `loss_date`, `claim_date`, `claim_amount`, `claim_summary`, `claim_stage`, `status`, `close_date`, `paid_amount`, `settlment_notes`, `created_at`, `updated_at`) VALUES
(4, NULL, NULL, 'CLM1001', 'FSP-21-P00012999', '2025-11-30', '2025-12-02', 10000.00, 'new meesage', 'Awaiting Documents', 'Processing', '2026-01-02', 100.00, NULL, '2025-12-15 05:01:44', '2025-12-15 05:02:18'),
(5, NULL, 22, 'CLM1002', '098', '2025-12-01', '2025-12-02', 12.00, NULL, 'Awaiting Documents', 'Processing', '2025-12-09', 1212.00, NULL, '2025-12-29 00:03:59', '2025-12-29 00:03:59');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nin_bcrn` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob_dor` date DEFAULT NULL,
  `mobile_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signed_up` date NOT NULL,
  `agency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `income_source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monthly_income` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `married` tinyint(1) NOT NULL DEFAULT '0',
  `spouses_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alternate_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` text COLLATE utf8mb4_unicode_ci,
  `island` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `po_box_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pep` tinyint(1) NOT NULL DEFAULT '0',
  `pep_comment` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salutation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `other_names` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `surname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `passport_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_expiry_date` date DEFAULT NULL,
  `has_vehicle` tinyint(1) NOT NULL DEFAULT '0',
  `has_house` tinyint(1) NOT NULL DEFAULT '0',
  `has_business` tinyint(1) NOT NULL DEFAULT '0',
  `has_boat` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_document_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  `poa_document_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `clients_clid_unique` (`clid`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `client_name`, `client_type`, `nin_bcrn`, `dob_dor`, `mobile_no`, `wa`, `district`, `occupation`, `source`, `source_name`, `status`, `signed_up`, `agency`, `agent`, `employer`, `clid`, `contact_person`, `income_source`, `monthly_income`, `married`, `spouses_name`, `alternate_no`, `email_address`, `location`, `island`, `country`, `po_box_no`, `pep`, `pep_comment`, `image`, `salutation`, `first_name`, `other_names`, `surname`, `passport_no`, `id_expiry_date`, `has_vehicle`, `has_house`, `has_business`, `has_boat`, `notes`, `created_at`, `updated_at`, `id_document_encrypted`, `poa_document_encrypted`) VALUES
(22, 'Mahmaar', 'Business', '123', NULL, '03470917748', '1', '272', NULL, '80', 'Usman', 'Active', '2025-12-18', '118', '123', NULL, 'CL1001', 'Touqeer', NULL, NULL, 0, NULL, NULL, 'atouqeer745@gmail.com', 'Rawalpindi', '155', '131', 'Rawalpidni', 0, NULL, NULL, NULL, '', '', 'Mahmaar', NULL, NULL, 0, 0, 0, 0, NULL, '2025-12-28 23:03:32', '2025-12-28 23:03:32', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `commissions`
--

DROP TABLE IF EXISTS `commissions`;
CREATE TABLE IF NOT EXISTS `commissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `commission_note_id` bigint UNSIGNED NOT NULL,
  `commission_statement_id` bigint UNSIGNED DEFAULT NULL,
  `grouping` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `basic_premium` decimal(15,2) DEFAULT NULL,
  `rate` decimal(8,2) DEFAULT NULL,
  `amount_due` decimal(15,2) DEFAULT NULL,
  `payment_status_id` bigint UNSIGNED DEFAULT NULL,
  `amount_received` decimal(15,2) DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `statement_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mode_of_payment_id` bigint UNSIGNED DEFAULT NULL,
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
  KEY `commissions_mode_of_payment_id_foreign` (`mode_of_payment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `commissions`
--

INSERT INTO `commissions` (`id`, `commission_note_id`, `commission_statement_id`, `grouping`, `basic_premium`, `rate`, `amount_due`, `payment_status_id`, `amount_received`, `date_received`, `statement_no`, `mode_of_payment_id`, `variance`, `variance_reason`, `date_due`, `commission_code`, `created_at`, `updated_at`) VALUES
(2, 2, 2, 'Policy Commission', 1000000.00, 0.00, 0.00, 120, 100.00, '2026-02-05', '123123123', 162, NULL, NULL, '2025-12-31', 'COM000009', '2025-12-29 00:15:16', '2025-12-29 02:13:08');

-- --------------------------------------------------------

--
-- Table structure for table `commission_notes`
--

DROP TABLE IF EXISTS `commission_notes`;
CREATE TABLE IF NOT EXISTS `commission_notes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `schedule_id` bigint UNSIGNED NOT NULL,
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
  KEY `commission_notes_schedule_id_foreign` (`schedule_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `commission_notes`
--

INSERT INTO `commission_notes` (`id`, `schedule_id`, `com_note_id`, `issued_on`, `total_premium`, `expected_commission`, `attachment_path`, `remarks`, `created_at`, `updated_at`) VALUES
(2, 9, 'CN000009', '2025-12-29', 1000000.00, 0.00, NULL, 'Auto-generated commission note', '2025-12-29 00:15:16', '2025-12-29 00:15:16');

-- --------------------------------------------------------

--
-- Table structure for table `commission_statements`
--

DROP TABLE IF EXISTS `commission_statements`;
CREATE TABLE IF NOT EXISTS `commission_statements` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `commission_note_id` bigint UNSIGNED DEFAULT NULL,
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
  KEY `commission_statements_commission_note_id_foreign` (`commission_note_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `commission_statements`
--

INSERT INTO `commission_statements` (`id`, `commission_note_id`, `com_stat_id`, `period_start`, `period_end`, `net_commission`, `tax_withheld`, `attachment_path`, `remarks`, `created_at`, `updated_at`) VALUES
(2, 2, 'CS000009', '2025-12-01', '2025-12-31', 0.00, 0.00, NULL, 'Auto-generated commission statement', '2025-12-29 00:15:16', '2025-12-29 00:15:16');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wa` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acquired` date DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rank` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_contact` date DEFAULT NULL,
  `next_follow_up` date DEFAULT NULL,
  `coid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `salutation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `savings_budget` decimal(10,2) DEFAULT NULL,
  `married` tinyint(1) NOT NULL DEFAULT '0',
  `children` int NOT NULL DEFAULT '0',
  `children_details` text COLLATE utf8mb4_unicode_ci,
  `vehicle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `house` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contacts_contact_id_unique` (`contact_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `contact_name`, `contact_no`, `wa`, `type`, `occupation`, `employer`, `acquired`, `source`, `status`, `rank`, `first_contact`, `next_follow_up`, `coid`, `dob`, `salutation`, `source_name`, `agency`, `agent`, `address`, `location`, `email_address`, `contact_id`, `savings_budget`, `married`, `children`, `children_details`, `vehicle`, `house`, `business`, `other`, `created_at`, `updated_at`) VALUES
(8, 'Touqeer', '03470917748', NULL, '41', 'Accountant', 'test', '2025-12-25', '80', '104', '263', NULL, NULL, NULL, '2017-01-02', '172', 'Umans', '118', '123', 'test', NULL, 'gg@gmail.com', 'CT171', 10000.00, 0, 2, NULL, NULL, NULL, NULL, NULL, '2025-12-27 02:49:28', '2025-12-27 03:26:05');

-- --------------------------------------------------------

--
-- Table structure for table `debit_notes`
--

DROP TABLE IF EXISTS `debit_notes`;
CREATE TABLE IF NOT EXISTS `debit_notes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_plan_id` bigint UNSIGNED NOT NULL,
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
  KEY `debit_notes_payment_plan_id_foreign` (`payment_plan_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `debit_notes`
--

INSERT INTO `debit_notes` (`id`, `payment_plan_id`, `debit_note_no`, `issued_on`, `amount`, `status`, `document_path`, `is_encrypted`, `created_at`, `updated_at`) VALUES
(10, 29, 'DN000001', '2025-12-01', 500000.00, 'partial', NULL, 0, '2025-12-29 02:13:08', '2025-12-29 02:14:03');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
CREATE TABLE IF NOT EXISTS `documents` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `doc_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tied_to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `format` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_added` date DEFAULT NULL,
  `year` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `documents_doc_id_unique` (`doc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `doc_id`, `tied_to`, `name`, `group`, `type`, `format`, `date_added`, `year`, `notes`, `file_path`, `is_encrypted`, `created_at`, `updated_at`) VALUES
(41, 'DOC1001', 'CL1001', 'Client Photo', 'Photo', 'Photo', 'docx', '2025-12-02', '12', NULL, 'documents/doc_6951fdb7c9183.docx', 0, '2025-12-28 23:04:08', '2025-12-28 23:04:08');

-- --------------------------------------------------------

--
-- Table structure for table `endorsements`
--

DROP TABLE IF EXISTS `endorsements`;
CREATE TABLE IF NOT EXISTS `endorsements` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `policy_id` bigint UNSIGNED NOT NULL,
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
  KEY `endorsements_policy_id_foreign` (`policy_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `endorsements`
--

INSERT INTO `endorsements` (`id`, `policy_id`, `endorsement_no`, `type`, `effective_date`, `status`, `description`, `endorsement_notes`, `created_at`, `updated_at`) VALUES
(1, 27, 'END000001', '320', '2025-12-04', 'draft', 'asd', NULL, '2025-12-23 02:20:58', '2025-12-23 02:20:58'),
(2, 27, 'END000002', '320', '2025-12-19', 'draft', 'asd', NULL, '2025-12-23 02:22:11', '2025-12-23 02:22:11');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `expense_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expense_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payee` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_paid` date DEFAULT NULL,
  `amount_paid` decimal(15,2) DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `mode_of_payment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expense_notes` text COLLATE utf8mb4_unicode_ci,
  `mode_of_payment_id` bigint UNSIGNED DEFAULT NULL,
  `attachment_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expenses_expense_code_unique` (`expense_code`),
  UNIQUE KEY `expenses_expense_id_unique` (`expense_id`),
  KEY `expenses_category_id_foreign` (`category_id`),
  KEY `expenses_mode_of_payment_id_foreign` (`mode_of_payment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `expense_id`, `expense_code`, `payee`, `date_paid`, `amount_paid`, `description`, `category_id`, `mode_of_payment`, `receipt_no`, `expense_notes`, `mode_of_payment_id`, `attachment_path`, `receipt_path`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'EX1001', 'EX1001', '123', '2025-12-07', 10000.00, 'asd', 187, 'Transfer', '12312312312', 'sdasd', 162, NULL, NULL, NULL, '2025-12-15 06:07:02', '2025-12-29 00:04:57'),
(2, 'EX1002', 'EX1002', 'new', '2025-12-01', 1000.00, 'asd', 187, 'Cash', '123', NULL, NULL, NULL, NULL, NULL, '2025-12-15 06:27:05', '2025-12-15 06:27:05');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `followups`
--

DROP TABLE IF EXISTS `followups`;
CREATE TABLE IF NOT EXISTS `followups` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `follow_up_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_id` bigint UNSIGNED DEFAULT NULL,
  `client_id` bigint UNSIGNED DEFAULT NULL,
  `life_proposal_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
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
  KEY `followups_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `incomes`
--

DROP TABLE IF EXISTS `incomes`;
CREATE TABLE IF NOT EXISTS `incomes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `income_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `income_source_id` bigint UNSIGNED DEFAULT NULL,
  `date_rcvd` date DEFAULT NULL,
  `amount_received` decimal(15,2) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mode_of_payment_id` bigint UNSIGNED DEFAULT NULL,
  `statement_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `income_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `incomes_income_id_unique` (`income_id`),
  KEY `incomes_income_source_id_foreign` (`income_source_id`),
  KEY `incomes_mode_of_payment_id_foreign` (`mode_of_payment_id`),
  KEY `incomes_category_id_foreign` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `incomes`
--

INSERT INTO `incomes` (`id`, `income_id`, `income_source_id`, `date_rcvd`, `amount_received`, `description`, `category_id`, `category`, `mode_of_payment_id`, `statement_no`, `income_notes`, `created_at`, `updated_at`) VALUES
(5, 'INC1001', 2, '2025-12-10', 123.00, '123', 313, NULL, 162, NULL, NULL, '2025-12-29 01:41:47', '2025-12-29 01:41:47');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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

-- --------------------------------------------------------

--
-- Table structure for table `life_proposals`
--

DROP TABLE IF EXISTS `life_proposals`;
CREATE TABLE IF NOT EXISTS `life_proposals` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `proposers_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `insurer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_plan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sum_assured` decimal(15,2) DEFAULT NULL,
  `term` int NOT NULL,
  `add_ons` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `riders` json DEFAULT NULL,
  `rider_premiums` json DEFAULT NULL,
  `offer_date` date NOT NULL,
  `premium` decimal(10,2) NOT NULL,
  `annual_premium` decimal(15,2) DEFAULT NULL,
  `base_premium` decimal(15,2) DEFAULT NULL,
  `admin_fee` decimal(15,2) DEFAULT NULL,
  `total_premium` decimal(15,2) DEFAULT NULL,
  `frequency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stage` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `age` int NOT NULL,
  `sex` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anb` int DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loading_premium` decimal(15,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `maturity_date` date DEFAULT NULL,
  `source_of_payment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method_of_payment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mcr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doctor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_sent` date DEFAULT NULL,
  `date_completed` date DEFAULT NULL,
  `medical_examination_required` tinyint(1) NOT NULL DEFAULT '0',
  `clinic` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_referred` date DEFAULT NULL,
  `exam_notes` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `agency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_submitted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `life_proposals_prid_unique` (`prid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `life_proposals`
--

INSERT INTO `life_proposals` (`id`, `proposers_name`, `insurer`, `policy_plan`, `sum_assured`, `term`, `add_ons`, `riders`, `rider_premiums`, `offer_date`, `premium`, `annual_premium`, `base_premium`, `admin_fee`, `total_premium`, `frequency`, `stage`, `date`, `age`, `sex`, `anb`, `status`, `policy_no`, `loading_premium`, `start_date`, `maturity_date`, `source_of_payment`, `method_of_payment`, `mcr`, `doctor`, `date_sent`, `date_completed`, `medical_examination_required`, `clinic`, `date_referred`, `exam_notes`, `notes`, `agency`, `source_name`, `prid`, `class`, `is_submitted`, `created_at`, `updated_at`) VALUES
(1, 'testasdas', 'Hsavy', 'Householder\'s', 444.00, 44, 'Accidental Death', '[\"ADB\", \"TPDWoP\", \"FIBT\"]', '{\"ADB\": \"1\", \"FIBT\": \"2\", \"TPDWoP\": \"1000\"}', '2025-10-30', 1003.00, NULL, NULL, NULL, 1003.00, 'Days', 'Offer Made', '2025-10-22', 45, NULL, NULL, 'Approved', NULL, NULL, NULL, NULL, 'Prize', NULL, '555', 'Dr. Williams', '2025-10-23', NULL, 0, NULL, NULL, NULL, 'test', 'Keystone', NULL, 'PR1001', 'General', 0, '2025-10-21 22:13:57', '2025-12-23 00:57:12');

-- --------------------------------------------------------

--
-- Table structure for table `lookup_categories`
--

DROP TABLE IF EXISTS `lookup_categories`;
CREATE TABLE IF NOT EXISTS `lookup_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lookup_categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lookup_categories`
--

INSERT INTO `lookup_categories` (`id`, `name`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Insurers', 1, NULL, NULL),
(2, 'Policy Classes', 1, NULL, NULL),
(3, 'Policy Plans', 1, NULL, NULL),
(4, 'Policy Statuses', 1, NULL, NULL),
(5, 'Business Types', 1, NULL, NULL),
(6, 'Term Units', 1, NULL, NULL),
(7, 'Frequencies', 1, NULL, NULL),
(8, 'Pay Plans', 1, NULL, NULL),
(9, 'Contact Type', 1, '2025-10-21 20:53:40', '2025-10-21 20:53:40'),
(10, 'Claim Stage', 1, '2025-10-21 20:53:40', '2025-10-21 20:53:40'),
(11, 'Vehicle Make', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(12, 'Client Type', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(13, 'Insurer', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(14, 'Frequency', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(15, 'Payment Plan', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(16, 'Contact Stage', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(17, 'Source', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(18, 'Contact Status', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(19, 'Policy Status', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(20, 'APL Agency', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(21, 'Payment Status', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(22, 'Agent', 1, '2025-10-21 20:53:41', '2025-12-12 23:36:45'),
(23, 'Ranking', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(24, 'Client Status', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(25, 'Issuing Country', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(26, 'Source Of Payment', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(27, 'ID Type', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(28, 'Class', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(29, 'Island', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(30, 'Mode Of Payment (Life)', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(31, 'Claim Status', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(32, 'Salutation', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(33, 'Mode Of Payment (General)', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(34, 'Useage', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(35, 'Expense Category', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(36, 'Vehicle Type', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(37, 'Income Category', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(38, 'Business Type', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(39, 'Income Source', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(40, 'Proposal Stage', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(41, 'Proposal Status', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(42, 'PaymentType', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(43, 'Term', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(44, 'Engine Type', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(45, 'ENDORSEMENT', 1, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(50, 'Channel', 1, '2025-12-09 23:31:44', '2025-12-09 23:31:44'),
(52, 'Task Category', 1, '2025-12-12 23:16:55', '2025-12-12 23:16:55'),
(53, 'Rank', 1, '2025-12-15 02:32:48', '2025-12-15 02:32:48'),
(58, 'District', 1, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(59, 'Occupation', 1, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(60, 'Document Type', 1, '2025-12-15 04:06:38', '2025-12-15 04:06:38'),
(62, 'Endorsements', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lookup_values`
--

DROP TABLE IF EXISTS `lookup_values`;
CREATE TABLE IF NOT EXISTS `lookup_values` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `lookup_category_id` bigint UNSIGNED NOT NULL,
  `seq` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lookup_values_lookup_category_id_seq_unique` (`lookup_category_id`,`seq`)
) ENGINE=InnoDB AUTO_INCREMENT=321 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lookup_values`
--

INSERT INTO `lookup_values` (`id`, `lookup_category_id`, `seq`, `name`, `active`, `description`, `type`, `code`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'SACOS', 1, 'asd', '123', 'as', NULL, '2025-12-12 23:15:41'),
(2, 1, 2, 'Alliance', 1, NULL, NULL, NULL, NULL, NULL),
(3, 1, 3, 'Hsavy', 1, NULL, NULL, NULL, NULL, NULL),
(4, 1, 4, 'AON', 1, NULL, NULL, NULL, NULL, NULL),
(5, 1, 5, 'Marsh', 1, NULL, NULL, NULL, NULL, NULL),
(6, 2, 1, 'Motor', 1, NULL, NULL, NULL, NULL, NULL),
(7, 2, 2, 'General', 1, NULL, NULL, NULL, NULL, NULL),
(8, 2, 3, 'Travel', 1, NULL, NULL, NULL, NULL, NULL),
(9, 2, 4, 'Marine', 1, NULL, NULL, NULL, NULL, NULL),
(10, 2, 5, 'Health', 1, NULL, NULL, NULL, NULL, NULL),
(11, 2, 6, 'Life', 1, NULL, NULL, NULL, NULL, NULL),
(12, 3, 1, 'Comprehensive', 1, NULL, NULL, NULL, NULL, NULL),
(13, 3, 2, 'Third Party', 1, NULL, NULL, NULL, NULL, NULL),
(14, 3, 3, 'Householder\'s', 1, NULL, NULL, NULL, NULL, NULL),
(15, 3, 4, 'Public Liability', 1, NULL, NULL, NULL, NULL, NULL),
(16, 3, 5, 'Employer\'s Liability', 1, NULL, NULL, NULL, NULL, NULL),
(17, 3, 6, 'Fire & Special Perils', 1, NULL, NULL, NULL, NULL, NULL),
(18, 3, 7, 'House Insurance', 1, NULL, NULL, NULL, NULL, NULL),
(19, 3, 8, 'Fire Industrial', 1, NULL, NULL, NULL, NULL, NULL),
(20, 3, 9, 'World Wide Basic', 1, NULL, NULL, NULL, NULL, NULL),
(21, 3, 10, 'Marine Hull', 1, NULL, NULL, NULL, NULL, NULL),
(22, 4, 1, 'In Force', 1, NULL, NULL, NULL, NULL, NULL),
(23, 4, 2, 'DFR', 1, NULL, NULL, NULL, NULL, NULL),
(24, 4, 3, 'Expired', 1, NULL, NULL, NULL, NULL, NULL),
(25, 4, 4, 'Cancelled', 1, NULL, NULL, NULL, NULL, NULL),
(26, 5, 1, 'Direct', 1, NULL, NULL, NULL, NULL, NULL),
(27, 5, 2, 'Transfer', 1, NULL, NULL, NULL, NULL, NULL),
(28, 5, 3, 'Renewal', 1, NULL, NULL, NULL, NULL, NULL),
(29, 6, 1, 'Year', 1, NULL, NULL, NULL, NULL, NULL),
(30, 6, 2, 'Month', 1, NULL, NULL, NULL, NULL, NULL),
(31, 6, 3, 'Days', 1, NULL, NULL, NULL, NULL, NULL),
(32, 7, 1, 'Annually', 1, NULL, NULL, NULL, NULL, NULL),
(33, 7, 2, 'Monthly', 1, NULL, NULL, NULL, NULL, NULL),
(34, 7, 3, 'Quarterly', 1, NULL, NULL, NULL, NULL, NULL),
(35, 7, 4, 'One Off', 1, NULL, NULL, NULL, NULL, NULL),
(36, 7, 5, 'Single', 1, NULL, NULL, NULL, NULL, NULL),
(37, 8, 1, 'Full', 1, NULL, NULL, NULL, NULL, NULL),
(38, 8, 2, 'Instalments', 1, NULL, NULL, NULL, NULL, NULL),
(39, 8, 3, 'Regular', 1, NULL, NULL, NULL, NULL, NULL),
(40, 9, 1, 'Lead', 1, NULL, NULL, NULL, '2025-10-21 20:53:40', '2025-10-21 20:53:40'),
(41, 9, 2, 'Prospect', 1, NULL, NULL, NULL, '2025-10-21 20:53:40', '2025-10-21 20:53:40'),
(42, 9, 3, 'Contact', 1, NULL, NULL, NULL, '2025-10-21 20:53:40', '2025-10-21 20:53:40'),
(43, 9, 4, 'SO Bank Officer', 1, NULL, NULL, NULL, '2025-10-21 20:53:40', '2025-10-21 20:53:40'),
(44, 9, 5, 'Payroll Officer', 1, NULL, NULL, NULL, '2025-10-21 20:53:40', '2025-10-21 20:53:40'),
(45, 10, 1, 'Awaiting Documents', 1, NULL, NULL, NULL, '2025-10-21 20:53:40', '2025-10-21 20:53:40'),
(46, 10, 2, 'Awaiting QS Report', 1, NULL, NULL, NULL, '2025-10-21 20:53:40', '2025-10-21 20:53:40'),
(47, 11, 1, 'Hyundai', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(48, 11, 2, 'Kia', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(49, 11, 3, 'Suzuki', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(50, 11, 4, 'Toyota', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(51, 11, 5, 'Ford', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(52, 11, 6, 'MG', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(53, 11, 7, 'Nissan', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(54, 11, 8, 'Mazda', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(55, 11, 9, 'BMW', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(56, 11, 10, 'Mercedes', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(57, 11, 11, 'Lexus', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(58, 11, 12, 'Haval', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(59, 11, 13, 'Honda', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(60, 11, 14, 'Tata', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(61, 11, 15, 'Isuzu', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(62, 12, 1, 'Individual', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(63, 12, 2, 'Business', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(64, 12, 3, 'Company', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(65, 12, 4, 'Organization', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(66, 13, 1, 'SACOS', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(67, 13, 2, 'HSavy', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(68, 13, 3, 'Alliance', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(69, 13, 4, 'MUA', 0, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(70, 14, 1, 'Year', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(71, 14, 2, 'Days', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(72, 14, 3, 'Weeks', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(73, 15, 1, 'Single', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(74, 15, 2, 'Instalments', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(75, 15, 3, 'Regular (Life)', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(76, 16, 1, 'Open', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(77, 16, 2, 'Qualified', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(78, 16, 3, 'KIV', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(79, 16, 4, 'Closed', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(80, 17, 1, 'Direct', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(81, 17, 2, 'Online', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(82, 17, 3, 'Bank ABSA', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(83, 17, 4, 'MCB', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(84, 17, 5, 'NOU', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(85, 17, 6, 'BAR', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(86, 17, 7, 'BOC', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(87, 17, 8, 'SCB', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(88, 17, 9, 'SCU', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(89, 17, 10, 'AIRTEL', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(90, 17, 11, 'Cable & Wireless', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(91, 17, 12, 'Intelvision', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(92, 17, 13, 'PUC', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(93, 17, 14, 'SFA', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(94, 17, 15, 'STC', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(95, 17, 16, 'FSA', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(96, 17, 17, 'Mins Of Education', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(97, 17, 18, 'Mins Of Health', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(98, 17, 19, 'SFRSA', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(99, 17, 20, 'Seychelles Police', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(100, 17, 21, 'Treasury', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(101, 17, 22, 'Judiciary', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(102, 17, 23, 'Pilgrims', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(103, 17, 24, 'SPTC', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(104, 18, 1, 'Not Contacted', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(105, 18, 2, 'Qualified', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(106, 18, 3, 'Converted to Client', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(107, 18, 4, 'Keep In View', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(108, 18, 5, 'Archived', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(109, 19, 1, 'In Force', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(110, 19, 2, 'Expired', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(111, 19, 3, 'Cancelled', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(112, 19, 4, 'Lapsed', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(113, 19, 5, 'Matured', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(114, 19, 6, 'Surrenders', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(115, 19, 7, 'Payout D', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(116, 19, 8, 'Payout TPD', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(117, 19, 9, 'Null & Void', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(118, 20, 1, 'Keystone', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(119, 20, 2, 'LIS', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(120, 21, 1, 'Paid', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(121, 21, 2, 'Partly Paid', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(122, 21, 3, 'Unpaid', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(123, 22, 1, 'Mandy', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(124, 22, 2, 'Simon', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(125, 23, 1, 'VIP', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(126, 23, 2, 'High', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(127, 23, 3, 'Medium', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(128, 23, 4, 'Low', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(129, 24, 1, 'Active', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(130, 24, 2, 'Dormant', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(131, 25, 1, 'Seychelles', 1, NULL, NULL, 'SEY', '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(132, 25, 2, 'Great Britain', 1, NULL, NULL, 'GBR', '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(133, 25, 3, 'Botswana', 1, NULL, NULL, 'BOT', '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(134, 25, 4, 'Sri Lanka', 1, NULL, NULL, 'SRI', '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(135, 25, 5, 'India', 1, NULL, NULL, 'IND', '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(136, 25, 6, 'Nepal', 1, NULL, NULL, 'NEP', '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(137, 25, 7, 'Bangladesh', 1, NULL, NULL, 'BAN', '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(138, 25, 8, 'Russia', 1, NULL, NULL, 'RUS', '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(139, 25, 9, 'Ukraine', 1, NULL, NULL, 'UKR', '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(140, 25, 10, 'Kenya', 1, NULL, NULL, 'KEN', '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(141, 26, 1, 'Commission', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(142, 26, 2, 'Bonus', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(143, 26, 3, 'Prize', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(144, 26, 4, 'Other', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(145, 27, 1, 'ID Card', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(146, 27, 2, 'Driving License', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(147, 27, 3, 'Passport', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(148, 28, 1, 'Motor', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(149, 28, 2, 'General', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(150, 28, 3, 'Life', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(151, 28, 4, 'Bonds', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(152, 28, 5, 'Travel', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(153, 28, 6, 'Marine', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(154, 28, 7, 'Health', 0, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(155, 29, 1, 'Mahe', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(156, 29, 2, 'Praslin', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(157, 29, 3, 'La Digue', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(158, 29, 4, 'Perseverance', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(159, 29, 5, 'Cerf', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(160, 29, 6, 'Eden', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(161, 29, 7, 'Silhouette', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(162, 30, 1, 'Transfer', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(163, 30, 2, 'Cheque', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(164, 30, 3, 'Cash', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(165, 30, 4, 'Online', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(166, 30, 5, 'Standing Order', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(167, 30, 6, 'Salary Deduction', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(168, 30, 7, 'Direect', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(169, 31, 1, 'Processing', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(170, 31, 2, 'Settled', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(171, 31, 3, 'Declined', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(172, 32, 1, 'Mr', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(173, 32, 2, 'Ms', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(174, 32, 3, 'Mrs', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(175, 32, 4, 'Miss', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(176, 32, 5, 'Dr', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(177, 32, 6, 'Mr & Mrs', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(178, 33, 1, 'Cash', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(179, 33, 2, 'Card', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(180, 33, 3, 'Transfer', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(181, 33, 4, 'Cheque', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(182, 34, 1, 'Private', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(183, 34, 2, 'Commercial', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(184, 34, 3, 'For Hire', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(185, 34, 4, 'Carriage Of Goods', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(186, 34, 5, 'Commuter', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(187, 35, 1, 'License', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(188, 35, 2, 'Insurance', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(189, 35, 3, 'Office supplies', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(190, 35, 4, 'Telephone & Internet', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(191, 35, 5, 'Marketting', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(192, 35, 6, 'Travel', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(193, 35, 7, 'Referals', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(194, 35, 8, 'Rentals', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(195, 35, 9, 'Vehicle', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(196, 35, 10, 'Fuel', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(197, 35, 11, 'Bank Fees', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(198, 35, 12, 'Charges', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(199, 35, 13, 'Misc', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(200, 35, 14, 'Asset Purchase', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(201, 36, 1, 'SUV', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(202, 36, 2, 'Hatchback', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(203, 36, 3, 'Sedan', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(204, 36, 4, 'Twin Cab', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(205, 36, 5, 'Pick Up', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(206, 36, 6, 'Scooter', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(207, 36, 7, 'Motor Cycle', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(208, 36, 8, 'Taxi', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(209, 36, 9, 'Van', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(216, 38, 1, 'Direct', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(217, 38, 2, 'Transfer', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(218, 38, 3, 'Renewal', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(219, 39, 1, 'Employment', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(220, 39, 2, 'Self Employed', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(221, 39, 3, 'Business', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(222, 39, 4, 'Investment', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(223, 39, 5, 'Rentals', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(224, 39, 6, 'Retirement', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(225, 39, 7, 'Allowance', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(226, 39, 8, 'Other', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(227, 40, 1, 'Not Contacted', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(228, 40, 2, 'RNR', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(229, 40, 3, 'In Discussion', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(230, 40, 4, 'Offer Made', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(231, 40, 5, 'Proposal Filled', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(232, 41, 1, 'Awaiting Medical', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(233, 41, 2, 'Awaiting Policy', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(234, 41, 3, 'Approved', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(235, 41, 4, 'Declined', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(236, 41, 5, 'Withdrawn', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(237, 42, 1, 'Full', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(238, 42, 2, 'Instalment', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(239, 42, 3, 'Adjustment', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(240, 43, 1, 'Annual', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(241, 43, 2, 'Single', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(242, 43, 3, 'Monthly', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(243, 43, 4, 'Quarterly', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(244, 43, 5, 'Bi-Annual', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(245, 44, 1, 'Hybrid', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(246, 44, 2, 'Petrol', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(247, 44, 3, 'Diesel', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(248, 44, 4, 'Electric', 1, NULL, NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(249, 45, 1, 'Renewal', 1, 'Policy Renewed', NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(250, 45, 2, 'Cancelation', 1, 'Policy Cancelled', NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(251, 45, 3, 'Amendment', 1, 'Sum Insured Reduced', NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(252, 45, 4, 'Amendment', 1, 'Sum Insured Increased', NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(253, 45, 5, 'Amendment', 1, 'Plan Cover Changed', NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(254, 45, 6, 'Amendment', 1, 'Beneficary change', NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(255, 45, 7, 'Amendment', 1, 'Pay Plan Changed', NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(256, 45, 8, 'Amendment', 1, 'Vehicle changed', NULL, NULL, '2025-10-21 20:53:41', '2025-10-21 20:53:41'),
(257, 50, 1, 'Direct', 1, NULL, NULL, NULL, '2025-12-09 23:31:44', '2025-12-09 23:31:44'),
(258, 50, 2, 'Online', 1, NULL, NULL, NULL, '2025-12-09 23:31:44', '2025-12-09 23:31:44'),
(259, 50, 3, 'Agent', 1, NULL, NULL, NULL, '2025-12-09 23:31:44', '2025-12-09 23:31:44'),
(260, 50, 4, 'Broker', 1, NULL, NULL, NULL, '2025-12-09 23:31:44', '2025-12-09 23:31:44'),
(261, 50, 5, 'Referral', 1, NULL, NULL, NULL, '2025-12-09 23:31:44', '2025-12-09 23:31:44'),
(262, 52, 1, 'new tast', 1, 'asd', '123', '12', '2025-12-12 23:19:11', '2025-12-12 23:19:11'),
(263, 53, 1, 'VIP', 1, NULL, NULL, NULL, '2025-12-15 02:32:48', '2025-12-15 02:32:48'),
(264, 53, 2, 'High', 1, NULL, NULL, NULL, '2025-12-15 02:32:49', '2025-12-15 02:32:49'),
(265, 53, 3, 'Medium', 1, NULL, NULL, NULL, '2025-12-15 02:32:49', '2025-12-15 02:32:49'),
(266, 53, 4, 'Low', 1, NULL, NULL, NULL, '2025-12-15 02:32:49', '2025-12-15 02:32:49'),
(267, 53, 5, 'Warm', 1, NULL, NULL, NULL, '2025-12-15 02:32:49', '2025-12-15 02:32:49'),
(268, 24, 3, 'Inactive', 1, NULL, NULL, NULL, '2025-12-15 02:51:47', '2025-12-15 02:51:47'),
(269, 24, 4, 'Suspended', 1, NULL, NULL, NULL, '2025-12-15 02:51:47', '2025-12-15 02:51:47'),
(270, 24, 5, 'Pending', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(271, 24, 6, 'Dormant', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(272, 58, 1, 'Victoria', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(273, 58, 2, 'Beau Vallon', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(274, 58, 3, 'Mont Fleuri', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(275, 58, 4, 'Cascade', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(276, 58, 5, 'Providence', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(277, 58, 6, 'Grand Anse', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(278, 58, 7, 'Anse Aux Pins', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(279, 59, 1, 'Accountant', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(280, 59, 2, 'Driver', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(281, 59, 3, 'Customer Service Officer', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(282, 59, 4, 'Real Estate Agent', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(283, 59, 5, 'Rock Breaker', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(284, 59, 6, 'Payroll Officer', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(285, 59, 7, 'Boat Charter', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(286, 59, 8, 'Contractor', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(287, 59, 9, 'Technician', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(288, 59, 10, 'Paymaster', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(289, 59, 11, 'Human Resources Manager', 1, NULL, NULL, NULL, '2025-12-15 02:51:48', '2025-12-15 02:51:48'),
(290, 24, 7, 'Inactive', 1, NULL, NULL, NULL, '2025-12-15 04:06:37', '2025-12-15 04:06:37'),
(291, 24, 8, 'Suspended', 1, NULL, NULL, NULL, '2025-12-15 04:06:37', '2025-12-15 04:06:37'),
(292, 24, 9, 'Pending', 1, NULL, NULL, NULL, '2025-12-15 04:06:38', '2025-12-15 04:06:38'),
(293, 24, 10, 'Dormant', 1, NULL, NULL, NULL, '2025-12-15 04:06:38', '2025-12-15 04:06:38'),
(294, 60, 1, 'Policy Document', 1, NULL, NULL, NULL, '2025-12-15 04:06:38', '2025-12-15 04:06:38'),
(295, 60, 2, 'Certificate', 1, NULL, NULL, NULL, '2025-12-15 04:06:38', '2025-12-15 04:06:38'),
(296, 60, 3, 'Claim Document', 1, NULL, NULL, NULL, '2025-12-15 04:06:38', '2025-12-15 04:06:38'),
(297, 60, 4, 'Other Document', 1, NULL, NULL, NULL, '2025-12-15 04:06:38', '2025-12-15 04:06:38'),
(298, 24, 11, 'Inactive', 1, NULL, NULL, NULL, '2025-12-15 06:42:41', '2025-12-15 06:42:41'),
(299, 24, 12, 'Suspended', 1, NULL, NULL, NULL, '2025-12-15 06:42:41', '2025-12-15 06:42:41'),
(300, 24, 13, 'Pending', 1, NULL, NULL, NULL, '2025-12-15 06:42:41', '2025-12-15 06:42:41'),
(301, 24, 14, 'Dormant', 1, NULL, NULL, NULL, '2025-12-15 06:42:41', '2025-12-15 06:42:41'),
(302, 37, 7, 'General', 1, NULL, NULL, NULL, '2025-12-15 06:42:41', '2025-12-15 06:42:41'),
(303, 37, 8, 'Commission', 1, NULL, NULL, NULL, '2025-12-15 06:42:41', '2025-12-15 06:42:41'),
(304, 37, 9, 'Bonus', 1, NULL, NULL, NULL, '2025-12-15 06:42:41', '2025-12-15 06:42:41'),
(305, 37, 10, 'Salary', 1, NULL, NULL, NULL, '2025-12-15 06:42:41', '2025-12-15 06:42:41'),
(306, 37, 11, 'Investment', 1, NULL, NULL, NULL, '2025-12-15 06:42:41', '2025-12-15 06:42:41'),
(307, 37, 12, 'Rentals', 1, NULL, NULL, NULL, '2025-12-15 06:42:41', '2025-12-15 06:42:41'),
(308, 37, 13, 'Other', 1, NULL, NULL, NULL, '2025-12-15 06:42:42', '2025-12-15 06:42:42'),
(309, 24, 15, 'Inactive', 1, NULL, NULL, NULL, '2025-12-22 05:56:00', '2025-12-22 05:56:00'),
(310, 24, 16, 'Suspended', 1, NULL, NULL, NULL, '2025-12-22 05:56:00', '2025-12-22 05:56:00'),
(311, 24, 17, 'Pending', 1, NULL, NULL, NULL, '2025-12-22 05:56:00', '2025-12-22 05:56:00'),
(312, 24, 18, 'Dormant', 1, NULL, NULL, NULL, '2025-12-22 05:56:00', '2025-12-22 05:56:00'),
(313, 37, 1, 'General', 1, NULL, NULL, NULL, '2025-12-22 05:56:00', '2025-12-22 05:56:00'),
(314, 37, 2, 'Commission', 1, NULL, NULL, NULL, '2025-12-22 05:56:00', '2025-12-22 05:56:00'),
(315, 37, 3, 'Bonus', 1, NULL, NULL, NULL, '2025-12-22 05:56:00', '2025-12-22 05:56:00'),
(316, 37, 4, 'Salary', 1, NULL, NULL, NULL, '2025-12-22 05:56:01', '2025-12-22 05:56:01'),
(317, 37, 5, 'Investment', 1, NULL, NULL, NULL, '2025-12-22 05:56:01', '2025-12-22 05:56:01'),
(318, 37, 6, 'Rentals', 1, NULL, NULL, NULL, '2025-12-22 05:56:01', '2025-12-22 05:56:01'),
(319, 37, 14, 'Other', 1, NULL, NULL, NULL, '2025-12-22 05:56:01', '2025-12-22 05:56:01'),
(320, 62, 1, 'Renewel', 1, 'Renewel', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `medicals`
--

DROP TABLE IF EXISTS `medicals`;
CREATE TABLE IF NOT EXISTS `medicals` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `life_proposal_id` bigint UNSIGNED NOT NULL,
  `medical_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `medical_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordered_on` date DEFAULT NULL,
  `completed_on` date DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `results_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `medicals_medical_code_unique` (`medical_code`),
  KEY `medicals_life_proposal_id_foreign` (`life_proposal_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(11, '0001_01_01_000000_create_users_table', 1),
(12, '0001_01_01_000001_create_cache_table', 1),
(13, '0001_01_01_000002_create_jobs_table', 1),
(14, '2025_10_05_171758_create_tasks_table', 1),
(15, '2025_10_11_084446_create_lookup_tables', 1),
(16, '2025_10_15_101405_create_policies_table', 1),
(17, '2025_10_19_112711_create_contacts_table', 1),
(18, '2025_10_19_153256_create_clients_table', 1),
(19, '2025_10_19_184103_create_life_proposals_table', 1),
(21, '2025_11_09_125623_create_documents_table', 2),
(22, '2025_11_09_141513_create_vehicles_table', 2),
(23, '2025_11_09_145357_create_claims_table', 2),
(24, '2025_11_09_152705_create_incomes_table', 2),
(26, '2025_11_09_180836_create_statements_table', 2),
(27, '2025_11_18_112128_create_beneficial_owners_table', 3),
(28, '2025_11_18_112148_create_nominees_table', 3),
(29, '2025_11_18_112215_create_renewal_notices_table', 3),
(30, '2025_11_18_112236_create_schedules_table', 3),
(31, '2025_11_18_112248_create_payment_plans_table', 3),
(32, '2025_11_18_112305_create_debit_notes_table', 3),
(33, '2025_11_18_112314_create_payments_table', 3),
(35, '2025_11_18_112341_create_followups_table', 3),
(36, '2025_11_18_112352_create_medicals_table', 3),
(37, '2025_11_18_112401_create_commission_notes_table', 3),
(38, '2025_11_18_112418_create_commission_statements_table', 3),
(39, '2025_11_18_112435_create_tax_returns_table', 3),
(41, '2025_11_18_112445_add_note_and_statement_refs_to_commissions_table', 4),
(42, '2025_11_19_100000_add_roles_to_users_table', 5),
(43, '2025_11_19_100100_create_permissions_table', 5),
(44, '2025_11_19_100200_create_audit_logs_table', 5),
(46, '2025_11_19_100300_create_roles_table', 6),
(47, '2025_11_27_101333_update_role_permissions_table_to_use_role_id', 7),
(50, '2025_11_27_110318_add_encryption_flags_to_tables', 8),
(51, '2025_11_27_111755_add_client_id_to_policies_table', 8),
(52, '2025_12_02_085132_add_document_columns_to_clients_table', 9),
(53, '2025_12_02_090031_add_additional_fields_to_clients_table', 10),
(54, '2025_12_02_091316_remove_document_columns_from_clients_table', 11),
(56, '2025_12_09_093600_add_policy_status_id_to_policies_table', 12),
(57, '2025_12_10_041500_make_term_unit_nullable_in_policies_table', 13),
(58, '2025_12_10_042000_add_wsc_lou_pa_to_policies_table', 13),
(59, '2025_12_10_043000_add_last_endorsement_cancelled_date_to_policies_table', 14),
(60, '2025_12_10_120000_remove_redundant_fields_from_policies_table', 15),
(61, '2025_12_11_095000_add_nin_passport_no_to_nominees_table', 16),
(62, '2025_12_11_100000_add_date_removed_to_nominees_table', 17),
(63, '2025_12_11_101000_make_policy_id_nullable_in_nominees_table', 18),
(64, '2025_12_13_012248_add_item_to_tasks_table', 19),
(65, '2025_12_13_015306_add_wa_to_contacts_table', 19),
(66, '2025_12_13_024706_add_comprehensive_fields_to_life_proposals_table', 19),
(67, '2025_12_13_030232_add_additional_fields_to_life_proposals_table', 19),
(68, '2025_12_15_063841_update_claims_table_add_policy_id_foreign_key', 20),
(69, '2025_12_15_064602_add_claim_stage_to_claims_table', 21),
(70, '2025_12_15_065910_remove_client_name_from_claims_table', 22),
(71, '2025_12_15_071004_add_receipt_no_to_expenses_table', 22),
(72, '2025_12_15_072345_remove_receipt_path_from_expenses_table', 23),
(73, '2025_12_15_072351_remove_document_path_from_incomes_table', 23),
(74, '2025_12_15_073025_add_category_id_to_incomes_table', 24),
(75, '2025_10_19_200529_create_expenses_table', 25),
(77, '2025_12_15_105446_add_missing_columns_to_expenses_table', 26),
(78, '2025_11_18_112323_create_endorsements_table', 27),
(79, '2025_12_20_055847_add_columns_to_beneficial_owners_table', 27),
(80, '2025_11_09_163024_create_commissions_table', 28);

-- --------------------------------------------------------

--
-- Table structure for table `nominees`
--

DROP TABLE IF EXISTS `nominees`;
CREATE TABLE IF NOT EXISTS `nominees` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nominee_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_id` bigint UNSIGNED DEFAULT NULL,
  `client_id` bigint UNSIGNED DEFAULT NULL,
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
  KEY `nominees_client_id_foreign` (`client_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nominees`
--

INSERT INTO `nominees` (`id`, `nominee_code`, `policy_id`, `client_id`, `full_name`, `relationship`, `share_percentage`, `date_of_birth`, `date_removed`, `nin_passport_no`, `id_document_path`, `notes`, `created_at`, `updated_at`) VALUES
(3, 'NM1003', NULL, NULL, '12312', '123', 1.00, '2026-01-01', '2025-12-12', '123', NULL, 'asd', '2025-12-11 05:06:58', '2025-12-11 05:35:44'),
(7, 'NM1007', NULL, NULL, 'Usman', 'Son', 10.00, '2024-12-31', '2025-12-27', '1212121', NULL, 'new', '2025-12-11 06:00:38', '2025-12-11 06:00:54'),
(6, 'NM1006', NULL, NULL, 'asd12312', '12', NULL, '2025-12-19', NULL, '123', NULL, 'asdas', '2025-12-11 05:10:22', '2025-12-11 05:13:32');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `debit_note_id` bigint UNSIGNED NOT NULL,
  `payment_reference` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_on` date DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `mode_of_payment_id` bigint UNSIGNED DEFAULT NULL,
  `receipt_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_payment_reference_unique` (`payment_reference`),
  KEY `payments_debit_note_id_foreign` (`debit_note_id`),
  KEY `payments_mode_of_payment_id_foreign` (`mode_of_payment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `debit_note_id`, `payment_reference`, `paid_on`, `amount`, `mode_of_payment_id`, `receipt_path`, `is_encrypted`, `notes`, `created_at`, `updated_at`) VALUES
(4, 10, '123', '2025-12-10', 10000.00, 162, 'receipts/b4fec59699f1f34e635f99cdb282589f8dc92223179183709411c14c9116f331.enc', 1, NULL, '2025-12-29 02:14:03', '2025-12-29 02:14:03');

-- --------------------------------------------------------

--
-- Table structure for table `payment_plans`
--

DROP TABLE IF EXISTS `payment_plans`;
CREATE TABLE IF NOT EXISTS `payment_plans` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `schedule_id` bigint UNSIGNED NOT NULL,
  `installment_label` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `frequency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_plans_schedule_id_foreign` (`schedule_id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_plans`
--

INSERT INTO `payment_plans` (`id`, `schedule_id`, `installment_label`, `due_date`, `amount`, `frequency`, `status`, `created_at`, `updated_at`) VALUES
(29, 9, 'Instalment 1 of 2', '2025-12-01', 500000.00, 'year', 'pending', '2025-12-29 02:13:08', '2025-12-29 02:13:08');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`),
  UNIQUE KEY `permissions_slug_unique` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `description`, `module`, `created_at`, `updated_at`) VALUES
(1, 'View Policies', 'policies.view', NULL, 'policies', '2025-11-27 04:37:45', '2025-11-27 04:37:45'),
(2, 'Create Policies', 'policies.create', NULL, 'policies', '2025-11-27 04:37:45', '2025-11-27 04:37:45'),
(3, 'Edit Policies', 'policies.edit', NULL, 'policies', '2025-11-27 04:37:45', '2025-11-27 04:37:45'),
(4, 'Delete Policies', 'policies.delete', NULL, 'policies', '2025-11-27 04:37:45', '2025-11-27 04:37:45'),
(5, 'View Clients', 'clients.view', NULL, 'clients', '2025-11-27 04:37:45', '2025-11-27 04:37:45'),
(6, 'Create Clients', 'clients.create', NULL, 'clients', '2025-11-27 04:37:45', '2025-11-27 04:37:45'),
(7, 'Edit Clients', 'clients.edit', NULL, 'clients', '2025-11-27 04:37:45', '2025-11-27 04:37:45'),
(8, 'Delete Clients', 'clients.delete', NULL, 'clients', '2025-11-27 04:37:45', '2025-11-27 04:37:45'),
(9, 'View Users', 'users.view', NULL, 'users', '2025-11-27 04:37:45', '2025-11-27 04:37:45'),
(10, 'Create Users', 'users.create', NULL, 'users', '2025-11-27 04:37:45', '2025-11-27 04:37:45'),
(11, 'Edit Users', 'users.edit', NULL, 'users', '2025-11-27 04:37:45', '2025-11-27 04:37:45'),
(12, 'Delete Users', 'users.delete', NULL, 'users', '2025-11-27 04:37:45', '2025-11-27 04:37:45'),
(13, 'View Reports', 'reports.view', NULL, 'reports', '2025-11-27 04:37:45', '2025-11-27 04:37:45'),
(14, 'Export Reports', 'reports.export', NULL, 'reports', '2025-11-27 04:37:45', '2025-11-27 04:37:45'),
(15, 'Manage Settings', 'settings.manage', NULL, 'settings', '2025-11-27 04:37:45', '2025-11-27 04:37:45'),
(16, 'contacts Create', 'contacts.create', 'contacts', 'contacts', '2025-12-03 23:46:36', '2025-12-03 23:46:36'),
(17, 'View Dashboard', 'dashboard.view', NULL, 'dashboard', '2025-12-15 02:43:30', '2025-12-15 02:43:30'),
(18, 'View Contacts', 'contacts.view', NULL, 'contacts', '2025-12-15 02:43:30', '2025-12-15 02:43:30'),
(19, 'Edit Contacts', 'contacts.edit', NULL, 'contacts', '2025-12-15 02:43:30', '2025-12-15 02:43:30'),
(20, 'Delete Contacts', 'contacts.delete', NULL, 'contacts', '2025-12-15 02:43:30', '2025-12-15 02:43:30'),
(21, 'View Life Proposals', 'life-proposals.view', NULL, 'life-proposals', '2025-12-15 02:43:30', '2025-12-15 02:43:30'),
(22, 'Create Life Proposals', 'life-proposals.create', NULL, 'life-proposals', '2025-12-15 02:43:30', '2025-12-15 02:43:30'),
(23, 'Edit Life Proposals', 'life-proposals.edit', NULL, 'life-proposals', '2025-12-15 02:43:30', '2025-12-15 02:43:30'),
(24, 'Delete Life Proposals', 'life-proposals.delete', NULL, 'life-proposals', '2025-12-15 02:43:30', '2025-12-15 02:43:30'),
(25, 'View Claims', 'claims.view', NULL, 'claims', '2025-12-15 02:43:30', '2025-12-15 02:43:30'),
(26, 'Create Claims', 'claims.create', NULL, 'claims', '2025-12-15 02:43:30', '2025-12-15 02:43:30'),
(27, 'Edit Claims', 'claims.edit', NULL, 'claims', '2025-12-15 02:43:30', '2025-12-15 02:43:30'),
(28, 'Delete Claims', 'claims.delete', NULL, 'claims', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(29, 'View Expenses', 'expenses.view', NULL, 'expenses', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(30, 'Create Expenses', 'expenses.create', NULL, 'expenses', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(31, 'Edit Expenses', 'expenses.edit', NULL, 'expenses', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(32, 'Delete Expenses', 'expenses.delete', NULL, 'expenses', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(33, 'View Incomes', 'incomes.view', NULL, 'incomes', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(34, 'Create Incomes', 'incomes.create', NULL, 'incomes', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(35, 'Edit Incomes', 'incomes.edit', NULL, 'incomes', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(36, 'Delete Incomes', 'incomes.delete', NULL, 'incomes', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(37, 'View Commissions', 'commissions.view', NULL, 'commissions', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(38, 'Create Commissions', 'commissions.create', NULL, 'commissions', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(39, 'Edit Commissions', 'commissions.edit', NULL, 'commissions', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(40, 'Delete Commissions', 'commissions.delete', NULL, 'commissions', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(41, 'View Statements', 'statements.view', NULL, 'statements', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(42, 'Create Statements', 'statements.create', NULL, 'statements', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(43, 'Edit Statements', 'statements.edit', NULL, 'statements', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(44, 'Delete Statements', 'statements.delete', NULL, 'statements', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(45, 'View Payments', 'payments.view', NULL, 'payments', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(46, 'Create Payments', 'payments.create', NULL, 'payments', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(47, 'Edit Payments', 'payments.edit', NULL, 'payments', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(48, 'Delete Payments', 'payments.delete', NULL, 'payments', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(49, 'View Payment Plans', 'payment-plans.view', NULL, 'payment-plans', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(50, 'Create Payment Plans', 'payment-plans.create', NULL, 'payment-plans', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(51, 'Edit Payment Plans', 'payment-plans.edit', NULL, 'payment-plans', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(52, 'Delete Payment Plans', 'payment-plans.delete', NULL, 'payment-plans', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(53, 'View Debit Notes', 'debit-notes.view', NULL, 'debit-notes', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(54, 'Create Debit Notes', 'debit-notes.create', NULL, 'debit-notes', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(55, 'Edit Debit Notes', 'debit-notes.edit', NULL, 'debit-notes', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(56, 'Delete Debit Notes', 'debit-notes.delete', NULL, 'debit-notes', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(57, 'View Vehicles', 'vehicles.view', NULL, 'vehicles', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(58, 'Create Vehicles', 'vehicles.create', NULL, 'vehicles', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(59, 'Edit Vehicles', 'vehicles.edit', NULL, 'vehicles', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(60, 'Delete Vehicles', 'vehicles.delete', NULL, 'vehicles', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(61, 'View Documents', 'documents.view', NULL, 'documents', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(62, 'Create Documents', 'documents.create', NULL, 'documents', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(63, 'Edit Documents', 'documents.edit', NULL, 'documents', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(64, 'Delete Documents', 'documents.delete', NULL, 'documents', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(65, 'View Tasks', 'tasks.view', NULL, 'tasks', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(66, 'Create Tasks', 'tasks.create', NULL, 'tasks', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(67, 'Edit Tasks', 'tasks.edit', NULL, 'tasks', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(68, 'Delete Tasks', 'tasks.delete', NULL, 'tasks', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(69, 'View Schedules', 'schedules.view', NULL, 'schedules', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(70, 'Create Schedules', 'schedules.create', NULL, 'schedules', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(71, 'Edit Schedules', 'schedules.edit', NULL, 'schedules', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(72, 'Delete Schedules', 'schedules.delete', NULL, 'schedules', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(73, 'View Calendar', 'calendar.view', NULL, 'calendar', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(74, 'View Nominees', 'nominees.view', NULL, 'nominees', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(75, 'Create Nominees', 'nominees.create', NULL, 'nominees', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(76, 'Edit Nominees', 'nominees.edit', NULL, 'nominees', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(77, 'Delete Nominees', 'nominees.delete', NULL, 'nominees', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(78, 'View Roles', 'roles.view', NULL, 'roles', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(79, 'Create Roles', 'roles.create', NULL, 'roles', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(80, 'Edit Roles', 'roles.edit', NULL, 'roles', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(81, 'Delete Roles', 'roles.delete', NULL, 'roles', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(82, 'View Permissions', 'permissions.view', NULL, 'permissions', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(83, 'Create Permissions', 'permissions.create', NULL, 'permissions', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(84, 'Edit Permissions', 'permissions.edit', NULL, 'permissions', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(85, 'Delete Permissions', 'permissions.delete', NULL, 'permissions', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(86, 'View Audit Logs', 'audit-logs.view', NULL, 'audit-logs', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(87, 'View Lookups', 'lookups.view', NULL, 'lookups', '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(88, 'Manage Lookups', 'lookups.manage', NULL, 'lookups', '2025-12-15 02:43:31', '2025-12-15 02:43:31');

-- --------------------------------------------------------

--
-- Table structure for table `policies`
--

DROP TABLE IF EXISTS `policies`;
CREATE TABLE IF NOT EXISTS `policies` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` bigint UNSIGNED DEFAULT NULL,
  `policy_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurer_id` bigint UNSIGNED DEFAULT NULL,
  `policy_class_id` bigint UNSIGNED DEFAULT NULL,
  `policy_plan_id` bigint UNSIGNED DEFAULT NULL,
  `sum_insured` decimal(15,2) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `cancelled_date` date DEFAULT NULL,
  `insured` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_registered` date NOT NULL,
  `insured_item` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `policy_status_id` bigint UNSIGNED DEFAULT NULL,
  `renewable` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_type_id` bigint UNSIGNED DEFAULT NULL,
  `term` int NOT NULL,
  `term_unit` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `base_premium` decimal(10,2) NOT NULL,
  `premium` decimal(10,2) NOT NULL,
  `wsc` decimal(15,2) DEFAULT NULL,
  `lou` decimal(15,2) DEFAULT NULL,
  `pa` decimal(15,2) DEFAULT NULL,
  `frequency_id` bigint UNSIGNED DEFAULT NULL,
  `pay_plan_lookup_id` bigint UNSIGNED DEFAULT NULL,
  `agency_id` bigint UNSIGNED DEFAULT NULL,
  `channel_id` bigint UNSIGNED DEFAULT NULL,
  `agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `last_endorsement` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `policies_policy_code_unique` (`policy_code`),
  KEY `policies_client_id_foreign` (`client_id`),
  KEY `policies_policy_status_id_foreign` (`policy_status_id`),
  KEY `policies_insurer_id_foreign` (`insurer_id`),
  KEY `policies_policy_class_id_foreign` (`policy_class_id`),
  KEY `policies_policy_plan_id_foreign` (`policy_plan_id`),
  KEY `policies_business_type_id_foreign` (`business_type_id`),
  KEY `policies_frequency_id_foreign` (`frequency_id`),
  KEY `policies_pay_plan_lookup_id_foreign` (`pay_plan_lookup_id`),
  KEY `policies_agency_id_foreign` (`agency_id`),
  KEY `policies_channel_id_foreign` (`channel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `policies`
--

INSERT INTO `policies` (`id`, `client_id`, `policy_no`, `policy_code`, `insurer_id`, `policy_class_id`, `policy_plan_id`, `sum_insured`, `start_date`, `end_date`, `cancelled_date`, `insured`, `date_registered`, `insured_item`, `policy_status_id`, `renewable`, `business_type_id`, `term`, `term_unit`, `base_premium`, `premium`, `wsc`, `lou`, `pa`, `frequency_id`, `pay_plan_lookup_id`, `agency_id`, `channel_id`, `agent`, `notes`, `last_endorsement`, `created_at`, `updated_at`) VALUES
(68, 22, '123', 'POL000001', 1, 148, 12, 1000.00, '2025-12-01', '2025-12-31', NULL, NULL, '2025-12-02', '123', NULL, '0', 216, 1, '29', 1000.00, 1000000.00, 10000.00, 15000.00, 250000.00, 70, 73, 118, 258, '123', 'asdfgh', NULL, '2025-12-29 00:15:16', '2025-12-29 02:13:08');

-- --------------------------------------------------------

--
-- Table structure for table `renewal_notices`
--

DROP TABLE IF EXISTS `renewal_notices`;
CREATE TABLE IF NOT EXISTS `renewal_notices` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `policy_id` bigint UNSIGNED NOT NULL,
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
  KEY `renewal_notices_policy_id_foreign` (`policy_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`),
  UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin', 'Full system access', 1, '2025-11-27 05:02:29', '2025-11-27 05:02:29'),
(2, 'Support', 'support', 'Support staff access', 0, '2025-11-27 05:02:29', '2025-11-27 05:02:29'),
(3, 'Manager', 'manager', NULL, 0, '2025-11-27 05:12:16', '2025-11-27 05:12:16'),
(4, 'super_admin', 'superadmin', 'super_admin', 0, '2025-12-03 23:42:32', '2025-12-03 23:42:32'),
(5, 'cacher', 'cacher', NULL, 0, '2025-12-03 23:47:43', '2025-12-03 23:47:43'),
(6, 'recovery', 'recovery', NULL, 0, '2025-12-03 23:47:54', '2025-12-03 23:47:54'),
(7, 'marketing', 'marketing', NULL, 0, '2025-12-03 23:48:30', '2025-12-03 23:48:30'),
(8, 'sales', 'sales', NULL, 0, '2025-12-03 23:48:38', '2025-12-03 23:48:38');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` bigint UNSIGNED NOT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permission_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  KEY `role_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `role`, `permission_id`, `created_at`, `updated_at`) VALUES
(1, 0, 'admin', 1, '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(2, 0, 'admin', 2, '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(3, 0, 'admin', 3, '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(4, 0, 'admin', 4, '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(5, 0, 'admin', 5, '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(6, 0, 'admin', 6, '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(7, 0, 'admin', 7, '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(8, 0, 'admin', 8, '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(9, 0, 'admin', 9, '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(10, 0, 'admin', 10, '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(11, 0, 'admin', 11, '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(12, 0, 'admin', 12, '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(13, 0, 'admin', 13, '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(14, 0, 'admin', 14, '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(15, 0, 'admin', 15, '2025-12-15 02:43:31', '2025-12-15 02:43:31'),
(16, 0, 'support', 1, '2025-11-27 04:38:45', '2025-11-27 04:38:45'),
(17, 0, 'support', 5, '2025-11-27 04:38:45', '2025-11-27 04:38:45'),
(18, 0, 'support', 9, '2025-11-27 04:38:45', '2025-11-27 04:38:45'),
(19, 0, 'support', 13, '2025-11-27 04:38:45', '2025-11-27 04:38:45'),
(20, 1, 'admin', 10, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(21, 1, 'admin', 12, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(22, 1, 'admin', 11, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(23, 1, 'admin', 9, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(24, 1, 'admin', 2, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(25, 1, 'admin', 4, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(26, 1, 'admin', 3, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(27, 1, 'admin', 14, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(28, 1, 'admin', 13, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(29, 1, 'admin', 1, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(30, 1, 'admin', 5, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(31, 1, 'admin', 6, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(32, 1, 'admin', 7, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(33, 1, 'admin', 8, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(34, 1, 'admin', 15, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(35, 1, 'admin', 16, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(36, 1, 'admin', 17, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(37, 1, 'admin', 18, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(38, 1, 'admin', 19, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(39, 1, 'admin', 20, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(40, 1, 'admin', 21, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(41, 1, 'admin', 22, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(42, 1, 'admin', 23, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(43, 1, 'admin', 24, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(44, 1, 'admin', 25, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(45, 1, 'admin', 26, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(46, 1, 'admin', 27, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(47, 1, 'admin', 28, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(48, 1, 'admin', 29, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(49, 1, 'admin', 30, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(50, 1, 'admin', 31, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(51, 1, 'admin', 32, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(52, 1, 'admin', 33, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(53, 1, 'admin', 34, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(54, 1, 'admin', 35, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(55, 1, 'admin', 36, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(56, 1, 'admin', 37, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(57, 1, 'admin', 38, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(58, 1, 'admin', 39, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(59, 1, 'admin', 40, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(60, 1, 'admin', 41, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(61, 1, 'admin', 42, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(62, 1, 'admin', 43, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(63, 1, 'admin', 44, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(64, 1, 'admin', 45, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(65, 1, 'admin', 46, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(66, 1, 'admin', 47, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(67, 1, 'admin', 48, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(68, 1, 'admin', 49, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(69, 1, 'admin', 50, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(70, 1, 'admin', 51, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(71, 1, 'admin', 52, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(72, 1, 'admin', 53, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(73, 1, 'admin', 54, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(74, 1, 'admin', 55, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(75, 1, 'admin', 56, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(76, 1, 'admin', 57, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(77, 1, 'admin', 58, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(78, 1, 'admin', 59, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(79, 1, 'admin', 60, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(80, 1, 'admin', 61, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(81, 1, 'admin', 62, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(82, 1, 'admin', 63, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(83, 1, 'admin', 64, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(84, 1, 'admin', 65, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(85, 1, 'admin', 66, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(86, 1, 'admin', 67, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(87, 1, 'admin', 68, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(88, 1, 'admin', 69, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(89, 1, 'admin', 70, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(90, 1, 'admin', 71, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(91, 1, 'admin', 72, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(92, 1, 'admin', 73, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(93, 1, 'admin', 74, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(94, 1, 'admin', 75, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(95, 1, 'admin', 76, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(96, 1, 'admin', 77, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(97, 1, 'admin', 78, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(98, 1, 'admin', 79, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(99, 1, 'admin', 80, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(100, 1, 'admin', 81, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(101, 1, 'admin', 82, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(102, 1, 'admin', 83, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(103, 1, 'admin', 84, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(104, 1, 'admin', 85, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(105, 1, 'admin', 86, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(106, 1, 'admin', 87, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(107, 1, 'admin', 88, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(108, 2, 'support', 1, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(109, 2, 'support', 5, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(110, 2, 'support', 9, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(111, 2, 'support', 13, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(112, 2, 'support', 17, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(113, 2, 'support', 18, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(114, 2, 'support', 21, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(115, 2, 'support', 25, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(116, 2, 'support', 29, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(117, 2, 'support', 33, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(118, 2, 'support', 37, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(119, 2, 'support', 41, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(120, 2, 'support', 45, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(121, 2, 'support', 49, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(122, 2, 'support', 53, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(123, 2, 'support', 57, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(124, 2, 'support', 61, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(125, 2, 'support', 65, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(126, 2, 'support', 69, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(127, 2, 'support', 73, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(128, 2, 'support', 74, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(129, 2, 'support', 78, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(130, 2, 'support', 82, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(131, 2, 'support', 86, '2025-12-15 02:43:58', '2025-12-15 02:43:58'),
(132, 2, 'support', 87, '2025-12-15 02:43:58', '2025-12-15 02:43:58');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

DROP TABLE IF EXISTS `schedules`;
CREATE TABLE IF NOT EXISTS `schedules` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `policy_id` bigint UNSIGNED NOT NULL,
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
  KEY `schedules_policy_id_foreign` (`policy_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `policy_id`, `schedule_no`, `issued_on`, `effective_from`, `effective_to`, `status`, `debit_note_path`, `receipt_path`, `policy_schedule_path`, `renewal_notice_path`, `payment_agreement_path`, `notes`, `created_at`, `updated_at`) VALUES
(1, 5, '1', '2025-11-29', '2025-11-29', '2026-12-28', 'active', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-27 05:40:04', '2025-11-27 05:40:04'),
(2, 25, 'SCH000002', '2025-12-10', '2025-12-18', '2025-12-12', 'active', NULL, NULL, NULL, NULL, NULL, 'Auto-generated from policy creation', '2025-12-10 07:22:34', '2025-12-10 07:22:34'),
(3, 26, 'SCH000003', '2025-12-10', '2025-12-17', '2025-12-12', 'active', NULL, NULL, NULL, NULL, NULL, 'Auto-generated from policy creation', '2025-12-10 07:26:07', '2025-12-10 07:26:07'),
(4, 27, 'SCH000004', '2025-12-11', '2025-12-04', '2025-12-18', 'active', NULL, NULL, NULL, NULL, NULL, 'Auto-generated from policy creation', '2025-12-10 07:41:48', '2025-12-10 07:41:48'),
(5, 27, 'SCH000005', '2025-12-10', '2026-12-04', '2026-12-18', 'active', NULL, NULL, NULL, NULL, NULL, 'Renewal Schedule Details:\nYear: 2026\nPolicy Plan: Comprehensive\nSum Insured: 1,000,000.00\nTerm: 1 Year\nAdd Ons: 123\nBase Premium: 1,000.00\nFull Premium: 1,000,000.00\nPay Plan Type: Single\nNOP: 1\nFrequency: year\nNote: 12312', '2025-12-10 08:02:39', '2025-12-10 08:02:39'),
(6, 28, 'SCH000006', '2025-12-11', '2025-12-11', '2026-05-11', 'active', NULL, NULL, NULL, NULL, NULL, 'Auto-generated from policy creation', '2025-12-10 08:15:57', '2025-12-10 08:15:57'),
(7, 27, 'SCH000007', '2025-12-10', '2026-12-04', '2026-12-18', 'active', NULL, NULL, NULL, NULL, NULL, 'Renewal Schedule Details:\nYear: 2026\nPolicy Plan: Comprehensive\nSum Insured: 1,000,000.00\nTerm: 1 Year\nAdd Ons: bnw e\nBase Premium: 1,000.00\nFull Premium: 1,000,000.00\nPay Plan Type: Single\nNOP: 4\nFrequency: year\nNote: sad', '2025-12-10 08:19:15', '2025-12-10 08:19:15'),
(8, 67, 'SCH000008', '2025-12-01', '2025-12-01', '2025-12-22', 'active', NULL, NULL, NULL, NULL, NULL, 'Auto-generated from policy creation', '2025-12-28 23:10:25', '2025-12-28 23:10:25'),
(9, 68, 'SCH000009', '2025-12-02', '2025-12-01', '2025-12-31', 'active', NULL, NULL, NULL, NULL, NULL, 'Auto-generated from policy creation', '2025-12-29 00:15:16', '2025-12-29 00:15:16');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('W7SRniSOA7HteM6BxvFkvfYLKKPsY6Y1vgJ8Io0q', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 OPR/125.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibU93SlRDOXY3MWhDb2JyeXp6c2lCNUtZWTVHMzcxS25KRUdvOWpNdyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wb2xpY2llcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxODoiY29tbWlzc2lvbl9jb2x1bW5zIjthOjE2OntpOjA7czoxMToicG9saWN5X2NvZGUiO2k6MTtzOjExOiJjbGllbnRfbmFtZSI7aToyO3M6NDoiY25pZCI7aTozO3M6MTM6ImJhc2ljX3ByZW1pdW0iO2k6NDtzOjc6Imluc3VyZXIiO2k6NTtzOjg6Imdyb3VwaW5nIjtpOjY7czo0OiJyYXRlIjtpOjc7czoxMDoiYW1vdW50X2R1ZSI7aTo4O3M6MTQ6InBheW1lbnRfc3RhdHVzIjtpOjk7czoxMToiYW1vdW50X3JjdmQiO2k6MTA7czo5OiJkYXRlX3JjdmQiO2k6MTE7czo4OiJzdGF0ZV9ubyI7aToxMjtzOjE1OiJtb2RlX29mX3BheW1lbnQiO2k6MTM7czo4OiJ2YXJpYW5jZSI7aToxNDtzOjY6InJlYXNvbiI7aToxNTtzOjg6ImRhdGVfZHVlIjt9fQ==', 1766995963);

-- --------------------------------------------------------

--
-- Table structure for table `statements`
--

DROP TABLE IF EXISTS `statements`;
CREATE TABLE IF NOT EXISTS `statements` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `statement_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurer_id` bigint UNSIGNED DEFAULT NULL,
  `business_category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `amount_received` decimal(15,2) DEFAULT NULL,
  `mode_of_payment_id` bigint UNSIGNED DEFAULT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `statements_statement_no_unique` (`statement_no`),
  KEY `statements_insurer_id_foreign` (`insurer_id`),
  KEY `statements_mode_of_payment_id_foreign` (`mode_of_payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `due_date` date NOT NULL,
  `due_time` time DEFAULT NULL,
  `date_in` date DEFAULT NULL,
  `assignee` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `task_status` enum('Not Done','In Progress','Completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Not Done',
  `date_done` date DEFAULT NULL,
  `repeat` tinyint(1) NOT NULL DEFAULT '0',
  `frequency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rpt_date` date DEFAULT NULL,
  `rpt_stop_date` date DEFAULT NULL,
  `task_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tasks_task_id_unique` (`task_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `task_id`, `category`, `item`, `description`, `name`, `contact_no`, `due_date`, `due_time`, `date_in`, `assignee`, `task_status`, `date_done`, `repeat`, `frequency`, `rpt_date`, `rpt_stop_date`, `task_notes`, `created_at`, `updated_at`) VALUES
(13, 'TK24001', '262', 'task add', '123', '8', '03470917748', '2025-12-09', '13:06:00', NULL, '4', 'Not Done', '2026-01-02', 1, '70', '2025-12-03', '2026-01-03', 'new tas', '2025-12-27 03:03:01', '2025-12-27 03:10:36');

-- --------------------------------------------------------

--
-- Table structure for table `tax_returns`
--

DROP TABLE IF EXISTS `tax_returns`;
CREATE TABLE IF NOT EXISTS `tax_returns` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `commission_statement_id` bigint UNSIGNED NOT NULL,
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
  KEY `tax_returns_commission_statement_id_foreign` (`commission_statement_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role_id`, `is_active`, `last_login_at`, `last_login_ip`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@example.com', 1, 1, '2025-12-29 00:26:30', '127.0.0.1', NULL, '$2y$12$.dSd/Wr9Iu0oj2JQ1gxlfuu.eM9GOKBneQ6lGBzs5Z6Xl0dKIiaRW', NULL, '2025-10-21 20:41:17', '2025-12-29 00:26:30'),
(2, 'touqeer', 'atouqeer745@gmail.com', 4, 1, NULL, NULL, NULL, '$2y$12$4pk43T73lWf455rUvh453.khepp7l5M6D8in6Qv55uKSY6dVOznKy', NULL, '2025-12-03 23:43:38', '2025-12-03 23:43:38'),
(4, 'touqeer', 'admin@gmail.com', 1, 1, NULL, NULL, '2025-12-04 04:23:33', '$2y$12$WG1ZVgsmIBc45IuwgWlgZ.WJ8Cgq.CxQZJ/bAq2obUlcuj.uFwkjC', NULL, '2025-12-04 04:23:33', '2025-12-04 04:23:33');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
CREATE TABLE IF NOT EXISTS `vehicles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `vehicle_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `regn_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `make` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `useage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` decimal(15,2) DEFAULT NULL,
  `policy_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chassis_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from` date DEFAULT NULL,
  `to` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicles_vehicle_id_unique` (`vehicle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle_id`, `regn_no`, `make`, `model`, `type`, `useage`, `year`, `value`, `policy_id`, `engine`, `engine_type`, `cc`, `engine_no`, `chassis_no`, `from`, `to`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'VH1001', '1213', '12312', '123', '123', '12312', '123', 123.00, NULL, NULL, '123', '123', '123', '123', NULL, NULL, 'asdasd', '2025-12-11 04:50:09', '2025-12-11 04:50:09'),
(2, 'VH1002', '1213', '12312', '123', '123', '12312', '123', 123.00, NULL, NULL, '123', '123', '123', '123', NULL, NULL, 'asdasd', '2025-12-11 04:50:10', '2025-12-11 04:50:10'),
(3, 'VH1003', '123', '12312', '123', '123', 'asd', '123', 12.00, NULL, NULL, '123', '123', '123', '123', NULL, NULL, NULL, '2025-12-11 05:15:26', '2025-12-11 05:15:26'),
(4, 'VH1004', '123', '123', '123', '123', '123', '123', 123.00, '123', '123', '123', '123', '123', '123', '2025-12-26', '2026-01-08', '12312', '2025-12-11 05:45:08', '2025-12-11 05:45:08'),
(5, 'VH1005', '123', '123', '123', '123', '123', '123', 123.00, '123', '123', '123', '123', '123', '123', '2025-12-02', '2026-01-02', '123', '2025-12-11 05:45:39', '2025-12-11 05:45:39'),
(6, 'VH1006', '12345', 'Honda', '2025', 'SUV', 'new', '2026', 1000000.00, NULL, NULL, 'honda', '2500', '121212', '12121', NULL, NULL, NULL, '2025-12-11 05:59:53', '2025-12-11 05:59:53'),
(7, 'VH1007', '12', '3123', '123', '123', '123', '123', 123.00, NULL, NULL, '123', '123', '123', '123', NULL, NULL, NULL, '2025-12-28 23:09:06', '2025-12-28 23:09:06'),
(8, 'VH1008', '123', '123', '123', '123', '23', '2025', 123.00, '67', '123', '123', '123', '123', '123', '2025-12-01', '2025-12-26', NULL, '2025-12-28 23:17:52', '2025-12-28 23:17:52');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `claims`
--
ALTER TABLE `claims`
  ADD CONSTRAINT `claims_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `claims_policy_id_foreign` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `incomes`
--
ALTER TABLE `incomes`
  ADD CONSTRAINT `incomes_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incomes_income_source_id_foreign` FOREIGN KEY (`income_source_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incomes_mode_of_payment_id_foreign` FOREIGN KEY (`mode_of_payment_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lookup_values`
--
ALTER TABLE `lookup_values`
  ADD CONSTRAINT `lookup_values_lookup_category_id_foreign` FOREIGN KEY (`lookup_category_id`) REFERENCES `lookup_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `policies`
--
ALTER TABLE `policies`
  ADD CONSTRAINT `policies_agency_id_foreign` FOREIGN KEY (`agency_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `policies_business_type_id_foreign` FOREIGN KEY (`business_type_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `policies_channel_id_foreign` FOREIGN KEY (`channel_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `policies_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `policies_frequency_id_foreign` FOREIGN KEY (`frequency_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `policies_insurer_id_foreign` FOREIGN KEY (`insurer_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `policies_pay_plan_lookup_id_foreign` FOREIGN KEY (`pay_plan_lookup_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `policies_policy_class_id_foreign` FOREIGN KEY (`policy_class_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `policies_policy_plan_id_foreign` FOREIGN KEY (`policy_plan_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `policies_policy_status_id_foreign` FOREIGN KEY (`policy_status_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `statements`
--
ALTER TABLE `statements`
  ADD CONSTRAINT `statements_insurer_id_foreign` FOREIGN KEY (`insurer_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `statements_mode_of_payment_id_foreign` FOREIGN KEY (`mode_of_payment_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
