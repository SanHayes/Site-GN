<?php
namespace Org;

/**
 * 微信授权相关接口
 *
 * @link http://www.gouxiu,me
 */
class Wechat{
    
    //高级功能-》开发者模式-》获取
    private $app_id = '';
    private $app_secret = '';
    private $redirect_uri = '';
    
    public function setconf($appid,$appsecret,$redirecturi)
    {
        $this->app_id = $appid;
        $this->app_secret = $appsecret;
        $this->redirect_uri = $redirecturi;
    }
    /**
     * 获取微信授权链接
     *
     * @param string $redirect_uri 跳转地址
     * @param mixed $state 参数
     */
    public function get_authorize_url($state)
    {
        $redirect_uri = urlencode($this->redirect_uri);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->app_id}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect";
    }
    /**
     * 获取授权token
     *
     * @param string $code 通过get_authorize_url获取到的code
     */
    public function get_access_token($code)
    {
        $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->app_id}&secret={$this->app_secret}&code={$code}&grant_type=authorization_code";
        
        $token_data = $this->get_request($token_url);
        $jsoninfo = json_decode($token_data, true);
        return($jsoninfo);
    }

    /**
     * 获取授权后的微信用户信息
     *
     * @param string $access_token
     * @param string $open_id
     */
    public function get_user_info($access_token,$open_id)
    {
        if($access_token && $open_id)
        {
            $info_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$open_id}&lang=zh_CN";
            $info_data = $this->get_request($info_url);

            $jsoninfo = json_decode($info_data, true);
            return($jsoninfo);
        }

        return FALSE;
    }

    public function get_request($url){
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        $output =curl_exec($ch);  
        curl_close($ch); 
        return $output;
    }

}
