package com.example.packaging

import android.os.Bundle
import android.util.Log
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.example.packaging.data.network.RetrofitInstance
import kotlinx.coroutines.launch

class MainActivity : AppCompatActivity() {
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)
        
        // اختبار API عند بدء التطبيق
        testApiConnection()
    }
    
    private fun testApiConnection() {
        lifecycleScope.launch {
            try {
                Log.d("MainActivity", "🔍 Testing API connection...")
                Log.d("MainActivity", "🔍 BASE_URL: ${RetrofitInstance.getBaseUrl()}")
                
                // اختبار getStats
                val statsResponse = RetrofitInstance.api.getStatistics("2024-01-01", null)
                Log.d("MainActivity", "🔍 getStats Response: $statsResponse")
                
                // اختبار getCompanies
                val companiesResponse = RetrofitInstance.api.getCompanies()
                Log.d("MainActivity", "🔍 getCompanies Response: $companiesResponse")
                
            } catch (e: Exception) {
                Log.e("MainActivity", "🔍 API Error: ${e.message}", e)
            }
        }
    }
}

/*
تعليمات الاستخدام:

1. استبدل ملف MainActivity.kt الحالي بهذا الملف
2. أعد بناء التطبيق (Build → Rebuild Project)
3. شغّل التطبيق
4. تحقق من Logcat للأخطاء
5. اذهب إلى صفحة الإحصائيات
6. استخدم زر "إعادة المحاولة"

في Logcat، يجب أن ترى:
- 🔍 Testing API connection...
- 🔍 BASE_URL: https://zabda-al-tajamil.com/
- 🔍 getStats Response: [الاستجابة]
- 🔍 getCompanies Response: [الاستجابة]

إذا لم تر هذه الرسائل، فهناك مشكلة في إعدادات Retrofit
*/
