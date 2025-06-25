$(function () {

    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
        theme: 'bootstrap4'
    })

  $("#center_number").focus().select();
  //enter key press event
  $(document).keypress(function (e) {
    if (e.which === 13) {
      e.preventDefault();

      if (e.target.id === "center_number") {
        if (validateRequired("center_number")) {
          $("#center_name").focus().select();
        }
      } else if (e.target.id === "center_name") {
          if (validateRequired("center_name")) {
              $("#contact").focus().select();
          }
      } else if (e.target.id === "contact") {
          if (validateRequired("contact")) {
              $("#address").focus().select();
          }
      } else if (e.target.id === "address") {
          if (validateRequired("address")) {
              $("#center_incharge").focus().select();
          }
      } else if (e.target.id === "center_incharge") {
        if (validateRequired("center_incharge")) {
            validateSubmitCanter(e);
        }
      }
    }
  });
});

const validateSubmitCanter = (event) => {
  event.preventDefault();

  let err = 0;

  let arr = ["center_number", "center_name","contact","address","center_incharge","location"];
  err = check_validate(arr, err);

  if (err == 0) {
      saveCenter(event);
  } else {
      Swal.fire("Error!", "Please fill the required fields !", "error");
    return false;
  }
};



const saveCenter = (e) => {
  e.preventDefault();

  const center_number = $("#center_number").val();
  const center_name = $("#center_name").val();
  const contact = $("#contact").val();
  const route = $("#route").val();
  const address = $("#address").val();
  const center_incharge = $("#center_incharge").val();
  const location = $("#location").val();
  const route_id = $("#route_id").val();


  if (route_id==""){
      Swal.fire("Error!", "Please Add Route !", "error");
  }else{
      Swal.fire({
          title: "Are you sure?",
          text: "Do you want to save this Center ?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, Save it!",
      }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  type: "POST",
                  url: "/center",
                  headers: {
                      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                  },
                  data: {
                      center_number: center_number,
                      center_name: center_name,
                      contact: contact,
                      address: address,
                      center_incharge: center_incharge,
                      location: location,
                      route_id: route_id,
                      route:route
                  },
                  success: function (data, textStatus, xhr) {

                      console.log(data);

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
                              Swal.fire("Error!", "This Center Number Is Already Exist !", "error");
                          }

                      } else {
                          Swal.fire("Error!", "Failed to save data!", "error");
                      }
                  },
              });
          }
      });
  }


};

function update_center(){
    const center_id = $("#center_id").val();
    const center_number = $("#center_number").val();
    const center_name = $("#center_name").val();
    const contact = $("#contact").val();
    const route = $("#route").val();
    const address = $("#address").val();
    const center_incharge = $("#center_incharge").val();
    const location = $("#location").val();
    const route_id = $("#route_id").val();

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to update this Center ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Update it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "/center/update",
                headers: {
                  "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    center_id: center_id,
                    route_id: route_id,
                    center_number: center_number,
                    center_name: center_name,
                    contact: contact,
                    address: address,
                    center_incharge: center_incharge,
                    location: location,
                    route:route
                },
                success: function (data, textStatus, xhr) {
                      if (xhr.status === 200) {
                          Swal.fire({
                              position: "center",
                              icon: "success",
                              title: "Successfully updated!",
                          }).then(function () {
                              window.location.reload();
                          });
                      } else {
                          Swal.fire("Error!", "Failed to update data!", "error");
                      }
                  },
              });
        }
    });
}


function delete_center(id){
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to delete this Center ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "GET",
                url: "/center/delete/"+id,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Successfully deleted!",
                        }).then(function () {
                            window.location.reload();
                        });
                    } else if (xhr.status === 201) {
                        Swal.fire("Error!", data.message, "error");
                    }else{
                        Swal.fire("Error!", "Failed to delete data!", "error");
                    }
                },
            });
        }
    });
}

