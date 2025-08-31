$(function () {
  $("#company_name").focus().select();
  //enter key press event
  $(document).keypress(function (e) {
    if (e.which === 13) {
        e.preventDefault();

      if(e.target.id === "company_name") {
          if (validateRequired("company_name")) {
              $("#address").focus().select();
          }
      }else if (e.target.id === "address") {
        if (validateRequired("address")) {
          $("#con").focus().select();
        }
      } else if (e.target.id === "con") {
          if (validateRequired("con")) {
              validateSubmitProfile();
          }
      }
    }
  });
});

const validateSubmitProfile = (event) => {
  event.preventDefault();

  let err = 0;

  let arr = ["company_name", "address", "con"];

  err = check_validate(arr, err);

  if (err == 0) {
      saveProfile(event);
  } else {
      Swal.fire("Error!", "Please fill the required fields !", "error");
    return false;
  }
};

let CUSTOMER_FMT_BASELINE = null;

// Read current state safely (works even if elements don’t exist yet)
function getCustomerFormatState() {
    return {
        sep:  $('#separate_from').val() ?? null,
        raw:  $('#field_output_customer').val() ?? '',
        pretty: $('#field_output_customer_2').val() ?? '',
        auto: $('#auto_number').val() ?? ''
    };
}

// Compare current vs baseline
function hasCustomerFormatChanged() {
    if (!CUSTOMER_FMT_BASELINE) return false; // nothing to compare
    const now = getCustomerFormatState();
    return (
        (CUSTOMER_FMT_BASELINE.sep ?? null)   !== (now.sep ?? null)   ||
        (CUSTOMER_FMT_BASELINE.raw ?? '')     !== (now.raw ?? '')     ||
        (CUSTOMER_FMT_BASELINE.pretty ?? '')  !== (now.pretty ?? '')  ||
        (CUSTOMER_FMT_BASELINE.auto ?? '')    !== (now.auto ?? '')
    );
}

// Capture baseline once the section is first rendered
(function captureWhenReady(){
    const target = document.getElementById('format_section_customer');
    if (!target) return; // safety
    if (target.children.length) {
        CUSTOMER_FMT_BASELINE = getCustomerFormatState();
        return;
    }
    const obs = new MutationObserver(() => {
        if (target.children.length) {
            CUSTOMER_FMT_BASELINE = getCustomerFormatState();
            obs.disconnect();
        }
    });
    obs.observe(target, { childList: true });
})();


