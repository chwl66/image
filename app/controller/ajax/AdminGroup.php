<?php


namespace app\controller\ajax;


use app\BaseController;
use app\model\Group;
use app\model\Storage;
use think\facade\Request;

class AdminGroup extends BaseController
{
    public function get()
    {
        $param = Request::param();
        $page = empty($param['page']) ? 1 : $param['page'];
        $limit = empty($param['limit']) ? 10 : $param['limit'];
        $type = empty($param['type']) ? 'normal' : $param['type'];
        $model = Group::order('id', 'asc');
        $count = $model->count();
        if ($type !== 'all') {
            $model = $model
                ->page($page, $limit);
        }
        $data = $model
            ->select()
            ->toArray();

        $storageList = Storage::column('name');

        array_walk($data, function (&$value) use ($storageList) {
            $value['storageList'] = $storageList;
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

        $id = $param['id'];
        unset($param['id']);

        $model = Group::where('id', $id)->findOrEmpty();

        if (!$model->isExists()) {

            return msg(400, '该用户组不存在');
        }

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
            $model = Group::where('id', 'IN', $id);
        } else {
            $model = Group::where('id', $id);
        }
        $model = $model->findOrEmpty();

        if ($model->isEmpty()) {

            return msg(400, '该用户组不存在');
        }
        if (in_array($model->id, [1, 2])) {
            return msg(400, '禁止删除该成员');
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
        unset($param['id']);

        $user = new Group($param);

        $user->name = empty($user->name) ? 'VIP' . mt_rand(1, 100) . '组' : $user->name;
        $user->capacity = empty($user->capacity) ? 10737418240 : $user->capacity;
        $user->storage = empty($user->storage) ? 'this' : $user->storage;
        $user->price = empty($user->price) ? 99999999 : $user->price;
        $user->frequency = empty($user->frequency) ? -1 : $user->frequency;
        $user->picture_process = empty($user->picture_process) ? 0 : $user->picture_process;

        $user->save();
        return msg(200, '添加成功');
    }
}