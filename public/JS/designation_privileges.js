// designation_privileges.js
// Collect, save and load designation based privileges using JSON column `privileges` in `designation` table.

const collectDesignationPrivileges = () => {
    const privileges = {};
    $(".access_module").each(function () {
        const key = $(this).data("key");
        privileges[key] = $(this).prop("checked") ? 1 : 0;
    });
    return privileges;
};

const resetAllPrivilegeChecks = () => {
    $(".access_module").prop("checked", false);
};

function savePrivileges(e){
    e.preventDefault();
    const designationId = $("#userid").val();
    const propagate = $("#propagate-users").is(":checked") ? 1 : 0;
    if(designationId === "0"){
        Swal.fire("Please select Designation!", "", "error");
        return;
    }
    const privileges = collectDesignationPrivileges();
    Swal.fire({
        title:"Update Privileges?",
        icon:"warning",
        showCancelButton:true,
        confirmButtonText:"Yes, update"
    }).then(result => {
        if(result.isConfirmed){
            $.ajax({
                type:'POST',
                url:'/designation/privileges/save',
                headers:{
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data:{ designationId: designationId, privileges: privileges, propagate: propagate },
                success:function(res){
                    if (propagate === 1 && res && typeof res.updated_users !== 'undefined') {
                        Swal.fire("Updated!", `Designation privileges updated and applied to ${res.updated_users} user(s).`, "success");
                    } else {
                        Swal.fire("Updated!", "Designation privileges updated.", "success");
                    }
                },
                error:function(){
                    Swal.fire("Error","Unable to update privileges","error");
                }
            });
        }
    });
}

function load_to_table(designationId){
    resetAllPrivilegeChecks();
    if(!designationId || designationId === '0') return;
    
    $.ajax({
        type:'GET',
        url:'/designation/privileges/load/'+designationId,
        success:function(res){
            if(res && res.privileges){
                const data = res.privileges; // object map key=>1/0
                
                // Set all checkboxes based on saved data without triggering events
                Object.keys(data).forEach(k=>{
                    const val = data[k];
                    const $cb = $(".access_module[data-key='"+k+"']");
                    if($cb.length){
                        // Use prop without triggering change events
                        $cb.prop("checked", parseInt(val) === 1);
                    }
                });
                
                // Show sub-sections for permissions that have enabled sub-items
                $('.sub-topic-wrapper').each(function(){
                    const wrapperKey = $(this).data('wrapper');
                    const hasEnabledSubs = $(this).find('.access_module:checked').length > 0;
                    if(hasEnabledSubs){
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        }
    });
}
