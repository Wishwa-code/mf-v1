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
                                                $newnum = str_replace(['@Center_No@', '@Group_No@','@Customize_No@','@Auto_ID@','@Branch_No@','@Root@'], ['C000', 'G000','Customize No',$formatted_customer_id,'@Branch_No@','@Root@'], $company->customer_format);
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
                                        <input type="tel" id="contact_number" name="contact_number" class="form-control"  onkeypress="validateContactNumber(event)">
                                    </div>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Mobile No 02</label>
                                        <input type="tel" id="contact_number_2" name="contact_number_2" class="form-control"  onkeypress="validateContactNumber(event)">
                                    </div>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">NIC <span class="required-asterisk">*</span></label>
                                        <input type="text" id="nic" name="nic" class="form-control">
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
                                        <input type="file" id="cus_phto" name="cus_phto" class="form-control">
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
                                                    <input type="text" id="occu_contact_no" class="form-control">
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
                                                        <label for="otherChargesDescription"
                                                               class="form-label">Description</label>
                                                        <input type="text" class="form-control" id="otherDocDescription">
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
                                                    <label for="branch" class="form-label">Branch</label>
                                                    <input type="text" id="branch" class="form-control">
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
                                                    <th>Branch</th>
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
                                                        <input type="text" id="gua_nic" name="gua_nic" class="form-control">
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
                                                        <input type="text" id="gua_contact" name="gua_contact" class="form-control">
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
        $(document).ready(function() {

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
                let nic = $('#nic').val().trim();
                let birthdayInfo = getBirthdayFromNIC(nic);

                if (birthdayInfo) {
                    $('#dob').val('' + birthdayInfo.year + '-' + birthdayInfo.month + '-' + birthdayInfo.day + '');
                    $('#gender').val(birthdayInfo.gender);
                } else {
                    $('#gender').val("-");
                    $('#dob').val('Invalid NIC number.');
                }
            });

            $('#addDocBtn').on('click', function() {
                var description = $('#otherDocDescription').val().trim();

                if (description === '') {
                    Swal.fire("Error!", "Description cannot be empty !", "error");
                    return;
                }

                var newRow = `
                    <tr>
                        <td>${description}</td>
                        <td><input type="file" class="form-control"></td>
                        <td><button type="button" class="btn btn-danger removeDocBtn">Remove</button></td>
                    </tr>
                `;

                $('#documenttable tbody').append(newRow);

                // Clear the input field after adding
                $('#otherDocDescription').val('');
            });

            // Use event delegation to handle dynamically added remove buttons
            $('#documenttable').on('click', '.removeDocBtn', function() {
                $(this).closest('tr').remove();
            });
        });

        function getBirthdayFromNIC(nic) {
            let year, days, newNic, oldNic;

            // Check if it's an old NIC and convert it to the new NIC format
            if (nic.length === 10) {
                // Old NIC format
                year = '19' + nic.substr(0, 2);  // Extract the year (e.g., '94' -> '1994')
                days = nic.substr(2, 3);  // Extract the days part (e.g., '287')

                // Convert to new NIC format
                let sequencePart = '0' + nic.substr(5, 4); // Add leading '0' for new NIC format
                newNic = year + days + sequencePart;
                $("#new_nic").val(newNic);
                console.log("Converted Old NIC to New NIC format:", newNic);
            } else if (nic.length === 12) {
                // New NIC format
                year = nic.substr(0, 4);
                days = parseInt(nic.substr(4, 3), 10);

                // Convert to Old NIC format
                let oldYear = nic.substr(2, 2);  // Take the last two digits of the year (e.g., '96')
                let sequencePart = nic.substr(8, 4);  // Extract the correct last 4 digits (e.g., '0398')
                oldNic = oldYear + nic.substr(4, 3) + sequencePart + "V"; // Append 'V' at the end
                console.log("Converted New NIC to Old NIC format:", oldNic);
                $("#new_nic").val(oldNic);
            } else {
                return null;
            }

            // Determine if the person is male or female
            let gender = 'Male';
            if (parseInt(days) > 500) {
                gender = 'Female';
                days = parseInt(days) - 500; // Adjust days for female
            }

            // Create a date starting from January 1st of the given year
            let date = new Date(year, 0, 1);
            date.setDate(days);  // Add the number of days

            let month = date.getMonth() + 1; // Months are zero-based in JS
            let day = date.getDate();

            // Return the result
            return {
                year: year,
                month: month < 10 ? '0' + month : month,
                day: day < 10 ? '0' + day : day,
                gender: gender
            };
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

        $(document).ready(function() {
            $('#addBankBtn').click(function() {
                // Get the values from the input fields
                var bankName = $('#bank_name').val();
                var accountName = $('#account_name').val();
                var accountNumber = $('#account_number').val();
                var branch = $('#branch').val();

                // Validate input (optional)
                if (bankName === '' || accountName === '' || accountNumber === '' || branch === '') {
                    Swal.fire("Error!", "All fields are required!", "error");
                    return;
                }

                // Check if the table already contains the same values
                var isDuplicate = false;
                $('#bank_table tbody tr').each(function() {
                    var rowBankName = $(this).find('td').eq(0).text();
                    var rowAccountName = $(this).find('td').eq(1).text();
                    var rowAccountNumber = $(this).find('td').eq(2).text();
                    var rowBranch = $(this).find('td').eq(3).text();

                    if (rowBankName === bankName && rowAccountName === accountName &&
                        rowAccountNumber === accountNumber && rowBranch === branch) {
                        isDuplicate = true;
                        return false; // Break the loop
                    }
                });

                if (isDuplicate) {
                    Swal.fire("Error!", "This bank account already exists in the table.", "error");
                    return;
                }

                // Create a new row with the input values and a remove button
                var newRow = `
            <tr>
                <td>${bankName}</td>
                <td>${accountName}</td>
                <td>${accountNumber}</td>
                <td>${branch}</td>
                <td>
                    <button type="button" class="btn btn-danger remove-btn">Remove</button>
                </td>
            </tr>
        `;

                // Append the new row to the table
                $('#bank_table tbody').append(newRow);

                // Clear the input fields
                $('#bank_name').val('');
                $('#account_name').val('');
                $('#account_number').val('');
                $('#branch').val('');
            });

            // Delegate the click event to the remove buttons
            $('#bank_table').on('click', '.remove-btn', function() {
                $(this).closest('tr').remove();
            });
        });

    </script>
@endsection
