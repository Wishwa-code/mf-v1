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
                    if (items.payment_member_name) {
                        $('#payment_member_name').val(items.payment_member_name);
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

    </script>
@endsection
