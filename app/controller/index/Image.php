<?php
// +----------------------------------------------------------------------
// | Hidove [ http://www.hidove.cn ]
// +----------------------------------------------------------------------
// | Author: Ivey <loliconla@qq.com>
// +----------------------------------------------------------------------
// | Date: 2019年11月4日17:43:33
// +----------------------------------------------------------------------

namespace app\controller\index;

use app\BaseController;
use app\controller\index\service\getHttpCode;
use app\controller\index\service\ImageFilter;
use app\controller\common\ImageInitial;
use app\provider\WeightRand;
use app\model\ApiRequest;
use app\model\ImageRequest;
use Carbon\Carbon;
use think\exception\HttpException;
use think\facade\Cache;
use think\facade\Request;

class Image extends BaseController
{

    private $signatures = '';

    private $image;
    private $weightRand;
    private $getHttpCode;
    private $allowHttpCode;

    protected function initialize()
    {
        $this->signatures = Request::param('signatures');
    }

    public function index()
    {
        $redirectUrl = Cache::get('image_' . $this->signatures);

        //记录请求信息
        $apiRecord = hidove_config_get('system.other.apiRecord');

        if ($redirectUrl && $apiRecord != 1) {
            return redirect($redirectUrl);
        }

        $this->image = \app\model\Image::where('signatures', $this->signatures)
            ->findOrEmpty();
        if ($this->image->isEmpty()) {
            throw new HttpException(404, '404 NOT FOUND!');
        }

        if ($this->image->is_invalid === 1){
            throw new HttpException(410, '该资源的CDN节点已全部失效');
        }
        if (!$redirectUrl) {

            //拼接图片地址
            $imageUrl = (new ImageInitial($this->image))->run();

            //拼接代理地址
            $imageUrl = $this->spliceProxyUrl($imageUrl);

            //拼接权重地址
            $result = (new ImageFilter($imageUrl, $this->image))->run();
            //获取跳转地址
            $redirectUrl = $this->getRedirectUrl($result);
            $userId = 0;
            if (!empty($this->image->user->id)) {
                $userId = $this->image->user->id;
            }
            Cache::tag(['image_user_' . $userId, 'image'])
                ->set('image_' . $this->signatures, $redirectUrl);
        }
        //记录请求信息
        if ($apiRecord == 1) {
            $this->recordRequest();
        }
        return redirect($redirectUrl);
    }

    private function  spliceProxyUrl(array $url){

        $proxy = hidove_config_get('system.distribute.proxy');
        $proxyNode = format_api_type(hidove_config_get('system.distribute.proxyNode'));

        array_walk($url,function (&$value,$key)use($proxy,$proxyNode){
            if (in_array($key,$proxyNode))
                $value = $proxy. $value;
        });
        return $url;
    }
    /**
     * 获取跳转地址
     * @param $result
     * @return bool
     */

    private function getRedirectUrl($result)
    {
        if (empty($result)) {
            $this->isInvalid();
        }
        //根据权重取出地址
        $this->weightRand = new WeightRand();

        $this->getHttpCode = new getHttpCode();

        $this->allowHttpCode = explode(',',hidove_config_get('system.distribute.httpCode'));

        $recursion = $this->recursion($result);
        if ($recursion === false) {
            $this->isInvalid();
        }
        return $recursion;
    }

    /**
     * 该资源的CDN节点已全部失效
     */
    private function isInvalid(){

        $this->image->is_invalid = 1;
        $this->image->save();
        throw new HttpException(410, '该资源的CDN节点已全部失效');
    }

    /**
     * 递归获取有效的url
     * @param array $result
     * @return bool
     */
    private function recursion(array $result)
    {

        if (empty($result)) {
            return false;
        }
        //根据权重取出地址
        $redirectData = $this->weightRand->run($result);

        $code = $this->getHttpCode->run($redirectData['data']);
        if (in_array($code, $this->allowHttpCode)) {
            return $redirectData['data'];
        } else {
            unset($result[$redirectData['key']]);
            return $this->recursion($result);
        }
    }

    /**
     * 记录请求信息
     */
    private function recordRequest()
    {

        $referer = parse_url(Request::server('HTTP_REFERER'));
        $referer = empty($referer['host']) ? '直接访问' : $referer['host'];
        $ip = Request::ip();
        //更新图片请求信息
        if (!Carbon::parse($this->image->final_request_time)->isToday()) {
            $this->image->today_request_times = 1;
        } else {
            $this->image->today_request_times++;
        }
        $this->image->total_request_times++;
        $this->image->final_request_time = time();
        $this->image->save();
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
        if (!Carbon::parse($imageRequest->final_request_time)->isToday()){
            $imageRequest->today_request_times = 0;
        }
        $imageRequest->final_request_time = time();
        $imageRequest->ip = $ip;
        $imageRequest->today_request_times++;
        $imageRequest->total_request_times++;
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
        $apiRequest->total_request_times++;
        $apiRequest->update_time = time();
        $apiRequest->save();
    }
}
