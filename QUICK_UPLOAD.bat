@echo off
chcp 65001 >nul
echo ========================================
echo    🚀 رفع نظام تتبع الشحنات 🚀
echo ========================================
echo.

echo 📋 الخطوات المطلوبة:
echo.
echo 1️⃣  اذهب إلى: https://cpanel.zabda-al-tajamil.com
echo 2️⃣  سجل دخول بحسابك
echo 3️⃣  افتح File Manager
echo 4️⃣  اذهب إلى مجلد public_html
echo 5️⃣  ارفع ملف shipment_system.zip
echo 6️⃣  استخرج الملفات
echo 7️⃣  أنشئ قاعدة بيانات shipment_tracking
echo 8️⃣  استورد ملف database_setup.sql
echo 9️⃣  اختبر الموقع
echo.

echo 🌐 فتح الروابط المطلوبة...
start https://cpanel.zabda-al-tajamil.com
timeout /t 3 /nobreak >nul
start https://zabda-al-tajamil.com/website/login.php

echo.
echo ✅ تم فتح الروابط المطلوبة!
echo.
echo 📁 ملف shipment_system.zip جاهز للرفع
echo 📖 راجع ملف UPLOAD_INSTRUCTIONS.md للتفاصيل
echo.
echo 🔑 بيانات الدخول:
echo    اسم المستخدم: admin
echo    كلمة المرور: 1234
echo.
echo ========================================
pause
