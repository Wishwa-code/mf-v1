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

document.addEventListener("DOMContentLoaded", function () {
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

  // Dynamic Sidebar Width Logic
  initDynamicSidebarWidth();
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
        // Check if we are in condensed mode and the click is NOT on a link (to avoid re-expanding when navigating, though expanding is safer)
        // Actually, user wants "click need to expand".
        // It's better to just check if condensed.
        if (currentSize === "condensed") {
          html.setAttribute("data-sidenav-size", "default");
        }
      }
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
