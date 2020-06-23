<?php


namespace app\controller\ajax;


use app\BaseController;
use app\model\Image;
use think\facade\Cache;
use think\facade\Request;
use think\facade\Session;

class UserCache extends BaseController
{
    private $userId;

    protected function initialize()
    {
        $this->userId = Session::get('userId');
    }

    public function refresh()
    {

        $refreshUrl = Request::param('refreshUrl');
        array_walk($refreshUrl, function (&$value) {
            $value = get_mid_str($value, 'image/', '.');
            Image::where('signatures' ,$value)->update(['is_invalid' => 0]);
            Cache::delete('image_' . $value);
        });
        return msg(200, 'success');
    }

    public function refreshConfig()
    {

        Cache::tag('config_user_' . $this->userId)->clear();
        return msg(200, 'success');
    }

    public function imageIsValid(){
        Image::where('user_id',$this->userId)
            ->update(['is_invalid' => 0]);
        return msg(200, 'success');
    }
    public function refreshAll()
    {
        Image::where('user_id' ,$this->userId)
            ->update(['is_invalid' => 0]);

        Cache::tag('image_user_' . $this->userId)->clear();

        return msg(200, 'success');

    }
}