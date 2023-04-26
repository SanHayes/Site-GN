<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Base extends Controller
{
    public function __construct(){

		parent::__construct();
		/*
		$host = explode('.', $_SERVER['HTTP_HOST']);
		if($host[0]!='gm518btb'){
			header("HTTP/1.0 404 Not Found");//使HTTP返回404状态码 
			header("status: 404 not found");
			exit;
		}
		*/
		//验证登录
		$login = session('denglu');
		if(!isset($login['userid'])){
			$this->error('请先登录！','login/login',1,1);
		}
		
		if(!isset($login['token']) || $login['token'] != md5('nimashabi')){
			$this->redirect('login/logout');
		}

		$request = \think\Request::instance();
		
		$contrname = $request->controller();
        $actionname = $request->action();
        
        $this->assign('contrname',$contrname);
        $this->assign('actionname',$actionname);

        
        $this->otype = $login['otype'];
        $this->uid = $login['userid'];

        $this->assign('otype',$this->otype);
	}

	protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
    	$replace['__ADMIN__'] = str_replace('/index.php','',\think\Request::instance()->root()).'/static/admin';
        return $this->view->fetch($template, $vars, $replace, $config);
    }
}
