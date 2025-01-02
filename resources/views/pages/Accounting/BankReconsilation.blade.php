@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css" />

    <style>
        h1 {
            color: #343a40;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 600;
        }

        .filters {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filters div {
            display: flex;
            flex-direction: column;
            margin-right: 15px;
            margin-bottom: 10px;
        }

        .filters label {
            font-weight: 500;
            font-size: 1rem;
            color: #495057;
            margin-bottom: 5px;
        }

        .filters select, .filters input {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ced4da;
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
        }

        .filters select:focus, .filters input:focus {
            border-color: #007bff;
        }

        /* Fixed width for the bank select dropdown */
        #bank-account {
            width: 250px !important;
        }

        .filters button.search {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 18px;
            cursor: pointer;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
            align-self: flex-end;
            margin-left: auto;
        }

        .filters button.search:hover {
            background-color: #0056b3;
        }

        .table-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            overflow-x: auto; /* Horizontal scroll for responsive tables */
        }

        .balance-display {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 12px;
            background-color: #f1f3f5;
            border-radius: 6px;
            font-size: 1.2rem;
            font-weight: 600;
            color: #343a40;
        }

        .balance-display span {
            padding: 8px 15px;
            border-radius: 5px;
        }

        .opening-balance {
            background-color: #ffc107;
            color: #fff;
        }

        .closing-balance {
            background-color: #28a745;
            color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #dee2e6;
        }

        table thead th {
            background-color: #f8f9fa;
            font-weight: 500;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .status-label {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
        }

        .status-pending {
            background-color: #ffc107;
            color: #fff;
        }

        .status-complete {
            background-color: #1ec900;
            color: #fff;
        }

        .status-missing {
            background-color: #ff0000;
            color: #fff;
        }

        .update-btn {
            background-color: #007bff;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .update-btn:hover {
            background-color: #0056b3;
        }

        .export {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 18px;
            cursor: pointer;
            border-radius: 6px;
            display: block;
            margin-left: auto;
            transition: background-color 0.3s ease;
        }

        .export:hover {
            background-color: #0056b3;
        }
        .table-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            overflow-x: auto; /* Horizontal scroll for responsive tables */
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .filters {
                flex-direction: column;
                align-items: flex-start;
            }

            .filters div {
                width: 100%;
                margin-right: 0;
            }

            .filters button.search {
                width: 100%;
            }

            table th, table td {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 576px) {
            .balance-display {
                flex-direction: column;
                font-size: 1rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container mt-5">
        <h1>Bank Reconciliation Report</h1>
        <form action="{{route('Bank.BankReconciliation')}}" method="post">
            <div class="filters shadow-sm">
                @csrf
                <div>
                    <label for="bank-account">Bank Account</label>
                    <select id="bank-account" name="bank" class="select2">
                        <option value="all">All</option>
                        @foreach($bank as $item)
                            <option value="{{$item->Idbank}}">{{$item->Bank_Name}}-{{$item->Account_No}}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date-from">Date From</label>
                    <input type="date" id="date-from" name="date_from" value="{{$date_from}}">
                </div>

                <div>
                    <label for="date-to">Date To</label>
                    <input type="date" id="date-to" name="date_to" value="{{$date_to}}">
                </div>

                <button class="search" type="submit">View Report</button>
            </div>
        </form>

        <div class="table-container shadow-sm">
            <div class="balance-display">
                <span class="opening-balance">Opening Balance: {{number_format($opening_balance,2,'.',',')}}</span>
                <span class="closing-balance">Closing Balance: {{number_format($closing_balance,2,'.',',')}}</span>
            </div>

            <table id="bankReconciliationTable" class="display">
                <thead>
                <tr>
                    <th hidden></th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Credit</th>
                    <th>Debit</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Note</th>
                    <th>Missing</th>
                    <th>Checked</th>
                </tr>
                </thead>
                <tbody>
                @foreach($bank_log as $item)
                    <tr>
                        <td hidden>{{$item->id}}</td> <!-- Use hidden or display log ID -->
                        <td>{{$item->Date_Time}}</td>
                        <td>{{$item->Description}}</td>
                        <td>{{number_format($item->Credit,2,'.',',')}}</td>
                        <td>{{number_format($item->Debit,2,'.',',')}}</td>
                        <td>{{number_format($item->Balance,2,'.',',')}}</td>
                        @if($item->updated_status==="Missing")
                            <td><span class="status-label status-missing">{{$item->updated_status}}</span></td>
                        @elseif($item->updated_status==="Checked")
                            <td><span class="status-label status-complete">{{$item->updated_status}}</span></td>
                        @else
                            <td><span class="status-label status-pending">{{$item->updated_status}}</span></td>
                        @endif
                        <td><input type="text" class="form-control" value="{{$item->updated_note}}"></td>
                        <td>
                            <input type="button" class="btn btn-danger" value="Missing">
                        </td>
                        <td>
                            <input type="button" class="btn btn-success" value="Checked">
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection


@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize Select2 for dropdowns
            $('.select2').select2();

            // Initialize DataTable
            $('#bankReconciliationTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,  // Make table responsive
                "lengthChange": true,
                "pageLength": 10,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: 'Export to Excel',
                        title: 'Bank Reconciliation Report'
                    }
                ]
            });


            // Export to Excel functionality (Alternative method)
            $("#exportButton").click(function () {
                var table = document.querySelector("bankReconciliationTable");
                var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet 1"});
                XLSX.writeFile(wb, "bank_reconciliation_report.xlsx");
            });


            // Handle 'Missing' button click
            $('.btn-danger').click(function () {
                var row = $(this).closest('tr');
                var logId = row.find('td:first').text(); // Assuming the first column holds the ID (adjust accordingly)
                var note = row.find('input[type="text"]').val(); // Get the note input

                if (!note) {
                    alert("Please enter a note for missing status.");
                    return;
                }

                // Send AJAX request for 'Missing' status
                $.ajax({
                    url: '{{ route('bankLog.updateStatus') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        log_id: logId,
                        status: 'Missing',
                        note: note
                    },
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                            // Optionally reload the page or update the row dynamically
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });

            // Handle 'Checked' button click
            $('.btn-success').click(function () {
                var row = $(this).closest('tr');
                var logId = row.find('td:first').text(); // Assuming the first column holds the ID (adjust accordingly)

                // Send AJAX request for 'Checked' status
                $.ajax({
                    url: '{{ route('bankLog.updateStatus') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        log_id: logId,
                        status: 'Checked',
                        note: null // No need for note in 'Checked' status
                    },
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                            // Optionally reload the page or update the row dynamically
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });

        });
    </script>
@endsection
