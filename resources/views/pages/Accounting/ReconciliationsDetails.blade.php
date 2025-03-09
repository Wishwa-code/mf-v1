@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <!-- jQuery (must be before DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>


    <style>
        .table thead th {
            background-color: #007bff;
            color: #000000;
            text-align: center;
        }

        .table tbody td {
            text-align: center;
        }

        .table tfoot th {
            background-color: #0e0e0e;
            font-weight: bold;
            text-align: center;
        }

        .table tfoot th:first-child {
            text-align: left;
        }

        .table th,
        .table td {
            padding: 12px;
        }

        body {
            background-color: #ffffff;
        }
    </style>
    <style>
        .modal-lg {
            max-width: 70%;  /* Set modal to 90% of the screen width */
        }
        /* Hover effect for better UX */
        .clickable-row:hover {
            background-color: #f6f1f1 !important; /* Light green */
            cursor: pointer;
            font-weight: bold;
        }
    </style>
    <style>
        /* Table Header Styling */
        .table thead th {
            background-color: #007bff;
            color: #ffffff;
            text-align: center;
        }

        /* Table Body & Footer Styling */
        .table tbody td, .table tfoot th {
            text-align: center;
            padding: 12px;
        }

        /* Highlighted Sections */
        .table-secondary {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }

        /* Special Styling for Checks and Payments, Deposits and Credits */
        .section-header {
            background-color: #343a40 !important; /* Dark Background */
            color: #000000 !important; /* White Text */
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
        }

        /* Currency Formatting */
        .text-end {
            font-weight: bold;
        }

        /* Modal Width */
        .modal-lg {
            max-width: 70%;
        }

        /* Hover Effect for Better UX */
        .clickable-row:hover {
            background-color: #f6f1f1 !important;
            cursor: pointer;
            font-weight: bold;
        }

        /* Centered Download Button */
        #downloadPdf {
            display: block;
            margin: 20px auto;
            font-size: 16px;
        }

        /* Table Borders */
        .table-bordered td, .table-bordered th {
            border: 1px solid #ddd;
        }
        /* Reduce row height */
        .table th, .table td {
            padding: 10px !important; /* Reduce padding to decrease row height */
            line-height: 1.2 !important; /* Adjust line spacing */
        }

    </style>

@endsection

