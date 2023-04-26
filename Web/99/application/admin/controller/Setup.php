<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
/**
 * 系统设置和积分比例设置，可自定义设置
 */

class Setup extends Base
{

    /**
     * 基本设置
     * @author lukui  2017-04-19
     * @return [type] [description]
     */
    public function index()
    {
		if($this->otype != 3){
			echo '死你全家!';exit;
		}
		
        $map['group'] = 1;
        $map['status'] = 1;

        $data = Db::name('config')->where($map)->order('sort asc')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }


    /**
     * 比例设置
     * @author lukui  2017-04-19
     * @return [type] [description]
     */
    public function proportion()
    {
        if($this->otype != 3){
			echo '死你全家!';exit;
		}
		
        $map['group'] = 2;
        $map['status'] = 1;

        $data = Db::name('config')->where($map)->order('sort asc')->select();
        $this->assign('data',$data);
        return $this->fetch('index');
    }


    /**
     * 配置比例
     * @author lukui  2017-04-19
     * @return [type] [description]
     */
    public function addsetup()
    {
        if($this->otype != 3){
			echo '死你全家!';exit;
		}		
        if(input('post.')){
            $data = input('post.');
            $data['create_time'] = $data['update_time'] = time();
            $data['status'] = 1;
            if(isset($data['id'])){
                $ids = Db::name('config')->update($data);
            }else{
                $ids = Db::name('config')->insert($data);
            }            
            if($ids){
                cache('conf',null);
                return WPreturn('配置成功',1);
            }else{
                return WPreturn('配置失败，请重试',1);
            }
            exit;
        }else{
            if(input('param.id')){
                $id = input('param.id');
                $data = Db::name('config')->where('id',$id)->find();
                $this->assign($data);
            }
            return $this->fetch();
        }
    }


    /**
     * 编辑配置/比例
     * @author lukui  2017-04-19
     * @return [type] [description]
     */
    public function editconf()
    {
		
		//echo '死你全家!';exit;
		
		if($this->otype != 3){
			echo '死你全家!';exit;
		}
        if(input('post.')){

            $data = input('post.');
            $app = request()->file();
            foreach ($data as $k => $v) {
                $arr = explode('_',$k);
                $_data['id'] = $arr[1];
                $_data['value'] = $v;
                $img = request()->file('img_'.$_data['id']);
                $app = request()->file('app_'.$_data['id']);
                if($_data['id'] == 1){
                    file_put_contents(UPLOADS_PATH . '99/app/title.txt', $v);
                }
                if($app){
                    $info = $app->getInfo();
                    $suffix = explode('.',$info['name'])[1];
        			$name = 'EYuYidqBxn';
        			$path = '99/app';
                    $res = $app->move(UPLOADS_PATH . $path, $name.'.'.$suffix);
                    if($res){
                        $_data['value'] = DS . $path . DS . $name.'.'.$suffix;
                    }
                }
                if($img){
                    $info = $img->getInfo();
                    $suffix = 'jpg';
        			$name = date('YmdHis').mt_rand(100,999);
        			$path = 'public' . DS . 'uploads' . DS . date('Ymd');
        			if($info['type']){
        			    $suffix = explode('/',$info['type'])[1];
        			}
                    if (!function_exists($path)) {
                        mkdirs($path);
                    }
                    $info = $img->move(UPLOADS_PATH . $path, $name.'.'.$suffix);
                    if($info){
                        copy(UPLOADS_PATH . $path . '/' . $name.'.'.$suffix,UPLOADS_PATH . '99/app/img/logo.png');
                        $_data['value'] = DS . $path . DS . $name.'.'.$suffix;
                    }
                }
                if($_data['value'] == '' && isset($arr[2]) && in_array($arr[2],[3,4,5])){
                    continue;
                }
                
                Db::name('config')->update($_data);

            }
            cache('conf',null);
            $this->success('编辑成功',$_SERVER['HTTP_REFERER'],'',1);
        }

        
    }



    public function deleteconf()
    {
        
		if($this->otype != 3){
			echo '死你全家!';exit;
		}
		
        if(input('post.')){

            $id = input('post.id');
            
            if(!$id){
                $this->error('参数错误',$_SERVER['HTTP_REFERER'],'',1);
            }

            $_data['id'] = $id;
            $_data['status'] = 0;

            $ids = Db::name('config')->update($_data);
            if($ids){
                cache('conf',null);
                $this->success('删除成功',$_SERVER['HTTP_REFERER'],'',1);
            }else{
                $this->error('删除失败，请重试',$_SERVER['HTTP_REFERER'],'',1);
            }
            
        }
    }


