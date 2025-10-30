<div class="loan-details-container">
    <!-- Loan Summary Stats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="ri-file-chart-line me-2"></i>Loan Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Loan Stock</small>
                                <strong class="fs-6">Rs. {{ number_format($loan->Amount ?? 0, 2) }}</strong>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Total Loan Amount</small>
                                <strong class="fs-6">Rs. {{ number_format($loan->Total_Loan_Amount ?? 0, 2) }}</strong>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Capital Balance</small>
                                <strong class="fs-6 text-danger">Rs. {{ number_format($loan->capital_balance ?? 0, 2) }}</strong>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Interest Balance</small>
                                <strong class="fs-6 text-warning">Rs. {{ number_format($loan->Interest_Amount ?? 0, 2) }}</strong>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Loan Status</small>
                                <span class="badge bg-warning">
                                    @if($loan->Status == '-1') Pending
                                    @elseif($loan->Status == '-3') Awaiting HO Approval
                                    @elseif($loan->Status == '0') Active
                                    @else {{ $loan->Status }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Created Date</small>
                                <strong class="fs-6">{{ $loan->created_at ?? $loan->Date_Time ?? 'N/A' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Collection Type</small>
                                <strong class="fs-6">{{ $loan->Collection_Type ?? 'N/A' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block">Interest Rate</small>
                                <strong class="fs-6">{{ $loan->Interest_Rate ?? 0 }}%</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loan Details -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="ri-file-text-line me-2"></i>Loan Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold">Loan Number:</td>
                                    <td>{{ $loan->Loan_No }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Product Name:</td>
                                    <td>{{ $loanCategory->Name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Amount:</td>
                                    <td class="text-success fw-bold">Rs. {{ number_format($loan->Amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Interest Rate:</td>
                                    <td>{{ $loan->Interest_Rate }}% ({{ $loanCategory->Repayment_type ?? 'N/A' }})</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Penalty Rate:</td>
                                    <td>{{ $loan->Panalty_Rate ?? 0 }}%</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Installment Count:</td>
                                    <td>{{ $loan->Installment_Count }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold">Interest Amount:</td>
                                    <td>Rs. {{ number_format($loan->Interest_Amount ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Total Loan Amount:</td>
                                    <td>Rs. {{ number_format($loan->Total_Loan_Amount ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Installment Amount:</td>
                                    <td>Rs. {{ number_format($loan->Installment_Amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Collection Type:</td>
                                    <td>{{ $loan->Collection_Type }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created User:</td>
                                    <td>{{ $user->Full_Name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Lending Officer:</td>
                                    <td>{{ $lendingOfficer->Full_Name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Details -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="ri-user-line me-2"></i>Customer Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold" width="40%">Customer No:</td>
                                    <td>{{ $customer->cus_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Name:</td>
                                    <td>{{ $customer->First_Name ?? '' }} {{ $customer->Last_Name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">NIC:</td>
                                    <td>{{ $customer->Nic ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Contact Number:</td>
                                    <td>{{ $customer->Mobile_No ?? $customer->Mobile ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Email:</td>
                                    <td>{{ $customer->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Address:</td>
                                    <td>{{ $customer->Address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">City:</td>
                                    <td>{{ $customer->City ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Civil Status:</td>
                                    <td>{{ $customer->Civil_Status ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold" width="40%">Occupation:</td>
                                    <td>{{ $customer->Occupation ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Working Place:</td>
                                    <td>{{ $customer->Work_Place ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Guardian Name:</td>
                                    <td>{{ $customer->Gua_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Guardian NIC:</td>
                                    <td>{{ $customer->Gua_nic ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Guardian Contact:</td>
                                    <td>{{ $customer->Gua_mobile ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Guardian Address:</td>
                                    <td>{{ $customer->Gua_address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Relation to Customer:</td>
                                    <td>{{ $customer->Relation_to_Customer ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Levels -->
    @if(count($approvalLevels) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="ri-check-line me-2"></i>Approval Levels</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Level</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Approved By</th>
                                    <th>Date</th>
                                    <th>Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($approvalLevels as $level)
                                <tr>
                                    <td>{{ $level->level }}</td>
                                    <td>{{ $level->description }}</td>
                                    <td>
                                        @if($level->date != '-')
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $approver = DB::table('user')->where('id', $level->user_id)->first();
                                        @endphp
                                        {{ $approver->Full_Name ?? '-' }}
                                    </td>
                                    <td>{{ $level->date != '-' ? $level->date : '-' }}</td>
                                    <td>{{ $level->comment ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Installment Schedule -->
    @if(count($installments) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="ri-calendar-line me-2"></i>Installment Schedule ({{ count($installments) }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Installment</th>
                                    <th>Capital</th>
                                    <th>Interest</th>
                                    <th>Penalty</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Penalty Bal</th>
                                    <th>Interest Bal</th>
                                    <th>Capital Bal</th>
                                    <th>Total Bal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($installments as $index => $inst)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><small>{{ $inst->Installment_Date ?? 'N/A' }}</small></td>
                                    <td class="text-end"><small>{{ number_format($inst->Installment_Amount ?? 0, 2) }}</small></td>
                                    <td class="text-end"><small>{{ number_format($inst->Capital_Amount ?? 0, 2) }}</small></td>
                                    <td class="text-end"><small>{{ number_format($inst->Interest_Amount ?? 0, 2) }}</small></td>
                                    <td class="text-end"><small>{{ number_format($inst->Panalty_Amount ?? 0, 2) }}</small></td>
                                    <td class="text-end"><small>{{ number_format(($inst->Installment_Amount ?? 0) + ($inst->Panalty_Amount ?? 0), 2) }}</small></td>
                                    <td class="text-end"><small>{{ number_format($inst->Paid_Amount ?? 0, 2) }}</small></td>
                                    <td class="text-end"><small>{{ number_format($inst->Panalty_Balance ?? 0, 2) }}</small></td>
                                    <td class="text-end"><small>{{ number_format($inst->Interest_Balance ?? 0, 2) }}</small></td>
                                    <td class="text-end"><small>{{ number_format($inst->capital_balance ?? 0, 2) }}</small></td>
                                    <td class="text-end"><small>{{ number_format($inst->Total_Balance ?? 0, 2) }}</small></td>
                                    <td>
                                        @if(($inst->Paid_Amount ?? 0) >= ($inst->Installment_Amount ?? 0))
                                            <span class="badge bg-success">Paid</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Witnesses/Guarantors -->
    @if(count($witnesses) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-secondary">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="ri-shield-user-line me-2"></i>Witnesses/Guarantors</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>NIC</th>
                                    <th>Mobile</th>
                                    <th>Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($witnesses as $witness)
                                <tr>
                                    <td>
                                        @php
                                            $typeLabel = $witness->display_type ?? $witness->type ?? 'N/A';
                                            $typeClass = $typeLabel === 'Guarantor' ? 'bg-danger' : ($typeLabel === 'Cross Customer' ? 'bg-info' : 'bg-secondary');
                                        @endphp
                                        <span class="badge {{ $typeClass }}">
                                            {{ $typeLabel }}
                                        </span>
                                    </td>
                                    <td>{{ $witness->Name ?? 'N/A' }}</td>
                                    <td>{{ $witness->Nic ?? $witness->NIC ?? 'N/A' }}</td>
                                    <td>{{ $witness->Mobile ?? 'N/A' }}</td>
                                    <td>{{ $witness->Address ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Other Charges -->
    @if(count($otherCharges) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-dark">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="ri-money-dollar-circle-line me-2"></i>Other Charges</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($otherCharges as $charge)
                                <tr>
                                    <td>{{ $charge->Description }}</td>
                                    <td class="text-end">Rs. {{ number_format($charge->Amount, 2) }}</td>
                                </tr>
                                @endforeach
                                <tr class="table-light fw-bold">
                                    <td class="text-end">Total:</td>
                                    <td class="text-end">Rs. {{ number_format($otherCharges->sum('Amount'), 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
