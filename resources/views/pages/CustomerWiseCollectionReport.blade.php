@extends('layout.admin')

@section('head')

@endsection


@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    </div>
                    <h4 class="page-title">Customer Wise Repayment Collection</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <div class="row">
                            <form action="{{route('customerrepaymentreportview.create')}}" method="post">
                                {{csrf_field()}}
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label for="simpleinput" class="form-label">Date</label>
                                            <input type="date" class="form-control" id="date" name="date" value="{{ $dates }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <div class="mb-3">
                                            <label for="simpleinput" class="form-label">Date</label>
                                            <input type="date" class="form-control" id="date_2" name="date_2" value="{{ $date_2 }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-danger" ><i class="bi bi-search"></i> </button>
                                </div>
                            </form>
                        </div>
                        <hr>

                        <div class="table-responsive-sm">
                            <table class="table table-centered mb-0" id="loan_table">
                                <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Customer Name</th>
                                    <th>Loan ID</th>
                                    <th>Payment Amount</th>
                                    <th>Collector</th>

                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $tot=0;
                                ?>
{{--                                @foreach($userPayments as $payment)--}}
{{--                                    <tr>--}}
{{--                                        <td>{{ $payment['date'] }}</td>--}}
{{--                                        <td>{{ $payment['First_Name'] }} {{ $payment['Last_Name'] }}</td>--}}
{{--                                        <td>{{ $payment['Loan_No'] }}</td>--}}
{{--                                        <td>{{ $payment['Amount'] }}</td>--}}
{{--                                        <td>{{ $payment['Full_Name'] }}</td>--}}
{{--                                    </tr>--}}
{{--                                        <?php $tot += $payment['total_amount']; ?>--}}
{{--                                @endforeach--}}
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->

                        <div class="row mt-1 mb-1 p-2">
                            <div class="col-md-8 row">
                                <div class="col-sm-3">
                                    <div>
                                        <span class="fw-bold">Total Pending Amount</span>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div>
                                        <span id="tot_amount"><?php echo number_format($tot, 2, '.', ''); ?></span>
                                    </div>

                                </div>
                            </div>



                        </div>

                    </div> <!-- end card-->
                </div> <!-- end col -->


            </div>
            <!-- end row -->




        </div> <!-- container -->

    </div>




    <div class="modal fade" id="standard-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header pt-2">
                    <h4>Collection Log</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <div class="table-responsive-sm">
                        <table class="table table-centered mb-0" id="ins_table">
                            <thead>
                            <tr>
                                <th>Time</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div> <!-- end table-responsive-->

                    <br><br>
                    <div class="mb-3">
                        <label for="simpleinput" class="form-label">Comment</label>
                        <input type="text" class="form-control" id="comment" name="comment" >
                    </div>
                    <input type="hidden" id="load_user_id" name="load_user_id">
                    <input type="hidden" id="load_date" name="load_date">
                    <input type="button" class="btn btn-danger" style="float: right" onclick="confirm_payment()" value="Confirm">


                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


@endsection

@section('script')
    <script src="../JS/validate.js"></script>
    {{--    <script src="../JS/today_payment.js"></script>--}}

    <script>
        $(function() {

            let x = ["#payment_amount"];
            decimalFormat(x);
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

        })
    </script>


    <script>
        function load_data(userid, date) {
            $("#load_user_id").val(userid);
            $("#load_date").val(date);
            $.ajax({
                type: "POST",
                url: "/collection_view",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    userid: userid,
                    date: date
                },
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        console.log(data);

                        $("#ins_table tbody").empty();
                        // Iterate over the data array
                        $.each(data.item, function(index, item) {
                            // Construct table row dynamically
                            var row =  "<tr>" +
                                "<td>" + item.time + "</td>" +
                                "<td>" + item.Description + "</td>" +
                                "<td>" + parseFloat(item.Amount).toFixed(2) + "</td>" +
                                "</tr>";

                            // Append row to table body
                            $("#ins_table tbody").append(row);
                        });
                    } else {
                        Swal.fire("Error!", "Failed to save data!", "error");
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    Swal.fire("Error!", "Failed to retrieve data!", "error");
                }
            });
        }


        function confirm_payment() {
            let userid = $("#load_user_id").val();
            let date = $("#load_date").val();
            let comment = $("#comment").val();

            if (comment === "") {
                comment = "-";
            }

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to confirm this Payment?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Confirm it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "/collection_confirm",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: {
                            userid: userid,
                            comment: comment,
                            date: date
                        },
                        success: function (data, textStatus, xhr) {
                            if (xhr.status === 200) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Successfully confirmed!",
                                }).then(function () {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire("Error!", "Failed to save data!", "error");
                            }
                        },
                        error: function(xhr, textStatus, errorThrown) {
                            Swal.fire("Error!", "Failed to retrieve data!", "error");
                        }
                    });
                }
            });
        }


    </script>


@endsection

