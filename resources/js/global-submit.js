import Swal from "sweetalert2";
import $ from "jquery";

document.addEventListener("DOMContentLoaded", function () {
  /**
   * Global Form Submit with Confirmation
   * Add class 'js-confirm-submit' to any form to trigger SweetAlert confirmation.
   * Optional Attributes:
   * - data-title: Title of the modal
   * - data-text: Description text
   * - data-icon: 'warning', 'info', 'question', etc.
   * - data-confirm-btn: Button text
   * - data-ajax: 'true' (if you want AJAX submission instead of standard)
   */
  $(document).on("submit", ".js-confirm-submit", function (e) {
    e.preventDefault();
    let form = this;
    let $form = $(this);

    let title = $form.data("title") || "Are you sure?";
    let text = $form.data("text") || "This action cannot be undone.";
    let icon = $form.data("icon") || "warning";
    let confirmButtonText = $form.data("confirm-btn") || "Yes, proceed!";
    let confirmButtonColor =
      $form.data("confirm-color") ||
      window.CommonColors?.primary ||
      "var(--bs-primary)";
    let cancelButtonColor = "#6c757d";

    Swal.fire({
      title: title,
      text: text,
      icon: icon,
      showCancelButton: true,
      confirmButtonColor: confirmButtonColor,
      cancelButtonColor: cancelButtonColor,
      confirmButtonText: confirmButtonText,
      customClass: {
        popup: "card-modern",
      },
    }).then((result) => {
      if (result.isConfirmed) {
        if ($form.data("ajax")) {
          // Clear previous error messages
          $(".error-msg").remove();
          $(".is-invalid").removeClass("is-invalid");

          $.ajax({
            url: $form.attr("action"),
            headers: {
              "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            method: $form.attr("method") || "POST",
            data: new FormData(form),
            processData: false,
            contentType: false,
            success: function (response) {
              Swal.fire({
                title: "Success!",
                text: response.message || "Operation successful.",
                icon: "success",
                confirmButtonColor:
                  window.CommonColors?.primary || "var(--bs-primary)",
              }).then(() => {
                if (response.redirect) {
                  window.location.href = response.redirect;
                } else {
                  location.reload();
                }
              });
            },
            error: function (xhr) {
              if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;

                // Display validation errors
                $.each(errors, function (key, value) {
                  // Handle dot notation for arrays (e.g., charges.0.amount -> charges[0][amount])
                  let inputName = key;
                  if (key.includes(".")) {
                    let parts = key.split(".");
                    inputName = parts.shift();
                    $.each(parts, function (index, part) {
                      inputName += "[" + part + "]";
                    });
                  }

                  let inputField = $form.find('[name="' + inputName + '"]');
                  if (inputField.length > 0) {
                    inputField.addClass("is-invalid");
                    // Check if it's inside an input-group
                    if (inputField.parent(".input-group").length > 0) {
                      inputField
                        .parent()
                        .after(
                          '<div class="text-danger small mt-1 error-msg">' +
                            value[0] +
                            "</div>",
                        );
                    } else {
                      inputField.after(
                        '<div class="text-danger small mt-1 error-msg">' +
                          value[0] +
                          "</div>",
                      );
                    }
                  }
                });

                // Close the loading spinner and stop execution so no other alert shows
                Swal.close();
                return;
              }

              let msg = "An error occurred.";
              let title = "Error!";

              if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
              } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                msg = Object.values(xhr.responseJSON.errors)
                  .flat()
                  .join("<br>");
              }

              Swal.fire({
                title: title,
                html: msg,
                icon: "error",
                confirmButtonColor: "#d33",
              });
            },
          });
        } else {
          form.submit();
        }
      }
    });
  });

  /**
   * Delete Link Helper
   * Usage: <a href="javascript:void(0)" class="js-delete-trigger" data-url="/delete/1" data-title="Delete Item?">Delete</a>
   */
  $(document).on("click", ".js-delete-trigger", function (e) {
    e.preventDefault();
    let url = $(this).data("url");
    let title = $(this).data("title") || "Delete this item?";
    let text = $(this).data("text") || "You won't be able to revert this!";

    Swal.fire({
      title: title,
      text: text,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Yes, delete it!",
    }).then((result) => {
      if (result.isConfirmed) {
        let form = $("<form>", {
          action: url,
          method: "POST",
        });
        form.append(
          '<input type="hidden" name="_token" value="' +
            $('meta[name="csrf-token"]').attr("content") +
            '" />',
        );
        form.append('<input type="hidden" name="_method" value="DELETE" />');
        $("body").append(form);
        form.submit();
      }
    });
  });
});
