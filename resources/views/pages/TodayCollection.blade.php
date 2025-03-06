@extends('layout.admin')

@section('head')
    <style>
        .total-pending-container {
            background-color: #1A2942; /* Light background color */
            padding: 5px; /* Padding around the container */
            margin-top: 10px; /* Top margin */
            border: 1px solid #dee2e6; /* Border color */
            border-radius: 5px; /* Rounded corners */

        }

        .total-pending-details {
            display: flex; /* Flex layout */
            justify-content: space-between; /* Space between elements */
            align-items: center; /* Center vertically */
            margin: 8px; /* Top margin */
            font-weight: bold; /* Bold text */
            font-size: 18px; /* Larger font size */
            /*margin-bottom: 10px; !* Bottom margin *!*/
            color: #d3d3d3;
        }

        .bg-purple th {
            color: #e1e1e1 !important; /* Ensure white text color for th elements */
        }


        .bg-purple {
            background-color: #1A2942 !important; /* Purple color */
            color: white !important; /* White text color */
        }

    </style>
@endsection


@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    </div>
                    <h4 class="page-title">Collector Wise Collection</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <div class="row">
                            <form action="{{route('collection.create')}}" method="post">
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
                                <thead class="bg-purple">
                                <tr>
                                    <th>Collector Name</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $tot=0;
                                ?>
                                @foreach($userPayments as $payment)
                                    <tr>
                                        <td>{{ $payment['username'] }}</td>
                                        <td>{{ $payment['date'] }}</td>
                                        <td>{{ number_format($payment['total_amount'], 2, '.', ',') }}</td>
                                        @if($payment['confirm_user']==="-")
                                            <td><span class="px-1" style="background-color: #ff0000;border-radius: 10px; color: #ff0000;">-</span></td>
                                        @else
                                            <td ><span class="px-1" style="background-color: #05ff00;border-radius: 10px; color: #05ff00;">-</span></td>
                                        @endif
                                        <td>
                                            <div>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" onclick="load_data('{{ $payment['user_id'] }}','{{ $payment['date'] }}')" data-bs-target="#standard-modal"><i class="bi bi-eye"></i> </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                        <?php $tot += $payment['total_amount']; ?>
                                @endforeach
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->

                        <div class="total-pending-container">
                            <div class="total-pending-details">
                                <div>
                                    <span class="total-pending-label">Total Pending Amount :</span>
                                    <span id="tot_amount">Rs. <?php echo number_format($tot, 2, '.', ','); ?></span>
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
                                <th>Loan Number</th>
                                <th>Customer Number</th>
                                <th>Customer Name</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div> <!-- end table-responsive-->




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
            let type="2";
            $.ajax({
                type: "POST",
                url: "/collection_view",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    userid: userid,
                    date: date,
                    type:type
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
                                "<td>" + item.Loan_No + "</td>" +
                                "<td>" + item.cus_number + "</td>" +
                                "<td>" + item.First_Name + " " + item.Last_Name + "</td>" +
                                "<td>" + item.Description + "</td>" +
                                "<td>" + parseFloat(item.PayedAmount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + "</td>" +
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

