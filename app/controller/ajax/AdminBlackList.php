<?php


namespace app\controller\ajax;


use app\model\Blacklist;
use think\facade\Request;

class AdminBlackList
{

    public function get()
    {

        $param = Request::param();
        $page = empty($param['page']) ? 1 : $param['page'];
        $limit = empty($param['limit']) ? 10 : $param['limit'];
        unset($param['page']);
        unset($param['limit']);
        $param = array_filter($param);
        $where = [];
        foreach ($param as $key => $value) {
            $where[] = [
                $key, 'like', "%$value%"
            ];
        }
        $model = Blacklist::order('create_time', 'desc');
        $count = $model->count();
        $data = $model
            ->page($page, $limit)
            ->where($where)
            ->select();

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

        $parram = Request::param();
        $id = $parram['id'];
        unset($parram['id']);
        $model = Blacklist::where('id', $id)->findOrEmpty();
        if (!$model->isExists()) {
            return msg(400, '找不到这条记录');
        }
        if (!empty($parram['duration'])) {
            $model->duration = (int)$parram['duration'];
            $model->release_time = (int)$model->create_time + (int)$model->duration;
        }
        if (!empty($parram['release_time'])) {
            $model->duration = (int)$parram['release_time'] - (int)$model->create_time;
            $model->release_time = (int)$model->create_time + (int)$model->duration;
        }
        $response = $model->save();
        if (!$response) {
            return msg(400, '更新失败');
        } else {
            return msg(200, '更新成功', $model);
        }

    }

    public function delete()
    {
        $id = Request::param('id');
        if (!is_array($id)) {
            $id = explode(',', $id);
        }
        $response = Blacklist::where('id', 'IN', $id)->delete();
        if (empty($response)) {
            return msg(400, '删除失败');
        } else {
            return msg(200, '删除成功', ['id' => $id]);
        }
    }

    public function create()
    {

        $param = Request::param();
        $param['create_time'] = time();
        $param['duration'] = empty($param['duration']) ? 300 : $param['duration'];
        $param['fraction'] = empty($param['fraction']) ? 100 : $param['fraction'];
        $param['reason'] = empty($param['reason']) ? '看他不爽' : $param['reason'];
        $param['username'] = empty($param['username']) ? uniqid() : $param['username'];
        $param['ip'] = empty($param['ip']) ? '0.0.0.0' : $param['ip'];
        $param['release_time'] = (int)$param['create_time'] + (int)$param['duration'];
        unset($param['id']);
        try {
            Blacklist::create($param);
            return msg(200, 'success');
        } catch (\Exception $e) {
            return msg(400, $e->getMessage());
        }
    }
}