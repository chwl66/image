$(document).keyup(function(event){
    if(event.keyCode ===13){
        $("#send").trigger("click");
    }
});
$('#send').click(function () {
    var apiType =[];
    $("input[name='apiType']:checked").each(function() {
        apiType.push($(this).val());
    });
    apiType.push('');

    spop({
        template: '正在提交，请稍等',
        autoclose: spopTimeOut,
        style: 'info'
    });
    $.ajax({
        url:'/ajax/userNode/update',
        type:'post',
        data:{'forbiddenNode':apiType},
        success:function(data){
            if (data.code == 200){
                spop({
                    template: '更新成功',
                    autoclose: spopTimeOut,
                    style: 'success'
                });
            }else {
                spop({
                    template: data.msg,
                    autoclose: spopTimeOut,
                    style: 'warning'
                });
            }
        },
        error:function(){
            spop({
                template: '请求失败',
                autoclose: spopTimeOut,
                style: 'error'
            });
        },
    });
});