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

        body{
            background-color: #ffffff;
        }
    </style>

@endsection

@section('content')
    <div class="container mt-4">
        <h2>Trial Balance</h2>

        <!-- Search Section -->
        <form class="row g-3 mb-4">
            <div class="col-md-6">
                <select id="searchOption" class="form-control">
                    <option value="">Select Option</option>
                </select>
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
                    <th id="totalDebit"></th>
                    <th id="totalCredit"></th>
                </tr>
                </tfoot>
            </table>
        </div>


    </div>

    <!-- Select2 JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SheetJS (XLSX.js) for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>

    <!-- jsPDF for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

    <script>
        // Function to get the financial year range with today as end date if within the current financial year
        function getFinancialYearRange(offset, isCurrent = false) {
            const today = new Date();
            const currentYear = today.getFullYear();
            const currentMonth = today.getMonth() + 1; // getMonth is 0-indexed

            let startYear, endYear, endDate;

            // Determine financial year start
            if (currentMonth > 3) {
                startYear = currentYear - offset;
                endYear = currentYear + 1 - offset;
            } else {
                startYear = currentYear - 1 - offset;
                endYear = currentYear - offset;
            }

            // Start date is always April 1 of the starting year
            const startDate = `${startYear}-04-01`;

            // For current financial year, use today's date as the end date if within the financial year
            if (isCurrent) {
                const financialYearEnd = new Date(`${endYear}-03-31`);
                if (today <= financialYearEnd) {
                    // If today's date is before the next year's March 31, use today's date
                    const todayFormatted = today.toISOString().split('T')[0]; // Format today as YYYY-MM-DD
                    endDate = todayFormatted;
                } else {
                    // Otherwise, use March 31 of the next year
                    endDate = `${endYear}-03-31`;
                }
            } else {
                // For past financial years, end date is always March 31
                endDate = `${endYear}-03-31`;
            }

            return `${startDate} to ${endDate}`;
        }

        // Populate select options with the calculated financial years
        const searchOption = document.getElementById('searchOption');

        const currentFinancialYear = getFinancialYearRange(0, true);  // For current year, using today as end date if applicable
        const oneYearAgo = getFinancialYearRange(1);
        const twoYearsAgo = getFinancialYearRange(2);
        const threeYearsAgo = getFinancialYearRange(3);

        searchOption.innerHTML += `<option value="${currentFinancialYear}">${currentFinancialYear}</option>`;
        searchOption.innerHTML += `<option value="${oneYearAgo}">${oneYearAgo}</option>`;
        searchOption.innerHTML += `<option value="${twoYearsAgo}">${twoYearsAgo}</option>`;
        searchOption.innerHTML += `<option value="${threeYearsAgo}">${threeYearsAgo}</option>`;
    </script>
    <script>
        $(document).ready(function () {

            // Function to download table data as Excel
            $('#btnExportExcel').click(function () {
                var wb = XLSX.utils.table_to_book(document.getElementById('trialTable'), { sheet: "Trial Balance" });
                XLSX.writeFile(wb, 'Trial_Balance.xlsx');
            });




            $('#searchOption').change(function () {
                var selectedOption = $(this).val();
                $.ajax({
                    url: '/get-trialBalance-data',  // Adjust the URL as needed
                    type: 'GET',
                    data: {
                        searchOption: selectedOption,
                    },
                    success: function (data) {
                        console.log(data);  // For debugging

                        // Select the tbody where you want to populate the data
                        var trialBalanceTable = $('#trialBalanceTable');
                        var totalDebit = 0;
                        var totalCredit = 0;

                        trialBalanceTable.empty();  // Clear existing table data before populating

                        // Loop through the returned data and append rows
                        data.forEach(function (item) {
                            var row = `
                        <tr>
                            <td>${item.account}</td>
                            <td>${formatNumber(item.debit.toFixed(2))}</td>
                            <td>${formatNumber(item.credit.toFixed(2))}</td>
                        </tr>
                    `;

                            // Append each row to the table body
                            trialBalanceTable.append(row);

                            // Calculate total debit and credit
                            totalDebit += parseFloat(item.debit);
                            totalCredit += parseFloat(item.credit);
                        });

                        // Update totals in the footer
                        $('#totalDebit').text(formatNumber(totalDebit.toFixed(2)));
                        $('#totalCredit').text(formatNumber(totalCredit.toFixed(2)));
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);  // Handle errors
                    }
                });
            });


            // Function to format numbers with commas
            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

        });

    </script>

@endsection
