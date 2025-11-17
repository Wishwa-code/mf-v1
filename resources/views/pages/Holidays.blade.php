@extends('layout.admin')

@section('head')
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">

    <style>
        .page-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: .75rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        .card-body {
            padding: 1.25rem 1.5rem;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
            font-weight: 600;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .table thead th {
            background-color: #007bff;
            color: #fff;
            font-weight: 600;
            border: none;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .nav-tabs .nav-link {
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            font-weight: 600;
        }

        .warning-text {
            font-size: 0.9rem;
        }

        .sticky-actions-right {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .sticky-actions-right .btn {
            white-space: nowrap;
        }

        .due-skip-note {
            font-size: 0.85rem;
            color: #6c757d;
        }
    </style>
@endsection

@section('content')
    <div>
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active"
                        id="holidays-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#holidays"
                        type="button"
                        role="tab"
                        aria-controls="holidays"
                        aria-selected="true">
                    Customize Holidays
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link"
                        id="poya-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#poya"
                        type="button"
                        role="tab"
                        aria-controls="poya"
                        aria-selected="false">
                    Holidays
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link"
                        id="special-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#special"
                        type="button"
                        role="tab"
                        aria-controls="special"
                        aria-selected="false">
                    Due Skip Process
                </button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="myTabContent">

            {{-- ========== TAB 1: CUSTOMIZE HOLIDAYS ========== --}}
            <div class="tab-pane fade show active"
                 id="holidays"
                 role="tabpanel"
                 aria-labelledby="holidays-tab">
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="page-title mb-0">Holiday Details</h4>
                                </div>

                                <div class="alert alert-warning border-warning" role="alert">
                                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                    <span class="warning-text">
                                        Once a holiday is added, it cannot be removed because installment dates depend on it.
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="holiday_date" class="form-label">Date</label>
                                            <input type="date" id="holiday_date" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="account_name" class="form-label">Reason</label>
                                            <input type="text" id="account_name" class="form-control" placeholder="Reason for holiday">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="button"
                                                    class="btn btn-success w-100"
                                                    id="addBankBtn"
                                                    onclick="validateSubmitBank(event)">
                                                <i class="fa-solid fa-floppy-disk me-1"></i> Save
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive-sm">
                                    <table class="table table-centered mb-0" id="holiday_table">
                                        <thead>
                                        <tr>
                                            <th style="width: 160px;">Date</th>
                                            <th>Reason</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($holidays as $item)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
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

            {{-- ========== TAB 2: HOLIDAYS / POYA DAYS ========== --}}
            <div class="tab-pane fade"
                 id="poya"
                 role="tabpanel"
                 aria-labelledby="poya-tab">
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="page-title mb-0">Poya Days (Full Moons)</h4>
                                    <span class="text-muted small">Year: {{ $year }}</span>
                                </div>

                                <div class="d-flex justify-content-end mb-2 sticky-actions-right">
                                    <button class="btn btn-dark btn-sm" id="addPoyaDays">
                                        <i class="fa-solid fa-moon me-1"></i> Add Poya Days
                                    </button>
                                    <button class="btn btn-warning btn-sm" id="addWeekendDays">
                                        <i class="fa-solid fa-calendar-days me-1"></i> Add Weekend Days
                                    </button>
                                    <button class="btn btn-primary btn-sm" id="addOnlySaturdays">
                                        <i class="fa-solid fa-calendar-day me-1"></i> Add Only Saturdays
                                    </button>
                                    <button class="btn btn-info btn-sm text-white" id="addOnlySundays">
                                        <i class="fa-solid fa-sun me-1"></i> Add Only Sundays
                                    </button>
                                    <button class="btn btn-danger btn-sm" id="savePoyaDays">
                                        <i class="fa-solid fa-floppy-disk me-1"></i> Save All
                                    </button>
                                </div>

                                <div class="table-responsive-sm mt-3">
                                    <table class="table table-centered mb-0" id="poya_table">
                                        <thead>
                                        <tr>
                                            <th style="width: 160px;">Date</th>
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

            {{-- ========== TAB 3: DUE SKIP PROCESS ========== --}}
            <div class="tab-pane fade"
                 id="special"
                 role="tabpanel"
                 aria-labelledby="special-tab">
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="page-title mb-0">Due Skip Process</h4>
                                    <div class="text-end">
                                        <div class="small text-muted">Installments falling on holidays:</div>
                                        <div class="fs-5 fw-semibold text-primary">
                                            {{ $holidayWithInstallmentsCount }}
                                        </div>
                                    </div>
                                </div>
                                <p class="due-skip-note mb-3">
                                    Use this tool to automatically adjust installments that fall on holidays.
                                    Choose the target (all loans / specific loan / product, etc.) and how you want to skip them.
                                </p>

                                <div class="row">
                                    <!-- Left Table -->
                                    <div class="col-md-6">
                                        <div class="card mb-0">
                                            <div class="card-body p-2">
                                                <h6 class="fw-semibold mb-2">Holiday List</h6>
                                                <div class="table-responsive-sm" style="max-height: 400px; overflow-y: auto;">
                                                    <table class="table table-bordered mb-0">
                                                        <thead>
                                                        <tr class="table-primary">
                                                            <th style="width: 140px;">Date</th>
                                                            <th>Note</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="due-skip-table-body">
                                                        @foreach($holidays as $item)
                                                            <tr>
                                                                <td>{{ \Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                                                                <td>{{ $item->reason }}</td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Side Options -->
                                    <div class="col-md-6">
                                        <div class="card mb-0">
                                            <div class="card-body">
                                                <h6 class="fw-semibold mb-3">Due Skip Options</h6>

                                                <div class="mb-3">
                                                    <label for="skipFor" class="form-label">Skip Due For</label>
                                                    <select id="skipFor" class="form-select">
                                                        <option value="all">All Loans</option>
                                                        <option value="loan">Specific Loan</option>
                                                        {{-- <option value="branch">Specific Branch</option> --}}
                                                        {{-- <option value="center">Specific Center</option> --}}
                                                        <option value="product">Specific Product</option>
                                                    </select>
                                                </div>

                                                <!-- Target dropdowns -->
                                                <div class="mb-3 d-none target-dropdown" id="loanSelectWrapper">
                                                    <label class="form-label">Select Loan</label>
                                                    <select id="loanSelect" class="form-select">
                                                        @foreach($loans as $loan)
                                                            <option value="{{ $loan->idCustomer_Loan }}">{{ $loan->Loan_No }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3 d-none target-dropdown" id="branchSelectWrapper">
                                                    <label class="form-label">Select Branch</label>
                                                    <select id="branchSelect" class="form-select">
                                                        @foreach($branches as $branch)
                                                            <option value="{{ $branch->branch_id }}">{{ $branch->Name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3 d-none target-dropdown" id="centerSelectWrapper">
                                                    <label class="form-label">Select Center</label>
                                                    <select id="centerSelect" class="form-select">
                                                        @foreach($centers as $center)
                                                            <option value="{{ $center->idCenter }}">{{ $center->Name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3 d-none target-dropdown" id="productSelectWrapper">
                                                    <label class="form-label">Select Product</label>
                                                    <select id="productSelect" class="form-select">
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->idLoan_Category }}">{{ $product->Name }}</option>
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

                                                <button class="btn btn-primary w-100" onclick="generateDueSkip()">
                                                    <i class="fa-solid fa-gears me-1"></i> Generate Due Skip
                                                </button>
                                                <p class="due-skip-note mt-2 mb-0">
                                                    This process may take a few moments depending on the number of affected installments.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- /row -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- /tab-content -->
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/suncalc/suncalc.min.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/holiday.js"></script>

    <script>
        $(document).ready(function () {
            $('#holiday_table').DataTable();

            const year = new Date().getFullYear();

            // ----------- Poya Days -----------
            $('#addPoyaDays').click(function () {
                fetch('/poya-days')
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(day => {
                            const y = day[0];
                            const m = String(day[1]).padStart(2, '0');
                            const d = String(day[2]).padStart(2, '0');
                            const fullDate = `${y}-${m}-${d}`;

                            if (!isDateInTable(fullDate)) {
                                $('#poya-days').append(
                                    `<tr><td>${fullDate}</td><td>Full Moon</td></tr>`
                                );
                            }
                        });

                        Swal.fire({
                            icon: "success",
                            title: "Poya Days added!",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching Poya days:', error);
                        Swal.fire({
                            icon: "error",
                            title: "Failed to load Poya Days",
                            text: "Please try again later."
                        });
                    });

            });

            // show / hide target dropdowns
            $('#skipFor').on('change', function () {
                const value = $(this).val();
                $('.target-dropdown').addClass('d-none');

                switch (value) {
                    case 'loan':
                        $('#loanSelectWrapper').removeClass('d-none');
                        break;
                    case 'branch':
                        $('#branchSelectWrapper').removeClass('d-none');
                        break;
                    case 'center':
                        $('#centerSelectWrapper').removeClass('d-none');
                        break;
                    case 'product':
                        $('#productSelectWrapper').removeClass('d-none');
                        break;
                }
            });

            $('#savePoyaDays').click(function () {
                const poyaDays = [];
                $("#poya-days tr").each(function () {
                    const date = $(this).find("td:first").text();
                    const description = $(this).find("td:last").text();
                    if (date) {
                        poyaDays.push({ date, description });
                    }
                });

                if (poyaDays.length === 0) {
                    Swal.fire("No Data", "Please add at least one date before saving.", "warning");
                    return;
                }

                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to save all Poya Days?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, Save them!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "POST",
                            url: "/poya-days/save",
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            },
                            data: { poyaDays },
                            success: function (response) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: response.message || "Poya Days saved!",
                                    showConfirmButton: false,
                                    timer: 1500,
                                }).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function () {
                                Swal.fire("Error!", "Failed to save Poya Days!", "error");
                            },
                        });
                    }
                });
            });

            function isDateInTable(date) {
                let exists = false;
                $('#poya-days tr').each(function () {
                    const existingDate = $(this).find('td:first').text();
                    if (existingDate === date) {
                        exists = true;
                        return false;
                    }
                });
                return exists;
            }

            function calculateWeekendDays(year) {
                const weekendDays = [];
                const startDate = new Date(`${year}-01-01`);
                const endDate = new Date(`${year}-12-31`);
                let currentDate = startDate;

                while (currentDate <= endDate) {
                    const dayOfWeek = currentDate.getDay(); // 0 Sun, 6 Sat
                    if (dayOfWeek === 0 || dayOfWeek === 6) {
                        const formattedDate = currentDate.toISOString().split('T')[0];
                        if (!isDateInTable(formattedDate)) {
                            weekendDays.push({
                                date: formattedDate,
                                description: dayOfWeek === 0 ? "Sunday" : "Saturday"
                            });
                        }
                    }
                    currentDate.setDate(currentDate.getDate() + 1);
                }

                return weekendDays;
            }

            $('#addWeekendDays').click(function () {
                const weekendDays = calculateWeekendDays(year);
                weekendDays.forEach(day => {
                    $('#poya-days').append(`<tr><td>${day.date}</td><td>${day.description}</td></tr>`);
                });
                Swal.fire({ position: "center", icon: "success", title: "Weekend days added!", showConfirmButton: false, timer: 1500 });
            });

            $('#addOnlySaturdays').click(function () {
                const saturdays = calculateWeekendDays(year).filter(d => d.description === 'Saturday');
                saturdays.forEach(day => {
                    $('#poya-days').append(`<tr><td>${day.date}</td><td>${day.description}</td></tr>`);
                });
                Swal.fire({ position: 'center', icon: 'success', title: 'Saturdays added!', showConfirmButton: false, timer: 1500 });
            });

            $('#addOnlySundays').click(function () {
                const sundays = calculateWeekendDays(year).filter(d => d.description === 'Sunday');
                sundays.forEach(day => {
                    $('#poya-days').append(`<tr><td>${day.date}</td><td>${day.description}</td></tr>`);
                });
                Swal.fire({ position: 'center', icon: 'success', title: 'Sundays added!', showConfirmButton: false, timer: 1500 });
            });
        });

        function generateDueSkip() {
            const skipFor = $('#skipFor').val();
            const skipType = $('#skipType').val();

            let targetValue = null;
            let targetText  = null;

            if (skipFor === 'loan') {
                targetValue = $('#loanSelect').val();
                targetText  = $('#loanSelect option:selected').text();
            } else if (skipFor === 'branch') {
                targetValue = $('#branchSelect').val();
                targetText  = $('#branchSelect option:selected').text();
            } else if (skipFor === 'center') {
                targetValue = $('#centerSelect').val();
                targetText  = $('#centerSelect option:selected').text();
            } else if (skipFor === 'product') {
                targetValue = $('#productSelect').val();
                targetText  = $('#productSelect option:selected').text();
            }

            if (skipFor !== 'all' && (!targetValue || targetValue === '')) {
                Swal.fire("Required", "Please select a target before generating due skip.", "warning");
                return;
            }

            let displayText = `<b>Skip For:</b> ${skipFor}<br>`;
            if (targetValue) {
                displayText += `<b>Target:</b> ${targetText} (ID: ${targetValue})<br>`;
            }
            displayText += `<b>Skip Type:</b> ${skipType}`;

            Swal.fire({
                title: 'Generate Due Skip',
                html: displayText,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Proceed',
            }).then((result) => {
                if (!result.isConfirmed) return;

                // Show progress modal
                let progressInterval;
                let progressVal = 10;

                Swal.fire({
                    title: 'Processing Due Skip...',
                    html: `
                <div class="mt-2 mb-1 small text-muted">
                    Please wait while we update affected installments.
                </div>
                <div class="progress mt-2" style="height: 18px;">
                    <div id="dueSkipProgressBar"
                         class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar"
                         style="width: 10%;"
                         aria-valuenow="10"
                         aria-valuemin="0"
                         aria-valuemax="100">
                    </div>
                </div>
            `,
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        progressInterval = setInterval(() => {
                            progressVal = Math.min(progressVal + 5, 95);
                            $('#dueSkipProgressBar')
                                .css('width', progressVal + '%')
                                .attr('aria-valuenow', progressVal);
                        }, 400);
                    },
                    willClose: () => {
                        if (progressInterval) clearInterval(progressInterval);
                    }
                });

                $.ajax({
                    url: '/generate-due-skip',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        skip_for:  skipFor,
                        target_id: targetValue,
                        skip_type: skipType,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        $('#dueSkipProgressBar').css('width', '100%').attr('aria-valuenow', 100);

                        setTimeout(() => {
                            Swal.fire({
                                title: 'Success',
                                text: response.message || 'Due skip processed successfully.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        }, 300);
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Something went wrong while processing due skip.',
                            icon: 'error'
                        });
                    }
                });
            });
        }


        // (Optional) helper for "special holidays" if you later use that section
        function addSpecialHoliday() {
            const name = $('#special_name').val();
            const start = $('#special_start').val();
            const end = $('#special_end').val();
            const remarks = $('#special_remarks').val();

            if (!name || !start || !end) {
                Swal.fire("Missing Fields", "Please fill all required fields", "warning");
                return;
            }

            const row = `<tr><td>${name}</td><td>${start}</td><td>${end}</td><td>${remarks}</td></tr>`;
            $('#special-body').append(row);

            $('#special_name').val('');
            $('#special_start').val('');
            $('#special_end').val('');
            $('#special_remarks').val('');

            Swal.fire({ position: 'center', icon: 'success', title: 'Special Holiday added!', showConfirmButton: false, timer: 1500 });
        }
    </script>
@endsection
