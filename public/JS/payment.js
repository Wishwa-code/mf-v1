$(function () {
    load_table();
});
function load_table(page = 1) {
    let group = $("#group").val();
    let category = $("#category").val();
    let customer = $("#customer_id").val();
    let center_details = $("#center_details").val();
    let route = $("#route").val();
    let loan_number_search = $("#loan_number_search").val();
    let from_date = $("#from_date").val();
    let to_date = $("#to_date").val();

    $.ajax({
        type: "GET",
        url: `/payment_step_1_loan_load?page=${page}`,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            group: group,
            category: category,
            center_details: center_details,
            route: route,
            loan_number_search: loan_number_search,
            customer: customer,
            from_date: from_date,
            to_date: to_date
        },
        success: function(response) {
            let data = response.item.data; // paginated data
            let total = response.totals;
            let designation = response.designation;
            let loan_agreement = response.loan_agreement;
            let extra_charge = response.extra_charge;
            let current_loan_delete = response.current_loan;
            let currentPage = response.item.current_page;
            let lastPage = response.item.last_page;

            // Clear the table body
            $("#loan_table tbody").empty();

            let tot = parseFloat(total.totalPendingAmount);

            // Calculate total capital balance
            let capital_balance = parseFloat(total.totalCapitalBalance);

            // Calculate total loan amount
            let loan_amount = parseFloat(total.totalLoanAmount);

            let loan_count = total.totalLoanCount;

            // Loop through each item in the response data
            data.forEach(function(item) {
                let agreementButton = '';

                if (loan_agreement == '1') {
                    agreementButton = `
            <a href="#" data-bs-toggle="modal" onclick="agreement(${item.idCustomer_Loan})" data-bs-target="#agreement" class="btn btn-dark">
                <i class="bi bi-receipt"></i>
            </a>`;
                }

                let deleteButton = '';

                if (current_loan_delete == '1') {
                    deleteButton = `
        <button onclick="deleteLoan(${item.idCustomer_Loan})" class="btn btn-outline-danger">
            <i class="bi bi-trash"></i>
        </button>`;
                }

                let extra_chargeButton = '';

                if (extra_charge == '1') {
                    extra_chargeButton = `
        <a href="javascript:void(0)" class="btn btn-info" onclick="openExtraChargeModal(${item.idCustomer_Loan})">
                                <i class="bi bi-plus-circle"></i>
                            </a>`;
                }



                // Add row data to the table
                $("#loan_table tbody").append(`
                    <tr>
                        <td>${item.Loan_No}</td>
                        <td>${item.route_name}</td>
                        <td>${item.center_no}</td>
                        <td>${item.group_name}</td>
                        <td>${formatName(item.First_Name, item.Last_Name)}</td>
                        <td>${item.loan_name}</td>
                        <td>${parseFloat(item.Amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td>${(function(){
                            const due = (item.Installment_Amount ?? item.due_amount ?? item.installment_amount ?? 0);
                            const n = parseFloat(due) || 0;
                            return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        })()}</td>
                        <td>${parseFloat(item.capital_balance).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td>${parseFloat(item.Total_Loan_Amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td>${(
                    (parseFloat(item.Balance_Amount) || 0) +
                    (parseFloat(item.total_penalty) || 0)
                ).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td>${item.Date_Time}</td>
                        <td>${item.lending_officer}</td>
                        <td>${item.user_name}</td>
                        <td>
                            <a href="/loanview/${item.idCustomer_Loan}" target="_blank" class="btn btn-warning me-2"><i class="bi bi-eye"></i></a>
                            <a href="/invoice/${item.idCustomer_Loan}" target="_blank" class="btn btn-danger"><i class="bi bi-file-earmark-text"></i></a>
                            ${agreementButton} 
                            ${extra_chargeButton} 
                       
                        </td>
                    </tr>
                `);

            });

            // Update total amount and loan count
            $('#tot_amount').text(tot.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#cap_balance').text(capital_balance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#loan_amount').text(loan_amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#loan_count').text(loan_count);

            // Add pagination controls
            let paginationControls = '';

            if (currentPage > 1) {
                paginationControls += `
        <button onclick="load_table(${currentPage - 1})" class="btn btn-sm btn-outline-primary me-2">
            <i class="bi bi-arrow-left-circle me-1"></i> Previous
        </button>`;
            }

            if (currentPage < lastPage) {
                paginationControls += `
        <button onclick="load_table(${currentPage + 1})" class="btn btn-sm btn-outline-primary">
            Next <i class="bi bi-arrow-right-circle ms-1"></i>
        </button>`;
            }

            document.getElementById('pagination').innerHTML = paginationControls;

            $('#pagination').html(paginationControls);
        },
        error: function(xhr, textStatus, errorThrown) {
            console.log("Error:", errorThrown);
        }
    });
}

