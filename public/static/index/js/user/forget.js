$(document).keyup(function (event) {
    if (event.keyCode === 13) {
        $("#send").trigger("click");
    }
});
$('#send').click(function () {
    var data = {
        username: $('#username').val(),
        email: $('#email').val(),
    };
    spop({
        template: '正在提交，请稍等',
        autoclose: spopTimeOut,
        style: 'info'
    });
    $.ajax({
        url: '/ajax/auth/forget',
        type: 'post',
        data: data,
        success: function (data) {
            if (data.code === 200) {
                spop({
                    template: '发送验证码成功',
                    autoclose: spopTimeOut,
                    style: 'success'
                });
                setTimeout(function () {
                    window.location = '/user/login';
                }, 2000);
            } else {
                spop({
                    template: data.msg,
                    autoclose: spopTimeOut,
                    style: 'warning'
                });
            }
        },
        error: function () {
            spop({
                template: data.msg,
                autoclose: spopTimeOut,
                style: 'error'
            });
        },
    });
});