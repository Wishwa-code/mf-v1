@extends('layout.admin')

@section('head')
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <!-- Custom CSS -->
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
    </style>
@endsection

@section('content')
    <div>
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="holidays-tab" data-bs-toggle="tab" data-bs-target="#holidays" type="button" role="tab" aria-controls="holidays" aria-selected="true">
                    Customize Holidays
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="poya-tab" data-bs-toggle="tab" data-bs-target="#poya" type="button" role="tab" aria-controls="poya" aria-selected="false">
                    Holidays
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="special-tab" data-bs-toggle="tab" data-bs-target="#special" type="button" role="tab" aria-controls="special" aria-selected="false">
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
                                <h5 style="color: #ff0000">"Once a holiday is added, it cannot be removed as installment dates depend on it."</h5>
                                <br><br><br>
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
                                    <button type="button" class="btn btn-success" id="addBankBtn" onclick="validateSubmitBank(event)">Save Holiday</button>
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
                                                <td>{{$item->date}}</td>
                                                <td>{{$item->reason}}</td>
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
                                <h4 class="page-title">Poya Days (Full Moons)</h4>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-dark ms-2" id="addPoyaDays">Add Poya Days</button>
                                    <button class="btn btn-warning ms-2" id="addWeekendDays">Add Weekend Days</button>
                                    <button class="btn btn-primary ms-2" id="addOnlySaturdays">Add Only Saturdays</button>
                                    <button class="btn btn-info ms-2" id="addOnlySundays">Add Only Sundays</button>
                                    <button class="btn btn-danger ms-2" id="savePoyaDays">Save All</button>
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

            <!-- Special Holidays Tab -->
            <div class="tab-pane fade" id="special" role="tabpanel" aria-labelledby="due-tab">
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="page-title">Due Skip Process</h4>
                                <div class="row">
                                    <!-- Left Table -->
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
                                                        <td>{{$item->date}}</td>
                                                        <td>{{$item->reason}}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Right Side Options -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Installment Count in holidays:</label>
                                            <div id="installment-count" class="fs-5">{{$holidayWithInstallmentsCount}}</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="skipFor" class="form-label">Skip Due For</label>
                                            <select id="skipFor" class="form-select">
                                                <option value="all">All Loans</option>
                                                <option value="loan">Specific Loan</option>
                                                <option value="branch">Specific Branch</option>
                                                <option value="center">Specific Center</option>
                                                <option value="product">Specific Product</option>
                                            </select>
                                        </div>

                                        <!-- All Target Dropdowns (Loaded at start, hidden initially) -->
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
                                                <option value="installment">Skip An Installment</option>
                                                <option value="day">Skip a Day</option>
                                            </select>
                                        </div>

                                        <button class="btn btn-primary" onclick="generateDueSkip()">Generate Due Skip</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>
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


            $('#addPoyaDays').click(function () {
                fetch('/poya-days')
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(day => {
                            const year = day[0];
                            const month = String(day[1]).padStart(2, '0');
                            const date = String(day[2]).padStart(2, '0');
                            const fullDate = `${year}-${month}-${date}`;

                            $('#poya-days').append(
                                `<tr><td>${fullDate}</td><td>Full Moon</td></tr>`
                            );
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


            // Handle show/hide of relevant dropdown
            $('#skipFor').on('change', function () {
                const value = $(this).val();

                // Hide all
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
                                    title: response.message,
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
                    const dayOfWeek = currentDate.getDay();
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
            let targetText = null;

            // Get the corresponding target value based on skipFor
            if (skipFor === 'loan') {
                targetValue = $('#loanSelect').val();
                targetText = $('#loanSelect option:selected').text();
            } else if (skipFor === 'branch') {
                targetValue = $('#branchSelect').val();
                targetText = $('#branchSelect option:selected').text();
            } else if (skipFor === 'center') {
                targetValue = $('#centerSelect').val();
                targetText = $('#centerSelect option:selected').text();
            } else if (skipFor === 'product') {
                targetValue = $('#productSelect').val();
                targetText = $('#productSelect option:selected').text();
            }

            // Sample confirmation using SweetAlert
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
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/generate-due-skip',
                        type: 'POST',
                        data: {
                            skip_for: skipFor,
                            target_id: targetValue,
                            skip_type: skipType,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            Swal.fire({
                                title: 'Success',
                                text: 'Due skip processed successfully',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                title: 'Error',
                                text: 'Something went wrong!',
                                icon: 'error'
                            });
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        }


        // JS function to handle adding special holidays
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
