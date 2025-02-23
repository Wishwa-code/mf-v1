@extends('layout.admin')

@section('head')
<!-- Include Datepicker CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;

    }

    .main-row {
        border-bottom: 2px solid #000;
        text-align: right;

    }

    .sub-row {
        background-color: #ffffff;
        text-align: right;
    }

    .mhead {
        text-align: left;
    }

    th,
    td {
        padding: 8px 12px;
        font-weight: bold;
        border: 1px solid #ccc;
    }

    th {
        background-color: #000f3b;
        /* Highlight table head with yellow */
        color: #f3f3f3;
        font-weight: bold;
    }
</style>
@endsection

@section('content')
<div class="page-content">

    <div class="container-fluid">
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="page-title">Loan Status</h6>
                </div>
            </div>
        </div>


        <div class="row">
            <form action="{{ route('profitreport.loanStatus') }}" method="POST" class="row g-2"> <!-- Smaller gap between rows -->
                @csrf
                <div class="col-lg-3">
                    <div class="mb-1"> <!-- Smaller bottom margin -->
                        <label for="route" class="form-label">Route</label>
                        <select class="form-control form-control-sm select2" id="route" name="route">
                            <option value="0" {{ old('route', $selectedRoute ?? 0) == 0 ? 'selected' : '' }}>All</option>
                            @foreach($route as $item)
                                <option value="{{ $item->id_route }}" {{ old('route', $selectedRoute ?? 0) == $item->id_route ? 'selected' : '' }}>
                                    {{ $item->name }} - {{ $item->Full_Name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="mb-1">
                        <label for="center_details" class="form-label">Center</label>
                        <select class="form-control form-control-sm select2" id="center_details" name="center_details">
                            <option value="0" {{ old('center_details', $selectedCenter ?? 0) == 0 ? 'selected' : '' }}>All</option>
                            @foreach($center as $item)
                                <option value="{{ $item->idCenter }}" {{ old('center_details', $selectedCenter ?? 0) == $item->idCenter ? 'selected' : '' }}>
                                    {{ $item->Name }}-{{ $item->Route }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="mb-1">
                        <label for="group" class="form-label">Group</label>
                        <select class="form-control form-control-sm select2" id="group" name="group">
                            <option value="0" {{ old('group', $selectedGroup ?? 0) == 0 ? 'selected' : '' }}>All</option>
                            @foreach($group as $item)
                                <option value="{{ $item->idCustomer_Group }}" {{ old('group', $selectedGroup ?? 0) == $item->idCustomer_Group ? 'selected' : '' }}>
                                    {{ $item->Group_No }}-{{ $item->Name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="mb-1">
                        <label for="customer" class="form-label">Customer</label>
                        <select class="form-control form-control-sm select2" id="customer_details" name="customer">
                            <option value="0" {{ old('customer', $selectedCustomer ?? 0) == 0 ? 'selected' : '' }}>All</option>
                            @foreach($customer as $item)
                                <option value="{{ $item->idCustomer }}" {{ old('customer', $selectedCustomer ?? 0) == $item->idCustomer ? 'selected' : '' }}>
                                    {{ $item->First_Name }} {{ $item->Last_Name }} - {{ $item->Contact_No }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="mb-1">
                        <label for="lending" class="form-label">Lending Officer</label>
                        <select class="form-control form-control-sm select2" id="lending" name="lending">
                            <option value="0" {{ old('lending', $selectedLendingOfficer ?? 0) == 0 ? 'selected' : '' }}>All</option>
                            @foreach($lending_officer as $item)
                                <option value="{{ $item->id }}" {{ old('lending', $selectedLendingOfficer ?? 0) == $item->id ? 'selected' : '' }}>
                                    {{ $item->Full_Name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="mb-1">
                        <label for="recovery" class="form-label">Recovery Officer</label>
                        <select class="form-control form-control-sm select2" id="recovery" name="recovery">
                            <option value="0" {{ old('recovery', $selectedRecoveryOfficer ?? 0) == 0 ? 'selected' : '' }}>All</option>
                            @foreach($recovery_officer as $item)
                                <option value="{{ $item->id }}" {{ old('recovery', $selectedRecoveryOfficer ?? 0) == $item->id ? 'selected' : '' }}>
                                    {{ $item->Full_Name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 d-flex align-items-end"> <!-- Align button to the bottom -->
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>
        </div>


        <button onclick="exportTableToPDF()" class="btn btn-warning">Download PDF</button>
        <button onclick="exportTableToExcel()" class="btn btn-success">Download Excel</button>



        <table>
            <thead>
                <tr>
                    <th>Loan Status</th>
                    <th></th>
                    <th>Principal</th>
                    <th>Interst</th>
{{--                    <th>Fees</th>--}}
                    <th>Penalty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <!-- Row 1 with 3 sub-rows -->
                <tr class="sub-row">
                    <td rowspan="3" class="mhead">Current loans</td>
                    <td class="text-danger">Gross Loan Amount</td>
                    <td>{{ number_format(round($current_loan_capital_amount), 2) }}</td>
                    <td>{{ number_format(round($current_loan_interest_amount), 2) }}</td>
                    <td>{{ number_format(round($current_loan_panelty_amount), 2) }}</td>
                    <td class="text-danger">{{ number_format(round($current_loan_total), 2) }}</td>
                </tr>
                <tr class="sub-row">
                    <td class="text-success">Paid Amount</td>
                    <td>{{ number_format(round($Capital_Payment), 2) }}</td>
                    <td>{{ number_format(round($Interest_Payment), 2) }}</td>
                    <td>{{ number_format(round($Panelty_Payment), 2) }}</td>
                    <td class="text-success">{{ number_format(round($current_loan_total_payment), 2) }}</td>
                </tr>
                <tr class="main-row">
                    <td class="text-dark">Net Loan Balance</td>
                    <td>{{ number_format(round($current_loan_capital_amount - $Capital_Payment), 2) }}</td>
                    <td>{{ number_format(round($current_loan_interest_amount - $Interest_Payment), 2) }}</td>
                    <td>{{ number_format(round($current_loan_panelty_amount - $Panelty_Payment), 2) }}</td>
                    <td class="text-dark">{{ number_format(round(($current_loan_capital_amount + $current_loan_interest_amount + $current_loan_panelty_amount)
        - ($Capital_Payment + $Interest_Payment + $Panelty_Payment))
        ,
        2
    ) }}</td>
                </tr>


                <tr class="sub-row">
                    <td rowspan="3" class="mhead">Past maturity loans(Arrears)</td>
                    <td class="text-danger">Gross Loan Amount</td>
                    <td>{{ number_format(round($past_capital_amount), 2) }}</td>
                    <td>{{ number_format(round($past_loan_interest_amount), 2) }}</td>
                    <td>{{ number_format(round($past_panelty_amount), 2) }}</td>
                    <td class="text-danger">{{ number_format(round($past_total), 2) }}</td>
                </tr>
                <tr class="sub-row">
                    <td class="text-success">Paid Amount</td>
                    <td>{{ number_format(round($past_Capital_Payment), 2) }}</td>
                    <td>{{ number_format(round($past_Interest_Payment), 2) }}</td>
                    <td>{{ number_format(round($past_Panelty_Payment), 2) }}</td>
                    <td class="text-success">{{ number_format(round($past_total_payment), 2) }}</td>
                </tr>
                <tr class="main-row">
                    <td class="text-dark">Net Loan Balance</td>
                    <td>{{ number_format(round($past_capital_amount - $past_Capital_Payment), 2) }}</td>
                    <td>{{ number_format(round($past_loan_interest_amount - $past_Interest_Payment), 2) }}</td>
                    <td>{{ number_format(round($past_panelty_amount - $past_Panelty_Payment), 2) }}</td>
                    <td class="text-dark">{{ number_format(round(($past_capital_amount + $past_loan_interest_amount + $past_panelty_amount)
        - ($past_Capital_Payment + $past_Interest_Payment + $past_Panelty_Payment))
        ,
        2
    ) }}</td>
                </tr>


                <!-- Row 4 with 3 sub-rows -->
                <tr class="sub-row">
                    <td rowspan="3" class="mhead">Settled loans</td>
                    <td class="text-danger">Gross Loan Amount</td>
                    <td>{{ number_format(round($fully_paid_capital_amount), 2) }}</td>
                    <td>{{ number_format(round($fully_paid_interest_amount), 2) }}</td>
                    <td>{{ number_format(round($fully_paid_panelty_amount), 2) }}</td>
                    <td class="text-danger">{{ number_format(round($fully_paid_total), 2) }}</td>
                </tr>
                <tr class="sub-row">
                    <td class="text-success">Paid Amount</td>
                    <td>{{ number_format(round($fully_paid_Capital_Payment), 2) }}</td>
                    <td>{{ number_format(round($fully_paid_Interest_Payment), 2) }}</td>
                    <td>{{ number_format(round($fully_paid_Panelty_Payment), 2) }}</td>
                    <td class="text-success">{{ number_format(round($fully_paid_total_payment), 2) }}</td>
                </tr>
                <tr class="main-row">
                    <td class="text-dark">Net Loan Balance</td>
                    <td>{{ number_format(round($fully_paid_capital_amount - $fully_paid_Capital_Payment), 2) }}</td>
                    <td>{{ number_format(round($fully_paid_interest_amount - $fully_paid_Interest_Payment), 2) }}</td>
                    <td>{{ number_format(round($fully_paid_panelty_amount - $fully_paid_Panelty_Payment), 2) }}</td>
                    <td class="text-dark">{{ number_format(round(($fully_paid_capital_amount + $fully_paid_interest_amount + $fully_paid_panelty_amount)
        - ($fully_paid_Capital_Payment + $fully_paid_Interest_Payment + $fully_paid_Panelty_Payment))
        ,
        2
    ) }}</td>
                </tr>

                <!-- Row 4 with 3 sub-rows -->


                <!-- Row 4 with 3 sub-rows -->
{{--                <tr class="sub-row">--}}
{{--                    <td rowspan="3" class="mhead">Resheduled loans</td>--}}
{{--                    <td class="text-danger">Gross Loan Amount</td>--}}
{{--                    <td>0</td>--}}
{{--                    <td>0</td>--}}
{{--                    <td>0</td>--}}
{{--                    <td class="text-danger">0</td>--}}
{{--                </tr>--}}
{{--                <tr class="sub-row">--}}
{{--                    <td class="text-success">Paid Amount</td>--}}
{{--                    <td>0</td>--}}
{{--                    <td>0</td>--}}
{{--                    <td>0</td>--}}
{{--                    <td class="text-success">0</td>--}}
{{--                </tr>--}}
{{--                <tr class="main-row">--}}
{{--                    <td class="text-dark">Net Laon Balance</td>--}}
{{--                    <td>0</td>--}}
{{--                    <td>0</td>--}}
{{--                    <td>0</td>--}}
{{--                    <td class="text-dark">0</td>--}}
{{--                </tr>--}}

            </tbody>
        </table>


        @include('component.footer')
    </div>
    @endsection

    @section('script')
    <!-- Include Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.14/jspdf.plugin.autotable.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.20/jspdf.plugin.autotable.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

    <script>
        function exportTableToPDF() {
            const { jsPDF } = window.jspdf;

            // Get the selected values from the dropdowns
            const selectedRoute = document.getElementById('route').options[document.getElementById('route').selectedIndex].text;
            const selectedCenter = document.getElementById('center_details').options[document.getElementById('center_details').selectedIndex].text;
            const selectedGroup = document.getElementById('group').options[document.getElementById('group').selectedIndex].text;
            const selectedCustomer = document.getElementById('customer_details').options[document.getElementById('customer_details').selectedIndex].text;
            const selectedLendingOfficer = document.getElementById('lending').options[document.getElementById('lending').selectedIndex].text;
            const selectedRecoveryOfficer = document.getElementById('recovery').options[document.getElementById('recovery').selectedIndex].text;

            // Create a new jsPDF instance in landscape mode
            var doc = new jsPDF('landscape');

            // Center the title on the page
            var pageWidth = doc.internal.pageSize.getWidth();
            var title = "Loan Status Report";
            var titleX = (pageWidth - doc.getTextWidth(title)) / 2;
            doc.setFontSize(16);
            doc.text(title, titleX, 20);

            // Set font size for the details
            doc.setFontSize(12);

            // Define fixed positions for the columns (three columns per row)
            const colWidth = (pageWidth - 20) / 3;  // Divide page width by 3 for three equal columns
            const firstColX = 10;
            const secondColX = firstColX + colWidth;
            const thirdColX = secondColX + colWidth;

            // Define the row positions
            const row1Y = 40;
            const row2Y = row1Y + 10;

            // Row 1: Route, Center, Group
            doc.text("Route:", firstColX, row1Y);
            doc.text(selectedRoute, firstColX + 30, row1Y);  // Adjust spacing after the label

            doc.text("Center:", secondColX, row1Y);
            doc.text(selectedCenter, secondColX + 30, row1Y);

            doc.text("Group:", thirdColX, row1Y);
            doc.text(selectedGroup, thirdColX + 40, row1Y);

            // Row 2: Customer, Lending Officer, Recovery Officer
            doc.text("Customer:", firstColX, row2Y);
            doc.text(selectedCustomer, firstColX + 30, row2Y);

            doc.text("Lending Officer:", secondColX, row2Y);
            doc.text(selectedLendingOfficer, secondColX + 30, row2Y);

            doc.text("Recovery Officer:", thirdColX, row2Y);
            doc.text(selectedRecoveryOfficer, thirdColX + 40, row2Y);

            // Add the table after the details
            doc.autoTable({
                html: 'table',
                startY: row2Y + 20,
                theme: 'grid',  // Optional, customize the table theme
                headStyles: { fillColor: [40, 167, 69] },  // Optional, customize header color
                margin: { top: 30 }
            });

            // Save the PDF in landscape mode
            doc.save('loan-status.pdf');
        }





        function exportTableToExcel() {
            // Get the selected values from the dropdowns
            const selectedRoute = document.getElementById('route').options[document.getElementById('route').selectedIndex].text;
            const selectedCenter = document.getElementById('center_details').options[document.getElementById('center_details').selectedIndex].text;
            const selectedGroup = document.getElementById('group').options[document.getElementById('group').selectedIndex].text;
            const selectedCustomer = document.getElementById('customer_details').options[document.getElementById('customer_details').selectedIndex].text;
            const selectedLendingOfficer = document.getElementById('lending').options[document.getElementById('lending').selectedIndex].text;
            const selectedRecoveryOfficer = document.getElementById('recovery').options[document.getElementById('recovery').selectedIndex].text;

            // Create a workbook
            var wb = XLSX.utils.table_to_book(document.querySelector('table'), { sheet: "Sheet1" });

            // Add a new sheet for selected values
            var ws_data = [
                ["Route", selectedRoute],
                ["Center", selectedCenter],
                ["Group", selectedGroup],
                ["Customer", selectedCustomer],
                ["Lending Officer", selectedLendingOfficer],
                ["Recovery Officer", selectedRecoveryOfficer]
            ];

            var ws = XLSX.utils.aoa_to_sheet(ws_data);
            XLSX.utils.book_append_sheet(wb, ws, "Selected Values");

            // Save the Excel file
            XLSX.writeFile(wb, 'loan-status.xlsx');
        }



    </script>
    @endsection