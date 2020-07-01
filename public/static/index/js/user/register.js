$(document).keyup(function(event){
    if(event.keyCode ==13){
        $("#register").trigger("click");
    }
});
$('#register').click(function () {
    var data = {
        username:$('#username').val(),
        password:$('#password').val(),
        confirm_password:$('#confirm_password').val(),
        email:$('#email').val(),
        captcha:$('#captcha').val(),
    };
    spop({
        template: '正在提交，请稍等',
        autoclose: spopTimeOut,
        style: 'info'
    });
    $.ajax({
        url:'/ajax/auth/register',
        type:'post',
        data:data,
        success:function(data){
            if (data.code == 200){
                spop({
                    template: '注册成功',
                    autoclose: spopTimeOut,
                    style: 'success'
                });
                window.location = '/user/login';
            }else {
                $('#captcha-img').attr('src', refreshCaptcha($('#captcha-img').data('src')));
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

$('#captcha-img').click(function () {
    $('#captcha-img').attr('src', refreshCaptcha($('#captcha-img').data('src')));
});
