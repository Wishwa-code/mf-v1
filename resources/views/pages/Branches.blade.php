@extends('layout.admin')
@section('head')
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
                            <h4 class="page-title">Branches</h4>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#standard-modal">
                                Request A Branch
                            </button>
                        </div>
                        <table id="centerTable" class="responsive nowrap table  table-bordered" >
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($branches as $item)
                                <tr class="style-tr">
                                    <td>{{$item->Name}}</td>
                                    @if($item->status==1)
                                        <td><span style="color: green">Active</span></td>
                                    @else
                                        <td><span style="color: red">Inactive</span></td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="standard-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title" >gwegerg</h4> -->
                        <h4>Request Branch</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Branch Number<span class="required-asterisk">*</span></label>
                                    <input type="text" id="branch" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="saveBranchButton" class="btn btn-success">Save Branch</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="assets/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="assets/js/pages/dashboard.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/center.js?n=5"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
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

    <script>
        $(document).ready(function () {
            $('#saveBranchButton').on('click', function () {
                let branchName = $('#branch').val();

                // Clear previous alerts
                $('.alert').remove();

                // Validate input
                if (branchName.trim() === '') {
                    $('#branch').after('<div class="alert alert-danger mt-2">Branch name is required.</div>');
                    return;
                }

                // SweetAlert confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to save this branch?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, save it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send AJAX request
                        $.ajax({
                            url: "{{ route('save.branch') }}",
                            type: "POST",
                            data: {
                                branch: branchName,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                if (response.success) {
                                    window.location.reload();
                                }
                            },
                            error: function (xhr) {
                                Swal.fire(
                                    'Error!',
                                    'An error occurred: ' + xhr.responseJSON.message,
                                    'error'
                                );
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire(
                            'Cancelled',
                            'Branch saving was cancelled.',
                            'error'
                        );
                    }
                });
            });
        });
    </script>

@endsection
