$(function () {

    load_payment_table();
});

function load_payment_table() {
    let center_details = $("#center_details").val();
    let group = $("#group").val();
    let customer = $("#customer_id").val();
    let date = $("#select_date").val();
    let date_to = $("#select_date_to").val();
    let user = $("#agent").val();
    let loan_number_search = $("#loan_number_search").val();

    $.ajax({
        type: "POST",
        url: "/view_payment_load",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            center_details: center_details,
            group: group,
            customer: customer,
            date: date,
            date_to: date_to,
            user: user,
            loan_number_search: loan_number_search
        },
        success: function (data, textStatus, xhr) {
            console.log(data);
            if (xhr.status === 200) {
                var tableBody = $('#loan_table tbody');
                tableBody.empty(); // Clear existing rows

                let totalAmount = 0.0;

                data.item.forEach(function (item) {
                    var amount = parseFloat(item.Amount);

                    // Only add to totalAmount if the payment status is not 0
                    if (item.status == '0') {
                        totalAmount += amount;
                    }

                    var formattedAmount = amount.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    var viewButton = item.Slip ?
                        `<button type="button" class="btn btn-success btn-sm ${item.status != '0' ? 'disabled' : ''}" onclick="viewSlip('${item.Slip}')">
                            <i class="bi bi-eye"></i>
                        </button>` :
                        `<button type="button" class="btn btn-success btn-sm disabled">
                            <i class="bi bi-eye"></i>
                        </button>`;

                    var addButton = `<button type="button" class="btn btn-primary btn-sm ${item.status != '0' ? 'disabled' : ''}" onclick="addSlip(${item.idCustomer_Payments})">
                            <i class="bi bi-plus"></i>
                        </button>`;

                    var paymentButton = `<button type="button" class="btn btn-warning btn-sm ${item.status != '0' ? 'disabled' : ''}" onclick="payment_slip(${item.idCustomer_Payments})">
                            <i class="bi bi-file-earmark-text"></i>
                        </button>`;
                    var undoButton="";
                    if (data.payment_delete_status===1){
                        undoButton = `<button type="button" class="btn btn-danger btn-sm ${item.status != '0' ? 'disabled' : ''}" onclick="undo_payment(${item.idCustomer_Payments})">
                            <i class="bi bi-trash"></i>
                        </button>`;
                    }


                    var row = `<tr>
<td style="vertical-align: middle">${item.Inv_no}</td>
                        <td style="vertical-align: middle">${item.center_name || item.center_no ? (item.center_name || '-') + ' (' + (item.center_no || '-') + ')' : '-'}</td>
                        <td style="vertical-align: middle">${item.Location || '-'}</td>
                        <td style="vertical-align: middle">${item.group_name || item.group_no ? (item.group_name || '-') + ' (' + (item.group_no || '-') + ')' : '-'}</td>
                        <td style="vertical-align: middle">${item.cus_number}</td>
                        <td style="vertical-align: middle">${item.Loan_No}</td>
                        <td style="vertical-align: middle">${item.customer_name} ${item.customer_lastname}</td>
                        <td style="vertical-align: middle">${item.Date}</td>
                        <td style="vertical-align: middle">${item.Payment_type}</td>
                        <td style="vertical-align: middle">${formattedAmount}</td>
                        <td style="vertical-align: middle">${item.Full_Name}</td>
                        <td style="vertical-align: middle">${item.comment}</td>
                        <td style="vertical-align: middle">
                            <div class="d-flex flex-nowrap gap-2">
                                ${viewButton}
                                ${addButton}
                                ${paymentButton}
                                ${undoButton}
                            </div>
                        </td>
                    </tr>`;
                    // ${undoButton}
                    tableBody.append(row);
                });

                $("#tot_amount").text(totalAmount.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log("Error:", errorThrown);
        }
    });
}


function undo_payment(id) {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to undo this payment?",
        icon: "warning",
        input: "textarea", // Adds a text box for the reason
        inputPlaceholder: "Enter your reason here...",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Undo it!",
        preConfirm: (reason) => {
            if (!reason) {
                Swal.showValidationMessage("Reason is required!");
            }
            return reason;
        },
    }).then((result) => {
        if (result.isConfirmed) {
            const reason = result.value; // Get the entered reason

            $.ajax({
                type: "POST", // Use POST for sending data securely
                url: "/undoPayment/" + id,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {reason: reason}, // Pass the reason to the backend
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Payment removed successfully!",
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire("Error!", "Failed to undo payment!", "error");
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    Swal.fire("Error!", "An error occurred. Please try again.", "error");
                    console.log("Error:", errorThrown);
                },
            });
        }
    });
}


