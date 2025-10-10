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
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <input type="hidden" id="modalLoanId">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control shadow-sm" id="extra_date">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control shadow-sm" id="extra_description" placeholder="Enter reason...">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Amount</label>
                            <input type="number" class="form-control shadow-sm" id="extra_amount" placeholder="0.00" min="0" step="0.01">
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
    <script src="../JS/payment.js?n=100"></script>
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

@endsection

