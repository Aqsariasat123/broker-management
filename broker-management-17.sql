-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 17, 2026 at 02:50 PM
-- Server version: 8.0.44-0ubuntu0.24.04.2
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `broker-management`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` bigint UNSIGNED DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `url` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `description`, `old_values`, `new_values`, `ip_address`, `user_agent`, `url`, `method`, `created_at`, `updated_at`) VALUES
(1, NULL, 'login_failed', NULL, NULL, 'Failed login attempt for: admin', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/login', 'POST', '2026-01-09 01:59:53', '2026-01-09 01:59:53'),
(2, NULL, 'login_failed', NULL, NULL, 'Failed login attempt for: admin', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/login', 'POST', '2026-01-09 02:01:19', '2026-01-09 02:01:19'),
(3, NULL, 'login_failed', NULL, NULL, 'Failed login attempt for: webadmin', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/login', 'POST', '2026-01-09 02:04:08', '2026-01-09 02:04:08'),
(4, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/login', 'POST', '2026-01-09 02:04:40', '2026-01-09 02:04:40'),
(5, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/login', 'POST', '2026-01-09 02:07:58', '2026-01-09 02:07:58'),
(6, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-09 06:33:11', '2026-01-09 06:33:11'),
(7, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8002/login', 'POST', '2026-01-09 09:57:02', '2026-01-09 09:57:02'),
(8, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-09 11:42:18', '2026-01-09 11:42:18'),
(9, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-09 11:42:24', '2026-01-09 11:42:24'),
(10, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-09 11:46:17', '2026-01-09 11:46:17'),
(11, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-09 11:46:22', '2026-01-09 11:46:22'),
(12, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-10 06:15:48', '2026-01-10 06:15:48'),
(13, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 00:09:10', '2026-01-11 00:09:10'),
(14, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-11 00:23:38', '2026-01-11 00:23:38'),
(15, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 00:23:45', '2026-01-11 00:23:45'),
(16, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-11 01:10:49', '2026-01-11 01:10:49'),
(17, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 01:10:55', '2026-01-11 01:10:55'),
(18, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-11 01:36:31', '2026-01-11 01:36:31'),
(19, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 01:36:37', '2026-01-11 01:36:37'),
(20, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-11 02:29:49', '2026-01-11 02:29:49'),
(21, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 02:29:55', '2026-01-11 02:29:55'),
(22, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-11 02:51:37', '2026-01-11 02:51:37'),
(23, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 02:51:46', '2026-01-11 02:51:46'),
(24, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 20:51:28', '2026-01-11 20:51:28'),
(25, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-11 21:04:25', '2026-01-11 21:04:25'),
(26, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 21:04:31', '2026-01-11 21:04:31'),
(27, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-11 21:11:03', '2026-01-11 21:11:03'),
(28, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 21:11:09', '2026-01-11 21:11:09'),
(29, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-11 21:17:52', '2026-01-11 21:17:52'),
(30, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 21:17:58', '2026-01-11 21:17:58'),
(31, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-11 21:21:50', '2026-01-11 21:21:50'),
(32, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 21:21:56', '2026-01-11 21:21:56'),
(33, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 21:28:30', '2026-01-11 21:28:30'),
(34, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-11 21:35:28', '2026-01-11 21:35:28'),
(35, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 21:35:34', '2026-01-11 21:35:34'),
(36, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-11 21:40:12', '2026-01-11 21:40:12'),
(37, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 21:40:18', '2026-01-11 21:40:18'),
(38, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-11 21:42:35', '2026-01-11 21:42:35'),
(39, NULL, 'login_failed', NULL, NULL, 'Failed login attempt for: admin', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 21:42:41', '2026-01-11 21:42:41'),
(40, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 21:42:45', '2026-01-11 21:42:45'),
(41, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-11 21:50:15', '2026-01-11 21:50:15'),
(42, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 21:50:20', '2026-01-11 21:50:20'),
(43, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-11 21:57:29', '2026-01-11 21:57:29'),
(44, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 21:57:39', '2026-01-11 21:57:39'),
(45, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-11 21:59:56', '2026-01-11 21:59:56'),
(46, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 22:00:03', '2026-01-11 22:00:03'),
(47, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-11 22:01:59', '2026-01-11 22:01:59'),
(48, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 22:02:05', '2026-01-11 22:02:05'),
(49, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-11 22:51:58', '2026-01-11 22:51:58'),
(50, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-11 22:52:07', '2026-01-11 22:52:07'),
(51, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-12 03:48:23', '2026-01-12 03:48:23'),
(52, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-12 03:48:29', '2026-01-12 03:48:29'),
(53, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-12 06:14:40', '2026-01-12 06:14:40'),
(54, 1, 'logout', NULL, NULL, 'User logged out', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/logout', 'GET', '2026-01-12 09:04:52', '2026-01-12 09:04:52'),
(55, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8001/login', 'POST', '2026-01-12 09:04:59', '2026-01-12 09:04:59'),
(56, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/login', 'POST', '2026-01-13 05:32:36', '2026-01-13 05:32:36'),
(57, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/login', 'POST', '2026-01-14 04:04:43', '2026-01-14 04:04:43'),
(58, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/login', 'POST', '2026-01-14 04:07:21', '2026-01-14 04:07:21'),
(59, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/login', 'POST', '2026-01-15 04:31:04', '2026-01-15 04:31:04'),
(60, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/login', 'POST', '2026-01-15 04:38:12', '2026-01-15 04:38:12'),
(61, 1, 'create', 'App\\Models\\Payment', 9, 'Payment recorded: hgn', NULL, '{\"id\": 9, \"notes\": null, \"amount\": \"4546\", \"paid_on\": \"2025-12-12 00:00:00\", \"created_at\": \"2026-01-15 11:59:43\", \"updated_at\": \"2026-01-15 11:59:43\", \"debit_note_id\": \"3\", \"payment_reference\": \"hgn\", \"mode_of_payment_id\": \"137\"}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/payments', 'POST', '2026-01-15 06:59:43', '2026-01-15 06:59:43'),
(62, 1, 'create', 'App\\Models\\Payment', 10, 'Payment recorded: hgntryt', NULL, '{\"id\": 10, \"notes\": null, \"amount\": \"34444\", \"paid_on\": \"2026-12-12 00:00:00\", \"created_at\": \"2026-01-15 12:23:07\", \"updated_at\": \"2026-01-15 12:23:07\", \"debit_note_id\": \"4\", \"payment_reference\": \"hgntryt\", \"mode_of_payment_id\": \"137\"}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/payments', 'POST', '2026-01-15 07:23:07', '2026-01-15 07:23:07'),
(63, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/login', 'POST', '2026-01-15 17:14:28', '2026-01-15 17:14:28'),
(64, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/login', 'POST', '2026-01-16 05:07:50', '2026-01-16 05:07:50'),
(65, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/login', 'POST', '2026-01-16 13:08:36', '2026-01-16 13:08:36'),
(66, 1, 'login', NULL, NULL, 'User logged in', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/login', 'POST', '2026-01-17 04:44:02', '2026-01-17 04:44:02'),
(67, 1, 'create', 'App\\Models\\DebitNote', 11, 'Debit note created: 43256t44', NULL, '{\"id\": 11, \"amount\": \"344\", \"status\": \"paid\", \"issued_on\": \"2026-12-12 00:00:00\", \"created_at\": \"2026-01-17 10:31:33\", \"updated_at\": \"2026-01-17 10:31:33\", \"debit_note_no\": \"43256t44\", \"payment_plan_id\": \"6\"}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/debit-notes', 'POST', '2026-01-17 05:31:33', '2026-01-17 05:31:33'),
(68, 1, 'create', 'App\\Models\\DebitNote', 12, 'Debit note created: 43256t44444', NULL, '{\"id\": 12, \"amount\": \"4356\", \"status\": \"overdue\", \"issued_on\": \"2026-12-12 00:00:00\", \"created_at\": \"2026-01-17 10:58:47\", \"updated_at\": \"2026-01-17 10:58:47\", \"debit_note_no\": \"43256t44444\", \"payment_plan_id\": \"3\"}', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/debit-notes', 'POST', '2026-01-17 05:58:47', '2026-01-17 05:58:47');

-- --------------------------------------------------------

--
-- Table structure for table `beneficial_owners`
--

CREATE TABLE `beneficial_owners` (
  `id` bigint UNSIGNED NOT NULL,
  `owner_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` bigint UNSIGNED NOT NULL,
  `full_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `nin_passport_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shares` decimal(5,2) DEFAULT NULL,
  `pep` tinyint(1) NOT NULL DEFAULT '0',
  `pep_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date_added` date DEFAULT NULL,
  `removed` tinyint(1) NOT NULL DEFAULT '0',
  `relationship` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ownership_percentage` decimal(5,2) DEFAULT NULL,
  `id_document_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poa_document_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `beneficial_owners`
--

INSERT INTO `beneficial_owners` (`id`, `owner_code`, `client_id`, `full_name`, `dob`, `nin_passport_no`, `country`, `expiry_date`, `status`, `position`, `shares`, `pep`, `pep_details`, `date_added`, `removed`, `relationship`, `ownership_percentage`, `id_document_path`, `poa_document_path`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'BO001', 3, 'James Cornerstone', '1970-05-15', 'SEY123456', 'Seychelles', '2030-05-15', 'Active', 'Director', 60.00, 0, NULL, '2025-01-15', 0, 'Owner', 60.00, NULL, NULL, 'Primary shareholder', '2026-01-13 10:36:04', '2026-01-13 10:36:04'),
(2, 'BO002', 3, 'Helen Cornerstone', '1972-08-22', 'SEY234567', 'Seychelles', '2029-08-22', 'Active', 'Secretary', 40.00, 0, NULL, '2025-01-15', 0, 'Spouse', 40.00, NULL, NULL, 'Co-owner', '2026-01-13 10:36:04', '2026-01-13 10:36:04'),
(3, 'BO003', 7, 'Marcus Beta', '1965-03-10', 'SEY345678', 'Seychelles', '2028-03-10', 'Active', 'CEO', 100.00, 0, NULL, '2025-02-01', 0, 'Owner', 100.00, NULL, NULL, 'Sole proprietor', '2026-01-13 10:36:04', '2026-01-13 10:36:04'),
(4, 'BO004', 4, 'Anna Summers', '1980-11-28', 'SEY456789', 'Seychelles', '2031-11-28', 'Active', 'Owner', 100.00, 0, NULL, '2025-01-20', 0, 'Self', 100.00, NULL, NULL, 'Spa owner', '2026-01-13 10:36:04', '2026-01-13 10:36:04'),
(5, 'BO005', 9, 'Ahmed Ali', '1975-07-04', 'GBR567890', 'Great Britain', '2027-07-04', 'Active', 'Director', 50.00, 1, NULL, '2025-03-01', 0, 'Partner', 50.00, NULL, NULL, 'Foreign investor - PEP', '2026-01-13 10:36:04', '2026-01-13 10:36:04');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

