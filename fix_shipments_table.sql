-- إصلاح جدول الشحنات
-- تشغيل هذا الملف في phpMyAdmin

-- إضافة عمود scan_time إذا لم يكن موجوداً
ALTER TABLE shipments ADD COLUMN IF NOT EXISTS scan_time TIME NOT NULL DEFAULT '00:00:00';

-- إضافة عمود company_name إذا لم يكن موجوداً
ALTER TABLE shipments ADD COLUMN IF NOT EXISTS company_name VARCHAR(100) NOT NULL DEFAULT '';

-- تحديث البيانات الموجودة
UPDATE shipments SET scan_time = TIME(created_at) WHERE scan_time = '00:00:00';

-- إضافة فهرس للعمود الجديد
ALTER TABLE shipments ADD INDEX IF NOT EXISTS idx_scan_time (scan_time);

-- عرض هيكل الجدول للتأكد
DESCRIBE shipments;
