package com.example.packaging.data.network

data class Pagination(
    val page: Int,
    val limit: Int,
    val total: Int,
    val pages: Int
)
