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
@endsection

@section('content')
    <div class="container mt-4">
        <h3 class="text-center">Reconciliation Summary</h3>
        <h5 class="text-center">Bank {{$reconciliation->Bank_Name}}, Period <span id="summaryEndDate">{{$reconciliation->date}},User - {{$reconciliation->Full_Name}}</span></h5>
        <button id="downloadPdf" class="btn btn-primary mb-3">Download PDF</button>


        <table class="table table-bordered mt-3">
            <tbody>
            <tr>
                <th>Beginning Balance</th>
                <td class="text-end" id="beginningBalance">{{number_format($reconciliation->balance,2,'.',',')}}</td>
            </tr>
            <tr class="table-secondary">
                <th colspan="2">Cleared Transactions</th>
            </tr>
            <tr>
                <td>Checks and Payments - <span id="clearedChecksCount">{{$Checks_and_Payments_count}}</span> items</td>
                <td class="text-end" id="clearedChecksTotal">{{number_format($Checks_and_Payments_sum*-1,2,'.',',')}}</td>
            </tr>
            <tr>
                <td>Deposits and Credits - <span id="clearedDepositsCount">{{$Deposits_and_Credits_count}}</span> items</td>
                <td class="text-end" id="clearedDepositsTotal">{{number_format($Deposits_and_Credits_sum,2,'.',',')}}</td>
            </tr>
            <tr>
                <th>Total Cleared Transactions</th>
                <td class="text-end fw-bold" id="totalClearedTransactions">{{number_format($Checks_and_Payments_sum*-1 +$Deposits_and_Credits_sum,2,'.',',')}}</td>
            </tr>
            <tr>
                <th>Cleared Balance</th>
                <td class="text-end fw-bold text-primary" id="clearedBalance">{{number_format(($Checks_and_Payments_sum*-1 +$Deposits_and_Credits_sum)+$reconciliation->balance,2,'.',',')}}</td>
            </tr>
            <tr class="table-secondary">
                <th colspan="2">Uncleared Transactions</th>
            </tr>
            <tr>
                <td>Checks and Payments - <span id="unclearedChecksCount">{{$Checks_and_Payments_count_uncleared}}</span> items</td>
                <td class="text-end" id="unclearedChecksTotal">{{number_format($Checks_and_Payments_sum_uncleared*-1,2,'.',',')}}</td>
            </tr>
            <tr>
                <td>Deposits and Credits - <span id="unclearedDepositsCount">{{$Deposits_and_Credits_count_uncleared}}</span> item</td>
                <td class="text-end" id="unclearedDepositsTotal">{{number_format($Deposits_and_Credits_sum_uncleared,2,'.',',')}}</td>
            </tr>
            <tr>
                <th>Total Uncleared Transactions</th>
                <td class="text-end fw-bold text-danger" id="totalUnclearedTransactions">{{number_format($Checks_and_Payments_sum_uncleared*-1 +$Deposits_and_Credits_sum_uncleared,2,'.',',')}}</td>
            </tr>
            <tr>
                <th>Register Balance</th>
                <td class="text-end fw-bold text-primary" id="registerBalance">{{number_format((($Checks_and_Payments_sum*-1 +$Deposits_and_Credits_sum)+$reconciliation->balance)+$Checks_and_Payments_sum_uncleared*-1 +$Deposits_and_Credits_sum_uncleared,2,'.',',')}}</td>
            </tr>
            <tr class="table-secondary">
                <th colspan="2">New Transactions</th>
            </tr>
            <tr>
                <td>Checks and Payments - <span id="newChecksCount">{{$Checks_and_Payments_count_new}}</span> items</td>
                <td class="text-end" id="newChecksTotal">{{number_format($Checks_and_Payments_sum_new*-1,2,'.',',')}}</td>
            </tr>
            <tr>
                <td>Deposits and Credits - <span id="newDepositsCount">{{$Deposits_and_Credits_count_new}}</span> items</td>
                <td class="text-end" id="newDepositsTotal">{{number_format($Deposits_and_Credits_sum_new,2,'.',',')}}</td>
            </tr>
            <tr>
                <th>Total New Transactions</th>
                <td class="text-end fw-bold text-warning" id="totalNewTransactions">{{number_format($Checks_and_Payments_sum_new*-1 +$Deposits_and_Credits_sum_new,2,'.',',')}}</td>
            </tr>
            <tr>
                <th>Ending Balance</th>
                <td class="text-end fw-bold text-success" id="endingBalance">{{number_format($reconciliation->endingBalance,2,'.',',')}}</td>
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
                    let pageHeight = 280; // A4 height in mm
                    let imgHeight = (canvas.height * imgWidth) / canvas.width;

                    doc.addImage(imgData, "PNG", 10, 10, imgWidth, imgHeight);

                    // Show the button again after capturing
                    $("#downloadPdf").show();

                    // Save the PDF
                    doc.save("Reconciliation_Summary.pdf");
                });
            });
        });
    </script>


@endsection

