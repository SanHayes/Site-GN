<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:69:"/www/wwwroot/default/Web/99/application/home/view/login/register.html";i:1722261189;}*/ ?>

<!DOCTYPE html>
<html data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
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

<title>註冊</title>

<link rel="stylesheet" href="/pc/css/c2056b.css">
<!-- ======= Header ======= -->
<header id="header" class=" bgMainDark">
    <div class=" logoNavWrap d-flex align-items-center flex align-items justify-between">

        <h1 class="logo mr-auto ml"><a href="javascript:viod(0)"> <img alt="logo" src="/pc/images/logo.svg" /> </a>
        </h1>


        <nav class="nav-menu d-none d-lg-block mr">
            <ul>
                <li class=""><a href="javascript:viod(0)" class="forJsClick" data-url="<?php echo url('/home/login/login'); ?>">登入</a></li>

                <li><a href="javascript:viod(0)" class="forJsClick" data-url="<?php echo url('/home/login/register'); ?>">註冊</a></li>


            </ul>
        </nav><!-- .nav-menu -->

    </div>
</header><!-- End Header -->
<canvas id="space" style="position:fixed; z-index: -1; top:0; left:0; width:100%; height:100vh;" width="602" height="625"></canvas>
<a href="#" id="warp"></a>
<main class="main" style="background:none;">
    <div class="container">
        <div class="register" style="background:rgba(255,255,255,0.8);">
            <h2 class="fontTitle" >建立您的賬號</h2>
            <!-- <h4>用户第一、安全第一</h4> -->
            <form class="form-horizontal" role="form">
                <?php if($conf['sys_truename']): ?>
                <div class="form-group">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="name" name="name" placeholder="姓名">
                    </div>
                </div>
                <?php endif; if($conf['register_id'] == 1): ?>
                <div class="form-group">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="scard" name="scard" placeholder="身份證號">
                    </div>
                </div>
                <?php endif; ?>
                <div class="form-group">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="account" name="account" placeholder="會員賬號">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <input class="form-control"  id="password" name="password" type="password" placeholder="登入密碼">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <input class="form-control" id="confirmPwd" name="confirmPwd" type="password" placeholder="確認密碼">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <input class="form-control"  id="withdrawal" name="withdrawal" type="password" placeholder="提現密碼">
                    </div>
                </div>
                <?php if($conf['sys_mobile'] == 1): ?>
                <div class="form-group">
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="utel" name="utel" placeholder="手機號碼">
                    </div>
                </div>
                <?php endif; ?>
                <div class="form-group">
                    <div class="col-sm-12">
                        <?php if($conf['sys_invit'] == 1): ?>
                        <input type="text" class="form-control" id="invitCode" name="invitCode" placeholder="邀請碼(必填)">
                        <?php else: ?>
                        <input type="text" class="form-control" id="invitCode" name="invitCode" placeholder="邀請碼(必填)">
                        <?php endif; ?>
                    </div>
                </div>
                <!--<div class="form-group">-->
                <!--<div class="col-sm-12 fontDetail">-->
                <!--<label>-->
                <!--<input type="checkbox"  id="agree"> 我已同意并阅读 <span class="fontMain hoverPointer" >《用户协议》</span>-->
                <!--</label>-->
                <!--</div>-->
                <!--</div>-->
                <div class="form-group">
                    <div class="  col-sm-12">
                        <button id="btn" class="btn btn-lg  ripple bgUseable fontPanel">註冊</button>
                    </div>
                </div>
                <div class="form-group">
                    <div class="  col-sm-12 ">
                        <div class="flex justify-end" style="font-size:18px;" >

                            已經有賬號?&nbsp;&nbsp;<a class="fontMain forJsClick" href="javascript:void(0)" data-url="<?php echo url('/home/login/login'); ?>">登入</a>
                        </div>

                    </div>
                </div>

            </form>
        </div>

    </div>

</main><!-- End #main -->
<div data-url="<?php echo $conf['sys_kefu']; ?>"  class="kefu hoverPointer forJsClick003 bgUseable" >
    <span><i class="iconfont icon-kefu fontPanel" ></i></span>
    &nbsp;&nbsp;&nbsp;<span class="fontPanel" id="zaixian" >線上客服</span>
</div>

<script src="/pc/js/jquery.min.js"></script>
<script src="/pc/js/layer/layer.js"></script>
<script src="/pc/js/bootstrap.min.js"></script>
<script src="/pc/js/bootstrapValidator.js"></script>
<script>
    var _regurl = "<?php echo url('/home/login/register'); ?>";
    var _loginurl = "<?php echo url('/home/login/login.html'); ?>";
    var _inviteNeed = '<?php echo $conf['sys_invit']; ?>';
    var _registerIdNeed = '<?php echo $conf['register_id']; ?>';
    var _msg_name = "姓名必須填寫",
        _msg_account = "賬號必須填寫",
        _msg_password = "密碼必須填寫",
        _msg_password_length = "密碼長度要不小於6位",
        _msg_confirm = "確認密碼必須填寫",
        _msg_password_disagree = "密碼與確認密碼不一致",
        _msg_tx_password = "提現密碼必須填寫",
        _msg_tx_password_length = "提現密碼長度要不小於6位",
        _msg_invite_need = '必須填寫邀請碼',
        _msg_register_id = '必須填寫身份證號',
        _msg_reg_success = "註冊成功", 
        _cp_extra1_name = "身份證";
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
<script src="/pc/js/bbacde.js"></script>
</body>
</html>