@section('content')
    <div class="container mt-4">
        <h3 class="text-center">Reconciliation Detail</h3>
        <h5 class="text-center">Bank - {{$reconciliation->Bank_Name}}, Period <span id="summaryEndDate">{{$reconciliation->date}},User - {{$reconciliation->Full_Name}}</span></h5>
        <button id="downloadPdf" class="btn btn-primary mb-3">Download PDF</button>


        <table class="table table-bordered mt-3">
            <tbody>
            <tr>
                <th><h4>Beginning Balance</h4></th>
                <td class="text-end" id="beginningBalance"><h4>{{number_format($reconciliation->balance,2,'.',',')}}</h4></td>
            </tr>
            <tr class="table-secondary">
                <th colspan="2">Cleared Transactions</th>
            </tr>
            @foreach($Checks_and_Payments as $item)
                @if($item->credit>0)
                    <tr>
                        <th><span id="clearedChecksCount">{{$item->type}}</span></th>
                        <td class="text-end" id="clearedChecksTotal">({{number_format($item->credit,2,'.',',')}})</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <th><h4>Checks and Payments - <span id="clearedChecksCount">{{$Checks_and_Payments_count}}</span> items</h4></th>
                <td class="text-end" id="clearedChecksTotal"><h4>{{number_format($Checks_and_Payments_sum*-1,2,'.',',')}}</h4></td>
            </tr>
            @foreach($Checks_and_Payments as $item)
                @if($item->debit>0)
                    <tr>
                        <th><span id="clearedChecksCount">{{$item->type}}</span></th>
                        <td class="text-end" id="clearedChecksTotal">{{number_format($item->debit,2,'.',',')}}</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <th><h4>Deposits and Credits - <span id="clearedDepositsCount">{{$Deposits_and_Credits_count}}</span> items</h4></th>
                <td class="text-end" id="clearedDepositsTotal"><h4>{{number_format($Deposits_and_Credits_sum,2,'.',',')}}</h4></td>
            </tr>
            <tr>
                <th><h4>Total Cleared Transactions</h4></th>
                <td class="text-end fw-bold" id="totalClearedTransactions"><h4>{{number_format($Checks_and_Payments_sum*-1 +$Deposits_and_Credits_sum,2,'.',',')}}</h4></td>
            </tr>
            <tr>
                <th><h4>Cleared Balance</h4></th>
                <td class="text-end fw-bold text-primary" id="clearedBalance"><h4>{{number_format(($Checks_and_Payments_sum*-1 +$Deposits_and_Credits_sum)+$reconciliation->balance,2,'.',',')}}</h4></td>
            </tr>
            <tr class="table-secondary">
                <th colspan="2">Uncleared Transactions</th>
            </tr>
            @foreach($Checks_and_Payments_uncleared as $item)
                @if($item->credit>0)
                    <tr>
                        <th><span id="clearedChecksCount">{{$item->type}}</span></th>
                        <td class="text-end" id="clearedChecksTotal">({{number_format($item->credit,2,'.',',')}})</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <th><h4>Checks and Payments - <span id="unclearedChecksCount">{{$Checks_and_Payments_count_uncleared}}</span> items</h4></th>
                <td class="text-end" id="unclearedChecksTotal"><h4>{{number_format($Checks_and_Payments_sum_uncleared*-1,2,'.',',')}}</h4></td>
            </tr>
            @foreach($Checks_and_Payments_uncleared as $item)
                @if($item->debit>0)
                    <tr>
                        <th><span id="clearedChecksCount">{{$item->type}}</span></th>
                        <td class="text-end" id="clearedChecksTotal">({{number_format($item->debit,2,'.',',')}})</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <th><h4>Deposits and Credits - <span id="unclearedDepositsCount">{{$Deposits_and_Credits_count_uncleared}}</span> item</h4></th>
                <td class="text-end" id="unclearedDepositsTotal"><h4>{{number_format($Deposits_and_Credits_sum_uncleared,2,'.',',')}}</h4></td>
            </tr>
            <tr>
                <th><h4>Total Uncleared Transactions</h4></th>
                <td class="text-end fw-bold text-danger" id="totalUnclearedTransactions"><h4>{{number_format($Checks_and_Payments_sum_uncleared*-1 +$Deposits_and_Credits_sum_uncleared,2,'.',',')}}</h4></td>
            </tr>
            <tr>
                <th><h4>Register Balance</h4></th>
                <td class="text-end fw-bold text-primary" id="registerBalance"><h4>{{number_format((($Checks_and_Payments_sum*-1 +$Deposits_and_Credits_sum)+$reconciliation->balance)+$Checks_and_Payments_sum_uncleared*-1 +$Deposits_and_Credits_sum_uncleared,2,'.',',')}}</h4></td>
            </tr>
            <tr class="table-secondary">
                <th colspan="2">New Transactions</th>
            </tr>

            @foreach($Checks_and_Payments_new as $item)
                @if($item->credit>0)
                    <tr>
                        <th><span id="clearedChecksCount">{{$item->description}}</span></th>
                        <td class="text-end" id="clearedChecksTotal">({{number_format($item->credit,2,'.',',')}})</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <th><h4>Checks and Payments - <span id="newChecksCount">{{$Checks_and_Payments_count_new}}</span> items</h4></th>
                <td class="text-end" id="newChecksTotal"><h4>{{number_format($Checks_and_Payments_sum_new*-1,2,'.',',')}}</h4></td>
            </tr>
            @foreach($Checks_and_Payments_new as $item)
                @if($item->debit>0)
                    <tr>
                        <th><span id="clearedChecksCount">{{$item->description}}</span></th>
                        <td class="text-end" id="clearedChecksTotal">({{number_format($item->debit,2,'.',',')}})</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <th><h4>Deposits and Credits - <span id="newDepositsCount">{{$Deposits_and_Credits_count_new}}</span> items</h4></th>
                <td class="text-end" id="newDepositsTotal"><h4>{{number_format($Deposits_and_Credits_sum_new,2,'.',',')}}</h4></td>
            </tr>
            <tr>
                <th><h4>Total New Transactions</h4></th>
                <td class="text-end fw-bold text-warning" id="totalNewTransactions"><h4>{{number_format($Checks_and_Payments_sum_new*-1 +$Deposits_and_Credits_sum_new,2,'.',',')}}</h4></td>
            </tr>
            <tr>
                <th><h4>Ending Balance</h4></th>
                <td class="text-end fw-bold text-success" id="endingBalance"><h4>{{number_format($reconciliation->endingBalance,2,'.',',')}}</h4></td>
            </tr>
            </tbody>
        </table>
    </div>



@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#downloadPdf").click(function () {
                const { jsPDF } = window.jspdf;
                let doc = new jsPDF('p', 'mm', 'a4');

                // Get the reconciliation summary container
                let element = document.querySelector(".container");

                // Hide the download button before capturing
                $("#downloadPdf").hide();

                html2canvas(element, { scale: 2 }).then(canvas => {
                    let imgData = canvas.toDataURL("image/png");
                    let imgWidth = 190; // A4 width in mm
                    let pageHeight = 400; // A4 height in mm
                    let imgHeight = (canvas.height * imgWidth) / canvas.width;

                    doc.addImage(imgData, "PNG", 10, 10, imgWidth, imgHeight);

                    // Show the button again after capturing
                    $("#downloadPdf").show();

                    // Save the PDF
                    doc.save("Reconciliation_Detail.pdf");
                });
            });
        });
    </script>


@endsection

