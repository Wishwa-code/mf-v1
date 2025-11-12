@extends('layout.admin')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <style>
        .bg-purple th {
            color: #e1e1e1 !important;
        }

        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Other Charges Codes</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="header-title">Charge Codes List</h4>
                            <button type="button" class="btn btn-primary" onclick="openAddModal()">
                                <i class="bi bi-plus-circle me-1"></i> Add New Code
                            </button>
                        </div>

                        <table id="codes-table" class="table table-striped table-bordered dt-responsive nowrap w-100">
                            <thead class="bg-purple">
                                <tr>
                                    <th>ID</th>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($codes as $code)
                                    <tr>
                                        <td>{{ $code->id }}</td>
                                        <td>{{ $code->code }}</td>
                                        <td>{{ $code->description ?? '-' }}</td>
                                        <td>{{ $code->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    onclick="openEditModal({{ $code->id }})" 
                                                    title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="deleteCode({{ $code->id }})" 
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
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

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="codeModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-3">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="bi bi-cash-coin me-2"></i><span id="modalTitleText">Add Charge Code</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <form id="codeForm">
                        <input type="hidden" id="codeId">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code" required 
                                       placeholder="Enter code">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="description" rows="3" 
                                          placeholder="Enter description"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveCode()">
                        <i class="bi bi-save me-1"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#codes-table').DataTable({
                responsive: true,
                order: [[0, 'desc']]
            });
        });

        function openAddModal() {
            $('#codeId').val('');
            $('#code').val('');
            $('#description').val('');
            $('#modalTitleText').text('Add Charge Code');
            $('#codeModal').modal('show');
        }

        function openEditModal(id) {
            $.ajax({
                url: `/other-charges-codes/${id}`,
                type: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#codeId').val(response.data.id);
                        $('#code').val(response.data.code);
                        $('#description').val(response.data.description);
                        $('#modalTitleText').text('Edit Charge Code');
                        $('#codeModal').modal('show');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Failed to load charge code', 'error');
                }
            });
        }

        function saveCode() {
            const id = $('#codeId').val();
            const url = id ? `/other-charges-codes/${id}` : '/other-charges-codes';
            const method = id ? 'PUT' : 'POST';

            const data = {
                code: $('#code').val(),
                description: $('#description').val(),
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: url,
                type: method,
                data: data,
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Success!', response.message, 'success').then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Operation failed';
                    Swal.fire('Error!', message, 'error');
                }
            });
        }

        function deleteCode(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/other-charges-codes/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Deleted!', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            const message = xhr.responseJSON?.message || 'Delete failed';
                            Swal.fire('Error!', message, 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
