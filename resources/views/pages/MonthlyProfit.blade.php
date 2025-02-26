
@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <style>

        .card-body {
            padding: 1.5rem;
        }
        .card {
            border-radius: 0.5rem;
        }
        thead {
            background-color: #d9edf7; /* Light blue color */
            color: #31708f; /* Darker blue text for contrast */
        }

        .bg-purple th {
            color: #e1e1e1 !important; /* Ensure white text color for th elements */
        }

        .bg-purple {
            background-color: #1A2942 !important; /* Purple color */
            color: white !important; /* White text color */
        }
        .table {
            width: 100%;
        }

        .card-body {
            padding: 0;
        }

        .card-body {
            padding: 0;
        }

        .table {
            width: 100%;
        }

        .bg-purple th {
            color: #e1e1e1 !important;
        }

        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;
        }


        tfoot tr {
            background-color: #f0f0f0;
            font-weight: bold;
            color: #333;
        }

        tfoot th {
            border-top: 2px solid #1A2942;
            padding: 8px;
            text-align: right;
        }

    </style>
    <style>
        .table-responsive {
            overflow-x: auto;
        }
    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">

                <h4 class="page-title">Loan Repayment Summary Report Overview</h4>
            </div>
        </div>
        <span style="color: #a19595">"The Loan Repayment Summary Report provides an overview of loan repayments for a specified date range, helping you track the capital and interest payments made by each borrower. It allows you to see the due and paid amounts, as well as the balance remaining for both the capital and interest portions of the loan."</span>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <!-- Date Range Filter Form -->
            <form method="GET" action="{{ route('monthlyprofit') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="date_from">Date From</label>
                        <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to">Date To</label>
                        <input type="date" class="form-control" name="date_to" id="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_from">Center</label>
                        <select class="form-control select2" id="center_id" name="center_id">
                            <option value="0">All</option>
                            @foreach($center as $item)
                                <option value="{{ $item->idCenter }}" {{ $center_id == $item->idCenter ? 'selected' : '' }}>
                                    {{ $item->No }} - {{ $item->Name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 align-self-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
    <br>
    <button id="downloadExcel" class="btn btn-danger">Download Excel</button>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- DataTable Wrapper -->
                    <div style="overflow-y: auto; height: auto">
                        <!-- Message indicating the selected date range -->
                        <span style="font-size: 12px; color: red">This table displays data filtered for the selected date range.</span>

                        <!-- Table displaying loan details -->
                        <table id="customerTable" class="table-responsive display nowrap table table-striped table-bordered" style="width:100%">
                            <thead class="sticky-top bg-purple">
                            <tr>
                                <th>Center</th>
                                <th>Group</th>
                                <th>Loan No</th>
                                <th>Member No</th>
                                <th>Due Capital Amount</th>
                                <th>Due Interest Amount</th>
                                <th>Paid Amount</th>
                                <th>Paid from Capital</th>
                                <th>Capital Balance</th>
                                <th>Paid from Interest</th>
                                <th>Interest Balance</th>
                                <th>Total Balance(Not Paid)</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $totalCapitalAmount = 0;
                                $totalInterestAmount = 0;
                                $totalPaidAmount = 0;
                                $totalPaidCapital = 0;
                                $totalCapitalBalance = 0;
                                $totalPaidInterest = 0;
                                $totalInterestBalance = 0;
                                $totalBalance = 0;
                            @endphp

                            @foreach($loan as $item)
                                @php
                                    $paid_amount = $item->Paid_Amount;
                                    $capital_balance = $item->capital_amount;
                                    $interest_balance = 0;
                                    $paid_capital = 0;
                                    $paid_interest = 0;
                                    $Total_Balance = 0;

                                    if ($item->Paid_Amount > $item->interest_amount) {
                                        $interest_balance = 0;
                                        $paid_interest = $item->interest_amount;
                                        $paid_amount = $paid_amount - $paid_interest;

                                        if ($item->Paid_Amount > 0) {
                                            $capital_balance = $item->capital_amount - $paid_amount;
                                            $paid_capital = $paid_amount;
                                        }
                                    } else {
                                        $interest_balance = $item->interest_amount - $paid_amount;
                                        $paid_interest = $paid_amount;
                                    }

                                    $Total_Balance = $capital_balance + $interest_balance;
                                @endphp

                                <tr>
                                    <td>{{ $item->center_no }}</td>
                                    <td>{{ $item->group_name }}</td>
                                    <td>{{ $item->Loan_No }}</td>
                                    <td>{{ $item->cus_number }}</td>
                                    <td>{{ number_format($item->capital_amount, 2) }}</td>
                                    <td>{{ number_format($item->interest_amount, 2) }}</td>
                                    <td>{{ number_format($item->Paid_Amount, 2) }}</td>
                                    <td>{{ number_format($paid_capital, 2) }}</td>
                                    <td>{{ number_format($capital_balance, 2) }}</td>
                                    <td>{{ number_format($paid_interest, 2) }}</td>
                                    <td>{{ number_format($interest_balance, 2) }}</td>
                                    <td>{{ number_format($Total_Balance, 2) }}</td>
                                </tr>

                                @php
                                    $totalCapitalAmount += $item->capital_amount;
                                    $totalInterestAmount += $item->interest_amount;
                                    $totalPaidAmount += $item->Paid_Amount;
                                    $totalPaidCapital += $paid_capital;
                                    $totalCapitalBalance += $capital_balance;
                                    $totalPaidInterest += $paid_interest;
                                    $totalInterestBalance += $interest_balance;
                                    $totalBalance += $Total_Balance;
                                @endphp
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr class="bg-light">
                                <th colspan="4" class="text-right">Total</th>
                                <th>{{ number_format($totalCapitalAmount, 2) }}</th>
                                <th>{{ number_format($totalInterestAmount, 2) }}</th>
                                <th>{{ number_format($totalPaidAmount, 2) }}</th>
                                <th>{{ number_format($totalPaidCapital, 2) }}</th>
                                <th>{{ number_format($totalCapitalBalance, 2) }}</th>
                                <th>{{ number_format($totalPaidInterest, 2) }}</th>
                                <th>{{ number_format($totalInterestBalance, 2) }}</th>
                                <th>{{ number_format($totalBalance, 2) }}</th>
                            </tr>
                            </tfoot>
                        </table>

                    </div>

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div> <!-- end row -->




@endsection

@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();


        });

    </script>

    <script>
        document.getElementById('downloadExcel').addEventListener('click', function() {
            const table = document.getElementById('customerTable');
            const wb = XLSX.utils.table_to_book(table, { sheet: "Loan Repayment Summary Report" });
            XLSX.writeFile(wb, 'repayment_sheet.xlsx');
        });

    </script>

@endsection

