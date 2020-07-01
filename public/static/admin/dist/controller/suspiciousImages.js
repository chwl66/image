/**
 * Hidove Ivey
 * wwww.hidove.cn
 * 2020年5月30日09:53:21
 */
var layer = null;
layui.use('layer', function () {
    layer = layui.layer;
});

function preview(obj) {
    var image = $(obj).attr('src')
    layer.open({
        type: 1,
        area: '500px',
        title: false, //不显示标题
        shadeClose: true,
        content: '<img style="max-width: 100%;max-height: 100%;" src="' + image + '" />'
    });
}
layui.define(["table", "form"],
    function (obj) {
        var admin = layui.admin,
            view = layui.view,
            table = layui.table,
            form = layui.form;
        //重载图片信息表格
        table.render({
            elem: "#suspicious-images-table",
            url: ajaxPath + "/images/get?type=suspicious",
            cols: [[
                {type: 'checkbox', fixed: 'left'}
                , {field: 'id', title: 'ID', width: 80, sort: true}
                , {
                    field: 'preview', title: '预览图', width: 160, templet: function (data) {
                        return '<img onclick="preview(this)" class="preview" src="/image/' + data.signatures + '" alt="' + data.filename + '">';
                    }
                }
                , {field: 'signatures', title: '唯一标识', width: 150}
                , {
                    field: 'url', title: 'URL', width: 160, templet: function (data) {
                        return json_format(JSON.stringify(data.url), false);
                    }
                }
                , {
                    field: 'username', title: '用户名', width: 100, sort: true, templet: function (data) {
                        return data.user.username;
                    }
                }
                , {field: 'filename', title: '文件名', width: 200, sort: true, edit: 'text'}
                , {field: 'fraction', title: '鉴定分数', width: 110, sort: true, edit: 'text'}
                , {field: 'image_type', title: '文件类型', width: 100, sort: true}
                , {field: 'mime', title: 'MIME', width: 150, sort: true}
                , {
                    field: 'file_size', title: '大小', width: 100, templet: function (data) {
                        return change_filesize(data.file_size);
                    }
                }
                , {field: 'ip', title: '上传IP', width: 150, sort: true}
                , {
                    field: 'create_time', title: '创建时间', width: 160, sort: true, templet: function (data) {
                        return date_chage(data.create_time);
                    }
                }
                , {field: 'today_request_times', title: '今日请求次数', width: 150, sort: true}, {
                    field: 'total_request_times',
                    title: '总共请求次数',
                    width: 150,
                    sort: true
                }
                , {
                    field: 'final_request_time', title: '最后请求时间', width: 160, sort: true, templet: function (data) {
                        return date_chage(data.final_request_time);
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
            page: !0,
            limit: 10,
            limits: [10, 15, 20, 25, 30],
            text: "对不起，加载出现异常！"
        }),
            //监听右侧工具栏
            table.on("tool(suspicious-images-table)",
                function (obj) {
                    var data = obj.data;
                    if (obj.event === 'delete') {
                        layer.confirm('真的删除行么', function (index) {
                            var images = [];
                            images.push({
                                'id': data.id,
                                'userId': data.user_id,
                            });
                            $.ajax({
                                url: ajaxPath + '/images/delete',
                                type: 'post',
                                data: {'images': images},
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
                        window.open('/info/' + obj.data.signatures);
                    } else if (obj.event === 'edit') {
                        layer.open({
                            title: '编辑'
                            , type: 0
                            // ,area: '500px'
                            , area: ['80%', '550px']
                            , shadeClose: true//是否点击遮罩关闭
                            , btn: ['取消'] //可以无限个按钮
                            // ,content:'123',
                            , content: '<div class="layui-form">\n' +
                                '  <div class="layui-form-item">\n' +
                                '    <label class="layui-form-label">文件名</label>\n' +
                                '    <div class="layui-input-block">\n' +
                                '      <input value=' + data.id + ' type="hidden" name="id" lay-verify="title" autocomplete="off" placeholder="" class="layui-input">\n' +
                                '      <input value=' + data.filename + ' type="text" name="filename" lay-verify="title" autocomplete="off" placeholder="请输入文件名" class="layui-input">\n' +
                                '       <div class="layui-form-mid layui-word-aux">鞠婧祎.png</div>' +
                                '    </div>\n' +
                                '  </div>\n' +
                                '  <div class="layui-form-item">\n' +
                                '    <label class="layui-form-label">外链地址</label>\n' +
                                '    <div class="layui-input-block">\n' +
                                '<textarea name="url"  placeholder="请输入外链地址json数据" class="layui-textarea">' + JSON.stringify(data.url) + '</textarea>' +
                                '       <div class="layui-form-mid layui-word-aux">{"this":"https:\/\/www.1.com\/1.png"}</div>' +
                                '    </div>\n' +
                                '  </div>\n' +
                                '  <div class="layui-form-item">\n' +
                                '    <label class="layui-form-label">违规分数</label>\n' +
                                '    <div class="layui-input-block">\n' +
                                '      <input value=' + data.fraction + ' type="text" name="fraction" lay-verify="title" autocomplete="off" placeholder="请输入违规分数" class="layui-input">' +
                                '       <div class="layui-form-mid layui-word-aux">60分 什么的</div>' + '' +
                                '    </div>\n' +
                                '  </div>\n' +
                                ' <div class="layui-form-item">\n' +
                                '    <label class="layui-form-label">更新外链地址</label>\n' +
                                '    <div class="layui-input-block" id="Hidove-images-edit-form-api">\n' +
                                '    </div>\n' +
                                '  </div>' +
                                '    <div class="layui-input-block">\n' +
                                '      <button type="submit" class="layui-btn" lay-submit="" id="submit" lay-filter="updateform">立即提交</button>\n' +
                                '    </div>\n' +
                                '</div>',
                            success: function (layero, index) {
                                $.ajax({
                                    url: '/ajax/api/get',
                                    type: 'get',
                                    success: function (data) {
                                        if (data.code == 200) {
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
        //监听单元格编辑
        table.on('edit(suspicious-images-table)', function (obj) {

            var data = {
                'id': obj.data.id
            };
            data[obj.field] = obj.value;
            data[obj.field] = obj.value;
            $.ajax({
                url: ajaxPath + '/images/update',
                type: 'post',
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.code === 200) {
                        layer.msg('更新成功', {icon: 1});
                        table.reload('suspicious-images-table', {}); //只重载数据
                    } else {
                        layer.msg('更新失败', {icon: 2});
                    }
                },
                error: function () {
                    layer.msg('请求失败', {icon: 2});
                }
            });
        });
        //监听编辑表单
        form.on('submit(updateform)', function (data) {
            layer.load(2);
            $.ajax({
                url: ajaxPath + '/images/update',
                type: 'post',
                data: data.field,
                success: function (data) {
                    layer.closeAll('loading');
                    if (data.code === 200) {
                        layer.msg('修改成功', {icon: 6});
                        var table = layui.table;
                        table.reload('suspicious-images-table', {}); //只重载数据
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
        //监听搜索
        form.on('submit(images-search)', function (data) {
            var field = data.field;
            var table = layui.table;
            table.reload('suspicious-images-table', {
                where: field
                , page: {
                    curr: 1 //重新从第 1 页开始
                }
            }); //只重载数据
            //执行重载
        });

        form.on('submit(save-fraction)', function (data) {
            var field = form.val("suspicious-images-fraction");
            var table = layui.table;
            layer.confirm('确定更新选中鉴黄分数吗？', {icon: 3, title: '提示'}, function (index) {
                var checkStatus = table.checkStatus('suspicious-images-table');
                var data = checkStatus.data;
                var images = [];
                data.forEach(function (value, index) {
                    images.push({
                        'id': value.id,
                        'fraction': field.fraction,
                    });
                });
                $.ajax({
                    url: ajaxPath + "/images/update",
                    type: 'post',
                    data: {'images': images},
                    dataType: 'json',
                    success: function (data) {
                        if (data.code === 200) {
                            layer.msg('更新选中鉴黄分数成功', {icon: 6});
                            table.reload('suspicious-images-table', {}); //只重载数据
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
        //工具栏事件
        $('#deleteImages').click(function (data) {
            layer.confirm('确定删除吗？', {icon: 3, title: '提示'}, function (index) {
                var checkStatus = table.checkStatus('suspicious-images-table');
                var data = checkStatus.data;
                var images = [];
                data.forEach(function (value, index) {
                    images.push({
                        'id': value.id,
                        'userId': value.user_id,
                    });
                });
                $.ajax({
                    url: ajaxPath + '/images/delete',
                    type: 'post',
                    data: {'images': images},
                    dataType: 'json',
                    success: function (data) {
                        if (data.code === 200) {
                            layer.msg('删除成功', {icon: 6});
                            table.reload('suspicious-images-table', {}); //只重载数据
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
            table.reload('suspicious-images-table', {}); //只重载数据
        });
        obj("suspiciousImages", {});
    });
