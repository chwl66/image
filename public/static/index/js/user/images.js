function initialize() {
    $.ajax({
        url: '/ajax/api/get',
        type: 'get',
        success: function (data) {
            if (data.code === 200) {
                var html = '<div class="btn-group-toggle" data-toggle="buttons" id="Hidove-apiInfo-list">';
                var apiInfo = data.data;
                for (var key in apiInfo) {
                    if (key === 'privateStorage') {
                        for (var index in apiInfo[key]) {
                            var tmp = $.base64.encode(JSON.stringify(apiInfo[key][index]));
                            if (apiInfo[key][index]['cdn'] == '') {
                                html = html +
                                    '<label class="btn btn-outline-secondary disabled" data-key="' + apiInfo[key][index]['name'] + '" data-json="'
                                    + tmp + '">\n' +
                                    ' <input type="checkbox" autocomplete="off" name="privateStorage" value="' + apiInfo[key][index]['name'] + '">Private ' + apiInfo[key][index]['name'] +
                                    '</label>';
                            } else {
                                html = html +
                                    '<label class="btn btn-outline-secondary" data-key="' + apiInfo[key][index]['name'] + '" data-json="'
                                    + tmp + '">\n' +
                                    ' <input type="checkbox" autocomplete="off" name="privateStorage" value="' + apiInfo[key][index]['name'] + '">Private ' + apiInfo[key][index]['name'] +
                                    '</label>';
                            }
                        }
                    } else {
                        var tmp = $.base64.encode(JSON.stringify(apiInfo[key]));
                        html = html +
                            '<label class="btn btn-outline-secondary" data-key="' + apiInfo[key]['key'] + '" data-json="'
                            + tmp + '">\n' +
                            ' <input type="checkbox" autocomplete="off" name="apitype" value="' + apiInfo[key]['key'] + '">' + apiInfo[key]['name'] +
                            '</label>';
                    }
                }
                html = html +
                    '</div>';
                $('#Hidove-apiInfo').html(html);
            } else {
                spop({
                    template: data.msg,
                    autoclose: spopTimeOut,
                    style: 'error'
                });
            }
        },
        error: function (data) {
            spop({
                template: '获取API接口列表失败，请重试',
                autoclose: spopTimeOut,
                style: 'error'
            });
        },
    });
    // Context Start
    context.init({
        fadeSpeed: 100,
        filter: function ($obj) {
        },
        above: 'auto',
        preventDoubleContext: true,
        compress: false
    });
    //加载空白处右键菜单
    context.attach('.container', [
        {header: 'Compressed Menu'},
        {
            text: '新建文件夹', action: function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Please enter your folder name',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    showLoaderOnConfirm: true,
                    inputValidator: function (value) {
                        if (!value) {
                            spop({
                                template: '文件名格式错误',
                                autoclose: spopTimeOut,
                                style: 'error'
                            });
                            Swal.clickCancel();
                        }
                    },
                }).then(function (value) {
                    if (value.value) {
                        var parentId = $('.Hidove-breadcrumb>.breadcrumb li').last().data('id');
                        $.ajax({
                            url: '/ajax/userImages/buildFolder',
                            type: 'post',
                            data: {
                                'name': value.value,
                                'parentId': parentId
                            },
                            dataType: 'json',
                            success: function (data) {
                                if (data.code == 200) {
                                    spop({
                                        template: data.msg,
                                        autoclose: spopTimeOut,
                                        style: 'success'
                                    });
                                    Render(parentId);
                                } else {
                                    spop({
                                        template: data.msg,
                                        autoclose: spopTimeOut,
                                        style: 'error'
                                    });
                                }
                            },
                            error: function (data) {
                                spop({
                                    template: '请求失败，请重试',
                                    autoclose: spopTimeOut,
                                    style: 'error'
                                });
                            },
                        });
                    }
                });
            }
        },
        {
            text: '刷新', action: function (e) {
                e.preventDefault();
                let parentId = $('.Hidove-breadcrumb>.breadcrumb li').last().data('id');
                switch (Number(status)) {
                    case 1:
                        Render(parentId);
                        break;
                    case 2:
                        let keyword = $('#Hidove-action-search').val();
                        imageSearch(keyword, initialPage, initialLimit, initialFolder);
                        break;
                    default:
                        Render(parentId);

                }
            }
        }
    ]);
    var clipboard = new ClipboardJS('.copy-url');
    // 监听复制操作
    clipboard.on('success', function (e) {
        spop({
            template: '复制成功！',
            autoclose: spopTimeOut,
            style: 'success'
        });
        e.clearSelection();
    });
    clipboard.on('error', function (e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
        spop({
            template: '复制失败！',
            autoclose: spopTimeOut,
            style: 'error'
        });
    });
    //加载model中复制url组件
    var imageUrlCopy = new ClipboardJS('#imageUrlCopy', {
        container: document.getElementById('imageAttribute')
    });
    // 监听复制操作
    imageUrlCopy.on('success', function (e) {
        spop({
            template: '复制成功！',
            autoclose: spopTimeOut,
            style: 'success'
        });
        e.clearSelection();
    });
    imageUrlCopy.on('error', function (e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
        spop({
            template: '复制失败！',
            autoclose: spopTimeOut,
            style: 'error'
        });
    });
    //第一次加载图片文件夹
    Render(initialFolder);
}

