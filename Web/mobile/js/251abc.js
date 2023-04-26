'use strict';

$(document).ready(function (e) {
    //链接 点击
    $('.forJsClick').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var url = $(this).data('url');
        if (url) {
            setTimeout(function () {
                window.location.href = url;

            }, 300)
        }
    });
    //弹窗关闭
    $('.footerItem').each(function (e) {

        var url = $(this).data('url');
        url = url && url.replace('./', '');
        if (window.location.href.indexOf(url) > -1) {
            $(this).addClass('on')
        }
    })
    //遮罩层可以关闭
    $('.loadingWrap').on('click', function () {
        $('#loading').addClass('hidden')
    });
    //返回
    $('#goBack').on('click', function () {
        window.history.back()
    });
    var browser = {
        versions: function () {
            var u = navigator.userAgent, app = navigator.appVersion;
            return {     //移动终端浏览器版本信息
                mobile: !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端
            };
        }(),
    };

    if (!browser.versions.mobile) {//pc
        $('body').addClass('plantPc')
    }

});