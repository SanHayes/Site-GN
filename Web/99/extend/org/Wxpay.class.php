<?php
namespace Org\Pay;
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);

class Wxpay {

	public function __construct() {
		/* 初始化日志 */
		vendor('BizWxpay.WxPay#Log');
		$_log_handler = new \ClogFileHandler(ROOT_PATH."Public".DIRECTORY_SEPARATOR."_logs".DIRECTORY_SEPARATOR."wxpay".DIRECTORY_SEPARATOR.date('Y-m-d').'.log');
		\Log::Init($_log_handler, 15);
	}

	/**
	 * 返回支付模块安装信息
	 * @access public
	 * @author ruby
	 * @version 20141114
	 */
	public function info() {
		$modules ['pay_name'] = "微信支付";
		$modules ['pay_code'] = 'Wxpay';
		$modules ['pay_desc'] = "微信支付";
		$modules ['iscod'] = '0';
		$modules ['isonline'] = '1';
		$modules ['config'] = "";
		return $modules;
	}

	public function unifiedOrder($_data, $trade_type = "JSAPI") {
		if($trade_type == "JSAPI") {
			vendor('BizWxpay.WxPay#JsApiPay');
			$_tools = new \JsApiPay();
			//$_openid = $_tools->GetOpenid();
			$_openid = $_data['openid'];
		} elseif($trade_type == "NATIVE") {
			vendor('BizWxpay.WxPay#NativePay');
			$_tools = new \NativePay();
		}
		
		$_input = new \WxPayUnifiedOrder();
		$_out_trade_no = !empty($_data['out_trade_no']) ? "$_data[out_trade_no]" : \WxPayConfig::MCHID.date("YmdHis");
		$_body = !empty($_data['body']) ? $_data['body'] : '';
		/* 设置商品或支付单简要描述 */
		$_input->SetBody($_body);
		/* 设置商户系统内部的订单号 */
		$_input->SetOut_trade_no($_out_trade_no);
		/* 设置订单总金额 */
		$_input->SetTotal_fee($_data['total_fee'] * 100);
		$_input->SetTime_start(date("YmdHis"));
		/* 设置接收微信支付异步通知回调地址 */
		$_input->SetNotify_url(\WxPayConfig::NOTIFY_URL);
		/* 设置取值如下：JSAPI，NATIVE，APP */
		$_input->SetTrade_type($trade_type);
		if($trade_type == "JSAPI") {
			$_input->SetDetail($_data['detail'] ? $_data['detail'] : '');
			/* 设置trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识 */
			$_input->SetOpenid($_openid);
			$_order = \WxPayApi::unifiedOrder($_input);
			// dump($_order);exit; // for debug
			if($_order['return_code'] == "FAIL") {
				exit('微信方回复：'.$_order['return_msg']);
			}
			if($_order['result_code'] == "FAIL") {
				if($_order['err_code'] == 'OUT_TRADE_NO_USED') {
					// 重新生成订单号
					$_sub_data = M('Order')->where(array('id'=>array('in', $_out_trade_no)))->select();
					foreach ($_sub_data as $value) {
						$_origin_oid = $value['id'];
						unset($value['id']);
						$value['createtime'] = date('Y-m-d H:i:s');
						$_out_trade_nos[] = M('Order')->add($value);
						foreach ($_out_trade_nos as $_val) { 
							M('OrderGoods')->where(array('order_id'=>array('in', $_origin_oid)))->save(array('order_id'=>$_val));
						}
						M('Order')->where(array('id'=>$_origin_oid))->save(array('is_delete'=>1));
					}
					exit('<script>alert("改价后的订单需要重新生成编号，请重新支付");setTimeout(function() { location.href = "'.U('Home/User/orders').'"}, 1000);</script>');
				} else {
					exit('微信方回复：'.$_order['err_code'].' '.$_order['err_code_des']);
				}
			}
			$_result['_js_api_parameters'] = $_tools->GetJsApiParameters($_order);
			$_result['_edit_address_parameters'] = $_tools->GetEditAddressParameters();
			return $_result;
		} elseif($trade_type == "NATIVE") {
			$_input->SetProduct_id($_data['product_id']);
			$_url = $_tools->GetPayUrl($_input);
			!empty($_GET['debug']) && dump($_input);

			if($_url['result_code'] == "FAIL") {
				!empty($_GET['debug']) && dump($_url);
				// return $_url;
			}
			if($_url['return_code'] == "FAIL") {
				!empty($_GET['debug']) && dump($_url);
				// return $_url['return_msg'];
			}
			return $_url["code_url"];
		}
	}

	public function shareScript($_js_api_parameters) {
		return "<script type='text/javascript'>
	//调用微信JS api 支付
	function jsApiCall() {
		WeixinJSBridge.invoke('getBrandWCPayRequest',<?php echo $_js_api_parameters; ?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				alert(res.err_code+res.err_desc+res.err_msg);
			}
		);
	}
	function callpay() {
		if (typeof WeixinJSBridge == \"undefined\"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	</script>";
	}
	
	
	public function refund($_oid,$amount,$re_amount){
		vendor('BizWxpay.WxPay#Data');
		vendor('BizWxpay.WxPay#Api');
		$_input = new \WxPayRefund();
		
		$_input->SetOut_trade_no($_oid);
		$_input->SetTotal_fee($amount*100);
		$_input->SetRefund_fee($re_amount*100);
		$_input->SetOut_refund_no(\WxPayConfig::MCHID.date("YmdHis"));
		
		$_input->SetOp_user_id(\WxPayConfig::MCHID);
		$res=\WxPayApi::refund($_input);
		F('WXBACKRES',$res);
		return $res;
	}
	
	
	
	
	
	
}