package com.example.packaging.data.network

import okhttp3.Interceptor
import okhttp3.OkHttpClient
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit

object RetrofitInstance {
    
    // ========================================
    // خيارات مختلفة لـ BASE_URL
    // ========================================
    
    // الخيار الأول: رفع مباشر إلى الجذر
    private const val BASE_URL_OPTION_1 = "https://zabda-al-tajamil.com/"
    
    // الخيار الثاني: مجلد api في الجذر
    private const val BASE_URL_OPTION_2 = "https://zabda-al-tajamil.com/api/"
    
    // الخيار الثالث: المسار الأصلي (إذا تم إصلاحه)
    private const val BASE_URL_OPTION_3 = "https://zabda-al-tajamil.com/shipment_tracking/api/"
    
    // الخيار الرابع: subdomain
    private const val BASE_URL_OPTION_4 = "https://api.zabda-al-tajamil.com/"
    
    // ========================================
    // اختر الخيار المناسب حسب مكان رفع الملفات
    // ========================================
    
    // إذا رفعت الملفات إلى public_html/ مباشرة
    private const val BASE_URL = BASE_URL_OPTION_1
    
    // إذا رفعت الملفات إلى public_html/api/
    // private const val BASE_URL = BASE_URL_OPTION_2
    
    // إذا أصلحت المسار الأصلي
    // private const val BASE_URL = BASE_URL_OPTION_3
    
    // إذا استخدمت subdomain
    // private const val BASE_URL = BASE_URL_OPTION_4
    
    // بيانات المصادقة
    private const val USERNAME = "admin"
    private const val PASSWORD = "1234"

    private val authInterceptor = Interceptor { chain ->
        val credentials = "$USERNAME:$PASSWORD"
        val encoded = android.util.Base64.encodeToString(credentials.toByteArray(), android.util.Base64.NO_WRAP)
        val request = chain.request().newBuilder()
            .addHeader("Authorization", "Basic $encoded")
            .build()
        chain.proceed(request)
    }

    private val okHttpClient = OkHttpClient.Builder()
        .addInterceptor(authInterceptor)
        .connectTimeout(30, TimeUnit.SECONDS)
        .readTimeout(30, TimeUnit.SECONDS)
        .writeTimeout(30, TimeUnit.SECONDS)
        .build()

    val api: ApiService by lazy {
        Retrofit.Builder()
            .baseUrl(BASE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(ApiService::class.java)
    }
    
    // دالة لاختبار الاتصال
    fun getBaseUrl(): String = BASE_URL
}

/*
تعليمات الاستخدام:

1. إذا رفعت الملفات إلى public_html/ مباشرة:
   - استخدم BASE_URL_OPTION_1
   - اختبر: https://zabda-al-tajamil.com/getStats.php

2. إذا رفعت الملفات إلى public_html/api/:
   - استخدم BASE_URL_OPTION_2
   - اختبر: https://zabda-al-tajamil.com/api/getStats.php

3. إذا أصلحت المسار الأصلي:
   - استخدم BASE_URL_OPTION_3
   - اختبر: https://zabda-al-tajamil.com/shipment_tracking/api/getStats.php

4. إذا استخدمت subdomain:
   - استخدم BASE_URL_OPTION_4
   - اختبر: https://api.zabda-al-tajamil.com/getStats.php

ملاحظة: غيّر BASE_URL حسب المكان الذي رفعت فيه الملفات
*/
