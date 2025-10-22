# دليل حل مشكلة المصادقة على السيرفر

## 🔍 تحليل المشكلة

### ✅ ما تم تأكيده:
- التطبيق يرسل البيانات الصحيحة (`admin` / `1234`)
- ملف `RetrofitInstance.kt` صحيح
- المشكلة في السيرفر وليس في التطبيق

### 🎯 الاحتمالات:
1. **خطأ في ملف `config.php` على السيرفر**
2. **مشكلة في إعدادات السيرفر**
3. **مشكلة في قاعدة البيانات**

## 🛠️ خطوات الحل

### الخطوة 1: التحقق من ملف config.php على السيرفر

#### 1. ادخل إلى cPanel
#### 2. افتح File Manager
#### 3. انتقل إلى `public_html`
#### 4. افتح ملف `config.php`
#### 5. تحقق من هذه الأسطر بالضبط:

```php
// إعدادات المصادقة
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', '1234');
```

### الخطوة 2: إذا كانت البيانات مختلفة

#### 1. غيّر البيانات في ملف `config.php` على السيرفر
#### 2. أو غيّر البيانات في التطبيق لتتطابق مع السيرفر

### الخطوة 3: التحقق من سجلات الأخطاء

#### 1. في cPanel، اذهب إلى "Error Logs"
#### 2. ابحث عن أخطاء متعلقة بـ PHP أو قاعدة البيانات
#### 3. راجع الأخطاء الحديثة

### الخطوة 4: اختبار الاتصال بقاعدة البيانات

#### 1. أنشئ ملف اختبار بسيط:

```php
<?php
// اختبار الاتصال بقاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_NAME', 'ztjmal_shipmen');
define('DB_USER', 'ztjmal_ahmed');
define('DB_PASS', 'Ahmedhelmy12');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    echo "✅ الاتصال بقاعدة البيانات نجح!";
} catch (PDOException $e) {
    echo "❌ خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage();
}
?>
```

#### 2. ارفع هذا الملف إلى `public_html/test_db.php`
#### 3. اختبر الرابط: `https://zabda-al-tajamil.com/test_db.php`

### الخطوة 5: اختبار المصادقة

#### 1. أنشئ ملف اختبار للمصادقة:

```php
<?php
// اختبار المصادقة
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', '1234');

function testAuth() {
    if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
        return "❌ لا يوجد Authorization header";
    }
    
    $auth = $_SERVER['HTTP_AUTHORIZATION'];
    if (strpos($auth, 'Basic ') !== 0) {
        return "❌ تنسيق Authorization غير صحيح";
    }
    
    $credentials = base64_decode(substr($auth, 6));
    list($username, $password) = explode(':', $credentials, 2);
    
    if ($username !== ADMIN_USERNAME || $password !== ADMIN_PASSWORD) {
        return "❌ بيانات المصادقة غير صحيحة. المستلم: '$username' / '$password'";
    }
    
    return "✅ المصادقة نجحت!";
}

echo testAuth();
?>
```

#### 2. ارفع هذا الملف إلى `public_html/test_auth.php`
#### 3. اختبر الرابط: `https://zabda-al-tajamil.com/test_auth.php`

## 🔧 حلول إضافية

### الحل 1: إعادة رفع ملف config.php

#### 1. استخدم الملف المحدث `config_updated.php`
#### 2. ارفعه إلى `public_html/config.php`
#### 3. تأكد من استبدال الملف القديم

### الحل 2: التحقق من إعدادات PHP

#### 1. في cPanel، اذهب إلى "PHP Selector"
#### 2. تأكد من أن PHP يعمل بشكل صحيح
#### 3. تحقق من إعدادات PHP

### الحل 3: التحقق من قاعدة البيانات

#### 1. في cPanel، اذهب إلى "phpMyAdmin"
#### 2. تحقق من وجود قاعدة البيانات `ztjmal_shipmen`
#### 3. تحقق من وجود الجداول المطلوبة

## 🧪 اختبار نهائي

### 1. اختبر الرابط:
```
https://zabda-al-tajamil.com/getStats.php
```

### 2. إذا ظهر خطأ 401، هذا طبيعي
### 3. إذا ظهر خطأ 404، الملف غير موجود
### 4. إذا ظهر خطأ 500، هناك مشكلة في قاعدة البيانات

## 📱 اختبار التطبيق

### 1. أعد بناء التطبيق
### 2. شغّل التطبيق
### 3. اذهب إلى صفحة الإحصائيات
### 4. استخدم زر "إعادة المحاولة"

## 🎯 النتيجة المتوقعة

- ✅ لا توجد أخطاء 404
- ✅ لا توجد أخطاء مصادقة
- ✅ تظهر الإحصائيات بشكل صحيح
- ✅ يعمل زر "إعادة المحاولة"
- ✅ البيانات تظهر من قاعدة البيانات

## 🚨 إذا استمرت المشاكل

1. **راجع سجلات الأخطاء في cPanel**
2. **تحقق من إعدادات قاعدة البيانات**
3. **تأكد من أن PHP يعمل بشكل صحيح**
4. **راجع إعدادات Apache**
