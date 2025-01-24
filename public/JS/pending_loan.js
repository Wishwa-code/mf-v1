$(function () {
    load_table();
    $('#issue_loan_btn').prop('disabled', true);
});


function load_table() {
    let group = $("#group").val();
    let category = $("#category").val();
    let customer = $("#customer_id").val();
    let status_type = $("#status").val();
    let center_details = $("#center_details").val();
    let route = $("#route").val();

    $.ajax({
        type: "GET",
        url: "/pendingloanload",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            group: group,
            category: category,
            customer: customer,
            center_details: center_details,
            route: route,
            status: status_type
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
                    tot += amount; // Assuming `tot` is initialized outside this scope

                    // Add row data to DataTable
                    $('#loan_table').DataTable().row.add([
                        item.Loan_No,
                        item.route_name,
                        item.center_no,
                        item.group_name,
                        item.First_Name + ' ' + item.Last_Name,
                        item.loan_name,
                        parseFloat(item.Amount).toFixed(2),
                        item.Date_Time,
                        item.reason,
                        item.lending_officer,
                        item.user_name,
                        '<span style="color: red">'+item.approved_count+'/'+item.approval_count+'</span>',
                        // Status type conditional rendering
                        status_type === "-1"
                            ? '<span class="px-2" style="background-color: #FFD700;border-radius: 10px; color: white;">Pending</span>'
                            : '<span class="px-2" style="background-color: #ff0000;border-radius: 10px; color: white;">Deleted</span>',

                        // Buttons based on status_type
                        status_type === "-2" ?
                            '' +
                            '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#issue-loan-modal" disabled><i class="ri ri-send-plane-line"></i></button>' +
                            '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#view-modal" disabled><i class="bi bi-trash"></i></button>' +
                            '<a href="/loanview/' + item.idCustomer_Loan + '" target="_blank" class="btn btn-warning"><i class="bi bi-eye"></i></a>' +
                            '' :
                            '' +
                            '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#issue-loan-modal" onclick="issue_request(' + item.idCustomer_Loan + ')"><i class="ri ri-send-plane-line"></i></button>' +
                            '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#view-modal" onclick="delete_request(' + item.idCustomer_Loan + ')"><i class="bi bi-trash"></i></button>' +
                            '<a href="/loanview/' + item.idCustomer_Loan + '" target="_blank" class="btn btn-warning"><i class="bi bi-eye"></i></a>' +
                            '<a href="#" target="_blank" data-bs-toggle="modal" onclick="agreement(' + item.idCustomer_Loan + ')" data-bs-target="#agreement" class="btn btn-dark"><i class="bi bi-receipt"></i></a>' +
                            '<a href="#' + item.idCustomer_Loan + '" data-bs-toggle="modal" data-bs-target="#loan_edit" onclick="change_installment(' + item.idCustomer_Loan + ')"  class="btn btn-info"><i class="bi bi-pen"></i></a>' +
                            '<button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#standard-modal_2" onclick="set_cus(' + item.idCustomer_Loan + ')">' +
                            '<i class="bi bi-camera fs-4"></i></button>'
                    ]).draw(false); // Draw the row without refreshing the DataTable

                    loan_count++; // Increment loan count (assuming `loan_count` is defined)
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




function change_installment(loan_id) {
    $.ajax({
        url: '/get-installments/' + loan_id,  // The route to the controller
        method: 'GET',
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function(response) {
            // Clear the table before appending new data
            $('#installment_table tbody').empty();
            $("#interest_period").val(response.interest);
            $("#saturday_sunday").val(response.saturday_sunday);
            $("#panelty_date_2").val(response.panelty_date_count);
            $("#loan_id").val(loan_id);


            if (response.interest === "Twice A Month") {
                console.log(response.interest)
                $('#twice_a_month').show(); // Show the div
            } else {
                $('#twice_a_month').hide(); // Hide the div
            }

            // Loop through the response data and populate the table
            $.each(response.installment, function(index, installment) {
                let totalBalance = parseFloat(installment.Interest_Balance) + parseFloat(installment.capital_balance);

                var row = '<tr>' +
                    '<td>' + installment.No + '</td>' +
                    '<td>' + installment.Installment_Date + '</td>' +
                    '<td class="text-end">' + installment.Installment_Amount + '</td>' +
                    '<td class="text-end">' + installment.capital_amount + '</td>' +
                    '<td class="text-end">' + installment.interest_amount + '</td>' +
                    '<td class="text-end">' + installment.Panelty_date + '</td>' +
                    '<td class="text-end">' + installment.Panalty_Amount + '</td>' +
                    '<td class="text-end">' + installment.Total_Amount + '</td>' +
                    '<td class="text-end">' + installment.Paid_Amount + '</td>' +
                    '<td class="text-end">' + installment.Panalty_Balance + '</td>' +
                    '<td class="text-end">' + totalBalance.toFixed(2) + '</td>' +
                    '<td class="text-end">' + installment.Total_Balance + '</td>' +
                    '</tr>';

                // Append the row to the table body
                $('#installment_table tbody').append(row);
            });
        },
        error: function(xhr, status, error) {
            console.log("Error loading installments: " + error);
        }
    });
}


function update_installment() {
    // Get loan_id value (Assuming it's stored in a hidden input or similar)
    var loanId = $('#loan_id').val(); // Replace with actual selector for your loan_id input

    // Initialize an array to hold the installment data
    var installmentData = [];

    // Get table body
    var tableBody = $('#installment_table tbody');

    // Loop through each row in the table
    tableBody.find('tr').each(function() {
        var row = $(this);

        // Get data from the current row
        var data = {
            no: row.find('td').eq(0).text(), // Installment No
            installment_date: row.find('td').eq(1).text(), // Installment Date
            penalty_date: row.find('td').eq(5).text(), // Penalty Date
        };

        // Add the data object to the installmentData array
        installmentData.push(data);
    });

    // Show confirmation dialog before sending AJAX request
    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to save the changes to installment dates.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with AJAX request if confirmed
            $.ajax({
                url: '/update-installments', // Adjust the route as needed
                method: 'POST',
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    loan_id: loanId,
                    installments: installmentData,
                },
                success: function(response) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Successfully changed installment dates!",
                    }).then(function () {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    // Handle error (e.g., show an error message)
                    Swal.fire('Error!', 'Failed to update installments: ' + xhr.responseText, 'error');
                }
            });
        } else {
            // User canceled the operation
            Swal.fire({
                icon: 'info',
                title: 'Cancelled',
                text: 'Your changes were not saved.',
            });
        }
    });
}


