package com.example.packaging.ui.shipping

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import com.example.packaging.data.*
import kotlinx.coroutines.launch

class ShippingCompaniesViewModel(application: Application) : AndroidViewModel(application) {

    private val repository = Repository(application)
    
    private val _companies = MutableLiveData<List<CompanyEntity>>()
    val companies: LiveData<List<CompanyEntity>> = _companies
    
    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading
    
    private val _errorMessage = MutableLiveData<String>()
    val errorMessage: LiveData<String> = _errorMessage
    
    private val _successMessage = MutableLiveData<String>()
    val successMessage: LiveData<String> = _successMessage

    init {
        loadCompanies()
    }

    private fun loadCompanies() {
        _isLoading.value = true
        viewModelScope.launch {
            try {
                repository.getCompanies().collect { companiesList ->
                    _companies.value = companiesList
                    _isLoading.value = false
                }
            } catch (e: Exception) {
                _errorMessage.value = "خطأ في تحميل الشركات: ${e.message}"
                _isLoading.value = false
            }
        }
    }

    fun addCompany(name: String) {
        if (name.isBlank()) {
            _errorMessage.value = "يرجى إدخال اسم الشركة"
            return
        }

        _isLoading.value = true
        viewModelScope.launch {
            try {
                val result = repository.addCompany(name)
                result.fold(
                    onSuccess = { company ->
                        _successMessage.value = "تم إضافة الشركة: ${company.name}"
                        loadCompanies() // إعادة تحميل القائمة
                    },
                    onFailure = { error ->
                        _errorMessage.value = "خطأ في إضافة الشركة: ${error.message}"
                    }
                )
            } catch (e: Exception) {
                _errorMessage.value = "خطأ في الاتصال: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun toggleCompany(company: CompanyEntity) {
        _isLoading.value = true
        viewModelScope.launch {
            try {
                val result = repository.toggleCompany(company.id)
                result.fold(
                    onSuccess = { updatedCompany ->
                        val message = if (updatedCompany.isActive) "تم تفعيل الشركة" else "تم تعطيل الشركة"
                        _successMessage.value = "$message: ${updatedCompany.name}"
                        loadCompanies() // إعادة تحميل القائمة
                    },
                    onFailure = { error ->
                        _errorMessage.value = "خطأ في تحديث الشركة: ${error.message}"
                    }
                )
            } catch (e: Exception) {
                _errorMessage.value = "خطأ في الاتصال: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun syncCompanies() {
        _isLoading.value = true
        viewModelScope.launch {
            try {
                repository.syncCompanies()
                _successMessage.value = "تم تحديث الشركات من السيرفر"
                loadCompanies()
            } catch (e: Exception) {
                _errorMessage.value = "خطأ في المزامنة: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun clearMessages() {
        _errorMessage.value = null
        _successMessage.value = null
    }
}