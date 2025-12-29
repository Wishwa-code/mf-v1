<style>
    /* Modern Sidebar Styles */
    :root {
        --sidebar-width: 300px;
        --sidebar-bg: linear-gradient(180deg, #1a1f35 0%, #252b46 100%);
        --sidebar-active-bg: linear-gradient(90deg, rgba(118, 75, 162, 0.9) 0%, rgba(102, 126, 234, 0.9) 100%);
        --sidebar-hover-bg: rgba(255, 255, 255, 0.08);
        --sidebar-text: #a0aec0;
        --sidebar-text-active: #ffffff;
        --sidebar-border: rgba(255, 255, 255, 0.05);
    }

    .leftside-menu {
        background: var(--sidebar-bg) !important;
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
        border-right: 1px solid var(--sidebar-border);
        transition: all 0.3s ease;
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
        color: rgba(255, 255, 255, 0.4) !important;
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
        color: #ffffff !important;
        transform: translateX(4px);
    }

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

    /* Active State */
    .side-nav-item.menuitem-active>.side-nav-link,
    .side-nav-link[aria-expanded="true"],
    .side-nav-link:active,
    .side-nav-link.active {
        background: var(--sidebar-active-bg) !important;
        color: var(--sidebar-text-active) !important;
        box-shadow: 0 4px 12px rgba(118, 75, 162, 0.3);
    }

    .side-nav-item.menuitem-active>.side-nav-link i,
    .side-nav-link.active i {
        color: #fff !important;
        opacity: 1;
    }

    /* Submenu */
    .side-nav-second-level,
    .side-nav-third-level {
        padding-left: 28px !important;
        margin-top: 4px;
    }

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
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.05);
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
        background: rgba(255, 255, 255, 0.2);
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
        margin: 4px 8px;
    }

    /* Responsive Styles */
    @media (max-width: 1024px) {
        .leftside-menu {
            transform: translateX(-100%);
            box-shadow: none;
            z-index: 1040;
        }

        /* When sidebar is enabled based on common template classes */
        body.sidebar-enable .leftside-menu {
            transform: translateX(0);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.3);
        }
    }
</style>