<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{session('company_name') ?? session('user') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    {{-- DataTables CSS --}}
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet">
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

    @include('component.header.navbarcss')
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

    {{-- DataTables JS --}}
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
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
                video: {
                    facingMode: {
                        ideal: facingMode
                    }
                }
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
                    const file = new File([blob], `capture_${Date.now()}.png`, {
                        type: "image/png"
                    });
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
    @stack('scripts')

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
                        window.location.href = "{{ route('user.logout') }}";
                    } else {
                        Swal.update({
                            html: `You will be logged out in <b>${countdown}</b> seconds due to inactivity.`,
                        });
                    }
                    countdown--;
                }, 1000);

                Swal.fire({
                    title: 'Inactivity Detected',
                    html: `You will be logged out in <b>10</b> seconds due to inactivity.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Stay Logged In',
                    cancelButtonText: 'Logout Now',
                    reverseButtons: true,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        clearInterval(interval);
                        resetTimer();
                    } else {
                        clearInterval(interval);
                        window.location.href = "{{ route('user.logout') }}";
                    }
                });
            }, timeoutMinutes * 60 * 1000);
        };

        window.onload = resetTimer;
        document.onmousemove = resetTimer;
        document.onkeypress = resetTimer;
    </script>

    <script>
        $(document).ready(function() {
            $(document).on('click', '.branch-option', function(e) {
                e.preventDefault();

                const branchId = $(this).data('branch-id');
                const branchName = $(this).data('branch-name');

                $('.branch-text').text(branchName);

                $.ajax({
                    url: "{{ route('update.branch') }}",
                    type: "POST",
                    data: {
                        branch_id: branchId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = '/';
                        } else {
                            Swal?.fire?.('Oops', response.message || 'Failed to update branch.', 'error');
                        }
                    },
                    error: function() {
                        Swal?.fire?.('Error!', 'Failed to update branch.', 'error');
                    }
                });
            });
        });
    </script>

    {{-- HEAD OFFICE NOTIFICATIONS – new pending approvals --}}
    <script>
        @if(session('branch_id') == -1)
        let lastApprovalId = 0;
        let approvalPollInterval = null;

        function initApprovalNotifications() {
            $.ajax({
                url: '{{ route("approval.notifications") }}',
                method: 'GET',
                data: {
                    init: 1
                },
                success: function(response) {
                    if (response.success) {
                        lastApprovalId = response.last_id || 0;
                        startApprovalPolling();
                    } else {
                        console.warn('Approval notification init failed:', response.message);
                    }
                },
                error: function() {
                    console.warn('Error initializing approval notifications');
                }
            });
        }

        function startApprovalPolling() {
            if (approvalPollInterval) {
                clearInterval(approvalPollInterval);
            }
            approvalPollInterval = setInterval(pollApprovals, 15000);
        }

        function pollApprovals() {
            $.ajax({
                url: '{{ route("approval.notifications") }}',
                method: 'GET',
                data: {
                    since_id: lastApprovalId
                },
                success: function(response) {
                    if (!response.success) {
                        console.warn('Approval notification error:', response.message);
                        return;
                    }

                    if (typeof response.last_id !== 'undefined') {
                        lastApprovalId = response.last_id;
                    }

                    const approvals = response.approvals || [];
                    if (approvals.length > 0) {
                        showApprovalNotification(approvals);
                        updateApprovalMenuBadge(approvals.length);
                    }
                },
                error: function() {
                    console.warn('Error polling approval notifications');
                }
            });
        }

        function showApprovalNotification(approvals) {
            let html = '<div style="text-align:left;">';
            html += '<strong>' + approvals.length + ' new approval request(s)</strong><br><br>';

            approvals.forEach(function(item) {
                const typeText = item.type ? item.type : ('Type ' + item.typeid);
                const branchName = item.branch_name ? item.branch_name : 'Unknown Branch';
                const dateTime = item.data_time;

                html += '<div style="margin-bottom:6px;">';
                html += '<i class="ri-notification-3-line"></i> ';
                html += '<strong>' + typeText + '</strong>';
                html += ' from <span style="color:#0d6efd;">' + branchName + '</span>';
                html += '<br><small class="text-muted">' + dateTime + '</small>';
                html += '</div>';
            });

            html += '</div>';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: 'New pending approvals',
                    html: html,
                    showConfirmButton: false,
                    timer: 8000,
                    timerProgressBar: true
                });
            } else {
                alert(approvals.length + ' new approval request(s) arrived.');
            }
        }

        function updateApprovalMenuBadge(newCount) {
            const badge = document.getElementById('approvalBadge');
            if (!badge) return;

            let current = parseInt(badge.innerText || '0', 10);
            if (isNaN(current)) current = 0;

            const total = current + newCount;
            badge.innerText = total;
            badge.style.display = total > 0 ? 'inline-block' : 'none';
        }

        $(document).ready(function() {
            initApprovalNotifications();
        });
        @endif
    </script>

    {{-- BRANCH NOTIFICATIONS – when HO approves/rejects/callback --}}
    {{-- BRANCH NOTIFICATIONS – when HO approves / rejects / callback --}}
    <script>
        @if(session('branch_id') != -1)
        let branchNotifInterval = null;

        function pollBranchNotifications() {
            $.ajax({
                url: '{{ route("approval.branch.notifications") }}',
                method: 'GET',
                success: function(response) {
                    if (!response.success) {
                        console.warn('Branch notification error:', response.message);
                        return;
                    }

                    const items = response.items || [];
                    if (items.length > 0) {
                        showBranchNotification(items);
                    }
                },
                error: function() {
                    console.warn('Error polling branch notifications');
                }
            });
        }

        function mapStatus(status) {
            switch (parseInt(status, 10)) {
                case 1:
                    return {
                        text: 'Approved', icon: 'success'
                    };
                case 2:
                    return {
                        text: 'Rejected', icon: 'error'
                    };
                case 3:
                    return {
                        text: 'Sent for Callback', icon: 'warning'
                    };
                default:
                    return {
                        text: 'Updated', icon: 'info'
                    };
            }
        }

        function showBranchNotification(items) {
            let html = '<div style="text-align:left;">';
            let mainIcon = 'info';

            html += '<strong>' + items.length + ' approval update(s)</strong><br><br>';

            items.forEach(function(item) {
                const statusMap = mapStatus(item.status);
                mainIcon = statusMap.icon;

                const typeText = item.type ? item.type : ('Type ' + item.typeid);
                const timeText = item.created_at || item.updated_at;

                html += '<div style="margin-bottom:6px;">';
                html += '<strong>' + typeText + '</strong> - ' + statusMap.text;
                if (item.message) {
                    html += '<br><small>' + item.message + '</small>';
                }
                if (timeText) {
                    html += '<br><small class="text-muted">' + timeText + '</small>';
                }
                html += '</div>';
            });

            html += '</div>';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: mainIcon,
                    title: 'Approval status updated',
                    html: html,
                    showConfirmButton: false,
                    timer: 8000,
                    timerProgressBar: true
                });
            } else {
                alert(items.length + ' approval request(s) updated.');
            }
        }

        $(document).ready(function() {
            // poll every 15 seconds
            branchNotifInterval = setInterval(pollBranchNotifications, 15000);
        });
        @endif
    </script>



    <!-- Global Theme Switcher Script -->
    <script>
        $(document).ready(function() {
            // Initialize Theme from LocalStorage
            const savedTheme = localStorage.getItem('theme') || 'dark'; // Default to dark
            $('html').attr('data-layout-mode', savedTheme);
            $('html').attr('data-bs-theme', savedTheme); // Also set BS theme

            // Toggle Button Click
            // Unbind previous events to prevent double toggling if script is included twice
            $(document).off('click', '#light-dark-mode').on('click', '#light-dark-mode', function(e) {
                e.preventDefault();
                const currentMode = $('html').attr('data-layout-mode');
                const newMode = (currentMode === 'light') ? 'dark' : 'light';

                $('html').attr('data-layout-mode', newMode);
                $('html').attr('data-bs-theme', newMode); // Also set BS theme
                localStorage.setItem('theme', newMode);
            });

            // Sidebar Toggle Logic
            // Sidebar Toggle Logic
            $(document).off('click', '.button-toggle-menu').on('click', '.button-toggle-menu', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const windowWidth = $(window).width();

                if (windowWidth < 768) {
                    // Mobile: Toggle sidebar-enable class on body
                    $('body').toggleClass('sidebar-enable');
                } else {
                    // Desktop: Toggle between default and condensed
                    const html = $('html');
                    const currentSize = html.attr('data-sidenav-size') || 'default';
                    const newSize = (currentSize === 'condensed') ? 'default' : 'condensed';

                    html.attr('data-sidenav-size', newSize);

                    // Trigger resize event to smooth out charts/tables
                    setTimeout(function() {
                        window.dispatchEvent(new Event('resize'));
                    }, 300);
                }
            });
        });
    </script>
</body>

</html>
```