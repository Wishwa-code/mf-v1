@extends('layout.admin')

@section('head')
<!-- Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<link href="/css/pages/customer.css" rel="stylesheet" />
@endsection

@section('content')
<div class="container-fluid pb-5">
    <!-- Page Header -->
    <!-- Page Header -->
    <!-- Page Header -->
    <div class="row align-items-center mb-4 mt-3" id="page-header">
        <div class="col-md-6">
            <h4 class="mb-1 fw-bold text-dark">Customer Details</h4>
            <p class="text-muted mb-0 small">Manage customer information and verified documents</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button type="button" class="btn btn-primary-common px-5 rounded-pill shadow-sm fw-bold" onclick="validateSubmitCustomer(event)">
                <i class="bi bi-check-lg me-2"></i> Submit Customer
            </button>
        </div>
    </div>

    <!-- Sticky Header Script -->
    <script>
        window.addEventListener('scroll', function() {
            var header = document.getElementById('page-header');
            if (window.scrollY > 10) {
                header.classList.add('sticky-top-header', 'shadow-sm', 'px-3', 'rounded-bottom');
                header.classList.remove('mt-3', 'mb-4'); // Remove original margins for tighter fit
            } else {
                header.classList.remove('sticky-top-header', 'shadow-sm', 'px-3', 'rounded-bottom');
                header.classList.add('mt-3', 'mb-4'); // Restore
            }
        });
    </script>

    <div class="row">
        <!-- Sticky Sidebar Navigation -->
        <div class="col-lg-3 d-none d-lg-block">
            <nav class="sticky-sidebar">
                <!-- Profile Image Widget -->
                <div class="profile-widget-container text-center mb-4">
                    <div class="position-relative d-inline-block">
                        <div class="profile-avatar-wrapper shadow-lg" onclick="openImageSelectionModal()">
                            <img id="profile-preview" src="https://ui-avatars.com/api/?name=Customer&background=EBF4FF&color=7F9CF5&size=150" alt="Profile" class="img-fluid rounded-circle">
                            <div class="profile-avatar-overlay">
                                <i class="bi bi-camera-fill fs-3 text-white"></i>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 shadow-sm edit-profile-btn" onclick="openImageSelectionModal()">
                            <i class="bi bi-pencil-fill small"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <h6 class="fw-bold text-dark mb-0">Customer Photo</h6>
                        <small class="text-muted" style="font-size: 0.8rem;">Click to upload</small>
                    </div>

                    <!-- Hidden Inputs -->
                    <input type="file" id="hidden_file_upload" accept="image/*" class="d-none" onchange="previewProfileImage(this)">
                    <input type="file" id="hidden_camera_upload" accept="image/*" capture="environment" class="d-none" onchange="previewProfileImage(this)">
                </div>

                <div class="nav flex-column nav-pills nav-pills-custom" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active" href="#section-basic">
                        <i class="bi bi-person-lines-fill"></i> Basic Details
                    </a>
                    <a class="nav-link" href="#section-address">
                        <i class="bi bi-geo-alt-fill"></i> Address & Location
                    </a>
                    <a class="nav-link" href="#section-occupation">
                        <i class="bi bi-briefcase-fill"></i> Occupation
                    </a>
                    <a class="nav-link" href="#section-documents">
                        <i class="bi bi-file-earmark-text-fill"></i> Documents
                    </a>
                    <a class="nav-link" href="#section-bank">
                        <i class="bi bi-bank2"></i> Bank Details
                    </a>
                    <a class="nav-link" href="#section-guardian">
                        <i class="bi bi-shield-lock-fill"></i> Guardian
                    </a>
                </div>

                <!-- Progress Widget -->
                <div class="card border-0 shadow-sm mt-4 rounded-4" style="background: linear-gradient(145deg, #ffffff, #f5f7fa);">
                    <div class="card-body p-4 text-center">
                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Completion</h6>
                        <div class="position-relative d-inline-block">
                            <svg class="progress-ring" width="80" height="80">
                                <circle class="progress-ring__circle-bg" stroke="#e2e8f0" stroke-width="6" fill="transparent" r="34" cx="40" cy="40" />
                                <circle class="progress-ring__circle" stroke="url(#gradient)" stroke-width="6" fill="transparent" r="34" cx="40" cy="40" />
                                <defs>
                                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#667eea" />
                                        <stop offset="100%" stop-color="#764ba2" />
                                    </linearGradient>
                                </defs>
                            </svg>
                            <span class="position-absolute top-50 start-50 translate-middle fw-bold text-dark" id="completion-percentage">0%</span>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Main Content Form Area -->
        <div class="col-lg-9">
            {{-- ===================== SECTION 1: BASIC CUSTOMER DETAILS ===================== --}}
            <div class="card card-modern" id="section-basic">
                <div class="card-body p-4">
                    <div class="section-header">
                        <i class="bi bi-person-lines-fill"></i> Basic Customer Details
                    </div>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <label for="cus_number" class="form-label">
                                Customer Number <span class="required-asterisk">*</span>
                            </label>
                            <!-- Logic moved to JS -->
                            <div id="cus_number_container">
                                <input type="text" id="cus_number" name="cus_number" class="form-control" placeholder="Loading..." readonly>
                                <div class="invalid-feedback"></div>
                                <label id="formatted_num_use" hidden></label>
                                <label id="formatted_num" style="color: red; display: none;"></label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="route" class="form-label">Route</label>
                            <label for="route" class="form-label">Route</label>
                            <select class="form-select select2" id="route" name="route">
                                <option value="" selected disabled>Select Route</option>
                                <!-- Options loaded by JS -->
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="title" class="form-label">Title</label>
                            <label for="title" class="form-label">Title</label>
                            <select class="form-select select2" id="title" name="title">
                                <option value="" selected disabled>Select Title</option>
                                <option>Mr</option>
                                <option>Ms.</option>
                                <option>Mrs</option>
                                <option>Dr</option>
                                <option>Rev</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="civil_status" class="form-label">Civil Status</label>
                            <select class="form-select select2" id="civil_status" name="civil_status">
                                <option value="" selected disabled>Select Civil Status</option>
                                <option>Married</option>
                                <option>Single</option>
                                <option>Seperated</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="f_name" class="form-label">First Name <span class="required-asterisk">*</span></label>
                            <input type="text" id="f_name" name="f_name" class="form-control" placeholder="First Name">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="last_name" class="form-label">Middle/Last Name <span class="required-asterisk">*</span></label>
                            <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Middle/Last Name">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="example@email.com">
                        </div>

                        <div class="col-md-4">
                            <label for="contact_number" class="form-label">Mobile No <span class="required-asterisk">*</span></label>
                            <input type="tel" id="contact_number" name="contact_number" class="form-control" oninput="validateMobileNumber(this)" maxlength="10" placeholder="07XXXXXXXX">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="contact_number_2" class="form-label">Mobile No 02</label>
                            <input type="tel" id="contact_number_2" name="contact_number_2" class="form-control" oninput="validateMobileNumber(this)" maxlength="10" placeholder="07XXXXXXXX">
                        </div>

                        <div class="col-md-4" hidden>
                            <label for="business_registration" class="form-label">Business Registration Number</label>
                            <input type="tel" id="business_registration" name="business_registration" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label for="nic" class="form-label">NIC <span class="required-asterisk">*</span></label>
                            <input type="text" id="nic" name="nic" class="form-control" oninput="validateNIC(this)" maxlength="12" placeholder="NIC Number">
                            <div class="invalid-feedback"></div>
                            <input type="hidden" id="new_nic" name="new_nic" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label for="gender" class="form-label">Gender</label>
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select select2" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="-">-</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="dob" class="form-label">Date Of Birth</label>
                            <input type="text" id="dob" name="dob" class="form-control datepicker" placeholder="YYYY-MM-DD">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="landline" class="form-label">Landline Phone</label>
                            <input type="tel" id="landline" name="landline" class="form-control" onkeypress="validateContactNumber(event)" placeholder="0XXXXXXXXX">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="cus_phto" class="form-label">Customer Photo</label>
                            <div class="d-flex flex-column flex-sm-row gap-1">
                                <input type="file" id="cus_phto" name="cus_phto" class="form-control" accept="image/*" capture="environment">
                                <button type="button" onclick="openGlobalCamera('#cus_phto')" class="btn btn-outline-secondary mt-1 mt-sm-0">
                                    <i class="bi bi-camera"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== SECTION 2: ADDRESS & LOCATION ===================== --}}
            <div class="card card-modern" id="section-address">
                <div class="card-body p-4">
                    <div class="section-header">
                        <i class="bi bi-geo-alt-fill"></i> Address & Location
                    </div>

                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label">Current Address</label>
                            <div class="row g-2">
                                <div class="col-md-4"><input type="text" id="curr_address_01" class="form-control" placeholder="Address line 1"></div>
                                <div class="col-md-4"><input type="text" id="curr_address_02" class="form-control" placeholder="Address line 2"></div>
                                <div class="col-md-4"><input type="text" id="curr_address_03" class="form-control" placeholder="Address line 3"></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Permanent Address</label>
                            <div class="row g-2">
                                <div class="col-md-4"><input type="text" id="per_address_01" class="form-control" placeholder="Address line 1"></div>
                                <div class="col-md-4"><input type="text" id="per_address_02" class="form-control" placeholder="Address line 2"></div>
                                <div class="col-md-4"><input type="text" id="per_address_03" class="form-control" placeholder="Address line 3"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="state" class="form-label">Province / State</label>
                            <select id="state" name="state" class="form-select select2">
                                <option value="">Select Province</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="city" class="form-label">City / Town</label>
                            <select id="city" name="city" class="form-select select2">
                                <option value="">Select City / Town</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12">
                            <label for="note" class="form-label">Note</label>
                            <textarea class="form-control" placeholder="Enter any additional notes" id="note" name="note" style="min-height: 100px;"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input type="text" id="longitude" name="longitude" class="form-control" placeholder="Longitude">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="latitude" class="form-label">Latitude</label>
                            <div class="input-group">
                                <input type="text" id="latitude" name="latitude" class="form-control" placeholder="Latitude">
                                <button type="button" class="btn btn-outline-danger" onclick="getlocation();">Get Location</button>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== SECTION 3: OCCUPATION DETAILS ===================== --}}
            <div class="card card-modern" id="section-occupation">
                <div class="card-body p-4">
                    <div class="section-header">
                        <i class="bi bi-briefcase-fill"></i> Occupation Details
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="occu_job_position" class="form-label">Job Position</label>
                            <input type="text" id="occu_job_position" class="form-control" placeholder="Job Position">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="occu_monthly_salary" class="form-label">Monthly Salary</label>
                            <input type="text" id="occu_monthly_salary" class="form-control" placeholder="0.00">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12">
                            <label for="occu_address_01" class="form-label">Working Place Address</label>
                            <div class="row g-2">
                                <div class="col-md-4"><input type="text" id="occu_address_01" class="form-control" placeholder="Address line 1"></div>
                                <div class="col-md-4"><input type="text" id="occu_address_02" class="form-control" placeholder="Address line 2"></div>
                                <div class="col-md-4"><input type="text" id="occu_address_03" class="form-control" placeholder="Address line 3"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="occu_contact_no" class="form-label">Working Place Contact Number</label>
                            <input type="tel" id="occu_contact_no" class="form-control" oninput="validateMobileNumber(this)" maxlength="10" placeholder="0XXXXXXXXX">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6"></div> {{-- Spacer --}}

                        <div class="col-md-6">
                            <label for="occu_longitude" class="form-label">Longitude</label>
                            <input type="text" id="occu_longitude" name="occu_longitude" class="form-control" placeholder="Longitude">
                        </div>
                        <div class="col-md-6">
                            <label for="occu_latitude" class="form-label">Latitude</label>
                            <div class="input-group">
                                <input type="text" id="occu_latitude" name="occu_latitude" class="form-control" placeholder="Latitude">
                                <button type="button" class="btn btn-outline-danger" onclick="getlocation_occu();">Get Location</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== SECTION 4: REQUIRED DOCUMENTS ===================== --}}
            <div class="card card-modern" id="section-documents">
                <div class="card-body p-4">
                    <div class="section-header">
                        <i class="bi bi-file-earmark-text-fill"></i> Required Documents
                    </div>

                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="otherDocDescription" class="form-label">Document Type</label>
                            <select class="form-control" id="otherDocDescription">
                                <option value="">Select Document Type</option>
                                <!-- Options loaded dynamically -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary-common w-100 w-md-auto" id="addDocBtn">Add Document</button>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-hover align-middle mb-0" id="documenttable">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th>File</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ===================== SECTION 5: BANK DETAILS ===================== --}}
            <div class="card card-modern" id="section-bank">
                <div class="card-body p-4">
                    <div class="section-header">
                        <i class="bi bi-bank2"></i> Bank Details
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="bank_name" class="form-label">Bank Name</label>
                            <select id="bank_name" class="form-select select2">
                                <option value="">Select Bank</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="account_name" class="form-label">Account Name</label>
                            <input type="text" id="account_name" class="form-control" placeholder="Account Name">
                        </div>
                        <div class="col-md-6">
                            <label for="account_number" class="form-label">Account Number</label>
                            <input type="text" id="account_number" class="form-control" placeholder="Account Number">
                        </div>
                        <div class="col-md-6">
                            <label for="branch" class="form-label">Branch code</label>
                            <select id="branch" class="form-select select2">
                                <option value="">Select Branch</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="bank_code" class="form-label">Bank code</label>
                            <input type="text" id="bank_code" class="form-control" placeholder="Bank Code">
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-primary-common mt-md-4" id="addBankBtn">Add Bank Account</button>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-hover align-middle mb-0" id="bank_table">
                            <thead class="table-light">
                                <tr>
                                    <th>Bank Name</th>
                                    <th>Account Name</th>
                                    <th>Account Number</th>
                                    <th>Branch code</th>
                                    <th>Bank code</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ===================== SECTION 6: GUARDIAN DETAILS ===================== --}}
            <div class="card card-modern" id="section-guardian">
                <div class="card-body p-4">
                    <div class="section-header">
                        <i class="bi bi-shield-lock-fill"></i> Guardian Details
                    </div>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <label for="gua_title" class="form-label">Guardian Title</label>
                            <select class="form-select select2" id="gua_title" name="gua_title">
                                <option value="" selected disabled>Select Title</option>
                                <option>Mr</option>
                                <option>Mrs</option>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label for="gua_name" class="form-label">Guardian Name <span class="required-asterisk">*</span></label>
                            <input type="text" id="gua_name" name="gua_name" class="form-control" placeholder="Guardian Name">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="gua_nic" class="form-label">NIC <span class="required-asterisk">*</span></label>
                            <input type="text" id="gua_nic" name="gua_nic" class="form-control" oninput="validateNIC(this)" maxlength="12" placeholder="NIC">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="guardian_gender" class="form-label">Gender</label>
                            <select class="form-select select2" id="guardian_gender" name="guardian_gender">
                                <option value="-">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="gua_relation" class="form-label">Relation to borrower</label>
                            <input type="text" id="gua_relation" name="gua_relation" class="form-control" placeholder="Relation">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="gua_occu" class="form-label">Occupation</label>
                            <input type="text" id="gua_occu" name="gua_occu" class="form-control" placeholder="Occupation">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="gua_contact" class="form-label">Contact No <span class="required-asterisk">*</span></label>
                            <input type="tel" id="gua_contact" name="gua_contact" class="form-control" oninput="validateMobileNumber(this)" maxlength="10" placeholder="0XXXXXXXXX">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <div class="row g-2">
                                <div class="col-md-4"><input type="text" id="gua_address_01" class="form-control" placeholder="Address line 1"></div>
                                <div class="col-md-4"><input type="text" id="gua_address_02" class="form-control" placeholder="Address line 2"></div>
                                <div class="col-md-4"><input type="text" id="gua_address_03" class="form-control" placeholder="Address line 3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== SAVE BUTTON ===================== --}}


        </div> <!-- end col-lg-9 -->
    </div> <!-- end row -->
