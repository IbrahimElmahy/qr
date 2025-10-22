@echo off
chcp 65001 >nul
echo ========================================
echo    🔧 حل خطأ المصادقة (401)
echo ========================================
echo.

echo 🚨 المشكلة:
echo خطأ في المصادقة (401): اسم المستخدم أو كلمة المرور غير صحيحة
echo.

echo 🎯 الحلول:
echo.

echo الحل الأول: تحقق من ملف config.php على السيرفر
echo 1. ادخل إلى cPanel
echo 2. افتح File Manager
echo 3. انتقل إلى public_html
echo 4. افتح ملف config.php
echo 5. تحقق من بيانات المصادقة
echo.

echo الحل الثاني: تأكد من البيانات في التطبيق
echo في ملف RetrofitInstance.kt:
echo USERNAME = "admin"
echo PASSWORD = "1234"
echo.

echo الحل الثالث: اختبار المصادقة
echo 1. اختبر الرابط: https://zabda-al-tajamil.com/getStats.php
echo 2. إذا ظهر خطأ 401، هذا طبيعي
echo 3. إذا ظهر خطأ 404، الملف غير موجود
echo.

echo 🛠️ خطوات الحل:
echo 1. تحقق من ملف config.php على السيرفر
echo 2. تأكد من أن البيانات صحيحة
echo 3. أعد بناء التطبيق
echo 4. شغّل التطبيق
echo 5. اذهب إلى صفحة الإحصائيات
echo 6. استخدم زر "إعادة المحاولة"
echo.

echo 🎯 النتيجة المتوقعة:
echo ✅ لا توجد أخطاء 404
echo ✅ لا توجد أخطاء مصادقة
echo ✅ تظهر الإحصائيات بشكل صحيح
echo ✅ يعمل زر "إعادة المحاولة"
echo ✅ البيانات تظهر من قاعدة البيانات
echo.

echo 📖 اقرأ ملف FIX_AUTHENTICATION_ERROR.md للمزيد من التفاصيل
echo.

pause
