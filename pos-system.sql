-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for pos_inventory_system
CREATE DATABASE IF NOT EXISTS `pos_inventory_system` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `pos_inventory_system`;

-- Dumping structure for table pos_inventory_system.activity_logs
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `action` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_activity_user` (`user_id`),
  CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pos_inventory_system.activity_logs: ~0 rows (approximately)
REPLACE INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `created_at`) VALUES
	(4, NULL, 'logout', 'User logged out.', '2026-04-17 16:23:45'),
	(5, 1, 'login', 'User logged in.', '2026-04-17 16:24:36'),
	(6, 1, 'setup_organization', 'Saved organization information.', '2026-04-17 16:28:21'),
	(7, 1, 'setup_admin', 'Completed first-time admin account setup.', '2026-04-17 16:29:01'),
	(8, 1, 'category_create', 'Added category: Heavy Equipment Parts', '2026-04-17 16:29:59'),
	(9, 1, 'product_create', 'Added product: Break Pad', '2026-04-17 16:30:17'),
	(10, 1, 'sale_create', 'Completed sale INV-20260418003132-714 for Walk-in Customer - ₱3,500.00', '2026-04-17 16:31:32'),
	(11, 1, 'logout', 'User logged out.', '2026-04-17 16:32:45'),
	(12, 1, 'login', 'User logged in.', '2026-04-17 16:32:59'),
	(13, 1, 'user_create', 'Created user: vincentpaulb', '2026-04-17 16:33:07'),
	(14, 1, 'user_update', 'Updated user ID: 2', '2026-04-17 16:33:15'),
	(15, 1, 'logout', 'User logged out.', '2026-04-17 16:33:16'),
	(16, 2, 'login', 'User logged in.', '2026-04-17 16:33:18'),
	(17, 2, 'logout', 'User logged out.', '2026-04-17 16:39:24'),
	(18, 1, 'login', 'User logged in.', '2026-04-17 16:39:31'),
	(19, 1, 'user_create', 'Created user: cdbarruga', '2026-04-17 16:49:49'),
	(20, 1, 'logout', 'User logged out.', '2026-04-17 16:50:04'),
	(21, 3, 'login', 'User logged in.', '2026-04-17 16:50:06'),
	(22, 3, 'logout', 'User logged out.', '2026-04-17 16:50:31'),
	(23, 2, 'login', 'User logged in.', '2026-04-17 16:50:34'),
	(24, 2, 'logout', 'User logged out.', '2026-04-17 16:51:33'),
	(25, 1, 'login', 'User logged in.', '2026-04-17 16:51:36'),
	(26, 1, 'user_update', 'Updated user ID: 2', '2026-04-17 16:51:58'),
	(27, 1, 'logout', 'User logged out.', '2026-04-17 16:52:01'),
	(28, 2, 'login', 'User logged in.', '2026-04-17 16:52:10'),
	(29, 2, 'logout', 'User logged out.', '2026-04-17 16:52:38'),
	(30, 3, 'login', 'User logged in.', '2026-04-17 16:52:40'),
	(31, 3, 'logout', 'User logged out.', '2026-04-17 16:53:43'),
	(32, 1, 'login', 'User logged in.', '2026-04-17 16:53:47'),
	(33, 1, 'logout', 'User logged out.', '2026-04-17 17:00:25'),
	(34, 1, 'login', 'User logged in.', '2026-04-17 17:00:31'),
	(35, 1, 'user_create', 'Created user: vpbarruga', '2026-04-17 17:01:02'),
	(36, 1, 'logout', 'User logged out.', '2026-04-17 17:01:06'),
	(37, 4, 'login', 'User logged in.', '2026-04-17 17:01:23'),
	(38, 3, 'login', 'User logged in.', '2026-04-17 17:01:40'),
	(39, 3, 'logout', 'User logged out.', '2026-04-17 17:02:51'),
	(40, 4, 'logout', 'User logged out.', '2026-04-17 17:03:16'),
	(41, 1, 'login', 'User logged in.', '2026-04-17 17:03:18'),
	(42, 1, 'logout', 'User logged out.', '2026-04-17 17:03:23'),
	(43, 3, 'login', 'User logged in.', '2026-04-17 17:03:25'),
	(44, 3, 'logout', 'User logged out.', '2026-04-17 17:03:28'),
	(45, 2, 'login', 'User logged in.', '2026-04-17 17:03:30'),
	(46, 2, 'logout', 'User logged out.', '2026-04-17 17:04:28'),
	(47, 1, 'login', 'User logged in.', '2026-04-17 17:04:30'),
	(48, 1, 'logout', 'User logged out.', '2026-04-17 17:06:15'),
	(49, 2, 'login', 'User logged in.', '2026-04-17 17:06:17'),
	(50, 2, 'logout', 'User logged out.', '2026-04-17 17:06:23'),
	(51, 1, 'login', 'User logged in.', '2026-04-17 17:06:26'),
	(52, 1, 'logout', 'User logged out.', '2026-04-17 17:24:14'),
	(53, 1, 'login', 'User logged in.', '2026-04-17 17:24:17'),
	(54, 1, 'logout', 'User logged out.', '2026-04-17 17:34:43'),
	(55, 1, 'login', 'User logged in.', '2026-04-17 17:34:49'),
	(56, 1, 'quotation_create', 'Created quotation QT-20260418013557-186 for Sampol Kampani - 22,000.00', '2026-04-17 17:35:57'),
	(57, 1, 'quotation_update', 'Updated quotation QT-20260418013557-186 for Sampol Kampani - 46,500.00', '2026-04-17 17:48:11'),
	(58, 1, 'product_create', 'Added product: Breaker (EX 200)', '2026-04-17 17:49:11'),
	(59, 1, 'quotation_update', 'Updated quotation QT-20260418013557-186 for Sampol Kampani - 126,500.00', '2026-04-17 17:49:20'),
	(60, 1, 'supplier_create', 'Added supplier: JBEL', '2026-04-17 17:50:59'),
	(61, 1, 'product_update', 'Updated product ID: 2', '2026-04-17 17:51:15'),
	(62, 1, 'logout', 'User logged out.', '2026-04-17 17:53:51'),
	(63, 2, 'login', 'User logged in.', '2026-04-17 17:53:54'),
	(64, 2, 'logout', 'User logged out.', '2026-04-17 17:54:29'),
	(65, 3, 'login', 'User logged in.', '2026-04-17 17:54:35'),
	(66, 3, 'logout', 'User logged out.', '2026-04-17 17:54:37'),
	(67, 4, 'login', 'User logged in.', '2026-04-17 17:54:40'),
	(68, 2, 'login', 'User logged in.', '2026-04-17 17:57:15'),
	(69, 2, 'sale_create', 'Completed sale INV-20260418020050-775 for Vincent Paul - ₱3,500.00', '2026-04-17 18:00:50'),
	(70, 4, 'logout', 'User logged out.', '2026-04-17 18:03:14'),
	(71, 1, 'login', 'User logged in.', '2026-04-17 18:03:23'),
	(72, 1, 'cash_on_hand_update', 'Updated cash on hand for 2026-04-18 to ₱5,000.00', '2026-04-17 18:25:50'),
	(73, 1, 'expense_create', 'Added expense Miscellaneous - Food from Cash on Hand for ₱250.00', '2026-04-17 18:26:21'),
	(74, 1, 'expense_create', 'Added expense Cash Advance from Sales for ₱500.00', '2026-04-17 18:27:40'),
	(75, 1, 'logout', 'User logged out.', '2026-04-17 18:42:26'),
	(76, 2, 'login', 'User logged in.', '2026-04-17 18:42:29'),
	(77, 2, 'cash_on_hand_update', 'Updated cash on hand for 2026-04-18 to ₱5,000.00', '2026-04-17 18:42:40'),
	(78, 2, 'logout', 'User logged out.', '2026-04-17 18:47:31'),
	(79, 1, 'login', 'User logged in.', '2026-04-17 18:47:33'),
	(80, 2, 'login', 'User logged in.', '2026-04-17 18:47:47'),
	(81, 1, 'daily_report_submit', 'Submitted daily sales report for 2026-04-18', '2026-04-17 19:13:49'),
	(82, 2, 'daily_report_submit', 'Submitted daily sales report for 2026-04-18', '2026-04-17 19:16:05'),
	(83, 2, 'sale_create', 'Completed sale INV-20260418031643-279 for Walk-in Customer - ₱40,000.00', '2026-04-17 19:16:43'),
	(84, 2, 'daily_report_submit', 'Submitted daily sales report for 2026-04-18', '2026-04-17 19:17:11'),
	(85, 2, 'sale_create', 'Completed sale INV-20260418032537-612 for Walk-in Customer - ₱10,500.00', '2026-04-17 19:25:37'),
	(86, 2, 'daily_report_submit', 'Submitted daily sales report for 2026-04-18', '2026-04-17 19:25:43'),
	(87, 2, 'sale_void', 'Voided sale INV-20260418020050-775', '2026-04-17 19:30:53'),
	(88, 2, 'daily_report_submit', 'Submitted daily sales report for 2026-04-18', '2026-04-17 19:31:13'),
	(89, 2, 'logout', 'User logged out.', '2026-04-17 19:31:41'),
	(90, 3, 'login', 'User logged in.', '2026-04-17 19:31:44'),
	(91, 3, 'sale_create', 'Completed sale INV-20260418033155-807 for Walk-in Customer - ₱7,000.00', '2026-04-17 19:31:55'),
	(92, 3, 'daily_report_submit', 'Submitted daily sales report for 2026-04-18', '2026-04-17 19:31:59'),
	(93, 3, 'sale_create', 'Completed sale INV-20260418033236-858 for Sampol Kampani - ₱40,000.00', '2026-04-17 19:32:36'),
	(94, 3, 'daily_report_submit', 'Submitted daily sales report for 2026-04-18', '2026-04-17 19:32:42'),
	(95, 3, 'daily_report_submit', 'Submitted daily sales report for 2026-04-18', '2026-04-17 19:35:56'),
	(96, 1, 'logout', 'User logged out.', '2026-04-17 19:36:08'),
	(97, 2, 'login', 'User logged in.', '2026-04-17 19:36:11'),
	(98, 2, 'daily_report_submit', 'Submitted daily sales report for 2026-04-18', '2026-04-17 19:37:06'),
	(99, 3, 'login', 'User logged in.', '2026-04-17 19:43:06'),
	(100, 3, 'logout', 'User logged out.', '2026-04-17 19:51:45'),
	(101, 2, 'login', 'User logged in.', '2026-04-17 19:51:52'),
	(102, 2, 'logout', 'User logged out.', '2026-04-17 19:51:55'),
	(103, 1, 'login', 'User logged in.', '2026-04-17 19:51:57'),
	(104, 1, 'product_create', 'Added product: Bucket Tooth (EX 100)', '2026-04-17 20:51:30'),
	(105, 1, 'stock_adjust', 'OUT stock on product ID: 1', '2026-04-17 20:53:29'),
	(106, 1, 'organization_update', 'Updated organization information.', '2026-04-17 21:28:58'),
	(107, 1, 'organization_update', 'Updated organization information.', '2026-04-17 21:29:11'),
	(108, 1, 'organization_update', 'Updated organization information.', '2026-04-17 21:29:14');

-- Dumping structure for table pos_inventory_system.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pos_inventory_system.categories: ~0 rows (approximately)
REPLACE INTO `categories` (`id`, `name`, `created_at`) VALUES
	(1, 'Heavy Equipment Parts', '2026-04-17 16:29:59');

-- Dumping structure for table pos_inventory_system.daily_cash_on_hand
CREATE TABLE IF NOT EXISTS `daily_cash_on_hand` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `entry_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `user_id` int unsigned NOT NULL,
  `recorded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `entry_date` (`entry_date`),
  KEY `fk_cash_on_hand_user` (`user_id`),
  CONSTRAINT `fk_cash_on_hand_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pos_inventory_system.daily_cash_on_hand: ~1 rows (approximately)
