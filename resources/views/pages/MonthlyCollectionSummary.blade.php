@extends('layout.admin')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <style>
        .total-pending-container {
            background-color: #1A2942;
            padding: 5px;
            margin-top: 10px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            color: #ffffff !important;
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

        #overlay {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .bg-purple {
            background-color: purple;
            color: white;
        }

        .custom-hover:hover {
            background-color: darkorchid;
            color: white;
        }

        @media print {
            @page {
                size: landscape; /* Landscape orientation */
                margin: 10mm; /* Adjust margins */
            }
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
                color: #333;
            }
            .container {
                width: 100%; /* Full width */
                margin: 0;
                padding: 0;
            }
            table {
                width: 100%; /* Full width */
                border-collapse: collapse;
                margin-bottom: 20px;
                page-break-inside: auto; /* Allow page breaks inside table */
            }
            th, td {
                border: 1px solid #dee2e6;
                padding: 8px; /* Reduced padding for more content */
                text-align: left;
                overflow-wrap: break-word;
            }
            th {
                background-color: #f2f2f2;
                color: #333;
                font-weight: bold;
            }
            thead {
                background-color: #007bff;
                color: #fff;
            }
            .print-title {
                text-align: center;
                margin-bottom: 10px; /* Reduced margin */
                font-size: 18px; /* Slightly smaller font size */
                font-weight: bold;
            }
            .print-date {
                text-align: right;
                margin-bottom: 10px; /* Reduced margin */
                font-size: 12px; /* Slightly smaller font size */
                color: #666;
            }
            .page-break {
                page-break-before: always; /* Force page break */
            }
        }
    </style>
    <style>
        .table-responsive {
            overflow-x: auto;
        }

        .table th, .table td {
            white-space: nowrap;
        }
    </style>
 
    <style>
        .btn-sm {
            font-size: 0.875rem; /* Smaller font size */
            padding: 0.25rem 0.5rem; /* Reduce padding */
            border-radius: 0.2rem; /* Optional: Adjust border radius */
        }
    </style>
    <style>
        @media print {
            table {
                page-break-inside: auto; /* Allow page breaks inside the table */
            }
            tr {
                page-break-inside: avoid; /* Avoid page break inside table rows */
                page-break-after: auto; /* Allow page breaks after rows */
            }
        }
    </style>
 
@endsection

