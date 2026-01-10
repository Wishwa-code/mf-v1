<style>
    /* Navbar Responsive & Modern styles */
    .navbar-custom {
        display: flex;
        flex-wrap: wrap;
        /* allow wrapping on small screens */
        justify-content: space-between;
        align-items: center;
        padding: 0 1rem;
    }

    .topbar {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }



    /* Small screen adjustments */
    @media (max-width: 768px) {
        .modern-date-time {
            display: none !important;
            /* Hide date on mobile */
        }

        .navbar-custom {
            padding: 0.5rem;
        }

        .button-toggle-menu {
            margin-right: 0.5rem !important;
        }
    }

    /* Default (Light Mode) Navbar */
    .navbar-custom {
        background: rgba(255, 255, 255, 0.7) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border-bottom: none !important;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        /* Layout Fix: Push navbar to right of sidebar */
        /* margin-left handled by fixed positioning in sidebar-styles */
        transition: margin-left 0.3s ease, background 0.3s ease;
    }

    /* Dark Mode Navbar Override */
    html[data-layout-mode="dark"] .navbar-custom,
    html[data-bs-theme="dark"] .navbar-custom {
        background: rgba(30, 30, 40, 0.6) !important;
        /* Dark Glass */
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    /* Mobile: Reset margin */
    @media (max-width: 1024px) {
        .navbar-custom {
            margin-left: 0 !important;
        }
    }

    .topbar {
        overflow: visible !important;
    }

    .modern-branch-switcher .modern-dropdown-toggle {
        background: linear-gradient(135deg, #6a5e87 0%, #ea7074 100%);
        border: none;
        color: white;
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(106, 94, 135, 0.2);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .modern-branch-switcher .modern-dropdown-toggle:hover,
    .modern-branch-switcher .modern-dropdown-toggle:focus {
        background: linear-gradient(135deg, #5b4d7a 0%, #e65b60 100%);
        transform: translateY(-1px);
        box-shadow: 0 8px 12px -1px rgba(106, 94, 135, 0.3);
    }

    .modern-branch-switcher .modern-dropdown-menu {
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 16px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        padding: 8px;
        margin-top: 10px;
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
        min-width: 240px;
        /* Scrollbar styling */
        scroll-behavior: smooth;
    }

    .modern-branch-switcher .modern-dropdown-menu::-webkit-scrollbar {
        width: 6px;
    }

    .modern-branch-switcher .modern-dropdown-menu::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 4px;
    }

    .modern-branch-switcher .modern-dropdown-menu::-webkit-scrollbar-thumb {
        background: rgba(99, 102, 241, 0.5);
        border-radius: 4px;
    }

    .modern-branch-switcher .modern-dropdown-item {
        border-radius: 10px;
        padding: 10px 16px;
        font-weight: 500;
        color: #475569;
        transition: all 0.2s;
        position: relative;
    }

    .modern-branch-switcher .modern-dropdown-item:hover {
        background: linear-gradient(to right, #eef2ff, #f5f3ff);
        color: #4f46e5;
        transform: translateX(4px);
    }

    @media (max-width: 768px) {
        .modern-branch-switcher .modern-dropdown-toggle {
            width: 35px;
            height: 35px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.4);
            min-width: 0px !important;
        }

        .modern-branch-switcher .modern-dropdown-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(99, 102, 241, 0.5);
        }

        .modern-branch-switcher .modern-dropdown-toggle i {
            font-size: 1.2rem;
            margin-right: 0 !important;
        }

        /* Hide text and arrow on mobile */
        .modern-branch-switcher .branch-text,
        .modern-branch-switcher .dropdown-arrow {
            display: none !important;
        }
    }

    /* Modern Date/Time */
    .modern-date-time {
        line-height: 1;
        text-align: right;
    }

    .modern-date-time .day-text {
        font-family: 'Poppins', sans-serif;
        font-size: 1.2rem;
        font-weight: 800;
        color: #334155;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: linear-gradient(135deg, #475569 0%, #1e293b 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        display: block;
        margin-bottom: 0px;
    }

    .modern-date-time .date-text {
        font-family: 'Poppins', sans-serif;
        font-size: 0.85rem;
        font-weight: 600;
        color: #94a3b8;
        display: block;
    }

    /* Modern Action Buttons */
    .modern-action-btn {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #fff;
        color: #64748b;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #e2e8f0;
        text-decoration: none !important;
        position: relative;
    }

    .modern-action-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    /* Specific Action Colors */
    .modern-action-btn[title="Approvals"] {
        color: #ef4444;
        background: #fef2f2;
        border-color: #fee2e2;
    }

    .modern-action-btn[title="Approvals"]:hover {
        background: #ef4444;
        color: white;
        border-color: #ef4444;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2);
    }

    .modern-action-btn[title="Toggle Theme"] {
        color: #6a5e87;
        background: #f3f0ff;
        border-color: #e9e6f7;
    }

    .modern-action-btn[title="Toggle Theme"]:hover {
        background: #6a5e87;
        color: white;
        border-color: #6a5e87;
        box-shadow: 0 4px 10px rgba(106, 94, 135, 0.2);
    }

    .modern-action-btn i {
        font-size: 1.25rem;
    }


    /* Modern Account Center Styles */
    .modern-account-wrapper {
        position: relative;
    }

    .modern-account-btn {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        border: none;
        border-radius: 12px;
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.3);
        position: relative;
        z-index: 1002;
    }

    .modern-account-btn i {
        color: white;
        font-size: 22px;
        transition: transform 0.3s ease;
    }

    .modern-account-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
    }

    .modern-account-btn.active {
        border-radius: 12px 12px 0 0;
        box-shadow: none;
    }

    .modern-account-btn.active i {
        transform: rotate(180deg);
    }

    /* Dropdown Content */
    .account-center-content {
        position: absolute;
        top: 100%;
        right: 0;
        width: 320px;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 16px 0 16px 16px;
        /* Top right corner sharp to match button */
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        overflow: hidden;

        /* Animation State */
        max-height: 0;
        opacity: 0;
        transform-origin: top right;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 1001;
        margin-top: 0;
        /* Connected to button */
    }

    .account-center-content.open {
        max-height: 600px;
        /* Arbitrary large enough height */
        opacity: 1;
        padding: 16px;
    }

    /* Loading State */
    .account-loader {
        display: flex;
        justify-content: center;
        padding: 20px;
    }

    .spinner {
        width: 24px;
        height: 24px;
        border: 3px solid #e2e8f0;
        border-top-color: #6366f1;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Modern List Items */
    .account-list-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-radius: 10px;
        color: #475569;
        text-decoration: none;
        transition: all 0.2s;
        margin-bottom: 4px;
    }

    .account-list-item:hover {
        background: #f1f5f9;
        color: #4f46e5;
        transform: translateX(4px);
    }

    .account-list-item i {
        font-size: 18px;
        margin-right: 12px;
        color: #94a3b8;
        transition: color 0.2s;
    }

    .account-list-item:hover i {
        color: #6366f1;
    }

    .account-section-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        margin: 16px 0 8px 12px;
    }

    /* Animated Account Button (User Provided Style) */
    .account-btn-animated {
        width: 35px;
        height: 35px;
        border-radius: 12px;
        background: linear-gradient(to right, #6a5e87, #ea7074);
        /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */


        border: none;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0px 0px 0px 4px rgba(223, 230, 244, 0.25);
        /* Blue Shadow */
        cursor: pointer;
        transition-duration: 0.3s;
        overflow: hidden;
        position: relative;
        text-decoration: none !important;
        color: white !important;
    }

    .account-btn-animated .svgIcon {
        font-size: 20px;
        transition-duration: 0.3s;
        display: flex;
        align-items: center;
    }

    .account-btn-animated:hover {
        width: 140px;
        height: 40px;
        border-radius: 12px;
        transition-duration: 0.3s;
        background: linear-gradient(to right, #6a5e87, #ea7074);

        /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

        /* Blue match */
        align-items: center;
    }

    .account-btn-animated:hover .svgIcon {
        transition-duration: 0.3s;
        transform: translateY(-200%);
    }

    .account-btn-animated::before {
        position: absolute;
        bottom: -20px;
        content: "Account Center";
        color: white;
        font-size: 0px;
        white-space: nowrap;
    }

    .account-btn-animated:hover::before {
        font-size: 13px;
        opacity: 1;
        bottom: unset;
        transition-duration: 0.3s;
    }

    /* Hiding on mobile to prevent layout issues if needed, or keeping it small */
</style>

<style>
    /* 
       GLASSMORPHISM & MODERN UI ENHANCEMENTS 
       - Adds dynamic gradient mesh backgrounds.
       - Applies 'True Glass' effect with backdrop-filter to cards, modals, and sidebars.
       - Modernizes spacing and borders for a depth effect.
    */

    /* --- LIGHT MODE (Default) --- */
    body {
        background-color: #f3f4f6;
        /* Pastel/Soft Gradient Mesh for Light Mode */
        background-image:
            radial-gradient(at 40% 20%, hsla(28, 100%, 74%, 0.1) 0px, transparent 50%),
            radial-gradient(at 80% 0%, hsla(189, 100%, 56%, 0.1) 0px, transparent 50%),
            radial-gradient(at 0% 50%, hsla(340, 100%, 76%, 0.1) 0px, transparent 50%),
            radial-gradient(at 80% 50%, hsla(355, 100%, 93%, 0.1) 0px, transparent 50%),
            radial-gradient(at 0% 100%, hsla(22, 100%, 77%, 0.1) 0px, transparent 50%),
            radial-gradient(at 80% 100%, hsla(242, 100%, 70%, 0.1) 0px, transparent 50%),
            radial-gradient(at 0% 0%, hsla(343, 100%, 76%, 0.1) 0px, transparent 50%);
        background-attachment: fixed;
    }

    /* True Glass Cards */
    .card,
    .card-modern,
    .modal-content,
    .offcanvas {
        background: rgba(255, 255, 255, 0.7) !important;
        /* Semi-transparent White */
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border: 1px solid rgba(255, 255, 255, 0.6) !important;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07) !important;
    }

    /* --- DARK MODE (Overrides) --- */
    html[data-bs-theme="dark"] body,
    html[data-layout-mode="dark"] body {
        background-color: #0f0c29 !important;
        /* Vivid / Deep Cosmic Gradient Mesh */
        background-image:
            radial-gradient(at 0% 0%, hsla(253, 16%, 7%, 1) 0, transparent 50%),
            radial-gradient(at 50% 0%, hsla(225, 39%, 30%, 1) 0, transparent 50%),
            radial-gradient(at 100% 0%, hsla(339, 49%, 30%, 1) 0, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(123, 31, 162, 0.4) 0%, transparent 50%),
            radial-gradient(circle at 20% 80%, rgba(69, 104, 220, 0.4) 0%, transparent 50%);
        background-attachment: fixed;
    }

    html[data-bs-theme="dark"] .card,
    html[data-bs-theme="dark"] .card-modern,
    html[data-bs-theme="dark"] .modal-content,
    html[data-bs-theme="dark"] .offcanvas,
    html[data-layout-mode="dark"] .card,
    html[data-layout-mode="dark"] .card-modern,
    html[data-layout-mode="dark"] .modal-content,
    html[data-layout-mode="dark"] .offcanvas,
    html[data-layout-mode="dark"] .footer,
    html[data-bs-theme="dark"] .footer {
        background: rgba(30, 30, 40, 0.6) !important;
        /* Semi-transparent Dark */
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3) !important;
        color: #e0e0e0 !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
    }

    /* Dark Mode Tables */
    html[data-layout-mode="dark"] .table,
    html[data-bs-theme="dark"] .table {
        color: #e0e0e0 !important;
        border-color: rgba(255, 255, 255, 0.05) !important;
    }

    html[data-layout-mode="dark"] .table th,
    html[data-layout-mode="dark"] .table td,
    html[data-bs-theme="dark"] .table th,
    html[data-bs-theme="dark"] .table td {
        border-color: rgba(255, 255, 255, 0.05) !important;
        background: transparent !important;
    }

    /* Dark Mode Inputs */
    html[data-layout-mode="dark"] .form-control,
    html[data-layout-mode="dark"] .form-select,
    html[data-bs-theme="dark"] .form-control,
    html[data-bs-theme="dark"] .form-select {
        background-color: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
    }

    html[data-layout-mode="dark"] .form-control:focus,
    html[data-layout-mode="dark"] .form-select:focus,
    html[data-bs-theme="dark"] .form-control:focus,
    html[data-bs-theme="dark"] .form-select:focus {
        background-color: rgba(255, 255, 255, 0.08) !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25) !important;
    }

    /* Dark Mode Dropdowns */
    html[data-layout-mode="dark"] .dropdown-menu,
    html[data-bs-theme="dark"] .dropdown-menu {
        background: rgba(30, 30, 40, 0.8) !important;
        backdrop-filter: blur(12px) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
    }

    html[data-layout-mode="dark"] .dropdown-item,
    html[data-bs-theme="dark"] .dropdown-item {
        color: #e0e0e0 !important;
    }

    html[data-layout-mode="dark"] .dropdown-item:hover,
    html[data-bs-theme="dark"] .dropdown-item:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
    }


    /* Text Adjustments for Glass */
    html[data-bs-theme="dark"] .text-muted,
    html[data-layout-mode="dark"] .text-muted {
        color: rgba(224, 224, 224, 0.6) !important;
    }

    html[data-bs-theme="dark"] h1,
    html[data-bs-theme="dark"] h2,
    html[data-bs-theme="dark"] h3,
    html[data-bs-theme="dark"] h4,
    html[data-bs-theme="dark"] h5,
    html[data-bs-theme="dark"] h6,
    html[data-layout-mode="dark"] h1,
    html[data-layout-mode="dark"] h2,
    html[data-layout-mode="dark"] h3,
    html[data-layout-mode="dark"] h4,
    html[data-layout-mode="dark"] h5,
    html[data-layout-mode="dark"] h6 {
        color: #f0f0f0 !important;
    }

    /* Glass Panels in Dark Mode */
    html[data-bs-theme="dark"] .glass-panel,
    html[data-layout-mode="dark"] .glass-panel {
        background: rgba(30, 30, 40, 0.6) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
    }

    /* Text Visiblity Fixes */
    html[data-bs-theme="dark"] .text-dark,
    html[data-layout-mode="dark"] .text-dark {
        color: #f0f0f0 !important;
    }

    html[data-bs-theme="dark"] .stat-value,
    html[data-layout-mode="dark"] .stat-value {
        color: #f0f0f0 !important;
    }

    html[data-bs-theme="dark"] .stat-label,
    html[data-layout-mode="dark"] .stat-label {
        color: rgba(224, 224, 224, 0.7) !important;
    }

    html[data-bs-theme="dark"] .shortcut-btn,
    html[data-layout-mode="dark"] .shortcut-btn {
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #e0e0e0 !important;
    }

    html[data-bs-theme="dark"] .shortcut-btn:hover,
    html[data-layout-mode="dark"] .shortcut-btn:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
    }

    /* ===== Modern Footer ===== */
    .footer {
        position: relative;
        width: 100%;
        padding: 12px 0;
        font-size: 0.85rem;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-top: 1px solid rgba(255, 255, 255, 0.15);
        background: rgba(255, 255, 255, 0.75);
        color: #333;
        transition: all 0.3s ease;
    }

    /* Footer text */
    .footer a {
        color: #0d6efd;
        font-weight: 500;
        text-decoration: none;
    }

    .footer a:hover {
        text-decoration: underline;
    }

    /* Center alignment */
    .footer .col-12 {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 4px;
    }

    /* ===== Dark Mode Support ===== */
    [data-bs-theme="dark"] .footer {
        background: rgba(20, 20, 20, 0.75);
        color: #e4e4e4;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    [data-bs-theme="dark"] .footer a {
        color: #6ea8fe;
    }

    /* ===== Optional: Fixed footer (DISABLED for Floating Layout) ===== */
    /* .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        z-index: 1001;
        margin-left: var(--sidebar-width) !important;
    } */

    /* Ensure consistent Z-Layering on Desktop to prevent "inside" look */
    @media (min-width: 1025px) {
        .leftside-menu {
            z-index: 1001 !important;
        }

        .navbar-custom {
            z-index: 1001 !important;
        }
    }

    body {
        padding-bottom: 45px;
    }
</style>