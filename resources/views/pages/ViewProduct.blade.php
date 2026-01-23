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
            <div class="modal-header border-bottom-0 bg-white pb-0">
                <div>
                    <h4 class="modal-title fw-bold text-dark mb-1" id="viewProductModalLabel">Product Details</h4>
                    <p class="text-muted small">Comprehensive overview of the selected product</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light-subtle">
                <!-- General Info -->
                <div class="card border-0 shadow-sm mb-4 rounded-4">
                    <div class="card-body p-4">
                        <h6 class="text-uppercase text-muted fw-bold mb-4 small border-bottom pb-2 d-inline-block">General Information</h6>
                        <div class="row g-4">
                            <div class="col-md-3">
                                <label class="small text-muted fw-semibold">Product Name</label>
                                <div id="view-product-name" class="fw-bold fs-5 text-dark mt-1">-</div>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted fw-semibold">Product Code</label>
                                <div id="view-product-code" class="text-dark fs-6 mt-1">-</div>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted fw-semibold">Status</label>
                                <div id="view-status" class="mt-1">-</div>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted fw-semibold">Interest Method</label>
                                <div id="view-interest-method" class="text-dark fs-6 mt-1">-</div>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted fw-semibold">Min Loan Amount</label>
                                <div id="view-min-loan" class="text-dark fs-6 mt-1 font-monospace">-</div>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted fw-semibold">Max Loan Amount</label>
                                <div id="view-max-loan" class="text-dark fs-6 mt-1 font-monospace">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Sub Products / Items -->
                    <div class="col-lg-12">
                        <div class="card border-0 shadow-sm h-100 rounded-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-shape bg-primary-subtle text-primary rounded-circle me-2 p-2"><i class="bi bi-layers-fill"></i></div>
                                    <h6 class="fw-bold text-dark mb-0">Sub Products (Items)</h6>
                                </div>
                                <div class="table-responsive rounded-3 border">
                                    <table id="view-items-table" class="table table-hover align-middle mb-0 text-nowrap">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="small text-uppercase fw-semibold ps-3">Item Name</th>
                                                <th class="small text-uppercase fw-semibold">Interest Range</th>
                                                <th class="small text-uppercase fw-semibold">Period</th>
                                                <th class="small text-uppercase fw-semibold">Penalty</th>
                                            </tr>
                                        </thead>
                                        <tbody id="view-items-body" class="small bg-white">
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">No items found.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charges & Docs -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm mb-4 rounded-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-shape bg-warning-subtle text-warning rounded-circle me-2 p-2"><i class="bi bi-currency-dollar"></i></div>
                                    <h6 class="fw-bold text-dark mb-0">Additional Charges</h6>
                                </div>
                                <ul class="list-group list-group-flush small" id="view-charges-list">
                                    <li class="list-group-item text-muted text-center py-3 border-0">No charges found.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-shape bg-success-subtle text-success rounded-circle me-2 p-2"><i class="bi bi-file-earmark-text"></i></div>
                                    <h6 class="fw-bold text-dark mb-0">Documents</h6>
                                </div>
                                <ul class="list-group list-group-flush small" id="view-docs-list">
                                    <li class="list-group-item text-muted text-center py-3 border-0">No documents required.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
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
                                <th class="py-3 ps-4">Item Details</th>
                                <th class="py-3">Loan Terms</th>
                                <th class="py-3">Collection & Guarantees</th>
                                <th class="py-3 pe-4">Penalty Rules</th>
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

                            itemsHtml += `
                                <tr class="border-bottm">
                                    <td class="align-middle ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-shape icon-sm bg-primary-subtle text-primary rounded-3 me-3">
                                                <i class="bi bi-box-seam"></i>
                                            </div>
                                            <div>
                                                <span class="d-block text-dark fw-bold h6 mb-0">${item.product_item_name}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle py-3">
                                        <div class="d-flex flex-column gap-1">
                                            <small class="text-muted d-flex align-items-center">
                                                <i class="bi bi-cash me-2 text-success"></i>
                                                <span class="fw-bold text-dark">${minLoan} - ${maxLoan}</span>
                                            </small>
                                            <small class="text-muted d-flex align-items-center">
                                                <i class="bi bi-calendar-range me-2 text-info"></i>
                                                <span>${item.minimum_loan_period} - ${item.maximum_loan_period} ${formatText(p.loan_period_type)}</span>
                                            </small>
                                            <small class="text-muted d-flex align-items-center">
                                                <i class="bi bi-percent me-2 text-warning"></i>
                                                <span>${minInt}% - ${maxInt}% Int.</span>
                                            </small>
                                        </div>
                                    </td>
                                    <td class="align-middle py-3">
                                        <div class="d-flex flex-column gap-1">
                                            <small class="text-muted d-flex align-items-center">
                                                <i class="bi bi-hourglass-split me-2 text-secondary"></i>
                                                <span>Coll: ${item.minimum_collection_period || 0} - ${item.maximum_collection_period || 0}</span>
                                            </small>
                                            <small class="text-muted d-flex align-items-center">
                                                <i class="bi bi-people me-2 text-secondary"></i>
                                                <span>${item.required_guarantee_count || 0} Guarantors</span>
                                            </small>
                                        </div>
                                    </td>
                                    <td class="align-middle pe-4 py-3">
                                        <div class="d-flex flex-column gap-1">
                                            <small class="text-muted d-flex align-items-center">
                                                <i class="bi bi-exclamation-triangle me-2 text-danger"></i>
                                                <span>${formatText(item.penalty_method || 'N/A')} (${item.penalty_percentage || 0}%)</span>
                                            </small>
                                            <div class="d-flex gap-2">
                                                <span class="badge bg-light text-secondary border">Start: ${item.penalty_start_after_days || 0} Days</span>
                                                <span class="badge bg-light text-secondary border">${formatText(item.penalty_apply_type || '-')}</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        $('#view-items-table thead').html(''); // Clear header if no items
                        itemsHtml = '<tr><td colspan="4" class="text-center text-muted py-5"><i class="bi bi-box-seam display-6 d-block mb-3 opacity-25"></i>No items configured.</td></tr>';
                    }
                    $('#view-items-body').html(itemsHtml);

                    // Charges
                    let chargesHtml = '';
                    if (p.additional_charges && p.additional_charges.length > 0) {
                        p.additional_charges.forEach(op => {
                            let valueDisplay = parseFloat(op.value).toLocaleString('en-US', {
                                minimumFractionDigits: 2
                            });
                            let typeBadge = '';

                            // Determine type display
                            if (op.value_type === 'percentage') {
                                typeBadge = '<span class="badge bg-light text-dark border ms-2">Rate: ' + valueDisplay + '%</span>';
                                // For percentage, we might just show the percentage value nicely
                                valueDisplay = valueDisplay + '%';
                            } else {
                                typeBadge = '<span class="badge bg-light text-dark border ms-2">Fixed</span>';
                            }

                            chargesHtml += `
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-0 border-bottom">
                                    <div>
                                        <span class="fw-bolder text-dark h6 mb-0">${op.description}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bolder text-primary h6 mb-0">${parseFloat(op.value).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                                        <span class="text-uppercase small fw-bold text-muted ms-2" style="font-size: 0.75rem;">${op.value_type === 'percentage' ? '(%)' : '(Fixed)'}</span>
                                    </div>
                                </li>
                            `;
                        });
                    } else {
                        chargesHtml = '<li class="list-group-item text-muted text-center py-3">No charges found.</li>';
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