
@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
    <!-- DataTable CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">



    <style>

        .card-body {
            padding: 1.5rem;
        }
        .card {
            border-radius: 0.5rem;
        }
        thead {
            background-color: #d9edf7; /* Light blue color */
            color: #31708f; /* Darker blue text for contrast */
        }

        .bg-purple th {
            color: #e1e1e1 !important; /* Ensure white text color for th elements */
        }

        .bg-purple {
            background-color: #1A2942 !important; /* Purple color */
            color: white !important; /* White text color */
        }
        .table {
            width: 100%;
        }

        .card-body {
            padding: 0;
        }

        .card-body {
            padding: 0;
        }

        .table {
            width: 100%;
        }

        .bg-purple th {
            color: #e1e1e1 !important;
        }

        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;
        }



    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">

                <h4 class="page-title">Center Wise Collection Summary</h4>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <!-- Date Range Filter Form -->
            <form method="GET" action="{{ route('center_collection_summary.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="date_from">Select Date</label>
                        <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="simpleinput" class="form-label">Collector</label>
                        <select class="form-control select2" id="collector_id" name="collector_id">
                            <option value="0" {{ $collector_id == 0 ? 'selected' : '' }}>All</option>
                            @foreach($collector as $item)
                                <option value="{{ $item->id }}" {{ $collector_id == $item->id ? 'selected' : '' }}>
                                    {{ $item->Full_Name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-3 align-self-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
                <br><br>
                <button id="customPDF" class="btn btn-danger">Generate PDF</button>
            </form>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- DataTable Wrapper -->
                    <div style="overflow-y: auto; height: auto">
                        <table id="customerTable" class="display nowrap table table-striped table-bordered" style="width:100%">
                            <thead class="sticky-top bg-purple">
                            <tr>
                                <th>Center No</th>
                                <th>Center Name</th>
                                <th>Payment Type</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($finalGroupedData as $center_name => $data)
                                @foreach($data as $record)
                                    <tr>
                                        <td>{{ $record->center_No ?? '-' }}</td>
                                        <td>{{ $center_name ?? 'No Center' }}</td>
                                        <td>{{ $record->payment_type }}</td>
                                        <td>{{ number_format($record->total_amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div> <!-- end row -->




@endsection

@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();

            $('#customPDF').on('click', function() {
                var branchName = {!! json_encode(session('branch_name')) !!} + ' Branch' || 'All Branches';
                var executiveName = {!! json_encode(session('username')) !!} || 'All Executives';
                var selectedDate = $('#date_from').val() || 'All Dates';
                var companyName = {!! json_encode(session('company_name')) !!}.replace(/[^\w\s]/gi, '');
                var totalAmount = 0;

                var data = [];
                $('#customerTable tbody tr').each(function() {
                    var row = [];
                    $(this).find('td').each(function(index) {
                        var cellText = $(this).text().trim();
                        if (index === 3) { // Amount column
                            var amount = parseFloat(cellText.replace(/,/g, '')) || 0;
                            totalAmount += amount;
                            row.push({ text: amount.toFixed(2), alignment: 'right' });
                        } else {
                            row.push({ text: cellText, alignment: 'center' });
                        }
                    });
                    data.push(row);
                });

                var cashTableBody = [
                    [{ text: "5000", alignment: 'center' }, { text: "", alignment: 'center' }, { text: "", alignment: 'right' }],
                    [{ text: "1000", alignment: 'center' }, { text: "", alignment: 'center' }, { text: "", alignment: 'right' }],
                    [{ text: "500", alignment: 'center' }, { text: "", alignment: 'center' }, { text: "", alignment: 'right' }],
                    [{ text: "100", alignment: 'center' }, { text: "", alignment: 'center' }, { text: "", alignment: 'right' }],
                    [{ text: "50", alignment: 'center' }, { text: "", alignment: 'center' }, { text: "", alignment: 'right' }],
                    [{ text: "20", alignment: 'center' }, { text: "", alignment: 'center' }, { text: "", alignment: 'right' }],
                    [{ text: "10", alignment: 'center' }, { text: "", alignment: 'center' }, { text: "", alignment: 'right' }],
                    [
                        { text: "Total of Coins", colSpan: 2, style: 'totalRow', alignment: 'right' },
                        {},
                        { text: "", style: 'totalRow', alignment: 'right' }
                    ],
                    [
                        { text: "Total", colSpan: 2, style: 'totalRow', alignment: 'right' },
                        {},
                        { text: "", style: 'totalRow', alignment: 'right' }
                    ]
                ];

                var docDefinition = {
                    pageSize: 'A4',
                    pageMargins: [30, 20, 30, 20], // Keep margins minimal to fit everything
                    content: [
                        { text: companyName, style: 'companyName', margin: [0, 0, 0, 5] },
                        { text: 'CASH DENOMINATION', style: 'title', margin: [0, 5, 0, 5] },
                        {
                            columns: [
                                { text: `Branch: ${branchName}`, style: 'subheader' },
                                { text: `Executive: ${executiveName}`, style: 'subheader', alignment: 'right' }
                            ]
                        },
                        { text: `Date: ${selectedDate}`, style: 'subheader', margin: [0, 2, 0, 5] },

                        // Collection Summary Table
                        {
                            table: {
                                headerRows: 1,
                                widths: ['15%', '35%', '25%', '25%'],
                                body: [
                                    [
                                        { text: 'Center No', style: 'tableHeader' },
                                        { text: 'Center Name', style: 'tableHeader' },
                                        { text: 'Payment Type', style: 'tableHeader' },
                                        { text: 'Amount', style: 'tableHeader' }
                                    ],
                                    ...data
                                ]
                            },
                            layout: {
                                fillColor: function(rowIndex) {
                                    return rowIndex % 2 === 0 ? '#F5F5F5' : null;
                                }
                            },
                            pageBreak: 'avoid' // Prevents splitting to a new page
                        },
                        { text: '\n' },

                        // Total Row
                        {
                            table: {
                                widths: ['75%', '25%'],
                                body: [
                                    [
                                        { text: 'TOTAL', style: 'totalRow', alignment: 'right' },
                                        { text: totalAmount.toFixed(2), style: 'totalRow', alignment: 'right' }
                                    ]
                                ]
                            },
                            layout: 'noBorders',
                            pageBreak: 'avoid'
                        },

                        // Cash Details Table (Empty for manual input)
                        { text: "CASH DETAILS", style: 'tableTitle', margin: [0, 10, 0, 5] },
                        {
                            table: {
                                headerRows: 1,
                                widths: ['30%', '30%', '40%'],
                                body: [
                                    [
                                        { text: "Denomination", style: 'tableHeader' },
                                        { text: "Quantity", style: 'tableHeader' },
                                        { text: "Total Value", style: 'tableHeader' }
                                    ],
                                    ...cashTableBody
                                ]
                            },
                            layout: 'lightHorizontalLines',
                            pageBreak: 'avoid'
                        },

                        // Excess/Short & Slip Number Section with Blank Space
                        {
                            columns: [
                                { text: "Excess/Short: ____________________", style: 'signatureLabel', alignment: 'left', margin: [0, 10, 0, 0] },
                                { text: "Slip Number: ____________________", style: 'signatureLabel', alignment: 'right', margin: [0, 10, 0, 0] }
                            ],
                            pageBreak: 'avoid'
                        },

                        // Signature Section
                        {
                            columns: [
                                { text: 'Executive Signature', style: 'signatureLabel', alignment: 'center', margin: [0, 80, 0, 5] },
                                { text: 'Manager Signature', style: 'signatureLabel', alignment: 'center', margin: [0, 80, 0, 5] }
                            ],
                            pageBreak: 'avoid'
                        },
                        {
                            columns: [
                                {
                                    canvas: [{ type: 'line', x1: 0, y1: 0, x2: 200, y2: 0, lineWidth: 1 }],
                                    alignment: 'center',
                                    margin: [0, 50, 0, 20]
                                },
                                {
                                    canvas: [{ type: 'line', x1: 0, y1: 0, x2: 200, y2: 0, lineWidth: 1 }],
                                    alignment: 'center',
                                    margin: [0, 50, 0, 20]
                                }
                            ]
                        }
                    ],

                    styles: {
                        companyName: {
                            fontSize: 18,
                            bold: true,
                            alignment: 'center',
                            color: '#1A2942',
                            margin: [0, 0, 0, 5]
                        },
                        title: {
                            fontSize: 14,
                            bold: true,
                            alignment: 'center',
                            color: '#004085'
                        },
                        tableTitle: {
                            fontSize: 12,
                            bold: true,
                            color: '#004085'
                        },
                        subheader: {
                            fontSize: 10,
                            bold: true,
                            color: '#333333'
                        },
                        tableHeader: {
                            fontSize: 10,
                            bold: true,
                            color: 'white',
                            fillColor: '#004085',
                            alignment: 'center'
                        },
                        totalRow: {
                            fontSize: 11,
                            bold: true,
                            fillColor: '#E0F7FA',
                            color: '#333333'
                        },
                        signatureLabel: {
                            fontSize: 11,
                            bold: true,
                            alignment: 'center',
                            color: '#004085'
                        }
                    }
                };

                pdfMake.createPdf(docDefinition).download('Cash_Denomination.pdf');
            });











            // Initialize DataTable with export buttons
            $('#customerTable').DataTable({
                dom: 'Bfrtip',
                buttons: [

                ],
                responsive: true,
                fixedHeader: true,
                order: [[0, 'asc']],
                pageLength: 25
            });
        });


    </script>



@endsection

