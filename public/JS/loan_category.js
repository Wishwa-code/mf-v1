$(function () {
    repayment_type("Days");
  $("#product_name").focus().select();
  //enter key press event
  $(document).keypress(function (e) {
    if (e.which === 13) {
      e.preventDefault();

      if (e.target.id === "product_name") {
        if (validateRequired("product_name")) {
          $("#loan_amount").focus().select();
        }
      } else if (e.target.id === "loan_amount") {
          if (validateRequired("loan_amount")) {
              $("#interest").focus().select();
          }
      } else if (e.target.id === "interest") {
          if (validateRequired("interest")) {
              $("#period_count").focus().select();
          }
      } else if (e.target.id === "period_count") {
          if (validateRequired("period_count")) {
              $("#witnessCount").focus().select();
          }
      } else if (e.target.id === "witnessCount") {
          if (validateRequired("witnessCount")) {
              $("#loan_duration").focus().select();
          }
      } else if (e.target.id === "loan_duration") {
          if (validateRequired("loan_duration")) {
              $("#panelty_rate").focus().select();
          }
      } else if (e.target.id === "panelty_rate") {
          if (validateRequired("panelty_rate")) {
              $("#panelty_rate_date").focus().select();
          }
      } else if (e.target.id === "panelty_rate_date") {
        if (validateRequired("panelty_rate_date")) {
            validateSubmitLoanCate(e);
        }
      }
    }
  });
});

const validateSubmitLoanCate = (event) => {
  event.preventDefault();

  let err = 0;

  let arr = ["product_name","product_code", "loan_amount_from", "loan_amount_to", "interest_from", "interest_to", "loan_duration", "panelty_rate", "panelty_rate_date", "witnessCount"];

  err = check_validate(arr, err);

  if (err == 0) {
      saveLoanCategory(event);
  } else {
      Swal.fire("Error!", "Please fill the required fields !", "error");
    return false;
  }
};



