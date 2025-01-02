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
        .form-label {
            font-weight: bold;
        }
        .form-control {
            border-radius: 0.25rem;
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


    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h4 class="page-title">Loan Summary Report</h4>
                    </div>

                    <!-- Center Filter Dropdown -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-5 col-sm-6">
                            <form action="{{ route('loan.edit') }}" method="get">
                                @csrf
                                <!-- Center Filter -->
                                <div class="mb-3">
                                    <label for="centerFilter" class="form-label">Filter by Center</label>
                                    <select id="centerFilter" name="center_id" class="form-select">
                                        <option value="">All</option>
                                        @foreach($centers as $center)
                                            <option value="{{ $center->idCenter }}"
                                                    {{ request('center_id') == $center->idCenter ? 'selected' : '' }}>
                                                {{ $center->No }} - {{ $center->Name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Group Filter -->
                                <div class="mb-3">
                                    <label for="groupFilter" class="form-label">Filter by Group</label>
                                    <select id="groupFilter" name="group_name" class="form-select">
                                        <option value="">All Groups</option>
                                        @foreach($groups as $group)
                                            <option value="{{ $group->group_name }}"
                                                    {{ request('group_name') == $group->group_name ? 'selected' : '' }}>
                                                {{ $group->group_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-danger">Search</button>
                            </form>
                        </div>
                    </div>





                    <!-- DataTable -->
                    <table id="customerTable" class="display nowrap table table-striped table-bordered" style="width:100%">
                        <thead class="bg-purple">
                        <tr>
                            <th>No</th>
                            <th>Branch</th>
                            <th>Credit Officer</th>
                            <th>Center</th>
                            <th>Group</th>
                            <th>Customer Name</th>
                            <th>Customer No</th>
                            <th>Customer Nic</th>
                            <th>Contact No</th>
                            <th>Product</th>
                            <th>Loan Number</th>
                            <th>Loan Status</th>
                            <th>Issue Date</th>
                            <th>Loan Amount</th>
                            <th>Agreed Amount</th>
                            <th>Outstanding</th>
                            <th>Loan Stock</th>
                            <th>Portfolio</th>
                            <th>Rental</th>
                            <th>Arrears</th>
                            <th>Total Over Paid</th>
                            <th>Age</th>
                            <th>Period Over Date</th>
                            <th>Period Over Days</th>
                            <th>Last Payment Date</th>
                            <th>Last Payment Amount</th>
                            <th>Last Due Date</th>
                            <th>Next Due Date</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($loan as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->branch_name }}</td>
                                <td>{{ $item->LendingOfficer }}</td>
                                <td>{{ $item->center_no }}</td>
                                <td>{{ $item->group_name }}</td>
                                <td>{{ $item->First_Name }} {{ $item->Last_Name }}</td>
                                <td>{{ $item->cus_number }}</td>
                                <td>{{ $item->Nic }}</td>
                                <td>{{ $item->Contact_No }}</td>
                                <td>{{ $item->loan_name }}</td>
                                <td>{{ $item->Loan_No }}</td>
                                @if($item->Status == "1")
                                    <td class="text-center"><span class="badge bg-primary">Completed</span></td>
                                @elseif($item->Status == "0")
                                    <td class="text-center"><span class="badge bg-danger">Ongoing</span></td>
                                @elseif($item->Status == "-2")
                                    <td class="text-center"><span class="badge bg-warning">Deleted</span></td>
                                @endif
                                <td>{{ $item->Date_Time }}</td>
                                <td>{{ number_format($item->Amount,2) }}</td>
                                <td>{{ number_format($item->Total_Loan_Amount,2) }}</td>
                                <td>{{ number_format($item->Balance_Amount,2) }}</td>
                                <td>{{ number_format($item->capital_balance,2) }}</td>
                                @php
                                    $capital = DB::table('installments')
                                        ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                                        ->where('Installment_Date', '>', date('Y-m-d'))
                                        ->where('branch_id', session('branch_id'))
                                        ->sum('capital_amount');
                                    $arrease = DB::table('installments')
                                        ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                                        ->where('Installment_Date', '<=', date('Y-m-d'))
                                        ->where('branch_id', session('branch_id'))
                                        ->sum('Total_Balance');
                                    $portfolio = $capital + $arrease; // Calculate Portfolio
                                @endphp

                                <td>{{ number_format($portfolio, 2) }}</td>
                                <td>{{ number_format($item->Installment_Amount,2) }}</td>
                                @php
                                    $arrease = DB::table('installments')
                                        ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                                        ->where('Installment_Date', '<', date('Y-m-d'))
                                        ->where('branch_id', session('branch_id'))
                                        ->sum('Total_Balance');
                                @endphp
                                <td>{{ number_format($arrease, 2) }}</td>

                                @php
                                    $overpaid = DB::table('installments')
                                        ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                                        ->where('Installment_Date', '>', date('Y-m-d'))
                                        ->where('branch_id', session('branch_id'))
                                        ->sum('Paid_Amount');
                                @endphp
                                <td>{{ number_format($overpaid, 2) }}</td>
                                @php
                                    $age = date_diff(new DateTime(date('Y-m-d')), new DateTime($item->Date_Time)); // Calculate the age in years
                                $age=$age->days;
                                @endphp
                                <td>{{ $age }} Days</td>
                                @php
                                    $maturityDate = DB::table('installments')
                                        ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                                        ->where('branch_id', session('branch_id'))
                                        ->orderBy('Installment_Date', 'desc')
                                        ->value('Installment_Date'); // Get the last installment date directly
                                @endphp

                                <td>{{ $maturityDate }}</td>
                                @php
                                    $currentDate = date('Y-m-d'); // Today's date
                                    $dateDiff = date_diff(new DateTime($currentDate), new DateTime($maturityDate)); // Calculate the difference
                                    $diffDays = $dateDiff->days; // Get the total number of days
                                    $isPast = $dateDiff->invert; // Check if the maturity date is in the past (1 if true, 0 if false)

                                    // If the date difference is negative, make it negative
                                    $diffDays = $isPast ? -$diffDays : $diffDays;
                                @endphp
                                <td>{{ $diffDays }} Days</td>

                                @php
                                    $lastPayment = DB::table('customer_payments')
                                        ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                                        ->where('branch_id', session('branch_id'))
                                        ->orderBy('Date', 'desc')
                                        ->first(['Date', 'Amount']); // Get the last payment date and amount
                                @endphp

                                <td>{{ $lastPayment->Date ?? 'N/A' }}</td>
                                <td>{{ isset($lastPayment) ? number_format($lastPayment->Amount, 2) : 'N/A' }}</td>

                                @php
                                    $lastDueDate = DB::table('installments')
                                        ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                                        ->where('Installment_Date', '<', date('Y-m-d'))
                                        ->where('branch_id', session('branch_id'))
                                        ->orderBy('Installment_Date', 'desc') // Order by date descending
                                        ->value('Installment_Date'); // Get the last due date
                                    $nextDueDate = DB::table('installments')
                                        ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                                        ->where('branch_id', session('branch_id'))
                                        ->where('Installment_Date', '>', date('Y-m-d'))
                                        ->orderBy('Installment_Date', 'asc') // Order by date descending
                                        ->value('Installment_Date'); // Get the last due date
                                @endphp

                                <td>{{ $lastDueDate ?? 'N/A' }}</td> <!-- Display the last due date or 'N/A' if not found -->
                                <td>{{ $nextDueDate ?? 'N/A' }}</td> <!-- Display the last due date or 'N/A' if not found -->
                                <td><a href="/loanview/{{$item->idCustomer_Loan}}" target="_blank" class="btn btn-warning"><i class="bi bi-eye"></i></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div> <!-- end row -->




    <div class="modal fade" id="view-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h4 class="modal-title" >gwegerg</h4> -->
                    <h4>Customer Document</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive-sm">
                        <table class="table table-centered mb-0" id="document_table">
                            <thead>
                            <tr>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div>
    </div>

    <div class="modal fade" id="standard-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h4 class="modal-title" >gwegerg</h4> -->
                    <h4>Edit Customer</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="cus_id">
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
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">Address</label>
                                <input type="text" id="address" name="address" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">City</label>
                                <input type="text" id="city" name="city" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">Province / State</label>
                                <input type="text" id="state" name="state" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">Landline Phone</label>
                                <input type="tel" id="landline" name="landline" class="form-control">
                            </div>

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
                                                        <label for="simpleinput" class="form-label">Guardian Name</label>
                                                        <input type="text" id="gua_name" name="gua_name" class="form-control">
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
                                                    <div class="mb-3">
                                                        <label for="simpleinput" class="form-label">Address</label>
                                                        <input type="text" id="gua_address" name="gua_address" class="form-control">
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>



                    </div>


                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" onclick="update_cus()">Update changes</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->



    <div class="modal fade" id="standard-modal_2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Add Customer Documents</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="hidden" id="customer">
                        </div>
                        <div class="mb-3">
                            <label for="simpleinput" class="form-label">Description</label>
                            <input type="text" id="description" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="simpleinput" class="form-label">Document</label>
                            <input type="file" id="file" class="form-control">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="saveDocument()"><i
                                        class="bi bi-upload"></i>&nbsp;&nbsp;
                                Upload</button>
                        </div>
                    </div>
                </div>



            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

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

            // Initialize DataTable
            $('#customerTable').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="bi bi-clipboard"></i> Copy',
                        className: 'btn btn-secondary',
                    },
                    {
                        extend: 'csv',
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV',
                        className: 'btn btn-success',
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                        className: 'btn btn-primary',
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                        className: 'btn btn-danger',
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i> Print',
                        className: 'btn btn-info',
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
