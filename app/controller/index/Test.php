<?php


namespace app\controller\index;



use think\facade\Cache;

class Test
{
    public function index(){
        Cache::tag('config')->clear();
        return '你好';
    }

}