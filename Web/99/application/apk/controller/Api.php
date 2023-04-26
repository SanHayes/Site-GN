<?php 
namespace app\apk\controller;
use think\Controller;
use think\Db;

class Api extends Controller{

	public $apiface = 2;

	public function __construct(){
		parent::__construct();

		$this->nowtime = time();
		$minute = date('Y-m-d H:i',$this->nowtime).':00';
		$this->minute = strtotime($minute);

		//指定客户赢利或亏损：
		//array()里面写客户编号，如客户id为1027则写为  array(1027)
		//如多个客户，则以英文逗号分开，如 array(1027,2018,3765)
		//注意一定是英文逗号，中文逗号会报错
		$this->user_win = array();//指定客户赢利
		$this->user_loss = array();//指定客户亏损
		
		//K线数据库
		$this->klinedata = db('klinedata');
	}

	public function getdate()
	{		
	    //echo date('Y-m-d H:i:s') . " getdate[3] -> ok \n\r";
		//产品列表
		$pro = db('productinfo')->where('isdelete',0)->select();
		if(!isset($pro)) return json(false);

		$nowtime = time();
		$_rand = rand(1,900)/100000;
		$thisdatas = array();
		
		foreach ($pro as $k => $v) {
			//验证休市
			$isopen = ChickIsOpen($v['pid']);
			if($isopen){
 				continue;
			}
			
			
			//腾讯证券
			if($v['procode'] == "btc" || $v['procode'] == "ltc"|| $v['procode'] == "eth" || $v['procode'] == "eos" || $v['procode'] == "ant" || $v['procode'] == "sol"){
				
					$minute = date('i',$nowtime);
					if($minute >= 0 && $minute < 15){ $minute = 0;}
					elseif($minute >= 15 && $minute < 30){ $minute = 15;}
					elseif($minute >= 30 && $minute < 45){ $minute = 30;}
					elseif($minute >= 45 && $minute < 60){ $minute = 45;}
					$new_date = strtotime(date('Y-m-d H',$nowtime).':'.$minute.':00');
				
					if($this->apiface == 1) {
						if($v['procode'] == 'btc'){
							$url = 'http://api.bitkk.com/data/v1/ticker?market=btc_usdt';
						}elseif($v['procode'] == 'ltc'){
							$url = 'http://api.bitkk.com/data/v1/ticker?market=ltc_usdt';
						}elseif($v['procode'] == 'eth'){
							$url = 'http://api.bitkk.com/data/v1/ticker?market=eth_usdt';
						}elseif($v['procode'] == 'eos'){
							$url = 'http://api.bitkk.com/data/v1/ticker?market=eos_usdt';
						}
						$getdata = $this->curlfun($url);
						$res = json_decode($getdata,1);
						$data_arr=$res['ticker'];
						//var_dump($res['ticker']);
						//exit;			
						//$getdata = substr($getdata,2,-2);
						//$data_arr = explode(':',$getdata);
			
						if(!is_array($data_arr)) continue;
						//$Price = explode(',',$data_arr[10]);
						//$Open = explode(',',$data_arr[7]);
						//$Close = explode(',',$data_arr[7]);
						//$High = explode(',',$data_arr[3]);
						//$Low = explode(',',$data_arr[7]);
						$thisdata['Price'] = $this->fengkong($data_arr['sell'],$v);;
						$thisdata['Open'] = $data_arr['buy'];
						$thisdata['Close'] = $data_arr['last'];
						$thisdata['High'] = $data_arr['high'];
						$thisdata['Low'] = $data_arr['low'];
						$thisdata['Diff'] = 0;
						$thisdata['DiffRate'] = 0;
						//$thisdata['Name'] = $v['ptitle'];
					} else {
						if($v['procode'] == 'btc'){
							$url = 'https://api.huobi.pro/market/detail/merged?symbol=btcusdt';
						}elseif($v['procode'] == 'ltc'){
							$url = 'https://api.huobi.pro/market/detail/merged?symbol=ltcusdt';
						}elseif($v['procode'] == 'eth'){
							$url = 'https://api.huobi.pro/market/detail/merged?symbol=ethusdt';
						}elseif($v['procode'] == 'eos'){
							$url = 'https://api.huobi.pro/market/detail/merged?symbol=eosusdt';
						}elseif($v['procode'] == 'ant'){
							$url = 'https://api.huobi.pro/market/detail/merged?symbol=antusdt';
						}elseif($v['procode'] == 'sol'){
							$url = 'https://api.huobi.pro/market/detail/merged?symbol=solusdt';
						}
						
						$res = $this->curlfun($url);
						$data = json_decode($res,true);
						$data_arr = $data['tick'];
						if(!is_array($data_arr)) continue;
						$thisdata['Price'] = $this->fengkong($data_arr['ask'][0],$v);;
						$thisdata['Open'] = $data_arr['open'];
						$thisdata['Close'] = $data_arr['close'];
						$thisdata['High'] = $data_arr['high'];
						$thisdata['Low'] = $data_arr['low'];
						$thisdata['Diff'] = 0;
						$thisdata['DiffRate'] = 0;

					}
			
				
				//外汇网：http://forex.cnfol.com/
			}elseif(in_array($v['procode'],array("USDJPY","GBPJPY","EURUSD","GBPUSD","EURGBP"))){

				$url = "http://forex.cnfol.com/fol_inc/v6.0/Gold/goldhq/json_table/".$v['procode']."_forexdata.json";
				//file_put_contents('/www/wwwroot/test.7shou.com/url.txt',date('Y-m-d H:i:s') . '->' . $url . "\n\r",FILE_APPEND);
				$getdata = $this->curlfun($url,array(),'GET');
				
				$arrdata = json_decode($getdata,1);
				$arrdata = $arrdata["data"];
				//file_put_contents('/www/wwwroot/test.7shou.com/data.txt',$v['procode'].' -> ' .date('Y-m-d H:i:s') . ' -> ' . $getdata . "\n\r");

				$_data = array();
				foreach ($arrdata as $key => $value) {
					if($v['procode'] == $value['Code']){
						$_data = $value;
					}
				}
				
				$thisdata['Price'] = $_data['Last'];
				$thisdata['Open'] = $_data['Open'];
				$thisdata['Close'] = $_data['LastClose'];
				$thisdata['High'] = $_data['High'];
				$thisdata['Low'] = $_data['Low'];
				$thisdata['Diff'] = 0;
				$thisdata['DiffRate'] = 0;

			}elseif(in_array($v['procode'],array(5,6,7,8,10,11,12,13,14,15,22,23,24,25,26,27,28,29,30,31,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,55,56,57,58,59,60,61,62,63,70,71,72,73,74,75,76,77,78,79,80,87,90,91,92,93,94,95,96,97,98,99,100,101,102,105,106,107,108,113,114,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,144,149,150,151,152,162,163,164,165,166,167,168))){  	//口袋贵金属
				$url = 'https://m.sojex.net/api.do?rtp=GetQuotesDetail&id='.$v['procode'];
				//$html = file_get_contents($url); 
				$html = $this->curlfun($url);
				
		   		$res = json_decode($html,1);
		   		$res = $res['data']['quotes'];
		  
		   		//$thisdata['Price'] = $this->fengkong($res['buy'],$v);;
		   		$thisdata['Price'] = $res['buy'];
				$thisdata['Open'] = $res['open'];
				$thisdata['Close'] = $res['last_close'];
				$thisdata['High'] = $res['top'];
				$thisdata['Low'] = $res['low'];
				$thisdata['Diff'] = 0;
				$thisdata['DiffRate'] = 0;

			}elseif(in_array($v['procode'],array('llg','lls'))){ 
				$url = "https://hq.91pme.com/getmarketinfo/getQuotationByCode2.do?code=".$v['procode'];
				$html = $this->curlfun($url);
				$arr = json_decode($html,1);
				if(!isset($arr[0])) continue;
				$data_arr = $arr[0];
				$thisdata['Price'] = $data_arr['buy'];
				$thisdata['Open'] = $data_arr['open'];
				$thisdata['Close'] = $data_arr['lastclose'];
				$thisdata['High'] = $data_arr['high'];
				$thisdata['Low'] = $data_arr['low'];
				$thisdata['Diff'] = 0;
				$thisdata['DiffRate'] = 0;
				
			}else{
				$url = "http://hq.sinajs.cn/rn=".$nowtime."list=".$v['procode'];

				$getdata = $this->curlfun($url);
				$data_arr = explode(',',$getdata);
				
				if(!is_array($data_arr) || count($data_arr) != 18) continue;
				$thisdata['Price'] = $data_arr[1];
				$thisdata['Open'] = $data_arr[5];
				$thisdata['Close'] = $data_arr[3];
				$thisdata['High'] = $data_arr[6];
				$thisdata['Low'] = $data_arr[7];
				$thisdata['Diff'] = $data_arr[12];
				$thisdata['DiffRate'] = $data_arr[4]/10000;

			}
		
			
			//$thisdata['Name'] = $v['ptitle'];
			$thisdata['UpdateTime'] = $nowtime;
			$thisdata['pid'] = $v['pid'];

			$thisdatas[$v['pid']] = $thisdata;
			$ids = db('productdata')->where('pid',$v['pid'])->update($thisdata);

			
		}
        //print_r($thisdatas);
		cache('nowdata',$thisdatas);
		return true;
	}


