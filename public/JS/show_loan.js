$(document).ready(function() {

    $("#weekly").hide();
    $("#first_of_the_month").hide();
    $("#end_of_the_month").hide();
    $("#twice_a_month").hide();
    $("#on_a_selected_date").hide();

    let cate_id=$("#loan_cate_id").val();
    changeCategory(cate_id);

});

function changeCategory(id){
    $.ajax({
        type: "GET",
        url: "/loancategory/" + id,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function(data, textStatus, xhr) {
            var loanCategory = data.item[0]; // Assuming there's only one item in the array

            // Set values to spans
            $("#only_interest").text(loanCategory.Type);
            $("#witness").text(loanCategory.Witness_Count);
            $("#collection_type").text(loanCategory.Collection_Type);
            view_doc(id);
            calculateInterest();
            $('#panelty_date').text("Installment Date + "+loanCategory.Panalty_Start_date_Count+" Days");
            $('#panelty_date_2').text(loanCategory.Panalty_Start_date_Count);
            if(loanCategory.Type !== "Installment"){
                $("#registration_no").show();
                $("#leasing_type").show();
            }else{
                $("#registration_no").hide();
                $("#leasing_type").hide();
            }

            if (loanCategory.Collection_Type==="Weekly"){
                $("#weekly").show();
                $("#first_of_the_month").hide();
                $("#end_of_the_month").hide();
                $("#twice_a_month").hide();
                $("#on_a_selected_date").hide();
            }else if (loanCategory.Collection_Type==="First Of The Month"){
                $("#weekly").hide();
                $("#first_of_the_month").show();
                $("#end_of_the_month").hide();
                $("#twice_a_month").hide();
                $("#on_a_selected_date").hide();
            }else if (loanCategory.Collection_Type==="End Of The Month"){
                $("#weekly").hide();
                $("#first_of_the_month").hide();
                $("#end_of_the_month").show();
                $("#twice_a_month").hide();
                $("#on_a_selected_date").hide();
            }else if (loanCategory.Collection_Type==="Twice A Month"){
                $("#weekly").hide();
                $("#first_of_the_month").hide();
                $("#end_of_the_month").hide();
                $("#twice_a_month").show();
                $("#on_a_selected_date").hide();
                var currentDate = new Date();
                currentDate.setDate(1);
                $("#installment_date_txt").val(currentDate.toISOString().split('T')[0]);
            }else if (loanCategory.Collection_Type==="On A Selected Date"){
                $("#weekly").hide();
                $("#first_of_the_month").hide();
                $("#end_of_the_month").hide();
                $("#twice_a_month").hide();
                $("#on_a_selected_date").show();
                check_date(1);
            }



            installment=[];


        },
        error: function(xhr, textStatus, errorThrown) {
            console.log("Error:", errorThrown);
        }
    });
}


function change_type(id) {
    if (id==="Vehicle"){
        $('#leasing_type_name').text("Registration Number")
    }else if(id==="Land"){
        $('#leasing_type_name').text("Land Number")
    }else if(id==="Gold"){
        $('#leasing_type_name').text("Gold Details")
    }
}


function view_doc(id){
    $.ajax({
        type: "GET",
        url: "/loancategory/cost/"+id,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data, textStatus, xhr) {
            console.log(data);
            if (xhr.status === 200) {
                // Process other charges
                var otherChargesTable = $('#loan_charge_table tbody');
                otherChargesTable.empty(); // Clear existing rows
                var totalAmount = 0; // Initialize total amount
                data.other_charges.forEach(function(charge) {
                    var row = $('<tr></tr>');
                    row.append('<td>' + charge.Description + '</td>');
                    row.append('<td class="text-end">' + charge.Amount + '</td>');
                    otherChargesTable.append(row);
                    totalAmount += parseFloat(charge.Amount);
                });


                $('#total_loan_charge').text(totalAmount.toFixed(2));

                var documentsTable = $('#document_show_table tbody');
                documentsTable.empty(); // Clear existing rows

                data.required_documents.forEach(function(document) {
                    var row = $('<tr></tr>');
                    row.append('<td>' + document.Name + '</td>');
                    row.append('<td><button class="btn btn-danger open-document" data-filename="' + document.Path + '">Open</button></td>');
                    documentsTable.append(row);
                });

                calculateInterest();

            }
        },

        error: function(xhr, textStatus, errorThrown) {
            console.log("Error:", errorThrown);
        }
    });
}

