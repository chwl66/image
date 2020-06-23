<?php


namespace app\controller\ajax;


use app\BaseController;
use app\model\Folders;
use think\facade\Request;
use think\facade\Session;
use think\facade\Validate;

class UserControl extends BaseController
{
    private $user;

    protected function initialize()
    {
        $userId = Session::get('userId');
        $this->user = \app\model\User::where('id', $userId)->find();
    }

    public function get()
    {
        return msg(200, 'success', $this->user);
    }

    public function update()
    {
        $param = Request::only([
            'email',
            'password',
            'token',
            'is_private',
        ]);
        $validate = Validate::rule([
            'password|密码' => 'alphaDash|length:6,26',
            'email|邮箱' => 'email|unique:User',
        ]);
        if (!$validate->check($param)) {
            return msg(400, $validate->getError());
        }
        $param = array_filter($param, function ($value) {
            if ($value === '' || $value === null) {
                return false;
            }
            return true;
        });
        if (!empty($param['password'])) {
            $param['password'] = hidove_md5($param['password']);
        }
        if (!empty($param['token'])) {
            $param['token'] = make_token();
        }
        if (!empty($param['is_private'])) {

            $param['is_private'] = $param['is_private'] == 1 ? 1 : 0;
        }
        $this->user->save($param);
        return msg(200, 'success', $this->user);
    }

    public function updateApiFolder()
    {

        $folderName = Request::param('folderName');

        $validate = Validate::rule(['regex' => '/^[\x{4e00}-\x{9fa5}A-Za-z0-9\_\-\/]+$/u']);
        if (!$validate->check([$folderName])) {
            return msg(400, $validate->getError());
        }
        $parentId = 0;
        if ($folderName != '/') {
            $folderArr = explode('/', $folderName);
            $folderArr = array_filter($folderArr, function ($value) {
                return $value;
            });
            foreach ($folderArr as $value) {
                $find = Folders::create([
                    'name' => $value,
                    'user_id' => $this->user['id'],
                    'parent_id' => $parentId,
                    'create_time' => time(),
                ]);
                $parentId = $find->id;
            }
        }
        $this->user->api_folder_id = $parentId;
        $this->user->save();

        //获取多级目录名称
        $parent = $this->user->apiFolder->parent;
        while (!empty($parent->parent_id) && $parent->parent_id != 0) {
            $this->user->apiFolder->name = $parent->name . '/' . $this->user->apiFolder->name;
            $parent = $parent->parent;
        }
        $this->user->apiFolder->name = '/' . $this->user->apiFolder->name;
        return msg(200, 'success', $this->user->apiFolder);
    }
}