<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use app\common;

class System extends Base{

	public function __construct(){
		parent::__construct();
		if($this->otype != 3){
			echo '死��!';exit;
		}
	}

	/**
	 * ��信�管�
	 * @author lukui  2017-02-17
	 * @return [type] [description]
	 */
	public function setbasic()
	{
		if (input('post.')) {
			$data = input('post.');
			$data['webname'] = trim($data['webname']);
			if(!$data['webname']){
				return WPreturn('请�平���!',-1);
			}
			if(!isset($data['webisopen']) || $data['webisopen'] == ''){
				$data['webisopen'] = 0;
			}else{
				$data['webisopen'] = 1;
			}



			if(!isset($data['festivalisrec']) || $data['festivalisrec'] == ''){
				$data['festivalisrec'] = 0;
			}else{
				$data['festivalisrec'] = 1;
			}

			if($data['id']){ //��
				 $editconf = Db::name('conf')->update($data);
	            if($editconf){
	            	//���罥�� 永�
	            	cache('page', $data['pagenum']);
	            	cache('conf', $data);
	                return WPreturn('�',1);
	            }else{
	                return WPreturn('�失败',-1);
	            }
			}else{ //丨�
				$addid = Db::name('conf')->insert($data);
	            if($addid){
	            	//���罥��永�
	            	cache('page', $data['pagenum']);
	            	cache('conf', $data);
	                return WPreturn('添�',1);
	            }else{
	                return WPreturn('添�失败',-1);
	            }
			}




		}else{

			$conf = Db::name('conf')->find();
			$this->assign($conf);
			return $this->fetch();
		}

	}


	/**
	 * 添�管�
	 * @author lukui  2017-02-18
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function addrole($value='')
	{


	}


	/**
	 * ��夻���
	 * @author lukui  2017-02-17
	 * @return [type] [description]
	 */
	public function dbbase()
	{
		$file = $data = array();
		$dir = "./databak/";
		$file = scandir($dir);
		unset($file[0]);
		unset($file[1]);
		$i = 1;
		foreach ($file as $key => $value) {
			$data[$key]['filename'] = $value;
			$data[$key]['id'] = $i;
			$handle = fopen($dir.$value,"r");
			$fstat= fstat($handle);
			$data[$key]['size'] = round($fstat["size"]/1024,2)."kb";
			$data[$key]['time'] = date("Y-m-d H:i:s",$fstat["mtime"]);
			$i++;
		}
		rsort($data);

		$this->assign('database',$data);
		return $this->fetch();
	}

	/**
	 * ��夻����
	 * @author lukui  2017-02-17
	 * @return [type] [description]
	 */
	public function backupsbase()
	{

		$type=input("tp");
        $name=input("name");
        $sql=new \org\Baksql(\think\Config::get("database"));
        switch ($type)
        {
        case "backup": //夻�
          return $sql->backup();
          break;
        case "dowonload": //丽�
          $sql->downloadFile($name);
          break;
        case "restore": //�
          return $sql->restore($name);
          break;
        case "del": //
          return $sql->delfilename($name);
          break;
        default: //��夻�����
            return $this->fetch("db_bak",["list"=>$sql->get_filelist()]);

        }


	}




	/**
	 * 徿���
	 * @author lukui  2017-02-17
	 * @return [type] [description]
	 */
	public function setwx()
	{
		if (input('post.')) {
			$data = input('post.');
			foreach ($data as $key => $value) {
				$data[$key] = trim($value);
 			}


			if($data['wcid']){ //��
				 $editwechat = Db::name('wechat')->update($data);
	            if($editwechat){
	                return WPreturn('�',1);
	            }else{
	                return WPreturn('�失败',-1);
	            }
			}else{ //丨�
				$addid = Db::name('wechat')->insert($data);
	            if($addid){
	                return WPreturn('添�',1);
	            }else{
	                return WPreturn('添�失败',-1);
	            }
			}
		}else{

			//徿���信�
			$wechat = Db::name('wechat')->find();
			$this->assign($wechat);
			return $this->fetch();
		}

	}