</div>

<!-- Image Selection Modal -->
<div class="modal fade" id="imageSelectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body p-0">
                <div class="p-3 text-center bg-light border-bottom">
                    <h6 class="fw-bold mb-0">Update Photo</h6>
                </div>
                <div class="list-group list-group-flush">
                    <button type="button" class="list-group-item list-group-item-action p-3 d-flex align-items-center gap-3" onclick="triggerFileUpload()">
                        <div class="icon-box bg-blue-light text-primary rounded-circle p-2">
                            <i class="bi bi-image fs-5"></i>
                        </div>
                        <div class="text-start">
                            <div class="fw-semibold text-dark">Upload Image</div>
                            <small class="text-muted" style="font-size: 0.75rem;">From gallery</small>
                        </div>
                    </button>
                    <button type="button" class="list-group-item list-group-item-action p-3 d-flex align-items-center gap-3" onclick="triggerCameraUpload()">
                        <div class="icon-box bg-purple-light text-purple rounded-circle p-2">
                            <i class="bi bi-camera fs-5"></i>
                        </div>
                        <div class="text-start">
                            <div class="fw-semibold text-dark">Take Photo</div>
                            <small class="text-muted" style="font-size: 0.75rem;">Use camera</small>
                        </div>
                    </button>
                </div>
                <div class="p-2 bg-light">
                    <button type="button" class="btn btn-light w-100 text-muted btn-sm fw-bold" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="progress-container" style="display:none;">
    <div class="progress">
        <div id="progress-bar"
            class="progress-bar progress-bar-striped progress-bar-animated"
            role="progressbar"
            style="width: 0%;"
            aria-valuenow="0"
            aria-valuemin="0"
            aria-valuemax="100">
            0%
        </div>
    </div>
