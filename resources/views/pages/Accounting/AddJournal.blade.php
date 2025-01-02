@extends('layout.admin')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <style>
        /* Include your other styles */
        .select2-container {
            width: 100% !important;
        }
    </style>
    <style>
        .content-container {
            background-color: #fff;
            border-radius: 6px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .content-header h2 {
            font-size: 22px;
            font-weight: bold;
            color: #333;
        }

        .btn {
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-primary {
            background-color: #007bff;
            color: #fff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-success {
            background-color: #28a745;
            color: #fff;
            border: none;
        }

        .btn-success:hover {
            background-color: #218838;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-danger {
            background-color: #dc3545;
            color: #fff;
            border: none;
        }

        .btn-danger:hover {
            background-color: #c82333;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .table thead {
            background-color: #f8f9fa;
            text-align: center;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .move-up,
        .move-down {
            color: #007bff;
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
        }

        .move-up:hover,
        .move-down:hover {
            color: #0056b3;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .form-check-label {
            margin-left: 0.5rem;
        }
    </style>
@endsection

@section('content')
    <div class="content-container">
        <!-- Header Section -->
        <div class="content-header">
            <h2>Add Manual Journal</h2>
        </div>

        <!-- Journal Form -->
        <form id="manualJournalForm">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="narration" class="form-label">Narration</label>
                    <textarea class="form-control" id="narration" placeholder="Enter narration"></textarea>
{{--                    <div class="form-check mt-2">--}}
{{--                        <input type="checkbox" class="form-check-input" id="defaultNarrationCheckbox">--}}
{{--                        <label class="form-check-label" for="defaultNarrationCheckbox">Default narration to journal line description?</label>--}}
{{--                    </div>--}}
                </div>
                <div class="col-md-6">
                    <label for="journalDate" class="form-label">Journal Date</label>
                    <input type="date" class="form-control" id="journalDate">
                    <div class="mt-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="basis" id="accrualOnly" value="Accrual">
                            <label class="form-check-label" for="accrualOnly">Accrual Basis Only</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="basis" id="cashAndAccrual" value="CashAndAccrual">
                            <label class="form-check-label" for="cashAndAccrual">Cash and Accrual Basis</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Editable Table Section -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered" id="journalTable">
                    <thead>
                    <tr>
                        <th style="width: 5%;">Move</th>
                        <th style="width: 30%;">Description</th>
                        <th style="width: 20%;">Account</th>
                        <th style="width: 15%;">Tax Rate</th>
                        <th style="width: 15%;">Debit Amount</th>
                        <th style="width: 15%;">Credit Amount</th>
                        <th style="width: 5%;">Remove</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="text-center">
                            <span class="move-up">↑</span> <span class="move-down">↓</span>
                        </td>
                        <td><input type="text" class="form-control" placeholder="Enter description"></td>
                        <td>
                            <select class="form-select account-select select2-account">
                                <option value="">Select Account</option>
                                @foreach ($chart_of_accounts as $account)
                                    <option value="{{ $account->code }}-{{ $account->acc_name }}">
                                        {{ $account->acc_name }} - {{ $account->acc_type }}
                                    </option>
                                @endforeach
                            </select>
                        </td>


                        <td><input type="number" step="0.01" class="form-control tax-rate-input" placeholder="0.00"></td>
                        <td><input type="number" step="0.01" class="form-control debit-amount" placeholder="0.00"></td>
                        <td><input type="number" step="0.01" class="form-control credit-amount" placeholder="0.00"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-row-btn">✖</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <span class="move-up">↑</span> <span class="move-down">↓</span>
                        </td>
                        <td><input type="text" class="form-control" placeholder="Enter description"></td>
                        <td>
                            <select class="form-select account-select select2-account">
                                <option value="">Select Account</option>
                                @foreach ($chart_of_accounts as $account)
                                    <option value="{{ $account->code }}-{{ $account->acc_name }}">
                                        {{ $account->acc_name }} - {{ $account->acc_type }}
                                    </option>
                                @endforeach
                            </select>
                        </td>


                        <td><input type="number" step="0.01" class="form-control tax-rate-input" placeholder="0.00"></td>
                        <td><input type="number" step="0.01" class="form-control debit-amount" placeholder="0.00"></td>
                        <td><input type="number" step="0.01" class="form-control credit-amount" placeholder="0.00"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-row-btn">✖</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <span class="move-up">↑</span> <span class="move-down">↓</span>
                        </td>
                        <td><input type="text" class="form-control" placeholder="Enter description"></td>
                        <td>
                            <select class="form-select account-select select2-account">
                                <option value="">Select Account</option>
                                @foreach ($chart_of_accounts as $account)
                                    <option value="{{ $account->code }}-{{ $account->acc_name }}">
                                        {{ $account->acc_name }} - {{ $account->acc_type }}
                                    </option>
                                @endforeach
                            </select>
                        </td>


                        <td><input type="number" step="0.01" class="form-control tax-rate-input" placeholder="0.00"></td>
                        <td><input type="number" step="0.01" class="form-control debit-amount" placeholder="0.00"></td>
                        <td><input type="number" step="0.01" class="form-control credit-amount" placeholder="0.00"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-row-btn">✖</button>
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3"></td>
                        <td class="text-end fw-bold">Subtotal</td>
                        <td class="text-end"><span id="subtotalDebit">0.00</span></td>
                        <td class="text-end"><span id="subtotalCredit">0.00</span></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td class="text-end fw-bold">Total</td>
                        <td class="text-end"><span id="totalDebit">0.00</span></td>
                        <td class="text-end"><span id="totalCredit">0.00</span></td>
                        <td></td>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <button type="button" class="btn btn-primary mb-4" id="addRowBtn">Add a new line</button>

            <div class="d-flex justify-content-end ">
                <button type="button" class="btn btn-success me-2">Add Journal</button>
                <button type="button" class="btn btn-danger me-2"><a href="/ManualJournal" class="text-white">Cancel</a></button>
            </div>

            <!-- Totals Section -->
            <!-- Totals Section -->



        </form>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $('.select2-account').select2();


            const editJournalData = localStorage.getItem("editJournalData");
            console.log(editJournalData);
            if (editJournalData) {
                const journal = JSON.parse(editJournalData);

                // Populate the form fields
                $("#narration").val(journal.narration);
                $("#journalDate").val(journal.date);
                $(`input[name="basis"][value="${journal.type}"]`).prop("checked", true);

                // Clear existing rows in the table
                $("#journalTable tbody").empty();

                // Populate the journal rows
                journal.details.forEach((detail) => {
                    const accountsOptions = `
            <option value="">Select Account</option>
            @foreach ($chart_of_accounts as $account)
                    <option value="{{ $account->code }}-{{ $account->acc_name }}"
                        ${detail.account === "{{ $account->code }}-{{ $account->acc_name }}" ? "selected" : ""}>
                    {{ $account->acc_name }} - {{ $account->acc_type }}
                    </option>
@endforeach
                    `;

                    const newRow = `
            <tr>
                <td class="text-center">
                    <span class="move-up">↑</span> <span class="move-down">↓</span>
                </td>
                <td><input type="text" class="form-control" value="${detail.description}" placeholder="Enter description"></td>
                <td>
                    <select class="form-select account-select select2-account">
                        ${accountsOptions}
                    </select>
                </td>
                <td><input type="number" step="0.01" class="form-control tax-rate-input" value="${detail.tax_rate}" placeholder="0.00"></td>
                <td><input type="number" step="0.01" class="form-control debit-amount" value="${detail.debit_amount}" placeholder="0.00"></td>
                <td><input type="number" step="0.01" class="form-control credit-amount" value="${detail.credit_amount}" placeholder="0.00"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-row-btn">✖</button>
                </td>
            </tr>`;
                    $("#journalTable tbody").append(newRow);
                });

                // Reinitialize Select2 for new rows
                $(".select2-account").select2();

                // Calculate totals
                calculateTotals();
            }






            function calculateTotals() {
                let subtotalDebit = 0;
                let subtotalCredit = 0;

                $('#journalTable tbody tr').each(function () {
                    const debit = parseFloat($(this).find('.debit-amount').val()) || 0;
                    const credit = parseFloat($(this).find('.credit-amount').val()) || 0;
                    subtotalDebit += debit;
                    subtotalCredit += credit;
                });

                $('#subtotalDebit, #totalDebit').text(subtotalDebit.toFixed(2));
                $('#subtotalCredit, #totalCredit').text(subtotalCredit.toFixed(2));
            }

            $('#addRowBtn').on('click', function () {

                const accountsOptions = `
            <option value="">Select Account</option>
            @foreach ($chart_of_accounts as $account)
                <option value="{{ $account->code }}-{{ $account->acc_name }}">
                    {{ $account->acc_name }} - {{ $account->acc_type }}
                </option>
            @endforeach
                `;

                const newRow = `
        <tr>
            <td class="text-center">
                <span class="move-up">↑</span> <span class="move-down">↓</span>
            </td>
            <td><input type="text" class="form-control" placeholder="Enter description"></td>
            <td>
                <select class="form-select account-select select2-account">
                        ${accountsOptions}
                    </select>
            </td>
            <td><input type="number" step="0.01" class="form-control tax-rate-input" placeholder="0.00"></td>
            <td><input type="number" step="0.01" class="form-control debit-amount" placeholder="0.00"></td>
            <td><input type="number" step="0.01" class="form-control credit-amount" placeholder="0.00"></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remove-row-btn">✖</button>
            </td>
        </tr>`;
                $('#journalTable tbody').append(newRow);
                $('.select2-account').select2();
            });

            $(document).on('click', '.move-up', function () {
                const row = $(this).closest('tr');
                row.prev('tr').before(row);
            });

            $(document).on('click', '.move-down', function () {
                const row = $(this).closest('tr');
                row.next('tr').after(row);
            });

            $(document).on('click', '.remove-row-btn', function () {
                $(this).closest('tr').remove();
                calculateTotals();
            });

            $(document).on('input', '.debit-amount', function () {
                const row = $(this).closest('tr');
                const debitValue = $(this).val();

                // If debit is entered, clear credit field in the same row
                if (debitValue) {
                    row.find('.credit-amount').val('');
                }

                calculateTotals();
            });

            $(document).on('input', '.credit-amount', function () {
                const row = $(this).closest('tr');
                const creditValue = $(this).val();

                // If credit is entered, clear debit field in the same row
                if (creditValue) {
                    row.find('.debit-amount').val('');
                }

                calculateTotals();
            });

            calculateTotals();



            $('.btn-success').on('click', function () {
                // Gather form data
                const narration = $('#narration').val();
                const date = $('#journalDate').val();
                const type = $('input[name="basis"]:checked').val(); // Get the selected radio button value
                const totDebit = parseFloat($('#totalDebit').text());
                const totCredit = parseFloat($('#totalCredit').text());
                const totalAmount = totDebit + totCredit;

                const editJournalData = localStorage.getItem("editJournalData");
                const journalId = editJournalData ? JSON.parse(editJournalData).id : null;

                console.log(journalId);


                // Gather table data
                let rows = [];
                let hasValidRow = false;
                let hasIncompleteRow = false;

                $('#journalTable tbody tr').each(function () {
                    const description = $(this).find('input[type="text"]').val().trim();
                    const account = $(this).find('.account-select').val();
                    const taxRate = parseFloat($(this).find('.tax-rate-input').val()) || 0;
                    const debitAmount = parseFloat($(this).find('.debit-amount').val()) || 0;
                    const creditAmount = parseFloat($(this).find('.credit-amount').val()) || 0;

                    const isRowFilled = description || account || debitAmount > 0 || creditAmount > 0;
                    const isRowIncomplete = isRowFilled && (!description || !account || (debitAmount === 0 && creditAmount === 0));

                    if (isRowIncomplete) {
                        hasIncompleteRow = true;
                    }

                    if (!isRowIncomplete && isRowFilled) {
                        hasValidRow = true;
                        rows.push({
                            description: description,
                            account: account,
                            tax_rate: taxRate,
                            debit_amount: debitAmount,
                            credit_amount: creditAmount,
                        });
                    }
                });

                // Validation
                if (!narration || !date || !type) {
                    Swal.fire('Error', 'Please fill in the narration, journal date, and select a type.', 'error');
                    return;
                }
                if (hasIncompleteRow) {
                    Swal.fire('Error', 'Please complete all required fields in the table rows.', 'error');
                    return;
                }
                if (!hasValidRow) {
                    Swal.fire('Error', 'The table must have at least one complete row.', 'error');
                    return;
                }

                // Check if Debit and Credit totals are equal
                if (totDebit !== totCredit) {
                    Swal.fire('Error', 'Total Debit Amount and Credit Amount must be equal.', 'error');
                    return;
                }

                // Confirmation Dialog
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to save this Manual Journal?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Save it!',
                    cancelButtonText: 'No, Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // AJAX Request
                        $.ajax({
                            url: "{{ route('manual_journal.save') }}",
                            method: 'POST',
                            data: {
                                id_manual_journal: journalId, // Include the ID for editing
                                narration: narration,
                                date: date,
                                type: type,
                                tot_debit: totDebit,
                                tot_credit: totCredit,
                                total_amount: totalAmount,
                                rows: rows,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (response) {
                                if (response.status === 'success') {
                                    localStorage.removeItem("editJournalData");
                                    Swal.fire('Saved!', response.message, 'success').then(() => {
                                        window.location.href = '/ManualJournal';
                                    });
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('Error', 'An unexpected error occurred.', 'error');
                            }
                        });
                    }
                });
            });


        });
    </script>
@endsection

