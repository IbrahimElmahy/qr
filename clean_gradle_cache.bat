@echo off
echo ========================================
echo    تنظيف ملفات Gradle المؤقتة
echo ========================================

echo.
echo هذا الحل يحذف ملفات Gradle المؤقتة
echo التي قد تسبب مشاكل في البناء
echo.

echo 1. حذف مجلدات البناء...
echo.

if exist "build" (
    echo حذف مجلد build...
    rmdir /s /q "build"
    echo ✅ تم حذف مجلد build
) else (
    echo ℹ️ مجلد build غير موجود
)

if exist "app\build" (
    echo حذف مجلد app\build...
    rmdir /s /q "app\build"
    echo ✅ تم حذف مجلد app\build
) else (
    echo ℹ️ مجلد app\build غير موجود
)

echo.
echo 2. حذف ملفات Gradle المؤقتة...
echo.

if exist ".gradle" (
    echo حذف مجلد .gradle...
    rmdir /s /q ".gradle"
    echo ✅ تم حذف مجلد .gradle
) else (
    echo ℹ️ مجلد .gradle غير موجود
)

echo.
echo 3. حذف ملفات Gradle Wrapper المؤقتة...
echo.

if exist "gradle\wrapper\gradle-wrapper.jar" (
    echo حذف gradle-wrapper.jar...
    del "gradle\wrapper\gradle-wrapper.jar"
    echo ✅ تم حذف gradle-wrapper.jar
) else (
    echo ℹ️ gradle-wrapper.jar غير موجود
)

echo.
echo 4. إعادة تحميل Gradle Wrapper...
echo.

echo تحميل Gradle Wrapper جديد...
curl -o "gradle\wrapper\gradle-wrapper.jar" "https://services.gradle.org/distributions/gradle-8.4-bin.zip"

if %errorlevel% neq 0 (
    echo ❌ خطأ في تحميل Gradle Wrapper
    echo.
    echo حل بديل:
    echo 1. افتح Android Studio
    echo 2. File → Invalidate Caches and Restart
    echo 3. انتظر حتى يتم إعادة التشغيل
    echo 4. Build → Clean Project
    echo 5. Build → Rebuild Project
) else (
    echo ✅ تم تحميل Gradle Wrapper بنجاح
    echo.
    echo 5. اختبار البناء...
    echo.
    echo تنظيف Gradle...
    call gradlew clean
    if %errorlevel% neq 0 (
        echo ❌ خطأ في التنظيف
        echo.
        echo حلول إضافية:
        echo 1. تأكد من Java 17
        echo 2. استخدم Android Studio's built-in Java
        echo 3. File → Invalidate Caches and Restart
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
)

echo.
echo ========================================
echo    تنظيف ملفات Gradle مكتمل!
echo ========================================
echo.
echo إذا استمرت المشاكل:
echo 1. جرب الحلول الأخرى
echo 2. أخبرني بالأخطاء المحددة
echo 3. سأقوم بإنشاء حل مخصص
echo.
pause
