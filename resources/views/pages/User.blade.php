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

                                            <div class="col-md-2">
                                                <br><br>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="1" id="lending_officer" name="lending_officer">
                                                    <label class="form-check-label" for="lending_officer">
                                                        Lending Officer
                                                    </label>
                                                </div>

                                            </div>
                                            <div class="col-md-2">
                                                <br><br>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="1" id="collecting_officer" name="collecting_officer">
                                                    <label class="form-check-label" for="collecting_officer">
                                                        Collecting Officer
                                                    </label>
                                                </div>

                                            </div>
                                            <div class="col-md-2">
                                                <br><br>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="1" id="cashier" name="cashier">
                                                    <label class="form-check-label" for="cashier">
                                                        Cashier
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
                                            <th>Edit</th>
                                            <th>Reset Password</th>
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

                                                <!-- Edit Button -->
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-edit" data-id="{{ $item->id }}" data-toggle="modal" data-target="#editUserModal">Edit</button>
                                                </td>

                                                <!-- Reset Password Button -->
                                                <td>
                                                    <button type="button" class="btn btn-warning btn-reset-password" data-id="{{ $item->id }}" data-toggle="modal" data-target="#resetPasswordModal">Reset Password</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Modal for Editing User Details -->
                        <!-- Modal for Editing User Details -->
                        <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editUserModalLabel">Edit User Details</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="editUserForm">
                                            <input type="hidden" id="userId">

                                            <!-- EPF Number -->
                                            <div class="form-group">
                                                <label for="editEpfNo">EPF Number</label>
                                                <input type="text" class="form-control" id="editEpfNo" name="epf_no" required>
                                            </div>

                                            <!-- Designation -->
                                            <div class="form-group">
                                                <label for="editDesignation">Designation</label>
                                                <select class="form-control" id="editDesignation" name="desi">
                                                    @foreach($designation as $item)
                                                        <option value="{{ $item->name }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- NIC -->
                                            <div class="form-group">
                                                <label for="editNic">NIC</label>
                                                <input type="text" class="form-control" id="editNic" name="nic" required>
                                            </div>

                                            <!-- Full Name -->
                                            <div class="form-group">
                                                <label for="editFullName">Full Name</label>
                                                <input type="text" class="form-control" id="editFullName" name="full_name" required>
                                            </div>

                                            <!-- Email -->
                                            <div class="form-group">
                                                <label for="editEmail">Email</label>
                                                <input type="email" class="form-control" id="editEmail" name="email" required readonly>
                                            </div>

                                            <!-- Contact Number -->
                                            <div class="form-group">
                                                <label for="editTP">Contact Number</label>
                                                <input type="text" class="form-control" id="editTP" name="tp" required>
                                            </div>

                                            <!-- Lending Officer -->
                                            <div class="form-group form-check">
                                                <input type="checkbox" class="form-check-input" id="editLendingOfficer" name="editLendingOfficer">
                                                <label class="form-check-label" for="editLendingOfficer">Lending Officer</label>
                                            </div>

                                            <!-- Collecting Officer -->
                                            <div class="form-group form-check">
                                                <input type="checkbox" class="form-check-input" id="editCollectingOfficer" name="editCollectingOfficer">
                                                <label class="form-check-label" for="editCollectingOfficer">Collecting Officer</label>
                                            </div>

                                            <div class="form-group form-check">
                                                <input type="checkbox" class="form-check-input" id="editcashier" name="editcashier">
                                                <label class="form-check-label" for="editcashier">Cashier</label>
                                            </div>

                                            <!-- Branch -->
                                            <div class="form-group" hidden>
                                                <label for="editBranch">Branch</label>
                                                <select class="form-control" id="editBranch" name="branch">
                                                    @foreach($branch as $item)
                                                        <option value="{{ $item->branch_id }}">{{ $item->Name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Branch Access -->
                                            <div class="form-group form-check">
                                                <input type="checkbox" class="form-check-input" id="editBranchAccess" name="branch_access">
                                                <label class="form-check-label" for="editBranchAccess">Branch Access</label>
                                            </div>

                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" id="updateUserBtn">Save Changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Modal for Resetting Password -->
                        <div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="resetPasswordModalLabel">Reset User Password</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to reset the password for this user?</p>
                                        <div class="form-group">
                                            <label for="newPassword">New Password</label>
                                            <input type="password" class="form-control" id="newPassword" placeholder="Enter new password">
                                            <small id="passwordHelp" class="form-text text-muted">Enter a new password for the user.</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-warning" id="resetPasswordBtn">Reset Password</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="../JS/validate.js"></script>
    <script src="../JS/user.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS (make sure to include both JS and CSS for modals) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

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

            $('.btn-edit').on('click', function () {
                const userId = $(this).data('id');

                // Fetch User Data via AJAX
                $.ajax({
                    url: '/user/get-details/' + userId,
                    method: 'GET',
                    success: function (response) {
                        // Populate the modal fields with user data
                        $('#userId').val(response.id);
                        $('#editEpfNo').val(response.Epf_no);
                        $('#editDesignation').val(response.Designation);
                        $('#editNic').val(response.Nic);
                        $('#editFullName').val(response.Full_Name);
                        $('#editEmail').val(response.email);
                        $('#editTP').val(response.TP);
                        $('#editLendingOfficer').prop('checked', response.lending_officer == 1);
                        $('#editCollectingOfficer').prop('checked', response.collector == 1);
                        $('#editBranch').val(response.branch_id);
                        $('#editBranchAccess').prop('checked', response.branch_access == 1);
                        $('#editcashier').prop('checked', response.cashier == 1);
                    },
                    error: function (err) {
                        console.error('Error fetching user data:', err);
                    }
                });
            });

// Handle Edit User Form Submission
            $('#updateUserBtn').on('click', function () {
                // Gather form data
                const formData = $('#editUserForm').serialize();

                console.log('Form Data:', formData);  // For debugging

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to update this user's details ?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, reset it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/user/update',
                            method: 'POST',
                            data: formData,
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        position: "center",
                                        icon: "success",
                                        title: "Successfully updated !",
                                    }).then(function () {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire('Error', 'Failed to update user details', 'error');
                                }
                            },
                            error: function (err) {
                                console.error('Error in updating user:', err);
                                Swal.fire('Error', 'Something went wrong!', 'error');
                            }
                        });
                    }
                });
            });


            // Reset Password Modal Trigger
            $('.btn-reset-password').on('click', function() {
                const userId = $(this).data('id');
                $('#userId').val(userId);  // Set the userId in the hidden field
            });

            // Handle Password Reset
            $('#resetPasswordBtn').on('click', function() {
                const userId = $('#userId').val();
                const newPassword = $('#newPassword').val();  // Get the new password from the modal input

                // Check if the new password is not empty
                if (!newPassword) {
                    Swal.fire('Error', 'Please enter a new password!', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to reset this user's password?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, reset it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/user/reset-password/' + userId,
                            method: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                newPassword: newPassword // Send the new password in the request
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        position: "center",
                                        icon: "success",
                                        title: "Password reset successfully !",
                                    }).then(function () {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire('Error', 'Failed to reset password', 'error');
                                }
                            }
                        });
                    }
                });
            });
        });

    </script>
@endsection
