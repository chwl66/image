<?php


namespace app\controller\api\service;


use app\model\Blacklist;
use think\Exception;
use think\facade\Request;

class BlackListFilter
{
    private $user;

    private $ip;

    public function __construct($user)
    {
        $this->user = $user;
        $this->ip = get_request_ip();
    }

    public function run()
    {
        $username = $this->user['username'] ;

        if (empty($this->user['username']) || $this->user['username'] == "游客") {
            $username = uniqid();
        }
        $data = Blacklist::whereOr([
                [
                    ['username', '=', $username],
                    ['release_time', '>', time()],
                ],
                [
                    ['ip', '=', $this->ip],
                    ['release_time', '>', time()],
                ]
            ]
        )->find();
        if (!empty($data)) {
            if ($data['ip'] == $this->ip) {
                throw new Exception('您的IP已被限制上传<br/>原因：' . $data['reason'] . '<br/>释放时间：' . date('Y年m月d日 H时i分s秒', $data['release_time']), 10006);
            } else {
                throw new Exception('您的用户名已被限制上传<br/>原因：' . $data['reason'] . '<br/>释放时间：' . date('Y年m月d日 H时i分s秒', $data['release_time']), 10006);
            }
        }
    }
}