// Function to format the name as required
function formatName(firstName, lastName) {
    let firstNameParts = firstName.trim().split(' ');
    let initials = firstNameParts.map(name => name.charAt(0).toUpperCase() + '.').join('');

    let lastNameParts = lastName.trim().split(' ');
    let formattedLastName = lastNameParts[lastNameParts.length - 1];

    return `${initials} ${formattedLastName}`;
}

function deleteLoan(loanId) {
    $('#deleteLoanId').val(loanId);
    $('#deleteReason').val('');
    $('#deleteLoanModal').modal('show');
}

function confirmLoanDelete() {
    const loanId = $('#deleteLoanId').val();
    const reason = $('#deleteReason').val();

    if (!reason.trim()) {
        Swal.fire('Reason Required', 'Please enter a reason for deleting this loan.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: "This will mark the loan as deleted.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, update it'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/loan-delete',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    loan_id: loanId,
                    reason: reason
                },
                success: function (res) {
                    Swal.fire('Deleted!', 'Loan has been updated.', 'success');
                    $('#deleteLoanModal').modal('hide');
                    load_table(); // Refresh the table if you're using it
                },
                error: function () {
                    Swal.fire('Error', 'Something went wrong while updating.', 'error');
                }
            });
        }
    });
}



function openExtraChargeModal(loanId) {
    $('#modalLoanId').val(loanId);
    $('#extra_date').val('');
    $('#extra_description').val('');
    $('#extra_amount').val('');
    $('#extraChargesTableBody').html('');
    $('#extraChargeModal').modal('show');

    $.ajax({
        url: '/get-extra-charges',
        type: 'GET',
        data: { loan_id: loanId },
        success: function (res) {
            let rows = '';
            res.forEach(function (item) {
                rows += `<tr>
                    <td>${item.date}</td>
                    <td>${item.description}</td>
                    <td>${parseFloat(item.amount).toFixed(2)}</td>
                </tr>`;
            });
            $('#extraChargesTableBody').html(rows);
        }
    });
}

function saveExtraCharge() {
    const loanId = $('#modalLoanId').val();
    const date = $('#extra_date').val();
    const description = $('#extra_description').val();
    const amount = $('#extra_amount').val();
    const chargeCodeId = $('#extra_charge_type').val();
    const chargeCodeText = $('#extra_charge_type option:selected').text();
    const chargeCodeDescription = (chargeCodeId && chargeCodeText !== '-- Select Charge Type --') ? chargeCodeText : null;


    if (!date || !description || !amount) {
        Swal.fire({
            icon: 'warning',
            title: 'All fields are required!',
            confirmButtonText: 'OK'
        });
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        html: `<b>Date:</b> ${date}<br><b>Description:</b> ${description}<br><b>Amount:</b> Rs. ${parseFloat(amount).toFixed(2)}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Save',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/save-extra-charge',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content") },
                data: {
                    loan_id: loanId,
                    date: date,
                    description: description,
                    amount: amount,
                    other_charges_code_id: chargeCodeId ? chargeCodeId : null,
                    other_charges_codes_discription: chargeCodeDescription
                },
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved successfully!',
                        timer: 1200,
                        showConfirmButton: false
                    });
                    openExtraChargeModal(loanId);
                }
            });
        }
    });
}





function agreement(id) {
    $.ajax({
        type: "GET",
        url: "/load_agreement_doc", // Ensure this endpoint returns the updated JSON data
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data, textStatus, xhr) {
            console.log(data);
            var tbody = $("#agreement_table tbody");
            tbody.empty(); // Clear existing data

            // Check if 'data.agreement' is an array
            if (data && Array.isArray(data.agreement)) {
                // Iterate over the array and create rows for each agreement
                data.agreement.forEach(function(agreement) {


                    var url;
                    switch (agreement.type) {
                        case 'voucher':
                            url = `generate-voucher-pdf/${id}`;
                            break;
                        case 'Facilities Approval Form':
                            url = `generate-loan-pdf/${id}`;
                            break;
                        default:
                            url = `agreement_view/${agreement.type}/${id}`;
                            break;
                    }




                    var row = `<tr>
                                    <td hidden>${agreement.id}</td>
                                    <td class="text-center">${agreement.type}</td>
                                    <td  class="text-center">
                                        <a href="${url}" target="_blank" data-bs-target="#agreement" class="btn btn-dark"><i class="bi bi-receipt"></i></a>
                                    </td>
                                </tr>`;

                    tbody.append(row);
                });
            } else {
                console.log("No data available or data format is incorrect.");
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log("Error:", errorThrown);
        }
    });
}




function load_details(id,loan_id){

    window.location.href = "/payment_step_1_show_loan/" + id+"/"+loan_id;
}



function payment_page(id){
    window.location.href = "/payment_step_2/" + id;
}


function payment_model(amount){

}

function payment(){

    let loan_id=$('#loan_id').val();
    let payment_amount=$('#payment_amount').val();

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to pay this installment ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Pay it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "/payment_save",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    loan_id: loan_id,
                    payment_amount: payment_amount
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
