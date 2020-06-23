<?php


namespace app\controller\ajax;


use app\model\RechargeCard;
use app\model\User;
use think\facade\Request;
use think\model\Relation;

class AdminRechargeCard
{

    public function get()
    {

        $param = Request::param();
        $page = empty($param['page']) ? 1 : $param['page'];
        $limit = empty($param['limit']) ? 10 : $param['limit'];
        $model = RechargeCard::order('id', 'desc');
        $count = $model->count();

        if (!empty($param['key'])) {
            $model->where('key', 'LIKE', '%' . $param['key'] . '%');
        }
        if (!empty($param['username'])) {
            $id = User::where('username', 'LIKE', '%' . $param['username'] . '%')
                ->column('id');
            $model->where('user_id', 'IN', $id);
        }
        $data = $model
            ->with(['user' => function (Relation $query) {
                $query->field(['id', 'username']);
            }])
            ->page($page, $limit)
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

    public function export()
    {
        $type = Request::param('type');
        $model = new RechargeCard();
        switch ($type) {
            case 'notUsed':
                $model = $model->where('user_id', 0);
                break;
            case 'used':
                $model = $model->where('user_id', '<>', 0);
                break;
            case 'all':
                break;
        }
        $data = $model->column('key');
        $data = implode("\n", $data);
        return download($data, '卡密' . date('Y年m月d日H时i分s秒') . '.txt', true);
    }

    public function update()
    {
        $param = Request::param();
        unset($param['id']);
        $response = RechargeCard::where('id', $param['id'])->save($param);
        if (empty($response)) {
            return msg(400, '更新失败');
        } else {
            return msg(200, '更新成功', $param);
        }

    }

    public function delete()
    {
        $id = Request::param('id');
        $type = Request::param('type');
        if ($type == 'clearUsed') {
            $model = RechargeCard::where('user_id', '<>', 0);
        } else {
            if (is_array($id)) {
                $model = RechargeCard::where('id', 'IN', $id);
            } else {
                $model = RechargeCard::where('id', $id);
            }
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
        $number = empty($param['number']) ? 1 : $param['number'];
        $data = [];
        for ($i = 0; $i < $number; $i++) {
            $insertData['key'] = make_token();
            //面额
            $insertData['denomination'] = empty($param['denomination']) ? 100 : $param['denomination'];
            $insertData['user_id'] = 0;
            $insertData['create_time'] = time();
            $insertData['used_time'] = 0;
            $data[] = $insertData;
        }
        if (empty($data)) {
            return msg(400, '生成异常');
        }
        $model = new RechargeCard();
        $model->saveAll($data);
        return msg(200, 'success', $data);
    }
}