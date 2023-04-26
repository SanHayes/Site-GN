<?php
//可以访问静态目录
$STATIC = ['99'];
if(isset($_SERVER['REDIRECT_URL'])){
	$Folder = explode('/',$_SERVER['REDIRECT_URL'])[1];
	foreach($STATIC as $key => $val){
	    if(!in_array($Folder,$STATIC)){
	        header("Location:https://www.baidu.com");
	        exit;
	    }
	}
}else{
	header("Location:https://www.baidu.com");
	exit;
}
