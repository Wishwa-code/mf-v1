@extends('layout.admin')

@section('head')
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Custom CSS -->
    <style>

        .main-topic {
            background-color: #ffffff; /* Light gray background for main topics */
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
@endsection

@section('content')
    <div>
        <div class="row mt-3">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                <tr>
                                    <th class="fw-bolder">Shortcut Permission</th>
                                    <th class="fw-bolder text-center">Access</th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- Customer Main Topic -->
                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Add Customer</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="1" type="checkbox">
                                    </td>
                                </tr>
                                <!-- Customer Subtopics -->

                                <!-- Center Main Topic -->
                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">View Customer</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="2" type="checkbox">
                                    </td>
                                </tr>
                                <!-- Center Subtopics -->

                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Assign Customers to group</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="3" type="checkbox">
                                    </td>
                                </tr>
                                <!-- Loan Category Subtopics -->
                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">View Products</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="4" type="checkbox">
                                    </td>
                                </tr>

                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Pending Loans</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="5" type="checkbox">
                                    </td>
                                </tr>




                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Current Loans</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="6" type="checkbox">
                                    </td>
                                </tr>
                                <!-- Loan Category Subtopics -->


                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Loan In arrears</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="7" type="checkbox">
                                    </td>
                                </tr>
                                <!-- Loan Category Subtopics -->

                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Add Repayment</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="8" type="checkbox">
                                    </td>
                                </tr>
                                <!-- Loan Category Subtopics -->




                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Repayment details</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="9" type="checkbox">
                                    </td>
                                </tr>
                                <!-- Loan Category Subtopics -->

                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Collector wise collections</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="10" type="checkbox">
                                    </td>
                                </tr>


                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Loan Calculator</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="11" type="checkbox">
                                    </td>
                                </tr>

                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Add Expenses</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="12" type="checkbox">
                                    </td>
                                </tr>

                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Add Income</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="13" type="checkbox">
                                    </td>
                                </tr>



                                </tbody>
                            </table>

                            <input type="button" class="btn btn-danger" value="Update Shortcut" onclick="saveShortcut(event)">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Settings</h5>
                        <hr>
                        <!-- Loan Disbursement Policy -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Loan Disbursement Policy</label>
                            <div class="d-flex flex-column gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="loan_policy" id="strict_mode" value="strict">
                                    <label class="form-check-label" for="strict_mode">
                                        <span class="fw-bold">Strict mode</span> → Don’t allow disbursement if balance is insufficient.
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="loan_policy" id="flexible_mode" value="flexible" checked>
                                    <label class="form-check-label" for="flexible_mode">
                                        <span class="fw-bold">Flexible mode</span> → Allow disbursement and show the account in minus (overdraft-like).
                                    </label>
                                </div>
                                <button id="btnUpdateLoanPolicy" class="btn btn-primary mt-2" style="max-width: 150px;">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Update
                                </button>
                            </div>
                            <small class="text-muted">Controls how loan disbursements are handled.</small>
                        </div>
                        <hr>
                        <!-- Payment Section Member Name -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Payment Section Member Name</label>
                            <div class="d-flex gap-2">
                                <select id="payment_member_name" class="form-select" style="max-width: 300px;">
                                    <option value="full_name">Full Name</option>
                                    <option value="with_initial">With Initial</option>
                                    <option value="only_first_name">Only First Name</option>
                                    <option value="only_last_name">Only Last Name</option>
                                </select>
                                <button id="btnUpdatePaymentMemberName" class="btn btn-primary">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Update
                                </button>
                            </div>
                            <small class="text-muted">Controls how member names show on the Payment section.</small>
                        </div>
                        <hr>
                        <!-- Payment Backdate -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Payment Backdate</label>
                            <div class="d-flex gap-2">
                                <select id="payment_backdate" class="form-select" style="max-width: 300px;">
                                    <option value="disabled">Disable</option>
                                    <option value="enabled">Enable</option>
                                </select>
                                <button id="btnUpdatePaymentBackdate" class="btn btn-primary">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Update
                                </button>
                            </div>
                            <small class="text-muted">Allow entering payments with a back-dated date when enabled.</small>
                        </div>

                        <hr>
                        <!-- Loan Number Order -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Loan Number Order</label>
                            <div class="d-flex gap-2">
                                <select id="loan_order" class="form-select" style="max-width: 300px;">
                                    <option value="create_date">Create Date</option>
                                    <option value="loan_number">Loan Number</option>
                                    <option value="issue_date">Issue Date</option>
                                </select>
                                <button id="btnUpdateLoanOrder" class="btn btn-primary">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Update
                                </button>
                            </div>
                            <small class="text-muted">Controls the default ordering of loans in lists and dropdowns.</small>
                        </div>

                        <hr>
                        <!-- Maximum Allowed Loans -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Maximum Allowed Loans</label>
                            <div class="d-flex gap-2">
                                <input type="number" id="max_allowed_loans" class="form-control" min="1" max="50" value="3" style="max-width: 300px;">
                                <button id="btnUpdateMaxLoans" class="btn btn-primary">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Update
                                </button>
                            </div>
                            <small class="text-muted">Maximum number of loans a customer can have at once (pending + current).</small>
                        </div>

                        <hr>
                        <!-- Document Types -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Document Types</label>
                            <small class="text-muted d-block mb-3">Manage predefined document types for customer registration.</small>
                            
                            <div class="mb-3">
                                <div class="input-group mb-2">
                                    <input type="text" id="new_document_type" class="form-control" placeholder="Enter document type" maxlength="100">
                                    <button id="btnAddDocumentType" class="btn btn-outline-success">
                                        <i class="fa-solid fa-plus me-1"></i> Add
                                    </button>
                                </div>
                            </div>
                            
                            <div id="document_types_list" class="mb-3">
                                <!-- Document types will be loaded here -->
                            </div>
                            
                            <button id="btnUpdateDocumentTypes" class="btn btn-primary">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                            </button>
                        </div>


                        <hr>
                        <!-- Collector Account Transaction Modes -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Collector Account Transaction Modes</label>
                            <div class="d-flex flex-column gap-2">
                                <div class="form-check">
                                    <input class="form-check-input collector-mode" type="checkbox" id="mode_cash_bank" value="cash_bank">
                                    <label class="form-check-label" for="mode_cash_bank">Cash</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input collector-mode" type="checkbox" id="mode_bank_deposit" value="bank_deposit">
                                    <label class="form-check-label" for="mode_bank_deposit">Bank Deposit</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input collector-mode" type="checkbox" id="mode_cheques" value="cheques">
                                    <label class="form-check-label" for="mode_cheques">Cheques</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input collector-mode" type="checkbox" id="mode_collector_account" value="collector_account">
                                    <label class="form-check-label" for="mode_collector_account">Collector Account</label>
                                </div>

                                <button id="btnUpdateCollectorModes" class="btn btn-primary mt-2" style="max-width: 220px;">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Update Modes
                                </button>
                            </div>
                            <small class="text-muted">Select which transaction modes are available when recording collector account transactions.</small>
                        </div>



                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <!-- jQuery and Bootstrap 5 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="../JS/profile.js"></script>
    <!-- Custom JS -->
    <script>

        $(document).ready(function() {
            load_data_shortcut();
            // you already call load_data_shortcut(); keep it
            load_settings();

            $('#btnUpdatePaymentMemberName').on('click', function (e) {
                e.preventDefault();
                const value = $('#payment_member_name').val(); // 'full_name' or 'with_initial'
                save_setting('payment_member_name', value);
            });

            $('#btnUpdateLoanPolicy').on('click', function (e) {
                e.preventDefault();
                const value = $('input[name="loan_policy"]:checked').val(); // 'strict' or 'flexible'

                if (!value) {
                    Swal.fire("Warning", "Please select a Loan Disbursement Policy before updating.", "warning");
                    return;
                }

                save_setting('loan_disbursement_policy', value);
            });

            $('#btnUpdatePaymentBackdate').on('click', function (e) {
                e.preventDefault();
                const value = $('#payment_backdate').val(); // 'enabled' | 'disabled'
                save_setting('payment_backdate', value);
            });

                        $('#btnUpdateLoanOrder').on('click', function (e) {
                e.preventDefault();
                const value = $('#loan_order').val(); // 'create_date' | 'loan_number' | 'issue_date'
                save_setting('loan_order', value);
            });

            $('#btnUpdateMaxLoans').on('click', function (e) {
                e.preventDefault();
                const value = $('#max_allowed_loans').val();
                if (!value || value < 1 || value > 50) {
                    Swal.fire("Warning", "Max allowed loans must be between 1 and 50.", "warning");
                    return;
                }
                save_setting('max_allowed_loans', value);
            });

            // Document Types Management
            $('#btnAddDocumentType').on('click', function (e) {
                e.preventDefault();
                addDocumentType();
            });

            $('#new_document_type').on('keypress', function (e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    addDocumentType();
                }
            });

            $('#btnUpdateDocumentTypes').on('click', function (e) {
                e.preventDefault();
                saveDocumentTypes();
            });

            $('#btnUpdateCollectorModes').on('click', function (e) {
                e.preventDefault();
                const selected = $('.collector-mode:checked').map(function(){ return $(this).val(); }).get();

                // Optional: prevent empty selection
                if (selected.length === 0) {
                    Swal.fire("Warning", "Select at least one mode.", "warning");
                    return;
                }

                // Save as JSON string
                save_setting('collector_txn_modes', JSON.stringify(selected));
            });

        });


        const getSelectedCheckboxValues = () => {
            const checkboxValues = {};
            $('input.access_module:checked').each(function() {
                const id = $(this).attr('id');
                let key;
                switch (id) {
                    case '1':
                        key = 'Add_Customer';
                        break;
                    case '2':
                        key = 'View_Customer';
                        break;
                    case '3':
                        key = 'Assign_Customers_to_group';
                        break;
                    case '4':
                        key = 'View_Products';
                        break;
                    case '5':
                        key = 'Pending_Loans';
                        break;
                    case '6':
                        key = 'Current_Loans';
                        break;
                    case '7':
                        key = 'Loan_In_arrears';
                        break;
                    case '8':
                        key = 'Add_Repayment';
                        break;
                    case '9':
                        key = 'Repayment_details';
                        break;
                    case '10':
                        key = 'Collector_wise_collections';
                        break;
                    case '11':
                        key = 'Loan_Calculator';
                        break;
                    case '12':
                        key = 'Add_Expenses';
                        break;
                    case '13':
                        key = 'Add_Income';
                        break;
                    default:
                        key = `Checkbox_${id}`;
                }
                checkboxValues[key] = 1;
            });
            return checkboxValues;
        }

        const saveShortcut = (e) => {
            e.preventDefault();

            const selectedCheckboxValues = getSelectedCheckboxValues();

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to update these shortcuts?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Update it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "/shortcuts",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: { checkboxValues: selectedCheckboxValues }, // Use ES6 shorthand
                        success: function (data) {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Successfully updated shortcuts!",
                            }).then(function () {
                                load_data_shortcut();
                            });
                        },
                    });
                }
            });
        };


        const load_data_shortcut = () => {
            $.ajax({
                type: "GET",
                url: "/shortcuts/all",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data) {
                    console.log(data);
                    const items = data.items;
                    items.forEach(item => {
                        switch (item.name) {
                            case 'Add_Customer':
                                $('#1').prop('checked', true);
                                break;
                            case 'View_Customer':
                                $('#2').prop('checked', true);
                                break;
                            case 'Assign_Customers_to_group':
                                $('#3').prop('checked', true);
                                break;
                            case 'View_Products':
                                $('#4').prop('checked', true);
                                break;
                            case 'Pending_Loans':
                                $('#5').prop('checked', true);
                                break;
                            case 'Current_Loans':
                                $('#6').prop('checked', true);
                                break;
                            case 'Loan_In_arrears':
                                $('#7').prop('checked', true);
                                break;
                            case 'Add_Repayment':
                                $('#8').prop('checked', true);
                                break;
                            case 'Repayment_details':
                                $('#9').prop('checked', true);
                                break;
                            case 'Collector_wise_collections':
                                $('#10').prop('checked', true);
                                break;
                            case 'Loan_Calculator':
                                $('#11').prop('checked', true);
                                break;
                            case 'Add_Expenses':
                                $('#12').prop('checked', true);
                                break;
                            case 'Add_Income':
                                $('#13').prop('checked', true);
                                break;
                        }
                    });
                },
                error: function (error) {
                    console.log('Error:', error);
                }
            });
        };

        // ========== SETTINGS ==========

        // Load settings into UI
        const load_settings = () => {
            $.ajax({
                type: "GET",
                url: "/settings/all",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data) {
                    const items = data.items || {};

// Payment Member Name
                    if (items.payment_member_name) {
                        $('#payment_member_name').val(items.payment_member_name);
                    }

// Loan Disbursement Policy
                    if (items.loan_disbursement_policy) {
                        $(`input[name="loan_policy"][value="${items.loan_disbursement_policy}"]`).prop('checked', true);
                    }

// NEW: Payment Backdate
                    if (items.payment_backdate) {
                        $('#payment_backdate').val(items.payment_backdate); // 'enabled' | 'disabled'
                    }

// NEW: Loan Number Order
                    if (items.loan_order) {
                        $('#loan_order').val(items.loan_order); // 'create_date' | 'loan_number' | 'issue_date'
                    }

                    // Max Allowed Loans
                    if (items.max_allowed_loans) {
                        $('#max_allowed_loans').val(items.max_allowed_loans);
                    }

                    // Document Types
                    if (items.document_types) {
                        try {
                            const documentTypes = JSON.parse(items.document_types);
                            loadDocumentTypesList(documentTypes);
                        } catch (e) {
                            console.error('Error parsing document types:', e);
                            loadDocumentTypesList(getDefaultDocumentTypes());
                        }
                    } else {
                        loadDocumentTypesList(getDefaultDocumentTypes());
                    }

                    // Collector Transaction Modes
                    if (items.collector_txn_modes) {
                        let modes = [];
                        try {
                            // expected to be a JSON array
                            modes = JSON.parse(items.collector_txn_modes);
                            if (!Array.isArray(modes)) modes = [];
                        } catch (e) {
                            // fallback if stored as comma-separated
                            modes = String(items.collector_txn_modes).split(',').map(s => s.trim()).filter(Boolean);
                        }

                        // Uncheck all first, then check the ones present
                        $('.collector-mode').prop('checked', false);
                        modes.forEach(v => $(`.collector-mode[value="${v}"]`).prop('checked', true));
                    }
                },
                error: function (xhr) {
                    console.error('Settings load error:', xhr.responseText || xhr.statusText);
                }
            });
        };

        // Save a single setting with confirmation
        const save_setting = (key, value) => {
            Swal.fire({
                title: "Are you sure?",
                text: "Update this setting?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Update",
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    type: "POST",
                    url: "/settings/upsert",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: { key, value },
                    success: function () {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Setting updated!",
                            timer: 1400,
                            showConfirmButton: false
                        });
                    },
                    error: function (xhr) {
                        Swal.fire("Error", xhr.responseJSON?.message || "Failed to update setting", "error");
                    }
                });
            });
        };

        // ========== DOCUMENT TYPES MANAGEMENT ==========

        // Get default document types
        const getDefaultDocumentTypes = () => {
            return [
                'NIC Copy',
                'Income Certificate'
            ];
        };

        // Load document types list in UI
        const loadDocumentTypesList = (documentTypes) => {
            const container = $('#document_types_list');
            container.empty();

            if (!documentTypes || documentTypes.length === 0) {
                container.html('<p class="text-muted">No document types added yet.</p>');
                return;
            }

            documentTypes.forEach((type, index) => {
                const item = $(`
                    <div class="d-flex align-items-center mb-2 document-type-item" data-index="${index}">
                        <div class="badge bg-light text-dark me-2 flex-grow-1 text-start py-2 px-3">
                            ${type}
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-doc-type" data-index="${index}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                `);
                container.append(item);
            });

            // Attach remove handlers
            $('.remove-doc-type').on('click', function() {
                const index = $(this).data('index');
                removeDocumentType(index);
            });
        };

        // Add new document type
        const addDocumentType = () => {
            const newType = $('#new_document_type').val().trim();
            
            if (!newType) {
                Swal.fire("Warning", "Please enter a document type.", "warning");
                return;
            }

            // Get current types
            const currentTypes = getCurrentDocumentTypes();
            
            // Check for duplicates (case insensitive)
            if (currentTypes.some(type => type.toLowerCase() === newType.toLowerCase())) {
                Swal.fire("Warning", "This document type already exists.", "warning");
                return;
            }

            // Add new type
            currentTypes.push(newType);
            loadDocumentTypesList(currentTypes);
            
            // Clear input
            $('#new_document_type').val('');
        };

        // Remove document type
        const removeDocumentType = (index) => {
            const currentTypes = getCurrentDocumentTypes();
            currentTypes.splice(index, 1);
            loadDocumentTypesList(currentTypes);
        };

        // Get current document types from UI
        const getCurrentDocumentTypes = () => {
            const types = [];
            $('.document-type-item').each(function() {
                const type = $(this).find('.badge').text().trim();
                if (type) {
                    types.push(type);
                }
            });
            return types;
        };

        // Save document types to database
        const saveDocumentTypes = () => {
            const documentTypes = getCurrentDocumentTypes();
            
            if (documentTypes.length === 0) {
                Swal.fire("Warning", "Please add at least one document type.", "warning");
                return;
            }

            const jsonValue = JSON.stringify(documentTypes);
            
            Swal.fire({
                title: "Are you sure?",
                text: "Update document types?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Update",
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    type: "POST",
                    url: "/settings/upsert",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: { 
                        key: 'document_types', 
                        value: jsonValue 
                    },
                    success: function () {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Document types updated!",
                            timer: 1400,
                            showConfirmButton: false
                        });
                    },
                    error: function (xhr) {
                        Swal.fire("Error", xhr.responseJSON?.message || "Failed to update document types", "error");
                    }
                });
            });
        };

    </script>
@endsection
