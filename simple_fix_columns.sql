-- إصلاح بسيط لجدول الشحنات
-- تشغيل هذا الملف في phpMyAdmin

-- إضافة عمود scan_time
ALTER TABLE shipments ADD COLUMN scan_time TIME NOT NULL DEFAULT '00:00:00';

-- إضافة عمود company_name
ALTER TABLE shipments ADD COLUMN company_name VARCHAR(100) NOT NULL DEFAULT '';

-- تحديث البيانات الموجودة
UPDATE shipments SET scan_time = TIME(created_at) WHERE scan_time = '00:00:00';

-- عرض هيكل الجدول
DESCRIBE shipments;
