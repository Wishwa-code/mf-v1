@extends('layout.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Product / Loan</a></li>
                    <li class="breadcrumb-item active">Business Categories</li>
                </ol>
            </div>
            <h4 class="page-title">Business Category Management</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="header-title">Business Categories List</h4>
                <button type="button" class="btn btn-primary" id="createNewCategory">
                    <i class="bi bi-plus-lg"></i> Add New Category
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="categoryTable" class="table table-striped table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Category Name</th>
                                <th width="15%" class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- DataTables will populate this --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Create/Edit Modal --}}
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    @csrf
                    <input type="hidden" id="categoryId" name="id">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter category name">
                        <div class="invalid-feedback" id="name_error"></div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save Category</button>
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
        // Initialize DataTables
        var table = $('#categoryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('business-categories.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        // Open Modal for New Category
        $('#createNewCategory').click(function () {
            $('#categoryForm').trigger("reset");
            $('#categoryId').val('');
            $('#modalTitle').html("Add New Category");
            $('#saveBtn').html("Save Category");
            $('#name').removeClass('is-invalid');
            $('#categoryModal').modal('show');
        });

        // Open Modal for Edit
        $('body').on('click', '.edit-btn', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var url = $(this).data('url'); // This is business-categories.edit route, but we have data directly
            
            $('#modalTitle').html("Edit Category");
            $('#saveBtn').html("Update Category");
            $('#categoryId').val(id);
            $('#name').val(name).removeClass('is-invalid');
            $('#categoryModal').modal('show');
        });

        // Handle Form Submission (Create and Update)
        $('#categoryForm').on('submit', function(e) {
            e.preventDefault();
            
            var id = $('#categoryId').val();
            var url = id ? "/business-categories/" + id : "{{ route('business-categories.store') }}";
            var method = id ? 'PUT' : 'POST';
            var formData = $(this).serialize();
            
            // Add method spoofing for PUT if updating
            if(id) {
                formData += '&_method=PUT';
                method = 'POST'; // Ajax actually sends POST with _method field
            }

            $('#saveBtn').html('Saving...').prop('disabled', true);
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            $.ajax({
                data: formData,
                url: url,
                type: method,
                dataType: 'json',
                success: function (response) {
                    if(response.success){
                        $('#categoryForm').trigger("reset");
                        $('#categoryModal').modal('hide');
                        table.draw();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                },
                error: function (xhr) {
                    $('#saveBtn').html('Save Category').prop('disabled', false);
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key + '_error').text(value[0]);
                        });
                    } else {
                        Swal.fire('Error', xhr.responseJSON.message || 'Something went wrong', 'error');
                    }
                },
                complete: function() {
                     if($('#saveBtn').prop('disabled')) {
                         $('#saveBtn').html('Save Category').prop('disabled', false); // Restore if not success
                     }
                }
            });
        });

        // Delete Handler
        $('body').on('click', '.delete-btn', function () {
            var id = $(this).data("id");
            var url = $(this).data("url");
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: url,
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            if(response.success) {
                                table.draw();
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function (data) {
                            Swal.fire('Error', 'Failed to delete category.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
