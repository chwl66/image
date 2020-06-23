<?php


namespace app\crontab;


use app\controller\ajax\provider\ImagesProvider;
use app\model\Image;
use app\provider\Table;
use think\facade\Cache;
use think\facade\Log;

class Distribute
{
    /**
     * 执行任务
     * @return mixed
     */
    public function execute()
    {
        //判断时间间隔
        $lastExecuteTime = Cache::get('cron_image_update');
        $cron_image_update_time = env('CRON_IMAGEUPDATE_TIME');
        $retry = ($lastExecuteTime + $cron_image_update_time) - time();
        if ($retry >= 0 && php_sapi_name() !== 'cli') {
            //...具体的任务执行
            $result = [
                'msg' => '尚未到执行时间，请等待' . $retry . '秒后再试',
            ];

        } else {
            //...具体的任务执行
            Cache::set('cron_image_update', time());
            $result = $this->main();

        }
        $result['running_time'] = time();

        //任务名称
        $taskName = __CLASS__;
        if (php_sapi_name() === 'cli') {

            $msg = (new Table())->run($result);
            print "******************************************\n";
            print "执行任务 " . $taskName . " 时间：" . date('H:i:s', time()) . "\n";
            print  $msg;
            print "******************************************\n\n";
            Log::record("执行任务 " . $taskName . " 时间：" . date('H:i:s', time()) . "\n" . $msg, 'info');
            //命令行运行
        } else {

            return msg(200, 'success', $result);
        }
    }

    public function main()
    {
        $limit = env('cron.IMAGEUPDATE_LIMIT');
        $apiType = format_api_type(env('cron.IMAGEUPDATE_API_TYPE'));

        $model = Image::limit($limit)
            ->order('update_time', 'asc')
            ->where('is_invalid', 0);
        $model->where(function ($query) use ($apiType) {
            foreach ($apiType as $key => $value) {
                $query->whereOr('url->' . $value, '=', null);
            }
        });
        $collection = $model
            ->select();
        $result = [
            'apiType' => $apiType
        ];
        foreach ($collection as $value) {
            $publicCloud = [];
            $existsApiType = array_keys($value->url);
            foreach ($apiType as $v) {
                if (!in_array($v, $existsApiType)) {
                    $publicCloud[] = $v;
                }
            }
            $doApiType = [
                'publicCloud' => $publicCloud,
                'privateStorage' => []
            ];
            $updataInfo = (new ImagesProvider())->updateInfo($value, [
                'update_time' => time()
            ], $doApiType);
            $result['items'][$value->signatures] = ($updataInfo === true) ? 'success' : $updataInfo;
        }
        return $result;
    }

}