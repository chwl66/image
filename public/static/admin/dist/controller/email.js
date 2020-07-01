/**
 * Hidove Ivey
 * wwww.hidove.cn
 * 2019年10月16日00:12:15
 */
;
layui.define(["table", "form"],
    function(obj) {
        var  form = layui.form;
        var loadinMsg = layer.load(2);
        $.ajax({
            url:'/api/admin/getSystemSet',
            type:'get',
            success:function(data){
                if (data.code == 200){
                    //给表单赋值
                    var basic = data.data.mail;

                    form.val("Hidove-system-set-mail", ergodic(basic,""));
                }else {
                    layer.msg(data.msg, {icon: 5});
                }
            },
            error:function(){
                layer.msg(data.msg, {icon: 5});
            },
        }).done(function () {
            layer.close(loadinMsg);
        });
        //监听提交
        form.on('submit(set_system_email)', function(data){
            $.ajax({
                url:'/api/admin/updateSystemSet',
                type:'post',
                data:{'mail':data.field},
                success:function(data){
                    if (data.code == 200){
                        layer.msg('更新成功', {icon: 6});
                    }else {
                        layer.msg(data.msg, {icon: 5});
                    }
                },
                error:function(){
                    layer.msg(data.msg, {icon: 5});
                },
            });
            return false;
        });
        form.on('submit(sendMailtest)', function(data){
            var send = {
                'address':data.field.replyMail,
            };
            $.ajax({
                url:'/api/admin/sendMail',
                type:'post',
                data:send,
                success:function(data){
                    if (data.code == 200){
                        layer.msg('发送成功', {icon: 6});
                    }else {
                        layer.msg(data.msg, {icon: 5});
                    }
                },
                error:function(){
                    layer.msg(data.msg, {icon: 5});
                },
            });
            return false;
        });
        obj("email", {});
    });