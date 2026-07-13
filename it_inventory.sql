-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260707.3e756d69dd
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 13, 2026 at 03:25 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `it_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `ip_address`, `created_at`, `updated_at`) VALUES
(1, 2, 'update', 'mereset Password Login untuk akun: nathan', '10.126.27.173', '2026-07-10 17:31:08', '2026-07-10 17:31:08'),
(2, 2, 'create', 'menambahkan master departemen baru: Accounting (HO)', '10.126.27.173', '2026-07-10 17:31:47', '2026-07-10 17:31:47'),
(3, 2, 'create', 'menambahkan data IP Accounting untuk Nathanaeel Chrystian Prasetyo', '10.126.27.173', '2026-07-10 17:32:59', '2026-07-10 17:32:59'),
(4, 2, 'update', 'mereset Password Login untuk akun: IT Support', '10.126.27.173', '2026-07-10 17:51:50', '2026-07-10 17:51:50'),
(5, 2, 'update', 'mereset Vault PIN untuk akun: IT Support', '10.126.27.173', '2026-07-10 17:52:09', '2026-07-10 17:52:09'),
(6, 2, 'create', 'menambahkan data IP Accounting untuk Test', '10.126.27.173', '2026-07-10 19:35:41', '2026-07-10 19:35:41'),
(7, 2, 'update', 'mengubah data IP Accounting untuk Test', '10.126.27.173', '2026-07-10 19:36:10', '2026-07-10 19:36:10'),
(8, 2, 'update', 'mengubah data IP Accounting untuk Test', '10.126.27.173', '2026-07-10 19:36:56', '2026-07-10 19:36:56'),
(9, 2, 'delete', 'menghapus data inventory untuk Test', '10.126.27.173', '2026-07-10 19:37:08', '2026-07-10 19:37:08'),
(10, 2, 'update', 'mengubah data IP Accounting untuk Nathanaeel Chrystian Prasetyo', '10.126.27.173', '2026-07-10 19:38:53', '2026-07-10 19:38:53'),
(11, 2, 'update', 'mengubah data IP Accounting untuk Nathanaeel Chrystian Prasetyo', '10.126.27.173', '2026-07-10 19:39:10', '2026-07-10 19:39:10');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-da4b9237bacccdf19c0760cab7aec4a8359010b0', 'i:3;', 1783741186),
('laravel-cache-da4b9237bacccdf19c0760cab7aec4a8359010b0:timer', 'i:1783741186;', 1783741186),
('laravel-cache-switch_statuses_all', 'a:51:{i:1;s:6:\"online\";i:16;s:6:\"online\";i:19;s:6:\"online\";i:10;s:7:\"offline\";i:26;s:6:\"online\";i:7;s:6:\"online\";i:2;s:6:\"online\";i:24;s:6:\"online\";i:37;s:6:\"online\";i:27;s:7:\"offline\";i:38;s:6:\"online\";i:6;s:6:\"online\";i:4;s:7:\"offline\";i:25;s:6:\"online\";i:29;s:6:\"online\";i:30;s:6:\"online\";i:31;s:6:\"online\";i:3;s:6:\"online\";i:28;s:6:\"online\";i:35;s:6:\"online\";i:39;s:6:\"online\";i:36;s:6:\"online\";i:33;s:6:\"online\";i:34;s:6:\"online\";i:32;s:6:\"online\";i:12;s:6:\"online\";i:20;s:6:\"online\";i:22;s:6:\"online\";i:17;s:6:\"online\";i:15;s:7:\"offline\";i:23;s:6:\"online\";i:11;s:6:\"online\";i:8;s:6:\"online\";i:5;s:6:\"online\";i:40;s:6:\"online\";i:41;s:6:\"online\";i:42;s:6:\"online\";i:43;s:6:\"online\";i:44;s:6:\"online\";i:18;s:6:\"online\";i:9;s:6:\"online\";i:45;s:6:\"online\";i:47;s:6:\"online\";i:13;s:6:\"online\";i:14;s:6:\"online\";i:46;s:6:\"online\";i:21;s:6:\"online\";i:48;s:6:\"online\";i:49;s:6:\"online\";i:50;s:6:\"online\";i:51;s:6:\"online\";}', 1783904505);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_switches`
--

