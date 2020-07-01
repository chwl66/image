var clip = function(el) {
    var range = document.createRange();
    range.selectNodeContents(el);
    var sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);
};
function uploadImageUrl(url) {
    Swal.fire({
        type: 'warning', // 弹框类型
        title: '上传', //标题
        text: "是否上传粘贴板链接图片？", //显示内容
        confirmButtonText: '确定',// 确定按钮的 文字
        showCancelButton: true, // 是否显示取消按钮
        cancelButtonText: "取消", // 取消按钮的 文字
        focusCancel: true, // 是否聚焦 取消按钮
    }).then(function (isConfirm) {
        if (isConfirm.value) {
            $.ajax({
                url:'/ajax/imagePreview?url=' + url,
                cache:false,
                xhrFields:{
                    responseType: 'blob'
                },
                success: function(blob){
                    const arr =  url.split('/');
                    var fileName = arr.pop();
                    if (fileName === ''){
                        spop({
                            template: '非法链接，注意！仅支持图片链接',
                            autoclose: spopTimeOut,
                            style: 'error'
                        });
                        return ;
                    }
                    let index = fileName.lastIndexOf('.');
                    if (index === -1){
                        fileName = fileName + '.jpg';
                    }
                    blob.name = fileName;
                    let images = [];
                    images.push(blob);
                    if(images.length > 0) {
                        let form = $("#Hidove");
                        $('.file-drop-zone-title').remove();
                        form.fileinput('readFiles', images);
                        spop({
                            template: '已添加到上传列表',
                            autoclose: spopTimeOut,
                            style: 'success'
                        });
                    }
                },error:function (data) {
                    spop({
                        template: '非法链接，注意！仅支持图片链接',
                        autoclose: spopTimeOut,
                        style: 'error'
                    });
                }
            });
        }else {
            Swal.close();
        }
    });
}
function uploadBlobFile(images) {
    let form = $("#Hidove");
    Swal.fire({
        type: 'warning', // 弹框类型
        title: '上传', //标题
        text: "是否上传粘贴板图片？", //显示内容
        confirmButtonText: '确定',// 确定按钮的 文字
        showCancelButton: true, // 是否显示取消按钮
        cancelButtonText: "取消", // 取消按钮的 文字
        focusCancel: true, // 是否聚焦 取消按钮
        // reverseButtons: true  // 是否 反转 两个按钮的位置 默认是  左边 确定  右边 取消
    }).then(function (isConfirm) {
        if (isConfirm.value) {
            $('.file-drop-zone-title').remove();
            form.fileinput('readFiles', images);
            spop({
                template: '已添加到上传列表',
                autoclose: spopTimeOut,
                style: 'success'
            });
            // form.fileinput('upload');
        }else {
            Swal.close();
        }
    });
}
function generateFileId (file) {
    if (!file) {
        return null;
    }
    var relativePath = String(file.relativePath || file.webkitRelativePath || $h.getFileName(file) || null);
    if (!relativePath) {
        return null;
    }
    return (file.size + '_' + relativePath.replace(/\s/img, '_'));
}

document.addEventListener('paste', function (event) {
    var isChrome = false;
    if ( event.clipboardData || event.originalEvent ) {
        var clipboardData = (event.clipboardData || event.originalEvent.clipboardData);
        if ( clipboardData.items ) {
            // for chrome
            var  items = clipboardData.items,
                len = items.length,
                blob = null;
            isChrome = true;
            event.preventDefault();
            let images = [];
            for (var i = 0; i < len; i++) {
                if (items[i].type.indexOf("image") !== -1) {
                    blob = items[i].getAsFile();
                    images.push(blob);
                    if(images.length > 0) {
                        uploadBlobFile(images);
                    }
                }else if (items[i].type == 'text/plain'){
                    items[i].getAsString(function (str) {
                        uploadImageUrl(str);
                    })
                }

            }
        } else {
            //for firefox
        }
    } else {
    }
});
