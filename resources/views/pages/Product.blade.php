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

    #productTable tr,
    #productTable th,
    #productTable td {
        margin: 10px !important;
        padding: 10px 25px !important;
        /* Adjust padding to your preference */
    }

    .table-centered {
        margin: 0 !important;
        padding: 0 !important;
    }

    /* keep the Select2 menu above the Bootstrap modal */
    .modal.show .select2-container--open {
        z-index: 2000 !important;
        /* higher than Bootstrap modal (1055) */
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
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                    <!-- File input -->
                                    <div class="d-flex align-items-center">
                                        <input type="file" id="uploadExcel" accept=".xlsx, .xls" class="form-control me-2" style="width: auto;">
                                        <input type="button" onclick="upload_excel()" class="btn btn-success" value="Upload">
                                    </div>

                                    <div class="ms-auto">
                                        <a href="{{ route('product.create') }}" class="btn btn-primary me-2">
                                            <i class="bi bi-plus-lg me-1"></i> Create Product
                                        </a>
                                        <button type="button" class="btn btn-secondary" id="openCloneModal">
                                            <i class="fas fa-random me-1"></i> Clone / Merge
                                        </button>
                                    </div>
                                </div>
                                {{-- Button to open modal --}}

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
                                                <td class="text-center">{{ number_format($item->Loan_amount ?? 0, 2, '.', ',') }}</td>
                                                <td class="text-center">{{ number_format((float) ($item->Loan_amount_to ?? 0), 2, '.', ',') }}</td>
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

                                                    <a href="{{ route('loan-products.edit', $item->idLoan_Category) }}" class="btn btn-warning">
                                                        <i class="bi bi-pencil-square fs-4"></i> Update
                                                    </a>

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


    <div class="modal fade" id="cloneProductsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Clone / Merge Products to Other Branches</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="cloneProductsForm">
                        @csrf

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Source Branch</label>
                                <select id="source_branch_id" class="form-select" required></select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Target Branches</label>
                                <select id="target_branch_ids" class="form-select" multiple required></select>
                                <small class="text-muted">Select one or more branches (cannot include the source).</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold d-block mb-1">Merge Mode</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="merge_mode" id="mergeSkip" value="skip" checked>
                                    <label class="form-check-label" for="mergeSkip">Skip if exists</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="merge_mode" id="mergeOverwrite" value="overwrite">
                                    <label class="form-check-label" for="mergeOverwrite">Overwrite if exists</label>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="runCloneBtn">
                        <i class="fas fa-play-circle me-1"></i> Run Clone
                    </button>
                </div>

            </div>
        </div>
    </div>


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
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {

        let x = ["#loan_amount", "#loan_amount_to", "#interest", "#interest_to"];
        decimalFormat(x);

        // Initial checks when the page loads
        toggleSavingFields();

        // Listen for changes on the enable_saving select box
        $('#enable_saving').change(function() {
            toggleSavingFields();
        });

        // Replace special characters in the company name
        var companyName = {
            !!json_encode(session('company_name')) !!
        }.replace(/&/g, ' And ');


        $('#productTable').DataTable({
            dom: 'Bfrtip',
            responsive: true,
            buttons: [{
                    extend: 'copy',
                    text: '<i class="bi bi-clipboard"></i> Copy',
                    className: 'btn btn-secondary',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 9]
                    },
                    filename: companyName
                },
                {
                    extend: 'csv',
                    text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV',
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 9]
                    },
                    filename: companyName
                },
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                    className: 'btn btn-primary',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 9]
                    },
                    filename: companyName
                },
                {
                    extend: 'pdf',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    className: 'btn btn-danger',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 9]
                    },
                    filename: companyName
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Print',
                    className: 'btn btn-info',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 9]
                    },
                    filename: companyName
                }
            ]
        });






    });


    function upload_excel() {
        const fileInput = document.getElementById('uploadExcel');
        const file = fileInput.files[0];

        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, {
                type: 'array'
            });
            const sheet = workbook.Sheets[workbook.SheetNames[0]];
            const rows = XLSX.utils.sheet_to_json(sheet, {
                header: 1
            });
            const dataFrom5thRow = rows.slice(5); // Skip first 5 rows

            const finalData = [];

            for (let row of dataFrom5thRow) {
                if (!row[1]) continue; // Skip if product_code is missing

                const product_code = row[1];
                const product_name = product_code;

                // ⬇️ Collect Other Charges from columns 15–26 (4 charges max)
                const othercharges = [];
                for (let i = 15; i <= 26; i += 3) {
                    const desc = row[i];
                    const type = row[i + 1];
                    const amount = parseFloat(row[i + 2]) || 0;

                    if (desc && type && amount) {
                        othercharges.push([String(desc).trim(), String(type).trim(), amount]);
                    }
                }

                // ⬇️ Collect Required Documents from columns 27, 28, 29
                const document = [];
                for (let i = 27; i <= 29; i++) {
                    if (row[i]) {
                        document.push([String(row[i]).trim()]);
                    }
                }

                // ⬇️ Build the product entry
                finalData.push({
                    product_name: product_name,
                    product_code: product_code,
                    loan_amount_from: parseFloat(row[2]) || 0,
                    loan_amount_to: parseFloat(row[3]) || 0,
                    interest_from: parseFloat(row[4]) || 0,
                    interest_to: parseFloat(row[5]) || 0,
                    interest_method: "Flat Rate",
                    interest_period: "Per Month",
                    duration_period: "Weeks",
                    loan_duration: parseFloat(row[7]) || 0,
                    collection_type: row[6] || "Weekly",
                    penalty_period: row[8] || "Weekly",
                    panelty_rate: parseFloat(row[9]) || 0,
                    panelty_rate_date: '1',
                    witnessCount: 2,
                    period_count: 1,
                    enable_saving: 'No',

                    saving_account_amount_type: row[30] ? row[30].toString() : "pre_defined",
                    saving_payment: row[31] ? row[31].toString() : "0",
                    saving_amount: parseFloat(row[32]) || 0,

                    default_loan_duration_period: "Weeks",
                    othercharges: othercharges,
                    document: document,
                    level_data: []
                });
            }

            // ✅ Confirm and Send to Server
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to upload the Excel data?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, upload it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/upload-excel-product',
                        method: 'POST',
                        data: {
                            excelData: finalData
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            Swal.fire("Success", res.message, "success").then(() => location.reload());
                        },
                        error: function(err) {
                            Swal.fire("Error", "Upload failed", "error");
                            console.error(err);
                        }
                    });
                }
            });
        };

        reader.readAsArrayBuffer(file);
    }
