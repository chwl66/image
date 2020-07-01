var percentage = Number(capacityUsed)/Number(capacity) * 100;
percentage = percentage.toFixed(2);
$('#capacityProgress').attr('aria-valuenow',capacityUsed);
$('#capacityProgress').attr('aria-valuemax',capacity);
$('#capacityProgress').css('width',percentage+'%');
$('#capacityProgress span').text(percentage+'%');
$('#capacityPercentage').text(change_filesize(capacityUsed)+' / '+change_filesize(capacity));
$('#capacity').text(change_filesize(capacity));
$('#resetToken').click(function () {
    Swal.fire({
        type: 'warning', // 弹框类型
        title: '重置Token', //标题
        text: "是否重置Token？", //显示内容
        confirmButtonText: '确定',// 确定按钮的 文字
        showCancelButton: true, // 是否显示取消按钮
        cancelButtonText: "取消", // 取消按钮的 文字
        focusCancel: true, // 是否聚焦 取消按钮
        // reverseButtons: true  // 是否 反转 两个按钮的位置 默认是  左边 确定  右边 取消
    }).then(function (isConfirm) {
        if (isConfirm.value) {
            $.ajax({
                url:'/ajax/user/update?token=1',
                type:'get',
                success:function (data) {
                    if (data.code == 200){
                        spop({
                            template: 'Token重置成功！',
                            autoclose: spopTimeOut,
                            style: 'success'
                        });
                        $('#HidoveToken').text(data.data.token);
                    }else {
                        spop({
                            template: data.msg,
                            autoclose: spopTimeOut,
                            style: 'error'
                        });
                    }
                },
                error:function (data) {
                    spop({
                        template: '请求失败，请重试',
                        autoclose: spopTimeOut,
                        style: 'error'
                    });
                },
            });
        }
        else {
            Swal.close();
        }
    });
});
$('#resetPassword').click(function () {
    Swal.fire({
        title: 'Please enter your new password',
        input: 'text',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Save',
        showLoaderOnConfirm: true,
        allowOutsideClick: true,
        inputValidator: function(value) {
            if(!value) {
                spop({
                    template: '密码不得为空',
                    autoclose: spopTimeOut,
                    style: 'error'
                });
                Swal.clickCancel();
                return;
            }
        },
        preConfirm:function (value) {
            $.ajax({
                url:'/ajax/user/update',
                type:'post',
                data:{'password':value},
                dataType:'json',
                success:function (data) {
                    if (data.code == 200){
                        spop({
                            template: data.msg,
                            autoclose: spopTimeOut,
                            style: 'success'
                        });
                    }else {
                        spop({
                            template: data.msg,
                            autoclose: spopTimeOut,
                            style: 'error'
                        });
                    }
                },
                error:function (data) {
                    spop({
                        template: '请求失败，请重试',
                        autoclose: spopTimeOut,
                        style: 'error'
                    });
                },
            });
        },
    });
});
$('#resetEmail').click(function () {
    Swal.fire({
        title: 'Please enter your new email address',
        input: 'text',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Save',
        showLoaderOnConfirm: true,
        allowOutsideClick: true,
        inputValidator: function(value) {
            if(!value) {
                spop({
                    template: '邮箱不得为空',
                    autoclose: spopTimeOut,
                    style: 'error'
                });
                Swal.clickCancel();
                return;
            }
        },
        preConfirm: function (value) {
            $.ajax({
                url:'/ajax/user/update',
                type:'post',
                data:{'email':value},
                dataType:'json',
                success:function (response) {
                    if (response.code == 200){
                        spop({
                            template: '更新成功',
                            autoclose: spopTimeOut,
                            style: 'success'
                        });
                        $('#HidoveEmail').text(response.data.email);
                    }else {
                        spop({
                            template: response.msg,
                            autoclose: spopTimeOut,
                            style: 'error'
                        });
                    }
                },
                error:function (data) {
                    spop({
                        template: '请求失败，请重试',
                        autoclose: spopTimeOut,
                        style: 'error'
                    });
                },
            });
        },
    });
});
$('#resetApiFolder').click(function () {
    Swal.fire({
        title: 'Please enter your new folder name',
        input: 'text',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Save',
        showLoaderOnConfirm: true,
        allowOutsideClick: true,
        preConfirm: function (value) {
            $.ajax({
                url:'/ajax/user/updateApiFolder',
                type:'post',
                data:{'folderName':value},
                dataType:'json',
                success:function (response) {
                    if (response.code == 200){
                        spop({
                            template: '更新成功',
                            autoclose: spopTimeOut,
                            style: 'success'
                        });
                        $('#apiFolder').text(response.data.name);
                    }else {
                        spop({
                            template: response.msg,
                            autoclose: spopTimeOut,
                            style: 'error'
                        });
                    }
                },
                error:function (data) {
                    spop({
                        template: '请求失败，请重试',
                        autoclose: spopTimeOut,
                        style: 'error'
                    });
                },
            });
        },
    });
});
$('#isprivate').click(function () {
    var status = $('#isprivate').text()
    if(status === '开启'){
        status = 1;
    }else {
        status = 0;
    }
    $.ajax({
        url:'/ajax/user/update',
        type:'post',
        data:{is_private:status},
        success:function (data) {
            if (data.code == 200){
                spop({
                    template: '私密设置更新成功',
                    autoclose: spopTimeOut,
                    style: 'success'
                });
                if (data.data.is_private == 1){
                    $('#isprivateStatus').text('已开启');
                    $('#isprivate').text('关闭');
                }else {
                    $('#isprivateStatus').text('已关闭');
                    $('#isprivate').text('开启');
                }
            }else {
                spop({
                    template: data.msg,
                    autoclose: spopTimeOut,
                    style: 'error'
                });
            }
        },
        error:function (data) {
            spop({
                template: '请求失败，请重试',
                autoclose: spopTimeOut,
                style: 'error'
            });
        },
    });
});