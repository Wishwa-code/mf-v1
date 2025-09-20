$(function () {


    $("#bank_name").focus().select();
    //enter key press event
    $(document).keypress(function (e) {
        if (e.which === 13) {
            e.preventDefault();

            if (e.target.id === "bank_name") {
                if (validateRequired("bank_name")) {
                    $("#account_name").focus().select();
                }
            } else if (e.target.id === "account_name") {
                if (validateRequired("account_name")) {
                    $("#account_number").focus().select();
                }
            } else if (e.target.id === "account_number") {
                if (validateRequired("account_number")) {
                    $("#branch").focus().select();
                }
            } else if (e.target.id === "branch") {
                if (validateRequired("branch")) {
                    $("#opening_balance").focus().select();
                }
            } else if (e.target.id === "opening_balance") {
                if (validateRequired("opening_balance")) {
                    validateSubmitBank(e);
                }
            }
        }
    });
});

const validateSubmitBank = (event) => {
    event.preventDefault();

    let err = 0;

    let arr = ["bank_code","bank_name", "account_name","account_number","branch","opening_balance"];
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

  const bank_code = $("#bank_code").val();
  const bank_name = $("#bank_name").val();
  const account_name = $("#account_name").val();
  const account_number = $("#account_number").val();
  const branch = $("#branch").val();
  const opening_balance = $("#opening_balance").val();

  Swal.fire({
    title: "Are you sure?",
    text: "Do you want to save this Bank ?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, Save it!",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: "POST",
        url: "/bank_account",
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            bank_code: bank_code,
            bank_name: bank_name,
            account_name: account_name,
            account_number: account_number,
            branch: branch,
            opening_balance: opening_balance
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
                      Swal.fire("Error!", "This Account Number or Account Code Already Exist !", "error");
                  }

              } else {
                  Swal.fire("Error!", "Failed to save data!", "error");
              }
          },
      });
    }
  });
};

function change_status(id){
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to inactive this Bank ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "GET",
                url: "/bank/change/"+id,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {

                        if (data.id==="1"){
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Successfully Changed Status!",
                            }).then(function () {
                                window.location.reload();
                            });
                        }else{
                            Swal.fire("Error!", "This Account Number Is Already Used !", "error");
                        }



                    } else {
                        Swal.fire("Error!", "Failed to delete data!", "error");
                    }
                },
            });
        }
    });
}
function view_log(id, bankName, accountName, accountNumber, todayOnly = false, startDate = null, endDate = null) {

    // cache last viewed account details for reuse from Blade button
    try {
        window.lastViewedAccount = { id, bankName, accountName, accountNumber };
    } catch (e) {}

    $('#standard-modal .modal-header h4').html(`Bank Log Report - <b>${bankName} (${accountName} - ${accountNumber})</b>`);

    let url = "/bank/view_log/" + id;
    const params = [];
    if (todayOnly) params.push("todayfilter=1");
    if (startDate && endDate) {
        params.push("start_date=" + encodeURIComponent(startDate));
        params.push("end_date=" + encodeURIComponent(endDate));
    }
    if (params.length) url += "?" + params.join("&");

    $.ajax({
        type: "GET",
        url,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data, textStatus, xhr) {
            if (xhr.status === 200) {
                console.log(data);

                // Destroy the existing DataTable instance
                if ($.fn.DataTable.isDataTable("#bank_table_log")) {
                    $('#bank_table_log').DataTable().clear().destroy();
                }

                // Clear the existing rows
                $('#bank_table_log tbody').empty();

                // Iterate over the data and create new rows
                data.item.forEach(function(log) {
                    var newRow = `<tr>
        <td>${log.Date_Time}</td>
        <td>${log.Type}</td>
        <td>${log.Description}</td>
        <td>${log.Note}</td>
        <td>${isNaN(log.Debit) ? log.Debit : Number(log.Debit).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
<td>${isNaN(log.Credit) ? log.Credit : Number(log.Credit).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
<td>${isNaN(log.Balance) ? log.Balance : Number(log.Balance).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
<td>${log.Account_Name ?? '-'}</td>
<td>${log.reconsilation_status}</td>
        <td>${log.Full_Name}</td>
    </tr>`;
                    $('#bank_table_log tbody').append(newRow);
                });


                // Reinitialize DataTable
                $('#bank_table_log').DataTable({
                    paging: true,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    responsive: true
                });

            } else {
                Swal.fire("Error!", "Failed to load data!", "error");
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            Swal.fire("Error!", "Failed to load data!", "error");
        }
    });
}



