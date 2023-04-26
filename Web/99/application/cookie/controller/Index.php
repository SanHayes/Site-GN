<?php
namespace app\cookie\controller;
use think\Controller;
use think\Db;
use think\Cookie;


class Index extends Controller
{
    public function _verify()
    {
        var_dump($_COOKIE['verify']);
    }
}
