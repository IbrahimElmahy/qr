# حل مباشر لمشكلة 404 - رفع الملفات مباشرة

## 🚨 المشكلة
```
404 Not Found
The requested URL was not found on this server.
```

## 🎯 الحل المباشر

### الخطوة 1: رفع الملفات مباشرة إلى الجذر
ارفع الملفات إلى `public_html/` مباشرة (وليس في مجلد api)

### الخطوة 2: تحديث BASE_URL
```kotlin
private const val BASE_URL = "https://zabda-al-tajamil.com/"
```

### الخطوة 3: اختبار الرابط
```
https://zabda-al-tajamil.com/getStats.php
```

## 📁 الملفات المطلوبة للرفع

### 1. config.php
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

### 2. getStats.php
```php
<?php
require_once 'config.php';

// التحقق من نوع الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse(['error' => 'Method not allowed'], 405);
}

// التحقق من المصادقة
authenticate();

// قراءة المعاملات
$companyId = isset($_GET['company_id']) ? (int)$_GET['company_id'] : null;
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

try {
    $pdo = getDatabaseConnection();
    
    // بناء الاستعلام
    $whereClause = "WHERE s.scan_date = ?";
    $params = [$date];
    
    if ($companyId) {
        $whereClause .= " AND s.company_id = ?";
        $params[] = $companyId;
    }
    
    // إحصائيات عامة
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT s.barcode) as total_unique_shipments,
            COUNT(s.id) as total_scans,
            (COUNT(s.id) - COUNT(DISTINCT s.barcode)) as duplicate_count
        FROM shipments s 
        $whereClause
    ");
    $stmt->execute($params);
    $stats = $stmt->fetch();
    
    // قائمة الشحنات مع عدد التكرار
    $stmt = $pdo->prepare("
        SELECT 
            s.barcode,
            c.name as company_name,
            COUNT(s.id) as scan_count,
            MIN(s.created_at) as first_scan,
            MAX(s.created_at) as last_scan
        FROM shipments s 
        JOIN companies c ON s.company_id = c.id 
        $whereClause
        GROUP BY s.barcode, c.name
        ORDER BY scan_count DESC, s.barcode
    ");
    $stmt->execute($params);
    $shipments = $stmt->fetchAll();
    
    // إحصائيات حسب الشركة
    $stmt = $pdo->prepare("
        SELECT 
            c.id,
            c.name,
            COUNT(DISTINCT s.barcode) as unique_shipments,
            COUNT(s.id) as total_scans
        FROM companies c 
        LEFT JOIN shipments s ON c.id = s.company_id AND s.scan_date = ?
        " . ($companyId ? "WHERE c.id = ?" : "") . "
        GROUP BY c.id, c.name
        ORDER BY c.name
    ");
    
    $companyParams = [$date];
    if ($companyId) {
        $companyParams[] = $companyId;
    }
    $stmt->execute($companyParams);
    $companyStats = $stmt->fetchAll();
    
    sendResponse([
        'success' => true,
        'date' => $date,
        'company_id' => $companyId,
        'statistics' => [
            'total_unique_shipments' => (int)$stats['total_unique_shipments'],
            'total_scans' => (int)$stats['total_scans'],
            'duplicate_count' => (int)$stats['duplicate_count']
        ],
        'shipments' => $shipments,
        'company_statistics' => $companyStats
    ]);
    
} catch (PDOException $e) {
    sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
```

### 3. getCompanies.php
```php
<?php
require_once 'config.php';

// التحقق من نوع الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse(['error' => 'Method not allowed'], 405);
}

// التحقق من المصادقة
authenticate();

try {
    $pdo = getDatabaseConnection();
    
    // جلب جميع الشركات
    $stmt = $pdo->prepare("
        SELECT 
            c.id,
            c.name,
            c.is_active,
            c.created_at,
            COUNT(s.id) as total_shipments,
            COUNT(DISTINCT s.scan_date) as active_days
        FROM companies c 
        LEFT JOIN shipments s ON c.id = s.company_id
        GROUP BY c.id, c.name, c.is_active, c.created_at
        ORDER BY c.name
    ");
    $stmt->execute();
    $companies = $stmt->fetchAll();
    
    sendResponse([
        'success' => true,
        'companies' => $companies
    ]);
    
} catch (PDOException $e) {
    sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
```

## 🛠️ خطوات الرفع

### 1. عبر cPanel File Manager:
1. ادخل إلى cPanel
2. افتح File Manager
3. انتقل إلى `public_html`
4. ارفع الملفات مباشرة إلى `public_html/` (وليس في مجلد فرعي)

### 2. عبر FTP:
1. اتصل بالسيرفر عبر FTP
2. انتقل إلى `public_html`
3. ارفع الملفات مباشرة

## 🔧 تحديث التطبيق

في ملف `RetrofitInstance.kt`:
```kotlin
private const val BASE_URL = "https://zabda-al-tajamil.com/"
```

## 🧪 اختبار بعد الرفع

### 1. اختبر الرابط:
```
https://zabda-al-tajamil.com/getStats.php
```

### 2. إذا حصلت على خطأ 401:
- هذا طبيعي! يعني أن الملف يعمل ولكن يحتاج مصادقة

### 3. إذا حصلت على خطأ 404:
- تحقق من وجود الملفات في `public_html/`
- تحقق من الصلاحيات (644 للملفات)

## 📱 اختبار التطبيق

1. أعد بناء التطبيق
2. شغّل التطبيق
3. اذهب إلى صفحة الإحصائيات
4. استخدم زر "إعادة المحاولة"
5. تحقق من ظهور البيانات