function addInstallmentDates() {

    let startDate = $("#installment_date").val();
    let panelty_date_2 = parseFloat($("#panelty_date_2").val()); // Get penalty date from input
    let loan_type = $("#interest_period").val();
    let installmentCount = $('#installment_table tbody tr').length;
    let saturday_sunday = $("#saturday_sunday").val();
    let on_a_selected_date_txt=$('#installment_date').val();
    if (startDate.trim() === "" || isNaN(new Date(startDate))) {
        Swal.fire("Error!", "Please enter a valid installment date !", "error");
    } else if (isNaN(parseInt(installmentCount)) || parseInt(installmentCount) <= 0) {
        Swal.fire("Error!", "Please enter a valid installment count greater than zero !", "error");
    } else {
        var selected_date = new Date(startDate);
        var installmentDates = [];
        var currentDate = new Date(startDate);

        function formatDate(date) {
            var year = date.getFullYear();
            var month = (date.getMonth() + 1).toString().padStart(2, '0');
            var day = date.getDate().toString().padStart(2, '0');
            return year + '-' + month + '-' + day;
        }

        if (loan_type === "Daily") {
            for (var i = 0; i < installmentCount;) {
                currentDate.setDate(currentDate.getDate() + 1);

                var dayOfWeek = currentDate.getDay();
                if (saturday_sunday === '1') {
                    if (dayOfWeek !== 0 && dayOfWeek !== 6) { // Skip Saturday and Sunday
                        installmentDates.push(formatDate(currentDate));
                        i++;
                    }
                } else {
                    installmentDates.push(formatDate(currentDate));
                    i++;
                }
            }

            var tableBody = $('#installment_table tbody');

            // Loop through each row and update only the 1st and 5th columns
            installmentDates.forEach(function(date, index) {
                let currentDate = new Date(date);
                currentDate.setDate(currentDate.getDate() + panelty_date_2);
                let formattedPenaltyDate = formatDate(currentDate);

                let row = tableBody.find('tr').eq(index);
                row.find('td').eq(1).text(date); // Update the first column with the installment number
                row.find('td').eq(5).text(formattedPenaltyDate); // Update the fifth column with the penalty date
            });
        }else if (loan_type === "Weekly") {
            // Loop through each installment count
            for (var i = 0; i < installmentCount; i++) {
                // Add 7 days to the current date for weekly installment
                var formattedDate = formatDate(currentDate);
                installmentDates.push(formattedDate);

                // Increment by 7 days for the next weekly installment
                currentDate.setDate(currentDate.getDate() + 7);
            }

            // Get the table body
            var tableBody = $('#installment_table tbody');

            // Loop through the installment dates and update the table rows
            installmentDates.forEach(function(date, index) {
                let currentDate = new Date(date);
                currentDate.setDate(currentDate.getDate() + panelty_date_2); // Add penalty date offset
                let panelty_date = currentDate.toISOString().slice(0, 10);  // Format the penalty date

                // Find the rows in the table
                let rows = tableBody.find('tr');

                // Update only the 1st and 5th columns
                rows.each(function(rowIndex, row) {
                    if (rowIndex === index) { // Match row by index
                        let currentRow = $(row);

                        // Update the 1st column with the row count
                        currentRow.find('td').eq(1).text(date);

                        // Update the 5th column with the penalty date
                        currentRow.find('td').eq(5).text(panelty_date);
                    }
                });
            });
        }else if (loan_type === "First Of The Month") {
            var selected_date = new Date($('#installment_date').val());
            // Check if it's the first day of the month
            if (selected_date.getDate() === 1) {
                if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
                    Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
                } else {
                    var installmentDates = [];
                    var currentDate = new Date(on_a_selected_date_txt);

                    // Function to format date as YYYY-MM-DD
                    function formatDate(date) {
                        var year = date.getFullYear();
                        var month = (date.getMonth() + 1).toString().padStart(2, '0');
                        var day = date.getDate().toString().padStart(2, '0');
                        return year + '-' + month + '-' + day;
                    }

                    // Loop through each installment count
                    for (var i = 0; i < installmentCount; i++) {
                        // Format the date
                        var formattedDate = formatDate(currentDate);
                        installmentDates.push(formattedDate);
                        currentDate.setMonth(currentDate.getMonth() + 1); // Move to the next month
                    }

                    // Get the table body
                    var tableBody = $('#installment_table tbody');

                    // Loop through the installment dates and update the table rows
                    installmentDates.forEach(function (date, index) {
                        let currentDate = new Date(date);
                        currentDate.setDate(currentDate.getDate() + panelty_date_2); // Add penalty date offset
                        let panelty_date = formatDate(currentDate); // Format the penalty date

                        // Find the rows in the table
                        let row = tableBody.find('tr').eq(index);

                        // Update only the 1st and 5th columns
                        if (row.length) { // Ensure the row exists
                            row.find('td').eq(1).text(date); // Update the 1st column with the row count
                            row.find('td').eq(5).text(panelty_date); // Update the 5th column with the penalty date
                        }
                    });
                }
            } else {
                Swal.fire("Error!", "Selected date is not the first day of the month !", "error");
            }
        }else if (loan_type === "End Of The Month") {
            var selected_date = new Date(on_a_selected_date_txt);

            // Get the last day of the month for the selected date
            var lastDayOfMonth = new Date(selected_date.getFullYear(), selected_date.getMonth() + 1, 0);

            // Check if it's the last day of the month
            if (selected_date.getDate() === lastDayOfMonth.getDate()) {
                if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
                    Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
                } else {
                    var installmentDates = [];
                    var currentDate = new Date(on_a_selected_date_txt);

                    // Function to format date as YYYY-MM-DD
                    function formatDate(date) {
                        var year = date.getFullYear();
                        var month = (date.getMonth() + 1).toString().padStart(2, '0');
                        var day = date.getDate().toString().padStart(2, '0');
                        return year + '-' + month + '-' + day;
                    }

                    // Loop through each installment count
                    for (var i = 0; i < installmentCount; i++) {
                        var lastDayOfNextMonth = new Date(selected_date.getFullYear(), selected_date.getMonth() + 1 + i, 0);
                        var formattedDate = formatDate(lastDayOfNextMonth);
                        installmentDates.push(formattedDate);
                    }

                    // Get the table body
                    var tableBody = $('#installment_table tbody');

                    // Loop through the installment dates and update the table rows
                    installmentDates.forEach(function (date, index) {
                        let penaltyDate = new Date(date);
                        penaltyDate.setDate(penaltyDate.getDate() + panelty_date_2);
                        let formattedPenaltyDate = formatDate(penaltyDate);

                        // Find the row to update
                        let row = tableBody.find('tr').eq(index);
                        if (row.length) {
                            row.find('td').eq(1).text(date); // Update the 1st column with the installment number
                            row.find('td').eq(5).text(formattedPenaltyDate); // Update the 5th column with the penalty date
                        }
                    });
                }
            } else {
                Swal.fire("Error!", "Selected date is not the last day of the month !", "error");
            }
        }else if (loan_type === "Twice A Month") {
            let selectedOption = $('#twice_a_month_txt').val(); // Get the selected option value
            var installmentDates = [];
            var currentDate = new Date(on_a_selected_date_txt); // Check the initial date input

            // Check if on_a_selected_date_txt is a valid date
            if (isNaN(currentDate.getTime())) {
                console.error("Invalid initial date:", on_a_selected_date_txt);
                return; // Exit if the initial date is invalid
            }

            // Function to format date as YYYY-MM-DD
            function formatDate(date) {
                var year = date.getFullYear();
                var month = (date.getMonth() + 1).toString().padStart(2, '0');
                var day = date.getDate().toString().padStart(2, '0');
                return year + '-' + month + '-' + day;
            }

            // Calculate installment dates based on the selected option
            for (var i = 0; i < (installmentCount / 2); i++) {
                // Ensure the date is valid before processing
                var selectedDateStr = $('#installment_date').val(); // Get the installment date input


                var selectedDate = new Date(selectedDateStr);

                // Check if selectedDate is valid
                if (isNaN(selectedDate.getTime())) {
                    console.error("Invalid selected date:", selectedDateStr);
                    return; // Exit if the selected date is invalid
                }

                if (selectedOption === "1") { // First Of Month And 15th
                    // First installment on the first of the month
                    var firstOfMonth = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + i, 1);
                    installmentDates.push(formatDate(firstOfMonth));

                    // Second installment on the 15th of the month
                    var fifteenthOfMonth = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + i, 15);
                    installmentDates.push(formatDate(fifteenthOfMonth));
                } else if (selectedOption === "2") { // 15th And End Of Month
                    // First installment on the 15th of the month
                    var fifteenthOfMonth = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + i, 15);
                    installmentDates.push(formatDate(fifteenthOfMonth));

                    // Second installment on the last day of the month
                    lastDayOfMonth = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + i + 1, 0);
                    installmentDates.push(formatDate(lastDayOfMonth));
                }
            }

            // Get the table body
            var tableBody = $('#installment_table tbody');

            // Loop through the installment dates and update the table rows
            installmentDates.forEach(function (date, index) {
                let currentDate = new Date(date);

                // Check if currentDate is a valid date
                if (isNaN(currentDate.getTime())) {
                    console.error("Invalid date:", date);
                    return; // Skip this iteration if the date is invalid
                }

                // Add penalty date offset
                currentDate.setDate(currentDate.getDate() + panelty_date_2);

                // Format the penalty date
                let panelty_date = currentDate.toISOString().slice(0, 10);

                // Find the rows in the table
                let row = tableBody.find('tr').eq(index);

                if (row.length) {
                    row.find('td').eq(1).text(date);
                    row.find('td').eq(5).text(panelty_date);
                }
            });
        }if (loan_type === "On A Selected Date") {
            let on_a_selected_date_txt = $("#installment_date").val();

            if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
                Swal.fire("Error!", "Please enter a valid Collection Date!", "error");
            } else {
                var installmentDates = [];
                var currentDate = new Date(on_a_selected_date_txt);

                // Function to format date as YYYY-MM-DD
                function formatDate(date) {
                    var year = date.getFullYear();
                    var month = (date.getMonth() + 1).toString().padStart(2, '0');
                    var day = date.getDate().toString().padStart(2, '0');
                    return year + '-' + month + '-' + day;
                }

                // Loop through each installment count
                for (var i = 0; i < installmentCount; i++) {
                    // Format the date
                    var formattedDate = formatDate(currentDate);
                    installmentDates.push(formattedDate);
                    currentDate.setMonth(currentDate.getMonth() + 1); // Increment month for the next installment
                }

                // Get the table body
                var tableBody = $('#installment_table tbody');

                // Loop through the installment dates and update the table rows
                installmentDates.forEach(function(date, index) {
                    let currentDate = new Date(date);
                    currentDate.setDate(currentDate.getDate() + panelty_date_2); // Add penalty date offset
                    let formattedPenaltyDate = formatDate(currentDate); // Format the penalty date

                    // Find the rows in the table and update only the 1st and 5th columns
                    let row = tableBody.find('tr').eq(index);
                    if (row.length) {
                        row.find('td').eq(1).text(date); // Update the 1st column with the installment number
                        row.find('td').eq(5).text(formattedPenaltyDate); // Update the 5th column with the penalty date
                    }
                });
            }
        }
    }
}



