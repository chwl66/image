$.ajax({
    url: '/ajax/userStorage/get',
    success: function (data) {
        if (data.code === 200) {
            var res = data.data;
            for (var i in res) {
                for (var index in res[i]) {
                    $('#' + i).find('input[name=' + index + ']').val(res[i][index]);
                }
            }
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
$(document).keyup(function (event) {
    if (event.keyCode === 13) {
        $("#send").trigger("click");
    }
});
$('#send').click(function () {
    var postData = {};
    $('#content > div').each(function () {
        var id = $(this).attr('id');
        var tmp = {};
        $(this).find('input').each(function () {
            var key = $(this).attr('name');
            var value = $(this).val()
            tmp[key] = value;
        });
        postData[id] = tmp;
    });
    spop({
        template: '正在提交，请稍等',
        autoclose: spopTimeOut,
        style: 'info'
    });
    $.ajax({
        url: '/ajax/userStorage/update',
        type: 'post',
        data: postData,
        success: function (data) {
            if (data.code === 200) {
                spop({
                    template: '更新成功',
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