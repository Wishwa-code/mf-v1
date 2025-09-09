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


    </style>
    <style>
        @media print {
            body {
                font-family: Arial, sans-serif;
                font-size: 12px !important;
                margin: 0;
                padding: 0;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 11px !important;
            }

            th, td {
                border: 1.5px solid #000 !important;
                padding: 5px !important;
                text-align: center;
                vertical-align: middle;
            }

            .page-title,
            .btn,
            .select2-container {
                display: none !important;
            }

            @page {
                size: A4 landscape;
                margin: 0.4in;
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
                            <form action="{{ route('transaction.daily_repayment_sheet_filter_finwin') }}" method="POST">
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
                                        <label for="group_details" class="form-label">Group</label>
                                        <select class="form-control select2" id="group_details" name="group_details">
                                            <option value="0">All</option>
                                        </select>
                                        <input type="hidden" id="group_details_selected" value="{{ $group_details ?? 0 }}">
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
                                            // Calculate penalty from database for this specific loan
                                            $penalty_balance = \Illuminate\Support\Facades\DB::table('installments')
                                                ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                                                ->where('branch_id', session('branch_id'))
                                                ->sum('Panalty_Balance');
                                            
                                            $total_balance_with_penalty = $item->Total_Balance + $penalty_balance;
                                        @endphp
                                        <tr>
                                            <td>{{ format_member_name($item->customer_name, $item->customer_lastname, $name_mode ?? 'with_initial') }}</td>
                                            <td>{{ $item->Loan_No }}</td>
                                            <td>{{ $item->Contact_No }}</td>
                                            <td class="loan-amount">{{ number_format($item->Loan_Amount, 2) }}</td>
                                            <td class="due-amount">{{ number_format($item->Installment_Amount, 2) }}</td>
                                            <td class="total-balance" data-penalty="{{ $penalty_balance }}">{{ number_format($total_balance_with_penalty, 2) }}</td>
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
            $('.select2').select2();

            let centerId = $('#center_details').val();
            let selectedGroupId = $('#group_details_selected').val();

            // Function to load groups for selected center
            function loadGroups(centerId, selectedGroupId) {
                if (centerId) {
                    $.ajax({
                        url: '/get-groups-by-center/' + centerId,
                        type: 'GET',
                        success: function (groups) {
                            let $groupSelect = $('#group_details');
                            $groupSelect.empty();
                            $groupSelect.append('<option value="0">All</option>');

                            $.each(groups, function (key, group) {
                                let isSelected = (group.idCustomer_Group == selectedGroupId) ? 'selected' : '';
                                $groupSelect.append(`<option value="${group.idCustomer_Group}" ${isSelected}>${group.Group_No} - ${group.Name}</option>`);
                            });

                            $groupSelect.trigger('change');
                        }
                    });
                }
            }

            // Load groups on page load
            loadGroups(centerId, selectedGroupId);

            // Load groups again when user manually changes center
            $('#center_details').on('change', function () {
                let selectedCenter = $(this).val();
                $('#group_details').html('<option value="0">All</option>'); // reset immediately
                loadGroups(selectedCenter, 0); // Reset group on manual change
            });

            function calculateTotals() {
                // helper: parse "12,345.67" -> 12345.67
                const num = (txt) => {
                    if (txt == null) return 0;
                    const t = String(txt).replace(/,/g, '').trim();
                    const v = parseFloat(t);
                    return isNaN(v) ? 0 : v;
                };
                // helper: format 2dp with thousands
                const fmt = (n) => Number(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                let centerLoan = 0, centerDue = 0, centerBal = 0, centerArr = 0;
                let groupLoan = 0, groupDue = 0, groupBal = 0, groupArr = 0;

                // We iterate rows in order and keep running group totals
                const $rows = $('#repaymentTable tbody tr');

                $rows.each(function () {
                    const $tr = $(this);

                    // Group header row uses <th> (e.g., "Group No :- X")
                    const isGroupHeader = $tr.find('th').length && /Group\s*No\s*:-/i.test($tr.text());

                    // The “data” rows have the numeric cells with these classes
                    const isDataRow = $tr.find('td').length &&
                        ($tr.find('.loan-amount').length ||
                            $tr.find('.due-amount').length ||
                            $tr.find('.total-balance').length ||
                            $tr.find('.arrears').length);

                    const isGroupTotalRow = $tr.hasClass('group-total');

                    if (isGroupHeader) {
                        // New group starting — reset per-group accumulators
                        groupLoan = groupDue = groupBal = groupArr = 0;
                        return; // continue
                    }

                    if (isDataRow) {
                        groupLoan += num($tr.find('.loan-amount').text());
                        groupDue  += num($tr.find('.due-amount').text());
                        groupBal  += num($tr.find('.total-balance').text());
                        groupArr  += num($tr.find('.arrears').text());
                        return; // continue
                    }

                    if (isGroupTotalRow) {
                        // Write group totals to this total row
                        $tr.find('.group-loan-amount').text(fmt(groupLoan));
                        $tr.find('.group-due-amount').text(fmt(groupDue));
                        $tr.find('.group-total-balance').text(fmt(groupBal));
                        $tr.find('.group-arrears').text(fmt(groupArr));

                        // Add to center totals
                        centerLoan += groupLoan;
                        centerDue  += groupDue;
                        centerBal  += groupBal;
                        centerArr  += groupArr;

                        // Reset for safety before next group (optional)
                        groupLoan = groupDue = groupBal = groupArr = 0;
                    }
                });

                // Footer (center totals)
                $('#total-loan-amount').text(fmt(centerLoan));
                $('#total-due-amount').text(fmt(centerDue));
                $('#total-balance').text(fmt(centerBal));
                $('#total-arrears').text(fmt(centerArr));
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
            printWindow.document.write('body { font-family: Arial, sans-serif; margin: 10px; font-size: 11px; }');
            printWindow.document.write('table { width: 100%; border-collapse: collapse; font-size: 11px; table-layout: auto; }');
            printWindow.document.write('th, td { border: 1.5px solid #000; padding: 5px; text-align: center; word-break: break-word; }');
            printWindow.document.write('@media print { @page { size: ' + orientation + '; margin: 0.4in; } }');
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

