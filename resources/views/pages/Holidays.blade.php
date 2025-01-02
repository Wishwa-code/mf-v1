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
                    Holiday Table
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="poya-tab" data-bs-toggle="tab" data-bs-target="#poya" type="button" role="tab" aria-controls="poya" aria-selected="false">
                    Poya Days
                </button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="myTabContent">
            <!-- Holiday Table Tab -->
            <div class="tab-pane fade show active" id="holidays" role="tabpanel" aria-labelledby="holidays-tab">
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <h4 class="page-title">Holiday Details</h4>
                                </div>

                                <!-- Holiday Form -->
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

                                <!-- Holiday Table -->
                                <div class="table-responsive-sm">
                                    <table class="table table-centered mb-0" id="holiday_table">
                                        <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Reason</th>
                                            <th style="text-align: center">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($holidays as $item)
                                            <tr>
                                                <td>{{$item->date}}</td>
                                                <td>{{$item->reason}}</td>
                                                <td style="text-align: center">
                                                    <a href="#" class="btn btn-danger btn-sm" onclick="deleteHolidays({{$item->id_holidays}})">Delete</a>
                                                </td>
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

            <!-- Poya Days Tab -->
            <!-- Poya Days Tab -->
            <div class="tab-pane fade" id="poya" role="tabpanel" aria-labelledby="poya-tab">
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="page-title">Poya Days (Full Moons)</h4>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-primary" id="addWeekendDays">Add Weekend Days</button> <!-- New Button -->
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
            // Initialize DataTables for both tables
            $('#holiday_table').DataTable();

            // Function to calculate full moons
            function calculateFullMoons(year) {
                const fullMoons = [];
                const startDate = new Date(`${year}-01-01`);
                const endDate = new Date(`${year}-12-31`);

                let currentDate = startDate;

                while (currentDate <= endDate) {
                    const moonData = SunCalc.getMoonIllumination(currentDate);

                    if (moonData.fraction > 0.99) {
                        fullMoons.push({
                            date: new Date(currentDate).toISOString().split('T')[0],
                            description: "Full Moon"
                        });
                        currentDate.setDate(currentDate.getDate() + 29);
                    } else {
                        currentDate.setDate(currentDate.getDate() + 1);
                    }
                }

                return fullMoons;
            }

            // Load Poya Days
            const year = new Date().getFullYear();
            const poyaDays = calculateFullMoons(year);
            $('#year').text(year);

            poyaDays.forEach(day => {
                $('#poya-days').append(`<tr><td>${day.date}</td><td>${day.description}</td></tr>`);
            });

            // Save Poya Days
            $('#savePoyaDays').click(function () {
                const poyaDays = []; // Prepare the Poya Days array
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

            // Function to calculate weekend days (Saturdays and Sundays)
            function calculateWeekendDays(year) {
                const weekendDays = [];
                const startDate = new Date(`${year}-01-01`);
                const endDate = new Date(`${year}-12-31`);

                let currentDate = startDate;

                while (currentDate <= endDate) {
                    const dayOfWeek = currentDate.getDay(); // 0 for Sunday, 6 for Saturday

                    if (dayOfWeek === 0 || dayOfWeek === 6) { // Check if it's Saturday (6) or Sunday (0)
                        weekendDays.push({
                            date: new Date(currentDate).toISOString().split('T')[0], // Format as YYYY-MM-DD
                            description: dayOfWeek === 0 ? "Sunday" : "Saturday"
                        });
                    }

                    currentDate.setDate(currentDate.getDate() + 1); // Move to the next day
                }

                return weekendDays;
            }

// Event listener for adding weekend days
            $('#addWeekendDays').click(function () {
                const year = new Date().getFullYear(); // Get the current year
                const weekendDays = calculateWeekendDays(year); // Get the weekend days

                weekendDays.forEach(day => {
                    // Append each weekend day to the table
                    $('#poya-days').append(`<tr><td>${day.date}</td><td>${day.description}</td></tr>`);
                });

                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Weekend days added to the table!",
                    showConfirmButton: false,
                    timer: 1500,
                });
            });

        });
    </script>
@endsection
