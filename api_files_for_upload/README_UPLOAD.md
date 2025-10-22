# ملفات API جاهزة للرفع 🚀

## الملفات المطلوبة للرفع:

### الملفات الأساسية (مطلوبة):
- ✅ `config.php` - إعدادات قاعدة البيانات والمصادقة
- ✅ `getStats.php` - إحصائيات الشحنات (الملف المفقود)
- ✅ `getCompanies.php` - قائمة الشركات
- ✅ `getShipments.php` - قائمة الشحنات
- ✅ `addCompany.php` - إضافة شركة
- ✅ `addShipment.php` - إضافة شحنة
- ✅ `toggleCompany.php` - تفعيل/إلغاء تفعيل شركة

### الملفات الإضافية (اختيارية):
- `getStatsFixed.php` - نسخة محسنة من getStats
- `getStatsSimple.php` - نسخة مبسطة من getStats
- `getCompaniesFixed.php` - نسخة محسنة من getCompanies
- `getCompaniesSimple.php` - نسخة مبسطة من getCompanies

## خطوات الرفع:

### 1. عبر cPanel File Manager:
1. ادخل إلى cPanel الخاص بك
2. افتح File Manager
3. انتقل إلى `public_html/shipment_tracking/`
4. أنشئ مجلد `api` إذا لم يكن موجوداً
5. ارفع جميع الملفات من هذا المجلد إلى `api/` على السيرفر

### 2. عبر FTP:
1. استخدم برنامج FTP مثل FileZilla
2. اتصل بالسيرفر
3. انتقل إلى `public_html/shipment_tracking/`
4. أنشئ مجلد `api` إذا لم يكن موجوداً
5. ارفع جميع الملفات

## بعد الرفع:

### 1. اختبر الرابط:
```
https://zabda-al-tajamil.com/shipment_tracking/api/getStats.php
```

### 2. يجب أن تحصل على استجابة JSON مثل:
```json
{
  "success": true,
  "date": "2024-01-01",
  "statistics": {
    "total_unique_shipments": 0,
    "total_scans": 0,
    "duplicate_count": 0
  }
}
```

### 3. إذا حصلت على خطأ 401:
- هذا طبيعي لأن الملف يتطلب مصادقة
- يعني أن الملف يعمل بشكل صحيح

## ملاحظات مهمة:

- ✅ تأكد من رفع ملف `config.php` أولاً
- ✅ جميع الملفات تحتاج إلى صلاحيات 644
- ✅ مجلد `api` يحتاج صلاحيات 755
- ✅ قاعدة البيانات يجب أن تكون متصلة

## استكشاف الأخطاء:

إذا استمر الخطأ 404:
1. تحقق من وجود الملفات على السيرفر
2. تحقق من إعدادات قاعدة البيانات في `config.php`
3. تحقق من سجلات الأخطاء في cPanel
4. تأكد من أن PHP يعمل بشكل صحيح

## بعد النجاح:

1. استخدم زر "إعادة المحاولة" في التطبيق
2. تحقق من ظهور الإحصائيات
3. اختبر جميع الوظائف في التطبيق
