@extends('layout.admin')

@section('head')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css">


    <style>
        thead {
            background-color: #d9edf7; /* Light blue color */
            color: #31708f; /* Darker blue text for contrast */
        }
        .table-responsive-sm {
            /* Optional: Add some padding to the table container for better visuals */
            padding: 10px;
        }

        /*#loan_table {*/
        /*    !* Optional: Set a smaller font size if needed *!*/
        /*    font-size: 0.875rem; !* Example: 14px *!*/
        /*}*/

        #loan_table th, #loan_table td {
            /* Reduce padding to decrease row height */
            padding: 4px 8px;
            /* Set a smaller line height */
            line-height: 1.2;
        }
        .bulb-icon {
            color: #ffcc00; /* Example color for the bulb icon */
        }

        .fa-lightbulb {
            font-size: 1.5rem; /* Adjust size as needed */
        }

        .bg-purple th {
            color: #e1e1e1 !important;
        }

        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;

        }
        /*#loan_table tr, #loan_table th, #loan_table td {*/
        /*    margin: 0 !important;*/
        /*    padding: 10px !important; !* Adjust padding to your preference *!*/
        /*}*/

    </style>

    <style>
        .status-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 10px;
            background-color: #f4f4f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .status-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 1rem;
            color: #333;
        }
        .status-item i {
            font-size: 1.5rem;
        }
        .table-responsive-sm {
            overflow-x: auto;
        }

        table.dataTable {
            width: 100%;  /* Ensures table uses full width */
        }

        @media (max-width: 768px) {
            /* Adjust table column visibility or width as needed */
            #loan_table {
                font-size: 12px;  /* Smaller font for smaller screens */
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
                    <h4 class="page-title">Savings Report Overview</h4>
                </div>
            </div>
            <span style="color: #a19595">"The Savings Report provides a detailed record of the savings contributions made by customers over a specified period. It allows you to track the savings details for each center, group, and customer, providing an overview of how much has been saved by each individual."</span>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3" hidden>
                                <div class="mb-2"> <!-- Reduced bottom margin -->
                                    <label for="route" class="form-label">Route</label>
                                    <select class="form-control select2" id="route">
                                        <option value="0" selected>All</option>

                                    </select>
                                </div>
                            </div>


                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Center</label>
                                    <select class="form-control select2" id="center_details">
                                        <option value="0">All</option>
                                        @foreach($center as $item)
                                            <option value="{{$item->idCenter}}">{{ $item->Name }}-{{ $item->Route }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Group</label>
                                    <select class="form-control select2" id="group">
                                        <option value="0">All</option>
                                        @foreach($group as $item)
                                            <option value="{{$item->idCustomer_Group}}">{{ $item->Group_No }}-{{ $item->Name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Customer</label>
                                    <select class="form-control select2" id="customer_id">
                                        <option value="0">All</option>
                                        @foreach($customers as $item)
                                            <option value="{{$item->idCustomer}}">{{ $item->First_Name }} {{$item->Last_Name}}-{{ $item->Nic }}-{{ $item->Contact_No }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Lending Officer</label>
                                    <select class="form-control select2" id="lending">
                                        <option value="0">All</option>
                                        @foreach($lending_officer as $item)
                                            <option value="{{ $item->id }}">{{ $item->Full_Name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="recovery" class="form-label">Date From</label>
                                    <input type="date" class="form-control" id="date_from">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="recovery" class="form-label">Date To</label>
                                    <input type="date" class="form-control" id="date_to">
                                </div>
                            </div>

                            <br>
                            <div class="col-lg-3">
                                <div class="mb-3">

                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger" onclick="load_payment_table();"><i class="bi bi-search"></i> </button>

                        <button id="export_excel" class="btn btn-success ms-2"><i class="fas fa-file-excel"></i> Excel</button>
                        <button id="export_pdf" class="btn btn-primary ms-2"><i class="fas fa-file-pdf"></i> PDF</button>


                        <hr>
                        <div class="table-responsive-sm">
                            <table class="table table-centered mb-0" id="loan_table">
                                <thead class="sticky-top bg-purple">
                                <tr>
                                    <th>Center No</th>
                                    <th>Group No</th>
                                    <th>Member NIC</th>
                                    <th>Member Name</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->

                        <div class="row mt-1 mb-1 p-2">
                            <div class="col-md-8 row">
                                <div class="col-sm-3">
                                    <div>
                                        <span class="fw-bold">Total Saving Amount</span>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div>
                                        <span id="tot_amount">0.00</span>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div> <!-- end card-->
                </div> <!-- end col -->


            </div>
            <!-- end row -->




        </div> <!-- container -->

    </div>

@endsection

@section('script')
    <!-- Core jQuery (must come first) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- DataTables Core -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <!-- DataTables Extensions -->
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <!-- PDF & Excel Support -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>

    <script src="../JS/validate.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

    <!-- XLSX for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <!-- jsPDF for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

    {{--    <script src="../JS/today_payment.js"></script>--}}
    <script>
        $(function() {
            load_payment_table();
            let x = ["#payment_amount"];
            decimalFormat(x);
            $('.select2').select2()

            let table = $('#loan_table').DataTable({
                responsive: true,
                order: [[0, 'desc']]
            });
        });

        $(document).ready(function () {
            $('#export_excel').on('click', function () {
                let table = $('#loan_table').DataTable();

                // Show all rows temporarily
                table.page.len(-1).draw();

                setTimeout(() => {
                    // Export entire visible table
                    const tableClone = document.getElementById("loan_table").cloneNode(true);
                    const totalRow = tableClone.insertRow(-1);
                    totalRow.innerHTML = `<td colspan="4" style="font-weight:bold">Total Saving Amount</td><td>${document.getElementById("tot_amount").innerText}</td>`;

                    const wb = XLSX.utils.table_to_book(tableClone, {sheet: "Savings Report"});
                    XLSX.writeFile(wb, "savings_report.xlsx");

                    // Reset pagination back to original (10 rows)
                    table.page.len(10).draw();
                }, 500); // Give it time to render full rows
            });


            $('#export_pdf').on('click', function () {
                let table = $('#loan_table').DataTable();

                // Show all rows temporarily
                table.page.len(-1).draw();

                setTimeout(() => {
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF('landscape');

                    doc.setFontSize(14);
                    doc.text("Savings Report", 14, 14);

                    const head = [["Center No", "Group No", "Member NIC", "Member Name", "Amount"]];
                    const body = [];

                    $("#loan_table tbody tr").each(function () {
                        const row = [];
                        $(this).find("td").each(function () {
                            row.push($(this).text().trim());
                        });
                        if (row.length > 0) {
                            body.push(row);
                        }
                    });

                    // Add total row
                    body.push([
                        { content: "Total Saving Amount", colSpan: 4, styles: { halign: 'right', fontStyle: 'bold' } },
                        document.getElementById("tot_amount").innerText
                    ]);

                    doc.autoTable({
                        head: head,
                        body: body,
                        startY: 20,
                        styles: { fontSize: 9, cellPadding: 2 },
                        headStyles: { fillColor: [26, 41, 66] }
                    });

                    doc.save('savings_report.pdf');

                    // Reset pagination back to 10 rows
                    table.page.len(10).draw();
                }, 500);
            });

        });



        function load_payment_table() {
            let center_details = $("#center_details").val();
            let route = $("#route").val();
            let group = $("#group").val();
            let customer = $("#customer_id").val();
            let lending = $("#lending").val();
            let date_from = $("#date_from").val();
            let date_to = $("#date_to").val();

            $.ajax({
                type: "POST",
                url: `/savings_report_filter`,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    center_details: center_details,
                    route: route,
                    group: group,
                    customer: customer,
                    lending: lending,
                    date_from: date_from,

                    date_to: date_to
                },
                success: function(data, textStatus, xhr) {
                    console.log(data);
                    let table = $('#loan_table').DataTable();

                    // Clear existing rows and redraw the table
                    table.clear().draw(); // Clear the table and redraw

                    if (xhr.status === 200) {
                        // Check if there are items in the data
                        if (data.item.length > 0) {
                            let currentPage = data.item.current_page;
                            let lastPage = data.item.last_page;
                            let tot = 0.0;

                            // Loop through each item in the response data
                            data.item.forEach(function(item) {


                                if(parseFloat(item.saving_amount)>0){
                                    // Calculate total balance
                                    let totalBalance = parseFloat(item.saving_amount);
                                    tot += totalBalance;

                                    table.row.add([
                                        item.center_no+"-"+item.center_name,
                                        item.group_name,
                                        item.member_nic,
                                        item.member_name,
                                        `<td style="padding: 5px;">${formatNumber(parseFloat(item.saving_amount))}</td>`,
                                    ]);
                                }


                            });

                            // Draw the table with new data
                            table.draw();

                            // Update total amount
                            $("#tot_amount").text(tot.toFixed(2));

                        } else {
                            // Handle empty data case
                            $("#tot_amount").text("0.00"); // Reset total amount if no items
                            // Optionally draw the empty table
                            table.draw(); // Ensure the table reflects the cleared state
                        }
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });
        }



        function formatNumber(num) {
            return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

    </script>
    <script>
        $('#branch').change(function () {
            let branch_id = $(this).val();

            $.ajax({
                url: '/get_branch_data',
                type: 'POST',
                data: {
                    branch_id: branch_id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    // Update center dropdown
                    let centerDropdown = $('#center_details');
                    centerDropdown.empty().append(`<option value="0">All</option>`);
                    data.center.forEach(function (item) {
                        centerDropdown.append(`<option value="${item.idCenter}">${item.Name} - ${item.Route}</option>`);
                    });

                    // Update group dropdown
                    let groupDropdown = $('#group');
                    groupDropdown.empty().append(`<option value="0">All</option>`);
                    data.group.forEach(function (item) {
                        groupDropdown.append(`<option value="${item.idCustomer_Group}">${item.Group_No} - ${item.Name}</option>`);
                    });

                    // Update customer dropdown
                    let customerDropdown = $('#customer_id');
                    customerDropdown.empty().append(`<option value="0">All</option>`);
                    data.customers.forEach(function (item) {
                        customerDropdown.append(`<option value="${item.idCustomer}">${item.First_Name} ${item.Last_Name} - ${item.Nic} - ${item.Contact_No}</option>`);
                    });

                    // Update lending officer dropdown
                    let lendingDropdown = $('#lending');
                    lendingDropdown.empty().append(`<option value="0">All</option>`);
                    data.lending_officer.forEach(function (item) {
                        lendingDropdown.append(`<option value="${item.id}">${item.Full_Name}</option>`);
                    });

                    // Update route dropdown (if visible)
                    let routeDropdown = $('#route');
                    routeDropdown.empty().append(`<option value="0">All</option>`);
                    data.route.forEach(function (item) {
                        routeDropdown.append(`<option value="${item.id_route}">${item.Route_Name}</option>`);
                    });

                    // Reinitialize Select2 after content change
                    $('.select2').select2();
                },
                error: function (xhr) {
                    console.error('Failed to load branch related data:', xhr);
                }
            });
        });
    </script>


@endsection

