@extends('layout.admin')

@section('head')
<!-- Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<style>
    /* Modern Styles Ported from Leads Create */
    .card-modern {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: #fff;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #343a40;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        height: 50px;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 0.6rem 1rem;
        font-size: 0.95rem;
        background-color: #f8f9fa;
        transition: all 0.2s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #556ee6;
        box-shadow: 0 0 0 0.25rem rgba(85, 110, 230, 0.1);
        background-color: #fff;
    }

    textarea.form-control {
        height: auto;
        min-height: 120px;
    }

    /* Select2 Customization */
    .select2-container--default .select2-selection--single {
        height: 50px;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        display: flex;
        align-items: center;
        background-color: #f8f9fa;
    }

    .select2-container--default .select2-selection--single:focus-within {
        border-color: #556ee6;
        box-shadow: 0 0 0 0.25rem rgba(85, 110, 230, 0.1);
        background-color: #fff;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 50px;
        right: 10px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 15px;
        font-size: 0.95rem;
        color: #495057;
        font-weight: 500;
    }

    .select2-dropdown {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        margin-top: 8px;
    }

    .select2-search__field {
        border-radius: 8px !important;
        padding: 8px 12px !important;
    }

    /* Section Headers */
    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .section-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.25rem;
    }

    /* Buttons */
    .btn-modern {
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .required-asterisk {
        color: red;
    }

    /* Sticky Header Styles */
    .sticky-top-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        background-color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 10px 0;
        margin-top: 0 !important;
        margin-bottom: 2rem !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
</style>
@endsection

@section('content')
<div class="container-fluid pb-5">
    <!-- Page Header -->
    <!-- Page Header -->
    <div class="row align-items-center mb-4 mt-3" id="page-header">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="page-title mb-1 fw-bold fs-3 text-dark">Customer Details</h4>
                    <ol class="breadcrumb m-0 small text-muted">
                        <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active text-primary">Create Customer</li>
                    </ol>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-lg rounded-pill shadow fw-bold" onclick="validateSubmitCustomer(event)">
                        <i class="bi bi-check-circle-fill me-2"></i> Submit Customer
                    </button>
                </div>
            </div>
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

    <div class="row justify-content-center">
        <div class="col-12">
            {{-- ===================== SECTION 1: BASIC CUSTOMER DETAILS ===================== --}}
            <div class="card glass-card">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="section-icon bg-light text-primary">
                            <i class="bi bi-person-lines-fill"></i>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark">Basic Customer Details</h5>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <label for="cus_number" class="form-label">
                                Customer Number <span class="required-asterisk">*</span>
                            </label>
                            @if($company?->customer_num_type == "Customize")
                            <input type="text" id="cus_number" name="cus_number" class="form-control" onkeyup="create_id_2(this.value)" placeholder="Enter Number">
                            <div class="invalid-feedback"></div>
                            <label id="formatted_num_use" hidden></label>
                            @elseif($company?->customer_num_type == "Format")
                            @php
                            $newnum = str_replace(
                            ['@Center_No@', '@Group_No@','@Customize_No@','@Auto_ID@','@Branch_No@','@Root@','@Center_Cus_Count@'],
                            ['C000', 'G000','Customize No',$formatted_customer_id,'@Branch_No@','@Root@','CenterCustomerCount'],
                            $company?->customer_format
                            );
                            @endphp

                            @if(strpos($company?->customer_format, '@Customize_No@') !== false)
                            <input type="number" id="cus_number" name="cus_number" class="form-control" onkeyup="create_id(this.value)">
                            <label id="formatted_num" style="color: red">{{ $newnum }}</label>
                            <label id="formatted_num_use" hidden>{{$newnum}}</label>
                            @else
                            <input type="text" id="cus_number" name="cus_number" class="form-control" value="{{ $newnum }}" readonly>
                            <div class="invalid-feedback"></div>
                            <label id="formatted_num_use" hidden>{{$newnum}}</label>
                            @endif
                            @endif
                        </div>

                        <div class="col-md-4">
                            <label for="root" class="form-label">Root</label>
                            <select class="form-select choices-select" id="root" name="root">
                                <option value="" selected disabled>Select Root</option>
                                @foreach($route as $item)
                                <option value="{{$item->id_route}}">{{$item->name}}-{{$item->root_code}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="title" class="form-label">Title</label>
                            <select class="form-select" id="title" name="title">
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
                            <select class="form-select choices-select" id="civil_status" name="civil_status">
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
                            <select class="form-control" id="gender" name="gender">
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
            <div class="card card-modern">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="section-icon bg-light text-info">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark">Address & Location</h5>
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
                            <select id="state" name="state" class="form-control">
                                <option value="">Select Province</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="city" class="form-label">City / Town</label>
                            <select id="city" name="city" class="form-control">
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
            <div class="card card-modern">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="section-icon bg-light text-warning">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark">Occupation Details</h5>
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
            <div class="card card-modern">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="section-icon bg-light text-secondary">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark">Required Documents</h5>
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
                            <button type="button" class="btn btn-success btn-modern w-100 w-md-auto" id="addDocBtn">Add Document</button>
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
            <div class="card card-modern">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="section-icon bg-light text-success">
                            <i class="bi bi-bank2"></i>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark">Bank Details</h5>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="bank_name" class="form-label">Bank Name</label>
                            <select id="bank_name" class="form-control">
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
                            <select id="branch" class="form-control">
                                <option value="">Select Branch</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="bank_code" class="form-label">Bank code</label>
                            <input type="text" id="bank_code" class="form-control" placeholder="Bank Code">
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-success btn-modern mt-md-4" id="addBankBtn">Add Bank Account</button>
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
            <div class="card card-modern">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="section-icon bg-light text-danger">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark">Guardian Details</h5>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <label for="gua_title" class="form-label">Guardian Title</label>
                            <select class="form-select" id="gua_title" name="gua_title">
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
                            <select class="form-control" id="guardian_gender" name="guardian_gender">
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


        </div> <!-- end col-lg-10 -->
    </div> <!-- end row -->
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
<script src="../JS/validate.js"></script>
<script src="../JS/group.js"></script>
<script src="../JS/customer.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

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


{{-- ===================== CUSTOMER NUMBER HELPER ===================== --}}
<script>
    function create_id(value) {
        $.ajax({
            url: "{{ route('customers.preview_number') }}",
            method: 'POST',
            data: {
                custom_val: value,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.formatted_number) {
                    $('#formatted_num').text(response.formatted_number);
                    $('#formatted_num_use').text(response.formatted_number);
                }
            },
            error: function(err) {
                console.error("Error fetching preview number", err);
            }
        });
    }

    function create_id_2(value) {
        $('#formatted_num_use').text(value);
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