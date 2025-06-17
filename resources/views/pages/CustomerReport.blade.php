@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
    <!-- DataTable CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <style>
        .style-tr>td {
            padding: 2px 15px;
        }
    </style>

    <style>
        .style-tr > td {
            padding: 2px 15px;
        }

        .card-body {
            padding: 1.5rem;
        }
        .card {
            border-radius: 0.5rem;
        }
        thead {
            background-color: #d9edf7; /* Light blue color */
            color: #31708f; /* Darker blue text for contrast */
        }

        .bg-purple th {
            color: #e1e1e1 !important; /* Ensure white text color for th elements */
        }

        .bg-purple {
            background-color: #1A2942 !important; /* Purple color */
            color: white !important; /* White text color */
        }

    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Customer Details Report Overview</h4>
            </div>
        </div>
        <span style="color: #a19595">"The Customer Details Report provides a comprehensive overview of customer information, allowing you to track and manage client data efficiently. This report includes key personal details, contact information, and status, which can be used for customer service, communication, and internal record-keeping."</span>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- DataTable -->
                    <table id="customerTable" class="display nowrap table table-striped table-bordered" style="width:100%">
                        <thead  class="sticky-top bg-purple">
                        <tr>
                            <th>Member Number</th>
                            <th>Center</th>
                            <th>Group Name</th>
                            <th>Title</th>
                            <th>Customer Name</th>
                            <th>Civil Status</th>
                            <th>Nic</th>
                            <th>Gender</th>
                            <th>DOB</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Email</th>
                            <th>Current Address</th>
                            <th>Permanent Address</th>
                            <th>Contact Number</th>
                            <th>Contact Number 02</th>
                            <th>Landline</th>
                            <th>Guardian Title</th>
                            <th>Guardian Name</th>
                            <th>Guardian Gender</th>
                            <th>Relation</th>
                            <th>Occupation</th>
                            <th>Guardian Contact</th>
                            <th>Guardian NIC</th>
                            <th>Guardian Address</th>
                            <th>Current Loan Count</th>
                            <th>Settled Loan Count</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td>{{ $customer->cus_number }}</td>
                                <td>{{ $customer->center_name ?? '-' }}</td>
                                <td>{{ $customer->group_name ?? '-' }}</td>
                                <td>{{ $customer->Title }}</td>
                                <td>{{ $customer->First_Name }} {{ $customer->Last_Name }}</td>
                                <td>{{ $customer->civil_status }}</td>
                                <td>{{ $customer->Nic }}</td>
                                <td>{{ $customer->Gender }}</td>
                                <td>{{ $customer->Dob }}</td>
                                <td>{{ $customer->City }}</td>
                                <td>{{ $customer->State }}</td>
                                <td>{{ $customer->Email }}</td>
                                <td>{{ $customer->Address }},{{ $customer->Address_02 }},{{ $customer->Address_03 }}</td>
                                <td>{{ $customer->Per_Address_01 }},{{ $customer->Per_Address_02 }},{{ $customer->Per_Address_03 }}</td>
                                <td>{{ $customer->Contact_No }}</td>
                                <td>{{ $customer->contact_number_2 }}</td>
                                <td>{{ $customer->Landline }}</td>
                                <td>{{ $customer->Gua_title }}</td>
                                <td>{{ $customer->Gua_name }}</td>
                                <td>{{ $customer->Guardian_gender }}</td>
                                <td>{{ $customer->Gua_relation }}</td>
                                <td>{{ $customer->Gua_occu }}</td>
                                <td>{{ $customer->Gua_contact }}</td>
                                <td>{{ $customer->Gua_nic }}</td>
                                <td>{{ $customer->Gua_address }}</td>
                                <td>{{ $customer->current_loan_count }}</td>
                                <td>{{ $customer->settled_loan_count }}</td>
                            @if($customer->Status == "1")
                                    <td class="text-center"><span class="badge bg-primary">Active</span></td>
                                @else
                                    <td class="text-center"><span class="badge bg-danger">Inactive</span></td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div> <!-- end row -->










@endsection

