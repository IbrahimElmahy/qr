package com.example.packaging.ui.stats

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.packaging.R
import com.example.packaging.data.Shipment

class ShipmentAdapter : RecyclerView.Adapter<ShipmentAdapter.ShipmentViewHolder>() {

    private var shipments = emptyList<Pair<String, Int>>()

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ShipmentViewHolder {
        val itemView = LayoutInflater.from(parent.context).inflate(R.layout.shipment_list_item, parent, false)
        return ShipmentViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: ShipmentViewHolder, position: Int) {
        val currentItem = shipments[position]
        holder.barcodeTextView.text = currentItem.first
        holder.scanCountTextView.text = currentItem.second.toString()
    }

    override fun getItemCount() = shipments.size

    fun setData(shipments: List<Shipment>) {
        val shipmentCounts = shipments.groupingBy { it.barcode }.eachCount().toList()
        this.shipments = shipmentCounts
        notifyDataSetChanged()
    }

    class ShipmentViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        val barcodeTextView: TextView = itemView.findViewById(R.id.barcode_textview)
        val scanCountTextView: TextView = itemView.findViewById(R.id.scan_count_textview)
    }
}