	/**
	 * 数据风控
	 * @author lukui  2017-06-27
	 * @param  [type] $price [description]
	 * @param  [type] $pro   [description]
	 * @return [type]        [description]
	 */
	public function fengkong($price,$pro)
	{
		
		$point_low = $pro['point_low'];
		$point_top = $pro['point_top'];
		
		$FloatLength = getFloatLength($point_top);
		$jishu_rand = pow(10,$FloatLength);
		$point_low = $point_low * $jishu_rand;
		$point_top = $point_top * $jishu_rand;
		$rand = rand($point_low,$point_top)/$jishu_rand;
		
		$_new_rand = rand(0,10);
		if($_new_rand % 2 == 0){
			$price = $price + $rand;
		}else{
			$price = $price - $rand;
		}
		return $price;
	}




	//curl获取数据
	public function curlfun($url, $params = array(), $method = 'GET')
	{
		
		$header = array();
		$opts = array(CURLOPT_TIMEOUT => 10, CURLOPT_RETURNTRANSFER => 1, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false, CURLOPT_HTTPHEADER => $header);

		/* 根据请求类型设置特定参数 */
		switch (strtoupper($method)) {
			case 'GET' :
				$opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
				$opts[CURLOPT_URL] = substr($opts[CURLOPT_URL],0,-1);
				
				break;
			case 'POST' :
				//判断是否传输文件
				$params = http_build_query($params);
				$opts[CURLOPT_URL] = $url;
				$opts[CURLOPT_POST] = 1;
				$opts[CURLOPT_POSTFIELDS] = $params;
				break;
			default :
				break;
		}

		/* 初始化并执行curl请求 */
		$ch = curl_init();
		curl_setopt_array($ch, $opts);
		$data = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
		
		if($error){
			$data = null;
		}		
		return $data;
	}
	
	public function ceshi()
	{
		//file_put_contents('/www/wwwroot/test.7shou.com/ceshi.txt', date('Y-m-d H:i:s') . " JiLu");
		//echo $getdata = pretendData('http://forex.cnfol.com/fol_inc/v6.0/Gold/goldhq/json_table/EURUSD_forexdata.json');
		return json(false);
	}

