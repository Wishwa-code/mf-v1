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
                            
                            <!-- Header Section -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="voucher_no" class="form-label">Voucher No</label>
                                            <input type="text" class="form-control" id="voucher_no" name="voucher_no" placeholder="Auto generate">
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
                                            <select class="form-select" id="debit_account" name="debit_account">
                                                <option value="">Select Account</option>
                                                <option value="salary">Salary / Electricity / Supplier0001</option>
                                                <option value="expenses">General Expenses</option>
                                                <option value="supplier001">Supplier001</option>
                                            </select>
                                            <small class="text-muted">(Vendor or expenses account)</small>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="due_date" class="form-label">Due Date</label>
                                            <input type="date" class="form-control" id="due_date" name="due_date">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Branch</label>
                                            <div class="form-control" style="background-color: #f8f9fa; border: none;">Kalutara</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Date</label>
                                            <div class="form-control" style="background-color: #f8f9fa; border: none;">2025-08-20</div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">User</label>
                                            <div class="form-control" style="background-color: #f8f9fa; border: none;">Admin</div>
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
                                        <tr>
                                            <td class="text-center">1</td>
                                            <td><input type="text" class="form-control" placeholder="Text field input" /></td>
                                            <td><input type="number" class="form-control item-amount" placeholder="0.00" step="0.01" /></td>
                                            <td><input type="text" class="form-control" placeholder="Number input field" /></td>
                                            <td><input type="date" class="form-control" /></td>
                                            <td>
                                                <input type="file" class="form-control" multiple style="font-size: 12px;" />
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-view-file btn-sm me-1">View</button>
                                                <button type="button" class="btn btn-remove btn-sm">Remove</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">2</td>
                                            <td><input type="text" class="form-control" /></td>
                                            <td><input type="number" class="form-control item-amount" placeholder="0.00" step="0.01" /></td>
                                            <td><input type="text" class="form-control" /></td>
                                            <td><input type="date" class="form-control" /></td>
                                            <td><input type="file" class="form-control" multiple style="font-size: 12px;" /></td>
                                            <td>
                                                <button type="button" class="btn btn-view-file btn-sm me-1">View</button>
                                                <button type="button" class="btn btn-remove btn-sm">Remove</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">3</td>
                                            <td><input type="text" class="form-control" /></td>
                                            <td><input type="number" class="form-control item-amount" placeholder="0.00" step="0.01" /></td>
                                            <td><input type="text" class="form-control" /></td>
                                            <td><input type="date" class="form-control" /></td>
                                            <td><input type="file" class="form-control" multiple style="font-size: 12px;" /></td>
                                            <td>
                                                <button type="button" class="btn btn-view-file btn-sm me-1">View</button>
                                                <button type="button" class="btn btn-remove btn-sm">Remove</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">4</td>
                                            <td><input type="text" class="form-control" /></td>
                                            <td><input type="number" class="form-control item-amount" placeholder="0.00" step="0.01" /></td>
                                            <td><input type="text" class="form-control" /></td>
                                            <td><input type="date" class="form-control" /></td>
                                            <td><input type="file" class="form-control" multiple style="font-size: 12px;" /></td>
                                            <td>
                                                <button type="button" class="btn btn-view-file btn-sm me-1">View</button>
                                                <button type="button" class="btn btn-remove btn-sm">Remove</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">5</td>
                                            <td><input type="text" class="form-control" /></td>
                                            <td><input type="number" class="form-control item-amount" placeholder="0.00" step="0.01" /></td>
                                            <td><input type="text" class="form-control" /></td>
                                            <td><input type="date" class="form-control" /></td>
                                            <td><input type="file" class="form-control" multiple style="font-size: 12px;" /></td>
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
                                            <input type="text" class="form-control" id="totalAmount" value="00.00" readonly>
                                        </div>
                                        <div class="amount-row">
                                            <label>Discount Amount</label>
                                            <input type="number" class="form-control" id="discountAmount" value="0.00" step="0.01">
                                        </div>
                                        <div class="amount-row">
                                            <label>Tax Amount</label>
                                            <input type="number" class="form-control" id="taxAmount" value="0.00" step="0.01">
                                        </div>
                                        <div class="amount-row total">
                                            <label>Sub Total Amount</label>
                                            <input type="text" class="form-control" id="subTotalAmount" value="00.00" readonly>
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
        $(document).ready(function() {
            let rowCounter = 6;

            // Calculate totals
            function calculateTotals() {
                let total = 0;
                $('.item-amount').each(function() {
                    const value = parseFloat($(this).val()) || 0;
                    total += value;
                });
                
                $('#totalAmount').val(total.toFixed(2));
                
                const discount = parseFloat($('#discountAmount').val()) || 0;
                const tax = parseFloat($('#taxAmount').val()) || 0;
                const subTotal = total - discount + tax;
                
                $('#subTotalAmount').val(subTotal.toFixed(2));
                
                // Convert to words
                $('#amountInWords').text(numberToWords(subTotal));
            }

            // Add new row
            $('#addRowBtn').on('click', function() {
                const newRow = `
                    <tr>
                        <td class="text-center">${rowCounter}</td>
                        <td><input type="text" class="form-control" /></td>
                        <td><input type="number" class="form-control item-amount" placeholder="0.00" step="0.01" /></td>
                        <td><input type="text" class="form-control" /></td>
                        <td><input type="date" class="form-control" /></td>
                        <td><input type="file" class="form-control" multiple style="font-size: 12px;" /></td>
                        <td>
                            <button type="button" class="btn btn-view-file btn-sm me-1">View</button>
                            <button type="button" class="btn btn-remove btn-sm">Remove</button>
                        </td>
                    </tr>
                `;
                $('#itemsTable tbody').append(newRow);
                rowCounter++;
            });

            // Remove row
            $(document).on('click', '.btn-remove', function() {
                if ($('#itemsTable tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                    calculateTotals();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cannot Remove',
                        text: 'At least one row is required',
                        confirmButtonColor: '#667eea'
                    });
                }
            });

            // Calculate on input change
            $(document).on('input', '.item-amount, #discountAmount, #taxAmount', function() {
                calculateTotals();
            });

            // Form submission
            $('#voucherForm').on('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Success!',
                    text: 'Payment voucher created successfully',
                    icon: 'success',
                    confirmButtonColor: '#667eea'
                });
            });

            // Number to words conversion
            function numberToWords(num) {
                if (num === 0) return 'Zero';
                
                const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
                const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
                const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
                
                function convertHundreds(n) {
                    if (n === 0) return '';
                    if (n < 10) return ones[n];
                    if (n < 20) return teens[n - 10];
                    if (n < 100) return tens[Math.floor(n / 10)] + (n % 10 ? ' ' + ones[n % 10] : '');
                    return ones[Math.floor(n / 100)] + ' Hundred' + (n % 100 ? ' ' + convertHundreds(n % 100) : '');
                }
                
                const intPart = Math.floor(num);
                const decPart = Math.round((num - intPart) * 100);
                
                let result = '';
                
                if (intPart >= 1000000) {
                    result += convertHundreds(Math.floor(intPart / 1000000)) + ' Million ';
                    intPart %= 1000000;
                }
                if (intPart >= 1000) {
                    result += convertHundreds(Math.floor(intPart / 1000)) + ' Thousand ';
                    intPart %= 1000;
                }
                if (intPart > 0) {
                    result += convertHundreds(intPart);
                }
                
                if (decPart > 0) {
                    result += ' and ' + decPart + '/100';
                }
                
                return result.trim() || 'Zero';
            }
        });
    </script>
@endsection
