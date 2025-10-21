# 📱 بناء ملف APK للتطبيق

## 🚀 خطوات بناء APK:

### الطريقة الأولى: استخدام Android Studio (الأسهل)

1. **افتح المشروع في Android Studio**:
   - افتح Android Studio
   - اختر "Open an existing project"
   - اختر مجلد المشروع

2. **تأكد من الإعدادات**:
   - اذهب إلى File → Project Structure
   - تأكد من أن SDK صحيح
   - تأكد من أن Build Tools محدثة

3. **بناء APK**:
   - اذهب إلى Build → Build Bundle(s) / APK(s) → Build APK(s)
   - انتظر حتى ينتهي البناء
   - ستجد APK في: `app/build/outputs/apk/debug/app-debug.apk`

### الطريقة الثانية: استخدام Gradle (متقدم)

1. **افتح Terminal في Android Studio**:
   - اذهب إلى View → Tool Windows → Terminal

2. **شغّل الأوامر**:
   ```bash
   ./gradlew assembleDebug
   ```

3. **ستجد APK في**:
   - `app/build/outputs/apk/debug/app-debug.apk`

## 📱 تثبيت APK على الهاتف:

### الطريقة الأولى: USB Debugging
1. **فعّل Developer Options**:
   - اذهب إلى Settings → About Phone
   - اضغط على "Build Number" 7 مرات
   - اذهب إلى Settings → Developer Options
   - فعّل "USB Debugging"

2. **اتصل بالكمبيوتر**:
   - استخدم كابل USB
   - اذهب إلى Android Studio
   - اختر Run → Run 'app'
   - اختر الهاتف من القائمة

### الطريقة الثانية: تثبيت مباشر
1. **انسخ APK للهاتف**:
   - انسخ `app-debug.apk` للهاتف
   - استخدم Bluetooth أو USB

2. **ثبّت APK**:
   - اذهب إلى Settings → Security
   - فعّل "Unknown Sources"
   - اضغط على APK وثبّته

## 🔧 إعدادات مهمة:

### 1. تحديث رابط السيرفر:
في ملف `RetrofitInstance.kt`:
```kotlin
private const val BASE_URL = "https://zabda-al-tajamil.com/shipment_tracking/api/"
```

### 2. بيانات المصادقة:
```kotlin
private const val USERNAME = "admin"
private const val PASSWORD = "1234"
```

## 🎯 اختبار التطبيق:

### 1. اختبار الاتصال:
- تأكد من أن الهاتف متصل بالإنترنت
- جرب "تحديث الشركات" في التطبيق

### 2. اختبار مسح الباركود:
- اذهب إلى "مسح الباركود"
- اختر شركة
- جرب مسح باركود

### 3. اختبار الإحصائيات:
- اذهب إلى "الإحصائيات"
- اختر تاريخ وشركة
- راجع النتائج

## 🌐 الروابط المهمة:

- **تسجيل الدخول**: https://zabda-al-tajamil.com/website/login.php
- **لوحة التحكم**: https://zabda-al-tajamil.com/website/dashboard.php
- **اختبار النظام**: https://zabda-al-tajamil.com/test_basic.php

## 🔑 بيانات الدخول:
- **اسم المستخدم**: admin
- **كلمة المرور**: 1234

---
**التطبيق جاهز للبناء والتثبيت! 🚀**