</div>
@endsection

@section('script')

{{-- <script src="assets/js/pages/dashboard.js"></script> --}}
<script src="/JS/validate.js"></script>
<script src="/JS/group.js"></script>
<script src="/JS/customer.js"></script>
<input type="hidden" id="customer_id">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Searchable Selects for all inputs with .select2 class
        // Exclude specific ajax-loaded ones to avoid double-init issues if they have their own logic
        $('.select2').not('#state, #city, #bank_name, #branch').select2({
            width: '100%'
        });

        // --- ScrollSpy Logic ---
        const sections = ['section-basic', 'section-address', 'section-occupation', 'section-documents', 'section-bank', 'section-guardian'];
        const navLinks = document.querySelectorAll('.nav-pills-custom .nav-link');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionEl = document.getElementById(section);
                if (sectionEl) { // Check if element exists
                    const sectionTop = sectionEl.offsetTop;
                    const sectionHeight = sectionEl.clientHeight;
                    // Offset for sticky header
                    if (scrollY >= (sectionTop - 150)) {
                        current = '#' + section;
                    }
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === current) {
                    link.classList.add('active');
                }
            });
            // Default to first if at top
            if (scrollY < 200 && navLinks.length > 0) {
                navLinks[0].classList.add('active');
            }
        });

        // --- Simple Completion Progress (Visual Only for now) ---
        // This calculates based on filled inputs in the form
        function updateProgress() {
            const inputs = document.querySelectorAll('.form-control, .form-select');
            let total = 0;
            let filled = 0;

            // Filter only visible inputs
            inputs.forEach(input => {
                if (input.offsetParent !== null && input.type !== 'hidden') {
                    total++;
                    if (input.value.trim() !== '') {
                        filled++;
                    }
                }
            });

            const percent = total === 0 ? 0 : Math.round((filled / total) * 100);

            // Update Ring
            const circle = document.querySelector('.progress-ring__circle');
            if (circle) {
                const radius = circle.r.baseVal.value;
                const circumference = radius * 2 * Math.PI;
                circle.style.strokeDasharray = `${circumference} ${circumference}`;
                const offset = circumference - (percent / 100) * circumference;
                circle.style.strokeDashoffset = offset;
            }

            const text = document.getElementById('completion-percentage');
            if (text) text.innerText = percent + '%';
        }

        // Run on load and input change
        updateProgress();
        $('input, select, textarea').on('change input', updateProgress);
    });

    // --- Profile Image Logic ---
    window.openImageSelectionModal = function() {
        $('#imageSelectionModal').modal('show');
    };

    window.triggerFileUpload = function() {
        // Hide modal
        $('#imageSelectionModal').modal('hide');
        // Trigger file input
        $('#hidden_file_upload').click();
    };

    window.triggerCameraUpload = function() {
        // Hide modal
        $('#imageSelectionModal').modal('hide');

        // Try global camera first if available, else fallback to standard capture input
        if (typeof window.openGlobalCamera === 'function') {
            window.openGlobalCamera('#hidden_camera_upload');
        } else {
            $('#hidden_camera_upload').click();
        }
    };

    window.previewProfileImage = function(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);

            // Also update the original form input if it exists
            const mainInput = document.getElementById('cus_phto');
            if (mainInput) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(input.files[0]);
                mainInput.files = dataTransfer.files;
            }
        }
    };
