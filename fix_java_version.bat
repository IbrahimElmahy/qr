@echo off
echo ========================================
echo    حل مشكلة إصدار Java
echo ========================================

echo.
echo المشكلة: Java 25 جديد جداً وقد يسبب مشاكل
echo الحلول المتاحة:
echo.

echo 1. تثبيت Java 17 (الأفضل):
echo.
echo تحميل Java 17 من:
echo https://adoptium.net/temurin/releases/?version=17
echo.
echo أو استخدم الرابط المباشر:
echo https://github.com/adoptium/temurin17-binaries/releases/download/jdk-17.0.9%2B9/OpenJDK17U-jdk_x64_windows_hotspot_17.0.9_9.msi
echo.

echo 2. إعداد متغيرات البيئة:
echo.
echo بعد تثبيت Java 17:
echo 1. اذهب إلى متغيرات البيئة
echo 2. أضف JAVA_HOME = C:\Program Files\Java\jdk-17
echo 3. أضف إلى PATH: %JAVA_HOME%\bin
echo 4. أعد تشغيل الكمبيوتر
echo.

echo 3. حل بديل - استخدام Android Studio:
echo.
echo إذا كان لديك Android Studio:
echo 1. افتح Android Studio
echo 2. افتح المشروع من المجلد الحالي
echo 3. انتظر تحميل Gradle
echo 4. اضغط Build > Build Bundle(s) / APK(s) > Build APK(s)
echo.

echo 4. حل سريع - استخدام Java 11:
echo.
echo تحميل Java 11 من:
echo https://adoptium.net/temurin/releases/?version=11
echo.

echo ========================================
echo    اختر الحل المناسب لك
echo ========================================
echo.
pause
