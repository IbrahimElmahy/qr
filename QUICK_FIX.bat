@echo off
chcp 65001 >nul
echo ========================================
echo    🔧 حل مشكلة قاعدة البيانات 🔧
echo ========================================
echo.

echo ❌ المشكلة:
echo Error #1044: Access denied for user 'ztjmal'@'localhost'
echo.

echo ✅ الحل:
echo.
echo 1️⃣  اذهب إلى cPanel
echo 2️⃣  أنشئ قاعدة بيانات جديدة: shipment_tracking
echo 3️⃣  أنشئ مستخدم جديد للقاعدة
echo 4️⃣  اربط المستخدم بقاعدة البيانات
echo 5️⃣  استورد ملف tables_only.sql
echo 6️⃣  حدث ملف config.php
echo.

echo 🌐 فتح الروابط المطلوبة...
start https://cpanel.zabda-al-tajamil.com
timeout /t 3 /nobreak >nul
start https://zabda-al-tajamil.com/website/login.php

echo.
echo ✅ تم فتح الروابط المطلوبة!
echo.
echo 📁 ملف shipment_system_fixed.zip جاهز للرفع
echo 📖 راجع ملف DATABASE_FIX.md للتفاصيل
echo.
echo 🔑 بيانات الدخول:
echo    اسم المستخدم: admin
echo    كلمة المرور: 1234
echo.
echo ========================================
pause
