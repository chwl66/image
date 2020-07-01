$.ajax({
    url: '/ajax/uploadOption',
    type: 'get',
    async: false,
    success: function (response) {
        if (response.code === 200) {
            var data = response.data;
            $('#maxImageSize').text(change_filesize(data.maxImageSize));
            $('#maxFileCount').text(data.maxFileCount);
            fileinput(data.imageType, parseInt(data.maxImageSize / 1000), data.maxFileCount);
        } else {
            spop({
                template: '获取上传选项失败',
                autoclose: spopTimeOut,
                style: 'error',
                position: 'top-center',
            });
        }
    },
    error: function () {
    }
});

function fileinput(allowedFileExtensions, maxFileSize, maxFileCount) {
    $("#Hidove").fileinput({
        language: 'en',
        theme: "fas",
        previewFileType: "image",
        uploadUrl: '/api/upload',
        uploadExtraData: function (previewId, index) {
            let publicStorage = [];
            let privateStorage = [];
            $("#publicStorage input[name='apiType']:checked").each(function () {
                publicStorage.push($(this).val());
            });
            $("#privateStorage>label:not(.disabled)>input[name='privateStorage']:checked").each(function () {
                privateStorage.push($(this).val());
            });
            return {
                'apiType': publicStorage,
                'privateStorage': privateStorage
            };
        },
        showClose: false,
        //允许上传的图片类型
        allowedFileExtensions: allowedFileExtensions,
        //允许上传的图片最大尺寸
        maxFileSize: maxFileSize,
        //允许同时上传的最大个数
        maxFileCount: maxFileCount,
        validateInitialCount: true,
        overwriteInitial: false,
        uploadAsync: true,
        fileActionSettings: {
            showRemove: true,
            showUpload: true,
            showZoom: true,
            showDrag: true,
        },
        browseClass: "btn btn-secondary",
        browseLabel: "Select Image(s)",
        removeClass: "btn btn-danger",
        removeLabel: "Clear",
        uploadClass: "btn btn-info",
        uploadLabel: "Upload",
        dropZoneTitle: "Drag & drop files here ...<br>or<br>Copy & paste screenshots here ..."
    });
}

