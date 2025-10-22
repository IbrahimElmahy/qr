@echo off
chcp 65001 >nul
echo ========================================
echo    🔧 حل مشكلة إعدادات السيرفر
echo ========================================
echo.

echo 🚨 المشكلة المكتشفة:
echo ❌ لا يوجد Authorization header
echo.

echo 🔍 السبب:
echo المتصفح لا يرسل Authorization header!
echo هذا يعني أن المشكلة في إعدادات السيرفر
echo.

echo 🔧 الحل:
echo.

echo الخطوة 1: تحقق من ملف config.php على السيرفر
echo 1. ادخل إلى cPanel
echo 2. افتح File Manager
echo 3. انتقل إلى public_html
echo 4. افتح ملف config.php
echo 5. تحقق من بيانات المصادقة:
echo    define('ADMIN_USERNAME', 'admin');
echo    define('ADMIN_PASSWORD', '1234');
echo.

echo الخطوة 2: إذا كانت البيانات مختلفة
echo 1. غيّر البيانات في config.php على السيرفر
echo 2. تأكد من أن البيانات صحيحة
echo.

echo الخطوة 3: إذا كانت البيانات صحيحة
echo المشكلة في إعدادات Apache أو PHP:
echo 1. تحقق من إعدادات Apache
echo 2. تحقق من إعدادات PHP
echo 3. تحقق من وجود mod_rewrite
echo 4. تحقق من إعدادات .htaccess
echo.

echo الخطوة 4: حل بديل - تعطيل المصادقة مؤقتاً
echo 1. غيّر ملف getStats.php على السيرفر
echo 2. علّق على السطر: authenticate();
echo 3. اختبر الرابط مرة أخرى
echo.

echo الخطوة 5: اختبار نهائي
echo 1. اختبر الرابط: https://zabda-al-tajamil.com/getStats.php
echo 2. إذا ظهر بيانات JSON، المشكلة محلولة
echo 3. إذا ظهر خطأ 401، المصادقة تعمل
echo.

echo 🎯 النتيجة المتوقعة:
echo ✅ Authorization header موجود
echo ✅ المصادقة تعمل بشكل صحيح
echo ✅ API يعمل بشكل مثالي
echo ✅ لا توجد أخطاء JSON parsing
echo ✅ تظهر الإحصائيات بشكل صحيح
echo.

echo 🚨 إذا استمرت المشاكل:
echo 1. تحقق من إعدادات Apache
echo 2. تحقق من إعدادات PHP
echo 3. تحقق من وجود mod_rewrite
echo 4. جرب تعطيل المصادقة مؤقتاً
echo.

echo 📖 اقرأ ملف FIX_SERVER_CONFIG.md للمزيد من التفاصيل
echo.

pause
