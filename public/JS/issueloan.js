function select_customer(id){
    $.ajax({
        type: "GET",
        url: "/customers/" + id,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function(data, textStatus, xhr) {

            var customer = data.item[0]; // Assuming there's only one item in the array
            $("#customer_name").text(customer.Full_Name);
            $("#customer_contact").text(customer.Contact_No);
            $("#risk_level").text(customer.Customer_Risk_Level);
            if (customer.Note === null) {
                $("#note").text("-");
            } else {
                $("#note").text(customer.Note);
            }


            // Append loan data to the table
            var loanData = data.loan;
            var tableBody = $("#loan_table tbody");
            tableBody.empty(); // Clear existing rows
            loanData.forEach(function(loan) {
                var statusText, statusClass;
                switch (loan.Status) {
                    case '-1':
                        statusText = 'Pending Loan';
                        statusClass = 'text-warning'; // Yellow color for Pending Loan
                        break;
                    case '0':
                        statusText = 'Current Loan';
                        statusClass = 'text-success'; // Green color for Current Loan
                        break;
                    case '-2':
                        statusText = 'Deleted Loan';
                        statusClass = 'text-danger'; // Red color for Deleted Loan
                        break;
                    case '1':
                        statusText = 'Finished Loan';
                        statusClass = 'text-danger'; // Red color for Deleted Loan
                        break;
                    default:
                        statusText = 'Unknown';
                        statusClass = ''; // Default styling if status is unknown
                }

// Construct the status cell with the appropriate styling
                var statusCell = "<td><span class=\"" + statusClass + "\">" + statusText + "</span></td>";



                var row =  "<tr>" +
                    "<td>" + loan.Date_Time + "</td>" +
                    "<td>" + loan.loan_name + "</td>" +
                    "<td>" + parseFloat(loan.Amount).toFixed(2) + "</td>" +
                    "<td>" + loan.Installment_Count + "</td>" +
                    "<td>" + loan.Installment_Amount + "</td>" +
                    "<td>" + loan.Collection_Date + "</td>" +
                    "<td>" + parseFloat(loan.Total_Loan_Amount).toFixed(2) + "</td>" +
                    statusCell +
                    "<td><button type=\"button\" class=\"btn btn-success\" onclick=\"load_details('" + loan.Customer_idCustomer + "', '" + loan.idCustomer_Loan + "')\"><i class=\"bi bi-eye\"></i></button></td>" +
                    "</tr>";


                tableBody.append(row);
            });

            if (id!=="0"){
                var newHref = "/loan_step_2/" + id;

                // Change the href attribute of the anchor tag
                $("a.btn.btn-success").attr("href", newHref);
            }



        },
        error: function(xhr, textStatus, errorThrown) {
            console.log("Error:", errorThrown);
        }
    });

}

function load_details(id,loan_id){

    window.location.href = "/payment_step_1_show_loan/" + id+"/"+loan_id;
}


