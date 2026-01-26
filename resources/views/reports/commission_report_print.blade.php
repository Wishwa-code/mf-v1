<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Commission Report</title>

    <style>
        @page { size: A4 landscape; margin: 8mm; }

        html, body { background:#fff; }

        body{
            margin:0;
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial;
            color:#0f172a;
            font-size:11px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .paper{ padding: 10px 12px; }

        .card{
            border:1px solid #e5e7eb;
            border-radius:14px;
            background:#fff;
            padding:14px 16px;
        }

        .header{
            display:flex;
            justify-content:space-between;
            gap:12px;
            align-items:flex-start;
        }

        .h-title{
            font-size:18px;
            font-weight:900;
            margin:0;
            letter-spacing:.2px;
        }
        .h-sub{
            margin-top:2px;
            color:#64748b;
            font-weight:600;
            font-size:11px;
        }

        .pill{
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:5px 10px;
            border-radius:999px;
            background:#0f172a;
            color:#fff;
            font-weight:800;
            font-size:11px;
            white-space:nowrap;
        }

        .meta{
            text-align:right;
            color:#334155;
            font-weight:600;
            font-size:11px;
        }

        .rule{
            height:1px;
            background:#eef2f7;
            margin:10px 0;
        }

        /* ===== Summary strip ===== */
        .summary{
            display:grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap:8px;
        }

        .sum{
            border:1px solid #eef2f7;
            border-radius:12px;
            padding:10px 10px;
            min-height:60px;
        }

        .sum .label{
            color:#64748b;
            font-size:10px;
            font-weight:800;
            margin-bottom:4px;
            line-height:1.2;
            display:flex;
            justify-content:space-between;
            gap:8px;
        }

        .sum .value{
            font-size:15px;
            font-weight:900;
            line-height:1.1;
        }

        .badge{
            font-size:9px;
            font-weight:900;
            padding:2px 8px;
            border-radius:999px;
            display:inline-block;
            white-space:nowrap;
        }
        .b-collector{ background:#2563eb; color:#fff; }
        .b-commission{ background:#64748b; color:#fff; }

        /* ===== Table ===== */
        .table-card{
            margin-top:10px;
            border:1px solid #e5e7eb;
            border-radius:14px;
            overflow:hidden;
            background:#fff;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        thead{
            background:#0f172a;
            color:#fff;
        }
        thead th{
            font-size:10px;
            font-weight:900;
            padding:7px 8px;
            white-space:nowrap;
        }

        tbody td{
            font-size:10px;
            padding:7px 8px;
            border-bottom:1px solid #eef2f7;
            white-space:nowrap;
            vertical-align:middle;
        }

        tbody tr:nth-child(even){
            background:#f8fafc;
        }

        .td-wrap{
            white-space:normal !important;
            max-width:240px;
        }

        .num{ text-align:right; }
        .ctr{ text-align:center; }
        .strong{ font-weight:900; }

        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }

        /* ===== Totals row ===== */
        .totals-row td{
            background:#0f172a !important;
            color:#fff !important;
            font-weight:900 !important;
            border-bottom:none !important;
        }

        .grand-total{
            margin-top:6px;
            display:flex;
            justify-content:flex-end;
            gap:10px;
            color:#0f172a;
            font-weight:900;
            font-size:11px;
        }

        /* ===== Sign ===== */
        .sign-row{
            margin-top:10px;
            display:grid;
            grid-template-columns: repeat(3, 1fr);
            gap:10px;
        }
        .sign{
            border:1px dashed #94a3b8;
            border-radius:12px;
            padding:10px 12px;
            min-height:70px;
        }
        .sign .t{
            font-weight:900;
            font-size:11px;
            margin-bottom:6px;
        }
        .sign .l{
            font-size:10px;
            color:#64748b;
        }

        .tight thead th, .tight tbody td { padding:6px 6px; font-size:9.5px; }

        @media print {
            a[href]:after { content: "" !important; }
        }
    </style>
</head>

<body>
<div class="paper">

    <div class="card">
        <div class="header">
            <div>
                <h1 class="h-title">Commission Report</h1>
                <div class="h-sub">
                    Settled Loans • Branch: <span class="strong">{{ $branch->Name ?? '-' }}</span>
                </div>
            </div>

            <div class="meta">
                <div class="pill">{{ $meta['date_from'] }} → {{ $meta['date_to'] }}</div>
                <div style="margin-top:4px;color:#64748b;">
                    Printed: {{ now()->format('Y-m-d H:i') }}
                </div>
            </div>
        </div>

        <div class="rule"></div>

        <div class="summary">
            <div class="sum">
                <div class="label">Total Interest</div>
                <div class="value">{{ number_format($meta['total_interest'] ?? 0, 2) }}</div>
            </div>
            <div class="sum">
                <div class="label">Collectors Total</div>
                <div class="value">{{ number_format($meta['collector_total'] ?? 0, 2) }}</div>
            </div>
            <div class="sum">
                <div class="label">Other Commission Total</div>
                <div class="value">{{ number_format($meta['commission_total'] ?? 0, 2) }}</div>
            </div>

            @foreach($summary as $s)
                <div class="sum">
                    <div class="label">
                        <span style="max-width:125px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $s['name'] }}
                        </span>
                        <span class="badge {{ $s['type']==='Collector' ? 'b-collector' : 'b-commission' }}">
                            {{ $s['type'] }}
                        </span>
                    </div>
                    <div class="value">{{ number_format($s['total'] ?? 0, 2) }}</div>
                </div>
            @endforeach
        </div>
    </div>

    @php
        // If there are many people columns, tighten table padding/fonts a bit
        $tightClass = (count($people) >= 7) ? 'tight' : '';
    @endphp

    <div class="table-card">
        <table class="{{ $tightClass }}">
            <thead>
            <tr>
                <th style="width:32px;">#</th>
                <th style="width:150px;">Loan No</th>
                <th>Customer</th>
                <th class="num" style="width:110px;">Capital</th>
                <th class="ctr" style="width:70px;">Months</th>
                <th class="num" style="width:110px;">Interest</th>
                <th class="ctr" style="width:92px;">Settled</th>

                @foreach($people as $p)
                    <th class="num" style="min-width:110px;">
                        <div style="font-weight:900; max-width:140px; overflow:hidden; text-overflow:ellipsis;">
                            {{ $p['full_name'] ?? '' }}
                        </div>
                        <span class="badge {{ ($p['type'] ?? '')==='Collector' ? 'b-collector' : 'b-commission' }}">
                            {{ $p['type'] ?? '' }}
                        </span>
                    </th>
                @endforeach
            </tr>
            </thead>

            <tbody>
            @if(count($rows) == 0)
                <tr>
                    <td colspan="{{ 7 + count($people) }}" class="ctr" style="padding:22px;color:#64748b;">
                        No settled loans found for the selected period.
                    </td>
                </tr>
            @endif

            @foreach($rows as $r)
                <tr>
                    <td class="ctr">{{ $r['no'] }}</td>
                    <td class="strong">{{ $r['loan_no'] }}</td>
                    <td class="td-wrap">{{ $r['name'] }}</td>
                    <td class="num">{{ number_format($r['capital'],2) }}</td>
                    <td class="ctr">{{ $r['months'] }}</td>
                    <td class="num strong">{{ number_format($r['interest'],2) }}</td>
                    <td class="ctr">{{ $r['settle_date'] ?? '-' }}</td>

                    @foreach($people as $p)
                        <td class="num">{{ number_format($r['commissions'][$p['id']] ?? 0, 2) }}</td>
                    @endforeach
                </tr>
            @endforeach

            <!-- ✅ TOTALS ROW -->
            <tr class="totals-row">
                <td colspan="5" class="ctr">TOTAL</td>

                <!-- Interest total should be under Interest column -->
                <td class="num">{{ number_format($meta['total_interest'] ?? 0, 2) }}</td>

                <!-- Settled column - keep blank or dash -->
                <td class="ctr">-</td>

                @foreach($people as $p)
                    <td class="num">
                        {{ number_format($columnTotals[$p['id']] ?? 0, 2) }}
                    </td>
                @endforeach
            </tr>

            </tbody>
        </table>
    </div>

    <div class="grand-total">
        <div>Grand Total Commission :</div>
        <div>{{ number_format($grandTotal ?? 0, 2) }}</div>
    </div>

    <div class="sign-row">
        <div class="sign">
            <div class="t">Prepared By</div>
            <div class="l">Signature: __________________________</div>
        </div>
        <div class="sign">
            <div class="t">Checked By</div>
            <div class="l">Signature: __________________________</div>
        </div>
        <div class="sign">
            <div class="t">Approved By</div>
            <div class="l">Signature: __________________________</div>
        </div>
    </div>
</div>

<script>
    // ✅ Auto open print dialog on load
    window.addEventListener('load', () => {
        setTimeout(() => window.print(), 250);
    });

    // Optional: auto close after printing (enable if you want)
    // window.addEventListener('afterprint', () => window.close());
</script>

</body>
</html>
