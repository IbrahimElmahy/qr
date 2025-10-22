@echo off
chcp 65001 >nul
echo ========================================
echo    🔧 حل شامل لمشكلة المصادقة
echo ========================================
echo.

echo 🎯 المشكلة: خطأ في المصادقة (401)
echo ✅ التطبيق يرسل البيانات الصحيحة
echo ❌ المشكلة في السيرفر
echo.

echo 🛠️ خطوات الحل:
echo.

echo الخطوة 1: التحقق من ملف config.php على السيرفر
echo 1. ادخل إلى cPanel
echo 2. افتح File Manager
echo 3. انتقل إلى public_html
echo 4. افتح ملف config.php
echo 5. تحقق من هذه الأسطر:
echo    define('ADMIN_USERNAME', 'admin');
echo    define('ADMIN_PASSWORD', '1234');
echo.

echo الخطوة 2: اختبار قاعدة البيانات
echo 1. ارفع ملف test_database_connection.php إلى public_html
echo 2. اختبر الرابط: https://zabda-al-tajamil.com/test_database_connection.php
echo 3. تحقق من النتائج
echo.

echo الخطوة 3: اختبار المصادقة
echo 1. ارفع ملف test_authentication.php إلى public_html
echo 2. اختبر الرابط: https://zabda-al-tajamil.com/test_authentication.php
echo 3. تحقق من النتائج
echo.

echo الخطوة 4: إعادة رفع ملف config.php
echo 1. استخدم ملف config_updated.php
echo 2. ارفعه إلى public_html/config.php
echo 3. تأكد من استبدال الملف القديم
echo.

echo الخطوة 5: اختبار نهائي
echo 1. اختبر الرابط: https://zabda-al-tajamil.com/getStats.php
echo 2. إذا ظهر خطأ 401، هذا طبيعي
echo 3. إذا ظهر خطأ 404، الملف غير موجود
echo 4. إذا ظهر خطأ 500، هناك مشكلة في قاعدة البيانات
echo.

echo 📱 اختبار التطبيق:
echo 1. أعد بناء التطبيق
echo 2. شغّل التطبيق
echo 3. اذهب إلى صفحة الإحصائيات
echo 4. استخدم زر "إعادة المحاولة"
echo.

echo 🎯 النتيجة المتوقعة:
echo ✅ لا توجد أخطاء 404
echo ✅ لا توجد أخطاء مصادقة
echo ✅ تظهر الإحصائيات بشكل صحيح
echo ✅ يعمل زر "إعادة المحاولة"
echo ✅ البيانات تظهر من قاعدة البيانات
echo.

echo 📖 اقرأ ملف SERVER_DEBUG_GUIDE.md للمزيد من التفاصيل
echo.

pause