function change_group(id) {
    $.ajax({
        type: "GET",
        url: "/load_group/" + id,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function(data, textStatus, xhr) {
            var groupData = data.item;
            $('#customer_details').empty();

            // Append a default "Select" option
            var defaultOption = $('<option></option>')
                .attr('value', "0")
                .text("Select");
            $('#customer_details').append(defaultOption);
            // Clear existing options in the select element
            $('#group').empty();

            // Check if groupData is not empty and is an array
            if (Array.isArray(groupData) && groupData.length > 0) {
                var option_1 = $('<option></option>')
                    .attr('value', "0")
                    .text("Select"); // Adjust according to your group data structure
                $('#group').append(option_1);
                // Iterate through the data and create new options
                groupData.forEach(function(group) {
                    var option = $('<option></option>')
                        .attr('value', group.idCustomer_Group)
                        .text(group.Name); // Adjust according to your group data structure
                    $('#group').append(option);
                });
            } else {
                // If no data, append a default "No groups available" option
                var option = $('<option></option>')
                    .attr('value', '')
                    .text('No groups available');
                $('#group').append(option);
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            console.log("Error:", errorThrown);
        }
    });
}



function save_loan(){

    let bank_acc = $("#bank_acc").val();
    let customer_id = $("#customer_details").val();
    if (customer_id=='0') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please select a Customer.',
        });
        return;
    }
    let loan_cate_id = $("#package_details").val();
    let leasing_type = $("#leasing_type").val();
    let loan_number = "001";
    let loan_number_txt = "001";
    let loan_amount = $("#loan_amount").val();
    let interest = $("#loan_interest").val();
    let panelty_amount = $("#penalty_percentage").val();
    let ins_count = $("#loan_period").val();
    let interest_amount = $("#interest_amount").val();
    let total_loan_charge = $("#total_loan_charge").text();
    let saving_amount = $("#saving_amount").text();
    let loanChargesBalanceCheckbox = document.getElementById("loanChargesBalance");
    let deductCharges = document.getElementById("deductCharges");
    let separateCharges = document.getElementById("separateCharges");

    let loan_charge_balance = "";

    if (loanChargesBalanceCheckbox.checked) {
        loan_charge_balance = "1";
    } else if (deductCharges.checked) {
        loan_charge_balance = "0";
    } else if (separateCharges.checked) {
        loan_charge_balance = "-1";
    }

    let total_loan_amount = $("#total_loan_amount").text();
    let new_interest_amount = $("#new_interest_amount").text();
    let collection_type = $("#repayment_type").val();
    let installment_date_txt = $("#installment_date_txt").val();
    let panelty_date = $("#panelty_date_2").text();
    let witness = $("#guarantee_count").val();
    let loan_type = $("#type").val();
    let Interest_period = $("#interest_period").val();
    let lease_type = $("#lease_type").val();
    let vehicle_num = $("#vehicle_num").val();
    let lending_officer = $("#lending_officer").val();
    let collector_officer = $("#collector_officer").val();
    let repayment_duration_period = $("#repayment_duration_period").val();
    if (lease_type=="0"){
        vehicle_num="-";
    }


    let total_capital_amount = $("#total_capital_amount").text();
    let total_interest_amount = $("#total_interest_amount").text();
    let interest_method = $("#interest_method").val();

    let loan_broker = $("#loan_broker").val();
    let loan_broker_commission = $("#loan_broker_commission").val();
    let type_loan_number = $("#type_loan_number").val();
    let issue_date = $("#issue_date").val();

    const loanAmountFrom = parseFloat($("#loan_amount_from").val());
    const loanAmountTo = parseFloat($("#loan_amount_to").val());

    // Validate if loan_amount_from and loan_amount_to are numeric
    if (isNaN(loanAmountFrom) || isNaN(loanAmountTo)) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please enter valid numeric values for Minimum Loan Amount to Maximum Loan Amount',
        });
        return; // Stop the function if validation fails
    }

    // Validate if loan_amount is a valid number
    loan_amount = parseFloat(loan_amount);
    if (isNaN(loan_amount)) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please enter a valid Loan Amount.',
        });
        return; // Stop the function if validation fails
    }

    // Check if loan_amount is within the range
    if (loan_amount < loanAmountFrom) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: `Loan Amount must be greater than or equal to ${loanAmountFrom}`,
        });
        return; // Stop the function if validation fails
    } else if (loan_amount > loanAmountTo) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: `Loan Amount must be less than or equal to ${loanAmountTo}`,
        });
        return; // Stop the function if validation fails
    }

    const loan_interest_from = parseFloat($("#loan_interest_from").val());
    const loan_interest_to = parseFloat($("#loan_interest_to").val());

    // Validate if loan_interest_from and loan_interest_to are numeric
    if (isNaN(loan_interest_from) || isNaN(loan_interest_to)) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please enter valid numeric values for Minimum Interest to Maximum Interest',
        });
        return; // Stop the function if validation fails
    }

    // Validate if interest is a valid number
    interest = parseFloat(interest);
    if (isNaN(interest)) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please enter a valid Loan Interest.',
        });
        return; // Stop the function if validation fails
    }

    // Check if interest is within the range
    if (interest < loan_interest_from) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: `Interest must be greater than or equal to ${loan_interest_from}`,
        });
        return; // Stop the function if validation fails
    } else if (interest > loan_interest_to) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: `Interest must be less than or equal to ${loan_interest_to}`,
        });
        return; // Stop the function if validation fails
    }

    if (!lending_officer) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please create or select a Lending Officer.',
        });
        return;
    }

    if (!collector_officer) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please create or select a Collector Officer.',
        });
        return;
    }

    if (!loan_broker) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please create or select a Loan Broker.',
        });
        return;
    }




    leasing_type="-";
    loan_number="-";

// Initialize an empty array to store table data
    installment=[];


    let saving=$("#enable_saving").text();

    // if (saving==="Yes"){

