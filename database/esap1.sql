-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 18, 2026 at 04:05 AM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `esap1`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

DROP TABLE IF EXISTS `attributes`;
CREATE TABLE IF NOT EXISTS `attributes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Size', '2024-10-18 12:17:54', '2024-10-18 15:41:03'),
(2, 'Colors', '2024-10-18 15:36:42', '2024-10-20 09:40:35'),
(3, 'Tag', '2024-10-20 10:49:08', '2024-10-20 10:49:08'),
(4, 'Demo', '2024-10-20 10:53:02', '2024-10-26 02:38:32');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `created_at`, `updated_at`) VALUES
(2, 'MIcrosoft', '2024-10-18 15:00:42', '2024-10-26 05:02:55');

-- --------------------------------------------------------

--
-- Table structure for table `campaigns`
--

DROP TABLE IF EXISTS `campaigns`;
CREATE TABLE IF NOT EXISTS `campaigns` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0.00',
  `deadline` date DEFAULT NULL,
  `company` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Created By User',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_user_id_foreign` (`source`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `campaigns`
--

INSERT INTO `campaigns` (`id`, `subject`, `deadline`, `company`, `service`, `description`, `contact`, `source`, `created_at`, `updated_at`, `type`, `status`) VALUES
(1, 'Demo', '2024-10-28', 'Global Innovations', 'Demo Service', 'na', 'cfbdg', 'FB', NULL, '2024-10-28 06:55:34', 'Social', 'Active'),
(2, 'c2', '2024-10-30', 'Global Innovations', 'dvfdxgv', 'dfvgshg', 'dsvdggb', 'dvdfsbg', '2024-10-28 07:08:58', '2024-10-28 07:09:52', 'Email', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'Electronics', NULL, 'Electronics', '2024-10-18 12:07:13', '2024-10-18 13:17:56'),
(2, 'Accessories', NULL, 'USER', '2024-10-18 13:19:19', '2024-10-18 13:19:19'),
(3, 'Others', NULL, '4', '2024-10-18 13:33:21', '2024-10-26 06:39:30'),
(4, 'c2', NULL, '2', '2024-10-20 13:32:42', '2024-10-20 13:54:56'),
(5, 'c1', NULL, '1', '2024-10-26 06:28:36', '2024-10-26 06:28:36');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

DROP TABLE IF EXISTS `feedbacks`;
CREATE TABLE IF NOT EXISTS `feedbacks` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `title`, `product`, `reference`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Demo', 'Demo Product', 'Nai', 'fdbfn dh rthry', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
CREATE TABLE IF NOT EXISTS `leads` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0.00',
  `description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leadStatus` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fullName` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Created By User',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assignedTo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `feedback` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `industry` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_user_id_foreign` (`address`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `title`, `description`, `leadStatus`, `fullName`, `email`, `phone`, `address`, `created_at`, `updated_at`, `company`, `status`, `assignedTo`, `region`, `feedback`, `industry`) VALUES
(1, 'Demo', 'na', 'Active', 'Majed1', 'majed1@gmail.com', '01793445566', 'Dhaka', NULL, '2024-10-28 10:24:54', 'Kimi Hardware', 'Active', '67', 'Bangladesh', NULL, 'Ecommerce'),
(2, 'rr', 'Lead 4', 'Active', 'l23', 'l@g.com', '01979009988', 'dhaka', '2024-10-28 10:36:24', '2024-10-28 10:38:17', 'Tech Solutions', NULL, '67', 'dgvbe', NULL, 'Ecommerce');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2020_12_13_191618_create_products_table', 1),
(5, '2024_10_02_014407_create_roles_table', 2),
(6, '2024_10_02_014817_create_permissions_table', 2),
(7, '2024_10_02_014938_create_role_permission_table', 2),
(8, '2024_10_02_020723_create_role_user_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('anasbinsabiet8@gmail.com', '$2y$10$LLt0rqDQs7gwduQm4V4VLuff7Ix9c8XMncS/nlQIr2th1HxPRvNpa', '2024-10-03 14:38:52'),
('anasbinsabiet@gmail.com', '$2y$10$p8DjTDEteiQJidR2WedCQux3sTawJJzOelbHp5iX/3Rda77ewqnzm', '2024-10-03 14:43:01');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `group`, `module`, `created_at`, `updated_at`) VALUES
(2, 'User Add', 'users', 'USER', '2024-10-08 14:26:00', '2024-10-08 14:26:00'),
(3, 'User Edit', 'users', 'USER', '2024-10-08 14:26:42', '2024-10-08 14:26:42'),
(4, 'User Delete', 'users', 'USER', '2024-10-08 14:26:59', '2024-10-08 14:26:59'),
(5, 'User Approve', 'users', 'CRM', '2024-10-09 13:24:55', '2024-10-13 06:17:41'),
(6, 'g23', 'fdgfhgj', 'USER', '2024-10-13 06:23:44', '2024-10-17 07:00:17'),
(7, 'rt41', 'dve', 'HR', '2024-10-17 07:13:05', '2024-10-18 02:03:13'),
(8, 'dsgvb', 'edsgb', 'HR', '2024-10-18 04:40:13', '2024-10-18 04:40:13'),
(9, 'g237', 'dsget', 'CRM', '2024-10-18 09:22:16', '2024-10-18 11:25:02'),
(10, 'f1', '56', 'HR', '2024-10-19 15:24:06', '2024-10-19 15:24:06'),
(11, 'g5', 'wert', 'CRM', '2024-10-19 15:26:31', '2024-10-19 15:26:31'),
(13, 'per5', 'cfgh', 'CRM', '2024-10-19 15:30:17', '2024-10-27 04:31:06'),
(14, 'f2', 'dvesg', 'USER', '2024-10-27 04:32:40', '2024-10-27 04:32:40');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `actualPrice` double(8,2) NOT NULL DEFAULT '0.00',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sellPrice` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attributes` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL COMMENT 'Created By User',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `actualPrice`, `description`, `sellPrice`, `category`, `attributes`, `unit`, `user_id`, `created_at`, `updated_at`, `code`, `brand`, `size`, `name`, `status`, `thumbnail`) VALUES
(1, 10120.00, 'Description', NULL, NULL, NULL, NULL, 1, '2024-10-01 19:04:31', '2024-10-01 19:04:31', NULL, NULL, NULL, NULL, 'Active', NULL),
(2, 40.00, NULL, '30', 'electronics', NULL, NULL, 7, '2024-10-13 14:45:26', '2024-10-13 14:45:26', '3456', 'nike', 'medium', 'a1', 'Active', NULL),
(3, 40.00, NULL, '30', 'electronics', NULL, NULL, 7, '2024-10-16 14:51:07', '2024-10-16 14:51:07', '345', 'samsung', 'small', 'p3', 'Active', NULL),
(4, 40.00, NULL, '30', 'electronics', NULL, NULL, 7, '2024-10-16 14:52:05', '2024-10-16 14:52:05', '345', 'samsung', 'small', 'p3', 'Active', NULL),
(5, 30.00, NULL, '30', 'electronics', NULL, NULL, 7, '2024-10-16 15:05:21', '2024-10-16 15:05:21', '3450', 'apple', 'small', 'p4', 'Active', NULL),
(6, 30.00, NULL, '50', 'electronics', NULL, NULL, 7, '2024-10-16 15:08:43', '2024-10-17 09:45:10', '345', 'nike', 'small', 'ff34', 'Active', 'uploads/1729112923.png'),
(7, 30.00, 'undefined', '50', 'clothing', NULL, NULL, 7, '2024-10-17 10:18:29', '2024-10-17 10:49:20', '34506654', 'apple', 'small', 'RRR', 'Active', 'uploads/1729183665.png'),
(8, 10.00, 'undefined', '20', '3', NULL, '3', 7, '2024-10-17 10:53:16', '2024-10-18 16:00:39', '23456', 'nike', 'small', 'dfg', 'Active', 'uploads/1729183996.png'),
(9, 30.00, NULL, '40', '2', '4,3', '1', 7, '2024-10-18 06:01:38', '2024-10-20 16:02:21', '2345677', '1', 'small', 'gt5', 'Inactive', 'uploads/1729458200.png'),
(10, 340.00, 'null', '345', '4', NULL, '10', 7, '2024-10-20 17:05:40', '2024-10-26 15:20:55', '100', '1', NULL, '1100456', 'Inactive', 'uploads/1729977655.png'),
(11, 30.00, NULL, '20', '4', NULL, '9', 7, '2024-10-26 14:36:02', '2024-10-26 14:56:21', '345678', '2', NULL, 'eeeeeeeeeeeeeeee', 'Active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `permissions`, `created_at`, `updated_at`) VALUES
(1, 'Admin', '[3,2]', '2024-10-01 19:59:56', '2024-10-09 10:46:47'),
(2, 'Store', '[3,2]', '2024-10-08 08:44:56', '2024-10-08 08:44:56'),
(3, 'Demo', '[3,2]', '2024-10-08 08:51:32', '2024-10-08 08:51:32'),
(7, 'Supervisor', '[3]', '2024-10-09 13:22:35', '2024-10-09 13:24:12'),
(4, 'Challan', '[3,2]', '2024-10-08 12:04:15', '2024-10-08 13:51:48'),
(5, 'GG', '\"[]\"', '2024-10-08 14:14:43', '2024-10-27 06:54:11'),
(6, 'Demo 2', '[3,2]', '2024-10-08 15:23:59', '2024-10-08 15:40:54'),
(8, 'gg4', '[4]', '2024-10-13 05:18:46', '2024-10-13 05:26:06'),
(9, 'Demo 33', '[6,5]', '2024-10-16 09:06:57', '2024-10-17 06:09:11'),
(10, 'gt2', '[]', '2024-10-17 06:10:01', '2024-10-17 06:10:01'),
(11, 'gtr2', '[6,5,7]', '2024-10-17 06:14:01', '2024-10-18 11:06:40'),
(12, 'tt6', '[6]', '2024-10-18 09:32:46', '2024-10-18 09:32:46'),
(13, 'h11', '[11,10,9,8]', '2024-10-18 09:49:26', '2024-10-27 07:05:35'),
(14, 'p', '\"[13]\"', '2024-10-27 08:31:00', '2024-10-27 08:31:00'),
(15, 'dve', '[14,9,8]', '2024-10-27 08:34:36', '2024-10-27 08:34:54');

-- --------------------------------------------------------

--
-- Table structure for table `role_permission`
--

DROP TABLE IF EXISTS `role_permission`;
CREATE TABLE IF NOT EXISTS `role_permission` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` bigint UNSIGNED NOT NULL,
  `permission_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_permission_role_id_foreign` (`role_id`),
  KEY `role_permission_permission_id_foreign` (`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

DROP TABLE IF EXISTS `role_user`;
CREATE TABLE IF NOT EXISTS `role_user` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_user_user_id_foreign` (`user_id`),
  KEY `role_user_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

DROP TABLE IF EXISTS `stocks`;
CREATE TABLE IF NOT EXISTS `stocks` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product` int NOT NULL,
  `warehouse` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`product`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`id`, `product`, `warehouse`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 2, 'd1', '500', '2024-10-18 16:41:37', '2024-10-18 16:49:04'),
(2, 3, '1', '3', '2024-10-18 16:59:55', '2024-10-26 10:12:26'),
(3, 1, '1', '2', '2024-10-26 10:29:20', '2024-10-26 10:29:20');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
CREATE TABLE IF NOT EXISTS `units` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `created_at`, `updated_at`) VALUES
(5, '12', '2024-10-25 10:08:10', '2024-10-25 10:08:10'),
(4, '11', '2024-10-25 10:03:45', '2024-10-25 10:03:45'),
(6, 'cgmfvkj', '2024-10-25 10:26:55', '2024-10-25 10:26:55'),
(7, 'dd', '2024-10-25 10:28:49', '2024-10-25 10:28:49'),
(8, 'dd789', '2024-10-25 10:29:47', '2024-10-25 23:17:03'),
(9, 'd44', '2024-10-25 23:24:41', '2024-10-25 23:24:41'),
(10, 'h3', '2024-10-25 23:53:51', '2024-10-25 23:53:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fullName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobileNo` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `reset_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullName`, `mobileNo`, `email`, `email_verified_at`, `reset_code`, `password`, `roles`, `avatar`, `country`, `address`, `token`, `remember_token`, `created_at`, `updated_at`, `status`) VALUES
(7, 'Anas', '01793478194', 'anasbinsabiet@gmail.com', NULL, '980051', '$2y$10$GHInB/0FUqvByLG/ZgMNTux/4JA2ehZB1IkBzbZWnW7uJaUawZ/CG', '[7, 6, 5, 4, 3, 2, 1]', 'uploads/1729066331.png', 'BD', 'dhaka', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTczMDIyNTg1OSwiZXhwIjoxNzMwMjI5NDU5LCJuYmYiOjE3MzAyMjU4NTksImp0aSI6IlFFWnJrSTVPRHZLVTVpbEMiLCJzdWIiOjcsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.vmWCeZ4AWSrjqkJATZSsjQOauTXuNXoFSXTl4MZdE4M', NULL, '2024-10-03 06:30:40', '2024-10-29 12:17:39', 'Inactive'),
(65, 'Demo 6', '01717785553', 'd5@gmail.com', NULL, NULL, '$2y$10$XYl6kyLgmprJowIfrow/8.aHrBJI79EbUaIrQkiRDar.IhKLXHzZe', '[9]', 'uploads/1729156517.png', 'BD', 'dhaka', NULL, NULL, '2024-10-17 03:15:17', '2024-10-17 03:43:47', 'Active'),
(63, NULL, NULL, 'd1@gmail.com', NULL, NULL, '$2y$10$1hjdPU9piXVDLgjEDCuur.OLm/F9ooDlcZWM3xuGEJfGKjIWlnZM2', NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-16 05:57:37', '2024-10-16 12:55:46', 'Active'),
(64, NULL, NULL, 'd2@gmail.com', NULL, NULL, '$2y$10$4tf3QWQDczfZ28oo4QCYnOLfsPG/Nr0ojhKV7iTOz1d9f8tK7s4My', NULL, 'uploads/1729079901.png', NULL, NULL, NULL, NULL, '2024-10-16 05:58:21', '2024-10-17 06:51:49', 'Active'),
(66, 'df31', '01979009988', 'df3@gmail.com', NULL, NULL, '$2y$10$Y3/RqrHQ1BYzQ/bWB0xfSe1t.mMgRw.MpSkxOmzef7sKViE5NXZ.W', '[9, 8, 7, 6, 5, 4, 3, 2]', 'uploads/1729238666.jpg', 'BD', 'dhaka', NULL, NULL, '2024-10-17 04:46:28', '2024-10-18 06:22:24', 'Active'),
(67, 'h21', '12345678901', 'h11@g.com', NULL, NULL, '$2y$10$fcvaj4LdQquoL.VJOB0zw.qU7DwFTA2ggcY5b85igTeTlXYYdJKwS', '[12, 13]', 'uploads/1729269816.jpg', 'BD', 'sdbdg', NULL, NULL, '2024-10-18 10:04:16', '2024-10-20 03:16:55', 'Inactive'),
(68, '1', '1234567654', 'a@s.com', NULL, NULL, '$2y$10$RleBmRrdBSllpCMSa72yxeLwktXYvbKpiHOFKDY/9T99j9gReRP8K', '[9]', 'uploads/1730046418.png', 'BD', 'Dhaka', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTcyOTM2NTI4MCwiZXhwIjoxNzI5MzY4ODgwLCJuYmYiOjE3MjkzNjUyODAsImp0aSI6Ijl2eHpwMXJwS3VGRE9BVEsiLCJzdWIiOjY4LCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.IRwaNe2ZduumaByJcTpKF4e06b7nbaKH2Yaq1dmupGQ', NULL, '2024-10-19 13:14:03', '2024-10-27 10:30:07', NULL),
(69, '1', '11234567890', 'a@g.com', NULL, '314973', '$2y$10$yllb4pPGUrGGh1k68SWcKukG86e.uA4z4PrW64yVpsOtaMDCP7hP2', NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-19 13:23:40', '2024-10-19 13:23:40', NULL),
(70, '1', '1234567890', 'a1@g.com', NULL, NULL, '$2y$10$redD/y57FSIy1vCTXLxb/OkbvIw7FCq9A2AmszGzsRS5cveEfJUli', NULL, NULL, NULL, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTcyOTM2NTk4OSwiZXhwIjoxNzI5MzY5NTg5LCJuYmYiOjE3MjkzNjU5ODksImp0aSI6IlNmS2h4UXNzMzJNMzhqNFciLCJzdWIiOjcwLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.ZlZCVidNwYdfGl63fb3l17sE4CDx0-JfdambdVs3Zks', NULL, '2024-10-19 13:26:08', '2024-10-19 13:26:29', NULL),
(71, 'Anas Ahmed', '01717785553', 'anasbinsabiet333@gmail.com', NULL, NULL, '$2y$10$97d7bvp5Gi7xYBnRQ0Y7WuNRaYBP.d.OYW5K2cpvhotIWQ3is5nWS', '[13, 12, 11, 10, 9, 8, 7, 6, 5, 4]', NULL, 'BD', 'dhaka', NULL, NULL, '2024-10-20 03:33:28', '2024-10-20 03:33:28', 'Active'),
(72, 'Anas Ahmed', '01717785553', 'anasbinsabiet3333@gmail.com', NULL, NULL, '$2y$10$Ck09JgyK6NFeh6aMWNpg4.IZSZ5xyymWpIXDqSOzB3qLdxbg9O6nS', '[13, 12, 11, 10, 9, 8, 7, 6, 5, 4]', 'uploads/1729416822.jpg', 'BD', 'dhaka', NULL, NULL, '2024-10-20 03:33:42', '2024-10-20 03:33:42', 'Active'),
(73, 'Anas Ahmed', '01717785553', 'anasbinsabieter@gmail.com', NULL, NULL, '$2y$10$rfUZY91O5u9CvNby6JRHf./B.kMg5EtF3XK9iNq2A5Uu.FND0IuMi', '[12, 10]', 'uploads/1730047756.png', 'BD', NULL, NULL, NULL, '2024-10-27 10:49:16', '2024-10-27 10:49:16', 'Active'),
(62, 'Anas1', '01793478194', 'anas@gmail.com', NULL, NULL, '$2y$10$k0IJ4zJfeaeeX8DH83usluwV8kLNgqV4wlb48mU46L72Xh742anUO', '[8, 7, 6, 5, 4, 3, 2, 1]', 'uploads/1729079729.png', 'BD', 'Dahaka', NULL, NULL, '2024-10-16 02:13:21', '2024-10-16 14:11:20', 'Active');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
