$(function () {
  $("#cus_number").focus().select();
  //enter key press event
  $(document).keypress(function (e) {
    if (e.which === 13) {
        e.preventDefault();

      if(e.target.id === "cus_number") {
          if (validateRequired("cus_number")) {
              $("#f_name").focus().select();
          }
      }else if (e.target.id === "f_name") {
        if (validateRequired("f_name")) {
          $("#last_name").focus().select();
        }
      } else if (e.target.id === "last_name") {
        if (validateRequired("last_name")) {
          $("#email").focus().select();
        }
      } else if (e.target.id === "email") {
          $("#contact_number").focus().select();
      } else if (e.target.id === "contact_number") {
          if (validateRequired("contact_number")) {
              $("#contact_number_2").focus().select();
          }
      }  else if (e.target.id === "contact_number_2") {
          $("#business_registration").focus().select();
      }   else if (e.target.id === "business_registration") {
          $("#nic").focus().select();
      } else if (e.target.id === "nic") {
          if (validateRequired("nic")) {
              $("#dob").focus().select();
          }
      } else if (e.target.id === "dob") {
          $("#curr_address_01").focus().select();
      } else if (e.target.id === "curr_address_01") {
          $("#curr_address_02").focus().select();
      } else if (e.target.id === "curr_address_02") {
          $("#curr_address_03").focus().select();
      } else if (e.target.id === "curr_address_03") {
          $("#per_address_01").focus().select();
      } else if (e.target.id === "per_address_01") {
          $("#per_address_02").focus().select();
      } else if (e.target.id === "per_address_02") {
          $("#per_address_03").focus().select();
      } else if (e.target.id === "per_address_03") {
          $("#city").focus().select();
      } else if (e.target.id === "city") {
          $("#state").focus().select();
      } else if (e.target.id === "state") {
          $("#landline").focus().select();
      } else if (e.target.id === "landline") {
          $("#longitude").focus().select();
      } else if (e.target.id === "longitude") {
          $("#latitude").focus().select();
      } else if (e.target.id === "latitude") {
          $("#gua_name").focus().select();
      } else if (e.target.id === "gua_name") {
          $("#gua_relation").focus().select();
      } else if (e.target.id === "gua_relation") {
          $("#gua_occu").focus().select();
      } else if (e.target.id === "gua_occu") {
          $("#gua_contact").focus().select();
      } else if (e.target.id === "gua_contact") {
          $("#gua_address_01").focus().select();
      } else if (e.target.id === "gua_address_01") {
          if (validateRequired("gua_address_01")) {
              $("#gua_address_02").focus().select();
          }
      } else if (e.target.id === "gua_address_02") {
          if (validateRequired("gua_address_02")) {
              $("#gua_address_03").focus().select();
          }
      } else if (e.target.id === "gua_address_03") {
          if (validateRequired("gua_address_03")) {
              validateSubmitCustomer();
          }
      }
    }
  });
});

const validateSubmitCustomer = (event) => {
  event.preventDefault();

  let err = 0;

  let arr = ["f_name", "last_name", "contact_number", "nic","cus_number"];

  err = check_validate(arr, err);

  if (err == 0) {
    saveCustomer(event);
  } else {
      Swal.fire("Error!", "Please fill the required fields !", "error");
    return false;
  }
};

function validatePhoneNumber(err) {
  let checkValue = $("#contact_number").val();
  const errorMessage = document.getElementById("error-message");

  if (checkValue.length !== 10 && checkValue.length !== 0) {
    errorMessage.classList.add("error-visible");
    err++;
  } else {
    errorMessage.classList.remove("error-visible");
  }
  return err;
}

