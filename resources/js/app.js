import "./bootstrap";

import jQuery from "jquery";
if (!window.jQuery) {
  window.$ = window.jQuery = jQuery;
}

import select2 from "select2";
select2();

import flatpickr from "flatpickr";
import Choices from "choices.js";
import Swal from "sweetalert2";
window.Swal = Swal;

// Common Colors
window.CommonColors = {
  primary: "#144BB8",
  edit: "#14B82E",
  delete: "#B86314",
  other1: "#8114B8",
  other2: "#B8A814",
};

// Initialize global plugins
import "./global-submit";

// Import Bootstrap Bundle (includes Popper)
import * as bootstrap from "bootstrap/dist/js/bootstrap.bundle.min.js";
window.bootstrap = bootstrap;

document.addEventListener("DOMContentLoaded", function () {
  // Initialize Bootstrap Tooltips
  const tooltipTriggerList = document.querySelectorAll(
    '[data-bs-toggle="tooltip"], [data-bs-custom-class="shadcn-tooltip"]',
  );
  const tooltipList = [...tooltipTriggerList].map((tooltipTriggerEl) => {
    if (!bootstrap.Tooltip.getInstance(tooltipTriggerEl)) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    }
  });

  // Initialize Flatpickr
  flatpickr(".datepicker", {
    dateFormat: "Y-m-d",
  });

  // Initialize Choices.js
  const selectElements = document.querySelectorAll(".choices-select");
  selectElements.forEach(function (element) {
    new Choices(element, {
      itemSelectText: "",
      searchEnabled: true,
    });
  });

  // Sidebar Toggle Logic
  initSidebarToggle();
});

// function initDynamicSidebarWidth() {
//   // logic removed to allow CSS to handle transitions smoothly
// }

function initSidebarToggle() {
  const html = document.documentElement;
  const body = document.body;
  const toggleBtn = document.querySelector(".button-toggle-menu");
  const sidebar = document.querySelector(".leftside-menu");

  // Toggle Button Click
  if (toggleBtn) {
    toggleBtn.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation(); // Prevent bubbling to sidebar click listener

      if (window.innerWidth < 992) {
        // Mobile
        body.classList.toggle("sidebar-enable");
      } else {
        // Desktop
        const currentSize = html.getAttribute("data-sidenav-size");
        const newSize = currentSize === "condensed" ? "default" : "condensed";
        html.setAttribute("data-sidenav-size", newSize);
        // Force width update (handled by observer/listeners, but good to be explicit safely)
      }
    });
  }

  // Sidebar Click - Expand when clicked (only in condensed mode & desktop)
  if (sidebar) {
    sidebar.addEventListener("click", function (e) {
      if (window.innerWidth >= 992) {
        const currentSize = html.getAttribute("data-sidenav-size");
        if (currentSize === "condensed") {
          html.setAttribute("data-sidenav-size", "default");
        }
      }
    });

    // Ensure links trigger expansion
    const links = sidebar.querySelectorAll(".side-nav-link");
    links.forEach((link) => {
      link.addEventListener("click", function (e) {
        if (
          window.innerWidth >= 992 &&
          html.getAttribute("data-sidenav-size") === "condensed"
        ) {
          html.setAttribute("data-sidenav-size", "default");
          // Note: We allow the default action (Bootstrap collapse or navigation) to proceed
          // e.stopPropagation(); // Do not stop propagation or preventing default unless desired
        }
      });
    });
  }

  // Close on click outside (Mobile)
  document.addEventListener("click", function (e) {
    if (window.innerWidth < 992 && body.classList.contains("sidebar-enable")) {
      const sidebar = document.querySelector(".leftside-menu");
      const toggle = document.querySelector(".button-toggle-menu");

      if (
        sidebar &&
        !sidebar.contains(e.target) &&
        !toggle.contains(e.target)
      ) {
        body.classList.remove("sidebar-enable");
      }
    }
  });
}
