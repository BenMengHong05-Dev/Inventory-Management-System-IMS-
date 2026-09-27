-- =========================================================
-- INVENTORY MANAGEMENT SYSTEM - COMPLETE XAMPP DATABASE DUMP
-- Compatible with MySQL 5.7+ / MariaDB / MySQL 8.0+
-- Database: inventory_management
-- =========================================================

CREATE DATABASE IF NOT EXISTS `inventory_management` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `inventory_management`;

-- Disable Foreign Key Checks during import
SET FOREIGN_KEY_CHECKS = 0;

-- 1. ROLES
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'Super Admin', 'Full system access and security administration'),
(2, 'Warehouse Manager', 'Manages warehouses, stock operations, transfers, and inventory audits'),
(3, 'Purchasing Officer', 'Handles purchase requisitions, orders, and vendor communications'),
(4, 'Inventory Staff', 'Handles daily stock-in, stock-out, and physical counts');

-- 2. PERMISSIONS
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `module` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permissions` (`id`, `name`, `slug`, `module`, `description`) VALUES
(1, 'View Dashboard', 'dashboard.view', 'dashboard', 'Access dashboard metrics and KPIs'),
(2, 'View Items', 'items.view', 'items', 'View item catalog and inventory details'),
(3, 'Create Items', 'items.create', 'items', 'Create new inventory items'),
(4, 'Edit Items', 'items.edit', 'items', 'Update item details and pricing'),
(5, 'Delete Items', 'items.delete', 'items', 'Delete items from catalog'),
(6, 'View Stock Levels', 'inventory.view', 'inventory', 'View inventory quantities across warehouses'),
(7, 'Stock In', 'inventory.stock_in', 'inventory', 'Perform inbound stock transactions'),
(8, 'Stock Out', 'inventory.stock_out', 'inventory', 'Perform outbound stock transactions'),
(9, 'Stock Transfer', 'inventory.transfer', 'inventory', 'Create and manage inter-warehouse transfers'),
(10, 'Stock Adjustment', 'inventory.adjust', 'inventory', 'Perform inventory balance adjustments'),
(11, 'Approve Stock Adjustment', 'inventory.adjust_approve', 'inventory', 'Approve stock adjustments (dual control)'),
(12, 'Stock Take', 'inventory.stock_take', 'inventory', 'Initiate and reconcile physical audits'),
(13, 'Item Returns', 'inventory.returns', 'inventory', 'Process supplier and employee returns'),
(14, 'View PR', 'purchasing.pr_view', 'purchasing', 'View purchase requests'),
(15, 'Create PR', 'purchasing.pr_create', 'purchasing', 'Create new purchase requisitions'),
(16, 'Approve PR', 'purchasing.pr_approve', 'purchasing', 'Approve purchase requests'),
(17, 'View PO', 'purchasing.po_view', 'purchasing', 'View purchase orders'),
(18, 'Create PO', 'purchasing.po_create', 'purchasing', 'Create purchase orders to suppliers'),
(19, 'Approve PO', 'purchasing.po_approve', 'purchasing', 'Authorize purchase orders'),
(20, 'Goods Receiving', 'purchasing.grn', 'purchasing', 'Receive goods against purchase orders'),
(21, 'View Assets', 'assets.view', 'assets', 'View asset register and details'),
(22, 'Create Assets', 'assets.create', 'assets', 'Register new assets/equipment'),
(23, 'Edit Assets', 'assets.edit', 'assets', 'Update asset specifications and value'),
(24, 'Delete Assets', 'assets.delete', 'assets', 'Remove asset records'),
(25, 'Assign Assets', 'assets.assign', 'assets', 'Check out equipment to employees'),
(26, 'Return Assets', 'assets.return', 'assets', 'Check in returned equipment'),
(27, 'Asset Maintenance', 'assets.maintenance', 'assets', 'Manage repair and servicing tickets'),
(28, 'Asset Disposal', 'assets.dispose', 'assets', 'Decommission and write off assets'),
(29, 'Manage Categories', 'master.categories', 'master', 'CRUD for item categories'),
(30, 'Manage Brands', 'master.brands', 'master', 'CRUD for brands/manufacturers'),
(31, 'Manage Units', 'master.units', 'master', 'CRUD for measurement units'),
(32, 'Manage Suppliers', 'master.suppliers', 'master', 'CRUD for vendor directory'),
(33, 'Manage Warehouses', 'master.warehouses', 'master', 'CRUD for warehouse facilities'),
(34, 'Manage Locations', 'master.locations', 'master', 'CRUD for storage bins/racks'),
(35, 'Manage Departments', 'master.departments', 'master', 'CRUD for organizational departments'),
(36, 'Manage Employees', 'master.employees', 'master', 'CRUD for personnel records'),
(37, 'View Reports', 'reports.view', 'reports', 'View and export inventory/asset reports'),
(38, 'Manage Users', 'admin.users', 'admin', 'Manage user accounts and roles'),
(39, 'Manage Roles', 'admin.roles', 'admin', 'Configure RBAC permissions'),
(40, 'View Audit Logs', 'admin.audit_logs', 'admin', 'Inspect immutable activity trace');

-- 3. ROLE_PERMISSIONS
DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permission_unique` (`role_id`,`permission_id`),
  KEY `role_permissions_role_id_foreign` (`role_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Assign all 40 permissions to Super Admin (role_id = 1)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, `id` FROM `permissions`;

-- 4. WAREHOUSES
DROP TABLE IF EXISTS `warehouses`;
CREATE TABLE `warehouses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `warehouses_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `warehouses` (`id`, `name`, `code`, `address`, `city`, `phone`, `email`, `is_default`, `is_active`) VALUES
(1, 'Central Warehouse', 'WH-MAIN', 'St 271, Sangkat Boeung Tumpun', 'Phnom Penh', '+855 23 888 999', 'central@inventory.com', 1, 1),
(2, 'Depot B (Tuol Kouk)', 'WH-TK', 'St 598, Khan Tuol Kouk', 'Phnom Penh', '+855 23 777 666', 'depot-b@inventory.com', 0, 1);

-- 5. USERS
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `role_id` bigint unsigned DEFAULT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  KEY `users_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password is 'password' hashed with Bcrypt
INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role_id`, `warehouse_id`, `is_active`) VALUES
(1, 'System Administrator', 'admin@inventory.com', '$2y$12$4L3v4oG1lE.qGf1L8fVbfe2o0e4fC7c4uQ0y3fP6o0e4fC7c4uQ0y', '+855 12 345 678', 1, 1, 1);

-- 6. CATEGORIES
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `code`, `parent_id`, `description`, `status`) VALUES
(1, 'Electronics & IT', 'CAT-IT', NULL, 'Computers, laptops, monitors and networking gear', 'active'),
(2, 'Office Supplies', 'CAT-OFF', NULL, 'Stationery, paper, toner and desk consumables', 'active'),
(3, 'Furniture & Fixtures', 'CAT-FURN', NULL, 'Desks, ergonomic chairs and filing cabinets', 'active'),
(4, 'Spare Parts & Tools', 'CAT-SPARE', NULL, 'Maintenance tools, cables and replacement components', 'active');

-- 7. BRANDS
DROP TABLE IF EXISTS `brands`;
CREATE TABLE `brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `brands` (`id`, `name`, `code`, `description`, `status`) VALUES
(1, 'Dell', 'DELL', 'Dell Technologies computers and servers', 'active'),
(2, 'HP', 'HP', 'Hewlett-Packard printers and computing devices', 'active'),
(3, 'Logitech', 'LOGI', 'Computer peripherals and accessories', 'active'),
(4, 'Apple', 'APPL', 'Apple Inc hardware and accessories', 'active'),
(5, 'Generic', 'GEN', 'Standard unbranded equipment and consumables', 'active');

-- 8. UNITS
DROP TABLE IF EXISTS `units`;
CREATE TABLE `units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `symbol` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `units_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `units` (`id`, `name`, `code`, `symbol`, `status`) VALUES
(1, 'Piece', 'PCS', 'pcs', 'active'),
(2, 'Box', 'BOX', 'bx', 'active'),
(3, 'Pack', 'PACK', 'pk', 'active'),
(4, 'Kilogram', 'KG', 'kg', 'active'),
(5, 'Liter', 'LTR', 'ltr', 'active'),
(6, 'Set', 'SET', 'set', 'active'),
(7, 'Roll', 'ROLL', 'rl', 'active');

-- 9. SUPPLIERS
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) NOT NULL,
  `contact_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Cambodia',
  `tax_id` varchar(50) DEFAULT NULL,
  `payment_terms` varchar(50) DEFAULT 'Net 30',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `suppliers` (`id`, `name`, `code`, `contact_name`, `email`, `phone`, `address`, `city`, `payment_terms`, `status`) VALUES
(1, 'Khmer IT Solutions Co., Ltd', 'SUP-001', 'Mr. Vanna Sok', 'sales@khmerit.com', '+855 23 111 222', 'Monivong Blvd, Sangkat Boeung Keng Kang', 'Phnom Penh', 'Net 30', 'active'),
(2, 'Angkor Stationery & Supplies', 'SUP-002', 'Mrs. Bopha Chan', 'contact@angkorstationery.com', '+855 23 333 444', 'Russian Federation Blvd', 'Phnom Penh', 'Net 15', 'active');

-- 10. DEPARTMENTS
DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `departments` (`id`, `name`, `code`, `description`, `status`) VALUES
(1, 'Information Technology', 'DEPT-IT', 'IT Infrastructure, Systems, and Network Operations', 'active'),
(2, 'Human Resources', 'DEPT-HR', 'Talent acquisition and personnel administration', 'active'),
(3, 'Finance & Accounting', 'DEPT-FIN', 'Corporate accounts, payroll, and procurement audits', 'active'),
(4, 'Logistics & Warehouse', 'DEPT-LOG', 'Warehousing, distribution, and inventory custody', 'active');

-- 11. EMPLOYEES
DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_code_unique` (`code`),
  KEY `employees_department_id_foreign` (`department_id`),
  CONSTRAINT `employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `employees` (`id`, `name`, `code`, `email`, `phone`, `department_id`, `designation`, `status`) VALUES
(1, 'Sok Dara', 'EMP-001', 'dara.sok@company.com', '+855 12 111 222', 1, 'Senior Systems Engineer', 'active'),
(2, 'Keo Sreymom', 'EMP-002', 'sreymom.keo@company.com', '+855 12 333 444', 2, 'HR Specialist', 'active'),
(3, 'Chheng Rithy', 'EMP-003', 'rithy.chheng@company.com', '+855 12 555 666', 4, 'Warehouse Supervisor', 'active');

-- 12. LOCATIONS (BINS / RACKS)
DROP TABLE IF EXISTS `locations`;
CREATE TABLE `locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` bigint unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `aisle` varchar(20) DEFAULT NULL,
  `rack` varchar(20) DEFAULT NULL,
  `shelf` varchar(20) DEFAULT NULL,
  `bin` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `locations_warehouse_code_unique` (`warehouse_id`,`code`),
  CONSTRAINT `locations_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `locations` (`id`, `warehouse_id`, `name`, `code`, `aisle`, `rack`, `shelf`, `bin`, `status`) VALUES
