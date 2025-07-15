let currentLoadedPage = 1;

$(function () {
    load_payment_table(); // <-- this is fine now
});
function load_payment_table(page = 1) {
    currentLoadedPage = page;  // Update current page number
    let route = $("#route").val();
    let center_details = $("#center_details").val();
    let group = $("#group").val();
    let customer = $("#customer_id").val();
    let status = $("#status").val();
    let loan_number_search = $("#loan_number_search").val();

    $.ajax({
        type: "POST",
        url: `/today_payment_load_check?page=${page}`,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            center_details: center_details,
            group: group,
            customer: customer,
            route: route,
            loan_number_search: loan_number_search,
            status: status,
        },
        success: function (response, textStatus, xhr) {
            if (xhr.status === 200) {
                let data = response.item.data;
                let currentPage = response.item.current_page;
                let lastPage = response.item.last_page;
                const getTotalData = response.gettotal;


                let tot = 0.0;
                let pending_amount = 0.0;
                let ins = 0.0;
                let arrese = 0.0;

                // Clear existing table rows
                $("#loan_table tbody").empty();

                // Loop through each item in the response data
                data.forEach(function (item) {


                    // Determine which modal button to use based on item type
                    let modalButton;

                    if (item.type === "Flat Rate") {
                        modalButton = `<button type="button" class="btn btn-primary btn-sm d-flex align-items-center justify-content-center mx-1" value="Payment" data-bs-toggle="modal" data-bs-target="#issue-loan-modal" onclick="payment_model(${item.idCustomer},'${item.type}',${item.idCustomer_Loan},${item.Today_installment})" style="margin: 0 !important;">
        Payment
    </button>`;
                    } else if (item.type === "Draft") {
                        modalButton = `<button type="button" class="btn btn-primary btn-sm d-flex align-items-center justify-content-center mx-1" value="Payment" data-bs-toggle="modal" data-bs-target="#issue-loan-modal_2" onclick="payment_model_2(${item.idCustomer},${item.Total_Balance},${item.idCustomer_Loan},'${item.type}','${item.capital_balance}',${item.Total_Balance},${item.Total_Balance_until},${item.Today_installment},${item.arrease},${item.Installment_Amount})" style="margin: 0 !important;">
        Payment
    </button>`;
                    } else if (item.type === "Reducing Balance") {
                        modalButton = `<button type="button" class="btn btn-primary btn-sm d-flex align-items-center justify-content-center mx-1" value="Payment" data-bs-toggle="modal" data-bs-target="#issue-loan-modal_2" onclick="payment_model_3(${item.idCustomer},${item.Total_Balance},${item.idCustomer_Loan},'${item.type}','${item.capital_balance}',${item.Total_Balance},${item.Total_Balance_until},${item.Today_installment},${item.arrease},${item.Installment_Amount})" style="margin: 0 !important;">
        Payment
    </button>`;
                    }
                    let viewmodel = `<a href="#" class="btn btn-warning btn-sm d-flex align-items-center justify-content-center mx-1" data-bs-toggle="modal" data-bs-target="#view_details_payment" onclick="load_data_model(${item.idCustomer_Loan})" style="border-radius: 5px;">
    <i class="bi bi-eye"></i>
</a>`;
                    let loan_comment = `<a href="#" class="btn btn-danger btn-sm d-flex align-items-center justify-content-center mx-1" id="viewLoanLog" onclick="open_model(${item.idCustomer})" style="border-radius: 5px;">
    <i class="bi bi-chat-left-text me-1"></i> Comment
</a>`;



                    var row = `<tr>
                        <td>${item.Loan_No}</td>
                        <td>${formatName(item.customer_name, item.customer_lastname)}</td>
                        <td>${item.cus_number}</td>
                        <td>${parseFloat(item.Loan_Amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td>${parseFloat(item.Today_installment).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                ${modalButton}
                                ${viewmodel}
                                ${loan_comment}
                            </div>
                        </td>
                        <td>${item.NIC}</td>
                        <td>${item.type}</td>
                    </tr>`;

                    // Append row to table
                    $("#loan_table tbody").append(row);
                });


                getTotalData.forEach(function (item) {
                     tot += parseFloat(item.Total_Balance);
                     pending_amount += parseFloat(item.Total_Balance_until);
                     ins += parseFloat(item.Today_installment);
                     arrese += parseFloat(item.arrease);
                });

                // Update totals
                $("#tot_amount").text(tot.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $("#balance_until").text(pending_amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $("#today_installment").text(ins.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $("#total_arrease").text(arrese.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                // Add pagination controls
                let paginationControls = '';

                if (currentPage > 1) {
                    paginationControls += `
                        <button onclick="load_payment_table(${currentPage - 1})" class="btn btn-sm btn-outline-primary me-2">
                            <i class="bi bi-arrow-left-circle me-1"></i> Previous
                        </button>`;
                }

                if (currentPage < lastPage) {
                    paginationControls += `
                        <button onclick="load_payment_table(${currentPage + 1})" class="btn btn-sm btn-outline-primary">
                            Next <i class="bi bi-arrow-right-circle ms-1"></i>
                        </button>`;
                }

                $('#pagination').html(paginationControls);
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log("Error:", errorThrown);
        }
    });
}






function open_model(id){
    $('#loan_id_comment').val(id);
    $('#loanCommentModal').modal('show');
}



function formatName(firstName, lastName) {
    let firstNameParts = firstName.trim().split(' ');
    let initials = firstNameParts.map(name => name.charAt(0).toUpperCase() + '.').join('');

    let lastNameParts = lastName.trim().split(' ');
    let formattedLastName = lastNameParts[lastNameParts.length - 1];

    return `${initials} ${formattedLastName}`;
}



function load_data_model(idCustomer_Loan) {
    $.ajax({
        url: '/get-loan-details-model', // The route we'll define in Laravel
        method: 'GET',
        data: { idCustomer_Loan: idCustomer_Loan },
        success: function(response) {

            console.log(response);
            // Populate the modal with the received data
            $('#total_capital_balance').text(parseFloat(response.capital_balance).toFixed(2));
            $('#total_interest_balance').text(parseFloat(response.Interest_Balance).toFixed(2));
            $('#total_penalty_balance').text(parseFloat(response.Panalty_Balance).toFixed(2));
            $('#total_due_saving_amount').text(parseFloat(response.Saving_balance).toFixed(2));
            $('#total_balance_amount').text(parseFloat(response.Total_Balance).toFixed(2));
        },
        error: function(error) {
            console.log(error);
            alert('Failed to load data. Please try again.');
        }
    });
}


function payment_model(cus_id, topic, loan_id,Today_installment) {
    $('#cus_id').val(cus_id);
    $('#topic_2').text(topic);
    $('#reduce_balance_loan_id').val(loan_id);
    $('#today_installment_loan').val(parseFloat(Today_installment).toFixed(2));

    $.ajax({
        url: '/today_payment_load_check_loan/'+loan_id, // The route we'll define in Laravel
        method: 'GET',
        success: function(response) {
            let savings=parseFloat(response.savings);
            $.each(response.item, function (index, item) {
                let Total_Balance=parseFloat(item.Total_Balance);

                let arrease=parseFloat(item.arrease);
                let Total_Balance_until=parseFloat(item.Total_Balance_until);
                let Installment_Amount=parseFloat(item.Today_installment);
                let Total_Paid_Amount=parseFloat(item.Total_Paid_Amount);

                $('#loan_balance').val(Total_Balance.toFixed(2));
                $('#today_arrese').val(arrease.toFixed(2));
                $('#total_outstanding').val(Total_Balance_until.toFixed(2));
                $('#total_loan_balance').val(Total_Balance.toFixed(2));
                $('#total_savings_balance').val(savings.toFixed(2));
                $('#ins_amount').val(Installment_Amount.toFixed(2));
                $('#tot_paid_amount').val(Total_Paid_Amount.toFixed(2));
            });
            // Assuming response is already defined somewhere in your script
            if (response.saving==="1") {
                document.getElementById("savings_section").style.display = "flex";
                document.querySelector("label[for='payment_amount']").textContent = "Installment Amount (LKR)";
            } else {
                document.querySelector("label[for='payment_amount']").textContent = "Paid Amount (LKR)";
                document.getElementById("savings_section").style.display = "none";
            }



        },
        error: function(error) {
            console.log(error);
            alert('Failed to load data. Please try again.');
        }
    });
}

function payment_model_2(cus_id, pending_amount, loan_id, topic, loan_capital_balance,loan_balance,Total_Balance_until,Today_installment,arrease) {
    $('#cus_id_2').val(cus_id);
    $('#loan_balance_2').val(loan_balance.toFixed(2));
    $('#reduce_balance_loan_id').val(loan_id);
    $('#pending_amount').text(parseFloat(Total_Balance_until).toFixed(2));
    $('#topic').text(topic);
    $('#loan_capital_balance').text(parseFloat(loan_capital_balance).toFixed(2));


    $.ajax({
        type: "GET",
        url: "/loanviewajax/" + loan_id,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data, textStatus, xhr) {
            let interest = parseFloat(data.interest);
            let paid_amount = parseFloat(data.paid_amount);
            console.log(paid_amount);
            let days_from_last_payment_date = parseFloat(data.days_from_last_payment_date);
            if (data.loan.Interest_period === "Daily") {
                $('#rate_date').text(data.loan.Interest_Rate + "%");
                let tot=interest * days_from_last_payment_date;
                tot = tot-paid_amount;
                if (tot<0){
                    tot=0.00;
                }
                $('#installment_interest_today').text(parseFloat(tot).toFixed(2));
            } else if (data.loan.Interest_period === "Weekly") {
                let rate = data.loan.Interest_Rate / 7;
                $('#rate_date').text(rate.toFixed(2) + "%");

                let tot = (interest / 7) * days_from_last_payment_date;
                tot = tot-paid_amount;
                if (tot<0){
                    tot=0.00;
                }
                $('#installment_interest_today').text(tot.toFixed(2));

            } else if (data.loan.Interest_period === "Per Month") {
                let rate = data.loan.Interest_Rate / 30;
                $('#rate_date').text(rate.toFixed(2) + "%");
                let tot = (interest / 30) * days_from_last_payment_date;
                tot = tot-paid_amount;
                if (tot<0){
                    tot=0.00;
                }
                $('#installment_interest_today').text(tot.toFixed(2));
            } else if (data.loan.Interest_period === "Per Year") {
                let rate = data.loan.Interest_Rate / 365;
                $('#rate_date').text(rate.toFixed(2) + "%");
                let tot = (interest / 365) * days_from_last_payment_date;
                tot = tot-paid_amount;
                if (tot<0){
                    tot=0.00;
                }
                $('#installment_interest_today').text(tot.toFixed(2));
            }


            $('#last_payment_date').text(data.Paid_Date ? data.Paid_Date : '-');

            $('#interest_type').text(data.loan.Interest_period);
            $('#interest_rate').text(data.loan.Interest_Rate + "%");
            $('#next_payment_date').text(data.next_installment_date);
            $('#days_from_last_payment_date').text(data.days_from_last_payment_date);
            $('#ins_capital').text(data.capital);
            $('#installment_interest').text(data.interest);

            let pending_amount = parseFloat($('#pending_amount').text());
            let installment_interest_today = parseFloat($('#installment_interest_today').text());
            let tot_amount = pending_amount + installment_interest_today;
            $('#required_payment_before_capital').text(tot_amount.toFixed(2));
        },
    });
}




function payment_model_3(cus_id, pending_amount, loan_id, topic, loan_capital_balance,loan_balance,Total_Balance_until) {
    $('#cus_id_2').val(cus_id);
    $('#loan_balance_2').val(loan_balance.toFixed(2));
    $('#reduce_balance_loan_id').val(loan_id);
    $('#pending_amount').text(parseFloat(Total_Balance_until).toFixed(2));
    $('#topic').text(topic);
    $('#loan_capital_balance').text(parseFloat(loan_capital_balance).toFixed(2));


    $.ajax({
        type: "GET",
        url: "/loanviewajax/" + loan_id,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data, textStatus, xhr) {
            let interest = parseFloat(data.interest);
            let paid_amount = parseFloat(data.paid_amount);
            console.log(paid_amount);
            let days_from_last_payment_date = parseFloat(data.days_from_last_payment_date);
            if (data.loan.Interest_period === "Daily") {
                $('#rate_date').text(data.loan.Interest_Rate + "%");
                let tot=interest * days_from_last_payment_date;
                tot = tot-paid_amount;
                if (tot<0){
                    tot=0.00;
                }
                $('#installment_interest_today').text(parseFloat(tot).toFixed(2));
            } else if (data.loan.Interest_period === "Weekly") {
                let rate = data.loan.Interest_Rate / 7;
                $('#rate_date').text(rate.toFixed(2) + "%");

                let tot = (interest / 7) * days_from_last_payment_date;
                tot = tot-paid_amount;
                if (tot<0){
                    tot=0.00;
                }
                $('#installment_interest_today').text(tot.toFixed(2));

            } else if (data.loan.Interest_period === "Per Month") {
                let rate = data.loan.Interest_Rate / 30;
                $('#rate_date').text(rate.toFixed(2) + "%");
                let tot = (interest / 30) * days_from_last_payment_date;
                tot = tot-paid_amount;
                if (tot<0){
                    tot=0.00;
                }
                $('#installment_interest_today').text(tot.toFixed(2));
            } else if (data.loan.Interest_period === "Per Year") {
                let rate = data.loan.Interest_Rate / 365;
                $('#rate_date').text(rate.toFixed(2) + "%");
                let tot = (interest / 365) * days_from_last_payment_date;
                tot = tot-paid_amount;
                if (tot<0){
                    tot=0.00;
                }
                $('#installment_interest_today').text(tot.toFixed(2));
            }


            $('#last_payment_date').text(data.Paid_Date ? data.Paid_Date : '-');

            $('#interest_type').text(data.loan.Interest_period);
            $('#interest_rate').text(data.loan.Interest_Rate + "%");
            $('#next_payment_date').text(data.next_installment_date);
            $('#days_from_last_payment_date').text(data.days_from_last_payment_date);
            $('#ins_capital').text(data.capital);
            $('#installment_interest').text(data.interest);

            let pending_amount = parseFloat($('#pending_amount').text());
            let installment_interest_today = parseFloat($('#installment_interest_today').text());
            let tot_amount = pending_amount + installment_interest_today;
            $('#required_payment_before_capital').text(tot_amount.toFixed(2));
        },
    });
}



let extraAmount = 0;
$('#payment_amount').on('blur', function () {
    let total_loan_balance = parseFloat($('#total_loan_balance').val()) || 0;
    let payment_amount = parseFloat($('#payment_amount').val()) || 0;

    // Round up to nearest 10
    let maxAllowed = Math.ceil(total_loan_balance / 10) * 10;

    if (payment_amount > maxAllowed) {
        Swal.fire("Error!", `Payment cannot exceed ${maxAllowed}. Please check the amount!`, "error");
        $('#payment_amount').val('');
        extraAmount = 0;
        return false;
    }

    if (payment_amount > total_loan_balance) {
        extraAmount = +(payment_amount - total_loan_balance).toFixed(2);  // store it
        Swal.fire({
            icon: 'info',
            title: 'Extra Amount Detected',
            html: `
                <p><strong>Loan Balance:</strong> ${total_loan_balance}</p>
                <p><strong>You Tried to Pay:</strong> ${payment_amount}</p>
                <p><strong>Extra:</strong> ${extraAmount} will go to <u>Installment Part Payment Entry</u></p>
            `,
            confirmButtonText: 'OK'
        });
    } else {
        extraAmount = 0;
    }
});




function payment() {
    let cus_id = $('#cus_id').val();
    let payment_amount = $('#payment_amount').val();
    let saving_amount = $('#saving_amount').val() || 0.00;
    let reduce_balance_loan_id = $('#reduce_balance_loan_id').val();
    let file = $('#file')[0].files[0];
    let payment_date = $('#payment_date').val();
    let payment_type = $('#payment_type').val();
    let bank_account_company = $('#bank_account_company').val();
    let cheque_issue_bank = $('#cheque_issue_bank').val();
    let name_on_cheque = $('#name_on_cheque').val();
    let chq_number = $('#chq_number').val();
    let chq_date = $('#chq_date').val();
    let chq_type = $('#chq_type').val();
    let total_loan_balance = $('#total_loan_balance').val();

    if (payment_type === "Cheque") {
        if (chq_number === "") {
            Swal.fire("Error!", "Please Enter Cheque Number!", "error");
        } else if (chq_date === "") {
            Swal.fire("Error!", "Please Enter Cheque Date!", "error");
        } else if (chq_type === "") {
            Swal.fire("Error!", "Please Enter Cheque Type!", "error");
        } else {
            if (payment_amount === "") {
                Swal.fire("Error!", "Please Enter Paid Amount!", "error");
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
                        $('.btn-success').prop('disabled', true);
                        // Show loading alert
                        Swal.fire({
                            title: "Processing...",
                            text: "Please wait while we process your payment.",
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        let formData = new FormData();
                        formData.append('cus_id', cus_id);
                        formData.append('payment_amount', payment_amount);
                        formData.append('saving_amount', saving_amount);
                        formData.append('file', file);
                        formData.append('loan_id', reduce_balance_loan_id);
                        formData.append('payment_date', payment_date);
                        formData.append('payment_type', payment_type);
                        formData.append('bank_account_company', bank_account_company);
                        formData.append('cheque_issue_bank', cheque_issue_bank);
                        formData.append('name_on_cheque', name_on_cheque);
                        formData.append('chq_number', chq_number);
                        formData.append('chq_date', chq_date);
                        formData.append('chq_type', chq_type);
                        formData.append('extraAmount', extraAmount);
                        formData.append('total_loan_balance', total_loan_balance);

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
                                let payment_id = data.payment_id;
                                if (xhr.status === 200) {
                                    Swal.fire({
                                        position: "center",
                                        icon: "success",
                                        title: "Successfully saved!",
                                    }).then(function () {
                                        if (payment_id===0){
                                            load_payment_table(currentLoadedPage);
                                            $("#printerModal").fadeOut();
                                            $("#issue-loan-modal").fadeOut();
                                            $("#issue-loan-modal_2").fadeOut();
                                            $("#payment_amount").val("");
                                            $("#payment_amount_2").val("");
                                            $('.btn-success').prop('disabled', false);
                                        }else{
                                            load_payment_reciept(payment_id);
                                        }
                                    });
                                } else {
                                    Swal.fire("Error!", "Failed to save data!", "error");
                                }
                            },
                            error: function () {
                                Swal.fire("Error!", "Failed to save data!", "error");
                            },
                            complete: function () {
                                Swal.close(); // Close loading alert after completion
                            }
                        });
                    }
                });
            }
        }
    } else {
        if (payment_amount === "") {
            Swal.fire("Error!", "Please Enter Paid Amount!", "error");
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
                    $('.btn-success').prop('disabled', true);
                    // Show loading alert
                    Swal.fire({
                        title: "Processing...",
                        text: "Please wait while we process your payment.",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    let formData = new FormData();
                    formData.append('cus_id', cus_id);
                    formData.append('payment_amount', payment_amount);
                    formData.append('saving_amount', saving_amount);
                    formData.append('file', file);
                    formData.append('loan_id', reduce_balance_loan_id);
                    formData.append('payment_date', payment_date);
                    formData.append('payment_type', payment_type);
                    formData.append('bank_account_company', bank_account_company);
                    formData.append('cheque_issue_bank', cheque_issue_bank);
                    formData.append('name_on_cheque', name_on_cheque);
                    formData.append('chq_number', chq_number);
                    formData.append('chq_date', chq_date);
                    formData.append('chq_type', chq_type);
                    formData.append('extraAmount', extraAmount);
                    formData.append('total_loan_balance', total_loan_balance);

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
                            let payment_id = data.payment_id;
                            if (xhr.status === 200) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Successfully saved!",
                                }).then(function () {
                                    if (payment_id===0){
                                        load_payment_table(currentLoadedPage);
                                        $("#printerModal").fadeOut();
                                        $("#issue-loan-modal").fadeOut();
                                        $("#issue-loan-modal_2").fadeOut();
                                        $("#payment_amount").val("");
                                        $("#payment_amount_2").val("");
                                        $('.btn-success').prop('disabled', false);
                                    }else{
                                        load_payment_reciept(payment_id);
                                    }
                                });
                            } else {
                                Swal.fire("Error!", "Failed to save data!", "error");
                            }
                        },
                        error: function () {
                            Swal.fire("Error!", "Failed to save data!", "error");
                        },
                        complete: function () {
                            Swal.close(); // Close loading alert after completion
                        }
                    });
                }
            });
        }
    }
}



