@echo off
chcp 65001 >nul
echo ========================================
echo    رفع جميع ملفات API إلى السيرفر
echo ========================================
echo.

echo 📁 الملفات جاهزة في مجلد: api_files_for_upload
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

echo 🎯 الوجهة المطلوبة:
echo https://zabda-al-tajamil.com/shipment_tracking/api/
echo.

echo 📝 خطوات الرفع:
echo 1. ادخل إلى cPanel
echo 2. افتح File Manager
echo 3. انتقل إلى public_html/shipment_tracking/
echo 4. أنشئ مجلد api إذا لم يكن موجوداً
echo 5. ارفع جميع الملفات من مجلد api_files_for_upload
echo.

echo 🧪 بعد الرفع، اختبر الرابط:
echo https://zabda-al-tajamil.com/shipment_tracking/api/getStats.php
echo.

echo ⚠️  إذا حصلت على خطأ 401، هذا طبيعي (المصادقة مطلوبة)
echo ✅ إذا حصلت على خطأ 404، الملفات لم يتم رفعها بشكل صحيح
echo.

echo 📖 اقرأ ملف README_UPLOAD.md للمزيد من التفاصيل
echo.

pause
