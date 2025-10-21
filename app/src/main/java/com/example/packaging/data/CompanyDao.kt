package com.example.packaging.data

import androidx.lifecycle.LiveData
import androidx.room.*
import kotlinx.coroutines.flow.Flow

@Dao
interface CompanyDao {
    @Query("SELECT * FROM companies ORDER BY name")
    fun getAllCompanies(): Flow<List<CompanyEntity>>
    
    @Query("SELECT * FROM companies WHERE isActive = 1 ORDER BY name")
    fun getActiveCompanies(): Flow<List<CompanyEntity>>
    
    @Query("SELECT * FROM companies WHERE id = :id")
    suspend fun getCompanyById(id: Int): CompanyEntity?
    
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertCompany(company: CompanyEntity)
    
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertCompanies(companies: List<CompanyEntity>)
    
    @Update
    suspend fun updateCompany(company: CompanyEntity)
    
    @Delete
    suspend fun deleteCompany(company: CompanyEntity)
    
    @Query("DELETE FROM companies")
    suspend fun deleteAllCompanies()
}
