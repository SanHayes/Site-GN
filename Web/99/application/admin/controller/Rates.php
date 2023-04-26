<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Rates extends Base
{


	public function __construct(){
		parent::__construct();
		if($this->otype != 3){
			echo '死你全家!';exit;
		}
	}

	/**
	 * 产品列表
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */
	public function invest()
	{
        $list =  Db::name('invest')->select();
        $this->assign('list',$list);
        return $this->fetch();
	}

	/**
	 * 添加产品
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */
	public function add()
	{
		if(input('post.')){
			$data = input('post.');

			if($data['pid']){ //编辑
				$res = Db::name('invest')->update($data);
                if($res){
                    return WPreturn('修改成功',1);
                }else{
                    return WPreturn('修改失败',-1);
                }
			}else{  //新添加
				$pid = Db::name('invest')->insertGetId($data);
                if($pid){
                    return WPreturn('添加成功',1);
                }else{
                    return WPreturn('添加失败',-1);
                }
			}

		}else{
            $pid = input('param.pid', 0, 'intval');
			if($pid){  //编辑产品
                $item = Db::name('invest')->where('pid',$pid)->find();
            }else{
				$item = ['state'=>1];
			}
            $this->assign('item', $item);
			return $this->fetch();
		}
	}

	/**
	 * 删除产品
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */
	public function delete()
	{
		$pid = input('pid',0,'intval');
    	if(!$pid){
    		return WPreturn('参数错误',-1);
    	}

    	$res = Db::name('invest')->where('pid', $pid)->delete();
    	if($res){
    		return WPreturn('删除成功',1);
    	}else{
    		return WPreturn('删除失败',-1);
    	}
	}

	/**
	 * 客户投资
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */
	public function userinvest()
    {
        $pagenum = cache('page');
        $data = input('param.');

        //用户名称、id、手机、昵称
        if($data['username']){
            $where['username'] = $data['username'];
			$getdata['username'] = $data['username'];
        }
        $list = Db::name('userinvest')->where($where)->order('id desc')->paginate($pagenum,false,['query'=> $getdata]);

        $this->assign('list', $list);
        $this->assign('getdata', $getdata);
        return $this->fetch();
    }

	/**
	 * 投资回款
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */
	public function interest() {
		$id = input('id',0,'intval');
		$userinvest = Db::name('userinvest')->field('uid,username,money,interest,state')->where(['id'=>$id])->find();
		if(!$userinvest){
			return WPreturn('操作失败！',1);
		}
		if($userinvest['state'] != 1){
			return WPreturn('此订单已操作',-1);
		}

		//获取个人信息
		$userinfo = Db::name('userinfo')->field('uid,username,usermoney')->where('uid',$userinvest['uid'])->find();
		if(empty($userinfo)){
			return WPreturn('回款失败，缺少参数!',-1);
		}

		$ret = Db::name('userinvest')->where(['id'=>$id,'state'=>1])->update(['state'=>2,'totime'=>time()]);
		if($ret){
			db('userinfo')->where('uid',$userinvest['uid'])->setInc('usermoney',$userinvest['money']+$userinvest['interest']);
			//资金日志
			set_price_log($userinvest['uid'],1,$userinvest['money'],'利息宝','会员投资本金',$id,$userinfo['usermoney']);
			set_price_log($userinvest['uid'],1,$userinvest['interest'],'利息宝','会员投资利息',$id,$userinfo['usermoney']+$userinvest['money']);
			return WPreturn('操作成功！',1);
		}else{
			return WPreturn('操作失败！',-1);
		}
	}


}
