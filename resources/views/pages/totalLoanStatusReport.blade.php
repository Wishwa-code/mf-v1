@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;

        }

        th,
        td {
            padding: 8px 12px;
            font-weight: bold;
            border: 1px solid #ccc;
        }

        th {
            background-color: #ffff66;
            /* Highlight table head with yellow */
            color: black;
            font-weight: bold;
        }

        .main-row {
            background-color: #e0f7fa;
            /* Light blue for main rows */
            font-weight: bold;
            text-align: left;
        }

        .main-row-total {
            background-color: rgb(19, 19, 100);
            /* Light blue for main rows */
            font-weight: bold;
            text-align: left;
            color: white;
        }

        .sub-row {
            text-align: center;
            font-weight: normal;
            text-align: right;
        }

        .header-row {
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Loan Status</h4>
            </div>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>Num Loans Released</th>
            <th>Principal Released</th>
            <th>Principal</th>
            <th>Interest</th>
            <th>Fees</th>
            <th>Penalty</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <!-- Main Row 1 -->
        <tr class="main-row">
            <td colspan="7">1000001(buddika karunaratna)</td>

        </tr>
        <!-- Sub-rows under Row 1 -->
        <tr class="sub-row">
            <td>1</td>
            <td>0</td>
            <td class="text-danger">Due Loans:</td>
            <td class="text-danger">0</td>
            <td class="text-danger">0</td>
            <td class="text-danger">0</td>
            <td class="text-danger">0</td>
        </tr>
        <tr class="sub-row">
            <td></td>
            <td></td>
            <td class="text-success">Payments (1):</td>
            <td class="text-success">0</td>
            <td class="text-success">0</td>
            <td class="text-success">0</td>
            <td class="text-success">0</td>
        </tr>
        <tr class="sub-row">
            <td></td>
            <td></td>
            <td class="text-dark">Net Due:</td>
            <td class="text-dark">0</td>
            <td class="text-dark">0</td>
            <td class="text-dark">0</td>
            <td class="text-dark">0</td>
        </tr>


        <!-- Main Row 2 -->
        <tr class="main-row">
            <td colspan="7">1000002(chathuranga De silva - ASD)</td>
        </tr>
        <!-- Sub-rows under Row 1 -->
        <tr class="sub-row">
            <td>1</td>
            <td>0</td>
            <td class="text-danger">Due Loans:</td>
            <td class="text-danger">0</td>
            <td class="text-danger">0</td>
            <td class="text-danger">0</td>
            <td class="text-danger">0</td>
        </tr>
        <tr class="sub-row">
            <td></td>
            <td></td>
            <td class="text-success">Payments (1):</td>
            <td class="text-success">0</td>
            <td class="text-success">0</td>
            <td class="text-success">0</td>
            <td class="text-success">0</td>
        </tr>
        <tr class="sub-row">
            <td></td>
            <td></td>
            <td class="text-dark">Net Due:</td>
            <td class="text-dark">0</td>
            <td class="text-dark">0</td>
            <td class="text-dark">0</td>
            <td class="text-dark">0</td>
        </tr>


        <!-- Main Row 3 -->
        <tr class="main-row-total">
            <td colspan="7">Total</td>
        </tr>
        <!-- Sub-rows under Row 1 -->
        <tr class="sub-row">
            <td>2</td>
            <td>0</td>
            <td class="text-danger">Due Loans:</td>
            <td class="text-danger">0</td>
            <td class="text-danger">0</td>
            <td class="text-danger">0</td>
            <td class="text-danger">0</td>
        </tr>
        <tr class="sub-row">
            <td></td>
            <td></td>
            <td class="text-success">Payments (1):</td>
            <td class="text-success">0</td>
            <td class="text-success">0</td>
            <td class="text-success">0</td>
            <td class="text-success">0</td>
        </tr>
        <tr class="sub-row">
            <td></td>
            <td></td>
            <td class="text-dark">Net Due:</td>
            <td class="text-dark">0</td>
            <td class="text-dark">0</td>
            <td class="text-dark">0</td>
            <td class="text-dark">0</td>
        </tr>

        </tbody>
    </table>

@endsection

@section('script')
    <!-- jQuery and Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.flash.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
@endsection