// Handle click event for the "Open" button
$(document).on('click', '.open-document', function() {
    var filename = $(this).data('filename');

    // Call the Laravel endpoint to open the file
    window.open('/open-file/' + filename, '_blank');
});


function calculateInterest(){
    let loan_amount = parseFloat($("#loan_amount").val());
    let interest = parseFloat($("#interest").val());
    let total_loan_charge = parseFloat($("#total_loan_charge").text());
    let installment_count = parseFloat($("#ins_count").val());

    if (isNaN(total_loan_charge)) {
        total_loan_charge=0.00;
    }
    let new_installment_count=0.0;
        if (!isNaN(loan_amount) && !isNaN(interest) && !isNaN(installment_count)) {
        let interest_amount = ((loan_amount * interest) / 100)*installment_count;
        $("#interest_amount").val(interest_amount.toFixed(2));

        let total = loan_amount + interest_amount + total_loan_charge;
        $("#total_loan_amount").text(total.toFixed(2));
            new_installment_count=total/installment_count;
        $("#new_interest_amount").text(new_installment_count.toFixed(2));
    }



}


function calculatenetBalance() {
    let loan_amount = parseFloat($("#loan_amount").val());
    let ins_count = parseFloat($("#ins_count").val());

    let tot_loan_amount=parseFloat($("#total_loan_amount").text());
    let loan_charge_balance=$("#loan_charge_balance").val();
    let interest = parseFloat($("#interest").val());
    let total_loan_charge = parseFloat($("#total_loan_charge").text());
    if (loan_charge_balance===""){
        let tot=loan_amount+interest+total_loan_charge;
        $("#total_loan_amount").val(tot);
    }else{
        let new_intallment_amount=tot_loan_amount/ins_count;

        $("#new_interest_amount").text(new_intallment_amount.toFixed(2));



        let loan_charge_balance = parseFloat($("#loan_charge_balance").val());

        if (isNaN(total_loan_charge)) {
            total_loan_charge=0.00;
        }
        let new_interest=0.00;
        if (!isNaN(loan_amount) && !isNaN(interest)) {
            let interest_amount = (loan_amount * interest) / 100;
            $("#interest_amount").val(interest_amount.toFixed(2));

            let total = loan_amount + interest_amount + total_loan_charge;


            let difference = total-loan_charge_balance  ;
            new_interest=(loan_amount * interest) / 100;

            $("#total_loan_amount").text(difference.toFixed(2));

        }
    }
}


