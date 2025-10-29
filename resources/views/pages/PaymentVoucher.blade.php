@extends('layout.admin')

@section('head')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .page-header {
            background: white;
            color: #2d3748;
            padding: 25px 30px;
            border-radius: 16px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-bottom: 3px solid #f0f0f0;
        }
        
        .page-header h3 {
            font-weight: 600;
            letter-spacing: -0.3px;
            margin: 0;
            color: #2d3748;
        }
        
        .card {
            border-radius: 20px;
            border: none;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            margin-bottom: 20px;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }
        
        .form-label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .supplier-info-box {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            min-height: 120px;
            transition: all 0.3s ease;
        }
        
        .supplier-info-box:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        }
        
        .voucher-table {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-top: 20px;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead {
            background: #f8f9fa;
            color: #2d3748;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .table thead th {
            border: none;
            padding: 16px 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            font-size: 14px;
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .table tbody tr:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            transform: scale(1.005);
        }
        
        .table tbody td {
            padding: 12px;
            vertical-align: middle;
        }
        
        .table tbody td input,
        .table tbody td select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            font-size: 14px;
        }
        
        .btn-add-row {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            border: none;
            padding: 10px 25px;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
        }
        
        .btn-add-row:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(72, 187, 120, 0.4);
        }
        
        .btn-create-voucher {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 14px 40px;
            color: white;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
            font-size: 16px;
        }
        
        .btn-create-voucher::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-create-voucher:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-create-voucher:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
            color: white;
        }
        
        .btn-view-file {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
            border: none;
            padding: 6px 15px;
            color: white;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 13px;
        }
        
        .btn-view-file:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(66, 153, 225, 0.4);
        }
        
        .btn-remove {
            background: linear-gradient(135deg, #fc8181 0%, #f56565 100%);
            border: none;
            padding: 6px 15px;
            color: white;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 13px;
        }
        
        .btn-remove:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(252, 129, 129, 0.4);
        }
        
        .amount-summary {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 2px solid #e2e8f0;
        }
        
        .amount-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .amount-row label {
            font-weight: 600;
            color: #4a5568;
            font-size: 15px;
        }
        
        .amount-row input {
            width: 200px;
            text-align: right;
            font-weight: 600;
            font-size: 16px;
        }
        
        .amount-row.total {
            border-top: 2px solid #667eea;
            padding-top: 15px;
            margin-top: 10px;
        }
        
        .amount-row.total label {
            color: #667eea;
            font-size: 18px;
        }
        
        .amount-in-words {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
            border-left: 4px solid #667eea;
        }
        
        .amount-in-words label {
            font-weight: 600;
            color: #4a5568;
            font-size: 13px;
            margin-bottom: 5px;
            display: block;
        }
        
        .amount-in-words .word-value {
            font-weight: 500;
            color: #2d3748;
            font-size: 16px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .section-divider {
            height: 2px;
            background: linear-gradient(to right, transparent, #667eea, transparent);
            margin: 30px 0;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
            border: none;
            padding: 10px 20px;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(66, 153, 225, 0.3);
        }
        
        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(66, 153, 225, 0.4);
            color: white;
        }
        
        .btn-save {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            border: none;
            padding: 10px 20px;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
        }
        
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(72, 187, 120, 0.4);
            color: white;
        }
    </style>
@endsection

