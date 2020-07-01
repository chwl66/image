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
            elem: "#blacklist-table",
            url: ajaxPath+"/blacklist/get",
            cols: [[
                {type: 'checkbox'}
                , {field: 'id', title: 'ID', width: 80, sort: true}
                , {field: 'username', title: '用户名', width: 150, sort: true, edit: 'text'}
                , {field: 'reason', title: '封锁原因', width: 200, edit: 'text'}
                , {field: 'ip', title: '上传IP', width: 150, sort: true, edit: 'text'}
                , {field: 'referer', title: '来源域名', width: 150, sort: true, edit: 'text'}
                , {
                    field: 'create_time', title: '创建时间', width: 160, sort: true, templet: function (data) {
                        return date_chage(data.create_time);
                    }
                }
                , {
                    field: 'release_time', title: '解封时间', width: 160, sort: true, templet: function (data) {
                        return date_chage(data.release_time);
                    }, edit: 'text'
                }
                , {
                    field: 'duration', title: '时长', width: 200, sort: true, templet: function (data) {
                        return formatSeconds(data.duration);
                    }, edit: 'text'
                }
                , {field: 'fraction', title: '评分', width: 150, sort: true, edit: 'text'}
                , {fixed: 'right', title: '操作', toolbar: '#tableTools', width: 130}
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
            table.on("tool(blacklist-table)",
                function (obj) {
                    var data = obj.data;
                    if (obj.event === 'delete') {
                        layer.confirm('真的删除行么', function (index) {
                            var blacklist = [];
                            blacklist.push(data.id);
                            $.ajax({
                                url: ajaxPath + '/blacklist/delete',
                                type: 'post',
                                data: {'id': blacklist},
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
                    } else if (obj.event === 'read') {
                        if (data.image === false) {
                            layer.msg('没有图片哦', {icon: 5});
                        } else {
                            layer.open({
                                type: 1,
                                area: '500px',
                                title: false, //不显示标题
                                shadeClose: true,
                                content: '<img style="max-width: 100%;max-height: 100%;" src="' + data.image + '" />'
                            });
                        }
                    }
                }),
            //监听单元格编辑
            table.on('edit(blacklist-table)', function (obj) { //注：edit是固定事件名，test是table原始容器的属性 lay-filter="对应的值"
                var data = {
                    'id': obj.data.id
                };
                data[obj.field] = obj.value;
                data[obj.field] = obj.value;
                $.ajax({
                    url: ajaxPath + '/blacklist/update',
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
        form.on('submit(Hidove-info-search)', function (data) {
            var field = data.field;
            var table = layui.table;
            table.reload('blacklist-table', {
                where: field
                , page: {
                    curr: 1 //重新从第 1 页开始
                }
            }); //只重载数据
            //执行重载
        });
        //工具栏事件
        $('#delete').click(function (data) {
            layer.confirm('确定删除吗？', {icon: 3, title: '提示'}, function (index) {
                var checkStatus = table.checkStatus('blacklist-table');
                var data = checkStatus.data;
                var blacklist = [];
                data.forEach(function (value, index) {
                    blacklist.push(value.id);
                });
                $.ajax({
                    url: ajaxPath + '/blacklist/delete',
                    type: 'post',
                    data: {'id': blacklist},
                    dataType: 'json',
                    success: function (data) {
                        if (data.code === 200) {
                            layer.msg('删除成功', {icon: 6});
                            table.reload('blacklist-table', {}); //只重载数据
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
            table.reload('blacklist-table', {}); //只重载数据
        });
        $('#add').click(function (data) {
            var blacklist = {
                'username': '',
                'ip': '0.0.0.0',
                'referer': 'www.china.com',
                'reason': '无',
                'image': '',
            };
            $.ajax({
                url: ajaxPath + '/blacklist/create',
                type: 'post',
                data: blacklist,
                dataType: 'json',
                success: function (data) {
                    if (data.code === 200) {
                        layer.msg('添加成功', {icon: 6});
                        table.reload('blacklist-table', {}); //只重载数据
                    } else {
                        layer.msg(data.msg, {icon: 5});
                    }
                },
                error: function () {
                    layer.msg(data.msg, {icon: 5});
                },
            });
        });
        obj("blacklist", {});
    });