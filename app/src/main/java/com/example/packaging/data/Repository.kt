package com.example.packaging.data

import android.content.Context
import androidx.lifecycle.LiveData
import androidx.lifecycle.asLiveData
import com.example.packaging.data.network.AddCompanyRequest
import com.example.packaging.data.network.AddShipmentRequest
import com.example.packaging.data.network.RetrofitInstance
import com.example.packaging.data.network.StatisticsResponse
import com.example.packaging.data.network.ToggleCompanyRequest
import java.util.Date

class Repository(context: Context) {
    private val database = AppDatabase.getDatabase(context)
    private val shipmentDao = database.shipmentDao()
    private val companyDao = database.companyDao()
    private val apiService = RetrofitInstance.api

    // إدارة الشركات
    fun getCompanies(): LiveData<List<CompanyEntity>> = companyDao.getAllCompanies().asLiveData()
    fun getActiveCompanies(): LiveData<List<CompanyEntity>> = companyDao.getActiveCompanies().asLiveData()
    suspend fun getCompanyById(id: Int): CompanyEntity? = companyDao.getCompanyById(id)
    
    suspend fun syncCompanies() {
        try {
            val response = apiService.getCompanies()
            if (response.isSuccessful && response.body()?.success == true) {
                val companies = response.body()?.data ?: emptyList()
                val companyEntities = companies.map { company ->
                    CompanyEntity(
                        id = company.id,
                        name = company.name,
                        isActive = company.isActive
                    )
                }
                companyDao.insertCompanies(companyEntities)
            }
        } catch (e: Exception) {
            e.printStackTrace()
        }
    }

    suspend fun loadCompaniesFromAPI() {
        try {
            android.util.Log.d("Repository", "🔍 Loading companies from API...")
            val response = apiService.getCompanies()
            android.util.Log.d("Repository", "🔍 API Response: ${response.code()}")
            
            if (response.isSuccessful) {
                val responseBody = response.body()
                android.util.Log.d("Repository", "🔍 Response body: $responseBody")
                
                if (responseBody?.success == true) {
                    val companies = responseBody.data ?: emptyList()
                    android.util.Log.d("Repository", "🔍 Companies count: ${companies.size}")
                    
                    if (companies.isNotEmpty()) {
                        val companyEntities = companies.map { company ->
                            CompanyEntity(
                                id = company.id,
                                name = company.name,
                                isActive = company.isActive
                            )
                        }
                        companyDao.insertCompanies(companyEntities)
                        android.util.Log.d("Repository", "🔍 Companies saved to database: ${companyEntities.size}")
                    } else {
                        android.util.Log.w("Repository", "🔍 No companies returned from API")
                    }
                } else {
                    android.util.Log.e("Repository", "🔍 API returned success=false: ${responseBody?.message}")
                }
            } else {
                android.util.Log.e("Repository", "🔍 API call failed: ${response.code()}")
                android.util.Log.e("Repository", "🔍 Error body: ${response.errorBody()?.string()}")
            }
        } catch (e: Exception) {
            android.util.Log.e("Repository", "🔍 Error loading companies: ${e.message}")
            e.printStackTrace()
        }
    }

    suspend fun addCompany(name: String): Result<Company> {
        return try {
            val response = apiService.addCompany(AddCompanyRequest(name))
            if (response.isSuccessful && response.body()?.success == true) {
                val company = response.body()?.data!!
                companyDao.insertCompany(CompanyEntity(
                    id = company.id,
                    name = company.name,
                    isActive = company.isActive
                ))
                Result.success(company)
            } else {
                Result.failure(Exception(response.body()?.error ?: "Unknown error"))
            }
        } catch (e: Exception) {
            e.printStackTrace()
            Result.failure(e)
        }
    }

