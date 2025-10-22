package com.example.packaging.ui.stats

import android.app.DatePickerDialog
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ArrayAdapter
import androidx.fragment.app.Fragment
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import com.example.packaging.data.CompanyEntity
import com.example.packaging.data.Shipment
import com.example.packaging.data.network.StatisticsFilters
import com.example.packaging.data.network.StatisticsResponse
import com.example.packaging.databinding.FragmentDailyStatisticsBinding
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
        binding.dateButton.setOnClickListener { showDatePicker() }
        binding.retryButton.setOnClickListener { viewModel.loadStatistics() }
        binding.refreshButton.setOnClickListener { viewModel.loadStatistics() }
        binding.companySpinner.onItemSelectedListener = object : android.widget.AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: android.widget.AdapterView<*>?, view: View?, position: Int, id: Long) {
                if (position == 0) {
                    viewModel.setSelectedCompany(null)
                    binding.selectedCompanyText.text = "جميع الشركات"
                } else if (position > 0 && position <= companies.size) {
                    viewModel.setSelectedCompany(companies[position - 1])
                    binding.selectedCompanyText.text = companies[position - 1].name
                }
            }
            override fun onNothingSelected(parent: android.widget.AdapterView<*>?) {}
        }
    }

    private fun observeViewModel() {
        viewModel.selectedDate.observe(viewLifecycleOwner) { date ->
            binding.dateButton.text = date
        }

        viewModel.statistics.observe(viewLifecycleOwner) { stats ->
            if (stats != null) {
                updateStatisticsDisplay(stats)
                binding.errorTextview.visibility = View.GONE
                binding.retryButton.visibility = View.GONE
            }
        }

        viewModel.activeFilters.observe(viewLifecycleOwner) { filters ->
            updateFilterSummary(filters)
        }

        viewModel.isLoading.observe(viewLifecycleOwner) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.retryButton.isEnabled = !isLoading
        }

        viewModel.errorMessage.observe(viewLifecycleOwner) { error ->
            if (error != null) {
                binding.errorTextview.text = error
                binding.errorTextview.visibility = View.VISIBLE
                binding.retryButton.visibility = View.VISIBLE
            } else {
                binding.errorTextview.visibility = View.GONE
                binding.retryButton.visibility = View.GONE
            }
        }

        viewModel.activeCompanies.observe(viewLifecycleOwner) { companiesList ->
            companies = companiesList
            val companyNames = listOf("جميع الشركات") + companiesList.map { it.name }
            val adapter = ArrayAdapter(requireContext(), android.R.layout.simple_spinner_item, companyNames)
            adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
            binding.companySpinner.adapter = adapter

            // Debug logging
            android.util.Log.d("DailyStatistics", "🔍 Companies loaded: ${companiesList.size}")
            companiesList.forEach { company ->
                android.util.Log.d("DailyStatistics", "🔍 Company: ${company.name} (ID: ${company.id})")
            }

            viewModel.activeFilters.value?.let { updateFilterSummary(it) }
        }
    }

    private fun loadCompanies() {
        // تحميل الشركات من API مباشرة
        android.util.Log.d("DailyStatistics", "🔍 Starting to load companies...")
        viewModel.loadCompanies()
        
        // تحميل إضافي من قاعدة البيانات المحلية
        viewModel.activeCompanies.observe(viewLifecycleOwner) { companiesList ->
            android.util.Log.d("DailyStatistics", "🔍 Companies observed: ${companiesList.size}")
            if (companiesList.isNotEmpty()) {
                companies = companiesList
                val companyNames = listOf("جميع الشركات") + companiesList.map { it.name }
                val adapter = ArrayAdapter(requireContext(), android.R.layout.simple_spinner_item, companyNames)
                adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                binding.companySpinner.adapter = adapter
                
                android.util.Log.d("DailyStatistics", "🔍 Spinner updated with ${companyNames.size} items")
            } else {
                android.util.Log.w("DailyStatistics", "🔍 No companies found, trying to load from API...")
                // محاولة تحميل من API إذا لم توجد شركات محلياً
                viewModel.loadCompanies()
            }
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
    }

    private fun updateFilterSummary(filters: StatisticsFilters?) {
        val dateText = when {
            filters?.date?.isNotBlank() == true -> filters.date
            filters?.startDate != null || filters?.endDate != null -> {
                val start = filters?.startDate ?: "غير محدد"
                val end = filters?.endDate ?: "غير محدد"
                "$start → $end"
            }
            else -> viewModel.selectedDate.value ?: binding.dateButton.text.toString()
        }
        binding.dateButton.text = dateText

        val companyLabel = filters?.companyId?.let { id ->
            val companyName = companies.find { it.id == id }?.name
            companyName ?: "معرف الشركة: $id"
        } ?: "جميع الشركات"
        binding.selectedCompanyText.text = "الشركة: $companyLabel"

        if (binding.companySpinner.adapter != null) {
            filters?.companyId?.let { id ->
                val index = companies.indexOfFirst { it.id == id }
                if (index >= 0 && binding.companySpinner.selectedItemPosition != index + 1) {
                    binding.companySpinner.setSelection(index + 1)
                }
            } ?: run {
                if (binding.companySpinner.selectedItemPosition != 0) {
                    binding.companySpinner.setSelection(0)
                }
            }
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}