@echo off
chcp 65001 >nul
echo ========================================
echo    🚨 حل عاجل لمشكلة 404 عادت مرة أخرى
echo ========================================
echo.

echo ❌ المشكلة عادت:
echo خطأ في تحميل الإحصائيات: الملف غير موجود على السيرفر (404)
echo.

echo 🎯 الأسباب المحتملة:
echo 1. الملفات تم حذفها من السيرفر
echo 2. تغيير في مسار الملفات
echo 3. مشكلة في إعدادات السيرفر
echo 4. تغيير في BASE_URL
echo.

echo 🔧 الحل العاجل:
echo.

echo الخطوة 1: تحقق من وجود الملفات على السيرفر
echo 1. ادخل إلى cPanel
echo 2. افتح File Manager
echo 3. انتقل إلى public_html
echo 4. تحقق من وجود الملفات:
echo    - config.php
echo    - getStats.php
echo    - getCompanies.php
echo    - getShipments.php
echo    - addCompany.php
echo    - addShipment.php
echo    - toggleCompany.php
echo.

echo الخطوة 2: إذا كانت الملفات موجودة
echo 1. اختبر الرابط: https://zabda-al-tajamil.com/getStats.php
echo 2. إذا ظهر خطأ 404، الملفات غير موجودة
echo 3. إذا ظهر خطأ 401، الملفات موجودة ولكن تحتاج مصادقة
echo.

echo الخطوة 3: إذا كانت الملفات غير موجودة
echo 1. ارفع الملفات مرة أخرى من مجلد direct_upload/
echo 2. تأكد من رفعها إلى public_html مباشرة
echo 3. تحقق من الصلاحيات (644 للملفات)
echo.

echo الخطوة 4: تحقق من BASE_URL في التطبيق
echo في ملف RetrofitInstance.kt:
echo private const val BASE_URL = "https://zabda-al-tajamil.com/"
echo.

echo الخطوة 5: اختبار نهائي
echo 1. اختبر الرابط: https://zabda-al-tajamil.com/getStats.php
echo 2. إذا ظهر خطأ 401، هذا طبيعي
echo 3. إذا ظهر خطأ 404، الملفات غير موجودة
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

echo 🚨 إذا استمرت المشاكل:
echo 1. تحقق من وجود الملفات على السيرفر
echo 2. تأكد من رفع الملفات إلى المكان الصحيح
echo 3. تحقق من الصلاحيات
echo 4. راجع سجلات الأخطاء في cPanel
echo.

pause