CREATE TABLE `claims` (
  `id` bigint UNSIGNED NOT NULL,
  `claim_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_id` bigint UNSIGNED DEFAULT NULL,
  `client_id` bigint UNSIGNED DEFAULT NULL,
  `vehicle_id` bigint UNSIGNED DEFAULT NULL,
  `loss_date` date DEFAULT NULL,
  `claim_date` date DEFAULT NULL,
  `claim_amount` decimal(15,2) DEFAULT NULL,
  `claim_summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `claim_stage` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `close_date` date DEFAULT NULL,
  `paid_amount` decimal(15,2) DEFAULT NULL,
  `settlment_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `claims`
--

INSERT INTO `claims` (`id`, `claim_id`, `policy_id`, `client_id`, `vehicle_id`, `loss_date`, `claim_date`, `claim_amount`, `claim_summary`, `claim_stage`, `status`, `close_date`, `paid_amount`, `settlment_notes`, `created_at`, `updated_at`) VALUES
(1, 'CLM001', 1, 1, 1, '2024-06-15', '2024-06-17', 25000.00, 'Minor collision at parking lot', 'Awaiting Documents', 'Processing', NULL, NULL, 'Awaiting police report', '2026-01-13 10:37:12', '2026-01-13 10:37:12'),
(2, 'CLM002', 6, 5, 2, '2024-03-20', '2024-03-22', 85000.00, 'Side impact accident on highway', 'Awaiting QS Report', 'Processing', NULL, NULL, 'QS survey scheduled', '2026-01-13 10:37:12', '2026-01-13 10:37:12'),
(3, 'CLM003', 7, 6, 3, '2023-11-10', '2023-11-12', 15000.00, 'Windscreen damage from debris', 'Awaiting Documents', 'Settled', '2023-12-15', 14500.00, 'Claim settled with deduction', '2026-01-13 10:37:12', '2026-01-13 10:37:12'),
(4, 'CLM004', 2, 2, NULL, '2024-08-05', '2024-08-07', 150000.00, 'Water damage from pipe burst', 'Awaiting QS Report', 'Processing', NULL, NULL, 'Plumber report submitted', '2026-01-13 10:37:12', '2026-01-13 10:37:12'),
(5, 'CLM005', 1, 1, 1, '2024-09-28', '2024-09-30', 5000.00, 'Theft of side mirrors', 'Awaiting Documents', 'Processing', NULL, NULL, 'Police case opened', '2026-01-13 10:37:12', '2026-01-13 10:37:12');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` bigint UNSIGNED NOT NULL,
  `client_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nin_bcrn` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob_dor` date DEFAULT NULL,
  `mobile_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `home_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wa` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pc_channel` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `has_vehicle` tinyint(1) NOT NULL DEFAULT '0',
  `has_house` tinyint(1) NOT NULL DEFAULT '0',
  `has_business` tinyint(1) NOT NULL DEFAULT '0',
  `has_boat` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `signed_up` date NOT NULL,
  `agency` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agent` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employer` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `income_source` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monthly_income` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `savings_budget` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `married` tinyint(1) NOT NULL DEFAULT '0',
  `spouses_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `children` int DEFAULT NULL,
  `children_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `alternate_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `island` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `po_box_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pep` tinyint(1) NOT NULL DEFAULT '0',
  `pep_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salutation` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `other_names` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `surname` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `passport_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pic` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `industry` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_expiry_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_document_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  `poa_document_encrypted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `client_name`, `client_type`, `nin_bcrn`, `dob_dor`, `mobile_no`, `contact_no`, `home_no`, `wa`, `district`, `occupation`, `source`, `source_name`, `pc_channel`, `has_vehicle`, `has_house`, `has_business`, `has_boat`, `notes`, `status`, `signed_up`, `agency`, `agent`, `employer`, `clid`, `contact_person`, `income_source`, `monthly_income`, `savings_budget`, `married`, `spouses_name`, `children`, `children_details`, `alternate_no`, `email_address`, `location`, `island`, `country`, `po_box_no`, `pep`, `pep_comment`, `image`, `salutation`, `first_name`, `other_names`, `surname`, `passport_no`, `pic`, `industry`, `id_expiry_date`, `created_at`, `updated_at`, `id_document_encrypted`, `poa_document_encrypted`) VALUES
(1, 'Jean Grey', 'Individual', NULL, NULL, '00000000', NULL, NULL, '0', NULL, 'Engineer', 'Direct', NULL, NULL, 0, 0, 0, 0, NULL, 'Active', '2026-01-09', NULL, NULL, 'ABC Tech Company', 'CL1001', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'jean.grey@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'Jean', NULL, 'Grey', NULL, NULL, NULL, '2031-01-14', '2026-01-09 02:03:22', '2026-01-12 09:57:37', 0, 0),
(2, 'Barbara Walton', 'Individual', NULL, NULL, '00000000', NULL, NULL, '0', NULL, 'Teacher', 'Direct', NULL, NULL, 0, 0, 0, 0, NULL, 'Active', '2026-01-09', NULL, NULL, 'International School', 'CL1002', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'barbara.walton@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'Barbara', NULL, 'Walton', NULL, NULL, NULL, '2031-01-14', '2026-01-09 02:03:22', '2026-01-12 09:57:37', 0, 0),
(3, 'Cornerstone (Pty) Ltd', 'Individual', NULL, NULL, '00000000', NULL, NULL, NULL, NULL, 'Accountant', 'Direct', NULL, NULL, 0, 0, 0, 0, NULL, 'Active', '2026-01-09', NULL, NULL, 'Finance Corp', 'CL1003', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'cornerstone.(pty).ltd@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'Cornerstone', NULL, '(Pty) Ltd', NULL, NULL, NULL, '2031-01-14', '2026-01-09 02:03:22', '2026-01-12 09:57:37', 0, 0),
(4, 'Anna\'s Spa', 'Individual', NULL, NULL, '00000000', NULL, NULL, '0', NULL, NULL, 'Direct', NULL, NULL, 0, 0, 0, 0, NULL, 'Active', '2026-01-09', NULL, NULL, NULL, 'CL1004', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'annas.spa@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'Anna\'s', NULL, 'Spa', NULL, NULL, NULL, '2031-01-14', '2026-01-09 02:03:23', '2026-01-12 06:49:04', 0, 0),
(5, 'Brian Trapper', 'Individual', NULL, NULL, '00000000', NULL, NULL, '0', NULL, NULL, 'Direct', NULL, NULL, 0, 0, 0, 0, NULL, 'Active', '2026-01-09', NULL, NULL, NULL, 'CL1005', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'brian.trapper@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'Brian', NULL, 'Trapper', NULL, NULL, NULL, '2031-01-14', '2026-01-09 02:03:23', '2026-01-12 06:49:05', 0, 0),
(6, 'Adbul Juma', 'Individual', NULL, NULL, '00000000', NULL, NULL, '0', NULL, NULL, 'Direct', NULL, NULL, 0, 0, 0, 0, NULL, 'Active', '2026-01-09', NULL, NULL, NULL, 'CL1006', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'adbul.juma@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'Adbul', NULL, 'Juma', NULL, NULL, NULL, '2031-01-14', '2026-01-09 02:03:23', '2026-01-12 06:49:06', 0, 0),
(7, 'Beta Center', 'Individual', NULL, NULL, '00000000', NULL, NULL, '0', NULL, NULL, 'Direct', NULL, NULL, 0, 0, 0, 0, NULL, 'Active', '2026-01-09', NULL, NULL, NULL, 'CL1007', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'beta.center@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'Beta', NULL, 'Center', NULL, NULL, NULL, '2031-01-14', '2026-01-09 02:03:23', '2026-01-12 06:49:06', 0, 0),
(8, 'Steven Drax', 'Individual', NULL, NULL, '00000000', NULL, NULL, '0', NULL, NULL, 'Direct', NULL, NULL, 0, 0, 0, 0, NULL, 'Active', '2026-01-09', NULL, NULL, NULL, 'CL1008', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'steven.drax@example.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'Steven', NULL, 'Drax', NULL, NULL, NULL, '2031-01-14', '2026-01-09 02:03:23', '2026-01-12 06:49:07', 0, 0),
(9, 'a', 'Business', 'a', NULL, '545028212', NULL, NULL, '0', '232', NULL, '41', 'a', NULL, 0, 0, 0, 0, 's', 'Inactive', '2026-01-13', '79', '89', NULL, 'CL1009', 'a', NULL, NULL, NULL, 0, NULL, NULL, NULL, 'q', 'aqsariasat235@gmail.com', NULL, NULL, '105', 'q', 0, NULL, NULL, NULL, '', '', 'a', NULL, NULL, NULL, '2031-01-14', '2026-01-11 01:14:58', '2026-01-12 06:49:01', 0, 0),
(10, 'sharjeel ahmed nvb vb bv', 'Individual', '435533333', '2026-12-12', '03126658134333', NULL, NULL, '1', '234', '240', '42', NULL, NULL, 1, 0, 1, 0, 'cfdv', 'Pending', '2026-12-12', '79', '90', 'r44', 'CL1010', 'sharjeel ahmed', '196', 'cd dv', NULL, 1, 'dvdf', NULL, NULL, '43245', 'asas@gmail.com', 'sheembagh', NULL, '107', 'fvdf vv bv', 1, 'dvdf v', NULL, '149', 'sharjeel ahmed', 'nvb', 'vb bv', '453', NULL, NULL, '2025-12-12', '2026-01-13 06:33:57', '2026-01-14 11:44:02', 0, 0),
(11, 'sharjeel ahmed nvb vb bv', 'Individual', '543', '2025-11-11', '03126658134', NULL, NULL, NULL, '234', NULL, '43', NULL, NULL, 1, 0, 0, 0, 'C DV', 'Active', '2025-12-12', NULL, '90', 'VFVFG', 'CL1011', NULL, '197', NULL, NULL, 1, NULL, NULL, NULL, 'CD', 'asas@gmail.com', 'DFVF', '131', '106', 'FDVDV', 1, NULL, NULL, '146', 'sharjeel ahmed', 'nvb', 'vb bv', '453', NULL, NULL, '2023-03-04', '2026-01-13 08:42:51', '2026-01-13 08:42:51', 0, 0),
(12, 'sharjeel ahmed nvb vb bv', 'Individual', NULL, NULL, '03126658134', NULL, NULL, '1', '234', NULL, '46', NULL, NULL, 1, 0, 0, 0, 'eggfbtgb', 'Active', '2026-12-12', '80', '89', NULL, 'CL1012', NULL, '197', '543', NULL, 0, NULL, NULL, NULL, '43245', 'asas@gmail.com', 'greg', '130', '106', '54t45', 1, NULL, NULL, '147', 'sharjeel ahmed', 'nvb', 'vb bv', '453', NULL, NULL, '2035-12-12', '2026-01-14 07:32:16', '2026-01-14 07:32:16', 0, 0),
(13, 'sharjeel ahmed nvb vb bv', 'Individual', '43553', NULL, '03126658134', NULL, NULL, '1', '236', '240', '43', NULL, NULL, 1, 0, 0, 0, 'fdbvdb', 'Active', '2025-12-12', '79', NULL, 'r44', 'CL1013', NULL, '195', '435432', NULL, 1, NULL, NULL, NULL, '43245', 'asas@gmail.com', 'vfvgfb', '129', '106', '54t45', 1, '345345', NULL, '149', 'sharjeel ahmed', 'nvb', 'vb bv', '453', NULL, NULL, '2026-12-12', '2026-01-14 07:34:01', '2026-01-14 07:34:01', 0, 0),
(22, 'Kamran Hussain', 'Individual', '42101-5555555-5', '1987-06-20', '03219876543', '03219876543', '02199887766', '1', 'Karachi Central', 'Software Engineer', 'Direct', NULL, NULL, 0, 0, 0, 0, NULL, 'Active', '2026-01-14', NULL, NULL, 'Systems Limited', 'CL1014', NULL, 'Salary', '180000', '40000', 1, 'Saba Hussain', 2, NULL, '03339876543', 'kamran.hussain@example.com', 'Gulshan-e-Iqbal, Block 10, Karachi', '105', 'Pakistan', 'PO15000', 0, NULL, NULL, 'Mr', 'Kamran', NULL, 'Hussain', 'PX9876543', NULL, NULL, '2031-06-20', '2026-01-14 13:15:50', '2026-01-14 13:15:50', 0, 0),
(23, 'Prime Traders (Pvt) Ltd', 'Business', 'NTN9999999-9', NULL, '03331112233', '03331112233', '02135556677', '1', 'Karachi West', 'Trading', 'Referral', NULL, NULL, 0, 0, 1, 0, NULL, 'Active', '2026-01-14', NULL, NULL, 'Prime Traders (Pvt) Ltd', 'CL1015', 'Asad Malik (Director)', 'Business Operations', '900000', '180000', 0, NULL, 0, NULL, '03441112233', 'info@primetraders.com', 'I.I. Chundrigar Road, Karachi', '106', 'Pakistan', 'PO15001', 0, NULL, NULL, 'Mr', 'Prime', NULL, 'Traders (Pvt) Ltd', 'PY1234567', NULL, 'Import/Export', '2033-01-14', '2026-01-14 13:15:50', '2026-01-14 13:15:50', 0, 0),
(26, 'sharjeel ahmed nvbf frarr', 'Individual', '42101-5555555-5', '2013-12-12', '03126658134', NULL, NULL, '1', '234', NULL, '43', 'fdvfdv', NULL, 1, 0, 0, 0, 'fdvfgb', 'Active', '2026-12-12', '80', '90', NULL, 'CL1016', NULL, NULL, '180000', NULL, 1, 'Saba Hussain', NULL, NULL, '43245', 'asas@gmail.com', 'GBFD', '129', '107', '54t45', 1, 'fvdbgfb', NULL, '147', 'sharjeel ahmed', 'nvbf', 'frarr', 'PX9876543', NULL, NULL, NULL, '2026-01-14 11:43:16', '2026-01-14 11:43:16', 0, 0),
(27, 'fdgf', 'Business', 'bfdbf', NULL, '5654656', NULL, NULL, '1', '233', NULL, '42', 'vdf', NULL, 0, 0, 0, 0, 'fdvfdv', 'Active', '2025-12-12', '79', '89', NULL, 'CL1017', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'fbvgfgb', 'asas@gmail.com', 'fvv', '130', NULL, 'bg gf', 0, NULL, NULL, NULL, '', '', 'fdgf', NULL, NULL, NULL, NULL, '2026-01-15 06:17:22', '2026-01-15 06:17:22', 0, 0),
(28, 'vffdvdfv', 'Business', '4656', NULL, '4563463', NULL, NULL, '1', '233', NULL, '43', '4563', NULL, 1, 0, 0, 0, '54656', 'Active', '2025-12-12', '79', '89', NULL, 'CL1018', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'dvfdvdfv', 'asas@gmail.com', 'fv dsfvd', '130', '107', '6346', 0, NULL, NULL, NULL, '', '', 'vffdvdfv', NULL, NULL, NULL, NULL, '2026-01-15 06:28:13', '2026-01-15 06:28:13', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `commissions`
--

CREATE TABLE `commissions` (
  `id` bigint UNSIGNED NOT NULL,
  `commission_note_id` bigint UNSIGNED NOT NULL,
  `commission_statement_id` bigint UNSIGNED DEFAULT NULL,
  `grouping` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `basic_premium` decimal(15,2) DEFAULT NULL,
  `rate` decimal(8,2) DEFAULT NULL,
  `amount_due` decimal(15,2) DEFAULT NULL,
  `payment_status_id` bigint UNSIGNED DEFAULT NULL,
  `amount_received` decimal(15,2) DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `mode_of_payment_id` bigint UNSIGNED DEFAULT NULL,
  `variance` decimal(15,2) DEFAULT NULL,
  `variance_reason` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_due` date DEFAULT NULL,
  `commission_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `commissions`
--

INSERT INTO `commissions` (`id`, `commission_note_id`, `commission_statement_id`, `grouping`, `basic_premium`, `rate`, `amount_due`, `payment_status_id`, `amount_received`, `date_received`, `mode_of_payment_id`, `variance`, `variance_reason`, `date_due`, `commission_code`, `created_at`, `updated_at`) VALUES
(1, 3, 4, 'Motor', 9875.77, 3310.00, 987.58, 88, 987.00, '2023-11-15', 138, 988.00, 'vfdvdfbdf', '2025-11-01', 'COM001', '2026-01-13 10:50:46', '2026-01-15 04:41:31'),
(2, 2, 2, 'General', 7650.00, 10.00, 765.00, 86, 765.00, '2020-05-20', 154, 0.00, NULL, '2020-05-15', 'COM002', '2026-01-13 10:50:46', '2026-01-13 10:50:46'),
(3, 3, 3, 'General', 5000.00, 10.00, 500.00, 86, 500.00, '2023-01-10', 154, 0.00, NULL, '2023-01-01', 'COM003', '2026-01-13 10:50:46', '2026-01-13 10:50:46'),
(4, 4, 4, 'General', 3750.00, 10.00, 375.00, 86, 375.00, '2023-11-01', 152, 0.00, NULL, '2023-10-25', 'COM004', '2026-01-13 10:50:46', '2026-01-13 10:50:46'),
(5, 5, 5, 'Motor', 6652.00, 10.00, 665.20, 86, 665.20, '2022-12-20', 154, 0.00, NULL, '2022-12-15', 'COM005', '2026-01-13 10:50:46', '2026-01-13 10:50:46');

-- --------------------------------------------------------

--
-- Table structure for table `commission_notes`
--

CREATE TABLE `commission_notes` (
  `id` bigint UNSIGNED NOT NULL,
  `schedule_id` bigint UNSIGNED NOT NULL,
  `com_note_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `issued_on` date DEFAULT NULL,
  `total_premium` decimal(15,2) DEFAULT NULL,
  `expected_commission` decimal(15,2) DEFAULT NULL,
  `attachment_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `commission_notes`
--

INSERT INTO `commission_notes` (`id`, `schedule_id`, `com_note_id`, `issued_on`, `total_premium`, `expected_commission`, `attachment_path`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 'CN001', '2023-10-20', 11455.89, 1145.59, NULL, 'Motor comprehensive commission', '2026-01-13 10:49:47', '2026-01-13 10:49:47'),
(2, 2, 'CN002', '2020-05-01', 35467.00, 3546.70, NULL, 'Home insurance commission', '2026-01-13 10:49:47', '2026-01-13 10:49:47'),
(3, 3, 'CN003', '2022-12-05', 5800.00, 580.00, NULL, 'Business liability commission', '2026-01-13 10:49:47', '2026-01-13 10:49:47'),
(4, 5, 'CN004', '2023-10-10', 4350.00, 435.00, NULL, 'Spa business commission', '2026-01-13 10:49:47', '2026-01-13 10:49:47'),
(5, 6, 'CN005', '2022-11-20', 7716.32, 771.63, NULL, 'SUV insurance commission', '2026-01-13 10:49:47', '2026-01-13 10:49:47');

-- --------------------------------------------------------

--
-- Table structure for table `commission_statements`
--

CREATE TABLE `commission_statements` (
  `id` bigint UNSIGNED NOT NULL,
  `commission_note_id` bigint UNSIGNED DEFAULT NULL,
  `com_stat_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_start` date DEFAULT NULL,
  `period_end` date DEFAULT NULL,
  `net_commission` decimal(15,2) DEFAULT NULL,
  `tax_withheld` decimal(15,2) DEFAULT NULL,
  `attachment_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `commission_statements`
--

INSERT INTO `commission_statements` (`id`, `commission_note_id`, `com_stat_id`, `period_start`, `period_end`, `net_commission`, `tax_withheld`, `attachment_path`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 'CS001', '2023-10-01', '2023-10-31', 1030.03, 115.56, NULL, 'October 2023 commission statement', '2026-01-13 10:50:19', '2026-01-13 10:50:19'),
(2, 2, 'CS002', '2020-04-01', '2020-04-30', 3192.03, 354.67, NULL, 'April 2020 commission statement', '2026-01-13 10:50:19', '2026-01-13 10:50:19'),
(3, 3, 'CS003', '2022-12-01', '2022-12-31', 522.00, 58.00, NULL, 'December 2022 commission statement', '2026-01-13 10:50:19', '2026-01-13 10:50:19'),
(4, 4, 'CS004', '2023-10-01', '2023-10-31', 391.50, 43.50, NULL, 'Spa commission October 2023', '2026-01-13 10:50:19', '2026-01-13 10:50:19'),
(5, 5, 'CS005', '2022-11-01', '2022-11-30', 694.47, 77.16, NULL, 'November 2022 SUV commission', '2026-01-13 10:50:19', '2026-01-13 10:50:19');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint UNSIGNED NOT NULL,
  `contact_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wa` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `occupation` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employer` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acquired` date DEFAULT NULL,
  `source` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rank` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `second_follow_up` date DEFAULT NULL,
  `first_contact` date DEFAULT NULL,
  `next_follow_up` date DEFAULT NULL,
  `coid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `salutation` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agency` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agent` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `location` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `savings_budget` decimal(10,2) DEFAULT NULL,
  `married` tinyint(1) NOT NULL DEFAULT '0',
  `children` int NOT NULL DEFAULT '0',
  `children_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `vehicle` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `house` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `contact_name`, `contact_no`, `mobile_no`, `wa`, `type`, `occupation`, `employer`, `acquired`, `source`, `status`, `rank`, `second_follow_up`, `first_contact`, `next_follow_up`, `coid`, `dob`, `salutation`, `source_name`, `agency`, `agent`, `address`, `location`, `email_address`, `contact_id`, `savings_budget`, `married`, `children`, `children_details`, `vehicle`, `house`, `business`, `other`, `created_at`, `updated_at`) VALUES
(1, 'huihftfk', 'ntrhn', 'ytm,m', NULL, '2', 'Doctor', 'City Hospital', '2026-01-21', '43', '67', '98', NULL, NULL, '2026-01-14', NULL, '2026-01-08', '148', 'mnhgmh', '79', '89', 'hymtjlkht', NULL, 'aqsariasat235@gmail.com', 'CT166', 850.00, 0, 5, NULL, '1', '0', '0', 'hjfhjg', '2026-01-11 01:01:51', '2026-01-17 06:37:58'),
(2, 'John Smith', '2764521', '2514578', '2514578', '1', 'Manager', 'ACME Corp', '2025-06-15', '41', '65', '92', NULL, '2025-06-15', '2026-02-01', NULL, '1985-03-22', 'Mr', NULL, NULL, NULL, NULL, NULL, 'john.smith@email.com', 'CT167', 1500.00, 1, 2, NULL, NULL, NULL, NULL, NULL, '2026-01-13 10:34:11', '2026-01-13 10:34:11'),
(3, 'Mary Jane Watson', '2789654', '2589741', '2589741', '2', 'Nurse', 'Central Hospital', '2025-08-10', '42', '66', '93', NULL, '2025-08-10', '2026-01-20', NULL, '1990-07-14', 'Mrs', NULL, NULL, NULL, NULL, NULL, 'mary.watson@email.com', 'CT168', 800.00, 1, 1, NULL, NULL, NULL, NULL, NULL, '2026-01-13 10:34:11', '2026-01-13 10:34:11'),
(4, 'Robert Brown', '2741258', '2536987', NULL, '1', 'Fisherman', 'Self Employed', '2025-09-20', '43', '65', '94', NULL, '2025-09-20', '2026-01-25', NULL, '1978-11-30', 'Mr', NULL, NULL, NULL, NULL, NULL, 'robert.brown@email.com', 'CT169', 600.00, 0, 0, NULL, NULL, NULL, NULL, NULL, '2026-01-13 10:34:11', '2026-01-13 10:34:11'),
(5, 'Emily Davis', '2798562', '2541236', '2541236', '3', 'Teacher', 'International School', '2025-10-05', '44', '67', '91', NULL, '2025-10-05', '2026-02-15', NULL, '1988-05-18', 'Ms', NULL, NULL, NULL, NULL, NULL, 'emily.davis@email.com', 'CT170', 1200.00, 0, 0, NULL, NULL, NULL, NULL, NULL, '2026-01-13 10:34:11', '2026-01-13 10:34:11'),
(6, 'David Wilson', '2785412', '2563214', '2563214', '1', 'Accountant', 'Finance Ltd', '2025-11-12', '41', '65', '95', NULL, '2025-11-12', '2026-01-30', NULL, '1982-09-05', 'Mr', NULL, NULL, NULL, NULL, NULL, 'david.wilson@email.com', 'CT171', 2000.00, 1, 3, NULL, NULL, NULL, NULL, NULL, '2026-01-13 10:34:11', '2026-01-13 10:34:11');

-- --------------------------------------------------------

--
-- Table structure for table `debit_notes`
--

CREATE TABLE `debit_notes` (
  `id` bigint UNSIGNED NOT NULL,
  `payment_plan_id` bigint UNSIGNED NOT NULL,
  `debit_note_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `issued_on` date DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `document_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `debit_notes`
--

INSERT INTO `debit_notes` (`id`, `payment_plan_id`, `debit_note_no`, `issued_on`, `amount`, `status`, `document_path`, `is_encrypted`, `created_at`, `updated_at`) VALUES
(1, 1, 'DN001', '2023-10-16', 11455.89, 'paid', NULL, 0, '2026-01-13 10:47:25', '2026-01-13 10:47:25'),
(2, 2, 'DN002', '2020-04-18', 7093.40, 'paid', NULL, 0, '2026-01-13 10:47:25', '2026-01-13 10:47:25'),
(3, 3, 'DN003', '2021-04-18', 7093.40, 'paid', NULL, 0, '2026-01-13 10:47:25', '2026-01-13 10:47:25'),
(4, 4, 'DN004', '2022-04-18', 7093.40, 'paid', NULL, 0, '2026-01-13 10:47:25', '2026-01-13 10:47:25'),
(5, 5, 'DN005', '2023-04-18', 7093.40, 'paid', NULL, 0, '2026-01-13 10:47:25', '2026-01-13 10:47:25'),
(6, 6, 'DN006', '2024-04-18', 7093.40, 'pending', NULL, 0, '2026-01-13 10:47:25', '2026-01-13 10:47:25'),
(7, 7, 'DN007', '2022-11-30', 5800.00, 'paid', NULL, 0, '2026-01-13 10:47:25', '2026-01-13 10:47:25'),
(8, 8, 'DN008', '2023-10-06', 1087.50, 'paid', NULL, 0, '2026-01-13 10:47:25', '2026-01-13 10:47:25'),
(9, 9, 'DN009', '2024-01-06', 1087.50, 'paid', NULL, 0, '2026-01-13 10:47:25', '2026-01-13 10:47:25'),
(10, 10, 'DN010', '2024-04-06', 1087.50, 'pending', NULL, 0, '2026-01-13 10:47:25', '2026-01-13 10:47:25'),
(11, 6, '43256t44', '2026-12-12', 344.00, 'paid', NULL, 0, '2026-01-17 05:31:33', '2026-01-17 05:31:33'),
(12, 3, '43256t44444', '2026-12-12', 4356.00, 'overdue', NULL, 0, '2026-01-17 05:58:47', '2026-01-17 05:58:47');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` bigint UNSIGNED NOT NULL,
  `doc_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tied_to` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `format` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_added` date DEFAULT NULL,
  `year` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `file_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `doc_id`, `tied_to`, `name`, `group`, `type`, `format`, `date_added`, `year`, `notes`, `file_path`, `is_encrypted`, `created_at`, `updated_at`) VALUES