(1, 1, 'Zone A - IT Rack 1', 'A-01-01', 'A', '01', '1', 'A', 'active'),
(2, 1, 'Zone A - IT Rack 2', 'A-01-02', 'A', '01', '2', 'B', 'active'),
(3, 1, 'Zone B - Stationery Rack', 'B-02-01', 'B', '02', '1', 'A', 'active'),
(4, 2, 'Main Depot Floor', 'TK-FLR-01', 'F', '01', '1', '1', 'active');

-- 13. ITEMS (CATALOG)
DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `brand_id` bigint unsigned DEFAULT NULL,
  `unit_id` bigint unsigned DEFAULT NULL,
  `cost_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `selling_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `min_stock_level` decimal(10,2) NOT NULL DEFAULT '5.00',
  `max_stock_level` decimal(10,2) DEFAULT '100.00',
  `reorder_level` decimal(10,2) NOT NULL DEFAULT '10.00',
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `items_sku_unique` (`sku`),
  KEY `items_category_id_foreign` (`category_id`),
  KEY `items_brand_id_foreign` (`brand_id`),
  KEY `items_unit_id_foreign` (`unit_id`),
  CONSTRAINT `items_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  CONSTRAINT `items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `items` (`id`, `name`, `sku`, `barcode`, `category_id`, `brand_id`, `unit_id`, `cost_price`, `selling_price`, `min_stock_level`, `max_stock_level`, `reorder_level`, `status`) VALUES
