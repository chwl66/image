/**
 * Hidove Ivey
 * wwww.hidove.cn
 * 2019年10月16日00:12:15
 */
;
layui.define(["table", "form"],
    function (obj) {
        var form = layui.form;
        //监听提交
        form.on('submit(send)', function (data) {
            let field = data.field;
            let imageUrl = field.imageUrl.split(/[(\r\n)\r\n]+/);
            let username = field.username.split(/[(\r\n)\r\n]+/);
            imageUrl = imageUrl.filter(function (value) {
                return value !== '';
            });
            username = username.filter(function (value) {
                return value !== '';
            });
            if (imageUrl.length > 0) {
                $.ajax({
                    url: ajaxPath + '/cache/imageByUrl',
                    type: 'post',
                    data: {'url': imageUrl},
                    success: function (data) {
                        if (data.code === 200) {
                            layer.msg('刷新图片缓存成功', {icon: 6});
                        } else {
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error: function () {
                        layer.msg(data.msg, {icon: 5});
                    },
                });
            }
            if (username.length > 0) {
                $.ajax({
                    url: ajaxPath + '/cache/ImageByUsername',
                    type: 'post',
                    data: {'username': username},
                    success: function (data) {
                        if (data.code === 200) {
                            layer.msg('刷新用户图片缓存成功', {icon: 6});
                        } else {
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error: function () {
                        layer.msg(data.msg, {icon: 5});
                    },
                });
            }
            return false;
        });
        //监听刷新全部按钮
        $('#refreshAllOfImage').click(function () {
            $.ajax({
                url: ajaxPath + '/cache/allOfImage',
                type: 'get',
                success: function (response) {
                    if (response.code === 200) {
                        layer.msg('刷新全部图片缓存成功', {icon: 1});
                    } else {
                        layer.msg(response.msg, {icon: 2});
                    }
                },
                error: function () {
                    layer.msg('请求失败', {icon: 2});
                }
            });
        });
        $('#refreshAllOfImageDistribution').click(function () {
            $.ajax({
                url: ajaxPath + '/cache/imageIsValid',
                type: 'get',
                success: function (response) {
                    if (response.code === 200) {
                        layer.msg('刷新全部图片分发识别缓存成功', {icon: 1});
                    } else {
                        layer.msg(response.msg, {icon: 2});
                    }
                },
                error: function () {
                    layer.msg('请求失败', {icon: 2});
                }
            });
        });
        $('#refreshAllOfConfig').click(function () {
            $.ajax({
                url: ajaxPath + '/cache/allOfConfig',
                type: 'get',
                success: function (response) {
                    if (response.code === 200) {
                        layer.msg('刷新全部配置缓存成功', {icon: 1});
                    } else {
                        layer.msg(response.msg, {icon: 2});
                    }
                },
                error: function () {
                    layer.msg('请求失败', {icon: 2});
                }
            });
        });
        $('#refreshOpcache').click(function () {
            $.ajax({
                url: ajaxPath + '/cache/opcache',
                type: 'get',
                success: function (response) {
                    if (response.code === 200) {
                        layer.msg('刷新Opcache缓存成功', {icon: 1});
                    } else {
                        layer.msg(response.msg, {icon: 2});
                    }
                },
                error: function () {
                    layer.msg('请求失败', {icon: 2});
                }
            });
        });
        $('#refreshAll').click(function () {
            $.ajax({
                url: ajaxPath + '/cache/all',
                type: 'get',
                success: function (response) {
                    if (response.code === 200) {
                        layer.msg('刷新缓存成功', {icon: 1});
                    } else {
                        layer.msg(response.msg, {icon: 2});
                    }
                },
                error: function () {
                    layer.msg('请求失败', {icon: 2});
                }
            });
        });
        obj("cache", {});
    });