package com.example.packaging.ui.stats

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import com.example.packaging.data.*
import kotlinx.coroutines.launch
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
    
    private val _errorMessage = MutableLiveData<String>()
    val errorMessage: LiveData<String> = _errorMessage

    val activeCompanies = repository.getActiveCompanies()
    val allShipments = repository.getAllShipments()

    init {
        // تعيين التاريخ الحالي كافتراضي
        val today = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(Date())
        _selectedDate.value = today
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
                        _errorMessage.value = "خطأ في تحميل الإحصائيات: ${error.message}"
                    }
                )
            } catch (e: Exception) {
                _errorMessage.value = "خطأ في الاتصال: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun syncUnsyncedShipments() {
        _isLoading.value = true
        viewModelScope.launch {
            try {
                repository.syncUnsyncedShipments()
                loadStatistics() // إعادة تحميل الإحصائيات بعد المزامنة
            } catch (e: Exception) {
                _errorMessage.value = "خطأ في المزامنة: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun clearMessages() {
        _errorMessage.value = null
    }
}