(1, 'Dell Latitude 5440 14" Laptop Core i7', 'IT-LPT-001', '884116372019', 1, 1, 1, 850.00, 1050.00, 5.00, 50.00, 10.00, 'active'),
(2, 'Logitech MX Master 3S Wireless Mouse', 'IT-MOU-002', '097855174543', 1, 3, 1, 65.00, 95.00, 10.00, 100.00, 15.00, 'active'),
(3, 'Double A A4 Copy Paper 80gsm (500 Sheets)', 'OFF-PAP-003', '885871780001', 2, 5, 2, 4.50, 6.00, 20.00, 200.00, 40.00, 'active'),
(4, 'Cat6 UTP Network Patch Cable 2m', 'SP-CBL-004', '793573182931', 4, 5, 1, 1.80, 3.50, 30.00, 300.00, 50.00, 'active');

-- 14. INVENTORY
DROP TABLE IF EXISTS `inventory`;
CREATE TABLE `inventory` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `location_id` bigint unsigned DEFAULT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT '0.00',
  `reserved_quantity` decimal(12,2) NOT NULL DEFAULT '0.00',
  `available_quantity` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_item_warehouse_unique` (`item_id`,`warehouse_id`),
  KEY `inventory_warehouse_id_foreign` (`warehouse_id`),
  KEY `inventory_location_id_foreign` (`location_id`),
  CONSTRAINT `inventory_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `inventory` (`item_id`, `warehouse_id`, `location_id`, `quantity`, `reserved_quantity`, `available_quantity`) VALUES