    suspend fun toggleCompany(companyId: Int): Result<Company> {
        return try {
            val response = apiService.toggleCompany(ToggleCompanyRequest(companyId))
            if (response.isSuccessful && response.body()?.success == true) {
                val company = response.body()?.data!!
                companyDao.updateCompany(CompanyEntity(
                    id = company.id,
                    name = company.name,
                    isActive = company.isActive
                ))
                Result.success(company)
            } else {
                Result.failure(Exception(response.body()?.error ?: "Unknown error"))
            }
        } catch (e: Exception) {
            e.printStackTrace()
            Result.failure(e)
        }
    }

    // إدارة الشحنات
    suspend fun addShipment(barcode: String, companyId: Int, companyName: String): Result<Shipment> {
        return try {
            val shipment = Shipment(
                barcode = barcode,
                companyId = companyId,
                companyName = companyName,
                scanDate = Date()
            )
            
            // حفظ محلياً أولاً
            shipmentDao.insert(shipment)
            
            // إرسال للسيرفر
            val response = apiService.addShipment(
                AddShipmentRequest(
                    barcode = barcode,
                    company_id = companyId,
                    scan_date = null
                )
            )
            
            if (response.isSuccessful && response.body()?.success == true) {
                val syncedShipment = response.body()?.data!!
                shipmentDao.markAsSynced(shipment.id)
                Result.success(syncedShipment)
            } else {
                Result.success(shipment) // إرجاع الشحنة المحلية حتى لو فشل الإرسال
            }
        } catch (e: Exception) {
            // في حالة فشل الاتصال، نحفظ الشحنة محلياً
            e.printStackTrace()
            try {
                val shipment = Shipment(
                    barcode = barcode,
                    companyId = companyId,
                    companyName = companyName,
                    scanDate = Date()
                )
                shipmentDao.insert(shipment)
                Result.success(shipment)
            } catch (dbException: Exception) {
                dbException.printStackTrace()
                Result.failure(dbException)
            }
        }
    }

    fun getAllShipments() = shipmentDao.getAllShipments()
    fun getShipmentsByCompany(companyId: Int) = shipmentDao.getShipmentsByCompany(companyId)
    fun getShipmentsByDate(date: Date) = shipmentDao.getShipmentsByDate(date)
    fun searchShipments(barcode: String) = shipmentDao.searchShipments(barcode)

    suspend fun getShipmentCountByCompanyAndDate(companyId: Int, date: Date): Int =
        shipmentDao.getShipmentCountByCompanyAndDate(companyId, date)

    suspend fun getUniqueShipmentCountByCompanyAndDate(companyId: Int, date: Date): Int =
        shipmentDao.getUniqueShipmentCountByCompanyAndDate(companyId, date)

    // مزامنة الشحنات غير المرسلة
    suspend fun syncUnsyncedShipments() {
        try {
            val unsyncedShipments = shipmentDao.getUnsyncedShipments()
            for (shipment in unsyncedShipments) {
                val response = apiService.addShipment(AddShipmentRequest(
                    barcode = shipment.barcode,
                    company_id = shipment.companyId,
                    scan_date = null
                ))
                
                if (response.isSuccessful && response.body()?.success == true) {
                    shipmentDao.markAsSynced(shipment.id)
                }
            }
        } catch (e: Exception) {
            e.printStackTrace()
        }
    }

    // الإحصائيات
    suspend fun getStatistics(
        companyId: Int? = null,
        date: String? = null,
        startDate: String? = null,
        endDate: String? = null
    ): Result<StatisticsResponse> {
        return try {
            val response = apiService.getStatistics(
                companyId = companyId,
                date = date,
                startDate = startDate,
                endDate = endDate
            )
            if (response.isSuccessful) {
                val stats = response.body()
                if (stats != null) {
                    Result.success(stats)
                } else {
                    Result.failure(Exception("Response body is null"))
                }
            } else {
                Result.failure(Exception("Network request failed with code ${response.code()}: ${response.message()}"))
            }
        } catch (e: Exception) {
            e.printStackTrace()
            Result.failure(e)
        }
    }
}