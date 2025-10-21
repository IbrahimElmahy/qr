@echo off
echo ========================================
echo    استخدام Android Studio للبناء
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

echo خطوات تثبيت التطبيق:
echo 1. انسخ ملف APK إلى هاتفك
echo 2. فعّل "مصادر غير معروفة" في إعدادات الأمان
echo 3. افتح ملف APK على الهاتف
echo 4. اتبع التعليمات للتثبيت
echo.

echo اختبار التطبيق:
echo 1. افتح التطبيق
echo 2. اذهب إلى "شركات الشحن"
echo 3. أضف شركة جديدة
echo 4. اذهب إلى "مسح الباركود"
echo 5. اختر الشركة وامسح باركود
echo 6. اذهب إلى "الإحصائيات" لرؤية النتائج
echo.

echo ========================================
echo    التطبيق جاهز للاستخدام!
echo ========================================
echo.
pause