@section('content')
    @php
        $today = now()->format('Y-m-d');
        $branchName = session('branch_name') ?? 'N/A';
        $userName = session('username') ?? 'User';
    @endphp
    <div class="container-fluid">
        <div class="row mt-3">
            <div class="col-12">
                <div class="page-header">
                    <h3 class="mb-0"><i class="ri-file-text-line me-2"></i>Create Payment Voucher</h3>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form id="voucherForm">
                            @csrf
                            <input type="hidden" id="show_date" name="show_date" value="{{ $today }}">
                            <input type="hidden" id="amountInWordsInput" name="amount_in_words" value="Zero">
                            <input type="hidden" id="debit_account_id" name="debit_account_id" value="">
                            
                            <!-- Header Section -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="voucher_no" class="form-label">Voucher No</label>
                                            <input type="text" class="form-control" id="voucher_no" name="voucher_no" placeholder="Auto generated" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="type" class="form-label">Type</label>
                                            <select class="form-select" id="type" name="type">
                                                <option value="supplier">Supplier / Expenses</option>
                                                <option value="salary">Salary</option>
                                                <option value="utility">Utility Bills</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-8">
                                            <label for="debit_account" class="form-label">Debit Account</label>
                                            <select class="form-select" id="debit_account" name="debit_account_type">
                                                <option value="">Select account</option>
                                                <option value="supplier">Supplier payable</option>
                                                <option value="salary">Salary payable</option>
                                                <option value="utility">Utility expense</option>
                                                <option value="general_expense">General expense</option>
                                            </select>
                                            <small class="text-muted">(Vendor or expenses account)</small>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="due_date" class="form-label">Due Date</label>
                                            <input type="date" class="form-control" id="due_date" name="due_date">
                                        </div>
                                    </div>

                                    <div class="row" id="supplierRow">
                                        <div class="col-12">
                                            <label for="supplier_id" class="form-label">Supplier</label>
                                            <select class="form-select" id="supplier_id" name="supplier_id" disabled>
                                                <option value="">Select supplier</option>
                                            </select>
                                            <small class="text-muted" id="supplierHelper">Required for supplier vouchers</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Branch</label>
                                            <div class="form-control" style="background-color: #f8f9fa; border: none;" id="branchDisplay">{{ $branchName }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Date</label>
                                            <div class="form-control" style="background-color: #f8f9fa; border: none;" id="showDateDisplay" data-value="{{ $today }}">{{ $today }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">User</label>
                                            <div class="form-control" style="background-color: #f8f9fa; border: none;" id="userDisplay">{{ $userName }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="credit_account" class="form-label">Credit Account</label>
                                            <select class="form-select" id="credit_account" name="credit_account">
                                                <option value="cash">Cash / Bank</option>
                                                <option value="bank">Bank Account</option>
                                                <option value="petty_cash">Petty Cash</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Supplier Details - Full Width -->
                            <div class="card mb-4" style="border: 2px solid #e2e8f0; border-radius: 12px;">
                                <div class="card-body" style="padding: 20px;">
                                    <h6 class="mb-3"><i class="ri-information-line me-2"></i>Supplier Details</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead style="background: #f8f9fa;">
                                                <tr>
                                                    <th style="font-size: 13px;">Supplier No</th>
                                                    <th style="font-size: 13px;">Company / Supplier</th>
                                                    <th style="font-size: 13px;">Contact No</th>
                                                    <th style="font-size: 13px;">Address</th>
                                                </tr>
                                            </thead>
                                            <tbody id="supplierListBody">
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted" style="font-size: 13px; padding: 20px;">
                                                        <i class="ri-information-line me-2"></i>Select a supplier from Debit Account
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="section-divider"></div>

                            <!-- Items Table -->
                            <div class="table-responsive voucher-table">
                                <table class="table" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">S. No</th>
                                            <th style="width: 25%;">Description</th>
                                            <th style="width: 15%;">Amount</th>
                                            <th style="width: 15%;">Invoice / Bill No</th>
                                            <th style="width: 12%;">Date</th>
                                            <th style="width: 15%;">Files</th>
                                            <th style="width: 120px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="voucher-row" data-row="0">
                                            <td class="text-center row-index">1</td>
                                            <td><input type="text" class="form-control item-description" placeholder="Item description" /></td>
                                            <td><input type="number" class="form-control item-amount" placeholder="0.00" step="0.01" min="0" /></td>
                                            <td><input type="text" class="form-control item-invoice" placeholder="Invoice or bill no" /></td>
                                            <td><input type="date" class="form-control item-date" /></td>
                                            <td>
                                                <input type="file" class="form-control item-file" style="font-size: 12px;" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-view-file btn-sm me-1">View</button>
                                                <button type="button" class="btn btn-remove btn-sm">Remove</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                <button type="button" class="btn btn-add-row" id="addRowBtn">
                                    <i class="ri-add-line me-2"></i>Add new Row
                                </button>
                            </div>

                            <div class="section-divider"></div>

                            <!-- Amount Summary -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="amount-summary">
                                        <div class="amount-row">
                                            <label>Total Amount</label>
                                            <input type="text" class="form-control" id="totalAmount" name="total_amount" value="0.00" readonly>
                                        </div>
                                        <div class="amount-row">
                                            <label>Discount Amount</label>
                                            <input type="number" class="form-control" id="discountAmount" name="discount_amount" value="0.00" step="0.01" min="0">
                                        </div>
                                        <div class="amount-row">
                                            <label>Tax Amount</label>
                                            <input type="number" class="form-control" id="taxAmount" name="tax_amount" value="0.00" step="0.01" min="0">
                                        </div>
                                        <div class="amount-row total">
                                            <label>Sub Total Amount</label>
                                            <input type="text" class="form-control" id="subTotalAmount" name="sub_total_amount" value="0.00" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card mb-3" style="border: 2px solid #e2e8f0; border-radius: 12px;">
                                        <div class="card-body" style="padding: 20px;">
                                            <div class="amount-in-words">
                                                <label>Amount in words</label>
                                                <div class="word-value" id="amountInWords">Zero</div>
                                            </div>
                                            
                                            <div class="mt-3">
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-edit flex-fill">
                                                        <i class="ri-edit-line me-2"></i>Edit
                                                    </button>
                                                    <button type="button" class="btn btn-save flex-fill">
                                                        <i class="ri-save-line me-2"></i>Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card" style="border: 2px solid #e2e8f0; border-radius: 12px;">
                                        <div class="card-body" style="padding: 20px;">
                                            <button type="submit" class="btn btn-create-voucher w-100">
                                                <i class="ri-file-add-line me-2"></i>Create Voucher
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            const routes = {!! json_encode([
                'store' => route('payment-vouchers.store'),
                'nextNumber' => route('payment-vouchers.next-number'),
                'suppliers' => route('suppliers.index'),
            ]) !!};

            const $voucherForm = $('#voucherForm');
            const $itemsTableBody = $('#itemsTable tbody');
            const $supplierSelect = $('#supplier_id');
            const $supplierHelper = $('#supplierHelper');
            const $amountWordsDisplay = $('#amountInWords');
            const $amountWordsInput = $('#amountInWordsInput');
            const $debitAccountId = $('#debit_account_id');
            const $totalAmount = $('#totalAmount');
            const $subTotalAmount = $('#subTotalAmount');
            const $discountAmount = $('#discountAmount');
            const $taxAmount = $('#taxAmount');
            const $type = $('#type');
            const $voucherNo = $('#voucher_no');
            const $showDateDisplay = $('#showDateDisplay');
            const $showDateHidden = $('#show_date');
            const $submitBtn = $('.btn-create-voucher');

            let suppliers = [];
            let allowAutoWords = true;

            bootstrapPage();

            function bootstrapPage() {
                renumberRows();
                updateViewButtonState($itemsTableBody.find('tr').first());
                $showDateHidden.val($showDateDisplay.data('value'));
                fetchNextVoucherNumber();
                loadSuppliers();
                syncSupplierState();
                calculateTotals();
            }

            function fetchNextVoucherNumber() {
                $.getJSON(routes.nextNumber)
                    .done(function(response) {
                        if (response && response.data && response.data.voucher_no) {
                            $voucherNo.val(response.data.voucher_no);
                        }
                    });
            }

            function loadSuppliers() {
                $.getJSON(routes.suppliers)
                    .done(function(response) {
                        suppliers = response && response.data ? response.data : [];
                        populateSupplierOptions();
                    })
                    .fail(function() {
                        suppliers = [];
                        populateSupplierOptions();
                    });
            }

            function populateSupplierOptions() {
                const current = $supplierSelect.val();
                $supplierSelect.empty().append('<option value="">Select supplier</option>');
                suppliers.forEach(function(supplier) {
                    const name = supplier && supplier.company_name ? supplier.company_name : '';
                    $supplierSelect.append(`<option value="${supplier.id}">${escapeHtml(name)}</option>`);
                });

                if (current && suppliers.some(function(item) { return String(item.id) === String(current); })) {
                    $supplierSelect.val(current);
                    renderSupplierDetails(getSupplierById(current));
                } else {
                    $supplierSelect.val('');
                    resetSupplierDetails();
                }

                syncSupplierState();
            }

            function getSupplierById(id) {
                return suppliers.find(function(item) {
                    return String(item.id) === String(id);
                });
            }

            function renderSupplierDetails(supplier) {
                if (!supplier) {
                    resetSupplierDetails();
                    return;
                }

                const html = `
                    <tr>
                        <td>${escapeHtml(supplier && supplier.supplier_no ? supplier.supplier_no : '')}</td>
                        <td>${escapeHtml(supplier && supplier.company_name ? supplier.company_name : '')}</td>
                        <td>${escapeHtml(supplier && supplier.contact_number ? supplier.contact_number : '')}</td>
                        <td>${escapeHtml(supplier && supplier.address ? supplier.address : '')}</td>
                    </tr>
                `;
                $('#supplierListBody').html(html);
            }

            function resetSupplierDetails() {
                $('#supplierListBody').html(`
                    <tr>
                        <td colspan="4" class="text-center text-muted" style="font-size: 13px; padding: 20px;">
                            <i class="ri-information-line me-2"></i>Select a supplier to view details
                        </td>
                    </tr>
                `);
            }

            function syncSupplierState() {
                const isSupplierType = $type.val() === 'supplier';
                $('#supplierRow').toggle(isSupplierType);
                $supplierSelect.prop('disabled', !isSupplierType);
                $supplierHelper.text(isSupplierType ? 'Required for supplier vouchers' : 'Disabled for non-supplier vouchers');

                if ($type.val() === 'supplier') {
                    $('#debit_account').val('supplier');
                } else if ($type.val() === 'salary') {
                    $('#debit_account').val('salary');
                } else if ($type.val() === 'utility') {
                    $('#debit_account').val('utility');
                }

                if (isSupplierType && $supplierSelect.val()) {
                    renderSupplierDetails(getSupplierById($supplierSelect.val()));
                }

                if (!isSupplierType) {
                    $supplierSelect.val('');
                    resetSupplierDetails();
                }

                syncDebitAccountId();
            }

            function syncDebitAccountId() {
                const supplierId = $supplierSelect.val();
                const isSupplierType = $type.val() === 'supplier';
                $debitAccountId.val(isSupplierType && supplierId ? supplierId : '');
            }

            function renumberRows() {
                $itemsTableBody.find('tr').each(function(index) {
                    $(this).attr('data-row', index);
                    $(this).find('.row-index').text(index + 1);
                });
            }

            function buildRowTemplate() {
                return `
                    <tr class="voucher-row" data-row="-1">
                        <td class="text-center row-index"></td>
                        <td><input type="text" class="form-control item-description" placeholder="Item description" /></td>
                        <td><input type="number" class="form-control item-amount" placeholder="0.00" step="0.01" min="0" /></td>
                        <td><input type="text" class="form-control item-invoice" placeholder="Invoice or bill no" /></td>
                        <td><input type="date" class="form-control item-date" /></td>
                        <td><input type="file" class="form-control item-file" style="font-size: 12px;" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" /></td>
                        <td>
                            <button type="button" class="btn btn-view-file btn-sm me-1" disabled>View</button>
                            <button type="button" class="btn btn-remove btn-sm">Remove</button>
                        </td>
                    </tr>
                `;
            }

            function updateViewButtonState($row) {
                const fileInput = $row.find('.item-file')[0];
                const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
                $row.find('.btn-view-file').prop('disabled', !hasFile);
            }

            function calculateTotals() {
                let total = 0;
                $('.item-amount').each(function() {
                    const value = parseFloat($(this).val());
                    if (!isNaN(value) && value >= 0) {
                        total += value;
                    }
                });

                total = Number(total.toFixed(2));
                const discount = sanitizeCurrencyInput($discountAmount);
                const tax = sanitizeCurrencyInput($taxAmount);
                const subTotal = Number((total - discount + tax).toFixed(2));

                $totalAmount.val(total.toFixed(2));
                $subTotalAmount.val(subTotal.toFixed(2));

                const words = numberToWords(subTotal);
                if (allowAutoWords) {
                    $amountWordsDisplay.text(words);
                    $amountWordsInput.val(words);
                }
            }

            function sanitizeCurrencyInput($input) {
                let value = parseFloat($input.val());
                if (isNaN(value) || value < 0) {
                    value = 0;
                }
                $input.val(value.toFixed(2));
                return value;
            }

            function numberToWords(amount) {
                if (!isFinite(amount)) {
                    return 'Zero';
                }

                if (amount === 0) {
                    return 'Zero';
                }

                const negative = amount < 0;
                amount = Math.abs(amount);

                const intPart = Math.floor(amount);
                const decPart = Math.round((amount - intPart) * 100);

                let words = convertInteger(intPart);
                if (!words) {
                    words = 'Zero';
                }

                if (decPart > 0) {
                    words += ' and ' + decPart.toString().padStart(2, '0') + '/100';
                }

                return (negative ? 'Minus ' : '') + words;
            }

            function convertInteger(number) {
                if (number === 0) {
                    return '';
                }

                const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
                const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
                const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

                let result = '';

                if (number >= 1000000) {
                    result += convertInteger(Math.floor(number / 1000000)) + ' Million ';
                    number %= 1000000;
                }

                if (number >= 1000) {
                    result += convertInteger(Math.floor(number / 1000)) + ' Thousand ';
                    number %= 1000;
                }

                if (number >= 100) {
                    result += ones[Math.floor(number / 100)] + ' Hundred ';
                    number %= 100;
                }

                if (number >= 20) {
                    result += tens[Math.floor(number / 10)];
                    if (number % 10) {
                        result += ' ' + ones[number % 10];
                    }
                } else if (number >= 10) {
                    result += teens[number - 10];
                } else if (number > 0) {
                    result += ones[number];
                }

                return result.trim();
            }

            function escapeHtml(text) {
                if (text === undefined || text === null) {
                    text = '';
                }
                return $('<div>').text(text).html();
            }

            function gatherItems() {
                const items = [];
                let hasInvalidAmount = false;
                let hasMissingDescription = false;

                $itemsTableBody.find('tr').each(function() {
                    const $row = $(this);
                    const description = $.trim($row.find('.item-description').val());
                    const amountValue = parseFloat($row.find('.item-amount').val());
                    const invoiceNo = $.trim($row.find('.item-invoice').val());
                    const invoiceDate = $row.find('.item-date').val();

                    const hasData = description || invoiceNo || invoiceDate || (!isNaN(amountValue) && amountValue > 0);

                    if (!hasData) {
                        $row.find('.item-amount').removeClass('is-invalid');
                        $row.find('.item-description').removeClass('is-invalid');
                        return;
                    }

                    $row.find('.item-amount').removeClass('is-invalid');
                    if (!description) {
                        hasMissingDescription = true;
                        $row.find('.item-description').addClass('is-invalid');
                        return;
                    } else {
                        $row.find('.item-description').removeClass('is-invalid');
                    }

                    if (isNaN(amountValue) || amountValue <= 0) {
                        hasInvalidAmount = true;
                        $row.find('.item-amount').addClass('is-invalid');
                        return;
                    }

                    $row.find('.item-amount').removeClass('is-invalid');

                    items.push({
                        description: description,
                        amount: amountValue,
                        invoiceNo: invoiceNo,
                        invoiceDate: invoiceDate,
                        $row: $row
                    });
                });

                if (hasInvalidAmount) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Amount',
                        text: 'Each item with details must include an amount greater than zero.',
                        confirmButtonColor: '#667eea'
                    });
                    return null;
                }

                if (hasMissingDescription) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Missing Description',
                        text: 'Each item must include a description.',
                        confirmButtonColor: '#667eea'
                    });
                    return null;
                }

                if (items.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Items',
                        text: 'Add at least one voucher item before saving.',
                        confirmButtonColor: '#667eea'
                    });
                    return null;
                }

                return items;
            }

            function buildFormData(items) {
                const baseData = new FormData($voucherForm[0]);
                const formData = new FormData();

                baseData.forEach(function(value, key) {
                    formData.append(key, value);
                });

                formData.set('total_amount', $totalAmount.val());
                formData.set('discount_amount', $discountAmount.val());
                formData.set('tax_amount', $taxAmount.val());
                formData.set('sub_total_amount', $subTotalAmount.val());
                formData.set('amount_in_words', $amountWordsInput.val());
                formData.set('show_date', $showDateHidden.val());

                items.forEach(function(item, index) {
                    formData.append(`items[${index}][serial_no]`, index + 1);
                    formData.append(`items[${index}][description]`, item.description);
                    formData.append(`items[${index}][amount]`, item.amount.toFixed(2));

                    if (item.invoiceNo) {
                        formData.append(`items[${index}][invoice_no]`, item.invoiceNo);
                    }

                    if (item.invoiceDate) {
                        formData.append(`items[${index}][invoice_date]`, item.invoiceDate);
                    }

                    const fileInput = item.$row.find('.item-file')[0];
                    if (fileInput && fileInput.files && fileInput.files.length) {
                        formData.append(`items[${index}][files]`, fileInput.files[0]);
                    }
                });

                return formData;
            }

            function resetForm() {
                const defaultDate = $showDateDisplay.data('value');
                $voucherForm[0].reset();
                $showDateHidden.val(defaultDate);
                $itemsTableBody.html(buildRowTemplate());
                renumberRows();
                updateViewButtonState($itemsTableBody.find('tr').first());
                resetSupplierDetails();
                syncSupplierState();
                allowAutoWords = true;
                $('.btn-edit').prop('disabled', false);
                $('.btn-save').prop('disabled', true);
                $amountWordsDisplay.removeAttr('contenteditable');
                calculateTotals();
                fetchNextVoucherNumber();
            }

            $('#addRowBtn').on('click', function() {
                $itemsTableBody.append(buildRowTemplate());
                const $newRow = $itemsTableBody.find('tr').last();
                updateViewButtonState($newRow);
                renumberRows();
            });

            $(document).on('click', '.btn-remove', function() {
                if ($itemsTableBody.find('tr').length === 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cannot Remove',
                        text: 'At least one row is required.',
                        confirmButtonColor: '#667eea'
                    });
                    return;
                }

                $(this).closest('tr').remove();
                renumberRows();
                allowAutoWords = true;
                calculateTotals();
            });

            $(document).on('change', '.item-file', function() {
                updateViewButtonState($(this).closest('tr'));
            });

            $(document).on('click', '.btn-view-file', function() {
                const $row = $(this).closest('tr');
                const fileInput = $row.find('.item-file')[0];

                if (!fileInput || !fileInput.files || !fileInput.files.length) {
                    Swal.fire({
                        icon: 'info',
                        title: 'No File',
                        text: 'Attach a file before viewing.',
                        confirmButtonColor: '#667eea'
                    });
                    return;
                }

                const file = fileInput.files[0];
                const url = URL.createObjectURL(file);
                window.open(url, '_blank');
                setTimeout(function() {
                    URL.revokeObjectURL(url);
                }, 5000);
            });

            $(document).on('input', '.item-amount', function() {
                const value = parseFloat($(this).val());
                if (value < 0) {
                    $(this).val('');
                }
                allowAutoWords = true;
                calculateTotals();
            });

            $discountAmount.on('input', function() {
                allowAutoWords = true;
                calculateTotals();
            });

            $taxAmount.on('input', function() {
                allowAutoWords = true;
                calculateTotals();
            });

            $type.on('change', function() {
                syncSupplierState();
            });

            $supplierSelect.on('change', function() {
                const supplier = getSupplierById($(this).val());
                renderSupplierDetails(supplier);
                syncDebitAccountId();
            });

            $('.btn-edit').on('click', function() {
                $amountWordsDisplay.attr('contenteditable', 'true').focus();
                $(this).prop('disabled', true);
                $('.btn-save').prop('disabled', false);
                allowAutoWords = false;
            });

            $('.btn-save').on('click', function() {
                const text = $.trim($amountWordsDisplay.text());
                $amountWordsDisplay.removeAttr('contenteditable');
                $amountWordsInput.val(text || 'Zero');
                $(this).prop('disabled', true);
                $('.btn-edit').prop('disabled', false);
                allowAutoWords = false;
            }).prop('disabled', true);

            $voucherForm.on('submit', function(event) {
                event.preventDefault();

                if ($type.val() === 'supplier' && !$supplierSelect.val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Supplier Required',
                        text: 'Select a supplier before creating this voucher.',
                        confirmButtonColor: '#667eea'
                    });
                    return;
                }

                const wasAuto = allowAutoWords;
                calculateTotals();
                allowAutoWords = wasAuto;

                const items = gatherItems();
                if (!items) {
                    return;
                }

                const formData = buildFormData(items);

                $submitBtn.prop('disabled', true).addClass('disabled');

                $.ajax({
                    url: routes.store,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                })
                    .done(function(response) {
                        const message = response && response.message ? response.message : 'Payment voucher created successfully';
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: message,
                            confirmButtonColor: '#667eea'
                        }).then(function() {
                            resetForm();
                        });
                    })
                    .fail(function(xhr) {
                        let text = 'Unable to create payment voucher. Please try again.';
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            text = errors.join('\n');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            text = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Request Failed',
                            text: text,
                            confirmButtonColor: '#667eea'
                        });
                    })
                    .always(function() {
                        $submitBtn.prop('disabled', false).removeClass('disabled');
                    });
            });
        });
    </script>
@endsection
