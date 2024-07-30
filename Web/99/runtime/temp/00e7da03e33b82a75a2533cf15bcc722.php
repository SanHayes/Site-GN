<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:65:"/www/wwwroot/default/Web/99/application/home/view/index/home.html";i:1722261189;s:58:"/www/wwwroot/default/Web/99/application/home/view/nav.html";i:1722261189;}*/ ?>
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
<link rel="stylesheet" href="/pc/css/5ea401.css">
<!-- ======= Header ======= -->
<header id="header" class=" bgMainDark">
    <div class=" logoNavWrap d-flex align-items-center flex align-items justify-between">
        <h1 class="logo mr-auto ml">
            <a href="javascript:viod(0)" class="forJsClick" data-url="<?php echo url('/home/index/home'); ?>">
                <img alt="logo" src="<?php echo $conf['web_logo']; ?>">
            </a>
        </h1>
        <nav class="nav-menu d-none d-lg-block mr">
    <ul>
        <li class="">
            <a class="forJsClick" data-url="<?php echo url('/home/index/home'); ?>" href="javascript:viod(0)">
                <i class="iconfont icon-zhuye" ></i>
                首頁
            </a>
        </li>
        <li  class="">
            <a href="javascript:viod(0)" class="forJsClick" data-url="<?php echo url('/home/index/pay'); ?>">
                <i class="iconfont icon-chongzhi" ></i>
                儲值
            </a>
        </li>
        <li class="">
            <a href="javascript:viod(0)" class="forJsClick" data-url="<?php echo url('/home/index/withdraw'); ?>">
                <i class="iconfont icon-tixian" ></i>
                提現
            </a>
        </li>
        <?php if(($conf['sys_rates']==1)): ?>
        <li class="">
            <a href="javascript:viod(0)" class="forJsClick" data-url="<?php echo url('/home/index/invest'); ?>">
                <i class="iconfont icon-jijindingtou" ></i>
                訂投
            </a>
        </li>
        <?php endif; ?>
        <li class="drop-down ">
            <a href="javascript:viod(0)">
                <i class="iconfont icon-ziyuan" ></i>
                訂單
                <span class="caret"></span>
            </a>
            <ul>
                <li><a href="javascript:viod(0)" class="forJsClick" data-url="<?php echo url('/home/index/accountrecord'); ?>">儲值明細</a></li>
                <li><a href="javascript:viod(0)" class="forJsClick" data-url="<?php echo url('/home/index/withdrawrecord'); ?>">提現明細</a></li>
                <li><a href="javascript:viod(0)" class="forJsClick" data-url="<?php echo url('/home/order/hold'); ?>">持倉記錄</a></li>
                <li><a href="javascript:viod(0)" class="forJsClick" data-url="<?php echo url('/home/order/history'); ?>">歷史記錄</a></li>
                <?php if(($conf['sys_rates']==1)): ?>
                <li><a href="javascript:viod(0)" class="forJsClick" data-url="<?php echo url('/home/index/userinvest'); ?>">訂投訂單</a></li>
                <?php endif; ?>
            </ul>
        </li>
        <li class="drop-down ">
            <a href="javascript:viod(0)">
                <i class="iconfont icon-zhanghao" ></i>
                帳號
                <span class="caret"></span>
            </a>
            <ul>
                <?php if($conf['personal_edit'] == 1): ?>
                <li><a href="javascript:viod(0)" class="forJsClick" data-url="<?php echo url('/home/index/moditypwd'); ?>">個人資訊</a></li>
                <?php endif; ?>
                <li><a href="javascript:viod(0)" class="forJsClick" data-url="<?php echo url('/home/index/bankcard'); ?>">銀行資訊</a></li>
                <li><a href="javascript:viod(0)" class="forJsClick" data-url="<?php echo url('/home/index/loginout'); ?>">退出</a></li>
            </ul>
        </li>
        <li class="drop-down"><a href="javascript:viod(0)"><i class="iconfont icon-zichan " ></i>資產<span class="caret"></span> </a>
            <ul  class="font18  zhichan" id="acc_wrap">
                <li class="flex  pl pr justify-around" ><span class="mLabel" >總金額:</span><span class="mValue _a1"><?php echo $userinfo['usermoney']; ?></span></li>
                <?php if(($conf['sys_rates']==1)): ?>
                <li class="flex  pl pr justify-around" ><span class="mLabel" >定投金額:</span><span class="mValue _a2" ><?php echo $userinfo['invest_count']; ?></span></li>
                <?php endif; ?>
                <li class="flex  pl pr justify-around" ><span class="mLabel" >昨日收益:</span><span class="mValue _a3" ><?php echo $userinfo['yesterday_count']; ?></span></li>
                <li class="flex  pl pr justify-around" ><span class="mLabel" >今天收益:</span><span class="mValue _a4" ><?php echo $userinfo['today_count']; ?></span></li>
            </ul>
        </li>
        <li class="">
            <a href="javascript:viod(0)" class="forJsClick" data-url="<?php echo url('/home/index/historynotice'); ?>">
                <i class="iconfont icon-gonggao1" ></i>
                公告
            </a>
        </li>
    </ul>
