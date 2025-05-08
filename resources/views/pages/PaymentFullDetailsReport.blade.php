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
        #repaymentTable td, #repaymentTable th {
            text-align: center; /* Center text for all columns */
        }

        #repaymentTable td:nth-child(8),
        #repaymentTable td:nth-child(9),
        #repaymentTable td:nth-child(10),
        #repaymentTable td:nth-child(11),
        #repaymentTable td:nth-child(12),
        #repaymentTable td:nth-child(13),
        #repaymentTable td:nth-child(14),
        #repaymentTable td:nth-child(16) {
            text-align: right !important; /* Right-align numeric values */
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
                    <label>Installment Type</label>
                    <select name="paid_type" class="form-control">
                        <option value="All" {{ request('paid_type') == 'All' ? 'selected' : '' }}>All</option>
                        <option value="Not Paid" {{ request('paid_type') == 'Not Paid' ? 'selected' : '' }}>Not Paid</option>
{{--                        <option value="Over Paid" {{ request('paid_type') == 'Over Paid' ? 'selected' : '' }}>Over Paid</option>--}}
                        <option value="Under Paid" {{ request('paid_type') == 'Under Paid' ? 'selected' : '' }}>Under Paid</option>
                        <option value="Normal" {{ request('paid_type') == 'Normal' ? 'selected' : '' }}>Normal</option>
                    </select>
                </div>

                <div class="col-md-3 mt-3">
                    <label>Installment Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') ?? date('Y-m-d') }}">
                </div>

                <div class="col-md-3 mt-3">
                    <label>Installment End Date</label>
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
            <button id="pdfButton" class="btn btn-danger">Download PDF</button>
            <button id="excelButton" class="btn btn-success">Download Excel</button>
        </div>


        <!-- Report Table -->
        <!-- Report Table -->
        <div class="table-responsive">
            <div style="max-height: 1000px; overflow-y: auto;">
                <table id="repaymentTable" class="table table-centered mt-4">
                    <thead class="sticky-top bg-purple">
                    <tr>
                        <th>Branch</th>
                        <th>Route</th>
                        <th>Center</th>
                        <th>Group</th>
                        <th>Loan No</th>
                        <th>Customer Name</th>
                        <th>Loan Product</th>
                        <th>Loan Amount</th>
                        <th>Installment Amount</th>
                        <th>Total Installment</th>
                        <th>Penalty Amount</th>
                        <th>Total Payable</th>
                        <th>Paid Ins. Amount</th>
                        <th>Paid Amount</th>
                        <th>Paid Arrears</th>
                        <th>Over Paid</th>
                        <th>Balance Amount</th>
                        <th>Installment Type</th>
                        <th>Loan Balance</th>
                        <th>Collector</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $totalLoanAmount = 0;
                        $totalInstallmentAmount = 0;
                        $totalInstallmentTotal = 0;
                        $totalPenaltyAmount = 0;
                        $totalPayableAmount = 0;
                        $totalPaidAmount = 0;
                        $totalBalanceAmount = 0;
                        $totalLoanBalance = 0;
                        $totalRealPaidAmount = 0;
                        $totalArrease = 0;
                        $totalOverPay = 0;
                    @endphp

                    @foreach($payments as $payment)
                        @php
                            $totalLoanAmount += $payment->LoanAmount;
                            $totalInstallmentAmount += $payment->InstallmentAmount;
                            $totalInstallmentTotal += $payment->TotalInstallmentAmount;
                            $totalPenaltyAmount += $payment->TotalPenaltyAmount;
                            $totalPayableAmount += $payment->TotalInstallmentAmount + $payment->TotalPenaltyAmount;
                            $totalPaidAmount += $payment->TotalPaidAmount;
                            $totalRealPaidAmount += $payment->TotalRealPaidAmount;
                            $totalBalanceAmount += max(($payment->TotalInstallmentAmount + $payment->TotalPenaltyAmount) - $payment->TotalPaidAmount, 0);
                            $totalLoanBalance += $payment->Balance_Amount;

                            $payable_amount=$payment->TotalInstallmentAmount + $payment->TotalPenaltyAmount;
                            $ins_paid=$payment->TotalPaidAmount;
                            $orginal_paid=$payment->TotalRealPaidAmount;
                            $arrears=0;
                            $over_pay=0;
                            if ($orginal_paid>$ins_paid){
                                $additional_paid=$orginal_paid-$ins_paid;
                                if($payable_amount>$ins_paid){
                                    $arrears=$additional_paid;
                                }else if($payable_amount=$ins_paid){
                                    $over_pay=$additional_paid;
                                }
                            }
                            $totalArrease+=$arrears;
                            $totalOverPay+=$over_pay;


                        @endphp
                        <tr>
                            <td>{{ $payment->Branch }}</td>
                            <td>{{ $payment->Route }}</td>
                            <td>{{ $payment->Center }}</td>
                            <td>{{ $payment->GroupName }}</td>
                            <td>{{ $payment->LoanNo }}</td>
                            <td style="text-align: left">{{ $payment->CustomerName }}</td>
                            <td>{{ $payment->LoanProduct }}</td>
                            <td>{{ number_format($payment->LoanAmount, 2) }}</td>
                            <td>{{ number_format($payment->InstallmentAmount, 2) }}</td>
                            <td>{{ number_format($payment->TotalInstallmentAmount, 2) }}</td>
                            <td>{{ number_format($payment->TotalPenaltyAmount, 2) }}</td>
                            <td>{{ number_format($payment->TotalInstallmentAmount + $payment->TotalPenaltyAmount, 2) }}</td>
                            <td>{{ number_format($payment->TotalPaidAmount, 2) }}</td>
                            <td>{{ number_format($payment->TotalRealPaidAmount, 2) }}</td>
                            <td>{{ number_format($arrears, 2) }}</td>
                            <td>{{ number_format($over_pay, 2) }}</td>
                            <td>{{ number_format(max(($payment->TotalInstallmentAmount + $payment->TotalPenaltyAmount) - $payment->TotalPaidAmount, 0), 2) }}</td>
                            <td>
                                @if ($payment->TotalPaidAmount < 1)
                                    <span class="text-danger font-weight-bold">Not Paid</span>
                                @elseif (($payment->TotalInstallmentAmount + $payment->TotalPenaltyAmount) > $payment->TotalPaidAmount)
                                    <span class="text-warning font-weight-bold">Under Paid</span>
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
                    <tfoot>
                    <tr class="font-weight-bold bg-light">
                        <td colspan="7" class="text-right">Total:</td>
                        <td class="text-right" style="text-align: right"><strong>{{ number_format($totalLoanAmount, 2) }}</strong></td>
                        <td class="text-right" style="text-align: right"><strong>{{ number_format($totalInstallmentAmount, 2) }}</strong></td>
                        <td class="text-right" style="text-align: right"><strong>{{ number_format($totalInstallmentTotal, 2) }}</strong></td>
                        <td class="text-right" style="text-align: right"><strong>{{ number_format($totalPenaltyAmount, 2) }}</strong></td>
                        <td class="text-right" style="text-align: right"><strong>{{ number_format($totalPayableAmount, 2) }}</strong></td>
                        <td class="text-right" style="text-align: right"><strong>{{ number_format($totalPaidAmount, 2) }}</strong></td>
                        <td class="text-right" style="text-align: right"><strong>{{ number_format($totalRealPaidAmount, 2) }}</strong></td>
                        <td class="text-right" style="text-align: right"><strong>{{ number_format($totalArrease, 2) }}</strong></td>
                        <td class="text-right" style="text-align: right"><strong>{{ number_format($totalOverPay, 2) }}</strong></td>
                        <td class="text-right" style="text-align: right"><strong>{{ number_format($totalBalanceAmount, 2) }}</strong></td>
                        <td></td> <!-- Empty for Paid Type -->
                        <td class="text-right" style="text-align: right"><strong>{{ number_format($totalLoanBalance, 2) }}</strong></td>
                        <td></td> <!-- Empty for Collector -->
                        <td></td> <!-- Empty for Action -->
                    </tr>
                    </tfoot>



                </table>
            </div>


        </div>

        @php
            $notPaidCount = 0;
            $overPaidCount = 0;
            $underPaidCount = 0;
            $normalCount = 0;
            $totalCount = count($payments); // Get total number of records

            foreach ($payments as $payment) {
                if ($payment->TotalPaidAmount < 1) {
                    $notPaidCount++;
                } elseif (($payment->TotalInstallmentAmount + $payment->TotalPenaltyAmount) > $payment->TotalPaidAmount) {
                    $underPaidCount++;
                } elseif (($payment->TotalInstallmentAmount + $payment->TotalPenaltyAmount) < $payment->TotalPaidAmount) {
                    $overPaidCount++;
                } else {
                    $normalCount++;
                }
            }

            // Calculate percentages
            $notPaidPercentage = ($totalCount > 0) ? ($notPaidCount / $totalCount) * 100 : 0;
            $underPaidPercentage = ($totalCount > 0) ? ($underPaidCount / $totalCount) * 100 : 0;
            $overPaidPercentage = ($totalCount > 0) ? ($overPaidCount / $totalCount) * 100 : 0;
            $normalPercentage = ($totalCount > 0) ? ($normalCount / $totalCount) * 100 : 0;
        @endphp

                <!-- Summary Table Below -->
        <div class="mt-4">
            <h4>Payment Status Summary</h4>
            <table class="table table-bordered table-striped">
                <thead class="bg-purple text-white">
                <tr>
                    <th>Not Paid</th>
                    <th>Under Paid</th>
                    <th>Over Paid</th>
                    <th>Normal</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-center">
                        <strong>{{ $notPaidCount }}</strong>
                        <br>
                        <small><strong>({{ number_format($notPaidPercentage, 2) }}%)</strong></small>
                    </td>
                    <td class="text-center">
                        <strong>{{ $underPaidCount }}</strong>
                        <br>
                        <small><strong>({{ number_format($underPaidPercentage, 2) }}%)</strong></small>
                    </td>
                    <td class="text-center">
                        <strong>{{ $overPaidCount }}</strong>
                        <br>
                        <small><strong>({{ number_format($overPaidPercentage, 2) }}%)</strong></small>
                    </td>
                    <td class="text-center">
                        <strong>{{ $normalCount }}</strong>
                        <br>
                        <small><strong>({{ number_format($normalPercentage, 2) }}%)</strong></small>
                    </td>
                    <td class="text-center">
                        <strong>{{ $totalCount }}</strong>
                    </td>
                </tr>
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
                let table = document.getElementById('repaymentTable');

                // Clone the table to modify without affecting the displayed table
                let clonedTable = table.cloneNode(true);

                // Remove the last column (Action column) from cloned table
                let rows = clonedTable.rows;
                for (let i = 0; i < rows.length; i++) {
                    rows[i].deleteCell(-1); // Remove last cell from each row
                }

                // Get selected filter values
                let branch = $("select[name='branch_id'] option:selected").text();
                let route = $("select[name='route_id'] option:selected").text();
                let center = $("select[name='center_id'] option:selected").text();
                let collector = $("select[name='collector_id'] option:selected").text();
                let loanProduct = $("select[name='loan_product_id'] option:selected").text();
                let paidType = $("select[name='paid_type'] option:selected").text();
                let startDate = $("input[name='start_date']").val();
                let endDate = $("input[name='end_date']").val();

                // Count the payment statuses
                let notPaidCount = 0, underPaidCount = 0, overPaidCount = 0, normalCount = 0;
                let totalCount = $('#repaymentTable tbody tr').length;

                $('#repaymentTable tbody tr').each(function () {
                    let statusText = $(this).find("td:eq(14)").text().trim(); // Paid Type Column

                    if (statusText === "Not Paid") {
                        notPaidCount++;
                    } else if (statusText === "Under Paid") {
                        underPaidCount++;
                    } else if (statusText === "Over Paid") {
                        overPaidCount++;
                    } else if (statusText === "Normal") {
                        normalCount++;
                    }
                });

                // Calculate percentages
                let notPaidPercentage = (totalCount > 0) ? ((notPaidCount / totalCount) * 100).toFixed(2) : 0;
                let underPaidPercentage = (totalCount > 0) ? ((underPaidCount / totalCount) * 100).toFixed(2) : 0;
                let overPaidPercentage = (totalCount > 0) ? ((overPaidCount / totalCount) * 100).toFixed(2) : 0;
                let normalPercentage = (totalCount > 0) ? ((normalCount / totalCount) * 100).toFixed(2) : 0;

                // Create the filter info to display at the top of the PDF
                let filterInfo = `
    <div style="text-align:center; margin-bottom: 10px;">
        <h2 style="color:#1A2942; font-size: 18px; font-weight:bold; margin-bottom:5px;">Payment Detail Report</h2>
    </div>
    <table style="width: 100%; border-collapse: collapse; font-size: 12px; background-color: #f8f9fa; padding: 10px; border-radius: 5px;">
        <tr>
            <td style="padding: 5px; font-weight: bold; width: 20%;">Branch:</td>
            <td style="padding: 5px; width: 30%;">${branch}</td>
            <td style="padding: 5px; font-weight: bold; width: 20%;">Route:</td>
            <td style="padding: 5px; width: 30%;">${route}</td>
        </tr>
        <tr>
            <td style="padding: 5px; font-weight: bold;">Center:</td>
            <td style="padding: 5px;">${center}</td>
            <td style="padding: 5px; font-weight: bold;">Collector:</td>
            <td style="padding: 5px;">${collector}</td>
        </tr>
        <tr>
            <td style="padding: 5px; font-weight: bold;">Loan Product:</td>
            <td style="padding: 5px;">${loanProduct}</td>
            <td style="padding: 5px; font-weight: bold;">Paid Type:</td>
            <td style="padding: 5px;">${paidType}</td>
        </tr>
        <tr>
            <td style="padding: 5px; font-weight: bold;">Start Date:</td>
            <td style="padding: 5px;">${startDate}</td>
            <td style="padding: 5px; font-weight: bold;">End Date:</td>
            <td style="padding: 5px;">${endDate}</td>
        </tr>
    </table>
    <br>`;

                // Payment Status Summary Table
                let summaryTable = `
    <h3 style="text-align:center; margin-top: 20px;">Payment Status Summary</h3>
    <table style="width: 100%; border-collapse: collapse; font-size: 12px; text-align: center;">
        <thead>
            <tr style="background-color: #1A2942; color: white;">
                <th>Not Paid</th>
                <th>Under Paid</th>
                <th>Over Paid</th>
                <th>Normal</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>${notPaidCount}</strong> <br> <small>(${notPaidPercentage}%)</small></td>
                <td><strong>${underPaidCount}</strong> <br> <small>(${underPaidPercentage}%)</small></td>
                <td><strong>${overPaidCount}</strong> <br> <small>(${overPaidPercentage}%)</small></td>
                <td><strong>${normalCount}</strong> <br> <small>(${normalPercentage}%)</small></td>
                <td><strong>${totalCount}</strong> <br> <small>(100%)</small></td>
            </tr>
        </tbody>
    </table>
    <br>`;

                // Custom styles for better readability in PDF
                let style = `
    <style>
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; }
        thead { background-color: #1A2942; color: white; }
    </style>`;

                let htmlContent = style + filterInfo + clonedTable.outerHTML + summaryTable; // Include Summary Table

                let opt = {
                    margin: [0.2, 0.2, 0.2, 0.2],
                    filename: `Payment_Report_${new Date().toISOString().slice(0, 10)}.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 3, useCORS: true },
                    jsPDF: { unit: 'in', format: [16, 11], orientation: 'landscape' } // Ensure full width
                };

                html2pdf().from(htmlContent).set(opt).save();
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

