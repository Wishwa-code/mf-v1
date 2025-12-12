<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    // Handle branch switching for both super and non-super users
    $(document).on('click', '.branch-option', function (e) {
        e.preventDefault();
        const branchId = $(this).data('branch-id');
        const branchName = $(this).data('branch-name');

        $.ajax({
            url: '/update-branch',
            method: 'POST',
            data: {
                branch_id: branchId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function () {
                // Update UI and reload to apply scoping
                $('.branch-text').text(branchName);
                window.location.reload();
            },
            error: function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unauthorized or error updating branch';
                Swal.fire('Error', msg, 'error');
            }
        });
    });
</script>

<script>
    // Function to update the Grand Total
    function updateGrandTotal() {
        let grandTotal = 0;
        $("#modalTableBody tr").each(function () {
            let total = parseFloat($(this).find(".total-amount").text()) || 0;
            grandTotal += total;
        });
        $("#grandTotal").text(grandTotal.toFixed(2));
    }

    // Add to Table Button Click
    $("#addToTable").click(function () {
        let amount = parseFloat($("#amount").val());
        let quantity = parseInt($("#quantity").val());

        if (!amount || !quantity || amount <= 0 || quantity <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Input',
                text: 'Please select a valid amount and quantity.',
            });
            return;
        }

        let totalAmount = amount * quantity;
        let existingRow = $("#modalTableBody").find(`tr[data-amount='${amount}']`);

        if (existingRow.length > 0) {
            // Update existing row
            let existingQuantity = parseInt(existingRow.find(".quantity").text());
            let newQuantity = existingQuantity + quantity;
            let newTotal = amount * newQuantity;

            existingRow.find(".quantity").text(newQuantity);
            existingRow.find(".total-amount").text(newTotal.toFixed(2));

            Swal.fire({
                icon: 'info',
                title: 'Entry Updated',
                text: `Updated quantity for amount ${amount}`,
            });
        } else {
            // Add new row
            let rowCount = $("#modalTableBody tr").length + 1;

            $("#modalTableBody").append(`
            <tr data-amount="${amount}">
                <td>${rowCount}</td>
                <td class="amount">${amount}</td>
                <td class="quantity">${quantity}</td>
                <td class="total-amount">${totalAmount.toFixed(2)}</td>
                <td><button class="btn btn-danger btn-sm remove-entry">Remove</button></td>
            </tr>
        `);

            Swal.fire({
                icon: 'success',
                title: 'Entry Added',
                text: `Amount: ${amount}, Quantity: ${quantity}`,
            });
        }

        updateGrandTotal();
        $("#amount").val('');
        $("#quantity").val('');
    });

    // Handle Remove Entry button
    $(document).on('click', '.remove-entry', function () {
        $(this).closest('tr').remove();
        updateGrandTotal();
    });
    $(document).ready(function () {
        $("#cashierStartModal").on('show.bs.modal', function () {
            loadSavedPlotEntries();
        });

        $("#dayEndModal").on('show.bs.modal', function () {
            $.ajax({
                url: '/cashier/day-end-data',
                method: 'GET',
                success: function (response) {
                    $("#plotAmount").val(response.startingCash.toFixed(2));


                    // Inside AJAX success of #dayEndModal
                    let cashInBody = $("#cashInTableBody").empty();
                    response.cashIn.forEach((item, i) => {
                        cashInBody.append(`
        <tr>
            <td>${i + 1}</td>
            <td>${item.Date_Time}</td>
            <td>${item.Type}</td>
            <td>${item.Description}</td>
            <td>${parseFloat(item.Debit).toFixed(2)}</td>
        </tr>
    `);
                    });

                    let cashOutBody = $("#cashOutTableBody").empty();
                    response.cashOut.forEach((item, i) => {
                        cashOutBody.append(`
        <tr>
            <td>${i + 1}</td>
            <td>${item.Date_Time}</td>
            <td>${item.Type}</td>
            <td>${item.Description}</td>
            <td>${parseFloat(item.Credit).toFixed(2)}</td>
        </tr>
    `);
                    });






                    $("#totalIncome").text(response.totalIncome.toFixed(2));
                    $("#totalExpenses").text(response.totalExpenses.toFixed(2));
                    $("#balanceAmount").val(response.balanceAmount.toFixed(2));


                    updateCashDrawerTotals(); // Reset drawer totals if needed
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error loading data',
                        text: 'Could not fetch day end data.'
                    });
                }
            });
        });
        $("#dayEndModal").on('show.bs.modal', function () {
            $.ajax({
                url: '/cashier/get-saved-day-end',
                method: 'GET',
                success: function (res) {

                    // Set summary fields
                    $("#plotAmount").val(parseFloat(res.startingCash).toFixed(2));
                    $("#totalIncome").text(parseFloat(res.totalIncome).toFixed(2));
                    $("#totalExpenses").text(parseFloat(res.totalExpenses).toFixed(2));
                    $("#balanceAmount").val(parseFloat(res.balanceAmount).toFixed(2));

                    // Inside success callback for /cashier/get-saved-day-end
                    let cashInBody = $("#cashInTableBody").empty();
                    res.cashIn.forEach((item, i) => {
                        cashInBody.append(`
        <tr>
            <td>${i + 1}</td>
            <td>${item.Date_Time}</td>
            <td>${item.Type}</td>
            <td>${item.Description}</td>
            <td>${parseFloat(item.Debit).toFixed(2)}</td>
        </tr>
    `);
                    });

                    let cashOutBody = $("#cashOutTableBody").empty();
                    res.cashOut.forEach((item, i) => {
                        cashOutBody.append(`
        <tr>
            <td>${i + 1}</td>
            <td>${item.Date_Time}</td>
            <td>${item.Type}</td>
            <td>${item.Description}</td>
            <td>${parseFloat(item.Credit).toFixed(2)}</td>
        </tr>
    `);
                    });


                    // Cash drawer entries
                    $("#cashDrawerTableBody").empty();
                    res.cashDrawerEntries.forEach((entry, index) => {
                        $("#cashDrawerTableBody").append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td class="cash-amt">${parseFloat(entry.denomination)}</td>
                        <td class="cash-qty">${parseInt(entry.quantity)}</td>
                        <td class="cash-total">${parseFloat(entry.total_amount).toFixed(2)}</td>
                        <td></td> <!-- Empty cell (no Remove button) -->
                    </tr>
                `);
                    });

                    // Totals
                    $("#totalCashDrawer").text(parseFloat(res.savedData.cash_drawer_total).toFixed(2));
                    $("#balanceDifference").text(parseFloat(res.savedData.balance_difference).toFixed(2));

                    // Disable inputs if already saved
                    if (res.dayEndExists) {
                        $("#cashDrawerForm :input").prop("disabled", true);
                        $("#saveDayEnd").prop("disabled", true);
                        $("#addCashToTable").prop("disabled", true);
                    } else {
                        $("#cashDrawerForm :input").prop("disabled", false);
                        $("#saveDayEnd").prop("disabled", false);
                        $("#addCashToTable").prop("disabled", false);
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load Day End data.'
                    });
                }
            });
        });

    });



    $("#saveEntries").click(function () {
        let entries = [];

        $("#modalTableBody tr").each(function () {
            let amount = parseFloat($(this).find(".amount").text());
            let quantity = parseInt($(this).find(".quantity").text());
            let total = parseFloat($(this).find(".total-amount").text());

            entries.push({
                amount: amount,         // backend expects this as the denomination
                quantity: quantity,     // qty
                totalAmount: total      // calculated amount = denomination * quantity
            });
        });

        if (entries.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Data',
                text: 'Please add at least one entry before saving.',
            });
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to save the entries.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, save it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/save-cashier-data',
                    method: 'POST',
                    data: {
                        entries: entries,
                        grandTotal: parseFloat($("#grandTotal").text()),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        });
                        loadSavedPlotEntries();
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'An error occurred while saving.'
                        });
                    }
                });
            }
        });
    });
    function loadSavedPlotEntries() {
        $.ajax({
            url: '/get-today-cashier-data',
            method: 'GET',
            success: function (response) {
                let tableBody = $("#modalTableBody");
                tableBody.empty();

                let grandTotal = 0;

                response.entries.forEach((entry, index) => {
                    let amount = parseFloat(entry.money);
                    let qty = parseInt(entry.qty);
                    let total = parseFloat(entry.amount);

                    grandTotal += total;

                    tableBody.append(`
                    <tr data-amount="${amount}">
                        <td>${index + 1}</td>
                        <td class="amount">${amount}</td>
                        <td class="quantity">${qty}</td>
                        <td class="total-amount">${total.toFixed(2)}</td>
                        <td><button class="btn btn-danger btn-sm remove-entry">Remove</button></td>
                    </tr>
                `);
                });

                $("#grandTotal").text(grandTotal.toFixed(2));
                // Disable save/add buttons if finalized
                if (response.isFinalized) {
                    $("#saveEntries").prop("disabled", true);
                    $("#addToTable").prop("disabled", true);
                    $("#amount, #quantity").prop("disabled", true);
                    $("#modalTableBody .remove-entry").prop("disabled", true); // ✅ Disable buttons instead of removing
                } else {
                    $("#saveEntries").prop("disabled", false);
                    $("#addToTable").prop("disabled", false);
                    $("#amount, #quantity").prop("disabled", false);
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Could not load saved entries.'
                });
            }
        });
    }


    $("#printDayStartReport").click(function () {
        let tableClone = $("#modalTableBody").closest("table").clone();

        // Remove the last column (Action) in both header and body
        tableClone.find("thead tr th:last-child").remove();
        tableClone.find("tbody tr").each(function () {
            $(this).find("td:last-child").remove();
        });

        // Get current date and time
        const now = new Date();
        const dateStr = now.toLocaleDateString();
        const timeStr = now.toLocaleTimeString();

        // Get user name from Laravel session
        const userName = `{{ session('Full_Name') }}`;
        const branchName = `{{ session('branch_name') }}`;

        let printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Day Start Report</title>');
        printWindow.document.write('<style>table { width: 100%; border-collapse: collapse; } th, td { padding: 8px; border: 1px solid #ccc; }</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<h3>Day Start Report</h3>');
        printWindow.document.write(`<p><strong>User:</strong> ${userName}</p>`);
        printWindow.document.write(`<p><strong>Branch:</strong> ${branchName}</p>`);
        printWindow.document.write(`<p><strong>Date:</strong> ${dateStr} <strong>Time:</strong> ${timeStr}</p>`);
        printWindow.document.write(tableClone.prop('outerHTML'));
        printWindow.document.write('<h4>Total: ' + $("#grandTotal").text() + '</h4>');
        printWindow.document.write('</body></html>');

        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    });



    $("#addCashToTable").click(function () {
        let amount = parseFloat($("#cashAmount").val());
        let quantity = parseInt($("#cashQuantity").val());

        if (!amount || !quantity || amount <= 0 || quantity <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Input',
                text: 'Please enter valid denomination and quantity.',
            });
            return;
        }

        let total = amount * quantity;
        let existingRow = $("#cashDrawerTableBody").find(`tr[data-amount='${amount}']`);

        if (existingRow.length > 0) {
            let existingQty = parseInt(existingRow.find(".cash-qty").text());
            let newQty = existingQty + quantity;
            let newTotal = amount * newQty;

            existingRow.find(".cash-qty").text(newQty);
            existingRow.find(".cash-total").text(newTotal.toFixed(2));
        } else {
            let rowCount = $("#cashDrawerTableBody tr").length + 1;

            $("#cashDrawerTableBody").append(`
            <tr data-amount="${amount}">
                <td>${rowCount}</td>
                <td class="cash-amt">${amount}</td>
                <td class="cash-qty">${quantity}</td>
                <td class="cash-total">${total.toFixed(2)}</td>
                <td><button class="btn btn-danger btn-sm remove-cash-row">Remove</button></td>
            </tr>
        `);
        }

        updateCashDrawerTotals();

        $("#cashAmount").val('');
        $("#cashQuantity").val('');
    });

    function updateCashDrawerTotals() {
        let total = 0;
        $("#cashDrawerTableBody tr").each(function () {
            let rowTotal = parseFloat($(this).find(".cash-total").text()) || 0;
            total += rowTotal;
        });
        $("#totalCashDrawer").text(total.toFixed(2));

        let balanceAmount = parseFloat($("#balanceAmount").val()) || 0;
        let difference = balanceAmount - total;
        $("#balanceDifference").text(difference.toFixed(2));
    }


    $(document).on('click', '.remove-cash-row', function () {
        $(this).closest('tr').remove();
        updateCashDrawerTotals();
    });

    $("#saveDayEnd").click(function () {
        let startingCash = parseFloat($("#plotAmount").val());
        let totalIncome = parseFloat($("#totalIncome").text());
        let totalExpense = parseFloat($("#totalExpenses").text());
        let balanceAmount = parseFloat($("#balanceAmount").val());
        let cashDrawerTotal = parseFloat($("#totalCashDrawer").text());
        let balanceDifference = parseFloat($("#balanceDifference").text());


        if (balanceDifference != 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Unbalanced Cash Drawer',
                html: `Balance Difference must be <b>0.00</b> to save the Day End.<br><br>
                   Current Difference: <strong style="color:red;">${balanceDifference.toFixed(2)}</strong>`,
            });
            return; // ❌ Stop further execution
        }


        let drawerEntries = [];

        $("#cashDrawerTableBody tr").each(function () {
            drawerEntries.push({
                denomination: parseFloat($(this).find(".cash-amt").text()),
                quantity: parseInt($(this).find(".cash-qty").text()),
                total_amount: parseFloat($(this).find(".cash-total").text())
            });
        });


        $.ajax({
            url: '/cashier/bank-list',
            method: 'GET',
            success: function (banks) {
                let selectOptions = banks.map(bank =>
                    `<option value="${bank.Idbank}">${bank.Bank_Name} - ${bank.Account_Name}</option>`
                ).join('');

                Swal.fire({
                    title: 'Select Bank to Deposit',
                    html: `
                <label>Select a bank account to deposit:</label>
                <select id="swal-bank-select" class="form-control mt-2">
                    <option value="">-- Select Bank --</option>
                    ${selectOptions}
                </select>
            `,
                    showCancelButton: true,
                    confirmButtonText: 'Save Day End',
                    preConfirm: () => {
                        const selectedBankId = $('#swal-bank-select').val();
                        if (!selectedBankId) {
                            Swal.showValidationMessage('Please select a bank account.');
                        }
                        return selectedBankId;
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        let selectedBankId = result.value;

                        $.ajax({
                            url: '/cashier/save-day-end',
                            method: 'POST',
                            data: {
                                starting_cash: startingCash,
                                total_income: totalIncome,
                                total_expense: totalExpense,
                                balance_amount: balanceAmount,
                                cash_drawer_total: cashDrawerTotal,
                                balance_difference: balanceDifference,
                                selected_bank_id: selectedBankId,
                                cash_drawer_entries: drawerEntries,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Saved',
                                    text: res.message
                                });
                                $("#saveDayEnd").prop('disabled', true);
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message || 'Failed to save day end.'
                                });
                            }
                        });
                    }
                });
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Could not load bank list.'
                });
            }
        });

    });


    $("#printDayEndReport").click(function () {
        const printWindow = window.open('', '', 'height=700,width=900');
        const incomeRows = $("#cashInTableBody").html();
        const expenseRows = $("#cashOutTableBody").html();
        const drawerRows = $("#cashDrawerTableBody").html();

        const html = `
        <html>
        <head>
            <title>Day End Summary</title>
            <style>
                body { font-family: Arial; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #000; padding: 6px; text-align: left; }
                h2, h4 { margin: 10px 0; }
            </style>
        </head>
        <body>
            <h2>📋 Day End Summary - ${new Date().toLocaleDateString()}</h2>
            <h4>Branch: {{ session('branch_name') }}</h4>

            <h4>Starting Cash: ${$("#plotAmount").val()}</h4>
            <h4>Total Income: ${$("#totalIncome").text()}</h4>
            <h4>Total Expenses: ${$("#totalExpenses").text()}</h4>
            <h4>Balance Amount: ${$("#balanceAmount").val()}</h4>

            <h3>Other Incomes</h3>
            <table>
                <thead><tr><th>#</th><th>Date</th><th>Type</th><th>Description</th><th>Amount</th></tr></thead>
                <tbody>${incomeRows}</tbody>
            </table>

            <h3>Other Expenses</h3>
            <table>
                <thead><tr><th>#</th><th>Date</th><th>Type</th><th>Description</th><th>Amount</th></tr></thead>
                <tbody>${expenseRows}</tbody>
            </table>

            <h3>Cash Drawer</h3>
            <table>
                <thead><tr><th>#</th><th>Denomination</th><th>Qty</th><th>Total</th><th></th></tr></thead>
                <tbody>${drawerRows.replace(/<td>.*Remove.*<\/td>/g, '')}</tbody>
            </table>

            <h4>Total Cash Drawer Balance: ${$("#totalCashDrawer").text()}</h4>
            <h4 style="color: red;">Balance Difference: ${$("#balanceDifference").text()}</h4>
        </body>
        </html>
    `;

        printWindow.document.write(html);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    });



</script>
