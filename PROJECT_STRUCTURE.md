# 📁 هيكل مشروع نظام تتبع الشحنات

## 🎯 **المشروع الكامل**:

```
packaging/                                    # 📂 المجلد الرئيسي
├── 📱 **تطبيق Android** (Kotlin)
│   ├── app/
│   │   ├── src/main/java/com/example/packaging/
│   │   │   ├── 📱 MainActivity.kt                    # النشاط الرئيسي
│   │   │   ├── data/                                 # 📊 طبقة البيانات
│   │   │   │   ├── AppDatabase.kt                    # قاعدة البيانات المحلية
│   │   │   │   ├── Shipment.kt                       # نموذج الشحنة
│   │   │   │   ├── CompanyEntity.kt                  # نموذج الشركة
│   │   │   │   ├── ShipmentDao.kt                    # واجهة قاعدة البيانات
│   │   │   │   ├── CompanyDao.kt                     # واجهة الشركات
│   │   │   │   ├── Repository.kt                     # طبقة البيانات الموحدة
│   │   │   │   └── network/                          # 🌐 طبقة الشبكة
│   │   │   │       ├── ApiService.kt                 # واجهة API
│   │   │   │       └── RetrofitInstance.kt          # إعداد Retrofit
│   │   │   └── ui/                                   # 🎨 واجهة المستخدم
│   │   │       ├── scanner/                         # مسح الباركود
│   │   │       │   ├── BarcodeScannerFragment.kt     # شاشة المسح
│   │   │       │   └── BarcodeScannerViewModel.kt    # منطق المسح
│   │   │       ├── shipping/                         # إدارة الشركات
│   │   │       │   ├── ShippingCompaniesFragment.kt  # شاشة الشركات
│   │   │       │   ├── ShippingCompaniesViewModel.kt # منطق الشركات
│   │   │       │   └── CompaniesAdapter.kt          # قائمة الشركات
│   │   │       └── stats/                            # الإحصائيات
│   │   │           ├── DailyStatisticsFragment.kt    # شاشة الإحصائيات
│   │   │           ├── DailyStatisticsViewModel.kt   # منطق الإحصائيات
│   │   │           └── ShipmentAdapter.kt            # قائمة الشحنات
│   │   ├── src/main/res/                             # 🎨 الموارد
│   │   │   ├── layout/                               # تخطيطات الشاشات
│   │   │   ├── values/                               # القيم والسلاسل
│   │   │   └── drawable/                             # الصور والأيقونات
│   │   └── build/outputs/apk/debug/
│   │       └── app-debug.apk                         # 📱 ملف التطبيق النهائي
│   └── build.gradle.kts                             # إعدادات Gradle
│
├── 🌐 **الموقع الإلكتروني** (PHP)
│   ├── website/                                     # 📂 مجلد الموقع
│   │   ├── login.php                                 # 🔐 تسجيل الدخول
│   │   ├── dashboard.php                             # 📊 لوحة التحكم العادية
│   │   ├── shipments.php                             # 📦 استعراض الشحنات
│   │   ├── logout.php                                # 🚪 تسجيل الخروج
│   │   └── **الملفات الجديدة** 🆕
│   │       ├── admin_dashboard.php                   # 🎛️ لوحة التحكم الإدارية
│   │       ├── companies_management.php              # 🏢 إدارة الشركات
│   │       ├── users_management.php                  # 👥 إدارة المستخدمين
│   │       └── reports.php                           # 📈 التقارير المتقدمة
│   │
│   └── api/                                          # 🔌 واجهة برمجة التطبيقات
│       ├── config.php                                # ⚙️ إعدادات قاعدة البيانات
│       ├── getCompanies.php                          # 📋 جلب الشركات
│       ├── addCompany.php                            # ➕ إضافة شركة
│       ├── toggleCompany.php                         # 🔄 تفعيل/إلغاء شركة
│       ├── addShipment.php                           # 📦 إضافة شحنة
│       ├── getShipments.php                          # 📋 جلب الشحنات
│       └── getStats.php                              # 📊 الإحصائيات
│
├── 🗄️ **قاعدة البيانات** (MySQL)
│   ├── database_setup.sql                           # 🏗️ إنشاء قاعدة البيانات
│   ├── tables_only.sql                             # 📋 إنشاء الجداول فقط
│   └── **الجداول**:
│       ├── companies                                # 🏢 جدول الشركات
│       └── shipments                                # 📦 جدول الشحنات
│
├── 📋 **ملفات التوثيق**
│   ├── README.md                                     # 📖 دليل المشروع
│   ├── BUILD_APK.md                                  # 🔨 بناء التطبيق
│   ├── INSTALL_APK.md                                # 📱 تثبيت التطبيق
│   ├── ENHANCED_FEATURES.md                          # ✨ المميزات الجديدة
│   └── UPLOAD_INSTRUCTIONS_SIMPLE.md                # 📤 تعليمات الرفع
│
├── 🧪 **ملفات الاختبار**
│   ├── test_integration.php                          # 🔗 اختبار التكامل
│   ├── test_simple.php                               # 🧪 اختبار بسيط
│   ├── test_basic.php                                # ✅ اختبار أساسي
│   └── test_admin.php                                # 🎛️ اختبار الإدارة
│
└── 📦 **ملفات التوزيع**
    ├── new_admin_files.zip                           # 📁 الملفات الجديدة
    ├── shipment_system.zip                           # 📦 النظام الكامل
    └── app-debug.apk                                 # 📱 التطبيق النهائي
```

## 🎯 **المكونات الرئيسية**:

### **1. 📱 تطبيق Android**:
- **اللغة**: Kotlin
- **قاعدة البيانات المحلية**: Room Database
- **الشبكة**: Retrofit + Gson
- **المسح**: ZXing Barcode Scanner
- **الواجهة**: Material Design

### **2. 🌐 الموقع الإلكتروني**:
- **اللغة**: PHP
- **قاعدة البيانات**: MySQL
- **التصميم**: Bootstrap 5
- **المصادقة**: Basic Authentication

### **3. 🗄️ قاعدة البيانات**:
- **النوع**: MySQL
- **الجداول**: companies, shipments
- **الفهارس**: محسنة للأداء

## 🚀 **الروابط المهمة**:

### **الموقع**:
- **تسجيل الدخول**: https://zabda-al-tajamil.com/shipment_tracking/website/login.php
- **لوحة التحكم العادية**: https://zabda-al-tajamil.com/shipment_tracking/website/dashboard.php
- **لوحة التحكم الإدارية**: https://zabda-al-tajamil.com/shipment_tracking/website/admin_dashboard.php

### **API**:
- **جلب الشركات**: https://zabda-al-tajamil.com/shipment_tracking/api/getCompanies.php
- **الإحصائيات**: https://zabda-al-tajamil.com/shipment_tracking/api/getStats.php

## 🔑 **بيانات الدخول**:
- **اسم المستخدم**: admin
- **كلمة المرور**: 1234

## 📱 **التطبيق**:
- **ملف APK**: `app-debug.apk`
- **الحجم**: ~15 MB
- **الإصدار**: Debug

---
**المشروع جاهز للاستخدام! 🚀**
