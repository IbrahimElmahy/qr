<?php
// إعدادات قاعدة البيانات - تم التحقق من صحتها
define('DB_HOST', 'localhost');
define('DB_NAME', 'ztjmal_shipmen');
define('DB_USER', 'ztjmal_ahmed');
define('DB_PASS', 'Ahmedhelmy12');

// إعدادات التطبيق
define('API_BASE_URL', 'https://zabda-al-tajamil.com/');

// إعدادات المصادقة - تأكد من هذه البيانات
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
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// دالة التحقق من المصادقة
function authenticate() {
    // التحقق من وجود Authorization header
    if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
        // محاولة الحصول من متغيرات أخرى
        $auth = null;
        if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $auth = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']);
        } elseif (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (isset($headers['Authorization'])) {
                $auth = $headers['Authorization'];
            }
        }
        
        if (!$auth) {
            sendResponse(['error' => 'Authorization header required'], 401);
        }
    } else {
        $auth = $_SERVER['HTTP_AUTHORIZATION'];
    }
    
    // التحقق من تنسيق Authorization
    if (strpos($auth, 'Basic ') !== 0) {
        sendResponse(['error' => 'Invalid authorization format'], 401);
    }
    
    // فك تشفير بيانات المصادقة
    $credentials = base64_decode(substr($auth, 6));
    if (!$credentials) {
        sendResponse(['error' => 'Invalid credentials format'], 401);
    }
    
    $parts = explode(':', $credentials, 2);
    if (count($parts) !== 2) {
        sendResponse(['error' => 'Invalid credentials format'], 401);
    }
    
    list($username, $password) = $parts;
    
    // التحقق من بيانات المصادقة
    if ($username !== ADMIN_USERNAME || $password !== ADMIN_PASSWORD) {
        sendResponse(['error' => 'Invalid credentials'], 401);
    }
}

// دالة تنظيف البيانات
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// دالة تسجيل الأخطاء
function logError($message) {
    error_log("API Error: " . $message);
}

// دالة اختبار الاتصال
function testConnection() {
    try {
        $pdo = getDatabaseConnection();
        return ['status' => 'success', 'message' => 'Database connection successful'];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}
?>
