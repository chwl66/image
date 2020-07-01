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
        table.render({
            elem: '#storage-table'
            , height: 312
            , url: ajaxPath + '/storage/get' //数据接口
            , page: true //开启分页
            , cols: [[ //表头
                {type: 'checkbox'}
                , {field: 'id', title: 'ID', width: 80, sort: true}
                , {field: 'name', title: '策略标识', width: 100, edit: 'text'}
                , {field: 'cdn', title: 'CDN域名', width: 200, edit: 'text'}
                , {field: 'driver', title: '驱动', width: 200, edit: 'text'}
                , {
                    field: 'data', title: '配置信息', width: 160, templet: function (data) {
                        return json_format(JSON.stringify(data.data), false);
                    }, edit: 'text'
                }
                , {
                    field: 'create_time', title: '创建时间', width: 160, sort: true, templet: function (data) {
                        return date_chage(data.create_time);
                    }
                }
                , {
                    field: 'update_time', title: '更新时间', width: 160, sort: true, templet: function (data) {
                        return date_chage(data.update_time);
                    }
                }
                , {fixed: 'right', title: '操作', toolbar: '#tableTools', width: 180}
            ]]
            , response: {
                statusName: 'code' //规定数据状态的字段名称，默认：code
                , statusCode: 200 //规定成功的状态码，默认：0
                , msgName: 'msg' //规定状态信息的字段名称，默认：msg
                , countName: 'count' //规定数据总数的字段名称，默认：count
                , dataName: 'data' //规定数据列表的字段名称，默认：data
            },
        });
        table.on('edit(storage-table)', function (obj) { //注：edit是固定事件名，test是table原始容器的属性 lay-filter="对应的值"
            var data = {
                'id': obj.data.id
            };
            data[obj.field] = obj.value;
            data[obj.field] = obj.value;
            $.ajax({
                url: ajaxPath + '/storage/update',
                type: 'post',
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.code === 200) {
                        layer.msg('更新成功', {icon: 1});
                    } else {
                        table.reload('storage-table', {}); //只重载数据
                        layer.msg(data.msg, {icon: 2});
                    }
                },
                error: function () {
                    layer.msg('请求失败', {icon: 2});
                }
            });
        });

        //监听指定开关
        form.on('switch(Hidove-is_ok)', function (data) {
            var id = $(data.elem).data('id');
            var name = $(data.elem).attr('name');
            var status = this.checked ? 1 : 0;
            var postData = {};
            postData['id'] = id;
            postData[name] = status;
            $.ajax({
                url: ajaxPath + "/storage/update",
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

        //监听行工具事件
        table.on('tool(storage-table)', function (obj) {
            var data = obj.data;
            if (obj.event === 'delete') {
                layer.confirm('真的删除行么', function (index) {
                    $.ajax({
                        url: ajaxPath + '/storage/delete',
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
            } else if (obj.event === 'read') {
                layer.open({
                    type: 1,
                    skin: 'layui-layer-rim', //加上边框
                    area: ['420px', '240px'], //宽高
                    content: '<textarea class="layui-textarea" style="height: 100%">' + json_format(JSON.stringify(data.data)) + '</textarea>'
                });
            }else if (obj.event === 'edit') {
                layer.open({
                    title: '编辑'
                    , type: 0
                    // ,area: '500px'
                    , area: ['80%', '550px']
                    , shadeClose: true//是否点击遮罩关闭
                    , btn: ['取消'] //可以无限个按钮
                    , content: '<div class="layui-form">\n' +
                        '  <div class="layui-form-item">\n' +
                        '    <label class="layui-form-label">策略标识</label>\n' +
                        '    <div class="layui-input-block">\n' +
                        '      <input value="' + data.id + '" type="hidden" name="id" lay-verify="title" autocomplete="off" placeholder="" class="layui-input">\n' +
                        '      <input value=' + data.name + ' type="text" name="name" lay-verify="title" autocomplete="off" placeholder="请输入文件名" class="layui-input">\n' +
                        '       <div class="layui-form-mid layui-word-aux">例如：ftp,aftp,bftp,请不要与图床API的标识重复，如有重复，储存策略优先</div>' +
                        '    </div>\n' +
                        '  </div>\n' +
                        '  <div class="layui-form-item">\n' +
                        '    <label class="layui-form-label">CDN域名</label>\n' +
                        '    <div class="layui-input-block">\n' +
                        '      <input value="' + data.cdn + '" type="text" name="cdn" lay-verify="title" autocomplete="off" placeholder="请输入cdn域名" class="layui-input">' +
                        '       <div class="layui-form-mid layui-word-aux">https://pic.abcyun.co</div>' +
                        '    </div>\n' +
                        '  </div>\n' +
                        '  <div class="layui-form-item">\n' +
                        '    <label class="layui-form-label">驱动</label>\n' +
                        '    <div class="layui-input-block">\n' +
                        '      <input value="' + data.driver + '" type="text" name="driver" lay-verify="title" autocomplete="off" placeholder="请输入驱动标识" class="layui-input">' +
                        '       <div class="layui-form-mid layui-word-aux">驱动标识：例如：this,ftp</div>' + '' +
                        '    </div>\n' +
                        '  </div>\n' +
                        '  <div class="layui-form-item">\n' +
                        '    <label class="layui-form-label">配置信息</label>\n' +
                        '    <div class="layui-input-block">\n' +
                        '      <textarea name="data"  placeholder="请输入配置信息json数据" class="layui-textarea">'
                        + JSON.stringify(data.data)
                        + '</textarea>' +
                        '       <div class="layui-form-mid layui-word-aux">配置信息</div>' + '' +
                        '    </div>\n' +
                        '  </div>\n' +
                        '    <div class="layui-input-block">\n' +
                        '      <button type="submit" class="layui-btn" lay-submit="" id="submit" lay-filter="updateform">立即提交</button>\n' +
                        '    </div>\n' +
                        '</div>',
                    success: function (layero, index) {
                        $.ajax({
                            url: '/ajax/api/get',
                            type: 'get',
                            success: function (data) {
                                if (data.code === 200) {
                                    var html = '';
                                    data.data.forEach(function (value, index) {
                                        html = html + '<input type="checkbox" name="apiType[publicCloud][]"  value="' + value.key + '"  title="' + value.name + '">';
                                    });
                                    $('#Hidove-images-edit-form-api').append(html);
                                    form.render('checkbox'); //刷新select选择框渲染
                                }
                            }
                        });
                    }
                });
            }
        });
        //监听搜索
        form.on('submit(tableSearch)', function (data) {
            var field = data.field;
            var table = layui.table;
            table.reload('storage-table', {
                where: field
                , page: {
                    curr: 1 //重新从第 1 页开始
                }
            }); //只重载数据
            //执行重载
        });
        //监听编辑表单
        form.on('submit(updateform)', function (data) {
            layer.msg('正在更新中，请骚等~', {icon: 0}, function () {
                $.ajax({
                    url: ajaxPath + '/storage/update',
                    type: 'post',
                    data: data.field,
                    success: function (data) {
                        if (data.code === 200) {
                            layer.msg('修改成功', {icon: 6});
                            var table = layui.table;
                            table.reload('storage-table', {}); //只重载数据
                        } else {
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error: function () {
                        layer.msg(data.msg, {icon: 5});
                    },
                });
            });
        });
        //工具栏事件
        $('#deleteImages').click(function () {
            layer.confirm('确定删除吗？', {icon: 3, title: '提示'}, function (index) {
                var checkStatus = table.checkStatus('storage-table');
                var data = checkStatus.data;
                var idArr = [];
                data.forEach(function (value, index) {
                    idArr.push(value.id);
                });
                $.ajax({
                    url: ajaxPath + '/storage/delete',
                    type: 'post',
                    data: {'id': idArr},
                    dataType: 'json',
                    success: function (data) {
                        if (data.code === 200) {
                            layer.msg('删除成功', {icon: 6});
                            table.reload('storage-table', {}); //只重载数据
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
        $('#reloadTable').click(function () {
            table.reload('storage-table', {}); //只重载数据
        });
        $('#add').click(function () {
            $.ajax({
                url: ajaxPath + '/storage/create',
                type: 'get',
                dataType: 'json',
                success: function (data) {
                    if (data.code === 200) {
                        layer.msg('新增成功', {icon: 6});
                        table.reload('storage-table', {}); //只重载数据
                    } else {
                        layer.msg(data.msg, {icon: 5});
                    }
                },
                error: function () {
                    layer.msg(data.msg, {icon: 5});
                },
            });

        });
        obj("storage", {});
    });
