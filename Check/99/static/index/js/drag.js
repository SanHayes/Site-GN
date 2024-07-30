/**
 * Created by shuai_wy on 2017/3/14.
 */
$.fn.drag = function (options) {
    var left = 0
    var TEMP = 5
    var x, drag = this, isMove = false, defaults = {
    };
    var options = $.extend(defaults, options);
    var handler = drag.find('.handler');
    var drag_bg = drag.find('.drag_bg');
    var text = drag.find('.drag_text');
    var maxWidth = drag.width() - handler.width();  //能滑动的最大间距
    var Cookie = {
        set: function (key, value, exdays) {
            let exdate = new Date() // 获取时间
            exdate.setTime(exdate.getTime() + 24 * 60 * 60 * 1000 * exdays) // 保存的天数
            // 字符串拼接cookie
            // eslint-disable-next-line camelcase
            window.document.cookie = key + '=' + value + ';path=/;expires=' + exdate.toGMTString()
        },
        
        get: function (key) {
            if (document.cookie.length > 0) {
                var arr = document.cookie.split('; ') // 这里显示的格式需要切割一下自己可输出看下
                for (let i = 0; i < arr.length; i++) {
                    let arr2 = arr[i].split('=') // 再次切割
                    // 判断查找相对应的值
                    if (arr2[0] === key) {
                        return arr2[1]
                    }
                }
            }
        },
     
        remove: function (key) {
            set(key, '', -1);
        }
    };
    
    handler.on('click', function (e) {
        text.html('加载中...')
        window.requestAnimationFrame(animation)

    })
    
    handler.on('touchstart', function (e) {
        text.html('加载中...')
        window.requestAnimationFrame(animation)

    })

    function animation() {
        if (left > maxWidth) {
            text.html('加载完成!');
            Cookie.set("is_user_do",'yes',99);
            return window.location = _jump_url;
        } else {
            handler.css({ 'left': left });
            drag_bg.css({ 'width': left });
            left += TEMP
            requestAnimationFrame(animation)
        }
    }

};