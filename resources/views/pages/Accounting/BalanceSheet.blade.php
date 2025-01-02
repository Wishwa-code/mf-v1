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

        .export {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            margin-bottom: 20px;
            cursor: pointer;
            border-radius: 5px;
            display: inline-block;
            margin-right: 10px;
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

        .ps-3 {
            padding-left: 1rem;
        }

        .ps-5 {
            padding-left: 2rem;
        }

        /* Style for total rows */
        .total-row {
            background-color: #f1f1f1; /* Light gray background for total rows */
            font-weight: bold;
            color: #333; /* Optional: Make the text stand out */
        }

    </style>
@endsection

@section('content')
    <div class="container mt-5">
        <h1>Balance Sheet</h1>
        <div class="filters shadow-sm">
            <form action="{{route('BalanceSheetView.profit')}}" method="POST">
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
        <button id="exportPdfButton" class="export">Export to PDF</button>

        <div class="statement shadow-sm">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Category</th>
                    <th>Amount</th>
                </tr>
                </thead>
                <tbody>
                <!-- Assets Section -->
                <tr class="fw-bold">
                    <td>Assets</td>
                    <td></td>
                </tr>

                <!-- Current Assets -->
                <tr class="fw-bold">
                    <td class="ps-3">Current Assets</td>
                    <td></td>
                </tr>
                @if(!empty($current_assets))
                    @foreach($current_assets as $key => $value)
                        @if($value != 0)
                            <tr>
                                <td class="ps-5">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                <td>{{ formatNegativeInParentheses($value) }}</td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    <tr>
                        <td class="ps-5 text-muted">No Current Assets</td>
                        <td>0.00</td>
                    </tr>
                @endif
                <tr class="fw-bold total-row">
                    <td>Total Current Assets</td>
                    <td>{{ formatNegativeInParentheses(array_sum($current_assets)) }}</td>
                </tr>

                <!-- Non-Current Assets -->
                <tr class="fw-bold">
                    <td class="ps-3">Non-Current Assets</td>
                    <td></td>
                </tr>
                @if(!empty($non_current_assets))
                    @foreach($non_current_assets as $key => $value)
                        <tr>
                            <td class="ps-5">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                            <td>{{ formatNegativeInParentheses($value) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="ps-5 text-muted">No Non-Current Assets</td>
                        <td>0.00</td>
                    </tr>
                @endif
                <tr class="fw-bold total-row">
                    <td>Total Non-Current Assets</td>
                    <td>{{ formatNegativeInParentheses(array_sum($non_current_assets)) }}</td>
                </tr>

                <!-- Total Assets -->
                <tr class="fw-bold total-row">
                    <td>Total Assets</td>
                    <td>{{ formatNegativeInParentheses(array_sum($current_assets) + array_sum($non_current_assets)) }}</td>
                </tr>

                <!-- Liabilities Section -->
                <tr class="fw-bold">
                    <td>Liabilities</td>
                    <td></td>
                </tr>
                @if(!empty($liabilities))
                    @foreach($liabilities as $key => $value)
                        <tr>
                            <td class="ps-3">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                            <td>{{ formatNegativeInParentheses($value) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="ps-3 text-muted">No Liabilities</td>
                        <td>0.00</td>
                    </tr>
                @endif
                <tr class="fw-bold total-row">
                    <td>Total Liabilities</td>
                    <td>{{ formatNegativeInParentheses(array_sum($liabilities)) }}</td>
                </tr>

                <!-- Equity Section -->
                <tr class="fw-bold">
                    <td>Equity</td>
                    <td></td>
                </tr>
                @if(!empty($equity) && array_sum($equity) > 0)
                    @foreach($equity as $key => $value)
                        @if($value != 0)
                            <tr>
                                <td class="ps-3">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                <td>{{ formatNegativeInParentheses($value) }}</td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    <tr>
                        <td class="ps-3 text-muted">No Equity</td>
                        <td>0.00</td>
                    </tr>
                @endif
                <tr class="fw-bold total-row">
                    <td>Total Equity</td>
                    <td>{{ formatNegativeInParentheses(array_sum($equity)) }}</td>
                </tr>

                <!-- Total Liabilities and Equity -->
                <tr class="fw-bold total-row">
                    <td>Total Liabilities and Equity</td>
                    <td>{{ formatNegativeInParentheses(array_sum($liabilities) + array_sum($equity)) }}</td>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        $(document).ready(function () {
            // Export to Excel
            $("#exportButton").click(function () {
                var table = document.querySelector("table");
                var wb = XLSX.utils.table_to_book(table, { sheet: "Sheet 1" });
                XLSX.writeFile(wb, "BalanceSheet.xlsx");
            });

            // Export to PDF
            $("#exportPdfButton").click(function () {
                const statementElement = document.querySelector('.statement'); // Target the container

                // Use html2canvas to capture the element
                html2canvas(statementElement, {
                    scale: 2, // Increase resolution for better quality
                }).then((canvas) => {
                    const imgData = canvas.toDataURL('image/png'); // Convert canvas to image
                    const pdf = new jspdf.jsPDF('p', 'mm', 'a4'); // Create jsPDF instance

                    // Calculate the dimensions for the PDF
                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

                    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight); // Add image to PDF
                    pdf.save('BalanceSheet.pdf'); // Save and download the PDF
                }).catch((error) => {
                    console.error("Error generating PDF:", error); // Handle errors gracefully
                });
            });
        });
    </script>
@endsection


@php
    function formatNegativeInParentheses($value) {
        if ($value < 0) {
            return '(' . number_format(abs($value), 2, '.', ',') . ')';
        }
        return number_format($value, 2, '.', ',');
    }
@endphp
