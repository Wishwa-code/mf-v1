@extends('layout.admin')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <style>
        thead {
            background-color: #d9edf7; /* Light blue color */
            color: #31708f; /* Darker blue text for contrast */
        }

        .bg-purple th {
            color: #e1e1e1 !important;
        }

        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;
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
                    <h4 class="page-title">Disbursement Loans</h4>
                </div>

            </div>
        </div>
        <!-- end page title -->
        <input type="hidden" id="designation_user" value="{{session('designation')}}">
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <div class="row g-2"> <!-- Use g-2 for reduced spacing -->
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
                                <div class="mb-2">
                                    <label for="group" class="form-label">Group</label>
                                    <select class="form-control select2" id="group">
                                        <option value="0">All</option>
                                        @foreach($group as $item)
                                            <option value="{{$item->idCustomer_Group}}">{{ $item->Group_No }} - {{ $item->Name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-2">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-control select2" id="category">
                                        <option value="0">All</option>
                                        @foreach($loan_category as $item)
                                            <option value="{{$item->idLoan_Category}}">{{ $item->Name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-2">
                                    <label for="customer_id" class="form-label">Customer</label>
                                    <select class="form-control select2" id="customer_id">
                                        <option value="0">All</option>
                                        @foreach($customers as $item)
                                            <option value="{{$item->idCustomer}}">{{ $item->First_Name }} {{ $item->Last_Name }} - {{ $item->Nic }} - {{ $item->Contact_No }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3" hidden>
                                <div class="mb-2">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control select2" id="status">
                                        <option value="-1" selected>Pending</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 d-flex align-items-center"> <!-- Align button vertically in the center -->
                                <button type="button" class="btn btn-danger w-100" onclick="load_table();">
                                    <i class="bi bi-search"></i> Search
                                </button>
                            </div>

                            <div class="row mt-4">
                                <div class="col-lg-4">
                                    <button class="btn btn-primary w-100" onclick="exportFundRequestPDF();">FUND REQUEST</button>
                                </div>
                                <div class="col-lg-4">
                                    <button class="btn btn-success w-100" onclick="promptDisbursementExport();">DISBURSEMENT SHEET</button>
                                </div>
                                <div class="col-lg-4">
                                    <button class="btn btn-info w-100" onclick="exportDocumentChargesPDF();">Document Charges Register</button>
                                </div>
                                <div class="col-12 mt-2">
                                    <!-- Minimal settings button -->
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="openFundRequestConfig()">
                                        Configure Fund Request Columns
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm ms-2" onclick="openDisbursementConfig()">
                                        Configure Disbursement Columns
                                    </button>
                                </div>
                            </div>

                        </div>


                        <hr>


                        <div class="table-responsive">
                            <table class="table table-centered mb-0" id="loan_table">
                                <thead class="sticky-top bg-purple">
                                <tr>
                                    <th>Loan No</th>
                                    <th>Route Code</th>
                                    <th>Route Name</th>
                                    <th>Collector ID</th>
                                    <th>Collector Name</th>
                                    <th>Center ID</th>
                                    <th>Center Name</th>
                                    <th>Group</th>
                                    <th>Customer</th>
                                    <th>Customer Code</th>
                                    <th>NIC</th>
                                    <th>Product</th>
                                    <th>Amount</th>
                                    <th>Doc Charge</th>
                                    <th>Interest Rate</th>
                                    <th>No of Weeks</th>
                                    <th>Date</th>
                                    <th>Reason</th>
                                    <th>Lending Officer</th>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->

                        <div class="row mt-1 mb-1 p-2">
                            <div class="col-md-8 row">
                                <div class="col-sm-3">
                                    <div>
                                        <span class="fw-bold">Total Loan Count </span>
                                    </div>
                                    <div>
                                        <span class="fw-bold">Total Pending Amount</span>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div>
                                        <span id="loan_count">0</span>
                                    </div>
                                    <div>
                                        <span id="tot_amount">0.00</span>
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

    <!-- Fund Request Column Config Modal -->
    <div class="modal fade" id="fundRequestConfigModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Fund Request Columns</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="fund-request-col-list" class="row g-2">
                        <!-- checkboxes injected by JS -->
                    </div>
                    <small class="text-muted d-block mt-2">Your selection is saved in this browser.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveFundRequestConfig()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Disbursement Sheet Column Config Modal -->
    <div class="modal fade" id="disbursementConfigModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Disbursement Sheet Columns</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="disbursement-col-list" class="row g-2"></div>
                    <small class="text-muted d-block mt-2">Saved in this browser.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveDisbursementConfig()">Save</button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="issue-loan-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Issue Loan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <input type="hidden" id="loan_id_for_issue">

                <div class="modal-body" >
                    <div class="card shadow mb-3" hidden>
                        <div class="card-header">
                            Approval Section
                        </div>
                        <div class="card-body">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table table-bordered table-sm table-striped" id="approval_table">
                                    <thead class="sticky-top bg-white">
                                    <tr>
                                        <th hidden>#</th>
                                        <th scope="col">Level</th>
                                        <th scope="col">Permissions</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Comment</th>
                                        <th scope="col">Approve</th>
                                        <th scope="col">Approved User</th>
                                        <th scope="col">Date Time</th>
                                        <th scope="col">Check List</th>
                                    </tr>
                                    </thead>
                                    <tbody class="custom-scrollbar" style="max-height: 400px;">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-3">
                        <div class="card-header">
                            Uploaded Document Details
                        </div>
                        <div class="card-body">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table table-bordered table-sm table-striped" id="file_table">
                                    <thead class="sticky-top bg-white">
                                    <tr>
                                        <th hidden>#</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">File</th>
                                        <th scope="col" hidden>Check</th>
                                    </tr>
                                    </thead>
                                    <tbody class="custom-scrollbar" style="max-height: 400px;">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12 px-3">
                            <label for="simpleinput" class="form-label">Customer Bank Account</label>
                            <select class="form-control" id="bank_acc">
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12 px-3">
                            <label for="simpleinput" class="form-label">Company Bank Account</label>
                            <select class="form-control" id="company_bank">
                                @foreach($bank as $item)
                                    <option value="{{$item->Idbank}}">{{$item->Bank_Name}} - {{$item->Account_Name}} - {{$item->Account_No}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="issue_loan_btn" onclick="issue_loan()">Issue Loan</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="checklist-modal" tabindex="-1" aria-labelledby="checklistModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checklistModalLabel">Checklist</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="checklist-container"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>





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


    <div class="modal fade" id="standard-modal_2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Add Loan Documents</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="hidden" id="loan_location_id">
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

    <div class="modal fade" id="loan_edit" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Change Installments</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <input type="hidden" id="loan_id">
                <div class="modal-body">

                    <!-- Date chooser -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="installment_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="installment_date" value="{{date('Y-m-d')}}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="installment_date" class="form-label">Repayment Type</label>
                            <input type="text" class="form-control" id="interest_period" disabled>
                            <input type="hidden" class="form-control" id="saturday_sunday">
                            <input type="hidden" class="form-control" id="panelty_date_2">
                        </div>
                    </div>


                    <div class="row mb-3" id="twice_a_month">
                        <div class="col-md-4">
                            <label for="installment_date" class="form-label">Choose</label>
                            <select class="form-control" id="twice_a_month_txt">
                                <option value="1" selected>First Of Month And 15th</option>
                                <option value="2">15th And End Of Month</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-10" onclick="addInstallmentDates()">Generate Installments</button>
                    <br><br>
                    <!-- Installment table -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table table-centered mb-0" id="installment_table">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Installment Date</th>
                                        <th class="text-end">Installment Amount</th>
                                        <th class="text-end">Capital Amount</th>
                                        <th class="text-end">Interest Amount</th>
                                        <th class="text-end">Penalty Date</th>
                                        <th class="text-end">Penalty Amount</th>
                                        <th class="text-end">Total Amount</th>
                                        <th class="text-end">Paid Amount</th>
                                        <th class="text-end">Penalty Balance</th>
                                        <th class="text-end">Installment Balance</th>
                                        <th class="text-end">Total Balance</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <!-- Data will be populated here -->
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                    <button class="btn btn-danger btn-10" style="float: right" onclick="update_installment()">Update Installments</button>
                    <br><br>

                </div><!-- /.modal-body -->
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/disbursement_loan.js?n=16"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>



    <script>
        $(function() {

            $('#twice_a_month').hide(); // Hide the div

            // Initialize DataTable
            let table = $('#loan_table').DataTable({
                responsive: true, // Enable responsiveness
                columnDefs: [
                    { width: '7%', targets: 0 }, // Loan No
                    { width: '8%', targets: 1 }, // Route Code
                    { width: '10%', targets: 2 }, // Route Name
                    { width: '5%', targets: 3 }, // Collector ID
                    { width: '10%', targets: 4 }, // Collector Name
                    { width: '5%', targets: 5 }, // Center ID
                    { width: '10%', targets: 6 }, // Center Name
                    { width: '8%', targets: 7 }, // Group
                    { width: '12%', targets: 8 }, // Customer
                    { width: '6%', targets: 9 }, // Customer Code
                    { width: '6%', targets: 10 }, // NIC
                    { width: '6%', targets: 11 }, // Product
                    { width: '5%', targets: 12 }, // Amount
                    { width: '5%', targets: 13 }, // Doc Charge
                    { width: '6%', targets: 14 }, // Interest Rate
                    { width: '6%', targets: 15 }, // No of Weeks
                    { width: '7%', targets: 16 }, // Date
                    { width: '3%', targets: 17 }, // Reason
                    { width: '8%', targets: 18 }, // Lending Officer
                    { width: '6%', targets: 19 }, // User
                    { width: '4%', targets: 20 }, // Status
                    { width: '45%', targets: 21 }, // Action
                    { targets: [22], visible: false } // Hide the idCustomer column
                ],

                // Additional DataTables options and initialization here
            });

            // Call load_table() function here or wherever needed
            load_table();

            // Initialize Select2 Elements
            $('.select2').select2();

            // Initialize Select2 Elements for Bootstrap 4
            $('.select2bs4').select2({ theme: 'bootstrap4' });


        })

    var authorizedName = "{{ session('Full_Name') }}";
    var companyName = {!! json_encode(session('company_name')) !!};
    var branchName = {!! json_encode(session('branch_name')) !!} || '';


        // Function to get the current date and time in Asia/Colombo timezone
        function getColomboDateTime() {
            const options = {
                timeZone: 'Asia/Colombo',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                second: 'numeric',
                hour12: true
            };
            const formatter = new Intl.DateTimeFormat('en-IN', options);
            return formatter.format(new Date());
        }

        function exportFundRequestPDF() {
            // dynamic column export with new required default ordering
            const cfg = getFundRequestConfig();
            const table = $('#loan_table').DataTable();
            const rows = table.rows().data();

            // ALL available (optional) columns
            const colDefs = {
                loan_no:        { label: 'Loan Number',   fn: r => r[0] },
                customer_name:  { label: 'Customer Name', fn: r => r[8] },
                nic:            { label: 'NIC',           fn: r => r[10] },
                amount:         { label: 'Amount',        fn: r => (parseFloat(r[12].replace(/[^0-9.-]+/g,''))||0).toFixed(2) },
                branch_name:    { label: 'Branch Name',   fn: _ => branchName || '' },
                center_name:    { label: 'Center Name',   fn: r => r[6] },
                route_name:     { label: 'Route',         fn: r => r[2] },
                collector_name: { label: 'Collector Name',fn: r => r[4] },
                group_no:       { label: 'Group Number',  fn: r => r[7] },
                interest:       { label: 'Interest Rate', fn: r => r[14] },
                weeks:          { label: 'No of Weeks',   fn: r => r[15] },
                // extra optional columns below
                customer_code:  { label: 'Customer Code', fn: r => r[9] },
                route_code:     { label: 'Route Code',    fn: r => r[1] },
                product:        { label: 'Product',       fn: r => r[11] },
                doc_charge:     { label: 'Doc Charge',    fn: r => r[13] },
                date:           { label: 'Date',          fn: r => r[16] },
                reason:         { label: 'Reason',        fn: r => r[17] },
                lending_officer:{ label: 'Lending Officer', fn: r => r[18] },
                user:           { label: 'User',          fn: r => r[19] },
                status:         { label: 'Status',        fn: r => stripHtml(r[20]) }
            };

            // Required default order if nothing saved
            const defaultFundCols = [
                'loan_no','customer_name','nic','amount','branch_name','center_name','route_name','collector_name','group_no','interest','weeks'
            ];
            const activeKeys = cfg.length ? cfg : defaultFundCols;

            let header=['#']; activeKeys.forEach(k=> header.push(colDefs[k]?.label||k));
            let body=[]; let totalAmount=0; const amountIncluded = activeKeys.includes('amount');
            for(let i=0;i<rows.length;i++){
                const row = rows[i];
                const line=[i+1];
                activeKeys.forEach(k=>{ const def=colDefs[k]; line.push(def?def.fn(row):''); });
                if(amountIncluded){ totalAmount += parseFloat(colDefs.amount.fn(row)); }
                body.push(line);
            }
            if(amountIncluded){
                const totalRow = new Array(header.length).fill('');
                const amtIdx = header.indexOf('Amount');
                if(amtIdx>-1){ totalRow[amtIdx] = totalAmount.toFixed(2); totalRow[Math.max(1,amtIdx-1)] = 'Total Amount'; }
                body.push(totalRow);
            }
            body.push(new Array(header.length).fill(''));
            body.push([`Authorized 01: ${authorizedName}`].concat(new Array(header.length-1).fill('')));
            body.push(['Authorized 02:'].concat(new Array(header.length-1).fill('')));

            var pdf = new window.jspdf.jsPDF('landscape', 'mm', 'a4');
            var dateTime = getColomboDateTime();
            var pageWidth = pdf.internal.pageSize.getWidth();
            pdf.setFontSize(14);
            pdf.text(companyName, (pageWidth - pdf.getTextWidth(companyName)) / 2, 16);
            pdf.setFontSize(12);
            pdf.text('Fund Request', (pageWidth - pdf.getTextWidth('Fund Request')) / 2, 24);
            pdf.setFontSize(11);
            var branchText = 'Branch: ' + branchName;
            pdf.text(branchText, (pageWidth - pdf.getTextWidth(branchText)) / 2, 30);
            pdf.setFontSize(10);
            var dateTimeText = 'Date: ' + dateTime;
            pdf.text(dateTimeText, (pageWidth - pdf.getTextWidth(dateTimeText)) / 2, 36);

            pdf.autoTable({
                head: [header],
                body: body,
                startY: 44,
                theme: 'grid',
                styles: { halign: 'center', lineWidth: 0.5, lineColor: [0,0,0], fontSize: 9 },
                headStyles: { fillColor: [0,0,0], textColor: [255,255,255] }
            });
            pdf.save('Fund_Request.pdf');
        }
        // --- minimal config logic (localStorage) ---
        const FUND_REQ_KEY = 'fund_request_cols_v1';
        function getFundRequestConfig(){
            try { return JSON.parse(localStorage.getItem(FUND_REQ_KEY)) || []; } catch(e){ return []; }
        }
        function openFundRequestConfig(){
            const holder = document.getElementById('fund-request-col-list');
            if(!holder) return; holder.innerHTML='';
            const allDefs = [
                {k:'loan_no',l:'Loan Number'},
                {k:'customer_name',l:'Customer Name'},
                {k:'nic',l:'NIC'},
                {k:'amount',l:'Amount'},
                {k:'branch_name',l:'Branch Name'},
                {k:'center_name',l:'Center Name'},
                {k:'route_name',l:'Route'},
                {k:'collector_name',l:'Collector Name'},
                {k:'group_no',l:'Group Number'},
                {k:'interest',l:'Interest Rate'},
                {k:'weeks',l:'No of Weeks'},
                // optional extras
                {k:'customer_code',l:'Customer Code'},
                {k:'route_code',l:'Route Code'},
                {k:'product',l:'Product'},
                {k:'doc_charge',l:'Doc Charge'},
                {k:'date',l:'Date'},
                {k:'reason',l:'Reason'},
                {k:'lending_officer',l:'Lending Officer'},
                {k:'user',l:'User'},
                {k:'status',l:'Status'}
            ];
            const saved = getFundRequestConfig();
            const defaults = ['loan_no','customer_name','nic','amount','branch_name','center_name','route_name','collector_name','group_no','interest','weeks'];
            const activeSet = new Set(saved.length? saved : defaults);
            allDefs.forEach(d=>{ const div=document.createElement('div'); div.className='col-6'; div.innerHTML=`<div class="form-check"><input class="form-check-input" type="checkbox" value="${d.k}" id="fr_${d.k}" ${activeSet.has(d.k)?'checked':''}><label class="form-check-label" for="fr_${d.k}">${d.l}</label></div>`; holder.appendChild(div); });
            new bootstrap.Modal(document.getElementById('fundRequestConfigModal')).show();
        }
        function saveFundRequestConfig(){
            const checks = document.querySelectorAll('#fund-request-col-list input[type=checkbox]');
            const sel = Array.from(checks).filter(c=>c.checked).map(c=>c.value);
            localStorage.setItem(FUND_REQ_KEY, JSON.stringify(sel));
            // close modal
            bootstrap.Modal.getInstance(document.getElementById('fundRequestConfigModal')).hide();
        }
        // Disbursement config helpers
        const DISBURSE_KEY='disbursement_cols_v1';
        function getDisbursementConfig(){ try{return JSON.parse(localStorage.getItem(DISBURSE_KEY))||[];}catch(e){return [];} }
        function openDisbursementConfig(){ const holder=document.getElementById('disbursement-col-list'); if(!holder)return; holder.innerHTML=''; const defs=[
            {k:'loan_no',l:'Loan Number'},
            {k:'nic',l:'NIC'},
            {k:'customer_name',l:'Customer Name'},
            {k:'amount',l:'Amount'},
            {k:'received_by',l:'Received By'},
            {k:'center_name',l:'Center Name'},
            {k:'interest',l:'Loan Interest'},
            {k:'weeks',l:'Number Of Weeks'},
            {k:'doc_charge',l:'Document Charge'},
            {k:'collector_name',l:'Collector Name'},
            {k:'route_name',l:'Route'},
            // extras
            {k:'bank_details',l:'Bank Details'},
            {k:'customer_code',l:'Customer Code'},
            {k:'route_code',l:'Route Code'},
            {k:'group_no',l:'Group'},
            {k:'product',l:'Product'},
            {k:'date',l:'Date'},
            {k:'reason',l:'Reason'},
            {k:'lending_officer',l:'Lending Officer'},
            {k:'user',l:'User'},
            {k:'status',l:'Status'}
        ]; const saved=getDisbursementConfig(); const defaults=['loan_no','nic','customer_name','amount','received_by','center_name','interest','weeks','doc_charge','collector_name','route_name']; const active=new Set(saved.length?saved:defaults); defs.forEach(d=>{ const div=document.createElement('div'); div.className='col-6'; div.innerHTML=`<div class="form-check"><input class="form-check-input" type="checkbox" value="${d.k}" id="ds_${d.k}" ${active.has(d.k)?'checked':''}><label class="form-check-label" for="ds_${d.k}">${d.l}</label></div>`; holder.appendChild(div);}); new bootstrap.Modal(document.getElementById('disbursementConfigModal')).show(); }
        function saveDisbursementConfig(){ const checks=document.querySelectorAll('#disbursement-col-list input[type=checkbox]'); const sel=Array.from(checks).filter(c=>c.checked).map(c=>c.value); localStorage.setItem(DISBURSE_KEY, JSON.stringify(sel)); bootstrap.Modal.getInstance(document.getElementById('disbursementConfigModal')).hide(); }
        function promptDisbursementExport() {
            Swal.fire({
                title: 'Choose Export Format',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'PDF',
                cancelButtonText: 'Excel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    exportDisbursementSheetPDF();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    exportDisbursementSheetExcel();
                }
            });
        }

        function exportDisbursementSheetExcel() {
            const cfg = getDisbursementConfig();
            const table = $('#loan_table').DataTable();
            const rows  = table.rows().data();
            const wb = XLSX.utils.book_new();
            const colDefs = {
                loan_no:{label:'Loan Number', fn:r=>r[0]},
                nic:{label:'NIC', fn:r=>r[10]},
                customer_name:{label:'Customer Name', fn:r=>r[8]},
                amount:{label:'Amount', fn:r=> (parseFloat(r[12].replace(/[^0-9.-]+/g,''))||0)},
                received_by:{label:'Received By', fn:()=>''},
                center_name:{label:'Center Name', fn:r=>r[6]},
                interest:{label:'Loan Interest', fn:r=>r[14]},
                weeks:{label:'Number Of Weeks', fn:r=>r[15]},
                doc_charge:{label:'Document Charge', fn:r=>r[13]},
                collector_name:{label:'Collector Name', fn:r=>r[4]},
                route_name:{label:'Route', fn:r=>r[2]},
                bank_details:{label:'Bank Details', fn:()=>''},
                customer_code:{label:'Customer Code', fn:r=>r[9]},
                route_code:{label:'Route Code', fn:r=>r[1]},
                group_no:{label:'Group', fn:r=>r[7]},
                product:{label:'Product', fn:r=>r[11]},
                date:{label:'Date', fn:r=>r[16]},
                reason:{label:'Reason', fn:r=>r[17]},
                lending_officer:{label:'Lending Officer', fn:r=>r[18]},
                user:{label:'User', fn:r=>r[19]},
                status:{label:'Status', fn:r=>r[20]}
            };
            const defaultDisCols=['loan_no','nic','customer_name','amount','received_by','center_name','interest','weeks','doc_charge','collector_name','route_name'];
            const activeKeys = (cfg.length?cfg:defaultDisCols);
            let header=['#']; activeKeys.forEach(k=> header.push(colDefs[k].label));
            let dataRows=[header]; let rowData=[]; let customerIds=[]; let totalAmount=0;
            for(let i=0;i<rows.length;i++){ const row=rows[i]; const idCustomer=row[22]; rowData.push({idCustomer,row}); customerIds.push(idCustomer);}            
            $.ajax({
                url:'/get-customer-bank-details', type:'POST', dataType:'json', data:{ customer_ids: customerIds, _token:$('meta[name="csrf-token"]').attr('content')},
                success:function(resp){
                    rowData.forEach((rec,idx)=>{ let line=[idx+1]; activeKeys.forEach(k=>{ if(k==='bank_details') line.push(resp[String(rec.idCustomer).trim()]||''); else if(k==='amount'){ const amt=colDefs.amount.fn(rec.row); totalAmount+=amt; line.push(amt.toFixed(2)); } else line.push(colDefs[k]?colDefs[k].fn(rec.row):''); }); dataRows.push(line); });
                    if(activeKeys.includes('amount')){ dataRows.push([]); const totalRow=new Array(header.length).fill(''); const aI=header.indexOf('Amount'); if(aI>-1){ totalRow[Math.max(1,aI-1)]='Total Amount'; totalRow[aI]=totalAmount.toFixed(2);} dataRows.push(totalRow);}                    
                    const ws = XLSX.utils.aoa_to_sheet(dataRows); XLSX.utils.book_append_sheet(wb, ws, 'Disbursement Sheet'); XLSX.writeFile(wb, 'Disbursement_Sheet.xlsx');
                }, error:()=> console.error('Error loading bank details for Excel') });
        }

        // Helper function to strip HTML tags and get clean text
        function stripHtml(html) {
            if (!html) return '';
            const temp = document.createElement('div');
            temp.innerHTML = html;
            return temp.textContent || temp.innerText || '';
        }

        function exportDisbursementSheetPDF() {
            const cfg = getDisbursementConfig();
            const table = $('#loan_table').DataTable();
            const rows  = table.rows().data();
            // Auto-sizing columns (no fixed widths), same approach as Fund Request PDF
            const colDefs = {
                loan_no:{label:'Loan Number', fn:r=>r[0]},
                nic:{label:'NIC', fn:r=>r[10]},
                customer_name:{label:'Customer Name', fn:r=>r[8]},
                amount:{label:'Amount', fn:r=> (parseFloat(r[12].replace(/[^0-9.-]+/g,''))||0)},
                received_by:{label:'Received By', fn:()=>''},
                center_name:{label:'Center Name', fn:r=>r[6]},
                interest:{label:'Loan Interest', fn:r=>r[14]},
                weeks:{label:'Number Of Weeks', fn:r=>r[15]},
                doc_charge:{label:'Document Charge', fn:r=>r[13]},
                collector_name:{label:'Collector Name', fn:r=>r[4]},
                route_name:{label:'Route', fn:r=>r[2]},
                bank_details:{label:'Bank Details', fn:()=>''},
                customer_code:{label:'Customer Code', fn:r=>r[9]},
                route_code:{label:'Route Code', fn:r=>r[1]},
                group_no:{label:'Group', fn:r=>r[7]},
                product:{label:'Product', fn:r=>r[11]},
                date:{label:'Date', fn:r=>r[16]},
                reason:{label:'Reason', fn:r=>r[17]},
                lending_officer:{label:'Lending Officer', fn:r=>r[18]},
                user:{label:'User', fn:r=>r[19]},
                status:{label:'Status', fn:r=>stripHtml(r[20])}
            };
            const defaultDisCols=['loan_no','nic','customer_name','amount','received_by','center_name','interest','weeks','doc_charge','collector_name','route_name'];
            const activeKeys = (cfg.length?cfg:defaultDisCols);
            let header=['#']; activeKeys.forEach(k=> header.push(colDefs[k].label));
            let bodyRows=[]; let rowData=[]; let customerIds=[]; let totalAmount=0;
            for(let i=0;i<rows.length;i++){ const row=rows[i]; const idCustomer=row[22]; rowData.push({idCustomer,row}); customerIds.push(idCustomer);}            
            $.ajax({
                url:'/get-customer-bank-details', type:'POST', data:{customer_ids:customerIds,_token:$('meta[name="csrf-token"]').attr('content')},
                success:function(resp){
                    // Build rows with dynamic columns and running total (like Fund Request)
                    rowData.forEach((rec,idx)=>{
                        const line=[idx+1];
                        activeKeys.forEach(k=>{
                            if(k==='bank_details'){
                                line.push(resp[String(rec.idCustomer).trim()]||'');
                            }else if(k==='amount'){
                                const amt=colDefs.amount.fn(rec.row);
                                totalAmount+=amt;
                                line.push(amt.toFixed(2));
                            }else{
                                line.push(colDefs[k]? colDefs[k].fn(rec.row):'');
                            }
                        });
                        bodyRows.push(line);
                    });

                    // Add total row if amount included
                    if(activeKeys.includes('amount')){
                        bodyRows.push(new Array(header.length).fill(''));
                        const totalRow=new Array(header.length).fill('');
                        const amtIdx = header.indexOf('Amount');
                        if(amtIdx>-1){
                            totalRow[amtIdx] = totalAmount.toFixed(2);
                            totalRow[Math.max(1, amtIdx-1)] = 'Total Amount';
                        }
                        bodyRows.push(totalRow);
                    }

                    // Create PDF with auto-sized table (no columnStyles), same pattern as Fund Request
                    const pdf=new window.jspdf.jsPDF('landscape','mm','a4');
                    const dateTime=getColomboDateTime();
                    const pageWidth=pdf.internal.pageSize.getWidth();
                    pdf.setFontSize(14);
                    pdf.text(companyName,(pageWidth-pdf.getTextWidth(companyName))/2,16);
                    pdf.setFontSize(12);
                    pdf.text('Disbursement Sheet',(pageWidth-pdf.getTextWidth('Disbursement Sheet'))/2,24);
                    pdf.setFontSize(10);
                    pdf.text('Date: '+dateTime,(pageWidth-pdf.getTextWidth('Date: '+dateTime))/2,32);

                    pdf.autoTable({
                        head:[header],
                        body:bodyRows,
                        startY:40,
                        theme:'grid',
                        styles:{ halign:'center', lineWidth:0.5, lineColor:[0,0,0], fontSize:9 },
                        headStyles:{ fillColor:[0,0,0], textColor:[255,255,255] }
                    });

                    pdf.save('Disbursement_Sheet.pdf');
                },
                error:()=> console.error('Error loading bank details')
            });
        }




        function exportDocumentChargesPDF() {
            var table = $('#loan_table').DataTable();
            var rows = table.rows().data();

            var data = [['#', 'Customer Number', 'NIC', 'Customer Name', 'Doc Charges']];
            var totalDocCharges = 0;

            for (var i = 0; i < rows.length; i++) {
                var row = rows[i];
                var index = i + 1;
                var customerNumber = row[5];
                var nic = row[6];
                var customerName = row[4];
                var docCharges = parseFloat(row[9].replace(/[^0-9.-]+/g, "")) || 0;

                if (docCharges > 0) {
                    totalDocCharges += docCharges;
                    data.push([index, customerNumber, nic, customerName, docCharges.toFixed(2)]);
                }
            }

            data.push(['', '', '', 'Total Doc Charges', totalDocCharges.toFixed(2)]);
            data.push([]);
            data.push(['', 'CRO:', '', 'Branch Manager:', '']);

            var pdf = new window.jspdf.jsPDF('p', 'mm', 'a4');
            var dateTime = getColomboDateTime();
            var pageWidth = pdf.internal.pageSize.getWidth();

            pdf.setFontSize(14);
            pdf.text(companyName, (pageWidth - pdf.getTextWidth(companyName)) / 2, 16);

            pdf.setFontSize(12);
            var title = "Document Charges Register";
            pdf.text(title, (pageWidth - pdf.getTextWidth(title)) / 2, 24);

            pdf.setFontSize(10);
            var dateTimeText = "Date: " + dateTime;
            pdf.text(dateTimeText, (pageWidth - pdf.getTextWidth(dateTimeText)) / 2, 32);

            pdf.autoTable({
                head: [data[0]],
                body: data.slice(1),
                startY: 40,
                theme: 'grid',
                styles: { halign: 'center', fontSize: 10, lineColor: [0, 0, 0], lineWidth: 0.4 },
                headStyles: { fillColor: [0, 0, 0], textColor: [255, 255, 255] },
                columnStyles: {
                    0: { cellWidth: 10 },
                    1: { cellWidth: 35 },
                    2: { cellWidth: 35 },
                    3: { cellWidth: 50 },
                    4: { cellWidth: 30 },
                }
            });

            pdf.save('Document_Charges_Register.pdf');
        }









        function set_cus(id){
            $('#loan_location_id').val(id);
        }


        function upload_excel() {
            var fileInput = document.getElementById('uploadExcel');  // Get the file input element
            var file = fileInput.files[0];  // Get the selected file

            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var data = new Uint8Array(e.target.result);
                    var workbook = XLSX.read(data, { type: 'array' });

                    // Assuming the first sheet in the Excel file
                    var firstSheet = workbook.Sheets[workbook.SheetNames[0]];

                    // Convert sheet to JSON, starting from the 5th row (index 5 in zero-indexed array)
                    var jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });

                    // Start reading data from the 5th index (skip the first 5 rows)
                    var dataFrom5thRow = jsonData.slice(5);

                    console.log(dataFrom5thRow);  // Debugging: see the data in console

                    // SweetAlert2 confirmation prompt
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to upload the Excel data?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, upload it!',
                        cancelButtonText: 'No, cancel!',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Send data to backend using AJAX
                            $.ajax({
                                url: '/upload-excel-loan',  // Your route URL
                                type: 'POST',
                                data: {
                                    excelData: dataFrom5thRow,  // Send the Excel data
                                },
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                                },
                                success: function(response) {
                                    Swal.fire({
                                        position: "center",
                                        icon: "success",
                                        title: "Your Excel data has been uploaded.",
                                    }).then(function () {
                                        window.location.reload();
                                    });
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire(
                                        'Error!',
                                        'There was an issue uploading the file.',
                                        'error'
                                    );
                                    console.error(error);  // Handle errors
                                }
                            });
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            Swal.fire(
                                'Cancelled',
                                'Your Excel data upload was cancelled.',
                                'error'
                            );
                        }
                    });
                };
                reader.readAsArrayBuffer(file);
            }
        }
    </script>
    {{--    <script>--}}
    {{--        const exampleModal = document.getElementById('standard-modal')--}}
    {{--        exampleModal.addEventListener('show.bs.modal', event => {--}}
    {{--            // Button that triggered the modal--}}
    {{--            const button = event.relatedTarget--}}
    {{--            // Extract info from data-bs-* attributes--}}
    {{--            const recipient = button.getAttribute('data-bs-whatever')--}}
    {{--            // If necessary, you could initiate an AJAX request here--}}
    {{--            // and then do the updating in a callback.--}}
    {{--            //--}}
    {{--            // Update the modal's content.--}}
    {{--            const modalTitle = exampleModal.querySelector('.modal-title')--}}
    {{--            const modalBodyInput = exampleModal.querySelector('.modal-body input')--}}

    {{--            modalTitle.textContent = `New message to ${recipient}`--}}
    {{--            modalBodyInput.value = recipient--}}
    {{--        })--}}
    {{--    </script>--}}
    {{--    <script>--}}
    {{--        const issueLoanModal = document.getElementById('issue-loan-modal')--}}
    {{--        issueLoanModal.addEventListener('show.bs.modal', event => {--}}
    {{--            // Button that triggered the modal--}}
    {{--            const button = event.relatedTarget--}}
    {{--            // Extract info from data-bs-* attributes--}}
    {{--            const recipient = button.getAttribute('data-bs-whatever')--}}
    {{--            // If necessary, you could initiate an AJAX request here--}}
    {{--            // and then do the updating in a callback.--}}
    {{--            //--}}
    {{--            // Update the modal's content.--}}
    {{--            const modalTitle = issueLoanModal.querySelector('.modal-title')--}}
    {{--            const modalBodyInput = issueLoanModal.querySelector('.modal-body input')--}}

    {{--            modalTitle.textContent = `New message to ${recipient}`--}}
    {{--            modalBodyInput.value = recipient--}}
    {{--        })--}}
    {{--    </script>--}}

    {{--    <script>--}}
    {{--        const viewModal = document.getElementById('view-modal')--}}
    {{--        exampleModal.addEventListener('show.bs.modal', event => {--}}
    {{--            // Button that triggered the modal--}}
    {{--            const button = event.relatedTarget--}}
    {{--            // Extract info from data-bs-* attributes--}}
    {{--            const recipient = button.getAttribute('data-bs-whatever')--}}
    {{--            // If necessary, you could initiate an AJAX request here--}}
    {{--            // and then do the updating in a callback.--}}
    {{--            //--}}
    {{--            // Update the modal's content.--}}
    {{--            const modalTitle = viewModal.querySelector('.modal-title')--}}
    {{--            const modalBodyInput = viewModal.querySelector('.modal-body input')--}}

    {{--            modalTitle.textContent = `New message to ${recipient}`--}}
    {{--            modalBodyInput.value = recipient--}}
    {{--        })--}}
    {{--    </script>--}}

@endsection

