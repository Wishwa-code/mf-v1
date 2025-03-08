@extends('layout.admin')

@section('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">

    <!-- DataTable Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <style>
        /* Container Styling */
        .content-container {
            background-color: #fff;
            border-radius: 6px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .content-header h2 {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .btn {
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            font-weight: 500;
        }

        .btn-success {
            background-color: #28a745;
            color: #fff;
        }
        .btn-success:hover {
            background-color: #218838;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .btn-primary {
            background-color: #007bff;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #0069d9;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        /* Tabs and Export Section */
        .tabs-section {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .tabs-section .btn-secondary {
            border-radius: 0;
        }

        .tabs-section .btn-secondary:first-child {
            border-top-left-radius: 6px;
            border-bottom-left-radius: 6px;
        }

        .tabs-section .btn-secondary:last-child {
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
        }

        .tabs-section .btn-secondary.active {
            background-color: #343a40;
        }

        .export-btn {
            margin-left: 20px;
        }

        /* Search and Reset */
        .search-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-box input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        /* Table Styling */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: #fff;
            border-radius: 6px;
            overflow: hidden;
        }

        .table thead {
            background-color: #f5f5f5;
        }

        .table th,
        .table td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
            color: #333;
        }

        .table tbody tr {
            transition: background-color 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f9f9f9;
        }

        .close {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #333;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #000;
        }

        /* Modals */
        .modal, .modal_2 {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            align-items: center;
            justify-content: center;
        }

        .modal-content, .modal_2-content {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.1);
        }

        /* Medium Modal */
        .modal-content-medium {
            width: 600px;
            max-width: 90%;
        }

        /* Large Modal */
        .large-modal-content {
            width: 900px;
            max-width: 90%;
            height: auto;
            overflow-y: auto;
        }

        .modal-header, .modal_2-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .modal-header h5, .modal_2-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .modal-body .form-group,
        .modal_2-body .dropdown-section {
            margin-bottom: 15px;
        }

        .modal-body label,
        .modal_2-body label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }

        .modal-body input,
        .modal-body select,
        .modal_2-body select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .modal-body input:focus,
        .modal-body select:focus,
        .modal_2-body select:focus {
            border-color: #999;
            outline: none;
        }

        .modal-footer,
        .modal_2-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .modal-footer .btn,
        .modal_2-footer .btn {
            margin-left: 10px;
        }

        .ending-balance {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }

        /* Hide tables except the active one */
        .tab-table {
            display: none;
        }

        .tab-table.active {
            display: table;
        }
        .search-section .btn {
            height: calc(100% - 0.5rem); /* Match the height of inputs */
            margin-top: 28px; /* Space adjustment */
            display: flex;
            align-items: center;
            justify-content: center;
        }

    </style>
@endsection

@section('content')
    <div class="content-container">
        <!-- Header Section with Add Buttons -->
        <div class="content-header">
            <h2>Chart of Accounts</h2>
            <div>
                <button class="btn btn-success" id="addChartOfAccountBtn">Add Chart of Account</button>
            </div>
        </div>

        <div class="search-section">
            <form class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label for="code" class="form-label">Code</label>
                    <input type="text" class="form-control" id="code" placeholder="Code">
                </div>
                <div class="col-md-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Name">
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-control account_type_select" name="type" id="type" required="">
                        <option value=""></option>
                        <optgroup label="Revenue">
                            <option value="29">Non-operating Revenue</option>
                            <option value="36">Revenue from Deposit</option>
                            <option value="5">Revenue from Lender Investment</option>
                            <option value="34">Revenue from Loan</option>
                            <option value="21">Subsidy</option>
                        </optgroup>
                        <optgroup label="Expenses">
                            <option value="41">Asset Disposal</option>
                            <option value="16">Default Loan</option>
                            <option value="17">Depreciation</option>
                            <option value="18">Exchange Rate Loss</option>
                            <option value="39">Expenses on Borrowing</option>
                            <option value="3">Expenses on Deposit</option>
                            <option value="19">Miscellaneous Expense</option>
                            <option value="20">Non-operating Expense</option>
                            <option value="15">Provision for Loan Impairment</option>
                            <option value="43">Restructured Loan</option>
                            <option value="6">Tax</option>
                        </optgroup>
                        <optgroup label="Assets">
                            <option value="10">Account Receivable</option>
                            <option value="7">Cash and Bank</option>
                            <option value="23">Current Asset</option>
                            <option value="8">Lender Investment</option>
                            <option value="35">Loan</option>
                            <option value="42">Non-Current Asset</option>
                            <option value="30">Receivable</option>
                            <option value="11">Taxes Paid on Purchase</option>
                        </optgroup>
                        <optgroup label="Liabilities">
                            <option value="12">Accounts Payable</option>
                            <option value="24">Accumulated Depreciation</option>
                            <option value="22">Accumulated Loan Impairment</option>
                            <option value="27">Borrower Saving Deposit</option>
                            <option value="26">Investor Deposit</option>
                            <option value="40">Liability</option>
                            <option value="37">Merchant Borrowing</option>
                            <option value="25">Payable</option>
                            <option value="13">Taxes Received on Sale</option>
                        </optgroup>
                        <optgroup label="Equity">
                            <option value="14">Equity</option>
                            <option value="28">Retained Earning</option>
                        </optgroup>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary w-100" >Search</button>
                </div>
            </form>
        </div>



        <!-- Tabs Section -->
        <div class="tabs-section">
            <button class="btn btn-secondary tab-btn active" data-tab="all">All Accounts</button>
            <button class="btn btn-secondary tab-btn" data-tab="assets">Assets</button>
            <button class="btn btn-secondary tab-btn" data-tab="liabilities">Liabilities</button>
            <button class="btn btn-secondary tab-btn" data-tab="equity">Equity</button>
            <button class="btn btn-secondary tab-btn" data-tab="expenses">Expenses</button>
            <button class="btn btn-secondary tab-btn" data-tab="revenue">Revenue</button>

            <button class="btn btn-secondary export-btn">Export Data</button>
        </div>

        <!-- Search and Reset -->


        <!-- Tables for Each Tab -->
        <table class="table tab-table active" id="table-all">
            <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Type</th>
                <th>Group</th>
                <th>Cash Flow Type</th>
                <th>Created As</th>
                <th>Ledger</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <table class="table tab-table" id="table-assets">
            <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Type</th>
                <th>Group</th>
                <th>Cash Flow Type</th>
                <th>Created As</th>
                <th>Ledger</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <table class="table tab-table" id="table-liabilities">
            <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Type</th>
                <th>Group</th>
                <th>Cash Flow Type</th>
                <th>Created As</th>
                <th>Ledger</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <table class="table tab-table" id="table-equity">
            <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Type</th>
                <th>Group</th>
                <th>Cash Flow Type</th>
                <th>Created As</th>
                <th>Ledger</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <table class="table tab-table" id="table-expenses">
            <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Type</th>
                <th>Group</th>
                <th>Cash Flow Type</th>
                <th>Created As</th>
                <th>Ledger</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <table class="table tab-table" id="table-revenue">
            <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Type</th>
                <th>Group</th>
                <th>Cash Flow Type</th>
                <th>Created As</th>
                <th>Ledger</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    <!-- Modal: New Account (Medium Size) -->
    <div class="modal" id="newAccountModal">
        <div class="modal-content modal-content-medium">
            <div class="modal-header">
                <h5>New Account</h5>
                <button class="close" id="closeModal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="accountCode">Code</label>
                    <input type="number" id="accountCode" placeholder="Enter account code">
                </div>
                <div class="form-group">
                    <label for="accountName">Account Name</label>
                    <input type="text" id="accountName" placeholder="Enter account name">
                </div>
                <div class="form-group">
                    <label for="accountType">Account Type</label>
                    <select class="form-control account_type_select" name="coa_account_type_id" id="inputAccountGroupId" required="">
                        <option value=""></option>
                        <optgroup label="Revenue">
                            <option value="29">Non-operating Revenue</option>
                            <option value="36">Revenue from Deposit</option>
                            <option value="5">Revenue from Lender Investment</option>
                            <option value="34">Revenue from Loan</option>
                            <option value="21">Subsidy</option>
                        </optgroup>
                        <optgroup label="Expenses">
                            <option value="41">Asset Disposal</option>
                            <option value="16">Default Loan</option>
                            <option value="17">Depreciation</option>
                            <option value="18">Exchange Rate Loss</option>
                            <option value="39">Expenses on Borrowing</option>
                            <option value="3">Expenses on Deposit</option>
                            <option value="19">Miscellaneous Expense</option>
                            <option value="20">Non-operating Expense</option>
                            <option value="15">Provision for Loan Impairment</option>
                            <option value="43">Restructured Loan</option>
                            <option value="6">Tax</option>
                            <option value="66">Financial Expenses</option>
                        </optgroup>
                        <optgroup label="Assets">
                            <option value="10">Account Receivable</option>
                            <option value="7">Cash and Bank</option>
                            <option value="23">Current Asset</option>
                            <option value="8">Lender Investment</option>
                            <option value="35">Loan</option>
                            <option value="42">Non-Current Asset</option>
                            <option value="30">Receivable</option>
                            <option value="11">Taxes Paid on Purchase</option>
                        </optgroup>
                        <optgroup label="Liabilities">
                            <option value="12">Accounts Payable</option>
                            <option value="24">Accumulated Depreciation</option>
                            <option value="22">Accumulated Loan Impairment</option>
                            <option value="27">Borrower Saving Deposit</option>
                            <option value="26">Investor Deposit</option>
                            <option value="40">Liability</option>
                            <option value="37">Merchant Borrowing</option>
                            <option value="25">Payable</option>
                            <option value="13">Taxes Received on Sale</option>
                        </optgroup>
                        <optgroup label="Equity">
                            <option value="14">Equity</option>
                            <option value="28">Retained Earning</option>
                        </optgroup>
                    </select>
                </div>
                <div class="form-group">
                    <label for="detailType">Cash Flow Type</label>
                    <select class="form-control" name="coa_cash_flow_type_id" id="inputCashFlowType" required="">
                        <option value=""></option>
                        <option value="Operating activities">Operating activities</option>
                        <option value="Investing activities">Investing activities</option>
                        <option value="Financing activities">Financing activities</option>
                        <option value="Non-operating activities">Non-operating activities</option>
                        <option value="Cash Flow from taxes">Cash Flow from taxes</option>
                        <option value="Non Applicable">Non Applicable</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" id="description" placeholder="Enter description">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="cancelModal">Cancel</button>
                <button class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>

    <!-- Large Modal for Account History -->
    <div class="modal" id="accountHistoryModal">
        <div class="modal-content large-modal-content">
            <div class="modal-header">
                <h5 id="ledgerAccountTitle">Ledger Details</h5> <!-- Add dynamic title here -->
                <button class="close" id="closeHistoryModal">&times;</button>
            </div>
            <div class="modal-body">
                <table  id="financialReportTable" class="table">
                    <thead>
                    <tr>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Debit Amount</th>
                        <th>Credit Amount</th>
                        <th>Balance</th>
                        <th>Contra Account</th>
                        <th>Reconciliation No</th>
                        <th>Created At</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Data will be dynamically populated -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="closeHistoryFooterModal">Close</button>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <!-- Include SheetJS -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <!-- DataTable JS -->
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

    <!-- DataTable Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>

    <!-- JS for Excel export (from xlsx library) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#financialReportTable').DataTable({
                dom: 'Bfrtip',  // Adds the button container to the top of the table
                buttons: [
                    {
                        extend: 'excelHtml5',  // Exports the table to Excel
                        text: 'Download Excel', // Text displayed on the button
                        title: 'Ledger Details', // The name of the table in the exported Excel file
                        className: 'btn btn-success' // Button styling (optional)
                    }
                ]
            });
            // Set up AJAX headers for CSRF
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $('.btn-primary').on('click', function(e) {
                    e.preventDefault();

                    // Get values from fields
                    var code = $('#accountCode').val();
                    var accName = $('#accountName').val();
                    var cashFlowType = $('#inputCashFlowType').val();
                    var description = $('#description').val();

                    var $selectedOption = $('#inputAccountGroupId option:selected');
                    var accType = $selectedOption.text();
                    var accTypeGroup = $selectedOption.closest('optgroup').attr('label');

                    // Validation checks for required fields
                    // Adjust these conditions based on which fields are mandatory
                    if (!code || !accName || !accTypeGroup || !accType || !cashFlowType) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Incomplete Fields',
                            text: 'Please fill out all required fields before saving.'
                        });
                        return; // Stop here if any required field is empty
                    }

                    // Prepare data to send
                    var data = {
                        code: code,
                        acc_name: accName,
                        acc_type_group: accTypeGroup,
                        acc_type: accType,
                        cash_flow_type: cashFlowType,
                        description: description
                    };


                    // Ask for confirmation before saving
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to save this chart of account?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Save it!',
                        cancelButtonText: 'No, Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // User confirmed, proceed with AJAX
                            $.ajax({
                                url: "{{ route('chart_of_account.store') }}",
                                type: "POST",
                                data: data,
                                success: function(response) {
                                    if(response.status === 'success') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Saved!',
                                            text: 'Chart of account has been saved successfully.',
                                            confirmButtonText: 'OK'
                                        }).then((res) => {
                                            if (res.isConfirmed) {
                                                window.location.reload();
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'This code is already exist.',
                                        });
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'An unexpected error occurred.'
                                    });
                                }
                            });
                        }
                        // If user cancels, do nothing
                    });
                });
            });



            $(document).ready(function () {
                // Function to fetch and filter data
                function fetchAndPopulateData(group, filters = {}) {
                    // Determine table ID
                    const tableId = group === "all" ? "#table-all" : `#table-${group}`;

                    // Make AJAX call to fetch data
                    $.ajax({
                        url: "{{ route('chart_of_account.list', '') }}/" + group,
                        type: "GET",
                        data: filters, // Pass search filters here
                        success: function (data) {
                            populateTable(tableId, data);
                        },
                        error: function () {
                            console.error("Failed to fetch data for the search");
                        }
                    });
                }

                // Populate the table dynamically
                function populateTable(tableId, data) {
                    const $tbody = $(tableId + " tbody");
                    $tbody.empty(); // Clear table body

                    if (data.length === 0) {
                        $tbody.html("<tr><td colspan='8' class='text-center'>No records found</td></tr>");
                        return;
                    }

                    // Append rows to the table
                    data.forEach((item) => {
                        // Check if Bank_Type starts with "System_default_"
                        const bankType = item.Bank_Type.startsWith("System_default_") ? "System Default" : item.Bank_Type;

                        const ledger = `<a href="#" class="view-btn"
        data-account="${item.Idbank}"
        data-account-name="${item.Account_Name}"
        data-account-type="${item.type}"
        data-acc-type-group="${item.acc_type_group}">View</a>`;

                        const row = `
        <tr>
            <td>${item.code ? item.code : '-'}</td>
            <td>${item.Account_Name}</td>
            <td>${item.type}</td>
            <td>${item.acc_type_group}</td>
            <td>${item.cashflow ? item.cashflow : '-'}</td>
            <td>${bankType}</td> <!-- Updated -->
            <td>${ledger}</td>
        </tr>`;

                        $tbody.append(row);
                    });

                }

                // Attach event handler for the search button
                $('.btn-secondary').on('click', function (e) {
                    e.preventDefault();

                    // Get search input values
                    const code = $('#code').val();
                    const name = $('#name').val();
                    const type = $('#type option:selected').text();

                    // Determine the current active group
                    const activeTab = $('.tab-btn.active').data('tab');

                    // Fetch and filter data using AJAX
                    fetchAndPopulateData(activeTab, { code, name, type });
                });

                // Tab switching logic
                $('.tab-btn').on('click', function () {
                    // Remove active class from all tabs
                    $('.tab-btn').removeClass('active');
                    // Add active class to clicked tab
                    $(this).addClass('active');

                    // Get the tab name
                    const tabName = $(this).data('tab');

                    // Trigger a fetch for the selected tab
                    fetchAndPopulateData(tabName);
                });

                // Load data for the default (active) tab on page load
                const defaultTab = $('.tab-btn.active').data('tab');
                fetchAndPopulateData(defaultTab);
            });


            // When the export button is clicked
            $('.export-btn').on('click', function(e) {
                e.preventDefault();

                // Define the tables and corresponding sheet names
                var tables = [
                    {id: '#table-all', sheetName: 'All Accounts'},
                    {id: '#table-assets', sheetName: 'Assets'},
                    {id: '#table-liabilities', sheetName: 'Liabilities'},
                    {id: '#table-equity', sheetName: 'Equity'},
                    {id: '#table-expenses', sheetName: 'Expenses'},
                    {id: '#table-revenue', sheetName: 'Revenue'}
                ];

                // Create a new workbook
                var wb = XLSX.utils.book_new();
                wb.Props = {
                    Title: "Chart of Accounts",
                    CreatedDate: new Date()
                };

                // Loop through each table and convert it to a worksheet
                tables.forEach(function(tbl) {
                    var tableElement = $(tbl.id);
                    if (tableElement.length > 0) {
                        // Convert the HTML table to worksheet
                        var ws = XLSX.utils.table_to_sheet(tableElement[0]);
                        XLSX.utils.book_append_sheet(wb, ws, tbl.sheetName);
                    }
                });

                // Generate a binary XLSX file
                var wbout = XLSX.write(wb, {bookType:'xlsx',  type: 'binary'});

                // Convert binary string to array buffer
                function s2ab(s) {
                    var buf = new ArrayBuffer(s.length);
                    var view = new Uint8Array(buf);
                    for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
                    return buf;
                }

                // Create a Blob from the workbook
                var blob = new Blob([s2ab(wbout)], {type:"application/octet-stream"});

                // Create a temporary link element to trigger download
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = "charts.xlsx";
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            const addChartOfAccountBtn = document.getElementById("addChartOfAccountBtn");
            const newAccountModal = document.getElementById("newAccountModal");
            const closeModal = document.getElementById("closeModal");
            const cancelModal = document.getElementById("cancelModal");

            const accountHistoryModal = document.getElementById("accountHistoryModal");
            const closeHistoryModal = document.getElementById("closeHistoryModal");
            const closeHistoryFooterModal = document.getElementById("closeHistoryFooterModal");

            // Show newAccountModal when Add Chart of Account is clicked
            addChartOfAccountBtn.addEventListener("click", () => {
                newAccountModal.style.display = "flex";
            });

            // Close newAccountModal
            closeModal.addEventListener("click", () => {
                newAccountModal.style.display = "none";
            });
            cancelModal.addEventListener("click", () => {
                newAccountModal.style.display = "none";
            });

            // Close newAccountModal when clicking outside modal content
            window.addEventListener("click", (e) => {
                if (e.target === newAccountModal) {
                    newAccountModal.style.display = "none";
                }
            });

            // Show accountHistoryModal when any .view-btn is clicked
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const accountName = btn.getAttribute('data-account');
                    document.getElementById('accountDropdown').value = accountName;
                    accountHistoryModal.style.display = "flex";
                });
            });

            // Close accountHistoryModal
            closeHistoryModal.addEventListener("click", () => {
                accountHistoryModal.style.display = "none";
            });
            if (closeHistoryFooterModal) {
                closeHistoryFooterModal.addEventListener("click", () => {
                    accountHistoryModal.style.display = "none";
                });
            }

            // Close accountHistoryModal when clicking outside
            window.addEventListener("click", (e) => {
                if (e.target === accountHistoryModal) {
                    accountHistoryModal.style.display = "none";
                }
            });

            // Show accountHistoryModal when any .view-btn is clicked using delegated event binding
            $(document).on('click', '.view-btn', function () {
                const accountName = $(this).data('account');
                $('#accountDropdown').val(accountName); // Set the account in the dropdown
                $('#accountHistoryModal').css('display', 'flex'); // Show the modal
            });

            // Close accountHistoryModal
            $('#closeHistoryModal, #closeHistoryFooterModal').on('click', function () {
                $('#accountHistoryModal').css('display', 'none'); // Hide the modal
            });

            // Tab switching logic
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabTables = document.querySelectorAll('.tab-table');

            tabButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Remove active class from all tabs
                    tabButtons.forEach(b => b.classList.remove('active'));
                    // Add active class to clicked tab
                    btn.classList.add('active');

                    // Hide all tables
                    tabTables.forEach(table => {
                        table.classList.remove('active');
                    });

                    // Show the selected table
                    const tabName = btn.getAttribute('data-tab');
                    const activeTable = document.getElementById(`table-${tabName}`);
                    if (activeTable) {
                        activeTable.classList.add('active');
                    }
                });
            });
        });


        $(document).on('click', '.view-btn', function () {
            const account = $(this).data('account'); // Get the account value from the button

            const accountName = $(this).data('account-name');
            const accountType = $(this).data('account-type');
            const accTypeGroup = $(this).data('acc-type-group');

            // ✅ Set the modal header dynamically
            $("#ledgerAccountTitle").html(`<b>${accountName} - ${accountType} - ${accTypeGroup}</b>`);

            // Fetch ledger data via AJAX
            $.ajax({
                url: `/manual_journal/ledger/${account}`,
                method: "GET",
                success: function (response) {
                    const $modalTable = $("#financialReportTable");
                    const $modalTableBody = $("#financialReportTable tbody");
                    const $modalContent = $(".large-modal-content");

                    // ✅ Destroy existing DataTable before updating (prevents duplication issues)
                    if ($.fn.DataTable.isDataTable($modalTable)) {
                        $modalTable.DataTable().destroy();
                    }

                    $modalTableBody.empty(); // Clear any existing rows

                    if (response.length === 0) {
                        $modalTableBody.html("<tr><td colspan='6' class='text-center'>No records found</td></tr>");
                    } else {
                        // Populate the modal table with fetched data
                        response.forEach((item) => {
                            const row = `
                        <tr>
                            <td>${item.Type}</td>
                            <td>${item.Description}</td>
                            <td>${parseFloat(item.Debit || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            <td>${parseFloat(item.Credit || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            <td>${parseFloat(item.Balance || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            <td>${item.Account_Name ?? '-'}</td>
<td>${item.reconsilation_status}</td>
<td>${item.Date_Time}</td>
                        </tr>`;
                            $modalTableBody.append(row);
                        });

                        // ✅ Reinitialize DataTable after adding rows
                        $modalTable.DataTable({
                            "responsive": true,
                            "paging": true,          // Enable pagination
                            "ordering": true,        // Enable sorting
                            "info": true,            // Show table info
                            "searching": true,       // Enable search bar
                            "pageLength": 10,        // Default number of rows per page
                            "lengthMenu": [10, 25, 50, 100] // Dropdown to select number of rows
                        });
                    }

                    // ✅ Dynamically Adjust Modal Height based on DataTable rows
                    let newHeight = Math.min(response.length * 40 + 300, $(window).height() - 100);
                    $modalContent.css({ "max-height": newHeight + "px", "overflow-y": "auto" });

                    // ✅ Show the modal
                    $('#accountHistoryModal').css('display', 'flex');
                },
                error: function () {
                    Swal.fire("Error", "Failed to fetch ledger details.", "error");
                }
            });
        });





        $('#closeHistoryModal, #closeHistoryFooterModal').on('click', function () {
            $('#accountHistoryModal').css('display', 'none');
        });

        // Close modal on clicking outside
        $(window).on('click', function (e) {
            if ($(e.target).is('#accountHistoryModal')) {
                $('#accountHistoryModal').css('display', 'none');
            }
        });


    </script>
@endsection
