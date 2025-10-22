@echo off
chcp 65001 >nul
echo ========================================
echo    🚨 حل نهائي لمشكلة 404 Not Found
echo ========================================
echo.

echo المشكلة: الملفات لم يتم رفعها إلى المكان الصحيح
echo.

echo 🎯 الحل النهائي: رفع مباشر إلى الجذر
echo.

echo 📁 الملفات جاهزة في مجلد: direct_upload/
echo.

echo 🛠️ خطوات الرفع:
echo 1. ادخل إلى cPanel
echo 2. افتح File Manager
echo 3. انتقل إلى public_html (وليس إلى مجلد فرعي)
echo 4. ارفع جميع الملفات من direct_upload/ مباشرة إلى public_html/
echo.

echo 🔧 تم تحديث التطبيق:
echo BASE_URL = "https://zabda-al-tajamil.com/"
echo.

echo 🧪 اختبار بعد الرفع:
echo https://zabda-al-tajamil.com/getStats.php
echo.

echo ⚠️ إذا حصلت على خطأ 401، هذا طبيعي (المصادقة مطلوبة)
echo ✅ إذا حصلت على خطأ 404، الملفات لم يتم رفعها بشكل صحيح
echo.

echo 📱 بعد الرفع:
echo 1. أعد بناء التطبيق
echo 2. شغّل التطبيق
echo 3. اذهب إلى صفحة الإحصائيات
echo 4. استخدم زر "إعادة المحاولة"
echo 5. تحقق من ظهور البيانات
echo.

echo 🎯 النتيجة المتوقعة:
echo ✅ لا توجد أخطاء 404
echo ✅ تظهر الإحصائيات بشكل صحيح
echo ✅ يعمل زر "إعادة المحاولة"
echo.

echo 📖 اقرأ ملف DIRECT_UPLOAD_SOLUTION.md للمزيد من التفاصيل
echo.

pause