	/**
	 * 全局平仓
	 * @return [type] [description]
	 */
	public function order()
	{
	    //echo date('Y-m-d H:i:s') . " order[1] -> ok \n\r";
		$oid = input('oid', 0, 'intval');
		$cache = cache('order:'.$oid);
		// if($cache){
		// 	return json(false);
		// }

		trace('api - order 全局平仓 oid : '.$oid, 'sql');
		
		$config = db('config')->field('value')->where(['name'=>'sys_limit','status'=>1])->find();
		$sys_limit = $config['value'];
		if($oid>0 && $sys_limit==0){
			return json(false);
		}

		$nowtime = time();
		$kong_end = getconf('kong_end');
		$kong_end_arr = explode('-',$kong_end );
		if(count($kong_end_arr) == 2){
			$s_rand = rand($kong_end_arr[0],$kong_end_arr[1]);
		}else{
			$s_rand = rand(6,12);
		}
		
		$db_order = db('order');
		$db_userinfo = db('userinfo');
		$prodata = array();
		//风控参数
		$risk = db('risk')->find();

		cache('order:'.$oid, 1, 2);

		//订单列表
		if($oid>0){
			$orderlist = $db_order->where(['oid'=>$oid])->select();
			if(!$orderlist){
				return json(false);
			}
			$pid = $orderlist[0]['pid'];
			$proItem = db('productdata')->field('Price')->where(['pid'=>$pid])->find();
			$prodata[$pid] = $proItem['Price'];
		}else{
			$map['ostaus'] = 0;
			//$map['selltime'] = array('elt',$nowtime+$s_rand);
			$_orderlist = $db_order->where($map)->order('selltime asc')->limit(5)->select();

			$data_info = db('productinfo');
			//此刻产品价格
			$p_map['isdelete'] = 0;
			$pro = db('productdata')->field('pid,Price')->where($p_map)->select();
			foreach ($pro as $k => $v) {
				$_pro = cache('nowdata');
				if(!isset($_pro[$v['pid']])){
					$prodata[$v['pid']] = $v['Price'];
					continue;
				}
				$prodata[$v['pid']] = $this->order_type($_orderlist,$_pro[$v['pid']],$risk,$data_info);
			}

			//订单列表
			$map['ostaus'] = 0;
			$map['selltime'] = array('elt',$nowtime);
			$orderlist = $db_order->where($map)->limit(0,50)->select();

			if(!$orderlist){
				return json(false);
			}
		}
		Db::startTrans(); // 启动事务
		try{
			//循环处理订单
			$nowtime = time();
			foreach ($orderlist as $k => $v) {
				//此刻可平仓价位
				$sellprice = isset($prodata[$v['pid']])?$prodata[$v['pid']]:0;

				if($sellprice == 0){
					continue;
				}

				//买入价
				$buyprice = $v['buyprice'];
				$fee = $v['fee'];
				$order_cha = round(floatval($sellprice)-floatval($buyprice), 6);

				/**
				 * 判断
				 * 纠正输赢逆反的结局
				 * 逆方向，波动小一点
				 * && abs($order_cha)/$v['point']<10
				 * 波动小于10%，才调整
				 */
				$gain = 0;
				// 判断时间段
				$is_win = 0;
				if($sys_limit==0) {
					if($v['kong_type']==3){ // 单控-全赢
						$risk['special_yk'] = 1;
						$risk['to_win'] .= '|'.$v['uid'];
						$risk['to_loss'] = '';
					}elseif($v['kong_type']==4){ // 单控-全输
						$risk['special_yk'] = 1;
						$risk['to_loss'] .= '|'.$v['uid'];
						$risk['to_win'] = '';
					}elseif($v['kong_type']==1){ // 单控-全赢
						$risk['to_win'] .= '|'.$v['uid'];
						$risk['to_loss'] = '';
					}elseif($v['kong_type']==2){ // 单控-全输
						$risk['to_loss'] .= '|'.$v['uid'];
						$risk['to_win'] = '';
					}elseif($risk['time1'] && $risk['time2']) {
						$time1 = strtotime(date('Y-m-d').' '.$risk['time1']);
						$time2 = strtotime(date('Y-m-d').' '.$risk['time2']);
						if($time1<=time() && time()<=$time2){
							$is_win = 1;
						}
					}
					if($is_win==1 || strpos($risk['to_win']."", $v['uid']."")!==false){
						if($v['ostyle'] == 1){
							if($risk['special_yk']==1){ // 翻倍盈利
								$rand = mt_rand(9600,9900)/100;
								$sellprice = round($v['buyprice']-$rand*$v['point'], 6);
							}else{
								$gain = mt_rand($risk['min_gain']*100,$risk['max_gain']*100)/100*($v['endprofit']/10);
								$sellprice = round($v['buyprice']-$gain*$v['point'], 6);
							}
						}elseif($v['ostyle'] == 0){
							if($risk['special_yk']==1){ // 翻倍盈利
								$rand = mt_rand(9600,9900)/100;
								$sellprice = round($v['buyprice']+$rand*$v['point'], 6);
							}else{
								$gain = mt_rand($risk['min_gain']*100,$risk['max_gain']*100)/100*($v['endprofit']/10);
								$sellprice = round($v['buyprice']+$gain*$v['point'], 6);
							}
						}
						db('productdata')->where(['pid'=>$v['pid']])->update(['Price'=>$sellprice]);
						$order_cha = round(floatval($sellprice)-floatval($buyprice), 6);

					}elseif(strpos($risk['to_loss']."", $v['uid']."")!==false){
						if($v['ostyle'] == 0){
							if($risk['special_yk']==1){ // 全输光
								$rand = mt_rand(9600,9900)/100;
								$sellprice = round($v['buyprice']-$rand*$v['point'], 6);
							}else{
								$gain = mt_rand($risk['min_gain']*100,$risk['max_gain']*100)/100*($v['endloss']/10);
								$sellprice = round($v['buyprice']-$gain*$v['point'], 6);
							}
						}elseif($v['ostyle'] == 1){
							if($risk['special_yk']==1){ // 全输光
								$rand = mt_rand(9600,9900)/100;
								$sellprice = round($v['buyprice']+$rand*$v['point'], 6);
							}else{
								$gain = mt_rand($risk['min_gain']*100,$risk['max_gain']*100)/100*($v['endloss']/10);
								$sellprice = round($v['buyprice']+$gain*$v['point'], 6);
							}
						}
						db('productdata')->where(['pid'=>$v['pid']])->update(['Price'=>$sellprice]);
						$order_cha = round(floatval($sellprice)-floatval($buyprice), 6);
					}else{
						$is_dist = 0;
						// 未指定客户输赢
						$percent = round(($sellprice-$v['buyprice'])/$v['point'], 4);
						if($risk['min_lost']>0){
							if($percent>0 && $percent<$risk['min_lost']){
								$sellprice = round($v['buyprice']+$risk['min_lost']*$v['point']*mt_rand(7,10)/10, 6);
								$is_dist = 1;
							}elseif($percent<0 && abs($percent)<$risk['min_lost']){
								$sellprice = round($v['buyprice']-$risk['min_lost']*$v['point']*mt_rand(7,10)/10, 6);
								$is_dist = 1;
							}
						}
						if($risk['max_lost']>0){
							if($percent>0 && $percent>$risk['max_lost']) {
								$sellprice = round($v['buyprice']+$risk['max_lost']*$v['point']*mt_rand(7,10)/10, 6);
								$is_dist = 1;
							}elseif($percent<0 && abs($percent)>$risk['max_lost']) {
								$sellprice = round($v['buyprice']-$risk['max_lost']*$v['point']*mt_rand(7,10)/10, 6);
								$is_dist = 1;
							}
						}
						if($is_dist==1){
							db('productdata')->where(['pid'=>$v['pid']])->update(['Price'=>$sellprice]);
							$order_cha = round(floatval($sellprice)-floatval($buyprice), 6);
						}
					}
				}

				//买涨
				if($v['ostyle'] == 0 && (($sys_limit==1 && $oid>0) || ($nowtime >= $v['selltime']))) {
					if($order_cha > 0){  //盈利
				// 		$proloss = round($order_cha/$v['point'], 4);
				// 		if($sys_limit==1 && $oid>0 && $proloss<$v['endprofit']){ // 没有触及警戒线
				// 			return json(false);
				// 		}
				// 		if($risk['special_yk']==1){
							
				// 		}elseif($gain>0){

				// 		}else{
				// 			$proloss = $proloss>$v['endprofit'] ? $v['endprofit'] : $proloss;
				// 		}
				        $arr = explode(',',$v['endloss']);
				        $proloss = mt_rand($arr[0]*$v['fee'],$arr[1]*$v['fee']) / $v['fee'];
						$yingli = $v['fee']*($proloss/100);
						// 判断
						$flag = $this->upOrder($v['oid'], $sellprice, $yingli, 1);
						if(!$flag){
							return json(false);
						}
						//平仓增加用户金额
						$u_add = $yingli + $fee;
						$db_userinfo->where('uid',$v['uid'])->setInc('usermoney',$u_add);
						//写入日志
						$this->set_order_log($v,$u_add);
					}elseif($order_cha < 0){	//亏损
				// 		$proloss = round(-$order_cha/$v['point'], 4);
				// 		if($sys_limit==1 && $oid>0 && $proloss<$v['endloss']){ // 没有触及警戒线
				// 			return json(false);
				// 		}
				// 		if($risk['special_yk']==1){
							
				// 		}elseif($gain>0){

				// 		}else{
				// 			$proloss = $proloss>$v['endloss'] ? $v['endloss'] : $proloss;
				// 		}
				        $arr = explode(',',$v['endprofit']);
				        $proloss = mt_rand($arr[0]*$v['fee'],$arr[1]*$v['fee']) / $v['fee'];
						$yingli = -$v['fee']*($proloss/100);
						// 判断
						$flag = $this->upOrder($v['oid'], $sellprice, $yingli, 2);
						if(!$flag){
							return json(false);
						}
						//平仓增加用户金额
						$u_add = $yingli + $fee;
						$db_userinfo->where('uid',$v['uid'])->setInc('usermoney',$u_add);
						$this->set_order_log($v,$u_add);
					}else{		//无效
						$yingli = 0;
						// 判断
						$flag = $this->upOrder($v['oid'], $sellprice, $yingli, 3);
						if(!$flag){
							return json(false);
						}
						//平仓增加用户金额
						$u_add = $fee;
						$db_userinfo->where('uid',$v['uid'])->setInc('usermoney',$u_add);
						//写入日志
						$this->set_order_log($v,$u_add);
					}

					//平仓处理订单
					$d_map['ostaus'] = 1;
					$d_map['sellprice'] = $sellprice;
					$d_map['ploss'] = $yingli;
					$d_map['oid'] = $v['oid'];
					db('order')->update($d_map);

				//买跌
				}elseif($v['ostyle']==1 && (($sys_limit==1 && $oid>0) || ($nowtime >= $v['selltime']))) {
					if($order_cha < 0){  //盈利
				// 		$proloss = round(-$order_cha/$v['point'], 4);
				// 		if($sys_limit==1 && $oid>0 && $proloss<$v['endprofit']){ // 没有触及警戒线
				// 			return json(false);
				// 		}
				// 		if($risk['special_yk']==1){
							
				// 		}elseif($gain>0){

				// 		}else{
				// 			$proloss = $proloss>$v['endprofit'] ? $v['endprofit'] : $proloss;
				// 		}
				        $arr = explode(',',$v['endloss']);
				        $proloss = mt_rand($arr[0]*$v['fee'],$arr[1]*$v['fee']) / $v['fee'];
						$yingli = $v['fee']*($proloss/100);
						// 判断
						$flag = $this->upOrder($v['oid'], $sellprice, $yingli, 1);
						if(!$flag){
							return json(false);
						}
						//平仓增加用户金额
						$u_add = $yingli + $fee;
						$db_userinfo->where('uid',$v['uid'])->setInc('usermoney',$u_add);
						//写入日志
						$this->set_order_log($v,$u_add);
					}elseif($order_cha > 0){	//亏损
				// 		$proloss = round($order_cha/$v['point'], 4);
				// 		if($sys_limit==1 && $oid>0 && $proloss<$v['endloss']){ // 没有触及警戒线
				// 			return json(false);
				// 		}
				// 		if($risk['special_yk']==1){
							
				// 		}elseif($gain>0){

				// 		}else{
				// 			$proloss = $proloss>$v['endloss'] ? $v['endloss'] : $proloss;
				// 		}
				        $arr = explode(',',$v['endprofit']);
				        $proloss = mt_rand($arr[0]*$v['fee'],$arr[1]*$v['fee']) / $v['fee'];
						$yingli = -$v['fee']*($proloss/100);
						// 判断
						$flag = $this->upOrder($v['oid'], $sellprice, $yingli, 2);
						if(!$flag){
							return json(false);
						}
						//平仓增加用户金额
						$u_add = $yingli + $fee;
						$db_userinfo->where('uid',$v['uid'])->setInc('usermoney',$u_add);
						$this->set_order_log($v,$u_add);
					}else{		//无效
						$yingli = 0;
						// 判断
						$flag = $this->upOrder($v['oid'], $sellprice, $yingli, 3);
						if(!$flag){
							return json(false);
						}
						//平仓增加用户金额
						$u_add = $fee;
						$db_userinfo->where('uid',$v['uid'])->setInc('usermoney',$u_add);
						//写入日志
						$this->set_order_log($v,$u_add);
					}
				}
			}
			// 提交事务
			Db::commit();
			return json(true);
		} catch (\Exception $e) {
			// 回滚事务
			Db::rollback();
			return json(false);
		}
	}

