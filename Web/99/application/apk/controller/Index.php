<?php
namespace app\apk\controller;
use think\Db;
use think\Cookie;



class Index extends Base
{

	/**
	 * 首页 行情列表
	 * @author lukui  2017-02-18
	 * @return [type] [description]
	 */
    public function index()
    {
        if(!input('token')){
            //$this->redirect('apk/login/login?token='.$this->token);
        }
		if($this->conf['sys_homepage']==1){
			$this->redirect(Url('/apk/index/home',['token' => $this->token]));
		}
        //获取产品信息
        $pro = db('productdata')->where('isdelete',0)->order('sort')->select();
        $img_data = Db::name('productinfo')->field('pid,img')->select();
        $imgarr = array();
        foreach ($img_data as $kk => $vv){
            $imgarr[$vv['pid']] = $vv['img'];
        }
        foreach ($pro as $k => $v) {
        	$pro[$k]['img'] = $imgarr[$v['pid']];
        }
        $this->assign('pro',$pro);
        $notices = db('notice')->where(['state' => 1])->order('id desc')->limit(3)->select();
        $this->assign('notices', $notices);
        if (!isset($_SESSION['notices'])) {
            $this->assign('notices_tags', true);
            $_SESSION['notices'] = 1;
        }else{
            $this->assign('notices_tags', false);
        }
        // 轮播图
		$gallery = db('gallery')->where(['state'=>1])->order('sort asc,id asc')->select();
        $this->assign('gallery', $gallery);
		// 判断注册登录送彩金
		$caijin = cache('caijin');
		if($caijin) cache('caijin', NULL);
		$this->assign('caijin',$caijin);
        return $this->fetch();
    }

	public function ajax_order(){
		$pro_length = 50;
		$phone_pre_arr = array("139","138","137","136","135","134","159","158","157","150","151","152","187","188","130","131","132","156","155","133","153","189");
		$phone_pre_length = count($phone_pre_arr);
		//$price_arr = array(100,200,300,400,500,600,700,800,500,500,1000,1000,5000,5000,10000,20000);
		//$price_arr_length = count($price_arr);
		$type_arr = array('买涨','买跌');
		$type_arr_length = count($type_arr);
		$order_pub = array();
		for($i=0;$i<$pro_length;$i++){
			//$rand_pid_index = rand(0,($pro_length - 1));
			$phone_pre_index = rand(0,($phone_pre_length - 1));
			//$rand_price_index = rand(0,($price_arr_length - 1));
			//$rand_type_index = rand(0,($type_arr_length - 1));

			$o_pub = array();
			//$o_pub['buytime'] = time();
			//$o_pub['pid'] = $id_arr[$rand_pid_index];
			$o_pub['phone'] = $phone_pre_arr[$phone_pre_index] . "****" . rand(1000,9999);


			//$o_pub['price'] = $price_arr[$rand_price_index];

			$o_pub['price'] = $this->getrd();

			/*
			else if(rand(1,100)>=80){
				$o_pub['price'] = 50 * rand(0,100);
			}
			*/

			//$o_pub['otype'] = $type_arr[$rand_type_index];
			array_push($order_pub,$o_pub);
		}


		//foreach($order_pub as $k => $v){
			//$order_pub[$k]['buytime'] = date("H:i:s",$v['buytime']);
		//}
		 echo json_encode($order_pub);
	}

