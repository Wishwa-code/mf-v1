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
                <h4 class="page-title">ARREARS REPORT EXECUTIVE SUMMARY</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card report-card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('arrears.index') }}" class="filter-shell">
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
                                'Arrears' => 0,
                                'Week_01' => ['loan' => 0, 'amount' => 0],
                                'Week_1to3' => ['loan' => 0, 'amount' => 0],
                                'Week_4to8' => ['loan' => 0, 'amount' => 0],
                                'Week_9to12' => ['loan' => 0, 'amount' => 0],
                                'Week_13plus' => ['loan' => 0, 'amount' => 0],
                                'OC_Clients_Loan' => 0,
                                'Total_Arrears_Loan' => 0,
                                'Total_Arrear_Customer' => 0,
                                'Total_Capital_Arrears' => 0,
                                'Total_Interest_Arrears' => 0,
                            ];
                            
                            foreach($data as $row) {
                                $totals['Arrears'] += $row['Arrears'];
                                $totals['Week_01']['loan'] += $row['Week_01']['loan'];
                                $totals['Week_01']['amount'] += $row['Week_01']['amount'];
                                $totals['Week_1to3']['loan'] += $row['Week_1to3']['loan'];
                                $totals['Week_1to3']['amount'] += $row['Week_1to3']['amount'];
                                $totals['Week_4to8']['loan'] += $row['Week_4to8']['loan'];
                                $totals['Week_4to8']['amount'] += $row['Week_4to8']['amount'];
                                $totals['Week_9to12']['loan'] += $row['Week_9to12']['loan'];
                                $totals['Week_9to12']['amount'] += $row['Week_9to12']['amount'];
                                $totals['Week_13plus']['loan'] += $row['Week_13plus']['loan'];
                                $totals['Week_13plus']['amount'] += $row['Week_13plus']['amount'];
                                $totals['OC_Clients_Loan'] += $row['OC_Clients_Loan'];
                                $totals['Total_Arrears_Loan'] += $row['Total_Arrears_Loan'];
                                $totals['Total_Arrear_Customer'] += $row['Total_Arrear_Customer'];
                                $totals['Total_Capital_Arrears'] += $row['Total_Capital_Arrears'];
                                $totals['Total_Interest_Arrears'] += $row['Total_Interest_Arrears'];
                            }
                            $avgDebtorRatio = count($data) > 0 ? array_sum(array_column($data->toArray(), 'Debtor_Ratio')) / count($data) : 0;
                        @endphp

                        <div class="table-wrapper mt-4">
                            <table class="table table-bordered table-hover table-sm report-table">
                                <thead>
                                    <tr>
                                        <th>IN.NO</th>
                                        <th>Loan Officer</th>
                                        <th>Arrears</th>
                                        <th>Debtor Ratio</th>
                                        <th colspan="2" class="text-center">01 Week</th>
                                        <th colspan="2" class="text-center">1 to 3 Week</th>
                                        <th colspan="2" class="text-center">4 to 8 Week</th>
                                        <th colspan="2" class="text-center">9 to 12 Week</th>
                                        <th colspan="2" class="text-center">13< Week</th>
                                        <th colspan="2" class="text-center">OC Clients</th>
                                        <th>Total Arrears Loan</th>
                                        <th>Total Arrear Customer</th>
                                        <th>Total Capital Arrears</th>
                                        <th>Total Interest Arrears</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4"></th>
                                        <th class="text-center">Loan</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Loan</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Loan</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Loan</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Loan</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Loan</th>
                                        <th class="text-center">Amount</th>
                                        <th colspan="4"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $index => $row)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $row['Loan_Officer'] }}</td>
                                            <td class="text-end">{{ number_format($row['Arrears'], 2) }}</td>
                                            <td class="text-end">{{ number_format($row['Debtor_Ratio'], 2) }}%</td>
                                            <td class="text-end">{{ number_format($row['Week_01']['loan'], 0) }}</td>
                                            <td class="text-end">{{ number_format($row['Week_01']['amount'], 2) }}</td>
                                            <td class="text-end">{{ number_format($row['Week_1to3']['loan'], 0) }}</td>
                                            <td class="text-end">{{ number_format($row['Week_1to3']['amount'], 2) }}</td>
                                            <td class="text-end">{{ number_format($row['Week_4to8']['loan'], 0) }}</td>
                                            <td class="text-end">{{ number_format($row['Week_4to8']['amount'], 2) }}</td>
                                            <td class="text-end">{{ number_format($row['Week_9to12']['loan'], 0) }}</td>
                                            <td class="text-end">{{ number_format($row['Week_9to12']['amount'], 2) }}</td>
                                            <td class="text-end">{{ number_format($row['Week_13plus']['loan'], 0) }}</td>
                                            <td class="text-end">{{ number_format($row['Week_13plus']['amount'], 2) }}</td>
                                            <td class="text-end">{{ number_format($row['OC_Clients_Loan'], 0) }}</td>
                                            <td class="text-end">{{ number_format($row['OC_Clients_Amount'], 2) }}</td>
                                            <td class="text-end">{{ number_format($row['Total_Arrears_Loan'], 0) }}</td>
                                            <td class="text-end">{{ number_format($row['Total_Arrear_Customer'], 0) }}</td>
                                            <td class="text-end">{{ number_format($row['Total_Capital_Arrears'], 2) }}</td>
                                            <td class="text-end">{{ number_format($row['Total_Interest_Arrears'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="20" class="text-center">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">TOTAL</th>
                                        <th class="text-end">{{ number_format($totals['Arrears'], 2) }}</th>
                                        <th class="text-end">{{ number_format($avgDebtorRatio, 2) }}%</th>
                                        <th class="text-end">{{ number_format($totals['Week_01']['loan'], 0) }}</th>
                                        <th class="text-end">{{ number_format($totals['Week_01']['amount'], 2) }}</th>
                                        <th class="text-end">{{ number_format($totals['Week_1to3']['loan'], 0) }}</th>
                                        <th class="text-end">{{ number_format($totals['Week_1to3']['amount'], 2) }}</th>
                                        <th class="text-end">{{ number_format($totals['Week_4to8']['loan'], 0) }}</th>
                                        <th class="text-end">{{ number_format($totals['Week_4to8']['amount'], 2) }}</th>
                                        <th class="text-end">{{ number_format($totals['Week_9to12']['loan'], 0) }}</th>
                                        <th class="text-end">{{ number_format($totals['Week_9to12']['amount'], 2) }}</th>
                                        <th class="text-end">{{ number_format($totals['Week_13plus']['loan'], 0) }}</th>
                                        <th class="text-end">{{ number_format($totals['Week_13plus']['amount'], 2) }}</th>
                                        <th class="text-end">{{ number_format($totals['OC_Clients_Loan'], 0) }}</th>
                                        <th class="text-end">-</th>
                                        <th class="text-end">{{ number_format($totals['Total_Arrears_Loan'], 0) }}</th>
                                        <th class="text-end">{{ number_format($totals['Total_Arrear_Customer'], 0) }}</th>
                                        <th class="text-end">{{ number_format($totals['Total_Capital_Arrears'], 2) }}</th>
                                        <th class="text-end">{{ number_format($totals['Total_Interest_Arrears'], 2) }}</th>
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
            
            if (!data || data.length === 0) {
                alert('No data to export.');
                return;
            }

            // Build headers
            var headers = [
                'IN.NO',
                'Loan Officer',
                'Arrears',
                'Debtor Ratio',
                '01_Week_Loan',
                '01_Week_Amount',
                '1to3_Week_Loan',
                '1to3_Week_Amount',
                '4to8_Week_Loan',
                '4to8_Week_Amount',
                '9to12_Week_Loan',
                '9to12_Week_Amount',
                '13plus_Week_Loan',
                '13plus_Week_Amount',
                'OC_Clients_Loan',
                'OC_Clients_Amount',
                'Total_Arrears_Loan',
                'Total_Arrear_Customer',
                'Total_Capital_Arrears',
                'Total_Interest_Arrears'
            ];

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
                    toRaw(row.Arrears),
                    toRaw(row.Debtor_Ratio),
                    toRaw(row.Week_01.loan),
                    toRaw(row.Week_01.amount),
                    toRaw(row.Week_1to3.loan),
                    toRaw(row.Week_1to3.amount),
                    toRaw(row.Week_4to8.loan),
                    toRaw(row.Week_4to8.amount),
                    toRaw(row.Week_9to12.loan),
                    toRaw(row.Week_9to12.amount),
                    toRaw(row.Week_13plus.loan),
                    toRaw(row.Week_13plus.amount),
                    toRaw(row.OC_Clients_Loan),
                    toRaw(row.OC_Clients_Amount),
                    toRaw(row.Total_Arrears_Loan),
                    toRaw(row.Total_Arrear_Customer),
                    toRaw(row.Total_Capital_Arrears),
                    toRaw(row.Total_Interest_Arrears)
                ];

                lines.push(line.join(','));
            });

            // Add totals row
            var totals = @json($totals);
            var avgDebtorRatio = @json($avgDebtorRatio);
            var totalLine = [
                '',
                'TOTAL',
                toRaw(totals.Arrears),
                toRaw(avgDebtorRatio),
                toRaw(totals.Week_01.loan),
                toRaw(totals.Week_01.amount),
                toRaw(totals.Week_1to3.loan),
                toRaw(totals.Week_1to3.amount),
                toRaw(totals.Week_4to8.loan),
                toRaw(totals.Week_4to8.amount),
                toRaw(totals.Week_9to12.loan),
                toRaw(totals.Week_9to12.amount),
                toRaw(totals.Week_13plus.loan),
                toRaw(totals.Week_13plus.amount),
                toRaw(totals.OC_Clients_Loan),
                '-',
                toRaw(totals.Total_Arrears_Loan),
                toRaw(totals.Total_Arrear_Customer),
                toRaw(totals.Total_Capital_Arrears),
                toRaw(totals.Total_Interest_Arrears)
            ];

            lines.push(totalLine.join(','));

            // Create and download file
            var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'Arrears_Executive_Summary.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });
    </script>
@endsection
