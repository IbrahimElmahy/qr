<?php
// اختبار بسيط بدون مصادقة
require_once 'config.php';

echo "<h1>🧪 اختبار النظام البسيط</h1>";

try {
    $pdo = getDatabaseConnection();
    echo "<p>✅ الاتصال بقاعدة البيانات نجح</p>";
    
    // اختبار جلب الشركات
    $stmt = $pdo->prepare("SELECT * FROM companies ORDER BY name");
    $stmt->execute();
    $companies = $stmt->fetchAll();
    
    echo "<h2>📋 الشركات:</h2>";
    echo "<ul>";
    foreach ($companies as $company) {
        $status = $company['is_active'] ? '✅ مفعلة' : '❌ معطلة';
        echo "<li>{$company['name']} - {$status}</li>";
    }
    echo "</ul>";
    
    // اختبار جلب الشحنات
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM shipments");
    $stmt->execute();
    $result = $stmt->fetch();
    
    echo "<h2>📦 الشحنات:</h2>";
    echo "<p>إجمالي الشحنات: {$result['total']}</p>";
    
    // اختبار الإحصائيات
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT barcode) as unique_shipments,
            COUNT(*) as total_scans,
            COUNT(*) - COUNT(DISTINCT barcode) as duplicates
        FROM shipments 
        WHERE scan_date = CURDATE()
    ");
    $stmt->execute();
    $stats = $stmt->fetch();
    
    echo "<h2>📊 الإحصائيات اليومية:</h2>";
    echo "<ul>";
    echo "<li>الشحنات الفريدة: {$stats['unique_shipments']}</li>";
    echo "<li>إجمالي المسحات: {$stats['total_scans']}</li>";
    echo "<li>المكررة: {$stats['duplicates']}</li>";
    echo "</ul>";
    
    echo "<h2>🎉 النظام يعمل بشكل مثالي!</h2>";
    
} catch (PDOException $e) {
    echo "<p>❌ خطأ في قاعدة البيانات: " . $e->getMessage() . "</p>";
}
?>