    public function ajaxindexpro()
    {
    	//获取产品信息
        $pro = Db::name('productdata')->field('pid,Name ptitle,Price,UpdateTime,Low,High,img')->where('isdelete',0)->select();
        $img_data = Db::name('productinfo')->field('pid,img')->select();
        $imgarr = array();
        foreach ($img_data as $kk => $vv){
            $imgarr[$vv['pid']] = $vv['img'];
        }
        $newpro = array();
        foreach ($pro as $k => $v) {
        	$newpro[$v['pid']] = $pro[$k];
        	$newpro[$v['pid']]['UpdateTime'] = date('H:i:s',$v['UpdateTime']);
        	$newpro[$v['pid']]['img'] = $imgarr[$v['pid']];
        	if($v['Price'] < session('pid'.$v['pid']) ){  //跌了
        		$newpro[$v['pid']]['isup'] = 0;
        	}elseif($v['Price'] > session('pid'.$v['pid']) ){  //涨了
        		$newpro[$v['pid']]['isup'] = 1;
        	}else{  //没跌没涨
        		$newpro[$v['pid']]['isup'] = 2;
        	}
        	session('pid'.$v['pid'],$v['Price']);
        }
        return base64_encode(json_encode($newpro));
    }

    public function getchart()
    {

        $data['hangqing'] = '商品行情';
        $data['jiaoyijilu'] = '交易记录';
        $data['shangpinmingcheng'] = '商品名称';
        $data['xianjia'] = '现价';
        $data['zuidi'] = '最低';
        $data['zuigao'] = '最高';
        $data['xianjia'] = '现价';
        $data['xianjia'] = '现价';


        $res = base64_encode(json_encode($data));
        return $res;
    }

    public  function  getrd(){
        $rdarr=array(88,90,92,93,176,180,184,186,264,270,276,
            279,352,440,450,460,465,880,900,920,930,1760,1800,1840,
            4400,4500,4600,4650,8800,9000,9200,9300,17600,18000,18400,18600);

        return $rdarr[array_rand($rdarr)];
    }

    /**
     * 获取最新的动态数据
     */
    public  function ajaxdata() {
        $product = db('productdata')->field("pid,Name,price,isdelete")->where(array('isdelete' => 0))->select();

        foreach( $product as $k=>$val) {
         //   $rd = rand(-3,3);
          //  $product[$k]['price'] = $val['price'] +$rd*0.01*$val['price'];
            $lastprice= session('price'.$val['pid']);
            $product[$k]['is_rise']=($lastprice>=$val['price'])?1:2;
            $product[$k]['price'] = number_format($val['price'],4);
            session('price'.$val['pid'],$product[$k]['price']);
        }
        return  json_encode($product);
    }

    public  function home() {
        if (!isset($_SESSION['uid'])) {
            $this->redirect(Url('/apk/index/home',['token' => $this->token]));
        }
		// if($this->conf['sys_homepage']==2){
		// 	$this->redirect('apk/index/index?token='.$this->token);
		// }
        $product = db('productdata')->where(array('isdelete' => 0))->order('sort')->select();
        foreach ($product as $key=>$item) {
            $productinfo = db('productinfo')->where('pid', $item['pid'])->find();
            $item['isopen'] = $item['is_deal'];
            $item['img'] = $productinfo['img'];
            $item['Price'] = number_format($item['Price'],4);
            $product[$key] = $item;
        }
        $this->assign('pro', $product);
        $notices = db('notice')->where(['state' => 1])->order('id desc')->limit(3)->select();
        $this->assign('notices', $notices);
		// 轮播图
		$gallery = db('gallery')->where(['state'=>1])->order('sort asc,id asc')->select();
        $this->assign('gallery', $gallery);
		// 判断注册登录送彩金
		$caijin = cache('caijin');
		if($caijin) cache('caijin', NULL);
        $this->assign('hide', $this->hide());
		$this->assign('caijin',$caijin);
        return $this->fetch();
    }

    public function hide() {
        cookie('hide', true, 3600);
    }

    public function home1() {
        if (input('post.')) {
            $data = input('post.'); //前端对应的数据

            $result = db('userinfo')
                ->where('username', $data['username'])->whereOr('nickname', $data['username'])->whereOr('utel', $data['username'])
                ->field("uid,upwd,username,utel,utime,otype,ustatus")->find();
            $_SESSION['uid'] = $result['uid'];
            $_SESSION['sessionkey'] = rand(10000, 99999);
            $t_data['sessionkey'] = $_SESSION['sessionkey'];
            $t_data['logintime'] = time();
            $t_data['uid'] = $result['uid'];
            db('userinfo')->update($t_data);
        }
        $product = db('productdata')->where(array('isdelete' => 0))->select();        
        $this->assign('pro', $product);
        return $this->fetch();
    }

