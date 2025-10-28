@extends('layout.admin')

@section('head')
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .profile-card {
            margin: auto;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        .profile-card .card-header {
            background: #007bff;
            color: white;
            text-align: center;
        }
        .profile-card .profile-image {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: -50px;
        }
        .profile-card .profile-image img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 5px solid white;
        }
    </style>
    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 0.5in;
                counter-increment: page;
            }

            .page-break {
                page-break-after: always;
            }

            #bank_table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
            }

            #bank_table th,
            #bank_table td {
                border: 1px solid black;
                padding: 4px;
                text-align: center;
                word-break: break-word;
            }

            .btn, form, .page-title, .no-print, .d-flex {
                display: none !important;
            }
        }
        @media print {
            .print-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                font-size: 11px;
                color: black;
                display: flex;
                justify-content: space-between;
                padding: 5px 30px;
                background-color: white;
                border-top: 1px solid #000;
            }
        }
        @media print {
            body {
                font-size: 12px !important;
            }

            #bank_table {
                font-size: 11px !important;
            }

            #bank_table th,
            #bank_table td {
                padding: 6px !important;
                font-size: 10px !important;
                border: 1px solid #333 !important;
                vertical-align: middle !important;
            }
        }
    </style>
@endsection

@section('content')
    <div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h4 class="page-title">Cheque Details</h4>
                            <div class="d-flex gap-2 align-items-center">
                                <div class="d-flex gap-2 align-items-center">
                                    <label class="form-label mb-0" style="min-width: 60px; font-weight: 500;">From:</label>
                                    <input type="date" id="start_date" value="{{ request('start_date') }}" class="form-control" style="width: 150px;">
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <label class="form-label mb-0" style="min-width: 40px; font-weight: 500;">To:</label>
                                    <input type="date" id="end_date" value="{{ request('end_date') }}" class="form-control" style="width: 150px;">
                                </div>
                                <button class="btn btn-primary" onclick="applyFilters()">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <a href="{{ route('bank.chq') }}" class="btn btn-secondary">
                                    <i class="fas fa-refresh me-1"></i>Show All
                                </a>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Product</label>
                                    <select class="form-control select2" id="product_id">
                                        <option value="">All</option>
                                        @foreach($loan_categories as $category)
                                            <option value="{{ $category->idLoan_Category }}" {{ request('product_id') == $category->idLoan_Category ? 'selected' : '' }}>
                                                {{ $category->Name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="loan_number" class="form-label">Loan Number</label>
                                    <input type="text" id="loan_number" value="{{ request('loan_number') }}" class="form-control" placeholder="Enter loan number">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Customer</label>
                                    <select class="form-control select2" id="customer_id">
                                        <option value="">All</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->idCustomer }}" {{ request('customer_id') == $customer->idCustomer ? 'selected' : '' }}>
                                                {{ $customer->cus_number }} - {{ $customer->First_Name }} {{ $customer->Last_Name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="chq_number" class="form-label">Cheque Number</label>
                                    <input type="text" id="chq_number" value="{{ request('chq_number') }}" class="form-control" placeholder="Enter cheque number">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="center_id" class="form-label">Center</label>
                                    <select class="form-control select2" id="center_id">
                                        <option value="">All</option>
                                        @foreach($centers as $center)
                                            <option value="{{ $center->idCenter }}" {{ request('center_id') == $center->idCenter ? 'selected' : '' }}>
                                                {{ $center->No }} - {{ $center->Name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control select2" id="status">
                                        <option value="">All</option>
                                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Pending</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Proceeded</option>
                                        <option value="-1" {{ request('status') == '-1' ? 'selected' : '' }}>Returned</option>
                                        <option value="-2" {{ request('status') == '-2' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <button type="button" class="btn btn-danger" onclick="applyFilters()"><i class="bi bi-search"></i> </button>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-12 d-flex align-items-center gap-2">
                                <label for="pageOrientation" style="margin-right: 10px;">Print Orientation:</label>
                                <select id="pageOrientation" class="form-select w-auto">
                                    <option value="landscape" selected>Landscape</option>
                                    <option value="portrait">Portrait</option>
                                </select>

                                <button id="printButton" class="btn btn-primary"><i class="bi bi-printer"></i> Print</button>
                                <button id="downloadExcel" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Download Excel</button>
                                
                                @php
                                    $pendingCount = 0;
                                    $pendingTotal = 0;
                                    foreach($chq as $item) {
                                        if($item->chq_status === "0") {
                                            $pendingCount++;
                                            $pendingTotal += floatval(str_replace(',', '', $item->payment_amount));
                                        }
                                    }
                                @endphp
                                
                                <div class="ms-auto d-flex gap-3">
                                    <div class="badge bg-warning text-dark p-2" style="font-size: 14px;">
                                        <i class="bi bi-clock-history"></i> Pending Cheques: <strong>{{ $pendingCount }}</strong>
                                    </div>
                                    <div class="badge bg-info text-dark p-2" style="font-size: 14px;">
                                        <i class="bi bi-currency-dollar"></i> Total Amount: <strong>{{ number_format($pendingTotal, 2, '.', ',') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>




                        <div class="modal-body">
                            <div class="row">
                                <table class="table table-centered mb-0" id="bank_table">
                                    <thead>
                                    <tr>
                                        <th>Date Time</th>
                                        <th>Loan No</th>
                                        <th>Customer No</th>
                                        <th>Customer Name</th>
                                        <th>Cheque No</th>
                                        <th>Name Of Cheque</th>
                                        <th>Cheque Type</th>
                                        <th>Cheque Date</th>
                                        <th>Bank Account</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th style="text-align: center">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($chq as $item)
                                        <tr>
                                            <td>{{$item->date}} {{$item->time}}</td>
                                            <td>{{$item->Loan_No}}</td>
                                            <td>{{$item->cus_number}}</td>
                                            <td>{{$item->First_Name}} {{$item->Last_Name}}</td>
                                            <td>{{$item->chq_number}}</td>
                                            <td>{{$item->name_on_cheque}}</td>
                                            <td>{{$item->chq_type}}</td>
                                            <td>{{$item->chq_date}}</td>
                                            <td>{{$item->Bank_Name}}-{{$item->Account_Name}}</td>
                                            <td>{{ number_format(str_replace(',', '', $item->payment_amount), 2, '.', ',') }}</td>
                                            @if($item->chq_status==="1")
                                                <td><span style="color: red">Proceeded</span></td>
                                            @elseif($item->chq_status==="-1")
                                                <td><span style="color: #ffab00">Returned</span></td>
                                            @elseif($item->chq_status==="-2")
                                                <td><span style="color: #6c757d">Cancelled</span></td>
                                            @else
                                                <td><span style="color: green">Pending</span></td>
                                            @endif
                                            <td style="text-align: center">
                                                @if($item->chq_status==="0")
                                                    <button type="button" class="btn btn-success"
                                                            style="background-color: white; color: #5691FF;"
                                                            onclick="process(
        {{ $item->idChq }},'{{ $item->cus_id }}',
        '{{ $item->payment_amount }}',
        '{{ $item->file }}',
        '{{ $item->loan_id }}',
        '{{ $item->chq_date }}',
        '{{ $item->payment_type }}',
        '{{ $item->bank_account_company }}',
        '{{ $item->cheque_issue_bank }}',
        '{{ $item->name_on_cheque }}',
        '{{ $item->chq_number }}',
        '{{ $item->chq_date }}',
        '{{ $item->chq_type }}'
    )">
                                                        Process
                                                        <i class="fas fa-spinner fa-spin fs-4" style="display:none;" id="process_icon_{{ $item->idChq }}"></i>
                                                    </button>

                                                    <button type="button" class="btn btn-danger"
                                                            style="background-color: white; color: #f51515;" onclick="decline({{ $item->idChq }})">Return
                                                        <i class="fas fa-spinner fa-spin fs-4" style="display:none;" id="return_icon_{{ $item->idChq }}"></i>
                                                    </button>

                                                    <button type="button" class="btn btn-secondary"
                                                            style="background-color: white; color: #6c757d;" onclick="cancel({{ $item->idChq }})">Cancel
                                                        <i class="fas fa-spinner fa-spin fs-4" style="display:none;" id="cancel_icon_{{ $item->idChq }}"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-success"
                                                            style="background-color: white; color: #5691FF;" disabled>Process
                                                        <i class="fas fa-spinner fa-spin fs-4" style="display:none;" id="process_icon_{{ $item->idChq }}"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger"
                                                            style="background-color: white; color: #f51515;" disabled>Return
                                                        <i class="fas fa-spinner fa-spin fs-4" style="display:none;" id="return_icon_{{ $item->idChq }}"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-secondary"
                                                            style="background-color: white; color: #6c757d;" disabled>Cancel
                                                        <i class="fas fa-spinner fa-spin fs-4" style="display:none;" id="cancel_icon_{{ $item->idChq }}"></i>
                                                    </button>
                                                @endif




                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>




                    </div> <!-- end card-->
                </div> <!-- end col -->


            </div>
            <!-- end row -->

        </div>



        <div class="modal fade" id="standard-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Bank Log Report</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table table-centered mb-0" id="bank_table_log">
                                    <thead>
                                    <tr>
                                        <th>Date Time</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Note</th>
                                        <th>Credit</th>
                                        <th>Debit</th>
                                        <th>Balance</th>
                                        <th>User</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->







    </div>
    <div class="print-footer">
        <div class="left">
            Company: Asipiya Holdings | Cheque Details Report
        </div>
        <div class="right">
            Printed by: {{ session('Full_Name') ?? 'System' }} on {{ now()->format('Y-m-d h:i A') }} | Page <span class="page-number"></span>
        </div>
    </div>

    @php
        $printedBy = session('Full_Name');
        $printedAt = now()->format('Y-m-d h:i A');
    @endphp

@endsection

@section('script')
    <!-- Use global jQuery/Bootstrap from layout to avoid duplicate instances -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="../JS/validate.js"></script>
    
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
    <script>
        function process(id,cus_id,payment_amount,file,loan_id,payment_date,payment_type,bank_account_company,cheque_issue_bank,name_on_cheque,chq_number,chq_date,chq_type) {
            let date = new Date().toISOString().slice(0, 10); // "2025-08-19"

            document.getElementById('process_icon_' + id).style.display = 'inline-block';
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to process this cheque payment?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Process it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading alert
                    Swal.fire({
                        title: "Processing...",
                        text: "Please wait while we process your payment.",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    let formData = new FormData();
                    formData.append('cus_id', cus_id);
                    formData.append('payment_amount', payment_amount);
                    formData.append('file', file);
                    formData.append('loan_id', loan_id);
                    formData.append('payment_date', date);
                    formData.append('payment_type', payment_type);
                    formData.append('bank_account_company', bank_account_company);
                    formData.append('cheque_issue_bank', cheque_issue_bank);
                    formData.append('name_on_cheque', name_on_cheque);
                    formData.append('chq_number', chq_number);
                    formData.append('chq_date', chq_date);
                    formData.append('chq_type', chq_type);
                    formData.append('cheque_accept', "1");
                    formData.append('cheque_id', id);

                    $.ajax({
                        type: "POST",
                        url: "/payment_save_today",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (data, textStatus, xhr) {
                            let payment_id = data.payment_id;
                            if (xhr.status === 200) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Successfully processed !",
                                }).then(function () {
                                    // if (payment_id===0){
                                    //     window.location.reload();
                                    // }else{
                                    //     load_payment_reciept(payment_id);
                                    // }
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire("Error!", "Failed to save data!", "error");
                                document.getElementById('process_icon_' + id).style.display = 'none';
                            }
                        },
                        error: function () {
                            Swal.fire("Error!", "Failed to save data!", "error");
                            document.getElementById('process_icon_' + id).style.display = 'none';
                        },
                        complete: function () {
                            Swal.close(); // Close loading alert after completion
                        }
                    });
                }
            });
        }


        function decline(id) {
// Show the process icon before making the AJAX request
            document.getElementById('return_icon_' + id).style.display = 'inline-block';
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to return this cheque?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Return it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "GET",
                        url: "/return_chq/" + id,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function (data, textStatus, xhr) {
                            // Hide the process icon after the AJAX request completes
                            document.getElementById('process_icon_' + id).style.display = 'none';

                            if (xhr.status === 200) {
                                // Reload the page or update the UI as needed
                                location.reload();
                            } else {
                                Swal.fire("Error!", "Failed to save data!", "error");
                            }
                        },
                        error: function() {
                            // Hide the process icon in case of an error
                            document.getElementById('return_icon_' + id).style.display = 'none';
                            Swal.fire("Error!", "An error occurred during the process!", "error");
                        }
                    });
                }else{
                    document.getElementById('return_icon_' + id).style.display = 'none';
                }
            });
        }

        function cancel(id) {
            // Show the cancel icon before making the AJAX request
            document.getElementById('cancel_icon_' + id).style.display = 'inline-block';
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to cancel this cheque?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Cancel it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "GET",
                        url: "/cancel_chq/" + id,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function (data, textStatus, xhr) {
                            // Hide the cancel icon after the AJAX request completes
                            document.getElementById('cancel_icon_' + id).style.display = 'none';

                            if (xhr.status === 200) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Cheque cancelled successfully!",
                                }).then(function () {
                                    location.reload();
                                });
                            } else {
                                Swal.fire("Error!", "Failed to cancel cheque!", "error");
                            }
                        },
                        error: function() {
                            // Hide the cancel icon in case of an error
                            document.getElementById('cancel_icon_' + id).style.display = 'none';
                            Swal.fire("Error!", "An error occurred during the process!", "error");
                        }
                    });
                }else{
                    document.getElementById('cancel_icon_' + id).style.display = 'none';
                }
            });
        }
        function applyFilters() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const productId = document.getElementById('product_id').value;
            const loanNumber = document.getElementById('loan_number').value;
            const customerId = document.getElementById('customer_id').value;
            const chqNumber = document.getElementById('chq_number').value;
            const centerId = document.getElementById('center_id').value;
            const status = document.getElementById('status') ? document.getElementById('status').value : '';
            
            // make request 
            let queryParams = new URLSearchParams();
            
            if (startDate) {
                queryParams.append('start_date', startDate);
            }
            
            if (endDate) {
                queryParams.append('end_date', endDate);
            }
            
            if (productId) {
                queryParams.append('product_id', productId);
            }
            
            if (loanNumber) {
                queryParams.append('loan_number', loanNumber);
            }
            
            if (customerId) {
                queryParams.append('customer_id', customerId);
            }
            
            if (chqNumber) {
                queryParams.append('chq_number', chqNumber);
            }
            
            if (centerId) {
                queryParams.append('center_id', centerId);
            }

            if (status !== '') {
                queryParams.append('status', status);
            }
            
            // Validate date range
            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Date Range',
                    text: 'Start date cannot be later than end date.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            
            // Apply filters
            window.location.href = `?${queryParams.toString()}`;
        }



        // Excel download functionality
        $('#downloadExcel').click(function () {
            // Create custom data structure
            const customData = [];
            
            // Headers
            customData.push([
                'Cheque Number',
                'Cheque Date', 
                'Received Date',
                'Customer Name',
                'Customer Number',
                'Loan Number',
                'Bank Name',
                'Branch Name',
                'Cheque Amount',
                'Status',
                'Deposited Date',
                'Collector / Received By',
                'Remarks'
            ]);

            // Process table rows and sort by status
            const tableRows = [];
            $('#bank_table tbody tr').each(function() {
                const row = $(this);
                const cells = row.find('td');
                if (cells.length > 0) {
                    const statusText = cells.eq(10).find('span').text();
                    let status = statusText;
                    let statusOrder = 0;
                    
                    if (statusText === 'Pending') { status = 'Pending'; statusOrder = 1; }
                    else if (statusText === 'Proceeded') { status = 'Deposited'; statusOrder = 2; }
                    else if (statusText === 'Returned') { status = 'Returned'; statusOrder = 3; }
                    else if (statusText === 'Cancelled') { status = 'Cancelled'; statusOrder = 4; }
                    
                    tableRows.push({
                        order: statusOrder,
                        data: [
                            cells.eq(4).text(), // Cheque Number
                            cells.eq(7).text(), // Cheque Date
                            cells.eq(0).text(), // Received Date (Date Time)
                            cells.eq(3).text(), // Customer Name
                            cells.eq(2).text(), // Customer Number
                            cells.eq(1).text(), // Loan Number
                            '', // Bank Name (empty)
                            '', // Branch Name (empty)
                            cells.eq(9).text(), // Cheque Amount
                            status, // Status
                            statusText === 'Proceeded' ? cells.eq(0).text() : '', // Deposited Date
                            '', // Collector (empty)
                            '' // Remarks (empty)
                        ]
                    });
                }
            });
            
            // Sort by status order
            tableRows.sort((a, b) => a.order - b.order);
            
            // Add sorted data
            tableRows.forEach(row => customData.push(row.data));

            const ws = XLSX.utils.aoa_to_sheet(customData);
            
            // Set column widths
            ws['!cols'] = [
                {wch: 15}, // Cheque Number
                {wch: 12}, // Cheque Date
                {wch: 15}, // Received Date
                {wch: 25}, // Customer Name
                {wch: 15}, // Customer Number
                {wch: 15}, // Loan Number
                {wch: 20}, // Bank Name
                {wch: 20}, // Branch Name
                {wch: 15}, // Cheque Amount
                {wch: 12}, // Status
                {wch: 15}, // Deposited Date
                {wch: 20}, // Collector
                {wch: 15}  // Remarks
            ];

            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Cheque Details");
            XLSX.writeFile(wb, `Cheque_Details_${new Date().toLocaleString('default', { month: 'long' })}.xlsx`);
        });

        // Print functionality
        $('#printButton').click(function () {
            const orientation = $('#pageOrientation').val();

            // Create custom print data
            const printRows = [];
            
            // Process table rows and sort by status
            $('#bank_table tbody tr').each(function() {
                const row = $(this);
                const cells = row.find('td');
                if (cells.length > 0) {
                    const statusText = cells.eq(10).find('span').text();
                    let status = statusText;
                    let statusOrder = 0;
                    
                    if (statusText === 'Pending') { status = 'Pending'; statusOrder = 1; }
                    else if (statusText === 'Proceeded') { status = 'Deposited'; statusOrder = 2; }
                    else if (statusText === 'Returned') { status = 'Returned'; statusOrder = 3; }
                    else if (statusText === 'Cancelled') { status = 'Cancelled'; statusOrder = 4; }
                    
                    printRows.push({
                        order: statusOrder,
                        chequeNumber: cells.eq(4).text(),
                        chequeDate: cells.eq(7).text(),
                        receivedDate: cells.eq(0).text(),
                        customerName: cells.eq(3).text(),
                        customerNumber: cells.eq(2).text(),
                        loanNumber: cells.eq(1).text(),
                        bankName: '',
                        branchName: '',
                        chequeAmount: cells.eq(9).text(),
                        status: status,
                        depositedDate: statusText === 'Proceeded' ? cells.eq(0).text() : '',
                        collector: '',
                        remarks: ''
                    });
                }
            });
            
            // Sort by status
            printRows.sort((a, b) => a.order - b.order);

            // Build custom table
            let printContent = '<table id="customTable" style="width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 9px;">';
            printContent += '<thead><tr>';
            printContent += '<th style="border: 1px solid black; padding: 4px; font-weight: bold;">Cheque Number</th>';
            printContent += '<th style="border: 1px solid black; padding: 4px; font-weight: bold;">Cheque Date</th>';
            printContent += '<th style="border: 1px solid black; padding: 4px; font-weight: bold;">Received Date</th>';
            printContent += '<th style="border: 1px solid black; padding: 4px; font-weight: bold;">Customer Name</th>';
            printContent += '<th style="border: 1px solid black; padding: 4px; font-weight: bold;">Customer Number</th>';
            printContent += '<th style="border: 1px solid black; padding: 4px; font-weight: bold;">Loan Number</th>';
            printContent += '<th style="border: 1px solid black; padding: 4px; font-weight: bold;">Bank Name</th>';
            printContent += '<th style="border: 1px solid black; padding: 4px; font-weight: bold;">Branch Name</th>';
            printContent += '<th style="border: 1px solid black; padding: 4px; font-weight: bold;">Cheque Amount</th>';
            printContent += '<th style="border: 1px solid black; padding: 4px; font-weight: bold;">Status</th>';
            printContent += '<th style="border: 1px solid black; padding: 4px; font-weight: bold;">Deposited Date</th>';
            printContent += '<th style="border: 1px solid black; padding: 4px; font-weight: bold;">Collector</th>';
            printContent += '<th style="border: 1px solid black; padding: 4px; font-weight: bold;">Remarks</th>';
            printContent += '</tr></thead><tbody>';
            
            printRows.forEach(row => {
                printContent += '<tr>';
                printContent += '<td style="border: 1px solid black; padding: 3px;">' + row.chequeNumber + '</td>';
                printContent += '<td style="border: 1px solid black; padding: 3px;">' + row.chequeDate + '</td>';
                printContent += '<td style="border: 1px solid black; padding: 3px;">' + row.receivedDate + '</td>';
                printContent += '<td style="border: 1px solid black; padding: 3px;">' + row.customerName + '</td>';
                printContent += '<td style="border: 1px solid black; padding: 3px;">' + row.customerNumber + '</td>';
                printContent += '<td style="border: 1px solid black; padding: 3px;">' + row.loanNumber + '</td>';
                printContent += '<td style="border: 1px solid black; padding: 3px;">' + row.bankName + '</td>';
                printContent += '<td style="border: 1px solid black; padding: 3px;">' + row.branchName + '</td>';
                printContent += '<td style="border: 1px solid black; padding: 3px;">' + row.chequeAmount + '</td>';
                printContent += '<td style="border: 1px solid black; padding: 3px;">' + row.status + '</td>';
                printContent += '<td style="border: 1px solid black; padding: 3px;">' + row.depositedDate + '</td>';
                printContent += '<td style="border: 1px solid black; padding: 3px;">' + row.collector + '</td>';
                printContent += '<td style="border: 1px solid black; padding: 3px;">' + row.remarks + '</td>';
                printContent += '</tr>';
            });
            
            printContent += '</tbody></table>';

            const printWindow = window.open('', '', 'height=800,width=1200');

            printWindow.document.write('<html><head><title>Cheque Details Report</title>');
            printWindow.document.write('<style>');
            printWindow.document.write('body { font-family: Arial, sans-serif; font-size: 10px; margin: 0.3in; }');
            printWindow.document.write('#customTable { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 8px; }');
            printWindow.document.write('#customTable th, #customTable td { border: 1px solid black; padding: 3px; text-align: center; white-space: normal !important; word-wrap: break-word !important; word-break: break-word !important; line-height: 1.2 !important; vertical-align: middle !important; overflow: hidden !important; }');
            printWindow.document.write('#customTable thead th { font-size: 9px !important; font-weight: bold; text-align: center; }');
            printWindow.document.write('#customTable th:nth-child(1), #customTable td:nth-child(1) { width: 10%; }'); // Cheque Number
            printWindow.document.write('#customTable th:nth-child(2), #customTable td:nth-child(2) { width: 8%; }'); // Cheque Date  
            printWindow.document.write('#customTable th:nth-child(3), #customTable td:nth-child(3) { width: 10%; }'); // Received Date
            printWindow.document.write('#customTable th:nth-child(4), #customTable td:nth-child(4) { width: 15%; }'); // Customer Name
            printWindow.document.write('#customTable th:nth-child(5), #customTable td:nth-child(5) { width: 8%; }'); // Customer Number
            printWindow.document.write('#customTable th:nth-child(6), #customTable td:nth-child(6) { width: 8%; }'); // Loan Number
            printWindow.document.write('#customTable th:nth-child(7), #customTable td:nth-child(7) { width: 15%; font-size: 7px !important; }'); // Bank Name
            printWindow.document.write('#customTable th:nth-child(8), #customTable td:nth-child(8) { width: 8%; }'); // Branch Name
            printWindow.document.write('#customTable th:nth-child(9), #customTable td:nth-child(9) { width: 8%; }'); // Amount
            printWindow.document.write('#customTable th:nth-child(10), #customTable td:nth-child(10) { width: 6%; }'); // Status
            printWindow.document.write('#customTable th:nth-child(11), #customTable td:nth-child(11) { width: 8%; }'); // Deposited Date
            printWindow.document.write('#customTable th:nth-child(12), #customTable td:nth-child(12) { width: 8%; }'); // Collector
            printWindow.document.write('#customTable th:nth-child(13), #customTable td:nth-child(13) { width: 8%; }'); // Remarks
            printWindow.document.write('@media print { @page { size: ' + orientation + '; margin: 0.5in; } }');
            printWindow.document.write('</style>');
            printWindow.document.write('</head><body>');

            printWindow.document.write('<h2 style="text-align:center;">Cheque Details Report</h2>');
            printWindow.document.write(printContent);

            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        });

    </script>

    <script>
        window.addEventListener('beforeprint', function () {
            const existing = document.querySelectorAll('.print-footer');
            existing.forEach(e => e.remove());

            const footer = document.createElement('div');
            footer.className = 'print-footer';

            const left = document.createElement('div');
            left.className = 'left';
            left.innerHTML = "Company: Asipiya Holdings | Cheque Details Report";

            const right = document.createElement('div');
            right.className = 'right';
            right.innerHTML = "Printed by: {{ $printedBy }} on {{ $printedAt }} | Page 1";

            footer.appendChild(left);
            footer.appendChild(right);

            document.body.appendChild(footer);
        });
    </script>

@endsection
