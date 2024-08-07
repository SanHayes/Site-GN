<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:75:"/www/wwwroot/default/Web/99/application/index/view/index/historynotice.html";i:1722261189;s:59:"/www/wwwroot/default/Web/99/application/index/view/nav.html";i:1722261189;}*/ ?>

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
    
    <link rel="stylesheet"  href="/mobile/css/weui.css">
    <link rel="stylesheet" href="/mobile/css/jquery-weui.min.css">
    <script src="/mobile/js/2461f3.js"></script>
</head>
<body>
<title>平臺公告</title>
<link rel="stylesheet" href="/mobile/css/567602.css">

<div class="adPage pa ma">
    <!-- title开始 -->
    <div class="topPart  flex align-items">
        <i id="goBack" class="iconfont  icon-back1 pl pr "></i>
        <div class="flex justify-center align-items box">平臺公告</div>
    </div>
    <!-- title结束 -->
    <?php foreach($notices as $vo): ?>
    <h2 class="fontTitle" >平臺公告</h2>
    <div class="flex item  align-items" >
        <!-- <img class=" mr"  src="./static/images/mock_img_dts.png" /> -->
        <div class="box flex column " >
            <div class=" fontDetail" ><?php echo date('Y-m-d',$vo['time']); ?></div>
        </div>
    </div>
    <div class="content" >
        <p><?php echo $vo['title']; ?></p>
        <p><?php echo htmlspecialchars_decode(nl2br($vo['content'])); ?></p>
    </div>
    <?php endforeach; ?>
</div>
<div class="footer flex bgMainActive align-items pa">
    <!-- data-url 传入跳转的地址 -->
    <div data-url="<?php echo url('/index/index/home'); ?>" class="ripple footerItem forJsClick item box flex column align-items ">
        <i class="iconfont   icon-shouye"></i>
        <div>首頁</div>
    </div>
    <div data-url="<?php echo url('/index/index/pay'); ?>" class="ripple footerItem forJsClick item box flex column align-items ">
        <i class="iconfont   icon-money"></i>
        <div>充值</div>
    </div>
    <div data-url="<?php echo $conf['sys_kefu']; ?>" class="ripple footerItem forJsClick item box flex column align-items ">
        <i class="iconfont icon-zaixiankefu  "></i>
        <div>客服</div>
    </div>
    <div data-url="<?php echo url('/index/order/hold'); ?>" class="ripple footerItem forJsClick item box flex column align-items ">
        <i class="iconfont   icon-zixun"></i>
        <div>訂單</div>
    </div>
    <div data-url="<?php echo url('/index/index/member'); ?>" class="ripple footerItem forJsClick item box flex column align-items ">
        <i class="iconfont   icon-wode"></i>
        <div>賬戶</div>
    </div>
</div>


<!-- loading遮罩层开始 -->
<div id="loading" class="hidden">
    <div class="mloading">
        <div class="flex align-items justify-center loadingWrap">
            <i class="weui-loading"></i>
        </div>
    </div>
</div>
<!-- loading遮罩层结束 -->
<!-- loading遮罩层,点击不能关闭开始 -->
<div id="loadingNotTuochClose" class="hidden">
    <div class="mloadingNoTouch">
        <div class="flex align-items justify-center loadingWrap">
            <i class="weui-loading"></i>
        </div>
    </div>
</div>
<!-- loading遮罩层结束 -->

<!-- body 最后 -->
<script src="/mobile/js/jquery.min.js"></script>
<script src="/mobile/js/jquery-weui.min.js"></script>
<script>

    $(document).ready(function (e) {

        //链接 点击
        $('.forJsClick').on('click', function (e) {
            e.preventDefault()
            e.stopPropagation()
            var url = $(this).data('url')
            if (url) {
                setTimeout(function () {
                    window.location.href = url;

                }, 300)
            }
        })
        //弹窗关闭
        $('.footerItem').each(function (e) {

            var url = $(this).data('url')
            url = url && url.replace('./', '')
            if (window.location.href.indexOf(url) > -1) {
                $(this).addClass('on')
            }
        })
        //遮罩层可以关闭
        $('.loadingWrap').on('click', function () {
            $('#loading').addClass('hidden')
        })
        //返回
        $('#goBack').on('click', function () {
            window.history.back()
        })
        var browser = {
            versions: function () {
                var u = navigator.userAgent, app = navigator.appVersion;
                return {     //移动终端浏览器版本信息
                    mobile: !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端
                };
            }(),
        }

        if (!browser.versions.mobile) {
            //pc
            $('body').addClass('plantPc')
        }


    });
</script>
<!-- 如果使用了某些拓展插件还需要额外的JS -->
<script src="/mobile/js/ede332.js"></script>

</body>
</html>
