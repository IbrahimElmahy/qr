@echo off
echo ========================================
echo    اختبار النظام بدون بناء التطبيق
echo ========================================

echo.
echo 1. اختبار قاعدة البيانات...
echo.

echo فتح phpMyAdmin...
start https://zabda-al-tajamil.com/phpmyadmin

echo.
echo 2. اختبار الموقع...
echo.

echo فتح صفحة تسجيل الدخول...
start https://zabda-al-tajamil.com/shipment_tracking/website/login.php

echo.
echo 3. اختبار API...
echo.

echo اختبار getCompanies.php:
curl -u admin:1234 "https://zabda-al-tajamil.com/shipment_tracking/api/getCompanies.php"

echo.
echo اختبار getStats.php:
curl -u admin:1234 "https://zabda-al-tajamil.com/shipment_tracking/api/getStats.php"

echo.
echo 4. معلومات النظام:
echo.
echo قاعدة البيانات: MySQL ✅
echo الخادم: https://zabda-al-tajamil.com/ ✅
echo API: https://zabda-al-tajamil.com/shipment_tracking/api/ ✅
echo الموقع: https://zabda-al-tajamil.com/shipment_tracking/website/ ✅
echo.

echo 5. خطوات بناء التطبيق:
echo.
echo أ) حل مشكلة Java:
echo    - شغل fix_java_version.bat
echo    - أو شغل download_java17.bat
echo    - أو استخدم Android Studio

echo.
echo ب) بعد حل مشكلة Java:
echo    - شغل build_and_test_app.bat
echo    - انتظر اكتمال البناء
echo    - احصل على ملف APK

echo.
echo ج) تثبيت التطبيق:
echo    - انسخ ملف APK إلى هاتفك
echo    - فعّل "مصادر غير معروفة"
echo    - ثبت التطبيق

echo.
echo د) اختبار التطبيق:
echo    1. افتح التطبيق
echo    2. اذهب إلى "شركات الشحن"
echo    3. أضف شركة جديدة
echo    4. اذهب إلى "مسح الباركود"
echo    5. اختر الشركة وامسح باركود
echo    6. اذهب إلى "الإحصائيات"
echo    7. تحقق من الموقع لرؤية البيانات

echo.
echo ========================================
echo    النظام جاهز للاختبار!
echo ========================================
echo.
pause
