package com.example.packaging.data

import android.content.Context
import kotlinx.coroutines.flow.Flow
import java.util.Date

class Repository(context: Context) {
    private val database = AppDatabase.getDatabase(context)
    private val shipmentDao = database.shipmentDao()
    private val companyDao = database.companyDao()
    private val apiService = RetrofitInstance.api

    // إدارة الشركات
    suspend fun getCompanies(): Flow<List<CompanyEntity>> = companyDao.getAllCompanies()
    suspend fun getActiveCompanies(): Flow<List<CompanyEntity>> = companyDao.getActiveCompanies()
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
            // Handle error
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
            Result.failure(e)
        }
    }

    // إدارة الشحنات
    suspend fun addShipment(barcode: String, companyId: Int): Result<Shipment> {
        return try {
            val shipment = Shipment(
                barcode = barcode,
                companyId = companyId,
                scanDate = Date()
            )
            
            // حفظ محلياً أولاً
            shipmentDao.insert(shipment)
            
            // إرسال للسيرفر
            val response = apiService.addShipment(AddShipmentRequest(
                barcode = barcode,
                company_id = companyId,
                scan_date = null
            ))
            
            if (response.isSuccessful && response.body()?.success == true) {
                val syncedShipment = response.body()?.data!!
                shipmentDao.markAsSynced(shipment.id)
                Result.success(syncedShipment)
            } else {
                Result.success(shipment) // إرجاع الشحنة المحلية حتى لو فشل الإرسال
            }
        } catch (e: Exception) {
            // في حالة فشل الاتصال، نحفظ الشحنة محلياً
            try {
                val shipment = Shipment(
                    barcode = barcode,
                    companyId = companyId,
                    scanDate = Date()
                )
                shipmentDao.insert(shipment)
                Result.success(shipment)
            } catch (dbException: Exception) {
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
            // Handle error
        }
    }

    // الإحصائيات
    suspend fun getStatistics(companyId: Int? = null, date: String? = null): Result<StatisticsResponse> {
        return try {
            val response = apiService.getStats(companyId, date)
            if (response.isSuccessful && response.body()?.success == true) {
                Result.success(response.body()!!)
            } else {
                Result.failure(Exception(response.body()?.error ?: "Unknown error"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }
}
