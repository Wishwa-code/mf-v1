@extends('layout.admin')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <style>
        .style-tr>td {
            padding: 2px 15px
        }


        .bg-purple th {
            color: #e1e1e1 !important;
        }

        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;

        }
        #productTable tr, #productTable th, #productTable td {
            margin: 10px !important;
            padding: 10px 25px!important; /* Adjust padding to your preference */
        }

        .table-centered {
            margin: 0 !important;
            padding: 0 !important;
        }


    </style>

@endsection


@section('content')
    <div>

        <!-- start page title -->
        <div class="row mt-3">
            <div class="col-12">
                <div>

                    <div class="row">

                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex flex-column align-items-start mb-3">
                                        <h4 class="page-title mb-3">Loan Product</h4>

                                        <!-- File input -->
                                        <label style="color: red">Upload Excel</label>
                                        <input type="file" id="uploadExcel" accept=".xlsx, .xls" class="form-control mb-2 w-50">

                                        <!-- Upload button, aligned below the file input -->
                                        <input type="button" onclick="upload_excel()" class="btn btn-success mt-2" value="Upload">
                                    </div>
                                    <div class="table-responsive-sm">
                                        <table id="productTable" class="display nowrap table table-striped table-bordered" style="width:100%">
                                            <thead class="sticky-top bg-purple">
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Product Code</th>
                                                <th class="text-center">Minimum Loan Amount</th>
                                                <th class="text-center">Maximum Loan Amount</th>
                                                <th>Interest Method</th>
                                                <th>Loan Interest Period</th>
                                                <th>Minimum Interest</th>
                                                <th>Maximum Interest</th>
                                                <th>Loan Duration Period</th>
                                                <th>Default Loan Duration</th>
                                                <th>Repayment Type</th>
                                                <th>Penalty Period</th>
                                                <th>Penalty Percentage(%)</th>
                                                <th>Penalty Start date after payment date</th>
                                                <th>Savings Account</th>
                                                <th>Saving Account Amount Type</th>
                                                <th>Saving Account Amount</th>
                                                <th class="text-center">Guarantee Count</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($loan_category as $item)
                                                <tr class="style-tr">
                                                    <td>{{$item->Name}}</td>
                                                    <td>{{$item->Product_code}}</td>
                                                    <td class="text-center">{{ number_format($item->Loan_amount ?? 0, 2, '.', '') }}</td>
                                                    <td class="text-center">{{ number_format((float) ($item->Loan_amount_to ?? 0), 2, '.', '') }}</td>
                                                    <td>{{$item->Interest_method}}</td>
                                                    <td>{{$item->Interest_period}}</td>
                                                    <td class="text-center">{{$item->Loan_interest ?? 0}}</td>
                                                    <td class="text-center">{{$item->Loan_interest_to ?? 0}}</td>
                                                    <td class="text-center">{{$item->Duration_period}}</td>
                                                    <td class="text-center">{{$item->Loan_period}}</td>
                                                    <td class="text-center">{{$item->Repayment_type}}</td>
                                                    <td class="text-center">{{$item->Panelty_period}}</td>
                                                    <td class="text-center">{{$item->Panelty_pecentage}}</td>
                                                    <td class="text-center">{{$item->Panelty_date}}</td>
                                                    <td class="text-center">{{$item->enable_saving_process}}</td>
                                                    <td class="text-center">{{$item->saving_amount_type}}</td>
                                                    <td class="text-center">{{$item->saving_amount}}</td>
                                                    <td class="text-center">{{$item->Guarantee_count}}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-primary" onclick="view_doc({{$item->idLoan_Category}})" data-bs-toggle="modal" data-bs-target="#view-modal">
                                                            <i class="bi bi-eye fs-4"></i></button>
                                                        <button type="button" class="btn btn-light edit-btn" data-bs-toggle="modal" data-bs-target="#standard-modal" onclick="view_doc_2({{$item->idLoan_Category}})"
                                                              data-product-name="{{$item->Name}}" data-product-code="{{$item->Product_code}}" data-loan-amount="{{ number_format($item->Loan_amount, 2, '.', '') }}" data-loan-amount-to="{{ number_format((float) ($item->Loan_amount_to ?? 0), 2, '.', '') }}" data-interest-method="{{$item->Interest_method}}"
                                                              data-interest-period="{{$item->Interest_period}}" data-loan-interest="{{$item->Loan_interest}}" data-loan-interest-to="{{$item->Loan_interest_to}}" data-duration-period="{{$item->Duration_period}}"
                                                                data-loan-period="{{$item->Loan_period}}" data-repayment-type="{{$item->Repayment_type}}" data-panelty-period="{{$item->Panelty_period}}"
                                                                data-panelty-percentage="{{$item->Panelty_pecentage}}" data-panelty-date="{{$item->Panelty_date}}" data-guarantee-count="{{$item->Guarantee_count}}"
                                                                data-product-id="{{$item->idLoan_Category}}"
                                                        >
                                                            <i class="bi bi-pencil fs-4"></i></button>

                                                        <button type="button" class="btn btn-danger" onclick="remove_loan_category({{$item->idLoan_Category}})">
                                                            <i class="bi bi-trash fs-4"></i></button>
                                                        <button type="button" class="btn btn-info" onclick="view_doc_3({{$item->idLoan_Category}},'{{$item->enable_saving_process}}','{{$item->saving_amount_type}}','{{$item->saving_amount}}','{{$item->saving_payment}}')">
                                                            <i class="bi bi-bank fs-4"></i>
                                                        </button>

                                                    </td>


                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>

                                    </div> <!-- end table-responsive-->

                                </div> <!-- end card body-->
                            </div> <!-- end card -->
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

        </div>


        <!-- Modal HTML -->
        <!-- Modal HTML -->
        <!-- Modal HTML -->
        <div class="modal fade" id="info-modal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="infoModalLabel">Saving Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <input type="hidden" id="saving_id" class="form-control">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="enable_saving" class="form-label">Enable Savings Account Process</label>
                                    <select class="form-select" id="enable_saving">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6" id="saving-account-details">
                                <div class="mb-3">
                                    <label for="saving_account_amount_type" class="form-label">Saving Account Amount Type</label>
                                    <select class="form-select" id="saving_account_amount_type">
                                        <option value="pre_defined">Pre Defined Amount</option>
                                        <option value="percentage">Percentage From Total Loan Amount</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6" id="saving-amount">
                                <div class="mb-3">
                                    <label for="saving_amount" class="form-label">Amount<span class="required-asterisk">*</span></label>
                                    <input type="text" id="saving_amount" class="form-control" value="0.00">
                                </div>
                            </div>
                            <div class="col-md-6" id="saving-amount">
                                <div class="mb-3">
                                    <label for="interest_method" class="form-label">Saving Payment Type</label>
                                    <select class="form-select" id="saving_payment">
                                        <option value="0" selected>Deduct Savings From Installment</option>
                                        <option value="1">Collect Savings Separately</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="updateLoanCategory()" id="update-button">Update</button>
                    </div>
                </div>
            </div>
        </div>





        <div class="modal fade" id="view-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title" >gwegerg</h4> -->
                        <h4>Other Chargers & Documents</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card border-secondary border">
                            <div class="card-body">
                                <div class="mb-3">
                                    <span style="color: red">Other Chargers</span>
                                    <div class="table-responsive-sm">
                                        <table class="table table-centered mb-0" id="chargers_show_table">
                                            <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card border-secondary border">
                            <div class="card-body">
                                <div class="mb-3">
                                    <span style="color: red">Documents</span>
                                    <div class="table-responsive-sm">
                                        <table class="table table-centered mb-0" id="document_show_table">
                                            <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal -->
            </div>
        </div>


        <div class="modal fade" id="standard-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Update Loan Product</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <input type="hidden" id="product_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="categoryName" class="form-label">Product Name<span class="required-asterisk">*</span></label>
                                    <input type="text" id="product_name" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="categoryName" class="form-label">Product Code<span class="required-asterisk">*</span></label>
                                    <input type="text" id="product_code" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="categoryName" class="form-label">Minimum Loan Amount<span class="required-asterisk">*</span></label>
                                    <input type="text" id="loan_amount" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="categoryName" class="form-label">Maximum Loan Amount<span class="required-asterisk">*</span></label>
                                    <input type="text" id="loan_amount_to" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleSelect" class="form-label">Interest Method<span class="required-asterisk">*</span></label>
                                    <select class="form-select" id="interest_method">
                                        <option value="Flat Rate">Flat Rate</option>
                                        <option value="Draft">Draft</option>
                                        <option value="Reducing Balance">Reducing Balance</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="witnessCount" class="form-label">Loan Interest Period<span class="required-asterisk">*</span></label>
                                    <select class="form-select" id="interest_period">
                                        <option value="Daily">Per Day</option>
                                        <option value="Weekly">Per Week</option>
                                        <option value="Per Month">Per Month</option>
                                        <option value="Per Year">Per Year</option>
                                        <option value="Per Loan">Per Loan</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="witnessCount" class="form-label">Minimum Interest<span class="required-asterisk">*</span></label>
                                    <input type="text" id="interest" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="witnessCount" class="form-label">Maximum Interest<span class="required-asterisk">*</span></label>
                                    <input type="text" id="interest_to" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="witnessCount" class="form-label">Loan Duration Period<span class="required-asterisk">*</span></label>
                                    <select class="form-select" id="duration_period">
                                        <option value="Days">Days</option>
                                        <option value="Weeks">Weeks</option>
                                        <option value="Months">Months</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="witnessCount" class="form-label">Default Loan Duration<span class="required-asterisk">*</span></label>
                                    <input type="number" id="loan_duration" class="form-control">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="witnessCount" class="form-label">Repayment Type<span class="required-asterisk">*</span></label>
                                    <select class="form-select" id="collection_type">
                                        <option value="Daily">Daily</option>
                                        <option value="Weekly">Weekly</option>
                                        <option value="First Of The Month">First Of The Month</option>
                                        <option value="End Of The Month">End Of The Month</option>
                                        <option value="Twice A Month">Twice A Month</option>
                                        <option value="On A Selected Date">On A Selected Date</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="witnessCount" class="form-label">Penalty Period<span class="required-asterisk">*</span></label>
                                    <select class="form-select" id="penalty_period">
                                        <option value="Daily">Per Day</option>
                                        <option value="Weekly">Per Week</option>
                                        <option value="Per Month">Per Month</option>
                                        <option value="Per Installment">Per Installment</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="witnessCount" class="form-label">Penalty Percentage (%)<span class="required-asterisk">*</span></label>
                                    <input type="text" id="panelty_rate" class="form-control">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="witnessCount" class="form-label">Penalty Start date after payment date<span class="required-asterisk">*</span></label>
                                    <input type="text" id="panelty_rate_date" class="form-control">
                                </div>
                            </div>


                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="witnessCount" class="form-label">Guarantee Count<span class="required-asterisk">*</span></label>
                                    <input type="number" id="witnessCount" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="card border-secondary border">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label" style="color: red">Other Charges</label>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="otherChargesDescription" class="form-label">Description</label>
                                                <input type="text" class="form-control" id="otherChargesDescription">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="otherChargesAmount" class="form-label">Type</label>
                                                <select class="form-control" id="charge_type" onchange="change_name_amount(this.value)">
                                                    <option value="Amount">Amount</option>
                                                    <option value="Percentage">Percentage</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="otherChargesAmount" class="form-label"  id="change_amount">Amount</label>
                                                <input type="text" id="otherChargesAmount" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-success" id="addChargesBtn">Add Charges</button>
                                </div>
                                <div class="table-responsive-sm">
                                    <table class="table table-centered mb-0" id="otherchargetable_2">
                                        <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="card border-secondary border">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label" style="color: red">Required Document</label>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="otherChargesDescription"
                                                           class="form-label">Description</label>
                                                    <input type="text" class="form-control" id="otherDocDescription">
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-success" id="addDocBtn">Add Document</button>
                                    </div>
                                    <div class="table-responsive-sm">
                                        <table class="table table-centered mb-0" id="documenttable_2">
                                            <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"> <i class="bi bi-x"></i>
                            &nbsp;Close</button>
                        <button type="button" class="btn btn-success" onclick="update_center()"><i
                                class="bi bi-save"></i>
                            &nbsp;&nbsp;Update Product</button>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@section('script')

    <script src="{{ asset('../JS/loan_category.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        $(document).ready(function() {

            let x = ["#loan_amount","#loan_amount_to","#interest","#interest_to"];
            decimalFormat(x);

