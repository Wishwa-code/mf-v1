@extends('layout.admin')

@section('head')
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
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
                            <h4 class="page-title">Collector Account Details</h4>
                        </div>



                        <div class="modal-body">
                            <div class="row">
                                    <div class="card-body">

                                        <div class="table-responsive-sm">
                                            <table class="table table-centered mb-0" id="bank_table">
                                                <thead>
                                                <tr>
                                                    <th>Bank Name</th>
                                                    <th>Account Name</th>
                                                    <th>Account Number</th>
                                                    <th>Branch</th>
                                                    <th>Opening Balance</th>
                                                    <th>User</th>
                                                    <th style="text-align: center">Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($banks as $item)
                                                    <tr>
                                                        <td>{{$item->Bank_Name}}</td>
                                                        <td>{{$item->Account_Name}}</td>
                                                        <td>{{$item->Account_No}}</td>
                                                        <td>{{$item->Bank_Branch}}</td>
                                                        <td id="balance-{{$item->Idbank}}">{{number_format($item->Account_Balance,2,'.',',')}}</td>
                                                        <td>{{$item->Full_Name}}</td>
                                                        <td style="text-align: center">
                                                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                                                    style="background-color: white; color: #5691FF; border:none"
                                                                    data-bs-target="#standard-modal" onclick="view_log({{$item->Idbank}}, '{{$item->Bank_Name}}', '{{$item->Account_Name}}', '{{$item->Account_No}}')">
                                                                <i class="bi bi-eye fs-4"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                                                    style="background-color: white; color: #5691FF; border:none"
                                                                    data-bs-target="#standard-modal-2" onclick="openTransferModal({{$item->Idbank}}, {{$item->Account_Balance}})">
                                                                <i class="bi bi-rewind-circle fs-4"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                </div>
                            </div>
                        </div>




                    </div> <!-- end card-->
                </div> <!-- end col -->


            </div>
            <!-- end row -->

        </div>



        <div class="modal fade" id="standard-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Bank Log Report</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex mb-3">
                                    <button class="btn btn-success me-2" id="download_excel">
                                        <i class="fas fa-file-excel"></i> Download Excel
                                    </button>
                                    <button class="btn btn-primary" id="download_pdf">
                                        <i class="fas fa-file-pdf"></i> Download PDF
                                    </button>
                                </div>
                                <table class="table table-centered mb-0" id="bank_table_log">
                                    <thead>
                                    <tr>
                                        <th>Date Time</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Note</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Balance</th>
                                        <th>Contra Account</th>
                                        <th>Reconciliation No</th>
                                        <th>User</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!-- Rows will be dynamically populated -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->


        <!-- Transfer Modal -->
        <div class="modal fade" id="standard-modal-2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Transfer</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <input type="hidden" id="bankId" value="">
                                <select id="routeSelect" class="form-control mb-3">
                                    <option value="0">Select Bank</option>
                                    @foreach($company_banks as $r)
                                        <option value="{{ $r->Idbank }}">{{ $r->Bank_Name }}-{{ $r->Account_Name }}-{{ $r->Account_No }}</option>
                                    @endforeach
                                </select>
                                <input type="number" id="transferAmount" class="form-control mb-3" placeholder="Enter Amount" min="1">
                                <button type="button" class="btn btn-primary" onclick="initiateTransfer()">Transfer</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- jQuery and Bootstrap 5 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

    <script src="../JS/validate.js"></script>
    <script src="../JS/bank.js"></script>
    <!-- Include SheetJS for Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

    <!-- Include jsPDF and jsPDF AutoTable for PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.14/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(function() {
            let x = ["#opening_balance"];
            decimalFormat(x);
        })
        $(document).ready(function () {
            // Initialize DataTable with pagination
            $('#bank_table_log').DataTable({
                paging: true,             // Enables pagination
                lengthChange: true,       // Allows the user to change the number of records per page
                searching: true,          // Enables search functionality
                ordering: true,           // Enables column sorting
                info: true,               // Shows table information
                autoWidth: false,         // Disables automatic column width adjustment
                responsive: true          // Makes the table responsive
            });
        });
    </script>
    <script>
        // Replace special characters in the company name
        var companyName = {!! json_encode(session('company_name')) !!}.replace(/&/g, ' And ') + " Bank Details Report";

        // Function to download table as Excel
        document.getElementById('download_excel').addEventListener('click', function () {
            let table = document.getElementById('bank_table_log');
            let wb = XLSX.utils.table_to_book(table, {sheet: "Sheet JS"});
            XLSX.writeFile(wb, companyName+".xlsx");
        });

        // Function to download table as PDF
        document.getElementById('download_pdf').addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.autoTable({ html: '#bank_table_log' });
            doc.save(companyName+".pdf");
        });

        let maxAmount = 0;

        // Open Transfer Modal with Bank Data
        function openTransferModal(bankId, balance) {
            document.getElementById("bankId").value = bankId;
            document.getElementById("transferAmount").value = "";
            maxAmount = balance; // Set max amount for validation
        }

        // Transfer Function with Validation and SweetAlert Confirmation
        function initiateTransfer() {
            const bankId = document.getElementById("bankId").value;
            const selectedBankId = document.getElementById("routeSelect").value;
            const transferAmount = document.getElementById("transferAmount").value;

            if (selectedBankId == "0") {
                Swal.fire("Error", "Please select a destination bank", "error");
                return;
            }

            if (transferAmount === "" || transferAmount <= 0) {
                Swal.fire("Error", "Please enter a valid transfer amount", "error");
                return;
            }

            if (transferAmount > maxAmount) {
                Swal.fire("Error", "Transfer amount cannot exceed the available balance", "error");
                return;
            }

            // Confirm Transfer
            Swal.fire({
                title: "Confirm Transfer",
                text: `Transfer ${transferAmount} to selected bank?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, transfer it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX Call for Transfer
                    $.ajax({
                        url: '/transfer', // Your transfer route
                        method: 'POST',
                        data: {
                            bankId: bankId,
                            selectedBankId: selectedBankId,
                            amount: transferAmount,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Transfer completed successfully",
                                }).then(function () {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire("Error", response.message || "Transfer failed", "error");
                            }
                        },
                        error: function() {
                            Swal.fire("Error", "An error occurred during the transfer", "error");
                        }
                    });
                }
            });
        }


    </script>



@endsection
