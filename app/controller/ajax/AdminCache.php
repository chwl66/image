<?php


namespace app\controller\ajax;


use app\BaseController;
use app\model\Image;
use app\model\User;
use think\facade\Cache;
use think\facade\Request;

class AdminCache extends BaseController
{

    /**
     * 根据url刷新图片缓存
     * @return \think\response\Json
     */

    public function imageByUrl()
    {

        $refreshUrl = Request::param('url');
        array_walk($refreshUrl, function (&$value) {
            $value = get_mid_str($value, 'image/', '.');
            Image::where('signatures', $value)->update(['is_invalid' => 0]);
            Cache::delete('image_' . $value);
        });
        return msg(200, 'success');
    }

    /**
     * 根据 Signatures 刷新缓存
     * @return \think\response\Json
     */
    public function imageBySignatures()
    {

        $signatures = Request::param('signatures');
        Cache::delete('image_' . $signatures);
        Image::where('signatures', $signatures)
            ->update(['is_invalid' => 0]);
        return msg(200, 'success');
    }
    public function imageIsValid(){
        Image::where('id', '>', 0)
            ->update(['is_invalid' => 0]);
        return msg(200, 'success');
    }

    /**
     * 刷新全部图片缓存
     * @return \think\response\Json
     */
    public function allOfImage()
    {

        Image::where('id', '>', 0)
            ->update(['is_invalid' => 0]);
        Cache::tag('image')->clear();

        return msg(200, 'success');

    }

    /**
     * 刷新某个用户的图片缓存
     * @return \think\response\Json
     */
    public function ImageByUsername()
    {

        $userId = Request::param('id');
        $model = User::where('username', $userId)->findOrEmpty();
        if (!$model->isExists()) {
            return msg(400, '该用户不存在');
        }
        Image::where('user_id', $userId)->update(['is_invalid' => 0]);

        Cache::tag('image_user_' . $userId)->clear();

        return msg(200, 'success');

    }
    public function opcache(){

        if (!function_exists('opcache_reset'))
            return msg(400, 'The function opcache_reset is not found!');

        opcache_reset();

        return msg(200, 'success');
    }

    /**
     * 刷新全部配置缓存
     * @return \think\response\Json
     */
    public function allOfConfig()
    {
        Cache::tag('config')->clear();
        Cache::tag('config_user')->clear();

        return msg(200, 'success');

    }

    /**
     * 刷新全部缓存
     * @return \think\response\Json
     */
    public function all()
    {

        if (function_exists('opcache_reset'))
            opcache_reset();

        Image::where('id', '>', 0)
            ->update(['is_invalid' => 0]);
        Cache::clear();

        return msg(200, 'success');

    }
}