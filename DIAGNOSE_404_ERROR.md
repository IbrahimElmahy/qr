# تشخيص وحل مشكلة 404 Not Found

## 🔍 المشكلة الحالية
```
Not Found
The requested URL was not found on this server.
https://zabda-al-tajamil.com/shipment_tracking/api/
https://zabda-al-tajamil.com/shipment_tracking/api/getStats.php
```

## 🎯 الحلول المطلوبة

### الحل الأول: التحقق من هيكل السيرفر

#### 1. تحقق من وجود المجلدات:
```
https://zabda-al-tajamil.com/shipment_tracking/
https://zabda-al-tajamil.com/shipment_tracking/api/
```

#### 2. إذا لم يكن المجلد موجوداً:
- أنشئ مجلد `shipment_tracking` في `public_html`
- أنشئ مجلد `api` داخل `shipment_tracking`

### الحل الثاني: رفع الملفات مباشرة

#### 1. ارفع الملفات إلى:
```
public_html/shipment_tracking/api/
```

#### 2. الملفات المطلوبة:
- `config.php`
- `getStats.php`
- `getCompanies.php`
- `getShipments.php`
- `addCompany.php`
- `addShipment.php`
- `toggleCompany.php`

### الحل الثالث: استخدام مسار بديل

#### 1. جرب هذه المسارات:
```
https://zabda-al-tajamil.com/api/
https://zabda-al-tajamil.com/shipment_tracking/
https://zabda-al-tajamil.com/
```

#### 2. إذا كان المسار مختلفاً، غيّر في `RetrofitInstance.kt`:
```kotlin
private const val BASE_URL = "https://zabda-al-tajamil.com/api/"
// أو
private const val BASE_URL = "https://zabda-al-tajamil.com/shipment_tracking/"
```

## 🛠️ خطوات الحل

### الخطوة 1: التحقق من السيرفر
1. ادخل إلى cPanel
2. افتح File Manager
3. انتقل إلى `public_html`
4. تحقق من وجود مجلد `shipment_tracking`

### الخطوة 2: إنشاء المجلدات
إذا لم تكن موجودة:
1. أنشئ مجلد `shipment_tracking`
2. أنشئ مجلد `api` داخل `shipment_tracking`

### الخطوة 3: رفع الملفات
1. ارفع جميع الملفات من `api_files_for_upload/`
2. تأكد من الصلاحيات (644 للملفات، 755 للمجلدات)

### الخطوة 4: اختبار الاتصال
```
https://zabda-al-tajamil.com/shipment_tracking/api/getStats.php
```

## 🔧 حلول بديلة

### الحل البديل 1: رفع الملفات إلى الجذر
```
public_html/api/
```

### الحل البديل 2: استخدام subdomain
```
api.zabda-al-tajamil.com/
```

### الحل البديل 3: رفع الملفات مباشرة
```
public_html/getStats.php
```

## 📋 قائمة التحقق

- [ ] تحقق من وجود مجلد `shipment_tracking`
- [ ] تحقق من وجود مجلد `api`
- [ ] ارفع جميع الملفات
- [ ] تحقق من الصلاحيات
- [ ] اختبر الرابط
- [ ] غيّر BASE_URL إذا لزم الأمر

## 🚨 إذا استمر الخطأ

1. **تحقق من إعدادات السيرفر**
2. **تحقق من وجود ملف .htaccess**
3. **تحقق من إعدادات PHP**
4. **راجع سجلات الأخطاء في cPanel**
