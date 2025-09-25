<div class="card p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h5 class="text-primary mb-0"><i class="fas fa-list me-2"></i>Registration Summary</h5>
    </div>

    @if(isset($summary) && $summary)
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Registered Branch</label>
                <input type="text" class="form-control" value="{{ $summary->branch_name }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Route</label>
                <input type="text" class="form-control" value="{{ $summary->route_name }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Center</label>
                <input type="text" class="form-control" value="{{ $summary->center_name }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Group</label>
                <input type="text" class="form-control" value="{{ $summary->group_name }}" disabled>
            </div>
        </div>

        <hr class="my-4">

        <h5 class="mb-3 text-primary"><i class="fas fa-info-circle me-2"></i>Other Data</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Registered On</label>
                <input type="text" class="form-control" value="{{ (isset($summary->registered_at) && $summary->registered_at) ? \Carbon\Carbon::parse($summary->registered_at)->format('Y-m-d h:i A') : '-' }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Customer Age with Company</label>
                <input type="text" class="form-control" value="{{ (isset($summary->registered_at) && $summary->registered_at) ? \Carbon\Carbon::parse($summary->registered_at)->diff(\Carbon\Carbon::now())->format('%y years %m months %d days') : '-' }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Customer Loan Count</label>
                <input type="text" class="form-control" value="{{ (isset($summary->loan_count) && $summary->loan_count !== null) ? $summary->loan_count : ((isset($summary->loans_count) && $summary->loans_count !== null) ? $summary->loans_count : '-') }}" disabled>
            </div>
        </div>

        @if(isset($groupMembers) && $groupMembers->count() > 0)
            <hr class="my-4">
            <h5 class="mb-3 text-primary"><i class="bi bi-people me-2"></i>Other Customers in Same Group</h5>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                    <tr>
                        <th style="width: 120px;">Customer No</th>
                        <th>Name</th>
                        <th style="width: 160px;">NIC</th>
                        <th style="width: 140px;">Contact</th>
                        <th style="width: 80px;" class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($groupMembers as $m)
                        <tr>
                            <td>{{ $m->cus_number }}</td>
                            <td>{{ $m->First_Name }} {{ $m->Last_Name }}</td>
                            <td>{{ $m->Nic }}</td>
                            <td>{{ $m->Contact_No }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectKycCustomer({{ $m->idCustomer }})">
                                    <i class="bi bi-eye"></i> View
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @else
        <p class="text-muted mb-0">No summary details found for this customer.</p>
    @endif
</div>
