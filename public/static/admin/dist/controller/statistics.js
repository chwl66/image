layui.define(function (exports) {


    //区块轮播切换
    layui.use(['admin', 'carousel', 'laypage'], function () {
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


//折线图


    //区块轮播切换
    layui.use(['admin', 'carousel', 'laypage'], function () {
        var laypage = layui.laypage;
        //轮播切换

        //今日图片请求次数排行榜
        getTodayPictureRequest(1, 10).then((res) => {
            laypage.render({
                elem: 'pageForTodayPictureRequest'
                , count: res.data.count //数据总数
                , limit: 10
                , jump: function (obj, first) {
                    if (!first) {
                        getTodayPictureRequest(obj.curr, obj.limit);
                    }
                }
            });
        });
        //获取今日来源域名访问数排行榜
        getTodayRefereRequest(1, 10).then((res) => {
            laypage.render({
                elem: 'pageForTodayRefereRequest'
                , count: res.data.count //数据总数
                , limit: 10
                , jump: function (obj, first) {
                    if (!first) {
                        getTodayRefereRequest(obj.curr, obj.limit);
                    }
                }
            });
        });

        //获取总来源域名访问数排行榜
        getTotalRefereRequest(1, 10).then((res) => {
            laypage.render({
                elem: 'pageForTotalRefereRequest'
                , count: res.data.count //数据总数
                , limit: 10
                , jump: function (obj, first) {
                    if (!first) {
                        getTotalRefereRequest(obj.curr, obj.limit);
                    }
                }
            });
        });
    });

    layui.use(['carousel', 'echarts'], function () {
        var $ = layui.$
            , carousel = layui.carousel
            , echarts = layui.echarts;

        $.ajax({
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

                //标准折线图
                var echnormline = [], normline = [getWeeklyPictureUpload
                ]
                    , elemnormline = $('#LAY-index-normline').children('div')
                    , rendernormline = function (index) {
                    echnormline[index] = echarts.init(elemnormline[index], layui.echartsTheme);
                    echnormline[index].setOption(normline[index]);
                    window.onresize = echnormline[index].resize;
                };
                if (!elemnormline[0]) return;
                rendernormline(0);
            },
        });
        //今日用户图片上传数排行榜
        $.ajax({
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
                        text: '',//今日用户图片上传数排行榜
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
                //堆积折线图
                var echheapline = [], heapline = [getTodayUserPictureUpload
                ]
                    , elemheapline = $('#LAY-index-heapline').children('div')
                    , renderheapline = function (index) {
                    echheapline[index] = echarts.init(elemheapline[index], layui.echartsTheme);
                    echheapline[index].setOption(heapline[index]);
                    window.onresize = echheapline[index].resize;
                };
                if (!elemheapline[0]) return;
                renderheapline(0);
            },
        });
        //获取最近一周API调用情况
        $.ajax({
            url: ajaxPath + '/statistics/getWeekApiRequestInfo',
            type: 'get',
            success: function (response) {
                var data = response.data;
                var apiName = [];//API接口地址
                var series = [];
                var requestDate = [];
                for (let i in data) {
                    apiName.push(i);
                    var requestData = [];

                    for (let index in data[i]) {
                        if (requestDate.length <= 0) {
                            requestDate = Object.keys(data[i]);
                        }
                        requestData.push(data[i][index]);
                    }
                    var obj = {
                        name: i,
                        type: 'line',
                        stack: '总量',
                        data: requestData
                    };
                    series.push(obj);
                }
                var getWeekApiRequestInfo =
                    {
                        tooltip: {
                            trigger: 'axis'
                        },
                        legend: {data: apiName},
                        calculable: true,
                        xAxis: [
                            {
                                type: 'category',
                                boundaryGap: false,
                                data: requestDate
                            }
                        ],
                        yAxis: [
                            {
                                type: 'value'
                            }
                        ],
                        series: series
                    };
                var echheapline = [], heapline = [getWeekApiRequestInfo]
                    , elemheapline = $('#getWeekApiRequestInfo').children('div')
                    , renderheapline = function (index) {
                    echheapline[index] = echarts.init(elemheapline[index], layui.echartsTheme);
                    echheapline[index].setOption(heapline[index]);
                    window.onresize = echheapline[index].resize;
                };
                if (!elemheapline[0]) return;
                renderheapline(0);
            },
        });

    });


    exports('statistics', {})

});