function payment_slip(id) {
    openModal();
    load_payment_reciept(id);
}


function load_payment_reciept(id) {

    // 🔽 Show loading spinner
    document.getElementById('loadingSpinner').style.display = 'flex';


    $.ajax({
        type: "POST",
        url: "/view_payment_load_reciept/" + id + "/0",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data, textStatus, xhr) {

            let customer = data.customer;
            let payment = data.payment;
            let loan = data.loan;
            let user = data.user;
            let chequeDetails = data.cheque_details;
            let saving_on = data.saving_on;

            let saving_amount = data.saving_amount;
            if (xhr.status === 200) {
                if (data.point_check === "1") {

                    $('#loyalty_section').show();
                } else {

                    $('#loyalty_section').hide();
                }
                $("#Inv_number").text("Receipt No. :"+payment.idCustomer_Payments);
                $("#customer_name").text(customer.First_Name + " " + customer.Last_Name);
                $("#customer_number").text(customer.cus_number);
                $("#loyalty_points").text(parseFloat(data.points_to_add).toFixed(2));
                $("#loan_number").text(loan.Loan_No);
                $("#payment_date").text(payment.Date);
                $("#payment_time").text(payment.time);
                $("#payment_type_view").text(payment.Payment_type);

                if (parseFloat(data.panelty_balance) > 0) {
                    $("#panelty_balance").text(parseFloat(data.panelty_balance).toFixed(2));
                    $("#tot_balance").text(parseFloat(data.tot_balance).toFixed(2));

                    // Show the relevant sections if they are hidden
                    $(".payment-info.balance").show();
                } else {
                    // Optionally hide the sections if no penalty balance exists
                    $(".payment-info.balance").hide();
                }
                if (payment.Payment_type === "Cheque") {
                    console.log(chequeDetails.Cheque_No
                    );
                    // Show the cheque section
                    $('#cheque_section').show();

                    // // Populate the cheque details
                    $("#cheque_no").text(chequeDetails.Cheque_No);
                    $("#cheque_date").text(chequeDetails.Cheque_Date);
                    $("#cheque_name").text(chequeDetails.Name_On_The_Cheque);
                    $("#cheque_type").text(chequeDetails.Cheque_Type);
                } else {
                    $('#cheque_section').hide(); // Hide the cheque section if payment type is not "Cheque"
                }
                if (saving_on === "1") {
                    let saving_amounts = parseFloat(saving_amount) || 0; // Ensure it's a valid number
                    let savingContainer = document.getElementById("saving_amount_container");

                    if (savingContainer) {
                        savingContainer.style.display = "block"; // Make sure it's visible
                        savingContainer.innerHTML = `
            <table width="100%">
                <tr>
                    <td><strong>Saving Amount</strong></td>
                    <td align="right">${saving_amounts.toFixed(2)}</td>
                </tr>
            </table>
        `;
                    }
                } else {
                    let savingContainer = document.getElementById("saving_amount_container");
                    if (savingContainer) {
                        savingContainer.style.display = "none"; // Hide it if not needed
                    }
                }



                $("#loan_amount").text(parseFloat(loan.Amount).toFixed(2));
                $("#full_loan_amount").text(parseFloat(loan.Total_Loan_Amount).toFixed(2));
                if(saving_on === "1"){
                    $("#payed_amount").text(parseFloat(payment.Amount-saving_amount).toFixed(2));
                }else{
                    $("#payed_amount").text(parseFloat(payment.Amount).toFixed(2));
                }
                $("#capital_balance").text(parseFloat(loan.Balance_Amount).toFixed(2));


                $("#signature").text(user.Full_Name);
            }
        },
        complete: function () {
            // ✅ Always hide spinner when request is done
            document.getElementById('loadingSpinner').style.display = 'none';
        },
        error: function () {
            // 🛑 Hide spinner on error too
            document.getElementById('loadingSpinner').style.display = 'none';
            alert('Failed to load payment details.');
        }
    });
}