function addInstallmentDates() {

    let startDate = $("#installment_date_txt").val();

    let installmentCount = $("#ins_count").val();
    let panelty_amount = $("#panelty_amount").val();
    let installmentAmount = parseFloat($("#new_interest_amount").text());

    let loan_type = $("#collection_type").text();

    if (startDate.trim() === "" || isNaN(new Date(startDate))) {
        Swal.fire("Error!", "Please enter a valid installment date !", "error");
    } else if (isNaN(parseInt(installmentCount)) || parseInt(installmentCount) <= 0) {
        Swal.fire("Error!", "Please enter a valid installment count greater than zero !", "error")
    } else if (isNaN(installmentAmount) || installmentAmount <= 0) {
        Swal.fire("Error!", "Please calculate the installment amount first !", "error")
    } else if (panelty_amount === "") {
        Swal.fire("Error!", "Please calculate the penalty rate !", "error")
    } else {


        if(loan_type==="Weekly"){

        }else if (loan_type==="First Of The Month"){
            var selected_date = new Date($('#installment_date_txt').val());

            // Check if it's the first day of the month
            if (selected_date.getDate() === 1) {

                let on_a_selected_date_txt=$('#installment_date_txt').val();

                if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
                    Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
                }else{
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
                        // Add one month to the current date
                        // Format the date

                        var formattedDate = formatDate(currentDate);
                        installmentDates.push(formattedDate);
                        currentDate.setMonth(currentDate.getMonth() + 1);
                    }

                    // Get the table body
                    var tableBody = $('#installment_table tbody');

                    // Clear existing rows
                    tableBody.empty();
                    installmentAmount=installmentAmount.toFixed(2);
                    let count=1;
                    installmentDates.forEach(function(date) {
                        var row = '<tr><td>' + count + '</td><td>' + date + '</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">0.00</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">'+installmentAmount+'</td><td class="text-center"><span class="px-1" style="background-color: #ff0000;border-radius: 10px; color: #ff0000;">-</span></td></tr>';
                        tableBody.append(row);
                        count++;
                    });
                }
            } else {
                Swal.fire("Error!", "Selected date is not the first day of the month !", "error")
            }
        }else if (loan_type==="End Of The Month"){
            var on_a_selected_date_txt = $('#installment_date_txt').val();
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

                        var selectedDate = new Date($('#installment_date_txt').val());

// Get the last day of the selected month
                        var lastDayOfMonth = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + 1+i, 0);


                        // Get the last day of the current month
                        // var nextMonthDate = new Date(currentDate);
                        // nextMonthDate.setMonth(nextMonthDate.getMonth() + 1);
                        // nextMonthDate.setDate(0); // Set to last day of current month

                        // Format the date
                        var formattedDate = formatDate(lastDayOfMonth);
                        installmentDates.push(formattedDate);

                        // Move to the first day of the next month
                        // currentDate.setMonth(currentDate.getMonth() + 1);
                        // currentDate.setDate(1);
                    }

                    // Get the table body
                    var tableBody = $('#installment_table tbody');

                    // Clear existing rows
                    tableBody.empty();
                    installmentAmount = installmentAmount.toFixed(2);
                    let count=1;
                    installmentDates.forEach(function(date) {
                        var row = '<tr><td>' + count + '</td><td>' + date + '</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">0.00</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">'+installmentAmount+'</td><td class="text-center"><span class="px-1" style="background-color: #ff0000;border-radius: 10px; color: #ff0000;">-</span></td></tr>';
                        tableBody.append(row);
                        count++;
                    });
                }
            } else {
                Swal.fire("Error!", "Selected date is not the last day of the month !", "error")
            }


        }else if (loan_type==="Twice A Month"){
            let twice_a_month_txt=$('#twice_a_month_txt').val();
            if (twice_a_month_txt=="2"){
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
                for (var i = 0; i < (installmentCount/2); i++) {

                    var selectedDate = new Date($('#installment_date_txt').val());

// Get the last day of the selected month


                    var lastDayOfMonth2 = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + i, 15);
                    var lastDayOfMonth = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + i+1, 0);


                    var formattedDate = formatDate(lastDayOfMonth2);
                    installmentDates.push(formattedDate);
                    var formattedDate2 = formatDate(lastDayOfMonth);
                    installmentDates.push(formattedDate2);

                    // Move to the first day of the next month
                    // currentDate.setMonth(currentDate.getMonth() + 1);
                    // currentDate.setDate(1);
                }

                // Get the table body
                var tableBody = $('#installment_table tbody');

                // Clear existing rows
                tableBody.empty();
                installmentAmount = installmentAmount.toFixed(2);
                let count=1;
                installmentDates.forEach(function(date) {
                    var row = '<tr><td>' + count + '</td><td>' + date + '</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">0.00</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">'+installmentAmount+'</td><td class="text-center"><span class="px-1" style="background-color: #ff0000;border-radius: 10px; color: #ff0000;">-</span></td></tr>';
                    tableBody.append(row);
                    count++;
                });





            }else{
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
                for (var i = 0; i < (installmentCount/2); i++) {

                    var selectedDate = new Date($('#installment_date_txt').val());

// Get the last day of the selected month
                    var lastDayOfMonth = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + i, 1);

                    var lastDayOfMonth2 = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + i, 15);


                    // Get the last day of the current month
                    // var nextMonthDate = new Date(currentDate);
                    // nextMonthDate.setMonth(nextMonthDate.getMonth() + 1);
                    // nextMonthDate.setDate(0); // Set to last day of current month
                    // Format the date
                    var formattedDate = formatDate(lastDayOfMonth);
                    installmentDates.push(formattedDate);
                    var formattedDate2 = formatDate(lastDayOfMonth2);
                    installmentDates.push(formattedDate2);

                    // Move to the first day of the next month
                    // currentDate.setMonth(currentDate.getMonth() + 1);
                    // currentDate.setDate(1);
                }

                // Get the table body
                var tableBody = $('#installment_table tbody');

                // Clear existing rows
                tableBody.empty();
                installmentAmount = installmentAmount.toFixed(2);
                let count=1;
                installmentDates.forEach(function(date) {
                    var row = '<tr><td>' + count + '</td><td>' + date + '</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">0.00</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">'+installmentAmount+'</td><td class="text-center"><span class="px-1" style="background-color: #ff0000;border-radius: 10px; color: #ff0000;">-</span></td></tr>';
                    tableBody.append(row);
                    count++;
                });
            }

        }else if (loan_type==="On A Selected Date"){

            let on_a_selected_date_txt = $("#installment_date_txt").val();

            if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
                Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
            }else{
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
                    // Add one month to the current date
                    // Format the date

                    var formattedDate = formatDate(currentDate);
                    installmentDates.push(formattedDate);
                    currentDate.setMonth(currentDate.getMonth() + 1);
                }

                // Get the table body
                var tableBody = $('#installment_table tbody');

                // Clear existing rows
                tableBody.empty();
                installmentAmount=installmentAmount.toFixed(2);
                let count=1;
                installmentDates.forEach(function(date) {
                    var row = '<tr><td>' + count + '</td><td>' + date + '</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">0.00</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">'+installmentAmount+'</td><td class="text-center"><span class="px-1" style="background-color: #ff0000;border-radius: 10px; color: #ff0000;">-</span></td></tr>';
                    tableBody.append(row);
                    count++;
                });
            }
        }
    }


}

