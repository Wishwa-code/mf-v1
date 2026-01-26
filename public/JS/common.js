/**
 * Common AJAX Form Submission
 *
 * @param {string} formSelector - CSS selector for the form
 * @param {function} successCallback - Callback function on success (optional)
 * @param {function} errorCallback - Callback function on error (optional)
 */
function handleAjaxFormSubmission(
  formSelector,
  successCallback = null,
  errorCallback = null,
) {
  $(document).on("submit", formSelector, function (e) {
    e.preventDefault();

    let form = $(this);
    let formData = new FormData(this);
    let submitBtn = form.find('button[type="submit"]');
    let originalBtnText = submitBtn.text();

    // Clear previous error messages
    $(".error-msg").remove();
    $(".is-invalid").removeClass("is-invalid");

    // Disable button to prevent double submission
    submitBtn.prop("disabled", true).text("Processing...");

    $.ajax({
      url: form.attr("action"),
      method: form.attr("method"),
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        submitBtn.prop("disabled", false).text(originalBtnText);

        if (response.status === "success" || response.success) {
          Swal.fire({
            icon: "success",
            title: "Success!",
            text: response.message || "Operation successful.",
            timer: 2000,
            showConfirmButton: false,
          }).then(() => {
            if (successCallback) {
              successCallback(response);
            } else if (response.redirect_url) {
              window.location.href = response.redirect_url;
            } else {
              // Default behavior: reload or redirect to generic listing if applicable
              location.reload();
            }
          });
        } else {
          // Handle logical errors that returned 200 OK but with success: false
          Swal.fire({
            icon: "error",
            title: "Error!",
            text: response.message || "Something went wrong.",
          });
        }
      },
      error: function (xhr) {
        submitBtn.prop("disabled", false);

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

            let inputField = form.find('[name="' + inputName + '"]');
            if (inputField.length > 0) {
              inputField.addClass("is-invalid");
              // inputs are often wrapped in col-md-*, append after the input container or directly after input
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
        } 

        if (errorCallback) {
          errorCallback(xhr);
        }
      },
    });
  });
}
