@extends('layout.admin')

@section('head')
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
        font-family: 'Inter', sans-serif;
    }

    .card-modern {
        border: none;
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        background: #fff;
        margin-bottom: 2rem;
        transition: transform 0.3s ease;
    }

    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f2f5;
        color: #2d3748;
        font-weight: 800;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .section-header i {
        margin-right: 12px;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 1.5rem;
    }

    .form-label {
        font-weight: 700;
        color: #4a5568;
        margin-bottom: 0.4rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s;
        background-color: #fdfdfd;
    }

    .form-control:focus {
        border-color: #764ba2;
        box-shadow: var(--input-focus-shadow);
    }

    /* --- Enhanced Select2 UI --- */
    .select2-container--default .select2-selection--single {
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        height: 42px !important;
        display: flex !important;
        align-items: center !important;
        background-color: #fdfdfd !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #4a5568 !important;
        font-size: 0.9rem !important;
    }

    .select2-container--default .select2-dropdown {
        border: none !important;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
        border-radius: 12px !important;
        padding: 5px !important;
    }

    .select2-results__option--highlighted[aria-selected] {
        background: var(--primary-gradient) !important;
        border-radius: 8px !important;
    }

    /* --- Custom Toggle Switch --- */
    .form-switch .form-check-input {
        width: 2.2em;
        height: 1.1em;
        cursor: pointer;
    }

    .form-switch .form-check-input:checked {
        background-color: #764ba2;
        border-color: #764ba2;
    }

    .config-box {
        background-color: #fcfcfd;
        border: 1px solid #edf2f7;
        border-radius: 15px;
        padding: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid mt-5 pt-4">
    <form action="{{ isset($product) ? route('product.update', $product->id) : route('product.store') }}" method="POST" id="productForm">
        @csrf
        @if(isset($product))
        <input type="hidden" id="product_id" value="{{ $product->id }}">
        @endif

        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h4 class="mb-1 fw-bold text-dark">{{ isset($product) ? 'Edit Product' : 'Create Product' }}</h4>
                <p class="text-muted mb-0 small">Define financial rules and collection cycles</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <button type="submit" class="btn btn-primary-common px-5 rounded-pill shadow-sm fw-bold">
                    <i class="bi bi-check-lg me-2"></i> {{ isset($product) ? 'Update Product' : 'Save Product' }}
                </button>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: General & Config -->
            <div class="col-lg-8">
                <!-- General Information -->
                <div class="card card-modern">
                    <div class="card-body p-4">
                        <div class="section-header"><i class="bi bi-box-seam"></i> General Information</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="product_name" class="form-control" placeholder="e.g. Personal Loan">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Product Code <span class="text-danger">*</span></label>
                                <input type="text" name="product_code" class="form-control" placeholder="PL-001">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Interest Method <span class="text-danger">*</span></label>
                                <select name="interest_method" class="form-select select2">
                                    <option value="flat_rate">Flat Rate</option>
                                    <option value="reducing_balance">Reducing Balance</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
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
                                <label class="form-label">Global Guarantors</label>
                                <input type="number" name="guarantee_count" class="form-control" placeholder="0" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Configuration Items -->
                <div class="card card-modern">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="section-header border-0 m-0 p-0">
                                <i class="bi bi-sliders"></i> Configuration Sub Products
                            </div>
                            <button type="button" class="btn btn-primary-common btn-sm rounded-pill px-3" id="addNewItemBtn">
                                <i class="bi bi-plus-lg me-1"></i> Add to List
                            </button>
                        </div>

                        <div class="config-box mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Sub Product Label</label>
                                    <input type="text" id="newItemName" class="form-control" placeholder="e.g. Gold Tier">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Loan Amount Range</label>
                                    <div class="input-group">
                                        <input type="number" id="newItemMinLoan" class="form-control" placeholder="Min" min="0">
                                        <input type="number" id="newItemMaxLoan" class="form-control" placeholder="Max" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Interest % Range</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" id="newItemMinInt" class="form-control" placeholder="Min" min="0">
                                        <input type="number" step="0.01" id="newItemMaxInt" class="form-control" placeholder="Max" min="0">
                                        <span class="input-group-text" id="display_interest_period_type">Per Month</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label">Loan Period</label>
                                    </div>
                                    <div class="input-group">
                                        <input type="number" id="newItemMinPeriod" class="form-control" placeholder="Min" min="0">
                                        <input type="number" id="newItemMaxPeriod" class="form-control" placeholder="Max" min="0">
                                        <span class="input-group-text" id="display_loan_period_type">Months</span>
                                        <div class="input-group-text ">
                                            <div class="form-check form-switch mb-0 min-h-0">
                                                <input class="form-check-input" type="checkbox" id="newItemDiffColl">
                                                <label class="form-check-label small text-muted text-uppercase fw-bold" for="newItemDiffColl" style="font-size: 0.6rem;">Different Collection</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4" id="newItemCollContainer" style="display: none;">
                                    <label class="form-label">Collection Period</label>
                                    <div class="input-group">
                                        <input type="number" id="newItemMinColl" class="form-control" placeholder="Min" min="0">
                                        <input type="number" id="newItemMaxColl" class="form-control" placeholder="Max" min="0">
                                        <span class="input-group-text" id="display_collection_period_type">Months</span>
                                    </div>
                                </div>

                            </div>

                            <div class="mt-3">
                                <div id="advancedItemFields" class="mt-3 pt-3 border-top">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Required Guarantors</label>
                                            <input type="number" id="newItemGuarantors" class="form-control" placeholder="Count" min="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Penalty Method</label>
                                            <select id="newItemPenaltyMethod" class="form-select">
                                                <option value="every_installment">Recurring</option>
                                                <option value="one_time">One-time</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Penalty %</label>
                                            <input type="number" step="0.01" id="newItemPenaltyRate" class="form-control" placeholder="0.00" min="0">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Apply After</label>
                                            <div class="input-group">
                                                <input type="number" id="newItemPenaltyStart" class="form-control" placeholder="Days" min="0">
                                                <select id="newItemPenaltyType" class="form-select" disabled>
                                                    <option value="Days">Days</option>
                                                    <option value="Weeks">Weeks</option>
                                                    <option value="Months">Months</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle border-top" id="itemsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="small fw-bold text-muted text-uppercase">Label</th>
                                        <th class="small fw-bold text-muted text-uppercase">Loan Amount</th>
                                        <th class="small fw-bold text-muted text-uppercase">Period</th>
                                        <th class="small fw-bold text-muted text-uppercase">Interest</th>
                                        <th class="small fw-bold text-muted text-uppercase">Penalty</th>
                                        <th class="text-end"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white"></tbody>
                            </table>
                            <div class="text-center py-4 empty-state-table">
                                <p class="text-muted small">No configurations added yet.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Savings, Recovery, Charges, Docs -->
            <div class="col-lg-4">

                <!-- Recovery Account -->
                <div class="card card-modern mb-3">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="section-header border-0 p-0 m-0"><i class="bi bi-wallet2"></i> Recovery Account</div>
                            <div class="form-check form-switch">
                                <input type="hidden" name="recovery_account_status" value="No">
                                <input class="form-check-input" type="checkbox" id="recovery_account_status" name="recovery_account_status" value="Yes">
                            </div>
                        </div>
                        <div id="recoveryAccountInfo" class="alert alert-light border small text-muted mt-0 mb-0" style="display: none;">
                            <strong>Note:</strong> When the Recovery Account option is enabled for a product, the following process will apply: <br><br>
                            After a loan is created using this product, any installment payments made by the customer will not be directly applied to the loan installments.
                            Instead, the payment amount will be credited to the customer’s Recovery Account.<br><br>
                            When the customer’s loan installment becomes due, the system will automatically deduct the due amount from the Recovery Account balance and apply it to the loan.
                            This ensures that payments are first collected into the Recovery Account and then settled against loan dues on their respective due dates.
                        </div>
                    </div>
                </div>

                <!-- Savings -->
                <div class="card card-modern mb-3">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="section-header border-0 p-0 m-0"><i class="bi bi-piggy-bank"></i> Savings</div>
                            <div class="form-check form-switch">
                                <input type="hidden" name="enable_saving" value="No">
                                <input class="form-check-input" type="checkbox" id="enable_saving" name="enable_saving" value="Yes">
                            </div>
                        </div>

                        <!-- Savings Info Note -->
                        <div id="savingsAccountInfo" class="alert alert-light border small text-muted mt-0 mb-3" style="display: none;">
                            <strong>Note:</strong> Enabling this option activates mandatory savings collection. Detailed process information to be added here.
                        </div>

                        <div id="savingFields" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">Amount Type</label>
                                <select class="form-select select2" name="saving_amount_type" id="saving_account_amount_type">
                                    <option value="pre_defined">Fixed Amount</option>
                                    <option value="percentage">Percentage of Loan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" id="savingAmountLabel">Amount</label>
                                <input type="number" step="0.01" name="saving_amount" id="saving_amount" class="form-control" value="0.00" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Type</label>
                                <select class="form-select select2" id="saving_payment" name="saving_payment_type">
                                    <option value="0">Deduct From Installment</option>
                                    <option value="1">Collect Separately</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Charges -->
                <div class="card card-modern mb-3">
                    <div class="card-body p-4">
                        <div class="section-header"><i class="bi bi-receipt"></i> Additional Charges</div>
                        <div class="config-box mb-3 p-3">
                            <div class="mb-2">
                                <label class="form-label small">Description</label>
                                <input type="text" id="newChargeDesc" class="form-control form-control-sm" placeholder="e.g. Late Payment Fee">
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label small" id="chargeAmountLabel">Amount</label>
                                    <input type="number" step="0.01" id="newChargeVal" class="form-control form-control-sm" min="0" placeholder="0.00">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Type</label>
                                    <select id="newChargeType" class="form-select form-select-sm">
                                        <option value="Fixed">Fixed</option>
                                        <option value="Percentage">%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Deduction</label>
                                <input type="text" id="newChargeDeduction" class="form-control form-control-sm" value="On Loan Disbursement">
                            </div>
                            <button class="btn btn-primary-common btn-sm w-100" type="button" id="addChargeBtn">Add Charge</button>
                        </div>

                        <ul class="list-group list-group-flush" id="chargesList">
                            <!-- Charges will append here -->
                        </ul>
                    </div>
                </div>

                <!-- Documents -->
                <div class="card card-modern mb-3">
                    <div class="card-body p-4">
                        <div class="section-header"><i class="bi bi-file-earmark-text"></i> Required Documents</div>
                        <div class="input-group mb-3">
                            <input type="text" id="newDocName" class="form-control" placeholder="ID Card, Photo...">
                            <button class="btn btn-primary-common" type="button" id="addDocBtn">Add</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm" id="docsTable">
                                <tbody></tbody>
                            </table>
                            <div class="text-center text-muted p-2 empty-state-docs">
                                <small>No documents added.</small>
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
        // Initialize Searchable Selects
        $('.select2').select2({
            width: '100%'
        });

        // Savings Toggle
        $('#enable_saving').change(function() {
            var enableSaving = $(this).is(':checked');
            $('#savingFields').toggle(enableSaving);

            if (enableSaving) {
                $('#savingsAccountInfo').slideDown();
            } else {
                $('#savingsAccountInfo').slideUp();
            }
        });

        // Recovery Account Toggle
        $('#recovery_account_status').change(function() {
            var enableRecovery = $(this).is(':checked');
            if (enableRecovery) {
                $('#recoveryAccountInfo').slideDown();
            } else {
                $('#recoveryAccountInfo').slideUp();
            }
        });

        // Collection Period Toggle in Add Item
        $('#newItemDiffColl').change(function() {
            if ($(this).is(':checked')) {
                $('#newItemCollContainer').fadeIn();
            } else {
                $('#newItemCollContainer').fadeOut();
            }
        });

    // -----------------------------------------
    // Sync Period Types
    // -----------------------------------------
    function syncPeriodType(sourceName, targetId) {
        let val = $('select[name="' + sourceName + '"] option:selected').text();
        $('#' + targetId).text(val);
    }

    $('select[name="loan_period_type"]').change(function() {
        syncPeriodType('loan_period_type', 'display_loan_period_type');
    });
    $('select[name="interest_period_type"]').change(function() {
        syncPeriodType('interest_period_type', 'display_interest_period_type');
    });
    $('select[name="collection_period_type"]').change(function() {
        syncPeriodType('collection_period_type', 'display_collection_period_type');
    });

    // Initial Sync
    syncPeriodType('loan_period_type', 'display_loan_period_type');
    syncPeriodType('interest_period_type', 'display_interest_period_type');
    syncPeriodType('collection_period_type', 'display_collection_period_type');



    // -----------------------------------------
    // Validation Logic for Percentage Inputs
    // -----------------------------------------
    function handlePercentageLogic(selectId, inputId, labelId, standardLabel) {
        $(selectId).change(function() {
            let val = $(this).val();
            let isPercentage = (val === 'percentage' || val === 'Percentage');

            if (isPercentage) {
                $(labelId).text('Percentage (%)');
                $(inputId).val('').attr('max', 100);
            } else {
                $(labelId).text(standardLabel);
                $(inputId).removeAttr('max');
            }
        });

        $(inputId).on('input', function() {
            let valType = $(selectId).val();
            let isPercentage = (valType === 'percentage' || valType === 'Percentage');

            if (isPercentage) {
                let val = parseFloat($(this).val());
                if (val > 100) {
                    $(this).val(100);
                }
            }
        });
    }

    // Apply to Savings
    handlePercentageLogic('#saving_account_amount_type', '#saving_amount', '#savingAmountLabel', 'Amount');

    // Apply to Charges
    handlePercentageLogic('#newChargeType', '#newChargeVal', '#chargeAmountLabel', 'Amount');

    // -----------------------------------------
    // Dynamic Items Logic
    // -----------------------------------------
    let itemIndex = 0;

    // Function to Add Item Row
    window.addItemRow = function(data) {
        console.log("Adding row", data); // Debug
        $('.empty-state-table').hide();

        // Penalty Text Display
        let penaltyTxt = '-';
        if (data.penalty_percentage && data.penalty_percentage > 0) {
            penaltyTxt = `${data.penalty_percentage}% (${data.penalty_method == 'one_time' ? 'One Time' : 'Recur'}) after ${data.penalty_start_after_days} ${data.penalty_apply_type}`;
        }

        let html = `
                <tr>
                    <td><span class="fw-bold">${data.product_item_name || 'Tier ' + (itemIndex+1)}</span>
                        <input type="hidden" name="items[${itemIndex}][product_item_name]" value="${data.product_item_name || ''}">
                    </td>
                    <td>${data.minimum_loan_amount} - ${data.maximum_loan_amount}
                        <input type="hidden" name="items[${itemIndex}][minimum_loan_amount]" value="${data.minimum_loan_amount}">
                        <input type="hidden" name="items[${itemIndex}][maximum_loan_amount]" value="${data.maximum_loan_amount}">
                    </td>
                    <td>${data.minimum_loan_period} - ${data.maximum_loan_period}
                         ${data.minimum_collection_period ? `<br><small class='text-muted'>Coll: ${data.minimum_collection_period}-${data.maximum_collection_period}</small>` : ''}
                        
                        <input type="hidden" name="items[${itemIndex}][minimum_loan_period]" value="${data.minimum_loan_period}">
                        <input type="hidden" name="items[${itemIndex}][maximum_loan_period]" value="${data.maximum_loan_period}">
                        <input type="hidden" name="items[${itemIndex}][minimum_collection_period]" value="${data.minimum_collection_period || ''}">
                        <input type="hidden" name="items[${itemIndex}][maximum_collection_period]" value="${data.maximum_collection_period || ''}">
                    </td>
                    <td>${data.minimum_interest} - ${data.maximum_interest}%
                         <input type="hidden" name="items[${itemIndex}][minimum_interest]" value="${data.minimum_interest}">
                        <input type="hidden" name="items[${itemIndex}][maximum_interest]" value="${data.maximum_interest}">
                    </td>
                    <td>
                         <small style="font-size:0.75rem;">${penaltyTxt}</small>
                         <input type="hidden" name="items[${itemIndex}][penalty_method]" value="${data.penalty_method}">
                         <input type="hidden" name="items[${itemIndex}][penalty_percentage]" value="${data.penalty_percentage}">
                         <input type="hidden" name="items[${itemIndex}][penalty_start_after_days]" value="${data.penalty_start_after_days}">
                         <input type="hidden" name="items[${itemIndex}][penalty_apply_type]" value="${data.penalty_apply_type}">
                         <input type="hidden" name="items[${itemIndex}][required_guarantee_count]" value="${data.required_guarantee_count}">
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-link text-danger remove-row"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;

        $('#itemsTable tbody').append(html);
        itemIndex++;
    }

    $('#addNewItemBtn').click(function() {
        let name = $('#newItemName').val();
        let minL = $('#newItemMinLoan').val();
        let maxL = $('#newItemMaxLoan').val();

        if (!minL || !maxL) {
            // Use standard alert if Swal not available, but user had swal before. Assuming Swal exists or use alert.
            alert('Please enter a valid loan range');
            return;
        }

        // Guarantor Validation
        let globalGuarantors = parseInt($('input[name="guarantee_count"]').val()) || 0;
        let reqGuarantors = parseInt($('#newItemGuarantors').val()) || 0;

        if (reqGuarantors > globalGuarantors) {
            alert(`Required Guarantors (${reqGuarantors}) cannot be greater than Global Guarantors (${globalGuarantors})`);
            return;
        }

        let data = {
            product_item_name: name,
            minimum_loan_amount: minL,
            maximum_loan_amount: maxL,
            minimum_interest: $('#newItemMinInt').val(),
            maximum_interest: $('#newItemMaxInt').val(),
            minimum_loan_period: $('#newItemMinPeriod').val(),
            maximum_loan_period: $('#newItemMaxPeriod').val(),
            minimum_collection_period: $('#newItemDiffColl').is(':checked') ? $('#newItemMinColl').val() : '',
            maximum_collection_period: $('#newItemDiffColl').is(':checked') ? $('#newItemMaxColl').val() : '',
            required_guarantee_count: $('#newItemGuarantors').val(),
            penalty_method: $('#newItemPenaltyMethod').val(),
            penalty_percentage: $('#newItemPenaltyRate').val(),
            penalty_start_after_days: $('#newItemPenaltyStart').val(),
            penalty_apply_type: $('#newItemPenaltyType').val()
        };

        addItemRow(data);

        // Reset input fields
        $('.config-box input').val('');
        $('#newItemDiffColl').prop('checked', false).trigger('change');
        $('#newItemPenaltyMethod').val('every_installment');
        $('#newItemPenaltyType').val('Days');
    });

    $(document).on('click', '.remove-row', function() {
        // Check if it's an item row or other
        $(this).closest('tr').remove();
        if ($('#itemsTable tbody tr').length === 0) $('.empty-state-table').show();
    });


    // -----------------------------------------
    // Additional Charges Logic
    // -----------------------------------------
    let chargeIndex = 0;

    window.addChargeRow = function(charge) {
        let html = `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold">${charge.description}</div>
                        <div class="small text-muted">${charge.value} (${charge.value_type}) - ${charge.deduction_type}</div>
                        
                        <input type="hidden" name="charges[${chargeIndex}][description]" value="${charge.description}">
                        <input type="hidden" name="charges[${chargeIndex}][value_type]" value="${charge.value_type}">
                        <input type="hidden" name="charges[${chargeIndex}][value]" value="${charge.value}">
                        <input type="hidden" name="charges[${chargeIndex}][deduction_type]" value="${charge.deduction_type}">
                    </div>
                    <button type="button" class="btn btn-sm text-danger remove-charge"><i class="bi bi-x-circle"></i></button>
                </li>
            `;
        $('#chargesList').append(html);
        chargeIndex++;
    }

    $('#addChargeBtn').click(function() {
        let desc = $('#newChargeDesc').val();
        let val = $('#newChargeVal').val();
        if (!desc || !val) {
            alert("Please provide description and amount");
            return;
        }

        let charge = {
            description: desc,
            value: val,
            value_type: $('#newChargeType').val(),
            deduction_type: $('#newChargeDeduction').val()
        };
        addChargeRow(charge);

        $('#newChargeDesc').val('');
        $('#newChargeVal').val('');
    });

    $(document).on('click', '.remove-charge', function() {
        $(this).closest('li').remove();
    });


    // -----------------------------------------
    // Documents Logic
    // -----------------------------------------
    let docIndex = 0;

    window.addDocRow = function(docData) {
        $('.empty-state-docs').hide();
        let html = `
                <tr>
                    <td>
                        <span class="small fw-bold">${docData.name}</span>
                        <input type="hidden" name="documents[${docIndex}][name]" value="${docData.name}">
                    </td>
                    <td class="text-center" style="width: 50px;">
                         <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="documents[${docIndex}][]" value="1" ${docData._status ? 'checked' : ''} title="Mandatory">
                        </div>
                    </td>
                    <td style="width: 30px;">
                        <button type="button" class="btn btn-sm text-danger remove-doc-row p-0"><i class="bi bi-x-circle"></i></button>
                    </td>
                </tr>
            `;
        $('#docsTable tbody').append(html);
        docIndex++;
    }

    $('#addDocBtn').click(function() {
        let docName = $('#newDocName').val();
        if (!docName) return;

        addDocRow({
            name: docName,
            _status: true
        });
        $('#newDocName').val('');
    });

    $(document).on('click', '.remove-doc-row', function() {
        $(this).closest('tr').remove();
        if ($('#docsTable tbody tr').length === 0) $('.empty-state-docs').show();
    });


    // -----------------------------------------
    // AJAX Edit Mode Population
    // -----------------------------------------
    let productId = $('#product_id').val();
    if (productId) {
        $.ajax({
            url: `/product/get-details/${productId}`,
            type: 'GET',
            success: function(response) {
                let product = response.product;

                // Core Fields
                $('input[name="product_name"]').val(product.product_name);
                $('input[name="product_item_name"]').val(product.product_item_name);
                $('input[name="product_code"]').val(product.product_code);
                $('select[name="interest_method"]').val(product.interest_method).trigger('change');
                $('select[name="loan_period_type"]').val(product.loan_period_type).trigger('change');
                $('select[name="interest_period_type"]').val(product.interest_period_type).trigger('change');
                $('select[name="collection_period_type"]').val(product.collection_period_type).trigger('change');
                $('select[name="collection_date_type"]').val(product.collection_date_type).trigger('change');
                $('input[name="guarantee_count"]').val(product.guarantee_count);

                // Recover & Savings
                if (product.recovery_account_status === 'Yes') {
                    $('#recovery_account_status').prop('checked', true).trigger('change');
                } else {
                    $('#recovery_account_status').prop('checked', false).trigger('change');
                }
                if (product.enable_saving === 'Yes') {
                    $('#enable_saving').prop('checked', true).trigger('change');
                    $('select[name="saving_amount_type"]').val(product.saving_amount_type).trigger('change');
                    $('input[name="saving_amount"]').val(product.saving_amount);
                    $('select[name="saving_payment_type"]').val(product.saving_payment).trigger('change');
                }

                // Items
                if (product.product_has_items && product.product_has_items.length > 0) {
                    $('#itemsTable tbody').empty();
                    product.product_has_items.forEach(item => {
                        addItemRow(item);
                    });
                }

                // Charges
                if (product.additional_charges && product.additional_charges.length > 0) {
                    product.additional_charges.forEach(charge => {
                        addChargeRow(charge);
                    });
                }

                // Documents
                if (product._documents && product._documents.length > 0) {
                    $('#docsTable tbody').empty();
                    product._documents.forEach(doc => {
                        addDocRow(doc);
                    });
                }
            },
            error: function(e) {
                console.error("Error loading product", e);
            }
        });
    }
    });
</script>
<script src="{{ asset('JS/common.js') }}"></script>
@endsection