function change_date(val){
    let twice_a_month_txt = $("#twice_a_month_txt").val();
    if (twice_a_month_txt==="1"){
        var currentDate = new Date();
        currentDate.setDate(1);
        $("#installment_date_txt").val(currentDate.toISOString().split('T')[0]);
    }else{
        var currentDate = new Date();

// Set the current date to the first day of the next month
        currentDate.setDate(1);
        currentDate.setMonth(currentDate.getMonth() + 1);

// Subtract one day to get the last day of the current month
        currentDate.setDate(currentDate.getDate() - 1);

// Format the last day of the month as needed (e.g., YYYY-MM-DD)
        var lastDayOfMonth = currentDate.toISOString().split('T')[0];

// Set the last day of the month to the installment_date_txt or wherever you need
        $("#installment_date_txt").val(lastDayOfMonth);

    }
}


function check_date(on_a_selected_date_txt){
    var selectedValue = parseInt(on_a_selected_date_txt);
    selectedValue++;
    // Get current date
    var currentNewDate = new Date();
    // Calculate next month's date
    var nextMonthDate = new Date(currentNewDate.getFullYear(), currentNewDate.getMonth() + 1, selectedValue);
    $('#installment_date_txt').val(nextMonthDate.toISOString().slice(0, 10));
}

