package com.example.packaging.data.model

data class StatisticsResponse(
    val success: Boolean,
    val data: StatisticsData?,
    val error: String?
)

data class StatisticsData(
    val totalShipments: Int,
    val totalCompanies: Int,
    val shipmentsToday: Int,
    val shipmentsByCompany: List<CompanyShipments>
)

data class CompanyShipments(
    val companyName: String,
    val shipmentCount: Int
)
