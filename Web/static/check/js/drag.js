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
            return self.location = _jump_url;
        } else {
            handler.css({ 'left': left });
            drag_bg.css({ 'width': left });
            left += TEMP
            requestAnimationFrame(animation)
        }
    }

};