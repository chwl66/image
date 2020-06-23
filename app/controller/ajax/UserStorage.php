<?php


namespace app\controller\ajax;


use app\BaseController;
use app\model\Folders;
use think\facade\Request;
use think\facade\Session;
use think\facade\Validate;

class UserStorage extends BaseController
{
    private $user;

    protected function initialize()
    {
        $userId = Session::get('userId');
        $this->user = \app\model\User::where('id', $userId)->find();
    }

    public function get()
    {
        return msg(200, 'success', $this->user->storage);
    }

    public function update()

    {
        $param = Request::param();
        $retentionDomain = base64_decode(hidove_config_get('system.other.retentionDomain'));
        $retentionDomainArr = explode("\n", $retentionDomain);

        foreach ($param as $key => $value) {
            if (empty($value['cdn']) && empty($value['distribute'])) {
                continue;
            }
            if (empty($value['cdn'])) {
                $parse_url = parse_url($value['distribute']);
            } else {
                $parse_url = parse_url($value['cdn']);
            }
            if (!empty($parse_url['host'])) {
                $cdnDomain = $parse_url['host'];
            } else {
                return msg(400, ucfirst($key) . '：CDN加速域名添加错误，正确格式[http://xxx.com]');
            }
            foreach ($retentionDomainArr as $v) {
                $pattern = '~' . str_replace('*.', '[^\s]+', $v) . '$~';
                if (preg_match($pattern, $cdnDomain) != 0 && !empty($v))
                    return msg(400, '[' . $cdnDomain . ']该域名为系统保留域名，禁止绑定');
            }
        }
        $this->user->storage = $param;
        $this->user->save();
        return msg(200, 'success', $this->user);
    }

}