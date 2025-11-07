@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <style>
        .style-tr>td {
            padding: 2px 15px
        }


    </style>

@endsection


@section('content')
    <div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h4 class="page-title">Customer Details</h4>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Customer Number <span class="required-asterisk">*</span></label>
                                        @if($company->customer_num_type == "Customize")
                                            <input type="text" id="cus_number" name="cus_number" class="form-control" onkeyup="create_id_2(this.value)">
                                            <label id="formatted_num_use" hidden></label>
                                        @elseif($company->customer_num_type == "Format")
                                            @php
                                                $newnum = str_replace(['@Center_No@', '@Group_No@','@Customize_No@','@Auto_ID@','@Branch_No@','@Root@','@Center_Cus_Count@'], ['C000', 'G000','Customize No',$formatted_customer_id,'@Branch_No@','@Root@','CenterCustomerCount'], $company->customer_format);
                                            @endphp

                                            @if(strpos($company->customer_format, '@Customize_No@') !== false)
                                                <input type="number" id="cus_number" name="cus_number" class="form-control" onkeyup="create_id(this.value)">
                                                <label id="formatted_num" style="color: red">{{ $newnum }}</label>
                                                <label id="formatted_num_use" hidden>{{$newnum}}</label>
                                            @else
                                                <input type="text" id="cus_number" name="cus_number" class="form-control" value="{{ $newnum }}" readonly>
                                                <label id="formatted_num_use" hidden>{{$newnum}}</label>
                                            @endif
                                        @endif


                                    </div>

                                    <div class="mb-3">
                                        <label for="example-select" class="form-label">Root</label>
                                        <select class="form-select" id="root" name="root">
                                            @foreach($route as $item)
                                                <option value="{{$item->id_route}}">{{$item->name}}-{{$item->root_code}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="example-select" class="form-label">Title</label>
                                        <select class="form-select" id="title" name="title">
                                            <option>Mr</option>
                                            <option>Ms.</option>
                                            <option>Mrs</option>
                                            <option>Dr</option>
                                            <option>Rev</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="example-select" class="form-label">Civil Status</label>
                                        <select class="form-select" id="civil_status" name="civil_status">
                                            <option>Married</option>
                                            <option>Single</option>
                                            <option>Seperated</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">First Name <span class="required-asterisk">*</span></label>
                                        <input type="text" id="f_name" name="f_name" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Middle/Last Name <span class="required-asterisk">*</span></label>
                                        <input type="text" id="last_name" name="last_name" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Email</label>
                                        <input type="email" id="email" name="email" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Mobile No <span class="required-asterisk">*</span></label>
                                        <input type="tel" id="contact_number" name="contact_number" class="form-control" oninput="validateMobileNumber(this)" maxlength="10">
                                    </div>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Mobile No 02</label>
                                        <input type="tel" id="contact_number_2" name="contact_number_2" class="form-control" oninput="validateMobileNumber(this)" maxlength="10">
                                    </div>
                                    <div class="mb-3" hidden>
                                        <label for="simpleinput" class="form-label">Business Registration Number</label>
                                        <input type="tel" id="business_registration" name="business_registration" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">NIC <span class="required-asterisk">*</span></label>
                                        <input type="text" id="nic" name="nic" class="form-control" oninput="validateNIC(this)" maxlength="12">
                                        <input type="hidden" id="new_nic" name="new_nic" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Gender</label>
                                        <select class="form-control" id="gender" name="gender">
                                            <option value="-">-</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Date Of Birth</label>
                                        <input type="text" id="dob" name="dob" class="form-control">
                                    </div>
                                    <div class="row mb-3">
                                        <label for="bank_name" class="form-label">Current Address</label>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <input type="text" id="curr_address_01" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <input type="text" id="curr_address_02" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <input type="text" id="curr_address_03" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="bank_name" class="form-label">Permanent Address</label>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <input type="text" id="per_address_01" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <input type="text" id="per_address_02" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <input type="text" id="per_address_03" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="state" class="form-label">Province / State</label>
                                        <select id="state" name="state" class="form-control">
                                            <option value="Western Province" selected>Western Province</option>
                                            <option value="Central Province">Central Province</option>
                                            <option value="Eastern Province">Eastern Province</option>
                                            <option value="North Central Province">North Central Province</option>
                                            <option value="Northern Province">Northern Province</option>
                                            <option value="North Western Province">North Western Province</option>
                                            <option value="Sabaragamuwa Province">Sabaragamuwa Province</option>
                                            <option value="Southern Province">Southern Province</option>
                                            <option value="Uva Province">Uva Province</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">City</label>
                                        <select  id="city" name="city" class="form-control">
                                            <?php
                                            $query = "SELECT * FROM cities";
                                            $cities = DB::select($query);
                                            ?>
                                            @foreach($cities as $item)
                                                <option value="{{$item->name_en}}">{{$item->name_en}}</option>
                                            @endforeach

                                        </select>
                                    </div>


                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Landline Phone</label>
                                        <input type="tel" id="landline" name="landline" class="form-control" onkeypress="validateContactNumber(event)">
                                    </div>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Customer Photo</label>
                                        <input type="file" id="cus_phto" name="cus_phto" class="form-control" accept="image/*" capture="environment">
                                        <button type="button" onclick="openGlobalCamera('#cus_phto')" class="btn btn-outline-secondary mt-1">📷</button>
                                    </div>
                                </div>




                            </div>
                            <div class="row">
                                <label for="simpleinput" class="form-label">Note</label>
                                <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="Leave a comment here" id="note" name="note"
                                      style="height: 100px"></textarea>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="simpleinput" class="form-label">Longitude</label>
                                        <input type="text" id="longitude" name="longitude" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-4">
                                        <label for="simpleinput" class="form-label">Latitude</label>
                                        <input type="text" id="latitude" name="latitude" class="form-control">
                                        <br>
                                        <input type="button" class="btn btn-danger" onclick="getlocation();"
                                               value="Get Location">
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="card border-secondary border">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label" style="color: red">Occupation Details</label>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="occu_job_position" class="form-label">Job Position</label>
                                                    <input type="text" id="occu_job_position" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="occu_monthly_salary" class="form-label">Monthly Salary</label>
                                                    <input type="text" id="occu_monthly_salary" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label for="occu_address_01" class="form-label">Working Place Address</label>
                                                <div class="col-md-4">
                                                    <input type="text" id="occu_address_01" class="form-control">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" id="occu_address_02" class="form-control">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" id="occu_address_03" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="occu_contact_no" class="form-label">Working Place Contact Number</label>
                                                    <input type="tel" id="occu_contact_no" class="form-control" oninput="validateMobileNumber(this)" maxlength="10">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-lg-6">
                                                    <label for="occu_longitude" class="form-label">Longitude</label>
                                                    <input type="text" id="occu_longitude" name="occu_longitude" class="form-control">
                                                </div>
                                                <div class="col-lg-6">
                                                    <label for="occu_latitude" class="form-label">Latitude</label>
                                                    <input type="text" id="occu_latitude" name="occu_latitude" class="form-control">
                                                    <br>
                                                    <input type="button" class="btn btn-danger" onclick="getlocation_occu();" value="Get Location">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>






                            <div class="row">
                                <div class="card border-secondary border">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label" style="color: red">Required Document</label>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label for="otherDocDescription" class="form-label">Document Type</label>
                                                        <select class="form-control" id="otherDocDescription">
                                                            <option value="">Select Document Type</option>
                                                            <!-- Options will be loaded dynamically -->
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-success" id="addDocBtn">Add Document</button>
                                        </div>
                                        <div class="table-responsive-sm">
                                            <table class="table table-centered mb-0" id="documenttable">
                                                <thead>
                                                <tr>
                                                    <th>Description</th>
                                                    <th>File</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="card border-secondary border">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label" style="color: red">Bank Details</label>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="bank_name" class="form-label">Bank Name</label>
                                                    <input type="text" id="bank_name" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="account_name" class="form-label">Account Name</label>
                                                    <input type="text" id="account_name" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="account_number" class="form-label">Account Number</label>
                                                    <input type="text" id="account_number" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="branch" class="form-label">Branch code</label>
                                                    <input type="text" id="branch" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="bank_code" class="form-label">Bank code</label>
                                                    <input type="text" id="bank_code" class="form-control">
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-success" id="addBankBtn">Add Bank Account</button>
                                        </div>
                                        <div class="table-responsive-sm">
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
                                                <!-- Dynamic content will be inserted here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <hr>
                            <div class="row">
                                <div class="card border-secondary border">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label" style="color: red">Guardian Details</label>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="mb-3">
                                                        <label for="example-select" class="form-label">Guardian Title</label>
                                                        <select class="form-select" id="gua_title" name="gua_title">
                                                            <option>Mr</option>
                                                            <option>Mrs</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="simpleinput" class="form-label">Guardian Name<span class="required-asterisk">*</span></label>
                                                        <input type="text" id="gua_name" name="gua_name" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="simpleinput" class="form-label">NIC <span class="required-asterisk">*</span></label>
                                                        <input type="text" id="gua_nic" name="gua_nic" class="form-control" oninput="validateNIC(this)" maxlength="12">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="simpleinput" class="form-label">Gender</label>
                                                        <select class="form-control" id="guardian_gender" name="guardian_gender">
                                                            <option value="-">-</option>
                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="simpleinput" class="form-label">Relation to borrower</label>
                                                        <input type="text" id="gua_relation" name="gua_relation" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="simpleinput" class="form-label">Occupation</label>
                                                        <input type="text" id="gua_occu" name="gua_occu" class="form-control">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="simpleinput" class="form-label">Contact No</label>
                                                        <input type="tel" id="gua_contact" name="gua_contact" class="form-control" oninput="validateMobileNumber(this)" maxlength="10">
                                                    </div>
                                                    <div class="row mb-3">
                                                        <label for="bank_name" class="form-label">Address</label>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <input type="text" id="gua_address_01" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <input type="text" id="gua_address_02" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <input type="text" id="gua_address_03" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>




                            <button type="button" class="btn btn-success" style="float: right" onclick="validateSubmitCustomer(event)"><i
                                        class="bi bi-save"></i>&nbsp;&nbsp;Save Customer</button>
                        </div>




                    </div> <!-- end card-->
                </div> <!-- end col -->


            </div>
            <!-- end row -->

        </div>










    </div>



    <div id="progress-container" style="display:none;">
        <div class="progress">
            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>
    </div>


@endsection

@section('script')
    <script src="assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="assets/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="assets/js/pages/dashboard.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/group.js"></script>
    <script src="../JS/customer.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        function validateNIC(input) {
            // Allow only digits, x, and v (case insensitive)
            input.value = input.value.replace(/[^0-9xvXV]/g, '').toUpperCase();
        }

        function validateMobileNumber(input) {
            // Allow only 10 digits
            input.value = input.value.replace(/[^0-9]/g, '');
        }

        function create_id(value){


            @if($company->customer_num_type == "Format")
            // Get the original format string
            var originalFormat = '{{ $newnum }}';


            // Update the label with the new formatted string
            document.getElementById('formatted_num').innerText = originalFormat.replace('Customize No', value);
            document.getElementById('formatted_num_use').innerText = originalFormat.replace('Customize No', value);
            @endif


        }

        function create_id_2(value){
            // Update the label with the new formatted string
            document.getElementById('formatted_num_use').innerText = value;
        }

    </script>
    <script>
        $(function initLiveBankSelectors() {
            // Upgrade #bank_name to <select id="bank_name">
            (function ensureBankSelect(){
                var $old = $('#bank_name');
                if ($old.length && !$old.is('select')) {
                    var $sel = $('<select/>', { id:'bank_name', class: $old.attr('class') || 'form-control' });
                    $old.replaceWith($sel);
                }
            })();

            // Upgrade #branch to <select id="branch">
            (function ensureBranchSelect(){
                var $old = $('#branch');
                if ($old.length && !$old.is('select')) {
                    var $sel = $('<select/>', { id:'branch', class: $old.attr('class') || 'form-control' });
                    $old.replaceWith($sel);
                }
            })();

            // Normalizers so display shows "Name (Code)" and we still get codes
            function normalizeBankItem(it){
                let code = it.code || it.id || '';
                let name = it.name || '';
                if (!name && it.text) {
                    const raw = String(it.text).trim();
                    const m = raw.match(/\s*\((\d{3,4})\)\s*$/);
                    if (m) { if (!code) code = m[1]; name = raw.replace(m[0],'').trim(); }
                    else { name = raw; }
                }
                if (!name && code) name = code;
                return { id: code, code, name, text: `${name} (${code})` };
            }
            function normalizeBranchItem(br){
                let code = br.branch_code || br.id || '';
                let name = br.branch_name || '';
                if (!name && br.text) {
                    const raw = String(br.text).trim();
                    const m = raw.match(/\s*\((\d{3,4})\)\s*$/);
                    if (m) { if (!code) code = m[1]; name = raw.replace(m[0],'').trim(); }
                    else { name = raw; }
                }
                if (!name && code) name = code;
                return { id: code, branch_code: code, branch_name: name, text: `${name} (${code})` };
            }

            // BANK Select2
            $('#bank_name').select2({
                placeholder: 'Select Bank',
                allowClear: true,
                ajax: {
                    url: '/lk-live/banks',
                    dataType: 'json',
                    delay: 200,
                    data: params => ({ q: params.term || '' }),
                    processResults: (data) => ({ results: (data.results||[]).map(normalizeBankItem) })
                },
                templateResult: item => item.text,
                templateSelection: item => item.text,
                minimumInputLength: 0,
                width: '100%'
            })
                .on('select2:select', function(e){
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
                            data: params => ({ q: params.term || '' }),
                            processResults: (data) => ({ results: (data.results||[]).map(normalizeBranchItem) })
                        },
                        templateResult: br => br.text,
                        templateSelection: br => br.text,
                        minimumInputLength: 0,
                        width: '100%'
                    })
                        .off('select2:select').on('select2:select', function(ev){
                        const br = normalizeBranchItem(ev.params.data);
                        // keep BRANCH CODE in the same #branch field (your requirement)
                        $('#branch').data('selected-name', br.branch_name);
                        $('#branch').val(br.branch_code);
                        // ensure <option> exists so form submit has the code
                        const $opt = $('#branch').find('option:selected');
                        if ($opt.length === 0) {
                            $('#branch').append(new Option(br.text, br.id, true, true)).trigger('change');
                        }
                    });
                })
                .on('select2:clear', function(){
                    $('#bank_code').val('');
                    $('#branch').val(null).trigger('change').prop('disabled', true).empty();
                });

            // Initially disabled
            $('#branch').prop('disabled', true);
        });
    </script>



    <script>
        $(document).ready(function() {
            // Load document types on page load
            loadDocumentTypes();

            $('#state').select2({
                placeholder: "Select Province", // Optional placeholder
                allowClear: true // Allows clearing the selection
            });

            $('#root').select2({
                placeholder: "Select Root", // Optional placeholder
                allowClear: true // Allows clearing the selection
            });




            $('#city').select2({
                placeholder: "Select City", // Optional placeholder
                allowClear: true // Allows clearing the selection
            });

            $('#nic').on('input', function() {
                let info = getBirthdayFromNIC($(this).val());
                if (info) { $('#dob').val(info.dob); $('#gender').val(info.gender); }
                else { $('#gender').val('-'); $('#dob').val('Invalid NIC number.'); }
            });

            // guardian nic -> gender
            $('#gua_nic').on('input', function(){
                let info = getBirthdayFromNIC($(this).val());
                if (info) { $('#guardian_gender').val(info.gender); }
                else { $('#guardian_gender').val('-'); }
            });

            $('#addDocBtn').on('click', function() {
                var description = $('#otherDocDescription').val();

                if (!description || description === '') {
                    Swal.fire("Error!", "Please select a document type!", "error");
                    return;
                }

                var uniqueId = 'docInput_' + Date.now(); // ensures unique ID based on timestamp

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

                // Clear the select field after adding
                $('#otherDocDescription').val('');
            });

            // Use event delegation to handle dynamically added remove buttons
            $('#documenttable').on('click', '.removeDocBtn', function() {
                $(this).closest('tr').remove();
            });
        });



        function getBirthdayFromNIC(nic) {
            // nic -> dob, gender (using exact HTML logic)
            nic = (nic||'').toString().trim().toUpperCase();
            if (!nic) return null;

            var NICNo = nic;
            var dayText = 0;
            var year = "";
            var month = "";
            var day = "";
            var gender = "";

            // Validation
            if (NICNo.length != 10 && NICNo.length != 12) {
                return null; // Invalid NIC NO
            } else if (NICNo.length == 10 && !/^\d{9}[VX]$/.test(NICNo)) {
                return null; // Invalid NIC NO
            } else if (NICNo.length == 12 && !/^\d{12}$/.test(NICNo)) {
                return null; // Invalid NIC NO
            }

            // Year
            if (NICNo.length == 10) {
                year = "19" + NICNo.substr(0, 2);
                dayText = parseInt(NICNo.substr(2, 3));
            } else {
                year = NICNo.substr(0, 4);
                dayText = parseInt(NICNo.substr(4, 3));
            }

            // Gender
            if (dayText > 500) {
                gender = "Female";
                dayText = dayText - 500;
            } else {
                gender = "Male";
            }

            // Day Digit Validation
            if (dayText < 1 || dayText > 366) {
                return null; // Invalid NIC NO
            }

            // Month calculation (exact HTML logic)
            if (dayText > 335) {
                day = dayText - 335;
                month = "December";
            }
            else if (dayText > 305) {
                day = dayText - 305;
                month = "November";
            }
            else if (dayText > 274) {
                day = dayText - 274;
                month = "October";
            }
            else if (dayText > 244) {
                day = dayText - 244;
                month = "September";
            }
            else if (dayText > 213) {
                day = dayText - 213;
                month = "August";
            }
            else if (dayText > 182) {
                day = dayText - 182;
                month = "July";
            }
            else if (dayText > 152) {
                day = dayText - 152;
                month = "June";
            }
            else if (dayText > 121) {
                day = dayText - 121;
                month = "May";
            }
            else if (dayText > 91) {
                day = dayText - 91;
                month = "April";
            }
            else if (dayText > 60) {
                day = dayText - 60;
                month = "March";
            }
            else if (dayText < 32) {
                month = "January";
                day = dayText;
            }
            else if (dayText > 31) {
                day = dayText - 31;
                month = "February";
            }

            // Convert month name to number for consistent format
            const monthNames = {
                "January": "01", "February": "02", "March": "03", "April": "04",
                "May": "05", "June": "06", "July": "07", "August": "08",
                "September": "09", "October": "10", "November": "11", "December": "12"
            };

            const dob = `${year}-${monthNames[month]}-${String(day).padStart(2,'0')}`;
            return { dob, gender };
        }






        function getlocation() {
            // Check if Geolocation is supported by the browser
            if ("geolocation" in navigator) {
                // Geolocation is supported
                // Use getCurrentPosition method to get the user's current position
                navigator.geolocation.getCurrentPosition(function(position) {
                    // Get latitude and longitude from the position object
                    var latitude = position.coords.latitude;
                    var longitude = position.coords.longitude;

                    // Log the latitude and longitude to the console (you can do further processing here)
                    console.log("Latitude:", latitude);
                    console.log("Longitude:", longitude);

                    // If you want to do something with the latitude and longitude, you can call a function here
                    // For example, set the values of input fields:
                    $('#latitude').val(latitude);
                    $('#longitude').val(longitude);
                }, function(error) {
                    // Handle any errors that occur while getting the location
                    console.error("Error getting location:", error);
                });
            } else {
                // Geolocation is not supported by the browser
                console.error("Geolocation is not supported by this browser.");
            }
        }

        function getlocation_occu() {
            // Check if Geolocation is supported by the browser
            if ("geolocation" in navigator) {
                // Geolocation is supported
                // Use getCurrentPosition method to get the user's current position
                navigator.geolocation.getCurrentPosition(function(position) {
                    // Get latitude and longitude from the position object
                    var latitude = position.coords.latitude;
                    var longitude = position.coords.longitude;

                    // Log the latitude and longitude to the console (you can do further processing here)
                    console.log("Latitude:", latitude);
                    console.log("Longitude:", longitude);

                    // If you want to do something with the latitude and longitude, you can call a function here
                    // For example, set the values of input fields:
                    $('#occu_latitude').val(latitude);
                    $('#occu_longitude').val(longitude);
                }, function(error) {
                    // Handle any errors that occur while getting the location
                    console.error("Error getting location:", error);
                });
            } else {
                // Geolocation is not supported by the browser
                console.error("Geolocation is not supported by this browser.");
            }
        }

        $(document).ready(function () {
            $('#addBankBtn').off('click').on('click', function () {
                // --- Bank name / code from Select2 ---
                const $bank = $('#bank_name');
                const bankDataArr = ($bank.data('select2') && $bank.select2('data')) ? $bank.select2('data') : [];
                const bankText = bankDataArr[0]?.text || $bank.val() || '';       // e.g. "Commercial Bank (7056)"
                const bankCode = $('#bank_code').val() || bankDataArr[0]?.id || '';// e.g. "7056"
                const bankName = String(bankText).replace(/\s*\(\d{3,4}\)\s*$/, '').trim(); // => "Commercial Bank"

                // --- Other fields (as you already had) ---
                const accountName   = $('#account_name').val();
                const accountNumber = $('#account_number').val();

                // Branch: you want the code in the table
                const branchCode = $('#branch').val(); // select2 value is the code

                // Basic validation
                if (!bankName || !accountName || !accountNumber || !branchCode || !bankCode) {
                    Swal.fire("Error!", "All fields are required!", "error");
                    return;
                }

                // Duplicate check (compares what’s shown in the table)
                let isDuplicate = false;
                $('#bank_table tbody tr').each(function () {
                    const rowBankName     = $(this).find('td').eq(0).text();
                    const rowAccountName  = $(this).find('td').eq(1).text();
                    const rowAccountNo    = $(this).find('td').eq(2).text();
                    const rowBranchCode   = $(this).find('td').eq(3).text();
                    const rowBankCode     = $(this).find('td').eq(4).text();
                    if (
                        rowBankName    === bankName &&
                        rowAccountName === accountName &&
                        rowAccountNo   === accountNumber &&
                        rowBranchCode  === branchCode &&
                        rowBankCode    === bankCode
                    ) {
                        isDuplicate = true;
                        return false;
                    }
                });
                if (isDuplicate) {
                    Swal.fire("Error!", "This bank account already exists in the table.", "error");
                    return;
                }

                // Add row — first column now shows the BANK NAME (not the code)
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

                // Clear inputs & select2s
                $('#account_name').val('');
                $('#account_number').val('');
                $('#bank_code').val('');
                $('#branch').val(null).trigger('change'); // select2 reset
                $('#bank_name').val(null).trigger('change'); // select2 reset
            });

            // keep your existing remove handler
            $('#bank_table').on('click', '.remove-btn', function () {
                $(this).closest('tr').remove();
            });
        });


        // Load document types from settings
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

                    // Try to get document types from settings
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

                    // Populate dropdown
                    const dropdown = $('#otherDocDescription');
                    dropdown.empty();
                    dropdown.append('<option value="">Select Document Type</option>');

                    documentTypes.forEach(function(type) {
                        dropdown.append(`<option value="${type}">${type}</option>`);
                    });
                },
                error: function (xhr) {
                    console.error('Error loading document types:', xhr.responseText || xhr.statusText);
                    // Load default types on error
                    const dropdown = $('#otherDocDescription');
                    dropdown.empty();
                    dropdown.append('<option value="">Select Document Type</option>');

                    getDefaultDocumentTypes().forEach(function(type) {
                        dropdown.append(`<option value="${type}">${type}</option>`);
                    });
                }
            });
        }

        // Get default document types (same as in Settings page)
        function getDefaultDocumentTypes() {
            return [
                'NIC Copy',
                'Income Certificate'
            ];
        }

    </script>
@endsection