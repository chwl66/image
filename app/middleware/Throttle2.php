<?php

declare (strict_types=1);

namespace app\middleware;


use Carbon\Carbon;
use think\facade\Cache;
use think\facade\Config;
use think\model\Collection;

class Throttle2
{
    public function __construct()
    {
        $this->rateLimit = Config::get('throttle.default');
    }

    public function handle($request, \Closure $next)
    {
        $identification = $this->rateLimit['prefix'];
        if ($this->rateLimit['key'] === true) {
            $identification .= $request->ip();
        } else {
            $this->rateLimit['key'] = str_replace('__CONTROLLER__',$request->controller(),$this->rateLimit['key']);
            $this->rateLimit['key'] = str_replace('__ACTION__',$request->action(),$this->rateLimit['key']);
            $this->rateLimit['key'] = str_replace('__IP__',$request->ip(),$this->rateLimit['key']);
            $identification .= $this->rateLimit['prefix'] . $this->rateLimit['key'];

        }

        $rateLimit = empty($this->rateLimit['visit_frequency']) ? 9999999999 : $this->rateLimit['visit_frequency'];
        $rateLimitTime = empty($this->rateLimit['visit_time_interval']) ? 0 : $this->rateLimit['visit_time_interval'];

        $requestLog = Cache::get($identification);

        if (empty($requestLog)) {
            $requestLog = new Collection();
        }
        $requestLog
            ->whereNotBetween('create_time', [time() - $rateLimitTime, time()])
            ->delete();

        $count = $requestLog->count();

        $currentTime = time();
        //剩余次数
        $remaining = $rateLimit - $count;
        $first = $requestLog->first();
        //下次请求间隔秒数
        $retryAfter = 0;
        if (!empty($first) && $remaining <= 0) {
            $retryAfter = $rateLimitTime - Carbon::parse($first['create_time'])->diffInSeconds();
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
        $requestLog->push([
            'create_time' => $currentTime
        ]);
        Cache::set($identification, $requestLog);
        return $next($request)->header($header);
    }

}