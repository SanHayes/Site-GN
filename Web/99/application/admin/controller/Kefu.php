<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Kefu extends Base {


	/**
	 * 客服设置
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */
	public function set()
	{
		if(input('post.')){
			$data = input('post.');
            foreach ($data as $k => $v) {
                $arr = explode('_',$k);
                $_data['id'] = $arr[1];
                $_data['value'] = $v;
                $file = request()->file('pic_'.$_data['id']);
                
                if($file){
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'jpg', 'kefu_img.png');
                    if($info){
                        $_data['value'] = DS . 'public' . DS . 'jpg' . DS . 'kefu_img.png';
                    }
                }
                if($_data['value'] == '' && isset($arr[2]) && $arr[2] == 3){
                    continue;
                }                
                Db::name('config')->update($_data);
            }
            cache('conf',null);
            $this->success('编辑成功');
		}else{
			$map['group'] = 3;
			$map['status'] = 1;
			$data = Db::name('config')->where($map)->order('sort,id')->select();
			$this->assign('data',$data);
			return $this->fetch();
		}
	}

	/**
	 * 添加产品
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */
	public function wadd()
	{
		if(input('post.')){
			$data = input('post.');
			if($data['id']){ //编辑
				$res = Db::name('words')->update($data);
                if($res){
                    return WPreturn('修改成功',1);
                }else{
                    return WPreturn('修改失败',-1);
                }
			}else{  //新添加
				$id = Db::name('words')->insertGetId($data);
                if($id){
                    return WPreturn('添加成功',1);
                }else{
                    return WPreturn('添加失败',-1);
                }
			}

		}else{
            $id = input('param.id', 0, 'intval');
			if($id){  //编辑产品
                $item = Db::name('words')->where('id',$id)->find();
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
	public function words()
	{
		$pagenum = cache('page');
        $list = Db::name('words')->order('id desc')->paginate($pagenum,false,[]);
        $this->assign('list', $list);
        return $this->fetch();
	}

	/**
	 * 删除产品
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */
	public function wdel()
	{
		$id = input('id',0,'intval');
    	if(!$id){
    		return WPreturn('参数错误',-1);
    	}

    	$res = Db::name('words')->where('id', $id)->delete();
    	if($res){
    		return WPreturn('删除成功',1);
    	}else{
    		return WPreturn('删除失败',-1);
    	}
	}

	// 客服工作台
	public function index() {
		$host = str_replace(['gm518btc','gm518btb','gm296btc','gma6btc','gmk8btc'],'kefu',$_SERVER['HTTP_HOST']);
		$code = encrypt('70f3gBFen3fT67hwVaz8d6c', 'E', $_SERVER['SERVER_ADDR'].$host);
		$url = $host.'/kefu/php/app.php?login&btcode='.$code;
		header('Location:http://'.$url);//使HTTP返回404状态码 
		exit;
	}

}
