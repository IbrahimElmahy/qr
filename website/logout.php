<?php
session_start();

// مسح جميع بيانات الجلسة
session_destroy();

// توجيه المستخدم لصفحة تسجيل الدخول
header('Location: login.php');
exit;
?>
