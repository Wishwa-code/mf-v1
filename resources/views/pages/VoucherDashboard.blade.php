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
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(255, 255, 255, 0.18);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent 0%, rgba(102, 126, 234, 0.03) 100%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }
        
        .stat-card:hover::before {
            opacity: 1;
        }
        
        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }
        
        .stat-card:hover .stat-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 800;
            margin: 12px 0 5px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
        }
        
        .voucher-table {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .table thead th {
            border: none;
            padding: 18px;
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
            transform: scale(1.01);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .table tbody td {
            padding: 16px;
            vertical-align: middle;
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
        
        .btn-add-voucher {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 14px 30px;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
        }
        
        .btn-add-voucher::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-add-voucher:hover::before {
            width: 400px;
            height: 400px;
        }
        
        .btn-add-voucher:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.5);
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
        
        .status-badge {
            padding: 8px 18px;
            border-radius: 25px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }
        
        .status-badge:hover {
            transform: scale(1.05);
        }
        
        .status-pending { 
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            box-shadow: 0 2px 8px rgba(251, 191, 36, 0.3);
        }
        
        .status-approved { 
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }
        
        .status-rejected { 
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }
        
        .status-paid { 
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e3a8a;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
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
            border-bottom: none;
            padding: 25px 30px;
        }
        
        .modal-body {
            padding: 30px;
        }
        
        #searchVoucher {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 10px 20px;
            transition: all 0.3s;
        }
        
        #searchVoucher:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .stat-card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0"><i class="ri-file-list-3-line me-2"></i>Voucher Dashboard</h3>
                        <button class="btn btn-add-voucher" data-bs-toggle="modal" data-bs-target="#addVoucherModal">
                            <i class="ri-add-line me-2"></i>Create New Voucher
                        </button>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon" style="background-color: #e3f2fd; color: #1e88e5;">
                                    <i class="ri-file-text-line"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <div class="stat-value">245</div>
                                    <div class="stat-label">Total Vouchers</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon" style="background-color: #fff3cd; color: #ffc107;">
                                    <i class="ri-time-line"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <div class="stat-value">18</div>
                                    <div class="stat-label">Pending Approval</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon" style="background-color: #d1e7dd; color: #198754;">
                                    <i class="ri-checkbox-circle-line"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <div class="stat-value">212</div>
                                    <div class="stat-label">Approved</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon" style="background-color: #f8d7da; color: #dc3545;">
                                    <i class="ri-money-dollar-circle-line"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <div class="stat-value">Rs. 1.2M</div>
                                    <div class="stat-label">Total Amount</div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                <label class="form-label">Status</label>
                                <select class="form-select" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Supplier</label>
                                <select class="form-select" id="supplierFilter">
                                    <option value="">All Suppliers</option>
                                    <option value="1">ABC Company Ltd</option>
                                    <option value="2">XYZ Traders</option>
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

                <!-- Vouchers Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5><i class="ri-file-list-line me-2"></i>Recent Vouchers</h5>
                            <input type="text" class="form-control" id="searchVoucher" placeholder="Search vouchers..." style="width: 250px;">
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover voucher-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Voucher No</th>
                                        <th>Date</th>
                                        <th>Supplier</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="voucherTableBody">
                                    <!-- Sample data -->
                                    <tr>
                                        <td><strong>VCH-2025-001</strong></td>
                                        <td>2025-01-15</td>
                                        <td>ABC Company Ltd</td>
                                        <td>Office Supplies Purchase</td>
                                        <td><strong>Rs. 45,000</strong></td>
                                        <td><span class="status-badge status-approved">Approved</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" title="View"><i class="ri-eye-line"></i></button>
                                            <button class="btn btn-sm btn-warning" title="Edit"><i class="ri-edit-line"></i></button>
                                            <button class="btn btn-sm btn-primary" title="Print"><i class="ri-printer-line"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>VCH-2025-002</strong></td>
                                        <td>2025-01-16</td>
                                        <td>XYZ Traders</td>
                                        <td>Equipment Maintenance</td>
                                        <td><strong>Rs. 28,500</strong></td>
                                        <td><span class="status-badge status-pending">Pending</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" title="View"><i class="ri-eye-line"></i></button>
                                            <button class="btn btn-sm btn-warning" title="Edit"><i class="ri-edit-line"></i></button>
                                            <button class="btn btn-sm btn-primary" title="Print"><i class="ri-printer-line"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>VCH-2025-003</strong></td>
                                        <td>2025-01-17</td>
                                        <td>Tech Solutions Inc</td>
                                        <td>Software License</td>
                                        <td><strong>Rs. 85,000</strong></td>
                                        <td><span class="status-badge status-paid">Paid</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" title="View"><i class="ri-eye-line"></i></button>
                                            <button class="btn btn-sm btn-warning" title="Edit"><i class="ri-edit-line"></i></button>
                                            <button class="btn btn-sm btn-primary" title="Print"><i class="ri-printer-line"></i></button>
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

    <!-- Add Voucher Modal -->
    <div class="modal fade" id="addVoucherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title"><i class="ri-add-line me-2"></i>Create New Voucher</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="voucherForm">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Voucher No</label>
                                <input type="text" class="form-control" name="voucher_no" value="VCH-2025-004" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="voucher_date" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select class="form-select" name="supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    <option value="1">ABC Company Ltd</option>
                                    <option value="2">XYZ Traders</option>
                                    <option value="3">Tech Solutions Inc</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="amount" step="0.01" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select" name="payment_method">
                                    <option value="cash">Cash</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Attachments</label>
                            <input type="file" class="form-control" name="attachments[]" multiple>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitVoucher()">Save Voucher</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Search functionality
            $('#searchVoucher').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#voucherTableBody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });

        function submitVoucher() {
            Swal.fire({
                title: 'Success!',
                text: 'Voucher created successfully',
                icon: 'success',
                confirmButtonColor: '#667eea'
            }).then(() => {
                $('#addVoucherModal').modal('hide');
                $('#voucherForm')[0].reset();
            });
        }
    </script>
@endsection