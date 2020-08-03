//全选
var clip = function (el) {
    var range = document.createRange();
    range.selectNodeContents(el);
    var sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);
};

//文件格式转换
function change_filesize(filesize) {

    filesize = parseInt(filesize);
    var size = "";
    if (filesize === 0) {
        size = "0.00 B"
    } else if (filesize < 0.1 * 1024) {                            //小于0.1KB，则转化成B
        size = filesize.toFixed(2) + " B"
    } else if (filesize < 0.1 * 1024 * 1024) {            //小于0.1MB，则转化成KB
        size = (filesize / 1024).toFixed(2) + " KB"
    } else if (filesize < 0.1 * 1024 * 1024 * 1024) {        //小于0.1GB，则转化成MB
        size = (filesize / (1024 * 1024)).toFixed(2) + " MB"
    } else {                                            //其他转化成GB
        size = (filesize / (1024 * 1024 * 1024)).toFixed(2) + " GB"
    }
    return size;
}

//日期转换
function date_chage(timestamp) {
    var date = new Date(timestamp * 1000);
    Y = date.getFullYear() + '-';
    M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
    D = (date.getDate() + 1 < 10 ? '0' + date.getDate() : date.getDate()) + ' ';
    h = (date.getHours() + 1 < 10 ? '0' + date.getHours() : date.getHours()) + ':';
    m = (date.getMinutes() + 1 < 10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
    s = (date.getSeconds() + 1 < 10 ? '0' + date.getSeconds() : date.getSeconds());
    return Y + M + D + h + m + s;
}

// JSON 格式化
function json_format(txt, compress) {
    var indentChar = '    ';
    if (/^\s*$/.test(txt)) {
        console.log('数据为空,无法格式化! ');
        return;
    }
    try {
        var data = eval('(' + txt + ')');
    } catch (e) {
        console.log('数据源语法错误,格式化失败! 错误信息: ' + e.description, 'err');
        return;
    }
    var draw = [],
        last = false,
        This = this,
        line = compress ? '' : '\n',
        nodeCount = 0,
        maxDepth = 0;

    var notify = function (name, value, isLast, indent, formObj) {
        nodeCount++; /*节点计数*/
        for (var i = 0, tab = ''; i < indent; i++)
            tab += indentChar; /* 缩进HTML */
        tab = compress ? '' : tab; /*压缩模式忽略缩进*/
        maxDepth = ++indent; /*缩进递增并记录*/
        if (value && value.constructor == Array) {
            /*处理数组*/
            draw.push(
                tab + (formObj ? '"' + name + '":' : '') + '[' + line
            ); /*缩进'[' 然后换行*/
            for (var i = 0; i < value.length; i++)
                notify(i, value[i], i == value.length - 1, indent, false);
            draw.push(
                tab + ']' + (isLast ? line : ',' + line)
            ); /*缩进']'换行,若非尾元素则添加逗号*/
        } else if (value && typeof value == 'object') {
            /*处理对象*/
            draw.push(
                tab + (formObj ? '"' + name + '":' : '') + '{' + line
            ); /*缩进'{' 然后换行*/
            var len = 0,
                i = 0;
            for (var key in value)
                len++;
            for (var key in value)
                notify(key, value[key], ++i == len, indent, true);
            draw.push(
                tab + '}' + (isLast ? line : ',' + line)
            ); /*缩进'}'换行,若非尾元素则添加逗号*/
        } else {
            if (typeof value == 'string') value = '"' + value + '"';
            draw.push(
                tab +
                (formObj ? '"' + name + '":' : '') +
                value +
                (isLast ? '' : ',') +
                line
            );
        }
    };
    var isLast = true,
        indent = 0;
    notify('', data, isLast, indent, false);
    return draw.join('');
}

// 取get参数
function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg); //匹配目标参数
    if (r != null) return unescape(r[2]);
    return null; //返回参数值
}

/**
 * 格式化秒
 * @param int  value 总秒数
 * @return string result 格式化后的字符串
 */