    /**
     * 用户的系统中心
     * @return mixed|string
     */
    public function mine() {
        $isDownload = db('config')->where('name', 'app_show')->value('value');
        $downloadUrl = db('config')->where('name', 'app_url')->value('value');

        $this->assign('isDownload', $isDownload);
        $this->assign('downloadUrl', $downloadUrl);
        $this->assign('userInfo', $this->user);
        return $this->fetch();
    }
  
      public function member() {
        $downloadUrl = db('config')->where('name', 'sys_app_url')->value('value');
        $this->assign('downloadUrl', $downloadUrl);
        $this->assign('userInfo', $this->user);
        return $this->fetch('mine');
    }

    public function minen() {

        return $this->fetch();
    }

    public function pay() {
        $conf = $this->conf;
        $arr=explode('|',$conf['reg_push']);
        $this->assign('reg_push',$arr);
        // 充值方式
        $payment = db('payment')->where(['is_use'=>1,'isdelete'=>0])->order('id desc')->select();
        $this->assign('payment',$payment);
        // 入款银行
        $sysbank = db('sysbank')->find();
        $this->assign('sysbank',$sysbank);
        return $this->fetch();
    }

    public function paysubmit(){
        $uid = $_SESSION['uid'];
        $bpprice = input('price', 0, 'floatval');
        $pay_type = input('pay_type', 5, 'floatval');
        $truename = input('truename', '', 'trim');
        $btime = input('btime', '', 'trim');

        if($bpprice<$this->conf['userpay_min']){
            return ['status'=>false,'msg'=>'最小充值金额'.$this->conf['userpay_min'].'起'];
        }elseif($bpprice>$this->conf['userpay_max']){
            return ['status'=>false,'msg'=>'最大充值金额'.$this->conf['userpay_max'].'元'];
        }

        // 充值方式
        $payment = db('payment')->where(['is_use'=>1,'isdelete'=>0,'id'=>$pay_type])->find();
        if(!$payment){
            return ['status'=>false,'msg'=>'请选择充值方式'];
        }
        $user = db('userinfo')->field('usermoney')->where(['uid'=>$uid])->find();
        $bpbalance = $user['usermoney'];
        $insert = ['uid'=>$uid,'bpprice'=>$bpprice,'pay_type'=>trim($payment['pay_conf'],'name:'),'bptype'=>1,'bptime'=>time(),'btime'=>$btime?strtotime($btime):time(),'remarks'=>$payment['pay_name'],'isverified'=>0,'reg_par'=>0,'bpbalance'=>$bpbalance,'truename'=>$truename];

        $bid = db('balance')->insertGetId($insert);
        if($bid){
            return ['status'=>true,'msg'=>'充值审核中'];
        }else{
            return ['status'=>false,'msg'=>'充值失败'];
        }
    }

    public  function  am() {
        return $this->fetch();
    }
    public  function  hold() {
        return $this->fetch();
    }

    public  function  winvoucher() {
        return $this->fetch();
    }


