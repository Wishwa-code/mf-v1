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
            {{-- Sample Rows --}}
            <tr>
                <td>L-000123</td>
                <td>2024-05-20</td>
                <td>Rs. 100,000</td>
                <td>12%</td>
                <td>24</td>
                <td>Rs. 42,000</td>
                <td><span class="badge bg-success">Active</span></td>
                <td>
                    <button class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i> View
                    </button>
                </td>
            </tr>
            <tr>
                <td>L-000124</td>
                <td>2023-12-12</td>
                <td>Rs. 250,000</td>
                <td>10%</td>
                <td>36</td>
                <td>Rs. 90,000</td>
                <td><span class="badge bg-warning text-dark">Pending</span></td>
                <td>
                    <button class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i> View
                    </button>
                </td>
            </tr>
            {{-- Loop real data in production --}}
            </tbody>
        </table>
    </div>
</div>
