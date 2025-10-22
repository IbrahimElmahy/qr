<?php
// اختبار الاتصال بقاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_NAME', 'ztjmal_shipmen');
define('DB_USER', 'ztjmal_ahmed');
define('DB_PASS', 'Ahmedhelmy12');

echo "<h2>اختبار الاتصال بقاعدة البيانات</h2>";

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
    
    echo "✅ الاتصال بقاعدة البيانات نجح!<br>";
    echo "✅ قاعدة البيانات: " . DB_NAME . "<br>";
    echo "✅ المستخدم: " . DB_USER . "<br>";
    
    // اختبار الجداول
    $tables = ['companies', 'shipments'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch();
            echo "✅ جدول $table: " . $result['count'] . " صف<br>";
        } catch (Exception $e) {
            echo "❌ خطأ في جدول $table: " . $e->getMessage() . "<br>";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage() . "<br>";
    echo "❌ تحقق من إعدادات قاعدة البيانات<br>";
}
?>
