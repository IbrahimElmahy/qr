-- إنشاء جدول شحنات جديد بالشكل الصحيح
-- تشغيل هذا الملف في phpMyAdmin

-- إنشاء جدول مؤقت
CREATE TABLE shipments_temp LIKE shipments;

-- إضافة الأعمدة المطلوبة
ALTER TABLE shipments_temp ADD COLUMN scan_time TIME NOT NULL DEFAULT '00:00:00';
ALTER TABLE shipments_temp ADD COLUMN company_name VARCHAR(100) NOT NULL DEFAULT '';

-- نسخ البيانات من الجدول القديم
INSERT INTO shipments_temp (id, barcode, company_id, scan_date, created_at, updated_at, scan_time, company_name)
SELECT 
    id, 
    barcode, 
    company_id, 
    scan_date, 
    created_at, 
    updated_at,
    TIME(created_at) as scan_time,
    (SELECT name FROM companies WHERE companies.id = shipments.company_id) as company_name
FROM shipments;

-- حذف الجدول القديم
DROP TABLE shipments;

-- إعادة تسمية الجدول المؤقت
RENAME TABLE shipments_temp TO shipments;

-- إضافة الفهارس
ALTER TABLE shipments ADD INDEX idx_scan_time (scan_time);
ALTER TABLE shipments ADD INDEX idx_company_name (company_name);

-- عرض البيانات النهائية
SELECT * FROM shipments LIMIT 5;
