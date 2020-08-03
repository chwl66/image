<?php
declare (strict_types=1);

namespace app\middleware;


use app\model\User;

class AdminAuth
{

    public function handle($request, \Closure $next)
    {

        if (!User::is_admin()) {
            if ($request->isAjax()){
                return msg(401,'请登录');
            }
            return redirect((string)url('admin.login/index'));
        }
        return $next($request);
    }
}
