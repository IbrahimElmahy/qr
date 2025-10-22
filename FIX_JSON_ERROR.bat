@echo off
chcp 65001 >nul
echo ========================================
echo    🚨 حل مشكلة JSON Parsing Error
echo ========================================
echo.

echo 🚨 المشكلة:
echo java.lang.IllegalStateException: Expected BEGIN_OBJECT but was STRING
echo.

echo 🔍 السبب:
echo التطبيق يتوقع JSON object ولكن يحصل على string
echo {"error":"Authorization header required"}
echo.

echo 🔧 الحل:
echo.

echo الخطوة 1: تحقق من ملف config.php على السيرفر
echo 1. ادخل إلى cPanel
echo 2. افتح File Manager
echo 3. انتقل إلى public_html
echo 4. افتح ملف config.php
echo 5. تحقق من بيانات المصادقة:
echo    define('ADMIN_USERNAME', 'admin');
echo    define('ADMIN_PASSWORD', '1234');
echo.

echo الخطوة 2: ارفع ملف الاختبار
echo 1. ارفع ملف test_auth_final.php إلى public_html
echo 2. اختبر الرابط: https://zabda-al-tajamil.com/test_auth_final.php
echo 3. إذا ظهر "المصادقة نجحت"، المشكلة في التطبيق
echo 4. إذا ظهر خطأ، المشكلة في بيانات المصادقة
echo.

echo الخطوة 3: إعادة بناء التطبيق
echo 1. Build → Clean Project
echo 2. Build → Rebuild Project
echo 3. File → Invalidate Caches and Restart
echo.

echo الخطوة 4: احذف التطبيق من الجهاز
echo 1. احذف التطبيق من الجهاز
echo 2. أعد تثبيت التطبيق
echo.

echo الخطوة 5: تحقق من Logcat
echo ابحث عن رسائل Debug:
echo 🔍 Sending request to: https://zabda-al-tajamil.com/getStats.php
echo 🔍 Authorization header: Basic YWRtaW46MTIzNA==
echo 🔍 Response code: 200
echo.

echo 🎯 النتيجة المتوقعة:
echo ✅ لا توجد أخطاء JSON parsing
echo ✅ تظهر الإحصائيات بشكل صحيح
echo ✅ ثيم أبيض وأزرق جميل
echo.

echo 🚨 إذا استمرت المشاكل:
echo 1. تحقق من ملف config.php على السيرفر
echo 2. ارفع ملف الاختبار واختبره
echo 3. تحقق من Logcat للأخطاء
echo 4. جرب إعادة تثبيت التطبيق
echo.

echo 📖 اقرأ ملف FIX_JSON_PARSING_ERROR.md للمزيد من التفاصيل
echo.

pause