//今日图片请求次数排行榜
function getTodayPictureRequest(page, limit) {
    return $.ajax({
        url: ajaxPath + '/statistics/getTodayPictureRequest',
        type: 'get',
        data: {
            page: page,
            limit: limit,
        },
        success: function (response) {
            var data = response.data.item;
            var html = '';
            data.forEach(function (value, index) {
                var ranking = '';
                switch (index) {
                    case 0:
                        ranking = 'first';
                        break;
                    case 1:
                        ranking = 'second';
                        break;
                    case 2:
                        ranking = 'third';
                        break;
                    default :
                        ranking = '';
                }
                html = html +
                    '            <tr>\n' +
                    '              <td><span class="' + ranking + '">' + (!value.user.username ? '游客' : value.user.username) + '</span></td>\n' +
                    '              <td><i class="layui-icon layui-icon-log"> ' + value.filename + '</i></td>\n' +
                    '              <td><span>' + value.today_request_times + '</span></td>\n' +
                    '              <td>' + value.total_request_times + ' </i></td>\n' +
                    '              <td>' + date_chage(value.final_request_time) + ' </i></td>\n' +
                    '              <td><a href="/info/' + value.signatures + '" target="_blank">查看</a></i></td>\n' +
                    '            </tr>'
            });
            $('#getTodayPictureRequest').html(html);
        },
    });
}

//获取今日来源域名访问数排行榜
function getTodayRefereRequest(page, limit) {
    return $.ajax({
        url: ajaxPath + '/statistics/getTodayRefereRequest',
        type: 'get',
        data: {
            page: page,
            limit: limit,
        },
        success: function (response) {
            var data = response.data.item;
            var html = '';
            data.forEach(function (value, index) {
                switch (index) {
                    case 0:
                        var ranking = 'first';
                        break;
                    case 1:
                        var ranking = 'second';
                        break;
                    case 2:
                        var ranking = 'third';
                        break;
                    default :
                        var ranking = '';
                }
                ;
                html = html +
                    '            <tr>\n' +
                    '              <td><span class="' + ranking + '">' + value.referer + '</span></td>\n' +
                    '              <td>' + date_chage(value.create_time) + ' </i></td>\n' +
                    '              <td>' + date_chage(value.final_request_time) + ' </i></td>\n' +
                    '              <td>' + value.ip + ' </i></td>\n' +
                    '              <td>' + value.today_request_times + ' </i></td>\n' +
                    '              <td><a href="//' + value.referer + '" target="_blank">查看</a></i></td>\n' +
                    '            </tr>'
            })
            $('#getTodayRefereRequest').html(html);
        },
    });

}

//获取总来源域名访问数排行榜
function getTotalRefereRequest(page, limit) {
    return $.ajax({
        url: ajaxPath + '/statistics/getTotalRefereRequest',
        type: 'get',
        data: {
            page: page,
            limit: limit,
        },
        success: function (response) {
            var data = response.data.item;
            var html = '';
            data.forEach(function (value, index) {
                switch (index) {
                    case 0:
                        var ranking = 'first';
                        break;
                    case 1:
                        var ranking = 'second';
                        break;
                    case 2:
                        var ranking = 'third';
                        break;
                    default :
                        var ranking = '';
                }
                ;
                html = html +
                    '            <tr>\n' +
                    '              <td><span class="' + ranking + '">' + value.referer + '</span></td>\n' +
                    '              <td>' + date_chage(value.create_time) + ' </i></td>\n' +
                    '              <td>' + date_chage(value.final_request_time) + ' </i></td>\n' +
                    '              <td>' + value.ip + ' </i></td>\n' +
                    '              <td>' + value.total_request_times + ' </i></td>\n' +
                    '              <td><a href="//' + value.referer + '" target="_blank">查看</a></i></td>\n' +
                    '            </tr>'
            })
            $('#getTotalRefereRequest').html(html);
        },
    });
}