<?php
//跳转301
function Jump_301($url){
    header("HTTP/1.1 301 Moved Permanently");
    header("Location:" . $url);
}
//可以访问静态目录
$STATIC = ['mobile','pc','public','static','99'];
$Folder = explode('/',$_SERVER['REDIRECT_URL'])[1];
$jump = true;
foreach($STATIC as $key => $val){
    if($Folder == $val){
    	$jump = false;
    }
}
if ($jump) Jump_301('https://baidu.com');