function saveDocument() {

    var loan_location_id = $('#loan_location_id').val();
    var description = $('#description').val();
    var file = $('#file')[0].files[0]; // Get the first selected file

    var formData = new FormData();
    formData.append('id', loan_location_id);
    formData.append('documentNames', description);
    formData.append('documents', file);

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to save this Document ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Upload it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/save-files-pending',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
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
                error: function(xhr, status, error) {
                    // Handle error response
                    console.error('Error saving document:', error);
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




function issue_request(id) {
    $("#loan_id_for_issue").val(id);

    $.ajax({
        type: "GET",
        url: "/loadbank/"+id,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data, textStatus, xhr) {

            console.log(data);
            // Clear the current options in the Select2 dropdown
            $('#bank_acc').empty();

            // Parse the response data if necessary
            var groups = data.item;
            $('#bank_acc').append(new Option("Cash", 0));
            // Populate the Select2 dropdown with the groups
            $.each(data.bank, function (index, group) {
                $('#bank_acc').append(new Option(group.bank_name+" - "+group.account_number+" - "+group.account_name+" - "+group.branch+"", group.id));
            });

            // Set the selected bank ID
            $('#bank_acc').val(data.bank_id).trigger('change');

            // Refresh the Select2 dropdown
            $('#group').trigger('change');
            load_document_check(id);
            load_approval_check(id);
        },
        error: function(xhr, textStatus, errorThrown) {
            console.log("Error:", errorThrown);
        }
    });
}

function load_document_check(id) {
    $.ajax({
        type: "GET",
        url: "/load_loan_document/" + id,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data, textStatus, xhr) {
            if (xhr.status === 200) {
                console.log(data);

                // Clear the existing rows
                $('#file_table tbody').empty();

                // Iterate over the data and create new rows
                data.item.forEach(function(document, index) {
                    var fileButton = document.Path !== "-" ?
                        `<button type="button" class="btn btn-primary btn-sm" onclick="openFile('storage/${document.Path}')">Open File</button>` :
                        "No File";

                    var checkboxId = `checkbox_${index + 1}`;

                    var newRow = `<tr>
                        <td hidden>${document.idDocuments}</td>
                        <td>${document.Name}</td>
                        <td>${fileButton}</td>
                        <td hidden><input type="checkbox" class="form-check-input" id="${checkboxId}"></td>
                    </tr>`;
                    $('#file_table tbody').append(newRow);
                });
            } else {
                Swal.fire("Error!", "Failed to load data!", "error");
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            console.log("Error:", errorThrown);
        }
    });
}

//
// function load_approval_check(id) {
//     $.ajax({
//         type: "GET",
//         url: "/load_loan_approval/" + id,
//         headers: {
//             "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
//         },
//         success: function (data, textStatus, xhr) {
//             if (xhr.status === 200) {
//                 let login_designation = data.login_designation;
//
//                 // Clear the existing rows
//                 $('#approval_table tbody').empty();
//
//                 let designationNames = "";
//                 let uniqueDesignations = new Set();
//
//                 // Process designations
//                 data.designation.forEach(function (document) {
//                     if (!uniqueDesignations.has(document.designation)) {
//                         uniqueDesignations.add(document.designation);
//                         designationNames += (designationNames ? ", " : "") + document.designation;
//                     }
//                 });
//
//                 let allUsersHaveIds = true; // Flag for "Issue Loan" button
//
//                 // Iterate over approval items
//                 data.item.forEach(function (document, index) {
//                     if (document.user_id === 0) allUsersHaveIds = false;
//
//                     let newRow = `<tr>
//                         <td hidden>${document.id}</td>
//                         <td>${document.level}</td>
//                         <td>${designationNames}</td>
//                         <td>${document.description}</td>
//                         <td><input type="text" class="form-control" value="${document.comment}" id="des_${index}"></td>
//                         <td>
//                             <input type="button" class="btn btn-primary" value="Approve" id="approve_btn_${document.level_id}" disabled>
//                         </td>
//                         <td>${document.user_id === 0 ? '-' : document.Full_Name}</td>
//                         <td>${document.date}</td>
//                         <td>
//                             <button class="btn btn-info btn-sm" onclick="toggleChecklist(${document.level_id})">
//                                 View Checklist (<span id="checklist_progress_${document.level_id}">0%</span>)
//                             </button>
//                         </td>
//                     </tr>
//                     <tr id="checklist_row_${document.level_id}" style="display: none;">
//                         <td colspan="9">
//                             <div id="checklist_container_${document.level_id}" class="p-3 bg-light"></div>
//                         </td>
//                     </tr>`;
//
//                     $('#approval_table tbody').append(newRow);
//
//                     // Load checklist progress
//                     loadChecklistProgress(document.level_id);
//                 });
//
//                 // Enable or disable "Issue Loan" button
//                 $('#issue_loan_btn').prop('disabled', !allUsersHaveIds);
//             } else {
//                 Swal.fire("Error!", "Failed to load data!", "error");
//             }
//         },
//         error: function (xhr) {
//             console.log("Error:", xhr.responseText);
//         },
//     });
// }

function load_approval_check(id) {
    $.ajax({
        type: "GET",
        url: "/load_loan_approval/" + id,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data, textStatus, xhr) {
            if (xhr.status === 200) {
                let login_designation = data.login_designation;

                // Clear the existing rows
                $('#approval_table tbody').empty();

                let designationNames = "";
                let uniqueDesignations = new Set();

                // Process designations
                data.designation.forEach(function (document) {
                    if (!uniqueDesignations.has(document.designation)) {
                        uniqueDesignations.add(document.designation);
                        designationNames += (designationNames ? ", " : "") + document.designation;
                    }
                });

                let allUsersHaveIds = true; // Flag for "Issue Loan" button

                // Iterate over approval items
                data.item.forEach(function (document, index) {
                    if (document.user_id === 0) allUsersHaveIds = false;


                    var approveButton;
                    if (login_designation==="Admin"){
                        if (document.user_id === 0) {
                            approveButton = `<input type="button" class="btn btn-primary" id="approve_btn_${document.level_id}" value="Approve" onclick="approve(${document.id}, '${index}')">`;
                        } else {
                            approveButton = `<input type="button" class="btn btn-primary"  value="Approve" disabled>`;
                        }
                    }else{
                        let designation_user=$("#designation_user").val();
                        if (designationNames.includes(designation_user) && document.user_id === 0) {

                            approveButton = `<input type="button" class="btn btn-primary" id="approve_btn_${document.level_id}" value="Approve" onclick="approve(${document.id}, '${index}')">`;
                        } else {
                            approveButton = `<input type="button" class="btn btn-primary" value="Approve" disabled>`;
                        }
                    }


                    let newRow = `<tr>
                        <td hidden>${document.id}</td>
                        <td>${document.level}</td>
                        <td>${designationNames}</td>
                        <td>${document.description}</td>
                        <td><input type="text" class="form-control" value="${document.comment}" id="des_${index}"></td>
                        <td>${approveButton}</td>
                        <td>${document.user_id === 0 ? '-' : document.Full_Name}</td>
                        <td>${document.date}</td>
                        <td>
    <button class="btn btn-info btn-sm" onclick="toggleChecklist(${document.level_id},${id})">
        View Checklist (<span id="checklist_progress_${document.level_id}">0/0</span>)
    </button>
</td>

                    </tr>
                    <tr id="checklist_row_${document.level_id}" style="display: none;">
                        <td colspan="9">
                            <div id="checklist_container_${document.level_id}" class="p-3 bg-light"></div>
                        </td>
                    </tr>`;

                    $('#approval_table tbody').append(newRow);

                    // Load checklist progress
                    loadChecklistProgress(document.level_id,id);
                });

                // Enable or disable "Issue Loan" button
                $('#issue_loan_btn').prop('disabled', !allUsersHaveIds);
            } else {
                Swal.fire("Error!", "Failed to load data!", "error");
            }
        },
        error: function (xhr) {
            console.log("Error:", xhr.responseText);
        },
    });
}


