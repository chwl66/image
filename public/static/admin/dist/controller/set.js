/**
 * Hidove Ivey
 * wwww.hidove.cn
 * 2020年5月20日17:37:49
 */
;
layui.define(["form"],
    function (obj) {
        var form = layui.form,
            router = layui.router();


        let groupName = router.path;
        groupName.shift();
        groupName = groupName.join('.');
        let loadinMsg = layer.load(2);
        let field = {};
        $.ajax({
            url: ajaxPath + '/set/get?name=' + groupName,
            type: 'get',
            success: function (data) {
                if (data.code === 200) {
                    field = data.data;
                    form.val("Hidove-set", ergodic(data.data, ""));
                } else {
                    layer.msg(data.msg, {icon: 5});
                }
            },
            error: function () {
                layer.msg(data.msg, {icon: 5});
            },
        }).done(function () {
            layer.close(loadinMsg);
        });
        //监听提交
        form.on('submit(save_set)', function (data) {
            var update = data.field;

            for (let i in field) {
                if (!update.hasOwnProperty(field[i]['name'])) {
                    update[field[i]['name']] = 0;
                }
            }
            update['name'] = groupName;
            $.ajax({
                url: ajaxPath + '/set/update',
                type: 'post',
                data: update,
                success: function (data) {
                    if (data.code === 200) {
                        layer.msg('更新成功', {icon: 6});
                    } else {
                        layer.msg(data.msg, {icon: 5});
                    }
                },
                error: function () {
                    layer.msg(data.msg, {icon: 5});
                },
            });
            return false;
        });
        obj("set", {});
    });