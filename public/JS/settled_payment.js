$(function () {
    load_table();
});


function load_table() {
    let group = $("#group").val();
    let category = $("#category").val();
    let customer = $("#customer_id").val();
    let route = $("#route").val();

    $.ajax({
        type: "GET",
        url: "/showsettleloan_filter",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            group: group,
            category: category,
            route: route,
            customer: customer
        },
        success: function(data, textStatus, xhr) {
            console.log(data);
            if (xhr.status === 200) {
                // Initialize DataTable if not already initialized
                if (!$.fn.DataTable.isDataTable('#loan_table')) {
                    $('#loan_table').DataTable({
                        // DataTables options here
                        "paging": true,
                        "lengthMenu": [10, 25, 50, 100],
                        // Add more options as needed
                    });
                }

                let tot = 0.0;
                let loan_count = 0;

                // Clear DataTable rows
                $('#loan_table').DataTable().clear().draw();

                // Loop through each item in the response data
                data.item.forEach(function(item) {
                    // Calculate total loan amount
                    let amount = parseFloat(item.Total_Loan_Amount);
                    tot += amount;

                    // Add row data to DataTable
                    $('#loan_table').DataTable().row.add([
                        item.Loan_No,
                        item.route_name,
                        item.group_name,
                        item.First_Name + ' ' + item.Last_Name,
                        item.loan_name,
                        parseFloat(item.Amount).toFixed(2),
                        item.Total_Loan_Amount,
                        item.Date_Time,
                        item.lending_officer,
                        item.user_name,
                        '<a href="/loanview/' + item.idCustomer_Loan + '" target="_blank" class="btn btn-warning me-2"><i class="bi bi-eye"></i></a>'
                    ]).draw(false);

                    loan_count++;
                });

                // Update total amount and loan count
                $('#tot_amount').text(tot.toFixed(2));
                $('#loan_count').text(loan_count);
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            console.log("Error:", errorThrown);
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
