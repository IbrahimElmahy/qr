# 📤 تعليمات رفع النظام على السيرفر

## 🎯 الهدف:
رفع نظام تتبع الشحنات على السيرفر: **zabda-al-tajamil.com**

## 📁 الملفات الجاهزة:
✅ تم إنشاء ملف: `shipment_system.zip` يحتوي على:
- مجلد `api` (APIs النظام)
- مجلد `website` (الموقع الإداري)
- ملف `database_setup.sql` (إعداد قاعدة البيانات)
- ملف `test_integration.php` (اختبار النظام)

## 🚀 خطوات الرفع:

### الخطوة 1: تسجيل الدخول لـ cPanel
1. اذهب إلى: **https://cpanel.zabda-al-tajamil.com**
2. سجل دخول بحسابك
3. ابحث عن **"File Manager"** أو **"مدير الملفات"**

### الخطوة 2: رفع الملفات
1. في File Manager، اذهب إلى مجلد **`public_html`**
2. ارفع ملف **`shipment_system.zip`**
3. اضغط كليك يمين على الملف → **"Extract"** أو **"استخراج"**
4. احذف ملف ZIP بعد الاستخراج

### الخطوة 3: تنظيم الملفات
بعد الاستخراج، يجب أن تكون الملفات كالتالي:
```
public_html/
├── website/          (الموقع الإداري)
├── shipment_tracking/
│   └── api/          (APIs النظام)
├── database_setup.sql
└── test_integration.php
```

### الخطوة 4: إعداد قاعدة البيانات
1. في cPanel، ابحث عن **"MySQL Databases"**
2. أنشئ قاعدة بيانات جديدة: **`shipment_tracking`**
3. أنشئ مستخدم جديد للقاعدة
4. اربط المستخدم بقاعدة البيانات
5. اذهب إلى **"phpMyAdmin"**
6. اختر قاعدة البيانات الجديدة
7. اضغط **"Import"** أو **"استيراد"**
8. ارفع ملف **`database_setup.sql`** واشغله

### الخطوة 5: تحديث إعدادات قاعدة البيانات
عدّل ملف **`shipment_tracking/api/config.php`**:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'اسم_قاعدة_البيانات_الفعلي');
define('DB_USER', 'اسم_المستخدم_الفعلي');
define('DB_PASS', 'كلمة_المرور_الفعلة');
```

### الخطوة 6: اختبار النظام
1. اذهب إلى: **https://zabda-al-tajamil.com/website/login.php**
2. سجل دخول بـ:
   - اسم المستخدم: **admin**
   - كلمة المرور: **1234**
3. اختبر جميع الصفحات

### الخطوة 7: تحديث التطبيق
عدّل ملف **`RetrofitInstance.kt`** في التطبيق:
```kotlin
private const val BASE_URL = "https://zabda-al-tajamil.com/shipment_tracking/api/"
```

## 🌐 الروابط النهائية:
- **تسجيل الدخول**: https://zabda-al-tajamil.com/website/login.php
- **لوحة التحكم**: https://zabda-al-tajamil.com/website/dashboard.php
- **استعراض الشحنات**: https://zabda-al-tajamil.com/website/shipments.php
- **اختبار التكامل**: https://zabda-al-tajamil.com/test_integration.php

## 🔑 بيانات الدخول:
- **اسم المستخدم**: admin
- **كلمة المرور**: 1234

## ✅ قائمة التحقق:
- [ ] رفع ملف shipment_system.zip
- [ ] استخراج الملفات
- [ ] إنشاء قاعدة البيانات
- [ ] استيراد database_setup.sql
- [ ] تحديث config.php
- [ ] اختبار الموقع
- [ ] تحديث التطبيق

## 🆘 في حالة وجود مشاكل:
1. تأكد من صحة بيانات قاعدة البيانات
2. تحقق من صلاحيات الملفات (755 للمجلدات، 644 للملفات)
3. راجع سجلات الأخطاء في cPanel
4. تأكد من دعم PHP 7.4+ في السيرفر

---
**النظام جاهز للرفع! 🚀**
