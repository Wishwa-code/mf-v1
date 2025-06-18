$(function () {

    load_payment_table();
});

let inputDataStore = {}; // Object to store input data

function load_payment_table(page = 1) {
    let center_details = $("#center_details").val();
    let group = $("#group").val();
    let customer = $("#customer_id").val();
    let status = $("#status").val();
    let route = $("#route").val();
    let loan_number_search = $("#loan_number_search").val();


    var routeText = $('#route option:selected').text();
    var centerText = $('#center_details option:selected').text();
    var groupText = $('#group option:selected').text();
    var customerText = $('#customer_id option:selected').text();
    var statusText = $('#status option:selected').text();
    var loanNumberText = $('#loan_number_search option:selected').text();

    var selectedFiltersHtml = `
        <span class="badge bg-success">Route: ${routeText}</span>
        <span class="badge bg-success">Center: ${centerText}</span>
        <span class="badge bg-success">Group: ${groupText}</span>
        <span class="badge bg-success">Customer: ${customerText}</span>
        <span class="badge bg-success">Status: ${statusText}</span>
        <span class="badge bg-success">Loan No: ${loanNumberText}</span>
    `;

    $('#selected_filters_content').html(selectedFiltersHtml);


    $.ajax({
        type: "POST",
        url: `/today_payment_load_check_bulk`,
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
                let getTotalData = response.gettotal;

                let tot = 0.0;

                // Clear existing table rows
                $("#loan_table tbody").empty();

                // Loop through each item in the response data
                data.forEach(function (item) {
                    if (item.type === "Flat Rate") {
                        let name = item.customer_name + " " + item.customer_lastname;

                        // Use stored values if available
                        let storedInputData = inputDataStore[item.idCustomer_Loan] || {};
                        let inputDate = storedInputData.date || "";
                        let inputAmount = storedInputData.amount || "";
                        let savingAmount = storedInputData.savingAmount || "";

                        // Change placeholder text based on saving_payment
                        let installmentPlaceholder = item.saving_payment === "1" ? "Installment Amount" : "Enter amount";

                        // Define payment field
                        let paymentField = `<input type="text" class="form-control numeric-input amount-input" placeholder="${installmentPlaceholder}" value="${inputAmount}" data-loan-id="${item.idCustomer_Loan}" data-balance="${item.Balance_With_Penalty}" />`;

                        // Add extra input if saving_payment is "1"
                        if (item.saving_payment === "1") {
                            paymentField += `<br><input type="text" class="form-control numeric-input saving-amount-input" placeholder="Enter Saving Amount" value="${savingAmount}" data-loan-id="${item.idCustomer_Loan}" data-balance="${item.Balance_With_Penalty}" />`;
                        }

                        // Construct row HTML
                        var row = `<tr>
                            <td>${item.Loan_No}</td>
                            <td>${name}</td>
                            <td>${item.center_no}</td>
                            <td>${item.group_name}</td>
                            <td>${parseFloat(item.Loan_Amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            <td>${parseFloat(item.Balance_With_Penalty).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            <td>${parseFloat(item.Last_Payment_Amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            <td>${item.Last_Payment_Date}</td>
                            <td>${parseFloat(item.Today_installment).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            <td><input type="date" name="date_bulk" class="form-control" value="${inputDate || new Date().toISOString().split('T')[0]}" data-loan-id="${item.idCustomer_Loan}" /></td>
                            <td>
                                ${paymentField}
                                <input type="hidden" name="loan_id" value="${item.idCustomer_Loan}" />
                                <input type="hidden" name="cus_id" value="${item.idCustomer}" />
                                
                            </td>
                            <td>${item.NIC}</td>
                            <td>${item.type}</td>
                        </tr>`;

                        // Add row to DataTable
                        $("#loan_table tbody").append(row);
                    }
                });

                getTotalData.forEach(function (item) {
                    tot += parseFloat(item.Today_installment);
                });

                // Update total today installment amount
                $('#tot_amount').text(tot.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                $(".amount-input").on("input", function () {
                    let entered = parseFloat($(this).val()) || 0;
                    let max = parseFloat($(this).data("balance")) || 0;

                    if (entered >= max) {
                        Swal.fire({
                            icon: "error",
                            title: "Invalid Payment Amount",
                            text: `Entered amount (${entered.toFixed(2)}) exceeds balance (${max.toFixed(2)}).`,
                        });
                        $(this).val(""); // Clear invalid input
                    }

                    updateTotalEnteredAmount();
                });


                // Initial calculation in case prefilled values exist
                updateTotalEnteredAmount();
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log("Error:", errorThrown);
        },
    });
}


