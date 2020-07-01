;layui.define(function (e) {
    var i = (layui.$, layui.layer, layui.laytpl, layui.setter, layui.view, layui.admin);
    i.events.logout = function () {
        i.req({
            url: "./json/user/logout.js", type: "get", data: {}, done: function (e) {
                i.exit()
            }
        })
    }, e("common", {})
});

function redirect(param) {
    window.open(authPath + "/" + param, '_blank');
}

/**
 * 表单批量赋值
 * @param obj obj 对象
 * @param key 键
 * @returns {{}}
 */
var ergodic = function (obj, key) {
    let data = {};
    for (let i in obj) {
        if (obj[i]['value'] ==='0'){
            data[obj[i]['name']] =0;
        }else{
            data[obj[i]['name']] = obj[i]['value'];
        }
    }
    return data;
};
var ergodic2 = function (obj, key) {
    let data = {};
    for (let i in obj) {
        let k = i;
        if (key != "") {
            k = key + "[" + i + "]";
        }
        if (typeof obj[i] == 'object') {
            let tmp = ergodic(obj[i], k);
            for (let index in tmp) {
                data[index] = tmp[index];
            }
        } else {
            if (obj[i] == 'true') {
                data[k] = true;
            } else if (obj[i] == 'false') {
                data[k] = false;
            } else {
                data[k] = obj[i];
            }
        }
    }
    return data;
};