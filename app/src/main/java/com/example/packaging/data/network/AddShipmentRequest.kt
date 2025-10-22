package com.example.packaging.data.network

data class AddShipmentRequest(
    val barcode: String,
    val company_id: Int,
    val scan_date: String? = null
)