function generateWitnessTabs(witnessCount) {
    let tabsContainer = $(".nav-tabs");
    let tabsContent = $(".tab-content");

    tabsContainer.empty();
    tabsContent.empty();

    for (let i = 1; i <= witnessCount; i++) {
        // Create tab link
        let tabLink = $('<a></a>')
            .attr("href", "#witness" + i)
            .attr("data-bs-toggle", "tab")
            .addClass("nav-link")
            .text("Guarantor " + i);

        // Set active class for the first witness tab
        if (i === 1) {
            tabLink.addClass("active");
        }

        let tabItem = $('<li></li>').addClass("nav-item").append(tabLink);
        tabsContainer.append(tabItem);

        // Create tab content
        let tabPane = $('<div></div>')
            .addClass("tab-pane")
            .attr("id", "witness" + i);

        let rowDiv = $('<div></div>').addClass("row");

        // Left column
        let leftColDiv = $('<div></div>').addClass("col-lg-6");

        let titleSelect = $('<select></select>')
            .addClass("form-select mb-3")
            .append('<option>Mr</option>')
            .append('<option>Mrs</option>');

        let fullNameInput = $('<input>')
            .addClass("form-control mb-3")
            .attr("type", "text")
            .attr("id", "full_name_" + i) // Set ID dynamically
            .attr("placeholder", "Full Name");

        let addressInput = $('<input>')
            .addClass("form-control mb-3")
            .attr("type", "text")
            .attr("id", "address_" + i) // Set ID dynamically
            .attr("placeholder", "Address");

        leftColDiv.append($('<label></label>').addClass("form-label").text("Title"));
        leftColDiv.append(titleSelect);
        leftColDiv.append($('<label></label>').addClass("form-label").text("Full Name"));
        leftColDiv.append(fullNameInput);
        leftColDiv.append($('<label></label>').addClass("form-label").text("Address"));
        leftColDiv.append(addressInput);

        // Right column
        let rightColDiv = $('<div></div>').addClass("col-lg-6");

        let firstNameInput = $('<input>')
            .addClass("form-control mb-3")
            .attr("type", "text")
            .attr("id", "first_name_" + i) // Set ID dynamically
            .attr("placeholder", "First Name");

        let nicInput = $('<input>')
            .addClass("form-control mb-3")
            .attr("type", "text")
            .attr("id", "nic_" + i) // Set ID dynamically
            .attr("placeholder", "NIC");

        let contactNoInput = $('<input>')
            .addClass("form-control mb-3")
            .attr("type", "text")
            .attr("id", "contact_no_" + i) // Set ID dynamically
            .attr("placeholder", "Contact No");

        rightColDiv.append($('<label></label>').addClass("form-label").text("First Name"));
        rightColDiv.append(firstNameInput);
        rightColDiv.append($('<label></label>').addClass("form-label").text("NIC"));
        rightColDiv.append(nicInput);
        rightColDiv.append($('<label></label>').addClass("form-label").text("Contact No"));
        rightColDiv.append(contactNoInput);

        // Append left and right columns to row
        rowDiv.append(leftColDiv);
        rowDiv.append(rightColDiv);

        // Append row to tab content
        tabPane.append(rowDiv);

        // Save button
        let buttonDiv = $('<div></div>').addClass("d-flex justify-content-end mt-3");

        tabPane.append(buttonDiv);

        tabsContent.append(tabPane);
    }
}


var installment = [];

