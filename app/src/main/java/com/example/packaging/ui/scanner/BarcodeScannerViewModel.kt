package com.example.packaging.ui.scanner

import android.app.Application
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import com.example.packaging.R
import com.example.packaging.data.CompanyEntity
import com.example.packaging.data.Repository
import kotlinx.coroutines.launch

class BarcodeScannerViewModel(application: Application) : AndroidViewModel(application) {

    private val repository = Repository(application)
    private val app = getApplication<Application>()

    private val _selectedCompany = MutableLiveData<CompanyEntity?>()
    val selectedCompany: LiveData<CompanyEntity?> = _selectedCompany

    private val _scanResult = MutableLiveData<String?>()
    val scanResult: LiveData<String?> = _scanResult

    private val _isLoading = MutableLiveData<Boolean>(false)
    val isLoading: LiveData<Boolean> = _isLoading

    private val _errorMessage = MutableLiveData<String?>()
    val errorMessage: LiveData<String?> = _errorMessage

    private val _scannedShipments = MutableLiveData<List<ScannedShipmentItem>>(emptyList())
    val scannedShipments: LiveData<List<ScannedShipmentItem>> = _scannedShipments

    val activeCompanies = repository.getActiveCompanies()

    fun setSelectedCompany(company: CompanyEntity) {
        _selectedCompany.value = company
    }

    fun handleScannedBarcode(rawBarcode: String) {
        val company = _selectedCompany.value
        if (company == null) {
            _errorMessage.value = "يرجى اختيار شركة الشحن أولاً"
            return
        }

        val barcode = rawBarcode.trim()
        if (barcode.isEmpty()) {
            _errorMessage.value = "لم يتم التعرف على الباركود"
            return
        }

        val currentItems = _scannedShipments.value.orEmpty().toMutableList()
        val index = currentItems.indexOfFirst { it.barcode == barcode && it.companyId == company.id }
        if (index >= 0) {
            val updated = currentItems[index].copy(
                count = currentItems[index].count + 1,
                lastScannedAt = System.currentTimeMillis()
            )
            currentItems[index] = updated
            _scanResult.value = "تم تحديث الكمية للباركود $barcode"
        } else {
            currentItems.add(
                ScannedShipmentItem(
                    barcode = barcode,
                    companyId = company.id,
                    companyName = company.name,
                    count = 1
                )
            )
            _scanResult.value = "تم إضافة الباركود $barcode"
        }
        _scannedShipments.value = currentItems
    }

    fun submitShipments() {
        val pendingShipments = _scannedShipments.value.orEmpty()
        if (pendingShipments.isEmpty()) {
            _errorMessage.value = app.getString(R.string.scanner_no_shipments_error)
            return
        }

        _isLoading.value = true
        viewModelScope.launch {
            val remaining = mutableListOf<ScannedShipmentItem>()
            var successCount = 0
            val failures = mutableListOf<String>()

            try {
                pendingShipments.forEach { item ->
                    var failedCount = 0
                    repeat(item.count) {
                        val result = repository.addShipment(item.barcode, item.companyId, item.companyName)
                        result.fold(
                            onSuccess = {
                                successCount++
                            },
                            onFailure = { error ->
                                failedCount++
                                failures.add(error.message ?: "خطأ غير معروف")
                            }
                        )
                    }

                    if (failedCount > 0) {
                        remaining.add(
                            item.copy(
                                count = failedCount,
                                lastScannedAt = System.currentTimeMillis()
                            )
                        )
                    }
                }

                if (failures.isEmpty()) {
                    _scannedShipments.value = emptyList()
                    _scanResult.value = "تم إرسال $successCount شحنة بنجاح"
                } else {
                    _scannedShipments.value = remaining
                    _errorMessage.value = "لم يتم إرسال ${remaining.sumOf { it.count }} شحنة. حاول مرة أخرى"
                }
            } catch (e: Exception) {
                _errorMessage.value = "حدث خطأ أثناء الإرسال: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    fun clearScannedShipments() {
        _scannedShipments.value = emptyList()
        _scanResult.value = "تم إفراغ قائمة الشحنات"
    }

    fun refreshCompanies() {
        _isLoading.value = true
        viewModelScope.launch {
            try {
                repository.syncCompanies()
                _scanResult.value = app.getString(R.string.scanner_company_updated)
            } catch (e: Exception) {
                _errorMessage.value = "خطأ في تحديث الشركات: ${e.message}"
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
