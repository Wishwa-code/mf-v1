<style>
    /* Modern Sidebar Styles */
    :root {
        --sidebar-width: 300px;
        /* Increased from 260px */
        --sidebar-bg: #1e293b;
        /* Darker slate for premium feel */
        --sidebar-active-bg: linear-gradient(135deg, #4f46e5 0%, #ec4899 100%);
        /* Modern Indigo-Pink gradient */
        --sidebar-hover-bg: rgba(255, 255, 255, 0.05);
        --sidebar-text: #94a3b8;
        --sidebar-text-active: #ffffff;
        --sidebar-border: rgba(255, 255, 255, 0.05);
        --sidebar-border: rgba(255, 255, 255, 0.05);
        --sidebar-font: 'Inter', sans-serif;
    }

    /* Body Background for Card Contrast */
    body {
        background-color: #f3f4f6 !important;
        /* Light gray/silvery */
    }

    /* Apply Font */
    .leftside-menu,
    .side-nav-link {
        font-family: var(--sidebar-font) !important;
    }

    /* Light Mode */
    html[data-layout-mode="light"] .leftside-menu {
        --sidebar-bg: #ffffff;
        --sidebar-text: #64748b;
        --sidebar-hover-bg: #f1f5f9;
        border-right: 1px solid #e2e8f0;
    }

    /* Dark Mode */
    html[data-layout-mode="dark"] .leftside-menu,
    html[data-bs-theme="dark"] .leftside-menu {
        background: rgba(30, 41, 59, 0.95) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        --sidebar-text: #cbd5e1;
        --sidebar-hover-bg: rgba(255, 255, 255, 0.05);
        border-right: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 10px 0 30px rgba(0, 0, 0, 0.1);
    }

    /* General Sidebar Styling */
    /* General Sidebar Styling - Floating & Reduced Height */
    .leftside-menu {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        width: var(--sidebar-width) !important;
        min-width: unset;
        max-width: 450px;
        z-index: 1005 !important;
        position: fixed !important;
        top: 90px !important;
        bottom: 60px !important;
        left: 20px !important;
        right: auto !important;
        height: auto !important;
        /* overflow-y: auto !important; - Moved to container */
        scrollbar-width: none;
        /* Firefox */
        -ms-overflow-style: none;
        /* IE/Edge */

        margin-top: 15px !important;
        margin-bottom: 30px !important;
        border-radius: 24px !important;
        /* padding-top: 20px; removed to let content scroll */
        padding-bottom: 0px !important;
        /* Reset padding for footer */
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        /* Ensure footer pushes down */
        align-items: stretch !important;
        /* Stretch children */
        gap: 0px !important;
        box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.05), -2px -2px 10px rgba(255, 255, 255, 0.5) !important;
        border: 1px solid rgba(255, 255, 255, 0.6);
        background: #f8fafc !important;
        /* overflow: hidden !important;  Removed to allow tooltips to popup */
    }

    #leftside-menu-container {
        flex-grow: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding-top: 20px;
        scrollbar-width: none;
    }

    #leftside-menu-container::-webkit-scrollbar {
        display: none;
    }

    /* Rounded corners for footer since parent overflow is visible */
    .sidebar-footer {
        border-bottom-left-radius: 24px;
        border-bottom-right-radius: 24px;
        background: inherit;
        /* Blend with sidebar */
    }

    .leftside-menu::-webkit-scrollbar {
        display: none;
    }

    /* Toggle Button in Sidebar */
    .sidebar-toggle-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: #fff;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        z-index: 10;
    }

    .sidebar-toggle-btn:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    /* Ensure text stays on one line to force width expansion */
    .side-nav-link span:not(.menu-arrow) {
        white-space: nowrap;
        overflow: visible !important;
        text-overflow: clip !important;
        flex-grow: 1;
        margin-right: 8px;
    }

    /* Modern Glassy/Gradient Active State */
    .side-nav-item.menuitem-active>.side-nav-link,
    .side-nav-link[aria-expanded="true"],
    .side-nav-link.active {
        background: var(--sidebar-active-bg) !important;
        color: var(--sidebar-text-active) !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        /* Soft shadow matching gradient */
        border-radius: 8px;
        /* Softer edges */
        margin: 0 10px;
        /* Inset look */
        width: auto;
    }

    /* Hover State */
    .side-nav-link {
        border-radius: 8px;
        margin: 2px 10px;
        width: auto;
        transition: all 0.2s ease;
        padding: 10px 15px !important;
    }

    .side-nav-link:hover {
        background: var(--sidebar-hover-bg);
        color: var(--sidebar-text-active) !important;
        transform: translateX(4px);
    }

    /* Icon Styles */
    .side-nav-link i {
        font-size: 1.25rem;
        margin-right: 12px;
        opacity: 0.7;
        transition: all 0.3s ease;
    }

    .side-nav-link:hover i,
    .side-nav-item.menuitem-active>.side-nav-link i,
    .side-nav-link.active i {
        color: inherit !important;
        opacity: 1;
        transform: scale(1.1);
    }

    /* Light Mode Adjustments */
    html[data-layout-mode="light"] .side-nav-link:hover {
        background: var(--sidebar-hover-bg) !important;
        color: #0f172a !important;
        /* Dark text for contrast */
    }

    html[data-layout-mode="light"] .side-nav-link:hover i {
        color: #0f172a !important;
    }

    html[data-layout-mode="light"] .side-nav-item.menuitem-active>.side-nav-link,
    html[data-layout-mode="light"] .side-nav-link.active {
        background: var(--sidebar-active-bg) !important;
        color: #ffffff !important;
    }

    html[data-layout-mode="light"] .side-nav-item.menuitem-active>.side-nav-link i,
    html[data-layout-mode="light"] .side-nav-link.active i {
        color: #ffffff !important;
    }

    /* Submenus */
    .side-nav-second-level li a,
    .side-nav-third-level li a {
        color: var(--sidebar-text) !important;
        padding: 8px 10px 8px 30px !important;
        font-size: 0.85rem !important;
        transition: all 0.2s ease;
        border-radius: 6px;
        margin: 2px 15px;
        display: block;
    }

    .side-nav-second-level li a:hover {
        color: #fff !important;
        background: rgba(255, 255, 255, 0.08);
        /* Subtle highlight */
        padding-left: 35px !important;
    }

    html[data-layout-mode="light"] .side-nav-second-level li a:hover {
        color: #0f172a !important;
        background: rgba(0, 0, 0, 0.05);
    }

    .side-nav-second-level li.menuitem-active>a {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.1);
        font-weight: 500;
    }

    html[data-layout-mode="light"] .side-nav-second-level li.menuitem-active>a {
        color: #4f46e5 !important;
        /* Primary color text */
        background: rgba(79, 70, 229, 0.1) !important;
    }


    /* Condensed Mode - The main implementation */
    html[data-sidenav-size="condensed"] {
        --sidebar-width: 80px;
        /* Slightly wider for better touch target */
    }

    html[data-sidenav-size="condensed"] .leftside-menu {
        width: var(--sidebar-width) !important;
        padding-top: 20px;
        /* Keep floating style in condensed mode too, width adapts */
    }

    /* Flexbox centering for icons */
    html[data-sidenav-size="condensed"] .side-nav-item {
        display: flex;
        justify-content: center;
        width: 100%;
        margin-bottom: 4px;
    }

    html[data-sidenav-size="condensed"] .side-nav-link {
        display: flex;
        /* Ensure flexbox is used */
        justify-content: center !important;
        align-items: center !important;
        width: 48px;
        height: 48px;
        border-radius: 12px;
        padding: 0 !important;
        /* Reset padding */
        margin: 0 auto;
        position: relative;
    }

    /* Hide text & arrows in condensed mode AND normal mode arrows */
    .menu-arrow {
        display: none !important;
        /* Always hide arrows as requested */
    }

    html[data-sidenav-size="condensed"] .side-nav-link span:not(.menu-arrow),
    html[data-sidenav-size="condensed"] .side-nav-title,
    html[data-sidenav-size="condensed"] .logo-text,
    html[data-sidenav-size="condensed"] .logo-text-section,
    html[data-sidenav-size="condensed"] .collapse {
        display: none !important;
    }

    /* Icon styling in condensed mode */
    html[data-sidenav-size="condensed"] .side-nav-link i {
        margin: 0 !important;
        font-size: 1.4rem;
    }

    /* Tooltip on Hover */
    /* Tooltip on Hover - Modern Floating Pill */
    html[data-sidenav-size="condensed"] .side-nav-link::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 70px;
        /* Slight offset for float effect */
        top: 50%;
        transform: translateY(-50%) translateX(-10px);
        /* Start slightly left */

        background: #1e293b;
        /* Dark Slate */
        color: #fff;
        padding: 8px 16px;
        border-radius: 8px;
        /* Rounded pill */
        font-weight: 500;
        font-size: 0.85rem;
        white-space: nowrap;

        opacity: 0;
        visibility: hidden;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 4px 4px 12px rgba(0, 0, 0, 0.15);
        /* Drop prominent shadow */
        z-index: 9999;
        pointer-events: none;

        /* Arrow tip (optional, can simulate with border or clipart) - keeping simple pill for now */
    }

    html[data-sidenav-size="condensed"] .side-nav-link:hover::after {
        opacity: 1;
        visibility: visible;
        transform: translateY(-50%) translateX(0);
        /* Slide in */
    }

    /* Icon Hover Effect in Condensed Mode */
    html[data-sidenav-size="condensed"] .side-nav-link:hover i {
        transform: scale(1.2);
        /* Scale up */
        color: #4f46e5 !important;
        /* Brand color */
        filter: drop-shadow(0 0 5px rgba(79, 70, 229, 0.3));
        /* Glow */
    }

    /* Clean up native title display if any leftovers */

    html[data-sidenav-size="condensed"] .side-nav-link:hover::after {
        opacity: 1;
        visibility: visible;
        transform: translateY(-50%) translateX(0);
    }

    /* Floating Submenu for Condensed Mode */
    html[data-sidenav-size="condensed"] .side-nav-item {
        position: relative;
        /* Anchor for floating menu */
    }

    html[data-sidenav-size="condensed"] .side-nav-item:hover>.collapse,
    html[data-sidenav-size="condensed"] .side-nav-item:hover>.collapsing {
        display: block !important;
        position: absolute;
        left: calc(var(--sidebar-width) + 15px);

        html[data-sidenav-size="condensed"] .menuitem-active .side-nav-link i {
            color: #7c3aed !important;
        }

        display: block;
        color: #64748b;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Floating Menu Items */
    html[data-sidenav-size="condensed"] .side-nav-second-level {
        padding-left: 0;
        list-style: none;
    }

    html[data-sidenav-size="condensed"] .side-nav-second-level li a {
        padding: 8px 20px;
        display: block;
        color: #64748b;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s;
    }

    html[data-sidenav-size="condensed"] .side-nav-second-level li a:hover {
        color: #4f46e5;
        background: rgba(79, 70, 229, 0.05);
        padding-left: 25px;
        /* Slide effect */
    }

    /* Hide the simple tooltip if submenu is shown? 
       Actually, keep it for items without submenu, but maybe hide for items WITH submenu.
       Hard to distinguish with CSS only. Let's keep both or let submenu cover.
    */

    /* Logo handling */
    html[data-sidenav-size="condensed"] .logo-box {
        display: flex;
        justify-content: center;
        padding: 10px 0;
    }

    html[data-sidenav-size="condensed"] .logo-img {
        height: 32px;
        /* Adjust as needed */
        width: auto;
    }

    /* Tooltip on Hover */
    html[data-sidenav-size="condensed"] .side-nav-link::after {
        /* ... existing tooltip ... */
    }

    /* Navbar adjustment - Variable Width Mode (Legacy) -> Full Width Mode (New) */
    /* Navbar adjustment - Variable Width Mode (Legacy) -> Full Width Mode (New) */
    /* Navbar adjustment for Content Card Header - Glass Mode */
    /* Navbar adjustment for Content Card Header - Modern Glass Mode */
    .navbar-custom {
        position: absolute !important;
        /* Stuck to top of content card */
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        margin: 0 !important;

        min-height: 80px;
        /* Taller for modern feel */
        padding: 0 30px;
        /* More horizontal breathing room */
        display: flex;
        align-items: center;
        /* Vertically center content */

        border-radius: 20px 20px 0 0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03) !important;
        /* Subtle shadow */
        border-bottom: 1px solid rgba(255, 255, 255, 0.5);
        /* Crisp glass edge */
        z-index: 1004 !important;

        /* Glass Effect */
        background: #ffffff !important;
        /* User requested "added" look - solid white is cleanest */
        /* backdrop-filter: blur(16px) !important; */
        /* -webkit-backdrop-filter: blur(16px) !important; */
        opacity: 0.95;
        /* Slight transparency */
    }

    /* Ensure Toggle Button is Visible */
    .button-toggle-menu,
    .navbar-toggle {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        width: 42px;
        /* Slightly larger */
        height: 42px;
        margin-right: 15px;
        /* Spacing */
        border: 1px solid rgba(0, 0, 0, 0.05);
        /* Subtle border */

        background: #ffffff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        /* Floating button look */

        border-radius: 12px;
        /* Rounded square */
        color: #334155;
        cursor: pointer;
        transition: all 0.2s;
        z-index: 1005;
    }

    .button-toggle-menu:hover {
        background: rgba(0, 0, 0, 0.1);
        color: #0f172a;
    }

    .button-toggle-menu i {
        font-size: 1.25rem;
    }

    /* Content push adjustments */
    .content-page {
        margin-left: 0 !important;
        /* Reset, maybe padding? */
        padding-left: var(--sidebar-width) !important;
        /* Use padding for content push so full width elements are easier? No, margin is standard. */
        margin-left: var(--sidebar-width) !important;
    }

    /* Footer adjustments */
    .footer {
        left: 0 !important;
        width: 100% !important;
        margin-left: 0 !important;
        z-index: 1002 !important;
    }

    /* Logo Alignment in Header - Since sidebar is below, we might need a logo in header or stick with sidebar logo? 
       User said "Sidebar between footer and top bar". Usually implies header spans full width. 
       If header spans full width, logo usually goes to header-left. 
       But currently logo is IN the sidebar. 
       Let's keep logo in sidebar for now, effectively "cutting" the header visually if sidebar z-index was higher, but now sidebar is lower.
       Actually, if sidebar is top: 70px, the logo in sidebar is below the header.
       We might need to adjust .logo-box to be visible or moved. 
       For now, let's assume the user wants the sidebar to start *below* the existing header.
    */

    /* Content Card Styling */
    .content-page {
        /* Postioning as the right card */
        margin-left: calc(var(--sidebar-width) + 35px) !important;
        margin-right: 10px;
        margin-top: 90px !important;
        margin-bottom: 60px !important;
        /* If we want space at bottom */

        min-height: calc(100vh - 150px);
        /* 90px top + 60px bottom = 150px */

        background: #ffffff;
        /* White Card Background */
        border-radius: 20px;
        /* Rounded Corners */
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        /* Soft Card Shadow */

        padding-top: 20px !important;
        /* Reset top padding since navbar is fixed and we now have margin */
        padding-left: 20px !important;
        /* Inner padding */
        padding-right: 20px;
        /* Inner padding */

        position: relative;
        /* Context for absolute navbar */
        transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Footer Full Width? Content-page usually contains footer. 
       If content-page has margin-left, footer inside it respects it.
       To make footer full width, it needs to be outside content-page OR negative margin.
       Let's try negative margin on footer if it's inside content-page.
    */
    /* Footer adjustments - Inside Content Card */
    /* Footer adjustments - Fixed at Bottom */
    .footer {
        position: fixed !important;
        /* bottom: 10px !important; */
        right: 10px !important;
        /* left: calc(var(--sidebar-width) + 20px) !important; */
        /* width: auto !important; */
        margin: 0 !important;
        /* border-radius: 0 0 20px 20px; */
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(5px);
        padding: 15px 20px;
        z-index: 1003 !important;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Navbar Animation Utilities */
    .hover-scale {
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .hover-scale:hover {
        transform: scale(1.05);
    }

    .hover-bg-red-50:hover {
        background-color: #fef2f2 !important;
        color: #dc2626 !important;
    }

    /* Centered Dropdown fix */
    .dropdown-menu-center {
        left: 50% !important;
        right: auto !important;
        transform: translateX(-50%) !important;
    }

    /* Mobile overrides */
    @media (max-width: 991.98px) {
        .leftside-menu {
            transition: transform 0.3s ease-in-out;
        }

        /* Disable condensed logic on mobile if needed, or handle differently */
        html[data-sidenav-size="condensed"] .leftside-menu {
            width: 260px !important;
            /* Reset to full width on mobile slide-out */
        }
    }

    /* Icon Animations */
    @keyframes icon-pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.2);
        }

        100% {
            transform: scale(1);
        }
    }

    @keyframes icon-shake {
        0% {
            transform: rotate(0deg);
        }

        25% {
            transform: rotate(-10deg);
        }

        75% {
            transform: rotate(10deg);
        }

        100% {
            transform: rotate(0deg);
        }
    }

    .side-nav-link:hover .icon-pulse {
        animation: icon-pulse 0.6s ease infinite;
        color: #ec4899 !important;
        /* Pink highlight */
    }

    .side-nav-link:hover .icon-shake {
        animation: icon-shake 0.4s ease-in-out;
        color: #ef4444 !important;
        /* Red highlight for logout */
    }
</style>