	//平仓处理订单
	private function upOrder($oid, $sellprice, $yingli, $is_win) {		
		//平仓处理订单
		$d_map['ostaus'] = 1;
		$d_map['is_win'] = $is_win;
		$d_map['ploss'] = $yingli;
		$d_map['sellprice'] = $sellprice;
		return db('order')->where(['ostaus'=>0,'oid'=>$oid])->update($d_map);
	}



	/**
	 * 写入平仓日志
	 * @author lukui  2017-07-01
	 * @param  [type] $v        [description]
	 * @param  [type] $addprice [description]
	 */
	public function set_order_log($v,$addprice)
	{
		$o_log['uid'] = $v['uid'];
       	$o_log['oid'] = $v['oid'];
       	$o_log['addprice'] = $addprice;
       	$o_log['addpoint'] = 0;
       	$o_log['time'] = time();
       	$o_log['user_money'] = db('userinfo')->where('uid',$v['uid'])->value('usermoney');
       	db('order_log')->insert($o_log);

       	//资金日志
       	set_price_log($v['uid'],1,$addprice,'结单','订单到期获利结算',$v['oid'],$o_log['user_money']);
	}


	/**
	 * 订单类型
	 * @param  [type] $orders [description]
	 * @return [type]         [description]
	 */
	public function order_type($orders,$pro,$risk,$data_info)
	{
		$_prcie = $pro['Price'];

		$pid = $pro['pid'];
		$thispro = array();		//买此产品的用户
		
		//买涨金额，计算过盈亏比例以后的
		$up_price = 0;
		//买跌金额，计算过盈亏比例以后的
		$down_price = 0;
		//买入最低价
		$min_buyprice = 0;
		//买入最高价
		$max_buyprice = 0;
		//下单最大金额
		$max_fee = 0;

		$dankong_ying = 0;
		$dankong_kui = 0;
		$is_to_win = 0;
		$is_to_loss = 0;

		//指定客户亏损
		$to_win = explode('|',$risk['to_win']);
		$to_win = array_filter(array_merge($to_win,$this->user_win));
		//指定客户亏损
		$to_loss = explode('|',$risk['to_loss']);
		$to_loss = array_filter(array_merge($to_loss,$this->user_loss));

		$proinfo = $data_info->where('pid',$pro['pid'])->find();

		$i = 0;
		$point = 0;
		$sum_proloss = 0;
		$max_proloss = 0;
		foreach ($orders as $k => $v) {
			if($v['pid'] == $pid ){
				//没炒过最小风控值直接退出price
				if ($v['fee'] < $risk['min_price']) {
					return $pro['Price'];
				}
				$i++;
				$point = $v['point'];
				$sum_proloss += $v['endprofit']+$v['endloss'];
				if($v['endprofit']>$max_proloss){
					$max_proloss = $v['endprofit'];
				}elseif($v['endloss']>$max_proloss){
					$max_proloss = $v['endloss'];
				}
			
				//单控 赢利  
				if($v['kong_type'] == '1' || $v['kong_type'] == '3'){
					$dankong_ying = 1;
					break;
				}
		
				//单控 亏损  
				if($v['kong_type'] == '2' || $v['kong_type'] == '4'){
					$dankong_kui = 1;
					break;
				}

				//是否存在指定盈利
				if(in_array($v['uid'], $to_win)){
					$is_to_win = 1;
					break;
				}
				//是否存在指定亏损
				if(in_array($v['uid'], $to_loss)){
					$is_to_loss = 1;
					break;
				}

				//买涨买跌累加
				if($v['ostyle'] == 0){
					$up_price += $v['fee']*$v['endprofit']/100;
				}else{
					$down_price += $v['fee']*$v['endloss']/100;
				}
				//统计最大买入价与最大下单价
				if($i == 1){
					$min_buyprice = $v['buyprice'];
					$max_buyprice = $v['buyprice'];
					$max_fee = $v['fee'];
				}else{
					if($min_buyprice > $v['buyprice']){
						$min_buyprice = $v['buyprice'];
					}
					if($max_buyprice < $v['buyprice']){
						$max_buyprice = $v['buyprice'];
					}
					if($max_fee < $v['fee']){
						$max_fee = $v['fee'];
					}
				}
			}
		}

		//是否存在指定盈利
		$is_do_price = 0; 	//是否已经操作了价格
		// 随机
		if($max_proloss<3){
			$s_base = mt_rand(18,30)/10;
		}else{
			$base = $sum_proloss/$i*10/mt_rand(21,66);
			$s_base = mt_rand(round($base*1000), round($max_proloss*1000))/1000;
		}
		$do_rand = round($point*$s_base, 6);


		//先考虑单控
		if($dankong_ying==1 && $is_do_price == 0){ 	//单控 1赢利
			if($v['ostyle'] == 0 ){
				$pro['Price'] = $v['buyprice'] + $do_rand;
			}elseif($v['ostyle'] == 1 ){
				$pro['Price'] = $v['buyprice'] - $do_rand;
			}
			$is_do_price = 1;
		}elseif($dankong_kui==1 && $is_do_price == 0){ //单控 2亏损
			if($v['ostyle'] == 0  ){
				$pro['Price'] = $v['buyprice'] - $do_rand;
			}elseif($v['ostyle'] == 1 ){
				$pro['Price'] = $v['buyprice'] + $do_rand;
			}			
			$is_do_price = 1;
		}elseif($is_to_win==1 && $is_do_price == 0){ //指定客户赢利
			if($is_to_win['ostyle'] == 0 ){
				$pro['Price'] = $v['buyprice'] + $do_rand;
			}elseif($is_to_win['ostyle'] == 1 ){
				$pro['Price'] = $v['buyprice'] - $do_rand;
			}
			$is_do_price = 1;			
		}elseif($is_to_loss==1 && $is_do_price == 0){ //是否存在指定亏损
			if($is_to_loss['ostyle'] == 0 ){
				$pro['Price'] = $v['buyprice'] - $do_rand;
			}elseif($is_to_loss['ostyle'] == 1 ){
				$pro['Price'] = $v['buyprice'] + $do_rand;
			}
			$is_do_price = 1;
		}
		
		if($up_price == 0 && $down_price == 0 && $is_do_price == 0){//没有任何下单记录
			$is_do_price = 2;
		}		
		
		if(( ($up_price == 0 && $down_price != 0) || ($up_price != 0 && $down_price == 0) )  && $is_do_price == 0 ){//只有一个人下单，或者所有人下单买的方向相同
			//风控参数
			$chance = $risk["chance"];
			$chance_1 = explode('|',$chance);
			$chance_1 = array_filter($chance_1);
			//循环风控参数
			if(count($chance_1) >= 1){
				foreach ($chance_1 as $key => $value) {
					//切割风控参数
					$arr_1 = explode(":",$value);
					$arr_2 = explode("-",$arr_1[0]);
					//比较最大买入价格
					if($max_fee >= $arr_2[0] && $max_fee < $arr_2[1]){
						//得出风控百分比
						if(!isset($arr_1[1])){
							$chance_num = 30;
						}else{
							$chance_num = $arr_1[1];
						}						
						$_rand = rand(1,100);
						continue;						
					}					
				}
			}			
			
			//买涨
			if(isset($_rand) && $up_price != 0){
				if($_rand > $chance_num){	//客损
					$pro['Price'] = $min_buyprice-$do_rand;
					$is_do_price = 1;
				}else{		//客赢
					$pro['Price'] = $max_buyprice+$do_rand;
					$is_do_price = 1;
				}				
			}
			
			if(isset($_rand) && $down_price != 0){
				if($_rand > $chance_num){	//客损
					$pro['Price'] = $max_buyprice+$do_rand;
					$is_do_price = 1;
				}else{		//客赢
					$pro['Price'] = $min_buyprice-$do_rand;
					$is_do_price = 1;
				}				
			}
		}elseif($up_price != 0 && $down_price != 0  && $is_do_price == 0){ //多个人下单，并且所有人下单买的方向不相同
			//买涨大于买跌的
			if ($up_price > $down_price) {
				$pro['Price'] = $min_buyprice-$do_rand;
				$is_do_price = 1;				
			}
			//买涨小于买跌的
			if ($up_price < $down_price) {
				$pro['Price'] = $max_buyprice+$do_rand;
				$is_do_price = 1;
			}
			if ($up_price == $down_price) {
				$is_do_price = 2;
			}			
		}elseif($is_do_price == 2 || $is_do_price == 0){
			$pro['Price'] = $this->fengkong($pro['Price'],$proinfo);
		}
		
		db('productdata')->where('pid',$pro['pid'])->update($pro);
		return $pro['Price'];
	}



