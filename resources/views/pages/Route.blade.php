@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <style>
        .style-tr>td {
            padding: 2px 15px;
        }

        thead {
            background-color: #d9edf7;
            color: #31708f;
        }
    </style>
@endsection

@section('content')
    <div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h4 class="page-title">Routes</h4>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-route-modal">
                                Add New Route
                            </button>
                        </div>
                        <table id="centerTable" class="display nowrap table table-striped table-bordered" style="width:100%">
                            <thead>
                            <tr>
                                <th>Route Name</th>
                                <th>Route Code</th>
                                <th>Collection Type</th>
                                <th>Collection Date</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($userData as $item)
                                <tr class="style-tr">
                                    <td>{{$item->name}}</td>
                                    <td>{{$item->root_code}}</td>
                                    @if($item->collection_type=='customizable')
                                        <td>Customizable</td>
                                        <td>-</td>
                                    @else
                                        <td>Fixed Day Of The Week</td>
                                        <td>{{$item->collection_date}}</td>
                                    @endif
                                    <td class="text-center">
                                        <button
                                                type="button"
                                                class="btn btn-light edit-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#standard-modal"
                                                data-route="{{$item->name}}"
                                                data-route-id="{{$item->id_route}}"
                                                data-route-code="{{$item->root_code}}"
                                                data-collection-type="{{$item->collection_type}}"
                                                data-collection-date="{{$item->collection_date}}"
                                                title="Edit Route">
                                            <i class="bi bi-pencil fs-4"></i>
                                        </button>

                                        <button type="button" class="btn btn-danger" onclick="confirmDelete('{{$item->id_route}}')" title="Delete Route">
                                            <i class="bi bi-trash fs-4"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div> <!-- end card-->
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div>

        <!-- Edit Center Modal -->
        <div class="modal fade" id="standard-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Edit Route</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" id="center_id">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="route" class="form-label">Route Name<span class="required-asterisk">*</span></label>
                                    <input type="text" id="route" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="route" class="form-label">Route Code<span class="required-asterisk">*</span></label>
                                    <input type="text" id="route_code" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_collection_type" class="form-label">Collection Type<span class="required-asterisk">*</span></label>
                                    <select class="form-select" id="edit_collection_type">
                                        <option value="customizable">Customizable</option>
                                        <option value="fixed">Fixed Day Of The Week</option>
                                    </select>
                                </div>

                                <div class="mb-3" id="edit_collection_date_wrap">
                                    <label for="edit_collection_date" class="form-label">Collection Date<span class="required-asterisk">*</span></label>
                                    <select class="form-select" id="edit_collection_date">
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                        <option value="Sunday">Sunday</option>
                                        <option value="First Week Monday">First Week Monday</option>
                                        <option value="First Week Tuesday">First Week Tuesday</option>
                                        <option value="First Week Wednesday">First Week Wednesday</option>
                                    </select>
                                </div>

                                <div class="mb-3" hidden>
                                    <label for="center_incharge" class="form-label">Route In-charge<span class="required-asterisk">*</span></label>
                                    <select class="form-control" id="center_incharge">
                                        <option value="1" selected>Admin</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" onclick="update_center()">Update changes</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="add-route-modal" tabindex="-1" role="dialog" aria-labelledby="addRouteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add New Route</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_route_name" class="form-label">Route Name<span class="required-asterisk">*</span></label>
                            <input type="text" id="new_route_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="new_route_name" class="form-label">Route Code<span class="required-asterisk">*</span></label>
                            <input type="text" id="new_route_code" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="new_collection_type" class="form-label">Collection Type<span class="required-asterisk">*</span></label>
                            <select class="form-select" id="new_collection_type">
                                <option value="customizable" selected>Customizable</option>
                                <option value="fixed">Fixed Day Of The Week</option>
                            </select>
                        </div>

                        <div class="mb-3" id="new_collection_date_wrap" style="display:none;">
                            <label for="new_collection_date" class="form-label">Collection Date<span class="required-asterisk">*</span></label>
                            <select class="form-select" id="new_collection_date">
                                <option value="Monday" selected>Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                                <option value="First Week Monday">First Week Monday</option>
                                <option value="First Week Tuesday">First Week Tuesday</option>
                                <option value="First Week Wednesday">First Week Wednesday</option>
                            </select>
                        </div>

                        <div class="mb-3" hidden>
                            <label for="new_route_incharge" class="form-label">Route In-charge/Recovery Officer<span class="required-asterisk">*</span></label>
                            <select class="form-control" id="new_route_incharge">
                                <option value="1" selected>Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" onclick="saveRoute()">Save Route</button>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <!-- SweetAlert2 for confirmation dialogs -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            var companyName = {!! json_encode(session('company_name')) !!}.replace(/&/g, ' And ');

            // --- DataTable (unchanged) ---
            $('#centerTable').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                buttons: [
                    { extend: 'copy',  text: '<i class="bi bi-clipboard"></i> Copy',  className: 'btn btn-secondary', exportOptions: { columns: [0,1,2,3] }, filename: companyName },
                    { extend: 'csv',   text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV', className: 'btn btn-success', exportOptions: { columns: [0,1,2,3] }, filename: companyName },
                    { extend: 'excel', text: '<i class="bi bi-file-earmark-excel"></i> Excel', className: 'btn btn-primary', exportOptions: { columns: [0,1,2,3] }, filename: companyName },
                    { extend: 'pdf',   text: '<i class="bi bi-file-earmark-pdf"></i> PDF', className: 'btn btn-danger', exportOptions: { columns: [0,1,2,3] }, filename: companyName },
                    { extend: 'print', text: '<i class="bi bi-printer"></i> Print', className: 'btn btn-info', exportOptions: { columns: [0,1,2,3] }, filename: companyName }
                ],
                initComplete: function () {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });

            // --- Toggle helpers ---
            function applyToggle($typeSelect, $wrap, $dateSelect) {
                const val = $typeSelect.val();
                if (val === 'fixed') {
                    $wrap.show();
                    $dateSelect.prop('disabled', false).attr('required', true);
                } else {
                    $wrap.hide();
                    $dateSelect.prop('disabled', true).removeAttr('required');
                }
            }

            // Add modal toggle
            $('#new_collection_type').on('change', function () {
                applyToggle($('#new_collection_type'), $('#new_collection_date_wrap'), $('#new_collection_date'));
            });
            // Initialize Add modal default state
            applyToggle($('#new_collection_type'), $('#new_collection_date_wrap'), $('#new_collection_date'));

            // Edit modal toggle
            $('#edit_collection_type').on('change', function () {
                applyToggle($('#edit_collection_type'), $('#edit_collection_date_wrap'), $('#edit_collection_date'));
            });

            // --- Populate Edit Modal ---
            $(document).on('click', '.edit-btn', function () {
                const b = $(this);
                $('#route').val(b.data('route'));
                $('#center_id').val(b.data('route-id'));
                $('#route_code').val(b.data('route-code'));

                const cType = (b.data('collection-type') || '').toString().toLowerCase();
                const cDate = (b.data('collection-date') || 'Monday');

                // set type
                if (cType === 'fixed' || cType === 'customizable') {
                    $('#edit_collection_type').val(cType);
                } else {
                    $('#edit_collection_type').val('customizable'); // safe default
                }

                // set date
                $('#edit_collection_date').val(cDate);

                // apply toggle after setting values
                applyToggle($('#edit_collection_type'), $('#edit_collection_date_wrap'), $('#edit_collection_date'));
            });
        });

        // --- Delete (unchanged) ---
        function confirmDelete(idCenter) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to undo this action!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/route/delete/" + idCenter,
                        method: "GET",
                        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                        success: function (response) {
                            if (response.item === 1) {
                                Swal.fire({ title: 'Deleted !', text: 'The route delete successfully.', icon: 'success', confirmButtonText: 'OK' })
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Error!', 'This Route is already exist in center', 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error!', 'An error occurred while deleting the center.', 'error');
                        }
                    });
                }
            });
        }

        // --- Update Route (AJAX) ---
        function update_center() {
            const collection_type = $('#edit_collection_type').val();
            const payload = {
                center_id: $('#center_id').val(),
                route: $('#route').val(),
                route_code: $('#route_code').val(),
                collection_type: collection_type,
                collection_date: (collection_type === 'fixed') ? $('#edit_collection_date').val() : null,
            };

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to update the route details?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('route.update') }}",
                        method: "post",
                        data: payload,
                        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                        success: function () {
                            Swal.fire({ title: 'Updated!', text: 'The route details have been updated.', icon: 'success', confirmButtonText: 'OK' })
                                .then(() => { $('#standard-modal').modal('hide'); location.reload(); });
                        },
                        error: function () {
                            Swal.fire('Error!', 'An error occurred while updating the center.', 'error');
                        }
                    });
                }
            });
        }

        // --- Save Route (AJAX) ---
        function saveRoute() {
            const collection_type = $('#new_collection_type').val();
            const payload = {
                route_name: $('#new_route_name').val(),
                route_incharge: $('#new_route_incharge').val(),
                root_code: $('#new_route_code').val(),
                collection_type: collection_type,
                collection_date: (collection_type === 'fixed') ? $('#new_collection_date').val() : null,
            };

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to add this new route?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, add it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('route.store') }}",
                        method: "post",
                        data: payload,
                        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                        success: function () {
                            Swal.fire({ title: 'Saved!', text: 'The new route has been added successfully.', icon: 'success', confirmButtonText: 'OK' })
                                .then(() => { $('#add-route-modal').modal('hide'); location.reload(); });
                        },
                        error: function () {
                            Swal.fire('Error!', 'An error occurred while saving the route.', 'error');
                        }
                    });
                }
            });
        }
    </script>

@endsection
