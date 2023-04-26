<?php
namespace app\apk\controller;
use think\Db;


class Payment extends Base{
    
    public function index(){
        $uid = $this->uid;
        
        $date = date("YmdHi",time());
        $orderid = $date.rand(100000,999999);
        
        $post["uid"] = $uid;
//        $post["price"] = I("get.money",1);
        $post["money"] = $_REQUEST["money"];
        $post["istype"] = $_REQUEST["istype"];  //10001表示支付宝，20001表示微信
        //$post["notify_url"] = $_REQUEST["notify_url"];
        //$post["return_url"] = $_REQUEST["return_url"];
        //$post["orderid"] = $_REQUEST["orderid"];
        //$post["orderuid"] = $_REQUEST["orderuid"];
//        $post["notify_url"] = "http://127.0.0.1/phpzy/callback.php";
//        $post["return_url"] = "http://127.0.0.1/phpzy/ok.php?type=test";
        $post["orderid"] = $orderid;
//        $post["orderuid"] = $orderid;
        $post["goodsname"] = $_REQUEST["goodsname"];
        $post["pay_type"] = 1; //支付状态：1表示未支付，2表示已经支付
        $post["ctime"] = time();
        
        $post["uuid"] = 2; //渠道商id
        
//        p($post);
        $post["cost"] = round($post["money"])*0.02; //2%手续费
        Db::name('payorder')->insert($post);
       
        if(!strpos($post["money"],'.')){
            $post["money"] = $post["money"].".00";
        }
        
       $this->assign('post',$post);
       $this->assign('info',1);
        return $this->fetch();
    }

}

