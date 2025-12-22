@extends('layout.admin')

@section('content')
<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white py-3">
                    <h4 class="card-title mb-0" style="font-weight: 600; color: #333;">Lead Approvals</h4>
                    <p class="text-muted mb-0 small">Review pending leads and send them for next approval step.</p>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table id="leads_approval_table" class="table table-hover table-striped dt-responsive nowrap w-100" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $('#leads_approval_table').DataTable({
                ajax: {
                    url: "{{ route('leads.approvals.data') }}",
                    dataSrc: 'data'
                },
                processing: true,
                pageLength: 10,
                order: [[0, 'asc']],
                language: {
                    searchPlaceholder: "Search records",
                    search: "",
                },
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn btn-light btn-sm'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-light btn-sm'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-light btn-sm'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-light btn-sm'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-light btn-sm'
                    }
                ],
                columns: [
                    {
                        data: 'id',
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {data: 'full_name', className: 'fw-medium'},
                    {data: 'email'},
                    {data: 'phone_number'},
                    {
                        data: 'type',
                        render: function(data) {
                            return `<span class="badge bg-soft-info text-info">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                        }
                    },
                    {
                        data: 'status',
                        render: function (data) {
                            let cls = 'badge bg-soft-warning text-warning';
                            let text = 'Pending';
                            
                            if (data === 'pending-approved') {
                                cls = 'badge bg-soft-success text-success';
                                text = 'Approved';
                            }
                            
                            return `<span class="${cls}">${text}</span>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            const url = "{{ route('leads.show', ['lead' => '__ID__']) }}".replace('__ID__', row.id);
                            return `
                                <a href="${url}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" style="border-radius: 6px;">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            `;
                        }
                    }
                ],
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control').css('margin-left','10px');
                    $('.dt-buttons .btn').removeClass('btn-secondary').addClass('btn-light btn-sm border');
                }
            });
        });
    </script>
@endsection
