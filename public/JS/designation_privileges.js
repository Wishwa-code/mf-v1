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
                data:{ designationId: designationId, privileges: privileges },
                success:function(){
                    Swal.fire("Updated!","Designation privileges updated.","success");
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
                Object.keys(data).forEach(k=>{
                    const val = data[k];
                    const $cb = $(".access_module[data-key='"+k+"']");
                    if($cb.length){
                        $cb.prop("checked", parseInt(val) === 1);
                        // ensure parent row visible if sub
                        const wrapperKey = $cb.closest('tbody.sub-topic-wrapper').data('wrapper');
                        if(wrapperKey){
                            // show its main section
                            $(".access_module[data-key='"+wrapperKey+"']").prop('checked', true);
                            $(".sub-topic-wrapper[data-wrapper='"+wrapperKey+"']").show();
                        }
                    }
                });
            }
        }
    });
}
