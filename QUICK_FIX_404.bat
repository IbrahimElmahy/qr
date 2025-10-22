@echo off
chcp 65001 >nul
echo ========================================
echo    حل سريع لمشكلة 404 Not Found
echo ========================================
echo.

echo 🚨 المشكلة:
echo Not Found - The requested URL was not found on this server
echo https://zabda-al-tajamil.com/shipment_tracking/api/getStats.php
echo.

echo 🎯 الحلول السريعة:
echo.

echo الحل الأول: رفع مباشر إلى الجذر
echo 1. ارفع الملفات إلى public_html/ مباشرة
echo 2. غيّر BASE_URL إلى: https://zabda-al-tajamil.com/
echo 3. اختبر: https://zabda-al-tajamil.com/getStats.php
echo.

echo الحل الثاني: إنشاء مجلد api في الجذر
echo 1. أنشئ مجلد api في public_html/
echo 2. ارفع الملفات إلى public_html/api/
echo 3. غيّر BASE_URL إلى: https://zabda-al-tajamil.com/api/
echo 4. اختبر: https://zabda-al-tajamil.com/api/getStats.php
echo.

echo 📁 الملفات جاهزة في مجلد: alternative_upload/
echo.

echo 🛠️ خطوات الرفع:
echo 1. ادخل إلى cPanel
echo 2. افتح File Manager
echo 3. انتقل إلى public_html
echo 4. أنشئ مجلد api (إذا لم يكن موجوداً)
echo 5. ارفع جميع الملفات من alternative_upload/
echo.

echo 🔧 بعد الرفع:
echo 1. غيّر BASE_URL في RetrofitInstance.kt
echo 2. اختبر الرابط في المتصفح
echo 3. استخدم زر "إعادة المحاولة" في التطبيق
echo.

echo 📖 اقرأ ملف QUICK_UPLOAD_GUIDE.md للمزيد من التفاصيل
echo.

pause
