<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
function onHttps() {
    return true;
    if (defined('HTTPS') && HTTPS) return true;
    if (!isset($_SERVER)) return false;
    if (!isset($_SERVER['HTTPS'])) return false;
    if ($_SERVER['HTTPS'] === 1) {  //Apache
        return true;
    } elseif ($_SERVER['HTTPS'] === 'on') { //IIS
        return true;
    } elseif ($_SERVER['SERVER_PORT'] == 443) { //其他
        return true;
    }
    return false;
}
/**
 * 取得根域名
 * @param type $domain 域名
 * @return string 返回根域名
 */
function GetUrlToDomain($domain) {
    $re_domain = '';
    $domain_postfix_cn_array = array("com", "net", "org", "gov", "edu", "com.cn", "cn");
    $array_domain = explode(".", $domain);
    $array_num = count($array_domain) - 1;
    if ($array_domain[$array_num] == 'cn') {
        if (in_array($array_domain[$array_num - 1], $domain_postfix_cn_array)) {
            $re_domain = $array_domain[$array_num - 2] . "." . $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
        } else {
            $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
        }
    } else {
        $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
    }
    return $re_domain;
}
function getBaseDomain($url=''){
    if(!$url){
        return $url;
    }
    #列举域名中固定元素
    $state_domain = array(
    'al','dz','af','ar','ae','aw','om','az','eg','et','ie','ee','ad','ao','ai','ag','at','au','mo','bb','pg','bs','pk','py','ps','bh','pa','br','by','bm','bg','mp','bj','be','is','pr','ba','pl','bo','bz','bw','bt','bf','bi','bv','kp','gq','dk','de','tl','tp','tg','dm','do','ru','ec','er','fr','fo','pf','gf','tf','va','ph','fj','fi','cv','fk','gm','cg','cd','co','cr','gg','gd','gl','ge','cu','gp','gu','gy','kz','ht','kr','nl','an','hm','hn','ki','dj','kg','gn','gw','ca','gh','ga','kh','cz','zw','cm','qa','ky','km','ci','kw','cc','hr','ke','ck','lv','ls','la','lb','lt','lr','ly','li','re','lu','rw','ro','mg','im','mv','mt','mw','my','ml','mk','mh','mq','yt','mu','mr','us','um','as','vi','mn','ms','bd','pe','fm','mm','md','ma','mc','mz','mx','nr','np','ni','ne','ng','nu','no','nf','na','za','aq','gs','eu','pw','pn','pt','jp','se','ch','sv','ws','yu','sl','sn','cy','sc','sa','cx','st','sh','kn','lc','sm','pm','vc','lk','sk','si','sj','sz','sd','sr','sb','so','tj','tw','th','tz','to','tc','tt','tn','tv','tr','tm','tk','wf','vu','gt','ve','bn','ug','ua','uy','uz','es','eh','gr','hk','sg','nc','nz','hu','sy','jm','am','ac','ye','iq','ir','il','it','in','id','uk','vg','io','jo','vn','zm','je','td','gi','cl','cf','cn','yr','com','arpa','edu','gov','int','mil','net','org','biz','info','pro','name','museum','coop','aero','xxx','idv','me','mobi','asia','ax','bl','bq','cat','cw','gb','jobs','mf','rs','su','sx','tel','travel'
    
    );
    if(!preg_match("/^http/is", $url)){
        $url="http://".$url;
    }
    $res = null;
    $res['domain'] = null;
    $res['host'] = null;
    $url_parse = parse_url(strtolower($url));
    $urlarr = explode(".", $url_parse['host']);
    $count = count($urlarr);
    if($count <= 2){
        #当域名直接根形式不存在host部分直接输出
        $res['domain'] = $url_parse['host'];
    }elseif($count > 2){
        $last = array_pop($urlarr);
        $last_1 = array_pop($urlarr);
        $last_2 = array_pop($urlarr);
        $res['domain'] = $last_1.'.'.$last;
        $res['host'] = $last_2;
        if(in_array($last, $state_domain)){
            $res['domain']=$last_1.'.'.$last;
            $res['host']=implode('.', $urlarr);
        }
        if(in_array($last_1, $state_domain)){
            $res['domain'] = $last_2.'.'.$last_1.'.'.$last;
            $res['host'] = implode('.', $urlarr);
        }
        #print_r(get_defined_vars());die;
    }
    return $res;
}
// [ 应用入口文件 ]
header("Content-type: text/html; charset=utf-8"); 

$HTTP_REFERER = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
$DOMAIN = GetUrlToDomain($_SERVER['HTTP_HOST']);
if($_SERVER['HTTP_HOST'] != 'admin.'.$DOMAIN){
    $arr = explode('.',$_SERVER['HTTP_HOST']);
    
    if($HTTP_REFERER == "")
    {
        header("Location:" . (onHttps() ? 'https://' : 'http://') . $DOMAIN);
        exit;
    }else{
        if(count($arr) < 3 || $arr[0] == 'www')
        {
            //指定来源域名
            $referer = getBaseDomain($HTTP_REFERER);
            $inlet = explode("\r\n", trim(@file_get_contents('../inlet.txt'),"\r\n"));
            if(!in_array($referer['domain'],$inlet)){
                header("Location:https://www.baidu.com");
                exit;
            }
        }else{
            $project = explode("\r\n", trim(@file_get_contents('../project.txt'),"\r\n"));
            if(!in_array($DOMAIN,$project)){
                header("Location:https://www.baidu.com");
                exit;
            }
        }
    }

    

    if(count($arr) < 3 || $arr[0] == 'www')
    {
        header("Location:" . (onHttps() ? 'https://' : 'http://') . $DOMAIN);
        exit; 
    }

    if($arr[0] != 'dn' && $arr[0] != 'sj')
    {
        header("Location:" . (onHttps() ? 'https://' : 'http://') . $DOMAIN);
        exit;
    }
}

//开启session
session_start();

// 定义应用目录
//die( __DIR__ . '/application/');
define('APP_PATH', __DIR__ . '/application/');
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';