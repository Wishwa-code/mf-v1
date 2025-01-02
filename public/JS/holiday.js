$(function () {


    $("#account_name").focus().select();
    //enter key press event
    $(document).keypress(function (e) {
        if (e.which === 13) {
            e.preventDefault();

            if (e.target.id === "account_name") {
                if (validateRequired("account_name")) {
                    validateSubmitBank(e);
                }
            }
        }
    });
});

const validateSubmitBank = (event) => {
    event.preventDefault();

    let err = 0;

    let arr = ["holiday_date", "account_name"];
    err = check_validate(arr, err);

    if (err == 0) {
        savebank(event);
    } else {
        Swal.fire("Error!", "Please fill the required fields !", "error");
        return false;
    }
};




    const savebank = (e) => {
      e.preventDefault();

      const date = $("#holiday_date").val();
      const reason = $("#account_name").val();

      console.log(date,reason)

      Swal.fire({
        title: "Are you sure?",
        text: "Do you want to save this Holiday ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Save it!",
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            type: "POST",
            url: "/holidays",
            headers: {
              "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                date: date,
                reason: reason,
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
                          Swal.fire("Error!", "This Date Is Already Exist !", "error");
                      }

                  } else {
                      Swal.fire("Error!", "Failed to save data!", "error");
                  }
              },
          });
        }
      });
    };


function deleteHolidays(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Perform the AJAX request
            $.ajax({
                url: `/holidays/delete/${id}`, // Assuming this is the route to handle deletion
                type: 'DELETE',
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire(
                            'Deleted!',
                            response.message,
                            'success'
                        ).then(() => {
                            location.reload(); // Reload the page to refresh the data
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            response.message,
                            'error'
                        );
                    }
                },
                error: function () {
                    Swal.fire(
                        'Error!',
                        'Something went wrong. Please try again later.',
                        'error'
                    );
                }
            });
        }
    });
}


