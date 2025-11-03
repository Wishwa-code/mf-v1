@extends('layout.admin')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <style>
        .bg-purple th {
            color: #e1e1e1 !important;
        }

        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;

        }
        /*#loan_table tr, #loan_table th, #loan_table td {*/
        /*    margin: 0 !important;*/
        /*    padding: 10px !important; !* Adjust padding to your preference *!*/
        /*}*/

        .table-centered {
            margin: 0 !important;
            padding: 0 !important;
        }

        #loan_table .btn {
            margin: 0 !important;
        }
    </style>
@endsection


@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    </div>
                    <h4 class="page-title">Current Loans</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <div class="row">

                            <div class="col-lg-3">
                                <div class="mb-2"> <!-- Reduced bottom margin -->
                                    <label for="route" class="form-label">Route</label>
                                    <select class="form-control select2" id="route">
                                        <option value="0">All</option>
                                        @foreach($route as $item)
                                            <option value="{{$item->id_route}}">{{ $item->name }} - {{ $item->Full_Name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-2">
                                    <label for="center" class="form-label">Center</label>
                                    <select class="form-control select2" id="center_details">
                                        <option value="0">All</option>
                                        @foreach($center as $item)
                                            <option value="{{$item->idCenter}}">{{ $item->No }} - {{ $item->Name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Group</label>
                                    <select class="form-control select2" id="group">
                                        <option value="0">All</option>
                                        @foreach($group as $item)
                                            <option value="{{$item->idCustomer_Group}}">{{ $item->Name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Category</label>
                                    <select class="form-control select2" id="category">
                                        <option value="0">All</option>
                                        @foreach($loan_category as $item)
                                            <option value="{{$item->idLoan_Category}}">{{ $item->Name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Customer</label>
                                    <select class="form-control select2" id="customer_id">
                                        <option value="0">All</option>
                                        @foreach($customers as $item)
                                            <option value="{{$item->idCustomer}}">{{ $item->cus_number }}-{{ $item->First_Name }} {{$item->Last_Name}}-{{ $item->Nic }}-{{ $item->Contact_No }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Loan Number</label>
                                    <input type="text" class="form-control" id="loan_number_search" onkeyup="load_table();">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="from_date" class="form-label">From Date (Disbursement)</label>
                                    <input type="date" class="form-control" id="from_date">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="to_date" class="form-label">To Date (Disbursement)</label>
                                    <input type="date" class="form-control" id="to_date">
                                </div>
                            </div>
                            <div class="col-lg-3 d-flex align-items-center"> <!-- Align button vertically in the center -->
                                <button type="button" class="btn btn-danger w-100" onclick="load_table();">
                                    <i class="bi bi-search"></i> Search
                                </button>
                            </div>
                        </div>
                        <hr>

                        <div class="table-responsive">
                            <table class="table table-centered mb-0" id="loan_table">
                                <thead class="sticky-top bg-purple">
                                <tr>
                                    <th>Loan No</th>
                                    <th>Route</th>
                                    <th>Center</th>
                                    <th>Group</th>
                                    <th>Customer</th>
                                    <th>Loan Category</th>
                                    <th>Loan Amount</th>
                                    <th>Due Amount</th>
                                    <th>Capital Balance</th>
                                    <th>Total Amount</th>
                                    <th>Total Balance</th>
                                    <th>Issue Date</th>
                                    <th>Lending Officer</th>
                                    <th>User</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- Table data dynamically inserted here -->
                                </tbody>
                            </table>
                            <div id="pagination" class="d-flex justify-content-end mt-3 me-3"></div>

                        </div>
                        <!-- end table-responsive-->

                        <div class="row mt-1 mb-1 p-2">
                            <div class="col-md-8 row">
                                <div class="col-sm-3">
                                    <div>
                                        <span class="fw-bold">Total Loan Count </span>
                                    </div>
                                    <div>
                                        <span class="fw-bold">Total Capital Balance</span>
                                    </div>
                                    <div>
                                        <span class="fw-bold">Total Pending Amount</span>
                                    </div>
                                    <div>
                                        <span class="fw-bold">Total Loan Amount</span>
                                    </div>


                                </div>
                                <div class="col-lg-4">
                                    <div>
                                        <span id="loan_count">0</span>
                                    </div>
                                    <div>
                                        <span id="cap_balance">0.00</span>
                                    </div>
                                    <div>
                                        <span id="tot_amount">0.00</span>
                                    </div>
                                    <div>
                                        <span id="loan_amount">0</span>
                                    </div>



                                </div>
                            </div>
                        </div>

                    </div> <!-- end card-->
                </div> <!-- end col -->


            </div>
            <!-- end row -->




        </div> <!-- container -->

    </div>

    <div class="modal fade" id="deleteLoanModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Loan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="deleteLoanId">
                    <div class="mb-3">
                        <label for="deleteReason" class="form-label">Reason for deletion</label>
                        <textarea id="deleteReason" class="form-control" rows="3" placeholder="Enter reason..."></textarea>
                    </div>
                    <button class="btn btn-danger" onclick="confirmLoanDelete()">Update</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="extraChargeModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-3">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-cash-coin me-2"></i>Add Extra Charges</h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-light" onclick="openChargeCodesModal()" title="Manage Charge Codes">
                            <i class="bi bi-gear"></i> Manage Codes
                        </button>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body py-4">
                    <input type="hidden" id="modalLoanId">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control shadow-sm" id="extra_date">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Amount</label>
                            <input type="number" class="form-control shadow-sm" id="extra_amount" placeholder="0.00" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Select Extra Charge Type</label>
                            <select class="form-control shadow-sm" id="extra_charge_type" onchange="fillChargeDescription()">
                                <option value="">-- Select Charge Type --</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control shadow-sm" id="extra_description" placeholder="Enter or edit description...">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-success px-4" onclick="saveExtraCharge()">
                            <i class="bi bi-check-circle me-1"></i> Save Charge
                        </button>
                    </div>

                    <hr class="my-4">

                    <h6 class="text-dark mb-3"><i class="bi bi-clock-history me-2"></i>Charge History</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover">
                            <thead class="table-light">
                            <tr>
                                <th style="width: 20%">Date</th>
                                <th>Description</th>
                                <th style="width: 20%">Amount (Rs)</th>
                            </tr>
                            </thead>
                            <tbody id="extraChargesTableBody">
                            <!-- Fetched rows go here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="standard-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h4 class="modal-title" >gwegerg</h4> -->
                    <h4>View Loan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">


                        <div class="col-lg-6 mb-3">
                            <label for="simpleinput" class="form-label">Group</label>
                            <select class="form-control select2" data-toggle="select2">
                                <option>Select</option>
                                <optgroup>
                                    <option value="AK">Customer 01</option>
                                    <option value="AK">Customer 02</option>
                                    <option value="AK">Customer 03</option>
                                    <option value="AK">Customer 04</option>
                                    <option value="AK">Customer 05</option>
                                    <option value="AK">Customer 06</option>
                                    <option value="AK">Customer 07</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label for="simpleinput" class="form-label">Category</label>
                            <select class="form-control select2" data-toggle="select2">
                                <option>Select</option>
                                <optgroup>
                                    <option value="AK">Customer 01</option>
                                    <option value="AK">Customer 02</option>
                                    <option value="AK">Customer 03</option>
                                    <option value="AK">Customer 04</option>
                                    <option value="AK">Customer 05</option>
                                    <option value="AK">Customer 06</option>
                                    <option value="AK">Customer 07</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label for="simpleinput" class="form-label">Customer</label>
                            <select class="form-control select2" data-toggle="select2">
                                <option>Select</option>
                                <optgroup>
                                    <option value="AK">Customer 01</option>
                                    <option value="AK">Customer 02</option>
                                    <option value="AK">Customer 03</option>
                                    <option value="AK">Customer 04</option>
                                    <option value="AK">Customer 05</option>
                                    <option value="AK">Customer 06</option>
                                    <option value="AK">Customer 07</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label for="simpleinput" class="form-label">Total Amount</label>
                            <input type="text" id="simpleinput" class="form-control">
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label for="simpleinput" class="form-label">Status</label>
                            <select class="form-control select2" data-toggle="select2">
                                <option>Select</option>
                                <optgroup>
                                    <option value="AK">Pending</option>
                                    <option value="AK">Completed</option>
                                </optgroup>
                            </select>
                        </div>


                    </div>

                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success">Save changes</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <div class="modal fade" id="issue-loan-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Issue Loan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <input type="hidden" id="loan_id_for_issue">
                <div class="modal-body">
                    <h4 class="page-title">Do you really want to issue this loan ?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" onclick="issue_loan()">Issue Loan</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="view-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h4 class="modal-title" >gwegerg</h4> -->
                    <h4>Delete Loan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <input type="hidden" id="loan_id_for_delete">
                <div class="modal-body">

                    <div class="row">
                        <div class="mb-3 px-3">
                            <label for="simpleinput" class="form-label">Reason</label>
                            <input type="text" id="reason_for_dlt" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="delete_loan()">Delete Loan</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="agreement" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h4 class="modal-title" >gwegerg</h4> -->
                    <h4>Download Document</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <input type="hidden" id="loan_id_for_delete">
                <div class="modal-body">

                    <div class="row">
                        <div class="mb-3 px-3">
                            <div class="card-body">
                                <div class="table-responsive custom-scrollbar">
                                    <table class="table table-bordered table-sm table-striped" id="agreement_table">
                                        <thead class="sticky-top bg-white">
                                        <tr>
                                            <th hidden>#</th>
                                            <th scope="col"  class="text-center">Description</th>
                                            <th scope="col"  class="text-center">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody class="custom-scrollbar" style="max-height: 400px;">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <!-- Other Charges Codes Modal -->
    <div class="modal fade" id="chargeCodesModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-3">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-gear me-2"></i>Manage Charge Codes</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Charge Codes List</h6>
                        <button type="button" class="btn btn-sm btn-primary" onclick="openAddChargeCodeModal()">
                            <i class="bi bi-plus-circle me-1"></i> Add New Code
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover" id="chargeCodesTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%">ID</th>
                                    <th width="30%">Code</th>
                                    <th width="45%">Description</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="chargeCodesTableBody">
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Charge Code Modal -->
    <div class="modal fade" id="chargeCodeFormModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-3">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="chargeCodeFormTitle">
                        <i class="bi bi-plus-circle me-2"></i><span id="chargeCodeFormTitleText">Add Charge Code</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <form id="chargeCodeForm">
                        <input type="hidden" id="chargeCodeId">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="charge_code" required placeholder="Enter code">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="charge_description" rows="3" placeholder="Enter description"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveChargeCode()">
                        <i class="bi bi-save me-1"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/payment.js?n=101"></script>
    <script>
        $(function() {


            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
            // $('#loan_table').DataTable({
            //     responsive: true,
            //     // Other options if needed
            // });

        })
    </script>
    <script>
        const exampleModal = document.getElementById('standard-modal')
        exampleModal.addEventListener('show.bs.modal', event => {
            // Button that triggered the modal
            const button = event.relatedTarget
            // Extract info from data-bs-* attributes
            const recipient = button.getAttribute('data-bs-whatever')
            // If necessary, you could initiate an AJAX request here
            // and then do the updating in a callback.
            //
            // Update the modal's content.
            const modalTitle = exampleModal.querySelector('.modal-title')
            const modalBodyInput = exampleModal.querySelector('.modal-body input')

            modalTitle.textContent = `New message to ${recipient}`
            modalBodyInput.value = recipient
        })
    </script>
    <script>
        const issueLoanModal = document.getElementById('issue-loan-modal')
        issueLoanModal.addEventListener('show.bs.modal', event => {
            // Button that triggered the modal
            const button = event.relatedTarget
            // Extract info from data-bs-* attributes
            const recipient = button.getAttribute('data-bs-whatever')
            // If necessary, you could initiate an AJAX request here
            // and then do the updating in a callback.
            //
            // Update the modal's content.
            const modalTitle = issueLoanModal.querySelector('.modal-title')
            const modalBodyInput = issueLoanModal.querySelector('.modal-body input')

            modalTitle.textContent = `New message to ${recipient}`
            modalBodyInput.value = recipient
        })
    </script>

    <script>
        const viewModal = document.getElementById('view-modal')
        exampleModal.addEventListener('show.bs.modal', event => {
            // Button that triggered the modal
            const button = event.relatedTarget
            // Extract info from data-bs-* attributes
            const recipient = button.getAttribute('data-bs-whatever')
            // If necessary, you could initiate an AJAX request here
            // and then do the updating in a callback.
            //
            // Update the modal's content.
            const modalTitle = viewModal.querySelector('.modal-title')
            const modalBodyInput = viewModal.querySelector('.modal-body input')

            modalTitle.textContent = `New message to ${recipient}`
            modalBodyInput.value = recipient
        })
    </script>

    <script>
        // Charge Codes Management Functions
        function openChargeCodesModal() {
            $('#extraChargeModal').modal('hide');
            loadChargeCodes();
            $('#chargeCodesModal').modal('show');
        }

        function loadChargeCodes() {
            $.ajax({
                url: '/other-charges-codes/list',
                type: 'GET',
                success: function(response) {
                    // Populate table
                    let tbody = $('#chargeCodesTableBody');
                    tbody.empty();

                    // Populate dropdown
                    let dropdown = $('#extra_charge_type');
                    dropdown.empty();
                    dropdown.append('<option value="">-- Select Charge Type --</option>');

                    if (response.length === 0) {
                        tbody.append('<tr><td colspan="3" class="text-center">No charge codes found</td></tr>');
                        return;
                    }

                    response.forEach(function(code) {
                        // Add to table
                        let row = `
                            <tr>
                                <td>${code.id}</td>
                                <td>${code.code}</td>
                                <td>${code.description || '-'}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="editChargeCode(${code.id})" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteChargeCode(${code.id})" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        tbody.append(row);

                        // Add to dropdown
                        let option = `<option value="${code.id}" data-description="${code.description || ''}">${code.code} - ${code.description || 'No description'}</option>`;
                        dropdown.append(option);
                    });
                },
                error: function() {
                    Swal.fire('Error!', 'Failed to load charge codes', 'error');
                }
            });
        }

        function fillChargeDescription() {
            const selected = $('#extra_charge_type option:selected');
            const description = selected.data('description');
            if (description) {
                $('#extra_description').val(description);
            }
        }

        function openAddChargeCodeModal() {
            $('#chargeCodeId').val('');
            $('#charge_code').val('');
            $('#charge_description').val('');
            $('#chargeCodeFormTitleText').text('Add Charge Code');
            $('#chargeCodeFormModal').modal('show');
        }

        function editChargeCode(id) {
            $.ajax({
                url: `/other-charges-codes/${id}`,
                type: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#chargeCodeId').val(response.data.id);
                        $('#charge_code').val(response.data.code);
                        $('#charge_description').val(response.data.description);
                        $('#chargeCodeFormTitleText').text('Edit Charge Code');
                        $('#chargeCodeFormModal').modal('show');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Failed to load charge code', 'error');
                }
            });
        }

        function saveChargeCode() {
            const id = $('#chargeCodeId').val();
            const url = id ? `/other-charges-codes/${id}` : '/other-charges-codes';
            const method = id ? 'PUT' : 'POST';

            const data = {
                code: $('#charge_code').val(),
                description: $('#charge_description').val(),
                _token: '{{ csrf_token() }}'
            };

            if (!data.code) {
                Swal.fire('Error!', 'Code is required', 'error');
                return;
            }

            $.ajax({
                url: url,
                type: method,
                data: data,
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Success!', response.message, 'success');
                        $('#chargeCodeFormModal').modal('hide');
                        loadChargeCodes();
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Operation failed';
                    Swal.fire('Error!', message, 'error');
                }
            });
        }

        function deleteChargeCode(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/other-charges-codes/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Deleted!', response.message, 'success');
                                loadChargeCodes();
                            }
                        },
                        error: function(xhr) {
                            const message = xhr.responseJSON?.message || 'Delete failed';
                            Swal.fire('Error!', message, 'error');
                        }
                    });
                }
            });
        }

        // Restore Add Extra Charges modal when Manage Charge Codes modal is closed
        $('#chargeCodesModal').on('hidden.bs.modal', function () {
            if (!$('#chargeCodeFormModal').hasClass('show')) {
                setTimeout(() => {
                    $('#extraChargeModal').modal('show');
                }, 300);
            }
        });

        // Restore Manage Charge Codes modal when Add/Edit Code Form modal is closed
        $('#chargeCodeFormModal').on('hidden.bs.modal', function () {
            if (!$('#chargeCodesModal').hasClass('show')) {
                $('#chargeCodesModal').modal('show');
            }
        });

        // Load charge codes dropdown when Extra Charges modal opens
        $('#extraChargeModal').on('shown.bs.modal', function () {
            // Load charge codes for dropdown only (don't need to load table)
            $.ajax({
                url: '/other-charges-codes/list',
                type: 'GET',
                success: function(response) {
                    let dropdown = $('#extra_charge_type');
                    dropdown.empty();
                    dropdown.append('<option value="">-- Select Charge Type --</option>');

                    response.forEach(function(code) {
                        let option = `<option value="${code.id}" data-description="${code.description || ''}">${code.code} - ${code.description || 'No description'}</option>`;
                        dropdown.append(option);
                    });
                }
            });
        });
    </script>

@endsection

