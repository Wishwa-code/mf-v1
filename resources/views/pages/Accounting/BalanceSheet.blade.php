@extends('layout.admin')

@section('head')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
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

        /* General Table Styling */
        .statement table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #ffffff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        /* Table Headings */
        .statement thead th {
            background-color: #ffffff;
            color: #000000;
            text-align: center;
            padding: 14px;
            font-size: 16px;
            border-bottom: 3px solid #262626;
        }

        /* Table Rows */
        .statement tbody tr {
            border-bottom: 1px solid #ddd;
            transition: background-color 0.3s ease-in-out;
        }

        /* Alternating Row Colors */
        .statement tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Hover Effect */
        .statement tbody tr:hover {
            background-color: #e9f5ff !important;
            cursor: pointer;
        }

        /* Cells */
        .statement td {
            padding: 12px;
            text-align: right;
            font-size: 15px;
        }

        /* Left Align Category Names */
        .statement td:first-child {
            text-align: left;
            font-weight: bold;
        }

        /* Totals and Highlighted Rows */
        .statement .total-row {
            background-color: #e3e5e5 !important;
            font-weight: bold;
            color: #393a3a;
            border-top: 2px solid #000000;
        }

        /* Highlighted Balance Check */
        .statement .highlight {
            background-color: #363228 !important;
            color: #fff5f5;
            font-weight: bold;
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 10px;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background-color: #eeeeef;
            color: #000000;
            border-bottom: 3px solid #7a7f85;
        }

        .modal-title {
            font-weight: bold;
        }

    </style>
@endsection

@section('content')

    <div class="container mt-4">
        <h2>Balance Sheet Overview</h2>
        <span style="color: #a19595">"This section provides a snapshot of your organization’s financial position as of a specific date, helping you evaluate the financial health by showing assets, liabilities, and equity."</span>
        <br><br>

        <!-- Search Section -->
        <form class="row g-3 mb-4 align-items-end" action="{{route('BalanceSheetView.profit')}}" method="POST">
            @csrf
            <div class="col-md-3">
                <label for="date_from" class="form-label">Generate Balance Sheet Untill :</label>
                <input type="date" class="form-control" name="date_to" id="date_to" value="{{$date_to}}">
            </div>


            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Generate</button>
            </div>
            <div class="col-md-2">
                <button id="btnExportExcel" class="btn btn-success w-100">Download Excel</button>
            </div>
            <div class="col-md-2">
                <button id="btnExportPDF" class="btn btn-danger w-100">Download PDF</button>
            </div>


        </form>


    </div>



    <div class="container mt-5">

        <div class="statement">
            <table>
                <thead>
                <tr><th style="text-align: left">Accounts</th><th style="text-align: right">Balance Amount</th></tr>
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
                    <td >{{ formatNegativeInParentheses($total_liabilities_and_equity) }}</td>
                </tr>

{{--                <tr class="fw-bold highlight">--}}
{{--                    <td>(Must Equal Total Assets)</td>--}}
{{--                    <td>{{ formatNegativeInParentheses($total_assets+$total_liabilities_and_equity) }}</td>--}}
{{--                </tr>--}}

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
                            <th style="text-align: left">Type</th>
                            <th style="text-align: left">Description</th>
                            <th style="text-align: right">Debit Amount</th>
                            <th style="text-align: right">Credit Amount</th>
                            <th style="text-align: right">Balance</th>
                            <th style="text-align: right">Contra Account</th>
                            <th style="text-align: right">Reconciliation No</th>
                            <th style="text-align: right">Created At</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.20/jspdf.plugin.autotable.min.js"></script>
    <script>
        $(document).ready(function () {
            // Export to Excel
            $("#btnExportExcel").click(function (e) {
                e.preventDefault();
                exportTableToExcel();
            });

            // Export to PDF
            $("#btnExportPDF").click(function (e) {
                e.preventDefault();
                exportTableToPDF();
            });

            function exportTableToExcel() {
                let table = document.querySelector(".statement table");
                let workbook = XLSX.utils.table_to_book(table, { sheet: "Balance Sheet" });
                XLSX.writeFile(workbook, "Balance_Sheet.xlsx");
            }

            function exportTableToPDF() {
                const { jsPDF } = window.jspdf;
                let doc = new jsPDF();

                doc.setFontSize(14);
                doc.text("Balance Sheet Overview", 14, 10);

                doc.autoTable({
                    html: '.statement table',
                    startY: 20,
                    styles: { fontSize: 10, textColor: [0, 0, 0] },
                    theme: 'grid'
                });

                doc.save("Balance_Sheet.pdf");
            }
        });
    </script>

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
                    <td style="text-align: left">${item.Type || 'N/A'}</td>
                    <td style="text-align: left">${item.Description || 'N/A'}</td>
                    <td style="text-align: right">${formatNumber(parseFloat(item.Debit).toFixed(2) || 0)}</td>
                    <td style="text-align: right">${formatNumber(parseFloat(item.Credit).toFixed(2) || 0)}</td>
                    <td style="text-align: right">${formatNumber(parseFloat(item.Balance).toFixed(2) || 0)}</td>
                    <td style="text-align: right">${item.Account_Name ?? '-'}</td>
<td style="text-align: right">${item.reconsilation_status || 'N/A'}</td>
<td style="text-align: right">${item.Date_Time || 'N/A'}</td>
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


