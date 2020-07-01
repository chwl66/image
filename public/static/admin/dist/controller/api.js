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
            elem: "#api-table",
            url: ajaxPath + "/api/get",
            cols: [[
                {type: 'checkbox'}
                , {field: 'id', title: 'ID', width: 60, sort: true, edit: 'text'}
                , {field: 'name', title: '标题', width: 150, edit: 'text'}
                , {field: 'key', title: '唯一标识', width: 160, edit: 'text'}
                , {
                    field: 'is_ok', title: '是否开启', width: 120, sort: true, templet: function (data) {
                        if (data.is_ok == 1) {
                            var html =
                                '<input data-id="' + data.id + '" type="checkbox" checked name="is_ok" lay-skin="switch" lay-filter="Hidove-api" lay-text="ON|OFF">';
                        } else {
                            var html =
                                '<input data-id="' + data.id + '" type="checkbox" name="is_ok" lay-skin="switch" lay-filter="Hidove-api" lay-text="ON|OFF">';
                        }
                        return html;
                    }
                }
                , {field: 'weight', title: '权重', width: 150, sort: true, edit: 'text'}
                , {
                    field: 'checked', title: '是否选中', width: 110, sort: true, templet: function (data) {
                        if (data.checked == 1) {
                            var html =
                                '<input data-id="' + data.id + '" type="checkbox" checked name="checked" lay-skin="switch" lay-filter="Hidove-api" lay-text="ON|OFF">';
                        } else {
                            var html =
                                '<input data-id="' + data.id + '" type="checkbox" name="checked" lay-skin="switch" lay-filter="Hidove-api" lay-text="ON|OFF">';
                        }
                        return html;
                    }
                }
                , {fixed: 'right', title: '操作', toolbar: '#api-toolbar', width: 70}
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
            table.on("tool(api-table)",
                function (obj) {
                    var data = obj.data;
                    if (obj.event === 'delete') {
                        layer.confirm('真的删除行么', function (index) {
                            $.ajax({
                                url: ajaxPath + "/api/delete",
                                type: 'post',
                                data: {'id': data.id},
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
            table.on('edit(api-table)', function (obj) { //注：edit是固定事件名，test是table原始容器的属性 lay-filter="对应的值"
                let data = {};
                if (obj.field === 'id') {
                    data = {
                        'id': obj.data.id,
                        'oldId': $(this).prev().text()
                    };
                } else {
                    data = {
                        'id': obj.data.id
                    };
                    data[obj.field] = obj.value;
                }
                $.ajax({
                    url: ajaxPath + "/api/update",
                    type: 'post',
                    data: data,
                    dataType: 'json',
                    success: function (data) {
                        if (data.code == 200) {
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

        //监听指定开关
        form.on('switch(Hidove-api)', function (data) {
            var id = $(data.elem).data('id');
            var name = $(data.elem).attr('name');
            var status = this.checked ? 1 : 0;
            var postData = {};
            postData['id'] = id;
            postData[name] = status;
            $.ajax({
                url: ajaxPath + "/api/update",
                type: 'post',
                data: postData,
                dataType: 'json',
                success: function (data) {
                    if (data.code == 200) {
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
        //监听添加按钮
        $('#Hidove-add').click(function () {
            $.ajax({
                url: ajaxPath + "/api/create",
                type: 'post',
                data: {
                    'name': 'Hidove图床',
                    'key': Date.now().toString(36),
                    'is_ok': 0,
                    'weight': 100,
                    'checked': 0,
                },
                dataType: 'json',
                success: function (response) {
                    if (response.code == 200) {
                        layer.msg('添加成功', {icon: 1});
                        table.reload('api-table');
                    } else {
                        layer.msg('添加失败', {icon: 2});
                    }
                },
                error: function () {
                    layer.msg('请求失败', {icon: 2});
                }
            });
        });
        $('#reloadTable').click(function (data) {
            table.reload('api-table', {}); //只重载数据
        });
        obj("api", {});
    });