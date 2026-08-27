-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 23, 2026 at 11:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `invenium_assist`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `module` varchar(100) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(10) UNSIGNED NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `company_name`, `contact_name`, `email`, `phone`, `created_at`, `updated_at`) VALUES
(1, 'Invenium Technologies', 'Daniel Marisa', 'daniel@inveniumtech.com', '0123456789', '2026-08-22 10:38:41', '2026-08-22 10:38:41'),
(2, 'Aurabloom Colletions Pty Ltd', 'Tshegofatso Tefo', 'admin@aurabloomcollection.com', '0123456789', '2026-08-22 17:12:41', '2026-08-22 17:12:41');

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `hostname` varchar(255) DEFAULT NULL,
  `device_name` varchar(255) DEFAULT NULL,
  `operating_system` varchar(255) DEFAULT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `mac_address` varchar(100) DEFAULT NULL,
  `local_ip` varchar(50) DEFAULT NULL,
  `public_ip` varchar(50) DEFAULT NULL,
  `fqdn` varchar(255) DEFAULT NULL,
  `monitoring_url` varchar(2048) DEFAULT NULL,
  `agent_version` varchar(50) DEFAULT NULL,
  `status` enum('online','offline','unknown') DEFAULT 'unknown',
  `last_seen` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`id`, `customer_id`, `hostname`, `device_name`, `operating_system`, `serial_number`, `mac_address`, `local_ip`, `public_ip`, `fqdn`, `monitoring_url`, `agent_version`, `status`, `last_seen`, `created_at`, `updated_at`) VALUES
(1, 1, 'INV-TEST-PC', 'TEST PC', 'Windows 11 Pro', 'TEST-SERIAL-001', '00:11:22:33:44:55', '192.168.1.100', '', NULL, NULL, '1.0.0', 'unknown', NULL, '2026-08-22 12:05:50', '2026-08-22 12:19:25'),
(2, 2, 'ABC - Web Server - 01', 'Afrihost - Web Server', 'Linux - Ubuntu', 'aurabloomcollection.com', 'aurabloomcollection.com', '197.242.159.147', '197.242.159.147', NULL, NULL, '1.0.0', 'online', '2026-08-23 13:41:06', '2026-08-22 17:17:32', '2026-08-23 11:41:06'),
(3, 1, 'Invenium-Web-Server', 'Invenium Web Server', 'Linux - Ubuntu', '', '', '197.221.14.18', '197.221.14.18', 'inveniumtech.com', 'https://www.inveniumtech.com', '1.0.0', 'online', '2026-08-23 13:41:06', '2026-08-22 18:58:54', '2026-08-23 11:41:06');

-- --------------------------------------------------------

--
-- Table structure for table `device_monitoring`
--

