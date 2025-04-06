<div class="card p-4">
    <h5 class="mb-3 text-primary">
        <i class="fas fa-handshake me-2"></i>Guaranteed Loans
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
            {{-- Sample Data --}}
            <tr>
                <td>GL-00982</td>
                <td>2024-11-02</td>
                <td>Rs. 300,000</td>
                <td>14%</td>
                <td>36</td>
                <td>Rs. 150,000</td>
                <td><span class="badge bg-success">Active</span></td>
                <td>
                    <button class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i> View
                    </button>
                </td>
            </tr>
            <tr>
                <td>GL-00983</td>
                <td>2023-08-15</td>
                <td>Rs. 150,000</td>
                <td>10%</td>
                <td>24</td>
                <td>Rs. 40,000</td>
                <td><span class="badge bg-secondary">Closed</span></td>
                <td>
                    <button class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i> View
                    </button>
                </td>
            </tr>
            {{-- Loop actual guaranteed loan data here --}}
            </tbody>
        </table>
    </div>
</div>
