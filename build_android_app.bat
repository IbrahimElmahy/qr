@echo off
echo ========================================
echo    بناء تطبيق الأندرويد
echo ========================================

echo.
echo 1. فحص Java...
java -version
if %errorlevel% neq 0 (
    echo خطأ: Java غير مثبت أو غير موجود في PATH
    echo يرجى تثبيت Java Development Kit (JDK)
    pause
    exit /b 1
)

echo.
echo 2. فحص Android SDK...
if not exist "%ANDROID_HOME%" (
    echo تحذير: ANDROID_HOME غير محدد
    echo يرجى إعداد Android SDK
)

echo.
echo 3. بناء التطبيق...
call gradlew clean
if %errorlevel% neq 0 (
    echo خطأ في التنظيف
    pause
    exit /b 1
)

call gradlew assembleDebug
if %errorlevel% neq 0 (
    echo خطأ في البناء
    pause
    exit /b 1
)

echo.
echo 4. فحص ملف APK...
if exist "app\build\outputs\apk\debug\app-debug.apk" (
    echo ✅ تم بناء التطبيق بنجاح!
    echo 📱 ملف APK: app\build\outputs\apk\debug\app-debug.apk
    echo.
    echo يمكنك الآن:
    echo - نسخ ملف APK إلى هاتفك
    echo - تثبيت التطبيق على الهاتف
    echo - اختبار التطبيق
) else (
    echo ❌ لم يتم العثور على ملف APK
)

echo.
pause