	/**
	 * 获取K线数据
	 * @author lukui  2017-07-01
	 * @return [type] [description]
	 */
	public function getkdata($pid=null,$num=null,$interval=null,$isres=null)
	{
		
		$pid = empty($pid)?input('param.pid'):$pid;
		$num = empty($num)?input('param.num'):$num;
		$num = empty($num)?30:$num;
		$pro = GetProData($pid);
		$all_data = array();

		if(!$pro){
			//echo 'data error!';
			exit;
		}
		$interval = empty($interval)?input('param.interval'):$interval;
		$interval = input('param.interval') ? input('param.interval') : 1;
		$nowtime = time().rand(100,999);
 
		//数据库里的产品K线参考值
		$klength = $interval*60*$num;
		if($klength == 'd') $klength = 1*60*24*$num;

		$k_map['pid'] = $pid;
		$k_map['ktime'] = array('between',array( ($this->nowtime - $klength),$this->nowtime ) );
		/*
		$kline = $this->klinedata->where($k_map)->select();
		foreach ($kline as $k => $v) {
			$_kline[$v['ktime']] = $v;
		}*/
      
        $pro['procode']=GetProcode($pid);
      
//print_r($pro);
      //  exit($pro['procode']);
		//guonei
		if($pro['procode'] == "btc" || $pro['procode'] == "ltc"|| $pro['procode'] == "eth"|| $pro['procode'] == "eos"|| $pro['procode'] == "ant"|| $pro['procode'] == "sol"){
	 
			switch ($interval) {				
				case '1':					
					$datalen = "1min";					
					break;				
				case '5':					
					$datalen = "5min";					
					break;				
				case '15':					$datalen = "15min";					break;				
				case '30':					$datalen = "30min";					break;				
				case '60':					$datalen = "1hour";					break;				
				case 'd':					$datalen = "1day";					break;								
				default:										
					exit;					
					break;			
				}			
				//////////////////star
			if($this->apiface == 1) {
				if($pro['procode'] == "btc"){				
					$geturl = "http://api.bitkk.com/data/v1/kline?market=btc_usdt&type=".$datalen."&size=".$num;			
				}elseif($pro['procode'] == "ltc"){				
					$geturl = "http://api.bitkk.com/data/v1/kline?market=ltc_usdt&type=".$datalen."&size=".$num."&contract_type=this_week";	
				}elseif($pro['procode'] == "eth"){				
					$geturl = "http://api.bitkk.com/data/v1/kline?market=eth_usdt&type=".$datalen."&size=".$num;
				}elseif($pro['procode'] == "eos"){				
					$geturl = "http://api.bitkk.com/data/v1/kline?market=eos_usdt&type=".$datalen."&size=".$num;
				}elseif($pro['procode'] == "ant"){				
					$geturl = "http://api.bitkk.com/data/v1/kline?market=ant_usdt&type=".$datalen."&size=".$num;
				}
				$html = file_get_contents($geturl);			
				$html = substr($html,25,-22);
				
				$_data_arr = explode('],[',$html);	

				foreach ($_data_arr as $k => $v) {
					$_son_arr = explode(',', $v);
					$res_arr[] = array($_son_arr[0]/1000,$_son_arr[1],$_son_arr[4],$_son_arr[3],$_son_arr[2]);
				}
			} else {
				if($pro['procode'] == "btc"){				
					$geturl = "https://api.huobi.pro/market/history/kline?symbol=btcusdt&period=".$datalen."&size=".$num;			
				}elseif($pro['procode'] == "ltc"){				
					$geturl = "https://api.huobi.pro/market/history/kline?symbol=ltcusdt&period=".$datalen."&size=".$num."&contract_type=this_week";	
				}elseif($pro['procode'] == "eth"){				
					$geturl = "https://api.huobi.pro/market/history/kline?symbol=ethusdt&period=".$datalen."&size=".$num;
				}elseif($pro['procode'] == "eos"){				
					$geturl = "https://api.huobi.pro/market/history/kline?symbol=eosusdt&period=".$datalen."&size=".$num;
				}elseif($pro['procode'] == "ant"){				
					$geturl = "https://api.huobi.pro/market/history/kline?symbol=antusdt&period=".$datalen."&size=".$num;
				}elseif($pro['procode'] == "sol"){				
					$geturl = "https://api.huobi.pro/market/history/kline?symbol=solusdt&period=".$datalen."&size=".$num;
				}
				$html = file_get_contents($geturl);			
				$res = json_decode($html, true);				
				$_data_arr = $res['data'];
				krsort($_data_arr);
				foreach ($_data_arr as $k => $v) {
					$res_arr[] = array($v['id'],$v['open'],$v['close'],$v['low'],$v['high']);
				}
			}
			////////////////end
			
/*************黄金白银K线数据开始*****************/	
		
		}elseif(in_array($pro['procode'],array("sz399300"))){
			if($interval == 'd'){
				$url = "http://web.ifzq.gtimg.cn/appstock/app/fqkline/get?_var=kline_dayqfq&param=".$pro['procode'].",day,,,$num,qfq&r=0.".$this->nowtime;
			}else{
				$url = "http://ifzq.gtimg.cn/appstock/app/kline/mkline?param=".$pro['procode'].",m$interval,,$num&_var=m".$interval."_today&r=0.".$this->nowtime;;
			}
			$res = $this->curlfun($url);
			$arr1 = explode('=',$res);
			$arr2 = json_decode($arr1[1],1);
			if($interval == 'd'){
				$arr3 = $arr2["data"][$pro['procode']]['day'];
			}else{
				$arr3 = $arr2["data"][$pro['procode']]['m'.$interval];
			}
			
			foreach($arr3 as $k=>$v){
				$_time = strtotime($v[0]);
				
				$res_arr[] = array($_time,$v[1],$v[2],$v[3],$v[4]);
			}
		
		}elseif(in_array($pro['procode'],array("USDJPY","GBPJPY","EURUSD","GBPUSD","EURGBP"))){
			$geturl = "http://forex.cnfol.com/fol_inc/v6.0/Gold/goldhq/json/w/".strtolower($pro['procode'])."/Kl5Min.json?t=0.".$nowtime;
			$html = $this->curlfun($geturl);
			
			$arr1 = json_decode($html,1);
			//krsort($arr1);
			
			$_count = count($arr1);
			$arr1 = array_slice($arr1,$_count-$num,$_count);
			foreach ($arr1 as $k => $v) {
				$_time = $v[0]/1000;
				
				$_h = $v[2];
				$_l = $v[3];
				$_o = $v[4];
				$_c = $v[1];
				$res_arr[] = array($_time,$_o,$_c,$_h,$_l);
			}

			
		}elseif(in_array($pro['procode'],array('llg','lls'))){
			if($interval == 'd') $interval = 1440;
			$geturl = "https://hq.91pme.com/query/kline?callback=jQuery183014447531082730047_".$nowtime."&code=".$pro['procode']."&level=".$interval."&maxrecords=".$num."&_=".$nowtime;

			$html = $this->curlfun($geturl);
			$str_1 = explode('[{',$html);
			if(!isset($str_1[1])){
				return;
			}
			$str_2 = substr($str_1[1],0,-4);
			$str_3 = explode('},{',$str_2);
			
			krsort($str_3);

			foreach ($str_3 as $k => $v) {
				
				$_son_arr = explode(',', $v);

				$_time = substr($_son_arr[11],7,-3);
				if(in_array($interval,array(1,5)) && isset($_kline[$_time])){
					$_h = $_kline[$_time]['updata'];
					$_l = $_kline[$_time]['downdata'];
					$_o = $_kline[$_time]['opendata'];
					$_c = $_kline[$_time]['closdata'];
				}else{
					$_h = substr($_son_arr[4],6,-1);
					$_l = substr($_son_arr[3],7,-1);
					$_o = substr($_son_arr[10],7,-1);
					$_c = substr($_son_arr[0],8,-1);
				}
				
				$res_arr[] = array($_time,$_o,$_c,$_h,$_l);

			}

		}elseif(in_array($pro['procode'],array(5,6,7,8,10,11,12,13,14,15,22,23,24,25,26,27,28,29,30,31,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,55,56,57,58,59,60,61,62,63,70,71,72,73,74,75,76,77,78,79,80,87,90,91,92,93,94,95,96,97,98,99,100,101,102,105,106,107,108,113,114,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,144,149,150,151,152,162,163,164,165,166,167,168))){
				if($interval == 1) $interval =1;
				if($interval == 5) $interval =2;
				if($interval == 15) $interval =3;
				if($interval == 30) $interval =4;
				if($interval == 60) $interval =5;
				if($interval == 'd') $interval =6;
				
				$geturl = 'https://m.sojex.net/api.do?rtp=CandleStick&type='.$interval.'&qid='.$pro['procode'];
				$html = $this->curlfun($geturl);
				$json = json_decode($html,true);
				$ret = $json['data']['candle'];

				foreach($ret as $v){
					$res_arr[] = array(
							$v['ts']/1000,
							$v['o'],
							$v['c'],
							$v['l'],
							$v['h'],
						);
				}
		}else{

			switch ($interval) {
				case '1':
					$datalen = 1440;
					break;
				case '5':
					$datalen = 1440;
					break;
				case '15':
					$datalen = 480;
					break;
				case '30':
					$datalen = 240;
					break;
				case '60':
					$datalen = 120;
					break;
				case 'd':
					
					break;
				
				default:
					exit;
					break;
			}
			
			$year = date('Y_n_j',time());
			
			if($interval == 'd'){
				$geturl = "http://vip.stock.finance.sina.com.cn/forex/api/jsonp.php/var%20_".$pro['procode']."$year=/NewForexService.getDayKLine?symbol=".$pro['procode']."&_=$year";
			}else{
				$geturl = "http://vip.stock.finance.sina.com.cn/forex/api/jsonp.php/var%20_".$pro['procode']."_".$interval."_$nowtime=/NewForexService.getMinKline?symbol=".$pro['procode']."&scale=".$interval."&datalen=".$datalen;
			}
			
			$html = $this->curlfun($geturl);

			if($interval == 'd'){
				$_arr = explode('("',$html);
				if(!isset($_arr[1])){
					return;
				}
				$_str = substr($_arr[1],1,-4);
				$_data_arr = explode(',|',$_str);
				
			}else{
				$_arr = explode('[',$html);
				if(!isset($_arr[1])){
					return;
				}
				$_str = substr($_arr[1],1,-3);
				$_data_arr = explode('},{',$_str);
				
			}

			$_count = count($_data_arr);
			$_data_arr = array_slice($_data_arr,$_count-$num,$_count);
			

			foreach ($_data_arr as $k => $v) {				
				$_son_arr = explode(',', $v);
				if($interval == 'd'){
					$res_arr[] = array(
								substr($_son_arr[0],5),
								$_son_arr[1],
								$_son_arr[4],
								$_son_arr[2],
								$_son_arr[3],
						);
				}else{
					$_time = strtotime(substr($_son_arr[0],3,-1));
					if(in_array($interval,array(1,5)) && isset($_kline[$_time])){
						$_h = $_kline[$_time]['updata'];
						$_l = $_kline[$_time]['downdata'];
						$_o = $_kline[$_time]['opendata'];
						$_c = $_kline[$_time]['closdata'];
					}else{
						$_h = substr($_son_arr[3],3,-1);
						$_l = substr($_son_arr[2],3,-1);
						$_o = substr($_son_arr[1],3,-1);
						$_c = substr($_son_arr[4],3,-1);
					}
					$res_arr[] = array($_time,$_o,$_c,$_h,$_l);
				}
			}
		}
		

		
		if($pro['Price'] < $res_arr[$num-1][1]){
			$_state = 'down';
		}else{
			$_state = 'up';
		}		
		
		$all_data['topdata'] = array(
								'topdata'=>strtotime("now"),
								'now'=>$pro['Price'],
								'open'=>$pro['Open'],
								'lowest'=>$pro['Low'],
								'highest'=>$pro['High'],
								'close'=>$pro['Close'],
								'state'=>$_state
							);
		
		$all_data['items'] = $res_arr;
		if($isres){
			return (json_encode($all_data));
		}else{
			exit(json_encode(base64_encode(json_encode($all_data))));
		}
	}

