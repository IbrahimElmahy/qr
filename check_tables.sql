-- فحص الجداول الموجودة
-- تشغيل هذا الملف في phpMyAdmin

-- عرض جميع الجداول
SHOW TABLES;

-- فحص هيكل جدول الشركات
DESCRIBE companies;

-- فحص هيكل جدول الشحنات
DESCRIBE shipments;

-- فحص البيانات الموجودة
SELECT COUNT(*) as companies_count FROM companies;
SELECT COUNT(*) as shipments_count FROM shipments;