function loadChecklistProgress(levelId,loan_id) {
    $.ajax({
        type: "GET",
        url: `/load_checklist/${levelId}/${loan_id}`,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            console.log(data); // Debugging to ensure correct data is received
            if (data.success) {
                const total = data.checklist.length;
                const completed = data.checklist.filter(item => parseInt(item.status) === 1).length;

                // Handle cases with no checklist items
                const progressText = total > 0 ? `${completed}/${total}` : `0/0`;

                // Update progress as a fraction (e.g., 1/3)
                $(`#checklist_progress_${levelId}`).text(progressText);

            } else {
                Swal.fire("Error!", "Failed to load checklist progress!", "error");
            }
        },
        error: function (xhr) {
            console.log("Error:", xhr.responseText);
        },
    });
}




function toggleChecklist(levelId,loan_id) {
    const row = $(`#checklist_row_${levelId}`);
    if (row.is(':visible')) {
        row.hide();
    } else {
        loadChecklist(levelId,loan_id);
        row.show();
    }
}

function loadChecklist(levelId, loan_id) {
    $.ajax({
        type: "GET",
        url: `/load_checklist/${levelId}/${loan_id}`,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            if (data.success) {
                let checklistHtml = `<ul class="list-group">`;
                data.checklist.forEach(item => {
                    const isMarked = parseInt(item.status) === 1;
                    const rowStyle = isMarked
                        ? "background-color:#b3e5af; color: Green; height: 40px;" // Green background, white text, reduced height
                        : "background-color:white; color: Gray; height: 40px;"; // Red background, white text, reduced height
                    const buttonLabel = isMarked ? "Remove Checked" : "Checked";
                    const buttonStyle = isMarked
                        ? "background-color: #f8f9fa; color:red;" // Light background with green text
                        : "background-color: #f8f9fa; color: green;"; // Light background with red text

                    checklistHtml += `
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="${rowStyle}">
                            ${item.description}
                            <button class="btn btn-sm" style="${buttonStyle}" onclick="markChecklistItem(${item.id}, ${levelId}, ${item.status}, ${loan_id})">
                                ${buttonLabel}
                            </button>
                        </li>`;
                });
                checklistHtml += `</ul>`;
                $(`#checklist_container_${levelId}`).html(checklistHtml);
            } else {
                Swal.fire("Error!", "Failed to load checklist!", "error");
            }
        },
        error: function (xhr) {
            console.log("Error:", xhr.responseText);
        },
    });
}