CREATE TABLE `device_monitoring` (
  `id` int(10) UNSIGNED NOT NULL,
  `device_id` int(10) UNSIGNED NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `interval_seconds` int(10) UNSIGNED NOT NULL DEFAULT 60,
  `timeout_seconds` int(10) UNSIGNED NOT NULL DEFAULT 10,
  `last_check_at` datetime DEFAULT NULL,
  `next_check_at` datetime DEFAULT NULL,
  `current_status` enum('online','offline','unknown') NOT NULL DEFAULT 'unknown',
  `current_latency_ms` int(10) UNSIGNED DEFAULT NULL,
  `consecutive_failures` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `consecutive_successes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `outage_started_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `device_monitoring`
--

INSERT INTO `device_monitoring` (`id`, `device_id`, `enabled`, `interval_seconds`, `timeout_seconds`, `last_check_at`, `next_check_at`, `current_status`, `current_latency_ms`, `consecutive_failures`, `consecutive_successes`, `outage_started_at`, `created_at`, `updated_at`) VALUES
(3, 2, 1, 120, 10, '2026-08-23 13:41:06', '2026-08-23 13:43:06', 'online', 39, 0, 34, NULL, '2026-08-23 07:36:24', '2026-08-23 11:41:06'),
(4, 3, 1, 60, 10, '2026-08-23 13:41:06', '2026-08-23 13:42:06', 'online', 52, 0, 131, NULL, '2026-08-23 08:51:49', '2026-08-23 11:41:06');

-- --------------------------------------------------------

--
-- Table structure for table `monitoring_checks`
--

CREATE TABLE `monitoring_checks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `device_id` int(10) UNSIGNED NOT NULL,
  `checked_at` datetime NOT NULL,
  `status` enum('online','offline','unknown') NOT NULL DEFAULT 'unknown',
  `latency_ms` int(10) UNSIGNED DEFAULT NULL,
  `error_code` varchar(100) DEFAULT NULL,
  `error_message` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `monitoring_checks`
--

INSERT INTO `monitoring_checks` (`id`, `device_id`, `checked_at`, `status`, `latency_ms`, `error_code`, `error_message`, `created_at`) VALUES
(7, 2, '2026-08-23 09:37:01', 'online', 55, NULL, NULL, '2026-08-23 07:37:01'),
(8, 2, '2026-08-23 09:41:17', 'online', 42, NULL, NULL, '2026-08-23 07:41:17'),
(9, 2, '2026-08-23 09:42:20', 'online', 49, NULL, NULL, '2026-08-23 07:42:20'),
(10, 2, '2026-08-23 09:44:44', 'offline', NULL, 'TCP_10065', 'A socket operation was attempted to an unreachable host', '2026-08-23 07:44:44'),
(11, 2, '2026-08-23 09:45:50', 'offline', NULL, 'TCP_10065', 'A socket operation was attempted to an unreachable host', '2026-08-23 07:45:50'),
(12, 2, '2026-08-23 09:46:58', 'offline', NULL, 'CONNECTION_FAILED', 'php_network_getaddresses: getaddrinfo for ABC - Web Server - 01 failed: No such host is known. ', '2026-08-23 07:46:58'),
(13, 2, '2026-08-23 09:48:14', 'online', 58, NULL, NULL, '2026-08-23 07:48:14'),
(14, 2, '2026-08-23 09:50:16', 'online', 60, NULL, NULL, '2026-08-23 07:50:16'),
(15, 2, '2026-08-23 09:51:20', 'offline', NULL, 'CONNECTION_FAILED', 'php_network_getaddresses: getaddrinfo for ABC - Web Server - 01 failed: No such host is known. ', '2026-08-23 07:51:20'),
(16, 2, '2026-08-23 09:53:03', 'online', 52, NULL, NULL, '2026-08-23 07:53:03'),
(17, 2, '2026-08-23 10:05:13', 'online', 39, NULL, NULL, '2026-08-23 08:05:13'),
(18, 2, '2026-08-23 10:20:56', 'online', 23, NULL, NULL, '2026-08-23 08:20:56'),
(19, 2, '2026-08-23 10:26:32', 'online', 51, NULL, NULL, '2026-08-23 08:26:32'),
(20, 2, '2026-08-23 10:29:15', 'online', 37, NULL, NULL, '2026-08-23 08:29:15'),
(21, 2, '2026-08-23 10:30:26', 'online', 62, NULL, NULL, '2026-08-23 08:30:26'),
(22, 2, '2026-08-23 10:31:41', 'online', 68, NULL, NULL, '2026-08-23 08:31:41'),
(23, 2, '2026-08-23 10:50:57', 'online', 59, NULL, NULL, '2026-08-23 08:50:57'),
(24, 3, '2026-08-23 10:52:03', 'online', 67, NULL, NULL, '2026-08-23 08:52:03'),
(25, 2, '2026-08-23 10:52:03', 'online', 59, NULL, NULL, '2026-08-23 08:52:03'),
(26, 2, '2026-08-23 10:53:08', 'online', 44, NULL, NULL, '2026-08-23 08:53:08'),
(27, 3, '2026-08-23 10:53:08', 'online', 53, NULL, NULL, '2026-08-23 08:53:08'),
(28, 3, '2026-08-23 10:58:21', 'online', 76, NULL, NULL, '2026-08-23 08:58:21'),
(29, 2, '2026-08-23 10:58:21', 'online', 62, NULL, NULL, '2026-08-23 08:58:21'),
(30, 3, '2026-08-23 11:02:42', 'online', 85, NULL, NULL, '2026-08-23 09:02:42'),
(31, 2, '2026-08-23 11:02:42', 'online', 53, NULL, NULL, '2026-08-23 09:02:42'),
(32, 3, '2026-08-23 11:06:39', 'online', 72, NULL, NULL, '2026-08-23 09:06:39'),
(33, 2, '2026-08-23 11:06:39', 'online', 56, NULL, NULL, '2026-08-23 09:06:39'),
(34, 3, '2026-08-23 11:13:50', 'online', 69, NULL, NULL, '2026-08-23 09:13:50'),
(35, 2, '2026-08-23 11:13:50', 'online', 56, NULL, NULL, '2026-08-23 09:13:50'),
(36, 3, '2026-08-23 11:19:55', 'online', 61, NULL, NULL, '2026-08-23 09:19:55'),
(37, 2, '2026-08-23 11:19:55', 'online', 65, NULL, NULL, '2026-08-23 09:19:55'),
(38, 3, '2026-08-23 11:21:38', 'online', 59, NULL, NULL, '2026-08-23 09:21:38'),
(39, 2, '2026-08-23 11:22:05', 'online', 17, NULL, NULL, '2026-08-23 09:22:05'),
(40, 3, '2026-08-23 11:23:06', 'online', 81, NULL, NULL, '2026-08-23 09:23:06'),
(41, 2, '2026-08-23 11:24:06', 'online', 43, NULL, NULL, '2026-08-23 09:24:06'),
(42, 3, '2026-08-23 11:24:06', 'online', 55, NULL, NULL, '2026-08-23 09:24:06'),
(43, 3, '2026-08-23 11:25:06', 'online', 61, NULL, NULL, '2026-08-23 09:25:06'),
(44, 2, '2026-08-23 11:26:06', 'online', 58, NULL, NULL, '2026-08-23 09:26:06'),
(45, 3, '2026-08-23 11:26:06', 'online', 70, NULL, NULL, '2026-08-23 09:26:06'),
(46, 3, '2026-08-23 11:27:06', 'online', 66, NULL, NULL, '2026-08-23 09:27:06'),
(47, 2, '2026-08-23 11:28:06', 'online', 37, NULL, NULL, '2026-08-23 09:28:06'),
(48, 3, '2026-08-23 11:28:06', 'online', 41, NULL, NULL, '2026-08-23 09:28:06'),
(49, 3, '2026-08-23 11:29:06', 'online', 76, NULL, NULL, '2026-08-23 09:29:06'),
(50, 2, '2026-08-23 11:30:06', 'online', 27, NULL, NULL, '2026-08-23 09:30:06'),
(51, 3, '2026-08-23 11:30:06', 'online', 48, NULL, NULL, '2026-08-23 09:30:06'),
(52, 3, '2026-08-23 11:39:06', 'online', 75, NULL, NULL, '2026-08-23 09:39:06'),
(53, 2, '2026-08-23 11:39:06', 'online', 30, NULL, NULL, '2026-08-23 09:39:06'),
(54, 3, '2026-08-23 11:40:06', 'online', 78, NULL, NULL, '2026-08-23 09:40:06'),
(55, 2, '2026-08-23 11:41:06', 'online', 50, NULL, NULL, '2026-08-23 09:41:06'),
(56, 3, '2026-08-23 11:41:06', 'online', 54, NULL, NULL, '2026-08-23 09:41:06'),
(57, 3, '2026-08-23 11:42:06', 'online', 51, NULL, NULL, '2026-08-23 09:42:06'),
(58, 2, '2026-08-23 11:43:06', 'online', 42, NULL, NULL, '2026-08-23 09:43:06'),
(59, 3, '2026-08-23 11:43:06', 'online', 42, NULL, NULL, '2026-08-23 09:43:06'),
(60, 3, '2026-08-23 11:44:06', 'online', 54, NULL, NULL, '2026-08-23 09:44:06'),
(61, 2, '2026-08-23 11:45:06', 'online', 30, NULL, NULL, '2026-08-23 09:45:06'),
(62, 3, '2026-08-23 11:45:06', 'online', 48, NULL, NULL, '2026-08-23 09:45:06'),
(63, 3, '2026-08-23 11:46:06', 'online', 79, NULL, NULL, '2026-08-23 09:46:06'),
(64, 2, '2026-08-23 11:47:06', 'online', 51, NULL, NULL, '2026-08-23 09:47:06'),
(65, 3, '2026-08-23 11:47:06', 'online', 49, NULL, NULL, '2026-08-23 09:47:06'),
(66, 3, '2026-08-23 11:48:06', 'online', 70, NULL, NULL, '2026-08-23 09:48:06'),
(67, 2, '2026-08-23 11:49:06', 'online', 47, NULL, NULL, '2026-08-23 09:49:06'),
(68, 3, '2026-08-23 11:49:06', 'online', 58, NULL, NULL, '2026-08-23 09:49:06'),
(69, 3, '2026-08-23 11:50:06', 'online', 57, NULL, NULL, '2026-08-23 09:50:06'),
(70, 2, '2026-08-23 11:51:06', 'online', 18, NULL, NULL, '2026-08-23 09:51:06'),
(71, 3, '2026-08-23 11:51:06', 'online', 49, NULL, NULL, '2026-08-23 09:51:06'),
(72, 3, '2026-08-23 11:52:06', 'online', 44, NULL, NULL, '2026-08-23 09:52:06'),
(73, 2, '2026-08-23 11:53:06', 'online', 69, NULL, NULL, '2026-08-23 09:53:06'),
(74, 3, '2026-08-23 11:53:06', 'online', 43, NULL, NULL, '2026-08-23 09:53:06'),
(75, 3, '2026-08-23 11:54:06', 'online', 38, NULL, NULL, '2026-08-23 09:54:06'),
(76, 2, '2026-08-23 11:55:06', 'online', 48, NULL, NULL, '2026-08-23 09:55:06'),
(77, 3, '2026-08-23 11:55:06', 'online', 44, NULL, NULL, '2026-08-23 09:55:06'),
(78, 3, '2026-08-23 11:56:06', 'online', 58, NULL, NULL, '2026-08-23 09:56:06'),
(79, 2, '2026-08-23 11:57:06', 'online', 34, NULL, NULL, '2026-08-23 09:57:06'),
(80, 3, '2026-08-23 11:57:06', 'online', 49, NULL, NULL, '2026-08-23 09:57:06'),
(81, 3, '2026-08-23 11:58:06', 'online', 69, NULL, NULL, '2026-08-23 09:58:06'),
(82, 2, '2026-08-23 11:59:06', 'online', 30, NULL, NULL, '2026-08-23 09:59:06'),
(83, 3, '2026-08-23 11:59:06', 'online', 38, NULL, NULL, '2026-08-23 09:59:06'),
(84, 3, '2026-08-23 12:00:06', 'online', 69, NULL, NULL, '2026-08-23 10:00:06'),
(85, 2, '2026-08-23 12:01:06', 'online', 45, NULL, NULL, '2026-08-23 10:01:06'),
(86, 3, '2026-08-23 12:01:06', 'online', 51, NULL, NULL, '2026-08-23 10:01:06'),
(87, 3, '2026-08-23 12:02:06', 'online', 58, NULL, NULL, '2026-08-23 10:02:06'),
(88, 2, '2026-08-23 12:03:06', 'online', 31, NULL, NULL, '2026-08-23 10:03:06'),
(89, 3, '2026-08-23 12:03:06', 'online', 51, NULL, NULL, '2026-08-23 10:03:06'),
(90, 3, '2026-08-23 12:04:06', 'online', 47, NULL, NULL, '2026-08-23 10:04:06'),
(91, 2, '2026-08-23 12:05:06', 'online', 39, NULL, NULL, '2026-08-23 10:05:06'),
(92, 3, '2026-08-23 12:05:06', 'online', 51, NULL, NULL, '2026-08-23 10:05:06'),
(93, 3, '2026-08-23 12:06:06', 'online', 52, NULL, NULL, '2026-08-23 10:06:06'),
(94, 2, '2026-08-23 12:07:06', 'online', 34, NULL, NULL, '2026-08-23 10:07:06'),
(95, 3, '2026-08-23 12:07:06', 'online', 61, NULL, NULL, '2026-08-23 10:07:06'),
(96, 3, '2026-08-23 12:08:06', 'online', 74, NULL, NULL, '2026-08-23 10:08:06'),
(97, 2, '2026-08-23 12:09:06', 'online', 56, NULL, NULL, '2026-08-23 10:09:06'),
(98, 3, '2026-08-23 12:09:06', 'online', 53, NULL, NULL, '2026-08-23 10:09:06'),
(99, 3, '2026-08-23 12:10:06', 'online', 65, NULL, NULL, '2026-08-23 10:10:06'),
(100, 2, '2026-08-23 12:11:06', 'online', 45, NULL, NULL, '2026-08-23 10:11:06'),
(101, 3, '2026-08-23 12:11:06', 'online', 72, NULL, NULL, '2026-08-23 10:11:06'),
(102, 3, '2026-08-23 12:12:06', 'online', 54, NULL, NULL, '2026-08-23 10:12:06'),
(103, 2, '2026-08-23 12:13:06', 'online', 38, NULL, NULL, '2026-08-23 10:13:06'),
(104, 3, '2026-08-23 12:13:06', 'online', 45, NULL, NULL, '2026-08-23 10:13:06'),
(105, 3, '2026-08-23 12:14:06', 'online', 43, NULL, NULL, '2026-08-23 10:14:06'),
(106, 2, '2026-08-23 12:15:06', 'online', 77, NULL, NULL, '2026-08-23 10:15:06'),
(107, 3, '2026-08-23 12:15:06', 'online', 62, NULL, NULL, '2026-08-23 10:15:06'),
(108, 3, '2026-08-23 12:16:06', 'online', 73, NULL, NULL, '2026-08-23 10:16:06'),
(109, 2, '2026-08-23 12:17:06', 'online', 42, NULL, NULL, '2026-08-23 10:17:06'),
(110, 3, '2026-08-23 12:17:06', 'online', 50, NULL, NULL, '2026-08-23 10:17:06'),
(111, 3, '2026-08-23 12:18:06', 'online', 40, NULL, NULL, '2026-08-23 10:18:06'),
(112, 2, '2026-08-23 12:19:06', 'online', 70, NULL, NULL, '2026-08-23 10:19:06'),
(113, 3, '2026-08-23 12:19:06', 'online', 35, NULL, NULL, '2026-08-23 10:19:06'),
(114, 3, '2026-08-23 12:20:06', 'online', 71, NULL, NULL, '2026-08-23 10:20:06'),
(115, 2, '2026-08-23 12:21:06', 'online', 46, NULL, NULL, '2026-08-23 10:21:06'),
(116, 3, '2026-08-23 12:21:06', 'online', 50, NULL, NULL, '2026-08-23 10:21:06'),
(117, 3, '2026-08-23 12:22:06', 'online', 63, NULL, NULL, '2026-08-23 10:22:06'),
(118, 2, '2026-08-23 12:23:06', 'online', 58, NULL, NULL, '2026-08-23 10:23:06'),
(119, 3, '2026-08-23 12:23:06', 'online', 39, NULL, NULL, '2026-08-23 10:23:06'),
(120, 3, '2026-08-23 12:24:06', 'online', 58, NULL, NULL, '2026-08-23 10:24:06'),
(121, 2, '2026-08-23 12:25:06', 'online', 46, NULL, NULL, '2026-08-23 10:25:06'),
(122, 3, '2026-08-23 12:25:06', 'online', 45, NULL, NULL, '2026-08-23 10:25:06'),
(123, 3, '2026-08-23 12:26:06', 'online', 52, NULL, NULL, '2026-08-23 10:26:06'),
(124, 2, '2026-08-23 12:27:06', 'online', 29, NULL, NULL, '2026-08-23 10:27:06'),
(125, 3, '2026-08-23 12:27:06', 'online', 52, NULL, NULL, '2026-08-23 10:27:06'),
(126, 3, '2026-08-23 12:28:06', 'online', 85, NULL, NULL, '2026-08-23 10:28:06'),
(127, 2, '2026-08-23 12:29:06', 'offline', NULL, 'CONNECTION_FAILED', 'php_network_getaddresses: getaddrinfo for ABC - Web Server - 01 failed: No such host is known. ', '2026-08-23 10:29:06'),
(128, 3, '2026-08-23 12:29:06', 'online', 73, NULL, NULL, '2026-08-23 10:29:06'),
(129, 3, '2026-08-23 12:30:06', 'online', 42, NULL, NULL, '2026-08-23 10:30:06'),
(130, 2, '2026-08-23 12:31:06', 'online', 43, NULL, NULL, '2026-08-23 10:31:06'),
(131, 3, '2026-08-23 12:31:07', 'online', 45, NULL, NULL, '2026-08-23 10:31:07'),
(132, 3, '2026-08-23 12:33:06', 'online', 42, NULL, NULL, '2026-08-23 10:33:06'),
(133, 2, '2026-08-23 12:33:07', 'online', 55, NULL, NULL, '2026-08-23 10:33:07'),
(134, 3, '2026-08-23 12:34:07', 'online', 80, NULL, NULL, '2026-08-23 10:34:07'),
(135, 2, '2026-08-23 12:36:06', 'online', 53, NULL, NULL, '2026-08-23 10:36:06'),
(136, 3, '2026-08-23 12:36:07', 'online', 49, NULL, NULL, '2026-08-23 10:36:07'),
(137, 3, '2026-08-23 12:38:07', 'online', 52, NULL, NULL, '2026-08-23 10:38:07'),
(138, 2, '2026-08-23 12:38:07', 'online', 59, NULL, NULL, '2026-08-23 10:38:07'),
(139, 3, '2026-08-23 12:40:07', 'online', 86, NULL, NULL, '2026-08-23 10:40:07'),
(140, 2, '2026-08-23 12:41:07', 'online', 53, NULL, NULL, '2026-08-23 10:41:07'),
(141, 3, '2026-08-23 12:42:07', 'online', 63, NULL, NULL, '2026-08-23 10:42:07'),
(142, 2, '2026-08-23 12:44:07', 'online', 65, NULL, NULL, '2026-08-23 10:44:07'),
(143, 3, '2026-08-23 12:44:07', 'online', 49, NULL, NULL, '2026-08-23 10:44:07'),
(144, 3, '2026-08-23 12:45:07', 'online', 51, NULL, NULL, '2026-08-23 10:45:07'),
(145, 2, '2026-08-23 12:46:07', 'online', 29, NULL, NULL, '2026-08-23 10:46:07'),
(146, 3, '2026-08-23 12:46:07', 'online', 46, NULL, NULL, '2026-08-23 10:46:07'),
(147, 3, '2026-08-23 12:47:07', 'online', 73, NULL, NULL, '2026-08-23 10:47:07'),
(148, 2, '2026-08-23 12:48:07', 'online', 39, NULL, NULL, '2026-08-23 10:48:07'),
(149, 3, '2026-08-23 12:48:07', 'online', 54, NULL, NULL, '2026-08-23 10:48:07'),
(150, 3, '2026-08-23 12:49:07', 'online', 67, NULL, NULL, '2026-08-23 10:49:07'),
(151, 2, '2026-08-23 12:50:07', 'online', 44, NULL, NULL, '2026-08-23 10:50:07'),
(152, 3, '2026-08-23 12:50:07', 'online', 53, NULL, NULL, '2026-08-23 10:50:07'),
(153, 3, '2026-08-23 12:51:07', 'online', 46, NULL, NULL, '2026-08-23 10:51:07'),
(154, 2, '2026-08-23 12:52:07', 'online', 26, NULL, NULL, '2026-08-23 10:52:07'),
(155, 3, '2026-08-23 12:52:07', 'online', 51, NULL, NULL, '2026-08-23 10:52:07'),
(156, 3, '2026-08-23 12:53:07', 'online', 83, NULL, NULL, '2026-08-23 10:53:07'),
(157, 2, '2026-08-23 12:54:07', 'online', 70, NULL, NULL, '2026-08-23 10:54:07'),
(158, 3, '2026-08-23 12:54:07', 'online', 75, NULL, NULL, '2026-08-23 10:54:07'),
(159, 3, '2026-08-23 12:55:07', 'online', 67, NULL, NULL, '2026-08-23 10:55:07'),
(160, 2, '2026-08-23 12:56:07', 'online', 45, NULL, NULL, '2026-08-23 10:56:07'),
(161, 3, '2026-08-23 12:56:07', 'online', 52, NULL, NULL, '2026-08-23 10:56:07'),
(162, 3, '2026-08-23 12:57:07', 'online', 66, NULL, NULL, '2026-08-23 10:57:07'),
(163, 2, '2026-08-23 12:58:07', 'online', 24, NULL, NULL, '2026-08-23 10:58:07'),
(164, 3, '2026-08-23 12:58:07', 'online', 56, NULL, NULL, '2026-08-23 10:58:07'),
(165, 3, '2026-08-23 12:59:07', 'online', 81, NULL, NULL, '2026-08-23 10:59:07'),
(166, 2, '2026-08-23 13:00:07', 'online', 19, NULL, NULL, '2026-08-23 11:00:07'),
(167, 3, '2026-08-23 13:00:07', 'online', 51, NULL, NULL, '2026-08-23 11:00:07'),
(168, 3, '2026-08-23 13:01:08', 'online', 68, NULL, NULL, '2026-08-23 11:01:08'),
(169, 2, '2026-08-23 13:02:07', 'online', 38, NULL, NULL, '2026-08-23 11:02:07'),
(170, 3, '2026-08-23 13:03:07', 'online', 56, NULL, NULL, '2026-08-23 11:03:07'),
(171, 2, '2026-08-23 13:05:06', 'online', 48, NULL, NULL, '2026-08-23 11:05:06'),
(172, 3, '2026-08-23 13:05:06', 'online', 50, NULL, NULL, '2026-08-23 11:05:06'),
(173, 3, '2026-08-23 13:06:06', 'online', 49, NULL, NULL, '2026-08-23 11:06:06'),
(174, 2, '2026-08-23 13:07:06', 'online', 23, NULL, NULL, '2026-08-23 11:07:06'),
(175, 3, '2026-08-23 13:07:06', 'online', 52, NULL, NULL, '2026-08-23 11:07:06'),
(176, 3, '2026-08-23 13:08:06', 'online', 88, NULL, NULL, '2026-08-23 11:08:06'),
(177, 2, '2026-08-23 13:09:06', 'online', 66, NULL, NULL, '2026-08-23 11:09:06'),
(178, 3, '2026-08-23 13:09:06', 'online', 71, NULL, NULL, '2026-08-23 11:09:06'),
(179, 3, '2026-08-23 13:10:06', 'online', 43, NULL, NULL, '2026-08-23 11:10:06'),
(180, 2, '2026-08-23 13:11:06', 'online', 49, NULL, NULL, '2026-08-23 11:11:06'),
(181, 3, '2026-08-23 13:11:06', 'online', 47, NULL, NULL, '2026-08-23 11:11:06'),
(182, 3, '2026-08-23 13:12:06', 'online', 73, NULL, NULL, '2026-08-23 11:12:06'),
(183, 2, '2026-08-23 13:13:06', 'online', 46, NULL, NULL, '2026-08-23 11:13:06'),
(184, 3, '2026-08-23 13:13:06', 'online', 66, NULL, NULL, '2026-08-23 11:13:06'),
(185, 3, '2026-08-23 13:14:06', 'online', 47, NULL, NULL, '2026-08-23 11:14:06'),
(186, 2, '2026-08-23 13:15:06', 'online', 56, NULL, NULL, '2026-08-23 11:15:06'),
(187, 3, '2026-08-23 13:15:06', 'online', 45, NULL, NULL, '2026-08-23 11:15:06'),
(188, 3, '2026-08-23 13:16:06', 'online', 58, NULL, NULL, '2026-08-23 11:16:06'),
(189, 2, '2026-08-23 13:17:06', 'online', 40, NULL, NULL, '2026-08-23 11:17:06'),
(190, 3, '2026-08-23 13:17:06', 'online', 59, NULL, NULL, '2026-08-23 11:17:06'),
(191, 3, '2026-08-23 13:18:06', 'online', 54, NULL, NULL, '2026-08-23 11:18:06'),
(192, 2, '2026-08-23 13:19:06', 'online', 39, NULL, NULL, '2026-08-23 11:19:06'),
(193, 3, '2026-08-23 13:19:06', 'online', 47, NULL, NULL, '2026-08-23 11:19:06'),
(194, 3, '2026-08-23 13:20:06', 'online', 40, NULL, NULL, '2026-08-23 11:20:06'),
(195, 2, '2026-08-23 13:21:06', 'online', 64, NULL, NULL, '2026-08-23 11:21:06'),
(196, 3, '2026-08-23 13:21:06', 'online', 70, NULL, NULL, '2026-08-23 11:21:06'),
(197, 3, '2026-08-23 13:22:06', 'online', 72, NULL, NULL, '2026-08-23 11:22:06'),
(198, 2, '2026-08-23 13:23:06', 'online', 49, NULL, NULL, '2026-08-23 11:23:06'),
(199, 3, '2026-08-23 13:23:06', 'online', 52, NULL, NULL, '2026-08-23 11:23:06'),
(200, 3, '2026-08-23 13:24:06', 'online', 73, NULL, NULL, '2026-08-23 11:24:06'),
(201, 2, '2026-08-23 13:25:06', 'online', 35, NULL, NULL, '2026-08-23 11:25:06'),
(202, 3, '2026-08-23 13:25:06', 'online', 53, NULL, NULL, '2026-08-23 11:25:06'),
(203, 3, '2026-08-23 13:26:06', 'online', 42, NULL, NULL, '2026-08-23 11:26:06'),
(204, 2, '2026-08-23 13:27:06', 'online', 49, NULL, NULL, '2026-08-23 11:27:06'),
(205, 3, '2026-08-23 13:27:06', 'online', 58, NULL, NULL, '2026-08-23 11:27:06'),
(206, 3, '2026-08-23 13:28:06', 'online', 56, NULL, NULL, '2026-08-23 11:28:06'),
(207, 2, '2026-08-23 13:29:06', 'online', 26, NULL, NULL, '2026-08-23 11:29:06'),
(208, 3, '2026-08-23 13:29:06', 'online', 48, NULL, NULL, '2026-08-23 11:29:06'),
(209, 3, '2026-08-23 13:30:06', 'online', 44, NULL, NULL, '2026-08-23 11:30:06'),
(210, 2, '2026-08-23 13:31:06', 'online', 53, NULL, NULL, '2026-08-23 11:31:06'),
(211, 3, '2026-08-23 13:31:06', 'online', 58, NULL, NULL, '2026-08-23 11:31:06'),
(212, 3, '2026-08-23 13:32:06', 'online', 77, NULL, NULL, '2026-08-23 11:32:06'),
(213, 2, '2026-08-23 13:33:06', 'online', 55, NULL, NULL, '2026-08-23 11:33:06'),
(214, 3, '2026-08-23 13:33:06', 'online', 37, NULL, NULL, '2026-08-23 11:33:06'),
(215, 3, '2026-08-23 13:34:06', 'online', 59, NULL, NULL, '2026-08-23 11:34:06'),
(216, 2, '2026-08-23 13:35:06', 'online', 42, NULL, NULL, '2026-08-23 11:35:06'),
(217, 3, '2026-08-23 13:35:06', 'online', 56, NULL, NULL, '2026-08-23 11:35:06'),
(218, 3, '2026-08-23 13:36:06', 'online', 35, NULL, NULL, '2026-08-23 11:36:06'),
(219, 2, '2026-08-23 13:37:06', 'online', 80, NULL, NULL, '2026-08-23 11:37:06'),
(220, 3, '2026-08-23 13:37:06', 'online', 69, NULL, NULL, '2026-08-23 11:37:06'),
(221, 3, '2026-08-23 13:38:06', 'online', 75, NULL, NULL, '2026-08-23 11:38:06'),
(222, 2, '2026-08-23 13:39:06', 'online', 51, NULL, NULL, '2026-08-23 11:39:06'),
(223, 3, '2026-08-23 13:39:06', 'online', 45, NULL, NULL, '2026-08-23 11:39:06'),
(224, 3, '2026-08-23 13:40:06', 'online', 62, NULL, NULL, '2026-08-23 11:40:06'),
(225, 2, '2026-08-23 13:41:06', 'online', 39, NULL, NULL, '2026-08-23 11:41:06'),
(226, 3, '2026-08-23 13:41:06', 'online', 52, NULL, NULL, '2026-08-23 11:41:06');

-- --------------------------------------------------------

--
-- Table structure for table `monitoring_incidents`
--

CREATE TABLE `monitoring_incidents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `device_id` int(10) UNSIGNED NOT NULL,
  `started_at` datetime NOT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `duration_seconds` bigint(20) UNSIGNED DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `status` enum('open','resolved') NOT NULL DEFAULT 'open',
  `notes` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `monitoring_incidents`
--

INSERT INTO `monitoring_incidents` (`id`, `device_id`, `started_at`, `resolved_at`, `duration_seconds`, `reason`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(3, 2, '2026-08-23 09:44:44', '2026-08-23 09:48:14', 210, 'A socket operation was attempted to an unreachable host', 'resolved', '', '2026-08-23 07:44:44', '2026-08-23 07:48:14'),
(4, 2, '2026-08-23 09:51:20', '2026-08-23 09:53:03', 103, 'php_network_getaddresses: getaddrinfo for ABC - Web Server - 01 failed: No such host is known. ', 'resolved', '', '2026-08-23 07:51:20', '2026-08-23 07:53:03'),
(5, 2, '2026-08-23 12:29:06', '2026-08-23 12:31:06', 120, 'php_network_getaddresses: getaddrinfo for ABC - Web Server - 01 failed: No such host is known. ', 'resolved', '', '2026-08-23 10:29:06', '2026-08-23 10:31:06');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'System Owner', '2026-08-06 17:48:28', NULL),
(2, 'Administrator', 'System Administrator', '2026-08-06 17:48:28', NULL),
(3, 'Technician', 'Support Technician', '2026-08-06 17:48:28', NULL),
(4, 'Customer', 'Customer Portal User', '2026-08-06 17:48:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `session_uuid` char(36) NOT NULL,
  `technician_id` int(10) UNSIGNED DEFAULT NULL,
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `device_id` int(10) UNSIGNED DEFAULT NULL,
  `session_token` char(64) NOT NULL,
  `status` enum('created','downloaded','waiting','connected','disconnected','closed','expired') DEFAULT 'created',
  `expires_at` datetime DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `ended_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(150) DEFAULT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `technicians`
--

CREATE TABLE `technicians` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `display_name` varchar(150) NOT NULL,
  `status` enum('available','busy','away','offline') DEFAULT 'offline',
  `heartbeat_at` datetime DEFAULT NULL,
  `current_sessions` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive','locked') DEFAULT 'active',
  `failed_logins` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `first_name`, `last_name`, `email`, `username`, `password`, `status`, `failed_logins`, `locked_until`, `last_login`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 2, 'Daniel', 'Marisa', 'daniel@inveniumtech.com', 'daniel@inveniumtech.com', '$2y$10$Ma257j0W6FGKywdcYMEVCOneajqEtaR26D3z/K.3pw/XTyD4rGs7S', 'active', 0, NULL, '2026-08-23 12:43:33', NULL, '2026-08-10 23:11:51', '2026-08-23 10:43:33'),
(2, 3, 'Jorge', 'Marisa', 'jorge@inveniumtech.com', 'jorge@inveniumtech.com', '$2y$10$mppxSrPavK/oA05/QPPVdOYyZ5q1tgBG7i.c59wZO/GugLhAeqMaK', 'active', 0, NULL, '2026-08-23 12:40:31', NULL, '2026-08-22 20:05:49', '2026-08-23 10:40:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_audit_user` (`user_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_device_customer` (`customer_id`);

--
-- Indexes for table `device_monitoring`
--
ALTER TABLE `device_monitoring`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_device_monitoring_device_id` (`device_id`),
  ADD KEY `idx_device_monitoring_status` (`current_status`),
  ADD KEY `idx_device_monitoring_next_check` (`next_check_at`),
  ADD KEY `idx_device_monitoring_enabled` (`enabled`);

--
-- Indexes for table `monitoring_checks`
--
ALTER TABLE `monitoring_checks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_monitoring_checks_device_time` (`device_id`,`checked_at`),
  ADD KEY `idx_monitoring_checks_status` (`status`);

--
-- Indexes for table `monitoring_incidents`
--
ALTER TABLE `monitoring_incidents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_monitoring_incidents_device_time` (`device_id`,`started_at`),
  ADD KEY `idx_monitoring_incidents_status` (`status`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reset_user` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_uuid` (`session_uuid`),
  ADD KEY `fk_session_technician` (`technician_id`),
  ADD KEY `fk_session_customer` (`customer_id`),
  ADD KEY `fk_session_device` (`device_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `technicians`
--
ALTER TABLE `technicians`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `device_monitoring`
--
ALTER TABLE `device_monitoring`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `monitoring_checks`
--
ALTER TABLE `monitoring_checks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=227;

--
-- AUTO_INCREMENT for table `monitoring_incidents`
--
ALTER TABLE `monitoring_incidents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `technicians`
--
ALTER TABLE `technicians`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `fk_device_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `device_monitoring`
--
ALTER TABLE `device_monitoring`
  ADD CONSTRAINT `fk_device_monitoring_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `monitoring_checks`
--
ALTER TABLE `monitoring_checks`
  ADD CONSTRAINT `fk_monitoring_checks_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `monitoring_incidents`
--
ALTER TABLE `monitoring_incidents`
  ADD CONSTRAINT `fk_monitoring_incidents_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_reset_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `fk_session_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `fk_session_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`),
  ADD CONSTRAINT `fk_session_technician` FOREIGN KEY (`technician_id`) REFERENCES `technicians` (`id`);

--
-- Constraints for table `technicians`
--
ALTER TABLE `technicians`
  ADD CONSTRAINT `fk_technician_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
