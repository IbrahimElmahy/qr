package com.example.packaging.ui.scanner

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.example.packaging.databinding.ItemScannedShipmentBinding

class ScannedShipmentsAdapter :
    ListAdapter<ScannedShipmentItem, ScannedShipmentsAdapter.ViewHolder>(DiffCallback) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val inflater = LayoutInflater.from(parent.context)
        val binding = ItemScannedShipmentBinding.inflate(inflater, parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    class ViewHolder(private val binding: ItemScannedShipmentBinding) :
        RecyclerView.ViewHolder(binding.root) {

        fun bind(item: ScannedShipmentItem) {
            binding.barcodeTextView.text = item.barcode
            binding.companyNameTextView.text = item.companyName
            binding.countTextView.text = "الكمية: ${item.count}"
            binding.lastScanTextView.text = "آخر مسح: ${item.formattedScanTime()}"
        }
    }

    private object DiffCallback : DiffUtil.ItemCallback<ScannedShipmentItem>() {
        override fun areItemsTheSame(
            oldItem: ScannedShipmentItem,
            newItem: ScannedShipmentItem
        ): Boolean {
            return oldItem.barcode == newItem.barcode && oldItem.companyId == newItem.companyId
        }

        override fun areContentsTheSame(
            oldItem: ScannedShipmentItem,
            newItem: ScannedShipmentItem
        ): Boolean {
            return oldItem == newItem
        }
    }
}
