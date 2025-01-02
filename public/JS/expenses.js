$(function () {

    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
        theme: 'bootstrap4'
    })

  $("#reason").focus().select();
  //enter key press event
  $(document).keypress(function (e) {
    if (e.which === 13) {
      e.preventDefault();

      if (e.target.id === "reason") {
        if (validateRequired("reason")) {
          $("#amount").focus().select();
        }
      } else if (e.target.id === "amount") {
        if (validateRequired("amount")) {
            validateSubmitExpense(e);
        }
      }
    }
  });
});

const validateSubmitExpense = (event) => {
  event.preventDefault();

  let err = 0;

  let arr = ["reason", "amount"];
  err = check_validate(arr, err);

  if (err == 0) {
      saveExpenses(event);
  } else {
      Swal.fire("Error!", "Please fill the required fields !", "error");
    return false;
  }
};



const saveExpenses = (e) => {
  e.preventDefault();

  const type = $("#type").val();
  const reason = $("#reason").val();
  const date = $("#date_choose").val();
  const amount = $("#amount").val();
  const bank = $("#bank").val();
  const category = $("#category").val();

  Swal.fire({
    title: "Are you sure?",
    text: "Do you want to save this ?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, Save it!",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: "POST",
        url: "/income",
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            type: type,
            reason: reason,
            date: date,
            bank: bank,
            category: category,
            amount: amount
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
      });
    }
  });
};

function update_center(){
    const center_id = $("#center_id").val();
    const center_number = $("#center_number").val();
    const center_name = $("#center_name").val();
    const contact = $("#contact").val();
    const route = $("#route").val();
    const address = $("#address").val();
    const center_incharge = $("#center_incharge").val();

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
                    center_number: center_number,
                    center_name: center_name,
                    contact: contact,
                    address: address,
                    center_incharge: center_incharge,
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
                    } else {
                        Swal.fire("Error!", "Failed to delete data!", "error");
                    }
                },
            });
        }
    });
}

