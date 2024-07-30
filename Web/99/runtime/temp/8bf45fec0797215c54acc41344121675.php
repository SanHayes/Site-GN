<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:66:"/www/wwwroot/default/Web/99/application/admin/view/goods/risk.html";i:1722315029;s:60:"/www/wwwroot/default/Web/99/application/admin/view/head.html";i:1722315029;s:60:"/www/wwwroot/default/Web/99/application/admin/view/menu.html";i:1722315029;s:60:"/www/wwwroot/default/Web/99/application/admin/view/foot.html";i:1722315029;}*/ ?>
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
                        playVoice('/static/admin/sound/cash.wav', 'cash-voice', 1);
                    },3000)
                }
            }else if(withdraw>0){
                playVoice('/static/admin/sound/cash.wav', 'cash-voice', 1);
            }else{
                $('#withdraw').css('background','#ffb800');
                $('#recharge').css('background','#ffb800');
            }
			if(dingdan>0){
				playVoice('/static/admin/sound/dingdan.mp3', 'dingdan-voice', 2);
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
                          <li <?php if($actionname ==  'inlet'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/system/inlet'); ?>">域名修改</a></li>
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
      <div class="col-sm-8">
          <aside class="profile-info col-lg-12">
          <section class="panel">
              <hr>
              <div class="panel-body bio-graph-info">
                  <form class="form-horizontal" role="form" action="" method="post" id="formid">
				  
                      <div class="form-group" style="display:none">
                          <label class="col-lg-3 control-label">结算时间段必赢：</label>
                          <div class="col-lg-8">
                                <input type="text" class="form-control" name="time1" placeholder="起始时间，如00:00" value="<?php echo $time1; ?>" style="width:160px;display:inline" />
                                 - 
                                <input type="text" class="form-control" name="time2" placeholder="结束时间，如23:59" value="<?php echo $time2; ?>" style="width:160px;display:inline" />
                                <div class="alert alert-block alert-danger fade in" style="margin:10px 0 0 0 ">
                                       <strong>说明：</strong> 时间设置在 00:00 ~ 23:59，不能跨天，注意区分半角全角符号。
                                  </div>
                          </div>
                          <label class="col-lg-3 control-label"></label>
                      </div>
            
                      <div class="form-group">
                          <label class="col-lg-3 control-label">指定客户亏损：</label>
                          <div class="col-lg-8">
                                <textarea name="to_loss"  class="form-control" cols="30" rows="6"><?php echo !empty($to_loss)?$to_loss:''; ?></textarea>
                                <div class="alert alert-block alert-danger fade in" style="margin:10px 0 0 0 ">
                                       <strong>说明：</strong> 此处设置会员ID（如：8888），多个用户用|符号分开（如：8888|9999）设置之后该会员所有订单都会亏损，请谨慎操作。如停止该功能请在上面留空或者填0，并提交。
                                  </div>
                          </div>
                          <label class="col-lg-3 control-label"></label>
                      </div>

                      <div class="form-group">
                          <label class="col-lg-3 control-label">指定客户赢利：</label>
                          <div class="col-lg-8">
                                <textarea name="to_win"  class="form-control" cols="30" rows="6"><?php echo !empty($to_win)?$to_win:''; ?></textarea>
                                <div class="alert alert-block alert-danger fade in" style="margin:10px 0 0 0 ">
                                       <strong>说明：</strong> 此处设置会员ID（如：8888），多个用户用|符号分开（如：8888|9999）设置之后该会员所有订单都会赢利，请谨慎操作。如停止该功能请在上面留空或者填0，并提交。
                                  </div>
                          </div>
                          <label class="col-lg-3 control-label"></label>
                      </div>

                      <div class="form-group" style="display:none">
                          <label class="col-lg-3 control-label">总止盈止损%：</label>
                          <div class="col-lg-8">
                                <input type="text" class="form-control" name="min_yk" placeholder="请填写最小止盈止损" value="<?php echo $min_yk; ?>" style="width:160px;display:inline" />
                                 - 
                                <input type="text" class="form-control" name="max_yk" placeholder="请填写最大止盈止损" value="<?php echo $max_yk; ?>" style="width:160px;display:inline" />
                          </div>
                          <label class="col-lg-3 control-label"></label>
                      </div>

                      <div class="form-group">
                          <label class="col-lg-3 control-label">时间1止盈止损%：</label>
                          <div class="col-lg-8">
                                <input type="text" class="form-control" name="min_yk1" placeholder="请填写最小止盈止损" value="<?php echo $min_yk1; ?>" style="width:160px;display:inline" />
                                 - 
                                <input type="text" class="form-control" name="max_yk1" placeholder="请填写最大止盈止损" value="<?php echo $max_yk1; ?>" style="width:160px;display:inline" />
                          </div>
                          <label class="col-lg-3 control-label"></label>
                      </div>

                      <div class="form-group">
                          <label class="col-lg-3 control-label">时间2止盈止损%：</label>
                          <div class="col-lg-8">
                                <input type="text" class="form-control" name="min_yk2" placeholder="请填写最小止盈止损" value="<?php echo $min_yk2; ?>" style="width:160px;display:inline" />
                                 - 
                                <input type="text" class="form-control" name="max_yk2" placeholder="请填写最大止盈止损" value="<?php echo $max_yk2; ?>" style="width:160px;display:inline" />
                          </div>
                          <label class="col-lg-3 control-label"></label>
                      </div>

                      <div class="form-group">
                          <label class="col-lg-3 control-label">时间3止盈止损%：</label>
                          <div class="col-lg-8">
                                <input type="text" class="form-control" name="min_yk3" placeholder="请填写最小止盈止损" value="<?php echo $min_yk3; ?>" style="width:160px;display:inline" />
                                 - 
                                <input type="text" class="form-control" name="max_yk3" placeholder="请填写最大止盈止损" value="<?php echo $max_yk3; ?>" style="width:160px;display:inline" />
                          </div>
                          <label class="col-lg-3 control-label"></label>
                      </div>

                      <div class="form-group" style="display:none">
                          <label class="col-lg-3 control-label">客户盈亏%：</label>
                          <div class="col-lg-8">
                                <input type="text" class="form-control" name="min_gain" placeholder="请填写最小客户盈亏" value="<?php echo $min_gain; ?>" style="width:160px;display:inline" />
                                 - 
                                <input type="text" class="form-control" name="max_gain" placeholder="请填写最大客户盈亏" value="<?php echo $max_gain; ?>" style="width:160px;display:inline" />
                                <div class="alert alert-block alert-danger fade in" style="margin:10px 0 0 0 ">
                                       <strong>说明：</strong> 客户盈亏最大为10，不要超过10 ！<br/>
                                       &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 若下注100，止盈止损为6，客户盈亏随机为8，则计算公式为：8/100*6/10*100=4.8。<br/>
                                       &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 若下注100，止盈止损为10，客户盈亏随机为8，则计算公式为：8/100*10/10*100=8。<br/>
                                  </div>
                          </div>
                          <label class="col-lg-3 control-label"></label>
                      </div>

                      <div class="form-group" style="display:none">
                          <label class="col-lg-3 control-label">盈亏百分百：</label>
                          <div class="col-lg-8">
                                <label><input type="radio" name="special_yk" id="special_yk" value="1" <?php if($special_yk==1): ?>checked<?php endif; ?> /> 开启</label>
                                <label><input type="radio" name="special_yk" id="special_yk" value="0" <?php if($special_yk==0): ?>checked<?php endif; ?> /> 关闭</label>
                                <div class="alert alert-block alert-danger fade in" style="margin:10px 0 0 0 ">
                                       <strong>说明：</strong> 指定的客户 96%~99% 全输全赢。
                                  </div>
                          </div>
                          <label class="col-lg-3 control-label"></label>
                      </div>

                      <div class="form-group">
                          <label class="col-lg-3 control-label">最小风控值：</label>
                          <div class="col-lg-8">
                                <input type="text" class="form-control" name="min_price" placeholder="请填写最小风控值" value="<?php echo !empty($min_price)?$min_price:''; ?>">
                                <div class="alert alert-block alert-danger fade in" style="margin:10px 0 0 0 ">
                                       <strong>说明：</strong> 下单达到此金额，则会受到下面风控影响。
                                  </div>
                          </div>
                          <label class="col-lg-3 control-label"></label>
                      </div>

                      <div class="form-group">
                          <label class="col-lg-3 control-label">风控概率：</label>
                          <div class="col-lg-8">
                                <input type="text" class="form-control" name="chance" placeholder="请填写客户ID" value="<?php echo !empty($chance)?$chance:''; ?>"/>
                                <div class="alert alert-block alert-danger fade in" style="margin:10px 0 0 0 ">
                                       <strong>说明：</strong> 输入金额区间，在金额区间之内会根据此概率盈亏。不在此区间则不受风控影响<br>
                                       <strong>格式：</strong> 区间开始-区间结束:客户赢利概率|　　符号必须为英文符号，如0-100:50|100-200:30
                                  </div>
                          </div>
                          <label class="col-lg-3 control-label"></label>
                      </div>

                      <hr/>

                      <div class="form-group" style="display:none">
                          <label class="col-lg-3 control-label">未指定客户盈亏%：</label>
                          <div class="col-lg-8">
                                <input type="text" class="form-control" name="min_lost" placeholder="请填写最小未指定客户盈亏" value="<?php echo $min_lost; ?>" style="width:160px;display:inline" />
                                 - 
                                <input type="text" class="form-control" name="max_lost" placeholder="请填写最大未指定客户盈亏" value="<?php echo $max_lost; ?>" style="width:160px;display:inline" />
                                <div class="alert alert-block alert-danger fade in" style="margin:10px 0 0 0 ">
                                       <strong>说明：</strong> 未指定客户盈亏 最大不超过止盈止损上限 ！<br/>
                                       &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 若下注100元，未指定客户盈亏为2%，则客户输赢不超过2元。<br/>
                                  </div>
                          </div>
                          <label class="col-lg-3 control-label"></label>
                      </div>
                      
                      <input type="hidden" name="id" value="<?php echo !empty($id)?$id:''; ?>">
                                  
                      <div class="form-group">
                          <div class="col-lg-offset-2 col-lg-10">
                              <input type="submit" class="btn btn-success" onclick="return editrisk(this.form)" value="提交"/>
                          </div>
                      </div>
                  </form>
              </div>
          </section>
          
      </aside>
      </div>

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

function editrisk(form) {
  
  var formurl = "<?php echo Url('goods/addrisk'); ?>"
    var data = $('#formid').serialize();
    var locurl = "<?php echo Url('goods/risk'); ?>";
    WPpost(formurl,data,locurl);
    return false;
}


</script>