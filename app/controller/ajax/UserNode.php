<?php


namespace app\controller\ajax;


use app\BaseController;
use app\model\User;
use think\facade\Request;
use think\facade\Session;

class UserNode extends BaseController
{
    private $user;

    protected function initialize()
    {
        $userId = Session::get('userId');
        $this->user = User::where('id', $userId)->findOrEmpty();
    }
    public function update()
    {

        $forbiddenNode = Request::param('forbiddenNode');
        foreach ($forbiddenNode as $key => $value){
            if (!empty($value)){
                $forbiddenNode[$key]= mb_strtolower($value);
            }else{
                unset($forbiddenNode[$key]);
            }
        }
        $this->user->forbidden_node = $forbiddenNode;
        $this->user->save();
        return msg(200,'success');
    }
}