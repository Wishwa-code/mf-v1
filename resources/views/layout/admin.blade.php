@if (!session()->has('username'))
    <script>
        window.location.href = "{{ route('login') }}"
    </script>
@endif
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ session('company_name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        .required-asterisk {
            color: red;
        }
        @media print {
            body {
                font-family: Arial, sans-serif;
                font-size: 11px;
                color: #000;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            table th,
            table td {
                border: 2px solid #000 !important;
                padding: 6px !important;
                text-align: center;
                vertical-align: middle;
            }

            thead {
                background-color: #f0f0f0 !important;
            }

            tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            /* Hide unnecessary UI elements */
            .btn,
            .no-print,
            .dataTables_length,
            .dataTables_filter,
            .dataTables_info,
            .dataTables_paginate,
            .dt-buttons {
                display: none !important;
            }
        }

    </style>

    @yield('head')
</head>
<body>
<div class="wrapper">
    @include('component.header')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>
    @include('component.footer')
    <!-- Global Camera Modal -->
    <div id="globalCameraModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📷 Camera</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="closeGlobalCamera()"></button>
                </div>
                <div class="modal-body text-center">
                    <select id="cameraFacing" class="form-select mb-2" style="width: auto; display:inline-block;">
                        <option value="user">📸 Selfie Camera</option>
                        <option value="environment">📷 Back Camera</option>
                    </select>

                    <video id="globalVideo" autoplay style="width:100%; max-height:300px; border:1px solid #ccc;"></video>
                    <canvas id="globalCanvas" style="display:none;"></canvas>
                    <br>
                    <button class="btn btn-success mt-2" onclick="captureGlobalImage()">✅</button>
                </div>

            </div>
        </div>
    </div>

</div>
<script src="{{asset('assets/js/vendor.min.js')}}"></script>
<script src="{{asset('assets/js/app.min.js')}}"></script>
<script src="{{asset('../JS/validate.js?n=2')}}"></script>
<script src="{{asset('assets/vendor/select2/js/select2.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Make all settings globally available
    window.APP_SETTINGS = @json(config('app.settings', []));
</script>

<script>
    let globalTargetInput = null;
    let globalStream = null;

    function openGlobalCamera(targetInputSelector) {
        globalTargetInput = document.querySelector(targetInputSelector);
        const video = document.getElementById('globalVideo');
        $('#globalCameraModal').modal('show');

        const facingMode = document.getElementById('cameraFacing').value || 'user';

        const constraints = {
            video: { facingMode: { ideal: facingMode } }
        };

        navigator.mediaDevices.getUserMedia(constraints)
            .then(stream => {
                globalStream = stream;
                video.srcObject = stream;
            })
            .catch(err => {
                Swal.fire('Error', 'Unable to access selected camera: ' + err.message, 'error');
            });
    }



    function captureGlobalImage() {
        const video = document.getElementById('globalVideo');
        const canvas = document.getElementById('globalCanvas');

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = canvas.toDataURL("image/png");

        // Stop camera
        if (globalStream) {
            globalStream.getTracks().forEach(track => track.stop());
        }

        $('#globalCameraModal').modal('hide');

        // Convert base64 to File and attach to target file input
        fetch(imageData)
            .then(res => res.blob())
            .then(blob => {
                const file = new File([blob], `capture_${Date.now()}.png`, { type: "image/png" });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                globalTargetInput.files = dataTransfer.files;
            });
    }

    document.getElementById('cameraFacing').addEventListener('change', () => {
        if (globalStream) {
            globalStream.getTracks().forEach(track => track.stop());
        }
        openGlobalCamera(globalTargetInput ? `#${globalTargetInput.id}` : null);
    });


    function closeGlobalCamera() {
        if (globalStream) {
            globalStream.getTracks().forEach(track => track.stop());
        }
    }
</script>

<script>
    function validateContactNumber(event) {
        var charCode = event.which || event.keyCode;
        // Check if the pressed key is a digit (0-9) or a special key like backspace or delete
        if (charCode < 48 || charCode > 57) {
            event.preventDefault();
        }
    }
</script>

@yield('script')
<script>
    let timer;
    let timeoutMinutes = 60; // 1 hour

    const resetTimer = () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            let countdown = 10; // 10 seconds countdown
            const interval = setInterval(() => {
                if (countdown === 0) {
                    clearInterval(interval);
                    // Auto logout if countdown reaches 0
                    window.location.href = "{{ route('user.logout') }}"; // Update with your logout route
                } else {
                    Swal.update({
                        html: `You will be logged out in <b>${countdown}</b> seconds due to inactivity.`,
                    });
                }
                countdown--;
            }, 1000);

            // SweetAlert2 dialog for inactivity
            Swal.fire({
                title: 'Inactivity Detected',
                html: `You will be logged out in <b>10</b> seconds due to inactivity.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Stay Logged In',
                cancelButtonText: 'Logout Now',
                reverseButtons: true,
                didOpen: () => {
                    Swal.showLoading(); // Show loading spinner
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    // If user chooses to stay logged in, reset the timer
                    clearInterval(interval);
                    resetTimer();
                } else {
                    // If user cancels, logout immediately
                    clearInterval(interval);
                    window.location.href = "{{ route('user.logout') }}"; // Update with your logout route
                }
            });
        }, timeoutMinutes * 60 * 1000);
    };

    window.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;
</script>

<script>
    $(document).ready(function () {
        $('.branch-select').on('change', function () {

            let branchId = $(this).val();

            // Send AJAX request to update the session
            $.ajax({
                url: "{{ route('update.branch') }}",
                type: "POST",
                data: {
                    branch_id: branchId,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    if (response.success) {
                        location.reload(); // Reload the page to apply changes if necessary
                    }
                },
                error: function (xhr) {
                    Swal.fire(
                        'Error!',
                        'Failed to update branch.',
                        'error'
                    );
                }
            });
        });
    });
</script>


</body>
</html>



