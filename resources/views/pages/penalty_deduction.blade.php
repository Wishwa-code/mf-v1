@extends('layout.admin')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Use the Bootstrap 5 bundle for DataTables (includes Responsive + Buttons) --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-1.13.8/r-2.5.0/b-2.4.2/datatables.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .page-title-box {
            background: linear-gradient(135deg,#1A2942 0%, #2f4770 100%);
            color: #fff; border-radius: 14px; padding: 16px 20px; box-shadow: 0 8px 24px rgba(0,0,0,.06);
        }
        .page-title { margin: 0; font-weight: 700; letter-spacing:.3px }
        .filter-bar {
            background: #fff; border: 1px solid #eef1f6; border-radius: 14px; padding: 12px;
            box-shadow: 0 10px 30px rgba(26,41,66,.05);
        }
        .filter-bar .form-label { font-weight: 600; color: #334b74; }
        .badge-soft { background: #eef2ff; color:#1A2942; border:1px solid #dfe8ff; border-radius: 999px; padding: .35rem .75rem; }
        .stat-card {
            border-radius: 14px; border: 1px solid #eef1f6; background:#fff; padding: 14px 16px;
            box-shadow: 0 10px 30px rgba(26,41,66,.06);
        }
        .stat-title { color:#64748b; font-size:.8rem; text-transform:uppercase; letter-spacing:.04em }
        .stat-value { font-size:1.15rem; font-weight:700; color:#1A2942 }
        .bg-purple th { color: #e1e1e1 !important; }
        .bg-purple { background-color: #1A2942 !important; color: #fff !important; }
        .table thead th { white-space: nowrap; }
        table.dataTable.nowrap th, table.dataTable.nowrap td { white-space: nowrap; }
        .btn-rounded { border-radius: 999px; }
    </style>
@endsection


@section('content')
    <div class="container-fluid">
        <div class="row"><div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Penalty Deduction</h4>
                </div>
            </div></div>

        {{-- Filters --}}
        <div class="filter-bar mt-3">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-2">
                    <label class="form-label">Loan No</label>
                    <input type="text" id="filter_loan_no" class="form-control" placeholder="e.g. KI/DKTN/BL1/014">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Group</label>
                    <select class="form-control select2" id="filter_group">
                        <option value="0">All</option>
                        @foreach($group as $g)
                            <option value="{{ $g->idCustomer_Group }}">{{ $g->Name }} - {{ $g->Leader_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Category</label>
                    <select class="form-control select2" id="filter_category">
                        <option value="0">All</option>
                        @foreach($loan_category as $lc)
                            <option value="{{ $lc->idLoan_Category }}">{{ $lc->Name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Customer</label>
                    <select class="form-control select2" id="filter_customer">
                        <option value="0">All</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->idCustomer }}">{{ $c->First_Name }} {{ $c->Last_Name }} - {{ $c->Nic }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-1 d-grid">
                    <button class="btn btn-danger btn-rounded" id="btn-search">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </div>
        </div>

        {{-- Summary --}}
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-title">Loans with Penalty</div>
                    <div class="stat-value" id="stat_count">0</div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-title">Total Penalty Balance</div>
                        <div class="stat-value" id="stat_total_penalty">0.00</div>
                    </div>
                    <span class="badge badge-soft" id="summary_badge">Count: 0 | Total Penalty: 0.00</span>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card mt-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle nowrap" id="loan_table" style="width:100%">
                        <thead class="sticky-top bg-purple">
                        <tr>
                            <th>Loan No</th>
                            <th>Group</th>
                            <th>Customer</th>
                            <th>Category</th>
                            <th>Loan Amount</th>
                            <th>Capital Balance</th>
                            <th>Total Amount</th>
                            <th>Total Balance</th>
                            <th>Penalty Balance</th>
                            <th>Created</th>
                            <th>Maturity</th>
                            <th style="width:120px;">Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection


@section('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- CSS (Bootstrap 5 builds) --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-1.13.8/r-2.5.0/b-2.4.2/datatables.min.css"/>

    {{-- JS (Bootstrap 5 builds) --}}
    <script src="https://cdn.datatables.net/v/bs5/dt-1.13.8/r-2.5.0/b-2.4.2/datatables.min.js"></script>

    <script>
        $(function(){
            $('.select2').select2();

            const table = $('#loan_table').DataTable({
                responsive: true,       // enable Responsive extension
                scrollX: true,          // allow horizontal scroll when needed
                autoWidth: false,       // don’t compute column widths inline
                ordering: false,
                paging: true,
                searching: false,
                info: true,
                data: [],
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },   // Loan No
                    { responsivePriority: 2, targets: -1 },  // Action
                    { responsivePriority: 3, targets: 2 },   // Customer
                ],
                columns: [
                    { data: 'loan_no' },
                    { data: 'group' },
                    { data: 'customer' },
                    { data: 'category' },
                    { data: 'loan_amount' },
                    { data: 'capital_balance' },
                    { data: 'total_amount' },
                    { data: 'total_balance' },
                    { data: 'penalty_balance' },
                    { data: 'created_at' },
                    { data: 'maturity_date' },
                    {
                        data: null,
                        orderable: false,
                        render: function(row){
                            return `
          <button class="btn btn-sm btn-warning deduct-btn"
            data-loan-id="${row.loan_id}"
            data-penalty="${row.penalty_raw}">
            <i class="bi bi-scissors"></i> Deduct
          </button>`;
                        }
                    }
                ]
            });


            $('#btn-search').on('click', loadTable);
            loadTable();

            function loadTable(){
                const payload = {
                    group_id: $('#filter_group').val() || 0,
                    category_id: $('#filter_category').val() || 0,
                    customer_id: $('#filter_customer').val() || 0,
                    loan_no: $('#filter_loan_no').val() || 0,
                };

                $.ajax({
                    url: "{{ route('penalty.deduction.load') }}",
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: payload,
                    beforeSend(){ $('#btn-search').prop('disabled', true).text('Loading...'); },
                    complete(){ $('#btn-search').prop('disabled', false).html('<i class="bi bi-search"></i> Search'); },
                    success: function(res){
                        table.clear().rows.add(res.data || []).draw();
                        $('#summary_badge').text(`Count: ${res.summary?.count ?? 0} | Total Penalty: ${res.summary?.total_penalty ?? '0.00'}`);
                    },
                    error: function(){
                        Swal.fire('Error','Failed to load data','error');
                    }
                });
            }

            $('#loan_table').on('click', '.deduct-btn', function(){
                const loanId  = $(this).data('loan-id');
                const penalty = parseFloat($(this).data('penalty') || 0);

                if (penalty <= 0) {
                    Swal.fire('Notice','No penalty to deduct','info');
                    return;
                }

                Swal.fire({
                    title: 'Deduct Penalty (Partial)',
                    html: `
        <div style="text-align:left">
          <div>Current Penalty Balance: <b>${penalty.toFixed(2)}</b></div>
          <div class="mt-2">Enter amount to deduct:</div>
        </div>
        <input type="number" id="deductAmount" class="swal2-input" placeholder="0.00" min="0.01" step="0.01">
      `,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: 'Deduct',
                    preConfirm: () => {
                        const val = parseFloat(document.getElementById('deductAmount').value || '0');
                        if (isNaN(val) || val <= 0) {
                            Swal.showValidationMessage('Enter a positive amount');
                            return false;
                        }
                        if (val > penalty + 1e-9) {
                            Swal.showValidationMessage('Amount cannot exceed current penalty balance');
                            return false;
                        }
                        return val.toFixed(2);
                    }
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ route('penalty.deduction.apply') }}",
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        data: { loan_id: loanId, amount: result.value },
                        success: function(resp){
                            if(resp.ok){
                                Swal.fire('Done', `Penalty of ${resp.amount} deducted successfully.`, 'success');
                                // reload table so balances update
                                $('#btn-search').trigger('click');
                            }else{
                                Swal.fire('Notice', resp.msg || 'No changes made', 'info');
                            }
                        },
                        error: function(xhr){
                            const msg = xhr.responseJSON?.msg || 'Failed to deduct penalty';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                });
            });
        });
    </script>
@endsection