    public  function  inquiries() {
        $uid = $_SESSION['uid'];
        if(!$uid){
            $this->redirect(Url(url('apk/login/login')));
        }
		
		$fromtime = input('fromtime','','trim');
		$totime = input('totime','','trim');
		$pid = input('pid','0','intval');

		$where = ['uid'=>$uid];
		if($fromtime && $totime){
			$where = array_merge($where,['buytime'=>[['egt',strtotime($fromtime)],['lt',strtotime($totime)+86400]]]);
		}elseif($fromtime){
			$where = array_merge($where,['buytime'=>['egt',strtotime($fromtime)]]);
		}elseif($totime){
			$where = array_merge($where,['buytime'=>['lt',strtotime($totime)+86400]]);
		}
		if($pid){
			$where = array_merge($where,['pid'=>$pid]);
		}

        $orders = db('order')->where($where)->order('oid desc')->select();
		foreach($orders as  $k=>$val) {
			$orders[$k]['name'] =$val['ptitle'];
			$orders[$k]['fx'] = ($val['ostyle']==0)?'买涨':'买跌';
			$orders[$k]['yk'] = $val['ploss'];
			$orders[$k]['money'] =$val['fee'];
		}
        $this->assign('orders',$orders);

		$product = db('productdata')->field('pid,Name')->where(['isdelete' => 0])->select();
        $this->assign('product', $product);
        return $this->fetch();
    }

    public  function accountrecord() {
        $uid = $_SESSION['uid'];
        if(!$uid){
            $this->redirect(url('apk/login/login'));
        }
        $acountrf = db('balance')->where(['uid'=>$uid,'isshow'=>1,'bptype'=>1])->order('bpid desc')->select();
        $dealhis = [];
        $types = [1=>'充值',2=>'手动加款',3=>'正在充值',4=>'取消','5'=>'提现','6'=>'手动扣款',7=>'邀请奖励'];
        foreach($acountrf  as $key=>$val) {
            $dealhis[$key]['utime'] = date('Y-m-d H:i:s',$val['bptime']);
            $dealhis[$key]['reg_par'] = $val['reg_par'];
            $dealhis[$key]['realprice'] = $val['realprice'];
            $dealhis[$key]['typedesc'] =$types[$val['bptype']];
            $dealhis[$key]['money'] = $val['bpprice'];
            $dealhis[$key]['isverified']= $val['isverified'];
            $dealhis[$key]['bptype'] = $val['bptype'];
			if($val['isverified']==1){
				$dealhis[$key]['is_verify']='审核通过';
			}elseif($val['isverified']==2){
				$dealhis[$key]['is_verify']='拒绝';
			}elseif($val['isverified']==0){
				$dealhis[$key]['is_verify']='待审核';
			}else{
				$dealhis[$key]['is_verify']='审核中';
			}
            $dealhis[$key]['remarks'] = $val['remarks'];
        }

       $this->assign('dealhis',$dealhis);
        return $this->fetch();
    }

    public  function withdrawrecord() {
        $uid = $_SESSION['uid'];
        if(!$uid){
            $this->redirect(url('apk/login/login'));
        }
        $acountrf = db('balance')->where(['uid'=>$uid,'isshow'=>1,'bptype'=>5])->order('bpid desc')->select();
        $dealhis = [];
        $types = [1=>'充值',2=>'手动加款',3=>'正在充值',4=>'取消','5'=>'提现','6'=>'手动扣款',7=>'邀请奖励'];
        foreach($acountrf  as $key=>$val) {
            $dealhis[$key]['utime'] = date('Y-m-d H:i:s',$val['bptime']);
            $dealhis[$key]['reg_par'] = $val['reg_par'];
            $dealhis[$key]['realprice'] = $val['realprice'];
            $dealhis[$key]['typedesc'] =$types[$val['bptype']];
            $dealhis[$key]['money'] = $val['bpprice'];
            $dealhis[$key]['isverified']= $val['isverified'];
            $dealhis[$key]['bptype'] = $val['bptype'];
			if($val['isverified']==1){
				$dealhis[$key]['is_verify']='审核通过';
			}elseif($val['isverified']==2){
				$dealhis[$key]['is_verify']='拒绝';
			}elseif($val['isverified']==0){
				$dealhis[$key]['is_verify']='待审核';
			}else{
				$dealhis[$key]['is_verify']='审核中';
			}
            $dealhis[$key]['remarks'] = $val['remarks'];
        }

       $this->assign('dealhis',$dealhis);
        return $this->fetch();
    }

