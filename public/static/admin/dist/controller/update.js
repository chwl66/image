/**
 * Hidove Ivey
 * wwww.hidove.cn
 * 2019年10月16日00:12:15
 */
;
layui.define([],
    function (obj) {
        $().ready(function () {
            $('#check').click();
        });
        //监听按钮
        $('#check').click(function (data) {
            layer.msg('正在检查更新', {icon: 0});
            $.ajax({
                url: authPath + '/update/check',
                type: 'get',
                success: function (data) {
                    if (data.code === 200) {
                        //赋值
                        $('#currentVersion').text(data.data.version);
                        $('#latestVersion').text(data.data.lastestVersion);
                        $('#updateLog').html(data.data.content);
                        layer.msg('获取数据成功', {icon: 1});
                        if (data.data.version !== data.data.lastestVersion) {
                            layer.open({
                                type: 1
                                ,
                                title: false //不显示标题栏
                                ,
                                closeBtn: false
                                ,
                                area: '300px;'
                                ,
                                shade: 0.8
                                ,
                                id: 'LAY_layuipro' //设定一个id，防止重复弹出
                                ,
                                btn: ['火速围观', '残忍拒绝']
                                ,
                                btnAlign: 'c'
                                ,
                                moveType: 1 //拖拽模式，0或者1
                                ,
                                content: '<div style="padding: 50px; line-height: 22px; background-color: #393D49; color: #fff; ttf-weight: 300;"><pre>'
                                    + data.data.content +
                                    '</pre></div>'
                                ,
                                success: function (layero) {
                                    var btn = layero.find('.layui-layer-btn');
                                    btn.find('.layui-layer-btn0').attr({
                                        href: authPath + '/update'
                                        , target: '_blank'
                                    });
                                }
                            });
                        }
                    } else {
                        layer.msg(data.msg, {icon: 5});
                    }
                },
                error: function () {
                    layer.msg(data.msg, {icon: 5});
                },
            });
        });
        $('#update').click(function (data) {
            // window.open('https://blog.hidove.cn/post/479');
            // return;
            window.open(authPath + '/update');
        });
        obj("update", {});
    });