// 回车搜索
$('#Hidove-action-search').on('keypress', function (event) {
    if (event.keyCode === 13) {
        $('#folders').text('');
        $('#images').text('');
        var keyword = $('#Hidove-action-search').val();
        if (keyword.length > 0) {
            status = 2;
            imageSearch(keyword, initialPage, initialLimit, initialFolder);
        } else {
            Render(initialFolder)
        }
    }
});
$('#selectAction').change(function () {
    let value = $(this).val();
    let items = $('.imageinfo-active');
    let imageId = [];
    $(this).val('');
    if (value === '移动') {
        items.each(function () {
            var $item = $(this);
            var imageInfo = JSON.parse($.base64.decode($item.data('json')));
            imageId.push(imageInfo['id']);
        });
        $('#Hidove-move-image').data('imageId', imageId);
        var html = `<li class="breadcrumb-item" onclick="loadMoveListBreadcrumb(this);" data-id="0" data-parent_id="0"><a>Home</a></li>`;
        $('.Hidove-move-breadcrumb>.breadcrumb').html(html);
        $('#Hidove-move-image').modal('show');
        RenderMoveList(0);
    } else if (value === '删除') {
        spop({
            template: '正在处理中',
            group: 'process',
            style: 'info',
        });
        items.each(function () {
            var $item = $(this);
            var imageInfo = JSON.parse($.base64.decode($item.data('json')));
            imageId.push(imageInfo.id)
        });
        $.ajax({
            url: '/ajax/userImages/deleteImage',
            type: 'get',
            dataType: 'json',
            data: {id: imageId},
            success: function (data) {
                if (data.code === 200) {
                    items.remove();
                    //清除当前的列表
                    spop({
                        template: '删除成功',
                        autoclose: spopTimeOut,
                        group: 'process',
                        style: 'success'
                    });
                } else {
                    spop({
                        template: data.msg,
                        autoclose: spopTimeOut,
                        group: 'process',
                        style: 'error'
                    });
                }
            },
            error: function (data) {
                spop({
                    template: '请求错误',
                    autoclose: spopTimeOut,
                    group: 'process',
                    style: 'error'
                });
            },
        });

    } else if (value === '强制删除') {

        Swal.fire({
            title: '确定删除吗？',
            html: $('<div>')
                .text('强制删除会屏蔽错误信息以实现删除'),
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '删除',
            animation: true,
            customClass: 'animated tada'
        }).then(function (isConfirm) {
            if (isConfirm.value) {
                spop({
                    template: '正在处理中',
                    group: 'process',
                    style: 'info',
                });
                items.each(function () {
                    var $item = $(this);
                    var imageInfo = JSON.parse($.base64.decode($item.data('json')));
                    imageId.push(imageInfo.id)
                });
                $.ajax({
                    url: '/ajax/userImages/deleteImage',
                    type: 'get',
                    dataType: 'json',
                    data: {id: imageId, force: 1},
                    success: function (data) {
                        if (data.code === 200) {
                            items.remove();
                            //清除当前的列表
                            spop({
                                template: '删除成功',
                                autoclose: spopTimeOut,
                                group: 'process',
                                style: 'success'
                            });
                        } else {
                            spop({
                                template: data.msg,
                                autoclose: spopTimeOut,
                                group: 'process',
                                style: 'error'
                            });
                        }
                    },
                    error: function (data) {
                        spop({
                            template: '请求错误',
                            autoclose: spopTimeOut,
                            group: 'process',
                            style: 'error'
                        });
                    },
                });
            }
            Swal.close();
        });

    } else if (value === '更新') {
        Swal.fire({
            title: '<strong>Tips 选择需要更新的CDN节点</strong>',
            type: 'info',
            html: $('#Hidove-apiInfo').html(),
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText:
                '更新',
            cancelButtonText:
                '取消',
        }).then(function (result) {
            if (result.value) {
                var activeApi = [];
                var publicCloud = [];
                var privateStorage = [];
                $('#Hidove-apiInfo-list .active').each(function (obj) {
                    var $active = $(this);
                    if ($active.find('input[name=privateStorage]').length === 0) {
                        publicCloud.push($active.data('key'));
                    } else {
                        privateStorage.push($active.data('key'));
                    }
                });
                activeApi = {
                    'publicCloud': publicCloud,
                    'privateStorage': privateStorage,
                };
                spop({
                    template: '正在更新中！',
                    style: 'info',
                    group: 'updateCdn',
                });
                var updateAjax = [];
                items.each(function () {
                    var $item = $(this);
                    var imageInfo = JSON.parse($.base64.decode($item.data('json')));
                    var json = {
                        'id': imageInfo.id,
                        'apiType': activeApi,
                    };
                    var ajax = $.ajax({
                        url: '/ajax/userImages/updateImageInfo',
                        type: 'post',
                        data: json,
                        success: function (data) {
                            if (data.code === 200) {
                                spop({
                                    template: '[' + imageInfo.filename + ']更新成功！',
                                    autoclose: spopTimeOut,
                                    style: 'success',
                                });
                            } else {
                                spop({
                                    template: data.msg,
                                    autoclose: spopTimeOut,
                                    style: 'danger'
                                });
                            }
                        },
                        error: function () {
                            spop({
                                template: '[' + imageInfo.filename + ']请求错误！',
                                autoclose: spopTimeOut,
                                style: 'danger',
                            });
                        },
                    });
                    updateAjax.push(ajax);
                });
                Promise.all(updateAjax).then((results) => {
                    spop({
                        template: '更新完毕！',
                        autoclose: spopTimeOut,
                        style: 'success',
                        group: 'updateCdn',
                    });
                });
                $.when(updateAjax).done(function () {
                });
            }
        });
    } else if (value === '导出') {
        let $items = $('.imageinfo-active img');
        let imageUrl = [];
        $items.each(function () {
            var original = $(this).data('original');
            imageUrl = imageUrl + original + "\n"
        });
        Swal.fire({
            title: '<strong>Url <u>List</u></strong>',
            html:
                '<textarea class="form-control" rows="5">' + imageUrl + '</textarea>',
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonText:
                'Copy',
        }).then((result) => {
            if (result.value) {
                $('#copy-url').attr('data-clipboard-text', imageUrl).click();
            }
        })
    }
});
//每页显示数目
$('#limit').change(function () {
    initialLimit = $(this).val();
    switch (status) {
        case 1:
            imageSearch('', initialPage, initialLimit, initialFolder);
            break;
        case 2:
            let keyword = $('#Hidove-action-search').val();
            imageSearch(keyword, initialPage, initialLimit, initialFolder);
            break;
        default:
            imageSearch('', initialPage, initialLimit, initialFolder);
    }
});
//弹出框中的打开链接
$("#imageUrlOpen").click(function () {
    window.open($("#imageUrlOpen").data('url'));
});
//弹出框中的删除链接
$("#imageUrlDelete").click(function () {
    spop({
        template: '正在删除中',
        group: 'deleting',
        style: 'info',
    });
    var id = $("#imageUrlDelete").data('id');
    $.ajax({
        url: '/ajax/userImages/deleteImage/?id=' + id,
        type: 'get',
        success: function (data) {
            if (data.code === 200) {
                spop({
                    template: data.msg,
                    autoclose: spopTimeOut,
                    style: 'success',
                    group: 'deleting'
                });
            } else {
                spop({
                    template: data.msg,
                    autoclose: spopTimeOut,
                    style: 'error',
                    group: 'deleting'
                });
            }
        },
        error: function (data) {
            spop({
                template: '请求失败，请重试',
                autoclose: spopTimeOut,
                style: 'error',
                group: 'deleting'
            });
        },
    });
});
//按钮全选
$('#selectAll').click(function () {
    if ($('#selectAll').text() === '全选') {
        $('#selectAll').text('全不选')
        $('.imageinfo').addClass('imageinfo-active');
    } else {
        $('#selectAll').text('全选')
        $('.imageinfo').removeClass('imageinfo-active');
    }
});
//文件夹重命名
$('#folderRenameSubmit').click(function () {
    var newName = $('#folderName').val();
    var id = $('#folderName').data('id');
    $.ajax({
        url: '/ajax/userImages/folderRename',
        type: 'get',
        data: {
            'id': id,
            'newName': newName
        },
        dataType: 'json',
        success: function (data) {
            if (data.code === 200) {
                spop({
                    template: data.msg,
                    autoclose: spopTimeOut,
                    style: 'success'
                });
                $('#folder-rename').modal('hide');
                Render($('#folderName').data('parent_id'));
            } else {
                spop({
                    template: data.msg,
                    autoclose: spopTimeOut,
                    style: 'error'
                });
            }
        },
        error: function (data) {
            spop({
                template: '请求失败，请重试',
                autoclose: spopTimeOut,
                style: 'error'
            });
        },
    });
});
//图片移动提交按钮
$('#Hidove-move-image-Submit').click(function () {
    var imageId = $('#Hidove-move-image').data('imageId');
    var folderId = $('.Hidove-move-breadcrumb .breadcrumb li').last().data('id');
    var oldfolderId = $('.Hidove-breadcrumb .breadcrumb li').last().data('id');
    $.ajax({
        url: '/ajax/userImages/moveImage',
        type: 'get',
        data: {
            'imageId': imageId,
            'folderId': folderId
        },
        dataType: 'json',
        success: function (data) {
            if (data.code === 200) {
                spop({
                    template: data.msg,
                    autoclose: spopTimeOut,
                    style: 'success'
                });
                $('#Hidove-move-image').modal('hide');
                Render(oldfolderId);
            } else {
                spop({
                    template: data.msg,
                    autoclose: spopTimeOut,
                    style: 'error'
                });
            }
        },
        error: function (data) {
            spop({
                template: '请求失败，请重试',
                autoclose: spopTimeOut,
                style: 'error'
            });
        },
    });
});

