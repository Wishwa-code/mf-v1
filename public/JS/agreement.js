
function updateTemplate() {
    var smsContent = quill.root.innerHTML;
    var smsTypeSelect = $('#smsTypeSelect').val();


    console.log(smsContent);



    // var smsContent = $('#cardBody').html();

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to save this Agreement Format ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Update it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "/save_agreement",
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




