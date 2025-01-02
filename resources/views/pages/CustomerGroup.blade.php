@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <style>
        .style-tr>td {
            padding: 2px 15px;
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
                            <h4 class="page-title">Assign Groups</h4>
                        </div>

                        <div class="table-responsive-sm border border-1">
                            <table class="table table-centered mb-0" id="customerTable">
                                <thead>
                                <tr>
                                    <th>Center No</th>
                                    <th>Center Name</th>
                                    <th>Group No</th>
{{--                                    <th>Group Name</th>--}}
                                    <th class="text-center">Member Count</th>
                                    <th class="text-center">Total Issued Loans</th>
                                    <th class="text-center">Total Loan Amount</th>
                                    <th style="width:20%" class="text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($group as $item)
                                        <?php
                                        $customerCount = \Illuminate\Support\Facades\DB::table('group_has_customer')
                                            ->where('group_id', '=', $item->idCustomer_Group)->count();

                                        $loanCount = \Illuminate\Support\Facades\DB::table('customer_loan')
                                            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
                                            ->join('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
                                            ->join('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
                                            ->where('group_has_customer.group_id', '=', $item->idCustomer_Group)
                                            ->where('customer_loan.Status', '=', '0')
                                            ->count();

                                        $totalLoanAmount = \Illuminate\Support\Facades\DB::table('customer_loan')
                                            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
                                            ->join('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
                                            ->join('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
                                            ->where('group_has_customer.group_id', '=', $item->idCustomer_Group)
                                            ->where('customer_loan.Status', '=', '0')
                                            ->sum('customer_loan.Total_Loan_Amount');
                                        ?>
                                    <tr class="style-tr">
                                        <td>{{ $item->center_no }}</td>
                                        <td>{{ $item->center_name }}</td>
                                        <td>{{ $item->Group_No }}</td>
{{--                                        <td>{{ $item->Name }}</td>--}}
                                        <td class="text-center">{{ $customerCount }}</td>
                                        <td class="text-center">{{ $loanCount }}</td>
                                        <td class="text-center">{{ number_format($totalLoanAmount, 2) }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                                    style="background-color: white; color: #5691FF; border:none"
                                                    onclick="setGroupIdGroup({{ $item->idCustomer_Group }})"
                                                    data-bs-target="#view-modal"><i
                                                    class="bi bi-people fs-4"></i> </button>
                                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                                    style="background-color: white; color: #5691FF; border:none"
                                                    onclick="load_to_table({{ $item->idCustomer_Group }})"
                                                    data-bs-target="#add-modal"><i
                                                    class="bi bi-eye fs-4"></i> </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive -->
                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div>

    <!-- Modal -->
    <div class="modal fade" id="view-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Assign To Group</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="hidden" id="group_id_input">
                            <label for="customer" class="form-label">Select Customer</label>
                            <select class="form-control" id="customer_id">
                                @foreach($customersNotInGroup as $item)
                                    <option value="{{ $item->idCustomer }}">
                                        ({{ $item->cus_number }})-{{ $item->First_Name }} {{ $item->Last_Name }} - {{ $item->Nic }} - {{ $item->Contact_No }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="assignCustomer()">
                        <i class="bi bi-upload"></i>&nbsp;&nbsp; Assign
                    </button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myCenterModalLabel">View Group Members</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>



                <div class="modal-body">
                    <table class="table table-centered mb-0" id="load_group">
                        <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Contact Number</th>
                            <th class="text-center">Contact Number</th>
                            <th class="text-center">Nic</th>
                            <th style="width:20%" class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')
    <!-- jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

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


            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            // Re-initialize Select2 when modal is shown
            $('#view-modal').on('shown.bs.modal', function () {
                $('#customer_id').select2({
                    dropdownParent: $('#view-modal')
                });
            });


            // Initialize DataTable
            $('#customerTable').DataTable({
                "paging": true, // Enable pagination
                "lengthChange": false, // Disable the ability to change number of records per page
                "searching": true, // Enable search box
                "ordering": true, // Enable sorting
                "info": true, // Enable showing information
                "autoWidth": false, // Disable auto-width
                "responsive": true // Enable responsive mode
            });

            // Function to get location (not related to Select2, but included in original request)
            getlocation();
        });

        function getlocation() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var latitude = position.coords.latitude;
                    var longitude = position.coords.longitude;
                    console.log("Latitude:", latitude);
                    console.log("Longitude:", longitude);
                    $('#latitude').val(latitude);
                    $('#longitude').val(longitude);
                }, function(error) {
                    console.error("Error getting location:", error);
                });
            } else {
                console.error("Geolocation is not supported by this browser.");
            }
        }
        function setGroupIdGroup(id){
            $("#group_id_input").val(id);
        }


        function load_to_table(idCustomerGroup) {
            $.ajax({
                type: "POST",
                url: "/get_customer_details",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    idCustomerGroup: idCustomerGroup
                },
                success: function (response) {
                    var tableBody = $('#load_group tbody');
                    tableBody.empty(); // Clear any existing rows

                    if (response.length > 0) {
                        response.forEach(function (customer) {
                            var row = `<tr>
                        <td>${customer.First_Name} ${customer.Last_Name}</td>
                        <td>${customer.Contact_No}</td>
                        <td class="text-center">${customer.Contact_No}</td>
                        <td class="text-center">${customer.Nic}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger" onclick="removeCustomerFromGroup('${customer.idCustomer}', ${idCustomerGroup})">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </td>
                    </tr>`;
                            tableBody.append(row);
                        });
                    } else {
                        var noDataRow = `<tr><td colspan="5" class="text-center">No customers found</td></tr>`;
                        tableBody.append(noDataRow);
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });
        }


        function removeCustomerFromGroup(customerId, groupId) {


            console.log(customerId,groupId);

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to remove this Customer ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Remove it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "/remove_customer_from_group",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: {
                            customerId: customerId,
                            groupId: groupId
                        },
                        success: function (response) {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Successfully removed !",
                            }).then(function () {
                                window.location.reload();
                            });
                        },
                        error: function (xhr, textStatus, errorThrown) {
                            console.log("Error:", errorThrown);
                        }
                    });
                }
            });


        }





        function assignCustomer(){

            var id = $('#group_id_input').val();
            var customer = $('#customer_id').val();

            console.log(customer);


            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to assign this Customer ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Assign it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send AJAX request
                    $.ajax({
                        url: '/assigngrouomember',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id: id,
                            customer: customer,
                        },
                        success: function(response) {
                            console.log(response);
                            if(response.id==="1"){
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Successfully updated !",
                                }).then(function () {
                                    window.location.reload();
                                });
                            }else{
                                Swal.fire("Error!", "This customer is already in a group !", "error");
                            }

                        },
                        error: function(xhr, status, error) {
                            // Handle error
                            console.error(error);
                        }
                    });

                }
            });

        }

    </script>
@endsection
