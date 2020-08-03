<?php


namespace app\controller\ajax;


use app\BaseController;
use app\middleware\Throttle;
use app\model\User;
use think\facade\Request;
use think\facade\Session;
use think\facade\Validate;

class UserAuth extends BaseController
{
    protected $middleware = [
        Throttle::class => ['only' => ['forget', 'resetPassword']]
    ];

    public function login()
    {
        $param = Request::param();
        $validate = Validate::rule([
            'username|用户名' => 'require|alphaNum|length:5,26',
            'password|密码' => 'require|alphaDash|length:6,26',
        ]);
        if (!$validate->check($param)) {

            return msg(400, $validate->getError());
        }

        $user = User::where([
            'username' => $param['username'],
            'password' => hidove_md5($param['password']),
        ])->findOrEmpty();
        if (!$user->isExists()) {
            return msg(400, '用户名或密码错误');
        }
        Session::set('userId', $user->id);
        return msg(200, '登录成功');
    }

    public function register()
    {
        if (hidove_config_get('system.base.openRegistration') != 1) return msg(400, '已禁止注册');
        $param = Request::param();
        $validate = Validate::rule([
            'username|用户名' => 'require|alphaNum|length:5,26|unique:User',
            'password|密码' => 'require|alphaDash|length:6,26',
            'email|邮箱' => 'require|email|unique:User',
            'captcha|验证码' => 'require|captcha'
        ]);
        if (!$validate->check($param)) {

            return msg(400, $validate->getError());
        }

        $user = new User($param);
        $user->token = make_token();
        $user->password = hidove_md5($user->password);
        $user->create_time = time();
        $user->capacity_used = 0;
        $user->api_folder_id = 0;
        $user->group_id = 1;
        $user->ip = get_request_ip();
        $user->is_private = 0;
        $user->is_whitelist = 0;
        $user->watermark = [];
        $user->storage = [];
        $user->forbidden_node = [];

        $user->save();
        return msg(200, '注册成功');
    }

    public function forget()
    {
        $param = Request::param();
        $validate = Validate::rule([
            'username|用户名' => 'require|alphaNum',
            'email|邮箱' => 'require|email',
        ]);
        if (!$validate->check($param)) {

            return msg(400, $validate->getError());
        }
        $model = User::where([
            'username' => $param['username'],
            'email' => $param['email'],
        ])->findOrEmpty();
        if (!$model->isExists()) {
            return msg(400, '用户名或邮箱错误');
        }

        $model->reset_key = make_token();
        $model->reset_time = time() + 3600;
        $model->save();
        $sitename = hidove_config_get('system.base.sitename');
        $subject = $sitename . '密码重置校验';
        $toAddresses = $model->email;
        $reset_key = $model->reset_key;
        $resetUrl = Request::domain() . '/user/resetPassword/?resetKey=' . $reset_key;
        $body = "
        <h3>亲爱的 $model->username,</h3>
<p>您选择了通过邮件找回 $sitename 密码。请在重设密码的页面中输入以下 验证秘钥 和新的密码，完成密码重设：</p>
<p>【<span style='color:red;'> $reset_key </span>】</p>
<p>(此验证码有效时间为 10 分钟，若超时请重新获取邮件)</p>
<p>重设密码页面 <a href='$resetUrl' target='_blank'>$resetUrl</a></p>
<p>如果您要放弃重设密码，或者未曾申请密码重设，请忽略此邮件。</p>
<p>为了您的账户安全，请您注意对此邮件内容保密。</p>";
        $res = (new \app\provider\Email())->run($subject, $toAddresses, $body);
        if ($res) {
            return msg(200, '发送成功');
        }
        return msg(400, $res);

    }

    public function resetPassword()
    {
        $param = Request::param();
        $validate = Validate::rule([
            'resetKey|验证秘钥' => 'require|alphaNum',
            'password|密码' => 'require|alphaDash|length:6,26',
        ]);
        if (!$validate->check($param)) {

            return msg(400, $validate->getError());
        }
        $model = User::where(
            [
                ['reset_key', '=', $param['resetKey']],
                ['reset_time', '>=', time()],
            ]
        )->findOrEmpty();

        if (!$model->isExists()) {
            return msg(400, '验证秘钥无效');
        }
        $model->reset_time = 0;
        $model->reset_key = time();
        $model->password = hidove_md5($param['password']);
        $model->save();
        return msg(200, '密码重置成功');
    }
}