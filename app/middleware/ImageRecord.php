<?php
declare (strict_types=1);

namespace app\middleware;

use app\model\ApiRequest;
use app\model\Image;
use app\model\ImageRequest;
use Carbon\Carbon;
use think\facade\Request;

class ImageRecord
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {

        $res = $next($request);

        //记录请求信息
        $apiRecord = hidove_config_get('system.other.apiRecord');

        //记录请求信息
        if ($apiRecord == 1) {
            $image = Image::where('signatures', Request::param('signatures'))
                ->findOrEmpty();
            if (!$image->isEmpty())
                $this->recordRequest($image);
        }
        return $res->header([
            'Cache-Control' => 'max-age=259200'
        ]);
    }

    /**
     * 记录请求信息.
     */
    private function recordRequest($image)
    {
        $referer = parse_url(Request::server('HTTP_REFERER'));
        $referer = empty($referer['host']) ? '直接访问' : $referer['host'];
        $ip = Request::ip();
        //更新图片请求信息
        if (!Carbon::parse($image->final_request_time)->isToday()) {
            $image->today_request_times = 1;
        } else {
            ++$image->today_request_times;
        }
        ++$image->total_request_times;
        $image->final_request_time = time();
        $image->save();
        $imageRequest = ImageRequest::where('referer', $referer)
            ->findOrEmpty();
        if ($imageRequest->isEmpty()) {
            $imageRequest = new ImageRequest([
                'referer' => $referer,
                'create_time' => time(),
                'final_request_time' => 0,
                'ip' => $ip,
                'today_request_times' => 0,
                'total_request_times' => 0,
            ]);
        }
        if (!Carbon::parse($imageRequest->final_request_time)->isToday()) {
            $imageRequest->today_request_times = 0;
        }
        $imageRequest->final_request_time = time();
        $imageRequest->ip = $ip;
        ++$imageRequest->today_request_times;
        ++$imageRequest->total_request_times;
        $imageRequest->save();

        //更新API接口请求信息
        $apiRequest = ApiRequest::where('key', 'image')->whereTime('create_time', 'today')
            ->findOrEmpty();
        if ($apiRequest->isEmpty()) {
            $apiRequest = new ApiRequest([
                'key' => 'image',
                'create_time' => time(),
                'total_request_times' => 0,
            ]);
        }
        ++$apiRequest->total_request_times;
        $apiRequest->update_time = time();
        $apiRequest->save();
    }
}
