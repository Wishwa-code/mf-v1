const savePrivileges = (e) => {
    e.preventDefault();

    // Create an object to store checkbox values
    const checkboxValues = {
        dashboard: $("#dashboard").prop("checked") ? 1 : 0,
        customer: $("#1").prop("checked") ? 1 : 0,
        add_customer: $("#2").prop("checked") ? 1 : 0,
        view_customer: $("#3").prop("checked") ? 1 : 0,
        loan_center: $("#4").prop("checked") ? 1 : 0,
        create_loan_center: $("#5").prop("checked") ? 1 : 0,
        view_center: $("#6").prop("checked") ? 1 : 0,
        create_group: $("#7").prop("checked") ? 1 : 0,
        view_group: $("#8").prop("checked") ? 1 : 0,
        assign_customer_to_group: $("#9").prop("checked") ? 1 : 0,
        guarantee: $("#10").prop("checked") ? 1 : 0,
        add_guarantee: $("#11").prop("checked") ? 1 : 0,
        view_guarantee: $("#12").prop("checked") ? 1 : 0,
        product: $("#13").prop("checked") ? 1 : 0,
        add_product: $("#14").prop("checked") ? 1 : 0,
        view_product: $("#15").prop("checked") ? 1 : 0,
        issue_loan: $("#16").prop("checked") ? 1 : 0,
        pending_loan: $("#17").prop("checked") ? 1 : 0,
        current_loan: $("#18").prop("checked") ? 1 : 0,
        loan_in_arrease: $("#19").prop("checked") ? 1 : 0,
        payment: $("#20").prop("checked") ? 1 : 0,
        add_re_payment: $("#21").prop("checked") ? 1 : 0,
        view_repayment: $("#22").prop("checked") ? 1 : 0,
        pending_approval_repayment: $("#23").prop("checked") ? 1 : 0,
        approval_repayment: $("#24").prop("checked") ? 1 : 0,
        agent_collection: $("#25").prop("checked") ? 1 : 0,
        loan_calculator: $("#26").prop("checked") ? 1 : 0,
        calender: $("#27").prop("checked") ? 1 : 0,
        expenses: $("#28").prop("checked") ? 1 : 0,
        add_expenses: $("#29").prop("checked") ? 1 : 0,
        view_expenses: $("#30").prop("checked") ? 1 : 0,
        income: $("#31").prop("checked") ? 1 : 0,
        add_income: $("#32").prop("checked") ? 1 : 0,
        view_income: $("#33").prop("checked") ? 1 : 0,
        user: $("#34").prop("checked") ? 1 : 0,
        create_user: $("#35").prop("checked") ? 1 : 0,
        user_privilage: $("#36").prop("checked") ? 1 : 0,
        report: $("#37").prop("checked") ? 1 : 0,
        report_1: $("#AllLoanDetailReport").prop("checked") ? 1 : 0,
        report_2: $("#customerreport_details").prop("checked") ? 1 : 0,
        report_3: $("#loanreport").prop("checked") ? 1 : 0,
        report_4: $("#borrowerreport").prop("checked") ? 1 : 0,
        report_5: $("#repaymentreport").prop("checked") ? 1 : 0,
        report_6: $("#customerrepaymentreport").prop("checked") ? 1 : 0,
        report_7: $("#deduct_report").prop("checked") ? 1 : 0,
        report_8: $("#cashbook").prop("checked") ? 1 : 0,
        report_9: $("#par").prop("checked") ? 1 : 0,
        report_10: $("#profit").prop("checked") ? 1 : 0,
        report_11: $("#gl_report").prop("checked") ? 1 : 0,
        report_12: $("#cash_flow").prop("checked") ? 1 : 0,
        report_13: $("#statement").prop("checked") ? 1 : 0,
        report_14: $("#sms_history").prop("checked") ? 1 : 0,
        report_15: $("#late_payment_report").prop("checked") ? 1 : 0,
        report_16: $("#ViewDateWiseCashFlow").prop("checked") ? 1 : 0,
        report_17: $("#MonthlyCollectionSummary").prop("checked") ? 1 : 0,
        add_bulk_re_payment: $("#51").prop("checked") ? 1 : 0,
        account: $("#52").prop("checked") ? 1 : 0,
        bank_details: $("#53").prop("checked") ? 1 : 0,
        chq_details: $("#54").prop("checked") ? 1 : 0,
        account_department: $("#account_department").prop("checked") ? 1 : 0,
    };


    const userId = $("#userid").val();

    if (userId === "0") {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Please select User !",
        });
    } else {
        // Confirm the action with a modal dialog
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to update these privileges?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Update it!",
        }).then((result) => {
            if (result.isConfirmed) {
                // Send an Ajax request to save privileges
                $.ajax({
                    type: "POST",
                    url: "/privileges",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: { checkboxValues: checkboxValues, userId: userId }, // Use ES6 shorthand
                    success: function (data) {
                        load_to_table(data.data);
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Successfully changed user privileges!",
                        }).then(function () {
                            window.location.reload();
                        });
                    },
                });
            }
        });
    }
};

