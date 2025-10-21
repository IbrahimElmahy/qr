package com.example.packaging.ui.scanner

import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

data class ScannedShipmentItem(
    val barcode: String,
    val companyId: Int,
    val companyName: String,
    val count: Int = 1,
    val lastScannedAt: Long = System.currentTimeMillis()
) {
    fun formattedScanTime(): String {
        val formatter = SimpleDateFormat("yyyy-MM-dd HH:mm", Locale.getDefault())
        return formatter.format(Date(lastScannedAt))
    }
}
