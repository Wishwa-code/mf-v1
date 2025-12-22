@extends('layout.admin')

@section('content')
<div class="row mt-4">
    <div class="col-12 col-md-8 col-lg-6 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom pt-4 px-4 pb-0">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div>
                        <h4 class="mb-1">Edit Business Category</h4>
                        <p class="text-muted small mb-0">Update business category details.</p>
                    </div>
                    <div>
                        <a href="{{ route('business-categories.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form id="editCategoryForm" action="{{ route('business-categories.update', $businessCategory->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="name" class="form-label fw-medium">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="name" name="name" value="{{ $businessCategory->name }}" placeholder="E.g. Retail, Manufacturing">
                        <div class="invalid-feedback" id="name_error"></div>
                        <div class="form-text text-muted">Enter a unique name for the business category.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('business-categories.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4" id="submitBtn">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#editCategoryForm').on('submit', function(e) {
            e.preventDefault();
            
            let form = $(this);
            let btn = $('#submitBtn');
            let originalText = btn.text();
            
            // Reset validation errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Updating...');
            
            $.ajax({
                url: form.attr('action'),
                method: 'POST', // Method spoofing is handled by @method('PUT')
                data: form.serialize(),
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = "{{ route('business-categories.index') }}";
                        });
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text(originalText);
                    
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key + '_error').text(value[0]);
                        });
                    } else {
                        Swal.fire('Error', xhr.responseJSON.message || 'Something went wrong', 'error');
                    }
                }
            });
        });
    });
</script>
@endsection
