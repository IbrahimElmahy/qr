-- إصلاح ترميز قاعدة البيانات
-- تشغيل هذا الملف في phpMyAdmin

-- تغيير ترميز قاعدة البيانات
ALTER DATABASE ztjmal_shipmen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- تغيير ترميز جدول الشركات
ALTER TABLE companies CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- تغيير ترميز جدول الشحنات
ALTER TABLE shipments CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- تغيير ترميز جدول المستخدمين
ALTER TABLE users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- إعدادات إضافية للترميز
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET CHARACTER SET utf8mb4;
