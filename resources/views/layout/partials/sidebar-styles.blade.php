<style>
    /* Modern Sidebar Styles */
    :root {
        --sidebar-width: 260px;
        --sidebar-bg: #313a46;
        /* Using the requested primary gradient for active background */
        --sidebar-active-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --sidebar-hover-bg: rgba(106, 94, 135, 0.2);
        --sidebar-text: #ced4da;
        --sidebar-text-active: #ffffff;
        --sidebar-border: rgba(255, 255, 255, 0.1);
        --sidebar-font: 'Inter', sans-serif;
    }

    /* Apply Font */
    .leftside-menu,
    .side-nav-link {
        font-family: var(--sidebar-font) !important;
    }

    /* Light Mode */
    html[data-layout-mode="light"] .leftside-menu {
        --sidebar-bg: #ffffff;
        --sidebar-text: #5d6778;
        --sidebar-hover-bg: rgba(106, 94, 135, 0.1);
        border-right: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Dark Mode */
    html[data-layout-mode="dark"] .leftside-menu,
    html[data-bs-theme="dark"] .leftside-menu,
    html[data-layout-mode="detached"] .leftside-menu {
        background: rgba(30, 30, 40, 0.8) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        --sidebar-text: #aab8c5;
        --sidebar-hover-bg: rgba(255, 255, 255, 0.05);
        border-right: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 5px 0 25px rgba(0, 0, 0, 0.2);
    }

    /* Modern Glassy/Gradient Active State */
    .side-nav-item.menuitem-active>.side-nav-link,
    .side-nav-link[aria-expanded="true"],
    .side-nav-link:active,
    .side-nav-link.active {
        background: var(--sidebar-active-bg) !important;
        color: var(--sidebar-text-active) !important;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        /* Glow effect from primary color */
        border: none;
    }

    /* Hover State */
    .side-nav-link:hover {
        background: var(--sidebar-hover-bg);
        color: var(--sidebar-text-active) !important;
        transform: translateX(4px);
    }

    /* Icon Styles */
    .side-nav-link i {
        font-size: 1.2rem;
        margin-right: 12px;
        opacity: 0.8;
        transition: all 0.3s ease;
    }

    .side-nav-item.menuitem-active>.side-nav-link i,
    .side-nav-link.active i {
        color: #fff !important;
        opacity: 1;
    }

    /* Light Mode Pressed Effect */
    html[data-layout-mode="light"] .side-nav-link:hover {
        background: var(--sidebar-hover-bg) !important;
        color: #ffffff !important;
        box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.1), inset -2px -2px 5px rgba(255, 255, 255, 0.05);
    }

    html[data-layout-mode="light"] .side-nav-link:hover i {
        color: #ffffff !important;
    }

    html[data-layout-mode="light"] .side-nav-item.menuitem-active>.side-nav-link,
    html[data-layout-mode="light"] .side-nav-link.active {
        background: var(--sidebar-active-bg) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(106, 94, 135, 0.3) !important;
    }

    html[data-layout-mode="light"] .side-nav-item.menuitem-active>.side-nav-link i,
    html[data-layout-mode="light"] .side-nav-link.active i {
        color: #ffffff !important;
    }

    /* Ensure text color adapts */
    .side-nav-link span:not(.menu-arrow) {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex-grow: 1;
        margin-right: 8px;
    }

    .side-nav-link i {
        font-size: 1.2rem;
        margin-right: 12px;
        opacity: 0.8;
        transition: all 0.3s ease;
    }

    .side-nav-link:hover i {
        opacity: 1;
        transform: scale(1.1);
    }

    .side-nav-item.menuitem-active>.side-nav-link i,
    .side-nav-link.active i {
        color: #fff !important;
        opacity: 1;
    }

    /* Submenu styling fix for text color */
    .side-nav-second-level li a,
    .side-nav-third-level li a {
        color: var(--sidebar-text) !important;
        padding: 10px 10px 10px 14px !important;
        font-size: 0.88rem !important;
        transition: all 0.2s ease;
        position: relative;
        border-radius: 6px;
        display: block;
    }

    .side-nav-second-level li a:hover,
    .side-nav-third-level li a:hover {
        color: var(--sidebar-text) !important;
        background: var(--sidebar-hover-bg);
        padding-left: 18px !important;
        /* Subtle inset for submenus */
        box-shadow: inset 1px 1px 3px rgba(0, 0, 0, 0.1);
    }

    .side-nav-second-level li.menuitem-active>a,
    .side-nav-third-level li.menuitem-active>a {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.1);
        font-weight: 500;
        box-shadow: inset 2px 2px 4px rgba(0, 0, 0, 0.2);
    }

    /* Light Mode Active Submenu Fix */
    html[data-layout-mode="light"] .side-nav-second-level li.menuitem-active>a,
    html[data-layout-mode="light"] .side-nav-third-level li.menuitem-active>a {
        color: var(--sidebar-active-bg) !important;
        background: rgba(106, 94, 135, 0.1) !important;
        box-shadow: none;
        font-weight: 600;
    }

    /* Scrollbar */
    .simplebar-scrollbar:before {
        background: rgba(128, 128, 128, 0.5);
        /* Neutral scrollbar */
    }

    /* Menu Arrow - Hidden as per request */
    .menu-arrow {
        display: none !important;
    }

    .side-nav-link[aria-expanded="true"] .menu-arrow {
        transform: rotate(90deg);
    }

    /* Branding Area */
    .logo-box {
        background: transparent !important;
    }

    /* Condensed Sidebar Styles - Modern Icon-Only Mode */
    html[data-sidenav-size="condensed"] {
        --sidebar-width: 70px;
    }

    html[data-sidenav-size="condensed"] .leftside-menu {
        width: var(--sidebar-width) !important;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(20px) !important;
    }

    /* Condensed Mode - Dark Theme Override */
    html[data-layout-mode="dark"][data-sidenav-size="condensed"] .leftside-menu,
    html[data-bs-theme="dark"][data-sidenav-size="condensed"] .leftside-menu {
        background: rgba(30, 30, 40, 0.6) !important;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
    }

    /* Condensed Mode - Light Theme Override */
    /* Condensed Mode - Light Theme Override */
    html[data-layout-mode="light"][data-sidenav-size="condensed"] .leftside-menu {
        background: #ffffff !important;
        border-right: 1px solid rgba(0, 0, 0, 0.1);
    }

    html[data-sidenav-size="condensed"] .content-page {
        margin-left: var(--sidebar-width) !important;
        transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    html[data-sidenav-size="condensed"] #leftside-menu-container {
        padding: 12px 0;
    }

    html[data-sidenav-size="condensed"] .side-nav {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    html[data-sidenav-size="condensed"] .side-nav-item {
        margin: 2px 0;
        width: 100%;
        display: flex;
        justify-content: center;
    }

    html[data-sidenav-size="condensed"] .side-nav-link {
        justify-content: center !important;
        padding: 14px 0 !important;
        width: 50px;
        height: 50px;
        border-radius: 12px;
        margin: 0 auto;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    html[data-sidenav-size="condensed"] .side-nav-link:hover {
        background: rgba(106, 94, 135, 0.6) !important;
        transform: scale(1.1);
    }

    html[data-sidenav-size="condensed"] .side-nav-link span:not(.menu-arrow),
    html[data-sidenav-size="condensed"] .menu-arrow,
    html[data-sidenav-size="condensed"] .side-nav-title {
        display: none !important;
    }

    html[data-sidenav-size="condensed"] .side-nav-link i {
        margin-right: 0 !important;
        font-size: 1.35rem;
        transition: all 0.25s ease;
    }

    /* Icon Colors - Dark Mode */
    html[data-layout-mode="dark"][data-sidenav-size="condensed"] .side-nav-link i,
    html[data-bs-theme="dark"][data-sidenav-size="condensed"] .side-nav-link i {
        color: rgba(255, 255, 255, 0.8);
    }

    html[data-layout-mode="dark"][data-sidenav-size="condensed"] .side-nav-link:hover i,
    html[data-bs-theme="dark"][data-sidenav-size="condensed"] .side-nav-link:hover i {
        color: #fff;
    }

    /* Icon Colors - Light Mode */
    html[data-layout-mode="light"][data-sidenav-size="condensed"] .side-nav-link i {
        color: #6c757d;
    }

    html[data-layout-mode="light"][data-sidenav-size="condensed"] .side-nav-link:hover i {
        color: #ffffff !important;
    }

    html[data-sidenav-size="condensed"] .side-nav-link:hover {
        transform: scale(1.1);
    }

    /* Hover BG - Dark Mode */
    html[data-layout-mode="dark"][data-sidenav-size="condensed"] .side-nav-link:hover,
    html[data-bs-theme="dark"][data-sidenav-size="condensed"] .side-nav-link:hover {
        background: rgba(106, 94, 135, 0.6) !important;
    }

    /* Hover BG - Light Mode */
    html[data-layout-mode="light"][data-sidenav-size="condensed"] .side-nav-link:hover {
        background: var(--sidebar-hover-bg) !important;
    }

    /* Active state in condensed mode */
    html[data-sidenav-size="condensed"] .side-nav-item.menuitem-active>.side-nav-link,
    html[data-sidenav-size="condensed"] .side-nav-link.active {
        background: var(--sidebar-active-bg) !important;
        box-shadow: 0 4px 15px rgba(106, 94, 135, 0.5);
    }

    html[data-sidenav-size="condensed"] .side-nav-item.menuitem-active>.side-nav-link i,
    html[data-sidenav-size="condensed"] .side-nav-link.active i {
        color: #fff !important;
    }

    /* Hide submenus in condensed mode */
    html[data-sidenav-size="condensed"] .collapse,
    html[data-sidenav-size="condensed"] .side-nav-second-level,
    html[data-sidenav-size="condensed"] .side-nav-third-level {
        display: none !important;
    }

    /* Tooltip for condensed mode - show menu name on hover */
    html[data-sidenav-size="condensed"] .side-nav-link::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 70px;
        top: 50%;
        transform: translateY(-50%);
        background: #1e2530;
        color: #fff;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 0.85rem;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        z-index: 9999;
        pointer-events: none;
    }

    html[data-sidenav-size="condensed"] .side-nav-link:hover::after {
        opacity: 1;
        visibility: visible;
        left: 75px;
    }

    /* Navbar adjustment - Global (Disable Sticky) */
    /* Navbar adjustment - Global (Sticky / Fixed) */
    .navbar-custom {
        position: fixed !important;
        top: 0 !important;
        right: 0 !important;
        left: var(--sidebar-width) !important;
        width: auto !important;
        margin-left: 0 !important;
        /* Reset any margin since we use left positioning */
        z-index: 1001;
        transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Logo Alignment */
    .logo {
        width: 100% !important;
        text-align: center;
        display: block;
    }

    /* Content Page Global Adjustment */
    .content-page {
        margin-left: var(--sidebar-width) !important;
        transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        padding-top: 70px;
        min-height: 100vh;
    }

    /* Desktop Layout Enforcement */
    @media (min-width: 992px) {
        .leftside-menu {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;
            width: var(--sidebar-width) !important;
            height: 100vh !important;
            z-index: 1005 !important;
            display: flex !important;
            flex-direction: column !important;
        }
    }

    /* Responsive Styles & Mobile Toggle */
    @media (max-width: 991.98px) {
        .leftside-menu {
            transform: translateX(-100%);
            box-shadow: none;
            z-index: 1040;
        }

        .content-page {
            margin-left: 0 !important;
        }

        .navbar-custom {
            margin-left: 0 !important;
            left: 0 !important;
        }

        /* When sidebar is enabled via body class */
        body.sidebar-enable .leftside-menu {
            transform: translateX(0);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.3);
            width: var(--sidebar-width) !important;
            /* Full width on mobile */
            opacity: 1 !important;
        }
    }

    /* Remove Offcanvas Backdrop Overlay */
    .offcanvas-backdrop.show {
        opacity: 0 !important;
        display: none !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }

    /* Mobile Close Button Style */
    .button-close-fullsidebar {
        display: none;
    }

    @media (max-width: 991.98px) {
        .button-close-fullsidebar {
            display: flex;
            align-items: center;
            justify-content: center;
            position: fixed;
            /* Changed from absolute to fixed to stay on top */
            top: 20px;
            right: 20px;
            /* Adjust if needed, relative to viewport/sidebar edge */
            width: 36px;
            height: 36px;
            background: rgba(0, 0, 0, 0.2);
            /* Darker default bg */
            border-radius: 50%;
            color: #fff;
            font-size: 1.2rem;
            text-decoration: none;
            z-index: 1060;
            /* Higher than sidebar */
            transition: all 0.3s ease;
            backdrop-filter: blur(4px);
        }

        /* Explicit Light Mode Override */
        html[data-layout-mode="light"] .button-close-fullsidebar,
        html[data-bs-theme="light"] .button-close-fullsidebar {
            background: rgba(0, 0, 0, 0.1);
            /* Dark bg for contrast */
            color: #333 !important;
            /* Dark icon */
        }

        .button-close-fullsidebar:hover {
            background: rgba(255, 50, 50, 0.8) !important;
            color: #fff !important;
            transform: rotate(90deg);
        }
    }

    /* SimpleBar Fix */
    .simplebar-content-wrapper {
        background: transparent !important;
    }

    /* Mobile Sidebar Dark Mode Fix */
    html[data-bs-theme="dark"] body.sidebar-enable .leftside-menu,
    html[data-layout-mode="dark"] body.sidebar-enable .leftside-menu {
        background: rgba(30, 30, 40, 0.9) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
    }
</style>