<div class="card p-4">
    <h5 class="mb-3 text-primary">
        <i class="fas fa-money-bill-wave me-2"></i>Loan Details
    </h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
            <tr>
                <th>Loan No</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Interest Rate</th>
                <th>Installments</th>
                <th>Balance</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($loans as $loan)
                <tr>
                    <td>{{ $loan->Loan_No }}</td>
                    <td>{{ \Carbon\Carbon::parse($loan->Date_Time)->format('Y-m-d') }}</td>
                    <td>Rs. {{ number_format($loan->Amount, 2) }}</td>
                    <td>{{ $loan->Interest_Rate }}%</td>
                    <td>{{ $loan->Installment_Count }}</td>
                    <td>Rs. {{ number_format($loan->Balance_Amount, 2) }}</td>
                    <td>
                        @php
                            $badgeClass = match((string) $loan->Status) {
                                '0' => 'bg-success', // On going
                                '-1' => 'bg-warning text-dark', // Pending
                                '1', '2', 'completed' => 'bg-secondary', // Settled
                                '-2' => 'bg-danger', // Deleted
                                default => 'bg-info',
                            };

                            $statusText = match((string) $loan->Status) {
                                '0' => 'On going',
                                '-1' => 'Pending',
                                '1', '2', 'completed' => 'Settled',
                                '-2' => 'Deleted',
                                default => ucfirst($loan->Status),
                            };

                            $loanUrl = ($loan->Status == -1)
                                ? url('loanview/' . $loan->idCustomer_Loan . '/438217')
                                : url('loanview/' . $loan->idCustomer_Loan);
                        @endphp

                        <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                    </td>
                    <td>
                        <a href="{{ $loanUrl }}" target="_blank" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-1"></i> View
                        </a>
                    </td>


                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">No loans found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
