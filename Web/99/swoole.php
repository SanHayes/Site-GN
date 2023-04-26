<?php
use Swoole\Coroutine;
use function Swoole\Coroutine\run;
use Swoole\Runtime;

Runtime::enableCoroutine(SWOOLE_HOOK_ALL);

define('API_URL', 'https://admin.827618.com/99/index/api/');

run(function () {
    go(function () {
        // 获取数据
        do{
            file_get_contents(API_URL . 'getdate');
            sleep(3);
        }
        while(true);
    });
    
    go(function() {
        // 订单更新
        do{
            file_get_contents(API_URL . 'order');
            sleep(3);
        }
        while(true);
    });
    
    go(function() {
        // 订单对冲
        do{
            file_get_contents(API_URL . 'allotorder');
            sleep(3);
        }
        while(true);
    });
    
    go(function() {
        // 订单更新
        do{
            file_get_contents(API_URL . 'checkbal');
            sleep(30);
        }
        while(true);
    });
    
    go(function() {
        // 订单更新
        do{
            file_get_contents(API_URL . 'interest');
            sleep(60);
        }
        while(true);
    });
});