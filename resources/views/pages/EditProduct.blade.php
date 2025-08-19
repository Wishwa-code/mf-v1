@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"/>

    <style>
        .style-tr > td {
            padding: 2px 15px;
        }

        .section-break {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .section-title {
            background-color: #c9c9c9; /* Background color for the title */
            color: #726262; /* Text color for the title */
            padding: 5px; /* Padding for the title */
            font-weight: bold;
            font-size: 16px;
            border-radius: 5px 5px 0 0; /* Rounded corners at the top */
        }

        .section-title:after {
            content: '';
            display: block;
            height: 0px; /* Height of the colored line */
            background-color: #8f8f8f; /* Color of the line */
            border-radius: 0 0 5px 5px; /* Rounded corners at the bottom */
            margin-top: 5px;
        }

        #levels-container {
            display: flex;
            flex-direction: column-reverse; /* Makes new levels appear on top */
        }

        .level-section {
            margin-bottom: 20px; /* Optional: Space between levels */
        }

        .highlight-card {
            background-color: #f8f9fa;
            border: 1px solid #1A2942;
        }

        .highlight-card .card-header {
            background-color: #1A2942;
            color: #fff;
        }

        .highlight-card .card-body {
            font-size: 1.2em; /* Increase font size */
        }

        .table {
            margin-bottom: 0;
        }

        .table th, .table td {
            text-align: center; /* Center the text */
            vertical-align: middle; /* Center vertically */
            word-wrap: break-word; /* Ensure content wraps within the cell */
            white-space: normal;   /* Allow content to break within the cell */
        }

        .table-bordered th, .table-bordered td {
            border: 1px solid #dee2e6;
        }

        .level-card {
            display: flex;
            flex-direction: column;
            margin-bottom: 1rem;
        }

        .card-body {
            flex: 1 1 auto;
            padding: 1rem;
        }

        .card-header {
            padding: 0.75rem 1.25rem;
            background-color: #001434;
            color: #fff;
        }

        .custom-scrollbar {
            max-height: 800px;
            overflow-y: auto;
        }

        .table-responsive {
            overflow: visible; /* Ensure table expands with content */
        }

        .table-responsive.custom-scrollbar {
            overflow: hidden; /* Hide scroll bars */
        }
    </style>
@endsection


