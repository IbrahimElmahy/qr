package com.example.packaging.ui.shipping

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import com.example.packaging.data.CompanyEntity
import com.example.packaging.databinding.FragmentShippingCompaniesBinding
import com.example.packaging.ui.shipping.CompaniesAdapter

class ShippingCompaniesFragment : Fragment() {

    private var _binding: FragmentShippingCompaniesBinding? = null
    private val binding get() = _binding!!

    private lateinit var viewModel: ShippingCompaniesViewModel
    private lateinit var companiesAdapter: CompaniesAdapter

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentShippingCompaniesBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        viewModel = ViewModelProvider(this).get(ShippingCompaniesViewModel::class.java)

        setupRecyclerView()
        setupUI()
        observeViewModel()
    }

    private fun setupRecyclerView() {
        companiesAdapter = CompaniesAdapter { company ->
            viewModel.toggleCompany(company)
        }
        binding.companiesRecyclerView.apply {
            layoutManager = LinearLayoutManager(requireContext())
            adapter = companiesAdapter
        }
    }

    private fun setupUI() {
        binding.addCompanyButton.setOnClickListener {
            val companyName = binding.companyNameEditText.text.toString().trim()
            if (companyName.isNotEmpty()) {
                viewModel.addCompany(companyName)
                binding.companyNameEditText.text.clear()
            } else {
                Toast.makeText(requireContext(), "يرجى إدخال اسم الشركة", Toast.LENGTH_SHORT).show()
            }
        }

        binding.syncButton.setOnClickListener {
            viewModel.syncCompanies()
        }
    }

    private fun observeViewModel() {
        viewModel.companies.observe(viewLifecycleOwner) { companies ->
            companiesAdapter.submitList(companies)
        }

        viewModel.isLoading.observe(viewLifecycleOwner) { isLoading ->
            binding.progressBar.visibility = if (isLoading) View.VISIBLE else View.GONE
            binding.addCompanyButton.isEnabled = !isLoading
            binding.syncButton.isEnabled = !isLoading
        }

        viewModel.errorMessage.observe(viewLifecycleOwner) { error ->
            error?.let {
                Toast.makeText(requireContext(), it, Toast.LENGTH_LONG).show()
                viewModel.clearMessages()
            }
        }

        viewModel.successMessage.observe(viewLifecycleOwner) { message ->
            message?.let {
                Toast.makeText(requireContext(), it, Toast.LENGTH_SHORT).show()
                viewModel.clearMessages()
            }
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}