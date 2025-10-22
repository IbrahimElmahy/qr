package com.example.packaging.data.network

import com.example.packaging.data.Company
import com.example.packaging.data.Shipment
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
    suspend fun getStatistics(
        @Query("company_id") companyId: Int? = null,
        @Query("date") date: String? = null,
        @Query("start_date") startDate: String? = null,
        @Query("end_date") endDate: String? = null
    ): Response<StatisticsResponse>
}
