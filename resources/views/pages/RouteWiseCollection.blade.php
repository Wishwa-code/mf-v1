
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

        .table th, .table td {
            vertical-align: middle;
            font-size: 14px;
            padding: 0.6rem;
        }

        tr:nth-child(even) td {
            background-color: #fcfcfc;
        }

        .table td, .table th {
            padding: 0.6rem;
            font-size: 14px;
        }

        tr:nth-child(even) td {
            background-color: #fcfcfc;
        }

        tr td.text-end {
            text-align: right;
        }

    </style>

@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Route Wise Daily Collected Payment</h4>
            </div>
        </div>

    </div>

    <div class="row mt-3">
        <div class="col-12">
            <!-- Date Range Filter Form -->
            <form method="GET" action="{{ route('root_wise.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="date_from">Select Date</label>
                        <input type="date" class="form-control" name="date_from" id="date_from" value="{{ $date }}">
                    </div>
                    <div class="col-md-3">
                        <label for="simpleinput" class="form-label">Route</label>
                        <select class="form-control select2" id="route_id" name="route_id">
                            <option value="0" {{ $route_id == 0 ? 'selected' : '' }}>All</option>
                            @foreach($route as $item)
                                <option value="{{ $item->id_route }}" {{ $route_id == $item->id_route ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-3 align-self-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
                <br><br>
            </form>
            <button id="customPDF" class="btn btn-danger">Generate PDF</button>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- DataTable Wrapper -->
                    <div style="overflow-y: auto; height: auto">
                        @php
                            $grouped = $collection->groupBy('route_name');
                            $grandTotal = 0;
                        @endphp

                        <table id="customerTable" class="table table-bordered table-hover align-middle">
                            <thead class="bg-purple text-white text-center">
                            <tr>
                                <th>Customer Number</th>
                                <th>Customer Name</th>
                                <th>Loan Number</th>
                                <th class="text-end">Installment Amount</th>
                                <th class="text-end">Paid Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($grouped as $routeName => $items)
                                {{-- Route Header --}}
                                <tr style="background-color: #2c2c2c; color: #fff; font-weight: bold;">
                                    <td colspan="5">{{ $routeName ?? 'Unknown' }}</td>
                                </tr>

                                @php $subTotal = 0; @endphp

                                @foreach($items as $item)
                                    @php $subTotal += $item->paid_amount; @endphp
                                    <tr>
                                        <td>{{ $item->customer_number }}</td>
                                        <td>{{ $item->customer_name }}</td>
                                        <td>{{ $item->loan_number }}</td>
                                        <td class="text-end">{{ number_format($item->installment_amount, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->paid_amount, 2) }}</td>
                                    </tr>
                                @endforeach

                                {{-- Route Subtotal --}}
                                <tr style="background-color: #e8f0fe;">
                                    <td colspan="4" class="text-end fw-bold">Total for {{ $routeName }}</td>
                                    <td class="text-end fw-bold">{{ number_format($subTotal, 2) }}</td>
                                </tr>

                                @php $grandTotal += $subTotal; @endphp
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr style="background-color: #050505; color: #ffffff;">
                                <td colspan="4" class="text-end fw-bold">Grand Total</td>
                                <td class="text-end fw-bold">{{ number_format($grandTotal, 2) }}</td>
                            </tr>
                            </tfoot>
                        </table>







                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div> <!-- end row -->




@endsection

@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>



    <script>
        $(document).ready(function () {
            $('#customPDF').on('click', function () {
                const companyName = {!! json_encode(session('company_name')) !!};
                const selectedDate = $('#date_from').val() || 'All Dates';

                let contentBody = [];
                let currentRoute = '';
                let subTotal = 0;
                let grandTotal = 0;
                let routeTable = [];

                // ✅ Shared table layout for all tables
                const fullTableLayout = {
                    hLineWidth: function () { return 0.5; },
                    vLineWidth: function () { return 0.5; },
                    hLineColor: function () { return '#000000'; },
                    vLineColor: function () { return '#000000'; },
                    paddingLeft: function () { return 6; },
                    paddingRight: function () { return 6; },
                    paddingTop: function () { return 4; },
                    paddingBottom: function () { return 4; },
                    fillColor: function (rowIndex) {
                        return rowIndex === 0 ? '#4d4c4c' : null;
                    }
                };

                $('#customerTable tbody tr').each(function () {
                    const tds = $(this).find('td');

                    if (tds.length === 1) {
                        if (routeTable.length > 1) {
                            contentBody.push({
                                table: {
                                    widths: ['*', '*', '*', 'auto', 'auto', '*'],
                                    body: routeTable
                                },
                                layout: fullTableLayout,
                                margin: [0, 0, 0, 10]
                            });

                            contentBody.push({
                                table: {
                                    widths: ['*', '*', '*', 'auto', 'auto', '*'],
                                    body: [
                                        [
                                            { text: `Total for ${currentRoute}`, colSpan: 5, alignment: 'right', bold: true },
                                            {}, {}, {}, {},
                                            { text: subTotal.toFixed(2), alignment: 'right', bold: true }
                                        ]
                                    ]
                                },
                                layout: fullTableLayout,
                                margin: [0, 0, 0, 10]
                            });

                            grandTotal += subTotal;
                            routeTable = [];
                            subTotal = 0;
                        }

                        currentRoute = $(this).text().trim();
                        contentBody.push({ text: currentRoute, bold: true, fontSize: 12, margin: [0, 10, 0, 5] });

                        routeTable.push([
                            { text: 'Customer No', bold: true, color: 'white' },
                            { text: 'Customer Name', bold: true, color: 'white' },
                            { text: 'Loan No', bold: true, color: 'white' },
                            { text: 'Installment', bold: true, alignment: 'right', color: 'white' },
                            { text: 'Paid', bold: true, alignment: 'right', color: 'white' },
                            { text: 'Remark', bold: true, color: 'white' }
                        ]);
                    } else if (tds.length === 5) {
                        const values = tds.map(function () {
                            return $(this).text().trim();
                        }).get();

                        const paid = parseFloat(values[4].replace(/,/g, '')) || 0;
                        subTotal += paid;

                        routeTable.push([
                            { text: values[0] },
                            { text: values[1] },
                            { text: values[2] },
                            { text: values[3], alignment: 'right' },
                            { text: values[4], alignment: 'right' },
                            { text: '' }
                        ]);
                    }
                });

                if (routeTable.length > 1) {
                    contentBody.push({
                        table: {
                            widths: ['*', '*', '*', 'auto', 'auto', '*'],
                            body: routeTable
                        },
                        layout: fullTableLayout,
                        margin: [0, 0, 0, 10]
                    });

                    contentBody.push({
                        table: {
                            widths: ['*', '*', '*', 'auto', 'auto', '*'],
                            body: [
                                [
                                    { text: `Total for ${currentRoute}`, colSpan: 5, alignment: 'right', bold: true },
                                    {}, {}, {}, {},
                                    { text: subTotal.toFixed(2), alignment: 'right', bold: true }
                                ]
                            ]
                        },
                        layout: fullTableLayout,
                        margin: [0, 0, 0, 10]
                    });

                    grandTotal += subTotal;
                }

                // ✅ Final Grand Total
                contentBody.push({
                    table: {
                        widths: ['*', '*', '*', 'auto', 'auto', '*'],
                        body: [
                            [
                                { text: 'Grand Total', colSpan: 5, alignment: 'right', bold: true },
                                {}, {}, {}, {},
                                { text: grandTotal.toFixed(2), alignment: 'right', bold: true }
                            ]
                        ]
                    },
                    layout: fullTableLayout,
                    margin: [0, 10, 0, 0]
                });

                const docDefinition = {
                    pageSize: 'A4',
                    pageMargins: [30, 30, 30, 30],
                    content: [
                        {
                            columns: [
                                { text: '', width: '*' },
                                {
                                    text: `${companyName} - Route Collection Report`,
                                    alignment: 'center',
                                    bold: true,
                                    fontSize: 14,
                                    width: 'auto'
                                },
                                {
                                    text: `Date: ${selectedDate}`,
                                    alignment: 'right',
                                    fontSize: 10,
                                    width: '*'
                                }
                            ],
                            margin: [0, 0, 0, 20]
                        },
                        ...contentBody
                    ]
                };

                pdfMake.createPdf(docDefinition).download('RouteWiseCollection.pdf');
            });
        });
    </script>






@endsection

