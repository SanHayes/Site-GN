<?php
namespace app\apk\controller;
use think\Controller;
use think\Db;
use think\Loader;

class Base extends Controller
{
    public function __construct(){
		parent::__construct();
        $HTTP_HOST = $_SERVER['HTTP_HOST'];
        $HTTP_HOST_ARR = explode('.',$HTTP_HOST);
        // if($this->verify()){
        //     $url = (isHttps() ? 'https://' : 'http://').$HTTP_HOST_ARR[1].'.'.$HTTP_HOST_ARR[2].'/99/';
        //     $this->redirect($url, [], 302, []);
        // }
	    session(['prefix' => '', 'expire' => 60*60*24]);
		$this->token = md5(time());
		$this->assign('token',$this->token);

		$fid = input('get.fid');
		if($fid){
			$_SESSION['fid'] = $fid;
			if(!isset($_SESSION['uid'])){
				$this->redirect(url('apk/login/register',['token'=>$this->token]));
			}
		}

		$host = explode('.',$_SERVER['HTTP_HOST']);
		if($host[0]=='admin'){
			$this->redirect(url('admin/index/index'));
		}

		//验证登录
		if(!isset($_SESSION['uid'])){
			//$this->error('请先登录！','index.php/index/user/login',1,1);
			$this->redirect(url('apk/login/login',['token'=>$this->token]));
		}
		

		$this->uid = $_SESSION['uid'];
		$this->user = db('userinfo')->where('uid',$this->uid)->find();

		if(!$this->user){
			session_unset();
			$this->redirect(url('apk/login/login',['token'=>$this->token]));
		}

		if($this->uid && $this->user['ustatus']==1){
			session_unset();
			$this->redirect(url('apk/login/login',['token'=>$this->token]));
		}

		if($this->uid && $_SESSION['upwd']!=$this->user['uid'].$this->user['upwd']){
			session_unset();
			$this->redirect(url('apk/login/login',['token'=>$this->token]));
		}

		if($this->uid){
			db('userinfo')->where(['uid'=>$this->uid])->update(['online'=>1,'update_time'=>time()]);
		}

		$this->user['today_count'] = db('order')->where('uid', $this->user['uid'])->whereBetween('selltime', [
			strtotime(date("Y-m-d"),time()),
			strtotime(date('Y-m-d',strtotime('+1 day'))),
		])->sum('ploss');
		if (!$this->user['today_count']) {
			$this->user['today_count'] = '0.00';
		}
		$this->user['yesterday_count'] = db('order')->where('uid', $this->user['uid'])->whereBetween('selltime', [
			strtotime(date('Y-m-d',strtotime('-1 day'))),
			strtotime(date("Y-m-d"),time()),
		])->sum('ploss');
		if (!$this->user['yesterday_count']) {
			$this->user['yesterday_count'] = '0.00';
		}
		$this->user['invest_count'] = db('userinvest')->where('uid', $this->user['uid'])->sum('money');
		if (!$this->user['invest_count']) {
			$this->user['invest_count'] = '0.00';
		}

		$this->assign('userinfo',$this->user);
		//网站配置信息
		$this->conf = getconf('');
		if($this->conf['is_close'] != 1){
            header('Location:/error.html');
            exit;
        }
		$this->assign('conf',$this->conf);
	}
	
	public function verify(){
        return isset($_COOKIE['verify']) ? false : true;
    }

	protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
    	$replace['__HOME__'] = str_replace('/index.php','',\think\Request::instance()->root()).'/static/index';
        return $this->view->fetch($template, $vars, $replace, $config);
    }
}
