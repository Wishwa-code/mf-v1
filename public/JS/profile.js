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


const saveProfile = (e) => {
    e.preventDefault();

    const formData = new FormData();
    formData.append("company_name", $("#company_name").val());
    formData.append("address", $("#address").val());
    formData.append("branch", $("#branch").val());
    formData.append("con", $("#con").val());
    let customer_format_selection=$("#customer_format_selection").val();
    formData.append("customer_format_selection", $("#customer_format_selection").val());
    formData.append("separate_from", $("#separate_from").val());
    formData.append("field_output_customer", $("#field_output_customer").val());
    formData.append("auto_number", $("#auto_number").val());
    if (customer_format_selection==="Customize"){
        formData.append("separate_from", "");
        formData.append("field_output_customer", "");
        formData.append("auto_number", "");
    }

    let loan_format_selection=$("#loan_format_selection").val();
    formData.append("loan_format_selection", $("#loan_format_selection").val());

    formData.append("separate_from_loan", $("#separate_from_loan").val());
    formData.append("field_output_loan", $("#field_output_loan").val());
    if (loan_format_selection==="Customize"){
        formData.append("separate_from_loan", "");
        formData.append("field_output_loan", "");
    }



    let saving_selection=$("#saving_selection").val();
    formData.append("saving_selection", $("#saving_selection").val());

    formData.append("separate_from_savings", $("#separate_from_savings").val());
    formData.append("field_output_saving", $("#field_output_saving").val());
    if (saving_selection==="Customize"){
        formData.append("separate_from_savings", "");
        formData.append("field_output_saving", "");
    }



    let inv_loan_format_selection=$("#inv_loan_format_selection").val();
    formData.append("inv_loan_format_selection", $("#inv_loan_format_selection").val());

    formData.append("separate_from_inv_loan", $("#separate_from_inv_loan").val());
    formData.append("field_output_inv_loan", $("#field_output_inv_loan").val());
    if (inv_loan_format_selection==="Customize"){
        formData.append("separate_from_inv_loan", "");
        formData.append("field_output_inv_loan", "");
    }




    formData.append("logo", $("#profile_image")[0].files[0]); // File input

    formData.append("activatePoints",$("#activatePoints").prop("checked") ? 1 : 0);
    formData.append("pointsPercentage", $("#pointsPercentage").val());

    formData.append("company_header", $("#company_header")[0].files[0]); // File input
    formData.append("company_footer", $("#company_footer")[0].files[0]); // File input

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to save this Company Profile ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Save it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "/company-profile",
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
                            Swal.fire("Error!", "This customer number is already exist !", "error");
                        }else{
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