</script>

<style>
    /* Progress Ring CSS */
    .progress-ring__circle {
        transition: stroke-dashoffset 0.35s;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }
</style>

<!-- Select2 JavaScript -->


{{-- ===================== PROVINCE & CITY (SL LOCATIONS) ===================== --}}
<script>
    $(document).ready(function() {
        // Province / State - load from /sl-locations/provinces
        $('#state').select2({
            placeholder: "Select Province",
            allowClear: true,
            ajax: {
                url: '/sl-locations/provinces',
                dataType: 'json',
                delay: 200,
                processResults: function(data) {
                    // Expecting: { provinces: [ {id,text}, ... ] }
                    return {
                        results: data.provinces || []
                    };
                }
            },
            width: '100%'
        });

        // City / Town - depends on selected province
        $('#city').select2({
            placeholder: "Select City / Town",
            allowClear: true,
            ajax: {
                url: '/sl-locations/cities',
                dataType: 'json',
                delay: 200,
                data: function(params) {
                    return {
                        province_id: $('#state').val(),
                        q: params.term || ''
                    };
                },
                processResults: function(data) {
                    // Expecting: { cities: [ {id,text}, ... ] }
                    return {
                        results: data.cities || []
                    };
                }
            },
            width: '100%'
        });

        // When province changes, clear city selection
        $('#state').on('change', function() {
            $('#city').val(null).trigger('change');
        });
    });
