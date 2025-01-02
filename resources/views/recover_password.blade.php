<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Recover Password | Asipiya Finance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully responsive admin theme which can be used to build CRM, CMS,ERP etc." name="description" />
    <meta content="Techzaa" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Theme Config Js -->
    <script src="assets/js/config.js"></script>

    <!-- App css -->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>

<body class="authentication-bg position-relative">
<div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-8 col-lg-10">
                <div class="card overflow-hidden">
                    <div class="row g-0">
                        <div class="col-lg-6 d-none d-lg-block p-2">
                            <img src="assets/images/login_image.jpg" alt="" class="img-fluid rounded h-100">
                        </div>
                        <div class="col-lg-6">
                            <div class="d-flex flex-column h-100">

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
                                <div class="p-4 my-auto">
                                    <h4 class="fs-20">Recover Password</h4>
                                    <p class="text-muted mb-3">Enter your new password
                                    </p>


                                    <form action="{{ route('user.recover_password') }}" method="post">
                                        {{ csrf_field() }}
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email address</label>
                                            <input class="form-control" type="email" id="email" value="{{ $email }}" name="email" placeholder="Enter your email" required readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="otp" class="form-label">OTP Number</label>
                                            <input class="form-control" type="text" id="otp" name="otp" placeholder="Enter your OTP" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input class="form-control" type="password" id="password" name="password" placeholder="Enter your password" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                                            <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required>
                                        </div>
                                        <div class="mb-0 text-start">
                                            <button class="btn btn-soft-primary w-100" type="submit"><i class="ri-login-circle-fill me-1"></i> <span class="fw-bold">Next</span> </button>
                                        </div>

                                        <div class="mb-3 text-end">
                                            <a href="/login" id="forgot-password-link">Login</a>
                                        </div>
                                    </form>
                                    <!-- end form-->
                                </div>
                            </div>
                        </div> <!-- end col -->
                    </div>
                </div>
            </div>
            <!-- end row -->
        </div>

    </div>
    <!-- end container -->
</div>
<!-- end page -->

<footer class="footer footer-alt fw-medium">
        <span class="text-dark">
            <script>document.write(new Date().getFullYear())</script> © <a href="https://www.asipiya.lk/" target="_blank">Asipiya Soft Solutions</a><b></b>
        </span>
</footer>
<!-- Vendor js -->
<script src="assets/js/vendor.min.js"></script>

<!-- App js -->
<script src="assets/js/app.min.js"></script>

</body>


<!-- Mirrored from techzaa.getappui.com/velonic/layouts/auth-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 16 Apr 2024 05:22:27 GMT -->
</html>
