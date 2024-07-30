<?php
define('API_URL', 'http://45.194.37.19/99/index/api/');
while(true){
	$t = time();
    if($t % 5 == 0){
        file_get_contents(API_URL . 'order');
    }
    if($t % 30 == 0){
        file_get_contents(API_URL . 'allotorder');
        file_get_contents(API_URL . 'checkbal');
    }
    if($t % 60 == 0){
        file_get_contents(API_URL . 'getdate');
        file_get_contents(API_URL . 'interest');
    }
    sleep(1);
}