//重载文件文件管理器
function Render(folderId) {
    //操作锁定
    if (lock === true) return;
    lock = true;
    folderExtend = false;
    initialFolder = folderId;
    status = 1;
    $('#images').text('');
    loadFolders(initialFolder);
    $.when(folderAjax).done(function () {
        //加载图片
        imageSearch('', initialPage, initialLimit, initialFolder);
        lock = false;
    });
}

//加载管理器中文件夹
function loadFolders(parent_id) {
    folderAjax = $.ajax({
        url: '/ajax/userImages/folders/?id=' + parent_id,
        type: 'get',
        success: function (data) {
            if (data.code === 200) {
                data.data.forEach(function (currentValue, index, arr) {
                    var html = `
                                <div class="col-4 col-sm-3 col-md-2 col-xs-1 Hidove-folders" data-id="` + currentValue.id + `" data-parent_id="` + currentValue.parent_id + `" data-json=` + $.base64.encode(JSON.stringify(currentValue)) + `>
                                <div class="image-main">
                                <a><img src="/static/index/images/folder-open.svg"></a>
                                        <div class="caption">
                                            <p>` + currentValue.name + `</p>
                                        </div>
                                 </div>
                             </div>`;
                    $('#images').append(html);
                });
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
                template: data.msg,
                autoclose: spopTimeOut,
                style: 'warning'
            });
        }
    });
}

