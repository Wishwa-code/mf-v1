@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .table {
            margin-top: 20px;
        }

        .table thead th {
            background-color: #e9ecef;
            color: #333;
            border: none;
        }

        .table tbody tr td {
            vertical-align: middle;
        }

        .table .fw-bold {
            font-weight: 700;
        }

        .table .bg-light {
            background-color: #f1f1f1;
        }

        .table-bordered {
            border: none;
        }

        .table-bordered td,
        .table-bordered th {
            border: none;
        }

        .border-bottom-light {
            border-bottom: 1px solid #dee2e6;
        }

        .border-top-bottom-dark {
            border-top: 1px solid #adb5bd;
            border-bottom: 1px solid #adb5bd;
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .mt-3 {
            margin-top: 1rem !important;
        }

        .mb-4 {
            margin-bottom: 1.5rem !important;
        }

        .mt-4 {
            margin-top: 1.5rem !important;
        }

        .d-none {
            display: none !important; /* Ensure that the class is applied with !important to override any other styles */
        }

    </style>
@endsection

@section('content')
    <div class="container mt-4">
        <h2>Cash Flow Monthly Overview</h2>
        <span style="color: #a19595">"This section provides a detailed view of the cash inflows and outflows on a monthly basis, helping you track your organization's financial health and liquidity each month. You can select the desired month to view the cash flow for that period."</span>
        <br><br>
        <!-- Search Section -->
        <form class="row g-3 mb-4">
            <div class="col-md-3" hidden>
                <input type="date" id="startDate" class="form-control d-none" placeholder="Start Date">
            </div>
            <div class="col-md-3" hidden>
                <input type="date" id="endDate" class="form-control d-none" placeholder="End Date">
            </div>
            <div class="col-md-6">
                <select id="searchOption" class="form-control">
                    <option value="">Select Option</option>
                    <option value="oneYear">Current Month</option>
                    <option value="twoYear">Two Months</option>
                    <option value="threeYear">Three Months</option>
                    <option value="fourYear">Four Months</option>
                </select>
            </div>
        </form>

        <!-- Table Section -->
        <div class="table-responsive">
            <table id="cashFlowTable" class="table table-bordered">
                <thead>
                <tr>
                    <th colspan="2" class="text-start fw-bold">Cash Flow Statement</th>
                    <th class="text-end" id="headerOne"></th>
                    <th class="text-end d-none" id="headerTwo"></th>
                    <th class="text-end d-none" id="headerThree"></th>
                    <th class="text-end d-none" id="headerFour"></th>
                    <th class="text-end d-none" id="headerFive"></th>
                </tr>
                </thead>
                <tbody>
                <!-- Rows for Receipts -->
                <tr class="fw-bold text-success">
                    <td colspan="6" class="text-start text-success">Receipts</td>
                </tr>
                <tr>
                    <td colspan="2" class="text-start">Loan Principal Repayments</td>
                    <td class="text-end" data-id="loan_capital_repayments_1"></td>
                    <td class="text-end d-none" data-id="loan_capital_repayments_2"></td>
                    <td class="text-end d-none" data-id="loan_capital_repayments_3"></td>
                    <td class="text-end d-none" data-id="loan_capital_repayments_4"></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-start">Loan Interest Repayments</td>
                    <td class="text-end" data-id="loan_interest_repayments_1"></td>
                    <td class="text-end d-none" data-id="loan_interest_repayments_2"></td>
                    <td class="text-end d-none" data-id="loan_interest_repayments_3"></td>
                    <td class="text-end d-none" data-id="loan_interest_repayments_4"></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-start">Loan Penalty Repayments</td>
                    <td class="text-end" data-id="loan_panalty_repayments_1"></td>
                    <td class="text-end d-none" data-id="loan_panalty_repayments_2"></td>
                    <td class="text-end d-none" data-id="loan_panalty_repayments_3"></td>
                    <td class="text-end d-none" data-id="loan_panalty_repayments_4"></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-start">Savings Deposits</td>
                    <td class="text-end" data-id="savings_deposits_1"></td>
                    <td class="text-end d-none" data-id="savings_deposits_2"></td>
                    <td class="text-end d-none" data-id="savings_deposits_3"></td>
                    <td class="text-end d-none" data-id="savings_deposits_4"></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-start">Other Income</td>
                    <td class="text-end" data-id="other_income_1"></td>
                    <td class="text-end d-none" data-id="other_income_2"></td>
                    <td class="text-end d-none" data-id="other_income_3"></td>
                    <td class="text-end d-none" data-id="other_income_4"></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-start">Assets Sale</td>
                    <td class="text-end" data-id="assets_sale_1"></td>
                    <td class="text-end d-none" data-id="assets_sale_2"></td>
                    <td class="text-end d-none" data-id="assets_sale_3"></td>
                    <td class="text-end d-none" data-id="assets_sale_4"></td>
                </tr>

                <tr class="fw-bold border-top-bottom-dark">
                    <td colspan="2" class="text-start text-success">Total Receipts (A)</td>
                    <td class="text-end" data-id="total_receipts_1"></td>
                    <td class="text-end d-none" data-id="total_receipts_2"></td>
                    <td class="text-end d-none" data-id="total_receipts_3"></td>
                    <td class="text-end d-none" data-id="total_receipts_4"></td>
                </tr>
                <!-- Rows for Payments -->
                <tr class="fw-bold text-danger border-top-bottom-dark">
                    <td colspan="6" class="text-start text-danger">Payments</td>
                </tr>
                <tr>
                    <td colspan="2" class="text-start">Loans Released (Principal)</td>
                    <td class="text-end" data-id="loans_released_1"></td>
                    <td class="text-end d-none" data-id="loans_released_2"></td>
                    <td class="text-end d-none" data-id="loans_released_3"></td>
                    <td class="text-end d-none" data-id="loans_released_4"></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-start">Asset Purchased</td>
                    <td class="text-end" data-id="asset_purchased_1"></td>
                    <td class="text-end d-none" data-id="asset_purchased_2"></td>
                    <td class="text-end d-none" data-id="asset_purchased_3"></td>
                    <td class="text-end d-none" data-id="asset_purchased_4"></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-start">Other Expenses</td>
                    <td class="text-end" data-id="other_expenses_1"></td>
                    <td class="text-end d-none" data-id="other_expenses_2"></td>
                    <td class="text-end d-none" data-id="other_expenses_3"></td>
                    <td class="text-end d-none" data-id="other_expenses_4"></td>
                </tr>
                <tr class="fw-bold border-top-bottom-dark">
                    <td colspan="2" class="text-start text-danger">Total Payments (B)</td>
                    <td class="text-end"  data-id="total_payments_1"></td>
                    <td class="text-end d-none" data-id="total_payments_2"></td>
                    <td class="text-end d-none" data-id="total_payments_3"></td>
                    <td class="text-end d-none" data-id="total_payments_4"></td>
                </tr>
                <!-- Final Calculations -->
                <tr class="fw-bold border-top-bottom-dark bg-light">
                    <td colspan="2" class="text-start">Total Cash Balance (A) - (B)</td>
                    <td class="text-end" data-id="total_cash_balance_1"></td>
                    <td class="text-end d-none" data-id="total_cash_balance_2"></td>
                    <td class="text-end d-none" data-id="total_cash_balance_3"></td>
                    <td class="text-end d-none" data-id="total_cash_balance_4"></td>
                </tr>
                <tr class="fw-bold border-bottom-light">
                    <td colspan="2" class="text-start">Previous Balance</td>
                    <td class="text-end" data-id="previous_balance_1"></td>
                    <td class="text-end d-none" data-id="previous_balance_2"></td>
                    <td class="text-end d-none" data-id="previous_balance_3"></td>
                    <td class="text-end d-none" data-id="previous_balance_4"></td>
                </tr>
                <tr class="fw-bold">
                    <td colspan="2" class="text-start">Total Balance</td>
                    <td class="text-end" data-id="total_balance_1"></td>
                    <td class="text-end d-none" data-id="total_balance_2"></td>
                    <td class="text-end d-none" data-id="total_balance_3"></td>
                    <td class="text-end d-none" data-id="total_balance_4"></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Select2 JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#searchOption').change(function() {
                var selectedOption = $(this).val();
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();
                console.log(selectedOption);
                resetTable();

                if (selectedOption === 'dates') {
                    $('#startDate, #endDate').removeClass('d-none');
                } else {
                    $('#startDate, #endDate').addClass('d-none');
                }

                if (selectedOption !== '') {
                    $.ajax({
                        url: '/get-cashflow-data-monthly',
                        type: 'GET',
                        data: {
                            searchOption: selectedOption,
                            startDate: startDate,
                            endDate: endDate
                        },
                        success: function(data) {
                            console.log(data); // Log the data for debugging
                            populateTable(data, selectedOption);
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText); // Error handling
                        }
                    });
                }
            });

            function resetTable() {
                $('#headerOne, #headerTwo, #headerThree, #headerFour, #headerFive').addClass('d-none');
                $('td[data-id]').html('0'); // Reset the cells
            }

            function populateTable(data, selectedOption) {
                console.log(`Selected option: ${selectedOption}`); // Debug log to check selected option

                if (selectedOption === 'oneYear') {
                    $('#headerOne, #headerTwo').removeClass('d-none');
                    $('#headerThree, #headerFour, #headerFive').addClass('d-none');
                    $("#headerOne").text(data['date_1']+"-"+data['date_2']);
                } else if (selectedOption === 'twoYear') {
                    $('#headerOne, #headerTwo, #headerThree').removeClass('d-none');
                    $('#headerFour, #headerFive').addClass('d-none');
                    $("#headerOne").text(data['date_1']+"-"+data['date_2']);
                    $("#headerTwo").text(data['date_3']+"-"+data['date_4']);
                } else if (selectedOption === 'threeYear') {
                    $('#headerOne, #headerTwo, #headerThree, #headerFour').removeClass('d-none');
                    $('#headerFive').addClass('d-none');
                    $("#headerOne").text(data['date_1']+"-"+data['date_2']);
                    $("#headerTwo").text(data['date_3']+"-"+data['date_4']);
                    $("#headerThree").text(data['date_5']+"-"+data['date_6']);
                } else if (selectedOption === 'fourYear') {
                    $('#headerOne, #headerTwo, #headerThree, #headerFour, #headerFive').removeClass('d-none');
                    $("#headerOne").text(data['date_1']+"-"+data['date_2']);
                    $("#headerTwo").text(data['date_3']+"-"+data['date_4']);
                    $("#headerThree").text(data['date_5']+"-"+data['date_6']);
                    $("#headerFour").text(data['date_7']+"-"+data['date_8']);
                }

                // Update relevant table cells with data
                updateCells(data, ['loan_capital_repayments_1', 'loan_capital_repayments_2', 'loan_capital_repayments_3', 'loan_capital_repayments_4']);
                updateCells(data, ['loan_interest_repayments_1', 'loan_interest_repayments_2', 'loan_interest_repayments_3', 'loan_interest_repayments_4']);
                updateCells(data, ['loan_panalty_repayments_1', 'loan_panalty_repayments_2', 'loan_panalty_repayments_3', 'loan_panalty_repayments_4']);
                updateCells(data, ['savings_deposits_1', 'savings_deposits_2', 'savings_deposits_3', 'savings_deposits_4']);
                updateCells(data, ['other_income_1', 'other_income_2', 'other_income_3', 'other_income_4']);
                updateCells(data, ['assets_sale_1', 'assets_sale_2', 'assets_sale_3', 'assets_sale_4']);
                updateCells(data, ['total_receipts_1', 'total_receipts_2', 'total_receipts_3', 'total_receipts_4']);
                updateCells(data, ['loans_released_1', 'loans_released_2', 'loans_released_3', 'loans_released_4']);
                updateCells(data, ['asset_purchased_1', 'asset_purchased_2', 'asset_purchased_3', 'asset_purchased_4']);
                updateCells(data, ['other_expenses_1', 'other_expenses_2', 'other_expenses_3', 'other_expenses_4']);
                updateCells(data, ['total_payments_1', 'total_payments_2', 'total_payments_3', 'total_payments_4']);
                updateCells(data, ['total_cash_balance_1', 'total_cash_balance_2', 'total_cash_balance_3', 'total_cash_balance_4']);
                updateCells(data, ['previous_balance_1', 'previous_balance_2', 'previous_balance_3', 'previous_balance_4']);
                updateCells(data, ['total_balance_1', 'total_balance_2', 'total_balance_3', 'total_balance_4']);
            }

            function updateCells(data, fields) {
                fields.forEach(function(field) {
                    let cellValue = data[field] !== undefined ? data[field] : '';  // Check if the field exists in data
                    console.log(`Updating ${field} with value: ${cellValue}`); // Debug log for each field
                    $('td[data-id="' + field + '"]').html(cellValue).removeClass('d-none'); // Ensure cell is visible
                });
            }
        });
    </script>

@endsection
