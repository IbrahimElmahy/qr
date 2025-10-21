# 🚀 نظام تتبع الشحنات - الإعداد الكامل

## 📋 **ملف شامل يحتوي على**:

### **🌐 الموقع الإلكتروني**:
- ✅ **جميع صفحات الموقع** (login, dashboard, admin_dashboard, companies_management, users_management, reports, shipments)
- ✅ **واجهة برمجة التطبيقات** (API) كاملة
- ✅ **إعدادات قاعدة البيانات** محدثة
- ✅ **ملفات الاختبار** للتأكد من عمل النظام

### **🗄️ قاعدة البيانات**:
- ✅ **ملف إنشاء قاعدة البيانات** (`database_setup.sql`)
- ✅ **ملف إنشاء الجداول فقط** (`tables_only.sql`)
- ✅ **بيانات تجريبية** للاختبار

### **📱 تطبيق Android**:
- ✅ **ملف APK جاهز** (`app-debug.apk`)
- ✅ **كود التطبيق** (Kotlin)
- ✅ **قاعدة البيانات المحلية** (Room)
- ✅ **مسح الباركود** (ZXing)

## 🚀 **خطوات الإعداد**:

### **1. رفع الملفات**:
1. اذهب إلى **cPanel** → **File Manager**
2. انتقل إلى `public_html/`
3. ارفع ملف `complete_system.zip`
4. اضغط **Extract** لاستخراج الملفات
5. انقل محتويات مجلد `complete_system/` إلى `public_html/`

### **2. إعداد قاعدة البيانات**:
1. اذهب إلى **cPanel** → **phpMyAdmin**
2. أنشئ قاعدة بيانات جديدة: `ztjmal_shipmen`
3. أنشئ مستخدم جديد: `ztjmal_ahmed` مع كلمة المرور: `Ahmedhelmy12`
4. ارفع ملف `tables_only.sql` لإنشاء الجداول

### **3. اختبار النظام**:
1. **تسجيل الدخول**: https://zabda-al-tajamil.com/website/login.php
2. **لوحة التحكم الإدارية**: https://zabda-al-tajamil.com/website/admin_dashboard.php
3. **اختبار API**: https://zabda-al-tajamil.com/test_basic.php

## 🔑 **بيانات الدخول**:
- **اسم المستخدم**: admin
- **كلمة المرور**: 1234

## 📱 **تثبيت التطبيق**:
1. انسخ ملف `app-debug.apk` للهاتف
2. فعّل "Unknown Sources" في الهاتف
3. اضغط على APK وثبّته

## 🎯 **المميزات المتاحة**:

### **الموقع الإلكتروني**:
- ✅ **لوحة تحكم إدارية متقدمة** مع إحصائيات شاملة
- ✅ **إدارة الشركات** (إضافة، تعديل، حذف، تفعيل)
- ✅ **إدارة المستخدمين** مع أدوار مختلفة
- ✅ **تقارير متقدمة** مع فلاتر وتصدير
- ✅ **استعراض الشحنات** مع بحث وتصفية

### **تطبيق Android**:
- ✅ **مسح الباركود** وإضافة الشحنات
- ✅ **إدارة الشركات** من التطبيق
- ✅ **الإحصائيات اليومية** مع تفاصيل الشحنات
- ✅ **مزامنة مع السيرفر** تلقائياً
- ✅ **عمل بدون إنترنت** مع قاعدة بيانات محلية

## 🌐 **الروابط المهمة**:

### **الموقع**:
- **تسجيل الدخول**: https://zabda-al-tajamil.com/website/login.php
- **لوحة التحكم الإدارية**: https://zabda-al-tajamil.com/website/admin_dashboard.php
- **إدارة الشركات**: https://zabda-al-tajamil.com/website/companies_management.php
- **إدارة المستخدمين**: https://zabda-al-tajamil.com/website/users_management.php
- **التقارير**: https://zabda-al-tajamil.com/website/reports.php
- **استعراض الشحنات**: https://zabda-al-tajamil.com/website/shipments.php

### **API**:
- **جلب الشركات**: https://zabda-al-tajamil.com/api/getCompanies.php
- **إضافة شركة**: https://zabda-al-tajamil.com/api/addCompany.php
- **إضافة شحنة**: https://zabda-al-tajamil.com/api/addShipment.php
- **الإحصائيات**: https://zabda-al-tajamil.com/api/getStats.php

## 🧪 **ملفات الاختبار**:
- **اختبار أساسي**: https://zabda-al-tajamil.com/test_basic.php
- **اختبار التكامل**: https://zabda-al-tajamil.com/test_integration.php
- **اختبار بسيط**: https://zabda-al-tajamil.com/test_simple.php

## 📊 **قاعدة البيانات**:
- **اسم قاعدة البيانات**: `ztjmal_shipmen`
- **اسم المستخدم**: `ztjmal_ahmed`
- **كلمة المرور**: `Ahmedhelmy12`

## 🎉 **النظام جاهز للاستخدام!**

---
**تم إعداد النظام بالكامل! 🚀**