	//test web data
	public function setusers()
	{
		test_web();
	}



	public function getprodata()
	{		

		$pid = input('param.pid');
		$pro = GetProData($pid);		

		if(!$pro){
			exit;
		}

		$topdata = array(
						'topdata'=>$pro['UpdateTime'],
						'now'=>$pro['Price'],
						'open'=>$pro['Open'],
						'lowest'=>$pro['Low'],
						'highest'=>$pro['High'],
						'close'=>$pro['Close']

					);

		//exit(json_encode($topdata));
		exit(json_encode(base64_encode(json_encode($topdata))));

	}




	/**
	 * 分配订单
	 * @return [type] [description]
	 */
	public function allotorder()
	{
		return json(false);
		//查找以平仓未分配的订单  isshow字段
		$map['isshow'] = 0;
		$map['ostaus'] = 1;
		$map['selltime'] = array('<',time()-300);
		$list = db('order')->where($map)->limit(0,10)->select();

		if(!$list){
			return json(false);
		}

		foreach ($list as $k => $v) {
			//分配金额
			$this->allotfee($v['uid'],$v['fee'],$v['is_win'],$v['oid'],$v['ploss']);
			//更改订单状态
			db('order')->where('oid',$v['oid'])->update(array('isshow'=>1));

		}
		//dump($list);
	}



