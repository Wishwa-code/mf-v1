$(function () {

    load_payment_table();
});

function load_payment_table() {
    let center_details = $("#center_details").val();
    let group = $("#group").val();
    let date = $("#select_date").val();
    let date_to = $("#select_date_to").val();


    $.ajax({
        type: "POST",
        url: "/view_date_wise_installment",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            center_details: center_details,
            group: group,
            date_to: date_to,
            date: date
        },
        success: function (data, textStatus, xhr) {
            if (xhr.status === 200) {
                var tableBody = $('#loan_table tbody');
                tableBody.empty(); // Clear any existing rows

                let totalAmount = 0.0;

                data.item.forEach(function(item) {
                    var amount = parseFloat(item.Installment_Amount);
                    var tot_balance = parseFloat(item.Total_Balance);
                    totalAmount += amount;
                    var row = `<tr>
    <td>${item.Installment_Date}</td>
    <td>${item.center_name || item.center_no ? (item.center_name || '-') + ' (' + (item.center_no || '-') + ')' : '-'}</td>
    <td>${item.group_name || item.group_no ? (item.group_name || '-') + ' (' + (item.group_no || '-') + ')' : '-'}</td>
    <td>${item.cus_number}</td>
    <td>${item.Loan_No}</td>
    <td>${item.customer_name} ${item.customer_lastname}</td>

    <td>${amount.toFixed(2)}</td>
    <td>${tot_balance.toFixed(2)}</td>

</tr>`;
                    tableBody.append(row);
                });

                $("#tot_amount").text(totalAmount.toFixed(2));
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log("Error:", errorThrown);
        }
    });
}


function payment_slip(id) {
    openModal();
    load_payment_reciept(id);
}


function load_payment_reciept(id){
    $.ajax({
        type: "POST",
        url: "/view_payment_load_reciept/"+id,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data, textStatus, xhr) {
            console.log(data);
            let customer=data.customer;
            let payment=data.payment;
            let loan=data.loan;
            let user=data.user;
            console.log(loan);
            if (xhr.status === 200) {

                $("#customer_name").text(customer.First_Name+" "+customer.Last_Name);
                $("#customer_number").text(customer.cus_number);
                $("#loan_number").text(loan.Loan_No);
                $("#payment_date").text(payment.Date);
                $("#payment_time").text(payment.time);
                $("#payment_type_view").text(payment.Payment_type);


                $("#loan_amount").text(parseFloat(loan.Amount).toFixed(2));
                $("#payed_amount").text(parseFloat(payment.Amount).toFixed(2));
                $("#capital_balance").text(parseFloat(loan.Balance_Amount).toFixed(2));


                $("#signature").text(user.Full_Name);
            }
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



function payment_model(loan_id){
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



function load_installment_log_model(id){
    $.ajax({
        type: "GET",
        url: "/installment_log/"+id,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data, textStatus, xhr) {
            if (xhr.status === 200) {
                $("#ins_table tbody").empty();


                let panelty_tot=0.0;
                let ins_tot=0.0;
                let tot_balance=0.0;
                // Iterate over the data array
                $.each(data.item, function(index, item) {
                    // Construct table row dynamically
                    var row =  "<tr>" +
                        "<td>" + item.Date + "</td>" +
                        "<td>" + item.Description + "</td>" +
                        "<td class='text-end'>" + parseFloat(item.Amount).toFixed(2) + "</td>" +
                        "<td class='text-end'>" + parseFloat(item.Panalty_Total).toFixed(2) + "</td>" +
                        "<td class='text-end'>" + parseFloat(item.Installment_Balance).toFixed(2) + "</td>" +
                        "<td class='text-end'>" + parseFloat(item.Total_Balance).toFixed(2) + "</td>" +
                        "</tr>";

                    // Append row to table body
                    $("#ins_table tbody").append(row);

                    panelty_tot=panelty_tot+parseFloat(item.Panalty_Total);
                    ins_tot=ins_tot+parseFloat(item.Installment_Balance);
                    tot_balance=tot_balance+parseFloat(item.Total_Balance);


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




