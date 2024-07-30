<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:71:"/www/wwwroot/default/Web/99/application/admin/view/price/pricelist.html";i:1722261189;s:60:"/www/wwwroot/default/Web/99/application/admin/view/head.html";i:1722261189;s:60:"/www/wwwroot/default/Web/99/application/admin/view/menu.html";i:1722261189;s:60:"/www/wwwroot/default/Web/99/application/admin/view/foot.html";i:1722261189;}*/ ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Mosaddek">
    <meta name="keyword" content="FlatLab, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
    <link rel="shortcut icon" href="/favicon.ico">

    <title>后台管理系统</title>

    <!-- Bootstrap core CSS -->
    <link href="__ADMIN__/css/bootstrap.min.css" rel="stylesheet">
    <link href="__ADMIN__/css/bootstrap-reset.css" rel="stylesheet">
    <!--external css-->
    <link href="__ADMIN__/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="__ADMIN__/assets/jquery-easy-pie-chart/jquery.easy-pie-chart.css" rel="stylesheet" type="text/css" media="screen"/>
    <link rel="stylesheet" href="__ADMIN__/css/owl.carousel.css" type="text/css">
    <!-- Custom styles for this template -->
    <link href="__ADMIN__/css/style.css" rel="stylesheet">
    <link href="__ADMIN__/css/style-responsive.css" rel="stylesheet" />
    <link href="__ADMIN__/css/addstyle.css" rel="stylesheet">
    
    <script src="__ADMIN__/js/jquery-1.8.3.min.js"></script>
    <script src="/static/layer/layer.js"></script>

    <!-- 时间选择器 -->
    <link rel="stylesheet" type="text/css" href="__ADMIN__/css/jquery.datetimepicker.css"/>
    
  </head>

  <body>

  <section id="container" class="">
      <!--header start-->
      <header class="header white-bg">
            <div class="sidebar-toggle-box">
                <div data-original-title="显示/隐藏" data-placement="right" class="icon-reorder tooltips"></div>
            </div>
            <!--logo start-->
            <a href="#" class="logo"><span>双赢系统</span></a>
            <!--logo end-->
            
            <div class="top-nav ">
                <!--search & user info start-->
                <ul class="nav pull-right top-menu">

                    <?php if(isset($_SESSION['username'])): ?>

                    <li><a href="<?php echo url('admin/user/userlist'); ?>?online=1" title="在线会员">在线 <span class="remind" id="online">0</span></a></li>
                    <li><a href="<?php echo url('admin/user/userprice'); ?>" title="充值记录">充值 <span class="remind" id="recharge">0</span></a><span class="volume" id="volume0"></span></li>
                    <li><a href="<?php echo url('admin/user/cash'); ?>" title="提款记录">提款 <span class="remind" id="withdraw">0</span></a><span class="volume" id="volume1"></span></li>
                    <li><a href="<?php echo url('admin/order/orderlist'); ?>" title="交易订单">订单 <span class="remind" id="dingdan">0</span></a><span class="volume" id="volume2"></span></li>

                    <!-- user login dropdown start-->
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            
                            <span class="username"><?php echo !empty($_SESSION['username'])?$_SESSION['username']:''; ?></span>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu extended logout">
                            <li><a href="<?php echo Url('login/logout'); ?>"><i class="icon-signout"></i> 退出</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <!-- user login dropdown end -->
                </ul>
                <!--search & user info end-->
            </div>
        </header>
<!--header end-->