</script>


<script>
    // Global variables for settings
    let appSettings = {};
    let nextCustomerId = '';

    $(document).ready(function() {
        // 1. Determine if Edit Mode
        const pathSegments = window.location.pathname.split('/');
        // Assuming URL is /customers/{id}/edit or /customers/create
        // or /customers/{id}
        let customerId = null;
        if (pathSegments.includes('edit')) {
            customerId = pathSegments[pathSegments.indexOf('edit') - 1]; // get ID before 'edit'
        } else if (pathSegments.length > 2 && !isNaN(pathSegments[pathSegments.length - 1])) {
            // Maybe /customers/{id}?
            customerId = pathSegments[pathSegments.length - 1];
        }

        // 2. Fetch Form Data
        $.ajax({
            url: "{{ route('customers.form-data') }}",
            type: "GET",
            data: customerId ? {
                id: customerId
            } : {},
            success: function(response) {
                appSettings = response.settings;
                nextCustomerId = response.next_customer_id;

                // Populate Routes
                const rootSelect = $('#root');
                if (response.routes && response.routes.length > 0) {
                    response.routes.forEach(route => {
                        rootSelect.append(new Option(`${route.name}-${route.root_code}`, route.id_route));
                    });
                }

                // Handle Customer Number Logic
                handleCustomerNumber(response);

                // If Edit Mode, Populate Fields
                if (response.customer) {
                    populateCustomerData(response.customer);
                }
            },
            error: function(err) {
                console.error("Error loading form data", err);
                alert("Failed to load form data. Please refresh.");
            }
        });
    });

    function handleCustomerNumber(data) {
        const cusType = data.settings['customer_num_type'];
        const cusFmt = data.settings['customer_format'];
        const input = $('#cus_number');
        const hiddenLabel = $('#formatted_num_use');
        const displayLabel = $('#formatted_num');

        if (data.customer) {
            // Edit mode: just show the code
            input.val(data.customer.customer_code).prop('readonly', true);
            hiddenLabel.text(data.customer.customer_code);
            return;
        }

        if (cusType === "Customize") {
            input.prop('readonly', false).attr('placeholder', 'Enter Number');
            input.on('keyup', function() {
                create_id_2(this.value);
            });
        } else if (cusType === "Format") {
            // Logic to format number
            // str_replace equivalent
            // ['@Center_No@', '@Group_No@','@Customize_No@','@Auto_ID@','@Branch_No@','@Root@','@Center_Cus_Count@'],
            // ['C000', 'G000','Customize No',$formatted_customer_id,'@Branch_No@','@Root@','CenterCustomerCount'],

            let newNum = cusFmt
                .replace('@Center_No@', 'C000')
                .replace('@Group_No@', 'G000')
                .replace('@Auto_ID@', data.next_customer_id)
                .replace('@Branch_No@', '@Branch_No@') // Placeholder?
                .replace('@Root@', '@Root@') // Placeholder?
                .replace('@Center_Cus_Count@', 'CenterCustomerCount'); // Placeholder?

            if (cusFmt.includes('@Customize_No@')) {
                newNum = newNum.replace('@Customize_No@', 'Customize No');
                input.prop('readonly', false).attr('type', 'number');
                input.on('keyup', function() {
                    create_id(this.value);
                });
                displayLabel.text(newNum).show();
                hiddenLabel.text(newNum);
            } else {
                newNum = newNum.replace('@Customize_No@', 'Customize No'); // Just in case
                input.val(newNum).prop('readonly', true);
                hiddenLabel.text(newNum);
            }
        }
    }

    function populateCustomerData(customer) {
        // Update Page Title
        $('.page-title').text('Edit Customer Details');
        $('.breadcrumb-item.active').text('Edit Customer');

        // Set Customer ID
        $('#customer_id').val(customer.id);

        // Basic Details
        $('#root').val(customer.route_id).trigger('change');
        $('#title').val(customer.title);
        $('#civil_status').val(customer.civil_status);
        $('#f_name').val(customer.first_name);
        $('#last_name').val(customer.last_name);
        $('#email').val(customer.email);
        $('#contact_number').val(customer.contact_no);
        $('#contact_number_2').val(customer.contact_no_2 || '');
        // $('#business_registration').val(customer.business_reg_no); // if exists
        $('#nic').val(customer.new_nic || customer.nic);
        $('#gender').val(customer.gender);
        $('#dob').val(customer.dob);
        $('#landline').val(customer.landline || '');

        // Address
        $('#curr_address_01').val(customer.address_1);
        $('#curr_address_02').val(customer.address_2);
        $('#curr_address_03').val(customer.address_3);

        $('#per_address_01').val(customer.permanent_address_1);
        $('#per_address_02').val(customer.permanent_address_2);
        $('#per_address_03').val(customer.permanent_address_3);

        // Province/City (Best effort - ideally needs pre-fetching option)
        if (customer.province_id) {
            // Create option if using AJAX and valid
            if ($('#state').find("option[value='" + customer.province_id + "']").length) {
                $('#state').val(customer.province_id).trigger('change');
            } else {
                // Create a temporary option
                // Note: We need the name, which we might not have.
                // Assuming standard usage or that it's just value setting.
                // $('#state').append(new Option("Selected Province ("+customer.province_id+")", customer.province_id, true, true)).trigger('change');
            }
        }

        $('#note').val(customer.description || customer.note || '');
        $('#longitude').val(customer.longitude);
        $('#latitude').val(customer.latitude);

        // Occupation
        $('#occu_job_position').val(customer.job_position);
        $('#occu_monthly_salary').val(customer.monthly_salary);
        $('#occu_address_01').val(customer.work_address_1);
        $('#occu_address_02').val(customer.work_address_2);
        $('#occu_address_03').val(customer.work_address_3);
        $('#occu_contact_no').val(customer.work_contact_no);
        $('#occu_longitude').val(customer.work_longitude);
        $('#occu_latitude').val(customer.work_latitude);

        // Guardian (if flat structure)
        $('#gua_title').val(customer.guarantor_title || '');
        $('#gua_name').val(customer.guarantor_name);
        $('#gua_nic').val(customer.guarantor_nic);
        $('#guardian_gender').val(customer.guarantor_gender);
        $('#gua_relation').val(customer.guarantor_relation);
        $('#gua_occu').val(customer.guarantor_occupation);
        $('#gua_contact').val(customer.guarantor_contact_no);
        $('#gua_address_01').val(customer.guarantor_address_1);
        $('#gua_address_02').val(customer.guarantor_address_2);
        $('#gua_address_03').val(customer.guarantor_address_3);

        // Banks - This requires a separate fetch or including it in the getFormData
        // We will leave the existing AJAX logic for bank to handle it (load_bank route) or add it here if needed.
        // There is existing code: function load_bank(id) ...
        // We should trigger that if it exists.
        if (typeof load_bank === 'function') {
            load_bank(customer.id);
        }
    }
