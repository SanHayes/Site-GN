<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Order extends Base
{
	/**
	 * 产品订单列表
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */
	public function orderlist()
	{	
		$pagenum = cache('page');
		$data = input('get.');
		
		//验证搜索数据
		$res = $this->ecickdata($data);
		
        $where = $res['where'];
        //默认的数据搜索是空的
		$getdata = $res['getdata'];
		//权限检测
		if($this->otype != 3){
         
		   $uids = myuids($this->uid);
            if(!empty($uids)){
                $where['u.uid'] = array('IN',$uids);
            }
        }
             
//		if($getdata&&(cache('order'.md5(json_encode($where))).md5(json_encode($getdata)).$pagenum)){
//			$order = cache('order'.md5(json_encode($where)).md5(json_encode($getdata)).$pagenum);
//		}else{
			$order = Db::name('order')->alias('o')->field('o.*,u.username,u.nickname,u.oid as uoid,u.managername')
				->join('__USERINFO__ u','o.uid=u.uid')
				->where($where)->order('oid desc')->paginate($pagenum,false,['query'=> $getdata]);
			cache('order'.md5(json_encode($where)).md5(json_encode($getdata)).$pagenum,$order);
//		}
		//产品
// 		if(cache('pro')){
// 			$pro = cache('pro');
// 		}else{
// 			$pro = Db::name('productinfo')->field('pid,ptitle')->where('isdelete',0)->select();
// 			cache('pro',$pro);
// 		}
        $pro = Db::name('productinfo')->field('pid,ptitle')->where('isdelete',0)->select();
		//没有数据
                if(!$order) {
                    $this->assign('noorder',1);
                } else if($order->total() == 0){
			   $this->assign('noorder',1);
		}
		
                 
		$this->assign('pro',$pro);
		$this->assign('order',$order);
		$this->assign('getdata',$getdata);
		$getdata['display'] = 0;
		$this->assign('jsongetdata',http_build_query($getdata));
		
		//dankong
		$iskong = 0;
		$can_kong = getconf('can_kong');
		if($can_kong && $can_kong != '' && !is_array($can_kong)){
			$arr = explode(',',$can_kong);
			if(in_array($this->uid,$arr)){
				$iskong = 1;
			}
		}
           
		$this->assign('iskong',$iskong);
		
		
		return $this->fetch();
	}
	
	//	错单处理
	public function  ordererror(){
		$kong_type= intval(input('kong_type'));
		$oid =input('oid');
		if($oid){
		    $res = Db::name('order')->where('oid',$oid)->find();
		    $buyprice = $res['buyprice'];
		    $sellprice = $res['sellprice'];
		    $maxwin = $res['fee']*$res['endprofit']/100;
		    $maxloss = $res['fee']*$res['endloss']/100;
		    $data = array();
		    Db::startTrans(); // 启动事务
			try{
			    if($res['ploss'] > 0){
    		        $data['sellprice'] = round($buyprice - ($sellprice-$buyprice),3);
    		        $data['ploss'] = $res['ploss'] > $maxloss ? -$maxloss : -$res['ploss'];
    		        $data['is_win'] = 2;
    		        $orders = Db::name('order')->where(['oid'=>$oid])->update($data);
    		        if($orders){
    					db('userinfo')->where('uid',$res['uid'])->setDec('usermoney',abs($res['ploss'])+abs($data['ploss']));
    					// 提交事务
    					Db::commit();
    					return WPreturn('操作成功！',1);
    				}else{
    					// 回滚事务
    					Db::rollback();
    					return WPreturn('操作失败！',-1);
    				}
    		    }else{
    		        $data['sellprice'] = round($buyprice + ($buyprice - $sellprice),3);
    		        $data['ploss'] = (-$res['ploss']) > $maxwin ? $maxwin : -$res['ploss'];
    		        $data['is_win'] = 1;
    		        $orders = Db::name('order')->where(['oid'=>$oid])->update($data);
    		        if($orders){
    					db('userinfo')->where('uid',$res['uid'])->setInc('usermoney',abs($res['ploss'])+abs($data['ploss']));
    					// 提交事务
    					Db::commit();
    					return WPreturn('操作成功！',1);
    				}else{
    					// 回滚事务
    					Db::rollback();
    					return WPreturn('操作失败！',-1);
    				}
    		    }
				
			} catch (\Exception $e) {
				// 回滚事务
				Db::rollback();
				return WPreturn('操作失败！',-1);
			}
		}else{
		    return WPreturn('该订单不存在!',-1);
		}
		//dump($kong_type);	dump($uid);exit;
		/*if($kong_type == 1){
			$kong_type =3;
		}elseif($kong_type == 2){
			$kong_type =4;
		}else{
			$kong_type =0;
		}*/
         
   }

	//	修改kong_type
	public function  changekong(){
		$kong_type= intval(input('kong_type'));
		$oid =input('oid');
		//dump($kong_type);	dump($uid);exit;
		/*if($kong_type == 1){
			$kong_type =3;
		}elseif($kong_type == 2){
			$kong_type =4;
		}else{
			$kong_type =0;
		}*/
         $res = Db::name('order')->where('oid',$oid)->update(array('kong_type'=>$kong_type)) ;
   }
	/**
	 * 平仓日志
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */
	public function orderlog()
	{
		$pagenum = cache('page');
		$where = array();
		$getdata = array();
		$data = input('get.');
		//用户名称、id、手机、昵称
		if(isset($data['username']) && !empty($data['username'])){
			$where['username|u.uid|utel|nickname'] = array('like','%'.$data['username'].'%');
			$getdata['username'] = $data['username'];
		}

		//时间搜索
		if(isset($data['starttime']) && !empty($data['starttime'])){
			if(!isset($data['endtime']) || empty($data['endtime'])){
				$data['endtime'] = date('Y-m-d H:i:s',time());
			}
			$where['time'] = array('between time',array($data['starttime'],$data['endtime']));
			$getdata['starttime'] = $data['starttime'];
			$getdata['endtime'] = $data['endtime'];
		}

		//盈亏
		if(isset($data['ploss']) && !empty($data['ploss'])){
			if ($data['ploss'] == 1) { //赢利
				$where['addpoint'] = array('>',0);
			}
			if ($data['ploss'] == 2) { //亏损
				$where['addpoint'] = array('<=',0);
			}
			
			$getdata['ploss'] = $data['ploss'];
		}

		//权限检测
		if($this->otype != 3){
         
		   $uids = myuids($this->uid);
            if(!empty($uids)){
                $where['u.uid'] = array('IN',$uids);
            }
        }

        if(isset($data['doble']) && !empty($data['doble'])){
        	$sql = 'select * from wp_order_log
                where oid in (select   oid from wp_order_log group by   oid having count(oid) > 1) and uid='.trim($data['username']);
			if(cache('orderlog'.md5($sql))){
				$orderlog = cache('orderlog'.md5($sql));
			}else{
				$orderlog =  Db::query($sql);
				cache('orderlog'.md5($sql),$orderlog);
			}
			$this->assign('doble',1);
        }else{
			if(cache('orderlog'.md5(json_encode($where)).md5(json_encode($getdata)).$pagenum)){
				$orderlog = cache('orderlog'.md5(json_encode($where)).md5(json_encode($getdata)).$pagenum);
			}else{
				$orderlog = Db::name('order_log')->alias('ol')->field('ol.*,u.username,u.nickname,u.oid as uoid')
					->join('__USERINFO__ u','ol.uid=u.uid')
					->where($where)->order('id desc')->paginate($pagenum,false,['query'=> $getdata]);
				cache('orderlog'.md5(json_encode($where)).md5(json_encode($getdata)).$pagenum,$orderlog);
			}
        	
        }
		

		$this->assign('orderlog',$orderlog);
		$this->assign('getdata',$getdata);
		$this->assign('doble',0);
		return $this->fetch();
	}

	/**
	 * 订单详情
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */
	public function orderinfo()
	{
		$oid = input('param.oid');
		
		if(!$oid){
			$this->redirect('order/orderlist');
		}
		$where['o.oid'] = $oid;
		if(cache('order'.$oid)){
			$order = cache('order'.$oid);
		}else{
			$order = Db::name('order')->alias('o')->field('o.*,u.username,u.nickname,u.oid as uoid,u.usermoney,u.portrait')
					->join('__USERINFO__ u','o.uid=u.uid')
					->where($where)->order('oid desc')->find();
			cache('order'.$oid,$order);
		}
		if($this->otype != 3){
		   $uids = myuids($this->uid);
           if(!in_array($order['uid'],$uids)){
           		$this->error("无权查看！");
           }
        }

		$this->assign($order);
		return $this->fetch();
	}

	/**
	 * 订单统计
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */

	public function ordersta()
	{
		$data = input('post.');
		
		//验证搜索数据
		$res = $this->ecickdata($data);
		
		$where = $res['where'];
		if(isset($where['o.oid'])){
			$where['ol.oid'] = $where['o.oid'];
			unset($where['o.oid']);
		}
		$getdata = $res['getdata'];
		if($this->otype != 3){
            $uids = myuids($this->uid);
            if(!empty($uids)){
                $where['ol.uid'] = array('IN',$uids);
            }
        }

		$ordersta = Db::name('order')->alias('ol')->field('SUM(ploss) as ploss,SUM(fee) as fee')->join('__USERINFO__ u','ol.uid=u.uid')->where($where)->select();
		//盈亏统计
		$resdata['profit'] = $ordersta['0']['ploss']?$ordersta['0']['ploss']:0;
		//委托金额
		$resdata['fee'] = $ordersta['0']['fee']?$ordersta['0']['fee']:0;
		//交易手数
		$ordersta = Db::name('order')->alias('ol')->join('__USERINFO__ u','ol.uid=u.uid')->where($where)->count();
		$resdata['count'] = $ordersta;
		//无效金额
		$where['ostaus'] = 1;
		if(!isset($data['ploss'])){
			$where['ploss'] = 0;
		}
		if(isset($data['ploss']) && ($data['ploss'] == 1 || $data['ploss'] == 2)){
			$ordersta = 0;
		}else{
			$ordersta = Db::name('order')->alias('ol')->join('__USERINFO__ u','ol.uid=u.uid')->where($where)->sum('fee');
		}
		$resdata['invalid_fee'] = $ordersta?$ordersta:0;
		//有效金额
		$where['ostaus'] = 1;
		if(!isset($data['ploss'])){
			$where['ploss'] = array('neq',0);
		}
		
		if(isset($data['ploss']) && $data['ploss'] == 3){
			$ordersta = 0;
		}else{
			$ordersta = Db::name('order')->alias('ol')->join('__USERINFO__ u','ol.uid=u.uid')->where($where)->sum('fee');
		}
		$resdata['valid_fee'] = $ordersta?$ordersta:0;
		//建仓金额
		$where['ostaus'] = 0;
		$ordersta = Db::name('order')->alias('ol')->join('__USERINFO__ u','ol.uid=u.uid')->where($where)->sum('fee');
		$resdata['now_fee'] = $ordersta?$ordersta:0;

		//手续费
		unset($where['ploss']);
		unset($where['ostaus']);
		$resdata['valid_shouxu'] = Db::name('order')->alias('ol')->join('__USERINFO__ u','ol.uid=u.uid')->where($where)->sum('sx_fee');
		if(!$resdata['valid_shouxu']) $resdata['valid_shouxu']=0;
		
		
		return $resdata;
	}

	/**
	 * 订单搜索数据验证
	 * @author lukui  2017-02-16
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function ecickdata($data)
	{
		
		$where = array();
		$getdata = array();
		//订单id/编号
		if(isset($data['orderid']) && !empty($data['orderid'])){
			$where['o.oid|orderno'] = array('like','%'.$data['orderid'].'%');
			$getdata['oid'] = $data['orderid'];
		}

		//用户名称、id、手机、昵称
		if(isset($data['username']) && !empty($data['username'])){
			if(!isset($data['stype'])) $data['stype'] = 1;
			if($data['stype'] == 1){
				$where['username|u.uid|utel|nickname'] = array('like','%'.$data['username'].'%');
			}
			if($data['stype'] == 2){
				$puid = db('userinfo')->where(array('username'=>$data['username']))->whereOr('utel',$data['username'])->value('uid');
				if(!$puid) $puid = 0;
				$where['u.oid'] = $puid;
			}
			$getdata['username'] = $data['username'];
			$getdata['stype'] = $data['stype'];

			
		}

		//时间搜索
		if(isset($data['starttime']) && !empty($data['starttime'])){
			if(!isset($data['endtime']) || empty($data['endtime'])){
				$data['endtime'] = date('Y-m-d H:i:s',time());
			}
			$where['buytime'] = array('between time',array($data['starttime'],$data['endtime']));
			$getdata['starttime'] = $data['starttime'];
			$getdata['endtime'] = $data['endtime'];
		}
		
		//涨跌
		if(isset($data['ostyle']) && !empty($data['ostyle'])){
			$where['ostyle'] = $data['ostyle']-1;
			$getdata['ostyle'] = $data['ostyle'];
		}

		//盈亏
		if(isset($data['ploss']) && !empty($data['ploss'])){
			if ($data['ploss'] == 1) { //赢利
				$where['ploss'] = array('>',0);
			}
			if ($data['ploss'] == 2) { //亏损
				$where['ploss'] = array('<',0);
			}
			if ($data['ploss'] == 3) { //无效
				$where['ploss'] = array('=',0);
			}
			$getdata['ploss'] = $data['ploss'];
		}

		//产品
		if(isset($data['pid']) && !empty($data['pid'])){
			$where['pid'] = $data['pid'];
			$getdata['pid'] = $data['pid'];
		}

		//状态
		if(isset($data['ostaus']) && !empty($data['ostaus'])){
			$where['ostaus'] = $data['ostaus']-1;
			$getdata['ostaus'] = $data['ostaus'];
		}
		//特定oid
		if(isset($data['oid']) && !empty($data['oid'])){
			$where['o.oid'] = $data['oid'];
			$getdata['oid'] = $data['oid'];
		}
		if (empty($where)) {
			$times['starttime'] = strtotime(date("Y").'-'.date("m").'-'.date("d").' 00:00:00');
			$times['endtime'] = strtotime(date("Y").'-'.date("m").'-'.date("d").' 24:00:00');
    		$where['buytime'] = array('between time',array($times['starttime'],$times['endtime']));
		}



		$res['where'] = $where;
		$res['getdata'] = $getdata;
		return $res;
	}

	
	/**
	 * 我的对冲
	 * @return [type] [description]
	 */
	public function myallot()
	{
		
		$map['uid'] = 2;
		$getdata = array();
		$pagenum = cache('page');

		$list = db('allot')->where($map)->order('id desc')->paginate($pagenum,false,['query'=> $getdata]);
		
		$this->assign('list',$list);
		$this->assign('getdata',$getdata);
		$this->assign('jsongetdata',http_build_query($getdata));
		return $this->fetch();

	}
	public function duokong() {
		$oid = input('post.oid');
		$kong_type = input('post.kong_type');
		if(!$oid || !$kong_type){
		    return WPreturn('参数提交不完整',-1);
		}
		$map['oid'] = ['in',$oid]; 
		$map['ostaus'] = 0;
		$ids = db('order')->where($map)->update(['kong_type'=>$kong_type]);
		if($ids){
			return WPreturn('操作成功',1);
		}else{
			return WPreturn('操作失败',-1);
		}
	}


	public function dankong() {
		$data = input('post.');
		$order = db('order')->where('oid',$data['oid'])->find();
		
        if($order['ostaus'] == 1){
			return WPreturn('此单已平仓',-1);
		}
		$ids = db('order')->update($data);
		if($ids){
			return WPreturn('操作成功',1);
		}else{
			return WPreturn('操作失败',-1);
		}
		

	}







}
