@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <style>
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
        }



        .table thead th {
            font-weight: 600;
            background-color: #f1f1f1;
        }

        .table td,
        .table th {
            vertical-align: middle;
            padding: 15px;
        }



        .radio-group label {
            margin-right: 20px;
        }


    </style>
@endsection

@section('content')
    <br><br>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Internal Account Transfer</h4>
        </div>
        <div class="card-body">
            <form class="row g-3 mb-4">
                <!-- Left Section -->
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="fromBank">From Bank Account</label>
                            <select id="fromBank" name="fromBank" class="form-control select2">
                                <option value="0">Select Bank Account</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->Idbank }}">{{ $bank->Bank_Name }} - {{ $bank->Account_Name }} - {{ $bank->Account_No }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="fromBank">Amount</label>
                            <input type="text" class="form-control" name="fromAmount" id="fromAmount" placeholder="From Amount">
                        </div>
                    </div>
                </div>
                <!-- Right Section -->
                <div class="col-md-6">
                    <div class="row">
                        @if($isHeadOffice)
                        <div class="col-md-12 mb-3">
                            <label for="branchFilter">Filter by Branch</label>
                            <select id="branchFilter" class="form-control select2">
                                <option value="">All Branches</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->branch_id }}">{{ $branch->Name }} Branch</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-md-12 mb-3">
                            <label for="toBank">To Bank Account</label>
                            <select id="toBank" name="toBank" class="form-control select2">
                                <option value="0">Select Bank Account</option>
                                @foreach($banks_2 as $bank)
                                    <option value="{{ $bank->Idbank }}" data-branch="{{ $bank->branch_id }}">
                                        {{ $bank->Bank_Name }} - {{ $bank->Account_Name }} - {{ $bank->Account_No }}
                                        @if($isHeadOffice)
                                            @php
                                                $branchName = DB::table('branch')->where('branch_id', $bank->branch_id)->value('Name');
                                            @endphp
                                            ({{ $branchName ?? 'Head Office' }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="fromBank">Reason</label>
                            <input type="text" class="form-control" name="reason" id="reason" placeholder="Reason">
                        </div>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <button type="submit" class="btn btn-success me-2">Add Inter Bank Transfer</button>
                </div>
            </form>


        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h4>Internal Account Transfers</h4>
                <table id="transfersTable" class="table table-bordered table-hover">
                    <thead class="table-light">
                    <tr>
                        <th>Date Time</th>
                        <th>Description</th>
                        <th>Reason</th>
                        <th>Debit Amount</th>
                        <th>Credit Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($banklog as $item)
                            <tr>
                                <td>{{$item->Date_Time}}</td>
                                <td>{{$item->Description}}</td>
                                <td>{{$item->Note}}</td>
                                <td>{{number_format($item->Credit,2,'.',',')}}</td>
                                <td>{{number_format($item->Debit,2,'.',',')}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            let x = ["#fromAmount"];
            decimalFormat(x);

            $('.select2').select2();

            @if($isHeadOffice)
            var allOptions = $('#toBank option').clone();

            $('#branchFilter').on('change', function() {
                var selectedBranch = $(this).val();
                var $toBank = $('#toBank');

                $toBank.select2('destroy');
                $toBank.empty();

                $toBank.append('<option value="0">Select Bank Account</option>');

                allOptions.each(function() {
                    var $option = $(this);
                    var optionBranch = $option.data('branch');
                    if ($option.val() !== '0' && (selectedBranch === '' || optionBranch == selectedBranch)) {
                        $toBank.append($option.clone());
                    }
                });

                $toBank.select2();
            });
            @endif

            // -----------------------------------------
            // FORM SUBMIT WITH BEAUTIFUL SWEET ALERT
            // -----------------------------------------
            $('form').on('submit', function(e) {
                e.preventDefault();

                var fromBank = $('#fromBank').val();
                var toBank = $('#toBank').val();
                var reason = $('#reason').val();
                var fromAmount = $('#fromAmount').val();

                // ------------------------------------------------
                // FRONTEND VALIDATIONS
                // ------------------------------------------------
                if (fromBank === "0") {
                    return Swal.fire("Missing Info", "Please select a FROM bank account.", "warning");
                }
                if (toBank === "0") {
                    return Swal.fire("Missing Info", "Please select a TO bank account.", "warning");
                }
                if (fromBank === toBank) {
                    return Swal.fire("Invalid Selection", "FROM and TO bank accounts cannot be the same.", "error");
                }
                if (fromAmount === "") {
                    return Swal.fire("Missing Info", "Please enter an amount.", "warning");
                }
                if (reason === "") {
                    return Swal.fire("Missing Info", "Please enter a reason.", "warning");
                }

                // ------------------------------------------------
                // CONFIRMATION ALERT
                // ------------------------------------------------
                Swal.fire({
                    title: "Confirm Transfer",
                    html: "<b>You are about to transfer funds internally.</b><br><br>This action cannot be undone.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#198754",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, Transfer",
                    cancelButtonText: "Cancel"
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: '{{ route("bankTransfer.store") }}',
                            type: "POST",
                            data: $('form').serialize(),
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            },
                            success: function(res) {

                                // -------------------------
                                // STRICT POLICY FAIL CASES
                                // -------------------------
                                if (res.id === 0 && res.error === "Bank Balance is not enough") {
                                    return Swal.fire({
                                        icon: "error",
                                        title: "Insufficient Bank Balance",
                                        text: "The selected FROM account does not have enough balance to complete this transfer.",
                                    });
                                }

                                if (res.id === 0 && res.error === "Invalid bank account") {
                                    return Swal.fire({
                                        icon: "error",
                                        title: "Invalid Bank",
                                        text: "Selected bank account is not valid.",
                                    });
                                }

                                // -------------------------
                                // SUCCESS
                                // -------------------------
                                Swal.fire({
                                    icon: "success",
                                    title: "Transfer Completed!",
                                    text: "The internal bank transfer was successfully processed.",
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: "error",
                                    title: "System Error",
                                    text: "Something went wrong: " + xhr.responseText,
                                });
                            }
                        });
                    }
                });
            });

        });
    </script>

@endsection
