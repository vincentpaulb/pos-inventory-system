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


-- Dumping database structure for rb_heavy_inventory
CREATE DATABASE IF NOT EXISTS `rb_heavy_inventory` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `rb_heavy_inventory`;

-- Dumping structure for table rb_heavy_inventory.activity_logs
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_activity_user` (`user_id`),
  CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rb_heavy_inventory.activity_logs: ~22 rows (approximately)
REPLACE INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `created_at`) VALUES
	(1, 1, 'login', 'User logged in.', '2026-03-27 12:28:48'),
	(2, 1, 'product_create', 'Added product: Oring-Kit', '2026-03-27 12:40:17'),
	(3, 1, 'stock_adjust', 'IN stock on product ID: 3', '2026-03-27 12:41:02'),
	(4, 1, 'product_create', 'Added product: Repair Kit', '2026-03-27 13:28:53'),
	(5, 1, 'sale_create', 'Completed sale INV-20260327134616-171 — ₱14,000.00', '2026-03-27 13:46:16'),
	(6, 1, 'user_password_reset', 'Reset password for user ID: 1', '2026-03-27 13:49:52'),
	(7, 1, 'user_create', 'Created user: vincentpaulb', '2026-03-27 13:50:30'),
	(8, 1, 'user_create', 'Created user: cdbarruga', '2026-03-27 13:50:47'),
	(9, 1, 'logout', 'User logged out.', '2026-03-27 13:50:48'),
	(10, 3, 'login', 'User logged in.', '2026-03-27 13:51:03'),
	(11, 3, 'quotation_create', 'Created quotation QT-20260327140353-627 for Sampol Kampani — ₱57,500.00', '2026-03-27 14:03:53'),
	(12, 3, 'logout', 'User logged out.', '2026-03-27 14:06:16'),
	(13, 2, 'login', 'User logged in.', '2026-03-27 14:06:20'),
	(14, 2, 'supplier_delete', 'Deleted supplier ID: 2', '2026-03-27 14:12:02'),
	(15, 2, 'supplier_create', 'Added supplier: RB Heavy Equipment Parts Trading', '2026-03-27 14:12:30'),
	(16, 2, 'supplier_update', 'Updated supplier ID: 3', '2026-03-27 14:12:46'),
	(17, 2, 'product_create', 'Added product: Bucket Tooth EX100', '2026-03-27 14:14:19'),
	(18, 2, 'sale_create', 'Completed sale INV-20260327141654-904 — ₱3,750.00', '2026-03-27 14:16:54'),
	(19, 2, 'login', 'User logged in.', '2026-03-30 14:14:41'),
	(20, 2, 'sale_create', 'Completed sale INV-20260330141457-891 — ₱1,450.00', '2026-03-30 14:14:57'),
	(21, 2, 'stock_adjust', 'IN stock on product ID: 7', '2026-03-30 14:15:57'),
	(22, 2, 'sale_create', 'Completed sale INV-20260330142804-797 — ₱19,500.00', '2026-03-30 14:28:04'),
	(23, 2, 'login', 'User logged in.', '2026-04-01 00:35:52'),
	(24, 2, 'sale_create', 'Completed sale INV-20260401003744-126 — ₱11,300.00', '2026-04-01 00:37:44'),
	(25, 2, 'logout', 'User logged out.', '2026-04-01 04:46:54'),
	(26, 2, 'login', 'User logged in.', '2026-04-09 11:24:40'),
	(27, 2, 'logout', 'User logged out.', '2026-04-09 16:28:16');

-- Dumping structure for table rb_heavy_inventory.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rb_heavy_inventory.categories: ~4 rows (approximately)
REPLACE INTO `categories` (`id`, `name`, `created_at`) VALUES
	(1, 'Engine Parts', '2026-03-27 12:28:30'),
	(2, 'Hydraulic Parts', '2026-03-27 12:28:30'),
	(3, 'Undercarriage', '2026-03-27 12:28:30'),
	(4, 'Electrical', '2026-03-27 12:28:30');

-- Dumping structure for table rb_heavy_inventory.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned NOT NULL,
  `supplier_id` int unsigned DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `buying_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `selling_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `stock_quantity` int NOT NULL DEFAULT '0',
  `barcode` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_products_category` (`category_id`),
  KEY `fk_products_supplier` (`supplier_id`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_products_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rb_heavy_inventory.products: ~7 rows (approximately)
REPLACE INTO `products` (`id`, `category_id`, `supplier_id`, `name`, `description`, `buying_price`, `selling_price`, `stock_quantity`, `barcode`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'Oil Filter', 'Standard heavy equipment oil filter', 180.00, 250.00, 19, 'RB-OF-001', '2026-03-27 12:28:30', '2026-03-30 14:14:57'),
	(2, 2, NULL, 'Hydraulic Hose', 'Durable pressure-resistant hose', 850.00, 1200.00, 11, 'RB-HH-002', '2026-03-27 12:28:30', '2026-03-30 14:14:57'),
	(3, 3, 1, 'Track Roller', 'Undercarriage roller assembly', 3500.00, 4800.00, 8, 'RB-TR-003', '2026-03-27 12:28:30', '2026-04-01 00:37:44'),
	(4, 4, NULL, 'Starter Relay', 'Electrical starter relay component', 420.00, 650.00, 9, 'RB-SR-004', '2026-03-27 12:28:30', '2026-03-27 12:28:30'),
	(7, 2, NULL, 'Oring-Kit', 'Oring-Kit', 5000.00, 6500.00, 20, '', '2026-03-27 12:40:17', '2026-04-01 00:37:44'),
	(8, 1, 1, 'Repair Kit', 'Repair Kit', 6000.00, 7500.00, 14, '', '2026-03-27 13:28:53', '2026-03-27 13:46:16'),
	(9, 3, NULL, 'Bucket Tooth EX100', 'Bucket Tooth EX100', 1500.00, 2500.00, 29, '', '2026-03-27 14:14:19', '2026-03-27 14:16:54');

-- Dumping structure for table rb_heavy_inventory.quotations
CREATE TABLE IF NOT EXISTS `quotations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `quote_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `customer_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_contact` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_address` text COLLATE utf8mb4_unicode_ci,
  `service_option` enum('without_service_repair','with_service_repair') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'without_service_repair',
  `service_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_fee` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `valid_until` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quote_no` (`quote_no`),
  KEY `fk_quotations_user` (`user_id`),
  CONSTRAINT `fk_quotations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rb_heavy_inventory.quotations: ~1 rows (approximately)
REPLACE INTO `quotations` (`id`, `quote_no`, `user_id`, `customer_name`, `customer_contact`, `customer_address`, `service_option`, `service_description`, `service_fee`, `subtotal_amount`, `total_amount`, `valid_until`, `notes`, `created_at`) VALUES
	(1, 'QT-20260327140353-627', 3, 'Sampol Kampani', '09568884512', 'Sampol Kampani, Cebu', 'with_service_repair', NULL, 45000.00, 12500.00, 57500.00, '2026-04-03', NULL, '2026-03-27 14:03:53');

-- Dumping structure for table rb_heavy_inventory.quotation_items
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rb_heavy_inventory.quotation_items: ~2 rows (approximately)
REPLACE INTO `quotation_items` (`id`, `quotation_id`, `product_id`, `quantity`, `unit_price`, `subtotal`, `created_at`) VALUES
	(1, 1, 2, 1, 1200.00, 1200.00, '2026-03-27 14:03:53'),
	(2, 1, 3, 1, 4800.00, 4800.00, '2026-03-27 14:03:53'),
	(3, 1, 7, 1, 6500.00, 6500.00, '2026-03-27 14:03:53');

-- Dumping structure for table rb_heavy_inventory.stock_movements
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `movement_type` enum('in','out') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_stock_product` (`product_id`),
  KEY `fk_stock_user` (`user_id`),
  CONSTRAINT `fk_stock_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_stock_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rb_heavy_inventory.stock_movements: ~11 rows (approximately)
REPLACE INTO `stock_movements` (`id`, `product_id`, `user_id`, `movement_type`, `quantity`, `remarks`, `created_at`) VALUES
	(1, 3, 1, 'in', 5, '', '2026-03-27 12:41:02'),
	(2, 7, 1, 'out', 1, 'POS Sale INV-20260327134616-171', '2026-03-27 13:46:16'),
	(3, 8, 1, 'out', 1, 'POS Sale INV-20260327134616-171', '2026-03-27 13:46:16'),
	(4, 1, 2, 'out', 5, 'POS Sale INV-20260327141654-904', '2026-03-27 14:16:54'),
	(5, 9, 2, 'out', 1, 'POS Sale INV-20260327141654-904', '2026-03-27 14:16:54'),
	(6, 2, 2, 'out', 1, 'POS Sale INV-20260330141457-891', '2026-03-30 14:14:57'),
	(7, 1, 2, 'out', 1, 'POS Sale INV-20260330141457-891', '2026-03-30 14:14:57'),
	(8, 7, 2, 'in', 15, '', '2026-03-30 14:15:57'),
	(9, 7, 2, 'out', 3, 'POS Sale INV-20260330142804-797', '2026-03-30 14:28:04'),
	(10, 7, 2, 'out', 1, 'POS Sale INV-20260401003744-126', '2026-04-01 00:37:44'),
	(11, 3, 2, 'out', 1, 'POS Sale INV-20260401003744-126', '2026-04-01 00:37:44');

-- Dumping structure for table rb_heavy_inventory.suppliers
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rb_heavy_inventory.suppliers: ~2 rows (approximately)
REPLACE INTO `suppliers` (`id`, `name`, `contact_person`, `phone`, `address`, `created_at`) VALUES
	(1, 'Prime Industrial Supply', 'Juan Dela Cruz', '09171234567', 'Manila, Philippines', '2026-03-27 12:28:30'),
	(3, 'RB Heavy Equipment Parts Trading', 'Rudy Barruga Jr', '+639291046992', 'Cebu, Philippines', '2026-03-27 14:12:30');

-- Dumping structure for table rb_heavy_inventory.transactions
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `payment_amount` decimal(12,2) NOT NULL,
  `change_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_no` (`invoice_no`),
  KEY `fk_transactions_user` (`user_id`),
  CONSTRAINT `fk_transactions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rb_heavy_inventory.transactions: ~5 rows (approximately)
REPLACE INTO `transactions` (`id`, `invoice_no`, `user_id`, `total_amount`, `payment_amount`, `change_amount`, `created_at`) VALUES
	(11, 'INV-20260327134616-171', 1, 14000.00, 14000.00, 0.00, '2026-03-27 13:46:16'),
	(12, 'INV-20260327141654-904', 2, 3750.00, 6500.00, 2750.00, '2026-03-27 14:16:54'),
	(13, 'INV-20260330141457-891', 2, 1450.00, 1500.00, 50.00, '2026-03-30 14:14:57'),
	(14, 'INV-20260330142804-797', 2, 19500.00, 20000.00, 500.00, '2026-03-30 14:28:04'),
	(15, 'INV-20260401003744-126', 2, 11300.00, 11500.00, 200.00, '2026-04-01 00:37:44');

-- Dumping structure for table rb_heavy_inventory.transaction_items
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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rb_heavy_inventory.transaction_items: ~9 rows (approximately)
REPLACE INTO `transaction_items` (`id`, `transaction_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
	(11, 11, 7, 1, 6500.00, 6500.00),
	(12, 11, 8, 1, 7500.00, 7500.00),
	(13, 12, 1, 5, 250.00, 1250.00),
	(14, 12, 9, 1, 2500.00, 2500.00),
	(15, 13, 2, 1, 1200.00, 1200.00),
	(16, 13, 1, 1, 250.00, 250.00),
	(17, 14, 7, 3, 6500.00, 19500.00),
	(18, 15, 7, 1, 6500.00, 6500.00),
	(19, 15, 3, 1, 4800.00, 4800.00);

-- Dumping structure for table rb_heavy_inventory.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('Admin','Cashier') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Cashier',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rb_heavy_inventory.users: ~3 rows (approximately)
REPLACE INTO `users` (`id`, `name`, `username`, `password`, `role`, `created_at`) VALUES
	(1, 'System Administrator', 'admin', '$2y$10$4t8WzV2gPp4MSkLCsmLr2.nzQDyEScB850HwmZPWvT8cLSrlXy9Zi', 'Admin', '2026-03-27 12:28:30'),
	(2, 'Vincent Paul Barruga', 'vincentpaulb', '$2y$10$vxUReGTn7JibmOoabINAo.BldZadQtKGtcX8a75cUMoj39N7GG4X.', 'Admin', '2026-03-27 13:50:30'),
	(3, 'Clark Dave Barruga', 'cdbarruga', '$2y$10$E7ZFZn9vmkL/mdqgINKhE.KGUXyUjW8NTGawfu4ioO5sMRhveRPzy', 'Cashier', '2026-03-27 13:50:47');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
