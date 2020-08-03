<?php


namespace app\controller\api\service;


use app\model\Image;
use app\model\User;
use think\Exception;
use think\facade\Cache;
use think\facade\Request;
use think\facade\Session;
use think\model\Collection;

class AuthCheck
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function run()
    {
        $model = User::get_user();
        if (!User::is_login($model)) {
            //游客
            if ($this->config['touristsUpload'] != 1) {

                throw new Exception('禁止游客上传');

            } else {
                if (is_web_upload()) {

                    $model = new User([
                        'id' => 0,
                        'username' => '游客',
                        'api_folder_id' => 0,
                        'group_id' => 1,
                    ]);
                } else {
                    throw new Exception('Unauthorized!');
                }
            }

        }
        //请求次数限制
        if ($model->group->frequency !== -1) {
            if ($model->id == 0) {

                $upload_log = Cache::get('upload_log_' . get_request_ip());

                if (empty($upload_log) && $upload_log instanceof Collection) {
                    $upload_log = new Collection();
                }
                $upload_log
                    ->whereNotBetween('create_time', [time() - 3600, time()])
                    ->delete();

                $count = $upload_log
                    ->count();

                $upload_log->push([
                    'create_time' => time()
                ]);
                Cache::set('upload_log_' . get_request_ip(), $upload_log);

            } else {

                $count = Image::where('user_id', '=', $model->id)
                    ->whereBetweenTime('create_time', time() - 3600, time())
                    ->count();

            }

            if ($model->group->frequency <= $count) {
                throw new Exception('上传频率过快！请稍后再进行上传！');
            }
        }
        return $model;
    }

}