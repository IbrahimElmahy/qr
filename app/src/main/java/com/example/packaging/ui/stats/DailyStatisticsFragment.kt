package com.example.packaging.ui.stats

import android.app.DatePickerDialog
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ArrayAdapter
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import com.example.packaging.data.CompanyEntity
import com.example.packaging.data.Shipment
import com.example.packaging.data.network.StatisticsResponse
import com.example.packaging.databinding.FragmentDailyStatisticsBinding
import com.example.packaging.ui.stats.ShipmentAdapter
import java.text.SimpleDateFormat
import java.util.*

class DailyStatisticsFragment : Fragment() {

    private var _binding: FragmentDailyStatisticsBinding? = null
    private val binding get() = _binding!!

    private lateinit var viewModel: DailyStatisticsViewModel
    private lateinit var adapter: ShipmentAdapter
    private var companies: List<CompanyEntity> = emptyList()

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentDailyStatisticsBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        viewModel = ViewModelProvider(this).get(DailyStatisticsViewModel::class.java)

        setupRecyclerView()
        setupUI()
        observeViewModel()
        loadCompanies()
    }

    private fun setupRecyclerView() {
        adapter = ShipmentAdapter()
        binding.shipmentsRecyclerview.apply {
            layoutManager = LinearLayoutManager(requireContext())
            this.adapter = this@DailyStatisticsFragment.adapter
        }
    }

    private fun setupUI() {
        binding.dateButton.setOnClickListener {
            showDatePicker()
        }

        binding.companySpinner.onItemSelectedListener = object : android.widget.AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: android.widget.AdapterView<*>?, view: View?, position: Int, id: Long) {
                if (position == 0) {
                    viewModel.setSelectedCompany(null) // جميع الشركات
                } else if (position > 0 && position <= companies.size) {
                    viewModel.setSelectedCompany(companies[position - 1])
                }
            }
            override fun onNothingSelected(parent: android.widget.AdapterView<*>?) {}
        }

        binding.syncButton.setOnClickListener {
            viewModel.syncUnsyncedShipments()
        }

        binding.refreshButton.setOnClickListener {
            viewModel.loadStatistics()
        }
    }

    private fun observeViewModel() {
        viewModel.selectedDate.observe(viewLifecycleOwner) { date ->
            binding.dateButton.text = date
        }

        viewModel.selectedCompany.observe(viewLifecycleOwner) { company ->
            binding.selectedCompanyText.text = company?.name ?: "جميع الشركات"
        }

        viewModel.statistics.observe(viewLifecycleOwner) { stats ->
            stats?.let {
                updateStatisticsDisplay(it)
            }
        }

        viewModel.isLoading.observe(viewLifecycleOwner) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.refreshButton.isEnabled = !isLoading
            binding.syncButton.isEnabled = !isLoading
        }

        viewModel.errorMessage.observe(viewLifecycleOwner) { error ->
            error?.let {
                Toast.makeText(requireContext(), it, Toast.LENGTH_LONG).show()
                viewModel.clearMessages()
            }
        }
    }

    private fun loadCompanies() {
        viewModel.activeCompanies.observe(viewLifecycleOwner) { companiesList ->
            companies = companiesList
            val companyNames = listOf("جميع الشركات") + companiesList.map { it.name }
            val adapter = ArrayAdapter(requireContext(), android.R.layout.simple_spinner_item, companyNames)
            adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
            binding.companySpinner.adapter = adapter
        }
    }

    private fun showDatePicker() {
        val calendar = Calendar.getInstance()
        val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        val currentDate = viewModel.selectedDate.value?.let { dateFormat.parse(it) } ?: Date()
        calendar.time = currentDate

        DatePickerDialog(
            requireContext(),
            { _, year, month, dayOfMonth ->
                val selectedDate = String.format("%04d-%02d-%02d", year, month + 1, dayOfMonth)
                viewModel.setSelectedDate(selectedDate)
            },
            calendar.get(Calendar.YEAR),
            calendar.get(Calendar.MONTH),
            calendar.get(Calendar.DAY_OF_MONTH)
        ).show()
    }

    private fun updateStatisticsDisplay(stats: StatisticsResponse) {
        binding.totalShipmentsTextview.text = "إجمالي الشحنات: ${stats.statistics.totalUniqueShipments}"
        binding.duplicateShipmentsTextview.text = "الشحنات المكررة: ${stats.statistics.duplicateCount}"
        binding.totalScansTextview.text = "إجمالي المسحات: ${stats.statistics.totalScans}"
        
        // تحديث قائمة الشحنات
        adapter.setData(stats.shipments.map { shipmentStats ->
            Shipment(
                barcode = shipmentStats.barcode,
                companyId = 0, // سيتم تحديثه لاحقاً
                companyName = shipmentStats.companyName,
                scanDate = Date(), // سيتم تحديثه لاحقاً
                isSynced = true
            )
        })
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}