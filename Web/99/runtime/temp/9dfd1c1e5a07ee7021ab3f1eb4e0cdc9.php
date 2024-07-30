<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:66:"/www/wwwroot/default/Web/99/application/index/view/index/mine.html";i:1722261189;s:59:"/www/wwwroot/default/Web/99/application/index/view/nav.html";i:1722261189;}*/ ?>
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
    
    <link rel="stylesheet"  href="/mobile/css/weui.css">
    <link rel="stylesheet" href="/mobile/css/jquery-weui.min.css">
    <script src="/mobile/js/2461f3.js"></script>
</head>
<body>
<title>我的</title>
<link rel="stylesheet" href="/mobile/css/8af909.css">

<div class="me">
    <div class="metopPart">
        <div class="pure_top">
            <div class="title flex justify-center fontMepage">總金額(元)</div>
            <div class="flex justify-center fontButtonTitle value "><?php echo $userInfo['usermoney']; ?></div>
            <div class="flex  justify-around ml mr pl pr moneyWrap">
                <div class="flex goForJS column align-items moneyItem">
                    <span class="fontMepage">昨日收益</span>
                    <span class="fontButtonTitle"><?php echo $userinfo['yesterday_count']; ?></span>
                </div>
                <div class="flex goForJS column align-items moneyItem">
                    <span class="fontMepage">今日收益</span>
                    <span class="fontButtonTitle"><?php echo $userinfo['today_count']; ?></span>
                </div>
                <?php if(($conf['sys_rates']==1)): ?>
                <div class="flex goForJS column align-items moneyItem">
                    <span class="fontMepage">定投金額</span>
                    <span class="fontButtonTitle"><?php echo $userinfo['invest_count']; ?></span>
                </div>
                <?php endif; ?>
            </div>
            <div class="flex   justify-around bgMain ml mr borderRadius pa moneyWrap fontDetail">
                <div class="flex column align-items forJsClick ripple moneyItem" data-url="<?php echo url('/index/index/pay'); ?>">
                    <i class="iconfont  icon-tixian "></i>
                    <span class=" ">充值</span>
                </div>
                <div class="flex column align-items forJsClick ripple moneyItem" data-url="<?php echo url('/index/index/withdraw'); ?>">
                    <i class="iconfont  icon-tongqian "></i>
                    <span class=" ">提現</span>
                </div>
                <?php if(($conf['sys_rates']==1)): ?>
                <div class="flex column align-items forJsClick ripple moneyItem" data-url="<?php echo url('/index/index/invest'); ?>">
                    <i class="iconfont  icon-yuebao "></i>
                    <span class=" ">定投</span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- list开始 -->
    <div class=" pa list">
        <div data-url="<?php echo url('/index/order/hold'); ?>"
             class="forJsClick listItem ripple   flex pa  alig n-items justify-between fontDetail    brTop ">
            <div class="flex align-items ">
                <i class="iconfont icon-jilu2 mr colorForIcon"></i>
                交易記錄
            </div>
            <div>
                <i class="iconfont icon-xiangyou mr  "></i>
            </div>
        </div>
        <div  data-url="<?php echo url('/index/index/accountrecord'); ?>"
              class=" forJsClick   listItem ripple   flex pa  alig n-items justify-between fontDetail   ">
            <div class="flex align-items">
                <i class="iconfont icon-chongzhijilu mr colorForIcon "></i>
                充值明細
            </div>
            <div>
                <i class="iconfont icon-xiangyou mr "></i>
            </div>
        </div>
        <div  data-url="<?php echo url('/index/index/withdrawrecord'); ?>"
              class=" forJsClick   listItem ripple   flex pa  alig n-items justify-between fontDetail   ">
            <div class="flex align-items">
                <i class="iconfont icon-chongzhijilu mr colorForIcon "></i>
                提現明細
            </div>
            <div>
                <i class="iconfont icon-xiangyou mr "></i>
            </div>
        </div>
        <div data-url="<?php echo url('/index/index/userinvest'); ?>"
             class=" forJsClick   listItem ripple   flex pa  alig n-items justify-between fontDetail   ">
            <div class="flex align-items">
                <i class="iconfont icon-tixianjilu1 mr colorForIcon"></i>
                定投記錄
            </div>
            <div>
                <i class="iconfont icon-xiangyou mr "></i>
            </div>
        </div>
        <div data-url="<?php echo url('/index/index/moditypwd'); ?>"
             class=" forJsClick   listItem ripple   flex pa  alig n-items justify-between fontDetail   ">
            <div class="flex align-items">
                <i class="iconfont icon-zhanghaoxinxi-xiugai mr colorForIcon"></i>
                個人資訊
            </div>
            <div>
                <i class="iconfont icon-xiangyou mr "></i>
            </div>
        </div>
        <div data-url="<?php echo url('/index/index/bankcard'); ?>"
             class=" forJsClick   listItem ripple   flex pa  alig n-items justify-between fontDetail   ">
            <div class="flex align-items">
                <i class="iconfont icon-shuaqiaqiapianyinhangqia mr colorForIcon"></i>
                銀行資訊
            </div>
            <div>
                <i class="iconfont icon-xiangyou mr "></i>
            </div>
        </div>
        <div data-url="<?php echo url('/index/index/historynotice'); ?>"
             class=" forJsClick   listItem ripple   flex pa  alig n-items justify-between fontDetail   ">
            <div class="flex align-items">
                <i class="iconfont icon-gonggao mr colorForIcon"></i>
                平臺公告
            </div>
            <div>
                <i class="iconfont icon-xiangyou mr "></i>
            </div>
        </div>
        <div data-url="<?php echo $conf['sys_kefu']; ?>" class=" forJsClick listItem ripple flex pa  alig n-items justify-between fontDetail   ">
            <div class="flex align-items">
                <i class="iconfont icon-zaixiankefu mr colorForIcon"></i>
                線上客服
            </div>
            <div>
                <i class="iconfont icon-xiangyou mr "></i>
            </div>
        </div>
        <?php if($downloadUrl): ?>
        <div data-url="<?php echo $downloadUrl; ?>"
             class=" forJsClick   listItem ripple   flex pa  alig n-items justify-between fontDetail   ">
            <div class="flex align-items">
                <i class="iconfont icon-icon-test mr colorForIcon"></i>
                App下載
            </div>
            <div>
                <i class="iconfont icon-xiangyou mr "></i>
            </div>
        </div>
        <?php endif; ?>

        <div data-url="<?php echo url('/index/index/loginout'); ?>" class="forJsClick listItem ripple   flex pa  alig n-items justify-between fontDetail    brBottom">
            <div class="">
                <i class="iconfont icon-tuichu mr colorForIcon"></i>
                退出
            </div>
            <div>
                <i class="iconfont icon-xiangyou mr "></i>
            </div>
        </div>

    </div>
    <!-- list结束 -->
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

<script src="/mobile/js/jquery.min.js"></script>
<script src="/mobile/js/jquery-weui.min.js"></script>
<!-- 如果使用了某些拓展插件还需要额外的JS -->
<script src="/mobile/js/251abc.js"></script>
</body>
</html>