(1, 'DOC001', 'policy_1', 'Policy Certificate', 'Policies', 'Certificate', 'pdf', '2023-10-16', '2023', 'Original policy certificate', 'documents/doc001.pdf', 0, '2026-01-13 10:52:29', '2026-01-13 10:52:29'),
(3, 'DOC003', 'claim_1', 'Police Report', 'Claims', 'Claim Document', 'pdf', '2024-06-17', '2024', 'Accident police report', 'documents/doc003.pdf', 0, '2026-01-13 10:52:29', '2026-01-13 10:52:29'),
(4, 'DOC004', 'client_1', 'ID Copy', 'Clients', 'Other Document', 'jpg', '2023-10-15', '2023', 'Client identification', 'documents/doc004.jpg', 0, '2026-01-13 10:52:29', '2026-01-13 10:52:29'),
(5, 'DOC005', 'policy_6', 'Vehicle Registration', 'Vehicles', 'Certificate', 'pdf', '2022-11-15', '2022', 'SUV registration document', 'documents/doc005.pdf', 0, '2026-01-13 10:52:29', '2026-01-13 10:52:29'),
(6, 'DOC006', 'claim_4', 'Plumber Report', 'Claims', 'Claim Document', 'pdf', '2024-08-08', '2024', 'Water damage assessment', 'documents/doc006.pdf', 0, '2026-01-13 10:52:29', '2026-01-13 10:52:29'),
(7, 'DOC007', 'policy_3', 'Liability Certificate', 'Policies', 'Certificate', 'pdf', '2022-11-30', '2022', 'Business liability cert', 'documents/doc007.pdf', 0, '2026-01-13 10:52:29', '2026-01-13 10:52:29'),
(8, 'DOC888', NULL, 'Demo File', NULL, NULL, 'jpg', '2026-01-14', NULL, NULL, 'documents/demo.jpg', 0, '2026-01-14 10:23:04', '2026-01-14 10:23:04'),
(9, 'DOC999', NULL, 'Real Test Image', NULL, NULL, 'jpg', '2026-01-14', NULL, NULL, 'documents/real-image.jpg', 0, '2026-01-14 10:25:26', '2026-01-14 10:25:26'),
(10, 'DOC100', 'Test Client', 'Sample Document Image', NULL, 'Sample', 'jpg', '2026-01-14', NULL, NULL, 'documents/sample-document.jpg', 0, '2026-01-14 10:46:13', '2026-01-14 10:46:13'),
(11, 'DOC101', 'CL1017', 'Business Document', 'Client Document', 'other', 'png', '2026-01-15', '2026', NULL, 'documents/client_27_other_6968ccc2e6834.png', 0, '2026-01-15 06:17:22', '2026-01-15 06:17:22'),
(12, 'DOC102', 'CL1017', 'Business Document', 'Client Document', 'other', 'png', '2026-01-15', '2026', NULL, 'documents/client_27_other_6968ccc3030e3.png', 0, '2026-01-15 06:17:23', '2026-01-15 06:17:23');

-- --------------------------------------------------------

--
-- Table structure for table `endorsements`
--

