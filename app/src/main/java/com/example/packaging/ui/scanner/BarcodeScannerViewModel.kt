package com.example.packaging.ui.scanner

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import com.example.packaging.data.*
import kotlinx.coroutines.launch

class BarcodeScannerViewModel(application: Application) : AndroidViewModel(application) {

    private val repository = Repository(application)
    
    private val _selectedCompany = MutableLiveData<CompanyEntity?>()
    val selectedCompany: LiveData<CompanyEntity?> = _selectedCompany
    
    private val _scanResult = MutableLiveData<String>()
    val scanResult: LiveData<String> = _scanResult
    
    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading
    
    private val _errorMessage = MutableLiveData<String>()
    val errorMessage: LiveData<String> = _errorMessage

    val activeCompanies = repository.getActiveCompanies()

    fun setSelectedCompany(company: CompanyEntity) {
        _selectedCompany.value = company
    }

    fun saveShipment(barcode: String) {
        val company = _selectedCompany.value
        if (company == null) {
            _errorMessage.value = "يرجى اختيار شركة الشحن أولاً"
            return
        }

        _isLoading.value = true
        viewModelScope.launch {
            try {
                val result = repository.addShipment(barcode, company.id)
                result.fold(
                    onSuccess = { shipment ->
                        _scanResult.value = "تم حفظ الشحنة: $barcode"
                        _errorMessage.value = null
                    },
                    onFailure = { error ->
                        _errorMessage.value = "خطأ في حفظ الشحنة: ${error.message}"
                    }
                )
            } catch (e: Exception) {
                _errorMessage.value = "خطأ في الاتصال: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun clearMessages() {
        _scanResult.value = null
        _errorMessage.value = null
    }
}