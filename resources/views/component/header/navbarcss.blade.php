 <style>
     .navbar-custom,
     .topbar {
         overflow: visible !important;
     }

     .modern-branch-switcher .modern-dropdown-toggle {
         background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
         border: none;
         color: white;
         border-radius: 12px;
         padding: 10px 20px;
         font-weight: 600;
         box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
         transition: all 0.3s ease;
         display: flex;
         align-items: center;
         gap: 8px;
     }

     .modern-branch-switcher .modern-dropdown-toggle:hover,
     .modern-branch-switcher .modern-dropdown-toggle:focus {
         background: linear-gradient(135deg, #4f46e5 0%, #9333ea 100%);
         transform: translateY(-1px);
         box-shadow: 0 8px 12px -1px rgba(99, 102, 241, 0.3);
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
         font-size: 1.5rem;
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
         color: #6366f1;
         background: #eef2ff;
         border-color: #e0e7ff;
     }

     .modern-action-btn[title="Toggle Theme"]:hover {
         background: #6366f1;
         color: white;
         border-color: #6366f1;
         box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2);
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
         background: linear-gradient(to right, #6A82FB, #FC5C7D);
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
         background: linear-gradient(to right, #6A82FB, #FC5C7D);

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