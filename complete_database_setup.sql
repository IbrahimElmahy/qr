-- إعداد قاعدة البيانات الكاملة
-- تشغيل هذا الملف في phpMyAdmin

-- إنشاء قاعدة البيانات (إذا لم تكن موجودة)
CREATE DATABASE IF NOT EXISTS `ztjmal_shipmen` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- استخدام قاعدة البيانات
USE `ztjmal_shipmen`;

-- إنشاء جدول الشركات
CREATE TABLE IF NOT EXISTS `companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إنشاء جدول المستخدمين
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إنشاء جدول الشحنات
CREATE TABLE IF NOT EXISTS `shipments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barcode` varchar(100) NOT NULL,
  `company_id` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `scan_date` date NOT NULL,
  `scan_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `barcode` (`barcode`),
  KEY `company_id` (`company_id`),
  KEY `scan_date` (`scan_date`),
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إضافة بيانات تجريبية للشركات
INSERT IGNORE INTO `companies` (`name`, `is_active`) VALUES 
('شركة النقل السريع', 1),
('شركة الشحن الدولي', 1),
('شركة التوصيل المحلي', 1),
('شركة الشحن السريع', 1),
('شركة النقل البري', 1);

-- إضافة مستخدم مدير افتراضي
INSERT IGNORE INTO `users` (`username`, `password`, `role`, `is_active`) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

-- عرض النتائج
SELECT 'Database setup completed successfully!' as status;
SELECT COUNT(*) as companies_count FROM companies;
SELECT COUNT(*) as users_count FROM users;
SELECT COUNT(*) as shipments_count FROM shipments;
