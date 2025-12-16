@extends('layout.admin')

@section('head')
    {{-- DataTables + Buttons CSS (same style as your other tables) --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Lead Approvals</h4>
                    <p class="text-muted mb-0">
                        Review pending leads and send them for next approval step.
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="leads_approval_table" class="table table-centered mb-0" style="width:100%">
                                <thead>
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
    {{-- jQuery already loaded globally from header --}}
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

    <script>
        $(function () {
            $('#leads_approval_table').DataTable({
                ajax: {
                    url: "{{ route('leads.approvals.data') }}",
                    dataSrc: 'data'
                },
                processing: true,
                pageLength: 10,
                order: [[0, 'asc']],
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                columns: [
                    {
                        data: 'id',
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {data: 'full_name'},
                    {data: 'email'},
                    {data: 'phone_number'},
                    {data: 'type'},
                    {
                        data: 'status',
                        render: function (data) {
                            let cls = 'badge bg-warning text-dark';
                            if (data === 'pending-approved') cls = 'badge bg-success';
                            return `<span class="${cls}">${data}</span>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            const url = "{{ route('leads.show', ['lead' => '__ID__']) }}".replace('__ID__', row.id);
                            return `
                                <button class="btn btn-sm btn-primary"
                                        onclick="window.location.href='${url}'">
                                    <i class="bi bi-eye"></i>
                                </button>
                            `;
                        }
                    }
                ]
            });
        });
    </script>
@endsection




