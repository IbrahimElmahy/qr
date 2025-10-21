<?php
// إعدادات قاعدة البيانات الصحيحة
define('DB_HOST', 'localhost');
define('DB_NAME', 'ztjmal_shipmen');  // اسم قاعدة البيانات الكامل

// ⚠️ غيّر هذه البيانات حسب ما هو موجود في cPanel
define('DB_USER', 'ztjmal_username_here');  // اسم المستخدم الكامل من cPanel
define('DB_PASS', 'password_here');  // كلمة المرور من cPanel

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
