<?php
session_start();

// إذا كان المستخدم مسجل دخول بالفعل، توجيهه للوحة التحكم
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// توجيه المستخدم إلى صفحة تسجيل الدخول
header('Location: login.php');
exit;
?>
