<?php


namespace app\controller\api\service;


use app\model\ApiRequest;
use think\facade\Request;

class RecordRequest
{

    public static function run()
    {
        $model = ApiRequest::whereDay('create_time', 'today');
        $is_token = is_token();
        if (empty($is_token)) {
            $model = $model->where('key', 'officialUpload');
        } else {
            $model = $model->where('key', 'tokenUpload');
        }
        $model = $model
            ->findOrEmpty();
        if (!$model->isExists()) {
            $model = new ApiRequest();
            $model->key = empty($is_token)?'officialUpload':'tokenUpload';
            $model->create_time = time();
            $model->total_request_times = 0;
        }
        $model->update_time = time();
        $model->total_request_times++;
        $model->save();
    }

}