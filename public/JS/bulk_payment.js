$(function () {

    load_payment_table();
});

function load_payment_table(page = 1) {
    let center_details = $("#center_details").val();
    let group = $("#group").val();
    let customer = $("#customer_id").val();
    let status = $("#status").val();
    let route = $("#route").val();
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
            route:route,
            loan_number_search: loan_number_search,
            status: status
        },
        success: function (response, textStatus, xhr) {
            if (xhr.status === 200) {
                let data = response.item.data;
                console.log(data);
                let currentPage = response.item.current_page;
                let lastPage = response.item.last_page;
                const getTotalData = response.gettotal;

                let tot = 0.0;

// Clear existing table rows
                $("#loan_table tbody").empty();

                // Loop through each item in the response data
                data.forEach(function (item) {


                    if (item.type==="Flat Rate"){

                        // Determine which modal button to use based on item type


                        let name = item.customer_name + " " + item.customer_lastname;
                        // Construct row HTML
                        var row = `<tr>
                        <td>${item.Loan_No}</td>
                        <td>${name}</td>
                        <td>${parseFloat(item.Loan_Amount).toFixed(2)}</td>
                        <td>${parseFloat(item.Today_installment).toFixed(2)}</td>
                        <td><input type="date" name="date_bulk" id="date_bulk"  class="form-control"></td>
                        <td>
                            <input type="text" class="form-control numeric-input" placeholder="Enter amount" data-id="${item.idCustomer_Loan}" />
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


                // Update total amount
                $("#tot_amount").text(tot.toFixed(2));

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

// Allow only numeric input
$(document).on('input', '.numeric-input', function () {
    let value = $(this).val();
    // Remove non-numeric characters and allow only one decimal point
    $(this).val(value.replace(/[^\d.]/g, '').replace(/^(\d*\.?)|(\d*)\.?/g, '$1$2'));
});

function automatePayments() {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to pay these installments?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Pay it!",
    }).then((result) => {
        if (result.isConfirmed) {
            let errorOccurred = false;  // Flag to track error
            let successCount = 0; // Counter to track successful payments

            // Loop through each table row
            $('#loan_table tbody tr').each(function (index, row) {
                // Extract data from current row
                let loan_number = $(row).find('td:eq(0)').text();
                let payment_amount = $(row).find('input.numeric-input').val();
                let reduce_balance_loan_id = $(row).find('input[name="loan_id"]').val();
                let cus_id = $(row).find('input[name="cus_id"]').val();
                let payment_date = $(row).find('input[name="date_bulk"]').val();

                // Check if both payment_amount and payment_date are empty
                if (!payment_amount && !payment_date) {
                    return true; // Skip this row (continue with the next row)
                }

                // If only payment_amount is empty, show alert
                if (!payment_amount) {
                    Swal.fire("Error!", 'Payment amount is empty in loan number ' + loan_number, "error");
                    errorOccurred = true;
                    return true; // Skip this row (continue with the next row)
                }

                // If only payment_date is empty, show alert
                if (!payment_date) {
                    Swal.fire("Error!", 'Payment date is empty in loan number ' + loan_number, "error");
                    errorOccurred = true;
                    return true; // Skip this row (continue with the next row)
                }

                // Perform payment if both payment amount and payment date are valid
                performPayment(cus_id, payment_amount, reduce_balance_loan_id, payment_date);
                successCount++; // Increment the successful payment counter
            });

            // Check if any successful payment was made
            if (successCount > 0) {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Successfully saved!",
                }).then(function () {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "No valid payments found.",
                    text: "Please check the payment amounts and dates."
                });
            }
        }
    });
}




function performPayment(cus_id, payment_amount, reduce_balance_loan_id, payment_date,type) {


    let file = $('#file')[0].files[0];
    // Prepare FormData
    let formData = new FormData();
    formData.append('cus_id', cus_id);
    formData.append('payment_amount', payment_amount);
    formData.append('file', file);
    formData.append('loan_id', reduce_balance_loan_id);
    formData.append('payment_date', payment_date);
    formData.append('payment_type', 'Cash');
    formData.append('bank_account_company', '1');

    // Perform AJAX request to save payment
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
                return true;
            } else {
                return false;
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            Swal.fire("Error!", "Failed to save data: " + errorThrown, "error");
        }
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


