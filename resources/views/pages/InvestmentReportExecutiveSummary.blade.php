@extends('layout.admin')

@section('head')
    <style>
        .page-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1.5rem;
        }
        .report-card {
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }
        .filter-shell {
            background: #f8f9fa;
            border-radius: 0.75rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
        }
        .filter-shell .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: #4b5563;
            margin-bottom: 0.5rem;
        }
        .filter-shell .form-control,
        .filter-shell .form-select {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            padding: 0.5rem 0.75rem;
            transition: all 0.2s;
        }
        .filter-shell .form-control:focus,
        .filter-shell .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }
        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .table-wrapper {
            overflow-x: auto;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
        }
        .report-table {
            font-size: 0.875rem;
            margin-bottom: 0;
        }
        .report-table thead th {
            background-color: #374151 !important;
            color: #fff !important;
            font-weight: 600;
            white-space: nowrap;
            padding: 0.875rem 0.75rem !important;
            vertical-align: middle !important;
            border: 1px solid #4b5563 !important;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        .report-table tbody td {
            padding: 0.75rem;
            vertical-align: middle;
            border-color: #e5e7eb;
        }
        .report-table tbody td:first-child {
            font-weight: 600;
            color: #374151;
        }
        .report-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .report-table tbody tr:hover {
            background-color: #f3f4f6;
            transition: background-color 0.15s;
        }
        .report-table tfoot th {
            background-color: #f3f4f6 !important;
            font-weight: 700;
            padding: 0.875rem 0.75rem !important;
            border-top: 2px solid #d1d5db !important;
            color: #1f2937;
        }
        .table-wrapper::-webkit-scrollbar {
            height: 8px;
        }
        .table-wrapper::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 0.5rem;
        }
        .table-wrapper::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 0.5rem;
        }
        .table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h4 class="page-title">INVESTMENT REPORT EXECUTIVE SUMMARY</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card report-card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('investment.index') }}" class="filter-shell">
                            <div class="row g-3 align-items-end">
                                <div class="col-lg-3 col-md-6">
                                    <label for="from" class="form-label fw-semibold">From</label>
                                    <input type="date" id="from" name="from" class="form-control" value="{{ request('from', \Carbon\Carbon::now()->startOfMonth()->toDateString()) }}">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <label for="to" class="form-label fw-semibold">To</label>
                                    <input type="date" id="to" name="to" class="form-control" value="{{ request('to', \Carbon\Carbon::now()->toDateString()) }}">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <label for="loan_officer" class="form-label fw-semibold">Loan Officer</label>
                                    <select class="form-control" id="loan_officer" name="loan_officer">
                                        <option value="">All Officers</option>
                                        @foreach($officer as $item)
                                            <option value="{{ $item->id }}" {{ request('loan_officer') == $item->id ? 'selected' : '' }}>
                                                {{ $item->Full_Name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search me-1"></i> Search
                                    </button>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <button id="exportExcel" type="button" class="btn btn-success w-100">
                                        <i class="bi bi-file-earmark-excel me-1"></i> Export to Excel (CSV)
                                    </button>
                                </div>
                            </div>
                        </form>

                        @php
                            // Calculate totals
                            $totals = [
                                'Beginning_Stock' => 0,
                                'Current_End_Stock' => 0,
                                'Investment' => 0,
                                'Depletion' => 0,
                                'Total_Loans' => 0,
                            ];
                            $productTotals = [];
                            foreach($product as $p) {
                                $productTotals[$p->idLoan_Category] = ['count' => 0, 'amount' => 0];
                            }
                            foreach($data as $row) {
                                $totals['Beginning_Stock'] += $row['Beginning_Stock'];
                                $totals['Current_End_Stock'] += $row['Current_End_Stock'];
                                $totals['Investment'] += $row['Investment'];
                                $totals['Depletion'] += $row['Depletion'];
                                $totals['Total_Loans'] += $row['Total_Loans'];
                                
                                foreach($product as $p) {
                                    $pid = (string)$p->idLoan_Category;
                                    if(isset($row['product_data']->$pid)) {
                                        $productTotals[$p->idLoan_Category]['count'] += $row['product_data']->$pid['count'];
                                        $productTotals[$p->idLoan_Category]['amount'] += $row['product_data']->$pid['amount'];
                                    }
                                }
                            }
                        @endphp

                        <div class="table-wrapper mt-4">
                            <table class="table table-bordered table-hover table-sm report-table">
                                <thead>
                                    <tr>
                                        <th>IN.NO</th>
                                        <th>Loan Officer</th>
                                        <th>Beginning Stock</th>
                                        <th>Current/End Stock</th>
                                        <th>Investment</th>
                                        <th>Depletion</th>
                                        <th>Total Loan</th>
                                        {{-- Hidden columns --}}
                                        {{-- <th>Collection</th>
                                        <th>Arrears</th>
                                        <th>Portfolio</th>
                                        <th>Debtor Ratio</th>
                                        <th>Penalty Arrears</th>
                                        <th>OC Loans</th>
                                        <th>Total Clients</th>
                                        <th>OC Clients</th>
                                        <th>Active Clients</th>
                                        <th>Total Outstanding</th> --}}
                                        @foreach($product as $p)
                                            <th colspan="2" class="text-center">{{ $p->Product_code }} - {{ $p->Name }}</th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th colspan="7"></th>
                                        @foreach($product as $p)
                                            <th class="text-center">Loan</th>
                                            <th class="text-center">Amount</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $index => $row)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $row['Loan_Officer'] }}</td>
                                            <td class="text-end">{{ number_format($row['Beginning_Stock'], 2) }}</td>
                                            <td class="text-end">{{ number_format($row['Current_End_Stock'], 2) }}</td>
                                            <td class="text-end">{{ number_format($row['Investment'], 2) }}</td>
                                            <td class="text-end">{{ number_format($row['Depletion'], 2) }}</td>
                                            <td class="text-end">{{ number_format($row['Total_Loans'], 0) }}</td>
                                            {{-- Hidden columns --}}
                                            {{-- <td class="text-end">{{ number_format($row['Collection'], 2) }}</td>
                                            <td class="text-end">{{ number_format($row['Arrears'], 2) }}</td>
                                            <td class="text-end">{{ number_format($row['Portfolio'], 2) }}</td>
                                            <td class="text-end">{{ number_format($row['Debtor_Ratio'], 2) }}%</td>
                                            <td class="text-end">{{ number_format($row['Penalty_Arrears'], 2) }}</td>
                                            <td class="text-end">{{ number_format($row['OC_Loans'], 0) }}</td>
                                            <td class="text-end">{{ number_format($row['Total_Clients'], 0) }}</td>
                                            <td class="text-end">{{ number_format($row['OC_Clients'], 0) }}</td>
                                            <td class="text-end">{{ number_format($row['Active_Clients'], 0) }}</td>
                                            <td class="text-end">{{ number_format($row['Total_Outstanding_Balance'], 2) }}</td> --}}
                                            @foreach($product as $p)
                                                @php
                                                    $pid = (string)$p->idLoan_Category;
                                                    $pData = $row['product_data']->$pid ?? ['count' => 0, 'amount' => 0];
                                                @endphp
                                                <td class="text-end">{{ number_format($pData['count'], 0) }}</td>
                                                <td class="text-end">{{ number_format($pData['amount'], 2) }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ 7 + ($product->count() * 2) }}" class="text-center">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">TOTAL</th>
                                        <th class="text-end">{{ number_format($totals['Beginning_Stock'], 2) }}</th>
                                        <th class="text-end">{{ number_format($totals['Current_End_Stock'], 2) }}</th>
                                        <th class="text-end">{{ number_format($totals['Investment'], 2) }}</th>
                                        <th class="text-end">{{ number_format($totals['Depletion'], 2) }}</th>
                                        <th class="text-end">{{ number_format($totals['Total_Loans'], 0) }}</th>
                                        {{-- Hidden columns --}}
                                        {{-- <th class="text-end">{{ number_format($totals['Collection'], 2) }}</th>
                                        <th class="text-end">{{ number_format($totals['Arrears'], 2) }}</th>
                                        <th class="text-end">{{ number_format($totals['Portfolio'], 2) }}</th>
                                        <th class="text-end">{{ number_format($debtorRatioTotal, 2) }}%</th>
                                        <th class="text-end">{{ number_format($totals['Penalty_Arrears'], 2) }}</th>
                                        <th class="text-end">{{ number_format($totals['OC_Loans'], 0) }}</th>
                                        <th class="text-end">{{ number_format($totals['Total_Clients'], 0) }}</th>
                                        <th class="text-end">{{ number_format($totals['OC_Clients'], 0) }}</th>
                                        <th class="text-end">{{ number_format($totals['Active_Clients'], 0) }}</th>
                                        <th class="text-end">{{ number_format($totals['Total_Outstanding_Balance'], 2) }}</th> --}}
                                        @foreach($product as $p)
                                            <th class="text-end">{{ number_format($productTotals[$p->idLoan_Category]['count'], 0) }}</th>
                                            <th class="text-end">{{ number_format($productTotals[$p->idLoan_Category]['amount'], 2) }}</th>
                                        @endforeach
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.getElementById('exportExcel').addEventListener('click', function() {
            var data = @json($data);
            var products = @json($product);
            
            if (!data || data.length === 0) {
                alert('No data to export.');
                return;
            }

            // Build headers
            var headers = [
                'IN.NO',
                'Loan Officer',
                'Beginning Stock',
                'Current/End Stock',
                'Investment',
                'Depletion',
                'Total Loan'
            ];

            products.forEach(function(p) {
                headers.push(p.Product_code + '_Loan');
                headers.push(p.Product_code + '_Amount');
            });

            // Helper function to escape CSV values
            function escapeCSV(value) {
                var str = value == null ? '' : String(value);
                return /[",\n]/.test(str) ? '"' + str.replace(/"/g, '""') + '"' : str;
            }

            function toRaw(value) {
                return value == null ? '' : String(value).replace(/[, ]/g, '');
            }

            var lines = [];
            lines.push(headers.join(','));

            // Add data rows
            data.forEach(function(row, index) {
                var line = [
                    index + 1,
                    escapeCSV(row.Loan_Officer || '—'),
                    toRaw(row.Beginning_Stock),
                    toRaw(row.Current_End_Stock),
                    toRaw(row.Investment),
                    toRaw(row.Depletion),
                    toRaw(row.Total_Loans)
                ];

                // Add product data
                products.forEach(function(p) {
                    var pid = String(p.idLoan_Category);
                    var pData = row.product_data[pid] || { count: 0, amount: 0 };
                    line.push(toRaw(pData.count));
                    line.push(toRaw(pData.amount));
                });

                lines.push(line.join(','));
            });

            // Add totals row
            var totals = @json($totals);
            var productTotals = @json($productTotals);
            var totalLine = [
                '',
                'TOTAL',
                toRaw(totals.Beginning_Stock),
                toRaw(totals.Current_End_Stock),
                toRaw(totals.Investment),
                toRaw(totals.Depletion),
                toRaw(totals.Total_Loans)
            ];

            products.forEach(function(p) {
                var pTotal = productTotals[p.idLoan_Category] || { count: 0, amount: 0 };
                totalLine.push(toRaw(pTotal.count));
                totalLine.push(toRaw(pTotal.amount));
            });

            lines.push(totalLine.join(','));

            // Create and download file
            var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'Investment_Executive_Summary.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });
    </script>
@endsection
