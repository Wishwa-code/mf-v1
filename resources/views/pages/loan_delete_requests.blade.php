@extends('layout.admin')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #ctxPre {
            background: #0f172a;
            color: #aee4ff;
            font-family: "Fira Code", monospace;
            border-radius: 8px;
            padding: 14px;
            line-height: 1.6;
            font-size: 13px;
            white-space: pre-wrap;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid mt-2">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">🗑️ Loan Delete Requests</h6>
                <div>
                    <span class="kpi-chip">Pending: <b>{{ $counts->pending_count ?? 0 }}</b></span>
                    <span class="kpi-chip">Approved: <b>{{ $counts->approved_count ?? 0 }}</b></span>
                    <span class="kpi-chip">Rejected: <b>{{ $counts->rejected_count ?? 0 }}</b></span>
                </div>
            </div>

            <div class="card-body">
                <form method="get" class="row gy-2 gx-3 align-items-end mb-3">
                    <div class="col-md-2">
                        <label class="form-label mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="PENDING" {{ $status==='PENDING' ? 'selected' : '' }}>Pending</option>
                            <option value="APPROVED" {{ $status==='APPROVED' ? 'selected' : '' }}>Approved</option>
                            <option value="REJECTED" {{ $status==='REJECTED' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1">Search (Loan / Customer)</label>
                        <input id="searchBox" type="text" name="search" class="form-control form-control-sm"
                               placeholder="e.g., LN-00123 or John" value="{{ $search }}">

                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary btn-sm me-2"><i class="fas fa-search"></i> Filter</button>
                        <a href="{{ route('loan.delete_requests') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </div>
                </form>

                @if($requests->isEmpty())
                    <p class="text-center text-muted my-5">No delete requests found.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Loan No</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Requested By</th>
                                <th>Requested At</th>
                                <th>Status</th>
                                <th>Approved By</th>
                                <th>Approved At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($requests as $r)
                                @php
                                    // Prefer live DB value; then context; then '-'
                                    $displayLoanNo = $r->Loan_No ?: (($r->ctx_loan_no ?? null) ?: '-');

                                    // Joined customer full name from DB if exists
                                    $joinedCustomer  = trim(($r->First_Name ?? '') . ' ' . ($r->Last_Name ?? ''));
                                    // Context carries "Customer_Name"
                                    $ctxCustomerName = $r->ctx_customer_name ?? ($r->ctx_customer ?? null); // support both keys if present

                                    $displayCustomer = $joinedCustomer !== ''
                                        ? $joinedCustomer
                                        : ($ctxCustomerName ?: '-');

                                    $displayAmountRaw = $r->Amount ?? ($r->ctx_amount ?? null);
                                    $displayAmount    = is_numeric((string)$displayAmountRaw)
                                        ? number_format((float)$displayAmountRaw, 2)
                                        : ($displayAmountRaw ?? '0.00');

                                    // Build context for View modal (safe-access with ?? to avoid undefined property notices)
                                    $ctxPayload = [
                                        'Loan No'        => $displayLoanNo,
                                        'Amount'         => $displayAmountRaw,

                                        // Customer block
                                        'Customer No'    => $r->ctx_customer_no ?? ($r->ctx_customer_id ?? null), // try number; fallback to id if needed
                                        'Customer Name'  => $displayCustomer,
                                        'Contact No'     => $r->ctx_customer_phone ?? null,

                                        'Product'        => $r->ctx_product ?? null,
                                        'Status'         => $r->status,
                                        'Requested By'   => ($r->requester_name ?: $r->requested_by),
                                        'Requested At'   => $r->requested_at,
                                        'Approved By'    => ($r->approver_name ?: $r->approved_by),
                                        'Approved At'    => $r->approved_at,
                                    ];

                                    if (!empty($r->payments_summary)) {
                                        $ctxPayload['Payments'] = [
                                            'count'  => $r->payments_summary['count'],
                                            'total'  => number_format($r->payments_summary['total'], 2),
                                        ];
                                        $ctxPayload['payments'] = $r->payments_summary['items'];
                                    }
                                @endphp

                                <tr>
                                    <td>{{ $r->id }}</td>
                                    <td><b>{{ $displayLoanNo }}</b></td>
                                    <td>{{ $displayCustomer }}</td>
                                    <td>{{ $displayAmount }}</td>
                                    <td>{{ $r->requester_name ?: $r->requested_by }}</td>
                                    <td>{{ \Carbon\Carbon::parse($r->requested_at)->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if($r->status == 'PENDING')
                                            <span class="badge-soft badge-soft-warning">Pending</span>
                                        @elseif($r->status == 'APPROVED')
                                            <span class="badge-soft badge-soft-success">Approved</span>
                                        @else
                                            <span class="badge-soft badge-soft-secondary">Rejected</span>
                                        @endif
                                    </td>
                                    <td>{{ $r->approver_name ?: ($r->approved_by ?? '-') }}</td>
                                    <td>{{ $r->approved_at ? \Carbon\Carbon::parse($r->approved_at)->format('Y-m-d H:i') : '-' }}</td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button"
                                                    class="btn btn-primary btnViewContext"
                                                    data-context='@json($ctxPayload)'>
                                                View
                                            </button>
                                            @if($r->status == 'PENDING')
                                                <button type="button" class="btn btn-success btnApproveRequest" data-id="{{ $r->id }}">Approve</button>
                                                <button type="button" class="btn btn-danger btnRejectRequest"  data-id="{{ $r->id }}">Reject</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Context Modal -->
    <div class="modal fade" id="ctxModal" tabindex="-1" aria-labelledby="ctxModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white py-2">
                    <h6 class="modal-title" id="ctxModalLabel">Request Context</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-2">
                    {{-- this <pre> is left in place per your original, but gets replaced dynamically --}}
                    <pre id="ctxPre" class="mb-0"></pre>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        (function () {
            // live filter by typing (Loan No + Customer columns)
            const input = document.getElementById('searchBox');
            const table = document.querySelector('.table.table-striped.table-hover');
            if (!input || !table) return;

            const rows = Array.from(table.querySelectorAll('tbody tr'));
            const noRowsMsgId = 'live-filter-empty';
            let debounceTimer = null;

            function normalize(s) {
                return (s || '').toString().toLowerCase().replace(/\s+/g, ' ').trim();
            }

            function ensureNoRowsMessage() {
                let el = document.getElementById(noRowsMsgId);
                if (!el) {
                    el = document.createElement('tr');
                    el.id = noRowsMsgId;
                    el.innerHTML = `<td colspan="10" class="text-center text-muted py-3">No matching rows</td>`;
                    table.querySelector('tbody').appendChild(el);
                }
                return el;
            }

            function liveFilter() {
                const q = normalize(input.value);

                let shown = 0;
                rows.forEach(tr => {
                    // columns: [#, Loan No, Customer, Amount, Requested By, ...]
                    const tds = tr.children;
                    const loanNo   = normalize(tds[1]?.textContent);
                    const customer = normalize(tds[2]?.textContent);

                    const match = !q || loanNo.includes(q) || customer.includes(q);
                    tr.style.display = match ? '' : 'none';
                    if (match) shown++;
                });

                // show/hide "No matching rows" helper row
                const emptyRow = ensureNoRowsMessage();
                emptyRow.style.display = shown === 0 ? '' : 'none';
            }

            // run once to set initial state (in case value came from server)
            liveFilter();

            input.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(liveFilter, 150);
            });

            // optional: clear filter when user hits ESC
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    input.value = '';
                    liveFilter();
                }
            });
        })();
    </script>

    <script>
        (function(){
            console.log('loan_delete_requests inline JS ✅');

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            function post(url, payload) {
                return fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
            }

            function showJSONModal(ctx) {
                try { if (typeof ctx === 'string') ctx = JSON.parse(ctx); } catch(e){}
                const obj = (ctx && typeof ctx === 'object') ? ctx : {'Info': String(ctx||'-')};

                // KV table (skip nested objects except 'payments')
                const kvRows = Object.entries(obj)
                    .filter(([k]) => k !== 'payments')
                    .filter(([_,v]) => v !== null && v !== undefined && v !== '' && !(typeof v === 'object'))
                    .map(([k,v]) => `
<tr>
  <th class="pe-3 text-nowrap" style="min-width:160px">${k}</th>
  <td>${v}</td>
</tr>`).join('');

                let html = `<table class="table table-sm mb-3"><tbody>${kvRows || '<tr><td>-</td></tr>'}</tbody></table>`;

                // Payments table (Date / Amount / Type)
                if (Array.isArray(obj.payments) && obj.payments.length) {
                    const payHead = `
<div class="d-flex align-items-center justify-content-between mb-2">
  <h6 class="mb-0">Undone Payments</h6>
  <small class="text-muted">
    Count: ${obj?.Payments?.count ?? obj.payments.length}
    ${obj?.Payments?.total ? ' • Total: ' + obj.Payments.total : ''}
  </small>
</div>`;

                    const payRows = obj.payments.map(p => `
<tr>
  <td>${p.date ?? '-'}</td>
  <td class="text-end">${Number(p.amount ?? 0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
  <td>${p.type ?? '-'}</td>
</tr>`).join('');

                    html += `
${payHead}
<div class="table-responsive">
<table class="table table-sm table-bordered align-middle mb-0">
  <thead class="table-light">
    <tr>
      <th>Date</th>
      <th class="text-end">Amount</th>
      <th>Type</th>
    </tr>
  </thead>
  <tbody>${payRows}</tbody>
</table>
</div>`;
                }

                const body = document.querySelector('#ctxModal .modal-body');
                body.innerHTML = html;

                const el = document.getElementById('ctxModal');
                (window.bootstrap?.Modal
                        ? bootstrap.Modal.getOrCreateInstance(el)
                        : { show(){ $('#ctxModal').modal('show'); } }
                ).show();
            }

            function confirmBox(title, text) {
                if (window.Swal) {
                    return Swal.fire({title, text, icon:'warning', showCancelButton:true, confirmButtonText:'Yes', cancelButtonText:'Cancel'})
                        .then(r => r.isConfirmed);
                }
                return Promise.resolve(confirm(text));
            }

            function notify(icon, title, text) {
                if (window.Swal) return Swal.fire(title || '', text || '', icon || 'info');
                alert(text || title || 'Done');
                return Promise.resolve();
            }

            // Delegated listeners
            document.addEventListener('click', async function(e){
                const btn = e.target.closest('.btnViewContext, .btnApproveRequest, .btnRejectRequest');
                if (!btn) return;

                e.preventDefault();

                if (btn.classList.contains('btnViewContext')) {
                    showJSONModal(btn.getAttribute('data-context'));
                    return;
                }

                if (btn.classList.contains('btnApproveRequest')) {
                    const ok = await confirmBox('Approve Delete?', 'This will reverse and permanently delete the loan.');
                    if (!ok) return;

                    post("{{ route('loan.approve_destroy_loan') }}", { request_id: Number(btn.dataset.id) })
                        .then(r => r.json())
                        .then(data => notify('success', 'Done', data.message || 'Loan deletion completed.').then(()=>location.reload()))
                        .catch(() => notify('error', 'Error', 'Failed to approve.'));
                    return;
                }

                if (btn.classList.contains('btnRejectRequest')) {
                    const ok = await confirmBox('Reject Request?', 'This will mark the request as REJECTED.');
                    if (!ok) return;

                    post("{{ route('loan.reject_destroy_loan') }}", { request_id: Number(btn.dataset.id) })
                        .then(r => r.json())
                        .then(data => notify('success', 'Done', data.message || 'Request rejected.').then(()=>location.reload()))
                        .catch(() => notify('error', 'Error', 'Failed to reject.'));
                    return;
                }
            });
        })();
    </script>
@endsection
