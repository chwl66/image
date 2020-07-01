
let resetKey = getUrlParam('resetKey');

if (resetKey === null){
    resetKey = getUrlParam('resetkey');
}
$('#resetKey').val(resetKey);
$(document).keyup(function(event){
    if(event.keyCode ===13){
        $("#send").trigger("click");
    }
});
$('#send').click(function () {
    var data = {
        resetKey:$('#resetKey').val(),
        password:$('#password').val(),
    };
    spop({
        template: '正在提交，请稍等',
        autoclose: spopTimeOut,
        style: 'info'
    });
    $.ajax({
        url:'/ajax/auth/resetPassword',
        type:'post',
        data:data,
        success:function(data){
            if (data.code === 200){
                spop({
                    template: '重置成功，请使用新密码登录',
                    autoclose: spopTimeOut,
                    style: 'success'
                });
                setTimeout(function () {
                    window.location = '/user/login';
                },2000);
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
                template: data.msg,
                autoclose: spopTimeOut,
                style: 'error'
            });
        },
    });
});