CREATE TABLE `endorsements` (
  `id` bigint UNSIGNED NOT NULL,
  `policy_id` bigint UNSIGNED NOT NULL,
  `endorsement_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `endorsement_notes` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `endorsements`
--

INSERT INTO `endorsements` (`id`, `policy_id`, `endorsement_no`, `type`, `effective_date`, `status`, `description`, `endorsement_notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'END001', 'Amendment', '2024-02-15', 'approved', 'Sum Insured Increased', 'Increased from 390K to 420K', '2026-01-13 10:51:39', '2026-01-13 10:51:39'),
(2, 2, 'END002', 'Renewal', '2024-04-18', 'approved', 'Policy Renewed', 'Annual renewal processed', '2026-01-13 10:51:39', '2026-01-13 10:51:39'),
(3, 3, 'END003', 'Cancelation', '2023-06-30', 'approved', 'Policy Cancelled', 'Client requested cancellation', '2026-01-13 10:51:39', '2026-01-13 10:51:39'),
(4, 6, 'END004', 'Amendment', '2023-05-20', 'approved', 'Vehicle changed', 'Updated to new vehicle', '2026-01-13 10:51:39', '2026-01-13 10:51:39'),
(5, 1, 'END005', 'Amendment', '2024-06-01', 'draft', 'Beneficary change', 'Adding new nominee', '2026-01-13 10:51:39', '2026-01-13 10:51:39');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint UNSIGNED NOT NULL,
  `expense_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expense_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payee` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_paid` date DEFAULT NULL,
  `amount_paid` decimal(15,2) DEFAULT NULL,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `mode_of_payment` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mode_of_payment_id` bigint UNSIGNED DEFAULT NULL,
  `attachment_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expense_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `expense_id`, `expense_code`, `payee`, `date_paid`, `amount_paid`, `description`, `category_id`, `mode_of_payment`, `receipt_no`, `mode_of_payment_id`, `attachment_path`, `expense_notes`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'EXP001', 'EXP001', 'Seychelles Revenue Commission', '2024-01-15', 5000.00, 'Business License Renewal', 161, 'Transfer', 'REC001', 154, NULL, 'Annual license fee', NULL, '2026-01-13 10:53:01', '2026-01-13 10:53:01'),
(2, 'EXP002', 'EXP002', 'SACOS Insurance', '2024-02-01', 2500.00, 'Office Insurance Premium', 162, 'Transfer', 'REC002', 154, NULL, 'Professional indemnity', NULL, '2026-01-13 10:53:01', '2026-01-13 10:53:01'),
(3, 'EXP003', 'EXP003', 'Office Supplies Ltd', '2024-03-10', 800.00, 'Printer and Stationery', 163, 'Cash', 'REC003', 152, NULL, 'Quarterly supplies', NULL, '2026-01-13 10:53:01', '2026-01-13 10:53:01'),
(4, 'EXP004', 'EXP004', 'Intelvision', '2024-03-15', 1200.00, 'Internet Quarterly', 164, 'Transfer', 'REC004', 154, NULL, 'Q1 internet bill', NULL, '2026-01-13 10:53:01', '2026-01-13 10:53:01'),
(5, 'EXP005', 'EXP005', 'Print Shop', '2024-04-01', 3500.00, 'Marketing Brochures', 165, 'Cheque', 'REC005', 155, NULL, 'Promotional materials', NULL, '2026-01-13 10:53:01', '2026-01-13 10:53:01'),
(6, 'EXP006', 'EXP006', 'Air Seychelles', '2024-04-15', 8500.00, 'Conference Travel', 166, 'Card', 'REC006', 153, NULL, 'Insurance conference Dubai', NULL, '2026-01-13 10:53:01', '2026-01-13 10:53:01'),
(7, 'EXP007', 'EXP007', 'Agent Commissions', '2024-05-01', 2000.00, 'Referral Fee', 165, 'Transfer', 'REC007', 138, NULL, 'Lead referral payment', NULL, '2026-01-13 10:53:01', '2026-01-14 04:32:58');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `followups`
--

CREATE TABLE `followups` (
  `id` bigint UNSIGNED NOT NULL,
  `follow_up_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_id` bigint UNSIGNED DEFAULT NULL,
  `client_id` bigint UNSIGNED DEFAULT NULL,
  `life_proposal_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `channel` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `next_action` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `followups`
--

INSERT INTO `followups` (`id`, `follow_up_code`, `contact_id`, `client_id`, `life_proposal_id`, `user_id`, `follow_up_date`, `channel`, `status`, `summary`, `next_action`, `created_at`, `updated_at`) VALUES
(2, 'FU000002', 2, NULL, 1, 1, '2025-08-25', 'System', 'Open', 'Life proposal updated', 'Review proposal', '2026-01-13 10:56:16', '2026-01-16 07:03:34'),
(3, 'FU000003', 3, NULL, 2, 2, '2025-09-10', 'Email', 'Completed', 'Sent policy options', 'Follow up on decision', '2026-01-13 10:56:16', '2026-01-16 14:40:57'),
(4, 'FU000004', NULL, 1, NULL, 1, '2024-07-01', 'Phone', 'Completed', 'Claim status update', 'Await documents', '2026-01-13 10:56:16', '2026-01-13 10:56:16'),
(5, 'FU000005', NULL, 5, NULL, 3, '2024-04-15', 'WhatsApp', 'Open', 'Vehicle change request', 'Process endorsement', '2026-01-13 10:56:16', '2026-01-13 10:56:16');

-- --------------------------------------------------------

--
-- Table structure for table `incomes`
--

