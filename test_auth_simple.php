<?php
// اختبار المصادقة - نسخة مبسطة
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', '1234');

echo "<h2>اختبار المصادقة</h2>";

if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    echo "❌ لا يوجد Authorization header<br>";
    echo "🔍 هذا يعني أن التطبيق لا يرسل بيانات المصادقة<br>";
    exit;
}

echo "✅ Authorization header موجود<br>";

$auth = $_SERVER['HTTP_AUTHORIZATION'];
echo "🔍 Authorization header: " . $auth . "<br>";

if (strpos($auth, 'Basic ') !== 0) {
    echo "❌ تنسيق Authorization غير صحيح<br>";
    exit;
}

echo "✅ تنسيق Authorization صحيح<br>";

$credentials = base64_decode(substr($auth, 6));
echo "🔍 بيانات المصادقة المفكوكة: " . $credentials . "<br>";

list($username, $password) = explode(':', $credentials, 2);
echo "🔍 اسم المستخدم المستلم: " . $username . "<br>";
echo "🔍 كلمة المرور المستلمة: " . $password . "<br>";
echo "🔍 اسم المستخدم المتوقع: " . ADMIN_USERNAME . "<br>";
echo "🔍 كلمة المرور المتوقعة: " . ADMIN_PASSWORD . "<br>";

if ($username !== ADMIN_USERNAME) {
    echo "❌ اسم المستخدم غير صحيح<br>";
    echo "المستلم: '$username'<br>";
    echo "المتوقع: '" . ADMIN_USERNAME . "'<br>";
    exit;
}

if ($password !== ADMIN_PASSWORD) {
    echo "❌ كلمة المرور غير صحيحة<br>";
    echo "المستلمة: '$password'<br>";
    echo "المتوقعة: '" . ADMIN_PASSWORD . "'<br>";
    exit;
}

echo "✅ المصادقة نجحت!<br>";
echo "✅ البيانات صحيحة<br>";
?>
