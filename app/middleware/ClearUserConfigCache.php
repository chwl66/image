<?php
declare (strict_types = 1);

namespace app\middleware;

use app\model\User;
use think\facade\Cache;

class ClearUserConfigCache
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        Cache::tag('config_user_' . User::get_user_id())->clear();
        return $response;
    }
}
