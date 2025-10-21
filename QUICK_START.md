# دليل البدء السريع 🚀

## خطوات التشغيل السريع

### 1. إعداد قاعدة البيانات (5 دقائق)

```bash
# تسجيل الدخول لـ MySQL
mysql -u root -p

# إنشاء قاعدة البيانات
CREATE DATABASE shipment_tracking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# استخدام قاعدة البيانات
USE shipment_tracking;

# تشغيل ملف SQL
source database_setup.sql;
```

### 2. إعداد السيرفر (3 دقائق)

1. **انسخ الملفات:**
   ```bash
   # انسخ مجلد api إلى مجلد الويب
   cp -r api /var/www/html/shipment_tracking/
   
   # انسخ مجلد website إلى مجلد الويب
   cp -r website /var/www/html/
   ```

2. **عدّل إعدادات قاعدة البيانات:**
   ```php
   // في ملف api/config.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'shipment_tracking');
   define('DB_USER', 'root');
   define('DB_PASS', 'your_password');
   ```

### 3. اختبار الموقع (2 دقيقة)

1. افتح المتصفح واذهب إلى: `http://localhost/website/login.php`
2. سجل دخول بـ:
   - اسم المستخدم: `admin`
   - كلمة المرور: `1234`
3. تأكد من عمل الصفحات

### 4. إعداد التطبيق (5 دقائق)

1. **افتح المشروع في Android Studio**
2. **عدّل رابط السيرفر:**
   ```kotlin
   // في ملف RetrofitInstance.kt
   private const val BASE_URL = "http://10.0.2.2/shipment_tracking/api/"
   // أو استخدم IP السيرفر الفعلي
   ```

3. **شغّل التطبيق على الجهاز**

### 5. اختبار التكامل (5 دقائق)

1. **في التطبيق:**
   - أضف شركة جديدة
   - امسح باركود تجريبي
   - تحقق من الإحصائيات

2. **في الموقع:**
   - تأكد من ظهور البيانات
   - جرب الفلترة والبحث

## 🔧 إعدادات مهمة

### للمحاكي Android
```kotlin
// استخدم 10.0.2.2 للوصول لـ localhost
private const val BASE_URL = "http://10.0.2.2/shipment_tracking/api/"
```

### للجهاز الفعلي
```kotlin
// استخدم IP السيرفر الفعلي
private const val BASE_URL = "http://192.168.1.100/shipment_tracking/api/"
```

### للسيرفر الخارجي
```kotlin
// استخدم رابط السيرفر الكامل
private const val BASE_URL = "https://yourdomain.com/shipment_tracking/api/"
```

## 🐛 حل المشاكل الشائعة

### خطأ الاتصال في التطبيق
1. تأكد من صحة رابط السيرفر
2. تحقق من إعدادات الشبكة
3. تأكد من تشغيل السيرفر

### خطأ قاعدة البيانات
1. تحقق من بيانات الاتصال
2. تأكد من وجود قاعدة البيانات
3. راجع صلاحيات المستخدم

### مشاكل المسح
1. أعط صلاحية الكاميرا للتطبيق
2. تأكد من جودة الباركود
3. تحقق من الإضاءة

## 📱 اختبار سريع

### 1. اختبار APIs
```bash
# اختبار جلب الشركات
curl -u admin:1234 http://localhost/shipment_tracking/api/getCompanies.php

# اختبار إضافة شحنة
curl -X POST -u admin:1234 \
  -H "Content-Type: application/json" \
  -d '{"barcode":"TEST123","company_id":1}' \
  http://localhost/shipment_tracking/api/addShipment.php
```

### 2. اختبار الموقع
- صفحة تسجيل الدخول: `http://localhost/website/login.php`
- لوحة التحكم: `http://localhost/website/dashboard.php`
- استعراض الشحنات: `http://localhost/website/shipments.php`

## ✅ قائمة التحقق

- [ ] قاعدة البيانات تم إنشاؤها
- [ ] APIs تعمل بشكل صحيح
- [ ] الموقع يفتح ويسجل دخول
- [ ] التطبيق يتصل بالسيرفر
- [ ] مسح الباركود يعمل
- [ ] البيانات تظهر في الموقع

## 🎯 الخطوات التالية

1. **إضافة شركات تجريبية**
2. **اختبار مسح باركود حقيقي**
3. **تخصيص التصميم حسب الحاجة**
4. **إضافة مميزات إضافية**
5. **نشر النظام على سيرفر إنتاج**

---

**مدة الإعداد الإجمالية: 15-20 دقيقة** ⏱️
