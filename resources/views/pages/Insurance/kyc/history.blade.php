<style>
    .img-tile {
        height: 100px;
        object-fit: cover;
        border-radius: 4px;
    }
</style>

<div class="card p-4">

    <h5 class="mb-3 text-primary">
        <i class="fas fa-history me-2"></i>Insurance Request History
    </h5>

    <div class="table-responsive mb-5">
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
                <td>2025-04-06</td>
                <td>Vehicle Insurance</td>
                <td>Rs. 25,000</td>
                <td>Annual cover</td>
                <td>Admin</td>
                <td>Manager</td>
                <td><span class="badge bg-success">Approved</span></td>
                <td>
                    <button class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></button>
                </td>
                <td>
                    <button class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    {{-- File Attachments --}}
    <h5 class="mb-3 text-primary"><i class="fas fa-file me-2"></i>Uploaded Insurance Files</h5>

    {{-- Images as Tiles --}}
    <div class="mb-4">
        <div class="row g-3">
            {{-- Image Sample Tile --}}
            <div class="col-md-2">
                <div class="border rounded shadow-sm p-2 text-center">
                    <img src="/storage/uploads/insurance1.jpg" class="img-fluid rounded mb-2" style="height: 100px; object-fit: cover;">
                    <p class="small mb-0">insurance1.jpg</p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="border rounded shadow-sm p-2 text-center">
                    <img src="/storage/uploads/insurance2.png" class="img-fluid rounded mb-2" style="height: 100px; object-fit: cover;">
                    <p class="small mb-0">insurance2.png</p>
                </div>
            </div>
            {{-- Loop more images --}}
        </div>
    </div>

    {{-- PDF/DOC files in table --}}
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
            <tr>
                <th>File Name</th>
                <th>Type</th>
                <th>Uploaded At</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>insurance-policy.pdf</td>
                <td>PDF</td>
                <td>2025-04-06</td>
                <td>
                    <a href="/storage/uploads/insurance-policy.pdf" target="_blank" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i> View
                    </a>
                </td>
            </tr>
            <tr>
                <td>company-cover.docx</td>
                <td>DOCX</td>
                <td>2025-04-06</td>
                <td>
                    <a href="/storage/uploads/company-cover.docx" target="_blank" class="btn btn-sm btn-primary">
                        <i class="fas fa-download me-1"></i> View
                    </a>
                </td>
            </tr>
            {{-- Loop more non-image files --}}
            </tbody>
        </table>
    </div>
</div>
