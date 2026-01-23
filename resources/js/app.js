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
});

function initSidebarToggle() {
  const html = document.documentElement;
  const body = document.body;
  const toggleBtn = document.querySelector(".button-toggle-menu");

  if (toggleBtn) {
    toggleBtn.addEventListener("click", function (e) {
      e.preventDefault();

      if (window.innerWidth < 992) {
        // Mobile
        body.classList.toggle("sidebar-enable");
      } else {
        // Desktop
        const currentSize = html.getAttribute("data-sidenav-size");
        const newSize = currentSize === "condensed" ? "default" : "condensed";
        html.setAttribute("data-sidenav-size", newSize);
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
