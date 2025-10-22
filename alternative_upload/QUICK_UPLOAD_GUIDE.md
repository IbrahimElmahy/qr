# دليل الرفع السريع - حل مشكلة 404

## 🚨 المشكلة
```
Not Found
The requested URL was not found on this server.
https://zabda-al-tajamil.com/shipment_tracking/api/getStats.php
```

## 🎯 الحلول السريعة

### الحل الأول: رفع مباشر إلى الجذر
1. **ارفع الملفات إلى `public_html/` مباشرة**
2. **غيّر BASE_URL في التطبيق إلى:**
   ```kotlin
   private const val BASE_URL = "https://zabda-al-tajamil.com/"
   ```

### الحل الثاني: إنشاء مجلد api في الجذر
1. **أنشئ مجلد `api` في `public_html/`**
2. **ارفع الملفات إلى `public_html/api/`**
3. **غيّر BASE_URL إلى:**
   ```kotlin
   private const val BASE_URL = "https://zabda-al-tajamil.com/api/"
   ```

### الحل الثالث: رفع إلى مجلد موجود
1. **تحقق من المجلدات الموجودة في `public_html/`**
2. **ارفع الملفات إلى أي مجلد موجود**
3. **غيّر BASE_URL حسب المسار الجديد**

## 📁 الملفات المطلوبة للرفع

### الملفات الأساسية:
- ✅ `config.php`
- ✅ `getStats.php`
- ✅ `getCompanies.php`
- ✅ `getShipments.php`
- ✅ `addCompany.php`
- ✅ `addShipment.php`
- ✅ `toggleCompany.php`

## 🛠️ خطوات الرفع

### الطريقة 1: عبر cPanel File Manager
1. ادخل إلى cPanel
2. افتح File Manager
3. انتقل إلى `public_html`
4. أنشئ مجلد `api` (إذا لم يكن موجوداً)
5. ارفع جميع الملفات إلى `api/`

### الطريقة 2: عبر FTP
1. اتصل بالسيرفر عبر FTP
2. انتقل إلى `public_html`
3. أنشئ مجلد `api`
4. ارفع جميع الملفات

## 🔧 تعديل التطبيق

### في ملف `RetrofitInstance.kt`:
```kotlin
// الحل الأول: رفع مباشر
private const val BASE_URL = "https://zabda-al-tajamil.com/"

// الحل الثاني: مجلد api في الجذر
private const val BASE_URL = "https://zabda-al-tajamil.com/api/"

// الحل الثالث: حسب المسار الجديد
private const val BASE_URL = "https://zabda-al-tajamil.com/your_folder/"
```

## 🧪 اختبار بعد الرفع

### إذا رفعت إلى الجذر:
```
https://zabda-al-tajamil.com/getStats.php
```

### إذا رفعت إلى مجلد api:
```
https://zabda-al-tajamil.com/api/getStats.php
```

## 📋 قائمة التحقق

- [ ] رفع جميع الملفات
- [ ] تحقق من الصلاحيات (644 للملفات، 755 للمجلدات)
- [ ] غيّر BASE_URL في التطبيق
- [ ] اختبر الرابط في المتصفح
- [ ] استخدم زر "إعادة المحاولة" في التطبيق

## 🚨 إذا استمر الخطأ

1. **تحقق من إعدادات السيرفر**
2. **تحقق من وجود ملف .htaccess**
3. **تحقق من إعدادات PHP**
4. **راجع سجلات الأخطاء في cPanel**
5. **جرب مسارات مختلفة**

## 💡 نصائح إضافية

- **ابدأ بالحل الأول** (رفع مباشر إلى الجذر)
- **اختبر الرابط** في المتصفح قبل تغيير التطبيق
- **احتفظ بنسخة احتياطية** من الملفات
- **اختبر كل ملف على حدة** بعد الرفع