// Iterate over each row of the table
    $('#installment_table tbody tr').each(function() {
        var rowData = {};

        $(this).find('td').each(function(index) {
            var cellData = $(this).text().trim();

            // ===== Normal column mapping =====
            switch(index) {
                case 0:
                    rowData.No = cellData;
                    break;
                case 1:
                    rowData.installmentDate = cellData;
                    break;
                case 2:
                    rowData.installmentAmount = cellData;
                    break;
                case 3:
                    rowData.capitalAmount = cellData;
                    break;
                case 4:
                    rowData.interestAmount = cellData;
                    break;
                case 5:
                    rowData.panaltyDate = cellData;
                    break;
                case 6:
                    rowData.panaltyAmount = cellData;
                    break;
                case 7:
                    rowData.savingAmount = cellData;
                    break;
                case 8:
                    rowData.totalAmount = cellData;
                    break;
                case 9:
                    rowData.paidAmount = cellData;
                    break;
                case 10:
                    rowData.panaltyBalance = cellData;
                    break;
                case 11:
                    rowData.installmentBalance = cellData;
                    break;
                case 12:
                    rowData.savingBalance = cellData;
                    break;
                case 13:
                    rowData.totalBalance = cellData;
                    break;
                // ===== NEW columns for "fixed" + "according_to_route" =====
                case 14:
                    if (route_collection_type === "fixed" && collection_date_type_global === "according_to_route") {
                        rowData.collectionDate = cellData;
                    } else {
                        rowData.status = cellData;
                    }
                    break;
                case 15:
                    if (route_collection_type === "fixed" && collection_date_type_global === "according_to_route") {
                        rowData.difference = cellData;
                    }
                    break;
                case 16:
                    if (route_collection_type === "fixed" && collection_date_type_global === "according_to_route") {
                        rowData.status = cellData;
                    }
                    break;
                default:
                    break;
            }
        });

        installment.push(rowData);
    });


    // Initialize an empty array to store table data
    loan_charge_table=[];

// Iterate over each row of the table
    $('#loan_charge_table tbody tr').each(function() {
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
                    rowData.Description = cellData; // Add No column data
                    break;
                case 1:
                    rowData.Type = cellData;
                    break;
                case 2:
                    rowData.Amount = cellData;
                    break;
                default:
                    break;
            }

        });

        // Push the row data object to the table data array
        loan_charge_table.push(rowData);
    });







// Assuming witnessCount contains the number of witnesses
    var witnessCount = witness; // for example
    var witnessesArray = []; // Array to store witness data

    for (var i = 1; i <= witnessCount; i++) {
        var cus_id = $('#customer_' + i).val();
        var type = $('#type_' + i).val();

        if (cus_id!=="0"){
            var witnessData = {
                guatantor_type: type,
                cus_id: cus_id
            };

            // Push the object to the witnessesArray
            witnessesArray.push(witnessData);
        }
    }

