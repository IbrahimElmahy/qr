package com.example.packaging.data.network

import com.google.gson.GsonBuilder
import okhttp3.Interceptor
import okhttp3.OkHttpClient
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit

object RetrofitInstance {
    // رابط السيرفر الحقيقي
    private const val BASE_URL = "https://zabda-al-tajamil.com/"

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

    // Create a lenient Gson instance
    private val gson = GsonBuilder()
        .setLenient()
        .create()

    val api: ApiService by lazy {
        Retrofit.Builder()
            .baseUrl(BASE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create(gson)) // Use the lenient Gson instance
            .build()
            .create(ApiService::class.java)
    }
}