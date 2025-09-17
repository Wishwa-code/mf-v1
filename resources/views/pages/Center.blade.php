@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <style>
        .style-tr>td {
            padding: 2px 15px
        }
        /* Custom thead style */
        thead {
            background-color: #d9edf7; /* Light blue color */
            color: #31708f; /* Darker blue text for contrast */
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
                            <h4 class="page-title">Centers</h4>
                        </div>
                        <table id="centerTable" class="display nowrap table table-striped table-bordered" style="width:100%">
                            <thead>
                            <tr>
                                <th>Route</th>
                                <th>Center No</th>
                                <th>Center Name</th>
                                <th>Contact Number</th>
                                <th>Address</th>
                                <th>Location</th>
                                <th>Center In-charge</th>
                                <th class="text-center">Group Count</th>
                                <th class=" text-center">Member Count</th>
                                <th style="width:20%" class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($userData as $item)
                                <tr class="style-tr">
                                    <td>{{$item->name ?? '-'}}</td>
                                    <td>{{$item->No}}</td>
                                    <td>{{$item->Name}}</td>
                                    <td>{{$item->Contact_no}}</td>
                                    <td>{{$item->Address}}</td>
                                    <td>{{$item->Location}}</td>
                                    <td>{{$item->Center_incharge}}</td>
                                    <td class="text-center">{{$item->Groups}}</td>
                                    <td class="text-center">{{$item->Members}}</td>
                                    <td  class="text-center">
                                        <button type="button" class="btn btn-light edit-btn" data-bs-toggle="modal" data-bs-target="#standard-modal"
                                                data-canter-no="{{$item->No}}" data-center-name="{{$item->Name}}" data-contact-no="{{$item->Contact_no}}"
                                                data-address="{{$item->Address}}" data-route="{{$item->Route}}" data-location="{{$item->Location}}" data-center-incharge="{{$item->Center_incharge}}"
                                                data-center-id="{{$item->idCenter}}" data-route-id="{{$item->id_route}}" data-bs-placement="top" title="Edit Center">
                                            <i class="bi bi-pencil fs-4"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" onclick="delete_center('{{$item->idCenter}}')" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Center">
                                            <i class="bi bi-trash fs-4"></i>
                                        </button>

                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                    </div> <!-- end card-->
                </div> <!-- end col -->


            </div>
            <!-- end row -->

        </div>


        <div class="modal fade" id="standard-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title" >gwegerg</h4> -->
                        <h4>Edit Center</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" id="center_id">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="center_incharge" class="form-label">Select Route<span class="required-asterisk">*</span></label>
                                    <select class="form-control" id="route_id">
                                        @foreach($route as $item)
                                            <option value="{{$item->id_route}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Center Number<span class="required-asterisk">*</span></label>
                                    <input type="text" id="center_number" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Center Name<span class="required-asterisk">*</span></label>
                                    <input type="text" id="center_name" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Contact Number<span class="required-asterisk">*</span></label>
                                    <input type="text" id="contact" class="form-control">
                                </div>
                                <div class="mb-3" hidden>
                                    <label for="simpleinput" class="form-label">Route<span class="required-asterisk">*</span></label>
                                    <input type="text" id="route" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Address<span class="required-asterisk">*</span></label>
                                    <input type="text" id="address" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Location<span class="required-asterisk">*</span></label>
                                    <input type="text" id="location" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Center In-charge<span class="required-asterisk">*</span></label>
                                    <input type="text" id="center_incharge" class="form-control">
                                </div>


                            </div>



                        </div>


                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" onclick="update_center()">Update changes</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->








    </div>
@endsection

@section('script')
    <script src="assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="assets/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="assets/js/pages/dashboard.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/center.js?n=6"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {

            // Replace special characters in the company name
            var companyName = {!! json_encode(session('company_name')) !!}.replace(/&/g, ' And ');

            $('#centerTable').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="bi bi-clipboard"></i> Copy',
                        className: 'btn btn-secondary',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'csv',
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                        className: 'btn btn-primary',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        customize: function(doc) {
                            doc.defaultStyle.fontSize = 10; // Example customization
                            doc.styles.tableHeader.fontSize = 12;
                        },
                        filename: companyName
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i> Print',
                        className: 'btn btn-info',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        filename: companyName
                    }
                ],
                initComplete: function(settings, json) {
                    // Initialize tooltips after DataTable is fully initialized
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });


            $(document).on('click', '.edit-btn', function() {
                const editButton = $(this); // Use $(this) instead of document.querySelector
                // Retrieve the data attributes from the button
                const No = editButton.data('canter-no');
                const name = editButton.data('center-name');
                const Contact_no = editButton.data('contact-no');
                const Address = editButton.data('address');
                const route = editButton.data('route');
                const center_incharge = editButton.data('center-incharge');
                const center_id = editButton.data('center-id');
                const location = editButton.data('location');
                const route_id = editButton.data('route-id');

                // Set the values of the input fields
                $('#center_number').val(No);
                $('#center_name').val(name);
                $('#contact').val(Contact_no);
                $('#address').val(Address);
                $('#route').val(route);
                $('#center_incharge').val(center_incharge);
                $('#center_id').val(center_id);
                $('#location').val(location);
                $('#route_id').val(route_id);
            });

            // Initialize tooltips on document ready
            $('[data-bs-toggle="tooltip"]').tooltip();
        });



    </script>
@endsection
