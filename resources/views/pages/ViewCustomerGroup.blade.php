@extends('layout.admin')

@section('head')


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
                        <h4 class="page-title">Customer Groups</h4>
                    </div>
                    <div class=" bg-white mb-4">
                        <div class="row">
                            <div class="col-md-5 col-sm-12">
                                <table class=' table-bordered' style="border-color:#CBC9C8;">
                                    <tr>
                                        <td class="col-md-5 p-1">Group Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class='p-1'>{{$group->Name}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive-sm border border-1">
                        <table class="table table-centered mb-0">
                            <thead>
                                <tr>
{{--                                    <th style="width:10%">Member No</th>--}}
                                    <th style="width:10%">Full Name</th>
                                    <th>NIC</th>
                                    <th>Address</th>
                                    <th>Contact No</th>
                                    <th>Note</th>
                                    <th>Total Loan Amount</th>
{{--                                    <th>Total Pending Amount</th>--}}
                                    <th>Status</th>
                                    <th>Location</th>
                                    <th style="width:15%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer as $item)


                                        <?php
                                        $totalLoanAmount = \Illuminate\Support\Facades\DB::table('customer_loan')
                                            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
                                            ->join('customer_group', 'customer.Customer_Group_idCustomer_Group', '=', 'customer_group.idCustomer_Group')
                                            ->where('customer.idCustomer', '=', $item->idCustomer)
                                            ->where('branch_id', session('branch_id'))
                                            ->where('customer_loan.Status', '=', '0')
                                            ->sum('customer_loan.Total_Loan_Amount');
                                        $totalpemdingLoanAmount = \Illuminate\Support\Facades\DB::table('customer_loan')
                                            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
                                            ->join('customer_group', 'customer.Customer_Group_idCustomer_Group', '=', 'customer_group.idCustomer_Group')
                                            ->where('customer.idCustomer', '=', $item->idCustomer)
                                            ->where('branch_id', session('branch_id'))
                                            ->where('customer_loan.Status', '=', '0')
                                            ->sum('customer_loan.Balance_Amount');
                                        ?>

                                <tr>
{{--                                    <td>001</td>--}}
                                    <td>{{$item->Full_Name}}</td>
                                    <td>{{$item->NIC}}</td>
                                    <td>{{$item->Address}}</td>
                                    <td>{{$item->Contact_No}}</td>
                                    <td>{{ $item->Note ? $item->Note : '-' }}</td>
                                    <td>{{number_format($totalLoanAmount, 2) }}</td>
{{--                                    <td>{{number_format($totalpemdingLoanAmount, 2) }}</td>--}}

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
                                                    onclick="load_document({{$item->idCustomer}});"><i
                                                        class="bi bi-eye fs-4"></i> </button>

                                            </div>
                                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                                    style="background-color: white; color: #5691FF; border:none"
                                                    onclick="setGroupIdGroup('{{ $item->idCustomer_Group }}',{{ $item->idCustomer }})"
                                                    data-bs-target="#view-modal_2"><i
                                                    class="bi bi-trash fs-4"></i> </button>
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



    <div class="modal fade" id="view-modal_2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Remove From Group</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="hidden" id="center_id_input" value="{{$id}}">
                            <input type="hidden" id="group_id_input">
                            <input type="hidden" id="customer_id_input">
                        </div>
                        <div class="modal-body">
                            <h4 class="page-title">Do you really want to remove this customer from this group ?</h4>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success" onclick="updateassignCustomer()">Remove</button>
                        </div>

                    </div>
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

            <script>
                $(document).ready(function() {

                });
            </script>


<script>
    function openMap(latitude, longitude) {
        var url = `https://maps.google.com/maps?q=${latitude},${longitude}`;
        window.open(url, '_blank');
    }
    function setGroupIdGroup(id,cus){
        $("#group_id_input").val(id);
        $("#customer_id_input").val(cus);
    }

    function updateassignCustomer(){
        var id = $('#group_id_input').val();
        var customer = $('#customer_id_input').val();
        var center = $('#center_id_input').val();

        console.log(customer);


        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to remove this Customer ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Remove !",
        }).then((result) => {
            if (result.isConfirmed) {
                // Send AJAX request
                $.ajax({
                    url: '/updateassigngrouomember',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id,
                        customer: customer,
                        center: center,
                    },
                    success: function(response) {
                        console.log(response);
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Successfully removed !",
                        }).then(function () {
                            window.location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        console.error(error);
                    }
                });

            }
        });
    }

</script>


            @endsection
