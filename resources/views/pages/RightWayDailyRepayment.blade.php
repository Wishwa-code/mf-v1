@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }

        .page-title {
            font-size: 28px;
            font-weight: bold;
            margin: 20px 0;
            color: #333;
        }

        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse; /* Ensure borders collapse */
            background-color: #fff;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 2px solid black; /* Set border thickness and color */
            font-size: 14px;
        }

        th {
            background-color: #fffdfd;
            color: black;
        }

        tfoot td {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        button {
            padding: 8px 16px;
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #d32f2f;
        }

        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 6px;
            font-size: 14px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            table {
                font-size: 12px;
            }

            th, td {
                padding: 5px;
            }
        }

        @media (max-width: 480px) {
            table {
                font-size: 10px;
            }

            th, td {
                padding: 3px;
            }
        }
    </style>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
                font-size: 9px;
            }

            #repaymentTable {
                width: 100%;
                table-layout: fixed;
                border-collapse: collapse;
                font-size: 8px;
            }

            #repaymentTable th,
            #repaymentTable td {
                border: 1px solid #000;
                padding: 4px;
                word-wrap: break-word;
            }

            .attendance-cell {
                width: 20px;
                height: 20px;
            }

            @page {
                size: auto; /* let the browser decide: supports both portrait & landscape */
                margin: 0.5in;
            }

            .page-title, .btn, .select2, form {
                display: none !important; /* hide UI for printing */
            }
        }


        .attendance-cell {
            border: 1px solid black;
            width: 25px;
            height: 25px;
        }

    </style>


@endsection