</script>



{{-- ===================== BANK SELECT2 (LIVE BANKS + BRANCHES) ===================== --}}
<script>
    $(function initLiveBankSelectors() {
        // JS replacement removed as HTML now uses <select> tags directly.

        function normalizeBankItem(it) {
            let code = it.code || it.id || '';
            let name = it.name || '';
            if (!name && it.text) {
                const raw = String(it.text).trim();
                const m = raw.match(/\s*\((\d{3,4})\)\s*$/);
                if (m) {
                    if (!code) code = m[1];
                    name = raw.replace(m[0], '').trim();
                } else {
                    name = raw;
                }
            }
            if (!name && code) name = code;
            return {
                id: code,
                code,
                name,
                text: `${name} (${code})`
            };
        }

        function normalizeBranchItem(br) {
            let code = br.branch_code || br.id || '';
            let name = br.branch_name || '';
            if (!name && br.text) {
                const raw = String(br.text).trim();
                const m = raw.match(/\s*\((\d{3,4})\)\s*$/);
                if (m) {
                    if (!code) code = m[1];
                    name = raw.replace(m[0], '').trim();
                } else {
                    name = raw;
                }
            }
            if (!name && code) name = code;
            return {
                id: code,
                branch_code: code,
                branch_name: name,
                text: `${name} (${code})`
            };
        }

        // BANK Select2
        $('#bank_name').select2({
                placeholder: 'Select Bank',
                allowClear: true,
                ajax: {
                    url: '/lk-live/banks',
                    dataType: 'json',
                    delay: 200,
                    data: params => ({
                        q: params.term || ''
                    }),
                    processResults: (data) => ({
                        results: (data.results || []).map(normalizeBankItem)
                    })
                },
                templateResult: item => item.text,
                templateSelection: item => item.text,
                minimumInputLength: 0,
                width: '100%'
            })
            .on('select2:select', function(e) {
                const item = normalizeBankItem(e.params.data);
                $('#bank_code').val(item.code);

                // Branch Select2 (depends on selected bank)
                $('#branch').prop('disabled', false).val(null).trigger('change').select2({
                        placeholder: 'Select Branch',
                        allowClear: true,
                        ajax: {
                            url: `/lk-live/banks/${item.code}/branches`,
                            dataType: 'json',
                            delay: 200,
                            data: params => ({
                                q: params.term || ''
                            }),
                            processResults: (data) => ({
                                results: (data.results || []).map(normalizeBranchItem)
                            })
                        },
                        templateResult: br => br.text,
                        templateSelection: br => br.text,
                        minimumInputLength: 0,
                        width: '100%'
                    })
                    .off('select2:select').on('select2:select', function(ev) {
                        const br = normalizeBranchItem(ev.params.data);
                        $('#branch').data('selected-name', br.branch_name);
                        $('#branch').val(br.branch_code);
                        const $opt = $('#branch').find('option:selected');
                        if ($opt.length === 0) {
                            $('#branch').append(new Option(br.text, br.id, true, true)).trigger('change');
                        }
                    });
            })
            .on('select2:clear', function() {
                $('#bank_code').val('');
                $('#branch').val(null).trigger('change').prop('disabled', true).empty();
            });

        // Initially disabled
        $('#branch').prop('disabled', true);
    });


    // Geolocation Scripts
    function getlocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            Swal.fire("Error", "Geolocation is not supported by this browser.", "error");
        }
    }

    function showPosition(position) {
        document.getElementById("latitude").value = position.coords.latitude;
        document.getElementById("longitude").value = position.coords.longitude;
    }

    function getlocation_occu() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPositionOccu, showError);
        } else {
            Swal.fire("Error", "Geolocation is not supported by this browser.", "error");
        }
    }

    function showPositionOccu(position) {
        document.getElementById("occu_latitude").value = position.coords.latitude;
        document.getElementById("occu_longitude").value = position.coords.longitude;
    }

    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                Swal.fire("Error", "User denied the request for Geolocation.", "error");
                break;
            case error.POSITION_UNAVAILABLE:
                Swal.fire("Error", "Location information is unavailable.", "error");
                break;
            case error.TIMEOUT:
                Swal.fire("Error", "The request to get user location timed out.", "error");
                break;
            case error.UNKNOWN_ERROR:
                Swal.fire("Error", "An unknown error occurred.", "error");
                break;
        }
    }
