@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        thead {
            background-color: #d9edf7; /* Light blue color */
            color: #31708f; /* Darker blue text for contrast */
        }

        .bg-purple th {
            color: #e1e1e1 !important;
        }

        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;
        }
    </style>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
                font-size: 10px; /* Adjust font size for better fit */
            }

            .page-title, .btn {
                display: none; /* Hide elements that should not be printed */
            }

            #repaymentTable {
                width: 100%;
                border-collapse: collapse;
            }

            #repaymentTable th, #repaymentTable td {
                padding: 5px;
                border: 1px solid #ddd;
                text-align: center;
            }

            @page {
                size: landscape; /* Set landscape orientation for print */
                margin: 0.5in; /* Adjust margins as needed */
            }
        }
        .table-responsive {
            width: 100%;
        }

        #repaymentTable {
            width: 100% !important;
            border-collapse: collapse;
            table-layout: auto; /* Adjusts columns automatically */
        }

        #repaymentTable th, #repaymentTable td {
            padding: 8px; /* More spacing */
            white-space: nowrap; /* Prevent text wrapping */
        }
        #repaymentTable td:nth-child(7),
        #repaymentTable td:nth-child(8),
        #repaymentTable td:nth-child(9),
        #repaymentTable td:nth-child(10),
        #repaymentTable td:nth-child(11),
        #repaymentTable td:nth-child(12),
        #repaymentTable td:nth-child(13) {
            text-align: right !important;
        }


    </style>


@endsection

