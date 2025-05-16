const savePrivileges = (e) => {
    e.preventDefault();

    const userId = $("#userid").val();
    if (userId === "0") {
        Swal.fire("Please select User!", "", "error");
        return;
    }

    const privileges = {};
    $(".access_module").each(function () {
        const key = $(this).data("key");
        const isChecked = $(this).prop("checked") ? 1 : 0;
        privileges[key] = isChecked;
    });

    Swal.fire({
        title: "Update Privileges?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, update",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "/privileges",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: { userId: userId, privileges: privileges },
                success: function () {
                    Swal.fire("Updated!", "Privileges updated.", "success").then(() => {
                        location.reload();
                    });
                },
            });
        }
    });
};


function load_to_table(userId) {
    $(".access_module").prop("checked", false);

    $.ajax({
        type: "GET",
        url: "/privileges/load/" + userId,
        success: function (data) {
            const permissions = data.privileges;
            permissions.forEach((p) => {
                $(`.access_module[data-key='${p.permission_key}']`).prop("checked", p.value == 1);
            });
        },
    });
}
