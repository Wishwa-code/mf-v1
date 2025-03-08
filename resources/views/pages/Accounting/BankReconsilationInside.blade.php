@extends('layout.admin')

@section('head')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        .container {
            max-width: 95%;
            margin: auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
            font-size: 1.8rem;
            font-weight: 700;
            color: #007bff;
            margin-bottom: 20px;
        }

        .period {
            font-size: 16px;
            font-weight: bold;
            text-align: left;
            margin-bottom: 15px;
        }

        .reconciliation-wrapper {
            display: flex;
            gap: 20px;
        }

        .table-container {
            background: #ffffff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            flex: 1;
        }

        .table-container h4 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        table th {
            background: #f5f5f5;
            color: #000;
            padding: 10px;
            text-align: center;
            font-weight: 600;
            border: 1px solid #dee2e6;
        }

        table td {
            padding: 8px;
            text-align: center;
            border: 1px solid #dee2e6;
            background: #ffffff;
        }

        table tbody tr:hover {
            background-color: #e9f5ff;
            cursor: pointer;
        }

        .highlight {
            background-color: #4CAF50 !important;
            color: white;
        }

        .summary-section {
            background: #ffffff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .summary-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .summary-buttons button {
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            margin-right: 5px;
        }

        .btn-mark {
            background-color: #007bff;
            color: white;
        }

        .btn-unmark {
            background-color: #dc3545;
            color: white;
        }

        .summary-values {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .summary-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 10px;
        }

        /* Left Side - Beginning Balance */
        .summary-left {
            flex: 1;
            padding-right: 20px;
        }

        /* Right Side - Other Balances */
        .summary-right {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
        }

        .btn {
            padding: 10px 15px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }
        .selected-row {
            background-color: #d4edda !important; /* Light Green */
            transition: background-color 0.3s ease-in-out;
        }


    </style>
@endsection

@section('content')
    <div class="container">
        <h2>Bank Reconciliation</h2>
        <div class="period">For period: {{$reconciliation->date}}</div>

        <div class="reconciliation-wrapper">
            <!-- Checks and Payments Table (Credits) -->
            <div class="table-container">
                <h4>Checks and Payments</h4>
                <table id="creditTable">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Date</th>
                        <th>CHK#</th>
                        <th>Payee</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($bank_log as $transaction)
                        @if($transaction->Credit > 0)  <!-- Filter Credit Transactions -->
                        <tr>
                            <td><input type="checkbox" class="mark-transaction"></td>
                            <td>{{ $transaction->Date_Time }}</td>
                            <td>{{ $transaction->Description }}</td>
                            <td>{{ $transaction->Type }}</td>
                            <td>{{ number_format($transaction->Credit, 2) }}</td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="table-container">
                <h4>Deposits and Other Credits</h4>
                <table id="debitTable">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Date</th>
                        <th>Memo</th>
                        <th>Type</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($bank_log as $transaction)
                        @if($transaction->Debit > 0)  <!-- Filter Debit Transactions -->
                        <tr>
                            <td><input type="checkbox" class="mark-transaction"></td>
                            <td>{{ $transaction->Date_Time }}</td>
                            <td>{{ $transaction->Description }}</td>
                            <td>{{ $transaction->Type }}</td>
                            <td>{{ number_format($transaction->Debit, 2) }}</td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-header">
                <div class="summary-buttons">
                    <button id="markAll" class="btn btn-mark">Mark All</button>
                    <button id="unmarkAll" class="btn btn-unmark">Unmark All</button>
                </div>
            </div>

            <div class="summary-content">
                <div class="summary-left">
                    <div class="summary-values"><span>Beginning Balance:</span> <span id="beginningBalance">0.00</span></div>
                </div>

                <div class="summary-right">
                    <div class="summary-values"><span>Service Charge:</span> <span id="serviceCharge">0.00</span></div>
                    <div class="summary-values"><span>Interest Earned:</span> <span id="interestEarned">0.00</span></div>
                    <div class="summary-values"><span>Ending Balance:</span> <span id="endingBalance">{{number_format($reconciliation->balance),2,'.',','}}</span></div>
                    <div class="summary-values"><span>Cleared Balance:</span> <span id="clearedBalance">0.00</span></div>
                    <div class="summary-values"><span>Difference:</span> <span id="difference">0.00</span></div>
                </div>
            </div>

            <div class="action-buttons">
                <button class="btn btn-primary" id="reconcileNow">Reconcile Now</button>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        $(document).ready(function () {
            $(".mark-transaction").change(function () {
                let row = $(this).closest("tr");

                if (this.checked) {
                    row.addClass("selected-row"); // Add highlight color when checked
                } else {
                    row.removeClass("selected-row"); // Remove highlight color when unchecked
                }

                updateSummary(); // Call update function on check/uncheck
            });

            $("#markAll").click(function () {
                $(".mark-transaction").prop("checked", true).change();
            });

            $("#unmarkAll").click(function () {
                $(".mark-transaction").prop("checked", false).change();
            });

            function updateSummary() {
                let beginningBalance = 0.00; // Sample starting balance
                let clearedBalance = beginningBalance;

                $(".mark-transaction:checked").each(function () {
                    let amount = parseFloat($(this).closest("tr").find("td:last-child").text().replace(/,/g, '')) || 0;
                    clearedBalance += amount;
                });

                $("#clearedBalance").text(clearedBalance.toFixed(2)); // Update cleared balance
                $("#difference").text((0.00 - clearedBalance).toFixed(2)); // Update difference
            }
        });

    </script>
@endsection
