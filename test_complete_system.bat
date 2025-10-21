@echo off
echo ========================================
echo    اختبار النظام الكامل
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
echo 4. خطوات اختبار التطبيق:
echo.

echo أ) بناء التطبيق:
echo    - شغل build_and_test_app.bat
echo    - انتظر اكتمال البناء
echo    - احصل على ملف APK

echo.
echo ب) تثبيت التطبيق:
echo    - انسخ ملف APK إلى هاتفك
echo    - فعّل "مصادر غير معروفة"
echo    - ثبت التطبيق

echo.
echo ج) اختبار التطبيق:
echo    1. افتح التطبيق
echo    2. اذهب إلى "شركات الشحن"
echo    3. أضف شركة جديدة (مثل: "شركة النقل السريع")
echo    4. تأكد من ظهورها في الموقع
echo    5. اذهب إلى "مسح الباركود"
echo    6. اختر الشركة
echo    7. امسح باركود (أو أدخل رقم يدوياً)
echo    8. اذهب إلى "الإحصائيات"
echo    9. تحقق من ظهور البيانات

echo.
echo د) اختبار الموقع:
echo    1. اذهب إلى الموقع
echo    2. سجل دخول (admin / 1234)
echo    3. تحقق من ظهور الشركة الجديدة
echo    4. تحقق من ظهور الشحنة الجديدة
echo    5. تحقق من الإحصائيات

echo.
echo 5. معلومات النظام:
echo.
echo قاعدة البيانات: MySQL
echo الخادم: https://zabda-al-tajamil.com/
echo API: https://zabda-al-tajamil.com/shipment_tracking/api/
echo الموقع: https://zabda-al-tajamil.com/shipment_tracking/website/
echo التطبيق: Android APK
echo.

echo ========================================
echo    النظام جاهز للاختبار!
echo ========================================
echo.
pause