// Function to calculate and update total entered amount
function updateTotalEnteredAmount() {
    let totalEntered = 0.0;

    $(".amount-input").each(function () {
        let enteredValue = parseFloat($(this).val()) || 0;
        totalEntered += enteredValue;
    });

    $('#tot_installment').text(totalEntered.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
}



// Allow only numeric input
$(document).on('input', '.numeric-input', function () {
    let value = $(this).val();
    // Remove non-numeric characters and allow only one decimal point
    $(this).val(value.replace(/[^\d.]/g, '').replace(/^(\d*\.?)|(\d*)\.?/g, '$1$2'));
});

function automatePayments() {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to process all payments?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with the payment processing
            Swal.fire({
                title: "Processing Payments",
                html: `
                    <div style="margin-top: 20px;">
                        <div id="progress-container" style="width: 100%; background: #f3f3f3; border-radius: 8px; overflow: hidden; height: 25px;">
                            <div id="progress-bar" style="height: 100%; width: 0%; background: #4caf50; transition: width 0.3s;"></div>
                        </div>
                        <p style="margin-top: 10px;" id="progress-text">Initializing...</p>
                    </div>
                `,
                showConfirmButton: false,
                allowOutsideClick: false,
                willOpen: async () => {
                    const rows = $('#loan_table tbody tr');
                    let successCount = 0;
                    let processedCount = 0; // Count only valid payments
                    let totalRows = rows.length; // Total rows including empty ones

                    for (let i = 0; i < totalRows; i++) {
                        const row = rows[i];


                        // Get the installment amount (first input field)
                        let payment_amount = $(row).find('input.numeric-input.amount-input').val();
                        let saving_amount = $(row).find('input.saving-amount-input').val() || 0;
                        let reduce_balance_loan_id = $(row).find('input[name="loan_id"]').val();
                        let cus_id = $(row).find('input[name="cus_id"]').val();
                        let payment_date = $(row).find('input[name="date_bulk"]').val();

                        if (!payment_amount || !payment_date) {
                            // Skip rows with empty payment amount or date
                            continue;
                        }

                        processedCount++; // Increment processed count only for valid payments

                        // Update progress text
                        document.getElementById("progress-text").innerText = `Processing payment ${processedCount} of ${totalRows}...`;

                        // Update progress bar
                        const progress = (processedCount / totalRows) * 100;
                        document.getElementById("progress-bar").style.width = `${progress}%`;

                        // Perform payment
                        const success = await performPayment(cus_id, payment_amount, reduce_balance_loan_id, payment_date,saving_amount);
                        if (success) {
                            successCount++;
                        }
                    }

                    if (successCount > 0) {
                        Swal.fire({
                            icon: "success",
                            title: "Payments Complete",
                            text: `${successCount} payments were successfully processed.`,
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "No Payments Processed",
                            text: "No valid payments were made. Please check your data.",
                        });
                    }
                },
            });
        } else {
            // User canceled the action
            Swal.fire({
                icon: "info",
                title: "Action Cancelled",
                text: "Payment processing was not started.",
            });
        }
    });
}



async function performPayment(cus_id, payment_amount, reduce_balance_loan_id, payment_date,saving_amount) {

    let file = $('#file')[0]?.files[0];
    let formData = new FormData();
    formData.append('cus_id', cus_id);
    formData.append('payment_amount', payment_amount);
    formData.append('saving_amount', saving_amount);
    formData.append('file', file || "");
    formData.append('loan_id', reduce_balance_loan_id);
    formData.append('payment_date', payment_date);
    formData.append('payment_type', 'Cash');
    formData.append('bank_account_company', '1');

    return new Promise((resolve) => {
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
                resolve(xhr.status === 200);
            },
            error: function (xhr, textStatus, errorThrown) {
                Swal.fire("Error!", `Failed to save data: ${errorThrown}`, "error");
                resolve(false);
            }
        });
    });
}




