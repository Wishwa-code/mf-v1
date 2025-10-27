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

    /* Same Column widths for all */
    #repaymentTable th, #repaymentTable td { white-space: nowrap; }
    #repaymentTable th:nth-child(1), #repaymentTable td:nth-child(1) { width: 8%; }
    #repaymentTable th:nth-child(2), #repaymentTable td:nth-child(2) { width: 18%; }
    #repaymentTable th:nth-child(3), #repaymentTable td:nth-child(3) { width: 20%; }
    #repaymentTable th:nth-child(4), #repaymentTable td:nth-child(4) { width: 9%; }
    #repaymentTable th:nth-child(5), #repaymentTable td:nth-child(5) { width: 9%; }
    #repaymentTable th:nth-child(6), #repaymentTable td:nth-child(6) { width: 10%; }

    /* logic for contact no always no wrap */
    .contact-no { max-width: none !important; white-space: nowrap; overflow: visible; text-overflow: clip; }

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
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            #repaymentTable th:nth-child(1), #repaymentTable td:nth-child(1) { width: 8%; }   /* Loan No */
            #repaymentTable th:nth-child(2), #repaymentTable td:nth-child(2) { width: 18%; } /* Full Name */
            #repaymentTable th:nth-child(3), #repaymentTable td:nth-child(3) { width: 20%; } /* Contact No (wider for one-line mobile) */
            #repaymentTable th:nth-child(4), #repaymentTable td:nth-child(4) { width: 9%; }  /* Loan Amount */
            #repaymentTable th:nth-child(5), #repaymentTable td:nth-child(5) { width: 9%; }  /* Due Installment */
            #repaymentTable th:nth-child(6), #repaymentTable td:nth-child(6) { width: 10%; } /* New Loan Amount */
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
                            <form action="{{ route('transaction.RepaymentSheet10') }}" method="GET">
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

                                <div class="d-inline-flex align-items-center flex-wrap gap-2 ms-2 mt-2">


                                    <input type="hidden" id="empty_row_count_local" class="form-control"
                                           style="width: 100px;" min="0" max="100"
                                           value="{{ $empty_row_count ?? 5 }}" />
                                </div>
                            </div>
                        </div>


                        <!-- Repayment table -->
                        <div class="table-responsive">
                            <table id="repaymentTable">
                                <colgroup>
                                    <col style="width:6%">
                                    <col style="width:12%">
                                    <col style="width:8%">
                                    <col style="width:8%">
                                    <col style="width:8%">
                                    <col style="width:8%">
                                    <col style="width:8%">
                                    <col style="width:6%">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <col class="paid-col" style="width:5%">
                                        <col class="correct-col" style="width:4%">
                                    @endfor
                                </colgroup>
                                <thead>
                                <tr>
                                    <th rowspan="2">MEMBER NO</th>
                                    <th rowspan="2">MEMBER NAME</th>
                                    <th rowspan="2">LOAN AMOUNT</th>
                                    <th rowspan="2">INSTALLMENT</th>
                                    <th rowspan="2">LOAN BALANCE</th>
                                    <th rowspan="2">Phone No</th>
                                    <th rowspan="2">NEW LOAN</th>
                                    <th rowspan="2">No.Of Arreas</th>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <th colspan="2">DATE</th>
                                    @endfor
                                </tr>
                                <tr>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <th>PAID AMOUNT</th>
                                        <th>CORRE-CTION</th>
                                    @endfor
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($grouped_loans->chunk(2) as $groupPair)
                                    <tbody class="page-break">
                                    @foreach($groupPair as $group_name => $group)
                                        <tr><td colspan="18"><strong>Group No: {{ $group_name }}</strong></td></tr>
                                        @foreach($group as $item)
                                            <tr class="group-row">
                                                <td>{{ $item->cus_number }}</td>
                                                <td class="fixed-name">{{ format_member_name($item->customer_name, $item->customer_lastname, $name_mode ?? 'with_initial') }}</td>
                                                <td>{{ number_format($item->Loan_Amount, 2) }}</td>
                                                <td>{{ number_format($item->Installment_Amount, 2) }}</td>
                                                <td>{{ number_format($item->Balance_Amount, 2) }}</td>
                                                <td class="fixed-name contact-no">{{ $item->Contact_No }}</td>
                                                <td></td>
                                                <td>{{ $item->Installment_Count }}</td>
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <td class="paid-amount"></td>
                                                    <td class="correct-column"></td>
                                                @endfor
                                            </tr>
                                        @endforeach
                                        <tr class="group-row" style="font-weight: bold;">
                                            <td colspan="2">Group Total</td>
                                            <td>{{ number_format($group->sum('Loan_Amount'), 2) }}</td>
                                            <td>{{ number_format($group->sum('Installment_Amount'), 2) }}</td>
                                            <td>{{ number_format($group->sum('Balance_Amount'), 2) }}</td>
                                            <td colspan="13"></td>
                                        </tr>
                                        {{-- Empty 7 Rows --}}
                                        @for ($j = 0; $j < 2; $j++)
                                            <tr class="group-row">
                                                @for ($k = 0; $k < 18; $k++)
                                                    <td>&nbsp;</td>
                                                @endfor
                                            </tr>
                                        @endfor
                                    @endforeach
                                    </tbody>
                                    @endforeach
                                    </tbody>
                            </table>

                            <br><br>

                            <!-- Summary Table -->
                            <table id="summaryTable" style="width: 100%; border-collapse: collapse; margin-top: 20px; table-layout: fixed;">
                                <colgroup>
                                    <col style="width: 15%;">
                                    @for ($i = 1; $i <= 6; $i++)
                                        <col style="width: 5%;">
                                    @endfor
                                    @for ($i = 1; $i <= 10; $i++)
                                        <col style="width: 5%;">
                                    @endfor
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="border: 1px solid black; padding: 8px; text-align: left;"></th>
                                        @for ($i = 1; $i <= 6; $i++)
                                            <th rowspan="2" style="border: 1px solid black; padding: 8px; text-align: center;"></th>
                                        @endfor
                                        @for ($i = 1; $i <= 5; $i++)
                                            <th colspan="2" style="border: 1px solid black; padding: 8px; text-align: center;">DATE</th>
                                        @endfor
                                    </tr>
                                    <tr>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <th style="border: 1px solid black; padding: 8px; text-align: center;">PAID AMOUNT</th>
                                            <th style="border: 1px solid black; padding: 8px; text-align: center;">CORRE-CTION</th>
                                        @endfor
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="border: 1px solid black; padding: 8px; font-weight: bold;">Total Collection Amount</td>
                                        @for ($i = 1; $i <= 6; $i++)
                                            <td style="border: 1px solid black; padding: 8px;"></td>
                                        @endfor
                                        @for ($i = 1; $i <= 10; $i++)
                                            <td style="border: 1px solid black; padding: 8px;"></td>
                                        @endfor
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid black; padding: 8px; font-weight: bold;">Total Due Amount</td>
                                        @for ($i = 1; $i <= 6; $i++)
                                            <td style="border: 1px solid black; padding: 8px;"></td>
                                        @endfor
                                        @for ($i = 1; $i <= 10; $i++)
                                            <td style="border: 1px solid black; padding: 8px;"></td>
                                        @endfor
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid black; padding: 8px; font-weight: bold;">Number Of Under Paid</td>
                                        @for ($i = 1; $i <= 6; $i++)
                                            <td style="border: 1px solid black; padding: 8px;"></td>
                                        @endfor
                                        @for ($i = 1; $i <= 10; $i++)
                                            <td style="border: 1px solid black; padding: 8px;"></td>
                                        @endfor
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid black; padding: 8px; font-weight: bold;">Under Paid Amount</td>
                                        @for ($i = 1; $i <= 6; $i++)
                                            <td style="border: 1px solid black; padding: 8px;"></td>
                                        @endfor
                                        @for ($i = 1; $i <= 10; $i++)
                                            <td style="border: 1px solid black; padding: 8px;"></td>
                                        @endfor
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid black; padding: 8px; font-weight: bold;">Number Of Not Paid</td>
                                        @for ($i = 1; $i <= 6; $i++)
                                            <td style="border: 1px solid black; padding: 8px;"></td>
                                        @endfor
                                        @for ($i = 1; $i <= 10; $i++)
                                            <td style="border: 1px solid black; padding: 8px;"></td>
                                        @endfor
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid black; padding: 8px; font-weight: bold;">Not Paid Amount</td>
                                        @for ($i = 1; $i <= 6; $i++)
                                            <td style="border: 1px solid black; padding: 8px;"></td>
                                        @endfor
                                        @for ($i = 1; $i <= 10; $i++)
                                            <td style="border: 1px solid black; padding: 8px;"></td>
                                        @endfor
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid black; padding: 8px; font-weight: bold;">Number Of Settlment</td>
                                        @for ($i = 1; $i <= 6; $i++)
                                            <td style="border: 1px solid black; padding: 8px;"></td>
                                        @endfor
                                        @for ($i = 1; $i <= 10; $i++)
                                            <td style="border: 1px solid black; padding: 8px;"></td>
                                        @endfor
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid black; padding: 8px; font-weight: bold;">Settlment Amount</td>
                                        @for ($i = 1; $i <= 6; $i++)
                                            <td style="border: 1px solid black; padding: 8px;"></td>
                                        @endfor
                                        @for ($i = 1; $i <= 10; $i++)
                                            <td style="border: 1px solid black; padding: 8px;"></td>
                                        @endfor
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid black; padding: 8px; font-weight: bold;">Deposit Slip Number</td>
                                        <td colspan="6" style="border: 1px solid black; padding: 8px;"></td>
                                        @for ($i = 1; $i <= 10; $i++)
                                            <td style="border: 1px solid black; padding: 8px;"></td>
                                        @endfor
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid black; padding: 8px; font-weight: bold;">Center Manager Signature</td>
                                        <td colspan="6" style="border: 1px solid black; padding: 8px; height: 40px;"></td>
                                        @for ($i = 1; $i <= 10; $i++)
                                            <td style="border: 1px solid black; padding: 8px; height: 40px;"></td>
                                        @endfor
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid black; padding: 8px; font-weight: bold;">Cashier/Accountant Signature</td>
                                        <td colspan="6" style="border: 1px solid black; padding: 8px; height: 40px;"></td>
                                        @for ($i = 1; $i <= 10; $i++)
                                            <td style="border: 1px solid black; padding: 8px; height: 40px;"></td>
                                        @endfor
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid black; padding: 8px; font-weight: bold;">Field Manager Signature</td>
                                        <td colspan="6" style="border: 1px solid black; padding: 8px; height: 40px;"></td>
                                        @for ($i = 1; $i <= 10; $i++)
                                            <td style="border: 1px solid black; padding: 8px; height: 40px;"></td>
                                        @endfor
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid black; padding: 8px; font-weight: bold;">Branch Manager Signature</td>
                                        <td colspan="6" style="border: 1px solid black; padding: 8px; height: 40px;"></td>
                                        @for ($i = 1; $i <= 10; $i++)
                                            <td style="border: 1px solid black; padding: 8px; height: 40px;"></td>
                                        @endfor
                                    </tr>
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

            $('#btnUpdateEmptyRowCountLocal').on('click', function () {
                const emptyRows = parseInt($('#empty_row_count_local').val(), 10);
                const orderBy   = $('#order_by').val();
                const sheetKey  = $('#repayment_sheet_key').val(); // e.g., "rs9"

                if (isNaN(emptyRows) || emptyRows < 0 || emptyRows > 100) {
                    alert('Empty row count must be between 0 and 100.');
                    return;
                }

                const token   = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').first().val();
                const headers = { 'X-CSRF-TOKEN': token };

                const req1 = $.ajax({
                    type: 'POST',
                    url: '/settings/upsert',
                    headers,
                    data: { key: `empty_row_count_${sheetKey}`, value: emptyRows }
                });

                const req2 = $.ajax({
                    type: 'POST',
                    url: '/settings/upsert',
                    headers,
                    data: { key: `repayment_order_${sheetKey}`, value: orderBy }
                });

                $.when(req1, req2).done(function () {
                    location.reload();
                }).fail(function (xhr) {
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to update setting(s)';
                    alert(msg);
                });
            });


            $('#printButton').click(function () {
                const currentMonth = new Date().toLocaleString('default', {month: 'long'});
                const center_details = $('#center_details').find('option:selected').text();
                const orientation = $('#pageOrientation').val();

                const printWindow = window.open('', '', 'height=800,width=1200');
                const repaymentTableContent = document.getElementById('repaymentTable').outerHTML;
                const summaryTableContent = document.getElementById('summaryTable').outerHTML;

                printWindow.document.write('<html><head><title>Repayment Sheet</title>');
                printWindow.document.write('<style>');
                printWindow.document.write('body { font-family: Arial, sans-serif; font-size: 10px; margin: 0.5in; }');
                printWindow.document.write('#repaymentTable { width: 100%; border-collapse: collapse; font-size: 8px; }');
                printWindow.document.write('#repaymentTable th, #repaymentTable td { border: 1px solid black; padding: 3px; text-align: center; font-size: 7px; }');
                printWindow.document.write('#repaymentTable th { font-weight: bold; white-space: normal; word-wrap: break-word; }');
                printWindow.document.write('#repaymentTable td { white-space: nowrap; overflow: hidden; }');
                printWindow.document.write('.paid-amount, .correct-column { white-space: nowrap; }');
                printWindow.document.write('#summaryTable { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 8px; table-layout: fixed; }');
                printWindow.document.write('#summaryTable th { border: 1px solid black; padding: 4px; text-align: center; font-weight: bold; white-space: normal; word-wrap: break-word; font-size: 7px; }');
                printWindow.document.write('#summaryTable td { border: 1px solid black; padding: 4px; text-align: center; white-space: nowrap; overflow: hidden; font-weight: normal; }');
                printWindow.document.write('#summaryTable tbody td:first-child { text-align: left; font-weight: bold; white-space: normal; }');
                printWindow.document.write('@media print { @page { size: ' + orientation + '; margin: 0.5in; } }');
                printWindow.document.write('</style>');


                printWindow.document.write('<h2 style="text-align:center;">Repayment Sheet for ' + currentMonth + ' (' + center_details + ')</h2>');
                printWindow.document.write(repaymentTableContent);
                printWindow.document.write('<br><br>');
                printWindow.document.write(summaryTableContent);

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