const saveProfile = (e) => {
    e.preventDefault();

    const formData = new FormData();
    formData.append("company_name", $("#company_name").val());
    formData.append("address", $("#address").val());
    formData.append("branch", $("#branch").val());
    formData.append("con", $("#con").val());
    // NOTE: we'll set customer_format_scope later right before upload

    let customer_format_selection = $("#customer_format_selection").val();
    formData.append("customer_format_selection", customer_format_selection);
    formData.append("separate_from", $("#separate_from").val());
    formData.append("field_output_customer", $("#field_output_customer").val());
    formData.append("auto_number", $("#auto_number").val());
    if (customer_format_selection === "Customize") {
        formData.set("separate_from", "");
        formData.set("field_output_customer", "");
        formData.set("auto_number", "");
    }

    let loan_format_selection = $("#loan_format_selection").val();
    formData.append("loan_format_selection", loan_format_selection);
    formData.append("separate_from_loan", $("#separate_from_loan").val());
    formData.append("field_output_loan", $("#field_output_loan").val());
    if (loan_format_selection === "Customize") {
        formData.set("separate_from_loan", "");
        formData.set("field_output_loan", "");
    }

    let saving_selection = $("#saving_selection").val();
    formData.append("saving_selection", saving_selection);
    formData.append("separate_from_savings", $("#separate_from_savings").val());
    formData.append("field_output_saving", $("#field_output_saving").val());
    if (saving_selection === "Customize") {
        formData.set("separate_from_savings", "");
        formData.set("field_output_saving", "");
    }

    let inv_loan_format_selection = $("#inv_loan_format_selection").val();
    formData.append("inv_loan_format_selection", inv_loan_format_selection);
    formData.append("separate_from_inv_loan", $("#separate_from_inv_loan").val());
    formData.append("field_output_inv_loan", $("#field_output_inv_loan").val());
    if (inv_loan_format_selection === "Customize") {
        formData.set("separate_from_inv_loan", "");
        formData.set("field_output_inv_loan", "");
    }

    // Files (safe if empty)
    if ($("#profile_image")[0]?.files?.[0]) formData.append("logo", $("#profile_image")[0].files[0]);
    formData.append("activatePoints", $("#activatePoints").prop("checked") ? 1 : 0);
    formData.append("pointsPercentage", $("#pointsPercentage").val());
    if ($("#company_header")[0]?.files?.[0]) formData.append("company_header", $("#company_header")[0].files[0]);
    if ($("#company_footer")[0]?.files?.[0]) formData.append("company_footer", $("#company_footer")[0].files[0]);


    const confirmAndUpload = () => {
        // ensure the (maybe newly chosen) scope is in the form data
        formData.set("customer_format_scope", $("#customer_format_scope").val() || "");

        // Progress modal (bar + % + elapsed; real upload progress)
        Swal.fire({
            title: "Generating Customer Number Formats…",
            html: `
    <div id="upload-status" style="margin-bottom:8px;font-size:14px;">Connecting…</div>
    <div style="width:100%; background:#eee; border-radius:8px; overflow:hidden; height:14px;">
      <div id="upload-progress" style="width:0%; height:100%; background:#3085d6; transition:width .2s ease;"></div>
    </div>
    <div style="display:flex; justify-content:space-between; margin-top:8px; font-size:12px;" hidden>
      <div>Progress: <b><span id="upload-percent">0</span>%</b></div>
      <div>Elapsed: <b><span id="elapsed">00:00</span></b></div>
    </div>
    <div id="server-phase" style="display:none; margin-top:8px; font-size:12px;">
       Elapsed: <b><span id="server-elapsed">00:00</span></b>
    </div>
  `,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                const el         = Swal.getHtmlContainer();
                const statusEl   = el.querySelector('#upload-status');
                const barEl      = el.querySelector('#upload-progress');
                const pctEl      = el.querySelector('#upload-percent');
                const elapsedEl  = el.querySelector('#elapsed');
                const serverBox  = el.querySelector('#server-phase');
                const serverEl   = el.querySelector('#server-elapsed');

                const two = n => String(n).padStart(2,'0');
                const fmt = ms => {
                    const s = Math.floor(ms/1000), m = Math.floor(s/60), ss = s%60, mm = m%60, hh = Math.floor(m/60);
                    return hh ? `${two(hh)}:${two(mm)}:${two(ss)}` : `${two(mm)}:${two(ss)}`;
                };

                const start = performance.now();
                let serverStart = null;
                const tick = setInterval(() => {
                    const now = performance.now();
                    elapsedEl.textContent = fmt(now - start);
                    if (serverStart) serverEl.textContent = fmt(now - serverStart);
                }, 250);

                $.ajax({
                    type: "POST",
                    url: "/company-profile",
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                    data: formData,
                    contentType: false,
                    processData: false,
                    xhr: function () {
                        const xhr = new window.XMLHttpRequest();

                        // >>> REAL upload progress <<<
                        xhr.upload.addEventListener("progress", function (e) {
                            if (!e.lengthComputable) return;
                            const percent = Math.round((e.loaded / e.total) * 100);
                            if (barEl) barEl.style.width = percent + "%";
                            if (pctEl) pctEl.textContent = percent;
                            if (statusEl) statusEl.textContent = percent < 100 ? "Generating Customer Number Formats…" : "Please wait until process complete…";

                            if (percent >= 100 && !serverStart) {
                                serverStart = performance.now();
                                if (serverBox) serverBox.style.display = "block";
                            }
                        });

                        xhr.addEventListener("load", function () {
                            // ensure the bar is full when upload finishes
                            if (barEl) barEl.style.width = "100%";
                            if (pctEl) pctEl.textContent = 100;
                            if (!serverStart) {
                                serverStart = performance.now();
                                if (serverBox) serverBox.style.display = "block";
                            }
                        });

                        xhr.addEventListener("loadend", function () {
                            clearInterval(tick);
                        });

                        return xhr;
                    },
                    success: function (data, textStatus, xhr) {
                        Swal.close();
                        if (xhr.status === 200) {
                            if (data.id === "0") {
                                Swal.fire("Error!", "This customer number already exists!", "error");
                            } else {
                                Swal.fire({ position: "center", icon: "success", title: "Successfully saved!" })
                                    .then(() => window.location.reload());
                            }
                        } else {
                            Swal.fire("Error!", "Failed to save data!", "error");
                        }
                    },
                    error: function () {
                        Swal.close();
                        Swal.fire("Error!", "Failed to save data!", "error");
                    },
                });
            },
        });

    };

    // ---- if the customer format section CHANGED and no scope chosen yet, ask now
    if (hasCustomerFormatChanged() && !$("#customer_format_scope").val()) {
        Swal.fire({
            title: 'Apply number-format change?',
            icon: 'warning',
            input: 'select',
            inputOptions: {
                all: 'Change all existing customer numbers (may take time)',
                future: 'Change only future customer numbers'
            },
            inputPlaceholder: 'Select scope…',
            inputValidator: v => !v && 'Please choose one option',
            showCancelButton: true,
            confirmButtonText: 'Apply',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false
        }).then((res) => {
            if (res.isConfirmed) {
                $("#customer_format_scope").val(res.value);
                confirmAndUpload();
            }
            // if cancelled: do nothing
        });
    } else {
        // unchanged or scope already chosen → proceed directly
        confirmAndUpload();
    }
};