CREATE TABLE `incomes` (
  `id` bigint UNSIGNED NOT NULL,
  `income_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `commission_statement_id` bigint UNSIGNED DEFAULT NULL,
  `statement_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `income_source_id` bigint UNSIGNED DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `amount_received` decimal(15,2) DEFAULT NULL,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `mode_of_payment_id` bigint UNSIGNED DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `income_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `incomes`
--

INSERT INTO `incomes` (`id`, `income_code`, `commission_statement_id`, `statement_no`, `income_source_id`, `date_received`, `amount_received`, `description`, `category_id`, `mode_of_payment_id`, `notes`, `income_notes`, `created_at`, `updated_at`) VALUES
(1, 'INC001', 1, '2', 264, '2023-11-15', 1030.03, 'Motor Commission Q4-2023', 189, 139, 'SACOS commission received', 'ytbfgn', '2026-01-13 10:54:05', '2026-01-15 04:42:10'),
(2, 'INC002', 2, NULL, 194, '2020-05-20', 3192.03, 'Home Insurance Commission', 185, 154, 'HSavy commission received', NULL, '2026-01-13 10:54:05', '2026-01-13 10:54:05'),
(3, 'INC003', 3, '5', 194, '2023-01-10', 52332.00, 'Business Liability Commission', 184, 138, 'Alliance commission received', NULL, '2026-01-13 10:54:05', '2026-01-14 04:32:29'),
(4, 'INC004', 4, NULL, 194, '2023-11-01', 391.50, 'Spa Business Commission', 185, 152, 'Cash commission collected', NULL, '2026-01-13 10:54:05', '2026-01-13 10:54:05'),
(5, 'INC005', 5, NULL, 194, '2022-12-20', 694.47, 'SUV Insurance Commission', 185, 154, 'Transfer received', NULL, '2026-01-13 10:54:05', '2026-01-13 10:54:05'),
(6, 'INC006', NULL, NULL, 195, '2024-06-15', 5000.00, 'Consulting Income', 184, 154, 'Advisory services fee', NULL, '2026-01-13 10:54:05', '2026-01-13 10:54:05'),
(7, 'INC007', NULL, NULL, 198, '2024-07-01', 1500.00, 'Office Rental Income', 189, 154, 'Subletting office space', NULL, '2026-01-13 10:54:05', '2026-01-13 10:54:05');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `life_proposals`
--

CREATE TABLE `life_proposals` (
  `id` bigint UNSIGNED NOT NULL,
  `proposers_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_id` bigint UNSIGNED NOT NULL,
  `insurer_id` bigint UNSIGNED DEFAULT NULL,
  `policy_plan_id` bigint UNSIGNED DEFAULT NULL,
  `salutation_id` bigint UNSIGNED DEFAULT NULL,
  `sum_assured` decimal(15,2) DEFAULT NULL,
  `term` int DEFAULT NULL,
  `add_ons` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `offer_date` date DEFAULT NULL,
  `premium` decimal(15,2) DEFAULT NULL,
  `frequency_id` bigint UNSIGNED DEFAULT NULL,
  `proposal_stage_id` bigint UNSIGNED DEFAULT NULL,
  `age` int DEFAULT NULL,
  `status_id` bigint UNSIGNED DEFAULT NULL,
  `source_of_payment_id` bigint UNSIGNED DEFAULT NULL,
  `mcr` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agency` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_id` bigint UNSIGNED DEFAULT NULL,
  `is_submitted` tinyint(1) NOT NULL DEFAULT '0',
  `sex` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anb` int DEFAULT NULL,
  `riders` json DEFAULT NULL,
  `rider_premiums` json DEFAULT NULL,
  `annual_premium` decimal(15,2) DEFAULT NULL,
  `base_premium` decimal(15,2) DEFAULT NULL,
  `admin_fee` decimal(15,2) DEFAULT NULL,
  `total_premium` decimal(15,2) DEFAULT NULL,
  `medical_examination_required` tinyint(1) NOT NULL DEFAULT '0',
  `policy_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loading_premium` decimal(15,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `maturity_date` date DEFAULT NULL,
  `method_of_payment` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `life_proposals`
--

INSERT INTO `life_proposals` (`id`, `proposers_name`, `contact_id`, `insurer_id`, `policy_plan_id`, `salutation_id`, `sum_assured`, `term`, `add_ons`, `offer_date`, `premium`, `frequency_id`, `proposal_stage_id`, `age`, `status_id`, `source_of_payment_id`, `mcr`, `agency`, `prid`, `class_id`, `is_submitted`, `sex`, `anb`, `riders`, `rider_premiums`, `annual_premium`, `base_premium`, `admin_fee`, `total_premium`, `medical_examination_required`, `policy_no`, `loading_premium`, `start_date`, `maturity_date`, `method_of_payment`, `source_name`, `created_at`, `updated_at`) VALUES
(1, 'John Smith1231223', 2, 262, 273, 148, 500000.00, 20, NULL, '2025-08-15', 2200.00, 32, 204, 40, 207, 115, NULL, 'Keystone', 'LP001', 124, 0, 'M', 41, NULL, NULL, 2500.00, 2200.00, NULL, 2200.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-13 10:55:17', '2026-01-16 07:03:34'),
(2, 'Mary Jane Watson', 3, 264, 273, 148, 300000.00, 15, NULL, '2025-09-01', 1800.00, 217, 205, 35, 208, 115, NULL, 'LIS', 'LP002', 124, 1, 'F', 36, NULL, NULL, 1800.00, 1600.00, NULL, 1800.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-13 10:55:17', '2026-01-13 10:55:17'),
(3, 'David Wilson', 6, 262, 274, 146, 750000.00, 25, NULL, '2025-10-20', 3200.00, 218, 203, 43, 207, 116, NULL, 'Keystone', 'LP003', 124, 0, 'M', 44, NULL, NULL, 3200.00, 2900.00, NULL, 3200.00, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-13 10:55:17', '2026-01-13 10:55:17'),
(4, 'Emily Davis', 5, 263, 273, 147, 200000.00, 10, NULL, '2025-11-05', 1200.00, 217, 206, 37, 209, 115, NULL, 'LIS', 'LP004', 124, 1, 'F', 38, NULL, NULL, 1200.00, 1050.00, NULL, 1200.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-13 10:55:17', '2026-01-13 10:55:17');

-- --------------------------------------------------------

--
-- Table structure for table `lookup_categories`
--

CREATE TABLE `lookup_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lookup_categories`
--

INSERT INTO `lookup_categories` (`id`, `name`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Contact Type', 1, '2026-01-09 02:03:20', '2026-01-09 02:03:20'),
(2, 'Claim Stage', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(3, 'Vehicle Make', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(4, 'Client Type', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(5, 'Insurer', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(6, 'Frequency', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(7, 'Payment Plan', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(8, 'Contact Stage', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(9, 'Source', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(10, 'Contact Status', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(11, 'Policy Status', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(12, 'APL Agency', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(13, 'Channel', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(14, 'Payment Status', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(15, 'Agent', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(16, 'Ranking', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(17, 'Rank', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(18, 'Client Status', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(19, 'Issuing Country', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(20, 'Source Of Payment', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(21, 'ID Type', 1, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(22, 'Class', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(23, 'Island', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(24, 'Mode Of Payment (Life)', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(25, 'Claim Status', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(26, 'Salutation', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(27, 'Mode Of Payment (General)', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(28, 'Useage', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(29, 'Expense Category', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(30, 'Vehicle Type', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(31, 'Income Category', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(32, 'Business Type', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(33, 'Income Source', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(34, 'Proposal Stage', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(35, 'Proposal Status', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(36, 'PaymentType', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(37, 'Term', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(38, 'Engine Type', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(39, 'ENDORSEMENT', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(40, 'District', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(41, 'Occupation', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(42, 'Term Units', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(43, 'Document Type', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(44, 'Task Category', 1, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(45, 'Insurers', 1, NULL, NULL),
(46, 'Policy Classes', 1, NULL, NULL),
(47, 'Policy Plans', 1, NULL, NULL),
(48, 'Policy Statuses', 1, NULL, NULL),
(49, 'Business Types', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lookup_values`
--

CREATE TABLE `lookup_values` (
  `id` bigint UNSIGNED NOT NULL,
  `lookup_category_id` bigint UNSIGNED NOT NULL,
  `seq` int NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lookup_values`
--

INSERT INTO `lookup_values` (`id`, `lookup_category_id`, `seq`, `name`, `active`, `description`, `type`, `code`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Lead', 1, NULL, NULL, NULL, '2026-01-09 02:03:20', '2026-01-09 02:03:20'),
(2, 1, 2, 'Prospect', 1, NULL, NULL, NULL, '2026-01-09 02:03:20', '2026-01-09 02:03:20'),
(3, 1, 3, 'Contact', 1, NULL, NULL, NULL, '2026-01-09 02:03:20', '2026-01-09 02:03:20'),
(4, 1, 4, 'SO Bank Officer', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(5, 1, 5, 'Payroll Officer', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(6, 2, 1, 'Awaiting Documents', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(7, 2, 2, 'Awaiting QS Report', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(8, 3, 1, 'Hyundai', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(9, 3, 2, 'Kia', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(10, 3, 3, 'Suzuki', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(11, 3, 4, 'Toyota', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(12, 3, 5, 'Ford', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(13, 3, 6, 'MG', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(14, 3, 7, 'Nissan', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(15, 3, 8, 'Mazda', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(16, 3, 9, 'BMW', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(17, 3, 10, 'Mercedes', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(18, 3, 11, 'Lexus', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(19, 3, 12, 'Haval', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(20, 3, 13, 'Honda', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(21, 3, 14, 'Tata', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(22, 3, 15, 'Isuzu', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(23, 4, 1, 'Individual', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(24, 4, 2, 'Business', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(25, 4, 3, 'Company', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(26, 4, 4, 'Organization', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(27, 5, 1, 'SACOS', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(28, 5, 2, 'HSavy', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(29, 5, 3, 'Alliance', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(30, 5, 4, 'MUA', 0, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(31, 6, 1, 'Year', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(32, 6, 2, 'Days', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(33, 6, 3, 'Weeks', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(34, 7, 1, 'Single', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(35, 7, 2, 'Instalments', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(36, 7, 3, 'Regular (Life)', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(37, 8, 1, 'Open', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(38, 8, 2, 'Qualified', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(39, 8, 3, 'KIV', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(40, 8, 4, 'Closed', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(41, 9, 1, 'Direct', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(42, 9, 2, 'Online', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(43, 9, 3, 'Bank ABSA', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(44, 9, 4, 'MCB', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(45, 9, 5, 'NOU', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(46, 9, 6, 'BAR', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(47, 9, 7, 'BOC', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(48, 9, 8, 'SCB', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(49, 9, 9, 'SCU', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(50, 9, 10, 'AIRTEL', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(51, 9, 11, 'Cable & Wireless', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(52, 9, 12, 'Intelvision', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(53, 9, 13, 'PUC', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(54, 9, 14, 'SFA', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(55, 9, 15, 'STC', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(56, 9, 16, 'FSA', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(57, 9, 17, 'Mins Of Education', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(58, 9, 18, 'Mins Of Health', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(59, 9, 19, 'SFRSA', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(60, 9, 20, 'Seychelles Police', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(61, 9, 21, 'Treasury', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(62, 9, 22, 'Judiciary', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(63, 9, 23, 'Pilgrims', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(64, 9, 24, 'SPTC', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(65, 10, 1, 'Not Contacted', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(66, 10, 2, 'Qualified', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(67, 10, 3, 'Converted to Client', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(68, 10, 4, 'Keep In View', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(69, 10, 5, 'Archived', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(70, 11, 1, 'In Force', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(71, 11, 2, 'Expired', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(72, 11, 3, 'Cancelled', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(73, 11, 4, 'Lapsed', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(74, 11, 5, 'Matured', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(75, 11, 6, 'Surrenders', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(76, 11, 7, 'Payout D', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(77, 11, 8, 'Payout TPD', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(78, 11, 9, 'Null & Void', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(79, 12, 1, 'Keystone', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(80, 12, 2, 'LIS', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(81, 13, 1, 'Direct', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(82, 13, 2, 'Online', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(83, 13, 3, 'Agent', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(84, 13, 4, 'Broker', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(85, 13, 5, 'Referral', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(86, 14, 1, 'Paid', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(87, 14, 2, 'Partly Paid', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(88, 14, 3, 'Unpaid', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(89, 15, 1, 'Mandy', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(90, 15, 2, 'Simon', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(91, 16, 1, 'VIP', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(92, 16, 2, 'High', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(93, 16, 3, 'Medium', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(94, 16, 4, 'Low', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(95, 17, 1, 'VIP', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(96, 17, 2, 'High', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(97, 17, 3, 'Medium', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(98, 17, 4, 'Low', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(99, 17, 5, 'Warm', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(100, 18, 1, 'Active', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(101, 18, 2, 'Inactive', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(102, 18, 3, 'Suspended', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(103, 18, 4, 'Pending', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(104, 18, 5, 'Dormant', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(105, 19, 1, 'Seychelles', 1, NULL, NULL, 'SEY', '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(106, 19, 2, 'Great Britain', 1, NULL, NULL, 'GBR', '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(107, 19, 3, 'Botswana', 1, NULL, NULL, 'BOT', '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(108, 19, 4, 'Sri Lanka', 1, NULL, NULL, 'SRI', '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(109, 19, 5, 'India', 1, NULL, NULL, 'IND', '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(110, 19, 6, 'Nepal', 1, NULL, NULL, 'NEP', '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(111, 19, 7, 'Bangladesh', 1, NULL, NULL, 'BAN', '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(112, 19, 8, 'Russia', 1, NULL, NULL, 'RUS', '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(113, 19, 9, 'Ukraine', 1, NULL, NULL, 'UKR', '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(114, 19, 10, 'Kenya', 1, NULL, NULL, 'KEN', '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(115, 20, 1, 'Commission', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(116, 20, 2, 'Bonus', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(117, 20, 3, 'Prize', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(118, 20, 4, 'Other', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(119, 21, 1, 'ID Card', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(120, 21, 2, 'Driving License', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(121, 21, 3, 'Passport', 1, NULL, NULL, NULL, '2026-01-09 02:03:21', '2026-01-09 02:03:21'),
(122, 22, 1, 'Motor', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(123, 22, 2, 'General', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(124, 22, 3, 'Life', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(125, 22, 4, 'Bonds', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(126, 22, 5, 'Travel', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(127, 22, 6, 'Marine', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(128, 22, 7, 'Health', 0, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(129, 23, 1, 'Mahe', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(130, 23, 2, 'Praslin', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(131, 23, 3, 'La Digue', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(132, 23, 4, 'Perseverance', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(133, 23, 5, 'Cerf', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(134, 23, 6, 'Eden', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(135, 23, 7, 'Silhouette', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(136, 24, 1, 'Transfer', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(137, 24, 2, 'Cheque', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(138, 24, 3, 'Cash', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(139, 24, 4, 'Online', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(140, 24, 5, 'Standing Order', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(141, 24, 6, 'Salary Deduction', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(142, 24, 7, 'Direect', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(143, 25, 1, 'Processing', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(144, 25, 2, 'Settled', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(145, 25, 3, 'Declined', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(146, 26, 1, 'Mr', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(147, 26, 2, 'Ms', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(148, 26, 3, 'Mrs', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(149, 26, 4, 'Miss', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(150, 26, 5, 'Dr', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(151, 26, 6, 'Mr & Mrs', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(152, 27, 1, 'Cash', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(153, 27, 2, 'Card', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(154, 27, 3, 'Transfer', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(155, 27, 4, 'Cheque', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(156, 28, 1, 'Private', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(157, 28, 2, 'Commercial', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(158, 28, 3, 'For Hire', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(159, 28, 4, 'Carriage Of Goods', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(160, 28, 5, 'Commuter', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(161, 29, 1, 'License', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(162, 29, 2, 'Insurance', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(163, 29, 3, 'Office supplies', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(164, 29, 4, 'Telephone & Internet', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(165, 29, 5, 'Marketting', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(166, 29, 6, 'Travel', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(167, 29, 7, 'Referals', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(168, 29, 8, 'Rentals', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(169, 29, 9, 'Vehicle', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(170, 29, 10, 'Fuel', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(171, 29, 11, 'Bank Fees', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(172, 29, 12, 'Charges', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(173, 29, 13, 'Misc', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(174, 29, 14, 'Asset Purchase', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(175, 30, 1, 'SUV', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(176, 30, 2, 'Hatchback', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(177, 30, 3, 'Sedan', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(178, 30, 4, 'Twin Cab', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(179, 30, 5, 'Pick Up', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(180, 30, 6, 'Scooter', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(181, 30, 7, 'Motor Cycle', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(182, 30, 8, 'Taxi', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(183, 30, 9, 'Van', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(184, 31, 1, 'General', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(185, 31, 2, 'Commission', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(186, 31, 3, 'Bonus', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(187, 31, 4, 'Salary', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(188, 31, 5, 'Investment', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(189, 31, 6, 'Rentals', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(190, 31, 7, 'Other', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(191, 32, 1, 'Direct', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(192, 32, 2, 'Transfer', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(193, 32, 3, 'Renewal', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(194, 33, 1, 'Employment', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(195, 33, 2, 'Self Employed', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(196, 33, 3, 'Business', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(197, 33, 4, 'Investment', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(198, 33, 5, 'Rentals', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(199, 33, 6, 'Retirement', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(200, 33, 7, 'Allowance', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(201, 33, 8, 'Other', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(202, 34, 1, 'Not Contacted', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(203, 34, 2, 'RNR', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(204, 34, 3, 'In Discussion', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(205, 34, 4, 'Offer Made', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(206, 34, 5, 'Proposal Filled', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(207, 35, 1, 'Awaiting Medical', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(208, 35, 2, 'Awaiting Policy', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(209, 35, 3, 'Approved', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(210, 35, 4, 'Declined', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(211, 35, 5, 'Withdrawn', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(212, 36, 1, 'Full', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(213, 36, 2, 'Instalment', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(214, 36, 3, 'Adjustment', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(215, 37, 1, 'Annual', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(216, 37, 2, 'Single', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(217, 37, 3, 'Monthly', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(218, 37, 4, 'Quarterly', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(219, 37, 5, 'Bi-Annual', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(220, 38, 1, 'Hybrid', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(221, 38, 2, 'Petrol', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(222, 38, 3, 'Diesel', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(223, 38, 4, 'Electric', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(224, 39, 1, 'Renewal', 1, 'Policy Renewed', NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(225, 39, 2, 'Cancelation', 1, 'Policy Cancelled', NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(226, 39, 3, 'Amendment', 1, 'Sum Insured Reduced', NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(227, 39, 4, 'Amendment', 1, 'Sum Insured Increased', NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(228, 39, 5, 'Amendment', 1, 'Plan Cover Changed', NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(229, 39, 6, 'Amendment', 1, 'Beneficary change', NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(230, 39, 7, 'Amendment', 1, 'Pay Plan Changed', NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(231, 39, 8, 'Amendment', 1, 'Vehicle changed', NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(232, 40, 1, 'Victoria', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(233, 40, 2, 'Beau Vallon', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(234, 40, 3, 'Mont Fleuri', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(235, 40, 4, 'Cascade', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(236, 40, 5, 'Providence', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(237, 40, 6, 'Grand Anse', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(238, 40, 7, 'Anse Aux Pins', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(239, 41, 1, 'Accountant', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(240, 41, 2, 'Driver', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(241, 41, 3, 'Customer Service Officer', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(242, 41, 4, 'Real Estate Agent', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(243, 41, 5, 'Rock Breaker', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(244, 41, 6, 'Payroll Officer', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(245, 41, 7, 'Boat Charter', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(246, 41, 8, 'Contractor', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(247, 41, 9, 'Technician', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(248, 41, 10, 'Paymaster', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(249, 41, 11, 'Human Resources Manager', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(250, 42, 1, 'Year', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(251, 42, 2, 'Month', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(252, 42, 3, 'Days', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(253, 43, 1, 'Policy Document', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(254, 43, 2, 'Certificate', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(255, 43, 3, 'Claim Document', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(256, 43, 4, 'Other Document', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(257, 44, 1, 'Payment', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(258, 44, 2, 'Report', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(259, 44, 3, 'Follow-up', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(260, 44, 4, 'Meeting', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(261, 44, 5, 'Call', 1, NULL, NULL, NULL, '2026-01-09 02:03:22', '2026-01-09 02:03:22'),
(262, 45, 1, 'SACOS', 1, NULL, NULL, NULL, NULL, NULL),
(263, 45, 2, 'Alliance', 1, NULL, NULL, NULL, NULL, NULL),
(264, 45, 3, 'Hsavy', 1, NULL, NULL, NULL, NULL, NULL),
(265, 45, 4, 'AON', 1, NULL, NULL, NULL, NULL, NULL),
(266, 45, 5, 'Marsh', 1, NULL, NULL, NULL, NULL, NULL),
(267, 46, 1, 'Motor', 1, NULL, NULL, NULL, NULL, NULL),
(268, 46, 2, 'General', 1, NULL, NULL, NULL, NULL, NULL),
(269, 46, 3, 'Travel', 1, NULL, NULL, NULL, NULL, NULL),
(270, 46, 4, 'Marine', 1, NULL, NULL, NULL, NULL, NULL),
(271, 46, 5, 'Health', 1, NULL, NULL, NULL, NULL, NULL),
(272, 46, 6, 'Life', 1, NULL, NULL, NULL, NULL, NULL),
(273, 47, 1, 'Comprehensive', 1, NULL, NULL, NULL, NULL, NULL),
(274, 47, 2, 'Third Party', 1, NULL, NULL, NULL, NULL, NULL),
(275, 47, 3, 'Householder\'s', 1, NULL, NULL, NULL, NULL, NULL),
(276, 47, 4, 'Public Liability', 1, NULL, NULL, NULL, NULL, NULL),
(277, 47, 5, 'Employer\'s Liability', 1, NULL, NULL, NULL, NULL, NULL),
(278, 47, 6, 'Fire & Special Perils', 1, NULL, NULL, NULL, NULL, NULL),
(279, 47, 7, 'House Insurance', 1, NULL, NULL, NULL, NULL, NULL),
(280, 47, 8, 'Fire Industrial', 1, NULL, NULL, NULL, NULL, NULL),
(281, 47, 9, 'World Wide Basic', 1, NULL, NULL, NULL, NULL, NULL),
(282, 47, 10, 'Marine Hull', 1, NULL, NULL, NULL, NULL, NULL),
(283, 48, 1, 'In Force', 1, NULL, NULL, NULL, NULL, NULL),
(284, 48, 2, 'DFR', 1, NULL, NULL, NULL, NULL, NULL),
(285, 48, 3, 'Expired', 1, NULL, NULL, NULL, NULL, NULL),
(286, 48, 4, 'Cancelled', 1, NULL, NULL, NULL, NULL, NULL),
(287, 49, 1, 'Direct', 1, NULL, NULL, NULL, NULL, NULL),
(288, 49, 2, 'Transfer', 1, NULL, NULL, NULL, NULL, NULL),
(289, 49, 3, 'Renewal', 1, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `medicals`
--

CREATE TABLE `medicals` (
  `id` bigint UNSIGNED NOT NULL,
  `life_proposal_id` bigint UNSIGNED NOT NULL,
  `medical_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `medical_type_id` bigint UNSIGNED DEFAULT NULL,
  `clinic` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordered_on` date DEFAULT NULL,
  `completed_on` date DEFAULT NULL,
  `status_id` bigint UNSIGNED DEFAULT NULL,
  `results_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medicals`
--

INSERT INTO `medicals` (`id`, `life_proposal_id`, `medical_code`, `medical_type_id`, `clinic`, `ordered_on`, `completed_on`, `status_id`, `results_path`, `notes`, `created_at`, `updated_at`) VALUES
(2, 3, 'MED002', 254, 'Mont Fleuri Clinic', '2025-10-25', NULL, 143, NULL, 'Awaiting medical results', '2026-01-13 10:55:45', '2026-01-13 10:55:45');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_10_05_171758_create_tasks_table', 1),
(5, '2025_10_11_084446_create_lookup_tables', 1),
(6, '2025_10_14_153256_create_clients_table', 1),
(7, '2025_10_15_101405_create_policies_table', 1),
(8, '2025_10_19_112711_create_contacts_table', 1),
(9, '2025_10_19_184103_create_life_proposals_table', 1),
(10, '2025_10_19_200529_create_expenses_table', 1),
(11, '2025_11_09_125623_create_documents_table', 1),
(12, '2025_11_09_141513_create_vehicles_table', 1),
(13, '2025_11_09_145357_create_claims_table', 1),
(14, '2025_11_09_152400_create_schedules_table', 1),
(15, '2025_11_09_152500_create_commission_notes_table', 1),
(16, '2025_11_09_152600_create_commission_statements_table', 1),
(17, '2025_11_09_152705_create_incomes_table', 1),
(18, '2025_11_09_163024_create_commissions_table', 1),
(19, '2025_11_09_180836_create_statements_table', 1),
(20, '2025_11_18_112128_create_beneficial_owners_table', 1),
(21, '2025_11_18_112148_create_nominees_table', 1),
(22, '2025_11_18_112215_create_renewal_notices_table', 1),
(23, '2025_11_18_112248_create_payment_plans_table', 1),
(24, '2025_11_18_112305_create_debit_notes_table', 1),
(25, '2025_11_18_112314_create_payments_table', 1),
(26, '2025_11_18_112323_create_endorsements_table', 1),
(27, '2025_11_18_112341_create_followups_table', 1),
(28, '2025_11_18_112352_create_medicals_table', 1),
(29, '2025_11_18_112435_create_tax_returns_table', 1),
(30, '2025_11_19_100000_add_roles_to_users_table', 1),
(31, '2025_11_19_100100_create_permissions_table', 1),
(32, '2025_11_19_100200_create_audit_logs_table', 1),
(33, '2025_11_19_100300_create_roles_table', 1),
(34, '2025_11_27_101333_update_role_permissions_table_to_use_role_id', 1),
(35, '2025_11_27_110318_add_encryption_flags_to_tables', 1),
(36, '2025_11_27_111755_add_client_id_to_policies_table', 1),
(37, '2025_12_02_090031_add_additional_fields_to_clients_table', 1),
(38, '2025_12_09_093600_add_policy_status_id_to_policies_table', 1),
(39, '2025_12_10_120000_remove_redundant_fields_from_policies_table', 1),
(40, '2025_12_11_095000_add_nin_passport_no_to_nominees_table', 1),
(41, '2025_12_11_100000_add_date_removed_to_nominees_table', 1),
(42, '2025_12_11_101000_make_policy_id_nullable_in_nominees_table', 1),
(43, '2025_12_13_012248_add_item_to_tasks_table', 1),
(44, '2025_12_13_015306_add_wa_to_contacts_table', 1),
(45, '2025_12_15_063841_update_claims_table_add_policy_id_foreign_key', 1),
(46, '2025_12_15_064602_add_claim_stage_to_claims_table', 1),
(47, '2025_12_15_065910_remove_client_name_from_claims_table', 1),
(48, '2025_12_15_071004_add_receipt_no_to_expenses_table', 1),
(49, '2025_12_15_072345_remove_receipt_path_from_expenses_table', 1),
(50, '2025_12_15_072351_remove_document_path_from_incomes_table', 1),
(51, '2025_12_15_073025_add_category_id_to_incomes_table', 1),
(52, '2025_12_15_105446_add_missing_columns_to_expenses_table', 1),
(53, '2025_12_20_055847_add_columns_to_beneficial_owners_table', 1),
(54, '2026_01_09_210631_add_mobile_no_to_contacts_table', 2),
(55, '2026_01_09_230000_add_wsc_lou_pa_to_policies_table', 2),
(56, '2026_01_11_090122_make_salutation_nullable_in_contacts_table', 3),
(57, '2026_01_11_092415_add_missing_columns_to_clients_table', 4),
(58, '2026_01_11_114158_add_second_follow_up_to_contacts_table', 5),
(59, '2026_01_12_045743_add_extra_fields_to_clients_table', 6),
(60, '2026_01_13_110116_add_statement_no_to_incomes_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `nominees`
--

CREATE TABLE `nominees` (
  `id` bigint UNSIGNED NOT NULL,
  `nominee_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_id` bigint UNSIGNED DEFAULT NULL,
  `client_id` bigint UNSIGNED DEFAULT NULL,
  `full_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `relationship` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `share_percentage` decimal(5,2) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `date_removed` date DEFAULT NULL,
  `nin_passport_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_document_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nominees`
--

INSERT INTO `nominees` (`id`, `nominee_code`, `policy_id`, `client_id`, `full_name`, `relationship`, `share_percentage`, `date_of_birth`, `date_removed`, `nin_passport_no`, `id_document_path`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'NOM001', 1, 1, 'Peter Grey', 'Son', 50.00, '2005-03-15', NULL, 'SEY111222', NULL, 'Primary beneficiary', '2026-01-13 10:36:37', '2026-01-13 10:36:37'),
(2, 'NOM002', 1, 1, 'Susan Grey', 'Daughter', 50.00, '2008-07-22', NULL, 'SEY222333', NULL, 'Secondary beneficiary', '2026-01-13 10:36:37', '2026-01-13 10:36:37'),
(3, 'NOM003', 2, 2, 'Mark Walton', 'Husband', 100.00, '1978-12-10', NULL, 'SEY333444', NULL, 'Sole beneficiary', '2026-01-13 10:36:37', '2026-01-13 10:36:37'),
(4, 'NOM004', 6, 5, 'Jennifer Trapper', 'Wife', 60.00, '1990-04-18', NULL, 'SEY444555', NULL, 'Primary beneficiary', '2026-01-13 10:36:37', '2026-01-13 10:36:37'),
(5, 'NOM005', 6, 5, 'Tommy Trapper', 'Son', 40.00, '2015-09-25', NULL, 'SEY555666', NULL, 'Minor beneficiary', '2026-01-13 10:36:37', '2026-01-13 10:36:37');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint UNSIGNED NOT NULL,
  `debit_note_id` bigint UNSIGNED NOT NULL,
  `payment_reference` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_on` date DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `mode_of_payment_id` bigint UNSIGNED DEFAULT NULL,
  `receipt_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `debit_note_id`, `payment_reference`, `paid_on`, `amount`, `mode_of_payment_id`, `receipt_path`, `is_encrypted`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'PAY001', '2023-10-16', 11455.89, 152, NULL, 0, 'Cash payment at office', '2026-01-13 10:48:34', '2026-01-13 10:48:34', NULL),
(2, 2, 'PAY002', '2020-04-18', 7093.40, 154, NULL, 0, 'Bank transfer', '2026-01-13 10:48:34', '2026-01-13 10:48:34', NULL),
(3, 3, 'PAY003', '2021-04-20', 7093.40, 154, NULL, 0, 'Bank transfer', '2026-01-13 10:48:34', '2026-01-13 10:48:34', NULL),
(4, 4, 'PAY004', '2022-04-20', 7093.40, 154, NULL, 0, 'Bank transfer', '2026-01-13 10:48:34', '2026-01-13 10:48:34', NULL),
(5, 5, 'PAY005', '2023-04-20', 7093.40, 154, NULL, 0, 'Bank transfer', '2026-01-13 10:48:34', '2026-01-13 10:48:34', NULL),
(6, 7, 'PAY006', '2022-12-01', 5800.00, 155, NULL, 0, 'Cheque payment', '2026-01-13 10:48:34', '2026-01-13 10:48:34', NULL),
(7, 8, 'PAY007', '2023-10-08', 1087.50, 152, NULL, 0, 'Cash payment', '2026-01-13 10:48:34', '2026-01-13 10:48:34', NULL),
(8, 9, 'PAY008', '2024-01-08', 1087.50, 152, NULL, 0, 'Cash payment', '2026-01-13 10:48:34', '2026-01-13 10:48:34', NULL),
(9, 3, 'hgn', '2025-12-12', 4546.00, 137, NULL, 0, NULL, '2026-01-15 06:59:43', '2026-01-15 06:59:43', NULL),
(10, 4, 'hgntryt', '2026-12-12', 34444.00, 137, NULL, 0, NULL, '2026-01-15 07:23:07', '2026-01-15 07:23:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_plans`
--

CREATE TABLE `payment_plans` (
  `id` bigint UNSIGNED NOT NULL,
  `schedule_id` bigint UNSIGNED NOT NULL,
  `installment_label` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `frequency` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_plans`
--

INSERT INTO `payment_plans` (`id`, `schedule_id`, `installment_label`, `due_date`, `amount`, `frequency`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Annual Payment', '2023-10-16', 11455.89, 'Annual', 'paid', '2026-01-13 10:39:05', '2026-01-13 10:39:05'),
(2, 2, 'Year 1', '2020-04-18', 7093.40, 'Annual', 'paid', '2026-01-13 10:39:05', '2026-01-13 10:39:05'),
(3, 2, 'Year 2', '2021-04-18', 7093.40, 'Annual', 'paid', '2026-01-13 10:39:05', '2026-01-13 10:39:05'),
(4, 2, 'Year 3', '2022-04-18', 7093.40, 'Annual', 'paid', '2026-01-13 10:39:05', '2026-01-13 10:39:05'),
(5, 2, 'Year 4', '2023-04-18', 7093.40, 'Annual', 'paid', '2026-01-13 10:39:05', '2026-01-13 10:39:05'),
(6, 2, 'Year 5', '2024-04-18', 7093.40, 'Annual', 'pending', '2026-01-13 10:39:05', '2026-01-13 10:39:05'),
(7, 3, 'Full Payment', '2022-11-30', 5800.00, 'Single', 'paid', '2026-01-13 10:39:05', '2026-01-13 10:39:05'),
(8, 5, 'Quarter 1', '2023-10-06', 1087.50, 'Quarterly', 'paid', '2026-01-13 10:39:05', '2026-01-13 10:39:05'),
(9, 5, 'Quarter 2', '2024-01-06', 1087.50, 'Quarterly', 'paid', '2026-01-13 10:39:05', '2026-01-13 10:39:05'),
(10, 5, 'Quarter 3', '2024-04-06', 1087.50, 'Quarterly', 'pending', '2026-01-13 10:39:05', '2026-01-13 10:39:05');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `policies`
--

CREATE TABLE `policies` (
  `id` bigint UNSIGNED NOT NULL,
  `client_id` bigint UNSIGNED NOT NULL,
  `policy_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `insurer_id` bigint UNSIGNED DEFAULT NULL,
  `policy_class_id` bigint UNSIGNED DEFAULT NULL,
  `policy_plan_id` bigint UNSIGNED DEFAULT NULL,
  `sum_insured` decimal(15,2) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `insured` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insured_item` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `policy_status_id` bigint UNSIGNED DEFAULT NULL,
  `date_registered` date NOT NULL,
  `renewable` tinyint(1) NOT NULL DEFAULT '1',
  `business_type_id` bigint UNSIGNED DEFAULT NULL,
  `term` int DEFAULT NULL,
  `term_unit` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `base_premium` decimal(15,2) DEFAULT NULL,
  `premium` decimal(15,2) DEFAULT NULL,
  `frequency_id` bigint UNSIGNED DEFAULT NULL,
  `pay_plan_lookup_id` bigint UNSIGNED DEFAULT NULL,
  `agency_id` bigint UNSIGNED DEFAULT NULL,
  `agent` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_date` date DEFAULT NULL,
  `last_endorsement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `channel_id` bigint UNSIGNED DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `wsc` decimal(15,2) DEFAULT NULL,
  `lou` decimal(15,2) DEFAULT NULL,
  `pa` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `policies`
--

INSERT INTO `policies` (`id`, `client_id`, `policy_no`, `policy_code`, `insurer_id`, `policy_class_id`, `policy_plan_id`, `sum_insured`, `start_date`, `end_date`, `insured`, `insured_item`, `policy_status_id`, `date_registered`, `renewable`, `business_type_id`, `term`, `term_unit`, `base_premium`, `premium`, `frequency_id`, `pay_plan_lookup_id`, `agency_id`, `agent`, `cancelled_date`, `last_endorsement`, `channel_id`, `notes`, `created_at`, `updated_at`, `wsc`, `lou`, `pa`) VALUES
(1, 1, 'POL1001', 'MPV-23-HEA-P0002132', NULL, NULL, NULL, 390000.00, '2023-10-16', '2028-01-14', 'S44444', 'Suzuki Fronx', 71, '2024-10-16', 1, 192, 1, '250', 9875.77, 11455.89, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'New vehicle policy', '2026-01-09 02:03:22', '2026-01-17 05:55:48', 10000.00, 15000.00, 250000.00),
(2, 2, 'POL1002', 'FSP-21-P00012999', NULL, NULL, NULL, NULL, '2020-04-18', '2028-01-14', NULL, 'Residence at Anse Royal', NULL, '2020-04-18', 1, NULL, 1, 'Year', 7650.00, 35467.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Home insurance policy', '2026-01-09 02:03:22', '2026-01-09 02:03:22', NULL, NULL, NULL),
(3, 3, 'POL1003', 'PL-22-ALP-000033', NULL, NULL, NULL, NULL, '2022-11-30', '2028-01-14', NULL, NULL, NULL, '2022-11-30', 1, NULL, 1, 'Year', 5000.00, 5800.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Business liability insurance', '2026-01-09 02:03:22', '2026-01-09 02:03:22', NULL, NULL, NULL),
(4, 3, 'POL1004', 'HS1-23-P00023132', NULL, NULL, NULL, NULL, '2022-11-12', '2028-01-14', NULL, NULL, NULL, '2022-11-12', 1, NULL, 1, 'Year', 2500.00, 2900.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Employee coverage', '2026-01-09 02:03:22', '2026-01-09 02:03:22', NULL, NULL, NULL),
(5, 4, 'POL1005', 'FSP-19-P00024', NULL, NULL, NULL, NULL, '2023-10-06', '2028-01-14', NULL, 'SPA at English River', NULL, '2022-10-05', 1, NULL, 1, 'Year', 3750.00, 4350.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Spa business insurance', '2026-01-09 02:03:23', '2026-01-09 02:03:23', NULL, NULL, NULL),
(6, 5, 'POL1006', 'MVC-18-000331', NULL, NULL, NULL, 285000.00, '2022-11-15', '2028-01-14', 'S260', 'Toyota Hyrider', NULL, '2022-11-15', 1, NULL, 1, 'Year', 6652.00, 7716.32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SUV insurance', '2026-01-09 02:03:23', '2026-01-09 02:03:23', NULL, NULL, NULL),
(7, 6, 'POL1007', 'MTC-22-000012', NULL, NULL, NULL, 0.00, '2022-09-11', '2028-01-14', 'S32453', 'Hyundai Creta', NULL, '2022-09-11', 1, NULL, 1, 'Year', 1500.00, 1827.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Third party only', '2026-01-09 02:03:23', '2026-01-09 02:03:23', NULL, NULL, NULL),
(8, 7, 'POL1008', 'MVT-21-000324', NULL, NULL, NULL, NULL, '2022-12-03', '2028-01-14', NULL, 'Shop Office Complex Providence', NULL, '2022-12-03', 1, NULL, 1, 'Year', 14377.00, 16677.32, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Commercial property', '2026-01-09 02:03:23', '2026-01-09 02:03:23', NULL, NULL, NULL),
(10, 4, '34566', 'POL000001', NULL, NULL, NULL, 0.06, '2025-12-12', '2025-12-12', NULL, 'bgfgf', NULL, '2025-12-12', 1, NULL, 1, '250', 545656.00, 456665.00, NULL, NULL, NULL, '89', NULL, NULL, NULL, 'vgdgbr', '2026-01-17 05:57:26', '2026-01-17 05:57:26', 10000.00, 15000.00, 250000.00);

-- --------------------------------------------------------

--
-- Table structure for table `renewal_notices`
--

CREATE TABLE `renewal_notices` (
  `id` bigint UNSIGNED NOT NULL,
  `policy_id` bigint UNSIGNED NOT NULL,
  `rnid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notice_date` date DEFAULT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `delivery_method` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `renewal_notices`
--

INSERT INTO `renewal_notices` (`id`, `policy_id`, `rnid`, `notice_date`, `status`, `delivery_method`, `document_path`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 'RN001', '2024-09-16', 'sent', 'Email', NULL, 'Renewal notice sent 30 days prior', '2026-01-13 10:52:05', '2026-01-13 10:52:05'),
(2, 2, 'RN002', '2025-03-18', 'pending', 'Email', NULL, 'Scheduled for renewal', '2026-01-13 10:52:05', '2026-01-13 10:52:05'),
(3, 5, 'RN003', '2024-09-06', 'sent', 'SMS', NULL, 'SMS reminder sent', '2026-01-13 10:52:05', '2026-01-13 10:52:05'),
(4, 6, 'RN004', '2023-10-15', 'acknowledged', 'Phone', NULL, 'Client confirmed renewal', '2026-01-13 10:52:05', '2026-01-13 10:52:05'),
(5, 8, 'RN005', '2023-11-02', 'sent', 'Email', NULL, 'Renewal notice emailed', '2026-01-13 10:52:05', '2026-01-13 10:52:05'),
(6, 10, 'RN000006', '2026-01-17', 'pending', 'email', NULL, NULL, '2026-01-17 05:57:26', '2026-01-17 05:57:26');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin', 'Full system access', 1, '2026-01-09 01:58:52', '2026-01-09 01:58:52'),
(2, 'Support', 'support', 'Support staff access', 0, '2026-01-09 01:58:52', '2026-01-09 01:58:52');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `role` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permission_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` bigint UNSIGNED NOT NULL,
  `policy_id` bigint UNSIGNED NOT NULL,
  `schedule_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `issued_on` date DEFAULT NULL,
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `debit_note_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `policy_schedule_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `renewal_notice_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_agreement_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `policy_id`, `schedule_no`, `issued_on`, `effective_from`, `effective_to`, `status`, `debit_note_path`, `receipt_path`, `policy_schedule_path`, `renewal_notice_path`, `payment_agreement_path`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'SCH001', '2023-10-16', '2023-10-16', '2024-10-15', 'active', NULL, NULL, NULL, NULL, NULL, 'Initial policy schedule', '2026-01-13 10:38:04', '2026-01-13 10:38:04'),
(2, 2, 'SCH002', '2020-04-18', '2020-04-18', '2025-04-17', 'active', NULL, NULL, NULL, NULL, NULL, 'Long term home insurance', '2026-01-13 10:38:04', '2026-01-13 10:38:04'),
(3, 3, 'SCH003', '2022-11-30', '2022-11-30', '2023-11-29', 'expired', NULL, NULL, NULL, NULL, NULL, 'Business liability schedule', '2026-01-13 10:38:04', '2026-01-13 10:38:04'),
(4, 4, 'SCH004', '2022-11-12', '2022-11-12', '2023-11-11', 'expired', NULL, NULL, NULL, NULL, NULL, 'Employee coverage schedule', '2026-01-13 10:38:04', '2026-01-13 10:38:04'),
(5, 5, 'SCH005', '2022-10-05', '2023-10-06', '2024-10-05', 'active', NULL, NULL, NULL, NULL, NULL, 'Spa business schedule', '2026-01-13 10:38:04', '2026-01-13 10:38:04'),
(6, 6, 'SCH006', '2022-11-15', '2022-11-15', '2023-11-14', 'expired', NULL, NULL, NULL, NULL, NULL, 'SUV insurance schedule', '2026-01-13 10:38:04', '2026-01-13 10:38:04'),
(7, 7, 'SCH007', '2022-09-11', '2022-09-11', '2023-09-10', 'expired', NULL, NULL, NULL, NULL, NULL, 'Third party schedule', '2026-01-13 10:38:04', '2026-01-13 10:38:04'),
(8, 8, 'SCH008', '2022-12-03', '2022-12-03', '2023-12-02', 'expired', NULL, NULL, NULL, NULL, NULL, 'Commercial property schedule', '2026-01-13 10:38:04', '2026-01-13 10:38:04');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('85jz2t1Yj7YYtlDttdL1JdKkDpV0393tbW7pJaPx', 1, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTmdxY0Q0Um1wcVJzYmVvVGgyQW50VHQ5ekdlWDV1S052M0pNY3hzSiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1768656135);

-- --------------------------------------------------------

--
-- Table structure for table `statements`
--

CREATE TABLE `statements` (
  `id` bigint UNSIGNED NOT NULL,
  `statement_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurer_id` bigint UNSIGNED DEFAULT NULL,
  `business_category` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `amount_received` decimal(15,2) DEFAULT NULL,
  `mode_of_payment_id` bigint UNSIGNED DEFAULT NULL,
  `remarks` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `statements`
--

INSERT INTO `statements` (`id`, `statement_no`, `year`, `insurer_id`, `business_category`, `date_received`, `amount_received`, `mode_of_payment_id`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 'STM001', '2023', 262, 'Motor', '2023-11-20', 15000.00, 154, 'SACOS Motor Statement Q4', '2026-01-13 10:54:45', '2026-01-13 10:54:45'),
(2, 'STM002', '2023', 263, 'General', '2023-12-15', 8500.00, 154, 'Alliance General Statement', '2026-01-13 10:54:45', '2026-01-13 10:54:45'),
(3, 'STM003', '2024', 264, 'Life', '2024-01-20', 12000.00, 154, 'Hsavy Life Statement', '2026-01-13 10:54:45', '2026-01-13 10:54:45'),
(4, 'STM004', '2024', 262, 'Motor', '2024-02-15', 18500.00, 154, 'SACOS Motor Statement Q1', '2026-01-13 10:54:45', '2026-01-13 10:54:45'),
(5, 'STM005', '2024', 263, 'Travel', '2024-03-10', 3500.00, 152, 'Alliance Travel Statement', '2026-01-13 10:54:45', '2026-01-13 10:54:45');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint UNSIGNED NOT NULL,
  `task_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `due_date` date NOT NULL,
  `due_time` time DEFAULT NULL,
  `date_in` date DEFAULT NULL,
  `assignee` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `task_status` enum('Not Done','In Progress','Completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Not Done',
  `date_done` date DEFAULT NULL,
  `repeat` tinyint(1) NOT NULL DEFAULT '0',
  `frequency` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rpt_date` date DEFAULT NULL,
  `rpt_stop_date` date DEFAULT NULL,
  `task_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `task_id`, `category`, `item`, `description`, `name`, `contact_no`, `due_date`, `due_time`, `date_in`, `assignee`, `task_status`, `date_done`, `repeat`, `frequency`, `rpt_date`, `rpt_stop_date`, `task_notes`, `created_at`, `updated_at`) VALUES
(1, 'TK24043', 'Payment', NULL, 'P.O. Box Rental', 'Seychelles Postal Services', '2765937', '2025-10-18', NULL, NULL, 'Mandy', 'Not Done', NULL, 1, 'Annually', '2025-01-01', '2027-12-31', NULL, '2026-01-09 02:03:23', '2026-01-09 02:03:23'),
(2, 'TK24044', 'Report', NULL, 'Beneficial Owner Report', 'FIU', '4282828', '2025-10-17', NULL, NULL, 'Mandy', 'Not Done', NULL, 1, 'Bi-Annually', '2025-01-15', '2026-12-31', NULL, '2026-01-09 02:03:23', '2026-01-09 02:03:23'),
(3, 'TK24045', 'Follow-up', 'test', '1', 'Mary Jane Watson', '00000000', '2026-01-10', '22:34:00', NULL, 'Sarah Johnson', 'In Progress', '2026-01-10', 0, '31', '2026-01-06', '2026-01-08', NULL, '2026-01-09 06:33:50', '2026-01-16 07:01:12'),
(4, 'TK24046', 'Follow-up', 'sharjeel ahmed', '1', 'Brian Trapper', 'jjkfn', '2025-12-31', '17:57:00', NULL, 'admin', 'In Progress', '2026-01-01', 1, 'Days', '2026-01-14', '2026-01-22', 'dcndcmdkl', '2026-01-11 01:55:02', '2026-01-17 05:06:03'),
(5, 'TK24047', 'Follow-up', 'hdfj', 'hdfj', 'John Smith', '2789654', '2025-12-12', '12:12:00', NULL, 'Lisa Anderson', 'In Progress', '2025-12-12', 1, 'Year', '2025-12-12', '2025-12-12', 'dvv', '2026-01-15 04:57:39', '2026-01-15 04:57:39'),
(6, 'TK24048', 'Payment', 'sharjeel ahmed 123', 'sharjeel ahmed 123', 'David Wilson', '00000000', '2025-12-12', '00:21:00', NULL, 'Lisa Anderson', 'Completed', '2025-12-12', 1, 'Year', '2026-12-12', '2028-12-12', 'gergfvrev', '2026-01-17 05:28:21', '2026-01-17 05:28:21');

-- --------------------------------------------------------

--
-- Table structure for table `tax_returns`
--

CREATE TABLE `tax_returns` (
  `id` bigint UNSIGNED NOT NULL,
  `commission_statement_id` bigint UNSIGNED NOT NULL,
  `tax_ref_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `filing_period` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `filed_on` date DEFAULT NULL,
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `amount_due` decimal(15,2) DEFAULT NULL,
  `amount_paid` decimal(15,2) DEFAULT NULL,
  `supporting_doc_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tax_returns`
--

INSERT INTO `tax_returns` (`id`, `commission_statement_id`, `tax_ref_id`, `filing_period`, `filed_on`, `status`, `amount_due`, `amount_paid`, `supporting_doc_path`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'TAX001', 'Q4-2023', '2024-01-15', 'filed', 115.56, 115.56, NULL, 'Q4 2023 tax filed', '2026-01-13 10:51:09', '2026-01-13 10:51:09'),
(2, 2, 'TAX002', 'Q2-2020', '2020-07-15', 'filed', 354.67, 354.67, NULL, 'Q2 2020 tax filed', '2026-01-13 10:51:09', '2026-01-13 10:51:09'),
(3, 3, 'TAX003', 'Q4-2022', '2023-01-15', 'filed', 58.00, 58.00, NULL, 'Q4 2022 tax filed', '2026-01-13 10:51:09', '2026-01-13 10:51:09'),
(4, 4, 'TAX004', 'Q4-2023', '2024-01-15', 'pending', 43.50, 0.00, NULL, 'Pending tax payment', '2026-01-13 10:51:09', '2026-01-13 10:51:09'),
(5, 5, 'TAX005', 'Q4-2022', '2023-01-15', 'filed', 77.16, 77.16, NULL, 'Q4 2022 tax filed', '2026-01-13 10:51:09', '2026-01-13 10:51:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `role` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'support',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role_id`, `role`, `is_active`, `last_login_at`, `last_login_ip`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', 1, 'support', 1, '2026-01-17 04:44:02', '127.0.0.1', '2026-01-09 02:03:11', '$2y$12$hyKwrpMvQkRYXpBoj30HnOtErCAlesmVgLY3wglkGEU.LCyR4RjQ2', NULL, '2026-01-09 02:03:11', '2026-01-17 04:44:02'),
(2, 'Sarah Johnson', 'sarah.johnson@broker.com', 2, 'support', 1, NULL, NULL, NULL, '$2y$12$hyKwrpMvQkRYXpBoj30HnOtErCAlesmVgLY3wglkGEU.LCyR4RjQ2', NULL, '2026-01-13 10:33:39', '2026-01-13 10:33:39'),
(3, 'Mike Williams', 'mike.williams@broker.com', 2, 'support', 1, NULL, NULL, NULL, '$2y$12$hyKwrpMvQkRYXpBoj30HnOtErCAlesmVgLY3wglkGEU.LCyR4RjQ2', NULL, '2026-01-13 10:33:39', '2026-01-13 10:33:39'),
(4, 'Lisa Anderson', 'lisa.anderson@broker.com', 1, 'support', 1, NULL, NULL, NULL, '$2y$12$hyKwrpMvQkRYXpBoj30HnOtErCAlesmVgLY3wglkGEU.LCyR4RjQ2', NULL, '2026-01-13 10:33:39', '2026-01-13 10:33:39');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint UNSIGNED NOT NULL,
  `vehicle_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_id` bigint UNSIGNED NOT NULL,
  `regn_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `make` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usage` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manufacture_year` year DEFAULT NULL,
  `value` decimal(15,2) DEFAULT NULL,
  `engine` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine_capacity` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chassis_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_from` date DEFAULT NULL,
  `cover_to` date DEFAULT NULL,
  `slta_certificate_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proof_of_purchase_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value_certificate_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_seats` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_color` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle_code`, `policy_id`, `regn_no`, `make`, `model`, `type`, `usage`, `manufacture_year`, `value`, `engine`, `engine_type`, `engine_capacity`, `engine_no`, `chassis_no`, `cover_from`, `cover_to`, `slta_certificate_path`, `proof_of_purchase_path`, `value_certificate_path`, `vehicle_seats`, `vehicle_color`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'VEH001', 1, 'S44444', 'Suzuki', 'Fronx', 'SUV', 'Private', '2023', 390000.00, '1.5L', 'Petrol', '1500cc', 'ENG123456', 'CHS789012', '2023-10-16', '2024-10-15', NULL, NULL, NULL, '5', 'White', 'New vehicle comprehensive', '2026-01-13 10:35:07', '2026-01-13 10:35:07'),
(2, 'VEH002', 6, 'S260', 'Toyota', 'Highlander', 'SUV', 'Private', '2022', 285000.00, '2.4L', 'Hybrid', '2400cc', 'ENG234567', 'CHS890123', '2022-11-15', '2023-11-14', NULL, NULL, NULL, '7', 'Silver', 'Family SUV', '2026-01-13 10:35:07', '2026-01-13 10:35:07'),
(3, 'VEH003', 7, 'S32453', 'Hyundai', 'Creta', 'SUV', 'Private', '2021', 180000.00, '1.6L', 'Petrol', '1600cc', 'ENG345678', 'CHS901234', '2022-09-11', '2023-09-10', NULL, NULL, NULL, '5', 'Blue', 'Third party only coverage', '2026-01-13 10:35:07', '2026-01-13 10:35:07'),
(4, 'VEH004', 1, 'S55678', 'Kia', 'Sportage', 'SUV', 'Commercial', '2022', 320000.00, '2.0L', 'Diesel', '2000cc', 'ENG456789', 'CHS012345', '2024-01-01', '2025-01-01', NULL, NULL, NULL, '5', 'Black', 'Business use', '2026-01-13 10:35:07', '2026-01-13 10:35:07'),
(5, 'VEH005', 6, 'S99012', 'Honda', 'CRV', 'SUV', 'Private', '2023', 450000.00, '1.5T', 'Petrol', '1500cc', 'ENG567890', 'CHS123456', '2023-06-01', '2024-06-01', NULL, NULL, NULL, '5', 'Red', 'Premium SUV', '2026-01-13 10:35:07', '2026-01-13 10:35:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `audit_logs_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `audit_logs_action_index` (`action`);

--
-- Indexes for table `beneficial_owners`
--
ALTER TABLE `beneficial_owners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `beneficial_owners_owner_code_unique` (`owner_code`),
  ADD KEY `beneficial_owners_client_id_foreign` (`client_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `claims`
--
ALTER TABLE `claims`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `claims_claim_id_unique` (`claim_id`),
  ADD KEY `claims_policy_id_foreign` (`policy_id`),
  ADD KEY `claims_client_id_foreign` (`client_id`),
  ADD KEY `claims_vehicle_id_foreign` (`vehicle_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clients_clid_unique` (`clid`);

--
-- Indexes for table `commissions`
--
ALTER TABLE `commissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `commissions_commission_code_unique` (`commission_code`),
  ADD KEY `commissions_commission_note_id_foreign` (`commission_note_id`),
  ADD KEY `commissions_commission_statement_id_foreign` (`commission_statement_id`),
  ADD KEY `commissions_payment_status_id_foreign` (`payment_status_id`),
  ADD KEY `commissions_mode_of_payment_id_foreign` (`mode_of_payment_id`);

--
-- Indexes for table `commission_notes`
--
ALTER TABLE `commission_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `commission_notes_com_note_id_unique` (`com_note_id`),
  ADD KEY `commission_notes_schedule_id_foreign` (`schedule_id`);

--
-- Indexes for table `commission_statements`
--
ALTER TABLE `commission_statements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `commission_statements_com_stat_id_unique` (`com_stat_id`),
  ADD KEY `commission_statements_commission_note_id_foreign` (`commission_note_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contacts_contact_id_unique` (`contact_id`);

--
-- Indexes for table `debit_notes`
--
ALTER TABLE `debit_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `debit_notes_debit_note_no_unique` (`debit_note_no`),
  ADD KEY `debit_notes_payment_plan_id_foreign` (`payment_plan_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `documents_doc_id_unique` (`doc_id`);

--
-- Indexes for table `endorsements`
--
ALTER TABLE `endorsements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `endorsements_endorsement_no_unique` (`endorsement_no`),
  ADD KEY `endorsements_policy_id_foreign` (`policy_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expenses_expense_code_unique` (`expense_code`),
  ADD UNIQUE KEY `expenses_expense_id_unique` (`expense_id`),
  ADD KEY `expenses_category_id_foreign` (`category_id`),
  ADD KEY `expenses_mode_of_payment_id_foreign` (`mode_of_payment_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `followups`
--
ALTER TABLE `followups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `followups_follow_up_code_unique` (`follow_up_code`),
  ADD KEY `followups_contact_id_foreign` (`contact_id`),
  ADD KEY `followups_client_id_foreign` (`client_id`),
  ADD KEY `followups_life_proposal_id_foreign` (`life_proposal_id`),
  ADD KEY `followups_user_id_foreign` (`user_id`);

--
-- Indexes for table `incomes`
--
ALTER TABLE `incomes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `incomes_income_code_unique` (`income_code`),
  ADD KEY `incomes_commission_statement_id_foreign` (`commission_statement_id`),
  ADD KEY `incomes_income_source_id_foreign` (`income_source_id`),
  ADD KEY `incomes_category_id_foreign` (`category_id`),
  ADD KEY `incomes_mode_of_payment_id_foreign` (`mode_of_payment_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `life_proposals`
--
ALTER TABLE `life_proposals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `life_proposals_prid_unique` (`prid`),
  ADD KEY `life_proposals_contact_id_foreign` (`contact_id`),
  ADD KEY `life_proposals_insurer_id_foreign` (`insurer_id`),
  ADD KEY `life_proposals_policy_plan_id_foreign` (`policy_plan_id`),
  ADD KEY `life_proposals_salutation_id_foreign` (`salutation_id`),
  ADD KEY `life_proposals_frequency_id_foreign` (`frequency_id`),
  ADD KEY `life_proposals_proposal_stage_id_foreign` (`proposal_stage_id`),
  ADD KEY `life_proposals_status_id_foreign` (`status_id`),
  ADD KEY `life_proposals_source_of_payment_id_foreign` (`source_of_payment_id`),
  ADD KEY `life_proposals_class_id_foreign` (`class_id`);

--
-- Indexes for table `lookup_categories`
--
ALTER TABLE `lookup_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lookup_categories_name_unique` (`name`);

--
-- Indexes for table `lookup_values`
--
ALTER TABLE `lookup_values`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lookup_values_lookup_category_id_seq_unique` (`lookup_category_id`,`seq`);

--
-- Indexes for table `medicals`
--
ALTER TABLE `medicals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `medicals_medical_code_unique` (`medical_code`),
  ADD KEY `medicals_medical_type_id_foreign` (`medical_type_id`),
  ADD KEY `medicals_status_id_foreign` (`status_id`),
  ADD KEY `medicals_life_proposal_id_status_id_index` (`life_proposal_id`,`status_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nominees`
--
ALTER TABLE `nominees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nominees_nominee_code_unique` (`nominee_code`),
  ADD KEY `nominees_policy_id_foreign` (`policy_id`),
  ADD KEY `nominees_client_id_foreign` (`client_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_payment_reference_unique` (`payment_reference`),
  ADD KEY `payments_debit_note_id_foreign` (`debit_note_id`),
  ADD KEY `payments_mode_of_payment_id_foreign` (`mode_of_payment_id`);

--
-- Indexes for table `payment_plans`
--
ALTER TABLE `payment_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_plans_schedule_id_foreign` (`schedule_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`),
  ADD UNIQUE KEY `permissions_slug_unique` (`slug`);

--
-- Indexes for table `policies`
--
ALTER TABLE `policies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `policies_policy_no_unique` (`policy_no`),
  ADD UNIQUE KEY `policies_policy_code_unique` (`policy_code`),
  ADD KEY `policies_client_id_foreign` (`client_id`),
  ADD KEY `policies_insurer_id_foreign` (`insurer_id`),
  ADD KEY `policies_policy_class_id_foreign` (`policy_class_id`),
  ADD KEY `policies_policy_plan_id_foreign` (`policy_plan_id`),
  ADD KEY `policies_policy_status_id_foreign` (`policy_status_id`),
  ADD KEY `policies_business_type_id_foreign` (`business_type_id`),
  ADD KEY `policies_frequency_id_foreign` (`frequency_id`),
  ADD KEY `policies_pay_plan_lookup_id_foreign` (`pay_plan_lookup_id`),
  ADD KEY `policies_agency_id_foreign` (`agency_id`),
  ADD KEY `policies_channel_id_foreign` (`channel_id`);

--
-- Indexes for table `renewal_notices`
--
ALTER TABLE `renewal_notices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `renewal_notices_rnid_unique` (`rnid`),
  ADD KEY `renewal_notices_policy_id_foreign` (`policy_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_permissions_role_id_permission_id_unique` (`role_id`,`permission_id`),
  ADD KEY `role_permissions_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `schedules_schedule_no_unique` (`schedule_no`),
  ADD KEY `schedules_policy_id_foreign` (`policy_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `statements`
--
ALTER TABLE `statements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `statements_statement_no_unique` (`statement_no`),
  ADD KEY `statements_insurer_id_foreign` (`insurer_id`),
  ADD KEY `statements_mode_of_payment_id_foreign` (`mode_of_payment_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tasks_task_id_unique` (`task_id`);

--
-- Indexes for table `tax_returns`
--
ALTER TABLE `tax_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tax_returns_tax_ref_id_unique` (`tax_ref_id`),
  ADD KEY `tax_returns_commission_statement_id_foreign` (`commission_statement_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicles_vehicle_code_unique` (`vehicle_code`),
  ADD KEY `vehicles_policy_id_foreign` (`policy_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `beneficial_owners`
--
ALTER TABLE `beneficial_owners`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `commissions`
--
ALTER TABLE `commissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `commission_notes`
--
ALTER TABLE `commission_notes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `commission_statements`
--
ALTER TABLE `commission_statements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `debit_notes`
--
ALTER TABLE `debit_notes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `endorsements`
--
ALTER TABLE `endorsements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `followups`
--
ALTER TABLE `followups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `incomes`
--
ALTER TABLE `incomes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `life_proposals`
--
ALTER TABLE `life_proposals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lookup_categories`
--
ALTER TABLE `lookup_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `lookup_values`
--
ALTER TABLE `lookup_values`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=290;

--
-- AUTO_INCREMENT for table `medicals`
--
ALTER TABLE `medicals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `nominees`
--
ALTER TABLE `nominees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payment_plans`
--
ALTER TABLE `payment_plans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `policies`
--
ALTER TABLE `policies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `renewal_notices`
--
ALTER TABLE `renewal_notices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `statements`
--
ALTER TABLE `statements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tax_returns`
--
ALTER TABLE `tax_returns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `beneficial_owners`
--
ALTER TABLE `beneficial_owners`
  ADD CONSTRAINT `beneficial_owners_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `claims`
--
ALTER TABLE `claims`
  ADD CONSTRAINT `claims_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `claims_policy_id_foreign` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `claims_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `commissions`
--
ALTER TABLE `commissions`
  ADD CONSTRAINT `commissions_commission_note_id_foreign` FOREIGN KEY (`commission_note_id`) REFERENCES `commission_notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `commissions_commission_statement_id_foreign` FOREIGN KEY (`commission_statement_id`) REFERENCES `commission_statements` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `commissions_mode_of_payment_id_foreign` FOREIGN KEY (`mode_of_payment_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `commissions_payment_status_id_foreign` FOREIGN KEY (`payment_status_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `commission_notes`
--
ALTER TABLE `commission_notes`
  ADD CONSTRAINT `commission_notes_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `commission_statements`
--
ALTER TABLE `commission_statements`
  ADD CONSTRAINT `commission_statements_commission_note_id_foreign` FOREIGN KEY (`commission_note_id`) REFERENCES `commission_notes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `debit_notes`
--
ALTER TABLE `debit_notes`
  ADD CONSTRAINT `debit_notes_payment_plan_id_foreign` FOREIGN KEY (`payment_plan_id`) REFERENCES `payment_plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `endorsements`
--
ALTER TABLE `endorsements`
  ADD CONSTRAINT `endorsements_policy_id_foreign` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_mode_of_payment_id_foreign` FOREIGN KEY (`mode_of_payment_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `followups`
--
ALTER TABLE `followups`
  ADD CONSTRAINT `followups_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `followups_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `followups_life_proposal_id_foreign` FOREIGN KEY (`life_proposal_id`) REFERENCES `life_proposals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `followups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `incomes`
--
ALTER TABLE `incomes`
  ADD CONSTRAINT `incomes_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incomes_commission_statement_id_foreign` FOREIGN KEY (`commission_statement_id`) REFERENCES `commission_statements` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incomes_income_source_id_foreign` FOREIGN KEY (`income_source_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incomes_mode_of_payment_id_foreign` FOREIGN KEY (`mode_of_payment_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `life_proposals`
--
ALTER TABLE `life_proposals`
  ADD CONSTRAINT `life_proposals_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `life_proposals_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `life_proposals_frequency_id_foreign` FOREIGN KEY (`frequency_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `life_proposals_insurer_id_foreign` FOREIGN KEY (`insurer_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `life_proposals_policy_plan_id_foreign` FOREIGN KEY (`policy_plan_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `life_proposals_proposal_stage_id_foreign` FOREIGN KEY (`proposal_stage_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `life_proposals_salutation_id_foreign` FOREIGN KEY (`salutation_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `life_proposals_source_of_payment_id_foreign` FOREIGN KEY (`source_of_payment_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `life_proposals_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lookup_values`
--
ALTER TABLE `lookup_values`
  ADD CONSTRAINT `lookup_values_lookup_category_id_foreign` FOREIGN KEY (`lookup_category_id`) REFERENCES `lookup_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medicals`
--
ALTER TABLE `medicals`
  ADD CONSTRAINT `medicals_life_proposal_id_foreign` FOREIGN KEY (`life_proposal_id`) REFERENCES `life_proposals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medicals_medical_type_id_foreign` FOREIGN KEY (`medical_type_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `medicals_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `nominees`
--
ALTER TABLE `nominees`
  ADD CONSTRAINT `nominees_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `nominees_policy_id_foreign` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_debit_note_id_foreign` FOREIGN KEY (`debit_note_id`) REFERENCES `debit_notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_mode_of_payment_id_foreign` FOREIGN KEY (`mode_of_payment_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payment_plans`
--
ALTER TABLE `payment_plans`
  ADD CONSTRAINT `payment_plans_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `renewal_notices`
--
ALTER TABLE `renewal_notices`
  ADD CONSTRAINT `renewal_notices_policy_id_foreign` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_policy_id_foreign` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `statements`
--
ALTER TABLE `statements`
  ADD CONSTRAINT `statements_insurer_id_foreign` FOREIGN KEY (`insurer_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `statements_mode_of_payment_id_foreign` FOREIGN KEY (`mode_of_payment_id`) REFERENCES `lookup_values` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tax_returns`
--
ALTER TABLE `tax_returns`
  ADD CONSTRAINT `tax_returns_commission_statement_id_foreign` FOREIGN KEY (`commission_statement_id`) REFERENCES `commission_statements` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_policy_id_foreign` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