@section('content')

    <div class="container-fluid">
        <br><br>
        <h2 class="mb-4">Payment Detail Report</h2>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('payment-detail.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <label>Branch</label>
                    <select name="branch_id" class="form-control">
                        <option value="0">All</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->branch_id }}" {{ request('branch_id') == $branch->branch_id ? 'selected' : '' }}>
                                {{ $branch->Name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Route</label>
                    <select name="route_id" class="form-control">
                        <option value="0">All</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->id_route }}" {{ request('route_id') == $route->id_route ? 'selected' : '' }}>
                                {{ $route->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Center</label>
                    <select name="center_id" class="form-control">
                        <option value="0">All</option>
                        @foreach($centers as $center)
                            <option value="{{ $center->idCenter }}" {{ request('center_id') == $center->idCenter ? 'selected' : '' }}>
                                {{ $center->Name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Collector</label>
                    <select name="collector_id" class="form-control">
                        <option value="0">All</option>
                        @foreach($collectors as $collector)
                            <option value="{{ $collector->id }}" {{ request('collector_id') == $collector->id ? 'selected' : '' }}>
                                {{ $collector->Full_Name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mt-3">
                    <label>Loan Product</label>
                    <select name="loan_product_id" class="form-control">
                        <option value="0">All</option>
                        @foreach($loanProducts as $loanProduct)
                            <option value="{{ $loanProduct->idLoan_Category }}" {{ request('loan_product_id') == $loanProduct->idLoan_Category ? 'selected' : '' }}>
                                {{ $loanProduct->Name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mt-3">
                    <label>Paid Type</label>
                    <select name="paid_type" class="form-control">
                        <option value="All" {{ request('paid_type') == 'All' ? 'selected' : '' }}>All</option>
                        <option value="Over Paid" {{ request('paid_type') == 'Over Paid' ? 'selected' : '' }}>Over Paid</option>
                        <option value="Under Paid" {{ request('paid_type') == 'Under Paid' ? 'selected' : '' }}>Under Paid</option>
                        <option value="Normal" {{ request('paid_type') == 'Normal' ? 'selected' : '' }}>Normal</option>
                    </select>
                </div>

                <div class="col-md-3 mt-3">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') ?? date('Y-m-d') }}">
                </div>

                <div class="col-md-3 mt-3">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') ?? date('Y-m-d') }}">
                </div>

                <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('payment-detail.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <!-- Export Buttons -->
        <div class="mt-3">
            <button id="pdfButton" class="btn btn-danger" hidden>Download PDF</button>
            <button id="excelButton" class="btn btn-success">Download Excel</button>
        </div>


        <!-- Report Table -->
        <!-- Report Table -->
        <div class="table-responsive">
            <table id="repaymentTable" class="table table-centered mt-4">
                <thead class="sticky-top bg-purple">
                <tr>
                    <th>Branch</th>
                    <th>Route</th>
                    <th>Center</th>
                    <th>Loan No</th>
                    <th>Group</th>
                    <th>Customer Name</th>
                    <th>Loan Product</th>
                    <th>Loan Amount</th>
                    <th>Installment Amount</th>
                    <th>Total Installment</th>
                    <th>Penalty Amount</th>
                    <th>Total Payable</th>
                    <th>Paid Amount</th>
                    <th>Balance Amount</th>
                    <th>Paid Type</th>
                    <th>Loan Balance</th>
                    <th>Collector</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->Branch }}</td>
                        <td>{{ $payment->Route }}</td>
                        <td>{{ $payment->Center }}</td>
                        <td>{{ $payment->LoanNo }}</td>
                        <td>{{ $payment->GroupName }}</td>
                        <td>{{ $payment->CustomerName }}</td>
                        <td>{{ $payment->LoanProduct }}</td>
                        <td>{{ number_format($payment->LoanAmount, 2) }}</td>
                        <td>{{ number_format($payment->InstallmentAmount, 2) }}</td>
                        <td>{{number_format($payment->TotalInstallmentAmount, 2)}}</td>
                        <td>{{number_format($payment->TotalPenaltyAmount, 2)}}</td>
                        <td>{{ number_format($payment->TotalInstallmentAmount+$payment->TotalPenaltyAmount, 2) }}</td>
                        <td>{{ number_format($payment->TotalPaidAmount, 2) }}</td>
                        <td>{{ number_format(($payment->TotalInstallmentAmount+$payment->TotalPenaltyAmount)-$payment->TotalPaidAmount, 2) }}</td>
                        <td>
                            @if (($payment->TotalInstallmentAmount + $payment->TotalPenaltyAmount) > $payment->TotalPaidAmount)
                                <span class="text-danger font-weight-bold">Under Paid</span>
                            @elseif (($payment->TotalInstallmentAmount + $payment->TotalPenaltyAmount) < $payment->TotalPaidAmount)
                                <span class="text-success font-weight-bold">Over Paid</span>
                            @else
                                <span class="text-primary font-weight-bold">Normal</span>
                            @endif
                        </td>
                        <td>{{ number_format($payment->Balance_Amount, 2) }}</td>
                        <td>{{ $payment->Collector }}</td>
                        <td>
                            <a href="{{ url('loanview/' . $payment->idCustomer_Loan) }}" target="_blank" class="btn btn-warning">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>



    </div>

@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        $(document).ready(function () {
            $('.select2').select2(); // Initialize Select2 elements

            $('#pdfButton').click(function () {
                const element = document.getElementById('repaymentTable');

                const opt = {
                    margin: [0.2, 0.2, 0.2, 0.2],
                    filename: `Payment_Report_${new Date().toISOString().slice(0, 10)}.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 3, useCORS: true },
                    jsPDF: { unit: 'in', format: [16, 11], orientation: 'landscape' } // Wider format
                };



                html2pdf().from(element).set(opt).save();
            });


            $('#excelButton').click(function () {
                let table = document.getElementById('repaymentTable');

                // Clone the table to modify without affecting the displayed table
                let clonedTable = table.cloneNode(true);

                // Remove the last column (Action column) from cloned table
                let rows = clonedTable.rows;
                for (let i = 0; i < rows.length; i++) {
                    rows[i].deleteCell(-1); // Remove last cell from each row
                }

                // Convert the modified table to Excel
                let wb = XLSX.utils.table_to_book(clonedTable, {sheet: "Payments"});
                XLSX.writeFile(wb, `Payment_Report_${new Date().toISOString().slice(0, 10)}.xlsx`);
            });


            // Print functionality
            $('#printButton').click(function () {
                window.print();
            });

        });
    </script>

@endsection

