package com.example.packaging.data

import androidx.lifecycle.LiveData
import androidx.room.*
import kotlinx.coroutines.flow.Flow
import java.util.Date

@Dao
interface ShipmentDao {

    @Insert
    suspend fun insert(shipment: Shipment)

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertShipments(shipments: List<Shipment>)

    @Query("SELECT * FROM shipments ORDER BY scanDate DESC")
    fun getAllShipments(): LiveData<List<Shipment>>

    @Query("SELECT * FROM shipments WHERE isSynced = 0")
    suspend fun getUnsyncedShipments(): List<Shipment>

    @Query("SELECT * FROM shipments WHERE companyId = :companyId ORDER BY scanDate DESC")
    fun getShipmentsByCompany(companyId: Int): Flow<List<Shipment>>

    @Query("SELECT * FROM shipments WHERE scanDate = :date ORDER BY scanDate DESC")
    fun getShipmentsByDate(date: Date): Flow<List<Shipment>>

    @Query("SELECT * FROM shipments WHERE barcode LIKE '%' || :barcode || '%' ORDER BY scanDate DESC")
    fun searchShipments(barcode: String): Flow<List<Shipment>>

    @Query("SELECT COUNT(*) FROM shipments WHERE companyId = :companyId AND scanDate = :date")
    suspend fun getShipmentCountByCompanyAndDate(companyId: Int, date: Date): Int

    @Query("SELECT COUNT(DISTINCT barcode) FROM shipments WHERE companyId = :companyId AND scanDate = :date")
    suspend fun getUniqueShipmentCountByCompanyAndDate(companyId: Int, date: Date): Int

    @Update
    suspend fun updateShipment(shipment: Shipment)

    @Query("UPDATE shipments SET isSynced = 1 WHERE id = :id")
    suspend fun markAsSynced(id: Int)

    @Query("UPDATE shipments SET isSynced = 1 WHERE id IN (:ids)")
    suspend fun markAsSynced(ids: List<Int>)

    @Delete
    suspend fun deleteShipment(shipment: Shipment)

    @Query("DELETE FROM shipments")
    suspend fun deleteAll()
}