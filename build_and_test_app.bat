@echo off
echo ========================================
echo    بناء واختبار تطبيق الأندرويد
echo ========================================

echo.
echo 1. فحص Java...
java -version
if %errorlevel% neq 0 (
    echo ❌ Java غير مثبت
    echo يرجى تثبيت Java JDK أولاً
    echo.
    echo تحميل Java من:
    echo https://adoptium.net/
    echo.
    pause
    exit /b 1
) else (
    echo ✅ Java مثبت
)

echo.
echo 2. فحص Gradle...
if exist "gradlew.bat" (
    echo ✅ Gradle Wrapper موجود
) else (
    echo ❌ Gradle Wrapper غير موجود
    echo يرجى التأكد من وجود ملف gradlew.bat
    pause
    exit /b 1
)

echo.
echo 3. تنظيف المشروع...
call gradlew clean
if %errorlevel% neq 0 (
    echo ❌ خطأ في التنظيف
    pause
    exit /b 1
) else (
    echo ✅ تم التنظيف بنجاح
)

echo.
echo 4. بناء التطبيق...
call gradlew assembleDebug
if %errorlevel% neq 0 (
    echo ❌ خطأ في البناء
    echo.
    echo الحلول المحتملة:
    echo 1. تأكد من تثبيت Android SDK
    echo 2. تأكد من إعداد ANDROID_HOME
    echo 3. تأكد من وجود ملفات المشروع
    echo.
    pause
    exit /b 1
) else (
    echo ✅ تم بناء التطبيق بنجاح
)

echo.
echo 5. فحص ملف APK...
if exist "app\build\outputs\apk\debug\app-debug.apk" (
    echo ✅ تم العثور على ملف APK
    echo 📱 الملف: app\build\outputs\apk\debug\app-debug.apk
    echo.
    echo 6. معلومات التطبيق:
    echo - اسم التطبيق: Shipment Tracker
    echo - رابط الخادم: https://zabda-al-tajamil.com/shipment_tracking/api/
    echo - بيانات الدخول: admin / 1234
    echo.
    echo 7. خطوات التثبيت:
    echo - انسخ ملف APK إلى هاتفك
    echo - فعّل "مصادر غير معروفة" في إعدادات الأمان
    echo - افتح ملف APK على الهاتف
    echo - اتبع التعليمات للتثبيت
    echo.
    echo 8. اختبار التطبيق:
    echo - افتح التطبيق
    echo - اذهب إلى "شركات الشحن"
    echo - أضف شركة جديدة
    echo - اذهب إلى "مسح الباركود"
    echo - اختر الشركة وامسح باركود
    echo - اذهب إلى "الإحصائيات" لرؤية النتائج
    echo.
) else (
    echo ❌ لم يتم العثور على ملف APK
    echo يرجى التحقق من عملية البناء
)

echo.
echo ========================================
echo    التطبيق جاهز للاستخدام!
echo ========================================
echo.
pause
