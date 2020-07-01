layui.define(["upload"],
    function (obj) {
        let upload = layui.upload;

        //执行实例
        var uploadInst = upload.render({
            elem: '#uploadImageWatermark' //绑定元素
            , url: ajaxPath + '/adminImageEdit/upload' //上传接口
            , done: function (res) {
                $('#imageWatermark > img').attr('src','/images/watermark/watermark?v='+new Date().getTime())
                //上传完毕回调
            }
            , error: function () {
                //请求异常回调
            }
        });

        obj("imageEdit", {});
    });