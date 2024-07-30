<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:66:"/www/wwwroot/Site-GN/Web/99/application/index/view/index/home.html";i:1696505732;s:59:"/www/wwwroot/Site-GN/Web/99/application/index/view/nav.html";i:1696506481;}*/ ?>
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
<title><?php echo !empty($conf['web_name'])?$conf['web_name']:''; ?></title>
<link rel="stylesheet" href="/mobile/swiper-bundle.min.css">
<script src="/mobile/swiper-bundle.min.js"></script>
<link rel="stylesheet" href="/mobile/css/5ea401.css">

<div class="indexPage">
    <!-- 幻灯片开始 -->
    <div id="imgSwriper" data-auto="false" class="swiper-container">
        <div class="swiper-wrapper">
            <?php if(is_array($gallery) || $gallery instanceof \think\Collection || $gallery instanceof \think\Paginator): if( count($gallery)==0 ) : echo "" ;else: foreach($gallery as $key=>$vo): ?>
            <div class="swiper-slide"><img src="<?php echo $vo['img']; ?>" alt="<?php echo $vo['title']; ?>"/></div>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <!-- 如果需要分页器 -->
        <div class="swiper-pagination"></div>
    </div>
    <!-- 幻灯片结束 -->
    <!-- 跑马灯开始 -->
    <div class="flex pl pr bgMain align-items">
        <i class="iconfont fontTitle icon-laba8 mr"></i>
        <marquee class="box fontDanger marquee">
            <p><?php echo $notices['zouma']['content']; ?></p>
        </marquee>
    </div>
    <!-- 跑马灯结束 -->
    <!-- 入口开始 -->
    <div class="flex entrance bgBetweenPanel pa">
        <div class="box flex bgMain borderRadius pa mr ripple forJsClick" data-url="<?php echo Url('/index/index/pay'); ?>">
            <div class="flex box column justify-center ">
                <div class="font18 fontTitle">快捷充值</div>
                <div class="font12  fontDetail fontDetail ">支援多種充值方式</div>
            </div>
            <div class="flex align-items justify-center">
                <i class="iconfont icon-drxx89 mr ficonStyle fontMain"></i>
                <!-- <img src="/mobile/images/fast_buy.png" class="ficonStyle" /> -->
            </div>
        </div>
        <div class="box flex column justify-between centerPart  ">
            <div data-url="<?php echo $conf['sys_kefu']; ?>" class="flex align-items justify-center bgMain borderRadius ripple forJsClick">
                <!-- <img src="/mobile/images/obline.png" class="iconStyle pr mr" /> -->
                <i class="iconfont icon-zaixiankefu iconStyle pr mr fontMain"></i>
                <span class="fontTitle">線上客服</span>
            </div>
            <?php if(($conf['sys_rates']==1)): ?>
            <div data-url="<?php echo Url('index/index/invest'); ?>" class="flex align-items justify-center bgMain borderRadius ripple forJsClick">
                <!-- <img src="/mobile/images/help_center.png" class="iconStyle pr mr" /> -->
                <i class="iconfont icon-yuebao iconStyle pr mr fontMain"></i>
                <span class="fontTitle">我的定投</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- 入口结束 -->
    <!-- 信息面板轮播图开始 -->
    <div id="infoPanel" data-auto="true" class="swiper-container bgBetweenPanel ">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <div class="flex column  bgMain justify-center align-items mb ml mr"
                     style="padding: 10px; border-radius: 8px;">
                    <div class="fontb font20 fontTitle">熱門產品</div>
                    <div class=" fontDetail font12 pt fontDetail">已經幫助超過1000萬用戶獲取收益 </div>
                    <div class="fontDanger pt">+145.86% &uarr; <i class="iconfont icon-up"></i></div>
                    <div class="ripple  btnWrap">
                        <a href="javascript:;" data-url="<?php echo url('index/goods/goods',['pid'=>$pro[0]['pid']]); ?>" class="weui-btn bgUseable fontButtonTitle weui-btn_primary forJsClick">去看一看</a>
                    </div>
                </div>
            </div>
            <div class="swiper-slide">
                <div class="flex column  bgMain justify-center align-items mb ml mr" style="padding: 10px; border-radius: 8px;">
                    <div class="fontb font20 fontTitle">平臺精選</div>
                    <div class=" fontDetail font12 pt fontDetail">行情震盪，走勢難以把握 </div>
                    <div class="fontDanger pt">專家為你捕捉機會 <i class="iconfont icon-up"></i></div>
                    <div class="ripple  btnWrap">
                        <a href="javascript:;" data-url="<?php echo url('index/goods/goods',['pid'=>$pro[0]['pid']]); ?>" class="weui-btn bgUseable fontButtonTitle weui-btn_primary forJsClick">立即投資</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- 如果需要分页器 -->
        <div class="swiper-pagination"></div>
    </div>
    <!-- 信息面板轮播图结束 -->
    <!-- detailList开始 -->
    <div class="detailList borderBottomBetweenPanel bgMain">
        <div class="headItem flex  pb  justify-center pt listItem fontTitle splitItem">
            <div class="box flex pl">名稱</div>
            <div class="box flex ">狀態</div>
            <div class="box flex ">最新價</div>
        </div>
        <!-- data-url 点击要跳转的地址 -->
        <?php if(is_array($pro) || $pro instanceof \think\Collection || $pro instanceof \think\Paginator): $k = 0; $__LIST__ = $pro;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?>
        <div data-url="<?php echo url('index/goods/goods',['pid'=>$vo['pid']]); ?>" data-status="0" class="listItem  ripple forJsClick2 flex  pb pt bgMain justify-center align-items splitItem">
            <div class="box flex2  pl align-items fontb font14 fontTitle">
                <img alt="" src="<?php echo $vo['img']; ?>" width="40" height="40" />
                <span class="pl"><?php echo $vo['Name']; ?></span>
            </div>
            <?php if($vo['isopen'] == 1): ?>
            <div class="box flex  fontb font14 fontDetail procode_status_name_XAU" >
                交易中
            </div>
            <?php else: ?>
            <div class="box flex  fontb font14 fontDetail procode_status_name_XAU" >
                休市
            </div>
            <?php endif; ?>
            <div class="box flex ">
                <a href="javascript:;" data-price="<?php echo $vo['Price']; ?>" class="weui-btn_mini borderRadius weui-btn_primary btn bgSuccess fontButtonTitle procode_price_<?php echo $vo['pid']; ?>"><?php echo $vo['Price']; ?></a>
            </div>
        </div>
        <?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
    <!-- 弹窗开始 -->
  
    <div id="dialog" class="<?php if ($hide) { echo 'hidden'; } ?>">
        <div class="glass"></div>
        <div class="content bgMain fontTitle">
            <div class="title bgUseable ">
                <div class="flex justify-center pa fontButtonTitle p_ad_tl"><?php echo $notices['alert']['title']; ?></div>
                <span data-id="dialog" class="close pa fontButtonTitle" >X</span>
            </div>
            <div class="pa ma p_ad_ct"><?php echo nl2br($notices['alert']['content']); ?></div>
            <div class="dfonter  bgMain fontMain">確定</div>
        </div>
    </div>
   
    <!-- 弹窗结束 -->
    <!-- detailList结束 -->
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
    var _index_url = "<?php echo url('/index/index/ajaxdata'); ?>";
    $(document).ready(function (e) {
        //链接 点击2
        $('.forJsClick2').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var url = $(this).data('url'), _status = $(this).attr('data-status');
            if(_status == 1){
                return $.toast('產品處於休市中不能交易', 'forbidden')
            }
            if (url) {
                setTimeout(function () {
                    window.location.href = url;
                }, 300)
            }
        })
        //链接 点击
        $('.forJsClick').on('click', function (e) {
            e.preventDefault()
            e.stopPropagation()
            var url = $(this).data('url');
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
    })
</script>
<!-- 如果使用了某些拓展插件还需要额外的JS -->
<script src="/mobile/js/42f698.js"></script>

</body>
</html>
