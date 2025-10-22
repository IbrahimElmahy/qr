# Server Upload Guide - حل مشكلة 404 Not Found

## المشكلة
التطبيق يحصل على خطأ 404 Not Found عند محاولة الوصول إلى API. السبب أن ملفات API غير موجودة على السيرفر.

## الحل المطلوب

### 1. الملفات المطلوب رفعها
يجب رفع الملفات التالية إلى مجلد `/shipment_tracking/api/` على السيرفر:

```
api/
├── config.php          (ملف الإعدادات الأساسي)
├── getStats.php        (إحصائيات الشحنات)
├── getCompanies.php    (قائمة الشركات)
├── getShipments.php    (قائمة الشحنات)
├── addCompany.php      (إضافة شركة)
├── addShipment.php     (إضافة شحنة)
└── toggleCompany.php   (تفعيل/إلغاء تفعيل شركة)
```

### 2. خطوات الرفع

#### الطريقة الأولى: عبر cPanel File Manager
1. ادخل إلى cPanel الخاص بك
2. افتح File Manager
3. انتقل إلى مجلد `public_html/shipment_tracking/`
4. أنشئ مجلد `api` إذا لم يكن موجوداً
5. ارفع جميع الملفات من مجلد `api/` المحلي إلى `api/` على السيرفر

#### الطريقة الثانية: عبر FTP
1. استخدم برنامج FTP مثل FileZilla
2. اتصل بالسيرفر باستخدام بيانات FTP الخاصة بك
3. انتقل إلى مجلد `public_html/shipment_tracking/`
4. أنشئ مجلد `api` إذا لم يكن موجوداً
5. ارفع جميع الملفات

### 3. التحقق من الإعدادات

#### في ملف config.php
تأكد من أن إعدادات قاعدة البيانات صحيحة:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ztjmal_shipmen');
define('DB_USER', 'ztjmal_ahmed');
define('DB_PASS', 'Ahmedhelmy12');
```

#### في ملف RetrofitInstance.kt
تأكد من أن الرابط صحيح:
```kotlin
private const val BASE_URL = "https://zabda-al-tajamil.com/shipment_tracking/api/"
```

### 4. اختبار الاتصال

بعد رفع الملفات، اختبر الاتصال عبر المتصفح:
- `https://zabda-al-tajamil.com/shipment_tracking/api/getStats.php`
- `https://zabda-al-tajamil.com/shipment_tracking/api/getCompanies.php`

### 5. إعدادات الأمان

تأكد من أن:
- جميع الملفات لها صلاحيات 644
- مجلد api له صلاحيات 755
- قاعدة البيانات متصلة وتعمل بشكل صحيح

### 6. استكشاف الأخطاء

إذا استمر الخطأ:
1. تحقق من وجود الملفات على السيرفر
2. تحقق من إعدادات قاعدة البيانات
3. تحقق من سجلات الأخطاء في cPanel
4. تأكد من أن PHP يعمل بشكل صحيح

## الملفات المطلوبة للرفع

### config.php
```php
<?php
// إعدادات قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_NAME', 'ztjmal_shipmen');
define('DB_USER', 'ztjmal_ahmed');
define('DB_PASS', 'Ahmedhelmy12');

// إعدادات التطبيق
define('API_BASE_URL', 'http://localhost/shipment_tracking/api/');

// إعدادات المصادقة
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', '1234');

// دالة الاتصال بقاعدة البيانات
function getDatabaseConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        
        // إعداد UTF-8
        $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("SET CHARACTER SET utf8mb4");
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
        exit;
    }
}

// دالة إرسال الاستجابة
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// دالة التحقق من المصادقة
function authenticate() {
    if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
        sendResponse(['error' => 'Authorization header required'], 401);
    }
    
    $auth = $_SERVER['HTTP_AUTHORIZATION'];
    if (strpos($auth, 'Basic ') !== 0) {
        sendResponse(['error' => 'Invalid authorization format'], 401);
    }
    
    $credentials = base64_decode(substr($auth, 6));
    list($username, $password) = explode(':', $credentials, 2);
    
    if ($username !== ADMIN_USERNAME || $password !== ADMIN_PASSWORD) {
        sendResponse(['error' => 'Invalid credentials'], 401);
    }
}

// دالة تنظيف البيانات
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>
```

## ملاحظات مهمة

1. **الأمان**: تأكد من أن كلمات المرور في config.php آمنة
2. **النسخ الاحتياطي**: قم بعمل نسخة احتياطية من الملفات الموجودة قبل الرفع
3. **الاختبار**: اختبر كل ملف على حدة بعد الرفع
4. **السجلات**: راقب سجلات الأخطاء في cPanel

## بعد الرفع

1. اختبر التطبيق مرة أخرى
2. استخدم زر "إعادة المحاولة" في التطبيق
3. تحقق من أن البيانات تظهر بشكل صحيح
