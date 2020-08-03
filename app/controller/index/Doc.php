<?php

namespace app\controller\index;

use app\BaseController;
use app\middleware\Template;
use app\model\Api;
use app\model\Storage;
use app\model\User;
use think\facade\Session;
use think\facade\View;

class Doc extends BaseController
{
    private $Hidove;
    private $userId;

    protected $middleware = [
        Template::class,
    ];

    protected function initialize()
    {
        $this->userId = User::get_user_id();
        $this->Hidove['user'] = User::where('id', $this->userId)->find();
        $this->Hidove['config']['system']['base'] = hidove_config_get('system.base.');
    }

    public function upload()
    {
        $this->Hidove['api'] = Api::order('id', 'asc')
            ->where('is_ok', 1)
            ->select();
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }

    public function imageList()
    {
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }

    public function folderList()
    {
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }

    public function imageInfo()
    {
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }

    public function ImageUpdate()
    {
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }
}