//加载管理器中图片
//图片搜索
function imageSearch(keyword, page, limit, folder) {
    imagesAjax = $.ajax({
        url: '/ajax/UserImages/imageSearch?keyword=' + keyword + '&page=' + page + '&limit=' + limit + '&folder=' + folder,
        type: 'get',
        success: function (data) {
            if (data.code === 200) {
                $('.imageinfo').remove();
                $('#totalImage').text(data.totalImage);
                data.data.forEach(function (currentValue, index, arr) {
                    var html = `
                                <div class="col-4 col-sm-3 col-md-2 col-xs-auto imageinfo" data-id="` + currentValue.id + `" data-json=` + $.base64.encode(JSON.stringify(currentValue)) + `>
                                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                            <a class="fancybox" href="` + currentValue.distribute + `" class="fancybox" data-caption="` + currentValue.filename + `" data-fancybox="gallery" data-key="` + index + `">
                            <img class="shadow" data-original="` + currentValue.distribute + `">
                            </a>
                            <div class="caption">
                            <p>` + currentValue.filename + `</p>
                            </div>
                             </div>`;
                    $('#images').append(html);
                    lazyload($("#images .fancybox img"));
                });
                var html = ``;
                $('.pagination').text('');
                if (data.page == 1) {
                    html = `
                    <li class="page-item disabled">
                            <p class="page-link" onclick="imageSearch('` + keyword + `',` + (Number(data.page) - 1) + `,` + limit + `,` + folder + `);" tabindex="-1">Previous</p>
                            </li>`;
                } else {
                    html = `
                            <li class="page-item">
                            <p class="page-link" onclick="imageSearch('` + keyword + `',` + (Number(data.page) - 1) + `,` + limit + `,` + folder + `);">Previous</p>
                            </li>`;
                }
                $('.pagination').append(html);
                html = '';
                for (var index = 1; index <= Math.ceil(data.count / data.limit); index++) {
                    if (index == data.page) {
                        html = html + `
    <li class="page-item disabled"><p class="page-link" onclick="imageSearch('` + keyword + `',` + index + `,` + limit + `,` + folder + `);">` + index + `</p></li>`;
                    } else if (data.page > 5 && index == 3) {
                        html = html + `
    <li class="page-item"><p class="page-link" onclick="imageSearch('` + keyword + `',` + index + `,` + limit + `,` + folder + `);">...</p></li>`;
                    } else if (data.page - index > 3 && index > 3) {
                        continue;
                    } else if ((Math.ceil(data.count / data.limit) - index) < 2) {
                        html = html + `
    <li class="page-item"><p class="page-link" onclick="imageSearch('` + keyword + `',` + index + `,` + limit + `,` + folder + `);">` + index + `</p></li>`;
                    } else if ((index - data.page) == 3) {
                        html = html + `
    <li class="page-item disabled"><p class="page-link" onclick="imageSearch('` + keyword + `',` + index + `,` + limit + `,` + folder + `);">...</p></li>`;
                    } else if ((index - data.page) > 3) {
                        continue;
                    } else {
                        html = html + `
    <li class="page-item"><p class="page-link" onclick="imageSearch('` + keyword + `',` + index + `,` + limit + `,` + folder + `);">` + index + `</p></li>`;
                    }
                }
                $('.pagination').append(html);
                if (Math.ceil(data.count / data.limit) == data.page) {
                    html = `
                    <li class="page-item disabled">
                            <p class="page-link" onclick="imageSearch('` + keyword + `',` + (Number(data.page) + 1) + `,` + limit + `,` + folder + `);">Next</p>
                            </li>`;
                } else {
                    html = `
                            <li class="page-item">
                            <p class="page-link" onclick="imageSearch('` + keyword + `',` + (Number(data.page) + 1) + `,` + limit + `,` + folder + `);">Next</p>
                            </li>`;
                }
                $('.pagination').append(html);
                //清除当前的列表
                spop({
                    template: '加载完毕',
                    group: 'loading',
                    autoclose: spopTimeOut,
                    style: 'success'
                });
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
                template: data.msg,
                autoclose: spopTimeOut,
                style: 'error'
            });
        },
    });

    $.when(imagesAjax).done(function () {
        if (page <= 1) {
            $('.Hidove-folders').show();
            //加载文件夹拓展
            loadFoldersMethod();
        }else{
            $('.Hidove-folders').hide();
        }
        //加载图片拓展
        loadImageInfo();
    });
}

