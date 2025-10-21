package com.example.packaging.ui.shipping

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Switch
import android.widget.TextView
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.example.packaging.R
import com.example.packaging.data.CompanyEntity

class CompaniesAdapter(
    private val onToggleCompany: (CompanyEntity) -> Unit
) : ListAdapter<CompanyEntity, CompaniesAdapter.CompanyViewHolder>(CompanyDiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): CompanyViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_company, parent, false)
        return CompanyViewHolder(view)
    }

    override fun onBindViewHolder(holder: CompanyViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class CompanyViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val companyNameText: TextView = itemView.findViewById(R.id.company_name_text)
        private val companyStatusSwitch: Switch = itemView.findViewById(R.id.company_status_switch)
        private val companyIdText: TextView = itemView.findViewById(R.id.company_id_text)

        fun bind(company: CompanyEntity) {
            companyNameText.text = company.name
            companyIdText.text = "ID: ${company.id}"
            companyStatusSwitch.isChecked = company.isActive
            
            companyStatusSwitch.setOnCheckedChangeListener { _, isChecked ->
                if (isChecked != company.isActive) {
                    onToggleCompany(company)
                }
            }
        }
    }

    class CompanyDiffCallback : DiffUtil.ItemCallback<CompanyEntity>() {
        override fun areItemsTheSame(oldItem: CompanyEntity, newItem: CompanyEntity): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: CompanyEntity, newItem: CompanyEntity): Boolean {
            return oldItem == newItem
        }
    }
}
