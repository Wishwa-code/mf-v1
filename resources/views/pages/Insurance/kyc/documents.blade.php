<div class="card p-4">
    <h5 class="mb-3 text-primary">
        <i class="fas fa-file-alt me-2"></i>Customer Documents
    </h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
            <tr>
                <th>Description</th>
                <th>Preview</th>
                <th style="width: 100px;">Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($documents as $doc)
                <tr>
                    <td>{{ $doc->Description }}</td>
                    <td>
                        @php
                            $ext = pathinfo($doc->Path, PATHINFO_EXTENSION);
                            $fullPath = asset('storage/' .$doc->Path);
                        @endphp

                        @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']))
                            <img src="{{$fullPath }}" alt="preview" style="height: 50px; border-radius: 4px;">
                        @elseif(strtolower($ext) === 'pdf')
                            <i class="fas fa-file-pdf fa-2x text-danger"></i>
                        @else
                            <i class="fas fa-file fa-2x text-secondary"></i>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary preview-btn" data-path="{{ $fullPath }}">
                            <i class="fas fa-eye me-1"></i> View
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">No documents found.</td>
                </tr>
            @endforelse
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
    $(document).on('click', '.preview-btn', function () {
        const filePath = $(this).data('path');
        $('#docPreviewFrame').attr('src', filePath);
        new bootstrap.Modal(document.getElementById('docPreviewModal')).show();
    });
</script>