    public  function activitycenter() {
        return $this->fetch();
    }
    public  function ruleintroduce() {
        return $this->fetch();
    }
    public  function historynotice() {
        $notices = db('notice')->field('title,content,time')->where(['state'=>1])->order('id desc')->limit(6)->select();
        $this->assign('notices',$notices);
        return $this->fetch();
    }
    public  function bankcard() {
        $uid = $_SESSION['uid'];
        if(!$uid){
            $this->redirect(url('apk/login/login'));
        }
        $bankcards = db('bankcard')->field('*')->where(['uid'=>$uid])->select();
        $this->assign('bankcards',$bankcards);
     //  p($bankcards);
        return $this->fetch();
    }

    public  function card_reset() {
        $uid = $_SESSION['uid'];
        if(!$uid){
            $this->redirect(url('apk/login/login'));
        }
        $res = db('bankcard')->where(['uid'=>$uid])->delete();
        if($res){
            return WPreturn('银行卡解绑成功', 1);
        }else{
            return WPreturn('银行卡解绑失败');
        }
    }

    public function applicant() {
        return $this->fetch();
    }

    public function modityepwd()
    {
        if (!$this->uid) {
            $this->redirect(Url('/apk/login/login'));
        }
        if (request()->isPost()) {
            $post = input('post.');
            $user = $this->user;
            if ($user['upwd'] != $post['loginpwd']) {
                return WPreturn('登录密码不正确');
            }
            if ($post['password'] != $post['cpassword']) {
                return WPreturn('新提现密码与确认密码不一致');
            }
            // if (db('userinfo')->where('uid', $this->uid)->count()) {
            //     db('userinfo_password')->update([
            //         'uid' => $this->uid,
            //         'trade_password' => $post['password'],
            //     ]);
            // } else {
            //     db('userinfo_password')->insert([
            //         'uid' => $this->uid,
            //         'trade_password' => $post['password'],
            //     ]);
            // }
            $flag = Db::name('userinfo')->where('uid', $this->uid)->update(['epwd' => $post['password']]);
            if ($flag) {
                return WPreturn('提现密码修改成功', 1);
            } else {
                return WPreturn('提现密码修改失败');
            }
        } else {
            $this->assign('user', $this->user);
            return $this->fetch();
        }
    }

    public function moditypwd() {
        if (!$this->uid) {
            $this->redirect(Url('/apk/login/login'));
        }
        if(request()->isPost()){
            $post = input('post.');
            $user = $this->user;
            $loginpwd = $post['loginpwd'];
            if($user['upwd'] != $loginpwd){
                return WPreturn('登录密码不正确');
            }
            if($post['password'] != $post['cpassword']){
                return WPreturn('提款密码与确认密码不一致');
            }
            $upwd = $post['password'];
            $flag = Db::name('userinfo')->where('uid',$this->uid)->update(['upwd'=>$upwd]);
            if($flag){
                return WPreturn('登录密码修改成功', 1);
            }else{
                return WPreturn('登录密码修改失败');
            }
        }else{
            $this->assign('user', $this->user);
            return $this->fetch();
        }
    }
    public  function  respwd() {
      $uid = $_SESSION['uid'];
        if(!$uid){
            $this->redirect(url('apk/login/login'));
        }
      $data = input('post.');

      $pwd  = $data['oldpwd'];
      $name =$data['name'];

      $newpwd = $data['newpwd'];
      $newpwd2 = $data['newpwd2'];
      $res = db('userinfo')->where(['uid'=>$uid,'upwd'=>$pwd])->select();
      // dump($data);
      $row = array('upwd'=>$newpwd,'username'=>$name);
      if($res && ($newpwd==$newpwd2)){
          $ok = db('userinfo')->where(['uid'=>$uid,'upwd'=>$pwd])->update($row);

          if($ok){
          return json_encode(1);
          }else{
        return json_encode(0);
          }
      }else{
            return json_encode(0);
      }

    }