	private function allotfee($uid,$fee,$is_win,$order_id,$ploss)
	{
		$userinfo = db('userinfo');
		$allot = db('allot');
		$nowtime = time();

		$user = $userinfo->field('uid,oid')->where('uid',$uid)->find();
		$myoids = myupoid($user['oid']);

		

		if(!$myoids){
			return;
		}
		
		//红利
		$_fee = 0;
		//佣金
		$_feerebate = 0;
		//手续费
		$web_poundage = getconf('web_poundage');
		//分配金额
		if($is_win == 1){
			$pay_fee = $ploss;
		}elseif($is_win == 2){
			$pay_fee = $fee;
		}else{
			//20170801 edit
			return;
		}
		
		
		foreach ($myoids as $k => $v) {

			if($user['oid'] == $v['uid']){	//直接推荐者拿自己设置的比例

				
				$_fee = round($pay_fee * ($v["rebate"]/100),2);
				$_feerebate = round($fee*$web_poundage/100 * ($v["feerebate"]/100),2);
				echo $_feerebate;

			}else{		//他上级比例=本级-下级比例
				
				$_my_rebate = ($v["rebate"] - $myoids[$k-1]["rebate"]);
				
				if($_my_rebate < 0) $_my_rebate = 0;
				$_fee = round($pay_fee * ( $_my_rebate /100),2);
				
				$_my_feerebate = ($v["feerebate"]  - $myoids[$k-1]["feerebate"] );
				if($_my_feerebate < 0) $_my_feerebate = 0;
				$_feerebate = round($fee*$web_poundage/100 * ( $_my_feerebate /100),2);

				
			}
			
			
			//红利
			if($is_win == 1){	//客户盈利代理亏损
				if($_fee != 0){
					$ids_fee = $userinfo->where('uid',$v['uid'])->setDec('usermoney', $_fee);
				}else{
					$ids_fee = null;
				}

				$type = 2;
				$_fee = $_fee*-1;
			}elseif($is_win == 2){	//客户亏损代理盈利
				if($_fee != 0){
					$ids_fee = $userinfo->where('uid',$v['uid'])->setInc('usermoney', $_fee);
				}else{
					$ids_fee = null;
				}
				
				$type = 1;
			}elseif($is_win == 3){	//无效订单不做操作
				$ids_fee = null;
			}

			if($ids_fee){
				//余额
				$nowmoney = $userinfo->where('uid',$v['uid'])->value('usermoney');
				set_price_log($v['uid'],$type,$_fee,'对冲','下线客户平仓对冲',$order_id,$nowmoney);
				
			}

			//手续费
			if($_feerebate != 0){
				$ids_feerebate = $userinfo->where('uid',$v['uid'])->setInc('usermoney', $_feerebate);
			}else{
				$ids_feerebate = null;
			}

			if($ids_feerebate){
				//余额
				$nowmoney = $userinfo->where('uid',$v['uid'])->value('usermoney');
				set_price_log($v['uid'],1,$_feerebate,'客户手续费','下线客户下单手续费',$order_id,$nowmoney);
				
			}

		}
		

	}



