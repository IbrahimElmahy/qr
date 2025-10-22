package com.example.packaging.data.network

import com.google.gson.annotations.SerializedName

data class StatisticsResponse(
    val success: Boolean,
    val filters: StatisticsFilters?,
    val statistics: Statistics,
    val shipments: List<ShipmentStats>,
    @SerializedName("company_statistics")
    val companyStatistics: List<CompanyStats>
)

data class StatisticsFilters(
    val date: String?,
    @SerializedName("start_date") val startDate: String?,
    @SerializedName("end_date") val endDate: String?,
    @SerializedName("company_id") val companyId: Int?
)
