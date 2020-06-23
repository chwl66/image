<?php


namespace app\controller\ajax;


use app\BaseController;
use app\controller\ajax\provider\ImagesProvider;
use app\model\Image;
use think\facade\Request;

class AdminImages extends BaseController
{
    public function get()
    {

        $page = empty(Request::param('page')) ? 1 : Request::param('page');
        $limit = empty(Request::param('limit')) ? 12 : Request::param('limit');

        $username = empty(Request::param('username')) ? null : Request::param('username');
        $signatures = empty(Request::param('signatures')) ? null : Request::param('signatures');
        $filename = empty(Request::param('filename')) ? null : Request::param('filename');
        $type = Request::param('type');

        $model = Image::page($page, $limit)
            ->order('create_time', 'desc');

        $where = [];
        if (!empty($username) || !empty($signatures) || !empty($filename)) {

            if (!empty($signatures)) {
                $where[] = ['signatures', 'LIKE', '%' . $signatures . '%'];
            }
            if (!empty($filename)) {
                $where[] = ['filename', 'LIKE', '%' . $filename . '%'];
            }
            if (!empty($username)) {
                $userId = \app\model\User::where('username', 'LIKE', '%' . $username . '%')->column('id');
                $where[] = ['user_id', 'in', $userId];
            }
        }
        if ($type == 'suspicious') {
            $where[] = ['fraction', '>=', 70];
            $count = Image::where('fraction', '>=', 70)
                ->count();
        }else{
            $count = Image::count();
        }
        if (!empty($where)) {
            $model = $model
                ->where($where);
        }
        $model = $model
            ->with(['user' => function ($query) {
                $query->field(['id','username']);
            }])
            ->select();
        $images = $model->toArray();
        array_walk($images, function (&$value, $key) {
            if (empty($value['user'])){
                $value['user'] = [
                    'id'=>0,
                    'username'=>'游客',
                ];
            }
            $value['distribute'] = splice_distribute_url($value['signatures']);
            $value['info'] = Request::domain() . '/info/' . $value['signatures'];
        });
        return json_table(
            200,
            'success',
            $images,
            $page,
            $limit,
            $count
        );
    }


    public function delete()
    {
        $images = Request::param('images');
        //是否强制删除 === 1 强制删除
        $force = Request::param('force');
        $list = [];
        foreach ($images as $value) {
            $list[$value['userId']][] = $value['id'];
        }
        try {
            foreach ($list as $key => $value) {
                (new ImagesProvider())->deleteImages($value, $key, $force);
            }
        } catch (\Exception $e) {
            return msg(400, '错误信息：' . $e->getMessage());
        }
        return msg(200, '删除成功');
    }

    public function update()
    {
        $images = Request::param('images');
        if (empty($images) || !is_array($images)){
            $images = [];
            $images[] = Request::only(['id', 'url', 'filename', 'user_id', 'fraction', 'apiType']);
        }
        $errors = [];
        foreach ($images as $value){
            $model = Image::where('id', $value['id'])->find();
            $apiType = [];
            if (!empty($value['apiType'])) {
                $apiType = $value['apiType'];
            }
            if (!empty($value['url'])) {
                $value['url'] = json_decode($value['url'], true);
                if (!empty($value['url'])) {
                    $model->url =  $value['url'];
                    $model->save();
                }
            }
            unset($value['user_id']);
            unset($value['apiType']);
            $updateInfo = (new ImagesProvider())->updateInfo($model, $value, $apiType);

            if ($updateInfo !== true) {
                $errors[$value['id']] = $updateInfo;
            }
        }
        if (empty($errors)){
            return msg(200, 'success');
        }
        return msg(400, $updateInfo);

    }
}