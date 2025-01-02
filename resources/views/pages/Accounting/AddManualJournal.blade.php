@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .card-header {
            background: linear-gradient(45deg, #4e73df, #224abe);
            color: #fff;
            padding: 15px;
            border-bottom: 0;
        }

        .card-header h4 {
            margin: 0;
        }

        .form-label {
            font-weight: 600;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .move-up-down,
        .remove-row {
            font-size: 18px;
            cursor: pointer;
            color: #6c757d;
        }

        .move-up-down:hover,
        .remove-row:hover {
            color: #495057;
        }

        .table-light th {
            background-color: #f8f9fa;
        }

        .summary-table {
            margin-top: 10px;
        }

        .summary-table td {
            border-top: none;
            padding: 5px 0;
        }

        .summary-table tr.subtotal-row td,
        .summary-table tr.total-row td {
            border-top: 2px solid #6c757d;
            border-bottom: 2px solid #6c757d;
        }

        .btn-add-line {
            background-color: #17a2b8;
            color: #fff;
            border-radius: 50px;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        .btn-add-line:hover {
            background-color: #138496;
        }

        .form-check-label {
            cursor: pointer;
        }

        .form-control:focus {
            box-shadow: 0px 0px 5px #17a2b8;
            border-color: #17a2b8;
        }

        .btn-success, .btn-danger {
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 50px;
        }

        .btn-success {
            background-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
@endsection

@section('content')

    <div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="container mt-12">
                    <div class="card shadow">
                        <div class="card-header">
                            <h4 class="mb-0">Add Manual Journal</h4>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <label for="narration" class="form-label">Narration</label>
                                        <textarea id="narration" class="form-control" rows="2" placeholder="Enter narration..."></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="journal-date" class="form-label">Journal Date</label>
                                        <input type="date" id="journal-date" class="form-control">
                                    </div>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="default-narration">
                                    <label for="default-narration" class="form-check-label">Default narration to journal line description?</label>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Basis</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="accrual-basis" name="basis" checked>
                                            <label class="form-check-label" for="accrual-basis">Accrual Basis Only</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="cash-accrual-basis" name="basis">
                                            <label class="form-check-label" for="cash-accrual-basis">Cash and Accrual Basis</label>
                                        </div>
                                    </div>
                                </div>

                                <table class="table table-bordered">
                                    <thead class="table-light">
                                    <tr>
                                        <th></th>
                                        <th>Description</th>
                                        <th>Account</th>
                                        <th>Tax rate</th>
                                        <th>Debit Amount</th>
                                        <th>Credit Amount</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!-- Repeatable journal entry rows -->
                                    <tr>
                                        <td class="text-center">
                                            <span class="move-up-down">&#8597;</span>
                                        </td>
                                        <td><input type="text" class="form-control" placeholder="Description"></td>
                                        <td><select class="form-select select2-account"></select></td>
                                        <td><input type="text" class="form-control" placeholder="Tax rate"></td>
                                        <td><input type="text" class="form-control" placeholder="Debit"></td>
                                        <td><input type="text" class="form-control" placeholder="Credit"></td>
                                        <td class="text-center">
                                            <span class="remove-row">&#10060;</span>
                                        </td>
                                    </tr>
                                    <!-- Add more rows dynamically -->
                                    </tbody>
                                </table>

                                <button type="button" class="btn-add-line mb-3">+ Add a new line</button>

                                <!-- Summary table -->
                                <table class="table summary-table">
                                    <tbody>
                                    <tr class="subtotal-row">
                                        <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                        <td class="text-end"><strong>0.00</strong></td>
                                        <td class="text-end"><strong>0.00</strong></td>
                                    </tr>

                                    <tr class="total-row">
                                        <td colspan="4" class="text-end"><strong>TOTAL:</strong></td>
                                        <td class="text-end"><strong>0.00</strong></td>
                                        <td class="text-end"><strong>0.00</strong></td>
                                    </tr>
                                    </tbody>
                                </table>

                                <div class="form-check mb-4">
                                    <input type="checkbox" class="form-check-input" id="save-draft">
                                    <label for="save-draft" class="form-check-label">Save as draft?</label>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success me-2">Post</button>
                                    <button type="button" class="btn btn-danger">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="assets/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="assets/js/pages/dashboard.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/center.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2-account').select2({
                placeholder: 'Select an account',
                allowClear: true
            });

            $('.btn-add-line').click(function() {
                let newRow = `<tr>
                    <td class="text-center">
                        <span class="move-up-down">&#8597;</span>
                    </td>
                    <td><input type="text" class="form-control" placeholder="Description"></td>
                    <td><select class="form-select select2-account"></select></td>
                    <td><input type="text" class="form-control" placeholder="Tax rate"></td>
                    <td><input type="text" class="form-control" placeholder="Debit"></td>
                    <td><input type="text" class="form-control" placeholder="Credit"></td>
                    <td class="text-center">
                        <span class="remove-row">&#10060;</span>
                    </td>
                </tr>`;
                $('table tbody').append(newRow);
                $('.select2-account').select2({
                    placeholder: 'Select an account',
                    allowClear: true
                });
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
            });
        });
    </script>
@endsection
