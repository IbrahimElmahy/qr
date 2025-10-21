# 🎯 الإعداد النهائي مع قاعدة البيانات `shipmen`

## ✅ إعداد قاعدة البيانات:

### 1. في cPanel:
- **Database Name**: `shipmen`
- **الاسم الكامل**: `ztjmal_shipmen`
- **Character Set**: `utf8mb4_unicode_ci`
- **User**: أنشئ مستخدم جديد (مثل: `shipment_user`)

### 2. بعد إنشاء قاعدة البيانات:
```php
// في ملف shipment_tracking/api/config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ztjmal_shipmen');
define('DB_USER', 'ztjmal_shipment_user');  // أو اسم المستخدم الذي أنشأته
define('DB_PASS', 'كلمة_المرور_التي_اخترتها');
```

## 📋 الخطوات النهائية:

### 1. إنشاء قاعدة البيانات:
1. اذهب إلى cPanel
2. أنشئ قاعدة بيانات: `shipmen`
3. أنشئ مستخدم جديد
4. اربط المستخدم بقاعدة البيانات

### 2. استيراد الجداول:
1. اذهب إلى phpMyAdmin
2. اختر قاعدة البيانات: `ztjmal_shipmen`
3. اضغط "Import"
4. ارفع ملف `tables_only.sql`

### 3. تحديث الإعدادات:
1. افتح ملف `shipment_tracking/api/config.php`
2. حدث بيانات قاعدة البيانات كما هو موضح أعلاه

### 4. اختبار النظام:
- **تسجيل الدخول**: https://zabda-al-tajamil.com/website/login.php
- **بيانات الدخول**: admin / 1234

## 🔧 الملفات المحدثة:

### 1. database_setup.sql:
- ✅ اسم قاعدة البيانات: `shipmen`
- ✅ يحتوي على إنشاء قاعدة البيانات

### 2. tables_only.sql:
- ✅ إنشاء الجداول فقط
- ✅ بيانات تجريبية
- ✅ فهارس محسنة

### 3. config_updated.php:
- ✅ إعدادات محدثة لقاعدة البيانات
- ✅ اسم قاعدة البيانات: `ztjmal_shipmen`

## 🌐 الروابط النهائية:

- **تسجيل الدخول**: https://zabda-al-tajamil.com/website/login.php
- **لوحة التحكم**: https://zabda-al-tajamil.com/website/dashboard.php
- **استعراض الشحنات**: https://zabda-al-tajamil.com/website/shipments.php

## 🔑 بيانات الدخول:
- **اسم المستخدم**: admin
- **كلمة المرور**: 1234

---
**النظام جاهز مع قاعدة البيانات `shipmen`! 🎉**
