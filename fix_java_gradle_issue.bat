@echo off
echo ========================================
echo    حل مشكلة Java و Gradle
echo ========================================

echo.
echo المشكلة: Java 25 غير متوافق مع Gradle 8.0
echo الحل: استخدام Java 17 أو تحديث Gradle
echo.

echo 1. فحص Java الحالي...
java -version
if %errorlevel% neq 0 (
    echo ❌ Java غير موجود
    goto :install_java
) else (
    echo ✅ Java موجود
    echo.
    echo 2. فحص إصدار Java...
    java -version 2>&1 | findstr "25"
    if %errorlevel% equ 0 (
        echo ❌ Java 25 موجود - غير متوافق
        goto :fix_java
    ) else (
        echo ✅ Java متوافق
        goto :test_gradle
    )
)

:install_java
echo.
echo 3. تثبيت Java 17...
echo.
echo أ) تحميل Java 17 من: https://adoptium.net/temurin/releases/?version=17
echo ب) تثبيت Java 17
echo ج) إضافة JAVA_HOME إلى متغيرات البيئة
echo د) إضافة %%JAVA_HOME%%\bin إلى PATH
echo.
echo أو استخدم Android Studio's built-in Java:
echo 1. File → Project Structure → SDK Location
echo 2. اختر "Use embedded JDK"
echo 3. اضغط OK
echo.
goto :end

:fix_java
echo.
echo 4. حلول Java 25...
echo.
echo الحل الأول - استخدام Android Studio's built-in Java:
echo 1. افتح Android Studio
echo 2. File → Project Structure → SDK Location
echo 3. اختر "Use embedded JDK"
echo 4. اضغط OK
echo 5. أعد تشغيل Android Studio
echo.
echo الحل الثاني - تثبيت Java 17:
echo 1. تحميل Java 17 من: https://adoptium.net/temurin/releases/?version=17
echo 2. تثبيت Java 17
echo 3. إضافة JAVA_HOME إلى متغيرات البيئة
echo 4. إضافة %%JAVA_HOME%%\bin إلى PATH
echo 5. أعد تشغيل الكمبيوتر
echo.
echo الحل الثالث - تحديث Gradle:
echo 1. تم تحديث Gradle إلى 8.4
echo 2. تم تحديث Android Gradle Plugin إلى 8.2.0
echo 3. جرب البناء مرة أخرى
echo.

:test_gradle
echo.
echo 5. اختبار Gradle...
echo.
echo تنظيف Gradle...
call gradlew clean
if %errorlevel% neq 0 (
    echo ❌ خطأ في التنظيف
    echo.
    echo حلول إضافية:
    echo 1. حذف مجلد .gradle
    echo 2. حذف مجلد build
    echo 3. أعد تشغيل Android Studio
    echo 4. File → Invalidate Caches and Restart
    echo.
) else (
    echo ✅ تم التنظيف بنجاح
    echo.
    echo بناء المشروع...
    call gradlew assembleDebug
    if %errorlevel% neq 0 (
        echo ❌ خطأ في البناء
        echo.
        echo حلول إضافية:
        echo 1. File → Invalidate Caches and Restart
        echo 2. Build → Clean Project
        echo 3. Build → Rebuild Project
        echo 4. تأكد من Java 17
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

:end
echo.
echo ========================================
echo    حل مشكلة Java و Gradle مكتمل!
echo ========================================
echo.
echo الخطوات التالية:
echo 1. جرب الحلول أعلاه
echo 2. إذا استمرت المشاكل، أخبرني بالأخطاء المحددة
echo 3. سأقوم بإنشاء حل مخصص للمشكلة
echo.
pause
