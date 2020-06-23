<?php


namespace app\controller\ajax;


use app\BaseController;
use app\model\User;
use think\facade\Filesystem;
use think\facade\Request;
use think\facade\Session;
use think\facade\Validate;

class UserWatermark extends BaseController
{
    private $user;

    protected function initialize()
    {
        $userId = Session::get('userId');
        $this->user = User::where('id', $userId)->findOrEmpty();
    }

    public function get()
    {
        return msg(200, 'success', $this->user->watermark);
    }

    public function update()
    {
        $param = Request::param();
        $file = Request::file('file');

        if (!empty($file)) {
            $validate = Validate::rule([
                //200kb
                'file' => 'file|image|fileSize:204800',
            ]);
            if (!$validate->check([
                'file' => $file
            ])) {
                return msg(400, $validate->getError());
            }
            Filesystem::putFileAs('watermark', $file, md5($this->user->email));
        }

        $param['imageWatermark']['pathname'] = 'watermark/' . md5($this->user->email);
        if (empty($param['textWatermark']['font'])) {
            $param['textWatermark']['font'] = 'default.ttf';
        }
        $this->user->watermark = $param;
        $this->user->save();
        return msg(200, 'success');
    }
}