// Initial checks when the page loads
            toggleSavingFields();

            // Listen for changes on the enable_saving select box
            $('#enable_saving').change(function() {
                toggleSavingFields();
            });

            // Replace special characters in the company name
            var companyName = {!! json_encode(session('company_name')) !!}.replace(/&/g, ' And ');


            $('#productTable').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="bi bi-clipboard"></i> Copy',
                        className: 'btn btn-secondary',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4,9]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'csv',
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4,9]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                        className: 'btn btn-primary',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4,9]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4,9]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i> Print',
                        className: 'btn btn-info',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4,9]
                        },
                        filename: companyName
                    }
                ]
            });



            $(document).on('click', '.edit-btn', function() {
                // Retrieve the data attributes from the clicked button
                const editButton = $(this);
                const product_name = editButton.data('product-name');
                const product_code = editButton.data('product-code');
                const loan_amount = editButton.data('loan-amount');
                const loan_amount_to = editButton.data('loan-amount-to');
                const interest_method = editButton.data('interest-method');
                const interest_period = editButton.data('interest-period');
                const duration_period = editButton.data('duration-period');
                const loan_interest = editButton.data('loan-interest');
                const loan_interest_to = editButton.data('loan-interest-to');
                const loan_period = editButton.data('loan-period');
                const repayment_type = editButton.data('repayment-type');
                const panelty_period = editButton.data('panelty-period');
                const panelty_percentage = editButton.data('panelty-percentage');
                const panelty_date = editButton.data('panelty-date');
                const guarantee_count = editButton.data('guarantee-count');
                const product_id = editButton.data('product-id');

                // Set the values of the input fields in the modal
                $('#product_name').val(product_name);
                $('#product_code').val(product_code);
                $('#loan_amount').val(loan_amount);
                $('#loan_amount_to').val(loan_amount_to);
                $('#interest_method').val(interest_method);
                $('#interest_period').val(interest_period);
                $('#interest').val(loan_interest);
                $('#interest_to').val(loan_interest_to);
                $('#loan_duration').val(loan_period);
                $('#duration_period').val(duration_period);
                $('#collection_type').val(repayment_type);
                $('#penalty_period').val(panelty_period);
                $('#panelty_rate').val(panelty_percentage);
                $('#panelty_rate_date').val(panelty_date);
                $('#witnessCount').val(guarantee_count);
                $('#product_id').val(product_id);
            });


        });


        function upload_excel() {
            var fileInput = document.getElementById('uploadExcel');  // Get the file input element
            var file = fileInput.files[0];  // Get the selected file

            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var data = new Uint8Array(e.target.result);
                    var workbook = XLSX.read(data, { type: 'array' });

                    // Assuming the first sheet in the Excel file
                    var firstSheet = workbook.Sheets[workbook.SheetNames[0]];

                    // Convert sheet to JSON, starting from the 5th row (index 5 in zero-indexed array)
                    var jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });

                    // Start reading data from the 5th index (skip the first 5 rows)
                    var dataFrom5thRow = jsonData.slice(5);

                    console.log(dataFrom5thRow);  // Debugging: see the data in console

                    // SweetAlert2 confirmation prompt
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to upload the Excel data?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, upload it!',
                        cancelButtonText: 'No, cancel!',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Send data to backend using AJAX
                            $.ajax({
                                url: '/upload-excel-product',  // Your route URL
                                type: 'POST',
                                data: {
                                    excelData: dataFrom5thRow,  // Send the Excel data
                                },
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                                },
                                success: function(response) {
                                    Swal.fire({
                                        position: "center",
                                        icon: "success",
                                        title: "Your Excel data has been uploaded.",
                                    }).then(function () {
                                        window.location.reload();
                                    });
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire(
                                        'Error!',
                                        'There was an issue uploading the file.',
                                        'error'
                                    );
                                    console.error(error);  // Handle errors
                                }
                            });
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            Swal.fire(
                                'Cancelled',
                                'Your Excel data upload was cancelled.',
                                'error'
                            );
                        }
                    });
                };
                reader.readAsArrayBuffer(file);
            }
        }


    </script>
    <script>
        function change_name_amount(value){
            if(value==="Amount"){
                $("#change_amount").text("Amount");
            }else{
                $("#change_amount").text("Percentage %");

            }
        }
        document.getElementById('addChargesBtn').addEventListener('click', function() {
            // Get values from input fields
            var description = document.getElementById('otherChargesDescription').value;
            var amount = document.getElementById('otherChargesAmount').value;
            var charge_type = document.getElementById('charge_type').value;

            if (charge_type === "Amount") {
                document.getElementById('otherChargesAmount').value = parseFloat(amount).toFixed(2);
            } else if (charge_type === "Percentage") { // Assuming the other option is "Percentage"
                document.getElementById('otherChargesAmount').value = amount; // Update the input field with percentage value
            }

            if (!description || !amount) {
                Swal.fire("Error!", "Please enter details !", "error");
            } else {
                var formattedAmount = parseFloat(amount).toFixed(2);
                // Create a new table row
                var newRow = '<tr>' +
                    '<td>' + description + '</td>' +
                    '<td>' + charge_type + '</td>' +
                    '<td>' + formattedAmount + '</td>' +
                    '<td><button type="button" class="btn btn-success delete-row" style="background-color: white; color: #ff0000; border: none"><i class="bi bi-trash fs-3"></i></button></td>' +
                    '</tr>';

                // Append the new row to the table body
                document.getElementById('otherchargetable_2').getElementsByTagName('tbody')[0].insertAdjacentHTML(
                    'beforeend', newRow);

                // Clear the input fields
                document.getElementById('otherChargesDescription').value = '';
                document.getElementById('otherChargesAmount').value = '';
            }


            // Add event listener to delete button of the new row
            var deleteButtons = document.querySelectorAll('.delete-row');
            deleteButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var row = this.closest('tr');
                    row.remove();
                });
            });
        });


        document.getElementById('addDocBtn').addEventListener('click', function() {
            // Get values from input fields
            var description = document.getElementById('otherDocDescription').value;

            if (!description) {
                Swal.fire("Error!", "Please enter details !", "error");
            } else {

                // Create a new table row
                var newRow = '<tr>' +
                    '<td>' + description + '</td>' +
                    '<td><button type="button" class="btn btn-danger delete-row" style="background-color: white; color: #ff0000; border: none"><i class="bi bi-trash fs-3"></i></button></td>' +
                    '</tr>';

                // Append the new row to the table body
                document.getElementById('documenttable_2').getElementsByTagName('tbody')[0].insertAdjacentHTML(
                    'beforeend', newRow);

                // Clear the input fields
                document.getElementById('otherDocDescription').value = '';
            }


            // Add event listener to delete button of the new row
            var deleteButtons = document.querySelectorAll('.delete-row');
            deleteButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var row = this.closest('tr');
                    row.remove();
                });
            });
        });
        // Function to show the modal with data
        function view_doc_3(id, saving, type, amount,amount_type) {
            // Set default values
            document.getElementById('enable_saving').value = 'No';
            $('#saving_account_amount_type').closest('.col-md-6').hide();
            $('#saving_amount').closest('.col-md-6').hide();
            $('#saving_payment').closest('.col-md-6').hide();

            // Show fields if 'saving' is 'Yes'
            if (saving === "Yes") {
                document.getElementById('enable_saving').value = 'Yes';
                $('#saving_account_amount_type').closest('.col-md-6').show();
                $('#saving_amount').closest('.col-md-6').show();
                $('#saving_payment').closest('.col-md-6').show();
            }

            // Populate the modal fields
            document.getElementById('saving_id').value = id;
            document.getElementById('saving_account_amount_type').value = type;
            document.getElementById('saving_amount').value = amount;
            document.getElementById('saving_payment').value = amount_type;

            // Show the modal
            var myModal = new bootstrap.Modal(document.getElementById('info-modal'));
            myModal.show();
        }

        // Function to handle the update
        function updateLoanCategory() {
            var id = document.getElementById('saving_id').value;
            var enableSaving = document.getElementById('enable_saving').value;
            var savingAccountType = document.getElementById('saving_account_amount_type').value;
            var savingAmount = document.getElementById('saving_amount').value;

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to update this loan category?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/update-loan-category',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'), // Ensure CSRF token is included
                            id: id,
                            enable_saving_process: enableSaving,
                            saving_amount_type: savingAccountType,
                            saving_amount: savingAmount
                        },
                        success: function(response) {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Successfully updated !",
                            }).then(function () {
                                window.location.reload();
                            });
                            // You may want to reload the table or perform other actions here
                        },
                        error: function(xhr, status, error) {
                            Swal.fire(
                                'Error!',
                                'There was an error updating the loan category.',
                                'error'
                            );
                        }
                    });
                }
            });
        }


        function toggleSavingFields() {
            var enableSaving = $('#enable_saving').val();
            if (enableSaving === 'No') {
                $('#saving_account_amount_type').closest('.col-md-6').hide();
                $('#saving_amount').closest('.col-md-6').hide();
            } else {
                $('#saving_account_amount_type').closest('.col-md-6').show();
                $('#saving_amount').closest('.col-md-6').show();
            }
        }
    </script>
@endsection
