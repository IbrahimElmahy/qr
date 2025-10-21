-- إعادة إنشاء جدول الشحنات بالشكل الصحيح
-- تشغيل هذا الملف في phpMyAdmin

-- حذف الجدول القديم (احذر: سيتم حذف جميع البيانات!)
-- DROP TABLE IF EXISTS shipments;

-- إنشاء جدول الشحنات الجديد
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
  KEY `scan_time` (`scan_time`),
  FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إضافة بيانات تجريبية
INSERT IGNORE INTO `shipments` (`barcode`, `company_id`, `company_name`, `scan_date`, `scan_time`) VALUES 
('1234567890', 1, 'شركة النقل السريع', CURDATE(), NOW()),
('0987654321', 2, 'شركة الشحن الدولي', CURDATE(), NOW()),
('1122334455', 1, 'شركة النقل السريع', CURDATE(), NOW()),
('5566778899', 3, 'شركة التوصيل المحلي', CURDATE(), NOW()),
('9988776655', 2, 'شركة الشحن الدولي', CURDATE(), NOW());

-- عرض البيانات للتأكد
SELECT * FROM shipments LIMIT 5;
