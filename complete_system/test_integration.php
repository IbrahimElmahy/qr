<?php
/**
 * ملف اختبار التكامل بين التطبيق والموقع
 * يمكن تشغيله من المتصفح لاختبار جميع APIs
 */

require_once 'api/config.php';

echo "<h1>اختبار تكامل النظام</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .test{background:#f0f0f0;padding:10px;margin:10px 0;border-radius:5px;} .success{background:#d4edda;color:#155724;} .error{background:#f8d7da;color:#721c24;}</style>";

// اختبار الاتصال بقاعدة البيانات
echo "<div class='test'>";
echo "<h2>1. اختبار الاتصال بقاعدة البيانات</h2>";
try {
    $pdo = getDatabaseConnection();
    echo "<div class='success'>✅ الاتصال بقاعدة البيانات نجح</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ فشل الاتصال بقاعدة البيانات: " . $e->getMessage() . "</div>";
}
echo "</div>";

// اختبار جلب الشركات
echo "<div class='test'>";
echo "<h2>2. اختبار جلب الشركات</h2>";
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM companies");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<div class='success'>✅ عدد الشركات في قاعدة البيانات: " . $result['count'] . "</div>";
    
    if ($result['count'] == 0) {
        echo "<div class='error'>⚠️ لا توجد شركات في قاعدة البيانات. يرجى تشغيل database_setup.sql</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ خطأ في جلب الشركات: " . $e->getMessage() . "</div>";
}
echo "</div>";

// اختبار إضافة شركة تجريبية
echo "<div class='test'>";
echo "<h2>3. اختبار إضافة شركة تجريبية</h2>";
try {
    $stmt = $pdo->prepare("INSERT IGNORE INTO companies (name, is_active) VALUES ('شركة تجريبية', 1)");
    $stmt->execute();
    echo "<div class='success'>✅ تم إضافة شركة تجريبية</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ خطأ في إضافة الشركة: " . $e->getMessage() . "</div>";
}
echo "</div>";

// اختبار إضافة شحنة تجريبية
echo "<div class='test'>";
echo "<h2>4. اختبار إضافة شحنة تجريبية</h2>";
try {
    $stmt = $pdo->prepare("INSERT INTO shipments (barcode, company_id, scan_date) VALUES ('TEST123', 1, CURDATE())");
    $stmt->execute();
    echo "<div class='success'>✅ تم إضافة شحنة تجريبية</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ خطأ في إضافة الشحنة: " . $e->getMessage() . "</div>";
}
echo "</div>";

// اختبار الإحصائيات
echo "<div class='test'>";
echo "<h2>5. اختبار الإحصائيات</h2>";
try {
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT barcode) as total_unique_shipments,
            COUNT(id) as total_scans,
            (COUNT(id) - COUNT(DISTINCT barcode)) as duplicate_count
        FROM shipments 
        WHERE scan_date = CURDATE()
    ");
    $stmt->execute();
    $stats = $stmt->fetch();
    
    echo "<div class='success'>✅ الإحصائيات اليومية:</div>";
    echo "<ul>";
    echo "<li>إجمالي الشحنات: " . $stats['total_unique_shipments'] . "</li>";
    echo "<li>إجمالي المسحات: " . $stats['total_scans'] . "</li>";
    echo "<li>الشحنات المكررة: " . $stats['duplicate_count'] . "</li>";
    echo "</ul>";
} catch (Exception $e) {
    echo "<div class='error'>❌ خطأ في جلب الإحصائيات: " . $e->getMessage() . "</div>";
}
echo "</div>";

// اختبار APIs
echo "<div class='test'>";
echo "<h2>6. اختبار APIs</h2>";

$baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/api/";

// اختبار getCompanies.php
echo "<h3>اختبار getCompanies.php</h3>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . "getCompanies.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "admin:1234");
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    $data = json_decode($response, true);
    if ($data && $data['success']) {
        echo "<div class='success'>✅ getCompanies.php يعمل بشكل صحيح</div>";
    } else {
        echo "<div class='error'>❌ getCompanies.php يعيد استجابة خاطئة</div>";
    }
} else {
    echo "<div class='error'>❌ getCompanies.php فشل مع كود: " . $httpCode . "</div>";
}

// اختبار getStats.php
echo "<h3>اختبار getStats.php</h3>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . "getStats.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "admin:1234");
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    $data = json_decode($response, true);
    if ($data && $data['success']) {
        echo "<div class='success'>✅ getStats.php يعمل بشكل صحيح</div>";
    } else {
        echo "<div class='error'>❌ getStats.php يعيد استجابة خاطئة</div>";
    }
} else {
    echo "<div class='error'>❌ getStats.php فشل مع كود: " . $httpCode . "</div>";
}

echo "</div>";

// اختبار الموقع
echo "<div class='test'>";
echo "<h2>7. اختبار الموقع</h2>";
$websiteUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/website/";

echo "<p>روابط الموقع:</p>";
echo "<ul>";
echo "<li><a href='" . $websiteUrl . "login.php' target='_blank'>صفحة تسجيل الدخول</a></li>";
echo "<li><a href='" . $websiteUrl . "dashboard.php' target='_blank'>لوحة التحكم</a></li>";
echo "<li><a href='" . $websiteUrl . "shipments.php' target='_blank'>استعراض الشحنات</a></li>";
echo "</ul>";
echo "</div>";

// ملخص النتائج
echo "<div class='test'>";
echo "<h2>ملخص النتائج</h2>";
echo "<p>إذا كانت جميع الاختبارات تظهر ✅، فإن النظام جاهز للاستخدام!</p>";
echo "<p>يمكنك الآن:</p>";
echo "<ul>";
echo "<li>فتح التطبيق على الهاتف</li>";
echo "<li>إضافة شركات جديدة</li>";
echo "<li>مسح باركود الشحنات</li>";
echo "<li>عرض الإحصائيات في الموقع</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<p><small>تم إنشاء هذا الملف لاختبار تكامل النظام - يمكن حذفه بعد التأكد من عمل النظام</small></p>";
?>