@section('content')
    <div>



                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h4 class="page-title">Edit Product Details</h4>
                        </div>

                        <input type="hidden" id="product_id" value="{{ $id }}">

                        <div class="modal-body">

                            <div class="container">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="product_name" class="form-label">Product Name<span
                                                    class="required-asterisk">*</span></label>
                                            <input type="text" id="product_name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="product_name" class="form-label">Product Code<span
                                                        class="required-asterisk">*</span></label>
                                            <input type="text" id="product_code" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="interest_method" class="form-label">Interest Method<span
                                                    class="required-asterisk">*</span></label>
                                            <select class="form-select" id="interest_method">
                                                <option value="Flat Rate">Flat Rate</option>
                                                <option value="Draft">Draft</option>
                                                <option value="Reducing Balance">Reducing Balance</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Minimum Loan Amount<span class="required-asterisk">*</span></label>
                                                <input type="text" id="loan_amount_from" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Maximum Loan Amount<span class="required-asterisk">*</span></label>
                                                <input type="text" id="loan_amount_to" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-2">
                                        <label for="interest" class="form-label">Minimum Interest(%)<span
                                                    class="required-asterisk">*</span></label>
                                        <input type="text" id="interest_from" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="interest" class="form-label">Maximum Interest(%)<span
                                                    class="required-asterisk">*</span></label>
                                        <input type="text" id="interest_to" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="interest_period" class="form-label">Loan Interest Period<span
                                                    class="required-asterisk">*</span></label>
                                            <select class="form-select" id="interest_period">
                                                <option value="Daily">Per Day</option>
                                                <option value="Weekly">Per Week</option>
                                                <option value="Per Month">Per Month</option>
                                                <option value="Per Year">Per Year</option>
                                                <option value="Per Loan">Per Loan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label for="period_count" class="form-label">Default Loan Period<span
                                                    class="required-asterisk">*</span></label>
                                            <input type="number" id="period_count" class="form-control">
                                        </div>

                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label for="period_count" class="form-label">Type</label>
                                            <select class="form-select" id="default_loan_duration_period">
                                                <option value="Days">Days</option>
                                                <option value="Weeks">Weeks</option>
                                                <option value="Months">Months</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="witnessCount" class="form-label">Guarantee Count<span
                                                        class="required-asterisk">*</span></label>
                                                <input type="number" id="witnessCount" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row mb-3 section-break">
                                    <div class="col-12">
                                        <div class="section-title">
                                            Loan duration and Repayments
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label for="loan_duration" class="form-label">Loan Duration<span
                                                    class="required-asterisk">*</span></label>
                                            <input type="number" id="loan_duration" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label for="loan_duration" class="form-label">Type<span
                                                    class="required-asterisk">*</span></label>
                                            <select class="form-select" id="duration_period" onchange="repayment_type(this.value)">
                                                <option value="Days">Days</option>
                                                <option value="Weeks">Weeks</option>
                                                <option value="Months">Months</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2">

                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="collection_type" class="form-label">Repayment Type<span
                                                    class="required-asterisk">*</span></label>
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

                                </div>
                                <div class="row mb-3 section-break">
                                    <div class="col-12">
                                        <div class="section-title">
                                            Penalty Details
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="panelty_rate" class="form-label">Penalty Percentage (%)<span
                                                    class="required-asterisk">*</span></label>
                                            <input type="text" id="panelty_rate" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="penalty_period" class="form-label">Penalty Period<span
                                                    class="required-asterisk">*</span></label>
                                            <select class="form-select" id="penalty_period">
                                                <option value="Daily">Per Day</option>
                                                <option value="Weekly">Per Week</option>
                                                <option value="Per Month">Per Month</option>
                                                <option value="Per Installment">Per Installment</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">

                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label for="panelty_rate_date" class="form-label">Penalty Start After<span
                                                    class="required-asterisk">*</span></label>
                                            <input type="number" id="panelty_rate_date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label for="loan_duration" class="form-label">Type<span
                                                    class="required-asterisk">*</span></label>
                                            <select class="form-select" id="duration_period_panelty">
                                                <option value="Days">Days</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="row mb-3 section-break">
                                <div class="col-12">
                                    <div class="section-title">
                                        Other Chargers
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="otherChargesDescription"
                                                       class="form-label">Description</label>
                                                <input type="text" class="form-control" id="otherChargesDescription">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="otherChargesAmount" class="form-label">Type</label>
                                                <select class="form-control" id="charge_type"
                                                        onchange="change_name_amount(this.value)">
                                                    <option value="Amount">Amount</option>
                                                    <option value="Percentage">Percentage</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="otherChargesAmount" class="form-label" id="change_amount">Amount</label>
                                                <input type="text" id="otherChargesAmount" class="form-control"
                                                       oninput="validateAmount()">
                                                <div id="amountError" style="color: red;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="addChargesBtn">Add Charges
                                    </button>
                                </div>
                                <div class="table-responsive-sm">
                                    <table class="table table-centered mb-0" id="otherchargetable">
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

                            <div class="row mb-3 section-break">
                                <div class="col-12">
                                    <div class="section-title">
                                        Required Documents
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="otherChargesDescription"
                                                           class="form-label">Description</label>
                                                    <input type="text" class="form-control" id="otherDocDescription">
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-primary" id="addDocBtn">Add Document
                                        </button>
                                    </div>
                                    <div class="table-responsive-sm">
                                        <table class="table table-centered mb-0" id="documenttable">
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

                        <div class="row mb-3 section-break">
                            <div class="col-12">
                                <div class="section-title">
                                    Savings Account
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="interest_method" class="form-label">Enable Savings Account Process</label>
                                    <select class="form-select" id="enable_saving">
                                        <option value="Yes">Yes</option>
                                        <option value="No" selected>No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="interest_method" class="form-label">Saving Account Amount Type</label>
                                    <select class="form-select" id="saving_account_amount_type">
                                        <option value="pre_defined" selected>Pre Defined Amount</option>
                                        <option value="percentage">Percentage From Total Loan Amount</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="loan_amount" class="form-label">Amount<span class="required-asterisk">*</span></label>
                                    <input type="text" id="saving_amount" class="form-control" value="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="interest_method" class="form-label">Saving Payment Type</label>
                                    <select class="form-select" id="saving_payment">
                                        <option value="0" selected>Deduct Savings From Installment</option>
                                        <option value="1">Collect Savings Separately</option>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="row mb-3 section-break">
                            <div class="col-12">
                                <div class="section-title">
                                    Loan Approval Levels
                                </div>
                            </div>
                        </div>

                        <div class="mb-12">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-primary" id="addLevelBtn1">Add Level</button>
                                        <button type="button" class="btn btn-danger" id="removeLevelBtn1">Remove Level</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="levels-container" class="row"></div>


                        <div class="modal-footer"   >
                            <button type="button" class="btn btn-success " onclick="updateLoanCategory(event)"><i
                                    class="bi bi-save"></i>&nbsp;&nbsp;Update Product
                            </button>
                        </div>

                    </div> <!-- end card-->
                </div> <!-- end col -->
    </div>
