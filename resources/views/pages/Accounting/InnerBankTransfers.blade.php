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
                        <div class="col-md-12 mb-3">
                            <label for="toBank">To Bank Account</label>
                            <select id="toBank" name="toBank" class="form-control select2">
                                <option value="0">Select Bank Account</option>
                                @foreach($banks_2 as $bank)
                                    <option value="{{ $bank->Idbank }}">{{ $bank->Bank_Name }} - {{ $bank->Account_Name }} - {{ $bank->Account_No }}</option>
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
            // Initialize Select2
            $('.select2').select2();


            // Handle form submission
            $('form').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                // Get selected values
                var fromBank = $('#fromBank').val();
                var toBank = $('#toBank').val();
                var reason = $('#reason').val();
                var fromAmount = $('#fromAmount').val();

                // Check if "From Bank" and "To Bank" are the same
                if (fromBank === toBank) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'The "From Bank Account" and "To Bank Account" cannot be the same.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return; // Stop further execution
                }else if(fromBank===""){
                    Swal.fire({
                        title: 'Error!',
                        text: 'The select From Bank',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return; // Stop further execution
                }else if(toBank===""){
                    Swal.fire({
                        title: 'Error!',
                        text: 'The select To Bank',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return; // Stop further execution
                }else if(fromAmount===""){
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please enter Amount',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return; // Stop further execution
                }else if(reason===""){
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please enter reason',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return; // Stop further execution
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to make an inter-bank transfer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, proceed!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('bankTransfer.store') }}', // URL to send the request to
                            type: 'POST',
                            data: $(this).serialize(), // Serialize form data
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            },
                            success: function(response) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Successfully transferred !",
                                }).then(function () {
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'An error occurred: ' + xhr.responseText,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            });


        });

    </script>
@endsection
