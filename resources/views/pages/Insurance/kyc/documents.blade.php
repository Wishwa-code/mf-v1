<div class="card p-4">
    <h5 class="mb-3 text-primary">
        <i class="fas fa-file-alt me-2"></i>Customer Documents
    </h5>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-light">
            <tr>
                <th>Description</th>
                <th>File Path</th>
                <th>Branch</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            {{-- Example row --}}
            <tr>
                <td>NIC Front</td>
                <td>/documents/customer/nic_front.jpg</td>
                <td>Branch 001</td>
                <td>
                    <button class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i> View
                    </button>
                </td>
            </tr>
            <tr>
                <td>Utility Bill</td>
                <td>/documents/customer/bill.pdf</td>
                <td>Branch 002</td>
                <td>
                    <button class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i> View
                    </button>
                </td>
            </tr>
            {{-- You can loop actual data here --}}
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="docPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <iframe src="" frameborder="0" id="docPreviewFrame" class="w-100" style="height: 500px;"></iframe>
            </div>
        </div>
    </div>
</div>


<script>
    document.querySelectorAll('.btn-primary').forEach(btn => {
        btn.addEventListener('click', function () {
            const filePath = this.closest('tr').children[1].innerText;
            document.getElementById('docPreviewFrame').src = filePath;
            new bootstrap.Modal(document.getElementById('docPreviewModal')).show();
        });
    });
</script>