@endsection

@section('script')
    <script src="{{ asset('../JS/loan_category.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



    <script>
        $(document).ready(function() {
            let x = ["#loan_amount_from","#loan_amount_to","#interest_from","#interest_to"];
            decimalFormat(x);


            // Inject server data from controller into JS
            const loanCategory = @json($loanCategory);
            const otherCharges = @json($otherCharges);
            const requiredDocuments = @json($requiredDocuments);
            const levelsData = @json($levelsData);

            // === Fill Input Fields ===
            $('#product_name').val(loanCategory.Name);
            $('#product_code').val(loanCategory.Product_code);
            $('#interest_method').val(loanCategory.Interest_method);
            $('#loan_amount_from').val(loanCategory.Loan_amount);
            $('#loan_amount_to').val(loanCategory.Loan_amount_to);
            $('#interest_from').val(loanCategory.Loan_interest);
            $('#interest_to').val(loanCategory.Loan_interest_to);
            $('#interest_period').val(loanCategory.Interest_period);
            $('#period_count').val(loanCategory.Interest_Period_Count);
            $('#default_loan_duration_period').val(loanCategory.default_loan_duration_period);
            $('#witnessCount').val(loanCategory.Guarantee_count);
            $('#loan_duration').val(loanCategory.Loan_period);
            $('#duration_period').val(loanCategory.Duration_period);
            repayment_type(loanCategory.Duration_period);
            $('#collection_type').val(loanCategory.Repayment_type);
            $('#panelty_rate').val(loanCategory.Panelty_pecentage);
            $('#penalty_period').val(loanCategory.Panelty_period);
            $('#panelty_rate_date').val(loanCategory.Panelty_date);
            $('#duration_period_panelty').val('Days'); // always Days as per blade

            $('#enable_saving').val(loanCategory.enable_saving_process);
            $('#saving_account_amount_type').val(loanCategory.saving_amount_type);
            $('#saving_amount').val(loanCategory.saving_amount);
            $('#saving_payment').val(loanCategory.saving_payment);




            // === Fill Other Charges Table ===
            otherCharges.forEach(row => {
                $('#otherchargetable tbody').append(`
                <tr>
                    <td>${row.Description}</td>
                    <td>${row.charge_type}</td>
                    <td>${row.Amount}</td>
                    <td>
                        <button type="button" class="btn btn-success delete-row" style="background-color: white; color: #ff0000; border: none">
                            <i class="bi bi-trash fs-3"></i>
                        </button>
                    </td>
                </tr>
            `);
            });

            // === Fill Required Documents Table ===
            requiredDocuments.forEach(doc => {
                $('#documenttable tbody').append(`
                <tr>
                    <td>${doc.Name}</td>
                    <td>
                        <button type="button" class="btn btn-danger delete-row" style="background-color: white; color: #ff0000; border: none">
                            <i class="bi bi-trash fs-3"></i>
                        </button>
                    </td>
                </tr>
            `);
            });

            let levelCount = 0;

            function load_level() {
                levelCount++;


                let levelIndex = levelCount;

                let newLevel = `
        <div class="col-lg-12 mb-4 level-card" data-level="${levelIndex}">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" style="width: 20%;"> Level ${levelIndex < 10 ? '0' + levelIndex : levelIndex}</h5>
                    <div class="col-md-6" style="width: 80%;">
                        <input type="text" class="form-control" id="description_${levelIndex}" placeholder="Enter Level Description">
                    </div>
                </div>
                <div class="card-body" style="background-color: #f6ffee;">
                    <div class="row">
                        <!-- Designation -->
                        <div class="col-lg-6">
                            <div class="table-responsive custom-scrollbar">
                                <div class="row mb-3">
                                    <div class="col-md-12 d-flex align-items-center">
                                        <label for="desi_${levelIndex}" class="form-label me-2">Designation</label>
                                        <select class="form-control" id="desi_${levelIndex}" name="desi">
                                            @foreach($designation as $item)
                <option value="{{ $item->idDesignation }}">{{ $item->name }}</option>
                                            @endforeach
                </select>
                <button type="button" class="btn btn-primary ms-2 add-to-table" data-table="table_${levelIndex}">Add</button>
                                    </div>
                                </div>
                                <table class="table table-bordered table-sm designation-table" id="table_${levelIndex}">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Designation</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Checklist -->
                        <div class="col-lg-6">
                            <div class="table-responsive custom-scrollbar">
                                <div class="row mb-3">
                                    <div class="col-md-12 d-flex align-items-center">
                                        <label for="checklist_input_${levelIndex}" class="form-label me-2">Checklist Item</label>
                                        <input type="text" class="form-control" id="checklist_input_${levelIndex}" placeholder="Enter Checklist Item">
                                        <button type="button" class="btn btn-primary ms-2 add-to-checklist" data-table="checklist_table_${levelIndex}">Add</button>
                                    </div>
                                </div>
                                <table class="table table-bordered table-sm checklist-table" id="checklist_table_${levelIndex}">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Checklist Item</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

                $('#levels-container').append(newLevel);

                return levelIndex;
            }

            levelsData.forEach((level, index) => {
                let levelIndex = load_level(); // 👈 Create and get level index immediately
                setTimeout(() => {
                    const currentCard = $(`.level-card[data-level="${levelIndex}"]`);

                    currentCard.find(`#description_${levelIndex}`).val(level.description);

                    // Add designations
                    level.designations.forEach(d => {
                        const row = `
                <tr>
                    <td style="text-align: left" data-value="${d.designation_id}">${d.designation_id}</td>
                    <td style="text-align: left">
                        <button type="button" class="btn btn-danger btn-sm remove-from-table">Remove</button>
                    </td>
                </tr>`;
                        currentCard.find('.designation-table tbody').append(row);
                    });

                    // Add checklist items
                    level.checklist.forEach(item => {
                        const row = `
                <tr>
                    <td>${item.description}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                    </td>
                </tr>`;
                        currentCard.find('.checklist-table tbody').append(row);
                    });
                }, 10);
            });






            function toggleSavingFields() {
                var enableSaving = $('#enable_saving').val();
                if (enableSaving === 'No') {
                    $('#saving_account_amount_type').closest('.col-md-6').hide();
                    $('#saving_amount').closest('.col-md-6').hide();
                    $('#saving_payment').closest('.col-md-6').hide();
                } else {
                    $('#saving_account_amount_type').closest('.col-md-6').show();
                    $('#saving_amount').closest('.col-md-6').show();
                    $('#saving_payment').closest('.col-md-6').show();
                }
            }

            function updateAmountLabel() {
                var amountType = $('#saving_account_amount_type').val();
                var amountLabel = $('label[for="loan_amount"]');
                if (amountType === 'percentage') {
                    amountLabel.text('Percentage');
                    $('#saving_amount').attr('placeholder', 'Enter percentage').attr('max', 100);
                } else {
                    amountLabel.text('Amount');
                    $('#saving_amount').attr('placeholder', 'Enter amount').removeAttr('max');
                }
            }

            // Initial checks when the page loads
            toggleSavingFields();
            updateAmountLabel();

            // Listen for changes on the enable_saving select box
            $('#enable_saving').change(function() {
                toggleSavingFields();
            });

            // Listen for changes on the saving_account_amount_type select box
            $('#saving_account_amount_type').change(function() {
                updateAmountLabel();
            });

            // Validate the percentage field
            $('#saving_amount').on('input', function() {
                var amountType = $('#saving_account_amount_type').val();
                if (amountType === 'percentage') {
                    var value = $(this).val();
                    if (value > 100) {
                        $(this).val(100);
                    } else if (value < 0) {
                        $(this).val(0);
                    }
                }
            });



            $('#addLevelBtn1').click(function() {
                load_level();
            });

