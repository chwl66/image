<?php

// +----------------------------------------------------------------------
// | Hidove [ www.hidove.cn ]
// +----------------------------------------------------------------------
// | Author: Ivey <loliconla@qq.com>
// +----------------------------------------------------------------------
// | Date: 2020年5月24日18:07:45
// +----------------------------------------------------------------------


namespace app\controller\ajax;


use app\model\User;
use think\facade\Request;
use think\facade\Session;
use think\facade\Validate;

class AdminAuth
{

    public function login()
    {
        $param = Request::param();
        $validate = Validate::rule([
            'username|用户名' => 'require|alphaNum|length:5,26',
            'password|密码' => 'require|alphaDash|length:6,26',
            'captcha|验证码' => 'require|captcha',
        ]);
        if (!$validate->check($param)) {

            return msg(400, $validate->getError());
        }

        $user = User::where([
            'username' => $param['username'],
            'password' => hidove_md5($param['password']),
            'group_id' => 2,
        ])->findOrEmpty();
        if ($user->isEmpty()) {
            return msg(400, '用户名或密码错误');
        }
        Session::set('userId', $user->id);
        return msg(200, '登录成功');
    }

}