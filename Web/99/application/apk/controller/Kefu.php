<?php 
namespace app\apk\controller;
use think\Controller;

class Kefu extends Controller{

	public function index(){
		$uid = $_SESSION['uid'];
		$host = 'kefu.'.str_replace(['www.','app.','wap.'],'',$_SERVER['HTTP_HOST']);
		$code = encrypt($uid, 'E', $_SERVER['SERVER_ADDR'].$host);
		$url = $host.'/wap/kefu/login?btcode='.$code;
		header('Location:http://'.$url);//使HTTP返回404状态码 
	}

}
