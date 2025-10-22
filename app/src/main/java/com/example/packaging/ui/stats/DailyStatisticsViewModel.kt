package com.example.packaging.ui.stats

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import com.example.packaging.data.CompanyEntity
import com.example.packaging.data.Repository
import com.example.packaging.data.network.StatisticsResponse
import kotlinx.coroutines.launch
import kotlinx.coroutines.delay
import java.text.SimpleDateFormat
import java.util.*

class DailyStatisticsViewModel(application: Application) : AndroidViewModel(application) {

    private val repository = Repository(application)

    private val _selectedDate = MutableLiveData<String>()
    val selectedDate: LiveData<String> = _selectedDate

    private val _selectedCompany = MutableLiveData<CompanyEntity?>()
    val selectedCompany: LiveData<CompanyEntity?> = _selectedCompany

    private val _statistics = MutableLiveData<StatisticsResponse?>()
    val statistics: LiveData<StatisticsResponse?> = _statistics

    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    private val _errorMessage = MutableLiveData<String?>()
    val errorMessage: LiveData<String?> = _errorMessage

    val activeCompanies = repository.getActiveCompanies()

    init {
        val today = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(Date())
        _selectedDate.value = today
        
        // تحميل الشركات أولاً
        loadCompanies()
        
        // تحميل الإحصائيات بعد تحميل الشركات
        viewModelScope.launch {
            delay(1000) // انتظار قصير لتحميل الشركات
            loadStatistics()
        }
    }

    fun setSelectedDate(date: String) {
        _selectedDate.value = date
        loadStatistics()
    }

    fun setSelectedCompany(company: CompanyEntity?) {
        _selectedCompany.value = company
        loadStatistics()
    }

    fun loadStatistics() {
        val date = _selectedDate.value ?: return
        val companyId = _selectedCompany.value?.id

        _isLoading.value = true
        viewModelScope.launch {
            try {
                val result = repository.getStatistics(companyId, date)
                result.fold(
                    onSuccess = { stats ->
                        _statistics.value = stats
                        _errorMessage.value = null
                    },
                    onFailure = { error ->
                        _statistics.value = null
                        if (error.message?.contains("404") == true) {
                            _errorMessage.value = "خطأ 404: الملف غير موجود."
                        } else if (error.message?.contains("401") == true) {
                            _errorMessage.value = "خطأ 401: المصادقة فشلت."
                        } else {
                            _errorMessage.value = "خطأ في التحميل: ${error.message}"
                        }
                    }
                )
            } catch (e: Exception) {
                _statistics.value = null
                _errorMessage.value = "خطأ في الاتصال: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun loadCompanies() {
        viewModelScope.launch {
            try {
                android.util.Log.d("DailyStatisticsViewModel", "🔍 Loading companies...")
                repository.loadCompaniesFromAPI()
            } catch (e: Exception) {
                android.util.Log.e("DailyStatisticsViewModel", "🔍 Error loading companies: ${e.message}")
                _errorMessage.value = "فشل تحميل الشركات: ${e.message}"
            }
        }
    }

    fun syncUnsyncedShipments() {
        // This function seems to be for shipments, not companies.
        // It is kept for its original purpose.
    }

    fun clearMessages() {
        _errorMessage.value = null
    }
}