    public  function  palyStep() {
        return $this->fetch();
    }


    public  function  loginout() {
        unset($_SESSION['uid']);
        $this->redirect(url('apk/login/login',['t'=>time()]));
    }

    /**
     * 用户的提现功能
     * @return mixed|string
     */
    public function withdraw() {
        $uid = $this->uid;
        if(input('post.')){
            $data = input('post.');
            if($data){
                if(!$data['price']){
                    return WPreturn('请输入提现金额');
                }
				if(empty($data['bankcardno'])){
					 return WPreturn('请设置提现银行卡');
				}
                //验证申请金额
                $user = $this->user;
                if($user['ustatus'] == 1){
        			return WPreturn('您的账户已被冻结,请联系在线客服！',-1);
        		}
        		
        		if($user['ustatus'] == 2){
        			return WPreturn('您的账户已被限制交易,请联系在线客服！',-1);
        		}

                $conf = $this->conf;
                if($conf['is_cash'] != 1){
                    return WPreturn('抱歉！暂时无法出金');
                }
                if($conf['cash_min'] > $data['price']){
                    return WPreturn('单笔最低提现金额为：'.$conf['cash_min']);
                }
                if($conf['cash_max'] < $data['price']){
                  return    WPreturn('单笔最高提现金额为：'.$conf['cash_max']);
                }

                $epwd = $data['passwd'];
                if($user['epwd'] != $epwd){
                    return    WPreturn('提款密码不正确');
                }

                $_map['uid'] = $uid;
                $_map['bptype'] = 5;
                $_map['isverified'] = array('neq',2);
                $cash_num = db('balance')->where($_map)->whereTime('bptime', 'd')->count();

                if($cash_num + 1 > $conf['day_cash']){
                    return WPreturn('每天最多提现'.$conf['day_cash'].'次');
                }
                $cash_day_max = db('balance')->where($_map)->whereTime('bptime', 'd')->sum('bpprice');
                if($conf['cash_day_max'] < $cash_day_max + $data['price']){
                    return WPreturn('当日累计最高提现金额为：'.$conf['cash_day_max']);
                }



                $statrdate=Db::name("config")->where("name='role_ks'")->select();
                $txstatrdate = $statrdate[0]['value']?$statrdate[0]['value']:'9:00';
                $starttime = str_replace(':','',$txstatrdate);
				
				
                $enddate=Db::name("config")->where("name='role_js'")->select();
                $txenddate= $enddate[0]['value']?$enddate[0]['value']:'22:00';
                $endtime = str_replace(':','',$txenddate);
				if(date('Gi') < intval($starttime) || date('Gi') >  intval($endtime)){
					return WPreturn('出金时间为'.$txstatrdate.'-'.$txenddate.'',-1);
				}

				if($conf['sys_yue_benjin']==1){
					$this->user['usermoney'] = $this->user['usermoney']-$this->user['freeze']>0 ? $this->user['usermoney']-$this->user['freeze'] : 0;
				}

                //代理商的话判断金额是否够
                if($this->user['otype'] == 101){
                    if( ($this->user['usermoney'] - $data['price']) < $this->user['minprice'] ){
                        return WPreturn('您的保证金是'.$this->user['minprice'].'元，提现后余额不得少于保证金。',-1);
                    }
                }

                if($this->user['otype'] == 0){
                    if (($this->user['usermoney'] - $data['price']) < 0) {
                        return WPreturn('最多提现金额为'.$this->user['usermoney'].'元',-1);
                    }
                }

                if( ($this->user['usermoney'] - $data['price']) < 0){
                    return WPreturn('最多提现金额为'.$this->user['usermoney'].'元');
                }

                $reg_par = round($conf['reg_par']*$data['price']/100,2);
                // if($data['price']+$reg_par>$this->user['usermoney']){
                //     $realprice = $this->user['usermoney'] - $reg_par;
                // }else{
                //     $realprice = $data['price'];
                // }


                //签约信息
              //  $mybank = db('bankcard')->where('uid',$uid)->find();

                //提现申请
                $newdata['bpprice'] = $data['price'];
                $newdata['bptime'] = time();
                $newdata['bptype'] = 5;
                $newdata['remarks'] = '会员提现';
                $newdata['uid'] = $uid;
                $newdata['isverified'] = 0;
                $newdata['bpbalance'] = 0;
                $newdata['bankid'] =input('bankid');   // $data['bankcardno'];
                $newdata['btime'] = time();
                $newdata['reg_par'] = $reg_par;
                $newdata['realprice'] = $data['price'] - $reg_par;

                //$withmoney = ($realprice + $reg_par);
                $withmoney = $data['price'];
                $bpid = Db::name('balance')->insertGetId($newdata);
                if($bpid){
                    //插入申请成功后,扣除金额
                    $editmoney = Db::name('userinfo')->where('uid',$uid)->setDec('usermoney',$withmoney);
                    if($editmoney){
                        //插入此刻的余额。
                        $usermoney = Db::name('userinfo')->where('uid',$uid)->value('usermoney');
                        Db::name('balance')->where('bpid',$bpid)->update(array('bpbalance'=>$usermoney));
                        //资金日志
                        set_price_log($uid,2,$data['price'],'提现','提现申请',$bpid,$usermoney);

                        return WPreturn('提现申请提交成功！',1);
                    }else{
                        //扣除金额失败，删除提现记录
                        Db::name('balance')->where('bpid',$bpid)->delete();
                        return WPreturn('提现失败！');
                    }

                }else{
                    return WPreturn('提现失败！');
                }

            }else{
                return WPreturn('暂不支付此提现类型！');
            }
        }else{

            $bankinfo = db('bankcard')->where(['uid'=>$uid])->order('id desc')->find();
            if(!$bankinfo){
                $this->redirect(url('apk/index/bankcard'));
            }else{
                $this->assign('bankinfo',$bankinfo);
                return $this->fetch();
            }
        }
    }


