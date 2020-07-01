/**
 * Hidove Ivey
 * wwww.hidove.cn
 * 2019年10月16日00:12:15
 */
;
layui.define(["table", "form"],
    function (obj) {
        var table = layui.table,
            form = layui.form;
        //重载图片信息表格
        table.render({
            elem: "#table",
            url: ajaxPath + '/rechargeCard/get',
            cols: [[
                {type: 'checkbox'}
                , {field: 'id', title: 'ID', width: 80, sort: true}
                , {field: 'key', title: '卡密', width: 250, edit: 'text'}
                , {
                    field: 'username', title: '用户名', width: 150, sort: true, templet: function (data) {
                        if (data.user !== null) {
                            return data.user.username;
                        }
                        return '未使用';
                    }
                }
                , {field: 'denomination', title: '面额', width: 150, sort: true, edit: 'text'}
                , {
                    field: 'create_time', title: '创建时间', width: 160, sort: true, templet: function (data) {
                        return date_chage(data.create_time);
                    }
                },
                {
                    field: 'used_time', title: '使用时间', width: 160, sort: true, templet: function (data) {
                        return date_chage(data.used_time);
                    }
                }
                , {fixed: 'right', title: '操作', toolbar: '#tableTools', width: 70}
            ]]
            , response: {
                statusName: 'code' //规定数据状态的字段名称，默认：code
                , statusCode: 200 //规定成功的状态码，默认：0
                , msgName: 'msg' //规定状态信息的字段名称，默认：msg
                , countName: 'count' //规定数据总数的字段名称，默认：count
                , dataName: 'data' //规定数据列表的字段名称，默认：data
            },
            page: !0,
            limit: 10,
            limits: [10, 15, 20, 25, 30],
            text: "对不起，加载出现异常！"
        }),
            //监听右侧工具栏
            table.on("tool(table)",
                function (obj) {
                    var data = obj.data;
                    if (obj.event === 'delete') {
                        layer.confirm('真的删除行么', function (index) {
                            var rechargecard = [];
                            rechargecard.push(data.id);
                            $.ajax({
                                url: ajaxPath + '/rechargeCard/delete',
                                type: 'post',
                                data: {'id': rechargecard},
                                dataType: 'json',
                                success: function (response) {
                                    if (response.code === 200) {
                                        layer.msg('删除成功', {icon: 1});
                                        obj.del();
                                    } else {
                                        layer.msg('删除失败', {icon: 2});
                                    }
                                },
                                error: function () {
                                    layer.msg('请求失败', {icon: 2});
                                }
                            });
                            layer.close(index);
                        });
                    }
                }),
            //监听单元格编辑
            table.on('edit(table)', function (obj) { //注：edit是固定事件名，test是table原始容器的属性 lay-filter="对应的值"
                var data = {
                    'id': obj.data.id
                };
                data[obj.field] = obj.value;
                data[obj.field] = obj.value;
                $.ajax({
                    url: ajaxPath + '/rechargeCard/update',
                    type: 'post',
                    data: data,
                    dataType: 'json',
                    success: function (data) {
                        if (data.code === 200) {
                            layer.msg('更新成功', {icon: 1});
                        } else {
                            layer.msg('更新失败', {icon: 2});
                        }
                    },
                    error: function () {
                        layer.msg('请求失败', {icon: 2});
                    }
                });
            });
        //监听搜索
        form.on('submit(recharge-card-search)', function (data) {
            var field = data.field;
            var table = layui.table;
            table.reload('table', {
                where: field
                , page: {
                    curr: 1 //重新从第 1 页开始
                }
            }); //只重载数据
            //执行重载
        });
        //监听生成表单
        form.on('submit(sendForm)', function (data) {

            layer.load(2);
            $.ajax({
                url: ajaxPath + '/rechargeCard/create',
                type: 'post',
                data: data.field,
                success: function (data) {
                    layer.closeAll('loading');
                    if (data.code === 200) {
                        layer.msg('生成成功', {icon: 6});
                        var table = layui.table;
                        table.reload('table', {}); //只重载数据
                        var html = '<textarea style="height: 100%" class="layui-textarea">';
                        var list = data.data;
                        list.forEach(function (value) {
                            html = html + value['key'] + '\n';
                        });
                        html = html + '</textarea>';
                        layer.open({
                            type: 1,
                            skin: 'layui-layer-rim', //加上边框
                            shadeClose: true, //开启遮罩关闭
                            area: ['420px', '240px'], //宽高
                            content: html
                        });
                    } else {
                        layer.msg(data.msg, {icon: 5});
                    }
                },
                error: function () {
                    layer.closeAll('loading');
                    layer.msg(data.msg, {icon: 5});
                },
            });
        });
        //工具栏事件
        $('#deleteRechargeCards').click(function (data) {
            layer.confirm('确定删除吗？', {icon: 3, title: '提示'}, function (index) {
                var checkStatus = table.checkStatus('table');
                var data = checkStatus.data;
                var rechargecard = [];
                data.forEach(function (value, index) {
                    rechargecard.push(value.id);
                });
                $.ajax({
                    url: ajaxPath + '/rechargeCard/delete',
                    type: 'post',
                    data: {'id': rechargecard},
                    dataType: 'json',
                    success: function (data) {
                        if (data.code === 200) {
                            layer.msg('删除成功', {icon: 6});
                            table.reload('table', {}); //只重载数据
                        } else {
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error: function () {
                        layer.msg(data.msg, {icon: 5});
                    },
                });
                layer.close(index);
            });
        });
        $('#reloadTable').click(function (data) {
            table.reload('table', {}); //只重载数据
        });
        $('#add').click(function (data) {
            layer.open({
                title: '编辑'
                , type: 0
                , area: ['80%', '550px']
                , shadeClose: true//是否点击遮罩关闭
                , btn: ['取消'] //可以无限个按钮
                , content: '<div class="layui-form">\n' +
                    '  <div class="layui-form-item">\n' +
                    '    <label class="layui-form-label">面额</label>\n' +
                    '    <div class="layui-input-block">\n' +
                    '      <input value="" type="text" name="denomination" lay-verify="title" autocomplete="off" placeholder="请输入面额" class="layui-input">\n' +
                    '       <div class="layui-form-mid layui-word-aux">单位：元 </div>' +
                    '    </div>\n' +
                    '  </div>\n' +
                    '  <div class="layui-form-item">\n' +
                    '    <label class="layui-form-label">生成个数</label>\n' +
                    '    <div class="layui-input-block">\n' +
                    '      <input value="10" type="text" name="number" lay-verify="title" autocomplete="off" placeholder="请输入生成个数" class="layui-input">\n' +
                    '    </div>\n' +
                    '  </div>\n' +
                    '    <div class="layui-input-block">\n' +
                    '      <button type="submit" class="layui-btn" lay-submit="" id="submit" lay-filter="sendForm">立即提交</button>\n' +
                    '    </div>\n' +
                    '</div>',
            });
        });

        $('#clearUsed').click(function (data) {
            layer.confirm('确定清理已使用卡密吗？', {icon: 3, title: '提示'}, function (index) {
                $.ajax({
                    url: ajaxPath + '/rechargeCard/delete',
                    type: 'post',
                    data: {'type': 'clearUsed'},
                    dataType: 'json',
                    success: function (data) {
                        if (data.code === 200) {
                            layer.msg('清理成功', {icon: 6});
                            table.reload('table', {}); //只重载数据
                        } else {
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error: function () {
                        layer.msg(data.msg, {icon: 5});
                    },
                });
                layer.close(index);
            });
        });
        $('#exportNotUsed').click(function (data) {
            layer.confirm('确定导出未使用卡密吗？', {icon: 3, title: '提示'}, function (index) {
                window.open(ajaxPath + '/rechargeCard/export?type=notUsed');
                layer.close(index);
            });
        });
        $('#exportUsed').click(function (data) {
            layer.confirm('确定导出已使用卡密吗？', {icon: 3, title: '提示'}, function (index) {
                window.open(ajaxPath + '/rechargeCard/export?type=used');
                layer.close(index);
            });
        });
        $('#exportAll').click(function (data) {
            layer.confirm('确定导出全部卡密吗？', {icon: 3, title: '提示'}, function (index) {
                window.open(ajaxPath + '/rechargeCard/export?type=all');
                layer.close(index);
            });
        });
        obj("rechargeCard", {});
    });