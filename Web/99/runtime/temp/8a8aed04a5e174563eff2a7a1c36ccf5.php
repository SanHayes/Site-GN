<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:66:"/www/wwwroot/default/Web/99/application/index/view/order/hold.html";i:1722261189;s:59:"/www/wwwroot/default/Web/99/application/index/view/nav.html";i:1722261189;}*/ ?>
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
<title>交易明細</title>
<link rel="stylesheet" href="/mobile/css/0f1a58.css">
<div class="businessList">
    <!-- title开始 -->
    <div class="topPart  flex align-items">
        <i id="goBack" class="iconfont  icon-back1 pl pr "></i>
        <div class="flex justify-center align-items box">交易明細</div>
    </div>
    <!-- title结束 -->
    <!-- tab开始 -->
    <div class="weui-tab">
        <div class="weui-navbar">
            <a class="weui-navbar__item weui-bar__item--on" href="<?php echo url('/index/order/hold'); ?>">
                持倉記錄
            </a>
            <a class="weui-navbar__item " href="<?php echo url('/index/order/history'); ?>">
                歷史記錄
            </a>
        </div>
        <div class="weui-tab__bd">
            <!-- 持有模块开始 -->
            <div id="tabHold" class="weui-tab__bd-item weui-tab__bd-item--active"></div>
            <!-- 持有模块结束 -->
            <!-- 历史模块开始 -->
            <div id="tabHistory" class="weui-tab__bd-item">
                <div data-url="./transactionDetailList.html" class="item ripple forJsClick pa">
                    <div class="flex justify-between">
                        <div class="flex upPart align-items">
                            <img class="mr" src="" /> <span class="fontb fontTitle">中國黃金</span>
                        </div>
                        <div class="fontDetail">
                            <span>已完成</span>
                            <i class="iconfont icon-xiangyou mr fontbn"></i>
                        </div>
                    </div>
                    <div class="flex justify-between fontDetail">
                        <div>
                            <div class="mt">買漲:¥119.49</div>
                            <div class="mt">下單時間 2020-12-17 17:30:45</div>
                        </div>
                        <div>
                            <div class="mt textr">盈虧</div>
                            <div class="fontDanger mt money textr">
                                ¥8.65
                            </div>
                        </div>
                    </div>
                </div>
                <div class="weui-loadmore weui-loadmore_line">
                    <span class="weui-loadmore__tips">到底了</span>
                </div>
                <div class="weui-loadmore">
                    <i class="weui-loading"></i>
                    <span class="weui-loadmore__tips  fontDetail">正在載入</span>
                </div>
            </div>
            <!-- 历史模块结束 -->
        </div>
    </div>
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
<script src="__HOME__/js/lk/order.js"></script>
<script src="/static/public/js/base64.js"></script>
<script type="text/javascript">
    var Base64 = new Base64();
</script>
<script src="/mobile/js/jquery.min.js"></script>
<script src="/mobile/js/jquery-weui.min.js"></script>
<script>
    var _detailurl = "/oerfa/detail.html";
    var proprice;
    setInterval(function () {
        var _orderUrl = "<?php echo url('/index/order/ajaxorder_list'); ?>";
        var url = "<?php echo url('/index/order/get_price'); ?>";
        $.get(url, function (resdata) {
            if (!resdata) {
                proprice = '';
            } else {
                proprice = jQuery.parseJSON(Base64.decode(resdata));
            }
            //console.log(proprice);
        });
        $.get(_orderUrl, function (res) {
            if(res){
                res = jQuery.parseJSON(Base64.decode(res))['data'];
                console.log(res)
                if (res.length) {
                    var _str = '';
                    for (var i = 0; i < res.length; i++) {
                        var maxtime = res[i].selltime - res[i].time;
                        var msg = '';
                        if (!res[i].ostaus && maxtime > 0) {
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
                        } else if (res[i].ostaus) {
                            msg = '待平倉';
                        }
                        console.log(msg)
                        _str += `
                    <div class="item ripple pa forJsClick">
                        <div class="flex upPart align-items">
                            <img class="mr" src="${ res[i].img }" />
                            <span class="fontb fontTitle">${ res[i].ptitle }</span>
                            <span style="color: #ffffff;margin-left: 20px;">${ res[i].ostyle == 0 ? '買漲' : '買跌' }</span>
                        </div>
                        <div class="flex justify-between fontDetail">
                            <div>
                                <div class="mt">建倉點位：¥${ res[i].buyprice }</div>
                                <div class="mt">交易金額：¥${ res[i].fee }</div>
                                <div class="mt">下單時間：${ getLocalTime(res[i].buytime) }</div>
                            </div>
                            <div>
                                <div class="mt textr">平倉點位：¥${ res[i].sellprice }</div>
                                <div class="mt textr">手續費：¥${ res[i].sx_fee }</div>
                                <div class="mt textr">平倉時間 ${ getLocalTime(res[i].selltime) }</div>
                                <div data-time="<?php echo $vo['time']; ?>" class="fontSuccess mt time textr">${ msg }</div>
                            </div>
                        </div>
                    </div>
                    `;
                    }
                    $('#tabHold').html(_str);
                }
            } else {
                $('#tabHold').html(`<div class="weui-loadmore weui-loadmore_line">
                    <span class="weui-loadmore__tips">到底了</span>
                </div>`);
            }
        });
    }, 1000);
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
        });
        //弹窗关闭
        $('.footerItem').each(function (e) {
            var url = $(this).data('url');
            url = url && url.replace('./', '');
            if (window.location.href.indexOf(url) > -1) {
                $(this).addClass('on');
            }
        });
        //遮罩层可以关闭
        $('.loadingWrap').on('click', function () {
            $('#loading').addClass('hidden');
        });
        //返回
        $('#goBack').on('click', function () {
            window.history.back();
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
            $('body').addClass('plantPc');
        }
    });
</script>
<!-- 如果使用了某些拓展插件还需要额外的JS -->
<script src="/mobile/js/4b3859.js"></script>
</body>
</html>
