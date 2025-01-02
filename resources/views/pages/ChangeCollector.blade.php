@extends('layout.admin')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        .style-tr>td {
            padding: 2px 15px
        }


        .bg-purple th {
            color: #e1e1e1 !important;
        }

        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;

        }
        #productTable tr, #productTable th, #productTable td {
            margin: 10px !important;
            padding: 10px 25px!important; /* Adjust padding to your preference */
        }

        .table-centered {
            margin: 0 !important;
            padding: 0 !important;
        }

        .select2-container {
            z-index: 2050 !important; /* Higher than modal's z-index */
        }

        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ced4da; /* Consistent with Bootstrap inputs */
            border-radius: 4px;
            padding: 5px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #1A2942;
            border: 1px solid #e1e1e1;
            color: white;
        }
        @media (max-width: 768px) {
            .modal-lg {
                max-width: 90%;
            }
        }



    </style>

@endsection


@section('content')
    <div>


        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('load.collector_filtor')}}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="mb-2">
                                        <label for="route" class="form-label">Route</label>
                                        <select class="form-control form-control-sm select2" id="route" name="route">
                                            <option value="0" {{ old('route', $route ?? 0) == 0 ? 'selected' : '' }}>All</option>
                                            @foreach($routes as $item)
                                                <option value="{{ $item->id_route }}" {{ old('route', $route ?? 0) == $item->id_route ? 'selected' : '' }}>
                                                    {{ $item->name }} - {{ $item->Full_Name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <label for="recovery" class="form-label">Recovery Officer</label>
                                        <select class="form-control form-control-sm select2" id="recovery" name="recovery">
                                            <option value="0" {{ old('recovery', $recovery ?? 0) == 0 ? 'selected' : '' }}>All</option>
                                            @foreach($recovery_officer as $item)
                                                <option value="{{ $item->id }}" {{ old('recovery', $recovery ?? 0) == $item->id ? 'selected' : '' }}>
                                                    {{ $item->Full_Name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <button type="submit" class="btn btn-danger"><i class="bi bi-search"></i></button>
                        </form>

                        <hr>

                        <div class="table-responsive-sm">
                            <table class="table table-centered mb-0" id="loan_table">
                                <thead class="sticky-top bg-purple">
                                <tr>
                                    <th>Route</th>
                                    <th>Officer Name</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($load as $data)
                                    <tr>
                                        <td>{{ $data->route_names }}</td>  <!-- Display concatenated routes -->
                                        <td>{{ $data->recovery_officer }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-success" onclick="openPaymentModal('{{ $data->user_id }}')">
                                                <i class="fa fa-money-bill"></i> Assigned Route
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



        <!-- Payment Modal -->
        <div class="modal fade" id="issue-loan-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Collector</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="userId" value="">
                        <label for="routeSelect" class="form-label">Select Routes</label>
                        <select class="form-control select2" id="routeSelect" name="routeSelect[]" multiple>
                            @foreach($routes as $r)
                                <option value="{{ $r->id_route }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>



                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" onclick="updateRoute()">Assign Route</button>
                    </div>
                </div>
            </div>
        </div>






    </div>
@endsection

@section('script')

    <script src="{{ asset('../JS/loan_category.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Select2 CSS -->


    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            $('.routeSelect').select2();

            $(document).on('shown.bs.modal', '#issue-loan-modal', function () {
                $('#routeSelect').select2({
                    dropdownParent: $('#issue-loan-modal'),
                    placeholder: "Select routes",
                    allowClear: true
                });
            });
        });





        function openPaymentModal(id) {
            $('#issue-loan-modal').data('officer-id', id).modal('show');
        }

        function updateRoute() {
            const officerId = $('#issue-loan-modal').data('officer-id');
            const selectedRoutes = $('#routeSelect').val(); // Get multiple selected values

            if (!officerId || !selectedRoutes || selectedRoutes.length === 0) {
                Swal.fire('Error', 'Please select at least one route.', 'error');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to assign these routes.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, assign them!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("update.route") }}',
                        type: 'POST',
                        data: {
                            officer_id: officerId,
                            route_ids: selectedRoutes, // Send array of route IDs
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Success', 'Routes assigned successfully!', 'success')
                                    .then(() => {
                                        location.reload(); // Refresh the page
                                    });
                            } else {
                                Swal.fire('Error', 'Failed to assign routes.', 'error');
                            }
                        },
                        error: function(error) {
                            Swal.fire('Error', 'An error occurred. Please try again.', 'error');
                        }
                    });
                }
            });
        }



    </script>

@endsection