function markChecklistItem(itemId, levelId, currentStatus,loan_id) {
    const newStatus = currentStatus === 1 ? 0 : 1; // Toggle status (1 -> 0, 0 -> 1)
    const action = newStatus === 1 ? "mark this item as completed" : "remove the mark";

    Swal.fire({
        title: "Are you sure?",
        text: `Do you want to ${action}?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, proceed!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed to toggle the checklist item status
            $.ajax({
                type: "POST",
                url: `/update_checklist/${itemId}`,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: { status: newStatus },
                success: function (data) {
                    if (data.success) {
                        // Reload the checklist to reflect changes
                        loadChecklist(levelId,loan_id);
                        // Refresh checklist progress after updating the database
                        loadChecklistProgress(levelId,loan_id);

                        // Show success notification
                        Swal.fire(
                            newStatus === 1 ? "Marked!" : "Unmarked!",
                            `The checklist item has been ${newStatus === 1 ? "marked as completed" : "unmarked"}.`,
                            "success"
                        );
                    } else {
                        Swal.fire("Error!", "Failed to update checklist item!", "error");
                    }
                },
                error: function (xhr) {
                    console.log("Error:", xhr.responseText);
                    Swal.fire("Error!", "An unexpected error occurred!", "error");
                },
            });
        }
    });
}



function approve(id,index){
    let comment = $("#des_" + index).val();

    if (comment===" "){
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please Enter Comment !',
        })
    }else{
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to approve this Loan ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Approve it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "/approve_loan",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: {
                        id:id,
                        comment:comment
                    },
                    success: function (data, textStatus, xhr) {
                        if (xhr.status === 200) {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Successfully Updated !",
                            }).then(function () {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire("Error!", "Failed to load data!", "error");
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.log("Error:", errorThrown);
                    }
                });
            }
        });
    }






}


function openFile(path) {
    window.open(path, '_blank');
}




function delete_request(id) {
    $("#loan_id_for_delete").val(id);
}
function issue_loan(){
    let loan_id=$("#loan_id_for_issue").val();
    let bank_acc=$("#bank_acc").val();
    let company_bank=$("#company_bank").val();


    var document_details = [];

    $('#file_table tbody tr').each(function() {
        var row = $(this);
        var rowData = {
            id: row.find('td').eq(0).text().trim(), // Get the hidden # and trim whitespace
            checked: row.find('td input[type="checkbox"]').is(':checked') // Get the checkbox state
        };

        document_details.push(rowData);
    });


    if (bank_acc===null){
        Swal.fire("Error!", "Please select customer bank account !", "error");
    }else if(company_bank===null){
        Swal.fire("Error!", "Please select company bank account !", "error");
    }else{
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to issue this Loan ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Issue it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "/pendingloanissue",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: {
                        loan_id: loan_id,
                        bank_acc: bank_acc,
                        company_bank: company_bank,
                        document_details:document_details
                    },
                    success: function (data, textStatus, xhr) {
                        console.log(data)
                        if (xhr.status === 200) {

                            if (data.id===1){
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Successfully issued !',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'OK',
                                    cancelButtonText: 'Print Invoice'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.reload();
                                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                                        window.open('/invoice/' + loan_id, '_blank');
                                        window.location.reload();
                                    }
                                });
                            }else{
                                Swal.fire("Error!", "Bank Balance is not enough !", "error");
                            }
                        } else {
                            Swal.fire("Error!", "Failed to issue loan!", "error");
                        }
                    },
                });
            }
        });
    }
}


function delete_loan(){
    let loan_id=$("#loan_id_for_delete").val();
    let reason_for_dlt=$("#reason_for_dlt").val();

    if (reason_for_dlt===""){
        Swal.fire("Error!", "Please enter a reason !", "error");
    }else {
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to delete this Loan ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "GET",
                    url: "/pendingloandelete/" + loan_id,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: {
                        reason_for_dlt: reason_for_dlt
                    },
                    success: function (data, textStatus, xhr) {
                        if (xhr.status === 200) {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Successfully Deleted !",
                            }).then(function () {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire("Error!", "Failed to delete loan!", "error");
                        }
                    },
                });
            }
        });
    }
}


function load_details(id,loan_id){

    window.location.href = "/show_loan/" + id+"/"+loan_id;
}
