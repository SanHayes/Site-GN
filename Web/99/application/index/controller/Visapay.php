<?php
namespace app\api\controller;
use think\Controller;
use think\Db;

class Visapay extends Controller
{
    /**
     * visapay
     * @return mixed
     */
	public function pay() {
	    //充值金额
        $amount = $_REQUEST['amount'];
        if(!$amount){
            return json(['code'=>-5,'data'=>[],'msg'=>'充值金额无效']);
        }
        //充值账户
        $uid = $_REQUEST['uid'];
        if(!$uid){
            return json(['code'=>-1,'data'=>[],'msg'=>'账号ID无效']);
        }
        
        $email = $_REQUEST['email']?$_REQUEST['email']:'';
        $cellphone = $_REQUEST['cellphone']?$_REQUEST['cellphone']:'';
        
        $user_money = db('userinfo')->where('uid',$uid)->value('usermoney');
        $Paydata['uid']     = $uid;//对应会员ID
        $Paydata['balance_sn']    = $uid.rand(111,999).date("YmdHis");    //订单号
        $Paydata['bpprice']      = $amount;    //交易金额
        $Paydata['bptype'] = 3;  //正在充值
        $Paydata['remarks'] = 'Online recharge';  //正在充值
        $Paydata['pay_type'] =  'visapay';  //正在充值
        $Paydata['btime']  = time();//充值时间
        $Paydata['bptime']  = time();//充值时间
        $Paydata['bpbalance'] = $user_money;
        
        $merchantNum = '16Gff ex';
        $orderNo = $Paydata['balance_sn'];
        $fiat = 'USD';
        $payType = $_REQUEST['payType'] ? $_REQUEST['payType'] :'creditCard';
        $notifyUrl='https://'.$_SERVER['HTTP_HOST'].'/99/api/visapay/notify';
        $returnUrl = 'https://'.$_SERVER['HTTP_HOST'].'/h5/#/pages/fund/receive_withdraw_record';
        $token = "c9258af6ff809ead3a1aa0cf62cdddb4";

        $sign = md5($merchantNum . $orderNo . $amount . $notifyUrl . $token);
        $postUrl = 'http://yffmzq.kowloon.vip/api/startOrder';

        $postData = array(
            'merchantNum'=>$merchantNum,
            'orderNo'=>$orderNo,
            'amount'=>$amount,
            'fiat'=>$fiat,
            'payType'=>$payType,
            'notifyUrl'=>$notifyUrl,
            'returnUrl'=>$returnUrl,
            'email'=>$email,
            'cellphone'=>$cellphone,
            'attch'=>$uid,
            'sign'=>$sign
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $postUrl);
        curl_setopt($curl, CURLOPT_USERAGENT,'Opera/9.80 (Windows NT 6.2; Win64; x64) Presto/2.12.388 Version/12.15');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // stop verifying certificate
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $exec = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($exec,true);
        file_put_contents("/www/wwwroot/Site/Web/99/pay.txt", $orderNo.'->'.$exec."\r\n", FILE_APPEND);
        if($res['success']){
            $result= db('balance')->insert($Paydata);
            if(!$result){
                return json(['code'=>-105,'data'=>[],'msg'=>'订单写入失败']);
            } else {
                return json(['code'=>200,'data'=>['payUrl'=>$res['data']['payUrl']],'msg'=>$res['msg']]);
            }
        } else {
            return json(['code'=>-301,'data'=>[],'msg'=>$res['msg']]);
        }
    }

    public function notify(){
        $token='c9258af6ff809ead3a1aa0cf62cdddb4';
        //file_put_contents("/www/wwwroot/Site/Web/99/notify.txt", json_encode($_GET)."\r\n", FILE_APPEND);
        
        $merchantNum = $_GET['merchantNum']; //商户号
        $orderNo = $_GET['orderNo']; //平台返回商户提交的订单号
        $actualFiatAmount = (string)$_GET['actualFiatAmount']; //法币金额
        $state = $_GET['state']; //支付是否成功
        $sign = $state . $merchantNum . $orderNo . $actualFiatAmount . $token;
        if (!$_GET['orderNo'] || md5($sign) != $_GET['sign']) { //不合法的数据
            exit('Illegal data');
        } else { //合法的数据
            //业务处理
            $result = $this->notify_ok_dopay($orderNo, $_GET['merchantAmt']);
            if ($result) {
                exit('success');
            }else{
                exit('The order has been processed');
            }
        }
    }
    
    public function notify_ok_dopay($order_no,$order_amount){
        if(!$order_no || !$order_amount){
            return false;
        }

        $balance = db('balance')->where('balance_sn',$order_no)->where('isverified',0)->find();
        if(!$balance){
            return false;
        }

        if($balance['bptype'] != 3){
            return true;
        }
        $user_money = db('userinfo')->where('uid',$balance['uid'])->value('usermoney');
        $_edit['bpid'] = $balance['bpid'];
        $_edit['bptype'] = 1;
        $_edit['isverified'] = 1;
        $_edit['cltime'] = time();
        $_edit['bpbalance'] = $balance['bpprice']+$user_money;
        
        $is_edit = db('balance')->update($_edit);
        
        if($is_edit){
            // add money
            $_ids=db('userinfo')->where('uid',$balance['uid'])->setInc('usermoney',$balance['bpprice']);
            if($_ids){
                //资金日志
                set_price_log($balance['uid'],1,$balance['bpprice'],'充值','用户充值',$_edit['bpid'],$_edit['bpbalance']);
            }
            return true;
        }else{
            return false;
        }
    }
}