<?php
namespace app\index\controller;
use think\Db;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function open()
    {
        $HTTP_HOST_ARR = explode('.',$_SERVER['HTTP_HOST']);
        $HTTP_HOST = $HTTP_HOST_ARR[0] === 'www' ? $HTTP_HOST_ARR[1] . '.' . $HTTP_HOST_ARR[2] : $_SERVER['HTTP_HOST'];
        if(isMobile()){
            $HOST = (isHttps() ? 'https://' : 'http://').'sj.'.$HTTP_HOST.'/99';
        }else{
            $HOST = (isHttps() ? 'https://' : 'http://').'dn.'.$HTTP_HOST.'/99';
        }
        echo "<script language='javascript' type='text/javascript'>";  
        echo "window.location.href='$HOST'";  
        echo "</script>";
    }
}