REPLACE INTO `daily_cash_on_hand` (`id`, `entry_date`, `amount`, `user_id`, `recorded_at`, `updated_at`) VALUES
	(1, '2026-04-18', 5000.00, 2, '2026-04-17 18:42:40', '2026-04-17 18:42:40');

-- Dumping structure for table pos_inventory_system.daily_sales_reports
CREATE TABLE IF NOT EXISTS `daily_sales_reports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `report_date` date NOT NULL,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total_transactions` int NOT NULL DEFAULT '0',
  `total_units_sold` int NOT NULL DEFAULT '0',
  `gross_sales` decimal(12,2) NOT NULL DEFAULT '0.00',
  `net_sales` decimal(12,2) NOT NULL DEFAULT '0.00',
  `vat_collected` decimal(12,2) NOT NULL DEFAULT '0.00',
  `average_transaction_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cash_sales` decimal(12,2) NOT NULL DEFAULT '0.00',
  `credit_card_sales` decimal(12,2) NOT NULL DEFAULT '0.00',
  `gcash_maya_sales` decimal(12,2) NOT NULL DEFAULT '0.00',
  `bank_transfer_sales` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_voids` int NOT NULL DEFAULT '0',
  `voided_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_expenses` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dsr_user_date` (`user_id`,`report_date`),
  KEY `fk_dsr_user` (`user_id`),
  KEY `idx_dsr_report_date` (`report_date`),
  CONSTRAINT `fk_dsr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pos_inventory_system.daily_sales_reports: ~0 rows (approximately)
REPLACE INTO `daily_sales_reports` (`id`, `user_id`, `report_date`, `submitted_at`, `total_transactions`, `total_units_sold`, `gross_sales`, `net_sales`, `vat_collected`, `average_transaction_value`, `cash_sales`, `credit_card_sales`, `gcash_maya_sales`, `bank_transfer_sales`, `total_voids`, `voided_amount`, `total_expenses`, `notes`) VALUES
	(1, 1, '2026-04-18', '2026-04-17 19:13:49', 1, 1, 3500.00, 3125.00, 375.00, 3500.00, 3500.00, 0.00, 0.00, 0.00, 0, 0.00, 750.00, NULL),
	(4, 2, '2026-04-18', '2026-04-17 19:37:06', 2, 4, 50500.00, 45089.29, 5410.71, 25250.00, 10500.00, 0.00, 0.00, 40000.00, 1, 3500.00, 0.00, NULL),
	(6, 3, '2026-04-18', '2026-04-17 19:35:56', 2, 3, 47000.00, 41964.29, 5035.71, 23500.00, 7000.00, 40000.00, 0.00, 0.00, 0, 0.00, 0.00, NULL);

-- Dumping structure for table pos_inventory_system.expenses
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `expense_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fund_resource` enum('Sales','Cash on Hand') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `expense_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_expenses_user` (`user_id`),
  KEY `idx_expenses_time` (`expense_time`),
  CONSTRAINT `fk_expenses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pos_inventory_system.expenses: ~2 rows (approximately)
