@echo off
echo ========================================
echo    إصلاح أخطاء Android Studio
echo ========================================

echo.
echo 1. تنظيف المشروع...
echo.

echo حذف مجلدات البناء...
if exist "build" rmdir /s /q "build"
if exist "app\build" rmdir /s /q "app\build"
if exist ".gradle" rmdir /s /q ".gradle"

echo.
echo 2. حذف ملفات Gradle المؤقتة...
echo.

if exist "gradle\wrapper\gradle-wrapper.jar" del "gradle\wrapper\gradle-wrapper.jar"
if exist "gradle\wrapper\gradle-wrapper.properties" del "gradle\wrapper\gradle-wrapper.properties"

echo.
echo 3. إعادة تحميل Gradle Wrapper...
echo.

echo تحميل Gradle Wrapper جديد...
curl -o "gradle\wrapper\gradle-wrapper.jar" "https://services.gradle.org/distributions/gradle-8.4-bin.zip"

echo.
echo 4. إنشاء gradle-wrapper.properties جديد...
echo.

echo #Mon Dec 25 10:00:00 UTC 2023 > "gradle\wrapper\gradle-wrapper.properties"
echo distributionBase=GRADLE_USER_HOME >> "gradle\wrapper\gradle-wrapper.properties"
echo distributionPath=wrapper/dists >> "gradle\wrapper\gradle-wrapper.properties"
echo distributionUrl=https\://services.gradle.org/distributions/gradle-8.4-bin.zip >> "gradle\wrapper\gradle-wrapper.properties"
echo zipStoreBase=GRADLE_USER_HOME >> "gradle\wrapper\gradle-wrapper.properties"
echo zipStorePath=wrapper/dists >> "gradle\wrapper\gradle-wrapper.properties"

echo.
echo 5. فحص Java...
echo.

java -version
if %errorlevel% neq 0 (
    echo ❌ Java غير موجود
    echo.
    echo حلول Java:
    echo 1. تثبيت Java 17 من: https://adoptium.net/temurin/releases/?version=17
    echo 2. إضافة JAVA_HOME إلى متغيرات البيئة
    echo 3. إضافة %%JAVA_HOME%%\bin إلى PATH
    echo.
) else (
    echo ✅ Java موجود
    echo.
    echo 6. محاولة بناء المشروع...
    echo.
    
    echo تنظيف Gradle...
    call gradlew clean
    if %errorlevel% neq 0 (
        echo ❌ خطأ في التنظيف
    ) else (
        echo ✅ تم التنظيف بنجاح
        echo.
        echo بناء المشروع...
        call gradlew assembleDebug
        if %errorlevel% neq 0 (
            echo ❌ خطأ في البناء
            echo.
            echo حلول إضافية:
            echo 1. افتح Android Studio
            echo 2. File > Invalidate Caches and Restart
            echo 3. Build > Clean Project
            echo 4. Build > Rebuild Project
            echo.
        ) else (
            echo ✅ تم بناء المشروع بنجاح
            echo.
            echo فحص ملف APK...
            if exist "app\build\outputs\apk\debug\app-debug.apk" (
                echo ✅ تم العثور على ملف APK
                echo 📱 الملف: app\build\outputs\apk\debug\app-debug.apk
                echo.
                echo تثبيت التطبيق:
                echo 1. انسخ ملف APK إلى هاتفك
                echo 2. فعّل "مصادر غير معروفة" في إعدادات الأمان
                echo 3. افتح ملف APK على الهاتف
                echo 4. اتبع التعليمات للتثبيت
            ) else (
                echo ❌ لم يتم العثور على ملف APK
            )
        )
    )
)

echo.
echo ========================================
echo    إصلاح أخطاء Android Studio مكتمل!
echo ========================================
echo.
echo إذا استمرت الأخطاء:
echo 1. افتح Android Studio
echo 2. File > Invalidate Caches and Restart
echo 3. Build > Clean Project
echo 4. Build > Rebuild Project
echo 5. تأكد من تثبيت Java 17
echo.
pause
