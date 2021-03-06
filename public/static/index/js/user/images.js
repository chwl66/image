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
                template: '??????API??????????????????????????????',
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
    //???????????????????????????
    context.attach('.container', [
        {header: 'Compressed Menu'},
        {
            text: '???????????????', action: function (e) {
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
                                template: '?????????????????????',
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
                                    template: '????????????????????????',
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
            text: '??????', action: function (e) {
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
    // ??????????????????
    clipboard.on('success', function (e) {
        spop({
            template: '???????????????',
            autoclose: spopTimeOut,
            style: 'success'
        });
        e.clearSelection();
    });
    clipboard.on('error', function (e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
        spop({
            template: '???????????????',
            autoclose: spopTimeOut,
            style: 'error'
        });
    });
    //??????model?????????url??????
    var imageUrlCopy = new ClipboardJS('#imageUrlCopy', {
        container: document.getElementById('imageAttribute')
    });
    // ??????????????????
    imageUrlCopy.on('success', function (e) {
        spop({
            template: '???????????????',
            autoclose: spopTimeOut,
            style: 'success'
        });
        e.clearSelection();
    });
    imageUrlCopy.on('error', function (e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
        spop({
            template: '???????????????',
            autoclose: spopTimeOut,
            style: 'error'
        });
    });
    //??????????????????????????????
    Render(initialFolder);
}

// ????????????
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
    if (value === '??????') {
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
    } else if (value === '??????') {
        spop({
            template: '???????????????',
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
                    //?????????????????????
                    spop({
                        template: '????????????',
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
                    template: '????????????',
                    autoclose: spopTimeOut,
                    group: 'process',
                    style: 'error'
                });
            },
        });

    } else if (value === '????????????') {

        Swal.fire({
            title: '??????????????????',
            html: $('<div>')
                .text('????????????????????????????????????????????????'),
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '??????',
            animation: true,
            customClass: 'animated tada'
        }).then(function (isConfirm) {
            if (isConfirm.value) {
                spop({
                    template: '???????????????',
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
                            //?????????????????????
                            spop({
                                template: '????????????',
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
                            template: '????????????',
                            autoclose: spopTimeOut,
                            group: 'process',
                            style: 'error'
                        });
                    },
                });
            }
            Swal.close();
        });

    } else if (value === '??????') {
        Swal.fire({
            title: '<strong>Tips ?????????????????????CDN??????</strong>',
            type: 'info',
            html: $('#Hidove-apiInfo').html(),
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText:
                '??????',
            cancelButtonText:
                '??????',
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
                    template: '??????????????????',
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
                                    template: '[' + imageInfo.filename + ']???????????????',
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
                                template: '[' + imageInfo.filename + ']???????????????',
                                autoclose: spopTimeOut,
                                style: 'danger',
                            });
                        },
                    });
                    updateAjax.push(ajax);
                });
                Promise.all(updateAjax).then((results) => {
                    spop({
                        template: '???????????????',
                        autoclose: spopTimeOut,
                        style: 'success',
                        group: 'updateCdn',
                    });
                });
                $.when(updateAjax).done(function () {
                });
            }
        });
    } else if (value === '??????') {
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
//??????????????????
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
//???????????????????????????
$("#imageUrlOpen").click(function () {
    window.open($("#imageUrlOpen").data('url'));
});
//???????????????????????????
$("#imageUrlDelete").click(function () {
    spop({
        template: '???????????????',
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
                template: '????????????????????????',
                autoclose: spopTimeOut,
                style: 'error',
                group: 'deleting'
            });
        },
    });
});
//????????????
$('#selectAll').click(function () {
    if ($('#selectAll').text() === '??????') {
        $('#selectAll').text('?????????')
        $('.imageinfo').addClass('imageinfo-active');
    } else {
        $('#selectAll').text('??????')
        $('.imageinfo').removeClass('imageinfo-active');
    }
});
//??????????????????
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
                template: '????????????????????????',
                autoclose: spopTimeOut,
                style: 'error'
            });
        },
    });
});
//????????????????????????
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
                template: '????????????????????????',
                autoclose: spopTimeOut,
                style: 'error'
            });
        },
    });
});

