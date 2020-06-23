<?php


namespace app\controller\ajax;


use app\model\SetGroup;
use think\facade\Request;

class AdminSetGroup
{
    public function get(){
        $id = Request::param('id');
        $name = Request::param('name');

        $model = SetGroup::whereOr('id', $id)
            ->whereOr('name',$name)->findOrEmpty();
        if ($model->isEmpty()){
            return msg(400,'没有找到该配置组');
        }
        return msg(200,'success',$model);

    }
    public function update(){

    }
    public function delete(){

    }
    public function create(){

    }

}