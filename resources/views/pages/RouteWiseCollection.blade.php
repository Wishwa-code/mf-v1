
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
                        <input type="date" class="form-control" name="date_from" id="date_from" value="{{ $date ?? date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="simpleinput" class="form-label">Route</label>
                        <select class="form-control select2" id="route_id" name="route_id">
{{--                            <option value="0" {{ $route_id == 0 ? 'selected' : '' }}>All</option>--}}
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
            <button id="customPDF" class="btn btn-danger">Generate PDF With Paid Amounts</button>
            <button id="customPDF_2" class="btn btn-danger">Generate PDF Without Paid Amounts</button>
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
                                    @php
                                        $paidAmount = $item->paid_amount ?? 0;
                                        $subTotal += $paidAmount;
                                    @endphp
                                    <tr @if($item->paid_amount === null) style="background-color: #fff5f5;" @endif>
                                        <td>{{ $item->customer_number }}</td>
                                        <td>{{ $item->customer_name }}</td>
                                        <td>{{ $item->loan_number }}</td>
                                        <td class="text-end">{{ number_format($item->installment_amount, 2) }}</td>
                                        <td class="text-end">
                                            {{ $item->paid_amount !== null ? number_format($item->paid_amount, 2) : '' }}
                                        </td>
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
        function generatePDF(includePaid) {
            const companyName = {!! json_encode(session('company_name')) !!};
            const selectedDate = $('#date_from').val() || 'All Dates';
            let currentRoute = '';
            let routeTable = [];
            let subTotal = 0;

            const fullTableLayout = {
                hLineWidth: () => 0.5,
                vLineWidth: () => 0.5,
                hLineColor: () => '#aaa',
                vLineColor: () => '#aaa',
                paddingLeft: () => 5,
                paddingRight: () => 5,
                paddingTop: () => 3,
                paddingBottom: () => 3,
                fillColor: (rowIndex) => rowIndex === 0 ? '#1A2942' : null
            };

            $('#customerTable tbody tr').each(function () {
                const tds = $(this).find('td');

                if (tds.length === 1) {
                    // Header row - route
                    currentRoute = tds.eq(0).text().trim();
                } else if (tds.length === 5) {
                    const values = tds.map(function () {
                        return $(this).text().trim();
                    }).get();

                    const paidValue = includePaid ? values[4] : ''; // leave blank if false
                    const paidAmount = parseFloat(values[4].replace(/,/g, '')) || 0;
                    if (includePaid) subTotal += paidAmount;

                    routeTable.push([
                        { text: values[0], fontSize: 9 },
                        { text: values[1], fontSize: 9 },
                        { text: values[2], fontSize: 9 },
                        { text: values[3], alignment: 'right', fontSize: 9 },
                        { text: paidValue, alignment: 'right', fontSize: 9 },
                        { text: '', fontSize: 9 } // remark left blank
                    ]);
                }
            });

            // Table header
            routeTable.unshift([
                { text: 'Customer No', bold: true, color: 'white', fontSize: 9 },
                { text: 'Customer Name', bold: true, color: 'white', fontSize: 9 },
                { text: 'Loan No', bold: true, color: 'white', fontSize: 9 },
                { text: 'Installment', bold: true, color: 'white', alignment: 'right', fontSize: 9 },
                { text: 'Paid Amount', bold: true, color: 'white', alignment: 'right', fontSize: 9 },
                { text: 'Remark', bold: true, color: 'white', fontSize: 9 }
            ]);

            const content = [
                {
                    columns: [
                        { text: `${companyName}`, style: 'header', width: '*' },
                        { text: `Route: ${currentRoute}`, alignment: 'center', style: 'header', width: '*' },
                        { text: `Date: ${selectedDate}`, alignment: 'right', style: 'subheader', width: '*' }
                    ],
                    margin: [0, 0, 0, 10]
                },
                {
                    table: {
                        widths: ['12%', '24%', '18%', '13%', '13%', '20%'],
                        body: routeTable
                    },
                    layout: fullTableLayout,
                    margin: [0, 0, 0, 10]
                }
            ];

            if (includePaid) {
                content.push({
                    text: `Total Paid: ${subTotal.toFixed(2)}`,
                    alignment: 'right',
                    bold: true,
                    margin: [0, 5, 0, 0],
                    fontSize: 10
                });
            }

            const docDefinition = {
                pageSize: 'A4',
                pageMargins: [30, 30, 30, 30],
                content: content,
                styles: {
                    header: { fontSize: 12, bold: true },
                    subheader: { fontSize: 10 }
                }
            };

            const fileName = includePaid ? 'RouteWiseCollection_WithPaid.pdf' : 'RouteWiseCollection_WithoutPaid.pdf';
            pdfMake.createPdf(docDefinition).download(fileName);
        }

        $(document).ready(function () {
            $('#customPDF').on('click', function () {
                generatePDF(true);
            });

            $('#customPDF_2').on('click', function () {
                generatePDF(false);
            });
        });
    </script>

@endsection