//加载图片拓展
function loadImageInfo() {
    //要执行的操作
    $(".imageinfo>i").click(function () {
        var $parent = $(this).parent();
        if ($parent.hasClass('imageinfo-active')) {
            $parent.removeClass('imageinfo-active');
        } else {
            $parent.addClass('imageinfo-active');
        }
    });
    //加载fancybox图片查看器
    $(".fancybox").fancybox({
        padding: 15,
        infobar: true,
        toolbar: true,
        buttons: [
            'slideShow',
            'fullScreen',
            'download',
            'zoom',
            'thumbs',
            'close'
        ]
    });
    $('.imageinfo').on('contextmenu', function (e) {
        var $item = $(this);
        var data = JSON.parse($.base64.decode($item.data('json')));
        context.attach('.imageinfo', [
            {header: 'Compressed Menu'},
            {
                text: '查看图片', action: function (e) {
                    e.preventDefault();
                    $item.find('img').click();
                }
            },
            {text: '新窗口打开图片', href: data.info, target: '_blank'},
            {divider: true},
            {
                text: '复制链接', action: function (e) {
                    e.preventDefault();
                    $('#copy-url').attr('data-clipboard-text', data.distribute).click();
                }
            },
            {
                text: '删除', action: function (e) {
                    e.preventDefault();
                    spop({
                        template: '正在删除中',
                        group: 'deleting',
                        style: 'info',
                    });
                    $.ajax({
                        url: '/ajax/UserImages/deleteImage/?id=' + data.id,
                        type: 'get',
                        success: function (data) {
                            if (data.code === 200) {
                                spop({
                                    template: data.msg,
                                    autoclose: spopTimeOut,
                                    style: 'success',
                                    group: 'deleting'
                                });
                                $item.remove();
                            } else {
                                spop({
                                    template: data.msg,
                                    autoclose: spopTimeOut,
                                    style: 'error',
                                    group: 'deleting'
                                });
                            }
                        },
                        error: function (data) {
                            spop({
                                template: '请求失败，请重试',
                                autoclose: spopTimeOut,
                                style: 'error',
                                group: 'deleting'
                            });
                        },
                    });
                }
            },
            {
                text: '强制删除', action: function (e) {
                    e.preventDefault();

                    Swal.fire({
                        title: '确定删除吗？',
                        html: $('<div>')
                            .text('强制删除会屏蔽错误信息以实现删除'),
                        type: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: '删除',
                        animation: true,
                        customClass: 'animated tada'
                    }).then(function (isConfirm) {
                        if (isConfirm.value) {
                            spop({
                                template: '正在删除中',
                                group: 'deleting',
                                style: 'info',
                            });
                            $.ajax({
                                url: '/ajax/UserImages/deleteImage/?id=' + data.id + '&force=1',
                                type: 'get',
                                success: function (data) {
                                    if (data.code === 200) {
                                        spop({
                                            template: data.msg,
                                            autoclose: spopTimeOut,
                                            style: 'success',
                                            group: 'deleting'
                                        });
                                        $item.remove();
                                    } else {
                                        spop({
                                            template: data.msg,
                                            autoclose: spopTimeOut,
                                            style: 'error',
                                            group: 'deleting'
                                        });
                                    }
                                },
                                error: function (data) {
                                    spop({
                                        template: '请求失败，请重试',
                                        autoclose: spopTimeOut,
                                        style: 'error',
                                        group: 'deleting'
                                    });
                                },
                            });
                        }
                        Swal.close();
                    });
                }
            },
            {
                text: '属性', action: function (e) {
                    e.preventDefault();
                    $("#imageAttributeLabel").text(data.filename);
                    $("#imageDistribute").text(data.distribute);
                    var qrcode = '/api/qrcode?text=' + data.distribute;
                    $(".qr-code").attr("src", qrcode);
                    $("#fileSize").text(change_filesize(data.file_size));
                    $("#imageType").text(data.mime);
                    $("#imageCreateTime").text(date_chage(data.create_time));
                    $("#imagemd5").text(data.md5);
                    $("#imageUrlOpen").data('url', data.info);
                    $("#imageUrlDelete").data('id', data.id);
                    $('#imageUrlCopy').attr('data-clipboard-text', data.distribute);
                    $('#imageUrlCopyUrl').attr('data-clipboard-text', data.distribute);
                    $('#imageAttribute').modal();
                }
            }
        ]);
    });
}

