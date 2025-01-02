@extends('layout.admin')

@section('head')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.4.1/font/bootstrap-icons.min.css">
    <style>
        .style-tr > td {
            padding: 2px 15px;
        }

        .strength-meter {
            display: flex;
            gap: 2px;
        }

        .strength-meter div {
            height: 5px;
            flex: 1;
            background-color: lightgray;
        }

        .strength-meter .weak {
            background-color: red;
        }

        .strength-meter .medium {
            background-color: #dede0c;
        }

        .strength-meter .strong {
            background-color: green;
        }

        .show-password {
            cursor: pointer;
            position: absolute;
            right: 20px;
            top: 35px;
        }

        .password-wrapper {
            position: relative;
        }
    </style>
@endsection

@section('content')
    <div>
        <!-- start page title -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="row">
                    <div class="col-12">

                        <div class="card card-shadow-new border-primary">
                            @if ($errors->any())
                                <div class="mt-5">
                                    <div class="col-12">
                                        @foreach ($errors->all() as $error)
                                            <div class="alert alert-danger">{{ $error }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if (session()->has('error'))
                                <div class="mt-5">
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                </div>
                            @endif
                            @if (session()->has('success'))
                                <div class="mt-5">
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                </div>
                            @endif
                            <div class="card-header text-black card-title-n">
                                <h4 class="page-title">User Details</h4>
                            </div>

                                <div class="card-body">
                                    <form action="{{ route('user.signup') }}" method="post">
                                        {{ csrf_field() }}

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="epf_no" class="form-label">EPF Number</label>
                                                <input type="text" class="form-control" id="epf_no" name="epf_no" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="desi" class="form-label">Designation</label>
                                                <select class="form-control" id="desi" name="desi">
                                                    @foreach($designation as $item)
                                                        <option value="{{$item->name}}">{{$item->name}}</option>
                                                    @endforeach
                                                </select>
{{--                                                <input type="text" class="form-control" id="desi" name="desi" required>--}}
                                                <br>
                                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#standard-modal" >Create Designation</button>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="nic" class="form-label">NIC</label>
                                                <input type="text" class="form-control" id="nic" name="nic" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="full_name" class="form-label">Full Name</label>
                                                <input type="text" class="form-control" id="full_name" name="full_name" required>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" required>
                                            </div>
                                            <div class="col-md-6 password-wrapper">
                                                <label for="password" class="form-label">Password</label>
                                                <input type="password" class="form-control" id="password" name="password" required onkeyup="checkPasswordStrength()">
                                                <span class="show-password" onclick="togglePasswordVisibility()">
                    <i class="bi bi-eye-fill"></i>
                </span>
                                                <br>
                                                <div class="strength-meter" id="strength-meter">
                                                    <div></div>
                                                    <div></div>
                                                    <div></div>
                                                    <div></div>
                                                    <div></div>
                                                </div>
                                                <br>
                                                <button type="button" class="btn btn-danger" onclick="generatePassword()">Generate Strong Password</button>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="tp" class="form-label">Contact Number</label>
                                                <input type="text" class="form-control" id="tp" name="tp" onkeypress="validateContactNumber(event)">
                                            </div>

                                            <div class="col-md-3">
                                                <br><br>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="1" id="lending_officer" name="lending_officer">
                                                    <label class="form-check-label" for="lending_officer">
                                                        Lending Officer
                                                    </label>
                                                </div>

                                            </div>
                                            <div class="col-md-3">
                                                <br><br>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="1" id="collecting_officer" name="collecting_officer">
                                                    <label class="form-check-label" for="collecting_officer">
                                                        Collecting Officer
                                                    </label>
                                                </div>

                                            </div>

                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">

                                                @if (session('branch_access')==1)
                                                    <label for="tp" class="form-label">Branch</label>
                                                    <select class="form-control" id="branch" name="branch">
                                                        @foreach($branch as $item)
                                                            <option value="{{$item->branch_id}}">{{$item->Name}}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <label for="tp" class="form-label" hidden>Branch</label>
                                                    <select class="form-control" id="branch" name="branch" hidden>
                                                        @foreach($branch as $item)
                                                            <option value="{{$item->branch_id}}">{{$item->Name}}</option>
                                                        @endforeach
                                                    </select>
                                                @endif

                                            </div>
                                            <div class="col-md-3">
                                                <br><br>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="1" id="branch_access" name="branch_access">
                                                    <label class="form-check-label" for="branch_access">
                                                        Branch Access
                                                    </label>
                                                </div>

                                            </div>
                                        </div>


                                        <div class="form-row end-align">
                                            <button type="submit" class="btn btn-primary">Save User</button>
                                        </div>
                                    </form>
                                </div>

                        </div>

                        <div class="card card-shadow-new border-primary">
                            <div class="card-header new-back text-white card-title-n">
                                View User Details
                            </div>
                            <div class="card-body less-padding">
                                <div class="table-responsive">
                                    <table id="example" class="table table-bordered dash-table dash-table-d table-hover">
                                        <thead>
                                        <tr>
                                            <th>EPF Number</th>
                                            <th>Designation</th>
                                            <th>Full Name</th>
                                            <th>Nic</th>
                                            <th>Email</th>
                                            <th>Contact Number</th>
                                            <th>Lending Officer</th>
                                            <th>Status</th>
                                            <th>Change Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($userData as $item)
                                            <tr>
                                                <td>{{ $item->Epf_no }}</td>
                                                <td>{{ $item->Designation }}</td>
                                                <td>{{ $item->Full_Name }}</td>
                                                <td>{{ $item->Nic }}</td>
                                                <td>{{ $item->email }}</td>
                                                <td>{{ $item->TP }}</td>
                                                <td>
                                                    @if($item->lending_officer == 1)
                                                        Yes
                                                    @else
                                                        No
                                                    @endif
                                                </td>

                                            @if ($item->Status === '1')
                                                    <td><span style="color: green">Admin</span></td>
                                                    <td><input type="button" value="Inactive" onclick="updateStatusUser({{ $item->id }})" class="btn btn-danger"></td>
                                                @elseif($item->Status === '2')
                                                    <td><span style="color: black">Cleaner</span></td>
                                                    <td><input type="button" value="Change Status" class="btn btn-danger" disabled></td>
                                                @else
                                                    <td><span style="color: red">Inactive</span></td>
                                                    <td><input type="button" value="Active" onclick="updateStatusUser({{ $item->id }})" class="btn btn-success"></td>
                                                @endif
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>




                        <div class="modal fade" id="standard-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4>Create Designation</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="container">
                                            <div class="row mb-3">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="designation1" class="form-label">Designation<span class="required-asterisk">*</span></label>
                                                            <input type="text" id="designation" class="form-control">
                                                        </div>
                                                        <div class="col-md-6 mb-3" hidden>
                                                            <label for="designation1" class="form-label">Designation Level<span class="required-asterisk">*</span></label>
                                                            <select class="form-control" id="desi_level">
                                                                <option>1</option>
                                                                <option>2</option>
                                                                <option>3</option>
                                                                <option>4</option>
                                                                <option>5</option>
                                                                <option>6</option>
                                                                <option>7</option>
                                                                <option>8</option>
                                                                <option>9</option>
                                                                <option>10</option>
                                                            </select>
                                                        </div>

                                                    </div>
{{--                                                    <hr>--}}
                                                    <br>
                                                    <div class="row" hidden>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="loan_create" class="form-label">Loan Create (Issue Loan)<span class="required-asterisk">*</span></label>
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input" id="loan_create">
                                                                <label class="form-check-label" for="loan_create">Allow</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="loan_approve" class="form-label">Loan Approve<span class="required-asterisk">*</span></label>
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input" id="loan_approve">
                                                                <label class="form-check-label" for="loan_approve">Allow</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">

                                                        <div class="col-md-6 mb-3">
                                                            <label for="max_amount_create" class="form-label">Maximum Amount for Create Loan<span class="required-asterisk">*</span></label>
                                                            <input type="text" id="max_amount_create" class="form-control">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="max_amount_approve" class="form-label">Maximum Amount for Approve Loan<span class="required-asterisk">*</span></label>
                                                            <input type="text" id="max_amount_approve" class="form-control">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-success" style="float: right" onclick="saveUserDesignation()">Update Designation</button>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="card card-shadow-new border-primary">

                                        <div class="card-body less-padding">
                                            <div class="table-responsive">
                                                <table id="example" class="table table-bordered dash-table dash-table-d table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center">Designation</th>
                                                        <th class="text-center" hidden>Level</th>
                                                        <th class="text-center" hidden>Create</th>
                                                        <th class="text-center" hidden>Approve</th>
                                                        <th class="text-center">Max for Create Loan</th>
                                                        <th class="text-center">Max for Approve Loan</th>
                                                        <th class="text-center">Change Status</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach ($designation as $item)
                                                        <tr>
                                                            <td class="align-middle text-center"><input type="text" class="form-control"  data-item-id="{{ $item->idDesignation }}" id="" value="{{ $item->name }}"></td>
                                                            <td class="align-middle text-center" hidden>
                                                                <select class="form-control desi-level" data-item-id="{{ $item->idDesignation }}">
                                                                    @for ($i = 1; $i <= 10; $i++)
                                                                        <option value="{{ $i }}" @if ($i == $item->desi_level) selected @endif>{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </td>
                                                            <td class="align-middle text-center" hidden>
                                                                <div class="form-check">
                                                                    <input type="checkbox" class="form-check-input loan-create" id="loan_create_{{ $item->idDesignation }}" @if ($item->loan_creat == 1) checked @endif>
                                                                    <label class="form-check-label" for="loan_create_{{ $item->idDesignation }}"></label>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle text-center" hidden>
                                                                <div class="form-check">
                                                                    <input type="checkbox" class="form-check-input loan-approve" id="loan_approve_{{ $item->idDesignation }}" @if ($item->loan_issue == 1) checked @endif>
                                                                    <label class="form-check-label" for="loan_approve_{{ $item->idDesignation }}"></label>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <input type="text" class="form-control max-create-amount"  data-item-id="{{ $item->idDesignation }}" value="{{ number_format($item->max_create_amount, 2, '.', '') }}">
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <input type="text" class="form-control max-issue-amount"  data-item-id="{{ $item->idDesignation }}" value="{{ number_format($item->max_issue_amount, 2, '.', '') }}">
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <button type="button" class="btn btn-warning btn-update" data-item-id="{{ $item->idDesignation }}">Update</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>



                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->



                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="../JS/validate.js"></script>
    <script src="../JS/user.js"></script>
    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthMeter = document.getElementById('strength-meter');
            const strength = validatePasswordStrength(password);

            const meterChildren = strengthMeter.children;
            for (let i = 0; i < meterChildren.length; i++) {
                meterChildren[i].className = '';
                if (i < strength) {
                    meterChildren[i].classList.add(i < 2 ? 'weak' : i < 4 ? 'medium' : 'strong');
                }
            }
        }

        function generatePassword() {
            const length = 12;
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+~`|}{[]:;?><,./-=";
            let password = "";
            for (let i = 0, n = charset.length; i < length; ++i) {
                password += charset.charAt(Math.floor(Math.random() * n));
            }
            document.getElementById('password').value = password;
            checkPasswordStrength();
        }

        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const showPasswordIcon = document.querySelector('.show-password i');
            if (passwordField.type === "password") {
                passwordField.type = "text";
                showPasswordIcon.classList.remove('bi-eye-fill');
                showPasswordIcon.classList.add('bi-eye-slash-fill');
            } else {
                passwordField.type = "password";
                showPasswordIcon.classList.remove('bi-eye-slash-fill');
                showPasswordIcon.classList.add('bi-eye-fill');
            }
        }

        $(document).ready(function() {

            let x = ["#max_amount_create","#max_amount_create"];
            decimalFormat(x);

            $('.btn-update').click(function() {
                var itemId = $(this).data('item-id');
                var designation = $(`#example input[data-item-id="${itemId}"]`).val(); // Retrieve name value
                var desiLevel = $(`#example select[data-item-id="${itemId}"]`).val(); // Retrieve desiLevel value
                var loanCreate = $(`#loan_create_${itemId}`).prop('checked') ? 1 : 0; // Retrieve loanCreate value
                var loanApprove = $(`#loan_approve_${itemId}`).prop('checked') ? 1 : 0; // Retrieve loanApprove value
                var maxCreateAmount = $(`#example .max-create-amount[data-item-id="${itemId}"]`).val(); // Retrieve maxCreateAmount value
                var maxIssueAmount = $(`#example .max-issue-amount[data-item-id="${itemId}"]`).val(); // Retrieve maxIssueAmount value



                if(itemId==="" || designation==="" || desiLevel==="" ||  maxCreateAmount==="" || maxIssueAmount===""){
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please fill all required fields !'
                    })
                }else{
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to save this designation ?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Update it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Example of sending data via AJAX to a route or function
                            $.ajax({
                                method: 'POST',
                                url: '/update-designation', // Replace with your route
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                                },
                                data: {
                                    id: itemId,
                                    designation: designation,
                                    desiLevel: desiLevel,
                                    loanCreate: loanCreate,
                                    loanApprove: loanApprove,
                                    maxCreateAmount: maxCreateAmount,
                                    maxIssueAmount: maxIssueAmount
                                },
                                success: function(response) {
                                    Swal.fire({
                                        position: "center",
                                        icon: "success",
                                        title: "Successfully updated !",
                                    }).then(function () {
                                        window.location.reload();
                                    });
                                },
                                error: function(xhr, status, error) {
                                    // Handle error
                                    console.error('Error updating data:', error);
                                    // Optionally, show an error message
                                }
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