    public  function  add_bank() {
        $uid = $_SESSION['uid'];
        if (input('post.')) {
            $data = input('post.');
			if(!$data['bankno']){
				echo '<script>alert("请设置提现银行卡");history.back();</script>';
                exit;
			}
            if(!$uid){
                $this->redirect(url('apk/login/login'));
            }
            $item = db('bankcard')->where(['accntno'=>$data['bankno'],'isdelete'=>0])->find();
            if($item){
                //echo '<script>alert("银行卡已经存在");history.back();</script>';
                //exit;
            }
            $item = db('bankcard')->where(['uid'=>$uid,'isdelete'=>0])->find();
            if($item){
                echo '<script>alert("您已经绑卡了");history.back();</script>';
                exit;
            }
            $user = db('userinfo')->field('nickname')->where(['uid'=>$uid])->find();
            if($user['nickname'] && $user['nickname']!=$data['accntnm']){
                echo '<script>alert("持卡人姓名与注册姓名不一致，请联系客服处理");history.back();</script>';
                exit;
            }
            $arr = [
                'accntno'=>$data['bankno'], //卡号
                'accntnm'=>$data['accntnm'], //持卡人用户名
                'uid'=>$uid,  //用户的uid
                'phone'=>$data['phone'], //手机号码
               'address'=>$data['banksmall'], //分行地址
                'content'=>$data['bankname']
            ];
          $res= db('bankcard')->insert($arr);

         header("Location:".Url('/apk/index/bankcard'));
         exit;
        }
        $user = db('userinfo')->field('nickname')->where(['uid'=>$uid])->find();
        $this->assign('user',$user);
        return $this->fetch();
    }

