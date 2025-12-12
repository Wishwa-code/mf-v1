@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <style>
        .style-tr > td {
            padding: 2px 15px;
        }

        .required-asterisk {
            color: red;
        }

        .section-card {
            margin-bottom: 1.5rem;
        }

        .section-header {
            font-size: 1rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #e9ecef;
            background-color: #f8f9fa;
        }

        .section-header span {
            border-left: 4px solid #0d6efd;
            padding-left: 0.5rem;
        }

        @media (max-width: 576px) {
            .section-header {
                font-size: 0.95rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="mt-3">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3 align-items-center">
                            <h4 class="page-title mb-0">Customer Details</h4>
                        </div>

                        <div class="modal-body px-1 px-md-2">

                            {{-- ===================== SECTION: BASIC CUSTOMER DETAILS ===================== --}}
                            <div class="card section-card">
                                <div class="section-header">
                                    <span>Basic Customer Details</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="cus_number" class="form-label">
                                                Customer Number <span class="required-asterisk">*</span>
                                            </label>

                                            @if($company->customer_num_type == "Customize")
                                                <input type="text" id="cus_number" name="cus_number" class="form-control"
                                                       onkeyup="create_id_2(this.value)">
                                                <label id="formatted_num_use" hidden></label>
                                            @elseif($company->customer_num_type == "Format")
                                                @php
                                                    $newnum = str_replace(
                                                        ['@Center_No@', '@Group_No@','@Customize_No@','@Auto_ID@','@Branch_No@','@Root@','@Center_Cus_Count@'],
                                                        ['C000', 'G000','Customize No',$formatted_customer_id,'@Branch_No@','@Root@','CenterCustomerCount'],
                                                        $company->customer_format
                                                    );
                                                @endphp

                                                @if(strpos($company->customer_format, '@Customize_No@') !== false)
                                                    <input type="number" id="cus_number" name="cus_number" class="form-control"
                                                           onkeyup="create_id(this.value)">
                                                    <label id="formatted_num" style="color: red">{{ $newnum }}</label>
                                                    <label id="formatted_num_use" hidden>{{$newnum}}</label>
                                                @else
                                                    <input type="text" id="cus_number" name="cus_number" class="form-control"
                                                           value="{{ $newnum }}" readonly>
                                                    <label id="formatted_num_use" hidden>{{$newnum}}</label>
                                                @endif
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="root" class="form-label">Root</label>
                                            <select class="form-select" id="root" name="root">
                                                @foreach($route as $item)
                                                    <option value="{{$item->id_route}}">{{$item->name}}-{{$item->root_code}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="title" class="form-label">Title</label>
                                            <select class="form-select" id="title" name="title">
                                                <option>Mr</option>
                                                <option>Ms.</option>
                                                <option>Mrs</option>
                                                <option>Dr</option>
                                                <option>Rev</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="civil_status" class="form-label">Civil Status</label>
                                            <select class="form-select" id="civil_status" name="civil_status">
                                                <option>Married</option>
                                                <option>Single</option>
                                                <option>Seperated</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="f_name" class="form-label">
                                                First Name <span class="required-asterisk">*</span>
                                            </label>
                                            <input type="text" id="f_name" name="f_name" class="form-control">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="last_name" class="form-label">
                                                Middle/Last Name <span class="required-asterisk">*</span>
                                            </label>
                                            <input type="text" id="last_name" name="last_name" class="form-control">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" id="email" name="email" class="form-control">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="contact_number" class="form-label">
                                                Mobile No <span class="required-asterisk">*</span>
                                            </label>
                                            <input type="tel" id="contact_number" name="contact_number" class="form-control"
                                                   oninput="validateMobileNumber(this)" maxlength="10">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="contact_number_2" class="form-label">Mobile No 02</label>
                                            <input type="tel" id="contact_number_2" name="contact_number_2" class="form-control"
                                                   oninput="validateMobileNumber(this)" maxlength="10">
                                        </div>

                                        <div class="col-md-4" hidden>
                                            <label for="business_registration" class="form-label">Business Registration Number</label>
                                            <input type="tel" id="business_registration" name="business_registration" class="form-control">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="nic" class="form-label">
                                                NIC <span class="required-asterisk">*</span>
                                            </label>
                                            <input type="text" id="nic" name="nic" class="form-control"
                                                   oninput="validateNIC(this)" maxlength="12">
                                            <input type="hidden" id="new_nic" name="new_nic" class="form-control">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="gender" class="form-label">Gender</label>
                                            <select class="form-control" id="gender" name="gender">
                                                <option value="-">-</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="dob" class="form-label">Date Of Birth</label>
                                            <input type="text" id="dob" name="dob" class="form-control">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="landline" class="form-label">Landline Phone</label>
                                            <input type="tel" id="landline" name="landline" class="form-control"
                                                   onkeypress="validateContactNumber(event)">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="cus_phto" class="form-label">Customer Photo</label>
                                            <div class="d-flex flex-column flex-sm-row gap-1">
                                                <input type="file" id="cus_phto" name="cus_phto" class="form-control" accept="image/*" capture="environment">
                                                <button type="button" onclick="openGlobalCamera('#cus_phto')" class="btn btn-outline-secondary mt-1 mt-sm-0">
                                                    📷
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ===================== SECTION: ADDRESS & LOCATION ===================== --}}
                            <div class="card section-card">
                                <div class="section-header">
                                    <span>Address & Location</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        {{-- Current Address --}}
                                        <div class="col-12">
                                            <label class="form-label">Current Address</label>
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <input type="text" id="curr_address_01" class="form-control" placeholder="Address line 1">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" id="curr_address_02" class="form-control" placeholder="Address line 2">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" id="curr_address_03" class="form-control" placeholder="Address line 3">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Permanent Address --}}
                                        <div class="col-12">
                                            <label class="form-label">Permanent Address</label>
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <input type="text" id="per_address_01" class="form-control" placeholder="Address line 1">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" id="per_address_02" class="form-control" placeholder="Address line 2">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" id="per_address_03" class="form-control" placeholder="Address line 3">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Province / City --}}
                                        <div class="col-md-6">
                                            <label for="state" class="form-label">Province / State</label>
                                            <select id="state" name="state" class="form-control">
                                                <option value="">Select Province</option>
                                                {{-- options via AJAX --}}
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="city" class="form-label">City / Town</label>
                                            <select id="city" name="city" class="form-control">
                                                <option value="">Select City / Town</option>
                                                {{-- options via AJAX --}}
                                            </select>
                                        </div>

                                        {{-- Note --}}
                                        <div class="col-12">
                                            <label for="note" class="form-label">Note</label>
                                            <div class="form-floating">
                                                <textarea class="form-control" placeholder="Leave a comment here"
                                                          id="note" name="note" style="height: 100px"></textarea>
                                                <label for="note">Enter any additional notes</label>
                                            </div>
                                        </div>

                                        {{-- Coordinates --}}
                                        <div class="col-md-6">
                                            <label for="longitude" class="form-label">Longitude</label>
                                            <input type="text" id="longitude" name="longitude" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="latitude" class="form-label">Latitude</label>
                                            <div class="d-flex gap-2">
                                                <input type="text" id="latitude" name="latitude" class="form-control">
                                                <button type="button" class="btn btn-danger" onclick="getlocation();">
                                                    Get Location
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ===================== SECTION: OCCUPATION DETAILS ===================== --}}
                            <div class="card border-secondary border section-card">
                                <div class="section-header">
                                    <span style="color: red">Occupation Details</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="occu_job_position" class="form-label">Job Position</label>
                                            <input type="text" id="occu_job_position" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="occu_monthly_salary" class="form-label">Monthly Salary</label>
                                            <input type="text" id="occu_monthly_salary" class="form-control">
                                        </div>

                                        <div class="col-12">
                                            <label for="occu_address_01" class="form-label">Working Place Address</label>
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <input type="text" id="occu_address_01" class="form-control" placeholder="Address line 1">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" id="occu_address_02" class="form-control" placeholder="Address line 2">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" id="occu_address_03" class="form-control" placeholder="Address line 3">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="occu_contact_no" class="form-label">Working Place Contact Number</label>
                                            <input type="tel" id="occu_contact_no" class="form-control"
                                                   oninput="validateMobileNumber(this)" maxlength="10">
                                        </div>

                                        <div class="col-md-6"></div>

                                        <div class="col-md-6">
                                            <label for="occu_longitude" class="form-label">Longitude</label>
                                            <input type="text" id="occu_longitude" name="occu_longitude" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="occu_latitude" class="form-label">Latitude</label>
                                            <div class="d-flex gap-2">
                                                <input type="text" id="occu_latitude" name="occu_latitude" class="form-control">
                                                <button type="button" class="btn btn-danger" onclick="getlocation_occu();">
                                                    Get Location
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ===================== SECTION: REQUIRED DOCUMENTS ===================== --}}
                            <div class="card border-secondary border section-card">
                                <div class="section-header">
                                    <span style="color: red">Required Document</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-6">
                                            <label for="otherDocDescription" class="form-label">Document Type</label>
                                            <select class="form-control" id="otherDocDescription">
                                                <option value="">Select Document Type</option>
                                                <!-- Options loaded dynamically -->
                                            </select>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <button type="button" class="btn btn-success mt-2 mt-md-0" id="addDocBtn">Add Document</button>
                                        </div>
                                    </div>

                                    <div class="table-responsive-sm mt-3">
                                        <table class="table table-centered mb-0" id="documenttable">
                                            <thead>
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

                            {{-- ===================== SECTION: BANK DETAILS ===================== --}}
                            <div class="card border-secondary border section-card">
                                <div class="section-header">
                                    <span style="color: red">Bank Details</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="bank_name" class="form-label">Bank Name</label>
                                            <input type="text" id="bank_name" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="account_name" class="form-label">Account Name</label>
                                            <input type="text" id="account_name" class="form-control">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="account_number" class="form-label">Account Number</label>
                                            <input type="text" id="account_number" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="branch" class="form-label">Branch code</label>
                                            <input type="text" id="branch" class="form-control">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="bank_code" class="form-label">Bank code</label>
                                            <input type="text" id="bank_code" class="form-control">
                                        </div>

                                        <div class="col-md-6 text-md-end">
                                            <button type="button" class="btn btn-success mt-2 mt-md-4" id="addBankBtn">
                                                Add Bank Account
                                            </button>
                                        </div>
                                    </div>

                                    <div class="table-responsive-sm mt-3">
                                        <table class="table table-centered mb-0" id="bank_table">
                                            <thead>
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

                            {{-- ===================== SECTION: GUARDIAN DETAILS ===================== --}}
                            <div class="card border-secondary border section-card">
                                <div class="section-header">
                                    <span style="color: red">Guardian Details</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="gua_title" class="form-label">Guardian Title</label>
                                            <select class="form-select" id="gua_title" name="gua_title">
                                                <option>Mr</option>
                                                <option>Mrs</option>
                                            </select>
                                        </div>

                                        <div class="col-md-8">
                                            <label for="gua_name" class="form-label">
                                                Guardian Name <span class="required-asterisk">*</span>
                                            </label>
                                            <input type="text" id="gua_name" name="gua_name" class="form-control">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="gua_nic" class="form-label">
                                                NIC <span class="required-asterisk">*</span>
                                            </label>
                                            <input type="text" id="gua_nic" name="gua_nic" class="form-control"
                                                   oninput="validateNIC(this)" maxlength="12">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="guardian_gender" class="form-label">Gender</label>
                                            <select class="form-control" id="guardian_gender" name="guardian_gender">
                                                <option value="-">-</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="gua_relation" class="form-label">Relation to borrower</label>
                                            <input type="text" id="gua_relation" name="gua_relation" class="form-control">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="gua_occu" class="form-label">Occupation</label>
                                            <input type="text" id="gua_occu" name="gua_occu" class="form-control">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="gua_contact" class="form-label">Contact No</label>
                                            <input type="tel" id="gua_contact" name="gua_contact" class="form-control"
                                                   oninput="validateMobileNumber(this)" maxlength="10">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Address</label>
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <input type="text" id="gua_address_01" class="form-control" placeholder="Address line 1">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" id="gua_address_02" class="form-control" placeholder="Address line 2">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" id="gua_address_03" class="form-control" placeholder="Address line 3">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ===================== SAVE BUTTON ===================== --}}
                            <div class="d-flex justify-content-end mt-2">
                                <button type="button" class="btn btn-success"
                                        onclick="validateSubmitCustomer(event)">
                                    <i class="bi bi-save"></i>&nbsp;&nbsp;Save Customer
                                </button>
                            </div>

                        </div> {{-- end modal-body --}}
                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div> <!-- end col-12 -->
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
    <script src="assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="assets/vendor/daterangepicker/daterangepicker.js"></script>
    {{-- <script src="assets/js/pages/dashboard.js"></script> --}}
    <script src="../JS/validate.js"></script>
    <script src="../JS/group.js"></script>
    <script src="../JS/customer.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    {{-- ===================== PROVINCE & CITY (SL LOCATIONS) ===================== --}}
    <script>
        $(document).ready(function () {
            // Province / State - load from /sl-locations/provinces
            $('#state').select2({
                placeholder: "Select Province",
                allowClear: true,
                ajax: {
                    url: '/sl-locations/provinces',
                    dataType: 'json',
                    delay: 200,
                    processResults: function (data) {
                        // Expecting: { provinces: [ {id,text}, ... ] }
                        return { results: data.provinces || [] };
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
                    data: function (params) {
                        return {
                            province_id: $('#state').val(),
                            q: params.term || ''
                        };
                    },
                    processResults: function (data) {
                        // Expecting: { cities: [ {id,text}, ... ] }
                        return { results: data.cities || [] };
                    }
                },
                width: '100%'
            });

            // When province changes, clear city selection
            $('#state').on('change', function () {
                $('#city').val(null).trigger('change');
            });
        });
    </script>

    {{-- ===================== CUSTOMER NUMBER HELPER ===================== --}}
    <script>
        function create_id(value) {
            @if($company->customer_num_type == "Format")
            var originalFormat = '{{ $newnum }}';
            document.getElementById('formatted_num').innerText = originalFormat.replace('Customize No', value);
            document.getElementById('formatted_num_use').innerText = originalFormat.replace('Customize No', value);
            @endif
        }

        function create_id_2(value) {
            document.getElementById('formatted_num_use').innerText = value;
        }
    </script>

    {{-- ===================== BANK SELECT2 (LIVE BANKS + BRANCHES) ===================== --}}
    <script>
        $(function initLiveBankSelectors() {
            // Upgrade #bank_name to <select>
            (function ensureBankSelect() {
                var $old = $('#bank_name');
                if ($old.length && !$old.is('select')) {
                    var $sel = $('<select/>', {
                        id: 'bank_name',
                        class: $old.attr('class') || 'form-control'
                    });
                    $old.replaceWith($sel);
                }
            })();

            // Upgrade #branch to <select>
            (function ensureBranchSelect() {
                var $old = $('#branch');
                if ($old.length && !$old.is('select')) {
                    var $sel = $('<select/>', {
                        id: 'branch',
                        class: $old.attr('class') || 'form-control'
                    });
                    $old.replaceWith($sel);
                }
            })();

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
                return {id: code, code, name, text: `${name} (${code})`};
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
                return {id: code, branch_code: code, branch_name: name, text: `${name} (${code})`};
            }

            // BANK Select2
            $('#bank_name').select2({
                placeholder: 'Select Bank',
                allowClear: true,
                ajax: {
                    url: '/lk-live/banks',
                    dataType: 'json',
                    delay: 200,
                    data: params => ({q: params.term || ''}),
                    processResults: (data) => ({
                        results: (data.results || []).map(normalizeBankItem)
                    })
                },
                templateResult: item => item.text,
                templateSelection: item => item.text,
                minimumInputLength: 0,
                width: '100%'
            })
                .on('select2:select', function (e) {
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
                            data: params => ({q: params.term || ''}),
                            processResults: (data) => ({
                                results: (data.results || []).map(normalizeBranchItem)
                            })
                        },
                        templateResult: br => br.text,
                        templateSelection: br => br.text,
                        minimumInputLength: 0,
                        width: '100%'
                    })
                        .off('select2:select').on('select2:select', function (ev) {
                        const br = normalizeBranchItem(ev.params.data);
                        $('#branch').data('selected-name', br.branch_name);
                        $('#branch').val(br.branch_code);
                        const $opt = $('#branch').find('option:selected');
                        if ($opt.length === 0) {
                            $('#branch').append(new Option(br.text, br.id, true, true)).trigger('change');
                        }
                    });
                })
                .on('select2:clear', function () {
                    $('#bank_code').val('');
                    $('#branch').val(null).trigger('change').prop('disabled', true).empty();
                });

            // Initially disabled
            $('#branch').prop('disabled', true);
        });
    </script>

    {{-- ===================== MAIN FORM LOGIC (DOC TYPES, NIC, BANK TABLE, ETC.) ===================== --}}
    <script>
        $(document).ready(function () {
            // Load document types on page load
            loadDocumentTypes();

            // Root select2 (simple, static)
            $('#root').select2({
                placeholder: "Select Root",
                allowClear: true
            });

            // NIC -> DOB+Gender
            $('#nic').on('input', function () {
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
            $('#gua_nic').on('input', function () {
                let info = getBirthdayFromNIC($(this).val());
                if (info) {
                    $('#guardian_gender').val(info.gender);
                } else {
                    $('#guardian_gender').val('-');
                }
            });

            // Add Document row
            $('#addDocBtn').on('click', function () {
                var description = $('#otherDocDescription').val();

                if (!description || description === '') {
                    Swal.fire("Error!", "Please select a document type!", "error");
                    return;
                }

                var uniqueId = 'docInput_' + Date.now();

                var newRow = `
                    <tr>
                        <td>${description}</td>
                        <td>
                            <input type="file" id="${uniqueId}" class="form-control doc-file" accept="image/*">
                            <button type="button" class="btn btn-outline-secondary mt-1" onclick="openGlobalCamera('#${uniqueId}')">📷</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger removeDocBtn">Remove</button>
                        </td>
                    </tr>
                `;

                $('#documenttable tbody').append(newRow);
                $('#otherDocDescription').val('');
            });

            // Remove document row
            $('#documenttable').on('click', '.removeDocBtn', function () {
                $(this).closest('tr').remove();
            });

            // Add Bank row (table)
            $('#addBankBtn').off('click').on('click', function () {
                const $bank = $('#bank_name');
                const bankDataArr = ($bank.data('select2') && $bank.select2('data')) ? $bank.select2('data') : [];
                const bankText = bankDataArr[0]?.text || $bank.val() || '';
                const bankCode = $('#bank_code').val() || bankDataArr[0]?.id || '';
                const bankName = String(bankText).replace(/\s*\(\d{3,4}\)\s*$/, '').trim();

                const accountName = $('#account_name').val();
                const accountNumber = $('#account_number').val();
                const branchCode = $('#branch').val(); // select2 value is branch code

                if (!bankName || !accountName || !accountNumber || !branchCode || !bankCode) {
                    Swal.fire("Error!", "All fields are required!", "error");
                    return;
                }

                let isDuplicate = false;
                $('#bank_table tbody tr').each(function () {
                    const rowBankName = $(this).find('td').eq(0).text();
                    const rowAccountName = $(this).find('td').eq(1).text();
                    const rowAccountNo = $(this).find('td').eq(2).text();
                    const rowBranchCode = $(this).find('td').eq(3).text();
                    const rowBankCode = $(this).find('td').eq(4).text();

                    if (
                        rowBankName === bankName &&
                        rowAccountName === accountName &&
                        rowAccountNo === accountNumber &&
                        rowBranchCode === branchCode &&
                        rowBankCode === bankCode
                    ) {
                        isDuplicate = true;
                        return false;
                    }
                });

                if (isDuplicate) {
                    Swal.fire("Error!", "This bank account already exists in the table.", "error");
                    return;
                }

                const newRow = `
                    <tr>
                        <td>${bankName}</td>
                        <td>${accountName}</td>
                        <td>${accountNumber}</td>
                        <td>${branchCode}</td>
                        <td>${bankCode}</td>
                        <td><button type="button" class="btn btn-danger remove-btn">Remove</button></td>
                    </tr>
                `;
                $('#bank_table tbody').append(newRow);

                $('#account_name').val('');
                $('#account_number').val('');
                $('#bank_code').val('');
                $('#branch').val(null).trigger('change');
                $('#bank_name').val(null).trigger('change');
            });

            $('#bank_table').on('click', '.remove-btn', function () {
                $(this).closest('tr').remove();
            });
        });

        // NIC validation
        function validateNIC(input) {
            input.value = input.value.replace(/[^0-9xvXV]/g, '').toUpperCase();
        }

        function validateMobileNumber(input) {
            input.value = input.value.replace(/[^0-9]/g, '');
        }

        // NIC -> DOB + Gender (same logic)
        function getBirthdayFromNIC(nic) {
            nic = (nic || '').toString().trim().toUpperCase();
            if (!nic) return null;

            var NICNo = nic;
            var dayText = 0;
            var year = "";
            var month = "";
            var day = "";
            var gender = "";

            if (NICNo.length != 10 && NICNo.length != 12) {
                return null;
            } else if (NICNo.length == 10 && !/^\d{9}[VX]$/.test(NICNo)) {
                return null;
            } else if (NICNo.length == 12 && !/^\d{12}$/.test(NICNo)) {
                return null;
            }

            if (NICNo.length == 10) {
                year = "19" + NICNo.substr(0, 2);
                dayText = parseInt(NICNo.substr(2, 3));
            } else {
                year = NICNo.substr(0, 4);
                dayText = parseInt(NICNo.substr(4, 3));
            }

            if (dayText > 500) {
                gender = "Female";
                dayText = dayText - 500;
            } else {
                gender = "Male";
            }

            if (dayText < 1 || dayText > 366) {
                return null;
            }

            if (dayText > 335) {
                day = dayText - 335;
                month = "December";
            } else if (dayText > 305) {
                day = dayText - 305;
                month = "November";
            } else if (dayText > 274) {
                day = dayText - 274;
                month = "October";
            } else if (dayText > 244) {
                day = dayText - 244;
                month = "September";
            } else if (dayText > 213) {
                day = dayText - 213;
                month = "August";
            } else if (dayText > 182) {
                day = dayText - 182;
                month = "July";
            } else if (dayText > 152) {
                day = dayText - 152;
                month = "June";
            } else if (dayText > 121) {
                day = dayText - 121;
                month = "May";
            } else if (dayText > 91) {
                day = dayText - 91;
                month = "April";
            } else if (dayText > 60) {
                day = dayText - 60;
                month = "March";
            } else if (dayText < 32) {
                month = "January";
                day = dayText;
            } else if (dayText > 31) {
                day = dayText - 31;
                month = "February";
            }

            const monthNames = {
                "January": "01", "February": "02", "March": "03", "April": "04",
                "May": "05", "June": "06", "July": "07", "August": "08",
                "September": "09", "October": "10", "November": "11", "December": "12"
            };

            const dob = `${year}-${monthNames[month]}-${String(day).padStart(2, '0')}`;
            return {dob, gender};
        }

        // Geolocation
        function getlocation() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var latitude = position.coords.latitude;
                    var longitude = position.coords.longitude;
                    $('#latitude').val(latitude);
                    $('#longitude').val(longitude);
                }, function (error) {
                    console.error("Error getting location:", error);
                });
            } else {
                console.error("Geolocation is not supported by this browser.");
            }
        }

        function getlocation_occu() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var latitude = position.coords.latitude;
                    var longitude = position.coords.longitude;
                    $('#occu_latitude').val(latitude);
                    $('#occu_longitude').val(longitude);
                }, function (error) {
                    console.error("Error getting location:", error);
                });
            } else {
                console.error("Geolocation is not supported by this browser.");
            }
        }

        // Load document types from /settings/all
        function loadDocumentTypes() {
            $.ajax({
                type: "GET",
                url: "/settings/all",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data) {
                    const items = data.items || {};
                    let documentTypes = [];

                    if (items.document_types) {
                        try {
                            documentTypes = JSON.parse(items.document_types);
                        } catch (e) {
                            console.error('Error parsing document types:', e);
                            documentTypes = getDefaultDocumentTypes();
                        }
                    } else {
                        documentTypes = getDefaultDocumentTypes();
                    }

                    const dropdown = $('#otherDocDescription');
                    dropdown.empty();
                    dropdown.append('<option value="">Select Document Type</option>');

                    documentTypes.forEach(function (type) {
                        dropdown.append(`<option value="${type}">${type}</option>`);
                    });
                },
                error: function (xhr) {
                    console.error('Error loading document types:', xhr.responseText || xhr.statusText);
                    const dropdown = $('#otherDocDescription');
                    dropdown.empty();
                    dropdown.append('<option value="">Select Document Type</option>');

                    getDefaultDocumentTypes().forEach(function (type) {
                        dropdown.append(`<option value="${type}">${type}</option>`);
                    });
                }
            });
        }

        function getDefaultDocumentTypes() {
            return [
                'NIC Copy',
                'Income Certificate'
            ];
        }
    </script>
@endsection
