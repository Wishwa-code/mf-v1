@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"/>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css"/>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">



    <style>
        /* Global Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        /* Title Styling */
        h1 {
            text-align: center;
            font-size: 2rem;
            font-weight: 600;
            color: #000000;
            margin-bottom: 20px;
        }

        /* Container */
        .container {
            max-width: 85%;
            margin: auto;
        }

        /* Filters Section - Improved Alignment */
        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); /* Responsive grid layout */
            gap: 15px;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            align-items: center;
        }

        /* Ensure each filter is displayed in column layout */
        .filters div {
            display: flex;
            flex-direction: column;
        }

        /* Label Styling */
        .filters label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        /* Inputs & Selects */
        .filters select, .filters input {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ced4da;
            font-size: 14px;
        }

        /* Buttons - Consistent Styling */
        .filters button {
            height: 40px; /* Ensures buttons match input height */
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease-in-out;
        }

        /* Search Button */
        #searchReconciliation {
            background: #007bff;
            color: white;
        }

        #searchReconciliation:hover {
            background: #0056b3;
        }

        /* New Reconciliation Button */
        #openModal {
            background: #28a745;
            color: white;
        }

        #openModal:hover {
            background: #218838;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .filters {
                grid-template-columns: repeat(2, 1fr); /* Two columns on smaller screens */
            }
        }

        @media (max-width: 576px) {
            .filters {
                grid-template-columns: 1fr; /* Single column layout on very small screens */
            }

            .filters button {
                width: 100%; /* Buttons take full width for better UX */
            }
        }


        /* Table Container */
        .table-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.08);
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border-radius: 8px;
            overflow: hidden;
        }

        /* Table Header */
        table th {
            background: #f5f5f5;
            color: #000000;
            padding: 12px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid #dee2e6;
        }

        /* Table Data */
        table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-size: 14px;
            background: #ffffff;
        }

        /* Alternating row colors */
        table tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        /* Hover Effect */
        table tbody tr:hover {
            background-color: #e9f5ff;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .filters {
                flex-direction: column;
                gap: 10px;
            }
        }

        @media (max-width: 576px) {
            .filters {
                flex-direction: column;
            }

            table th, table td {
                font-size: 12px;
                padding: 10px;
            }

            #openModal {
                width: 100%;
                text-align: center;
            }
        }


    </style>
    <style>

        .btn-success {
            background-color: green;
            color: white;
            padding: 12px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 6px;
        }

        .btn-success:hover {
            background-color: darkgreen;
        }

        /* ✅ Fixed Table Alignment */
        #transactionTable {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }

        #transactionTable th, #transactionTable td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        /* ✅ Fixed Remove Button Size */
        .removeRow {
            background-color: red;
            color: white;
            border: none;
            padding: 6px 12px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 13px;
        }

        .removeRow:hover {
            background-color: darkred;
        }

        /* ✅ Better spacing for the "Add to Table" button */
        #addEntry {
            margin-top: 15px;
            margin-bottom: 20px;
        }
    </style>



@endsection

