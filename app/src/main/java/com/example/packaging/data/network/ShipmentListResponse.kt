package com.example.packaging.data.network

import com.example.packaging.data.Shipment

data class ShipmentListResponse(
    val pagination: Pagination,
    val filters: Filters,
    val shipments: List<Shipment>
)
