package com.example.packaging.data.network

import com.example.packaging.data.*
import retrofit2.Response
import retrofit2.http.*

interface ApiService {
    // إدارة الشركات
    @GET("getCompanies.php")
    suspend fun getCompanies(): Response<ApiResponse<List<Company>>>
    
    @POST("addCompany.php")
    suspend fun addCompany(@Body request: AddCompanyRequest): Response<ApiResponse<Company>>
    
    @POST("toggleCompany.php")
    suspend fun toggleCompany(@Body request: ToggleCompanyRequest): Response<ApiResponse<Company>>
    
    // إدارة الشحنات
    @POST("addShipment.php")
    suspend fun addShipment(@Body request: AddShipmentRequest): Response<ApiResponse<Shipment>>
    
    @GET("getShipments.php")
    suspend fun getShipments(
        @Query("company_id") companyId: Int? = null,
        @Query("date") date: String? = null,
        @Query("barcode") barcode: String? = null,
        @Query("page") page: Int = 1,
        @Query("limit") limit: Int = 50
    ): Response<ApiResponse<ShipmentListResponse>>
    
    // الإحصائيات
    @GET("getStats.php")
    suspend fun getStats(
        @Query("company_id") companyId: Int? = null,
        @Query("date") date: String? = null
    ): Response<ApiResponse<StatisticsResponse>>
}

// نماذج الطلبات
data class AddCompanyRequest(val name: String)
data class ToggleCompanyRequest(val company_id: Int)
data class AddShipmentRequest(
    val barcode: String,
    val company_id: Int,
    val scan_date: String? = null
)

// نماذج الاستجابات
data class ApiResponse<T>(
    val success: Boolean,
    val message: String? = null,
    val data: T? = null,
    val error: String? = null
)

data class ShipmentListResponse(
    val pagination: Pagination,
    val filters: Filters,
    val shipments: List<Shipment>
)

data class Pagination(
    val page: Int,
    val limit: Int,
    val total: Int,
    val pages: Int
)

data class Filters(
    val company_id: Int?,
    val date: String?,
    val barcode: String?
)

data class StatisticsResponse(
    val date: String,
    val company_id: Int?,
    val statistics: Statistics,
    val shipments: List<ShipmentStats>,
    val company_statistics: List<CompanyStats>
)

data class Statistics(
    val totalUniqueShipments: Int,
    val duplicateCount: Int,
    val totalScans: Int
)

data class ShipmentStats(
    val barcode: String,
    val companyName: String,
    val scanCount: Int
)

data class CompanyStats(
    val id: Int,
    val name: String,
    val unique_shipments: Int,
    val total_scans: Int
)