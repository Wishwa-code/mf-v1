@extends('layout.admin')

@section('head')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        thead { background-color: #d9edf7; color: #31708f; }
        .bg-purple th { color: #e1e1e1 !important; }
        .bg-purple { background-color: #1A2942 !important; color: white !important; }
        #loan_table { width: 100% !important; border-collapse: separate; border-spacing: 0; font-size: 0.92rem; }
        #loan_table th, #loan_table td { padding: 8px 10px !important; vertical-align: middle !important; white-space: nowrap; }
        #loan_table tbody tr:nth-child(even) { background: #fafafa; }
        #loan_table tfoot th, #loan_table tfoot td { font-weight: 700; background: #f3f6fb; border-top: 2px solid #cdd8ea; }
        .text-end { text-align: right !important; }
        .table-container { max-height: 70vh; overflow: auto; border: 1px solid #e5e9f2; border-radius: 8px; }
        thead.sticky-top { position: sticky; top: 0; z-index: 5; }
        #loaderOverlay { position: fixed; inset: 0; background: rgba(255,255,255,0.7); display: none; align-items: center; justify-content: center; z-index: 2000; }
        .loader { width: 56px; height: 56px; border-radius: 50%; border: 6px solid #1A2942; border-top-color: transparent; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    {{-- Expose products to JS for dynamic columns --}}
    @php
        // $product is an Eloquent collection; no need for collect()
        $PRODUCT_COLUMNS = $product->map(function ($p) {
            return [
                'id'   => $p->idLoan_Category,
                'code' => $p->Product_code,
                'name' => $p->Name,
            ];
        })->values();
    @endphp
    <script>
        window.PRODUCT_COLUMNS = @json($PRODUCT_COLUMNS);
    </script>

@endsection

@section('content')
    <div id="loaderOverlay"><div class="loader"></div></div>

    <div class="container-fluid">
        <div class="row"><div class="col-12"><div class="page-title-box"><h4 class="page-title">DEPLETION REPORT EXECUTIVE SUMMARY</h4></div></div></div>

        <div class="row">
            <div class="col-12">
                <div class="card"><div class="card-body">
                        <form id="filterForm" class="p-3 border rounded shadow-sm bg-white">
                            @csrf
                            <div class="row g-3 align-items-end">
                                <!-- From Date -->
                                <div class="col-lg-3 col-md-6">
                                    <label for="from" class="form-label fw-bold">From</label>
                                    <input type="date" id="from" name="from" class="form-control"
                                           value="{{ now()->startOfMonth()->toDateString() }}">
                                </div>

                                <!-- To Date -->
                                <div class="col-lg-3 col-md-6">
                                    <label for="to" class="form-label fw-bold">To</label>
                                    <input type="date" id="to" name="to" class="form-control"
                                           value="{{ now()->endOfMonth()->toDateString() }}">
                                </div>

                                <!-- Product (hidden) -->
                                <div class="col-lg-3 col-md-6" hidden>
                                    <label for="product" class="form-label fw-bold">Product</label>
                                    <select class="form-control select2" id="product" name="product" data-allow-clear="1">
                                        <option value="" selected>All Products</option>
                                    </select>
                                </div>

                                <!-- Loan Officer -->
                                <div class="col-lg-3 col-md-6">
                                    <label for="loan_officer" class="form-label fw-bold">Loan Officer</label>
                                    <select class="form-control select2" id="loan_officer" name="loan_officer" data-allow-clear="1">
                                        <option value="">All Officers</option>
                                        @foreach($officer as $item)
                                            <option value="{{ $item->id }}">{{ $item->Full_Name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Search Button -->
                                <div class="col-lg-3 col-md-6">
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="bi bi-search me-1"></i> Search
                                    </button>
                                </div>

                                <!-- Export Button -->
                                <div class="col-lg-3 col-md-6">
                                    <button id="exportExcel" type="button" class="btn btn-success w-100">
                                        <i class="bi bi-file-earmark-excel me-1"></i> Export to Excel (CSV)
                                    </button>
                                </div>
                            </div>
                        </form>


                        <hr>

                        <div class="table-container">
                            <table class="table table-centered mb-0" id="loan_table">
                                <thead class="sticky-top bg-purple">
                                <tr>
                                    {{-- Product column removed --}}
                                    <th>Loan Officer</th>
                                    <th>Beginning Stock</th>
                                    <th>Current/End Stock (capital outstanding)</th>
                                    <th>Investment</th>
                                    <th>Depletion (collected capital amount)</th>
                                    <th>Collection (capital + interest + savings)</th>
                                    <th>Arrears</th>
                                    <th>Portfolio</th>
                                    <th>Debtor Ratio</th>
                                    <th>Penalty Arrears</th>
                                    <th>Total Loans</th>
                                    <th>OC Loans</th>
                                    <th>Total Clients</th>
                                    <th>OC Clients</th>
                                    <th>Active Clients</th>
                                    <th>Total Outstanding Balance</th>

                                    {{-- Dynamic product-wise loan counts --}}
                                    @foreach($product as $p)
                                        <th>{{ $p->Product_code }}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                <tr>
                                    <th class="text-end">TOTAL:</th>  {{-- footer col 0: label --}}
                                    <th></th>                          {{-- footer col 1: Loan Officer (blank) --}}
                                    <th class="text-end"></th>        {{-- 2 Beginning --}}
                                    <th class="text-end"></th>        {{-- 3 End --}}
                                    <th class="text-end"></th>        {{-- 4 Investment --}}
                                    <th class="text-end"></th>        {{-- 5 Depletion --}}
                                    <th class="text-end"></th>        {{-- 6 Collection --}}
                                    <th class="text-end"></th>        {{-- 7 Arrears --}}
                                    <th class="text-end"></th>        {{-- 8 Portfolio --}}
                                    <th class="text-end"></th>        {{-- 9 Debtor Ratio --}}
                                    <th class="text-end"></th>        {{-- 10 Penalty Arrears --}}
                                    <th class="text-end"></th>        {{-- 11 Total Loans --}}
                                    <th class="text-end"></th>        {{-- 12 OC Loans --}}
                                    <th class="text-end"></th>        {{-- 13 Total Clients --}}
                                    <th class="text-end"></th>        {{-- 14 OC Clients --}}
                                    <th class="text-end"></th>        {{-- 15 Active Clients --}}
                                    <th class="text-end"></th>        {{-- 16 Total Outstanding Balance --}}
                                    {{-- Dynamic product totals placeholders --}}
                                    @foreach($product as $p)
                                        <th class="text-end"></th>
                                    @endforeach
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div></div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>

    <script>
        function showLoader(show){
            var el = document.getElementById('loaderOverlay');
            if (el) el.style.display = show ? 'flex' : 'none';
        }
        function escapeHtml(s){
            var map = {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;'};
            return String(s == null ? '' : s).replace(/[&<>"'`=\/]/g, function(c){ return map[c]; });
        }
        function fmt(n, d){
            d = (typeof d === 'number') ? d : 2;
            var num = Number(n || 0);
            return num.toLocaleString(undefined,{minimumFractionDigits:d,maximumFractionDigits:d});
        }

        function renderTable(rows){
            var $tbody = $('#loan_table tbody');
            var $tfootRow = $('#loan_table tfoot tr');
            $tbody.empty();

            var PRODUCT_COLUMNS = (window.PRODUCT_COLUMNS || []);
            var PRODUCT_IDS = PRODUCT_COLUMNS.map(function(p){ return p.id; });

            // colspan = all visible columns in THEAD (16 base + product columns)
            if (!rows || !rows.length) {
                $tbody.append('<tr><td colspan="'+(16 + PRODUCT_IDS.length)+'" class="text-center">No data for selected filters.</td></tr>');
                $tfootRow.find('th').slice(1).html('');
                return;
            }

            // Totals accumulator
            var totals = {
                Beginning_Stock: 0,
                Current_End_Stock: 0,
                Investment: 0,
                Depletion: 0,
                Collection: 0,
                Arrears: 0,
                Portfolio: 0,
                Penalty_Arrears: 0,
                Total_Loans: 0,
                OC_Loans: 0,
                Total_Clients: 0,
                OC_Clients: 0,
                Active_Clients: 0,
                Total_Outstanding_Balance: 0
            };

            // product totals: use STRING keys to match JSON object keys
            var productTotals = {};
            PRODUCT_IDS.forEach(function(id){ productTotals[String(id)] = 0; });

            rows.forEach(function(r){
                // IMPORTANT: backend key is product_counts
                var pc = r.product_counts || {}; // {"12": 34, "15": 7, ...}

                var html =
                    '<tr>'
                    + '<td>'+escapeHtml(r.Loan_Officer || "—")+'</td>'
                    + '<td class="text-end">'+fmt(r.Beginning_Stock)+'</td>'
                    + '<td class="text-end">'+fmt(r.Current_End_Stock)+'</td>'
                    + '<td class="text-end">'+fmt(r.Investment)+'</td>'
                    + '<td class="text-end">'+fmt(r.Depletion)+'</td>'
                    + '<td class="text-end">'+fmt(r.Collection)+'</td>'
                    + '<td class="text-end">'+fmt(r.Arrears)+'</td>'
                    + '<td class="text-end">'+fmt(r.Portfolio)+'</td>'
                    + '<td class="text-end">'+fmt(r.Debtor_Ratio)+'%</td>'
                    + '<td class="text-end">'+fmt(r.Penalty_Arrears || 0)+'</td>'
                    + '<td class="text-end">'+Number(r.Total_Loans || 0)+'</td>'
                    + '<td class="text-end">'+Number(r.OC_Loans || 0)+'</td>'
                    + '<td class="text-end">'+Number(r.Total_Clients || 0)+'</td>'
                    + '<td class="text-end">'+Number(r.OC_Clients || 0)+'</td>'
                    + '<td class="text-end">'+Number(r.Active_Clients || 0)+'</td>'
                    + '<td class="text-end">'+fmt((r.Total_Outstanding_Balance != null ? r.Total_Outstanding_Balance : r.Current_End_Stock))+'</td>';

                PRODUCT_IDS.forEach(function(pid){
                    var key = String(pid);
                    var c = Number((pc && pc[key]) || 0);
                    html += '<td class="text-end">'+c+'</td>';
                    productTotals[key] = (productTotals[key] || 0) + c;
                });

                html += '</tr>';
                $tbody.append(html);

                // accumulate numeric totals
                totals.Beginning_Stock           += Number(r.Beginning_Stock || 0);
                totals.Current_End_Stock         += Number(r.Current_End_Stock || 0);
                totals.Investment                += Number(r.Investment || 0);
                totals.Depletion                 += Number(r.Depletion || 0);
                totals.Collection                += Number(r.Collection || 0);
                totals.Arrears                   += Number(r.Arrears || 0);
                totals.Portfolio                 += Number(r.Portfolio || 0);
                totals.Penalty_Arrears           += Number(r.Penalty_Arrears || 0);
                totals.Total_Loans               += Number(r.Total_Loans || 0);
                totals.OC_Loans                  += Number(r.OC_Loans || 0);
                totals.Total_Clients             += Number(r.Total_Clients || 0);
                totals.OC_Clients                += Number(r.OC_Clients || 0);
                totals.Active_Clients            += Number(r.Active_Clients || 0);
                totals.Total_Outstanding_Balance += Number((r.Total_Outstanding_Balance != null ? r.Total_Outstanding_Balance : r.Current_End_Stock) || 0);
            });

            // Debtor Ratio total
            var debtorRatioTotal = totals.Current_End_Stock > 0
                ? (totals.Arrears / totals.Current_End_Stock) * 100
                : 0;

            // Footer cells (indexes):
            // 0 label | 1 (Loan Officer blank) | 2..16 base totals | 17.. products
            var cells = $tfootRow.find('th');
            cells.eq(1).html(fmt(totals.Beginning_Stock));
            cells.eq(2).html(fmt(totals.Current_End_Stock));
            cells.eq(3).html(fmt(totals.Investment));
            cells.eq(4).html(fmt(totals.Depletion));
            cells.eq(5).html(fmt(totals.Collection));
            cells.eq(6).html(fmt(totals.Arrears));
            cells.eq(7).html(fmt(totals.Portfolio));
            cells.eq(8).html(fmt(debtorRatioTotal) + '%');
            cells.eq(9).html(fmt(totals.Penalty_Arrears));
            cells.eq(10).html(fmt(totals.Total_Loans, 0));
            cells.eq(11).html(fmt(totals.OC_Loans, 0));
            cells.eq(12).html(fmt(totals.Total_Clients, 0));
            cells.eq(13).html(fmt(totals.OC_Clients, 0));
            cells.eq(14).html(fmt(totals.Active_Clients, 0));
            cells.eq(15).html(fmt(totals.Total_Outstanding_Balance));

            var startIdx = 16; // first product footer cell
            PRODUCT_IDS.forEach(function(pid, i){
                cells.eq(startIdx + i).html(fmt(productTotals[String(pid)], 0));
            });
        }


        function exportToCSV(rows){
            if (!rows || !rows.length) { alert('Nothing to export.'); return; }

            var PRODUCT_COLUMNS = (window.PRODUCT_COLUMNS || []);
            var headers = [
                'Loan Officer','Beginning Stock','Current/End Stock',
                'Investment','Depletion','Collection','Arrears','Portfolio',
                'Debtor Ratio','Penalty Arrears','Total Loans','OC Loans',
                'Total Clients','OC Clients','Active Clients','Total Outstanding Balance'
            ].concat(PRODUCT_COLUMNS.map(function(p){ return p.code; }));

            function esc(v){
                var s = (v == null ? '' : String(v));
                return /[",\n]/.test(s) ? '"' + s.replace(/"/g,'""') + '"' : s;
            }
            function toRaw(n){ return n == null ? '' : String(n).replace(/[, ]/g,''); }

            var lines = [];
            lines.push(headers.join(','));

            rows.forEach(function(r){
                var base = [
                    esc(r.Loan_Officer || '—'),
                    toRaw(r.Beginning_Stock),
                    toRaw(r.Current_End_Stock),
                    toRaw(r.Investment),
                    toRaw(r.Depletion),
                    toRaw(r.Collection),
                    toRaw(r.Arrears),
                    toRaw(r.Portfolio),
                    toRaw(r.Debtor_Ratio),
                    toRaw(r.Penalty_Arrears || 0),
                    toRaw(r.Total_Loans || 0),
                    toRaw(r.OC_Loans || 0),
                    toRaw(r.Total_Clients || 0),
                    toRaw(r.OC_Clients || 0),
                    toRaw(r.Active_Clients || 0),
                    toRaw(r.Total_Outstanding_Balance != null ? r.Total_Outstanding_Balance : r.Current_End_Stock)
                ];

                var pc = r.product_counts || {};
                PRODUCT_COLUMNS.forEach(function(p){
                    base.push(toRaw(pc[String(p.id)] || 0));
                });

                lines.push(base.join(','));
            });

            var blob = new Blob([lines.join('\n')], {type: 'text/csv;charset=utf-8;'});
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'Depletion_Executive_Summary.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }


        $(function(){
            $('.select2').select2({ placeholder: 'Select...', allowClear: true, width: '100%' });

            var lastRows = [];

            $('#filterForm').on('submit', function(e){
                e.preventDefault();

                var payload = {
                    _token: $('input[name="_token"]').val(),
                    product: $('#product').val() || null,
                    loan_officer: $('#loan_officer').val() || null,
                    from: $('#from').val() || null,
                    to: $('#to').val() || null
                };
                if (payload.from && payload.to && new Date(payload.from) > new Date(payload.to)) {
                    alert('From date cannot be after To date.');
                    return;
                }

                showLoader(true);
                $.ajax({
                    url: "{{ route('depletion.data') }}",
                    method: "POST",
                    data: payload,
                    dataType: "json"
                }).done(function(res){
                    lastRows = (res && Array.isArray(res.data)) ? res.data : [];
                    renderTable(lastRows);
                }).fail(function(xhr){
                    console.error(xhr && xhr.responseText ? xhr.responseText : xhr);
                    alert('Failed to load data. Please check the console for details.');
                }).always(function(){ showLoader(false); });
            });

            $('#exportExcel').on('click', function(){ exportToCSV(lastRows); });

            // initial load
            $('#filterForm').trigger('submit');
        });
    </script>
@endsection
