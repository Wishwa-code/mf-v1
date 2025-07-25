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
                                    <button class="btn btn-success w-100" onclick="exportDisbursementSheetPDF();">DISBURSEMENT SHEET</button>
                                </div>
                                <div class="col-lg-4">
                                    <button class="btn btn-info w-100" onclick="exportDocumentChargesPDF();">Document Charges Register</button>
                                </div>
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
                                    <th>Customer Code</th>
                                    <th>NIC</th>
                                    <th>Product</th>
                                    <th>Amount</th>
                                    <th>Doc Charge</th>
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
    <script src="../JS/disbursement_loan.js?n=15"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>



    <script>
        $(function() {

            $('#twice_a_month').hide(); // Hide the div

            // Initialize DataTable
            let table = $('#loan_table').DataTable({
                responsive: true, // Enable responsiveness
                columnDefs: [
                    { width: '8%', targets: 0 }, // Loan No
                    { width: '5%', targets: 1 }, // Group
                    { width: '15%', targets: 2 }, // Customer
                    { width: '15%', targets: 3 }, // Loan Category
                    // Add more targets as needed
                    { width: '5%', targets: 4 }, // Total Amount
                    { width: '8%', targets: 5 }, // Created Date Time
                    { width: '3%', targets: 6 }, // Reason
                    { width: '15%', targets: 7 }, // Lending Officer
                    { width: '7%', targets: 8 }, // User
                    { width: '5%', targets: 9 }, // Status
                    { width: '50%', targets: 10 } // Action
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
            var table = $('#loan_table').DataTable();
            var rows = table.rows().data();

            var data = [['#', 'Customer No', 'Customer Name', 'NIC', 'Amount']];
            var totalAmount = 0;

            for (var i = 0; i < rows.length; i++) {
                var row = rows[i];
                var index = i + 1;
                var cus_no = row[5];
                var cus_name = row[4];
                var nic = row[6];
                var amount = parseFloat(row[8].replace(/[^0-9.-]+/g, "")) || 0;

                totalAmount += amount;
                data.push([index, cus_no, cus_name, nic, amount.toFixed(2)]);
            }

            data.push(['', '', '', 'Total Amount', totalAmount.toFixed(2)]);
            data.push(['', '', '', '', '']);
            var authorizedText = "Authorized 01: " + authorizedName;
            data.push([authorizedText, '', '', '', 'Authorized 02:']);

            var pdf = new window.jspdf.jsPDF('p', 'mm', 'a4');
            var dateTime = getColomboDateTime();
            var pageWidth = pdf.internal.pageSize.getWidth();

            pdf.setFontSize(14);
            var textWidth = pdf.getTextWidth(companyName);
            pdf.text(companyName, (pageWidth - textWidth) / 2, 16);

            pdf.setFontSize(12);
            var title = "Fund Request";
            pdf.text(title, (pageWidth - pdf.getTextWidth(title)) / 2, 24);

            pdf.setFontSize(10);
            var dateTimeText = "Date: " + dateTime;
            pdf.text(dateTimeText, (pageWidth - pdf.getTextWidth(dateTimeText)) / 2, 32);

            pdf.autoTable({
                head: [data[0]],
                body: data.slice(1),
                startY: 40,
                theme: 'grid',
                styles: { halign: 'center', lineWidth: 0.5, lineColor: [0, 0, 0] },
                headStyles: { fillColor: [0, 0, 0], textColor: [255, 255, 255] },
            });

            pdf.save('Fund_Request.pdf');
        }


        function exportDisbursementSheetPDF() {
            var table = $('#loan_table').DataTable();
            var rows = table.rows().data();

            var data = [['#', 'Customer Number', 'NIC', 'Customer Name','Bank Details', 'Amount', 'Received By']];
            var totalAmount = 0;

            for (var i = 0; i < rows.length; i++) {
                var row = rows[i];
                var index = i + 1;
                var customerNumber = row[5];
                var nic = row[6];
                var customerName = row[4];
                var amount = parseFloat(row[8].replace(/[^0-9.-]+/g, "")) || 0;

                totalAmount += amount;
                data.push([index, customerNumber, nic, customerName, amount.toFixed(2), '']);
            }

            data.push(['', '', '', 'Total Amount', totalAmount.toFixed(2), '']);
            data.push([]);
            var authorizedText = "Prepared By: " + authorizedName;
            data.push(['', authorizedText, '', '', 'Authorized 01:', '']);
            data.push(['', '', '', '', 'Authorized 02:', '']);
            data.push(['', '', '', '', 'All Cheques Received:', '']);

            var pdf = new window.jspdf.jsPDF('p', 'mm', 'a4');
            var dateTime = getColomboDateTime();
            var pageWidth = pdf.internal.pageSize.getWidth();

            pdf.setFontSize(14);
            pdf.text(companyName, (pageWidth - pdf.getTextWidth(companyName)) / 2, 16);

            pdf.setFontSize(12);
            var title = "Disbursement Sheet";
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
                    5: { cellWidth: 30 },
                }
            });

            pdf.save('Disbursement_Sheet.pdf');
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

