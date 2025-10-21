package com.example.packaging.data

import androidx.room.Entity
import androidx.room.PrimaryKey
import java.util.Date

@Entity(tableName = "shipments")
data class Shipment(
    @PrimaryKey(autoGenerate = true)
    val id: Int = 0,
    val barcode: String,
    val companyId: Int,
    val companyName: String = "",
    val scanDate: Date,
    val isSynced: Boolean = false
)

// نموذج للشركة
data class Company(
    val id: Int,
    val name: String,
    val isActive: Boolean,
    val totalShipments: Int = 0,
    val activeDays: Int = 0
)

// نموذج للإحصائيات
data class Statistics(
    val totalUniqueShipments: Int,
    val totalScans: Int,
    val duplicateCount: Int,
    val shipments: List<ShipmentStats>
)

data class ShipmentStats(
    val barcode: String,
    val companyName: String,
    val scanCount: Int,
    val firstScan: String,
    val lastScan: String
)