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
        @media print {
            body {
                font-size: 9px !important;
            }

            table, th, td {
                font-size: 8px !important;
                padding: 1px !important;
            }

            th, td {
                word-break: break-word;
            }
        }

    </style>


@endsection

@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-6">
                <div class="page-title-box">
                    <h4 class="page-title">Monthly Repayment Sheet</h4>
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
                            <form action="{{ route('transaction.daily_repayment_sheet_filter') }}" method="POST">
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
                                        <label for="center_details" class="form-label">Group</label>
                                        <select class="form-control select2" id="group_details" name="group_details">
                                            <option value="0">All</option>
                                            @foreach ($group as $item)
                                                <option value="{{ $item->idCustomer_Group }}"
                                                        {{ $item->idCustomer_Group == $group_details ? 'selected' : '' }}>
                                                    {{ $item->Group_No }}-{{ $item->Name }}
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
                            <div class="col-12">
                                <button id="portraitPrint" class="btn btn-primary"><i class="bi bi-printer"></i> Portrait Print</button>
                                <button id="landscapePrint" class="btn btn-secondary"><i class="bi bi-printer"></i> Landscape Print</button>
                                <button id="downloadExcel" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Download Excel</button>
                            </div>
                        </div>

                        <!-- Repayment table -->
                        <div class="table-responsive">
                            <table id="repaymentTable">
                                <thead>
                                <tr>
                                    <th rowspan="2">Client</th>
                                    <th rowspan="2">Loan No</th>
                                    <th rowspan="2">Phone No</th>
                                    <th rowspan="2">Loan Amount</th>
                                    <th rowspan="2">Due Amount</th>
                                    <th rowspan="2">Total Balance</th>
                                    <th rowspan="2">Arrears</th>
                                    <th colspan="2"></th>
                                    <th colspan="2"></th>
                                    <th colspan="2"></th>
                                    <th colspan="2"></th>
                                </tr>
                                <tr>
                                    <th class="portrait-hide">Collection</th>
                                    <th class="portrait-hide">Other</th>
                                    <th class="portrait-hide">Collection</th>
                                    <th class="portrait-hide">Other</th>
                                    <th class="portrait-hide">Collection</th>
                                    <th class="portrait-hide">Other</th>
                                    <th class="portrait-hide">Collection</th>
                                    <th class="portrait-hide">Other</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($grouped_loans as $group_name => $group)
                                    <tr>
                                        <th colspan="16">Group No :- {{ $group_name }}</th>
                                    </tr>
                                    @foreach ($group as $item)
                                        @php
                                            // Split the full name into parts
                                            $nameParts = explode(' ', $item->customer_name . ' ' . $item->customer_lastname);

                                            // Handle name abbreviation logic
                                            if (count($nameParts) >= 2) {
                                                $shortName = strtoupper(substr($nameParts[0], 0, 1)) . '.' . strtoupper(substr($nameParts[1], 0, 1)) . '.' . end($nameParts);
                                            } else {
                                                // Fallback if there are fewer than two parts in the name
                                                $shortName = $item->customer_name . ' ' . $item->customer_lastname;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $shortName }}</td>
                                            <td>{{ $item->Loan_No }}</td>
                                            <td>{{ $item->Contact_No }}</td>
                                            <td class="loan-amount">{{ number_format($item->Loan_Amount, 2) }}</td>
                                            <td class="due-amount">{{ number_format($item->Installment_Amount, 2) }}</td>
                                            <td class="total-balance">{{ number_format($item->Total_Balance, 2) }}</td>
                                            <td class="arrears">{{ number_format($item->arrease, 2) }}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                    <tr class="group-total">
                                        <td><strong>Group Total</strong></td>
                                        <td></td>
                                        <td></td>
                                        <td class="group-loan-amount"></td>
                                        <td class="group-due-amount"></td>
                                        <td class="group-total-balance"></td>
                                        <td class="group-arrears"></td>
                                        <td colspan="11"></td>
                                    </tr>
                                    <!-- Free empty rows after group -->
                                    @php
                                        $emptyRowCount = $group_rules[$group_name] ?? 5;
                                    @endphp
                                    @for ($i = 0; $i < $emptyRowCount; $i++)
                                        <tr class="group-empty-rule">
                                            <td colspan="16">&nbsp;</td>
                                        </tr>
                                    @endfor
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td><strong>Center Total</strong></td>
                                    <td></td>
                                    <td></td>
                                    <td id="total-loan-amount"></td>
                                    <td id="total-due-amount"></td>
                                    <td id="total-balance"></td>
                                    <td id="total-arrears"></td>
                                    <td colspan="11"></td>
                                </tr>
                                <tr><td><strong>Present</strong></td><td colspan="15"></td></tr>
                                <tr><td><strong>Late</strong></td><td colspan="15"></td></tr>
                                <tr><td><strong>Informed</strong></td><td colspan="15"></td></tr>
                                <tr><td><strong>Absent</strong></td><td colspan="15"></td></tr>
                                <tr><td><strong>%</strong></td><td colspan="15"></td></tr>
                                <tr><td><strong>Executive</strong></td><td colspan="15"></td></tr>
                                <tr><td><strong>Cashier</strong></td><td colspan="15"></td></tr>
                                <tr><td><strong>Manager</strong></td><td colspan="15"></td></tr>
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
                let totalDueAmount = 0;
                let totalBalance = 0;
                let totalArrears = 0;

                // Loop through each group
                $('#repaymentTable tbody').find('tr').each(function() {
                    // Check if this row is a group total row
                    if ($(this).hasClass('group-total')) {
                        let groupLoanAmount = 0;
                        let groupDueAmount = 0;
                        let groupBalance = 0;
                        let groupArrears = 0;

                        // Calculate totals for each group
                        $(this).prevAll('tr').each(function() {
                            let loanAmount = parseFloat($(this).find('.loan-amount').text().replace(/,/g, '')) || 0;
                            let dueAmount = parseFloat($(this).find('.due-amount').text().replace(/,/g, '')) || 0;
                            let balance = parseFloat($(this).find('.total-balance').text().replace(/,/g, '')) || 0;
                            let arrears = parseFloat($(this).find('.arrears').text().replace(/,/g, '')) || 0;

                            if ($(this).find('td').first().text().startsWith('Group No :-')) {
                                // This is the start of a new group
                                return false;
                            }

                            groupLoanAmount += loanAmount;
                            groupDueAmount += dueAmount;
                            groupBalance += balance;
                            groupArrears += arrears;
                        });

                        // Update the group total row
                        $(this).find('.group-loan-amount').text(groupLoanAmount.toFixed(2));
                        $(this).find('.group-due-amount').text(groupDueAmount.toFixed(2));
                        $(this).find('.group-total-balance').text(groupBalance.toFixed(2));
                        $(this).find('.group-arrears').text(groupArrears.toFixed(2));

                        // Update center totals
                        totalLoanAmount += groupLoanAmount;
                        totalDueAmount += groupDueAmount;
                        totalBalance += groupBalance;
                        totalArrears += groupArrears;
                    }
                });

                // Update the footer with center totals
                $('#total-loan-amount').text(totalLoanAmount.toFixed(2));
                $('#total-due-amount').text(totalDueAmount.toFixed(2));
                $('#total-balance').text(totalBalance.toFixed(2));
                $('#total-arrears').text(totalArrears.toFixed(2));
            }

            // Calculate totals when the document is ready
            calculateTotals();

            // Print button functionality
            $('#printButton').click(function() {
                window.print();
            });

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


        $('#printButton').click(function() {
            // Get the current month name
            const currentMonth = new Date().toLocaleString('default', { month: 'long' });
            let center_details = $('#center_details').find('option:selected').text();
            // Create a new window for printing
            let printWindow = window.open('', '', 'height=800,width=600');
            let printContent = document.getElementById('repaymentTable').outerHTML;

            printWindow.document.write('<html><head><title>Repayment Sheet</title>');
            printWindow.document.write('<style>');
            printWindow.document.write('body { font-family: Arial, sans-serif; margin: 0; padding: 0; }');
            printWindow.document.write('#repaymentTable { width: 100%; border-collapse: collapse; }');
            printWindow.document.write('#repaymentTable th, #repaymentTable td { padding: 5px; border: 2px solid #000; text-align: center; }'); // Thicker, bold borders
            printWindow.document.write('@page { size: landscape; margin: 0.5in; }');
            printWindow.document.write('</style></head><body>');
            printWindow.document.write('<h1>Repayment Sheet for ' + currentMonth + '('+center_details+')</h1>');
            printWindow.document.write(printContent);
            printWindow.document.write('</body></html>');

            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        });

        function customPrint(orientation = 'portrait') {
            const currentMonth = new Date().toLocaleString('default', { month: 'long' });
            const centerDetails = $('#center_details').find('option:selected').text();
            const table = document.getElementById('repaymentTable').cloneNode(true);

            // Hide extra columns in portrait mode
            if (orientation === 'portrait') {
                table.querySelectorAll('.portrait-hide').forEach(col => col.style.display = 'none');
            }

            const printWindow = window.open('', '', 'height=1000,width=1200');
            printWindow.document.write('<html><head><title>Repayment Sheet</title>');

            printWindow.document.write('<style>');
            printWindow.document.write('body { font-family: Arial, sans-serif; margin: 10px; font-size: 9px; }');
            printWindow.document.write('table { width: 100%; border-collapse: collapse; font-size: 8px; table-layout: auto; }');
            printWindow.document.write('th, td { border: 1px solid #000; padding: 4px; text-align: center; word-wrap: break-word; }');
            printWindow.document.write('@media print { @page { size: ' + orientation + '; margin: 0.5in; } }');
            printWindow.document.write('</style>');

            printWindow.document.write('</head><body>');
            printWindow.document.write('<h2>Repayment Sheet for ' + currentMonth + ' (' + centerDetails + ')</h2>');
            printWindow.document.write(table.outerHTML);
            printWindow.document.write('</body></html>');

            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }


        $('#portraitPrint').click(() => customPrint('portrait'));
        $('#landscapePrint').click(() => customPrint('landscape'));




    </script>
@endsection