const saveLoanCategory = (e) => {
  e.preventDefault();

    const loan_amount_from = $("#loan_amount_from").val().trim();
    const loan_amount_to = $("#loan_amount_to").val().trim();

    // Check if fields are empty
    if (!loan_amount_from || !loan_amount_to) {
        Swal.fire("Error!", "Both Minimum and Maximum Loan Amount fields are required !", "error");
        return false; // Stop the save operation
    }

    // Check if values are numeric
    if (isNaN(loan_amount_from) || isNaN(loan_amount_to)) {
        Swal.fire("Error!", "Please enter valid numeric values for Loan Amounts !", "error");
        return false; // Stop the save operation
    }

    // Check if minimum is less than or equal to maximum
    if (parseFloat(loan_amount_from) > parseFloat(loan_amount_to)) {
        Swal.fire("Error!", "Minimum Loan Amount must be less than or equal to Maximum Loan Amount !", "error");
        return false; // Stop the save operation
    }

    const interest_from = $("#interest_from").val().trim();
    const interest_to = $("#interest_to").val().trim();

// Check if fields are empty
    if (!interest_from || !interest_to) {
        Swal.fire("Error!", "Both Minimum and Maximum Interest fields are required !", "error");
        return false; // Stop the save operation
    }

    // Check if values are numeric
    if (isNaN(interest_from) || isNaN(interest_to)) {
        Swal.fire("Error!", "Please enter valid numeric values for Loan Interest !", "error");
        return false; // Stop the save operation
    }

    // Check if minimum is less than or equal to maximum
    if (parseFloat(interest_from) > parseFloat(interest_to)) {
        Swal.fire("Error!", "Minimum Interest must be less than or equal to Maximum Interest !", "error");
        return false; // Stop the save operation
    }




    const product_name = $("#product_name").val();
    const product_code = $("#product_code").val();

    const interest_method = $("#interest_method").val();
    const interest_period = $("#interest_period").val();


    const loan_duration = $("#loan_duration").val();
    const collection_type = $("#collection_type").val();
    const penalty_period = $("#penalty_period").val();
    const panelty_rate = $("#panelty_rate").val();
    const panelty_rate_date = $("#panelty_rate_date").val();
    const witnessCount = $("#witnessCount").val();

    const duration_period = $("#duration_period").val();

    const period_count = $("#period_count").val();
    const default_loan_duration_period = $("#default_loan_duration_period").val();

    let level_data = readLevelsData();


    const enable_saving = $("#enable_saving").val();
    const saving_account_amount_type = $("#saving_account_amount_type").val();
    let saving_amount = $("#saving_amount").val();
    let saving_payment = $("#saving_payment").val();
    let error_count=0;
    if (enable_saving==="Yes"){
        if (saving_amount===""){
            error_count=1;
        }
    }else{
        saving_amount=0.00;
    }
    let penalty_method = $("#penalty_method").val();
    let collection_date_type = $("#collection_date_type").val();




    var othercharges = [];

    // Iterate over table rows
    $('#otherchargetable tbody tr').each(function() {
        var rowData = [];

        var $columns = $(this).find('td:nth-child(1), td:nth-child(2), td:nth-child(3)');

        // Iterate over selected columns
        $columns.each(function() {
            rowData.push($(this).text()); // Add cell value to row data
        });

        // Add row data to main array
        othercharges.push(rowData);
    });


    var document = [];

    // Iterate over table rows
    $('#documenttable tbody tr').each(function() {
        var rowData = [];

        var $columns = $(this).find('td:nth-child(1)');

        // Iterate over selected columns
        $columns.each(function() {
            rowData.push($(this).text()); // Add cell value to row data
        });

        // Add row data to main array
        document.push(rowData);
    });

    console.log(level_data);

    if (level_data.length === 0) {
        Swal.fire("Error!", "Please add atleast one level !", "error");
        return false;
    }else if (error_count===1){
        Swal.fire("Error!", "Please enter saving amount !", "error");
        return false;
    }else{
        let count=0;
        level_data.forEach(level => {
            if (level.designations.length === 0) {
                count++;
            }
        });

        if (count!==0) {
            Swal.fire("Error!", "Please add atleast one designation !", "error");
            return false;
        }else{
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to save this Product ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Save it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "/loancategory",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: {
                            product_name:product_name,
                            product_code:product_code,
                            loan_amount_from: loan_amount_from,
                            loan_amount_to: loan_amount_to,
                            interest_method: interest_method,
                            interest_period: interest_period,
                            interest_from: interest_from,
                            interest_to: interest_to,
                            duration_period: duration_period,
                            loan_duration: loan_duration,
                            collection_type: collection_type,
                            penalty_period: penalty_period,
                            panelty_rate: panelty_rate,
                            panelty_rate_date: panelty_rate_date,
                            witnessCount: witnessCount,
                            othercharges: othercharges,
                            period_count: period_count,
                            level_data: level_data,
                            document: document,
                            enable_saving:enable_saving,
                            saving_account_amount_type:saving_account_amount_type,
                            saving_amount:saving_amount,
                            saving_payment:saving_payment,
                            default_loan_duration_period:default_loan_duration_period,
                            collection_date_type:collection_date_type,
                            penalty_method:penalty_method
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
        }



    }



};

function remove_loan_category(id){
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to remove this Product ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Remove it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/savecustomerdocument/remove/'+id,
                type: 'GET',
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Successfully removed !",
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire("Error!", "This category already used in loan !", "error");
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire("Error!", "This category already used in loan !", "error");
                }
            });

        }
    });
}


