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

        .main-row {
            border-bottom: 2px solid #000;
            text-align: right;

        }

        .sub-row {
            background-color: #ffffff;
            text-align: right;
        }

        .mhead {
            text-align: left;
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


    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <!-- DataTable -->
                    <table>
                        <thead>
                        <tr>
                            <th>Loan Status</th>
                            <th></th>
                            <th>Principal</th>
                            <th>Interst</th>
                            <th>Fees</th>
                            <th>Penalty</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Row 1 with 3 sub-rows -->
                        <tr class="sub-row">
                            <td rowspan="3" class="mhead">Current loans</td>
                            <td class="text-danger">Gross Loan Amount</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td class="text-danger">0</td>
                        </tr>
                        <tr class="sub-row">
                            <td class="text-success">Paid Amount</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td class="text-success">0</td>
                        </tr>
                        <tr class="main-row">
                            <td class="text-dark">Net Laon Balance</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td class="text-dark">0</td>
                        </tr>

                        <!-- Row 2 with 3 sub-rows -->
                        <tr class="sub-row">
                            <td rowspan="3" class="mhead">Arreas loans</td>
                            <td class="text-danger">Gross Loan Amount</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td class="text-danger">0</td>
                        </tr>
                        <tr class="sub-row">
                            <td class="text-success">Paid Amount</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td class="text-success">0</td>
                        </tr>
                        <tr class="main-row">
                            <td class="text-dark">Net Laon Balance</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td class="text-dark">0</td>
                        </tr>


                        <!-- Row 3 with 3 sub-rows -->
                        <tr class="sub-row">
                            <td rowspan="3" class="mhead">Past maturity loans</td>
                            <td class="text-danger">Gross Loan Amount</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td class="text-danger">0</td>
                        </tr>
                        <tr class="sub-row">
                            <td class="text-success">Paid Amount</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td class="text-success">0</td>
                        </tr>
                        <tr class="main-row">
                            <td class="text-dark">Net Laon Balance</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td class="text-dark">0</td>
                        </tr>

                        <!-- Row 4 with 3 sub-rows -->
                        <tr class="sub-row">
                            <td rowspan="3" class="mhead">Settled loans</td>
                            <td class="text-danger">Gross Loan Amount</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td class="text-danger">0</td>
                        </tr>
                        <tr class="sub-row">
                            <td class="text-success">Paid Amount</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td class="text-success">0</td>
                        </tr>
                        <tr class="main-row">
                            <td class="text-dark">Net Laon Balance</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td class="text-dark">0</td>
                        </tr>

                        <!-- Row 4 with 3 sub-rows -->

                        <!-- Row 4 with 3 sub-rows -->
                        <tr class="sub-row">
                            <td rowspan="3" class="mhead">Resheduled loans</td>
                            <td class="text-danger">Gross Loan Amount</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td class="text-danger">0</td>
                        </tr>
                        <tr class="sub-row">
                            <td class="text-success">Paid Amount</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td class="text-success">0</td>
                        </tr>
                        <tr class="main-row">
                            <td class="text-dark">Net Laon Balance</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td class="text-dark">0</td>
                        </tr>

                        </tbody>
                    </table>

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div> <!-- end row -->
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