	/**
	 * 获取K线。缓存起来
	 * @author lukui  2017-08-13
	 * @return [type] [description]
	 */
	public function cachekline()
	{
		
		$res[$v['pid']][1] = $this->getkdata(38,60,1,1);

	}

	function getkline(){

		$kline = cache('cache_kline');
		$pid = input('pid');
		$interval = input('interval');

		if(!$interval || !$pid){
			return json(false);
		}

		$info = json_decode($kline[$pid][$interval],1);

		return exit(json_encode($info));;
		
	}
	
	
	
	public function checkbal(){
		return json(false);
		$lttime = $this->nowtime-10*60;
		$map['bptime'] = array('lt',$lttime);
		$map['pay_type'] = array('in',array('ysy_alwap','ysy_wxwap'));
		$map['bptype'] = 3;
		$db_balance = db('balance');
		$list = $db_balance->where($map)->select();
		
		if(!$list) return json(false);
		
		foreach($list as $key=>$val){
			
			$miyao="5ca7b74af0d54b2483c1a9e75bb935fd";
			$mchntid = '308652650940006';
			$inscd = '93081888';

				$data = array();
				$qxdata = array();
				$data['version'] = "2.2";
				$data['signType'] = 'SHA256';
				$data['charset'] = 'utf-8';
				$data['origOrderNum'] = $val['balance_sn'];
				$data['busicd'] = 'INQY';
				//$data['respcd'] = '00';
				$data['inscd'] = $inscd;
				$data['mchntid'] = $mchntid;
				$data['terminalid'] = $inscd;
				$data['txndir'] = 'Q';
				ksort($data);
				$str = '';
				foreach($data as $k=>$v){
					if($str!=''){
						$str .= '&';
					}
					$str .= $k.'='.$v;
				}
				$str.= $miyao;
				$sign=hash("sha256", $str);
				$data['sign'] = $sign;
				$data=json_encode($data);
				$pc = json_decode($this->post_curl('https://showmoney.cn/scanpay/unified',$data),true);
				
						
				if($pc['errorDetail']=='待买家支付'){ 
						
					$qxdata['busicd'] = 'CANC';
					$qxdata['charset'] = 'utf-8';
					$qxdata['inscd'] = $inscd;
					$qxdata['mchntid'] = $mchntid;
					$qxdata['orderNum'] = time().rand(1000,9999);
					$qxdata['origOrderNum'] = $val['balance_sn'];
					$qxdata['signType'] = 'SHA256';
					$qxdata['terminalid'] = $inscd;
					$qxdata['txndir'] = 'Q';
					$qxdata['version'] = '2.2';
					ksort($qxdata);
					$str = '';
					foreach($qxdata as $k=>$v){
						if($str!=''){
							$str .= '&';
						}
						$str .= $k.'='.$v;
					}
					$str.= $miyao;
					$qxdata['sign'] = hash("sha256", $str);
					$qpc = json_decode($this->post_curl('https://showmoney.cn/scanpay/unified',json_encode($qxdata)),true);
					
					
					
					
				}elseif($pc['errorDetail']=='成功'){
					
				}elseif($pc['errorDetail']=='订单不存在'){
					
				}
				
				$_map['bpid'] = $val['bpid'];
				$_map['bptype'] = 4;
				$db_balance->update($_map);
				
		}
		
		
	}
	
	public function post_curl($url,$data){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			print curl_error($ch);
		}
		curl_close($ch);
		return $result;
	}

	// 会员投资收益
	public function interest() {
		$hi = date('Hi');
		if($hi == '0430'){ // 清除3天前的日志
			$dir = RUNTIME_PATH.'Log';
			$vpaths = scandir($dir);
			foreach($vpaths as $vpath){
				$path = $dir.DIRECTORY_SEPARATOR.$vpath;
				$files = scandir($path);
				foreach($files as $file){
					$realfile = $path.DIRECTORY_SEPARATOR.$file;
					if($file !='.' && $file !='..' && filemtime($realfile)<strtotime('-3 day')){
						@unlink($realfile);
					}
				}
			}
		}else{
			$userinvest = Db::name('userinvest')->field('id,uid,username,money,interest,state')->where(['state'=>1,'totime'=>['elt',time()]])->find();
			if(!$userinvest){
				return json(WPreturn('暂无可操作订单！',1));
			}
			if($userinvest['state'] != 1){
				return json(WPreturn('此订单已操作',-1));
			}
			//获取个人信息
			$userinfo = Db::name('userinfo')->field('uid,username,usermoney')->where('uid',$userinvest['uid'])->find();
			$id = $userinvest['id'];
			if(empty($userinfo)){
			    Db::name('userinvest')->where(['state'=>1,'id'=>$id])->update(['state'=>0]);
				return json(WPreturn('回款失败，缺少参数!',-1));
			}
			$ret = Db::name('userinvest')->where(['id'=>$id,'state'=>1])->update(['state'=>2,'totime'=>time()]);
			if($ret){
				$sys_yue_benjin = getconf('sys_yue_benjin');
				if($sys_yue_benjin==2){
					db('userinfo')->where('uid',$userinvest['uid'])->setInc('usermoney',$userinvest['money']+$userinvest['interest']);
				}else{
					db('userinfo')->where('uid',$userinvest['uid'])->setInc('usermoney',$userinvest['interest']);
				}
				db('userinfo')->where('uid',$userinvest['uid'])->setDec('freeze',$userinvest['money']);
				//资金日志
				if($sys_yue_benjin==2){
					set_price_log($userinvest['uid'],1,$userinvest['money'],'利息宝','会员投资本金',$id,$userinfo['usermoney']);
					set_price_log($userinvest['uid'],1,$userinvest['interest'],'利息宝','会员投资利息',$id,$userinfo['usermoney']+$userinvest['money']);
				}else{
					set_price_log($userinvest['uid'],1,$userinvest['interest'],'利息宝','会员投资利息',$id,$userinfo['usermoney']);
				}
				return json(WPreturn('操作成功！',1));
			}else{
				return json(WPreturn('操作失败！',-1));
			}
		}
	}

}
