@extends('layout.admin')

@section('head')
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Modern Switch */
    .form-switch .form-check-input {
        width: 2.2em;
        height: 1.1em;
        cursor: pointer;
    }

    .form-switch .form-check-input:checked {
        background-color: #764ba2;
        border-color: #764ba2;
    }
</style>
@endsection

@section('content')
<div class="container-fluid mt-5 pt-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h4 class="mb-1 fw-bold text-dark">Product List</h4>
            <p class="text-muted mb-0 small">Manage your microfinance products</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('product.create') }}" class="btn btn-primary-common px-5 rounded-pill shadow-sm fw-bold">
                <i class="bi bi-plus-lg me-2"></i> Create Product
            </a>
        </div>
    </div>

    <!-- <div class="card "> -->
    <div class="card-body p-4">
        <table id="product-table" class="table table-hover table-modern align-middle w-100 table-borderless text-nowrap">
            <thead class="bg-light text-muted">
                <tr class="text-uppercase small fw-bold">
                    <th>No</th>
                    <th>Product Name</th>
                    <th>Code</th>
                    <th>Interest Method</th>
                    <th>Period Type</th>
                    <th>Sub Products</th>
                    <th>Charges</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <!-- </div> -->
</div>

<!-- View Product Modal -->
<div class="modal fade" id="viewProductModal" tabindex="-1" aria-labelledby="viewProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 bg-white pb-3 pt-4 px-4">
                <div class="w-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="modal-title fw-bold text-dark mb-1" id="viewProductModalLabel">Product Details</h4>
                            <p class="text-muted small mb-0">Comprehensive overview of the selected product</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
            </div>

            <div class="modal-body p-0 bg-light-subtle">
                <!-- Product Header Info -->
                <div class="bg-white px-4 pb-4 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-shape icon-lg bg-primary-subtle text-primary rounded-4 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-box-seam fs-3"></i>
                        </div>
                        <div>
                            <h3 id="view-product-name" class="fw-bold text-dark mb-1">-</h3>
                            <div class="d-flex align-items-center gap-3 text-muted small">
                                <span class="d-flex align-items-center gap-1"><i class="bi bi-upc-scan"></i> <span id="view-product-code">-</span></span>
                                <span id="view-status">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <!-- General Info -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase text-muted fw-bold mb-4 small border-bottom pb-2">General Policies</h6>
                            <div class="row g-4">
                                <div class="col-md-3">
                                    <label class="small text-muted fw-semibold d-block mb-1">Interest Method</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-graph-up-arrow text-primary"></i>
                                        <span id="view-interest-method" class="text-dark fw-bold">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Charges -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-shape bg-warning-subtle text-warning rounded-circle me-2 p-2"><i class="bi bi-currency-dollar"></i></div>
                                <h6 class="fw-bold text-dark mb-0">Additional Charges</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="small text-uppercase fw-semibold ps-3">Description</th>
                                            <th class="small text-uppercase fw-semibold">Amount / Rate</th>
                                            <th class="small text-uppercase fw-semibold">Type</th>
                                            <th class="small text-uppercase fw-semibold pe-3 text-end">Deduction</th>
                                        </tr>
                                    </thead>
                                    <tbody id="view-charges-list">
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">No charges found.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-shape bg-success-subtle text-success rounded-circle me-2 p-2"><i class="bi bi-file-earmark-text"></i></div>
                                <h6 class="fw-bold text-dark mb-0">Required Documents</h6>
                            </div>
                            <ul class="list-group list-group-flush small" id="view-docs-list">
                                <li class="list-group-item text-muted text-center py-2 border-0">No documents required.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Configuration / Sub Products -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-shape bg-primary-subtle text-primary rounded-circle me-2 p-2"><i class="bi bi-layers-fill"></i></div>
                                <h6 class="fw-bold text-dark mb-0">Sub Products (Configuration)</h6>
                            </div>
                            <div class="table-responsive rounded-3 border">
                                <table id="view-items-table" class="table table-hover align-middle mb-0 text-nowrap table-striped">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="small text-uppercase fw-semibold ps-4 py-3">Label</th>
                                            <th class="small text-uppercase fw-semibold py-3">Loan Amount</th>
                                            <th class="small text-uppercase fw-semibold py-3">Period</th>
                                            <th class="small text-uppercase fw-semibold py-3">Interest</th>
                                            <th class="small text-uppercase fw-semibold py-3">Penalty</th>
                                            <th class="small text-uppercase fw-semibold pe-4 py-3">Savings</th>
                                        </tr>
                                    </thead>
                                    <tbody id="view-items-body" class="small bg-white">
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-5">No items found.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <div class="modal-footer border-0 bg-white pt-3 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ asset('JS/common.js') }}"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#product-table').DataTable({
            processing: true,
            serverSide: true,
            scrollX: false,
            ajax: "{{ route('product.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'product_name',
                    name: 'product_name'
                },
                {
                    data: 'product_code',
                    name: 'product_code'
                },
                {
                    data: 'interest_method',
                    name: 'interest_method'
                },
                {
                    data: 'loan_period_type',
                    name: 'loan_period_type'
                },
                {
                    data: 'items_list',
                    name: 'items_list',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'charges_list',
                    name: 'charges_list',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-end'
                },
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search products..."
            },
            dom: '<"d-flex justify-content-between align-items-center mb-3"Bf><"table-responsive"t><"d-flex justify-content-between align-items-center mt-3"ip>',
            buttons: []
        });

        // Status Toggle
        $(document).on('change', '.status-toggle', function() {
            var status = $(this).prop('checked') ? 'active' : 'inactive';
            var id = $(this).data('id');

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('product.status_update') }}",
                data: {
                    'status': status,
                    'id': id
                },
                success: function(data) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.success,
                        showConfirmButton: false,
                        timer: 3000
                    });
                },
                error: function(data) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong!'
                    });
                    $(this).prop('checked', !$(this).prop('checked'));
                }
            });
        });

        // View Product
        $(document).on('click', '.js-view-product', function() {
            let id = $(this).data('id');
            // Use Bootstrap 5 Vanilla JS to avoid jQuery conflict
            let modalElement = document.getElementById('viewProductModal');
            let modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(modalElement);
            }
            modalInstance.show();

            // Reset fields
            $('#view-product-name').text('Loading...');

            $.ajax({
                url: '/product/get-details/' + id,
                type: 'GET',
                success: function(res) {
                    let p = res.product;
                    console.log(p);

                    // General
                    $('#view-product-name').text(p.product_name);
                    $('#view-product-code').text(p.product_code);
                    $('#view-status').html(p.status === 'active' ?
                        '<span class="badge bg-success">Active</span>' :
                        '<span class="badge bg-danger">Inactive</span>');

                    $('#view-interest-method').text(formatText(p.interest_method));
                    $('#view-min-loan').text(parseFloat(p.minimum_loan_amount).toLocaleString('en-US', {
                        minimumFractionDigits: 2
                    }));
                    $('#view-max-loan').text(parseFloat(p.maximum_loan_amount).toLocaleString('en-US', {
                        minimumFractionDigits: 2
                    }));

                    // Items
                    let itemsHtml = '';
                    if (p.product_has_items && p.product_has_items.length > 0) {
                        // Create table header first
                        $('#view-items-table thead').html(`
                            <tr class="text-secondary text-uppercase small fw-bolder" style="font-size: 0.75rem; letter-spacing: 0.05em; background-color: #f8f9fa;">
                                <th class="py-3 ps-4">Label</th>
                                <th class="py-3">Loan Amount</th>
                                <th class="py-3">Period</th>
                                <th class="py-3">Interest</th>
                                <th class="py-3">Penalty</th>
                                <th class="py-3 pe-4">Savings</th>
                            </tr>
                        `);

                        p.product_has_items.forEach(item => {
                            // Formatting values
                            let minLoan = parseFloat(item.minimum_loan_amount || 0).toLocaleString('en-US', {
                                minimumFractionDigits: 2
                            });
                            let maxLoan = parseFloat(item.maximum_loan_amount || 0).toLocaleString('en-US', {
                                minimumFractionDigits: 2
                            });
                            let minInt = parseFloat(item.minimum_interest || 0);
                            let maxInt = parseFloat(item.maximum_interest || 0);

                            // Penalty Logic (Simplified for View)
                            let penaltyTxt = '-';
                            if (item.penalty_percentage && item.penalty_percentage > 0) {
                                let methodDesc = item.penalty_method === 'every_installment' ? 'APPLY PENALTY FOR EVERY INSTALLMENT' : 'APPLY PENALTY FOR LOAN AFTER MATURITY';
                                penaltyTxt = `${item.penalty_percentage}% (${methodDesc}) after ${item.penalty_start_after_days} ${item.penalty_apply_type}`;
                            }

                            // Savings Logic
                            let savingTxt = '-';
                            if (item.saving_amount) {
                                let amt = parseFloat(item.saving_amount).toFixed(2);
                                let rate = item.saving_interest_rate ? item.saving_interest_rate + '%' : '-';
                                savingTxt = `<span class="d-block small fw-bold text-dark">${amt}</span>
                                             <small class="text-muted d-block" style="font-size: 0.7rem;">${rate} Int.</small>`;
                            }


                            itemsHtml += `
                                <tr class="border-bottm">
                                    <td class="align-middle ps-4 py-3">
                                        <span class="d-block text-dark fw-bold h6 mb-0 text-uppercase">${item.product_item_name}</span>
                                    </td>
                                    <td class="align-middle py-3">
                                        <span class="text-dark">${minLoan} - ${maxLoan}</span>
                                    </td>
                                    <td class="align-middle py-3">
                                        <div class="d-flex flex-column">
                                            <span>${item.minimum_loan_period} - ${item.maximum_loan_period} ${formatText(p.loan_period_type)}</span>
                                            ${item.minimum_collection_period ? `<small class='text-muted'>Coll: ${item.minimum_collection_period}-${item.maximum_collection_period}</small>` : ''}
                                        </div>
                                    </td>
                                    <td class="align-middle py-3">
                                        <span>${minInt} - ${maxInt}% ${formatText(p.interest_period_type)}</span>
                                    </td>
                                    <td class="align-middle py-3">
                                         <span class="small text-uppercase" style="font-size: 0.75rem;">${penaltyTxt}</span>
                                         <div class="small text-muted mt-1">${item.required_guarantee_count || 0} Guarantors</div>
                                    </td>
                                    <td class="align-middle pe-4 py-3">
                                        ${savingTxt}
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        $('#view-items-table thead').html(''); // Clear header if no items
                        itemsHtml = '<tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-box-seam display-6 d-block mb-3 opacity-25"></i>No items configured.</td></tr>';
                    }
                    $('#view-items-body').html(itemsHtml);

                    // Charges
                    let chargesHtml = '';
                    if (p.additional_charges && p.additional_charges.length > 0) {
                        p.additional_charges.forEach(op => {
                            let valueDisplay = parseFloat(op.value).toLocaleString('en-US', {
                                minimumFractionDigits: 2
                            });
                            let typeDisplay = op.value_type === 'percentage' ? 'Percentage (%)' : 'Fixed Amount';
                            let deductionDisplay = formatText(op.deduction_type || '-');
                            if (op.value_type === 'percentage') valueDisplay += '%';

                            chargesHtml += `
                                <tr>
                                    <td class="ps-3 py-3">
                                        <span class="fw-bold text-dark">${op.description}</span>
                                    </td>
                                    <td class="py-3">
                                        <span class="fw-bold text-primary">${valueDisplay}</span>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-light text-dark border">${typeDisplay}</span>
                                    </td>
                                    <td class="pe-3 py-3 text-end">
                                        <span class="text-muted small">${deductionDisplay}</span>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        chargesHtml = '<tr><td colspan="4" class="text-center text-muted py-3">No charges found.</td></tr>';
                    }
                    $('#view-charges-list').html(chargesHtml);

                    // Docs
                    let docsHtml = '';
                    if (p.required_documents && p.required_documents.length > 0) {
                        p.required_documents.forEach(d => {
                            docsHtml += `
                                <li class="list-group-item px-0">
                                    <i class="bi bi-check2-circle text-success me-2"></i>${d.name}
                                </li>
                            `;
                        });
                    } else {
                        docsHtml = '<li class="list-group-item text-muted text-center">No documents required.</li>';
                    }
                    $('#view-docs-list').html(docsHtml);

                },
                error: function() {
                    Swal.fire('Error', 'Failed to fetch product details.', 'error');
                }
            });
        });

        function formatText(text) {
            if (!text) return '';
            return text.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        }
    });
</script>
@endsection