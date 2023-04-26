<?php
namespace app\home\controller;
use think\Controller;
use think\Db;

class confirm extends Controller{
    /**
     * 确认支付
     */
    public function order(){
        $where1['money'] = input('money');
        $opid = input('opid'); //1熊，2周，3董，4李
        $endTime = time();
        $startTime = $endTime-(15*60);
        $where1['ctime'] = array(array('EGT',$startTime),array('ELT',$endTime), 'and');
        $where1['pay_type'] = 1;

        $order = Db::name('payorder')->where($where1)->find();
        $order || p('订单超时或已被操作，或金额错误');
//        if($order['pay_type'] == 2) p('订单已被操作');

        //查询庄家余额是否足够扣除手续费
        $data['money'] = $order['cost']; //2%手续费
        $url=URL."index.php?s=/Home/Bcuser/deduction";
        $biao=apipost($url,$data);
        $res=json_decode($biao,true);
        switch ($res['code']) {
            case 1001:p('庄家用户余额不足');
                break;
            case 1002:p('扣除余额失败');
                break;
        }
        
//        修改自定义订单
        $ud = Db::name('payorder')->where('id', $order['id'])->update(['pay_type' => '2','opid' => $opid]);
        //修改系统订单
        $where2['bpprice'] = $where1['money'];
        $where2['bptime'] = array(array('EGT',$startTime),array('ELT',$endTime), 'and');
        $balance = Db::name('balance')->where($where2)->update(['bptype' => 1,'isverified' =>1,'cltime'=>$endTime]);
        //修改用户金额
        $money = round($where1['money']);
        $res = Db::name('userinfo')->where('uid', $order['uid'])->setInc('usermoney', $money);
        p($res);
    }
    
    /**
     * 订单列表
     */
    public function orderlist(){
        $page = input('p', 1);
        $limit = input('limit', 20);  //分页
        
        $pay_type = input('pay_type');
//        $uname = I('uname');
//        $project_name = I('project_name');
//        $time_start = I('time_start');
//        $time_end = I('time_end');
//        if($uid!=1){
//            $where['uid'] = $uid;
//            $map['uid'] = $uid;
//        }
//        if($uname){
//            $where['uname'] = $uname;
//            $map['uname'] = $uname;
//        }
//        if($project_name){
//            $where['project_name'] = $project_name;
//            $map['project_name'] = $project_name;
//        }
//        if($time_start && $time_end){
//            $newtime_start = str_replace('-', '', $time_start);
//            $newtime_end = str_replace('-', '', $time_end);
//            $where['completion_date'] = array('BETWEEN',array($newtime_start,$newtime_end));
//            $map['time_start'] = $time_start;
//            $map['time_end'] = $time_end;
//        }
        if($pay_type){
            $where['pay_type'] = $pay_type;
        }else{
            $where = '';
        }
        
        //查询客户列表
        $pay = Db::name('payorder');
        $countNum = $pay->where($where)->count();
//        print_r($countNum);
        $res = $pay->where($where)->page($page, $limit)->order('id desc')->select();
//        $workh = $pay->where($where)->sum('efficiency');
//        p($res);

       $arrRtn = array('countNum'=>$countNum,'res'=>$res);

       return json($arrRtn);

        
//        $Page = new \Think\Page($countNum, $limit); // 实例化分页类 传入总记录数和每页显示的记录数(25)
//       
//        // 获取查询参数
//        foreach($map as $key =>$val){
//            $Page->parameter[$key]=$val;
//        }
//        // 分页显示输出
//        $show = $Page->show(); 
//
//        $this->assign("uid", $uid);
//        $this->assign("workh", $workh);
//        $this->assign("tasklist", $res);
//        $this->assign('_page', $show); // 赋值分页输出
//	$project = D('Project')->select();
//        $this->assign('project',$project);
//
//
//
//        $this->display();
    }
    
    public function aa(){
        
        
//        $num = -0.05 + mt_rand() / mt_getrandmax() * (0.05 - (-0.05));  
        $a = sprintf("%.2f", -0.05 + mt_rand() / mt_getrandmax() * (0.05 - (-0.05)));  
        $b = 1000+$a;
        if($b==1000) $b = "1000.00";
        p($b);
        $_rand = rand(-0.5,0.5);
        p($_rand);
    }
}