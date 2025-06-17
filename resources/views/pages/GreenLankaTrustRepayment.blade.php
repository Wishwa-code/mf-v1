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
                font-size: 10px;
            }

            #repaymentTable {
                width: 100%;
                table-layout: fixed;
                border-collapse: collapse;
                font-size: 9px;

            }
            td, th {
                word-break: break-word;
            }

            #repaymentTable th,
            #repaymentTable td {
                border: 1px solid #000;
                padding: 5px;
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

        #repaymentTable {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        #repaymentTable th,
        #repaymentTable td {
            border: 1px solid black;
            text-align: center;
            padding: 6px;
            vertical-align: middle;
        }

        #repaymentTable thead th {
            background-color: #f1f1f1;
            font-weight: bold;
        }

        #repaymentTable tbody tr:nth-child(even) {
            background-color: #f9f9f9;
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
                            <form action="{{ route('transaction.GreenLankaTrustRepayment') }}" method="GET">
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
                                    <label for="group_filter" class="form-label">Group</label>
                                    <select class="form-control select2" id="group_filter" name="group_filter">
                                        <option value="">All</option>
                                        @foreach($grouped_loans->keys() as $groupKey)
                                            <option value="{{ $groupKey }}" {{ request('group_filter') == $groupKey ? 'selected' : '' }}>
                                                {{ $groupKey }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
<br>

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
                                    <th rowspan="2">Loan No</th>
                                    <th rowspan="2">Member Name</th>
                                    <th rowspan="2">Loan Amount</th>
                                    <th rowspan="2">Due Installment</th>
                                    <th rowspan="2">New Loan Amount</th>
                                    @for ($i = 1; $i <= 6; $i++)
                                        <th colspan="2">Date</th>
                                    @endfor
                                </tr>
                                <tr>
                                    @for ($i = 1; $i <= 6; $i++)
                                        <th>Paid Amount</th>
                                        <th>Correct</th>
                                    @endfor
                                </tr>
                                </thead>
                                <tbody>
                                <!-- Sample Row -->
                                @foreach($grouped_loans as $group_name => $group)
                                    <tr><td colspan="17" style="text-align:left;"><strong>Group No: {{ $group_name }}</strong></td></tr>
                                    @php
                                        $groupLoanAmount = $group->sum('Loan_Amount');
                                        $groupDueAmount = $group->sum('Installment_Amount');
                                        $groupBalance = $group->sum('Balance_Amount');
                                    @endphp

                                    @foreach($group as $item)
                                        <tr>
                                            <td>{{ $item->Loan_No }}</td>
                                            <td>{{ $item->name_with_initials }}</td>
                                            <td>{{ number_format($item->Loan_Amount, 2) }}</td>
                                            <td>{{ number_format($item->Installment_Amount, 2) }}</td>
                                            <td>{{ number_format($item->Balance_Amount, 2) }}</td>
                                            @for ($i = 1; $i <= 6; $i++)
                                                <td></td><td></td>
                                            @endfor
                                        </tr>
                                    @endforeach

                                    <tr style="font-weight: bold;">
                                        <td colspan="2">Group Total</td>
                                        <td>{{ number_format($groupLoanAmount, 2) }}</td>
                                        <td>{{ number_format($groupDueAmount, 2) }}</td>
                                        <td>{{ number_format($groupBalance, 2) }}</td>
                                        <td colspan="12"></td>
                                    </tr>
                                @endforeach


                                <!-- Summary Rows -->
                                <tr><td colspan="5"><strong>Cumulative Collection</strong></td><td colspan="12"></td></tr>
                                <tr><td colspan="5"><strong>Cumulative Due</strong></td><td colspan="12"></td></tr>
                                <tr><td colspan="5"><strong>Total</strong></td><td colspan="12"></td></tr>
                                <tr><td colspan="5">No of Under Payment</td><td colspan="12"></td></tr>
                                <tr><td colspan="5">Amount</td><td colspan="12"></td></tr>
                                <tr><td colspan="5">No of Not Paid</td><td colspan="12"></td></tr>
                                <tr><td colspan="5">Amount</td><td colspan="12"></td></tr>
                                <tr><td colspan="5">No of Settlement</td><td colspan="12"></td></tr>
                                <tr><td colspan="17" style="padding-top: 40px;"><strong>Full Signature Center Manager</strong></td></tr>
                                </tbody>
                            </table>


                        </div>

                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- end container-fluid -->
    @php
        $printedBy = session('Full_Name');
        $printedAt = now()->format('Y-m-d h:i A');
    @endphp

@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();

            function calculateTotals() {
                let totalLoanAmount = 0;
                let totalLoanBalance = 0;
                let totalDueAmount = 0;
                let totalArrears = 0;
                let totalSaving = 0;

                const rows = $('#repaymentTable tbody tr');
                let currentGroupRows = [];

                rows.each(function () {
                    const row = $(this);

                    if (row.find('th').first().text().startsWith('Group No')) {
                        currentGroupRows = [];
                    } else if (row.hasClass('group-total')) {
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

                        row.find('.group-loan-amount').text(groupLoanAmount.toFixed(2));
                        row.find('.group-loan-balance').text(groupLoanBalance.toFixed(2));
                        row.find('.group-due-amount').text(groupDueAmount.toFixed(2));
                        row.find('.group-arrears').text(groupArrears.toFixed(2));
                        row.find('.group-saving').text(groupSaving.toFixed(2));

                        totalLoanAmount += groupLoanAmount;
                        totalLoanBalance += groupLoanBalance;
                        totalDueAmount += groupDueAmount;
                        totalArrears += groupArrears;
                        totalSaving += groupSaving;

                    } else {
                        if (row.find('td').length && !row.hasClass('group-total')) {
                            currentGroupRows.push(row);
                        }
                    }
                });

                $('#total-loan-amount').text(totalLoanAmount.toFixed(2));
                $('#total-loan-balance').text(totalLoanBalance.toFixed(2));
                $('#total-due-amount').text(totalDueAmount.toFixed(2));
                $('#total-arrears').text(totalArrears.toFixed(2));
                $('#total-saving').text(totalSaving.toFixed(2));
            }

            calculateTotals();

            $('#downloadExcel').click(function () {
                const table = document.getElementById('repaymentTable');
                const ws = XLSX.utils.table_to_sheet(table, { raw: true });

                // Auto width for each column
                const columnWidths = [];
                const range = XLSX.utils.decode_range(ws['!ref']);
                for (let C = range.s.c; C <= range.e.c; ++C) {
                    let maxWidth = 10;
                    for (let R = range.s.r; R <= range.e.r; ++R) {
                        const cell_address = { c: C, r: R };
                        const cell_ref = XLSX.utils.encode_cell(cell_address);
                        const cell = ws[cell_ref];
                        if (cell && cell.v) {
                            const cellValue = cell.v.toString();
                            if (cellValue.length > maxWidth) maxWidth = cellValue.length;
                        }
                    }
                    columnWidths.push({ wch: maxWidth + 2 });
                }
                ws['!cols'] = columnWidths;

                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Repayment Data");
                XLSX.writeFile(wb, `Repayment_Report_${new Date().toLocaleString('default', { month: 'long' })}.xlsx`);
            });

            $('#pdfButton').click(function () {
                const element = document.getElementById('repaymentTable');
                const opt = {
                    margin: [0.5, 0.5, 0.5, 0.5],
                    filename: `Repayment_Report_${new Date().toLocaleString('default', {month: 'long'})}.pdf`,
                    image: {type: 'jpeg', quality: 0.98},
                    html2canvas: {scale: 2, useCORS: true},
                    jsPDF: {unit: 'in', format: [11, 8.5], orientation: 'landscape'}
                };
                html2pdf().from(element).set(opt).save();
            });

            $('#printButton').click(function () {
                const currentMonth = new Date().toLocaleString('default', {month: 'long'});
                const center_details = $('#center_details').find('option:selected').text();
                const orientation = $('#pageOrientation').val();

                const printWindow = window.open('', '', 'height=800,width=1200');
                const printContent = document.getElementById('repaymentTable').outerHTML;

                printWindow.document.write(`<div style="margin-top:10px;text-align:right;font-size:10px;">
Printed By: {{ $printedBy }}<br>
Printed On: {{ $printedAt }}
                </div>`);


                printWindow.document.write('<html><head><title>Repayment Sheet</title>');
                printWindow.document.write('<style>');
                printWindow.document.write('body { font-family: Arial, sans-serif; font-size: 9px; zoom: 80%; margin: 0.5in; }');
                printWindow.document.write('#repaymentTable { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 8px; }');
                printWindow.document.write('#repaymentTable th, #repaymentTable td { border: 1px solid black; padding: 4px; text-align: center; word-break: break-word; }');
                printWindow.document.write('@media print { @page { size: ' + orientation + '; margin: 0.5in; } }');
                printWindow.document.write('</style></head><body>');
                printWindow.document.write('<h2 style="text-align:center;">Repayment Sheet for ' + currentMonth + ' (' + center_details + ')</h2>');
                printWindow.document.write(printContent);

// Append signature section
                printWindow.document.write(`
   <br><br>
<table style="width: 100%; font-size: 12px; border: none; line-height: 2;">
    <tr>
        <td style="width: 35%;"><strong>EXECUTIVE SIGNATURE</strong></td>
        <td style="width: 65%;"><div style="border-bottom: 2px solid black; width: 100%;"></div></td>
    </tr>
    <tr>
        <td><strong>SLIP NUMBER</strong></td>
        <td><div style="border-bottom: 2px solid black; width: 100%;"></div></td>
    </tr>
    <tr>
        <td><strong>CASHIER SIGNATURE</strong></td>
        <td><div style="border-bottom: 2px solid black; width: 100%;"></div></td>
    </tr>
    <tr>
        <td><strong>MANAGER SIGNATURE</strong></td>
        <td><div style="border-bottom: 2px solid black; width: 100%;"></div></td>
    </tr>
</table>


`);


                printWindow.document.write('</body></html>');

                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
            });
        });
    </script>

@endsection

