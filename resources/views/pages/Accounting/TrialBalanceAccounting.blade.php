@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            max-width: 90%;  /* Set modal to 90% of the screen width */
        }
    </style>
@endsection

@section('content')
    <div class="container mt-4">
        <h2>Trial Balance</h2>

        <!-- Search Section -->
        <form class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="date-range">
                    <input type="date" class="form-control" onchange="search_trial()" name="date_from" id="date_from" value="{{date('Y-m-d')}}"> to
                    <input type="date" class="form-control" onchange="search_trial()"  name="date_to" id="date_to" value="{{date('Y-m-d')}}">
                </div>
            </div>
        </form>

        <div class="mb-3">
            <button id="btnExportExcel" class="btn btn-success">Download Excel</button>
        </div>

        <!-- Table Section -->
        <div class="table-responsive">
            <table class="table table-bordered" id="trialTable">
                <thead class="thead-light">
                <tr>
                    <th>Account Name</th>
                    <th>Type</th>
                    <th>Debit</th>
                    <th>Credit</th>
                </tr>
                </thead>
                <tbody id="trialBalanceTable">
                <!-- Table rows will be dynamically inserted here -->
                </tbody>
                <tfoot>
                <tr>
                    <th>Total</th>
                    <th></th>
                    <th id="totalDebit"></th>
                    <th id="totalCredit"></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>


    <!-- Modal for Financial Report -->
    <div class="modal fade" id="financialReportModal" tabindex="-1" role="dialog" aria-labelledby="financialReportModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="financialReportModalLabel">Log Report</h5>
                </div>
                <div class="modal-body">
                    <table id="financialReportTable" class="table">
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


    <!-- Select2 JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SheetJS (XLSX.js) for Excel export -->
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


            // Function to format numbers with commas

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
                            var totalDebitAmount = item.total_debit ? parseFloat(item.total_debit).toFixed(2) : '0.00';
                            var totalCreditAmount = item.total_credit ? parseFloat(item.total_credit).toFixed(2) : '0.00';

                            // Create a table row for each account
                            var row = `
                        <tr class="trialBalanceRow" data-account-id="${item.account_id}">
                            <td>${accName}</td>
                            <td>${type}</td>
                            <td>${formatNumber(totalDebitAmount)}</td>
                            <td>${formatNumber(totalCreditAmount)}</td>
                        </tr>
                    `;
                            trialBalanceTable.append(row);

                            // Add totals for debit and credit
                            totalDebit += parseFloat(totalDebitAmount);
                            totalCredit += parseFloat(totalCreditAmount);
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

        // Function to fetch and display the financial report data
        function fetchFinancialReport(accountId) {
            var date_from = $("#date_from").val();
            var date_to = $("#date_to").val();

            $.ajax({
                url: '/get-financial-report',  // Replace with the appropriate API endpoint
                type: 'POST',
                data: {
                    account_id: accountId,
                    date_from:date_from,
                    date_to:date_to
                },
                success: function (data) {
                    console.log(data);  // For debugging
                    var financialReportTable = $('#financialReportTable tbody');
                    financialReportTable.empty();  // Clear existing data

                    if (data.length === 0) {
                        financialReportTable.append(`
                    <tr>
                        <td colspan="6" style="text-align: center;">No financial report data found</td>
                    </tr>
                `);
                    } else {
                        // Populate the modal with the financial report data
                        data.forEach(function (item) {
                            var row = `
                        <tr>
                            <td>${item.Type || 'N/A'}</td>
                            <td>${item.Description || 'N/A'}</td>
                            <td>${formatNumber(item.Debit || 0)}</td>
                            <td>${formatNumber(item.Credit || 0)}</td>
                            <td>${formatNumber(item.Balance || 0)}</td>
                            <td>${item.Date_Time || 'N/A'}</td>
                        </tr>
                    `;
                            financialReportTable.append(row);
                        });
                    }

                    // Show the modal
                    $('#financialReportModal').modal('show');
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                }
            });
        }


    </script>
@endsection