//???????????????????????????
function Render(folderId) {
    //????????????
    if (lock === true) return;
    lock = true;
    folderExtend = false;
    initialFolder = folderId;
    status = 1;
    $('#images').text('');
    loadFolders(initialFolder);
    $.when(folderAjax).done(function () {
        //????????????
        imageSearch('', initialPage, initialLimit, initialFolder);
        lock = false;
    });
}

//???????????????????????????
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

//????????????????????????
//????????????
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
                //?????????????????????
                spop({
                    template: '????????????',
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
            //?????????????????????
            loadFoldersMethod();
        }else{
            $('.Hidove-folders').hide();
        }
        //??????????????????
        loadImageInfo();
    });
}

//??????????????????
function loadImageInfo() {
    //??????????????????
    $(".imageinfo>i").click(function () {
        var $parent = $(this).parent();
        if ($parent.hasClass('imageinfo-active')) {
            $parent.removeClass('imageinfo-active');
        } else {
            $parent.addClass('imageinfo-active');
        }
    });
    //??????fancybox???????????????
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
                text: '????????????', action: function (e) {
                    e.preventDefault();
                    $item.find('img').click();
                }
            },
            {text: '?????????????????????', href: data.info, target: '_blank'},
            {divider: true},
            {
                text: '????????????', action: function (e) {
                    e.preventDefault();
                    $('#copy-url').attr('data-clipboard-text', data.distribute).click();
                }
            },
            {
                text: '??????', action: function (e) {
                    e.preventDefault();
                    spop({
                        template: '???????????????',
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
                                template: '????????????????????????',
                                autoclose: spopTimeOut,
                                style: 'error',
                                group: 'deleting'
                            });
                        },
                    });
                }
            },
            {
                text: '????????????', action: function (e) {
                    e.preventDefault();

                    Swal.fire({
                        title: '??????????????????',
                        html: $('<div>')
                            .text('????????????????????????????????????????????????'),
                        type: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: '??????',
                        animation: true,
                        customClass: 'animated tada'
                    }).then(function (isConfirm) {
                        if (isConfirm.value) {
                            spop({
                                template: '???????????????',
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
                                        template: '????????????????????????',
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
                text: '??????', action: function (e) {
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

//?????????????????????
function loadFoldersMethod() {
    if (folderExtend === true) return;
    //?????????????????????
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
        //??????
        Render(id)
    });
    $('.Hidove-folders').on('contextmenu', function (e) {
        var $item = $(this);
        var data = JSON.parse($.base64.decode($item.data('json')));
        context.attach('.Hidove-folders', [
            {header: 'Compressed Menu'},
            {
                text: '??????', action: function (e) {
                    e.preventDefault();
                    $item.find('img').dblclick();
                }
            },
            {
                text: '?????????', action: function (e) {
                    e.preventDefault();
                    $("#folderName").val(data.name);
                    $("#folderName").data('id', data.id);
                    $("#folderName").data('parent_id', data.parent_id);
                    $('#folder-rename').modal();
                }
            },
            {divider: true},
            {
                text: '??????', action: function (e) {
                    e.preventDefault();
                    Swal.fire({
                        type: 'warning', // ????????????
                        title: '???????????????', //??????
                        text: "Are you sure???????????????????????????????????????", //????????????
                        confirmButtonText: '??????',// ??????????????? ??????
                        showCancelButton: true, // ????????????????????????
                        cancelButtonText: "??????", // ??????????????? ??????
                        focusCancel: true, // ???????????? ????????????
                        reverseButtons: true  // ?????? ?????? ????????????????????? ?????????  ?????? ??????  ?????? ??????
                    }).then((isConfirm) => {
                        if (isConfirm.value) {
                            $.ajax({
                                url: '/ajax/UserImages/deleteFolder/?id=' + data.id,
                                type: 'get',
                                success: function (data) {
                                    if (data.code == 200) {
                                        spop({
                                            template: '????????????',
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
                                        template: '????????????????????????',
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

//????????????????????????
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
                template: '????????????????????????',
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
