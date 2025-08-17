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
                max-width: 150px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .paid-amount {
                width: 80px;
            }

            .correct-column {
                width: 30px;
            }

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
            .print-group-block {
                break-inside: avoid;
                page-break-after: always;
            }
        }
        @media print {
            .print-group-block {
                break-inside: avoid;
            }

            div[style*="page-break-after"] {
                page-break-after: always;
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
                                    <th rowspan="2">Full Name</th>
                                    <th rowspan="2">Loan Amount</th>
                                    <th rowspan="2">Due Installment</th>
                                    <th rowspan="2">New Loan Amount</th>
                                    @for ($i = 1; $i < 6; $i++)
                                        <th colspan="2">Date</th>
                                    @endfor
                                </tr>
                                <tr>
                                    @for ($i = 1; $i < 6; $i++)
                                        <th>Paid</th>
                                        <th>Correct</th>
                                    @endfor
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($grouped_loans->chunk(2) as $groupPair)
                                    <tbody class="print-group-block">
                                    @foreach($groupPair as $group_name => $group)
                                        <tr><td colspan="15"><strong>Group No: {{ $group_name }}</strong></td></tr>
                                        @foreach($group as $item)
                                            <tr class="group-row">
                                                <td>{{ $item->Loan_No }}</td>
                                                <td class="fixed-name">{{ $item->name_with_initials }}</td>
                                                <td>{{ number_format($item->Loan_Amount, 2) }}</td>
                                                <td>{{ number_format($item->Installment_Amount, 2) }}</td>
                                                <td>{{ number_format($item->Balance_Amount, 2) }}</td>
                                                @for ($i = 1; $i < 6; $i++)
                                                    <td class="paid-amount"></td>
                                                    <td class="correct-column"></td>
                                                @endfor
                                            </tr>
                                        @endforeach



                                        @for ($j = 0; $j < 7; $j++)
                                            <tr class="group-row">
                                                @for ($k = 0; $k < 15; $k++)
                                                    <td>&nbsp;</td>
                                                @endfor
                                            </tr>
                                        @endfor
                                        <tr class="group-row" style="font-weight: bold;">
                                            <td colspan="2">Group Total</td>
                                            <td>{{ number_format($group->sum('Loan_Amount'), 2) }}</td>
                                            <td>{{ number_format($group->sum('Installment_Amount'), 2) }}</td>
                                            <td>{{ number_format($group->sum('Balance_Amount'), 2) }}</td>
                                            <td colspan="10"></td>
                                        </tr>
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
                XLSX.utils.book_append_sheet(wb, ws, "Repayment Data");
                XLSX.writeFile(wb, `Repayment_Report_${new Date().toLocaleString('default', { month: 'long' })}.xlsx`);
            });

            $('#printButton').click(function () {
                const centerDetails = $('#center_details option:selected').text();
                const orientation   = $('#pageOrientation').val();

                const centerNo    = '{{ $center_no }}';
                const centerName  = '{{ $center_name }}';
                const printedBy   = '{{ $printedBy }}';
                const printedAt   = '{{ $printedAt }}';
                const companyName = '{{ session("company_name") }}';

                const printWindow = window.open('', '', 'height=800,width=1200');

                // ===== CSS =====
                printWindow.document.write('<html><head><title>Repayment Sheet</title><style>');
                printWindow.document.write(`
    @page { size: ${orientation}; margin: 0.5in; }
    html, body { margin:0; padding:0; }
    body { font-family: Arial, sans-serif; font-size: 11px; }

    .page { page-break-after: always; }
    .page:last-child { page-break-after: auto; }

    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }

    h2 { margin: 2px 0 4px 0; font-size: 14px; text-align: center; }

    table { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 11px; }
    th, td { border: 1px solid #000; padding: 4px; text-align: center; word-break: break-word; min-height: 20px; }

    /* Name column */
    th:nth-child(2), td:nth-child(2) { width: 180px !important; }

    /* Paid columns wider */
    th:nth-child(6), td:nth-child(6),
    th:nth-child(8), td:nth-child(8),
    th:nth-child(10), td:nth-child(10),
    th:nth-child(12), td:nth-child(12),
    th:nth-child(14), td:nth-child(14) {
        width: 120px !important;
    }

    /* Correct columns narrower */
    th:nth-child(7), td:nth-child(7),
    th:nth-child(9), td:nth-child(9),
    th:nth-child(11), td:nth-child(11),
    th:nth-child(13), td:nth-child(13),
    th:nth-child(15), td:nth-child(15) {
        width: 50px !important;
    }

    .brandline { font-size: 14px; font-weight: 700; text-align: left; }
    .metaline  { font-size: 10px; font-weight: 500; text-align: right; }
    .thead-bar td { border: none; padding: 0; }
    .print-group-block { page-break-inside: avoid; }
  `);
                printWindow.document.write('</style></head><body>');

                // Column header HTML
                const columnsHeadHTML = document.querySelector('#repaymentTable thead').innerHTML;

                // Build header
                const buildRepeatingThead = () => `
    <thead>
      <tr class="thead-bar">
        <td colspan="15" style="border:none; padding:0 0 2px 0;">
          <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #ccc; padding-bottom:2px; margin-bottom:2px;">
            <div class="brandline">${companyName}</div>
            <div class="metaline">Center No: ${centerNo} | Center Name: ${centerName} | Printed by: ${printedBy} on ${printedAt}</div>
          </div>
          <h2>Repayment Sheet (${centerDetails})</h2>
        </td>
      </tr>
      ${columnsHeadHTML}
    </thead>
  `;

                const groupBlocks = Array.from(document.querySelectorAll('.print-group-block'));
                let html = '';

                // 2 groups per page
                for (let i = 0; i < groupBlocks.length; i += 2) {
                    html += '<div class="page"><table>';
                    html += buildRepeatingThead();
                    html += '<tbody>';
                    html += groupBlocks[i].outerHTML;
                    if (i + 1 < groupBlocks.length) html += groupBlocks[i + 1].outerHTML;
                    html += '</tbody></table></div>';
                }

                // Summary page (NO table header here)
                html += `
  <div class="page">
    <table>
      <tbody>
        <tr>
          <td colspan="15" style="border:none; padding:0 0 2px 0;">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #ccc; padding-bottom:2px; margin-bottom:2px;">
              <div class="brandline">${companyName}</div>
              <div class="metaline">Center No: ${centerNo} | Center Name: ${centerName} | Printed by: ${printedBy} on ${printedAt}</div>
            </div>
            <h2>Repayment Sheet (${centerDetails})</h2>
          </td>
        </tr>
        <tr><td colspan="15" style="height: 10px;"></td></tr>
        <tr><td colspan="2"><strong>Cumulative Collection</strong></td><td colspan="13"></td></tr>
        <tr><td colspan="2"><strong>Cumulative Due</strong></td><td colspan="13"></td></tr>
        <tr><td colspan="2"><strong>Total</strong></td><td colspan="13"></td></tr>
        <tr><td colspan="2"><strong>No of Under Payment</strong></td><td colspan="13"></td></tr>
        <tr><td colspan="2"><strong>Amount</strong></td><td colspan="13"></td></tr>
        <tr><td colspan="2"><strong>No of Not Paid</strong></td><td colspan="13"></td></tr>
        <tr><td colspan="2"><strong>Amount</strong></td><td colspan="13"></td></tr>
        <tr><td colspan="2"><strong>No of Settlement</strong></td><td colspan="13"></td></tr>
        <tr><td colspan="15" style="height: 15px;"></td></tr>
        <tr><td colspan="2"><strong>Full Signature</strong></td><td colspan="13"></td></tr>
        <tr><td colspan="2"><strong>Center Manager</strong></td><td colspan="13"></td></tr>
      </tbody>
    </table>
  </div>
`;


                printWindow.document.write(html);
                printWindow.document.write('</body></html>');
                printWindow.document.close();

                printWindow.onload = function () {
                    printWindow.focus();
                    printWindow.print();
                };
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

