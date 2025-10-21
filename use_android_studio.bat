@echo off
echo ========================================
echo    استخدام Android Studio
echo ========================================

echo.
echo إذا كان لديك Android Studio مثبت، يمكنك:
echo.

echo 1. فتح Android Studio
echo 2. فتح المشروع من المجلد الحالي
echo 3. الانتظار حتى يتم تحميل Gradle
echo 4. الضغط على Build > Build Bundle(s) / APK(s) > Build APK(s)
echo.

echo أو يمكنك استخدام الأوامر التالية في Android Studio Terminal:
echo.
echo gradlew clean
echo gradlew assembleDebug
echo.

echo ========================================
echo    ملف APK سيكون في:
echo    app\build\outputs\apk\debug\app-debug.apk
echo ========================================
echo.
pause
