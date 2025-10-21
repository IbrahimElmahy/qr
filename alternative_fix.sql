-- إصلاح بديل لجدول الشحنات
-- تشغيل هذا الملف في phpMyAdmin

-- محاولة إضافة الأعمدة مع تجاهل الأخطاء
ALTER TABLE shipments ADD COLUMN scan_time TIME NOT NULL DEFAULT '00:00:00';
ALTER TABLE shipments ADD COLUMN company_name VARCHAR(100) NOT NULL DEFAULT '';

-- تحديث البيانات الموجودة
UPDATE shipments SET scan_time = TIME(created_at) WHERE scan_time = '00:00:00';

-- إضافة فهارس للأعمدة الجديدة
ALTER TABLE shipments ADD INDEX idx_scan_time (scan_time);
ALTER TABLE shipments ADD INDEX idx_company_name (company_name);

-- عرض البيانات للتأكد
SELECT id, barcode, company_name, scan_date, scan_time, created_at FROM shipments LIMIT 5;
