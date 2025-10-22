@echo off
chcp 65001 >nul
echo ========================================
echo    📁 إعادة رفع الملفات إلى السيرفر
echo ========================================
echo.

echo 🚨 المشكلة: الملفات غير موجودة على السيرفر (404)
echo.

echo 📁 الملفات جاهزة في مجلد: direct_upload/
echo.

echo 🛠️ خطوات الرفع:
echo 1. ادخل إلى cPanel
echo 2. افتح File Manager
echo 3. انتقل إلى public_html
echo 4. ارفع جميع الملفات من direct_upload/ إلى public_html/
echo.

echo 📋 الملفات المطلوبة للرفع:
echo ✅ config.php
echo ✅ getStats.php
echo ✅ getCompanies.php
echo ✅ getShipments.php
echo ✅ addCompany.php
echo ✅ addShipment.php
echo ✅ toggleCompany.php
echo.

echo 🔧 بعد الرفع:
echo 1. تحقق من الصلاحيات (644 للملفات)
echo 2. اختبر الرابط: https://zabda-al-tajamil.com/getStats.php
echo 3. إذا ظهر خطأ 401، هذا طبيعي
echo 4. إذا ظهر خطأ 404، الملفات لم يتم رفعها بشكل صحيح
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
echo.

echo 📖 اقرأ ملف QUICK_404_FIX.md للمزيد من التفاصيل
echo.

pause