@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    </div>
                    <h4 class="page-title">Monthly Collection Summary</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <label for="center_details" class="form-label">Center</label>
                                <select class="form-control select2" id="center_details">
                                    <option value="0">All</option>
                                    @foreach($center as $item)
                                        <option value="{{ $item->idCenter }}">{{ $item->Name }}
                                            - {{ $item->Route }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label for="day" class="form-label">Day</label>
                                <select class="form-control" id="day" name="day">
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                    <option value="Sunday">Sunday</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label for="month" class="form-label">Month</label>
                                <select class="form-control" id="month" name="month">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $i == now()->month ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-lg-3">
                                <label for="year" class="form-label">Year</label>
                                <select class="form-control" id="year" name="year">
                                    @for ($i = now()->year - 20; $i <= now()->year + 20; $i++)
                                        <option value="{{ $i }}" {{ $i == now()->year ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <br><br><br><br>
                            <div class="col-lg-3 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-sm me-2" id="search_button">
                                    <i class="bi bi-search"></i> Search
                                </button>
 

                                <button type="button" class="btn btn-success btn-sm me-2" id="download_excel">
                                    <i class="bi bi-file-earmark-excel"></i> Download Excel
                                </button>

                                <button type="button" class="btn btn-info btn-sm" id="print_table">
                                    <i class="bi bi-printer"></i> Print
                                </button>
 
                            </div>


                        </div>

                        <div class="table-responsive">
                            <table id="result_table" class="table">
                                <thead>
                                <tr id="header_row" class="bg-purple">
                                    <th>Center No</th>
                                    <th>Group No</th>
                                    <th>Member No</th>
                                    <th>Member Name</th>
                                    <th>Loan Amount</th>
                                    <th>Installment</th>
                                    <th>Loan Balance</th>
                                    <th>Capital Amount</th>
                                    <th>Capital Balance</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-1 mb-1 p-2">
                            <div
                                    class="col-lg-12 d-flex align-items-center justify-content-between total-pending-container">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold">Total Collected Amount:</span>
                                    <span id="tot_Collected_amount" class="fw-bold ms-2">0.00</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold">Total Loan Amount:</span>
                                    <span id="tot_Issued_amount" class="fw-bold ms-2">0.00</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold">Total Other Charges:</span>
                                    <span id="tot_other_Charges" class="fw-bold ms-2">0.00</span>
                                </div>
                                {{-- <button class="btn btn-primary" onclick="">Print Report</button>--}}
                            </div>

                        </div>

                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div> <!-- end row -->

    </div>

@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
 
    <script type="text/javascript"
            src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.6.0/jspdf.umd.min.js"></script>

    <script>
        document.getElementById('download_excel').addEventListener('click', function() {
            var table = document.getElementById('result_table');
            var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            // Check if table has any rows in tbody
            if (rows.length === 0) {
                Swal.fire("Error!", "No data available to export.", "error");
                return;
            }

            // Prompt for file name
            var fileName = prompt('Enter the file name:', 'MonthlyCollectionSummary.xlsx');
            if (!fileName) {
                Swal.fire("Error!", "File name cannot be empty.", "error");
                return;
            }

            // Get table element
            var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});

            // Create an Excel file and trigger download
            XLSX.writeFile(wb, fileName);
        });

        document.getElementById('print_table').addEventListener('click', function() {
            var printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Print Table</title>');
            printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
            printWindow.document.write('<style>@media print { @page { size: landscape; margin: 10mm; } body { margin: 0; padding: 0; font-family: Arial, sans-serif; color: #333; } .container { width: 100%; margin: 0; padding: 0; } table { width: 100%; border-collapse: collapse; margin-bottom: 20px; page-break-inside: auto; } th, td { border: 1px solid #dee2e6; padding: 8px; text-align: left; overflow-wrap: break-word; } th { background-color: #f2f2f2; color: #333; font-weight: bold; } thead { background-color: #007bff; color: #fff; } .print-title { text-align: center; margin-bottom: 10px; font-size: 18px; font-weight: bold; } .print-date { text-align: right; margin-bottom: 10px; font-size: 12px; color: #666; } .page-break { page-break-before: always; } }</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<div class="container">');
            printWindow.document.write('<div class="print-title">Monthly Collection Summary</div>');
            printWindow.document.write('<div class="print-date">Date: ' + new Date().toLocaleDateString() + '</div>');
            printWindow.document.write(document.getElementById('result_table').outerHTML);
            printWindow.document.write('</div>'); // Close container div
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        });


    </script>
 

    <script>
        $(document).ready(function () {
            $('#search_button').click(function () {
                fix_table();
                {{--var centerId = $('#center_details').val();--}}
                {{--var day = $('#day').val();--}}
                {{--var month = $('#month').val();--}}
                {{--var year = $('#year').val();--}}

                {{--$.ajax({--}}
                {{--    url: '{{ route('generateMonthlyCollectionSummaryPDF') }}',--}}
                {{--    type: 'POST',--}}
                {{--    data: {--}}
                {{--        _token: '{{ csrf_token() }}',--}}
                {{--        center_id: centerId,--}}
                {{--        day: day,--}}
                {{--        month: month,--}}
                {{--        year: year--}}
                {{--    },--}}
                {{--    success: function (response) {--}}
                {{--        --}}
                {{--        --}}

                {{--        console.log(response);--}}

                {{--        table.clear().draw();--}}

                {{--        $.each(response.data, function (index, data) {--}}
                {{--            table.row.add([--}}
                {{--                data.center_no,--}}
                {{--                data.group_no,--}}
                {{--                data.member_no,--}}
                {{--                data.member_name,--}}
                {{--                data.loan_amount,--}}
                {{--                data.installment,--}}
                {{--                data.loan_balance,--}}
                {{--                data.capital_amount,--}}
                {{--                data.capital_balance--}}
                {{--            ]).draw(false);--}}
                {{--        });--}}

                {{--        $('#tot_Collected_amount').text(response.total_collected_amount);--}}
                {{--        $('#tot_Issued_amount').text(response.total_issued_amount);--}}
                {{--        $('#tot_other_Charges').text(response.total_other_charges);--}}
                {{--    },--}}
                {{--    error: function (xhr, status, error) {--}}
                {{--        console.error(error);--}}
                {{--    }--}}
                {{--});--}}
            });
        });



        function updateSummaryValues() {
            $.ajax({
                type: "GET",
                url: `/MonthlyCollectionSummary`, // URL to get summary data
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        $('#tot_Collected_amount').text(formatNumber(data.totalCollectedAmount));
                        $('#tot_Issued_amount').text(formatNumber(data.totalIssuedAmount));
                        $('#tot_other_Charges').text(formatNumber(data.totalOtherCharges));
                    } else {
                        Swal.fire("Error!", "Failed to load summary data!", "error");
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });
        }
        function fix_table() {
            var month = $('#month').val();
            var year = $('#year').val();
            var day = $('#day').val();

            if (month && year && day) {
                // Get dates of the selected day in the selected month
                var dates = getDatesOfSelectedDayInMonth(day, month, year);

                // Get table header row
                var headerRow = $('#header_row');

                // Clear existing dynamic columns (except the initial columns)
                clearDynamicColumns(headerRow);

                // Re-add static headers including new columns
                var staticHeaders = `
            <th>Center No</th>
            <th>Group No</th>
            <th>Member No</th>
            <th>Member Name</th>
            <th>Loan Amount</th>
            <th>Installment</th>
            <th>Loan Balance</th>
            <th>Capital Amount</th>
            <th>Capital Balance</th>
        `;
                headerRow.html(staticHeaders);

                // Append new headers for each date
                dates.forEach(date => {
                    headerRow.append(`<th>${date}</th>`);
                    headerRow.append('<th>Paid Amount</th>');
                });

                // Load table data via AJAX
                loadTableData(month, year, day, dates);
            } else {
                console.error('Month, year, or day value is missing.');
            }
        }

        function getDatesOfSelectedDayInMonth(day, month, year) {
            var daysOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            var selectedDayIndex = daysOfWeek.indexOf(day);
            var dates = [];
            var date = new Date(year, month - 1, 1);

            while (date.getMonth() === month - 1) {
                if (date.getDay() === selectedDayIndex) {
                    dates.push(formatDate(date));
                }
                date.setDate(date.getDate() + 1);
            }

            return dates;
        }

        function formatDate(date) {
            var day = date.getDate();
            var month = date.getMonth() + 1; // Months are zero-based
            var year = date.getFullYear();
            return `${year}-${month < 10 ? '0' : ''}${month}-${day < 10 ? '0' : ''}${day}`;
        }

        function clearDynamicColumns(headerRow) {
            // Get the index of the first dynamic column to clear
            var startIndex = 9; // Adjust this index based on the initial fixed columns

            // Remove headers from the startIndex onwards
            headerRow.children('th').slice(startIndex).remove();
        }



        function formatNumber(value) {
            const number = parseFloat(value);
            if (isNaN(number)) {
                return '0.00'; // Return a default value if not a valid number
            }
            return new Intl.NumberFormat('en-US', {
                style: 'decimal',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(number);
        }
    </script>


    <script>
        function loadTableData(month, year, day, dates) {
            let center = $("#center_details").val();
            $.ajax({
                type: "GET",
                url: "/MonthlyCollectionSummary/"+center,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {

                    if (xhr.status === 200) {
                        console.log(data);

                        // Clear the existing rows
                        $('#result_table tbody').empty();

                        // Flag to check if at least one row is added
                        let rowAdded = false;

                        // Iterate over the data and create new rows
                        data.items.forEach(function (item) {
                            // Check if there are any payments
                            let hasInstallments = dates.some(date => item.payments[date] !== undefined);

                            if (hasInstallments) {
                                rowAdded = true;
                                var newRow = `<tr>
                            <td>${item.centerNo}</td>
                            <td>${item.groupNo}</td>
                            <td>${item.memberNo}</td>
                            <td>${item.memberName}</td>
                            <td>${formatNumber(item.loanAmount)}</td>
                            <td>${formatNumber(item.installment)}</td>
                            <td>${formatNumber(item.loanBalance)}</td>
                            <td>${formatNumber(item.capitalAmount)}</td>
                            <td>${formatNumber(item.capitalBalance)}</td>`;

                                // Iterate over the dates to populate payment details
                                dates.forEach(date => {
                                    let payment = item.payments[date] || ''; // Default to '' if no payment exists
                                    let payid = item.payids[date] || ''; // Default to '' if no payid exists
                                    let payBalance = item.pay_Balances[date] || ''; // Default to '' if no pay_Balance exists

                                    if (payid === '0.00') {
                                        payid = ' ';
                                    }

                                    if (payBalance !== '0.00') {
                                        payid = ' ';
                                    }

                                    // Append the payment details to the row
                                    newRow += `<td>${payBalance}</td><td>${payid}</td>`;
                                });

                                newRow += `</tr>`;
                                $('#result_table tbody').append(newRow);
                            }
                        });

                        // Optionally, show a message if no rows were added
                        if (!rowAdded) {
                            $('#result_table tbody').append('<tr><td colspan="10" class="text-center">No installments available for the selected filters.</td></tr>');
                        }
                    } else {
                        Swal.fire("Error!", "Failed to load data!", "error");
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });
        }
    </script>


@endsection
