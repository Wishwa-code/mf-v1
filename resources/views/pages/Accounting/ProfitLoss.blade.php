@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        h1 {
            color: #495057;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .filters {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filters label {
            margin-right: 15px;
            font-weight: bold;
            font-size: 1.1rem;
            color: #495057;
        }

        .basis-options {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .basis-options input {
            margin-right: 5px;
        }

        .date-range, .compare-period {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .date-range input, .compare-period input {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            width: 200px;
        }

        .buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .buttons button {
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search {
            background-color: #28a745;
            color: #fff;
        }

        .reset {
            background-color: #6c757d;
            color: #fff;
        }

        .export {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            margin-bottom: 20px;
            cursor: pointer;
            border-radius: 5px;
            display: block;
            margin-left: auto;
        }

        .statement {
            margin-top: 30px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #343a40;
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.6rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead th {
            text-align: left;
            padding: 12px;
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
        }

        tbody td {
            padding: 12px;
            border: 1px solid #dee2e6;
        }

        .fw-bold {
            font-weight: 700;
        }

        .bg-light {
            background-color: #f8f9fa;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        .ps-3 {
            padding-left: 1rem;
        }

        .ps-5 {
            padding-left: 2rem;
        }
    </style>
@endsection

@section('content')
    <div class="container mt-5">
        <h1>Profit / Loss Statement Overview</h1>
        <span style="color: #a19595">"This section provides a detailed breakdown of your organization's revenue, expenses, and net income for a specific period, helping you evaluate financial performance. The statement includes both operating and non-operating revenues and expenses, along with tax-related figures."</span>
        <br><br>
        <div class="filters shadow-sm">
{{--            <div class="basis-options">--}}
{{--                <label>--}}
{{--                    <input type="radio" name="basis" checked> Cash Basis--}}
{{--                </label>--}}
{{--                <label>--}}
{{--                    <input type="radio" name="basis"> Accrual Basis--}}
{{--                </label>--}}
{{--            </div>--}}

            <form action="{{route('profitreport.profit')}}" method="POST">
                @csrf
                <div class="date-range">
                    <input type="date" name="date_from" value="{{$date_from}}"> to
                    <input type="date" name="date_to" value="{{$date_to}}">
                </div>


                <div class="buttons">
                    <button class="search" type="submit">Search!</button>
                </div>
            </form>
        </div>
        <button id="exportButton" class="export">Export to Excel</button>


        <div class="statement shadow-sm">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th></th>
                    <th>{{$date_from}}-{{$date_to}}</th>
                </tr>
                </thead>
                <tbody>
                <tr class="revenue fw-bold">
                    <td class="text-success">Revenue</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="ps-3">Revenue from Loans</td>

                </tr>
                <tr>
                    <td class="ps-5">Interest on Loans</td>
                    <td>{{number_format($interest,2,'.',',')}}</td>
                </tr>
                <tr>
                    <td class="ps-5">Penalty on Loans</td>
                    <td>{{number_format($panelty,2,'.',',')}}</td>
                </tr>
                <tr>
                    <td class="ps-5">Other Chargers On Loans</td>
                    <td>{{number_format($other_chargers,2,'.',',')}}</td>
                </tr>
                <tr class="total-revenue fw-bold border-bottom-light">
                    <td class="ps-3">Total Revenue</td>
                    <td>{{number_format($interest+$panelty+$other_chargers,2,'.',',')}}</td>
                </tr>
                <tr class="expenses fw-bold">
                    <td class="text-danger">Expenses</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="ps-3">Total Expenses</td>
                    <td>{{number_format($loan_expenses,2,'.',',')}}</td>
                </tr>
                <tr class="net-operating-income border-top-bottom-dark bg-light">
                    <td>Net Operating Income</td>
                    <td>{{number_format(($interest+$panelty+$other_chargers)-$loan_expenses,2,'.',',')}}</td>
                </tr>

                <tr class="revenue fw-bold">
                    <td class="text-success">Non-Operating Revenue</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="ps-5">Total Income</td>
                    <td>{{number_format($total_income,2,'.',',')}}</td>
                </tr>
                <tr class="expenses fw-bold">
                    <td class="text-danger">Expenses</td>
                    <td></td>
                </tr>
                <tr>
                    <td  class="ps-5">Non-Operating Expenses</td>
                    <td>{{number_format($total_expenses,2,'.',',')}}</td>
                </tr>
                <tr class="net-operating-income border-top-bottom-dark bg-light">
                    <td>Net Non-Operating Income</td>
                    <td>{{number_format(($total_income)-$total_expenses,2,'.',',')}}</td>
                </tr>
                <tr class="net-income-after border-top-bottom-dark bg-light fw-bold" style="font-size: 17px;">
                    <td >Net Income Before Taxes and Subsidy</td>
                    <td>{{ number_format((($interest + $panelty + $other_chargers) - $loan_expenses + $total_income - $total_expenses), 2, '.', ',') }}</td>

                </tr>
                <tr class="taxes fw-bold">
                    <td>Taxes</td>
                    <td></td>
                </tr>
                <tr class="income-tax-expense">
                    <td class="ps-5">Income Tax Expense</td>
                    <td>0.00</td>
                </tr>
                <tr class="net-income-after border-top-bottom-dark bg-light fw-bold" style="font-size: 20px;">
                    <td>Net Income After Taxes and Subsidy</td>
                    <td>{{ number_format((($interest + $panelty + $other_chargers) - $loan_expenses + $total_income - $total_expenses), 2, '.', ',') }}</td>
                </tr>
                </tbody>
            </table>


        </div>
    </div>
@endsection

@section('script')
    <script src="assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="assets/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="assets/js/pages/dashboard.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/center.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#exportButton").click(function () {
                // Get the table element
                var table = document.querySelector("table");

                // Convert the table to a worksheet
                var wb = XLSX.utils.table_to_book(table, { sheet: "Sheet 1" });

                // Export the table to an Excel file
                XLSX.writeFile(wb, "profit_loss_statement.xlsx");
            });
        });
    </script>

@endsection