@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-6">
                <div class="page-title-box">
                    <h4 class="page-title">REPAYMENT SHEET</h4>
                </div>
            </div>
            <span style="color: #a19595">"The Repayment Sheet is a detailed record that helps you track loan repayments for each client on a monthly basis. It includes details such as the client’s loan balance, due amounts, arrears, and collections. This sheet also provides an overview of the performance of each group and center for the month."</span>
        </div>

        <!-- Form to filter by center -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <form action="{{ route('transaction.rightway') }}" method="GET">
                                @csrf
                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <label for="center_details" class="form-label">Center</label>
                                        <select class="form-control select2" id="center_details" name="center_details">
                                            @foreach ($center as $item)
                                                <option value="{{ $item->idCenter }}"
                                                        {{ $item->idCenter == $center_details ? 'selected' : '' }}>
                                                    {{ $item->Name }}-{{ $item->Route }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-danger"><i class="bi bi-search"></i> Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-12 d-flex align-items-center gap-2">
                                <label for="pageOrientation" style="margin-right: 10px;">Print Orientation:</label>
                                <select id="pageOrientation" class="form-select w-auto">
                                    <option value="landscape" selected>Landscape</option>
                                    <option value="portrait">Portrait</option>
                                </select>

                                <button id="printButton" class="btn btn-primary"><i class="bi bi-printer"></i> Print</button>
                                <button id="downloadExcel" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Download Excel</button>
                            </div>
                        </div>


                        <!-- Repayment table -->
                        <div class="table-responsive">
                            <table id="repaymentTable">
                                <thead>
                                <tr>
                                    <th rowspan="2">Customer Code</th>
                                    <th rowspan="2">Customer Name</th>
                                    <th rowspan="2">Phone No</th>
                                    <th rowspan="2">Loan Product</th>
                                    <th rowspan="2">Loan No</th>
                                    <th rowspan="2">Loan Amount</th>
                                    <th rowspan="2">Loan Balance</th>
                                    <th rowspan="2">Loan Rental</th>
                                    <th rowspan="2">Loan Arrears</th>
                                    <th rowspan="2">Savings Balance</th>
                                    <th colspan="2">Rs</th>
                                    <th colspan="2">Rs</th>
                                    <th colspan="2">Rs</th>
                                    <th colspan="2">Rs</th>
                                    <th colspan="4">ATTENDANCE</th>
                                </tr>
                                <tr>
                                    <th>Rent.</th>
                                    <th>R.R.P</th>
                                    <th>Rent.</th>
                                    <th>R.R.P</th>
                                    <th>Rent.</th>
                                    <th>R.R.P</th>
                                    <th>Rent.</th>
                                    <th>R.R.P</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>

                                </thead>
                                <tbody>
                                @foreach ($grouped_loans as $group_name => $group)
                                    <tr>
                                        <th colspan="22">Group No: {{ $group_name }}</th>
                                    </tr>
                                    @foreach ($group as $item)
                                        @php
                                            // Format customer name abbreviation
                                            $nameParts = explode(' ', $item->customer_name . ' ' . $item->customer_lastname);
                                            if (count($nameParts) >= 2) {
                                                $shortName = strtoupper(substr($nameParts[0], 0, 1)) . '.' . strtoupper(substr($nameParts[1], 0, 1)) . '.' . end($nameParts);
                                            } else {
                                                $shortName = $item->customer_name . ' ' . $item->customer_lastname;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $item->cus_number }}</td>
                                            <td>{{ $shortName }}</td>
                                            <td>{{ $item->Contact_No }}</td>
                                            <td>{{ $item->Product_code }}</td>
                                            <td>{{ $item->Loan_No }}</td>
                                            <td class="loan-amount">{{ number_format($item->Loan_Amount, 2) }}</td>
                                            <td class="loan-balance">{{ number_format($item->Total_Balance, 2) }}</td>
                                            <td class="due-amount">{{ number_format($item->Installment_Amount, 2) }}</td>
                                            <td class="arrears">{{ number_format($item->arrease, 2) }}</td>
                                            <td class="saving-balance">{{ number_format($item->last_saving_balance, 2) }}</td>
                                            <td></td> <!-- Week 1 Rent -->
                                            <td></td> <!-- Week 1 R.R.P -->
                                            <td></td> <!-- Week 2 Rent -->
                                            <td></td> <!-- Week 2 R.R.P -->
                                            <td></td> <!-- Week 3 Rent -->
                                            <td></td> <!-- Week 3 R.R.P -->
                                            <td></td> <!-- Week 4 Rent -->
                                            <td></td> <!-- Week 4 R.R.P -->
                                            <td class="attendance-cell"></td>
                                            <td class="attendance-cell"></td>
                                            <td class="attendance-cell"></td>
                                            <td class="attendance-cell"></td>
                                        </tr>
                                    @endforeach
                                    <tr class="group-total">
                                        <td><strong>Group Total</strong></td>
                                        <td colspan="4"></td>
                                        <td class="group-loan-amount"></td>
                                        <td class="group-loan-balance"></td>
                                        <td class="group-due-amount"></td>
                                        <td class="group-arrears"></td>
                                        <td class="group-saving"></td>
                                        <td colspan="12"></td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td><strong>Center Total</strong></td>
                                    <td colspan="4"></td>
                                    <td id="total-loan-amount"></td>
                                    <td id="total-loan-balance"></td>
                                    <td id="total-due-amount"></td>
                                    <td id="total-arrears"></td>
                                    <td id="total-saving"></td>
                                    <td colspan="12"></td>
                                </tr>
                                </tfoot>

                            </table>

                        </div>

                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- end container-fluid -->