function view_doc(id){
    $.ajax({
        type: "GET",
        url: "/loancatedoc/"+id,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data, textStatus, xhr) {
            if (xhr.status === 200) {
                // Process other charges
                var otherChargesTable = $('#chargers_show_table tbody');
                otherChargesTable.empty(); // Clear existing rows

                data.other_charges.forEach(function(charge) {
                    var row = $('<tr></tr>');
                    row.append('<td>' + charge.Description + '</td>');
                    row.append('<td>' + charge.Amount + '</td>');
                    row.append('<td><button class="btn btn-danger" onclick="remove_other_charge('+charge.idOther_Charges+')" style="background-color: white; color: #ff0000; border: none"><i class="bi bi-trash fs-3"></i></button></td>');
                    otherChargesTable.append(row);
                });

                // Process required documents
                var documentsTable = $('#document_show_table tbody');
                documentsTable.empty(); // Clear existing rows

                data.required_documents.forEach(function(document) {
                    var row = $('<tr></tr>');
                    row.append('<td>' + document.Name + '</td>');
                    row.append('<td><button class="btn btn-danger" onclick="remove_doc('+document.idRequired_Documents+')" style="background-color: white; color: #ff0000; border: none"><i class="bi bi-trash fs-3"></i></button></td>');
                    documentsTable.append(row);
                });
            }
        },

        error: function(xhr, textStatus, errorThrown) {
            console.log("Error:", errorThrown);
        }
    });
}


function view_doc_2(id){
    $.ajax({
        type: "GET",
        url: "/loancatedoc/"+id,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data, textStatus, xhr) {
            if (xhr.status === 200) {
                // Process other charges
                var otherChargesTable = $('#otherchargetable_2 tbody');
                otherChargesTable.empty(); // Clear existing rows

                data.other_charges.forEach(function(charge) {
                    var row = $('<tr></tr>');
                    row.append('<td>' + charge.Description + '</td>');
                    row.append('<td>' + charge.charge_type + '</td>');
                    row.append('<td>' + charge.Amount + '</td>');
                    row.append('<td><button class="btn btn-danger delete-row" style="background-color: white; color: #ff0000; border: none"><i class="bi bi-trash fs-3"></i></button></td>');
                    otherChargesTable.append(row);
                });
                otherChargesTable.on('click', '.delete-row', function() {
                    $(this).closest('tr').remove();
                });

                // Process required documents
                var documentsTable = $('#documenttable_2 tbody');
                documentsTable.empty(); // Clear existing rows

                data.required_documents.forEach(function(document) {
                    var row = $('<tr></tr>');
                    row.append('<td>' + document.Name + '</td>');
                    row.append('<td><button class="btn btn-danger delete-row"  style="background-color: white; color: #ff0000; border: none"><i class="bi bi-trash fs-3"></i></button></td>');
                    documentsTable.append(row);
                });
                documentsTable.on('click', '.delete-row', function() {
                    $(this).closest('tr').remove();
                });
            }
        },

        error: function(xhr, textStatus, errorThrown) {
            console.log("Error:", errorThrown);
        }
    });
}


function remove_other_charge(id){
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to remove this Charge ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Remove it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/othercharge/remove/'+id,
                type: 'GET',
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Successfully removed !",
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {

                    }
                },
                error: function(xhr, status, error) {

                }
            });

        }
    });
}


function remove_doc(id){
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to remove this Document ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Remove it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/remove_doc/remove/'+id,
                type: 'GET',
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Successfully removed !",
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {

                    }
                },
                error: function(xhr, status, error) {

                }
            });

        }
    });
}



function repayment_type(id) {
    var select = document.getElementById("collection_type");

    // Clear the existing options
    select.innerHTML = '';

    // Conditionally add options based on the provided `id`
    if (id === "Days") {
        // Add only 'Daily'
        var option = new Option("Daily", "Daily");
        select.add(option);
    } else if (id === "Weeks") {
        // Add 'Weekly' and 'Twice A Month'
        var weekOptions = [
            { text: "Weekly", value: "Weekly" },
            { text: "Twice A Month", value: "Twice A Month" }
        ];

        weekOptions.forEach(function(optionData) {
            var option = new Option(optionData.text, optionData.value);
            select.add(option);
        });
    } else if (id === "Months") {
        // Add only month-related options (excluding 'Twice A Month')
        var monthOptions = [
            { text: "First Of The Month", value: "First Of The Month" },
            { text: "End Of The Month", value: "End Of The Month" },
            { text: "On A Selected Date", value: "On A Selected Date" }
        ];

        monthOptions.forEach(function(optionData) {
            var option = new Option(optionData.text, optionData.value);
            select.add(option);
        });
    }
}





