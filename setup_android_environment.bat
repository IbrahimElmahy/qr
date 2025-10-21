@echo off
echo ========================================
echo    إعداد بيئة تطوير الأندرويد
echo ========================================

echo.
echo هذا الملف سيساعدك في إعداد بيئة تطوير الأندرويد
echo.

echo 1. فحص Java الحالي...
java -version
if %errorlevel% neq 0 (
    echo ❌ Java غير مثبت
    echo.
    echo يرجى تثبيت Java Development Kit (JDK) من:
    echo https://www.oracle.com/java/technologies/downloads/
    echo أو
    echo https://adoptium.net/
    echo.
    pause
    exit /b 1
) else (
    echo ✅ Java مثبت
)

echo.
echo 2. فحص Android SDK...
if not exist "%ANDROID_HOME%" (
    echo ❌ ANDROID_HOME غير محدد
    echo.
    echo يرجى:
    echo 1. تثبيت Android Studio
    echo 2. إعداد Android SDK
    echo 3. إضافة ANDROID_HOME إلى متغيرات البيئة
    echo.
    pause
    exit /b 1
) else (
    echo ✅ Android SDK موجود في: %ANDROID_HOME%
)

echo.
echo 3. فحص Gradle...
if exist "gradlew.bat" (
    echo ✅ Gradle Wrapper موجود
) else (
    echo ❌ Gradle Wrapper غير موجود
    echo يرجى التأكد من وجود ملف gradlew.bat
)

echo.
echo 4. اختبار البناء...
echo محاولة بناء التطبيق...
call gradlew --version
if %errorlevel% neq 0 (
    echo ❌ خطأ في Gradle
    echo يرجى إعداد Gradle بشكل صحيح
    pause
    exit /b 1
) else (
    echo ✅ Gradle يعمل بشكل صحيح
)

echo.
echo ========================================
echo    البيئة جاهزة للبناء!
echo ========================================
echo.
echo يمكنك الآن تشغيل: build_android_app.bat
echo.
pause
