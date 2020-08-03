<?php
declare (strict_types=1);

namespace app\middleware;

class ApiFilter
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure $next
     * @return \think\response\Json
     */
    public function handle($request, \Closure $next)
    {
        //
        $upload_api_switch = hidove_config_get('system.upload.api.switch');

        if ($upload_api_switch != 1 && is_token()) {
            return msg(400,'The api is closed');
        }
        return $next($request);
    }
}
