@extends('layout.admin')

@section('head')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        
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
        
        .filter-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid rgba(255, 255, 255, 0.18);
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
        
        .voucher-table {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .table thead {
            background: #f8f9fa;
            color: #495057;
        }
        
        .table thead th {
            border: none;
            padding: 18px;
            font-weight: 600;
            letter-spacing: 0.5px;
            font-size: 14px;
            border-bottom: 2px solid #dee2e6;
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .table tbody tr:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            transform: scale(1.01);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .table tbody td {
            padding: 16px;
            vertical-align: middle;
        }
        
        .btn {
            border-radius: 10px;
            padding: 10px 18px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .card {
            border-radius: 20px;
            border: none;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }
        
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            border-radius: 20px 20px 0 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
            padding: 20px 25px;
        }
        
        .modal-body {
            padding: 25px;
        }
        
        h5 {
            font-weight: 700;
            color: #2d3748;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mt-3">
            <div class="col-12">
                <div class="page-header">
                    <h3 class="mb-0"><i class="ri-money-dollar-circle-line me-2"></i>Voucher Payments</h3>
                </div>

                <!-- Filter Section -->
                <div class="card mb-4">
                    <div class="card-body filter-section">
                        <h5 class="mb-3"><i class="ri-filter-3-line me-2"></i>Filters</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Date From</label>
                                <input type="date" class="form-control" id="dateFrom">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date To</label>
                                <input type="date" class="form-control" id="dateTo">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Type</label>
                                <select class="form-select" id="typeFilter">
                                    <option value="all">All</option>
                                    <option value="supplier">Supplier</option>
                                    <option value="expense">Expense</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" id="dynamicLabel">Supplier / Expense</label>
                                <select class="form-select" id="dynamicFilter">
                                    <option value="">All</option>
                                    <option value="1">ABC Company Ltd</option>
                                    <option value="2">XYZ Traders</option>
                                    <option value="3">Office Supplies</option>
                                    <option value="4">Maintenance</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 text-end">
                                <button class="btn btn-secondary me-2"><i class="ri-refresh-line me-2"></i>Reset</button>
                                <button class="btn btn-primary"><i class="ri-search-line me-2"></i>Search</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Vouchers Table -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="ri-money-dollar-circle-line me-2"></i>Voucher Payments List</h5>
                        <div class="table-responsive">
                            <table class="table table-hover voucher-table">
                                <thead>
                                    <tr>
                                        <th>Voucher No</th>
                                        <th>Voucher Date</th>
                                        <th>Due Date</th>
                                        <th>Type</th>
                                        <th>Debit Account</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="voucherTableBody">
                                    <!-- Sample data -->
                                    <tr>
                                        <td><strong>V001</strong></td>
                                        <td>2025-01-15</td>
                                        <td>2025-01-25</td>
                                        <td><span class="badge bg-primary">Supplier</span></td>
                                        <td>ABC Company Ltd</td>
                                        <td><strong>Rs. 45,000.00</strong></td>
                                        <td>
                                            <button class="btn btn-sm btn-success" onclick="viewVoucher(1)">
                                                <i class="ri-eye-line me-1"></i>View
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="makePayment('V001')">
                                                <i class="ri-money-dollar-circle-line me-1"></i>Make Payment
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>V002</strong></td>
                                        <td>2025-01-16</td>
                                        <td>2025-01-26</td>
                                        <td><span class="badge bg-warning">Expense</span></td>
                                        <td>Office Supplies</td>
                                        <td><strong>Rs. 15,500.00</strong></td>
                                        <td>
                                            <button class="btn btn-sm btn-success" onclick="viewVoucher(2)">
                                                <i class="ri-eye-line me-1"></i>View
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="makePayment('V002')">
                                                <i class="ri-money-dollar-circle-line me-1"></i>Make Payment
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>V003</strong></td>
                                        <td>2025-01-17</td>
                                        <td>2025-01-27</td>
                                        <td><span class="badge bg-primary">Supplier</span></td>
                                        <td>XYZ Traders</td>
                                        <td><strong>Rs. 28,750.00</strong></td>
                                        <td>
                                            <button class="btn btn-sm btn-success" onclick="viewVoucher(3)">
                                                <i class="ri-eye-line me-1"></i>View
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="makePayment('V003')">
                                                <i class="ri-money-dollar-circle-line me-1"></i>Make Payment
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Voucher Modal -->
    <div class="modal fade" id="viewVoucherModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white"><i class="ri-file-text-line me-2"></i>Voucher Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Voucher No:</strong> <span id="modalVoucherNo">V001</span></p>
                            <p><strong>Voucher Date:</strong> <span id="modalVoucherDate">2025-01-15</span></p>
                            <p><strong>Due Date:</strong> <span id="modalDueDate">2025-01-25</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Type:</strong> <span id="modalType">Supplier</span></p>
                            <p><strong>Debit Account:</strong> <span id="modalDebitAccount">ABC Company Ltd</span></p>
                        </div>
                    </div>
                    
                    <h6 class="mt-4 mb-3">Items</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody id="modalItemsBody">
                                <tr>
                                    <td>Office Chairs</td>
                                    <td>10</td>
                                    <td>Rs. 4,000.00</td>
                                    <td>Rs. 40,000.00</td>
                                </tr>
                                <tr>
                                    <td>Delivery Charges</td>
                                    <td>1</td>
                                    <td>Rs. 5,000.00</td>
                                    <td>Rs. 5,000.00</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="table-secondary">
                                    <td colspan="3" class="text-end"><strong>Sub Total:</strong></td>
                                    <td><strong id="modalSubTotal">Rs. 45,000.00</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="makePaymentBtn"><i class="ri-money-dollar-circle-line me-1"></i>Make Payment</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Type filter change event
            $('#typeFilter').on('change', function() {
                var type = $(this).val();
                var label = $('#dynamicLabel');
                var select = $('#dynamicFilter');
                
                // Clear current options
                select.empty();
                select.append('<option value="">All</option>');
                
                if (type === 'supplier') {
                    label.text('Supplier');
                    // Add supplier options
                    select.append('<option value="1">ABC Company Ltd</option>');
                    select.append('<option value="2">XYZ Traders</option>');
                } else if (type === 'expense') {
                    label.text('Expense');
                    // Add expense options
                    select.append('<option value="3">Office Supplies</option>');
                    select.append('<option value="4">Maintenance</option>');
                } else {
                    label.text('Supplier / Expense');
                    // Add all options
                    select.append('<option value="1">ABC Company Ltd</option>');
                    select.append('<option value="2">XYZ Traders</option>');
                    select.append('<option value="3">Office Supplies</option>');
                    select.append('<option value="4">Maintenance</option>');
                }
            });
        });
        
        function viewVoucher(id) {
            // Hardcoded sample data based on voucher id
            const voucherData = {
                1: {
                    voucherNo: 'V001',
                    voucherDate: '2025-01-15',
                    dueDate: '2025-01-25',
                    type: 'Supplier',
                    debitAccount: 'ABC Company Ltd',
                    items: [
                        { description: 'Office Chairs', quantity: 10, unitPrice: 4000, amount: 40000 },
                        { description: 'Delivery Charges', quantity: 1, unitPrice: 5000, amount: 5000 }
                    ],
                    subTotal: 45000
                },
                2: {
                    voucherNo: 'V002',
                    voucherDate: '2025-01-16',
                    dueDate: '2025-01-26',
                    type: 'Expense',
                    debitAccount: 'Office Supplies',
                    items: [
                        { description: 'Printing Paper A4', quantity: 20, unitPrice: 500, amount: 10000 },
                        { description: 'Toner Cartridges', quantity: 5, unitPrice: 1100, amount: 5500 }
                    ],
                    subTotal: 15500
                },
                3: {
                    voucherNo: 'V003',
                    voucherDate: '2025-01-17',
                    dueDate: '2025-01-27',
                    type: 'Supplier',
                    debitAccount: 'XYZ Traders',
                    items: [
                        { description: 'Computer Monitors', quantity: 3, unitPrice: 8500, amount: 25500 },
                        { description: 'Installation Service', quantity: 1, unitPrice: 3250, amount: 3250 }
                    ],
                    subTotal: 28750
                }
            };
            
            const voucher = voucherData[id] || voucherData[1];
            
            // Populate modal fields
            $('#modalVoucherNo').text(voucher.voucherNo);
            $('#modalVoucherDate').text(voucher.voucherDate);
            $('#modalDueDate').text(voucher.dueDate);
            $('#modalType').text(voucher.type);
            $('#modalDebitAccount').text(voucher.debitAccount);
            
            // Populate items table
            let itemsHtml = '';
            voucher.items.forEach(function(item) {
                itemsHtml += `
                    <tr>
                        <td>${item.description}</td>
                        <td>${item.quantity}</td>
                        <td>Rs. ${item.unitPrice.toLocaleString('en-LK', {minimumFractionDigits: 2})}</td>
                        <td>Rs. ${item.amount.toLocaleString('en-LK', {minimumFractionDigits: 2})}</td>
                    </tr>
                `;
            });
            $('#modalItemsBody').html(itemsHtml);
            
            // Set sub total
            $('#modalSubTotal').text('Rs. ' + voucher.subTotal.toLocaleString('en-LK', {minimumFractionDigits: 2}));
            
            // Show modal
            $('#viewVoucherModal').modal('show');
        }
        
        // Make payment from table
        function makePayment(voucherNo) {
            Swal.fire({
                title: 'Make Payment?',
                text: `Are you sure you want to make payment for voucher ${voucherNo}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Make Payment',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Here you would send payment request to backend
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Made!',
                        text: `Payment for voucher ${voucherNo} has been completed successfully.`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }
        
        // Make payment button click handler from modal
        $(document).on('click', '#makePaymentBtn', function() {
            const voucherNo = $('#modalVoucherNo').text();
            
            Swal.fire({
                title: 'Make Payment?',
                text: `Are you sure you want to make payment for voucher ${voucherNo}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Make Payment',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Here you would send payment request to backend
                    // For now, just show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Made!',
                        text: `Payment for voucher ${voucherNo} has been completed successfully.`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Close modal
                    $('#viewVoucherModal').modal('hide');
                }
            });
        });
    </script>
@endsection
