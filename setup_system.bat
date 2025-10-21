@echo off
echo ========================================
echo    إعداد نظام تتبع الشحنات
echo ========================================
echo.

echo 1. نسخ ملفات النظام إلى XAMPP...
if not exist "C:\xampp\htdocs\shipment_tracking" mkdir "C:\xampp\htdocs\shipment_tracking"
xcopy /E /I /Y "api" "C:\xampp\htdocs\shipment_tracking\api"
xcopy /E /I /Y "website" "C:\xampp\htdocs\website"

echo.
echo 2. إنشاء قاعدة البيانات...
echo يرجى تشغيل MySQL من XAMPP أولاً
echo ثم تشغيل ملف database_setup.sql
echo.

echo 3. فتح المتصفح لاختبار النظام...
start http://localhost/website/login.php
start http://localhost/test_integration.php

echo.
echo ========================================
echo    تم إعداد النظام بنجاح!
echo ========================================
echo.
echo خطوات إكمال الإعداد:
echo 1. تأكد من تشغيل Apache و MySQL في XAMPP
echo 2. شغّل ملف database_setup.sql في MySQL
echo 3. اختبر النظام من المتصفح
echo 4. عدّل رابط السيرفر في التطبيق
echo.
pause
