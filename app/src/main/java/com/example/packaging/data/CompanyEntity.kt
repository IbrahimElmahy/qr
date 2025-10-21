package com.example.packaging.data

import androidx.room.Entity
import androidx.room.PrimaryKey

// كيان الشركة في قاعدة البيانات المحلية
@Entity(tableName = "companies")
data class CompanyEntity(
    @PrimaryKey
    val id: Int,
    val name: String,
    val isActive: Boolean,
    val lastSync: Long = System.currentTimeMillis()
)
