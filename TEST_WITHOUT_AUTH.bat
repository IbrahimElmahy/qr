@echo off
chcp 65001 >nul
echo ========================================
echo    🧪 اختبار بدون مصادقة
echo ========================================
echo.

echo 🚨 المشكلة:
echo السيرفر لا يدعم Authorization header
echo.

echo 🔧 الحل:
echo تعطيل المصادقة مؤقتاً للاختبار
echo.

echo 🛠️ خطوات الاختبار:
echo.

echo الخطوة 1: ارفع ملف getStats_no_auth.php
echo 1. ارفع ملف getStats_no_auth.php إلى public_html
echo 2. اختبر الرابط: https://zabda-al-tajamil.com/getStats_no_auth.php
echo 3. إذا ظهر بيانات JSON، المشكلة في المصادقة
echo.

echo الخطوة 2: إذا نجح الاختبار
echo 1. غيّر اسم getStats.php إلى getStats_old.php
echo 2. غيّر اسم getStats_no_auth.php إلى getStats.php
echo 3. اختبر الرابط: https://zabda-al-tajamil.com/getStats.php
echo.

echo الخطوة 3: اختبار التطبيق
echo 1. أعد بناء التطبيق
echo 2. شغّل التطبيق
echo 3. اذهب إلى صفحة الإحصائيات
echo 4. استخدم زر "إعادة المحاولة"
echo.

echo 🎯 النتيجة المتوقعة:
echo ✅ لا توجد أخطاء 404
echo ✅ لا توجد أخطاء مصادقة
echo ✅ تظهر الإحصائيات بشكل صحيح
echo ✅ ثيم أبيض وأزرق جميل
echo.

echo ⚠️ ملاحظة:
echo هذا حل مؤقت للاختبار
echo يمكن إعادة تفعيل المصادقة لاحقاً
echo.

echo 📖 الملفات المتاحة:
echo - getStats_no_auth.php (بدون مصادقة)
echo - config_final_correct.php (config محدث)
echo.

pause