function payment_2() {
    let cus_id = $('#cus_id_2').val();
    let payment_amount = $('#payment_amount_2').val();
    let reduce_balance_loan_id = $('#reduce_balance_loan_id').val();
    let interest_type = $('#interest_type').text();
    let interest_rate = $('#interest_rate').text();
    let rate_date = $('#rate_date').text();
    let ins_capital = $('#ins_capital').text();
    let installment_interest = $('#installment_interest').text();
    let installment_interest_today = $('#installment_interest_today').text();
    let required_payment_before_capital = $('#required_payment_before_capital').text();
    let pending_amount = $('#pending_amount').text();
    let payment_type = $('#payment_type_2').val();

    let payment_date = $('#payment_date_2').val();

    let bank_account_company = $('#bank_account_company_2').val();
    let cheque_issue_bank = $('#cheque_issue_bank_2').val();
    let name_on_cheque = $('#name_on_cheque_2').val();
    let chq_number = $('#chq_number_2').val();
    let chq_date = $('#chq_date_2').val();
    let chq_type = $('#chq_type_2').val();

    let file = $('#file')[0].files[0];


    let loan_balance = $('#loan_balance_2').val();


    if (parseFloat(payment_amount)>parseFloat(loan_balance)){
        Swal.fire("Error!", "Please check the amount !", "error");
        return false;
    }




    if (payment_type === "Cheque") {
        if (chq_number === "") {
            Swal.fire("Error!", "Please Enter Cheque Number !", "error");
        } else if (chq_date === "") {
            Swal.fire("Error!", "Please Enter Cheque Date !", "error");
        } else if (chq_type === "") {
            Swal.fire("Error!", "Please Enter Cheque Type !", "error");
        } else {
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

                        formData.append('interest_type', interest_type);
                        formData.append('interest_rate', interest_rate);
                        formData.append('rate_date', rate_date);
                        formData.append('ins_capital', ins_capital);
                        formData.append('installment_interest', installment_interest);
                        formData.append('installment_interest_today', installment_interest_today);
                        formData.append('required_payment_before_capital', required_payment_before_capital);
                        formData.append('pending_amount', pending_amount);
                        formData.append('loan_id', reduce_balance_loan_id);
                        formData.append('payment_date', payment_date);
                        formData.append('payment_type', payment_type);
                        formData.append('bank_account_company', bank_account_company);
                        formData.append('cheque_issue_bank', cheque_issue_bank);
                        formData.append('name_on_cheque', name_on_cheque);
                        formData.append('chq_number', chq_number);
                        formData.append('chq_date', chq_date);
                        formData.append('chq_type', chq_type);

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
                                    let payment_id = data.payment_id;
                                    if (data.id === "1") {
                                        Swal.fire({
                                            position: "center",
                                            icon: "success",
                                            title: "Successfully saved!",
                                        }).then(function () {
                                            if (payment_id===0){
                                                load_payment_table(currentLoadedPage);
                                                $("#printerModal").fadeOut();
                                                $("#issue-loan-modal").fadeOut();
                                                $("#issue-loan-modal_2").fadeOut();
                                                $("#payment_amount").val("");
                                                $("#payment_amount_2").val("");
                                                $('.btn-success').prop('disabled', false);
                                            }else{
                                                load_payment_reciept(payment_id);
                                            }
                                        });
                                    } else {
                                        Swal.fire("Error!", "You Have To Pay Total Pending Amount Before Capital Reducing !", "error");
                                    }
                                } else {
                                    Swal.fire("Error!", "Failed to save data!", "error");
                                }
                            },
                        });
                    }
                });
            }
        }
    } else {

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

                    formData.append('interest_type', interest_type);
                    formData.append('interest_rate', interest_rate);
                    formData.append('rate_date', rate_date);
                    formData.append('ins_capital', ins_capital);
                    formData.append('installment_interest', installment_interest);
                    formData.append('installment_interest_today', installment_interest_today);
                    formData.append('required_payment_before_capital', required_payment_before_capital);
                    formData.append('pending_amount', pending_amount);
                    formData.append('loan_id', reduce_balance_loan_id);
                    formData.append('payment_date', payment_date);
                    formData.append('payment_type', payment_type);
                    formData.append('bank_account_company', bank_account_company);
                    formData.append('cheque_issue_bank', cheque_issue_bank);
                    formData.append('name_on_cheque', name_on_cheque);
                    formData.append('chq_number', chq_number);
                    formData.append('chq_date', chq_date);
                    formData.append('chq_type', chq_type);

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
                                let payment_id = data.payment_id;
                                if (data.id === "1") {
                                    Swal.fire({
                                        position: "center",
                                        icon: "success",
                                        title: "Successfully saved!",
                                    }).then(function () {
                                        if (payment_id===0){
                                            load_payment_table(currentLoadedPage);
                                            $("#printerModal").fadeOut();
                                            $("#issue-loan-modal").fadeOut();
                                            $("#issue-loan-modal_2").fadeOut();
                                            $("#payment_amount").val("");
                                            $("#payment_amount_2").val("");
                                            $('.btn-success').prop('disabled', false);
                                        }else{
                                            load_payment_reciept(payment_id);
                                        }
                                    });
                                } else {
                                    Swal.fire("Error!", "You Have To Pay Total Pending Amount Before Capital Reducing !", "error");
                                }
                            } else {
                                Swal.fire("Error!", "Failed to save data!", "error");
                            }
                        },
                    });
                }
            });
        }
    }
}