(1, 1, 1, 25.00, 2.00, 23.00),
(2, 1, 2, 45.00, 0.00, 45.00),
(3, 1, 3, 120.00, 10.00, 110.00),
(4, 1, 1, 80.00, 0.00, 80.00),
(1, 2, 4, 5.00, 0.00, 5.00),
(3, 2, 4, 30.00, 0.00, 30.00);

-- 15. INVENTORY TRANSACTIONS
DROP TABLE IF EXISTS `inventory_transactions`;
CREATE TABLE `inventory_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transaction_number` varchar(50) NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `transaction_type` enum('stock_in','stock_out','transfer_in','transfer_out','adjustment','return','opening_stock') NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `unit_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inv_trans_item_idx` (`item_id`),
  KEY `inv_trans_warehouse_idx` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `inventory_transactions` (`transaction_number`, `item_id`, `warehouse_id`, `transaction_type`, `quantity`, `unit_cost`, `total_cost`, `notes`) VALUES
('TRX-INIT-001', 1, 1, 'opening_stock', 25.00, 850.00, 21250.00, 'Initial opening stock'),
('TRX-INIT-002', 2, 1, 'opening_stock', 45.00, 65.00, 2925.00, 'Initial opening stock'),
('TRX-INIT-003', 3, 1, 'opening_stock', 120.00, 4.50, 540.00, 'Initial opening stock'),
('TRX-INIT-004', 4, 1, 'opening_stock', 80.00, 1.80, 144.00, 'Initial opening stock');

-- 16. STOCK TRANSFERS
DROP TABLE IF EXISTS `stock_transfers`;
CREATE TABLE `stock_transfers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transfer_number` varchar(50) NOT NULL,
  `source_warehouse_id` bigint unsigned NOT NULL,
  `destination_warehouse_id` bigint unsigned NOT NULL,
  `status` enum('draft','pending','in_transit','received','cancelled') NOT NULL DEFAULT 'pending',
  `transfer_date` date NOT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_transfers_number_unique` (`transfer_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. STOCK TRANSFER ITEMS
