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
                                        <label for="state" class="form-label">Province / State</label>
                                        <select id="state" name="state" class="form-control">
                                            <option value="">Select Province</option>
                                            {{-- Options via AJAX (Select2) --}}
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="city" class="form-label">City / Town</label>
                                        <select id="city" name="city" class="form-control">
                                            <option value="">Select City / Town</option>
                                            {{-- Options via AJAX (Select2 based on province) --}}
                                        </select>
                                    </div>


                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Landline Phone</label>
                                        <input type="tel" id="landline" name="landline" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Guarantee Photo</label>
                                        <input type="file" id="cus_phto" name="cus_phto" class="form-control">
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script src="assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="assets/vendor/daterangepicker/daterangepicker.js"></script>
    {{-- If dashboard.js gives ApexCharts error and you don't need charts here, comment this --}}
    {{-- <script src="assets/js/pages/dashboard.js"></script> --}}

    <script src="../JS/validate.js"></script>
    <script src="../JS/group.js"></script>
    <script src="../JS/guardian.js"></script>

    <script>
        $(document).ready(function() {

            // ================== PROVINCE (STATE) – SL LOCATIONS ==================
            $('#state').select2({
                placeholder: "Select Province",
                allowClear: true,
                ajax: {
                    url: '/sl-locations/provinces',
                    dataType: 'json',
                    delay: 200,
                    processResults: function (data) {
                        // expecting: { provinces: [ {id,text}, ... ] }
                        return {
                            results: data.provinces || []
                        };
                    }
                },
                width: '100%'
            });

            // ================== CITY – DEPENDS ON PROVINCE ==================
            $('#city').select2({
                placeholder: "Select City",
                allowClear: true,
                ajax: {
                    url: '/sl-locations/cities',
                    dataType: 'json',
                    delay: 200,
                    data: function (params) {
                        return {
                            province_id: $('#state').val(),   // match your controller param
                            q: params.term || ''
                        };
                    },
                    processResults: function (data) {
                        // expecting: { cities: [ {id,text}, ... ] }
                        return {
                            results: data.cities || []
                        };
                    }
                },
                width: '100%'
            });

            // When province changes, reset city
            $('#state').on('change', function () {
                $('#city').val(null).trigger('change');
            });

            // ================== NIC → DOB + GENDER ==================
            $('#nic').on('input', function() {
                let nic = $('#nic').val().trim();
                let birthdayInfo = getBirthdayFromNIC(nic);

                if (birthdayInfo) {
                    $('#dob').val(birthdayInfo.year + '-' + birthdayInfo.month + '-' + birthdayInfo.day);
                    $('#gender').val(birthdayInfo.gender);
                } else {
                    $('#gender').val("-");
                    $('#dob').val('Invalid NIC number.');
                }
            });

            // ================== REQUIRED DOCUMENT TABLE ==================
            $('#addDocBtn').on('click', function() {
                var description = $('#otherDocDescription').val().trim();

                if (description === '') {
                    Swal.fire("Error!", "Description cannot be empty !", "error");
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
                        <td><button type="button" class="btn btn-danger removeDocBtn">Remove</button></td>
                    </tr>
                `;

                $('#documenttable tbody').append(newRow);
                $('#otherDocDescription').val('');
            });

            $('#documenttable').on('click', '.removeDocBtn', function() {
                $(this).closest('tr').remove();
            });
        });

        // ================== NIC PARSER (OLD + NEW) ==================
        function getBirthdayFromNIC(nic) {
            let year, days;

            nic = (nic || '').toString().trim().toUpperCase();
            if (!nic) return null;

            if (nic.length === 10) {
                // Old NIC format
                if (!/^\d{9}[VX]$/.test(nic)) return null;
                year = '19' + nic.substr(0, 2);
                days = parseInt(nic.substr(2, 3), 10);
            } else if (nic.length === 12) {
                // New NIC format
                if (!/^\d{12}$/.test(nic)) return null;
                year = nic.substr(0, 4);
                days = parseInt(nic.substr(4, 3), 10);
            } else {
                return null;
            }

            let gender = 'Male';
            if (days > 500) {
                gender = 'Female';
                days -= 500;
            }

            if (days < 1 || days > 366) return null;

            let date = new Date(year, 0, days);
            let month = date.getMonth() + 1;
            let day = date.getDate();

            return {
                year: year,
                month: month < 10 ? '0' + month : month,
                day: day < 10 ? '0' + day : day,
                gender: gender
            };
        }

        // ================== GEO LOCATION ==================
        function getlocation() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var latitude = position.coords.latitude;
                    var longitude = position.coords.longitude;

                    $('#latitude').val(latitude);
                    $('#longitude').val(longitude);
                }, function(error) {
                    console.error("Error getting location:", error);
                });
            } else {
                console.error("Geolocation is not supported by this browser.");
            }
        }
    </script>
@endsection

