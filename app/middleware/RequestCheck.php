<?php
declare (strict_types = 1);

namespace app\middleware;

class RequestCheck
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return \think\response\Json
     */
    public function handle($request, \Closure $next)
    {
        if (!$request->isAjax()){
            return msg(400,'禁止访问');
        }
        return $next($request);
    }
}
