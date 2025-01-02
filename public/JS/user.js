function updateStatusUser (user_id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to update status ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Update it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "GET",
                url: '/user/update/'+user_id+'/',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Successfully changed user status !'
                    }).then(function() {
                        window.location.reload();
                    });
                }
            });
        }
    })
}



function saveUserDesignation () {

    let designation=$("#designation").val();
    let desi_level=$("#desi_level").val();
    let loan_create = $("#loan_create").is(":checked") ? 1 : 0;
    let loan_approve = $("#loan_approve").is(":checked") ? 1 : 0;
    let max_amount_create=$("#max_amount_create").val();
    let max_amount_approve=$("#max_amount_approve").val();

    if (designation==="" || desi_level==="" || loan_create==="" || loan_approve==="" || max_amount_create==="" || max_amount_approve===""){

        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please fill all required fields !'
        })
    }else{
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to save this designation ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: '/user/designation',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        designation:designation,
                        desi_level:desi_level,
                        loan_create:loan_create,
                        loan_approve:loan_approve,
                        max_amount_create:max_amount_create,
                        max_amount_approve:max_amount_approve
                    },
                    success: function (data) {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Successfully saved designation !'
                        }).then(function() {
                            window.location.reload();
                        });
                    }
                });
            }
        })
    }
}

function isValidEmail(email) {
    var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    return emailPattern.test(email);
}



function validateContactNumber(event) {
    var charCode = event.which || event.keyCode;
    // Check if the pressed key is a digit (0-9) or a special key like backspace or delete
    if (charCode < 48 || charCode > 57) {
        event.preventDefault();
    }
}