@section('content')
    <div>
        <br>
        <h2>Bank Reconciliation</h2>
        <br>
        <div class="filters">
            <div>
                <label>Bank Account</label>
                <select id="bank-account" class="form-control select2">
                    <option value="0">All</option>
                    @foreach($bank as $item)
                        @if($item->Bank_Type=="Bank")
                            <option value="{{$item->Idbank}}">{{$item->Bank_Name}} - {{$item->Account_No}}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div hidden>
                <label>Date From</label>
                <input type="date" class="form-control" id="date-from" value="2023-01-01">
            </div>

            <div>
                <label>Date To</label>
                <input type="date" class="form-control" id="date-to" value="{{date('Y-m-d')}}">
            </div>

            <button id="searchReconciliation" class="btn btn-primary">Search</button>
            <button id="openModal">New Reconciliation</button>

        </div>

        <div class="table-container mt-4">
            <div class="table-responsive">
                <table id="bankReconciliationTable" class="table table-bordered w-100">
                    <thead>
                    <tr>
                        <th>Account</th>
                        <th>Date</th>
                        <th>Created Date/Time</th>
                        <th>Note</th>
                        <th>Opening Balance</th>
                        <th>Ending Balance</th>
                        <th>Status</th>
                        <th style="width: 150px;">Action</th>
                        <th style="width: 150px;">Report</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>


    </div>

    <div class="modal fade" id="reconciliationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">New Bank Reconciliation</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Bank Account</label>
                            <select id="modal-account" class="form-select select2" onchange="load_data(this.value)">
                                <option value="0">Select Account</option>
                                @foreach($bank as $item)
                                    @if($item->Bank_Type=="Bank")
                                        <option value="{{$item->Idbank}}">{{$item->Bank_Name}} - {{$item->Account_No}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Reconciliation On</label>
                            <input type="date" id="modal-last-reconciliation-date" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Statement Date</label>
                            <input type="date" id="modal-statement-date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Beginning Balance</label>
                            <input type="text" id="modal-beginning-balance" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ending Balance</label>
                            <input type="text" id="modal-ending-balance" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3 mt-3" hidden>
                        <div class="col-md-6">
                            <label class="form-label">Note</label>
                            <input type="text" id="note" class="form-control">
                        </div>
                    </div>
                    <br>
                    <hr>
                    <br>
                    <span>Enter any service charge or interest earned</span>
                    <br><br>
                    <!-- Transactions Section -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <input type="text" id="modal-description" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="date" id="modal-entry-date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Transaction Type</label>
                            <select id="modal-type" class="form-select">
                                <option value="credit">Credit(Interest Or Other Receivables)</option>
                                <option value="debit">Debit(Service Chargers Or Other Payable)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contra
                                Account</label>
                            <select id="modal-account-data" class="form-select select2">
                                @foreach($bank as $item)
                                    <option value="{{$item->Idbank}}">{{$item->Bank_Name}} - {{$item->Account_No}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <label class="form-label">Amount</label>
                            <input type="text" id="modal-amount" class="form-control">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-success w-100" id="addEntry">Add to Table</button>
                        </div>
                    </div>

                    <hr>

                    <!-- Transactions Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered mt-3" id="transactionTable">
                            <thead class="table-light">
                            <tr>
                                <th>Account</th>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Credit</th>
                                <th>Debit</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-danger px-4">Start Reconciliation</button>
                    </div>
                </div>
            </div>
        </div>
    </div>





@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            $('.select2').select2({ width: '100%' });

            $("#bank-account").change(function () {
                let selectedAccount = $(this).val();
                $("#openModal").prop("disabled", selectedAccount === "0");
            });
            // Reinitialize Select2 inside the modal when it's opened
            $('#reconciliationModal').on('shown.bs.modal', function () {
                $('.select2').select2({
                    dropdownParent: $('#reconciliationModal') // Fixes Select2 inside modal
                });
            });

            // Destroy Select2 when the modal is closed
            $('#reconciliationModal').on('hidden.bs.modal', function () {
                $('.select2').select2({ width: '100%' });
            });

            $("#openModal").click(function () {
                let selectedAccount = $("#bank-account").val();


                load_data(selectedAccount);

                $("#reconciliationModal").modal('show'); // ✅ Open modal
            });




            $(".btn-close").click(function () {
                $("#reconciliationModal").modal('hide'); // ✅ Correct way to close Bootstrap modal
            });

            $("#addEntry").click(function () {
                let account_id = $("#modal-account-data").val(); // Get selected account
                let account_text = $("#modal-account-data option:selected").text(); // Account name for display
                let description = $("#modal-description").val();
                let date = $("#modal-entry-date").val();
                let type = $("#modal-type").val();
                let amount = $("#modal-amount").val();

                if (!account_id || !description || !date || !amount) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Information',
                        text: 'All fields are required!',
                    });
                    return;
                }

                let credit = type === "credit" ? amount : "0.00";
                let debit = type === "debit" ? amount : "0.00";

                $("#transactionTable tbody").append(`
            <tr data-account-id="${account_id}">
                <td>${account_text}</td>
                <td>${description}</td>
                <td>${date}</td>
                <td>${credit}</td>
                <td>${debit}</td>
                <td><button class="removeRow btn btn-sm btn-danger">X</button></td>
            </tr>
        `);

                // ✅ Reset fields after adding the entry
                $("#modal-description").val("");
                $("#modal-entry-date").val("");
                $("#modal-amount").val("");
                $("#modal-type").val("credit"); // Reset to default option

                // ✅ Attach remove event to new rows
                $(".removeRow").click(function () {
                    $(this).closest("tr").remove();
                });
            });


            $("#searchReconciliation").click(function () {
                let accountId = $("#bank-account").val();
                let dateFrom = $("#date-from").val();
                let dateTo = $("#date-to").val();

                if (!accountId || !dateFrom || !dateTo) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Information',
                        text: 'Please select an account and date range!',
                    });
                    return;
                }

                $.ajax({
                    url: "{{ route('search.reconciliation') }}",
                    type: "GET",
                    data: {
                        account_id: accountId,
                        date_from: dateFrom,
                        date_to: dateTo
                    },
                    success: function (response) {
                        $("#bankReconciliationTable tbody").html(""); // Clear table before appending new rows
                        if (response.length > 0) {
                            $.each(response, function (index, reconciliation) {

                                console.log(reconciliation.status);

                                let statusLabel = reconciliation.status == '-1'
                                    ? '<span class="badge bg-success">Completed</span>'
                                    : '<span class="badge bg-danger">Pending</span>';

                                // Enable Edit button only if status is Pending (0)
                                let editButton = reconciliation.status == '-1'
                                    ? `<button class="btn btn-sm btn-secondary" disabled>Edit</button>`
                                    : `<button class="btn btn-sm btn-warning edit-btn">Edit</button>`;

                                let deleteButton = reconciliation.status == '-1'
                                    ? `<button class="btn btn-sm btn-secondary" disabled>Delete</button>`
                                    : `<button class="btn btn-sm btn-danger delete-btn">Delete</button>`;

                                let row = `
    <tr data-id="${reconciliation.id_reconciliation}">
        <td>${reconciliation.Account_Name}-${reconciliation.Account_No}</td>
        <td>${reconciliation.date}</td>
        <td>${reconciliation.created_date_time}</td>
        <td>${reconciliation.note || '-'}</td>
        <td>${parseFloat(reconciliation.balance).toFixed(2)}</td>
        <td>${parseFloat(reconciliation.endingBalance).toFixed(2)}</td>
        <td>${statusLabel}</td>
        <td style="min-width: 160px; text-align: center;">
            <div class="d-flex justify-content-center gap-2">
                <button class="btn btn-sm btn-primary view-btn">View</button>
                ${deleteButton}
                ${editButton}
            </div>
        </td>
        <td style="min-width: 160px; text-align: center;">
            <div class="d-flex justify-content-center gap-2">
<button class="btn btn-sm btn-dark" onclick="open_details_report(${reconciliation.id_reconciliation})">Detail</button>
                <button class="btn btn-sm btn-dark" onclick="open_summary_report(${reconciliation.id_reconciliation})">Summary</button>
            </div>
        </td>
    </tr>
`;


                                $("#bankReconciliationTable tbody").append(row);
                            });

                        } else {
                            $("#bankReconciliationTable tbody").append(`<tr><td colspan="9" class="text-center">No records found</td></tr>`);
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'Something went wrong while fetching data!',
                        });
                    }
                });
            });






            // Handle Delete
            $(document).on("click", ".delete-btn", function () {
                let row = $(this).closest("tr");
                let id = row.data("id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('delete.reconciliation') }}",
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id
                            },
                            success: function (response) {
                                Swal.fire("Deleted!", "Reconciliation has been deleted.", "success").then(() => {
                                    row.remove();
                                });
                            },
                            error: function () {
                                Swal.fire("Error", "Something went wrong!", "error");
                            }
                        });
                    }
                });
            });

            // Handle Edit
            $(document).on("click", ".edit-btn", function () {
                let id = $(this).closest("tr").data("id");
                window.location.href = "BankReconsilationInside/" + id+"/edit";
            });

            // Handle View
            $(document).on("click", ".view-btn", function () {
                let id = $(this).closest("tr").data("id");
                window.location.href = "BankReconsilationInside/" + id+"/view";
            });

            $("#modal-statement-date").on("change", function () {
                let statement_date = new Date($(this).val());
                let ending_date = new Date($("#modal-last-reconciliation-date").val());

                // Check if ending_date is selected and if statement_date is earlier than ending_date
                if ($("#modal-last-reconciliation-date").val() && statement_date < ending_date) {
                    Swal.fire("Error", "Statement Date cannot be earlier than the Ending Date!", "error");
                    $(this).val(""); // Clear the invalid date
                }
            });




            // Handle Start Reconciliation
            $(".btn-danger").click(function () {
                let account_id = $("#modal-account").val();
                let statement_date = $("#modal-statement-date").val();
                let balance = $("#modal-ending-balance").val();
                let beginig_balance = parseFloat($("#modal-beginning-balance").val()) || 0;
                let note = $("#note").val();

                let transactions = [];
                $("#transactionTable tbody tr").each(function () {
                    let row = $(this).find("td");
                    let account_id = $(this).data("account-id"); // Get Account ID from row
                    transactions.push({
                        account_id: account_id, // Save correct account ID
                        description: row.eq(1).text(),
                        date: row.eq(2).text(),
                        credit: row.eq(3).text() || "0.00",
                        debit: row.eq(4).text() || "0.00",
                    });
                });

                if (account_id && statement_date && balance) {
                    Swal.fire({
                        title: "Are you sure?",
                        text: "Do you want to start reconciliation?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, Save it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ route('bankReconciliation.store') }}",
                                type: "POST",
                                data: {
                                    account_id: account_id,
                                    date: statement_date,
                                    balance: balance,
                                    beginig_balance: beginig_balance,
                                    note: note,
                                    transactions: transactions
                                },
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                                },
                                success: function (response) {
                                    Swal.fire("Saved!", "Reconciliation has been saved.", "success").then(() => {
                                        window.location.href = "BankReconsilationInside/" + response.id+"/edit";
                                    });
                                },
                                error: function () {
                                    Swal.fire("Error", "Something went wrong!", "error");
                                }
                            });
                        }
                    });
                } else {
                    Swal.fire("Error", "Please fill all fields and add at least one transaction", "error");
                }
            });

        });
        function load_data(selectedAccount){
            // **AJAX request to fetch last reconciliation details**
            $.ajax({
                url: "{{ route('get.last.reconciliation') }}",
                type: "GET",
                data: { account_id: selectedAccount },
                success: function (response) {
                    if (response) {
                        $("#modal-last-reconciliation-date").val(response.date || ""); // Set Last Reconciliation Date
                        let balance = parseFloat(response.balance);
                        $("#modal-beginning-balance").val(isNaN(balance) ? "0.00" : balance.toFixed(2));

                    } else {
                        $("#modal-last-reconciliation-date").val(""); // If no record found, keep empty
                        $("#modal-beginning-balance").val("0.00"); // Default balance
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Fetching Data',
                        text: 'Could not retrieve the last reconciliation record.',
                    });
                }
            });
        }

        function open_details_report(id){
            // Define the Laravel route and append the ID
            let url = `/ReconciliationDetails/${id}`;

            // Redirect to the route
            window.open(url, '_blank');
        }

        function open_summary_report(id){
            // Define the Laravel route and append the ID
            let url = `/ReconciliationSummary/${id}`;

            // Redirect to the route
            window.open(url, '_blank');
        }


    </script>



@endsection