REPLACE INTO `expenses` (`id`, `user_id`, `expense_type`, `fund_resource`, `amount`, `description`, `expense_time`, `created_at`) VALUES
	(1, 1, 'Miscellaneous - Food', 'Cash on Hand', 250.00, 'For Lunch', '2026-04-18 02:26:21', '2026-04-17 18:26:21'),
	(2, 1, 'Cash Advance', 'Sales', 500.00, 'Ryan', '2026-04-18 02:27:40', '2026-04-17 18:27:40');

-- Dumping structure for table pos_inventory_system.organization_settings
CREATE TABLE IF NOT EXISTS `organization_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_contact` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_rate` decimal(5,4) NOT NULL DEFAULT '0.1200',
  `low_stock_threshold` int NOT NULL DEFAULT '5',
  `logo_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `header_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `owner_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `owner_contact` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `owner_email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_setup_complete` tinyint(1) NOT NULL DEFAULT '0',
  `setup_completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pos_inventory_system.organization_settings: ~0 rows (approximately)
REPLACE INTO `organization_settings` (`id`, `company_name`, `company_address`, `company_contact`, `company_email`, `vat_rate`, `low_stock_threshold`, `logo_path`, `header_path`, `owner_name`, `owner_address`, `owner_contact`, `owner_email`, `is_setup_complete`, `setup_completed_at`, `created_at`, `updated_at`) VALUES
	(1, 'R\'B Heavy Equipment Parts Trading', 'M. C. Briones St, Tipolo, Mandaue City, Cebu', '09291046992', 'rbheavyequipmentpartstrading@gmail.com', 0.1200, 5, 'public/uploads/organization/logo_20260418002821_428210fe.png', 'public/uploads/organization/header_20260418002821_0ce6f527.png', 'Rudy A. Barruga Jr.', '', '09291046992', '', 1, '2026-04-17 16:29:01', '2026-04-17 16:28:21', '2026-04-17 21:29:14');

-- Dumping structure for table pos_inventory_system.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned NOT NULL,
  `supplier_id` int unsigned DEFAULT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `buying_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `selling_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `stock_quantity` int NOT NULL DEFAULT '0',
  `barcode` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_type` enum('PC','Set','Pair','Unit','Assembly') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PC',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_products_category` (`category_id`),
  KEY `fk_products_supplier` (`supplier_id`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_products_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pos_inventory_system.products: ~0 rows (approximately)
REPLACE INTO `products` (`id`, `category_id`, `supplier_id`, `name`, `description`, `buying_price`, `selling_price`, `stock_quantity`, `barcode`, `unit_type`, `created_at`, `updated_at`) VALUES
	(1, 1, NULL, 'Break Pad', '', 2500.00, 3500.00, 2, '', 'PC', '2026-04-17 16:30:17', '2026-04-17 20:53:29'),
	(2, 1, 1, 'Breaker (EX 200)', '', 35000.00, 40000.00, 3, '', 'Assembly', '2026-04-17 17:49:11', '2026-04-17 19:32:36'),
	(3, 1, NULL, 'Bucket Tooth (EX 100)', '', 550.00, 750.00, 25, '', 'PC', '2026-04-17 20:51:30', '2026-04-17 20:51:30');

-- Dumping structure for table pos_inventory_system.quotations
CREATE TABLE IF NOT EXISTS `quotations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `quote_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `customer_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_contact` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `service_option` enum('without_service_repair','with_service_repair') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'without_service_repair',
  `service_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_fee` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `valid_until` date DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quote_no` (`quote_no`),
  KEY `fk_quotations_user` (`user_id`),
  CONSTRAINT `fk_quotations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pos_inventory_system.quotations: ~0 rows (approximately)
REPLACE INTO `quotations` (`id`, `quote_no`, `user_id`, `customer_name`, `customer_contact`, `customer_address`, `service_option`, `service_description`, `service_fee`, `subtotal_amount`, `total_amount`, `valid_until`, `notes`, `created_at`) VALUES
	(1, 'QT-20260418013557-186', 1, 'Sampol Kampani', '09568884512', NULL, 'with_service_repair', NULL, 15000.00, 111500.00, 126500.00, '2026-04-30', NULL, '2026-04-17 17:35:57');

-- Dumping structure for table pos_inventory_system.quotation_items
CREATE TABLE IF NOT EXISTS `quotation_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `unit_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_quotation_items_quotation` (`quotation_id`),
  KEY `fk_quotation_items_product` (`product_id`),
  CONSTRAINT `fk_quotation_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_quotation_items_quotation` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pos_inventory_system.quotation_items: ~0 rows (approximately)
REPLACE INTO `quotation_items` (`id`, `quotation_id`, `product_id`, `quantity`, `unit_price`, `subtotal`, `created_at`) VALUES
	(3, 1, 1, 9, 3500.00, 31500.00, '2026-04-17 17:49:20'),
	(4, 1, 2, 2, 40000.00, 80000.00, '2026-04-17 17:49:20');

-- Dumping structure for table pos_inventory_system.stock_movements
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `movement_type` enum('in','out') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `remarks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_stock_product` (`product_id`),
  KEY `fk_stock_user` (`user_id`),
  CONSTRAINT `fk_stock_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_stock_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pos_inventory_system.stock_movements: ~0 rows (approximately)
REPLACE INTO `stock_movements` (`id`, `product_id`, `user_id`, `movement_type`, `quantity`, `remarks`, `created_at`) VALUES
	(1, 1, 1, 'out', 1, 'POS Sale INV-20260418003132-714', '2026-04-17 16:31:32'),
	(2, 1, 2, 'out', 1, 'POS Sale INV-20260418020050-775', '2026-04-17 18:00:50'),
	(3, 2, 2, 'out', 1, 'POS Sale INV-20260418031643-279', '2026-04-17 19:16:43'),
	(4, 1, 2, 'out', 3, 'POS Sale INV-20260418032537-612', '2026-04-17 19:25:37'),
	(5, 1, 2, 'in', 1, 'Voided sale INV-20260418020050-775', '2026-04-17 19:30:53'),
	(6, 1, 3, 'out', 2, 'POS Sale INV-20260418033155-807', '2026-04-17 19:31:55'),
	(7, 2, 3, 'out', 1, 'POS Sale INV-20260418033236-858', '2026-04-17 19:32:36'),
	(8, 1, 1, 'out', 2, 'Defect', '2026-04-17 20:53:29');

-- Dumping structure for table pos_inventory_system.suppliers
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pos_inventory_system.suppliers: ~0 rows (approximately)
REPLACE INTO `suppliers` (`id`, `name`, `contact_person`, `phone`, `address`, `created_at`) VALUES
	(1, 'JBEL', 'Joseph', '+6392123456789', 'Mandaue', '2026-04-17 17:50:59');

-- Dumping structure for table pos_inventory_system.transactions
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `payment_amount` decimal(12,2) NOT NULL,
  `change_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `customer_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payment_method` enum('Cash','Credit Card','GCash/Maya','Bank Transfer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Cash',
  `reference_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('completed','voided','deleted') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `voided_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_no` (`invoice_no`),
  KEY `fk_transactions_user` (`user_id`),
  CONSTRAINT `fk_transactions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pos_inventory_system.transactions: ~0 rows (approximately)
REPLACE INTO `transactions` (`id`, `invoice_no`, `user_id`, `total_amount`, `payment_amount`, `change_amount`, `customer_name`, `customer_address`, `payment_method`, `reference_no`, `status`, `voided_at`, `deleted_at`, `created_at`) VALUES
	(1, 'INV-20260418003132-714', 1, 3500.00, 3500.00, 0.00, 'Walk-in Customer', 'No Address Provided', 'Cash', NULL, 'completed', NULL, NULL, '2026-04-17 16:31:32'),
	(2, 'INV-20260418020050-775', 2, 3500.00, 3500.00, 0.00, 'Vincent Paul', 'No Address Provided', 'Credit Card', '123', 'voided', '2026-04-17 19:30:53', NULL, '2026-04-17 18:00:50'),
	(3, 'INV-20260418031643-279', 2, 40000.00, 40000.00, 0.00, 'Walk-in Customer', 'No Address Provided', 'Bank Transfer', '54654kj564', 'completed', NULL, NULL, '2026-04-17 19:16:43'),
	(4, 'INV-20260418032537-612', 2, 10500.00, 11000.00, 500.00, 'Walk-in Customer', 'No Address Provided', 'Cash', NULL, 'completed', NULL, NULL, '2026-04-17 19:25:37'),
	(5, 'INV-20260418033155-807', 3, 7000.00, 7000.00, 0.00, 'Walk-in Customer', 'No Address Provided', 'Cash', NULL, 'completed', NULL, NULL, '2026-04-17 19:31:55'),
	(6, 'INV-20260418033236-858', 3, 40000.00, 40000.00, 0.00, 'Sampol Kampani', 'No Address Provided', 'Credit Card', '14445ad', 'completed', NULL, NULL, '2026-04-17 19:32:36');

-- Dumping structure for table pos_inventory_system.transaction_items
CREATE TABLE IF NOT EXISTS `transaction_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_transaction_items_transaction` (`transaction_id`),
  KEY `fk_transaction_items_product` (`product_id`),
  CONSTRAINT `fk_transaction_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_transaction_items_transaction` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pos_inventory_system.transaction_items: ~0 rows (approximately)
REPLACE INTO `transaction_items` (`id`, `transaction_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
	(1, 1, 1, 1, 3500.00, 3500.00),
	(2, 2, 1, 1, 3500.00, 3500.00),
	(3, 3, 2, 1, 40000.00, 40000.00),
	(4, 4, 1, 3, 3500.00, 10500.00),
	(5, 5, 1, 2, 3500.00, 7000.00),
	(6, 6, 2, 1, 40000.00, 40000.00);

-- Dumping structure for table pos_inventory_system.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middle_initial` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('Admin','Supply Manager','Sales Manager','Cashier') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Cashier',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pos_inventory_system.users: ~1 rows (approximately)
REPLACE INTO `users` (`id`, `name`, `first_name`, `middle_initial`, `last_name`, `contact`, `username`, `password`, `role`, `created_at`) VALUES
	(1, 'Administrator Admin', 'Administrator', '', 'Admin', '', 'admin', '$2y$10$W.NBAuDmEkNPKU2UfzQpMOCHRFOZfwf4oBb83mmbF8lcdwjldICdi', 'Admin', '2026-04-17 16:12:58'),
	(2, 'Cashier Bins', 'Cashier', NULL, 'Bins', NULL, 'cashier', '$2y$10$A74M1o2oHc1Hc8F0.RKAdOPtOkJDDx37mC.E7HFxtUV.hzEjRTciK', 'Cashier', '2026-04-17 16:33:07'),
	(3, 'Clark Dave A. Barruga', 'Clark Dave', 'A', 'Barruga', NULL, 'cdbarruga', '$2y$10$NZZ2ORgZKyJ6f3H7y/YFbOU0JeeUQ4jl8bIGZwV6klWjTrOxzHH86', 'Sales Manager', '2026-04-17 16:49:49'),
	(4, 'Vincent Paul Barruga', 'Vincent Paul', NULL, 'Barruga', '09395689851', 'vpbarruga', '$2y$10$i3ZLEuoZlTUZEPRKSY7Y7Obx5bMFIuNq9btojpoEw8EdkPPqXNQPa', 'Supply Manager', '2026-04-17 17:01:02');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
