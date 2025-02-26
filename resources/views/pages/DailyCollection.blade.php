@extends('layout.admin')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

    <style>


        .total-pending-container {
            background-color: #1A2942; /* Light background color */
            padding: 5px; /* Padding around the container */
            margin-top: 10px; /* Top margin */
            border: 1px solid #dee2e6; /* Border color */
            border-radius: 5px; /* Rounded corners */
            color: #ffffff !important;

        }

        thead {
            background-color: #d9edf7; /* Light blue color */
            color: #31708f; /* Darker blue text for contrast */
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


        .bg-purple th {
            color: #e1e1e1 !important; /* Ensure white text color for th elements */
        }

        .bg-purple {
            background-color: #1A2942 !important; /* Purple color */
            color: white !important; /* White text color */
        }

        .modal_2 {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal_2-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #ccc;
            width: 90%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .close_2 {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close_2:hover,
        .close_2:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .printer-design {
            text-align: center;
        }

        .receipt {
            font-family: 'Arial', sans-serif;
            text-align: left;
            margin: 0;
        }

        .receipt .header {
            text-align: center;
        }

        .receipt .logo {
            width: 80px;
            margin: 0 auto 10px;
        }

        .receipt h1, .receipt h2 {
            margin: 5px 0;
        }

        .receipt p {
            margin: 5px 0;
            line-height: 1.5;
        }

        .receipt .details p {
            margin: 3px 0;
        }

        .receipt .payment-info {
            margin: 10px 0;
        }

        .receipt .payment-info .item {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }

        .receipt .payment-info .description {
            font-weight: bold;
        }

        .receipt .payment-info .amount {
            text-align: right;
        }

        .receipt hr {
            border: 0;
            border-top: 1px dashed #ddd;
            margin: 10px 0;
        }

        .receipt .totals p {
            margin: 5px 0;
            font-weight: bold;
        }

        .receipt .signature {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin: 20px 0;
        }

        .receipt .signature-line {
            width: 100%;
            text-align: center;
            margin-bottom: 5px;
        }

        .receipt .thank-you {
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
        }

        .bg-purple th {
            color: #e1e1e1 !important; /* Ensure white text color for th elements */
        }


        .bg-purple {
            background-color: #1A2942 !important; /* Purple color */
            color: white !important; /* White text color */
        }


        /* Print styles */
        @media print {
            body * {
                visibility: hidden;
            }

            .printer-design, .printer-design * {
                visibility: visible;
            }

            .printer-design {
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm; /* 80mm width for thermal printer */
                background: white;
            }

            .modal-content {
                width: 80mm; /* Ensures the modal content fits the thermal printer paper */
                border: none; /* Removes border during print */
            }

            .receipt {
                width: 100%;
                padding: 10px;
            }

            .printer-design button {
                display: none;
            }

            .form-control {
                height: calc(5.25rem + 2px); /* Adjust this value if needed */
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
                    <h4 class="page-title">Daily Collection Sheet Overview</h4>
                </div>
                <span style="color: #a19595">"The Daily Collection Sheet provides a detailed record of all loan installments, arrears, penalties, and pending amounts collected for the day. This sheet helps in tracking payments made by customers, along with the associated details like loan ID, group, center, and collector information."</span>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body" style="width: 98%">

                        <div class="row">
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Collector</label>
                                    <select class="form-control select2" id="collector">
                                        <option value="0">All</option>
                                        @foreach($user as $item)
                                            <option value="{{$item->id}}">{{ $item->Full_Name }}-{{ $item->TP }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Lending Officer</label>
                                    <select class="form-control select2" id="lending_officer">
                                        <option value="0">All</option>
                                        @foreach($lending_officer as $item)
                                            <option value="{{$item->id}}">{{ $item->Full_Name }}-{{ $item->TP }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="newDropdown" class="form-label">Filter</label>
                                    <select class="form-control select2" id="filter">
                                        <option value="1">All</option>
                                        <option value="2" selected>Today Collection</option>
                                        <option value="3">Arrears</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="newDropdown" class="form-label">Date</label>
                                    <input type="date" class="form-control" value="{{date('Y-m-d')}}" disabled>
                                </div>
                            </div>


                        </div>
                        <div class="col-lg-3">
                            <div class="mb-3">
                                <button type="button" class="btn btn-danger" onclick="load_table();">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div class="table-responsive" style="width: 98%">
                            <table class="table table-centered mb-0" id="loan_table">
                                <thead class="bg-purple">
                                <tr>
                                    <th>Loan Id</th>
                                    <th>Loan No</th>
                                    <th>Center No</th>
                                    <th>Group No</th>
                                    <th>Member NIC</th>
                                    <th>Member Name</th>
                                    <th>Today Installment</th>
                                    <th>Areas Payment</th>
                                    <th>Penalty Total</th>
                                    <th>Pending Total</th>
                                    <th>Lending Officer</th>
                                    <th>Current Collector</th>
                                    <th hidden>Current Id</th>
                                    <th>Updated Collector</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                        </div> <!-- end table-responsive-->

                        <div class="row mt-1 mb-1 p-2">
                            <div
                                class="col-lg-12 d-flex align-items-center justify-content-between total-pending-container">
                                <div class="d-flex align-items-center">
                                    <div class="col-sm-12">
                                        <div>
                                            <span class="fw-bold">Total Collected Amount</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div>
                                            <span id="tot_amount" class="fw-bold">0.00</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-primary" onclick="printReceipt()">Print Report</button>
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
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
            src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="../JS/validate.js"></script>

    <script>
        $(function () {
            // Initialize DataTable with desired options
            $('#loan_table').DataTable({
                "paging": true,
                "lengthMenu": [10, 25, 50, 100],
                "order": [[2, 'asc'],[3, 'asc'],[5, 'asc']], // Order by the 5th column (index 4) in ascending order
                "responsive": true,
                "searching": false  // Disable the search field
            });

            // Call load_table function to populate data initially
            load_table();
        });


        function load_table() {
            let collector = $('#collector').val();
            let lending_officer = $('#lending_officer').val();
            let filter = $('#filter').val();

            $.ajax({
                type: "POST",
                url: "/dailycollection",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    collector: collector,
                    lending_officer: lending_officer
                },
                success: function (data, textStatus, xhr) {
                    console.log(data);
                    // Get DataTable instance
                    let table = $('#loan_table').DataTable();

                    // Clear DataTable rows before adding new ones
                    table.clear().draw();

                    let tot = 0.0;

                    // Populate options for collector dropdown
                    let collectorOptions = '';
                    data.collector.forEach(function (collector) {
                        collectorOptions += `<option value="${collector.id}">${collector.Full_Name}</option>`;
                    });

                    // Filter items based on filter value
                    data.item.forEach(function (item) {
                        let includeItem = false;
                        if (filter == "2") {
                            if (parseFloat(item.Today_Installment) !== 0) {
                                includeItem = true;
                            }
                        } else if (filter == "3") {
                            if (parseFloat(item.Installment_Balance_Before_Today) !== 0) {
                                includeItem = true;
                            }
                        } else {
                            includeItem = true;
                        }

                        if (includeItem) {
                            // Calculate total balance
                            tot += parseFloat(item.Total_Balance);

                            // Construct row HTML
                            var row = `
                    <tr>
                        <td>${item.idCustomer_Loan}</td>
                        <td>${item.Loan_No}</td>
                        <td>${item.center_no}</td>
                        <td>${item.group_name}</td>
                        <td>${item.NIC}</td>
                        <td>${item.customer_name} ${item.customer_lastname}</td>
                        <td>${parseFloat(item.Today_Installment).toFixed(2)}</td>
                        <td>${parseFloat(item.Installment_Balance_Before_Today).toFixed(2)}</td>
                        <td>${parseFloat(item.Panalty_Balance_Before_Today).toFixed(2)}</td>
                        <td>${(parseFloat(item.Today_Installment) + parseFloat(item.Installment_Balance_Before_Today) + parseFloat(item.Panalty_Balance_Before_Today)).toFixed(2)}</td>
                        <td>${item.lending}</td>
                        <td>${item.collector}</td>
                        <td hidden>${item.collector_id}</td>
                        <td>
                            <select class="form-control collector-dropdown">
                                ${collectorOptions}
                            </select>
                        </td>
                        <td>
                            <input type="button" class="btn btn-primary update-collector-btn"
                                   data-idCustomer_Loan="${item.idCustomer_Loan}"
                                   data-Loan_No="${item.Loan_No}"
                                   data-center_no="${item.center_no}"
                                   data-group_name="${item.group_name}"
                                   data-NIC="${item.NIC}"
                                   data-customer_name="${item.customer_name}"
                                   data-customer_lastname="${item.customer_lastname}"
                                   data-Installment_Balance="${item.Installment_Balance}"
                                   data-Panalty_Balance="${item.Panalty_Balance}"
                                   data-Total_Balance="${item.Total_Balance}"
                                   data-collector="${item.collector}"
                                   data-collector-id="${item.collector_id}"
                                   value="Update">
                        </td>
                    </tr>`;

                            // Add row to DataTable
                            let rowNode = table.row.add($(row)).draw(false).node();

                            // Set selected collector in the dropdown
                            $(rowNode).find('.collector-dropdown').val(item.collector_id);
                        }
                    });

                    // Update total amount
                    $("#tot_amount").text(tot.toFixed(2));
                },
                error: function (xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });
        }




        $(document).on('click', '.update-collector-btn', function () {
            let tableData = [];

            // Initialize DataTable and get all data
            const table = $('#loan_table').DataTable();

            // Iterate through all rows in the DataTable
            table.rows().every(function (rowIdx, tableLoop, rowLoop) {
                const row = this.data();
                let idCustomer_Loan = row[0]; // Loan Id
                let Loan_No = row[1]; // Loan No
                let center_no = row[2]; // Center No
                let group_name = row[3]; // Group No
                let NIC = row[4]; // Member NIC
                let customer_name = row[5].split(' ')[0]; // Assuming first name is the first word
                let customer_lastname = row[5].split(' ')[1]; // Assuming last name is the second word
                let Today_Installment = parseFloat(row[6]); // Today Installment
                let Installment_Balance = parseFloat(row[7]); // Areas Payment
                let Panalty_Balance = parseFloat(row[8]); // Penalty Total
                let Total_Balance = parseFloat(row[9]); // Pending Total
                let collector_name = row[10]; // Current Collector

                // Find the collector dropdown value for the current row
                let current_collector, collector_id;

                // Check if collector dropdown exists in the row (page)
                const collectorDropdown = $(table.row(rowIdx).node()).find('.collector-dropdown');
                if (collectorDropdown.length) {
                    current_collector = collectorDropdown.find('option:selected').text();
                    collector_id = collectorDropdown.find('option:selected').val();
                } else {
                    current_collector = ''; // Default value if dropdown is not found
                    collector_id = ''; // Default value if dropdown is not found
                }

                tableData.push({
                    idCustomer_Loan: idCustomer_Loan,
                    Loan_No: Loan_No,
                    center_no: center_no,
                    group_name: group_name,
                    NIC: NIC,
                    customer_name: customer_name,
                    customer_lastname: customer_lastname,
                    Installment_Balance: Installment_Balance,
                    Panalty_Balance: Panalty_Balance,
                    Total_Balance: Total_Balance,
                    collector: current_collector, // Adjust as needed
                    current_collector: current_collector,
                    collector_id: collector_id,
                    Today_Installment: Today_Installment,
                    collector_name: collector_name
                });
            });

            console.log(tableData);
            change_collector(tableData);
        });



        function change_collector(tableData) {


            console.log(JSON.stringify(tableData));

            let collector = $('#collector').val();

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to update this collector ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Update it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "/dailycollection/change_collector",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            "Content-Type": "application/json"
                        },
                        data: JSON.stringify({
                            tableData: tableData,
                            collector: collector
                        }),
                        success: function (response) {
                            console.log(response);
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Successfully saved!",
                            }).then(function () {
                                window.location.reload();
                            });
                        },
                        error: function (xhr, textStatus, errorThrown) {
                            console.error("Error:", errorThrown);
                            console.error("Response:", xhr.responseText);
                        }
                    });

                }
            });
        }


        function printReceipt() {
            // Gather selected values
            const collector = document.getElementById('collector').options[document.getElementById('collector').selectedIndex].text;
            const totalPendingAmount = document.getElementById('tot_amount').innerText;

            // Initialize DataTable and get all data
            const table = $('#loan_table').DataTable();
            const allData = table.rows().data();

            // Gather table data (skip last column)
            let tableData = '';
            allData.each(row => {
                tableData += `<tr>`;
                tableData += `<td>${row[1]}</td>`; // Loan No
                tableData += `<td>${row[2]}</td>`; // Center No
                tableData += `<td>${row[3]}</td>`; // Group No
                tableData += `<td>${row[4]}</td>`; // Member NIC
                tableData += `<td>${row[5]}</td>`; // Member Name
                tableData += `<td>${row[6]}</td>`; // Today Installment
                tableData += `<td>${row[7]}</td>`; // Areas Payment
                tableData += `<td>${row[8]}</td>`; // Penalty Total
                tableData += `<td>${row[9]}</td>`; // Pending Total
                tableData += `</tr>`;
            });

            // Create a print-friendly format
            const printContent = `
        <div style="text-align: center;">
            <h1>Today Collections (${new Date().toISOString().slice(0, 10)})</h1>
        </div>
        <div style="text-align: left;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 10%;"><strong>Collector</strong></td>
                    <td style="width: 90%;">${collector}</td>
                </tr>
            </table>
            <br>
            <table border="1" cellspacing="0" cellpadding="5" style="width: 100%; text-align: left;">
                <thead>
                    <tr>
                        <th style="text-align: center;">Loan No</th>
                        <th style="text-align: center;">Center No</th>
                        <th style="text-align: center;">Group No</th>
                        <th style="text-align: center;">Member NIC</th>
                        <th style="text-align: center;">Member Name</th>
                        <th style="text-align: center;">Today Installment</th>
                        <th style="text-align: center;">Areas Payment</th>
                        <th style="text-align: center;">Penalty Total</th>
                        <th style="text-align: center;">Pending Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableData}
                </tbody>
            </table>
            <div style="text-align: right; font-weight: bold;">
                <h3>Total Amount: ${totalPendingAmount}</h3>
            </div>
        </div>
    `;

            const newWindow = window.open('', '', 'height=600,width=800');
            newWindow.document.write('<html><head><title>Print</title>');
            newWindow.document.write('</head><body>');
            newWindow.document.write(printContent);
            newWindow.document.write('</body></html>');
            newWindow.document.close();
            newWindow.print();
        }


    </script>

@endsection

