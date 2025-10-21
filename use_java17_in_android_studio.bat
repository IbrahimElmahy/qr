@echo off
echo ========================================
echo    استخدام Java 17 في Android Studio
echo ========================================

echo.
echo هذا الحل يستخدم Java 17 في Android Studio
echo بدلاً من Java 25 المثبت على النظام
echo.

echo 1. افتح Android Studio
echo 2. File → Project Structure → SDK Location
echo 3. في قسم "Gradle Settings":
echo    - اختر "Use embedded JDK"
echo    - أو اختر مسار Java 17 إذا كان مثبت
echo 4. اضغط OK
echo 5. أعد تشغيل Android Studio
echo.

echo 6. بعد إعادة التشغيل:
echo    - File → Invalidate Caches and Restart
echo    - انتظر حتى يتم إعادة التشغيل
echo    - Build → Clean Project
echo    - Build → Rebuild Project
echo.

echo 7. إذا نجح البناء:
echo    - Build → Build Bundle(s) / APK(s) → Build APK(s)
echo    - انتظر حتى ينتهي البناء
echo    - ابحث عن ملف APK في: app\build\outputs\apk\debug\
echo.

echo 8. تثبيت التطبيق:
echo    - انسخ ملف APK إلى هاتفك
echo    - فعّل "مصادر غير معروفة" في إعدادات الأمان
echo    - افتح ملف APK على الهاتف
echo    - اتبع التعليمات للتثبيت
echo.

echo ========================================
echo    استخدام Java 17 مكتمل!
echo ========================================
echo.
echo إذا استمرت المشاكل:
echo 1. تأكد من Java 17 مثبت
echo 2. تأكد من JAVA_HOME مضاف
echo 3. تأكد من PATH محدث
echo 4. أعد تشغيل الكمبيوتر
echo.
pause
