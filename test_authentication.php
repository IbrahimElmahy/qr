<?php
// اختبار المصادقة
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', '1234');

echo "<h2>اختبار المصادقة</h2>";

function testAuth() {
    echo "🔍 فحص Authorization header...<br>";
    
    if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
        return "❌ لا يوجد Authorization header<br>";
    }
    
    echo "✅ Authorization header موجود<br>";
    
    $auth = $_SERVER['HTTP_AUTHORIZATION'];
    echo "🔍 Authorization header: " . $auth . "<br>";
    
    if (strpos($auth, 'Basic ') !== 0) {
        return "❌ تنسيق Authorization غير صحيح. يجب أن يبدأ بـ 'Basic '<br>";
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
        return "❌ اسم المستخدم غير صحيح. المستلم: '$username'، المتوقع: '" . ADMIN_USERNAME . "'<br>";
    }
    
    if ($password !== ADMIN_PASSWORD) {
        return "❌ كلمة المرور غير صحيحة. المستلمة: '$password'، المتوقعة: '" . ADMIN_PASSWORD . "'<br>";
    }
    
    return "✅ المصادقة نجحت!<br>";
}

echo testAuth();
?>
