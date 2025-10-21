# 🎉 إعداد النظام لأحمد

## ✅ بيانات قاعدة البيانات:
- **اسم قاعدة البيانات**: `ztjmal_shipmen`
- **اسم المستخدم**: `ztjmal_ahmed`
- **كلمة المرور**: `Ahmedhelmy12`

## 🚀 الخطوات النهائية:

### 1. تحديث ملف config.php:
1. اذهب إلى مجلد `shipment_tracking/api/`
2. افتح ملف `config.php`
3. استبدل المحتوى بالكود التالي:

```php
<?php
// إعدادات قاعدة البيانات الصحيحة
define('DB_HOST', 'localhost');
define('DB_NAME', 'ztjmal_shipmen');
define('DB_USER', 'ztjmal_ahmed');
define('DB_PASS', 'Ahmedhelmy12');

// إعدادات التطبيق
define('API_BASE_URL', 'https://zabda-al-tajamil.com/shipment_tracking/api/');

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

### 2. اختبار النظام:
- **تسجيل الدخول**: https://zabda-al-tajamil.com/website/login.php
- **بيانات الدخول**: admin / 1234

### 3. اختبار APIs:
- **اختبار التكامل**: https://zabda-al-tajamil.com/test_integration.php

## 🌐 الروابط النهائية:

### الموقع الإداري:
- **تسجيل الدخول**: https://zabda-al-tajamil.com/website/login.php
- **لوحة التحكم**: https://zabda-al-tajamil.com/website/dashboard.php
- **استعراض الشحنات**: https://zabda-al-tajamil.com/website/shipments.php

### APIs:
- **قاعدة APIs**: https://zabda-al-tajamil.com/shipment_tracking/api/
- **اختبار التكامل**: https://zabda-al-tajamil.com/test_integration.php

## 🔑 بيانات الدخول:
- **اسم المستخدم**: admin
- **كلمة المرور**: 1234

## 📱 التطبيق:
- **رابط السيرفر**: `https://zabda-al-tajamil.com/shipment_tracking/api/`
- **بيانات المصادقة**: admin / 1234
- **جاهز للاستخدام** بعد تحديث ملف config.php

---
**النظام جاهز الآن! 🎉**
