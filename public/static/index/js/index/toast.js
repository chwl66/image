(function (window, undefined) {
    var Hidovetoast = function () { //构造函数大驼峰命名法
    };
    Hidovetoast.prototype = { //prototype 属性使您有能力向对象添加属性和方法。
        create: function (str, duration) {
            let self = this;
            var toastHtml = '';
            var toastText = '<span class="Hidove-toast-text">' + str + '</span>';
            toastHtml = '<div class="Hidove-toast"><span class="Hidove-toast-icon"></span>' + toastText + '</div>';
            if (document.querySelector(".Hidove-toast")) return; //当还没hide时禁止重复点击
            document.body.insertAdjacentHTML('beforeend', toastHtml);
            duration = duration== null ? 2000 : ''; //如果toast没有加上时间，默认2000毫秒；
            self.show();
            setTimeout(function () {
                self.hide();
            }, duration);
        },
        show: function () {
            let self = this;
            setTimeout(() => {
                document.querySelector(".Hidove-toast").classList.add("Hidove-toast-run");
            }, 10);
        },
        hide: function () {
            var self = this;

            document.querySelector(".Hidove-toast").classList.remove("Hidove-toast-run");
            document.querySelector(".Hidove-toast").classList.add("Hidove-toast-over");
            setTimeout(() => {
                if (document.querySelector(".Hidove-toast")) {
                    document.querySelector(".Hidove-toast").parentNode.removeChild(document.querySelector(".Hidove-toast"));
                }
            }, 1000);
        },
        toast: function (str, duration) {
            var self = this;
            return self.create(str, duration);
        }
    };
    window.Hidovetoast = Hidovetoast;
}(window));