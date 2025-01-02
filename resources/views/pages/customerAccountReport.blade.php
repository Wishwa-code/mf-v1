@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7f6;
            color: #495057;
        }

        .card {
            border-radius: 0.75rem;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-body {
            padding: 1.5rem;
        }

        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;
        }

        .page-title-box {
            margin-bottom: 1rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #343a40;
        }

        .customer-details {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .customer-details h4 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #343a40;
        }

        .customer-details p {
            margin: 0.5rem 0;
            font-size: 1rem;
            color: #495057;
        }

        .account-card {
            background-color: #ffffff;
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .account-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .account-card h5 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
            color: #343a40;
        }

        .account-card .balance,
        .account-card .status {
            margin-top: 0.75rem;
            font-weight: bold;
            color: #343a40;
        }

        .more-info {
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            background-color: #2575fc;
            color: white;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        .more-info:hover {
            background-color: #3b87ff;
        }

        .details-table td.label {
            font-weight: bold;
            padding-right: 1rem;
        }

        .details-table td.value {
            text-align: left;
            color: #6c757d;
        }

        .modal-header {
            background-color: #2575fc;
            color: white;
            border-bottom: 0;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .modal-footer {
            border-top: 0;
            border-radius: 0 0 0.5rem 0.5rem;
        }

        #transactionTable {
            width: 100% !important;
        }

        #transactionTable th,
        #transactionTable td {
            text-align: center;
        }

        #transactionTable th {
            background-color: #6a11cb;
            color: white;
        }

        @media (max-width: 768px) {
            .customer-details,
            .account-card,
            .modal-body {
                text-align: center;
            }

            .account-card {
                margin-bottom: 1rem;
            }

            .modal-dialog {
                max-width: 95%;
            }

            .modal-body {
                padding: 1rem;
            }

            #transactionTable th,
            #transactionTable td {
                font-size: 0.9rem;
                word-break: break-word;
            }
        }
        .account-card {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .account-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .account-card h5 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #007bff;
        }

        .account-card p {
            margin: 0 0 10px;
            font-size: 14px;
        }

        .account-card .balance {
            font-size: 16px;
            font-weight: 700;
            color: #28a745;
        }

        .account-card .status {
            font-size: 14px;
            font-weight: 600;
            color: #dc3545;
        }

        .account-card .status.active {
            color: #28a745;
        }

        .more-info {
            display: block;
            width: 100%;
            padding: 10px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            background-color: #d9d3d3;
            color: #fff;
            border: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .more-info:hover {
            background-color: #0056b3;
        }

        @media (max-width: 767px) {
            .account-card {
                margin-bottom: 15px;
            }
        }
        .transaction-table-container {
            overflow-x: auto; /* Allows horizontal scrolling if needed */
        }

        .transaction-table {
            width: 100%;
            border-collapse: collapse;
        }

        .transaction-table th,
        .transaction-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .transaction-table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .transaction-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .transaction-table tr:hover {
            background-color: #f1f1f1;
        }
        /* Container for the table to allow horizontal scrolling on smaller screens */
        .transaction-table-container {
            overflow-x: auto; /* Enables horizontal scroll */
            -webkit-overflow-scrolling: touch; /* Smooth scrolling for touch devices */
        }

        /* Basic table styles */
        .transaction-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1em; /* Space below the table */
        }

        .transaction-table th,
        .transaction-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            white-space: nowrap; /* Prevents text from wrapping */
        }

        .transaction-table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .transaction-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .transaction-table tr:hover {
            background-color: #f1f1f1;
        }

        /* Responsive design */
        @media (max-width: 767px) {
            .transaction-table th,
            .transaction-table td {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }

            .transaction-table th {
                position: absolute;
                top: 0;
                left: 0;
                width: 50%;
                padding-left: 15px;
                background-color: #f4f4f4;
            }

            .transaction-table td {
                position: relative;
                padding-left: 50%;
            }

            .transaction-table td:before {
                content: attr(data-label); /* Adds a label before the cell content */
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 15px;
                white-space: nowrap;
                font-weight: bold;
            }

            /* Make sure table header is visible on top */
            .transaction-table th {
                display: none; /* Hide the table headers */
            }

            .transaction-table td {
                display: block;
                text-align: right;
            }
        }

        .table-container {
            max-height: 400px; /* Set your desired height */
            overflow-y: auto; /* Adds vertical scrollbar if content exceeds height */
            border: 1px solid #ddd; /* Optional: Adds a border around the table container */
            border-radius: 4px; /* Optional: Adds rounded corners to the border */
            padding: 10px; /* Optional: Adds some padding inside the container */
        }

        .transaction-table {
            width: 100%; /* Ensures the table takes the full width of the container */
            border-collapse: collapse; /* Removes space between borders */
        }

        .transaction-table th, .transaction-table td {
            border: 1px solid #ddd; /* Adds border to table cells */
            padding: 8px; /* Adds padding inside table cells */
            text-align: left; /* Aligns text to the left */
        }

        .transaction-table thead {
            background-color: #f4f4f4; /* Light grey background for header */
        }
        .account-card {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .account-card h5 {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .btn-group {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .btn.custom-btn {
            border-radius: 50px;
            font-size: 14px;
            padding: 10px 20px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-outline-danger.custom-btn {
            border-color: #e74c3c;
            color: #e74c3c;
        }

        .btn-outline-danger.custom-btn:hover {
            background-color: #e74c3c;
            color: #fff;
        }

        .btn-outline-info.custom-btn {
            border-color: #3498db;
            color: #3498db;
        }

        .btn-outline-info.custom-btn:hover {
            background-color: #3498db;
            color: #fff;
        }

        .btn-outline-primary.custom-btn {
            border-color: #8e44ad;
            color: #8e44ad;
        }

        .btn-outline-primary.custom-btn:hover {
            background-color: #8e44ad;
            color: #fff;
        }

        /* Responsive adjustments for mobile devices */
        @media (max-width: 576px) {
            .btn-group {
                flex-direction: column;
                gap: 10px;
            }

            .btn.custom-btn {
                width: 100%;
            }
        }

    </style>

@endsection

@section('content')

    <div class="page-content">
        <div class="container-fluid">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="page-title">Customer Accounts</h6>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="/showcustomerssaving">Customer Saving Details</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Customer Accounts</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Customer Details Section -->
            <div class="customer-details">
                <h4>Customer Details</h4>
                <p><strong>Full Name:</strong> {{$customers->First_Name}} {{$customers->Last_Name}}</p>
                <p><strong>Telephone No:</strong> {{$customers->Contact_No}}</p>
                <p><strong>NIC:</strong> {{$customers->Nic}}</p>
                <p><strong>Total Accounts:</strong> {{$saving_account_count}}</p>
                <p><strong>Total Balance:</strong> LKR {{number_format($total_balance,2,'.',',')}}</p>
            </div>

            <!-- Account Cards Section -->
            <div class="row">
                @foreach($saving_account as $item)
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="account-card">
                            <h5>Savings Account - (LKR {{ number_format($item->Balance, 2, '.', ',') }})</h5>
                            <p><strong>ACCOUNT NUMBER:</strong> {{$item->Account_No}}</p>
                            <p><strong>ACCOUNT TYPE:</strong> {{$item->Account_Type}}</p>
                            <p><strong>CURRENCY:</strong> LKR</p>
                            <p><strong>Loan NO:</strong> {{$item->Loan_No}}</p>
                            <p class="balance"><strong>AVAILABLE BALANCE:</strong> {{ number_format($item->Balance, 2, '.', ',') }}</p>
                            <p class="status {{ $item->Status == '1' ? 'active' : '' }}"><strong>STATUS:</strong> {{ $item->Status == '1' ? 'Active' : 'Inactive' }}</p>

                            <div class="btn-group mt-3">
                                <button class="btn btn-outline-danger custom-btn" data-bs-toggle="modal" data-bs-target="#transactionModal"
                                        data-transaction="Deposit" data-id="{{ $item->id }}">
                                    Deposit
                                </button>
                                <button class="btn btn-outline-info custom-btn" data-bs-toggle="modal" data-bs-target="#transactionModal"
                                        data-transaction="Withdrawal" data-id="{{ $item->id }}">
                                    Withdrawal
                                </button>
                                <button class="btn btn-outline-primary custom-btn more-info" data-bs-toggle="modal" data-bs-target="#accountModal"
                                        data-id="{{ $item->id }}"
                                        data-account-number="{{ $item->Account_No }}"
                                        data-account-type="{{ $item->Account_Type }}"
                                        data-currency="LKR"
                                        data-balance="{{ number_format($item->Balance, 2, '.', ',') }}"
                                        data-status="{{ $item->Status == '1' ? 'Active' : 'Inactive' }}"
                                        data-ticket-no="{{ $item->Loan_No }}"
                                        data-transactions='[{ "date": "2024-08-20", "type": "{{ $item->Account_Type }}", "description": "Salary", "credit": "5,000.00", "debit": "", "balance": "7,438.72", "user": "John Doe" }]'>
                                    More Information...
                                </button>
                            </div>
                        </div>
                    </div>


                @endforeach
            </div>

        </div>
    </div>


    <!-- Deposit/Withdrawal Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionModalLabel">Deposit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="transactionForm">
                        <div class="mb-3">
                            <label for="transactionAmount" class="form-label">Amount (LKR)</label>
                            <input type="number" class="form-control" id="transactionAmount" placeholder="Enter amount" required>
                        </div>
                        <input type="hidden" id="transactionType" value="Deposit">
                        <input type="hidden" id="accountId" value="">
                        <input type="hidden" id="accountNumber" value="">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitTransaction">Deposit</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accountModalLabel">Account Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="details-table">
                                <tr>
                                    <td class="label">Account Number:</td>
                                    <td class="value" id="modal-account-number"></td>
                                </tr>
                                <tr>
                                    <td class="label">Account Type:</td>
                                    <td class="value" id="modal-account-type"></td>
                                </tr>
                                <tr>
                                    <td class="label">Currency:</td>
                                    <td class="value" id="modal-currency"></td>
                                </tr>
                                <tr>
                                    <td class="label">Ticket No:</td>
                                    <td class="value" id="modal-ticket-no"></td>
                                </tr>
                                <tr>
                                    <td class="label">Available Balance:</td>
                                    <td class="value" id="modal-balance"></td>
                                </tr>
                                <tr>
                                    <td class="label">Status:</td>
                                    <td class="value" id="modal-status"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="table-container">
                        <table class="transaction-table">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Credit</th>
                                <th>Debit</th>
                                <th>Balance</th>
                                <th>User</th>
                            </tr>
                            </thead>
                            <tbody id="transaction-details">
                            <!-- Transaction data will be dynamically populated -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
{{--                    <button type="button" class="btn btn-primary" id="downloadPdfBtn">Download PDF</button>--}}
                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')
    <!-- jQuery and Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.flash.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();

            // $('#accountModal').on('show.bs.modal', function (event) {
            //     const button = $(event.relatedTarget);
            //     $('#modal-account-number').text(button.data('account-number'));
            //     $('#modal-account-type').text(button.data('account-type'));
            //     $('#modal-currency').text(button.data('currency'));
            //     $('#modal-ticket-no').text(button.data('ticket-no'));
            //     $('#modal-balance').text(button.data('balance'));
            //     $('#modal-status').text(button.data('status'));
            //
            //     const transactions = button.data('transactions');
            //     $('#transaction-details').empty();
            //     transactions.forEach(transaction => {
            //         const row = `<tr>
            //             <td>${transaction.date}</td>
            //             <td>${transaction.type}</td>
            //             <td>${transaction.description}</td>
            //             <td>${transaction.credit}</td>
            //             <td>${transaction.debit}</td>
            //             <td>${transaction.balance}</td>
            //             <td>${transaction.user}</td>
            //         </tr>`;
            //         $('#transaction-details').append(row);
            //     });
            // });
        });

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalElement = document.getElementById('accountModal');
            var modal = new bootstrap.Modal(modalElement);

            modalElement.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget; // Button that triggered the modal

                // Extract info from data attributes
                var accountNumber = button.getAttribute('data-account-number');
                var accountType = button.getAttribute('data-account-type');
                var currency = button.getAttribute('data-currency');
                var balance = button.getAttribute('data-balance');
                var status = button.getAttribute('data-status');
                var ticketNo = button.getAttribute('data-ticket-no');
                var accountId = button.getAttribute('data-id'); // Added data-id attribute for AJAX

                // Populate modal with the data
                var modalTitle = modalElement.querySelector('.modal-title');
                var modalBody = modalElement.querySelector('.modal-body');

                modalTitle.textContent = `Account Number: ${accountNumber}`;
                modalBody.querySelector('#modal-account-number').textContent = accountNumber;
                modalBody.querySelector('#modal-account-type').textContent = accountType;
                modalBody.querySelector('#modal-currency').textContent = currency;
                modalBody.querySelector('#modal-ticket-no').textContent = ticketNo;
                modalBody.querySelector('#modal-balance').textContent = balance;
                modalBody.querySelector('#modal-status').textContent = status;

                // Fetch transaction data
                fetch(`/get-account-transactions/${accountId}`)
                    .then(response => response.json())
                    .then(transactions => {
                        var transactionTableBody = modalBody.querySelector('#transaction-details');
                        transactionTableBody.innerHTML = transactions.map(tx => `
                        <tr>
                            <td>${tx.date}</td>
                            <td>${tx.type}</td>
                            <td>${tx.description}</td>
                            <td>${parseFloat(tx.credit).toFixed(2)}</td>
                            <td>${parseFloat(tx.debit).toFixed(2)}</td>
                            <td>${parseFloat(tx.balance).toFixed(2)}</td>
                            <td>${tx.user}</td>
                        </tr>
                    `).join('');
                    });
            });
        });
    </script>

