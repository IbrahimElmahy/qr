package com.example.packaging.data.network

import okhttp3.Interceptor
import okhttp3.OkHttpClient
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit
import android.util.Log

object RetrofitInstance {
    // رابط السيرفر الحقيقي - رفع مباشر إلى الجذر
    private const val BASE_URL = "https://zabda-al-tajamil.com/"
    
    // بيانات المصادقة
    private const val USERNAME = "admin"
    private const val PASSWORD = "1234"

    private val authInterceptor = Interceptor { chain ->
        val credentials = "$USERNAME:$PASSWORD"
        val encoded = android.util.Base64.encodeToString(credentials.toByteArray(), android.util.Base64.NO_WRAP)
        
        // Debug logging
        Log.d("RetrofitInstance", "🔍 Sending request to: ${chain.request().url}")
        Log.d("RetrofitInstance", "🔍 Authorization header: Basic $encoded")
        Log.d("RetrofitInstance", "🔍 Username: $USERNAME")
        Log.d("RetrofitInstance", "🔍 Password: $PASSWORD")
        
        val request = chain.request().newBuilder()
            .addHeader("Authorization", "Basic $encoded")
            .addHeader("Content-Type", "application/json")
            .addHeader("Accept", "application/json")
            .build()
        
        Log.d("RetrofitInstance", "🔍 Request headers: ${request.headers}")
        
        val response = chain.proceed(request)
        
        // Debug logging
        Log.d("RetrofitInstance", "🔍 Response code: ${response.code}")
        Log.d("RetrofitInstance", "🔍 Response headers: ${response.headers}")
        
        response
    }

    private val okHttpClient = OkHttpClient.Builder()
        .addInterceptor(authInterceptor)
        .connectTimeout(30, TimeUnit.SECONDS)
        .readTimeout(30, TimeUnit.SECONDS)
        .writeTimeout(30, TimeUnit.SECONDS)
        .build()

    val api: ApiService by lazy {
        Log.d("RetrofitInstance", "🔍 Creating Retrofit instance with BASE_URL: $BASE_URL")
        
        Retrofit.Builder()
            .baseUrl(BASE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(ApiService::class.java)
    }
    
    // دالة لاختبار الاتصال
    fun testConnection() {
        Log.d("RetrofitInstance", "🔍 Testing API connection...")
        Log.d("RetrofitInstance", "🔍 BASE_URL: $BASE_URL")
        Log.d("RetrofitInstance", "🔍 USERNAME: $USERNAME")
        Log.d("RetrofitInstance", "🔍 PASSWORD: $PASSWORD")
    }
}

/*
تعليمات الاستخدام:

1. استبدل ملف RetrofitInstance.kt الحالي بهذا الملف
2. أعد بناء التطبيق (Build → Rebuild Project)
3. شغّل التطبيق
4. تحقق من Logcat للأخطاء
5. اذهب إلى صفحة الإحصائيات
6. استخدم زر "إعادة المحاولة"

في Logcat، يجب أن ترى:
- 🔍 Sending request to: https://zabda-al-tajamil.com/getStats.php
- 🔍 Authorization header: Basic YWRtaW46MTIzNA==
- 🔍 Response code: 200

إذا لم تر هذه الرسائل، فهناك مشكلة في إعدادات Retrofit
*/