//加载文件夹信息
function loadFoldersMethod() {
    if (folderExtend === true) return;
    //双击打开文件夹
    $('.Hidove-folders').dblclick(function () {

        var title = $(this).find('.caption>p').text();
        var id = $(this).data('id');
        var parent_id = $(this).data('parent_id');
        var html = `<li class="breadcrumb-item active" onclick="loadMethod(this);" data-id="` + id + `" data-parent_id="` + parent_id + `"><a>` + title + `</a></li>`;
        $('.Hidove-breadcrumb>.breadcrumb>li').removeClass('active');
        $('.Hidove-breadcrumb>.breadcrumb').append(html);
        // $('#folders').text('');
        $('#images').text('');
        id = $(this).data('id');
        //重载
        Render(id)
    });
    $('.Hidove-folders').on('contextmenu', function (e) {
        var $item = $(this);
        var data = JSON.parse($.base64.decode($item.data('json')));
        context.attach('.Hidove-folders', [
            {header: 'Compressed Menu'},
            {
                text: '打开', action: function (e) {
                    e.preventDefault();
                    $item.find('img').dblclick();
                }
            },
            {
                text: '重命名', action: function (e) {
                    e.preventDefault();
                    $("#folderName").val(data.name);
                    $("#folderName").data('id', data.id);
                    $("#folderName").data('parent_id', data.parent_id);
                    $('#folder-rename').modal();
                }
            },
            {divider: true},
            {
                text: '删除', action: function (e) {
                    e.preventDefault();
                    Swal.fire({
                        type: 'warning', // 弹框类型
                        title: '删除文件夹', //标题
                        text: "Are you sure？文件夹内图片将全部删除！", //显示内容
                        confirmButtonText: '确定',// 确定按钮的 文字
                        showCancelButton: true, // 是否显示取消按钮
                        cancelButtonText: "取消", // 取消按钮的 文字
                        focusCancel: true, // 是否聚焦 取消按钮
                        reverseButtons: true  // 是否 反转 两个按钮的位置 默认是  左边 确定  右边 取消
                    }).then((isConfirm) => {
                        if (isConfirm.value) {
                            $.ajax({
                                url: '/ajax/UserImages/deleteFolder/?id=' + data.id,
                                type: 'get',
                                success: function (data) {
                                    if (data.code == 200) {
                                        spop({
                                            template: '删除成功',
                                            autoclose: spopTimeOut,
                                            style: 'success'
                                        });
                                        $item.remove();
                                    } else {
                                        spop({
                                            template: data.msg,
                                            autoclose: spopTimeOut,
                                            style: 'error'
                                        });
                                    }
                                },
                                error: function (data) {
                                    spop({
                                        template: '请求失败，请重试',
                                        autoclose: spopTimeOut,
                                        style: 'error'
                                    });
                                },
                            });
                        } else {
                            Swal.close();
                        }
                    });
                }
            }
        ]);
    });
    folderExtend = true;
}

