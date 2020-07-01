$('#residualTime').html(function () {
    const expirationdate = $(this).data('timestamp');
    //获取过期剩余时间
    const time = Math.round(new Date().getTime() / 1000).toString();
    if (expirationdate < time) {
        return '剩余天数：<span class="text-danger">已过期</span>';
    } else {
        return '剩余天数：<span>' + formatSeconds(expirationdate - time) + '</span>';
    }
});
$.ajax({
    url: '/ajax/userFinance/getGroupList',
    success: function (data) {
        if (data.code === 200) {
            var item = data.data;
            var html = '';
            for (var index in item) {
                html = html + '<option data-info="' + $.base64.encode(JSON.stringify(item[index])) + '" value="' + item[index]['id'] + '">' + item[index]['name'] + '</option>'
            }
            $('#groupList').append(html)
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
$('#groupList').change(function () {
    if ($(this).val() === '0') {
        $('#group').html('<h5 class="text-center pt-5">请选择一个用户组</h5>');
        return;
    }
    var item = $(this).children('option:selected')[0];
    var info = JSON.parse($.base64.decode($(item).data('info')));
    var html =
        '<li class="list-group-item">Title：' + info['name'] + '</li>\n' +
        '<li class="list-group-item">Capacity：' + change_filesize(info['capacity']) + '</li>\n' +
        '<li class="list-group-item">Storage：' + info['storage'] + '</li>\n' +
        '<li class="list-group-item">Price：' + info['price'] + ' ￥</li>\n' +
        '<li class="list-group-item">Duration：365 days</li>';
    $('#group').html(html);
});
$('#send').click(function () {
    var key = $('#key').val();
    spop({
        template: '正在提交，请稍等',
        autoclose: spopTimeOut,
        style: 'info'
    });
    $.ajax({
        url: '/ajax/userFinance/recharge',
        type: 'post',
        data: {'card': key},
        success: function (data) {
            if (data.code === 200) {
                spop({
                    template: '充值成功',
                    autoclose: spopTimeOut,
                    style: 'success'
                });
                setTimeout(function () {
                    window.location = "";
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
                template: '请求失败',
                autoclose: spopTimeOut,
                style: 'error'
            });
        },
    });
});
$('#update').click(function () {
    var groupId = $('#groupList').val();
    spop({
        template: '正在提交，请稍等',
        autoclose: spopTimeOut,
        style: 'info'
    });
    $.ajax({
        url: '/ajax/userFinance/updateGroup',
        type: 'post',
        data: {'id': groupId},
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