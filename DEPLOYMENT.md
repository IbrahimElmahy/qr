# 🚀 دليل النشر

## 📋 نظرة عامة
هذا الدليل يوضح كيفية نشر نظام تتبع الشحنات على خادم الإنتاج.

## 🛠️ المتطلبات

### **الخادم**:
- **PHP**: 8.0 أو أحدث
- **MySQL**: 8.0 أو أحدث
- **Apache**: 2.4 أو أحدث
- **SSL Certificate**: للاتصال الآمن
- **Domain**: للوصول للموقع

### **البيئة المحلية**:
- **Android Studio**: 2023.1 أو أحدث
- **Java**: 17 أو أحدث
- **Git**: 2.30 أو أحدث

## 🌐 نشر الموقع

### **الخطوة 1 - إعداد قاعدة البيانات**:
```sql
-- إنشاء قاعدة البيانات
CREATE DATABASE ztjmal_shipmen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- إنشاء المستخدم
CREATE USER 'ztjmal_ahmed'@'localhost' IDENTIFIED BY 'Ahmedhelmy12';
GRANT ALL PRIVILEGES ON ztjmal_shipmen.* TO 'ztjmal_ahmed'@'localhost';
FLUSH PRIVILEGES;
```

### **الخطوة 2 - رفع ملفات الموقع**:
1. **ارفع ملفات `website/`** إلى مجلد الموقع
2. **ارفع ملفات `api/`** إلى مجلد API
3. **حدث إعدادات قاعدة البيانات** في `api/config.php`

### **الخطوة 3 - إعداد قاعدة البيانات**:
```bash
# تشغيل ملفات SQL
mysql -u ztjmal_ahmed -p ztjmal_shipmen < complete_database_setup.sql
mysql -u ztjmal_ahmed -p ztjmal_shipmen < fix_database_encoding.sql
```

### **الخطوة 4 - اختبار الموقع**:
1. افتح الموقع في المتصفح
2. اختبر تسجيل الدخول
3. اختبر جميع الميزات
4. تأكد من عمل API

## 📱 نشر التطبيق

### **الخطوة 1 - بناء التطبيق**:
1. افتح المشروع في Android Studio
2. File → Project Structure → SDK Location
3. اختر "Use embedded JDK"
4. Build → Clean Project
5. Build → Rebuild Project
6. Build → Build APK

### **الخطوة 2 - توقيع التطبيق**:
```bash
# إنشاء keystore
keytool -genkey -v -keystore my-release-key.keystore -alias alias_name -keyalg RSA -keysize 2048 -validity 10000

# توقيع APK
jarsigner -verbose -sigalg SHA1withRSA -digestalg SHA1 -keystore my-release-key.keystore app-release-unsigned.apk alias_name
```

### **الخطوة 3 - تحسين APK**:
```bash
# تحسين APK
zipalign -v 4 app-release-unsigned.apk app-release.apk
```

## 🔧 إعدادات الإنتاج

### **قاعدة البيانات**:
```php
// api/config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ztjmal_shipmen');
define('DB_USER', 'ztjmal_ahmed');
define('DB_PASS', 'Ahmedhelmy12');
define('DB_CHARSET', 'utf8mb4');
```

### **الأمان**:
```php
// إعدادات الأمان
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log');

// HTTPS
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}
```

### **الأداء**:
```apache
# .htaccess
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# ضغط الملفات
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
```

## 📊 المراقبة والصيانة

### **مراقبة الأداء**:
- **Google Analytics**: لتتبع الزوار
- **Server Monitoring**: لمراقبة الخادم
- **Database Monitoring**: لمراقبة قاعدة البيانات

### **النسخ الاحتياطي**:
```bash
# نسخ احتياطي لقاعدة البيانات
mysqldump -u ztjmal_ahmed -p ztjmal_shipmen > backup_$(date +%Y%m%d_%H%M%S).sql

# نسخ احتياطي للملفات
tar -czf website_backup_$(date +%Y%m%d_%H%M%S).tar.gz /path/to/website/
```

### **التحديثات**:
1. **اختبار التحديثات** في بيئة التطوير
2. **نسخ احتياطي** قبل التحديث
3. **تطبيق التحديثات** تدريجياً
4. **مراقبة الأداء** بعد التحديث

## 🔍 استكشاف الأخطاء

### **مشاكل قاعدة البيانات**:
```sql
-- فحص الاتصال
SELECT 1;

-- فحص الجداول
SHOW TABLES;

-- فحص البيانات
SELECT COUNT(*) FROM companies;
SELECT COUNT(*) FROM users;
SELECT COUNT(*) FROM shipments;
```

### **مشاكل API**:
```bash
# اختبار API
curl -u admin:1234 "https://zabda-al-tajamil.com/shipment_tracking/api/getCompanies.php"
curl -u admin:1234 "https://zabda-al-tajamil.com/shipment_tracking/api/getStats.php"
```

### **مشاكل التطبيق**:
1. **فحص Logcat** في Android Studio
2. **اختبار الاتصال** بالخادم
3. **فحص الصلاحيات** في التطبيق

## 📞 الدعم

إذا واجهت أي مشاكل في النشر:
1. تحقق من ملفات السجل
2. اختبر الاتصال بقاعدة البيانات
3. اختبر API endpoints
4. تواصل مع المطور

---

**تم التطوير بواسطة**: أحمد حليم  
**التاريخ**: 2025
