$(function () {
    load_table();
});


function load_table() {
    let group = $("#group").val();
    let category = $("#category").val();
    let customer = $("#customer_id").val();

    $.ajax({
        type: "GET",
        url: "/loan_settlement_load",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            group: group,
            category: category,
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
                        item.group_name,
                        item.First_Name + ' ' + item.Last_Name,
                        item.loan_name,
                        parseFloat(item.Amount).toFixed(2),
                        parseFloat(item.capital_balance).toFixed(2),
                        item.Total_Loan_Amount,
                        parseFloat(item.Balance_Amount).toFixed(2),
                        item.Date_Time,
                        item.Date_Time,
                        '<a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#standard-modal" data-loan-id="' + item.idCustomer_Loan + '" onclick="load_id(' + item.idCustomer_Loan + ', \'' + item.First_Name + '\', \'' + item.Last_Name + '\', \'' + item.loan_name + '\', \'' + item.Loan_No + '\')">Reshedule</a>\n'
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

function load_id(id,first_name,last_name,category,loan_number) {
    $('#loan-id').val(id);
    $('#cus_name').val(first_name+" "+last_name);
    $('#loan-cate').val(category);
    $('#loan_number').text(loan_number);

}


