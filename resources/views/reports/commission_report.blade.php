@extends('layout.admin')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .card-soft{ border:1px solid #e9ecef; border-radius:12px; }

        .summary-card{
            border:1px solid #e9ecef; border-radius:14px; padding:12px 14px;
            background:#fff;
            min-width: 220px;
        }
        .summary-card .t1{ font-weight:800; font-size:13px; }
        .summary-card .t2{ font-weight:900; font-size:22px; }
        .summary-card .tag{ font-size:11px; padding:3px 8px; border-radius:999px; }
        .tag-collector{ background:#e7f1ff; color:#0d6efd; }
        .tag-commission{ background:#f1f3f5; color:#495057; }

        table th, table td{ white-space: nowrap; font-size: 13px; vertical-align: middle; }
        .table thead th{ position: sticky; top: 0; z-index: 3; }

        /* totals row styling */
        .totals-row td{
            background:#0f172a !important;
            color:#fff !important;
            font-weight:900 !important;
        }

        .grand-total-bar{
            margin-top: 8px;
            display:flex;
            justify-content:flex-end;
            gap:10px;
            font-weight:900;
            font-size: 14px;
        }
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }


    </style>
@endsection

@section('content')
    <div class="container-fluid mt-3">

        <div class="card card-soft">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 align-items-end">
                    <div style="min-width:240px">
                        <label class="form-label fw-bold mb-1">Branch</label>
                        <select class="form-select" id="branch_id">
                            <option value="0">-- Select Branch --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->branch_id }}">{{ $b->Name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="min-width:200px">
                        <label class="form-label fw-bold mb-1">Date From</label>
                        <input type="date" class="form-control" id="date_from" value="{{ $dateFrom }}">
                    </div>

                    <div style="min-width:200px">
                        <label class="form-label fw-bold mb-1">Date To</label>
                        <input type="date" class="form-control" id="date_to" value="{{ $dateTo }}">
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" id="btnLoad">
                            <i class="fa-solid fa-rotate me-1"></i> Load
                        </button>

                        <button class="btn btn-dark" id="btnPrint">
                            <i class="fa-solid fa-print me-1"></i> Print
                        </button>
                        <button class="btn btn-success" id="btnExcel">
                            <i class="fa-solid fa-file-excel me-1"></i> Excel
                        </button>

                    </div>
                </div>

                <hr class="my-3">

                <div class="d-flex flex-wrap gap-2" id="summaryWrap"></div>

                <div class="table-responsive mt-3" style="max-height: 65vh;">
                    <table class="table table-bordered table-hover align-middle" id="reportTable">
                        <thead class="table-dark"></thead>
                        <tbody>
                        <tr><td class="text-center text-muted p-4">Select branch + date range and click Load.</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Grand Total Commission (UI) -->
                <div class="grand-total-bar" id="grandTotalBar" style="display:none;">
                    <div>Grand Total Commission :</div>
                    <div id="grandTotalValue">0.00</div>
                </div>

            </div>
        </div>

    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <script>
        let PEOPLE = [];
        let ROWS = [];
        let META = {};
        let COLUMN_TOTALS = {};
        let GRAND_TOTAL = 0;

        const money = (v) => Number(v || 0).toFixed(2);

        const buildSummary = (summary, meta) => {
            const $wrap = $("#summaryWrap");
            $wrap.html("");

            // Top totals cards
            const topCards = `
                <div class="summary-card">
                    <div class="t1 text-muted">Total Interest</div>
                    <div class="t2">${money(meta.total_interest)}</div>
                </div>
                <div class="summary-card">
                    <div class="t1 text-muted">Collectors Total</div>
                    <div class="t2">${money(meta.collector_total)}</div>
                </div>
                <div class="summary-card">
                    <div class="t1 text-muted">Other Commission Total</div>
                    <div class="t2">${money(meta.commission_total)}</div>
                </div>
            `;
            $wrap.append(topCards);

            // Per person cards
            summary.forEach(s => {
                const tag = s.type === 'Collector'
                    ? `<span class="tag tag-collector">Collector</span>`
                    : `<span class="tag tag-commission">Commission</span>`;

                $wrap.append(`
                    <div class="summary-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="t1">${s.name}</div>
                            ${tag}
                        </div>
                        <div class="t2 mt-1">${money(s.total)}</div>
                    </div>
                `);
            });
        };

        /**
         * Build table totals in frontend if backend didn't send.
         * But your backend already sends columnTotals/grandTotal, so this is a safe fallback.
         */
        const computeTotalsFromRows = (people, rows) => {
            const totals = {};
            people.forEach(p => totals[p.id] = 0);

            rows.forEach(r => {
                people.forEach(p => {
                    const v = (r.commissions && r.commissions[p.id]) ? Number(r.commissions[p.id]) : 0;
                    totals[p.id] += v;
                });
            });

            const gt = Object.values(totals).reduce((a,b)=>a+b,0);
            return { totals, grandTotal: gt };
        };

        const buildTable = (people, rows, meta, columnTotals, grandTotal) => {
            const $thead = $("#reportTable thead");
            const $tbody = $("#reportTable tbody");

            // head
            let h = `<tr>
                <th style="min-width:60px;">#</th>
                <th style="min-width:140px;">Loan No</th>
                <th style="min-width:240px;">Customer</th>
                <th class="text-end" style="min-width:120px;">Capital</th>
                <th class="text-center" style="min-width:90px;">Months</th>
                <th class="text-end" style="min-width:130px;">Interest</th>
                <th class="text-center" style="min-width:120px;">Settled Date</th>
            `;

            people.forEach(p => {
                h += `<th class="text-end" style="min-width:160px;">
                    ${p.full_name}<br>
                    <span class="badge ${p.type === 'Collector' ? 'bg-primary' : 'bg-secondary'}">${p.type}</span>
                </th>`;
            });

            h += `</tr>`;
            $thead.html(h);

            // body
            if (!rows || rows.length === 0) {
                $tbody.html(`<tr>
                    <td colspan="${7 + people.length}" class="text-center text-muted p-4">
                        No settled loans found for the selected period.
                    </td>
                </tr>`);

                $("#grandTotalBar").hide();
                $("#grandTotalValue").text("0.00");
                return;
            }

            let html = "";
            rows.forEach(r => {
                html += `<tr>
                    <td>${r.no}</td>
                    <td class="fw-bold">${r.loan_no}</td>
                    <td>${r.name}</td>
                    <td class="text-end">${money(r.capital)}</td>
                    <td class="text-center">${r.months}</td>
                    <td class="text-end fw-bold">${money(r.interest)}</td>
                    <td class="text-center">${r.settle_date || '-'}</td>
                `;

                people.forEach(p => {
                    const v = (r.commissions && r.commissions[p.id]) ? r.commissions[p.id] : 0;
                    html += `<td class="text-end">${money(v)}</td>`;
                });

                html += `</tr>`;
            });

            // ✅ TOTAL ROW (same logic as print)
            // TOTAL label should cover columns: #, Loan No, Customer, Capital, Months  => 5 cols
            // Interest total under Interest column (6th column)
            // Settled Date column keep "-"
            html += `<tr >
                <td colspan="5" >TOTAL</td>
                <td class="text-end fw-bold">${money(meta.total_interest)}</td>
                <td class="text-center">-</td>
            `;

            people.forEach(p => {
                html += `<td class="text-end fw-bold">${money(columnTotals[p.id] || 0)}</td>`;
            });

            html += `</tr>`;

            $tbody.html(html);

            // Grand Total bar
            $("#grandTotalBar").show();
            $("#grandTotalValue").text(money(grandTotal || 0));
        };

        const loadReport = () => {
            const branch_id = parseInt($("#branch_id").val() || "0");
            const date_from = $("#date_from").val();
            const date_to = $("#date_to").val();

            if (branch_id <= 0) {
                Swal.fire("Warning", "Please select a branch.", "warning");
                return;
            }
            if (!date_from || !date_to) {
                Swal.fire("Warning", "Please select date range.", "warning");
                return;
            }

            $.ajax({
                type: "GET",
                url: "{{ route('reports.commission.load') }}",
                data: { branch_id, date_from, date_to },
                success: function(res){
                    PEOPLE = res.people || [];
                    ROWS = res.rows || [];
                    META = res.meta || {};

                    // Prefer backend totals (recommended)
                    COLUMN_TOTALS = res.columnTotals || null;
                    GRAND_TOTAL = res.grandTotal || null;

                    // If backend didn't send totals, compute from rows
                    if (!COLUMN_TOTALS || GRAND_TOTAL === null) {
                        const computed = computeTotalsFromRows(PEOPLE, ROWS);
                        COLUMN_TOTALS = computed.totals;
                        GRAND_TOTAL = computed.grandTotal;
                    }

                    buildSummary(res.summary || [], META);
                    buildTable(PEOPLE, ROWS, META, COLUMN_TOTALS, GRAND_TOTAL);
                },
                error: function(xhr){
                    Swal.fire("Error", xhr.responseJSON?.message || "Failed to load report.", "error");
                }
            });
        };

        const openPrint = () => {
            const branch_id = parseInt($("#branch_id").val() || "0");
            const date_from = $("#date_from").val();
            const date_to = $("#date_to").val();

            if (branch_id <= 0) {
                Swal.fire("Warning", "Please select a branch.", "warning");
                return;
            }
            if (!date_from || !date_to) {
                Swal.fire("Warning", "Please select date range.", "warning");
                return;
            }

            const url = `{{ route('reports.commission.print') }}?branch_id=${branch_id}&date_from=${date_from}&date_to=${date_to}`;
            const w = window.open(url, "_blank");

            if (!w) {
                Swal.fire({
                    icon: "warning",
                    title: "Popup Blocked",
                    html: "Please allow popups for this site to open the print preview.",
                });
            }
        };

        $(document).ready(function(){
            $("#btnLoad").on("click", function(e){ e.preventDefault(); loadReport(); });
            $("#btnPrint").on("click", function(e){ e.preventDefault(); openPrint(); });
        });


        const fmt2 = (n) => Number(n || 0).toFixed(2);

        const safeText = (v) => (v === null || v === undefined) ? "" : String(v);

        const makeExcel = () => {
            if (!PEOPLE.length) {
                Swal.fire("Warning", "Please click Load first.", "warning");
                return;
            }

            // ===== Build 2D array (AOA) for SheetJS =====
            const aoa = [];

            const branchName = $("#branch_id option:selected").text().trim();
            const date_from = $("#date_from").val();
            const date_to = $("#date_to").val();

            // Title / Meta rows
            aoa.push(["Commission Report"]);
            aoa.push(["Branch", branchName]);
            aoa.push(["Period", `${date_from} to ${date_to}`]);
            aoa.push([]);

            aoa.push(["Total Interest", fmt2(META.total_interest)]);
            aoa.push(["Collectors Total", fmt2(META.collector_total)]);
            aoa.push(["Other Commission Total", fmt2(META.commission_total)]);
            aoa.push([]);

            // Header row (table)
            const header = [
                "#", "Loan No", "Customer", "Capital", "Months", "Interest", "Settled Date"
            ];

            PEOPLE.forEach(p => {
                header.push(`${safeText(p.full_name)} (${safeText(p.type)})`);
            });

            aoa.push(header);

            // Body rows
            ROWS.forEach(r => {
                const row = [
                    r.no,
                    safeText(r.loan_no),
                    safeText(r.name),
                    Number(r.capital || 0),
                    Number(r.months || 0),
                    Number(r.interest || 0),
                    safeText(r.settle_date || "")
                ];

                PEOPLE.forEach(p => {
                    const v = (r.commissions && r.commissions[p.id]) ? r.commissions[p.id] : 0;
                    row.push(Number(v || 0));
                });

                aoa.push(row);
            });

            // ===== Totals row (below table) =====
            // Column totals from summary cards
            const totalsMap = {};
            const summaryCards = $("#summaryWrap .summary-card"); // not needed, but keep safe

            // We already have totals in the API summary (best)
            // If you still have 'summary' array in the response, store it globally (recommended)
            // For now, compute totals by summing rows:
            const peopleTotals = {};
            PEOPLE.forEach(p => peopleTotals[p.id] = 0);

            let interestTotal = 0;
            ROWS.forEach(r => {
                interestTotal += Number(r.interest || 0);
                PEOPLE.forEach(p => {
                    const v = (r.commissions && r.commissions[p.id]) ? r.commissions[p.id] : 0;
                    peopleTotals[p.id] += Number(v || 0);
                });
            });

            const totalsRow = ["TOTAL", "", "", "", "", Number(interestTotal || 0), ""];
            PEOPLE.forEach(p => totalsRow.push(Number(peopleTotals[p.id] || 0)));
            aoa.push(totalsRow);

            // Grand total commission (sum of all people totals)
            let grand = 0;
            Object.values(peopleTotals).forEach(v => grand += Number(v || 0));
            aoa.push([]);
            aoa.push(["Grand Total Commission", fmt2(grand)]);

            // ===== Create workbook =====
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(aoa);

            // ===== Basic formatting (column widths) =====
            // Fixed columns widths + dynamic people columns
            const colWidths = [
                { wch: 6 },   // #
                { wch: 18 },  // Loan No
                { wch: 28 },  // Customer
                { wch: 14 },  // Capital
                { wch: 10 },  // Months
                { wch: 14 },  // Interest
                { wch: 14 },  // Settled Date
            ];
            PEOPLE.forEach(() => colWidths.push({ wch: 16 }));
            ws["!cols"] = colWidths;

            // Freeze header row (where table header is)
            // Table header row index in aoa:
            // 1 Title, 1 Branch, 1 Period, 1 blank, 3 totals, 1 blank => header is at row 10 (1-based)
            // Let's calculate dynamically:
            const headerRowIndex = aoa.findIndex(r => r[0] === "#" && r[1] === "Loan No") + 1; // 1-based
            ws["!freeze"] = { xSplit: 0, ySplit: headerRowIndex }; // freeze above header

            XLSX.utils.book_append_sheet(wb, ws, "Commission Report");

            // ===== Download =====
            const fileName = `Commission_Report_${branchName}_${date_from}_to_${date_to}.xlsx`
                .replace(/\s+/g, "_")
                .replace(/[^\w\-\.]/g, "");

            XLSX.writeFile(wb, fileName);
        };


        $("#btnExcel").on("click", function(e){
            e.preventDefault();
            makeExcel();
        });

    </script>
@endsection
