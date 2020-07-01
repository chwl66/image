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
            elem: '#HidoveTable'
            , height: 312
            , url: ajaxPath + '/user/get' //数据接口
            , page: true //开启分页
            , cols: [[ //表头
                {type: 'checkbox'}
                , {field: 'id', title: 'ID', width: 80, sort: true}
                , {field: 'username', title: '用户名', width: 100}
                , {
                    field: 'group', title: '权限组', width: 150, templet: function (data) {
                        var html = `<select name="` + data.id + `" lay-filter="groupChange"><option value="1">请选择分组</option>`;
                        for (var i in data.groupList) {
                            html = html + `<option value="`
                                + data.groupList[i].id + '"' + (data.group_id === data.groupList[i].id ? 'selected' : '')
                                + `>`
                                + data.groupList[i].name
                                + `</option>`;
                        }
                        html = html + '</select>;';
                        return html;
                    }, unresize: true
                }
                , {field: 'email', title: '邮箱', width: 160, edit: 'text'}
                , {
                    field: 'is_whitelist',
                    title: '<span lay-tips="鉴黄不拦截，上传频率不拦截">白名单用户</span>',
                    width: 120,
                    sort: true,
                    templet: function (data) {
                        let html = '';
                        if (data.is_whitelist === 1) {
                            html =
                                '<input data-id="' + data.id + '" type="checkbox" checked name="is_whitelist" lay-skin="switch" lay-filter="Hidove-whitelist" lay-text="ON|OFF">';
                        } else {
                            html =
                                '<input data-id="' + data.id + '" type="checkbox" name="is_whitelist" lay-skin="switch" lay-filter="Hidove-whitelist" lay-text="ON|OFF">';
                        }
                        return html;
                    }
                }
                , {field: 'token', title: 'Token', width: 260, sort: true}
                , {
                    field: 'create_time', title: '创建时间', width: 160, sort: true, templet: function (data) {
                        return date_chage(data.create_time);
                    }
                }
                , {
                    field: 'capacity_used',
                    title: '<span lay-tips="已使用容量，单位字节">已用容量</span>',
                    width: 100,
                    edit: 'text',
                    templet: function (data) {
                        return change_filesize(data.capacity_used);
                    },
                }
                , {field: 'images_count', title: '图片总数', width: 100, sort: true}
                , {field: 'finance', title: '余额', edit: 'text', width: 100, sort: true}
                , {
                    field: 'expiration_date',
                    title: '过期时间',
                    edit: 'text',
                    width: 160,
                    sort: true,
                    templet: function (data) {
                        return date_chage(data.expiration_date);
                    }
                }
                , {fixed: 'right', title: '操作', toolbar: '#tableTools', width: 220}
            ]]
            , response: {
                statusName: 'code' //规定数据状态的字段名称，默认：code
                , statusCode: 200 //规定成功的状态码，默认：0
                , msgName: 'msg' //规定状态信息的字段名称，默认：msg
                , countName: 'count' //规定数据总数的字段名称，默认：count
                , dataName: 'data' //规定数据列表的字段名称，默认：data
            },
        });
        table.on('edit(HidoveTable)', function (obj) { //注：edit是固定事件名，test是table原始容器的属性 lay-filter="对应的值"
            var data = {
                'id': obj.data.id
            };
            data[obj.field] = obj.value;
            data[obj.field] = obj.value;
            $.ajax({
                url: ajaxPath + '/user/update',
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

        //监听指定开关
        form.on('switch(Hidove-whitelist)', function (data) {
            var id = $(data.elem).data('id');
            var name = $(data.elem).attr('name');
            var status = this.checked ? 1 : 0;
            data = {};
            data['id'] = id;
            data[name] = status;
            $.ajax({
                url: ajaxPath + '/user/update',
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
        //监听行工具事件
        table.on('tool(HidoveTable)', function (obj) {
            var data = obj.data;
            if (obj.event === 'delete') {
                layer.confirm('真的删除该用户么，图片也将被清空哦！', function (index) {
                    $.ajax({
                        url: ajaxPath + '/user/delete',
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
            } else if (obj.event === 'resetToken') {
                layer.confirm('Token真的要重置啦？', function (index) {
                    $.ajax({
                        url: ajaxPath + '/user/update',
                        type: 'post',
                        data: {
                            'id': data.id,
                            'token': true
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.code === 200) {
                                layer.msg('Token重置成功', {icon: 1});
                                //同步更新缓存对应的值
                                obj.update({
                                    token: response.data.token
                                });
                            } else {
                                layer.msg(response.msg, {icon: 2});
                            }
                        },
                        error: function () {
                            layer.msg('请求失败', {icon: 2});
                        }
                    });
                    layer.close(index);
                });
            } else if (obj.event === 'resetPassword') {
                layer.prompt({
                    formType: 0
                    , value: 'Hidove'
                }, function (value, index) {
                    $.ajax({
                        url: ajaxPath + '/user/update',
                        type: 'post',
                        data: {
                            'id': data.id,
                            'password': value
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.code === 200) {
                                layer.msg('密码重置成功', {icon: 1});
                            } else {
                                layer.msg(response.msg, {icon: 2});
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

        //  监听权限分组
        form.on('select(groupChange)', function (data) {
            $.ajax({
                url: ajaxPath + '/user/update',
                type: 'post',
                data: {'id': data.elem.name, 'group_id': data.value},
                success: function (data) {
                    if (data.code === 200) {
                        layer.msg('更新成功', {icon: 6});
                    } else {
                        layer.msg(data.msg, {icon: 5});
                        var table = layui.table;
                        table.reload('HidoveTable', {}); //只重载数据
                    }
                },
                error: function () {
                    layer.msg(data.msg, {icon: 5});
                    var table = layui.table;
                    table.reload('HidoveTable', {}); //只重载数据
                },
            });
        });
        //监听搜索
        form.on('submit(tableSearch)', function (data) {
            var field = data.field;
            var table = layui.table;
            table.reload('HidoveTable', {
                where: field
                , page: {
                    curr: 1 //重新从第 1 页开始
                }
            }); //只重载数据
            //执行重载
        });
        $('#deleteSelect').click(function () {
            //获取选中行
            var checkStatus = table.checkStatus('HidoveTable'); //idTest 即为基础参数 id 对应的值
            var data = checkStatus.data;//获取选中行的数据
            var ids = 0;
            data.forEach(function (value, index) {
                ids++;
                $.ajax({
                    url: ajaxPath + '/user/delete',
                    type: 'post',
                    data: {'id': value.id},
                    dataType: 'json',
                    success: function (response) {
                        if (response.code === 200) {
                            table.reload('HidoveTable');
                        } else {
                            layer.msg(value.id + ' 删除失败', {icon: 2});
                        }
                    },
                    error: function () {
                        layer.msg('请求失败', {icon: 2});
                    }
                });
            });
            layer.msg('共删除 ' + ids + '个', {icon: 1});
        });

        $('#reloadTable').click(function (data) {
            table.reload('HidoveTable', {}); //只重载数据
        });
        obj("user", {});
    });

//加载搜索框中的GroupList
loadGroupSelect();

//加载搜索框中的GroupList
function loadGroupSelect() {
    $.ajax({
        url: ajaxPath + '/group/get?type=all',
        type: 'get',
        success: function (data) {
            if (data.code === 200) {
                var html = '';
                data.data.forEach(function (value, index) {
                    html = html + '<option value="' + value.id + '">' + value.name + '</option>';
                });
                $('#groupIdSearch').append(html);
                layui.form.render('select');
            } else {
                layer.msg(data.msg, {icon: 5});
            }
        },
        error: function () {
            layer.msg(data.msg, {icon: 5});
        },
    });
}