// Event handler for adding rows to the checklist table
            $(document).on('click', '.add-to-checklist', function() {
                let tableId = $(this).data('table');
                let levelId = tableId.split('_')[2]; // Extract level ID from table ID
                let inputId = `#checklist_input_${levelId}`;
                let value = $(inputId).val();

                if (value.trim() !== '') {
                    let newRow = `
        <tr>
            <td>${value}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
            </td>
        </tr>`;
                    $(`#${tableId} tbody`).append(newRow);
                    $(inputId).val(''); // Clear input field
                }
            });

            // Event handler for removing rows from tables
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
            });

            $('#removeLevelBtn1').click(function() {
                if (levelCount > 0) {
                    $('.level-card').last().remove();
                    levelCount--;
                }
            });

            // Use event delegation to handle click event for dynamically added elements
            $('#levels-container').on('click', '.add-to-table', function() {
                let cardBody = $(this).closest('.card-body');
                let select = cardBody.find('select');
                let selectedText = select.find('option:selected').text();
                let selectedValue = select.find('option:selected').val();
                let tableBody = cardBody.find('.designation-table tbody');

                // Check for duplicate entry
                let isDuplicate = false;
                tableBody.find('tr').each(function() {
                    let rowValue = $(this).find('td:first').data('value');
                    if (rowValue == selectedValue) {
                        isDuplicate = true;
                        return false; // Break the loop
                    }
                });

                if (!isDuplicate) {
                    tableBody.append(`
                <tr>
                    <td style="text-align: left" data-value="${selectedValue}">${selectedText}</td>
                    <td style="text-align: left">
                        <button type="button" class="btn btn-danger btn-sm remove-from-table">Remove</button>
                    </td>
                </tr>
            `);
                } else {
                    alert('This designation is already added.');
                }
            });

            // Use event delegation to handle click event for remove buttons
            $('#levels-container').on('click', '.remove-from-table', function() {
                $(this).closest('tr').remove();
            });
        });


        // Function to read levels data
        function readLevelsData() {
            let levelsData = [];

            $('.level-card').each(function() {
                // Retrieve the level number from the <h5> element
                let levelHeaderText = $(this).find('.card-header h5').text().trim();
                let level = levelHeaderText.replace('Level ', '').trim();

                let description = $(this).find(`input[id^="description_"]`).val(); // Using starts-with selector for robustness
                let designations = [];
                let checklist = [];

                // Gather designations
                $(this).find('.designation-table tbody tr').each(function() {
                    let designationId = $(this).find('td:first').data('value');
                    let designationName = $(this).find('td:first').text();
                    designations.push({ id: designationId, name: designationName });
                });

                // Gather checklist items
                $(this).find('.checklist-table tbody tr').each(function() {
                    let checklistItem = $(this).find('td:first').text().trim();
                    checklist.push(checklistItem);
                });

                // Add level data to the array
                levelsData.push({
                    level: level,
                    description: description,
                    designations: designations,
                    checklist: checklist
                });
            });

            return levelsData;
        }

        var deleteButtons = document.querySelectorAll('.delete-row');
        deleteButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var row = this.closest('tr');
                row.remove();
            });
        });
        // For both tables (delegated event handler)
        $(document).on('click', '#documenttable .delete-row, #otherchargetable .delete-row', function () {
            $(this).closest('tr').remove();
        });


        const updateLoanCategory = (e) => {
            e.preventDefault();

            const product_id = $("#product_id").val(); // Get the ID
            const loan_amount_from = $("#loan_amount_from").val().trim();
            const loan_amount_to = $("#loan_amount_to").val().trim();
            const interest_from = $("#interest_from").val().trim();
            const interest_to = $("#interest_to").val().trim();
            const product_name = $("#product_name").val();
            const product_code = $("#product_code").val();
            const interest_method = $("#interest_method").val();
            const interest_period = $("#interest_period").val();
            const loan_duration = $("#loan_duration").val();
            const collection_type = $("#collection_type").val();
            const penalty_period = $("#penalty_period").val();
            const panelty_rate = $("#panelty_rate").val();
            const panelty_rate_date = $("#panelty_rate_date").val();
            const witnessCount = $("#witnessCount").val();
            const duration_period = $("#duration_period").val();
            const period_count = $("#period_count").val();
            const default_loan_duration_period = $("#default_loan_duration_period").val();
            const enable_saving = $("#enable_saving").val();
            const saving_account_amount_type = $("#saving_account_amount_type").val();
            let saving_amount = $("#saving_amount").val();
            let saving_payment = $("#saving_payment").val();

            if (enable_saving === "No") {
                saving_amount = 0.00;
            }

            let error_count = 0;
            if (enable_saving === "Yes" && saving_amount === "") {
                error_count = 1;
            }

            // Validations
            if (!loan_amount_from || !loan_amount_to || isNaN(loan_amount_from) || isNaN(loan_amount_to)) {
                return Swal.fire("Error!", "Please enter valid Loan Amount range!", "error");
            }

            if (parseFloat(loan_amount_from) > parseFloat(loan_amount_to)) {
                return Swal.fire("Error!", "Minimum Loan Amount must be <= Maximum!", "error");
            }

            if (!interest_from || !interest_to || isNaN(interest_from) || isNaN(interest_to)) {
                return Swal.fire("Error!", "Please enter valid Interest range!", "error");
            }

            if (parseFloat(interest_from) > parseFloat(interest_to)) {
                return Swal.fire("Error!", "Minimum Interest must be <= Maximum!", "error");
            }

            if (error_count === 1) {
                return Swal.fire("Error!", "Please enter saving amount!", "error");
            }

            let level_data = readLevelsData();
            console.log(level_data);

            if (level_data.length === 0) {
                return Swal.fire("Error!", "Please add at least one level!", "error");
            }

            let designationError = level_data.some(l => l.designations.length === 0);
            if (designationError) {
                return Swal.fire("Error!", "Please add at least one designation to all levels!", "error");
            }

            // Other Charges
            let othercharges = [];
            $('#otherchargetable tbody tr').each(function () {
                const cols = $(this).find('td');
                othercharges.push([
                    $(cols[0]).text(),
                    $(cols[1]).text(),
                    $(cols[2]).text(),
                ]);
            });

            // Required Documents
            let document = [];
            $('#documenttable tbody tr').each(function () {
                document.push([$(this).find('td:first').text()]);
            });

            // === Ajax Update ===
            Swal.fire({
                title: "Are you sure?",
                html: `
        <div style="text-align:left;">
            <p>You are about to update this <b>Product</b>.</p>
            <p style="margin-top:6px;">
                <b>Important:</b> If you proceed, <b>all pending loans</b> under this product
                will need to be <b>approved again from the beginning</b>.
            </p>
            <p style="color:#b71c1c;margin-top:6px;"><b>This action cannot be undone.</b></p>
        </div>
    `,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, update it!",
                cancelButtonText: "Cancel",
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "PUT", // PUT for update
                        url: `/loan-products/${product_id}`, // make sure this route exists!
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: {
                            product_name,
                            product_code,
                            loan_amount_from,
                            loan_amount_to,
                            interest_method,
                            interest_period,
                            interest_from,
                            interest_to,
                            duration_period,
                            loan_duration,
                            collection_type,
                            penalty_period,
                            panelty_rate,
                            panelty_rate_date,
                            witnessCount,
                            period_count,
                            default_loan_duration_period,
                            level_data,
                            othercharges,
                            document,
                            enable_saving,
                            saving_account_amount_type,
                            saving_amount,
                            saving_payment,
                        },
                        success: function (res, status, xhr) {
                            if (xhr.status === 200) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Updated!",
                                    text: "Product updated successfully.",
                                    timer: 1500,
                                    showConfirmButton: false,
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire("Error!", "Update failed!", "error");
                            }
                        },
                        error: function () {
                            Swal.fire("Error!", "Something went wrong on the server!", "error");
                        }
                    });
                }
            });

        };

    </script>
    <script>
        function validateAmount() {
            var selectedType = document.getElementById("charge_type").value;
            var amountInput = document.getElementById("otherChargesAmount").value;
            var amountError = document.getElementById("amountError");

            if (selectedType === "Percentage" && parseInt(amountInput) > 99) {
                amountError.textContent = "Percentage amount cannot be greater than 99%";
                document.getElementById("otherChargesAmount").classList.add("is-invalid");
                $("#otherChargesAmount").val("99")
            } else {
                amountError.textContent = "";
                document.getElementById("otherChargesAmount").classList.remove("is-invalid");
            }
        }

        // Function to change label based on dropdown selection
        function change_name_amount(value) {
            var change_amount_label = document.getElementById("change_amount");
            if (value === "Percentage") {
                change_amount_label.textContent = "Percentage";
            } else {
                change_amount_label.textContent = "Amount";
            }
        }

        function change_name_amount(value) {
            if (value === "Amount") {
                $("#change_amount").text("Amount");
            } else {
                $("#change_amount").text("Percentage %");

            }
        }

        document.getElementById('addChargesBtn').addEventListener('click', function () {
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

                // Create a new table row
                var newRow = '<tr>' +
                    '<td>' + description + '</td>' +
                    '<td>' + charge_type + '</td>' +
                    '<td>' + amount + '</td>' +
                    '<td><button type="button" class="btn btn-success delete-row" style="background-color: white; color: #ff0000; border: none"><i class="bi bi-trash fs-3"></i></button></td>' +
                    '</tr>';

                // Append the new row to the table body
                document.getElementById('otherchargetable').getElementsByTagName('tbody')[0].insertAdjacentHTML(
                    'beforeend', newRow);

                // Clear the input fields
                document.getElementById('otherChargesDescription').value = '';
                document.getElementById('otherChargesAmount').value = '';
            }


            // Add event listener to delete button of the new row
            var deleteButtons = document.querySelectorAll('.delete-row');
            deleteButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var row = this.closest('tr');
                    row.remove();
                });
            });
        });


        document.getElementById('addDocBtn').addEventListener('click', function () {
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
                document.getElementById('documenttable').getElementsByTagName('tbody')[0].insertAdjacentHTML(
                    'beforeend', newRow);

                // Clear the input fields
                document.getElementById('otherDocDescription').value = '';
            }


            // Add event listener to delete button of the new row
            var deleteButtons = document.querySelectorAll('.delete-row');
            deleteButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var row = this.closest('tr');
                    row.remove();
                });
            });
        });
    </script>
@endsection
