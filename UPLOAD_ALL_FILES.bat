@echo off
chcp 65001 >nul
echo ========================================
echo    📁 رفع جميع الملفات إلى الموقع
echo ========================================
echo.

echo 📁 الملفات جاهزة في مجلد: all_files_to_upload
echo.

echo 📋 الملفات الأساسية (مطلوبة):
echo ✅ config.php - إعدادات قاعدة البيانات والمصادقة
echo ✅ getStats.php - إحصائيات الشحنات
echo ✅ getCompanies.php - قائمة الشركات
echo ✅ getShipments.php - قائمة الشحنات
echo ✅ addCompany.php - إضافة شركة
echo ✅ addShipment.php - إضافة شحنة
echo ✅ toggleCompany.php - تفعيل/إلغاء تفعيل شركة
echo.

echo 📋 الملفات الإضافية (اختيارية):
echo ✅ getStatsFixed.php - نسخة محسنة من getStats
echo ✅ getStatsSimple.php - نسخة مبسطة من getStats
echo ✅ getCompaniesFixed.php - نسخة محسنة من getCompanies
echo ✅ getCompaniesSimple.php - نسخة مبسطة من getCompanies
echo.

echo 📋 ملفات الاختبار (للتشخيص):
echo ✅ test_database_connection.php - اختبار قاعدة البيانات
echo ✅ test_auth_simple.php - اختبار المصادقة
echo.

echo 🛠️ خطوات الرفع:
echo 1. ادخل إلى cPanel
echo 2. افتح File Manager
echo 3. انتقل إلى public_html
echo 4. ارفع جميع الملفات من مجلد all_files_to_upload
echo.

echo 🔧 بعد الرفع:
echo 1. تحقق من الصلاحيات (644 للملفات، 755 للمجلدات)
echo 2. اختبر الرابط: https://zabda-al-tajamil.com/getStats.php
echo 3. إذا ظهر خطأ 401، هذا طبيعي (المصادقة مطلوبة)
echo 4. إذا ظهر خطأ 404، الملفات لم يتم رفعها بشكل صحيح
echo.

echo 📱 اختبار التطبيق:
echo 1. أعد بناء التطبيق (Build → Rebuild Project)
echo 2. شغّل التطبيق
echo 3. اذهب إلى صفحة الإحصائيات
echo 4. استخدم زر "إعادة المحاولة"
echo.

echo 🎯 النتيجة المتوقعة:
echo ✅ لا توجد أخطاء 404
echo ✅ لا توجد أخطاء مصادقة
echo ✅ تظهر الإحصائيات بشكل صحيح
echo ✅ يعمل زر "إعادة المحاولة"
echo.

echo 📖 اقرأ ملف README_UPLOAD_ALL.md للمزيد من التفاصيل
echo.

pause
