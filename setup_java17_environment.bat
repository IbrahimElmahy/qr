@echo off
echo ========================================
echo    إعداد Java 17 في متغيرات البيئة
echo ========================================

echo.
echo 1. فحص Java الحالي...
java -version
if %errorlevel% neq 0 (
    echo ❌ Java غير موجود في PATH
    echo.
    echo 2. إعداد متغيرات البيئة:
    echo.
    echo أ) اذهب إلى متغيرات البيئة:
    echo    - اضغط Windows + R
    echo    - اكتب: sysdm.cpl
    echo    - اضغط Enter
    echo    - اضغط "Environment Variables"
    echo.
    echo ب) أضف متغير جديد:
    echo    - اضغط "New" في System Variables
    echo    - Variable name: JAVA_HOME
    echo    - Variable value: C:\Program Files\Java\jdk-17
    echo    - اضغط OK
    echo.
    echo ج) أضف إلى PATH:
    echo    - ابحث عن "Path" في System Variables
    echo    - اضغط "Edit"
    echo    - اضغط "New"
    echo    - اكتب: %%JAVA_HOME%%\bin
    echo    - اضغط OK
    echo.
    echo د) أعد تشغيل الكمبيوتر
    echo.
    echo 3. بعد إعادة التشغيل:
    echo    - شغل build_and_test_app.bat
    echo    - أو شغل هذا الملف مرة أخرى
    echo.
) else (
    echo ✅ Java موجود في PATH
    echo.
    echo 4. محاولة بناء التطبيق...
    call gradlew clean
    if %errorlevel% neq 0 (
        echo ❌ خطأ في التنظيف
    ) else (
        echo ✅ تم التنظيف بنجاح
        echo.
        echo 5. بناء التطبيق...
        call gradlew assembleDebug
        if %errorlevel% neq 0 (
            echo ❌ خطأ في البناء
        ) else (
            echo ✅ تم بناء التطبيق بنجاح
            echo.
            echo 6. فحص ملف APK...
            if exist "app\build\outputs\apk\debug\app-debug.apk" (
                echo ✅ تم العثور على ملف APK
                echo 📱 الملف: app\build\outputs\apk\debug\app-debug.apk
            ) else (
                echo ❌ لم يتم العثور على ملف APK
            )
        )
    )
)

echo.
echo ========================================
echo    إعداد Java 17 مكتمل!
echo ========================================
echo.
pause