// function payment() {
//     let cus_id = $('#cus_id').val();
//     let payment_amount = $('#payment_amount').val();
//     let reduce_balance_loan_id=$('#reduce_balance_loan_id').val();
//     let file = $('#file')[0].files[0];
//     let payment_date=$('#payment_date').val();
//
//     if (payment_amount === "") {
//         Swal.fire("Error!", "Please Enter Paid Amount !", "error");
//     } else {
//         Swal.fire({
//             title: "Are you sure?",
//             text: "Do you want to pay this installment?",
//             icon: "warning",
//             showCancelButton: true,
//             confirmButtonColor: "#3085d6",
//             cancelButtonColor: "#d33",
//             confirmButtonText: "Yes, Pay it!",
//         }).then((result) => {
//             if (result.isConfirmed) {
//                 let formData = new FormData();
//                 formData.append('cus_id', cus_id);
//                 formData.append('payment_amount', payment_amount);
//                 formData.append('file', file);
//                 formData.append('loan_id', reduce_balance_loan_id);
//                 formData.append('payment_date', payment_date);
//
//                 $.ajax({
//                     type: "POST",
//                     url: "/payment_save_today",
//                     headers: {
//                         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
//                     },
//                     data: formData,
//                     processData: false,
//                     contentType: false,
//                     success: function (data, textStatus, xhr) {
//                         let payment_id=data.payment_id;
//                         if (xhr.status === 200) {
//                             Swal.fire({
//                                 position: "center",
//                                 icon: "success",
//                                 title: "Successfully saved!",
//                             }).then(function () {
//                                 load_payment_reciept(payment_id);
//                             });
//                         } else {
//                             Swal.fire("Error!", "Failed to save data!", "error");
//                         }
//                     },
//                 });
//             }
//         });
//     }
// }

//
// function payment_2() {
//     let cus_id = $('#cus_id_2').val();
//     let payment_amount = $('#payment_amount_2').val();
//     let reduce_balance_loan_id=$('#reduce_balance_loan_id').val();
//     let interest_type = $('#interest_type').text();
//     let interest_rate = $('#interest_rate').text();
//     let rate_date = $('#rate_date').text();
//     let ins_capital = $('#ins_capital').text();
//     let installment_interest = $('#installment_interest').text();
//     let installment_interest_today = $('#installment_interest_today').text();
//     let required_payment_before_capital = $('#required_payment_before_capital').text();
//     let pending_amount = $('#pending_amount').text();
//
//     let payment_date=$('#payment_date_2').val();
//
//
//     let file = $('#file')[0].files[0];
//
//     if (payment_amount === "") {
//         Swal.fire("Error!", "Please Enter Paid Amount !", "error");
//     } else {
//         Swal.fire({
//             title: "Are you sure?",
//             text: "Do you want to pay this installment?",
//             icon: "warning",
//             showCancelButton: true,
//             confirmButtonColor: "#3085d6",
//             cancelButtonColor: "#d33",
//             confirmButtonText: "Yes, Pay it!",
//         }).then((result) => {
//             if (result.isConfirmed) {
//                 let formData = new FormData();
//                 formData.append('cus_id', cus_id);
//                 formData.append('payment_amount', payment_amount);
//
//                 formData.append('interest_type', interest_type);
//                 formData.append('interest_rate', interest_rate);
//                 formData.append('rate_date', rate_date);
//                 formData.append('ins_capital', ins_capital);
//                 formData.append('installment_interest', installment_interest);
//                 formData.append('installment_interest_today', installment_interest_today);
//                 formData.append('required_payment_before_capital', required_payment_before_capital);
//                 formData.append('pending_amount', pending_amount);
//                 formData.append('loan_id', reduce_balance_loan_id);
//                 formData.append('payment_date', payment_date);
//
//                 formData.append('file', file);
//
//                 $.ajax({
//                     type: "POST",
//                     url: "/payment_save_today",
//                     headers: {
//                         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
//                     },
//                     data: formData,
//                     processData: false,
//                     contentType: false,
//                     success: function (data, textStatus, xhr) {
//                         if (xhr.status === 200) {
//                             let payment_id=data.payment_id;
//                             if (data.id==="1"){
//                                 Swal.fire({
//                                     position: "center",
//                                     icon: "success",
//                                     title: "Successfully saved!",
//                                 }).then(function () {
//                                     load_payment_reciept(payment_id);
//                                 });
//                             }else{
//                                 Swal.fire("Error!", "You Have To Pay Total Pending Amount Before Capital Reducing !", "error");
//                             }
//                         } else {
//                             Swal.fire("Error!", "Failed to save data!", "error");
//                         }
//                     },
//                 });
//             }
//         });
//     }
// }