    /**
     * 所有配置列表
     * @author lukui  2017-04-19
     * @return [type] [description]
     */
    public function deploy()
    {
		
		if($this->otype != 3){
			echo '死你全家!';exit;
		}

        $map['status'] = 1;

        $data = Db::name('config')->where($map)->order('sort asc')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }
    


    /**
     * 公告
     * @author lukui  2017-04-19
     * @return [type] [description]
     */
    public function notice()
    {
        $data = Db::name('notice')->order('id asc')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * 添加/修改公告
     * @author lukui  2017-04-19
     * @return [type] [description]
     */
    public function addnotice()
    {
        $id = input('id', 0, 'intval');
        $data = input('post.');
        if($data){
            if($id){
                $flag = db('notice')->update($data);
            }else{
                $data['time'] = time();
                $flag = db('notice')->insert($data);
            }
            if($flag){
                $msg = $id ? '公告修改' : '公告添加';
                return WPreturn($msg.'成功',1);
            }else{
                $msg = $id ? '公告修改' : '公告添加';
                return WPreturn($msg.'失败，请重试',1);
            }
        }else{
            if($id){
                $data = db('notice')->where('id',$id)->find();
            }else{
                $data['state'] = 1;
            }
            $this->assign('item', $data);
            return $this->fetch();
        }        
    }

    /**
     * 删除公告
     */
    public function delnotice()
    {        
        $id = input('post.id');
        if(!$id){
            return WPreturn('参数错误！',-1);
        }
        $flag = db('notice')->where('id',$id)->delete();
        if($flag){
            $this->success('删除成功',$_SERVER['HTTP_REFERER'],'',1);
        }else{
            $this->error('删除失败',$_SERVER['HTTP_REFERER'],'',1);
        }
    }

    // 邀请奖励
    public function reward() {
        $data = input('post.');
        if($data){
            $flag = db('reward')->update($data);
            if($flag){
                return WPreturn('邀请奖励修改成功', 1);
            }else{
                return WPreturn('邀请奖励修改失败，请重试',-1);
            }
        }else{
            $item = db('reward')->find();
            $this->assign('item', $item);
            return $this->fetch();
        }
    }
    


    /**
     * 轮播图
     * @author lukui  2017-04-19
     * @return [type] [description]
     */
    public function gallery()
    {
        $data = Db::name('gallery')->order('sort asc, id asc')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * 添加/修改轮播图
     * @author lukui  2017-04-19
     * @return [type] [description]
     */
    public function addgallery()
    {
        $id = input('id', 0, 'intval');
        $data = input('post.');
        if($data){
			$path = '/public/uploads/';
            $file = request()->file('img');
			if($id){
				if(!isset($file)){
                    $this->error('请上传图片',$_SERVER['HTTP_REFERER'],'',1);
				}
				$res = $file->move( UPLOADS_PATH . 'public' . DS . 'uploads');
				if($res){
					$data['img'] = $path.$res->getSaveName();
				}else{
					$this->error($file->getError(),$_SERVER['HTTP_REFERER'],'',1);
				}
			}
            if($id){
                $flag = db('gallery')->update($data);
            }else{
                $flag = db('gallery')->insert($data);
            }
            if($flag){
                $msg = $id ? '轮播图修改' : '轮播图添加';
                $this->success($msg.'成功',$_SERVER['HTTP_REFERER'],'',1);
            }else{
                $msg = $id ? '轮播图修改' : '轮播图添加';
                $this->error($msg.'失败，请重试',$_SERVER['HTTP_REFERER'],'',1);
            }
        }else{
            if($id){
                $data = db('gallery')->where('id',$id)->find();
            }else{
                $data['state'] = 1;
            }
            $this->assign('item', $data);
            return $this->fetch();
        }        
    }

    /**
     * 删除轮播图
     */
    public function delgallery()
    {        
        $id = input('post.id');
        if(!$id){
            return WPreturn('参数错误！',-1);
        }
        $flag = db('gallery')->where('id',$id)->delete();
        if($flag){
            $this->success('删除成功',$_SERVER['HTTP_REFERER'],'',1);
        }else{
            $this->error('删除失败',$_SERVER['HTTP_REFERER'],'',1);
        }
    }

}
