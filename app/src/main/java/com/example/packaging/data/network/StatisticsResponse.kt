package com.example.packaging.data.network

data class StatisticsResponse(
    val date: String,
    val company_id: Int?,
    val statistics: Statistics,
    val shipments: List<ShipmentStats>,
    val company_statistics: List<CompanyStats>
)