</nav><!-- .nav-menu -->

    </div>
</header><!-- End Header -->
<main class="main">
    <!-- ======= banner ======= -->
    <section class="banner bgMainDark">
        <h1 class="fontPanel flex justify-center "><?php echo !empty($conf['web_name'])?$conf['web_name']:''; ?></h1>
        <div class="flex imgWrap justify-center">
            <?php if(is_array($gallery) || $gallery instanceof \think\Collection || $gallery instanceof \think\Paginator): if( count($gallery)==0 ) : echo "" ;else: foreach($gallery as $key=>$vo): ?>
            <a href="javascript:void(0)" alt="" title="">
                <img alt="" src="<?php echo $vo['img']; ?>" height="185"/>
            </a>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </section><!-- banner结束 -->
    <!-- ======= marquee ======= -->
    <section class="marquee  ">
        <div class="container">
            <i class="iconfont fontMain icon-gonggao"></i>
            <marquee>
                <p><?php echo $notices['zouma']['content']; ?><br></p>
            </marquee>
        </div>
    </section>
    <!-- marquee结束 -->
    <!-- ======= table ======= -->
    <section class="mTable  ">
        <div class="container">
            <table class="table table-hover font18">
                <thead>
                <tr class="pt pb">
                    <th>名稱</th>
                    <th>狀態</th>
                    <th>最高價</th>
                    <th>最低價</th>
                    <th>最新價</th>
                </tr>
                </thead>
                <tbody>
                <?php if(is_array($pro) || $pro instanceof \think\Collection || $pro instanceof \think\Paginator): $k = 0; $__LIST__ = $pro;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?>
                <tr data-url="<?php echo url('/home/goods/goods',['pid'=>$vo['pid']]); ?>" data-status="0" class="pt pb forJsClick2 hoverPointer procode_<?php echo $vo['pid']; ?>" >
                    <td class="flex align-items">
                        <img src="<?php echo $vo['img']; ?>" class="tlogo" />
                        <?php echo $vo['Name']; ?>
                    </td>
                    <?php if($vo['isopen'] == 1): ?>
                    <td class="fontSuccess procode_status_name_<?php echo $vo['pid']; ?>" >交易中</td>
                    <?php else: ?>
                    <td class="fontSuccess procode_status_name_<?php echo $vo['pid']; ?>" >休市</td>
                    <?php endif; ?>
                    <td class="procode_high_<?php echo $vo['pid']; ?>"><?php echo $vo['High']; ?></td>
                    <td class="procode_low_<?php echo $vo['pid']; ?>"><?php echo $vo['Low']; ?></td>
                    <td  data-price="<?php echo $vo['Price']; ?>" class="procode_price_<?php echo $vo['pid']; ?>"><?php echo $vo['Price']; ?></td>
                </tr>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
        </div>
    </section>
    <!-- table结束 -->
</main><!-- End #main -->
<div data-url="<?php echo $conf['sys_kefu']; ?>"  class="kefu hoverPointer forJsClick003 bgUseable" >
    <span><i class="iconfont icon-kefu fontPanel" ></i></span>
    <span class="fontPanel" id="zaixian" >網路客服</span>
</div>

<div class="dialog <?php echo !empty($hide)?'hidden' : 'show'; ?>">
    <div class="mark"></div>
    <div class="dialogBox ">
        <div class="flex  pa titleWrap">
            <div class="box font18" id="dialogTitle"><?php echo $notices['alert']['title']; ?></div>
            <span id="dialogClose"> X</span>
        </div>
        <div class="mcontent pa" id="mcontent"><?php echo nl2br($notices['alert']['content']); ?></div>
        <div class="btnWrap" >
            <button id="dialogColseSure" class="btn ripple btn-primary">確定</button>
        </div>
    </div>
</div>

<script src="/pc/js/jquery.min.js"></script>
<script src="/pc/js/layer/layer.js"></script>
<script src="/pc/js/bootstrap.min.js"></script>
<script>
    var _index_url = "<?php echo url('/home/index/ajaxdata'); ?>";
    //链接 点击2
    $('.forJsClick2').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var url = $(this).data('url'), _status = $(this).attr('data-status');
        if(_status == 1){
            return showError('產品處於休市中不能交易')
        }
        if (url) {
            setTimeout(function () {
                window.location.href = url;
            }, 300)
        }
    })
    function closeDialog() {
        $.post("<?php echo url('home/index/hide'); ?>");
        $('#dialogTitle').html('');
        $('#mcontent').html('');
        $('.dialog').removeClass('show').addClass('hidden')
    }
    $('#dialogColseSure').on('click', function () {
        closeDialog()
    });
    $('.mark').on('click', function () {
        closeDialog()
    });
    $('.mark').on('wheel', function (e) {
        e.stopPropagation();
        e.preventDefault();
        return false

    });
    $('#dialogClose').on('click', function () {
        closeDialog()
    });
</script>
<script src="/pc/js/fde4e3.js"></script>
<script src="/pc/js/42f698.js"></script>
</body>
</html>
