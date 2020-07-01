/**
 * Hidove Ivey
 * wwww.hidove.cn
 * 2020年5月20日17:37:27
 */
layui.define(["form"],
    function (obj) {
        let form = layui.form;
        let interval = setInterval(function () {
            let set = form.val("Hidove-set");
            let node = set["system.loadBalance.node"];
            let split = node.split('\n');
            if ( node === '') {
                return;
            }
            clearInterval(interval);
            var html = '';
            split.forEach(function (value, index) {
                if (value.length === 0) {
                    html += `<tr>
									   <td class="node"></td>
									   <td>当前无节点</td>
								   </tr>`;
                } else {
                    html += `<tr>
									   <td class="node">` + value + `</td>
									   <td>正在测速中</td>
								   </tr>`;
                }
            });
            $('#nodes tbody').html(html);
            $('#nodes tbody .node').each(function () {
                if ($(this).text().length != 0) {
                    ping($(this).next(), $(this).text());
                }
            });
        }, 3000);

        obj("loadBalance", {});
});
function ping(ele, url) {
    $.ping({
        url: url,
        beforePing: function () {
            $(ele).html('正在测速中');
        },
        afterPing: function (ping) {
            $(ele).html(ping + "ms");
        },
        interval: 10
    });
}

$.ping = function (option) {
    var ping, requestTime, responseTime;
    var getUrl = function (url) {    //保证url带http://
        var strReg = "^((https|http)?://){1}";
        var re = new RegExp(strReg);
        return re.test(url) ? url : "http://" + url;
    };
    $.ajax({
        url: getUrl(option.url) + '/' + (new Date()).getTime() + '.html',  //设置一个空的ajax请求
        type: 'GET',
        dataType: 'html',
        timeout: 10000,
        beforeSend: function () {
            if (option.beforePing) option.beforePing();
            requestTime = new Date().getTime();
        },
        complete: function () {
            responseTime = new Date().getTime();
            ping = Math.abs(requestTime - responseTime);
            if (option.afterPing) option.afterPing(ping);
        }
    });

    if (option.interval && option.interval > 0) {
        var interval = option.interval * 1000;
        setTimeout(function () {
            $.ping(option)
        }, interval);
//        option.interval = 0;        // 阻止多重循环
//        setInterval(function(){$.ping(option)}, interval);
    }
};