function load_to_table(id) {

    $(".access_module").prop("checked", false);
    $.ajax({
        type: "GET",
        url: "privileges/load/" + id,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (data) {
            console.log(data);
            let value=data.items;
            // $.each(data.items, function(key, value) {
            console.log(value.customer);
            if (value.account_center === 1) {
                $("#account_department").prop("checked", true);
            } else {
                $("#account_department").prop("checked", false);
            }

            if (value.dashboard === 1) {
                $("#dashboard").prop("checked", true);
            } else {
                $("#dashboard").prop("checked", false);
            }

            if (value.customer === 1) {
                $("#1").prop("checked", true);
            } else {
                $("#1").prop("checked", false);
            }

            if (value.add_customer === 1) {
                $("#2").prop("checked", true);
            } else {
                $("#2").prop("checked", false);
            }

            if (value.view_customer === 1) {
                $("#3").prop("checked", true);
            } else {
                $("#3").prop("checked", false);
            }

            if (value.loan_center === 1) {
                $("#4").prop("checked", true);
            } else {
                $("#4").prop("checked", false);
            }

            if (value.create_loan_center === 1) {
                $("#5").prop("checked", true);
            } else {
                $("#5").prop("checked", false);
            }

            if (value.view_center === 1) {
                $("#6").prop("checked", true);
            } else {
                $("#6").prop("checked", false);
            }

            if (value.create_group === 1) {
                $("#7").prop("checked", true);
            } else {
                $("#7").prop("checked", false);
            }

            if (value.view_group === 1) {
                $("#8").prop("checked", true);
            } else {
                $("#8").prop("checked", false);
            }

            if (value.assign_customer_to_group === 1) {
                $("#9").prop("checked", true);
            } else {
                $("#9").prop("checked", false);
            }

            if (value.guarantee === 1) {
                $("#10").prop("checked", true);
            } else {
                $("#10").prop("checked", false);
            }

            if (value.add_guarantee === 1) {
                $("#11").prop("checked", true);
            } else {
                $("#11").prop("checked", false);
            }

            if (value.view_guarantee === 1) {
                $("#12").prop("checked", true);
            } else {
                $("#12").prop("checked", false);
            }

            if (value.product === 1) {
                $("#13").prop("checked", true);
            } else {
                $("#13").prop("checked", false);
            }

            if (value.add_product === 1) {
                $("#14").prop("checked", true);
            } else {
                $("#14").prop("checked", false);
            }

            if (value.view_product === 1) {
                $("#15").prop("checked", true);
            } else {
                $("#15").prop("checked", false);
            }

            if (value.issue_loan === 1) {
                $("#16").prop("checked", true);
            } else {
                $("#16").prop("checked", false);
            }

            if (value.pending_loan === 1) {
                $("#17").prop("checked", true);
            } else {
                $("#17").prop("checked", false);
            }

            if (value.current_loan === 1) {
                $("#18").prop("checked", true);
            } else {
                $("#18").prop("checked", false);
            }

            if (value.loan_in_arrease === 1) {
                $("#19").prop("checked", true);
            } else {
                $("#19").prop("checked", false);
            }

            if (value.payment === 1) {
                $("#20").prop("checked", true);
            } else {
                $("#20").prop("checked", false);
            }

            if (value.add_re_payment === 1) {
                $("#21").prop("checked", true);
            } else {
                $("#21").prop("checked", false);
            }

            if (value.view_repayment === 1) {
                $("#22").prop("checked", true);
            } else {
                $("#22").prop("checked", false);
            }

            if (value.pending_approval_repayment === 1) {
                $("#23").prop("checked", true);
            } else {
                $("#23").prop("checked", false);
            }

            if (value.approval_repayment === 1) {
                $("#24").prop("checked", true);
            } else {
                $("#24").prop("checked", false);
            }

            if (value.agent_collection === 1) {
                $("#25").prop("checked", true);
            } else {
                $("#25").prop("checked", false);
            }

            if (value.loan_calculator === 1) {
                $("#26").prop("checked", true);
            } else {
                $("#26").prop("checked", false);
            }

            if (value.calender === 1) {
                $("#27").prop("checked", true);
            } else {
                $("#27").prop("checked", false);
            }

            if (value.expenses === 1) {
                $("#28").prop("checked", true);
            } else {
                $("#28").prop("checked", false);
            }

            if (value.add_expenses === 1) {
                $("#29").prop("checked", true);
            } else {
                $("#29").prop("checked", false);
            }

            if (value.view_expenses === 1) {
                $("#30").prop("checked", true);
            } else {
                $("#30").prop("checked", false);
            }

            if (value.income === 1) {
                $("#31").prop("checked", true);
            } else {
                $("#31").prop("checked", false);
            }

            if (value.add_income === 1) {
                $("#32").prop("checked", true);
            } else {
                $("#32").prop("checked", false);
            }

            if (value.view_income === 1) {
                $("#33").prop("checked", true);
            } else {
                $("#33").prop("checked", false);
            }


            if (value.user === 1) {
                $("#34").prop("checked", true);
            } else {
                $("#34").prop("checked", false);
            }

            if (value.create_user === 1) {
                $("#35").prop("checked", true);
            } else {
                $("#35").prop("checked", false);
            }

            if (value.user_privilage === 1) {
                $("#36").prop("checked", true);
            } else {
                $("#36").prop("checked", false);
            }

            if (value.report === 1) {
                $("#37").prop("checked", true);
            } else {
                $("#37").prop("checked", false);
            }

            if (value.report_1 === 1) {
                $("#AllLoanDetailReport").prop("checked", true);
            } else {
                $("#AllLoanDetailReport").prop("checked", false);
            }

            if (value.report_2 === 1) {
                $("#late_payment_report").prop("checked", true);
            } else {
                $("#late_payment_report").prop("checked", false);
            }

            if (value.report_3 === 1) {
                $("#ViewDateWiseCashFlow").prop("checked", true);
            } else {
                $("#ViewDateWiseCashFlow").prop("checked", false);
            }

            if (value.report_4 === 1) {
                $("#MonthlyCollectionSummary").prop("checked", true);
            } else {
                $("#MonthlyCollectionSummary").prop("checked", false);
            }

            if (value.report_5 === 1) {
                $("#customerreport_details").prop("checked", true);
            } else {
                $("#customerreport_details").prop("checked", false);
            }

            if (value.report_6 === 1) {
                $("#loanreport").prop("checked", true);
            } else {
                $("#loanreport").prop("checked", false);
            }

            if (value.report_7 === 1) {
                $("#borrowerreport").prop("checked", true);
            } else {
                $("#borrowerreport").prop("checked", false);
            }


            if (value.report_8 === 1) {
                $("#repaymentreport").prop("checked", true);
            } else {
                $("#repaymentreport").prop("checked", false);
            }

            if (value.report_9 === 1) {
                $("#customerrepaymentreport").prop("checked", true);
            } else {
                $("#customerrepaymentreport").prop("checked", false);
            }

            if (value.report_10 === 1) {
                $("#deduct_report").prop("checked", true);
            } else {
                $("#deduct_report").prop("checked", false);
            }


            if (value.report_11 === 1) {
                $("#cashbook").prop("checked", true);
            } else {
                $("#cashbook").prop("checked", false);
            }


            if (value.report_12 === 1) {
                $("#par").prop("checked", true);
            } else {
                $("#par").prop("checked", false);
            }

            if (value.report_13 === 1) {
                $("#profit").prop("checked", true);
            } else {
                $("#profit").prop("checked", false);
            }

            if (value.report_14 === 1) {
                $("#gl_report").prop("checked", true);
            } else {
                $("#gl_report").prop("checked", false);
            }

            if (value.report_15 === 1) {
                $("#cash_flow").prop("checked", true);
            } else {
                $("#cash_flow").prop("checked", false);
            }

            if (value.report_16 === 1) {
                $("#statement").prop("checked", true);
            } else {
                $("#statement").prop("checked", false);
            }

            if (value.report_17 === 1) {
                $("#sms_history").prop("checked", true);
            } else {
                $("#sms_history").prop("checked", false);
            }

            if (value.add_bulk_re_payment === 1) {
                $("#51").prop("checked", true);
            } else {
                $("#51").prop("checked", false);
            }


            if (value.account === 1) {
                $("#52").prop("checked", true);
            } else {
                $("#52").prop("checked", false);
            }

            if (value.bank_details === 1) {
                $("#53").prop("checked", true);
            } else {
                $("#53").prop("checked", false);
            }

            if (value.chq_details === 1) {
                $("#54").prop("checked", true);
            } else {
                $("#54").prop("checked", false);
            }


            // });
        },
    });
}
