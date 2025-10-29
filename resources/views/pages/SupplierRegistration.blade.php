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
        
        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
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
        
        .section-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px 20px;
            border-radius: 12px;
            border-left: 5px solid #667eea;
            margin-bottom: 20px;
            font-weight: 600;
            color: #2d3748;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .section-header:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
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
        
        .attachment-box {
            border: 3px dashed #cbd5e0;
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);
            position: relative;
            overflow: hidden;
        }
        
        .attachment-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .attachment-box:hover {
            border-color: #667eea;
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.2);
        }
        
        .attachment-box:hover::before {
            opacity: 1;
        }
        
        .attachment-box i {
            position: relative;
            z-index: 1;
            transition: all 0.3s;
        }
        
        .attachment-box:hover i {
            transform: scale(1.2) rotate(5deg);
            color: #667eea !important;
        }
        
        .supplier-table {
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
            padding: 16px;
            font-weight: 600;
            letter-spacing: 0.5px;
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
        
        .btn-create {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 14px 35px;
            color: white;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-create::before {
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
        
        .btn-create:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-create:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        }
        
        .btn-create:active {
            transform: translateY(-1px);
        }
        
        .btn {
            border-radius: 10px;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        #searchSupplier {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 10px 20px;
            transition: all 0.3s;
        }
        
        #searchSupplier:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .row.mb-3 {
            margin-bottom: 1.5rem;
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
        
        .modern-card {
            animation: fadeInUp 0.6s ease-out;
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
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mt-3">
            <div class="col-12">
                <div class="page-header">
                    <h3 class="mb-0"><i class="ri-building-line me-2"></i>Supplier / Vendor Registration</h3>
                </div>

                <form id="supplierForm">
                    @csrf
                    
                    <!-- Basic Info Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="section-header">
                                Basic Info
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="supplier_no" class="form-label">Supplier / Vendor No</label>
                                    <input type="text" class="form-control" id="supplier_no" name="supplier_no" required>
                                </div>
                                <div class="col-md-8">
                                    <label for="company_name" class="form-label">Company / Supplier Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="company_name" name="company_name" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="contact_number" class="form-label">Contact Number</label>
                                    <input type="tel" class="form-control" id="contact_number" name="contact_number">
                                </div>
                                <div class="col-md-8">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bank & Payment Details Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="section-header">
                                Bank & Payment Details
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="bank_name" class="form-label">Bank Name</label>
                                    <input type="text" class="form-control" id="bank_name" name="bank_name">
                                </div>
                                <div class="col-md-4">
                                    <label for="branch" class="form-label">Branch</label>
                                    <input type="text" class="form-control" id="branch" name="branch">
                                </div>
                                <div class="col-md-4">
                                    <label for="account_number" class="form-label">Account Number</label>
                                    <input type="text" class="form-control" id="account_number" name="account_number">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="account_holder_name" class="form-label">Account Holder Name</label>
                                    <input type="text" class="form-control" id="account_holder_name" name="account_holder_name">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Info Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="section-header">
                                Advance Info (Optional)
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="business_reg_no" class="form-label">Business Registration No</label>
                                    <input type="text" class="form-control" id="business_reg_no" name="business_reg_no">
                                </div>
                                <div class="col-md-4">
                                    <label for="tax_vat_no" class="form-label">Tax / VAT No</label>
                                    <input type="text" class="form-control" id="tax_vat_no" name="tax_vat_no">
                                </div>
                                <div class="col-md-4">
                                    <label for="nic_passport" class="form-label">NIC / Passport No (if it's an individual supplier)</label>
                                    <input type="text" class="form-control" id="nic_passport" name="nic_passport">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments Card -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="section-header">
                                📎 Attachments
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="attachment-box" onclick="document.getElementById('file1').click()">
                                        <i class="ri-add-circle-line" style="font-size: 48px; color: #9ca3af;"></i>
                                        <p class="text-muted mt-2">Click to upload</p>
                                        <input type="file" id="file1" name="file1" hidden>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="attachment-box" onclick="document.getElementById('file2').click()">
                                        <i class="ri-add-circle-line" style="font-size: 48px; color: #9ca3af;"></i>
                                        <p class="text-muted mt-2">Click to upload</p>
                                        <input type="file" id="file2" name="file2" hidden>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="attachment-box" onclick="document.getElementById('file3').click()">
                                        <i class="ri-add-circle-line" style="font-size: 48px; color: #9ca3af;"></i>
                                        <p class="text-muted mt-2">Click to upload</p>
                                        <input type="file" id="file3" name="file3" hidden>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="attachment-box" onclick="document.getElementById('file4').click()">
                                        <i class="ri-add-circle-line" style="font-size: 48px; color: #9ca3af;"></i>
                                        <p class="text-muted mt-2">Click to upload</p>
                                        <input type="file" id="file4" name="file4" hidden>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-create"><i class="ri-save-line me-2"></i>Create Supplier</button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Supplier List Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mt-2">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5><i class="ri-list-check me-2"></i>Supplier List</h5>
                                <div class="d-flex align-items-center">
                                    <label class="me-2">Search:</label>
                                    <input type="text" class="form-control" id="searchSupplier" placeholder="Search suppliers..." style="width: 250px;">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover supplier-table">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Supplier No</th>
                                            <th>Company / Supplier</th>
                                            <th>Contact No</th>
                                            <th>Address</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="supplierTableBody">
                                        <!-- Sample data -->
                                        <tr>
                                            <td>SUP001</td>
                                            <td>ABC Company Ltd</td>
                                            <td>0771234567</td>
                                            <td>123 Main St, Colombo</td>
                                            <td>
                                                <button class="btn btn-sm btn-success"><i class="ri-eye-line"></i> View</button>
                                                <button class="btn btn-sm btn-warning"><i class="ri-edit-line"></i> Edit</button>
                                                <button class="btn btn-sm btn-info"><i class="ri-history-line"></i> History</button>
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
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Form submission
            $('#supplierForm').on('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Success!',
                    text: 'Supplier created successfully',
                    icon: 'success',
                    confirmButtonColor: '#667eea'
                });
            });

            // Search functionality
            $('#searchSupplier').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#supplierTableBody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // File upload preview
            $('input[type="file"]').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).parent().find('p').text(fileName || 'Click to upload');
            });
        });
    </script>
@endsection