function loadMethod(ele) {
    var $item = $(ele);
    $item.nextAll().remove();
    var id = $item.data('id');
    Render(id);
}

//重载移动文件列表
function RenderMoveList(id) {
    var MoveListAjax = $.ajax({
        url: '/ajax/UserImages/folders/?id=' + id,
        type: 'get',
        success: function (data) {
            if (data.code === 200) {
                var folders = data.data;
                var html = '';
                folders.forEach(function (value, index) {
                    html = html + `<button class="btn btn-outline-secondary" onclick="RenderMove(this);" data-id="` + value.id + `" data-id="` + value.parent_id + `" data-json="` + $.base64.encode(value) + `" type="button" class="list-group-item">` + value.name + `</button>`;
                });
                $('#Hidove-folder-list').html(html);
            }
        },
        error: function (data) {
            spop({
                template: '请求失败，请重试',
                autoclose: spopTimeOut,
                style: 'error'
            });
        },
    });
}

function RenderMove(obj) {
    var id = $(obj).data('id');
    var title = $(obj).text();
    var html = `<li class="breadcrumb-item active" onclick="loadMoveListBreadcrumb(this);" data-id="` + id + `"><a>` + title + `</a></li>`;
    $('.Hidove-move-breadcrumb>.breadcrumb li').removeClass('active');
    $('.Hidove-move-breadcrumb>.breadcrumb').append(html);
    loadMoveListBreadcrumb(obj);
}

function loadMoveListBreadcrumb(ele) {
    var $item = $(ele);
    $item.nextAll().remove();
    var id = $item.data('id');
    RenderMoveList(id);
}
