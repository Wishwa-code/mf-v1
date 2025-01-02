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
                            <h4 class="page-title">Guarantee Details</h4>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="example-select" class="form-label">Title</label>
                                        <select class="form-select" id="title" name="title">
                                            <option>Mr</option>
                                            <option>Mrs</option>
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
                                        <input type="tel" id="contact_number" name="contact_number" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">NIC <span class="required-asterisk">*</span></label>
                                        <input type="text" id="nic" name="nic" class="form-control">
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
{{--                                    <div class="mb-3">--}}
{{--                                        <label for="simpleinput" class="form-label">Address</label>--}}
{{--                                        <input type="text" id="address" name="address" class="form-control">--}}
{{--                                    </div>--}}
                                    <div class="row mb-3">
                                        <label for="occu_address_01" class="form-label">Address</label>
                                        <div class="col-md-4">
                                            <input type="text" id="address" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" id="address_2" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" id="address_3" class="form-control">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Province / State</label>
                                        <select id="state" name="state" class="form-control">
                                            <option value="Western Province" >Western Province</option>
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
                                            $cities = "SELECT * FROM cities";
                                            ?>
                                            @foreach($cities as $item)
                                                <option value="{{$item->name_en}}">{{$item->name_en}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Landline Phone</label>
                                        <input type="tel" id="landline" name="landline" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Guarantee Photo</label>
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

                            <button type="button" class="btn btn-success" style="float: right" onclick="validateSubmitGuardian(event)"><i
                                    class="bi bi-save"></i>&nbsp;&nbsp;Save Guarantee</button>
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
    <script src="../JS/guardian.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {

            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
            $('#state').select2({
                placeholder: "Select Province", // Optional placeholder
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
            let year, days;

            if (nic.length === 10) {
                // Old NIC format
                year = '19' + nic.substr(0, 2);
                days = parseInt(nic.substr(2, 3), 10);
            } else if (nic.length === 12) {
                // New NIC format
                year = nic.substr(0, 4);
                days = parseInt(nic.substr(4, 3), 10);
            } else {
                return null;
            }

            // Determine if the person is male or female
            let gender = 'Male';
            if (days > 500) {
                gender = 'Female';
                days -= 500;
            }

            // Calculate the birthday
            let date = new Date(year, 0, days);
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
    </script>
@endsection
