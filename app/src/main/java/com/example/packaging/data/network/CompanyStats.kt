package com.example.packaging.data.network

import com.google.gson.annotations.SerializedName

data class CompanyStats(
    val id: Int,
    val name: String,
    @SerializedName("unique_shipments") val uniqueShipments: Int,
    @SerializedName("total_scans") val totalScans: Int
)
