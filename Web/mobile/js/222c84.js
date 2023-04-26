'use strict';

$(document).ready(function (e) {

    $('.time').each(function (index) {
        var _this = this;

        var time = $(this).data('time');
        countDown(time, function (msg) {
            $(_this).addClass('ddd');
            $(_this).html(msg);
        });
    });
    function countDown(maxtime, fn) {
        var timer = setInterval(function () {
            if (!!maxtime) {
                var msg = '';
                var day = Math.floor(maxtime / 86400),
                    hour = Math.floor(maxtime % 86400 / 3600),
                    minutes = Math.floor(maxtime % 3600 / 60),
                    seconds = Math.floor(maxtime % 60);
                if (hour < 10) {
                    hour = '0' + hour;
                }
                if (minutes < 10) {
                    minutes = '0' + minutes;
                }
                if (seconds < 10) {
                    seconds = '0' + seconds;
                }
                msg = hour + ":" + minutes + ":" + seconds;
                fn(msg);
                --maxtime;
            } else {
                clearInterval(timer);
                fn("结束!");
            }
        }, 1000);
    }
    // countDown(86, function (msg) {
    //     document.getElementById('timer1').innerHTML = msg;
    // })
    // countDown(86400, function (msg) {
    //     document.getElementById('timer2').innerHTML = msg;
    // })
});