<?php


namespace app\controller\ajax;


use app\model\ApiRequest;
use app\model\Image;
use app\model\ImageRequest;
use app\model\User;
use Carbon\Carbon;
use think\facade\Request;

class AdminStatistics
{
    //是否缓存
    private $cache = false;
    private $cacheExpire = 60;

    //最近一周上传的图片数
    public function getWeeklyPictureUpload()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $startTime = Carbon::today()->subDays($i)->getTimestamp();
            $endTime = Carbon::today()->subDays($i - 1)->getTimestamp() - 1;
            $model = Image::whereBetweenTime('create_time', $startTime, $endTime);
            $count = $model->cache($this->cache, $this->cacheExpire)->count();
            $data[Carbon::today()->subDays($i)->format('m-d')] = $count;
        }
        return msg(200, 'success', $data);
    }

    //今日用户图片上传数排行榜
    public function getTodayUserPictureUpload()
    {
        $data = [
            '总数' => [],
            '游客' => [],
        ];
        $column = Image::whereDay('create_time', 'today')
            ->cache($this->cache, $this->cacheExpire)
            ->column('user_id');

        $array_count_values = array_count_values($column);
        $array_keys = array_keys($array_count_values);
        $userList = User::where('id', 'in', $array_keys)
            ->field('id,username')->cache($this->cache, $this->cacheExpire)->select();

        foreach ($array_keys as $v) {
            $username = $userList->where('id', $v)->first();
            if ($username) {
                $data[$username->username]['today'] = $array_count_values[$v];
                $data[$username->username]['yesterday'] =
                    $username->images()
                        ->whereDay('create_time', 'yesterday')
                        ->count();
            } else {
                $data['游客']['today'] = $array_count_values[$v];
                $data['游客']['yesterday'] = Image::whereDay('create_time', 'yesterday')
                    ->where('user_id', 0)->count();

            }
        }

        $data['总数']['today'] = count($column);
        $data['总数']['yesterday'] = Image::whereDay('create_time', 'yesterday')
            ->count();
        return msg(200, 'success', $data);
    }

    //获取最近一周API调用情况

    public function getWeekApiRequestInfo()
    {
        $data = [];

        $startTime = Carbon::today()->subDays(6)->getTimestamp();
        $endTime = Carbon::today()->addDays(1)->getTimestamp() - 1;
        $model = ApiRequest::whereBetweenTime('create_time', $startTime, $endTime);
        $collection = $model->cache($this->cache, $this->cacheExpire)->select();
        $column = $model->column('key');
        $column = array_unique($column);
        $image = [
            'image' => '分发API',
            'officialUpload' => '官方上传',
            'tokenUpload' => 'Token上传',
        ];
        array_walk($column, function (&$value) use ($image) {
            if (isset($image[$value]))
                $value = [
                    'title' => $image[$value],
                    'key' => $value,
                ];
        });
        for ($i = 6; $i >= 0; $i--) {

            $startTime = Carbon::today()->subDays($i)->getTimestamp();
            $endTime = Carbon::today()->subDays($i - 1)->getTimestamp() - 1;
            foreach ($column as $value) {
                $model = $collection
                    ->where('key', $value['key'])
                    ->whereBetween('create_time', [$startTime, $endTime])->first();
                if ($model) {
                    $data[$value['title']][Carbon::today()->subDays($i)->format('m-d')] = $model->total_request_times;
                } else {
                    $data[$value['title']][Carbon::today()->subDays($i)->format('m-d')] = 0;
                }
            }
        }
        return msg(200, 'success', $data);
    }

    //今日图片请求次数排行榜
    public function getTodayPictureRequest()
    {

        $page = empty(Request::param('page')) ? 1 : Request::param('page');
        $limit = empty(Request::param('limit')) ? 12 : Request::param('limit');
        $model = Image::order('today_request_times', 'desc')
            ->whereDay('final_request_time', 'today')
            ->withJoin([
                'user' => function ($query) {
                    return $query->withField(['id', 'username']);
                }
            ])
            ->cache($this->cache, $this->cacheExpire)
            ->field('user_id,final_request_time,signatures,filename,today_request_times,total_request_times');

        $collection = $model->page($page, $limit)
            ->select();
        $count = $model
            ->count();
        return msg(200, 'success', ['item' => $collection, 'count' => $count]);
    }

    //获取今日来源域名访问数排行榜
    public function getTodayRefereRequest()
    {

        $page = empty(Request::param('page')) ? 1 : Request::param('page');
        $limit = empty(Request::param('limit')) ? 12 : Request::param('limit');
        $model = ImageRequest::order('today_request_times', 'desc')
            ->whereDay('final_request_time', 'today')
            ->cache($this->cache, $this->cacheExpire);
        $count = $model->count();
        $collection = $model
            ->page($page, $limit)
            ->select();

        return msg(200, 'success', ['item' => $collection, 'count' => $count]);

    }

    //获取总来源域名访问数排行榜
    public function getTotalRefereRequest()
    {

        $page = empty(Request::param('page')) ? 1 : Request::param('page');
        $limit = empty(Request::param('limit')) ? 12 : Request::param('limit');
        $model = ImageRequest::order('today_request_times', 'desc')
            ->cache($this->cache, $this->cacheExpire);
        $count = $model->count();
        $collection = $model
            ->page($page, $limit)
            ->select();

        return msg(200, 'success', ['item' => $collection, 'count' => $count]);
    }

    public function getTotalInformation()
    {
        $totalImages = Image::cache($this->cache, $this->cacheExpire)->count();
        $sumImageSize = Image::cache($this->cache, $this->cacheExpire)->sum('file_size');
        $totalSuspiciousImages = Image::where('fraction', '>=', 70)
            ->cache($this->cache, $this->cacheExpire)
            ->count();
        $totalUsers = User::cache($this->cache, $this->cacheExpire)->count();
        $data = [
            'totalImages' => $totalImages,
            'sumImageSize' => $sumImageSize,
            'totalUsers' => $totalUsers,
            'totalSuspiciousImages' => $totalSuspiciousImages,
        ];
        return msg(200, 'success', $data);


    }

}