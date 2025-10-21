# 🚀 رفع النظام على السيرفر الحقيقي

## 📋 بيانات السيرفر:
- **الموقع**: zabda-al-tajamil.com
- **cPanel**: cpanel.zabda-al-tajamil.com

## 🔧 خطوات الرفع:

### 1. تسجيل الدخول لـ cPanel:
1. اذهب إلى: https://cpanel.zabda-al-tajamil.com
2. سجل دخول بحسابك
3. ابحث عن "File Manager" أو "مدير الملفات"

### 2. رفع ملفات النظام:
1. اذهب إلى مجلد `public_html` أو `www`
2. أنشئ مجلد جديد: `shipment_tracking`
3. ارفع جميع ملفات مجلد `api` إلى `shipment_tracking/api/`
4. ارفع جميع ملفات مجلد `website` إلى `public_html/website/`

### 3. إعداد قاعدة البيانات:
1. في cPanel، ابحث عن "MySQL Databases"
2. أنشئ قاعدة بيانات جديدة: `shipment_tracking`
3. أنشئ مستخدم جديد للقاعدة
4. اربط المستخدم بقاعدة البيانات
5. ارفع ملف `database_setup.sql` واشغله

### 4. تحديث إعدادات النظام:
عدّل ملف `api/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 5. تحديث التطبيق:
عدّل رابط السيرفر في `RetrofitInstance.kt`:
```kotlin
private const val BASE_URL = "https://zabda-al-tajamil.com/shipment_tracking/api/"
```

## 🌐 الروابط النهائية:
- **تسجيل الدخول**: https://zabda-al-tajamil.com/website/login.php
- **لوحة التحكم**: https://zabda-al-tajamil.com/website/dashboard.php
- **استعراض الشحنات**: https://zabda-al-tajamil.com/website/shipments.php

## 🔑 بيانات الدخول:
- **اسم المستخدم**: admin
- **كلمة المرور**: 1234
