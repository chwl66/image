<?php
// +----------------------------------------------------------------------
// | Hidove [ www.hidove.cn ]
// +----------------------------------------------------------------------
// | Author: Ivey <loliconla@qq.com>
// +----------------------------------------------------------------------
// | Date: 2020年5月13日17:23:44
// +----------------------------------------------------------------------

namespace app\controller\index;

use app\BaseController;
use app\middleware\Template;
use app\middleware\UserAuth;
use app\model\Api;
use think\facade\Session;
use think\facade\View;
use think\Request;

class User extends BaseController
{

    private $Hidove;

    protected $middleware = [
        UserAuth::class => ['except' => ['login', 'loginOut', 'register', 'forget', 'resetPassword']],
        Template::class,
    ];

    protected function initialize()
    {
        $this->Hidove['config']['system']['base'] = hidove_config_get('system.base.');
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
    }

    public function login()
    {
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }

    public function register()
    {
        return View::fetch();
    }

    public function loginOut()
    {
        Session::clear();
        $this->success('退出成功', url('user/login'));
    }

    public function images(Request $request)
    {

        $this->Hidove['user'] = $request->user;
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }

    public function index(Request $request)
    {
        $this->Hidove['user'] = $request->user;
        //获取多级目录名称
        if (!empty($this->Hidove['user']->apiFolder)) {
            $parent = $this->Hidove['user']->apiFolder->parent;
            while (!empty($parent->parent_id) && $parent->parent_id != 0) {
                $this->Hidove['user']->apiFolder->name = $parent->name . '/' . $this->Hidove['user']->apiFolder->name;
                $parent = $parent->parent;
            }
            $this->Hidove['user']->apiFolder->name = '/' . $this->Hidove['user']->apiFolder->name;
        }
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }

    public function forget()
    {
        return View::fetch();
    }

    public function resetPassword()
    {
        return View::fetch();
    }

    public function cache(Request $request)
    {
        $this->Hidove['user'] = $request->user;
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }

    public function node(Request $request)
    {
        $this->Hidove['user'] = $request->user;
        $this->Hidove['api'] = Api::order('id', 'asc')->where('is_ok', 1)->select();
        $this->Hidove['forbidden_node'] = $this->Hidove['user']->forbidden_node;
        foreach ($this->Hidove['api'] as &$value) {
            if (in_array(mb_strtolower($value['key']), $this->Hidove['forbidden_node'])) {
                $value['checked'] = 1;
            } else {
                $value['checked'] = 0;
            }
        }
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }

    public function storage(Request $request)
    {
        $this->Hidove['user'] = $request->user;
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }

    public function finance(Request $request)
    {
        $this->Hidove['user'] = $request->user;
        $this->Hidove['config']['system']['other'] = hidove_config_get('system.other.');
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }

    public function watermark(Request $request)
    {
        $this->Hidove['user'] = $request->user;
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }
}
