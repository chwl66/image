<?php

declare (strict_types=1);

namespace app\middleware;


use Carbon\Carbon;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Request;

class Throttle
{
    private $config = [
        // 缓存键前缀，防止键值与其他应用冲突
        'prefix' => 'throttle_',
        // 缓存的键，true 表示使用来源ip
        'key' => "__CONTROLLER__@__ACTION__@__IP__",
        // 设置访问次数。单位秒，默认值 null 表示不限制
        'visit_frequency' => 10,
        // 设置访问时间间隔。单位秒，默认值 null 表示不限制
        'visit_time_interval' => 86400,
        // 访问受限时返回的http状态码
        'visit_fail_code' => 429,
        // 访问受限时访问的文本信息 __WAIT__ 等待时间秒数
        'visit_fail_text' => '访问频率受到限制，请稍等__WAIT__秒再试',
    ];

    public function __construct()
    {


        $defaultConfig = Config::get('throttle.default');

        $this->rateLimit = Config::get(
            'throttle.' .
            str_replace('.', '_', Request::controller())
            . '@' . Request::action());

        if (empty($defaultConfig)){
            $defaultConfig = [];
        }
        if (empty($this->rateLimit)){
            $this->rateLimit = [];
        }
        $defaultConfig = array_merge($this->config, $defaultConfig);

        $this->rateLimit = array_merge($defaultConfig, $this->rateLimit);

        $this->identification = $this->rateLimit['prefix'];
        if ($this->rateLimit['key'] === true) {
            $this->identification .= get_request_ip();
        } else {
            $this->identification = str_replace('__CONTROLLER__', Request::controller(), $this->rateLimit['key']);
            $this->identification = str_replace('__ACTION__', Request::action(), $this->identification);
            $this->identification = str_replace('__IP__', get_request_ip(), $this->identification);
            $this->identification .= $this->rateLimit['key'];
        }
        if (empty($this->rateLimit['visit_frequency'])) {
            $this->rateLimit['visit_frequency'] = 9999999999;
        }
        if (empty($this->rateLimit['visit_time_interval'])) {
            $this->rateLimit['visit_time_interval'] = 0;
        }
    }

    public function handle($request, \Closure $next)
    {
        $rateLimit = $this->rateLimit['visit_frequency'];
        $rateLimitTime = $this->rateLimit['visit_time_interval'];

        $requestLog = Cache::get($this->identification);

        if (empty($requestLog)) {
            $requestLog = [
                'frequency' => 1,
                'create_time' => 0,
            ];
        }

        //剩余次数
        $remaining = $rateLimit - $requestLog['frequency'];
        //下次请求间隔秒数
        $retryAfter = 0;
        if ($remaining <= 0) {
            $retryAfter = $rateLimitTime - Carbon::parse($requestLog['create_time'])->diffInSeconds();
        }
        $header =
            [
                'Retry-After' => $retryAfter,
                'X-RateLimit-Limit' => $rateLimit,
                'X-RateLimit-Remaining' => $remaining,
            ];
        if ($remaining <= 0) {
            return msg($this->rateLimit['visit_fail_code'],
                str_replace('__WAIT__', $retryAfter, $this->rateLimit['visit_fail_text']))
                ->header($header);
        }
        $requestLog['frequency']++;
        $requestLog['create_time'] = time();
        $header['X-RateLimit-Remaining']++;
        Cache::set($this->identification, $requestLog);
        return $next($request)->header($header);
    }

}