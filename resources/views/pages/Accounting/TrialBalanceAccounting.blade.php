@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <!-- jQuery (must be before DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>


    <style>
        .table thead th {
            background-color: #007bff;
            color: #000000;
            text-align: center;
        }

        .table tbody td {
            text-align: center;
        }

        .table tfoot th {
            background-color: #0e0e0e;
            font-weight: bold;
            text-align: center;
        }

        .table tfoot th:first-child {
            text-align: left;
        }

        .table th,
        .table td {
            padding: 12px;
        }

        body {
            background-color: #ffffff;
        }
    </style>
    <style>
        .modal-lg {
            max-width: 70%;  /* Set modal to 90% of the screen width */
        }
    </style>
@endsection

@section('content')
    <div class="container mt-4">
        <h2>Trial Balance Overview</h2>
        <span style="color: #a19595">"The Trial Balance is a financial report used to ensure that the debits and credits in your accounting system are in balance. This report lists all accounts with their respective debit and credit balances, helping you identify discrepancies before preparing financial statements."</span>
        <br><br>

        <!-- Search Section -->
        <form class="row g-3 mb-4 align-items-end">
            <div class="col-md-3">
                <label for="date_from" class="form-label">Date From:</label>
                <input type="date" class="form-control" name="date_from" id="date_from" value="{{date('Y-m-d')}}">
            </div>

            <div class="col-md-3">
                <label for="date_to" class="form-label">Date To:</label>
                <input type="date" class="form-control" name="date_to" id="date_to" value="{{date('Y-m-d')}}">
            </div>

            <div class="col-md-2">
                <button type="button" onclick="search_trial()" class="btn btn-primary w-100">Search</button>
            </div>
            <div class="col-md-2">
                <button class="btn btn-danger w-100" type="button" onclick="LogReport();">Full Log</button>
            </div>
            <div class="col-md-2">
                <button id="btnExportExcel" class="btn btn-success w-100">Download Excel</button>
            </div>

        </form>


        <div class="mb-3">
        </div>

        <!-- Table Section -->
        <div class="table-responsive">
            <table class="table table-bordered" id="trialTable">
                <thead class="thead-light">
                <tr>
                    <th style="text-align: left;">Account Name</th>
                    <th style="text-align: left;">Type</th>
                    <th style="text-align: right;">Debit</th>
                    <th style="text-align: right;">Credit</th>
                </tr>
                </thead>
                <tbody id="trialBalanceTable">
                <!-- Table rows will be dynamically inserted here -->
                </tbody>
                <tfoot>
                <tr>
                    <th>Total</th>
                    <th></th>
                    <th id="totalDebit" style="text-align: right"></th>
                    <th id="totalCredit" style="text-align: right"></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>


    <!-- Modal for Financial Report -->
    <div class="modal fade" id="financialReportModal" tabindex="-1" role="dialog" aria-labelledby="financialReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="financialReportModalLabel">Financial Report</h5>
                </div>
                <div class="modal-body">
                    <table id="financialReportTable" class="table table-striped">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Debit Amount</th>
                            <th>Credit Amount</th>
                            <th>Balance</th>
                            <th>Created At</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Data will be dynamically populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal for Financial Report -->
    <div class="modal fade" id="financialFullReportModal" tabindex="-1" role="dialog" aria-labelledby="financialReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="financialFullReportModalLabel">Financial Report</h5>
                </div>
                <div class="modal-body">
                    <table id="financialFullReportTable" class="table table-striped">
                        <thead>
                        <tr>
                            <th>Account</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Debit Amount</th>
                            <th>Credit Amount</th>
                            <th>Balance</th>
                            <th>Created At</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Data will be dynamically populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>





    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
    <script>
        $(document).ready(function () {
            // Function to download table data as Excel
            $('#btnExportExcel').click(function () {
                var wb = XLSX.utils.table_to_book(document.getElementById('trialTable'), { sheet: "Trial Balance" });
                XLSX.writeFile(wb, 'Trial_Balance.xlsx');
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.getScript("https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js", function() {
                console.log("✅ DataTables manually loaded.");
            });


            // Initialize DataTable with pagination
            $('#financialReportTable').DataTable({
                paging: true,             // Enables pagination
                lengthChange: true,       // Allows the user to change the number of records per page
                searching: true,          // Enables search functionality
                ordering: true,           // Enables column sorting
                info: true,               // Shows table information
                autoWidth: false,         // Disables automatic column width adjustment
                responsive: true          // Makes the table responsive
            });

        });
        // Function to format numbers with commas
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Handle search option change (date range)
        function search_trial() {
            var date_from = $("#date_from").val();
            var date_to = $("#date_to").val();

            // Send AJAX request to fetch the trial balance data based on date range
            $.ajax({
                url: '/get-account-trialBalance-data',
                type: 'GET',
                data: {
                    date_from: date_from,
                    date_to: date_to
                },
                success: function (data) {
                    console.log(data);
                    var trialBalanceTable = $('#trialBalanceTable');
                    var totalDebit = 0;
                    var totalCredit = 0;

                    // Clear existing table data before populating
                    trialBalanceTable.empty();

                    if (data.length === 0) {
                        // If no data is found
                        trialBalanceTable.append(`
                    <tr>
                        <td colspan="3" style="text-align: center;">No records found</td>
                    </tr>
                `);
                    } else {
                        // Populate table rows with the fetched data
                        data.forEach(function (item) {
                            var accName = item.acc_name || 'N/A';
                            var type = item.type || 'N/A';
                            var acc_type = item.acc_type || 'N/A';
                            var totalDebitAmount = item.total_debit ? parseFloat(item.total_debit).toFixed(2) : '0.00';
                            var totalCreditAmount = item.total_credit ? parseFloat(item.total_credit).toFixed(2) : '0.00';

                            // Only add rows where either debit or credit is non-zero
                            if (parseFloat(totalDebitAmount) !== 0 || parseFloat(totalCreditAmount) !== 0) {
                                // Create a table row for each account
                                var row = `
                        <tr class="trialBalanceRow" data-account-id="${item.account_id}">
                            <td style="text-align: left;">${accName}</td>

                            <td style="text-align: left;">${type} <strong>(${acc_type})</strong></td>
                            <td style="text-align: right;">${formatNumber(totalDebitAmount)}</td>
                            <td style="text-align: right;">${formatNumber(totalCreditAmount)}</td>
                        </tr>
                    `;
                                trialBalanceTable.append(row);

                                // Add totals for debit and credit
                                totalDebit += parseFloat(totalDebitAmount);
                                totalCredit += parseFloat(totalCreditAmount);
                            }
                        });
                    }

                    // Update the totals for Debit and Credit
                    $('#totalDebit').text(formatNumber(totalDebit.toFixed(2)));
                    $('#totalCredit').text(formatNumber(totalCredit.toFixed(2)));

                    // Add row click event to open the modal with the financial report
                    $('.trialBalanceRow').on('click', function () {
                        var accountId = $(this).data('account-id');

                        // Fetch the financial report for the clicked account
                        fetchFinancialReport(accountId);
                        $('#financialReportModal').modal('show');
                    });
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                }
            });
        }


        function fetchFinancialReport(accountId) {
            var date_from = $("#date_from").val();
            var date_to = $("#date_to").val();

            $.ajax({
                url: '/get-financial-report',
                type: 'POST',
                data: {
                    account_id: accountId,
                    date_from: date_from,
                    date_to: date_to
                },
                success: function (data) {

                    console.log(data);

                    var financialReportTable = $('#financialReportTable tbody');

                    // Get account details from the UI
                    var accountName = $(`tr[data-account-id='${accountId}'] td:first`).text();
                    var accountType = $(`tr[data-account-id='${accountId}'] td:nth-child(2)`).text();

                    // Update modal title
                    $('#financialReportModalLabel').html(`${accountName} | <strong>${accountType}</strong>`);

                    // Clear existing data
                    financialReportTable.empty();

                    // Filter data for the selected account only
                    var filteredData = data.filter(item => item.Bank_Account_Id == accountId);

                    filteredData.forEach(function (item) {
                        var row = `
                <tr>
                    <td>${item.Type || 'N/A'}</td>
                    <td>${item.Description || 'N/A'}</td>
                    <td>${formatNumber(parseFloat(item.Debit).toFixed(2) || 0)}</td>
                    <td>${formatNumber(parseFloat(item.Credit).toFixed(2) || 0)}</td>
                    <td>${formatNumber(parseFloat(item.Balance).toFixed(2) || 0)}</td>
                    <td>${item.Date_Time || 'N/A'}</td>
                </tr>
            `;
                        financialReportTable.append(row);
                    });
                }
                ,
                error: function (xhr) {
                    console.error("AJAX error:", xhr.responseText);
                    alert("Error fetching financial report. Check console for details.");
                }
            });
        }




        function LogReport() {


            var date_from = $("#date_from").val();
            var date_to = $("#date_to").val();

            $.ajax({
                url: '/get-financial-report',
                type: 'POST',
                data: {
                    date_from: date_from,
                    date_to: date_to
                },
                success: function (data) {

                    var financialReportTable = $('#financialFullReportTable tbody');

                    // Update modal title
                    $('#financialFullReportModalLabel').html(`<strong>Full Log Report</strong>`);

                    // Clear existing data
                    financialReportTable.empty();

                    data.forEach(function (item) {
                        var row = `
                <tr>
                    <td>${item.Account_Name}-${item.Bank_Name}(${item.Account_No})</td>
                    <td>${item.Type || 'N/A'}</td>
                    <td>${item.Description || 'N/A'}</td>
                    <td>${formatNumber(parseFloat(item.Debit).toFixed(2) || 0)}</td>
                    <td>${formatNumber(parseFloat(item.Credit).toFixed(2) || 0)}</td>
                    <td>${formatNumber(parseFloat(item.Balance).toFixed(2) || 0)}</td>
                    <td>${item.Date_Time || 'N/A'}</td>
                </tr>
            `;
                        financialReportTable.append(row);
                    });
                    $('#financialFullReportModal').modal('show');
                }
                ,
                error: function (xhr) {
                    console.error("AJAX error:", xhr.responseText);
                    alert("Error fetching financial report. Check console for details.");
                }
            });
        }



    </script>
@endsection
