/**
 * Hidove Ivey
 * wwww.hidove.cn
 * 2019年10月16日00:12:15
 */
;
layui.define(['form'],
    function (obj) {
        //监听按钮
        var form = layui.form;
        form.on('submit(execute)', function(data){
            layer.load(1);
            $.ajax({
                url: ajaxPath + '/sql',
                type: 'post',
                data: data.field,
                success: function (data) {
                    layer.closeAll();
                    if (data.code === 200) {
                        layer.msg(data.msg, {icon: 1});
                    } else {
                        layer.msg(data.msg, {icon: 5});
                    }
                },
                error: function () {
                    layer.closeAll();
                    layer.msg(data.msg, {icon: 5});
                },
            });
            return false;
        });
        obj("sql", {});
    });