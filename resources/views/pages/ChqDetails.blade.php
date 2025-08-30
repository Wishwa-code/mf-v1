@extends('layout.admin')

@section('head')
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .profile-card {
            margin: auto;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        .profile-card .card-header {
            background: #007bff;
            color: white;
            text-align: center;
        }
        .profile-card .profile-image {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: -50px;
        }
        .profile-card .profile-image img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 5px solid white;
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
                            <h4 class="page-title">Cheque Details</h4>
                            <div class="d-flex gap-2 align-items-center">
                                <div class="d-flex gap-2 align-items-center">
                                    <label class="form-label mb-0" style="min-width: 60px; font-weight: 500;">From:</label>
                                    <input type="date" id="start_date" value="{{ request('start_date') }}" class="form-control" style="width: 150px;">
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <label class="form-label mb-0" style="min-width: 40px; font-weight: 500;">To:</label>
                                    <input type="date" id="end_date" value="{{ request('end_date') }}" class="form-control" style="width: 150px;">
                                </div>
                                <button class="btn btn-primary" onclick="applyDateRangeFilter()">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <a href="{{ route('bank.chq') }}" class="btn btn-secondary">
                                    <i class="fas fa-refresh me-1"></i>Show All
                                </a>
                            </div>
                        </div>




                        <div class="modal-body">
                            <div class="row">
                                <table class="table table-centered mb-0" id="bank_table">
                                    <thead>
                                    <tr>
                                        <th>Date Time</th>
                                        <th>Loan No</th>
                                        <th>Cheque No</th>
                                        <th>Name Of Cheque</th>
                                        <th>Cheque Type</th>
                                        <th>Cheque Date</th>
                                        <th>Bank Account</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th style="text-align: center">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($chq as $item)
                                        <tr>
                                            <td>{{$item->date}} {{$item->time}}</td>
                                            <td>{{$item->Loan_No}}</td>
                                            <td>{{$item->chq_number}}</td>
                                            <td>{{$item->name_on_cheque}}</td>
                                            <td>{{$item->chq_type}}</td>
                                            <td>{{$item->chq_date}}</td>
                                            <td>{{$item->Bank_Name}}-{{$item->Account_No}}</td>
                                            <td>{{ number_format(str_replace(',', '', $item->payment_amount), 2, '.', ',') }}</td>
                                            @if($item->chq_status==="1")
                                                <td><span style="color: red">Proceeded</span></td>
                                            @elseif($item->chq_status==="-1")
                                                <td><span style="color: #ffab00">Returned</span></td>
                                            @else
                                                <td><span style="color: green">Pending</span></td>
                                            @endif
                                            <td style="text-align: center">
                                                @if($item->chq_status==="0")
                                                    <button type="button" class="btn btn-success"
                                                            style="background-color: white; color: #5691FF;"
                                                            onclick="process(
        {{ $item->idChq }},'{{ $item->cus_id }}',
        '{{ $item->payment_amount }}',
        '{{ $item->file }}',
        '{{ $item->loan_id }}',
        '{{ $item->chq_date }}',
        '{{ $item->payment_type }}',
        '{{ $item->bank_account_company }}',
        '{{ $item->cheque_issue_bank }}',
        '{{ $item->name_on_cheque }}',
        '{{ $item->chq_number }}',
        '{{ $item->chq_date }}',
        '{{ $item->chq_type }}'
    )">
                                                        Process
                                                        <i class="fas fa-spinner fa-spin fs-4" style="display:none;" id="process_icon_{{ $item->idChq }}"></i>
                                                    </button>

                                                    <button type="button" class="btn btn-danger"
                                                            style="background-color: white; color: #f51515;" onclick="decline({{ $item->idChq }})">Return
                                                        <i class="fas fa-spinner fa-spin fs-4" style="display:none;" id="return_icon_{{ $item->idChq }}"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-success"
                                                            style="background-color: white; color: #5691FF;" disabled>Process
                                                        <i class="fas fa-spinner fa-spin fs-4" style="display:none;" id="process_icon_{{ $item->idChq }}"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger"
                                                            style="background-color: white; color: #f51515;" disabled>Return
                                                        <i class="fas fa-spinner fa-spin fs-4" style="display:none;" id="return_icon_{{ $item->idChq }}"></i>
                                                    </button>
                                                @endif




                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>




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
                        <h4>Bank Log Report</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table table-centered mb-0" id="bank_table_log">
                                    <thead>
                                    <tr>
                                        <th>Date Time</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Note</th>
                                        <th>Credit</th>
                                        <th>Debit</th>
                                        <th>Balance</th>
                                        <th>User</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->







    </div>
