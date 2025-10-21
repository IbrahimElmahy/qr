-- إضافة الأعمدة المفقودة بأمان
-- تشغيل هذا الملف في phpMyAdmin

-- التحقق من وجود الأعمدة وإضافتها إذا لم تكن موجودة
SET @sql = '';

-- إضافة عمود scan_time
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'shipments' 
AND COLUMN_NAME = 'scan_time';

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE shipments ADD COLUMN scan_time TIME NOT NULL DEFAULT "00:00:00"', 
    'SELECT "Column scan_time already exists" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- إضافة عمود company_name
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'shipments' 
AND COLUMN_NAME = 'company_name';

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE shipments ADD COLUMN company_name VARCHAR(100) NOT NULL DEFAULT ""', 
    'SELECT "Column company_name already exists" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- تحديث البيانات الموجودة
UPDATE shipments SET scan_time = TIME(created_at) WHERE scan_time = '00:00:00';

-- عرض هيكل الجدول النهائي
DESCRIBE shipments;
