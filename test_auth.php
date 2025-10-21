<?php
// اختبار المصادقة
require_once 'config.php';

echo "<h1>🔐 اختبار المصادقة</h1>";

// اختبار بدون مصادقة
echo "<h2>1. اختبار بدون مصادقة:</h2>";
try {
    authenticate();
    echo "<p>❌ يجب أن يفشل بدون مصادقة</p>";
} catch (Exception $e) {
    echo "<p>✅ فشل كما هو متوقع: " . $e->getMessage() . "</p>";
}

// اختبار مع مصادقة صحيحة
echo "<h2>2. اختبار مع مصادقة صحيحة:</h2>";
$_SERVER['HTTP_AUTHORIZATION'] = 'Basic ' . base64_encode('admin:1234');
try {
    authenticate();
    echo "<p>✅ المصادقة نجحت</p>";
} catch (Exception $e) {
    echo "<p>❌ فشل المصادقة: " . $e->getMessage() . "</p>";
}

// اختبار مع مصادقة خاطئة
echo "<h2>3. اختبار مع مصادقة خاطئة:</h2>";
$_SERVER['HTTP_AUTHORIZATION'] = 'Basic ' . base64_encode('wrong:password');
try {
    authenticate();
    echo "<p>❌ يجب أن يفشل مع مصادقة خاطئة</p>";
} catch (Exception $e) {
    echo "<p>✅ فشل كما هو متوقع: " . $e->getMessage() . "</p>";
}

echo "<h2>4. اختبار APIs:</h2>";
echo "<p><a href='getCompaniesFixed.php' target='_blank'>اختبار getCompaniesFixed.php</a></p>";
echo "<p><a href='getStatsFixed.php' target='_blank'>اختبار getStatsFixed.php</a></p>";

echo "<h2>5. اختبار APIs بدون مصادقة:</h2>";
echo "<p><a href='getCompaniesSimple.php' target='_blank'>اختبار getCompaniesSimple.php</a></p>";
echo "<p><a href='getStatsSimple.php' target='_blank'>اختبار getStatsSimple.php</a></p>";
?>
