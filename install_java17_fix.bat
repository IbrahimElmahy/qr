@echo off
echo ========================================
echo    تثبيت Java 17 لحل مشكلة Gradle
echo ========================================

echo.
echo المشكلة: Java 25 غير متوافق مع Gradle
echo الحل: تثبيت Java 17
echo.

echo 1. تحميل Java 17...
echo.
echo الرابط: https://adoptium.net/temurin/releases/?version=17
echo.
echo 2. تثبيت Java 17...
echo.
echo أ) حمل ملف التثبيت
echo ب) شغل ملف التثبيت
echo ج) اتبع التعليمات
echo د) تأكد من اختيار "Add to PATH"
echo.

echo 3. إعداد متغيرات البيئة...
echo.
echo أ) اضغط Windows + R
echo ب) اكتب: sysdm.cpl
echo ج) اضغط Enter
echo د) اضغط "Environment Variables"
echo ه) في System Variables:
echo    - اضغط "New"
echo    - Variable name: JAVA_HOME
echo    - Variable value: C:\Program Files\Java\jdk-17
echo    - اضغط OK
echo و) ابحث عن "Path" في System Variables:
echo    - اضغط "Edit"
echo    - اضغط "New"
echo    - اكتب: %%JAVA_HOME%%\bin
echo    - اضغط OK
echo.

echo 4. إعادة تشغيل الكمبيوتر...
echo.
echo أ) أعد تشغيل الكمبيوتر
echo ب) افتح Android Studio
echo ج) File → Project Structure → SDK Location
echo د) اختر Java 17
echo ه) اضغط OK
echo.

echo 5. اختبار البناء...
echo.
echo أ) File → Invalidate Caches and Restart
echo ب) انتظر حتى يتم إعادة التشغيل
echo ج) Build → Clean Project
echo د) Build → Rebuild Project
echo ه) Build → Build APK
echo.

echo ========================================
echo    تثبيت Java 17 مكتمل!
echo ========================================
echo.
echo إذا استمرت المشاكل:
echo 1. تأكد من Java 17 مثبت
echo 2. تأكد من JAVA_HOME مضاف
echo 3. تأكد من PATH محدث
echo 4. أعد تشغيل الكمبيوتر
echo.
pause