// Function to open the modal
function openModal() {
    document.getElementById('overlay').style.display = 'block';
    document.getElementById('printerModal').style.display = 'block';
}

// Function to close the modal
function closeModal() {
    document.getElementById('overlay').style.display = 'none';
    document.getElementById('printerModal').style.display = 'none';
}


function viewSlip(slipPath) {
    if (slipPath) {
        window.open('/storage/' + slipPath, '_blank');
    } else {
        alert('No slip available');
    }
}

function addSlip(paymentId) {
    // Open modal to upload new slip
    $('#addSlipModal').modal('show');
    $('#paymentId').val(paymentId); // Set the payment ID in the modal
}

function saveSlip() {


    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to upload this slip ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Upload it!",
    }).then((result) => {
        if (result.isConfirmed) {
            var paymentId = $('#paymentId').val();
            var formData = new FormData();
            formData.append('payment_id', paymentId);
            formData.append('file', $('#slipFile')[0].files[0]);

            $.ajax({
                type: "POST",
                url: "/add_slip",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        $('#addSlipModal').modal('hide');
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Slip uploaded successfully!",
                        }).then(function () {
                            load_payment_table(); // Reload the table to show the new slip
                        });
                    } else {
                        Swal.fire("Error!", "Failed to upload slip!", "error");
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });
        }
    });
}


function payment_model(loan_id) {
    $('#cus_id').val(loan_id);
}


function payment() {
    let cus_id = $('#cus_id').val();
    let payment_amount = $('#payment_amount').val();
    let file = $('#file')[0].files[0];


    if (payment_amount === "") {
        Swal.fire("Error!", "Please Enter Paid Amount !", "error");
    } else {
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to pay this installment?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Pay it!",
        }).then((result) => {
            if (result.isConfirmed) {
                let formData = new FormData();
                formData.append('cus_id', cus_id);
                formData.append('payment_amount', payment_amount);
                formData.append('file', file);

                $.ajax({
                    type: "POST",
                    url: "/payment_save_today",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
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


function load_installment_log_model(id) {
    $.ajax({
        type: "GET",
        url: "/installment_log/" + id,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data, textStatus, xhr) {
            if (xhr.status === 200) {
                $("#ins_table tbody").empty();


                let panelty_tot = 0.0;
                let ins_tot = 0.0;
                let tot_balance = 0.0;
                // Iterate over the data array
                $.each(data.item, function (index, item) {
                    // Construct table row dynamically
                    var row = "<tr>" +
                        "<td>" + item.Date + "</td>" +
                        "<td>" + item.Description + "</td>" +
                        "<td class='text-end'>" + parseFloat(item.Amount).toFixed(2) + "</td>" +
                        "<td class='text-end'>" + parseFloat(item.Panalty_Total).toFixed(2) + "</td>" +
                        "<td class='text-end'>" + parseFloat(item.Installment_Balance).toFixed(2) + "</td>" +
                        "<td class='text-end'>" + parseFloat(item.Total_Balance).toFixed(2) + "</td>" +
                        "</tr>";

                    // Append row to table body
                    $("#ins_table tbody").append(row);

                    panelty_tot = panelty_tot + parseFloat(item.Panalty_Total);
                    ins_tot = ins_tot + parseFloat(item.Installment_Balance);
                    tot_balance = tot_balance + parseFloat(item.Total_Balance);


                });

                $("#tot_panelty").text(panelty_tot.toFixed(2));
                $("#ins_tot").text(ins_tot.toFixed(2));
                $("#tot_balance").text(tot_balance.toFixed(2));

            } else {
                Swal.fire("Error!", "Failed to save data!", "error");
            }
        },
    });
}