</script>

{{-- ===================== MAIN FORM LOGIC (DOC TYPES, NIC, BANK TABLE, ETC.) ===================== --}}
<script>
    $(document).ready(function() {
        // Load document types on page load
        loadDocumentTypes();

        // Root select2 (simple, static)
        $('#root').select2({
            placeholder: "Select Root",
            allowClear: true
        });

        // NIC -> DOB+Gender
        $('#nic').on('input', function() {
            let info = getBirthdayFromNIC($(this).val());
            if (info) {
                $('#dob').val(info.dob);
                $('#gender').val(info.gender);
            } else {
                $('#gender').val('-');
                $('#dob').val('Invalid NIC number.');
            }
        });

        // guardian NIC -> guardian gender
        $('#gua_nic').on('input', function() {
            let info = getBirthdayFromNIC($(this).val());
            if (info) {
                $('#guardian_gender').val(info.gender);
            } else {
                $('#guardian_gender').val('-');
            }
        });

        // Add Document row
        $('#addDocBtn').on('click', function() {
            var description = $('#otherDocDescription').val();

            if (!description || description === '') {
                Swal.fire("Error!", "Please select a document type!", "error");
                return;
            }

            var fileInput = $('<input type="file" class="form-control" name="documents[]" accept="application/pdf, image/*">');
            var removeBtn = $('<button type="button" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>');

            removeBtn.on('click', function() {
                $(this).closest('tr').remove();
            });

            var row = $('<tr>');
            row.append($('<td>').text(description));
            row.append($('<td>').append(fileInput));
            row.append($('<td>').append(removeBtn));

            $('#documenttable tbody').append(row);
        });

        function loadDocumentTypes() {
            $.ajax({
                type: "GET",
                url: "/get-document-types", // Adjust route as needed
                success: function(data) {
                    var options = '<option value="">Select Document Type</option>';
                    $.each(data, function(index, value) {
                        options += '<option value="' + value.description + '">' + value.description + '</option>';
                    });
                    $('#otherDocDescription').html(options);
                },
                error: function() {
                    console.log('Error loading document types');
                    // Fallback or static options if endpoint fails or doesn't exist yet
                    var staticOptions = `
                        <option value="">Select Document Type</option>
                        <option value="NIC Copy">NIC Copy</option>
                        <option value="Billing Proof">Billing Proof</option>
                        <option value="Gramasewaka Certificate">Gramasewaka Certificate</option>
                        <option value="Other">Other</option>
                    `;
                    $('#otherDocDescription').html(staticOptions);
                }
            });
        }

        // Add Bank Row
        $('#addBankBtn').on('click', function() {
            var bankName = $('#bank_name option:selected').text();
            if (!bankName) bankName = $('#bank_name').val();

            var accountName = $('#account_name').val();
            var accountNumber = $('#account_number').val();

            var branchCode = $('#branch').val(); // numeric code
            var bankCode = $('#bank_code').val();

            if (!bankName || !accountName || !accountNumber) {
                Swal.fire("Error", "Please fill all bank details", "error");
                return;
            }

            var removeBtn = $('<button type="button" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>');
            removeBtn.on('click', function() {
                $(this).closest('tr').remove();
            });

            var row = $('<tr>');
            row.append($('<td>').text(bankName));
            row.append($('<td>').text(accountName));
            row.append($('<td>').text(accountNumber));
            row.append($('<td>').text(branchCode));
            row.append($('<td>').text(bankCode));
            row.append($('<td>').append(removeBtn));

            $('#bank_table tbody').append(row);

            // Clear inputs
            $('#bank_name').val(null).trigger('change');
            $('#account_name').val('');
            $('#account_number').val('');
            $('#branch').val(null).trigger('change');
            $('#bank_code').val('');
        });
    });
</script>
@endsection