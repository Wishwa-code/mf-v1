@extends('layout.admin')

@section('head')

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
@endsection


@section('content')
<div>

    <!-- start page title -->
    <!-- end page title -->

    <div class="row mt-3 ">
        <div class="col-12">
            <div class="card">


                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">
                        <h4 class="page-title">Loan Center Details</h4>
                    </div>
                    <div class=" bg-white mb-4">
                        <div class="row">
                            <div class="col-md-5 col-sm-12">
                                <table class=' table-bordered' style="border-color:#CBC9C8;">
                                    <tr>
                                        <td class="col-md-5 p-1">Center No</td>
                                        <td class='p-1'>{{$center->No}}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-5 p-1">Center Name</td>
                                        <td class='p-1'>{{$center->Name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-5 p-1">Route</td>
                                        <td class='p-1'>{{$center->Route}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive-sm border border-1">
                        <table class="table table-centered mb-0">
                            <thead>
                                <tr>
                                    <th style="width:10%">Full Name</th>
                                    <th>NIC</th>
                                    <th>Address</th>
                                    <th>Contact No</th>
                                    <th>Note</th>
                                    <th style="width:10%">Risk Level</th>
                                    <th>Status</th>
                                    <th>Location</th>
                                    <th style="width:15%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer as $item)
                                <tr>
                                    <td>{{$item->Full_Name}}</td>
                                    <td>{{$item->NIC}}</td>
                                    <td>{{$item->Address}}</td>
                                    <td>{{$item->Contact_No}}</td>
                                    <td>{{ $item->Note !== null ? $item->Note : '-' }}</td>
                                    <td class="text-center">{{$item->Customer_Risk_Level}}</td>
                                    @if($item->Status == "1")
                                    <td class="text-center"><span class="px-1"
                                            style="background-color: #10D100;border-radius: 10px; color: #10D100;">-</span>
                                    </td>
                                    @else
                                    <td><span class="px-2"
                                            style="background-color: #ff0000;border-radius: 10px; color: white;">Inactive</span>
                                    </td>
                                    @endif
                                    <td>
                                        <button type="button" class="btn btn-danger"
                                            style="background-color: white; color: #ff0000; border:none"
                                            onclick="openMap('{{ $item->Latitude }}', '{{ $item->Longitude }}')"><i
                                                class="bi bi-map fs-4"></i></button>
                                    </td>

                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <div class="d-flex flex-wrap">
                                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                                        style="background-color: white; color: #5691FF; border:none"
                                                        data-bs-target="#view-modal"
                                                        onclick="load_document({{$item->idCustomer}});"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title="View Document">
                                                    <i class="bi bi-eye fs-4"></i>
                                                </button>
                                                <button type="button" class="btn btn-warning edit-btn"
                                                        style="background-color: white; color: #FF922B; border:none"
                                                        data-full-name="{{$item->Full_Name}}"
                                                        data-first-name="{{$item->First_Name}}"
                                                        data-title="{{$item->Title}}"
                                                        data-nic="{{$item->NIC}}"
                                                        data-address="{{$item->Address}}"
                                                        data-contact-no="{{$item->Contact_No}}"
                                                        data-note="{{ $item->Note !== null ? $item->Note : '-' }}"
                                                        data-risk-level="{{$item->Customer_Risk_Level}}"
                                                        data-customer-id="{{$item->idCustomer}}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#standard-modal"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Customer">
                                                    <i class="bi bi-pencil fs-4"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>


                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div> <!-- end table-responsive-->


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
                    <h4>Edit Customer</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <input type="hidden" id="cus_id">
                            <div class="mb-3">
                                <label for="example-select" class="form-label">Title</label>
                                <select class="form-select" id="u_title">
                                    <option value="Mr">Mr</option>
                                    <option value="Mrs">Mrs</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">Full Name</label>
                                <input type="text" id="u_full_name" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">Address</label>
                                <input type="text" id="u_address" class="form-control">
                            </div>
                        </div> <!-- end col -->
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">First Name</label>
                                <input type="text" id="u_first_name" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">NIC</label>
                                <input type="text" id="u_nic" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="simpleinput" class="form-label">Contact No</label>
                                <input type="text" id="u_contact_no" class="form-control">
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <label for="simpleinput" class="form-label">Note</label>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="Leave a comment here" id="u_note"
                                style="height: 100px"></textarea>
                        </div>
                        <label for="simpleinput" class="form-label" hidden>Customer Risk Level</label>
                        <div class="mt-2 mb-3" hidden>
                            <div class="form-check form-check-inline">
                                <input type="radio" id="customRadio1" name="customRadio1" class="form-check-input" value="01" checked>
                                <label class="form-check-label" for="customRadio1">01</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" id="customRadio2" name="customRadio1" class="form-check-input" value="02">
                                <label class="form-check-label" for="customRadio2">02</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" id="customRadio3" name="customRadio1" class="form-check-input" value="03">
                                <label class="form-check-label" for="customRadio3">03</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" id="customRadio4" name="customRadio1" class="form-check-input" value="04">
                                <label class="form-check-label" for="customRadio4">04</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" id="customRadio5" name="customRadio1" class="form-check-input" value="05">
                                <label class="form-check-label" for="customRadio5">05</label>
                            </div>
                        </div>



                    </div>


                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" onclick="update_cus()">Update changes</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="view-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h4 class="modal-title" >gwegerg</h4> -->
                    <h4>Customer Document</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive-sm">
                        <table class="table table-centered mb-0" id="document_table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            @endsection
            @section('script')
            <!-- Daterangepicker js -->

            <script src="{{ asset('assets/js/pages/dashboard.js') }}"></script>
            <script src="{{ asset('../JS/group.js') }}"></script>
                <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
                <script>
                    // Initialize tooltips
                    document.addEventListener('DOMContentLoaded', function () {
                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });
                    });
                </script>
            <script>
                $(document).ready(function() {
                    $('.edit-btn').click(function() {
                        // Extract data from the button's data attributes
                        var fullName = $(this).data('full-name');
                        var firstName = $(this).data('first-name');
                        var nic = $(this).data('nic');
                        var address = $(this).data('address');
                        var contactNo = $(this).data('contact-no');
                        var note = $(this).data('note');
                        var customerId = $(this).data('customer-id');
                        var Title = $(this).data('title');



                        $("#u_title").val(Title);


                        // Populate modal fields with extracted data
                        $('#u_full_name').val(fullName);
                        $('#u_nic').val(nic);
                        $('#u_address').val(address);
                        $('#u_contact_no').val(contactNo);
                        $('#u_note').val(note);
                        $('#u_first_name').val(firstName);
                        $('#cus_id').val(customerId);
                        // Add similar lines to populate other fields

                        // Show the modal
                        $('#standard-modal').modal('show');
                    });
                });
            </script>




            <script>
            function openMap(latitude, longitude) {
                var url = `https://maps.google.com/maps?q=${latitude},${longitude}`;
                window.open(url, '_blank');
            }
            </script>
            @endsection
