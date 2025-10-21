@echo off
echo ========================================
echo    رفع المشروع على GitHub
echo ========================================

echo.
echo 1. إعداد Git...
echo.

echo فحص Git...
git --version
if %errorlevel% neq 0 (
    echo ❌ Git غير مثبت
    echo.
    echo تثبيت Git:
    echo 1. حمل Git من: https://git-scm.com/download/win
    echo 2. ثبت Git
    echo 3. أعد تشغيل الكمبيوتر
    echo 4. شغل هذا الملف مرة أخرى
    echo.
    goto :end
) else (
    echo ✅ Git موجود
)

echo.
echo 2. إعداد Git...
echo.

echo إعداد اسم المستخدم...
git config --global user.name "ahmedhalim1001-coder"
git config --global user.email "ahmedhalim1001@example.com"

echo.
echo 3. تهيئة المشروع...
echo.

echo تهيئة Git repository...
git init

echo.
echo 4. إضافة الملفات...
echo.

echo إضافة جميع الملفات...
git add .

echo.
echo 5. إنشاء commit أولي...
echo.

echo إنشاء commit...
git commit -m "Initial commit: Shipment Tracking System"

echo.
echo 6. إضافة remote repository...
echo.

echo إضافة GitHub repository...
git remote add origin https://github.com/ahmedhalim1001-coder/FREELANCE.git

echo.
echo 7. رفع المشروع...
echo.

echo رفع المشروع على GitHub...
git push -u origin main

if %errorlevel% neq 0 (
    echo ❌ خطأ في الرفع
    echo.
    echo حلول إضافية:
    echo 1. تأكد من تسجيل الدخول على GitHub
    echo 2. تأكد من صلاحيات الرفع
    echo 3. جرب: git push -u origin main --force
    echo.
) else (
    echo ✅ تم رفع المشروع بنجاح
    echo.
    echo رابط المشروع: https://github.com/ahmedhalim1001-coder/FREELANCE
    echo.
    echo الملفات المرفوعة:
    echo - تطبيق الأندرويد
    echo - موقع PHP
    echo - قاعدة البيانات
    echo - ملفات البناء
    echo - ملفات الإصلاح
    echo.
)

:end
echo.
echo ========================================
echo    رفع المشروع على GitHub مكتمل!
echo ========================================
echo.
echo إذا استمرت المشاكل:
echo 1. تأكد من تسجيل الدخول على GitHub
echo 2. تأكد من صلاحيات الرفع
echo 3. جرب الحلول الإضافية
echo.
pause