// Send this array to the backend
//     loan_broker,loan_broker_commission

    if (installment==""){
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'No Installment Found!',
        })

    }else{

        // === NEW: Prepare loan details (fallbacks) ===
        const loanDetails = {
            Loan_No: (type_loan_number && type_loan_number !== '') ? type_loan_number : loan_number_txt,
            Amount: Number(loan_amount) || 0,
            Interest_Rate: Number(interest) || 0,
            Installment_Count: Number(ins_count) || 0,
            Interest_Amount: Number(interest_amount) || 0,
        };




        // Count how many documents have files uploaded
        let uploadedDocumentsCount = 0;
        $('input[type="file"]').each(function() {
            if (this.files && this.files[0]) {
                uploadedDocumentsCount++;
            }
        });

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
                        saving_amount:saving_amount,
                        issue_date:issue_date,
                        loan_broker:loan_broker,
                        loan_broker_commission:loan_broker_commission,
                        installment:installment,
                        loan_charge_table:loan_charge_table,
                        bank_acc:bank_acc,
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
                        loan_type:loan_type,
                        witnessesArray:witnessesArray,
                        total_capital_amount:total_capital_amount,
                        total_interest_amount:total_interest_amount,
                        interest_method:interest_method,
                        Interest_period:Interest_period,
                        vehicle_num:vehicle_num,
                        lease_type:lease_type,
                        lending_officer:lending_officer,
                        collector_officer:collector_officer,
                        saving:saving,
                        repayment_duration_period:repayment_duration_period,
                        type_loan_number:type_loan_number,
                        route_collection_type:route_collection_type,
                        collection_date_type_global:collection_date_type_global,
                        uploaded_documents_count:uploadedDocumentsCount
                    },
                    success: function (data, textStatus, xhr) {
                        if (xhr.status === 200) {
                            if (data.item==="1"){
                                Swal.fire("Error!", "This loan number already exist !", "error");
                                return;
                            }

                            // === NEW: Merge server-returned details if provided ===
                            if (data.loan) {
                                if (data.loan.loan_no)        loanDetails.Loan_No = data.loan.loan_no;
                                if (data.loan.amount)         loanDetails.Amount = Number(data.loan.amount);
                                if (data.loan.interest_rate)  loanDetails.Interest_Rate = Number(data.loan.interest_rate);
                                if (data.loan.installments)   loanDetails.Installment_Count = Number(data.loan.installments);
                                if (data.loan.interest_amt)   loanDetails.Interest_Amount = Number(data.loan.interest_amt);
                            }

                            // === NEW: customer merge ===
                            if (data.customer) {
                                loanDetails.Customer_No   = data.customer.cus_number || '-';
                                loanDetails.Customer_Name = data.customer.name
                                    || [data.customer.first_name, data.customer.last_name].filter(Boolean).join(' ')
                                    || '-';
                            }

                            // proceed to file upload + final success popup with details
                            save_doc(data.item, loanDetails);
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

function save_doc(id, loanDetails) {
    const fmt2 = (n) => new Intl.NumberFormat(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(n || 0));

    // Build a beautiful, centered details card
    const detailsHTML = `
        <div style="text-align:center; margin-top:10px;">
            <h3 style="margin-bottom:15px; color:#1A2942; font-weight:600;">Loan Created Successfully</h3>
            <table style="
                width:90%;
                margin:0 auto;
                border-collapse:collapse;
                background:#f9f9f9;
                border-radius:8px;
                box-shadow:0 0 4px rgba(0,0,0,0.1);
                overflow:hidden;
                font-size:15px;
            ">
                <tbody>
                    <tr style="background:#eef2f5;">
                        <td style="padding:8px 15px; text-align:left; font-weight:600; width:40%;">Customer No</td>
                        <td style="padding:8px 15px; text-align:left;">${loanDetails?.Customer_No ?? '-'}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 15px; text-align:left; font-weight:600;">Customer Name</td>
                        <td style="padding:8px 15px; text-align:left;">${loanDetails?.Customer_Name ?? '-'}</td>
                    </tr>
                    <tr style="background:#eef2f5;">
                        <td style="padding:8px 15px; text-align:left; font-weight:600;">Loan No</td>
                        <td style="padding:8px 15px; text-align:left;">${loanDetails?.Loan_No ?? '-'}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 15px; text-align:left; font-weight:600;">Amount</td>
                        <td style="padding:8px 15px; text-align:left;">${fmt2(loanDetails?.Amount)}</td>
                    </tr>
                    <tr style="background:#eef2f5;">
                        <td style="padding:8px 15px; text-align:left; font-weight:600;">Interest Rate</td>
                        <td style="padding:8px 15px; text-align:left;">${fmt2(loanDetails?.Interest_Rate)}%</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 15px; text-align:left; font-weight:600;">Installments</td>
                        <td style="padding:8px 15px; text-align:left;">${loanDetails?.Installment_Count ?? '-'}</td>
                    </tr>
                    <tr style="background:#eef2f5;">
                        <td style="padding:8px 15px; text-align:left; font-weight:600;">Interest Amount</td>
                        <td style="padding:8px 15px; text-align:left;">${fmt2(loanDetails?.Interest_Amount)}</td>
                    </tr>
                </tbody>
            </table>
            <p style="margin-top:15px; font-size:14px; color:#666;">
                You will be redirected to the <b>Loan Approval</b> page shortly...
            </p>
        </div>
    `;

    var formData = new FormData();
    $('input[type="file"]').each(function(index, element) {
        var file = element.files[0];
        var documentName = $(element).closest('tr').find('td:first').text().trim();
        var checked = $(`#check${index + 1}`).prop('checked') ? 1 : 0;

        if (documentName !== '') {
            if (file) formData.append('documents[]', file);
            formData.append('documentNames[]', documentName);
            formData.append('issue_checked[]', checked);
        }
    });
    formData.append('id', id);

    Swal.fire({
        title: 'Processing...',
        html: `
            <p>The loan issuing process may take some time depending on your document upload sizes.</p>
            <div id="progress-container" style="width: 100%; background-color: #e9ecef; border-radius: 0.25rem;">
                <div id="progress-bar" style="width: 0%; height: 20px; background-color: #1A2942; border-radius: 0.25rem;"></div>
            </div>`,
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => { Swal.showLoading(); }
    });

    $.ajax({
        url: "/save-files",
        method: "POST",
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
        data: formData,
        contentType: false,
        processData: false,
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var percentValue = Math.round((evt.loaded / evt.total) * 100);
                    $('#progress-bar').css('width', percentValue + '%');
                }
            }, false);
            return xhr;
        },
        success: function() {
            Swal.close();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Success!",
                html: detailsHTML,
                showConfirmButton: false,
                timer: 15000,          // ⏱ stays visible for 15 seconds
                timerProgressBar: true,
                didOpen: () => {
                    const swalContainer = Swal.getHtmlContainer();
                    if (swalContainer) swalContainer.style.textAlign = "center";
                }
            }).then(() => {
                window.location.href = "/pendingloan";
            });
        },
        error: function() {
            Swal.close();
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Success!",
                html: detailsHTML + '<p style="color:#b94a48;margin-top:6px;">(Document upload failed, but the loan was created.)</p>',
                showConfirmButton: false,
                timer: 15000,
                timerProgressBar: true
            }).then(() => {
                window.location.href = "/pendingloan";
            });
        }
    });
}







