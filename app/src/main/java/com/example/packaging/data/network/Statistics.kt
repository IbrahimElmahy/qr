package com.example.packaging.data.network

import com.google.gson.annotations.SerializedName

data class Statistics(
    @SerializedName("total_unique_shipments") val totalUniqueShipments: Int,
    @SerializedName("duplicate_count") val duplicateCount: Int,
    @SerializedName("total_scans") val totalScans: Int
)