//
// function load_payment_reciept(id){
//     document.getElementById('issue-loan-modal').style.display = 'none';
//     document.getElementById('issue-loan-modal_2').style.display = 'none';
//     openModal();
//     $.ajax({
//         type: "POST",
//         url: "/view_payment_load_reciept/"+id,
//         headers: {
//             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
//         },
//         success: function (data, textStatus, xhr) {
//             console.log(data);
//             let customer=data.customer;
//             let payment=data.payment;
//             let loan=data.loan;
//             let user=data.user;
//
//             if (xhr.status === 200) {
//
//                 $("#customer_name").text(customer.First_Name);
//                 $("#customer_number").text(customer.cus_number);
//                 $("#loan_number").text(loan.Loan_No);
//                 $("#payment_date").text(payment.Date);
//                 $("#payment_time").text(payment.time);
//
//
//                 $("#loan_amount").text(parseFloat(loan.Amount).toFixed(2));
//                 $("#payed_amount").text(parseFloat(payment.Amount).toFixed(2));
//                 $("#capital_balance").text(loan.capital_balance);
//
//
//                 $("#signature").text(user.Full_Name);
//             }
//         }
//     });
// }


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



function upload_excel() {
    var fileInput = document.getElementById('uploadExcel');
    var file = fileInput.files[0];

    if (!file) return;

    var reader = new FileReader();

    reader.onload = function(e) {
        var data = new Uint8Array(e.target.result);
        var workbook = XLSX.read(data, { type: 'array' });
        var firstSheet = workbook.Sheets[workbook.SheetNames[0]];
        var jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });

        // Adjust this slice if you want to skip more rows (currently skips only the first row)
        var dataFrom5thRow = jsonData.slice(2);

        if (dataFrom5thRow.length === 0) {
            Swal.fire('No data', 'No valid data found in Excel.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to upload the Excel data?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, upload it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (!result.isConfirmed) {
                Swal.fire('Cancelled', 'Your Excel data upload was cancelled.', 'error');
                return;
            }

            // Show progress modal
            Swal.fire({
                title: 'Uploading...',
                html: `<div style="width: 100%; background: #eee; border-radius: 5px;">
                          <div id="upload-progress" style="width: 0%; height: 20px; background: #28a745; border-radius: 5px;"></div>
                       </div>
                       <div id="progress-text" style="margin-top: 10px;">0 / ${dataFrom5thRow.length}</div>`,
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: async () => {
                    const progressBar = document.getElementById('upload-progress');
                    const progressText = document.getElementById('progress-text');

                    for (let i = 0; i < dataFrom5thRow.length; i++) {
                        try {
                            await $.ajax({
                                url: '/upload-excel-customer-id',
                                method: 'POST',
                                data: {
                                    row: dataFrom5thRow[i]
                                },
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                                }
                            });

                            const percentage = Math.round(((i + 1) / dataFrom5thRow.length) * 100);
                            progressBar.style.width = `${percentage}%`;
                            progressText.innerHTML = `${i + 1} / ${dataFrom5thRow.length}`;
                        } catch (err) {
                            console.error(`Upload failed on row ${i + 1}`, err);
                            // Optional: skip failed row and continue
                        }
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Upload complete!',
                        text: `${dataFrom5thRow.length} rows uploaded successfully.`,
                        confirmButtonText: 'Reload'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        });
    };

    reader.readAsArrayBuffer(file);
}



