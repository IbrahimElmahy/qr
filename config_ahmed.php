<?php
// إعدادات قاعدة البيانات الصحيحة
define('DB_HOST', 'localhost');
define('DB_NAME', 'ztjmal_shipmen');  // اسم قاعدة البيانات الكامل
define('DB_USER', 'ztjmal_ahmed');  // اسم المستخدم الكامل
define('DB_PASS', 'Ahmedhelmy12');  // كلمة المرور

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
    // التحقق من وجود header المصادقة
    $auth = null;
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $auth = $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    } elseif (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $auth = $headers['Authorization'];
        }
    }
    
    if (!$auth) {
        throw new Exception('Authorization header required');
    }
    
    if (strpos($auth, 'Basic ') !== 0) {
        throw new Exception('Invalid authorization format');
    }
    
    $credentials = base64_decode(substr($auth, 6));
    if (!$credentials) {
        throw new Exception('Invalid base64 encoding');
    }
    
    $parts = explode(':', $credentials, 2);
    if (count($parts) !== 2) {
        throw new Exception('Invalid credentials format');
    }
    
    list($username, $password) = $parts;
    
    if ($username !== ADMIN_USERNAME || $password !== ADMIN_PASSWORD) {
        throw new Exception('Invalid username or password');
    }
}

// دالة تنظيف البيانات
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>