@endsection

@section('script')
    <!-- jQuery and Bootstrap 5 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="../JS/validate.js"></script>
    <script>
        function process(id,cus_id,payment_amount,file,loan_id,payment_date,payment_type,bank_account_company,cheque_issue_bank,name_on_cheque,chq_number,chq_date,chq_type) {
            let date = new Date().toISOString().slice(0, 10); // "2025-08-19"

            document.getElementById('process_icon_' + id).style.display = 'inline-block';
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to process this cheque payment?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Process it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading alert
                    Swal.fire({
                        title: "Processing...",
                        text: "Please wait while we process your payment.",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    let formData = new FormData();
                    formData.append('cus_id', cus_id);
                    formData.append('payment_amount', payment_amount);
                    formData.append('file', file);
                    formData.append('loan_id', loan_id);
                    formData.append('payment_date', date);
                    formData.append('payment_type', payment_type);
                    formData.append('bank_account_company', bank_account_company);
                    formData.append('cheque_issue_bank', cheque_issue_bank);
                    formData.append('name_on_cheque', name_on_cheque);
                    formData.append('chq_number', chq_number);
                    formData.append('chq_date', chq_date);
                    formData.append('chq_type', chq_type);
                    formData.append('cheque_accept', "1");
                    formData.append('cheque_id', id);

                    $.ajax({
                        type: "POST",
                        url: "/payment_save_today",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (data, textStatus, xhr) {
                            let payment_id = data.payment_id;
                            if (xhr.status === 200) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Successfully processed !",
                                }).then(function () {
                                    // if (payment_id===0){
                                    //     window.location.reload();
                                    // }else{
                                    //     load_payment_reciept(payment_id);
                                    // }
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire("Error!", "Failed to save data!", "error");
                                document.getElementById('process_icon_' + id).style.display = 'none';
                            }
                        },
                        error: function () {
                            Swal.fire("Error!", "Failed to save data!", "error");
                            document.getElementById('process_icon_' + id).style.display = 'none';
                        },
                        complete: function () {
                            Swal.close(); // Close loading alert after completion
                        }
                    });
                }
            });
        }


        function decline(id) {
// Show the process icon before making the AJAX request
            document.getElementById('return_icon_' + id).style.display = 'inline-block';
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to return this cheque?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Return it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "GET",
                        url: "/return_chq/" + id,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function (data, textStatus, xhr) {
                            // Hide the process icon after the AJAX request completes
                            document.getElementById('process_icon_' + id).style.display = 'none';

                            if (xhr.status === 200) {
                                // Reload the page or update the UI as needed
                                location.reload();
                            } else {
                                Swal.fire("Error!", "Failed to save data!", "error");
                            }
                        },
                        error: function() {
                            // Hide the process icon in case of an error
                            document.getElementById('return_icon_' + id).style.display = 'none';
                            Swal.fire("Error!", "An error occurred during the process!", "error");
                        }
                    });
                }else{
                    document.getElementById('return_icon_' + id).style.display = 'none';
                }
            });
        }
        function applyDateRangeFilter() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            // make request 
            let queryParams = new URLSearchParams();
            
            if (startDate) {
                queryParams.append('start_date', startDate);
            }
            
            if (endDate) {
                queryParams.append('end_date', endDate);
            }
            
            // Validate date range
            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Date Range',
                    text: 'Start date cannot be later than end date.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            
            // Error handling
            if (queryParams.toString()) {
                window.location.href = `?${queryParams.toString()}`;
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'No Date Selected',
                    text: 'Please select at least one date to filter.',
                    confirmButtonColor: '#3085d6'
                });
            }
        }


    </script>



@endsection
