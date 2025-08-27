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

    /* Column widths for single-column 7-day structure */
    #repaymentTable th, #repaymentTable td { 
        font-size: 12px;
        padding: 8px 4px;
        border: 2px solid #333;
        vertical-align: middle;
        position: relative;
    }
    
    /* Responsive font sizing for long content */
    #repaymentTable td:nth-child(1) {
        font-size: clamp(8px, 1.2vw, 12px) !important;
        line-height: 1.2;
        word-wrap: break-word;
        hyphens: auto;
    }
    
    #repaymentTable td:nth-child(2) {
        font-size: clamp(9px, 1.3vw, 12px) !important;
        line-height: 1.2;
        word-wrap: break-word;
    }
    
    /* Main column widths with overflow handling */
    #repaymentTable th:nth-child(1), #repaymentTable td:nth-child(1) { 
        width: 12%; 
        max-width: 12%;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-all;
        font-size: 10px;
    }  /* Loan Number */
    
    #repaymentTable th:nth-child(2), #repaymentTable td:nth-child(2) { 
        width: 18%; 
        max-width: 18%;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-word;
    }  /* Customer Name */
    
    #repaymentTable th:nth-child(3), #repaymentTable td:nth-child(3) { 
        width: 10%; 
        max-width: 10%;
        overflow: hidden;
        text-overflow: ellipsis;
    }  /* Amount */
    
    #repaymentTable th:nth-child(4), #repaymentTable td:nth-child(4) { 
        width: 10%; 
        max-width: 10%;
        overflow: hidden;
        text-overflow: ellipsis;
    }  /* Due */
    
    #repaymentTable th:nth-child(5), #repaymentTable td:nth-child(5) { 
        width: 10%; 
        max-width: 10%;
        overflow: hidden;
        text-overflow: ellipsis;
    }  /* Balance */
    
    /* Force consistent date column widths */
    #repaymentTable {
        table-layout: fixed !important;
        width: 100% !important;
    }
    
    /* Date columns - exactly 5.71% each for perfect consistency (40% total / 7 columns) */
    .paid-amount,
    .date-col,
    #repaymentTable colgroup .date-col { 
        width: 5.71% !important; 
        min-width: 5.71% !important; 
        max-width: 5.71% !important;
        font-size: 10px;
        text-align: center;
        overflow: hidden;
        word-break: break-all;
    }
    
    /* Header styling */
    #repaymentTable thead th {
        font-size: 14px !important;
        font-weight: bold;
        padding: 12px 4px;
        background-color: #f8f9fa;
        border: 2px solid #333;
        text-align: center;
    }

    /* Main "Paid Date" header styling */
    #repaymentTable thead tr:first-child th:last-child {
        font-size: 16px !important;
        font-weight: bold;
        text-align: center;
        background-color: #e9ecef !important;
        border: 2px solid #333 !important;
    }

    /* Empty sub-date headers for manual writing - CONSISTENT SIZING */
    .empty-date-header {
        font-size: 14px !important;
        font-weight: normal !important;
        text-align: center;
        vertical-align: middle;
        background-color: #fff !important;
        border: 2px solid #333 !important;
        padding: 15px 2px !important;
        height: 40px !important;
        width: 5.71% !important;
        min-width: 5.71% !important;
        max-width: 5.71% !important;
        writing-mode: horizontal-tb;
    }

    /* logic for contact no always no wrap */
    .contact-no { max-width: none !important; white-space: nowrap; overflow: visible; text-overflow: clip; }

        /* Responsive adjustments for single-column 7-day table */
        @media (max-width: 1200px) {
            #repaymentTable th, #repaymentTable td {
                font-size: 11px;
                padding: 6px;
            }
            #repaymentTable td:nth-child(1) {
                font-size: 9px !important;
            }
            #repaymentTable td:nth-child(2) {
                font-size: 10px !important;
            }
            .empty-date-header {
                font-size: 12px !important;
                padding: 12px 3px !important;
                height: 35px !important;
            }
        }

        @media (max-width: 768px) {
            #repaymentTable th, #repaymentTable td {
                font-size: 9px;
                padding: 4px;
            }
            #repaymentTable td:nth-child(1) {
                font-size: 7px !important;
            }
            #repaymentTable td:nth-child(2) {
                font-size: 8px !important;
            }
            .empty-date-header {
                font-size: 10px !important;
                padding: 8px 2px !important;
                height: 30px !important;
            }
            .table-responsive {
                overflow-x: auto;
            }
        }

        @media (max-width: 480px) {
            #repaymentTable th, #repaymentTable td {
                font-size: 8px;
                padding: 2px;
            }
            #repaymentTable td:nth-child(1) {
                font-size: 6px !important;
            }
            #repaymentTable td:nth-child(2) {
                font-size: 7px !important;
            }
            .empty-date-header {
                font-size: 8px !important;
                padding: 6px 1px !important;
                height: 25px !important;
            }
        }
    </style>
    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 0.5in;
                counter-increment: page;
            }



            .page-break {
                page-break-after: always;
            }

            .group-row {
                height: 30px !important;
                overflow: hidden;
            }

            .fixed-name {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 150px;
            }

            /* Override css for contact no print consistnt */
            .contact-no { max-width: none !important; overflow: visible; text-overflow: clip; }

            /* Adjusted Paid/Correct columns to a tighter width for better page usage */
            .paid-amount { width: 60px; min-width: 60px; }
            .correct-column { width: 50px; min-width: 50px; }

            #repaymentTable {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
            }

            #repaymentTable th,
            #repaymentTable td {
                border: 1px solid black;
                padding: 4px;
                text-align: center;
                word-break: break-word;
            }

            .btn, form, .select2, .page-title, .no-print {
                display: none !important;
            }
        }
        @media print {
            .print-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                font-size: 11px;
                color: black;
                display: flex;
                justify-content: space-between;
                padding: 5px 30px;
                background-color: white;
                border-top: 1px solid #000;
            }
        }
        @media print {
            body {
                font-size: 12px !important;
            }

            #repaymentTable {
                font-size: 11px !important;
            }

            #repaymentTable th,
            #repaymentTable td {
                padding: 6px !important;
            }
        }
        @media print {
            #repaymentTable th, #repaymentTable td {
                white-space: nowrap !important;
                overflow: hidden;
                text-overflow: clip;
                font-size: 10px !important;
                padding: 3px !important;
                border: 1px solid #333 !important;
            }

            #repaymentTable th:nth-child(1), #repaymentTable td:nth-child(1) { width: 12%; max-width: 12%; font-size: 8px !important; word-break: break-all; }  /* Loan Number */
            #repaymentTable th:nth-child(2), #repaymentTable td:nth-child(2) { width: 18%; max-width: 18%; font-size: 9px !important; word-break: break-word; }  /* Customer Name */
            #repaymentTable th:nth-child(3), #repaymentTable td:nth-child(3) { width: 10%; max-width: 10%; }  /* Amount */
            #repaymentTable th:nth-child(4), #repaymentTable td:nth-child(4) { width: 10%; max-width: 10%; }  /* Due */
            #repaymentTable th:nth-child(5), #repaymentTable td:nth-child(5) { width: 10%; max-width: 10%; }  /* Balance */
            
            /* Date columns - 5.71% each for 7 columns - CONSISTENT */
            .paid-amount,
            .empty-date-header { width: 5.71% !important; min-width: 5.71% !important; max-width: 5.71% !important; }
            
            /* Print header styles */
            #repaymentTable thead th {
                font-size: 10px !important;
                font-weight: bold;
                text-align: center;
            }
            
            /* Main "Paid Date" header for print */
            #repaymentTable thead tr:first-child th:last-child {
                font-size: 12px !important;
                background-color: #e9ecef !important;
            }
            
            /* Empty sub-headers for print */
            .empty-date-header {
                font-size: 12px !important;
                background-color: #fff !important;
                padding: 10px 2px !important;
                height: 30px !important;
                font-weight: normal !important;
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
                    <h4 class="page-title">DAILY REPORT TEMPLATE</h4>
                </div>
            </div>
            <span style="color: #a19595">"The Daily Report Template"</span>
        </div>

        <!-- Form to filter by center -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <form action="{{ route('transaction.dailyreport') }}" method="GET">
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
                                <colgroup>
                                    <col style="width:12%; max-width:12%;">
                                    <col style="width:18%; max-width:18%;">
                                    <col style="width:10%; max-width:10%;">
                                    <col style="width:10%; max-width:10%;">
                                    <col style="width:10%; max-width:10%;">
                                    @for ($i = 1; $i <= 7; $i++)
                                        <col class="date-col" style="width:5.71%; max-width:5.71%;">
                                    @endfor
                                </colgroup>
                                <thead>
                                <tr>
                                    <th rowspan="2">Loan Number</th>
                                    <th rowspan="2">Customer Name</th>
                                    <th rowspan="2">Amount</th>
                                    <th rowspan="2">Due</th>
                                    <th rowspan="2">Balance</th>
                                    <th colspan="7">Paid Date</th>
                                </tr>
                                <tr>
                                    @for ($i = 1; $i <= 7; $i++)
                                        <th class="empty-date-header">_______</th>
                                    @endfor
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($grouped_loans->chunk(2) as $groupPair)
                                    <tbody class="page-break">
                                    @foreach($groupPair as $group_name => $group)
                                        <tr><td colspan="12"><strong>Group No: {{ $group_name }}</strong></td></tr>
                                        @foreach($group as $item)
                                            <tr class="group-row">
                                                <td>{{ $item->Loan_No }}</td>
                                                <td class="fixed-name">{{ $item->name_with_initials }}</td>
                                                <td>{{ number_format($item->Loan_Amount, 2) }}</td>
                                                <td>{{ number_format($item->Installment_Amount, 2) }}</td>
                                                <td>{{ number_format($item->Balance_Amount, 2) }}</td>
                                                @for ($i = 1; $i <= 7; $i++)
                                                    <td class="paid-amount"></td>
                                                @endfor
                                            </tr>
                                        @endforeach
                                        <tr class="group-row" style="font-weight: bold;">
                                            <td colspan="2">Group Total</td>
                                            <td>{{ number_format($group->sum('Loan_Amount'), 2) }}</td>
                                            <td>{{ number_format($group->sum('Installment_Amount'), 2) }}</td>
                                            <td>{{ number_format($group->sum('Balance_Amount'), 2) }}</td>
                                            <td colspan="7"></td>
                                        </tr>
                                        {{-- Empty Rows for manual entries --}}
                                        @for ($j = 0; $j < 3; $j++)
                                            <tr class="group-row">
                                                @for ($k = 0; $k < 12; $k++)
                                                    <td>&nbsp;</td>
                                                @endfor
                                            </tr>
                                        @endfor
                                    @endforeach
                                    </tbody>
                                    @endforeach
                                    </tbody>
                            </table>



                        </div>


                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- end container-fluid -->
    <div class="print-footer">
        <div class="left">
            Company: Asipiya Holdings | Center No: {{ $center_no }} | Center Name: {{ $center_name }}
        </div>
        <div class="right">
            Printed by: {{ $printedBy }} on {{ $printedAt }} | Page <span class="page-number"></span>
        </div>
    </div>

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
                XLSX.utils.book_append_sheet(wb, ws, "Daily Report Data");
                XLSX.writeFile(wb, `Daily_Report_${new Date().toLocaleString('default', { month: 'long' })}.xlsx`);
            });

            $('#pdfButton').click(function () {
                const element = document.getElementById('repaymentTable');
                const opt = {
                    margin: [0.5, 0.5, 0.5, 0.5],
                    filename: `Daily_Report_${new Date().toLocaleString('default', {month: 'long'})}.pdf`,
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

                printWindow.document.write('<html><head><title>Daily Report Template</title>');
                printWindow.document.write('<style>');
                printWindow.document.write('body { font-family: Arial, sans-serif; font-size: 10px; margin: 0.3in; }');
                printWindow.document.write('#repaymentTable { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 9px; }');
                printWindow.document.write('#repaymentTable th, #repaymentTable td { border: 1px solid black; padding: 3px; text-align: center; white-space: nowrap !important; }');
                printWindow.document.write('#repaymentTable th:nth-child(1), #repaymentTable td:nth-child(1) { width: 12%; max-width: 12%; font-size: 7px !important; word-break: break-all; overflow: hidden; }');   // Loan Number
                printWindow.document.write('#repaymentTable th:nth-child(2), #repaymentTable td:nth-child(2) { width: 18%; max-width: 18%; font-size: 8px !important; word-break: break-word; overflow: hidden; }');   // Customer Name
                printWindow.document.write('#repaymentTable th:nth-child(3), #repaymentTable td:nth-child(3) { width: 10%; max-width: 10%; overflow: hidden; }');   // Amount
                printWindow.document.write('#repaymentTable th:nth-child(4), #repaymentTable td:nth-child(4) { width: 10%; max-width: 10%; overflow: hidden; }');   // Due
                printWindow.document.write('#repaymentTable th:nth-child(5), #repaymentTable td:nth-child(5) { width: 10%; max-width: 10%; overflow: hidden; }');   // Balance
                printWindow.document.write('.paid-amount { width: 5.71% !important; min-width: 5.71% !important; max-width: 5.71% !important; font-size: 8px; }'); // Date columns
                printWindow.document.write('#repaymentTable thead th { font-size: 10px !important; font-weight: bold; text-align: center; }');
                printWindow.document.write('#repaymentTable thead tr:first-child th:last-child { font-size: 12px !important; background-color: #e9ecef !important; }');
                printWindow.document.write('.empty-date-header { width: 5.71% !important; min-width: 5.71% !important; max-width: 5.71% !important; font-size: 10px !important; background-color: #fff !important; padding: 8px 2px !important; height: 25px !important; font-weight: normal !important; }');
                printWindow.document.write('@media print { @page { size: ' + orientation + '; margin: 0.5in; } .signature-section { page-break-before: always; } }');
                printWindow.document.write('</style>');


                printWindow.document.write('<h2 style="text-align:center;">Daily Report Template for ' + currentMonth + ' (' + center_details + ')</h2>');
                printWindow.document.write(printContent);

// Append signature section
                printWindow.document.write(`
   <br><br>
<table class="signature-section" style="width: 100%; font-size: 12px; border: none; line-height: 2; page-break-before: always; margin-top: 50px;">
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
    <script>
        window.addEventListener('beforeprint', function () {
            const existing = document.querySelectorAll('.print-footer');
            existing.forEach(e => e.remove());

            const footer = document.createElement('div');
            footer.className = 'print-footer';

            const left = document.createElement('div');
            left.className = 'left';
            left.innerHTML = "Company: Asipiya Holdings | Center No: {{ $center_no }} | Center Name: {{ $center_name }}";

            const right = document.createElement('div');
            right.className = 'right';
            right.innerHTML = "Printed by: {{ $printedBy }} on {{ $printedAt }} | Page 1";

            footer.appendChild(left);
            footer.appendChild(right);

            document.body.appendChild(footer);
        });
    </script>


@endsection
