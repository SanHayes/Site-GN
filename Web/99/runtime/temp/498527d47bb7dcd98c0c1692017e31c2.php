<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:66:"/www/wwwroot/default/Web/99/application/home/view/login/login.html";i:1722315029;}*/ ?>

<!DOCTYPE html>
<html data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta name="full-screen" content="yes">
    <meta name="x5-fullscreen" content="true">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <link rel="shortcut icon" href="" type="image/x-icon">
    <link rel="stylesheet" type="" href="/pc/css/bootstrap.min.css">
    <link rel="stylesheet" type="" href="/pc/css/bootstrapValidator.css">
</head>
<body>
<style type="text/css">
    .dialog{
        display: none
    }
</style>
<title><?php echo !empty($conf['web_name'])?$conf['web_name']:''; ?></title>
<link rel="stylesheet" href="/pc/css/c0928d.css">
<!-- ======= Header ======= -->
<header id="header" class=" bgMainDark">
    <div class=" logoNavWrap d-flex align-items-center flex align-items justify-between">

        <h1 class="logo mr-auto ml"><a href="javascript:viod(0)"> <img alt="logo" src="<?php echo $conf['web_logo']; ?>" /> </a>
        </h1>
        <nav class="nav-menu d-none d-lg-block mr">
            <ul>
                <li class=""><a href="javascript:viod(0)" class="forJsClick" data-url="<?php echo url('/home/login/login'); ?>">登录</a></li>

                <li><a href="javascript:viod(0)" class="forJsClick" data-url="<?php echo url('/home/login/register'); ?>">注册</a></li>
            </ul>
        </nav><!-- .nav-menu -->
    </div>
</header><!-- End Header -->
<canvas id="space" style="position:fixed; z-index: -1; top:0; left:0; width:100%; height:100vh;" width="602" height="625"></canvas>
<a href="#" id="warp"></a>
<main class="main" style="background:none;">
    <div class="container">
        <div class="login" style="background:rgba(255,255,255,0.8);">
            <h2 class="fontTitle" >欢迎登录</h2>
            <h4 class="fontDetail">用户第一、安全第一</h4>
            <form class="form-horizontal" role="form" method="post">
                <div class="form-group">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" name="username" placeholder="请输入用户名">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <input class="form-control" name="pwd" type="password" placeholder="请输入密码">
                    </div>
                </div>
                <div class="form-group">
                    <div class="  col-sm-12">
                        <button type="submit" class="btn ripple btn-lg  bgUseable fontPanel">登录</button>
                    </div>
                </div>
                <div class="form-group">
                    <div class="  col-sm-12 ">
                        <div class="flex justify-end"  style="font-size:18px;">
                            没有账号?&nbsp;&nbsp;<a class="fontMain forJsClick" href="javascript:void(0)" data-url="<?php echo url('/home/login/register'); ?>">去注册</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main><!-- End #main -->
<div data-url="<?php echo $conf['sys_kefu']; ?>"  class="kefu hoverPointer forJsClick003 bgUseable" >
    <span><i class="iconfont icon-kefu fontPanel" ></i></span>
    <span class="fontPanel" id="zaixian" >在线客服</span>
</div>
<script src="/pc/js/jquery.min.js"></script>
<script src="/pc/js/layer/layer.js"></script>
<script src="/pc/js/bootstrap.min.js"></script>
<script src="/pc/js/bootstrapValidator.js"></script>
<script>
    var _loginurl = "<?php echo url('/home/login/login'); ?>";
    var _indexurl = "<?php echo url('/home/index/home'); ?>";
    var _msg_username = "用户名必须填写",
        _msg_password = "密码必须填写",
        _msg_password_length = "密码长度要不小于6位",
        _msg_login_suc = "登录成功";
    let loadIndex = null;
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
    //链接 点击 跳出新窗口
    $('.forJsClick003').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var url = $(this).data('url');
        if (url) {
            setTimeout(function () {
                window.open(url);
            }, 300)
        }
    });
    function showError(text) {
        layer.msg(text, { icon: 2 });

    }
    function showSuccess(text) {
        layer.msg(text, { icon: 1 });

    }
    function showLoading() {
        loadIndex = layer.load();

    }
    function hiddenLoading() {
        //关闭
        layer.close(loadIndex);
    }
</script>
<script src="/pc/js/c99b45.js"></script>
<script>

    window.requestAnimFrame = (function () {
        return window.requestAnimationFrame
    })();
    var canvas = document.getElementById("space");
    var c = canvas.getContext("2d");
    var numStars = 1900;
    var radius = '0.' + Math.floor(Math.random() * 9) + 1;
    var focalLength = canvas.width * 2;
    var warp = 0;
    var centerX, centerY;
    var stars = [], star;
    var i;
    var animate = true;
    initializeStars();
    function executeFrame() {
        if (animate)
            requestAnimFrame(executeFrame);
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
            c.fillStyle = "rgba(31,17,54,1)";
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
</script>
</body>
</html>
