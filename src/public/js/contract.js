$(document).ready(function(){
    $('.contract_file_select').change(function(){
        console.log( URL.createObjectURL(this.files[0]));
        var f = this.files[0]
        if (f.size > 10485760  || f.fileSize > 10485760 ){
            Swal.fire(
                message,
                '10Mのファイルのみを選択して下さい',
                'warning'
            );
            this.value = null;
        }else{
            var target = $(this).attr('data-target');
            var fileName = $(this).val();
            var labelText = fileName.substr(12, fileName.length);
            $(".contract_file_show[data-target='"+target+"'").val(labelText);
            $("label[data-target='"+target+"'").text(formatDate());
            $(".local-view[data-target='"+target+"'").attr("href", URL.createObjectURL(this.files[0])).removeClass("contract-view-dis-none");
        }
    })

    $(".clear-file").click(function(event) {
        var target = $(this).attr('data-target');
        $(".contract_file_show[data-target='"+target+"'").val('');
        $("label[data-target='"+target+"'").text('');
        $(".note-area[data-target='"+target+"'").val('');
        $(".contract_file_select[data-target='"+target+"'").val('');
        $(".local-view[data-target='"+target+"'").addClass("contract-view-dis-none");
    });



    $("#form_submit").click(function(event) {
        $("#post_act").val(1);
        $("#edit_contract").submit();
    });

    $("#btn-add-contract").click(function(event) {
        $( ".contract-dis-none" ).first().removeClass("contract-dis-none");
    });
});

function formatDate() {
    var today = new Date();
    month = today.getMonth() + 1;
    day = today.getDate();
    if(month < 10){
        month = '0' + month;
    }
    if(day < 10){
        day = '0' + day;
    }
    var date = today.getFullYear()+'/'+ month +'/'+day;
    var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
    var dateTime = date+' '+time;
    return dateTime
}

function clearInputFile(f){
    if(f.value){
        try{
            f.value = ''; //for IE11, latest Chrome/Firefox/Opera...
        }catch(err){
        }
        if(f.value){ //for IE5 ~ IE10
            var form = document.createElement('form'), ref = f.nextSibling;
            form.appendChild(f);
            form.reset();
            ref.parentNode.insertBefore(f,ref);
        }
    }
}