@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2(); // Initialize Select2 elements

            function calculateTotals() {
                let totalLoanAmount = 0;
                let totalLoanBalance = 0;
                let totalDueAmount = 0;
                let totalArrears = 0;
                let totalSaving = 0;

                let rows = $('#repaymentTable tbody tr');
                let currentGroupRows = [];

                rows.each(function () {
                    const row = $(this);

                    if (row.find('th').first().text().startsWith('Group No')) {
                        // Reset group collection
                        currentGroupRows = [];
                    } else if (row.hasClass('group-total')) {
                        // Process current group rows
                        let groupLoanAmount = 0;
                        let groupLoanBalance = 0;
                        let groupDueAmount = 0;
                        let groupArrears = 0;
                        let groupSaving = 0;

                        currentGroupRows.forEach(r => {
                            groupLoanAmount += parseFloat(r.find('.loan-amount').text().replace(/,/g, '') || 0);
                            groupLoanBalance += parseFloat(r.find('.loan-balance').text().replace(/,/g, '') || 0);
                            groupDueAmount += parseFloat(r.find('.due-amount').text().replace(/,/g, '') || 0);
                            groupArrears += parseFloat(r.find('.arrears').text().replace(/,/g, '') || 0);
                            groupSaving += parseFloat(r.find('.saving-balance').text().replace(/,/g, '') || 0);
                        });

                        // Update this group total row
                        row.find('.group-loan-amount').text(groupLoanAmount.toFixed(2));
                        row.find('.group-loan-balance').text(groupLoanBalance.toFixed(2));
                        row.find('.group-due-amount').text(groupDueAmount.toFixed(2));
                        row.find('.group-arrears').text(groupArrears.toFixed(2));
                        row.find('.group-saving').text(groupSaving.toFixed(2));

                        // Add to center total
                        totalLoanAmount += groupLoanAmount;
                        totalLoanBalance += groupLoanBalance;
                        totalDueAmount += groupDueAmount;
                        totalArrears += groupArrears;
                        totalSaving += groupSaving;

                    } else {
                        // Push data rows to group
                        if (row.find('td').length && !row.hasClass('group-total')) {
                            currentGroupRows.push(row);
                        }
                    }
                });

                // Set final center totals
                $('#total-loan-amount').text(totalLoanAmount.toFixed(2));
                $('#total-loan-balance').text(totalLoanBalance.toFixed(2));
                $('#total-due-amount').text(totalDueAmount.toFixed(2));
                $('#total-arrears').text(totalArrears.toFixed(2));
                $('#total-saving').text(totalSaving.toFixed(2));
            }





            // Calculate totals when the document is ready
            calculateTotals();



            // Download Excel functionality
            $('#downloadExcel').click(function() {
                // Convert HTML table to a workbook object
                var wb = XLSX.utils.table_to_book(document.getElementById('repaymentTable'), { sheet: "Repayment Data" });

                // Generate and download the Excel file
                XLSX.writeFile(wb, `Repayment_Report_${new Date().toLocaleString('default', { month: 'long' })}.xlsx`);
            });

            $('#pdfButton').click(function() {
                const element = document.getElementById('repaymentTable');
                const opt = {
                    margin: [0.5, 0.5, 0.5, 0.5], // Margins: top, right, bottom, left
                    filename: `Repayment_Report_${new Date().toLocaleString('default', { month: 'long' })}.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true },
                    jsPDF: { unit: 'in', format: [11, 8.5], orientation: 'landscape' } // Landscape orientation with A4 dimensions
                };
                html2pdf().from(element).set(opt).save();
            });


        });


        $('#printButton').click(function () {
            const currentMonth = new Date().toLocaleString('default', { month: 'long' });
            const center_details = $('#center_details').find('option:selected').text();
            const orientation = $('#pageOrientation').val(); // Get selected orientation

            const printWindow = window.open('', '', 'height=800,width=600');
            const printContent = document.getElementById('repaymentTable').outerHTML;
            printWindow.document.write('<style>body { font-family: Arial, sans-serif; margin: 0; padding: 0; font-size: 9px; }');
            printWindow.document.write('#repaymentTable { width: 100%; border-collapse: collapse; table-layout: fixed; }');
            printWindow.document.write('#repaymentTable th, #repaymentTable td { border: 1px solid #000; padding: 4px; word-wrap: break-word; }');
            printWindow.document.write('body { zoom: 80%; }'); // 👈 this line

            printWindow.document.write('<html><head><title>Repayment Sheet</title>');
            printWindow.document.write('<style>');
            printWindow.document.write('body { font-family: Arial, sans-serif; margin: 0; padding: 0; font-size: 10px; }');
            printWindow.document.write('#repaymentTable { width: 100%; border-collapse: collapse; }');
            printWindow.document.write('#repaymentTable th, #repaymentTable td { padding: 5px; border: 1px solid #000; text-align: center; }');
            printWindow.document.write('@media print {@page { size: ' + orientation + '; margin: 0.5in; }}');
            printWindow.document.write('</style></head><body>');
            printWindow.document.write('<h1 style="text-align: center;">Repayment Sheet for ' + currentMonth + ' (' + center_details + ')</h1>');
            printWindow.document.write(printContent);
            printWindow.document.write('</body></html>');

            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        });



    </script>
@endsection

