<?php


namespace app\controller\ajax;


use app\BaseController;
use app\controller\ajax\provider\ImagesProvider;
use app\model\Folders;
use app\model\Group;
use app\model\User;
use think\facade\Request;
use think\facade\Validate;
use think\model\Relation;

class AdminUser extends BaseController
{
    public function get()
    {

        $param = Request::param();
        $page = empty($param['page']) ? 1 : $param['page'];
        $limit = empty($param['limit']) ? 10 : $param['limit'];
        $model = User::order('id', 'desc');
        $count = $model->count();

        if (!empty($param['id'])) {
            $model->where('id', 'LIKE', '%' . $param['id'] . '%');
        }
        if (!empty($param['username'])) {
            $model->where('username', 'LIKE', '%' . $param['username'] . '%');
        }
        if (!empty($param['email'])) {
            $model->where('email', 'LIKE', '%' . $param['email'] . '%');
        }
        if (!empty($param['group_id'])) {

            $model->where('group_id', '=', $param['group_id']);
        }
        $groupList = Group::field('id,name')->select()->toArray();
        $data = $model
            ->with(['group' => function (Relation $query) {
                $query->field('id,name');
            }])
            ->withCount('images')
            ->page($page, $limit)
            ->select()->toArray();
        array_walk($data, function (&$value) use ($groupList) {
            $value['groupList'] = $groupList;
        });
        return json_table(
            200,
            'success',
            $data,
            $page,
            $limit,
            $count
        );
    }

    public function update()
    {
        $param = Request::param();

        $validate = Validate::rule([
            'username|用户名' => 'alphaNum|length:5,26|unique:User',
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
        $id = $param['id'];
        unset($param['id']);
        $model = User::where('id', $id)->findOrEmpty();

        if ($model->isEmpty()) {

            return msg(400, '该用户不存在');
        }
        if (!empty($param['password'])) {
            $param['password'] = hidove_md5($param['password']);
        }
        if (!empty($param['token'])) {
            $param['token'] = make_token();
        }
        $param['update_time'] = time();
        $response = $model->save($param);
        if (empty($response)) {
            return msg(400, '更新失败');
        } else {
            return msg(200, '更新成功', $model);
        }
    }

    public function delete()
    {
        $id = Request::param('id');
        if (is_array($id)) {
            $model = User::where('id', 'IN', $id);
        } else {
            $model = User::where('id', $id);
        }
        $collection = $model->with(
            ['images' => function (Relation $query) {
                $query->field('user_id,id');
            }, 'folders' => function ($query) {
                $query->field('user_id,id');
            }]
        )->select();

        try {
            foreach ($collection as $value) {
                if ($value->id === 1) {
                    return msg(400, '禁止删除该成员');
                }
                $imageList = $value->images->toArray();
                $foldersList = $value->folders->toArray();
                array_walk($imageList, function (&$value) {
                    $value = $value['id'];
                });
                array_walk($foldersList, function (&$value) {
                    $value = $value['id'];
                });
                $this->deleteImage($imageList, $value->id);
                $this->deleteFolder($foldersList);
            }

        } catch (\Exception $e) {
            return msg(400, $e->getMessage());
        }
        if ($model->delete()) {
            return msg(200, '删除成功', ['id' => $id]);
        } else {
            return msg(400, '删除失败');
        }
    }

    public function create()
    {
        $param = Request::param();
        $validate = Validate::rule([
            'username|用户名' => 'require|alphaNum|length:5,26|unique:User',
            'password|密码' => 'require|alphaDash|length:6,26',
            'email|邮箱' => 'require|email|unique:User',
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
        $user->ip = Request::ip();
        $user->is_private = 0;
        $user->is_whitelist = 0;
        $user->watermark = [];
        $user->storage = [];
        $user->forbidden_node = [];

        $user->save();
        return msg(200, '注册成功');
    }


    private function deleteImage(array $imageId, int $userId)
    {
        return (new ImagesProvider())->deleteImages($imageId, $userId);
    }

    private function deleteFolder(array $folderId)
    {
        return Folders::where(
            'id', 'IN', $folderId
        )->delete();
    }

}