$('#Hidove').on("fileuploaded", function (event, data) {
    var response = data.response;
    if (response.code === 200) {
        if (response.data.url) {
            $("#showurl").show();
        }
        let imageUrl = response.data.url;
        let index = 0;
        let length = Object.keys(imageUrl).length;
        if (imageUrl['private'] !== undefined) {
            length += Object.keys(imageUrl['private']).length;
        } else {
            length += 1;
        }

        let urlcode = '';
        let htmlcode = '';
        let bbcode = '';
        let markdown = '';
        let markdownlinks = '';
        for (let key in imageUrl) {
            if (typeof (imageUrl[key]) != 'string') {
                for (let i in imageUrl[key]) {
                    index++;
                    if (index === 1) {
                        urlcode = `<tr><td class="Hidove-imageucode-cover" width="100px" rowspan="` + length + `"><img src="`
                            + imageUrl[key][i] + `"/></td></tr><tr><td><span class="Hidove-imageucode-tip">Private ` + i + `</span><input type="text" class="form-control" onfocus="this.select();" value="`
                            + imageUrl[key][i] + `"></td></tr>`;
                        htmlcode = `<tr><td class="Hidove-imageucode-cover" width="100px" rowspan="` + length + `"><img src="`
                            + imageUrl[key][i] + `"/></td></tr><tr><td><span class="Hidove-imageucode-tip">Private ` + i + `</span><input type="text" class="form-control" onfocus="this.select();" value="&lt;img src=&quot;`
                            + imageUrl[key][i] + `&quot;/&gt;"></td></tr>`;
                        bbcode = `<tr><td class="Hidove-imageucode-cover" width="100px" rowspan="` + length + `"><img src="`
                            + imageUrl[key][i] + `"/></td></tr><tr><td><span class="Hidove-imageucode-tip">Private ` + i + `</span><input type="text" class="form-control" onfocus="this.select();" value="[img]`
                            + imageUrl[key][i] + `[/img]"></td></tr>`;
                        markdown = `<tr><td class="Hidove-imageucode-cover" width="100px" rowspan="` + length + `"><img src="`
                            + imageUrl[key][i] + `"/></td></tr><tr><td><span class="Hidove-imageucode-tip">Private ` + i + `</span><input type="text" class="form-control" onfocus="this.select();" value="![](`
                            + imageUrl[key][i] + `)"></td></tr>`;
                        markdownlinks = `<tr><td class="Hidove-imageucode-cover" width="100px" rowspan="` + length + `"><img src="`
                            + imageUrl[key][i] + `"/></td></tr><tr><td><span class="Hidove-imageucode-tip">Private ` + i + `</span><input type="text" class="form-control" onfocus="this.select();" value="[![`
                            + imageUrl[key][i] + `](`
                            + imageUrl[key][i] + `)](`
                            + imageUrl[key][i] + `)"></td></tr>`;
                    } else {
                        urlcode += `<tr><td><span class="Hidove-imageucode-tip">Private ` + i + `</span><input type="text" class="form-control" onfocus="this.select();" value="` + imageUrl[key][i] + `"></td></tr>`;
                        htmlcode += `<tr><td><span class="Hidove-imageucode-tip">Private ` + i + `</span><input type="text" class="form-control" onfocus="this.select();" value="&lt;img src=&quot;` + imageUrl[key][i] + `&quot;/&gt;"></td></tr>`;
                        bbcode += `<tr><td><span class="Hidove-imageucode-tip">Private ` + i + `</span><input type="text" class="form-control" onfocus="this.select();" value="[img]` + imageUrl[key][i] + `[/img]"></td></tr>`;
                        markdown += `<tr><td><span class="Hidove-imageucode-tip">Private ` + i + `</span><input type="text" class="form-control" onfocus="this.select();" value="![](` + imageUrl[key][i] + `)"></td></tr>`;
                        markdownlinks += `<tr><td><span class="Hidove-imageucode-tip">Private ` + i + `</span><input type="text" class="form-control" onfocus="this.select();" value="[![` + imageUrl[key][i] + `](` + imageUrl[key][i] + `)](` + imageUrl[key][i] + `)"></td></tr>`;
                    }
                }
            } else {
                index++;
                if (index === 1) {
                    urlcode = `<tr><td class="Hidove-imageucode-cover" width="100px" rowspan="` + length + `"><img src="`
                        + imageUrl[key] + `"/></td></tr><tr><td><span class="Hidove-imageucode-tip">` + key + `</span><input type="text" class="form-control" onfocus="this.select();" value="`
                        + imageUrl[key] + `"></td></tr>`;
                    htmlcode = `<tr><td class="Hidove-imageucode-cover" width="100px" rowspan="` + length + `"><img src="`
                        + imageUrl[key] + `"/></td></tr><tr><td><span class="Hidove-imageucode-tip">` + key + `</span><input type="text" class="form-control" onfocus="this.select();" value="&lt;img src=&quot;`
                        + imageUrl[key] + `&quot;/&gt;"></td></tr>`;
                    bbcode = `<tr><td class="Hidove-imageucode-cover" width="100px" rowspan="` + length + `"><img src="`
                        + imageUrl[key] + `"/></td></tr><tr><td><span class="Hidove-imageucode-tip">` + key + `</span><input type="text" class="form-control" onfocus="this.select();" value="[img]`
                        + imageUrl[key] + `[/img]"></td></tr>`;
                    markdown = `<tr><td class="Hidove-imageucode-cover" width="100px" rowspan="` + length + `"><img src="`
                        + imageUrl[key] + `"/></td></tr><tr><td><span class="Hidove-imageucode-tip">` + key + `</span><input type="text" class="form-control" onfocus="this.select();" value="![](`
                        + imageUrl[key] + `)"></td></tr>`;
                    markdownlinks = `<tr><td class="Hidove-imageucode-cover" width="100px" rowspan="` + length + `"><img src="`
                        + imageUrl[key] + `"/></td></tr><tr><td><span class="Hidove-imageucode-tip">` + key + `</span><input type="text" class="form-control" onfocus="this.select();" value="[![`
                        + imageUrl[key] + `](`
                        + imageUrl[key] + `)](`
                        + imageUrl[key] + `)"></td></tr>`;
                } else {
                    urlcode += `<tr><td><span class="Hidove-imageucode-tip">` + key + `</span><input type="text" class="form-control" onfocus="this.select();" value="` + imageUrl[key] + `"></td></tr>`;
                    htmlcode += `<tr><td><span class="Hidove-imageucode-tip">` + key + `</span><input type="text" class="form-control" onfocus="this.select();" value="&lt;img src=&quot;` + imageUrl[key] + `&quot;/&gt;"></td></tr>`;
                    bbcode += `<tr><td><span class="Hidove-imageucode-tip">` + key + `</span><input type="text" class="form-control" onfocus="this.select();" value="[img]` + imageUrl[key] + `[/img]"></td></tr>`;
                    markdown += `<tr><td><span class="Hidove-imageucode-tip">` + key + `</span><input type="text" class="form-control" onfocus="this.select();" value="![](` + imageUrl[key] + `)"></td></tr>`;
                    markdownlinks += `<tr><td><span class="Hidove-imageucode-tip">` + key + `</span><input type="text" class="form-control" onfocus="this.select();" value="[![` + imageUrl[key] + `](` + imageUrl[key] + `)](` + imageUrl[key] + `)"></td></tr>`;
                }
            }
        }
        $('#urlcodes').append(urlcode);
        $('#htmlcodes').append(htmlcode);
        $('#bbcodes').append(bbcode);
        $('#markdowncodes').append(markdown);
        $('#markdowncodes2').append(markdownlinks);
    } else {
        spop({
            template: response.msg,
            autoclose: spopTimeOut,
            style: 'error',
            position: 'top-center',
        });
    }
});