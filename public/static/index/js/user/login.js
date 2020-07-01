$(document).keyup(function(event){
    if(event.keyCode ==13){
        $("#login").trigger("click");
    }
});
$('#login').click(function () {
    var data = {
        username:$('#username').val(),
        password:$('#password').val(),
    };
    spop({
        template: '正在提交，请稍等',
        autoclose: spopTimeOut,
        style: 'info'
    });
    $.ajax({
        url:'/ajax/auth/login',
        type:'post',
        data:data,
        success:function(data){
            if (data.code == 200){
                spop({
                    template: '登录成功',
                    autoclose: spopTimeOut,
                    style: 'success'
                });
                window.location = '/user';
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