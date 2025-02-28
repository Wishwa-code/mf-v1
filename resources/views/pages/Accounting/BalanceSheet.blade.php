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
                    @foreach($assets as $name => $balance)
                        <tr>
                            <td class="ps-3">{{ $name }}</td>
                            <td>{{ formatNegativeInParentheses($balance) }}</td>
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
                    @foreach($liabilities as $name => $balance)
                        <tr>
                            <td class="ps-3">{{ $name }}</td>
                            <td>{{ formatNegativeInParentheses($balance) }}</td>
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
                    @foreach($equity as $name => $balance)
                        <tr>
                            <td class="ps-3">{{ $name }}</td>
                            <td>{{ formatNegativeInParentheses($balance) }}</td>
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



@endsection
