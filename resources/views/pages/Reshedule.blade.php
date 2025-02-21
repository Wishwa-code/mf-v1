@extends('layout.admin')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <style>
        .bg-purple th {
            color: #e1e1e1 !important;
        }

        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;

        }
        #loan_table tr, #loan_table th, #loan_table td {
            margin: 0 !important;
            padding: 10px !important; /* Adjust padding to your preference */
        }

        .table-centered {
            margin: 0 !important;
            padding: 0 !important;
        }

        #loan_table .btn {
            margin: 0 !important;
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
                    <h4 class="page-title">Loan Reschedule</h4>
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
                                    <label for="simpleinput" class="form-label">Group</label>
                                    <select class="form-control select2" id="group">
                                        <option value="0">All</option>
                                        @foreach($group as $item)
                                            <option value="{{$item->idCustomer_Group}}">{{ $item->Name }}-{{ $item->Leader_name }}-{{ $item->Contact_no }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-3">
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
                                <thead class="sticky-top bg-purple">
                                <tr>
                                    <th>Loan No</th>
                                    <th>Group</th>
                                    <th>Customer</th>
                                    <th>Loan Category</th>
                                    <th>Loan Amount</th>
                                    <th>Capital Balance</th>
                                    <th>Total Amount</th>
                                    <th>Total Balance</th>
                                    <th>Created Date</th>
                                    <th>Maturity Date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->

                        <div class="row mt-1 mb-1 p-2">
                            <div class="col-md-8 row">
                                <div class="col-sm-3">
                                    <div>
                                        <span class="fw-bold">Total Loan Count </span>
                                    </div>
                                    <div>
                                        <span class="fw-bold">Total Pending Amount</span>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div>
                                        <span id="loan_count">0</span>
                                    </div>
                                    <div>
                                        <span id="tot_amount">0.00</span>
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
                <div class="modal-header">
                    <h4>Settle Loan - </h4><h4 style="color: #000f42" id="loan_number"> 001</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <input type="hidden" id="loan-id" class="form-control" >
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="loan-amount">Customer Name</label>
                                <input type="text" id="cus_name" class="form-control" value="1000" readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="loan-amount">Loan Category</label>
                                <input type="text" id="loan-cate" class="form-control" value="1000" readonly>
                            </div>
                        </div>
                        <br><br><br><br>
                        <hr>

                        <!-- Loan Amount -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="loan-amount">Loan Amount</label>
                                <input type="text" id="loan-amount" class="form-control" value="1000" readonly>
                            </div>
                        </div>
                        <!-- Interest Amount -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="interest-amount">Interest Amount</label>
                                <input type="text" id="interest-amount" class="form-control" value="200" readonly>
                            </div>
                        </div>
                        <!-- Total Loan Amount -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="total-loan-amount">Total Loan Amount</label>
                                <input type="text" id="total-loan-amount" class="form-control" value="1200" readonly>
                            </div>
                        </div>
                        <!-- Total Paid Amount -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="total-paid-amount">Total Paid Amount</label>
                                <input type="text" id="total-paid-amount" class="form-control" value="600" readonly>
                            </div>
                        </div>
                        <!-- Total Paid Penalty -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="total-paid-penalty">Total Paid Penalty</label>
                                <input type="text" id="total-paid-penalty" class="form-control" value="50" readonly>
                            </div>
                        </div>
                        <!-- Net Capital Balance -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="net-capital-balance">Total Loan Balance</label>
                                <input type="text" id="tot-loan-balance" class="form-control" value="550" readonly>
                            </div>
                        </div>
                        <br><br> <br><br>
                        <hr>

                        <!-- Net Interest Balance (Read-Only) -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="net-capital-balance">Net Capital Balance</label>
                                <input type="text" id="net-capital-balance" class="form-control" value="550" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="net-interest-balance-readonly">Net Interest Balance (Original)</label>
                                <input type="text" id="net-interest-balance-readonly" class="form-control" readonly>
                            </div>
                        </div>
                        <!-- Net Interest Balance (Editable) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="net-interest-balance">Net Interest Balance (Editable)</label>
                                <input type="number" id="net-interest-balance" class="form-control">
                                <small class="text-danger" id="net-interest-difference"></small>
                            </div>
                        </div>
                        <!-- Net Penalty Balance (Read-Only) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="net-penalty-balance-readonly">Net Penalty Balance (Original)</label>
                                <input type="text" id="net-penalty-balance-readonly" class="form-control" value="50" readonly>
                            </div>
                        </div>
                        <!-- Net Penalty Balance (Editable) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="net-penalty-balance">Net Penalty Balance (Editable)</label>
                                <input type="number" id="net-penalty-balance" class="form-control">
                                <small class="text-danger" id="net-penalty-difference"></small>
                            </div>
                        </div>

                        <!-- Net Balance Amount -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="net-balance-amount">Net Balance Amount</label>
                                <input type="text" id="net-balance-amount" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" onclick="openRescheduleModal()"  class="btn btn-primary">
                        <i class="bi bi-rewind-circle"></i> Reschedule
                    </a>
                </div>
            </div>
        </div>
    </div>





@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/reschedule.js"></script>

    <script>
        $(document).ready(function() {

            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
            $('#net-interest-balance').on('input', function() {
                var originalValue = parseFloat($('#net-interest-balance-readonly').val());
                var newValue = parseFloat($(this).val());

                if (newValue > originalValue) {
                    $('#net-interest-difference').text('The value cannot exceed ' + originalValue);
                    $(this).val(originalValue); // Revert to original value
                    $('.btn-danger').prop('disabled', false); // Disable the button
                } else {
                    $('#net-interest-difference').text('Reduced interest amount: ' + (originalValue - newValue));
                    $('.btn-danger').prop('disabled', false); // Enable the button
                }
                calculateNetBalance()
            });

            $('#net-penalty-balance').on('input', function() {
                var originalValue = parseFloat($('#net-penalty-balance-readonly').val());
                var newValue = parseFloat($(this).val());

                if (newValue > originalValue) {
                    $('#net-penalty-difference').text('The value cannot exceed ' + originalValue);
                    $(this).val(originalValue); // Revert to original value
                    $('.btn-danger').prop('disabled', false); // Disable the button
                } else {
                    $('#net-penalty-difference').text('Reduced penalty amount: ' + (originalValue - newValue));
                    $('.btn-danger').prop('disabled', false); // Enable the button
                }
                calculateNetBalance()
            });



            $('#net-interest-balance, #net-penalty-balance').on('input', function() {
                var netCapitalBalance = parseFloat($('#net-capital-balance').val());
                var netInterestBalance = parseFloat($('#net-interest-balance').val()) || 0;
                var netPenaltyBalance = parseFloat($('#net-penalty-balance').val()) || 0;

                var netBalanceAmount = netCapitalBalance + netInterestBalance + netPenaltyBalance;
                // $('#net-balance-amount').val(netBalanceAmount.toFixed(2));
            });

            $('#standard-modal').on('show.bs.modal', function(e) {
                var button = $(e.relatedTarget);
                var loanId = button.data('loan-id');

                $.ajax({
                    url: '/get-loan-details/' + loanId,
                    method: 'GET',
                    success: function(response) {
                        console.log(response);
                        // Populate the modal with the data
                        $('#loan-amount').val(response.Amount);
                        $('#interest-amount').val(response.Interest_Amount);
                        $('#total-loan-amount').val(response.Total_Loan_Amount);
                        $('#total-paid-amount').val(response.Total_Paid_Amount);
                        $('#total-paid-penalty').val(response.Total_Paid_Penalty);
                        $('#net-capital-balance').val(response.capital_balance);
                        $('#net-interest-balance-readonly').val(response.Interest_Balance);
                        $('#net-interest-balance').val(response.Interest_Balance);
                        $('#net-penalty-balance-readonly').val(response.Panalty_Rate); // Adjust accordingly
                        $('#net-penalty-balance').val(response.Panalty_Rate); // Adjust accordingly
                        $('#net-balance-amount').val(response.Balance_Amount);
                        $('#tot-loan-balance').val(response.Tot_loan_balance);
                        calculateNetBalance();
                    }
                });
            });



        });


        document.getElementById('net-interest-balance').addEventListener('input', calculateNetBalance);
        document.getElementById('net-penalty-balance').addEventListener('input', calculateNetBalance);

        function calculateNetBalance() {
            const netCapital = parseFloat(document.getElementById('net-capital-balance').value) || 0;
            const netInterest = parseFloat(document.getElementById('net-interest-balance').value) || 0;
            const netPenalty = parseFloat(document.getElementById('net-penalty-balance').value) || 0;

            const netBalance = netCapital + netInterest + netPenalty;

            document.getElementById('net-balance-amount').value = netBalance.toFixed(2);
        }

        function openRescheduleModal() {

            let loanId = $("#loan-id").val();
            let balance = $("#net-balance-amount").val();

            let url = "{{ route('reschedule.index', ['loan_id' => ':loan_id', 'balance' => ':balance']) }}";

            url = url.replace(':loan_id', loanId).replace(':balance', balance);

            // Show confirmation with SweetAlert
            Swal.fire({
                title: 'Are you sure?',
                text: "If you press Yes, you will be redirected to the loan reschedule page.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reschedule it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to the generated URL
                    window.location.href = url;
                }
            });
        }

    </script>





@endsection

