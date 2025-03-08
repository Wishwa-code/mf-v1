@extends('layout.admin')

@section('head')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        .container {
            max-width: 95%;
            margin: auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
            font-size: 1.8rem;
            font-weight: 700;
            color: #007bff;
            margin-bottom: 20px;
        }

        .period {
            font-size: 16px;
            font-weight: bold;
            text-align: left;
            margin-bottom: 15px;
        }

        .reconciliation-wrapper {
            display: flex;
            gap: 20px;
        }

        .table-container {
            background: #ffffff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            flex: 1;
        }

        .table-container h4 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        table th {
            background: #f5f5f5;
            color: #000;
            padding: 10px;
            text-align: center;
            font-weight: 600;
            border: 1px solid #dee2e6;
        }

        table td {
            padding: 8px;
            text-align: center;
            border: 1px solid #dee2e6;
            background: #ffffff;
        }

        table tbody tr:hover {
            background-color: #e9f5ff;
            cursor: pointer;
        }

        .highlight {
            background-color: #4CAF50 !important;
            color: white;
        }

        .summary-section {
            background: #ffffff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .summary-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .summary-buttons button {
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            margin-right: 5px;
        }

        .btn-mark {
            background-color: #007bff;
            color: white;
        }

        .btn-unmark {
            background-color: #dc3545;
            color: white;
        }

        .summary-values {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .summary-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 10px;
        }

        /* Left Side - Beginning Balance */
        .summary-left {
            flex: 1;
            padding-right: 20px;
        }

        /* Right Side - Other Balances */
        .summary-right {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
        }

        .btn {
            padding: 10px 15px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }


        /* ✅ Force row color when checkbox is checked */
        table tbody tr.selected-row {
            background-color: #000000 !important;
            color: #4CAF50; /* Keep text white for better contrast */
            transition: background-color 0.3s ease-in-out;
        }



    </style>
@endsection

@section('content')
    <div class="container">
        <h2>Bank Reconciliation</h2>
        <h4 style="float: right">{{$reconciliation->code}}/{{$reconciliation->Account_Name}}/{{$reconciliation->Bank_Name}}</h4>
        <div class="period">For period: {{$reconciliation->date}}</div>

        <div class="reconciliation-wrapper">
            <!-- Checks and Payments Table (Credits) -->
            <div class="table-container">
                <h4>Checks and Payments</h4>
                <table id="creditTable">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Date</th>
                        <th>CHK#</th>
                        <th>Payee</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($bank_log as $transaction)
                        @if($transaction->Credit > 0)  <!-- Filter Credit Transactions -->
                        <tr id="{{$transaction->id}}"
                            class="{{ $transaction->reconsilation_status == $id ? 'selected-row' : '' }}">
                            <td>
                                <input type="checkbox" class="mark-transaction"
                                        {{ $transaction->reconsilation_status == $id ? 'checked' : '' }}>
                            </td>
                            <td>{{ $transaction->Date_Time }}</td>
                            <td>{{ $transaction->Description }}</td>
                            <td>{{ $transaction->Type }}</td>
                            <td>{{ number_format($transaction->Credit, 2, '.', '') }}</td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="table-container">
                <h4>Deposits and Other Credits</h4>
                <table id="debitTable">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Date</th>
                        <th>Memo</th>
                        <th>Type</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($bank_log as $transaction)
                        @if($transaction->Debit > 0)  <!-- Filter Debit Transactions -->
                        <tr id="{{$transaction->id}}"
                            class="{{ $transaction->reconsilation_status == $id ? 'selected-row' : '' }}">
                            <td>
                                <input type="checkbox" class="mark-transaction"
                                        {{ $transaction->reconsilation_status == $id ? 'checked' : '' }}>
                            </td>
                            <td>{{ $transaction->Date_Time }}</td>
                            <td>{{ $transaction->Description }}</td>
                            <td>{{ $transaction->Type }}</td>
                            <td>{{ number_format($transaction->Debit, 2, '.', '') }}</td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>


        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            @if($status=="edit")
                <div class="summary-header">
                    <div class="summary-buttons">
                        <button id="markAll" class="btn btn-primary btn-sm">Mark All</button>
                        <button id="unmarkAll" class="btn btn-danger btn-sm">Unmark All</button>
                        <button id="modifyEntry" class="btn btn-warning btn-sm">Modify</button> <!-- New Modify Button -->
                    </div>
                </div>
            @endif

            <div class="table-responsive mt-2">
                <table class="table table-bordered" id="transactionTable">
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
                    <tbody>
                        @foreach($reconciliation_log as $item)
                            <tr id="{{$transaction->account_id}}">
                                <td>{{$item->Account_Name}}</td>
                                <td>{{$item->description}}</td>
                                <td>{{$item->date}}</td>
                                <td>{{$item->credit}}</td>
                                <td>{{$item->debit}}</td>
                                <td><button class="btn btn-danger btn-sm removeRow">X</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="summary-content">
                <div class="summary-left">
                    <div class="summary-values"><span>Beginning Balance:</span> <span id="beginningBalance">{{number_format($balance, 2,'.', '') ?? '0.00'}}</span></div>
                </div>

                <div class="summary-right">
                    <div class="summary-values"><span>Ending Balance:</span> <span id="endingBalance">{{ number_format($reconciliation->endingBalance, 2, '.', ',') }}</span></div>
                    <div class="summary-values"><span>Cleared Balance:</span> <span id="clearedBalance">0.00</span></div>
                    <div class="summary-values"><span>Difference:</span> <span id="difference">0.00</span></div>
                </div>
            </div>

            <!-- ✅ Transaction Table (Replaces Service Charge & Interest Earned) -->
            @if($status=="edit")
                <div class="action-buttons">
                    <button class="btn btn-success" id="startReconciliation">Start Reconciliation</button>
                </div>
            @endif



        </div>

    </div>

    <!-- ✅ Transaction Entry Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Transaction</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
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
                            <label class="form-label">Contra Account</label>
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
                </div>
            </div>
        </div>
    </div>