const saveCustomer = (e) => {
    e.preventDefault();



    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to save this Customer?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Save it!",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                html: '<p>The customer saving process may take some time depending on your document upload sizes.</p>' +
                    '<div id="progress-container" style="width: 100%; background-color: #e9ecef; border-radius: 0.25rem;">' +
                    '<div id="progress-bar" style="width: 0%; height: 20px; background-color: #1A2942; border-radius: 0.25rem;"></div>' +
                    '</div>',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append("cus_number", $("#cus_number").val());
            formData.append("title", $("#title").val());
            formData.append("f_name", $("#f_name").val());
            formData.append("last_name", $("#last_name").val());
            formData.append("email", $("#email").val());
            formData.append("contact_number", $("#contact_number").val());
            formData.append("nic", $("#nic").val());
            formData.append("new_nic", $("#new_nic").val());
            formData.append("gender", $("#gender").val());
            formData.append("dob", $("#dob").val());
            formData.append("root", $("#root").val());
            formData.append("business_registration", $("#business_registration").val());

            formData.append("curr_address_01", $("#curr_address_01").val());
            formData.append("curr_address_02", $("#curr_address_02").val());
            formData.append("curr_address_03", $("#curr_address_03").val());

            formData.append("per_address_01", $("#per_address_01").val());
            formData.append("per_address_02", $("#per_address_02").val());
            formData.append("per_address_03", $("#per_address_03").val());

            formData.append("city", $("#city").val());
            formData.append("state", $("#state").val());
            formData.append("landline", $("#landline").val());
            formData.append("cus_phto", $("#cus_phto")[0].files[0]); // File input
            formData.append("note", $("#note").val());

            // AJAX call with progress tracking
            $.ajax({
                type: "POST",
                url: "/customers",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: formData,
                contentType: false,
                processData: false,
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                            $("#progress-bar").css("width", percentComplete + "%");
                        }
                    }, false);
                    return xhr;
                },
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        if (data.id === "0") {
                            Swal.fire("Error!", "This customer already exists!", "error");
                        } else {
                            // Proceed to document and bank saving
                            save_doc(data.id, function () {
                                save_bank(data.id, function () {
                                    Swal.fire({
                                        position: "center",
                                        icon: "success",
                                        title: "Successfully saved!",
                                    }).then(function () {
                                        window.location.reload();
                                    });
                                });
                            });
                        }
                    } else {
                        Swal.fire("Error!", "Failed to save data!", "error");
                    }
                },
                error: function () {
                    Swal.fire("Error!", "Failed to save data!", "error");
                }
            });
        }
    });
};



function saveDocument() {
    var customer = $('#customer').val();
    var description = $('#description').val();
    var file = $('#file')[0].files[0]; // Get the first selected file

    var formData = new FormData();
    formData.append('customer', customer);
    formData.append('description', description);
    formData.append('file', file);

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to save this Document ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Upload it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/savecustomerdocument',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Successfully saved!",
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire("Error!", "Failed to save data!", "error");
                    }
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    console.error('Error saving document:', error);
                }
            });

        }
    });
}


function save_bank(id, callback) {
    var formData = new FormData();

    formData.append('bankName', $('#bank_name').val());
    formData.append('accountName', $('#account_name').val());
    formData.append('accountNumber', $('#account_number').val());
    formData.append('branch', $('#branch').val());
    formData.append('id', id);

    $('#bank_table tbody tr').each(function () {
        var row = $(this);
        formData.append('tableBankNames[]', row.find('td').eq(0).text().trim());
        formData.append('tableAccountNames[]', row.find('td').eq(1).text().trim());
        formData.append('tableAccountNumbers[]', row.find('td').eq(2).text().trim());
        formData.append('tableBranches[]', row.find('td').eq(3).text().trim());
    });

    $.ajax({
        url: "/save-bank-details",
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: formData,
        contentType: false,
        processData: false,
        xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                    $("#progress-bar").css("width", percentComplete + "%");
                }
            }, false);
            return xhr;
        },
        success: function (response) {
            console.log("Bank details saved successfully");
            callback(); // Final callback to show success message
        },
        error: function () {
            console.error("Error saving bank details.");
        }
    });
}





function check_group(val){
    $.ajax({
        type: "GET",
        url: "/getcustomergroup/"+val,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data, textStatus, xhr) {
            // Clear the current options in the Select2 dropdown
            $('#group').empty();

            // Parse the response data if necessary
            var groups = data.item;
            $('#group').append(new Option("Select", 0));
            // Populate the Select2 dropdown with the groups
            $.each(groups, function (index, group) {
                $('#group').append(new Option(group.Name, group.idCustomer_Group));
            });

            // Refresh the Select2 dropdown
            $('#group').trigger('change');
        },
        error: function(xhr, textStatus, errorThrown) {
            console.log("Error:", errorThrown);
        }
    });
}


function save_doc(id, callback) {
    var formData = new FormData();

    $('#documenttable input[type="file"]').each(function (index, element) {
        var file = element.files[0];
        var documentName = $(element).closest('tr').find('td:first').text().trim();

        if (file) {
            formData.append('documents[]', file);
            formData.append('documentNames[]', documentName);
        }
    });

    formData.append('id', id);

    $.ajax({
        url: "/save-files-customer",
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: formData,
        contentType: false,
        processData: false,
        xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                    $("#progress-bar").css("width", percentComplete + "%");
                }
            }, false);
            return xhr;
        },
        success: function (response) {
            console.log("Documents saved successfully");
            callback(); // Proceed to the next step (Bank Saving)
        },
        error: function () {
            console.error("Error saving documents.");
        }
    });
}




