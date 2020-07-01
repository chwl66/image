layui.define(function (exports) {
    //区块轮播切换
    layui.use(['admin', 'carousel'], function () {
        var $ = layui.$
            , carousel = layui.carousel
            , element = layui.element
            , device = layui.device();

        //轮播切换
        $('.layadmin-carousel').each(function () {
            var othis = $(this);
            carousel.render({
                elem: this
                , width: '100%'
                , arrow: 'none'
                , interval: othis.data('interval')
                , autoplay: othis.data('autoplay') === true
                , trigger: (device.ios || device.android) ? 'click' : 'hover'
                , anim: othis.data('anim')
            });
        });

        element.render('progress');

    });

    //数据概览
    layui.use(['carousel', 'echarts'], function () {
        var $ = layui.$
            , carousel = layui.carousel
            , echarts = layui.echarts;
        //今日上传图片数据
        var getTodayUserPictureUpload = {};
        //本周上传图片数据
        var getWeeklyPictureUpload = {};
        //标准条形图
        var echartajax1 = $.ajax({
            url: ajaxPath + '/statistics/getWeeklyPictureUpload',
            type: 'get',
            // async: false,
            success: function (response) {
                let data = response.data;

                let date = [];//日期
                let count = [];
                date = Object.keys(data);
                count = Object.values(data);
                getWeeklyPictureUpload = {
                    title: {
                        text: '最近一周上传的图片数',
                        x: 'center',
                        textStyle: {
                            fontSize: 14
                        }
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: ['', '']
                    },
                    xAxis: [{
                        type: 'category',
                        boundaryGap: false,
                        data: date
                    }],
                    yAxis: [{
                        type: 'value'
                    }],
                    series: [{
                        name: '上传图片数',
                        type: 'line',
                        smooth: true,
                        itemStyle: {normal: {areaStyle: {type: 'default'}}},
                        data: count
                    }]
                };
            },
        });
        var echartajax2 = $.ajax({
            url: ajaxPath + '/statistics/getTodayUserPictureUpload',
            type: 'get',
            success: function (response) {
                var data = response.data;
                var username = [];//用户名
                var todayTotal = [];
                var yesterdayTotal = [];
                for (let key in data) {
                    username.push(key);
                    yesterdayTotal.push(data[key]['yesterday']);
                    todayTotal.push(data[key]['today']);
                }
                getTodayUserPictureUpload = {
                    title: {
                        text: '今日上传图片统计',
                        subtext: '数据来自云计算'
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: ['今日', '昨日']
                    },
                    calculable: true,
                    xAxis: [
                        {
                            type: 'value',
                            boundaryGap: [0, 0.01]
                        }
                    ],
                    yAxis: [
                        {
                            type: 'category',
                            data: username
                        }
                    ],
                    series: [
                        {
                            name: '昨日',
                            type: 'bar',
                            data: yesterdayTotal
                        },
                        {
                            name: '今日',
                            type: 'bar',
                            data: todayTotal
                        }
                    ]
                };
            },
        });
        $.when(echartajax1, echartajax2).done(function () {
            //渲染 echart
            var echartsApp = [], options = [getWeeklyPictureUpload, getTodayUserPictureUpload]
                , elemDataView = $('#LAY-index-dataview').children('div')
                , renderDataView = function (index) {
                echartsApp[index] = echarts.init(elemDataView[index], layui.echartsTheme);
                echartsApp[index].setOption(options[index]);
                window.onresize = echartsApp[index].resize;
            };
            //没找到DOM，终止执行
            if (!elemDataView[0]) return;
            renderDataView(0);

            //监听数据概览轮播
            var carouselIndex = 0;
            carousel.on('change(LAY-index-dataview)', function (obj) {
                renderDataView(carouselIndex = obj.index);
            });

            //监听侧边伸缩
            layui.admin.on('side', function () {
                setTimeout(function () {
                    renderDataView(carouselIndex);
                }, 300);
            });

            //监听路由
            layui.admin.on('hash(tab)', function () {
                layui.router().path.join('') || renderDataView(carouselIndex);
            });
        });

        layui.use('laytpl', function () {
            $.ajax({
                url: ajaxPath + '/statistics/getTotalInformation',
                type: 'get',
                success: function (response) {
                    var laytpl = layui.laytpl;
                    var data = {
                        '图片总数': response.data.totalImages,
                        '图片总容量': change_filesize(response.data.sumImageSize),
                        '用户总数': response.data.totalUsers,
                        '可疑图片总数': response.data.totalSuspiciousImages,
                    };
                    var getTpl = document.getElementById('getTotalInformation').innerHTML
                        , view = document.getElementById('view');
                    laytpl(getTpl).render(data, function (html) {
                        view.innerHTML = html;
                    });
                },
                error: function () {
                    layer.msg('请求失败', {icon: 2});
                }
            });
        });
    });
    exports('console', {})
});