@endsection


@section('script')
    <script>
        $(document).ready(function () {
            // Ensure background color changes when checkbox is checked
            $(document).on("change", ".mark-transaction", function () {
                let row = $(this).closest("tr");

                if ($(this).is(":checked")) {
                    row.addClass("selected-row").css("background-color", "#b5f7b6"); // ✅ Apply green color manually
                } else {
                    row.removeClass("selected-row").css("background-color", ""); // ✅ Reset to default
                }

                updateSummary(); // Update summary calculations
            });

            // Mark all checkboxes
            $("#markAll").click(function () {
                $(".mark-transaction").prop("checked", true).trigger("change");
            });

            // Unmark all checkboxes
            $("#unmarkAll").click(function () {
                $(".mark-transaction").prop("checked", false).trigger("change");
            });


            // ✅ Open Modal on "Modify" Button Click
            $("#modifyEntry").click(function () {
                $("#transactionModal").modal("show");
            });

            // ✅ Add Entry to Table
            $("#addEntry").click(function () {
                let account_id = $("#modal-account-data").val();
                let account_text = $("#modal-account-data option:selected").text();
                let description = $("#modal-description").val();
                let date = $("#modal-entry-date").val();
                let type = $("#modal-type").val();
                let amount = $("#modal-amount").val();

                if (!account_id || !description || !date || !amount) {
                    Swal.fire({
                        icon: "warning",
                        title: "Missing Information",
                        text: "All fields are required!",
                    });
                    return;
                }

                let credit = type === "credit" ? amount : "0.00";
                let debit = type === "debit" ? amount : "0.00";

                let row = `
            <tr id="${account_id}">
                <td>${account_text}</td>
                <td>${description}</td>
                <td>${date}</td>
                <td>${credit}</td>
                <td>${debit}</td>
                <td><button class="btn btn-danger btn-sm removeRow">X</button></td>
            </tr>
        `;

                $("#transactionTable tbody").append(row);

                // ✅ Clear Fields After Adding
                $("#modal-description").val("");
                $("#modal-entry-date").val("");
                $("#modal-amount").val("");
                $("#modal-type").val("credit");
                updateSummary();
                // ✅ Attach Remove Event to New Rows
                $(".removeRow").click(function () {
                    $(this).closest("tr").remove();
                    updateSummary();
                });
            });

            // ✅ Remove Row When "X" Button is Clicked
            $(document).on("click", ".removeRow", function () {
                $(this).closest("tr").remove();
                updateSummary();
            });

            // ✅ Enable Select2 in Modal
            $('#transactionModal').on('shown.bs.modal', function () {
                $('.select2').select2({
                    dropdownParent: $('#transactionModal')
                });
            });

            // ✅ Function to calculate totals and update balances
            // ✅ Function to calculate totals and update balances
            function updateSummary() {
                let beginningBalance = 0;
                let clearedBalance = 0;
                let transactionCreditTotal = 0;
                let transactionDebitTotal = 0;
                let endingBalance = parseFloat($("#endingBalance").text().replace(/,/g, '')) || 0;

                // ✅ Calculate beginning balance from selected `creditTable` rows
                let creditTableSelectedSum = 0;
                $("#creditTable tbody tr").each(function () {
                    if ($(this).find(".mark-transaction").prop("checked")) {  // Only checked rows
                        let amount = parseFloat($(this).find("td:last-child").text().replace(/,/g, '')) || 0;
                        creditTableSelectedSum += amount;
                    }
                });

                // ✅ Calculate `debitTable` sum from selected rows
                let debitTableSelectedSum = 0;
                $("#debitTable tbody tr").each(function () {
                    if ($(this).find(".mark-transaction").prop("checked")) {  // Only checked rows
                        let amount = parseFloat($(this).find("td:last-child").text().replace(/,/g, '')) || 0;
                        debitTableSelectedSum += amount;
                    }
                });

                // ✅ Calculate transactionTable totals (all rows)
                $("#transactionTable tbody tr").each(function () {
                    let credit = parseFloat($(this).find("td:nth-child(4)").text().replace(/,/g, '')) || 0;
                    let debit = parseFloat($(this).find("td:nth-child(5)").text().replace(/,/g, '')) || 0;

                    transactionCreditTotal += credit;
                    transactionDebitTotal += debit;
                });



                // ✅ Final `clearedBalance` calculation
                clearedBalance = (beginningBalance - creditTableSelectedSum) + debitTableSelectedSum;

                // ✅ Add transaction table calculations
                clearedBalance += (transactionCreditTotal - transactionDebitTotal);

                // ✅ Compute difference
                let difference = clearedBalance - endingBalance;

                $("#clearedBalance").text(clearedBalance.toFixed(2));
                $("#difference").text(difference.toFixed(2));
            }

            updateSummary();




            // ✅ Start Reconciliation Click Event
            $("#startReconciliation").click(function () {
                // ✅ Collect Checked Transactions from Credit Table
                let creditTransactions = [];
                $("#creditTable tbody tr").each(function () {
                    if ($(this).find(".mark-transaction").prop("checked")) {
                        creditTransactions.push({
                            id: $(this).attr("id"),
                            date: $(this).find("td:nth-child(2)").text().trim(),
                            description: $(this).find("td:nth-child(3)").text().trim(),
                            type: $(this).find("td:nth-child(4)").text().trim(),
                            amount: parseFloat($(this).find("td:nth-child(5)").text().replace(/,/g, '')) || 0
                        });
                    }
                });

                // ✅ Collect Checked Transactions from Debit Table
                let debitTransactions = [];
                $("#debitTable tbody tr").each(function () {
                    if ($(this).find(".mark-transaction").prop("checked")) {
                        debitTransactions.push({
                            id: $(this).attr("id"),
                            date: $(this).find("td:nth-child(2)").text().trim(),
                            description: $(this).find("td:nth-child(3)").text().trim(),
                            type: $(this).find("td:nth-child(4)").text().trim(),
                            amount: parseFloat($(this).find("td:nth-child(5)").text().replace(/,/g, '')) || 0
                        });
                    }
                });

                // ✅ Collect All Transactions from Transaction Table
                let transactionEntries = [];
                $("#transactionTable tbody tr").each(function () {
                    transactionEntries.push({
                        id: $(this).attr("id"),
                        account: $(this).find("td:nth-child(1)").text().trim(),
                        description: $(this).find("td:nth-child(2)").text().trim(),
                        date: $(this).find("td:nth-child(3)").text().trim(),
                        credit: parseFloat($(this).find("td:nth-child(4)").text().replace(/,/g, '')) || 0,
                        debit: parseFloat($(this).find("td:nth-child(5)").text().replace(/,/g, '')) || 0
                    });
                });

                // ✅ Collect Summary Values
                let summaryData = {
                    beginningBalance: parseFloat($("#beginningBalance").text().replace(/,/g, '')) || 0,
                    endingBalance: parseFloat($("#endingBalance").text().replace(/,/g, '')) || 0,
                    clearedBalance: parseFloat($("#clearedBalance").text().replace(/,/g, '')) || 0,
                    difference: parseFloat($("#difference").text().replace(/,/g, '')) || 0
                };

                console.log(creditTransactions);
                console.log(debitTransactions);
                console.log(transactionEntries);
                console.log(summaryData);

                // ✅ Validate before sending
                if (summaryData.difference !== 0) {
                    Swal.fire({
                        icon: "warning",
                        title: "Reconciliation Not Balanced!",
                        text: "The difference is not zero. Please check your transactions.",
                    });
                    return;
                }



                // ✅ Confirm before submitting
                Swal.fire({
                    title: "Are you sure?",
                    text: "Once submitted, this reconciliation cannot be changed!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, submit it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ✅ Send AJAX request to Laravel
                        $.ajax({
                            url: "{{ route('reconciliation.store') }}", // ✅ Define this in web.php
                            type: "POST",
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            },
                            data: {
                                id: {{$id}},
                                creditTransactions: creditTransactions,
                                debitTransactions: debitTransactions,
                                transactionEntries: transactionEntries,
                                summary: summaryData
                            },
                            success: function (response) {
                                Swal.fire("Success!", "Reconciliation saved successfully.", "success").then(() => {
                                    location.reload(); // Reload page after success
                                });
                            },
                            error: function (error) {
                                Swal.fire("Error!", "Something went wrong while saving.", "error");
                            }
                        });
                    }
                });
            });




        });

    </script>
@endsection
