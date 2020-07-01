$(document).keyup(function (event) {
    if (event.keyCode === 13) {
        $("#send").trigger("click");
    }
});
$('#send').click(function () {
    var data = $('#refreshUrl').val();
    spop({
        template: '正在提交，请稍等',
        autoclose: spopTimeOut,
        style: 'info'
    });
    if (data.length === 0) {
        spop({
            template: '刷新地址不得为空',
            autoclose: spopTimeOut,
            style: 'error'
        });
        return;
    }
    var Arr = data.split(/[(\r\n)\r\n]+/);
    $.ajax({
        url: '/ajax/userCache/refresh',
        type: 'post',
        data: {'refreshUrl': Arr},
        success: function (data) {
            if (data.code === 200) {
                spop({
                    template: '刷新成功',
                    autoclose: spopTimeOut,
                    style: 'success'
                });
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
                template: '请求失败',
                autoclose: spopTimeOut,
                style: 'error'
            });
        },
    });
});
$('#refreshAll').click(function () {
    spop({
        template: '正在提交，请稍等',
        autoclose: spopTimeOut,
        style: 'info'
    });
    $.ajax({
        url: '/ajax/userCache/refreshAll',
        type: 'get',
        success: function (data) {
            if (data.code === 200) {
                spop({
                    template: '刷新全部缓存成功',
                    autoclose: spopTimeOut,
                    style: 'success'
                });
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
                template: '请求失败',
                autoclose: spopTimeOut,
                style: 'error'
            });
        },
    });
});
$('#refreshConfig').click(function () {
    spop({
        template: '正在提交，请稍等',
        autoclose: spopTimeOut,
        style: 'info'
    });
    $.ajax({
        url: '/ajax/userCache/refreshConfig',
        type: 'get',
        success: function (data) {
            if (data.code === 200) {
                spop({
                    template: '刷新配置缓存成功',
                    autoclose: spopTimeOut,
                    style: 'success'
                });
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
                template: '请求失败',
                autoclose: spopTimeOut,
                style: 'error'
            });
        },
    });
});
$('#refreshImageDistribution').click(function () {
    spop({
        template: '正在提交，请稍等',
        autoclose: spopTimeOut,
        style: 'info'
    });
    $.ajax({
        url: '/ajax/userCache/imageIsValid',
        type: 'get',
        success: function (data) {
            if (data.code === 200) {
                spop({
                    template: '刷新图片标识缓存成功',
                    autoclose: spopTimeOut,
                    style: 'success'
                });
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
                template: '请求失败',
                autoclose: spopTimeOut,
                style: 'error'
            });
        },
    });
});