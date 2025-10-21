@echo off
echo ========================================
echo    دليل تثبيت Java JDK
echo ========================================

echo.
echo Java غير مثبت على النظام
echo يرجى اتباع الخطوات التالية:
echo.

echo 1. تحميل Java JDK:
echo.
echo الطريقة الأولى - Oracle JDK:
echo https://www.oracle.com/java/technologies/downloads/
echo.
echo الطريقة الثانية - OpenJDK (مجاني):
echo https://adoptium.net/
echo.

echo 2. تثبيت Java:
echo - شغل الملف المحمل
echo - اتبع التعليمات
echo - اختر "Add to PATH" أثناء التثبيت
echo.

echo 3. إعادة تشغيل الكمبيوتر:
echo - أعد تشغيل الكمبيوتر بعد التثبيت
echo - هذا مهم لتحديث متغيرات البيئة
echo.

echo 4. اختبار التثبيت:
echo - افتح Command Prompt جديد
echo - اكتب: java -version
echo - يجب أن تظهر معلومات Java
echo.

echo 5. إعداد JAVA_HOME:
echo - اذهب إلى متغيرات البيئة
echo - أضف متغير جديد: JAVA_HOME
echo - القيمة: مسار تثبيت Java
echo - مثال: C:\Program Files\Java\jdk-17
echo.

echo ========================================
echo    بعد التثبيت، شغل setup_android_environment.bat
echo ========================================
echo.
pause
