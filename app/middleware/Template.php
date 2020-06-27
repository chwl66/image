<?php
declare (strict_types = 1);

namespace app\middleware;

use think\facade\View;

class Template
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
        hidove_config_get('system.base');
        View::config([
            'view_path' => get_template_path(),
        ]);
        return $next($request);
    }
}
