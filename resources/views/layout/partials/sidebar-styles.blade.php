<style>
    /* Modern Sidebar Styles */
    :root {
        --sidebar-width: 350px;
        /* Increased from 320px */
        --sidebar-bg: rgba(49, 58, 70, 0.3);
        /* Default Fallback */
        --sidebar-active-bg: #6a5e87;
        /* Solid Purple */
        --sidebar-hover-bg: rgba(255, 255, 255, 0.1);
        --sidebar-text: #ced4da;
        --sidebar-text-active: #ffffff;
        --sidebar-border: rgba(255, 255, 255, 0.1);
    }

    /* Light Mode */
    html[data-layout-mode="light"] .leftside-menu {
        --sidebar-bg: rgba(255, 255, 255, 0.9);
        --sidebar-text: #313a46;
        --sidebar-hover-bg: rgba(0, 0, 0, 0.05);
        border-right: 1px solid rgba(0, 0, 0, 0.1);
    }

    /* Dark Mode */
    html[data-layout-mode="dark"] .leftside-menu,
    html[data-layout-mode="detached"] .leftside-menu {
        /* Glass Effect with Gradient */
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.02) 100%) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        --sidebar-text: #ced4da;
        --sidebar-hover-bg: rgba(255, 255, 255, 0.1);
        border-right: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 5px 0 25px rgba(0, 0, 0, 0.2);
    }

    .leftside-menu {
        width: var(--sidebar-width) !important;
        background: var(--sidebar-bg);
        /* Fallback for light mode uses var */
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.1);
        border-right: 1px solid var(--sidebar-border);
        transition: all 0.3s ease;
        z-index: 1005 !important;
        /* Ensure it stays above content/navbar overlap if any */
        position: fixed;
        /* Ensure it's fixed */
        top: 0;
        bottom: 0;
        left: 0;
    }

    /* Adjust Content Page Margin */
    .content-page {
        margin-left: var(--sidebar-width) !important;
        transition: margin-left 0.3s ease;
    }

    #leftside-menu-container {
        padding-top: 1rem;
    }

    /* Side Nav Title */
    .side-nav-title {
        padding: 12px 24px;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--sidebar-text) !important;
        opacity: 0.7;
        font-weight: 600;
        margin-top: 10px;
    }

    /* Side Nav Item */
    .side-nav-item {
        margin: 4px 12px;
    }

    /* Side Nav Link */
    .side-nav-link {
        display: flex;
        align-items: center;
        justify-content: flex-start !important;
        padding: 12px 16px !important;
        color: var(--sidebar-text) !important;
        font-size: 0.92rem !important;
        border-radius: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .side-nav-link:hover {
        background: var(--sidebar-hover-bg);
        color: var(--sidebar-text) !important;
        transform: translateX(4px);
    }

    /* Active Item */
    .side-nav-item.menuitem-active>.side-nav-link,
    .side-nav-link[aria-expanded="true"],
    .side-nav-link:active,
    .side-nav-link.active {
        background: var(--sidebar-active-bg) !important;
        color: var(--sidebar-text-active) !important;
        box-shadow: 0 4px 12px rgba(106, 94, 135, 0.4);
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
    }

    .side-nav-second-level li.menuitem-active>a,
    .side-nav-third-level li.menuitem-active>a {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.1);
        font-weight: 500;
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

    /* Condensed Sidebar Styles */
    html[data-sidenav-size="condensed"] .leftside-menu {
        width: 70px !important;
    }

    html[data-sidenav-size="condensed"] .content-page {
        margin-left: 70px !important;
    }

    html[data-sidenav-size="condensed"] .side-nav-link {
        justify-content: center !important;
        padding: 12px 0 !important;
    }

    html[data-sidenav-size="condensed"] .side-nav-link span:not(.menu-arrow),
    html[data-sidenav-size="condensed"] .menu-arrow,
    html[data-sidenav-size="condensed"] .side-nav-title,
    html[data-sidenav-size="condensed"] .logo-box {
        display: none !important;
    }

    html[data-sidenav-size="condensed"] .side-nav-link i {
        margin-right: 0 !important;
        font-size: 1.4rem;
    }

    html[data-sidenav-size="condensed"] .side-nav-item {
        margin: 4px 0;
        /* Tighter margins for condensed */
    }

    /* Logo Alignment */
    .logo {
        width: 100% !important;
        text-align: center;
        display: block;
    }

    /* Responsive Styles & Mobile Toggle */
    @media (max-width: 1024px) {
        .leftside-menu {
            transform: translateX(-100%);
            box-shadow: none;
            z-index: 1040;
        }

        .content-page {
            margin-left: 0 !important;
        }

        /* When sidebar is enabled via body class */
        body.sidebar-enable .leftside-menu {
            transform: translateX(0);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.3);
            width: var(--sidebar-width) !important;
            /* Full width on mobile */
        }
    }
</style>