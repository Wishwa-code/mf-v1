
function updateTemplate() {
    var smsContent = $('#smsContent').val();
    var smsTypeSelect = $('#smsTypeSelect').val();



    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to save this SMS Format ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Update it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "/save_sms",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    smsContent: smsContent,
                    smsTypeSelect:smsTypeSelect
                },
                success: function (data, textStatus, xhr) {

                    if (xhr.status === 200) {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Successfully saved!",
                        });
                    } else {
                        Swal.fire("Error!", "Failed to save data!", "error");
                    }
                },
            });
        }
    });
}

function changeToggle() {
    var smsSendStatus = $("#smsSendToggle").prop('checked');
    var smsTypeSelect = $('#smsTypeSelect').val();

    if (!smsSendStatus) { // When trying to deactivate
        $.ajax({
            type: "POST",
            url: "/updatesmsstatus",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                smsSendStatus: '1',
                smsTypeSelect: smsTypeSelect
            },
            success: function (data, textStatus, xhr) {
                if (xhr.status === 200) {
                    Swal.fire("Updated!", "SMS Type has been activated.", "success");
                    $('#smsSendToggle').bootstrapToggle('on');
                } else {
                    Swal.fire("Error!", "Failed to save data!", "error");
                    $('#smsSendToggle').bootstrapToggle('on'); // Revert toggle state
                }
            },
            error: function () {
                Swal.fire("Error!", "Failed to save data!", "error");
                $('#smsSendToggle').bootstrapToggle('on'); // Revert toggle state
            }
        });
    } else { // When trying to activate
        $.ajax({
            type: "POST",
            url: "/updatesmsstatus",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                smsSendStatus: '0',
                smsTypeSelect: smsTypeSelect
            },
            success: function (data, textStatus, xhr) {
                if (xhr.status === 200) {
                    Swal.fire("Updated!", "SMS Type has been deactivated.", "success");
                    $('#smsSendToggle').bootstrapToggle('off');
                } else {
                    Swal.fire("Error!", "Failed to save data!", "error");
                    $('#smsSendToggle').bootstrapToggle('off'); // Revert toggle state
                }
            },
            error: function () {
                Swal.fire("Error!", "Failed to save data!", "error");
                $('#smsSendToggle').bootstrapToggle('off'); // Revert toggle state
            }
        });
    }
}



