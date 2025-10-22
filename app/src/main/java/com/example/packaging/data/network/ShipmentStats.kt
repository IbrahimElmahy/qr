package com.example.packaging.data.network

import com.google.gson.annotations.SerializedName

data class ShipmentStats(
    val barcode: String,
    @SerializedName("company_name") val companyName: String,
    @SerializedName("scan_count") val scanCount: Int,
    @SerializedName("first_scan") val firstScan: String?,
    @SerializedName("last_scan") val lastScan: String?
)
