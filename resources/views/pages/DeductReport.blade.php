@extends('layout.admin')

@section('head')
    <style>
        thead {
            background-color: #d9edf7; /* Light blue color */
            color: #31708f; /* Darker blue text for contrast */
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
                    <h4 class="page-title">Group Wise Loan Summary Report</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Center</label>
                                    <select class="form-control select2" id="center_details">
                                        <option value="0">All</option>
                                        @foreach($center as $item)
                                            <option value="{{$item->idCenter}}">{{ $item->No }}-{{ $item->Name }}-{{ $item->Contact_no }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Group</label>
                                    <select class="form-control select2" id="group">
                                        <option value="0">All</option>
                                        @foreach($group as $item)
                                            <option value="{{$item->idCustomer_Group}}">{{ $item->Name }}-{{ $item->Leader_name }}-{{ $item->Contact_no }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-lg-3" hidden>
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Customer</label>
                                    <select class="form-control select2" id="customer_id">
                                        <option value="0">All</option>
                                        @foreach($customers as $item)
                                            <option value="{{$item->idCustomer}}">{{ $item->First_Name }} {{$item->Last_Name}}-{{ $item->Nic }}-{{ $item->Contact_No }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3" hidden>
                                    <label for="simpleinput" class="form-label">Category</label>
                                    <select class="form-control select2" id="category">
                                        <option value="0">All</option>
                                        @foreach($loan_category as $item)
                                            <option value="{{$item->idLoan_Category}}">{{ $item->Name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">

                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <button type="button" class="btn btn-danger" onclick="load_table();"><i class="bi bi-search"></i> </button>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="table-responsive-sm">
                            <table class="table table-centered mb-0" id="loan_table">
                                <thead>
                                <tr>
                                    <th>Group</th>
                                    <th>Customer Name</th>
                                    <th>Loan Amount</th>
                                    <th>Total Loan Amount(With Interest)</th>
                                    <th>Capital Balance</th>
                                    <th>Paid Capital</th>
                                    <th>Paid Capital + Interest</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->

                        <div class="row mt-1 mb-1 p-2">
                            <div class="col-md-8 row">
                                <div class="col-sm-5">
                                    <div>
                                        <span class="fw-bold">Total Issued Loan Amount </span>
                                    </div>
                                    <div>
                                        <span class="fw-bold">Total Loan(With Interest)</span>
                                    </div>
                                    <div>
                                        <span class="fw-bold">Total Capital Balance</span>
                                    </div>
                                    <div>
                                        <span class="fw-bold">Total Paid Capital</span>
                                    </div>
                                    <div>
                                        <span class="fw-bold">Total Paid Amount(With Interest)</span>
                                    </div>
                                </div>
                                <div class="col-lg-2 text-end">
                                    <div>
                                        <span id="loan_count">0</span>
                                    </div>
                                    <div>
                                        <span id="tot_amount">0.00</span>
                                    </div>
                                    <div>
                                        <span id="tot_capital_balance">0.00</span>
                                    </div>
                                    <div>
                                        <span id="paid_capital">0.00</span>
                                    </div>
                                    <div>
                                        <span id="tot_paid_amount">0.00</span>
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




@endsection

@section('script')
    <script src="../JS/validate.js"></script>
    <script>
        $(function() {

            load_table();
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

        })

        function load_table() {
            let group = $("#group").val();
            let center_details = $("#center_details").val();

            $.ajax({
                type: "GET",
                url: "/deduct_report_view",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    group: group,
                    center_details: center_details,
                },
                success: function (data, textStatus, xhr) {
                    console.log(data);
                    if (xhr.status === 200) {
                        let totalIssuedLoanAmount = 0.0;
                        let totalLoanAmountWithInterest = 0.0;
                        let totalCapitalBalance = 0.0;
                        let totalPaidCapital = 0.0;
                        let totalPaidAmountWithInterest = 0.0;
                        let loan_count = 0.0;

                        // Clear existing table rows
                        $('#loan_table tbody').empty();

                        // Loop through each item in the response data
                        data.items.forEach(function (item) {
                            // Create a new row for each item
                            var newRow = $('<tr>');

                            // Calculate the difference
                            let paidCapital = parseFloat(item.Amount) - parseFloat(item.capital_balance);

                            // Add cells with data to the new row
                            newRow.append('<td>' + item.Name + '</td>');
                            newRow.append('<td>' + item.First_Name + ' ' + item.Last_Name + '</td>');
                            newRow.append('<td>' + parseFloat(item.Amount).toFixed(2) + '</td>');
                            newRow.append('<td>' + parseFloat(item.Total_Loan_Amount).toFixed(2) + '</td>');
                            newRow.append('<td>' + parseFloat(item.capital_balance).toFixed(2) + '</td>');
                            newRow.append('<td>' + paidCapital.toFixed(2) + '</td>');
                            newRow.append('<td>' + parseFloat(item.total_payments).toFixed(2) + '</td>');

                            // Calculate totals
                            totalIssuedLoanAmount += parseFloat(item.Amount);
                            totalLoanAmountWithInterest += parseFloat(item.Total_Loan_Amount);
                            totalCapitalBalance += parseFloat(item.capital_balance);
                            totalPaidCapital += paidCapital;
                            totalPaidAmountWithInterest += parseFloat(item.total_payments);
                            loan_count += parseFloat(item.Amount);

                            // Append the new row to the table body
                            $('#loan_table tbody').append(newRow);
                        });

                        // Update total amount and loan count in the UI
                        $('#loan_count').text(totalIssuedLoanAmount.toFixed(2));
                        $('#tot_amount').text(totalLoanAmountWithInterest.toFixed(2));
                        $('#tot_capital_balance').text(totalCapitalBalance.toFixed(2));
                        $('#paid_capital').text(totalPaidCapital.toFixed(2));
                        $('#tot_paid_amount').text(totalPaidAmountWithInterest.toFixed(2));
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    console.error("Error fetching data:", errorThrown);
                    // Handle error scenario if needed
                }
            });
        }


    </script>


@endsection

