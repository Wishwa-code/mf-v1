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
                            <h4 class="page-title">Groups</h4>
                        </div>
                        <table id="centerTable" class="display nowrap table table-striped table-bordered" style="width:100%">
                            <thead>
                            <tr>
                                <th>Center</th>
                                <th>Group No</th>
{{--                                <th>Group Name</th>--}}
                                <th>Leader Name</th>
                                <th>Contact Number</th>
                                <th style="width:20%" class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($group as $item)
                                <tr class="style-tr">
                                    <td>{{$item->center_name}}</td>
                                    <td>{{$item->Group_No}}</td>
{{--                                    <td>{{$item->Name}}</td>--}}
                                    <td>{{$item->Leader_name}}</td>
                                    <td>{{$item->Contact_no}}</td>
                                    <td  class="text-center">
                                        <button type="button" class="btn btn-light edit-btn" data-bs-toggle="modal" data-bs-target="#standard-modal"
                                                data-group-no="{{$item->Group_No}}" data-group-name="{{$item->Name}}" data-contact-no="{{$item->Contact_no}}"
                                                data-leader="{{$item->Leader_name}}" data-group-id="{{$item->idCustomer_Group}}">
                                            <i class="bi bi-pencil fs-4"></i></button>
                                        <button type="button" class="btn btn-danger" onclick="delete_group('{{$item->idCustomer_Group}}')">
                                            <i class="bi bi-trash fs-4"></i></button>
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
                        <h4>Edit Group</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" id="group_id">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="center" class="form-label">Select Center<span class="required-asterisk">*</span></label>
                                    <select class="form-control" id="center_details">
                                        @foreach($center as $item)
                                            <option value="{{$item->idCenter}}">{{ $item->Name }}-{{$item->Contact_no}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Group Number<span class="required-asterisk">*</span></label>
                                    <input type="text" id="group_number" class="form-control">
                                </div>
                                <div class="mb-3" hidden>
                                    <label for="simpleinput" class="form-label">Group Name<span class="required-asterisk">*</span></label>
                                    <input type="text" id="group_name" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Leader Name<span class="required-asterisk">*</span></label>
                                    <input type="text" id="leader" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Contact Number<span class="required-asterisk">*</span></label>
                                    <input type="text" id="contact" class="form-control">
                                </div>


                            </div>



                        </div>


                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" onclick="update_group()">Update changes</button>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
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
                            columns: [0, 1, 2, 3]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'csv',
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                        className: 'btn btn-primary',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i> Print',
                        className: 'btn btn-info',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        },
                        filename: companyName
                    }
                ]
            });


            {{--data-group-no="{{$item->Group_No}}" data-group-name="{{$item->Name}}" data-contact-no="{{$item->Contact_no}}"--}}
            {{--data-leader="{{$item->Leader_name}}">--}}

            $(document).on('click', '.edit-btn', function() {

                // Retrieve the data attributes from the clicked button
                const No = $(this).data('group-no');
                const name = $(this).data('group-name');
                const Contact_no = $(this).data('contact-no');
                const Leader = $(this).data('leader');
                const group_id = $(this).data('group-id');

                // Set the values of the input fields
                $('#group_number').val(No);
                $('#group_name').val(No);
                $('#leader').val(Leader);
                $('#contact').val(Contact_no);
                $('#group_id').val(group_id);

            });


        });


        function update_group(){
            const group_number = $("#group_number").val();
            const group_name = $("#group_name").val();
            const leader = $("#leader").val();
            const contact = $("#contact").val();
            const group_id = $("#group_id").val();
            const center_id = $("#center_details").val();

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to update this Group ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Update it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "/group/update",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: {
                            group_number: group_number,
                            group_name: group_name,
                            leader: leader,
                            contact: contact,
                            center_id: center_id,
                            group_id: group_id
                        },
                        success: function (data, textStatus, xhr) {
                            if (xhr.status === 200) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Successfully updated!",
                                }).then(function () {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire("Error!", "Failed to update data!", "error");
                            }
                        },
                    });
                }
            });
        }


        function delete_group(id){
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to delete this Group ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "GET",
                        url: "/group/delete/"+id,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function (data, textStatus, xhr) {
                            if (xhr.status === 200) {
                                if (data.id === "1") {
                                    Swal.fire({
                                        position: "center",
                                        icon: "success",
                                        title: "Successfully deleted!",
                                    }).then(function () {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire("Error!", "This Group Has Customers", "error");
                                }
                            } else {
                                Swal.fire("Error!", "Failed to delete data!", "error");
                            }
                        },

                    });
                }
            });
        }


    </script>
@endsection
