@echo off
echo ========================================
echo    تثبيت تطبيق الأندرويد
echo ========================================

echo.
echo هذا الملف سيساعدك في تثبيت التطبيق على هاتفك
echo.

echo 1. فحص ملف APK...
if exist "app\build\outputs\apk\debug\app-debug.apk" (
    echo ✅ تم العثور على ملف APK
    echo 📱 الملف: app\build\outputs\apk\debug\app-debug.apk
) else (
    echo ❌ لم يتم العثور على ملف APK
    echo يرجى تشغيل build_android_app.bat أولاً
    pause
    exit /b 1
)

echo.
echo 2. طرق التثبيت:
echo.
echo الطريقة الأولى - نسخ يدوي:
echo 1. انسخ ملف app-debug.apk إلى هاتفك
echo 2. افتح الملف على الهاتف
echo 3. اتبع التعليمات للتثبيت
echo.
echo الطريقة الثانية - ADB (إذا كان الهاتف متصل):
echo 1. تأكد من تفعيل "مطور الأندرويد" على الهاتف
echo 2. تأكد من تفعيل "تصحيح USB"
echo 3. وصل الهاتف بالكمبيوتر
echo 4. شغل الأمر التالي:
echo    adb install app\build\outputs\apk\debug\app-debug.apk
echo.

echo 3. إعدادات الأمان:
echo - تأكد من السماح بتثبيت التطبيقات من مصادر غير معروفة
echo - اذهب إلى: الإعدادات > الأمان > مصادر غير معروفة
echo.

echo 4. اختبار التطبيق:
echo - افتح التطبيق على الهاتف
echo - تأكد من عمل جميع الوظائف
echo - اختبر مسح الباركود
echo - اختبر إدارة الشركات
echo - اختبر الإحصائيات
echo.

echo ========================================
echo    التطبيق جاهز للاستخدام!
echo ========================================
echo.
pause
