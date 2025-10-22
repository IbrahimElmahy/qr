@echo off
echo ========================================
echo    رفع ملفات API إلى السيرفر
echo ========================================
echo.

echo الملفات المطلوب رفعها:
echo - config.php
echo - getStats.php  
echo - getCompanies.php
echo - getShipments.php
echo - addCompany.php
echo - addShipment.php
echo - toggleCompany.php
echo.

echo يجب رفع هذه الملفات إلى:
echo https://zabda-al-tajamil.com/shipment_tracking/api/
echo.

echo خطوات الرفع:
echo 1. ادخل إلى cPanel
echo 2. افتح File Manager
echo 3. انتقل إلى public_html/shipment_tracking/
echo 4. أنشئ مجلد api إذا لم يكن موجوداً
echo 5. ارفع جميع الملفات من مجلد api/ المحلي
echo.

echo بعد الرفع، اختبر الرابط:
echo https://zabda-al-tajamil.com/shipment_tracking/api/getStats.php
echo.

pause
