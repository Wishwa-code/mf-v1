@extends('layout.admin')

@section('head')
    <style>
        .comment-input {
            max-width: 200px;
        }
        .table-centered {
            text-align: center;
        }
        #comment_section {
            margin-top: 20px;
        }
        .form-check-label {
            margin-bottom: 0;
        }
        .total-amount-card {
            background: linear-gradient(45deg, #f3ec78, #af4261);
            border: none;
            border-radius: 10px;
            color: #fff;
            padding: 20px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .total-amount-card h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: normal;
        }


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
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
@endsection

@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    </div>
                    <h4 class="page-title">Pending Approval Repayments</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <form action="{{ route('collection.pending_collection_filter') }}" method="post">
                                {{ csrf_field() }}
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Date</label>
                                        <input type="date" class="form-control" id="date" name="date" value="{{ $dates }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Date</label>
                                        <input type="date" class="form-control" id="date_2" name="date_2" value="{{ $date_2 }}">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-danger"><i class="bi bi-search"></i></button>
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
                                <?php $tot = 0; ?>
                                @foreach($userPayments as $payment)
                                    <tr>
                                        <td>{{ $payment['username'] }}</td>
                                        <td>{{ $payment['date'] }}</td>
                                        <td>{{ number_format($payment['total_amount'], 2, '.', '') }}</td>
                                        @if($payment['confirm_user'] === "-")
                                            <td><span class="badge bg-danger">Pending</span></td>
                                        @else
                                            <td><span class="badge bg-success">Confirmed</span></td>
                                        @endif
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-success" data-bs-toggle="modal" onclick="load_data('{{ $payment['user_id'] }}', '{{ $payment['date'] }}')" data-bs-target="#standard-modal">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                        <?php $tot += $payment['total_amount']; ?>
                                @endforeach
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive -->
                        <div class="total-pending-container">
                            <div class="total-pending-details">
                                <div>
                                    <span class="total-pending-label">Total Pending Amount :</span>
                                    <span class="total-pending-value">Rs. {{ number_format($tot, 2, '.', '') }}</span>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end card -->
                </div> <!-- end col -->

            </div> <!-- end row -->

        </div> <!-- container -->

    </div>

    <div class="modal fade" id="standard-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Collection Log</h4>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <!-- Place any additional content here if needed -->
                        </div>
                        <div class="form-check form-switch text-end">
                            <input type="checkbox" class="form-check-input" id="check_all">
                            <label class="form-check-label" for="check_all">Select All</label>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-centered mb-0 table-bordered" id="ins_table">
                            <thead class="thead-dark">
                            <tr>
                                <th>Time</th>
                                <th>Loan Number</th>
                                <th>Customer Number</th>
                                <th>Customer Name</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Rows will be appended here dynamically -->
                            </tbody>
                        </table>
                    </div> <!-- end table-responsive -->

                    <div class="mt-3" id="comment_section" style="display: none;">
                        <label for="comment" class="form-label">Comment</label>
                        <input type="text" class="form-control" id="comment" name="comment">
                    </div>
                    <input type="hidden" id="load_user_id" name="load_user_id">
                    <input type="hidden" id="load_date" name="load_date">
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-danger" onclick="confirm_payment()">Confirm</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="../JS/validate.js"></script>
    {{-- <script src="../JS/today_payment.js"></script> --}}

    <script>
        $(document).ready(function() {
            $('#loan_table').DataTable({
                "responsive": true, // Enable responsive feature
                "paging": true, // Enable pagination
                "lengthChange": true, // Enable page length change
                "searching": true, // Enable search bar
                "ordering": true, // Enable column sorting
                "info": true, // Enable table information display
                "autoWidth": false, // Disable auto-width calculation and use a fixed width
                "language": { // Customize the language settings if needed
                    "emptyTable": "No data available in table",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "lengthMenu": "Show _MENU_ entries",
                    "loadingRecords": "Loading...",
                    "processing": "Processing...",
                    "search": "Search:",
                    "zeroRecords": "No matching records found"
                }
            });

            let x = ["#payment_amount"];
            decimalFormat(x);
            $('.select2').select2();
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
        });

        function load_data(userid, date) {
            $("#load_user_id").val(userid);
            $("#load_date").val(date);
            let type = "0";
            $.ajax({
                type: "POST",
                url: "/collection_view_2",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    userid: userid,
                    date: date,
                    type: type
                },
                success: function(data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        console.log(data);

                        $("#ins_table tbody").empty();
                        $.each(data.item, function(index, item) {
                            var row = "<tr>" +
                                "<td>" + item.time + "</td>" +
                                "<td>" + item.Loan_No + "</td>" +
                                "<td>" + item.cus_number + "</td>" +
                                "<td>" + item.First_Name + " " + item.Last_Name + "</td>" +
                                "<td>" + item.Description + "</td>" +
                                "<td>" + parseFloat(item.Amount).toFixed(2) + "</td>" +
                                "<td>" +
                                "<div class='d-flex align-items-center'>" +
                                "<div class='form-check'>" +
                                "<input type='checkbox' class='form-check-input item-checkbox' id='checkbox_" + index + "'>" +
                                "<label class='form-check-label' for='checkbox_" + index + "'>Check</label>" +
                                "</div>" +
                                "<div class='ml-2'>" +
                                "<input type='text' class='form-control comment-input' id='comment_" + index + "' placeholder='Comment'>" +
                                "<input type='hidden' class='idCustomer_Payments' value='" + item.idCustomer_Payments + "'>" +
                                "</div>" +
                                "</div>" +
                                "</td>" +
                                "</tr>";
                            $("#ins_table tbody").append(row);
                        });
                        toggleCommentInputs();
                    } else {
                        Swal.fire("Error!", "Failed to save data!", "error");
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    Swal.fire("Error!", "Failed to retrieve data!", "error");
                }
            });
        }

        function toggleCommentInputs() {
            $('.comment-input').each(function() {
                if ($('#check_all').is(':checked')) {
                    if ($(this).val()) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                } else {
                    $(this).show();
                }
            });
        }

        $('#check_all').change(function() {
            if (this.checked) {
                $('.item-checkbox').prop('checked', true);
                $('#comment_section').show();
            } else {
                $('.item-checkbox').prop('checked', false);
                $('#comment_section').hide();
            }
            toggleCommentInputs();
        });

        $(document).on('change', '.item-checkbox', function() {
            if ($('#check_all').is(':checked')) {
                if ($('.item-checkbox:checked').length === $('.item-checkbox').length) {
                    $('#comment_section').show();
                } else {
                    $('#comment_section').hide();
                }
            }
            toggleCommentInputs();
        });

        function confirm_payment() {
            let userid = $("#load_user_id").val();
            let date = $("#load_date").val();
            let comment = $("#comment").val();

            if (comment === "") {
                comment = "-";
            }
            var isChecked = $("#check_all").prop('checked') ? 1 : 0;

            let checkedItems = [];
            $("#ins_table tbody tr").each(function(index, row) {
                if ($(row).find('.item-checkbox').prop('checked')) {
                    let time = $(row).find('td:nth-child(1)').text().trim();
                    let description = $(row).find('.description').text().trim();
                    let amount = $(row).find('td:nth-child(3)').text().trim();
                    let rowComment = $(row).find('.comment-input').val().trim();
                    let idCustomer_Payments = $(row).find('.idCustomer_Payments').val().trim();

                    checkedItems.push({
                        time: time,
                        description: description,
                        amount: amount,
                        comment: rowComment,
                        idCustomer_Payments: idCustomer_Payments
                    });
                }
            });

            if (checkedItems.length === 0) {
                Swal.fire("Error!", "Please select at least one item to confirm!", "error");
                return;
            }

            console.log(checkedItems);

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
                            date: date,
                            checkedItems: checkedItems,
                            isChecked: isChecked
                        },
                        success: function(data, textStatus, xhr) {
                            if (xhr.status === 200) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Successfully confirmed!",
                                }).then(function() {
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
