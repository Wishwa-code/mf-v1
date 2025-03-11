@extends('layout.admin')

@section('head')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        /* Container Styling */
        .content-container {
            background-color: #fff;
            border-radius: 6px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .content-header h2 {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .btn {
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            font-weight: 500;
        }

        .btn-success {
            background-color: #28a745;
            color: #fff;
        }
        .btn-success:hover {
            background-color: #218838;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .btn-primary {
            background-color: #007bff;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #0069d9;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        /* Tabs and Export Section */
        .tabs-section {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .tabs-section .btn-secondary {
            border-radius: 0;
        }

        .tabs-section .btn-secondary:first-child {
            border-top-left-radius: 6px;
            border-bottom-left-radius: 6px;
        }

        .tabs-section .btn-secondary:last-child {
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
        }

        .tabs-section .btn-secondary.active {
            background-color: #343a40;
        }

        .export-btn {
            margin-left: 20px;
        }

        /* Search and Reset */
        .search-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-box input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        /* Table Styling */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: #fff;
            border-radius: 6px;
            overflow: hidden;
        }

        .table thead {
            background-color: #f5f5f5;
        }

        .table th,
        .table td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
            color: #333;
        }

        .table tbody tr {
            transition: background-color 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f9f9f9;
        }

        .close {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #333;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #000;
        }

        /* Modals */
        .modal, .modal_2 {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            align-items: center;
            justify-content: center;
        }

        .modal-content, .modal_2-content {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.1);
        }

        /* Medium Modal */
        .modal-content-medium {
            width: 600px;
            max-width: 90%;
        }

        /* Large Modal */
        .large-modal-content {
            width: 900px;
            max-width: 90%;
            height: auto;
            overflow-y: auto;
        }

        .modal-header, .modal_2-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .modal-header h5, .modal_2-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .modal-body .form-group,
        .modal_2-body .dropdown-section {
            margin-bottom: 15px;
        }

        .modal-body label,
        .modal_2-body label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }

        .modal-body input,
        .modal-body select,
        .modal_2-body select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .modal-body input:focus,
        .modal-body select:focus,
        .modal_2-body select:focus {
            border-color: #999;
            outline: none;
        }

        .modal-footer,
        .modal_2-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .modal-footer .btn,
        .modal_2-footer .btn {
            margin-left: 10px;
        }

        .ending-balance {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }

        /* Hide tables except the active one */
        .tab-table {
            display: none;
        }

        .tab-table.active {
            display: table;
        }
        .search-section .form-label {
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .search-section .btn {
            height: 38px; /* Matches input height */
            padding: 8px 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

    </style>
@endsection

@section('content')
    <div class="content-container">
        <!-- Header Section -->
        <div class="content-header">
            <h2>View Manual Journal</h2>
{{--            <button class="btn btn-primary" id="exportBtn">Export Data</button>--}}
        </div>

        <!-- Search Section -->
        <div class="search-section mb-4">
            <form class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="journalNarration" class="form-label">Journal Narration</label>
                    <input type="text" class="form-control" id="journalNarration" placeholder="Journal Narration">
                </div>
                <div class="col-md-2">
                    <label for="fromDate" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="fromDate">
                </div>
                <div class="col-md-2">
                    <label for="toDate" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="toDate">
                </div>
                <div class="col-md-2">
                    <label for="fromAmount" class="form-label">From Amount</label>
                    <input type="number" class="form-control" id="fromAmount" placeholder="From Amount">
                </div>
                <div class="col-md-2">
                    <label for="toAmount" class="form-label">To Amount</label>
                    <input type="number" class="form-control" id="toAmount" placeholder="To Amount">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-success w-100" id="searchButton">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>



        <!-- Add Journal Button -->
        <div class="d-flex justify-content-end mb-4">
            <a href="/AddJournal" class="btn btn-success" id="addJournalBtn">Add Journal</a>
        </div>

        <!-- Tabs Section -->
        <div class="tabs-section d-flex mb-3">
            <button class="btn btn-secondary me-2 active" data-tab="posted">Posted</button>
            <button class="btn btn-secondary" data-tab="deleted">Deleted</button>
        </div>

        <!-- Table Section -->
        <div>
            <!-- Posted Table -->
            <table class="table table-striped tab-table active" id="postedTable">
                <thead>
                <tr>
                    <th>Narration</th>
                    <th>Journal Date</th>
                    <th>Amount</th>
                    <th>Created Time</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>


            <!-- Deleted Table -->
            <table class="table table-striped tab-table" id="deletedTable">
                <thead>
                <tr>
                    <th>Narration</th>
                    <th>Journal Date</th>
                    <th>Amount</th>
                    <th>Created Time</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
    <div class="modal" id="viewDetailsModal">
        <div class="modal-content large-modal-content">
            <div class="modal-header">
                <h5>Journal Details</h5>
                <button class="close" id="closeDetailsModal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="detailsTable">
                    <thead>
                    <tr>
                        <th>Description</th>
                        <th>Account</th>

                        <th>Debit Amount</th>
                        <th>Credit Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Rows will be populated dynamically -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="closeDetailsFooterModal">Close</button>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script>
        $(document).ready(function () {
            localStorage.removeItem("editJournalData");
        });
        document.addEventListener("DOMContentLoaded", function () {
            const tabs = document.querySelectorAll(".tabs-section .btn");
            const tables = document.querySelectorAll(".tab-table");

            // Tab switching logic
            tabs.forEach(tab => {
                tab.addEventListener("click", function () {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove("active"));

                    // Add active class to the clicked tab
                    this.classList.add("active");

                    // Hide all tables and show the corresponding one
                    const tabName = this.getAttribute("data-tab");
                    tables.forEach(table => {
                        if (table.id === `${tabName}Table`) {
                            table.classList.add("active");
                        } else {
                            table.classList.remove("active");
                        }
                    });
                });
            });

            // Export Data to Excel
            document.getElementById("exportBtn").addEventListener("click", function () {
                const activeTable = document.querySelector(".tab-table.active");
                if (!activeTable) return;

                // Convert table to worksheet
                const worksheet = XLSX.utils.table_to_sheet(activeTable);

                // Create a new workbook and append the worksheet
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, "Exported Data");

                // Generate Excel file and download
                XLSX.writeFile(workbook, "ExportedData.xlsx");
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            const postedTableBody = document.querySelector("#postedTable tbody");
            const deletedTableBody = document.querySelector("#deletedTable tbody");

            // Function to fetch and filter data
            function fetchData(filters = {}) {
                $.ajax({
                    url: "{{ route('manual_journal.fetch') }}",
                    method: "GET",
                    data: filters, // Pass filters as query parameters
                    success: function (response) {
                        populateTable(postedTableBody, response.posted);
                        populateTable(deletedTableBody, response.deleted);
                    },
                    error: function () {
                        console.error("Failed to fetch data");
                    }
                });
            }

            function populateTable(tableBody, data) {
                tableBody.innerHTML = ""; // Clear existing rows

                if (data.length === 0) {
                    tableBody.innerHTML = "<tr><td colspan='6' class='text-center'>No records found</td></tr>";
                    return;
                }

                data.forEach((item) => {
                    const row = `
        <tr>
            <td>${item.narration}</td>
            <td>${item.date}</td>
            <td>${parseFloat(item.tot_credit || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
            <td>${item.created_at}</td>
            <td>
                <button class="btn btn-info btn-sm view-btn" data-id="${item.id_manual_journal}">View</button>
<button class="btn btn-warning btn-sm edit-btn" data-id="${item.id_manual_journal}" disabled>Edit</button>
                ${item.status === "1"
                        ? `<button class="btn btn-danger btn-sm delete-btn" data-id="${item.id_manual_journal}" disabled>Delete</button>`
                        : `<button class="btn btn-warning btn-sm restore-btn" data-id="${item.id_manual_journal}">Restore</button>`}
            </td>
        </tr>`;
                    tableBody.innerHTML += row;
                });

                attachRowEvents(); // Reattach events after updating rows
            }

            // Attach events to delete/restore buttons
            function attachRowEvents() {
                document.querySelectorAll(".delete-btn").forEach((btn) => {
                    btn.addEventListener("click", function () {
                        const id = this.getAttribute("data-id");
                        changeStatus(id, 0); // Move to Deleted
                    });
                });

                document.querySelectorAll(".restore-btn").forEach((btn) => {
                    btn.addEventListener("click", function () {
                        const id = this.getAttribute("data-id");
                        changeStatus(id, 1); // Move to Posted
                    });
                });
            }

            // Change status (Delete/Restore) with confirmation
            function changeStatus(id, status) {
                const actionText = status === 0 ? 'delete' : 'restore';
                const confirmationText = status === 0
                    ? "This will move the record to the Deleted tab."
                    : "This will restore the record to the Posted tab.";
                const confirmButtonText = status === 0 ? "Yes, delete it!" : "Yes, restore it!";
                const successMessage = status === 0 ? "Deleted!" : "Restored!";

                Swal.fire({
                    title: `Are you sure you want to ${actionText}?`,
                    text: confirmationText,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: confirmButtonText,
                    cancelButtonText: "No, cancel"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Proceed with the status change
                        $.ajax({
                            url: "{{ route('manual_journal.change_status') }}",
                            method: "POST",
                            data: {
                                id_manual_journal: id,
                                status: status,
                                _token: "{{ csrf_token() }}",
                            },
                            success: function (response) {
                                if (response.status === "success") {
                                    Swal.fire(successMessage, response.message, "success").then(() => {
                                        fetchData(); // Refresh data after success
                                    });
                                } else {
                                    Swal.fire("Error", response.message, "error");
                                }
                            },
                            error: function () {
                                Swal.fire("Error", "An unexpected error occurred.", "error");
                            }
                        });
                    }
                });
            }


            // Event listener for the search button
            document.querySelector(".btn-success").addEventListener("click", function () {
                const filters = {
                    narration: document.getElementById("journalNarration").value,
                    from_date: document.getElementById("fromDate").value,
                    to_date: document.getElementById("toDate").value,
                    from_amount: document.getElementById("fromAmount").value,
                    to_amount: document.getElementById("toAmount").value,
                };

                fetchData(filters); // Fetch data with filters
            });

            // Fetch all data on page load
            fetchData();
        });


        document.addEventListener("DOMContentLoaded", function () {
            // Attach event listener to the View buttons
            $(document).on("click", ".view-btn", function () {
                const id = $(this).data("id");

                // Fetch details using AJAX
                $.ajax({
                    url: `/manual_journal/view/${id}`,
                    method: "GET",
                    success: function (response) {
                        const $detailsTableBody = $("#detailsTable tbody");
                        $detailsTableBody.empty(); // Clear any existing rows

                        if (response.length === 0) {
                            $detailsTableBody.html("<tr><td colspan='5' class='text-center'>No details found</td></tr>");
                            return;
                        }

                        // Populate the table with fetched data
                        response.forEach((item) => {
                            const row = `
                    <tr>
                        <td>${item.description}</td>
                        <td>${item.account}</td>

                        <td>${parseFloat(item.debit_amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td>${parseFloat(item.credit_amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    </tr>`;
                            $detailsTableBody.append(row);
                        });

                        // Show the modal
                        $("#viewDetailsModal").css("display", "flex");
                    },
                    error: function () {
                        Swal.fire("Error", "Failed to fetch journal details.", "error");
                    }
                });
            });

            // Close the modal
            $("#closeDetailsModal, #closeDetailsFooterModal").on("click", function () {
                $("#viewDetailsModal").css("display", "none");
            });

            // Close the modal when clicking outside of it
            $(window).on("click", function (e) {
                if ($(e.target).is("#viewDetailsModal")) {
                    $("#viewDetailsModal").css("display", "none");
                }
            });
        });

        $(document).on("click", ".edit-btn", function () {
            const id = $(this).data("id"); // Get the ID of the manual journal

            // Fetch data using AJAX
            $.ajax({
                url: `/manual_journal/edit/${id}`,
                method: "GET",
                success: function (response) {
                    if (response.status === "success") {
                        // Redirect to the Add Journal interface with data
                        const journalData = response.data;

                        // Store data in local storage (or pass it via URL parameters)
                        localStorage.setItem("editJournalData", JSON.stringify(journalData));

                        // Redirect to the Add Journal interface
                        window.location.href = "/AddJournal";
                    } else {
                        Swal.fire("Error", response.message, "error");
                    }
                },
                error: function () {
                    Swal.fire("Error", "Failed to fetch journal data.", "error");
                }
            });
        });



    </script>
@endsection

