/**
 * Hidove Ivey
 * wwww.hidove.cn
 * 2019年10月16日00:12:15
 */
layui.define(["table", "form"],
    function (obj) {
        var table = layui.table,
            form = layui.form;

        table.render({
            elem: '#group-table'
            , height: 312
            , url: ajaxPath + '/group/get' //数据接口
            , page: true //开启分页
            , cols: [[ //表头
                {type: 'checkbox'}
                , {
                    field: 'id',
                    title: '<span lay-tips="管理组的ID必须为2，游客组ID为0">ID</span>',
                    width: 80,
                    sort: true,
                    edit: 'text'
                }
                , {field: 'name', title: '名称', width: 150, edit: 'text'}
                , {
                    field: 'capacity',
                    title: '<span lay-tips="单位为字节">容量</span>',
                    width: 150,
                    edit: 'text',
                    templet: function (data) {
                        return change_filesize(data.capacity);
                    },
                }
                , {
                    field: 'storage', title: '储存策略', width: 150, templet: function (data) {
                        var html = `<select name="` + data.id + `" lay-filter="storageChange"><option value="1">请选择分组</option>`;
                        for (var i in data.storageList) {
                            html = html + `<option value="`
                                + data.storageList[i] + '"' + (data.storage === data.storageList[i] ? 'selected' : '')
                                + `>`
                                + data.storageList[i]
                                + `</option>`;
                        }
                        html = html + '</select>;';
                        return html;
                    }, unresize: true
                }
                , {field: 'price', title: '价格', width: 150, edit: 'text'}
                , {
                    field: 'frequency',
                    title: '<span lay-tips="最近一小时内可上传的最大图片个数,0代表禁止-1为不限制">上传频率</span>',
                    width: 100,
                    edit: 'text'
                }
                , {
                    field: 'picture_process',
                    title: '<span lay-tips="图片不压缩，不加水印">图片处理</span>',
                    width: 120,
                    sort: true,
                    templet: function (data) {
                        var html = '';
                        if (data.picture_process === 1) {
                            html =
                                '<input data-id="' + data.id + '" type="checkbox" checked name="picture_process" lay-skin="switch" lay-filter="Hidove-picture_process" lay-text="ON|OFF">';
                        } else {
                            html =
                                '<input data-id="' + data.id + '" type="checkbox" name="picture_process" lay-skin="switch" lay-filter="Hidove-picture_process" lay-text="ON|OFF">';
                        }
                        return html;
                    }
                }
                , {fixed: 'right', title: '操作', toolbar: '#tableTools', width: 100}
            ]]
            , response: {
                statusName: 'code' //规定数据状态的字段名称，默认：code
                , statusCode: 200 //规定成功的状态码，默认：0
                , msgName: 'msg' //规定状态信息的字段名称，默认：msg
                , countName: 'count' //规定数据总数的字段名称，默认：count
                , dataName: 'data' //规定数据列表的字段名称，默认：data
            },
        });
        //监听指定开关
        form.on('switch(Hidove-picture_process)', function (data) {
            var id = $(data.elem).data('id');
            var name = $(data.elem).attr('name');
            var status = this.checked ? 1 : 0;
            var postData = {};
            postData['id'] = id;
            postData[name] = status;
            $.ajax({
                url: ajaxPath + '/group/update',
                type: 'post',
                data: postData,
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
        table.on('edit(group-table)', function (obj) { //注：edit是固定事件名，test是table原始容器的属性 lay-filter="对应的值"
            var data = {};
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
                url: ajaxPath + '/group/update',
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
        //监听行工具事件
        table.on('tool(group-table)', function (obj) {
            var data = obj.data;
            if (obj.event === 'delete') {
                layer.confirm('真的删除行么', function (index) {
                    var id = [];
                    id.push(data.id);
                    $.ajax({
                        url: ajaxPath + '/group/delete',
                        type: 'post',
                        data: {'id': id},
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
        });
        //监听添加按钮
        $('#add').click(function () {
            $.ajax({
                url: ajaxPath + '/group/create',
                type: 'get',
                success: function (response) {
                    if (response.code === 200) {
                        layer.msg('添加成功', {icon: 1});
                        table.reload('group-table');
                    } else {
                        layer.msg('添加失败', {icon: 2});
                    }
                },
                error: function () {
                    layer.msg('请求失败', {icon: 2});
                }
            });
        });
        $('#delete').click(function (data) {
            layer.confirm('确定删除吗？', {icon: 3, title: '提示'}, function (index) {
                var checkStatus = table.checkStatus('group-table');
                var data = checkStatus.data;
                var id = [];
                data.forEach(function (value, index) {
                    id.push(value.id);
                });
                $.ajax({
                    url: ajaxPath + '/group/delete',
                    type: 'post',
                    data: {'id': id},
                    dataType: 'json',
                    success: function (data) {
                        if (data.code === 200) {
                            layer.msg('删除成功', {icon: 6});
                            table.reload('group-table', {}); //只重载数据
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

        //  监听权限分组
        form.on('select(storageChange)', function (data) {
            $.ajax({
                url: ajaxPath + '/group/update',
                type: 'post',
                data: {'id': data.elem.name, 'storage': data.value},
                success: function (data) {
                    if (data.code === 200) {
                        layer.msg('更新成功', {icon: 6});
                    } else {
                        layer.msg(data.msg, {icon: 5});
                        var table = layui.table;
                        table.reload('group-table', {}); //只重载数据
                    }
                },
                error: function () {
                    layer.msg(data.msg, {icon: 5});
                    var table = layui.table;
                    table.reload('group-table', {}); //只重载数据
                },
            });
        });
        $('#reloadTable').click(function (data) {
            table.reload('group-table', {}); //只重载数据
        });
        obj("group", {});
    });
