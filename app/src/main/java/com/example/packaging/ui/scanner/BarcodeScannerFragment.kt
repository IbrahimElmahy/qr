package com.example.packaging.ui.scanner

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ArrayAdapter
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.fragment.app.Fragment
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.lifecycleScope
import com.example.packaging.R
import com.example.packaging.data.CompanyEntity
import com.example.packaging.databinding.FragmentBarcodeScannerBinding
import com.google.zxing.integration.android.IntentIntegrator
import com.journeyapps.barcodescanner.ScanOptions
import kotlinx.coroutines.launch

class BarcodeScannerFragment : Fragment() {

    private var _binding: FragmentBarcodeScannerBinding? = null
    private val binding get() = _binding!!

    private lateinit var viewModel: BarcodeScannerViewModel
    private var companies: List<CompanyEntity> = emptyList()

    private val scanLauncher = registerForActivityResult(ActivityResultContracts.StartActivityForResult()) {
        val result = IntentIntegrator.parseActivityResult(it.resultCode, it.data)
        if (result.contents == null) {
            Toast.makeText(requireContext(), "تم إلغاء المسح", Toast.LENGTH_LONG).show()
        } else {
            viewModel.saveShipment(result.contents)
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

        setupUI()
        observeViewModel()
        loadCompanies()
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

            val integrator = IntentIntegrator(requireActivity())
            integrator.initiateScan()
        }

        binding.syncButton.setOnClickListener {
            lifecycleScope.launch {
                viewModel.repository.syncCompanies()
                loadCompanies()
            }
        }
    }

    private fun observeViewModel() {
        viewModel.selectedCompany.observe(viewLifecycleOwner) { company ->
            binding.selectedCompanyText.text = company?.name ?: "لم يتم اختيار شركة"
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

        viewModel.isLoading.observe(viewLifecycleOwner) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.startScanningButton.isEnabled = !isLoading
        }
    }

    private fun loadCompanies() {
        viewModel.activeCompanies.observe(viewLifecycleOwner) { companiesList ->
            companies = companiesList
            val companyNames = companiesList.map { it.name }
            val adapter = ArrayAdapter(requireContext(), android.R.layout.simple_spinner_item, companyNames)
            adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
            binding.companySpinner.adapter = adapter

            binding.companySpinner.onItemSelectedListener = object : android.widget.AdapterView.OnItemSelectedListener {
                override fun onItemSelected(parent: android.widget.AdapterView<*>?, view: View?, position: Int, id: Long) {
                    if (position >= 0 && position < companies.size) {
                        viewModel.setSelectedCompany(companies[position])
                    }
                }
                override fun onNothingSelected(parent: android.widget.AdapterView<*>?) {}
            }
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}