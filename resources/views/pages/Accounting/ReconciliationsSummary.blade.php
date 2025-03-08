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
        /* Hover effect for better UX */
        .clickable-row:hover {
            background-color: #f6f1f1 !important; /* Light green */
            cursor: pointer;
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <div class="container mt-4">
        <h3 class="text-center">Reconciliation Summary</h3>
        <h5 class="text-center">Bank 1, Period Ending <span id="summaryEndDate">31/01/2021</span></h5>

        <table class="table table-bordered mt-3">
            <tbody>
            <tr>
                <th>Beginning Balance</th>
                <td class="text-end" id="beginningBalance">945,800.00</td>
            </tr>
            <tr class="table-secondary">
                <th colspan="2">Cleared Transactions</th>
            </tr>
            <tr>
                <td>Checks and Payments - <span id="clearedChecksCount">14</span> items</td>
                <td class="text-end" id="clearedChecksTotal">-287,200.00</td>
            </tr>
            <tr>
                <td>Deposits and Credits - <span id="clearedDepositsCount">3</span> items</td>
                <td class="text-end" id="clearedDepositsTotal">1,003,500.00</td>
            </tr>
            <tr>
                <th>Total Cleared Transactions</th>
                <td class="text-end fw-bold" id="totalClearedTransactions">716,300.00</td>
            </tr>
            <tr>
                <th>Cleared Balance</th>
                <td class="text-end fw-bold text-primary" id="clearedBalance">1,662,100.00</td>
            </tr>
            <tr class="table-secondary">
                <th colspan="2">Uncleared Transactions</th>
            </tr>
            <tr>
                <td>Checks and Payments - <span id="unclearedChecksCount">4</span> items</td>
                <td class="text-end" id="unclearedChecksTotal">-120,000.00</td>
            </tr>
            <tr>
                <td>Deposits and Credits - <span id="unclearedDepositsCount">1</span> item</td>
                <td class="text-end" id="unclearedDepositsTotal">5,600.00</td>
            </tr>
            <tr>
                <th>Total Uncleared Transactions</th>
                <td class="text-end fw-bold text-danger" id="totalUnclearedTransactions">-114,400.00</td>
            </tr>
            <tr>
                <th>Register Balance as of <span id="registerBalanceDate">31/01/2021</span></th>
                <td class="text-end fw-bold text-primary" id="registerBalance">1,547,700.00</td>
            </tr>
            <tr class="table-secondary">
                <th colspan="2">New Transactions</th>
            </tr>
            <tr>
                <td>Checks and Payments - <span id="newChecksCount">14</span> items</td>
                <td class="text-end" id="newChecksTotal">-103,375.00</td>
            </tr>
            <tr>
                <td>Deposits and Credits - <span id="newDepositsCount">2</span> items</td>
                <td class="text-end" id="newDepositsTotal">4,707.00</td>
            </tr>
            <tr>
                <th>Total New Transactions</th>
                <td class="text-end fw-bold text-warning" id="totalNewTransactions">-98,668.00</td>
            </tr>
            <tr>
                <th>Ending Balance</th>
                <td class="text-end fw-bold text-success" id="endingBalance">1,449,032.00</td>
            </tr>
            </tbody>
        </table>
    </div>



@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
@endsection
