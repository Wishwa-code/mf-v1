@extends('layout.admin')

@section('head')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        :root {
            --supplier-font: 'Inter', sans-serif;
            --supplier-primary: #667eea;
            --supplier-secondary: #764ba2;
            --supplier-dark: #2d3748;
            --supplier-muted: #4a5568;
            --supplier-border: #e2e8f0;
            --supplier-soft-bg: #f8f9fa;
            --supplier-accent: #48bb78;
            --supplier-primary-rgb: 102, 126, 234;
            --supplier-secondary-rgb: 118, 75, 162;
            --supplier-accent-rgb: 72, 187, 120;
        }

        * {
            font-family: var(--supplier-font);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .page-header {
            background: #ffffff;
            color: var(--supplier-dark);
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
            color: var(--supplier-dark);
        }
        
        .section-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px 20px;
            border-radius: 12px;
            border-left: 5px solid var(--supplier-primary);
            margin-bottom: 20px;
            font-weight: 600;
            color: var(--supplier-dark);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .section-header:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(var(--supplier-primary-rgb), 0.15);
        }
        
        .form-control, .form-select {
            border: 2px solid var(--supplier-border);
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--supplier-primary);
            box-shadow: 0 0 0 4px rgba(var(--supplier-primary-rgb), 0.1);
            transform: translateY(-2px);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--supplier-muted);
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
            background: linear-gradient(135deg, rgba(var(--supplier-primary-rgb), 0.05) 0%, rgba(var(--supplier-secondary-rgb), 0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .attachment-box:hover {
            border-color: var(--supplier-primary);
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 35px rgba(var(--supplier-primary-rgb), 0.2);
        }
        
        .attachment-box:hover::before {
            opacity: 1;
        }
        
        .attachment-icon {
            font-size: 48px;
            color: #9ca3af;
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .attachment-box:hover .attachment-icon {
            transform: scale(1.2) rotate(5deg);
            color: var(--supplier-primary);
        }

        .attachment-preview {
            display: none;
            width: 100%;
            max-height: 160px;
            object-fit: contain;
            border-radius: 12px;
            margin-top: 12px;
            pointer-events: none;
            background: var(--supplier-soft-bg);
        }

        .attachment-has-preview .attachment-preview {
            display: block;
        }

        .attachment-has-preview .attachment-icon,
        .attachment-has-preview .attachment-label {
            display: none;
        }

        .attachment-remove {
            position: absolute;
            top: 10px;
            right: 10px;
            display: none;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: none;
            background: rgba(45, 55, 72, 0.75);
            color: #ffffff;
            padding: 0;
            cursor: pointer;
            transition: background 0.2s ease;
            z-index: 2;
        }

        .attachment-remove:hover {
            background: rgba(var(--supplier-primary-rgb), 0.85);
        }

        .attachment-remove i {
            font-size: 16px;
            line-height: 1;
        }

        .attachment-has-file .attachment-remove {
            display: flex;
        }

        .attachment-preview-btn {
            position: absolute;
            left: 50%;
            bottom: 14px;
            transform: translateX(-50%);
            display: none;
            padding: 6px 16px;
            border-radius: 999px;
            border: none;
            background: rgba(var(--supplier-accent-rgb), 0.92);
            color: #1c4532;
            font-weight: 600;
            font-size: 13px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: background 0.2s ease, color 0.2s ease;
            z-index: 2;
        }

        .attachment-preview-btn:hover {
            background: rgba(var(--supplier-accent-rgb), 0.95);
            color: #ffffff;
        }

        .attachment-has-image .attachment-preview-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .supplier-table {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .table thead {
            background: linear-gradient(135deg, var(--supplier-primary) 0%, var(--supplier-secondary) 100%);
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
            border-bottom: 1px solid var(--supplier-border);
        }
        
        .table tbody tr:hover {
            background: linear-gradient(135deg, rgba(var(--supplier-primary-rgb), 0.05) 0%, rgba(var(--supplier-secondary-rgb), 0.05) 100%);
            transform: scale(1.01);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .btn-create {
            background: linear-gradient(135deg, var(--supplier-primary) 0%, var(--supplier-secondary) 100%);
            border: none;
            padding: 14px 35px;
            color: white;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 15px rgba(var(--supplier-primary-rgb), 0.3);
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
            box-shadow: 0 8px 25px rgba(var(--supplier-primary-rgb), 0.5);
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

        .supplier-search-input {
            width: 250px;
            border-radius: 12px;
            border: 2px solid var(--supplier-border);
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .supplier-search-input:focus {
            border-color: var(--supplier-primary);
            box-shadow: 0 0 0 4px rgba(var(--supplier-primary-rgb), 0.1);
        }
        
        .row.mb-3 {
            margin-bottom: 1.5rem;
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
        
        .modal-content {
            border-radius: 20px;
            border: none;
            overflow: hidden;
        }

        .modal-header {
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            border: none;
        }

        .modal-footer {
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            border: none;
        }

        .modal-header-accent {
            background: linear-gradient(135deg, var(--supplier-primary) 0%, var(--supplier-secondary) 100%);
            color: #ffffff;
        }

        .modal-body-muted {
            background: var(--supplier-soft-bg);
        }
        
        .info-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            border: 1px solid #e8e8e8;
        }
        
        .info-card .section-header {
            margin: -20px -20px 20px -20px;
            padding: 15px 20px;
            border-radius: 16px 16px 0 0;
        }
        
        .info-item {
            margin-bottom: 15px;
        }
        
        .info-item strong {
            display: block;
            color: var(--supplier-muted);
            font-size: 13px;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-item p {
            color: var(--supplier-dark);
            font-size: 15px;
            margin: 0;
            font-weight: 500;
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

                <div class="card mb-4">
                    <div class="card-body">
                        <form id="supplierForm">
                            @csrf
                            
                            <!-- Basic Info Section -->
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

                            <!-- Bank & Payment Details Section -->
                            <div class="section-header mt-4">
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

                            <!-- Advanced Info Section -->
                            <div class="section-header mt-4">
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

                            <!-- Attachments Section -->
                            <div class="section-header mt-4">
                                📎 Attachments
                            </div>
                            <div class="row mb-3">
                                @for ($i = 1; $i <= 4; $i++)
                                    <div class="col-md-3">
                                        <div class="attachment-box attachment-trigger">
                                            <button type="button" class="attachment-remove" aria-label="Remove file">
                                                <i class="ri-close-line"></i>
                                            </button>
                                            <i class="ri-add-circle-line attachment-icon"></i>
                                            <p class="text-muted mt-2 attachment-label">Click to upload</p>
                                            <img src="" alt="Attachment preview" class="attachment-preview">
                                            <button type="button" class="attachment-preview-btn" aria-label="Preview attachment">
                                                <i class="ri-eye-line"></i>
                                                Preview
                                            </button>
                                            <input type="file" id="file{{ $i }}" name="file{{ $i }}" class="attachment-input" hidden>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-create"><i class="ri-save-line me-2"></i>Create Supplier</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Supplier List Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mt-2">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5><i class="ri-list-check me-2"></i>Supplier List</h5>
                                <div class="d-flex align-items-center">
                                    <label class="me-2">Search:</label>
                                    <input type="text" class="form-control supplier-search-input" id="searchSupplier" placeholder="Search suppliers...">
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
                                    <tbody id="supplierTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Supplier Modal -->
    <div class="modal fade" id="viewSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-accent">
                    <h5 class="modal-title"><i class="ri-eye-line me-2"></i>View Supplier Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-muted">
                    <div class="info-card">
                        <div class="section-header">Basic Info</div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-item">
                                    <strong>Supplier No:</strong>
                                    <p id="view_supplier_no"></p>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="info-item">
                                    <strong>Company Name:</strong>
                                    <p id="view_company_name"></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-item">
                                    <strong>Contact Number:</strong>
                                    <p id="view_contact_number"></p>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="info-item">
                                    <strong>Address:</strong>
                                    <p id="view_address"></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-item">
                                    <strong>Email:</strong>
                                    <p id="view_email"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="section-header">Bank & Payment Details</div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-item">
                                    <strong>Bank Name:</strong>
                                    <p id="view_bank_name"></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <strong>Branch:</strong>
                                    <p id="view_branch"></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <strong>Account Number:</strong>
                                    <p id="view_account_number"></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-item">
                                    <strong>Account Holder Name:</strong>
                                    <p id="view_account_holder_name"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="section-header">Advanced Info</div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-item">
                                    <strong>Business Reg No:</strong>
                                    <p id="view_business_reg_no"></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <strong>Tax/VAT No:</strong>
                                    <p id="view_tax_vat_no"></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <strong>NIC/Passport:</strong>
                                    <p id="view_nic_passport"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div class="modal fade" id="editSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header modal-header-accent">
                    <h5 class="modal-title"><i class="ri-edit-line me-2"></i>Edit Supplier</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editSupplierForm">
                    <div class="modal-body modal-body-muted">
                        @csrf
                        <input type="hidden" id="edit_supplier_id" name="supplier_id">
                        
                        <div class="info-card">
                            <div class="section-header">Basic Info</div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Supplier / Vendor No</label>
                                    <input type="text" class="form-control" id="edit_supplier_no" name="supplier_no" required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Company / Supplier Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_company_name" name="company_name" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Contact Number</label>
                                    <input type="tel" class="form-control" id="edit_contact_number" name="contact_number">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Address</label>
                                    <input type="text" class="form-control" id="edit_address" name="address">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="edit_email" name="email">
                                </div>
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="section-header">Bank & Payment Details</div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Bank Name</label>
                                    <input type="text" class="form-control" id="edit_bank_name" name="bank_name">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Branch</label>
                                    <input type="text" class="form-control" id="edit_branch" name="branch">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Account Number</label>
                                    <input type="text" class="form-control" id="edit_account_number" name="account_number">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Account Holder Name</label>
                                    <input type="text" class="form-control" id="edit_account_holder_name" name="account_holder_name">
                                </div>
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="section-header">Advanced Info (Optional)</div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Business Registration No</label>
                                    <input type="text" class="form-control" id="edit_business_reg_no" name="business_reg_no">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tax / VAT No</label>
                                    <input type="text" class="form-control" id="edit_tax_vat_no" name="tax_vat_no">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">NIC / Passport No</label>
                                    <input type="text" class="form-control" id="edit_nic_passport" name="nic_passport">
                                </div>
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="section-header">📎 Attachments (Optional - upload new files to replace)</div>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">File 1</label>
                                    <input type="file" class="form-control" name="file1">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">File 2</label>
                                    <input type="file" class="form-control" name="file2">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">File 3</label>
                                    <input type="file" class="form-control" name="file3">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">File 4</label>
                                    <input type="file" class="form-control" name="file4">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-create"><i class="ri-save-line me-2"></i>Update Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Attachment Preview Modal -->
    <div class="modal fade" id="attachmentPreviewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-body p-0" style="background: #000;">
                    <img src="" alt="Attachment preview" id="attachmentPreviewImage" class="img-fluid w-100">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            const getCssVar = (name, fallback = '') => {
                const value = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
                return value || fallback;
            };
            const routeTemplates = Object.freeze({
                show: '{{ route('suppliers.show', ['id' => '__SUPPLIER__']) }}',
                update: '{{ route('suppliers.update', ['id' => '__SUPPLIER__']) }}'
            });
            const supplierRoutes = {
                list: '{{ route('suppliers.index') }}',
                store: '{{ route('suppliers.store') }}',
                show: (id) => routeTemplates.show.replace('__SUPPLIER__', id),
                update: (id) => routeTemplates.update.replace('__SUPPLIER__', id)
            };
            const $supplierTableBody = $('#supplierTableBody');
            const $searchInput = $('#searchSupplier');
            const $createSubmitBtn = $('#supplierForm .btn-create');
            const $attachmentTriggers = $('.attachment-trigger');
            const $attachmentInputs = $('.attachment-input');
            const attachmentLabelText = 'Click to upload';
            const attachmentPreviewClass = 'attachment-has-preview';
            const attachmentFileClass = 'attachment-has-file';
            const attachmentImageClass = 'attachment-has-image';
            const swalPrimaryColor = getCssVar('--supplier-primary', '#667eea');
            const $attachmentPreviewModal = $('#attachmentPreviewModal');
            const $attachmentPreviewImage = $('#attachmentPreviewImage');
            const feedbackMessages = Object.freeze({
                loading: 'Loading suppliers...',
                empty: 'No suppliers found.',
                loadError: 'Failed to load suppliers.',
                detailError: 'Failed to load supplier details.',
                createSuccess: 'Supplier created successfully',
                createError: 'Failed to save supplier.',
                updateSuccess: 'Supplier updated successfully',
                updateError: 'Failed to update supplier.'
            });
            const supplierFieldMap = {
                view: {
                    supplier_no: '#view_supplier_no',
                    company_name: '#view_company_name',
                    contact_number: '#view_contact_number',
                    address: '#view_address',
                    email: '#view_email',
                    bank_name: '#view_bank_name',
                    branch: '#view_branch',
                    account_number: '#view_account_number',
                    account_holder_name: '#view_account_holder_name',
                    business_reg_no: '#view_business_reg_no',
                    tax_vat_no: '#view_tax_vat_no',
                    nic_passport: '#view_nic_passport'
                },
                edit: {
                    supplier_no: '#edit_supplier_no',
                    company_name: '#edit_company_name',
                    contact_number: '#edit_contact_number',
                    address: '#edit_address',
                    email: '#edit_email',
                    bank_name: '#edit_bank_name',
                    branch: '#edit_branch',
                    account_number: '#edit_account_number',
                    account_holder_name: '#edit_account_holder_name',
                    business_reg_no: '#edit_business_reg_no',
                    tax_vat_no: '#edit_tax_vat_no',
                    nic_passport: '#edit_nic_passport'
                }
            };
            let supplierCache = [];

            const clearAttachment = ($box) => {
                const $input = $box.find('.attachment-input');
                const $label = $box.find('.attachment-label');
                const $preview = $box.find('.attachment-preview');
                const previewUrl = $preview.data('previewUrl');

                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                    $preview.removeData('previewUrl');
                }

                $preview.attr('src', '');
                $box.removeClass(`${attachmentPreviewClass} ${attachmentFileClass} ${attachmentImageClass}`);
                $label.text(attachmentLabelText);
                $input.val('');
            };

            const escapeHtml = (value) => $('<div>').text(value == null ? '' : value).html();
            const withFallbackText = (value) => (value == null || value === '' ? '-' : value);
            const withFallbackValue = (value) => (value == null ? '' : value);

            const renderPlaceholderRow = (text, cssClass) => {
                $supplierTableBody.html(`
                    <tr>
                        <td colspan="5" class="text-center py-4 ${cssClass}">${escapeHtml(text)}</td>
                    </tr>
                `);
            };

            const renderSuppliers = (items) => {
                const searchValue = ($searchInput.val() || '').trim().toLowerCase();
                const filtered = Array.isArray(items)
                    ? items.filter((supplier) => {
                        if (!searchValue) {
                            return true;
                        }

                        const haystack = [
                            supplier.supplier_no,
                            supplier.company_name,
                            supplier.contact_number,
                            supplier.address
                        ].map((value) => (value || '').toLowerCase()).join(' ');

                        return haystack.includes(searchValue);
                    })
                    : [];

                if (!filtered.length) {
                    renderPlaceholderRow(feedbackMessages.empty, 'text-muted');
                    return;
                }

                const rows = filtered.map((supplier) => `
                        <tr>
                            <td>${escapeHtml(supplier.supplier_no)}</td>
                            <td>${escapeHtml(supplier.company_name)}</td>
                            <td>${escapeHtml(withFallbackText(supplier.contact_number))}</td>
                            <td>${escapeHtml(withFallbackText(supplier.address))}</td>
                            <td>
                                <button class="btn btn-sm btn-success btn-view" data-id="${supplier.id}"><i class="ri-eye-line"></i> View</button>
                                <button class="btn btn-sm btn-warning btn-edit" data-id="${supplier.id}"><i class="ri-edit-line"></i> Edit</button>
                                <button class="btn btn-sm btn-info"><i class="ri-history-line"></i> History</button>
                            </td>
                        </tr>
                    `).join('');

                $supplierTableBody.html(rows);
            };

            const handleAjaxError = (xhr, fallbackMessage) => {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const firstError = Object.values(xhr.responseJSON.errors)[0] || [];
                    if (firstError.length) {
                        return firstError[0];
                    }
                }

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    return xhr.responseJSON.message;
                }

                return fallbackMessage;
            };

            const showAlert = (title, message, icon) => {
                Swal.fire({
                    title,
                    text: message,
                    icon,
                    confirmButtonColor: swalPrimaryColor
                });
            };

            const loadSuppliers = () => {
                renderPlaceholderRow(feedbackMessages.loading, 'text-muted');

                $.ajax({
                    url: supplierRoutes.list,
                    method: 'GET',
                    success(response) {
                        supplierCache = response.data || [];
                        renderSuppliers(supplierCache);
                    },
                    error() {
                        supplierCache = [];
                        renderPlaceholderRow(feedbackMessages.loadError, 'text-danger');
                    }
                });
            };

            const fillFields = (map, data, handler) => {
                Object.entries(map).forEach(([key, selector]) => {
                    handler($(selector), data[key]);
                });
            };

            const fetchSupplier = (supplierId, onSuccess) => {
                $.ajax({
                    url: supplierRoutes.show(supplierId),
                    method: 'GET',
                    success(response) {
                        onSuccess(response.data);
                    },
                    error(xhr) {
                        const message = handleAjaxError(xhr, feedbackMessages.detailError);
                        showAlert('Error', message, 'error');
                    }
                });
            };

            const resetAttachmentLabels = () => {
                $attachmentTriggers.each(function() {
                    clearAttachment($(this));
                });
            };

            $('#supplierForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const form = this;

                $createSubmitBtn.prop('disabled', true);

                $.ajax({
                    url: supplierRoutes.store,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success(response) {
                        showAlert('Success!', response.message || feedbackMessages.createSuccess, 'success');
                        form.reset();
                        resetAttachmentLabels();
                        loadSuppliers();
                    },
                    error(xhr) {
                        const message = handleAjaxError(xhr, feedbackMessages.createError);
                        showAlert('Error', message, 'error');
                    },
                    complete() {
                        $createSubmitBtn.prop('disabled', false);
                    }
                });
            });

            $searchInput.on('keyup', () => {
                renderSuppliers(supplierCache);
            });

            $attachmentTriggers.on('click', function(event) {
                const $target = $(event.target);
                if (
                    $target.hasClass('attachment-input') ||
                    $target.closest('.attachment-remove').length ||
                    $target.closest('.attachment-preview').length ||
                    $target.closest('.attachment-preview-btn').length
                ) {
                    return;
                }

                event.preventDefault();

                const input = $(this).find('.attachment-input').get(0);
                if (input) {
                    input.click();
                }
            });

            $attachmentInputs.on('change', function() {
                const file = this.files && this.files.length ? this.files[0] : null;
                const fileName = file ? file.name : '';
                const $box = $(this).closest('.attachment-box');
                const $label = $box.find('.attachment-label');
                const $preview = $box.find('.attachment-preview');
                const previousUrl = $preview.data('previewUrl');

                if (previousUrl) {
                    URL.revokeObjectURL(previousUrl);
                    $preview.removeData('previewUrl');
                }

                if (!file) {
                    clearAttachment($box);
                    return;
                }

                $box.addClass(attachmentFileClass).removeClass(`${attachmentPreviewClass} ${attachmentImageClass}`);
                $label.text(fileName || attachmentLabelText);

                if (file.type && file.type.toLowerCase().startsWith('image/')) {
                    const previewUrl = URL.createObjectURL(file);
                    $preview.attr('src', previewUrl).data('previewUrl', previewUrl);
                    $box.addClass(`${attachmentPreviewClass} ${attachmentImageClass}`);
                } else {
                    $preview.attr('src', '');
                }
            });

            $(document).on('click', '.attachment-remove', function(event) {
                event.preventDefault();
                event.stopPropagation();
                clearAttachment($(this).closest('.attachment-box'));
            });

            const openPreviewModal = (src) => {
                if (!src) {
                    return;
                }

                $attachmentPreviewImage.attr('src', src);
                $attachmentPreviewModal.modal('show');
            };

            $(document).on('click', '.attachment-preview, .attachment-preview-btn', function(event) {
                event.preventDefault();
                event.stopPropagation();

                const $preview = $(this).hasClass('attachment-preview')
                    ? $(this)
                    : $(this).siblings('.attachment-preview');

                openPreviewModal($preview.attr('src'));
            });

            $attachmentPreviewModal.on('hidden.bs.modal', function() {
                $attachmentPreviewImage.attr('src', '');
            });

            $(document).on('click', '.btn-view', function() {
                const supplierId = $(this).data('id');

                fetchSupplier(supplierId, (supplier) => {
                    fillFields(supplierFieldMap.view, supplier, ($el, value) => $el.text(withFallbackText(value)));
                    $('#viewSupplierModal').modal('show');
                });
            });

            $(document).on('click', '.btn-edit', function() {
                const supplierId = $(this).data('id');

                fetchSupplier(supplierId, (supplier) => {
                    $('#edit_supplier_id').val(supplier.id);
                    fillFields(supplierFieldMap.edit, supplier, ($el, value) => $el.val(withFallbackValue(value)));
                    $('#editSupplierModal').modal('show');
                });
            });

            $('#editSupplierForm').on('submit', function(e) {
                e.preventDefault();

                const supplierId = $('#edit_supplier_id').val();
                const formData = new FormData(this);
                formData.append('_method', 'PUT');

                $.ajax({
                    url: supplierRoutes.update(supplierId),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success(response) {
                        $('#editSupplierModal').modal('hide');
                        showAlert('Success!', response.message || feedbackMessages.updateSuccess, 'success');
                        loadSuppliers();
                    },
                    error(xhr) {
                        const message = handleAjaxError(xhr, feedbackMessages.updateError);
                        showAlert('Error', message, 'error');
                    }
                });
            });

            resetAttachmentLabels();
            loadSuppliers();
        });
    </script>
@endsection