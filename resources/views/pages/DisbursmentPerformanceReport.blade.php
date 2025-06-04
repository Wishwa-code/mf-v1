@extends('layout.admin')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <style>
        thead {
            background-color: #d9edf7; /* Light blue color */
            color: #31708f; /* Darker blue text for contrast */
        }

        .bg-purple th {
            color: #e1e1e1 !important;
        }

        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    </div>
                    <h4 class="page-title">Loan Disbursement Performance - Dashboard Report Overview</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                            <div class="row g-2"> <!-- Use g-2 for reduced spacing -->
                                <div class="col-md-3">
                                    <label>Date Range From</label>
                                    <input type="date" id="date_from" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>Date Range To</label>
                                    <input type="date" id="date_to" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label>Branch</label>
                                    <select id="branch_id" class="form-control select2">
                                        <option value="">All</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Center</label>
                                    <select id="center_id" class="form-control select2">
                                        <option value="">All</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Product</label>
                                    <select id="product_id" class="form-control select2">
                                        <option value="">All</option>
                                    </select>
                                </div>
                                <div class="col-lg-3 d-flex align-items-center">
                                    <button type="button" class="btn btn-danger w-100" onclick="loadLoans()">
                                        <i class="bi bi-search"></i> Search
                                    </button>
                                </div>
                            </div>



                        <hr>
                        <div class="mb-3">
                            <button id="exportExcel" class="btn btn-success">
                                <i class="bi bi-file-earmark-excel"></i> Export to Excel
                            </button>

                        </div>


                        <div class="table-responsive">
                            <table class="table table-bordered" id="loanTable">
                                <thead>
                                <tr>
                                    <th>Loan No</th>
                                    <th>Cus No</th>
                                    <th>Loan Create Date</th>
                                    <th>Loan Disbursement Date</th>
                                    <th>Approval Time</th>
                                    <th>Product Name</th>
                                    <th>Loan Amount</th>
                                    <th>Interest Amount</th>
                                    <th>Total Loan Amount</th>
                                    <th>Document Charge</th>
                                    <th>Capital Balance</th>
                                    <th>Interest Collected</th>
                                    <th>Loan Balance</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody id="loanTableBody">
                                    <tr><td colspan="13" class="text-center">Please run a search to view results.</td></tr>
                                </tbody>
                                <tfoot class="bg-light fw-bold text-white">
                                <tr>
                                    <td colspan="6" class="text-end">Totals:</td>
                                    <td id="total_amount">0.00</td>
                                    <td id="total_interest">0.00</td>
                                    <td id="total_total_loan">0.00</td>
                                    <td id="total_doc_charge">0.00</td>
                                    <td id="total_capital">0.00</td>
                                    <td id="total_interest_collected">0.00</td>
                                    <td id="total_balance">0.00</td>
                                    <td></td>
                                </tr>
                                </tfoot>

                            </table>
                        </div> <!-- end table-responsive-->





                    </div> <!-- end card-->
                </div> <!-- end col -->


            </div>
            <!-- end row -->




        </div> <!-- container -->

    </div>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- SheetJS for Excel Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <!-- jsPDF Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- jsPDF AutoTable Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>


    <script>
        const branchAccess = @json($branch_access);
        const userBranchId = @json(session('branch_id'));
    </script>

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.select2').select2();
            loadFilters();
        });

        function loadFilters() {
            $.get("{{ route('loan.report.filters') }}", function (res) {
                // Clear existing
                $('#branch_id').empty();

                // If user has access, show "All" and enable selection
                if (branchAccess == 1) {
                    $('#branch_id').append(`<option value="">All</option>`);
                    res.branches.forEach(b => {
                        const selected = b.branch_id == userBranchId ? 'selected' : '';
                        $('#branch_id').append(`<option value="${b.branch_id}" ${selected}>${b.Name}</option>`);
                    });
                    $('#branch_id').prop('disabled', false);
                } else {
                    // User has no access: only show their branch, and disable selection
                    const userBranch = res.branches.find(b => b.branch_id == userBranchId);
                    if (userBranch) {
                        $('#branch_id').append(`<option value="${userBranch.branch_id}" selected>${userBranch.Name}</option>`);
                    }
                    $('#branch_id').prop('disabled', true);
                }

                // Load other filters
                res.centers.forEach(c => {
                    $('#center_id').append(`<option value="${c.idCenter}">${c.Name}</option>`);
                });
                res.products.forEach(p => {
                    $('#product_id').append(`<option value="${p.idLoan_Category}">${p.Name}-${p.Product_code}</option>`);
                });
            });
        }


        function loadLoans() {
            const filters = {
                date_from: $('#date_from').val(),
                date_to: $('#date_to').val(),
                branch_id: $('#branch_id').val(),
                center_id: $('#center_id').val(),
                product_id: $('#product_id').val()
            };

            $.post("{{ route('loan.report.data') }}", filters, function (data) {
                const tbody = $('#loanTableBody');
                tbody.empty();

                if (data.length === 0) {
                    tbody.append('<tr><td colspan="13" class="text-center">No data found</td></tr>');
                    return;
                }

                data.forEach(row => {
                    tbody.append(`
        <tr>
            <td>${row.Loan_No}</td>
            <td>${row.cus_number}</td>
            <td>${row.create_date}</td>
            <td>${row.disburse_date}</td>
            <td>${row.time}</td>
            <td>${row.product_name}-${row.Product_code}</td>
            <td>${row.Amount}</td>
            <td>${row.Interest_Amount}</td>
            <td>${row.Total_Loan_Amount}</td>
            <td>${row.Total_Other_Amount}</td>
            <td>${row.capital_balance}</td>
            <td>${row.Other_Amount_Balance}</td>
            <td>${row.Balance_Amount}</td>
            <td>${row.Status == 0 ? 'Ongoing' : (row.Status == 1 ? 'Settled' : '')}</td>
            <td>
                <a href="/loanview/${row.idCustomer_Loan}" target="_blank" class="btn btn-warning">
                    <i class="bi bi-eye"></i>
                </a>
            </td>
        </tr>
    `);
                });

                let totalAmount = 0;
                let totalInterest = 0;
                let totalLoan = 0;
                let totalDocCharge = 0;
                let totalCapital = 0;
                let totalInterestCollected = 0;
                let totalBalance = 0;

                data.forEach(row => {
                    totalAmount += parseFloat(row.Amount.replace(/,/g, '')) || 0;
                    totalInterest += parseFloat(row.Interest_Amount.replace(/,/g, '')) || 0;
                    totalLoan += parseFloat(row.Total_Loan_Amount.replace(/,/g, '')) || 0;
                    totalDocCharge += parseFloat(row.Total_Other_Amount.replace(/,/g, '')) || 0;
                    totalCapital += parseFloat(row.capital_balance.replace(/,/g, '')) || 0;
                    totalInterestCollected += parseFloat(row.Other_Amount_Balance.replace(/,/g, '')) || 0;
                    totalBalance += parseFloat(row.Balance_Amount.replace(/,/g, '')) || 0;
                });

                $('#total_amount').text(totalAmount.toLocaleString(undefined, {minimumFractionDigits: 2}));
                $('#total_interest').text(totalInterest.toLocaleString(undefined, {minimumFractionDigits: 2}));
                $('#total_total_loan').text(totalLoan.toLocaleString(undefined, {minimumFractionDigits: 2}));
                $('#total_doc_charge').text(totalDocCharge.toLocaleString(undefined, {minimumFractionDigits: 2}));
                $('#total_capital').text(totalCapital.toLocaleString(undefined, {minimumFractionDigits: 2}));
                $('#total_interest_collected').text(totalInterestCollected.toLocaleString(undefined, {minimumFractionDigits: 2}));
                $('#total_balance').text(totalBalance.toLocaleString(undefined, {minimumFractionDigits: 2}));


            });
        }

        document.getElementById('exportExcel').addEventListener('click', function () {
            const table = document.getElementById('loanTable');

            // Clone table and remove the Action column for export
            const clone = table.cloneNode(true);
            const rows = clone.querySelectorAll('tr');
            rows.forEach(row => {
                row.removeChild(row.lastElementChild); // Remove the "Action" column
            });

            const wb = XLSX.utils.table_to_book(clone, { sheet: "Loan Report" });
            XLSX.writeFile(wb, "Loan_Disbursement_Report.xlsx");
        });

    </script>


@endsection

