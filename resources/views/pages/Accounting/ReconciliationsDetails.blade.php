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
        <h3 class="text-center">Reconciliation Detail</h3>
        <h5 class="text-center">Bank 1, Period Ending <span id="detailEndDate">31/01/2021</span></h5>

        <table class="table table-bordered mt-3">
            <thead class="table-dark">
            <tr>
                <th>Type</th>
                <th>Date</th>
                <th>Num</th>
                <th>Name</th>
                <th>Clr</th>
                <th>Amount</th>
                <th>Balance</th>
            </tr>
            </thead>
            <tbody id="detailTableBody">
            <tr>
                <td>Check</td>
                <td>18/11/2020</td>
                <td>5415</td>
                <td>Luck Furnitures</td>
                <td>✔</td>
                <td class="text-end">-40,000.00</td>
                <td class="text-end">-40,000.00</td>
            </tr>
            <tr>
                <td>Check</td>
                <td>02/01/2021</td>
                <td>5415</td>
                <td>Ceylon Electricity Board</td>
                <td>✔</td>
                <td class="text-end">-2,500.00</td>
                <td class="text-end">-42,500.00</td>
            </tr>
            <tr>
                <td>Deposit</td>
                <td>01/01/2021</td>
                <td>—</td>
                <td>—</td>
                <td>✔</td>
                <td class="text-end">1,000,000.00</td>
                <td class="text-end">1,000,000.00</td>
            </tr>
            <tr>
                <td>Deposit</td>
                <td>09/01/2021</td>
                <td>—</td>
                <td>—</td>
                <td>✔</td>
                <td class="text-end">1,003,500.00</td>
                <td class="text-end">1,003,500.00</td>
            </tr>
            <tr class="table-secondary">
                <th colspan="5">Total Cleared Transactions</th>
                <th class="text-end fw-bold">716,300.00</th>
                <th class="text-end fw-bold">716,300.00</th>
            </tr>
            </tbody>
        </table>
    </div>

@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
@endsection
