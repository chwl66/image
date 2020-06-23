<?php

// +----------------------------------------------------------------------
// | Hidove [ www.hidove.cn ]
// +----------------------------------------------------------------------
// | Author: Ivey <loliconla@qq.com>
// +----------------------------------------------------------------------
// | Date: 2020年5月24日18:09:04
// +----------------------------------------------------------------------

namespace app\controller\ajax;


use think\facade\Request;

class AdminApi
{

    public function get()
    {

        $param = Request::param();
        $page = empty($param['page']) ? 1 : $param['page'];
        $limit = empty($param['limit']) ? 10 : $param['limit'];
        $model = \app\model\Api::order('id', 'asc');
        $count = $model->count();

        $data = $model
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

    public function update()
    {
        $parram = Request::param();
        if (empty($parram['oldId'])) {
            $model  = \app\model\Api::where('id', $parram['id']);
        } else {
            $oldId = $parram['oldId'];
            unset($parram['oldId']);
            $model = \app\model\Api::where('id', $oldId);
        }
        if (isset($parram['key'])){
            $parram['key'] = strtolower($parram['key']);
        }
        $response = $model->update($parram);
        if (empty($response)) {
            return msg(400, '更新失败');
        } else {
            return msg(200, '更新成功', $parram);
        }

    }

    public function delete()
    {

        $id = Request::param('id');
        $response = \app\model\Api::where('id', $id)->delete();
        if (empty($response)) {
            return msg(400, '删除失败');
        } else {
            return msg(200, '删除成功', ['id' => $id]);
        }
    }
    public function create(){
        $parram = Request::param();
        $response = \app\model\Api::create($parram);
        if (empty($response)) {
            return msg(400, '添加失败');
        } else {
            return msg(200, '添加成功', $parram);
        }
    }
}