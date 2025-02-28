@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">




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

            <form action="{{route('profitreport.profit')}}" method="POST">
                @csrf
                <div class="date-range">
                    <input type="date" name="date_from" id="date_from" value="{{$date_from}}"> to
                    <input type="date" name="date_to" id="date_to" value="{{$date_to}}">
                </div>


                <div class="buttons">
                    <button class="search" type="submit">Search!</button>
                </div>
            </form>
        </div>


        <div class="statement shadow-sm">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th></th>
                    <th>{{$date_from}} - {{$date_to}}</th>
                </tr>
                </thead>
                <tbody>
                <!-- Revenue Section -->
                <tr class="revenue fw-bold">
                    <td class="text-success">Revenue</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="ps-3">Revenue from Loans</td>
                </tr>
                <tr class="interest-on-loans" style="cursor: pointer;">
                    <td class="ps-5">
                        <a href="javascript:void(0);">Interest on Loans</a>
                    </td>
                    <td>{{ number_format($interest,2,'.',',') }}</td>
                </tr>


                <tr>
                    <td class="ps-5">Penalty on Loans</td>
                    <td>{{ number_format($panelty,2,'.',',') }}</td>
                </tr>
                <tr>
                    <td class="ps-5">Other Charges On Loans</td>
                    <td>{{ number_format($other_chargers,2,'.',',') }}</td>
                </tr>
                <tr class="total-revenue fw-bold border-bottom-light">
                    <td class="ps-3">Total Revenue</td>
                    <td>{{ number_format($interest + $panelty + $other_chargers,2,'.',',') }}</td>
                </tr>

                <!-- Expenses Section -->
                <tr class="expenses fw-bold">
                    <td class="text-danger">Expenses</td>
                    <td></td>
                </tr>

                @php
                    $total_expenses = 0;
                @endphp
                @if (!empty($system_expenses) && is_iterable($system_expenses) && count($system_expenses) > 0)
                    @foreach ($system_expenses as $expense)
                        @php
                            $total_expenses += $expense->Balance;
                        @endphp
                        <tr>
                            <td class="ps-3">{{ $expense->Bank_Name }} ({{ $expense->type }})</td>
                            <td>{{ number_format($expense->Balance,2,'.',',') }}</td>
                        </tr>
                    @endforeach
                @endif

                <tr class="fw-bold border-top-light">
                    <td class="ps-3">Total Expenses</td>
                    <td>{{ number_format($total_expenses,2,'.',',') }}</td>
                </tr>

                <!-- Net Income Section -->
                <tr class="net-income-after border-top-bottom-dark bg-light fw-bold" style="font-size: 17px;">
                    <td>Net Income</td>
                    <td>
                        {{ number_format((($interest + $panelty + $other_chargers) - $total_expenses + $total_income - $total_expenses), 2, '.', ',') }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="loanInterestModal" tabindex="-1" aria-labelledby="loanInterestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loanInterestModalLabel">Loan Interest Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="loanInterestTable" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Interest Amount</th>
                        </tr>
                        </thead>
                        <tbody id="loanInterestTableBody">
                        <!-- Data will be loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
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
    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function () {
            // Ensure DataTables is loaded dynamically
            $.getScript("https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js", function() {
                console.log("✅ DataTables manually loaded.");
            });

            $(document).on("click", ".interest-on-loans", function () {
                const date_from = $("#date_from").val();
                const date_to = $("#date_to").val();

                $.ajax({
                    url: "{{ route('getLoanInterestDetails') }}",
                    type: "POST",
                    data: {
                        date_to: date_to,
                        date_from: date_from
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (response) {
                        var tableBody = $("#loanInterestTableBody");
                        tableBody.empty();

                        if (response.length > 0) {
                            response.forEach(function (loan) {
                                tableBody.append(`
                            <tr>
                                <td>${loan.Name}</td>
                                <td>${parseFloat(loan.total_interest).toFixed(2)}</td>
                            </tr>
                        `);
                            });
                        } else {
                            tableBody.append('<tr><td colspan="3" class="text-center">No data available</td></tr>');
                        }

                        // Destroy existing DataTable instance if it exists
                        if ($.fn.DataTable.isDataTable("#loanInterestTable")) {
                            $("#loanInterestTable").DataTable().destroy();
                        }

                        // Initialize DataTable with export buttons
                        setTimeout(function () {
                            $("#loanInterestTable").DataTable({
                                paging: true,
                                lengthChange: true,
                                searching: true,
                                ordering: true,
                                info: true,
                                autoWidth: false,
                                responsive: true,
                                dom: 'Bfrtip',
                                buttons: [
                                    {
                                        extend: 'excelHtml5',
                                        text: 'Export to Excel',
                                        className: 'btn btn-success'
                                    },
                                    {
                                        extend: 'pdfHtml5',
                                        text: 'Export to PDF',
                                        className: 'btn btn-danger'
                                    },
                                    {
                                        extend: 'print',
                                        text: 'Print',
                                        className: 'btn btn-primary'
                                    }
                                ]
                            });
                        }, 300);

                        // Open the modal
                        $("#loanInterestModal").modal("show");
                    },
                    error: function () {
                        alert("Failed to load data. Please try again.");
                    }
                });
            });
        });

    </script>

@endsection

