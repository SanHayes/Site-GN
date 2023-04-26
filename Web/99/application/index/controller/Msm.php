<?php
namespace app\index\controller;
use think\Config;
use think\Controller;
use think\Db;

class Msm extends Controller
{
    function sendMsm()
    {
        $utel = str_replace("+","",urlencode(input('utel')));
        if(!$utel){
            return json(['code'=>-103,'data'=>[],'msg'=>'请输入手机号']);
        }else{
            if(check_user('username',$utel)){
                return json(['code'=>-50,'data'=>[],'msg'=>'该用户名已存在']);
            }
        }
        $code = mt_rand(1000,9999);
        $content = '【GffEx】Your authentication code is'.$code.', valid for minutes at 10';
        
        
        $user_code = Db::name('usercode')->where(['number'=>$utel])->find();
        if($user_code){
            if(time() - $user_code['time']  < 60){
                return json(['code'=>-68,'data'=>null,'msg'=>'已发送验证码，请60秒后重试']);
            }
        }
        
        $statusStr = array(
    		"0" => "SMS sent successfully",
    		"-1" => "Incomplete parameters",
    		"-2" => "The server space is not supported. Please confirm that curl or fsocket is supported, contact your space provider to resolve or replace the space!",
    		"30" => "Password error",
    		"40" => "Account does not exist",
    		"41" => "Sorry, your credit is running low",
    		"42" => "Account has expired",
    		"43" => "IP Address Restrictions",
    		"50" => "Content contains sensitive words",
    		"51" => "Incorrect phone number"
	    );
    	$sendurl = "https://api.smsbao.com/wsms?u=hantec&p=66538fa088d845868c1b1c4aedd8ed6a&m=".urlencode("+".$utel)."&c=".urlencode($content);
    	$result =file_get_contents($sendurl);
        if($result == "0"){
            $data['number'] = $utel;
            $data['code'] = $code;
            $data['type'] = 'tel';
            $data['time'] = time();
            if(!$user_code){
                if(Db::name('usercode')->insertGetId($data)){
                    return json(['code'=>200,'data'=>$utel,'msg'=>'SMS sent successfully']);
                }else{
                    return json(['code'=>-62,'data'=>null,'msg'=>'SMS sending failed']);
                }
            }else{
                if(Db::name('usercode')->where(['number'=>$utel])->update($data)){
                    return json(['code'=>200,'data'=>$utel,'msg'=>'SMS sent successfully']);
                }else{
                    return json(['code'=>-62,'data'=>null,'msg'=>'SMS sending failed']);
                }
            }
        }else{
            return json(['code'=>-62,'data'=>$result,'msg'=>$statusStr[$result]]);
        }
    }
}
<?php
namespace app\api\controller;
use think\Config;
use think\Controller;
use think\Db;

class Msm extends Controller
{
    function sendMsm()
    {
        $utel = str_replace("+","",urlencode(input('utel')));
        if(!$utel){
            return json(['code'=>-103,'data'=>[],'msg'=>'请输入手机号']);
        }else{
            if(check_user('username',$utel)){
                return json(['code'=>-50,'data'=>[],'msg'=>'该用户名已存在']);
            }
        }
        $code = mt_rand(1000,9999);
        $content = '【GffEx】Your authentication code is'.$code.', valid for minutes at 10';
        
        
        $user_code = Db::name('usercode')->where(['number'=>$utel])->find();
        if($user_code){
            if(time() - $user_code['time']  < 60){
                return json(['code'=>-68,'data'=>null,'msg'=>'已发送验证码，请60秒后重试']);
            }
        }
        
        $statusStr = array(
    		"0" => "SMS sent successfully",
    		"-1" => "Incomplete parameters",
    		"-2" => "The server space is not supported. Please confirm that curl or fsocket is supported, contact your space provider to resolve or replace the space!",
    		"30" => "Password error",
    		"40" => "Account does not exist",
    		"41" => "Sorry, your credit is running low",
    		"42" => "Account has expired",
    		"43" => "IP Address Restrictions",
    		"50" => "Content contains sensitive words",
    		"51" => "Incorrect phone number"
	    );
    	$sendurl = "https://api.smsbao.com/wsms?u=hantec&p=66538fa088d845868c1b1c4aedd8ed6a&m=".urlencode("+".$utel)."&c=".urlencode($content);
    	$result =file_get_contents($sendurl);
        if($result == "0"){
            $data['number'] = $utel;
            $data['code'] = $code;
            $data['type'] = 'tel';
            $data['time'] = time();
            if(!$user_code){
                if(Db::name('usercode')->insertGetId($data)){
                    return json(['code'=>200,'data'=>$utel,'msg'=>'SMS sent successfully']);
                }else{
                    return json(['code'=>-62,'data'=>null,'msg'=>'SMS sending failed']);
                }
            }else{
                if(Db::name('usercode')->where(['number'=>$utel])->update($data)){
                    return json(['code'=>200,'data'=>$utel,'msg'=>'SMS sent successfully']);
                }else{
                    return json(['code'=>-62,'data'=>null,'msg'=>'SMS sending failed']);
                }
            }
        }else{
            return json(['code'=>-62,'data'=>$result,'msg'=>$statusStr[$result]]);
        }
    }
}
