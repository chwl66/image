$(document).ready(function () {
    $('.links-tab li').click(function () {
        $('.links-tab li').removeClass('active');
        $('.links-tab .content>textarea').removeClass('active');
        let hash = $(this).data('hash');
        $('#' + hash).addClass('active');
        $(this).addClass('active');
    });

    $("#upload").change(function () {
        upload(this.files);
    });


    $('#remove').click(() => {
        $('#links-url').text('')
        $('#links-html').text('')
        $('#links-bbcode').text('')
        $('#links-markdown').text('')
        $('#links-markdownwithlink').text('')
        msg('清空成功')
    })
    $('#copy').click(() => {
        copy($('.links-tab .content>textarea.active').text());
        msg('复制成功')
    });

    //监听拖拽

    $('.card').on('dragenter', (event) => {
        $('.card').addClass('shaky');
    });
    $('.card').on('dragleave', (event) => {
        $('.card').removeClass('shaky');
    });
    $('.card').on('drop', (event) => {
        $('.card').removeClass('shaky');
    });
    //监听粘贴板

    $(document).on('paste', function (event) {
        if (event.clipboardData || event.originalEvent) {
            var clipboardData = (event.clipboardData || event.originalEvent.clipboardData);
            if (clipboardData.items) {
                // for chrome
                var items = clipboardData.items,
                    len = items.length,
                    blob = null;
                event.preventDefault();
                let images = [];
                for (var i = 0; i < len; i++) {
                    if (items[i].type.indexOf("image") !== -1) {
                        blob = items[i].getAsFile();
                        if (blob != null) {
                            upload(blob);
                        }
                    } else if (items[i].type === 'text/plain') {
                        items[i].getAsString(function (str) {
                            uploadFromImageUrl(str)
                        })
                    }

                }
            }
        }
    });


    $('.loading').toggle();
});

function uploadFromImageUrl(url) {
    url = url.trim();
    $('.loading').css('display','flex');
    let p = new Promise((resolve,reject) => {
        $.ajax({
            url: '/ajax/imagePreview?url=' + url,
            cache: false,
            xhrFields: {
                responseType: 'blob'
            },
            success: function (blob) {
                const arr = url.split('/');
                let fileName = arr.pop();
                if (fileName === '') {
                    reject('非法链接，注意！仅支持图片链接')
                    return;
                }
                let index = fileName.lastIndexOf('.');
                if (index === -1) {
                    fileName = fileName + '.jpg';
                }
                resolve( new window.File([blob], fileName));
            },
            error: function (data) {
                reject(data.statusText)
            }
        });

    }).catch((res)=>{
        msg(res)
    });
    p.then((file) => {
        upload(file);
    })
}

function upload(files) {

    $('.loading').css('display','flex');

    let task = [];


    if (!Array.isArray(files) && !(files instanceof FileList)) {
        files = [files];
    }
    for (let item of files) {
        task.push(uploadPromise(item, apiType))
    }
    Promise.all(task)
        .catch((res) => {
            msg(res)
        })
        .finally(() => {
            $('.loading').css('display','none');
        })
}

function uploadPromise(file, apiType) {

    return new Promise((resolve, reject) => {
        let formdata = new FormData();
        formdata.append('image', file);
        formdata.append('apiType', apiType);
        $.post({
            url: '/api/upload',
            data: formdata,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: (res) => {
                if (res.code !== 200) {
                    reject(res.msg)
                    return;
                }
                let urlArr = spliceUrl(res.data.url.distribute, file.name)
                $('#links-url').append(urlArr.url + "\n")
                $('#links-html').append(document.createTextNode(urlArr.html + "\n"))
                $('#links-bbcode').append(urlArr.bbCode + "\n")
                $('#links-markdown').append(urlArr.markdown + "\n")
                $('#links-markdownwithlink').append(urlArr.markdownWithLink + "\n")
                $('.links-tab').css('display', 'flex');
                resolve(file.name + '：success')
            },
            error: (res) => {
                reject(file.name + ' 发现异常：' + res.statusText)
            }
        })
    })
}

function msg(msg = 'success') {

    spop({
        template: msg,
        position: 'top-right',
        style: 'success',
        autoclose: 2000,
    });
}

function spliceUrl(url, title) {
    return {
        url: url,
        html: '<a href="' + url + '" target="_blank"><img src="' + url + '" alt="' +
            title + '"></a>',
        bbCode: '[URL=' + url + '][IMG]' + url + '[/IMG][/URL]',
        markdown: '![' + title + '](' + url + ')',
        markdownWithLink: '[![' + url + '](' + url + ')](' + url + ')',
    }
}

function copy(text) {
    let oInput = document.createElement('textarea');
    oInput.value = text;
    document.body.appendChild(oInput);
    oInput.select(); // 选择对象
    document.execCommand("Copy"); // 执行浏览器复制命令
    oInput.className = 'oInput';
    oInput.style.display = 'none';
    oInput.remove();
}