function save_loan(){
    let customer_id = $("#customer_id").val();
    let loan_cate_id = $("#loan_cate_id").val();
    let leasing_type = $("#leasing_type").val();
    let loan_number = $("#loan_number").val();
    let loan_number_txt = $("#loan_number_txt").text();
    let loan_amount = $("#loan_amount").val();
    let interest = $("#interest").val();
    let panelty_amount = $("#panelty_amount").val();
    let ins_count = $("#ins_count").val();
    let interest_amount = $("#interest_amount").val();
    let total_loan_charge = $("#total_loan_charge").text();
    let loan_charge_balance = $("#loan_charge_balance").val();
    let total_loan_amount = $("#total_loan_amount").text();
    let new_interest_amount = $("#new_interest_amount").text();
    let collection_type = $("#collection_type").text();
    let installment_date_txt = $("#installment_date_txt").val();
    let panelty_date = $("#panelty_date_2").text();
    let witness = $("#witness").text();

    if (leasing_type===""){
        leasing_type="-";
        loan_number="-";
    }

// Initialize an empty array to store table data


// Iterate over each row of the table
    $('#installment_table tbody tr').each(function() {
        // Initialize an empty object to store row data
        var rowData = {};

        // Iterate over each cell of the row
        $(this).find('td').each(function(index) {
            // Get the text content of the cell
            var cellData = $(this).text();

            // Assign the cell data to the corresponding property of the row data object
            // Assuming the order of cells matches the order of headers in the table
            switch(index) {
                case 0:
                    rowData.No = cellData; // Add No column data
                    break;
                case 1:
                    rowData.installmentDate = cellData;
                    break;
                case 2:
                    rowData.installmentAmount = cellData;
                    break;
                case 3:
                    rowData.panaltyAmount = cellData;
                    break;
                case 4:
                    rowData.totalAmount = cellData;
                    break;
                case 5:
                    rowData.paidAmount = cellData;
                    break;
                case 6:
                    rowData.panaltyBalance = cellData;
                    break;
                case 7:
                    rowData.installmentBalance = cellData;
                    break;
                case 8:
                    rowData.totalBalance = cellData;
                    break;
                case 9:
                    rowData.status = cellData;
                    break;
                default:
                    break;
            }

        });

        // Push the row data object to the table data array
        installment.push(rowData);
    });

    console.log(installment);

// Assuming witnessCount contains the number of witnesses
    var witnessCount = witness; // for example
    var witnessesArray = []; // Array to store witness data

    for (var i = 1; i <= witnessCount; i++) {
        var title = $('#witness' + i + ' select').val();
        var fullName = $('#full_name_' + i).val();
        var address = $('#address_' + i).val();
        var firstName = $('#first_name_' + i).val();
        var nic = $('#nic_' + i).val();
        var contactNo = $('#contact_no_' + i).val();


        var witnessData = {
            title: title,
            fullName: fullName,
            address: address,
            firstName: firstName,
            nic: nic,
            contactNo: contactNo
        };

        // Push the object to the witnessesArray
        witnessesArray.push(witnessData);


    }
    console.log(witnessesArray)
    if (installment==""){
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'No Installment Found!',
        })

    }else{
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to create this Loan ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Create it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "/loan",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: {
                        installment:installment,
                        loan_number_txt:loan_number_txt,
                        customer_id: customer_id,
                        loan_cate_id: loan_cate_id,
                        leasing_type: leasing_type,
                        loan_number: loan_number,
                        loan_amount: loan_amount,
                        interest: interest,
                        panelty_amount: panelty_amount,
                        ins_count: ins_count,
                        interest_amount:interest_amount,
                        total_loan_charge:total_loan_charge,
                        loan_charge_balance:loan_charge_balance,
                        total_loan_amount:total_loan_amount,
                        new_interest_amount:new_interest_amount,
                        collection_type:collection_type,
                        installment_date_txt:installment_date_txt,
                        panelty_date:panelty_date,
                        witnessesArray:witnessesArray
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
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                            Swal.fire("Error!", xhr.responseJSON.message, "error");
                        } else {
                            Swal.fire("Error!", "Failed to save data!", "error");
                        }
                    },
                });
            }
        });
    }
}