function formatSeconds(value) {
    var theTime = parseInt(value);// 需要转换的时间秒
    var theTime1 = 0;// 分
    var theTime2 = 0;// 小时
    var theTime3 = 0;// 天
    if (theTime > 60) {
        theTime1 = parseInt(theTime / 60);
        theTime = parseInt(theTime % 60);
        if (theTime1 > 60) {
            theTime2 = parseInt(theTime1 / 60);
            theTime1 = parseInt(theTime1 % 60);
            if (theTime2 > 24) {
                //大于24小时
                theTime3 = parseInt(theTime2 / 24);
                theTime2 = parseInt(theTime2 % 24);
            }
        }
    }
    var result = '';
    if (theTime > 0) {
        result = "" + parseInt(theTime) + "秒";
    }
    if (theTime1 > 0) {
        result = "" + parseInt(theTime1) + "分" + result;
    }
    if (theTime2 > 0) {
        result = "" + parseInt(theTime2) + "小时" + result;
    }
    if (theTime3 > 0) {
        result = "" + parseInt(theTime3) + "天" + result;
    }
    return result;
}

//lazyload
function lazyload(obj) {
    $(obj).lazyload({
        placeholder: "/static/index/images/loading.gif", //用图片提前占位
        // placeholder,值为某一图片路径.此图片用来占据将要加载的图片的位置,待图片加载时,占位图则会隐藏
        effect: "fadeIn", // 载入使用何种效果
        // effect(特效),值有show(直接显示),fadeIn(淡入),slideDown(下拉)等,常用fadeIn
        threshold: 200, // 提前开始加载
        // threshold,值为数字,代表页面高度.如设置为200,表示滚动条在离目标位置还有200的高度时就开始加载图片,可以做到不让用户察觉
        event: 'click',  // 事件触发时才加载
        // event,值有click(点击),mouseover(鼠标划过),sporty(运动的),foobar(…).可以实现鼠标莫过或点击图片才开始加载,后两个值未测试…
        // container: $("#images"),  // 对某容器中的图片实现效果
        // container,值为某容器.lazyload默认在拉动浏览器滚动条时生效,这个参数可以让你在拉动某DIV的滚动条时依次加载其中的图片
        failurelimit: 10 // 图片排序混乱时
        // failurelimit,值为数字.lazyload默认在找到第一张不在可见区域里的图片时则不再继续加载,但当HTML容器混乱的时候可能出现可见区域内图片并没加载出来的情况,failurelimit意在加载N张可见区域外的图片,以避免出现这个问题.
    });
}

// 取得cookie
function getCookie(name) {
    var nameEQ = name + '='
    var ca = document.cookie.split(';') // 把cookie分割成组
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i] // 取得字符串
        while (c.charAt(0) == ' ') { // 判断一下字符串有没有前导空格
            c = c.substring(1, c.length) // 有的话，从第二位开始取
        }
        if (c.indexOf(nameEQ) == 0) { // 如果含有我们要的name
            return unescape(c.substring(nameEQ.length, c.length)) // 解码并截取我们要值
        }
    }
    return false
}

// 清除cookie
function clearCookie(name) {
    setCookie(name, "", -1);
}

// 设置cookie
function setCookie(name, value, seconds) {
    seconds = seconds || 0;   //seconds有值就直接赋值，没有为0，这个根php不一样。
    var expires = "";
    if (seconds != 0) {      //设置cookie生存时间
        var date = new Date();
        date.setTime(date.getTime() + (seconds * 1000));
        expires = "; expires=" + date.toGMTString();
    }
    document.cookie = name + "=" + escape(value) + expires + "; path=/";   //转码并赋值
}

function refreshCaptcha(src) {
    return src + '?v=' + new Date().getTime();
}

function is_web_upload(web_upload_key) {
    var _str = web_upload_key
    var staticchars = 'elr5vCgGnQ9pMqJxVcwdoh2KDPjAHbafRtSU61iZkTON0s84YXImuy7zELBF3W';
    var encodechars = "";
    for (var i = 0; i < _str.length; i++) {
        var num0 = staticchars.indexOf(_str[i]);
        if (num0 == -1) {
            var code = _str[i];
        } else {
            var code = staticchars[(num0 + 3) % 62];
        }
        var num1 = parseInt(Math.random() * 62, 10);
        var num2 = parseInt(Math.random() * 62, 10);
        encodechars += staticchars[num1] + code + staticchars[num2];
    }
    return encodechars;
}