	/**
	 * 幻� 添��
	 * @author lukui  2017-03-26
	 * @return [type] [description]
	 */
	public function homepic()
	{
		if(input('post.')){
			$path =  '/public/uploads/';

            $file = request()->file('img');

            if(!isset($file)){
                $this->error("请��");
            }

            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                $data['img'] = $path.$info->getSaveName();
            }else{
                echo $file->getError();
            }

            $ids = Db::name('slide')->insert($data);
            if($ids){
            	$this->success('添�',url('system/homepic'),1,1);
            }else{
            	$this->error('添�失败��');
            }

            die;

		}else{
			$slide = Db::name('slide')->where(array('isdelete'=>0))->select();
			$this->assign('slide',$slide);
			return $this->fetch();
		}


	}

	/**
	 * 幻�
	 * @author lukui  2017-03-26
	 * @return [type] [description]
	 */
	public function delslide()
	{

		$id = input('get.id',0);
        if(!$id){
            return WPreturn('',-1);
        }

        $del = Db::name('slide')->where('id', $id)->update(['isdelete' => 1]);
        if($del){
            return WPreturn('',1);
        }else{
            return WPreturn('失败',-1);
        }

	}

	/**添�管�
	*/
	public function adminadd()
	{
		if(input('post.')){


		$data = input('post.');

		$data = array_filter($data);
		unset($data['upwd2']);

		if(isset($data['uid'])){
			$user = Db::name('userinfo')->where('uid',$data['uid'])->field('utel,upwd,utime')->find();


			if(isset($data['upwd']) && !empty($data['upwd'])){



				$utime = Db::name('userinfo')->where('uid',$data['uid'])->value('utime');

				if(!isset($data['upwd']) || empty($data['upwd'])){
					return WPreturn('请输入密码!',-1);
				}
				if(isset($data['upwd']) && isset($data['upwd2']) && $data['upwd'] != $data['upwd2']){
					return WPreturn('两次密码不相同!',-1);
				}
				unset($data['upwd2']);
				unset($data['ordpwd']);
				$data['upwd'] = md5($data['upwd']);

			}



			$ids = Db::name('userinfo')->update($data);
		}else{
			$data['username'] = $data['nickname'];
			$data['utime'] = time();
			$data['upwd'] = md5($data['upwd']);
			$data['otype'] = 101;

			$isset = Db::name('userinfo')->where(array('username'=>$data['username']))->value('uid');
			if($isset) return WPreturn('此账号已存在!',-1);
			$ids = Db::name('userinfo')->insertGetId($data);
		}


		if ($ids) {
			return WPreturn('添加成功!',1);
		}else{
			return WPreturn('失败,请重试!',-1);
		}
	}
	if(input('param.uid')){
		$map['uid'] = input('param.uid');
		$user = db('userinfo')->where($map)->find();
		$this->assign($user);
		$this->assign('isedit',1);

	}
		return $this->fetch();
	}


	public function adminlist()
	{
		$map['otype'] = array('IN',array(101,3));
		$list = Db::name('userinfo')->where($map)->order('uid asc')->select();
		$this->assign('list',$list);
		return $this->fetch();
	}

	/**
	 * 渨�openid
	 * @author lukui  2017-05-31
	 * @return [type] [description]
	 */
	public function clearopenid()
	{
		
		$data['openid'] = null;
		$where['uid'] = array('gt',0);
		$ids = db('userinfo')->where($where)->update($data);

		if( $ids ){
			$this->success('成功');
		}else{
			$this->error('失败');
		}


	}





	/**
	 * ����
	 * @author lukui  2017-07-03
	 * @return [type] [description]
	 */
	public function banks()
	{
		

		$banks = db('banks')->select();

		$this->assign('banks',$banks);
		return $this->fetch();

	}

	/**
	 * ��
	 * @author lukui  2017-07-03
	 * @return [type] [description]
	 */
	public function deletebanks()
	{
		
		$id = input('id');
		$ids = db('banks')->where('id',$id)->delete();
		if($ids){
			return WPreturn('',1);
		}else{
			return WPreturn('失败',-1);
		}
		

	}

	/**
	 * 缶�
	 * @author lukui  2017-07-05
	 * @return [type] [description]
	 */
	public function editbanks()
	{
		

		$post = input('post.');

		if(isset($post['id']) && !empty($post['id'])){
			$ids = db('banks')->update($post);
		}else{
			unset($post['id']);
			$ids = db('banks')->insert($post);
		}
		if($ids){
			return WPreturn('',1);
		}else{
			return WPreturn('失败',-1);
		}
		
	}

	/**
	 * ���
	 * @author lukui  2017-07-05
	 * @return [type] [description]
	 */
	public function recharge()
	{
		
		$payment =db('payment')->where('isdelete',0)->order('pay_order desc')->select();
		
		$this->assign('payment',$payment);
		return $this->fetch();

	}

	/**
	 * 添�込��
	 * @author lukui  2017-07-05
	 * @return [type] [description]
	 */
	public function addrech()
	{
		
		$post = input('post.');
		if($post){

			$data['pay_name'] = trim($post['pay_name']);
			if($post['is_use'] != 1){
				$data['is_use'] = 0;
			}else{
				$data['is_use'] = $post['is_use'];
			}

			if($post['pay_point'] < 0 || $post['pay_point'] > 100){
				$data['pay_point'] = 0;
			}else{
				$data['pay_point'] = empty($post['pay_point'])?0:$post['pay_point'];
			}
			
			if(empty($post['pay_conf'])){
				return WPreturn('请��罿�',-1);
			}
			
			$data['pay_conf'] = $post['pay_conf'];
			$data['isdelete'] = 0;
			$data['dotime'] = time();
			$data['pay_order'] = $post['pay_order'];
			$data['account_no'] = $post['account_no'];

			// ��
			$file = request()->file('thumb');
			if($file){				
				$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
				if($info){
					$data['thumb'] = '/public' . DS . 'uploads/'.$info->getSaveName();
				}
			}

			if(isset($post['id']) && !empty($post['id'])){
				$data['id'] = $post['id'];
				$ids = db('payment')->update($data);
			}else{
				$ids = db('payment')->insert($data);
			}
			if($ids){
				return $this->success('');
			}else{
				return $this->error('失败');
			}
			
		}

		$id = input('id');
		if($id && is_numeric($id)){

			$payment = db('payment')->where('id',$id)->find();
			if(!$payment){
				$this->error('�');
			}
			
			
			
			$this->assign($payment);
		}
		
		return $this->fetch();
	}

	/**
	 * ��
	 * @author lukui  2017-07-05
	 * @return [type] [description]
	 */
	public function deletepay()
	{
		
		$id = input('id');
		$ids = db('payment')->where('id',$id)->update(array('isdelete'=>1));
		if($ids){
			return WPreturn('',1);
		}else{
			return WPreturn('失败',-1);
		}

	}

	// ���
	public function sysbank(){
		if(request()->isPost()){
			$post = input('post.');
			db('sysbank')->update($post);
			return WPreturn('',1);
		}else{
			$sysbank = db('sysbank')->find();
			$this->assign('sysbank', $sysbank);
			return $this->fetch();
		}
	}

	public function inlet(){
		if(request()->isPost()){
			$Post = input('post.');
			@file_put_contents(UPLOADS_PATH . 'inlet.txt',$Post['inlet']);
			@file_put_contents(UPLOADS_PATH . 'project.txt',$Post['project']);
			$this->success('编辑成功',$_SERVER['HTTP_REFERER'],'',1);
		}else{
			$data['inlet'] = @file_get_contents(UPLOADS_PATH . 'inlet.txt');
			$data['project'] = @file_get_contents(UPLOADS_PATH . 'project.txt');
			$this->assign('data', $data);
			return $this->fetch();
		}
	}

	// public function backup(){
	// 		$db = $this->DB;
	// 		var_dump($db->dataList());
	// 	}



}
