<div class="card p-4">
    <h5 class="mb-4 text-primary">
        <i class="fas fa-shield-alt me-2"></i>Request Insurance
    </h5>

    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Insurance Category</label>
            <select class="form-select" id="insuranceCategory">
                <option selected disabled>Select Category</option>
                <option value="1">Vehicle Insurance</option>
                <option value="2">Life Insurance</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-plus me-1"></i> Add New
            </button>
        </div>

        <div class="col-md-2">
            <label class="form-label">Date Count</label>
            <input type="number" class="form-control" id="dateCount" value="1">
        </div>

        <div class="col-md-2">
            <label class="form-label">Amount</label>
            <input type="number" class="form-control" id="amount" value="1000">
        </div>

        <div class="col-md-2">
            <label class="form-label">Total Amount</label>
            <input type="text" class="form-control" id="totalAmount" readonly>
        </div>
    </div>

    <div class="mt-4">
        <label class="form-label">Upload Evidence (Multiple allowed)</label>
        <input type="file" class="form-control" multiple>
    </div>

    <div class="mt-3">
        <label class="form-label">Note</label>
        <textarea class="form-control" rows="3" placeholder="Any notes related to this request..."></textarea>
    </div>

    <div class="mt-4">
        <button class="btn btn-success">
            <i class="fas fa-paper-plane me-1"></i> Request Insurance
        </button>
    </div>
</div>

<hr class="my-5">

<div class="card p-4">
    <h5 class="mb-3 text-primary"><i class="fas fa-history me-2"></i>Insurance Request History</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Note</th>
                <th>Created By</th>
                <th>Approved By</th>
                <th>Status</th>
                <th>View</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>2025-04-07</td>
                <td>Life Insurance</td>
                <td>Rs. 5000</td>
                <td>Annual cover</td>
                <td>Admin</td>
                <td>Manager</td>
                <td><span class="badge bg-success">Approved</span></td>
                <td><button class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></button></td>
                <td><button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>


<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add Insurance Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Type</label>
                        <select class="form-select">
                            <option>One Time</option>
                            <option>Recurring</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Amount</label>
                        <input type="number" class="form-control">
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="mb-2">Approval Levels</h6>
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Select Designation</label>
                        <select class="form-select" id="designationSelect">
                            <option>Manager</option>
                            <option>Supervisor</option>
                            <option>CEO</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-secondary w-100" id="addLevelBtn">
                            <i class="fas fa-plus me-1"></i> Add Level
                        </button>
                    </div>
                </div>

                <div class="mt-3">
                    <table class="table table-sm table-bordered" id="approvalLevelsTable">
                        <thead>
                        <tr>
                            <th>Level</th>
                            <th>Designation</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- Approval levels will appear here --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Save Category
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    // Auto-calculate total amount
    function calculateTotal() {
        const amount = parseFloat(document.getElementById('amount').value || 0);
        const days = parseInt(document.getElementById('dateCount').value || 1);
        document.getElementById('totalAmount').value = (amount * days).toFixed(2);
    }

    document.getElementById('amount').addEventListener('input', calculateTotal);
    document.getElementById('dateCount').addEventListener('input', calculateTotal);
    calculateTotal();

    // Add approval level row
    let levelCount = 1;
    document.getElementById('addLevelBtn').addEventListener('click', () => {
        const designation = document.getElementById('designationSelect').value;
        const table = document.getElementById('approvalLevelsTable').querySelector('tbody');
        const row = `<tr><td>Level ${levelCount}</td><td>${designation}</td></tr>`;
        table.insertAdjacentHTML('beforeend', row);
        levelCount++;
    });
</script>
