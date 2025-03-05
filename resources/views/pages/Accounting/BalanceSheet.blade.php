@extends('layout.admin')

@section('head')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        h1, h2 {
            text-align: center;
            color: #343a40;
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
        .filters {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .statement {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .total-row {
            background-color: #f1f1f1;
            font-weight: bold;
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead th, tbody td {
            text-align: left;
            padding: 12px;
            border: 1px solid #dee2e6;
        }
        thead th {
            background-color: #e9ecef;
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
        .export {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
        }
        .highlight {
            font-weight: bold;
            color: #dc3545;
        }
    </style>
    <style>
        .modal-lg {
            max-width: 70%;  /* Set modal to 90% of the screen width */
        }

        /* Blinking effect for table rows */
        .blinking {
            animation: blink-animation 1s infinite alternate;
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
    <div class="container mt-5">
        <h1>Balance Sheet Overview</h1>
        <span style="color: #a19595">"This section provides a snapshot of your organization’s financial position as of a specific date, helping you evaluate the financial health by showing assets, liabilities, and equity."</span>
        <br><br>

        <div class="filters">
            <form action="{{route('BalanceSheetView.profit')}}" method="POST">
                @csrf
                <div class="date-range">
{{--                    <input type="date" name="date_from" value="{{$date_from}}"> to--}}
                    To :<input type="date" name="date_to" value="{{$date_to}}">
                </div>
                <div class="buttons">
                    <button class="search" type="submit">Search!</button>
                </div>
            </form>
        </div>

        <button id="exportButton" class="export">Export to Excel</button>
        <button id="exportPdfButton" class="export">Export to PDF</button>
        <br><br>

        <div class="statement">
            <table>
                <thead>
                <tr><th>Category</th><th>Amount</th></tr>
                </thead>
                <tbody>

                <!-- Assets Section -->
                <tr class="fw-bold"><td>1. Assets</td><td></td></tr>

                @if (!empty($assets))
                    @foreach($assets as $asset)
                        <tr onclick="openFinancialReportModal('{{ $asset['idbank'] }}', '{{ $asset['name'] }}')"
                            class="clickable-row blinking">
                            <td class="ps-3">{{ $asset['name'] }}</td>
                            <td>{{ formatNegativeInParentheses($asset['balance']) }}</td>
                        </tr>
                    @endforeach
                @endif

                <tr class="fw-bold total-row">
                    <td>Total Assets</td>
                    <td>{{ formatNegativeInParentheses($total_assets) }}</td>
                </tr>

                <!-- Liabilities Section -->
                <tr class="fw-bold"><td>2. Liabilities</td><td></td></tr>

                @if (!empty($liabilities))
                    @foreach($liabilities as $liability)
                        <tr onclick="openFinancialReportModal('{{ $liability['idbank'] }}', '{{ $liability['name'] }}')"
                            class="clickable-row blinking">
                            <td class="ps-3">{{ $liability['name'] }}</td>
                            <td>{{ formatNegativeInParentheses($liability['balance']) }}</td>
                        </tr>
                    @endforeach
                @endif

                <tr class="fw-bold total-row">
                    <td>Total Liabilities</td>
                    <td>{{ formatNegativeInParentheses($total_liabilities) }}</td>
                </tr>

                <!-- Equity Section -->
                <tr class="fw-bold"><td>3. Equity</td><td></td></tr>

                @if (!empty($equity))
                    @foreach($equity as $equityItem)
                        <tr onclick="openFinancialReportModal('{{ $equityItem['idbank'] }}', '{{ $equityItem['name'] }}')"
                            class="clickable-row blinking">
                            <td class="ps-3">{{ $equityItem['name'] }}</td>
                            <td>{{ formatNegativeInParentheses($equityItem['balance']) }}</td>
                        </tr>
                    @endforeach
                @endif

                <tr class="fw-bold total-row">
                    <td>Total Equity</td>
                    <td>{{ formatNegativeInParentheses($total_equity) }}</td>
                </tr>

                <!-- Final Check -->
                <tr class="fw-bold total-row">
                    <td>Total Liabilities & Equity</td>
                    <td>{{ formatNegativeInParentheses($total_liabilities_and_equity) }}</td>
                </tr>

                <tr class="fw-bold highlight">
                    <td>(Must Equal Total Assets)</td>
                    <td>{{ formatNegativeInParentheses($total_assets+$total_liabilities_and_equity) }}</td>
                </tr>

                </tbody>
            </table>
        </div>





    </div>


    <!-- Modal for Financial Report -->
    <div class="modal fade" id="financialReportModal" tabindex="-1" role="dialog" aria-labelledby="financialReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="financialReportModalLabel">Financial Report</h5>
                </div>
                <div class="modal-body">
                    <table id="financialReportTable" class="table table-striped">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Debit Amount</th>
                            <th>Credit Amount</th>
                            <th>Balance</th>
                            <th>Created At</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Data will be dynamically populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



@endsection
@section('script')
    <script>
        function openFinancialReportModal(idbank, bankName) {
            $('#financialReportModalLabel').text('Financial Report for - ' + bankName);
            $('#financialReportModal').modal('show'); // Open modal

            // Clear previous data
            $('#financialReportTable tbody').empty();

            var date_from = $("#date_from").val();
            var date_to = $("#date_to").val();

            $.ajax({
                url: '/get-financial-full-report',
                type: 'POST',
                data: {
                    account_id: idbank,
                    date_from: date_from,
                    date_to: date_to
                }, headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, success: function (data) {

                    data.forEach(function (item) {
                        var row = `
                <tr>
                    <td>${item.Type || 'N/A'}</td>
                    <td>${item.Description || 'N/A'}</td>
                    <td>${formatNumber(parseFloat(item.Debit).toFixed(2) || 0)}</td>
                    <td>${formatNumber(parseFloat(item.Credit).toFixed(2) || 0)}</td>
                    <td>${formatNumber(parseFloat(item.Balance).toFixed(2) || 0)}</td>
                    <td>${item.Date_Time || 'N/A'}</td>
                </tr>
            `;
                        $('#financialReportTable tbody').append(row);
                    });
                }
                ,
                error: function (xhr) {
                    console.error("AJAX error:", xhr.responseText);
                    alert("Error fetching financial report. Check console for details.");
                }
            });

        }
        // Function to format numbers with commas
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
@endsection


