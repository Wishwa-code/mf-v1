@extends('layout.admin')

@section('head')
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <style>
        .page-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #343a40;
        }

        .card {
            border: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
            font-weight: bold;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .table thead th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
@endsection

@section('content')
    <div>
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="holidays-tab" data-bs-toggle="tab" data-bs-target="#holidays"
                        type="button" role="tab" aria-controls="holidays" aria-selected="true">
                    Customize Holidays
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="poya-tab" data-bs-toggle="tab" data-bs-target="#poya"
                        type="button" role="tab" aria-controls="poya" aria-selected="false">
                    Holidays
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="special-tab" data-bs-toggle="tab" data-bs-target="#special"
                        type="button" role="tab" aria-controls="special" aria-selected="false">
                    Due Skip Process
                </button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="myTabContent">
            <!-- Customize Holidays Tab -->
            <div class="tab-pane fade show active" id="holidays" role="tabpanel" aria-labelledby="holidays-tab">
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <h4 class="page-title">Holiday Details</h4>
                                </div>
                                <h5 class="text-danger">
                                    Once a holiday is added, it should not be removed as installment dates depend on it.
                                </h5>
                                <br>

                                <div class="mb-3">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="holiday_date" class="form-label">Date</label>
                                            <input type="date" id="holiday_date" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="account_name" class="form-label">Reason</label>
                                            <input type="text" id="account_name" class="form-control">
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-success" id="addBankBtn"
                                            onclick="validateSubmitBank(event)">
                                        Save Holiday
                                    </button>
                                </div>

                                <div class="table-responsive-sm">
                                    <table class="table table-centered mb-0" id="holiday_table">
                                        <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Reason</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($holidays as $item)
                                            <tr>
                                                <td>{{ $item->date }}</td>
                                                <td>{{ $item->reason }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Holidays Tab (Poya Days) -->
            <div class="tab-pane fade" id="poya" role="tabpanel" aria-labelledby="poya-tab">
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="page-title">Poya Days / Weekends</h4>
                                <div class="d-flex flex-wrap justify-content-end gap-2">
                                    <button class="btn btn-dark" id="addPoyaDays">Add Poya Days</button>
                                    <button class="btn btn-warning" id="addWeekendDays">Add Weekend Days</button>
                                    <button class="btn btn-primary" id="addOnlySaturdays">Add Only Saturdays</button>
                                    <button class="btn btn-info" id="addOnlySundays">Add Only Sundays</button>
                                    <button class="btn btn-danger" id="savePoyaDays">Save All</button>
                                </div>

                                <div class="table-responsive-sm mt-3">
                                    <table class="table table-centered mb-0" id="poya_table">
                                        <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                        </tr>
                                        </thead>
                                        <tbody id="poya-days"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Due Skip Process Tab -->
            <div class="tab-pane fade" id="special" role="tabpanel" aria-labelledby="due-tab">
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="page-title">Due Skip Process</h4>
                                <div class="row">
                                    <!-- Left Table: Holiday List -->
                                    <div class="col-md-6">
                                        <div class="table-responsive-sm" style="max-height: 400px; overflow-y: auto;">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr class="table-primary">
                                                    <th>Date</th>
                                                    <th>Note</th>
                                                </tr>
                                                </thead>
                                                <tbody id="due-skip-table-body">
                                                @foreach($holidays as $item)
                                                    <tr>
                                                        <td>{{ $item->date }}</td>
                                                        <td>{{ $item->reason }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Right Side Options -->
                                    <div class="col-md-6">
                                        <div class="mb-3" hidden>
                                            <label class="form-label fw-bold">Installment Count in holidays:</label>
                                            <div id="installment-count" class="fs-5">
                                                {{ $holidayWithInstallmentsCount ?? 0 }}
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="skipFor" class="form-label">Skip Due For</label>
                                            <select id="skipFor" class="form-select">
                                                <option value="all">All Loans</option>
                                                <option value="loan">Specific Loan</option>
                                                <option value="center">Specific Center</option>
                                                <option value="product">Specific Product</option>
                                            </select>
                                        </div>

                                        <!-- Target Dropdowns -->
                                        <div class="mb-3 d-none target-dropdown" id="loanSelectWrapper">
                                            <label class="form-label">Select Loan</label>
                                            <select id="loanSelect" class="form-select select2-single">
                                                <option value="">-- Select Loan --</option>
                                                @foreach($loans as $loan)
                                                    <option value="{{ $loan->idCustomer_Loan }}">
                                                        {{ $loan->Loan_No }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>



                                        <div class="mb-3 d-none target-dropdown" id="centerSelectWrapper">
                                            <label class="form-label">Select Center</label>
                                            <select id="centerSelect" class="form-select select2-single">
                                                <option value="">-- Select Center --</option>
                                                @foreach($centers as $center)
                                                    <option value="{{ $center->idCenter }}">
                                                        {{ $center->Name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3 d-none target-dropdown" id="productSelectWrapper">
                                            <label class="form-label">Select Product</label>
                                            <select id="productSelect" class="form-select select2-single">
                                                <option value="">-- Select Product --</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->idLoan_Category }}">
                                                        {{ $product->Name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="skipType" class="form-label">Skip Type</label>
                                            <select id="skipType" class="form-select">
                                                <option value="installment">Skip an Installment</option>
                                                <option value="day">Skip a Day</option>
                                            </select>
                                        </div>

                                        <button class="btn btn-primary" id="btnGenerateDueSkip">
                                            Generate Due Skip
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Progress Modal -->
    <div class="modal fade" id="dueSkipProgressModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Processing Due Skip</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2 small text-muted">
                        Please wait while the system processes the due skip for selected loans.
                    </p>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                             id="dueSkipProgressBar"
                             role="progressbar" style="width: 0%">0%
                        </div>
                    </div>
                    <p class="mt-3 text-center small" id="dueSkipStatusText">
                        Initializing...
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- SunCalc (if you still need it) -->
    <script src="https://cdn.jsdelivr.net/npm/suncalc/suncalc.min.js"></script>
    <!-- Your validation helper -->
    <script src="{{ asset('JS/validate.js') }}"></script>
    <!-- Holiday JS -->
    <script src="{{ asset('JS/holiday.js') }}"></script>
@endsection
