@echo off
echo ========================================
echo    حل مشكلة Java 25 مع Gradle
echo ========================================

echo.
echo المشكلة: Java 25 غير متوافق مع Gradle
echo الحلول المتاحة:
echo.

echo 1. الحل الأول - استخدام Android Studio's built-in Java:
echo.
echo أ) افتح Android Studio
echo ب) File → Project Structure → SDK Location
echo ج) في قسم "Gradle Settings":
echo    - اختر "Use embedded JDK"
echo    - أو اختر مسار Java 17 إذا كان مثبت
echo د) اضغط OK
echo ه) أعد تشغيل Android Studio
echo.

echo 2. الحل الثاني - تثبيت Java 17:
echo.
echo أ) حمل Java 17 من: https://adoptium.net/temurin/releases/?version=17
echo ب) ثبت Java 17
echo ج) أضف JAVA_HOME إلى متغيرات البيئة
echo د) أضف %%JAVA_HOME%%\bin إلى PATH
echo ه) أعد تشغيل الكمبيوتر
echo.

echo 3. الحل الثالث - تحديث Gradle:
echo.
echo أ) تم تحديث Gradle إلى 8.5
echo ب) تم تحديث Android Gradle Plugin إلى 8.3.0
echo ج) جرب البناء مرة أخرى
echo.

echo 4. الحل الرابع - تنظيف ملفات Gradle:
echo.
echo أ) حذف مجلد .gradle
echo ب) حذف مجلد build
echo ج) حذف مجلد app\build
echo د) أعد تشغيل Android Studio
echo ه) File → Invalidate Caches and Restart
echo.

echo 5. اختبار البناء:
echo.
echo أ) Build → Clean Project
echo ب) Build → Rebuild Project
echo ج) Build → Build APK
echo.

echo ========================================
echo    حل مشكلة Java 25 مكتمل!
echo ========================================
echo.
echo الخطوات التالية:
echo 1. جرب الحل الأول (الأسهل)
echo 2. إذا لم يعمل، جرب الحل الثاني
echo 3. إذا استمرت المشاكل، أخبرني بالأخطاء المحددة
echo.
pause