</script>
<script>
    function change_name_amount(value) {
        if (value === "Amount") {
            $("#change_amount").text("Amount");
        } else {
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
    function view_doc_3(id, saving, type, amount, amount_type) {
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
                        }).then(function() {
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

<script>
    (function() {
        const modalEl = document.getElementById('cloneProductsModal');
        const $source = $('#source_branch_id');
        const $targets = $('#target_branch_ids');

        // Open modal
        $('#openCloneModal').on('click', function() {
            $('#cloneProductsModal').modal('show');
        });

        function initSelect2() {
            const $modal = $('#cloneProductsModal');

            $('#source_branch_id').select2({
                placeholder: 'Select source branch',
                width: '100%',
                dropdownParent: $modal
            });

            $('#target_branch_ids').select2({
                placeholder: 'Select target branches',
                width: '100%',
                dropdownParent: $modal
            });
        }


        // Load branches (id, text)
        async function loadBranches() {
            try {
                const res = await fetch('{{ route('
                    api.branches ') }}');
                const data = await res.json();

                $source.empty();
                $targets.empty();

                $source.append(new Option('— Select —', '', true, false)).trigger('change');
                data.forEach(b => {
                    $source.append(new Option(b.text, b.id, false, false));
                    $targets.append(new Option(b.text, b.id, false, false));
                });

                $source.trigger('change');
                $targets.trigger('change');
            } catch (e) {
                console.error(e);
                Swal.fire('Error', 'Failed to load branches.', 'error');
            }
        }

        // Prevent selecting source in targets
        $source.on('change', function() {
            const src = $(this).val();
            const selectedTargets = $targets.val() || [];
            if (src && selectedTargets.includes(src)) {
                $targets.val(selectedTargets.filter(v => v !== src)).trigger('change');
            }
        });

        // Submit
        $('#runCloneBtn').on('click', async function() {
            const source_branch_id = $source.val();
            const target_branch_ids = $targets.val() || [];
            const merge_mode = $('input[name="merge_mode"]:checked').val();
            const token = $('meta[name="csrf-token"]').attr('content') || $('[name="_token"]').val();

            if (!source_branch_id) {
                Swal.fire('Required', 'Please select a source branch.', 'warning');
                return;
            }
            if (target_branch_ids.length === 0) {
                Swal.fire('Required', 'Please select at least one target branch.', 'warning');
                return;
            }
            if (target_branch_ids.includes(source_branch_id)) {
                Swal.fire('Invalid', 'Source branch cannot be a target.', 'error');
                return;
            }

            try {
                const resp = await fetch('{{ route('
                    loan - category.clone ') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            source_branch_id,
                            target_branch_ids,
                            merge_mode
                            // If you ever want to restrict to certain product IDs:
                            // category_ids: [/* IDs here */]
                        })
                    });

                const json = await resp.json();

                if (!resp.ok) {
                    throw new Error(json?.message || 'Request failed');
                }

                // Show brief summary
                let html = '';
                if (json?.summary?.per_target) {
                    html += '<ul class="text-start">';
                    Object.entries(json.summary.per_target).forEach(([bid, stats]) => {
                        if (stats.skipped_same_branch) {
                            html += `<li><b>Branch ${bid}</b>: skipped (same as source)</li>`;
                        } else {
                            html += `<li><b>Branch ${bid}</b>: created ${stats.created || 0}, overwritten ${stats.overwritten || 0}, skipped ${stats.skipped || 0}</li>`;
                        }
                    });
                    html += '</ul>';
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Clone complete',
                    html: html || 'Products cloned/merged successfully.'
                });

                $('#cloneProductsModal').modal('hide');

            } catch (err) {
                console.error(err);
                Swal.fire('Error', err.message || 'Clone failed.', 'error');
            }
        });

        // On modal show, init and load
        $('#cloneProductsModal').on('shown.bs.modal', function() {
            initSelect2();
            loadBranches();
        });

    })();
</script>
@endsection