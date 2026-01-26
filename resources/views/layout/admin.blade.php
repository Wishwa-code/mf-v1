<!DOCTYPE html>
<html lang="en" data-bs-theme="{{ session('theme', 'light') }}" data-layout-mode="{{ session('theme', 'light') }}" data-menu-color="{{ session('theme', 'light') }}" data-topbar-color="{{ session('theme', 'light') }}" data-layout-position="fixed" data-sidenav-size="condensed" class="menuitem-active">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{session('company_name') ?? session('user') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <!-- Tailwind CSS (for Navbar) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Tailwind Plus Elements -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>

    <script>
        tailwind.config = {
            prefix: 'tw-', // Prefix to avoid conflict with Bootstrap? Or leave empty?
            // User code doesn't use prefix. Bootstrap uses classes like .btn, .d-flex. 
            // Tailwind uses .flex, .block. 
            // There might be conflicts (e.g. .hidden). 
            // Let's use prefix 'tw-' effectively to avoid breaking Bootstrap layout, 
            // BUT the user provided code WITHOUT prefix.
            // Requirement: "use this fr navbar". 
            // I will try without prefix first, but scoped? 
            // Tailwind CDN takes over global. This is risky.
            // Let's use 'tw-' prefix and I will regex replace the user's code to add tw-.
            // ACTUALLY, "High Fidelity" usually implies full control. 
            // But this is a hybrid app.
            // Safest: Use prefix 'tw-' and apply it to user's code.
            prefix: 'tw-',
            corePlugins: {
                preflight: false, // Disable reset to save Bootstrap
            }
        }
    </script>


    @vite(['resources/css/app.scss', 'resources/js/app.js'])

    {{-- DataTables CSS --}}
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet">
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
                /* color: #000; */
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
    @include('layout.partials.sidebar-styles')
    @include('component.header.styles')

    @yield('head')
</head>

<?php
$company = user_data('company') ?? null;
$logo = $company['Logo'] ?? null;

// All branches
$branch = user_data('branches') ?? [];

// Allowed branches 
$allowedBranches = $branch;

// Use company data from session
$companyData = user_data('company') ?? null;
$companyItem = $companyData ? (object) $companyData : null;
//Sms Mask
$companyMask = $companyItem ? $companyItem->SMS_Mask : null;

?>

<body>
    @include('layout.sidebar')
    <div class="wrapper glass-bg">
        @include('layout.navbar')

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            @include('layout.footer')
        </div>


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

        @include('component.header.modals.cashier-start')
        @include('component.header.modals.day-end')
    </div>

    @include('component.header.scripts')

    @yield('script')
    @stack('scripts')

</body>

</html>