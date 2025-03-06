@extends('layout.admin')

@section('head')
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .profile-card {
            margin: auto;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        .profile-card .card-header {
            background: #007bff;
            color: white;
            text-align: center;
        }
        .profile-card .profile-image {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: -50px;
        }
        .profile-card .profile-image img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 5px solid white;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #3d5977;
        }
        .btn-primary {
            background-color: #87acd3;
            border: none;
        }
        .btn-primary:hover {
            background-color: #7da3cc;
        }
    </style>
@endsection

@section('content')
    <div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card profile-card">
                            <div class="card-header">
                                <h3>Company Profile</h3>
                            </div>
                            <div class="card-body">
                                <div class="profile-image">
                                    <img src="{{ isset($company) && $company->logo ? asset('storage/' . $company->logo) : 'https://via.placeholder.com/100' }}"
                                         id="profileImagePreview"
                                         alt="Profile Image">

                                </div>

                                <form method="POST" action="#" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="company_name" class="form-label">Company Name</label>
                                        <input type="text" class="form-control" id="company_name" name="company_name" value="{{$company->company_name}}" placeholder="Enter company name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="address" name="address" value="{{$company->address}}" placeholder="Enter company address">
                                    </div>
                                    <div class="mb-3">
                                        <label for="con" class="form-label">Contact Number</label>
                                        <input type="text" class="form-control" id="con" name="con" value="{{$company->contact_no}}" placeholder="Enter company contact number">
                                    </div>
                                    <div class="mb-3">
                                        <label for="profile_image" class="form-label">Company Logo</label>
                                        <input type="file" class="form-control" id="profile_image" name="profile_image" onchange="previewImage(event)">
                                    </div>
                                    <div class="mb-3">
                                        <label for="profile_image" class="form-label">Company Header</label>
                                        <input type="file" class="form-control" id="company_header" name="profile_image">
                                    </div>
                                    <div class="mb-3">
                                        <label for="profile_image" class="form-label">Company Footer</label>
                                        <input type="file" class="form-control" id="company_footer" name="profile_image">
                                    </div>
                                    <div class="mb-3">
                                        <label for="con" class="form-label">Branch Code</label>
                                        <input type="text" class="form-control" id="branch" name="branch" value="{{$company->branch}}" placeholder="Enter Branch Code">
                                    </div>
                                    <div class="row">
                                        <div class="card border-secondary border">
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label" style="color: red">Customer Number Format</label>
                                                        <select class="form-control" id="customer_format_selection" onchange="check_customer_format(this.value)">
                                                            <option value="Customize" {{ $company->customer_num_type === 'Customize' ? 'selected' : '' }}>Customize</option>
                                                            <option value="Format" {{ $company->customer_num_type === 'Format' ? 'selected' : '' }}>Format</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div id="format_section_customer">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="card border-secondary border">
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label" style="color: red">Loan Number Format</label>
                                                        <select class="form-control" id="loan_format_selection" onchange="check_loan_format(this.value)">
                                                            <option value="Customize" {{ $company->loan_num_type === 'Customize' ? 'selected' : '' }}>Auto</option>
                                                            <option value="Format" {{ $company->loan_num_type === 'Format' ? 'selected' : '' }}>Format</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div id="format_section_loan">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="card border-secondary border">
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label" style="color: red">Inv Loan Number Format</label>
                                                        <select class="form-control" id="inv_loan_format_selection" onchange="check_inv_loan_format(this.value)">
                                                            <option value="Customize" {{ $company->inv_loan_num_type === 'Customize' ? 'selected' : '' }}>Auto</option>
                                                            <option value="Format" {{ $company->loan_num_type === 'Format' ? 'selected' : '' }}>Format</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div id="format_section_inv_loan">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="card border-secondary border">
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label" style="color: red">Saving Account Format</label>
                                                        <select class="form-control" id="saving_selection" onchange="check_saving_format(this.value)">
                                                            <option value="Format" {{ $company->account_saving_type === 'Format' ? 'selected' : '' }}>Format</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div id="format_section_saving">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="activatePoints" name="activatePoints"
                                               @if($company->points == "1") checked @endif>
                                        <label class="form-check-label" for="activatePoints">Activate Points</label>
                                    </div>

                                    <div class="mb-3">
                                        <label for="pointsPercentage" class="form-label">Points Percentage (%)</label>
                                        <input type="text" class="form-control" id="pointsPercentage" name="pointsPercentage"
                                               value="{{ $company->points == '1' ? $company->points_percentage : '0' }}"
                                               placeholder="Enter points percentage" @if($company->points != '1') disabled @endif>
                                    </div>

                                    <button type="button" class="btn btn-primary w-100" onclick="validateSubmitProfile(event)">Update Company Profile</button>
                                </form>
                            </div>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- jQuery and Bootstrap 5 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="../JS/profile.js"></script>
    <!-- Custom JS -->
    <script>
        $(document).ready(function() {
            $("#format_section_customer").hide();
            $("#format_section_loan").hide();
            $("#format_section_inv_loan").hide();
            $("#format_section_saving").hide();
            // Trigger the onchange event when the page loads
            $("#customer_format_selection").trigger('change');
            $("#loan_format_selection").trigger('change');
            $("#saving_selection").trigger('change');
            $("#inv_loan_format_selection").trigger('change');
        });

        document.getElementById('activatePoints').addEventListener('change', function() {
            var pointsPercentageInput = document.getElementById('pointsPercentage');
            if (this.checked) {
                pointsPercentageInput.value = '';  // Clear the input field when deactivating
                pointsPercentageInput.disabled = false;
            } else {
                pointsPercentageInput.value = '0';  // Clear the input field when deactivating
                pointsPercentageInput.disabled = true;
            }
        });

        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('profileImagePreview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function check_customer_format(value) {
            const formatSection = $('#format_section_customer');
            if (value === "Format") {
                formatSection.html(`
                    <div class="col-md-6">
                        <label for="separate_from" class="form-label">Separate From</label>
                        <select class="form-control" id="separate_from">
                            <option value="-" {{ $company->customer_seperate_from === "-" ? "selected" : "" }}>-</option>
                            <option value="/" {{ $company->customer_seperate_from === "/" ? "selected" : "" }}>/</option>
<option value="." {{ $company->customer_seperate_from === "." ? "selected" : "" }}>.</option>
                        </select>
                    </div>
                    <br>
                    <div class="row mb-3">
                        <div class="col">
                            <button type="button" class="btn btn-primary" onclick="addToField('@Branch_No@','Branch_No')">Branch Number</button>
                            <button type="button" class="btn btn-primary" onclick="addToField('@Root@','Root')">Root Name</button>
                            <button type="button" class="btn btn-primary" onclick="addToField('@Center_No@','Center_No')">Center Number</button>
                            <button type="button" class="btn btn-primary" onclick="addToField('@Group_No@','Group_No')">Group Number</button>
                            <button type="button" class="btn btn-primary" onclick="addToField('@Customize_No@','Customize_No')">Customize Number</button>
                            <button type="button" class="btn btn-primary" onclick="addToField('@Auto_Id@','Auto_Id')">Auto Create Number</button>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <input type="text" class="form-control" id="field_output_customer" value="{{$company->customer_format}}" hidden>
                            <input type="text" class="form-control" id="field_output_customer_2" value="{{ str_replace('@', '', $company->customer_format) }}" readonly>
                            <br>
                            <button type="button" class="btn btn-danger" onclick="clear_feild('Customer')">Clear Format</button>
                        </div>
                    </div>
<div class="row mb-3">
                        <div class="col">
<label for="auto_number" class="form-label">Auto Generate Number Start From</label>
                            <input type="text" class="form-control" id="auto_number" value="{{ $company->customer_num_start_from }}">
                        </div>
                    </div>
                `);
                formatSection.slideDown();
            } else {
                formatSection.slideUp(() => formatSection.html(''));
            }
        }

        function addToField(value, new_value) {
            const separateFrom = $("#separate_from").val();
            const field = document.getElementById('field_output_customer');
            const field_output_customer_2 = document.getElementById('field_output_customer_2');
            const fieldValue_check = field.value;


            // Check if the value is already in the field
            if (!fieldValue_check.includes(value)) {
                if (field.value) {
                    field.value += separateFrom + value;
                } else {
                    field.value = value;
                }
            }

            // Check if the new_value is already in the field_output_customer_2
            if (!fieldValue_check.includes(new_value)) {
                if (field_output_customer_2.value) {
                    field_output_customer_2.value += separateFrom + new_value;
                } else {
                    field_output_customer_2.value = new_value;
                }
            }
        }

        function clear_feild(value){
            if(value==="Customer"){
                const field = document.getElementById('field_output_customer');
                const field_output_customer_2 = document.getElementById('field_output_customer_2');
                field.value = "";
                field_output_customer_2.value = "";
            }else if(value==="Saving"){
                const field = document.getElementById('field_output_saving');
                const field_output_customer_2 = document.getElementById('field_output_saving_2');
                field.value = "";
                field_output_customer_2.value = "";
            }else if(value==="Inv"){
                const field = document.getElementById('field_output_inv_loan');
                const field_output_customer_2 = document.getElementById('field_output_inv_loan_2');
                field.value = "";
                field_output_customer_2.value = "";
            }else{
                const field = document.getElementById('field_output_loan');
                const field_output_loan_2 = document.getElementById('field_output_loan_2');
                field.value = "";
                field_output_loan_2.value = "";
            }
        }




        function check_loan_format(value) {
            const formatSection = $('#format_section_loan');
            if (value === "Format") {
                formatSection.html(`
                    <div class="col-md-6">
                        <label for="separate_from" class="form-label">Separate From</label>
                        <select class="form-control" id="separate_from_loan">
                            <option value="-" {{ $company->loan_seperate_from === "-" ? "selected" : "" }}>-</option>
                            <option value="/" {{ $company->loan_seperate_from === "/" ? "selected" : "" }}>/</option>
<option value="." {{ $company->loan_seperate_from === "." ? "selected" : "" }}>.</option>
                        </select>
                    </div>
                    <br>
                    <div class="row mb-3">
                        <div class="col">
                            <button type="button" class="btn btn-primary" onclick="addToField_Loan('@Branch_No@','Branch_No')">Branch Number</button>
                            <button type="button" class="btn btn-primary" onclick="addToField_Loan('@Root@','Root')">Root Name</button>
                            <button type="button" class="btn btn-primary" onclick="addToField_Loan('@Center_No@','Center_No')">Center Number</button>
                            <button type="button" class="btn btn-primary" onclick="addToField_Loan('@Group_No@','Group_No')">Group Number</button>
                            <button type="button" class="btn btn-primary" onclick="addToField_Loan('@Product_Code@','Product_Code')">Product Code</button>
                            <button type="button" class="btn btn-primary" onclick="addToField_Loan('@Customer_No@','Customer_No')">Customer Number</button>
                            <button type="button" class="btn btn-primary" onclick="addToField_Loan('@Loan_Count@','Loan_Count')">Customer Loan Count</button>

                            <button type="button" class="btn btn-primary" onclick="addToField_Loan('@Auto_Id@','Auto_Id')">Auto Create Number</button>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <input type="text" class="form-control" id="field_output_loan" value="{{$company->loan_format}}" hidden>
                            <input type="text" class="form-control" id="field_output_loan_2" value="{{str_replace('@', '', $company->loan_format)}}" readonly>
                            <br>
                            <button type="button" class="btn btn-danger" onclick="clear_feild('Loan')">Clear Format</button>
                        </div>
                    </div>
                `);
                formatSection.slideDown();
            } else {
                formatSection.slideUp(() => formatSection.html(''));
            }
        }

        // <button type="button" class="btn btn-primary" onclick="addToField_Loan('@Loan_Center_Number@','Loan_Center_Number')">Loan Number Of The Center</button>
        function addToField_Loan(value,new_value) {
            const separateFrom = $("#separate_from_loan").val();
            const field = document.getElementById('field_output_loan');
            const field_output_loan_2 = document.getElementById('field_output_loan_2');
            const fieldValue = field.value.split(separateFrom);
            const fieldValue_2 = field_output_loan_2.value.split(separateFrom);

            // Check if the value is already in the field
            if (!fieldValue.includes(value)) {
                if (field.value) {
                    field.value += separateFrom + value;
                } else {
                    field.value = value;
                }
            }

            // Check if the value is already in the field
            if (!fieldValue_2.includes(new_value)) {
                if (field_output_loan_2.value) {
                    field_output_loan_2.value += separateFrom + new_value;
                } else {
                    field_output_loan_2.value = new_value;
                }
            }
        }


        function check_inv_loan_format(value) {
            const formatSection = $('#format_section_inv_loan');
            if (value === "Format") {
                formatSection.html(`
                    <div class="col-md-6">
                        <label for="separate_from" class="form-label">Separate From</label>
                        <select class="form-control" id="separate_from_inv_loan">
                            <option value="-" {{ $company->inv_loan_seperate_from === "-" ? "selected" : "" }}>-</option>
                            <option value="/" {{ $company->inv_loan_seperate_from === "/" ? "selected" : "" }}>/</option>
<option value="." {{ $company->inv_loan_seperate_from === "." ? "selected" : "" }}>.</option>
                        </select>
                    </div>
                    <br>
                    <div class="row mb-3">
                        <div class="col">
                            <button type="button" class="btn btn-primary" onclick="addToField_Inv_Loan('@Branch_No@','Branch_No')">Branch Number</button>
                            <button type="button" class="btn btn-primary" onclick="addToField_Inv_Loan('@Root@','Root')">Root Name</button>
                            <button type="button" class="btn btn-primary" onclick="addToField_Inv_Loan('@Product_Code@','Product_Code')">Product Code</button>
                            <button type="button" class="btn btn-primary" onclick="addToField_Inv_Loan('@Customer_No@','Customer_No')">Customer Number</button>
                            <button type="button" class="btn btn-primary" onclick="addToField_Inv_Loan('@Loan_Count@','Loan_Count')">Customer Loan Count</button>
                            <button type="button" class="btn btn-primary" onclick="addToField_Inv_Loan('@Auto_Id@','Auto_Id')">Auto Create Number</button>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <input type="text" class="form-control" id="field_output_inv_loan" value="{{$company->inv_loan_format}}" hidden>
                            <input type="text" class="form-control" id="field_output_inv_loan_2" value="{{str_replace('@', '', $company->inv_loan_format)}}" readonly>
                            <br>
                            <button type="button" class="btn btn-danger" onclick="clear_feild('Inv')">Clear Format</button>
                        </div>
                    </div>
                `);
                formatSection.slideDown();
            } else {
                formatSection.slideUp(() => formatSection.html(''));
            }
        }




        // <button type="button" class="btn btn-primary" onclick="addToField_Loan('@Loan_Center_Number@','Loan_Center_Number')">Loan Number Of The Center</button>
        function addToField_Inv_Loan(value,new_value) {
            const separateFrom = $("#separate_from_inv_loan").val();
            const field = document.getElementById('field_output_inv_loan');
            const field_output_loan_2 = document.getElementById('field_output_inv_loan_2');
            const fieldValue = field.value.split(separateFrom);
            const fieldValue_2 = field_output_loan_2.value.split(separateFrom);

            // Check if the value is already in the field
            if (!fieldValue.includes(value)) {
                if (field.value) {
                    field.value += separateFrom + value;
                } else {
                    field.value = value;
                }
            }

            // Check if the value is already in the field
            if (!fieldValue_2.includes(new_value)) {
                if (field_output_loan_2.value) {
                    field_output_loan_2.value += separateFrom + new_value;
                } else {
                    field_output_loan_2.value = new_value;
                }
            }
        }




        function check_saving_format(value) {
            const formatSection = $('#format_section_saving');
            if (value === "Format") {
                formatSection.html(`
                    <div class="col-md-6">
                        <label for="separate_from" class="form-label">Separate From</label>
                        <select class="form-control" id="separate_from_savings">
                            <option value="-" {{ $company->account_saving_type === "-" ? "selected" : "" }}>-</option>
                            <option value="/" {{ $company->account_saving_type === "/" ? "selected" : "" }}>/</option>
<option value="." {{ $company->account_saving_type === "." ? "selected" : "" }}>.</option>
                        </select>
                    </div>
                    <br>
                    <div class="row mb-3">
                        <div class="col">
                            <button type="button" class="btn btn-primary" onclick="addToField_Saving('@Branch_No@','Branch_No')">Branch Number</button>
                            <button type="button" class="btn btn-primary" onclick="addToField_Saving('@Root@','Root')">Root Name</button>
                            <button type="button" class="btn btn-primary" onclick="addToField_Saving('@Center_No@','Center_No')">Center Number</button>
                            <button type="button" class="btn btn-primary" onclick="addToField_Saving('@Group_No@','Group_No')">Group Number</button>
                            <button type="button" class="btn btn-primary" onclick="addToField_Saving('@Customer_No@','Customer_No')">Customer Number</button>
                            <button type="button" class="btn btn-primary" onclick="addToField_Saving('@Auto_Id@','Auto_Id')">Auto Create Number</button>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <input type="text" class="form-control" id="field_output_saving" value="{{$company->saving_format}}" hidden>
                            <input type="text" class="form-control" id="field_output_saving_2" value="{{str_replace('@', '', $company->saving_format)}}" readonly>
                            <br>
                            <button type="button" class="btn btn-danger" onclick="clear_feild('Saving')">Clear Format</button>
                        </div>
                    </div>
                `);
                formatSection.slideDown();
            } else {
                formatSection.slideUp(() => formatSection.html(''));
            }
        }
        // <button type="button" class="btn btn-primary" onclick="addToField_Loan('@Loan_Center_Number@','Loan_Center_Number')">Loan Number Of The Center</button>
        function addToField_Saving(value,new_value) {
            const separateFrom = $("#separate_from_savings").val();
            const field = document.getElementById('field_output_saving');
            const field_output_loan_2 = document.getElementById('field_output_saving_2');
            const fieldValue = field.value.split(separateFrom);
            const fieldValue_2 = field_output_loan_2.value.split(separateFrom);

            // Check if the value is already in the field
            if (!fieldValue.includes(value)) {
                if (field.value) {
                    field.value += separateFrom + value;
                } else {
                    field.value = value;
                }
            }

            // Check if the value is already in the field
            if (!fieldValue_2.includes(new_value)) {
                if (field_output_loan_2.value) {
                    field_output_loan_2.value += separateFrom + new_value;
                } else {
                    field_output_loan_2.value = new_value;
                }
            }
        }



    </script>
@endsection