@section('script')
    <script src="../assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="../assets/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="../assets/js/pages/dashboard.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/group.js"></script>
    <script src="../JS/customer.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 Elements
            $('.select2').select2();


            // Replace special characters in the company name
            var companyName = {!! json_encode(session('company_name')) !!}.replace(/&/g, ' And ');

            // Initialize DataTable
            $('#customerTable').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="bi bi-clipboard"></i> Copy',
                        className: 'btn btn-secondary',
                        filename: companyName
                    },
                    {
                        extend: 'csv',
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV',
                        className: 'btn btn-success',
                        filename: companyName
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                        className: 'btn btn-primary',
                        filename: companyName
                    }
                ]
            });
            $(document).on('click', '.edit-btn', function() {

                const editButton = document.querySelector('.edit-btn');
                // Retrieve the data attributes from the button
                const firstName = editButton.getAttribute('data-first-name');
                const lastName = editButton.getAttribute('data-last-name');
                const title = editButton.getAttribute('data-title');
                const email = editButton.getAttribute('data-email');
                const contactNo = editButton.getAttribute('data-contact-no');
                const nic = editButton.getAttribute('data-nic');
                const gender = editButton.getAttribute('data-gender');
                const dob = editButton.getAttribute('data-dob');
                const address = editButton.getAttribute('data-address');
                const city = editButton.getAttribute('data-city');
                const state = editButton.getAttribute('data-state');
                const landline = editButton.getAttribute('data-landline');
                const guaTitle = editButton.getAttribute('data-gua_title');
                const guaName = editButton.getAttribute('data-gua_name');
                const guardianGender = editButton.getAttribute('data-guardian_gender');
                const guaRelation = editButton.getAttribute('data-gua_relation');
                const guaOccu = editButton.getAttribute('data-gua_occu');
                const guaContact = editButton.getAttribute('data-gua_contact');
                const guaAddress = editButton.getAttribute('data-gua_address');
                const cus_id = editButton.getAttribute('data-customer-id');

                // Set the values of the input fields
                document.getElementById('title').value = title;
                document.getElementById('f_name').value = firstName;
                document.getElementById('last_name').value = lastName;
                document.getElementById('email').value = email;
                document.getElementById('contact_number').value = contactNo;
                document.getElementById('nic').value = nic;
                document.getElementById('gender').value = gender;
                document.getElementById('dob').value = dob;
                document.getElementById('address').value = address;
                document.getElementById('city').value = city;
                document.getElementById('state').value = state;
                document.getElementById('landline').value = landline;
                document.getElementById('gua_title').value = guaTitle;
                document.getElementById('gua_name').value = guaName;
                document.getElementById('guardian_gender').value = guardianGender;
                document.getElementById('gua_relation').value = guaRelation;
                document.getElementById('gua_occu').value = guaOccu;
                document.getElementById('gua_contact').value = guaContact;
                document.getElementById('gua_address').value = guaAddress;
                document.getElementById('cus_id').value = cus_id;
            });
        });


    </script>
    <script>
        function openMap(latitude, longitude) {
            var url = `https://maps.google.com/maps?q=${latitude},${longitude}`;
            window.open(url, '_blank');
        }

        function load_document(id){
            $.ajax({
                type: "GET",
                url: "/customerdoc/"+id,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {
                    var tbody = $('#document_table tbody');
                    tbody.empty(); // Clear existing rows

                    data.item.forEach(function(item) {
                        var idCustomer_Documents = item.idCustomer_Documents;
                        var description = item.Description;
                        var path = item.Path;

                        // Construct the table row
                        var row = `
            <tr>
                <td>${description}</td>
                <td>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-success" onclick="openDocument('${path}')"><i class="bi bi-eye"></i></button>
                        <button type="button" class="btn btn-danger" onclick="delete_doc(${idCustomer_Documents})">
    <i class="bi bi-trash"></i> <!-- Replace bi-bucket with bi-trash for trash bucket icon -->
</button>
                    </div>
                </td>
            </tr>
        `;

                        // Append the row to the table body
                        tbody.append(row);
                    });
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });
        }



        function openDocument(path) {
            path = "/storage/" + path;
            window.open(path, '_blank');
        }


        function delete_doc(id){
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to delete this Document ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "GET",
                        url: "/cusdocument/delete/"+id,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function (data, textStatus, xhr) {
                            if (xhr.status === 200) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Successfully deleted !",
                                }).then(function () {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire("Error!", "Failed to save data!", "error");
                            }
                        },
                    });
                }
            });
        }


        function update_cus() {
            // Get data from modal fields
            var id = $('#cus_id').val();
            var title = $('#title').val();
            var f_name = $('#f_name').val();
            var last_name = $('#last_name').val();
            var email = $('#email').val();
            var contact_number = $('#contact_number').val();
            var nic = $('#nic').val();
            var gender = $('#gender').val();
            var dob = $('#dob').val();
            var address = $('#address').val();
            var city = $('#city').val();
            var state = $('#state').val();
            var landline = $('#landline').val();
            var gua_title = $('#gua_title').val();
            var gua_name = $('#gua_name').val();
            var guardian_gender = $('#guardian_gender').val();
            var gua_relation = $('#gua_relation').val();
            var gua_occu = $('#gua_occu').val();
            var gua_contact = $('#gua_contact').val();
            var gua_address = $('#gua_address').val();
            var note = $('#u_note').val();
            var longitude = $('#longitude').val();
            var latitude = $('#latitude').val();



            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to update this Customer?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Update it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send AJAX request
                    $.ajax({
                        url: '/update-customer',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id: id,
                            title: title,
                            f_name: f_name,
                            last_name: last_name,
                            email: email,
                            contact_number: contact_number,
                            nic: nic,
                            gender: gender,
                            dob: dob,
                            address: address,
                            city: city,
                            state: state,
                            landline: landline,
                            gua_title: gua_title,
                            gua_name: gua_name,
                            guardian_gender: guardian_gender,
                            gua_relation: gua_relation,
                            gua_occu: gua_occu,
                            gua_contact: gua_contact,
                            gua_address: gua_address,
                            note: note,
                            longitude: longitude,
                            latitude: latitude,
                        },
                        success: function(response) {
                            console.log(response);
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Successfully updated!",
                            }).then(function () {
                                window.location.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            // Handle error
                            console.error(error);
                        }
                    });
                }
            });
        }


        function saveDocument() {
            var customer = $('#customer').val();
            var description = $('#description').val();
            var file = $('#file')[0].files[0]; // Get the first selected file

            var formData = new FormData();
            formData.append('customer', customer);
            formData.append('description', description);
            formData.append('file', file);

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to save this Document ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Upload it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/savecustomerdocument',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function (data, textStatus, xhr) {
                            if (xhr.status === 200) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Successfully saved!",
                                }).then(function () {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire("Error!", "Failed to save data!", "error");
                            }
                        },
                        error: function(xhr, status, error) {
                            // Handle error response
                            console.error('Error saving document:', error);
                        }
                    });

                }
            });
        }


        function set_cus(id){
            $('#customer').val(id);
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



        function change_status(id){
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to change the Status ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Change it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "GET",
                        url: "/customers/status/"+id,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function (data, textStatus, xhr) {
                            if (xhr.status === 200) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: data.message,
                                }).then(function () {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire("Error!", "Failed to save data!", "error");
                            }
                        },
                    });
                }
            });
        }

    </script>
@endsection
