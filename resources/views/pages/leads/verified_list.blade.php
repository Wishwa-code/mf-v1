@extends('layout.admin')

@section('head')
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('content')

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h4 class="card-title mb-0">Verified Leads History</h4>
            </div>
            <div class="card-body">
                <table id="verified-list-table" class="table table-bordered dt-responsive nowrap w-100">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Lead Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Visit Status</th>
                            <th>Visit Info</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(function() {
        $('#verified-list-table').DataTable({
            processing: true,
            serverSide: true, // Yajra
            ajax: "{{ route('leads.verifiedData') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'full_name', name: 'full_name' },
                { data: 'phone_number', name: 'phone_number' },
                { data: 'address', name: 'address' },
                { data: 'status', name: 'status' },
                { data: 'is_visited', name: 'is_visited' },
                { data: 'action', name: 'action', title: 'Status Detail' } // Renamed Action to Info since it's just badges
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endsection