{{--    <script>--}}
{{--        document.getElementById('downloadPdfBtn').addEventListener('click', function() {--}}
{{--            // Import jsPDF--}}
{{--            const { jsPDF } = window.jspdf;--}}

{{--            // Create a new jsPDF instance--}}
{{--            const doc = new jsPDF();--}}

{{--            // Add Account Details--}}
{{--            doc.setFontSize(16);--}}
{{--            doc.text("Account Details", 10, 10);--}}
{{--            doc.setFontSize(12);--}}

{{--            // Fetch details from the modal--}}
{{--            const accountNumber = document.getElementById('modal-account-number').textContent.trim();--}}
{{--            const accountType = document.getElementById('modal-account-type').textContent.trim();--}}
{{--            const currency = document.getElementById('modal-currency').textContent.trim();--}}
{{--            const ticketNo = document.getElementById('modal-ticket-no').textContent.trim();--}}
{{--            const balance = document.getElementById('modal-balance').textContent.trim();--}}
{{--            const status = document.getElementById('modal-status').textContent.trim();--}}

{{--            let yPosition = 20;--}}
{{--            doc.text(`Account Number: ${accountNumber}`, 10, yPosition);--}}
{{--            yPosition += 10;--}}
{{--            doc.text(`Account Type: ${accountType}`, 10, yPosition);--}}
{{--            yPosition += 10;--}}
{{--            doc.text(`Currency: ${currency}`, 10, yPosition);--}}
{{--            yPosition += 10;--}}
{{--            doc.text(`Ticket No: ${ticketNo}`, 10, yPosition);--}}
{{--            yPosition += 10;--}}
{{--            doc.text(`Available Balance: ${balance}`, 10, yPosition);--}}
{{--            yPosition += 10;--}}
{{--            doc.text(`Status: ${status}`, 10, yPosition);--}}
{{--            yPosition += 20;--}}

{{--            // Add Transactions--}}
{{--            doc.setFontSize(16);--}}
{{--            doc.text("Transactions", 10, yPosition);--}}
{{--            yPosition += 10;--}}
{{--            doc.setFontSize(12);--}}

{{--            // Table headers--}}
{{--            doc.text("Date", 10, yPosition);--}}
{{--            doc.text("Type", 40, yPosition);--}}
{{--            doc.text("Description", 70, yPosition);--}}
{{--            doc.text("Credit", 100, yPosition);--}}
{{--            doc.text("Debit", 130, yPosition);--}}
{{--            doc.text("Balance", 160, yPosition);--}}
{{--            doc.text("User", 190, yPosition);--}}
{{--            yPosition += 10;--}}

{{--            // Fetch and add transactions--}}
{{--            const transactions = JSON.parse(document.querySelector('[data-transactions]').getAttribute('data-transactions'));--}}

{{--            transactions.forEach(tx => {--}}
{{--                // Ensure values are treated as numbers--}}
{{--                const credit = parseFloat(tx.credit) || 0;--}}
{{--                const debit = parseFloat(tx.debit) || 0;--}}
{{--                const balance = parseFloat(tx.balance) || 0;--}}

{{--                doc.text(tx.date, 10, yPosition);--}}
{{--                doc.text(tx.type, 40, yPosition);--}}
{{--                doc.text(tx.description, 70, yPosition);--}}
{{--                doc.text(credit.toFixed(2), 100, yPosition);--}}
{{--                doc.text(debit.toFixed(2), 130, yPosition);--}}
{{--                doc.text(balance.toFixed(2), 160, yPosition);--}}
{{--                doc.text(tx.user, 190, yPosition);--}}
{{--                yPosition += 10;--}}
{{--            });--}}

{{--            // Save the PDF--}}
{{--            doc.save('account-details.pdf');--}}
{{--        });--}}
{{--    </script>--}}
    <script>
        // When the modal is shown, change its content based on the button clicked
        var transactionModal = document.getElementById('transactionModal');
        transactionModal.addEventListener('show.bs.modal', function (event) {

            $("#transactionAmount").val("");

            var button = event.relatedTarget; // Button that triggered the modal
            var transactionType = button.getAttribute('data-transaction'); // Extract info from data-* attributes
            var accountId = button.getAttribute('data-id');
            var accountNumber = button.getAttribute('data-account-number');

            // Update modal title and button text
            var modalTitle = transactionModal.querySelector('.modal-title');
            var submitButton = transactionModal.querySelector('#submitTransaction');
            modalTitle.textContent = transactionType;
            submitButton.textContent = transactionType;

            // Set form hidden fields for account info
            document.getElementById('transactionType').value = transactionType;
            document.getElementById('accountId').value = accountId;
            document.getElementById('accountNumber').value = accountNumber;
        });

        // Handle form submission
        document.getElementById('submitTransaction').addEventListener('click', function (event) {
            event.preventDefault();

            var amount = document.getElementById('transactionAmount').value;
            var type = document.getElementById('transactionType').value;
            var accountId = document.getElementById('accountId').value;


            if(amount===""){
                Swal.fire("Error!", "Please enter amount !", "error");
            }else{
                // Confirm with SweetAlert2 before submitting the transaction
                Swal.fire({
                    title: `Are you sure you want to ${type.toLowerCase()} LKR ${amount}?`,
                    text: `This action will ${type.toLowerCase()} the selected amount.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: `Yes, ${type}!`,
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Proceed with the AJAX request if confirmed
                        $.ajax({
                            url: '/transaction', // Your transaction endpoint
                            method: 'POST',
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            },
                            data: {
                                type: type,
                                amount: amount,
                                accountId: accountId,
                            },
                            success: function(response) {
                                // Show success alert with SweetAlert2
                                Swal.fire({
                                    title: 'Success!',
                                    text: `${type} was completed successfully.`,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Reload the page after success
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                // Show error alert with SweetAlert2
                                Swal.fire({
                                    title: 'Error!',
                                    text: `There was an issue with the ${type.toLowerCase()}. Please try again.`,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            }


        });
    </script>




@endsection
