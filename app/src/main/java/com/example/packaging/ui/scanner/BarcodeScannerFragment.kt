package com.example.packaging.ui.scanner

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ArrayAdapter
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.core.view.isVisible
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import com.example.packaging.data.CompanyEntity
import com.example.packaging.databinding.FragmentBarcodeScannerBinding
import com.journeyapps.barcodescanner.ScanContract
import com.journeyapps.barcodescanner.ScanOptions

class BarcodeScannerFragment : Fragment() {

    private var _binding: FragmentBarcodeScannerBinding? = null
    private val binding get() = _binding!!

    private lateinit var viewModel: BarcodeScannerViewModel
    private lateinit var adapter: ScannedShipmentsAdapter
    private var companies: List<CompanyEntity> = emptyList()
    private var currentShipments: List<ScannedShipmentItem> = emptyList()
    private var hasRequestedCompanySync = false
    private var currentIsLoading = false

    private val scanLauncher = registerForActivityResult(ScanContract()) { result ->
        if (result.contents == null) {
            Toast.makeText(requireContext(), "تم إلغاء المسح", Toast.LENGTH_LONG).show()
        } else {
            viewModel.handleScannedBarcode(result.contents)
        }
    }

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentBarcodeScannerBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        viewModel = ViewModelProvider(this).get(BarcodeScannerViewModel::class.java)
        adapter = ScannedShipmentsAdapter()

        setupRecyclerView()
        setupUI()
        observeViewModel()
        loadCompanies()
    }

    private fun setupRecyclerView() {
        binding.scannedShipmentsRecyclerView.layoutManager = LinearLayoutManager(requireContext())
        binding.scannedShipmentsRecyclerView.adapter = adapter
    }

    private fun setupUI() {
        binding.startScanningButton.setOnClickListener {
            if (viewModel.selectedCompany.value == null) {
                Toast.makeText(requireContext(), "يرجى اختيار شركة الشحن أولاً", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            val options = ScanOptions()
            options.setDesiredBarcodeFormats(ScanOptions.ALL_CODE_TYPES)
            options.setPrompt("امسح الباركود")
            options.setCameraId(0)
            options.setBeepEnabled(false)
            options.setBarcodeImageEnabled(true)
            scanLauncher.launch(options)
        }

        binding.syncButton.setOnClickListener {
            viewModel.refreshCompanies()
        }

        binding.sendShipmentsButton.setOnClickListener {
            viewModel.submitShipments()
        }

        binding.clearShipmentsButton.setOnClickListener {
            viewModel.clearScannedShipments()
        }

        binding.companySpinner.onItemSelectedListener = object : android.widget.AdapterView.OnItemSelectedListener {
            override fun onItemSelected(
                parent: android.widget.AdapterView<*>?,
                view: View?,
                position: Int,
                id: Long
            ) {
                if (position in companies.indices) {
                    viewModel.setSelectedCompany(companies[position])
                }
            }

            override fun onNothingSelected(parent: android.widget.AdapterView<*>?) {}
        }
    }

    private fun observeViewModel() {
        viewModel.selectedCompany.observe(viewLifecycleOwner) { company ->
            binding.selectedCompanyText.text = company?.name ?: "لم يتم اختيار شركة"
            company?.let {
                val index = companies.indexOfFirst { it.id == company.id }
                if (index >= 0 && binding.companySpinner.selectedItemPosition != index) {
                    binding.companySpinner.setSelection(index)
                }
            }
        }

        viewModel.scanResult.observe(viewLifecycleOwner) { result ->
            result?.let {
                Toast.makeText(requireContext(), it, Toast.LENGTH_LONG).show()
                viewModel.clearMessages()
            }
        }

        viewModel.errorMessage.observe(viewLifecycleOwner) { error ->
            error?.let {
                Toast.makeText(requireContext(), it, Toast.LENGTH_LONG).show()
                viewModel.clearMessages()
            }
        }

        viewModel.scannedShipments.observe(viewLifecycleOwner) { shipments ->
            adapter.submitList(shipments)
            val total = shipments.sumOf { it.count }
            binding.totalScannedText.text = "إجمالي العناصر: $total"
        }

        viewModel.isLoading.observe(viewLifecycleOwner) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.startScanningButton.isEnabled = !isLoading
            binding.sendShipmentsButton.isEnabled = !isLoading
            binding.syncButton.isEnabled = !isLoading
        }
    }

    private fun loadCompanies() {
        viewModel.activeCompanies.observe(viewLifecycleOwner) { companiesList ->
            companies = companiesList
            val companyNames = companiesList.map { it.name }
            val adapter = ArrayAdapter(requireContext(), android.R.layout.simple_spinner_item, companyNames)
            adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
            binding.companySpinner.adapter = adapter

            val selectedId = viewModel.selectedCompany.value?.id
            if (selectedId != null) {
                val index = companies.indexOfFirst { it.id == selectedId }
                if (index >= 0) {
                    binding.companySpinner.setSelection(index)
                }
            }

            if (companiesList.isEmpty() && !hasRequestedCompanySync) {
                hasRequestedCompanySync = true
                viewModel.refreshCompanies()
            }

            updateScanButtonState()
        }
    }

    private fun updateActionButtons() {
        val hasShipments = currentShipments.isNotEmpty()
        val enableActions = hasShipments && !currentIsLoading
        binding.sendShipmentsButton.isEnabled = enableActions
        binding.clearShipmentsButton.isEnabled = enableActions
    }

    private fun updateScanButtonState() {
        val hasCompanySelected = viewModel.selectedCompany.value != null
        binding.startScanningButton.isEnabled = hasCompanySelected && !currentIsLoading
        binding.selectedCompanyText.text = viewModel.selectedCompany.value?.name
            ?: getString(R.string.scanner_no_company_selected)
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}