DROP TABLE IF EXISTS `stock_transfer_items`;
CREATE TABLE `stock_transfer_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transfer_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `received_quantity` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `sti_transfer_id_foreign` FOREIGN KEY (`transfer_id`) REFERENCES `stock_transfers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. STOCK ADJUSTMENTS
DROP TABLE IF EXISTS `stock_adjustments`;
CREATE TABLE `stock_adjustments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `adjustment_number` varchar(50) NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `adjustment_type` enum('addition','subtraction') NOT NULL,
  `reason_code` varchar(50) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_adjustments_number_unique` (`adjustment_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. STOCK ADJUSTMENT ITEMS
DROP TABLE IF EXISTS `stock_adjustment_items`;
CREATE TABLE `stock_adjustment_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `adjustment_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `current_quantity` decimal(12,2) NOT NULL,
  `adjusted_quantity` decimal(12,2) NOT NULL,
  `quantity_difference` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `sai_adjustment_id_foreign` FOREIGN KEY (`adjustment_id`) REFERENCES `stock_adjustments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20. STOCK TAKES (AUDITS)
DROP TABLE IF EXISTS `stock_takes`;
CREATE TABLE `stock_takes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `take_number` varchar(50) NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `status` enum('in_progress','completed','reconciled') NOT NULL DEFAULT 'in_progress',
  `snapshot_date` date NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `reconciled_by` bigint unsigned DEFAULT NULL,
  `reconciled_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_takes_number_unique` (`take_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 21. STOCK TAKE ITEMS
DROP TABLE IF EXISTS `stock_take_items`;
CREATE TABLE `stock_take_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `take_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `snapshot_quantity` decimal(12,2) NOT NULL,
  `counted_quantity` decimal(12,2) DEFAULT NULL,
  `variance` decimal(12,2) DEFAULT NULL,
  `counted_by` bigint unsigned DEFAULT NULL,
  `counted_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `sti_take_id_foreign` FOREIGN KEY (`take_id`) REFERENCES `stock_takes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 22. PURCHASE REQUESTS (PR)
DROP TABLE IF EXISTS `purchase_requests`;
CREATE TABLE `purchase_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pr_number` varchar(50) NOT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `status` enum('pending','approved','rejected','converted_to_po') NOT NULL DEFAULT 'pending',
  `request_date` date NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_requests_number_unique` (`pr_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 23. PURCHASE REQUEST ITEMS
DROP TABLE IF EXISTS `purchase_request_items`;
CREATE TABLE `purchase_request_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pr_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `requested_quantity` decimal(12,2) NOT NULL,
  `estimated_unit_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `pri_pr_id_foreign` FOREIGN KEY (`pr_id`) REFERENCES `purchase_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 24. PURCHASE ORDERS (PO)
DROP TABLE IF EXISTS `purchase_orders`;
CREATE TABLE `purchase_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `po_number` varchar(50) NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `pr_id` bigint unsigned DEFAULT NULL,
  `status` enum('draft','approved','ordered','partial_received','received','cancelled') NOT NULL DEFAULT 'draft',
  `order_date` date NOT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_number_unique` (`po_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 25. PURCHASE ORDER ITEMS
DROP TABLE IF EXISTS `purchase_order_items`;
CREATE TABLE `purchase_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `po_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `ordered_quantity` decimal(12,2) NOT NULL,
  `received_quantity` decimal(12,2) NOT NULL DEFAULT '0.00',
  `unit_price` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `poi_po_id_foreign` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 26. GOODS RECEIPTS (GRN)
DROP TABLE IF EXISTS `goods_receipts`;
CREATE TABLE `goods_receipts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grn_number` varchar(50) NOT NULL,
  `po_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `received_date` date NOT NULL,
  `delivery_note_number` varchar(100) DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_receipts_number_unique` (`grn_number`),
  CONSTRAINT `grn_po_id_foreign` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 27. GOODS RECEIPT ITEMS
DROP TABLE IF EXISTS `goods_receipt_items`;
CREATE TABLE `goods_receipt_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grn_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `received_quantity` decimal(12,2) NOT NULL,
  `unit_cost` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `gri_grn_id_foreign` FOREIGN KEY (`grn_id`) REFERENCES `goods_receipts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 28. FIXED ASSETS
DROP TABLE IF EXISTS `assets`;
CREATE TABLE `assets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_tag` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `brand_id` bigint unsigned DEFAULT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `custodian_id` bigint unsigned DEFAULT NULL,
  `location_id` bigint unsigned DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `current_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `warranty_expiry` date DEFAULT NULL,
  `status` enum('available','assigned','under_maintenance','disposed') NOT NULL DEFAULT 'available',
  `notes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assets_tag_unique` (`asset_tag`),
  KEY `assets_category_id_foreign` (`category_id`),
  KEY `assets_custodian_id_foreign` (`custodian_id`),
  CONSTRAINT `assets_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `assets_custodian_id_foreign` FOREIGN KEY (`custodian_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `assets` (`asset_tag`, `name`, `category_id`, `brand_id`, `supplier_id`, `department_id`, `custodian_id`, `serial_number`, `model`, `purchase_date`, `purchase_cost`, `current_value`, `warranty_expiry`, `status`) VALUES
('AST-0001', 'Dell Latitude 5440 Core i7 Laptop', 1, 1, 1, 1, 1, 'SN-DELL-889901', 'Latitude 5440', '2025-01-15', 1050.00, 950.00, '2028-01-15', 'assigned'),
('AST-0002', 'HP LaserJet Pro MFP 4103fdw Printer', 1, 2, 1, 2, NULL, 'SN-HP-332211', 'LaserJet 4103', '2024-06-10', 480.00, 400.00, '2027-06-10', 'available');

-- 29. ASSET ASSIGNMENTS
DROP TABLE IF EXISTS `asset_assignments`;
CREATE TABLE `asset_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `location_id` bigint unsigned DEFAULT NULL,
  `assigned_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('active','returned') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `aa_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `aa_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `asset_assignments` (`asset_id`, `employee_id`, `assigned_date`, `status`, `notes`) VALUES
(1, 1, '2025-01-16', 'active', 'Assigned with laptop charger and wireless mouse');

-- 30. ASSET RETURNS
DROP TABLE IF EXISTS `asset_returns`;
CREATE TABLE `asset_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `assignment_id` bigint unsigned NOT NULL,
  `asset_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `return_date` date NOT NULL,
  `condition` enum('good','needs_repair','damaged') NOT NULL DEFAULT 'good',
  `location_id` bigint unsigned DEFAULT NULL,
  `received_by` bigint unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 31. ASSET MAINTENANCE
DROP TABLE IF EXISTS `asset_maintenances`;
CREATE TABLE `asset_maintenances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint unsigned NOT NULL,
  `maintenance_type` enum('preventive','corrective','calibration','upgrade') NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `cost` decimal(12,2) DEFAULT '0.00',
  `actual_cost` decimal(12,2) DEFAULT '0.00',
  `start_date` date DEFAULT NULL,
  `expected_completion_date` date DEFAULT NULL,
  `actual_completion_date` date DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `resolution_notes` text DEFAULT NULL,
  `performed_by` varchar(150) DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 32. ASSET DISPOSALS
DROP TABLE IF EXISTS `asset_disposals`;
CREATE TABLE `asset_disposals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint unsigned NOT NULL,
  `disposal_type` enum('sold','scrapped','donated','recycled','lost') NOT NULL,
  `disposal_date` date NOT NULL,
  `disposal_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `reason` text NOT NULL,
  `authorized_by_name` varchar(150) NOT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 33. ITEM RETURNS
DROP TABLE IF EXISTS `item_returns`;
CREATE TABLE `item_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `return_number` varchar(50) NOT NULL,
  `return_type` enum('to_supplier','from_employee') NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `employee_id` bigint unsigned DEFAULT NULL,
  `return_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_returns_number_unique` (`return_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 34. ITEM RETURN ITEMS
DROP TABLE IF EXISTS `item_return_items`;
CREATE TABLE `item_return_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `return_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `iri_return_id_foreign` FOREIGN KEY (`return_id`) REFERENCES `item_returns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 35. STATUS HISTORIES
DROP TABLE IF EXISTS `status_histories`;
CREATE TABLE `status_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `record_type` varchar(50) NOT NULL,
  `record_id` bigint unsigned NOT NULL,
  `from_status` varchar(50) DEFAULT NULL,
  `to_status` varchar(50) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 36. NOTIFICATIONS
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `notifications` (`user_id`, `title`, `message`, `type`, `link`, `is_read`) VALUES
(1, 'System Initialized', 'Welcome to the Enterprise Inventory Management System. System database and seeders are active.', 'info', '/dashboard', 0),
(1, 'Low Stock Warning', 'Dell Latitude 5440 is nearing reorder threshold in Central Warehouse.', 'low_stock', '/inventory/low-stock', 0);

-- 37. AUDIT LOGS
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `module` varchar(50) NOT NULL,
  `record_id` bigint unsigned DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `audit_logs` (`user_id`, `action`, `module`, `record_id`, `new_values`, `ip_address`) VALUES
(1, 'create', 'auth', 1, '{\"event\": \"Initial Database Installation & Super Admin Creation\"}', '127.0.0.1');

-- Re-enable Foreign Key Checks
SET FOREIGN_KEY_CHECKS = 1;