CREATE TABLE `data_switches` (
  `id` bigint UNSIGNED NOT NULL,
  `panel_id` bigint UNSIGNED DEFAULT NULL,
  `merk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `data_switches`
--

INSERT INTO `data_switches` (`id`, `panel_id`, `merk`, `ip_address`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'HP Procurve switch 4208-96 VL SWITCH (J8775B)', '10.100.27.1', 'Core Switch', '2026-07-09 18:35:33', '2026-07-09 18:35:33'),
(2, 1, 'HP ProCurve Switch 2510-48', '10.100.27.15', '48 Ports', '2026-07-09 18:37:15', '2026-07-09 18:37:15'),
(3, 2, 'HP 2530-24-PoEP Switch (J9779A)', '10.100.27.27', '24 Ports', '2026-07-09 18:38:04', '2026-07-09 18:38:04'),
(4, 2, 'HP 2530-24 Switch (J9782A)', '10.100.27.210', '48 Ports', '2026-07-09 18:38:31', '2026-07-09 18:41:02'),
(5, 3, 'HP 2530-48-PoEP Switch (J9778A)', '10.100.27.41', '48 ports', '2026-07-09 18:39:54', '2026-07-09 18:39:54'),
(6, 3, 'HP 2530-48 Switch (J9781A)', '10.100.27.201', '48 ports', '2026-07-09 18:42:05', '2026-07-09 18:42:05'),
(7, 4, 'HP 2530-24 Switch (J9782A)', '10.100.27.13', '24 ports', '2026-07-09 18:42:33', '2026-07-09 18:42:33'),
(8, 5, 'HP 2530-24-PoEP Switch (J9779A)', '10.100.27.40', '24 ports', '2026-07-09 18:43:10', '2026-07-09 19:34:24'),
(9, 6, 'HP 2510-48 Switch (J9020A)', '10.100.27.5', '48 port', '2026-07-09 19:09:32', '2026-07-09 19:09:32'),
(10, 6, 'HP 2530-24G-PoE+-2SFP+ Switch (J9854A)', '10.100.27.12', '24 Ports', '2026-07-09 19:10:14', '2026-07-09 19:10:14'),
(11, 7, 'HP 2530-24G-PoE+-2SFP+ Switch (J9854A)', '10.100.27.4', '24 Ports', '2026-07-09 19:14:25', '2026-07-09 19:14:40'),
(12, 7, 'HP 2530-24-PoEP Switch (J9779A)', '10.100.27.34', '24 Ports', '2026-07-09 19:15:02', '2026-07-09 19:15:02'),
(13, 8, 'HP 2530-24-PoEP Switch (J9779A)', '10.100.27.56', '24 Ports', '2026-07-09 19:15:39', '2026-07-09 19:15:39'),
(14, 8, 'HP 2530-8-PoEP Switch (JL070A)', '10.100.27.58', '8 Ports', '2026-07-09 19:16:02', '2026-07-09 19:16:02'),
(15, 9, 'HP 2530-24-PoEP Switch (J9779A)', '10.100.27.38', '24 Ports', '2026-07-09 19:16:39', '2026-07-09 19:16:39'),
(16, 10, 'ProCurve Switch 2510-48', '10.100.27.10', '48 Ports', '2026-07-09 19:17:31', '2026-07-09 19:17:31'),
(17, 10, 'HP 2530-24-PoEP Switch (J9779A)', '10.100.27.37', '24 Ports', '2026-07-09 19:17:55', '2026-07-09 19:17:55'),
(18, 10, 'HP 2530-8-PoEP Switch (J9780A)', '10.100.27.47', '8 Ports', '2026-07-09 19:18:21', '2026-07-09 19:18:21'),
(19, 11, 'HP 2530-8-PoEP Switch (J9780A)', '10.100.27.11', '8 Ports', '2026-07-09 19:19:04', '2026-07-09 19:19:04'),
(20, 12, 'HP 2530-24-PoEP Switch (J9779A)', '10.100.27.35', '24 Ports', '2026-07-09 19:19:29', '2026-07-09 19:19:29'),
(21, 13, 'ProCurve Switch 2510-48  ( J9020A)', '10.100.27.6', '48 Ports', '2026-07-09 19:19:55', '2026-07-09 19:19:55'),
(22, 13, 'HP 2530-24-PoEP Switch (J9779A)', '10.100.27.36', '24 Ports', '2026-07-09 19:21:52', '2026-07-09 19:21:52'),
(23, 14, 'HP 2530-24-PoEP Switch (J9779A)', '10.100.27.39', '24 Ports', '2026-07-09 19:22:20', '2026-07-09 19:22:20'),
(24, 15, 'HP E2620-48 Switch(J9626A)', '10.100.27.16', '48 Ports', '2026-07-09 19:22:41', '2026-07-09 19:22:41'),
(25, 16, 'SF300-24 24-Port 10/100', '10.100.27.22', NULL, '2026-07-09 19:24:21', '2026-07-09 19:24:21'),
(26, 18, 'HP 2510-48 Switch (J9020A)', '10.100.27.122', '48 Ports', '2026-07-09 19:26:01', '2026-07-09 19:26:01'),
(27, 19, 'HP 2530-8-PoEP Switch (J9780A)', '10.100.27.177', '8 Ports', '2026-07-09 19:27:20', '2026-07-09 19:27:20'),
(28, 20, 'Cisco SF302-08PP 8-Port 10/100 PoE+ Managed Switch', '10.100.27.28', '8 Ports', '2026-07-09 19:27:53', '2026-07-09 19:27:53'),
(29, 21, 'Cisco SF300-24PP 24-Port 10/100 PoE+ Managed Switch', '10.100.27.24', '24 Ports', '2026-07-09 19:29:12', '2026-07-09 19:30:22'),
(30, 21, 'Cisco SF300-24 24-Port 10/100 Managed Switch', '10.100.27.25', '24 Ports', '2026-07-09 19:30:46', '2026-07-09 19:30:46'),
(31, 21, 'Cisco SF300-24PP 24-Port 10/100 PoE+ Managed Switch', '10.100.27.26', '24 Ports', '2026-07-09 19:31:08', '2026-07-09 19:31:08'),
(32, 22, 'Cisco SF302-08PP 8-Port 10/100 PoE+ Managed Switch', '10.100.27.33', '8 Ports', '2026-07-09 19:31:38', '2026-07-09 19:31:38'),
(33, 23, 'Cisco SF300-24 24-Port 10/100 Managed Switch', '10.100.27.31', '24 Ports', '2026-07-09 19:32:25', '2026-07-09 19:32:25'),
(34, 23, 'Cisco SF300-24PP 24-Port 10/100 PoE+ Managed Switch', '10.100.27.32', '24 Ports', '2026-07-09 19:32:45', '2026-07-09 19:32:45'),
(35, 24, 'Cisco SF302-08PP 8-Port 10/100 PoE+ Managed Switch', '10.100.27.29', '8 Ports', '2026-07-09 19:33:11', '2026-07-09 19:33:11'),
(36, 24, 'Cisco SF300-24PP 24-Port 10/100 PoE+ Managed Switch', '10.100.27.30', '24 Ports', '2026-07-09 19:34:53', '2026-07-09 19:34:53'),
(37, 19, 'HP 2530-8-PoEP Switch (J9780A)', '10.100.27.17', NULL, '2026-07-10 00:59:17', '2026-07-10 00:59:17'),
(38, 14, 'ProCurve Switch 2510-48 (j9020a)', '10.100.27.200', NULL, '2026-07-10 01:07:33', '2026-07-10 01:07:33'),
(39, 5, 'HP 2530-24G-PoE+-2SFP+ Switch (J9854A)', '10.100.27.3', NULL, '2026-07-10 01:11:05', '2026-07-10 01:11:05'),
(40, 23, 'HP 2530-8-PoEP Switch (JL070A)', '10.100.27.42', NULL, '2026-07-10 01:13:48', '2026-07-10 01:13:48'),
(41, 20, 'HP 2530-8-PoEP Switch (JL070A)', '10.100.27.43', NULL, '2026-07-10 01:16:12', '2026-07-10 01:16:12'),
(42, 6, 'HP 2530-48-PoEP Switch (J9778A)', '10.100.27.44', NULL, '2026-07-10 01:23:52', '2026-07-10 01:23:52'),
(43, 25, 'HP 2530-8-PoEP Switch (JL070A)', '10.100.27.45', NULL, '2026-07-10 01:30:48', '2026-07-10 01:30:48'),
(44, 25, 'HP 2530-8-PoEP Switch (JL070A)', '10.100.27.46', NULL, '2026-07-10 01:32:08', '2026-07-10 01:32:08'),
(45, 6, 'ProCurve Switch 2510-48 (j9020a', '10.100.27.50', NULL, '2026-07-10 01:37:45', '2026-07-10 01:37:45'),
(46, 2, 'HP 2530-24 Switch (J9782A)', '10.100.27.59', NULL, '2026-07-10 01:41:47', '2026-07-10 01:41:47'),
(47, 25, 'HP 2530-24-PoEP Switch (J9779A)', '10.100.27.51', NULL, '2026-07-10 01:43:44', '2026-07-10 01:43:44'),
(48, 25, '6000 24G CL4 4SFP Swch', '10.100.27.60', NULL, '2026-07-10 01:53:24', '2026-07-10 01:53:24'),
(49, 25, '6000 24G CL4 4SFP Swch', '10.100.27.61', NULL, '2026-07-10 01:56:24', '2026-07-10 01:56:24'),
(50, 15, 'HP 2620-48 Switch (J9626A)', '10.100.27.65', NULL, '2026-07-10 02:01:04', '2026-07-10 02:01:04'),
(51, 17, 'HP 2530-8-PoEP Switch (JL070A)', '10.100.27.90', NULL, '2026-07-10 02:03:34', '2026-07-10 02:03:34');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `unit`, `created_at`, `updated_at`) VALUES
(1, 'Accounting', 'HO', '2026-07-10 17:31:47', '2026-07-10 17:31:47');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventories`
--

CREATE TABLE `inventories` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_karyawan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username_ad` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_asset_pc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_id` bigint UNSIGNED NOT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_remote` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventories`
--

INSERT INTO `inventories` (`id`, `nama_user`, `id_karyawan`, `username_ad`, `nomor_asset_pc`, `department_id`, `ip_address`, `password_remote`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Nathanaeel Chrystian Prasetyo', '2345465646', 'nathanael.prasetyo', '456546', 1, '10.126.27.173', 'eyJpdiI6IjVLdGQvSE14K1FzNzVDa1VtbDl2T1E9PSIsInZhbHVlIjoiT0R0N0xtUWd6OHBXZ2N5SGQwaWZKdz09IiwibWFjIjoiMjc3YjI5Y2U2YjBjMzJjZGQzZTA0ZTU3NzM3MzM4ZjIyMmY0MGViNWU3MmRiNGU5YzUyOWE1Zjk3OWZmYTk0NiIsInRhZyI6IiJ9', 'SD', 2, '2026-07-10 17:32:59', '2026-07-10 19:39:10');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_07_08_044100_create_departments_table', 1),
(5, '2026_07_08_044141_create_inventories_table', 1),
(6, '2026_07_08_044150_create_activity_logs_table', 1),
(7, '2026_07_08_085135_add_vault_pin_to_users_table', 1),
(8, '2026_07_09_034011_add_unit_to_departments_table', 1),
(9, '2026_07_09_042059_add_profile_fields_to_inventories_table', 1),
(10, '2026_07_09_075531_create_panels_table', 1),
(11, '2026_07_09_075538_create_data_switches_table', 1),
(12, '2026_07_09_094749_drop_switch_id_from_data_switches_table', 1),
(13, '2026_07_11_011910_change_cascade_on_delete_to_restrict_on_inventory', 2),
(14, '2026_07_11_021309_make_password_remote_nullable_on_inventories_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `panels`
--

CREATE TABLE `panels` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `panels`
--

INSERT INTO `panels` (`id`, `name`, `location`, `created_at`, `updated_at`) VALUES
(1, 'Panel Ruang Server 2', 'Lantai 2', '2026-07-09 18:28:02', '2026-07-10 00:45:49'),
(2, 'Panel Teknik BP', NULL, '2026-07-09 18:28:17', '2026-07-09 18:28:17'),
(3, 'Panel  GMP Oil', NULL, '2026-07-09 18:28:37', '2026-07-09 18:28:37'),
(4, 'Panel Gudang SP', NULL, '2026-07-09 18:28:48', '2026-07-09 18:28:48'),
(5, 'Panel CP Loader', NULL, '2026-07-09 18:29:01', '2026-07-09 18:29:01'),
(6, 'Panel Kasie Bumbu', NULL, '2026-07-09 18:29:25', '2026-07-09 18:29:25'),
(7, 'Panel Retort', NULL, '2026-07-09 18:29:47', '2026-07-09 18:29:47'),
(8, 'Panel Freeze Dry', NULL, '2026-07-09 18:29:57', '2026-07-09 18:29:57'),
(9, 'Panel Security', NULL, '2026-07-09 18:30:04', '2026-07-09 18:30:04'),
(10, 'Panel Exs Gudang FG/GD Garam', NULL, '2026-07-09 18:30:15', '2026-07-09 18:30:15'),
(11, 'Panel Timbangan Mobil', NULL, '2026-07-09 18:30:30', '2026-07-09 18:30:30'),
(12, 'Panel Topack', NULL, '2026-07-09 18:30:38', '2026-07-09 18:30:38'),
(13, 'Panel QC BP', NULL, '2026-07-09 18:30:46', '2026-07-09 18:30:46'),
(14, 'Panel FG BP Baru', NULL, '2026-07-09 18:31:00', '2026-07-09 18:31:00'),
(15, 'PAnel PDQC Basement', NULL, '2026-07-09 18:31:11', '2026-07-09 18:31:11'),
(16, 'Panel PDQC LT 3', 'Lantai 3', '2026-07-09 18:31:25', '2026-07-09 18:31:25'),
(17, 'Panel LT Mezanine', NULL, '2026-07-09 18:31:35', '2026-07-09 18:31:35'),
(18, 'Panel Lobi Ingredient', NULL, '2026-07-09 18:31:52', '2026-07-09 18:31:52'),
(19, 'Panel Workshop Ingredient', NULL, '2026-07-09 18:32:06', '2026-07-09 18:32:06'),
(20, 'Panel Ing Lantai 1', 'Depan Ruang QC Lantai 1', '2026-07-09 18:32:33', '2026-07-10 00:46:54'),
(21, 'Panel Ing Lantai 2 HR', NULL, '2026-07-09 18:32:42', '2026-07-10 00:48:29'),
(22, 'Panel Ing Lantai 2', 'Dekat Tangga', '2026-07-09 18:33:00', '2026-07-10 00:50:59'),
(23, 'Panel Ing Lantai 3', 'Ruang Teknik Panel', '2026-07-09 18:33:16', '2026-07-10 00:52:02'),
(24, 'Panel Ing LT 4', 'Dekat Lift', '2026-07-09 18:33:31', '2026-07-10 00:52:39'),
(25, 'Panel Cadangan', NULL, '2026-07-10 01:30:15', '2026-07-10 01:30:15');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('6gJ2lSaByc3OTExuRuy7cneR0dc3aD4WF7HUcvB2', NULL, '10.123.27.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoieWR3dnZ5enhGZWtPRFNCNjlYcFhvdTJzbGFDMGtmSXhDTVBTV21DbCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MjoiaHR0cDovLzEwLjEyNi4yNy4xNzM6ODAwMC9zd2l0Y2htb25pdG9yaW5nIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly8xMC4xMjYuMjcuMTczOjgwMDAvc3dpdGNobW9uaXRvcmluZyI7fX0=', 1783912936),
('kvXZm5waL1qPYNCuBihCHazI8iVg7ExiS3gc3nWo', NULL, '10.126.27.173', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibHFGdEs2NEdIRHdIZHN2VGFaZWY0Z1Y5VTk2VnBtNVdRZG1zQ0J3WiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMC4xMjYuMjcuMTczOjgwMDAvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1783904033),
('pgH0eTlPzBqHAugROMu3eVKI8wD08iw33Uzsl1bJ', 2, '10.126.27.173', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSUFxOXJEY1V3aktIb29Rc1d2cmZZS2NiSDdDdTNtNGMyNElvSDVObCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMC4xMjYuMjcuMTczOjgwMDAvZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1783904507),
('rBYskSM6sLcNbjIYhkBFSZsPj3liWk8fPJXTJB0Y', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUW52V1FoOUE1ZUIzVjZVWU52NUZPUW9TZkxCSVdHN1NwT0VXQzZYVCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9pdC1pbnZlbnRvcnktc3lzdGVtLnRlc3QvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1783904101);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `id_karyawan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username_ad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vault_pin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `id_karyawan`, `username_ad`, `name`, `email`, `role`, `password`, `vault_pin`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, '50180670', 'nathanael.prasetyo', 'nathan', NULL, 'IT Support', NULL, NULL, NULL, '2026-07-09 18:15:50', '2026-07-09 18:15:50'),
(2, '11223344', 'it.support', 'IT Support', NULL, 'Super Admin', '$2y$12$s2f2JlFapbZ0HQVHvMLLO.Kmh5alfJmfmMpcquJuNMxyu./YtYdLq', '$2y$12$WX69klUxVVDL.CGZ5nGW0uWKZ69pV37i22dw.eKMBCVJzAcxvXzWu', NULL, '2026-07-09 18:15:50', '2026-07-10 17:52:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

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
-- Indexes for table `data_switches`
--
ALTER TABLE `data_switches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `data_switches_ip_address_unique` (`ip_address`),
  ADD KEY `data_switches_panel_id_foreign` (`panel_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_name_unit_unique` (`name`,`unit`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inventories`
--
ALTER TABLE `inventories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventories_created_by_foreign` (`created_by`),
  ADD KEY `inventories_department_id_foreign` (`department_id`);

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `panels`
--
ALTER TABLE `panels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_id_karyawan_unique` (`id_karyawan`),
  ADD UNIQUE KEY `users_username_ad_unique` (`username_ad`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `data_switches`
--
ALTER TABLE `data_switches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventories`
--
ALTER TABLE `inventories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `panels`
--
ALTER TABLE `panels`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `data_switches`
--
ALTER TABLE `data_switches`
  ADD CONSTRAINT `data_switches_panel_id_foreign` FOREIGN KEY (`panel_id`) REFERENCES `panels` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `inventories`
--
ALTER TABLE `inventories`
  ADD CONSTRAINT `inventories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventories_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
