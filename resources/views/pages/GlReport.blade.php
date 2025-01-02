@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

@endsection


@section('content')
    <div class="container mt-4">
        <h2 class="text-center mb-4">General Ledger Report</h2>

        <!-- Filters Section -->
        <div class="card mb-3">
            <div class="card-body">
                <form id="filter-form">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="date-range" class="form-label">Date Range</label>
                            <input type="date" id="start-date" name="start_date" class="form-control mb-2" placeholder="Start Date">
                            <input type="date" id="end-date" name="end_date" class="form-control" placeholder="End Date">
                        </div>
                        <div class="col-md-4">
                            <label for="account-type" class="form-label">Account Type</label>
                            <select id="account-type" name="account_type" class="form-select">
                                <option value="">Select Account Type</option>
                                <option value="savings">Savings</option>
                                <option value="loans">Loans</option>
                                <option value="expenses">Expenses</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="branch" class="form-label">Branch</label>
                            <select id="branch" name="branch" class="form-select">
                                <option value="">Select Branch</option>
                                <option value="branch1">Branch 1</option>
                                <option value="branch2">Branch 2</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mt-3">Apply Filters</button>
                </form>
            </div>
        </div>

        <!-- General Ledger Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>General Ledger</span>
                <div>
                    <button class="btn btn-success btn-sm">Export to Excel</button>
                    <button class="btn btn-danger btn-sm">Export to PDF</button>
                    <button class="btn btn-secondary btn-sm">Print</button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Account Name</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Replace with dynamic data -->
                    <tr>
                        <td>2024-11-01</td>
                        <td>Cash</td>
                        <td>1000</td>
                        <td>0</td>
                        <td>1000</td>
                    </tr>
                    <tr>
                        <td>2024-11-02</td>
                        <td>Rent</td>
                        <td>0</td>
                        <td>500</td>
                        <td>500</td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="2" class="text-end">Total:</th>
                        <th>1000</th>
                        <th>500</th>
                        <th>500</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="assets/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="assets/js/pages/dashboard.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
@endsection