    // 找回提款密码
    public function forgot(){
        if (!$this->uid) {
            $this->redirect(Url('/apk/login/login'));
        }
        if(request()->isPost()){
            $post = input('post.');
            $user = $this->user;
            $loginpwd = $post['loginpwd'];
            if($user['upwd'] != $loginpwd){
                return WPreturn('登录密码不正确');
            }
            if($post['password'] != $post['cpassword']){
                return WPreturn('提款密码与确认密码不一致');
            }
            $epwd = $post['password'];
            $flag = Db::name('userinfo')->where('uid',$this->uid)->update(['epwd'=>$epwd]);
            if($flag){
                return WPreturn('找回提款密码成功', 1);
            }else{
                return WPreturn('找回提款密码失败');
            }
        }else{
            return $this->fetch();
        }
    }

	public function userinvest() {
        $uid = $_SESSION['uid'];
        if(!$uid){
            $this->redirect(url('apk/login/login'));
        }
        $list = db('userinvest')->where(['uid'=>$uid])->order('id desc')->select();
        $this->assign('list',$list);
        return $this->fetch();
	}

	public function invest() {
        $uid = $_SESSION['uid'];
        if(!$uid){
            $this->redirect(url('apk/login/login'));
        }
        $list['list'] = db('invest')->where(['state'=>1])->order('pid')->select();
		$sums = [];
		foreach($list['list'] as $ls){
			$sums[] = $ls['days'];
		}
		if($sums) $list['sums'] = implode('，',$sums);
        $this->assign('list',$list);
        return $this->fetch();
	}

	public function idetail() {
		$uid = $_SESSION['uid'];
        if(!$uid){
            $this->redirect(url('apk/login/login'));
        }
		$pid = input('pid',0,'intval');
        $item = db('invest')->where(['state'=>1,'pid'=>$pid])->find();
        if(!$item){
            return WPreturn('投资项目不存在');
        }
		$this->assign('item',$item);
        return $this->fetch();
	}

	public function doinvest() {
        $uid = $_SESSION['uid'];
        if(!$uid){
            $this->redirect(url('apk/login/login'));
        }
		$pid = input('pid',0,'intval');
		$money = input('money',0,'intval');
        $item = db('invest')->where(['state'=>1,'pid'=>$pid])->find();
        if(!$item){
            return WPreturn('投资项目不存在');
        }
		if($money<$item['min']){
			return WPreturn('投资金额必须大于最小起投金额');
		}
		if($money>$this->user['usermoney']) {
			return WPreturn('投资失败，您的可用余额不足');
		}
        
		if($this->conf['sys_yue_benjin']==2){
			$flag = db('userinfo')->where(['uid'=>$uid])->setDec('usermoney',$money);
		}
		$flag = db('userinfo')->where(['uid'=>$uid])->setInc('freeze',$money);
		if($flag){
			//投资
			$idata['pid'] = $pid;
			$idata['money'] = $money;
			$idata['interest'] = round($money * $item['rates'] / 100, 2);
			$idata['days'] = $item['days'];
			$idata['uid'] = $this->user['uid'];
			$idata['username'] = $this->user['username'];
			$idata['state'] = 1;
			$idata['time'] = time();

			if(0){  //if(date('H')<22){
				$idata['totime'] = strtotime(date('Y-m-d',strtotime('+'.$item['days'].' day')))+9*3600;
			}else{
				$idata['totime'] = strtotime('+'.$item['days'].' day');
			}

			$id = db('userinvest')->insertGetId($idata);
			if($id){
				//资金日志
				if($this->conf['sys_yue_benjin']==2){
					set_price_log($uid,2,$money,'利息宝','投资',$id,$this->user['usermoney']);
				}
				return WPreturn('投资成功', 1);
			}else{
				return WPreturn('投资失败');
			}
		}else{
			return WPreturn('投资失败，余额不足');
		}
	}

}
