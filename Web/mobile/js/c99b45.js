'use strict';

$(document).ready(function (e) {
    var login = {
        data: {
            canLogin: false
        }
    };
    $('.weui-input').on('keyup', function () {
        var userName = $.trim($('#userName').val());
        var password = $.trim($('#password').val());
        if (userName && password) {
            $('#subBtn').removeClass('bgDisable').addClass('bgUseable');
            login.data.canLogin = true;
        } else {
            $('#subBtn').removeClass('bgUseable').addClass('bgDisable');
            login.data.canLogin = false;
        }
    });
    $('.forJsClick').on('click', function (e) {
        var url = $(this).data('url');
        if (url) {
            setTimeout(function () {
                window.location.href = url;
            }, 300);
        }
    });
    //遮罩层可以关闭
    $('.loadingWrap').on('click', function () {
        $('#loading').addClass('hidden');
    });
    //登录按钮
    $('#submit').on('click', function () {
        var userName = $.trim($('#userName').val());
        var password = $.trim($('#password').val());
        if (!userName) {
            $.toast(_msg_username, "forbidden");
            return;
        }
        if (!password) {
            $.toast(_msg_password, "forbidden");
            return;
        }
        if (password.length < 6) {
            $.toast(_msg_password_length, "forbidden");
            return;
        }
        var _loading = $('#loadingNotTuochClose');
        _loading.removeClass('hidden');
        _loading.addClass('show');
        //var str = 'userName=' + userName + 'password=' + password;
        $.post(_loginurl, {username:userName, pwd:password}, function (res) {
            if(res.type != 1){
                _loading.removeClass('show');
                _loading.addClass('hidden');
                $.toast(res.data, "forbidden");
            }else{
                $.toast(_msg_login_suc, "text");
                setTimeout(function(){
                    window.location.href = _indexurl;
                },1500);
            }
        });
        return false;

    });
    var browser = {
        versions: function () {
            var u = navigator.userAgent,
                app = navigator.appVersion;
            return { //移动终端浏览器版本信息
                mobile: !!u.match(/AppleWebKit.*Mobile.*/) //是否为移动终端
            };
        }()
    };

    if (!browser.versions.mobile) {
        //pc
        $('body').addClass('plantPc');
    }
	window.requestAnimFrame = function () {
        return window.requestAnimationFrame;
    }();
    var canvas = document.getElementById("space");
    var c = canvas.getContext("2d");
    var numStars = 1900;
    var radius = '0.' + Math.floor(Math.random() * 9) + 1;
    var focalLength = canvas.width * 2;
    var warp = 0;
    var centerX, centerY;
    var stars = [],
        star;
    var i;
    var animate = true;
    initializeStars();
    function executeFrame() {
        if (animate) requestAnimFrame(executeFrame);
        moveStars();
        drawStars();
    }
    function initializeStars() {
        centerX = canvas.width / 2;
        centerY = canvas.height / 2;
        stars = [];
        for (i = 0; i < numStars; i++) {
            star = {
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                z: Math.random() * canvas.width,
                o: '0.' + Math.floor(Math.random() * 99) + 1
            };
            stars.push(star);
        }
    }
    function moveStars() {
        for (i = 0; i < numStars; i++) {
            star = stars[i];
            star.z--;
            if (star.z <= 0) {
                star.z = canvas.width;
            }
        }
    }
    function drawStars() {
        var pixelX, pixelY, pixelRadius;
        // Resize to the screen
        if (canvas.width != window.innerWidth || canvas.width != window.innerWidth) {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            initializeStars();
        }
        if (warp == 0) {
            c.fillStyle = "rgba(26,25,53,1)";
            c.fillRect(0, 0, canvas.width, canvas.height);
        }
        c.fillStyle = "rgba(235, 201, 159, " + radius + ")";
        for (i = 0; i < numStars; i++) {
            star = stars[i];
            pixelX = (star.x - centerX) * (focalLength / star.z);
            pixelX += centerX;
            pixelY = (star.y - centerY) * (focalLength / star.z);
            pixelY += centerY;
            pixelRadius = 1 * (focalLength / star.z);
            c.fillRect(pixelX, pixelY, pixelRadius, pixelRadius);
            c.fillStyle = "rgba(235, 201, 159, " + star.o + ")";
            //c.fill();
        }
    }
    document.getElementById('warp').addEventListener("click", function (e) {
        window.c.beginPath();
        window.c.clearRect(0, 0, window.canvas.width, window.canvas.height);
        window.warp = warp ? 0 : 1;
        executeFrame();
    });
    executeFrame();
    var thtem = $('html').data('theme');
    if (thtem === 'dark') {
        $('.login').removeClass('bgMain');
    }
});
