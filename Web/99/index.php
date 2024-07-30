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
function Jump_301($url){
    header("HTTP/1.1 301 Moved Permanently");
    header("Location:" . $url);
}
// [ 应用入口文件 ]
header("Content-type: text/html; charset=utf-8"); 

$useragent = addslashes(strtolower($_SERVER['HTTP_USER_AGENT']));
 
if (strpos($useragent, 'googlebot')!== false){$bot = 'Google';}
elseif (strpos($useragent,'mediapartners-google') !== false){$bot = 'Google Adsense';}
elseif (strpos($useragent,'baiduspider') !== false){$bot = 'Baidu';}
elseif (strpos($useragent,'sogou spider') !== false){$bot = 'Sogou';}
elseif (strpos($useragent,'sogou web') !== false){$bot = 'Sogou web';}
elseif (strpos($useragent,'sosospider') !== false){$bot = 'SOSO';}
elseif (strpos($useragent,'360spider') !== false){$bot = '360Spider';}
elseif (strpos($useragent,'yahoo') !== false){$bot = 'Yahoo';}
elseif (strpos($useragent,'msn') !== false){$bot = 'MSN';}
elseif (strpos($useragent,'msnbot') !== false){$bot = 'msnbot';}
elseif (strpos($useragent,'sohu') !== false){$bot = 'Sohu';}
elseif (strpos($useragent,'yodaoBot') !== false){$bot = 'Yodao';}
elseif (strpos($useragent,'twiceler') !== false){$bot = 'Twiceler';}
elseif (strpos($useragent,'ia_archiver') !== false){$bot = 'Alexa_';}
elseif (strpos($useragent,'iaarchiver') !== false){$bot = 'Alexa';}
elseif (strpos($useragent,'slurp') !== false){$bot = '雅虎';}
elseif (strpos($useragent,'bot') !== false){$bot = '其它蜘蛛';}
if(isset($bot)){
	die;
}

//开启session
session_start();

// 定义应用目录
//die( __DIR__ . '/application/');
define('APP_PATH', __DIR__ . '/application/');
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';