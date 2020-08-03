<?php
// +----------------------------------------------------------------------
// | Hidove [ www.hidove.cn ]
// +----------------------------------------------------------------------
// | Author: Ivey <loliconla@qq.com>
// +----------------------------------------------------------------------
// | Date: 2019年11月4日18:57:23
// +----------------------------------------------------------------------


namespace app\controller\index;

use app\BaseController;
use app\controller\common\ImageInitial;
use app\middleware\Template;
use app\model\Api;
use app\model\Image;
use app\model\User;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;

class Index extends BaseController
{
    protected $userId = null;
    protected $Hidove = null;

    protected $middleware = [
        Template::class
    ];

    protected function initialize()
    {
        $this->userId = User::get_user_id();
        $this->Hidove['user'] = \app\model\User::where('id', $this->userId)->find();

        $this->Hidove['config']['system']['base'] = hidove_config_get('system.base.');
        $this->Hidove['config']['system']['other'] = hidove_config_get('system.other.');
    }

    public function index()
    {
        $total = Image::count();
        $this->Hidove['api'] = Api::order('id', 'asc')->where('is_ok', 1)->select();

        $this->Hidove['total'] = $total;
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }

    public function simple()
    {
        $this->Hidove['api'] = Api::order('id', 'asc')->where('is_ok', 1)->select();

        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }

    public function info()
    {
        $signatures = Request::param('signatures');
        $image = Image::where('signatures', $signatures)->findOrEmpty();
        if (!$image->isExists()) {
            throw new \think\exception\HttpException(404, '404 NOT FOUND!');
        }
        $api = Api::select();
        foreach ($api as $value) {
            $this->Hidove['api'][strtolower($value['key'])] = $value['name'];
        }

        $imageInitial = new ImageInitial($image);
        $image['url'] = $imageInitial->run();
        $this->Hidove['image'] = $image;
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }

    public function about()
    {
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }

    public function changelog()
    {
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }

    public function explore()
    {
        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }

    public function distribute()
    {
        return $this->fetch('images/index.html');
    }

    public function cdn()
    {

        View::assign([
            'Hidove' => $this->Hidove,
        ]);
        return View::fetch();
    }
}
