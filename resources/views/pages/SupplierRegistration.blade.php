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
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Loading suppliers...</td>
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

    <!-- View Supplier Modal -->
    <div class="modal fade" id="viewSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title"><i class="ri-eye-line me-2"></i>View Supplier Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="section-header">Basic Info</div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Supplier No:</strong>
                            <p id="view_supplier_no"></p>
                        </div>
                        <div class="col-md-8">
                            <strong>Company Name:</strong>
                            <p id="view_company_name"></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Contact Number:</strong>
                            <p id="view_contact_number"></p>
                        </div>
                        <div class="col-md-8">
                            <strong>Address:</strong>
                            <p id="view_address"></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Email:</strong>
                            <p id="view_email"></p>
                        </div>
                    </div>

                    <div class="section-header mt-4">Bank & Payment Details</div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Bank Name:</strong>
                            <p id="view_bank_name"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Branch:</strong>
                            <p id="view_branch"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Account Number:</strong>
                            <p id="view_account_number"></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Account Holder Name:</strong>
                            <p id="view_account_holder_name"></p>
                        </div>
                    </div>

                    <div class="section-header mt-4">Advanced Info</div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Business Reg No:</strong>
                            <p id="view_business_reg_no"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Tax/VAT No:</strong>
                            <p id="view_tax_vat_no"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>NIC/Passport:</strong>
                            <p id="view_nic_passport"></p>
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
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title"><i class="ri-edit-line me-2"></i>Edit Supplier</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editSupplierForm">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" id="edit_supplier_id" name="supplier_id">
                        
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

                        <div class="section-header mt-4">Bank & Payment Details</div>
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

                        <div class="section-header mt-4">Advanced Info (Optional)</div>
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

                        <div class="section-header mt-4">📎 Attachments (Optional - upload new files to replace)</div>
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-create"><i class="ri-save-line me-2"></i>Update Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            const supplierListUrl = '{{ route('suppliers.index') }}';
            const supplierSaveUrl = '{{ route('suppliers.store') }}';
            const $supplierTableBody = $('#supplierTableBody');
            const $searchInput = $('#searchSupplier');
            const $submitBtn = $('.btn-create');
            let supplierCache = [];

            const escapeHtml = (value) => $('<div>').text(value == null ? '' : value).html();

            const renderSuppliers = (items) => {
                const searchValue = ($searchInput.val() || '').toLowerCase();
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

                        return haystack.indexOf(searchValue) !== -1;
                    })
                    : [];

                if (!filtered.length) {
                    $supplierTableBody.html(`
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No suppliers found.</td>
                        </tr>
                    `);
                    return;
                }

                const rows = filtered.map((supplier) => {
                    const contact = supplier.contact_number ? supplier.contact_number : '-';
                    const address = supplier.address ? supplier.address : '-';

                    return `
                        <tr>
                            <td>${escapeHtml(supplier.supplier_no)}</td>
                            <td>${escapeHtml(supplier.company_name)}</td>
                            <td>${escapeHtml(contact)}</td>
                            <td>${escapeHtml(address)}</td>
                            <td>
                                <button class="btn btn-sm btn-success btn-view" data-id="${supplier.id}"><i class="ri-eye-line"></i> View</button>
                                <button class="btn btn-sm btn-warning btn-edit" data-id="${supplier.id}"><i class="ri-edit-line"></i> Edit</button>
                                <button class="btn btn-sm btn-info"><i class="ri-history-line"></i> History</button>
                            </td>
                        </tr>
                    `;
                }).join('');

                $supplierTableBody.html(rows);
            };

            const showLoadingRow = (text, cssClass) => {
                $supplierTableBody.html(`
                    <tr>
                        <td colspan="5" class="text-center py-4 ${cssClass}">${text}</td>
                    </tr>
                `);
            };

            const loadSuppliers = () => {
                showLoadingRow('Loading suppliers...', 'text-muted');

                $.ajax({
                    url: supplierListUrl,
                    method: 'GET',
                    success: function(response) {
                        supplierCache = response.data || [];
                        renderSuppliers(supplierCache);
                    },
                    error: function() {
                        supplierCache = [];
                        showLoadingRow('Failed to load suppliers.', 'text-danger');
                    }
                });
            };

            const resetAttachmentLabels = () => {
                $('.attachment-box p').text('Click to upload');
            };

            $('#supplierForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const form = this;

                $submitBtn.prop('disabled', true);

                $.ajax({
                    url: supplierSaveUrl,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message || 'Supplier created successfully',
                            icon: 'success',
                            confirmButtonColor: '#667eea'
                        });

                        form.reset();
                        resetAttachmentLabels();
                        loadSuppliers();
                    },
                    error: function(xhr) {
                        let message = 'Failed to save supplier.';

                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            const firstError = Object.values(xhr.responseJSON.errors)[0] || [];
                            if (firstError.length) {
                                message = firstError[0];
                            }
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error',
                            text: message,
                            icon: 'error',
                            confirmButtonColor: '#667eea'
                        });
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false);
                    }
                });
            });

            $searchInput.on('keyup', function() {
                renderSuppliers(supplierCache);
            });

            $('input[type="file"]').on('change', function() {
                const fileName = $(this).val().split('\\').pop();
                $(this).parent().find('p').text(fileName || 'Click to upload');
            });

            $(document).on('click', '.btn-view', function() {
                const supplierId = $(this).data('id');
                const supplierShowUrl = '{{ route('suppliers.show', ':id') }}'.replace(':id', supplierId);

                $.ajax({
                    url: supplierShowUrl,
                    method: 'GET',
                    success: function(response) {
                        const supplier = response.data;
                        
                        $('#view_supplier_no').text(supplier.supplier_no || '-');
                        $('#view_company_name').text(supplier.company_name || '-');
                        $('#view_contact_number').text(supplier.contact_number || '-');
                        $('#view_address').text(supplier.address || '-');
                        $('#view_email').text(supplier.email || '-');
                        $('#view_bank_name').text(supplier.bank_name || '-');
                        $('#view_branch').text(supplier.branch || '-');
                        $('#view_account_number').text(supplier.account_number || '-');
                        $('#view_account_holder_name').text(supplier.account_holder_name || '-');
                        $('#view_business_reg_no').text(supplier.business_reg_no || '-');
                        $('#view_tax_vat_no').text(supplier.tax_vat_no || '-');
                        $('#view_nic_passport').text(supplier.nic_passport || '-');

                        $('#viewSupplierModal').modal('show');
                    },
                    error: function(xhr) {
                        let message = 'Failed to load supplier details.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: 'Error',
                            text: message,
                            icon: 'error',
                            confirmButtonColor: '#667eea'
                        });
                    }
                });
            });

            $(document).on('click', '.btn-edit', function() {
                const supplierId = $(this).data('id');
                const supplierShowUrl = '{{ route('suppliers.show', ':id') }}'.replace(':id', supplierId);

                $.ajax({
                    url: supplierShowUrl,
                    method: 'GET',
                    success: function(response) {
                        const supplier = response.data;
                        
                        $('#edit_supplier_id').val(supplier.id);
                        $('#edit_supplier_no').val(supplier.supplier_no || '');
                        $('#edit_company_name').val(supplier.company_name || '');
                        $('#edit_contact_number').val(supplier.contact_number || '');
                        $('#edit_address').val(supplier.address || '');
                        $('#edit_email').val(supplier.email || '');
                        $('#edit_bank_name').val(supplier.bank_name || '');
                        $('#edit_branch').val(supplier.branch || '');
                        $('#edit_account_number').val(supplier.account_number || '');
                        $('#edit_account_holder_name').val(supplier.account_holder_name || '');
                        $('#edit_business_reg_no').val(supplier.business_reg_no || '');
                        $('#edit_tax_vat_no').val(supplier.tax_vat_no || '');
                        $('#edit_nic_passport').val(supplier.nic_passport || '');

                        $('#editSupplierModal').modal('show');
                    },
                    error: function(xhr) {
                        let message = 'Failed to load supplier details.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: 'Error',
                            text: message,
                            icon: 'error',
                            confirmButtonColor: '#667eea'
                        });
                    }
                });
            });

            $('#editSupplierForm').on('submit', function(e) {
                e.preventDefault();

                const supplierId = $('#edit_supplier_id').val();
                const supplierUpdateUrl = '{{ route('suppliers.update', ':id') }}'.replace(':id', supplierId);
                const formData = new FormData(this);
                formData.append('_method', 'PUT');

                $.ajax({
                    url: supplierUpdateUrl,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#editSupplierModal').modal('hide');
                        
                        Swal.fire({
                            title: 'Success!',
                            text: response.message || 'Supplier updated successfully',
                            icon: 'success',
                            confirmButtonColor: '#667eea'
                        });

                        loadSuppliers();
                    },
                    error: function(xhr) {
                        let message = 'Failed to update supplier.';

                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            const firstError = Object.values(xhr.responseJSON.errors)[0] || [];
                            if (firstError.length) {
                                message = firstError[0];
                            }
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error',
                            text: message,
                            icon: 'error',
                            confirmButtonColor: '#667eea'
                        });
                    }
                });
            });

            loadSuppliers();
        });
    </script>
@endsection