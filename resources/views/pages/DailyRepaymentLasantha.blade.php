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

        .table-responsive {
            overflow-x: auto; /* Enable horizontal scrolling */
            margin: 20px auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        th {
            background-color: #f8f9fa;
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

        .btn-custom {
            padding: 8px 16px;
            margin: 5px;
        }

        /* Select2 styling */
        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 6px;
            font-size: 14px;
        }

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
    </style>
@endsection

@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-6">
                <div class="page-title-box">
                    <h4 class="page-title">Repayment Sheet</h4>
                </div>
            </div>

        </div>

        <!-- Form to filter by center -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <form action="{{ route('transaction.daily_repayment_sheet_filter_lasantha') }}" method="POST">
                                @csrf
                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <label for="center_details" class="form-label">Route</label>
                                        <select class="form-control select2" id="route" name="route">
                                            @foreach ($center as $item)
                                                <option value="{{ $item->id_route }}"
                                                        {{ $item->id_route == $center_details ? 'selected' : '' }}>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-danger btn-custom"><i class="bi bi-search"></i> Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-12">
                                <button id="portraitPrint" class="btn btn-primary btn-custom"><i class="bi bi-printer"></i> Portrait Print</button>
                                <button id="landscapePrint" class="btn btn-secondary btn-custom"><i class="bi bi-printer"></i> Landscape Print</button>

                                <button id="downloadExcel" class="btn btn-success btn-custom"><i class="bi bi-file-earmark-excel"></i> Download Excel</button>
                                <button id="downloadPDF" class="btn btn-secondary btn-custom"><i class="bi bi-file-earmark-pdf"></i> Download PDF</button>
                            </div>
                        </div>

                        <div class="table-responsive"> <!-- Wrapping div for responsive table -->
                            <table border="1" class="dataframe" id="repaymentTable">
                                <thead>
                                <tr style="text-align: right;">
                                    <th colspan="20">{{$route}}</th> <!-- Adjust the colspan value based on the total number of columns -->
                                </tr>


                                </thead>
                                <tbody>
                                <tr>
                                    <td>GROUP</td>
                                    <td>Member Name</td>
                                    <td>PHONE NUMBER</td>
                                    <td>Loan Installment</td>
                                    <td>Loan Amount</td>
                                    <td>Outstanding Amount</td>
                                    <td>DATE</td>
                                    <td>ATTENDANCE</td>
                                    <td>DATE</td>
                                    <td>ATTENDANCE</td>
                                    <td>DATE</td>
                                    <td>ATTENDANCE</td>
                                    <td>DATE</td>
                                    <td>ATTENDANCE</td>
                                    <td>DATE</td>
                                    <td>ATTENDANCE</td>
                                    <td>DATE</td>
                                    <td>ATTENDANCE</td>
                                </tr>
                                @php
                                    $grouped_loans = $grouped_loans->sortKeysUsing('strnatcmp');
                                @endphp



                                @foreach ($grouped_loans as $group_name => $group)
                                    @foreach ($group as $index => $item)
                                        <tr>
                                            @if ($index === 0) <!-- Only show the group name for the first item in the group -->
                                            <td rowspan="{{ count($group) + 5 }}" style="vertical-align: middle; text-align: center;">
                                                <div style="transform: rotate(-90deg); white-space: nowrap; height: auto;">{{ $group_name }}</div>
                                            </td>
                                            @endif

                                            <td>{{ format_member_name($item->customer_name, $item->customer_lastname, $name_mode ?? 'with_initial') }}</td>
                                            <td>{{ $item->Contact_No }}</td>
                                            <td class="loan-amount">{{ number_format($item->Installment_Amount, 2) }}</td>
                                            <td class="loan-amount">{{ number_format($item->Loan_Amount, 2) }}</td>
                                            <td class="total-balance">{{ number_format($item->Total_Balance, 2) }}</td>
                                            @for ($i = 0; $i < 6; $i++)
                                                <td>{{ isset($item->attendance[$i]['date']) ? $item->attendance[$i]['date'] : '' }}</td>
                                                <td>{{ isset($item->attendance[$i]['status']) ? $item->attendance[$i]['status'] : '' }}</td>
                                            @endfor
                                        </tr>
                                    @endforeach

                                    {{-- Add 5 empty rows after each group --}}
                                    @for ($j = 0; $j < 5; $j++)
                                        <tr>
                                            <td colspan="15">&nbsp;</td>
                                        </tr>
                                    @endfor
                                @endforeach


                                </tbody>
                            </table>
                        </div> <!-- end of responsive div -->

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
        $(document).ready(function () {
            $('.select2').select2();

            // Print functionality
            $('#printButton').on('click', function () {
                window.print();
            });

            // PDF download functionality
            $('#downloadPDF').on('click', function () {
                const element = document.getElementById('repaymentTable');
                html2pdf()
                    .from(element)
                    .save('repayment_sheet.pdf');
            });

            // Excel download functionality
            $('#downloadExcel').on('click', function () {
                const table = document.getElementById('repaymentTable');
                const wb = XLSX.utils.table_to_book(table, { sheet: "Repayment Sheet" });
                XLSX.writeFile(wb, 'repayment_sheet.xlsx');
            });
        });
        function customPrint(orientation = 'portrait') {
            const centerDetails = $('#route').find('option:selected').text();
            const table = document.getElementById('repaymentTable').cloneNode(true);

            const printWindow = window.open('', '', 'height=1000,width=1200');
            printWindow.document.write('<html><head><title>Repayment Sheet</title>');

            printWindow.document.write('<style>');
            printWindow.document.write('body { font-family: Arial, sans-serif; font-size: 10px; margin: 10px; padding: 0; }');
            printWindow.document.write('table { width: 100%; border-collapse: collapse; table-layout: auto; font-size: 9px; }');
            printWindow.document.write('th, td { border: 1px solid #000; padding: 6px; text-align: center; word-wrap: break-word; }');
            printWindow.document.write('@media print { @page { size: ' + orientation + '; margin: 0.5in; } }');
            printWindow.document.write('</style>');

            printWindow.document.write('</head><body>');
            printWindow.document.write('<h2 style="text-align:center;">Repayment Sheet</h2>');
            printWindow.document.write('<h4 style="text-align:center;">Route: ' + centerDetails + '</h4>');
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
