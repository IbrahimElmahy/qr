@echo off
echo ========================================
echo    اختبار اتصال API
echo ========================================

echo.
echo 1. اختبار اتصال الخادم...
echo.

echo اختبار getCompanies.php:
curl -u admin:1234 "https://zabda-al-tajamil.com/shipment_tracking/api/getCompanies.php"
if %errorlevel% neq 0 (
    echo ❌ خطأ في الاتصال بـ getCompanies.php
) else (
    echo ✅ تم الاتصال بـ getCompanies.php بنجاح
)

echo.
echo اختبار getStats.php:
curl -u admin:1234 "https://zabda-al-tajamil.com/shipment_tracking/api/getStats.php"
if %errorlevel% neq 0 (
    echo ❌ خطأ في الاتصال بـ getStats.php
) else (
    echo ✅ تم الاتصال بـ getStats.php بنجاح
)

echo.
echo 2. اختبار الموقع...
echo.

echo فتح الموقع في المتصفح...
start https://zabda-al-tajamil.com/shipment_tracking/website/login.php

echo.
echo 3. معلومات الاتصال:
echo.
echo رابط الخادم: https://zabda-al-tajamil.com/shipment_tracking/api/
echo بيانات الدخول: admin / 1234
echo رابط الموقع: https://zabda-al-tajamil.com/shipment_tracking/website/
echo.

echo 4. اختبار التطبيق:
echo.
echo بعد تثبيت التطبيق على الهاتف:
echo 1. افتح التطبيق
echo 2. اذهب إلى "شركات الشحن"
echo 3. أضف شركة جديدة
echo 4. اذهب إلى "مسح الباركود"
echo 5. اختر الشركة وامسح باركود
echo 6. اذهب إلى "الإحصائيات"
echo 7. تحقق من الموقع لرؤية البيانات
echo.

echo ========================================
echo    اختبار الاتصال مكتمل!
echo ========================================
echo.
pause
