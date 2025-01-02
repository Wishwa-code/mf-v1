$(function () {

    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
        theme: 'bootstrap4'
    })

  $("#group_number").focus().select();
  //enter key press event
  $(document).keypress(function (e) {
    if (e.which === 13) {
      e.preventDefault();

      if (e.target.id === "group_number") {
        if (validateRequired("group_number")) {
          $("#group_name").focus().select();
        }
      } else if (e.target.id === "group_name") {
          if (validateRequired("group_name")) {
              $("#leader").focus().select();
          }
      } else if (e.target.id === "leader") {
          if (validateRequired("leader")) {
              $("#contact").focus().select();
          }
      } else if (e.target.id === "contact") {
        if (validateRequired("contact")) {
            validateSubmitGroup(e);
        }
      }
    }
  });
});

const validateSubmitGroup = (event) => {
  event.preventDefault();

  let err = 0;

  let arr = [ "group_number","group_name","leader","contact"];
  err = check_validate(arr, err);

  if (err == 0) {
      saveGroup(event);
  } else {
      Swal.fire("Error!", "Please fill the required fields !", "error");
    return false;
  }
};



const saveGroup = (e) => {
    e.preventDefault();

    const group_number = $("#group_number").val();
    const group_name = $("#group_number").val();
    const leader = $("#leader").val();
    const contact = $("#contact").val();
    const center_id = $("#center_details").val(); // Correct ID for the select element

    console.log('center_id:', center_id); // Debugging: check the selected value

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to save this Group?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Save it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "/customergroup",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    group_number: group_number,
                    group_name: group_name,
                    leader: leader,
                    center_id: center_id,
                    contact: contact
                },
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        if (data.id==="1"){
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Successfully saved!",
                            }).then(function () {
                                window.location.reload();
                            });
                        }else{
                            Swal.fire("Error!", "This Group Number Is Already Exist !", "error");
                        }
                    } else {
                        Swal.fire("Error!", "Failed to save data!", "error");
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    console.error('Error:', textStatus, errorThrown);
                    Swal.fire("Error!", "Failed to save data!", "error");
                }
            });
        }
    });
};



