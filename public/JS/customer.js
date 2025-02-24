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

    const formData = new FormData();
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
    formData.append("longitude", $("#longitude").val());
    formData.append("latitude", $("#latitude").val());
    formData.append("gua_title", $("#gua_title").val());
    formData.append("gua_name", $("#gua_name").val());
    formData.append("guardian_gender", $("#guardian_gender").val());
    formData.append("gua_relation", $("#gua_relation").val());
    formData.append("gua_occu", $("#gua_occu").val());
    formData.append("gua_contact", $("#gua_contact").val());

    formData.append("gua_address_01", $("#gua_address_01").val());
    formData.append("gua_address_02", $("#gua_address_02").val());
    formData.append("gua_address_03", $("#gua_address_03").val());

    formData.append("cus_number", $("#formatted_num_use").text());
    formData.append("gua_nic", $("#gua_nic").val());
    formData.append("risk_level", "1");
    formData.append("civil_status", $("#civil_status").val());
    formData.append("contact_number_2", $("#contact_number_2").val());

    formData.append("occu_job_position", $("#occu_job_position").val());
    formData.append("occu_monthly_salary", $("#occu_monthly_salary").val());
    formData.append("occu_address_01", $("#occu_address_01").val());
    formData.append("occu_address_02", $("#occu_address_02").val());
    formData.append("occu_address_03", $("#occu_address_03").val());
    formData.append("occu_contact_no", $("#occu_contact_no").val());
    formData.append("occu_longitude", $("#occu_longitude").val());
    formData.append("occu_latitude", $("#occu_latitude").val());

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
            $.ajax({
                type: "POST",
                url: "/customers",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: formData,
                contentType: false,
                processData: false,
                success: function (data, textStatus, xhr) {
                    console.log(data);
                    if (xhr.status === 200) {

                        if (data.id==="0"){
                            Swal.fire("Error!", "This customer is already exist !", "error");
                        }else{
                            save_doc(data.id);
                            save_bank(data.id);
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Successfully saved!",
                            }).then(function () {
                                window.location.reload();
                            });
                        }

                    } else {
                        Swal.fire("Error!", "Failed to save data!", "error");
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
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


function save_bank(id) {
    var formData = new FormData();

    // Get the values from the input fields
    var bankName = $('#bank_name').val();
    var accountName = $('#account_name').val();
    var accountNumber = $('#account_number').val();
    var branch = $('#branch').val();

    // Append the values to the FormData object
    formData.append('bankName', bankName);
    formData.append('accountName', accountName);
    formData.append('accountNumber', accountNumber);
    formData.append('branch', branch);
    formData.append('id', id); // Append the general ID

    // Iterate over table rows to collect bank details
    $('#bank_table tbody tr').each(function() {
        var row = $(this);
        var rowBankName = row.find('td').eq(0).text().trim();
        var rowAccountName = row.find('td').eq(1).text().trim();
        var rowAccountNumber = row.find('td').eq(2).text().trim();
        var rowBranch = row.find('td').eq(3).text().trim();

        // Append each row's data to the FormData object
        formData.append('tableBankNames[]', rowBankName);
        formData.append('tableAccountNames[]', rowAccountName);
        formData.append('tableAccountNumbers[]', rowAccountNumber);
        formData.append('tableBranches[]', rowBranch);
    });

    // Send the form data via AJAX
    $.ajax({
        url: "/save-bank-details",
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            // Handle success
            console.log("Bank details saved successfully");
        },
        error: function(xhr, status, error) {
            // Handle error
            console.error(error);
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



function save_doc(id) {
    var formData = new FormData();

    // Scope the file input selector to the document table only
    $('#documenttable input[type="file"]').each(function(index, element) {
        // Get the file and document name
        var file = element.files[0];
        var documentName = $(element).closest('tr').find('td:first').text().trim();

        if (file) {
            // Append the file and document name to the FormData object
            formData.append('documents[]', file);
            formData.append('documentNames[]', documentName); // Append document name
        }
    });

    formData.append('id', id); // Append the general ID

    $.ajax({
        url: "/save-files-customer",
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            // Handle success
            console.log("Documents saved successfully");
        },
        error: function(xhr, status, error) {
            // Handle error
            console.error(error);
        }
    });
}