<style type="text/css">
.header .remind{color:red; font-weight:bold; border: 1px solid #f1f4f5; padding: 1px 5px;}
.header .top-nav ul.top-menu > li a{display: inline-block;}
.header .top-nav ul.top-menu > li span.volume{display: inline-block; width: 19px; height: 26px; vertical-align: middle; margin: -6px 0; background: url(/static/admin/sound/volume-ico.png) no-repeat;}
.header .top-nav ul.top-menu > li span.volume-off{background-position: 0 -25px;}
</style>

<script type="text/javascript">
$(function(){
    remind();
    setInterval(remind, 10000);
	var volume0 = localStorage.getItem("volume0"), volume1 = localStorage.getItem("volume1"), volume2 = localStorage.getItem("volume2");
	if(volume0) $('.header #volume0').addClass(volume0);
	if(volume1) $('.header #volume1').addClass(volume1);
	if(volume2) $('.header #volume2').addClass(volume2);
    $('.header .volume').on('click',function(){
        var hasc = $(this).hasClass('volume-off'), vid = $(this).attr('id');
        if(hasc){
            $(this).removeClass('volume-off');
			localStorage.setItem(vid,'');
        }else{
            $(this).addClass('volume-off');
			localStorage.setItem(vid,'volume-off');
        }
    });
});
function remind(){
    $.getJSON("<?php echo url('admin/index/remind'); ?>", function (result) {
        if (result.state) {
            var online = result.data.online;
            var recharge = result.data.recharge;
            var withdraw = result.data.withdraw;
			var dingdan = result.data.orders;
            $('#online').html(online);
            $('#recharge').html(recharge);
            $('#withdraw').html(withdraw);
            $('#dingdan').html(dingdan);
            if(recharge>0){
                playVoice('/static/admin/sound/recharge.wav', 'recharge-voice', 0);
                if(withdraw>0){
                    setTimeout(function () {
                        playVoice('/static/admin/sound/withdraw.wav', 'cash-voice', 1);
                    },3000)
                }
            }else if(withdraw>0){
                playVoice('/static/admin/sound/withdraw.wav', 'cash-voice', 1);
            }else{
                $('#withdraw').css('background','#ffb800');
                $('#recharge').css('background','#ffb800');
            }
			if(dingdan>0){
				playVoice('/static/admin/sound/orders.wav', 'dingdan-voice', 2);
			}
        }
    })
}
function playVoice(src, domId, idx) {
    var $dom = $('#' + domId);
    var hasc = $('.header #volume'+idx).hasClass('volume-off');
    if(hasc) return;
    if (/msie/.test(navigator.userAgent.toLowerCase())) {
        // IE用bgsound标签处理声音
        if ($dom.length) {
            $dom[0].src = src;
        } else {
            $('<bgsound>', {src: src, id: domId}).appendTo('body');
        }
    } else {
        // IE以外的其它浏览器用HTML5处理声音
        if ($dom.length) {
            $dom[0].play();
        } else {
            $('<audio>', {src: src, id: domId}).appendTo('body')[0].play();
        }
    }
};
</script>

<!--sidebar start-->
      <aside>
          <div id="sidebar"  class="nav-collapse ">
              <!-- sidebar menu start-->
              <ul class="sidebar-menu">
                  <li <?php if($actionname == 'index' && $contrname == 'Index'): ?> class="active" <?php endif; ?> >
                      <a class="" href="<?php echo Url('admin/index/index'); ?>">
                          <i class="icon-dashboard"></i>
                          <span>仪表盘</span>
                      </a>
                  </li>
				  <li  class="sub-menu " >
                      <!--<a href="javascript:;" class="">-->
                          <!--<i class="icon-book"></i>-->
                          <!--<span>在线客服</span>-->
                          <!--<span class="arrow"></span>-->
                      </a>
                      <ul class="sub">
                          <!--<li ><a href="http://zumsxid.cn/admin/login/index/business_id/1.html"target="_blank">登陆客服</a></li>-->
                          <!--<li ><a class="" href="http://zumsxid.cn/index/index/home?visiter_id=&visiter_name=&avatar=&business_id=7&groupid=0&special=7"target="_blank">测试客服</a></li>-->
                          
                      </ul>
                  </li>
                  <!--
                  <li <?php if($contrname == 'Index' && (in_array($actionname,array('contentclass','contentlist','contentadd')))): ?> class="active" <?php else: ?> class="sub-menu " <?php endif; ?>>
                      <a href="javascript:;" class="">
                          <i class="icon-book"></i>
                          <span>内容管理</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                          <li <?php if($actionname == 'contentclass'): ?> class="active" <?php endif; ?>><a href="<?php echo Url('admin/index/contentclass'); ?>">栏目管理</a></li>
                          <li <?php if($actionname == 'contentlist' || $actionname == 'contentadd'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/index/contentlist'); ?>">文章管理</a></li>
                          
                      </ul>
                  </li>
                  -->

                  <?php if($otype == 3): ?>
                  <li <?php if($contrname == 'Goods'): ?> class="active" <?php else: ?> class="sub-menu " <?php endif; ?>>
                      <a href="javascript:;" class="">
                          <i class="icon-btc"></i>
                          <span>产品管理</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                          <li <?php if($actionname == 'prolist' || $actionname == 'proadd'): ?> class="active" <?php endif; ?>><a  href="<?php echo Url('admin/goods/prolist'); ?>">产品列表</a></li>
                          <!--
                          <li <?php if($actionname == 'proclass'): ?> class="active" <?php endif; ?>><a  href="<?php echo Url('admin/goods/proclass'); ?>">产品分类</a></li>-->
                          <li <?php if($actionname == 'risk'): ?> class="active" <?php endif; ?>><a  href="<?php echo Url('admin/goods/risk'); ?>">风控管理</a></li>
                          <li <?php if($actionname == 'huishou'): ?> class="active" <?php endif; ?>><a  href="<?php echo Url('admin/goods/huishou'); ?>">垃圾管理</a></li>

                          
                      </ul>
                  </li>
                  <?php endif; ?>
                  <li <?php if($contrname == 'Order'): ?> class="active" <?php else: ?> class="sub-menu " <?php endif; ?>>
                      <a href="javascript:;" class="">
                          <i class="icon-paste"></i>
                          <span>订单管理</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                          <li <?php if($actionname == 'orderlist'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/order/orderlist'); ?>">交易流水</a></li>
                          <li <?php if($actionname == 'orderlog'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/order/orderlog'); ?>">平仓日志</a></li>                         
                      </ul>
                  </li>

                  <li <?php if($contrname == 'User' && ( in_array($actionname,array('userlist','useradd','userprice','bankcard','userinfo','cash','myteam','chongzhi','blacklist')) )): ?> class="active" <?php else: ?> class="sub-menu " <?php endif; ?>>
                      <a href="javascript:;" class="">
                          <i class="icon-user"></i>
                          <span>用户管理</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                          <li <?php if(in_array($actionname,array('userlist','useradd'))): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/user/userlist'); ?>">客户列表</a></li>
                          
                          <!--
                          <li <?php if(in_array($actionname,array('myteam'))): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/user/myteam'); ?>">我的团队</a></li>
                          -->

                          <li <?php if($actionname == 'bankcard'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/user/bankcard'); ?>">会员银行</a></li>
                          
                          <li <?php if($actionname == 'userprice'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/user/userprice'); ?>">充值列表</a></li>

                          <li <?php if($actionname == 'cash'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/user/cash'); ?>">提现列表</a></li>
                          <li <?php if($actionname == 'blacklist'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/user/blacklist'); ?>">封黑名单</a></li>
                          <?php if($otype == 3): ?>
                          <li <?php if($actionname == 'chongzhi'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/user/chongzhi'); ?>">手动充提</a></li>
                          <?php endif; ?>
                      </ul>
                  </li>
				  
                  <?php if($otype == 3): ?>
                  <li <?php if($contrname == 'Rates'): ?> class="active" <?php else: ?> class="sub-menu " <?php endif; ?>>
                      <a href="javascript:;" class="">
                          <i class="icon-pinterest"></i>
                          <span>期货建仓</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                          <li <?php if($actionname == 'invest'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/rates/invest'); ?>">利息产品</a></li>
                          <li <?php if($actionname == 'userinvest'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/rates/userinvest'); ?>">客户投资</a></li>                         
                      </ul>
                  </li>
                  <?php endif; ?>
<!-- 
                  <li <?php if($contrname == 'User' && ( in_array($actionname,array('vipuseradd','vipuserlist','usercode')) )): ?> class="active" <?php else: ?> class="sub-menu " <?php endif; ?>>
                      <a class="" href="javascript:;">
                          <i class="icon-user-md"></i>
                          <span>代理商管理 </span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                        
                          <li <?php if($actionname == 'vipuseradd'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/user/vipuseradd'); ?>">添加代理商</a></li>

                          <li <?php if(in_array($actionname,array('vipuserlist','usercode'))): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/user/vipuserlist'); ?>">代理商列表</a></li>


                      </ul>
                  </li>
                   -->
                  
                  <li <?php if($contrname == 'Price'): ?> class="active" <?php else: ?> class="sub-menu " <?php endif; ?>>
                      <a href="javascript:;" class="">
                          <i class="icon-yen"></i>
                          <span>报表管理</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                          
                          
                          <!-- <li <?php if($actionname == 'allot'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/price/allot'); ?>">红利报表</a></li>

                          <li <?php if($actionname == 'yongjin'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/price/yongjin'); ?>">佣金报表</a></li> -->

                          <li <?php if($actionname == 'pricelist'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/price/pricelist'); ?>">资金报表</a></li>

                          <li <?php if($actionname == 'myprice'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/price/myprice'); ?>">个人报表</a></li>
                          
                      </ul>
                  </li>
                  
                  <?php if($otype == 3): ?>
                  <li <?php if($contrname == 'Setup'): ?> class="active" <?php else: ?> class="sub-menu" <?php endif; ?>>
                      <a href="javascript:;" class="">
                          <i class="icon-paste"></i>
                          <span>参数设置</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">

                          <li <?php if($contrname == 'Setup' && $actionname == 'index'): ?> class="active" <?php endif; ?> >
                            <a class="" href="<?php echo Url('admin/Setup/index'); ?>">基本设置</a>
                          </li>

                          <li <?php if($contrname == 'Setup' && $actionname == 'proportion'): ?> class="active" <?php endif; ?> >
                            <a class="" href="<?php echo Url('admin/Setup/proportion'); ?>">参数设置</a>
                          </li>

                          <!--<li <?php if($contrname == 'Setup' && $actionname == 'reward'): ?> class="active" <?php endif; ?> >
                            <a class="" href="<?php echo Url('admin/Setup/reward'); ?>">邀请奖励</a>
                          </li>-->

                          <li <?php if($contrname == 'Setup' && ($actionname == 'notice' || $actionname == 'addnotice')): ?> class="active" <?php endif; ?> >
                            <a class="" href="<?php echo Url('admin/Setup/notice'); ?>">公告列表</a>
                          </li>
                          <li <?php if($contrname == 'Setup' && ($actionname == 'gallery' || $actionname == 'addgallery')): ?> class="active" <?php endif; ?> >
                            <a class="" href="<?php echo Url('admin/Setup/gallery'); ?>">首页轮播</a>
                          </li>
                          
                          <li  <?php if($contrname == 'Setup' && $actionname == 'addsetup'): ?> class="active" <?php endif; ?> >
                            <a class="" href="<?php echo Url('admin/Setup/addsetup'); ?>">添加配置</a>
                          </li>
                          <li  <?php if($contrname == 'Setup' && $actionname == 'deploy'): ?> class="active" <?php endif; ?> >
                            <a class="" href="<?php echo Url('admin/Setup/deploy'); ?>">配置管理</a>
                          </li>
                      </ul>
                  </li>
                  

                  <li <?php if($contrname == 'System'): ?> class="active" <?php else: ?> class="sub-menu" <?php endif; ?>>
                      <a href="javascript:;" class="">
                          <i class="icon-cogs"></i>
                          <span>系统设置</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                          <li <?php if($actionname == 'adminadd'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/system/adminadd'); ?>">添加管理员</a></li>
                          <li <?php if($actionname == 'adminlist'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/system/adminlist'); ?>">管理员列表</a></li>
                          <!-- <li <?php if($actionname == 'recharge' && $actionname ==  'recharge'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/system/recharge'); ?>">充值配置</a></li> -->
                          <li <?php if($actionname == 'recharge' && $actionname ==  'sysbank'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/system/sysbank'); ?>">充值银行卡</a></li>
                          <!--
                          <li <?php if($actionname == 'banks'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/system/banks'); ?>">提现银行卡</a></li>
                          
                          <li <?php if($actionname == 'setwx'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/system/setwx'); ?>">微信设置</a></li>
                          <li <?php if($actionname == 'dbbase'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/system/dbbase'); ?>">数据备份</a></li>
                        -->

                      </ul>
                  </li>

                  <!-- <li <?php if($contrname == 'Kefu'): ?> class="active" <?php else: ?> class="sub-menu" <?php endif; ?> >
                      <a href="javascript:;" class="">
                          <i class="icon-share"></i>
                          <span>客服设置</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                          <li <?php if($actionname == 'set'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/kefu/set'); ?>">聊天设置</a></li>
                          <li <?php if($actionname == 'words'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/kefu/words'); ?>">常用语设置</a></li>
                          <li <?php if($actionname == 'index'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/kefu/index'); ?>" target="_blank">客服工作台</a></li>
                      </ul>
                  </li>

                  <?php endif; ?> -->
                  <li <?php if($contrname == 'Kefu'): ?> class="active" <?php endif; ?>>
                    <a class="" href="<?php echo Url('admin/kefu/index'); ?>" target="_blank">
                      <i class="icon-share"></i>
                      <span>客服平台</span>
                    </a>
                  </li>
                  <li>
                      <a class="" href="<?php echo Url('admin/login/logout'); ?>">
                          <i class="icon-signout"></i>
                          <span>退出</span>
                      </a>
                  </li>
              </ul>
              <!-- sidebar menu end-->
          </div>
      </aside>
      <!--sidebar end-->




<!--main content start-->
      <section id="main-content">
          <section class="wrapper">
              <!--state overview start-->
              
              <div class="row state-overview">
                <div class="container">
                <div class="row">
                      <form action="<?php echo url('pricelist'); ?>" method="get">
                      <div class="col-lg-3 mar-10">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">用户名称</span>
                            <input type="text" value="<?php echo !empty($getdata['username'])?$getdata['username']:''; ?>" placeholder="昵称/姓名/手机号/编号" class="form-control" name="username" />
                        </div>
                      </div>

                      <div class="col-lg-6 mar-10">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">结算时间</span>
                            <input type="text"  id="datetimepicker" class="form-control" placeholder="点击选择时间" name="starttime" value="<?php echo !empty($getdata['starttime'])?$getdata['starttime']:''; ?>"/>
                            <span class="input-group-addon" id="basic-addon1">至</span>
                            <input type="text"  id="datetimepicker_end" class="form-control" placeholder="点击选择时间" name="endtime" value="<?php echo !empty($getdata['endtime'])?$getdata['endtime']:''; ?>" />
                        </div>
                      </div>

                    <div class="mar-10 col-lg-3">
                   <input type="submit" class="btn btn-success" value="搜索">
                  </div>

                      
                  </div>
                  

                  </form>
                </div>
                
              </div>
              <br><br><br>
            <div class="row state-overview">
                  <div class="col-lg-2 col-sm-2">
                      <section class="panel">
                          <div class="symbol red color_white">
                              <h5>入金总额</h5>
                          </div>
                          <div class="order-boo">
                              <h1 id="all_res"></h1>
                          </div>
                      </section>
                  </div>
                  <div class="col-lg-2 col-sm-2">
                      <section class="panel">
                          <div class="symbol gray color_white">
                              <h5>出金总额</h5>
                          </div>
                          <div class="order-boo">
                              <h1 id="all_cash"></h1>
                          </div>
                      </section>
                  </div>
                  <div class="col-lg-2 col-sm-2">
                      <section class="panel">
                          <div class="symbol blue color_white">
                              <h5>佣金总额</h5>
                          </div>
                          <div class="order-boo">
                              <h1 id="all_yj"></h1>
                          </div>
                      </section>
                  </div>

                  <div class="col-lg-2 col-sm-2">
                      <section class="panel">
                          <div class="symbol red color_white">
                              <h5>红利总额</h5>
                          </div>
                          <div class="order-boo">
                              <h1 id="all_hl"></h1>
                          </div>
                      </section>
                  </div>

                  <div class="col-lg-2 col-sm-2">
                      <section class="panel">
                          <div class="symbol terques color_white">
                              <h5>当日盈亏</h5>
                          </div>
                          <div class="order-boo">
                              <h1 id="tody_ploss"></h1>
                          </div>
                      </section>
                  </div>

                  <div class="col-lg-2 col-sm-2">
                      <section class="panel">
                          <div class="symbol blue color_white">
                              <h5>历史盈亏</h5>
                          </div>
                          <div class="order-boo">
                              <h1 id="all_lis"></h1>
                          </div>
                      </section>
                  </div>
                  
                  

              </div>
                      </div>

            <a href="<?php echo url('price/pricelist'); ?>"><button type="submit" class="btn btn-danger">搜索全部</button></a>&nbsp;&nbsp;&nbsp;&nbsp;
            
            <br><br>
             <div class="row">
                  <div class="col-lg-12">
                      <section class="panel">
                          <header class="panel-heading">
                              资金报表
                          </header>
                          <table class="table table-striped table-advance table-hover">
                            <thead class="ordertable">
                              <tr>
                                <th>用户</th>
                                <th>代理商</th>
                                <th>入金总额</th>
                                <th>入金次数</th>
                                <th>手动入金</th>
                                <th>出金总额</th>
                                <th>出金次数</th>
                                <th>出金审核中</th>
                                <th>佣金</th>
                                <th>红利</th>
                                <th>当前余额</th>
                                <!--<th>实际余额</th>-->
                                <th>净入金</th>
                                <th>当日盈亏</th>
                                <th>总盈亏</th>
                                <th>总手续费</th>
                            </tr>
                          </thead>
                          <tbody>
                          <!-- <?php if(is_array($_list) || $_list instanceof \think\Collection || $_list instanceof \think\Paginator): $i = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?> -->
                              <tr>
                                  
                                 <td><?php echo $vo['username']; ?></td>
                                 <td><?php echo !empty($vo['managername'])?$vo['managername']:'admin'; ?></td>
                                 <td><?php echo !empty($vo['all_res'])?$vo['all_res']:'0'; ?></td>
                                 <td><?php echo $vo['all_res_count']; ?></td>
                                 <td><?php echo !empty($vo['all_cash_shoudong'])?$vo['all_cash_shoudong']:'0'; ?></td>
                                 <td><?php echo !empty($vo['all_cash'])?$vo['all_cash']:'0'; ?></td>
                                 <td><?php echo $vo['all_cash_count']; ?></td>
                                 <td><?php echo !empty($vo['all_cash_shenhe'])?$vo['all_cash_shenhe']:'0'; ?></td>
                                 <td><?php echo !empty($vo['all_yj'])?$vo['all_yj']:'0'; ?></td>
                                 <td><?php echo !empty($vo['all_hl'])?$vo['all_hl']:'0'; ?></td>
                                 <td><?php echo !empty($vo['usermoney'])?$vo['usermoney']:'0'; ?></td>
                                 <!--<td><?php echo !empty($vo['shiji_money'])?$vo['shiji_money']:'0'; ?></td>-->
                                 <td><?php echo !empty($vo['res_cash'])?$vo['res_cash']:'0'; ?></td>
                                 <td><?php echo !empty($vo['tody_ploss'])?$vo['tody_ploss']:'0'; ?></td>
                                 <td><?php echo !empty($vo['all_ploss'])?$vo['all_ploss']:'0'; ?></td>
                                 <td><?php echo !empty($vo['all_sx_fee'])?$vo['all_sx_fee']:'0'; ?></td>
                                  
                                  
                              </tr>
							           <!-- <?php endforeach; endif; else: echo "" ;endif; ?> -->
                             
                              </tbody>
                          </table>
                      </section>
                  
              
                      <?php echo $list->render(); ?>
                </div>
              </div>


          </section>


      </section>
      <!--main content end-->
  </section>


    <!-- js placed at the end of the document so the pages load faster -->
    
    <script src="__ADMIN__/js/bootstrap.min.js"></script>
    <script src="__ADMIN__/js/jquery.scrollTo.min.js"></script>
    <script src="__ADMIN__/js/jquery.nicescroll.js" type="text/javascript"></script>
    <script src="__ADMIN__/js/jquery.sparkline.js" type="text/javascript"></script>
    <script src="__ADMIN__/assets/jquery-easy-pie-chart/jquery.easy-pie-chart.js"></script>
    <script src="__ADMIN__/js/owl.carousel.js" ></script>
    <script src="__ADMIN__/js/jquery.customSelect.min.js" ></script>

    <!--common script for all pages-->
    <script src="__ADMIN__/js/common-scripts.js"></script>

    <!--script for this page-->
    <script src="__ADMIN__/js/sparkline-chart.js"></script>
    <script src="__ADMIN__/js/easy-pie-chart.js"></script>

    <!-- active -->
    <script src="/static/public/js/function.js"></script>
    <!-- date -->
    <script type="text/javascript" src="__ADMIN__/js/date/jquery.datetimepicker.js" charset="UTF-8"></script>
  </body>
</html>

<script>  
pricesta();
function pricesta(){
  var formurl = "<?php echo url('/admin/price/pricesta'); ?>";
  var data  = '<?php echo $jsongetdata; ?>';
  
console.log(data);
  
  $.post(formurl,data,function(data){

      var obj = JSON.parse(data);
      $.each(obj,function(k,v){
        if(!v){
          v=0;
        }
        $('#'+k).html(v);
      })
      


    });
    
}

//时间选择器
$('#datetimepicker').datetimepicker();
$('#datetimepicker_end').datetimepicker();


</script>