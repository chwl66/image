var app = new Vue({
    el: '#app',
    data: {
        imgTempList: [], //图片临时路径列表
        isUploading: false, //是否正在上传
        successData: [], //上传成功后的路径
    },
    mounted: function () {
        var that = this;
        //监听粘贴板
        document.addEventListener('paste', function (event) {
            let isChrome = false;
            //判断图片数量是否已上限
            let currentImgTempArray = that.imgTempList;
            if (currentImgTempArray.length >= maxFileCount) {
                alert("最多上传" + maxFileCount + "张图片");
                return false;
            }
            if (event.clipboardData || event.originalEvent) {
                let clipboardData = (event.clipboardData || event.originalEvent.clipboardData);
                if (clipboardData.items) {
                    // for chrome
                    let items = clipboardData.items,
                        len = items.length,
                        blob = null;
                    isChrome = true;
                    event.preventDefault();
                    for (let i = 0; i < len; i++) {
                        if (items[i].type.indexOf("image") !== -1) {
                            blob = items[i].getAsFile();
                            let reader = new FileReader();
                            reader.readAsDataURL(blob); //将读取到的文件编码成Data URL
                            reader.onload = function () { //读取完成时
                                //调用图片压缩处理方法
                                that.compressedImage({
                                    src: reader.result,
                                    quality: 0.8,
                                    success: function (src) {
                                        //将压缩后的路径 追加到临时路径数组中
                                        let data = {
                                            src: src,
                                            fileName: blob.name
                                        };
                                        that.imgTempList.push(data);
                                    }
                                });
                                new Hidovetoast().toast('已添加到上传列表', 1500);
                            };
                        } else if (items[i].type === 'text/plain') {
                            items[i].getAsString(function (url) {
                                // new Hidovetoast().toast('读取图片中', 100);
                                axios({
                                    method: "get",
                                    url: '/ajax/imagePreview?url=' + url,
                                    responseType: 'blob'
                                }).then(function (res) {
                                    const arr = url.split('/');
                                    var fileName = arr.pop();
                                    let blob = res.data;
                                    if (fileName === '' || blob.type.lastIndexOf('image') < 0) {
                                        throw '非法链接，注意！仅支持图片链接';
                                    }
                                    const index = fileName.lastIndexOf('.');
                                    if (index === -1)
                                        fileName = fileName + '.jpg';
                                    blob.name = fileName;
                                    let reader = new FileReader();
                                    reader.readAsDataURL(blob); //将读取到的文件编码成Data URL
                                    reader.onload = function () { //读取完成时
                                        //调用图片压缩处理方法
                                        that.compressedImage({
                                            src: reader.result,
                                            quality: 0.8,
                                            success: function (src) {
                                                //将压缩后的路径 追加到临时路径数组中
                                                let data = {
                                                    src: src,
                                                    fileName: blob.name
                                                };
                                                that.imgTempList.push(data);
                                                new Hidovetoast().toast('已添加到上传列表', 1500);
                                            }
                                        });
                                    };
                                }).catch(function (error) {
                                    alert('非法链接，注意！仅支持图片链接');
                                    console.error(error);
                                });
                            })
                        }

                    }
                } else {
                    //for firefox
                }
            } else {
            }
        });
    },
    watch: {},
    methods: {
        //选择图片
        onChooseImage: function (event) {
            var that = this;

            //判断图片数量是否已上限
            var currentImgTempArray = that.imgTempList;
            if (currentImgTempArray.length >= maxFileCount) {
                alert("最多上传" + maxFileCount + "张图片");
                return false;
            }
            //使用FileReader对文件对象进行操作
            for (let i in event.target.files) {
                if (typeof event.target.files[i] != "object") {
                    continue;
                }
                let reader = new FileReader();

                reader.readAsDataURL(event.target.files[i]); //将读取到的文件编码成Data URL
                reader.onload = function () { //读取完成时
                    var replaceSrc = reader.result; //文件输出的内容
                    //调用图片压缩处理方法
                    if(compressor){
                        that.compressedImage({
                            src: replaceSrc,
                            quality: 0.8,
                            success: function (src) {
                                //将压缩后的路径 追加到临时路径数组中
                                let data = {
                                    src: src,
                                    fileName: event.target.files[i]['name']
                                };
                                that.imgTempList.push(data);
                            }
                        });
                    }else{
                        that.imgTempList.push({
                            src:replaceSrc,
                            fileName:event.target.files[i]['name'],
                        });
                    }
                };
            }
        },

        //删除某张图片
        deleteImg: function (idx) {
            var that = this;
            that.imgTempList.splice(idx, 1);
        },


        //提交上传图片
        onUploadImg: function () {
            var that = this;
            var imgTempList = that.imgTempList;
            if (imgTempList.length > 0) {

                that.isUploading = true; //正在上传 显示遮罩层 防止连续点击

                var countNum = 0; //计算数量用的 判断上传到第几张图片了
                //map循环遍历上传图片
                imgTempList.map(function (imgItem, imgIndex) {
                    var files = that.dataURLtoFile(imgItem.src, imgItem
                        .fileName); //DataURL转File

                    //创FormData对象
                    var formdata = new FormData();
                    //append(key,value)在数据末尾追加数据。 这儿的key值需要和后台定义保持一致
                    formdata.append('image', files);
                    formdata.append('apiType', apiType);
                    //用axios上传，
                    axios({
                        method: "POST",
                        url: "/api/upload/upload",
                        data: formdata,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    }).then(function (res) {
                        countNum++;
                        //图片全部上传完后去掉遮罩层
                        if (countNum >= imgTempList.length) {
                            that.isUploading = false;
                        }
                        if (res.data.code !== 200) {
                            new Hidovetoast().toast(res.data.msg, 1500);
                            return;
                        }
                        //处理successData 👇
                        var list = [];
                        let imageData = {
                            src: imgItem.src,
                            url: res.data.data.url.distribute,
                            fileName: imgItem.fileName,
                        };
                        if (that.successData.length > 0) {
                            list = that.successData.concat(imageData);
                        } else {
                            list[0] = imageData;
                        }
                        that.successData = list;
                        for (const key in imgTempList) {
                            if (imgItem.src === imgTempList[key]['src']) {
                                imgTempList.splice(key, 1);
                            }
                        }
                        that.imgTempList = imgTempList;
                    }).catch(function (error) {
                        console.error(error);
                    });
                });
            }
        },

        /**
         * 压缩图片处理
         * @src 需要压缩的图片base64路径
         * @quality 图片质量 0-1，默认1
         * @success()  成功后的回调
         * */
        compressedImage: function (params) {
            var that = this;
            var initParams = {
                src: params.src || "",
                quality: params.quality || 1,
            };

            var image = new Image();
            image.src = initParams.src;
            image.onload = function () {
                //获取图片初始宽高
                var width = image.width;
                var height = image.height;
                //判断图片宽度，再按比例设置宽度和高度的值
                if (width > 1024) {
                    width = 1024;
                    height = Math.ceil(1024 * (image.height / image.width));
                }

                //将图片重新画入canvas中
                var canvas = document.getElementById("compressCanvas");
                if (!canvas) { //如果没有压缩用的canvas 就创建一个canvas画布
                    var body = document.body;
                    canvas = document.createElement("canvas"); //创建canvas标签
                    canvas.id = "compressCanvas"; //给外层容器添加一个id
                    canvas.style.position = "fixed";
                    canvas.style.zIndex = "-1";
                    canvas.style.opacity = "0";
                    canvas.style.top = "-100%";
                    canvas.style.left = "-100%";
                    body.append(canvas);
                }

                var context = canvas.getContext("2d");
                canvas.width = width;
                canvas.height = height;
                context.beginPath();
                context.fillStyle = "#ffffff";
                context.fillRect(0, 0, width, height);
                context.fill();
                context.closePath();
                context.drawImage(image, 0, 0, width, height);
                var replaceSrc = canvas.toDataURL("image/jpeg", initParams
                    .quality); //canvas转DataURL(base64格式)

                params.success && params.success(replaceSrc);
            };
        },

        /**
         * 将base64转换为文件
         * @dataUrl base64路径地址
         * @fileName 自定义文件名字
         * */
        dataURLtoFile: function (dataUrl, fileName) {
            var arr = dataUrl.split(','),
                mime = arr[0].match(/:(.*?);/)[1],
                bstr = atob(arr[1]),
                n = bstr.length,
                u8arr = new Uint8Array(n);
            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }
            return new File([u8arr], fileName, {
                type: mime
            });
        },
        removeUrlItem: function (index) {
            var that = this;
            that.successData.splice(index, 1);
        },
        copy: function (text) {
            let oInput = document.createElement('textarea');
            oInput.value = text;
            document.body.appendChild(oInput);
            oInput.select(); // 选择对象
            document.execCommand("Copy"); // 执行浏览器复制命令
            oInput.className = 'oInput';
            oInput.style.display = 'none';
            let Hidove = new Hidovetoast();
            Hidove.toast("复制成功", 1500);
            oInput.remove();
        },
        copyAll: function () {
            let successData = this.successData;
            let text = '';
            successData.map(function (item, index) {
                if (index === successData.length - 1) {
                    text += item.url;
                } else {
                    text += (item.url + '\n');
                }
            });
            this.copy(text);
        },
        changeUrl: function (type) {
            let successData = this.successData;
            let text = '';
            switch (type) {
                case 'markdown':
                    successData.map(function (item, index) {
                        if (index === successData.length - 1) {
                            text += ('![' + item.fileName + '](' + item.url + ')');
                        } else {
                            text += ('![' + item.fileName + '](' + item.url + ')\n');
                        }
                    });
                    break;
                default:
                    text = 'text2';
            }
            this.copy(text);
        }

    }
});

