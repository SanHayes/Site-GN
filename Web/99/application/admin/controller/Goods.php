<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Goods extends Base
{

	/**
	 * 产品列表
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */
	public function prolist()
	{

		if($this->otype != 3){
			echo '死你全家!';exit;
		}
        $pro =  Db::name('productdata')->where('isdelete',0)->order('sort')->select();
        foreach ($pro as $k=>$val) {
            $info = Db::name('productinfo')->where('pid',$val['pid'])->find();
            $pro[$k]['proorder'] = $val['sort'];
            $pro[$k]['ptitle'] =$info['ptitle'];
            $pro[$k]['pcname'] ="外汇";
            $pro[$k]['rands'] =$info['rands'];
            $pro[$k]['point_low'] =$info['point_low'];
            $pro[$k]['point_top'] =$info['point_top'];
            $pro[$k]['isopen'] =$val['is_deal'];
          }
        $this->assign('proinfo',$pro);
        return $this->fetch();
	}
	/**
	 * 添加产品
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */
	public function proadd()
	{		
		if($this->otype != 3){
			echo '死你全家!';exit;
		}
		if(input('post.')){
		    
			$data = input('post.');
			if(count(explode(',',$data['protime'])) > 3){
			    $this->error('玩法时间错误',url('/admin/goods/proadd',array('pid'=>$data['pid'])),1,1);
			}
			$file = request()->file('pic_'.$data['pid']);
			if($file){
			    $info = $file->getInfo();
    			$suffix = 'jpg';
    			$name = 'pic_'.$data['pid'];
    			$path = 'public' . DS . 'uploads' . DS . date('Ymd');
    			if($info['type']){
    			    $suffix = explode('/',$info['type'])[1];
    			}
                if (!function_exists($path)) {
                    mkdirs($path);
                }
                if($file){
                    $info = $file->move(UPLOADS_PATH . $path, $name.'.'.$suffix);
                    if($info){
                        $data['img'] = DS . $path . DS . $name.'.'.$suffix;
                    }
                }
                if($data['img'] == ''){
                    $this->error('参数错误',url('/admin/goods/proadd',array('pid'=>$data['pid'])),1,1);
                }
			}
    
            //修改开市时间
            $opentime_arr = $data['opentime'];
            $opentime_str = '';
            foreach ($opentime_arr as $k => $v) {
                $opentime_str .= trim($v).'-';
            }
            $opentime['opentime'] = $opentime_str;
            $_otime = db('opentime')->where('pid',$data['pid'])->find();
            if($_otime){
                db('opentime')->where('pid',$data['pid'])->update($opentime);
            }else{
                $opentime['pid'] = $data['pid'];
                db('opentime')->insert($opentime);
            }

            unset($data['opentime']);

            
			if(!$data['ptitle'] || !$data['cid'] ){
				$this->error('参数错误',url('/admin/goods/proadd',array('pid'=>$data['pid'])),1,1);
			}
			$data['time'] = time();
			$data['isdelete'] = 0;
			if(isset($data['pid']) && !empty($data['pid']) && $data['pid'] != 0){ //编辑
				$editid = Db::name('productinfo')->update($data);
                if($editid){
                    db('productdata')->where('pid',$data['pid'])->update(['Name'=>$data['ptitle']]);
                    $this->success('修改成功',url('/admin/goods/proadd',array('pid'=>$data['pid'])),1,1);
                }else{
                    $this->error('修改失败',url('/admin/goods/proadd',array('pid'=>$data['pid'])),1,1);
                }
			}else{  //新添加
				$addid = Db::name('productinfo')->insertGetId($data);
                if($addid){
                    $p_data['pid'] = $addid;
                    $p_data['Name'] = $data['ptitle'];
                    Db::name('productdata')->insert($p_data);
                    $this->success('添加成功',$_SERVER['HTTP_REFERER'],1,1);
                }else{
                    $this->error('添加失败',$_SERVER['HTTP_REFERER'],1,1);
                }
			}
		}else{
			if(input('param.pid')){  //编辑产品

                $pid = input('param.pid');
                $productinfo = Db::name('productinfo')->where('pid',$pid)->find();
                $this->assign($productinfo);

                $opentime = db('opentime')->where('pid',$pid)->find();
                if($opentime){
                    $otime_arr = explode('-',$opentime["opentime"]);
                }else{
                    $otime_arr = array('','','','','','','');
                }
                
                $this->assign('opentime',$otime_arr);
            }
			//分类
			$proclass = Db::name('productclass')->where('isdelete',0)->order('pcid desc')->select();
            $this->assign('proclass',$proclass);
			return $this->fetch();
		}
	}

	/**
	 * 产品开、休市
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */
	public function proisopen()
	{
        $r = Db::name('productdata')->where(['pid'=>input('pid')])->update(['is_deal'=>input('isopen')]);
        echo "<script>self.location='".$_SERVER['HTTP_REFERER'] .url('goods/prolist')."'</script>";
//		if($this->otype != 3){
//			echo '死你全家!';exit;
//		}
//		if (input('post.')) {
//			$data = input('post.');
//			$editid = Db::name('productdata')->update($data);
//
//            if($editid){
//                return WPreturn('修改成功',1);
//            }else{
//                return WPreturn('修改失败',-1);
//            }
//		}else{
//			return WPreturn('参数错误',-1);
//		}
	}

	/**
	 * 删除产品
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */
	public function delpro()
	{
		if($this->otype != 3){
			echo '死你全家!';exit;
		}
		$id = input('get.id',0);
    	if(!$id){
    		return WPreturn('参数错误',-1);
    	}

    	$delpro = Db::name('productinfo')->where('pid', $id)->update(['isdelete' => 1]);
    	if($delpro){
            $p_data['isdelete'] = 1;
            Db::name('productdata')->where('pid', $id)->update($p_data);
    		return WPreturn('删除成功',1);
    	}else{
    		return WPreturn('删除失败',-1);
    	}
	}

    /**
     * 还原产品
     * @author lukui  2017-02-15
     * @return [type] [description]
     */
    public function hypro()
    {
		if($this->otype != 3){
			echo '死你全家!';exit;
		}
        $id = input('get.id',0);
        if(!$id){
            return WPreturn('参数错误',-1);
        }

        $delpro = Db::name('productinfo')->where('pid', $id)->update(['isdelete' => 0]);
        if($delpro){
            $p_data['isdelete'] = 0;
            Db::name('productdata')->where('pid', $id)->update($p_data);
            return WPreturn('还原成功',1);
        }else{
            return WPreturn('还原失败',-1);
        }
    }




	/**
	 * 产品分类
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */
	public function proclass()
	{
		if($this->otype != 3){
			echo '死你全家!';exit;
		}
		$productclass = Db::name('productclass')->where('isdelete',0)->order('pcid desc')->select();
    	$this->assign('productclass',$productclass);
		return $this->fetch();
	}

	/**
	 * 编辑、添加产品分类
	 * @author lukui  2017-02-15
	 * @return [type] [description]
	 */
	public function editclass()
	{
		
		echo '死你全家!';exit;
		if($this->otype != 3){
			echo '死你全家!';exit;
		}
		$data['pcid'] = input('post.pcid',0);
        $data['pcname'] = input('post.pcname',0);
        $data['isdelete'] = 0;
        if(!$data['pcname']){
            return WPreturn('参数错误',-1);
        }

        if($data['pcid']){ //有id 编辑信息
            $editnews = Db::name('productclass')->where('pcid', $data['pcid'])->update(array('pcname' => $data['pcname']));
            if($editnews){
                return WPreturn('修改成功',1);
            }else{
                return WPreturn('修改失败',-1);
            }
        }else{ //没di 增加一条
            $addid = Db::name('productclass')->insert($data);
            if($addid){
                return WPreturn('添加成功',1);
            }else{
                return WPreturn('添加失败',-1);
            }
        }
	}

	/**
     * 删除分类
     * @author lukui  2017-02-15
     * @return [type] [description]
     */
    public function deleteclass()
    {
		
		if($this->otype != 3){
			echo '死你全家!';exit;
		}
    	$id = input('get.id',0);
    	if(!$id){
    		return WPreturn('参数错误',-1);
    	}

    	$delpro = Db::name('productclass')->where('pcid', $id)->update(['isdelete' => 1]);
    	if($delpro){
    		return WPreturn('删除成功',1);
    	}else{
    		return WPreturn('删除失败',-1);
    	}
    	
    }

    /**
     * 风控管理
     * @author lukui  2017-02-15
     * @return [type] [description]
     */
    public function risk()
    {
    	if($this->otype != 3){
			echo '死你全家!';exit;
		}
		
    	$risk = Db::name('risk')->find();
    	$this->assign($risk);
    	return $this->fetch();
    }


    public function addrisk()
    {
        if($this->otype != 3){
			echo '死你全家!';exit;
		}
		
        $post = input('post.');
        
        
        if (!$post) {
            $this->error('禁止访问');
        }

        if(empty($post['id'])){
            unset($post['id']);
            $ids = db('risk')->insert($post);
        }else{
            $ids = db('risk')->update($post);
        }

        if($ids){
            return WPreturn('操作成功',1);
        }else{
            return WPreturn('操作失败',-1);
        }

    }


    /**
     * 排序
     * @author lukui  2017-08-27
     * @return [type] [description]
     */
    public function proorder()
    {
        if($this->otype != 3){
			echo '死你全家!';exit;
		}
		
        $post = input('post.');
        if(!isset($post["proorder"])){
            $this->error('参数错误！');
        }
        $proorder = $post["proorder"];
        $productdata = db('productdata');
        foreach ($proorder as $k => $v) {
            $productdata->where('pid',$k)->update(['sort'=>intval($v)]);
            
        }

        $this->success('操作成功！');
        
    }

    public function huishou()
    {
        if($this->otype != 3){
			echo '死你全家!';exit;
		}
		

        $proinfo = Db::name('productinfo')->alias('pi')->field('pi.*,pc.pcname')
                    ->join('__PRODUCTCLASS__ pc','pc.pcid = pi.cid')
                    ->where('pi.isdelete',1)->order('pi.pid asc')->select();

        $this->assign('proinfo',$proinfo);
        return $this->fetch();
    }





}
