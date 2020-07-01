$.ajax({
    url: '/ajax/userwaterMark/get',
    success: function (data) {
        console.log(data)
        if (data.code === 200) {
            var res = data.data;
            for (var i in res) {
                for (var index in res[i]) {
                    if (index !== 'pathname') {
                        $('#' + i).find('input[name=' + index + ']').val(res[i][index]);
                    }
                    $('#' + i).find('select[name=' + index + ']').val(res[i][index]);
                }
            }
            $('#watermarkImage').html('<img style="max-width:200px;" data-src="/images/' + res['imageWatermark']['pathname'] + '" src="/images/' + res['imageWatermark']['pathname'] + '?v=' + new Date().getTime() + '" alt="水印图片" onerror="this.onerror=null; this.src=\'/images/watermark/notfound\'";" />')
        } else {
            spop({
                template: data.msg,
                autoclose: spopTimeOut,
                style: 'warning'
            });
        }
    },
    error: function () {
        spop({
            template: '请求失败',
            autoclose: spopTimeOut,
            style: 'error'
        });
    },
});

$(document).keyup(function (event) {
    if (event.keyCode === 13) {
        $("#send").trigger("click");
    }
});
$('input[name="pathname"]').change(function () {
    let file = $('input[name="pathname"]')[0].files[0];
    if (file !== undefined) {
        $('#filename').text(file.name);
    }
})
$('#send').click(function () {
    var formData = new FormData();                      // 创建一个form类型的数据

    $('#content > div').each(function () {
        let id = $(this).attr('id');
        let tmp = [];
        $(this).find('input').each(function () {
            let key = $(this).attr('name');
            tmp[key] = $(this).val();
            formData.append(id + '[' + key + ']', $(this).val());
        });
        $(this).find('select').each(function () {
            let key = $(this).attr('name');
            tmp[key] = $(this).val();
            formData.append(id + '[' + key + ']', $(this).val());
        });
    });
    let file = $('input[name="pathname"]')[0].files[0];
    if (file !== undefined) {
        formData.append('file', file);
    }
    spop({
        template: '正在提交，请稍等',
        autoclose: spopTimeOut,
        style: 'info'
    });
    $.ajax({
        url: '/ajax/userwaterMark/update',
        type: 'post',
        data: formData,
        processData: false,                // jQuery不要去处理发送的数据
        contentType: false,                // jQuery不要去设置Content-Type请求头
        success: function (data) {
            if (data.code === 200) {
                spop({
                    template: '更新成功',
                    autoclose: spopTimeOut,
                    style: 'success'
                });
                $('#watermarkImage img').attr('src', $('#watermarkImage img').data('src') + '?v=' + new Date().getTime());
            } else {
                spop({
                    template: data.msg,
                    autoclose: spopTimeOut,
                    style: 'warning'
                });
            }
        },
        error: function () {
            spop({
                template: '请求失败',
                autoclose: spopTimeOut,
                style: 'error'
            });
        },
    });
});