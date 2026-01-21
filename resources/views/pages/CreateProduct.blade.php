@extends('layout.admin')

@section('head')
<!-- Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --secondary-gradient: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
        --danger-gradient: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%);
        --card-shadow: 0 10px 20px rgba(0, 0, 0, 0.05), 0 6px 6px rgba(0, 0, 0, 0.05);
        --input-focus-shadow: 0 0 0 3px rgba(118, 75, 162, 0.2);
    }

    body {
        background-color: #f8f9fa;
    }

    .card-modern {
        border: none;
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        background: #fff;
        margin-bottom: 2rem;
        transition: transform 0.3s ease;
    }

    .card-modern:hover {
        transform: translateY(-5px);
    }

    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f2f5;
        color: #2d3748;
        font-weight: 800;
        font-size: 1.25rem;
        letter-spacing: -0.025em;
    }

    .section-header i {
        margin-right: 12px;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .form-control,
    .form-select,
    .select2-container--default .select2-selection--single {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        height: auto;
        min-height: 48px;
        /* Taller inputs */
        transition: all 0.2s;
        background-color: #fdfdfd;
    }

    .select2-container--default .select2-selection--single {
        padding-top: 8px;
        /* Vertically align text in Select2 */
        padding-left: 1rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 10px;
        right: 10px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #764ba2;
        box-shadow: var(--input-focus-shadow);
        background-color: #fff;
    }

    .card-header-actions {
        display: flex;
        gap: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid mt-5 pt-4"> <!-- Added top padding -->
    <form action="{{ isset($product) ? route('product.update', $product->id) : route('product.store') }}" method="POST" id="productForm">
        @csrf
        @if(isset($product))
        <input type="hidden" id="product_id" value="{{ $product->id }}">
        @endif

        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h4 class="mb-1 fw-bold text-dark" style="letter-spacing: -0.5px;">
                    {{ isset($product) ? 'Edit Product' : 'Create Product' }}
                </h4>
                <p class="text-muted mb-0 small">Configure product details and rules</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">
                    <i class="bi bi-check-lg me-2"></i> {{ isset($product) ? 'Update' : 'Save' }}
                </button>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Core Settings -->
            <div class="col-lg-8">
                <!-- General Information -->
                <div class="card card-modern">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <i class="bi bi-box-seam"></i> General Information
                        </div>
                        <div class="row g-4">
                            <div class="col-md-3">
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="product_name" class="form-control" placeholder="Product Name">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Item Name <span class="text-danger">*</span></label>
                                <input type="text" name="product_item_name" class="form-control" placeholder="Item Name">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Product Code <span class="text-danger">*</span></label>
                                <input type="text" name="product_code" class="form-control" placeholder="Code">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Interest Method <span class="text-danger">*</span></label>
                                <select name="interest_method" class="form-select select2">
                                    <option value="" disabled selected>Choose...</option>
                                    <option value="flat_rate">Flat Rate</option>
                                    <option value="reducing_balance">Reducing Balance</option>
                                    <option value="compound_interest">Compound Interest</option>
                                </select>
                            </div>

                            <!-- New Fields Requested -->
                            <div class="col-md-3">
                                <label class="form-label">Loan Period Type</label>
                                <select name="loan_period_type" class="form-select select2">
                                    <option value="Months">Months</option>
                                    <option value="Weeks">Weeks</option>
                                    <option value="Days">Days</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Interest Period Type</label>
                                <select name="interest_period_type" class="form-select select2">
                                    <option value="Per Month">Per Month</option>
                                    <option value="Per Week">Per Week</option>
                                    <option value="Per Day">Per Day</option>
                                    <option value="Per Year">Per Year</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Collection Period Type</label>
                                <select name="collection_period_type" class="form-select select2">
                                    <option value="Months">Months</option>
                                    <option value="Weeks">Weeks</option>
                                    <option value="Days">Days</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Collection Date Strategy</label>
                                <select name="collection_date_type" class="form-select select2">
                                    <option value="same_as_installment">Same as Installment</option>
                                    <option value="fixed_date">Fixed Date</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Guarantors Count</label>
                                <input type="number" name="guarantee_count" class="form-control" placeholder="Count">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Configuration -->
                <div class="card card-modern">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <i class="bi bi-sliders"></i> Financial Configuration
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Loan Amount Range</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="minimum_loan_amount" class="form-control" placeholder="Min">
                                    <span class="input-group-text bg-light border-start-0 border-end-0">to</span>
                                    <input type="number" step="0.01" name="maximum_loan_amount" class="form-control" placeholder="Max">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Interest Rate Range (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="minimum_interest" class="form-control" placeholder="Min %">
                                    <span class="input-group-text bg-light border-start-0 border-end-0">to</span>
                                    <input type="number" step="0.01" name="maximum_interest" class="form-control" placeholder="Max %">
                                </div>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Loan Period</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="input-group">
                                        <input type="number" name="minimum_loan_period" class="form-control" placeholder="Min">
                                        <span class="input-group-text border-start-0 border-end-0">to</span>
                                        <input type="number" name="maximum_loan_period" class="form-control" placeholder="Max">
                                    </div>
                                    <div class="form-check mb-0" style="white-space: nowrap;">
                                        <input class="form-check-input" type="checkbox" value="" id="enable_collection_period" name="enable_collection_period">
                                        <label class="form-check-label small fw-bold text-uppercase text-muted" for="enable_collection_period">
                                            Diff Collection Period
                                        </label>
                                    </div>
                                </div>
                                <div id="collection_period_container" class="mt-2" style="display: none;">
                                    <label class="form-label small text-muted">Collection Period</label>
                                    <div class="input-group">
                                        <input type="number" name="minimum_collection_period" class="form-control" placeholder="Min">
                                        <span class="input-group-text border-start-0 border-end-0">to</span>
                                        <input type="number" name="maximum_collection_period" class="form-control" placeholder="Max">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Repayment Frequency</label>
                                <select name="repayment_type" class="form-select select2">
                                    <option value="Monthly">Monthly</option>
                                    <option value="Weekly">Weekly</option>
                                    <option value="Daily">Daily</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Penalties, Savings, Documents -->
            <div class="col-lg-4">
                <!-- Penalty Settings -->
                <div class="card card-modern">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <i class="bi bi-exclamation-triangle"></i> Penalties
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Method</label>
                            <select name="penalty_method" class="form-select select2">
                                <option value="every_installment">Recurring %</option>
                                <option value="one_time">One-time Fee</option>
                            </select>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label">Rate (%)</label>
                                <input type="number" step="0.01" name="penalty_percentage" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Penalty Period <span class="text-danger">*</span></label>
                                <select name="penalty_period" class="form-select select2">
                                    <option value="Per Day">Per Day</option>
                                    <option value="Per Week">Per Week</option>
                                    <option value="Per Month">Per Month</option>
                                    <option value="Per Year">Per Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mt-3">
                            <div class="col-6">
                                <label class="form-label">Penalty Start After <span class="text-danger">*</span></label>
                                <input type="number" name="penalty_start_after" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <select name="penalty_duration_type" class="form-select select2">
                                    <option value="Days">Days</option>
                                    <option value="Weeks">Weeks</option>
                                    <option value="Months">Months</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Savings Settings -->
                <div class="card card-modern">
                    <div class="card-body p-4">
                        <div class="section-header justify-content-between border-0 pb-0 mb-3">
                            <div><i class="bi bi-piggy-bank"></i> Savings Settings</div>
                            <div class="form-check form-switch cursor-pointer">
                                <input type="hidden" name="enable_saving" value="No">
                                <input class="form-check-input" type="checkbox" id="enable_saving" name="enable_saving" value="Yes" style="cursor: pointer;">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="saving_account_amount_type" class="form-label">Saving Account Amount Type</label>
                                    <select class="form-select select2" id="saving_account_amount_type" name="saving_amount_type">
                                        <option value="pre_defined" selected>Pre Defined Amount</option>
                                        <option value="percentage">Percentage From Total Loan Amount</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="saving_amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                    <input type="text" id="saving_amount" name="saving_amount" class="form-control" value="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="saving_payment" class="form-label">Saving Payment Type</label>
                                    <select class="form-select select2" id="saving_payment" name="saving_payment_type">
                                        <option value="0" selected>Deduct Savings From Installment</option>
                                        <option value="1">Collect Savings Separately</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full Width: Tables -->
            <div class="col-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-modern h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape bg-primary text-white p-2 me-2" style="border-radius: 8px;">
                                            <i class="bi bi-receipt"></i>
                                        </div>
                                        <h5 class="mb-0 fw-bold">Additional Charges</h5>
                                    </div>
                                </div>

                                <div class="bg-light p-3 rounded-3 mb-3">
                                    <div class="mb-2">
                                        <label class="form-label small text-muted">Description</label>
                                        <input type="text" id="newChargeDesc" class="form-control form-control-sm" placeholder="Charge description">
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <label class="form-label small text-muted">Amount</label>
                                            <input type="number" step="0.01" id="newChargeVal" class="form-control form-control-sm" placeholder="0.00">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small text-muted">Type</label>
                                            <select id="newChargeType" class="form-select form-select-sm">
                                                <option value="Fixed">Fixed</option>
                                                <option value="Percentage">%</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small text-muted">Deduction Type</label>
                                        <input type="text" id="newChargeDeduction" class="form-control form-control-sm" value="On Loan Disbursement" placeholder="On Loan Disbursement">
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary w-100" id="addChargeBtn">
                                        <i class="bi bi-plus-lg"></i> Add Charge
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-premium" id="chargesTable">
                                        <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th style="width: 110px;">Type</th>
                                                <th style="width: 120px;">Amount</th>
                                                <th style="width: 200px;">Deduction Type</th>
                                                <th style="width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($product) && $product->additional_charges->count() > 0)
                                            @foreach($product->additional_charges as $index => $charge)
                                            <tr>
                                                <td>
                                                    <input type="text" name="charges[{{$index}}][description]" class="form-control" value="{{ $charge->description }}">
                                                </td>
                                                <td>
                                                    <select name="charges[{{$index}}][value_type]" class="form-select select2">
                                                        <option value="Fixed" {{ $charge->value_type == 'Fixed' ? 'selected' : '' }}>Fixed</option>
                                                        <option value="Percentage" {{ $charge->value_type == 'Percentage' ? 'selected' : '' }}>%</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="charges[{{$index}}][value]" class="form-control" value="{{ $charge->value }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="charges[{{$index}}][deduction_type]" class="form-control" value="{{ $charge->deduction_type ?? 'On Loan Disbursement' }}" placeholder="On Loan Disbursement">
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    @if(!isset($product) || $product->additional_charges->count() == 0)
                                    <div class="text-center text-muted py-4 empty-state">
                                        <small>No additional charges configured</small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-modern h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape bg-info text-white p-2 me-2" style="border-radius: 8px;">
                                            <i class="bi bi-file-earmark-check"></i>
                                        </div>
                                        <h5 class="mb-0 fw-bold"> Documents</h5>
                                    </div>
                                </div>

                                <div class="bg-light p-3 rounded-3 mb-3">
                                    <div class="mb-3">
                                        <label class="form-label small text-muted">Document Name</label>
                                        <input type="text" id="newDocName" class="form-control form-control-sm" placeholder="Document Name">
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary text-white w-100" id="addDocBtn">
                                        <i class="bi bi-plus-lg"></i> Add Document
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-premium" id="docsTable">
                                        <thead>
                                            <tr>
                                                <th>Document Name</th>
                                                <th style="width: 100px;">Mandatory</th>
                                                <th style="width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($product) && $product->_documents->count() > 0)
                                            @foreach($product->_documents as $index => $doc)
                                            <tr>
                                                <td>
                                                    <input type="text" name="documents[{{$index}}][name]" class="form-control" value="{{ $doc->name }}">
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" name="documents[{{$index}}][]" value="1" {{ $doc->_status ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    @if(!isset($product) || $product->_documents->count() == 0)
                                    <div class="text-center text-muted py-4 empty-state">
                                        <small>No documents configured</small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



    </form>
</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Init Select2 with custom styling fix
        $('.select2').select2({
            width: '100%'
        });

        // Savings Toggle Logic
        $('#enable_saving').change(function() {
            toggleSavingFields();
        });

        function toggleSavingFields() {
            var enableSaving = $('#enable_saving').is(':checked');
            if (!enableSaving) {
                $('#saving_account_amount_type').closest('.col-md-6').hide();
                $('#saving_amount').closest('.col-md-6').hide();
                $('#saving_payment').closest('.col-md-6').hide();
            } else {
                $('#saving_account_amount_type').closest('.col-md-6').show();
                $('#saving_amount').closest('.col-md-6').show();
                $('#saving_payment').closest('.col-md-6').show();
            }
        }

        // Collection Period Toggle Logic
        $('#enable_collection_period').change(function() {
            toggleCollectionFields();
        });

        function toggleCollectionFields() {
            if ($('#enable_collection_period').is(':checked')) {
                $('#collection_period_container').slideDown();
            } else {
                $('#collection_period_container').slideUp();
            }
        }

        // Initial call
        toggleSavingFields();
        toggleCollectionFields();

        // Dynamic Charges
        let chargeIndex = 0;

        $('#newChargeType').change(function() {
            if ($(this).val() === 'Percentage') {
                $('#newChargeVal').attr('placeholder', 'Percentage %');
            } else {
                $('#newChargeVal').attr('placeholder', '0.00');
            }
        });

        $('#addChargeBtn').click(function() {
            let desc = $('#newChargeDesc').val();
            let val = $('#newChargeVal').val();
            let type = $('#newChargeType').val(); // Fixed or Percentage
            let deduct = $('#newChargeDeduction').val();

            if (!desc || !val) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please fill in Description and Amount',
                });
                return;
            }

            // Remove empty state if initialized
            $('#chargesTable').closest('.card-body').find('.empty-state').remove();

            let html = `
                    <tr>
                        <td>
                            <input type="text" name="charges[${chargeIndex}][description]" class="form-control" value="${desc}" readonly>
                        </td>
                        <td>
                            <input type="hidden" name="charges[${chargeIndex}][value_type]" value="${type}">
                            <span class="badge bg-light text-dark border">${type}</span>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="charges[${chargeIndex}][value]" class="form-control" value="${val}" readonly>
                        </td>
                         <td>
                             <input type="text" name="charges[${chargeIndex}][deduction_type]" class="form-control" value="${deduct}" readonly>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `;
            $('#chargesTable tbody').append(html);

            // Clear inputs
            $('#newChargeDesc').val('');
            $('#newChargeVal').val('');
            $('#newChargeDeduction').val('On Loan Disbursement');

            chargeIndex++;
        });

        // Dynamic Documents

        let docIndex = 0;

        $('#addDocBtn').click(function() {
            let name = $('#newDocName').val();

            if (!name) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please enter a Document Name',
                });
                return;
            }

            $('#docsTable').closest('.card-body').find('.empty-state').remove();

            let html = `
                     <tr>
                        <td>
                            <input type="text" name="documents[${docIndex}][name]" class="form-control" value="${name}" readonly>
                        </td>
                        <td class="text-center">
                             <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" name="documents[${docIndex}][]" value="1" checked>
                             </div>
                        </td>
                            <td class="text-end">
                            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `;
            $('#docsTable tbody').append(html);

            // Clear input
            $('#newDocName').val('');

            docIndex++;
        });

        // Remove Row
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').fadeOut(300, function() {
                $(this).remove();
            });
        });


        $(document).on('click', '.remove-checklist', function() {
            $(this).closest('.input-group').remove();
        });

        // AJAX FETCH FOR EDIT
        let productId = $('#product_id').val();
        if (productId) {
            $.ajax({
                url: `/product/get-details/${productId}`,
                type: 'GET',
                success: function(response) {
                    let product = response.product;
                    let levels = response.levels;

                    // Populate Core Fields
                    $('input[name="product_name"]').val(product.product_name);
                    $('input[name="product_item_name"]').val(product.product_item_name);
                    $('input[name="product_code"]').val(product.product_code);
                    $('select[name="interest_method"]').val(product.interest_method).trigger('change');
                    $('input[name="minimum_loan_amount"]').val(product.minimum_loan_amount);
                    $('input[name="maximum_loan_amount"]').val(product.maximum_loan_amount);
                    $('input[name="minimum_interest"]').val(product.minimum_interest);
                    $('input[name="maximum_interest"]').val(product.maximum_interest);

                    // Loan Period
                    $('input[name="minimum_loan_period"]').val(product.minimum_loan_period);
                    $('input[name="maximum_loan_period"]').val(product.maximum_loan_period);
                    $('select[name="loan_period_type"]').val(product.loan_period_type);

                    // Collection Period
                    if (product.minimum_collection_period || product.maximum_collection_period) {
                        $('#enable_collection_period').prop('checked', true).trigger('change');
                        $('input[name="minimum_collection_period"]').val(product.minimum_collection_period);
                        $('input[name="maximum_collection_period"]').val(product.maximum_collection_period);
                    } else {
                        $('#enable_collection_period').prop('checked', false).trigger('change');
                    }
                    $('select[name="collection_period_type"]').val(product.collection_period_type).trigger('change');

                    $('select[name="interest_period_type"]').val(product.interest_period_type).trigger('change');

                    // Removed loan_duration field as strictly mapped to max collection period in old logc, but now cleaner
                    // $('input[name="loan_duration"]').val(product.maximum_collection_period);

                    $('select[name="repayment_type"]').val(product.repayment_type).trigger('change');
                    $('select[name="collection_date_type"]').val(product.collection_date_type).trigger('change');
                    $('input[name="guarantee_count"]').val(product.guarantee_count);

                    $('select[name="penalty_method"]').val(product.penalty_method).trigger('change');
                    $('input[name="penalty_percentage"]').val(product.penalty_percentage);
                    $('select[name="penalty_period"]').val(product.penalty_apply_type).trigger('change');
                    $('input[name="penalty_start_after"]').val(product.penalty_start_after_days);
                    $('select[name="penalty_duration_type"]').val(product.collection_period_type).trigger('change'); // Note: mapped to collection_period_type or specific penalty type? 
                    // Re-checking controller: 
                    // $product->penalty_apply_type = $request->penalty_period; 
                    // $product->collection_period_type = $request->loan_duration_type;
                    // User's blade had 'penalty_duration_type' mapped to $product->penalty_duration_type but controller store maps loan_duration_type to collection_period_type?
                    // Wait, let's look at blade for 'penalty_duration_type'. 
                    // Blade: name="penalty_duration_type", value="{{ old('penalty_duration_type', $product->penalty_duration_type ?? '') }}"
                    // Controller store: $product->collection_period_type = $request->loan_duration_type;
                    // It seems the blade field 'penalty_duration_type' might not be saving to a distinct column in the controller snippet I saw?
                    // Ah, in controller store:
                    // $product->penalty_apply_type = $request->penalty_period;
                    // $product->penalty_start_after_days = $request->penalty_start_after;
                    // It seems 'penalty_duration_type' input is NOT explicitly saved in the create method I saw?
                    // Wait, I might have missed it or it might be missing in the backend. 
                    // The blade has it. 
                    // For now, I will map it if it exists in response.
                    if (product.penalty_duration_type) {
                        $('select[name="penalty_duration_type"]').val(product.penalty_duration_type).trigger('change');
                    }

                    // Savings
                    if (product.enable_saving === 'Yes') {
                        $('#enable_saving').prop('checked', true).trigger('change');
                        $('#saving_account_amount_type').val(product.saving_amount_type).trigger('change');
                        $('input[name="saving_amount"]').val(product.saving_amount);
                        $('#saving_payment').val(product.saving_payment).trigger('change');
                    } else {
                        $('#enable_saving').prop('checked', false).trigger('change');
                    }
                    toggleSavingFields();

                    // Populate Charges
                    if (product.additional_charges && product.additional_charges.length > 0) {
                        $('#chargesTable').closest('.card-body').find('.empty-state').remove();
                        product.additional_charges.forEach(charge => {
                            let html = `
                                <tr>
                                    <td>
                                        <input type="text" name="charges[${chargeIndex}][description]" class="form-control" value="${charge.description}" >
                                    </td>
                                    <td>
                                        <select name="charges[${chargeIndex}][value_type]" class="form-select select2-dynamic">
                                            <option value="Fixed" ${charge.value_type == 'Fixed' ? 'selected' : ''}>Fixed</option>
                                            <option value="Percentage" ${charge.value_type == 'Percentage' ? 'selected' : ''}>%</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="charges[${chargeIndex}][value]" class="form-control" value="${charge.value}" >
                                    </td>
                                    <td>
                                        <input type="text" name="charges[${chargeIndex}][deduction_type]" class="form-control" value="${charge.deduction_type || ''}">
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            `;
                            $('#chargesTable tbody').append(html);
                            $('#chargesTable tbody').find('.select2-dynamic').last().select2({
                                width: '100%'
                            });
                            chargeIndex++;
                        });
                    }

                    // Populate Documents
                    if (product._documents && product._documents.length > 0) {
                        $('#docsTable').closest('.card-body').find('.empty-state').remove();
                        product._documents.forEach(doc => {
                            let html = `
                                <tr>
                                    <td>
                                        <input type="text" name="documents[${docIndex}][name]" class="form-control" value="${doc.name}" >
                                    </td>
                                    <td class="text-center">
                                         <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" name="documents[${docIndex}][]" value="1" ${doc._status ? 'checked' : ''}>
                                         </div>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            `;
                            $('#docsTable tbody').append(html);
                            docIndex++;
                        });
                    }


                },
                error: function(xhr) {
                    console.error("Failed to load product details", xhr);
                    alert("Failed to load product details for editing.");
                }
            });
        }
    });

    $(document).ready(function() {
        if (typeof handleAjaxFormSubmission === 'function') {
            handleAjaxFormSubmission('#productForm', function(response) {
                if (response.redirect_url) {
                    window.location.href = response.redirect_url;
                } else {
                    location.reload();
                }
            });
        }
    });
</script>
<script src="{{ asset('JS/common.js') }}"></script>
@endsection