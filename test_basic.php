<?php
// اختبار أساسي بدون تعقيدات
echo "<h1>🧪 اختبار أساسي</h1>";

// اختبار PHP
echo "<p>✅ PHP يعمل</p>";

// اختبار الاتصال بقاعدة البيانات
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=ztjmal_shipmen;charset=utf8mb4",
        "ztjmal_ahmed",
        "Ahmedhelmy12",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    echo "<p>✅ الاتصال بقاعدة البيانات نجح</p>";
    
    // اختبار جلب الشركات
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM companies");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>✅ عدد الشركات: {$result['count']}</p>";
    
    // اختبار جلب الشحنات
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM shipments");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>✅ عدد الشحنات: {$result['count']}</p>";
    
    echo "<h2>🎉 النظام يعمل بشكل مثالي!</h2>";
    
} catch (PDOException $e) {
    echo "<p>❌ خطأ في قاعدة البيانات: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    echo "<p>❌ خطأ عام: " . $e->getMessage() . "</p>";
}
?>
